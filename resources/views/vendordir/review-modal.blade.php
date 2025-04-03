<style>
    .rate {
        float: left;
        height: 46px;
        padding: 10px 10px;
    }

    .rate:not(:checked)>input {
        position: absolute;
        display: none;
    }

    .rate:not(:checked)>label {
        float: right;
        width: 1em;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        font-size: 30px;
        color: #ccc;
    }

    .rated:not(:checked)>label {
        float: right;
        width: 1em;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        font-size: 30px;
        color: #ccc;
    }

    .rate:not(:checked)>label:before {
        content: '★ ';
    }

    .rate>input:checked~label {
        color: #ffc700;
    }

    .rate:not(:checked)>label:hover,
    .rate:not(:checked)>label:hover~label {
        color: #deb217;
    }

    .rate>input:checked+label:hover,
    .rate>input:checked+label:hover~label,
    .rate>input:checked~label:hover,
    .rate>input:checked~label:hover~label,
    .rate>label:hover~input:checked~label {
        color: #c59b08;
    }

    .star-rating-complete {
        color: #c59b08;
    }

    .rating-container .form-control:hover,
    .rating-container .form-control:focus {
        background: #fff;
        border: 1px solid #ced4da;
    }

    .rating-container textarea:focus,
    .rating-container input:focus {
        color: #000;
    }

    .rated {
        float: left;
        height: 46px;
        padding: 0 10px;
    }

    .rated:not(:checked)>input {
        position: absolute;
        display: none;
    }

    .rated:not(:checked)>label {
        float: right;
        width: 1em;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        font-size: 30px;
        color: #ffc700;
    }

    .rated:not(:checked)>label:before {
        content: '★ ';
    }

    .rated>input:checked~label {
        color: #ffc700;
    }

    .rated:not(:checked)>label:hover,
    .rated:not(:checked)>label:hover~label {
        color: #deb217;
    }

    .rated>input:checked+label:hover,
    .rated>input:checked+label:hover~label,
    .rated>input:checked~label:hover,
    .rated>input:checked~label:hover~label,
    .rated>label:hover~input:checked~label {
        color: #c59b08;
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
    .display-review {
        max-width: 50% !important;
    }
    .modal-md {
        max-width: 50% !important; /* Adjust the percentage as needed */
    }

    hr {
        margin : 0 !important;
    }
</style>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="review-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="py-2 px-4" action="{{ route('vendor.agency.review-store') }}" method="POST"
                style="box-shadow: 0 0 10px 0 #ddd;" autocomplete="off">
                    @csrf        
                    <!-- Rating Stars -->
                    <p class="mt-2">Review</p>
                    <div class="form-group row">
                        {{-- <input type="hidden" name="booking_id" value="{{ $value->id }}"> --}}
                        <div class="rating-stars">
                            <div class="rate">
                                <input type="radio" id="star5" class="rate" name="rating" value="5" />
                                <label for="star5" title="text">5 stars</label>
                                <input type="radio" id="star4" class="rate" name="rating" value="4" />
                                <label for="star4" title="text">4 stars</label>
                                <input type="radio" id="star3" class="rate" name="rating" value="3" />
                                <label for="star3" title="text">3 stars</label>
                                <input type="radio" id="star2" class="rate" name="rating" value="2">
                                <label for="star2" title="text">2 stars</label>
                                <input type="radio" id="star1" class="rate" name="rating" value="1" />
                                <label for="star1" title="text">1 star</label>
                            </div>
                        </div>                        
                    </div>
                    <div class="form-group select-error">
                        <span class="text-danger" id="rating-error-text"></span>
                    </div>
                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="hidden" name="vendor_agencies_id" id="vendor_agencies_id">
                        <input type="email" class="form-control" name="email" required
                            placeholder="Enter your email">
                    </div>
                    <!-- Comment Field -->
                    <div class="form-group row mt-4">
                        <div class="col">
                            <textarea class="form-control" name="review" rows="6 " placeholder="Comment" maxlength="200"></textarea>
                        </div>
                    </div>
                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-sm py-2 px-3 btn-style-one">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade display-review" id="displayReviews" tabindex="-1" aria-labelledby="reviewModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="display-review-title"></h5>                
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="all-reviews">
                
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registerModalLabel">Register</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Registration Form -->
          <form id="registerForm">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="useremail" class="form-label">Email</label>
              <input type="email" class="form-control" id="useremail" name="useremail" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-sm py-2 px-3 btn-style-one mt-2">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
