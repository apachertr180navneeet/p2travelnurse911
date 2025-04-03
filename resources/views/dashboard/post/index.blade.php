@extends('dashboard.master')
@section('title', 'All Posts')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All News</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All News</li>
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
                            <h3 class="card-title">All News</h3>
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
                            
                                
                            <form method="GET" action="{{ route('dashboard.posts.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="title" class="form-control" placeholder="Title" value="{{ request('title') }}">
                                    </div>
                        
                                    <div class="col-md-3">
                                        <select name="category" class="form-control">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                        
                                    <div class="col-md-3">
                                        <input type="date" name="posted_date" class="form-control" value="{{ request('posted_date') }}">
                                    </div>
                        
                                    <div class="col-md-3">
                                        <select name="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        </select>
                                    </div>
                        
                                    <div class="col-md-3 mt-2">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('dashboard.posts.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                                
                            <div class="table-responsiv mt-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Title</th>
                                            <th class="text-center">Author</th>
                                            <th class="text-center">Category</th>
                                            <th class="text-center">Tags</th>
                                            <th class="text-center">Status</th>                                                                                        
                                            <th class="text-center">Clicks</th>                                            
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($posts as $post)                                        
                                        <tr>
                                            <td class="text-center">{{ $loop->index + $posts->firstItem() }}</td>
                                            <td>{{ $post->title }}</td>
                                            <td class="text-center">{{ $post->user->name ?? '-' }}</td>
                                            <td class="text-center">{{ $post->newscategory->title ?? '-' }}</td>
                                            <td class="text-center">
                                                @forelse ($post->tags as $tag)
                                                <span class="badge bg-primary">{{ $tag->name }}</span>
                                                @empty
                                                <span class="badge bg-danger">Empty</span>
                                                @endforelse
                                            </td>
                                            <td class="text-center"><a href="{{ route("dashboard.posts.status", $post->id) }}"><span class="badge bg-{{ $post->status ? "success" : "danger" }}">{{ $post->status ? "Published" : "Draft" }}</span></a></td>                                                                                        
                                            <td class="text-center">{{ $post->views }}</td>                                            
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    
                                                    <a href="{{ route("dashboard.posts.edit", $post->id) }}" class="btn btn-warning">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <form action="{{ route("dashboard.posts.destroy", $post->id) }}" method="POST">
                                                        @method("DELETE")
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger deletebtn">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="11" class="text-center">No post found!</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                {!! $posts->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section("script")
<script src="{{ asset("public/assets/dashboard/plugins/sweetalert2/sweetalert2.all.js") }}"></script>
<script>
$('.deletebtn').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    Swal.fire({
        title: 'Are you sure?',
        type: 'warning',
        icon: 'warning',
        text: 'All comments of this post will delete!',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.value) {
            form.submit();
        }
    });
});
</script>
@endsection
