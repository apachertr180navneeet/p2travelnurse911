<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use App\Models\User as ModelsUser;
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

            // Get user data and its role
            $userID = [$request->user_id];
            $userData = ModelsUser::getUserDetailByID($request->user_id);
            if ($userData->role_id == ModelsUser::ROLE_OFFICE_ADMIN) {
                $userID[] = $userData->created_by;
            }

            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

            $jobs = DB::table('jobs as j')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('job_boosted as jb', 'j.id', '=', 'jb.job_id')
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
                    'j.status',
                    /*
                    DB::raw('COUNT(CASE WHEN ja.status = 1 THEN 1 END) as applied_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 2 THEN 1 END) as shortlisted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 3 THEN 1 END) as submitted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 4 THEN 1 END) as interview_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 5 THEN 1 END) as offered_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 6 THEN 1 END) as hired_jobs'),
                    */

                    DB::raw('COUNT(CASE WHEN ja.status = 1 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as applied_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 2 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as shortlisted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 3 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as submitted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 4 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as interview_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 5 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as offered_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 6 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as hired_jobs'),
                    DB::raw('MAX(CASE WHEN jb.id IS NOT NULL THEN 1 ELSE 0 END) as is_boosted'),
                    'jb.amount as boosted_amount',
                    'jb.start_date as boosted_start_date',
                    'jb.end_date as boosted_end_date'
                )
                ->whereIn('j.user_id', $userID)
                ->where('j.deleted_at', NULL)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('job_boosted as jb1')
                        ->whereRaw('jb1.job_id = j.id');
                });

            // Apply filters if present
            if (isset($request->state_id) && $request->state_id != 'all') {
                $jobs->where('j.state_id', $request->state_id);
            }
            if (isset($request->job_title) && !empty($request->job_title)) {
                $jobs->where('j.title', 'LIKE', "%{$request->job_title}%");
            }
            if (isset($request->date_posted) && !empty($request->date_posted)) {
                $jobs->where('j.created_at', $request->date_posted);
            }
            if (isset($request->starred_job) && !empty($request->starred_job)) {
                $jobs->where('j.is_starred', $request->starred_job);
            }

            if (isset($request->order_by) && $request->order_by == 'highest') {
                $jobs->orderByRaw('CAST(j.salary_start_range AS UNSIGNED) DESC');
            } else if (isset($request->order_by) && $request->order_by == 'lowest') {
                $jobs->orderByRaw('CAST(j.salary_start_range AS UNSIGNED) ASC');
            } else {
                $jobs->orderBy('j.id', 'desc');
            }

            $jobs = $jobs->groupBy('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.is_starred', 's.name', 'city.city_name', 'p.profession', 'sp.specialty', 'j.status')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($jobs)) . " Record found", 'data' => $jobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }



    ## Function to post job
    public function postJob(Request $request)
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

            if (isset($request->step) && $request->step == 'step1') {
                if ($request->shift_id == null)
                    $request->shift_id = 0;

                if ($request->total_opening == null)
                    $request->total_opening = 0;

                if ($request->specialty_id == null)
                    $request->specialty_id = 0;

                $param = [
                    'title' => $request->title,
                    'profession_id' => $request->profession_id,
                    'specialty_id' => $request->specialty_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'shift_id' => $request->shift_id,
                    'total_opening' => $request->total_opening,
                ];

                if (empty($request->id)) {
                    $param['unique_id'] = $request->unique_id;
                    $param['user_id'] = $request->user_id;
                    $param['progress'] = 1;
                    $param['status'] = 0;
                    $param['created_at'] = $this->entryDate;
                    $last_id = DB::table('jobs')->insertGetId($param);
                } else {
                    $param['updated_at'] = $this->entryDate;
                    DB::table('jobs')->where('id', $request->id)->update($param);
                    $last_id = $request->id;
                }
            } else if (isset($request->step) && $request->step == 'step2') {
                $param = [
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'salary_start_range' => $request->salary_start_range,
                    'salary_end_range' => $request->salary_end_range,
                    'salary_type' => $request->salary_type,
                    'show_pay_rate' => $request->show_pay_rate,
                    'employment_type_id' => $request->employment_type_id,
                    'min_work_per_week' => $request->min_work_per_week,
                    'progress' => 2,
                    'updated_at' => $this->entryDate,
                ];
                DB::table('jobs')->where('id', $request->id)->update($param);
                $last_id = $request->id;
            } else if (isset($request->step) && $request->step == 'step3') {
                $param = [
                    'description' => $request->description,
                    'qualification' => $request->qualification,
                    'responsibilities' => $request->responsibilities,
                    'progress' => 4,
                    'status' => 1,
                    'updated_at' => $this->entryDate,
                ];
                DB::table('jobs')->where('id', $request->id)->update($param);
                $last_id = $request->id;
            } else if (isset($request->step) && $request->step == 'step4') {
                $param = [
                    'progress' => 4,
                    'status' => 1,
                    'updated_at' => $this->entryDate,
                ];
                DB::table('jobs')->where('id', $request->id)->update($param);
                $last_id = $request->id;
            } else if (isset($request->step) && $request->step == 'step0') {
                if ($request->shift_id == null)
                    $request->shift_id = 0;

                if ($request->total_opening == null)
                    $request->total_opening = 0;

                if ($request->is_urgent == null)
                    $request->is_urgent = 0;

                if ($request->specialty_id == null)
                    $request->specialty_id = 0;

                $param = [
                    'title' => $request->title,
                    'profession_id' => $request->profession_id,
                    'specialty_id' => $request->specialty_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'shift_id' => $request->shift_id,
                    'total_opening' => $request->total_opening,
                    'is_urgent' => $request->is_urgent,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'salary_start_range' => $request->salary_start_range,
                    'salary_end_range' => $request->salary_end_range,
                    'salary_type' => $request->salary_type,
                    'show_pay_rate' => $request->show_pay_rate,
                    'employment_type_id' => $request->employment_type_id,
                    'min_work_per_week' => $request->min_work_per_week,
                    'updated_at' => $this->entryDate,
                    'description' => $request->description,
                    'qualification' => $request->qualification,
                    'responsibilities' => $request->responsibilities,
                    'progress' => 4,
                    'status' => 1
                ];
                if (empty($request->id)) {
                    $param['unique_id'] = $request->unique_id;
                    $param['user_id'] = $request->user_id;
                    $param['created_at'] = $this->entryDate;
                }
                $last_id = DB::table('jobs')->insertGetId($param);
            }

            DB::commit();
            $result = array('status' => true, 'message' => "Job has been successfully posted", 'data' => array('job_id' => $last_id));
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## Function to get draft job if exists
    public function getDraftJob(Request $request)
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

            # Check for drafted job
            $user_id = $request->user_id;

            if (isset($request->jobID) && !empty($request->jobID)) {
                $raw = DB::table('jobs as j')
                    ->select('j.*')
                    ->where(['j.user_id' => $user_id])
                    ->where('j.unique_id', $request->jobID)
                    ->where('j.deleted_at', NULL);
            } else {
                $raw = DB::table('jobs as j')
                    ->select('j.*')
                    ->where(['j.user_id' => $user_id])
                    ->where(['j.status' => 0])
                    ->where('j.progress', '<=', 4)
                    ->where('j.deleted_at', NULL);
                if (isset($request->job_id)) {
                    $raw->where('j.id', $request->job_id);
                }
            }
            $raw = $raw->first();

            if (!empty($raw)) {
                $raw->step = $request->step;
                $raw->token = "";
                /*
                if($raw->state_id == 0)
                    $raw->state_id = '';
                if($raw->city_id == 0)
                    $raw->city_id = '';
                    */
                $result = array('status' => true, 'message' => "Draft job found", 'data' => $raw);
            } else {
                $result = array('status' => false, 'message' => "Draft job not found");
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## FUnction to get job's attachments
    public function getJobAttatchments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $attachments = DB::table('job_attatchments as d')
                ->select('d.id', 'd.title', 'd.file_name', 'd.file_type', 'd.file_size')
                ->where('d.job_id', $request->job_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get()
                ->map(function ($attachments) {
                    // Add dir_path column and its value to each record
                    $attachments->dir_path = url(config('custom.job_attchment_folder') . $attachments->file_name);
                    return $attachments;
                })
                ->toArray();



            $result = array('status' => true, 'message' => "job attachment data found", 'data' => $attachments);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    public function generateAttachmentTitle()
    {
        $latestResume = DB::table('job_attatchments')
            ->select('title')
            ->where('title', 'like', 'Attatchment%')
            ->orderBy('title', 'desc')
            ->first();

        if ($latestResume) {
            // Extract the number from the title and increment it
            $latestNumber = intval(substr($latestResume->title, strlen('Attatchment ')));
            $nextNumber = $latestNumber + 1;
            $newTitle = 'Attatchment ' . $nextNumber;
        } else {
            // If no existing resumes found, start with 1
            $newTitle = 'Attatchment 1';
        }

        return $newTitle;
    }

    ## Function to upload job attachments
    public function uploadJobAttachments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
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
                $path = config('custom.job_attchment_folder');
                $upload = $file->move($path, $fileName);
                if ($upload) {
                    $param['file_name'] = $fileName;
                    $param['file_type'] = $ext;
                    $param['file_size'] = number_format($imageSize / 1048576, 2) . ' MB';
                }
            }
            $param['title'] = $this->generateAttachmentTitle();
            $param['user_id'] = $request->user_id;
            $param['job_id'] = $request->id;
            $param['created_at'] = $this->entryDate;
            DB::table('job_attatchments')->insert($param);
            $msg = "Attatchment has been been successfully uploaded";


            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get job applications
    public function getJobApplications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',

        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            // Get user data and its role
            $userID = [$request->user_id];
            $userData = ModelsUser::getUserDetailByID($request->user_id);
            if ($userData->role_id == ModelsUser::ROLE_OFFICE_ADMIN) {
                $userID[] = $userData->created_by;
            }
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");


            $jobs = DB::table('jobs as j')
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
                ->whereIn('j.user_id', $userID)
                ->whereNull('ja.deleted_at')
                ->whereNull('j.deleted_at')
                ->whereNull('u.deleted_at');

            // Apply filters if present
            if (isset($request->status) && $request->status != 'all') {
                $jobs->where('ja.status', $request->status);
            }
            if (isset($request->keyword) && !empty($request->keyword)) {
                $jobs->where('j.title', 'LIKE', "%{$request->keyword}%");
            }

            // Order and Group By
            $jobs = $jobs->orderBy('j.id', 'desc')
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
                ->toArray();

            if (count($jobs) > 0) {
                foreach ($jobs as $jobApp) {
                    $param = array(
                        'is_seen' => 1
                    );
                    DB::table('job_applications')->where('id', $jobApp->job_application_id)->where('job_id', $jobApp->job_id)->where('is_seen', 0)->update($param);
                }
            }

            $result = array('status' => true, 'message' => (count($jobs)) . " Record found", 'data' => $jobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get rejected job applications
    public function getRejectedJobApplications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            // Get user data and its role
            $userID = [$request->user_id];
            $userData = ModelsUser::getUserDetailByID($request->user_id);
            if ($userData->role_id == ModelsUser::ROLE_OFFICE_ADMIN) {
                $userID[] = $userData->created_by;
            }
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");


            $jobs = DB::table('jobs as j')
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
                    'ja.rejection_reason',
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
                ->whereIn('j.user_id', $userID)
                ->where('j.id', $request->job_id)
                ->where('ja.status', 7)
                ->whereNull('ja.deleted_at')
                ->whereNull('j.deleted_at')
                ->whereNull('u.deleted_at');

            // Apply filters if present
            if (isset($request->status) && $request->status != 'all') {
                $jobs->where('ja.status', $request->status);
            }
            if (isset($request->keyword) && !empty($request->keyword)) {
                $jobs->where('j.title', 'LIKE', "%{$request->keyword}%");
            }

            // Order and Group By
            $jobs = $jobs->orderBy('j.id', 'desc')
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
                ->toArray();

            if (count($jobs) > 0) {
                foreach ($jobs as $jobApp) {
                    $param = array(
                        'is_seen' => 1
                    );
                    DB::table('job_applications')->where('id', $jobApp->job_application_id)->where('job_id', $jobApp->job_id)->where('is_seen', 0)->update($param);
                }
            }

            $result = array('status' => true, 'message' => (count($jobs)) . " Record found", 'data' => $jobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get user job applications
    public function getUserJobApplications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required',

        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            // Get user data and its role
            $userID = [$request->user_id];
            $userData = ModelsUser::getUserDetailByID($request->user_id);
            if ($userData->role_id == ModelsUser::ROLE_OFFICE_ADMIN) {
                $userID[] = $userData->created_by;
            }

            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

            $data = array();
            $data['newApplications'] = DB::table('jobs as j')
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
                    'j.id as job_id',
                    'u.id as user_id',
                    'j.title',
                    'j.unique_id',
                    'ja.created_at',
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
                ->whereIn('j.user_id', $userID)
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.status', 1)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('ja.id')
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

            $data['shortlisted'] = DB::table('jobs as j')
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
                    'j.id as job_id',
                    'u.id as user_id',
                    'j.title',
                    'j.unique_id',
                    'ja.created_at',
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
                ->whereIn('j.user_id', $userID)
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.status', 2)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('ja.id')
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

            $data['submitted'] = DB::table('jobs as j')
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
                    'j.id as job_id',
                    'u.id as user_id',
                    'j.title',
                    'j.unique_id',
                    'ja.created_at',
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
                ->whereIn('j.user_id', $userID)
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.status', 3)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('ja.id')
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

            $data['interviews'] = DB::table('jobs as j')
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
                    'j.id as job_id',
                    'u.id as user_id',
                    'j.title',
                    'j.unique_id',
                    'ja.created_at',
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
                ->whereIn('j.user_id', $userID)
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->where('ja.status', 4)
                ->groupBy('ja.id')
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

            $data['offered'] = DB::table('jobs as j')
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
                    'j.id as job_id',
                    'u.id as user_id',
                    'j.title',
                    'j.unique_id',
                    'ja.created_at',
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
                ->whereIn('j.user_id', $userID)
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->whereNull('u.deleted_at')
                ->where('ja.status', 5)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('ja.id')
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

            $data['hired'] = DB::table('jobs as j')
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
                    'j.id as job_id',
                    'u.id as user_id',
                    'j.title',
                    'j.unique_id',
                    'ja.created_at',
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
                ->whereIn('j.user_id', $userID)
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->where('ja.status', 6)
                ->groupBy('ja.id')
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($data)) . " Record found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## FUnction to update user's job application status
    public function updateUserJobApplicationStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required',
            'emp_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            $param['updated_at'] = $this->entryDate;
            $param['status'] = $request->status;
            DB::table('job_applications')->where('job_id', $request->job_id)->where('user_id', $request->emp_id)->update($param);

            # insert candidate record in submission table if application status will be Submitted
            if ($request->status == 3) {
                $agencyUser = DB::table('jobs as j')
                    ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                    ->join('users as u', 'j.user_id', '=', 'u.id')
                    ->select('u.id', 'u.name', 'u.email', 'j.unique_id', 'j.title as job_title', 'ja.user_id')
                    ->where(['j.id' => $request->job_id])
                    ->first();

                $delParam['deleted_at'] = $this->entryDate;
                DB::table('submissions')->where('job_id', $request->job_id)->where('candidate_id', $agencyUser->user_id)->update($delParam);

                $args = array();
                $args['entry_date'] = date("Y-m-d");
                $args['job_id'] = $request->job_id;
                $args['candidate_id'] = $agencyUser->user_id;
                $args['agency_id'] = $agencyUser->id;
                $args['created_at'] = $this->entryDate;
                $args['created_by'] = $request->user_id;
                DB::table('submissions')->insert($args);
            }

            $msg = "Job application status has been been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to restore rejected job application to new application
    public function restoreJobApplication(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required',
            'job_application_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            $param['status'] = 1;
            $param['rejection_reason'] = '';
            $param['updated_at'] = $this->entryDate;
            DB::table('job_applications')->where('id', $request->job_application_id)->update($param);
            $msg = "Job Application has been restored successfully";
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## Function to rejected job application 
    public function rejectJobApplication(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'jobId' => 'required',
            'jobAppId' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            $param['status'] = 7;
            $param['rejection_reason'] = $request->rejectionReason;
            $param['updated_at'] = $this->entryDate;
            DB::table('job_applications')->where('id', $request->jobAppId)->update($param);
            $msg = "Job Application has been rejected successfully";
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to insert/update user's job interview
    public function updateUserJobInterview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            //'jobId' => 'required',
            'empId' => 'required',
            'interviewerId' => 'required',
            'interviewDate' => 'required',
            'interviewTime' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            $param['title'] = $request->title;
            $param['job_id'] = ($request->jobId) ? $request->jobId : 0;
            $param['user_id'] = $request->empId;
            $param['interview_date'] = $request->interviewDate;
            $param['interview_time'] = $request->interviewTime;
            $param['interviewer_id'] = $request->interviewerId;

            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('job_interviews')->where('id', $request->id)->update($param);
                $msg = "Follow Up has been been successfully updated";

                $interview_id = $request->id;
                ## Update Entry in Events Tables with task type
                $param = array(
                    'title' => $request->title,
                    'start_date' => $request->interviewDate,
                    'end_date' => $request->interviewDate,
                    'start_time' => $request->interviewTime,
                    'end_time' => $request->interviewTime,
                    'assignee_id' => $request->empId,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id
                );
                DB::table('events')->where('type', 'interview')->where('type_id', $interview_id)->update($param);
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $interview_id = DB::table('job_interviews')->insertGetId($param);
                $msg = "Follow Up has been been successfully scheduled";

                ## Make Entry in Events Tables with task type
                $param = array(
                    'title' => $request->title,
                    'type' => 'interview',
                    'type_id' => $interview_id,
                    'start_date' => $request->interviewDate,
                    'end_date' => $request->interviewDate,
                    'start_time' => $request->interviewTime,
                    'end_time' => $request->interviewTime,
                    'assignee_id' => $request->empId,
                    'created_at' => $this->entryDate,
                    'created_by' => $request->user_id
                );
                DB::table('events')->insert($param);
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## Function to get all job inteviews
    public function getJobInterviews(Request $request)
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
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            $jobs = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->join('job_interviews as ji', function ($join) {
                    $join->on('ji.job_id', '=', 'j.id')
                         ->on('ji.user_id', '=', 'ja.user_id')->where('ji.deleted_at', NULL);
                })
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('users as u', 'ja.user_id', '=', 'u.id')
                ->leftJoin('users as u2', 'ji.interviewer_id', '=', 'u2.id')
                ->select('ji.id as job_interview_id','j.id as job_id','u.id as user_id', 'j.title', 'j.unique_id', 'ja.created_at as applied_on','s.name as state_name', 's.code as state_code',  'city.city_name', 'ja.status', 'u.name','u.unique_id as user_unique_id','ji.interview_date','ji.interview_time','u2.name as interviewer_name','u2.id as interviewer_id')
                ->whereIn('ja.status', array(4)) // ->whereIn('ja.status', array(1, 2, 3)) 
                ->where('j.user_id', $request->user_id)
                ->where('j.deleted_at', NULL)
                ->whereNull('u.deleted_at');
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                $jobs->where('j.title', 'LIKE', "%{$request->keyword}%");
            } 
                $jobs = $jobs->groupBy('ja.user_id', 'ja.job_id')->orderBy('j.id', 'desc')
                ->get()->toArray();
            */

            // Get user data and its role
            $userID = [$request->user_id];
            $userData = ModelsUser::getUserDetailByID($request->user_id);
            if ($userData->role_id == ModelsUser::ROLE_OFFICE_ADMIN) {
                $userID[] = $userData->created_by;
            }
            $jobs = DB::table('job_interviews as ji')
                ->leftJoin('users as u', 'ji.user_id', '=', 'u.id')
                ->leftJoin('users as u2', 'ji.interviewer_id', '=', 'u2.id')
                ->select(
                    'ji.title',
                    'ji.id as job_interview_id',
                    'u.id as user_id',
                    'u.name',
                    'u.unique_id as user_unique_id',
                    'ji.interview_date',
                    'ji.interview_time',
                    'u2.name as interviewer_name',
                    'u2.id as interviewer_id'
                )
                ->where('ji.deleted_at', NULL)
                ->whereIn('ji.created_by', $userID);
            /*
            
            if (isset($request->status) && $request->status != 'all') {
                $jobs->where('ja.status', $request->status);
            }
            */

            if (isset($request->keyword) && !empty($request->keyword)) {
                $jobs->where(function ($query) use ($request) {
                    $query->where('u.name', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('u2.name', 'LIKE', "%{$request->keyword}%");
                });
            }

            $jobs = $jobs->orderBy('ji.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($jobs)) . " Record found", 'data' => $jobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get job employees
    public function getJobEmployees(Request $request)
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
            $jobs = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('users as u', 'ja.user_id', '=', 'u.id')
                ->select('u.id as user_id', 'u.name', 'u.unique_id as user_unique_id')
                ->whereIn('ja.status', array(1, 2, 3))
                ->where('j.id', $request->job_id)
                ->where('j.user_id', $request->user_id)
                ->where('j.deleted_at', NULL)
                ->whereNull('u.deleted_at');
            /*
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('job_applications')
                          ->whereRaw('job_applications.user_id = u.id')
                          ->whereRaw('job_applications.job_id = j.id');
                });
                */

            /* 
                ->whereNotIn('ja.user_id', function ($query) {
                    $query->select('user_id')->from('job_applications');
                }) 
                */
            if (isset($request->status) && $request->status != 'all') {
                $jobs->where('ja.status', $request->status);
            }
            $jobs = $jobs->orderBy('j.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($jobs)) . " Record found", 'data' => $jobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to delete job interview
    public function deleteJobInterview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_interview_id' => 'required',
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
            DB::table('job_interviews')->where('id', $request->job_interview_id)->update($param);
            $msg = "Follow Up has been been successfully deleted";
            
            $param = array(
                'deleted_at' => $this->entryDate,
            );
            DB::table('events')->where('type', 'interview')->where('type_id', $request->job_interview_id)->update($param);

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ##Function to delete job application
    public function deleteJobApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_application_id' => 'required',
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
            DB::table('job_applications')->where('id', $request->job_application_id)->update($param);
            $msg = "Job Application has been been successfully deleted";


            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to delete job
    public function deleteJob(Request $request)
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
            DB::table('jobs')->where('id', $request->id)->update($param);
            $msg = "Job has been been successfully deleted";


            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to toggle starred job
    public function toggleStarredJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required',
            'status' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

            $param['is_starred'] = ($request->status == 1) ? 0 : 1;
            $param['updated_at'] = $this->entryDate;
            $param['updated_by'] = $request->user_id;
            DB::table('jobs')->where('id', $request->job_id)->where('user_id', $request->user_id)->update($param);

            if ($request->status == 1)
                $msg = "Job has been been successfully removed as starred";
            else
                $msg = "Job has been been successfully mark as starred";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to insert job message
    public function insertJobMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required_without:job_request_id', // job_id is required if job_request_id is null
            'job_request_id' => 'required_without:job_id', // job_request_id is required if job_id is null
            'message' => 'required',
            'from_id' => 'required',
            'to_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }
        
        /*
        $disableMessageNotif = DB::table('user_details')->select('id')->where('user_id', $request->to_id)->where('enable_message_notification' ,1)->first();
        if(!$disableMessageNotif)
        {
            $result = array('status' => false, 'message' => "The user has disabled message notifications.");
            return response()->json($result);
        }
        */

        try {
            DB::beginTransaction();

            $where = [];
            if($request->job_id) {
                $where[] = ['jc.job_id', $request->job_id];
                $jobRecord = DB::table('jobs as j')
                    ->select('j.unique_id', 'j.title as job_title')
                    ->where(['j.id' => $request->job_id])
                    ->first();
            } elseif($request->job_request_id) {
                $where[] = ['jc.job_request_id', $request->job_request_id];
                $jobRecord = DB::table('job_requests as j')
                    ->where(['j.id' => $request->job_request_id])
                    ->first();
            }
            $agencyUser = DB::table('users as u')
                ->select('u.id', 'u.name', 'u.email')
                ->where(['u.id' => $request->to_id])
                ->first();                
            
            $jobChatResult = DB::table('job_chats as jc')
                ->select('jc.id')
                ->where($where)
                ->where(['jc.job_seeker_id' => $agencyUser->id,'employer_id' => $request->client_id])
                ->first();
            if(empty($jobChatResult)) {
                $chatParam = array(
                    'job_seeker_id' => $agencyUser->id,
                    'employer_id' => $request->client_id,
                    'created_by' => $request->user_id,
                    'created_at' => $this->entryDate,
                );
                if (!empty($request->job_request_id)) {
                    $chatParam['job_request_id'] = $request->job_request_id;
                } else if (!empty($request->job_id)) {
                    $chatParam['job_id'] = $request->job_id;
                }
                $job_chat_id = DB::table('job_chats')->insertGetId($chatParam);
            }
            else {
                $job_chat_id = $jobChatResult->id;
            }
            
            $param = array(
                'job_chat_id' => $job_chat_id,
                'to_id' => $agencyUser->id,
                'from_id' => $request->from_id,
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
                    ->select('u.name', 'u.email')
                    ->where(['u.id' => $request->to_id])
                    ->first();


                if (!empty($request->job_request_id)) {
                    $param = array(
                        'receiver_name' => $user->name,
                        'receiver_email' => $user->email,
                        'sender_name' => $agencyUser->name,
                        'message' => $request->message,
                        'dashboard_path' => config('custom.login_url')
                    );
                    Mail::send('emails.client.job-request-message', $param, function ($message) use ($param) {
                        $message->subject($param['sender_name'] . ' ' . config('custom.job_message'));
                        $message->to($param['receiver_email']);
                    });
                } else {
                    $param = array(
                        'job_title' => $jobRecord->job_title,
                        'job_unique_id' => $jobRecord->unique_id,
                        'receiver_name' => $user->name,
                        'receiver_email' => $user->email,
                        'sender_name' => $agencyUser->name,
                        'message' => $request->message,
                        'dashboard_path' => config('custom.login_url')
                    );

                    Mail::send('emails.client.job-message', $param, function ($message) use ($param) {
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

    ## FUnction to get unique job message
    public function getUniqueJobMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'client_id' => 'required'
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
                ->leftJoin('users as u', 'u.id', '=', 'jm.from_id')
                ->leftJoin(DB::raw("(SELECT MAX(id) as latest_id FROM job_messages WHERE deleted_at IS NULL GROUP BY job_id) as latest"), 'jm.id', '=', 'latest.latest_id')
                ->select(
                    'jm.from_id as user_id',
                    'j.id as job_id',
                    'j.title as job_title',
                    'u.name',
                    'u.unique_id',
                    'u.profile_pic',
                    DB::raw('(SELECT jm1.message FROM job_messages jm1 WHERE jm1.job_id = jm.job_id  AND jm1.deleted_at IS NULL AND (jm1.from_id = "' . $user_id . '" OR jm1.to_id = "' . $user_id . '") ORDER BY jm1.id DESC LIMIT 1) as message'), // AND jm1.from_id = jm.from_id 
                    DB::raw('(SELECT jm2.created_at FROM job_messages jm2 WHERE jm2.job_id = jm.job_id  AND jm2.deleted_at IS NULL AND (jm2.from_id = "' . $user_id . '" OR jm2.to_id = "' . $user_id . '") ORDER BY jm2.id DESC LIMIT 1) as created_at') // AND jm2.from_id = jm.from_id 
                )
                ->where(function ($query) use ($user_id) {
                    $query->where('jm.from_id', $user_id)
                        ->orWhere('jm.to_id', $user_id);
                })
                ->where('j.user_id', $user_id)
                ->where('jm.from_id', '!=', $user_id)
                ->whereNull('jm.deleted_at');
            
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                
                $uniqueJobs->where(function ($query) use ($request) {
                    $query->where('j.title', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('message', 'LIKE', "%{$request->keyword}%");
                });
            }

            $uniqueJobs = $uniqueJobs->orderBy('created_at', 'desc')
                ->groupBy('jm.job_id', 'jm.from_id', 'jm.to_id')
                ->get()
                ->map(function ($uniqueJobs) {
                    // Add dir_path column and its value to each record
                    $uniqueJobs->profile_pic_path = (!empty($uniqueJobs->profile_pic)) ? url(config('custom.user_folder') . $uniqueJobs->profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uniqueJobs;
                })
                ->toArray();
            */
            
            $user_id = $request->user_id;
            $client_id = $request->client_id;

            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            //DB::enableQueryLog();
            
            /*
            $uniqueJobs = DB::table('job_messages as jm')
                ->join('jobs as j', 'j.id', '=', 'jm.job_id')
                ->join('users as u', 'u.id', '=', 'jm.from_id')
                
                ->join(DB::raw("(SELECT MAX(id) as latest_id, job_id, from_id, to_id 
                                     FROM job_messages 
                                     WHERE deleted_at IS NULL and (from_id = ".$user_id." OR to_id = ".$user_id." )
                                     GROUP BY from_id,job_id
                                     order by id DESC
                                     ) as latest"), 
                    function ($join) {
                        $join->on('jm.job_id', '=', 'latest.job_id')
                            ->on('jm.id','=','latest.latest_id');
                        
                            //->on('jm.from_id', '=', 'latest.from_id')
                            //->on('jm.to_id', '=', 'latest.to_id')
                            ;
                        
                    }
                    
                    )
                
                //->leftJoin('job_messages as jm_latest', 'jm_latest.id', '=', 'latest.latest_id') // Joining with latest message
                ->select(
                    'jm.from_id as user_id',
                    'j.id as job_id',
                    'j.title as job_title',
                    'u.name',
                    'u.unique_id',
                    'u.profile_pic',
                    //'jm.message',
                    //'jm.created_at'
                    
                    DB::raw('(SELECT jm1.message 
                              FROM job_messages jm1 
                              WHERE jm1.job_id = jm.job_id  
                              AND jm1.deleted_at IS NULL 
                              AND ((jm1.from_id = jm.from_id and jm1.to_id = jm.to_id) OR (jm1.from_id = jm.to_id and jm1.from_id = jm.to_id)) 
                              ORDER BY jm1.id DESC 
                              LIMIT 1) as message'),
                    DB::raw('(SELECT jm2.created_at 
                              FROM job_messages jm2 
                              WHERE jm2.job_id = jm.job_id  
                              AND jm2.deleted_at IS NULL 
                              AND ((jm2.from_id = jm.from_id and jm2.to_id = jm.to_id) OR (jm2.from_id = jm.to_id and jm2.from_id = jm.to_id))
                              ORDER BY jm2.id DESC 
                              LIMIT 1) as created_at')
                    
                              
                )
                
                ->where(function ($query) use ($user_id) {
                    $query->where('jm.from_id', $user_id)
                        ->orWhere('jm.to_id', $user_id);
                })
                
                ->where('j.user_id', $user_id)
                ->where('jm.from_id', '!=', $user_id)  // Ensuring we exclude the current user in the "from"
                ->whereNull('jm.deleted_at');
            
            // Apply filters if present
            if (isset($request->keyword) && !empty($request->keyword)) {
                $uniqueJobs->where(function ($query) use ($request) {
                    $query->where('j.title', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                        ->orWhere('message', 'LIKE', "%{$request->keyword}%");
                });
            }
            
            $uniqueJobs = $uniqueJobs->orderBy('created_at', 'desc')
                ->groupBy('jm.job_id', 'jm.from_id', 'jm.to_id') // Group by both job_id and user-specific columns
                ->get()
                ->map(function ($uniqueJobs) {
                    // Add dir_path column and its value to each record
                    $uniqueJobs->profile_pic_path = (!empty($uniqueJobs->profile_pic)) ? url(config('custom.user_folder') . $uniqueJobs->profile_pic) : ''; 
                    return $uniqueJobs;
                })
                ->toArray();
            */
            
            //dd(DB::getQueryLog());

            $uniqueJobs = DB::table('job_chats as jc')
                ->join('job_chat_messages as jm', 'jc.id', '=', 'jm.job_chat_id')
                ->leftJoin('jobs as j', 'j.id', '=', 'jc.job_id') // Left join to handle null job_id
                ->leftJoin('job_requests as jr', 'jr.id', '=', 'jc.job_request_id')
                ->join('users as u', 'u.id', '=', 'jc.job_seeker_id')
                ->select(
                    'u.id as user_id',
                    'jc.id as job_chat_id',
                    'j.id as job_id',
                    'jr.id as job_request_id',
                    DB::raw('IF(jc.job_id IS NOT NULL, j.title, "Job Request") as job_title'),
                    'u.name',
                    'u.unique_id',
                    'u.profile_pic',
                    //'jm.message', // Using latest message
                    //'jm.created_at' // Using the latest message time
                    DB::raw('(SELECT jm1.message FROM job_chat_messages jm1 WHERE jm1.job_chat_id = jc.id ORDER BY jm1.id DESC LIMIT 1) as message'),
                    DB::raw('(SELECT jm2.created_at FROM job_chat_messages jm2 WHERE jm2.job_chat_id = jc.id ORDER BY jm2.id DESC LIMIT 1) as created_at')
                )
                ->where(function ($query) use ($client_id) {
                    $query->where('jc.employer_id', $client_id);
                })
                ->where(function ($query) {
                    $query->whereNotNull('jc.job_id')
                        ->orWhereNotNull('jc.job_request_id'); // Ensure at least one ID exists
                })
                /*->whereNull('jm.deleted_at')*/
                ->where(function ($query) {
                    $query->where('j.status', 1)
                        ->orWhere('jr.status', 1); // Ensure at least one ID exists
                });        
                //->where('j.user_id',$user_id);
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                $uniqueJobs->where(function ($query) use ($request) {
                    $query->where('j.title', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('jm.message', 'LIKE', "%{$request->keyword}%");
                });
            }
            
            $uniqueJobs = $uniqueJobs->orderBy('created_at', 'desc') // Sorting by last message time
                ->groupBy('jc.id')
                ->get()
                ->map(function ($uniqueJobs) {
                    $uniqueJobs->profile_pic_path = (!empty($uniqueJobs->profile_pic)) 
                        ? url(config('custom.user_folder') . $uniqueJobs->profile_pic) 
                        : '';
                    return $uniqueJobs;
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
            'userID' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {

            $user_id = $request->user_id;
            $userID = $request->userID;

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
                ->where(function ($query) use ($userID, $user_id) {
                    $query->where('jm.from_id', $userID)
                        ->orWhere('jm.to_id', $userID)
                        ->where('jm.from_id', $userID)
                        ->orWhere('jm.to_id', $userID);
                })
                ->where('j.id', $request->job_id)
                ->where('j.user_id', $user_id)
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

    ## Function to insert/update boosted jobs
    public function updateBoostedJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required',
            'amount' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $row = DB::table('job_boosted as bj')
                ->select('bj.id')
                ->where('bj.job_id', $request->job_id)
                ->where('bj.deleted_at', NULL)
                ->first();

            if (!empty($row)) {
                $param = array();
                $param['job_id'] = $request->job_id;
                $param['amount'] = $request->amount;
                $param['start_date'] = $request->startDate;
                $param['end_date'] = $request->endDate;
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('job_boosted')->where('id', $row->id)->update($param);
                $msg = "Boosted Job has been been successfully updated";
            } else {
                $param = array();
                $param['job_id'] = $request->job_id;
                $param['amount'] = $request->amount;
                $param['start_date'] = $request->startDate;
                $param['end_date'] = $request->endDate;
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $interview_id = DB::table('job_boosted')->insertGetId($param);
                $msg = "Boosted Job has been been successfully scheduled";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## Function to get boosted jobs
    public function getBoostedJobs(Request $request)
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

            $jobs = DB::table('jobs as j')
                ->join('job_boosted as jb', 'j.id', '=', 'jb.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('job_applications as ja', 'j.id', '=', 'ja.job_id')
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
                    'j.status',
                    DB::raw('COUNT(CASE WHEN ja.status = 1 THEN 1 END) as applied_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 2 THEN 1 END) as shortlisted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 3 THEN 1 END) as submitted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 4 THEN 1 END) as interview_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 5 THEN 1 END) as offered_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 6 THEN 1 END) as hired_jobs'),
                    'jb.amount as boosted_amount',
                    'jb.start_date as boosted_start_date',
                    'jb.end_date as boosted_end_date'
                )
                ->where('j.user_id', $request->user_id)
                ->where('j.deleted_at', NULL);

            // Apply filters if present
            if (isset($request->state_id) && $request->state_id != 'all') {
                $jobs->where('j.state_id', $request->state_id);
            }
            if (isset($request->job_title) && !empty($request->job_title)) {
                $jobs->where('j.title', 'LIKE', "%{$request->job_title}%");
            }
            if (isset($request->date_posted) && !empty($request->date_posted)) {
                $jobs->where('j.created_at', $request->date_posted);
            }
            if (isset($request->starred_job) && !empty($request->starred_job)) {
                $jobs->where('j.is_starred', $request->starred_job);
            }

            $jobs = $jobs->groupBy('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.is_starred', 's.name', 'city.city_name', 'p.profession', 'sp.specialty', 'j.status')
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($jobs)) . " Record found", 'data' => $jobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update job status
    public function updateJobStatus(Request $request)
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
            DB::table('jobs')->where('id', $request->id)->where('user_id', $request->user_id)->update($param);

            $msg = "Job status has been been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## Function to perform bulk actions
    public function jobBulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_ids' => 'required',
            'user_id' => 'required',
            'bulk_action' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {

            if ($request->bulk_action == 'delete' && !empty($request->job_ids)) {
                DB::beginTransaction();
                foreach ($request->job_ids as $k => $v) {
                    $param = array();
                    $param['deleted_at'] = $this->entryDate;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('jobs')->where('id', $v)->update($param);
                }

                $msg = count($request->job_ids) . " job(s) has been successfully deleted";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else if ($request->bulk_action == 'change-status-active' && !empty($request->job_ids)) {
                DB::beginTransaction();
                foreach ($request->job_ids as $k => $v) {
                    $param = array();
                    $param['status'] = 1;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('jobs')->where('id', $v)->where('status', '!=', 0)->update($param);
                }

                $msg = count($request->job_ids) . " job(s) status has been successfully updated";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else if ($request->bulk_action == 'change-status-inactive' && !empty($request->job_ids)) {
                DB::beginTransaction();
                foreach ($request->job_ids as $k => $v) {
                    $param = array();
                    $param['status'] = 2;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('jobs')->where('id', $v)->where('status', '!=', 0)->update($param);
                }

                $msg = count($request->job_ids) . " job(s) has been successfully updated";


                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else {
                $result = array('status' => false, 'message' => 'Unknown error occured');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## Function to perform bulk actions
    public function jobApplicationBulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_ids' => 'required',
            'user_id' => 'required',
            'bulk_action' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {

            if ($request->bulk_action == 'delete' && !empty($request->job_ids)) {
                DB::beginTransaction();
                foreach ($request->job_ids as $k => $v) {
                    $param = array();
                    $param['deleted_at'] = $this->entryDate;
                    $param['updated_at'] = $this->entryDate;
                    DB::table('job_applications')->where('id', $v)->update($param);
                }

                $msg = count($request->job_ids) . " job application(s) has been successfully deleted";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else {
                $result = array('status' => false, 'message' => 'Unknown error occured');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
        public function jobApplicationBulkActionsWithAdded(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|array',  // Expecting an array of user IDs
            'bulk_action' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error occurred in API",
                'errors' => $validator->errors(),  // Include validation errors for more clarity
                'data' => $request->all(),
            ]);
        }

        try {
            DB::beginTransaction(); // Start transaction

            // Loop through the user IDs provided in the request
            foreach ($request->user_id as $user_id) {
                // Fetch the existing user data based on user_id
                $existingUser = DB::table('users')->where('id', $user_id)->first();

                if ($existingUser) {
                    // Determine the role_id based on the bulk action
                    $role_id = null;

                    if ($request->bulk_action === 'convert-to-candidate') {
                        // Fetch role_id for candidate
                        $role = DB::table('user_roles')->where('role', 'candidate')->first();
                        if ($role) {
                            $role_id = $role->id; // Set role_id to the id of the candidate role
                        }
                    } elseif ($request->bulk_action === 'convert-to-employee') {
                        // Fetch role_id for employee
                        $role = DB::table('user_roles')->where('role', 'employee')->first();
                        if ($role) {
                            $role_id = $role->id; // Set role_id to the id of the employee role
                        }
                    }

                    // Only proceed if a valid role_id is set
                    if ($role_id) {
                        // Prepare user data to be added
                        $param = [
                            'name' => $existingUser->name,  // Reusing existing user name
                            'email' => $existingUser->email, // Reusing existing user email
                            'phone' => $existingUser->phone, // Reusing existing user phone
                            'unique_id' => $this->generateUniqueCode(8),
                            'password' => null,  // Password is not set
                            'role_id' => $role_id, // Set the determined role_id
                            'created_by' => $request->create_by, // Using the current user_id as the creator
                            'is_login_allowed' => 0,
                            'status' => 1,
                            'created_at' => now(),
                        ];

                        // Insert user data into 'users' table
                        $newUserId = DB::table('users')->insertGetId($param);

                        // Fetch existing user details from user_details table
                        $existingUserDetails = DB::table('user_details')->where('user_id', $user_id)->first();

                        if ($existingUserDetails) {
                            // Prepare user details, reusing data if available
                            $userDetails = [
                                'user_id' => $newUserId,
                                'bio' => $existingUserDetails->bio ?? '',
                                'country_id' => $existingUserDetails->country_id ?? $request->country_id,
                                'state_id' => $existingUserDetails->state_id ?? $request->state_id,
                                'city_id' => $existingUserDetails->city_id ?? $request->city_id,
                                'address_line1' => $existingUserDetails->address_line1 ?? $request->address_line1 ?? '',
                                'address_line2' => $existingUserDetails->address_line2 ?? $request->address_line2 ?? '',
                                'dob' => $existingUserDetails->dob ?? null,
                                'total_experience' => $existingUserDetails->total_experience ?? '',
                                'created_at' => now(),
                            ];

                            // Insert user details into 'user_details' table
                            DB::table('user_details')->insert($userDetails);
                        }

                        // Fetch existing user work histories from user_work_histories table
                        $existingUserWorkHistories = DB::table('user_work_histories')->where('user_id', $user_id)->get();

                        // Loop through each work history and add it to the new user ID
                        foreach ($existingUserWorkHistories as $workHistory) {
                            $userWorkHistory = [
                                'user_id' => $newUserId,  // Use new user ID
                                // 'token' => $workHistory->token,
                                'title' => $workHistory->title,
                                'company_name' => $workHistory->company_name,
                                'start_month' => $workHistory->start_month,
                                'start_year' => $workHistory->start_year,
                                'end_month' => $workHistory->end_month,
                                'end_year' => $workHistory->end_year,
                                'currently_working' => $workHistory->currently_working,
                                'city_id' => $workHistory->city_id,
                                'state_id' => $workHistory->state_id,
                                'profession_id' => $workHistory->profession_id,
                                'specialty_id' => $workHistory->specialty_id,
                                'employment_type_id' => $workHistory->employment_type_id,
                                'created_at' => now(),
                            ];

                            // Insert user work history into 'user_work_histories' table
                            DB::table('user_work_histories')->insert($userWorkHistory);
                        }

                        // Fetch existing user educations from user_educations table
                        $existingUserEducations = DB::table('user_educations')->where('user_id', $user_id)->get();

                        // Loop through each education history and add it to the new user ID
                        foreach ($existingUserEducations as $education) {
                            $userEducation = [
                                'user_id' => $newUserId,  // Use new user ID
                                // 'token' => $education->token,
                                'degree' => $education->degree,
                                'school_college' => $education->school_college,
                                'end_month' => $education->end_month,
                                'end_year' => $education->end_year,
                                'currently_attending' => $education->currently_attending,
                                'location' => $education->location,
                                'created_at' => now(),
                            ];

                            // Insert user education into 'user_educations' table
                            DB::table('user_educations')->insert($userEducation);
                        }

                        // Fetch existing user skills from user_skills table
                        $existingUserSkills = DB::table('user_skills')->where('user_id', $user_id)->get();

                        // Loop through each skill and add it to the new user ID
                        foreach ($existingUserSkills as $skill) {
                            $userSkill = [
                                'user_id' => $newUserId,  // Use new user ID
                                // 'token' => $skill->token,
                                'skill_id' => $skill->skill_id,
                                'skill' => $skill->skill,
                                'experience' => $skill->experience,
                                'experience_type' => $skill->experience_type,
                                'created_at' => now(),
                            ];

                            // Insert user skill into 'user_skills' table
                            DB::table('user_skills')->insert($userSkill);
                        }
                    }
                }
            }

            DB::commit(); // Commit transaction
            return response()->json([
                'status' => true,
                'message' => "Users have been successfully converted.",
            ]);
        } catch (\Throwable $th) {
            DB::rollback(); // Rollback transaction on error
            return response()->json([
                'status' => false,
                'message' => "An error occurred: " . $th->getMessage(),
            ]);
        }
    }

    public function generateUniqueCode(int $codeLength)
    {
        $characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);

        $code = '';

        while (strlen($code) < $codeLength) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code . $character;
        }

        if (User::where('unique_id', $code)->exists()) {
            $this->generateUniqueCode($codeLength);
        }

        return $code;
    }
    
    ## Clone the existing job
    public function cloneJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API");
            return response()->json($result);
        }
        // Find the job post by jobID
        $originalRecord = DB::table('jobs')->where('id', $request->job_id)->first();
        // If the record is not found, return an error
        if (!$originalRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }

        try {
            DB::beginTransaction();
            // Create clone and duplicate of job post
            $newRecordData = (array) $originalRecord;
            unset($newRecordData['id']);
            $newRecordData['title'] = $originalRecord->title . ' (clone)';
            $newRecordData['status'] = 0; // draft status
            $newRecordData['created_at'] = now();
            $newRecordData['updated_at'] = now();
            $newRecordId = DB::table('jobs')->insertGetId($newRecordData);
            DB::commit();

            $msg = "Job clone has been created successfully with ID: " . $newRecordId;
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}
