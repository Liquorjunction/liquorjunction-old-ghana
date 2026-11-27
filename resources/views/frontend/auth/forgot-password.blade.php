@extends('frontend.layouts.auth')
@section('title', 'Forgot Password')
@section('auth_visual_title', 'Think Celebration, Think Liquor Junction')
@section('auth_visual_subtitle', 'Enter your registered email address and we will send a verification code to help you back into your account.')

@section('content')
    @include('sweetalert::alert')

    <div class="lj-auth-header">
        <h1>{{ @Helper::language('forgot_password_heading') }}</h1>
    </div>

    <div class="registration-card" id="Register">
        <form action="#" class="row registration-form" method="POST" enctype="multipart/form-data" id="forgot_password_form">
            <input type="hidden" name="g-recaptcha-response" class="g-recaptcha-response">

            <div class="col-12">
                <div class="form-group">
                    <label for="email">{{ @Helper::language('email_label') }} <span class="valid_field">*</span></label>
                    <input type="email" placeholder="{{ @Helper::language('enter_email_place') }}" name="email" id="email">
                </div>
                <span class="help-block" id="errorMessagecommon" style="display:none">
                    <span style="color: red;display: none;" id="errorMsgcommon"
                        class="validate text-center d-block mb-2"></span>
                </span>
            </div>

            <div class="col-12">
                <button type="submit" class="solid-button w-100">{{ @Helper::language('submit_btn') }}</button>
                <p class="text-center"><a href="{{ route('websitelogin') }}"
                        class="d-block">{{ @Helper::language('back_to_login_msg') }}</a></p>
            </div>
        </form>
    </div>
@endsection

@push('after-scripts')
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">
        var validation_email_required = "{{ \Helper::language('email_field_required') }}";
        var validation_email = "{{ \Helper::language('enter_valid_email_validation') }}";

        var test = $("#forgot_password_form").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
            },
            messages: {
                email: {
                    required: validation_email_required,
                    email: validation_email,
                },
            },
            submitHandler: function() {
                var proceedForgotPassword = function(token) {
                    var form_data = new FormData($('#forgot_password_form')[0]);
                    if (token) {
                        form_data.set('g-recaptcha-response', token);
                    }
                    action_url = "{{ route('forgotpasswordpost') }}";
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
                            $('.loader').css("visibility", "visible");
                            var url = "{{ route('websiteforgototp') }}";
                            window.location.href = url;
                        },
                        error: function(errors) {
                            $('.loader').css("visibility", "hidden");
                            var erroJson = {};
                            try {
                                erroJson = JSON.parse(errors.responseText);
                            } catch (e) {}
                            if (erroJson.status == "error_forgot_password") {
                                $("span#errorMessagecommon").css("display", "block");
                                $("span#errorMsgcommon").css("display", "block");
                                $("span#errorMsgcommon").html(erroJson.errors);
                                setTimeout(function() {
                                    $('#errorMsgcommon').fadeOut('fast');
                                }, 4000);
                            }
                        }
                    });
                };

                if (typeof withReCaptcha === 'function') {
                    withReCaptcha('forgot_password', '#forgot_password_form', proceedForgotPassword);
                } else {
                    proceedForgotPassword('');
                }
            }
        });
    </script>
@endpush
