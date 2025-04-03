@extends('layouts.app')

@section('content')
<style>
    .card {
        height: auto !important;
    }
    .rating-stars {
        color: #FFD700;
    }
    
    .social-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #a9a5b447;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .social-icon:hover {
        background-color: #0d6efd;
        color: white;
    }
    
    .breadcrumb {
        background-color: transparent;
        padding: 1rem 0;
    }
    
    .company-logo {
        border: 1px solid #dee2e6;
        padding: 20px;
        border-radius: 5px;
    }
    .text-title {
        text-transform: uppercase;
        border-bottom: 2px solid #ff3900cc;
    }
    .action.btn-style-one {
        padding: 15px 15px 15px 15px !important;
    }
    .fade:not(.show) {
        opacity: 1;
    }
    .modal a.close-modal {
        top:-10px !important;
    }
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
                <li><a href="{{ route('vendorDetails', $vendor->id) }}">{{ $vendor->company_name }}</a></li>
            </ul>
    </div>
</section>
    
<div class="container py-4">
    <div class="card position-relative">
        <div class="card-body">
            <div class="row">
                <!-- Company Logo -->
                <div class="col-md-3  mb-3 mb-md-0">
                    <img class="company-logo" src="{{ asset('public/uploads/vendoragency/' . $vendor->logo) }}" class="img-fluid" alt="{{ $vendor->company_name  }}">
                </div>

                <!-- Company Info -->
                <div class="col-md-7">
                    <h2 class="h3 mb-2">{{ $vendor->company_name }}</h2>
                    <h3 class="h5 text-muted mb-3">{{ $vendor->tagline }}</h3>

                    <!-- Rating -->
                    @if($vendor->reviews_count > 0)
                    <div class="mb-3">
                        <span class="rating-stars">
                            @for ($i = 0; $i < floor($vendor->rating); $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </span>
                        <span class="ms-2">
                            Rating: {{ $vendor->rating }}/5 - 
                            <a href="#" class="text-primary text-decoration-none">
                                {{ $vendor->totalReviews }} reviews
                            </a>
                        </span>
                    </div>
                    @endif

                    <!-- Address -->
                    {{-- <p class="mb-2">
                        {{ $vendor->address->street ?? '' }}<br>
                        {{ $vendor->address->city ?? '' }}, {{ $vendor->address->state ?? '' }} {{ $vendor->address->zip ?? '' }}
                    </p> --}}

                    <!-- Contact Info -->
                    <p class="mb-3">
                        <a href="javascript:void(0);" class="phone-click"
                            data-value="{{ $vendor->phone_number }}"><i class="fa fa-phone icons-class"
                            aria-hidden="true"></i><span id="display-number"> click for
                            call</span>
                        </a>
                        <br>
                        <a href="{{ $vendor->website }}" 
                           class="text-primary text-decoration-none" 
                           target="_blank">
                            {{ $vendor->website }}
                        </a>
                    </p>

                    <!-- Social Media -->
                    <div class="mb-4">
                        @if(!empty($vendor->facebook))
                            <a href="{{ $vendor->facebook }}" class="social-icon" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(!empty($vendor->twitter))
                            <a href="{{ $vendor->twitter }}" class="social-icon" target="_blank"><i class="fab fa-twitter"></i></a>
                        @endif
                        @if(!empty($vendor->linkedin))
                            <a href="{{ $vendor->linkedin }}" class="social-icon" target="_blank"><i class="fab fa-linkedin-in"></i></a>       
                        @endif  
                        @if(!empty($vendor->youtube))
                            <a href="{{ $vendor->youtube }}" class="social-icon" target="_blank"><i class="fab fa-youtube"></i></a>
                        @endif
                    </div>                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <a href="javascript:void(0);" class="theme-btn action btn-style-one email-click" data-id="{{ $vendor->id}}" 
                        data-title="{{ $vendor->company_name }}">
                        Request information from this company
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div class="mt-4">
        <h3 class="h4 mb-3 text-title">ABOUT FOR {{ $vendor->company_name }}</h3>
        <p class="mb-3">{{ $vendor->desc }}</p>
        {{-- <p class="mb-0">{{ $vendor->shortDescription }}</p> --}}
    </div>

    <!-- Products Section -->
    <div class="mt-4">
        <h3 class="h4 mb-3 text-title">PRODUCTS BY {{ $vendor->company_name }}</h3>
        <div class="vendor-section">
            <ul class="vendor-list">
                @foreach ($products as $pkey => $product)                
                    <div class="product-card mb-4">
                        <div class="row g-3 align-items-center">
                            <!-- Product Image -->
                            @if(!empty($product->logo))
                            <div class="col-md-3 text-center">                                
                                <a href="{{ route('vendorProducts.details', ['id' =>  Crypt::encrypt($product->id) ]) }}">
                                    <img src="{{ asset('public/uploads/vendoragency_product/' . $product->logo) }}" 
                                        class="product-image img-fluid" 
                                        alt="{{ $product->product_title }}"  width="180">
                                </a>
                            </div>
                            @else
                            <div class="col-md-3 text-center">          
                                <a href="{{ route('vendorProducts.details', ['id' =>  Crypt::encrypt($product->id) ]) }}">                      
                                   <img src="https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg" width="180" height="180" alt="No Image Available" class="product-image img-fluid">
                                </a>
                            </div>
                            @endif
                            <!-- Product Details -->
                            <div class="col-md-9">
                                <a href="{{ route('vendorProducts.details', ['id' =>  Crypt::encrypt($product->id) ]) }}">
                                <h5 class="fw-bold">{{ $product->product_title }}</h5>
                                </a>
                                <p class="text-muted">{!! Str::limit($product->desc, 150) !!}</p>
                                <a href="{{ route('vendorProducts.details', ['id' =>  Crypt::encrypt($product->id) ]) }}">
                                    Read more >>
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($pkey == 1)
                        <div class="col-md-12 text-right">
                            <a href="{{ route('vendorProducts', $vendor->id) }}">
                                View all products >>
                            </a>
                        </div>  
                        @php
                            break;
                        @endphp
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    <!-- White Papers -->
    <div class="mt-4">
        <h3 class="h4 mb-3 text-title">Content by {{ $vendor->company_name }}</h3>
        <div class="vendor-section">
            <ul class="vendor-list">
                @foreach ($blogs as $bkey => $blog)                
                    <div class="product-card mb-4">
                        <div class="row g-3 align-items-center">
                            <!-- Product Image -->
                            @if(!empty($blog->logo))
                            <div class="col-md-3 text-center">                                
                                <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">
                                    <img src="{{ asset('public/uploads/vendoragency_blog/' . $blog->logo) }}" 
                                        class="product-image img-fluid" 
                                        alt="{{ $blog->title }}" width="180">
                                </a>
                            </div>
                            @else
                            <div class="col-md-3 text-center">                                
                                <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">
                                   <img src="https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg" width="180" height="180" alt="No Image Available" class="product-image img-fluid">
                                </a>
                            </div>
                            @endif
                            <!-- Product Details -->
                            <div class="col-md-9">
                                <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">
                                    <h5 class="fw-bold">{{ $blog->title }}</h5>
                                </a>
                                <p class="text-muted">{!! Str::limit($blog->desc, 150) !!}</p>
                                <a href="{{ route('vendorBlogs.details', ['id' =>  Crypt::encrypt($blog->id) ]) }}">                                    
                                    Read more >>
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($bkey == 2)
                        <div class="col-md-12 text-right">
                            <a href="{{ route('vendorBlogs', $vendor->id) }}">
                                View all content >>
                            </a>
                        </div>  
                        @php
                            break;
                        @endphp
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Press Releases -->
    <div class="mt-4">
        <h3 class="h4 mb-3 text-title">News about {{ $vendor->company_name }}</h3>
        <div class="vendor-section">
            <ul class="vendor-list">
                @foreach ($pressReleases as $prkey => $pressRelease)                
                    <div class="product-card mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-9">
                                <a href="{{ route('vendorNews.details', ['id' =>  Crypt::encrypt($pressRelease->id) ]) }}">
                                    <h5 class="fw-bold">{{ $pressRelease->title }}</h5>
                                </a>
                                <p class="text-muted">Posted {{ date('m/d/Y',strtotime($pressRelease->created_at)) }}</p>
                            </div>
                        </div>
                    </div>
                    @if($prkey == 2)
                        <div class="col-md-12 text-right">
                            <a href="{{ route('vendorPressReleases', $vendor->id) }}">
                                View all news >>
                            </a>
                        </div>  
                        @php
                            break;
                        @endphp
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

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
                            <input type="hidden" class="form-control" id="agency_id" name="vendor_agencies_id" >
                            <input type="hidden" class="form-control" id="agency_name" name="agency_name" >
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

</div>

<script>
    $(document).ready(function() {
        $('.phone-click').click(function() {
            var value = $(this).attr('data-value');
            $('#display-number').text(' '+value);
        });


        $(".email-click").click(function () {
            var title = $(this).attr('data-title');
            var agency_id = $(this).attr('data-id');
            $('#contactModalLabel').text('Contact ' + title);
            $('#agency_id').val(agency_id);
            $('#agency_name').val(title);
            $('#message').val('I saw your company in the Travel Nurse 911 Vendor Directory and would like to have someone contact me.');
            $("#contactModal").modal("show");
        });

        $('#contactForm').on('submit', function (event) {
            event.preventDefault();
            $.ajax({
                url: "{{ route('vendorcategory.mail.send') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    var agency_name =  $('#agency_name').val();
                    Swal.fire({
                        title: "Your request has been sent!",
                        text: agency_name + " has been notified of your request and will contact you directly.",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        $('#contactForm')[0].reset();
                        $('#contactModal').hide();
                        $('.blocker').hide(); 
                        $('.mm-wrapper').css('overflow','auto');
                    });
                },
                error: function (xhr) {
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
</script>
@endsection

