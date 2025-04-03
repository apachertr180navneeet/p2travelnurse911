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
            $country_id = null;
            $state = DB::table('states')->select('country_id')
                ->where('id', $request->state_id)->first();
            if (!empty($state)) {
                $country_id = $state->country_id;
            }

            if(empty($request->dob) || $request->dob == 'null')
                $request->dob = NULL;

            $info = [
                'bio' => $request->bio,
                'country_id' => $country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'dob' => (isset($request->dob))?$request->dob:null,
                'total_experience' => $request->total_experience,
                'updated_at' => $this->entryDate,
            ];
            DB::table('user_details')->where('user_id', $request->user_id)->update($info);

            DB::commit();
            $result = array('status' => true, 'message' => "Personal info has been been successfully updated");
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get profle
    public function getUserProfile(Request $request)
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
                ->join('user_details as ud', 'u.id', '=', 'ud.user_id')
                ->leftJoin('country as c', 'ud.country_id', '=', 'c.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->select('u.id as user_id', 'u.name', 'u.email', 'u.phone', 'u.country_code', 'u.profile_pic', 
                DB::raw('COALESCE(ud.bio, "") as bio'),
                'ud.country_id', 'ud.state_id', 'ud.city_id', 
                DB::raw('COALESCE(ud.address_line1, "") as address_line1'),
                DB::raw('COALESCE(ud.address_line2, "") as address_line2'),
                'ud.dob', 'ud.total_experience', 'c.name as country_name', 's.name as state_name', 'city.city_name')
                ->where('u.id', $request->user_id)->get()->toArray();

            if (!empty($user) && !empty($user[0]->profile_pic)) {
                $user[0]->profile_pic = url(config('custom.user_folder') . $user[0]->profile_pic);
                
            }
            
           

            $result = array('status' => true, 'message' => "Record fetched", 'data' => $user);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to add/update work history
    public function updateWorkHistory(Request $request)
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

            $param = $request->all();
            unset($param['token']);
            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                DB::table('user_work_histories')->where('id', $request->id)->update($param);
                $msg = "Work history has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                DB::table('user_work_histories')->insert($param);
                $msg = "Work history has been been successfully created";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get work history
    public function getWorkHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $work_history = DB::table('user_work_histories as wh')
                ->leftJoin('states as s', 'wh.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'wh.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'wh.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'wh.specialty_id', '=', 'sp.id')
                ->select('wh.id', 'wh.title', 'wh.company_name', 'wh.start_month', 'wh.start_year', 'wh.end_month', 'wh.end_year', 'wh.currently_working', 'wh.state_id', 'wh.city_id', 'wh.profession_id', 'wh.specialty_id', 'wh.employment_type_id', 's.name as state_name', 'city.city_name', 'p.profession', 'sp.specialty')
                ->where('wh.user_id', $request->user_id)
                ->where('wh.deleted_at', NULL)
                ->orderBy('wh.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($work_history)) . " Record found", 'data' => $work_history);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete work history
    public function deleteWorkHistory(Request $request)
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
                DB::table('user_work_histories')->where('id', $request->id)->update($param);
                $msg = "Work history has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to add/update educational Info
    public function updateEducationalInfo(Request $request)
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

            $param = $request->all();
            unset($param['token']);
            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                DB::table('user_educations')->where('id', $request->id)->update($param);
                $msg = "Educational Info has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                DB::table('user_educations')->insert($param);
                $msg = "Educational Info has been been successfully created";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get educational info
    public function getEducationalInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $educational_info = DB::table('user_educations as wh')
                ->select('wh.id', 'wh.user_id', 'wh.degree', 'wh.school_college', 'wh.location', 'wh.end_month', 'wh.end_year', 'wh.currently_attending')
                ->where('wh.user_id', $request->user_id)
                ->where('wh.deleted_at', NULL)
                ->orderBy('wh.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($educational_info)) . " Record found", 'data' => $educational_info);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete Educational Info
    public function deleteEducationalInfo(Request $request)
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
                DB::table('user_educations')->where('id', $request->id)->update($param);
                $msg = "Educational Info has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update user skills
    public function updateSkills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            
            $param = $request->all();
            unset($param['token']);
            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                DB::table('user_skills')->where('id', $request->id)->update($param);
                $msg = "Skill has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                DB::table('user_skills')->insert($param);
                $msg = "Skill has been been successfully created";
            }

            
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get user skills
    public function getUserSkills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {

            $skills = DB::table('user_skills as us')
                ->leftJoin('skills as s', 'us.skill_id', '=', 's.id')
                ->select('us.id', 'us.skill_id', 'us.skill as user_skill', 'us.experience', 'us.experience_type', 'us.created_at', 's.skill as skill_name')
                ->where('us.user_id', $request->user_id)->get()->toArray();

            $result = array('status' => true, 'message' => count($skills) . " record found", 'data' => $skills);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to delete Skill
    public function deleteUserSkills(Request $request)
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

            DB::table('user_skills')->where('id', $request->id)->delete();
            
            
                $msg = "Skill has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to Update Job Preference
    public function updateJobPreference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            
            
            $info = [
                'searchable_profile' => ($request->searchable_profile)?$request->searchable_profile:0,
                'profession_id' => ($request->profession_id)?$request->profession_id:0,
                'specialty_id' => ($request->specialty_id)?$request->specialty_id:0,
                'available_start_date' => ($request->available_start_date)?$request->available_start_date:"",
                'total_experience' => ($request->total_experience)?$request->total_experience:"",
            ];
            DB::table('user_details')->where('user_id', $request->user_id)->update($info);
            
            
            DB::table('user_preferred_employment_types')->where('user_id', $request->user_id)->delete();
            
            if(isset($request->employement_types) && !empty($request->employement_types))
            {
                foreach($request->employement_types as $k=>$v)
                {
                    $param = array(
                        'user_id' => $request->user_id,
                        'employment_type_id' => $v,
                        'created_at' => $this->entryDate
                        );
            
                    DB::table('user_preferred_employment_types')->insert($param);
                }
            }
            
            DB::table('user_preferred_shifts')->where('user_id', $request->user_id)->delete();
            
            if(isset($request->shifts) && !empty($request->shifts))
            {
                foreach($request->shifts as $k=>$v)
                {
                    $param = array(
                        'user_id' => $request->user_id,
                        'shift_id' => $v,
                        'created_at' => $this->entryDate
                        );
            
                    DB::table('user_preferred_shifts')->insert($param);
                }
            }
            
            DB::table('user_preferred_states')->where('user_id', $request->user_id)->delete();
            
            if(isset($request->desired_state_ids) && !empty($request->desired_state_ids))
            {
                foreach($request->desired_state_ids as $k=>$v)
                {
                    $param = array(
                        'user_id' => $request->user_id,
                        'state_id' => $v,
                        'created_at' => $this->entryDate
                        );
            
                    DB::table('user_preferred_states')->insert($param);
                }
            }
            
            $msg = "Job preference has been been successfully updated";
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
            
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get job preference
    public function getJobPreference(Request $request)
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
            $result = DB::table('user_preferred_employment_types as us')
                ->select('us.employment_type_id')
                ->where('us.user_id', $request->user_id)->get()->toArray();
            if(!empty($result))
            {
                foreach($result as $val)
                {
                    $data['employement_types'][] = $val->employment_type_id;
                }
            }
                
            $result = DB::table('user_preferred_shifts as us')
                ->select('us.shift_id')
                ->where('us.user_id', $request->user_id)->get()->toArray();
            if(!empty($result))
            {
                foreach($result as $val)
                {
                    $data['shifts'][] = $val->shift_id;
                }
            } 
                
            $result = DB::table('user_preferred_states as us')
                ->select('us.state_id')
                ->where('us.user_id', $request->user_id)->get()->toArray();
            if(!empty($result))
            {
                foreach($result as $val)
                {
                    $data['desired_state_ids'][] = $val->state_id;
                }
            } 
            
            $udResult = DB::table('user_details')->select('searchable_profile','profession_id','specialty_id','available_start_date','total_experience')
                ->where('user_id', $request->user_id)->first();
            if(!empty($udResult))
            {
                $data['searchable_profile'] = $udResult->searchable_profile;
                $data['profession_id'] = $udResult->profession_id;
                $data['specialty_id'] = $udResult->specialty_id;
                $data['available_start_date'] = $udResult->available_start_date;
                $data['total_experience'] = $udResult->total_experience;
            }
            
            $result = array('status' => true, 'message' => "record found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get profile common data
    public function getProfileData(Request $request){
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
            
            $overall_progress = round((($job_preference_progress+$resume_progress+$skill_progress+$work_history_progress+$education_progress+$personal_info_progress+$checklist_progress)*100)/700);
            
            $data['overall_progress'] = $overall_progress;
            
            $result = array('status' => true, 'message' => "profile data found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to add/update references
    public function updateReferences(Request $request)
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
                
           
                
            if(isset($request->references) && !empty($request->references))
            {
                foreach($request->references as $k=>$v)
                {
                    $param = array(
                        'title' => $v['title'],
                        'name' => $v['name'],
                        'email' => $v['email'],
                        'phone' => $v['phone'],
                        'user_id' => $request->user_id,
                        
                    );
                    
                    if (isset($v['id'])) {
                        $param['updated_at'] = $this->entryDate;
                        $param['updated_by'] = $request->user_id;
                        DB::table('user_references')->where('id', $v['id'])->update($param);
                        
                    } else {
                        $param['created_at'] = $this->entryDate;
                        $param['created_by'] = $request->user_id;
                        DB::table('user_references')->insert($param);
                        
                    }
                }
            }
            $msg = "References has been been successfully updated";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get references
    public function getReferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $userReferences = DB::table('user_references as ur')
                ->select('ur.*')
                ->where('ur.user_id', $request->user_id)
                ->where('ur.deleted_at', NULL)
                ->orderBy('ur.id', 'asc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($userReferences)) . " Record found", 'data' => $userReferences);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}
