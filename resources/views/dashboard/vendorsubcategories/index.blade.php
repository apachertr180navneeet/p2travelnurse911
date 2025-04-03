@extends('dashboard.master')
@section('title', 'All Subcategories - ' . config('app.name'))

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Subcategories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Subcategories</li>
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
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title">All Subcategories</h3>
                            <div class="ml-auto d-flex">
                                <!-- Trashed Subcategories -->
                                <a href="{{ route('dashboard.vendorsubcategories.trashed') }}" class="btn btn-warning me-2">
                                    <i class="fas fa-trash"></i> Trashed Subcategories
                                </a>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <!-- New Subcategory -->
                                <a href="{{ route('dashboard.vendorsubcategories.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> New Subcategory
                                </a>
                            </div>
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
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            {{-- <th class="text-center">Image</th> --}}
                                            <th class="text-center">Subcategory</th>
                                            <th class="text-center">Category</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @forelse ($subcategories as $subcategory)
                                        <tr>
                                            <td class="text-center">{{ $loop->index + $subcategories->firstItem() }}</td>
                                            {{-- <td class="text-center">
                                                <img width="100px" height="100px" src="{{ asset("uploads/vendorsubcategory/".($subcategory->image ?? "default.webp")) }}" alt="{{ $subcategory->title }}"/>
                                            </td> --}}
                                            <td class="text-center">{{ $subcategory->title }}</td>
                                            <td class="text-center">
                                                {{ $subcategory->vendorcategory->title ?? 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.vendorsubcategories.status', $subcategory->id) }}">
                                                    <span class="badge bg-{{ $subcategory->status ? 'success' : 'warning' }}">
                                                        {{ $subcategory->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">                                                    
                                                    <a href="{{ route("dashboard.vendorsubcategories.edit", $subcategory->id) }}" class="btn btn-warning">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <form action="{{ route("dashboard.vendorsubcategories.destroy", $subcategory->id) }}" method="POST">
                                                        @method("DELETE")
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger deletebtn">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No subcategories found!</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                {{ $subcategories->links() }}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset("assets/dashboard/plugins/sweetalert2/sweetalert2.all.js") }}"></script>
<script>
$('.deletebtn').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    Swal.fire({
        title: 'Are you sure?',
        type: 'warning',
        icon: 'warning',
        text: 'This subcategory will be deleted permanently!',
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