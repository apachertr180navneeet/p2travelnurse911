<?php

use Carbon\Carbon;
use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Available Opportunities</h1>
         <ul class="page-breadcrumb">
            <li>Discover Your Next Career Move: Explore Exciting Opportunities at Travel Nurse 911</li>
         </ul>
      </div>
   </div>
</section>
<!--End Page Title-->

<!-- About Section Three -->
<section class="about-section-three">
   <div class="auto-container">

      <!-- Job Section -->
      @if(isset($jobs) && !empty($jobs) && $jobs->total() > 0)
      <div class="ls-switcher">
         <div class="showing-result show-filters">
            <div class="text text-right">Showing <strong>{{ $jobs->firstItem() }}-{{ $jobs->lastItem() }}</strong> of <strong>{{ $jobs->total() }}</strong> jobs</div>
         </div>
      </div>
      <div class="row wow fadeInUp">
         <?php foreach ($jobs as $row) { ?>
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
      @else
      <h4 class="text-center">No Job Available</h4>
      @endif
      <!-- End Job Section -->


      <?php if ($jobs->hasMorePages()) { ?>
         <nav class="ls-pagination mb-5">
            <ul class="pagination">
               <!-- Previous Page Link -->
               @if ($jobs->onFirstPage())
               <li class="prev"><a class="disabled" href="#"><i class="fa fa-arrow-left"></i></a></li>
               @else
               <li class="prev"><a href="{{ $jobs->previousPageUrl() }}" rel="prev"><i class="fa fa-arrow-left"></i></a></li>
               @endif

               <!-- Pagination Elements -->
               @foreach ($jobs->links()->elements as $element)
               <!-- Make three dots -->
               @if (is_string($element))
               <li><a class="disabled">{{ $element }}</a></li>
               @endif

               <!-- Array Of Links -->
               @if (is_array($element))
               @foreach ($element as $page => $url)
               @if ($page == $jobs->currentPage())
               <li><a class="current-page">{{ $page }}</a></li>
               @else
               <li><a href="{{ $url }}">{{ $page }}</a></li>
               @endif
               @endforeach
               @endif
               @endforeach

               <!-- Next Page Link -->
               @if ($jobs->hasMorePages())
               <li class="text"><a href="{{ $jobs->nextPageUrl() }}" rel="next"><i class="fa fa-arrow-right"></i></a></li>
               @else
               <li class="text"><a class="disabled" href="#"><i class="fa fa-arrow-right"></i></a></li>
               @endif
            </ul>
         </nav>
      <?php } ?>

   </div>
</section>
<!-- End About Section Three -->



@endsection