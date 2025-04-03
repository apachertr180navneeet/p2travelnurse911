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
            'announcements' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            $announcements = $request->announcements;

            foreach($announcements as $a) {
                $a = (object)$a;
                $param = [
                    'description' => $a->description,
                    'module' => $a->module,
                ];
                
                if(isset($request->module) && !empty($request->module)){
                    $param['module'] = $request->module;
                }
                
                $exist = DB::table('announcements')->where('module', $a->module)->first();
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
        // Validate the input
        $validator = Validator::make($request->all(), [
            'module' => 'nullable|string',  // Make 'module' optional but a valid string if provided
        ]);

        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            // Start the query on the announcements table
            $query = DB::table('announcements as a')
                ->select('a.id', 'a.module', 'a.description', 'a.created_at');

            // Check if 'module' is provided and add the where clause for filtering
            if ($request->has('module') && $request->module) {
                $query->where('a.module', $request->module); // Adjust the field name as needed
            }

            // Fetch the announcements
            $announcements = $query->orderBy('a.id', 'desc')->get()->toArray();

            // Prepare the response with the announcement data
            $result = array('status' => true, 'message' => (count($announcements)) . " Record(s) found", 'data' => $announcements);
        } catch (Exception $e) {
            // Catch any errors and return the message
            $result = array('status' => false, 'message' => $e->getMessage());
        }

        return response()->json($result);
    }
}
