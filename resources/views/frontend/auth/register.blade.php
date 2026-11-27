@extends('frontend.layouts.auth')
@section('title', 'Register')
@section('auth_wrap_class', 'lj-auth-form-wrap--wide')
@section('auth_visual_title', 'Think Celebration, Think Liquor Junction')
@section('auth_visual_subtitle', 'Create your account for seamless ordering, curated recommendations, and fast delivery straight to your door.')

@section('content')
    @include('sweetalert::alert')

    <div class="lj-auth-header">
        <h1>{{ @Helper::language('register_create_an_account') }}</h1>
    </div>

    <div class="registration-card register" id="Register">
        <form class="row registration-form" id="register_form" action="" novalidate>
            <input type="hidden" name="g-recaptcha-response" class="g-recaptcha-response">

            <div class="col-md-6">
                <div class="form-group">
                    <label for="first_name">{{ @Helper::language('first_name_label') }}<span
                            class="valid_field">*</span></label>
                    <input type="text" id="first_name" name="first_name" onkeypress="return onlyString(event)"
                        placeholder="{{ @Helper::language('Enter_firstname_place') }}">
                    <span id="first-name-error" class="red-text"></span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="last_name">{{ @Helper::language('last_name_label') }}<span
                            class="valid_field">*</span></label>
                    <input type="text" id="last_name" name="last_name" onkeypress="return onlyString(event)"
                        placeholder="{{ @Helper::language('enter_lastname_place') }}">
                    <span id="last-name-error" class="red-text"></span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="age">Age<span class="valid_field">*</span></label>
                    <input type="number" name="age" id="age" placeholder="Enter Age" min="18" max="100" step="1">
                    <span id="age-error" class="red-text"></span>
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label for="reg_email">{{ @Helper::language('email_label') }}<span
                            class="valid_field">*</span></label>
                    <input type="email" id="reg_email" name="email"
                        placeholder="{{ @Helper::language('enter_email_place') }}">
                    <span id="email-error" class="red-text"></span>
                </div>
            </div>

            <div class="col-12">
                <div class="form-group has-validation">
                    <label for="phone">{{ @Helper::language('phone_number') }}<span
                            class="valid_field">*</span></label>
                    <div class="input-group phone-number">
                        <select class="numbers" name="phone_code">
                            @php
                                $countryData = @$countryData->sortBy(['name', 'ASC'])->values();
                            @endphp
                            @foreach($phonecode as $value)
                                <option value="{{ $value->phonecode }}">+{{ $value->phonecode }}</option>
                            @endforeach
                        </select>
                        <input type="tel" id="phone" name="phone"
                            placeholder="{{ @Helper::language('enter_phone_number_place') }}">
                        <span id="phone-error" class="red-text"></span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="country_id">{{ @Helper::language('country_label_web') }} <span
                            class="red-text star">*</span></label>
                    <select value="{{ old('country_id') }}" onchange="getSubCatList(this)" name="country_id"
                        id="country_id" class="form-select">
                        <option value="">{{ @Helper::language('choose_country_web') }}</option>
                        @foreach($countryData as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                    <span id="country-error" class="red-text"></span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="city">{{ @Helper::language('city_label') }}<span class="valid_field">*</span></label>
                    <input type="text" id="city" name="city" placeholder="Enter city">
                    <span id="city-error" class="red-text"></span>
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="zip_code">{{ @Helper::language('zip_code_label') }}<span
                            class="valid_field">*</span></label>
                    <input type="text" id="zip_code" name="zip_code" placeholder="Enter Zip Code">
                    <span id="zip-code-error" class="red-text"></span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group form-password">
                    <label for="reg_password">{{ @Helper::language('password_label') }}<span
                            class="valid_field">*</span></label>
                    <input type="password" id="reg_password" name="password"
                        placeholder="{{ @Helper::language('enter_password_placeholder') }}">
                    <div class="toggle-password">
                        <button type="button" class="show-password"><i class="icon-eye"></i></button>
                        <button type="button" class="hide-password"><i class="icon-eye-slash"></i></button>
                    </div>
                    <span id="password-error" class="red-text"></span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group form-password">
                    <label for="reg_confirm_password">{{ @Helper::language('confirm_password_label') }}<span
                            class="valid_field">*</span></label>
                    <input type="password" id="reg_confirm_password" name="confirm_password"
                        placeholder="{{ @Helper::language('enter_confirm_password') }}">
                    <div class="toggle-password">
                        <button type="button" class="show-password"><i class="icon-eye"></i></button>
                        <button type="button" class="hide-password"><i class="icon-eye-slash"></i></button>
                    </div>
                    <span id="confirm-password-error" class="red-text"></span>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="solid-button w-100"
                    id="submitDetail">{{ @Helper::language('sign_up_btn') }}</button>

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

                <p class="text-center">{{ @Helper::language('register_already_a_member') }}<a
                        href="{{ route('websitelogin') }}">{{ @Helper::language('login_label') }}</a></p>
            </div>
        </form>
    </div>
@endsection

@push('after-scripts')
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $.validator.setDefaults({
            ignore: [],
        });

        function isNumberKey(evt) {
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32) return false;
            return true;
        }

        function onlyString(evt) {
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
                return false;
            return true;
        }

        $('#phone').on("input", function() {
            this.value = this.value.replace(/[^0-9\.]/g, '');
            $(this).val($(this).val().replace(/^\s+/g, ''));
        });

        $('#age').on("input", function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            this.value = this.value.replace(/^0+/, '');
        });

        var test = $("#register_form").validate({
            submitHandler: function() {
                var proceedSubmit = function(token) {
                    var form_data = new FormData($('#register_form')[0]);
                    if (token) {
                        form_data.set('g-recaptcha-response', token);
                    }
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
                        },
                        success: function(response) {
                            $('.loader').css("visibility", "visible");
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                var url = "{{ route('websitesendotp') }}";
                                window.location.href = url;
                            }
                        },
                        error: function(errors) {
                            $('.loader').css("visibility", "hidden");
                            var errors = errors.responseJSON;
                            $("span#first-name-error,span#last-name-error,span#email-error,span#phone-error,span#password-error,span#age-error,span#confirm-password-error")
                                .text('');
                            if (errors.first_name) {
                                $("span#first-name-error").text(errors.first_name[0]);
                            }
                            if (errors.last_name) {
                                $("span#last-name-error").text(errors.last_name[0]);
                            }
                            if (errors.age) {
                                $("span#age-error").text(errors.age[0]);
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
                            if (errors['g-recaptcha-response']) {
                                swal("Security Check", errors['g-recaptcha-response'][0], "error");
                            }
                        }
                    });
                };

                if (typeof withReCaptcha === 'function') {
                    withReCaptcha('register', '#register_form', proceedSubmit);
                } else {
                    proceedSubmit('');
                }
            }
        });
    </script>
@endpush
