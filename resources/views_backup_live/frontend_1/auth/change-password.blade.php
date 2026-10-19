@extends('frontend.layouts.app')
@section('title','Change Password')
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
        <div id="Register">
            <div class="registration-content">
                <h1 class="text-center mb-2">{{@Helper::language('reset_password_label')}}</h1>

                <div class="registration-card">
                    <form action="#" class="row registration-form" method="POST" enctype="multipart/form-data" id="change_password_form">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">{{@Helper::language('new_password_label')}}<span
                                    class="valid_field">*</span></label>
                                <div class="form-password">
                                    <input type="password" placeholder="{{@Helper::language('enter_new_password_msg')}}" name="new_password" id="new_password">
                                    <div class="toggle-password">
                                        <button type="button" class="show-password"><i class="fa-solid fa-eye text-dark-grey"></i></button>
                                        <button type="button" class="hide-password"><i class="fa-solid fa-eye-slash text-dark-grey"></i></button>
                                    </div>
                                </div>
                                <span class="help-block" id="errorMessagecommon1" style="display:none">
                                        <span class="" style="color: #FF4444;font-size:14px;display: none;" id="errorMsgcommon1" class='validate validate-error text-center'></span>
                                    </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">{{@Helper::language('confirm_password_label')}}<span
                                    class="valid_field">*</span></label>
                                <div class="form-password">
                                    <input type="password" placeholder="{{@Helper::language('enter_old_password_msg')}}" name="confirm_password" id="confirm_password">
                                    <div class="toggle-password">
                                        <button type="button" class="show-password"><i class="fa-solid fa-eye text-dark-grey"></i></button>
                                        <button type="button" class="hide-password"><i class="fa-solid fa-eye-slash text-dark-grey"></i></button>
                                    </div>
                                    <span class="help-block" id="errorMessagecommon" style="display:none">
                                        <span class="" style="color: #FF4444;font-size:14px;display: none;" id="errorMsgcommon" class='validate validate-error text-center'></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="solid-button w-100">{{@Helper::language('reset_password_label')}}</button>
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
    
    var validation_new_password_required="{{ \Helper::language('new_password_field_required'); }}";
    var validation_new_password_length="{{ \Helper::language('new_password_length'); }}";
    
    var validation_confirm_password_required="{{ \Helper::language('confirm_password_required'); }}";
    var test = $("#change_password_form").validate({
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            // new_password: "required",
            new_password: {
                required: true,
                minlength: 6
            },
            
            confirm_password: "required",

        },
        // in 'messages' user have to specify message as per rules
        messages: {

            // new_password: "New Password field is required",
            new_password: {
                required: validation_new_password_required,
                minlength: validation_new_password_length
            },
            confirm_password: validation_confirm_password_required,
        },
        submitHandler: function() {
            var form_data = new FormData($('#change_password_form')[0]);
            action_url = "{{ route('changepasswordpost') }}";
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
                    // console.log('test'+response);

                    $('.loader').css("visibility", "visible");
                    var url = "{{route('websitelogin')}}";
                    window.location.href = url;
                },
                error: function(errors) {
                    // alert(errors);
                    // console.log(response)
                    $('.loader').css("visibility", "hidden");
                    var erroJson = JSON.parse(errors.responseText);
                    console.log(erroJson.status);
                    if (erroJson.status == "error_password_match") {
                        $("span#errorMessagecommon1").css("display", "none");
                        $("span#errorMsgcommon1").css("display", "none");
                        $("span#errorMessagecommon").css("display", "block");
                        $("span#errorMsgcommon").css("display", "block");

                        $("span#errorMsgcommon").html(erroJson.errors);
                    }
                    if(erroJson.status =="errorMessageCurrentPassword"){
                        $("span#errorMessagecommon").css("display", "none");
                        $("span#errorMsgcommon").css("display", "none");
                        $("span#errorMessagecommon1").css("display", "block");
                        $("span#errorMsgcommon1").css("display", "block");

                        $("span#errorMsgcommon1").html(erroJson.errors);
                    }

                }
            });
        }
    });
</script>
@endsection