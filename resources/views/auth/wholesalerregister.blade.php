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
                Welcome to Liquor<br>
                Register
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
            <!--begin::Signin-->
            <div class="login-form login-signin">
                
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block validate">
                        <button type="button" class="close" data-dismiss="alert">×</button> 
                        <strong>{{ $message }}</strong>
                    </div>
                @endif

                
                <!--begin::Form-->
                <form class="form"  method="POST" action="{{ route('adminwholesalerregister') }}" enctype="multipart/form-data">
                    @csrf   
                    <!--begin::Title-->
                    <div class="pb-13 pt-lg-0 pt-5">
                        <img src="{{ asset('assets/dashboard/images/trade25logonewlarge.png')}}" style="
                        height: 130px;" />
                    </div>
                    <!--begin::Title-->
                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Trading Name <span class="valid_field">*</span></label>
                        <input class="form-control  @error('store_name') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="store_name" id="store_name" placeholder="Store Name" value="{{old('store_name')}}" autocomplete="off"/>
                        @error('store_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <!--begin::Form group-->
                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">First Name <span class="valid_field">*</span></label>
                        <input class="form-control  @error('first_name') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="first_name" id="first_name" placeholder="First Name" value="{{old('first_name')}}" autocomplete="off"/>
                        @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Last Name <span class="valid_field">*</span></label>
                        <input class="form-control  @error('last_name') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="last_name" id="last_name" placeholder="Last Name" value="{{old('last_name')}}" autocomplete="off"/>
                        @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Email <span class="valid_field">*</span></label>
                        <input class="form-control  @error('email') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="email" id="email" placeholder="Email" value="{{@$main_user_data->email}}" autocomplete="off"/ readonly>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Phone <span class="valid_field">*</span></label>
                        <input class="form-control  @error('phone') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="phone" id="phone" onkeypress="return isNumberKey(event)" placeholder="Phone" value="{{old('phone')}}" autocomplete="off"/>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                     <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">ABN number <span class="valid_field">*</span></label>
                        <input class="form-control  @error('abn_number') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="abn_number" id="abn_number" maxlength="11" onkeypress="return isNumberKey(event)" placeholder="Phone" value="{{old('abn_number')}}" autocomplete="off"/>
                       @error('abn_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Store Description <span class="valid_field">*</span></label>
                        
                        <textarea class="form-control  @error('store_description') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="store_description" id="store_description" placeholder="Store Description" value="{{old('store_description')}}" autocomplete="off"></textarea>
                        @error('store_description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Country <span class="valid_field">*</span></label>
                        <input class="form-control  @error('country') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="country" id="country" placeholder="Country" value="{{old('country')}}" autocomplete="off"/ >
                        @error('country')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">State <span class="valid_field">*</span></label>
                        <input class="form-control  @error('state') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="state" id="state" placeholder="State" value="{{old('state')}}" autocomplete="off"/ >
                        @error('state')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">City <span class="valid_field">*</span></label>
                        <input class="form-control  @error('state') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="city" id="city" placeholder="City" value="{{old('city')}}" autocomplete="off"/ >
                        @error('city')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Street Address <span class="valid_field">*</span></label>
                        <input class="form-control  @error('street_address') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="street_address" id="street_address" placeholder="Stree Address" value="{{old('street_address')}}" autocomplete="off"/ >
                        @error('street_address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Zip Code <span class="valid_field">*</span></label>
                        <input class="form-control  @error('post_code') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="post_code" id="post_code" placeholder="post_code" value="{{old('post_code')}}" autocomplete="off"/ >
                        @error('post_code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                   
                   <div class="form-group">
                           <label class="font-size-h6 font-weight-bolder text-dark">Store Logo <span class="valid_field">*</span></label>
                           
                                 <img id="blah" src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="your image"  width="70" height="70" />
                                <input type="file" name="profile_picture" id="profile_picture" class="form-control" style="border: none; margin-left: -13px;" accept="image/png, image/jpg, image/jpeg">
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .png, .jpg, .jpeg
                                </small>
                           
                        </div>
                                @if ($errors->has('profile_picture'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('profile_picture') }}</span>
                            </span>
                            @endif


                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">New Password <span class="valid_field">*</span></label>
                        <input class="form-control  @error('password') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="password" name="password" id="password" placeholder="New Password" value="{{old('password')}}" autocomplete="off"/>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        @if(Session::has('errorMessageNewPassword'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ Session::get('errorMessageNewPassword') }}</span>
                            </span>
                         @endif
                    </div>

                    <div class="form-group">
                        <label class="font-size-h6 font-weight-bolder text-dark">Confirm Password <span class="valid_field">*</span></label>
                        <input class="form-control  @error('password_confirmation') is-invalid @enderror form-control-solid h-auto py-7 px-6 rounded-lg" type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" value="{{old('password_confirmation')}}" autocomplete="off"/>
                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        @if(Session::has('errorMessageConformPassword'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ Session::get('errorMessageConformPassword') }}</span>
                            </span>
                        @endif
                    </div>
                    <!--end::Form group-->

                    <!--end::Form group-->
                     @if ($message = Session::get('warning'))
                <div class="alert alert-danger alert-block validate">
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
                    <!--begin::Action-->
                    <div class="pb-lg-0 pb-5 text-center">
                        <button type="submit" id="kt_login_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3" style="background: #232B44;border:none;">Register</button>
                        <a href="{{ url('/wholesaler') }}"><button type="button" style="background: #232B44 !important;" id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bolder font-size-h6 px-8 py-4 my-3">Cancel</button></a>
                    </div>
                    <!--end::Action-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Signin-->

           
            <!--end::Forgot-->
        </div>
        <!--end::Content body-->
    </div>
    <!--end::Content-->
</div>
<!--end::Login-->
<!--end::Main-->

<script type="text/javascript">
    profile_picture.onchange = evt => {
             
  const [file] = profile_picture.files
  if (file) {
    blah.src = URL.createObjectURL(file)
  }
}  
    function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
</script>

@endsection