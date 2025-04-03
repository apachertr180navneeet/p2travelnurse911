<?php

namespace App\Http\Controllers;

use App\Helper\CommonFunction;
use Illuminate\Http\Request;
use DB;
use URL;
use Session;
use PDF;
use Validator;
use Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $entryLimit;
    private $entryDate;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->entryLimit = 10;
        $this->entryDate = date("Y-m-d H:i:s");
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $data['professions'] = DB::table('professions as p')
            ->leftJoin(DB::raw('(SELECT profession_id, COUNT(*) as job_count FROM jobs WHERE deleted_at IS NULL and status = 1 GROUP BY profession_id) as j'), 'p.id', '=', 'j.profession_id')
            ->select('p.*', DB::raw('COALESCE(j.job_count, 0) as job_count'))
            ->where('p.status', '1')
            ->whereNull('p.deleted_at')

            ->orderBy('p.profession', 'ASC')
            ->limit(8)
            ->get()->toArray();

        $jobs = DB::table('jobs as j')
            ->join('users as u', 'u.id', '=', 'j.user_id')
            ->leftJoin('states as s', 'j.state_id', '=', 's.id')
            ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
            ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
            ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
            ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_type', 'j.salary_end_range', 's.name as state_name',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.name as company_name', 'u.profile_pic', 'u.role_id as compnay_role_id')
            ->where('j.deleted_at', NULL)
            /*->orderBy('j.id', 'desc')*/
            ->orderByRaw('CAST(j.salary_start_range AS UNSIGNED) DESC')
            ->limit(6)
            ->get()
            ->map(function ($jobs) {
                // Add dir_path column and its value to each record
                $jobs->profile_pic_path = (!empty($jobs->profile_pic)) ? url(config('custom.user_folder') . $jobs->profile_pic) : '';
                return $jobs;
            })
            ->toArray();

        $data['jobs'] = $jobs;

        $blogs = DB::table('blogs as b')
            ->select('b.*')
            ->where('b.deleted_at', NULL)
            ->where('b.status', 1)
            ->orderBy('b.id', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($blogs) {
                // Add dir_path column and its value to each record
                $blogs->profile_pic_path = (!empty($blogs->image)) ? url(config('custom.blog_folder') . $blogs->image) : asset('public/assets/images/default.jpeg');
                return $blogs;
            })
            ->toArray();
        $data['blogs'] = $blogs;

        $data['title'] = 'Home';
        $data['cur_page'] = 'homepage';
        return view('index', $data);
    }

    public function search(Request $request)
    {

        $perPage = $request->input('per_page', 10);

        $jobs = DB::table('jobs as j')
            ->join('users as u', 'u.id', '=', 'j.user_id')
            ->leftJoin('states as s', 'j.state_id', '=', 's.id')
            ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
            ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
            ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
            ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_type', 'j.salary_end_range', 's.name as state_name',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.profile_pic', 'u.name as company_name', 'u.role_id as compnay_role_id')
            ->where('j.deleted_at', NULL);


        if ($request->has('keyword') && !empty($request->input('keyword'))) {
            $jobs->where('j.title', 'like', '%' . $request->input('keyword') . '%');
        }


        if ($request->has('location') && !empty($request->input('location'))) {

            $location = $request->input('location');
            $jobs->where(function ($query) use ($location) {
                $query->where('s.name', 'like', '%' . $location . '%')
                    ->orWhere('city.city_name', 'like', '%' . $location . '%');
            });
        }

        if ($request->has('empType') && !empty($request->input('empType'))) {
            $jobs->where('j.employment_type_id', $request->input('empType'));
        }

        if ($request->has('job_type') && !empty($request->input('job_type'))) {
            $jobs->where('job_type_id', $request->input('job_type'));
        }

        if ($request->has('orderBy') && $request->input('orderBy') == 'highest') {
            $jobs->orderByRaw('CAST(j.salary_start_range AS UNSIGNED) DESC');
        } else if ($request->has('orderBy') && $request->input('orderBy') == 'lowest') {
            $jobs->orderByRaw('CAST(j.salary_start_range AS UNSIGNED) ASC');
        } else {
            $jobs->orderBy('j.id', 'desc');
        }

        /* ->orderBy('j.id', 'desc') */

        $jobs = $jobs->paginate($perPage)->appends($request->except('page'));
        /*
            ->map(function ($jobs) {
                // Add dir_path column and its value to each record
                $jobs->profile_pic_path = (!empty($jobs->profile_pic)) ? url(config('custom.user_folder') . $jobs->profile_pic) : '';
                return $jobs;
            });
            */

        // Add profile_pic_path to each job
        $jobs->getCollection()->transform(function ($job) {
            $job->profile_pic_path = (!empty($job->profile_pic)) ? url(config('custom.user_folder') . $job->profile_pic) : '';
            return $job;
        });


        $data['jobs'] = $jobs;

        $data['empTypes'] = DB::table('employment_types as et')
            ->select('et.id', 'et.title')
            ->where('et.status', '1')
            ->whereNull('et.deleted_at')
            ->orderBy('et.title', 'ASC')
            ->get()->toArray();

        $data['title'] = 'Browse jobs';
        return view('search', $data);
    }

    public function forTravelNurses()
    {
        $data['title'] = 'For Travel Nurses';
        return view('forTravelNurses/forTravelNurses', $data);
    }

    public function travelNurseBenefits()
    {
        $data['title'] = 'Travel Nurse Benefits';
        return view('forTravelNurses/travelNurseBenefits', $data);
    }

    public function professionalProfile()
    {
        $data['title'] = 'Professional Profile';
        return view('forTravelNurses/professionalProfile', $data);
    }

    public function documentSafe()
    {
        $data['title'] = 'Document Safe';
        return view('forTravelNurses/documentSafe', $data);
    }

    public function resumeUploading()
    {
        $data['title'] = 'Resume Uploading';
        return view('forTravelNurses/resumeUploading', $data);
    }

    public function applicationStatusTracking()
    {
        $data['title'] = 'Application Tracking System';
        return view('forTravelNurses/applicationStatusTracking', $data);
    }

    public function emailNotification()
    {
        $data['title'] = 'Email & Notification';
        return view('forTravelNurses/emailNotification', $data);
    }

    public function messagingNotification()
    {
        $data['title'] = 'Messaging & Notification';
        return view('forTravelNurses/messagingNotification', $data);
    }

    public function messagingSMS()
    {
        $data['title'] = 'Messaging & SMS';
        return view('forTravelNurses/messagingSMS', $data);
    }

    public function bookmarkJob()
    {
        $data['title'] = 'Bookmark Job';
        return view('forTravelNurses/bookmarkJob', $data);
    }

    public function shortlistedJobs()
    {
        $data['title'] = 'Shortlisted Jobs';
        return view('forTravelNurses/shortlistedJobs', $data);
    }

    public function forEmployers()
    {
        $data['title'] = 'For Employers';
        return view('forEmployers', $data);
    }

    public function forTravelAgencies()
    {
        $data['title'] = 'For Travel Agencies';
        return view('forTravelAgencies/forTravelAgencies', $data);
    }

    public function agencyJobPosting()
    {
        $data['title'] = 'Job Posting';
        return view('forTravelAgencies/jobPosting', $data);
    }

    public function candidateTrackingSystem()
    {
        $data['title'] = 'Candidate Tracking System';
        return view('forTravelAgencies/candidateTrackingSystem', $data);
    }

    public function agencyApplicantTrackingSystem()
    {
        $data['title'] = 'Applicant Tracking System';
        return view('forTravelAgencies/applicantTrackingSystem', $data);
    }

    public function agencySubmissionFiles()
    {
        $data['title'] = 'Submission Files';
        return view('forTravelAgencies/submissionFiles', $data);
    }

    public function agencyTravelNurseManagement()
    {
        $data['title'] = 'Travel Nurse Management';
        return view('forTravelAgencies/travelNurseManagement', $data);
    }

    public function agencyComplianceFiles()
    {
        $data['title'] = 'Compliance Files';
        return view('forTravelAgencies/complianceFiles', $data);
    }

    public function agencyFollowUpScheduling()
    {
        $data['title'] = 'Follow Up Scheduling';
        return view('forTravelAgencies/followUpScheduling', $data);
    }

    public function agencyTaskManagement()
    {
        $data['title'] = 'Task Management';
        return view('forTravelAgencies/taskManagement', $data);
    }

    public function forHealthcareFacilities()
    {
        $data['title'] = 'For Healthcare Facilities';
        return view('forHealthcareFacilities/forHealthcareFacilities', $data);
    }

    public function facilityJobPosting()
    {
        $data['title'] = 'Job Posting';
        return view('forHealthcareFacilities/jobPosting', $data);
    }

    public function applicantTrackingSystem()
    {
        $data['title'] = 'Applicant Tracking System';
        return view('forHealthcareFacilities/applicantTrackingSystem', $data);
    }

    public function facilityTravelNurseManagement()
    {
        $data['title'] = 'Travel Nurse Management';
        return view('forHealthcareFacilities/travelNurseManagement', $data);
    }

    public function facilityComplianceFiles()
    {
        $data['title'] = 'Compliance Files';
        return view('forHealthcareFacilities/complianceFiles', $data);
    }

    public function facilityFollowUpScheduling()
    {
        $data['title'] = 'Follow Up Scheduling';
        return view('forHealthcareFacilities/followUpScheduling', $data);
    }

    public function facilityTaskManagement()
    {
        $data['title'] = 'Task Management';
        return view('forHealthcareFacilities/taskManagement', $data);
    }

    public function jobCategories()
    {

        $data['professions'] = DB::table('professions as p')
            ->leftJoin(DB::raw('(SELECT profession_id, COUNT(*) as job_count FROM jobs WHERE deleted_at IS NULL and status = 1  GROUP BY profession_id) as j'), 'p.id', '=', 'j.profession_id')
            ->select('p.*', DB::raw('COALESCE(j.job_count, 0) as job_count'))
            ->where('p.status', '1')
            ->whereNull('p.deleted_at')
            ->orderBy('p.profession', 'ASC')
            ->get()->toArray();

        $data['title'] = 'Job Categories';
        return view('jobCategories', $data);
    }

    public function jobs(Request $request, $pid = NULL)
    {

        $perPage = $request->input('per_page', 6);

        $jobs = DB::table('jobs as j')
            ->join('users as u', 'u.id', '=', 'j.user_id')
            ->leftJoin('states as s', 'j.state_id', '=', 's.id')
            ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
            ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
            ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
            ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_end_range', 's.name as state_name',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.name as company_name', 'u.profile_pic', 'u.role_id as compnay_role_id')
            ->where('j.deleted_at', NULL);

        if ($pid != NULL) {
            $jobs->where('j.profession_id', $pid);
        }

        $jobs = $jobs->orderBy('j.id', 'desc')
            ->paginate($perPage);
        /*
            ->map(function ($jobs) {
                // Add dir_path column and its value to each record
                $jobs->profile_pic_path = (!empty($jobs->profile_pic)) ? url(config('custom.user_folder') . $jobs->profile_pic) : '';
                return $jobs;
            });
            */

        // Add profile_pic_path to each job
        $jobs->getCollection()->transform(function ($job) {
            $job->profile_pic_path = (!empty($job->profile_pic)) ? url(config('custom.user_folder') . $job->profile_pic) : '';
            return $job;
        });


        $data['jobs'] = $jobs;

        $data['title'] = 'All Jobs';
        return view('jobs', $data);
    }

    public function job(Request $request, $id)
    {

        $job = DB::table('jobs as j')
            ->join('users as u', 'u.id', '=', 'j.user_id')
            ->leftJoin('clients as c', 'c.user_id', '=', 'j.user_id')
            ->leftJoin('states as s', 'j.state_id', '=', 's.id')
            ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
            ->leftJoin('states as s2', 'c.state_id', '=', 's2.id')
            ->leftJoin('cities as city2', 'c.city_id', '=', 'city2.id')
            ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
            ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
            ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_type', 'j.salary_end_range', 's.name as state_name',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.profile_pic', 'j.description', 'j.qualification', 'j.responsibilities', 'j.end_date', 'j.min_work_per_week', 'j.salary_type', 'c.company_name', 'c.website', 'c.primary_industry', 'c.company_size', 'c.bio', 'c.founded_in', 'c.social_media_links', 'u.profile_pic', 'u.role_id as compnay_role_id', 'u.email', 'u.phone', 's2.name as client_state_name',  'city2.city_name as client_city_name')
            ->where('j.deleted_at', NULL)
            ->where('j.unique_id', $id)
            ->first();

        // Add profile_pic_path to each job
        /*
        $jobs->getCollection()->transform(function ($job) {
            $job->profile_pic_path = (!empty($job->profile_pic)) ? url(config('custom.user_folder') . $job->profile_pic) : '';
            return $job;
        });
        */

        $data['row'] = $job;

        $data['relatedJobs'] = DB::table('jobs as j')
            ->join('users as u', 'u.id', '=', 'j.user_id')
            ->leftJoin('states as s', 'j.state_id', '=', 's.id')
            ->leftJoin('cities as city', 'j.city_id', '=', 'city.id')
            ->leftJoin('professions as p', 'j.profession_id', '=', 'p.id')
            ->leftJoin('specialities as sp', 'j.specialty_id', '=', 'sp.id')
            ->leftJoin('shifts as sf', 'j.shift_id', '=', 'sf.id')
            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_type', 'j.salary_end_range', 's.name as state_name',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.name as company_name', 'u.profile_pic', 'u.role_id as compnay_role_id')
            ->where('j.deleted_at', NULL)
            ->where('j.id', '!=', $job->id)
            ->orderBy('j.id', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($jobs) {
                // Add dir_path column and its value to each record
                $jobs->profile_pic_path = (!empty($jobs->profile_pic)) ? url(config('custom.user_folder') . $jobs->profile_pic) : '';
                return $jobs;
            })
            ->toArray();

        $data['title'] = $job->title;
        return view('job', $data);
    }

    public function termConditions()
    {
        $data['title'] = 'Term & Conditions';
        return view('termConditions', $data);
    }

    public function privacyPolicy()
    {
        $data['title'] = 'Privacy & Policy';
        return view('privacyPolicy', $data);
    }

    public function company()
    {
        $data['title'] = 'Company';
        return view('company', $data);
    }

    public function faqs()
    {
        $data['title'] = 'Frequently Asked Questions';
        return view('faqs', $data);
    }

    public function freeJobApplication()
    {
        $data['title'] = 'Free Job Application';
        return view('freeJobApplication', $data);
    }

    public function locations()
    {
        $data['states'] = DB::table('states as s')
            ->leftJoin(DB::raw('(SELECT state_id, COUNT(*) as job_count FROM jobs WHERE deleted_at IS NULL and status = 1 GROUP BY state_id) as j'), 's.id', '=', 'j.state_id')
            ->select('s.id', 's.name as state_name', 's.slug as state_slug', DB::raw('COALESCE(j.job_count, 0) as job_count'))
            ->where('s.status', '1')
            ->orderBy('s.name', 'ASC')
            ->get()->toArray();

        $data['title'] = 'Locations';
        return view('locations', $data);
    }

    public function nursingCeus()
    {
        $data['title'] = 'Nursing CEUs';
        return view('nursingCeus', $data);
    }

    public function nursingCompactStates()
    {
        $data['title'] = 'Nursing Compact States';
        return view('nursingCompactStates', $data);
    }

    public function travelNurseHousing()
    {
        $data['title'] = 'Travel Nurse Housing';
        return view('travelNurseHousing', $data);
    }

    public function travelNurseBlogs()
    {
        $data['title'] = 'Travel Nurse Blogs';
        return view('travelNurseBlogs', $data);
    }

    public function blogs(Request $request)
    {

        $perPage = $request->input('per_page', 6);

        $blogs = DB::table('blogs as b')
            ->select('b.*')
            ->where('b.deleted_at', NULL)
            ->where('b.status', 1);

        $blogs = $blogs->orderBy('b.id', 'desc')
            ->paginate($perPage);


        // Add profile_pic_path to each job
        $blogs->getCollection()->transform(function ($job) {
            $job->profile_pic_path = (!empty($job->image)) ? url(config('custom.blog_folder') . $job->image) : asset('public/assets/images/default.jpeg');
            return $job;
        });




        $data['blogs'] = $blogs;

        $data['title'] = 'Blogs';
        return view('blogs', $data);
    }

    public function blog(Request $request, $id)
    {

        $blog = DB::table('blogs as b')
            ->select('b.*')
            ->where('b.deleted_at', NULL)
            ->where('b.slug', $id)
            ->get()
            ->map(function ($blog) {
                // Add dir_path column and its value to each record
                $blog->image_path = (!empty($blog->image)) ? url(config('custom.blog_folder') . $blog->image) : '';
                return $blog;
            })
            ->first();


        $data['row'] = $blog;

        // Retrieve the previous blog
        $data['previousBlog'] = DB::table('blogs as b')
            ->select('b.title', 'b.slug')
            ->where('b.deleted_at', NULL)
            ->where('b.id', '<', $blog->id)
            ->orderBy('b.id', 'desc')
            ->first();

        // Retrieve the next blog
        $data['nextBlog'] = DB::table('blogs as b')
            ->select('b.title', 'b.slug')
            ->where('b.deleted_at', NULL)
            ->where('b.id', '>', $blog->id)
            ->orderBy('b.id', 'asc')
            ->first();


        $data['title'] = $blog->title;
        return view('blog', $data);
    }

    public function contactUs()
    {
        $data['title'] = 'Contact Us';
        return view('contactUs', $data);
    }

    public function contactUsSubmit(Request $request)
    {

        //=== Validation Section
        $rules = [
            'username' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ];
        $custom = array(
            'first_name.required' => "Please enter your full name.",
            'email.required' => "Please enter your email.",
            'subject.required' => "Please enter subject.",
            'message.required' => "Please enter your message.",
        );
        $validator = Validator::make($request->all(), $rules, $custom);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }


        $param = array(
            'name' => $request->username,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'created_at' => $this->entryDate,
            'updated_at' => $this->entryDate,
        );

        $last_id = DB::table('contact_enquiries')->insertGetId($param);
        if ($last_id) {


            //Send Notification Email
            $param = array(
                'title' => 'New Contact Enquiry',
                'description' => "A new contact enquiry has been received at " . config('app.name') . ".",
            );

            try {
                Mail::send('emails.contactSubmission', $param, function ($message) use ($param) {
                    $message->from(config('app.noreply_email'), config('app.name'));
                    $message->subject('New Contact Enquiry');
                    $message->to('support@travelnurse.com');
                });
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            $result = array('status' => true, 'message' => 'Hey, Your contact enquiry has been submitted successfully. We will get back to you soon.');
        } else {
            $result = array('status' => false, 'message' => 'Something went wrong, please try again later');
        }

        return response()->json($result);
    }
}
