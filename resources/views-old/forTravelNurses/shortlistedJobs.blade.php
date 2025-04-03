<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Shortlisted Jobs</h1>
         <ul class="page-breadcrumb">
            <li>Save your favorite job listings and access them anytime</li>
         </ul>
         @include('components.travelNurseMenus', ['currentPage' => 'shortlistedJobs'])
      </div>
   </div>
</section>
<!--End Page Title-->

<section class="about-section-two secion-3" style="padding-top:100px;">
   <div class="auto-container">
      <div class="row wow fadeInUp">

         <!-- Image Column -->
         <div class="image-column col-lg-12 col-md-12 col-sm-12 m-0">
            <figure class="image"><img src="{{ asset('public/assets/images/media/Post-39.jpg') }}" alt=""></figure>
         </div>
        </div>
    </div>
</section>
<!-- Job Section -->
<section class="about-section-two secion-3" style="padding-top:100px;">
   <div class="auto-container">
      <div class="row wow fadeInUp">

         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column pt-0">
               <div class="sec-title mb-4">
                  <h2 class="mb-0">
                     <h2 class="mb-0 color-style-1">Shortlisted Jobs</h2>
                     <p class="text mw-100">Know the exact moment you’ve made it through the initial screening process. Recruiters are encouraged to move applicants through their pipeline as quickly as possible and change applicant’s statuses from applied to shortlisted if the applicant meets all requirements.</p>

                     <p class="text mw-100">
                        Once your application has been marked shortlisted, you will be receiving a call or message from a Recruiter very soon. Monitor your inbox, cell phone, text messaging and voice mail just in case you miss their call. Good Luck on your interviews!
                     </p>
                  </h2>
               </div>
            </div>
         </div>

         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12 ">
            <div class="inner-column pt-0">
               <div class="sec-title mb-4">
                  <h2 class="mb-0 color-style-1">Interview Preparation</h2>

                  <p class="text mw-100">
                     Our platform provides a wealth of resources to help you prepare for your interviews. From expert tips on answering common interview questions to detailed guides on best practices, we ensure you have everything you need to make a great impression.
                  </p>

                  <p class="text mw-100">
                     Additionally, you can access comprehensive information about the employer and the specific job role, enabling you to tailor your preparation effectively. Stay confident and well-prepared with our invaluable insights and resources to help you succeed in your interviews.
                  </p>

                  <ul class="list-style-one my-3">
                     <li>Expert interview tips and advice.</li>
                     <li>Best Interview Questions to ask. </li>
                     <li>Specific job role insights.</li>
                     <li>Resources to boost confidence.</li>
                  </ul>

                  <a href="{{ route('faqs') }}" class="theme-btn btn-style-one bg-blue">
                     <span class="btn-title">Explore Our Interview FAQs</span>
                  </a>
               </div>


            </div>
         </div>


      </div>
   </div>
</section>
<!-- End Job Section -->

@endsection
