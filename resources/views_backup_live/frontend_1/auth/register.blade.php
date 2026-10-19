@extends('frontend.layouts.app')
@section('title','Register')
@section('content')
@include('sweetalert::alert')
<style>
    .valid_field{
        color:red;
    }
</style>
<div class="loader" id="loader"></div>
<section class="registration">
    <div class="container">
        <div id="Register" class="register">
            <div class="registration-content">
                <h1 class="text-center mb-0">{{@Helper::language('register_create_an_account')}}</h1>
                <div class="registration-card">
                    <form class="row registration-form" id="register_form" action="" novalidate>
                        <div class="col-12">
                            <div class="form-group">
                            <?php
                                // dd(@Helper::language('name_label'));
                                ?>
                                <label for="">{{@Helper::language('first_name_label')}}<span class="valid_field">*</span></label>
                                <input type="text" name="first_name" onkeypress="return onlyString(event)" placeholder="{{@Helper::language('Enter_firstname_place')}}" >
                             
                                <span id="first-name-error" class='red-text'></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                            <?php
                                // dd(@Helper::language('name_label'));
                                ?>
                                <label for="">{{@Helper::language('last_name_label')}}<span
                                    class="valid_field">*</span></label>
                                <input type="text" name="last_name" onkeypress="return onlyString(event)" placeholder="{{@Helper::language('enter_lastname_place')}}" >
                              
                                <span id="last-name-error" class='red-text'></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">{{@Helper::language('email_label')}}<span
                                    class="valid_field">*</span></label>
                                <input type="email" name="email" placeholder="{{@Helper::language('enter_email_place')}}" >
                               
                                <span id="email-error" class='red-text'></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group has-validation">
                                <label for="">{{@Helper::language('phone_number')}}<span
                                    class="valid_field">*</span></label>
                                <div class="input-group phone-number ">
                                    <!-- <span class="numbers body-normal text-black d-inline-block">+61</span> -->
                                    <select class="numbers" name="phone_code">
                                    <?php 
                                            $countryData = @$countryData->sortBy(['name', 'ASC']);
                                            $countryData = $countryData->values();
                                        ?>
                                        @foreach($phonecode as $value)
                                            <option value="{{$value->phonecode}}">+{{$value->phonecode.' ('.$value->shortname.')' }}</option>
                                        @endforeach
                                    </select>
                                    <input type="tel" id="phone" name="phone" placeholder="{{@Helper::language('enter_phone_number_place')}}" >
                                   
                                    <span id="phone-error" class='red-text'></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">{{@Helper::language('zip_code_label')}}<span class="valid_field">*</span></label>
                                <input type="text" name="zip_code" placeholder="Enter Zip Code" >
                                <span id="zip-code-error" class='red-text'></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                            <label for="">{{@Helper::language('country_label_web')}} <span class="red-text star">*</span></label>
                                <select value="{{old('country_id')}}" onchange="getSubCatList(this)" name="country_id" id="country_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_country_web')}}</option>
                                    @foreach($countryData as $value)
                                    <option  value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                                <span id="country-error" class='red-text'></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">{{@Helper::language('city_label')}}<span class="valid_field">*</span></label>
                                <input type="text" name="city" placeholder=" Enter city" >
                                <span id="city-error" class='red-text'></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group form-password">
                                <label for="">{{@Helper::language('password_label')}}<span
                                    class="valid_field">*</span></label>
                                <input type="password" name="password" placeholder="{{@Helper::language('enter_password_placeholder')}}" >
                                <div class="toggle-password">
                                    <button type="button" class="show-password"><i class="icon-eye"></i></button>
                                    <button type="button" class="hide-password"><i class="icon-eye-slash"></i></button>
                                </div>                              
                                <span id="password-error" class='red-text'></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group form-password">
                                <label for="">{{@Helper::language('confirm_password_label')}}<span
                                    class="valid_field">*</span></label>
                                <input type="password" name="confirm_password" placeholder="{{@Helper::language('enter_confirm_password')}}" >
                                <div class="toggle-password">
                                    <button type="button" class="show-password"><i class="icon-eye"></i></button>
                                    <button type="button" class="hide-password"><i class="icon-eye-slash"></i></button>
                                </div>                               
                                <span id="confirm-password-error" class='red-text'></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="solid-button w-100"  id="submitDetail">{{@Helper::language('sign_up_btn')}}</button>
                            <div class="registration-social">
                                <p class="text-sm grey-text">{{@Helper::language('login_or_continue_with')}}</p>
                                <ul>
                                    <li>
                                        <a href="{{route('auth.facebook')}}" target="_blank">
                                            <img src="{{ asset('assets/frontend/images/icon_login_facebook.svg')}}" alt="">
                                        </a>
                                    </li>
                                    <!-- <li>
                                        <a href="https://www.instagram.com/" target="_blank">
                                            <img src="{{ asset('assets/frontend/images/icon_login_insta.svg')}}" alt="">
                                        </a>
                                    </li> -->
                                    <li>
                                        <a href="{{route('auth.google')}}" target="_blank">
                                            <img src="{{ asset('assets/frontend/images/icon_login_google.svg')}}" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{route('auth.apple')}}" target="_blank">
                                            <img src="{{ asset('assets/frontend/images/icon_login_apple.svg')}}" alt="">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                          
                            <p class="text-center">{{@Helper::language('register_already_a_member')}}<a href="{{route('websitelogin')}}" onclick="return show('Login','Register');">{{@Helper::language('login_label')}}</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</section>

<script>
    function show(shown, hidden) {
        document.getElementById(shown).style.display = 'block';
        document.getElementById(hidden).style.display = 'none';
        return false;
    }
       
</script>
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

<script>
   
</script>
<script type="text/javascript">
    $.validator.setDefaults({
        ignore: [],
        // any other default options and/or rules
    });
  

    function isNumberKey(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32) return false;
        return true;
    }
    function onlyString(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

            return false;
        return true;

    }

    $('#phone').on("input", function() {
        this.value = this.value.replace(/[^0-9\.]/g, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });

    var test = $("#register_form").validate({        
        submitHandler: function() {
            var form_data = new FormData($('#register_form')[0]);
            action_url = "{{ route('websiteregisterpost') }}";
            var csrf = "{{ csrf_token() }}";
            $.ajax({
                url: action_url,
                data: form_data,
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                processData: false,
                contentType: false,
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                    //$('#submitDetail').prop('disabled', true);
                },
                success: function(response) {
                    $('.loader').css("visibility", "visible");
                    var url = "{{route('websitesendotp')}}";
                    window.location.href = url;
                },
                error: function(errors) {                  
                    $('.loader').css("visibility", "hidden");

                    var errors = errors.responseJSON;
                    $("span#first-name-error,span#last-name-error,span#email-error,span#phone-error,span#password-error").text('');
                    if (errors.first_name) {
                        $("span#first-name-error").text(errors.first_name[0]);
                    }   
                    if (errors.last_name) {
                        $("span#last-name-error").text(errors.last_name[0]);
                    }    
                    if (errors.email) {
                        $("span#email-error").text(errors.email[0]);
                    }  
                    if (errors.phone) {
                        $("span#phone-error").text(errors.phone[0]);
                    }    
                    if (errors.password) {
                        $("span#password-error").text(errors.password[0]);
                    }
                    if (errors.confirm_password) {
                        $("span#confirm-password-error").text(errors.confirm_password[0]);
                    }                   
                }
            });
        }
    });
</script>
@endsection