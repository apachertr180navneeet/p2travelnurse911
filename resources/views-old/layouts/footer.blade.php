<?php

use App\Helper\CommonFunction;
?>
<footer class="main-footer alternate3">
   <div class="auto-container">
      <div class="widgets-section wow fadeInUp pb-lg-0 pt-lg-3">
         <div class="row">
            <div class="big-column col-xl-4 col-lg-3 col-md-12 mb-lg-0">
               <div class="footer-column about-widget">
                  <div class="logo">
                     <a href="#"><img src="{{ asset('public/assets/images/logo.png') }}" /></a>
                  </div>
                  <p class="phone-num">
                     <span>Call us </span><a href="tell:{{ config('custom.phone') }}">{{ config('custom.phone') }}</a>
                  </p>
                  <p class="address">
                     <!--
                     329 Queensberry Street, North Melbourne VIC<br />
                     3051, USA. <br />
                     -->
                     <a href="mailto:{{ config('custom.email') }}" class="email">{{ config('custom.email') }}</a>
                  </p>
               </div>
            </div>

            <div class="big-column col-xl-8 col-lg-9 col-md-12 mb-lg-0">
               <div class="row">
                  <div class="footer-column col-lg-4 col-md-6 col-sm-12 mb-lg-0">
                     <div class="footer-widget links-widget">
                        <h4 class="widget-title">For Job Seekers</h4>
                        <div class="widget-content">
                           <ul class="list">
                              <li><a href="{{ route('professional-profile') }}">Professional Profile</a></li>
                              <li><a href="{{ route('document-safe') }}">Document Safe</a></li>
                              <li><a href="{{ route('free-job-application') }}">Free Job Application</a></li>
                              <li><a href="{{ route('shortlisted-jobs') }}">Shortlisted Jobs</a></li>
                              <li><a href="{{ route('messaging-sms') }}">Messaging & SMS</a></li>
                           </ul>
                        </div>
                     </div>
                  </div>

                  <div class="footer-column col-lg-4 col-md-6 col-sm-12 mb-lg-0">
                     <div class="footer-widget links-widget">
                        <h4 class="widget-title">For Employers</h4>
                        <div class="widget-content">
                           <ul class="list">
                              <li><a href="{{ route('agency-job-posting') }}">Job Posting</a></li>
                              <li><a href="{{ route('agency-follow-up-scheduling') }}">Follow Up Scheduling</a></li>
                              <li><a href="{{ route('agency-travel-nurse-management') }}">Travel Nurse Management</a></li>
                              <li><a href="{{ route('agency-task-management') }}">Task Management</a></li>
                              <li><a href="{{ route('agency-compliance-files') }}">Compliance Files</a></li>
                           </ul>
                        </div>
                     </div>
                  </div>

                  <div class="footer-column col-lg-4 col-md-6 col-sm-12 mb-lg-0">
                     <div class="footer-widget links-widget">
                        <h4 class="widget-title">About Us</h4>
                        <div class="widget-content">
                           <ul class="list">
                              <li><a href="{{ route('company') }}">Company</a></li>
                              <li><a href="{{ route('blogs') }}">Blogs</a></li>
                              <li><a href="{{ route('faqs') }}">FAQs</a></li>
                              <li><a href="{{ route('term-conditions') }}">Terms of Use</a></li>
                              <li><a href="{{ route('privacy-policy') }}">Privacy Center</a></li>
                              <!--<li><a href="#">Contact</a></li>-->
                           </ul>
                        </div>
                     </div>
                  </div>

               </div>
            </div>
         </div>
      </div>
   </div>

   <!--Bottom-->
   <div class="footer-bottom pt-lg-0">
      <div class="auto-container">
         <div class="outer-box justify-content-center">
            <div class="copyright-text">
               © 2024 Travel Nurse 911. All Right Reserved.
            </div>
            <!-- 
            <div class="social-links">
               <a href="#"><i class="fab fa-facebook-f"></i></a>
               <a href="#"><i class="fab fa-twitter"></i></a>
               <a href="#"><i class="fab fa-instagram"></i></a>
               <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            -->
         </div>
      </div>
   </div>

   <!-- Scroll To Top -->
   <div class="scroll-to-top scroll-to-target" data-target="html">
      <span class="fa fa-angle-up"></span>
   </div>

</footer>