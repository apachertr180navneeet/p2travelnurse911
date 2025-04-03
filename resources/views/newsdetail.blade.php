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
         <h1>{{ $data->title }}</h1>
         <ul class="page-breadcrumb">
            <li><a href="{{ route('news') }}">News Home</a></li>            
            <li>{{ $data->title  }}</li>
         </ul>
      </div>
   </div>

</section>
<!--End Page Title-->

<!-- About Section Three -->
<section class="about-section-three">
   <div class="small-container">

   <div class="title-outer">
         <!-- <h3>{{ $data->title }}</h3> -->
         <!-- <ul class="page-breadcrumb">
            <li><a href="{{ route('news') }}">News Home</a></li>            
            <li>{{ $data->title  }}</li>
         </ul> -->
      </div>

      <?php if (isset($data->thumbnail) && !empty($data->thumbnail)) { ?>
         <figure class="main-image">
         <img  src="{{ asset("public/uploads/news/".$data->thumbnail) }}" alt="{{ $data->title }}" style="width:100%" />
         </figure>
      <?php } ?>

      <div class="main-description">
         {!! $data->content !!}
      </div>

      
   </div>
</section>

@endsection