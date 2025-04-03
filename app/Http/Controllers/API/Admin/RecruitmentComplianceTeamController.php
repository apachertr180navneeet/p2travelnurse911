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

class RecruitmentComplianceTeamController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }
    
    ## Function to get all recruiment/compliance teams
    public function getRecruitmentComplianceTeams(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            $professions = DB::table('recruitment_compliance_teams as rct')
                ->select('rct.id', 'rct.team_type', 'rct.name','rct.email','rct.phone','rct.status','rct.created_at')
                ->where('rct.created_by', $request->user_id)
                ->where('rct.deleted_at', NULL);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $professions->where('rct.team_type', 'LIKE', "%{$request->keyword}%")->orWhere('rct.name', 'LIKE', "%{$request->keyword}%");
            }
            
                $professions = $professions->get()->toArray();
            
            DB::commit();
            $result = array('status' => true, 'message' => (count($professions)) . " Record found", 'data' => $professions);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
   
    ## Function to add/update recruiment/compliance team
    public function updateRecruitmentComplianceTeam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'team_type' => 'required',
            'name' => 'required',
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            if (isset($request->id) && !empty($request->id)) {
                
                $param = array(
                    'team_type' => strip_tags($request->team_type),
                    'name' => strip_tags($request->name),
                    'email' => strip_tags($request->email),
                    'phone' => strip_tags($request->phone),
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    'status' => $request->status,
                );
                DB::table('recruitment_compliance_teams')->where('id', $request->id)->update($param);
                
                 DB::commit();
                $result = array('status' => true, 'message' => "Recruitment/Compliance Team updated successfully");
            }
            else{
                # Check Team Email is already registered or not or not
                $query = CommonFunction::checkTeamEmailExist($request->email);
                if ($query) {
                    $result = array('status' => false, 'message' => "Email is already registered");
                    return response()->json($result);
                }
    
                
                $param = array(
                    'team_type' => strip_tags($request->team_type),
                    'name' => strip_tags($request->name),
                    'email' => strip_tags($request->email),
                    'phone' => strip_tags($request->phone),
                    'created_at' => $this->entryDate,
                    'created_by' => $request->user_id,
                    'status' => $request->status,
                );
                DB::table('recruitment_compliance_teams')->insert($param);
                DB::commit();
                $result = array('status' => true, 'message' => "Recruitment/Compliance Team added successfully");
                
            }

           
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete recruiment/compliance team
    public function deleteRecruitmentComplianceTeam(Request $request)
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
                DB::table('recruitment_compliance_teams')->where('id', $request->id)->update($param);
                $msg = "Recruitment/Compliance Team has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update recruiment/compliance team's status
    
    public function updateRecruitmentComplianceTeamStatus(Request $request)
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
                DB::table('recruitment_compliance_teams')->where('id', $request->id)->update($param);
                $msg = "Recruitment/Compliance Team status has been been successfully updated";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to perform bulk actions on recruiment/compliance team
    public function recruitmentComplianceTeamBulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required',
            'user_id' => 'required',
            'bulk_action' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            
            if($request->bulk_action == 'delete' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['deleted_at'] = $this->entryDate;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('recruitment_compliance_teams')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Recruitment/Compliance Team(s) has been successfully deleted";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-active' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "1";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('recruitment_compliance_teams')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Recruitment/Compliance Team(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-inactive' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "0";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('recruitment_compliance_teams')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Recruitment/Compliance Team(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else {
                $result = array('status' => false, 'message' => 'Unknown error occured');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}