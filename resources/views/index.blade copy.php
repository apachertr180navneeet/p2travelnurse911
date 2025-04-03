<?php

use App\Helper\CommonFunction;
use Carbon\Carbon;

?>

@extends('layouts.app')
@section('content')

<!-- Banner Section-->
<section class="banner-section secion-1">
   <div class="auto-container">
      <div class="row">
         <div class="content-column col-lg-6 col-md-12 col-sm-12">
            <div class="inner-column wow fadeInUp" data-wow-delay="1000ms">
               <div class="title-box">
                  <h3>Begin Your Healthcare Career <br />Journey with <br /><span class="colored">Travel Nurse 911</span> </h3>
                  <h4>Seamlessly manage your job on a platform where you are truly valued</h4>

                  <a href="{{ config('custom.user_job_url') }}" class="theme-btn btn-style-one mt-5"><span class="btn-title">Explore Jobs <i class="fa fa-angle-right"></i></span></a>
               </div>
            </div>
         </div>

         <div class="image-column col-lg-6 col-md-12">
            <div class="image-box">
               <figure class="main-image wow fadeIn" data-wow-delay="500ms">
                  <img src="{{ asset('public/assets/images/banner-img-2.png') }}" />
               </figure>

               <!-- Info BLock One -->
               <div class="info_block anm wow fadeIn" data-wow-delay="1000ms" data-speed-x="2" data-speed-y="2">
                  <span class="icon flaticon-email-3"></span>
                  <p>New Job Opportunity</p>
               </div>

               <!-- Info BLock Three -->
               <div class="info_block_three anm wow fadeIn" data-wow-delay="1500ms" data-speed-x="4" data-speed-y="4">
                  <span class="icon flaticon-briefcase"></span>
                  <p>Featured Employer</p>
                  <span class="right_icon fa fa-check"></span>
               </div>

               <!-- Info BLock Four -->
               <div class="info_block_four anm wow fadeIn" data-wow-delay="2500ms" data-speed-x="3" data-speed-y="3">
                  <span class="icon flaticon-file"></span>
                  <div class="inner">
                     <p>Quick CV Upload</p>
                     <span class="sub-text">It only takes a few seconds</span>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- End Banner Section-->

<!-- Job Section -->
@if(isset($jobs) && !empty($jobs))
<section class="news-section secion-7">
   <div class="auto-container">
      <div class="sec-title text-center">
         <h2>Featured Jobs</h2>
         <h6 class="text-muted mt-2">Embrace your value by seeking jobs that align with your desires</h6>
      </div>

      <div class="row wow fadeInUp">
         <?php foreach ($jobs as $row) { ?>
            <div class="job-block col-lg-6 col-md-12 col-sm-12">
               <div class="inner-box bg-light">
                  <div class="content pl-0">
                     <?php if (isset($row->profile_pic_path) && !empty($row->profile_pic_path) && 0) { ?>
                        <span class="company-logo">
                           <img src="{{$row->profile_pic_path}}" />
                        </span>
                     <?php } ?>
                     <h4><a href="{{ route('job',$row->unique_id) }}">{{ $row->title }}</a></h4>
                     <ul class="job-info">
                        <li><span class="icon flaticon-map-locator"></span> {{ $row->city_name}}, {{ $row->state_code}}</li>

                        <?php
                        $givenTime = Carbon::parse($row->created_at);
                        $currentTime = Carbon::now();
                        $timeDifference = $givenTime->diffForHumans($currentTime);
                        ?>
                        <li><span class="icon flaticon-clock-3"></span> {{ $timeDifference }}</li>
                        <?php if (isset($row->salary_start_range) && !empty($row->salary_start_range) && isset($row->salary_end_range) && !empty($row->salary_end_range)) { ?>
                           <li><span class="icon flaticon-money"></span> ${{ $row->salary_start_range }} - ${{ $row->salary_end_range }}</li>
                        <?php } ?>
                     </ul>
                     <ul class="job-other-info">
                        <?php if (isset($row->total_opening) && $row->total_opening > 0) { ?>
                           <li class="time">{{ $row->total_opening }} Opening</li>
                        <?php } ?>
                        <li class="privacy">{{ $row->profession }}</li>
                        <li class="required">{{ $row->specialty }}</li>
                        <?php if (isset($row->shift_title) && !empty($row->shift_title)) { ?>
                           <li class="bg-info text-white">{{ $row->shift_title }} Shift</li>
                        <?php } ?>
                     </ul>
                     <!--<button class="bookmark-btn"><span class="flaticon-bookmark"></span></button>-->
                  </div>
               </div>
            </div>
         <?php } ?>
      </div>

      <div class="btn-box text-center">
         <a href="{{ route('jobs') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Load More Opening</span></a>
      </div>
   </div>
</section>
@endif
<!-- End Job Section -->

<!-- Job Categories -->
@if(isset($professions) && !empty($professions))
<section class="job-section-five secion-8">
   <div class="auto-container">
      <div class="sec-title text-center">
         <h2>Travel Nurse 911, the Preferred Choice for Top Nursing Talent</h2>
         <h6 class="text-muted mt-2">Join our travel nurse community and discover the leading job board for travel nurses</h6>
      </div>

      <div class="row wow fadeInUp">
         <?php foreach ($professions as $row) { ?>
            <div class="job-block col-lg-3 col-md-6 col-sm-12">
               <a href="{{ route('job-category', ['pid' => $row->id]) }}" class="h-100 d-inline-block w-100">
                  <div class="inner-box h-100 d-flex align-items-center justify-content-center">
                     <div class="content pl-0 text-center">
                        <h4>{{ $row->profession }}</h4>
                        <p>[ {{ $row->job_count}} job(s) ]</p>
                     </div>
                  </div>
               </a>
            </div>
         <?php } ?>
      </div>

      <div class="btn-box text-center mt-4">
         <a href="{{ route('job-categories') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Load More Categories</span></a>
      </div>
   </div>
</section>
@endif
<!-- End Job Categories -->

<style>
   /*
   .cards .news-block-two {
      position: sticky;
      top: 0;
   }

   .cards .inner-box {
      will-change: transform;
      background: white;
      border-radius: 14px;
      display: flex;
      overflow: hidden;
      box-shadow: 0 25px 50px -12px hsla(265.3, 20%, 10%, 35%);
      transform-origin: center top;
   }

   .cards {
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
      display: grid;
      grid-template-rows: repeat(var(--cards-count), var(--card-height));
      gap: 40px 0;
   }

   .cards .image-box {
      display: flex;
      width: 30%;
      flex-shrink: 0;
   }

   .cards .image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      aspect-ratio: 1;
   }

   .cards .content-box {
      padding: 40px 30px;
      display: flex;
      flex-direction: column;
   }

   .space {
      height: 0;
   }

   .space--small {
      height: 0;
   }

   @media (max-width: 600px) {
      .cards .inner-box {
         flex-direction: column;
      }

      .cards .image-box {
         width: 100%;
      }

      .cards .image img {
         aspect-ratio: 16 / 9;
      }

   }
   */
</style>



<section class="news-section secion-1 aat-card-container stack-cards js-stack-cards">
   <div class="auto-container">
      <div class="sec-title text-center">
         <h2 class="mb-2">Empowering your Travel Nurse Career</h2>
         <!--<h6 class="text-muted mt-2">Discover the ultimate platform for travel nurses to find job opportunities, manage tasks, and stay compliant. Join our community and take control of your nursing career.</h6>-->
      </div>

      <div class="cards">

         <div class="news-block-two mb-0 pt-0 stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
               <div class="content-box">
                  <h3 class="font-22">Top Paying jobs</h3>
                  <p class="text">
                     Discover top paying job opportunities with Travel Nurse 911, where we connect you with lucrative positions that offer exceptional compensation and benefits. Maximize your earning potential and advance your career with our exclusive job listings and personalized support
                  </p>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">
               <div class="content-box">
                  <h3>Priority Placements</h3>
                  <p class="text">
                     At Travel Nurse 911, we are committed to ensuring your success .We collaborate with top healthcare facilities worldwide, providing you with a seamless placement process and the assurance of finding the right fit. With our extensive network and personalized support, you can trust us to secure rewarding and fulfilling assignments, giving you the opportunity to focus on delivering exceptional services.
                  </p>
               </div>
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/interview-scheduling.avif') }}" />
                     -->
                  </figure>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
               <div class="content-box">
                  <h3>Easy Job Apply</h3>
                  <p class="text">
                     Experience hassle-free job applications with Travel Nurse 911. Our easy application process saves your time and effort. Focus on advancing your travel nursing career with ease and confidence.
                  </p>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">

               <div class="content-box">
                  <h3>Skill Checklists</h3>
                  <p class="text">
                     Assess your qualifications with detailed checklists, ensuring you meet job requirements and allowing employers to verify your skills quickly and effectively.
                  </p>
               </div>
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
               <div class="content-box">
                  <h3>Profile</h3>
                  <p class="text">
                     Our industry profile simplifies your job search by enabling swift applications to positions that catch your interest. By maintaining one comprehensive profile, you can easily connect with recruiters and tap into a wide range of job opportunities across various companies and industries.
                  </p>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">

               <div class="content-box">
                  <h3>Follow Ups</h3>
                  <p class="text">
                     Travel Nurse 911 provides timely updates and feedback, keeping travel nurses informed after initial interactions or placements. This approach enhances their experience and maintains strong engagement, ensuring they stay connected and well-informed throughout their assignments.
                  </p>
               </div>
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
               <div class="content-box">
                  <h3>Professional Recruitment Solutions</h3>
                  <p class="text">
                     At Travel Nurse 911, we specialize in healthcare recruitment, offering unmatched expertise in nurse staffing. Our services connect employers with highly qualified travel nurses, ensuring you access top-tier professionals tailored to your needs. We streamline the recruitment process, making it easier for you to find the right talent for your healthcare facility.
                  </p>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0 stack-cards__item js-stack-cards__item " data-index="0">
            <div class="inner-box">

               <div class="content-box">
                  <h3>Commitment to Quality Care</h3>
                  <p class="text">
                     Travel Nurse 911 is dedicated to delivering exceptional healthcare staffing solutions by recruiting highly qualified travel nurses. Our commitment to quality care is reflected in our meticulous matching process, ensuring that skilled professionals are placed in roles where they can significantly enhance the quality of care
                  </p>
               </div>
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
               <div class="content-box">
                  <h3>Support & Help </h3>
                  <p class="text">
                     Receive prompt assistance from our dedicated support team for any issues or queries. Access solutions to common questions and troubleshoot with ease through our detailed Travel Nurse 911 FAQs page.
                  </p>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">

               <div class="content-box">
                  <h3>Messaging Center</h3>
                  <p class="text">
                     Our messaging center enables seamless communication by allowing you to send messages to applicants and SMS notifications directly to their cell phones. This feature ensures quick replies, accelerating the hiring process and fostering efficient interactions for a smoother recruitment experience.
                  </p>
               </div>
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
               <div class="content-box">
                  <h3>Calendar Management</h3>
                  <p class="text">
                     Our comprehensive calendar management tools help you stay organized by managing tasks, confirming interviews, and tracking important dates such as birthdays. With these tools, you can ensure that all critical dates and appointments are not missed.
                  </p>
               </div>
            </div>
         </div>

         <div class="news-block-two mb-0 pt-0  stack-cards__item js-stack-cards__item" data-index="0">
            <div class="inner-box">

               <div class="content-box">
                  <h3>Reports & Analytics</h3>
                  <p class="text">
                     Our comprehensive reports and analytics allow you to assess both open and filled positions, providing valuable insights that enhance your recruiting strategies. With detailed data at your fingertips, you can make informed decisions and improve your recruitment processes, ensuring effective and efficient staffing.
                  </p>
               </div>
               <div class="image-box">
                  <figure class="image w-100">
                     <div class="embed-responsive embed-responsive-1by1">
                        <iframe class="embed-responsive-item" src="{{ asset('public/assets/sample.mp4') }}"></iframe>
                     </div>
                     <!--
                     <img src="{{ asset('public/assets/images/search-candidate.avif') }}" />
                     -->
                  </figure>
               </div>
            </div>
         </div>

      </div>
   </div>
</section>



<!-- Call To Action Two -->
<section class="call-to-action-two style-two secion-5">
   <div class="auto-container wow fadeInUp">
      <div class="sec-title light text-center">
         <h2 class="mb-4">Looking to Recruit?</h2>
         <h5 class="text-white">Explore our comprehensive talent acquisition services and join a network to find <br />exceptional travel nurses that are available for immediate placement.</h5>
         <div class="text">
            Join the Travel Nurse 911 community, where companies make great hires every day.
            Improve your hiring process now!

         </div>
      </div>

      <div class="btn-box">
         <a href="{{ route('contact-us') }}" class="theme-btn btn-style-three">Start Hiring</a>
      </div>
   </div>
</section>
<!-- End Call To Action -->

<!-- Prioritize Section -->
<section class="about-section secion-3">
   <div class="auto-container">
      <div class="row">
         <!-- Content Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12 order-2">
            <div class="inner-column wow fadeInRight pt-0">
               <div class="sec-title">
                  <h2>What makes us Unique?
                  </h2>
                  <div class="text mw-100">
                     Discover a world of opportunities with our extensive travel nursing job listings.
                  </div>
                  <div class="text mw-100">
                     Gain experience, explore new places, and advance your Travel Nurse career with the perfect position that meets your desires and fits your every need.
                  </div>
               </div>
               <ul class="list-style-one">
                  <li><strong>Personalized Job Matches</strong> : Get matched with jobs tailored to your skills, desires and preferences, ensuring you find the perfect fit for your career goals.
                  </li>
                  <li><strong>Quick CV Upload</strong> : Easily upload your resume in seconds and start applying for jobs immediately, streamlining the application process.</li>
                  <li><strong>Trusted Employers</strong> : Work with top-rated hospitals, agencies and healthcare facilities, ensuring you have reliable and reputable employment options.
                  </li>
                  <li><strong>24/7 Support</strong> : Round-the-clock support to assist you with your job search and application process, ensuring you have help whenever you need it.</li>
               </ul>
               <a href="{{ config('custom.user_job_url') }}" class="theme-btn btn-style-one">Explore Jobs</a>
            </div>
         </div>

         <!-- Image Column -->
         <div class="image-column col-lg-6 col-md-12 col-sm-12">
            <figure class="image-box wow fadeInLeft"><img src="{{ asset('public/assets/images/prioritize.avif') }}" /></figure>
         </div>
      </div>
   </div>
</section>
<!-- End Prioritize Section -->

<!-- Candidate to Employee Section -->
<section class="about-section secion-4">
   <div class="auto-container">
      <div class="row">
         <!-- Content Column -->
         <div class="col-lg-6 col-md-12 col-sm-12 order-2">
            <div class="inner-column">
               <figure class="image-box wow fadeInLeft">
                  <img src="{{ asset('public/assets/images/transform.avif') }}" />
               </figure>
            </div>
         </div>

         <!-- Image Column -->
         <div class="content-column col-lg-6 col-md-12 col-sm-12 wow fadeInRight">
            <div class="sec-title">
               <span class="sub-title">How We Benefit Employers</span>
               <h2>Effortlessly Discover Top Travel Nursing Talent</h2>
               <div class="text mw-100">
                  Streamline Your Search for Highly Qualified Travel Nurses within our Platform, Ensuring You Find the Best Professionals Quickly and Easily
               </div>
            </div>
            <ul class="list-style-one">
               <li><strong>Fast Access to Qualified Nurses
                  </strong> : Quickly connect with a pool of vetted, skilled travel nurses ready to step into roles and make an immediate impact on your recruitment goals.</li>
               <li><strong>Streamlined Hiring Process</strong> : Simplify your recruitment with our user-friendly platform, which minimizes administrative tasks and accelerates candidate selection.</li>
               <li><strong>Advanced Candidate Filtering</strong> : Efficiently identify top candidates using our advanced search and filtering tools, designed to enhance and accelerate your recruitment process.
               </li>
            </ul>
            <a href="{{ route('contact-us') }}" class="theme-btn btn-style-one">Hire Now</a>
         </div>
      </div>
   </div>
</section>
<!-- End Candidate to Employee Section -->



<!-- Candidate Feature Section -->
<!--
<section class="about-section secion-6">
   <div class="auto-container">
      <div class="row">
<div class="content-column col-lg-6 col-md-12 col-sm-12 order-2">
   <div class="inner-column wow fadeInUp">
      <div class="sec-title">
         <h2>Discover thousands of job opportunities. Your perfect match is waiting.</h2>
         <div class="text">
            Explore a multitude of job opportunities tailored to fit your skills and
            aspirations. Find your ideal career path with us!
         </div>
      </div>
      <ul class="list-style-one">
         <li>Explore Diverse Openings</li>
         <li>Tailor Your Search</li>
         <li>Seize Your Perfect Match</li>
      </ul>
      <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Get Started</span></a>
   </div>
</div>

<div class="image-column col-lg-6 col-md-12 col-sm-12">
   <figure class="image wow fadeInLeft"><img src="{{ asset('public/assets/images/job-serach.avif') }}" /></figure>
</div>
</div>
</div>
</section>
-->
<!-- End Candidate Feature Section -->


@if(isset($blogs) && !empty($blogs))

<style>
   .news-section .title,
   .news-section .description {
      display: -webkit-box;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
   }

   .news-section .title {
      -webkit-line-clamp: 2;
      /* Limit to 2 lines */
      line-clamp: 2;
   }

   .news-section .description {
      -webkit-line-clamp: 3;
      /* Limit to 3 lines */
      line-clamp: 3;
   }
</style>

<section class="news-section secion-9" style="background-color: #ECEDF2;">
   <div class="auto-container">
      <div class="sec-title text-center">
         <h2>Recent Blogs</h2>
         <div class="text">Fresh job related news content posted each day.</div>
      </div>

      <div class="row wow fadeInUp justify-content-center">
         <?php foreach ($blogs as $row) { ?>
            <div class="news-block col-lg-4 col-md-6 col-sm-12">
               <div class="inner-box" style="background-color: #ffffff;">
                  <span class="image-box">
                     <figure class="image">
                        <?php if (isset($row->profile_pic_path) && !empty($row->profile_pic_path)) { ?>
                           <img src="{{$row->profile_pic_path}}" />
                        <?php } ?>
                     </figure>
                  </span>

                  <div class="lower-content">
                     <h3 class="title"><a href="#">{{ $row->title }}</a></h3>
                     <p class="text description">{{ $row->short_description }}</p>
                     <a href="#" class="read-more">Read More <i class="fa fa-angle-right"></i></a>
                  </div>
               </div>
            </div>
         <?php } ?>

      </div>

      <div class="btn-box text-center">
         <a href="{{ route('blogs') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Load All Blogs</span></a>
      </div>

   </div>
</section>
@endif

<!-- End News Section -->

<!-- Call To Action -->
<section class="registeration-bannerss job-categories secion-10">
   <div class="auto-container">
      <div class="row wow fadeInUp">
         <!-- Banner Style One -->
         <div class="banner-style-one col-lg-6 col-md-12 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <h3>Looking for Your Next Great Hire?</h3>
                  <p>
                     Effortlessly find top Travel Nurse talent and enhance your team with our streamlined hiring solutions.

                  </p>
                  <a href="{{ route('contact-us') }}" class="theme-btn btn-style-five">Register Account</a>
               </div>
               <figure class="image"><img src="{{ asset('public/assets/images/hiring-manager.png') }}" /></figure>
            </div>
         </div>

         <!-- Banner Style Two -->
         <div class="banner-style-two col-lg-6 col-md-12 col-sm-12">
            <div class="inner-box">
               <div class="content">
                  <h3>Searching for the Ideal Job?</h3>
                  <p>
                     Start your career journey with us. Explore exciting travel nursing opportunities perfectly matched to your skills, desires and ambitions.

                  </p>
                  <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-five">Register Account</a>
               </div>
               <figure class="image"><img src="{{ asset('public/assets/images/candidate2.png') }}" /></figure>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- End Call To Action -->

<!-- /section -->
@endsection