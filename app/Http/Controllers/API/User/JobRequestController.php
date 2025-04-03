<?php

namespace App\Http\Controllers\API\User;

use App\Helper\CommonFunction;
use App\Http\Controllers\Controller;
use App\Models\JobRequest;
use App\Models\JobRequestStateCity;
use App\Models\SendJobOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobRequestController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function jobRequestList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {

            // Get the count of active job requests (status = 1)
            $activeJobRequestCount = JobRequest::where('user_id', $request->user_id)
                ->where('status', 1)
                ->count();

            $jobRequest = JobRequest::query()
                    ->leftJoin('users', 'job_requests.user_id', '=', 'users.id')
                    ->leftJoin('professions', 'job_requests.profession_id', '=', 'professions.id')
                    ->leftJoin('specialities', 'job_requests.speciality_id', '=', 'specialities.id')
                    ->leftJoin('shifts', 'job_requests.shift_id', '=', 'shifts.id')
                    ->leftJoin('employment_types', 'job_requests.employment_type_id', '=', 'employment_types.id')
                    ->leftJoin('user_documents', 'job_requests.user_document_id', '=', 'user_documents.id')
                    ->where('job_requests.user_id', $request->user_id);

            // Filter by status if specified
            if (isset($request->status) && in_array($request->status, [0, 1])) {
                $jobRequest->where('job_requests.status', $request->status);
            }

            // Filter by job request id
            if (!empty($request->job_request_id)) {
                $jobRequest->where('job_requests.id', $request->job_request_id);
            }
             
            $jobRequest = $jobRequest->select(
                'job_requests.*',
                'professions.profession',
                'specialities.specialty',
                'shifts.title as shift_title',
                'employment_types.title as employment_type_title',
                DB::raw("IFNULL(DATE_FORMAT(start_date, '%m/%d/%Y'), '') as start_date"),
                DB::raw("IFNULL(DATE_FORMAT(end_date, '%m/%d/%Y'), '') as end_date"),
                DB::raw("CASE WHEN job_requests.user_document_id IS NOT NULL THEN user_documents.file_name 
                            ELSE job_requests.file END as file"),
            )->get();
            // Step 3: Check if the JobRequest exists
            if (count($jobRequest) == 0) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'active_job_request' => $activeJobRequestCount,
                        'job_requests' => $jobRequest
                    ]
                ], 200);
            }

            foreach ($jobRequest as $value) {
                // Update File URL
                if (!empty($value->file)) {
                    $value->file = url(config('custom.doc_folder') . $value->file);
                }
                $stateCityData = '';
                $stateCityData = JobRequestStateCity::where('job_request_id', $value->id)
                    ->join('states', 'job_request_state_cities.state_id', '=', 'states.id') // Assuming `states` table exists
                    ->join('cities', 'job_request_state_cities.city_id', '=', 'cities.id')  // Assuming `cities` table exists
                    ->select('states.id as state_id', 'states.name as state_name', 'cities.city_name as city_name')
                    ->get();
                // Group data by states and attach it to the jobRequest object
                $groupedStateCityData = $stateCityData->groupBy('state_name')->map(function ($cities, $state) {
                    return [
                        'state' => $state,
                        'cities' => $cities->pluck('city_name')->toArray(),
                    ];
                })->values();
                $value->state_city_data = $groupedStateCityData;
            }

            // Step 4: Return the job request data
            return response()->json([
                'status' => true,
                'data' => [
                    'active_job_request' => $activeJobRequestCount,
                    'job_requests' => $jobRequest                   
                ]
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
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
            if(!empty($request->start_date) && !empty($request->end_date)) {
                $params['start_date'] = CommonFunction::changeDateFormat($request->start_date);
                $params['end_date'] = CommonFunction::changeDateFormat($request->end_date);
            }

            if(empty($request->job_request_id)) {
                $jobRequest = JobRequest::create($params);
                $message = 'Job request created successfully.';
            } else {
                // update here
                $jobRequest = JobRequest::find($request->job_request_id);
                if (!$jobRequest) {
                    $message = 'Job request not found!';
                } else {
                    // If upload document file by user
                    if ($request->file('file')) {
                        $file = $request->file('file');
                        $ext = $file->getClientOriginalExtension();
                        $fileName = time() * rand() . '.' . $ext;
                        $path = config('custom.doc_folder');
                        $upload = $file->move($path, $fileName);
                        if ($upload) {
                            $params['file'] = $fileName;
                        }
                    }
                    $jobRequest->update($params);
                    $message = 'Job request updated successfully.';
                }
            }

            DB::commit();
            return response()->json(['status' => true,'message' => $message,'data' => $jobRequest],200);

        } catch(\Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeStateCity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_request_id' => 'required|integer',
            'user_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_id' => 'required',
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
            // update states & cities
            $jobRequest = JobRequest::find($request->job_request_id);
            if (!$jobRequest) {
                $message = 'Job request not found!';
            } else {            
                // state and city stored & add new entries
                $state_id = $request->state_id;
                $cities = $request->city_id;
                if (!empty($state_id)) {
                    foreach ($cities as $city_id) {
                        DB::table('job_request_state_cities')->updateOrInsert([
                                'job_request_id' => $request->job_request_id,
                                'state_id' => $state_id,
                                'city_id' => $city_id],
                            [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );                
                    }
                }
                $message = 'State & city updated successfully.';
            }

            // Fetch the response data grouped by states
            $data = DB::table('job_request_state_cities')
                    ->where('job_request_id', $request->job_request_id)
                    ->join('states', 'job_request_state_cities.state_id', '=', 'states.id') // Assuming you have a `states` table
                    ->join('cities', 'job_request_state_cities.city_id', '=', 'cities.id')  // Assuming you have a `cities` table
                    ->select('states.name as state', 'cities.city_name as city')
                    ->get()
                    ->groupBy('state')
                    ->map(function ($group) {
                        return [
                            'state' => $group->first()->state,
                            'cities' => $group->pluck('city')->toArray(),
                        ];
                    })->values();
           
            DB::commit();
            return response()->json(['status' => true, 'message' => $message, 'data' => $data], 200);
        } catch (\Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Received Job Opportunites from client/employer
     */
    public function getReceivedJobOpportunites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            // Get the count of active job requests (status = 1)
            $receivedJobOpportunites = SendJobOpportunity::join('job_opportunities', 'send_job_opportunities.job_opportunity_id', '=', 'job_opportunities.id')
                    ->leftJoin('clients', 'job_opportunities.client_id', '=', 'clients.user_Id')
                    ->leftJoin('users', 'clients.user_Id', '=', 'users.id')
                    ->leftJoin('professions', 'job_opportunities.profession_id', '=', 'professions.id')
                    ->leftJoin('specialities', 'job_opportunities.speciality_id', '=', 'specialities.id')
                    ->leftJoin('states', 'job_opportunities.state_id', '=', 'states.id')
                    ->leftJoin('cities', 'job_opportunities.city_id', '=', 'cities.id')
                    ->leftJoin('shifts', 'job_opportunities.shift_id', '=', 'shifts.id')
                    ->leftJoin('employment_types', 'job_opportunities.employment_type_id', '=', 'employment_types.id')
                    ->where('send_job_opportunities.user_id', $request->user_id)
                    ->where('send_job_opportunities.is_delete', 0);

            $receivedJobOpportunites = $receivedJobOpportunites->select(
                                            'job_opportunities.*',
                                            DB::raw('IF(clients.company_name IS NOT NULL,clients.company_name, "Job Opportunity By Admin") as company_name'),
                                            DB::raw("IF(users.profile_pic IS NOT NULL, CONCAT('" . rtrim(url(config('custom.user_folder')), '/') . "/', users.profile_pic), NULL) as profile_pic"),
                                            'professions.profession',
                                            'specialities.specialty',
                                            'states.name as state_name',
                                            'cities.city_name as city_name',
                                            'shifts.title as shift_title',
                                            'employment_types.title as employment_type_title',
                                            'send_job_opportunities.id as send_job_opportunity_id',
                                            'send_job_opportunities.response as job_opportuniy_status'
                                        )
                                        ->groupBy('job_opportunities.id')
                                        ->get();
            // Step 4: Return the job request data
            return response()->json([
                'status' => true,
                'data' => $receivedJobOpportunites
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }

    /**
     * Opportunity Details 
     */
    public function getJobOpportinityDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'job_opportunity_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            // Get the count of active job requests (status = 1)
            $jobOpportunityDetails = SendJobOpportunity::join('job_opportunities', 'send_job_opportunities.job_opportunity_id', '=', 'job_opportunities.id')
                    ->leftJoin('clients as c', 'job_opportunities.client_id', '=', 'c.user_Id')
                    ->leftJoin('users', 'c.user_Id', '=', 'users.id')
                    ->leftJoin('professions', 'job_opportunities.profession_id', '=', 'professions.id')
                    ->leftJoin('specialities', 'job_opportunities.speciality_id', '=', 'specialities.id')
                    ->leftJoin('states', 'job_opportunities.state_id', '=', 'states.id')
                    ->leftJoin('cities', 'job_opportunities.city_id', '=', 'cities.id')
                    ->leftJoin('shifts', 'job_opportunities.shift_id', '=', 'shifts.id')
                    ->leftJoin('employment_types', 'job_opportunities.employment_type_id', '=', 'employment_types.id')
                    ->where('send_job_opportunities.job_opportunity_id', $request->job_opportunity_id);

            $jobOpportunityDetails = $jobOpportunityDetails->select(
                    'job_opportunities.*',
                    'users.unique_id as company_id',
                    'c.company_name',
                    'c.primary_industry',
                    'c.company_size',
                    'c.bio',
                    'c.founded_in',
                    DB::raw("IF(users.profile_pic IS NOT NULL, CONCAT('" . rtrim(url(config('custom.user_folder')), '/') . "/', users.profile_pic), NULL) as company_profile_pic"),
                    'professions.profession',
                    'specialities.specialty',
                    'states.name as state_name',
                    'cities.city_name as city_name',
                    'shifts.title as shift_title',
                    'employment_types.title as employment_type_title',
                    'send_job_opportunities.id as send_job_opportunity_id',
                    'send_job_opportunities.response as job_opportuniy_status'
                )
            ->first();

            // Step 4: Return the job request data
            return response()->json([
                'status' => true,
                'data' => $jobOpportunityDetails
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }


    /**
     * Delete job opportunity  
     */
    public function deleteJobOpportunity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'send_job_opportunity_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            // delete job opportunity by user
            DB::table('send_job_opportunities')->where('id',$request->send_job_opportunity_id)
            ->update([
                'is_delete' => 1
            ]);

            return response()->json([
                'status' => true,
                'message' => "This job opportunity has been deleted!"
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }



    /**
     *  Update Job Opportunity Response
    */
    public function updateJobOpportunityResponse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'send_job_opportunity_id' => 'required|integer',
            'response' => 'required'
        ]);
        if ($validator->fails()) {
            $result = [
                'status' => false,
                'message' => $validator->errors()->first(), // fetching validation error messages
            ];
            return response()->json($result, 422);
        }
        try {
            // Get the count of active job requests (status = 1)
            $updateJobResponse = SendJobOpportunity::where('id',$request->send_job_opportunity_id)
            ->update([
                'response' => $request->response
            ]);

            return response()->json([
                'status' => true,
                'data' => $updateJobResponse,
                'message' => "Job response successfully updated!"
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
            return response()->json($result);
        }
    }

}
