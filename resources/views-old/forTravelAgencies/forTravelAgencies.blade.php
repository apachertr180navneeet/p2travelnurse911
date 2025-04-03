<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')



<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Partner with Leading Travel Agencies</h1>
         <ul class="page-breadcrumb">
            <li>Expand Your Reach with Trusted Agency Collaborations</li>
         </ul>

         @include('components.travelAgenciesMenus', ['currentPage' => 'default'])

      </div>
   </div>
</section>
<!--End Page Title-->


<section class="app-section p-0">
   <div class="auto-container">
      <div class="row">
         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12">

            <figure class="image wow fadeInLeft">
               <img src="{{ asset('public/assets/images/media/Post-13-01.jpg')}}" alt="">
            </figure>
         </div>

         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInRight">
               <div class="sec-title">
                  <h3 class="mb-5 color-style-1">The fastest growing job site supporting Nurse Travel Agencies, making it easier to Recruit and Connect with Travel Nurses.</h3>

                  <a href="{{ config('custom.client_login_url') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Start Recruiting Now</span></a>

               </div>

            </div>
         </div>
      </div>
   </div>
</section>



<script>
   document.addEventListener("DOMContentLoaded", function() {
      const cards = document.querySelectorAll('.news-section .cards .news-block-two');
      const colorCombinations = ['card-combo-6', 'card-combo-5', 'card-combo-1', 'card-combo-4', 'card-combo-3', 'card-combo-2'];

      cards.forEach((card, index) => {
         const comboClass = colorCombinations[index % colorCombinations.length];
         card.classList.add(comboClass);
      });
   });
</script>

<section class="news-section secion-1 aat-card-container  mb-5" style="background-color:#F0F5F7;">
   <div class="container-fluid">
      <div class="sec-title text-center">
         <h2 class="mb-2 color-style-1">For Healthcare Facilities</h2>
         <h6 class="text-muted mt-2">Streamline Your Hiring Process</h6>
      </div>

      <div class="cards">

         <div class="news-block-two mb-4 pt-0 " data-index="0">
            <a href="{{ route('agency-job-posting') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Free Job Postings</h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Streamline your hiring process with our free job posting service, allowing you to easily advertise your open positions to attract top talent in the healthcare industry.
                     </h5>
                  </div>
                  <div class="image-box p-5 order-2 ">
                     <figure class="image justify-content-center align-items-center">
                        <img src="{{ asset('public/assets/images/media/Post-14-01.jpg') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

         <div class="news-block-two mb-4 pt-0  " data-index="0">
            <a href="{{ route('agency-applicant-tracking-system') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Applicant Tracking System</h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Manage your job applications with our intuitive Applicant Tracking System, providing a centralized platform to track and organize candidate submissions, resumes, and cover letters.
                     </h5>
                  </div>
                  <div class="image-box p-5">
                     <figure class="image">
                        <img src="{{ asset('public/assets/images/media/Post-8-01.jpg') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

         <div class="news-block-two mb-4 pt-0  " data-index="0">
            <a href="{{ route('agency-submission-files') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Submission Files</h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Effortlessly manage and submit essential candidate documents, ensuring compliance and streamlined hiring for your healthcare facility.
                     </h5>
                  </div>
                  <div class="image-box p-5">
                     <figure class="image">
                        <img src="{{ asset('public/assets/images/media/Post-17-01.jpg') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

         <div class="news-block-two mb-4 pt-0  " data-index="0">
            <a href="{{ route('agency-travel-nurse-management') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Travel Nurse Management</h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Simplify your travel nurse management with our dedicated platform, offering features such as scheduling, payroll, and compliance tracking to ensure seamless coordination and reduced administrative burdens.
                     </h5>
                  </div>
                  <div class="image-box p-5">
                     <figure class="image">
                        <img src="{{ asset('public/assets/images/resource/banner-img-3.png') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

         <div class="news-block-two mb-4 pt-0  " data-index="0">
            <a href="{{ route('agency-compliance-files') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Compliance File Management</h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Stay compliant with our comprehensive file management system, designed to securely store and manage important documents, certifications, and licenses for your healthcare staff.
                     </h5>
                  </div>
                  <div class="image-box p-5 order-2">
                     <figure class="image">
                        <img src="{{ asset('public/assets/images/media/Post-15-01.jpg') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

      </div>
   </div>
</section>




<!-- Job Section -->
<section class="steps-section p-0">
   <div class="auto-container">
      <div class="row wow fadeInUp">

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12 d-flex align-items-center">
            <figure class="image"><img src="{{ asset('public/assets/images/media/Post-18-01.jpg') }}" /></figure>
         </div>
         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column p-0">
               <div class="sec-title">
                  <h2 class="color-style-1">How it Works</h2>
                  <p class="text">Learn how our platform simplifies the process of finding and managing your next travel nurse assignment, from job posting to placement and beyond.</p>
               </div>

               <ul class="steps-list mt-4">

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


      </div>
   </div>
</section>
<!-- End Job Section -->

<!-- Call To Action -->
<section class="registeration-bannerss job-categories secion-10 border-bottom-0">
   <div class="auto-container">
      @include('components.call-to-actions.callToActionBanners')
   </div>
</section>
<!-- End Call To Action -->

@endsection