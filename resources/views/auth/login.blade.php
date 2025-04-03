<?php

use Carbon\Carbon;
use App\Helper\CommonFunction;
?>

@extends('layouts.app')
@section("content")
<section class="login p-0 py-5">
    <div class="container-fluid">

        <div class="container">
            <div class="row justify-content-center w-100 align-items-center h-100">

          
                <div class="bg-white col-md-8 col-lg-6 col-xl-4 py-3 rounded shadow-lg">
                    <div class="p-3">
                        <div class="auth-brand text-center text-lg-start">
                            <div class="auth-brand">
                                <a class="logo logo-dark text-center" href="https://travelnurse911.com">
                                    <span class="logo-lg">
                                        <img src="https://travelnurse911.com/public/assets/images/logo.png" alt="logo" width="160">
                                    </span>
                                </a>                               
                            </div>
                        </div>
                        <h4 class="mt-0">Sign In</h4>
                        <p class="mb-4">Enter your email address and password to access account.</p>
                        @if ($errors->any())
                    <div class="alert alert-danger rounded-0">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                    </div>
                    @endif
                        <form  action="{{ route("auth.login") }}" class="sign-form widget-form" method="POST">
                        @csrf
                            <div class="mb-3">
                                <label for="emailaddress" class="form-label">Email address</label>
                                <input class="form-control" type="text" name="email" id="emailaddress" placeholder="Enter your email" value="">
                            </div>
                            <div class="mb-3">
                               
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" value="">
                                    <div class="input-group-append" data-password="false">
                                        <div class="input-group-text">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center d-grid">
                                <button class="btn btn-info btn-block" type="submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <?php
        /*
        ?>
        <div class="row">
            <div class="col-lg-6 col-md-8 m-auto">
                <div class="login-content">
                    <h4>Log In</h4>
                    @if ($errors->any())
                    <div class="alert alert-danger rounded-0">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                    </div>
                    @endif
                    <form  action="{{ route("auth.login") }}" class="sign-form widget-form" method="POST">
                        @csrf
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Email*" name="email" value="{{ old("email") }}"/>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Password*" name="password"/>
                        </div>
                        <div class="sign-controls form-group">
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="remember" value="1" class="custom-control-input" id="rememberMe">
                                <label class="custom-control-label" for="rememberMe">Remember Me</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-custom">Log In</button>
                        </div>
                        <p class="form-group text-center">Don't have an account? <a href="{{ route("auth.signup") }}" class="btn-link">Create One</a> </p>
                    </form>
                </div>
            </div>
        </div>
        <?php
          */
        ?>
    </div>
</section>
@endsection