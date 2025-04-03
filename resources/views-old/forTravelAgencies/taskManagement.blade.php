<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Seamless Task Coordination</h1>
         <ul class="page-breadcrumb">
            <li>Efficiently allocate and monitor tasks to maintain organization and ensure nothing is forgotten</li>
         </ul>
         @include('components.travelAgenciesMenus', ['currentPage' => 'taskManagement'])
      </div>
   </div>
</section>
<!--End Page Title-->

<section class="process-section">
   <div class="small-container">

      <div class="image-column mb-5">
         <figure class="image"><img class="border" src="{{ asset('public/assets/images/media/Post-30-01.jpg') }}" alt=""></figure>
      </div>

      <div class="sec-title text-center">
         <h2 class="color-style-1">Streamline Your Task Management</h2>
         <div class="text">Our Task Management system is tailored to help Recruiters stay organized and manage their duties effectively. From daily routines to long-term projects, our user-friendly platform ensures you stay on top of deadlines and can concentrate on delivering excellent care.</div>
      </div>

      <div class="row wow fadeInUp">
         <!-- Process Block -->
         <div class="process-block col-lg-4 col-md-6 col-sm-12">
            <div class="icon-box"><img src="{{ asset('public/assets/images/resource/process-1.png') }}" alt=""></div>
            <h4 class="mb-3">Organize Daily Tasks</h4>
            <p>Centralize all your daily tasks in one easy-to-use dashboard. Quickly add, update, or remove tasks, and set reminders to keep your schedule on track. This feature helps you stay organized and productive throughout your workday.
            </p>
         </div>

         <!-- Process Block -->
         <div class="process-block col-lg-4 col-md-6 col-sm-12">
            <div class="icon-box"><img src="{{ asset('public/assets/images/resource/process-2.png') }}" alt=""></div>
            <h4 class="mb-3">Set Priorities and Deadlines</h4>
            <p>Prioritize tasks to focus on what’s most critical. Establish deadlines and receive timely notifications as they approach. This tool helps you handle your responsibilities efficiently and reduces the chance of missing important deadlines.</p>
         </div>

         <!-- Process Block -->
         <div class="process-block col-lg-4 col-md-6 col-sm-12">
            <div class="icon-box"><img src="{{ asset('public/assets/images/resource/process-3.png') }}" alt=""></div>
            <h4 class="mb-3">Collaborate and Assign Tasks</h4>
            <p>Delegate tasks to yourself or colleagues, and track progress directly within the platform. Communicate clearly and effectively to ensure everyone is on the same page. This feature enhances teamwork and ensures accountability among travel nurses.</p>
         </div>
      </div>
   </div>
</section>

<!--
<div class="fun-fact-section style-two call-to-action-four mb-5">
   <div class="auto-container">
      <div class="row wow fadeInUp">
         <div class="counter-column col-lg-4 col-md-6 col-sm-12 wow fadeInUp mb-0">
            <div class="count-box"><span class="count-text" data-speed="3000" data-stop="4">0</span>M</div>
            <h4 class="counter-title">4 million daily active users</h4>
         </div>

         <div class="counter-column col-lg-4 col-md-6 col-sm-12 wow fadeInUp mb-0" data-wow-delay="400ms">
            <div class="count-box"><span class="count-text" data-speed="3000" data-stop="12">0</span>k</div>
            <h4 class="counter-title">Over 12k open job positions</h4>
         </div>

         <div class="counter-column col-lg-4 col-md-6 col-sm-12 wow fadeInUp mb-0" data-wow-delay="800ms">
            <div class="count-box"><span class="count-text" data-speed="3000" data-stop="2">0</span>k</div>
            <h4 class="counter-title">Over 2k job seekers hired</h4>
         </div>
      </div>
   </div>
</div>
 -->

<!--
<section class="about-section style-two">
   <div class="auto-container">
      <div class="row">
         <div class="content-column col-lg-6 col-md-12 col-sm-12 order-2">
            <div class="inner-column wow fadeInLeft">
               <div class="sec-title">
                  <h2>Watch Video</h2>
                  <div class="text">Discover how to maximize the benefits of our Task Management features by watching our brief tutorial video</div>
               </div>
               <ul class="list-style-two">
                  <li>Introduction to Task Management Features</li>
                  <li>Organizing and Managing Daily Tasks</li>
                  <li>Setting Priorities and Deadlines Effectively</li>
                  <li>Delegating Tasks and Collaborating Smoothly</li>
                  <li>Using Real-Time Notifications for Updates</li>
               </ul>
               <a href="https://www.youtube.com/watch?v=4UvS3k8D4rs" class="theme-btn btn-style-one lightbox-image">Watch Video</a>
            </div>
         </div>

         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInRight">
               <figure class="image">
                  <img src="{{ asset('public/assets/images/resource/image-5.png') }}" alt="">
                  <a href="https://www.youtube.com/watch?v=4UvS3k8D4rs" class="play-btn lightbox-image" data-fancybox="images"><span class="flaticon-play-button-2 icon"></span></a>
               </figure>
            </div>
         </div>
      </div>

   </div>
</section>
-->

@endsection
