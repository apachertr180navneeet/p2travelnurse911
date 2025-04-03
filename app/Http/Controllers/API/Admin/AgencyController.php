<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use DB;
use Exception;

class AgencyController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to get Agency
    public function getAgency(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $agencies = DB::table('clients as c')
                ->join('agency_feedbacks as af','af.client_id','=','c.user_id')
                ->select(
                    'c.user_id as client_id',
                    'c.company_name as agency_name',
                    DB::raw('(SELECT COUNT(*) FROM users WHERE created_by = c.user_id) as recruiters_count'),
                    DB::raw('(SELECT COUNT(*) FROM jobs WHERE user_id = c.user_id) as jobs_count'),
                    DB::raw('ROUND(IFNULL((SELECT AVG(rating) FROM agency_feedbacks WHERE client_id = c.user_id AND is_approved = 1), 0), 1) as avg_rating'),
                    DB::raw('(SELECT COUNT(*) FROM agency_feedbacks WHERE client_id = c.user_id AND is_approved = 1) as review_count'),
                    DB::raw('(SELECT COUNT(*) FROM agency_feedbacks WHERE client_id = c.user_id AND is_delete_request = 1 AND is_approved = 1) as delete_request_count')
                )
                ->groupBy('af.client_id')
                ->get()
                ->toArray();

            $result = array('status' => true, 'message' => (count($agencies)) . " Record found", 'data' => $agencies);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get AgencyFeedbacks
    public function getAgencyFeedbacks(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'user_id' => 'sometimes|required', // Validate only if user_id is present
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error occurred"
            ]);
        }

        try {
            $agenciesFeedbacks = DB::table('agency_feedbacks as af')
                ->join('clients as c', 'c.user_id', '=', 'af.client_id')
                ->join('users as u', 'u.id', '=', 'af.user_id')
                ->select(
                    'u.name as user_name',
                    'c.company_name as agency_name',
                    'c.user_id as agency_id',
                    'af.id',
                    'af.rating',
                    'af.comments',
                    'af.status',
                    'af.is_approved',
                    'af.created_at'
                )
                ->where('af.client_id', $request->user_id)
                ->where('af.is_approved', 1)
                ->get()
                ->toArray();

            $result = [
                'status' => true,
                'message' => count($agenciesFeedbacks) . " Record(s) found",
                'data' => $agenciesFeedbacks
            ];
        } catch (Exception $e) {
            $result = [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($result);
    }

    ## Function to get AgencyFeedbacks
    public function getRecentFeedbacks(Request $request)
    {
        try {
            $query = DB::table('agency_feedbacks as af')
            ->join('clients as c', 'c.user_id', '=', 'af.client_id')
            ->join('users as u', 'u.id', '=', 'af.user_id')
            ->select(
                'u.name as user_name',
                'c.company_name as agency_name',
                'c.user_id as agency_id',
                'af.id',
                'af.rating',
                'af.comments',
                'af.status',
                'af.is_approved',
                'af.created_at'
            );

            $query->where('af.is_approved', 0); // Only approved feedbacks if user_id is null

            $agenciesFeedbacks = $query->orderBy('af.created_at', 'desc') // Order by most recent
            ->get()
            ->toArray();

            $result = [
                'status' => true,
                'message' => count($agenciesFeedbacks) . " Record(s) found",
                'data' => $agenciesFeedbacks
            ];
        } catch (Exception $e) {
            $result = [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($result);
    }

    ## Function to Approve Feedback
    public function approveFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer', // Validate id as required and integer
            'agency_id' => 'required|integer', // Validate id as required and integer
            'approved' => 'required|boolean', // Validate approved as required and boolean
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error occurred",
                'errors' => $validator->errors()
            ]);
        }

        try {
            if ($request->approved == 1) {
                // Update the feedback record if approved is 1
                $updateStatus = DB::table('agency_feedbacks')
                    ->where('id', $request->id)
                    ->update([
                        'status' => 1,
                        'is_approved' => $request->approved,
                        'updated_at' => $this->entryDate, // Assuming $this->entryDate is the current timestamp
                    ]);
    
                if ($updateStatus) {
                    return response()->json([
                        'status' => true,
                        'message' => "Feedback approved successfully."
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "Failed to update feedback approval status. Record not found or unchanged."
                    ]);
                }
            } else {
                // Delete the feedback record if approved is 0
                $deleteStatus = DB::table('agency_feedbacks')
                    ->where('id', $request->id)
                    ->delete();
    
                if ($deleteStatus) {
                    return response()->json([
                        'status' => true,
                        'message' => "Feedback declined successfully."
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "Failed to delete feedback record. Record not found."
                    ]);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    ## Function to Delete Feedback Request
    public function getDeleteRequestFeedbacks(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'user_id' => 'sometimes|required', // Validate only if client_id is present
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error occurred"
            ]);
        }

        try {
            $agenciesFeedbacks = DB::table('agency_feedbacks as af')
                ->join('users as u', 'u.id', '=', 'af.user_id')
                ->join('clients as c', 'c.user_id', '=', 'af.client_id')
                ->select(
                    'u.id as user_id',
                    'u.name as user_name',
                    'c.company_name as agency_name',
                    'c.id as agency_id',
                    'af.id',
                    'af.rating',
                    'af.comments',
                    'af.status',
                    'af.is_approved',
                    'af.delete_reason',
                    'af.created_at'
                )
                ->where('af.is_approved','!=', 2)
                ->where('af.is_delete_request',1)
                ->get()
                ->toArray();

            $result = [
                'status' => true,
                'message' => count($agenciesFeedbacks) . " Record(s) found",
                'data' => $agenciesFeedbacks
            ];
        } catch (Exception $e) {
            $result = [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($result);
    }

    ## Function to Delete Feedback
    public function deleteRequestFeedbacks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer', // Validate id as required and integer
            'user_id' => 'required|integer', // Validate id as required and integer
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error occurred",
                'errors' => $validator->errors()
            ]);
        }

        try {
            $deleteFeedback = DB::table('agency_feedbacks')
                                ->where('id', $request->id)
                                ->delete();

            if ($deleteFeedback) {
                return response()->json([
                    'status' => true,
                    'message' => "Feedback deleted successfully!"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Failed to delete feedback record. Record not found."
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    ## Function to declined Feedback Request
    public function declinedFeedbackRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer', // Validate id as required and integer
            'user_id' => 'required|integer', // Validate id as required and integer
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error occurred",
                'errors' => $validator->errors()
            ]);
        }
        try {
            $declinedFeedback = DB::table('agency_feedbacks')
                                ->where('id', $request->id)
                                ->update([
                                    'is_approved' => 2,
                                    'delete_reason' => $request->reason,
                                    'updated_at' => $this->entryDate, // Assuming $this->entryDate is the current timestamp
                                ]);

            if ($declinedFeedback) {
                return response()->json([
                    'status' => true,
                    'message' => "Feedback declined successfully!"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Failed to decline feedback request. Record not found."
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}
