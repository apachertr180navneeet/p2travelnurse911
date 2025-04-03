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

            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

            $jobs = DB::table('jobs as j')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('users as u', 'j.user_id', '=', 'u.id')
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
                    'u.name',
                    'u.unique_id as user_unique_id',
                    'u.role_id as user_role_id',
                    /*
                    DB::raw('COUNT(CASE WHEN ja.status = 1 THEN 1 END) as applied_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 2 THEN 1 END) as shortlisted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 3 THEN 1 END) as submitted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 4 THEN 1 END) as interview_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 5 THEN 1 END) as offered_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 6 THEN 1 END) as hired_jobs')
                    */

                    DB::raw('COUNT(CASE WHEN ja.status = 1 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as applied_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 2 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as shortlisted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 3 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as submitted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 4 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as interview_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 5 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as offered_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 6 AND ja.deleted_at IS NULL AND ja.user_id IN (SELECT id FROM users WHERE deleted_at IS NULL) THEN 1 END) as hired_jobs')
                )


                ->where('j.deleted_at', NULL)

                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('job_boosted as jb1')
                        ->whereRaw('jb1.job_id = j.id');
                });

            $jobs->where(function ($query) use ($request) {
                $query->where('j.user_id', $request->user_id)
                    ->orWhere('j.status', '!=', 0);
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

            $jobs = $jobs->groupBy('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at',  's.name', 'city.city_name', 'p.profession', 'sp.specialty', 'j.status', 'u.name', 'u.unique_id')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($jobs)) . " Record found", 'data' => $jobs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get boostted jobs
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
                ->leftJoin('users as u', 'j.user_id', '=', 'u.id')
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
                    'u.name',
                    'u.unique_id as user_unique_id',
                    'u.role_id as user_role_id',
                    DB::raw('COUNT(CASE WHEN ja.status = 1 THEN 1 END) as applied_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 2 THEN 1 END) as shortlisted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 3 THEN 1 END) as submitted_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 4 THEN 1 END) as interview_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 5 THEN 1 END) as offered_jobs'),
                    DB::raw('COUNT(CASE WHEN ja.status = 6 THEN 1 END) as hired_jobs')
                )
                ->where('j.deleted_at', NULL)
                ->where('j.status', 1);

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

            $jobs = $jobs->groupBy('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at',  's.name', 'city.city_name', 'p.profession', 'sp.specialty', 'j.status', 'u.name', 'u.unique_id')
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

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
                    'c.id as company_id',
                    'c.company_name',
                    'c.primary_industry',
                    'c.company_size',
                    'c.bio',
                    'c.founded_in',
                    'et.title as employment_type_title',
                    DB::raw('CASE WHEN jb.id IS NOT NULL THEN 1 ELSE 0 END AS bookmark_exists'),
                    DB::raw('CASE WHEN ja.id IS NOT NULL THEN 1 ELSE 0 END AS job_applied')
                )
                ->where(['j.unique_id' => $request->jobId])
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

            if (!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $raw);
            else
                $result = array('status' => false, 'message' => 'Invalid job ID');
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
                    /*->where(['j.user_id' => $user_id])*/
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
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");


            $jobs = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('users as u2', 'j.user_id', '=', 'u2.id')
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
                    'u.role_id as user_role_id',
                    'u.unique_id as user_unique_id',
                    'u2.name as creator_name',
                    'u2.role_id as creator_role_id',
                    'u2.unique_id as creator_unique_id',
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
            $jobs = $jobs->orderBy('ja.id', 'desc')
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
                ->where('j.id', $request->job_id)
                /*->where('j.user_id', $request->user_id)*/
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
                /*->where('j.user_id', $request->user_id)*/
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.status', 1)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('j.user_id')
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
                /*->where('j.user_id', $request->user_id)*/
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.status', 2)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('j.user_id')
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
                /*->where('j.user_id', $request->user_id)*/
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.status', 3)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('j.user_id')
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
                /*->where('j.user_id', $request->user_id)*/
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.status', 4)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('j.user_id')
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
                /*->where('j.user_id', $request->user_id)*/
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.status', 5)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('j.user_id')
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
                /*->where('j.user_id', $request->user_id)*/
                ->where('j.id', $request->job_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.status', 6)
                ->whereNull('u.deleted_at')
                ->whereNull('ja.deleted_at')
                ->groupBy('j.user_id')
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($data)) . " Record found", 'data' => $data);
        } catch (Exception $e) {
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

    ## Function to insert/update user's job interview
    public function updateUserJobInterview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            /*'jobId' => 'required',*/
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

            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

            /*
            $jobs = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->join('job_interviews as ji', function ($join) {
                    $join->on('ji.job_id', '=', 'j.id')
                         ->on('ji.user_id', '=', 'ja.user_id')->where('ji.deleted_at', NULL);; 
                })
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('users as u', 'ja.user_id', '=', 'u.id')
                ->leftJoin('users as u2', 'ji.interviewer_id', '=', 'u2.id')
                ->select('ji.id as job_interview_id','j.id as job_id','u.id as user_id', 'j.title', 'j.unique_id', 'ja.created_at as applied_on','s.name as state_name', 's.code as state_code',  'city.city_name', 'ja.status', 'u.name','u.unique_id as user_unique_id','ji.interview_date','ji.interview_time','u2.name as interviewer_name','u2.id as interviewer_id')
                ->whereIn('ja.status', array(4))
                ->where('j.deleted_at', NULL);
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                $jobs->where('j.title', 'LIKE', "%{$request->keyword}%");
            }
            if (isset($request->status) && $request->status != 'all') {
                $jobs->where('ja.status', $request->status);
            } 
                $jobs = $jobs->groupBy('ja.user_id', 'ja.job_id')->orderBy('j.id', 'desc')
                ->get()->toArray();
            */


            $jobs = DB::table('job_interviews as ji')
                ->leftJoin('users as u', 'ji.user_id', '=', 'u.id')
                ->leftJoin('users as u2', 'ji.interviewer_id', '=', 'u2.id')
                ->leftJoin('users as u3', 'ji.created_by', '=', 'u3.id')
                ->select(
                    'ji.title',
                    'ji.id as job_interview_id',
                    'u.id as user_id',
                    'u.name',
                    'u.unique_id as user_unique_id',
                    'ji.interview_date',
                    'ji.interview_time',
                    'u2.name as interviewer_name',
                    'u2.id as interviewer_id',
                    'u3.name as creator_name',
                    'u3.unique_id as creator_unique_id',
                    'u3.role_id as creator_role_id'
                )
                ->where('ji.deleted_at', NULL);
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
            DB::table('jobs')->where('id', $request->job_id)->update($param);

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
            DB::table('jobs')->where('id', $request->id)->update($param);

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

    ## Clone the existing job
    public function cloneJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
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
            $newRecordData['user_id'] = $request->user_id;
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
