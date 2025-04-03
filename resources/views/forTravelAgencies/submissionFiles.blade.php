<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Professional Submission Files
         </h1>
         <ul class="page-breadcrumb">
            <li>Effortlessly Manage and Submit Essential Documents for Compliance and Credentialing</li>
         </ul>
         @include('components.travelAgenciesMenus', ['currentPage' => 'submissionFiles'])
      </div>
   </div>
</section>
<!--End Page Title-->


<section class="about-section">
   <div class="auto-container">
      <div class="row mb-5">
         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column">
               <figure class="image"><img src="{{ asset('public/assets/images/media/Post-24-01.jpg') }}" alt="Nursing Job Application"></figure>

            </div>
         </div>

         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInUp">
               <div class="sec-title">
                  <h2 class="color-style-1">Submission Files</h2>
                  <div class="text">The Professional Submission Files page is designed to streamline the document management process for employers, ensuring that all required documents for potential candidates are efficiently gathered and submitted. This system allows you to easily verify qualifications and maintain compliance with industry standards.</div>
               </div>
            </div>
         </div>
      </div>

      <div class="row wow fadeInUp mb-5">


         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column">
               <div class="sec-title  mb-3">
                  <h2 class="color-style-1">Submittal Ready Documents</h2>
                  <div class="text mw-100">When reviewing candidates for travel nursing positions, it's crucial to have all necessary documents ready for submission. These documents help verify the candidate's qualifications and ensure they meet your facility's requirements.</div>
               </div>

               <ul class="list-style-three mt-0">
                  <li><strong class="color-style-3">Resume/CV</strong> : Review candidates' resumes or CVs to ensure they include their educational background, professional experience, certifications, and specialized skills. The system allows you to highlight the most relevant experiences that align with your needs.</li>

                  <li><strong class="color-style-3">Professional References</strong> : Collect and verify contact information for at least two professional references per candidate. These references should attest to the candidate's skills, work ethic, and experience in the nursing field.</li>

                  <li><strong class="color-style-3">Skills Checklist</strong> : Access completed skills checklists that outline the candidate's proficiency in various nursing tasks and procedures. This helps you assess their competency and suitability for specific assignments.</li>


               </ul>

            </div>
         </div>

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12 d-flex align-items-center">
            <figure class="image"><img src="{{ asset('public/assets/images/media/Post-31-01.jpg') }}" alt="Search Travel Nursing Jobs"></figure>
         </div>
      </div>

      <div class="row wow fadeInUp">

         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column">
               <figure class="image"><img src="{{ asset('public/assets/images/resource/jobseeker.png') }}" alt="Search Travel Nurse Jobs"></figure>

            </div>
         </div>

         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column">
               <div class="sec-title mb-3">
                  <h2 class="color-style-1">Document Submission Process</h2>
                  <div class="text mw-100">Managing candidate documents is straightforward with our system:</div>
               </div>

               <ul class="list-style-three mt-0">
                  <li><strong class="color-style-3">Log In to Your Employer Account</strong> : Access your employer account to review and manage candidate submissions.</li>

                  <li><strong class="color-style-3">Upload and Organize Documents</strong> : Employers can easily upload and categorize the necessary documents for each candidate, ensuring that all files are in the correct format and meet your standards.</li>

                  <li><strong class="color-style-3">Review and Finalize</strong> : Before finalizing, review the documents for clarity and accuracy. The system allows you to make any necessary adjustments or request additional documentation from the candidate.
                  </li>

               </ul>

            </div>
         </div>

      </div>
   </div>
</section>

<section class="job-section-one pb-5">
   <div class="small-container">
      <blockquote class="blockquote-style-one mb-5">
         <p>By following these steps, you can ensure that all professional submission files are in order, making it easier to evaluate and onboard qualified candidates. If you have any questions or need assistance, please contact our support team for help.</p>
      </blockquote>
   </div>
</section>

@endsection
