@extends('layouts.app')
@section('content')
    <style>
        .main-category {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .search-form-type {
            display: flex;
            flex-direction: row;
            width: 100%;
            gap: 10px;
        }
        .page-title {
            padding: 2px 0 2px;
        }
        .list-company {
            padding: 10px 20px 10px 20px !important;
            font-size: 16px !important;
        }
        .find-service-div {
            display: flex;
            flex-wrap: wrap;
        }

        .fade:not(.show) {
            opacity: 1;
        }

        .modal a.close-modal {
            top: -10px !important;
        }
         .modal-header {
            color: #fff;
            background-color: #ff5712;
        }
        .modal a.close-modal {
            background: #ff5712;
        }
        .modal a.close-modal:before {
            color: #fff
        }
        div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm) {
            background-color: #ff5712;
            border: 1px solid #ff5712;
        }
    </style>
    <!--Page Title-->
    <section class="page-title">
        <div class="auto-container">
            <div class="title-outer" style="margin-left: 6%;">
                <h1 class="text-left">Travel Nursing Industry Vendor Directory</h1>
                <ul class="page-breadcrumb text-left">
                    <li><a href="{{ route('vendorcategory') }}">Category</a></li>
                </ul>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="content-box">
            <div class="content-title mt-3">
                <div class="row">
                    <form class="d-flex w-100 find-service-div" action="{{ route('vendorcategory') }}" method="GET">
                        <div class="col-md-3">
                            <select name="filter" class="form-control me-2">
                                <option value="">Find A Service</option>
                                <option value="category" {{ request('filter') == 'category' ? 'selected' : '' }}>By Categoy</option>
                                <option value="company" {{ request('filter') == 'company' ? 'selected' : '' }}>By Company</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group search-form-type">
                                <input type="text" name="search" class="form-control" placeholder="Find services and companies" value="{{ request('search') }}">
                                <button type="submit" class="theme-btn btn-style-one list-company">Search</button>
                            </div>                                                            
                        </div>
                        <div class="col-md-3">
                            <div class="form-group search-form-type">
                                <a href="javascript:void(0);" class="theme-btn btn-style-one list-company" 
                                id="openListCompanyModel">
                                List Your Company
                                </a>
                            </div>                                                            
                        </div>
                        
                    </form>
                </div>

                <h5>Select a category to view search results for quality vendors providing Travel Nurse Vendor Directory
                    products.</h5>
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <ul class="list mt-2">
                            @foreach ($categories as $category)
                                <li class="list mt-2">
                                    <a class="main-category" href="{{ route('vendorSubList', ['vendor_category_slug' => $category->slug]) }}">
                                        {{ $category->title }}
                                    </a>
                                    @php
                                        $search = request('search');
                                        $subcategories = App\Models\VendorSubCategory::select('vendor_subcategories.id', 'vendor_subcategories.title', 'vendor_subcategories.slug')
                                            ->leftJoin('vendor_categories', 'vendor_subcategories.vendor_category_id', '=', 'vendor_categories.id')
                                            ->leftJoin('vendor_agency_category', 'vendor_agency_category.vendor_categories_id', '=', 'vendor_categories.id')
                                            ->leftJoin('vendor_agencies', 'vendor_agency_category.vendor_agencies_id', '=', 'vendor_agencies.id')
                                            ->where('vendor_subcategories.status', 1)
                                            ->where('vendor_subcategories.vendor_category_id', $category->id)
                                            ->when(request('filter') == 'company' && !empty($search), function ($query) {
                                                $query->where('vendor_agencies.company_name', 'LIKE', '%' . request('search') . '%');
                                            })
                                            ->when(request('filter') == 'category' && !empty($search), function ($query) {
                                                $query->where('vendor_subcategories.title', 'LIKE', '%' . request('search') . '%');
                                            })
                                            ->distinct() // Prevents duplicate subcategories due to joins
                                            ->get()
                                            ->map(function ($subcategory) {
                                                $subcategoryId = (string) $subcategory->id;
                                                // Use REGEXP to match the value inside the JSON string
                                                $subcategory->vendor_agency_count = App\Models\VendorAgencyCategory::whereRaw(
                                                    'JSON_UNQUOTE(vendor_subcategories_ids) LIKE ?',
                                                    ['%"' . $subcategoryId . '"%'],
                                                )->count();
                                                return $subcategory;
                                            });
                                    @endphp
                                    <ul class="list">
                                        @foreach ($subcategories as $sub)
                                            <li class="list">
                                                <a href="{{ route('vendorList', ['vendor_category_slug' => $category->slug, 'vendor_subcategory_slug' => $sub->slug]) }}">
                                                    {{ $sub->title }}
                                                </a>
                                                ({{ $sub->vendor_agency_count }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">List Your Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="contactForm" method="POST" enctype="multipart/form-data">
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            $(document).on("click", "#openListCompanyModel", function() {
                $("#contactModal").modal("show");
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

            $('#contactForm').on('submit', function(event) {
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
                                $('#contactForm')[0].reset();
                                $('#contactModal').hide();
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
            $("#vendor_category").on("change", function() {
                var selectedCategory = $(this).val(); // Get selected category ID
                console.log(selectedCategory);
                $("#vendor_sub_category option").each(function() {
                    var subCategory = $(this);
                    var subCategoryId = subCategory.data("sub-id"); // Get data-sub-id
                    
                    if (selectedCategory == subCategoryId || subCategory.val() == "") {
                        subCategory.show();  // Show matching subcategories
                    } else {
                        subCategory.hide();  // Hide non-matching subcategories
                    }
                });
        
                // Reset sub-category selection to default
                $("#vendor_sub_category").val("");
            });
        });
        
    </script>
@endsection
