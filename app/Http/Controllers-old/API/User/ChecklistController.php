<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;
use DB;
use Exception;

class ChecklistController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }
    
    ## Function to get Assigned checklists
    public function getAssignedChecklists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            
            $user_id = $request->user_id;
            
            $agencyRow = DB::table('users')->select('created_by')
                        ->where('id', $request->user_id)->first();
            if($request->type == 'active')
            {
                $assignedChecklists = DB::table('compliance_checklists as cc')
                ->leftJoin('users as u', 'u.id', '=', 'cc.created_by')
                ->select('cc.id','cc.slug', 'cc.title')
                ->where('cc.status', 1)
                ->where('cc.deleted_at', NULL)
                ->where(function ($query) use ($agencyRow) {
                    $query->where('cc.created_by', $agencyRow->created_by)
                          ->orWhere('u.role_id', 1);
                })
                ->whereNotExists(function ($query) use ($user_id) {
                    $query->select(DB::raw(1))
                          ->from('user_compliance_checklists as ucc')
                          ->whereRaw('ucc.checklist_id = cc.id')
                          ->where('ucc.user_id', $user_id); 
                })
                ->whereNotExists(function ($query) use ($user_id) {
                    $query->select(DB::raw(1))
                          ->from('compliance_assigned_checklists as cac')
                          ->whereRaw('cac.checklist_id = cc.id')
                          ->where('cac.user_id', $user_id); 
                })
                ;
            }
            if($request->type == 'assigned')
            {
                $assignedChecklists = DB::table('compliance_checklists as cc')
                ->join('compliance_assigned_checklists as cac', 'cac.checklist_id', '=', 'cc.id')
                ->leftJoin('users as u', 'u.id', '=', 'cc.created_by')
                ->select('cc.id','cc.slug', 'cc.title')
                ->where('cc.status', 1)
                ->where('cc.deleted_at', NULL)
                ->where('cac.user_id', $user_id)
                ->where(function ($query) use ($agencyRow) {
                    $query->where('cc.created_by', $agencyRow->created_by)
                          ->orWhere('u.role_id', 1);
                })
                ->whereNotExists(function ($query) use ($user_id) {
                    $query->select(DB::raw(1))
                          ->from('user_compliance_checklists as ucc')
                          ->whereRaw('ucc.checklist_id = cc.id')
                          ->where('ucc.user_id', $user_id); 
                })
                ;
            }
            else if($request->type == 'draft')
            {
                $assignedChecklists = DB::table('compliance_checklists as cc')
                ->join('user_compliance_checklists as ucc','ucc.checklist_id', '=', 'cc.id')
                ->leftJoin('users as u', 'u.id', '=', 'cc.created_by')
                ->select('cc.id','cc.slug', 'cc.title','ucc.created_at as submitted_on','ucc.updated_at as updated_on')
                ->where('cc.status', 1)
                ->where('cc.deleted_at', NULL)
                ->where(function ($query) use ($agencyRow) {
                    $query->where('cc.created_by', $agencyRow->created_by)
                          ->orWhere('u.role_id', 1);
                })
                ->where('ucc.user_id', $user_id)
                ->where('ucc.status', 0);
            }
            else if($request->type == 'completed')
            {
                $assignedChecklists = DB::table('compliance_checklists as cc')
                ->join('user_compliance_checklists as ucc','ucc.checklist_id', '=', 'cc.id')
                ->leftJoin('users as u', 'u.id', '=', 'cc.created_by')
                ->select('cc.id','cc.slug', 'cc.title','ucc.created_at as submitted_on','ucc.updated_at as updated_on')
                ->where('cc.status', 1)
                ->where('cc.deleted_at', NULL)
                ->where(function ($query) use ($agencyRow) {
                    $query->where('cc.created_by', $agencyRow->created_by)
                          ->orWhere('u.role_id', 1);
                })
                ->where('ucc.user_id', $user_id)
                ->where('ucc.status', 1);
            }
            
            // Apply filters if present
            if (isset($request->keyword) && !empty($request->keyword)) {
                $assignedChecklists->where('cc.title', 'LIKE', "%{$request->keyword}%");
            }
            
                $assignedChecklists = $assignedChecklists->orderBy('cc.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($assignedChecklists)) . " Record found", 'data' => $assignedChecklists);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ##Function to get checklist details
    public function getChecklistDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'checklistId' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $raw = DB::table('compliance_checklists as cc')
                ->where('cc.status', 1)
                ->where('cc.deleted_at', NULL)
                ->where('cc.slug', $request->checklistId)
                ->first();
                
            if(!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $raw);
            else
                $result = array('status' => false, 'message' => 'Invalid job ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update checklist details
    public function updateChecklistDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'checklist_id' => 'required',
            'checklist_meta' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }
        
        try {
            
            $result = DB::table('user_compliance_checklists as ucc')
                ->select('ucc.id')
                ->where('ucc.user_id', $request->user_id)
                ->where('ucc.checklist_id', $request->checklist_id)
                ->first();

            if(!empty($result))
            {
                $param = array('checklist_meta' => json_encode($request->checklist_meta),
                        'status' => $request->submissionType,
                        'updated_at' => $this->entryDate,
                );
                DB::table('user_compliance_checklists')->where('user_id', $request->user_id)->where('checklist_id', $request->checklist_id)->update($param);
            }
            else
            {
                $param = array('user_id' => $request->user_id,
                        'checklist_id' => $request->checklist_id,
                        'created_at' => $this->entryDate,
                        'status' => $request->submissionType,
                );
                DB::table('user_compliance_checklists')->insert($param);
            }
            if($request->submissionType)
                $result = array('status' => true, 'message' => "Checklist completed successfully");
            else 
                $result = array('status' => true, 'message' => "Checklist saved as draft successfully");
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ##Function to get user checklist details
    public function getUserChecklistDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'checklistId' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $raw = DB::table('user_compliance_checklists as ucc')
                ->where('ucc.deleted_at', NULL)
                ->where('ucc.checklist_id', $request->checklistId)
                ->where('ucc.user_id', $request->user_id)
                ->first();
                
            if(!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $raw);
            else
                $result = array('status' => false, 'message' => 'Invalid user checklist ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}