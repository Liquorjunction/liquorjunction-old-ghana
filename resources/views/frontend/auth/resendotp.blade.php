@extends('frontend.layouts.auth')
@section('title', 'OTP Verification')
@section('auth_visual_title', 'Verify Your Account.')
@section('auth_visual_subtitle', 'Enter the 4-digit verification code we sent to confirm your details and get started.')

@section('content')
    @include('sweetalert::alert')

    <div class="lj-auth-header">
        <h1>{{ @Helper::language('otp_verification_header') }}</h1>
        <p>{{ @Helper::language('enter_the_verification_code_we_just_sent_on') }} <strong>{{ @$forgot_email }}</strong></p>
    </div>

    <div class="registration-card" id="Register">
        <form action="#" class="row registration-form" method="POST" enctype="multipart/form-data" id="otp_form">
            <div class="col-12">
                <div class="form-group">
                    <div class="pass-wrap">
                        <input type="text" class="otp1" id="digit_1" oninput="digitValidate(this);" onkeyup="tabChange(1)"
                            name="digit_1" data-next="digit-2" maxlength="1">
                        <input type="text" class="otp1" id="digit_2" name="digit_2" oninput="digitValidate(this);"
                            onkeyup="tabChange(2)" data-next="digit-3" data-previous="digit-1" maxlength="1">
                        <input type="text" class="otp1" id="digit_3" name="digit_3" oninput="digitValidate(this);"
                            onkeyup="tabChange(3)" data-next="digit-4" data-previous="digit-2" maxlength="1">
                        <input type="text" class="otp1" id="digit_4" name="digit_4" oninput="digitValidate(this);"
                            onkeyup="tabChange(4)" data-next="digit-5" data-previous="digit-3" maxlength="1">
                    </div>
                    <span class="help-block" id="errorMessagecommon" style="display:none">
                        <span style="color: red;display: none;" id="errorMsgcommon"
                            class="validate text-center validate-error d-block mb-2"></span>
                    </span>
                    <span class="d-block text-center body-large text-light-grey" id="exp_span">
                        {{ @Helper::language('expiring_in') }}
                        <span class="text-center body-large text-light-grey" id="some_div">{{ $invert == 1 ? '00:00' : '05:00' }}</span>
                    </span>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="solid-button w-100">{{ @Helper::language('verify_btn_label') }}</button>
                <p class="text-center">{{ @Helper::language('didnot_receive_otp') }}<a href="#"
                        onclick="return resendOtp()" id="resendotp">{{ @Helper::language('resend_btn') }}</a></p>
            </div>
        </form>
    </div>
@endsection

@push('after-scripts')
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var timer2 = $("#some_div").text();
            var interval = setInterval(function() {
                var timer = timer2.split(':');
                var minutes = parseInt(timer[0], 10);
                var seconds = parseInt(timer[1], 10);
                --seconds;
                minutes = (seconds < 0) ? --minutes : minutes;
                if (seconds == 0 && minutes < 1) {
                    clearTimeout(interval);
                }
                if (minutes < 0) {
                    clearInterval(interval);
                } else {
                    seconds = (seconds < 0) ? 59 : seconds;
                    seconds = (seconds < 10) ? '0' + seconds : seconds;
                    minutes = (minutes < 10) ? '0' + minutes : minutes;
                    $('#some_div').html(minutes + ':' + seconds);
                    timer2 = minutes + ':' + seconds;

                    if (timer2 == '00:00') {
                        $('#resendotp').css('pointer-events', '');
                        $('#resendotp').css('cursor', '');
                        $('#resendotp').css('opacity', '');
                        $('#exp_span').text('OTP Expired');
                        $('#some_div').text('');
                    } else {
                        $('#resendotp').css('pointer-events', 'none');
                        $('#resendotp').css('cursor', 'default');
                        $('#resendotp').css('opacity', '0.6');
                    }
                }
            }, 1000);
        });

        let digitValidate = function(ele) {
            ele.value = ele.value.replace(/[^0-9]/g, '');
        }

        let tabChange = function(val) {
            let ele = document.querySelectorAll('.otp1');
            if (ele[val - 1].value != '') {
                if (ele[val]) ele[val].focus();
            } else if (ele[val - 1].value == '') {
                if (ele[val - 2]) ele[val - 2].focus();
            }
        }

        $('#otp_form').on("submit", function(e) {
            e.preventDefault();
            var frm = $('#otp_form');
            var formData = new FormData(frm[0]);
            $.ajax({
                url: "{{ route('websiteotpverificationpost') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                },
                success: function(response) {
                    $('.loader').css("visibility", "visible");
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(errors) {
                    $('.loader').css("visibility", "hidden");
                    var erroJson = JSON.parse(errors.responseText);
                    if (erroJson.status == "error_otp") {
                        $("span#errorMessagecommon").css("display", "block");
                        $("span#errorMsgcommon").css("display", "block");
                        $("span#errorMsgcommon").html(erroJson.errors);
                    }
                }
            });
        });

        function resendOtp() {
            action_url = "{{ route('websiteresendotp') }}";
            var csrf = "{{ csrf_token() }}";

            window.getReCaptchaToken('resend_otp', function(recaptchaToken) {
                $.ajax({
                    url: action_url,
                    data: {
                        'id': 1,
                        'g-recaptcha-response': recaptchaToken
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    },
                    type: "POST",
                    beforeSend: function() {
                        $(".loader").fadeIn();
                        $('.loader').css("visibility", "visible");
                    },
                    success: function(response) {
                        var url = "{{ route('websitesendotp') }}";
                        window.location.href = url;
                    },
                    error: function() {
                        $(".loader").fadeOut();
                    }
                });
            });
        }
    </script>
@endpush
