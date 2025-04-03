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

class DocumentController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }



    ## Function to get document
    public function getDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $documents = DB::table('user_documents as d')
                ->leftJoin('doc_types as dt', 'd.doc_type_id', '=', 'dt.id')
                ->leftJoin('users as u', 'd.user_id', '=', 'u.id')
                ->select('d.id', 'd.title', 'd.doc_type_id', 'd.file_name', 'd.file_type', 'd.expiry_date','d.created_at','dt.doc_name as doc_type_name',
                'u.name as uploaded_by','u.unique_id as uploaded_by_unique_id','u.profile_pic as uploaded_by_profile_pic')
                ->where('d.deleted_at', NULL);
            
            if (isset($request->keyword) && !empty($request->keyword)) {
                $documents->where('d.title', 'LIKE', "%{$request->keyword}%")->orWhere('dt.doc_name', 'LIKE', "%{$request->keyword}%")->orWhere('u.name', 'LIKE', "%{$request->keyword}%");
            }
            
                $documents = $documents->orderBy('d.id', 'desc')
                ->get()
                ->map(function ($document) {
                    // Add dir_path column and its value to each record
                    $document->dir_path = url(config('custom.doc_folder') . $document->file_name); // Adjust 'your/directory/path/' to the actual directory path
                    $document->profile_pic_path = (!empty($document->profile_pic))?url(config('custom.user_folder') . $document->profile_pic):'';
                    return $document;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($documents)) . " Record found", 'data' => $documents);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to get document share history
    public function getDocumentShareHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $documents = DB::table('user_documents_share_history as dsh')
                ->leftJoin('user_documents as ud', 'dsh.doc_id', '=', 'ud.id')
                ->leftJoin('doc_types as dt', 'ud.doc_type_id', '=', 'dt.id')
                ->leftJoin('users as u', 'dsh.user_id', '=', 'u.id')
                ->select('dsh.id', 'ud.title','dsh.share_to', 'ud.file_name', 'ud.file_type', 'dsh.created_at','dt.doc_name as doc_type_name',
                'u.name as uploaded_by','u.unique_id as uploaded_by_unique_id','u.profile_pic as uploaded_by_profile_pic');
                
            if (isset($request->keyword) && !empty($request->keyword)) {
                $documents->where('ud.title', 'LIKE', "%{$request->keyword}%")->orWhere('dt.doc_name', 'LIKE', "%{$request->keyword}%")->orWhere('u.name', 'LIKE', "%{$request->keyword}%");
            }
            
                $documents = $documents->orderBy('dsh.id', 'desc')
                ->get()
                ->map(function ($document) {
                    // Add dir_path column and its value to each record
                    $document->dir_path = url(config('custom.doc_folder') . $document->file_name); // Adjust 'your/directory/path/' to the actual directory path
                    $document->profile_pic_path = (!empty($document->profile_pic))?url(config('custom.user_folder') . $document->profile_pic):'';
                    return $document;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($documents)) . " Record found", 'data' => $documents);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ##Function to get document types
    public function getDocumentTypes(Request $request)
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
                ->where('dt.module_type', 'document')
                ->orderBy('dt.id', 'desc')
                ->get()
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($documentTypes)) . " Record found", 'data' => $documentTypes);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to insert/update document types
    public function updateDocumentTypes(Request $request)
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
                $msg = "Document Type has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                $param['module_type'] = 'document';
                DB::table('doc_types')->insert($param);
                $msg = "Document Type has been been successfully inserted";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
     ## Function to delete document type
    public function deleteDocumentType(Request $request)
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
                $msg = "Document Type has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to update document type's status
    public function updateDocumentTypeStatus(Request $request)
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
                $msg = "Document type status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to perform bulk actions on type
    public function documentTypeBulkActions(Request $request)
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
                $msg = count($request->user_ids)." Document type(s) has been successfully deleted";
                    
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
                $msg = count($request->user_ids)." Document type(s) status has been successfully updated";
                    
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
                $msg = count($request->user_ids)." Document type(s) status has been successfully updated";
                    
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
