<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Travel Nurse Benefits</h1>
         <ul class="page-breadcrumb">
            <li>Discover the comprehensive benefits and advantages that make travel nursing a rewarding career choice.</li>
         </ul>
         @include('components.travelNurseMenus', ['currentPage' => 'travelNurseBenefits'])

      </div>
   </div>
</section>
<!--End Page Title-->

<section class="about-section secion-3">
   <div class="small-container">
      <div class="row wow fadeInUp">

         <!-- Image Column -->
         <div class="image-column col-lg-12 col-md-12 col-sm-12 d-flex align-items-center m-0">
            <figure class="image"><img src="{{ asset('public/assets/images/media/Post-25-01.jpg') }}" alt=""></figure>
         </div>

      </div>
   </div>
</section>


<!-- Job Section -->
<section class="job-section">
   <div class="auto-container">
      <div class="sec-title-outer justify-content-center">
         <div class="sec-title ">
            <h2 class="color-style-1">Key Features and Benefits</h2>
         </div>
      </div>

      <div class="row wow fadeInUp">

         <div class="job-block col-lg-12 col-md-12 col-sm-12">
            <div class="inner-box h-100 bg-light">
               <div class="content pl-0">
                  <h4>1. <span class="color-style-3">Exclusive Job Board for Travel Nurses</span></h4>
                  <ul class="list-style-one mt-3 row">
                     <div class="col-lg-6 col-md-12 col-sm-12">
                        <li><span><strong class="color-style-2">Easy Job Application</strong> : One-click applications for multiple positions.</span></li>
                        <li><span><strong class="color-style-2">Wide Range of Positions</strong> : From short-term contracts to long-term assignments across the country.</span></li>
                     </div>
                     <div class="col-lg-6 col-md-12 col-sm-12">
                        <li><span><strong class="color-style-2">Tailored Opportunities</strong> : 100% dedicated to RNs, LPNs, and CNAs seeking travel assignments.
                           </span></li>

                        <li class="mb-0"><span><strong class="color-style-2">Personalized Job Matches</strong> : Our algorithm highlights job opportunities that fit your skills and preferences.
                           </span></li>
                     </div>
                  </ul>
               </div>
            </div>
         </div>

         <div class="job-block col-lg-6 col-md-12 col-sm-12">
            <div class="inner-box h-100 bg-light">
               <div class="content pl-0">
                  <h4>2. <span class="color-style-3">Financial Benefits</span></h4>
                  <ul class="list-style-one mt-3">
                     <li><span><strong class="color-style-2">Competitive Pay Rates</strong> : Find jobs that offer competitive compensation and benefits.</span></li>
                     <li><span><strong class="color-style-2">Bonus Programs</strong> : Access to sign-on bonuses, referral bonuses, and completion bonuses.</span></li>
                  </ul>
               </div>
            </div>
         </div>

         <div class="job-block col-lg-6 col-md-12 col-sm-12">
            <div class="inner-box h-100 bg-light">
               <div class="content pl-0">
                  <h4>3. <span class="color-style-3">Seamless Communication Tools</span></h4>
                  <ul class="list-style-one mt-3">
                     <li><span><strong class="color-style-2">Follow Up & Messaging System</strong> : Ensures direct and efficient communication between travelers and recruiters.</span></li>
                     <li><span><strong class="color-style-2">Real-Time Updates</strong> : Receive instant notifications on job applications, interview requests, and recruiter messages.</span></li>
                  </ul>
               </div>
            </div>
         </div>

         <div class="job-block col-lg-6 col-md-12 col-sm-12">
            <div class="inner-box h-100 bg-light">
               <div class="content pl-0">
                  <h4>4. <span class="color-style-3">Document Management</span></h4>
                  <ul class="list-style-one mt-3">
                     <li><span><strong class="color-style-2">Document Safe</strong> : Securely store and share essential documents like licenses, certifications, and resumes.</span></li>
                     <li><span><strong class="color-style-2">Expedited Onboarding</strong> : Quick and easy access to your documents speeds up the hiring process.</span></li>
                  </ul>
               </div>
            </div>
         </div>

         <div class="job-block col-lg-6 col-md-12 col-sm-12">
            <div class="inner-box h-100 bg-light">
               <div class="content pl-0">
                  <h4>5. <span class="color-style-3">24/7 Support</span></h4>
                  <ul class="list-style-one mt-3">
                     <li><span><strong class="color-style-2">Round-the-Clock Assistance</strong> : Our dedicated support team is available any time to help with any issues or inquiries.</span></li>
                     <li><span><strong class="color-style-2">Comprehensive Help Center</strong> : Access to FAQs, tutorials, and guides to assist with common questions and processes.</span></li>
                  </ul>
               </div>
            </div>
         </div>

      </div>
   </div>
</section>
<!-- End Job Section -->


<!-- Prioritize Section -->
<section class="about-section-two secion-3 pb-5">
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
         <div class="image-column col-lg-6 col-md-12 col-sm-12">
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
                  <p><strong>24/7 Support Access</strong> If you need help connecting with a Recruiter or have questions regarding your submitted application, unlike other job boards, Travel Nurse 911 has Round-the-clock support to assist you with your job search and application process, ensuring you have help whenever needed. You can reach us 24 hours a day by calling ……………………. Or using the chat function on the website. </p>
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

@endsection