<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;
use DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class UserActivityController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to get user activities
    public function getUserActivities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $role = DB::table('user_roles')->where('role', $request->role)->first();
            if(empty($role)) {
                $result = array('status' => false, 'message' => "No role found");
                return response()->json($result);
            }

            $activities = DB::table('user_activities as a')
                ->join('users as u', 'a.user_id', '=', 'u.id')
                ->join('user_roles as ur', 'u.role_id', '=', 'ur.id')
                ->select('a.id', 'a.description', 'a.created_at', 'a.updated_at', 'u.name','u.unique_id')
                ->where('a.deleted_at', NULL)
                ->where('ur.id', $role->id);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                 $activities->where(function ($query) use ($request) {
                    $query->where('a.description', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('u.name', 'LIKE', "%{$request->keyword}%");
                });
            } 
            if ((isset($request->start_date) && !empty($request->start_date)) || isset($request->end_date) && !empty($request->end_date)) {
                if(isset($request->start_date) && !empty($request->start_date))
                    $activities->where('a.created_at', '>=', $request->start_date);
                    
                if(isset($request->end_date) && !empty($request->end_date))
                    $activities->where('a.created_at', '<=', $request->end_date);
            }  
            
                $activities = $activities->orderBy('a.id', 'desc')->get()->toArray();

            $result = array('status' => true, 'message' => (count($activities)) . " Record found", 'data' => $activities);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## Function to get contact entries
    public function getContactEnquiries(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            
            $activities = DB::table('contact_enquiries as ce')
                ->select('ce.*')
                ->where('ce.deleted_at', NULL);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                 $activities->where(function ($query) use ($request) {
                    $query->where('ce.subject', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('ce.message', 'LIKE', "%{$request->keyword}%");
                });
            } 
            if ((isset($request->start_date) && !empty($request->start_date)) || isset($request->end_date) && !empty($request->end_date)) {
                if(isset($request->start_date) && !empty($request->start_date))
                    $activities->where('ce.created_at', '>=', $request->start_date);
                    
                if(isset($request->end_date) && !empty($request->end_date))
                    $activities->where('ce.created_at', '<=', $request->end_date);
            }  
            
                $activities = $activities->orderBy('ce.id', 'desc')->get()->toArray();

            $result = array('status' => true, 'message' => (count($activities)) . " Record found", 'data' => $activities);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

}
