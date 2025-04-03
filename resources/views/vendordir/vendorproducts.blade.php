@extends('layouts.app')

@section('content')

<style>
    /* Card Styling */
    .product-card {
        border-radius: 10px;
        transition: transform 0.2s ease-in-out;
        background: #fff;
        padding: 20px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    .product-card:hover {
        transform: scale(1.02);
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
    }

    /* Product Image */
    .product-image {
        max-height: 150px;
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
                <li><a href="{{ route('vendorDetails', ['id' =>  Crypt::encrypt($vendor->id) ]) }}">{{ $vendor->company_name }}</a></li>
                <li>Products</li>
            </ul>
        </div>
    </div>
</section>

<div class="container">
    <h3 class="my-4">Products by {{ $vendor->company_name }}</h3>
    @if ($products->isEmpty())
        <p class="text-center">No products found for this vendor.</p>
    @else
        @foreach ($products as $product)
            <!-- Product Card -->
            <div class="product-card mb-4">
                <div class="row g-3 align-items-center">
                    <!-- Product Image -->
                    <div class="col-md-3 text-center">
                        @if(!empty($product->logo))
                        <a href="{{ route('vendorProducts.details', ['id' =>  Crypt::encrypt($product->id) ]) }}">
                            <img src="{{ asset('public/uploads/vendoragency_product/' . $product->logo) }}" 
                                class="product-image img-fluid" 
                                alt="{{ $product->product_title }}">
                        </a>
                        @else                                
                        <a href="{{ route('vendorProducts.details', ['id' =>  Crypt::encrypt($product->id) ]) }}">                      
                            <img src="https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg" width="180" height="180" alt="No Image Available" class="product-image img-fluid">
                        </a>
                        @endif                   
                    </div>

                    <!-- Product Details -->
                    <div class="col-md-9">
                        <a href="{{ route('vendorProducts.details', ['id' =>  Crypt::encrypt($product->id) ]) }}">       
                            <h5 class="fw-bold">{{ $product->product_title }}</h5>
                        </a>
                        <p class="text-muted">{!! Str::limit($product->desc, 150, '...') !!}</p>
                        <a href="{{ route('vendorProducts.details', ['id' =>  Crypt::encrypt($product->id) ]) }}">
                            Read more >>
                        </a>
                    </div>
                </div>
            </div>
            <!-- End of Product Card -->
        @endforeach
    @endif
</div>
@endsection
