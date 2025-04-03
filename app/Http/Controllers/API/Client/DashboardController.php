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
                ->where('j.user_id', $request->user_id)
                ->where('j.deleted_at', NULL)
                ->count();

            $data['totalCandidates'] = DB::table('users as u')
                ->where('u.created_by', $request->user_id)
                ->where('u.role_id', 4)
                ->where('u.deleted_at', NULL)
                ->count();

            $data['totalEmployees'] = DB::table('users as u')
                ->where('u.created_by', $request->user_id)
                ->where('u.role_id', 9)
                ->where('u.deleted_at', NULL)
                ->count();

            $data['totalApplicants'] = DB::table('users as u')
                ->where('u.created_by', $request->user_id)
                ->where('u.role_id', 5)
                ->where('u.deleted_at', NULL)
                ->count();

            $data['jobApplicantions'] = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('users as u', 'ja.user_id', '=', 'u.id')
                ->select('j.id', 'j.title', 'j.unique_id', 'ja.created_at', 's.name as state_name', 's.code as state_code',  'city.city_name', 'ja.status', 'u.name', 'u.unique_id as user_unique_id')
                ->where('j.user_id', $request->user_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.deleted_at', NULL)
                ->whereNull('u.deleted_at')
                ->limit(5)
                ->orderBy('ja.id', 'desc')
                ->get()->toArray();

            $data['recentCandidates'] = DB::table('users as u')
                ->select('u.id', 'u.unique_id', 'u.name', 'u.email', 'u.phone',  'u.created_at', 'u.profile_pic')
                ->where('u.created_by', $request->user_id)
                ->where('u.deleted_at', NULL)
                ->where('u.role_id', 4)
                ->limit(5)
                ->orderBy('u.id', 'desc')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic)) ? url(config('custom.user_folder') . $users->profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();

            $data['recentEmployees'] = DB::table('users as u')
                ->select('u.id', 'u.unique_id', 'u.name', 'u.email', 'u.phone',  'u.created_at', 'u.profile_pic')
                ->where('u.created_by', $request->user_id)
                ->where('u.deleted_at', NULL)
                ->where('u.role_id', 9)
                ->limit(5)
                ->orderBy('u.id', 'desc')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic)) ? url(config('custom.user_folder') . $users->profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();

            $result = array('status' => true, 'message' => "Dashboard data found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get resumes
    public function getResumes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {

            $resumes = DB::table('user_resumes as d')
                ->select('d.id', 'd.title',  'd.file_name', 'd.file_type', 'd.file_size', 'd.created_at')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->limit(4)
                ->get()
                ->map(function ($resumes) {
                    // Add dir_path column and its value to each record
                    $resumes->dir_path = url(config('custom.resume_folder') . $resumes->file_name);
                    return $resumes;
                })
                ->toArray();

            $result = array('status' => true, 'message' => (count($resumes)) . " Record found", 'data' => $resumes);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    public function generateResumeTitle()
    {
        $latestResume = DB::table('user_resumes')
            ->select('title')
            ->where('title', 'like', 'Resume%')
            ->orderBy('title', 'desc')
            ->first();

        if ($latestResume) {
            // Extract the number from the title and increment it
            $latestNumber = intval(substr($latestResume->title, strlen('Resume ')));
            $nextNumber = $latestNumber + 1;
            $newTitle = 'Resume ' . $nextNumber;
        } else {
            // If no existing resumes found, start with 1
            $newTitle = 'Resume 1';
        }

        return $newTitle;
    }

    ## Function to upload resume
    public function uploadResume(Request $request)
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

            if ($request->file('file_name')) {
                $file = $request->file('file_name');
                $imageSize = $request->file('file_name')->getSize();
                $ext = $file->getClientOriginalExtension();
                $fileName = time() * rand() . '.' . $ext;
                $path = config('custom.resume_folder');
                $upload = $file->move($path, $fileName);
                if ($upload) {
                    $param['file_name'] = $fileName;
                    $param['file_type'] = $ext;
                    $param['file_size'] = number_format($imageSize / 1048576, 2) . ' MB';
                }
            }
            $param['title'] = $this->generateResumeTitle();
            $param['user_id'] = $request->user_id;
            $param['created_at'] = $this->entryDate;
            DB::table('user_resumes')->insert($param);
            $msg = "Resume has been been successfully uploaded";


            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## FUnction to get Sidebar Data
    public function getSidebarData(Request $request)
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

            $user_id = $request->user_id;


            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

            $data['messages'] = DB::table('job_messages as jm')
                ->join('jobs as j', 'j.id', '=', 'jm.job_id')
                ->leftJoin('users as u', 'u.id', '=', 'j.user_id')
                ->leftJoin(DB::raw("(SELECT MAX(id) as latest_id FROM job_messages WHERE deleted_at IS NULL GROUP BY job_id) as latest"), 'jm.id', '=', 'latest.latest_id')
                ->select(
                    'j.id as job_id',
                    'j.title as job_title',
                    'u.name',
                    'u.unique_id',
                    'u.profile_pic',
                    DB::raw('(SELECT jm1.message FROM job_messages jm1 WHERE jm1.job_id = jm.job_id AND jm1.deleted_at IS NULL AND (jm1.from_id = "' . $user_id . '" OR jm1.to_id = "' . $user_id . '") ORDER BY jm1.id DESC LIMIT 1) as message'),
                    DB::raw('(SELECT jm2.created_at FROM job_messages jm2 WHERE jm2.job_id = jm.job_id AND jm2.deleted_at IS NULL AND (jm2.from_id = "' . $user_id . '" OR jm2.to_id = "' . $user_id . '") ORDER BY jm2.id DESC LIMIT 1) as created_at')
                )
                ->where(function ($query) use ($user_id) {
                    $query->where('jm.from_id', $user_id)
                        ->orWhere('jm.to_id', $user_id);
                })
                ->where('jm.is_seen', 0)
                ->whereNull('jm.deleted_at')
                ->orderBy('jm.id', 'desc')
                ->groupBy('jm.job_id')
                ->get()
                ->count();



            $data['applications'] = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('users as u', 'ja.user_id', '=', 'u.id')
                ->leftJoin('user_details as ud', 'u.id', '=', 'ud.user_id')
                ->leftJoin('user_preferred_employment_types as upet', 'u.id', '=', 'upet.user_id')
                ->leftJoin('user_preferred_shifts as ups', 'u.id', '=', 'ups.user_id')
                ->leftJoin('user_preferred_states as upst', 'u.id', '=', 'upst.user_id')
                ->select(
                    'ja.id as job_application_id',
                    'u.id as user_id',
                    'j.id as job_id',
                    'j.title',
                    'j.unique_id',
                    DB::raw("MAX(ja.created_at) as created_at"),
                    's.name as state_name',
                    's.code as state_code',
                    'city.city_name',
                    'ja.status',
                    'u.name',
                    'u.unique_id as user_unique_id',
                    DB::raw("
                        (
                            (CASE WHEN j.profession_id = ud.profession_id THEN 1 ELSE 0 END) +
                            (CASE WHEN j.specialty_id = ud.specialty_id THEN 1 ELSE 0 END) +
                            (CASE WHEN ud.available_start_date between j.start_date and j.end_date THEN 1 ELSE 0 END) +
                            (CASE WHEN j.employment_type_id = upet.employment_type_id THEN 1 ELSE 0 END) +
                            (CASE WHEN j.shift_id = ups.shift_id THEN 1 ELSE 0 END) +
                            (CASE WHEN j.state_id = upst.state_id THEN 1 ELSE 0 END) 
                        ) / 6 * 100 as match_percentage
                    ")
                )
                ->where('ja.is_seen', 0)
                ->where('j.user_id', $request->user_id)
                ->whereNull('ja.deleted_at')
                ->whereNull('j.deleted_at')
                ->orderBy('j.id', 'desc')
                ->groupBy(
                    'ja.id',
                    'u.id',
                    'j.id',
                    'j.title',
                    'j.unique_id',
                    's.name',
                    'city.city_name',
                    'ja.status',
                    'u.name',
                    'u.unique_id'
                )
                ->get()
                ->count();


            $result = array('status' => true, 'message' => "Dashboard data found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}
