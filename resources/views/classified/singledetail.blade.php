<?php

use Carbon\Carbon;
use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')

<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    body {
        background-color: #f8f8f8;
    }

    .container {
        max-width: 90%;
        margin: 0 auto;
        background-color: #efeded;
    }

    .header {
        background-color: #008cfc !important;
        color: white;
        padding: 10px 15px 10px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-container {
        /* background-color: white; */
        padding: 10px;
        display: flex;
        justify-content: center;
    }

    .search-box {
        width: 100%;
        max-width: 100%;
    }

    .search-box input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .user-options {
        display: flex;
        gap: 10px;
        padding: 0px 18px 0px 0px;
    }

    /* .user-options a {
        padding: 8px 12px;
        color: #c7dcda;
        border: 1px solid #397be7;
        cursor: pointer;
        border-radius: 10px;
        text-decoration: none;
        font-weight: bold;
    } */

    .categories {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px;
        /* background-color: white; */
        /* justify-content: center; */
    }

    .categories a {
        padding: 6px 10px;
        border: none;
        background-color: #bfdbfe;
        border: 1px solid #397be7;
        /* color: white; */
        border-radius: 10px;
        cursor: pointer;
        text-decoration: none;
    }

    .categoriess a {
        background-color: #91fb94;
    }

    .listing {
        display: flex;
        flex-direction: column;
        gap: 15px;
        /* padding: 20px; */
        justify-content: start;
        /* max-width: 800px; */
        margin: 20px auto;
    }

    .listing-item {
        padding: 15px;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .listing-item img {
        width: 120px;
        height: 120px;
        border-radius: 8px;
    }

    .listing-item .details {
        flex-grow: 1;
    }

    .listing-item a {
        margin-bottom: 5px;
        font-size: 16px;
        color: #008cff;
        text-decoration: none;
    }

    .listing-item .green {
        color: #3aa652;
    }

    .listing-item p {
        font-size: 14px;
        color: #666;
    }

    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            text-align: center;
        }

        .search-box {
            margin-top: 10px;
        }

        .listing-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .listing-item img {
            width: 100%;
            height: auto;
        }
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    /* h4 {
        text-align: center;
        padding: 20px 0px 0px 0px;
    } */

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 8px;
        color: #333;
    }

    select,
    input,
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .two-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .two-columns .form-group {
        margin-bottom: 0;
    }

    .hidden {
        display: none;
    }

    .button-align {
        display: flex;
        justify-content: flex-end;
    }

    button {
        background: #008cff;
        color: white;
        border: none;
        padding: 12px;
        cursor: pointer;
        width: 25%;
        font-size: 18px;
        border-radius: 5px;
        /* margin-top: 20px; */
        /* text-align: end; */
    }

    button:hover {
        background: #0056b3;
    }

    textarea {
        height: 150px;
    }

    .form-group select {
        background-color: #fafafa;
        border: 1px solid #ddd;
        transition: border-color 0.3s ease;
    }

    .form-group select:focus {
        border-color: #007bff;
    }

    .swal2-actions {
        width: 530px;
    }

    @media (max-width: 768px) {
        .two-columns {
            grid-template-columns: 1fr;
        }
    }
</style>
<?php
/*
?>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    body {
        background-color: #f8f8f8;
    }

    .container {
        max-width: 90%;
        margin: 0 auto;
        
    }
    /*
    .header {
        background-color: #2d3e50;
        color: white;
        padding: 10px 15px 10px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-container {
        
        padding: 10px;
        display: flex;
        justify-content: center;
    }

    .search-box {
        width: 100%;
        max-width: 100%;
    }

    .search-box input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .user-options {
        display: flex;
        gap: 10px;
        padding: 0px 18px 0px 0px;
    }

    .user-options a {
        padding: 8px 12px;
        color: #c7dcda;
        
        cursor: pointer;
        border-radius: 10px;
        text-decoration: none;
        font-weight: bold;
    }

    .categories {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px;
        
    }

    .categories a {
        padding: 6px 10px;
        border: none;
        background-color: #bfdbfe;
        border: 1px solid #397be7;
       
        border-radius: 10px;
        cursor: pointer;
        text-decoration: none;
    }

    .categoriess a {
        background-color: #91fb94;
    }

    .listing {
        display: flex;
        flex-direction: column;
        gap: 15px;
     
        justify-content: start;
    
        margin: 20px auto;
    }

    .listing-item {
        padding: 15px;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .listing-item img {
        width: 120px;
        height: 120px;
        border-radius: 8px;
    }

    .listing-item .details {
        flex-grow: 1;
    }

    .listing-item a {
        margin-bottom: 5px;
        font-size: 16px;
        color: #008cff;
        text-decoration: none;
    }

    .listing-item .green {
        color: #3aa652;
    }

    .listing-item p {
        font-size: 14px;
        color: #666;
    }

    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            text-align: center;
        }

        .search-box {
            margin-top: 10px;
        }

        .listing-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .listing-item img {
            width: 100%;
            height: auto;
        }
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    h4 {
        text-align: center;
        padding: 20px 0px 0px 0px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 8px;
        color: #333;
    }

    select,
    input,
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .two-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .two-columns .form-group {
        margin-bottom: 0;
    }

    .hidden {
        display: none;
    }

    .button-align {
        display: flex;
        justify-content: flex-end;
    }

    button {
        background: #008cff;
        color: white;
        border: none;
        padding: 12px;
        cursor: pointer;
        width: 25%;
        font-size: 18px;
        border-radius: 5px;
        margin-top: 20px;
       
    }

    button:hover {
        background: #0056b3;
    }

    textarea {
        height: 150px;
    }

    .form-group select {
        background-color: #fafafa;
        border: 1px solid #ddd;
        transition: border-color 0.3s ease;
    }

    .form-group select:focus {
        border-color: #007bff;
    }

    @media (max-width: 768px) {
        .two-columns {
            grid-template-columns: 1fr;
        }
    }
</style>
<?php
*/
?>




<div class="container">
    <!-- Header -->
    <div class="header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
        <h3 class="m-0">Travel Nursing Industry Classifieds</h3>
        <div class="user-options">
            <!-- <a href="{{ route('classified.add')}}">Post Ad</a> -->
            <a href="{{ route('classified.add') }}" class="theme-btn btn-style-one">Post Ad</a>

            @if(Auth::check())
            <form action="{{route('auth.logout')}}" method="POST">
                @csrf
                <button type="submit" class="theme-btn btn-style-one" style="width:100%;border:none!important;">Logout</button>
            </form>
            @else
            <a href="{{ route('auth.user.login') }}" class="theme-btn btn-style-one" style="border:none;">Login</a>
            @endif
        </div>
    </div>
</div>
<div class="container my-4">
    <div class="row mt-2">
        <div class="col-md-6">
            <a href="{{ route('classified.index') }}" class="theme-btn btn-style-one" style="height: 26px;width: 10px;">Back</a>
        </div>
    </div>
    <div class="row">


        <div class="col-md-4">
            <div class="card shadow" style="min-height:960px;">

                <div class="card-body" style="min-height:630px;">
                    <ul class="list-group list-group-flush">

                        @if(!empty($classifiedAds->marketplace->title))

                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">Marketplace:</div>
                                <div class="col-8 text-end">{{ ucfirst($classifiedAds->marketplace->title) ?? 'N/A' }}</div>
                            </div>
                        </li>


                        @endif



                        @if(!empty($classifiedAds->city->city_name))

                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">City:</div>
                                <div class="col-8 text-end">{{ ucfirst($classifiedAds->city->city_name) ?? 'N/A' }}</div>
                            </div>
                        </li>


                        @endif

                        @if(!empty($classifiedAds->state->name))

                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">State:</div>
                                <div class="col-8 text-end">{{ ucfirst($classifiedAds->state->name) ?? 'N/A' }}</div>
                            </div>
                        </li>


                        @endif


                        @if(!empty($classifiedAds->price) && ( $classifiedAds->marketplace->title == "Traveler Housing & Lodging" || $classifiedAds->marketplace->title == "Apparel & Gifts" || $classifiedAds->marketplace->title == "Agency Services" ))
                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">
                                    @if($classifiedAds->marketplace->title == "Traveler Housing & Lodging") 
                                    {{" Rental Price:"}}   
                                    @endif

                                    @if($classifiedAds->marketplace->title == "Apparel & Gifts") 
                                    {{"Price:"}}   
                                    @endif

                                    @if($classifiedAds->marketplace->title == "Agency Services") 
                                    {{"Price:"}}   
                                    @endif

                                   
                                </div>
                                <div class="col-8 text-end">{{ "$".$classifiedAds->price ." ".ucfirst($classifiedAds->price_type) ?? 'N/A' }}</div>
                            </div>
                        </li>
                        @endif


                        @if(!empty($classifiedAds->pets_allowed))

                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">Pets Allowed:</div>
                                <div class="col-8 text-end">{{ ucfirst($classifiedAds->pets_allowed) ?? 'N/A' }}</div>
                            </div>
                        </li>

                        @endif

                        @if(!empty($classifiedAds->bedrooms))

                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">Bedrooms:</div>
                                <div class="col-8 text-end">{{ ucfirst($classifiedAds->bedrooms) ?? 'N/A' }}</div>
                            </div>
                        </li>



                        @endif

                        @if(!empty($classifiedAds->service_type))


                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;padding: 0px 10px">Service Type:</div>
                                <div class="col-8 text-end">{{ ucfirst($classifiedAds->service_type) ?? 'N/A' }}</div>
                            </div>
                        </li>


                        @endif

                        @if(!empty($classifiedAds->name))



                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">Name:</div>
                                <div class="col-8 text-end">{{ ucfirst($classifiedAds->name) ?? 'N/A' }}</div>
                            </div>
                        </li>


                        @endif

                        @if(!empty($classifiedAds->phone))




                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">Phone:</div>
                                <div class="col-8 text-end">{{ $classifiedAds->phone ? ltrim($classifiedAds->phone, '+') : 'N/A' }}</div>
                            </div>
                        </li>



                        @endif

                        @if(!empty($classifiedAds->email) && $classifiedAds->marketplace->title != "Mental Health Resources")

                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">Email:</div>
                                <div class="col-8 text-end">{{ ucfirst($classifiedAds->email) ?? 'N/A' }}</div>
                            </div>
                        </li>



                        @endif

                        @if(!empty($classifiedAds->created_at))


                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 128px;font-weight:bold;">Posted Date:</div>
                                <div class="col-8 text-end">{{ $classifiedAds->created_at->format('F d, Y') }}</div>
                            </div>
                        </li>

                        @endif


                        @if(!empty($classifiedAds->website))
                        <li class="list-group-item" style="padding:15px 0px;">
                            <div class="row">
                                <div class="col-4 text-start" style="min-width: 110px;font-weight:bold;">Website:</div>
                                <div class="col-8 text-end text-truncate" style="max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;padding:0px 22px">
                                    @if($classifiedAds->website)
                                    <a href="{{ $classifiedAds->website }}" target="_blank" class="text-break">{{ $classifiedAds->website }}</a>
                                    @else
                                    N/A
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endif



                    </ul>
                </div>




                <!-- Social Share Section -->
                <div class="card-footer text-center">
                    <h6 class="mb-2">Share This Ad</h6>
                    <div class="d-flex justify-content-center gap-2">
                        <!-- Facebook Share Button -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{route('classified.singledetail', ['slug' => $classifiedAds->slug]) }}" target="_blank" class="btn btn-primary btn-sm ml-2">
                            <i class="fab fa-facebook-f"></i> Share
                        </a>

                        <!-- Twitter Share Button -->
                        <a href="https://twitter.com/intent/tweet?url={{route('classified.singledetail', ['slug' => $classifiedAds->slug]) }}&text={{ urlencode($classifiedAds->title) }}" target="_blank" class="btn btn-info btn-sm ml-2">
                            <i class="fab fa-twitter"></i> Share
                        </a>

                        <!-- LinkedIn Share Button -->
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{route('classified.singledetail', ['slug' => $classifiedAds->slug]) }}" target="_blank" class="btn btn-primary btn-sm ml-2">
                            <i class="fab fa-linkedin-in"></i> Share
                        </a>
                    </div>
                </div>
            </div>
        </div>




        <div class="col-md-8">
            <div class="card shadow mb-3" style="min-height:965px">
                <div class="row g-0">
                    <!-- Image Section -->
                    @if(!empty($classifiedAds->thumbnail))
                    <div class="col-md-4 p-3 d-flex align-items-center">
                        <img src="{{ asset('public/uploads/marketplace/' . $classifiedAds->thumbnail) }}"
                            class="img-fluid rounded"
                            alt="Listing Image"
                            style="width:100%; max-height: 100%; object-fit: cover;">
                    </div>
                    @endif

                    <!-- Details Section -->
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ $classifiedAds->title }}</h5>

                            <!-- Scrollable Description Box -->
                            @if(!empty($classifiedAds->description))
                            <div class="content-box p-3 border rounded bg-light" style="max-height: 200px; overflow-y: auto;">
                                <!-- Scrollable Description Box -->

                                <p class="card-text">{{ $classifiedAds->description ?? '' }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>


                <!-- Contact Seller Form -->
                <div class="card-body">
                    <h5 class="card-header bg-primary text-white p-2 rounded">Contact Us</h5>

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button style="background-color:#3aa652;width: 3%;padding: 0;" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                    </div>
                    @endif


                    <form action="{{ route('classified.contact_seller')}}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="classified_id" value="{{ $classifiedAds->id }}">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="contact_seller" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label fw-bold">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Send Message</button>
                    </form>
                </div>
            </div>
        </div>





    </div>
</div>
</div>











<script>
    $(document).ready(function() {
        $(".btn-close").click(function() {
            $(this).closest(".alert").fadeOut("slow");
        });



        function readURL(input) {
            if (input.files && input.files[0] && input.files[0].type.includes("image")) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#thumbnailpreview').removeClass("d-none");
                    $('#thumbnailpreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                $("#thumbnail").val('');
                $('#thumbnailpreview').addClass("d-none");
                Swal.fire({
                    icon: "error",
                    text: "Select a valid image!"
                });
            }
        }
        $("#thumbnail").change(function() {
            readURL(this);
        });




    });
</script>

<!-- End About Section Three -->



@endsection