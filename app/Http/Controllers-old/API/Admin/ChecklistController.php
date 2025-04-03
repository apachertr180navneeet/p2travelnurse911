<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;
use DB;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ChecklistController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }


    ## Function to get compliacne checklists
    public function getComplianceChecklists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $checklists = DB::table('compliance_checklists as c')
                ->leftJoin('users as u', 'c.created_by', '=', 'u.id')
                ->select('c.*','u.name as creator_name', 'u.role_id as creator_role_id', 'u.unique_id as creator_unique_id')
                ->where('c.deleted_at', NULL);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $checklists->where('c.title', 'LIKE', "%{$request->keyword}%");
            } 
            if (isset($request->status) && $request->status != 'all') {
                $checklists->where('c.status', $request->status);
            }
            
                $checklists = $checklists->orderBy('c.id', 'desc')->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($checklists)) . " Record found", 'data' => $checklists);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to get compliacne submitted checklists
    public function getComplianceSubmittedChecklists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $checklists = DB::table('compliance_checklists as c')
                ->join('user_compliance_checklists as ucc', 'ucc.checklist_id', '=', 'c.id')
                ->leftJoin('users as u', 'c.created_by', '=', 'u.id')
                ->leftJoin('users as u2', 'ucc.user_id', '=', 'u2.id')
                ->select('c.*','u.name as creator_name', 'u.role_id as creator_role_id', 'u.unique_id as creator_unique_id',
                'u2.name as user_name', 'u2.role_id as user_role_id', 'u2.unique_id as user_unique_id', 'ucc.created_at as submitted_on','ucc.updated_at as updated_on','ucc.checklist_meta as user_checklist_meta')
                ->where('c.deleted_at', NULL);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $checklists->where('c.title', 'LIKE', "%{$request->keyword}%");
            } 
            
                $checklists = $checklists->orderBy('ucc.id', 'desc')->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($checklists)) . " Record found", 'data' => $checklists);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get user submitted checklists
    public function getUserSubmittedChecklists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'userID' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $checklists = DB::table('compliance_checklists as c')
                ->join('user_compliance_checklists as ucc', 'ucc.checklist_id', '=', 'c.id')
                ->leftJoin('users as u', 'c.created_by', '=', 'u.id')
                ->leftJoin('users as u2', 'ucc.user_id', '=', 'u2.id')
                ->select('c.*','u.name as creator_name', 'u.role_id as creator_role_id', 'u.unique_id as creator_unique_id',
                'u2.name as user_name', 'u2.role_id as user_role_id', 'u2.unique_id as user_unique_id', 'ucc.created_at as submitted_on','ucc.updated_at as updated_on','ucc.checklist_meta as user_checklist_meta')
                ->where('c.deleted_at', NULL)
                ->where('ucc.user_id', $request->userID)
                ;
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $checklists->where('c.title', 'LIKE', "%{$request->keyword}%");
            } 
            
                $checklists = $checklists->orderBy('ucc.id', 'desc')->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($checklists)) . " Record found", 'data' => $checklists);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to insert/update compliance checklist
    public function updateComplianceChecklist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            
            $param['title'] = $request->title;
            $param['status'] = $request->status;
            $param['checklist_meta'] = json_encode($request->sections);
            if (isset($request->id)) {
                $param['slug'] = $this->generateChecklistSlug($request->title, $request->id);
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('compliance_checklists')->where('id', $request->id)->update($param);
                $msg = "Compliance Checklist has been been successfully updated";
            } else {
                $param['unique_id'] = $this->generateUniqueCode(8);
                $param['slug'] = $this->generateChecklistSlug($request->title);
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $param['status'] = 1;
                DB::table('compliance_checklists')->insert($param);
                $msg = "Compliance Checklist has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete compliance checklist
    public function deletedComplianceChecklist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                $param['deleted_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('compliance_checklists')->where('id', $request->id)->update($param);
                $msg = "Compliance Checklist has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update compliance checklist's status
    public function updateComplianceChecklistStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                
                $param = array(
                    'status' => $request->status,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    );
                DB::table('compliance_checklists')->where('id', $request->id)->update($param);
                $msg = "Compliance checklist status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to clone compliance checklist
    public function cloneComplianceChecklist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $checklistRow = DB::table('compliance_checklists')->select('id','title','checklist_meta','status','deleted_at')
                    ->where('id', $request->id)->first();
            if (!empty($checklistRow)) {
                
                $param = array(
                    'title' => $checklistRow->title,
                    'slug' => $this->generateChecklistSlug($checklistRow->title),
                    'unique_id' => $this->generateUniqueCode(8),
                    'checklist_meta' => $checklistRow->checklist_meta,
                    'status' => $checklistRow->status,
                    'deleted_at' => $checklistRow->deleted_at,
                    'created_at' => $this->entryDate,
                    'created_by' => $request->user_id,
                    );
                DB::table('compliance_checklists')->insert($param);
                $msg = "Compliance checklist has been successfully cloned";
                $result = array('status' => true, 'message' => $msg);
            }    
            else
            {
                $result = array('status' => false, 'message' => 'Invalid checklist ID');
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get compliance checklist details
    public function getComplianceChecklistDetails(Request $request)
    {
        
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'checklistID' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            # Check for drafted job
            $user_id = $request->user_id;
            $raw = DB::table('compliance_checklists as cc')
                ->select('cc.*')
                ->where(['cc.unique_id' => $request->checklistID]) /* , 'cc.created_by' => $request->user_id */
                ->first();
            
            if(!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $raw);
            else
                $result = array('status' => false, 'message' => 'Invalid Compliance Checklist ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    public function generateChecklistSlug($title, $id = null) {
        
        $slug = Str::slug($title);
        
        $originalSlug = $slug;
        $iteration = 1;
        
        if($id != null)
        {
            while (DB::table('compliance_checklists')->where('slug', $slug)->where('id','!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $iteration;
                $iteration++;
            }
        }
        else
        {
            while (DB::table('compliance_checklists')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $iteration;
                $iteration++;
            }
        }    
        return $slug;
    }
    
    public function generateUniqueCode(int $codeLength)
	{
        $characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);

        $code = '';

        while (strlen($code) < $codeLength) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;
        }

        if (DB::table('compliance_checklists')->where('unique_id', $code)->exists()) {
            $this->generateUniqueCode($codeLength);
        }

        return $code;
	}
    
    ## Function to get compliance assigned checklist
    public function getComplianceAssignedChecklists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $uChecklist = DB::table('compliance_assigned_checklists as c')
                ->join('compliance_checklists as cc', 'cc.id', '=', 'c.checklist_id')
                ->join('users as u', 'c.user_id', '=', 'u.id') 
                ->select('c.checklist_id','c.id','cc.title','c.created_at','c.user_id','u.profile_pic', 'u.name as user_name', 'u.unique_id as user_unique_id')
                ->where('cc.deleted_at', NULL);
                /*->where('cc.created_by', $request->user_id);*/
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $uChecklist->where('cc.title', 'LIKE', "%{$request->keyword}%");
            } 
            
                $uChecklist = $uChecklist->orderBy('c.id', 'desc')
                ->get()
                ->map(function ($uChecklist) {
                    // Add dir_path column and its value to each record
                    $uChecklist->profile_pic_path = (!empty($uChecklist->profile_pic))?url(config('custom.user_folder') . $uChecklist->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uChecklist;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($uChecklist)) . " Record found", 'data' => $uChecklist);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## FUnction to update compliance assigned checklist
    public function updateComplianceChecklistAssignedUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

           
            
            
            if (isset($request->id)) {
                if(isset($request->checklists) && !empty($request->checklists))
                {
                    foreach($request->checklists as $k=>$v)
                    {
                        if(!DB::table('compliance_assigned_checklists')->where('checklist_id', $v)->where('user_id', $request->assigned_user_id)->exists())
                        {
                             $param = array(
                                'checklist_id' => $v,
                                'user_id' => $request->assigned_user_id,
                                'created_at' => $this->entryDate,
                                'created_by' => $request->user_id,
                            );
                            DB::table('compliance_assigned_checklists')->insert($param);
                        }
                        else
                        {
                            $param = array(
                                'updated_at' => $this->entryDate,
                                'updated_by' => $request->user_id,
                            );
                            DB::table('compliance_assigned_checklists')->where('id', $request->id)->update($param);
                        }
                    }
                }
                
                $msg = "Compliance checklist assign successfully";
            } else {
                if(isset($request->checklists) && !empty($request->checklists))
                {
                    foreach($request->checklists as $k=>$v)
                    {
                        if(!DB::table('compliance_assigned_checklists')->where('checklist_id', $v)->where('user_id', $request->assigned_user_id)->exists())
                        {
                            $param = array(
                                'checklist_id' => $v,
                                'user_id' => $request->assigned_user_id,
                                'created_at' => $this->entryDate,
                                'created_by' => $request->user_id,
                            );
                            DB::table('compliance_assigned_checklists')->insert($param);
                        }
                    }
                }
                $msg = "Compliance checklist assign successfully";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## function to deleted compliance assigned checklist
    public function deleteComplianceAssignedChecklist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                DB::table('compliance_assigned_checklists')->where('id', $request->id)->delete();
                $msg = "Assigned Compliance File Checklist has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to get all facility compliances
    public function getFacilityCompliances(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $facilityCompliances = DB::table('facility_compliance as fc')
                ->select('fc.id', 'fc.unique_id','fc.title', 'fc.type','fc.status','fc.created_at')
                ->where('fc.deleted_at', NULL)
                ->where('fc.created_by', $request->user_id);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $facilityCompliances->where('fc.title', 'LIKE', "%{$request->keyword}%");
            } 
                $facilityCompliances = $facilityCompliances->orderBy('fc.id', 'desc')->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($facilityCompliances)) . " Record found", 'data' => $facilityCompliances);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to add/update facility compliance 
    public function updateFacilityCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = $request->all();
            
            $user_id = $request->user_id;
            unset($param['user_id']);

            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('facility_compliance')->where('id', $request->id)->update($param);
                $msg = "Facility Compliance List has been been successfully updated";
            } else {
                $param['unique_id'] = $this->generateFacilityCompliancesUniqueCode(8);
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('facility_compliance')->insert($param);
                $msg = "Facility Compliance List has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    public function generateFacilityCompliancesUniqueCode(int $codeLength)
	{
        $characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);

        $code = '';

        while (strlen($code) < $codeLength) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;
        }

        if (DB::table('facility_compliance')->where('unique_id', $code)->exists()) {
            $this->generateFacilityCompliancesUniqueCode($codeLength);
        }

        return $code;
	}
    
    ## Function to update facility compliance status
    public function updateFacilityCompliancesStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                
                $param = array(
                    'status' => $request->status,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    );
                DB::table('facility_compliance')->where('id', $request->id)->update($param);
                $msg = "Facility compliance status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete compliance compliance
    public function deleteFacilityCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                $param['deleted_at'] = $this->entryDate;
                DB::table('facility_compliance')->where('id', $request->id)->update($param);
                $msg = "Facility Compliance has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to clone facility compliance
    //cloneFacilityCompliance
     
    ## Function to get assigned facility compliance
    public function getAssignedFacilityCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $uChecklist = DB::table('user_facility_compliances as c')
                ->join('facility_compliance as cc', 'cc.id', '=', 'c.facility_compliance_id')
                ->join('users as u', 'c.user_id', '=', 'u.id') 
                ->select('cc.id as facility_compliance_id','c.id','cc.title','c.created_at','c.user_id','u.profile_pic', 'u.name as user_name', 'u.unique_id as user_unique_id')
                ->where('cc.deleted_at', NULL);
                /*->where('cc.created_by', $request->user_id)*/
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $uChecklist->where('cc.title', 'LIKE', "%{$request->keyword}%");
            } 
                $uChecklist = $uChecklist->orderBy('c.id', 'desc')
                ->get()
                ->map(function ($uChecklist) {
                    // Add dir_path column and its value to each record
                    $uChecklist->profile_pic_path = (!empty($uChecklist->profile_pic))?url(config('custom.user_folder') . $uChecklist->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uChecklist;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($uChecklist)) . " Record found", 'data' => $uChecklist);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update assigned facility compliance
    public function updateAssignedFacilityCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'facility_compliance_id' => 'required', 
            'assigned_user_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

           
            
            
            if (isset($request->id)) {
                
                if(!DB::table('user_facility_compliances')->where('facility_compliance_id', $request->facility_compliance_id)->where('user_id', $request->assigned_user_id)->exists())
                {
                     $param = array(
                        'facility_compliance_id' => $request->facility_compliance_id,
                        'user_id' => $request->assigned_user_id,
                        'created_at' => $this->entryDate,
                        'created_by' => $request->user_id,
                    );
                    DB::table('user_facility_compliances')->insert($param);
                }
                else
                {
                    $param = array(
                        'updated_at' => $this->entryDate,
                        'updated_by' => $request->user_id,
                    );
                    DB::table('user_facility_compliances')->where('id', $request->id)->update($param);
                }
                    
                
                $msg = "Facility Compliance assign successfully";
            } else {
               
                if(!DB::table('user_facility_compliances')->where('facility_compliance_id', $request->facility_compliance_id)->where('user_id', $request->assigned_user_id)->exists())
                {
                    $param = array(
                        'facility_compliance_id' => $request->facility_compliance_id,
                        'user_id' => $request->assigned_user_id,
                        'created_at' => $this->entryDate,
                        'created_by' => $request->user_id,
                    );
                    DB::table('user_facility_compliances')->insert($param);
                }
                    
                $msg = "Facility Compliance assign successfully";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## function to deleted compliance assigned checklist
    public function deleteAssignedFacilityCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                DB::table('user_facility_compliances')->where('id', $request->id)->delete();
                $msg = "Assigned Facility Compliance has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}
