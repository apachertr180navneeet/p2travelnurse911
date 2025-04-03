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

class AuthController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Register/Signup Function
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'company_name' => 'required',
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
            $query = CommonFunction::checkEmailExist($request->email, $request->role_id);
            if ($query) {
                $result = array('status' => false, 'message' => "Email is already registered");
                return response()->json($result);
            }

            # Create the user
            $user = new User;
            $user->name = strip_tags($request->name);
            $user->email = strip_tags($request->email);
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role_id;
            $user->created_at = $this->entryDate;
            $user->save();

            if ($user->id) {
                # Create entry in user_details
                $param = [
                    'user_id' => $user->id,
                    'company_name' => strip_tags($request->company_name),
                    'created_at' => $this->entryDate
                ];
                DB::table('clients')->insert($param);

                # Call the mail verification
                $code = self::sendVerificationCode($user);

                $data = array(
                    'user_id' => $user->id,
                    'email' => $request->email,
                    'verify_email' => '1'
                );

                DB::commit();
                $result = array('status' => true, 'message' => "Your account has been successfully created", 'data' => $data);
            } else {
                $result = array('status' => false, 'message' => "Something went wrong. Please try again");
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Resend verification mail Function
    public function resendVerifyEmail(Request $request)
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

            $user = User::where('id', $request->user_id)->first();
            if (!empty($user)) {

                # Delete previous entries
                DB::table('verify_users')->where(['user_id' => $user->id])->delete();

                # Call the mail verification
                $code = self::sendVerificationCode($user);

                $data = array(
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'verify_email' => '1'
                );

                DB::commit();
                $result = array('status' => true, 'message' => "Verification code has been sent successfully", 'data' => $data);
            } else {
                $result = array('status' => false, 'message' => "Something went wrong. Please try again");
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Verify email Function
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            # Check for the verification code
            $user_id = $request->user_id;
            $raw = DB::table('verify_users as vu')
                ->join('users as u', 'vu.user_id', '=', 'u.id')
                ->select('vu.id', 'u.*')
                ->where(['vu.user_id' => $user_id, 'vu.verification_type' => 'email', 'vu.verification_token' => $request->code])->first();

            if (!empty($raw)) {

                # Update the user status to active
                $user = User::find($user_id);
                $param = [
                    'status' => 1,
                    'updated_at' => $this->entryDate
                ];
                User::where('id', $user_id)->update($param);

                if (!empty($user->profile_pic)) {
                    $user->profile_pic = url(config('custom.user_folder') . $user->profile_pic);
                }

                # Generate login token
                $user->token = $token = CommonFunction::createLoginToken($user_id, 'web');
                
                # Convert the user data in base_64 (twice)
                $token = base64_encode(base64_encode($user));
                $data = ['token' => $token, 'token_type' => 'Bearer'];

                # Delete all verification codes
                DB::table('verify_users')->where(['user_id' => $user_id])->delete();

                DB::commit();
                $result = array('status' => true, 'message' => "Verification successful", 'data' => $data);
            } else {
                $result = array('status' => false, 'message' => "Invalid verification code");
            }
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
            //'role_id' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => 'Validation error occurred');
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            // Add 'deleted_at' condition in the query to ensure the user has not been soft deleted
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
                //'role_id' => $request->role_id,
                'deleted_at' => null // Make sure the user is not soft deleted
            ];
    
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if (!in_array($user->role_id, [2,3,6])) {
                    $result = array('status' => false, 'message' => 'Invalid login credentials');
                    return response()->json($result);
                }

                if ($user->status == 2) {
                    $result = array('status' => false, 'message' => 'Your account is deactivated! You can contact to support');
                } else if ($user->status == 0) {

                    # Call the mail verification
                    $code = self::sendVerificationCode($user);
                    $data = array(
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'verify_email' => '1'
                    );

                    DB::commit();
                    $result = array('status' => true, 'message' => "Verification code has been sent to email", 'data' => $data);
                } else {
                    if (!empty($user->profile_pic)) {
                        $user->profile_pic = url(config('custom.user_folder') . $user->profile_pic);
                    }
                    
                    $createdByRow = DB::table('users')->select('role_id','id')->where('id', $user->created_by)->first();
                    if($createdByRow)
                    {
                        $user->created_by_role_id = $createdByRow->role_id;
                    }
                    
                    if($user->role_id == 6)
                    {
                        $clientRow = DB::table('clients')->select('id')->where('user_id', $user->created_by)->first();
                        if($clientRow)
                        {
                            $user->client_id = $clientRow->id;
                        }
                    }
                    else
                    {
                        $clientRow = DB::table('clients')->select('id')->where('user_id', $user->id)->first();
                        if($clientRow)
                        {
                            $user->client_id = $clientRow->id;
                        }
                    }
                    
                    $user->token = $token = CommonFunction::createLoginToken($user->id, 'web');
                    
                    # Convert the user data in base_64 (twice)
                    $token = base64_encode(base64_encode($user));
                    $data = ['token' => $token, 'token_type' => 'Bearer'];

                    DB::commit();
                    $result = array('status' => true, 'message' => 'Login successful', 'data' => $data);
                }
            } else {
                $result = array('status' => false, 'message' => 'Invalid login credentials');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }

        /*$result['app_version'] = CommonFunction::driverAppVersion();*/
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

        $admin_email = DB::table('app_settings')
            ->select('field_value')
            ->where(['field_name' => 'admin_email'])
            ->where('field_value', '!=', NULL)
            ->first();

        $admin_phone = DB::table('app_settings')
            ->select('field_value')
            ->where(['field_name' => 'admin_phone'])
            ->where('field_value', '!=', NULL)
            ->first();

        $result = array('status' => false, 'message' => "Please contact the admin at " . $admin_email->field_value . " or call the " . $admin_phone->field_value . " to reset your password.");
        return response()->json($result);
        
        try {
            DB::beginTransaction();

            $user = User::select('id', 'name', 'email', 'status')
                ->where(['email' => $request->email])->first(); /* , 'role_id' => $this->role_id */
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
            'token' => 'required',
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
            $token = $request->token;

            // User status & Token validation
            $result = CommonFunction::checkUserTokenExist($user_id, $token, 'app');
            if (!empty($result)) {
                return response()->json($result);
            }

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
            'token' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => 'Validation error occurred');
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $user_id = $request->user_id;
            $token = $request->token;
            # User status & Token validation
            $result = CommonFunction::checkUserTokenExist($user_id, $token, 'app');
            if (!empty($result)) {
                return response()->json($result);
            }

            $logout = DB::table('login_tokens')->where(['user_id' => $user_id])->delete();
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
