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
            $jobs = DB::table('jobs as j')
                ->leftJoin('users as u', 'j.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
                ->leftJoin('job_bookmarks as jb', function ($join) use ($user_id){
                    $join->on('j.id', '=', 'jb.job_id')
                         ->where('jb.user_id', '=', $user_id); 
                })
                ->leftJoin('job_boosted as jb2', function ($join) use ($currentDate) {
                    $join->on('j.id', '=', 'jb2.job_id')
                         ->where('jb2.start_date', '<=', $currentDate)
                         ->where('jb2.end_date', '>=', $currentDate);
                })
                ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at','j.salary_start_range','j.salary_end_range','s.name as state_name',  'city.city_name', 
                'p.profession', 'sp.specialty', 'sf.title as shift_title','u.profile_pic',
                DB::raw('CASE WHEN jb.id IS NOT NULL THEN 1 ELSE 0 END AS bookmark_exists'),
                DB::raw('CASE WHEN jb2.id IS NOT NULL THEN 1 ELSE 0 END AS is_boosted')
                
                )
                /*->where('j.user_id', $request->user_id)*/
                ->where('j.deleted_at', NULL)
                ->where('j.status',1)
                ;
                
            // Apply filters if present
            if (isset($request->job_title) && !empty($request->job_title)) {
                $jobs->where('j.title', 'LIKE', "%{$request->job_title}%");
            }
            if (isset($request->state_id) && $request->state_id != '0') {
                $jobs->where('j.state_id', $request->state_id);
            }
            
            if (isset($request->no_of_opening) && !empty($request->no_of_opening)) {
                if($request->no_of_opening == '3+')
                    $jobs->where('j.total_opening','>=', $request->no_of_opening);
                else
                    $jobs->where('j.total_opening', $request->no_of_opening);
            }
            
            if (isset($request->profession_id) && !empty($request->profession_id) && $request->profession_id == "0") {
                $jobs->where('j.profession_id', $request->profession_id);
            }
            
            if (isset($request->specialty_id) && !empty($request->specialty_id) && $request->specialty_id == "0") {
                $jobs->where('j.specialty_id', $request->specialty_id);
            }
            
            if (isset($request->job_type) && !empty($request->job_type) && $request->job_type == "bookmarked") {
                $jobs->whereExists(function ($query) use ($user_id) {
                    $query->select(DB::raw(1))
                          ->from('job_bookmarks as jb1')
                          ->whereRaw('jb1.job_id = j.id')
                          ->where('jb1.user_id', $user_id); 
                });
            }
            
            $jobs = $jobs->orderBy('is_boosted', 'desc')
            ->orderBy('jb2.id', 'desc')
            ->orderBy('j.id', 'desc')
            ->get()
            ->map(function ($users) {
                // Add dir_path column and its value to each record
                $users->profile_pic_path = (!empty($users->profile_pic))?url(config('custom.user_folder') . $users->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                return $users;
            })
            ->toArray();

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
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
                ->join('job_bookmarks as jb', 'j.id', '=', 'jb.job_id')
                ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at','j.salary_start_range','j.salary_end_range','s.name as state_name',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title')
                ->where('jb.user_id', $request->user_id)
                ->where('j.deleted_at', NULL)
                ->where('j.status',1)
                ->orderBy('j.id', 'desc')
                ->get()->toArray();
            
            
            if(count($jobs) > 0)
            {
                foreach($jobs as $job)
                {
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
                ->leftJoin('clients as c','c.user_id','=','j.user_id')
                ->leftJoin('users as u','u.id','=','c.user_id')
                ->leftJoin('states as s2', 'c.state_id', '=', 's2.id')
                ->leftJoin('cities as city2', 'c.city_id', '=', 'city2.id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
                ->leftJoin('employment_types as et', 'j.employment_type_id', '=', 'et.id')
                ->leftJoin('job_bookmarks as jb', function ($join) use ($user_id){
                    $join->on('j.id', '=', 'jb.job_id')
                         ->where('jb.user_id', '=', $user_id); 
                })
                ->leftJoin('job_applications as ja', function ($join) use ($user_id){
                    $join->on('j.id', '=', 'ja.job_id')
                         ->where('ja.user_id', '=', $user_id); 
                })
                ->select('j.*','s.name as state_name',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'et.title as employment_type_title',
                DB::raw('CASE WHEN jb.id IS NOT NULL THEN 1 ELSE 0 END AS bookmark_exists') ,
                DB::raw('CASE WHEN ja.id IS NOT NULL THEN 1 ELSE 0 END AS job_applied'),'c.id as company_id',
                'c.company_name','c.primary_industry','c.company_size','c.bio','c.founded_in','s2.name as company_state_name',  'city2.city_name as company_city_name','c.created_at as company_created_at',
                'u.profile_pic as company_profile_pic','u.unique_id as company_unique_id'
                )
                ->where(['j.unique_id' => $request->jobId])
                ->where('j.status',1)
                ->get()
                ->map(function ($raw) {
                    // Add dir_path column and its value to each record
                    $raw->company_profile_pic_path = (!empty($raw->company_profile_pic))?url(config('custom.user_folder') . $raw->company_profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $raw;
                })
                ->first();
                
            $attachments = DB::table('job_attatchments as d')
                ->select('d.id','d.title', 'd.file_name', 'd.file_type', 'd.file_size')
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
            
            if(!$raw->company_id)
            {
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

            if(!empty($raw))
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

            if(!empty($result))
            {
                DB::table('job_bookmarks')->where('user_id', $request->user_id)->where('job_id', $request->job_id)->delete();
                $result = array('status' => true, 'message' => "Job removed from bookmarked",'data' => 0);
            }
            else
            {
                $param = array('user_id' => $request->user_id,
                        'job_id' => $request->job_id,
                );
                DB::table('job_bookmarks')->insert($param);
                $result = array('status' => true, 'message' => 'Job marked as bookmarked','data' => 1);
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
            'job_id' => 'required',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            
            $agencyUser = DB::table('jobs as j')
                ->join('users as u', 'j.user_id', '=', 'u.id')
                ->select('u.id','u.name','u.email','j.unique_id')
                ->where(['j.id' => $request->job_id])
                ->first();
                
            $param = array(
                'from_id' => $request->user_id,
                'to_id' => $agencyUser->id,
                'job_id' => $request->job_id,
                'message' => $request->message,
                'created_by' => $request->user_id,
                'created_at' => $this->entryDate,
            );
            $last_id = DB::table('job_messages')->insertGetId($param);
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
            
            $param = array(
                'job_unique_id' =>$agencyUser->unique_id,
                'receiver_name' => $agencyUser->name,
                'receiver_email' => $agencyUser->email,
                'sender_name' => $user->name,
                'message' => $request->message,
                'dashboard_path' => 'https://staging.orionallied.com/tn-dashboard/client/dashboard'
            );

            Mail::send('emails.user.job-message', $param, function ($message) use ($param) {
                $message->subject($param['sender_name'].' '.config('custom.job_message'));
                $message->to($param['receiver_email']);
            });
            

            DB::commit();
            $result = array('status' => true, 'message' => "Message has been successfully posted");
            }
            else
            {
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

            if(!empty($result))
            {
                /*
                $param['updated_at'] = $this->entryDate;
                $param['deleted_at'] = $this->entryDate;
                DB::table('job_applications')->where('id', $result->id)->update($param);
                */
                DB::table('job_applications')->where('user_id', $request->user_id)->where('job_id', $request->job_id)->delete();
                $result = array('status' => true, 'message' => "Job application removed",'data' => 0);
            }
            else
            {
                $param = array('user_id' => $request->user_id,
                        'job_id' => $request->job_id,
                        'status' => 1,
                        'created_at' => $this->entryDate,
                );
                DB::table('job_applications')->insert($param);
                
                $param = array(
                        'role_id' => 5
                    );
                DB::table('users')->where('id', $request->user_id)->where('role_id', 4)->update($param);        
                    
                $result = array('status' => true, 'message' => 'Applied for job successfully','data' => 1);
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
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('job_bookmarks as jb', function ($join) use ($user_id){
                    $join->on('j.id', '=', 'jb.job_id')
                         ->where('jb.user_id', '=', $user_id); 
                })
                ->select('j.id', 'j.title', 'j.unique_id', 'ja.created_at','s.name as state_name',  'city.city_name', 'ja.status',
                DB::raw('CASE WHEN jb.id IS NOT NULL THEN 1 ELSE 0 END AS bookmark_exists'))
                ->where('ja.user_id', $request->user_id)
                ->where('j.deleted_at', NULL)
                ->where('j.status',1);
            
            if (isset($request->job_status) && $request->job_status != '') {
                $appliedJobs->where('ja.status', $request->job_status);
            }
            if (isset($request->keyword) && $request->keyword != '') {
                $keyword = '%' . $request->keyword . '%';

                $appliedJobs->where('j.title', 'like', $keyword);
            }
                $appliedJobs = $appliedJobs->orderBy('ja.id', 'desc')
                ->get()->toArray();
            
            if(count($appliedJobs) > 0)
            {
                foreach($appliedJobs as $job)
                {
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
            
            $user_id = $request->user_id;
            
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            $uniqueJobs = DB::table('job_messages as jm')
            ->join('jobs as j', 'j.id', '=', 'jm.job_id')
            ->leftJoin('users as u','u.id','=','j.user_id')
            ->leftJoin(DB::raw("(SELECT MAX(id) as latest_id FROM job_messages WHERE deleted_at IS NULL GROUP BY job_id) as latest"), 'jm.id', '=', 'latest.latest_id')
            ->select('j.id as job_id','j.title as job_title', 'u.name', 'u.unique_id','u.profile_pic',
                    DB::raw('(SELECT jm1.message FROM job_messages jm1 WHERE jm1.job_id = jm.job_id AND jm1.deleted_at IS NULL AND (jm1.from_id = "'.$user_id.'" OR jm1.to_id = "'.$user_id.'") ORDER BY jm1.id DESC LIMIT 1) as message'), 
                    DB::raw('(SELECT jm2.created_at FROM job_messages jm2 WHERE jm2.job_id = jm.job_id AND jm2.deleted_at IS NULL AND (jm2.from_id = "'.$user_id.'" OR jm2.to_id = "'.$user_id.'") ORDER BY jm2.id DESC LIMIT 1) as created_at')
            )
            ->where(function ($query) use ($user_id) {
                $query->where('jm.from_id', $user_id)
                      ->orWhere('jm.to_id', $user_id);
            })
            ->whereNull('jm.deleted_at')
            ->where('j.status',1);
            
            // Apply filters if present
            if (isset($request->keyword) && !empty($request->keyword)) {
                $uniqueJobs->where('j.title', 'LIKE', "%{$request->keyword}%");
            }
            
            $uniqueJobs = $uniqueJobs->orderBy('jm.id', 'desc')
            ->groupBy('jm.job_id')
            ->get()
            ->map(function ($uniqueJobs) {
                // Add dir_path column and its value to each record
                $uniqueJobs->profile_pic_path = (!empty($uniqueJobs->profile_pic))?url(config('custom.user_folder') . $uniqueJobs->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
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
            'job_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            
            $user_id = $request->user_id;
            
            
            $uniqueJobs = DB::table('job_messages as jm')
            ->join('jobs as j', 'j.id', '=', 'jm.job_id')
            ->leftJoin('users as u','u.id','=','jm.from_id')
            ->leftJoin('users as u2','u2.id','=','jm.to_id')
            ->select('jm.id','j.id as job_id','jm.message','jm.created_at', 'u.id as sender_user_id','u.name as sender_name', 'u.unique_id as sender_unique_id','u.profile_pic as sender_profile_pic',
            'u2.id as receiver_user_id','u2.name as receiver_name', 'u2.unique_id as receiver_unique_id','u2.profile_pic as receiver_profile_pic')
            ->where(function ($query) use ($user_id) {
                $query->where('jm.from_id', $user_id)
                      ->orWhere('jm.to_id', $user_id);
            })
            ->where('j.status',1)
            ->where('j.id',$request->job_id)
            ->whereNull('jm.deleted_at')
            ->orderBy('jm.created_at', 'ASC')
            ->get()
            ->map(function ($uniqueJobs) {
                // Add dir_path column and its value to each record
                $uniqueJobs->sender_profile_pic_path = (!empty($uniqueJobs->sender_profile_pic))?url(config('custom.user_folder') . $uniqueJobs->sender_profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                return $uniqueJobs;
            })
            ->map(function ($uniqueJobs) {
                // Add dir_path column and its value to each record
                $uniqueJobs->receiver_profile_pic_path = (!empty($uniqueJobs->receiver_profile_pic))?url(config('custom.user_folder') . $uniqueJobs->receiver_profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                return $uniqueJobs;
            })
            ->toArray();
        
            if(count($uniqueJobs) > 0)
            {
                foreach($uniqueJobs as $job)
                {
                    $param = array(
                        'is_seen' => 1
                    );
                    DB::table('job_messages')->where('id', $job->id)->where('job_id', $job->job_id)->where('is_seen', 0)->update($param);        
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
                ->select('u.*','ud.bio','ud.country_id','ud.state_id','ud.city_id','ud.address_line1','ud.address_line2','ud.primary_industry','ud.company_size','ud.website','ud.founded_in','s.name as state_name', 'city.city_name','ud.social_media_links','ud.company_name')
                ->where(['u.unique_id' => $request->userID])
                ->first();



            $raw->facebook_url = $raw->twitter_url = $raw->instagram_url = $raw->linkedin_url = "";


            $data = array();
            
            // Check if $raw exists
            if ($raw) {
                // Add dir_path column and its value to the record
                $raw->profile_pic_path = (!empty($raw->profile_pic))?url(config('custom.user_folder') . $raw->profile_pic):'';


                $social_links = json_decode($raw->social_media_links,true);
                if(!empty($social_links))
                {
                    foreach($social_links as $k=>$v)
                    {
                        if($v['platform'] == 'Facebook' && !empty($v['url']) && $v['url'] != null)
                            $raw->facebook_url = $v['url'];
                        else if($v['platform'] == 'Twitter' && !empty($v['url']) && $v['url'] != null)
                            $raw->twitter_url = $v['url'];
                        else if($v['platform'] == 'Instagram' && !empty($v['url']) && $v['url'] != null)
                            $raw->instagram_url = $v['url'];
                        else if($v['platform'] == 'Linkedin' && !empty($v['url']) && $v['url'] != null)
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
                ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at','j.is_starred','s.name as state_name', 'city.city_name', 'p.profession', 'sp.specialty','j.status'
                    )
                ->where('j.user_id', $raw->id)
                ->where('j.status', 1)
                ->where('j.deleted_at', NULL)
                ->orderBy('j.id', 'desc')
                ->get()->toArray();
            }

            
            
            $data['agency_details'] = $raw;
            


            if(!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $data);
            else
                $result = array('status' => false, 'message' => 'Invalid Agency ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}