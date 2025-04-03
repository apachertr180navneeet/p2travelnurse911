<?php

use App\Helper\CommonFunction;
?>
<div class="main-box">
  <div class="nav-outer">
    <div class="logo-box">
      <div class="logo mr-0">
        <a href="{{ route('home') }}">
          <img src="{{ asset('public/assets/images/logo.png') }}" title="" />
        </a>
      </div>
    </div>
    <nav class="nav main-menu">
      <ul class="navigation" id="navbar">

        <li><a href="{{ route('jobs-search') }}">Jobs</a></li>
        <!--<li><a href="{{ route('locations') }}">Locations</a></li>-->


        <li><a href="{{ route('for-travel-nurses') }}">For Travel Nurses</a></li>

        <!-- 
        <li class="dropdown">
          <span>For Travel Nurses</span>
          <ul>
            <li><a href="{{ route('travel-nurse-benefits') }}">Travel Nurse Benefits</a></li>
            <li><a href="{{ route('professional-profile') }}">Professional Profile</a></li>
            <li><a href="{{ route('document-safe') }}">Document Safe</a></li>
            <li><a href="{{ route('application-status-tracking') }}">Application Status Tracking</a></li>
            <li><a href="{{ route('messaging-sms') }}">Messaging & SMS</a></li>
            <li><a href="{{ route('shortlisted-jobs') }}">Shortlisted Jobs</a></li>
          </ul>
        </li>
        -->


        <li><a href="{{ route('for-employers') }}">For Employers</a></li>

        <!-- 
        <li class="dropdown has-mega-menu" id="has-mega-menu">
          <span>For Employers</span>
          <div class="mega-menu" id="mega-menu">
            <div class="mega-menu-bar row">
              <div class="column col-lg-6 col-md-6 col-sm-12">
                <h3>Travel Agencies</h3>
                <ul>
                  <li><a href="{{ route('agency-job-posting') }}">Free Job Postings</a></li>
                  <li><a href="{{ route('agency-applicant-tracking-system') }}">Applicant Tracking System</a></li>
                  <li><a href="{{ route('agency-submission-files') }}">Submission Files</a></li>
                  <li><a href="{{ route('agency-travel-nurse-management') }}">Travel Nurse Management</a></li>
                  <li><a href="{{ route('agency-compliance-files') }}">Compliance File Management</a></li>
                  <li><a href="{{ route('agency-follow-up-scheduling') }}">Follow Up Scheduling</a></li>
                  <li><a href="{{ route('agency-task-management') }}">Task Management</a></li>
                </ul>
              </div>

              <div class="column col-lg-6 col-md-6 col-sm-12">
                <h3>Healthcare Facilities</h3>
                <ul>
                  <li><a href="{{ route('facility-job-posting') }}">Free Job Postings</a></li>
                  <li><a href="{{ route('applicant-tracking-system') }}">Applicant Tracking System</a></li>
                  <li><a href="{{ route('facility-travel-nurse-management') }}">Travel Nurse Management</a></li>
                  <li><a href="{{ route('facility-compliance-files') }}">Compliance File Management</a></li>
                  <li><a href="{{ route('facility-follow-up-scheduling') }}">Follow Up Scheduling</a></li>
                  <li><a href="{{ route('facility-task-management') }}">Task Management</a></li>
                </ul>
              </div>
            </div>
          </div>
        </li>
        -->

        <li class="dropdown">
          <span>Travel Nurse Resources</span>
          <ul>
            <li><a href="{{ route('faqs') }}">Travel Nurse FAQs</a></li>
            <!--<li><a href="#">Travel Nurse Agencies</a></li>-->
            <!--<li><a href="#">Travel Agency Directory</a></li>-->
            <li><a href="{{ route('nursing-ceus') }}">Nursing CEUs</a></li>
            <!--<li><a href="#">Certifications By Specialty</a></li>-->
            <li><a href="{{ route('nursing-compact-states') }}">Nursing Compact States</a></li>
            <li><a href="{{ route('travel-nurse-housing') }}">Travel Nurse Housing</a></li>
            <li><a href="{{ route('travel-nurse-blogs') }}">Travel Nurse Blogs</a></li>
            <li><a href="{{ route('blogs') }}">Our Blog</a></li>
          </ul>
        </li>

        <li><a href="{{ route('contact-us') }}">Contact Us</a></li>

        <!-- Only for Mobile View -->
        <li class="mm-add-listing">
          <a href="{{ config('custom.client_login_url') }}" class="theme-btn btn-style-one">Job Post</a>
          <span>
            <span class="contact-info">
              <span class="phone-num"><span>Call us</span><a href="tel:1234567890">1-800-485-7911</a></span>
              <span class="address">329 Queensberry Street, North Melbourne VIC <br />3051, USA.</span>
              <a href="mailto:support@travelnurse911.com" class="email">support@travelnurse911.com</a>
            </span>
            <span class="social-links">
              <a href="#"><span class="fab fa-facebook-f"></span></a>
              <a href="#"><span class="fab fa-twitter"></span></a>
              <a href="#"><span class="fab fa-instagram"></span></a>
              <a href="#"><span class="fab fa-linkedin-in"></span></a>
            </span>
          </span>
        </li>
      </ul>
    </nav>
  </div>
  <div class="outer-box">

    <!-- 
    <a href="{{ config('custom.login_url') }}" class="upload-cv">Upload your Resume</a>
    -->

    <!-- Login/Register -->
    <div class="btn-box">
      <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-one">Login</a>
      <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-one">Upload Resume</a>
      <a href="{{ config('custom.client_login_url') }}" class="theme-btn btn-style-one">Post Jobs</a>
    </div>
  </div>

</div>
<!-- Mobile Header -->
<div class="mobile-header">
  <div class="logo">
    <a href="{{ route('home') }}">
      <img src="{{ asset('public/assets/images/logo.png') }}" title="" />
    </a>
  </div>

  <!--Nav Box-->
  <div class=" nav-outer clearfix">
    <div class="outer-box">
      <a href="#nav-mobile" class="mobile-nav-toggler"><span class="flaticon-menu-1"></span></a>
    </div>
  </div>
</div>

<div id="nav-mobile"></div>