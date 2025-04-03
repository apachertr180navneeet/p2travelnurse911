@extends('dashboard.master')
@section('title', 'Company List - ' . config('app.name'))

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Company List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Company List</li>
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

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Company Name</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Phone</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Vendor Cateogry</th>
                                            <th class="text-center">Vendor Sub Cateogry</th>
                                            <th class="text-center">About</th>
                                            <th class="text-center">Website</th>
                                            <th class="text-center">Press Releases</th>
                                            <th class="text-center">Logo</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($contactLists as $contactList)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $contactList->company_name }}</td>
                                            <td class="text-center">{{ $contactList->email }}</td>
                                            <td class="text-center">{{ $contactList->phone }}</td>
                                            <td class="text-center">{{ $contactList->address }}</td>
                                            <td class="text-center">{{ $contactList->title }}</td>
                                            <td class="text-center">{{ $contactList->vendor_sub_categories_title }}</td>
                                            <td class="text-center">{{ $contactList->about }}</td>
                                            <td class="text-center">{{ $contactList->website }}</td>
                                            <td class="text-center">{{ $contactList->press_releases }}</td>
                                            <td class="text-center">
                                                @if($contactList->logo)
                                                    <img src="{{ asset('public/uploads/company_logo/' . $contactList->logo) }}" alt="Company Logo" width="50" height="50">
                                                @else
                                                    <span class="badge badge-secondary">No Logo</span>
                                                @endif
                                            </td>                                            
                                            <td class="text-center">
                                                <form action="{{ route( "dashboard.vendor_agency.delete_company_list")}}">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$contactList->id}}">
                                                    <button type="submit" class="btn btn-danger deletebtn">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No Contacts found!</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                {{ $contactLists->links() }}
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
$('.deletebtn').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    Swal.fire({
        title: 'Are you sure?',
        type: 'warning',
        icon: 'warning',
        text: 'This company will delete!',
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