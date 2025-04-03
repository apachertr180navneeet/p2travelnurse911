<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')



<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>For Travel Nurses</h1>
         <ul class="page-breadcrumb">
            <li>Maximize Your Career Potential</li>
         </ul>

         @include('components.travelNurseMenus', ['currentPage' => 'default'])
      </div>
   </div>
</section>
<!--End Page Title-->

<section class="app-section">
   <div class="auto-container">
      <div class="row">
         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12">

            <figure class="image wow fadeInLeft">
               <img src="{{ asset('public/assets/images/media/Post-5-01.jpg')}}" alt="">
            </figure>
         </div>

         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInRight p-0">
               <div class="sec-title">
                  <h3 class="color-style-1">The fastest growing job site for Travel Nurses, bridging the gap between the travel agencies and hospitals with their own internal travel nursing programs.</h3>

                  <div class="text">Sign up to receive awesome benefits and job alerts.</div>
                  <a href="{{ config('custom.register_url') }}" class="theme-btn btn-style-one my-3">Sign Up Today</a>

               </div>

            </div>
         </div>
      </div>
   </div>
</section>

<script>
   document.addEventListener("DOMContentLoaded", function() {
      const cards = document.querySelectorAll('.news-section .cards .news-block-two');
      const colorCombinations = ['card-combo-4', 'card-combo-1', 'card-combo-6', 'card-combo-5', 'card-combo-3', 'card-combo-2'];

      cards.forEach((card, index) => {
         const comboClass = colorCombinations[index % colorCombinations.length];
         card.classList.add(comboClass);
      });
   });
</script>

<section class="news-section secion-1 aat-card-container  mb-5" style="background-color:#F0F5F7;">
   <div class="container-fluid">
      <div class="sec-title text-center">
         <h2 class="mb-2 color-style-1">Navigate Your Nursing Career</h2>
         <h6 class="text-muted mt-2">Discover the Benefits and Tools Designed for Your Success.</h6>
      </div>

      <div class="cards">

         <div class="news-block-two mb-4 pt-0 " data-index="0">
            <a href="{{ route('travel-nurse-benefits') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Travel Nurse Benefits </h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Discover the perks and advantages tailored specifically for travel nurses, from housing stipends to travel reimbursements and more.
                     </h5>
                  </div>
                  <div class="image-box order-2 ">
                     <figure class="image justify-content-center align-items-center w-100 h-100">
                        <img src="{{ asset('public/assets/images/media/Post-6-01.jpg') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

         <div class="news-block-two mb-4 pt-0  " data-index="0">
            <a href="{{ route('professional-profile') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Professional Profile</h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Build and maintain a comprehensive profile that highlights your skills, experience, and certifications to attract top employers.
                     </h5>
                  </div>
                  <div class="image-box p-5">
                     <figure class="image">
                        <img src="{{ asset('public/assets/images/resource/image-3.png') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

         <div class="news-block-two mb-4 pt-0  " data-index="0">
            <a href="{{ route('document-safe') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Document Safe</h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Ensure all required compliance files and documents are organized in a single location.
                     </h5>
                  </div>
                  <div class="image-box p-5">
                     <figure class="image">
                        <img src="{{ asset('public/assets/images/media/Post-16-01.jpg') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

         <div class="news-block-two mb-4 pt-0  " data-index="0">
            <a href="{{ route('application-status-tracking') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Application Status Tracking</h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Keep track of your job applications in real-time, knowing exactly where you stand in the hiring process at any moment.
                     </h5>
                  </div>
                  <div class="image-box p-5 order-2">
                     <figure class="image">
                        <img src="{{ asset('public/assets/images/media/Post-7-01.jpg') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

         <div class="news-block-two mb-4 pt-0  " data-index="0">
            <a href="{{ route('messaging-sms') }}">
               <div class="inner-box p-0 border">
                  <div class="content-box text-center p-4 p-lg-5">
                     <h1 class="mb-3 font-weight-bold">Messaging & SMS</h1>
                     <h5 class="text-dark font-weight-bold" style="line-height: 1.5;">
                        Stay connected with recruiters and receive timely updates through our integrated messaging and sms system.
                     </h5>
                  </div>
                  <div class="image-box p-5">
                     <figure class="image">
                        <img src="{{ asset('public/assets/images/media/Post-9-01.png') }}" />
                     </figure>
                  </div>
               </div>
            </a>
         </div>

      </div>
   </div>
</section>


<!-- Job Section -->
<section class="steps-section">
   <div class="auto-container">
      <div class="row wow fadeInUp">

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12 ">
            <figure class="image"><img src="{{ asset('public/assets/images/media/Post-10.jpg') }}" alt=""></figure>
         </div>
         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column">
               <div class="sec-title">
                  <h2 class="color-style-1">How it Works</h2>
                  <p class="text">Learn how our platform simplifies the process of finding and securing your next assignment, from job search to application and beyond.</p>
               </div>

               <ul class="steps-list mt-4">

                  <li><span class="count">1</span><span class="color-style-3">Upload your Resume and Complete your Profile</span>
                     <p class="mt-2">Get started by uploading your resume and filling out your professional profile. This helps us match you with the best opportunities tailored to your experience and preferences.</p>
                  </li>

                  <li><span class="count">2</span><span class="color-style-3">Apply to jobs that meet your desires</span>
                     <p class="mt-2">Explore our extensive job listings and apply to positions that align with your career goals and personal preferences. Find the perfect fit in just a few clicks.</p>
                  </li>

                  <li><span class="count">3</span><span class="color-style-3">Interview and receive Offers</span>
                     <p class="mt-2">Once you've applied, participate in interviews with top healthcare facilities. Receive job offers that match your expectations and start your travel nursing journey.</p>
                  </li>
               </ul>


            </div>
         </div>


      </div>
   </div>
</section>
<!-- End Job Section -->

<!-- Call To Action -->
<section class="registeration-bannerss pb-5 secion-10 border-bottom-0">
   <div class="auto-container">
      @include('components.call-to-actions.callToActionBanners')
   </div>
</section>
<!-- End Call To Action -->

@endsection