@extends('layouts.app')

@section('content')
    <section class="contact-section">
        <div class="auto-container">
            <div class="contact-form default-form">
                <h3>Leave A Message</h3>

                <!-- Contact Form -->
                <form method="POST" action="{{ route('sendTestEmail') }}">
                    @csrf
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                            <div class="response text-center"></div>
                             @if (session('success'))
                                <div class="alert alert-success mt-3">
                                    {{ session('success') }}
                                </div>
                            @endif
                        </div>

                        <!-- Email Input -->
                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                            <label for="email">Your Email *</label>
                            <input type="email" name="email" class="email" placeholder="Your Email" required>
                        </div>


                        <!-- Message Textarea -->
                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                            <label for="description">Your Message *</label>
                            <textarea name="description" class="description" placeholder="Your Message" rows="5" required></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                            <button class="theme-btn btn-style-one" type="submit" id="submit" name="submit-form">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
