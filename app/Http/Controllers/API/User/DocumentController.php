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

class DocumentController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }


    ## Function to add/update document
    public function updateDocument(Request $request)
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
                $path = config('custom.doc_folder');
                $upload = $file->move($path, $fileName);
                if ($upload) {
                    $param['file_name'] = $fileName;
                    $param['file_type'] = $ext;
                }
            }


            if (isset($request->id)) {
                $param['updated_at'] = $this->entryDate;
                $param['updated_by'] = $request->user_id;
                DB::table('user_documents')->where('id', $request->id)->update($param);
                $msg = "Document has been been successfully updated";
            } else {
                $param['created_at'] = $this->entryDate;
                $param['created_by'] = $request->user_id;
                DB::table('user_documents')->insert($param);
                $msg = "Document has been been successfully uploaded";
            }

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
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
                ->select('d.id', 'd.title', 'd.doc_type_id', 'd.file_name', 'd.file_type', 'd.expiry_date','d.created_at','dt.doc_name as doc_type_name')
                ->where('d.user_id', $request->user_id)
                ->where('d.deleted_at', NULL)
                ->orderBy('d.id', 'desc')
                ->get()
                ->map(function ($document) {
                    // Add dir_path column and its value to each record
                    $document->dir_path = url(config('custom.doc_folder') . $document->file_name); // Adjust 'your/directory/path/' to the actual directory path
                    return $document;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($documents)) . " Record found", 'data' => $documents);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to delete document
    public function deleteDocument(Request $request)
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
                DB::table('user_documents')->where('id', $request->id)->update($param);
                $msg = "Document has been been successfully deleted";
            

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
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
                ->select('dsh.id', 'ud.title','dsh.share_to', 'ud.file_name', 'ud.file_type', 'dsh.created_at','dt.doc_name as doc_type_name')
                ->where('dsh.user_id', $request->user_id)
                ->orderBy('dsh.id', 'desc')
                ->get()
                ->map(function ($document) {
                    // Add dir_path column and its value to each record
                    $document->dir_path = url(config('custom.doc_folder') . $document->file_name); // Adjust 'your/directory/path/' to the actual directory path
                    return $document;
                })
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($documents)) . " Record found", 'data' => $documents);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    ## Function to get document access requests
    public function getDocumentAccessRequests(Request $request)
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
             
            $documents = DB::table('user_documents_access_requests as dsh')
                ->leftJoin('users as u', 'dsh.client_id', '=', 'u.id')
                ->select('dsh.*','u.name as client_name')
                ->where('dsh.user_id', $request->user_id)
                ->whereIn('dsh.id', function ($query) use ($request) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('user_documents_access_requests')
                        ->where('user_id', $request->user_id)
                        ->groupBy('client_id');
                })
                ->orderBy('dsh.id', 'desc')
                ->get()
                ->toArray();
            
            $result = array('status' => true, 'message' => (count($documents)) . " Record found", 'data' => $documents);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    
    }
    
    ## Function to share document
    public function shareDocument(Request $request)
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
            $param['created_at'] = $this->entryDate;
            $param['user_id'] = $request->user_id;
            $param['doc_id'] = $request->doc_id;
            $param['share_to'] = $request->share_to;
            DB::table('user_documents_share_history')->insert($param);
            
            $user = DB::table('users as u')
                ->select('u.name')
                ->where(['u.id' => $request->user_id])->first();
            $full_name = '';
            if(!empty($user))
            {
                $full_name = $user->name;
            }
            
            
            $doc = DB::table('user_documents as d')
                ->select('d.file_name')
                ->where(['d.id' => $request->doc_id])->first();
                
            
            
            $doc_path = '';
            if(!empty($doc))
            {
                $doc_path = url(config('custom.doc_folder') . $doc->file_name);
            }
            
            
            
            $shareExp = explode(',',$request->share_to);
            
            
            
            foreach($shareExp as $key => $value)
            {
                
                $param = array(
                'full_name' => $full_name,
                'doc_path' => $doc_path,
                'email' => $value
                );
    
                Mail::send('emails.user.share-document', $param, function ($message) use ($param) {
                    $message->subject(config('custom.share_document'));
                    $message->to(trim($param['email']));
                });
            }
            
            
            $msg = "Document shared successfully";
            
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    
    ## Function to respond document access request
    public function respondDocumentAccessRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
            'action' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $param = array();
            $param['status'] = $request->action;
            $param['updated_at'] = $this->entryDate;
            DB::table('user_documents_access_requests')->where('id', $request->id)->update($param);
            $requestRow = DB::table('user_documents_access_requests as j')
                ->select('j.user_id','j.client_id')
                ->where(['j.id' => $request->id])
                ->first();
            
            $candidate = User::where('id', $requestRow->user_id)->first();
            $client = User::where('id', $requestRow->client_id)->first();
            
            if($request->action == 1)
            {
                $msg = "Document access request accepted successfully.";
                
                $param = array(
                    'candidate_name' => $candidate->name,
                    'client_name' => $client->name,
                    'email' => $candidate->email,
                    'dashboard_path' => config('custom.client_login_url'),
                    'message_text' => 'Your request for document access has been accepted by '.$candidate->name.'. You can now view the documents in your client portal.',
                );
    
                Mail::send('emails.user.response-accept-document-access-request', $param, function ($message) use ($param) {
                    $message->subject(config('custom.document_access_request_response'));
                    $message->to($param['email']);
                });
            }
            else if($request->action == 2)
            {
                $msg = "Document access request rejected successfully.";
                
                 $param = array(
                    'candidate_name' => $candidate->name,
                    'client_name' => $client->name,
                    'email' => $candidate->email,
                    'dashboard_path' => config('custom.client_login_url'),
                    'message_text' => 'Your request for document access has been rejected by '.$candidate->name.'. The candidate chose not to grant access to the documents at this time.',
                );
    
                Mail::send('emails.user.response-reject-document-access-request', $param, function ($message) use ($param) {
                    $message->subject(config('custom.document_access_request_response'));
                    $message->to($param['email']);
                });
            }
            
            
            
            
            
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
}
