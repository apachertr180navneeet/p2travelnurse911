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

class UserRoleController extends Controller
{
    private $entryDate;
    private $table;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
        $this->table = 'user_roles';
    }

    ## Function to get user roles
    public function getUserRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $roles = DB::table($this->table)->where('status',1)->orderBy('role')->get()->toArray();
            
            // Define the custom entry you want to add
            $customEntry = [
                'id' => '0', // Assign a unique ID or some identifier
                'role' => 'Facility Compliance List',
                'created_at' => now(), // Use current date and time
                'updated_at' => now(),
                'created_by' => '1', // Example value, replace as needed
                'updated_by' => '1' // Example value, replace as needed
            ];
            $roles[] = (object) $customEntry;
            
            // Define the custom entry you want to add
            $customEntry = [
                'id' => '10', // Assign a unique ID or some identifier
                'role' => 'Front End',
                'created_at' => now(), // Use current date and time
                'updated_at' => now(),
                'created_by' => '1', // Example value, replace as needed
                'updated_by' => '1' // Example value, replace as needed
            ];
            
            // Add the custom entry to the roles array
            $roles[] = (object) $customEntry; // Convert array to object to match the existing array structure
    
            
            $result = array('status' => true, 'message' => (count($roles)) . " Record found", 'data' => $roles);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to add/edit user role
    public function addEditRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'role' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            if (isset($request->id) && !empty($request->id)) {
                # Check Record is unique or not
                $condition = ['role' => $request->role];
                $query = CommonFunction::isRecordUnique($this->table, $condition, $request->id);
                if ($query) {
                    $result = array('status' => false, 'message' => "Role is already registered");
                    return response()->json($result);
                }

                $param = array(
                    'role' => strip_tags($request->role),
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                );
                DB::table($this->table)->where('id', $request->id)->update($param);
                DB::commit();
                $result = array('status' => true, 'message' => "Role has been updated successfully");
            }
            else{
                # Check Record is unique or not
                $condition = ['role' => $request->role];
                $query = CommonFunction::isRecordUnique($this->table, $condition);
                if ($query) {
                    $result = array('status' => false, 'message' => "Role is already registered");
                    return response()->json($result);
                }

                $param = [
                    'role' => strip_tags($request->role),
                    'created_at' => $this->entryDate,
                    'created_by' => $request->user_id
                ];
                DB::table($this->table)->insert($param);
                DB::commit();
                $result = array('status' => true, 'message' => "Role has been created successfully");
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

}
