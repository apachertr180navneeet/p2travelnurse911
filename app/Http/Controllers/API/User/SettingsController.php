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

class SettingsController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to update settings
    public function updateSettings(Request $request)
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

            $info = [
                'enable_sms_notification' => $request->smsNotifications,
                /*'enable_message_notification' => $request->messageNotifications,*/
                'enable_message_notification' => ($request->searchable_profile) ? $request->searchable_profile : 0,
                'searchable_profile' => ($request->searchable_profile) ? $request->searchable_profile : 0,
                'updated_at' => $this->entryDate,
            ];
            DB::table('user_details')->where('user_id', $request->user_id)->update($info);

            DB::commit();
            $result = array('status' => true, 'message' => "Settings has been successfully updated");
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get settings
    public function getSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $user = DB::table('user_details as ud')
                ->select(
                    'ud.enable_sms_notification as smsNotifications',
                    'ud.enable_message_notification as messageNotifications',
                    'ud.searchable_profile'
                )
                ->where('ud.user_id', $request->user_id)->get()->first();
            /*
            $data = array(
                'smsNotifications' => $user->smsNotifications,
            );
            */

            $result = array('status' => true, 'message' => "Record fetched", 'data' => $user);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}
