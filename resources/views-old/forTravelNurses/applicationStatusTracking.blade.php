<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Application Status Tracking</h1>

         <ul class="page-breadcrumb">
            <li>Stay Informed Every Step of the Way: Track Your Journey from Application to Assignment</li>
         </ul>

         @include('components.travelNurseMenus', ['currentPage' => 'applicationStatusTracking'])
      </div>
   </div>
</section>
<!--End Page Title-->

<!-- Job Section -->
<section class="job-section-five style-two">
   <div class="small-container">
      <div class="row wow fadeInUp">
         <div class="featured-column col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="sec-title text-center">
               <h2 class="color-style-1">Application Status Tracking</h2>
               <div class="text">Once you have applied to a position, your application will show a status. This status is the stage that your application is in and is shown to keep you updated at all times. You can check your application status daily to ensure you never miss an update.</div>
            </div>
            <div class="outer-box">

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-5">
                     <div class="content p-0">
                        <h4>1. <span class="color-style-3">Applied</span></h4>
                        <p>Your application has been received and is being reviewed. Please allow up to two hours for the employer to follow up during business hours and up to 12 hours during non business hours. Travel Nurse 911’s goal is to ensure your application is received and reviewed as soon as possible by the employer. </p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-5">
                     <div class="content p-0">
                        <h4>2. <span class="color-style-3">Shortlisted</span></h4>
                        <p>Your application is currently under initial screening that includes verifying your credentials and qualifications. Please make sure you have your profile updated so that you stand out amongst the other applicants. Go to <a href="{{ config('custom.user_profile_url') }}" class="font-weight-bold">My Profile</a> to update your professional with the most updated information. </p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-5">
                     <div class="content p-0">
                        <h4>3. <span class="color-style-3">Submitted</span></h4>
                        <p>You have been submitted to the position you have applied to. Please be on the lookout for a phone call or email from the hiring manager regarding a phone screening. Once you have completed the interview, please notify your Recruiter as soon as possible so they can follow up and secure an offer.
                        </p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-5">
                     <div class="content p-0">
                        <h4>4. <span class="color-style-3">Interview</span></h4>
                        <p>If our status is set to “Interview”, this means that an interview is confirmed or you have already been interviewed for the position. Please follow up with your Recruiter immediately if you have questions. Refer to our <a href="{{ route('faqs') }}" class="font-weight-bold">Interview Questions</a> in the FAQs.
                        </p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-5">
                     <div class="content p-0">
                        <h4>5. <span class="color-style-3">Offered</span></h4>
                        <p>You have been offered the position. Please follow up with your Recruiter if you have not already to let them know if you are accepting or not. Facilities like to know within 24 - 48 hours if not sooner on if an offer would like to move forward so they can interview other candidates if you chose not to move forward.
                        </p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-5">
                     <div class="content p-0">
                        <h4>6. <span class="color-style-3">Hired</span></h4>
                        <p>Congratulations! You have accepted the position you have been offered. Please connect with your Recruiter immediately on next steps that includes signing your travel agreement and onboarding. </p>
                     </div>
                  </div>
               </div>

            </div>
         </div>

         <div class="col-xs-12 col-lg-12 col-md-12 col-sm-12 text-center btn-box my-3">
            <a href="{{ route('jobs-search') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Explore Jobs</span></a>
         </div>

         <div class="col-xs-12 col-lg-12 col-md-12 col-sm-12 text-center">
            <div class="text-box my-3">
               <p class="font-weight-bold">If your application status is not updating, please notify us at <a href="mailto:help@travelnurse911.com" class="font-weight-bold">help@travelnurse911.com</a> or reach out to your Recruiter. Your application status should be updated by your Recruiter so that everyone is on the same page at all times. </p>
            </div>
         </div>

      </div>
   </div>
</section>
<!-- End Job Section -->


@endsection