<?php

use App\Helper\CommonFunction;
use Carbon\Carbon;

?>

@extends('layouts.app')
@section('content')

<span class="header-span"></span>
<!-- Banner Section-->
<section class="banner-section secion-1 px-lg-0 ">
   <div class="full-container">
      <div class="row">
         <div class="content-column col-lg-12 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInUp py-0">
               <div class=" image-box position-relative">
                  <figure class=" main-image wow fadeIn mb-lg-0 mt-0">
                     <img src="{{ asset('public/assets/images/media/banner-1.jpg') }}" />
                  </figure>

                  <div class="btn-box text-center banner-profile-btn">
                     <a href="{{ config('custom.register_url') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Create Your Profile</span></a>
                  </div>
               </div>
            </div>
         </div>

      </div>
   </div>
</section>
<!-- End Banner Section-->

<!-- Job Section -->
@if(isset($jobs) && !empty($jobs))
<section class="job-categories secion-7">
   <div class="auto-container">
      <div class="sec-title text-center">
         <h2 class="color-style-1">Featured Jobs</h2>
         <h6 class="text-muted mt-2">Embrace your value by seeking jobs that align with your desires</h6>
      </div>

      <div class="row wow fadeInUp">
         <?php foreach ($jobs as $row) { ?>
            <div class="job-block col-lg-6 col-md-12 col-sm-12">
               <div class="inner-box bg-light h-100">
                  <div class="content pl-0">
                     <?php if (isset($row->profile_pic_path) && !empty($row->profile_pic_path) && 0) { ?>
                        <span class="company-logo">
                           <img src="{{$row->profile_pic_path}}" />
                        </span>
                     <?php } ?>
                     <?php
                     if (isset($row->compnay_role_id) && $row->compnay_role_id == 1) {
                     ?>
                        <?php
                        $company_name = DB::table('app_settings')
                           ->select('field_value')
                           ->where(['field_name' => 'app_name'])
                           ->where('field_value', '!=', NULL)
                           ->first()
                        ?>
                        <?php if (isset($company_name) && !empty($company_name)) { ?>
                           <span class="company-name">{{ $company_name->field_value }}</span>
                        <?php } ?>

                     <?php
                     } else if (isset($row->company_name) && !empty($row->company_name)) {
                     ?>
                        <span class="company-name">{{ $row->company_name }}</span>
                     <?php
                     } ?>
                     <h4 class="text-capitalize"><a href="{{ route('job',$row->unique_id) }}">{{ $row->title }}</a></h4>
                     <ul class="job-info">
                        <li><span class="icon flaticon-map-locator"></span> {{ $row->city_name}}, {{ $row->state_name}}</li>

                        <?php
                        $givenTime = Carbon::parse($row->created_at);
                        $currentTime = Carbon::now();
                        $timeDifference = $givenTime->diffForHumans($currentTime);
                        ?>
                        <li><span class="icon flaticon-clock-3"></span> {{ $timeDifference }}</li>

                        <?php if (isset($row->salary_start_range) && !empty($row->salary_start_range)) { ?>
                           <li><span class="icon flaticon-money"></span> ${{ $row->salary_start_range }} {{ $row->salary_type }}</li>
                        <?php } ?>
                     </ul>
                     <ul class="job-other-info">
                        <?php if (isset($row->total_opening) && $row->total_opening > 0) { ?>
                           <li class="time">{{ $row->total_opening }} Opening</li>
                        <?php } ?>
                        <li class="privacy">{{ $row->profession }}</li>
                        <li class="required">{{ $row->specialty }}</li>
                        <?php if (isset($row->shift_title) && !empty($row->shift_title)) { ?>
                           <li class="bg-info text-white">{{ $row->shift_title }} Shift</li>
                        <?php } ?>
                     </ul>
                     <!--<button class="bookmark-btn"><span class="flaticon-bookmark"></span></button>-->
                  </div>
               </div>
            </div>
         <?php } ?>
      </div>

      <div class="btn-box text-center">
         <a href="{{ route('jobs-search') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Load More Opening</span></a>
      </div>
   </div>
</section>
@endif
<!-- End Job Section -->

<!-- Job Categories -->
@if(isset($professions) && !empty($professions) && 0)
<section class="job-section-five secion-8">
   <div class="auto-container">
      <div class="sec-title text-center">
         <h2>Travel Nurse 911, the Preferred Choice for Top Nursing Talent</h2>
         <h6 class="text-muted mt-2">Join our travel nurse community and discover the leading job board for travel nurses</h6>
      </div>

      <div class="row wow fadeInUp">
         <?php foreach ($professions as $row) { ?>
            <div class="job-block col-lg-3 col-md-6 col-sm-12">
               <a href="{{ route('job-category', ['pid' => $row->id]) }}" class="h-100 d-inline-block w-100">
                  <div class="inner-box h-100 d-flex align-items-center justify-content-center">
                     <div class="content pl-0 text-center">
                        <h4>{{ $row->profession }}</h4>
                        <p>[ {{ $row->job_count}} job(s) ]</p>
                     </div>
                  </div>
               </a>
            </div>
         <?php } ?>
      </div>

      <div class="btn-box text-center mt-4">
         <a href="{{ route('job-categories') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Load More Categories</span></a>
      </div>
   </div>
</section>
@endif
<!-- End Job Categories -->

<!-- Call To Action -->
<section class="registeration-bannerss job-categories secion-10" style="background-color:#F0F5F7;">
   <div class="auto-container">
      @include('components.call-to-actions.callToActionBanners')
   </div>
</section>
<!-- End Call To Action -->

<!-- Prioritize Section -->
<section class="about-section pb-5">
   <div class="auto-container">
      <div class="row">
         <!-- Content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12 order-2">
            <div class="inner-column wow fadeInRight pt-0">
               <div class="sec-title">
                  <h3 class="color-style-1">What makes Travel Nurse 911 Unique?</h3>
                  <div class="text mw-100">
                     Discover a world of opportunities with our extensive travel nursing job listings. Travel Nurse 911 is the first job board created just for travel nurses. At Travel Nurse 911, our platform caters to travel nurses, travel nurse agencies, and hospitals that have created their own Internal Travel Nurse Program.
                  </div>
                  <div class="text mw-100">
                     Travel Nurse 911 was created to support healthcare facilities with internal travel nurse programs and assist with communication between travel nurses before and after assignments. In addition to assisting these programs, Travel Nurse 911 partners with reputable travel agencies known for excellent services and putting travelers to work as quickly as possible. Together, we have the Travel Nurse 911 community, everyone working together to address the immediate staffing needs of our healthcare communities.

                  </div>
               </div>

            </div>
         </div>
 
         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12 d-flex align-items-center">
            <figure class="image-box wow fadeInLeft"><img src="{{ asset('public/assets/images/media/Post-1.jpg') }}" /></figure>
         </div>


      </div>

      <div class="row wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">
         <!-- Category Block -->
         <div class="category-block-two col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <h4 class="mb-3 color-style-3">One-Click Application</h4>
                  <p>When you find a job, you click apply, and your resume is immediately sent to the Recruiter. You can easily upload your resume in seconds and start applying for jobs immediately, streamlining the application process. Unlike other job boards that requires you to complete an application for every job you apply to, Travel Nurse 911 only requires that you have one application on file to be consider for as many job opportunities you can apply for. </p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <h4 class="mb-3 color-style-3">Rapid Response Hiring </h4>
                  <p><strong>Need a travel nurse job now?</strong> Travel Nurse 911 makes the perfect match, connecting travel nurses with travel nurse agencies and healthcare facilities with an internal travel nurse program. Applicants can get matched with jobs tailored to their skills, desires, and preferences. At the same time, the travel agency or healthcare facilities can benefit from your shared documents uploaded directly to your Document Safe to expedite onboarding.</p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <h4 class="mb-3 color-style-3">Exclusive to Travel Nurses</h4>
                  <p>Travel Nurse 911 is and will always be exclusive to Registered Nurses, Licensed Practical or Vocational Nurses, and Certified Nursing Assistants who are looking for their next travel assignment. Whether working directly with the hospital within their internal travel program or with a travel agency, Travel Nurse 911 has the next opportunity for you. </p>
               </div>
            </div>
         </div>

         <!-- Category Block -->
         <div class="category-block-two col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box h-100">
               <div class="content">
                  <h4 class="mb-3 color-style-3">Customer Service at its Finest! </h4>
                  <p><strong>24/7 Support Access</strong> If you need help connecting with a Recruiter or have questions regarding your submitted application, unlike other job boards, Travel Nurse 911 has Round-the-clock support to assist you with your job search and application process, ensuring you have help whenever needed. You can reach us 24 hours a day by calling <a href="tell:{{ config('custom.phone') }}">{{ config('custom.phone') }}</a>. Or using the chat function on the website. </p>
               </div>
            </div>
         </div>
      </div>

      <div class="text-center">
         <a href="{{ route('jobs-search') }}" class="theme-btn btn-style-one">Explore Jobs</a>
      </div>
   </div>
</section>
<!-- End Prioritize Section -->





<!-- /section -->
@endsection