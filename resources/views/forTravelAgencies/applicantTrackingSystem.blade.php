<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Applicant Tracking System</h1>
         <ul class="page-breadcrumb">
            <li>Streamline Your Hiring Process with Advanced Applicant Tracking</li>
         </ul>
         @include('components.travelAgenciesMenus', ['currentPage' => 'applicantTrackingSystem'])
      </div>
   </div>
</section>
<!--End Page Title-->



<!-- About Section -->
<section class="about-section">
   <div class="auto-container">
      <div class="row">
         <!-- Content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12 order-2">
            <div class="inner-column wow fadeInUp">
               <div class="sec-title">
                  <h2 span class="color-style-1">Streamline Your Recruitment Process with Our Advanced Candidate Tracking System</h2>
                  <div class="text">Our Candidate Tracking System (CTS) is designed to simplify and enhance the recruitment process for travel nurses. With an intuitive interface and powerful features, our CTS ensures that you can efficiently manage and monitor every stage of the candidate lifecycle, from application to assignment.</div>
               </div>
            </div>
         </div>

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <figure class="image wow fadeInLeft"><img src="{{ asset('public/assets/images/media/Post-23.jpg') }}" alt="Search Travel Nursing Jobs"></figure>

         </div>
      </div>
   </div>
</section>
<!-- End About Section -->

<!-- Job Section -->
<section class="job-section-five">
   <div class="small-container">
      <div class="row wow fadeInUp">
         <div class="featured-column col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="sec-title text-center">
               <h2 class="color-style-1">Key Features</h2>
            </div>
            <div class="outer-box">

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>1. Comprehensive Candidate Profiles</h4>
                        <p>Create detailed profiles for each candidate, including personal information, work history, certifications, and references. Our system allows for easy uploading and storage of resumes, licenses, and other essential documents.</p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>2. Job Posting and Application Management</h4>
                        <p>Post job openings and receive applications directly through our platform. Track the status of each application, and easily manage candidate information and documents in one place.</p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>3. Follow Up Scheduling and Management</h4>
                        <p>Coordinate interviews with candidates using our built-in scheduling tool. Send automatic reminders and notifications to ensure that both candidates and recruiters are always informed and prepared.</p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>4. Real-Time Status Updates</h4>
                        <p>Stay up-to-date with real-time status updates on candidate progress. Our system provides visibility into each step of the recruitment process, ensuring transparency and efficiency.</p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>5. Task Management</h4>
                        <p>Assign tasks to team members, set deadlines, and track progress. Our task management feature helps keep your recruitment team organized and ensures that nothing falls through the cracks.</p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>6. Compliance Tracking</h4>
                        <p>Ensure that all candidates meet necessary compliance requirements. Our system tracks certifications, licenses, and other compliance documents, alerting you when renewals are needed.</p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>7. Customizable Dashboards and Reports</h4>
                        <p>Gain insights into your recruitment process with customizable dashboards and detailed reports. Monitor key metrics, identify bottlenecks, and make data-driven decisions to optimize your recruitment strategy.</p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>8. Communication Tools</h4>
                        <p>Communicate with candidates via email and SMS directly through our platform. Keep all communications in one place for easy reference and follow-up.</p>
                     </div>
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</section>
<!-- End Job Section -->

<!-- About Section -->
<section class="about-section">
   <div class="auto-container">
      <div class="row">

         <!-- Content Column -->
         <div class="content-column col-lg-12 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInUp">

               <div class="sec-title">
                  <h2 class="mb-4 mt-4 color-style-1">Why Choose Our Applicant Tracking System?</h2>
               </div>

               <ul class="list-style-one">
                  <li><strong>Efficiency</strong> : Automate repetitive tasks and streamline your workflow to save time and resources.</li>

                  <li><strong>Organization</strong> : Keep all candidate information and documents in one centralized location.</li>

                  <li><strong>Compliance</strong> : Ensure that all candidates meet regulatory requirements with automated tracking and alerts.</li>

                  <li><strong>Transparency</strong> : Gain visibility into every stage of the recruitment process with real-time updates and detailed reports.</li>

                  <li><strong>User-Friendly</strong> : Our intuitive interface is easy to navigate, making it simple for your team to adopt and use.</li>
               </ul>

               <div class="sec-title">
                  <h2 class="mb-4 mt-4 color-style-1">Get Started Today</h2>
                  <p>Transform your recruitment process with our Applicant Tracking System. Contact us today to learn more and schedule a demo. Let us help you find the best travel nurses quickly and efficiently, so you can focus on providing exceptional care.</p>
               </div>

            </div>
         </div>


      </div>
   </div>
</section>
<!-- End About Section -->



@endsection
