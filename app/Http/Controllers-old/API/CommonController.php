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
            ->orderBy('title')->get()->toArray();

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
            $users = DB::table('users as u')
                ->select('u.id', 'u.name')
                ->where('u.role_id', $role_id)
                ->where('u.deleted_at', NULL)
                ->orderBy('u.name')->get()->toArray();

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
    
    
    
   
}
