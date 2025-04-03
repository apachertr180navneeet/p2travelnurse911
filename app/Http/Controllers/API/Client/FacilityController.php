<?php

namespace App\Http\Controllers\API\Client;

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

class FacilityController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to get client/facility types with countes
    public function getClientTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {

            /*
           $result = DB::table(DB::raw("(SELECT 'Orion Allied' as type UNION SELECT 'Orion Workforce' as type) as types"))
            ->leftJoin('facilities', 'types.type', '=', 'facilities.type')
            ->select('types.type', DB::raw('COUNT(facilities.id) as count'))
            ->groupBy('types.type')
            ->get();
            */

            $query = DB::table(DB::raw("(SELECT 'Orion Allied' as type UNION SELECT 'Orion Workforce' as type) as types"))
                ->leftJoin('facilities', function ($join) {
                    $join->on('types.type', '=', 'facilities.type')
                        ->whereNull('facilities.deleted_at');
                })
                ->select('types.type', DB::raw('COUNT(facilities.id) as count'))
                ->groupBy('types.type')
                ->get();

            $result = array('status' => true, 'message' => (count($query)) . " Record found", 'data' => $query);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get clients
    public function getClients(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $complianceFiles = DB::table('facilities as f')
                ->leftJoin('states as s', 'f.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'f.city_id', '=', 'city.id')
                ->select('f.*', 's.name as state_name', 's.code as state_code', 'city.city_name')
                ->where('f.deleted_at', NULL);


            if (isset($request->tab)) {
                $complianceFiles->where('f.type', $request->tab);
            }
            if (isset($request->keyword) && !empty($request->keyword)) {
                $complianceFiles->where(function ($query) use ($request) {
                    $query->where('f.title', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('s.name', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('city.city_name', 'LIKE', "%{$request->keyword}%");
                });
            }
            if (isset($request->status) && $request->status != 'all') {
                $complianceFiles->where('f.status', $request->status);
            }

            $complianceFiles = $complianceFiles->orderBy('f.id', 'desc')->get()
                ->toArray();


            $result = array('status' => true, 'message' => (count($complianceFiles)) . " Record found", 'data' => $complianceFiles);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to add/update clients
    public function updateClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            /*'type' => 'required',*/
            'state_id' => 'required',
            'city_id' => 'required',
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
                DB::table('facilities')->where('id', $request->id)->update($param);
                $msg = "Client has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('facilities')->insert($param);
                $msg = "Client has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to delete client
    public function deleteClient(Request $request)
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
            DB::table('facilities')->where('id', $request->id)->update($param);
            $msg = "Client has been been successfully deleted";


            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update client's status
    public function updateClientStatus(Request $request)
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
            DB::table('facilities')->where('id', $request->id)->update($param);
            $msg = "Client status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function get all assignments 
    public function getAssignments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {


            $complianceFiles = DB::table('assignments as a')
                ->leftJoin('states as s', 'a.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'a.city_id', '=', 'city.id')
                ->leftJoin('users as u', 'a.user_id', '=', 'u.id');
            if ($request->user_role_id == 2) {
                $complianceFiles->leftJoin('users as u2', 'a.facility_id', '=', 'u2.id')
                    ->select(
                        'a.facility_id as facility_title',
                        'a.confirmed_with_facility',
                        'a.confirmed_with_traveler',
                        'a.assigned_unit',
                        'a.assignment_length',
                        'a.shift',
                        'a.approved_time_off',
                        'a.facility_id',
                        'a.start_date',
                        'a.end_date',
                        'a.id',
                        'a.user_id',
                        'a.status',
                        'a.state_id',
                        'a.city_id',
                        'a.created_at',
                        's.name as state_name',
                        's.code as state_code',
                        'city.city_name',
                        'u.name',
                        'u.unique_id as user_unique_id'
                    );
            } else {
                $complianceFiles->leftJoin('facilities as f', 'a.facility_id', '=', 'f.id')
                    ->select(
                        'a.confirmed_with_facility',
                        'a.confirmed_with_traveler',
                        'a.assigned_unit',
                        'a.assignment_length',
                        'a.shift',
                        'a.approved_time_off',
                        'a.facility_id',
                        'a.start_date',
                        'a.end_date',
                        'a.facility_id as facility_title',
                        'a.id',
                        'a.user_id',
                        'a.status',
                        'a.state_id',
                        'a.city_id',
                        'a.created_at',
                        's.name as state_name',
                        's.code as state_code',
                        'city.city_name',
                        'u.name',
                        'u.unique_id as user_unique_id'
                    );
            }

            $complianceFiles->where('a.deleted_at', NULL)
                ->where('a.created_by', $request->user_id);

            if (isset($request->keyword) && !empty($request->keyword)) {
                $complianceFiles->where(function ($query) use ($request) {
                    $query->where('f.title', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('u.name', 'LIKE', "%{$request->keyword}%");
                });
            }

            if (isset($request->status) && $request->status != 'all') {
                $complianceFiles->where('a.status', $request->status);
            }

            $complianceFiles = $complianceFiles->orderBy('a.id', 'desc')->get()->toArray();

            /*
            $complianceFiles = DB::table('assignments as a')
                ->leftJoin('facilities as f', 'a.facility_id', '=', 'f.id')
                ->leftJoin('states as s', 'a.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'a.city_id', '=', 'city.id')
                ->leftJoin('users as u', 'a.user_id', '=', 'u.id')
                ->select('a.confirmed_with_facility','a.confirmed_with_traveler','a.assigned_unit','a.assignment_length','a.shift','a.approved_time_off','a.facility_id','a.start_date',
                'a.end_date','f.title as facility_title','a.id','a.user_id', 'a.status', 'a.state_id','a.city_id','a.created_at','s.name as state_name', 's.code as state_code', 'city.city_name','u.name',
                    'u.unique_id as user_unique_id')
                ->where('a.deleted_at', NULL);
            
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                 $complianceFiles->where(function ($query) use ($request) {
                    $query->where('f.title', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('u.name', 'LIKE', "%{$request->keyword}%");
                });
            } 
            if (isset($request->status) && $request->status != 'all') {
                $complianceFiles->where('a.status', $request->status);
            }
            
                $complianceFiles = $complianceFiles->orderBy('a.id', 'desc')->get()
                ->toArray();
            */

            $result = array('status' => true, 'message' => (count($complianceFiles)) . " Record found", 'data' => $complianceFiles);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to add/update assignments
    public function updateAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'assigned_user_id' => 'required',
            'facility_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = $request->all();
            $param['user_id'] = $request->assigned_user_id;
            unset($param['assigned_user_id']);
            unset($param['user_role_id']);
            unset($param['editAssignmentData']);

            $user_id = $request->user_id;

            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('assignments')->where('id', $request->id)->update($param);
                $msg = "Assignment has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('assignments')->insert($param);
                $msg = "Assignment has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to delete assignment
    public function deleteAssignment(Request $request)
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
            DB::table('assignments')->where('id', $request->id)->update($param);
            $msg = "Assignment has been been successfully deleted";


            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update assignment's status
    public function updateAssignmentStatus(Request $request)
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
            $param = array(
                'status' => $request->status,
                'updated_at' => $this->entryDate,
                'updated_by' => $request->user_id,
            );
            DB::table('assignments')->where('id', $request->id)->update($param);
            $msg = "Assignment status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}
