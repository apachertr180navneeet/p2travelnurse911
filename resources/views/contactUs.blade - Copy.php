<?php

use Carbon\Carbon;
use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section('content')


<!--Page Title-->
<section class="page-title">
   <div class="auto-container">
      <div class="title-outer">
         <h1>Contact Us</h1>
      </div>
   </div>
</section>
<!--End Page Title-->

<!-- 
<section class="map-section">
   <div class="map-outer">
      <div class="map-canvas"
         data-zoom="12"
         data-lat="-37.817085"
         data-lng="144.955631"
         data-type="roadmap"
         data-hue="#ffc400"
         data-title="Envato"
         data-icon-path="{{ asset('public/assets/images/icons/contact-map-marker.png')}}"
         data-content="Melbourne VIC 3000, Australia<br><a href='mailto:info@youremail.com'>info@youremail.com</a>">
      </div>
   </div>
</section>
 -->


<!-- Contact Section -->
<section class="contact-section">
   <div class="auto-container">
      <div class="upper-box mt-0 pt-0">
         <div class="row">
            <!-- 
            <div class="contact-block col-lg-4 col-md-6 col-sm-12 text-center">
               <div class="inner-box">
                  <span class="icon"><img src="{{ asset('public/assets/images/icons/placeholder.svg')}}" alt=""></span>
                  <h4>Address</h4>
                  <p>329 Queensberry Street, North Melbourne VIC 3051, USA.</p>
               </div>
            </div>
-->
            <div class="contact-block col-lg-6 col-md-6 col-sm-12 text-center">
               <div class="inner-box">
                  <span class="icon"><img src="{{ asset('public/assets/images/icons/smartphone.svg')}}" alt=""></span>
                  <h4>Call Us</h4>
                  <p><a href="tell:{{ config('custom.phone') }}">{{ config('custom.phone') }}</a></p>
               </div>
            </div>
            <div class="contact-block col-lg-6 col-md-6 col-sm-12 text-center">
               <div class="inner-box">
                  <span class="icon"><img src="{{ asset('public/assets/images/icons/letter.svg')}}" alt=""></span>
                  <h4>Email</h4>
                  <p><a href="mailto:{{ config('custom.email') }}" class="email">{{ config('custom.email') }}</a></p>
               </div>
            </div>
         </div>
      </div>


      <!-- Contact Form -->
      <div class="contact-form default-form">
         <h3>Leave A Message</h3>
         <!--Contact Form-->
         <form method="POST" action="#" id="email-form">
            @csrf
            <div class="row">
               <div class="form-group col-lg-12 col-md-12 col-sm-12">
                  <div class="response text-center"></div>
                  <div class="success-response bg-success text-white text-center"></div>
               </div>

               <div class="col-lg-6 col-md-12 col-sm-12 form-group">
                  <label>Your Name *</label>
                  <input type="text" name="username" class="username" placeholder="Your Name" required>
               </div>

               <div class="col-lg-6 col-md-12 col-sm-12 form-group">
                  <label>Your Email *</label>
                  <input type="email" name="email" class="email" placeholder="Your Email" required>
               </div>

               <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                  <label>Subject *</label>
                  <input type="text" name="subject" class="subject" placeholder="Subject" required>
               </div>

               <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                  <label>Your Message *</label>
                  <textarea name="message" class="message" placeholder="Write your message..." required=""></textarea>
               </div>

               <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                  <button class="theme-btn btn-style-one" type="button" id="submit" name="submit-form">Send Massage</button>
               </div>
            </div>
         </form>
      </div>
      <!--End Contact Form -->
   </div>
</section>
<!-- Contact Section -->

<script>
   $(document).ready(function() {
      //Contact Form Validation
      if ($("#email-form").length) {
         $("#submit").click(function() {

            $("#email-form .response, #email-form .success-response").html('');

            var o = new Object();
            var form = "#email-form";

            var username = $("#email-form .username").val();
            var email = $("#email-form .email").val();
            var subject = $("#email-form .subject").val();
            var message = $("#email-form .message").val();

            // Regular expression to prevent script/php injection
            var regex =
               /<\s*script.*?>.*?<\s*\/\s*script\s*>|<\s*\?php.*?\?>|<.*?>/i;

            if (
               username == "" ||
               email == "" ||
               subject == "" ||
               message == ""
            ) {
               $(form + " .response").html(
                  '<div class="failed">Please fill the required fields.</div>'
               );
               return false;
            }

            if (
               regex.test(username) ||
               regex.test(email) ||
               regex.test(subject) ||
               regex.test(message)
            ) {
               $(form + " .response").html(
                  '<div class="failed">Invalid input detected. Please avoid using special characters or code.</div>'
               );
               return false;
            }

            $.ajax({
               url: "{{ route('contact-us-submit') }}",
               method: "POST",
               data: $(form).serialize(),
               beforeSend: function() {
                  $("#email-form .response").html(
                     '<div class="text-info"><img src="{{ asset("public/assets/images/icons/preloader.gif")}}"> Loading...</div>'
                  );
               },
               success: function(res) {

                  if (res.errors && res.errors != "") {

                     for (var error in res.errors) {
                        $(form + " .response ul").append(
                           "<li>" + res.errors[error] + "</li>"
                        );
                     }
                  } else {
                     if (res.status) {
                        $("#email-form .response").hide();
                        $("form").trigger("reset");
                        $("#email-form .success-response").fadeIn().html(res.message);
                        setTimeout(function() {
                           $("#email-form .success-response").fadeOut("slow");
                        }, 5000);
                     } else {
                        $(form + " .response").html(
                           '<div class="failed">' + res.message + '</div>'
                        );
                     }
                  }

               },
               error: function(data) {
                  $("#email-form .response").fadeIn().html(data);
               },
            });
         });
      }
   });
</script>

<!-- Call To Action -->
<section class="call-to-action style-two">
   <div class="auto-container">
      <div class="outer-box">
         <div class="content-column">
            <div class="sec-title">
               <h2>Recruiting?</h2>
               <div class="text">Advertise your jobs to millions of monthly users and search 15.8 million<br> CVs in our database.</div>
               <a href="{{ route('contact-us') }}" class="theme-btn btn-style-one bg-blue"><span class="btn-title">Start Recruiting Now</span></a>
            </div>
         </div>

         <div class="image-column" style="background-image: url({{ asset('public/assets/images/resource/image-1.png')}});">
            <figure class="image"><img src="{{ asset('public/assets/images/resource/image-1.png')}}" alt=""></figure>
         </div>
      </div>
   </div>
</section>
<!-- End Call To Action -->

<!--<script src="http://maps.google.com/maps/api/js?key=AIzaSyDaaCBm4FEmgKs5cfVrh3JYue3Chj1kJMw&#038;ver=5.2.4"></script>-->
<!--<script src="{{ asset('public/assets/js/map-script.js') }}"></script>-->

@endsection