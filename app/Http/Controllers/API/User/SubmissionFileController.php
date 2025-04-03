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

class SubmissionFileController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## FUnction to get Submission File Data
    public function getSubmissionFileData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $incomplete_tabs = array();
            
            $has_profile_completed = false;
            
            $resume_builder_progress = 0;
            /* Job Preference */
            $job_preference_progress = 0;

            $userEmployeeData = DB::table('user_preferred_employment_types as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->get();
            if ($userEmployeeData->count() > 0) {
                $job_preference_progress += 10;
            }
            $userShiftData = DB::table('user_preferred_shifts as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->get();
            if ($userShiftData->count() > 0) {
                $job_preference_progress += 10;
            }
            $userStateData = DB::table('user_preferred_states as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->get();
            if ($userStateData->count() > 0) {
                $job_preference_progress += 10;
            }
            // Get user certificate and license count
            $userActiveLicense = DB::table('user_active_certificates')->where('user_id', $request->user_id)->get();
            if ($userActiveLicense->count() > 0) {
                $job_preference_progress += 10;
            }
            // user active state license
            $userStateLicense = DB::table('user_state_license')->where('user_id', $request->user_id)
                ->get();
            if ($userStateLicense->count() > 0) {
                $job_preference_progress += 10;
            }
                        
            #Personal Info
            $personalInfoData = DB::table('users as u')
                ->join('user_details as ud', 'u.id', '=', 'ud.user_id')
                ->select('u.name', 'u.email', 'u.phone', 'u.profile_pic', 'ud.bio',  'ud.state_id', 'ud.city_id', 'ud.address_line1',
                'ud.specialty_experience',
                'ud.EMR_experience',
                'ud.teaching_hospital_experience',
                'ud.travel_experience',
                'ud.fully_vaccinated'
                )->where('u.id', $request->user_id)->get()->first();

            if (!empty($personalInfoData->specialty_experience)) {
                $job_preference_progress += 10;
            }
            if (!empty($personalInfoData->EMR_experience)) {
                $job_preference_progress += 10;
            }
            if (!empty($personalInfoData->teaching_hospital_experience)) {
                $job_preference_progress += 10;
            }
            if (!empty($personalInfoData->travel_experience)) {
                $job_preference_progress += 10;
            }
            if (!empty($personalInfoData->fully_vaccinated)) {
                $job_preference_progress += 10;
            }
            if ($job_preference_progress >= 99) {
                $job_preference_progress = 100;
            }
            
            if($job_preference_progress != 100)
                $incomplete_tabs['job_preference'] = array('title' => 'Professional Details','link' => '/user/profile?ref=submission-file','tab' => 'job_preference');

            $personal_info_progress = 0;
            if (!empty($personalInfoData)) {
                $incVal = 100 / 7;

                if ($personalInfoData->name != '')
                    $personal_info_progress += $incVal;
                if ($personalInfoData->email != '')
                    $personal_info_progress += $incVal;
                if ($personalInfoData->phone != '')
                    $personal_info_progress += $incVal;
                // if ($personalInfoData->profile_pic != '')
                //     $personal_info_progress += $incVal;
                if ($personalInfoData->bio != '')
                    $personal_info_progress += $incVal;
                if ($personalInfoData->state_id != '')
                    $personal_info_progress += $incVal;
                if ($personalInfoData->city_id != '')
                    $personal_info_progress += $incVal;
                if ($personalInfoData->address_line1 != '')
                    $personal_info_progress += $incVal;
            }
            
            if ($personal_info_progress >= 99) {
                $personal_info_progress = 100;
            }
            
            if($personal_info_progress != 100)
                $incomplete_tabs['personal_info'] = array('title' => 'Personal Information','link' => '/user/profile?ref=submission-file','tab' => 'personal_info');
            
            # Work History
            $workHistoryData = DB::table('user_work_histories as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get();

            $work_history_progress = 0;
            if ($workHistoryData->count() > 0) {
                $work_history_progress = 100;            
            }
            
            if($work_history_progress != 100)
                $incomplete_tabs['work_history'] = array('title' => 'Work History','link' => '/user/profile?ref=submission-file','tab' => 'work_history');

            #Educational Info
            $educationData = DB::table('user_educations as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get();

            $education_progress = 0;
            if ($educationData->count() > 0) {
                $education_progress = 100;
            }
            
            if($education_progress != 100)
                $incomplete_tabs['educational_info'] = array('title' => 'Educational Information','link' => '/user/profile?ref=submission-file','tab' => 'educational_info');

            $resume_builder_progress = 0;
            if($job_preference_progress == 100)
                $resume_builder_progress += 25;
            if($personal_info_progress == 100)
                $resume_builder_progress += 25;
            if($work_history_progress == 100)
                $resume_builder_progress += 25;
            if($education_progress == 100)
                $resume_builder_progress += 25;
            
            
            $submission_file_builder_progress = 0;
            
            #Skill Checklist
            $checklistData = DB::table('user_compliance_checklists as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->where('d.status', 1)
                ->orderBy('d.id', 'desc')
                ->get();

            $checklist_progress = 0;
            if ($checklistData->count() > 0) {
                $checklist_progress = 100;
            }
            
            if($checklist_progress != 100)
                $incomplete_tabs['skill_checklist'] = array('title' => 'Skill Checklist','link' => '/user/skill-checklists?ref=submission-file','tab' => '');

            
            #References
            $referenceData = DB::table('user_references as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->whereNotNull('d.name')
                ->where(function ($query) {
                    $query->whereNotNull('d.phone')
                        ->orWhereNotNull('d.email');
                })
                ->orderBy('d.id', 'desc')
                ->get();

            $references_progress = 0;
            if ($referenceData->count() > 0) {
                $references_progress = 100;
            }
        
            if($references_progress != 100)
                $incomplete_tabs['references'] = array('title' => 'References','link' => '/user/profile?ref=submission-file','tab' => 'references');
                
                
            $submission_file_builder_progress = 0;
            if($checklist_progress == 100)
                $submission_file_builder_progress += 50;
            if($references_progress == 100)
                $submission_file_builder_progress += 50;
            
            $overall_progress = 0;
            
            if ($resume_builder_progress == 100 && $submission_file_builder_progress == 100) {
                $overall_progress = 100;
            }
            else if ($resume_builder_progress == 100 ) {
                $overall_progress = 50;
            }
            else if ($submission_file_builder_progress == 100) {
                $overall_progress = 50;
            }
            
            $overall_progress = round($overall_progress);
            // Make sure overall progress can't exceed 100%
            $overall_progress = min(100, $overall_progress);
            
            if($overall_progress == 100)
                $has_profile_completed = true;

            $result = array('status' => true, 'message' => "Submission data found", 'has_profile_completed' => $has_profile_completed, 'incomplete_tabs' => $incomplete_tabs);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

}
