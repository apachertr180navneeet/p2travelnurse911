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
                <h1>Browse jobs</h1>
                <ul class="page-breadcrumb">
                    <li>Discover Your Next Career Move: Explore Exciting Opportunities at Travel Nurse 911</li>
                </ul>
            </div>
        </div>
    </section>
    <!--End Page Title-->

    <!-- Listing Section -->
    <section class="ls-section">
        <div class="auto-container">

            <div class="filters-backdrop"></div>
            <!-- Filters Column -->
            <div class="filters-column hide-left">
                <div class="inner-column" style="background-color:#f5f7fc;">
                    <div class="filters-outer">
                        <button type="button" class="theme-btn close-filters">X</button>
                        <form method="GET">
                            <input type="hidden" name="type" value="listing">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                            <input type="hidden" name="page" value="{{ request('page', 1) }}">
                            <!-- Filter Block -->
                            <div class="filter-block">
                                <h4>Search by Keywords</h4>
                                <div class="form-group">
                                    <input type="text" name="keyword" placeholder="Job title, keywords, or company"
                                        value="{{ request('keyword') }}">
                                    <span class="icon flaticon-search-3"></span>
                                </div>
                            </div>

                            <!-- Filter Block -->
                            <div class="filter-block">
                                <h4>Location</h4>
                                <div class="form-group">
                                    <input type="text" name="location" placeholder="City or State"
                                        value="{{ request('location') }}">
                                    <span class="icon flaticon-map-locator"></span>
                                </div>
                            </div>

                            <!-- Filter Block -->
                            <div class="filter-block">
                                <h4>Employement Types</h4>
                                <div class="form-group">
                                    <select class="chosen-select" name="empType">
                                        <option value="">Choose a Type</option>
                                        @foreach ($empTypes as $empType)
                                            <option value="{{ $empType->id }}"
                                                {{ request('empType') == $empType->id ? 'selected' : '' }}>
                                                {{ $empType->title }}</option>
                                        @endforeach
                                    </select>
                                    <span class="icon flaticon-briefcase"></span>
                                </div>
                            </div>

                            <!-- Filter Block -->
                            <div class="filter-block">
                                <h4>Order By</h4>
                                <div class="form-group">
                                    <select class="chosen-select" name="orderBy">
                                        <option value="default">Default</option>
                                        <option value="highest" {{ request('orderBy') == 'highest' ? 'selected' : '' }}>
                                            Highest Pay First</option>
                                        <option value="lowest" {{ request('orderBy') == 'lowest' ? 'selected' : '' }}>Lowest
                                            Pay First</option>
                                    </select>
                                    <span class="icon flaticon-order"></span>
                                </div>
                            </div>

                            <!-- Switchbox Outer -->
                            <!--
                         <div class="switchbox-outer">
                            <h4>Job Type</h4>
                            <ul class="switchbox">
                               <li>
                                  <label class="switch">
                                     <input type="checkbox" checked>
                                     <span class="slider round"></span>
                                     <span class="title">Freelance</span>
                                  </label>
                               </li>
                               <li>
                                  <label class="switch">
                                     <input type="checkbox">
                                     <span class="slider round"></span>
                                     <span class="title">Full Time</span>
                                  </label>
                               </li>
                               <li>
                                  <label class="switch">
                                     <input type="checkbox">
                                     <span class="slider round"></span>
                                     <span class="title">Internship</span>
                                  </label>
                               </li>
                               <li>
                                  <label class="switch">
                                     <input type="checkbox">
                                     <span class="slider round"></span>
                                     <span class="title">Part Time</span>
                                  </label>
                               </li>
                               <li>
                                  <label class="switch">
                                     <input type="checkbox">
                                     <span class="slider round"></span>
                                     <span class="title">Temporary</span>
                                  </label>
                               </li>
                            </ul>
                         </div>
                         -->

                            <!-- Filter Block -->
                            <div class="">
                                <input type="submit" value="Apply Filter" class="theme-btn btn-style-one btn-block">

                                <a href="{{ route('jobs-search') }}?type=listing" class="btn btn-secondary btn-block"
                                    style="padding-top:10px; padding-bottom:10px;padding:18px 35px 15px 35px; border-radius: 8px;font-size: 15px;line-height: 20px;">Reset</a>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="default-tabs tabs-box">

                <!--
             <ul class="tab-buttons justify-content-center">
                <li class="tab-btn <?php if ((isset($_GET['type']) && $_GET['type'] == 'heatmap') || !isset($_GET['type'])) {
                    echo 'active-btn';
                } ?>" data-tab="#heatMap" data-type="heatmap">Heat Map</li>
                <li class="tab-btn <?php if (isset($_GET['type']) && $_GET['type'] == 'listing') {
                    echo 'active-btn';
                } ?>" data-tab="#listing" data-type="listing">Listing</li>
             </ul>
             -->

                <div class="tabs-content">
                    <div class="tab <?php if (isset($_GET['type']) && $_GET['type'] == 'listing') {
                        echo 'active-tab';
                    } ?> active-tab" id="listing">

                        <div class="row">



                            <!-- Content Column -->
                            <div class="content-column col-lg-12">
                                <div class="ls-outer">
                                    <!-- ls Switcher -->
                                    <div class="ls-switcher">
                                        <div class="showing-result show-filters">
                                            <button type="button" class="theme-btn toggle-filters"><span
                                                    class="icon icon-filter"></span> Filter</button>
                                            <div class="text">Showing
                                                <strong>{{ $jobs->firstItem() }}-{{ $jobs->lastItem() }}</strong> of
                                                <strong>{{ $jobs->total() }}</strong> jobs</div>
                                        </div>
                                        <!--
                      <div class="sort-by">
                         <select class="chosen-select">
                            <option>New Jobs</option>
                            <option>Freelance</option>
                            <option>Full Time</option>
                            <option>Internship</option>
                            <option>Part Time</option>
                            <option>Temporary</option>
                         </select>

                         <select class="chosen-select">
                            <option>Show 10</option>
                            <option>Show 20</option>
                            <option>Show 30</option>
                            <option>Show 40</option>
                            <option>Show 50</option>
                            <option>Show 60</option>
                         </select>
                      </div>
                      -->

                                        <!-- Per Page Selection -->
                                        <form method="GET">
                                            <div class="sort-by">
                                                <select name="per_page" onchange="this.form.submit()"
                                                    class="chosen-select">
                                                    <option value="10"
                                                        {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page
                                                    </option>
                                                    <option value="20"
                                                        {{ request('per_page') == 20 ? 'selected' : '' }}>20 per page
                                                    </option>
                                                    <option value="30"
                                                        {{ request('per_page') == 30 ? 'selected' : '' }}>30 per page
                                                    </option>
                                                    <option value="40"
                                                        {{ request('per_page') == 40 ? 'selected' : '' }}>40 per page
                                                    </option>
                                                    <option value="50"
                                                        {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page
                                                    </option>
                                                </select>
                                                @foreach (request()->except('per_page', 'page') as $key => $value)
                                                    <input type="hidden" name="{{ $key }}"
                                                        value="{{ $value }}">
                                                @endforeach
                                            </div>
                                        </form>
                                    </div>

                                    <div class="row mb-5">

                                        @if (isset($jobs) && !empty($jobs) && $jobs->total() > 0)
                                            <?php foreach ($jobs as $row) { ?>
                                            <div class="job-block col-lg-6 col-md-12 col-sm-12">
                                                <a href="{{ route('job', $row->unique_id) }}" class="job-outer-link">
                                                    <div class="inner-box h-100 job-inner-box">
                                                        <div class="content pl-0">
                                                            <?php if (isset($row->profile_pic_path) && !empty($row->profile_pic_path) && 0) { ?>
                                                            <span class="company-logo">
                                                                <img src="{{ $row->profile_pic_path }}" />
                                                            </span>
                                                            <?php } ?>

                                                            <?php
                                          if (isset($row->compnay_role_id) && $row->compnay_role_id == 1) {
                                          ?>
                                                            <span class="company-name">Travel Nurse 911</span>
                                                            <?php
                                          } else if (isset($row->company_name) && !empty($row->company_name)) {
                                          ?>
                                                            <span class="company-name">{{ $row->company_name }}</span>
                                                            <?php
                                          } ?>
                                                            <h4 class="text-capitalize">{{ $row->title }}</h4>
                                                            <ul class="job-info">
                                                                <li><span class="icon flaticon-map-locator"></span>
                                                                    {{ $row->city_name }}, {{ $row->state_code }}</li>

                                                                <?php
                                                                $givenTime = Carbon::parse($row->created_at);
                                                                $currentTime = Carbon::now();
                                                                $timeDifference = $givenTime->diffForHumans($currentTime);
                                                                $timeDifference = str_replace('before', 'ago', $timeDifference);
                                                                ?>
                                                                <li><span class="icon flaticon-clock-3"></span>
                                                                    {{ $timeDifference }}</li>
                                                                <?php
                                             if ($row->show_pay_rate == 0) {
                                             ?>
                                                                <li><span class="icon flaticon-money"></span>
                                                                    <span class="job-inner-link"
                                                                        data-nav_url="{{ config('custom.login_url') }}"
                                                                        title="Contact Us">Contact Us</span>
                                                                </li>
                                                                <?php
                                             } elseif (isset($row->salary_start_range) && !empty($row->salary_start_range)) { ?>
                                                                <li><span class="icon flaticon-money"></span>
                                                                    {{ '$' . number_format($row->salary_start_range) . ' ' . $row->salary_type }}
                                                                </li>
                                                                <?php }  ?>
                                                            </ul>
                                                            <ul class="job-other-info">
                                                                <?php if (isset($row->total_opening) && $row->total_opening > 0) { ?>
                                                                <li class="time">{{ $row->total_opening }} Opening(s)
                                                                </li>
                                                                <?php } ?>
                                                                <li class="privacy">{{ $row->profession }}</li>
                                                                <li class="required">{{ $row->specialty }}</li>
                                                                <?php if (isset($row->shift_title) && !empty($row->shift_title)) { ?>
                                                                <li class="bg-info text-white">{{ $row->shift_title }}
                                                                    Shift</li>
                                                                <?php } ?>
                                                            </ul>
                                                            <!--<button class="bookmark-btn"><span class="flaticon-bookmark"></span></button>-->
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <?php } ?>
                                        @else
                                            <div class="col-md-12 mb-5">
                                                <h4 class="text-center">No job found related to your search criteria</h4>
                                            </div>
                                        @endif

                                    </div>

                                    <?php if ($jobs->total() > $jobs->perPage()) { ?>
                                    <nav class="ls-pagination mb-5">
                                        <ul class="pagination">
                                            <!-- Previous Page Link -->
                                            @if ($jobs->onFirstPage())
                                                <li class="prev"><a class="disabled" href="#"><i
                                                            class="fa fa-arrow-left"></i></a></li>
                                            @else
                                                <li class="prev"><a
                                                        href="{{ $jobs->appends(request()->except('page'))->previousPageUrl() }}"
                                                        rel="prev"><i class="fa fa-arrow-left"></i></a></li>
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
                                                            <li><a
                                                                    href="{{ $jobs->appends(request()->except('page'))->url($page) }}">{{ $page }}</a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach

                                            <!-- Next Page Link -->
                                            @if ($jobs->hasMorePages())
                                                <li class="text"><a
                                                        href="{{ $jobs->appends(request()->except('page'))->nextPageUrl() }}"
                                                        rel="next"><i class="fa fa-arrow-right"></i></a></li>
                                            @else
                                                <li class="text"><a class="disabled" href="#"><i
                                                            class="fa fa-arrow-right"></i></a></li>
                                            @endif
                                        </ul>
                                    </nav>
                                    <?php } ?>

                                    <!-- Call To Action -->
                                    <div class="call-to-action-four style-two">
                                        <h5>Recruiting?</h5>
                                        <p>Advertise your jobs to thousands of active users.</p>
                                        <a href="{{ route('pilot-partner-signup') }}"
                                            class="theme-btn btn-style-one bg-blue"><span class="btn-title">Start
                                                Recruiting Now</span></a>
                                        <div class="image"
                                            style="background-image: url({{ asset('public/assets/images/resource/image-1.png') }});">
                                        </div>
                                    </div>
                                    <!-- End Call To Action -->
                                </div>
                            </div>

                        </div>
                    </div>

                    <!--
                <div class="tab <?php if ((isset($_GET['type']) && $_GET['type'] == 'heatmap') || !isset($_GET['type'])) {
                    echo 'active-tab';
                } ?>" id="heatMap">
                   <div id="map" style="height:500px;"></div>
                </div>
                -->
                </div>
            </div>
        </div>
    </section>
    <!--End Listing Page Section -->

    <script>
        $(document).ready(function() {
            /*
            $('.tab-btn').on('click', function() {
               var type = $(this).data('type');
               var url = new URL(window.location.href);
               url.searchParams.set('type', type);
               window.history.pushState({}, '', url);
               window.location.reload();
            });
            */
        });

        var jobLocations = [
            <?php
            if (isset($jobs) && !empty($jobs) && $jobs->total() > 0) {
                foreach ($jobs as $row) {
                    echo "
                        {
                           city: '" .
                        $row->city_name .
                        "',
                           state: '" .
                        $row->state_name .
                        "',
                           country: 'USA'
                        },";
                }
            } ?>
        ];

        function initializeHeatMap() {

            // Define bounds for the USA
            var southWest = L.latLng(24.396308, -125.0); // Southwest corner of the USA
            var northEast = L.latLng(49.384358, -66.93457); // Northeast corner of the USA
            var bounds = L.latLngBounds(southWest, northEast);

            var testData = {
                max: 8,
                data: [{
                    lat: 24.6408,
                    lng: 46.7728,
                    count: 1
                }, {
                    lat: 50.75,
                    lng: -1.55,
                    count: 1
                }, {
                    lat: 52.6333,
                    lng: 1.75,
                    count: 1
                }, {
                    lat: 48.15,
                    lng: 9.4667,
                    count: 1
                }, {
                    lat: 52.35,
                    lng: 4.9167,
                    count: 1
                }, {
                    lat: 60.8,
                    lng: 11.1,
                    count: 1
                }, {
                    lat: 43.561,
                    lng: -116.214,
                    count: 1
                }, {
                    lat: 47.5036,
                    lng: -94.685,
                    count: 1
                }, {
                    lat: 42.1818,
                    lng: -71.1962,
                    count: 1
                }, {
                    lat: 42.0477,
                    lng: -74.1227,
                    count: 1
                }, {
                    lat: 40.0326,
                    lng: -75.719,
                    count: 1
                }, {
                    lat: 40.7128,
                    lng: -73.2962,
                    count: 1
                }, {
                    lat: 27.9003,
                    lng: -82.3024,
                    count: 1
                }, {
                    lat: 38.2085,
                    lng: -85.6918,
                    count: 1
                }, {
                    lat: 46.8159,
                    lng: -100.706,
                    count: 1
                }, {
                    lat: 30.5449,
                    lng: -90.8083,
                    count: 1
                }, {
                    lat: 44.735,
                    lng: -89.61,
                    count: 1
                }, {
                    lat: 41.4201,
                    lng: -75.6485,
                    count: 1
                }, {
                    lat: 39.4209,
                    lng: -74.4977,
                    count: 1
                }, {
                    lat: 39.7437,
                    lng: -104.979,
                    count: 1
                }, {
                    lat: 39.5593,
                    lng: -105.006,
                    count: 1
                }, {
                    lat: 45.2673,
                    lng: -93.0196,
                    count: 1
                }, {
                    lat: 41.1215,
                    lng: -89.4635,
                    count: 1
                }, {
                    lat: 43.4314,
                    lng: -83.9784,
                    count: 1
                }, {
                    lat: 43.7279,
                    lng: -86.284,
                    count: 1
                }, {
                    lat: 40.7168,
                    lng: -73.9861,
                    count: 1
                }, {
                    lat: 47.7294,
                    lng: -116.757,
                    count: 1
                }, {
                    lat: 47.7294,
                    lng: -116.757,
                    count: 1
                }, {
                    lat: 35.5498,
                    lng: -118.917,
                    count: 1
                }, {
                    lat: 34.1568,
                    lng: -118.523,
                    count: 1
                }, {
                    lat: 39.501,
                    lng: -87.3919,
                    count: 1
                }, {
                    lat: 33.5586,
                    lng: -112.095,
                    count: 1
                }, {
                    lat: 38.757,
                    lng: -77.1487,
                    count: 1
                }, {
                    lat: 33.223,
                    lng: -117.107,
                    count: 1
                }, {
                    lat: 30.2316,
                    lng: -85.502,
                    count: 1
                }, {
                    lat: 39.1703,
                    lng: -75.5456,
                    count: 8
                }, {
                    lat: 30.0041,
                    lng: -95.2984,
                    count: 1
                }, {
                    lat: 29.7755,
                    lng: -95.4152,
                    count: 1
                }, {
                    lat: 41.8014,
                    lng: -87.6005,
                    count: 1
                }, {
                    lat: 37.8754,
                    lng: -121.687,
                    count: 7
                }, {
                    lat: 38.4493,
                    lng: -122.709,
                    count: 1
                }, {
                    lat: 40.5494,
                    lng: -89.6252,
                    count: 1
                }, {
                    lat: 42.6105,
                    lng: -71.2306,
                    count: 1
                }, {
                    lat: 40.0973,
                    lng: -85.671,
                    count: 1
                }, {
                    lat: 40.3987,
                    lng: -86.8642,
                    count: 1
                }, {
                    lat: 40.4224,
                    lng: -86.8031,
                    count: 4
                }, {
                    lat: 47.2166,
                    lng: -122.451,
                    count: 1
                }, {
                    lat: 32.2369,
                    lng: -110.956,
                    count: 1
                }, {
                    lat: 41.3969,
                    lng: -87.3274,
                    count: 1
                }, {
                    lat: 41.7364,
                    lng: -89.7043,
                    count: 1
                }, {
                    lat: 42.3425,
                    lng: -71.0677,
                    count: 1
                }, {
                    lat: 33.8042,
                    lng: -83.8893,
                    count: 1
                }, {
                    lat: 36.6859,
                    lng: -121.629,
                    count: 1
                }, {
                    lat: 41.0957,
                    lng: -80.5052,
                    count: 1
                }, {
                    lat: 46.8841,
                    lng: -123.995,
                    count: 1
                }, {
                    lat: 40.2851,
                    lng: -75.9523,
                    count: 1
                }, {
                    lat: 42.4235,
                    lng: -85.3992,
                    count: 1
                }, {
                    lat: 39.7437,
                    lng: -104.979,
                    count: 1
                }, {
                    lat: 25.6586,
                    lng: -80.3568,
                    count: 7
                }, {
                    lat: 33.0975,
                    lng: -80.1753,
                    count: 1
                }, {
                    lat: 25.7615,
                    lng: -80.2939,
                    count: 1
                }, {
                    lat: 26.3739,
                    lng: -80.1468,
                    count: 1
                }, {
                    lat: 37.6454,
                    lng: -84.8171,
                    count: 1
                }, {
                    lat: 34.2321,
                    lng: -77.8835,
                    count: 1
                }, {
                    lat: 34.6774,
                    lng: -82.928,
                    count: 1
                }, {
                    lat: 39.9744,
                    lng: -86.0779,
                    count: 1
                }, {
                    lat: 35.6784,
                    lng: -97.4944,
                    count: 1
                }, {
                    lat: 33.5547,
                    lng: -84.1872,
                    count: 1
                }, {
                    lat: 27.2498,
                    lng: -80.3797,
                    count: 1
                }, {
                    lat: 41.4789,
                    lng: -81.6473,
                    count: 1
                }, {
                    lat: 41.813,
                    lng: -87.7134,
                    count: 1
                }, {
                    lat: 41.8917,
                    lng: -87.9359,
                    count: 1
                }, {
                    lat: 35.0911,
                    lng: -89.651,
                    count: 1
                }, {
                    lat: 32.6102,
                    lng: -117.03,
                    count: 1
                }, {
                    lat: 41.758,
                    lng: -72.7444,
                    count: 1
                }, {
                    lat: 39.8062,
                    lng: -86.1407,
                    count: 1
                }, {
                    lat: 41.872,
                    lng: -88.1662,
                    count: 1
                }, {
                    lat: 34.1404,
                    lng: -81.3369,
                    count: 1
                }, {
                    lat: 46.15,
                    lng: -60.1667,
                    count: 1
                }, {
                    lat: 36.0679,
                    lng: -86.7194,
                    count: 1
                }, {
                    lat: 43.45,
                    lng: -80.5,
                    count: 1
                }, {
                    lat: 44.3833,
                    lng: -79.7,
                    count: 1
                }, {
                    lat: 45.4167,
                    lng: -75.7,
                    count: 1
                }, {
                    lat: 43.75,
                    lng: -79.2,
                    count: 1
                }, {
                    lat: 45.2667,
                    lng: -66.0667,
                    count: 1
                }, {
                    lat: 42.9833,
                    lng: -81.25,
                    count: 1
                }, {
                    lat: 44.25,
                    lng: -79.4667,
                    count: 1
                }, {
                    lat: 45.2667,
                    lng: -66.0667,
                    count: 1
                }, {
                    lat: 34.3667,
                    lng: -118.478,
                    count: 1
                }, {
                    lat: 42.734,
                    lng: -87.8211,
                    count: 1
                }, {
                    lat: 39.9738,
                    lng: -86.1765,
                    count: 1
                }, {
                    lat: 33.7438,
                    lng: -117.866,
                    count: 1
                }, {
                    lat: 37.5741,
                    lng: -122.321,
                    count: 1
                }, {
                    lat: 42.2843,
                    lng: -85.2293,
                    count: 1
                }, {
                    lat: 34.6574,
                    lng: -92.5295,
                    count: 1
                }, {
                    lat: 41.4881,
                    lng: -87.4424,
                    count: 1
                }, {
                    lat: 25.72,
                    lng: -80.2707,
                    count: 1
                }, {
                    lat: 34.5873,
                    lng: -118.245,
                    count: 1
                }, {
                    lat: 35.8278,
                    lng: -78.6421,
                    count: 1
                }]
            };

            var baseLayer = L.tileLayer(
                'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
                    maxZoom: 18,
                    minZoom: 4
                }
            );

            var cfg = {
                // radius should be small ONLY if scaleRadius is true (or small radius is intended)
                "radius": 2,
                "maxOpacity": .8,
                // scales the radius based on map zoom
                "scaleRadius": true,
                // if set to false the heatmap uses the global maximum for colorization
                // if activated: uses the data maximum within the current map boundaries 
                //   (there will always be a red spot with useLocalExtremas true)
                "useLocalExtrema": true,
                // which field name in your data represents the latitude - default "lat"
                latField: 'lat',
                // which field name in your data represents the longitude - default "lng"
                lngField: 'lng',
                // which field name in your data represents the data value - default "value"
                valueField: 'count',
                gradient: {
                    // Custom gradient for more vibrant colors
                    '.1': 'cyan',
                    '.3': 'cyan',
                    '.5': 'cyan',
                    '.7': 'cyan',
                    '1': 'cyan'
                }
            };


            var heatmapLayer = new HeatmapOverlay(cfg);

            var map = new L.Map('map', {
                center: new L.LatLng(37.8, -96),
                zoom: 4,
                maxBounds: bounds, // Restrict map to USA bounds
                maxBoundsViscosity: 1.0, // Makes the bounds strict
                layers: [baseLayer, heatmapLayer],
                zoomControl: true, // Disable zoom control
                dragging: true, // Disable dragging
                scrollWheelZoom: true, // Disable scroll wheel zoom
                doubleClickZoom: true, // Disable double click zoom
                boxZoom: true, // Disable box zoom
                keyboard: true, // Disable keyboard navigation
            });

            heatmapLayer.setData(testData);

            // make accessible for debugging
            layer = heatmapLayer;
        };

        $(document).ready(function() {
            /*initializeHeatMap();*/
        });
        /*
        document.addEventListener('DOMContentLoaded', function() {
           // Event listener for the Heat Map tab
           document.querySelector('li[data-tab="#heatMap"]').addEventListener('click', function(e) {

              // Initialize the heatmap only if it hasn't been initialized already
              if (!document.querySelector('#map').classList.contains('initialized')) {
                 initializeHeatMap();
                 document.querySelector('#map').classList.add('initialized');
              }
           });
        });
        */
    </script>
@endsection
