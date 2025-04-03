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
         <h1>{{ $row->title }}</h1>
         <ul class="page-breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('blogs') }}">Blogs</a></li>
            <li>{{ $row->title  }}</li>
         </ul>
      </div>
   </div>

</section>
<!--End Page Title-->

<!-- About Section Three -->
<section class="about-section-three">
   <div class="small-container">

      <?php if (isset($row->image_path) && !empty($row->image_path)) { ?>
         <figure class="main-image"><img src="{{$row->image_path}}" alt=""></figure>
      <?php } ?>

      <div class="main-description">
         {!! $row->description !!}
      </div>

      @if($previousBlog || $nextBlog)
      <div class="post-control border-top mt-5 pt-3 border-bottom-0">
         @if($previousBlog)
         <div class="prev-post float-left mr-auto">
            <span class="icon flaticon-back"></span>
            <span class="title">Previous Post</span>
            <h5><a href="{{ url('blog/' . $previousBlog->slug) }}">{{ $previousBlog->title }}</a></h5>
         </div>
         @endif

         @if($nextBlog)
         <div class="next-post float-right ml-auto">
            <span class="icon flaticon-next"></span>
            <span class="title">Next Post</span>
            <h5><a href="{{ url('blog/' . $nextBlog->slug) }}">{{ $nextBlog->title }}</a></h5>
         </div>
         @endif
      </div>
      @endif

   </div>
</section>

@endsection