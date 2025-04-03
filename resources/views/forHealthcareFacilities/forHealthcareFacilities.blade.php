<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')



<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1 class="mb-4">Connect with Active Travel Nurses Nationwide</h1>

         @include('components.healthcareFacilitiesMenus', ['currentPage' => 'default'])


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
               <img src="{{ asset('public/assets/images/media/Post-19-01.png')}}" alt="Nursing Job Postings">
            </figure>
         </div>

         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInRight">
               <div class="sec-title">
                  <h3 class="mb-5 color-style-1">The fastest growing job site supporting Healthcare Facilities with their own internal travel nurse program.</h3>

                  <a href="{{ route('pilot-partner-signup') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Start Recruiting Now</span></a>

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
      
      
      <?php /* 
      <div class="sec-title text-center">
         <h2 class="mb-2 color-style-1">Streamline Your Staffing Needs</h2>
         <h6 class="text-muted mt-2">Partner with Top Healthcare Talent</h6>
      </div>
      */ ?>

      <div class="cards">

         <div class="news-block-two mb-4 pt-0 " data-index="0">
            <a href="{{ route('facility-job-posting') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h2 class="mb-3 font-weight-bold">Free Job Postings </h2>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Streamline your staffing needs with our free job posting service. Create job postings that attract top healthcare talent and reduce the administrative burden on your team.
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
            <a href="{{ route('applicant-tracking-system') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h2 class="mb-3 font-weight-bold">Applicant Tracking System</h2>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Effortlessly manage your candidate pipeline with our intuitive applicant tracking system. Track applications, schedule interviews, and make informed hiring decisions with ease.
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
            <a href="{{ route('facility-travel-nurse-management') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h2 class="mb-3 font-weight-bold">Travel Nurse Management</h2>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Simplify your travel nurse management process with our platform. Manage assignments, track travel details, and streamline communication with ease.
                     </h5>
                  </div>
                  <div class="image-box p-0">
                     <figure class="image">
                        <img src="{{ asset('public/assets/images/media/Post-21-01.jpg') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

         <div class="news-block-two mb-4 pt-0  " data-index="0">
            <a href="{{ route('facility-compliance-files') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h2 class="mb-3 font-weight-bold">Compliance File Management</h2>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Stay compliant with our automated compliance file management system. Easily store and manage required documents, and ensure regulatory compliance with ease.
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
<section class="steps-section pb-lg-0">
   <div class="auto-container">
      <div class="row wow fadeInUp">

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12 ">
            <figure class="image"><img src="{{ asset('public/assets/images/media/Post-19-01.jpg') }}" /></figure>
         </div>
         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column p-0">
               <div class="sec-title">
                  <h2 class="color-style-1">How it Works</h2>
                  <p class="text">Learn how our platform simplifies the process of finding and securing your next healthcare staffing solution, from job posting to onboarding and beyond.</p>
               </div>

               <ul class="steps-list mt-4">

                  <li class="mb-3"><span class="count">1</span><span>Post Job Opportunities</span>
                     <p class="mt-2">Easily create and manage job postings for your healthcare facility, including travel nurse positions, PRN staff, and permanent hires. Customize your job postings with detailed requirements and filters to attract top talent.</p>
                  </li>

                  <li class="mb-3"><span class="count">2</span><span>Applicant Tracking</span>
                     <p class="mt-2">Manage your job applications with ease, track candidate progress, and communicate directly with applicants through our intuitive applicant tracking system. Say goodbye to lost paperwork and hello to efficient hiring.</p>
                  </li>

                  <li class="mb-3"><span class="count">3</span><span>Compliance and Onboarding</span>
                     <p class="mt-2">Streamline your compliance and onboarding processes with our platform. Automate tasks such as document management, background checks, and credentialing to ensure a smooth transition for your new staff members.</p>
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