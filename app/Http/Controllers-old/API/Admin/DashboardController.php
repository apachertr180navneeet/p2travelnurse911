<?php

namespace App\Http\Controllers\API\Admin;

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
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }
    
    ## FUnction to get Dashboard Data
    public function getDashboardData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $data = array();
            
            $data['greetingText'] = CommonFunction::time_based_greeting();
            
            $data['totalJobs'] = DB::table('jobs as j')
                ->where('j.deleted_at', NULL)
                ->where('j.status', 1)
                ->count();
            
            
            $data['totalJobApplications'] = DB::table('job_applications as ja')
                ->join('jobs as j', 'j.id', '=', 'ja.job_id')
                ->where('j.status', 1)
                ->where('j.deleted_at', NULL)
                ->where('ja.deleted_at', NULL)
                ->count();
                
            $data['totalJobInterviews'] = DB::table('job_interviews as ji')
                ->join('jobs as j', 'j.id', '=', 'ji.job_id')
                ->where('j.deleted_at', NULL)
                ->where('ji.deleted_at', NULL)
                ->count();
            
            $data['totalApplicants'] = DB::table('users as u')
                ->where('u.role_id', 5)
                ->where('u.deleted_at', NULL)
                ->count();
            
            $data['totalCandidates'] = DB::table('users as u')
                ->where('u.role_id', 4)
                ->where('u.deleted_at', NULL)
                ->count();
                
            $data['totalEmployees'] = DB::table('users as u')
                ->where('u.role_id', 9)
                ->where('u.deleted_at', NULL)
                ->count();
                
            $data['totalAgencies'] = DB::table('users as u')
                ->where('u.role_id', 3)
                ->where('u.deleted_at', NULL)
                ->count();
                
            $data['totalFacilities'] = DB::table('users as u')
                ->where('u.role_id', 2)
                ->where('u.deleted_at', NULL)
                ->count();
            
            $data['totalInterviewers'] = DB::table('users as u')
                ->where('u.role_id', 6)
                ->where('u.deleted_at', NULL)
                ->count();
            
            $data['recentJobs'] = DB::table('jobs as j')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('users as u', 'j.user_id', '=', 'u.id')
                ->select('j.id', 'j.title', 'j.unique_id', 'j.created_at','s.name as state_name',  'city.city_name', 'j.status', 'u.name as creator_name','u.unique_id as creator_unique_id','u.role_id as creator_role_id')
                ->where('j.deleted_at', NULL)
                ->where('j.status', 1)
                ->limit(5)
                ->orderBy('j.id', 'desc')
                ->get()->toArray();
            
            $data['jobApplicantions'] = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('users as u', 'ja.user_id', '=', 'u.id')
                ->leftJoin('users as u2', 'j.user_id', '=', 'u2.id')
                ->select('j.id', 'j.title', 'j.unique_id', 'j.created_at as posted_on','ja.created_at','s.name as state_name',  'city.city_name', 'ja.status', 'u.name',
                'u.unique_id as user_unique_id','u2.name as creator_name','u2.unique_id as creator_unique_id','u2.role_id as creator_role_id')
                ->where('j.status', 1)
                ->where('j.deleted_at', NULL)
                ->limit(5)
                ->orderBy('ja.id', 'desc')
                ->get()->toArray();
            
            $data['recentApplicants'] = DB::table('users as u')
                ->select('u.id', 'u.unique_id', 'u.name', 'u.email','u.phone',  'u.created_at','u.profile_pic')
                ->where('u.deleted_at', NULL)
                ->where('u.role_id', 5)
                ->limit(5)
                ->orderBy('u.id', 'desc')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();
                
            $data['recentCandidates'] = DB::table('users as u')
                ->select('u.id', 'u.unique_id', 'u.name', 'u.email','u.phone',  'u.created_at','u.profile_pic')
                ->where('u.deleted_at', NULL)
                ->where('u.role_id', 4)
                ->limit(5)
                ->orderBy('u.id', 'desc')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();
                
            $data['recentEmployees'] = DB::table('users as u')
                ->select('u.id', 'u.unique_id', 'u.name', 'u.email','u.phone',  'u.created_at','u.profile_pic')
                ->where('u.deleted_at', NULL)
                ->where('u.role_id', 9)
                ->limit(5)
                ->orderBy('u.id', 'desc')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();
                
            $data['recentAgencies'] = DB::table('users as u')
                ->select('u.id', 'u.unique_id', 'u.name', 'u.email','u.phone',  'u.created_at','u.profile_pic')
                ->where('u.deleted_at', NULL)
                ->where('u.role_id', 3)
                ->limit(5)
                ->orderBy('u.id', 'desc')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();
             
             $data['recentFacilities'] = DB::table('users as u')
                ->select('u.id', 'u.unique_id', 'u.name', 'u.email','u.phone',  'u.created_at','u.profile_pic')
                ->where('u.deleted_at', NULL)
                ->where('u.role_id', 2)
                ->limit(5)
                ->orderBy('u.id', 'desc')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();
                
            $data['recentInterviewers'] = DB::table('users as u')
                ->select('u.id', 'u.unique_id', 'u.name', 'u.email','u.phone',  'u.created_at','u.profile_pic')
                ->where('u.deleted_at', NULL)
                ->where('u.role_id', 6)
                ->limit(5)
                ->orderBy('u.id', 'desc')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();
           
            $result = array('status' => true, 'message' => "Dashboard data found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

   
   
}
