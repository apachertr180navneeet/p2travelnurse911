<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Integrated Travel Nurse File Management
         </h1>
         <ul class="page-breadcrumb">
            <li>Effectively Monitor Progress, Profiles and Traveler Credentials</li>
         </ul>
         @include('components.healthcareFacilitiesMenus', ['currentPage' => 'travelNurseManagement'])
      </div>
   </div>
</section>
<!--End Page Title-->


<!-- Job Categories -->
<section class="job-categories border-bottom-0">
   <div class="small-container">
      <div class="sec-title text-center">
         <h4>Streamline every stage of your travel nurses’ journey with our comprehensive Travel Nurse Management system. From the initial application to final placement, our system ensures a smooth and efficient process. Enhance your recruitment efforts and maintain a steady pipeline of qualified travel nurses with ease.</h4>
      </div>

      <div class="row wow fadeInUp justify-content-center">
         <!-- Category Block -->
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-money-1"></span>
                  <h4 class="mb-3">Travel Nurse Profiles</h4>
                  <p class="mb-0">Manage detailed profiles for each travel nurse, including personal details, professional experience, skills, and certifications. Easily update and maintain this information to ensure accuracy and completeness.</p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-promotion"></span>
                  <h4 class="mb-3">Comprehensive Details</h4>
                  <p class="mb-0">Capture and store essential information such as contact details, employment history, and educational background for every travel nurse candidate.</p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-vector"></span>
                  <h4 class="mb-3">Skills & Certifications</h4>
                  <p class="mb-0">Track and verify skills, certifications, and licenses to ensure job seekers meet all job requirements and compliance standards.</p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-web-programming"></span>
                  <h4 class="mb-3">Application Progress Tracking</h4>
                  <p class="mb-0">Follow the progress of applications from submission to hiring with real-time updates, simplifying the review and decision-making process.</p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-rocket-ship"></span>
                  <h4 class="mb-3">Application Status Management</h4>
                  <p class="mb-0">Monitor and manage the status of each application, guiding travel nurses smoothly through the hiring pipeline.</p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-money-1"></span>
                  <h4 class="mb-3">Manage Active Submissions</h4>
                  <p class="mb-0">Monitor and manage the status of each submission that is made through the site. You can also add submissions that are not made through the site to ensure all submissions are followed up with accordingly. </p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-promotion"></span>
                  <h4 class="mb-3">Manage Active Assignments</h4>
                  <p class="mb-0">Monitor and manage all travelers placed with the site. Manage active assignments directly from your employer dashboard. </p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-support-1"></span>
                  <h4 class="mb-3">Communication Log</h4>
                  <p class="mb-0">Keep a detailed record of all interactions with candidates, including emails, calls, and interview notes, ensuring clear and organized communication.</p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-first-aid-kit-1"></span>
                  <h4 class="mb-3">Follow Up Coordination</h4>
                  <p class="mb-0">Efficiently schedule and manage follow ups, providing a seamless experience for both recruiters and your travel nurse candidates.</p>
               </div>
            </div>
         </div>

         <!--  
         <div class="category-block-two col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <span class="icon flaticon-4-square-shapes"></span>
                  <h4 class="mb-3">Interview Preparation Support</h4>
                  <p class="mb-0">Provide job seekers with helpful resources and tips to prepare for interviews, boosting their chances of success.</p>
               </div>
            </div>
         </div>
         -->

      </div>
   </div>
</section>
<!-- End Job Categories -->

@endsection