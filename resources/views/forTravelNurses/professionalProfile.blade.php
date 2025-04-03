<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Nurse Profile Management</h1>
         <ul class="page-breadcrumb">
            <li>Develop and maintain a detailed professional profile to make it easier for employers to select you for their openings. </li>
         </ul>

         @include('components.travelNurseMenus', ['currentPage' => 'professionalProfile'])
      </div>
   </div>
</section>
<!--End Page Title-->



<!-- Work Section -->
<section class="work-section style-one">
   <div class="auto-container">
      <div class="sec-title text-center">
         <h5>Build and manage a comprehensive professional profile that highlights your skills, experience, and certifications. Our platform helps travel nurses showcase their qualifications, making it easier for potential employers to discover and connect with you.</h5>
      </div>

      <div class="row">
         <!-- Work Block -->
         <div class="work-block col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box border h-100 d-flex flex-column">
               <figure class="image"><img src="{{ asset('public/assets/images/resource/work-2.png') }}" alt="Professional Summary"></figure>
               <h5 class="color-style-3">Professional Summary</h5>
               <p class="mb-5">Offer a concise summary of your professional journey, including work experience, education, and certifications. This section acts as your digital resume, providing employers with a quick insight into your qualifications.</p>

               <div class="mt-auto">
                  <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Create or Update Profile</span></a>
               </div>
            </div>
         </div>

         <!-- Work Block -->
         <div class="work-block col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box border h-100 d-flex flex-column">
               <figure class="image"><img src="{{ asset('public/assets/images/resource/work-1.png') }}" alt="Upload Your Resume"></figure>
               <h5 class="color-style-3">Upload Your Resume</h5>
               <p class="mb-5">Add your resume to your profile for easy access by potential employers. Keep it up to date with your latest experiences and skills, ensuring you always present the best version of yourself.</p>

               <div class="mt-auto">
                  <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Upload or Update Resume</span></a>
               </div>
            </div>
         </div>

         <!-- Work Block -->
         <div class="work-block col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box border h-100 d-flex flex-column">
               <figure class="image"><img src="{{ asset('public/assets/images/resource/work-3.png') }}" alt="Key Skills and Competencies"></figure>
               <h5 class="color-style-3">Key Skills and Competencies</h5>
               <p class="mb-5">Highlight your key skills and competencies to help employers recognize your strengths. Utilize our comprehensive skill checklists to assess and showcase your abilities, making it easier for employers to match you with suitable job opportunities.</p>

               <div class="mt-auto">
                  <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Update Skills</span></a>
               </div>
            </div>
         </div>

      </div>
   </div>
</section>
<!-- End Work Section -->

<section class="call-to-action-three bg-style-3">
   <div class="auto-container">
      <div class="outer-box">
         <div class="sec-title">
            <h2 class="text-white">Join Our Network of Travel Nurses</h2>
            <div class="text text-white" style="color:#fff !important;">Upload your resume to become part of our community. Explore diverse opportunities, enjoy flexible schedules, and make a difference in healthcare.</div>
         </div>

         <div class="btn-box">
            <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Create a Profile</span></a>
         </div>
      </div>
   </div>
</section>

<!-- Call To Action -->
<section class="registeration-bannerss job-categories secion-10 border-bottom-0">
   <div class="auto-container">
      @include('components.call-to-actions.callToActionBanners')
   </div>
</section>
<!-- End Call To Action -->

@endsection