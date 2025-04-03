@extends('dashboard.master')
@section('title', 'Agency Reviews & Feeback - ' . config('app.name'))

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Vendors Reviews & Feeback </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Vendors Reviews & Feeback </li>
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
                        <div class="card-body">

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
                                            <th class="text-center">Vendors</th>
                                            <th class="text-center">User Name</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Rating</th>
                                            <th class="text-center">Comments</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($agencyReviews as $review)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $review->company_name }}</td>
                                            <td class="text-center">{{ $review->user_name }}</td>
                                            <td class="text-center">{{ $review->email }}</td>
                                            <td class="text-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $review->rating)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                                ({{ $review->rating }}/5)
                                            </td>
                                            <td class="text-center">{{ $review->review_text }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center" style="gap: 10px;">                                                                                       
                                                    @if($review->is_approved == 1) 
                                                        <span style="font-weight:600" class="mt-1 text-success">Approved</span>
                                                    @elseif($review->is_approved == 2)
                                                        <span style="font-weight:600" class="mt-1 text-danger">Declined</span>
                                                    @else
                                                        <a href="{{ route('dashboard.vendor_agency.review-update', ['vendor_agency_id' => $review->id, 'is_approved' => 1]) }}" class="btn btn-success btn-sm">Approve</a>
                                                        <a href="{{ route('dashboard.vendor_agency.review-update', ['vendor_agency_id' => $review->id, 'is_approved' => 2]) }}" class="btn btn-warning btn-sm">Decline</a>
                                                    @endif
                                                    <form action="{{ route( "dashboard.vendor_agency.review-update")}}">
                                                        @csrf
                                                        <input type="hidden" name="vendor_agency_id" value="{{$review->id}}">
                                                        <button type="submit" class="btn btn-danger deletebtn">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No Reviews found!</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                {{ $agencyReviews->links() }}
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
<script src="{{ asset("assets/dashboard/plugins/sweetalert2/sweetalert2.all.js") }}"></script>
<script>
$('.deletebtn').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    Swal.fire({
        title: 'Are you sure?',
        type: 'warning',
        icon: 'warning',
        text: 'This review will delete!',
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