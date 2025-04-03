<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<style>
   .list-style-one.blue-list li::before {
      color: #fff;
   }

   .card-custom {
      display: flex;
      align-items: center;
      background-color: #e0e0e0;
      /* Gray background */
      border-radius: 15px;
      padding: 10px;
   }

   .icon-container {
      background-color: #003dff;
      /* Blue background for icon */
      border-radius: 10px;
      padding: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 120px;
      height: 100px;
   }

   .icon-container img {
      width: 40px;
      /* Adjust the size of the icon */
   }

   .text-container {
      margin-left: 15px;
   }

   .text-container h5 {
      margin: 0;
      font-weight: bold;
   }

   .text-container p {
      margin: 0;
   }
</style>

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Pilot Partner Program</h1>

      </div>
   </div>
</section>
<!--End Page Title-->


<section class="about-section-three">
   <div class="small-container">

      <div class="text-box">
         <div class="sec-title w-100 text-center">
            <span class="sub-title color-style-3">Program Overview</span>
            <h2 class="color-style-1">Pilot Partners</h2>
         </div>

         <div class="row">
            <div class="col-md-6">
               <img src="{{ asset('public/assets/images/media/pilot-partner-1.png')}}">

               <h5 class="my-3 mt-4 font-weight-bold">Maximized Exposure for Openings</h5>
               <p class="text-dark mb-0">Once your jobs are posted, our software
                  will start campaigns to bring visibility to
                  your openings. This includes the
                  following:</p>

               <ul class="list-style-one my-3">
                  <li>Job Email Notifications sent to over
                     100k acute care travelers.</li>
                  <li>Job SMS Campaign to target
                     matched candidates.</li>
               </ul>

               <img src="{{ asset('public/assets/images/media/pilot-partner-2.png')}}">



            </div>

            <div class="col-md-6">
               <div class="px-4 py-4 mb-5" style="background-color: #1B1BEB;">
                  <h4 class="color-style-2 mb-3 mt-0">Seamless Set Up Process</h4>
                  <p class="text-white">During the first week of signing up
                     with TravelNurse911.com, you will
                     work with a dedicated Success
                     Manager to ensure the following
                     are completed for maximum job
                     posting visibility:</p>

                  <ul class="list-style-one my-3 blue-list">
                     <li class="text-white">Company Profile Set Up.</li>
                     <li class="text-white">Post Jobs—Would you like us to
                        post your jobs? No problem!
                        Send them our way, and we will
                        post them for you.</li>
                  </ul>
               </div>

               <p class="text-dark">Your dedicated Success Manager will
                  optimize your postings and inform you
                  of all qualified applicants as they
                  arrive. We believe all applicants should
                  be followed up within a timely manner
                  to secure their interests. Your Success
                  Manager will send notifications if any
                  applicants are falling short of this.</p>

               <p class="text-dark">During the piloting period, we will
gather feedback from all partners and
adjust accordingly. We hope that
during this time, we can assist your
company with its end-of-the-year
growth expectations and go into the
New Year with higher hopes for 2025.</p>

               <blockquote class="blockquote-style-two my-4 p-0">
                  <h6 class="mb-0 text-dark font-weight-bold">We look forward to working with you and your team.</h6>
               </blockquote>

            </div>
         </div>
      </div>
   </div>
</section>
<!-- End About Section Three -->

<section class="testimonial-section">
   <div class="small-container">
      <div class="text-box">
         <div class="row">
            <div class="col-md-6">
               <h4 class="color-style-2 mb-3 mt-0 font-weight-bold">Exclusive Pilot Partner
                  Benefits</h4>
               <p class="text-dark mb-0">We will use our resources to attract
                  travel nurses to your job postings.
                  For a limited time, we are also
                  offering these special incentives to
                  all pilot partners:</p>

               <ul class="list-style-one my-3">
                  <li><strong>Free Job Postings</strong> -
                     We will post your jobs to the site
                     and monitor/screen and follow
                     up with applicants on your
                     behalf. This is optional and all
                     partners are welcome to post
                     their own jobs and screen their
                     own applicants.</li>
               </ul>

               <img src="{{ asset('public/assets/images/media/pilot-partner-4.png')}}">
               <div class="px-4 py-4 mb-4" style="background-color: #1B1BEB;">
                  <h4 class="color-style-2 mb-0 mt-0 text-center">Free Forever Plan</h4>
               </div>

               <p class="text-dark"><strong>The free Forever plan is</strong> designed
                  exclusively for pilot partners. If you remain
a partner, this plan will be offered after the
piloting period. The Free Forever plan will
include:</p>

               <ul class="list-style-one my-3">
                  <li>5 Free Monthly Job Posting slots</li>
                  <li>Systemwide Dormant Applicant
                     Outreach - Job Matching</li>
                  <li>Internal Dormant Applicant
                     Outreach (Ongoing Email blasts and
                     text campaigns to dormant
                     applicants on the site.)</li>
                  <li>Free access to Submission and
                     Assignment Management tools</li>
               </ul>



            </div>

            <div class="col-md-6">
               <img src="{{ asset('public/assets/images/media/pilot-partner-3.png')}}">
               <div class="px-4 py-4 mb-4" style="background-color: #1B1BEB;">
                  <h4 class="color-style-2 mb-0 mt-0 text-center">Free Job Postings</h4>
               </div>



               <div class="px-4 py-4 mb-5" style="background-color: #1B1BEB;">
                  <img src="{{ asset('public/assets/images/media/pilot-partner-5.png')}}">
                  <h4 class="color-style-2 mb-3 mt-3">Free Email & Text</h4>

                  <ul class="list-style-one my-3 blue-list">
                     <li class="text-white"><strong>Free Email blasts</strong> - to
                        over 100,000 travel
                        nurses that have been
                        active within the last
                        year.</li>
                     <li class="text-white"><strong>Free Text Campaigns</strong> -
                        We will send SMS
                        messages to potential
                        applicants and direct
                        them to your job
                        postings.</li>
                  </ul>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>

<section class="about-section-three">
   <div class="small-container">

      <div class="text-box">
         <div class="sec-title w-100 text-center">
            <h2 class="color-style-1">Work Smarter with Travel Nurse 911</h2>
            <div class="text">Let us post your jobs and notify you of qualified applicants</div>
         </div>

         <div class="card-custom w-100 mb-3">
            <div class="icon-container">
               <span class="fa fa-bullhorn text-white fa-3x"></span>
            </div>
            <div class="text-container text-center w-100">
               <h5 class="mb-2">POST JOBS</h5>
               <h6 class="mb-0 text-dark">Posted jobs are automatically posted on affiliate sites.</h6>
            </div>
         </div>

         <div class="card-custom w-100 mb-3">
            <div class="icon-container" style="background-color: #8C52FF;">
               <span class="fa fa-envelope text-white fa-3x"></span>
            </div>
            <div class="text-container text-center w-100">
               <h5 class="mb-2">EMAIL & TEXT CAMPAIGN</h5>
               <h6 class="mb-0 text-dark">Email & Text campaigns sent to over 100k Nurses
                  that were active in the last year</h6>
            </div>
         </div>

         <div class="card-custom w-100 mb-3">
            <div class="icon-container" style="background-color: #f85D12;">
               <span class="fa fa-search text-white fa-3x"></span>
            </div>
            <div class="text-container text-center w-100">
               <h5 class="mb-2">SCREEN APPLICANTS</h5>
               <h6 class="mb-0 text-dark">Only top-qualified candidates advance, improving efficiency.</h6>
            </div>
         </div>

         <div class="card-custom w-100 mb-3">
            <div class="icon-container" style="background-color: #233DFF;">
               <span class="fa fa-bell text-white fa-3x"></span>
            </div>
            <div class="text-container text-center w-100">
               <h5 class="mb-2">NOTIFICATIONS</h5>
               <h6 class="mb-0 text-dark">Receive notifications on all applicants that meet requirements.</h6>
            </div>
         </div>

         <p class="text-center text-dark mb-3 mt-5">We would love to work with your agency as a Pilot Partner!</p>

         <p class="text-center text-dark mb-3">Click on the link below and one of our Success Managers will sign
            you up for the site and is available to answer any questions you
            may have.</p>

         <div class="text-center">
            <a href="{{ route('pilot-partner-signup') }}" class="theme-btn btn-style-one">Sign Up</a>
         </div>

      </div>
   </div>
</section>

<section class="testimonial-section mb-5">
   <div class="small-container">
      <div class="text-box">
         <div class="sec-title w-100 text-center">
            <h2 class="color-style-1">Frequently Asked Questions</h2>
         </div>

         <ul class="accordion-box mb-0">
            <!--Block-->
            <li class="accordion block active-block bg-white">
               <div class="acc-btn active">Why should my company participate in the Pilot Program? <span class="icon flaticon-add"></span></div>
               <div class="acc-content current">
                  <div class="content">
                     <p>We think your company can benefit from posting your jobs on
                        TravelNurse911.com for the exposure you will receive from working with our
                        affiliate job posting sites, email, and text campaigns. We are dedicated to
                        bringing exposure to your jobs to ensure you connect with the travel nurses
                        and get them booked for an assignment as quickly as possible.</p>
                  </div>
               </div>
            </li>

            <!--Block-->
       <!--<li class="accordion block bg-white">
               <div class="acc-btn"> How long does the Pilot Program last? <span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                  <div class="content">
                      <p>Pilot Program will run for up to 90 days, through January 1, 2025 or 5 Bookings. Pricing will be based on payment options after 5 Bookings are reached.</p>
                     
                     <p>The piloting period starts at sign-up and ends within 90 days or when the site
                        is an effective recruiting tool for our partners. We will focus on applications
                        received and booking rates during the piloting period. If an agency can book 5+
                        travelers within 90 days, the piloting period will end for that particular agency.</p>
                     */ 
                    
                  </div>
               </div>
            </li> -->

            <!--Block-->
            <li class="accordion block bg-white">
               <div class="acc-btn">How much are the services for the pilot partners? <span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                  <div class="content">
                     <p>Pilot partners receive these services free of charge during the piloting
                        period.</p>
                  </div>
               </div>
            </li>

            

            <!--Block-->
            <li class="accordion block bg-white">
               <div class="acc-btn">What is expected of Pilot Partners? <span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                  <div class="content">
                     <p>The Pilot Partners are expected to post their open travel nursing jobs on the
                        website or send your jobs to your dedicated Success Manager to be posted.
                        Pilot Partners are also expected to follow up with qualified interested
                        applicants as soon as possible.</p>
                  </div>
               </div>
            </li>

            <!--Block-->
            <li class="accordion block bg-white mb-0">
               <div class="acc-btn">How many applicants will we receive weekly? <span class="icon flaticon-add"></span></div>
               <div class="acc-content">
                  <div class="content">
                     <p>During the piloting program, we cannot forecast how many weekly applicants
                        you will receive but we can guarantee that we will work with you hand in hand
                        to ensure your jobs are visible, optimized and promoted on several platforms.</p>
                  </div>
               </div>
            </li>
         </ul>
      </div>
   </div>
</section>

@endsection