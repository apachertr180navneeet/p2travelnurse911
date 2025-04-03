<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Efficient Staffing Solutions</h1>
         <ul class="page-breadcrumb">
            <li>Streamline your hiring process and connect with the best travel nursing professionals to meet your staffing demands.</li>
         </ul>
      </div>
   </div>
</section>
<!--End Page Title-->

<section class="call-to-action-two style-two " style="background-image: url({{ asset('public/assets/images/background/4.jpg')}});">
   <div class="auto-container wow fadeInUp">
      <div class="sec-title light text-center">
         <h2 class="mb-3">Ready to discover your next great hire?</h2>
         <h3 class=" text-white">Travel Nurse 911 empowers recruiters to connect swiftly and efficiently with motivated job seekers. We’ll assist you in reducing time-to-hire and reaching your recruitment objectives.</h3>
      </div>

      <div class="btn-box">
         <a href="{{ route('for-travel-agencies') }}" class="theme-btn btn-style-three">Travel Agencies</a>
         <a href="{{ route('for-healthcare-facilities') }}" class="theme-btn btn-style-three">Healthcare Facilities</a>
      </div>
   </div>
</section>

<section class="app-section">

   <div class="auto-container">

      <div class="row">

         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <figure class="image"><img src="{{ asset('public/assets/images/media/Post-11.jpg')}}" alt="Connect with Top Travel Nursing Talent"></figure>
         </div>

         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column pt-lg-3 pb-0">
               <div class="sec-title wow fadeInUp">
                  <h3 class="color-style-1">Connect with Top <span class="colored">Travel Nursing Talent</span></h3>

                  <div class="text">As an employer, finding the right travel nurses to meet your staffing needs is crucial. Our platform is designed to simplify the hiring process, offering you access to a vast network of skilled and experienced travel nurses. Whether you need short-term coverage or long-term placements, our tools help you create compelling job postings, manage applications, and communicate seamlessly with potential candidates.</div>

                  <div class="text">Our employer dashboard provides a centralized hub where you can track your job postings, review applicant information, and ensure compliance with healthcare standards. With detailed analytics and robust management tools, you can make informed hiring decisions that align with your organization's goals. Start connecting with the best travel nursing professionals today and ensure your facility is always staffed with top-tier talent.</div>
               </div>


            </div>

         </div>
      </div>
</section>



<!-- Candidate to Employee Section -->
<section class="blog-section secion-4 py-5 my-5">
   <div class="auto-container">

      <div class="sec-title text-center">
         <h4 class="mb-3" style="color:#ff5712;">How We Benefit Employers</h4>
         <h2>Effortlessly discover top Travel Nursing talent with a job board that connects employers with Active Travel Nurses who are ready to travel! </h2>
      </div>

      <div class="row">
         <!-- Content Column -->
         <div class="col-lg-6 col-md-12 col-sm-12 order-2">
            <div class="inner-column">
               <figure class="image-box wow fadeInLeft">
                  <img src="{{ asset('public/assets/images/media/Post-12.jpg') }}" alt="Effortlessly discover top Travel Nursing talent"/>
               </figure>
            </div>
         </div>



         <!-- Image Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12 wow fadeInRight">


            <div class="text mw-100 mt-0 mb-3">
               Streamline Your Search for Highly Qualified Travel Nurses within our Platform, Ensuring You Find the Best Professionals Quickly and Easily
            </div>
            <ul class="list-style-one">
               <li class="mb-1"><strong>Fast Access to Qualified Nurses
                  </strong> : Quickly connect with a pool of skilled travel nurses ready to step into roles and immediately impact your recruitment goals filling your most urgent openings.
               </li>

               <li class="mb-1"><strong>Streamlined Hiring Process</strong> : Simplify your recruitment with our user-friendly platform, which minimizes administrative tasks and accelerates candidate selection.Manage applicants in our pipeline especially designed for travel nurses. Our Applicant Management System manages and notifies applicants of their application status that includes when they are being short-listed, offered and hired for the position. </li>

               <li class="mb-1"><strong>Advanced Candidate Filtering
                  </strong> : Efficiently identify top candidates using our advanced search and filtering tools, designed to enhance and accelerate your recruitment process. Browse candidates based on their desired locations increasing candidate engagement and decreasing offer decline rates.
               </li>

               <li class="mb-1"><strong>Expedited Onboarding
                  </strong> : Our Document safe encourages and allows travel nurses to upload their documents directly to their profile and share with employers to expedite the onboarding process. Set up your compliance list to request specific documents to ensure all compliance requirements are met.

               </li>
            </ul>
            <!--
            <div class="mt-2 text-center">
               <a href="{{ route('contact-us') }}" class="theme-btn btn-style-one">Hire Now</a>
            </div>
            -->
         </div>
      </div>
   </div>
</section>
<!-- End Candidate to Employee Section -->


@endsection