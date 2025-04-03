<?php



namespace App\Http\Controllers;



use App\Helper\CommonFunction;

use App\Models\User;

use Illuminate\Http\Request;

use DB;

use URL;

use Session;

/*use PDF;*/

use Validator;

use Mail;

use \Mpdf\Mpdf as PDF;

use Illuminate\Support\Facades\Storage;

use App\Models\NewsCategory;

use App\Models\Post;

use App\Models\Marketplace;

use App\Models\Subscribe;



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

            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_type', 'j.salary_end_range','j.show_pay_rate', 's.name as state_name', 's.code as state_code',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.name as company_name', 'u.profile_pic', 'u.role_id as compnay_role_id')

            ->where('j.deleted_at', NULL)

            ->where('u.deleted_at', NULL)

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

        

        $data['announcement'] = DB::table('announcements as a')

            ->select('a.description')

            ->where('a.module', 'HOMEPAGE')

            ->whereNotNull('a.description')

            ->get()->first();



        $data['metadescription'] = 'Find top travel nursing jobs with the best travel nursing job board online. Travel Nurse 911 connects nurses with top healthcare agencies nationwide. Call us!';

        $data['title'] = 'Best Travel Nursing Job Board Online | Travel Nurse 911';

        $data['cur_page'] = 'homepage';

        $data['keywords'] = 'Travel Nursing Jobs, Travel Nurse Job Board, Nurse Jobs Online, Find A Nursing Job, Best Travel Nurse Jobs, Travel Nurse Jobs, Travel Nurse Job, Travel Nurse Assignments, Travel Nurse Contracts, Travel Nursing Jobs Online, Travel Nurse Agencies Near Me';

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

            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_type', 'j.salary_end_range', 'j.show_pay_rate','s.name as state_name', 's.code as state_code',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.profile_pic', 'u.name as company_name', 'u.role_id as compnay_role_id')

            ->where('j.deleted_at', NULL)

            ->where('u.deleted_at', NULL);





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



        $data['metadescription'] = 'Browse jobs. Discover Your Next Career Move: Explore Exciting Opportunities at Travel Nurse 911. Call us 1-800-485-7911';

        $data['title'] = 'Browse jobs';

        $data['keywords'] = '';

        return view('search', $data);

    }



    public function forTravelNurses()

    {

        $data['title'] ='Find Travel Nurse Jobs | Best Travel Nurse Careers';

        $data['metadescription'] = 'Find travel nurse jobs with the best pay, benefits, and career growth! Travel Nurse 911 connects you to the best travel nurse careers options. Apply today!';

        $data['keywords'] = 'Best Travel Nursing Jobs, Find Travel Nurse Jobs, Travel Nurse Jobs Near Me, Travel Nursing Jobs Near Me, Best Travel Nursing Assignments, Best Travel Nurse Contracts, Best Travel Nurse Careers, Find a Travel Nursing Job, Local Nurse Jobs Near Me, Local Nursing Jobs Near Me';

        return view('forTravelNurses/forTravelNurses', $data);

    }



    public function travelNurseBenefits()

    {

        $data['title'] = 'Travel Nurse Benefits | Travel Nurse Pay, Perks & Incentives';

        $data['metadescription'] = 'Travel Nurse 911 connects you to top agencies to get travel nurse benefits like great pay, perks & incentives. Find exclusive listings, bonuses, and support!';

        $data['keywords'] = 'Travel Nurse Benefits, Travel Nurse Pay, Travel Nurse Perks, Travel Nurse Incentives, Benefits to Travel Nurses';

        return view('forTravelNurses/travelNurseBenefits', $data);

    }



    public function professionalProfile()

    {

        $data['title'] = 'Travel Nurse Submission File | Nursing Certifications';

        $data['metadescription'] = 'Keep your travel nurse submission file on point with best travel nursing certifications. Travel Nurse 911 makes finding travel nursing jobs stress-free.';

        $data['keywords'] = 'Travel Nurse Submission File, Travel Nursing Experience, Nursing License, Nursing Certifications, Nursing Work History, Professional Travel Profile, Professional Submission File';

        return view('forTravelNurses/professionalProfile', $data);

    }



    public function documentSafe()

    {

        $data['title'] = 'Document Safe | Documentation Management | Travel Nurse 911';

        $data['metadescription'] = 'Travel Nurse 911 Document Safe ensures your nursing documents are always secure and ready. Easy documentation management for a stress-free job search.';

        $data['keywords'] = 'Document Safe, Documentation Management, Certification Tracking, Compliance Alerts, Document Upload, Upload Documents, Secure Document Upload';

        return view('forTravelNurses/documentSafe', $data);

    }



    public function resumeUploading()

    {

        $data['title'] = 'Resume Uploading';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forTravelNurses/resumeUploading', $data);

    }



    public function applicationStatusTracking()

    {

        $data['title'] = 'Application Tracking System';

        $data['metadescription'] = 'Keep track of your travel nurse applications with ease. TravelNurse911 Application Status Tracking keeps you updated every step of the way ';

        $data['keywords'] = '';

        return view('forTravelNurses/applicationStatusTracking', $data);

    }



    public function emailNotification()

    {

        $data['title'] = 'Email & Notification';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forTravelNurses/emailNotification', $data);

    }



    public function messagingNotification()

    {

        $data['title'] = 'Messaging & Notification';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forTravelNurses/messagingNotification', $data);

    }



    public function messagingSMS()

    {

        $data['title'] = 'Messages & SMS Updates | Travel Nursing Job Alerts';

        $data['metadescription'] = 'Stay updated with travel nursing job alerts via messages and SMS updates. Travel Nurse 911 keeps you informed about new opportunities instantly. Call now!';

        $data['keywords'] = 'Messages & SMS Updates, Travel Nursing Job Alerts';

        return view('forTravelNurses/messagingSMS', $data);

    }



    public function bookmarkJob()

    {

        $data['title'] = 'Bookmark Job';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forTravelNurses/bookmarkJob', $data);

    }



    public function shortlistedJobs()

    {

        $data['title'] = 'Shortlisted Jobs | Interview Preparation | Travel Nurse 911';

        $data['metadescription'] = 'Travel Nurse 911 helps you track shortlisted jobs and ace interviews with expert preparation resources. Land your ideal travel nursing position today!';

        $data['keywords'] = 'Shortlisted Jobs, Interview Preparation, Travel Nurse Job Board, Travel Nurse Platform';

        return view('forTravelNurses/shortlistedJobs', $data);

    }



    public function forEmployers()

    {

        $data['metadescription'] = 'Discover the best travel nursing agencies for healthcare facilities. Streamline staffing and hire top professionals efficiently with Travel Nurse 911. Call us!';

        $data['title'] = 'Best Travel Nursing Agencies | Healthcare Facilities';

        $data['keywords'] = 'Travel Nurse Agencies, Healthcare Facilities, Best Travel Nursing Agencies, Travel Nursing Agencies Near Me, Search Travel Nurse Jobs, Travel Nursing Agencies, Best Travel Nurse Agencies, Travel Nure Agencies Near Me, Local Travel Nursing Agencies Near Me';

        return view('forEmployers', $data);

    }



    public function forTravelAgencies()

    {

        $data['title'] = 'Best Travel Nurse Agencies | Travel Nurse Contracts';

        $data['metadescription'] = 'Travel Nurse 911 is your trusted platform, connecting you with the best travel nurse agencies and travel nurse contracts for top assignments and great benefits.';

        $data['keywords'] = 'Best Travel Nursing Agencies, Travel Nurse Contracts, Travel Nurse Assignments, reputable travel nurse agencies, travel nurse agency';

        return view('forTravelAgencies/forTravelAgencies', $data);

    }



    public function agencyJobPosting()

    {

        $data['title'] = 'Travel Nurse Job Postings | Travel Nursing Recruiters';

        $data['metadescription'] = 'Advertise your travel nurse job postings on TravelNurse911! Connect with skilled professionals and fill vacancies fast through targeted, effective job postings.';

        $data['keywords'] = 'Travel Nurse Job Postings, Travel Nursing Recruiters, Travel Nurse Recruiters, Post Travel Nurse Jobs, Agency Job Listings, Travel Nurse Agency Jobs';

        return view('forTravelAgencies/jobPosting', $data);

    }



    public function candidateTrackingSystem()

    {

        $data['title'] = 'Candidate Tracking System';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forTravelAgencies/candidateTrackingSystem', $data);

    }



    public function agencyApplicantTrackingSystem()

    {

        $data['title'] = 'Travel Nurse Applicant Tracking System | Travel Nurse 911';

        $data['metadescription'] = 'Travel Nurse 911’s applicant tracking system ensures you never miss a job update. Track applications, manage offers, and streamline your hiring process.';

        $data['keywords'] = 'Travel Nurse Applicant Tracking System, Travel Nurse Applicant Tracking, Candidate Tracking Software, Track Applicants';

        return view('forTravelAgencies/applicantTrackingSystem', $data);

    }



    public function agencySubmissionFiles()

    {

        $data['title'] = 'Professional Travel Nurse Submission Files | Travel Nurse 911';

        $data['metadescription'] = 'Streamline travel nursing needs with professional travel nurse submission files. Organize, upload, and share documents securely for seamless staffing processes.';

        $data['keywords'] = 'Professional Travel Nurse Submission Files, Travel Nurse Submission Files, Travel Nurse Submission File';

        return view('forTravelAgencies/submissionFiles', $data);

    }



    public function agencyTravelNurseManagement()

    {

        $data['title'] = 'Travel Nurse Management | Best Travel Nurse Agencies';

        $data['metadescription'] = 'Take control of your career with travel nurse management at Travel Nurse 911! Connect with the best travel nurse agencies, find premium jobs, and enjoy!';

        $data['keywords'] = 'Travel Nurse Management, Best Travel Nurse Agencies';

        return view('forTravelAgencies/travelNurseManagement', $data);

    }



    public function agencyComplianceFiles()

    {

        $data['title'] = 'Travel Nurse Compliance Files | Travel Nurse Assignments';

         $data['metadescription'] = 'Simplify hiring with travel nurse compliance files and travel nurse assignment tool! Post jobs, track credentials, and ensure compliance effortlessly.';

        $data['keywords'] = 'Travel Nurse Assignments, Compliance Files, Compliance Documents, Compliance Documentation, Travel Nurse Compliance Files';

        return view('forTravelAgencies/complianceFiles', $data);

    }



    public function agencyFollowUpScheduling()

    {

        $data['title'] = 'Streamlined Follow Up Scheduling Tool | Travel Nursing 911';

        $data['metadescription'] = 'Keep your job search running smoothly! Travel Nurse 911’s Follow Up Scheduling Tool helps you stay organized with interview and application reminders.';

        $data['keywords'] = 'Travel Nursing, Follow Up Scheduling Tool, Integrated Calendar for Travel Nurses, Travel Nurse Agencies';

        return view('forTravelAgencies/followUpScheduling', $data);

    }



    public function agencyTaskManagement()

    {

        $data['title'] = 'Travel Nurse Task Management | Travel Nursing Agencies';

        $data['metadescription'] = 'Streamline operations with travel nurse task management by TravelNurse911! Simplify scheduling, track tasks and boost efficiency for your travel nurse agency.';

        $data['keywords'] = 'Travel Nurse Task Management, Travel Nursing Agencies, Travel Nurse Agencies';

        return view('forTravelAgencies/taskManagement', $data);

    }



    public function forHealthcareFacilities()

    {

        $data['title'] = 'Healthcare Facility Recruiting | Travel Nurse Job Board';

        $data['metadescription'] = 'Travel Nurse 911 supports healthcare facilities recruiting with top travel nurses, ensuring high-quality patient care and seamless hiring solutions. Visit us.';

        $data['keywords'] = 'Healthcare Facility Recruiting, Travel Nurse Job Board';

        return view('forHealthcareFacilities/forHealthcareFacilities', $data);

    }



    public function facilityJobPosting()

    {

        $data['title'] = 'Job Posting';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forHealthcareFacilities/jobPosting', $data);

    }



    public function applicantTrackingSystem()

    {

        $data['title'] = 'Applicant Tracking System';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forHealthcareFacilities/applicantTrackingSystem', $data);

    }



    public function facilityTravelNurseManagement()

    {

        $data['title'] = 'Travel Nurse Management';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forHealthcareFacilities/travelNurseManagement', $data);

    }



    public function facilityComplianceFiles()

    {

        $data['title'] = 'Compliance Files';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forHealthcareFacilities/complianceFiles', $data);

    }



    public function facilityFollowUpScheduling()

    {

        $data['title'] = 'Follow Up Scheduling';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

        return view('forHealthcareFacilities/followUpScheduling', $data);

    }



    public function facilityTaskManagement()

    {

        $data['title'] = 'Task Management';

        $data['metadescription'] = ' ';

        $data['keywords'] = '';

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

            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_end_range','j.show_pay_rate', 's.name as state_name', 's.code as state_code',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.name as company_name', 'u.profile_pic', 'u.role_id as compnay_role_id')

            ->where('j.deleted_at', NULL)

            ->where('u.deleted_at', NULL);



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

            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_type', 'j.salary_end_range','j.show_pay_rate', 's.name as state_name', 's.code as state_code',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.profile_pic', 'j.description', 'j.qualification', 'j.responsibilities', 'j.end_date', 'j.min_work_per_week', 'j.salary_type', 'c.company_name', 'c.website', 'c.primary_industry', 'c.company_size', 'c.bio', 'c.founded_in', 'c.social_media_links', 'u.profile_pic', 'u.role_id as compnay_role_id', 'u.email', 'u.phone', 's2.name as client_state_name','s2.code as client_state_code',  'city2.city_name as client_city_name')

            ->where('j.deleted_at', NULL)

            ->where('u.deleted_at', NULL)

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

            ->select('j.id', 'j.title', 'j.unique_id', 'j.total_opening', 'j.created_at', 'j.salary_start_range', 'j.salary_type', 'j.salary_end_range','j.show_pay_rate', 's.name as state_name', 's.code as state_code',  'city.city_name', 'p.profession', 'sp.specialty', 'sf.title as shift_title', 'u.name as company_name', 'u.profile_pic', 'u.role_id as compnay_role_id')

            ->where('j.deleted_at', NULL)

            ->where('u.deleted_at', NULL)

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

        $data['title'] = 'Terms and Conditions | Travel Nurse Hiring Platform';

        $data['metadescription'] = 'Travel Nurse 911’s terms and conditions outline the guidelines for job seekers and employers on our travel nurse hiring platform. Stay informed and updated.';

        $data['keywords'] = 'Travel Nurse Terms and Conditions, Travel Nurse Hiring Platform, Travel Nurse Job Board, Travel Nurse Platform';

        return view('termConditions', $data);

    }



    public function privacyPolicy()

    {

        $data['title'] = 'Privacy & Policy | Healthcare Recruiting | Travel Nurse 911';

        $data['metadescription'] = 'Travel Nurse 911 values your privacy. Read our policy to know how we protect your data while connecting you with top healthcare Recruiting opportunities.';

        $data['keywords'] = 'Travel Nurse Privacy & Policy, Healthcare Recruiting';

        return view('privacyPolicy', $data);

    }



    public function company()

    {

        $data['title'] = 'About Travel Nurse 911 | Healthcare Travel Nurse Recruiting';

        $data['metadescription'] = 'Learn About Travel Nurse 911, the leader in healthcare travel nurse recruiting platform. We help nurses with top assignments at leading medical facilities!';

        $data['keywords'] = 'About Travel Nurse 911, Healthcare Travel Nurse Recruiting, Travel Nurse Recruiting Platform, Healthcare Recruiting';

        return view('company', $data);

    }



    public function faqs()

    {

        $data['metadescription'] = 'Get answers to travel nursing FAQs about roles, assignments, pay, and how to start. Begin your journey with Travel Nurse 911 and explore great opportunities.';

        $data['title'] = 'Frequently Asked Questions (FAQ) | Travel Nurse Jobs';

        $data['keywords'] = 'Travel Nurse Jobs, Travel Nurse Job Board,  Travel Nurse Platform, Travel Nursing Questions, Nurse Recruitment FAQs, Travel Nurse Help, Travel Nurse Support';

        return view('faqs', $data);

    }



    public function freeJobApplication()

    {

        $data['title'] = 'Nursing Job Application | Apply For Travel Nursing Jobs';

        $data['metadescription'] = 'Apply for travel nursing jobs application with Travel Nurse 911! Get matched with the best travel nurse agencies and start your next assignment quickly.';

        $data['keywords'] = 'Nursing Job Application, Search Travel Nursing Jobs, Apply For Nursing Jobs, Apply For Travel Nursing Jobs, Apply for Travel Nurse Jobs';

        return view('freeJobApplication', $data);

    }



    public function locations()

    {

        $data['states'] = DB::table('states as s')

            ->leftJoin(DB::raw('(SELECT state_id, COUNT(*) as job_count FROM jobs WHERE deleted_at IS NULL and status = 1 GROUP BY state_id) as j'), 's.id', '=', 'j.state_id')

            ->select('s.id', 's.name as state_name', 's.code as state_code', 's.slug as state_slug', DB::raw('COALESCE(j.job_count, 0) as job_count'))

            ->where('s.status', '1')

            ->orderBy('s.name', 'ASC')

            ->get()->toArray();



        $data['title'] = 'Locations';

        return view('locations', $data);

    }



    public function nursingCeus()

    {

        $data['metadescription'] = 'Keep your nursing license current and grow professionally with Nursing CEUs. Find affordable continuing education for travel nurses’ specific needs. Call now!';

        $data['title'] = 'Nursing CEUs | Continuing Education for Nurses | Travel Nurse';

        $data['keywords'] = 'Nursing CEUs, Nursing CEU, CEU for Nurses, CEUs for Nurses, Continuing Education for Nurses, Nursing Continuing Education, Online Nursing CEUs, Online Nursing CEU, Online CEU for Nurses, Accredited Nursing CEU, Affordable Nursing CEU, Affordable Nursing CEUs.';

        return view('nursingCeus', $data);

    }



    public function nursingCompactStates()

    {

        $data['metadescription'] = 'Get access to the nurse licensure in the nursing compact states. Enjoy freedom to work in multiple states with one license and great benefits. Call now!';

        $data['title'] = 'Nursing Compact States | Nurse Licensure Compact State List';

        $data['keywords'] = 'Nursing Compact States, Nursing Compact State List, Nurse Licensure Compact State List, Nursing Compact State, Nursing Compact';

        return view('nursingCompactStates', $data);

    }



    public function travelNurseHousing()

    {

        $data['metadescription'] = 'Need housing for your next assignment? Travel Nurse 911 connects you with best travel nurse housing websites offering safe, flexible, and affordable options.';

        $data['title'] = 'Best Travel Nurse Housing Websites | Travel Nurse 911';

        $data['keywords'] = 'Best Travel Nurse Housing Websites, Travel Nurse Housing, Temporary Housing for Travel Nurses, Travel Nurse Rentals, Travel Nurse Lodging';

        return view('travelNurseHousing', $data);

    }



    public function travelNurseBlogs()

    {

        $data['metadescription'] = 'Find the best travel nurse blogs with career tips, housing guides, and industry insights. Travel Nurse 911 helps you stay ahead in your nursing journey.';

        $data['title'] = 'Best Travel Nurse Blogs | Travel Nursing Agencies Near Me';

        $data['keywords'] = 'Best Travel Nurse Blogs, Travel Nurse Blogs, Travel Nursing Blog Posts, Travel Nurse Advice, Top Travel Nurse Blogs';

        return view('travelNurseBlogs', $data);

    }



    public function blogs(Request $request)

    {



        $perPage = $request->input('per_page', 6);

        $data['perPage'] = $perPage;

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



        $data['metadescription'] = 'Get the most recent travel nursing tips, insights, and blogs from Travel Nurse 911, offering professional guidance, career trends, and expert advice. Read more.';

        $data['title'] = 'Travel Nurse Tips, Insight and Blogs | Travel Nurse 911';

        $data['keywords'] = 'Travel Nursing Blogs, Travel Nurse Tips';

        return view('blogs', $data);

    }

    

    public function news(Request $request)

    {        

        $recentposts = NewsCategory::with(['posts' => function($query) {

            $query->orderBy('created_at', 'desc');

        }])->get(); 



        $marketplaces = Marketplace::orderBy('created_at', 'desc')->get(); 

              

        $recentposts = NewsCategory::paginate(5);

        $newscategories = NewsCategory::where("status", true)

                        ->withCount(['posts' => function ($query) {

                            $query->where('status', true); // Count only active posts

                        }])

                        ->orderBy("sequence", "ASC")

                        ->get();

        return view('news',compact("recentposts","newscategories","marketplaces"));

    }



    public function newsDetail(Request $request)

    {

        

            $data = Post::where('slug',$request->slug)->first();

        

            if ($data) {

                return view("newsdetail", compact("data"));                

            }

            return abort(404);

        

        

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





        $data['title'] = $blog->meta_title;

        $data['metadescription'] = $blog->meta_description;

        return view('blog', $data);

    }

    

    public function pilotPartnerProgram()

    {

        $data['title'] = 'Travel Nurse Agency Partnership | Travel Nurse 911';

        $data['metadescription'] = 'Partner with Travel Nurse 911 and grow your travel nurse agency. Gain access to job opportunities, recruitment tools, and a network of top nurses. Call Now!';

        $data['keywords'] = 'Travel Nurse Agency Partnership';

        return view('pilotPartnerProgram', $data);

    }

    

    public function pilotPartnerSignup()

    {

        $data['title'] = 'Travel Nurse Agency Partnership | Travel Nurse 911';

        $data['metadescription'] = 'Partner with Travel Nurse 911 and grow your travel nurse agency. Gain access to job opportunities, recruitment tools, and a network of top nurses. Call Now!';

        $data['keywords'] = 'Travel Nurse Agency Partnership';

        return view('pilotPartnerSignup', $data);

    }



    public function pilotSignupSubmit(Request $request)

    {



        //=== Validation Section

        $rules = [

            'username' => 'required',

            'email' => 'required',

            'phone' => 'required',

            'company_name' => 'required',

            'type_of_company' => 'required',

            /*'other_type' => 'required'*/

            'g-recaptcha-response' => 'required' // Add reCAPTCHA validation

        ];

        $custom = array(

            'first_name.required' => "Please enter your full name.",

            'email.required' => "Please enter your email.",

            'phone.required' => "Please enter phone no.",

            'company_name.required' => "Please enter your company name.",

            'type_of_company.required' => "Please enter type of company.",

            /*'other_type.required' => "Please enter your company name.",*/

            'g-recaptcha-response.required' => "Please complete the reCAPTCHA challenge."

        );

        $validator = Validator::make($request->all(), $rules, $custom);



        if ($validator->fails()) {

            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));

        }

        

        // Verify Google reCAPTCHA

        $recaptchaSecret = env('RECAPTCHA_SECRET_KEY');

        $recaptchaResponse = $request->input('g-recaptcha-response');

        $recaptchaVerify = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);

        $recaptchaData = json_decode($recaptchaVerify);



        if (!$recaptchaData->success) {

            return response()->json(['status' => false, 'message' => 'reCAPTCHA verification failed. Please try again.']);

        }





        $param = array(

            'type' => 'pilot',

            'name' => $request->username,

            'email' => $request->email,

            'phone' => $request->phone,

            'company_name' => $request->company_name,

            'type_of_company' => $request->type_of_company,

            'other_type' => $request->other_type,

            'subject' => '', //$request->subject,

            'message' => '', //$request->message,

            'created_at' => $this->entryDate,

            'updated_at' => $this->entryDate,

        );



        $last_id = DB::table('contact_enquiries')->insertGetId($param);

        if ($last_id) {





            //Send Notification Email

            $param = array(

                'title' => 'New Employer Signup Enquiry',

                'description' => "A new employer signup enquiry has been received at  " . config('app.name') . ".",

                'name' => $request->username,

                'email' => $request->email,

                'phone' => $request->phone,

                'company_name' => $request->company_name,

                'type_of_company' => $request->type_of_company,

                'other_type' => $request->other_type,

            );



            try {

                Mail::send('emails.employer-contact-inq', $param, function ($message) use ($param) {

                    $message->subject($param['title']);

                    $message->to(config('custom.email_delivery_to'));

                });                

            } catch (\Exception $e) {

                $error = $e->getMessage();

            }



            $result = array('status' => true, 'message' => 'Thank you! Your employer signup has been submitted successfully. We will review your details and get back to you shortly.');

        } else {

            $result = array('status' => false, 'message' => 'Something went wrong, please try again later');

        }



        return response()->json($result);

    }



    public function contactUs()

    {

        $data['metadescription'] = 'Contact Travel Nurse 911 for assistance with the best travel nurse jobs board. We connect nurses with top-paying jobs with leading travel nurse agencies.';

        $data['title'] = 'Contact Us | Travel Nurse Job Board | Travel Nurse 911';

        $data['keywords'] = 'Travel Nurse Job Board, Travel Nurse Platform';

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

            'g-recaptcha-response' => 'required' // Add reCAPTCHA validation

        ];

        $custom = array(

            'first_name.required' => "Please enter your full name.",

            'email.required' => "Please enter your email.",

            'subject.required' => "Please enter subject.",

            'message.required' => "Please enter your message.",

            'g-recaptcha-response.required' => "Please complete the reCAPTCHA challenge."

        );

        $validator = Validator::make($request->all(), $rules, $custom);



        if ($validator->fails()) {

            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));

        }



        // Verify Google reCAPTCHA

        $recaptchaSecret = env('RECAPTCHA_SECRET_KEY');

        $recaptchaResponse = $request->input('g-recaptcha-response');

        $recaptchaVerify = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);

        $recaptchaData = json_decode($recaptchaVerify);



        if (!$recaptchaData->success) {

            return response()->json(['status' => false, 'message' => 'reCAPTCHA verification failed. Please try again.']);

        }



        $param = array(

            'type' => 'contact',

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

                'name' => $request->username,

                'email' => $request->email,

                'subject' => $request->subject,

                'usermessage' => $request->message

            );



            try {

                Mail::send('emails.contactSubmission', $param, function ($message) use ($param) {

                    $message->subject($param['title']);

                    $message->to(config('custom.email_delivery_to'));

                });

            } catch (\Exception $e) {



                dd($e->getMessage());

                $error = $e->getMessage();

            }



            $result = array('status' => true, 'message' => 'Hey, Your contact enquiry has been submitted successfully. We will get back to you soon.');

        } else {

            $result = array('status' => false, 'message' => 'Something went wrong, please try again later');

        }



        return response()->json($result);

    }

    

    public function references(Request $request, $id)

    {



        try {



            $data['title'] = 'Candidate Reference Information Form';

            // Attempt to decrypt the ID

            $decryptedId = CommonFunction::decryptId($id);



            $referenceRecord = DB::table('user_references as r')

                ->join('users as u', 'u.id', '=', 'r.user_id')

                ->join('user_details as ud', 'ud.user_id', '=', 'u.id')

                ->leftJoin('professions as p', 'ud.profession_id', '=', 'p.id')

                ->select('r.id as ref_id', 'r.user_id', 'u.name', 'u.email', 'u.phone', 'p.profession'

                ,

                'r.name as reference_name','r.phone as reference_phone')

                ->where('r.deleted_at', NULL)

                ->where('r.id', $decryptedId)

                ->get()

                ->first();



            if (empty($referenceRecord)) {

                abort(404, 'Resource not found');

            }

            $data['referenceRecord'] = $referenceRecord;

            

            $referenceDetails = DB::table('user_reference_details as r')

                ->select('r.*')

                ->where('r.reference_id', $decryptedId)

                ->get()

                ->first();

            if (!empty($referenceDetails)) {

                $data['referenceDetails'] = $referenceDetails;

            }



            return view('reference', $data);

        } catch (DecryptException $e) {

            // If decryption fails, throw a 404 error

            throw new NotFoundHttpException('Resource not found');

        }

    }

    

    public function referenceFormSubmit(Request $request)

    {    

        $validator = Validator::make($request->all(),[

            'reference_by_name' => 'required|min:3',

            'reference_by_title' => 'required|min:3',

            'reference_by_phone' => 'required',

            'reference_by_email' => ['required','regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],

            'reference_by_signature' => 'required|min:2',

            'reference_by_signature_date' => 'required',

        ]);

        if ($validator->fails()) {

            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));

        }

    

        $param = $request->all();

        $param['reference_by_signature_date'] = CommonFunction::changeDateFormat($param['reference_by_signature_date']);

        unset($param['_token']);



        $referenceRecord = DB::table('user_reference_details as r')

            ->select('r.id')

            ->where('r.reference_id', $request->reference_id)

            ->where('r.user_id', $request->user_id)

            ->first();

        if (!empty($referenceRecord)) {

            $param['updated_at'] = $this->entryDate;

            DB::table('user_reference_details')->where('id', $referenceRecord->id)->update($param);

        } else {

            $param['created_at'] = $this->entryDate;

            DB::table('user_reference_details')->insert($param);

        }



        /*

        //Send Notification Email

        $param = array(

            'title' => 'New Contact Enquiry',

            'description' => "A new contact enquiry has been received at " . config('app.name') . ".",

        );



        try {



            Mail::send('emails.contactSubmission', $param, function ($message) use ($param) {

                $message->from(config('app.noreply_email'), config('app.name'));

                $message->subject('New Contact Enquiry');

                $message->to(config('custom.email'));

            });

        } catch (\Exception $e) {

            $error = $e->getMessage();

        }

        */



        $result = array('status' => true, 'message' => 'Hey, Your reference detail has been submitted successfully');

        return response()->json($result);

    }

    

    public function submissionFile(Request $request, $id)

    {

        $data['title'] = 'Submission File';



        $userRecord = DB::table('users as u')

            ->join('user_details as ud', 'ud.user_id', '=', 'u.id')

            ->leftJoin('states as s', 'ud.state_id', '=', 's.id')

            ->leftJoin('cities as city', 'ud.city_id', '=', 'city.id')

            ->leftJoin('professions as p', 'ud.profession_id', '=', 'p.id')

            ->leftJoin('specialities as sp', 'ud.specialty_id', '=', 'sp.id')

            ->select(

                'u.id',

                'u.name',

                'u.email',

                'u.phone',

                'p.profession',

                'sp.specialty',

                's.name as state_name',

                'city.city_name',

                'ud.address_line1',

                'ud.address_line2',

                'ud.available_start_date',

                'ud.total_experience',

                'ud.specialty_experience',

                'ud.EMR_experience',

                'ud.teaching_hospital_experience',

                'ud.travel_experience',

                'ud.fully_vaccinated',

                'u.unique_id'

            )

            ->where('u.deleted_at', NULL)

            ->where('u.unique_id', $id)

            ->get()

            ->first();



        if (empty($userRecord)) {

            abort(404, 'Resource not found');

        }



        $data['educationalDetails'] = DB::table('user_educations as ue')

        ->select('ue.*')

            ->where('ue.user_id', $userRecord->id)

            ->whereNull('ue.deleted_at')

            ->orderBy('ue.currently_attending', 'desc')  // currently_attending 1 first

            ->orderBy('ue.end_year', 'desc')            // end_year in descending order

            ->orderBy('ue.end_month', 'desc')

            ->get()->toArray();



        $result = DB::table('user_preferred_shifts as us')

        ->leftJoin('shifts as s', 's.id', '=', 'us.shift_id')

        ->select('s.title')

            ->where('us.user_id', $userRecord->id)->get()->toArray();

        if (!empty($result)) {

            foreach ($result as $val) {

                $data['desired_shifts'][] = $val->title;

            }

        }



        $data['work_histories'] = DB::table('user_work_histories as wh')

        ->leftJoin('states as s', 'wh.state_id', '=', 's.id')

            ->leftJoin('cities as city', 'wh.city_id', '=', 'city.id')

            ->leftJoin('professions as p', 'wh.profession_id', '=', 'p.id')

            ->leftJoin('specialities as sp', 'wh.specialty_id', '=', 'sp.id')

            ->leftJoin('employment_types as et', 'et.id', '=', 'wh.employment_type_id')

            ->select('wh.id', 'wh.title', 'wh.company_name', 'wh.start_month', 'wh.start_year', 'wh.end_month', 'wh.end_year', 'wh.currently_working', 'wh.state_id', 'wh.city_id', 'wh.profession_id', 'wh.specialty_id', 'wh.employment_type_id', 's.name as state_name', 's.code as state_code', 'city.city_name', 'p.profession', 'sp.specialty', 'et.title as employment_type_title')

            ->where('wh.user_id', $userRecord->id)

            ->where('wh.deleted_at', NULL)

            ->orderBy('wh.id', 'desc')

            ->get()->toArray();





        // Get the latest submission file title from user_submission_file_docs table

        $counter = 1;

        $latestSubmissionFile = DB::table('user_submission_file_docs')

        ->where('user_id', $userRecord->id)

            ->whereNotNull('file_title')

            ->orderby('id','DESC')

            ->first();

        if (!$latestSubmissionFile) {

            $title = 'Submission-File-1';

        } else {

            preg_match('/Submission-File-(\d+)$/', $latestSubmissionFile->file_title, $matches);

            $counter = isset($matches[1]) ? (int)$matches[1] : 0; // Extract the number or default to 0

            $title = 'Submission-File-' . $counter + 1 ;

        }

        // Update documents file title

        $user_submission_file_docs = DB::table('user_submission_file_docs')->where('status', 0)->update([

            'file_title' => $title,

            'status' => 1,

        ]);

        if($user_submission_file_docs == 0) {

            $title = 'Submission-File-' . $counter;

        }



        $data['completed_checklists'] = DB::table('compliance_checklists as cc')

            ->join('user_compliance_checklists as ucc', 'ucc.checklist_id', '=', 'cc.id')

            ->join('user_submission_file_docs as sf_doc', 'cc.id', '=', 'sf_doc.checklist_id')

            ->leftJoin('users as u', 'u.id', '=', 'cc.created_by')

            ->where('cc.status', 1)

            ->where('cc.deleted_at', NULL)

            ->where('ucc.status', 1)

            ->where('ucc.user_id', $userRecord->id)

            ->where('sf_doc.user_id', $userRecord->id)

            ->where('sf_doc.file_title', $title)

            ->select('cc.id', 'cc.slug', 'cc.title', 'ucc.created_at as submitted_on', 'ucc.updated_at as updated_on', 'cc.checklist_meta', 'ucc.checklist_meta as checklist_answer', 'sf_doc.id as sf_doc_id')

            ->get()->toArray();

        

        $data['checklistRow'] = !empty($data['completed_checklists']) ? $data['completed_checklists'][0] : null;



        $data['userRecord'] = $userRecord;



        $data['documents'] = DB::table('user_documents as d')

            ->join('user_submission_file_docs as sf_doc','d.id','=','sf_doc.doc_id')

            ->leftJoin('doc_types as dt', 'd.doc_type_id', '=', 'dt.id')

            ->select('d.id as document_id', 'd.title', 'd.expiry_date', 'dt.doc_name as doc_type_name', 'd.file_name', 'sf_doc.id as sf_doc_id')

            ->where('sf_doc.user_id', $userRecord->id)

            ->where('sf_doc.file_title', $title)

            ->where('d.deleted_at', NULL)

            ->orderBy('d.id', 'desc')

            ->get()

            ->toArray();

        

        $data['userStateLicense'] = DB::table('user_state_license as d')

        ->select('d.license_name', 'd.location', 'd.license_expiry_date')

        ->where('d.user_id', $userRecord->id)

            ->orderBy('d.id', 'desc')

            ->get()

            ->toArray();



        $data['userActiveCertificate'] = DB::table('user_active_certificates as d')

        ->select('d.certificate_name', 'd.certificate_expiry_date')

        ->where('d.user_id', $userRecord->id)

            ->orderBy('d.id', 'desc')

            ->get()

            ->toArray();



        $data['references'] = DB::table('user_reference_details as d')

            ->leftJoin('user_references as ur', 'd.reference_id', '=', 'ur.id')

            ->select('d.*','ur.*')

            ->where('d.user_id', $userRecord->id)

            ->where('ur.is_verify', 1)

            ->orderBy('d.id', 'desc')

            ->get()

            ->toArray();



        $data['user_rto_details'] = DB::table('user_rto_details as rto')       

            ->where('rto.user_id', $userRecord->id)

            ->select('rto.*',

                    DB::raw("DATE_FORMAT(rto_start_date, '%m/%d/%Y') as rto_start_date"),

                    DB::raw("DATE_FORMAT(rto_end_date, '%m/%d/%Y') as rto_end_date")

                )

            ->get()->toArray();



        if (!empty($request->save) && $request->save == 1) {

            $data['hide_action'] = true;

            $upload = false;



            $pdf = PDF::loadView('submissionFile', $data);

            return $pdf->stream('document.pdf');





            $path = config('custom.doc_folder');

            $fileName = 'Submission-File-' . date('Ymd') . time() . '.pdf';

            //$fullPath = $path . $fileName;

            

            $fullPath = 'uploads/documents/' . $fileName;

            $upload = file_put_contents($fullPath, $pdfContent);



            if ($upload) {

            //     // store submission file

            //     $param = [

            //         'user_id' => $userRecord->id,

            //         'Title' => $title,

            //         'file_name' => $fileName,

            //         'file_type' => 'pdf',

            //         'created_by' => $userRecord->id,

            //         'created_at' => now(),

            //         'updated_at' => now(),

            //     ];

            //     DB::table('user_documents')->insert($param);

                $result = array('status' => true, 'message' => "Success");

            } else {

                $result = array('status' => false, 'message' => "Something went wrong!");

            }

            return response()->json($result);

        } else {

            return view('submissionFile', $data);

        }

    }

    

    public function ourStory() 

    {

        $data['metadescription'] = 'Travel Nurse 911 is the top travel nurse job platform, connecting nurses with top jobs nationwide. Learn how travel nursing is easier with healthcare facilities.';

        $data['title'] = 'Our Story | Travel Nurse Job Platform | Travel Nurse 911';

        $data['keywords'] = 'Nurse Recruiters for Travel Nurses, Travel Nurse Jobs, Travel Nursing Agencies'; 

        return view('our-story', $data);

    }


    public function resources(Request $request)

    {

        $agencies = DB::table('users as u')

            ->join('clients as c', 'c.user_id', '=', 'u.id')

            ->where('u.role_id', User::ROLE_AGENCY)

            ->where('u.status', 1)

            ->where('u.deleted_at', NULL)

            ->select(

                'c.user_id as client_id',

                'c.company_name as agency_name',

                DB::raw('(SELECT COUNT(*) FROM users WHERE created_by = c.user_id) as recruiters_count'),

                DB::raw('(SELECT COUNT(*) FROM jobs WHERE user_id = c.user_id AND status = 1) as jobs_count'),

                DB::raw('ROUND(IFNULL((SELECT AVG(rating) FROM agency_feedbacks WHERE client_id = c.user_id AND is_approved = 1), 0), 1) as avg_rating'),

                DB::raw('(SELECT COUNT(*) FROM agency_feedbacks WHERE client_id = c.user_id AND is_approved = 1) as review_count')

            )

            ->get();



        return view('resources', compact('agencies'));

    }



    public function getResourceFeedback(Request $request)

    {

        try {

            // Fetch agency record

            $vendor = DB::table('users as u')

                            ->join('clients as c', 'c.user_id', '=', 'u.id')

                            ->select('c.user_id as client_id', 'u.name', 'c.company_name', 'u.email', 'u.phone', 'u.profile_pic')

                            ->whereNull('u.deleted_at')

                            ->where('u.id', $request->client_id)

                            ->first();



            if (!$vendor) {

                return response()->json(['message' => 'Agency not found'], 404);

            }



            // Fetch agency feedbacks

            $reviews = DB::table('agency_feedbacks as af')

                        ->join('users as u', 'u.id', '=', 'af.user_id')

                        ->select('af.*', 'u.name as username', 'u.profile_pic')

                        ->where([

                            ['af.client_id', '=', $request->client_id],

                            ['af.status', '=', 1],

                            ['af.is_approved', '=', 1]

                        ])

                        ->orderByDesc('af.id')

                        ->get();



            $html = view('resources-feedback', compact('vendor', 'reviews'))->render();

            return response()->json(['success' => 1,

                'data' => $html

            ]);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);

        }

    }



    public function submitReview(Request $request)

    {

        // Validate request data with custom messages

        $request->validate([

            'vendor_agencies_id' => 'required|exists:clients,user_id',

            'rating' => 'required|integer|min:1|max:5',

            'email' => 'required',

        ]);

        // Check user exist or not

        $user = User::where('email', $request->email)->first();

        if (!$user) {

            return response()->json(['error' => 'This email is not registered with us, Please register!']);

        }

        if($user->role_id != 0) {
            return response()->json(['error' => 'You are not authorized to write review!']);
        }


        DB::table('agency_feedbacks')->insert([

            'client_id' => $request->vendor_agencies_id,

            'user_id' => $user->id,

            'rating' => $request->rating,

            'comments' => $request->review,

            'status' => 0,

            'is_approved' => 0,

            'created_at' => now(),

            'updated_at' => now(),

        ]);



        return response()->json([

            'success' => true,

            'message' => 'Review submitted successfully!',

        ]);

    }

    

    public function newssubscribe(Request $request)

    {



        $request->validate([

            'email' => 'required|email|unique:subscribes,email',

            'category_id' => 'required|integer',

            'category_title' => 'required|string',

        ]);



        $subscribe = Subscribe::create([

            'email' => $request->input('email'),

            'category_id' => $request->input('category_id'),

            'category_title' => $request->input('category_title'),

        ]);



        return response()->json(['message' => 'Subscription successful!', 'subscribe' => $subscribe]);

    }



    public function particularcat($slug)

    {
        $category = NewsCategory::where('slug', $slug)->first();

        $posts = $category->posts()->where('status',1)->orderByDesc('posted_date')->get();

        $categories = NewsCategory::withCount(['posts' => function ($query) {

            $query->where('status', 1);

        }])->where("status", true)->orderBy("sequence", "ASC")->get(); 

       return view('newscategory', compact('category', 'posts','categories'));

    }

    

    public function trackNewsClicks(Request $request)

    {

        if($request->type == 1) {

            $categoryClick = NewsCategory::where('id', $request->click_id)->first(); 

            if($categoryClick) {

                // Increment the click count

                $categoryClick->increment('clicks');

            }

            return response()->json(['success' => true]);

        } else {

            $postClick = Post::where('id', $request->click_id)->first();

            if ($postClick) {

                // Increment the click count

                $postClick->increment('views');

            }

            return response()->json(['success' => true]);

        }

        return response()->json(['error' => false]);

    }



}

