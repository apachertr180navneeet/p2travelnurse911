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

class ProfileController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to update personal info
    public function updateProfile(Request $request)
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

            $user = [
                'name' => $request->name,
                'phone' => $request->phone,
                'country_code' => $request->country_code,
            ];

            if ($request->file('profile_pic')) {
                $file = $request->file('profile_pic');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() * rand() . '.' . $ext;
                $path = config('custom.user_folder');
                $upload = $file->move($path, $fileName);
                if ($upload) {
                    $user['profile_pic'] = $fileName;
                }
            }

            DB::table('users')->where('id', $request->user_id)->update($user);

            # Get country_id from state_id
            $country_id = 0;
            $state = DB::table('states')->select('country_id')
                ->where('id', $request->state_id)->first();
            if (!empty($state)) {
                $country_id = $state->country_id;
            }

            
            $request->social_media_links = array();
            
            if ((isset($request->facebook_url) && !empty($request->facebook_url)))  {
                $request->social_media_links[] = array('platform' => 'Facebook',
                'url' => $request->facebook_url);
            }
            
            if ((isset($request->twitter_url) && !empty($request->twitter_url)) 
            ) {
               $request->social_media_links[] = array('platform' => 'Twitter',
                'url' => $request->twitter_url); 
            }
            
            if ((isset($request->instagram_url) && !empty($request->instagram_url)) 
            ) {
                $request->social_media_links[] = array('platform' => 'Instagram',
                'url' => $request->instagram_url); 
            }
            
            if ((isset($request->linkedin_url) && !empty($request->linkedin_url))) {
                $request->social_media_links[] = array('platform' => 'Linkedin',
                'url' => $request->linkedin_url); 
            }

            $info = [
                'company_name' => $request->company_name,
                'website' => $request->website, 
                'primary_industry' => $request->primary_industry,
                'company_size' => $request->company_size,
                'bio' => $request->bio,
                'country_id' => $country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'founded_in' => $request->founded_in,
                'social_media_links' => $request->social_media_links,
                'updated_at' => $this->entryDate,
            ];
            DB::table('clients')->where('user_id', $request->user_id)->update($info);

            DB::commit();
            $result = array('status' => true, 'message' => "Profile has been been successfully updated");
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get profle
    public function getProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $user = DB::table('users as u')
                ->leftJoin('clients as ud', 'u.id', '=', 'ud.user_id')
                ->leftJoin('country as c', 'ud.country_id', '=', 'c.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->select('u.id as user_id', 'u.name', 'u.email', 'u.phone', 'u.country_code', 'u.profile_pic', 'ud.company_name','ud.website', 'ud.primary_industry','ud.company_size','ud.bio', 'ud.country_id', 'ud.state_id', 'ud.city_id', 'ud.address_line1', 'ud.address_line2', 'ud.founded_in', 'ud.social_media_links', 'c.name as country_name', 's.name as state_name', 'city.city_name')
                ->where('u.id', $request->user_id)->get()->toArray();

            if (!empty($user) && !empty($user[0]->profile_pic)) {
                $user[0]->profile_pic = url(config('custom.user_folder') . $user[0]->profile_pic);
            }
            
            $user[0]->facebook_url = $user[0]->twitter_url = $user[0]->instagram_url = $user[0]->linkedin_url = null;
            if (!empty($user) && !empty($user[0]->social_media_links)) {
                $social_links = json_decode($user[0]->social_media_links,true);
                
                foreach($social_links as $k=>$v)
                {
                    if($v['platform'] == 'Facebook' && !empty($v['url']))
                        $user[0]->facebook_url = $v['url'];
                    else if($v['platform'] == 'Twitter' && !empty($v['url']))
                        $user[0]->twitter_url = $v['url'];
                    else if($v['platform'] == 'Instagram' && !empty($v['url']))
                        $user[0]->instagram_url = $v['url'];
                    else if($v['platform'] == 'Linkedin' && !empty($v['url']))
                        $user[0]->linkedin_url = $v['url'];
                }
            }

            $result = array('status' => true, 'message' => "Record fetched", 'data' => $user);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    
    ## Function to get profile common meta
    public function getProfileMeta(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $data = array();
            
            
            $resumeData = DB::table('user_resumes as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get();
            
            $resume_progress = 0;
            if($resumeData->count() > 0)
            {
                if($resumeData->count() >= 3)
                {
                    $resume_progress = 100;        
                }
                else if($resumeData->count() == 2)
                {
                    $resume_progress = 66;        
                }
                else if($resumeData->count() == 1)
                {
                    $resume_progress = 33;        
                }
            }
            
            $skillData = DB::table('user_skills as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->orderBy('d.id', 'desc')
                ->get();
            
            $skill_progress = 0;
            if($skillData->count() > 0)
            {
                if($skillData->count() >= 5)
                {
                    $skill_progress = 100;        
                }
                else if($skillData->count() == 4)
                {
                    $skill_progress = 80;        
                }
                else if($skillData->count() == 3)
                {
                    $skill_progress = 60;        
                }
                else if($skillData->count() == 2)
                {
                    $skill_progress = 40;        
                }
                else if($skillData->count() == 1)
                {
                    $skill_progress = 20;        
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
                if($workHistoryData->count() >= 3)
                {
                    $work_history_progress = 100;        
                }
                else if($workHistoryData->count() == 2)
                {
                    $work_history_progress = 66;        
                }
                else if($workHistoryData->count() == 1)
                {
                    $work_history_progress = 33;        
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
                if($educationData->count() >= 3)
                {
                    $education_progress = 100;        
                }
                else if($educationData->count() == 2)
                {
                    $education_progress = 66;        
                }
                else if($educationData->count() == 1)
                {
                    $education_progress = 33;        
                }
            }
                
            $personalInfoData = DB::table('users as u')
                ->join('user_details as ud', 'u.id', '=', 'ud.user_id')
                ->select('u.name', 'u.email', 'u.phone', 'u.profile_pic', 'ud.bio', 'ud.country_id', 'ud.state_id', 'ud.city_id', 'ud.address_line1', 'ud.address_line2', 'ud.dob', 'ud.total_experience')
                ->where('u.id', $request->user_id)->get()->first();
            
            $personal_info_progress = 0;
            if(!empty($personalInfoData))
            {
                $incVal = 100/11;
                
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
                if($personalInfoData->country_id != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->state_id != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->city_id != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->address_line1 != '')
                    $personal_info_progress += $incVal;
                if($personalInfoData->dob != '')
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
            
            $overall_progress = round((($job_preference_progress+$resume_progress+$skill_progress+$work_history_progress+$education_progress+$personal_info_progress)*100)/600);
            
            $data['overall_progress'] = $overall_progress;
            
            $result = array('status' => true, 'message' => "profile data found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}
