@extends('layouts.app')

@section('content')
    <section class="page-title">
        <div class="auto-container">
            <div class="title-outer" style="margin-left: 6%;">
                <h1 class="text-left">Travel Nursing Industry Vendor Directory</h1>
                <ul class="page-breadcrumb text-left">
                    <li><a href="{{ route('vendorcategory') }}">Category</a></li>
                    <li>Vendors</li>
                </ul>
            </div>
        </div>
    </section>
    <style>
        .card {
            height: auto !important;
        }

        .icons-class {
            font-size: 13px;
            color: gray;
            margin-right: 4px
        }

        .img-fluid {
            width: 100%;
            height: 145px;
        }

        .fade:not(.show) {
            opacity: 1;
        }

        .modal a.close-modal {
            top: -10px !important;
        }
        div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm) {
            background-color: #ff5712;
            border: 1px solid #ff5712;
        }
        
    </style>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group search-form-type">
                    <a href="javascript:void(0);" class="theme-btn btn-style-one list-company" 
                    id="openListCompanyModel">
                    List Your Company
                    </a>
                </div>                                                            
            </div>
        </div>
        <h3 class="my-2">{{ $subcategory->title }}</h3>
        <h5>Compare {{ count($vendors) }} Companies</h5>
        <p>Compare and research {{ $subcategory->title }} companies and businesses</p>

        @foreach ($vendors as $key => $vendor)
            <div class="card my-3 p-3">
                <div class="row">
                    <div class="col-md-2">
                        <div class="img-box">
                            <img src="{{ asset('public/uploads/vendoragency/' . $vendor->logo) }}" class="img-fluid"
                                alt="{{ $vendor->company_name }}">
                        </div>
                    </div>
                    <div class="col-md-8" style="border-right: 1px solid;color: lightgray;">
                        <h4>
                            <a href="{{ route('vendorDetails', ['id' => Crypt::encrypt($vendor->id)]) }}"
                                class="text-primary fw-bold">{{ $vendor->company_name }}</a>
                        </h4>
                        <p>{{ $vendor->tagline }}</p>
                        @if ($vendor->reviews_count > 0)
                            <p class="text-warning">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($vendor->reviews_avg_rating)) 
                                        <i class="fas fa-star text-warning"></i>  {{-- Full Star --}}
                                    @elseif ($i == ceil($vendor->reviews_avg_rating) && $vendor->reviews_avg_rating - floor($vendor->reviews_avg_rating) >= 0.5)
                                        <i class="fas fa-star-half-alt text-warning"></i>  {{-- Half Star --}}
                                    @else
                                        <i class="far fa-star text-warning"></i>  {{-- Empty Star --}}
                                    @endif
                                @endfor

                                <span style="color:gray">Rating:
                                {{ round($vendor->reviews_avg_rating, 1) }}/5
                                -
                                <a href="javascript:void(0);" class="text-primary" id="display-review-modal" data-title="{{ $vendor->company_name }}" data-id="{{ $vendor->id }}">
                                    {{ $vendor->reviews_count }} reviews
                                </a>
                                </span>
                            </p>
                        @endif
                        @if (!empty($vendor->website))
                            <a href="{{ $vendor->website }}" target="_blank"
                                class="text-primary">{{ $vendor->website }}</a>
                        @endif
                        @if (!empty($vendor->desc))
                            <p>{{ Str::limit($vendor->desc, 150) }}</p>
                        @endif
                    </div>
                    <div class="col-md-2 text-end">
                        <p class="company-info">
                            <a href="javascript:void(0);" class="phone-click" data-id="{{ $key }}"
                                data-value="{{ $vendor->phone_number }}"><i class="fa fa-phone icons-class"
                                    aria-hidden="true"></i><span id="display-number-{{ $key }}"> click for
                                    call</span>
                            </a>
                        </p>
                        <p class="company-info">
                            <a href="javascript:void(0);" class="email-click" data-id="{{ $vendor->id }}"
                                data-title="{{ $vendor->company_name }}"><i class="fa fa-envelope icons-class"
                                    aria-hidden="true"></i> Send an email</a>
                        </p>

                        @if ($vendor->products_count > 0)
                            <p class="company-info">
                                <a href="{{ route('vendorProducts', $vendor->id) }}"><i class="fa fa-gear icons-class"
                                        aria-hidden="true"></i> {{ $vendor->products_count }} products</a>
                            </p>
                        @endif

                        @if ($vendor->blogs_count > 0)
                            <p class="company-info">
                                <a href="{{ route('vendorBlogs', $vendor->id) }}"><i class="fa fa-file icons-class"
                                        aria-hidden="true"></i> {{ $vendor->blogs_count }} White Papers</a>
                            </p>
                        @endif

                        @if ($vendor->releases_count > 0)
                            <p class="company-info">
                                <a href="{{ route('vendorPressReleases', $vendor->id) }}">
                                    <i class="fa fa-rss icons-class" aria-hidden="true"></i> {{ $vendor->releases_count }}
                                    Press Releases
                                </a>
                            </p>
                        @endif

                        <p class="company-info">
                            @if ($vendor->reviews_count > 0)
                                <a href="javascript:void(0);" id="display-review-modal" data-id="{{$vendor->id}}" data-title="{{ $vendor->company_name }}" > <i class="fa fa-star icons-class" aria-hidden="true"></i>
                                    {{ number_format($vendor->reviews_count) }} reviews</a>
                            @else
                                <a href="javascript:void(0);" data-id="{{$vendor->id}}" data-title="{{ $vendor->company_name }}" 
                                    id="review-modal"> <i class="fa fa-star icons-class" aria-hidden="true"></i> write a
                                    review</a>
                            @endif
                        </p>

                    </div>
                </div>
            </div>
        @endforeach

        <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contactModalLabel">Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="contactForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Your work email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <input type="hidden" class="form-control" id="agency_id" name="vendor_agencies_id">
                                <input type="hidden" class="form-control" id="agency_name" name="agency_name">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject line</label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                    value="Requesting information" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="3" required>                                
                               </textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send message</button>
                        </form>
                        <p class="text-muted mt-3 small">
                            By sending a message you agree to our <a href="#">Terms of Service</a> and <a
                                href="#">Privacy Policy</a>; and, you acknowledge that your information and message
                            will be shared with this company.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="companyModal" tabindex="-1" aria-labelledby="contactModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contactModalLabel">List Your Company</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="compnayForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Vendor Category</label>
                                <select name="vendor_categories_id" id="vendor_category" class="form-control me-2" required>
                                    <option value="">Select Category</option>         
                                    @foreach($vendor_categories as $category)
                                        <option value="{{$category->id}}">{{$category->title}}</option>    
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="sub_category" class="form-label">Vendor Sub Category</label>
                                <select name="sub_category" id="vendor_sub_category" class="form-control me-2" required>
                                    <option value="">Select Sub Category</option>         
                                    @foreach($vendor_sub_categories as $subcategory)
                                        <option value="{{$subcategory->id}}" data-sub-id={{ $subcategory->vendor_category_id }} >{{$subcategory->title}}</option>    
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="text" class="form-control" id="website" name="website" required>
                            </div>                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="about" class="form-label">About</label>
                                <textarea class="form-control" id="about" name="about" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="press_releases" class="form-label">Press Releases (add link comma Separated)</label>
                                <textarea class="form-control" id="press_releases" name="press_releases" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="theme-btn btn-style-one list-company w-100">Submit</button>
                        </form>
                    
                    </div>
                </div>
            </div>
        </div>
        
        @include('vendordir.review-modal')
        

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            $(document).on("click", "#openListCompanyModel", function() {
                $("#companyModal").modal("show");
            });

            $('#phone').on('input', function () {
                let value = $(this).val().replace(/\D/g, ''); // Remove all non-numeric characters
                
                if (value.length > 3 && value.length <= 6) {
                    value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
                } else if (value.length > 6) {
                    value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
                }
                
                $(this).val(value);
            });

            $('#companyModal').on('submit', function(event) {
                event.preventDefault();
            
                let formData = new FormData(this); // Use FormData to send files
            
                $.ajax({
                    url: "{{ route('vendorcategory.submit-company') }}", // Update with correct route name
                    method: "POST",
                    data: formData,
                    processData: false, // Prevent jQuery from processing data
                    contentType: false, // Prevent jQuery from setting content type
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Your details have been submitted!",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                $('#compnayForm')[0].reset();
                                $('#companyModal').hide();
                                $('.blocker').hide();
                                $('.mm-wrapper').css('overflow', 'auto');
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Error!",
                            text: "Something went wrong. Please try again.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            });
            

        });
        $(document).ready(function() {
            $('.phone-click').click(function() {
                var value = $(this).attr('data-value');
                var id = $(this).attr('data-id');
                $('#display-number-' + id).text(value);
            });

            $(".email-click").click(function() {
                var title = $(this).attr('data-title');
                var agency_id = $(this).attr('data-id');
                $('#contactModalLabel').text('Contact ' + title);
                $('#agency_id').val(agency_id);
                $('#agency_name').val(title);
                $('#message').val(
                    'I saw your company in the Travel Nurse 911 Vendor Directory and would like to have someone contact me.'
                    );
                $("#contactModal").modal("show");
            });

            $('#contactForm').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: "{{ route('vendorcategory.mail.send') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        var agency_name = $('#agency_name').val();
                        Swal.fire({
                            title: "Your request has been sent!",
                            text: agency_name +
                                " has been notified of your request and will contact you directly.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            $('#contactForm')[0].reset();
                            $('#contactModal').hide();
                            $('.blocker').hide();
                            $('.mm-wrapper').css('overflow', 'auto');
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Error!",
                            text: "Something went wrong. Please try again.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            });

            $(document).on("click", "#review-modal", function() {
                let company_name = $(this).attr('data-title');
                let vendor_agencies_id = $(this).attr('data-id');
                $('#review-modal-title').text('Write a review for '+ company_name);
                $('#agecy_name').text(company_name);
                $('#vendor_agencies_id').val(vendor_agencies_id);
                $("#reviewModal").modal("show");
            });

            $(document).on("click", "#display-review-modal", function() {
                let vendor_agencies_id = $(this).attr('data-id');
                let company_name = $(this).attr('data-title');
                $('#display-review-title').text('Reviews for ' + company_name);
                $.ajax({
                    url: "{{ route('vendor.agency.review-list') }}",
                    method: "GET",
                    data: {
                        vendor_agencies_id: vendor_agencies_id,
                    },
                    success: function(response) {
                        if(response.success) {
                            $('#all-reviews').html(response.data);
                            $('#displayReviews').modal('show');                            
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Please try again!",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    }
                });
            });


            $("#reviewModal form").on("submit", function(e) {
                if (!$("input[name='rating']:checked").val()) {
                    $("#rating-error-text").text("Please select a rating.");
                    return false;
                } else {
                    $("#rating-error-text").text("");
                }
                e.preventDefault(); // Prevent normal form submission

                let form = $(this);
                let formData = form.serialize();

                $.ajax({
                    url: form.attr("action"), // Get form action URL
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Show success alert
                            Swal.fire({
                                title: "Thank You!",
                                text: "Your review has been submitted successfully.",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                $("#reviewModal").hide(); // Close modal
                                form[0].reset(); // Reset form fields
                                $('.blocker').hide();
                                $('.mm-wrapper').css('overflow', 'auto');
                            });
                        } else {
                            // Show error message if submission fails
                            Swal.fire({
                                title: "Info!",
                                text: response.error || "Something went wrong. Please try again.",
                                icon: "info",
                                confirmButtonText: "Register"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#registerModal').modal('show');
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        Swal.fire({
                            title: "Error!",
                            text: "Please try again!",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            });

        });
    </script>
    <script>
        $(document).ready(function () {
            // Open modal when register button is clicked
            $("#register-modal").click(function () {
                $("#registerModal").modal("show");
            });
        
            // Handle form submission with AJAX
            $("#registerForm").submit(function (e) {
                e.preventDefault();
        
                let formData = {
                    name: $("#name").val(),
                    useremail: $("#useremail").val(),
                    password: $("#password").val(),
                    _token: "{{ csrf_token() }}" // CSRF token for security
                };
                console.log(formData);
        
                // Clear previous error messages
                $(".error-name, .error-email, .error-password").text("");
        
                $.ajax({
                    url: "{{ route('vendor.agency.register') }}", // Laravel route
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        $("#registerModal").hide(); // Close modal
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: response.message,
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload(); // Yahan apni redirect URL dalen
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessage = "";
        
                            if (errors.name) {
                                $(".error-name").text(errors.name[0]);
                                errorMessage += errors.name[0] + "<br>";
                            }
                            if (errors.email) {
                                $(".error-email").text(errors.email[0]);
                                errorMessage += errors.email[0] + "<br>";
                            }
                            if (errors.password) {
                                $(".error-password").text(errors.password[0]);
                                errorMessage += errors.password[0] + "<br>";
                            }
        
                            Swal.fire({
                                icon: "error",
                                title: "Validation Error!",
                                html: errorMessage,
                                timer: 3000, // Auto close in 3 seconds
                                showConfirmButton: false
                            });
        
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Oops!",
                                text: "An error occurred. Please try again.",
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
