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

class JobController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to get jobs
    public function getJobs(Request $request)
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
            $currentDate = date('Y-m-d');

            /*DB::enableQueryLog();*/

            $jobs = DB::table('jobs as j')
                ->leftJoin('users as u', 'j.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
                ->leftJoin('job_bookmarks as jb', function ($join) use ($user_id) {
                    $join->on('j.id', '=', 'jb.job_id')
                        ->where('jb.user_id', '=', $user_id);
                })
                /*
                ->leftJoin('job_boosted as jb2', function ($join) use ($currentDate) {
                    $join->on('j.id', '=', 'jb2.job_id')
                         ->where('jb2.start_date', '<=', $currentDate)
                         ->where('jb2.end_date', '>=', $currentDate);
                })
                */
                ->select(
                    'j.id',
                    'j.title',
                    'j.unique_id',
                    'j.total_opening',
                    'j.created_at',
                    'j.salary_start_range',
                    'j.show_pay_rate',
                    'j.salary_type',
                    'j.salary_end_range',
                    's.name as state_name',
                    's.code as state_code',
                    'city.city_name',
                    'p.profession',
                    'sp.specialty',
                    'sf.title as shift_title',
                    'u.profile_pic',
                    DB::raw('CASE 
                    WHEN u.role_id = 1 THEN 
                        (SELECT field_value FROM app_settings WHERE field_name = "app_name" AND field_value IS NOT NULL LIMIT 1)
                    ELSE u.name 
                 END AS company_name'),
                    'u.profile_pic',
                    'u.role_id as compnay_role_id',
                    DB::raw('CASE WHEN jb.id IS NOT NULL THEN 1 ELSE 0 END AS bookmark_exists')
                    /*,DB::raw('CASE WHEN jb2.id IS NOT NULL THEN 1 ELSE 0 END AS is_boosted')*/

                )
                /*->where('j.user_id', $request->user_id)*/
                ->where('j.deleted_at', NULL)
                ->where('u.deleted_at', NULL)
                ->where('j.status', 1);

            // Apply filters if present
            if (isset($request->job_title) && !empty($request->job_title)) {
                $jobs->where('j.title', 'LIKE', "%{$request->job_title}%");
            }
            if (isset($request->state_id) && $request->state_id != '0') {
                $jobs->where('j.state_id', $request->state_id);
            }

            if (isset($request->no_of_opening) && !empty($request->no_of_opening)) {
                if ($request->no_of_opening == '3+')
                    $jobs->where('j.total_opening', '>=', $request->no_of_opening);
                else
                    $jobs->where('j.total_opening', $request->no_of_opening);
            }

            if (isset($request->profession_id) && !empty($request->profession_id) && $request->profession_id != "0") {
                $jobs->where('j.profession_id', $request->profession_id);
            }

            if (isset($request->specialty_id) && !empty($request->specialty_id) && $request->specialty_id != "0") {
                $jobs->where('j.specialty_id', $request->specialty_id);
            }

            if (isset($request->job_type) && !empty($request->job_type)) {
                $jobs->where('j.employment_type_id', $request->job_type);
            }

            if (isset($request->listing_type) && !empty($request->listing_type) && $request->listing_type == "saved") {
                $jobs->whereExists(function ($query) use ($user_id) {
                    $query->select(DB::raw(1))
                        ->from('job_bookmarks as jb1')
                        ->whereRaw('jb1.job_id = j.id')
                        ->where('jb1.user_id', $user_id);
                });
            }

            if (isset($request->order_by) && $request->order_by == 'highest') {
                $jobs->orderByRaw('CAST(j.salary_start_range AS UNSIGNED) DESC');
            } else if (isset($request->order_by) && $request->order_by == 'lowest') {
                $jobs->orderByRaw('CAST(j.salary_start_range AS UNSIGNED) ASC');
            } else {
                $jobs->orderBy('j.id', 'desc');
            }

            /* 
                $jobs->orderBy('jb2.id', 'desc')
                ->orderBy('j.id', 'desc'); 
            */

            /* ->orderBy('is_boosted', 'desc') */
            $jobs = $jobs
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic)) ? url(config('custom.user_folder') . $users->profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();

            /*dd(DB::getQueryLog());*/

            $result = array('status' => true, 'message' => (count($jobs)) . " Record found", 'data' => $jobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ##FUnction to get bookmarked jobs
    public function getBookmarkedJobs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $jobs = DB::table('jobs as j')
                ->leftJoin('users as u', 'j.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
                ->join('job_bookmarks as jb', 'j.id', '=', 'jb.job_id')
                ->select(
                    'j.id',
                    'j.title',
                    'j.unique_id',
                    'j.total_opening',
                    'j.created_at',
                    'j.salary_start_range',
                    'j.salary_type',
                    'j.salary_end_range',
                    'j.show_pay_rate',
                    's.name as state_name',
                    's.code as state_code',
                    'city.city_name',
                    'p.profession',
                    'sp.specialty',
                    'sf.title as shift_title',
                    'u.profile_pic',
                    DB::raw('CASE 
                    WHEN u.role_id = 1 THEN 
                        (SELECT field_value FROM app_settings WHERE field_name = "app_name" AND field_value IS NOT NULL LIMIT 1)
                    ELSE u.name 
                 END AS company_name'),
                    'u.profile_pic',
                    'u.role_id as compnay_role_id'
                )
                ->where('jb.user_id', $request->user_id)
                ->where('j.deleted_at', NULL)
                ->where('j.status', 1);

            // Apply filters if present
            if (isset($request->job_title) && !empty($request->job_title)) {
                $jobs->where('j.title', 'LIKE', "%{$request->job_title}%");
            }
            if (isset($request->state_id) && $request->state_id != '0') {
                $jobs->where('j.state_id', $request->state_id);
            }

            if (isset($request->no_of_opening) && !empty($request->no_of_opening)) {
                if ($request->no_of_opening == '3+')
                    $jobs->where('j.total_opening', '>=', $request->no_of_opening);
                else
                    $jobs->where('j.total_opening', $request->no_of_opening);
            }

            if (isset($request->profession_id) && !empty($request->profession_id) && $request->profession_id != "0") {
                $jobs->where('j.profession_id', $request->profession_id);
            }

            if (isset($request->specialty_id) && !empty($request->specialty_id) && $request->specialty_id != "0") {
                $jobs->where('j.specialty_id', $request->specialty_id);
            }


            if (isset($request->job_type) && !empty($request->job_type)) {
                $jobs->where('j.employment_type_id', $request->job_type);
            }

            if (isset($request->listing_type) && !empty($request->listing_type) && $request->listing_type == "saved") {
                $jobs->whereExists(function ($query) use ($user_id) {
                    $query->select(DB::raw(1))
                        ->from('job_bookmarks as jb1')
                        ->whereRaw('jb1.job_id = j.id')
                        ->where('jb1.user_id', $user_id);
                });
            }

            if (isset($request->order_by) && $request->order_by == 'highest') {
                $jobs->orderByRaw('CAST(j.salary_start_range AS UNSIGNED) DESC');
            } else if (isset($request->order_by) && $request->order_by == 'lowest') {
                $jobs->orderByRaw('CAST(j.salary_start_range AS UNSIGNED) ASC');
            } else {
                $jobs->orderBy('j.id', 'desc');
            }

            $jobs = $jobs->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic)) ? url(config('custom.user_folder') . $users->profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();


            if (count($jobs) > 0) {
                foreach ($jobs as $job) {
                    $param = array(
                        'is_seen' => 1
                    );
                    DB::table('job_bookmarks')->where('user_id', $request->user_id)->where('job_id', $job->id)->where('is_seen', 0)->update($param);
                }
            }

            $result = array('status' => true, 'message' => (count($jobs)) . " Record found", 'data' => $jobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get Job Detail
    public function getJobDetail(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'jobId' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {


            # Check for drafted job
            $user_id = $request->user_id;
            $raw = DB::table('jobs as j')
                ->leftJoin('clients as c', 'c.user_id', '=', 'j.user_id')
                ->leftJoin('users as u', 'u.id', '=', 'j.user_id')
                ->leftJoin('states as s2', 'c.state_id', '=', 's2.id')
                ->leftJoin('cities as city2', 'c.city_id', '=', 'city2.id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
                ->leftJoin('employment_types as et', 'j.employment_type_id', '=', 'et.id')
                ->leftJoin('job_bookmarks as jb', function ($join) use ($user_id) {
                    $join->on('j.id', '=', 'jb.job_id')
                        ->where('jb.user_id', '=', $user_id);
                })
                ->leftJoin('job_applications as ja', function ($join) use ($user_id) {
                    $join->on('j.id', '=', 'ja.job_id')
                        ->where('ja.user_id', '=', $user_id);
                })
                ->select(
                    'j.*',
                    's.name as state_name',
                    's.code as state_code',
                    'city.city_name',
                    'p.profession',
                    'sp.specialty',
                    'sf.title as shift_title',
                    'et.title as employment_type_title',
                    DB::raw('CASE WHEN jb.id IS NOT NULL THEN 1 ELSE 0 END AS bookmark_exists'),
                    DB::raw('CASE WHEN ja.id IS NOT NULL THEN 1 ELSE 0 END AS job_applied'),
                    'c.id as company_id',
                    'c.company_name',
                    'c.primary_industry',
                    'c.company_size',
                    'c.bio',
                    'c.founded_in',
                    's2.name as company_state_name',
                    's2.code as company_state_code',
                    'city2.city_name as company_city_name',
                    'c.created_at as company_created_at',
                    'u.profile_pic as company_profile_pic',
                    'u.unique_id as company_unique_id',
                    'ja.status as job_application_status',
                    DB::raw('CASE 
                    WHEN u.role_id = 1 THEN 
                        (SELECT field_value FROM app_settings WHERE field_name = "app_name" AND field_value IS NOT NULL LIMIT 1)
                    ELSE u.name 
                 END AS main_company_name')
                )
                ->where(['j.unique_id' => $request->jobId])
                ->where('j.status', 1)
                ->get()
                ->map(function ($raw) {
                    // Add dir_path column and its value to each record
                    $raw->company_profile_pic_path = (!empty($raw->company_profile_pic)) ? url(config('custom.user_folder') . $raw->company_profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $raw;
                })
                ->first();

            $attachments = DB::table('job_attatchments as d')
                ->select('d.id', 'd.title', 'd.file_name', 'd.file_type', 'd.file_size')
                ->where('d.job_id', $raw->id)
                ->where('d.deleted_at', NULL)
                ->whereNotNull('d.file_name') // Add this condition
                ->where('d.file_name', '!=', '') // Add this condition
                ->orderBy('d.id', 'desc')
                ->get()
                ->map(function ($attachments) {
                    // Add dir_path column and its value to each record
                    $attachments->dir_path = url(config('custom.job_attchment_folder') . $attachments->file_name);
                    return $attachments;
                })
                ->toArray();

            // Add attachments list to the $raw object
            $raw->attachments = $attachments;

            if (!$raw->company_id) {
                $adminRow = DB::table('app_settings')
                    ->select('field_value')
                    ->where('field_name', 'app_name')
                    ->get()
                    ->first();
                $raw->admin_app_name = $adminRow->field_value;

                $adminRow = DB::table('app_settings')
                    ->select('field_value')
                    ->where('field_name', 'company_bio')
                    ->get()
                    ->first();
                $raw->admin_company_bio = $adminRow->field_value;

                $adminRow = DB::table('app_settings')
                    ->select('field_value')
                    ->where('field_name', 'company_email')
                    ->get()
                    ->first();
                $raw->admin_company_email = $adminRow->field_value;

                $adminRow = DB::table('app_settings')
                    ->select('field_value')
                    ->where('field_name', 'company_phone')
                    ->get()
                    ->first();
                $raw->admin_company_phone = $adminRow->field_value;

                $adminRow = DB::table('app_settings')
                    ->select('field_value')
                    ->where('field_name', 'company_address')
                    ->get()
                    ->first();
                $raw->admin_company_address = $adminRow->field_value;

                $adminRow = DB::table('app_settings')
                    ->select('field_value')
                    ->where('field_name', 'company_website')
                    ->get()
                    ->first();
                $raw->admin_company_website = $adminRow->field_value;
            }

            if (!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $raw);
            else
                $result = array('status' => false, 'message' => 'Invalid job ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to add/remove job as bookmark
    public function updateJobBookmark(Request $request)
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

            $result = DB::table('job_bookmarks as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.job_id', $request->job_id)
                ->first();

            if (!empty($result)) {
                DB::table('job_bookmarks')->where('user_id', $request->user_id)->where('job_id', $request->job_id)->delete();
                $result = array('status' => true, 'message' => "Job removed from saved jobs", 'data' => 0);
            } else {
                $param = array(
                    'user_id' => $request->user_id,
                    'job_id' => $request->job_id,
                );
                DB::table('job_bookmarks')->insert($param);
                $result = array('status' => true, 'message' => 'Job marked as saved job', 'data' => 1);
            }
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to insert job message
    public function insertJobMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'message' => 'required',
            'job_id' => 'required_without:job_request_id', // job_id is required if job_request_id is null
            'job_request_id' => 'required_without:job_id', // job_request_id is required if job_id is null
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $where = [];
            if($request->job_id) {
                $where[] = ['jc.job_id', $request->job_id];
                $agencyUser = DB::table('jobs as j')
                    ->join('users as u', 'j.user_id', '=', 'u.id')
                    ->join('clients as c', 'c.user_id', '=', 'u.id')
                    ->select('c.id as client_id', 'u.id', 'u.name', 'u.email', 'j.unique_id', 'j.title as job_title')
                    ->where(['j.id' => $request->job_id])
                    ->first();

            } elseif($request->job_request_id) {
                $where[] = ['jc.job_request_id', $request->job_request_id];
                $agencyUser = DB::table('clients as c')
                    ->join('users as u', 'c.user_id', '=', 'u.id')
                    ->select('c.id as client_id', 'u.id', 'u.name', 'u.email')
                    ->where(['c.id' => $request->client_id])
                    ->first();        
            }


            if (!isset($request->job_chat_id)) {
                
                $jobChatResult = DB::table('job_chats as jc')
                    ->select('jc.id')
                    ->where($where)
                    ->where(['jc.job_seeker_id' => $request->user_id, 'employer_id' => $agencyUser->client_id])
                    ->first();
                if (empty($jobChatResult)) {
                    $chatParam = array(
                        'job_seeker_id' => $request->user_id,
                        'employer_id' => $agencyUser->client_id,
                        'created_by' => $request->user_id,
                        'created_at' => $this->entryDate,
                    );
                    if (!empty($request->job_request_id)) {
                        $chatParam['job_request_id'] = $request->job_request_id;
                    } else if (!empty($request->job_id)) {
                        $chatParam['job_id'] = $request->job_id;
                    }
                    $job_chat_id = DB::table('job_chats')->insertGetId($chatParam);
                } else {
                    $job_chat_id = $jobChatResult->id;
                }
            } else {
                $job_chat_id = $request->job_chat_id;
            }

            $param = array(
                'job_chat_id' => $job_chat_id,
                'from_id' => $request->user_id,
                'to_id' => $agencyUser->id,
                'message' => $request->message,
                'created_by' => $request->user_id,
                'created_at' => $this->entryDate,
            );
            $last_id = DB::table('job_chat_messages')->insertGetId($param);
            if ($last_id) {

                /*
            $agencyUser = DB::table('jobs as j')
                ->join('users as u', 'j.user_id', '=', 'u.id')
                ->select('u.name','u.email','j.unique_id')
                ->where(['j.id' => $request->job_id])
                ->first();
            */

                $user = DB::table('users as u')
                    ->select('u.name')
                    ->where(['u.id' => $request->user_id])
                    ->first();

                if (!empty($request->job_request_id)) {
                    $param = array(
                        'receiver_name' => $agencyUser->name,
                        'receiver_email' => $agencyUser->email,
                        'sender_name' => $user->name,
                        'message' => $request->message,
                        'dashboard_path' => config('custom.client_login_url')
                    );
                    Mail::send('emails.client.job-request-message', $param, function ($message) use ($param) {
                        $message->subject($param['sender_name'] . ' ' . config('custom.job_message'));
                        $message->to($param['receiver_email']);
                    });
                } else if (!empty($request->job_id)) {
                    $param = array(
                        'job_title' => $agencyUser->job_title,
                        'job_unique_id' => $agencyUser->unique_id,
                        'receiver_name' => $agencyUser->name,
                        'receiver_email' => $agencyUser->email,
                        'sender_name' => $user->name,
                        'message' => $request->message,
                        'dashboard_path' => config('custom.client_login_url')
                    );
                    Mail::send('emails.user.job-message', $param, function ($message) use ($param) {
                        $message->subject($param['sender_name'] . ' ' . config('custom.job_message'));
                        $message->to($param['receiver_email']);
                    });
                }
                DB::commit();
                $result = array('status' => true, 'message' => "Message has been successfully posted");
            } else {
                $result = array('status' => false, 'message' => 'Something went wrong, please try again later');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to apply job 
    public function applyJob(Request $request)
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

            $result = DB::table('job_applications as ja')
                ->select('ja.id')
                ->where('ja.user_id', $request->user_id)
                ->where('ja.job_id', $request->job_id)
                ->first();

            if (!empty($result)) {
                /*
                $param['updated_at'] = $this->entryDate;
                $param['deleted_at'] = $this->entryDate;
                DB::table('job_applications')->where('id', $result->id)->update($param);
                */
                DB::table('job_applications')->where('user_id', $request->user_id)->where('job_id', $request->job_id)->delete();
                $result = array('status' => true, 'message' => "Job application has been canceled", 'data' => 0);
            } else {
                $param = array(
                    'user_id' => $request->user_id,
                    'job_id' => $request->job_id,
                    'status' => 1,
                    'created_at' => $this->entryDate,
                );
                DB::table('job_applications')->insert($param);

                $param = array(
                    'role_id' => 5
                );
                DB::table('users')->where('id', $request->user_id)->where('role_id', 4)->update($param);


                # Send notification email to job employer
                $employerResult = DB::table('jobs as j')
                    ->leftJoin('users as u', 'u.id', '=', 'j.user_id')
                    ->select('j.title', 'u.name', 'u.email', 'u.role_id')
                    ->where('j.id', $request->job_id)
                    ->first();

                if ($employerResult) {
                    $userRecord = User::where('id', $request->user_id)->first();

                    if ($employerResult->role_id == 1 || $employerResult->role_id == 7) {
                        $admin_email = DB::table('app_settings')
                            ->select('field_value')
                            ->where(['field_name' => 'admin_email'])
                            ->where('field_value', '!=', NULL)
                            ->first();

                        if (isset($admin_email->field_value) && !empty($admin_email->field_value)) {
                            $param = [
                                'receiver_name' => 'Admin',
                                'job_title' => $employerResult->title,
                                'applicant_name' => $userRecord->name,
                                'applicant_email' => $userRecord->email,
                                'login_url' => config('custom.admin_login_url'),
                                'receiver_email' => $admin_email->field_value,
                                'login_url_btn_text' => 'Login to Admin Dashboard'
                            ];
                            Mail::send('emails.user.job-application', $param, function ($message) use ($param) {
                                $message->subject(config('custom.job_application'));
                                $message->to($param['receiver_email']);
                            });
                        }
                    } else {
                        $param = [
                            'receiver_name' => $employerResult->name,
                            'job_title' => $employerResult->title,
                            'applicant_name' => $userRecord->name,
                            'applicant_email' => $userRecord->email,
                            'login_url' => config('custom.client_login_url'),
                            'receiver_email' => $employerResult->email,
                            'login_url_btn_text' => 'Login to Client Dashboard'
                        ];
                        Mail::send('emails.user.job-application', $param, function ($message) use ($param) {
                            $message->subject(config('custom.job_application'));
                            $message->to($param['receiver_email']);
                        });
                    }
                }

                $result = array('status' => true, 'message' => "You’ve successfully applied for this job", 'data' => 1);
            }
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get applied jobs
    public function getAppliedJobs(Request $request)
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

            $appliedJobs = DB::table('job_applications as ja')
                ->join('jobs as j', 'j.id', '=', 'ja.job_id')
                ->leftJoin('users as u', 'j.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('job_bookmarks as jb', function ($join) use ($user_id) {
                    $join->on('j.id', '=', 'jb.job_id')
                        ->where('jb.user_id', '=', $user_id);
                })
                ->select(
                    'j.id',
                    'j.title',
                    'j.unique_id',
                    'ja.created_at',
                    's.name as state_name',
                    's.code as state_code',
                    'city.city_name',
                    'ja.status',
                    DB::raw('CASE 
                    WHEN u.role_id = 1 THEN 
                        (SELECT field_value FROM app_settings WHERE field_name = "app_name" AND field_value IS NOT NULL LIMIT 1)
                    ELSE u.name 
                 END AS company_name'),
                    DB::raw('CASE WHEN jb.id IS NOT NULL THEN 1 ELSE 0 END AS bookmark_exists')
                )
                ->where('ja.user_id', $request->user_id)
                ->where('j.deleted_at', NULL)
                ->where('u.deleted_at', NULL)
                ->where('ja.deleted_at', NULL)
                ->where('j.status', 1);

            if (isset($request->job_status) && $request->job_status != '') {
                $appliedJobs->where('ja.status', $request->job_status);
            }
            if (isset($request->keyword) && $request->keyword != '') {
                $keyword = '%' . $request->keyword . '%';

                $appliedJobs->where('j.title', 'like', $keyword);
            }
            $appliedJobs = $appliedJobs->orderBy('ja.id', 'desc')
                ->get()->toArray();

            if (count($appliedJobs) > 0) {
                foreach ($appliedJobs as $job) {
                    $param = array(
                        'is_seen' => 1
                    );
                    DB::table('job_applications')->where('user_id', $request->user_id)->where('job_id', $job->id)->where('is_seen', 0)->update($param);
                }
            }

            $result = array('status' => true, 'message' => (count($appliedJobs)) . " Record found", 'data' => $appliedJobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## FUnction to get unique job message
    public function getUniqueJobMessages(Request $request)
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
            $user_id = $request->user_id;

            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");


            $uniqueJobs = DB::table('job_messages as jm')
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
                ->whereNull('jm.deleted_at')
                ->where('j.status', 1);

            // Apply filters if present
            if (isset($request->keyword) && !empty($request->keyword)) {
                
                
                $uniqueJobs->where(function ($query) use ($request) {
                    $query->where('j.title', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('message', 'LIKE', "%{$request->keyword}%");
                });
            }

            $uniqueJobs = $uniqueJobs->orderBy('created_at','desc') //('jm.id', 'desc')
                ->groupBy('jm.job_id')
                ->get()
                ->map(function ($uniqueJobs) {
                    // Add dir_path column and its value to each record
                    $uniqueJobs->profile_pic_path = (!empty($uniqueJobs->profile_pic)) ? url(config('custom.user_folder') . $uniqueJobs->profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uniqueJobs;
                })
                ->toArray();
            */

            $user_id = $request->user_id;


            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

            /*
            $uniqueJobs = DB::table('job_messages as jm')
                ->join('jobs as j', 'j.id', '=', 'jm.job_id')
                ->leftJoin('users as u', 'u.id', '=', 'j.user_id')
                ->leftJoin(DB::raw("(SELECT MAX(id) as latest_id, job_id FROM job_messages WHERE deleted_at IS NULL GROUP BY job_id) as latest"), 'jm.job_id', '=', 'latest.job_id')
                ->leftJoin('job_messages as jm_latest', 'jm_latest.id', '=', 'latest.latest_id') // Joining with latest message
                ->select(
                    'j.id as job_id',
                    'j.title as job_title',
                    'u.name',
                    'u.unique_id',
                    'u.profile_pic',
                    'jm_latest.message', // Using latest message
                    'jm_latest.created_at' // Using the latest message time
                )
                ->where(function ($query) use ($user_id) {
                    $query->where('jm.from_id', $user_id)
                          ->orWhere('jm.to_id', $user_id);
                })
                ->whereNull('jm.deleted_at')
                ->where('j.status', 1);
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                $uniqueJobs->where(function ($query) use ($request) {
                    $query->where('j.title', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('jm_latest.message', 'LIKE', "%{$request->keyword}%");
                });
            }
            
            $uniqueJobs = $uniqueJobs->orderBy('jm_latest.created_at', 'desc') // Sorting by last message time
                ->groupBy('jm.job_id')
                ->get()
                ->map(function ($uniqueJobs) {
                    $uniqueJobs->profile_pic_path = (!empty($uniqueJobs->profile_pic)) 
                        ? url(config('custom.user_folder') . $uniqueJobs->profile_pic) 
                        : '';
                    return $uniqueJobs;
                })
                ->toArray();
            */



            $uniqueJobs = DB::table('job_chats as jc')
                ->join('job_chat_messages as jm', 'jc.id', '=', 'jm.job_chat_id')
                ->leftJoin('jobs as j', 'j.id', '=', 'jc.job_id') // Left join to handle null job_id
                ->leftJoin('job_requests as jr', 'jr.id', '=', 'jc.job_request_id')
                ->join('clients as c', 'c.id', '=', 'jc.employer_id')
                ->join('users as u', 'u.id', '=', 'c.user_id')
                ->select(
                    'c.id as client_id',
                    'jc.id as job_chat_id',
                    'j.id as job_id',
                    'jr.id as job_request_id',
                    DB::raw('IF(jc.job_id IS NOT NULL, j.title, "Job Request") as job_title'), // Job or Job Request Title
                    'u.name',
                    'u.unique_id',
                    'u.profile_pic',
                    DB::raw('(SELECT jm1.message FROM job_chat_messages jm1 WHERE jm1.job_chat_id = jc.id ORDER BY jm1.id DESC LIMIT 1) as message'),
                    DB::raw('(SELECT jm2.created_at FROM job_chat_messages jm2 WHERE jm2.job_chat_id = jc.id ORDER BY jm2.id DESC LIMIT 1) as created_at'),
                    DB::raw('COALESCE(j.id, jr.id) as entity_id')
                )
                ->where(function ($query) use ($user_id) {
                    $query->where('jc.job_seeker_id', $user_id);
                })
                ->where(function ($query) {
                    $query->whereNotNull('jc.job_id')
                        ->orWhereNotNull('jc.job_request_id'); // Ensure at least one ID exists
                })
                /*->whereNull('jm.deleted_at')*/
                ->where(function ($query) {
                    $query->where('j.status', 1)
                        ->orWhere('jr.status',1); // Ensure at least one ID exists
                })
                ->when(isset($request->keyword) && !empty($request->keyword), function ($query) use ($request) {
                        $query->where(function ($subQuery) use ($request) {
                        $subQuery->where('j.title', 'LIKE', "%{$request->keyword}%")
                            ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                            ->orWhere('jm.message', 'LIKE', "%{$request->keyword}%");
                    });
                })
                ->orderBy('jc.created_at', 'desc') // Sorting by the latest message time
                ->groupBy(DB::raw('COALESCE(j.id, jr.id)'), 'u.id') // Grouping by job or job_request and user
                ->get()
                ->map(function ($uniqueJob) {
                    $uniqueJob->profile_pic_path = (!empty($uniqueJob->profile_pic))
                    ? url(config('custom.user_folder') . $uniqueJob->profile_pic)
                    : '';
                    return $uniqueJob;
                })
                ->toArray();

            $result = array('status' => true, 'message' => (count($uniqueJobs)) . " Record found", 'data' => $uniqueJobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## FUnction to get job message
    public function getJobMessages(Request $request)
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

            /*
            $uniqueJobs = DB::table('job_messages as jm')
                ->join('jobs as j', 'j.id', '=', 'jm.job_id')
                ->leftJoin('users as u', 'u.id', '=', 'jm.from_id')
                ->leftJoin('users as u2', 'u2.id', '=', 'jm.to_id')
                ->select(
                    'jm.id',
                    'j.id as job_id',
                    'jm.message',
                    'jm.created_at',
                    'u.id as sender_user_id',
                    'u.name as sender_name',
                    'u.unique_id as sender_unique_id',
                    'u.profile_pic as sender_profile_pic',
                    'u2.id as receiver_user_id',
                    'u2.name as receiver_name',
                    'u2.unique_id as receiver_unique_id',
                    'u2.profile_pic as receiver_profile_pic'
                )
                ->where(function ($query) use ($user_id) {
                    $query->where('jm.from_id', $user_id)
                        ->orWhere('jm.to_id', $user_id);
                })
                ->where('j.status', 1)
                ->where('j.id', $request->job_id)
                ->whereNull('jm.deleted_at')
                ->orderBy('jm.created_at', 'ASC')
                ->get()
                ->map(function ($uniqueJobs) {
                    // Add dir_path column and its value to each record
                    $uniqueJobs->sender_profile_pic_path = (!empty($uniqueJobs->sender_profile_pic)) ? url(config('custom.user_folder') . $uniqueJobs->sender_profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uniqueJobs;
                })
                ->map(function ($uniqueJobs) {
                    // Add dir_path column and its value to each record
                    $uniqueJobs->receiver_profile_pic_path = (!empty($uniqueJobs->receiver_profile_pic)) ? url(config('custom.user_folder') . $uniqueJobs->receiver_profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uniqueJobs;
                })
                ->toArray();

            if (count($uniqueJobs) > 0) {
                foreach ($uniqueJobs as $job) {
                    $param = array(
                        'is_seen' => 1
                    );
                    DB::table('job_messages')->where('id', $job->id)->where('job_id', $job->job_id)->where('is_seen', 0)->update($param);
                }
            }
            */


            $uniqueJobs = DB::table('job_chats as jc')
                ->join('job_chat_messages as jm', 'jc.id', '=', 'jm.job_chat_id')
                ->leftJoin('jobs as j', 'j.id', '=', 'jc.job_id') // Left join to handle null job_id
                ->leftJoin('job_requests as jr', 'jr.id', '=', 'jc.job_request_id')
                ->leftJoin('users as u', 'u.id', '=', 'jm.from_id')
                ->leftJoin('users as u2', 'u2.id', '=', 'jm.to_id')
                ->select(
                    'jm.id',
                    'j.id as job_id',
                    'jr.id as job_request_id',
                    'jm.message',
                    'jm.created_at',
                    'u.id as sender_user_id',
                    'u.name as sender_name',
                    'u.unique_id as sender_unique_id',
                    'u.profile_pic as sender_profile_pic',
                    'u2.id as receiver_user_id',
                    'u2.name as receiver_name',
                    'u2.unique_id as receiver_unique_id',
                    'u2.profile_pic as receiver_profile_pic'
                )
                ->where('jc.id', $request->job_chat_id)                
                ->where(function ($query) use ($request) {
                    if ($request->job_id) {
                        $query->where('j.id', $request->job_id)->where('j.status', 1);
                    } elseif ($request->job_request_id) {
                        $query->where('jr.id', $request->job_request_id)->where('jr.status', 1);
                    }
                })
                ->orderBy('jm.created_at', 'ASC')
                ->get()
                ->map(function ($uniqueJobs) {
                    // Add dir_path column and its value to each record
                    $uniqueJobs->sender_profile_pic_path = (!empty($uniqueJobs->sender_profile_pic)) ? url(config('custom.user_folder') . $uniqueJobs->sender_profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uniqueJobs;
                })
                ->map(function ($uniqueJobs) {
                    // Add dir_path column and its value to each record
                    $uniqueJobs->receiver_profile_pic_path = (!empty($uniqueJobs->receiver_profile_pic)) ? url(config('custom.user_folder') . $uniqueJobs->receiver_profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uniqueJobs;
                })
                ->toArray();

            if (count($uniqueJobs) > 0) {
                foreach ($uniqueJobs as $job) {
                    $param = array(
                        'is_seen' => 1
                    );
                    DB::table('job_chat_messages')->where('id', $job->id)->where('job_chat_id', $request->job_chat_id)->where('is_seen', 0)->update($param);
                }
            }

            $result = array('status' => true, 'message' => (count($uniqueJobs)) . " Record found", 'data' => $uniqueJobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get company details
    public function getCompanyDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'userID' => 'required',
            'role_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            # Check for drafted job
            $user_id = $request->user_id;
            $raw = DB::table('users as u')
                ->leftJoin('clients as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->select('u.*', 'ud.bio', 'ud.country_id', 'ud.state_id', 'ud.city_id', 'ud.address_line1', 'ud.address_line2', 'ud.primary_industry', 'ud.company_size', 'ud.website', 'ud.founded_in', 's.name as state_name', 's.code as state_code', 'city.city_name', 'ud.social_media_links', 'ud.company_name')
                ->where(['u.unique_id' => $request->userID])
                ->first();



            $raw->facebook_url = $raw->twitter_url = $raw->instagram_url = $raw->linkedin_url = "";


            $data = array();

            // Check if $raw exists
            if ($raw) {
                // Add dir_path column and its value to the record
                $raw->profile_pic_path = (!empty($raw->profile_pic)) ? url(config('custom.user_folder') . $raw->profile_pic) : '';


                $social_links = json_decode($raw->social_media_links, true);
                if (!empty($social_links)) {
                    foreach ($social_links as $k => $v) {
                        if ($v['platform'] == 'Facebook' && !empty($v['url']) && $v['url'] != null)
                            $raw->facebook_url = $v['url'];
                        else if ($v['platform'] == 'Twitter' && !empty($v['url']) && $v['url'] != null)
                            $raw->twitter_url = $v['url'];
                        else if ($v['platform'] == 'Instagram' && !empty($v['url']) && $v['url'] != null)
                            $raw->instagram_url = $v['url'];
                        else if ($v['platform'] == 'Linkedin' && !empty($v['url']) && $v['url'] != null)
                            $raw->linkedin_url = $v['url'];
                    }
                }

                /*
                $raw->total_jobs = DB::table('jobs as j')
                ->where('j.user_id', $raw->id)
                ->where('j.deleted_at', NULL)
                ->count();

    
                 $raw->total_candidates = DB::table('users as u')
                    ->where('u.created_by', $raw->id)
                    ->where('u.role_id', 4)
                    ->where('u.deleted_at', NULL)
                    ->count();

                $raw->total_employees = DB::table('users as u')
                    ->where('u.created_by', $raw->id)
                    ->where('u.role_id', 9)
                    ->where('u.deleted_at', NULL)
                    ->count();

                $raw->total_applicants = DB::table('users as u')
                    ->where('u.created_by', $raw->id)
                    ->where('u.role_id', 5)
                    ->where('u.deleted_at', NULL)
                    ->count();
                */

                $data['total_jobs'] = DB::table('jobs as j')
                    ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                    ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                    ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                    ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                    ->select(
                        'j.id',
                        'j.title',
                        'j.unique_id',
                        'j.total_opening',
                        'j.created_at',
                        'j.is_starred',
                        's.name as state_name',
                        's.code as state_code',
                        'city.city_name',
                        'p.profession',
                        'sp.specialty',
                        'j.status'
                    )
                    ->where('j.user_id', $raw->id)
                    ->where('j.status', 1)
                    ->where('j.deleted_at', NULL)
                    ->orderBy('j.id', 'desc')
                    ->get()->toArray();
            }



            $data['agency_details'] = $raw;



            if (!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $data);
            else
                $result = array('status' => false, 'message' => 'Invalid Agency ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}
