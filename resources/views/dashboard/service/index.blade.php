@extends('dashboard.master')
@section('title', 'All service - ' . config('app.name'))

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Service</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Service</li>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title">All Service</h3>
                                </div>
                                <div class="col-md-6 d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addServiceModel">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Marketplace</th>
                                            <th class="text-center">Service Name</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($services as $service)
                                        <tr>
                                            <td class="text-center">{{ $loop->index + $services->firstItem() }}</td>

                                            <td class="text-center">
                                                    {{ $service->marketplacedata->title ?? 'N/A' }}
                                            </td>


                                            <td class="text-center name">{{ $service->name ?? 'N/A' }}</td>
                                            <td class="text-center status">
                                                <span class="badge bg-{{ $service->status == 'active' ? 'success' : 'warning' }}">
                                                    {{ $service->status == 'active' ? 'Approve' : 'Decline' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <button type="button" class="btn btn-danger delete-btn"
                                                        data-id="{{ $service->id }}">
                                                        Delete
                                                    </button>
                                                    @if ($service->status == 'active')
                                                    <button type="button" class="btn btn-danger status-btn mr-2" style="margin-left: 2%;"
                                                        data-id="{{ $service->id }}" data-status="inactive">
                                                        Decline
                                                    </button>
                                                    @else
                                                    <button type="button" class="btn btn-success status-btn mr-2" style="margin-left: 2%;"
                                                        data-id="{{ $service->id }}" data-status="active">
                                                        Approve
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="17" class="text-center">No service ads found!</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="card-footer clearfix">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-end m-0">
                                    @if ($services->onFirstPage())
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                                    </li>
                                    @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $services->previousPageUrl() }}" tabindex="-1">Previous</a>
                                    </li>
                                    @endif

                                    @foreach ($services->getUrlRange(1, $services->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $services->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                    @endforeach

                                    @if ($services->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $services->nextPageUrl() }}">Next</a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#">Next</a>
                                    </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

// Add Service Model
<!-- Modal -->
<div class="modal fade" id="addServiceModel"/>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addServiceModelLabel">Add Service</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
            <div class="form-group">
              <label for="marketplace">Markateplace</label>
              <select class="form-control" id="marketplace">
                @foreach ($marketplaces as $marketplace )
                    <option value="{{ $marketplace->id }}">{{ $marketplace->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" placeholder="Enter Name">
              </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveservice">Save</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<!-- Bootstrap JS (add this before closing </body> tag) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#saveservice').click(function() {
            var marketplace = $('#marketplace').val();
            var name = $('#name').val();
    
            $.ajax({
                url: "{{ route("dashboard.services.store") }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    marketplace: marketplace,
                    name: name
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#addServiceModelLabel').modal('hide');
                        location.reload(); // Reload the page to see the new entry
                    } else {
                        alert("Something went wrong!");
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = "";
                    $.each(errors, function(key, value) {
                        errorMessage += value + "\n";
                    });
                    alert(errorMessage);
                }
            });
        });
    });

    $(document).on('click', '.delete-btn', function () {
        let serviceId = $(this).data('id');
        alert(serviceId + ' deleted');
    
        if (confirm("Are you sure you want to delete this service?")) {
            $.ajax({
                url: "{{ route("dashboard.services.delete") }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    serviceId: serviceId
                },
                success: function (response) {
                    alert(response.message);
                    location.reload(); // Refresh page after deletion
                },
                error: function (xhr) {
                    alert(xhr.responseJSON.message);
                }
            });
        }
    });


    $(document).on('click', '.status-btn', function() {
        var serviceId = $(this).data('id');
        var newStatus = $(this).data('status');

        $.ajax({
            url: "{{ route("dashboard.services.status") }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: serviceId,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Reload the page to update status visually
                } else {
                    alert("Failed to update status.");
                }
            }
        });
    });
</script>
@endsection