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

class SubmissionController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }


    ## Function to get submissions
    public function getSubmissions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $complianceFiles = DB::table('submissions as f')
                ->leftJoin('users as u', 'f.candidate_id', '=', 'u.id')
                ->leftJoin('facilities as fac', 'f.client_id', '=', 'fac.id')
                ->leftJoin('specialities as s', 'f.specialty_id', '=', 's.id')
                ->leftJoin('users as u2', 'f.created_by', '=', 'u2.id')
                ->select('f.id', 'f.entry_date', 'f.candidate_id', 'f.client_id','f.specialty_id','f.agency_id','f.status','f.no_of_submissions', 'f.created_at','u.name','u.unique_id as user_unique_id','fac.title as facility_title','s.specialty as specialty_title','u2.name as submitted_user_name','u2.unique_id as submitted_user_unique_id')
                ->where('f.deleted_at', NULL);
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                 $complianceFiles->where(function ($query) use ($request) {
                    $query->where('fac.title', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('s.specialty', 'LIKE', "%{$request->keyword}%");
                });
            }    
            
            if (isset($request->status) && $request->status != 'all') {
               $complianceFiles->where('f.status', $request->status);
            }
            
            if ((isset($request->start_date) && !empty($request->start_date)) || isset($request->end_date) && !empty($request->end_date)) {
                if(isset($request->start_date) && !empty($request->start_date))
                    $complianceFiles->where('f.entry_date', '>=', $request->start_date);
                    
                if(isset($request->end_date) && !empty($request->end_date))
                    $complianceFiles->where('f.entry_date', '<=', $request->end_date);
            }   
                
                $complianceFiles = $complianceFiles->orderBy('f.id', 'desc')->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($complianceFiles)) . " Record found", 'data' => $complianceFiles);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to add/update submission
    public function updateSubmission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'entry_date' => 'required',
            'candidate_id' => 'required',
            'client_id' => 'required',
            'specialty_id' => 'required',
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
            $param['agency_id'] = $user_id;
            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('submissions')->where('id', $request->id)->update($param);
                $msg = "Submission has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('submissions')->insert($param);
                $msg = "Submission has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete submission
    public function deleteSubmission(Request $request)
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
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('submissions')->where('id', $request->id)->update($param);
                $msg = "Submission has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update submission field
    public function updateSubmissionField(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'field' => 'required',
            /*'value' => 'required',*/
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            if ($request->field == 'status') {
                
                $submission = DB::table('submissions')->select('candidate_id')
                    ->where('id', $request->id)->first();
                $candidate_id = $submission->candidate_id;
                
                
    			if ($request->value == 'redirect') {
    				
    				$redirectRecord = DB::table('redirects')->select('id')
                    ->where('candidate_id', $candidate_id)->first();
                    
    				if (isset($redirectRecord) && !empty($redirectRecord)) {
    					$param = array(
    						'comes_from_submission' => 'Y',
    						'updated_at' => date("Y-m-d H:i:s"),
    						'updated_by' => $request->user_id,
    					);
    					DB::table('redirects')->where('id', $redirectRecord->id)->update($param);
    				} else {
    					$param = array(
    						'candidate_id' => $candidate_id,
    						'comes_from_submission' => 'Y',
    						'created_at' => $this->entryDate,
    						'created_by' => $request->user_id,
    					);
    					DB::table('redirects')->insert($param);
    				}
    			} else {
    				DB::table('redirects')->where('candidate_id', $candidate_id)->where('comes_from_submission', 'Y')->delete();
    			}
    			
    		}

            $param = array();
            $param[$request->field] = $request->value;
            $param['updated_at'] = $this->entryDate;
            $param['updated_by'] = $request->user_id;
            DB::table('submissions')->where('id', $request->id)->update($param);
            $msg = "Submission has been been successfully updated";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get all specialities
    public function getSpecialities(Request $request)
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
            $specialities = DB::table('specialities as s')
            ->leftJoin('professions as p', 'p.id', '=', 's.profession_id')
            ->select('s.id', 's.specialty', 'p.profession','s.created_at','s.status')
            ->where('s.deleted_at', NULL)
            ->get()->toArray();
            
            DB::commit();    
            $result = array('status' => true, 'message' => (count($specialities)) . " Record found", 'data' => $specialities);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}