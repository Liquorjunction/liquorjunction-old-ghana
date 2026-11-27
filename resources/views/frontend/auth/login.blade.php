@extends('frontend.layouts.auth')
@section('title', 'Login')
@section('auth_visual_title', 'Think Celebration, Think Liquor Junction')
@section('auth_visual_subtitle', 'Sign in to access your wishlist, saved delivery addresses, faster checkout, and exclusive bottle offers.')

@section('content')
    @include('sweetalert::alert')

    <div class="lj-auth-header">
        <h1>{{ @Helper::language('login_label') }}</h1>
    </div>

    <div class="registration-card" id="Register">
        <form action="#" class="row registration-form" method="POST" enctype="multipart/form-data" id="login_form">
            <input type="hidden" name="g-recaptcha-response" class="g-recaptcha-response">

            <div class="col-12">
                <div class="form-group">
                    <label for="email">{{ @Helper::language('email_label') }} /
                        {{ @Helper::language('phone_number') }}<span class="valid_field">*</span></label>
                    <input type="text"
                        placeholder="{{ @Helper::language('enter_email_place') }} / {{ @Helper::language('phone_number') }}"
                        name="email" id="email">
                </div>
            </div>

            <div class="col-12">
                <div class="form-group form-password">
                    <label for="login_password">{{ @Helper::language('password_label') }}<span
                            class="valid_field">*</span></label>
                    <input type="password" id="login_password" name="password"
                        placeholder="{{ @Helper::language('enter_password_placeholder') }}" required>
                    <div class="toggle-password">
                        <button type="button" class="show-password"><i class="icon-eye"></i></button>
                        <button type="button" class="hide-password"><i class="icon-eye-slash"></i></button>
                    </div>
                    <span class="help-block" id="errorMessagePassword" style="display:none;">
                        <span class="mb-4 validate validate-error text-center"
                            style="color: #FF4444;font-size:14px; display: none; float:left;"
                            id="errorMsgPassword"></span>
                    </span>
                    <div class="forget-pass">
                        <a href="{{ route('userforgotpassword') }}">{{ @Helper::language('forgot_password_text') }}</a>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <span class="help-block" id="errorMessagecommon" style="display:none">
                    <span style="color: #FF4444;font-size:14px;display: none;" id="errorMsgcommon"
                        class="validate validate-error text-center d-block mb-2"></span>
                </span>
                <span class="help-block" id="errorMessageEmail" style="display:none">
                    <span style="color: #FF4444;font-size:14px;display: none;" id="errorMsgEmail"
                        class="validate validate-error text-center d-block mb-2"></span>
                </span>
                <div id="otp_verify"></div>
            </div>

            <div class="col-12">
                <button type="submit" class="solid-button w-100">{{ @Helper::language('login_label') }}</button>

                @php
                    $hasCartItems = !empty(session('cart_info'))
                        || session()->has('buy_now_info')
                        || session('checkout_value') == 1
                        || request()->has('checkout')
                        || (url()->previous() && (str_contains(url()->previous(), 'cart') || str_contains(url()->previous(), 'checkout') || str_contains(url()->previous(), 'product')));
                @endphp
                @if ($hasCartItems)
                    <a href="{{ route('checkout', ['mode' => 'guest']) }}"
                        class="solid-button lj-auth-guest-btn w-100">Checkout as Guest</a>
                @endif

                <div class="registration-social">
                    <p class="text-sm grey-text">{{ @Helper::language('login_or_continue_with') }}</p>
                    <ul>
                        <li>
                            <a href="{{ route('auth.facebook') }}" target="_blank">
                                <img src="{{ asset('assets/frontend/images/icon_login_facebook.svg') }}" alt="Facebook">
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('auth.google') }}" target="_blank">
                                <img src="{{ asset('assets/frontend/images/icon_login_google.svg') }}" alt="Google">
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('auth.apple') }}" target="_blank">
                                <img src="{{ asset('assets/frontend/images/icon_login_apple.svg') }}" alt="Apple">
                            </a>
                        </li>
                    </ul>
                </div>

                <p class="text-center">{{ @Helper::language('login_not_register_yet') }}<a
                        href="{{ route('websiteregister') }}">{{ @Helper::language('sign_up_btn') }}</a>
                </p>
            </div>
        </form>
    </div>
@endsection

@push('after-scripts')
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>

    <script type="text/javascript">
        var validation_email_required = "{{ Helper::language('email_mobile_field_required') }}";
        var validation_email = "{{ Helper::language('enter_valid_email_validation') }}";
        var validation_password_required = "{{ Helper::language('password_field_required_validation') }}";
        var validation_password_length = "{{ Helper::language('password_length') }}";
        var these_credentials_do_not_match_our_records =
            "{{ Helper::language('these_credentials_do_not_match_our_records') }}";

        var test = $("#login_form").validate({
            rules: {
                email: {
                    required: true,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
            },
            messages: {
                email: {
                    required: validation_email_required,
                },
                password: {
                    required: validation_password_required,
                    minlength: validation_password_length,
                },
            },
            submitHandler: function() {
                var proceedLogin = function(token) {
                    var form_data = new FormData($('#login_form')[0]);
                    if (token) {
                        form_data.set('g-recaptcha-response', token);
                    }
                    action_url = "{{ route('websiteloginpost') }}";
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
                            //$(".loader").fadeIn();
                        },
                        success: function(response) {
                            let checkout_value = response.checkout_value;
                            let counter = response.counter;
                            let pack_size = response.pack_size;

                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else if (response.redirect) {
                                window.location.href = response.redirect;
                            } else if (checkout_value == 1) {
                                var checkoutUrl = '{{ route('checkout') }}';
                                window.location.href = checkoutUrl;
                            } else {
                                var homeUrl = '{{ route('frontend.home') }}';
                                window.location.href = homeUrl;
                            }
                        },
                        error: function(errors) {
                            var erroJson = JSON.parse(errors.responseText);
                            var a = JSON.stringify(erroJson);
                            var b = JSON.parse(a);
                            if (b.email == "These credentials do not match our records" || b.email ==
                                "Ces informations d'identification ne correspondent pas à nos dossiers"
                            ) {
                                $("span#errorMessageEmail").css("display", "block");
                                $("span#errorMsgEmail").css("display", "block");
                                $("span#errorMsgEmail").html(b.email);
                                setTimeout(function() {
                                    $('#errorMsgEmail').fadeOut('fast');
                                }, 4000);
                            }
                            if (erroJson.status == "error" || erroJson.id != "") {
                                $("span#errorMessagecommon").css("display", "block");
                                $("span#errorMsgcommon").css("display", "block");
                                var url = "/send-otp-login/?id=" + erroJson.id;
                                if (erroJson.id) {
                                    $("span#errorMsgcommon").html(erroJson.errors + ' <a href="' + url +
                                        '">click here</a>');
                                } else {
                                    $("span#errorMsgcommon").html(erroJson.errors);
                                }
                                setTimeout(function() {
                                    $('#errorMsgcommon').fadeOut('fast');
                                }, 4000);
                            } else if (erroJson.status == "error_password") {
                                $("span#errorMessagecommon").css("display", "block");
                                $("span#errorMsgcommon").css("display", "block");
                                $("span#errorMessageEmail").css("visible", "false");
                                $("span#errorMsgEmail").css("visible", "false");
                                $("span#errorMsgcommon").html(erroJson.errors);
                            } else {
                                for (var err in erroJson) {
                                    for (var errstr of erroJson[err])
                                        $("span#errorMessagecommon").css("display", "block");
                                    $("span#errorMsgcommon").css("display", "block");
                                    $("span#errorMsgcommon").html(errstr);
                                }
                            }
                        }
                    });
                };

                if (typeof withReCaptcha === 'function') {
                    withReCaptcha('login', '#login_form', proceedLogin);
                } else {
                    proceedLogin('');
                }
            }
        });
    </script>
@endpush
