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
            
            if($request->to_id == 'all')
            {
                $users = DB::table('users as u')
                    ->select(
                        'u.id',
                        'u.name',
                        'u.email',
                        'u.phone'
                    )
                    ->where('u.role_id', $request->role_id)
                    ->whereNull('u.deleted_at')
                    ->where('u.status', 1)
                    ->orderBy('u.id', 'desc')
                    ->get()
                    ->toArray();
                if($users)
                {
                    $total_users = count($users);
                    $disabled_notifications = 0;
                    DB::beginTransaction();
                    $emails = array();
                    $phones = array();
                    foreach ($users as $user) {
                        
                        if($request->type == 'sms')
                        {
                            $disableSmsNotif = DB::table('user_details')->select('id')->where('user_id', $user->id)->where('enable_sms_notification' ,1)->first();
                            if(!$disableSmsNotif)
                            {
                                $disabled_notifications++;
                                continue;
                            }
                        }
                        /*
                        else if($request->type == 'email')
                        {
                            $disableMessageNotif = DB::table('user_details')->select('id')->where('user_id', $user->id)->where('enable_message_notification' ,1)->first();
                            if(!$disableMessageNotif)
                            {
                                $disabled_notifications++;
                                continue;
                            }
                        }
                        */
                        
                        $emails[] = $user->email;
                        
                        if(!empty($user->phone))
                            $phones[] = $user->phone;
                    }
                    
                    
                    
                    
                    
                    
                    
                    ## To do - email delivery and SMS API
                    if(strtolower($request->type) == 'email') {
                        
                        if(empty($emails))
                        {
                            $result = array(
                                'status' => false,
                                'message' => ucwords($request->type) . " has not been sent because {$disabled_notifications} user(s) have disabled their " . ucwords($request->type) . " notifications."
                            );
                        }
                        
                        $param = [
                            'from_id' => $request->from_id,
                            'to_id' => 'all',
                            'type' => $request->type,
                            'message' => $request->message,
                            'created_at' => $this->entryDate
                        ];
                        DB::table('messages')->insert($param);
                        
                        $senderInfo = DB::table('users')->select('name')->where('id', $request->from_id)->first();
                        
                        $param = [
                        'sender_name' => $senderInfo->name,
                        'description' => $request->message
                        ];
                        
                        Mail::send('emails.common.message', $param, function ($message) use ($emails,$param) {
                            $message->subject(config('custom.new_message').' '.$param['sender_name']);
                            $message->bcc($emails);
                        });
 
                        /*
                        
                        $param = array(
                            'receiver_name' => $user->name,
                            'description' => $request->message,
                            'email' => $user->email
                        );
                        Mail::send('emails.common.message', $param, function ($message) use ($param) {
                            $message->subject(config('custom.new_message'));
                            $message->to($param['email']);
                        });
                        
                        $result = array('status' => true, 'message' => ucwords($request->type) ." has been sent successfully");
                        */
                        
                        
                        if ($disabled_notifications > 0) {
                            $result = array(
                                'status' => true,
                                'message' => ucwords($request->type) . " has been sent successfully to " . count($emails) . " user(s), and {$disabled_notifications} user(s) have disabled their " . ucwords($request->type) . " notification"
                            );
                        } else {
                            $result = array(
                                'status' => true,
                                'message' => ucwords($request->type) . " has been sent successfully to " . count($emails) . " user(s)"
                            );
                        }
                        
                    }
                    else if(strtolower($request->type) == 'sms' ) {
                        
                        if(empty($phones))
                        {
                            $result = array(
                                'status' => false,
                                'message' => ucwords($request->type) . " has not been sent because {$disabled_notifications} user(s) have disabled their " . ucwords($request->type) . " notifications."
                            );
                        }
                        
                        $param = [
                            'from_id' => $request->from_id,
                            'to_id' => 'all',
                            'type' => $request->type,
                            'message' => $request->message,
                            'created_at' => $this->entryDate
                        ];
                        DB::table('messages')->insert($param);
                        
                        foreach($phones as $phone)
                        {
                            $this->twilioService->sendSms($phone, $request->message);    
                        }
                        
                        
                        if ($disabled_notifications > 0) {
                            $result = array(
                                'status' => true,
                                'message' => ucwords($request->type) . " has been sent successfully to " . count($phones) . " user(s), and {$disabled_notifications} user(s) have disabled their " . ucwords($request->type) . " notification"
                            );
                        } else {
                            $result = array(
                                'status' => true,
                                'message' => ucwords($request->type) . " has been sent successfully to " . count($phones) . " user(s)"
                            );
                        }
                    }
                    
                    /*
                    if ($total_users == $successfully_send) {
                        $result = array(
                            'status' => true,
                            'message' => ucwords($request->type) . " has been sent successfully to {$successfully_send} user(s)"
                        );
                    } else if ($disabled_notifications > 0) {
                        $result = array(
                            'status' => true,
                            'message' => ucwords($request->type) . " has been sent successfully to {$successfully_send} user(s), and {$disabled_notifications} user(s) have disabled their " . ucwords($request->type) . " notification"
                        );
                    } else {
                        $result = array(
                            'status' => true,
                            'message' => ucwords($request->type) . " has been sent successfully to {$successfully_send} user(s)"
                        );
                    }
                    */
                    
                    
                    
                }
            }
            else
            {
                if($request->type == 'sms')
                {
                    $disableSmsNotif = DB::table('user_details')->select('id')->where('user_id', $request->to_id)->where('enable_sms_notification' ,1)->first();
                    if(!$disableSmsNotif)
                    {
                        $result = array('status' => false, 'message' => "The user has disabled SMS notifications.");
                        return response()->json($result);
                    }
                } 
                /*
                else if($request->type == 'email')
                {
                    $disableMessageNotif = DB::table('user_details')->select('id')->where('user_id', $request->to_id)->where('enable_message_notification' ,1)->first();
                    if(!$disableMessageNotif)
                    {
                        $result = array('status' => false, 'message' => "The user has disabled message notifications.");
                        return response()->json($result);
                    }
                }
                */
                
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
                    
                    
                    $senderInfo = DB::table('users')->select('name')->where('id', $request->from_id)->first();
                        
                    $param = [
                     'receiver_name' => $user->name,
                    'sender_name' => $senderInfo->name,
                    'description' => $request->message,
                    'email' => $user->email
                    ];
                    
    
                    Mail::send('emails.common.message', $param, function ($message) use ($param) {
                        $message->subject(config('custom.new_message').' '.$param['sender_name']);
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

        /* 'u2.name as to_name','u2.unique_id as to_unique_id' */
        
        try {
            $query = DB::table('messages as m')
                ->join('users as u', 'm.from_id', '=', 'u.id')
                ->leftJoin('users as u2', 'm.to_id', '=', 'u2.id')
                ->select('m.id', 'm.type', 'm.message', 'm.from_id', 'm.to_id', 'm.created_at', 'm.updated_at', 'u.name as from_name', 
                
                DB::raw("CASE 
                    WHEN m.to_id = 'all' THEN 'All' 
                    ELSE u2.name 
                END as to_name"),
                DB::raw("CASE 
                    WHEN m.to_id = 'all' THEN NULL 
                    ELSE u2.unique_id 
                END as to_unique_id")
                )
                ->where('m.type', $request->type)
                ->where('m.deleted_at', NULL);
                
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
