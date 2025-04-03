<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Effortless Recruitment Management</h1>
         <ul class="page-breadcrumb">
            <li>Quickly post travel nurse job openings and reach many qualified candidates</li>
         </ul>
         @include('components.travelAgenciesMenus', ['currentPage' => 'jobPosting'])
      </div>
   </div>
</section>
<!--End Page Title-->


<section class="steps-section pb-lg-0">
   <div class="auto-container">

      <div class="row">
         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column">
               <figure class="image"><img src="{{ asset('public/assets/images/media/Post-22.jpg') }}" alt=""></figure>
            </div>
         </div>

         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInUp pl-3">
               <div class="sec-title">
                  <h2 class="color-style-1">Quickly Find the Ideal Travel Nurses</h2>
                  <div class="text">Streamline your hiring process effortlessly. Post jobs to attract top talent, manage candidates through stages like applied, shortlisted, offered, and hired, and connect seamlessly via our messaging center and SMS.</div>

                  <div class="job-block-two mt-2">
                     <div class="inner-box p-4">
                        <div class="content p-0">
                           <h4 class="color-style-3">Post Free Jobs</h4>
                           <p>Maximize job postings with our unlimited job posting tool. Manage job postings and incoming applications directly from a user-friendly centralized dashboard for efficient hiring.</p>
                        </div>
                     </div>
                  </div>

                  <div class="job-block-two mt-2">
                     <div class="inner-box p-4">
                        <div class="content p-0">
                           <h4 class="color-style-3">Messaging</h4>
                           <p>Monitor incoming messages from applicants via your messaging inbox which includes SMS messaging. Connect with applicants in multiple ways ensuring they are qualified, submitted, and booked in a timely manner.</p>
                        </div>
                     </div>
                  </div>

                  <div class="job-block-two mt-2">
                     <div class="inner-box p-4">
                        <div class="content p-0">
                           <h4 class="color-style-3">Applicant Tracking & Management</h4>
                           <p>Monitor & Track Applicants through our applicant pipleine that includes stages such as applied, shortlisted, offered, and hired. Applicants are immediately notified once their application status has changed and this greatly helps with providing timely feedback and cuts down on back and forth follow up.</p>
                        </div>
                     </div>
                  </div>

                  <div class="job-block-two mt-2">
                     <div class="inner-box p-4">
                        <div class="content p-0">
                           <h4 class="color-style-3">Candidate Tracking & Management</h4>
                           <p>Once a hire is made, manage employee details from onboarding to managing their active travel assignment, compliance documents, and expirations with the compliance file feature.</p>
                        </div>
                     </div>
                  </div>

                  <div class="job-block-two mt-2">
                     <div class="inner-box p-4">
                        <div class="content p-0">
                           <h4 class="color-style-3">Compliance Files</h4>
                           <p>Our Compliance File feature allows for documents to be shared between travel nurses and their employers. Ensure you never miss a critical compliance deadline with our automated alert system. Receive notifications for key dates, such as license renewals, certification expirations, and required training updates. Stay informed and proactive with timely reminders to keep traveler credentials up to date.</p>
                        </div>
                     </div>
                  </div>

                  <div class="job-block-two mt-2">
                     <div class="inner-box p-4">
                        <div class="content p-0">
                           <h4 class="color-style-3">Tasks & Follow-Ups</h4>
                           <p>Our Task and Follow-Up features allow Recruiters to create tasks and follow-ups seamlessly and manage them from your calendar. These features can be helpful when following up on applications and setting future follow-up reminders.</p>
                        </div>
                     </div>
                  </div>

                  <a href="{{ config('custom.client_login_url') }}" class="theme-btn btn-style-one">Start Hiring Today</a>

               </div>
            </div>
         </div>
      </div>

   </div>
</section>


<!-- Job Section -->
<section class="job-section-four">
   <div class="auto-container">
      <div class="row wow fadeInUp">


         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column">
               <div class="sec-title mb-2">
                  <h2 class="color-style-1">How it Works</h2>
               </div>

               <ul class="steps-list mt-0">

                  <li class="mb-2"><span class="count">1</span><span class="color-style-3">Create a Job Posting</span>
                     <p class="mt-2">Effortlessly create detailed job postings tailored to attract qualified travel nurses. Specify job requirements, location, duration, and other essential details to ensure you find the right job seekers.</p>
                  </li>

                  <li class="mb-2"><span class="count">2</span><span class="color-style-3">Manage Applications</span>
                     <p class="mt-2">Stay organized with our robust applicant management tools. Review resumes, schedule interviews, and communicate with job seekers directly through the platform. Track the status of each application to streamline your hiring process.</p>
                  </li>

                  <li><span class="count">3</span><span class="color-style-3">Employer Dashboard</span>
                     <p class="mt-2">Access all your job postings, applicant information, and communication history from a centralized dashboard. Monitor the progress of your job postings, manage compliance documents, and gain insights through detailed analytics to make informed hiring decisions.</p>
                  </li>
               </ul>

            </div>
         </div>

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <figure class="image"><img src="{{ asset('public/assets/images/resource/jobseeker.png') }}" alt=""></figure>
         </div>
      </div>
   </div>
</section>
<!-- End Job Section -->

@endsection
