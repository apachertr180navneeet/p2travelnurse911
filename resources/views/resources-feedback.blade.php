<div class="review-modal-body">
    <div class="row">
        <div class="col-12">
            <div class="write-review-action text-right">
                <a href="javascript:void(0);" class="text-primary btn-btn-primary" id="review-modal" data-id="{{$vendor->client_id}}" 
                    data-title="{{ $vendor->company_name }}">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Write a review
                </a>
            </div>
            <div class="space-y-6" style="max-height: 90%;overflow:auto;">            
                @if (count($reviews) == 0)                    
                    <div class="text-center">
                        <p class="text-gray-500">No reviews available</p>    
                    </div>
                @endif
                {{-- Example of a review item --}}
                @foreach ($reviews as $review)
                    <div class="rounded p-2 flex flex-col space-y-4">

                        <!-- User Profile Picture -->
                        <div class="image-username d-flex" style="align-items:center">
                            @php
                                if (empty($review->profile_pic)) {
                                    $review->profile_pic = 'https://travelnurse911.com/public/uploads/users/1401461451813100977.jpg';
                                } else {
                                    $review->profile_pic = url(
                                        config('custom.user_folder').$review->profile_pic,
                                    );
                                }
                            @endphp

                            <img style="height:45px;margin-right: 10px;border-radius:50%" src="{{ $review->profile_pic }}" alt="Profile"
                                class="w-12 h-12 rounded-full" />
                            <span class="font-medium text-lg">{{ $review->username ?? '-' }}</span>
                        </div>
                        <div class="flex-1">
                            <!-- User Info and Rating -->
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex text-yellow-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i style="color:#ffc700;"
                                            class="fas {{ $i <= ($review->rating ?? 4) ? 'fa-star' : 'fa-star-o' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <!-- User Review Content -->
                            <p class="text-gray-700">{{ $review->comments ?? 'No comments available' }}</p>
                        </div>
                    </div>
                    <hr>                    
                @endforeach
            </div>
        </div>
    </div>
</div>
