<?php

use Carbon\Carbon;
use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<style>
   .title,
   .description {
      display: -webkit-box;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
   }

   .title {
      -webkit-line-clamp: 2;
      /* Limit to 2 lines */
      line-clamp: 2;
   }

   .description {
      -webkit-line-clamp: 3;
      /* Limit to 3 lines */
      line-clamp: 3;
   }
    .news-block .image-box img {
    width: 100%; /* Ensure the image spans the full width */
    height: 300px; /* Stretch the image to fill the height */
}
</style>

<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Blogs</h1>
         <ul class="page-breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>Blog</li>
         </ul>
      </div>
   </div>
</section>
<!--End Page Title-->

<!-- About Section Three -->
<section class="about-section-three">
   <div class="auto-container">

      @if(isset($blogs) && !empty($blogs) && $blogs->total() > 0)
      <div class="ls-switcher">
         <div class="showing-result show-filters">
            <div class="text text-right">Showing <strong>{{ $blogs->firstItem() }}-{{ $blogs->lastItem() }}</strong> of <strong>{{ $blogs->total() }}</strong> blogs</div>
         </div>
      </div>
      <div class="content-side">
         <div class="blog-grid">
            <div class="row justify-content-center">
               <?php foreach ($blogs as $row) { ?>
                  <div class="news-block col-lg-4 col-md-6 col-sm-12">
                     <div class="inner-box">
                        <span class="image-box">
                           <figure class="image">
                              <?php if (isset($row->profile_pic_path) && !empty($row->profile_pic_path)) { ?>
                                 <img src="{{$row->profile_pic_path}}" />
                              <?php } ?>
                           </figure>
                        </span>

                        <div class="lower-content">
                           <h3 class="title"><a href="{{ route('blog',$row->slug) }}">{{ $row->title }}</a></h3>
                           <p class="text description">{{ $row->short_description }}</p>
                           <a href="{{ route('blog',$row->slug) }}" class="read-more">Read More <i class="fa fa-angle-right"></i></a>
                        </div>
                     </div>
                  </div>
               <?php } ?>
            </div>
         </div>
      </div>
      @else
      <h4 class="text-center">No Blog Available</h4>
      @endif
      <!-- End Job Section -->


      <?php if ($blogs->total() > $perPage) { ?>
         <nav class="ls-pagination mb-5">
            <ul class="pagination">
               <!-- Previous Page Link -->
               @if ($blogs->onFirstPage())
               <li class="prev"><a class="disabled" href="#"><i class="fa fa-arrow-left"></i></a></li>
               @else
               <li class="prev"><a href="{{ $blogs->previousPageUrl() }}" rel="prev"><i class="fa fa-arrow-left"></i></a></li>
               @endif

               <!-- Pagination Elements -->
               @foreach ($blogs->links()->elements as $element)
               <!-- Make three dots -->
               @if (is_string($element))
               <li><a class="disabled">{{ $element }}</a></li>
               @endif

               <!-- Array Of Links -->
               @if (is_array($element))
               @foreach ($element as $page => $url)
               @if ($page == $blogs->currentPage())
               <li><a class="current-page">{{ $page }}</a></li>
               @else
               <li><a href="{{ $url }}">{{ $page }}</a></li>
               @endif
               @endforeach
               @endif
               @endforeach

               <!-- Next Page Link -->
               @if ($blogs->hasMorePages())
               <li class="text"><a href="{{ $blogs->nextPageUrl() }}" rel="next"><i class="fa fa-arrow-right"></i></a></li>
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