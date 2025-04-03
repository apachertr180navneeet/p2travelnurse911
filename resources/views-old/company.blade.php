<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Travel Nurse 911</h1>
         <ul class="page-breadcrumb">
            <li>One platform for all your hiring needs.</li>
         </ul>
      </div>
   </div>
</section>
<!--End Page Title-->


<section class="about-section-three">
   <div class="auto-container">

      <!--
      <div class="fun-fact-section">
         <div class="row">
            <div class="counter-column col-lg-4 col-md-4 col-sm-12 wow fadeInUp">
               <div class="count-box">
                  <span class="count-text" data-speed="1000" data-stop="4">0</span>k
               </div>
               <h4 class="counter-title">4 thousands active employers</h4>
            </div>

            <div class="counter-column col-lg-4 col-md-4 col-sm-12 wow fadeInUp" data-wow-delay="400ms">
               <div class="count-box">
                  <span class="count-text" data-speed="3000" data-stop="12">0</span>k
               </div>
               <h4 class="counter-title">Over 09k open job positions</h4>
            </div>

            <div class="counter-column col-lg-4 col-md-4 col-sm-12 wow fadeInUp" data-wow-delay="800ms">
               <div class="count-box">
                  <span class="count-text" data-speed="3000" data-stop="20">0</span>k
               </div>
               <h4 class="counter-title">Over 20 thousands resume uploaded</h4>
            </div>
         </div>
      </div>
      -->

      <div class="text-box">
         <h4 class="color-style-1">About Travel Nurse 911</h4>
         <p>
            Welcome to Travel Nurse 911, where we’re revolutionizing healthcare travel nurse staffing with innovative technology and a profound dedication to assisting with the transformation of the healthcare landscape. Our platform has orchestrated over a million connections, empowering traveling healthcare professionals and supporting institutions across the nation.

         </p>
         <p>
            Our mission transcends numbers. It’s about fostering meaningful relationships and aligning the aspirations of traveling healthcare professionals with the unique needs of institutions. We’ve built a dynamic network that not only enhances staffing efficiency but also ensures exceptional patient care by filling positions with qualified travel nurses.

         </p>
         <p>At Travel Nurse 911, every interaction is a step toward a stronger, more resilient healthcare community. We invite you to be part of this journey—where expertise meets opportunity, and compassionate care flourishes. Join us in shaping the future of healthcare staffing.</p>
      </div>
   </div>
</section>
<!-- End About Section Three -->

<!-- Call To Action Two -->
<section class="call-to-action-two about-company" style="background-image: url({{ asset('public/assets/images/background/1.jpg')}});">
   <div class="auto-container">
      <div class="sec-title light text-center">
         <h2>Step Into Your Dream Career Today!</h2>
         <p class="lead text-light mt-4">
            Create endless connections and start your success story. Your journey begins here and now.
         </p>
      </div>

      <div class="btn-box">
         <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-three">Apply Job Now</a>
      </div>
   </div>
</section>
<!-- End Call To Action -->

<!-- Features Section -->
<section class="news-section about-features">
   <div class="auto-container">
      <div class="sec-title text-center">
         <h2 class="color-style-1">Why Choose <b>Travel Nurse 911</b></h2>
         <h5 class="mt-3">
            Leading the Way in Healthcare Staffing Excellence
         </h5>
      </div>

      <div class="row wow fadeInUp">
         <div class="news-block col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box">
               <div class="lower-content">
                  <h3 class="color-style-3">Recruitment & HR</h3>
                  <p class="text">
                     Simplify recruitment with a centralized platform for job postings, resume reviews, and candidate communication. Access pre-qualified applicants and manage them seamlessly with our applicant tracking system. Boost job postings for quicker fulfillment.
                  </p>
               </div>
            </div>
         </div>

         <div class="news-block col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box">
               <div class="lower-content">
                  <h3 class="color-style-3">Compliance Management</h3>
                  <p class="text">
                     Automate compliance with tools for creating customized compliance lists, tracking certifications and licenses, and securely managing documents. Receive automated alerts for key deadlines to stay proactive and compliant.
                  </p>
               </div>
            </div>
         </div>

         <!-- News Block -->
         <div class="news-block col-lg-4 col-md-6 col-sm-12">
            <div class="inner-box">
               <div class="lower-content">
                  <h3 class="color-style-3">Nurse Management</h3>
                  <p class="text">
                     Optimize nurse management with tools for organizing onboarding documents, tracking assignments, and monitoring performance. Use a centralized dashboard to oversee all aspects of nurse administration, ensuring smooth transitions and effective management.

                  </p>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- End Features Section -->

@endsection