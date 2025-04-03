<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AgencyController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to get AgencyFeedbacks
    public function getAgencyFeedbacks(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'client_id' => 'sometimes|required', // Validate only if client_id is present
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
                    'af.is_delete_request',
                    'af.delete_reason',
                    'af.created_at'
                )
                ->where('af.client_id', $request->client_id)
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


    ## Function to Delete Feedback Request
    public function deleteFeedbackRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer', // Validate id as required and integer
            'client_id' => 'required|integer',
            'is_delete_request' => 'required|integer',
            'delete_reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error occurred",
                'errors' => $validator->errors()
            ]);
        }

        try {
            // delete request to admin
            DB::table('agency_feedbacks')
                ->where('id', $request->id)
                ->update([
                    'is_delete_request' => 1,
                    'delete_reason' => $request->delete_reason,
                    'updated_at' => $this->entryDate,
                ]);
                
            $agency = DB::table('clients as c')
                ->where('c.user_Id', $request->client_id)
                ->first();
            $agencyName = $agency->company_name	 ?? 'agency';
            // Send email to admin
            $param = array(
                'title' => 'New delete review request received',
                'description' => "A new delete review request has been raised by $agencyName",
            );
            try {
                Mail::send('emails.deleteRequestReview', $param, function ($message) use ($param) {
                    $message->subject($param['title']);
                    $message->to(config('custom.email_delivery_to'));
                });
            } catch (\Exception $e) {
                dd($e->getMessage());
            }

            return response()->json([
                'status' => true,
                'message' => "Delete request sent successfully!"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    ## Function to get Declined Feedback Request
    public function getDeclinedFeedbackRequest(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'client_id' => 'sometimes|required', // Validate only if client_id is present
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
                ->where('af.client_id', $request->client_id)
                ->where('af.is_approved', 2)
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
    
    
}
