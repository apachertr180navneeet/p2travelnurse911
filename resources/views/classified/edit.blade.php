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

    .user-options a {
        /* padding: 8px 12px; */
        color: #c7dcda;
        /* border: 1px solid #397be7; */
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

    .container h4 {
        text-align: center;
        padding: 20px 0px 0px 0px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
        color: #333;
        margin-top: 5px;
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
                <button type="submit" class="theme-btn btn-style-one" style="width:100%;border:none!important;">Logout</button>
            </form>
            @else
            <a href="{{ route('auth.user.login') }}" class="theme-btn btn-style-one" style="border:none;">Login</a>
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

<!-- for the form  -->

<div class="container mb-5" id="edit">
    <form name="housing_classified" id="housing_classified"
        action="{{ route('classified.update', $classifiedAds->id) }}"
        method="POST" enctype="multipart/form-data">

        @csrf
        @method('PUT') <!-- Required for update -->
        <h4>Edit Classified Ad</h4>
        <div class="form-group">
            <label for="marketplace_id">All Marketplaces</label>
            <select id="marketplace_id" name="marketplace_id">
                @foreach($marketplaces as $marketplace)
                <option value="{{ $marketplace->id }}"
                    {{ old('marketplace_id', $classifiedAds->marketplace_id) == $marketplace->id ? 'selected' : '' }}>
                    {{ $marketplace->title }}
                </option>
                @endforeach
            </select>
            @error('marketplace_id') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="default_hide">
        <div id="common-fields">
            <div class="two-columns">
                <div class="form-group">
                    <label for="title" class="pb-2">Title:</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $classifiedAds->title) }}"   style="height: 42px;" />
                    @error('title') <div class="text-danger ">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="state_id" class="pb-2">Select State:</label>
                    <select id="state_id" name="state_id">
                        @foreach($states as $state)
                        <option value="{{ $state->id }}"
                            {{ old('state_id', $classifiedAds->state_id) == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('state_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="two-columns">
                <div class="form-group">
                    <label for="city_id" >Select City:</label>
                    <select id="city_id" name="city_id">
                        @foreach($cities as $city)
                        <option value="{{ $city->id }}"
                            {{ old('city_id', $classifiedAds->city_id) == $city->id ? 'selected' : '' }}>
                            {{ $city->city_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('city_id') <div class="text-danger">{{ $message }}</div> @enderror
                    <div id="ajax-loader" style="display: none; text-align: center; margin-top: -45px; ">
                <img src="{{ asset('public/uploads/market/Gray_circles_rotate.gif')}}" alt="Loading..." class="" width="50 " />
            </div>
                </div>

                <div class="form-group">
                <label for="price" >Price:</label>
                <input type="text" id="price" name="price" value="{{ old('price', $classifiedAds->price) }}"   style="height: 42px;" />
                @error('price') <div class="text-danger ">{{ $message }}</div> @enderror
    
            </div>
        </div>
        <div class="two-columns">
                <div class="form-group">
                 <label for="price_type" >Price Type:</label>
                 <select id="price_type" name="price_type" class="form-control">
                 <option value="monthly"  {{ old('price_type', $classifiedAds->price_type) == 'monthly' ? 'selected' : '' }} selected>Monthly</option>
                 <option value="weekly" {{ old('price_type', $classifiedAds->price_type) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                  </select>
                  @error('price_type')
                <div class="text-danger">{{ $message }}</div>
                 @enderror
                 </div>
                </div>

            </div>
        <div id="housing-fields" class="two-columns">
            <div class="form-group">
                <label >Pets Allowed:</label>
                <select name="pets_allowed">
                    <option value="yes" {{ old('pets_allowed', $classifiedAds->pets_allowed) == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ old('pets_allowed', $classifiedAds->pets_allowed) == 'no' ? 'selected' : '' }}>No</option>
                </select>
                @error('pets_allowed') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="bedrooms" ># of Bedrooms:</label>
                <input type="number" id="bedrooms" name="bedrooms" style="height: 42px"; value="{{ old('bedrooms', $classifiedAds->bedrooms) }}" />
            </div>
        </div>

        <div id="certification-fields" class="two-columns">
            <div class="form-group">
                <label for="cert-type" >Certification Type:</label>
                <select id="cert-type" name="certification_type">
                    <option value="CPR" {{ old('certification_type', $classifiedAds->certification_type) == 'CPR' ? 'selected' : '' }}>CPR</option>
                    <option value="BLS" {{ old('certification_type', $classifiedAds->certification_type) == 'BLS' ? 'selected' : '' }}>BLS</option>
                    <option value="ACLS" {{ old('certification_type', $classifiedAds->certification_type) == 'ACLS' ? 'selected' : '' }}>ACLS</option>
                    <option value="PALS" {{ old('certification_type', $classifiedAds->certification_type) == 'PALS' ? 'selected' : '' }}>PALS</option>
                    <option value="TNCC" {{ old('certification_type', $classifiedAds->certification_type) == 'TNCC' ? 'selected' : '' }}>TNCC</option>
                    <option value="ENPC" {{ old('certification_type', $classifiedAds->certification_type) == 'ENPC' ? 'selected' : '' }}>ENPC</option>
                </select>
                @error('certification_type') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>

        <div id="employment-fields" class="two-columns">
                <div class="form-group">
                    <label for="service-type" >Service Type:</label>
                    <select id="service-type" name="service_type">
                       
                        <option value="resume" {{ old('service_type', $classifiedAds->service_type) == 'resume' ? 'selected' : '' }}>Resume Writing</option>
                        <option value="interview" {{ old('service_type', $classifiedAds->service_type) == 'interview' ? 'selected' : '' }}>Interview Coaching</option>
                        <option value="training" {{ old('service_type', $classifiedAds->service_type) == 'training' ? 'selected' : '' }}>Job Training</option>
                    </select>
                    @error('service_type')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

        <div class="form-group">
            <label for="description" >Description:</label>
            <textarea id="description" name="description">{{ old('description', $classifiedAds->description) }}</textarea>
            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div id="contact-info" class="form-group">
            <h5 class='mb-3 mt-2'>Contact Info</h5>
            <div class="two-columns">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $classifiedAds->name) }}" style="height: 42px"; />
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $classifiedAds->phone) }}" style="height: 42px";  />
                    @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="two-columns mb-3">
            <div class="form-group">
                <label for="email" class="mb-3">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $classifiedAds->email) }}" style="height: 42px";  />
                @error('email')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="website" class="mb-3">Website:</label>
                <input type="text" id="website" name="website" value="{{ old('website', $classifiedAds->website) }}" style="height: 42px";  placeholder="https://example.com"  />
                @error('website')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Image Upload -->
        <div class="two-columns">
            <div class="form-group">
                <label for="thumbnail" class="mb-3">Thumbnail:</label>
                <input type="file" class="form-control" style="height: 42px";  id="thumbnail" name="thumbnail" accept="image/*" />
                @if(!empty($classifiedAds->thumbnail))<label>{{ $classifiedAds->thumbnail }}</label> @endif
            </div>

            @if(!empty($classifiedAds->thumbnail))
            <div class="form-group">
                <img
                    id="thumbnailpreview" class="img-fluid img-thumbnail mt-3"
                    src="{{ asset('public/uploads/marketplace/' . $classifiedAds->thumbnail) }}"
                    alt="{{ $classifiedAds->title }}"
                    title="{{ $classifiedAds->title }}" style="width:100px;" />
            </div>
            @else
            <div class="form-group">
                <img id="thumbnailpreview" class="img-fluid img-thumbnail mt-3 d-none" style="width:100px;"  />
            </div>
            @endif
        </div>
        </div>
        <div class="button-align justify-content-start pb-3 mt-3">
            <button type="submit" name="submit">Update Post Ad</button>
        </div>
    </form>
</div>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>



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
    document.addEventListener("DOMContentLoaded", function () {
    const categorySelect = document.getElementById("marketplace_id");
    const priceTypeField = document.querySelector("select[name='price_type']").parentElement;

    function toggleFields() {
        const selectedValue = categorySelect.value;
        priceTypeField.style.display = "none";
        if (selectedValue === "1") { 
            priceTypeField.style.display = "block";
        }
    }

    toggleFields();

    categorySelect.addEventListener("change", toggleFields);
});
 </script>
<script>
    /*
    document.addEventListener("DOMContentLoaded", function() {
        function toggleFields() {

            const categorySelect = document.getElementById("marketplace_id");

            if (!categorySelect) return; // Ensure the element exists before proceeding

            const selectedValue = categorySelect.value;

            const fields = {
                petsAllowed: document.querySelector("select[name='pets_allowed']")?.parentElement || null,
                bedrooms: document.querySelector("input[name='bedrooms']")?.parentElement || null,
                certificationType: document.querySelector("select[name='certification_type']")?.parentElement || null,
                serviceType: document.querySelector("select[name='service_type']")?.parentElement || null,
            };
            // Show all fields initially
            //Object.values(fields).forEach(field => field.style.display = "block");

            // Hide fields based on category selection

            switch (selectedValue) {
                case "1": // Housing
                    fields.certificationType.style.display = "none";
                    fields.serviceType.style.display = "none";
                    break;
                case "2": // Certification Providers
                    fields.petsAllowed.style.display = "none";
                    fields.bedrooms.style.display = "none";
                    fields.serviceType.style.display = "none";
                    break;
                case "3": // Pre-Employment Services
                    fields.petsAllowed.style.display = "none";
                    fields.bedrooms.style.display = "none";
                    fields.certificationType.style.display = "none";
                    break;
                case "4": // Other Category
                    fields.petsAllowed.style.display = "none";
                    fields.bedrooms.style.display = "none";
                    fields.certificationType.style.display = "none";
                    fields.serviceType.style.display = "none";
                    break;
                case "5": // Other Category
                    fields.petsAllowed.style.display = "none";
                    fields.bedrooms.style.display = "none";
                    fields.certificationType.style.display = "none";
                    fields.serviceType.style.display = "none";
                    break;

                    // Object.values(fields).forEach(field => field.style.display = "none");
                    // break;
            }
        }

        // Run on page load
        toggleFields();

        // Run when category selection changes
        document.getElementById("marketplace_id")?.addEventListener("change", toggleFields);
    });

    */




    document.addEventListener("DOMContentLoaded", function() {


        const categorySelect = document.getElementById("marketplace_id");
        const petsAllowed = document.querySelector("select[name='pets_allowed']").parentElement;
        const bedrooms = document.querySelector("input[name='bedrooms']").parentElement;
        const certificationType = document.querySelector("select[name='certification_type']").parentElement;
        const serviceType = document.querySelector("select[name='service_type']").parentElement;
        const priceTypeField = document.querySelector("select[name='price_type']").parentElement;
        const defaultFields = document.querySelector(".default_hide");
        const titleField = document.querySelector("input[name='title']").parentElement;
        const websiteField = document.querySelector("input[name='website']").parentElement;
        const contactinfoDiv = document.getElementById("contact-info");
        const thumbnailField = document.querySelector("input[name='thumbnail']").parentElement;
        const cityField = document.getElementById("city_id").parentElement;;
        const stateField = document.getElementById("state_id").parentElement;;


        function toggleFields() {
            const selectedValue = categorySelect.value;

            document.querySelector(".default_hide").style.display = "block";

            // Show all fields first
            petsAllowed.style.display = "block";
            bedrooms.style.display = "block";
            certificationType.style.display = "block";
            serviceType.style.display = "block";
           
           
            if (selectedValue === "1") { // Housing

                defaultFields.querySelectorAll(".form-group").forEach(field => {
                field.style.display = "block";
                priceTypeField.style.display = "block";
                
            });
                certificationType.style.display = "none";
                serviceType.style.display = "none";
               
            } else if (selectedValue === "2") { // Certification Providers
                defaultFields.querySelectorAll(".form-group").forEach(field => {
                field.style.display = "block";
                priceTypeField.style.display = "block";
            });
                petsAllowed.style.display = "none";
                bedrooms.style.display = "none";
                serviceType.style.display = "none";
                priceTypeField.style.display = "none";
            } else if (selectedValue === "3") { // Pre-Employment Services
                defaultFields.querySelectorAll(".form-group").forEach(field => {
                field.style.display = "block";
              
            });
                petsAllowed.style.display = "none";
                bedrooms.style.display = "none";
                certificationType.style.display = "none";
                priceTypeField.style.display = "none";
            } else if (selectedValue === "4") { // Pre-Employment Services
                defaultFields.querySelectorAll(".form-group").forEach(field => {
                    field.style.display = "none";  
                    priceTypeField.style.display = "none";

                });

                // Show only title and website fields
                contactinfoDiv.style.display="block";
                websiteField.style.display = "block";
                titleField.style.display = "block";  
                
               
            } else if (selectedValue === "5") { // Pre-Employment Services
                 defaultFields.querySelectorAll(".form-group").forEach(field => {
                    field.style.display = "block";  
                                  
                });
                petsAllowed.style.display = "none";
                bedrooms.style.display = "none";
                certificationType.style.display = "none";
                serviceType.style.display = "none";
                thumbnailField.style.display="none";
                priceTypeField.style.display = "none";
                
            }
        }


    // Initial call to display correct fields on page load (for Edit form)
    toggleFields();

    // Event listener for category change
    categorySelect.addEventListener("change", toggleFields);
});

</script>
<script>
    $(document).ready(function() {
    $("#state_id").change(function() {
        var stateID = $(this).val();
        var cityDropdown = $("#city_id");
        var loader = $("#ajax-loader");

        if (stateID) {
            loader.show(); 
            $.ajax({
                url: "/get-cities/" + stateID, 
                type: "GET",
                dataType: "json",
                success: function(data) {
                    cityDropdown.empty().append('<option value="">Select City</option>');
                    $.each(data, function(key, value) {
                        cityDropdown.append('<option value="' + value.id + '">' + value.city_name + '</option>');
                    });
                    loader.hide(); 
                },
                error: function() {
                    loader.hide();
                   
                }
            });
        } else {
            cityDropdown.empty().append('<option value="">Select City</option>');
        }
    });
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


    $(document).ready(function() {
        $('#state_id').change(function() {
            var stateId = $(this).val();
            $('#city').empty();

            // Make an AJAX request to fetch cities based on the selected state
            if (stateId) {
                $('#ajax-loader').show();
                $.ajax({
                    url: "{{ route('getCitiesByState') }}", // Route to fetch cities by state
                    type: "GET",
                    data: {
                        state_id: stateId
                    },
                    success: function(response) {
                        // Clear the city dropdown
                        $('#city_id').empty();
                        $('#city_id').append('<option value="">Select City</option>');

                        // Append new cities to the dropdown
                        $.each(response.cities, function(key, city) {
                            $('#city_id').append('<option value="' + city.id + '">' + city.city_name + '</option>');
                        });
                        $('#ajax-loader').hide();
                    }
                });
            } else {
                // If no state is selected, clear the city dropdown
                $('#city_id').empty();
                $('#city_id').append('<option value="">Select City</option>');
            }
        });
    });



</script>





<!-- End About Section Three -->



@endsection