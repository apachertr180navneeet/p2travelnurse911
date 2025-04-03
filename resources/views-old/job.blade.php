<?php

use Carbon\Carbon;
use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')


<!-- Job Detail Section -->
<section class="job-detail-section">
   <!-- Upper Box -->
   <div class="upper-box">
      <div class="auto-container">
         <!-- Job Block -->
         <div class="job-block-seven">
            <div class="inner-box">
               <div class="content pl-0">
                  <?php if (isset($row->profile_pic_path) && !empty($row->profile_pic_path) && 0) { ?>
                     <span class="company-logo">
                        <img src="{{$row->profile_pic_path}}" />
                     </span>
                  <?php } ?>

                  <?php
                  if (isset($row->compnay_role_id) && $row->compnay_role_id == 1) {
                  ?>
                     <?php
                     $company_name = DB::table('app_settings')
                        ->select('field_value')
                        ->where(['field_name' => 'app_name'])
                        ->where('field_value', '!=', NULL)
                        ->first()
                     ?>
                     <?php if (isset($company_name) && !empty($company_name)) { ?>
                        <span class="company-name">{{ $company_name->field_value }}</span>
                     <?php } ?>
                  <?php
                  } else if (isset($row->company_name) && !empty($row->company_name)) {
                  ?>
                     <span class="company-name">{{ $row->company_name }}</span>
                  <?php
                  } ?>
                  <h4 class="text-capitalize">{{ $row->title }}</h4>

                  <ul class="job-info">
                     <li><span class="icon flaticon-map-locator"></span> {{ $row->city_name}}, {{ $row->state_name}}</li>

                     <?php
                     $givenTime = Carbon::parse($row->created_at);
                     $currentTime = Carbon::now();
                     $timeDifference = $givenTime->diffForHumans($currentTime);
                     ?>
                     <li><span class="icon flaticon-clock-3"></span> {{ $timeDifference }}</li>

                     <?php if (isset($row->salary_start_range) && !empty($row->salary_start_range)) { ?>
                        <li><span class="icon flaticon-money"></span> ${{ $row->salary_start_range }} {{ $row->salary_type }}</li>
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
               </div>

               <div class="btn-box">
                  <a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-one">Apply For Job</a>
                  <a href="{{ config('custom.login_url') }}" class="bookmark-btn"><i class="flaticon-bookmark"></i></a>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="job-detail-outer">
      <div class="auto-container">
         <div class="row">
            <div class="content-column col-lg-8 col-md-12 col-sm-12">
               <div class="job-detail">
                  <?php if (isset($row->description) && !empty($row->description)) { ?>
                     <h4>Job Description</h4>
                     <div class="mb-5">
                        {!! $row->description !!}
                     </div>
                  <?php } ?>

                  <?php if (isset($row->qualification) && !empty($row->qualification)) { ?>
                     <h4>Qualification</h4>
                     <div class="mb-5">
                        {{ $row->qualification }}
                     </div>
                  <?php } ?>

                  <?php if (isset($row->responsibilities) && !empty($row->descrresponsibilitiesiption)) { ?>
                     <h4>Key Responsibilities</h4>
                     <div class="mb-5">
                        {{ $row->responsibilities }}
                     </div>
                  <?php } ?>
               </div>
            </div>

            <div class="sidebar-column col-lg-4 col-md-12 col-sm-12">
               <aside class="sidebar">
                  <div class="sidebar-widget">
                     <!-- Job Overview -->
                     <h4 class="widget-title">Job Overview</h4>
                     <div class="widget-content">
                        <ul class="job-overview">
                           <li>
                              <i class="icon icon-calendar"></i>
                              <h5>Date Posted:</h5>
                              <span>{{ $timeDifference }}</span>
                           </li>

                           <?php if (isset($row->end_date) && !empty($row->end_date)) { ?>
                              <li>
                                 <i class="icon icon-expiry"></i>
                                 <h5>Expiration date:</h5>
                                 <span>{{ \Carbon\Carbon::parse($row->end_date)->format('m/d/Y') }}</span>
                              </li>
                           <?php } ?>

                           <li>
                              <i class="icon icon-location"></i>
                              <h5>Location:</h5>
                              <span>{{ $row->city_name}}, {{ $row->state_name}}</span>
                           </li>
                           <li>
                              <i class="icon icon-user-2"></i>
                              <h5>Job Title:</h5>
                              <span>{{ $row->title}}</span>
                           </li>
                           <?php if (isset($row->min_work_per_week) && !empty($row->min_work_per_week)) { ?>
                              <li>
                                 <i class="icon icon-clock"></i>
                                 <h5>Hours:</h5>
                                 <span>{{ $row->min_work_per_week }}</span>
                              </li>
                           <?php } ?>


                        </ul>
                     </div>
                  </div>

                  <?php
                  if (isset($row->compnay_role_id) && $row->compnay_role_id == 1) {
                  ?>
                     <div class="sidebar-widget company-widget">
                        <h4 class="widget-title">Company Details</h4>
                        <div class="widget-content">


                           <ul class="company-info">
                              <?php
                              $company_name = DB::table('app_settings')
                                 ->select('field_value')
                                 ->where(['field_name' => 'app_name'])
                                 ->where('field_value', '!=', NULL)
                                 ->first()
                              ?>
                              <?php if (isset($company_name) && !empty($company_name)) { ?>
                                 <li>Company Name : <span>{{ $company_name->field_value }}</span>
                                 </li>
                              <?php } ?>

                              <?php
                              $company_email = DB::table('app_settings')
                                 ->select('field_value')
                                 ->where(['field_name' => 'company_email'])
                                 ->where('field_value', '!=', NULL)
                                 ->first()
                              ?>
                              <?php if (isset($company_email) && !empty($company_email)) { ?>
                                 <li>Email : <span>{{ $company_email->field_value }}</span>
                                 </li>
                              <?php } ?>

                              <?php
                              $company_phone = DB::table('app_settings')
                                 ->select('field_value')
                                 ->where(['field_name' => 'company_phone'])
                                 ->where('field_value', '!=', NULL)
                                 ->first()
                              ?>
                              <?php if (isset($company_phone) && !empty($company_phone)) { ?>
                                 <li>Phone : <span>{{ $company_phone->field_value }}</span>
                                 </li>
                              <?php } ?>


                              <?php
                              $company_address = DB::table('app_settings')
                                 ->select('field_value')
                                 ->where(['field_name' => 'company_address'])
                                 ->where('field_value', '!=', NULL)
                                 ->first()
                              ?>
                              <?php if (isset($company_address) && !empty($company_address)) { ?>
                                 <li>Location : <span>{{ $company_address->field_value }}</span>
                                 </li>
                              <?php } ?>

                              <?php
                              $company_website = DB::table('app_settings')
                                 ->select('field_value')
                                 ->where(['field_name' => 'company_website'])
                                 ->where('field_value', '!=', NULL)
                                 ->first()
                              ?>
                              <?php if (isset($company_website) && !empty($company_website)) { ?>
                                 <li>Website : <span>{{ $company_website->field_value }}</span>
                                 </li>
                              <?php } ?>

                           </ul>

                           <div class="btn-box"><a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-three">View Employer Profile</a></div>
                        </div>
                     </div>
                  <?php
                  } else {
                  ?>
                     <div class="sidebar-widget company-widget">
                        <h4 class="widget-title">Company Details</h4>
                        <div class="widget-content">

                           <!--
                        <div class="company-title pl-0">
                           <div class="company-logo"><img src="images/logo1.jpeg" alt=""></div>
                           <h5 class="company-name text-center">{{ $row->company_name}}</h5>
                           <a href="./employer-profile.html" class="profile-link">View hospital profile</a>
                        </div>
                        -->

                           <ul class="company-info">
                              <li>Company Name : <span>{{ $row->company_name}}</span></li>
                              <li>Primary Industry : <span>{{ $row->primary_industry}}</span></li>
                              <li>Company size : <span>{{ $row->company_size}}</span></li>
                              <li>Founded in : <span>{{ $row->founded_in}}</span></li>
                              <li>Phone : <span>{{ $row->phone}}</span></li>
                              <li>Email : <span>{{ $row->email}}</span></li>
                              <li>Location : <span>{{ $row->client_city_name}}, {{ $row->client_state_name}}</span></li>
                              <?php if (isset($row->social_media_links) && !empty($row->social_media_links)) {
                                 $social_links = json_decode($row->social_media_links, true);
                              ?>
                                 <li>Social media :
                                    <div class="social-links">
                                       <?php
                                       foreach ($social_links as $k => $v) {

                                          if ($v['platform'] == 'Facebook')
                                             $v['platform'] = 'facebook-f';
                                          else if ($v['platform'] == 'Twitter')
                                             $v['platform'] = 'twitter';
                                          else if ($v['platform'] == 'Instagram')
                                             $v['platform'] = 'instagram';
                                          else if ($v['platform'] == 'Linkedin')
                                             $v['platform'] = 'linkedin-in';
                                       ?>
                                          <a href="{{ $v['url'] }}"><i class="fab fa-{{ $v['platform'] }}"></i></a>
                                       <?php
                                       }
                                       ?>
                                    </div>
                                 </li>
                              <?php
                              } ?>

                           </ul>

                           <div class="btn-box"><a href="{{ config('custom.login_url') }}" class="theme-btn btn-style-three">View Employer Profile</a></div>
                        </div>
                     </div>
                  <?php } ?>
               </aside>
            </div>
         </div>


         @if(isset($relatedJobs) && !empty($relatedJobs))
         <div class="related-jobs">
            <div class="title-box">
               <h3>Related Jobs</h3>
            </div>
            <div class="row">
               <?php foreach ($relatedJobs as $row) { ?>
                  <div class="job-block col-lg-6 col-md-12 col-sm-12">
                     <div class="inner-box">
                        <div class="content pl-0">
                           <?php if (isset($row->profile_pic_path) && !empty($row->profile_pic_path) && 0) { ?>
                              <span class="company-logo">
                                 <img src="{{$row->profile_pic_path}}" />
                              </span>
                           <?php } ?>

                           <?php
                           if (isset($row->compnay_role_id) && $row->compnay_role_id == 1) {
                           ?>
                              <?php
                              $company_name = DB::table('app_settings')
                                 ->select('field_value')
                                 ->where(['field_name' => 'app_name'])
                                 ->where('field_value', '!=', NULL)
                                 ->first()
                              ?>
                              <?php if (isset($company_name) && !empty($company_name)) { ?>
                                 <span class="company-name">{{ $company_name->field_value }}</span>
                              <?php } ?>
                           <?php
                           } else if (isset($row->company_name) && !empty($row->company_name)) {
                           ?>
                              <span class="company-name">{{ $row->company_name }}</span>
                           <?php
                           } ?>
                           <h4 class="text-capitalize"><a href="{{ route('job',$row->unique_id) }}">{{ $row->title }}</a></h4>
                           <ul class="job-info">
                              <li><span class="icon flaticon-map-locator"></span> {{ $row->city_name}}, {{ $row->state_name}}</li>

                              <?php
                              $givenTime = Carbon::parse($row->created_at);
                              $currentTime = Carbon::now();
                              $timeDifference = $givenTime->diffForHumans($currentTime);
                              ?>
                              <li><span class="icon flaticon-clock-3"></span> {{ $timeDifference }}</li>

                              <?php if (isset($row->salary_start_range) && !empty($row->salary_start_range)) { ?>
                                 <li><span class="icon flaticon-money"></span> ${{ $row->salary_start_range }} {{ $row->salary_type }}</li>
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
         </div>
         @endif

      </div>
   </div>
</section>
<!-- End Job Detail Section -->



@endsection