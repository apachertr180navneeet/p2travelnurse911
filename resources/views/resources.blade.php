@extends('layouts.app')

@section('content')

<style>
    .steps-section {
        padding: 50px 0;
    }
    /* Card Styles */
    .agency-card {
        height: auto;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .agency-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    .agency-card .card-body {
        text-align: center;
    }
    .agency-card .card-title {
        font-weight: bold;
        margin-bottom: 10px;
    }
    .agency-card img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 15px;
    }
    .agency-card .list-unstyled {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 15px;
    }
    .agency-card .btn {
        margin: 5px;
        font-size: 0.85rem;
    }
    .btn-style-one {
        padding: 10px 20px 10px 20px !important;
    }
    .action-btn {
        justify-content: center;
        gap: 10px;
    }
    .fade:not(.show) {
            opacity: 1;
        }

    .modal a.close-modal {
        top: -10px !important;
    }
</style>

<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
        <h1>Agency List</h1>
        <ul class="page-breadcrumb">
            <li>Find and review the best agencies for your needs</li>
        </ul>
      </div>
   </div>
</section>

<section class="steps-section">

    @include('vendordir.review-modal')

    <div class="container">
        <!-- Grid Section -->
        <div class="row">
            @foreach($agencies as $agency)
            <div class="col-md-4">
                <div class="card agency-card mb-4">
                    <div class="card-body">
                        <!-- Agency Logo -->
                        {{-- <img src="{{ asset('public/assets/images/'.$agency->logo) }}" alt="Agency Logo"> --}}

                        <!-- Agency Name -->
                        <h5 class="card-title">{{ $agency->agency_name }}</h5>

                        <!-- Agency Details -->
                        <ul class="list-unstyled">
                            <!--<li><i class="fas fa-user"></i> Recruiters: {{ $agency->recruiters_count }}</li>-->
                            @if ($agency->jobs_count != 0)
                                <li><i class="fas fa-briefcase"></i> Jobs: {{ $agency->jobs_count }}</li>
                            @endif
                            <li>
                                <a href="javascript:void(0);" id="display-review-modal" data-id="{{$agency->client_id}}" data-title="{{$agency->agency_name}}">
                                    <i class="fas fa-star" style="color: gold;"></i>
                                    {{ $agency->avg_rating ?? 'N/A' }}
                                    ({{ $agency->review_count ?? 0 }} reviews)
                                </a>
                            </li>
                        </ul>

                        <!-- Action Buttons -->
                        <div class="action-btn d-flex">
                            <a href="javascript:void(0)" 
                                class="btn-style-one btn-sm" id="review-modal" data-id="{{$agency->client_id}}" data-title="{{$agency->agency_name}}">
                                    <i class="fas fa-pencil-alt"></i> Submit Review
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
            $(document).on("click", "#review-modal", function() {
                let company_name = $(this).attr('data-title');
                let client_id = $(this).attr('data-id');
                $('#review-modal-title').text('Write a review for '+ company_name);
                $('#agecy_name').text(company_name);
                $('#vendor_agencies_id').val(client_id);
                $("#reviewModal").modal("show");
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
                    url: "{{route('resource.submit.review')}}", 
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

            $(document).on("click", "#display-review-modal", function() {
                let client_id = $(this).attr('data-id');
                let company_name = $(this).attr('data-title');
                $('#display-review-title').text('Reviews for ' + company_name);
                $.ajax({
                    url: "{{ route('resource.feedback') }}",
                    method: "GET",
                    data: {
                        client_id: client_id,
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
