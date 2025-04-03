<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Notification Updates</h1>
         <ul class="page-breadcrumb">
            <li>Stay Updated with Timely Email Alerts and Notifications
            </li>
         </ul>
      </div>
   </div>
</section>
<!--End Page Title-->


<!-- 
<section class="about-section-three">
   <div class="auto-container">

      <div class="fun-fact-section">
         <div class="row">
            <div class="counter-column col-lg-4 col-md-4 col-sm-12 wow fadeInUp">
               <div class="count-box"><span class="count-text" data-speed="3000" data-stop="4">0</span>M</div>
               <h4 class="counter-title">4 million daily active users</h4>
            </div>

            <div class="counter-column col-lg-4 col-md-4 col-sm-12 wow fadeInUp" data-wow-delay="400ms">
               <div class="count-box"><span class="count-text" data-speed="3000" data-stop="12">0</span>k</div>
               <h4 class="counter-title">Over 12k open job positions</h4>
            </div>

            <div class="counter-column col-lg-4 col-md-4 col-sm-12 wow fadeInUp" data-wow-delay="800ms">
               <div class="count-box"><span class="count-text" data-speed="3000" data-stop="2">0</span>k</div>
               <h4 class="counter-title">Over 2k job seeker hired</h4>
            </div>
         </div>
      </div>

   </div>
</section>
-->


<!-- Work Section -->
<section class="work-section style-one">
   <div class="auto-container">


      <div class="row">
         <!-- Work Block -->
         <div class="work-block col-lg-3 col-md-6 col-sm-12">
            <div class="inner-box h-100" style="background-color: #f5f7fc;">
               <h5>Manage Communication Preferences</h5>
               <p>Efficiently control how you receive updates with our email and notification tools. Stay informed about crucial job details, upcoming interviews, and task deadlines through personalized alerts.
               </p>
            </div>
         </div>

         <!-- Work Block -->
         <div class="work-block col-lg-3 col-md-6 col-sm-12">
            <div class="inner-box h-100" style="background-color: #f5f7fc;">
               <h5>Email Preferences</h5>
               <p>Tailor how you get notifications. Select from daily summaries, real-time alerts, or weekly digests according to your needs. Stay up-to-date without being inundated by emails.
               </p>
            </div>
         </div>

         <!-- Work Block -->
         <div class="work-block col-lg-3 col-md-6 col-sm-12">
            <div class="inner-box h-100" style="background-color: #f5f7fc;">
               <h5>SMS Notifications</h5>
               <p>Choose to receive important updates via SMS for immediate alerts on job offers, interview invites, and urgent tasks. Stay connected and never miss a critical update, even when you're on the go.</p>
            </div>
         </div>

         <!-- Work Block -->
         <div class="work-block col-lg-3 col-md-6 col-sm-12">
            <div class="inner-box h-100" style="background-color: #f5f7fc;">
               <h5>Notification Settings</h5>
               <p>Adjust your notification preferences to prioritize what’s important. Easily manage alerts for job applications, interview scheduling, compliance updates, and more to stay organized and responsive in your travel nursing career.
               </p>
            </div>
         </div>

      </div>
   </div>
</section>
<!-- End Work Section -->

<!-- Call To Action -->
<section class="registeration-bannerss job-categories secion-10">
   <div class="auto-container">
      <div class="row wow fadeInUp">
         <!-- Banner Style One -->
         <div class="banner-style-one col-lg-6 col-md-12 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <h3>Looking for Your Next Great Hire?</h3>
                  <p>Effortlessly find top Travel Nurse talent and enhance your team with our streamlined hiring solutions. </p>
                  <a href="{{ route('contact-us') }}" class="theme-btn btn-style-five">Register Account</a>
               </div>
               <figure class="image"><img src="{{ asset('public/assets/images/hiring-manager.png') }}" /></figure>
            </div>
         </div>

         <!-- Banner Style Two -->
         <div class="banner-style-two col-lg-6 col-md-12 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <h3>Searching for the Ideal Job?</h3>
                  <p>Start your career journey with us. Explore exciting travel nursing opportunities perfectly matched to your skills, desires and ambitions. </p>
                  <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-five">Register Account</a>
               </div>
               <figure class="image"><img src="{{ asset('public/assets/images/candidate2.png') }}" /></figure>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- End Call To Action -->

@endsection