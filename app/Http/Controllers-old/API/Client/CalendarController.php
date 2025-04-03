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
use Illuminate\Support\Facades\Storage;

class CalendarController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }
    
    ## Function to get events
    public function getEvents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            $tasks = DB::table('events as s')
                ->leftJoin('users as u', 's.created_by', '=', 'u.id')
                ->select('s.id', 's.title', 's.type', 's.start_date','s.end_date','s.start_time','s.end_time', 's.status','s.created_at',
                    'u.name as creator_name',
                    'u.role_id as creator_role_id',
                    'u.unique_id as creator_unique_id'
                )
                ->where('s.deleted_at', NULL)
                ->where('s.created_by',$request->user_id);
                
            // Apply filters if present
            if (isset($request->status) && $request->status != 'all') {
                $tasks->where('s.status', $request->status);
            }
            if (isset($request->keyword) && !empty($request->keyword)) {
                $tasks->where('s.title', 'LIKE', "%{$request->keyword}%");
            }
            
                $tasks = $tasks->orderBy('s.id', 'desc')
                ->get()
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($tasks)) . " Record found", 'data' => $tasks);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to insert/update event
    public function updateEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            'type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'assignee_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            $param['title'] = $request->title;
            $param['type'] = $request->type;
            $param['type_id'] = 0;
            $param['start_date'] = $request->start_date;
            $param['end_date'] = $request->end_date;
            $param['start_time'] = $request->start_time;
            $param['end_time'] = $request->end_time;
            $param['assignee_id'] = $request->assignee_id;
            $param['importance'] = $request->importance;
            $param['description'] = $request->description;
            $param['status'] = ($request->status)?$request->status:1;
            
            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('events')->where('id', $request->id)->update($param);
                $msg = "Event has been been successfully updated";
                
               
                
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $task_id = DB::table('events')->insertGetId($param);
                $msg = "Event has been been successfully created";
                
                
            }
            
            
            
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete event
    public function deleteEvent(Request $request)
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
                
            $param['updated_by'] = $request->user_id;
            $param['updated_at'] = $this->entryDate;
            $param['deleted_at'] = $this->entryDate;
            DB::table('events')->where('id', $request->id)->update($param);
            $msg = "Event has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update event status
    public function updateEventStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            $param['updated_by'] = $request->user_id;
            $param['updated_at'] = $this->entryDate;
            $param['status'] = $request->status;
            DB::table('events')->where('id', $request->id)->update($param);
                
            $msg = "Event status has been been successfully updated";
            
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}