<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Compliance Documentation</h1>
         <ul class="page-breadcrumb">
            <li>Ensure all required compliance files and documents are organized in a single location</li>
         </ul>
         @include('components.healthcareFacilitiesMenus', ['currentPage' => 'complianceFileMangement'])
      </div>
   </div>
</section>
<!--End Page Title-->



<!-- About Section -->
<section class="about-section">
   <div class="auto-container">
      <div class="row mb-5">
         <!-- Content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12 order-2">
            <div class="inner-column wow fadeInUp">
               <div class="sec-title">
                  <h2 class="color-style-1">Document Management</h2>
                  <div class="text mw-100">Easily upload, organize, and manage compliance documents with our user-friendly system. Keep track of certifications, licenses, and other critical paperwork.</div>
               </div>
               <ul class="list-style-one">
                  <li>Easy Document Upload</li>
                  <li>Organized Storage</li>
                  <li>Track Key Documents</li>
                  <li>Secure Management</li>
               </ul>
            </div>
         </div>

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12 d-flex align-items-center">
            <figure class="image wow fadeInLeft"><img src="{{ asset('public/assets/images/media/Post-26.jpg') }}" alt=""></figure>

         </div>
      </div>

      <div class="row mb-5">

         <!-- Content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInUp">
               <div class="sec-title">
                  <h2 class="color-style-1">Certification Tracking</h2>
                  <div class="text">Effortlessly manage employee certifications with our advanced tracking tools. Keep track of expiration dates, get renewal alerts, and maintain a complete record of employee credentials.</div>
               </div>
               <ul class="list-style-one">
                  <li>Monitor Expiration Dates</li>
                  <li>Receive Renewal Alerts</li>
                  <li>Maintain Comprehensive Records</li>
                  <li>Prevent Certification Lapses</li>
               </ul>
            </div>
         </div>

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12 d-flex align-items-center">
            <figure class="image wow fadeInLeft"><img src="{{ asset('public/assets/images/media/Post-27-01.jpg') }}" alt=""></figure>

         </div>

      </div>

      <div class="row mb-5">

         <div class="content-column col-lg-6 col-md-12 col-sm-12 order-2">
            <div class="inner-column wow fadeInUp">
               <div class="sec-title">
                  <h2 class="color-style-1">Compliance Alerts</h2>
                  <div class="text">Ensure you never miss a critical expiration deadline with our automated alert system. Receive notifications for key dates, such as license renewals, certification expirations, and required training updates. Stay informed and proactive with timely reminders to keep employee credentials up to date.</div>
               </div>
               <ul class="list-style-one">
                  <li>Automated Deadline Alerts</li>
                  <li>License Renewal Notifications</li>
                  <li>Certification Expiration Reminders</li>
               </ul>
            </div>
         </div>

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <figure class="image wow fadeInLeft"><img src="{{ asset('public/assets/images/media/Post-28-01.jpg') }}" alt=""></figure>

         </div>

      </div>
   </div>
</section>
<!-- End About Section -->



@endsection
