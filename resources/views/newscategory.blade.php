<?php

use Carbon\Carbon;
use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('public/assets/css/dark-mode.css') }}" />
<style>
    .title,
    .description {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
    }

    .title {
        -webkit-line-clamp: 10;
        /* Limit to 10 lines */
        line-clamp: 10;
    }

    .description {
        -webkit-line-clamp: 10;
        /* Limit to 10 lines */
        line-clamp:10;
    }

    .modal-backdrop {
        z-index: 1049;
        /* Ensure it's just below the modal */
    }

    .modal {
        z-index: 1050;
        /* Ensure it's above the backdrop */
    }

    .modal-dialog {
        position: fixed;
        /* Ensures it stays in place relative to the viewport */
        top: 50%;
        left: 35%;
        transform: translate(-50%, -50%);
        width: 90%;
    }
    
    
    .image-box {
        width: 30% !important; /* Force width to 25% */
        margin-left:10px !important;
    }
    .news-block .inner-box {
        display: flex;
        align-items: center;
        gap: 15px; /* Adjust spacing between image and text */
    }
    .news-block .lower-content {
        flex: 1; /* Ensure the text takes the remaining space */
        text-align: left; /* Keep text alignment consistent */
    }
    
    .news-block p {
        line-height: 1.5;
        margin-bottom: 10px;
    }
    .news-block .title a {
        color: #007bff;
        text-decoration: none;
    }
    .news-block p {
        line-height: 1.5;
        margin-bottom: 10px;
    }
    .news-block .image-box img {
        height: 190px !important;
    }
    .search-category-section {
        position: sticky;
        top: 12%;
        max-height: max-content;
    }
    .subscribe-btn {
        padding: 10px 20px 10px 20px;
    }
    @media (max-width: 768px) { /* Target screens smaller than 768px */
        .news-block .inner-box {
            flex-direction: column;
        }
        .image-box {
            width: 100% !important; /* Force width to 25% */
            margin:0 !important;
        }
    }
    .select2-container--default .select2-selection--single {
        height: 48px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-size: 16px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b:before {
        top: 55%;
    }
</style>

<!--Page Title-->
<section class="page-title">
    <div class="auto-container">
        <div class="title-outer">
            <h1>News</h1>
            <ul class="page-breadcrumb">
                <li><a href="{{ route('news') }}">News Home</a></li>                
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->

<!-- Subscribe Modal -->
<div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscribeModalLabel">Subscribe</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="subscribeForm">
                    <div class="form-group">
                        <label for="emailInput">Email address</label>
                        <input type="email" class="form-control" id="emailInput" placeholder="Enter your email" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="subscribeButton">Subscribe</button>
            </div>
        </div>
    </div>
</div>

<section class="about-section-three">
    <div class="auto-container">
        <div class="content-side">
            <div class="row justify-content-center">
                
                <!-- Search Form for Mobile View -->
                <div class="col-lg-4 search-mobile-section d-block d-md-none">
                    <div class="mobile-topics d-block d-md-none mb-4">
                        <!-- Dropdown for Mobile -->
                        <h3>News Topics</h3>
                        <select id="newsCategoryDropdown" class="form-control d-block d-md-none">
                            @if ($categories->isNotEmpty())
                                <option value=""></option>
                                @foreach ($categories as $cat)
                                    <option  value="{{ route('particularcat', $cat->slug) }}"
                                        data-id="{{ $cat->id }}" @if($cat->id == $category->id) selected @endif>
                                        {{ $cat->title }} ({{ $cat->posts_count ?? 0 }})
                                    </option>
                                @endforeach
                            @else
                                <option disabled>No category available</option>
                            @endif
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <!-- Category Title -->
                    <div class="d-flex align-items-center mb-2" style="margin-left: 2%; margin-right: 2%;">
                            <a class="category-click" data-id="{{$category->id}}" 
                                href="javascript:void(0);" data-url="{{ route('particularcat', $category->slug) }}" style="font-weight: 500;font-size: 15pt;">
                                {{ $category->title }}
                            </a>
                            <a href="#" class="subscribe-btn theme-btn btn-style-one btn-sm ml-3" data-toggle="modal" data-target="#subscribeModal" data-category-title="{{ $category->title }}" data-category-id="{{ $category->id }}">
                                Subscribe
                            </a>
                    </div>

                    <!-- Loop through the posts -->
                    @foreach($posts as $post)
                    <div class="news-block col-lg-12 col-md-12 col-sm-12">
                        <div class="inner-box d-flex align-items-center">
                            @if(!empty($post->thumbnail))
                            <span class="image-box" style="width:100%">
                                <figure class="image">
                                    <a class="post-click"  data-id="{{ $post->id }}" data-url="{{ $post->is_external_url ? $post->slug : route('news-detail', $post->slug) }}" href="javascript:void(0);">
                                        <img width="100px" height="100px" src="{{ asset("public/uploads/news/".$post->thumbnail) }}" alt="{{ $post->title }}" />
                                    </a>

                                </figure>
                            </span>
                            @endif
                            <div class="lower-content ml-3">
                                @if(!empty($post->posted_date))
                                <span class="post-date">Posted On : {{ date('m/d/Y',strtotime($post->posted_date)) }}</span>
                                @endif
                                <h3 class="title">
                                    <a class="post-click"  data-id="{{ $post->id }}" data-url="{{ $post->is_external_url ? $post->slug : route('news-detail', $post->slug) }}" href="javascript:void(0);"  style="color:#007bff;">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                @php
                                    $content = strip_tags($post->content);
                                    $shortContent = strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                                @endphp

                                <p>
                                    {!! $shortContent !!}
                                    @if(strlen($post->content) > 10)
                                    <a href="{{ $post->is_external_url ? $post->slug : route('news-detail', $post->slug) }}" target="_blank">
                                        Read more
                                    </a>
                                    @endif

                                </p>


                                <!-- Tags -->
                                <!--<p class="tags">
                                    @foreach($post->tags as $tag)
                                    <a href="#" class="tag">{{ $tag->name }}</a>
                                    @endforeach
                                </p>-->
                                @if($post->tags->isNotEmpty())
                                    <p class="category">
                                        @foreach($post->tags as $tag)
                                            <a href="{{ route('news', ['tag' => $tag->name]) }}">{{ $tag->name }}</a>
                                        @endforeach
                                    </p>
                                @endif


                                <!-- Social Share Buttons -->
                                <div class="social-share-buttons mt-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url('news-detail', $post->slug) }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fab fa-facebook-f"></i> Share
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ url('news-detail', $post->slug) }}&text={{ $post->title }}" target="_blank" class="btn btn-info btn-sm ml-2">
                                        <i class="fab fa-twitter"></i> Share
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ url('news-detail', $post->slug) }}" target="_blank" class="btn btn-primary btn-sm ml-2">
                                        <i class="fab fa-linkedin-in"></i> Share
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach



                </div>

                <div class="col-lg-4 search-category-section">

                    <!-- News Categories -->
                     <!-- Static News Categories Section -->
                     <div class="news-categories mb-5 d-none d-md-block">
                        <h3>News Topics</h3>
                        <ul class="list-unstyled">
                            @if($categories->isNotEmpty())

                            @foreach($categories as $category)
                                <li> <a class="category-click" data-id="{{$category->id}}" 
                                href="javascript:void(0);" data-url="{{ route('particularcat', $category->slug) }}">{{ $category->title }} ({{ $category->posts_count ?? 0 }})</a></li>
                                
                            @endforeach
                            @else
                                <li>
                                    <a href="#">No category available</a>
                                </li>
                            @endif



                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Dark mode toggle click event
        $("body").on("click", "#dark-mode-function", function () {
            $("body").toggleClass("dark-mode-page"); // Add/Remove dark mode class
            // Toggle icon between moon and sun
            let icon = $(this).find("i");
            if (icon.hasClass("fa-moon")) {
                icon.removeClass("fa-solid fa-moon").addClass("fa-regular fa-lightbulb"); // Change to Sun icon
            } else {
                icon.removeClass("fa-regular fa-lightbulb").addClass("fa-solid fa-moon"); // Change to Moon icon
            }
        });

        // OPTIONAL: Check localStorage to persist dark mode across page refresh
        if (localStorage.getItem("darkMode") === "enabled") {
            $("body").addClass("dark-mode-page");
            $("#dark-mode-function i").removeClass("fa-solid fa-moon").addClass("fa-regular fa-lightbulb");
        }

        // Store user preference in localStorage
        $("body").on("click", "#dark-mode-function", function () {
            if ($("body").hasClass("dark-mode-page")) {
                localStorage.setItem("darkMode", "enabled");
            } else {
                localStorage.setItem("darkMode", "disabled");
            }
        });
        
        // Category clicks
        $(".category-click").on("click", function (e) {
            var category_id = $(this).data('id');
            var clickURL = $(this).data('url');
            trackClicked(category_id,clickURL,1);
            window.open(clickURL, '_blank');
        });
        // Post clicks
        $(".post-click").on("click", function (e) {
            var post_id = $(this).data('id');
            var clickURL = $(this).data('url');
            trackClicked(post_id,clickURL,2);
            window.open(clickURL, '_blank');
        });
        function trackClicked(click_id,clickURL,type) {
            // AJAX request
            $.ajax({
                url: "{{ route('track.news.clicks') }}", // Route to handle the request
                type: "GET",
                data: {
                    click_id: click_id,
                    type : type,
                },
                success: function (response) {
                    console.log(response)
                },
                error: function (xhr, status, error) {
                    console.log("AJAX error:", error);
                }
            });
        }
        $('#newsCategoryDropdown').select2({
            placeholder: "Select News Topics", 
            allowClear: true
        });
        $('#newsCategoryDropdown').on('change', function () {
            const selectedUrl = this.value;
            if (selectedUrl) {
                window.location.href = selectedUrl;
            }
        });
    });
</script>

@endsection