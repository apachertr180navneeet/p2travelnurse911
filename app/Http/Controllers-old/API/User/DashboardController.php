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
            
            $data['resumes'] = DB::table('user_resumes as d')
                ->select('d.id', 'd.title',  'd.file_name', 'd.file_type', 'd.file_size','d.created_at')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->whereNotNull('d.file_name') // Add this condition
                ->where('d.file_name', '!=', '') // Add this condition
                ->orderBy('d.id', 'desc')
                ->limit(4)
                ->get()
                ->map(function ($resumes) {
                    // Add dir_path column and its value to each record
                    $resumes->dir_path = url(config('custom.resume_folder') . $resumes->file_name); 
                    return $resumes;
                })
                ->toArray();
            
            $data['applied_job'] = DB::table('job_applications as ja')
                ->join('jobs as j', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->select('j.id', 'j.title', 'j.unique_id', 'ja.created_at','s.name as state_name',  'city.city_name', 'ja.status')
                ->where('ja.user_id', $request->user_id)
                ->where('j.deleted_at', NULL)
                ->orderBy('ja.id', 'desc')
                ->get()->toArray();
                
            $resumeData = DB::table('user_resumes as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get();
            
            $resume_progress = 0;
            if($resumeData->count() > 0)
            {
                $resume_progress = 100;
            }
            
            $skillData = DB::table('user_skills as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->orderBy('d.id', 'desc')
                ->get();
            
             $skill_progress = 0;
            if($skillData->count() > 0)
            {
                if($skillData->count() == 1)
                {
                    $skill_progress = 50;        
                }
                else if($skillData->count() >= 2)
                {
                    $skill_progress = 100;        
                }
                
            }
            
            $workHistoryData = DB::table('user_work_histories as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get();
            
            $work_history_progress = 0;
            if($workHistoryData->count() > 0)
            {
                if($workHistoryData->count() == 1)
                {
                    $work_history_progress = 50;        
                }
                else if($workHistoryData->count() >= 2)
                {
                    $work_history_progress = 100;        
                }
            }
            
            $educationData = DB::table('user_educations as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get();
            
            $education_progress = 0;
            if($educationData->count() > 0)
            {
                $education_progress = 100;        
            }
            
            $checklistData = DB::table('user_compliance_checklists as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->where('d.status', 1)
                ->orderBy('d.id', 'desc')
                ->get();
            
            $checklist_progress = 0;
            if($checklistData->count() > 0)
            {
                $checklist_progress = 100;        
            }
                
            $personalInfoData = DB::table('users as u')
                ->join('user_details as ud', 'u.id', '=', 'ud.user_id')
                ->select('u.name', 'u.email', 'u.phone', 'u.profile_pic', 'ud.bio',  'ud.state_id', 'ud.city_id', 'ud.address_line1', 'ud.total_experience')
                ->where('u.id', $request->user_id)->get()->first();
            
            $personal_info_progress = 0;
            if(!empty($personalInfoData))
            {
                $incVal = 100/9;
                
                if($personalInfoData->name != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->email != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->phone != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->profile_pic != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->bio != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->state_id != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->city_id != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->address_line1 != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->total_experience != '')
                    $personal_info_progress += $incVal;
            }
            
            /* Job Preference */
            $job_preference_progress = 0;
            
            $userEmployeeData = DB::table('user_preferred_employment_types as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->get();
            if($userEmployeeData->count() > 0)
            {
                $job_preference_progress += 33;    
            }
            
            $userShiftData = DB::table('user_preferred_shifts as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->get();
            if($userShiftData->count() > 0)
            {
                $job_preference_progress += 33;    
            }
            
             $userStateData = DB::table('user_preferred_states as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->get();
            if($userStateData->count() > 0)
            {
                $job_preference_progress += 33;    
            }
            
            if($job_preference_progress == 99)
                $job_preference_progress = 100;
                
            #References
            $referenceData = DB::table('user_references as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->whereNotNull('d.name')
                ->where(function($query) {
                    $query->whereNotNull('d.phone')
                          ->orWhereNotNull('d.email');
                })
                ->orderBy('d.id', 'desc')
                ->get();
            
            $references_progress = 0;
            if($referenceData->count() > 0)
            {
                if($referenceData->count() == 1)
                {
                    $references_progress = 50;        
                }
                else if($referenceData->count() >= 2)
                {
                    $references_progress = 100;        
                }
            }
            
            $overall_progress = round((($job_preference_progress+$resume_progress+$skill_progress+$work_history_progress+$references_progress+$education_progress+$personal_info_progress+$checklist_progress)*100)/800);
            
            $data['profile_statistics'] = array('overall_progress' => $overall_progress, 
                                                'resume_progress' => $resume_progress, 
                                                'skill_progress' => $skill_progress, 
                                                'work_history_progress' => $work_history_progress,
                                                'education_progress' => $education_progress,
                                                'references_progress' => $references_progress,
                                                'personal_info_progress' => round($personal_info_progress),
                                                'job_preference_progress' => $job_preference_progress,
                                                'skill_checklist_progress' => $checklist_progress
                                                );
            
            $result = array('status' => true, 'message' => "Dashboard data found", 'data' => $data);
        } catch (Exception $e) {
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
            
            $data['shortListedJobs'] = DB::table('jobs as j')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id') 
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
                ->join('job_bookmarks as jb', 'j.id', '=', 'jb.job_id')
                ->select('j.id')
                ->where('jb.user_id', $user_id)
                ->where('j.deleted_at', NULL)
                ->where('jb.is_seen', 0)
                ->count();
                
            $data['appliedJobs'] = DB::table('job_applications as ja')
                ->join('jobs as j', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->select('j.id', 'j.title', 'j.unique_id', 'ja.created_at','s.name as state_name',  'city.city_name', 'ja.status')
                ->where('ja.user_id', $user_id)
                ->where('j.deleted_at', NULL)
                ->where('ja.is_seen', 0)
                ->count();    
            
             DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            $data['messages'] =DB::table('job_messages as jm')
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
            ->where('jm.is_seen',0)
            ->whereNull('jm.deleted_at')
            ->orderBy('jm.id', 'desc')
            ->groupBy('jm.job_id')
            ->get()
            ->count();
            
            $data['checklists'] = DB::table('compliance_assigned_checklists as cac')
                ->join('compliance_checklists as cc', 'cc.id', '=', 'cac.checklist_id')
                ->select('cc.id','cc.slug', 'cc.title',  'cac.created_at')
                ->where('cac.user_id', $user_id)
                ->where('cc.status', 1)
                ->where('cc.deleted_at', NULL)
                ->orderBy('cac.id', 'desc')
                ->get()->count();
            
            
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
                ->select('d.id', 'd.title',  'd.file_name', 'd.file_type', 'd.file_size','d.created_at')
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
    
    public function generateResumeTitle() {
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
            /*
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
                    $param['file_size'] = number_format($imageSize / 1048576,2).' MB';
                }
            } 
            $param['title'] = $this->generateResumeTitle();
            $param['user_id'] = $request->user_id;
            $param['created_at'] = $this->entryDate;
            DB::table('user_resumes')->insert($param);
            $msg = "Resume has been been successfully uploaded";
            */
            
            
                
            if ($request->file('file_name')) {
                $file = $request->file('file_name');
                $imageSize = $file->getSize();
                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $file->getClientOriginalExtension();
                $path = config('custom.resume_folder');
                
                $randomNumber = time() * rand();
                $fileName = $originalFileName .'-'.$randomNumber. '.' . $ext;
                while (file_exists($path . '/' . $fileName)) {
                    $randomNumber = time() * rand();
                    $fileName = $originalFileName . '-' . $randomNumber . '.' . $ext;
                }
            
                $upload = $file->move($path, $fileName);
                if ($upload) {
                    $param['file_name'] = $fileName;
                    $param['title'] = $fileName;
                    $param['file_type'] = $ext;
                    $param['file_size'] = number_format($imageSize / 1048576, 2) . ' MB';
                    
                     /*$param['title'] = $this->generateResumeTitle();*/
                    $param['user_id'] = $request->user_id;
                    $param['created_at'] = $this->entryDate;
                    DB::table('user_resumes')->insert($param);
                    $msg = "Resume has been been successfully uploaded";
                    DB::commit();
                    $result = array('status' => true, 'message' => $msg);
                }
                else
                {
                    DB::rollback();
                     $result = array('status' => false, 'message' => 'An Error occured while uploading resume.');
                }
            }

        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    
    
    
   
}
