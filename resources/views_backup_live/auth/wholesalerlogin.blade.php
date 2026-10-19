@extends('dashboard.layouts.login_layout')
@include('sweetalert::alert')
@section('content')
<style>
    .valid_field{
        color: red !important;
    }
</style>
        <!--begin::Main-->
    
        <!--begin::Login-->
<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
    <!--begin::Aside-->
    <div class="login-aside d-flex flex-column flex-row-auto" style="background-color: #7D4C9E;">
        <!--begin::Aside Top-->

        <!--end::Aside Top-->
        <!--begin::Aside Bottom-->
       <div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style=""> 
            <div class="d-flex flex-column-auto flex-column" style="-webkit-box-flex: 0;-ms-flex: none;flex: none;align-items: center;justify-content: center;width: 100%;padding: 0;background: #232B44;">
            <!--begin::Aside header-->
            <!-- <a href="#" class="text-center mb-10">
                <img src="http://instrushare.vrinsoft.in/dist/assets/media/logos/logo-letter-1.png" class="max-h-70px" alt=""/>
            </a> -->
            <!--end::Aside header-->

            <!--begin::Aside title-->
            <h3 class="font-weight-bolder text-center font-size-h4 font-size-h1-lg" style="color: #ffffff;">
                Welcome to Trade25<br>
                Wholesaler Login
            </h3>
            <!--end::Aside title-->
            </div>
        </div>
    </div>
    <!--begin::Aside-->

    <!--begin::Content-->
    <div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
        <!--begin::Content body-->
        <div class="d-flex flex-column-fluid flex-center">
            <?php 
                 if (Cookie::get('wholesaler_email') !== null)
                 {
                    $email = Cookie::get('wholesaler_email');
                    //dd($email);
                 }
                 if (Cookie::get('wholesaler_password') !== null)
                 {
                    $password = Cookie::get('wholesaler_password');

                 }
                 ?>
            <!--begin::Signin-->
            <div class="login-form login-signin">
                 @if ($message = Session::get('warning'))
                <div class="alert alert-danger alert-block validate">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }}</strong>
                </div>
                @endif
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block validate">
                        <button type="button" class="close" data-dismiss="alert">×</button> 
                        <strong>{{ $message }}</strong>
                    </div>
                @endif


                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block validate">
                        <button type="button" class="close" data-dismiss="alert">×</button> 
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                <!--begin::Form-->
                <form class="form"  method="POST" action="{{ route('adminwholesalerlogin') }}" >
                    @csrf   
                    <!--begin::Title-->
                    <div class="pb-13 pt-lg-0 pt-5">
                        <img src="{{ asset('assets/dashboard/images/trade25logonewlarge.png')}}" style="
                        height: 130px;" />
                    </div>
                    <!--begin::Title-->

                    <!--begin::Form group-->
                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Email <span class="valid_field">*</span></label>
                        <input class="form-control  @error('email') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="email" id="email" placeholder="Email" value="{{ isset($email) ? $email : '' }}" autocomplete="off"/>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <!--end::Form group-->

                    <!--begin::Form group-->
                    <div class="form-group">
                        <div class="d-flex justify-content-between mt-n5">
                            <label class="font-size-h6 font-weight-bolder text-dark pt-5">Password <span class="valid_field">*</span></label>

                            
                        </div>

                        <input class="form-control @error('password') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="password" id="password" placeholder="Password" name="password" value="{{ isset($password) ? $password : '' }}" autocomplete="off"/>
                        @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                    </div>
                    <!--end::Form group-->
                    <div class="m-b-md text-left">
                    <label class="md-check">
                        <input type="checkbox" name="remember_me" {{ isset($password) ? 'checked' : '' }}  value="true"><i
                            class="primary"></i> {{ __('backend.keepMeSignedIn') }}
                    </label>
                </div>
                    <!--begin::Action-->
                    <div class="pb-lg-0 pb-5">
                        <button type="submit" id="kt_login_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3" style="background: linear-gradient(90deg, #FE9901 -0.09%, #FFD93B 99.91%);border: none;border-radius: 45px;color: #232B44;">Sign In</button>
                        <a style="color:#232B44 !important; " href="{{ url('/'.env('WHOLESALER_BACKEND_PATH').'/forgot-password') }}" class="text-primary font-size-h6 font-weight-bolder text-hover-primary pt-5" id="kt_login_forgot">
                                Forgot Password ?
                        </a>


                    </div>
                        <!--  <a>
                                Don't have an account? <a style="color:#951111 !important; " href="{{ url('/'.env('WHOLESALER_BACKEND_PATH').'/register') }}" class="text-primary font-size-h6 font-weight-bolder text-hover-primary pt-5" id="kt_login_forgot">Register</a>
                        </a> -->
                    <!--end::Action-->

                </form>
                <!--end::Form-->
            </div>
            <!--end::Signin-->

            <!--begin::Forgot-->
            <div class="login-form login-forgot">
                <!--begin::Form-->
                <form class="form" method="POST" action='{{ url("forgot/mainuser") }}'>
                    @csrf
                    <!--begin::Title-->
                    <div class="pb-13 pt-lg-0 pt-5">
                        <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Forgotten Password ?</h3>
                        <p class="text-muted font-weight-bold font-size-h4">Enter your email to reset your password</p>
                    </div>
                    <!--end::Title-->

                    <!--begin::Form group-->
                    <div class="form-group">
                        <input class="form-control @error('email') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" value="{{ $email ?? old('email') }}"  type="email" placeholder="Email" name="email" autocomplete="off"/>
                    </div>
                    <!--end::Form group-->

                    <!--begin::Form group-->
                    <div class="form-group d-flex flex-wrap pb-lg-0">
                        <button type="button" id="kt_login_forgot_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-4">Submit</button>
                        <button type="button" id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bolder font-size-h6 px-8 py-4 my-3">Cancel</button>
                    </div>
                    <!--end::Form group-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Forgot-->
        </div>
        <!--end::Content body-->
    </div>
    <!--end::Content-->
</div>
<!--end::Login-->
<!--end::Main-->



@endsection