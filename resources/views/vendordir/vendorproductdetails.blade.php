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
                <li><a href="{{ route('vendorProducts',  $vendor->id) }}">Products</a></li>
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
                <!-- Company Info -->
                <div class="col-md-6">
                    <h2 class="h3 mb-2">{{ $product->product_title }}</h2>
                    <p class="text-muted">{!! $product->desc !!}</p>
                </div>

                <div class="col-md-4">
                    <div class="list-group">
                        <div class="list-group-item disabled bg-light d-none d-lg-block"><strong>Product Menu</strong></div>
                        <button onclick="event.preventDefault(); OpenEmailModalProduct('818421', 'HR Unlimited, Inc.', '223887');" class="list-group-item active d-block d-lg-none rounded-top text-left">Request Information</button>
                        <a class="list-group-item list-group-item-action" target="_blank" rel="noopener" href="
                        {{ $vendor->website }}">
                            Visit Product Website
                        </a>                    
                    </div>
                </div>

            </div>
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

