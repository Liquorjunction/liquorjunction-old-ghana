@extends('frontend.layouts.app')
@section('title',Helper::language('edit_account'))
@section('content')
@include('sweetalert::alert')
<style>
    .text-red{
        color:red;
    }
</style>
<div class="bread-crumb-block">
    <div class="container">
        <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
        <li class="breadcrumb-item"><a href="{{route('my-account')}}">{{@Helper::language('my_account_label')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{@Helper::language('edit_account')}}</li>
        </ul>
    </div>
</div>
<section class="change-password pt-20 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                @include('frontend.layouts.account-sidebar')
            </div>
            <div class="col-lg-9 col-md-8">
                <h2>{{@Helper::language('edit_profile_heading')}}</h2>
                <div class="common-card">
                    <form class="row edit-profile-form" id="edit_profile_form" novalidate>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('first_name_label')}}<span class="text-red">*</span></label>
                                <input type="text" name="first_name" placeholder="{{@Helper::language('Enter_firstname_place')}}" class="required" value="{{isset($myProfile->first_name) ? $myProfile->first_name :''}}" id="first_name" required>
                                <div class="invalid-feedback">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                            <?php
                                // dd(@Helper::language('name_label'));
                                ?>
                                <label for="">{{@Helper::language('last_name_label')}}<span class="text-red">*</span></label>
                                <input type="text" name="last_name" placeholder="{{@Helper::language('enter_lastname_place')}}" value="{{isset($myProfile->last_name) ? $myProfile->last_name :''}}"required>
                                <span class="help-block" id="errorMessageLastname" style="display:none;">
                                    <span class="mb-4"style="color: #FF4444;font-size:14px; display: none; float:left;"  id="errorMsgLastname" class='validate validate-error text-center'></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('email_label')}}<span class="text-red"></span></label>
                                <input type="email" readonly name="email"  placeholder="{{@Helper::language('enter_email_place')}}"value="{{isset($myProfile->email) ? $myProfile->email :''}}" class="required" required >
                                <div class="invalid-feedback">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group has-validation">
                                <label for="">{{@Helper::language('phone_number')}}<span class="text-red">*</span></label>
                                <div class="input-group phone-number">
                                    <!-- <span class="numbers body-normal text-black d-inline-block">+61</span> -->
                                    <select class="numbers" name="phone_code" >
                                        @foreach($countryData as $value)
                                        <option value="{{$value->phonecode}}" {{(@$myProfile->phone_code == $value->phonecode) ? 'selected' : ''}} > + {{$value->phonecode.' ('.$value->shortname.')'}}</option>
                                        
                                        @endforeach
                                    </select>
                                    <input type="tel" name="phone" maxlength="15" placeholder="{{@Helper::language('enter_phone_number_place')}}" value="{{isset($myProfile->phone) ? $myProfile->phone :''}}" id="phone" required>
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6"></div>
                        <div class="col-sm-6">
                            <button class="solid-button w-100" type="submit">{{@Helper::language('save_details_btn')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript">
    function isNumberKey(evt) {
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

    $('#first_name').on("input", function() {
        console.log(this.value);
        this.value = this.value.replace(/[^a-zA-Z\s]/gi, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });

    $('#last_name').on("input", function() {
        console.log(this.value);
        this.value = this.value.replace(/[^a-zA-Z\s]/gi, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });
    var validation_first_name_required="{{ \Helper::language('first_name_required'); }}";
    var validation_first_name_minlength="{{ \Helper::language('first_name_min_valiadation'); }}";
    var validation_first_name_max_length="{{ \Helper::language('first_name_max_validation'); }}";
    
    var validation_last_name_required="{{ \Helper::language('last_name_field_is_required'); }}";
    var validation_last_name_minlength="{{ \Helper::language('last_name_min_valiadation_msg'); }}";
    var validation_last_name_max_length="{{ \Helper::language('last_name_max_validation'); }}";
    
    var validation_email_required="{{ \Helper::language('email_field_required'); }}";
    var validation_email="{{ \Helper::language('enter_valid_email_validation'); }}";

    
    var validation_phone_required="{{ \Helper::language('phone_number_field_is_required'); }}";
    var validation_phone_minlength="{{ \Helper::language('phone_number_min_max'); }}";
    var validation_phone_maxlength="{{ \Helper::language('phone_number_min_max'); }}";
    var test = $("#edit_profile_form").validate({
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            first_name: {
                required:true,
                minlength:3,
                maxlength:30,
            },
            last_name: {
                required:true,
                minlength:3,
                maxlength:30,
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                minlength: 8,
                maxlength:15

            },
        },
        // in 'messages' user have to specify message as per rules
        messages: {
            first_name:{ 
                required:validation_first_name_required,
                minlength:validation_first_name_minlength,
                maxlength:validation_first_name_max_length,
            },
            last_name:{ 
                required:validation_last_name_required,
                minlength:validation_last_name_minlength,
                maxlength:validation_last_name_max_length,
            },
            email: {
                required: validation_email_required,
                email: validation_email
            },
            phone: {
                required: validation_phone_required,
                minlength: validation_phone_minlength,
                minlength: validation_phone_maxlength,

            },
           
        },
        submitHandler: function() { 


            var form_data = new FormData($('#edit_profile_form')[0]);
            action_url = "{{ route('upadte-profile') }}";
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
                },
                success: function(response) {
                    // return false;
                    // console.log(response);
                    // $('.loader').css("visibility", "visible");
                    var url = "{{route('my-account')}}";
                    window.location.href = url;
                },
                error: function(errors) {
                    // alert(errors);
                    // $('.loader').css("visibility", "none");
                    // $('.loader').css("visibility", "hidden");
                    var erroJson = JSON.parse(errors.responseText);
                    console.log(erroJson);
                    for (var err in erroJson) {
                        for (var errstr of erroJson[err])
                            // console.log(err);
                            if (err == "first_name") {
                                $("span#errorMessageLastname").css("display", "none");
                                $("span#errorMsgLastname").css("display", "none");
                                $("span#errorMessageFirstname").css("display", "block");
                                $("span#errorMsgFirstname").css("display", "block");

                                $("span#errorMsgFirstname").html(errstr);
                            } else if (err == "last_name") {
                            $("span#errorMessageFirstname").css("display", "none");
                            $("span#errorMsgFirstname").css("display", "none");

                            $("span#errorMessageLastname").css("display", "block");
                            $("span#errorMsgLastname").css("display", "block");

                            $("span#errorMsgLastname").html(errstr);
                        } else {
                            $("span#errorMessageFirstname").css("display", "none");
                            $("span#errorMsgFirstname").css("display", "none");
                            $("span#errorMessageFirstname").css("display", "none");
                            $("span#errorMsgFirstname").css("display", "none");
                            $("span#errorMessageLastname").css("display", "none");
                            $("span#errorMsgLastname").css("display", "none");
                            $("span#errorMessageEmail").css("display", "block");
                            $("span#errorMsgEmail").css("display", "block");

                            $("span#errorMsgEmail").html(errstr);
                        }
                    }
                }
            });
        }
    });
</script>
@endsection