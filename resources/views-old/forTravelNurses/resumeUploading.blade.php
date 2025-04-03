<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Seamless Resume Upload</h1>
         <ul class="page-breadcrumb">
            <li>Easily Upload and Manage Resumes with Our User-Friendly Interface</li>
         </ul>
         @include('components.travelNurseMenus', ['currentPage' => 'resumeUploading'])
      </div>
   </div>
</section>
<!--End Page Title-->


<!-- Job Section -->
<section class="about-section-two secion-3" style="padding-top:100px;">
   <div class="auto-container">
      <div class="row wow fadeInUp">


         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column pt-0">
               <div class="sec-title mb-4">
                  <h2 class="mb-0">
                     <h2 class="mb-0">Submit Your Resume</h2>
                  </h2>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>Streamlined Upload Process</h4>
                        <p>Easily upload your resume to your profile with our straightforward process. Simply select the file from your device and complete the upload in just a few seconds.
                        </p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>Accepted File Formats</h4>
                        <p>We support various file formats such as PDF, DOC, and DOCX. Ensure your resume is in one of these formats for a smooth and hassle-free upload.
                        </p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>Resume Recommendations</h4>
                        <p>To make the most of your resume, follow our expert tips and guidelines. Highlight your skills, experiences, and certifications to create a compelling document that captures the attention of potential employers.
                        </p>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <figure class="image"><img src="{{ asset('public/assets/images/resource/recruiter.png') }}" alt=""></figure>
         </div>
      </div>
   </div>
</section>
<!-- End Job Section -->

<!-- Job Section -->
<section class="job-section-four secion-4">
   <div class="auto-container">
      <div class="row wow fadeInUp">


         <!-- content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12 order-2">
            <div class="inner-column">
               <div class="sec-title mb-4">
                  <h2 class="mb-0">Update and Manage Your Resume</h2>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>Update and Revise</h4>
                        <p>Quickly update your resume with new experiences or skills. You can either replace the previous version with a new one or make changes to specific sections on our platform.
                        </p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>Version Tracking</h4>
                        <p>Easily manage different versions of your resume with our version tracking feature. Save multiple drafts and select the one you want to present to employers.</p>
                     </div>
                  </div>
               </div>

               <!-- Job Block -->
               <div class="job-block-five">
                  <div class="inner-box p-4">
                     <div class="content p-0">
                        <h4>Preview and Save</h4>
                        <p>Preview your resume as it will appear to employers and download a copy for your records. Ensure all details are accurate before submitting your applications.</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <figure class="image"><img src="{{ asset('public/assets/images/resource/jobseeker.png') }}" alt=""></figure>
         </div>
      </div>
   </div>
</section>
<!-- End Job Section -->


@endsection