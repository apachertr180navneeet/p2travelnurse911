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
use Illuminate\Support\Facades\Storage;

class SchedulingController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }
    
    ## Function to get schedules
    public function getSchedules(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $schedules = DB::table('schedules as s')
                ->leftJoin('users as u', 's.created_by', '=', 'u.id')
                ->leftJoin('users as u2', 's.assignee_id', '=', 'u2.id')
                ->select('s.id', 's.title',  's.start_date', 's.end_date', 's.start_time','s.end_time','s.status','s.assignee_id','s.importance','s.description',
                    'u.name as creator_name',
                    'u.role_id as creator_role_id',
                    'u.unique_id as creator_unique_id',
                    'u2.name',
                    'u2.role_id as user_role_id',
                    'u2.unique_id as user_unique_id')
                /*->where('s.created_by', $request->user_id)
                ->where('s.status', 1)*/
                ->where('s.deleted_at', NULL);
                
            // Apply filters if present
            if (isset($request->status) && $request->status != 'all') {
                $schedules->where('s.status', $request->status);
            }
            if (isset($request->keyword) && !empty($request->keyword)) {
                $schedules->where('s.title', 'LIKE', "%{$request->keyword}%");
            }
            
            if ((isset($request->start_date) && !empty($request->start_date)) || isset($request->end_date) && !empty($request->end_date)) {
                if(isset($request->start_date) && !empty($request->start_date))
                    $schedules->where('s.start_date', '>=', $request->start_date);
                    
                if(isset($request->end_date) && !empty($request->end_date))
                    $schedules->where('s.end_date', '<=', $request->end_date);
            } 
            
                $schedules = $schedules->orderBy('s.id', 'desc')
                ->get()
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($schedules)) . " Record found", 'data' => $schedules);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to insert/update schedule
    public function updateSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'startTime' => 'required',
            'endTime' => 'required',
            'assigneeId' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            $param['title'] = $request->title;
            $param['start_date'] = $request->startDate;
            $param['end_date'] = $request->endDate;
            $param['start_time'] = $request->startTime;
            $param['end_time'] = $request->endTime;
            $param['assignee_id'] = $request->assigneeId;
            $param['importance'] = $request->importance;
            $param['description'] = $request->description;
            
            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('schedules')->where('id', $request->id)->update($param);
                $schedule_id = $request->id;
                $msg = "Schedule has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $schedule_id = DB::table('schedules')->insertGetId($param);
                $msg = "Schedule has been been successfully created";
            }
            
            if(isset($request->attatchmentIds) && !empty($request->attatchmentIds))
            {
                $param = array();
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                $param['schedule_id'] = $schedule_id;
                DB::table('schedule_attatchments')->whereIn('id', $request->attatchmentIds)->update($param);
            }
            
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete schedule
    public function deleteSchedule(Request $request)
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
            DB::table('schedules')->where('id', $request->id)->update($param);
            $msg = "Schedule has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update schedule status
    public function updateScheduleStatus(Request $request)
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
            DB::table('schedules')->where('id', $request->id)->update($param);
                
            $msg = "Schedule status has been been successfully updated";
            
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get attatchments
    public function getScheduleAttatchments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'scheduleId' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $resumes = DB::table('schedule_attatchments as d')
                ->select('d.id', 'd.title',  'd.file_name', 'd.file_type', 'd.file_size','d.created_at')
                /*->where('d.user_id', $request->user_id)*/
                ->where('d.schedule_id',$request->scheduleId)
                ->where('d.deleted_at', NULL)
                ->whereNotNull('d.file_name')
                ->orderBy('d.id', 'desc')
                ->get()
                ->map(function ($resumes) {
                    // Add dir_path column and its value to each record
                    $resumes->dir_path = ($resumes->file_name)?url(config('custom.schedule_folder') . $resumes->file_name):""; 
                    return $resumes;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($resumes)) . " Record found", 'data' => $resumes);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    public function generateAttatchmentTitle() {
        $latestResume = DB::table('schedule_attatchments')
                        ->select('title')
                        ->where('title', 'like', 'Attatchment%')
                        ->orderBy('title', 'desc')
                        ->first();
    
        if ($latestResume) {
            // Extract the number from the title and increment it
            $latestNumber = intval(substr($latestResume->title, strlen('Attatchment ')));
            $nextNumber = $latestNumber + 1;
            $newTitle = 'Attatchment ' . $nextNumber;
        } else {
            // If no existing resumes found, start with 1
            $newTitle = 'Attatchment 1';
        }
    
        return $newTitle;
    }
    
    ## Function to upload Attatchment
    public function uploadScheduleAttatchment(Request $request)
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

            $param = array();
            
            if ($request->file('file_name')) {
                $file = $request->file('file_name');
                $imageSize = $request->file('file_name')->getSize();
                $ext = $file->getClientOriginalExtension();
                $fileName = time() * rand() . '.' . $ext;
                $path = config('custom.schedule_folder');
                $upload = $file->move($path, $fileName);
                if ($upload) {
                    $param['file_name'] = $fileName;
                    $param['file_type'] = $ext;
                    $param['file_size'] = number_format($imageSize / 1048576,2).' MB';
                }
            } 
            $param['title'] = $this->generateAttatchmentTitle();
            $param['user_id'] = $request->user_id;
            $param['created_by'] = $request->user_id;
            $param['created_at'] = $this->entryDate;
            
            if(isset($request->id) && !empty($request->id))
                $param['schedule_id'] = $request->id;
                
            $id = DB::table('schedule_attatchments')->insertGetId($param);
            $msg = "Schedule attatchment has been been successfully uploaded";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg,'attachmentId' => $id);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    
    
    
   
}