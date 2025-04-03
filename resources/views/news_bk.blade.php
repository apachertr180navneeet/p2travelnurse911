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
</style>

<!--Page Title-->
<section class="page-title">
    <div class="auto-container">
        <div class="title-outer">
            <h1>News</h1>
            <ul class="page-breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>News</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->

<!-- About Section Three -->
<section class="about-section-three">
    <div class="auto-container">

        @if($categories->isNotEmpty())

        <div class="content-side">

            <div class="row justify-content-center">

                <div class="col-lg-8">


                    @foreach ($recentposts as $category)

                    <!-- Section Title -->
                    <div class="d-flex align-items-center justify-content-between" style="margin-left: 2%;margin-right: 2%;">

                        <h3 class="title" style="font-weight: 500;font-size: 15pt;"><a href="#">{{ $category->title }}</a></h3>
                        <div class="button-group">
                            <a href="#" class="btn btn-secondary btn-sm ml-3">Subscribe</a>
                            <a href="#" class="btn btn-primary btn-sm ml-2">Share</a>
                        </div>
                    </div>


                    @php
                    $posts = $category->posts()
                    ->with('tags') // Eager load tags
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);
                    @endphp
                    @if($posts->isNotEmpty())
                    @foreach ($posts as $post)

                    <!-- Static News Block 1 -->
                    <div class="news-block col-lg-12 col-md-6 col-sm-12">
                        <div class="inner-box d-flex align-items-center">
                            @if(!empty($post->thumbnail))
                            <span class="image-box" style="width:100%">
                                <figure class="image">
                                    <a href="{{ route("news-detail", $post->slug) }}">
                                        <img width="100px" height="100px" src="{{ asset("public/uploads/news/".$post->thumbnail) }}" alt="{{ $post->title }}" />
                                    </a>

                                </figure>
                            </span>
                            @endif

                            <div class="lower-content ml-3">
                                <!-- Source Information -->
                                <h3 class="title"><a href="{{ route("news-detail", $post->slug) }}" style="color:#007bff;">{{ $post->title }}</a></h3>

                                @php
                                $content = strip_tags($post->content);
                                $shortContent = strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                                @endphp

                                <p>
                                    {!! $shortContent !!}
                                    @if(strlen($content) > 100)
                                    <a href="{{ route("news-detail", $post->slug) }}">Read more</a>
                                    @endif

                                </p>

                                </p>
                                <!-- Category Link -->
                                <p class="category">
                                    @if($post->tags->isNotEmpty())

                                    @foreach($post->tags as $tag)
                                    <a href="#">{{ $tag->name }}</a>
                                    @endforeach
                                </p>
                                @endif


                            </div>
                        </div>
                    </div>


                    @endforeach
                    <!-- Pagination Links for Posts -->
                    {{ $posts->links() }}
                    @else
                    <p>No posts available in this category.</p>
                    @endif
                    @endforeach

                    <!-- Pagination Links for Categories -->
                    {{ $recentposts->links() }}






























                    <!-- Static News Block 2 -->

                </div>


                <div class="col-lg-4">
                    <div class="search">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" value="">
                        </div>
                    </div>
                    <!-- Static News Categories Section -->
                    <div class="news-categories mb-5">
                        <h3>News Categories</h3>
                        <ul class="list-unstyled">
                            @if($categories->isNotEmpty())

                                @foreach($categories as $category)
                                    <li><a href="#">{{ $category->title }}</a></li>
                                @endforeach
                            @else
                            <li>
                                <a href="#">No category available</a>
                            </li>
                            @endif



                        </ul>
                    </div>

                     <!-- Static Traveler Market Place Section -->
                     <div class="news-categories">
                        <h3>Traveler Marketplace:</h3>
                        <ul class="list-unstyled">
                            @if($marketplaces->isNotEmpty())

                                @foreach($marketplaces as $marketplace)
                                    <li><a href="#">{{ $marketplace->title }}</a></li>
                                @endforeach
                            @else
                            <li>
                                <a href="#">No marketplace available</a>
                            </li>
                            @endif



                        </ul>
                    </div>


                </div>



            </div>



        </div>






    </div>
    </div>
    @else
    <h4 class="text-center">No News Available</h4>
    @endif
    <!-- End Job Section -->




    </div>
</section>
<!-- End About Section Three -->



@endsection