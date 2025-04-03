<?php

namespace App\Http\Controllers\API\Client;

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
use Carbon\Carbon;

class UserController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to get users by their role id
    public function getUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'role_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {

            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            $users = DB::table('users as u')
                ->leftJoin('user_preferred_states as ups', 'u.id', '=', 'ups.user_id')
                ->leftJoin('states as s2', 'ups.state_id', '=', 's2.id')
                ->leftJoin('user_details as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->leftJoin('job_applications as ja', 'u.id', '=', 'ja.user_id')
                ->leftJoin('jobs as j', 'j.id', '=', 'ja.job_id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('professions as p', 'ud.profession_id', '=', 'p.id')
                ->select('u.id', 'u.name', 'u.unique_id','u.email', 'u.phone', 'u.created_at','u.profile_pic','s.name as state_name', 'city.city_name','u.status as user_status','j.title as job_title',
                    'j.unique_id as job_unique_id','ja.status as job_status','sp.specialty', 'ud.state_id','ud.city_id','ud.bio', 
                    DB:: raw('GROUP_CONCAT(DISTINCT s2.name ORDER BY s2.name SEPARATOR ", ") as desired_states'),'p.profession')
                ->where('u.role_id', $request->role_id)
                ->where('u.created_by', $request->user_id)
                ->where('u.deleted_at', NULL);

            if (isset($request->keyword) && !empty($request->keyword)) {
                $users->where('u.name', 'LIKE', "%{$request->keyword}%");
            }
            /*
            if (isset($request->state_id) && !empty($request->state_id)) {
                $users->where('ud.state_id', $request->state_id);
            }
            */
            if (isset($request->state_id) && !empty($request->state_id)) {
                $users->whereExists(function ($query) use ($request) {
                    $query->select(DB::raw(1))
                          ->from('user_preferred_states as ups')
                          ->whereColumn('ups.user_id', 'u.id')
                          ->where('ups.state_id', $request->state_id);
                });
            }
            if (isset($request->profession_id) && !empty($request->profession_id)) {
                $users->where('j.profession_id', $request->profession_id);
            }
            if (isset($request->speciality_id) && !empty($request->speciality_id)) {
                $users->where('j.specialty_id', $request->speciality_id);
            }

            if (isset($request->status) && $request->status != 'all') {
                $users->where('ja.status', $request->status);
            }

                $users = $users->orderBy('u.id', 'desc')
                ->groupBy(
                    'u.id'
                )
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();

            $result = array('status' => true, 'message' => (count($users)) . " Record found", 'data' => $users);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get interviewers
    public function getInterviwers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            $jobRow = DB::table('jobs as j')
                ->join('users as u','u.id','=','j.user_id')
                ->select('u.role_id')
                ->where(['j.id' => $request->job_id])
                ->first();
            
            if($jobRow->role_id == 2 || $jobRow->role_id == 3)
            {
                $users = DB::table('users as u')
                ->select('u.id', 'u.name')
                ->where('u.role_id', 6)
                ->where('u.created_by',$request->user_id)
                ->where('u.deleted_at', NULL)
                ->orderBy('u.name')->get()->toArray(); 
            }
            else
            {
                $users = DB::table('users as u')
                ->select('u.id', 'u.name')
                ->where('u.role_id', 6)
                ->where('u.deleted_at', NULL)
                ->orderBy('u.name')->get()->toArray();   
            }
            
            
            $result = array('status' => true, 'message' => (count($users)) . " Record found", 'data' => $users);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get all users(Applicants/Candidates/Employees) by appling filters
    public function getAllUsers(Request $request)
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
            $users = DB::table('users as u')
                ->leftJoin('user_preferred_states as ups', 'u.id', '=', 'ups.user_id')
                ->leftJoin('states as s2', 'ups.state_id', '=', 's2.id')
                ->leftJoin('user_details as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->leftJoin('specialities as sp', 'ud.specialty_id', '=', 'sp.id')
                ->select('u.id', 'u.name', 'u.unique_id','u.email', 'u.phone', 'u.created_at','u.profile_pic','s.name as state_name', 'city.city_name','u.status as user_status','sp.specialty', 
                'ud.state_id','ud.city_id','ud.bio',DB::raw('GROUP_CONCAT(DISTINCT s2.name ORDER BY s2.name SEPARATOR ", ") as desired_states'))
                ->where(function ($query) use ($request) {
                    $query->where('u.role_id',  4)
                          ->orWhere('u.role_id',  5)
                          ->orWhere('u.role_id',  9);
                })
                ->where('u.created_by', $request->user_id)
                ->where('u.deleted_at', NULL)
                ->where('ud.searchable_profile',1);

            if (isset($request->keyword) && !empty($request->keyword)) {
                $users->where('u.name', 'LIKE', "%{$request->keyword}%");
            }
            /*
            if (isset($request->state_id) && !empty($request->state_id)) {
                $users->where('ud.state_id', $request->state_id);
            }
            */
            if (isset($request->state_id) && !empty($request->state_id)) {
                $users->whereExists(function ($query) use ($request) {
                    $query->select(DB::raw(1))
                          ->from('user_preferred_states as ups')
                          ->whereColumn('ups.user_id', 'u.id')
                          ->where('ups.state_id', $request->state_id);
                });
            }
            if (isset($request->speciality_id) && !empty($request->speciality_id)) {
                $users->where('ud.specialty_id', $request->speciality_id);
            }
            if ((isset($request->start_date) && !empty($request->start_date)) || isset($request->end_date) && !empty($request->end_date)) {
                if(isset($request->start_date) && !empty($request->start_date))
                    $users->where('u.created_at', '>=', $request->start_date);
                    
                if(isset($request->end_date) && !empty($request->end_date))
                    $users->where('u.created_at', '<=', $request->end_date);
            }  

                $users = $users->orderBy('u.id', 'desc')
                ->groupBy('u.id')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($users)) . " Record found", 'data' => $users);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## add/update user Function
    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            if (isset($request->id) && !empty($request->id)) {
                # Check Email is already registered or not
                $query = CommonFunction::checkEmailExist($request->email, $request->role_id, $request->id);
                if ($query) {
                    $result = array('status' => false, 'message' => "Email is already registered");
                    return response()->json($result);
                }

                $param = array(
                    'name' => strip_tags($request->name),
                    'email' => strip_tags($request->email),
                    'phone' => strip_tags($request->phone),
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                );
                if(isset($request->password) && !empty($request->password))
                {
                 $param['password'] = Hash::make($request->password);
                }
                DB::table('users')->where('id', $request->id)->update($param);

                if(isset($request->state_id))
                {
                    # Get country_id from state_id
                    $country_id = 0;
                    $state = DB::table('states')->select('country_id')
                        ->where('id', $request->state_id)->first();
                    if (!empty($state)) {
                        $country_id = $state->country_id;
                    }

                    # Create entry in user_details
                    $param = [
                        'user_id' => $request->id,
                        'bio' => strip_tags($request->bio),
                        'country_id' => $country_id,
                        'state_id' => $request->state_id,
                        'city_id' => $request->city_id,
                        'updated_at' => $this->entryDate
                    ];
                    DB::table('user_details')->where('user_id', $request->id)->update($param);
                }
                DB::commit();
                
                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = $role." updated successfully";
                $result = array('status' => true, 'message' => $msg);
            }
            else{
                # Check Email is already registered or not or not
                $query = CommonFunction::checkEmailExist($request->email, $request->role_id);
                if ($query) {
                    $result = array('status' => false, 'message' => "Email is already registered");
                    return response()->json($result);
                }

                # Create the user
                $user = new User;
                $user->unique_id = $this->generateUniqueCode(8);
                $user->name = strip_tags($request->name);
                $user->email = strip_tags($request->email);
                $user->phone = strip_tags($request->phone);
                $user->password = Hash::make($request->password);
                $user->role_id = $request->role_id;
                $user->created_at = $this->entryDate;
                $user->created_by = $request->user_id;
                $user->save();

                if ($user->id) {

                    # Get country_id from state_id
                    $country_id = 0;
                    $state = DB::table('states')->select('country_id')
                        ->where('id', $request->state_id)->first();
                    if (!empty($state)) {
                        $country_id = $state->country_id;
                    }

                    # Create entry in user_details
                    $param = [
                        'user_id' => $user->id,
                        'bio' => strip_tags($request->bio),
                        'country_id' => $country_id,
                        'state_id' => $request->state_id,
                        'city_id' => $request->city_id,
                        'created_at' => $this->entryDate
                    ];
                    DB::table('user_details')->insert($param);

                    DB::commit();
                    
                    $role = CommonFunction::getRolebyRoleID($request->role_id);
                    $msg = $role." added successfully";
                    
                    $result = array('status' => true, 'message' => $msg);
                } else {
                    $result = array('status' => false, 'message' => "Something went wrong. Please try again");
                }
            }

        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
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

        if (User::where('unique_id', $code)->exists()) {
            $this->generateUniqueCode($codeLength);
        }

        return $code;
	}

    ## add/update candidate function
    public function addCandidate(Request $request)
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

            if(isset($request->step) && $request->step == 'step1')
            {

                $param = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ];

                if(empty($request->id))
                {
                    # Check Email is already registered or not or not
                    $query = CommonFunction::checkEmailExist($request->email, array(4,5,9));
                    if ($query) {
                        $result = array('status' => false, 'message' => "Email is already registered");
                        return response()->json($result);
                    }

                    $param['unique_id'] = $this->generateUniqueCode(8);
                    $param['password'] = Hash::make($request->password);
                    $param['role_id'] = $request->role_id;
                    $param['created_by'] = $request->user_id;
                    $param['status'] = 0;
                    $param['created_at'] = $this->entryDate;
                    $last_id = DB::table('users')->insertGetId($param);
                    
                    if($request->role_id == 4)
                        $msg = "Candidate has been successfully created";
                    else if($request->role_id == 9)
                        $msg = "Employee has been successfully created";
                }
                else
                {
                    # Check Email is already registered or not
                    $query = CommonFunction::checkEmailExist($request->email, array(4,5,9), $request->id);
                    if ($query) {
                        $result = array('status' => false, 'message' => "Email is already registered");
                        return response()->json($result);
                    }

                    if(isset($request->password) && !empty($request->password)) {
                        $param['password'] = Hash::make($request->password);
                    }
                    $param['updated_by'] = $request->user_id;
                    $param['updated_at'] = $this->entryDate;
                    DB::table('users')->where('id', $request->id)->update($param);
                    $last_id = $request->id;

                    /*DB::table('user_details')->where('user_id', $last_id)->delete();*/
                    
                    if($request->role_id == 4)
                        $msg = "Candidate has been successfully updated";
                    else if($request->role_id == 9)
                        $msg = "Employee has been successfully updated";
                }

                # Get country_id from state_id
                $country_id = null;
                $state = DB::table('states')->select('country_id')
                    ->where('id', $request->state_id)->first();
                if (!empty($state)) {
                    $country_id = $state->country_id;
                }

                /*
                if(empty($request->dob) || $request->dob == 'null')
                    $request->dob = NULL;
                */
                
                $info = [
                    'user_id' => $last_id,
                    'bio' => '',
                    'country_id' => $country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'address_line1' => $request->address_line1,
                    'address_line2' => $request->address_line2,
                    'dob' => NULL,
                    'total_experience' => '',
                    'created_at' => $this->entryDate,
                ];
                
                $udResult = DB::table('user_details')->select('id')
                    ->where('user_id', $last_id)->first();
                if (!empty($udResult)) {
                    DB::table('user_details')->where('user_id', $last_id)->update($info);
                }
                else {
                    DB::table('user_details')->insert($info);
                }
                
            }
            else if(isset($request->step) && $request->step == 'step2')
            {
                $param = [
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'salary_start_range' => $request->salary_start_range,
                    'salary_end_range' => $request->salary_end_range,
                    'salary_type' => $request->salary_type,
                    'employment_type_id' => $request->employment_type_id,
                    'min_work_per_week' => $request->min_work_per_week,
                    'progress' => 2,
                    'updated_at' => $this->entryDate,
                ];
                DB::table('jobs')->where('id', $request->id)->update($param);
                $last_id = $request->id;
            }
            else if(isset($request->step) && $request->step == 'step3')
            {
                $param = [
                    'description' => $request->description,
                    'qualification' => $request->qualification,
                    'responsibilities' => $request->responsibilities,
                    'progress' => 3,
                    'updated_at' => $this->entryDate,
                ];
                DB::table('jobs')->where('id', $request->id)->update($param);
                $last_id = $request->id;
            }
            else if(isset($request->step) && $request->step == 'step4')
            {
                $param = [
                    'progress' => 4,
                    'status' => 1,
                    'updated_at' => $this->entryDate,
                ];
                DB::table('jobs')->where('id', $request->id)->update($param);
                $last_id = $request->id;
            }
            
            
                
            DB::commit();
            $result = array('status' => true, 'message' => $msg,'data' => array('id' => $last_id));
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get user details

    public function getUserDetails(Request $request)
    {

         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            # Check for drafted job
            $user_id = $request->user_id;
            $raw = DB::table('users as u')
                ->leftJoin('user_details as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->select('u.*','ud.bio','ud.country_id','ud.state_id','ud.city_id','ud.address_line1','ud.address_line2','ud.dob','ud.total_experience','s.name as state_name', 'city.city_name')
                ->where(['u.unique_id' => $request->userID, 'u.created_by' => $request->user_id])
                ->first();
            // Check if $raw exists
            if ($raw) {
                // Add dir_path column and its value to the record
                $raw->profile_pic_path = (!empty($raw->profile_pic))?url(config('custom.user_folder') . $raw->profile_pic):'';
            }


            if(!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $raw);
            else
                $result = array('status' => false, 'message' => 'Invalid User ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get job preference
    public function getJobPreference(Request $request)
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
            $data = array();
            $result = DB::table('user_preferred_employment_types as us')
                ->select('us.employment_type_id')
                ->where('us.user_id', $request->userID)->get()->toArray();
            if(!empty($result))
            {
                foreach($result as $val)
                {
                    $data['employement_types'][] = $val->employment_type_id;
                }
            }

            $result = DB::table('user_preferred_shifts as us')
                ->select('us.shift_id')
                ->where('us.user_id', $request->userID)->get()->toArray();
            if(!empty($result))
            {
                foreach($result as $val)
                {
                    $data['shifts'][] = $val->shift_id;
                }
            }

            $result = DB::table('user_preferred_states as us')
                ->select('us.state_id')
                ->where('us.user_id', $request->userID)->get()->toArray();
            if(!empty($result))
            {
                foreach($result as $val)
                {
                    $data['desired_state_ids'][] = $val->state_id;
                }
            }
            
            $udResult = DB::table('user_details')->select('searchable_profile','profession_id','specialty_id','available_start_date')
                ->where('user_id', $request->userID)->first();
            if(!empty($udResult))
            {
                $data['searchable_profile'] = $udResult->searchable_profile;
                $data['profession_id'] = $udResult->profession_id;
                $data['specialty_id'] = $udResult->specialty_id;
                $data['available_start_date'] = $udResult->available_start_date;
            }

            $result = array('status' => true, 'message' => "record found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update job preference
    public function updateJobPreference(Request $request)
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
            DB::beginTransaction();


            DB::table('user_preferred_employment_types')->where('user_id', $request->userID)->delete();

            if(isset($request->employement_types) && !empty($request->employement_types))
            {
                foreach($request->employement_types as $k=>$v)
                {
                    $param = array(
                        'user_id' => $request->userID,
                        'employment_type_id' => $v,
                        'created_at' => $this->entryDate
                        );

                    DB::table('user_preferred_employment_types')->insert($param);
                }
            }

            DB::table('user_preferred_shifts')->where('user_id', $request->userID)->delete();

            if(isset($request->shifts) && !empty($request->shifts))
            {
                foreach($request->shifts as $k=>$v)
                {
                    $param = array(
                        'user_id' => $request->userID,
                        'shift_id' => $v,
                        'created_at' => $this->entryDate
                        );

                    DB::table('user_preferred_shifts')->insert($param);
                }
            }

            DB::table('user_preferred_states')->where('user_id', $request->userID)->delete();

            if(isset($request->desired_state_ids) && !empty($request->desired_state_ids))
            {
                foreach($request->desired_state_ids as $k=>$v)
                {
                    $param = array(
                        'user_id' => $request->userID,
                        'state_id' => $v,
                        'created_at' => $this->entryDate
                        );

                    DB::table('user_preferred_states')->insert($param);
                }
            }
            
            $info = [
                'searchable_profile' => ($request->searchable_profile)?$request->searchable_profile:"0",
                'profession_id' => ($request->profession_id)?$request->profession_id:0,
                'specialty_id' => ($request->specialty_id)?$request->specialty_id:0,
                'available_start_date' => ($request->available_start_date)?$request->available_start_date:"",
            ];
            DB::table('user_details')->where('user_id', $request->userID)->update($info);

            $msg = "Job preference has been successfully updated";
            DB::commit();
            $result = array('status' => true, 'message' => $msg);

        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update user's status
    public function updateUserStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'role_id' => 'required',
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
                DB::table('users')->where('id', $request->id)->where('role_id', $request->role_id)->update($param);
                
                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = $role." status has been successfully updated";
                

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to delete user
    public function deleteUser(Request $request)
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
                DB::table('users')->where('id', $request->id)->update($param);
                
                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = $role." has been successfully deleted";
                

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to perform bulk actions
    public function userBulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required',
            'user_id' => 'required',
            'bulk_action' => 'required',
            'role_id' => 'required'
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
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }
                
                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids)." ".$role."(s) has been successfully deleted";
                
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-active' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = 1;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }
                
                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids)." ".$role."(s) status has been successfully updated";
                
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-inactive' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = 0;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }
                
                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids)." ".$role."(s) has been successfully updated";
                

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'convert-to-applicant' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['role_id'] = 5;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }
                
                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids)." ".$role."(s) has been successfully converted to applicant";
                
                
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'convert-to-candidate' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['role_id'] = 4;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }
                
                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids)." ".$role."(s) has been successfully converted to job seeker";
                
                
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'convert-to-employee' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['role_id'] = 9;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }
                
                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids)." ".$role."(s) has been successfully converted to employee";
                

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

    ## Function to update user field
    public function updateUserField(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'field' => 'required',
            'role_id' => 'required',
            /*'value' => 'required',*/
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            $role = CommonFunction::getRolebyRoleID($request->role_id);
            
            /*
            if($request->field == 'role_id' && $request->value == 5)
            {
                $candidateRow = DB::table('job_applications as ja')
                ->select('ja.id')
                ->where('ja.user_id', $request->id)
                ->where('ja.deleted_at', NULL)
                ->first();
                if(empty($candidateRow))
                {
                    $result = array('status' => false, 'message' => "Conversion Failed: The current ".$role." cannot be converted to an applicant because they have not applied for any job.");
                    return response()->json($result);
                }
            }
            */
            
            $param = array();
            $param[$request->field] = $request->value;
            $param['updated_at'] = $this->entryDate;
            $param['updated_by'] = $request->user_id;
            DB::table('users')->where('id', $request->id)->update($param);
            
            
            $msg = $role." has been successfully updated";
                

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get user job applications
    public function getUserJobApplications(Request $request)
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

            $jobApplications = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('users as u', 'ja.user_id', '=', 'u.id')
                ->select('j.id as job_id','u.id as user_id', 'j.title', 'j.unique_id', 'ja.created_at','s.name as state_name',  'city.city_name', 'ja.status', 'u.name','u.unique_id as user_unique_id')
                ->where('j.user_id', $request->user_id)
                ->where('ja.user_id',$request->userID)
                ->where('j.deleted_at', NULL);
                
            // Apply filters if present
            if (isset($request->status) && $request->status != 'all') {
                $jobApplications->where('ja.status', $request->status);
            }
            if (isset($request->keyword) && !empty($request->keyword)) {
                $jobApplications->where('j.title', 'LIKE', "%{$request->keyword}%");
            }
            
                $jobApplications = $jobApplications->orderBy('ja.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($jobApplications)) . " Record found", 'data' => $jobApplications);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get user connections
    public function getUserConnections(Request $request)
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

            $users = array();
            
            $userRecords = DB::table('users as u')->select('ud.profession_id')
                    ->leftJoin('user_details as ud', 'ud.user_id', '=', 'u.id')
                    ->where('u.id', $request->userID)
                    ->where('ud.profession_id','!=',"0")
                    ->first();
            if (!empty($userRecords)) {
                
                /*
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                */
                
                $users = DB::table('users as u')
                ->join('user_details as ud', 'ud.user_id', '=', 'u.id')
                ->select('u.id', 'u.name', 'u.unique_id','u.profile_pic','u.role_id')
                ->where(function($query) {
                    $query->where('u.role_id', 4)
                          ->orWhere('u.role_id', 5)
                          ->orWhere('u.role_id', 9);
                })
                ->where('ud.profession_id',$userRecords->profession_id)
                ->where('u.id', '!=',$request->userID)
                ->where('u.deleted_at', NULL)
                ->orderBy('u.id', 'desc')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();
            }
            
            $result = array('status' => true, 'message' => (count($users)) . " Record found", 'data' => $users);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
 
    ## Function to get user references
    public function getUserReferences(Request $request)
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
            $userReferences = DB::table('user_references as ur')
                ->select('ur.*')
                ->where('ur.user_id', $request->userID)
                ->where('ur.deleted_at', NULL)
                ->orderBy('ur.id', 'asc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($userReferences)) . " Record found", 'data' => $userReferences);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ##Function to get user's Compalicne Files
    public function getUserComplianceFiles(Request $request)
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
            
            $today = Carbon::today();
            
            $complianceFiles = DB::table('compliance_files as cf')
                ->leftJoin('doc_types as dt', 'cf.type_id', '=', 'dt.id')
                ->leftJoin('users as u', 'cf.assigned_user_id', '=', 'u.id')
                ->select('cf.id', 'cf.title', 'cf.type_id','cf.cat_id', 'cf.assigned_user_id', 'cf.notes',  'cf.expiration_date', 'cf.file_name','dt.doc_name as doc_type_name',
                'u.name as assigned_to','u.unique_id as assigned_unique_id','cf.status')
                ->where('cf.deleted_at', NULL)
                ->where('dt.module_type', 'compliance')
                ->where('u.created_by',$request->user_id)
                ->where('cf.assigned_user_id',$request->userID)
                ;
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $complianceFiles->where('cf.title', 'LIKE', "%{$request->keyword}%");
            } 
            
            if (isset($request->tab) && $request->tab != 'all') {
                $complianceFiles->where('cf.cat_id', $request->tab);
            }   
            if (isset($request->status) && $request->status != 'all') {
                if($request->status == 'expired')
                    $complianceFiles->where('cf.expiration_date', '<', $today)->where('cf.is_archive','0');
                else if($request->status == 'archived')
                    $complianceFiles->where('cf.expiration_date', '>=', $today)->where('cf.is_archive','1');
                else
                    $complianceFiles->where('cf.expiration_date', '>=', $today)->where('cf.is_archive','0');
            }
              
            if ((isset($request->expiration_start_date) && !empty($request->expiration_start_date)) || isset($request->expiration_end_date) && !empty($request->expiration_end_date)) {
                if(isset($request->expiration_start_date) && !empty($request->expiration_start_date))
                    $complianceFiles->where('cf.expiration_date', '>=', $request->expiration_start_date);
                    
                if(isset($request->expiration_end_date) && !empty($request->expiration_end_date))
                    $complianceFiles->where('cf.expiration_date', '<=', $request->expiration_end_date);
            }  
              
                $complianceFiles = $complianceFiles->orderBy('cf.id', 'desc')->get()
                ->map(function ($complianceFiles) {
                    // Add dir_path column and its value to each record
                    $complianceFiles->dir_path = (!empty($complianceFiles->file_name))?url(config('custom.compliance_file_folder') . $complianceFiles->file_name):""; // Adjust 'your/directory/path/' to the actual directory path
                    return $complianceFiles;
                })
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($complianceFiles)) . " Record found", 'data' => $complianceFiles);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}
