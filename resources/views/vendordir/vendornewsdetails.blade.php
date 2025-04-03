@extends('layouts.app')

@section('content')
<style>
    .card {
        height: auto !important;
    }
</style>

<section class="page-title">
    <div class="auto-container">
        <div class="title-outer">
            <h1>Travel Nurse Vendor Directory</h1>
            <ul class="page-breadcrumb">
                <li><a href="{{ route('vendorcategory') }}">Vendors</a></li>
                <li><a href="{{ route('vendorDetails', Crypt::encrypt($vendor->id)) }}">{{ $vendor->company_name }}</a></li>
                <li><a href="{{ route('vendorPressReleases',  $vendor->id) }}">News</a></li>
            </ul>
        </div>
    </div>
</section>

<div class="container py-4">
    <div class="card position-relative">
        <div class="card-body">
            <div class="row">              
                <div class="col-md-12">
                    <h2 class="h4 mb-2">{{ $release->title }}</h2>
                    <p class="text-muted">{!! $release->desc !!}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection