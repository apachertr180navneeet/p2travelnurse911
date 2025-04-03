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
        background-color: #008cfc;
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


<div class="container" style="padding:0px;">
    <div class="header">
        <h3 style="color:#c7dcda">Travel Nursing Industry Classifieds</h3>
        <div class="user-options">
            <!-- <a href="{{ route('classified.add')}}">Post Ad</a> -->
            <a href="{{ route('classified.add') }}" class="theme-btn btn-style-one">Post Ad</a>

            @if(Auth::check())
            <form action="{{route('auth.logout')}}" method="POST">
                @csrf
                <button type="submit" class="theme-btn btn-style-one" style="width:100%">Logout</button>
            </form>
            @else
            <a href="{{ route('auth.user.login') }}" class="theme-btn btn-style-one">Login</a>
            @endif
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button style="background-color:#3aa652;width: 3%;padding: 0;" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button style="background-color:red;width: 3%;padding: 0;" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button style="background-color:red;width: 3%;padding: 0;" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
    </div>
    @endif




</div>

<div class="container">


    <form name="search" id="search" action="{{ route('classified.search') }}" method="POST">
        <div class="row">

            @csrf

            <div class="col-md-3">
                <label for="marketplace" class="form-label">Marketplace</label>
                <select id="marketplace" name="marketplace_id" class="form-control select2" onchange="this.form.submit()">
                    <option value="">All Marketplaces</option>
                    @foreach($marketplaces as $marketplace)
                    <option value="{{ $marketplace->id }}"
                        {{ old('marketplace_id', request('marketplace_id')) == $marketplace->id ? 'selected' : '' }}>
                        {{ $marketplace->title }}
                    </option>
                    @endforeach
                </select>
            </div>


            <div class="col-md-3">
                <label for="state_id" class="form-label">State</label>
                <select id="state_id" name="state_id" class="form-control select2" onchange="this.form.submit()">
                    <option value="">Select State</option>
                    @foreach($states as $state)
                    <option value="{{ $state->id }}"
                        {{ old('$state->id', request('state_id')) == $state->id ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                    @endforeach
                </select>


            </div>

            <div class="col-md-3">

                <label for="city_id" class="form-label">City</label>

                <select id="city_id" name="city_id" class="form-control select2" onchange="this.form.submit()">
                    <option value="">Select City</option>
                    @foreach($cities as $city)
                    <option value="{{ $city->id }}"
                        {{ old('$city->id', request('city_id')) == $city->id ? 'selected' : '' }}>
                        {{ $city->city_name }}
                    </option>
                    @endforeach
                </select>
                <div id="ajax-loader" style="display: none; text-align: center; margin-top: -45px;">
                    <img src="{{ asset('public/uploads/market/Gray_circles_rotate.gif')}}" alt="Loading..." width="50" />

                </div>
            </div>
        </div>
    </form>

    <div class="row mt-2">
        <div class="col-md-6">
            <a href="{{ route('classified.index') }}" class="theme-btn btn-style-one" style="height: 26px;width: 10px;">Reset</a>
        </div>
    </div>




    <div class="listing">


        @if($classifieds->isEmpty())
        <p>No classifieds available at the moment.</p>
        @else
        @foreach($classifieds as $classified)
        <div class="listing-item">
            @if(!empty($classified->thumbnail))
            <img
                src="{{ asset('public/uploads/marketplace/' . $classified->thumbnail) }}"
                alt="{{ $classified->title }}"
                title="{{ $classified->title }}" />
            @else
            <img
                src="{{ asset('public/assets/images/noimage.jpg') }}"
                alt="{{ $classified->title }}"
                title="{{ $classified->title }}" />

            @endif

            <div class="details">
                <a href="{{ route('classified.singledetail', ['slug' => $classified->slug]) }}">
                    {{ $classified->title }}
                    @if(!empty($classified->price))
                    ${{number_format($classified->price, 2)}}
                    @endif
                </a>

                <p>
                    @if( !empty($classified->city->city_name))
                    {{ $classified->city->city_name }}
                    @endif
                    @if( !empty($classified->state->name))
                    {{ $classified->state->name }} -
                    @endif

                    {{ $classified->marketplace->title ?? 'Unknown Marketplace' }}
                </p>
                <p class="green">Posted by: {{ $classified->name ?? 'Anonymous' }}</p>
                <p class="green">Posted date: {{ $classified->created_at->format('F d, Y') }}</p>


                @if(Auth::check())
                <p>Contact: {{ isset($classified->phone) ? str_replace('+', '', $classified->phone) : 'N/A' }}| {{ $classified->email ?? 'N/A' }}</p>
                @else
                <a class="green" href="{{ route('classified.singledetail', ['slug' => $classified->slug]) }}">Contact us:</a>
                @endif



                @if($classified->website)
                <p><a href="{{ $classified->website }}" target="_blank">Visit Website</a></p>
                @endif

                <!-- Pending Status -->
                @if(Auth::check() && $classified->status == '0')
                <p class="text-warning"><strong>Pending Approval</strong></p>


                <!-- Edit & Delete Buttons -->
                <div class="action-buttons d-flex align-items-center gap-2 mt-2">
                    <!-- Edit Button -->
                    <a href="{{ route('classified.edit', $classified->id) }}" class="btn btn-sm btn-primary text-white d-flex align-items-center me-2">
                        Edit
                    </a>

                    <button type="button" class="btn btn-sm btn-danger delete-button" data-id="{{ $classified->id }}" style=" width: 5%;
                        margin-top: -5px;
                        margin-left: 6px;
                        padding: 5px;">
                        Delete
                    </button>

                    <!-- Delete Form (Hidden) -->
                    <form id="delete-form-{{ $classified->id }}" action="{{ route('classified.delete', $classified->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>

                </div>

                @endif

            </div>
        </div>
        @endforeach
        @endif

    </div>
</div>

<!-- for the form  -->

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const categorySelect = document.getElementById("marketplace_id");
        const petsAllowed = document.querySelector("select[name='pets_allowed']").parentElement;
        const bedrooms = document.querySelector("input[name='bedrooms']").parentElement;
        const certificationType = document.querySelector("select[name='certification_type']").parentElement;
        const serviceType = document.querySelector("select[name='service_type']").parentElement;

        function toggleFields() {
            const selectedValue = categorySelect.value;

            // Show all fields first
            petsAllowed.style.display = "block";
            bedrooms.style.display = "block";
            certificationType.style.display = "block";
            serviceType.style.display = "block";

            if (selectedValue === "1") { // Housing
                certificationType.style.display = "none";
                serviceType.style.display = "none";
            } else if (selectedValue === "2") { // Certification Providers
                petsAllowed.style.display = "none";
                bedrooms.style.display = "none";
                serviceType.style.display = "none";
            } else if (selectedValue === "3") { // Pre-Employment Services
                petsAllowed.style.display = "none";
                bedrooms.style.display = "none";
                certificationType.style.display = "none";
            } else if (selectedValue === "4") { // Pre-Employment Services
                petsAllowed.style.display = "none";
                bedrooms.style.display = "none";
                certificationType.style.display = "none";
                serviceType.style.display = "none";
            } else if (selectedValue === "5") { // Pre-Employment Services
                petsAllowed.style.display = "none";
                bedrooms.style.display = "none";
                certificationType.style.display = "none";
                serviceType.style.display = "none";
            }
        }

        // Initial call
        toggleFields();

        // Event listener
        categorySelect.addEventListener("change", toggleFields);
    });
</script>
<?php
/*
?>
<script>
    $(document).ready(function() {
        // Initialize Select2 for all dropdowns
        $('.select2').select2({
            placeholder: "Search...",
            allowClear: true
        });

        // Load States via AJAX
        $('#state').select2({
            ajax: {
                url: "{{ route('search.states') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return { id: item.id, text: item.name };
                        })
                    };
                },
                cache: true
            }
        });

        // Load Cities based on selected State
        $('#city').select2({
            ajax: {
                url: "{{ route('search.cities') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        state_id: $('#state').val()
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return { id: item.id, text: item.name };
                        })
                    };
                },
                cache: true
            }
        });

        // Load Marketplaces via AJAX
        $('#marketplace').select2({
            ajax: {
                url: "{{ route('search.marketplaces') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return { id: item.id, text: item.title };
                        })
                    };
                },
                cache: true
            }
        });
    });
</script>
<?php
*/
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                let classifiedId = this.getAttribute('data-id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`delete-form-${classifiedId}`).submit();
                    }
                });
            });
        });
    });


    $(document).ready(function() {
        // Show loader when form is submitted
        $('#search').submit(function() {
            $('#ajax-loader').show();
        });

        // Show loader when fetching cities based on state selection
        $('#state_id').change(function() {
            var stateId = $(this).val();
            $('#city_id').empty();

            if (stateId) {
                // Show Loader
                $('#ajax-loader').show();

                $.ajax({
                    url: "{{ route('getCitiesByState') }}",
                    type: "GET",
                    data: {
                        state_id: stateId
                    },
                    success: function(response) {
                        $('#city_id').empty().append('<option value="">Select City</option>');

                        $.each(response.cities, function(key, city) {
                            $('#city_id').append('<option value="' + city.id + '">' + city.city_name + '</option>');
                        });

                        // Hide Loader
                        $('#ajax-loader').hide();
                    },
                    error: function() {
                        alert('Something went wrong. Please try again.');
                        $('#ajax-loader').hide();
                    }
                });
            } else {
                $('#city_id').empty().append('<option value="">Select City</option>');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Initialize Select2 with search box enabled
        $('#state_id').select2({
            tags: true, // Allows adding new item
            placeholder: "Search & Select State", // Placeholder text
            allowClear: true, // Allow clearing selection
            width: '100%', // Ensure it fits well
            minimumInputLength: 1 // Start searching after typing 1 character
        });
        $('#city_id').select2({
            tags: true, // Allows adding new item
            placeholder: "Search & Select State", // Placeholder text
            allowClear: true, // Allow clearing selection
            width: '100%', // Ensure it fits well
            minimumInputLength: 1 // Start searching after typing 1 character
        });
        $('#marketplace_id').select2({
            tags: true, // Allows adding new item
            placeholder: "Search & Select State", // Placeholder text
            allowClear: true, // Allow clearing selection
            width: '100%', // Ensure it fits well
            minimumInputLength: 1 // Start searching after typing 1 character
        });
    });
</script>






<!-- End About Section Three -->



@endsection