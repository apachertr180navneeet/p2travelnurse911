<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;
use DB;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ComplianceFileController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }



    ## Function to get compliacne files
    public function getComplianceFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            
            $today = Carbon::today();
            
            $complianceFiles = DB::table('compliance_files as cf')
                ->leftJoin('doc_types as dt', 'cf.type_id', '=', 'dt.id')
                ->leftJoin('users as u', 'cf.assigned_user_id', '=', 'u.id')
                ->select('cf.id', 'cf.title', 'cf.type_id','cf.cat_id', 'cf.assigned_user_id', 'cf.notes',  'cf.expiration_date', 'cf.file_name','dt.doc_name as doc_type_name',
                'u.name as assigned_to','u.unique_id as assigned_unique_id','cf.status')
                ->where('cf.deleted_at', NULL)
                ->where('dt.module_type', 'compliance')
                ->where('cf.created_by',$request->user_id);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $complianceFiles->where(function ($query) use ($request) {
                    $query->where('cf.title', 'LIKE', "%{$request->keyword}%")
                    ->orWhere('u.name', 'LIKE', "%{$request->keyword}%");
                });
            }
            
            if (isset($request->tab) && $request->tab != 'all') {
                $complianceFiles->where('cf.cat_id', $request->tab);
            }   
            if (isset($request->status) && $request->status != 'all') {
                if($request->status == 'expired')
                    $complianceFiles->where('cf.expiration_date', '<', $today)->where('cf.is_archive','0');
                else if($request->status == 'archived')
                    $complianceFiles->where('cf.expiration_date', '>=', $today)->where('cf.is_archive','1');
                else
                    $complianceFiles->where('cf.expiration_date', '>=', $today)->where('cf.is_archive','0');
            }
              
            if ((isset($request->expiration_start_date) && !empty($request->expiration_start_date)) || isset($request->expiration_end_date) && !empty($request->expiration_end_date)) {
                if(isset($request->expiration_start_date) && !empty($request->expiration_start_date))
                    $complianceFiles->where('cf.expiration_date', '>=', $request->expiration_start_date);
                    
                if(isset($request->expiration_end_date) && !empty($request->expiration_end_date))
                    $complianceFiles->where('cf.expiration_date', '<=', $request->expiration_end_date);
            }  
              
                $complianceFiles = $complianceFiles->orderBy('cf.id', 'desc')->get()
                ->map(function ($complianceFiles) {
                    // Add dir_path column and its value to each record
                    $complianceFiles->dir_path = (!empty($complianceFiles->file_name))?url(config('custom.compliance_file_folder') . $complianceFiles->file_name):""; // Adjust 'your/directory/path/' to the actual directory path
                    return $complianceFiles;
                })
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($complianceFiles)) . " Record found", 'data' => $complianceFiles);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to add/update compliance files
    public function updateComplianceFile(Request $request)
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

            $param = $request->all();
            
            if ($request->file('file_name')) {
                $file = $request->file('file_name');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() * rand() . '.' . $ext;
                $path = config('custom.compliance_file_folder');
                $upload = $file->move($path, $fileName);
                if ($upload) {
                    $param['file_name'] = $fileName;
                    $param['file_type'] = $ext;
                }
            }
            
            $user_id = $request->user_id;
            unset($param['user_id']);

            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('compliance_files')->where('id', $request->id)->update($param);
                $msg = "Compliance file has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('compliance_files')->insert($param);
                $msg = "Compliance file has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete compliance file
    public function deleteComplianceFile(Request $request)
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

                $param = array(
                    'deleted_at' => $this->entryDate,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    );
                DB::table('compliance_files')->where('id', $request->id)->update($param);
                $msg = "Compliance file has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to update compliance file's status
    public function updateComplianceFileStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                
                $param = array(
                    'status' => $request->status,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    );
                DB::table('compliance_files')->where('id', $request->id)->update($param);
                $msg = "Compliance file status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to perform bulk actions 
    public function complianceFileBulkActions(Request $request)
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
                    DB::table('compliance_files')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file(s) has been successfully deleted";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-active' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = 1;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('compliance_files')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-inactive' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = 0;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('compliance_files')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'mark-as-archive' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['is_archive'] = 1;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('compliance_files')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file(s) has been successfully marked as archive";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'remove-from-archive' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['is_archive'] = 0;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('compliance_files')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file(s) has been successfully removed from archive";
                    
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
    
    ##Function to get compliance file types
    public function getComplianceFileTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $documentTypes = DB::table('doc_types as dt')
                ->select('dt.id','dt.doc_name', 'dt.created_at')
                ->where('dt.deleted_at', NULL)
                ->where('dt.module_type', 'compliance')
                ->orderBy('dt.id', 'desc')
                ->get()
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($documentTypes)) . " Record found", 'data' => $documentTypes);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to insert/update compliance file types
    public function updateComplianceFileType(Request $request)
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
            
            $param['doc_name'] = $request->doc_name;
            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('doc_types')->where('id', $request->id)->update($param);
                $msg = "Compliance File Type has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $param['module_type'] = 'compliance';
                $param['status'] = 1;
                DB::table('doc_types')->insert($param);
                $msg = "Compliance File Type has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete compliance file type
    public function deleteComplianceFileType(Request $request)
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

                $param['deleted_at'] = $this->entryDate;
                DB::table('doc_types')->where('id', $request->id)->update($param);
                $msg = "Compliance File Type has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ##Function to get compliance file category
    public function getComplianceFileCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            
            /*
            $documentTypes = DB::table('categories as c')
                ->leftJoin('compliance_files as cf', function($join) {
                    $join->on('cf.cat_id', '=', 'c.id')
                         ->whereNull('cf.deleted_at');
                })
                ->select('c.id', 'c.cat_name', 'c.created_at', DB::raw('count(cf.id) as total_compliance_files'))
                ->whereNull('c.deleted_at')
                 ->whereNull('cf.deleted_at')
                ->where('c.cat_type', 'compliance')
                ->groupBy('c.id', 'c.cat_name', 'c.created_at') // Include all non-aggregated columns in GROUP BY
                ->orderByDesc('c.id')
                ->get()
                ->toArray();
            */
            
            /*
            $documentTypes = DB::table('categories as c')
            ->leftJoin('compliance_files as cf', function($join) {
                $join->on('cf.cat_id', '=', 'c.id')
                     ->whereNull('cf.deleted_at');
            })
            ->select('c.id', 'c.cat_name', 'c.created_at', DB::raw('count(cf.id) as total_compliance_files'))
            ->whereNull('c.deleted_at')
            ->whereNull('cf.deleted_at')
            ->where('c.cat_type', 'compliance')
            ->where('cf.created_by',$request->user_id)
            ->groupBy('c.id', 'c.cat_name', 'c.created_at') // Include all non-aggregated columns in GROUP BY
            ->orderByDesc('c.id')
        
            ->unionAll(function($query) use ($request) {
                $query->select(DB::raw('null as id'), DB::raw('"All Categories" as cat_name '), DB::raw('null as created_at'), DB::raw('count(*) as total_compliance_files'))
                      ->from('categories')
                      ->join('compliance_files', 'categories.id', '=', 'compliance_files.cat_id')
                      ->where('compliance_files.created_by',$request->user_id)
                      ->whereNull('compliance_files.deleted_at')
                      ->whereNull('categories.deleted_at');
            })
            ->orderBy('cat_name', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
            */
            
            $documentTypes = DB::table('categories as c')
            ->leftJoin('compliance_files as cf', function ($join) use ($request) {
                $join->on('cf.cat_id', '=', 'c.id')
                    ->whereNull('cf.deleted_at')
                    ->where('cf.created_by', $request->user_id); // Include the user_id condition here
            })
            ->select('c.id', 'c.cat_name', 'c.created_at', 
                DB::raw('count(cf.id) as total_compliance_files'))
            ->whereNull('c.deleted_at')
            ->where('c.cat_type', 'compliance')
            ->groupBy('c.id', 'c.cat_name', 'c.created_at')
            ->orderByDesc('c.id')
            ->orderBy('c.cat_name', 'asc')
        
            ->unionAll(function ($query) use ($request) {
                $query->select(
                        DB::raw('null as id'),
                        DB::raw('"All Categories" as cat_name'),
                        DB::raw('null as created_at'),
                        DB::raw('count(cf.id) as total_compliance_files')
                    )
                    ->from('compliance_files as cf')
                    ->where('cf.created_by', $request->user_id)
                    ->whereNull('cf.deleted_at');
            })
            ->orderBy('cat_name', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

            
            $result = array('status' => true, 'message' => (count($documentTypes)) . " Record found", 'data' => $documentTypes);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to insert/update compliance file categories
    public function updateComplianceFileCategory(Request $request)
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
            
            $param['cat_name'] = $request->cat_name;
            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('categories')->where('id', $request->id)->update($param);
                $msg = "Compliance File Category has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $param['cat_type'] = 'compliance';
                DB::table('categories')->insert($param);
                $msg = "Compliance File Category has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete compliance file type
    public function deleteComplianceFileCategory(Request $request)
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

                $param['deleted_at'] = $this->entryDate;
                DB::table('categories')->where('id', $request->id)->update($param);
                $msg = "Compliance File Category has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to get compliacne checklists
    public function getComplianceChecklists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $checklists = DB::table('compliance_checklists as c')
                ->select('c.*')
                ->where('c.deleted_at', NULL)
                ;
                /* ->where('c.created_by', $request->user_id) */
            if (isset($request->keyword) && !empty($request->keyword)) {
                $checklists->where('c.title', 'LIKE', "%{$request->keyword}%");
            } 
            if (isset($request->status) && $request->status != 'all') {
                $checklists->where('c.status', $request->status);
            }
            
                $checklists = $checklists->orderBy('c.id', 'desc')->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($checklists)) . " Record found", 'data' => $checklists);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get compliacne submitted checklists
    public function getComplianceSubmittedChecklists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $checklists = DB::table('compliance_checklists as c')
                ->join('user_compliance_checklists as ucc', 'ucc.checklist_id', '=', 'c.id')
                ->leftJoin('users as u', 'c.created_by', '=', 'u.id')
                ->leftJoin('users as u2', 'ucc.user_id', '=', 'u2.id')
                ->select('c.*','u.name as creator_name', 'u.role_id as creator_role_id', 'u.unique_id as creator_unique_id',
                'u2.name as user_name', 'u2.role_id as user_role_id', 'u2.unique_id as user_unique_id', 'ucc.created_at as submitted_on','ucc.updated_at as updated_on','ucc.checklist_meta as user_checklist_meta')
                ->where('c.deleted_at', NULL)
                ->where('c.created_by', $request->user_id)
                ;
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $checklists->where('c.title', 'LIKE', "%{$request->keyword}%");
            } 
            
                $checklists = $checklists->orderBy('ucc.id', 'desc')->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($checklists)) . " Record found", 'data' => $checklists);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to get user submitted checklists
    public function getUserSubmittedChecklists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'userID' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $checklists = DB::table('compliance_checklists as c')
                ->join('user_compliance_checklists as ucc', 'ucc.checklist_id', '=', 'c.id')
                ->leftJoin('users as u', 'c.created_by', '=', 'u.id')
                ->leftJoin('users as u2', 'ucc.user_id', '=', 'u2.id')
                ->select('c.*','u.name as creator_name', 'u.role_id as creator_role_id', 'u.unique_id as creator_unique_id',
                'u2.name as user_name', 'u2.role_id as user_role_id', 'u2.unique_id as user_unique_id', 'ucc.created_at as submitted_on','ucc.updated_at as updated_on','ucc.checklist_meta as user_checklist_meta')
                ->where('c.deleted_at', NULL)
                ->where('ucc.user_id', $request->userID)
                ->where('ucc.status', 1);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $checklists->where('c.title', 'LIKE', "%{$request->keyword}%");
            } 
            
                $checklists = $checklists->orderBy('ucc.id', 'desc')->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($checklists)) . " Record found", 'data' => $checklists);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to insert/update compliance checklist
    public function updateComplianceChecklist(Request $request)
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
            
            $param['title'] = $request->title;
            $param['status'] = $request->status;
            $param['checklist_meta'] = json_encode($request->sections);
            if (isset($request->id)) {
                $param['slug'] = $this->generateChecklistSlug($request->title, $request->id);
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('compliance_checklists')->where('id', $request->id)->update($param);
                $msg = "Compliance Checklist has been been successfully updated";
            } else {
                $param['unique_id'] = $this->generateUniqueCode(8);
                $param['slug'] = $this->generateChecklistSlug($request->title);
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $param['status'] = 1;
                DB::table('compliance_checklists')->insert($param);
                $msg = "Compliance Checklist has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete compliance checklist
    public function deletedComplianceChecklist(Request $request)
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

                $param['deleted_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('compliance_checklists')->where('id', $request->id)->update($param);
                $msg = "Compliance Checklist has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update compliance checklist's status
    public function updateComplianceChecklistStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                
                $param = array(
                    'status' => $request->status,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    );
                DB::table('compliance_checklists')->where('id', $request->id)->update($param);
                $msg = "Compliance checklist status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to clone compliance checklist
    public function cloneComplianceChecklist(Request $request)
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

            $checklistRow = DB::table('compliance_checklists')->select('id','title','checklist_meta','status','deleted_at')
                    ->where('id', $request->id)->first();
            if (!empty($checklistRow)) {
                
                $param = array(
                    'title' => $checklistRow->title,
                    'slug' => $this->generateChecklistSlug($checklistRow->title),
                    'unique_id' => $this->generateUniqueCode(8),
                    'checklist_meta' => $checklistRow->checklist_meta,
                    'status' => $checklistRow->status,
                    'deleted_at' => $checklistRow->deleted_at,
                    'created_at' => $this->entryDate,
                    'created_by' => $request->user_id,
                    );
                DB::table('compliance_checklists')->insert($param);
                $msg = "Compliance checklist has been successfully cloned";
                $result = array('status' => true, 'message' => $msg);
            }    
            else
            {
                $result = array('status' => false, 'message' => 'Invalid checklist ID');
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get compliance checklist details
    public function getComplianceChecklistDetails(Request $request)
    {
        
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'checklistID' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            # Check for drafted job
            $user_id = $request->user_id;
            $raw = DB::table('compliance_checklists as cc')
                ->select('cc.*')
                ->where(['cc.unique_id' => $request->checklistID, 'cc.created_by' => $request->user_id])
                ->first();
            
            if(!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $raw);
            else
                $result = array('status' => false, 'message' => 'Invalid Compliance Checklist ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    public function generateChecklistSlug($title, $id = null) {
        
        $slug = Str::slug($title);
        
        $originalSlug = $slug;
        $iteration = 1;
        
        if($id != null)
        {
            while (DB::table('compliance_checklists')->where('slug', $slug)->where('id','!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $iteration;
                $iteration++;
            }
        }
        else
        {
            while (DB::table('compliance_checklists')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $iteration;
                $iteration++;
            }
        }    
        return $slug;
    }
    
    public function generateUniqueCode(int $codeLength)
	{
        $characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);

        $code = '';

        while (strlen($code) < $codeLength) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;
        }

        if (DB::table('compliance_checklists')->where('unique_id', $code)->exists()) {
            $this->generateUniqueCode($codeLength);
        }

        return $code;
	}
    
    ## Function to get compliance assigned checklist
    public function getComplianceAssignedChecklists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $uChecklist = DB::table('compliance_assigned_checklists as c')
                ->join('compliance_checklists as cc', 'cc.id', '=', 'c.checklist_id')
                ->join('users as u', 'c.user_id', '=', 'u.id') 
                ->select('c.checklist_id','c.id','cc.title','c.created_at','c.user_id','u.profile_pic', 'u.name as user_name', 'u.unique_id as user_unique_id')
                ->where('cc.deleted_at', NULL)
                ->where('c.created_by', $request->user_id);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $uChecklist->where('cc.title', 'LIKE', "%{$request->keyword}%");
            } 
            
                $uChecklist = $uChecklist->orderBy('c.id', 'desc')
                ->get()
                ->map(function ($uChecklist) {
                    // Add dir_path column and its value to each record
                    $uChecklist->profile_pic_path = (!empty($uChecklist->profile_pic))?url(config('custom.user_folder') . $uChecklist->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uChecklist;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($uChecklist)) . " Record found", 'data' => $uChecklist);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## FUnction to update compliance assigned checklist
    public function updateComplianceChecklistAssignedUsers(Request $request)
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

           
            
            
            if (isset($request->id)) {
                if(isset($request->checklists) && !empty($request->checklists))
                {
                    $checklist_title = array();
                    foreach($request->checklists as $k=>$v)
                    {
                        if(!DB::table('compliance_assigned_checklists')->where('checklist_id', $v)->where('user_id', $request->assigned_user_id)->exists())
                        {
                             $param = array(
                                'checklist_id' => $v,
                                'user_id' => $request->assigned_user_id,
                                'created_at' => $this->entryDate,
                                'created_by' => $request->user_id,
                            );
                            DB::table('compliance_assigned_checklists')->insert($param);
                            
                            $checklistResult = DB::table('compliance_checklists as c')
                                ->select('c.id', 'c.title', 'c.unique_id')
                                ->where(['c.id' => $v])
                                ->first();

                            $checklist_title[] = $checklistResult->title;
                        }
                        else
                        {
                            $param = array(
                                'updated_at' => $this->entryDate,
                                'updated_by' => $request->user_id,
                            );
                            DB::table('compliance_assigned_checklists')->where('id', $request->id)->update($param);
                            
                            
                        }
                    }
                    
                    if(!empty($checklist_title))
                    {
                        $agencyUser = DB::table('users as u')
                            ->select('u.id', 'u.name', 'u.email')
                            ->where(['u.id' => $request->user_id])
                            ->first();
    
                        $user = DB::table('users as u')
                            ->select('u.name', 'u.email')
                            ->where(['u.id' => $request->assigned_user_id])
                            ->first();
    
                        $param = array(
                            'checklist_titles' => implode(', ', $checklist_title),
                            'receiver_name' => $user->name,
                            'receiver_email' => $user->email,
                            'sender_name' => $agencyUser->name,
                            'dashboard_path' => config('custom.login_url')
                        );
    
    
                        Mail::send('emails.client.assign-skill-checklist', $param, function ($message) use ($param) {
                            $message->subject(config('custom.assign_skill_checklist') . ' ' . $param['sender_name']);
                            $message->to($param['receiver_email']);
                        });
                    }
                }
                
                $msg = "Compliance checklist assign successfully";
            } else {
                if(isset($request->checklists) && !empty($request->checklists))
                {
                    $checklist_title = array();
                    
                    foreach($request->checklists as $k=>$v)
                    {
                        if(!DB::table('compliance_assigned_checklists')->where('checklist_id', $v)->where('user_id', $request->assigned_user_id)->exists())
                        {
                            $param = array(
                                'checklist_id' => $v,
                                'user_id' => $request->assigned_user_id,
                                'created_at' => $this->entryDate,
                                'created_by' => $request->user_id,
                            );
                            DB::table('compliance_assigned_checklists')->insert($param);
                            
                            $checklistResult = DB::table('compliance_checklists as c')
                                ->select('c.id', 'c.title', 'c.unique_id')
                                ->where(['c.id' => $v])
                                ->first();

                            $checklist_title[] = $checklistResult->title;
                        }
                    }
                    
                    if(!empty($checklist_title))
                    {
                        $agencyUser = DB::table('users as u')
                            ->select('u.id', 'u.name', 'u.email')
                            ->where(['u.id' => $request->user_id])
                            ->first();
    
                        $user = DB::table('users as u')
                            ->select('u.name', 'u.email')
                            ->where(['u.id' => $request->assigned_user_id])
                            ->first();
    
                        $param = array(
                            'checklist_titles' => implode(', ', $checklist_title),
                            'receiver_name' => $user->name,
                            'receiver_email' => $user->email,
                            'sender_name' => $agencyUser->name,
                            'dashboard_path' => config('custom.login_url')
                        );
    
    
                        Mail::send('emails.client.assign-skill-checklist', $param, function ($message) use ($param) {
                            $message->subject(config('custom.assign_skill_checklist') . ' ' . $param['sender_name']);
                            $message->to($param['receiver_email']);
                        });
                    }
                }
                $msg = "Compliance checklist assign successfully";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## function to deleted compliance assigned checklist
    public function deleteComplianceAssignedChecklist(Request $request)
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

                DB::table('compliance_assigned_checklists')->where('id', $request->id)->delete();
                $msg = "Assigned Compliance File Checklist has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to get all facility compliances
    public function getFacilityCompliances(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $facilityCompliances = DB::table('facility_compliance as fc')
                ->select('fc.id', 'fc.unique_id','fc.title', 'fc.type','fc.status','fc.created_at')
                ->where('fc.deleted_at', NULL)
                ->where('fc.created_by', $request->user_id);
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $facilityCompliances->where('fc.title', 'LIKE', "%{$request->keyword}%");
            } 
                $facilityCompliances = $facilityCompliances->orderBy('fc.id', 'desc')->get()
                ->toArray();
            
            
            $result = array('status' => true, 'message' => (count($facilityCompliances)) . " Record found", 'data' => $facilityCompliances);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to add/update facility compliance 
    public function updateFacilityCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = $request->all();
            
            $user_id = $request->user_id;
            unset($param['user_id']);

            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('facility_compliance')->where('id', $request->id)->update($param);
                $msg = "Facility Compliance List has been been successfully updated";
            } else {
                $param['unique_id'] = $this->generateFacilityCompliancesUniqueCode(8);
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('facility_compliance')->insert($param);
                $msg = "Facility Compliance List has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    public function generateFacilityCompliancesUniqueCode(int $codeLength)
	{
        $characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);

        $code = '';

        while (strlen($code) < $codeLength) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;
        }

        if (DB::table('facility_compliance')->where('unique_id', $code)->exists()) {
            $this->generateFacilityCompliancesUniqueCode($codeLength);
        }

        return $code;
	}
    
    ## Function to update facility compliance status
    public function updateFacilityCompliancesStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();

                
                $param = array(
                    'status' => $request->status,
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                    );
                DB::table('facility_compliance')->where('id', $request->id)->update($param);
                $msg = "Facility compliance status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete compliance compliance
    public function deleteFacilityCompliance(Request $request)
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

                $param['deleted_at'] = $this->entryDate;
                DB::table('facility_compliance')->where('id', $request->id)->update($param);
                $msg = "Facility Compliance has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to clone facility compliance
    //cloneFacilityCompliance
     
    ## Function to get assigned facility compliance
    public function getAssignedFacilityCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $uChecklist = DB::table('user_facility_compliances as c')
                ->join('facility_compliance as cc', 'cc.id', '=', 'c.facility_compliance_id')
                ->join('users as u', 'c.user_id', '=', 'u.id') 
                ->select('cc.id as facility_compliance_id','c.id','cc.title','c.created_at','c.user_id','u.profile_pic', 'u.name as user_name', 'u.unique_id as user_unique_id')
                ->where('cc.deleted_at', NULL);
                /*->where('cc.created_by', $request->user_id)*/
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $uChecklist->where('cc.title', 'LIKE', "%{$request->keyword}%");
            } 
                $uChecklist = $uChecklist->orderBy('c.id', 'desc')
                ->get()
                ->map(function ($uChecklist) {
                    // Add dir_path column and its value to each record
                    $uChecklist->profile_pic_path = (!empty($uChecklist->profile_pic))?url(config('custom.user_folder') . $uChecklist->profile_pic):''; // Adjust 'your/directory/path/' to the actual directory path
                    return $uChecklist;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($uChecklist)) . " Record found", 'data' => $uChecklist);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update assigned facility compliance
    public function updateAssignedFacilityCompliance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'facility_compliance_id' => 'required', 
            'assigned_user_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

           
            
            
            if (isset($request->id)) {
                
                if(!DB::table('user_facility_compliances')->where('facility_compliance_id', $request->facility_compliance_id)->where('user_id', $request->assigned_user_id)->exists())
                {
                     $param = array(
                        'facility_compliance_id' => $request->facility_compliance_id,
                        'user_id' => $request->assigned_user_id,
                        'created_at' => $this->entryDate,
                        'created_by' => $request->user_id,
                    );
                    DB::table('user_facility_compliances')->insert($param);
                }
                else
                {
                    $param = array(
                        'updated_at' => $this->entryDate,
                        'updated_by' => $request->user_id,
                    );
                    DB::table('user_facility_compliances')->where('id', $request->id)->update($param);
                }
                    
                
                $msg = "Facility Compliance assign successfully";
            } else {
               
                if(!DB::table('user_facility_compliances')->where('facility_compliance_id', $request->facility_compliance_id)->where('user_id', $request->assigned_user_id)->exists())
                {
                    $param = array(
                        'facility_compliance_id' => $request->facility_compliance_id,
                        'user_id' => $request->assigned_user_id,
                        'created_at' => $this->entryDate,
                        'created_by' => $request->user_id,
                    );
                    DB::table('user_facility_compliances')->insert($param);
                }
                    
                $msg = "Facility Compliance assign successfully";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## function to deleted compliance assigned checklist
    public function deleteAssignedFacilityCompliance(Request $request)
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

                DB::table('user_facility_compliances')->where('id', $request->id)->delete();
                $msg = "Assigned Facility Compliance has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## function return all user list (candidate/applicants/employee/job applicants)
    public function getChecklistAssignedUsers(Request $request)
    {
        try {
            
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            $data = array();
            
            $jobApplicantIds = DB::table('job_applications as ja')
            ->join('jobs as j', 'j.id', '=', 'ja.job_id')
            ->join('users as u', 'ja.user_id', '=', 'u.id')
            ->whereNull('ja.deleted_at')
            ->whereNull('u.deleted_at')
            ->where('j.user_id', $request->user_id)
            ->pluck('u.id')
            ->toArray();
            
            $data['applicants'] = DB::table('users as u')
            ->select('u.id', 'u.name')
            ->where('u.role_id', 5)
            ->where('u.deleted_at', NULL)
            ->whereNotIn('u.id', $jobApplicantIds)
            ->where('u.created_by', $request->user_id)
            ->orderBy('u.name')->get()->toArray();
            
            $data['candidates'] = DB::table('users as u')
            ->select('u.id', 'u.name')
            ->where('u.role_id', 4)
            ->where('u.deleted_at', NULL)
            ->whereNotIn('u.id', $jobApplicantIds)
            ->where('u.created_by', $request->user_id)
            ->orderBy('u.name')->get()->toArray();
            
            $data['employees'] = DB::table('users as u')
            ->select('u.id', 'u.name')
            ->where('u.role_id', 9)
            ->where('u.deleted_at', NULL)
            ->whereNotIn('u.id', $jobApplicantIds)
            ->where('u.created_by', $request->user_id)
            ->orderBy('u.name')->get()->toArray();
            
            
            $data['job_applicants'] = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('users as u2', 'j.user_id', '=', 'u2.id')
                ->leftJoin('users as u', 'ja.user_id', '=', 'u.id')
                ->leftJoin('user_details as ud', 'u.id', '=', 'ud.user_id')
                ->leftJoin('user_preferred_employment_types as upet', 'u.id', '=', 'upet.user_id')
                ->leftJoin('user_preferred_shifts as ups', 'u.id', '=', 'ups.user_id')
                ->leftJoin('user_preferred_states as upst', 'u.id', '=', 'upst.user_id')
                ->select(
                    'ja.id as ja_id',
                    'u.id',
                    'u.name'
                )
                ->where('j.user_id', $request->user_id)
                ->whereNull('ja.deleted_at')
                ->whereNull('j.deleted_at')
                ->whereNull('u.deleted_at')
                ->orderBy('u.name', 'asc')
                ->groupBy(
                    'ja.user_id'
                )
                ->get()
                ->toArray();
            
            $result = array('status' => true, 'message' => "record found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}
