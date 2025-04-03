<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Compliance Documentation Management</h1>
         <ul class="page-breadcrumb">
            <li>Ensure all required compliance files and documents are organized in a single location.</li>
         </ul>
         @include('components.travelNurseMenus', ['currentPage' => 'documentSafe'])
      </div>
   </div>
</section>
<!--End Page Title-->


<!-- Job Section -->
<section class="job-section">
   <div class="small-container">
      <div class="image-column">
         <figure class="image"><img class="border" src="{{ asset('public/assets/images/media/Post_32-01.jpg') }}" alt=""></figure>
      </div>
   </div>

</section>

<section class="testimonial-section">
   <div class="auto-container">
      <div class="row wow fadeInUp">

         <div class="col-lg-6 col-md-12 col-sm-12 mb-5">
            <div class="testimonial-block m-0 p-0 h-100">
               <div class="inner-box h-100">
                  <h4 class="title">Document Safe</h4>
                  <div class="text mb-3">Our document safe allows you to securely store your documents so that you are prepared to submit them to Recruiters when you accept a position. This speeds up the onboarding time and allows you to start assignments sooner than later. </div>
                  <ul class="list-style-one">
                     <li>
                        <strong class="color-style-3">Documents</strong> - Resume, References, Skills Checklists
                     </li>
                     <li>
                        <strong class="color-style-3">Certifications</strong> - CPR, ACLS, TNCC, NRP, PALS,
                     </li>
                     <li>
                        <strong class="color-style-3">Health File</strong> - Physical, PPD, Immunizations, Vaccinations
                     </li>
                  </ul>

                  <p class="text mb-0">
                     Keep all of this information secure and ready to send when needed in our document safe. These documents can be sent directly to Recruiters and Compliance Specialists when you are ready to onboard by logging in to your account and going to shared documents. <a href="{{ config('custom.user_documents_url') }}" class="font-weight-bold">Try it now!</a>

                  </p>

                  <div class="btn-box mt-3">
                     <a href="{{ config('custom.user_documents_url') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Upload Documents</span></a>
                  </div>
               </div>
            </div>
         </div>

         <div class="col-lg-6 col-md-12 col-sm-12 mb-5 ">
            <div class="testimonial-block m-0 p-0 h-100">
               <div class="inner-box h-100">
                  <h4 class="title">Document Management</h4>
                  <div class="text mb-3">Easily upload, organize, and manage compliance documents with our user-friendly system. Keep track of certifications, licenses, and other critical paperwork, ensuring that all your documents are accessible whenever needed. </div>
                  <ul class="list-style-one">
                     <li>Easy Document Upload</li>
                     <li>Organized Storage</li>
                     <li>Track Key Documents</li>
                     <li>Secure Management</li>
                  </ul>

               </div>
            </div>
         </div>

         <div class="col-lg-6 col-md-12 col-sm-12 mb-5">
            <div class="testimonial-block m-0 p-0 h-100">
               <div class="inner-box h-100">
                  <h4 class="title">Certification Tracking</h4>
                  <div class="text mb-3">Effortlessly manage your certifications with our advanced tracking tools. Keep track of expiration dates, get renewal alerts, and maintain a complete record of your credentials. Prevent certification lapses and stay ready for new opportunities. </div>
                  <ul class="list-style-one">
                     <li>Monitor Expiration Dates</li>
                     <li>Receive Renewal Alerts</li>
                     <li>Maintain Comprehensive Records</li>
                     <li>Prevent Certification Lapses</li>
                     <li>Stay Prepared for Assignments</li>
                  </ul>

               </div>
            </div>
         </div>

         <div class="col-lg-6 col-md-12 col-sm-12 mb-5">
            <div class="testimonial-block m-0 p-0 h-100">
               <div class="inner-box h-100">
                  <h4 class="title">Compliance Alerts</h4>
                  <div class="text mb-3">Ensure you never miss a critical compliance deadline with our automated alert system. Receive notifications for key dates, such as license renewals, certification expirations, and required training updates. Stay informed and proactive with timely reminders to keep your credentials up to date. </div>
                  <ul class="list-style-one">
                     <li>Automated Deadline Alerts</li>
                     <li>License Renewal Notifications</li>
                     <li>Certification Expiration Reminders</li>
                     <li>Training Update Alerts</li>
                     <li>Proactive Compliance Management</li>
                  </ul>

               </div>
            </div>
         </div>
      </div>


   </div>
</section>
<!-- End Job Section -->

<section class="job-section pb-0"></section>

@endsection