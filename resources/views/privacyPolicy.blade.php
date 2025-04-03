<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Privacy & Policy</h1>
      </div>
   </div>
</section>
<!--End Page Title-->

<!-- About Section Three -->
<section class="about-section-three">
   <div class="auto-container">


      <div class="text-box">
         <p>
            Welcome to Travel Nurse 911 ("we", "our", "us") Privacy Policy. We are committed to protecting your privacy and ensuring that your personal information is handled in a safe and responsible manner. This Privacy Policy outlines how we collect, use, disclose, and safeguard your information when you visit our website or use our services.
         </p>

         <h5 class="mb-2">
            Information We Collect
         </h5>
         <p class="mb-3">
            We may collect personal information that you voluntarily provide to us when you interact with our website or services. This may include:
         </p>
         <ul class="mb-3 list-style-four">
            <li> Contact information such as name, email address, phone number</li>
            <li> Demographic information such as age, gender, location</li>
            <li> Professional information such as resume, qualifications, employment history</li>
            <li> Payment information for processing transactions</li>
         </ul>

         <h5 class="mb-2">How We Use Your Information</h5>

         <p class="mb-3">
            We use the information we collect for various purposes, including but not limited to:
         </p>

         <ul class="mb-3 list-style-four">
            <li> Providing and managing our services to you</li>
            <li> Communicating with you, including responding to your inquiries and providing customer support</li>
            <li> Improving our website and services</li>
            <li> Personalizing your experience and delivering relevant content and advertisements</li>
            <li> Complying with legal obligations and resolving disputes</li>
         </ul>

         <h5 class="mb-2">Sharing Your Information</h5>

         <p class="mb-3">We may share your personal information with third parties in the following circumstances:</p>

         <ul class="mb-3 list-style-four">
            <li> With service providers who assist us in operating our website and providing our services (e.g., hosting providers, payment processors)</li>
            <li> When required by law or to protect our rights</li>
            <li> With your consent or at your direction</li>
         </ul>

         <h5 class="mb-2">Security of Your Information</h5>

         <p class="mb-3">
            We take reasonable measures to protect your personal information from unauthorized access, use, or disclosure. However, no method of transmission over the internet or electronic storage is 100% secure. Therefore, while we strive to use commercially acceptable means to protect your personal information, we cannot guarantee its absolute security.
         </p>

         <h5 class="mb-2">
            Your Choices and Rights
         </h5>
         <p class="mb-3">
            You have the right to:
         </p>
         <ul class="mb-3 list-style-four">
            <li> Access and update your personal information</li>
            <li> Request deletion of your personal information</li>
            <li> Opt-out of receiving marketing communications</li>
            <li> Object to the processing of your personal information</li>
            <li> Withdraw your consent where we rely on consent as a legal basis for processing</li>
         </ul>

         <h5 class="mb-2">Changes to This Privacy Policy</h5>

         <p class="mb-3">We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page with an updated effective date. You are advised to review this Privacy Policy periodically for any changes.
         </p>

         <h5 class="mb-2">Contact Us</h5>

         <p>If you have any questions about this Privacy Policy, please contact us:</p>

         <p class="mb-0"><strong>Email</strong>: <a href="mailto:{{ config('custom.email') }}" class="email">{{ config('custom.email') }}</a></p>
         <p><strong>Address</strong>: 329 Queensberry Street, North Melbourne VIC
            3051, USA. </p>
         </p>
      </div>
   </div>
</section>
<!-- End About Section Three -->



@endsection