<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Messages & SMS Updates
         </h1>
         <ul class="page-breadcrumb">
            <li>Stay Updated with Timely Email Alerts and SMS
            </li>
         </ul>
         @include('components.travelNurseMenus', ['currentPage' => 'messagingSMS'])
      </div>
   </div>
</section>
<!--End Page Title-->


<!-- Work Section -->
<section class="work-section style-four">
   <div class="auto-container">


      <div class="row">
         <!-- Work Block -->
         <div class="news-block col-lg-3 col-md-6 col-sm-12">
            <div class="inner-box bg-white h-100">
               <div class="image-box">
                  <figure class="image"><img src="{{ asset('public/assets/images/media/Post-34-01.jpg') }}" alt=""></figure>
               </div>
               <div class="lower-content">
                  <h3 class="color-style-3">Manage Communication Preferences</h3>
                  <p class="text">Efficiently control how you receive updates with our email and notification tools. Stay informed about crucial job details, upcoming interviews, and task deadlines through personalized alerts.
                  </p>
               </div>
            </div>
         </div>

         <div class="news-block col-lg-3 col-md-6 col-sm-12">
            <div class="inner-box bg-white h-100">
               <div class="image-box">
                  <figure class="image"><img src="{{ asset('public/assets/images/media/Post-38.jpg') }}" alt=""></figure>
               </div>
               <div class="lower-content">
                  <h3 class="color-style-3">Email Preferences</h3>
                  <p class="text">Tailor how you get notifications. Select from daily summaries, real-time alerts, or weekly digests according to your needs. Stay up-to-date without being inundated by emails.
                  </p>
               </div>
            </div>
         </div>

         <div class="news-block col-lg-3 col-md-6 col-sm-12">
            <div class="inner-box bg-white h-100">
               <div class="image-box">
                  <figure class="image"><img src="{{ asset('public/assets/images/media/Post-35.jpg') }}" alt=""></figure>
               </div>
               <div class="lower-content">
                  <h3 class="color-style-3">SMS Notifications</h3>
                  <p class="text">Choose to receive important updates via SMS for immediate alerts on job offers, interview invites, and urgent tasks. Stay connected and never miss a critical update, even when you're on the go.
                  </p>
               </div>
            </div>
         </div>


         <div class="news-block col-lg-3 col-md-6 col-sm-12">
            <div class="inner-box bg-white h-100">
               <div class="image-box">
                  <figure class="image"><img src="{{ asset('public/assets/images/media/Post-36.jpg') }}" alt=""></figure>
               </div>
               <div class="lower-content">
                  <h3 class="color-style-3">Notification Settings</h3>
                  <p class="text">Adjust your notification preferences to prioritize what’s important. Easily manage alerts for job applications, interview scheduling, compliance updates, and more to stay organized and responsive in your travel nursing career.
                  </p>
               </div>
            </div>
         </div>

         <div class="col-lg-12 col-md-12 col-sm-12 text-center">
            <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-one bg-blue">
               <span class="btn-title">Update Message Notifications</span>
            </a>
         </div>

      </div>
   </div>
</section>
<!-- End Work Section -->

<!-- Call To Action -->
<section class="registeration-bannerss job-categories secion-10 border-bottom-0">
   <div class="auto-container">
      @include('components.call-to-actions.callToActionBanners')
   </div>
</section>
<!-- End Call To Action -->

@endsection
