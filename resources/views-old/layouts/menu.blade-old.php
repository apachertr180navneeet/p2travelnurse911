<?php

use App\Helper\CommonFunction;
?>
<div class="navbar-brand w-100">
  <a href="{{ route('index') }}">
    <img class="logo-dark" src="{{ asset('public/assets/img/logo.png') }}" srcset="{{ asset('public/assets/img/logo.png 8x') }}" alt="" />
    <img class="logo-dark" src="{{ asset('public/assets/img/quick-plus-logo.png') }}" srcset="{{ asset('public/assets/img/quick-plus-logo.png 4x') }}" alt="" />
    <img class="logo-light" src="{{ asset('public/assets/img/logo.png') }}" srcset="{{ asset('public/assets/img/logo.png 8x') }}" alt="" style="background: #fff; padding: 2px; border-radius: 4px" />
    <img class="logo-light" src="{{ asset('public/assets/img/quick-plus-logo.png') }}" srcset="{{ asset('public/assets/img/quick-plus-logo.png 4x') }}" alt="" style="background: #fff; padding: 2px; border-radius: 4px" />
  </a>
</div>
<div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">
  <div class="offcanvas-header d-lg-none">
    <h3 class="text-white fs-30 mb-0">Amer</h3>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body ms-lg-auto d-flex flex-column h-100">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="{{ route('index') }}">{{ CommonFunction::__lang('Home') }}</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ CommonFunction::__lang('About Us') }}</a>
        <ul class="dropdown-menu">
          <li class="nav-item"><a class="dropdown-item" href="{{ route('meet-the-team') }}">{{ CommonFunction::__lang('Meet the Team') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('testimonials') }}">{{ CommonFunction::__lang('Testimonials') }}</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ CommonFunction::__lang('Amer Services') }}</a>
        <ul class="dropdown-menu">
          <li class="nav-item"><a class="dropdown-item" href="{{ route('amer-services', 'emirates-id') }}">{{ CommonFunction::__lang('Emirates ID') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('amer-services', 'insurance') }}">{{ CommonFunction::__lang('Insurance') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('amer-services', 'visa') }}">{{ CommonFunction::__lang('Visa') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('amer-services', 'medical') }}">{{ CommonFunction::__lang('Medical Services') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('amer-services', 'tasheel') }}">{{ CommonFunction::__lang('Tasheel services') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('amer-services', 'tadbeer') }}">{{ CommonFunction::__lang('Tadbeer services') }}</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ CommonFunction::__lang('Business Setup Services') }}</a>
        <ul class="dropdown-menu">
          <li class="nav-item"><a class="dropdown-item" href="{{ route('business-services', 'dubai-mainland') }}">{{ CommonFunction::__lang('Dubai Mainland') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('business-services', 'abu-dhabi-mainland') }}">{{ CommonFunction::__lang('Abu Dhabi Mainland') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('business-services', 'uae-freezone') }}">{{ CommonFunction::__lang('UAE Freezone') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('business-services', 'offshore') }}">{{ CommonFunction::__lang('Offshore') }}</a></li>
          <li class="nav-item"><a class="dropdown-item" href="{{ route('business-services', 'cost-calculator') }}">{{ CommonFunction::__lang('Cost calculator') }}</a></li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="blogs.html">{{ CommonFunction::__lang('Blogs') }}</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="contact.html">{{ CommonFunction::__lang('Contact') }}</a>
      </li>
    </ul>
    <!-- /.navbar-nav -->
    <div class="offcanvas-footer d-lg-none">
      <div>
        <a href="mailto:first.last@email.com" class="link-inverse">info@email.com</a>
        <br /> (+971)456 7890 <br />
        <nav class="nav social social-white mt-4">
          <a href="#"><i class="uil uil-twitter"></i></a>
          <a href="#"><i class="uil uil-facebook-f"></i></a>
          <a href="#"><i class="uil uil-instagram"></i></a>
        </nav>
        <!-- /.social -->
      </div>
    </div>
    <!-- /.offcanvas-footer -->
  </div>
  <!-- /.offcanvas-body -->
</div>
<!-- /.navbar-collapse -->
<div class="navbar-other w-100 d-flex ms-auto">
  <ul class="navbar-nav flex-row align-items-center ms-auto">
    <li class="nav-item dropdown language-select text-uppercase">
      <a class="nav-link dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">En</a>
      <ul class="dropdown-menu">
        <li class="nav-item"><a class="dropdown-item" href="#">En</a></li>
        <li class="nav-item"><a class="dropdown-item" href="#">Ar</a></li>
        <li class="nav-item"><a class="dropdown-item" href="#">Tr</a></li>
        <li class="nav-item"><a class="dropdown-item" href="#">Ru</a></li>
      </ul>
    </li>
    <li class="nav-item d-none d-md-block">
      <a href="online-services.html" class="btn btn-sm btn-primary rounded-pill">{{ CommonFunction::__lang('Apply Online') }}</a>
    </li>
    <li class="nav-item d-lg-none">
      <button class="hamburger offcanvas-nav-btn"><span></span></button>
    </li>
  </ul>
  <!-- /.navbar-nav -->
</div>
<!-- /.navbar-other -->