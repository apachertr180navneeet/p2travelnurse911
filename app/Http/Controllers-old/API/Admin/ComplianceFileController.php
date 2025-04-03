<?php

namespace App\Http\Controllers\API\Admin;

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
                ->leftJoin('users as u2', 'cf.created_by', '=', 'u2.id')
                ->select('cf.id', 'cf.title', 'cf.type_id','cf.cat_id', 'cf.assigned_user_id', 'cf.notes',  'cf.expiration_date', 'cf.file_name','dt.doc_name as doc_type_name',
                'u.name as assigned_to','u.unique_id as assigned_unique_id',
                'u2.name as creator_name','u2.unique_id as creator_unique_id','u2.role_id as creator_role_id','cf.created_by as posted_user_id','cf.status')
                ->where('cf.deleted_at', NULL)
                ->where('dt.module_type', 'compliance');
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                $complianceFiles->where('cf.title', 'LIKE', "%{$request->keyword}%");
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

                $param['deleted_at'] = $this->entryDate;
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
                ->select('dt.id','dt.doc_name', 'dt.created_at','dt.status')
                ->where('dt.deleted_at', NULL)
                ->where('dt.module_type', 'compliance');
                
             if (isset($request->keyword) && !empty($request->keyword)) {
                $documentTypes->where('dt.doc_name', 'LIKE', "%{$request->keyword}%");
            }    
            
            if (isset($request->status) && $request->status != 'all') {
                $documentTypes->where('dt.status', $request->status);
            }
            
                $documentTypes = $documentTypes->orderBy('dt.id', 'desc')
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
                
                $typeResult = DB::table('doc_types')
                        ->select('id')
                        ->where('id', '!=',$request->id)
                        ->where('doc_name', 'like',$request->doc_name)
                        ->where('module_type', 'compliance')
                        ->first();
                if($typeResult)
                {
                    $result = array('status' => false, 'message' => $request->doc_name." type already exists", 'data' => $request->all());
                    return response()->json($result);
                }
                
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('doc_types')->where('id', $request->id)->update($param);
                $msg = "Compliance File Type has been been successfully updated";
            } else {
                
                $typeResult = DB::table('doc_types')
                        ->select('id')
                        ->where('doc_name','like', $request->doc_name)
                        ->where('module_type', 'compliance')
                        ->first();
                if($typeResult)
                {
                    $result = array('status' => false, 'message' => $request->doc_name." type already exists", 'data' => $request->all());
                    return response()->json($result);
                }
                
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
    
    ## Function to update compliance file type's status
    public function updateComplianceFileTypeStatus(Request $request)
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
                DB::table('doc_types')->where('id', $request->id)->update($param);
                $msg = "Compliance file type status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to perform bulk actions on type
    public function complianceFileTypeBulkActions(Request $request)
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
                    DB::table('doc_types')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file type(s) has been successfully deleted";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-active' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "1";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('doc_types')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file type(s) status has been successfully updated";
                    
                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            }
            else if($request->bulk_action == 'change-status-inactive' && !empty($request->user_ids))
            {
                DB::beginTransaction();
                foreach($request->user_ids as $k=>$v)
                {
                    $param = array();
                    $param['status'] = "0";
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('doc_types')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file type(s) status has been successfully updated";
                    
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
            
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            $documentTypes = DB::table('categories as c')
            ->leftJoin('compliance_files as cf', function($join) {
                $join->on('cf.cat_id', '=', 'c.id')
                     ->whereNull('cf.deleted_at');
            })
            ->select('c.id', 'c.cat_name', 'c.created_at','c.status', DB::raw('count(cf.id) as total_compliance_files'))
            ->whereNull('c.deleted_at')
            ->whereNull('cf.deleted_at')
            ->where('c.cat_type', 'compliance');
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                $documentTypes->where('c.cat_name', 'LIKE', "%{$request->keyword}%");
            }    
            
            if (isset($request->status) && $request->status != 'all') {
                $documentTypes->where('c.status', $request->status);
            }
            
            $documentTypes = $documentTypes->groupBy('c.id', 'c.cat_name', 'c.created_at') // Include all non-aggregated columns in GROUP BY
            ->orderByDesc('c.id');
        
            if(!isset($request->exclude_all_cat))
            {
                $documentTypes = $documentTypes->unionAll(function($query) {
                    $query->select(DB::raw('null as id'), DB::raw('"All Categories" as cat_name '), DB::raw('null as created_at'), DB::raw('null as status'),DB::raw('count(*) as total_compliance_files'))
                          ->from('categories')
                          ->join('compliance_files', 'categories.id', '=', 'compliance_files.cat_id')
                          ->whereNull('compliance_files.deleted_at')
                          ->whereNull('categories.deleted_at');
                });
            }
            $documentTypes = $documentTypes->orderBy('cat_name', 'asc')
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
                
                $typeResult = DB::table('categories')
                        ->select('id')
                        ->where('id', '!=',$request->id)
                        ->where('cat_name','like', $request->cat_name)
                        ->where('cat_type', 'compliance')
                        ->first();
                if($typeResult)
                {
                    $result = array('status' => false, 'message' => $request->cat_name." category already exists", 'data' => $request->all());
                    return response()->json($result);
                }
                
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('categories')->where('id', $request->id)->update($param);
                $msg = "Compliance File Category has been been successfully updated";
            } else {
                
                $typeResult = DB::table('categories')
                        ->select('id')
                        ->where('cat_name','like', $request->cat_name)
                        ->where('cat_type', 'compliance')
                        ->first();
                if($typeResult)
                {
                    $result = array('status' => false, 'message' => $request->cat_name." category already exists", 'data' => $request->all());
                    return response()->json($result);
                }
                
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
    
    ## Function to update compliance file category's status
    public function updateComplianceFileCategoryStatus(Request $request)
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
                DB::table('categories')->where('id', $request->id)->update($param);
                $msg = "Compliance file category status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to perform bulk actions on category
    public function complianceFileCategoryBulkActions(Request $request)
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
                    DB::table('categories')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file category(s) has been successfully deleted";
                    
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
                    DB::table('categories')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file category(s) status has been successfully updated";
                    
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
                    DB::table('categories')->where('id', $v)->update($param);
                }
                $msg = count($request->user_ids)." Compliance file category(s) status has been successfully updated";
                    
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
                ->orderBy('c.id', 'desc')->get()
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
            
            $param['checklist_meta'] = json_encode($request->sections);
            if (isset($request->id)) {
                $param['slug'] = $this->generateChecklistSlug($request->title, $request->id);
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('compliance_checklists')->where('id', $request->id)->update($param);
                $msg = "Compliance Checklist has been been successfully updated";
            } else {
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
                ->orderBy('c.id', 'desc')
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
                }
                
                $msg = "Compliance checklist assign successfully";
            } else {
                if(isset($request->checklists) && !empty($request->checklists))
                {
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
                        }
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
    
}
