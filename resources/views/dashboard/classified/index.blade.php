@extends('dashboard.master')
@section('title', 'All Classified - ' . config('app.name'))

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Classifieds</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Classifieds</li>
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
                            <h3 class="card-title">All Classifieds</h3>
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
                            <div class="mb-3">
                                <input type="text" id="search-marketplace" class="form-control d-inline-block w-auto" placeholder="Search Marketplace">
                                <input type="text" id="search-name" class="form-control d-inline-block w-auto" placeholder="Search Name">
                                <input type="text" id="search-phone" class="form-control d-inline-block w-auto" placeholder="Search Phone">
                                <input type="text" id="search-email" class="form-control d-inline-block w-auto" placeholder="Search Email">
                                <select id="search-status" class="form-control d-inline-block w-auto">
                                    <option value="">All Status</option>
                                    <option value="Approve">Approve</option>
                                    <option value="Decline">Decline</option>
                                </select>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Marketplace</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Phone</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Post Date</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($classifieds as $classified)
                                        <tr>
                                            <td class="text-center">{{ $loop->index + $classifieds->firstItem() }}</td>

                                            <td class="text-center marketplace">
                                                <a href="#" class="marketplace-link" data-id="{{ $classified->id }}">
                                                    {{ $classified->marketplace->title ?? 'N/A' }}
                                                </a>
                                            </td>


                                            <td class="text-center name">{{ $classified->name ?? 'N/A' }}</td>
                                            <td class="text-center phone">{{ $classified->phone ?? 'N/A' }}</td>
                                            <td class="text-center email">{{ $classified->email ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $classified->created_at ? $classified->created_at->format('m/d/Y') : 'N/A' }}</td>
                                            <td class="text-center status">
                                                <span class="badge bg-{{ $classified->status == '1' ? 'success' : 'warning' }}">
                                                    {{ $classified->status == '1' ? 'Approve' : 'Decline' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <button type="button" class="btn btn-danger delete-btn"
                                                        data-id="{{ $classified->id }}">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="17" class="text-center">No classified ads found!</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="card-footer clearfix">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-end m-0">
                                    @if ($classifieds->onFirstPage())
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                                    </li>
                                    @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $classifieds->previousPageUrl() }}" tabindex="-1">Previous</a>
                                    </li>
                                    @endif

                                    @foreach ($classifieds->getUrlRange(1, $classifieds->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $classifieds->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                    @endforeach

                                    @if ($classifieds->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $classifieds->nextPageUrl() }}">Next</a>
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

<!-- Modal for Approval -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Classified Ad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <form id="approve-form" action="" method="POST" style="display: block;">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Classified Ad Details -->

                    <div class="mb-3">
                        <label for="classified-status" class="form-label">Status</label>
                        <select class="form-select" id="classified-status" name="status">
                            <option value="1">Approve</option>
                            <option value="0">Decline</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


<!-- Classified Details Modal -->
<div class="modal fade" id="classifiedModal" tabindex="-1" aria-labelledby="classifiedModalLabel" aria-hidden="true">


    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classifiedModalLabel">Classified Ad Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>

            <form id="status-update-form" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Marketplace</label>
                                <input type="text" class="form-control" id="modal-marketplace" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" id="modal-title" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" id="modal-state" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" id="modal-city" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pets Allowed</label>
                                <input type="text" class="form-control" id="modal-pets" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <input type="text" class="form-control" id="modal-price" readonly>
                            </div>
                            <div class="mb-3 d-none">
                                <label class="form-label">Price Type</label>
                                <input type="text" class="form-control" id="modal-pricetype" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bedrooms</label>
                                <input type="text" class="form-control" id="modal-bedrooms" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Certification Type</label>
                                <input type="text" class="form-control" id="modal-certification" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Service Type</label>
                                <input type="text" class="form-control" id="modal-service" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="modal-description" readonly rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Name</label>
                                <input type="text" class="form-control" id="modal-name" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" id="modal-phone" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" id="modal-email" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Website</label>
                                <input type="text" class="form-control" id="modal-website" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" id="modal-status" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Update Status</label>
                                <select class="form-select" id="modal-status-select" name="status">
                                    <option value="1">Approve</option>
                                    <option value="0">Decline</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Status</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>



        </div>
    </div>
</div>






@section('script')
<!-- Bootstrap JS (add this before closing </body> tag) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Delete Button Click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                let classifiedId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send DELETE request using Fetch API
                        fetch(`/dashboard/classifieds/${classifiedId}/delete`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your classified ad has been deleted.',
                                        'success'
                                    ).then(() => {
                                        location.reload(); // Refresh the page
                                    });
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'There was an issue deleting the ad.',
                                        'error'
                                    );
                                }
                            });
                    }
                });
            });
        });
    });





    //Open Modal and Populate Data
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.marketplace-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                let classifiedId = this.getAttribute('data-id');

                // Fetch the classified details using AJAX
                fetch(`/dashboard/classifieds/${classifiedId}/show`)
                    .then(response => response.json())
                    .then(data => {
                        const safeValue = (value) => value ? value : '';

                        document.getElementById('modal-marketplace').value = safeValue(data.marketplace?.title);
                        document.getElementById('modal-title').value = safeValue(data.title);
                        document.getElementById('modal-state').value = safeValue(data.state?.name);
                        document.getElementById('modal-city').value = safeValue(data.city?.city_name);
                        document.getElementById('modal-pets').value = safeValue(data.pets_allowed);
                        document.getElementById('modal-price').value = safeValue(data.price);
                        document.getElementById('modal-pricetype').value = safeValue(data.price_type);
                        document.getElementById('modal-bedrooms').value = safeValue(data.bedrooms);
                        document.getElementById('modal-certification').value = safeValue(data.certification_type);
                        document.getElementById('modal-service').value = safeValue(data.service_type);
                        document.getElementById('modal-description').value = safeValue(data.description);
                        document.getElementById('modal-name').value = safeValue(data.name);
                        document.getElementById('modal-phone').value = safeValue(data.phone);
                        document.getElementById('modal-email').value = safeValue(data.email);
                        document.getElementById('modal-website').value = safeValue(data.website);
                        document.getElementById('modal-status').value = data.status == '1' ? 'Approved' : 'Declined';

                        // Ensure it's a string for comparison
                        let status = String(data.status);

                        // Set the value for the select box
                        let statusSelect = document.getElementById('modal-status-select');
                        statusSelect.value = status === '1' ? '1' : '0';

                        // Force repaint (optional)
                        statusSelect.dispatchEvent(new Event('change'));

                        // For debugging
                        console.log('Status:', data.status);


                        // Toggle visibility based on value
                        toggleFieldVisibility('modal-marketplace');
                        toggleFieldVisibility('modal-title');
                        toggleFieldVisibility('modal-state');
                        toggleFieldVisibility('modal-city');
                        toggleFieldVisibility('modal-pets');
                        toggleFieldVisibility('modal-price');
                        toggleFieldVisibility('modal-pricetype');
                        toggleFieldVisibility('modal-bedrooms');
                        toggleFieldVisibility('modal-certification');
                        toggleFieldVisibility('modal-service');
                        toggleFieldVisibility('modal-description');
                        toggleFieldVisibility('modal-name');
                        toggleFieldVisibility('modal-phone');
                        toggleFieldVisibility('modal-email');
                        toggleFieldVisibility('modal-website');
                        toggleFieldVisibility('modal-status');

                        // Set form action URL for updating status
                        document.getElementById('status-update-form').action = `/dashboard/classifieds/${classifiedId}/update-status`;

                        // Show the modal
                        var classifiedModal = new bootstrap.Modal(document.getElementById('classifiedModal'));
                        classifiedModal.show();
                    });
            });
        });

        // Handle Status Update Form Submission
        document.getElementById('status-update-form').addEventListener('submit', function(e) {
            e.preventDefault();

            let form = this;
            let actionUrl = form.action;
            let formData = {
                status: document.getElementById('modal-status-select').value
            };
            console.log(formData);

            // Use Fetch API for AJAX request
            fetch(actionUrl, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            }).then(response => {
                if (response.ok) {
                    // Update modal status field without reloading
                    document.getElementById('modal-status').value =
                        document.getElementById('modal-status-select').value === '1' ? 'Approved' : 'Declined';

                    // Display success message
                    let successMessage = document.createElement('div');
                    successMessage.classList.add('alert', 'alert-success', 'mt-3');
                    successMessage.innerText = 'Status updated successfully.';
                    form.appendChild(successMessage);

                    // Remove the message after 2 seconds
                    setTimeout(() => {
                        successMessage.remove();
                        location.reload();
                    }, 2000);
                } else {
                    alert('There was an issue updating the status.');
                }
            });
        });
    });


    //Reset modal fields when the modal is closed
    function toggleFieldVisibility(fieldId) {
        let field = document.getElementById(fieldId);
        if (field && field.value.trim() === '') {
            field.closest('.mb-3').style.display = 'none';
        } else {
            field.closest('.mb-3').style.display = 'block';
        }
    }

    //Reset modal fields when the modal is closed
    document.getElementById('classifiedModal').addEventListener('hidden.bs.modal', function() {
        //Reset all input fields
        document.querySelectorAll('#classifiedModal input, #classifiedModal textarea').forEach(field => {
            field.value = '';
        });

        //Optionally, reset the visibility if you are toggling visibility
        document.querySelectorAll('#classifiedModal .mb-3').forEach(div => {
            div.style.display = 'block';
        });
    });

    $(document).ready(function () {
        function filterTable() {
            var marketplace = $('#search-marketplace').val().toLowerCase();
            var name = $('#search-name').val().toLowerCase();
            var phone = $('#search-phone').val().toLowerCase();
            var email = $('#search-email').val().toLowerCase();
            var status = $('#search-status').val().toLowerCase();

            var rowCount = 0;
            $('tbody tr').each(function () {
                var rowMarketplace = $(this).find('.marketplace').text().toLowerCase();
                var rowName = $(this).find('.name').text().toLowerCase();
                var rowPhone = $(this).find('.phone').text().toLowerCase();
                var rowEmail = $(this).find('.email').text().toLowerCase();
                var rowStatus = $(this).find('.status').text().trim().toLowerCase(); // Trim to avoid extra spaces

                if (
                    rowMarketplace.includes(marketplace) &&
                    rowName.includes(name) &&
                    rowPhone.includes(phone) &&
                    rowEmail.includes(email) &&
                    (status === "" || rowStatus === status) // Allow all if status is empty
                ) {
                    $(this).show();
                    rowCount++;
                } else {
                    $(this).hide();
                }
            });

            // Remove any existing "No records found!" row
            $('.no-data').remove();

            // If no records are visible, show "No records found!" message
            if (rowCount === 0) {
                $('tbody').append('<tr class="no-data"><td colspan="7" class="text-center">No records found!</td></tr>');
            }
        }

        $('#search-marketplace, #search-name, #search-phone, #search-email, #search-status').on('keyup change', function () {
            filterTable();
        });
    });
</script>
@endsection