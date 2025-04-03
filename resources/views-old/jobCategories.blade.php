<?php

use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Job Categories</h1>
         <ul class="page-breadcrumb">
            <li>One platform for all your hiring needs.</li>
         </ul>
      </div>
   </div>
</section>
<!--End Page Title-->

<!-- About Section Three -->
<section class="about-section-three">
   <div class="auto-container">

      <!-- Job Categories -->
      @if(isset($professions) && !empty($professions))
      <div class="row wow fadeInUp justify-content-center">
         <?php foreach ($professions as $row) { ?>
            <div class="category-block col-lg-3 col-md-6 col-sm-12">
               <a href="{{ route('job-category', ['pid' => $row->id]) }}" class="h-100 w-100 d-inline-block">
                  <div class="inner-box h-100">
                     <div class="content h-100">
                        <h4>{{ $row->profession }}</h4>
                        <p>[ {{ $row->job_count}} job(s) ]</p>
                     </div>
                  </div>
               </a>
            </div>
         <?php } ?>
      </div>
      @else
      <h4 class="text-center">No Job Category Available</h4>
      @endif
      <!-- End Job Categories -->

   </div>
</section>
<!-- End About Section Three -->



@endsection