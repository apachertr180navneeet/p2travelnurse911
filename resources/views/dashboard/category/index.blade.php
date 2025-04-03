@extends('dashboard.master')
@section('title', 'All Categories - ' . config('app.name'))

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Categories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Categories</li>
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
                            <h3 class="card-title">All Categories</h3>
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
                            <div class="table-responsive">
                                <table class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Image</th>
                                            <th class="text-center">Title</th>
                                            <th class="text-center">Total Posts</th>
                                            <th class="text-center">Clicks</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody  id="sortable">
                                        @forelse ($categories as $category)
                                        <tr data-id="{{ $category->id }}">
                                            <td class="text-center">{{ $loop->index + $categories->firstItem() }}</td>
                                            <td class="text-center">
                                                <img width="100px" height="100px" src="{{ asset("public/uploads/category/".($category->image ?? "default.webp")) }}" alt="{{ $category->title }}"/>
                                            </td>
                                            <td class="text-center">{{ $category->title }}</td>
                                            <td class="text-center">{{ $category->posts_count }}</td>
                                            <td class="text-center">{{ $category->clicks }}</td>
                                            <td class="text-center"><a href="{{ route("dashboard.categories.status", $category->id) }}"><span class="badge bg-{{ $category->status ? "success" : "warning" }}">{{ $category->status ? "Active" : "Inactive" }}</span></a></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">                                                    
                                                    <a href="{{ route("dashboard.categories.edit", $category->id) }}" class="btn btn-warning">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <form action="{{ route("dashboard.categories.destroy", $category->id) }}" method="POST">
                                                        @method("DELETE")
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger deletebtn">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No category found!</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                            {{ $categories->links() }}
                            </ul>
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
$(document).ready(function() {

    $("#sortable").sortable({
        update: function(event, ui) {
            var order = [];
            $("#sortable tr").each(function(index) {
                var newSequence = index + 1; // Start sequence from 1

                // Update first column in UI immediately
                $(this).find("td:first").text(newSequence);

                order.push({
                    id: $(this).data("id"),
                    sequence: newSequence
                });
            });
            $.ajax({
                url: "{{ route('dashboard.categories.sort') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order: order
                },
                success: function(response) {
                    //console.log(response);
                }
            });
        }
    });
    $('.deletebtn').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    Swal.fire({
        title: 'Are you sure?',
        type: 'warning',
        icon: 'warning',
        text: 'All posts of this category will delete!',
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
});
</script>
@endsection
