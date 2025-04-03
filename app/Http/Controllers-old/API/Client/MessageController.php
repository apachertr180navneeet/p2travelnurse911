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
use App\Services\TwilioService;

class MessageController extends Controller
{
    private $entryDate;
    protected $twilioService;
    public function __construct(TwilioService $twilioService)
    {
        $this->entryDate = date("Y-m-d H:i:s");
        $this->twilioService = $twilioService;
    }

    ## Function to Send Message
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_id' => 'required',
            'to_id' => 'required',
            'type' => 'required|in:"sms","email"',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = [
                'from_id' => $request->from_id,
                'to_id' => $request->to_id,
                'type' => $request->type,
                'message' => $request->message,
                'created_at' => $this->entryDate
            ];
            DB::table('messages')->insert($param);

            ## To do - email delivery and SMS API
            if(strtolower($request->type) == 'email') {
                $user = DB::table('users')->select('name', 'email')->where('id', $request->to_id)->first();
                $param = array(
                    'receiver_name' => $user->name,
                    'description' => $request->message,
                    'email' => $user->email
                );

                Mail::send('emails.user.message', $param, function ($message) use ($param) {
                    $message->subject(config('custom.new_message'));
                    $message->to($param['email']);
                });
                $result = array('status' => true, 'message' => ucwords($request->type) ." has been sent successfully");
            }
            else if(strtolower($request->type) == 'sms') {
                $user = DB::table('users')->select('name', 'phone')->where('id', $request->to_id)->first();
                
                $this->twilioService->sendSms($user->phone, $request->message);
                /*
                $response = 
                if($response->status == 'sent')
                    $result = array('status' => true, 'message' => ucwords($request->type) ." has been sent successfully");
                else 
                    $result = array('status' => false, 'message' => "An error occured while sending sms.");
                */
                $result = array('status' => true, 'message' => ucwords($request->type) ." has been sent successfully");
            }

            DB::commit();
            
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get messages
    public function getMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type' => 'required|in:"sms","email"',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $query = DB::table('messages as m')
                ->join('users as u', 'm.from_id', '=', 'u.id')
                ->join('users as u2', 'm.to_id', '=', 'u2.id')
                ->select('m.id', 'm.type', 'm.message', 'm.from_id', 'm.to_id', 'm.created_at', 'm.updated_at', 'u.name as from_name', 'u2.name as to_name','u2.unique_id as to_unique_id')
                ->where('m.type', $request->type)
                ->where('m.deleted_at', NULL)
                ->where('m.from_id',$request->user_id);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                 $query->where(function ($query) use ($request) {
                    $query->where('u2.name', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('u.name', 'LIKE', "%{$request->keyword}%")
                          ->orWhere('m.message', 'LIKE', "%{$request->keyword}%");
                });
            } 
            if ((isset($request->start_date) && !empty($request->start_date)) || isset($request->end_date) && !empty($request->end_date)) {
                if(isset($request->start_date) && !empty($request->start_date))
                    $query->where('m.created_at', '>=', $request->start_date);
                    
                if(isset($request->end_date) && !empty($request->end_date))
                    $query->where('m.created_at', '<=', $request->end_date);
            }  
            
            $messages = $query->orderBy('m.id', 'desc')->get()->toArray();

            $result = array('status' => true, 'message' => (count($messages)) . " Record found", 'data' => $messages);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}
