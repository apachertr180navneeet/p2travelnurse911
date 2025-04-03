<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use Illuminate\Support\Facades\DB;
use Exception;
use Svg\Tag\Rect;

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

            # Check Email is already registered or not
            $query = CommonFunction::checkEmailExist($request->email, array(4, 5, 9), $request->user_id);
            if ($query) {
                $result = array('status' => false, 'message' => "Email is already registered");
                return response()->json($result);
            }

            # Get country_id from state_id
            $country_id = null;
            $state = DB::table('states')->select('country_id')
                ->where('id', $request->state_id)->first();
            if (!empty($state)) {
                $country_id = $state->country_id;
            }

            $user = [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'country_code' => $country_id,
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



            if (empty($request->dob) || $request->dob == 'null')
                $request->dob = NULL;

            $info = [
                'bio' => $request->bio,
                'country_id' => $country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'dob' => (isset($request->dob)) ? $request->dob : null,
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
                ->select(
                    'u.id as user_id',
                    'u.name',
                    'u.email',
                    'u.phone',
                    'u.country_code',
                    'u.profile_pic',
                    DB::raw('COALESCE(ud.bio, "") as bio'),
                    'ud.country_id',
                    'ud.state_id',
                    'ud.city_id',
                    DB::raw('COALESCE(ud.address_line1, "") as address_line1'),
                    DB::raw('COALESCE(ud.address_line2, "") as address_line2'),
                    'ud.dob',
                    'ud.total_experience',
                    'c.name as country_name',
                    's.name as state_name',
                    's.code as state_code',
                    'city.city_name'
                )
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
                ->select('wh.id', 'wh.title', 'wh.company_name', 'wh.start_month', 'wh.start_year', 'wh.end_month', 'wh.end_year', 'wh.currently_working', 'wh.state_id', 'wh.city_id', 'wh.profession_id', 'wh.specialty_id', 'wh.employment_type_id', 's.name as state_name', 's.code as state_code', 'city.city_name', 'p.profession', 'sp.specialty')
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
                'profession_id' => ($request->profession_id) ? $request->profession_id : 0,
                'specialty_id' => ($request->specialty_id) ? $request->specialty_id : 0,
                'available_start_date' => ($request->available_start_date) ? CommonFunction::changeDateFormat($request->available_start_date) : "",
                'total_experience' => ($request->total_experience) ? $request->total_experience : "",
                'specialty_experience' => ($request->specialty_experience) ? $request->specialty_experience : null,
                'EMR_experience' => ($request->EMR_experience) ? (is_array($request->EMR_experience) ? 
                implode(',', $request->EMR_experience) : $request->EMR_experience) : null,
                'teaching_hospital_experience' => ($request->teaching_hospital_experience) ? $request->teaching_hospital_experience : null,
                'travel_experience' => ($request->travel_experience) ? $request->travel_experience : null,
                'fully_vaccinated' => ($request->fully_vaccinated) ? $request->fully_vaccinated : null
            ];
            DB::table('user_details')->where('user_id', $request->user_id)->update($info);


            DB::table('user_preferred_employment_types')->where('user_id', $request->user_id)->delete();

            if (isset($request->employement_types) && !empty($request->employement_types)) {
                foreach ($request->employement_types as $k => $v) {
                    $param = array(
                        'user_id' => $request->user_id,
                        'employment_type_id' => $v,
                        'created_at' => $this->entryDate
                    );

                    DB::table('user_preferred_employment_types')->insert($param);
                }
            }

            DB::table('user_preferred_shifts')->where('user_id', $request->user_id)->delete();

            if (isset($request->shifts) && !empty($request->shifts)) {
                foreach ($request->shifts as $k => $v) {
                    $param = array(
                        'user_id' => $request->user_id,
                        'shift_id' => $v,
                        'created_at' => $this->entryDate
                    );

                    DB::table('user_preferred_shifts')->insert($param);
                }
            }

            DB::table('user_preferred_states')->where('user_id', $request->user_id)->delete();
            if (isset($request->desired_state_ids) && !empty($request->desired_state_ids)) {
                foreach ($request->desired_state_ids as $k => $v) {
                    $param = array(
                        'user_id' => $request->user_id,
                        'state_id' => $v,
                        'created_at' => $this->entryDate
                    );

                    DB::table('user_preferred_states')->insert($param);
                }
            }

            $msg = "Professional Details has been been successfully updated";
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
            if (!empty($result)) {
                foreach ($result as $val) {
                    $data['employement_types'][] = $val->employment_type_id;
                }
            }

            $result = DB::table('user_preferred_shifts as us')
                ->select('us.shift_id')
                ->where('us.user_id', $request->user_id)->get()->toArray();
            if (!empty($result)) {
                foreach ($result as $val) {
                    $data['shifts'][] = $val->shift_id;
                }
            }

            $result = DB::table('user_preferred_states as us')
                ->select('us.state_id')
                ->where('us.user_id', $request->user_id)->get()->toArray();
            if (!empty($result)) {
                foreach ($result as $val) {
                    $data['desired_state_ids'][] = $val->state_id;
                }
            }

            $udResult = DB::table('user_details')->select(
                'searchable_profile',
                'profession_id',
                'specialty_id',
                'available_start_date',
                'total_experience',
                'specialty_experience',
                'EMR_experience',
                'teaching_hospital_experience',
                'travel_experience',
                'fully_vaccinated'
            )->where('user_id', $request->user_id)->first();
            if (!empty($udResult)) {
                $data['searchable_profile'] = $udResult->searchable_profile;
                $data['profession_id'] = $udResult->profession_id;
                $data['specialty_id'] = $udResult->specialty_id;
                $data['available_start_date'] = $udResult->available_start_date;
                $data['total_experience'] = $udResult->total_experience;
                $data['specialty_experience'] = $udResult->specialty_experience;
                $data['EMR_experience'] = $udResult->EMR_experience ? explode(',', $udResult->EMR_experience) : [];
                $data['teaching_hospital_experience'] = $udResult->teaching_hospital_experience;
                $data['travel_experience'] = $udResult->travel_experience;
                $data['fully_vaccinated'] = $udResult->fully_vaccinated;
            }

            /** user active license */
            $userActiveLicense = DB::table('user_active_certificates')->where('user_id', $request->user_id)
                ->select(
                    'id',
                    'certificate_name',
                    DB::raw("DATE_FORMAT(certificate_expiry_date, '%m/%d/%Y') as certificate_expiry_date")
                )->get()->toArray();
            if (count($userActiveLicense) > 0) {
                $data['user_active_certificates'] = $userActiveLicense;
            }

            // user active state license
            $userStateLicense = DB::table('user_state_license')->where('user_id', $request->user_id)
                ->select('id', 'license_name', 'location',
                    DB::raw("DATE_FORMAT(license_expiry_date, '%m/%d/%Y') as license_expiry_date")
                )->get()
                ->toArray();
            if (count($userStateLicense) > 0) {
                $data['user_state_license'] = $userStateLicense;
            }

            // RTO dates details
            $userRTOdates = DB::table('user_rto_details')->where('user_id', $request->user_id)
                                ->select('id', 'rto_start_date', 'rto_end_date')
                                ->get()
                                ->toArray();
            $data['user_rto_dates'] = [];
            if (count($userRTOdates) > 0) {
                foreach ($userRTOdates as $date) {
                    $date->rto_start_date = CommonFunction::changeDateFormat($date->rto_start_date, 1);
                    $date->rto_end_date = CommonFunction::changeDateFormat($date->rto_end_date, 1);
                }
                $data['user_rto_dates'] = $userRTOdates;
            }

            $result = array('status' => true, 'message' => "record found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get profile common data
    public function getProfileData(Request $request)
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


            $resumeData = DB::table('user_resumes as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get();

            $resume_progress = 0;
            if ($resumeData->count() > 0) {
                $resume_progress = 100;
            }

            /*
            $skillData = DB::table('user_skills as d')
                ->select('d.id')
                ->where('d.user_id', $request->user_id)
                ->orderBy('d.id', 'desc')
                ->get();

            $skill_progress = 0;
            if ($skillData->count() > 0) {
                if ($skillData->count() == 1) {
                    $skill_progress = 50;
                } else if ($skillData->count() >= 2) {
                    $skill_progress = 100;
                }
            }
            */

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

        
            # Personal Info
            $personalInfoData = DB::table('users as u')
                ->join('user_details as ud', 'u.id', '=', 'ud.user_id')
                ->select('u.name', 'u.email', 'u.phone', 'u.profile_pic', 'ud.bio',  'ud.state_id', 'ud.city_id', 'ud.address_line1',
                'ud.specialty_experience',
                'ud.EMR_experience',
                'ud.teaching_hospital_experience',
                'ud.travel_experience',
                'ud.fully_vaccinated')
                ->where('u.id', $request->user_id)->get()->first();

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

            /*$resume_builder_progress = round((($job_preference_progress + $personal_info_progress + $work_history_progress + $education_progress) * 100) / 400);*/

            $resume_builder_progress = 0;
            if ($job_preference_progress == 100)
                $resume_builder_progress += 25;
            if ($personal_info_progress == 100)
                $resume_builder_progress += 25;
            if ($work_history_progress == 100)
                $resume_builder_progress += 25;
            if ($education_progress == 100)
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

            //$submission_file_builder_progress = round((($checklist_progress + $references_progress) * 100) / 200);

            $submission_file_builder_progress = 0;
            if ($checklist_progress == 100)
                $submission_file_builder_progress += 50;
            if ($references_progress == 100)
                $submission_file_builder_progress += 50;

            /*$overall_progress = round((($job_preference_progress + $resume_progress  + $work_history_progress + $references_progress + $education_progress + $personal_info_progress + $checklist_progress) * 100) / 700);*/

            $overall_progress = 0;

            /*
            if ($resume_builder_progress == 100) {
                // Resume Builder is 100%, so the overall progress is based on Submission File Builder
                $overall_progress = 50 + ($submission_file_builder_progress / 2);
            } else {
                // If Resume Builder isn't 100%, it's weighted equally with the Submission File Builder
                $overall_progress = ($resume_builder_progress / 2) + ($submission_file_builder_progress / 2);
            }
            */

            $progressMapping = [
                'job_preference_progress' => 10,
                'personal_info_progress' => 10,
                'work_history_progress' => 10,
                'education_progress' => 20,
                'checklist_progress' => 10,
                'references_progress' => 10,
            ];

            foreach ($progressMapping as $progressKey => $increment) {
                $value = $$progressKey; // Dynamically resolve the variable

                // Double increment for specific keys
                if (in_array($progressKey, ['job_preference_progress', 'personal_info_progress', 'work_history_progress'])) {
                    $overall_progress += ($value >= 50) * $increment + ($value == 100) * $increment;
                } else {
                    // Single increment for others (only for == 100)
                    $overall_progress += ($value == 100) * $increment;
                }
            }
            // Make sure overall progress can't exceed 100%
            $overall_progress = min(100, $overall_progress);

            /* $skill_progress + */

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
            'reference_no'=> 'required'
        ]);
        if ($validator->fails()) {
            $result = array(
                'status' => false, 
                'message' => $validator->errors(), 
                'data' => $request->all()
            );
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            if (isset($request->references) && !empty($request->references)) {
                foreach ($request->references as $k => $v) {
                    $k = $k + 1;
                    $param = array(
                        'title' => $v['title'],
                        'name' => $v['name'],
                        'email' => $v['email'],
                        'phone' => $v['phone'],
                        'user_id' => $request->user_id
                    );
                    $userRef = [
                        'user_id' => $request->user_id,
                        'dates_of_employment' => $v['dates_of_employment'],
                        'end_date_of_employment' => $v['end_date_of_employment'],
                        'title_while_employed' => $v['title_while_employed'],
                        'address' => $v['address']
                    ];

                    if($k == $request->reference_no) {
                        $checkAlreadyExists = DB::table('user_references')->where([
                            'user_id' => $request->user_id,
                            'reference_no' => $request->reference_no
                        ])->first();
                        $recordID = !empty($checkAlreadyExists) ? $checkAlreadyExists->id : '';
                        if($checkAlreadyExists) {
                            $param['updated_at'] = $this->entryDate;
                            $param['updated_by'] = $request->user_id;
                            DB::table('user_references')
                            ->where('reference_no', $request->reference_no)
                            ->where('user_id', $request->user_id)
                            ->update($param);
                        } else {
                            $param['reference_no'] = $request->reference_no;
                            $param['created_by'] = $request->user_id;
                            $param['created_at'] = $this->entryDate;
                            $recordID = DB::table('user_references')->insertGetId($param);
                        }
                        // update user reference details 
                        $alreadyExists = DB::table('user_reference_details')->where([
                            'reference_id' => $recordID,
                            'user_id' => $request->user_id
                        ])->first();
                        if (empty($alreadyExists)) {
                            $userRef['reference_id'] = $recordID;
                            DB::table('user_reference_details')->insert($userRef);
                        } else {
                            DB::table('user_reference_details')
                                ->where('id', $alreadyExists->id)
                                ->update($userRef);
                        }
                    }
                }
            }
            // fetch reference_no data
            $data = DB::table('user_references')
            ->leftJoin('user_reference_details as urd', 'urd.reference_id', 'user_references.id')
            ->where([
                'user_references.user_id' => $request->user_id,
                'user_references.reference_no' => $request->reference_no
            ])
            ->select('user_references.*',
                'urd.dates_of_employment',
                'urd.title_while_employed',
                'urd.address'
            )
            ->first();

            $msg = "References has been successfully updated";
            DB::commit();
            $result = array(
                'status' => true,
                'message' => $msg,
                'data' => $data
            );
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
                ->leftJoin('user_reference_details as urd', 'urd.reference_id', 'ur.id')
                ->select('ur.*', 'urd.dates_of_employment', 'urd.end_date_of_employment', 'urd.title_while_employed',
                'urd.address')
                ->where('ur.user_id', $request->user_id)
                ->where('ur.deleted_at', NULL)
                ->orderBy('ur.id', 'asc')
                ->get()->toArray();

            $userReferenceIds = array_column($userReferences, 'id');

            // Get IDs and details from user_reference_details
            $completedReferencesDetails = DB::table('user_reference_details')
                ->whereIn('reference_id', $userReferenceIds)
                ->get()
                ->keyBy('reference_id') // Use keyBy to easily match by reference_id
                ->toArray();

            // Map over each reference and set started, completed, and applied
            $userReferences = array_map(function ($userReference) use ($completedReferencesDetails) {
                // Initialize all options to false
                $userReference->started = false;
                $userReference->completed = false;
                $userReference->applied = false;

                //Convert date format
                if (!empty($userReference->dates_of_employment)) {
                    $userReference->dates_of_employment = date('Y-m-d',strtotime($userReference->dates_of_employment));
                }

                if (!empty($userReference->end_date_of_employment)) {
                    $userReference->end_date_of_employment = date('Y-m-d',strtotime($userReference->end_date_of_employment));
                }

                // Check if the reference has been started
                if (!empty($userReference->name) && !empty($userReference->email)) {
                    $userReference->started = true;
                }
                // Check if the reference has been applied
                if ($userReference->has_reference_sent == 1) {
                    $userReference->started = false;
                    $userReference->applied = true;
                }
                // Check if the userReference is completed and include details
                if (isset($completedReferencesDetails[$userReference->id])) {
                    // Attach the reference details to the user reference object
                    $userReference->details = $completedReferencesDetails[$userReference->id];
                    if(!empty($userReference->details->reference_by_email)) {
                        $userReference->started = false;
                        $userReference->applied = false;
                        $userReference->completed = true;
                    }
                }

                return $userReference;
            }, $userReferences);

            /*
            started: false,
            completed: false,
            applied: false,
            */

            $result = array('status' => true, 'message' => (count($userReferences)) . " Record found", 'data' => $userReferences);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update user certificates & licenses
    public function updateCertificatesLicense(Request $request)
    {
        if (!empty($request->id)) {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'id' => 'required',
                'type' => 'required|in:1,2,3'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'type' => 'required|in:1,2,3',
                'certificate_name' => 'required_if:type,1',
                'license_name' => 'required_if:type,2',
                'start_date' => 'required_if:type,3',
                'end_date' => 'required_if:type,3'
            ]);
        }

        if ($validator->fails()) {
            $result = array('status' => false, 'message' => $validator->errors());
            return response()->json($result);
        }

        if ($request->type == 1) {
            // update user active certificates and removed old data
            if (!empty($request->id)) {
                DB::table('user_active_certificates')->where('id', $request->id)->delete();
                $message = "User's certificate deleted successfully!";
            } else {
                $param = array(
                    'user_id' => $request->user_id,
                    'certificate_name' => $request->certificate_name,
                    'certificate_expiry_date' => CommonFunction::changeDateFormat($request->certificate_expiry_date)
                );
                DB::table('user_active_certificates')->insert($param);
                $message = "User's certificate added successfully!";
            }
        } elseif ($request->type == 2) {
            // Delete state license
            if (!empty($request->id)) {
                DB::table('user_state_license')->where('id', $request->id)->delete();
                $message = "User's state license deleted successfully!";
            } else {
                $param = array(
                    'user_id' => $request->user_id,
                    'license_name' => $request->license_name,
                    'location' => $request->location,
                    'license_expiry_date' => CommonFunction::changeDateFormat($request->license_expiry_date)
                );
                $message = "User's state license added successfully!";
                DB::table('user_state_license')->insert($param);
            }
        } elseif ($request->type == 3) {
            // Delete state license
            if (!empty($request->id)) {
                DB::table('user_rto_details')->where('id', $request->id)->delete();
                $message = "User's RTO details deleted successfully!";
            } else {
                $startDate = CommonFunction::changeDateFormat($request->start_date);
                $endDate = CommonFunction::changeDateFormat($request->end_date);

                $param = array(
                    'user_id' => $request->user_id,
                    'rto_start_date' => $startDate,
                    'rto_end_date' => $endDate,
                );
                $message = "User's RTO details added successfully!";
                DB::table('user_rto_details')->insert($param);
            }
        }
        // retrieve user's certificates 
        $data = [];
        $userActiveLicense = DB::table('user_active_certificates')->where('user_id', $request->user_id)
            ->select(
                'id',
                'certificate_name',
                DB::raw("DATE_FORMAT(certificate_expiry_date, '%m/%d/%Y') as certificate_expiry_date")
            )->get()->toArray();
        $data['user_active_certificates'] = [];
        if (count($userActiveLicense) > 0) {
            $data['user_active_certificates'] = $userActiveLicense;
        }
        $userStateLicense = DB::table('user_state_license')->where('user_id', $request->user_id)
            ->select('id', 'license_name', 'location',
                DB::raw("DATE_FORMAT(license_expiry_date, '%m/%d/%Y') as license_expiry_date") 
            )->get()
            ->toArray();
        $data['user_state_license'] = [];
        if (count($userStateLicense) > 0) {
            $data['user_state_license'] = $userStateLicense;
        }

        $userRTOdates = DB::table('user_rto_details')->where('user_id', $request->user_id)
            ->select('id', 'rto_start_date', 'rto_end_date')
            ->get()
            ->toArray();
        $data['user_rto_dates'] = [];
        if (count($userRTOdates) > 0) {
            foreach($userRTOdates as $date) {
                $date->rto_start_date = CommonFunction::changeDateFormat($date->rto_start_date,1);
                $date->rto_end_date = CommonFunction::changeDateFormat($date->rto_end_date,1);
            }
            $data['user_rto_dates'] = $userRTOdates;
        }

        $result = array(
            'status' => true,
            'message' => $message,
            'data' => $data
        );
        return response()->json($result);
    }

    ## Function to update reference details
    public function sendReferenceRequest(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            if (isset($request->id) && !empty($request->id)) {
                $param = array();
                $param['updated_at'] = $this->entryDate;
                $param['has_reference_sent'] = 1;
                $param['updated_by'] = $request->user_id;
                DB::table('user_references')->where('id', $request->id)->update($param);
                $ref_id = $request->id;
            } else {
                $param = array(
                    'user_id' => $request->user_id,
                    'title' => $request->title,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'created_at' => $this->entryDate,
                    'has_reference_sent' => 1,
                    'created_by' => $request->user_id
                );

                $ref_id = DB::table('user_references')->insertGetId($param);
            }
            $msg = "Reference request has been been successfully sent.";


            $user_name = DB::table('users')->where('id', $request->user_id)->value('name');
            $param = array(
                'receiver_name' => $request->name,
                'receiver_email' => $request->email,
                'sender_name' => $request->sender_name,
                'reference_path' => route('reference', CommonFunction::encryptId($ref_id)),
                'user_name' => $user_name
            );

            Mail::send('emails.user.reference-request', $param, function ($message) use ($param) {
                $message->subject("Reference Request From ". $param['user_name']);
                $message->to($param['receiver_email']);
            });

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function store user's submission file documents
    public function storeSubmissionFileDocs(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => $validator->errors());
            return response()->json($result);
        }

        try {
            // Store documents 
            if(!empty($request->doc_ids))  {
                foreach($request->doc_ids as $docID)
                {
                    $param = array(
                        'user_id' => $request->user_id,
                        'doc_id' => $docID,
                        'status' => 0
                    );
                    if (!DB::table('user_submission_file_docs')->where($param)->exists()) {
                        DB::table('user_submission_file_docs')->insert($param);
                    }
                }
            }

            // Store checklists 
            if (!empty($request->skill_checklist_ids)) {
                foreach ($request->skill_checklist_ids as $checkList) {
                    $param = array(
                            'user_id' => $request->user_id,
                            'checklist_id' => $checkList,
                            'status'=> 0
                        );
                    if(!DB::table('user_submission_file_docs')->where($param)->exists()) {
                        DB::table('user_submission_file_docs')->insert($param);
                    }
                }
            }

            $message = "Selected documents successully stored in submission file!";
            $result = array(
                'status' => true,
                'message' => $message,
            );
        } catch (Exception $e) {
            $result = [
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }

        return response()->json($result);
    }
}
