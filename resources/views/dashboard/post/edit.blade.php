@extends('dashboard.master')
@section('title', 'Edit Post')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Post</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route("dashboard.home") }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route("dashboard.posts.index") }}">All Posts</a></li>
                        <li class="breadcrumb-item active">Edit Post</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Post</h3>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                @foreach ($errors->all() as $error)
                                <p class="m-0">{{ $error }}</p>
                                @endforeach
                            </div>
                            @endif
                            @if (session("success"))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-check"></i> Success!</h5>
                                <p class="m-0">{{ session("success") }}</p>
                            </div>
                            @endif
                            <form action="{{ route("dashboard.posts.update", $post->id) }}" enctype="multipart/form-data" method="POST">
                                @csrf
                                @method("PUT")
                                <div class="row">
                                    <div class="col-md-8 mx-auto">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" value="{{ $post->title }}"/>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>
                                                <input type="radio" name="option" value="1" id="option-slug"
                                                    {{ old('option', isset($post) && !$post->is_external_url && !filter_var($post->slug, FILTER_VALIDATE_URL) ? 'checked' : '') }}>
                                                Slug
                                            </label>
                                            <label>
                                                <input type="radio" name="option" value="2" id="option-external"
                                                    {{ old('option', isset($post) && ($post->is_external_url || filter_var($post->slug, FILTER_VALIDATE_URL)) ? 'checked' : '') }}>
                                                External URL
                                            </label>
                                        </div>

                                        <div class="form-group" id="slug-div" style="{{ isset($post) && (filter_var($post->slug, FILTER_VALIDATE_URL) || $post->is_external_url) ? 'display: none;' : '' }}">
                                            <label for="slug">Slug</label>
                                            <input type="text" class="form-control" id="slug" name="slug" placeholder="Enter slug" 
                                            value="{{ empty($post->is_external_url) ? old('slug', $post->slug) : '' }}" >
                                        </div>

                                        <div class="form-group" id="external-url-div" style="{{ isset($post) && (filter_var($post->slug, FILTER_VALIDATE_URL) || $post->is_external_url) ? 'display: block;' : 'display: none;' }}">
                                            <label for="link">External URL</label>
                                            <input type="url" placeholder="Link" id="link" name="link" 
                                               value="{{ isset($post) && $post->is_external_url ? old('link', $post->slug) : '' }}" 
                                                class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label for="content">Content</label>
                                            <textarea class="form-control" id="content" name="content" placeholder="Write content">{{ $post->content }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mx-auto">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <select class="form-control" name="category" id="category" style="width: 100%;">
                                                @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ $post->news_category_id == $category->id ? "selected" : "" }}>{{ $category->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="tags">Tags</label>
                                            <div class="select2-purple">
                                                <select multiple="multiple" data-placeholder="Select tag" data-dropdown-css-class="select2-purple" class="form-control" name="tags[]" id="tags" style="width: 100%;">
                                                    @foreach ($tags as $tag)
                                                    <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="thumbnail">Thumbnail</label>
                                            <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*"/>
                                            <img id="thumbnailpreview" class="img-fluid img-thumbnail mt-3" src="{{ asset("public/uploads/news/".$post->thumbnail) }}"/>
                                        </div>                                      
                                      
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" name="status" id="status">
                                                @if (auth()->user()->role != 1)
                                                <option value="1" {{ $post->status ? "selected" : "" }}>Publish</option>
                                                @endif
                                                <option value="0" {{ !$post->status ? "selected" : "" }}>Draft</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group mt-3">
                                            <label for="posted_date">Posted Date</label>
                                            <input type="date" class="form-control" name="posted_date" id="posted_date"
                                                   value="{{ old('posted_date', $post->posted_date ? date('Y-m-d',strtotime($post->posted_date)) : '') }}">
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary" type="submit">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section("style")
<link rel="stylesheet" href="{{ asset("public/assets/dashboard/plugins/select2/css/select2.min.css") }}"/>
<link rel="stylesheet" href="{{ asset("public/assets/dashboard/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css") }}"/>
@endsection

@section("script")
<script src="{{ asset("public/assets/dashboard/plugins/sweetalert2/sweetalert2.all.js") }}"></script>
<script src="{{ asset("public/assets/dashboard/plugins/select2/js/select2.full.min.js") }}"></script>
<script src="{{ asset("public/assets/dashboard/plugins/speakingurl/speakingurl.min.js") }}"></script>
<script src="{{ asset("public/assets/dashboard/plugins/slugify/slugify.min.js") }}"></script>
<script>
    $(document).ready(function() {
        $('#title').on("input", () => {
            $('#slug').val($.slugify($('#title').val()));
        });
        $('#category').select2({
            theme: 'bootstrap4'
        });

        $('#tags').select2({
            tags: true,
        });
        @if($post->tags_count > 0)
        var tags = [];
        @foreach($post->tags as $tag)
        tags.push('{{ $tag->name }}');
        @endforeach
        $('#tags').val(tags).trigger('change');
        @endif
        $("#content").summernote({
            placeholder: 'Write content...',
            height: 200,
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
    document.addEventListener('DOMContentLoaded', function() {
        const slugDiv = document.getElementById('slug-div');
        const externalUrlDiv = document.getElementById('external-url-div');
        const optionSlug = document.getElementById('option-slug');
        const optionExternal = document.getElementById('option-external');
        const slugInput = document.getElementById('slug'); // Slug input field
        const externalUrlInput = document.getElementById('link'); // External URL input field

        // Show Slug option by default or when not a URL
        if (optionSlug.checked) {
            slugDiv.style.display = 'block';
            externalUrlDiv.style.display = 'none';
        } else if (optionExternal.checked) {
            slugDiv.style.display = 'none';
            externalUrlDiv.style.display = 'block';
        }

        // When 'Slug' option is clicked
        optionSlug.addEventListener('click', function() {
            slugDiv.style.display = 'block';
            externalUrlDiv.style.display = 'none';

            // Clear external URL input if slug is selected
            //externalUrlInput.value = '';
        });

        // When 'External URL' option is clicked
        optionExternal.addEventListener('click', function() {
            slugDiv.style.display = 'none';
            externalUrlDiv.style.display = 'block';

            // Clear slug input if external URL is selected
            //slugInput.value = '';
        });

        // Monitor changes in the 'Slug' input field and clear the 'External URL' input if slug has a value
        slugInput.addEventListener('input', function() {
            if (slugInput.value.trim() != '') {
                externalUrlInput.value = ''; // Clear external URL field
            }
        });

        // Monitor changes in the 'External URL' input field and clear the 'Slug' input if external URL has a value
        externalUrlInput.addEventListener('input', function() {
            if (externalUrlInput.value.trim() != '') {
                slugInput.value = ''; // Clear slug field
            }
        });
    });
</script>





@endsection