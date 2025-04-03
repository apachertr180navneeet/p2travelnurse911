<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Streamlined Follow Up Scheduling</h1>
         <ul class="page-breadcrumb">
            <li>Effortlessly Manage and Schedule Follow-Ups using our Integrated Calendar Tool</li>
         </ul>
         @include('components.healthcareFacilitiesMenus', ['currentPage' => 'followUpScheduling'])
      </div>
   </div>
</section>
<!--End Page Title-->


<!-- Job Section -->
<section class="about-section secion-3">
   <div class="auto-container">
      <div class="row wow fadeInUp">


         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12 d-flex align-items-center">
            <figure class="image"><img src="{{ asset('public/assets/images/media/Post-29-01.jpg') }}" alt=""></figure>
         </div>

         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column pt-3">
               <div class="sec-title">
                  <h2 class="color-style-1">Schedule Follow Up</h2>
                  <div class="text mw-100">Our follow-up scheduling tool simplifies the process of arranging follow-ups, making it easier than ever to coordinate with job applicants and actively working travelers. Offering a user-friendly interface, it allows you to select convenient time slots that fit perfectly into your calendar, keeping everyone organized and on track.</div>

                  <div class="text mw-100">This tool also automatically sends reminders and updates, ensuring you never miss a follow-up. You can view upcoming follow-ups, reschedule them if necessary, and keep detailed notes on each follow-up to stay organized. </div>

                  <div class="text mw-100">Plus, it integrates seamlessly with your existing calendar systems, providing a smooth and synchronized scheduling experience ensuring that all your professional engagements are coordinated effortlessly.
                  </div>
               </div>

            </div>
         </div>
         
         <div class="col-lg-12 col-md-12 col-sm-12 mb-lg-5">
            <blockquote class="blockquote-style-one mb-5 mt-0">
               <ul class="list-style-one mb-0 row">
                  <li class="col-lg-4 col-md-12 col-sm-12"><strong>Simple and intuitive interface</strong></li>
                  <li class="col-lg-4 col-md-12 col-sm-12"><strong>Choose convenient time slots</strong></li>
                  <li class="col-lg-4 col-md-12 col-sm-12"><strong>Track all scheduled follow ups </strong></li>
                  <li class="col-lg-4 col-md-12 col-sm-12"><strong>View and manage upcoming follow ups</strong></li>
                  <li class="col-lg-4 col-md-12 col-sm-12"><strong>Easily reschedule if necessary</strong></li>
                  <li class="col-lg-4 col-md-12 col-sm-12"><strong>Automated reminders and updates</strong></li>
                  <li class="col-lg-4 col-md-12 col-sm-12"><strong>Seamless calendar integration</strong></li>
               </ul>
            </blockquote>
         </div>

      </div>
   </div>
</section>
<!-- End Job Section -->


@endsection
