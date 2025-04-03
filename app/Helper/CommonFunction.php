<?php

namespace App\Helper;

use DB;
use Carbon\Carbon;
use Session;
use App;
use File;
use Lang;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
/*
use Yandex\Translate\Translator;
use Yandex\Translate\Exception;
*/

use YandexTranslate;

class CommonFunction
{
    
    ## Function to return greeting text
    public static function time_based_greeting() {
        $hour = date('H');

        if ($hour >= 5 && $hour < 12) {
            return 'Good Morning';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'Good Afternoon';
        } else {
            return 'Good Evening';
        }
    }

    //Check Email is registered or not with user type
    public static function checkEmailExist($email, $role_id,$id = null)
    {
        $row = DB::table('users')
            ->select('id')
            /*->where('deleted_at', NULL)*/
            ->where('email', $email)
            ->where('is_login_allowed',1);
        
        if (is_array($role_id)) {
            $row->whereIn('role_id', $role_id);
        } else {
            $row->where('role_id', $role_id);
        }
        
        if($id != null)
        {
            $row->where('id','!=',$id);
        }
            $row = $row->first();
            
        return $row;
    }

    public static function getRolebyRoleID($role_id)
    {
        $roleResult = DB::table('user_roles')
            ->select('role')
            ->where('deleted_at', NULL)
            ->where(['id' => $role_id])
            ->first();
            
        if(!empty($roleResult))
            return $roleResult->role;
        else
            return 'User';
    }

    // Function is used to create login token - one user can login to multiple devices
    public static function createLoginToken($user_id, $platform = null)
    {
        $length = 60;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = substr(str_shuffle($characters), 0, $length);

        $param = array(
            'user_id' => $user_id,
            'token' => $token,
            /*'platform' => $platform,*/
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $query = DB::table('login_tokens')->insertGetId($param);

        return ($query) ? $token : false;
    }

    //check user id and token exist or not
    public static function checkUserTokenExist($user_id, $token, $platform)
    {
        $result = array();
        /*if($platform != 'web') {
            $row = DB::table('users')->select('status', 'is_deleted')->where(['id' => $user_id])->first();
            if(!empty($row)){
                if($row->is_deleted == 1){
                    $result = array('status' => 404, 'message' => "Account deleted! You can not use the app.");
                }
                else if($row->status == 2){
                    $result = array('status' => 404, 'message' => "Account deactivated! You can contact to support.");
                }
                else{
                    $token_row = DB::table('login_tokens')->select('*')->where(['user_id' => $user_id, 'token'=> $token, 'platform' => $platform])->first();
                    if(empty($token_row)){
                        $result = array('status' => 404, 'message' => "Session expired or you might have logged in on another device");
                    }
                }
            }
            else{
                $result = array('status' => 404, 'message' => "Session expired or you might have logged in on another device");
            }
        }*/
        return $result;
    }

    //generate unique code for 2fa verification
    public static function generateVerificationToken()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $token = substr(str_shuffle($characters), 0, 50);
        return $token;
    }
    
    // Funtion to create directory, if not exist
    public static function createDirectory($path)
    {
        if (!\File::exists($path)) {
            \File::makeDirectory($path, $mode = 0777, true, true);
        }
        return $path;
    }

    public static function getTranslatedText($str)
    {
        /*
        $key = 'trnsl.1.1.20221110T120743Z.5bed07a5e801f257.7cc7f03a7042e408ad3c5f2624b1db077d97e941';
        $translator = new Translator($key);
        $curLang = App::getLocale();
        $translator->translate($str, $curLang);
        echo '<pre>';
        print_r($translator);
        */
        $curLang = App::getLocale();
        ob_start();
        echo YandexTranslate::translate($str, false, $curLang, true);
        $output = ob_get_contents();
        ob_clean();
        return $output;
    }

    public static function __lang($str)
    {
        //$str = htmlentities($str);
        $curLang = App::getLocale();
        $updateLangFile = true;
        if ($updateLangFile && $curLang != 'en') {
            //$languages = File::directories(base_path() . '/resources/lang/');

            $dirPath = base_path() . '/resources/lang/' . $curLang . '.json';

            if (!file_exists($dirPath)) {
                File::put($dirPath, json_encode(array()));
            }


            if (file_exists($dirPath)) {

                $fileMetaArray = json_decode(implode(file($dirPath)), true);
                //$fileMeta = File::getRequire($dirPath);
                //$fileMetaArray = json_decode($fileMeta, true);
                if ((!empty($fileMetaArray) && !array_key_exists($str, $fileMetaArray)) || empty($fileMetaArray)) {
                    $fileMetaArray[$str] = self::getTranslatedText($str);
                    File::put($dirPath, json_encode($fileMetaArray));
                }
            }
        }

        //return __(html_entity_decode($str));
        return __($str);
    }

    public static function get_language_code()
    {
        $session_code = Session::get('curLangCode');
        if (empty($session_code))
            $session_code = 'en';
        return $session_code;
    }


    // Function to generate subdomain slug
    public static function generateUrlSlug($table, $field, $string, $id = NULL)
    {
        $string = strtolower(str_replace(' ', '-', $string));
        $url_slug = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        if ($id != NULL) {
            $count = count(DB::table($table)->select('id')->where([$field => $url_slug])->where('id', '!=', $id)->get());
        } else {
            $count = count(DB::table($table)->select('id')->where([$field => $url_slug])->get());
        }

        if ($count == 0) {
            return $url_slug;
        } else {
            //return $url_slug."-".(++$count);
            $subdomain = self::generateNewUrlSlug($url_slug, ++$count);
            return $subdomain;
        }
    }


    public static function encryptId($id)
    {
        return Crypt::encrypt($id);
        /*
        $timestamp = time();
        $randomKey = rand(1,9999);

        return base64_encode($timestamp . $randomKey . $id);
        */
    }

    public static function decryptId($id)
    {
        return Crypt::decrypt($id);
        //return base64_decode($id);
    }

    //Funciton to generate unique application id
    public static function generateApplicationID()
    {
        $code = substr(str_shuffle('1234567980ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 12);
        $exist = DB::table('visa_applications')->select('id')->where('unique_id', $code)->get();
        if (count($exist) > 0) {
            $code = self::generateApplicationID();
            return $code;
        } else {
            return $code;
        }
    }

    // date format convert function
    public static function changeDateFormat($inputDate,$formatIndex = 0) {
        try {
            $format = ['Y-m-d','m/d/Y'];
            $datetime = \DateTime::createFromFormat('m/d/Y', $inputDate) ?: // Handle mm/dd/yyyy
                        \DateTime::createFromFormat('m-d-Y', $inputDate) ?: // Handle mm-dd-yyyy
                        \DateTime::createFromFormat('M d, Y', $inputDate) ?: //
                        \DateTime::createFromFormat('Y-m-d', $inputDate) ?:false; // Handle yyyy-mm-dd
            if ($datetime) {
                return $datetime->format($format[$formatIndex]);
            }
            return $datetime;
        } catch (\Exception $e) {
            // Handle invalid date formats gracefully
            return null;
        }        
    }

}
