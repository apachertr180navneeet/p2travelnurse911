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

class AnnouncementController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to add/update announcement
    public function addEditAnnouncement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'announcement' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            $announcement = $request->announcement;

            foreach($announcement as $a) {
                $a = (object)$a;
                $param = [
                    'role_id' => $a->role_id,
                    'description' => $a->description
                ];
                
                if(isset($request->module) && !empty($request->module))
                    $param['module'] = $request->module;
                
                $exist = DB::table('announcements')->where('role_id', $a->role_id)->first();
                //return response()->json($exist);
                if($exist) {
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('announcements')->where('id', $exist->id)->update($param);
                }
                else {
                    $param['created_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('announcements')->insert($param);
                }
            }

            DB::commit();
            $result = array('status' => true, 'message' => "Announcement(s) has been updated successfully");
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get announcements
    public function getAnnouncements(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $query = DB::table('announcements as a')
                ->select('a.id', 'a.role_id', 'a.description', 'a.created_at');

            $announcements = $query->orderBy('a.id', 'desc')->get()->toArray();

            $result = array('status' => true, 'message' => (count($announcements)) . " Record found", 'data' => $announcements);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}
