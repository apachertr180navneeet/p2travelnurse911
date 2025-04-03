<?php

namespace App\Http\Controllers\API\Admin;

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

class AuthController extends Controller
{
    private $entryDate;
    private $role_id;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
        $this->role_id = 1;
    }

    ## Register/Signup Function
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            # Check Email is already registered or not or not
            $query = CommonFunction::checkEmailExist($request->email, $this->role_id);
            if ($query) {
                $result = array('status' => false, 'message' => "Email is already registered");
                return response()->json($result);
            }

            # Create the user
            $user = new User;
            $user->name = strip_tags($request->name);
            $user->email = strip_tags($request->email);
            $user->password = Hash::make($request->password);
            $user->role_id = $this->role_id;
            $user->status = 1;
            $user->created_at = $this->entryDate;
            $user->save();

            DB::commit();
            $result = array('status' => true, 'message' => "Your account has been successfully created");

        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to Login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => 'Validation error occurred');
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) { /* , 'role_id' => $this->role_id */

                $user = Auth::user();
                
                if ($user->role_id == 1 || $user->role_id == 7) {
                    if ($user->status == 2) {
                        $result = array('status' => false, 'message' => 'Your account is deactivated! You can contact to support');
                    }
                    else {
                        if (!empty($user->profile_pic)) {
                            $user->profile_pic = url(config('custom.user_folder') . $user->profile_pic);
                        }
    
                        $user->token = $token = CommonFunction::createLoginToken($user->id, 'web');
    
                        # Convert the user data in base_64 (twice)
                        $token = base64_encode(base64_encode($user));
                        $data = ['token' => $token, 'token_type' => 'Bearer'];
    
                        DB::commit();
                        $result = array('status' => true, 'message' => 'Login successful', 'data' => $data);
                    }
                }
                else {
                    $result = array('status' => false, 'message' => 'Invalid login credentials');
                }
            } else {
                $result = array('status' => false, 'message' => 'Invalid login credentials');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }

        return response()->json($result);
    }

    ## This function is used for forgot password
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $user = User::select('id', 'name', 'email', 'status')
                ->where(['email' => $request->email, 'role_id' => $this->role_id])->first();
            $verifyType = "email";

            if (!empty($user)) {
                if ($user->status == 2) {
                    $result = array('status' => false, 'message' => 'Your account is deactivated! You can contact to support');
                    return response()->json($result);
                }

                # Delete previous request
                DB::table('verify_users')->where(['user_id' => $user->id])->delete();

                # Entry of code in DB
                $code = substr(str_shuffle('1234567980'), 0, 4);
                $param = array(
                    'user_id' => $user->id,
                    'verification_token' => $code,
                    'verification_type' => $verifyType,
                    'created_at' => $this->entryDate,
                    'updated_at' => $this->entryDate,
                );
                $last_id = DB::table('verify_users')->insertGetId($param);

                if ($last_id) {
                    $param = array(
                        'name' => $user->name,
                        'code' => $code,
                        'email' => $user->email
                    );

                    Mail::send('emails.user.reset-password', $param, function ($message) use ($param) {
                        $message->subject(config('custom.reset_password'));
                        $message->to($param['email']);
                    });

                    DB::commit();
                    $data = array(
                        'user_id' => $user->id,
                    );
                    $result = array('status' => true, 'message' => 'Reset code has been sent to your email, please check your inbox', 'data' => $data);
                } else {
                    $result = array('status' => false, 'message' => 'Something went wrong, please try again later');
                }
            } else {
                $result = array('status' => false, 'message' => "No account found with this email");
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    // Function to reset password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'code' => 'required',
            'new_pass' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => 'Validation error occurred');
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $row = DB::table('verify_users')->select('*')
                ->where(['verification_token' => $request->code, 'verification_type' => 'email', 'user_id' => $request->user_id])
                ->first();

            if (!empty($row)) {
                $data = array(
                    'password' => bcrypt($request->new_pass),
                    'updated_at' => $this->entryDate
                );
                User::where('id', $request->user_id)->update($data);

                # Delete previous codes
                DB::table('verify_users')->where(['user_id' => $request->user_id])->delete();

                DB::commit();
                $result = array('status' => true, 'message' => 'Password has been changed successfully');
            } else {
                $result = array('status' => false, 'message' => 'Invalid reset code');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    //Funciton to Change password
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'old_pass' => 'required',
            'new_pass' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => 'Validation error occurred');
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $user_id = $request->user_id;

            if (Auth::attempt(['id' => request('user_id'), 'password' => request('old_pass')])) {
                if ($request->old_pass != $request->new_pass) {
                    $data = array('password' => bcrypt($request->new_pass), 'updated_at' => $this->entryDate);
                    User::where('id', $user_id)->update($data);
                    DB::commit();
                    $result = array('status' => true, 'message' => 'Password has been changed successfully');
                } else {
                    $result = array('status' => false, 'message' => 'New password should be different from old password');
                }
            } else {
                $result = array('status' => false, 'message' => 'Current password does not match');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    //Funciton to Logout
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => 'Validation error occurred');
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            ## Convert the token
            $user_id = $request->user_id;
            $token = $request->bearerToken();
            $object = json_decode(base64_decode(base64_decode($token)));
            $login_token = $object->token;

            $logout = DB::table('login_tokens')->where(['user_id' => $user_id, 'token' => $login_token])->delete();
            return response()->json(['status' => true, 'message' => 'Logout successful']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    public static function sendVerificationCode($user)
    {
        try {
            // Generate a new verification code
            $token = substr(str_shuffle('1234567980'), 0, 4);

            //Email Verification
            $param = array(
                'user_id' => $user->id,
                'verification_token' => $token,
                'verification_type' => 'email',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
            DB::table('verify_users')->insertGetId($param);

            $param = array(
                'name' => $user->name,
                'token' => $token,
                'email' => $user->email,
            );

            Mail::send('emails.user.verify-email', $param, function ($message) use ($param) {
                $message->subject(config('custom.verify_subject'));
                $message->to($param['email']);
            });

            return $token;
        } catch (Exception $e) {
            return array('status' => false, 'message' => $e->getMessage());
        }
    }
}
