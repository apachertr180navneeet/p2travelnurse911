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
use Illuminate\Support\Facades\Storage;

class CalendarController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }
    
    ## FUnction to get Calendar Data
    public function getCalendarData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $tasks = DB::table('events as s')
                ->leftJoin('users as u', 's.created_by', '=', 'u.id')
                ->select('s.id', 's.title', 's.type', 's.start_date','s.end_date','s.start_time','s.end_time', 's.status','s.created_at',
                    'u.name as creator_name',
                    'u.role_id as creator_role_id',
                    'u.unique_id as creator_unique_id'
                )
                ->where('s.deleted_at', NULL)
                ->where('s.assignee_id',$request->user_id)
                ->orderBy('s.id', 'desc')
                ->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => "Calendar data found", 'data' => $tasks);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

   
}