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
use Carbon\Carbon;

class UserController extends Controller
{
    private $entryDate;
    public function __construct()
    {
        $this->entryDate = date("Y-m-d H:i:s");
    }

    ## Function to get users by their role id
    public function getUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'role_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            /*DB::enableQueryLog();*/

            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            $users = DB::table('users as u')
                ->leftJoin('user_preferred_states as ups', 'u.id', '=', 'ups.user_id')
                ->leftJoin('states as s2', 'ups.state_id', '=', 's2.id')
                ->leftJoin('user_details as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->leftJoin('job_applications as ja', 'u.id', '=', 'ja.user_id')
                ->leftJoin('jobs as j', 'j.id', '=', 'ja.job_id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('professions as p', 'ud.profession_id', '=', 'p.id')
                ->leftJoin('users as u2', 'u.created_by', '=', 'u2.id')
                ->select(
                    'u.id',
                    'u.name',
                    'u.unique_id',
                    'u.email',
                    'u.phone',
                    'u.created_at',
                    'u.profile_pic',
                    'u.status',
                    's.name as state_name',
                    's.code as state_code',
                    'city.city_name',
                    'u.status as user_status',
                    'u2.name as creator_name',
                    'u2.role_id as creator_role_id',
                    'u2.unique_id as creator_unique_id',
                    'j.title as job_title',
                    'ud.state_id',
                    'ud.city_id',
                    'ud.bio',
                    'j.unique_id as job_unique_id',
                    'ja.status as job_status',
                    'sp.specialty',
                    /*DB::raw('GROUP_CONCAT(DISTINCT s2.name ORDER BY s2.name SEPARATOR ", ") as desired_states'),*/
                    DB::raw('CASE 
                        WHEN FIND_IN_SET("all", GROUP_CONCAT(ups.state_id)) THEN "All States"
                        ELSE GROUP_CONCAT(DISTINCT s2.code ORDER BY s2.code SEPARATOR ", ")
                     END as desired_states'),
                    'p.profession'
                )
                ->when($request->role_id == 3 || $request->role_id == 2, function ($query) {
                    return $query->leftJoin('clients as c', 'c.user_id', '=', 'u.id')
                        ->addSelect('c.company_name', 'c.website', 'c.primary_industry', 'c.company_size', 'c.bio as company_bio', 'c.address_line1', 'c.address_line2', 'c.founded_in', 'c.social_media_links'); // Add specific column from clients table
                })
                ->where('u.role_id', $request->role_id)
                ->whereNull('u.deleted_at');

            if (isset($request->keyword) && !empty($request->keyword)) {
                $users->where('u.name', 'LIKE', "%{$request->keyword}%");
            }
            /*
            if (isset($request->state_id) && !empty($request->state_id)) {
                $users->where('ud.state_id', $request->state_id);
            }
            */
            if (isset($request->state_id) && !empty($request->state_id)) {
                $users->whereExists(function ($query) use ($request) {
                    $query->select(DB::raw(1))
                        ->from('user_preferred_states as ups')
                        ->whereColumn('ups.user_id', 'u.id')
                        ->where('ups.state_id', $request->state_id);
                });
            }
            if (isset($request->profession_id) && !empty($request->profession_id)) {
                $users->where('j.profession_id', $request->profession_id);
            }
            if (isset($request->speciality_id) && !empty($request->speciality_id)) {
                $users->where('j.specialty_id', $request->speciality_id);
            }

            if (isset($request->status) && $request->status != 'all') {
                $users->where('ja.status', $request->status);
            }

            if (isset($request->user_status) && $request->user_status != 'all') {
                $users->where('u.status', $request->user_status);
            } else {
                $users->where('u.status', 1);
            }


            $users = $users->orderBy('u.id', 'desc')
                ->groupBy('u.id')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = !empty($users->profile_pic) ? url(config('custom.user_folder') . $users->profile_pic) : '';

                    if (isset($users->social_media_links) && !empty($users->social_media_links)) {
                        $users->facebook_url = $users->twitter_url = $users->instagram_url = $users->linkedin_url = "";

                        $social_links = json_decode($users->social_media_links, true);
                        if (!empty($social_links)) {
                            foreach ($social_links as $k => $v) {
                                if ($v['platform'] == 'Facebook' && !empty($v['url']) && $v['url'] != null)
                                    $users->facebook_url = $v['url'];
                                else if ($v['platform'] == 'Twitter' && !empty($v['url']) && $v['url'] != null)
                                    $users->twitter_url = $v['url'];
                                else if ($v['platform'] == 'Instagram' && !empty($v['url']) && $v['url'] != null)
                                    $users->instagram_url = $v['url'];
                                else if ($v['platform'] == 'Linkedin' && !empty($v['url']) && $v['url'] != null)
                                    $users->linkedin_url = $v['url'];
                            }
                        }
                    }
                    return $users;
                })->toArray();

            /*dd(DB::getQueryLog());*/

            $result = array('status' => true, 'message' => (count($users)) . " Record found", 'data' => $users);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }



    ## Function to get all users(Applicants/Candidates/Employees) by appling filters
    public function getAllUsers(Request $request)
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

            $users = DB::table('users as u')
                ->leftJoin('user_preferred_states as ups', 'u.id', '=', 'ups.user_id')
                ->leftJoin('states as s2', 'ups.state_id', '=', 's2.id')
                ->leftJoin('user_details as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->leftJoin('specialities as sp', 'ud.specialty_id', '=', 'sp.id')
                ->leftJoin('users as u2', 'u2.id', '=', 'u.created_by')
                ->select(
                    'u.id',
                    'u.name',
                    'u.unique_id',
                    'u.email',
                    'u.phone',
                    'u.created_at',
                    'u.profile_pic',
                    's.name as state_name',
                    's.code as state_code',
                    'city.city_name',
                    'u.status as user_status',
                    'sp.specialty',
                    'ud.state_id',
                    'ud.city_id',
                    'ud.bio',
                    'u2.name as creator_name',
                    'u2.unique_id as creator_unique_id',
                    'u2.role_id as creator_role_id',
                    DB::raw('GROUP_CONCAT(DISTINCT s2.name ORDER BY s2.name SEPARATOR ", ") as desired_states')
                )
                ->where(function ($query) use ($request) {
                    $query->where('u.role_id',  4)
                        ->orWhere('u.role_id',  5)
                        ->orWhere('u.role_id',  9);
                })
                ->where('u.deleted_at', NULL)
                ->where('ud.searchable_profile', 1);

            if (isset($request->keyword) && !empty($request->keyword)) {
                $users->where('u.name', 'LIKE', "%{$request->keyword}%");
            }
            /*
            if (isset($request->state_id) && !empty($request->state_id)) {
                $users->where('ups.user_id', $request->user_id)->where('ups.state_id', $request->state_id);
            }
            */
            if (isset($request->state_id) && !empty($request->state_id)) {
                $users->whereExists(function ($query) use ($request) {
                    $query->select(DB::raw(1))
                        ->from('user_preferred_states as ups')
                        ->whereColumn('ups.user_id', 'u.id')
                        ->where('ups.state_id', $request->state_id);
                });
            }
            if (isset($request->speciality_id) && !empty($request->speciality_id)) {
                $users->where('ud.specialty_id', $request->speciality_id);
            }
            if ((isset($request->start_date) && !empty($request->start_date)) || isset($request->end_date) && !empty($request->end_date)) {
                if (isset($request->start_date) && !empty($request->start_date))
                    $users->where('u.created_at', '>=', $request->start_date);

                if (isset($request->end_date) && !empty($request->end_date))
                    $users->where('u.created_at', '<=', $request->end_date);
            }

            $users = $users->orderBy('u.id', 'desc')
                ->groupBy('u.id')
                ->get()
                ->map(function ($users) {
                    // Add dir_path column and its value to each record
                    $users->profile_pic_path = (!empty($users->profile_pic)) ? url(config('custom.user_folder') . $users->profile_pic) : ''; // Adjust 'your/directory/path/' to the actual directory path
                    return $users;
                })
                ->toArray();

            $result = array('status' => true, 'message' => (count($users)) . " Record found", 'data' => $users);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## Function to get interviewers
    public function getInterviwers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'job_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

            $jobRow = DB::table('jobs as j')
                ->join('users as u', 'u.id', '=', 'j.user_id')
                ->select('u.role_id')
                ->where(['j.id' => $request->job_id])
                ->first();

            if ($jobRow->role_id == 2 || $jobRow->role_id == 3) {
                $users = DB::table('users as u')
                    ->select('u.id', 'u.name')
                    ->where('u.role_id', 6)
                    ->where('u.created_by', $request->user_id)
                    ->where('u.deleted_at', NULL)
                    ->orderBy('u.name')->get()->toArray();
            } else {
                $users = DB::table('users as u')
                    ->select('u.id', 'u.name')
                    ->where('u.role_id', 6)
                    ->where('u.deleted_at', NULL)
                    ->orderBy('u.name')->get()->toArray();
            }



            $result = array('status' => true, 'message' => (count($users)) . " Record found", 'data' => $users);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## add/update user Function
    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            if (isset($request->id) && !empty($request->id)) {
                # Check Email is already registered or not
                $query = CommonFunction::checkEmailExist($request->email, $request->role_id, $request->id);
                if ($query) {
                    $result = array('status' => false, 'message' => "Email is already registered");
                    return response()->json($result);
                }

                $param = array(
                    'name' => strip_tags($request->name),
                    'email' => strip_tags($request->email),
                    'phone' => strip_tags($request->phone),
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                );
                if (isset($request->password) && !empty($request->password)) {
                    $param['password'] = Hash::make($request->password);
                }
                DB::table('users')->where('id', $request->id)->update($param);

                # Get country_id from state_id
                $country_id = 0;
                $state = DB::table('states')->select('country_id')
                    ->where('id', $request->state_id)->first();
                if (!empty($state)) {
                    $country_id = $state->country_id;
                }

                # Update entry in user_details
                $param = [
                    'user_id' => $request->id,
                    'bio' => strip_tags($request->bio),
                    'country_id' => $country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'updated_at' => $this->entryDate
                ];
                DB::table('user_details')->where('user_id', $request->id)->update($param);

                $request->social_media_links = array();

                if ((isset($request->facebook_url) && !empty($request->facebook_url))) {
                    $request->social_media_links[] = array(
                        'platform' => 'Facebook',
                        'url' => $request->facebook_url
                    );
                }

                if ((isset($request->twitter_url) && !empty($request->twitter_url))) {
                    $request->social_media_links[] = array(
                        'platform' => 'Twitter',
                        'url' => $request->twitter_url
                    );
                }

                if ((isset($request->instagram_url) && !empty($request->instagram_url))) {
                    $request->social_media_links[] = array(
                        'platform' => 'Instagram',
                        'url' => $request->instagram_url
                    );
                }

                if ((isset($request->linkedin_url) && !empty($request->linkedin_url))) {
                    $request->social_media_links[] = array(
                        'platform' => 'Linkedin',
                        'url' => $request->linkedin_url
                    );
                }

                # Create entry in client table
                $param = [
                    'company_name' => $request->company_name,
                    'website' => $request->website,
                    'primary_industry' => $request->primary_industry,
                    'company_size' => $request->company_size,
                    'bio' => strip_tags($request->company_bio),
                    'address_line1' => $request->address_line1,
                    'address_line2' => $request->address_line2,
                    'founded_in' => $request->founded_in,
                    'social_media_links' => $request->social_media_links,
                    'created_at' => $this->entryDate
                ];

                DB::table('clients')->where('user_id', $request->id)->update($param);

                DB::commit();

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $result = array('status' => true, 'message' => $role . " updated successfully");
            } else {
                # Check Email is already registered or not
                $query = CommonFunction::checkEmailExist($request->email, $request->role_id);
                if ($query) {
                    $result = array('status' => false, 'message' => "Email is already registered");
                    return response()->json($result);
                }

                # Create the user
                $user = new User;
                $user->unique_id = $this->generateUniqueCode(8);
                $user->name = strip_tags($request->name);
                $user->email = strip_tags($request->email);
                $user->phone = strip_tags($request->phone);
                $user->password = Hash::make($request->password);
                $user->role_id = $request->role_id;
                $user->created_at = $this->entryDate;
                $user->created_by = $request->user_id;
                $user->status = 1;
                $user->save();

                if ($user->id) {

                    # Get country_id from state_id
                    $country_id = 0;
                    $state = DB::table('states')->select('country_id')
                        ->where('id', $request->state_id)->first();
                    if (!empty($state)) {
                        $country_id = $state->country_id;
                    }

                    # Create entry in user_details
                    $param = [
                        'user_id' => $user->id,
                        'bio' => strip_tags($request->bio),
                        'country_id' => $country_id,
                        'state_id' => $request->state_id,
                        'city_id' => $request->city_id,
                        'created_at' => $this->entryDate
                    ];
                    DB::table('user_details')->insert($param);

                    $request->social_media_links = array();

                    if ((isset($request->facebook_url) && !empty($request->facebook_url))) {
                        $request->social_media_links[] = array(
                            'platform' => 'Facebook',
                            'url' => $request->facebook_url
                        );
                    }

                    if ((isset($request->twitter_url) && !empty($request->twitter_url))) {
                        $request->social_media_links[] = array(
                            'platform' => 'Twitter',
                            'url' => $request->twitter_url
                        );
                    }

                    if ((isset($request->instagram_url) && !empty($request->instagram_url))) {
                        $request->social_media_links[] = array(
                            'platform' => 'Instagram',
                            'url' => $request->instagram_url
                        );
                    }

                    if ((isset($request->linkedin_url) && !empty($request->linkedin_url))) {
                        $request->social_media_links[] = array(
                            'platform' => 'Linkedin',
                            'url' => $request->linkedin_url
                        );
                    }

                    # Create entry in client table
                    $param = [
                        'user_id' => $user->id,
                        'company_name' => $request->company_name,
                        'website' => $request->website,
                        'primary_industry' => $request->primary_industry,
                        'company_size' => $request->company_size,
                        'bio' => strip_tags($request->company_bio),
                        'address_line1' => $request->address_line1,
                        'address_line2' => $request->address_line2,
                        'founded_in' => $request->founded_in,
                        'social_media_links' => $request->social_media_links,
                        'created_at' => $this->entryDate
                    ];
                    DB::table('clients')->insert($param);

                    DB::commit();
                    $role = CommonFunction::getRolebyRoleID($request->role_id);
                    $result = array('status' => true, 'message' => $role . " added successfully");
                } else {
                    $result = array('status' => false, 'message' => "Something went wrong. Please try again");
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }



    ## add/update agency/facility Function
    public function updateAgency(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            if (isset($request->id) && !empty($request->id)) {
                # Check Email is already registered or not
                $query = CommonFunction::checkEmailExist($request->email, $request->role_id, $request->id);
                if ($query) {
                    $result = array('status' => false, 'message' => "Email is already registered");
                    return response()->json($result);
                }

                $param = array(
                    'name' => strip_tags($request->name),
                    'email' => strip_tags($request->email),
                    'phone' => strip_tags($request->phone),
                    'updated_at' => $this->entryDate,
                    'updated_by' => $request->user_id,
                );
                if (isset($request->password) && !empty($request->password)) {
                    $param['password'] = Hash::make($request->password);
                }
                DB::table('users')->where('id', $request->id)->update($param);

                # Get country_id from state_id
                $country_id = 0;
                $state = DB::table('states')->select('country_id')
                    ->where('id', $request->state_id)->first();
                if (!empty($state)) {
                    $country_id = $state->country_id;
                }

                DB::table('user_details')->where('user_id', $request->id)->delete();

                # Update entry in user_details
                $param = [
                    'user_id' => $request->id,
                    'bio' => strip_tags($request->bio),
                    'country_id' => $country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'updated_at' => $this->entryDate
                ];
                DB::table('user_details')->insert($param);


                $request->social_media_links = array();

                if ((isset($request->facebook_url) && !empty($request->facebook_url))) {
                    $request->social_media_links[] = array(
                        'platform' => 'Facebook',
                        'url' => $request->facebook_url
                    );
                }

                if ((isset($request->twitter_url) && !empty($request->twitter_url))) {
                    $request->social_media_links[] = array(
                        'platform' => 'Twitter',
                        'url' => $request->twitter_url
                    );
                }

                if ((isset($request->instagram_url) && !empty($request->instagram_url))) {
                    $request->social_media_links[] = array(
                        'platform' => 'Instagram',
                        'url' => $request->instagram_url
                    );
                }

                if ((isset($request->linkedin_url) && !empty($request->linkedin_url))) {
                    $request->social_media_links[] = array(
                        'platform' => 'Linkedin',
                        'url' => $request->linkedin_url
                    );
                }

                # Create entry in client table
                $param = [
                    'company_name' => $request->company_name,
                    'website' => $request->website,
                    'primary_industry' => $request->primary_industry,
                    'company_size' => $request->company_size,
                    'bio' => strip_tags($request->company_bio),
                    'country_id' => $country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'address_line1' => $request->address_line1,
                    'address_line2' => $request->address_line2,
                    'founded_in' => $request->founded_in,
                    'social_media_links' => json_encode($request->social_media_links),
                    'created_at' => $this->entryDate
                ];
                DB::table('clients')->where('user_id', $request->id)->update($param);

                DB::commit();

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $result = array('status' => true, 'message' => $role . " updated successfully");
            } else {
                # Check Email is already registered or not
                $query = CommonFunction::checkEmailExist($request->email, $request->role_id);
                if ($query) {
                    $result = array('status' => false, 'message' => "Email is already registered");
                    return response()->json($result);
                }

                # Create the user
                $user = new User;
                $user->unique_id = $this->generateUniqueCode(8);
                $user->name = strip_tags($request->name);
                $user->email = strip_tags($request->email);
                $user->phone = strip_tags($request->phone);
                $user->password = Hash::make($request->password);
                $user->role_id = $request->role_id;
                $user->created_at = $this->entryDate;
                $user->created_by = $request->user_id;
                $user->status = 1;
                $user->save();


                if ($user->id) {

                    # Get country_id from state_id
                    $country_id = 0;
                    $state = DB::table('states')->select('country_id')
                        ->where('id', $request->state_id)->first();
                    if (!empty($state)) {
                        $country_id = $state->country_id;
                    }

                    # Create entry in user_details
                    $param = [
                        'user_id' => $user->id,
                        'bio' => strip_tags($request->bio),
                        'country_id' => $country_id,
                        'state_id' => $request->state_id,
                        'city_id' => $request->city_id,
                        'created_at' => $this->entryDate
                    ];
                    DB::table('user_details')->insert($param);


                    $request->social_media_links = array();

                    if ((isset($request->facebook_url) && !empty($request->facebook_url))) {
                        $request->social_media_links[] = array(
                            'platform' => 'Facebook',
                            'url' => $request->facebook_url
                        );
                    }

                    if ((isset($request->twitter_url) && !empty($request->twitter_url))) {
                        $request->social_media_links[] = array(
                            'platform' => 'Twitter',
                            'url' => $request->twitter_url
                        );
                    }

                    if ((isset($request->instagram_url) && !empty($request->instagram_url))) {
                        $request->social_media_links[] = array(
                            'platform' => 'Instagram',
                            'url' => $request->instagram_url
                        );
                    }

                    if ((isset($request->linkedin_url) && !empty($request->linkedin_url))) {
                        $request->social_media_links[] = array(
                            'platform' => 'Linkedin',
                            'url' => $request->linkedin_url
                        );
                    }

                    # Create entry in client table
                    $param = [
                        'user_id' => $user->id,
                        'company_name' => $request->company_name,
                        'website' => $request->website,
                        'primary_industry' => $request->primary_industry,
                        'company_size' => $request->company_size,
                        'bio' => strip_tags($request->company_bio),
                        'country_id' => $country_id,
                        'state_id' => $request->state_id,
                        'city_id' => $request->city_id,
                        'address_line1' => $request->address_line1,
                        'address_line2' => $request->address_line2,
                        'founded_in' => $request->founded_in,
                        'social_media_links' => json_encode($request->social_media_links),
                        'created_at' => $this->entryDate
                    ];
                    DB::table('clients')->insert($param);

                    DB::commit();
                    $role = CommonFunction::getRolebyRoleID($request->role_id);
                    $result = array('status' => true, 'message' => $role . " added successfully");
                } else {
                    $result = array('status' => false, 'message' => "Something went wrong. Please try again");
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    public function generateUniqueCode(int $codeLength)
    {
        $characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);

        $code = '';

        while (strlen($code) < $codeLength) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code . $character;
        }

        if (User::where('unique_id', $code)->exists()) {
            $this->generateUniqueCode($codeLength);
        }

        return $code;
    }

    ## Function to update user's status
    public function updateUserStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'role_id' => 'required',
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
            DB::table('users')->where('id', $request->id)->where('role_id', $request->role_id)->update($param);

            $role = CommonFunction::getRolebyRoleID($request->role_id);
            $msg = $role . " status has been been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get agency details
    public function getAgencyDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'userID' => 'required',
            'role_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            # Check for drafted job
            $user_id = $request->user_id;
            $raw = DB::table('users as u')
                ->leftJoin('clients as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->select('u.*', 'ud.bio', 'ud.country_id', 'ud.state_id', 'ud.city_id', 'ud.address_line1', 'ud.address_line2', 'ud.primary_industry', 'ud.company_size', 'ud.website', 'ud.founded_in', 's.name as state_name', 's.code as state_code', 'city.city_name', 'ud.social_media_links', 'ud.company_name')
                ->where(['u.unique_id' => $request->userID])
                ->first();



            $raw->facebook_url = $raw->twitter_url = $raw->instagram_url = $raw->linkedin_url = "";


            $data = array();

            // Check if $raw exists
            if ($raw) {
                // Add dir_path column and its value to the record
                $raw->profile_pic_path = (!empty($raw->profile_pic)) ? url(config('custom.user_folder') . $raw->profile_pic) : '';


                $social_links = json_decode($raw->social_media_links, true);
                if (!empty($social_links)) {
                    foreach ($social_links as $k => $v) {
                        if ($v['platform'] == 'Facebook' && !empty($v['url']) && $v['url'] != null)
                            $raw->facebook_url = $v['url'];
                        else if ($v['platform'] == 'Twitter' && !empty($v['url']) && $v['url'] != null)
                            $raw->twitter_url = $v['url'];
                        else if ($v['platform'] == 'Instagram' && !empty($v['url']) && $v['url'] != null)
                            $raw->instagram_url = $v['url'];
                        else if ($v['platform'] == 'Linkedin' && !empty($v['url']) && $v['url'] != null)
                            $raw->linkedin_url = $v['url'];
                    }
                }

                /*
                $raw->total_jobs = DB::table('jobs as j')
                ->where('j.user_id', $raw->id)
                ->where('j.deleted_at', NULL)
                ->count();

    
                 $raw->total_candidates = DB::table('users as u')
                    ->where('u.created_by', $raw->id)
                    ->where('u.role_id', 4)
                    ->where('u.deleted_at', NULL)
                    ->count();

                $raw->total_employees = DB::table('users as u')
                    ->where('u.created_by', $raw->id)
                    ->where('u.role_id', 9)
                    ->where('u.deleted_at', NULL)
                    ->count();

                $raw->total_applicants = DB::table('users as u')
                    ->where('u.created_by', $raw->id)
                    ->where('u.role_id', 5)
                    ->where('u.deleted_at', NULL)
                    ->count();
                */

                $data['total_jobs'] = DB::table('jobs as j')
                    ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                    ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                    ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                    ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                    ->select(
                        'j.id',
                        'j.title',
                        'j.unique_id',
                        'j.total_opening',
                        'j.created_at',
                        'j.is_starred',
                        's.name as state_name',
                        's.code as state_code',
                        'city.city_name',
                        'p.profession',
                        'sp.specialty',
                        'j.status'
                    )
                    ->where('j.status', 1)
                    ->where('j.user_id', $raw->id)
                    ->where('j.deleted_at', NULL)
                    ->orderBy('j.id', 'desc')
                    ->get()->toArray();
            }



            $data['agency_details'] = $raw;



            if (!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $data);
            else
                $result = array('status' => false, 'message' => 'Invalid Agency ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get facility details
    public function getFacilityDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'userID' => 'required',
            'role_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            # Check for drafted job
            $user_id = $request->user_id;
            $raw = DB::table('users as u')
                ->leftJoin('user_details as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->select('u.*', 'ud.bio', 'ud.country_id', 'ud.state_id', 'ud.city_id', 'ud.address_line1', 'ud.address_line2', 's.name as state_name', 's.code as state_code', 'city.city_name', 'ud.dob', 'ud.total_experience')
                ->where(['u.unique_id' => $request->userID])
                ->first();



            $raw->facebook_url = $raw->twitter_url = $raw->instagram_url = $raw->linkedin_url = "";


            $data = array();
            // Check if $raw exists
            if ($raw) {
                // Add dir_path column and its value to the record
                $raw->profile_pic_path = (!empty($raw->profile_pic)) ? url(config('custom.user_folder') . $raw->profile_pic) : '';

                /*
                $raw->total_jobs = DB::table('jobs as j')
                ->where('j.user_id', $raw->id)
                ->where('j.deleted_at', NULL)
                ->count();

                 $raw->total_candidates = DB::table('users as u')
                    ->where('u.created_by', $raw->id)
                    ->where('u.role_id', 4)
                    ->where('u.deleted_at', NULL)
                    ->count();

                $raw->total_employees = DB::table('users as u')
                    ->where('u.created_by', $raw->id)
                    ->where('u.role_id', 9)
                    ->where('u.deleted_at', NULL)
                    ->count();

                $raw->total_applicants = DB::table('users as u')
                    ->where('u.created_by', $raw->id)
                    ->where('u.role_id', 5)
                    ->where('u.deleted_at', NULL)
                    ->count();
                */
            }

            $data['total_jobs'] = DB::table('jobs as j')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->select(
                    'j.id',
                    'j.title',
                    'j.unique_id',
                    'j.total_opening',
                    'j.created_at',
                    'j.is_starred',
                    's.name as state_name',
                    's.code as state_code',
                    'city.city_name',
                    'p.profession',
                    'sp.specialty',
                    'j.status'
                )
                ->where('j.user_id', $raw->id)
                ->where('j.status', 1)
                ->where('j.deleted_at', NULL)
                ->orderBy('j.id', 'desc')
                ->get()->toArray();

            $data['facility_details'] = $raw;

            if (!empty($raw))
                $result = array('status' => true, 'message' => "Record found", 'data' => $data);
            else
                $result = array('status' => false, 'message' => 'Invalid Agency ID');
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## add/update candidate function
    public function addCandidate(Request $request)
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

            if (isset($request->step) && $request->step == 'step1') {

                $param = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ];

                if (empty($request->id)) {
                    /*
                    # Check Email is already registered or not
                    $query = CommonFunction::checkEmailExist($request->email, array(4, 5, 9));
                    if ($query) {
                        $result = array('status' => false, 'message' => "Email is already registered");
                        return response()->json($result);
                    }
                    */

                    $param['unique_id'] = $this->generateUniqueCode(8);
                    /*$param['password'] = Hash::make($request->password);*/
                    $param['password'] = NULL;
                    $param['is_login_allowed'] = 0;
                    $param['role_id'] = $request->role_id;
                    $param['created_by'] = $request->user_id;
                    $param['status'] = 1;
                    $param['created_at'] = $this->entryDate;
                    $last_id = DB::table('users')->insertGetId($param);

                    if ($request->role_id == 4)
                        $msg = "Candidate has been successfully created";
                    else if ($request->role_id == 9)
                        $msg = "Employee has been successfully created";
                } else {
                    /*
                    # Check Email is already registered or not
                    $query = CommonFunction::checkEmailExist($request->email, array(4, 5, 9), $request->id);
                    if ($query) {
                        $result = array('status' => false, 'message' => "Email is already registered");
                        return response()->json($result);
                    }
                    */

                    if (isset($request->password) && !empty($request->password)) {
                        $param['password'] = Hash::make($request->password);
                    }
                    $param['updated_by'] = $request->user_id;
                    $param['updated_at'] = $this->entryDate;
                    DB::table('users')->where('id', $request->id)->update($param);
                    $last_id = $request->id;

                    /*DB::table('user_details')->where('user_id', $last_id)->delete();*/
                }

                # Get country_id from state_id
                $country_id = null;
                $state = DB::table('states')->select('country_id')
                    ->where('id', $request->state_id)->first();
                if (!empty($state)) {
                    $country_id = $state->country_id;
                }

                /*
                if(empty($request->dob) || $request->dob == 'null')
                    $request->dob = NULL;
                */

                $info = [
                    'user_id' => $last_id,
                    'bio' => '',
                    'country_id' => $country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'address_line1' => $request->address_line1,
                    'address_line2' => $request->address_line2,
                    'dob' => NULL,
                    'total_experience' => '',
                    'created_at' => $this->entryDate,
                ];

                $udResult = DB::table('user_details')->select('id')
                    ->where('user_id', $last_id)->first();
                if (!empty($udResult)) {
                    DB::table('user_details')->where('user_id', $last_id)->update($info);
                } else {
                    DB::table('user_details')->insert($info);
                }
            } else if (isset($request->step) && $request->step == 'step2') {
                $param = [
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'salary_start_range' => $request->salary_start_range,
                    'salary_end_range' => $request->salary_end_range,
                    'salary_type' => $request->salary_type,
                    'employment_type_id' => $request->employment_type_id,
                    'min_work_per_week' => $request->min_work_per_week,
                    'progress' => 2,
                    'updated_at' => $this->entryDate,
                ];
                DB::table('jobs')->where('id', $request->id)->update($param);
                $last_id = $request->id;
            } else if (isset($request->step) && $request->step == 'step3') {
                $param = [
                    'description' => $request->description,
                    'qualification' => $request->qualification,
                    'responsibilities' => $request->responsibilities,
                    'progress' => 3,
                    'updated_at' => $this->entryDate,
                ];
                DB::table('jobs')->where('id', $request->id)->update($param);
                $last_id = $request->id;
            } else if (isset($request->step) && $request->step == 'step4') {
                $param = [
                    'progress' => 4,
                    'status' => 1,
                    'updated_at' => $this->entryDate,
                ];
                DB::table('jobs')->where('id', $request->id)->update($param);
                $last_id = $request->id;
            }

            DB::commit();
            $result = array('status' => true, 'message' => "Job Seeker has been successfully created", 'data' => array('id' => $last_id));
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get user details

    public function getUserDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            # Check for drafted job
            $user_id = $request->user_id;
            $raw = DB::table('users as u')
                ->leftJoin('user_details as ud', 'ud.user_id', '=', 'u.id')
                ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                ->select('u.*', 'ud.bio', 'ud.country_id', 'ud.state_id', 'ud.city_id', 'ud.address_line1', 'ud.address_line2', 'ud.dob', 'ud.total_experience', 's.name as state_name', 's.code as state_code', 'city.city_name')
                ->where(['u.unique_id' => $request->userID])
                ->first();

            // Check if $raw exists
            if ($raw) {
                // Add profile picture path if it exists
                $raw->profile_pic_path = !empty($raw->profile_pic)
                    ? url(config('custom.user_folder') . $raw->profile_pic)
                    : '';

                // Fetch user resume details separately
                $resume = DB::table('user_resumes')
                    ->where('user_id', $raw->id)  // use $raw->id instead of $user_id for consistency
                    ->select('id', 'file_name')
                    ->orderBy('id', 'desc') // Correct method name
                    ->first();

                // Add resume details if they exist
                if ($resume) {
                    $raw->resume_id = $resume->id;
                    $raw->resume_path = url(config('custom.resume_folder') . $resume->file_name);
                } else {
                    $raw->resume_id = null;
                    $raw->resume_path = null;
                }

                $result = array('status' => true, 'message' => "Record found", 'data' => $raw);
            } else {
                $result = array('status' => false, 'message' => 'Invalid User ID');
            }
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }

        return response()->json($result);
    }

    ## Function to get job preference
    public function getJobPreference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            $data = array();
            $result = DB::table('user_preferred_employment_types as us')
                ->leftJoin('employment_types as e','e.id','=','us.employment_type_id')
                ->select('e.title')
                ->where('us.user_id', $request->user_id)->get()->toArray();
            if (!empty($result)) {
                foreach ($result as $val) {
                    $data['employement_types'][] = $val->title;
                }
            }

            $result = DB::table('user_preferred_shifts as us')
                ->leftJoin('shifts as s','s.id','=','us.shift_id')
                ->select('s.title')
                ->where('us.user_id', $request->user_id)->get()->toArray();
            if (!empty($result)) {
                foreach ($result as $val) {
                    $data['shifts'][] = $val->title;
                }
            }

            /*
            $result = DB::table('user_preferred_states as us')
                ->leftJoin('states as s','s.id','=','us.state_id')
                ->select('s.code')
                ->where('us.user_id', $request->user_id)->get()->toArray();
            if (!empty($result)) {
                foreach ($result as $val) {
                    $data['desired_state_ids'][] = $val->code;
                }
            }
            
            */
            // $result = DB::table('user_preferred_states as us')
            //     ->leftJoin('states as s', 's.id', '=', 'us.state_id')
            //     ->select(
            //         DB::raw('CASE 
            //             WHEN FIND_IN_SET("all", GROUP_CONCAT(us.state_id)) THEN "All States"
            //             ELSE GROUP_CONCAT(DISTINCT s.code ORDER BY s.code SEPARATOR ", ")
            //         END as desired_states')
            //     )
            //     ->where('us.user_id', $request->user_id)
            //     ->get();
            
            // if ($result->isNotEmpty()) {
            //     $data['desired_state_ids'] = $result[0]->desired_states;
            // }

            $result = DB::table('user_preferred_states as us')
                ->leftJoin('states as s', 's.id', '=', 'us.state_id')
                ->select(
                    DB::raw('CASE 
                        WHEN FIND_IN_SET("all", GROUP_CONCAT(us.state_id)) THEN "All States"
                        ELSE GROUP_CONCAT(DISTINCT s.code ORDER BY s.code SEPARATOR ", ")
                    END as desired_state_codes'),
                    DB::raw('CASE 
                        WHEN FIND_IN_SET("all", GROUP_CONCAT(us.state_id)) THEN "All States"
                        ELSE GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ", ")
                    END as desired_state_names')
                )
                ->where('us.user_id', $request->user_id)
                ->get();

            if ($result->isNotEmpty()) {
                $data['desired_state_codes'] = $result[0]->desired_state_codes;
                $data['desired_state_names'] = $result[0]->desired_state_names;
            }

              /** user active license */
            $userActiveLicense = DB::table('user_active_certificates')->where('user_id', $request->user_id)
              ->select(
                  'id',
                  'certificate_name',
                  DB::raw("DATE_FORMAT(certificate_expiry_date, '%m/%d/%Y') as certificate_expiry_date")
              )->get()->toArray();
            if (count($userActiveLicense) > 0) {
                $data['user_active_certificates'] = $userActiveLicense;
            }

             // user active state license
            $userStateLicense = DB::table('user_state_license')->where('user_id', $request->user_id)
                ->select('id', 'license_name', 'location',
                    DB::raw("DATE_FORMAT(license_expiry_date, '%m/%d/%Y') as license_expiry_date")
                )->get()
                ->toArray();
            if (count($userStateLicense) > 0) {
                $data['user_state_license'] = $userStateLicense;
            }

             // RTO dates details
             $userRTOdates = DB::table('user_rto_details')->where('user_id', $request->user_id)
                        ->select('id', 'rto_start_date', 'rto_end_date')
                        ->get()
                        ->toArray();
            $data['user_rto_dates'] = [];
            if (count($userRTOdates) > 0) {
            foreach ($userRTOdates as $date) {
            $date->rto_start_date = CommonFunction::changeDateFormat($date->rto_start_date, 1);
            $date->rto_end_date = CommonFunction::changeDateFormat($date->rto_end_date, 1);
            }
            $data['user_rto_dates'] = $userRTOdates;
            }

            $udResult = DB::table('user_details as ud')
            ->leftJoin('professions as p','p.id','=','ud.profession_id')
            ->leftJoin('specialities as s','s.id','=','ud.specialty_id')
            ->select('p.profession as profession_title', 's.specialty as specialty_title', 'available_start_date','EMR_experience','total_experience','specialty_experience','fully_vaccinated','teaching_hospital_experience','travel_experience')
                ->where('user_id', $request->user_id)->first();
            if (!empty($udResult)) {
                $data['profession'] = $udResult->profession_title;
                $data['specialty'] = $udResult->specialty_title;
                $data['available_start_date'] = $udResult->available_start_date;
                $data['EMR_experience'] = $udResult->EMR_experience ? explode(',', $udResult->EMR_experience) : [];
                $data['total_experience'] = $udResult->total_experience;
                $data['specialty_experience'] = $udResult->specialty_experience;
                $data['teaching_hospital_experience'] = $udResult->teaching_hospital_experience;
                $data['travel_experience'] = $udResult->travel_experience;
                $data['fully_vaccinated'] = $udResult->fully_vaccinated;
            }

            $result = array('status' => true, 'message' => "record found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update job preference
    public function updateJobPreference(Request $request)
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
            DB::beginTransaction();


            DB::table('user_preferred_employment_types')->where('user_id', $request->userID)->delete();

            if (isset($request->employement_types) && !empty($request->employement_types)) {
                foreach ($request->employement_types as $k => $v) {
                    $param = array(
                        'user_id' => $request->userID,
                        'employment_type_id' => $v,
                        'created_at' => $this->entryDate
                    );

                    DB::table('user_preferred_employment_types')->insert($param);
                }
            }

            DB::table('user_preferred_shifts')->where('user_id', $request->userID)->delete();

            if (isset($request->shifts) && !empty($request->shifts)) {
                foreach ($request->shifts as $k => $v) {
                    $param = array(
                        'user_id' => $request->userID,
                        'shift_id' => $v,
                        'created_at' => $this->entryDate
                    );

                    DB::table('user_preferred_shifts')->insert($param);
                }
            }

            DB::table('user_preferred_states')->where('user_id', $request->userID)->delete();

            if (isset($request->desired_state_ids) && !empty($request->desired_state_ids)) {
                foreach ($request->desired_state_ids as $k => $v) {
                    $param = array(
                        'user_id' => $request->userID,
                        'state_id' => $v,
                        'created_at' => $this->entryDate
                    );

                    DB::table('user_preferred_states')->insert($param);
                }
            }

            $info = [
                'searchable_profile' => ($request->searchable_profile) ? $request->searchable_profile : "0",
                'profession_id' => ($request->profession_id) ? $request->profession_id : 0,
                'specialty_id' => ($request->specialty_id) ? $request->specialty_id : 0,
                'available_start_date' => ($request->available_start_date) ? $request->available_start_date : "",
            ];
            DB::table('user_details')->where('user_id', $request->userID)->update($info);

            $msg = "Job preference has been been successfully updated";
            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to perform bulk actions
    public function userBulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required',
            'user_id' => 'required',
            'bulk_action' => 'required',
            'role_id' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {

            if ($request->bulk_action == 'delete' && !empty($request->user_ids)) {
                DB::beginTransaction();
                foreach ($request->user_ids as $k => $v) {
                    $param = array();
                    $param['deleted_at'] = $this->entryDate;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids) . " " . $role . "(s) has been successfully deleted";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else if ($request->bulk_action == 'change-status-active' && !empty($request->user_ids)) {
                DB::beginTransaction();
                foreach ($request->user_ids as $k => $v) {
                    $param = array();
                    $param['status'] = 1;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids) . " " . $role . "(s) status has been successfully updated";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else if ($request->bulk_action == 'change-status-inactive' && !empty($request->user_ids)) {
                DB::beginTransaction();
                foreach ($request->user_ids as $k => $v) {
                    $param = array();
                    $param['status'] = 0;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids) . " " . $role . "(s) status has been successfully updated";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else if ($request->bulk_action == 'change-status-block' && !empty($request->user_ids)) {
                DB::beginTransaction();
                foreach ($request->user_ids as $k => $v) {
                    $param = array();
                    $param['status'] = 2;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids) . " " . $role . "(s) status has been successfully updated";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else if ($request->bulk_action == 'change-status-terminate' && !empty($request->user_ids)) {
                DB::beginTransaction();
                foreach ($request->user_ids as $k => $v) {
                    $param = array();
                    $param['status'] = 3;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids) . " " . $role . "(s) status has been successfully updated";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else if ($request->bulk_action == 'convert-to-applicant' && !empty($request->user_ids)) {
                DB::beginTransaction();
                foreach ($request->user_ids as $k => $v) {
                    $param = array();
                    $param['role_id'] = 5;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids) . " " . $role . "(s) has been successfully converted to applicant";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else if ($request->bulk_action == 'convert-to-candidate' && !empty($request->user_ids)) {
                DB::beginTransaction();
                foreach ($request->user_ids as $k => $v) {
                    $param = array();
                    $param['role_id'] = 4;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids) . " " . $role . "(s) has been successfully converted to job seeker";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else if ($request->bulk_action == 'convert-to-employee' && !empty($request->user_ids)) {
                DB::beginTransaction();
                foreach ($request->user_ids as $k => $v) {
                    $param = array();
                    $param['role_id'] = 9;
                    $param['updated_at'] = $this->entryDate;
                    $param['updated_by'] = $request->user_id;
                    DB::table('users')->where('id', $v)->where('role_id', $request->role_id)->update($param);
                }

                $role = CommonFunction::getRolebyRoleID($request->role_id);
                $msg = count($request->user_ids) . " " . $role . "(s) has been successfully converted to employee";

                DB::commit();
                $result = array('status' => true, 'message' => $msg);
            } else {
                $result = array('status' => false, 'message' => 'Unknown error occured');
            }
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to update user field
    public function updateUserField(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'field' => 'required',
            'role_id' => 'required',
            /*'value' => 'required',*/
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred in API", 'data' => $request->all());
            return response()->json($result);
        }

        try {
            DB::beginTransaction();

            $role = CommonFunction::getRolebyRoleID($request->role_id);

            /*
            if($request->field == 'role_id' && $request->value == 5)
            {
                $candidateRow = DB::table('job_applications as ja')
                ->select('ja.id')
                ->where('ja.user_id', $request->id)
                ->where('ja.deleted_at', NULL)
                ->first();
                if(empty($candidateRow))
                {
                    $result = array('status' => false, 'message' => "Conversion Failed: The current ".$role." cannot be converted to an applicant because they have not applied for any job.");
                    return response()->json($result);
                }
            }
            */

            $param = array();
            $param[$request->field] = $request->value;
            $param['updated_at'] = $this->entryDate;
            $param['updated_by'] = $request->user_id;
            DB::table('users')->where('id', $request->id)->update($param);


            $msg = $role . " status has been successfully updated";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to delete user
    public function deleteUser(Request $request)
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
            $param['updated_at'] = $this->entryDate;
            $param['updated_by'] = $request->user_id;
            DB::table('users')->where('id', $request->id)->update($param);

            $role = CommonFunction::getRolebyRoleID($request->role_id);
            $msg = $role . " status has been successfully deleted";

            DB::commit();
            $result = array('status' => true, 'message' => $msg);
        } catch (Exception $e) {
            DB::rollback();
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get user job applications
    public function getUserJobApplications(Request $request)
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

            $jobApplications = DB::table('jobs as j')
                ->join('job_applications as ja', 'j.id', '=', 'ja.job_id')
                ->leftJoin('states as s', 'j.state_id', '=', 's.id')
                ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
                ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
                ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                ->leftJoin('users as u', 'ja.user_id', '=', 'u.id')
                ->select('j.id as job_id', 'u.id as user_id', 'j.title', 'j.unique_id', 'ja.created_at', 's.name as state_name', 's.code as state_code',  'city.city_name', 'ja.status', 'u.name', 'u.unique_id as user_unique_id')
                /*->where('j.user_id', $request->user_id)*/
                ->where('ja.user_id', $request->userID)
                ->where('j.deleted_at', NULL);

            // Apply filters if present
            if (isset($request->status) && $request->status != 'all') {
                $jobApplications->where('ja.status', $request->status);
            }
            if (isset($request->keyword) && !empty($request->keyword)) {
                $jobApplications->where('j.title', 'LIKE', "%{$request->keyword}%");
            }

            $jobApplications = $jobApplications->orderBy('ja.id', 'desc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($jobApplications)) . " Record found", 'data' => $jobApplications);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get user references
    public function getUserReferences(Request $request)
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
            $userReferences = DB::table('user_references as ur')
                ->leftJoin('user_reference_details as urd', 'ur.id', '=', 'urd.reference_id')
                ->select('ur.*', 'urd.*')
                ->where('ur.user_id', $request->userID)
                ->where('ur.deleted_at', NULL)
                ->orderBy('ur.id', 'asc')
                ->get()->toArray();

            $result = array('status' => true, 'message' => (count($userReferences)) . " Record found", 'data' => $userReferences);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    ## Function to get user's assigned compliance files
    public function getUserComplianceFiles(Request $request)
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

            $today = Carbon::today();
            $complianceFiles = DB::table('compliance_files as cf')
                ->leftJoin('doc_types as dt', 'cf.type_id', '=', 'dt.id')
                ->leftJoin('users as u', 'cf.assigned_user_id', '=', 'u.id')
                ->leftJoin('users as u2', 'cf.created_by', '=', 'u2.id')
                ->select(
                    'cf.id',
                    'cf.title',
                    'cf.type_id',
                    'cf.cat_id',
                    'cf.assigned_user_id',
                    'cf.notes',
                    'cf.expiration_date',
                    'cf.file_name',
                    'dt.doc_name as doc_type_name',
                    'u.name as assigned_to',
                    'u.unique_id as assigned_unique_id',
                    'u2.name as creator_name',
                    'u2.unique_id as creator_unique_id',
                    'u2.role_id as creator_role_id',
                    'cf.created_by as posted_user_id',
                    'cf.status'
                )
                ->where('cf.deleted_at', NULL)
                ->where('dt.module_type', 'compliance')
                ->where('cf.assigned_user_id', $request->userID);

            if (isset($request->keyword) && !empty($request->keyword)) {
                $complianceFiles->where('cf.title', 'LIKE', "%{$request->keyword}%");
            }

            if (isset($request->tab) && $request->tab != 'all') {
                $complianceFiles->where('cf.cat_id', $request->tab);
            }

            if (isset($request->status) && $request->status != 'all') {
                if ($request->status == 'expired')
                    $complianceFiles->where('cf.expiration_date', '<', $today)->where('cf.is_archive', '0');
                else if ($request->status == 'archived')
                    $complianceFiles->where('cf.expiration_date', '>=', $today)->where('cf.is_archive', '1');
                else
                    $complianceFiles->where('cf.expiration_date', '>=', $today)->where('cf.is_archive', '0');
            }

            if ((isset($request->expiration_start_date) && !empty($request->expiration_start_date)) || isset($request->expiration_end_date) && !empty($request->expiration_end_date)) {
                if (isset($request->expiration_start_date) && !empty($request->expiration_start_date))
                    $complianceFiles->where('cf.expiration_date', '>=', $request->expiration_start_date);

                if (isset($request->expiration_end_date) && !empty($request->expiration_end_date))
                    $complianceFiles->where('cf.expiration_date', '<=', $request->expiration_end_date);
            }

            $complianceFiles = $complianceFiles->orderBy('cf.id', 'desc')->get()
                ->map(function ($complianceFiles) {
                    // Add dir_path column and its value to each record
                    $complianceFiles->dir_path = (!empty($complianceFiles->file_name)) ? url(config('custom.compliance_file_folder') . $complianceFiles->file_name) : ""; // Adjust 'your/directory/path/' to the actual directory path
                    return $complianceFiles;
                })
                ->toArray();


            $result = array('status' => true, 'message' => (count($complianceFiles)) . " Record found", 'data' => $complianceFiles);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }


    ## function return all user list (candidate/applicants/employee/job applicants)
    public function getAllUserList(Request $request)
    {
        try {


            $data = array();


            $data['applicants'] = DB::table('users as u')
                ->select('u.id', 'u.name')
                ->where('u.role_id', 5)
                ->where('u.deleted_at', NULL)
                ->orderBy('u.name')->get()->toArray();

            $data['candidates'] = DB::table('users as u')
                ->select('u.id', 'u.name')
                ->where('u.role_id', 4)
                ->where('u.deleted_at', NULL)
                ->orderBy('u.name')->get()->toArray();

            $data['employees'] = DB::table('users as u')
                ->select('u.id', 'u.name')
                ->where('u.role_id', 9)
                ->where('u.deleted_at', NULL)
                ->orderBy('u.name')->get()->toArray();



            $result = array('status' => true, 'message' => "record found", 'data' => $data);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Admin approved reference 
     */
    public function verifiedReference(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'userID' => 'required',
            'referenceID' => 'required',
            'reference_by_verified_by' => 'required',
            'reference_by_verified_by_date' => 'required'
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }
        try {
            // Update verified flag
            DB::table('user_references as ur')
                ->where(['ur.user_id' => $request->userID, 'ur.id' => $request->referenceID])
                ->update(['is_verify' => 1]);

            // Update verified by details
            DB::table('user_reference_details as urd')
                ->where(['user_id' => $request->userID,'reference_id' => $request->referenceID])
                ->update([
                    'reference_by_verified_by' => $request->reference_by_verified_by,
                    'reference_by_verified_by_date' => $request->reference_by_verified_by_date
                ]);

            $result = array('status' => true, 'message' => "Reference verified successfully!");
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    /**
     * Get deleted users by role 
     */
    public function getDeletedUsersByRole(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }
        try {
            // Fetch deleted users with the given role
            $deletedUsers = DB::table('users as u')
                    ->leftJoin('user_preferred_states as ups', 'u.id', '=', 'ups.user_id')
                    ->leftJoin('states as s2', 'ups.state_id', '=', 's2.id')
                    ->leftJoin('user_details as ud', 'ud.user_id', '=', 'u.id')
                    ->leftJoin('states as s', 'ud.state_id', '=', 's.id')
                    ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')
                    ->leftJoin('job_applications as ja', 'u.id', '=', 'ja.user_id')
                    ->leftJoin('jobs as j', 'j.id', '=', 'ja.job_id')
                    ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
                    ->leftJoin('professions as p', 'ud.profession_id', '=', 'p.id')
                    ->leftJoin('users as u2', 'u.created_by', '=', 'u2.id')
                    ->select(
                        'u.id',
                        'u.name',
                        'u.unique_id',
                        'u.email',
                        'u.phone',
                        'u.created_at',
                        'u.profile_pic',
                        'u.status',
                        's.name as state_name',
                        's.code as state_code',
                        'city.city_name',
                        'u.status as user_status',
                        'u2.name as creator_name',
                        'u2.role_id as creator_role_id',
                        'u2.unique_id as creator_unique_id',
                        'j.title as job_title',
                        'ud.state_id',
                        'ud.city_id',
                        'ud.bio',
                        'j.unique_id as job_unique_id',
                        'ja.status as job_status',
                        'sp.specialty',
                        'p.profession'
                    )
                    ->whereNotNull('u.deleted_at') // Ensure user is soft-deleted
                    ->where('u.role_id', $request->role_id); // Filter by role_id
            if (isset($request->name) && !empty($request->name)) {
                $deletedUsers->where('u.name', 'LIKE', "%{$request->name}%");
            }
            $deletedUsers = $deletedUsers->groupBy('u.id')->get();

            // Check if users exist
            if ($deletedUsers->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => "No deleted users found for the given role",
                    'data' => []
                ], 200);
            }

            // Return deleted users
            return response()->json([
                'status' => true,
                'message' => "Deleted users retrieved successfully",
                'data' => $deletedUsers
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
    
    /**
     * Restore Deleted Users
     */
    public function restoreUser(Request $request)
    {
        // Validate request input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer', // User performing the restore action
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occurred");
            return response()->json($result);
        }

        try {
            DB::beginTransaction();
            // Check if user exists and is deleted
            $user = DB::table('users')->where('id', $request->id)->whereNotNull('deleted_at')->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => "User not found!"
                ], 404);
            }

            // Restore user (set `deleted_at` to NULL)
            DB::table('users')->where('id', $request->id)->update([
                'deleted_at' => NULL,
                'updated_at' => now(),
                'updated_by' => $request->user_id
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => "User restored successfully"
            ], 200);
        } catch (\Exception $e) {
            $result = array('status' => false, 'message' => $e->getMessage());
        }
        return response()->json($result);
    }
}
