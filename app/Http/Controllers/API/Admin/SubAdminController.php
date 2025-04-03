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

class SubAdminController extends Controller
{
    private $entryDate;
    private $roleID;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
        $this->roleID = 7;
    }
    
    ## Function to get all sub admins
    public function getSubAdmins(Request $request)
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
            $users = DB::table('users as u')
                ->select('u.id', 'u.unique_id', 'u.name','u.email','u.phone','u.status','u.created_at')
                ->where('u.created_by', $request->user_id)
                ->where('u.deleted_at', NULL)
                ->where('u.role_id',$this->roleID);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $users->where('u.name', 'LIKE', "%{$request->keyword}%");
            }
            
                $users = $users->get()->toArray();
            
            DB::commit();
            $result = array('status' => true, 'message' => (count($users)) . " Record found", 'data' => $users);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
   
    ## Function to add/update sub admin
    public function updateSubAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            if (isset($request->id) && !empty($request->id)) {
                
                $param = array(
                    'name' => strip_tags($request->name),
                    'email' => strip_tags($request->email),
                    'phone' => strip_tags($request->phone),
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                );
                if(isset($request->password) && !empty($request->password))
                {
                 $param['password'] = Hash::make($request->password);   
                }
                DB::table('users')->where('id', $request->id)->update($param);
                
                 DB::commit();
                $result = array('status' => true, 'message' => "Sub Admin updated successfully");
            }
            else{
                # Check Email is already registered or not or not
                $query = CommonFunction::checkEmailExist($request->email, $this->roleID);
                if ($query) {
                    $result = array('status' => false, 'message' => "Email is already registered");
                    return response()->json($result);
                }
    
                # Create the user
                $user = new User;
                $user->unique_id = $this->generateUniqueCode(8);
                $user->name = strip_tags($request->name);
                $user->email = strip_tags($request->email);
                $user->phone = strip_tags($request->phone);
                $user->password = Hash::make($request->password);
                $user->role_id = $this->roleID;
                $user->created_at = $this->entryDate;
                $user->created_by = $request->user_id;
                $user->status = 1;
                $user->save();
                
                if ($user->id) {
                     DB::commit();
                     
                    $param = array(
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => $request->password,
                        'login_url' => 'https://staging.orionallied.com/tn-dashboard/admin/login',
                    );
        
                    Mail::send('emails.admin.sub-admin-register', $param, function ($message) use ($param) {
                        $message->subject(config('custom.sub_admin_register'));
                        $message->to($param['email']);
                    });
                     
                    $result = array('status' => true, 'message' => "Sub Admin added successfully");
                } else {
                    $result = array('status' => false, 'message' => "Something went wrong. Please try again");
                }
            }

           
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete sub admin
    public function deleteSubAdmin(Request $request)
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
                DB::table('users')->where('id', $request->id)->update($param);
                $msg = "Sub Admin has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update sub admin's status
    
    public function updateSubAdminStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                
                $param = array(
                    'status' => $request->status,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    );
                DB::table('users')->where('id', $request->id)->update($param);
                $msg = "Sub Admin status has been been successfully updated";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     public function generateUniqueCode(int $codeLength)
	{
        $characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);

        $code = '';

        while (strlen($code) < $codeLength) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;
        }

        if (User::where('unique_id', $code)->exists()) {
            $this->generateUniqueCode($codeLength);
        }

        return $code;
	}
    
}