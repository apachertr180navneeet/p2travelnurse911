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

class TaskController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }
    
    ## Function to get task
    public function getTasks(Request $request)
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
            
            $tasks = DB::table('tasks as s')
                ->leftJoin('users as u', 's.created_by', '=', 'u.id')
                ->leftJoin('task_assigned_users', 's.id', '=', 'task_assigned_users.task_id')
                ->leftJoin('users as u2', 'task_assigned_users.user_id', '=', 'u2.id')
                ->select('s.id', 's.title',  's.description', 's.due_date', 's.status','s.created_at',
                    'u.name as creator_name',
                    'u.role_id as creator_role_id',
                    'u.unique_id as creator_unique_id', 
                    DB::raw('GROUP_CONCAT(u2.id SEPARATOR ", ") as user_ids'),
                    DB::raw('GROUP_CONCAT(u2.name SEPARATOR ", ") as user_names'))
                ->where('s.deleted_at', NULL);
                
            // Apply filters if present
            if (isset($request->status) && $request->status != 'all') {
                $tasks->where('s.status', $request->status);
            }
            if (isset($request->keyword) && !empty($request->keyword)) {
                $tasks->where('s.title', 'LIKE', "%{$request->keyword}%");
            }
            
                $tasks = $tasks->orderBy('s.id', 'desc')
                 ->groupBy('s.id', 's.title')
                ->get()
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($tasks)) . " Record found", 'data' => $tasks);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to insert/update task
    public function updateTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            'due_date' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            $param['title'] = $request->title;
            $param['due_date'] = $request->due_date;
            $param['description'] = $request->description;
            $param['status'] = ($request->status)?$request->status:0;
            
            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('tasks')->where('id', $request->id)->update($param);
                $task_id = $request->id;
                $msg = "Task has been been successfully updated";
                
               
                
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $task_id = DB::table('tasks')->insertGetId($param);
                $msg = "Task has been been successfully created";
                
            }
            
            if(isset($request->attatchmentIds) && !empty($request->attatchmentIds))
            {
                $param = array();
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                $param['task_id'] = $task_id;
                DB::table('task_attatchments')->whereIn('id', $request->attatchmentIds)->update($param);
            }
            
            DB::table('task_assigned_users')->where('task_id',$task_id)->delete();
            DB::table('events')->where('type','task')->where('type_id',$task_id)->delete();
            if(isset($request->assignedUserIDs) && !empty($request->assignedUserIDs))
            {
                foreach($request->assignedUserIDs as $k=>$v)
                {
                    $param = array();
                    $param['user_id'] = $v;
                    $param['task_id'] = $task_id;
                    DB::table('task_assigned_users')->insert($param);
                    
                    ## Make Entry in Events Tables with task type
                    $param = array(
                        'title' => $request->title,
                        'type' => 'task',
                        'type_id' => $task_id,
                        'start_date' => $request->due_date,
                        'end_date' => $request->due_date,
                        'start_time' => '00:00:00',
                        'end_time' => '00:00:00',
                        'assignee_id' => $v,
                        'created_at' => $this->entryDate,
                        'created_by' => $request->user_id
                    );
                    DB::table('events')->insert($param);
                }
            }
            
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete task
    public function deleteTask(Request $request)
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
            DB::table('tasks')->where('id', $request->id)->update($param);
            $msg = "Task has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update task status
    public function updateTaskStatus(Request $request)
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
            DB::table('tasks')->where('id', $request->id)->update($param);
                
            $msg = "Task status has been been successfully updated";
            
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get attatchments
    public function getTaskAttatchments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'taskId' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $resumes = DB::table('task_attatchments as d')
                ->select('d.id', 'd.title',  'd.file_name', 'd.file_type', 'd.file_size','d.created_at')
                ->where('d.task_id',$request->taskId)
                ->where('d.deleted_at', NULL)
                ->whereNotNull('d.file_name')
                ->orderBy('d.id', 'desc')
                ->get()
                ->map(function ($resumes) {
                    // Add dir_path column and its value to each record
                    $resumes->dir_path = ($resumes->file_name)?url(config('custom.task_folder') . $resumes->file_name):""; 
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
        $latestResume = DB::table('task_attatchments')
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
    public function uploadTaskAttatchment(Request $request)
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
                $path = config('custom.task_folder');
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
                $param['task_id'] = $request->id;
                
            $id = DB::table('task_attatchments')->insertGetId($param);
            $msg = "Task attatchment has been been successfully uploaded";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg,'attachmentId' => $id);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    
    ## Function to perform bulk actions on task
    public function taskBulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required',
            'user_id' => 'required',
            'bulk_action' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            
            if($request->bulk_action == 'delete' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['deleted_at'] = $this->entryDate;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('tasks')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Task(s) has been successfully deleted";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-pending' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "0";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('tasks')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Task(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-in-progress' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "1";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('tasks')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Task(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-completed' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "2";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('tasks')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Task(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else {
                $result = array('status' => false, 'message' => 'Unknown error occured');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
   
}