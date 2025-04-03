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

class RedirectController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }


    ## Function to get redirects
    public function getRedirects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            
            
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            $subQuery1 = DB::table('submissions as sub')
                ->select(
                    'u.id',
                    'u.name',
                    'u.role_id',
                    'u.email',
                    'u.phone',
                    'u.unique_id as user_unique_id',
                    's.specialty',
                    'sub.agency_id',
                    DB::raw("'redirect' as sub_status"),
                    'red1.status as red_status',
                    DB::raw("GROUP_CONCAT(DISTINCT st.name ORDER BY st.name ASC SEPARATOR ', ') as state_names"),
                    DB::raw("GROUP_CONCAT(DISTINCT sf.title ORDER BY sf.title ASC SEPARATOR ', ') as desired_shifts"),
                    'red1.notes'
                )
                ->leftJoin('users as u', 'u.id', '=', 'sub.candidate_id')
                ->leftJoin('redirects as red1', 'red1.candidate_id', '=', 'sub.candidate_id')
                ->leftJoin('specialities as s','sub.specialty_id','=','s.id')
                ->leftJoin('user_preferred_states as ups', 'u.id', '=', 'ups.user_id')
                ->leftJoin('states as st', 'ups.state_id', '=', 'st.id')
                ->leftJoin('user_preferred_shifts as upsh', 'u.id', '=', 'upsh.user_id')
                ->leftJoin('shifts as sf', 'upsh.shift_id', '=', 'sf.id')
                ->where('sub.status', 'redirect');
            
             if (isset($request->keyword) && !empty($request->keyword)) {
                 $subQuery1->where(function ($query) use ($request) {
                    $query->where('st.name', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('sf.title', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('s.specialty', 'LIKE', "%{$request->keyword}%");
                });
            }
            
            if (isset($request->speciality_id) && !empty($request->speciality_id)) {
               $subQuery1->where('s.specialty', $request->speciality_id);
            }
            
            if (isset($request->state_id) && !empty($request->state_id)) {
               $subQuery1->where('st.id', $request->state_id);
            }
            
            if (isset($request->status) && $request->status != 'all') {
               $subQuery1->where('red1.status', $request->status);
            }
                $subQuery1 = $subQuery1->groupBy('u.id');
            
            $subQuery2 = DB::table('redirects as red')
                ->select(
                    'u.id',
                    'u.name',
                    'u.role_id',
                    'u.email',
                    'u.phone',
                    'u.unique_id as user_unique_id',
                    DB::raw('NULL as specialty'),
                    DB::raw('NULL as agency_id'),
                    DB::raw('NULL as sub_status'),
                    'red.status as red_status',
                    DB::raw("GROUP_CONCAT(DISTINCT st.name ORDER BY st.name ASC SEPARATOR ', ') as state_names"),
                    DB::raw("GROUP_CONCAT(DISTINCT sf.title ORDER BY sf.title ASC SEPARATOR ', ') as desired_shifts"),
                    'red.notes'
                )
                ->leftJoin('users as u', 'u.id', '=', 'red.candidate_id')
                ->leftJoin('user_preferred_states as ups', 'u.id', '=', 'ups.user_id')
                ->leftJoin('states as st', 'ups.state_id', '=', 'st.id')
                ->leftJoin('user_preferred_shifts as upsh', 'u.id', '=', 'upsh.user_id')
                ->leftJoin('shifts as sf', 'upsh.shift_id', '=', 'sf.id');
                
             if (isset($request->keyword) && !empty($request->keyword)) {
                 $subQuery2->where(function ($query) use ($request) {
                    $query->where('st.name', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('sf.title', 'LIKE', "%{$request->keyword}%");
                });
            }
            if (isset($request->state_id) && !empty($request->state_id)) {
               $subQuery2->where('st.id', $request->state_id);
            }
            
            if (isset($request->status) && $request->status != 'all') {
               $subQuery2->where('red.status', $request->status);
            }
                $subQuery2 = $subQuery2->groupBy('u.id');
            
            $redirects = DB::table(DB::raw("({$subQuery1->toSql()} UNION {$subQuery2->toSql()}) as subquery"))
                ->mergeBindings($subQuery1)
                ->mergeBindings($subQuery2)
                ->select(
                    'id',
                    'name',
                    'role_id',
                    'email',
                    'phone',
                    'user_unique_id',
                    'specialty',
                    'agency_id',
                    'sub_status',
                    'red_status',
                    'state_names',
                    'desired_shifts',
                    'notes'
                )
                ->groupBy(
                    'id'
                )
                ->get()->toArray();
            
            $result = array('status' => true, 'message' => (count($redirects)) . " Record found", 'data' => $redirects);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to add/update redirect
    public function updateRedirect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'candidate_id' => 'required',
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
                DB::table('submissions')->where('id', $request->id)->update($param);
                $msg = "Submission has been been successfully updated";
            } else {
                
                $redirectRecord = DB::table('redirects')->select('id')
                    ->where('candidate_id', $request->candidate_id)->first();
                if(isset($redirectRecord) && !empty($redirectRecord))
                {
                    $result = array('status' => false, 'message' => "Employee(s) Record Already Exists in Redirect List");
                    return response()->json($result);
                }
                else
                {
                    $param['created_at'] = $this->entryDate;
                    $param['created_by'] = $request->user_id;
                    DB::table('redirects')->insert($param);
                    $msg = "Employee(s) added to redirect list successfully";
                }
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete redirect
    public function deleteRedirect(Request $request)
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

            DB::table('redirects')->where('candidate_id', $request->id)->delete();
            $msg = "Redirect has been been successfully deleted";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update redirect field
    public function updateRedirectField(Request $request)
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

            $param = array();
            $param[$request->field] = $request->value;
            $param['updated_at'] = $this->entryDate;
            $param['updated_by'] = $request->user_id;
            DB::table('redirects')->where('candidate_id', $request->id)->update($param);
            $msg = "Redirects has been been successfully updated";
            

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