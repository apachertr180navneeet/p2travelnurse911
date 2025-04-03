<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Shortlisted & Saved Jobs</h1>
         <ul class="page-breadcrumb">
            <li>Save your favorite job listings and access them anytime</li>
         </ul>
         @include('components.travelNurseMenus', ['currentPage' => 'bookmarkJob'])
      </div>
   </div>
</section>
<!--End Page Title-->

<!-- About Section Three -->
<section class="about-section-three pb-0">
   <div class="auto-container">

      <div class="text-box">
         <h5 class="mb-3 text-center">Easily save and organize job postings that attract your attention using our Bookmark Job feature. Keep tabs on potential opportunities and return to them at your convenience to make the best choices for your travel nursing career.

         </h5>
      </div>

   </div>
</section>
<!-- End About Section Three -->

<!-- Job Categories -->
<section class="job-categories secion-3 border-bottom-0">
   <div class="small-container">
      <div class="sec-title text-center">
         <h2>Saved Jobs</h2>
      </div>

      <div class="row wow fadeInUp">
         <!-- Category Block -->
         <div class="category-block-two col-lg-6 col-md-6 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <span class="icon flaticon-money-1"></span>
                  <h4>Job Details</h4>
                  <p>Access your personalized list of Saved jobs. View details, requirements, and employer information all in one place. Filter and sort by location, specialty, and deadline for a more efficient search.
                  </p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-lg-6 col-md-6 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <span class="icon flaticon-promotion"></span>
                  <h4>Application Status
                  </h4>
                  <p>Track the status of each Saved job. See which positions you’ve applied to, which are still open, and which have upcoming deadlines. Stay organized and proactive in your job search.</p>
               </div>
            </div>
         </div>


      </div>
   </div>

   <div class="small-container">
      <div class="sec-title text-center">
         <h2>Job Information</h2>
      </div>

      <div class="row wow fadeInUp">
         <!-- Category Block -->
         <div class="category-block-two col-lg-6 col-md-6 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <span class="icon flaticon-money-1"></span>
                  <h4>Detailed View</h4>
                  <p>Click on any Saved job to access comprehensive details about the position, including the job description, responsibilities, qualifications, location, salary, and benefits. Having all this information at your fingertips simplifies your decision-making process.
                  </p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-lg-6 col-md-6 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <span class="icon flaticon-promotion"></span>
                  <h4>Employer Insights</h4>
                  <p>Learn more about potential employers by exploring details about the healthcare facilities, their work culture, and reviews from other travel nurses. This insight helps you understand the work environment and make informed choices.
                  </p>
               </div>
            </div>
         </div>


      </div>
   </div>


   <div class="small-container">
      <div class="sec-title text-center">
         <h2>Application Management</h2>
      </div>

      <div class="row wow fadeInUp">
         <!-- Category Block -->
         <div class="category-block-two col-lg-6 col-md-6 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <span class="icon flaticon-money-1"></span>
                  <h4>Application Status</h4>
                  <p>Keep track of your applications for Saved jobs. Check when you applied, the status of your application, and any necessary follow-up actions. This feature helps streamline your job search.
                  </p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-lg-6 col-md-6 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <span class="icon flaticon-promotion"></span>
                  <h4>Interview Coordination</h4>
                  <p>Easily schedule interviews for jobs you’re progressing with using our integrated calendar. Receive reminders and updates to ensure you’re well-prepared for each interview.
                  </p>
               </div>
            </div>
         </div>


      </div>
   </div>
</section>
<!-- End Job Categories -->

@endsection