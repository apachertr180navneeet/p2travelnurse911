<?php

namespace App\Http\Controllers\API;

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

class CommonController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    
    
    ## Function to get all skills
    public function getSkills()
    {
        try {
            $skills = DB::table('skills')->where('status', 1)->where('deleted_at', NULL)->orderBy('skill')->get()->toArray();

            $result = array('status' => true, 'message' => count($skills) . " record found", 'data' => $skills);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get all job application
    public function getJobApplicationStatus()
    {
        try {
            $job_application_status = DB::table('job_application_status')->where('status', "1")->orderBy('id')->get()->toArray();

            $result = array('status' => true, 'message' => count($job_application_status) . " record found", 'data' => $job_application_status);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get all states
    public function getStates()
    {
        try {
            $states = DB::table('states')->where('status', 1)->orderBy('name')->get()->toArray();

            $result = array('status' => true, 'message' => count($states) . " record found", 'data' => $states);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get all cities
    public function getCities($state_id)
    {
        try {
            $cities = DB::table('cities as c')
                ->join('states as s', 'c.state_code', '=', 's.code')
                ->select('c.id', 'c.city_name')
                ->where('s.id', $state_id)->orderBy('c.city_name')->get()->toArray();

            $result = array('status' => true, 'message' => count($cities) . " record found", 'data' => $cities);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get all professions
    public function getProfessions()
    {
        try {
            $states = DB::table('professions')->where('status', 1)->where('deleted_at', NULL)->orderBy('profession')->get()->toArray();

            $result = array('status' => true, 'message' => count($states) . " record found", 'data' => $states);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get all specialties
    public function getSpecialties($profession_id)
    {
        try {
            $specialities = DB::table('specialities as s')
                ->join('professions as p', 's.profession_id', '=', 'p.id')
                ->select('s.id', 's.specialty')
                ->where('p.id', $profession_id)->where('s.status', 1)->where('s.deleted_at', NULL)->orderBy('s.specialty')->get()->toArray();

            $result = array('status' => true, 'message' => count($specialities) . " record found", 'data' => $specialities);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get all Doc types
    public function getDocTypes()
    {
        try {
            $docTypes = DB::table('doc_types')
            ->where('status', '1')
            ->where('module_type','document')
            ->where('deleted_at', NULL)
            ->orderBy('doc_name')->get()->toArray();

            $result = array('status' => true, 'message' => count($docTypes) . " record found", 'data' => $docTypes);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get Employment Types
    public function getEmploymentTypes()
    {
        try {
            $employmentTypes = DB::table('employment_types')
            ->where('status', '1')
            ->where('deleted_at', NULL)
            ->orderBy('title','desc')->get()->toArray();

            $result = array('status' => true, 'message' => count($employmentTypes) . " record found", 'data' => $employmentTypes);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get Shifts
    public function getShifts()
    {
        try {
            $shifts = DB::table('shifts')
            ->where('status', '1')
            ->where('deleted_at', NULL)
            ->orderBy('title')->get()->toArray();

            $result = array('status' => true, 'message' => count($shifts) . " record found", 'data' => $shifts);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to get all users by role id
    public function getUsers($role_id)
    {
        try {
            
            $exp = explode('~',$role_id);
            if(count($exp) > 1)
            {
                $roleID = DB::table('users as u')
                ->select('u.role_id')
                ->where('u.id', $exp[1])
                ->get()->first();
                
                if($roleID->role_id == 2 || $roleID->role_id == 3)
                {
                    $users = DB::table('users as u')
                    ->select('u.id', 'u.name')
                    ->where('u.role_id', $role_id)
                    ->where('u.deleted_at', NULL)
                    ->where('u.created_by',$exp[1])
                    ->orderBy('u.name')->get()->toArray();
                }
                else
                {
                     $users = DB::table('users as u')
                    ->select('u.id', 'u.name')
                    ->where('u.role_id', $role_id)
                    ->where('u.deleted_at', NULL)
                    ->orderBy('u.name')->get()->toArray();
                }
            }
            else
            {
                 $users = DB::table('users as u')
                ->select('u.id', 'u.name')
                ->where('u.role_id', $role_id)
                ->where('u.deleted_at', NULL)
                ->orderBy('u.name')->get()->toArray();
            }
            $result = array('status' => true, 'message' => count($users) . " record found", 'data' => $users);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get announcements by role id
    public function getAnnouncements($role_id)
    {
        try {
            $announcements = DB::table('announcements as a')
                ->select('a.description')
                ->where('a.role_id', $role_id)->whereOr('a.module', $role_id)
                ->whereNotNull('a.description')
                ->get()->first();
            
            if(!empty($announcements))
                $result = array('status' => true, 'message' => "Record found", 'data' => $announcements);
            else
                $result = array('status' => false, 'message' => 'No record found');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## function return all applicants list (candidate/applicants/employee/job applicants)
    public function getApplicantsList(Request $request)
    {
        try {

            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

            $data = array();


            $data['applicants'] = DB::table('users as u')
            ->select('u.id', 'u.name')
            ->where('u.role_id', 5)
            ->where('u.deleted_at', NULL)
                ->where('u.created_by', $request->user_id)
                ->orderBy('u.name')->get()->toArray();

            $data['candidates'] = DB::table('users as u')
            ->select('u.id', 'u.name')
            ->where('u.role_id', 4)
            ->where('u.deleted_at', NULL)
                ->where('u.created_by', $request->user_id)
                ->orderBy('u.name')->get()->toArray();

            $data['employees'] = DB::table('users as u')
            ->select('u.id', 'u.name')
            ->where('u.role_id', 9)
            ->where('u.deleted_at', NULL)
                ->where('u.created_by', $request->user_id)
                ->orderBy('u.name')->get()->toArray();

            $data['job_applicants'] = DB::table('jobs as j')
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
            ->where('j.user_id', $request->user_id)
                ->select(
                    'ja.id as ja_id',
                    'u.id',
                    'u.name'
                )
                ->whereNull('ja.deleted_at')
                ->whereNull('j.deleted_at')
                ->whereNull('u.deleted_at')
                ->orderBy('u.name', 'asc')
                ->groupBy(
                    'ja.user_id'
                )
                ->get()
                ->toArray();

            $result = array('status' => true, 'message' => "record found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get submission file
    public function submissionFile($id) 
    {
        try {
            $data['title'] = 'Submission File';

            $userRecord = DB::table('users as u')
            ->join('user_details as ud', 'ud.user_id', '=', 'u.id')
            ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
            ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
            ->leftJoin('professions as p', 'ud.profession_id', '=', 'p.id')
            ->leftJoin('specialities as sp', 'ud.specialty_id', '=', 'sp.id')
            ->select(
                'u.id',
                'u.name',
                'u.email',
                'u.phone',
                'p.profession',
                'sp.specialty',
                's.name as state_name',
                'city.city_name',
                'ud.address_line1',
                'ud.address_line2',
                'ud.available_start_date',
                'ud.total_experience',
                'ud.specialty_experience',
                'ud.EMR_experience',
                'ud.teaching_hospital_experience',
                'ud.travel_experience',
                'ud.fully_vaccinated',
                'u.unique_id'
            )
            ->where('u.deleted_at', NULL)
            ->where('u.unique_id', $id)
            ->get()
            ->first();

            if (empty($userRecord)) {
                abort(404, 'Resource not found');
            }

            $data['educationalDetails'] = DB::table('user_educations as ue')
            ->select('ue.*')
            ->where('ue.user_id', $userRecord->id)
            ->whereNull('ue.deleted_at')
            ->orderBy('ue.currently_attending', 'desc')  // currently_attending 1 first
            ->orderBy('ue.end_year', 'desc')            // end_year in descending order
            ->orderBy('ue.end_month', 'desc')
            ->get()->toArray();

            $result = DB::table('user_preferred_shifts as us')
            ->leftJoin('shifts as s', 's.id', '=', 'us.shift_id')
            ->select('s.title')
            ->where('us.user_id', $userRecord->id)->get()->toArray();
            if (!empty($result)) {
                foreach ($result as $val) {
                    $data['desired_shifts'][] = $val->title;
                }
            }

            $data['work_histories'] = DB::table('user_work_histories as wh')
            ->leftJoin('states as s', 'wh.state_id', '=', 's.id')
            ->leftJoin('cities as city', 'wh.city_id', '=', 'city.id')
            ->leftJoin('professions as p', 'wh.profession_id', '=', 'p.id')
            ->leftJoin('specialities as sp', 'wh.specialty_id', '=', 'sp.id')
            ->leftJoin('employment_types as et', 'et.id', '=', 'wh.employment_type_id')
            ->select('wh.id', 'wh.title', 'wh.company_name', 'wh.start_month', 'wh.start_year', 'wh.end_month', 'wh.end_year', 'wh.currently_working', 'wh.state_id', 'wh.city_id', 'wh.profession_id', 'wh.specialty_id', 'wh.employment_type_id', 's.name as state_name', 's.code as state_code', 'city.city_name', 'p.profession', 'sp.specialty', 'et.title as employment_type_title')
            ->where('wh.user_id', $userRecord->id)
            ->where('wh.deleted_at', NULL)
            ->orderBy('wh.id', 'desc')
            ->get()->toArray();


            // Get the latest submission file title from user_submission_file_docs table
            $counter = 1;
            $latestSubmissionFile = DB::table('user_submission_file_docs')
            ->where('user_id', $userRecord->id)
            ->whereNotNull('file_title')
            ->orderby('id', 'DESC')
            ->first();
            if (!$latestSubmissionFile) {
                $title = 'Submission-File-1';
            } else {
                preg_match('/Submission-File-(\d+)$/', $latestSubmissionFile->file_title, $matches);
                $counter = isset($matches[1]) ? (int)$matches[1] : 0; // Extract the number or default to 0
                $title = 'Submission-File-' . $counter + 1;
            }
            // Update documents file title
            $user_submission_file_docs = DB::table('user_submission_file_docs')->where('status', 0)->update([
                'file_title' => $title,
                'status' => 1,
            ]);
            if ($user_submission_file_docs == 0) {
                $title = 'Submission-File-' . $counter;
            }

            $data['completed_checklists'] = DB::table('compliance_checklists as cc')
            ->join('user_compliance_checklists as ucc', 'ucc.checklist_id', '=', 'cc.id')
            ->join('user_submission_file_docs as sf_doc', 'cc.id', '=', 'sf_doc.checklist_id')
            ->leftJoin('users as u', 'u.id', '=', 'cc.created_by')
            ->where('cc.status', 1)
            ->where('cc.deleted_at', NULL)
            ->where('ucc.status', 1)
            ->where('ucc.user_id', $userRecord->id)
            ->where('sf_doc.user_id', $userRecord->id)
            ->where('sf_doc.file_title', $title)
            ->select('cc.id', 'cc.slug', 'cc.title', 'ucc.created_at as submitted_on', 'ucc.updated_at as updated_on', 'cc.checklist_meta', 'ucc.checklist_meta as checklist_answer', 'sf_doc.id as sf_doc_id')
            ->get()->toArray();

            $data['checklistRow'] = !empty($data['completed_checklists']) ? $data['completed_checklists'][0] : null;

            $data['userRecord'] = $userRecord;

            $data['documents'] = DB::table('user_documents as d')
            ->join('user_submission_file_docs as sf_doc', 'd.id', '=', 'sf_doc.doc_id')
            ->leftJoin('doc_types as dt', 'd.doc_type_id', '=', 'dt.id')
            ->select('d.id as document_id', 'd.title', 'd.expiry_date', 'dt.doc_name as doc_type_name', 'd.file_name', 'sf_doc.id as sf_doc_id')
            ->where('sf_doc.user_id', $userRecord->id)
            ->where('sf_doc.file_title', $title)
            ->where('d.deleted_at', NULL)
            ->orderBy('d.id', 'desc')
            ->get()
            ->toArray();

            $data['userStateLicense'] = DB::table('user_state_license as d')
            ->select('d.license_name', 'd.location', 'd.license_expiry_date')
            ->where('d.user_id', $userRecord->id)
            ->orderBy('d.id', 'desc')
            ->get()
            ->toArray();

            $data['userActiveCertificate'] = DB::table('user_active_certificates as d')
            ->select('d.certificate_name', 'd.certificate_expiry_date')
            ->where('d.user_id', $userRecord->id)
            ->orderBy('d.id', 'desc')
            ->get()
            ->toArray();

            $data['references'] = DB::table('user_reference_details as d')
            ->leftJoin('user_references as ur', 'd.reference_id', '=', 'ur.id')
            ->select('d.*', 'ur.*')
            ->where('d.user_id', $userRecord->id)
            ->where('ur.is_verify', 1)
            ->orderBy('d.id', 'desc')
            ->get()
            ->toArray();

            $data['user_rto_details'] = DB::table('user_rto_details as rto')
            ->where('rto.user_id', $userRecord->id)
            ->select(
                'rto.*',
                DB::raw("DATE_FORMAT(rto_start_date, '%m/%d/%Y') as rto_start_date"),
                DB::raw("DATE_FORMAT(rto_end_date, '%m/%d/%Y') as rto_end_date")
            )
            ->get()->toArray();
            $html = view('submissionFile', $data)->render();
            $result = array(
                'status' => true,
                'message' => "Submission file retrieved successfully",
                'data' => $html
            );

        } catch(Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }

        return response()->json($result);
    }

    ## Function to get submission file data
    public function submissionFileData($id) 
    {
        try {
            $data['title'] = 'Submission File';

            $userRecord = DB::table('users as u')
                ->join('user_details as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'ud.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'ud.specialty_id', '=', 'sp.id')
                ->select(
                    'u.id',
                    'u.name',
                    'u.email',
                    'u.phone',
                    'p.profession',
                    'sp.specialty',
                    's.name as state_name',
                    'city.city_name',
                    'ud.address_line1',
                    'ud.address_line2',
                    'ud.available_start_date',
                    'ud.total_experience',
                    'ud.specialty_experience',
                    'ud.EMR_experience',
                    'ud.teaching_hospital_experience',
                    'ud.travel_experience',
                    'ud.fully_vaccinated',
                    'u.unique_id'
                )
                ->where('u.deleted_at',
                    NULL
                )
                ->where('u.unique_id', $id)
                ->get()
                ->first();

            if (empty($userRecord)) {
                abort(404, 'Resource not found');
            }

            $data['educationalDetails'] = DB::table('user_educations as ue')
            ->select('ue.*')
            ->where('ue.user_id', $userRecord->id)
            ->whereNull('ue.deleted_at')
                ->orderBy('ue.currently_attending', 'desc')  // currently_attending 1 first
                ->orderBy('ue.end_year', 'desc')            // end_year in descending order
                ->orderBy('ue.end_month', 'desc')
                ->get()->toArray();

            $result = DB::table('user_preferred_shifts as us')
            ->leftJoin('shifts as s', 's.id', '=', 'us.shift_id')
            ->select('s.title')
            ->where('us.user_id', $userRecord->id)->get()->toArray();
            if (!empty($result)) {
                foreach ($result as $val) {
                    $data['desired_shifts'][] = $val->title;
                }
            }

            $data['work_histories'] = DB::table('user_work_histories as wh')
            ->leftJoin('states as s', 'wh.state_id', '=', 's.id')
            ->leftJoin('cities as city', 'wh.city_id', '=', 'city.id')
            ->leftJoin('professions as p', 'wh.profession_id', '=', 'p.id')
            ->leftJoin('specialities as sp', 'wh.specialty_id', '=', 'sp.id')
            ->leftJoin('employment_types as et', 'et.id', '=', 'wh.employment_type_id')
            ->select('wh.id', 'wh.title', 'wh.company_name', 'wh.start_month', 'wh.start_year', 'wh.end_month', 'wh.end_year', 'wh.currently_working', 'wh.state_id', 'wh.city_id', 'wh.profession_id', 'wh.specialty_id', 'wh.employment_type_id', 's.name as state_name', 's.code as state_code', 'city.city_name', 'p.profession', 'sp.specialty', 'et.title as employment_type_title')
            ->where('wh.user_id', $userRecord->id)
            ->where('wh.deleted_at', NULL)
                ->orderBy('wh.id', 'desc')
                ->get()->toArray();


            // Get the latest submission file title from user_submission_file_docs table
            $counter = 1;
            $latestSubmissionFile = DB::table('user_submission_file_docs')
            ->where('user_id', $userRecord->id)
            ->whereNotNull('file_title')
                ->orderby('id', 'DESC')
                ->first();
            if (!$latestSubmissionFile) {
                $title = 'Submission-File-1';
            } else {
                preg_match('/Submission-File-(\d+)$/', $latestSubmissionFile->file_title, $matches);
                $counter = isset($matches[1]) ? (int)$matches[1] : 0; // Extract the number or default to 0
                $title = 'Submission-File-' . $counter + 1;
            }
            // Update documents file title
            $user_submission_file_docs = DB::table('user_submission_file_docs')->where('status', 0)->update([
                'file_title' => $title,
                'status' => 1,
            ]);
            if ($user_submission_file_docs == 0) {
                $title = 'Submission-File-' . $counter;
            }

            $data['completed_checklists'] = DB::table('compliance_checklists as cc')
            ->join('user_compliance_checklists as ucc', 'ucc.checklist_id', '=', 'cc.id')
            ->join('user_submission_file_docs as sf_doc', 'cc.id', '=', 'sf_doc.checklist_id')
            ->leftJoin('users as u', 'u.id', '=',
                'cc.created_by'
            )
            ->where('cc.status', 1)
            ->where('cc.deleted_at', NULL)
            ->where('ucc.status', 1)
            ->where('ucc.user_id', $userRecord->id)
            ->where('sf_doc.user_id', $userRecord->id)
            ->where('sf_doc.file_title', $title)
                ->select('cc.id', 'cc.slug', 'cc.title', 'ucc.created_at as submitted_on', 'ucc.updated_at as updated_on', 'cc.checklist_meta', 'ucc.checklist_meta as checklist_answer', 'sf_doc.id as sf_doc_id')
                ->get()->toArray();

            $data['checklistRow'] = !empty($data['completed_checklists']) ? $data['completed_checklists'][0] : null;

            $data['userRecord'] = $userRecord;

            $fileBaseUrl = "https://staging.travelnurse911.com/frontend/uploads/documents/";
            $data['documents'] = DB::table('user_documents as d')
            ->join('user_submission_file_docs as sf_doc', 'd.id', '=', 'sf_doc.doc_id')
            ->leftJoin('doc_types as dt', 'd.doc_type_id', '=', 'dt.id')
            ->select('d.id as document_id', 'd.title', 'd.expiry_date', 'dt.doc_name as doc_type_name', 'd.file_name', 'sf_doc.id as sf_doc_id')
            ->where('sf_doc.user_id', $userRecord->id)
            ->where('sf_doc.file_title', $title)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get()
                ->map(function ($doc) use ($fileBaseUrl) {
                    $doc->file_url = $fileBaseUrl . $doc->file_name; // Add the file_url to each document
                    return $doc;
                })
                ->toArray();

            $data['userStateLicense'] = DB::table('user_state_license as d')
            ->select('d.license_name', 'd.location', 'd.license_expiry_date')
            ->where('d.user_id', $userRecord->id)
                ->orderBy('d.id', 'desc')
                ->get()
                ->toArray();

            $data['userActiveCertificate'] = DB::table('user_active_certificates as d')
            ->select('d.certificate_name', 'd.certificate_expiry_date')
            ->where('d.user_id', $userRecord->id)
                ->orderBy('d.id', 'desc')
                ->get()
                ->toArray();

            $data['references'] = DB::table('user_reference_details as d')
            ->leftJoin('user_references as ur', 'd.reference_id', '=', 'ur.id')
            ->select('d.*', 'ur.*')
            ->where('d.user_id', $userRecord->id)
                ->where('ur.is_verify', 1)
                ->orderBy('d.id', 'desc')
                ->get()
                ->toArray();

            $data['user_rto_details'] = DB::table('user_rto_details as rto')
            ->where('rto.user_id', $userRecord->id)
            ->select(
                'rto.*',
                DB::raw("DATE_FORMAT(rto_start_date, '%m/%d/%Y') as rto_start_date"),
                DB::raw("DATE_FORMAT(rto_end_date, '%m/%d/%Y') as rto_end_date")
            )
            ->get()->toArray();
            $result = array(
                'status' => true,
                'data' => $data
            );
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }

        return response()->json($result);
    }

    ## Function to store submission file
    public function uploadSubmissionFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'submission_file' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }
        try {

            if ($request->file('submission_file')) {
                $file = $request->file('submission_file');
                $path = config('custom.doc_folder');

                $fileName = 'Submission-File-' . date('Ymd') . time() . '.pdf';
                $upload = $file->move($path, $fileName);      

                if ($upload) {
                    $counter = 1;
                    if(!empty($request->is_checklist)){
                        //$title = "Checklist";
                        $title = "Skill_Checklist_" . $request->checklist_name;
                        $doc_type_id = 13;


                    }else{
                        $doc_type_id = 40;
                        $latestSubmissionFile = DB::table('user_documents')
                                            ->where('user_id', $request->user_id)
                                            ->whereNotNull('title')
                                            ->orderby('id', 'DESC')
                                            ->first();
                        if (!$latestSubmissionFile) {
                            $title = 'Submission-File-1';
                        } else {
                            preg_match('/Submission-File-(\d+)$/', $latestSubmissionFile->title, $matches);
                            $counter = isset($matches[1]) ? (int)$matches[1] : 0; // Extract the number or default to 0
                            $title = 'Submission-File-' . $counter + 1;
                        }
                    }
                
                    // store submission file
                    $param = [
                        'user_id' => $request->user_id,
                        'Title' => $title,
                        'file_name' => $fileName,
                        'file_type' => 'pdf',
                        'doc_type_id' => $doc_type_id,
                        'created_by' => $request->user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('user_documents')->insert($param);
                    $result = array('status' => true, 'message' => "Success");
                } else {
                    $result = array('status' => false, 'message' => "Something went wrong!");
                }
            } else {
                $result = array('status' => false, 'message' => "Something went wrong!");
            }

        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }

        return response()->json($result);
    }
    
   
}
