@extends('dashboard.layouts.login_layout')

@section('content')
<style>
    .error{
        color: red !important;
    }
</style>
<style type="text/css">
    #pass {
    position: absolute;
    right: 35px;
    top: auto;
    bottom: 132px;
    /* top: 0; */
    transform: translateY(-50%);
    cursor: pointer;
    z-index: 9;
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
             <div class="d-flex flex-column-auto flex-column" style="-webkit-box-flex: 0;-ms-flex: none;flex: none;align-items: center;justify-content: center;width: 100%;padding: 0;background: #070004;">
            <!--begin::Aside header-->
            <!-- <a href="#" class="text-center mb-10">
                <img src="http://instrushare.vrinsoft.in/dist/assets/media/logos/logo-letter-1.png" class="max-h-70px" alt=""/>
            </a> -->
            <!--end::Aside header-->

            <!--begin::Aside title-->
            <h3 class="font-weight-bolder text-center font-size-h4 font-size-h1-lg" style="color: #ffffff;">
                Welcome to Liquor Junction<br>
                Login for great experience
            </h3>
            <!--end::Aside title-->
            </div>
        </div>
    </div>
    <!--begin::Aside-->

    <!--begin::Content-->
    <div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
        <?php 
                 if (Cookie::get('admin_email') !== null)
                 {
                    $email = Cookie::get('admin_email');
                    //dd($email);
                 }
                 if (Cookie::get('admin_password') !== null)
                 {
                    $password = Cookie::get('admin_password');

                 }
                 ?>
        <!--begin::Content body-->
        <div class="d-flex flex-column-fluid flex-center">
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
                <form class="form"  method="POST" action="{{ route('adminlogin') }}" >
                    @csrf   
                    <!--begin::Title-->
                    <div class="pb-13 pt-lg-0 pt-5" style="text-align: center;">
                         <img src="{{ asset('assets/dashboard/images/liquor-logo.svg')}}" style="
                        height: 130px;" />
                    </div>
                    <!--begin::Title-->

                    <!--begin::Form group-->
                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Email <span class="valid_field">*</span></label>
                        <input class="form-control  @error('email')  @enderror form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="text" name="email" id="email" placeholder="Email" value="{{ isset($email) ? $email : '' }}" autocomplete="off"/>
                        @error('email')
                            <span class="error" role="alert"> 
                                {{ $message }}
                             </span>
                        @enderror
                    </div>
                    <!--end::Form group-->

                    <!--begin::Form group-->
                    <div class="form-group">
                        <div class="d-flex justify-content-between mt-n5">
                            <label class="font-size-h6 font-weight-bolder text-dark pt-5">Password <span class="valid_field">*</span></label>

                            
                        </div>

                        <input class="form-control @error('password')  @enderror form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="password" id="password" placeholder="Password" name="password" value="{{ isset($password) ? $password : '' }}" autocomplete="off"/>
                        
                        @error('password')
                                <span class="error" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                    </div>

                    <div class="m-b-md text-left">
                    <label class="md-check">
                        <input type="checkbox" name="remember_me" {{ isset($password) ? 'checked' : '' }}  value="true"><i
                            class="primary"></i> {{ __('backend.keepMeSignedIn') }}
                    </label>
                </div>
                    <!--end::Form group-->

                    <!--begin::Action-->
                    <div class="pb-lg-0 pb-5">
                        <button type="submit" id="kt_login_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3" style="background: linear-gradient(90deg, #FE9901 -0.09%, #FFD93B 99.91%); border:none; border-radius: 45px; color: #232B44;">Sign In</button>
                        <a style="color:#232B44 !important; " href="{{ url('/'.env('BACKEND_PATH').'/forgot-password') }}" class="text-primary font-size-h6 font-weight-bolder text-hover-primary pt-5" id="kt_login_forgot">
                                Forgot Password ?
                        </a>

                    </div>
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
                        <input class="form-control @error('email') @enderror form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" value="{{ $email ?? old('email') }}"  type="email" placeholder="Email" name="email" autocomplete="off"/>
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
    @push("after-scripts")
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="{{ asset('assets/dashboard/js/jquery.validate.min.js') }} "></script>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).on('click', '.toggle-password', function() {
            // alert('hyy')
          $(this).toggleClass("fa-eye-slash fa-eye");
          var input = $("#password");
          if (input.attr("type") === "password") {
            input.attr("type", "text");
          } else {
            input.attr("type", "password");
          }

        });
</script>
@endpush