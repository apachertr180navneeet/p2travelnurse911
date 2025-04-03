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
                <li><a href="{{ route('vendorBlogs',  $vendor->id) }}"> White Papers</a></li>
            </ul>
        </div>
    </div>
</section>

<div class="container py-4">
    <div class="card position-relative">
        <div class="card-body">
            <div class="row">
                <!-- Company Logo -->
                <div class="col-md-2  mb-3 mb-md-0">
                    @if(!empty($blog->logo))
                    <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">
                        <img src="{{ asset('public/uploads/vendoragency_blog/' . $blog->logo) }}"
                            class="blog-image img-fluid"
                            alt="{{ $blog->blog_title }}">
                    </a>
                    @else
                    <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg" width="180" height="180" alt="No Image Available" class="blog-image img-fluid">
                    </a>
                    @endif
                </div>
                <!-- Company Info -->
                <div class="col-md-9">
                    <h2 class="h3 mb-2">{{ $blog->title }}</h2>
                    <p class="text-muted">{!! $blog->desc !!}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection