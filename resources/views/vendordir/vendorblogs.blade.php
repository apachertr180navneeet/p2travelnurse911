@extends('layouts.app')

@section('content')

<style>
    /* Card Styling */
    .blog-card {
        border-radius: 10px;
        transition: transform 0.2s ease-in-out;
        background: #fff;
        padding: 20px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    .blog-card:hover {
        transform: scale(1.02);
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        object-fit: cover;
        border-radius: 10px;
    }
</style>

<section class="page-title">
    <div class="auto-container">
        <div class="title-outer">
            <h1>Travel Nurse Vendor Directory</h1>
            <ul class="page-breadcrumb">
                <li><a href="{{ route('vendorcategory') }}">Vendors</a></li>
                <li><a href="{{ route('vendorDetails',['id' =>  Crypt::encrypt($vendor->id) ])  }}">{{ $vendor->company_name }}</a></li>
                <li> White Papers</li>
            </ul>
        </div>
    </div>
</section>

<div class="container">
    <h3 class="my-4"> White Papers by {{ $vendor->company_name }}</h3>
    @if ($blogs->isEmpty())
        <p class="text-center">No white papers found for this vendor.</p>
    @else
        @foreach ($blogs as $blog)
            <!-- Blog Card -->
            <div class="blog-card mb-4">
                <div class="row g-3 align-items-center">
                    <!-- Blog Image -->
                    <div class="col-md-3 text-center">
                        @if(!empty($blog->logo))
                            <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">
                                <img src="{{ asset('public/uploads/vendoragency_blog/' . $blog->logo) }}" 
                                    class="product-image img-fluid" 
                                    alt="{{ $blog->title }}" width="180">
                            </a>
                        @else
                            <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">
                                   <img src="https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg" width="180" height="180" alt="No Image Available" class="product-image img-fluid">
                            </a>
                        @endif
                    </div>

                    <!-- Blog Details -->
                    <div class="col-md-9">
                        <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">
                        <h5 class="fw-bold">{{ $blog->title }}</h5>
                        </a>
                        <p class="text-muted">{!! Str::limit($blog->desc, 150, '...') !!}</p>
                        <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">
                            Read more >>
                        </a>
                    </div>
                </div>
            </div>
            <!-- End of Blog Card -->
        @endforeach
    @endif
</div>
@endsection
