<?php

namespace App\Http\Controllers\API\Admin;

use App\Helper\CommonFunction;
use App\Http\Controllers\Controller;
use App\Models\JobOpportunity;
use App\Models\JobRequest;
use App\Models\JobRequestStateCity;
use App\Models\SendJobOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class JobRequestController extends Controller
{
    /**
     *  Create Job Opportunity
     */
    public function createJobOpportunity(Request $request) {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            DB::beginTransaction();
            // create new job request
            $params = $request->except('token'); // Removes 'token' if present

            // change date format if exists in params
            if (!empty($request->start_date)) {
                $params['start_date'] = CommonFunction::changeDateFormat($request->start_date);
            }

            if (empty($request->job_opportunity_id)) {
                $jobOpportunity = JobOpportunity::create($params);
                $message = 'Job Opportunity created successfully.';
            } else {
                // update here
                $jobOpportunity = JobOpportunity::find($request->job_opportunity_id);
                if (!$jobOpportunity) {
                    $message = 'Job Opportunity not found!';
                } else {
                    $jobOpportunity->update($params);
                    $message = 'Job Opportunity updated successfully.';
                }
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => $message, 'data' => $jobOpportunity], 200);

        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }


    /**
     * Get All job request list
     */
    public function getAllJobRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            // Fetch unmatched requests list 
            $allJobRequests = JobRequest::query()
                ->join('job_request_state_cities', 'job_requests.id', '=', 'job_request_state_cities.job_request_id')
                ->leftJoin('users', 'job_requests.user_id', '=', 'users.id')
                ->leftJoin('professions', 'job_requests.profession_id', '=', 'professions.id')
                ->leftJoin('specialities', 'job_requests.speciality_id', '=', 'specialities.id')
                ->leftJoin('shifts', 'job_requests.shift_id', '=', 'shifts.id')
                ->leftJoin('employment_types', 'job_requests.employment_type_id', '=', 'employment_types.id')
                ->leftJoin('user_resumes', 'job_requests.user_document_id', '=', 'user_resumes.id')
                ->leftJoin('hide_job_requests', 'job_requests.id', '=', 'hide_job_requests.job_request_id')
                ->where('job_requests.status', JobRequest::STATUS_ACTIVE);

        
            if ($request->filled('state_id')) {
                $allJobRequests->where('job_request_state_cities.state_id', $request->state_id);
            }

            if ($request->filled('name')) {
                $allJobRequests->where('users.name', 'like', '%' . $request->name . '%');
            }

            if ($request->filled('sort_by')) {
                $orderBy = $request->sort_by ? 'desc' : 'asc';
                $allJobRequests->orderBy('job_requests.created_at', $orderBy);
            }
            $allJobRequests = $allJobRequests->select(
                                        'job_requests.*',
                                        'users.name as user_name',
                                        'users.email as user_email',
                                        'users.profile_pic as profile_pic',
                                        'professions.profession',
                                        'specialities.specialty',
                                        'shifts.title as shift_title',
                                        'employment_types.title as employment_type_title',
                                        DB::raw("IFNULL(DATE_FORMAT(job_requests.start_date, '%m/%d/%Y'), '') as start_date"),
                                        DB::raw("IFNULL(DATE_FORMAT(job_requests.end_date, '%m/%d/%Y'), '') as end_date"),
                                        DB::raw("CASE WHEN job_requests.user_document_id IS NOT NULL THEN user_resumes.file_name 
                                                                        ELSE job_requests.file END as file"),
                                    )
                                    ->orderBy('job_requests.id','desc')
                                    ->groupBy('job_requests.id')
                                    ->get();

            $jobRequestIds = $allJobRequests->pluck('id')->toArray();

            $stateCityData = JobRequestStateCity::whereIn('job_request_id', $jobRequestIds)
                            ->join('states', 'job_request_state_cities.state_id', '=', 'states.id')
                            ->join('cities', 'job_request_state_cities.city_id', '=', 'cities.id')
                            ->select('job_request_id', 'states.name as state_name', 'cities.city_name as city_name')
                            ->get()
                            ->groupBy('job_request_id');

            $allJobRequests->each(function ($request) use ($stateCityData) {
                // Update file URL
                if (!empty($request->file)) {
                    $request->file = url(config('custom.doc_folder') . $request->file);
                }

                if (!empty($request->profile_pic)) {
                    $request->profile_pic = url(config('custom.user_folder') . $request->profile_pic);
                }

                // Get state and city data for the current request
                $stateCity = $stateCityData->get($request->id, collect()); // Ensure it's a collection
                // Group and map the state and city data
                $groupedStateCityData = $stateCity
                    ->groupBy('state_name')
                    ->map(function ($cities, $state) {
                        return [
                            'state' => $state,
                            'cities' => $cities->pluck('city_name')->toArray(),
                        ];
                    })
                    ->values();
                $request->state_city_data = $groupedStateCityData;
            });

            $allJobRequests->each(function ($request) {
                // Group multiple job opportunities by job_request_id
                $jobOpportunityList = JobOpportunity::query()
                    ->join('send_job_opportunities', 'job_opportunities.id', '=', 'send_job_opportunities.job_opportunity_id')
                    ->leftjoin('clients', 'job_opportunities.client_id', '=', 'clients.user_Id')
                    ->leftJoin('professions', 'job_opportunities.profession_id', '=', 'professions.id')
                    ->leftJoin('specialities', 'job_opportunities.speciality_id', '=', 'specialities.id')
                    ->leftJoin('states', 'job_opportunities.state_id', '=', 'states.id')
                    ->leftJoin('cities', 'job_opportunities.city_id', '=', 'cities.id')
                    ->leftJoin('shifts', 'job_opportunities.shift_id', '=', 'shifts.id')
                    ->leftJoin('employment_types', 'job_opportunities.employment_type_id', '=', 'employment_types.id')
                    ->where('send_job_opportunities.job_request_id', $request->id)
                    ->get([
                        'job_opportunities.title',      
                        'job_opportunities.client_id',                      
                        'job_opportunities.pay_rate',                       
                        DB::raw('IF(clients.company_name IS NOT NULL,clients.company_name, "Admin") as company_name'),
                        'professions.profession',
                        'specialities.specialty',
                        'states.name as state_name',
                        'cities.city_name as city_name',
                        'shifts.title as shift_title',
                        'employment_types.title as employment_type_title',
                        'send_job_opportunities.job_opportunity_id',
                        'send_job_opportunities.id as send_job_opportunity_id',               
                        'send_job_opportunities.response as user_response',
                        'send_job_opportunities.created_at as sent_date'
                    ]);
                $request->job_opportunities = $jobOpportunityList;
            });

            return response()->json([
                'status' => true,
                'data' => $allJobRequests
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }

    /**
     * Get job request details
     */
    public function getJobRequestDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_request_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            $jobRequest = JobRequest::query()
                                ->join('users', 'job_requests.user_id', '=', 'users.id')
                                ->leftJoin('professions', 'job_requests.profession_id', '=', 'professions.id')
                                ->leftJoin('specialities', 'job_requests.speciality_id', '=', 'specialities.id')
                                ->leftJoin('shifts', 'job_requests.shift_id', '=', 'shifts.id')
                                ->leftJoin('employment_types', 'job_requests.employment_type_id', '=', 'employment_types.id')
                                ->leftJoin('user_resumes', 'job_requests.user_document_id', '=', 'user_resumes.id')                            
                                ->where('job_requests.id', $request->job_request_id)
                                ->select(
                                    'job_requests.*',
                                    'users.name as user_name',
                                    'users.email as user_email',
                                    DB::raw("IF(users.profile_pic IS NOT NULL, CONCAT('" . rtrim(url(config('custom.user_folder')), '/') . "/', users.profile_pic), NULL) as profile_pic"),
                                    'professions.profession',
                                    'specialities.specialty',
                                    'shifts.title as shift_title',
                                    'employment_types.title as employment_type_title',
                                    DB::raw("IFNULL(DATE_FORMAT(job_requests.start_date, '%m/%d/%Y'), '') as start_date"),
                                    DB::raw("IFNULL(DATE_FORMAT(job_requests.end_date, '%m/%d/%Y'), '') as end_date"),
                                    DB::raw("CASE WHEN job_requests.user_document_id IS NOT NULL THEN user_resumes.file_name 
                                    ELSE job_requests.file END as file")
                                )
                                ->first();

            // Ensure the job request is found
            if (!$jobRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'Job request not found'
                ], 200);
            }
            // Fetch related state and city data
            $stateCityData = JobRequestStateCity::where('job_request_id', $jobRequest->id)
                                ->join('states', 'job_request_state_cities.state_id', '=', 'states.id')
                                ->join('cities', 'job_request_state_cities.city_id', '=', 'cities.id')
                                ->select('job_request_id', 'job_request_state_cities.state_id', 'states.name as state_name', 'cities.city_name as city_name')
                                ->get()
                                ->groupBy('state_name');

            $stateIDs = [];
            foreach ($stateCityData as $group) {
                foreach ($group as $item) {
                    $stateIDs[] = $item['state_id'];
                }
            }

            // Map the state and city data
            $jobRequest->state_city_data = $stateCityData->map(function ($cities, $state) {
                return [
                    'state' => $state, // The state name
                    'cities' => $cities->pluck('city_name')->toArray(), // List of city names under the state
                ];
            })->values();

            // Job opportunities
            $jobOpportunityList = JobOpportunity::query()
                            ->join('send_job_opportunities', 'job_opportunities.id', '=', 'send_job_opportunities.job_opportunity_id')
                            ->join('job_requests', 'send_job_opportunities.job_request_id', '=', 'job_requests.id')
                            ->leftJoin('specialities', 'job_opportunities.speciality_id', '=', 'specialities.id')
                            ->leftJoin('professions', 'job_opportunities.profession_id', '=', 'professions.id')
                            ->leftJoin('states', 'job_opportunities.state_id', '=', 'states.id')
                            ->leftJoin('cities', 'job_opportunities.city_id', '=', 'cities.id')
                            ->leftJoin('clients', 'job_opportunities.client_id', 'clients.user_id')
                            ->where('send_job_opportunities.user_id', $jobRequest->user_id)
                            ->whereNull('send_job_opportunities.job_id');
            $jobOpportunityList = $jobOpportunityList->select(
                                    'job_opportunities.*',
                                    'job_opportunities.id as job_opportunity_id',
                                    DB::raw('IF(clients.company_name IS NOT NULL,clients.company_name, "Admin") as company_name'),
                                    'states.name as state_name',
                                    'cities.city_name as city_name',
                                    'professions.profession',
                                    'specialities.specialty',
                                    'send_job_opportunities.job_request_id',
                                    'send_job_opportunities.id as send_job_opportunity_id',
                                    'send_job_opportunities.response as user_response'
                                )
                                ->orderBy('job_opportunities.id','desc')
                                ->get();
            $jobRequest->job_opportunities = $jobOpportunityList;

            // Job post matches 
            $jobPostMatched = DB::table('jobs')
                                ->leftJoin('send_job_opportunities', function ($join) use ($jobRequest) {
                                    $join->on('jobs.id', '=', 'send_job_opportunities.job_id')
                                        ->where('send_job_opportunities.job_request_id', '=', $jobRequest->id); // Condition on job_request_id
                                })
                                ->where('specialty_id', '=', $jobRequest->speciality_id)
                                ->where('shift_id', '=', $jobRequest->shift_id)
                                ->whereIn('state_id',$stateIDs)
                                ->select('jobs.id as job_id',
                                    'jobs.user_id as client_id',
                                    'title','unique_id','profession_id','specialty_id','start_date','end_date',
                                    'total_opening','is_urgent','state_id','city_id',
                                    'salary_start_range','salary_end_range',
                                    'salary_type','show_pay_rate','employment_type_id',
                                    'min_work_per_week','description','qualification',
                                    'responsibilities','is_starred',
                                    'send_job_opportunities.id as send_job_opportunity_id',
                                    'send_job_opportunities.response as user_response'
                                )
                                ->first();
            $jobRequest->job_post_matched = $jobPostMatched;

            return response()->json([
                'status' => true,
                'data' => $jobRequest
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }

    /**
     *  Send Job Opportunity
     */
    public function sendJobOpportunity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer',
            'user_id' => 'required|integer',
            'job_request_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            DB::beginTransaction();
            // create new job request
            $params = $request->except('token'); // Removes 'token' if present

            if(!empty($request->job_id)) {
                // First create opportunity same as job post
                $jobDetails = DB::table('jobs')
                                    ->where('id',$request->job_id)
                                    ->select(
                                        'user_id as client_id',
                                        'title','profession_id','specialty_id as speciality_id','start_date',
                                        'state_id','city_id','shift_id',                                      
                                        'salary_type as pay_type','show_pay_rate as pay_rate',
                                        'employment_type_id'
                                    )
                                    ->first();
                $createParams = (array) $jobDetails;
                // Create Opportunity 
                $jobOpportunity = JobOpportunity::create($createParams);
                // Send Opportunity
                $sendJobOpportunity = SendJobOpportunity::updateOrCreate(
                    [
                        'job_opportunity_id' => $jobOpportunity->id,
                        'job_request_id' => $params['job_request_id'],
                        'job_id' => $request->job_id,
                    ],
                    $params // Update data if exists or create new
                );
            } else {
                $sendJobOpportunity = SendJobOpportunity::updateOrCreate(
                    [
                        'job_opportunity_id' => $params['job_opportunity_id'],
                        'job_request_id' => $params['job_request_id']
                    ],
                    $params // Update data if exists or create new
                );
            }

            $message = 'Job Opportunity send successfully!';
            DB::commit();
            return response()->json(['status' => true, 'message' => $message, 'data' => $sendJobOpportunity], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }

    /**
     *  Job Opportunity Listing
     */
    public function jobOpportunityList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            $jobOpportunityList = JobOpportunity::query()
                ->join('send_job_opportunities', 'job_opportunities.id', '=', 'send_job_opportunities.job_opportunity_id')
                ->join('job_requests', 'send_job_opportunities.job_request_id', '=', 'job_requests.id')
                ->leftJoin('specialities', 'job_opportunities.speciality_id', '=', 'specialities.id')
                ->leftJoin('professions', 'job_opportunities.profession_id', '=', 'professions.id')
                ->leftJoin('states', 'job_opportunities.state_id', '=', 'states.id')
                ->leftJoin('cities', 'job_opportunities.city_id', '=', 'cities.id')
                ->where('job_opportunities.client_id', $request->client_id);

            if($request->job_request_id) {
                $jobOpportunityList->where('send_job_opportunities.job_request_id', $request->job_request_id);
            }

            $jobOpportunityList = $jobOpportunityList->select(
                                                'job_opportunities.*',
                                                'job_opportunities.id as job_opportunity_id',
                                                'states.name as state_name',
                                                'cities.city_name as city_name',
                                                'professions.profession',
                                                'specialities.specialty',
                                                'send_job_opportunities.job_request_id',
                                                'send_job_opportunities.response as user_response'
                                            )
                                            ->get();

            return response()->json([
                'status' => true,
                'data' => $jobOpportunityList
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }

    /*
     * Hide Job request 
    */
    public function hideJobRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer',
            'job_request_id' => 'required|integer',
            'is_hide' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            // Hide Job request
            $params = [
                'client_id' => $request->client_id,
                'job_request_id' => $request->job_request_id,
                'is_hide' => $request->is_hide,
                'created_at' => now(),
                'updated_at' => now() // Ensure updated_at field gets updated
            ];
            // Insert or update the record in the database
            DB::table('hide_job_requests')->updateOrInsert(
                [
                    'client_id' => $request->client_id,
                    'job_request_id' => $request->job_request_id, // Match on user_id and job_request_id
                ],
                $params // Data to insert or update
            );

            return response()->json([
                'status' => true,
                'message' => "Job Request hide successfully!"
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }

    ## Function to insert job message
    public function sendJobRequestMsg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'client_name' => 'required',
            'user_id' => 'required',
            'message' => 'required',
            'job_request_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $agencyUser = DB::table('clients as c')
                ->where(['user_Id' => $request->client_id])
                ->select('c.id as client_id')
                ->first();

            $user = DB::table('users as u')
                ->select('u.id', 'u.name', 'u.email')
                ->where(['u.id' => $request->user_id])
                ->first();

            $jobChatResult = DB::table('job_chats as jc')
                ->select('jc.id')
                ->where(['jc.job_request_id' => $request->job_request_id, 'jc.job_seeker_id' => $user->id, 'employer_id' => $agencyUser->client_id])
                ->first();

            if (empty($jobChatResult)) {
                $chatParam = array(
                    'job_request_id' => $request->job_request_id,
                    'job_seeker_id' => $request->user_id,
                    'employer_id' => $agencyUser->client_id,
                    'created_by' => $request->client_id,
                    'created_at' => date("Y-m-d H:i:s")
                );
                $job_chat_id = DB::table('job_chats')->insertGetId($chatParam);
            } else {
                $job_chat_id = $jobChatResult->id;
            }

            $param = array(
                'job_chat_id' => $job_chat_id,
                'to_id' => $request->user_id,
                'from_id' => $request->client_id,
                'message' => $request->message,
                'created_by' => $request->client_id,
                'created_at' => date("Y-m-d H:i:s")
            );
            $last_id = DB::table('job_chat_messages')->insertGetId($param);
            if ($last_id) {
                $param = array(
                    'receiver_name' => $user->name,
                    'receiver_email' => $user->email,
                    'sender_name' => $request->client_name,
                    'message' => $request->message,
                    'dashboard_path' => config('custom.login_url')
                );
                Mail::send('emails.client.job-request-message', $param, function ($message) use ($param) {
                    $message->subject($param['sender_name'] . ' ' . config('custom.job_message'));
                    $message->to($param['receiver_email']);
                });

                DB::commit();
                $result = array('status' => true, 'message' => "Message has been successfully posted");
            } else {
                $result = array('status' => true, 'message' => "Message has not been sent!");
            }     
        } catch (\Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Delete Job Opportunity
    */
    public function deleteJobOpportunity(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer',
            'job_opportunity_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            // Delete Job Opportunity
            DB::table('job_opportunities')->where('id', $request->job_opportunity_id)->delete();
            // also delete send opportunities 
            DB::table('send_job_opportunities')->where('job_opportunity_id', $request->job_opportunity_id)->delete();
            return response()->json([
                'status' => true,
                'message' => "Job Opportunity deleted successfully!"
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }

}
