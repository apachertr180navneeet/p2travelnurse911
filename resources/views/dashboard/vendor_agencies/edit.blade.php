@extends('dashboard.master')

@section('title', 'Edit Vendor Agency - ' . config('app.name'))

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Vendor Agency</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.vendor_agencies.index') }}">All
                                    Agencies</a></li>
                            <li class="breadcrumb-item active">Edit Vendor Agency</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Vendor Agency</h3>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-times-circle"></i> Error!</h5>
                            <p class="m-0">{{ session("error") }}</p>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                @foreach ($errors->all() as $error)
                                    <p class="m-0">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-check"></i> Success!</h5>
                                <p class="m-0">{{ session('success') }}</p>
                            </div>
                        @endif
                        <form action="{{ route('dashboard.vendor_agencies.update', $vendorAgency->id) }}"
                            enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- TABS -->
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab1">
                                        <i class="fas fa-building"></i> Vendor Agency
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab2">
                                        <i class="fas fa-box"></i> Vendor Products
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab3">
                                        <i class="fas fa-blog"></i> Vendor Blogs
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab4">
                                        <i class="fas fa-bullhorn"></i> Vendor Releases
                                    </a>
                                </li>
                            </ul>

                            <!-- TAB CONTENT -->
                            <div class="tab-content mt-3">
                                <!-- TAB 1: Vendor Agency -->
                                <div id="tab1" class="tab-pane fade show active">
                                    <div class="card p-4">
                                        <h4 class="mb-3">Vendor Agency Information</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company_name">Company Name</label>
                                                    <input type="text" class="form-control" id="company_name"
                                                        name="company_name" value="{{ $vendorAgency->company_name }}"
                                                        required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="tagline">Tagline</label>
                                                    <input type="text" class="form-control" id="tagline" name="tagline"
                                                        value="{{ $vendorAgency->tagline }}" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="website">Website</label>
                                                    <input type="text" class="form-control" id="website" name="website"
                                                        value="{{ $vendorAgency->website }}" />
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone_number">Phone Number</label>
                                                    <input type="text" class="form-control" id="phone_number"
                                                        name="phone_number" value="{{ $vendorAgency->phone_number }}" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        value="{{ $vendorAgency->email }}" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="desc">Description</label>
                                                    <textarea class="form-control" id="desc" name="desc">{{ $vendorAgency->desc }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Logo Upload Section -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="logo">Company Logo</label>
                                                    <input type="file" class="form-control logo-input" name="logo"
                                                        accept="image/*">
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-center mb-4">
                                                @if (!empty($vendorAgency->logo))
                                                    <img id="logo-preview"
                                                        src="{{ asset('public/uploads/vendoragency/' . $vendorAgency->logo) }}"
                                                        alt="Logo Preview"
                                                        style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px;">
                                                @else
                                                    <img id="logo-preview" src="" alt="Logo Preview"
                                                        style="max-width: 100px; height: auto; display: none;">
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="youtube">Youtube Link</label>
                                                    <input type="url" class="form-control" id="youtube" name="youtube"
                                                        {{ $vendorAgency->youtube ?? '' }} />
                                                </div>
                                            </div>                                          
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="facebook">Facebook Link</label>
                                                    <input type="url" class="form-control" id="facebook" name="facebook"
                                                        {{ $vendorAgency->facebook ?? '' }} />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="twitter">Twitter Link</label>
                                                    <input type="url" class="form-control" id="twitter" name="twitter"
                                                        {{ $vendorAgency->twitter ?? '' }} />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="linkedin">LinkedIn Link</label>
                                                    <input type="url" class="form-control" id="linkedin" name="linkedin"
                                                        {{ $vendorAgency->linkedin ?? '' }} />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="instagram">Instagram Link</label>
                                                    <input type="url" class="form-control" id="instagram" name="instagram"
                                                        {{ $vendorAgency->instagram ?? '' }} />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vendor Categories & Subcategories -->
                                    <div class="card mt-4 p-4">
                                        <h4>Vendor Categories & Subcategories</h4>
                                        <div id="categoriesContainer">
                                            @if(count($selectedCategories) == 0)
                                                <div class="row category-group">
                                                    <div class="col-md-5">
                                                        <label>Vendor Category</label>
                                                        <input type="hidden" name="add_more_cat" value="0">
                                                        <select class="form-control category-select"
                                                            name="vendor_categories[0][category_id]" required
                                                            onchange="loadSubcategories($(this))">
                                                            <option value="">Select Category</option>
                                                            @foreach ($vendorCategories as $category)
                                                                <option value="{{ $category->id }}">
                                                                    {{ $category->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>Vendor Subcategories</label>
                                                        <select class="form-control select2 subcategory-select" multiple
                                                            name="vendor_categories[0][subcategories][]">
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end gap-2">
                                                        <button type="button" class="btn btn-success add-category">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif

                                            <input type="hidden" id="add_more_cat"  value="{{count($selectedCategories)}}">
                                            @foreach ($selectedCategories as $index => $categoryData)
                                                <div class="row category-group align-items-end mt-2">
                                                    <div class="col-md-5">
                                                        @if($index == 0)
                                                            <label>Vendor Category</label>
                                                        @endif
                                                        <select class="form-control category-select"
                                                            name="vendor_categories[{{ $index }}][category_id]"
                                                            required>
                                                            <option value="">Select Category</option>
                                                            @foreach ($vendorCategories as $category)
                                                                <option value="{{ $category->id }}"
                                                                    {{ $categoryData['vendor_categories_id'] == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @php
                                                        $vendorSubcategories = \App\Models\VendorSubCategory::where('vendor_category_id', $categoryData['vendor_categories_id'])->get();                       
                                                        
                                                        $lastIndex = count($selectedCategories) - 1;
                                                    @endphp

                                                    <div class="col-md-5">
                                                        @if($index == 0)
                                                        <label>Vendor Subcategories</label>
                                                        @endif
                                                        <select class="form-control select2 subcategory-select" multiple
                                                            name="vendor_categories[{{ $index }}][subcategories][]">
                                                            @foreach ($vendorSubcategories as $subcategory)
                                                                <option value="{{ $subcategory->id }}" 
                                                                    @if(in_array($subcategory->id, $categoryData['vendor_subcategories_ids'])) selected @endif                                                                    
                                                                >
                                                                {{ $subcategory->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>              
                                                    
                                                    <div class="col-md-2 d-flex align-items-end gap-2">
                                                        @if($index == 0 && count($selectedCategories) == 1)
                                                            <button type="button" class="btn btn-success add-category">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        @endif
                                                        @if($index != 0)                                                        
                                                            <button type="button" class="btn btn-danger remove-category">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            @if($index == $lastIndex)
                                                                <button type="button" class="btn btn-success add-category ml-2">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            @endif
                                                        @endif          
                                                    </div>

                                                </div>
                                            @endforeach                                            
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary next-tab mt-3">
                                        Next <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>

                                <!-- TAB 2: Vendor Products -->
                                <div id="tab2" class="tab-pane fade">
                                    <div class="card p-4">
                                        <h4 class="mb-3">Vendor Products</h4>
                                        <input type="hidden" id="add_more_product"  value="{{count($vendorProducts)}}">
                                        <div id="productContainer">
                                            <!-- Default First Product (No Delete Button) -->
                                            @if(count($vendorProducts) == 0)                                                
                                                <div class="product-group card p-3 mb-4 shadow-sm border">
                                                    <div class="row gy-3">
                                                        <!-- First Row: Product Title & Description -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Product Title</label>
                                                                <input type="text" class="form-control"
                                                                    name="products[0][title]" required />
                                                            </div>
                                                        </div>

                                                        <!-- Second Row: Logo & Preview -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Logo</label>
                                                                <input type="file" class="form-control product-logo-input"
                                                                    name="products[0][logo]"
                                                                    onchange="previewProductLogo(this, 0)" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 d-flex align-items-center">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Logo Preview</label><br>
                                                                <img id="product-logo-preview-0" src=""
                                                                    alt="Logo Preview" class="img-thumbnail"
                                                                    style="max-width: 120px; height: auto; display: none;">
                                                            </div>
                                                        </div>

                                                        <!-- Third Row: Content -->
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Content</label>
                                                                <textarea class="form-control content-editor" name="products[0][desc]" id="content-0" rows="4"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @foreach ($vendorProducts as $pkey => $product)
                                                <div class="product-group card p-3 mb-4 shadow-sm border">
                                                    <div class="row gy-3">
                                                        <!-- First Row: Product Title & Description -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Product Title</label>
                                                                <input type="hidden" name="products[{{$pkey}}][id]" value="{{$product->id}}">
                                                                <input type="text" class="form-control"
                                                                    name="products[{{$pkey}}][title]" value="{{$product->product_title}}" required />
                                                            </div>
                                                        </div>

                                                        <!-- Second Row: Logo & Preview -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Logo</label>
                                                                <input type="file" class="form-control product-logo-input"
                                                                    name="products[0][logo]"
                                                                    onchange="previewProductLogo(this, 0)" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 d-flex align-items-center">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Logo Preview</label><br>

                                                                @if (!empty($product->logo))
                                                                    <img id="product-logo-preview-{{$pkey}}"
                                                                        src="{{ asset('public/uploads/vendoragency_product/' . $product->logo) }}"
                                                                        alt="Logo Preview"
                                                                        style="max-width: 120px; height: auto; border: 1px solid #ddd; padding: 5px;">
                                                                @else
                                                                    <img id="product-logo-preview-{{$pkey}}" src="" alt="Logo Preview"
                                                                        style="max-width: 120px; height: auto; display: none;">
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Third Row: Content -->
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Content</label>
                                                                <textarea class="form-control content-editor" name="products[{{$pkey}}][desc]" id="content-0" rows="4">{{ $product->desc }}</textarea>
                                                            </div>
                                                        </div>

                                                        <!-- Delete Button -->
                                                        @if($pkey != 0)
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button type="button" class="btn btn-danger remove-product">× Remove</button>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                                                  
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn btn-success add-product">
                                                <i class="fas fa-plus"></i> Add Product
                                            </button>
                                            <button type="button" class="btn btn-secondary prev-tab ml-2">
                                                <i class="fas fa-arrow-left"></i> Previous
                                            </button>
                                            <button type="button" class="btn btn-primary next-tab ml-2">
                                                Next <i class="fas fa-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 3: Vendor Blogs -->
                                <div id="tab3" class="tab-pane fade">
                                    <div class="card p-4">
                                        <h4 class="mb-3">Vendor Blogs</h4>
                                        <div id="blogContainer">
                                            <!-- Default First Blog Entry -->
                                            <input type="hidden" id="add_more_blogs"  value="{{count($vendorBlogs)}}">

                                            @if(count($vendorBlogs) == 0)
                                                <div class="blog-group card p-3 mb-4 shadow-sm border">
                                                    <div class="row gy-3">
                                                        <div class="col-md-6">
                                                            <label class="fw-bold">Blog Title</label>
                                                            <input type="text" class="form-control"
                                                                name="blogs[0][title]" />
                                                        </div>

                                                        <!-- Blog Image -->
                                                        <div class="col-md-6">
                                                            <label class="fw-bold">Blog Image</label>
                                                            <input type="file" class="form-control blog-logo-input"
                                                                name="blogs[0][logo]" onchange="previewBlogLogo(this, 0)" />
                                                        </div>
                                                        <div class="col-md-6 d-flex align-items-center">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Logo Preview</label><br>
                                                                <img id="blog-logo-preview-0" src=""
                                                                    class="img-thumbnail blog-logo-preview"
                                                                    style="max-width: 120px; height: auto; display: none;">
                                                            </div>
                                                        </div>

                                                        <!-- Blog Content -->
                                                        <div class="col-md-12">
                                                            <label class="fw-bold">Content</label>
                                                            <textarea class="form-control content-editor" name="blogs[0][desc]" id="blog-content-0" rows="4"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @foreach ($vendorBlogs as $bkey => $blog)
                                                <div class="blog-group card p-3 mb-4 shadow-sm border">
                                                    <div class="row gy-3">
                                                        <div class="col-md-6">
                                                            <label class="fw-bold">Blog Title</label>
                                                            <input type="hidden" name="blogs[{{$bkey}}][id]" value="{{$blog->id}}">
                                                            <input type="text" class="form-control"
                                                                name="blogs[{{$bkey}}][title]" value="{{$blog->title}}" />
                                                        </div>

                                                        <!-- Blog Image -->
                                                        <div class="col-md-6">
                                                            <label class="fw-bold">Blog Image</label>
                                                            <input type="file" class="form-control blog-logo-input"
                                                                name="blogs[{{$bkey}}][logo]" onchange="previewBlogLogo(this, {{$bkey}})" />
                                                        </div>
                                                        <div class="col-md-6 d-flex align-items-center">
                                                            <div class="form-group">
                                                                <label class="fw-bold">Logo Preview</label><br>
                                                                @if (!empty($blog->logo))
                                                                    <img id="blog-logo-preview-{{$bkey}}"
                                                                        src="{{ asset('public/uploads/vendoragency_blog/' . $blog->logo) }}"
                                                                        alt="Logo Preview"
                                                                        style="max-width: 120px; height: auto; border: 1px solid #ddd; padding: 5px;">
                                                                @else
                                                                    <img id="blog-logo-preview-{{$bkey}}" src="" alt="Logo Preview"
                                                                        style="max-width: 120px; height: auto; display: none;">
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Blog Content -->
                                                        <div class="col-md-12">
                                                            <label class="fw-bold">Content</label>
                                                            <textarea class="form-control content-editor" name="blogs[{{$bkey}}][desc]" id="blog-content-{{$bkey}}" rows="4">{{ $blog->desc }}</textarea>
                                                        </div>

                                                        <!-- Delete Button -->
                                                        @if($bkey != 0)
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button type="button" class="btn btn-danger remove-blog">× Remove</button>
                                                        </div>
                                                        @endif

                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Blog Buttons -->
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn btn-success add-blog">
                                                <i class="fas fa-plus"></i> Add Blog
                                            </button>
                                            <button type="button" class="btn btn-secondary prev-tab ml-2">
                                                <i class="fas fa-arrow-left"></i> Previous
                                            </button>
                                            <button type="button" class="btn btn-primary next-tab ml-2">
                                                Next <i class="fas fa-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 4: Vendor Releases -->
                                <div id="tab4" class="tab-pane fade">
                                    <div class="card p-4">
                                        <h4 class="mb-3">Vendor Releases</h4>
                                        <div id="releaseContainer">
                                            <!-- Default First Release Entry -->
                                            <input type="hidden" id="add_more_releases"  value="{{count($vendorNews)}}">
                                            @if(count($vendorNews) == 0)
                                                <div class="release-group card p-3 mb-4 shadow-sm border">
                                                    <div class="row gy-3">
                                                        <div class="col-md-6">
                                                            <label class="fw-bold">Release Title</label>
                                                            <input type="text" class="form-control"
                                                                name="releases[0][title]" />
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="fw-bold">Content</label>
                                                            <textarea class="form-control content-editor" name="releases[0][desc]" id="release-content-0" rows="4"></textarea>
                                                        </div>
                                                    </div>
                                                </div>                                        
                                            @endif

                                            @foreach ($vendorNews as $rkey => $release)
                                                <div class="release-group card p-3 mb-4 shadow-sm border">
                                                    <div class="row gy-3">
                                                        <div class="col-md-6">
                                                            <label class="fw-bold">Release Title</label>
                                                            <input type="hidden" name="releases[{{$rkey}}][id]" value="{{$release->id}}">
                                                            <input type="text" class="form-control"
                                                                name="releases[{{$rkey}}][title]" value="{{$release->title}}" />
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="fw-bold">Content</label>
                                                            <textarea class="form-control content-editor" name="releases[{{$rkey}}][desc]" id="release-content-{{$rkey}}" rows="4">{{ $release->desc }}</textarea>
                                                        </div>

                                                        <!-- Delete Button -->
                                                        @if($rkey != 0)
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button type="button" class="btn btn-danger remove-release">× Remove</button>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Release Buttons -->
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn btn-success add-release">
                                                <i class="fas fa-plus"></i> Add Release
                                            </button>
                                            <button type="button" class="btn btn-secondary prev-tab ml-2">
                                                <i class="fas fa-arrow-left"></i> Previous
                                            </button>
                                            <button type="submit" class="btn btn-success ml-2">
                                                <i class="fas fa-check"></i> Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('public/assets/dashboard/plugins/select2/css/select2.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('public/assets/dashboard/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" />
@endsection


@section('script')
    <script src="{{ asset('public/assets/dashboard/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
    <script src="{{ asset('public/assets/dashboard/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/assets/dashboard/plugins/speakingurl/speakingurl.min.js') }}"></script>
    <script src="{{ asset('public/assets/dashboard/plugins/slugify/slugify.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/44.1.0/classic/ckeditor.js"></script>

    <script>
        $(document).ready(function() {
            $(".select2").select2({
                placeholder: "Select subcategories",
                allowClear: true
            });

            // TAB 1: Vendor Agency
            $(".logo-input").on("change", function(event) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $("#logo-preview").attr("src", e.target.result).show();
                };
                reader.readAsDataURL(event.target.files[0]);
            });

            $(".next-tab").click(function(e) {
                e.preventDefault();

                let activeTab = $(".nav-tabs .active").attr("href"); // Get current tab ID

                if (validateStep(activeTab)) {
                    $(".nav-tabs .active").parent().next().find("a").tab("show"); // Move to next step
                }
            });

            $(".prev-tab").click(function() {
                $(".nav-tabs .active").parent().prev().find("a").tab("show");
            });

            function validateStep(activeTab) {
                console.log("activeTab:" + activeTab);
                let isValid = true;

                // Clear previous error messages
                $(".error-message").remove();

                if (activeTab === "#tab1") {
                    let companyName = $("#company_name").val().trim();
                    let email = $("#email").val().trim();
                    let phone = $("#phone_number").val().trim();
                    let categorySelected = $(".category-select").val();
                    let website = $("#website").val().trim();

                    if (companyName === "") {
                        showError("#company_name", "Company Name is required.");
                        isValid = false;
                    }
                    if (validateEmail(email)) {
                        showError("#email", "Enter a valid email.");
                        isValid = false;
                    }
                    if (!validateURL(website)) {
                        showError("#website", "Enter a valid website URL.");
                        isValid = false;
                    }
                    if (!validatePhone(phone)) {
                        showError("#phone_number", "Enter a valid phone number in (XXX) XXX-XXXX format.");
                        isValid = false;
                    }
                    if (!categorySelected) {
                        showError(".category-select", "Please select at least one category.");
                        isValid = false;
                    }
                }

                if (activeTab === "#tab2") {
                    let products = $(".product-group").length;
                    if (products === 0) {
                        showError("#productContainer", "Add at least one product.");
                        isValid = false;
                    }
                }

                if (activeTab === "#tab3") {
                    let blogs = $(".blog-group").length;
                    if (blogs === 0) {
                        showError("#blogContainer", "Add at least one blog.");
                        isValid = false;
                    }
                }

                return isValid;
            }

            function showError(selector, message) {
                $(selector).after('<div class="text-danger error-message">' + message + '</div>');
            }

            function validateEmail(email) {
                let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var a = emailPattern.test(email);
                console.log(a);
            }

            function validateURL(url) {
                const pattern = /^(https?:\/\/)[\w.-]+\.[a-z]{2,}.*$/i;
                return pattern.test(url);
            }

            function validatePhone(phone) {
                const pattern = /^\(\d{3}\) \d{3}-\d{4}$/;
                return pattern.test(phone);
            }

            // Function to load subcategories dynamically
            function loadSubcategories(categorySelect) {
                let categoryId = categorySelect.val();
                let subcategorySelect = categorySelect.closest(".category-group").find(".subcategory-select");

                if (categoryId) {
                    $.ajax({
                        url: "{{ route('dashboard.vendor_agencies.getSubcategories') }}",
                        type: "GET",
                        data: {
                            category_id: categoryId
                        },
                        success: function(data) {
                            subcategorySelect.empty();
                            data.forEach(function(subcategory) {
                                subcategorySelect.append('<option value="' + subcategory.id +
                                    '">' + subcategory.title + '</option>');
                            });
                            subcategorySelect.trigger("change"); // Refresh select2
                        }
                    });
                } else {
                    subcategorySelect.empty();
                }
            }

            // Attach event listener for category selection (including dynamically added elements)
            $(document).on("change", ".category-select", function() {
                loadSubcategories($(this));
            });

            // Add new category-subcategory row
            $(document).on("click", ".add-category", function() {
                let index = $("#add_more_cat").val();
                $("#add_more_cat").val(parseInt(index) + 1);
                let newRow = `
                <div class="row category-group mt-2">
                    <div class="col-md-5">
                        <label></label>
                        <select class="form-control category-select" name="vendor_categories[${index}][category_id]" required>
                            <option value="">Select Category</option>
                            @foreach ($vendorCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label></label>
                        <select class="form-control select2 subcategory-select" multiple name="vendor_categories[${index}][subcategories][]"></select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-danger remove-category">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-success add-category ml-2">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>`;

                $("#categoriesContainer").append(newRow);
                $(".select2").select2(); // Reinitialize select2 for new elements
                updateButtons();
            });

            // Remove category row
            $(document).on("click", ".remove-category", function() {
                if ($(".category-group").length > 1) {
                    $(this).closest(".category-group").remove();
                    updateButtons();
                }
            });

            // Ensure only the last row has the "+" button
            function updateButtons() {
                $(".add-category").remove();
                $(".category-group:last .col-md-2").append(
                    '<button type="button" class="btn btn-success add-category ml-2"><i class="fas fa-plus"></i></button>');
            }

            $(".category-select").change(function() {
                let categoryId = $(this).val();
                let subcategoryDropdown = $(this).closest(".category-group").find(".subcategory-select");

                if (categoryId) {
                    $.ajax({
                        url: "{{ route('dashboard.vendor_agencies.getSubcategories') }}",
                        type: "GET",
                        data: {
                            category_id: categoryId
                        },
                        success: function(data) {
                            subcategoryDropdown.empty();
                            $.each(data, function(key, value) {
                                subcategoryDropdown.append('<option value="' + value
                                    .id + '">' + value.title + '</option>');
                            });
                        }
                    });
                }
            });


            $(".content-editor").summernote({
                placeholder: "Write content...",
                height: 200,
            });

            // TAB 2: Vendor Products,Vendor Blogs,Vendor Releases 
            // Add new product dynamically
            $(".add-product").click(function() {
                let productIndex = $('#add_more_product').val();
                $('#add_more_product').val(parseInt(productIndex) + 1);
                let newProduct = $(`
                <div class="product-group card p-3 mb-3 shadow-sm border position-relative">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-bold">Product Title</label>
                                <input type="text" class="form-control" name="products[${productIndex}][title]" required />
                            </div>
                        </div>                    
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-bold">Logo</label>
                                <input type="file" class="form-control product-logo-input" name="products[${productIndex}][logo]" data-index="${productIndex}" onchange="previewProductLogo(this)" />
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-group">
                                <label class="fw-bold">Logo Preview</label><br>
                               <img id="product-logo-preview-${productIndex}" src="" alt="Logo Preview" class="img-thumbnail product-logo-preview" style="max-width: 120px; height: auto; display: none;">
                            </div>
                        </div>

                        <!-- Third Row: Content -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="fw-bold">Content</label>
                                <textarea class="form-control content-editor" name="products[${productIndex}][desc]" id="content-${productIndex}" rows="4"></textarea>
                            </div>
                        </div>

                        <!-- Delete Button -->
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-danger remove-product">× Remove</button>
                        </div>
                    </div>
                </div>
                `);

                // Append new product block
                $("#productContainer").append(newProduct);

                // Initialize Summernote for the newly added content editor
                $(`#content-${productIndex}`).summernote({
                    placeholder: "Write content...",
                    height: 200
                });

                productIndex++;
            });

            // Delete a product group dynamically
            $(document).on("click", ".remove-product", function() {
                $(this).closest(".product-group").remove();
            });

            // Add new blog dynamically
            $(".add-blog").click(function() {
                let blogIndex = $('#add_more_blogs').val();
                $('#add_more_blogs').val(parseInt(blogIndex) + 1);
                let newBlog = $(`
                <div class="blog-group card p-3 mb-3 shadow-sm border position-relative">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-bold">Blog Title</label>
                                <input type="text" class="form-control" name="blogs[${blogIndex}][title]" required />
                            </div>
                        </div>                        

                        <!-- Blog Image Upload -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-bold">Blog Image</label>
                                <input type="file" class="form-control blog-logo-input" name="blogs[${blogIndex}][logo]" data-index="${blogIndex}" onchange="previewBlogLogo(this, ${blogIndex})" />
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-group">
                                <label class="fw-bold">Logo Preview</label><br>
                                <img id="blog-logo-preview-${blogIndex}" src="" alt="Logo Preview" class="img-thumbnail blog-logo-preview" style="max-width: 120px; height: auto; display: none;">
                            </div>
                        </div>

                        <!-- Blog Content Editor -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="fw-bold">Content</label>
                                <textarea class="form-control content-editor" name="blogs[${blogIndex}][desc]" id="blog-content-${blogIndex}" rows="4"></textarea>
                            </div>
                        </div>

                        <!-- Remove Blog Button -->
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-danger remove-blog">× Remove</button>
                        </div>
                    </div>
                </div>
                `);

                // Append new blog block
                $("#blogContainer").append(newBlog);

                // Initialize Summernote for the newly added content editor
                $(`#blog-content-${blogIndex}`).summernote({
                    placeholder: "Write blog content...",
                    height: 200
                });

                blogIndex++;
            });

            // Remove blog dynamically
            $(document).on("click", ".remove-blog", function() {
                $(this).closest(".blog-group").remove();
            });

            // Add new release dynamically
            $(".add-release").click(function() {
                let newRelease = $(`
                <div class="release-group card p-3 mb-3 shadow-sm border position-relative">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Release Title</label>
                            <input type="text" class="form-control" name="releases[${releaseIndex}][title]" required />
                        </div>

                        <!-- Release Content -->
                        <div class="col-md-12">
                            <label class="fw-bold">Content</label>
                            <textarea class="form-control content-editor" name="releases[${releaseIndex}][desc]" id="release-content-${releaseIndex}" rows="4"></textarea>
                        </div>

                        <!-- Remove Release Button -->
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-danger remove-release">× Remove</button>
                        </div>
                    </div>
                </div>
                `);

                // Append new release block
                $("#releaseContainer").append(newRelease);

                // Initialize Summernote for the newly added content editor
                $(`#release-content-${releaseIndex}`).summernote({
                    placeholder: "Write release content...",
                    height: 200
                });

                releaseIndex++;
            });

            // Remove release dynamically
            $(document).on("click", ".remove-release", function() {
                $(this).closest(".release-group").remove();
            });
        });

        function previewProductLogo(input, index = null) {
            let file = input.files[0]; // Get the selected file
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let preview;

                    // If index is provided, use the direct ID selector
                    if (index !== null) {
                        preview = document.getElementById(`product-logo-preview-${index}`);
                    } else {
                        // Otherwise, find the closest preview image dynamically
                        preview = $(input).closest(".product-group").find(".product-logo-preview")[0];
                    }

                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        // Function to preview blog logo
        function previewBlogLogo(input, index = null) {
            let file = input.files[0]; // Get the selected file
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let preview;

                    // If index is provided, use the direct ID selector
                    if (index !== null) {
                        preview = document.getElementById(`blog-logo-preview-${index}`);
                    } else {
                        // Otherwise, find the closest preview image dynamically
                        preview = $(input).closest(".blog-group").find(".blog-logo-preview")[0];
                    }

                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        // Function to preview release logo
        function previewReleaseLogo(input, index = null) {
            let file = input.files[0]; // Get the selected file
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let preview;

                    // If index is provided, use the direct ID selector
                    if (index !== null) {
                        preview = document.getElementById(`release-logo-preview-${index}`);
                    } else {
                        // Otherwise, find the closest preview image dynamically
                        preview = $(input).closest(".release-group").find(".release-logo-preview")[0];
                    }

                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        $('#phone_number').on('input', function() {
            let value = $(this).val().replace(/\D/g, ''); // Remove all non-numeric characters

            if (value.length > 3 && value.length <= 6) {
                value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
            } else if (value.length > 6) {
                value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
            }

            $(this).val(value);
        });
    </script>
@endsection
