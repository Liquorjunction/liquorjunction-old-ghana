@extends('frontend.layouts.app')
@section('title', 'Continue as Guest')
@section('content')
    @include('sweetalert::alert')
    <style>
        .valid_field {
            color: red;
        }
        .phone-number {
            position: relative;
        }
        .phone-number .numbers {
            border: none !important;
            background-color: transparent !important;
            box-shadow: none !important;
            outline: none !important;
            z-index: 5;
            cursor: pointer;
        }
        label.error {
            color: #ff4444;
            font-size: 14px;
            margin-top: 6px;
            display: block;
            width: 100%;
            font-weight: 400;
            text-align: left;
        }
    </style>
    <div class="loader" id="loader"></div>
    <section class="registration">
        <div class="container">
            <div id="Register" class="register">
                <div class="registration-content">
                    <h1 class="text-center mb-0">Continue as Guest</h1>
                    <div class="registration-card">
                        <form class="row registration-form" id="guest_login_form" action="" novalidate>
                            @csrf
                            <input type="hidden" name="is_guest_user" value="1">
                            @if(isset($redirectTo))
                                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
                            @endif

                            {{-- Email Address --}}
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="email">{{ @Helper::language('email_label') }}<span class="valid_field">*</span></label>
                                    <input type="email" name="email" id="email" placeholder="{{ @Helper::language('enter_email_place') }}" required>
                                    <span id="guest-email-error" class="red-text"></span>
                                </div>
                            </div>

                            {{-- Phone Number --}}
                            <div class="col-12">
                                <div class="form-group has-validation">
                                    <label for="phone">{{ @Helper::language('phone_number') }}<span class="valid_field">*</span></label>
                                    <div class="input-group phone-number">
                                        <select class="numbers" name="phone_code" id="phone_code">
                                            @if(isset($phonecode) && count($phonecode) > 0)
                                                @foreach($phonecode as $value)
                                                    <option value="{{$value->phonecode}}" {{ $value->phonecode == '233' ? 'selected' : '' }}>+{{$value->phonecode}}</option>
                                                @endforeach
                                            @else
                                                <option value="233" selected>+233</option>
                                            @endif
                                        </select>
                                        <input type="tel" name="phone" id="phone" placeholder="{{ @Helper::language('enter_phone_number_place') }}" required>
                                    </div>
                                    <span id="guest-phone-error" class="red-text"></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="solid-button w-100" id="submitGuest">CONTINUE AS GUEST</button>

                                <div class="registration-social">
                                    <p class="text-sm grey-text">{{ @Helper::language('login_or_continue_with') }}</p>
                                    <ul>
                                        <li>
                                            <a href="{{ route('auth.facebook') }}" target="_blank">
                                                <img src="{{ asset('assets/frontend/images/icon_login_facebook.svg') }}" alt="">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('auth.google') }}" target="_blank">
                                                <img src="{{ asset('assets/frontend/images/icon_login_google.svg') }}" alt="">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('auth.apple') }}" target="_blank">
                                                <img src="{{ asset('assets/frontend/images/icon_login_apple.svg') }}" alt="">
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <p class="text-center">{{ @Helper::language('login_not_register_yet') }}<a
                                        href="{{ route('websiteregister') }}">{{ @Helper::language('sign_up_btn') }}</a>
                                </p>
                                <p class="text-center">{{ @Helper::language('register_already_a_member') }}<a href="{{ route('websitelogin') }}">{{ @Helper::language('login_label') }}</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('frontend.auth.partials.inline_login_modal')

<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
    var lastCheckedEmail = '';
    var lastCheckedPhone = '';
    var isRegisteredUser = false;

    // Only allow numbers in phone input
    $('#phone').on("input", function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });

    function openInlineLoginModal(identifier, message) {
        $('#inlineLoginEmail').val(identifier);
        if (message) {
            $('#existingAccountMessage').text(message);
        }
        $('#inlineLoginPassword').val('');
        $('#inlineLoginError').hide().text('');
        var modalEl = document.getElementById('existingAccountModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else if (typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('show');
        } else {
            $(modalEl).addClass('show').css('display', 'block');
            $('body').addClass('modal-open');
            if ($('#inlineModalBackdrop').length === 0) {
                $('body').append('<div class="modal-backdrop fade show" id="inlineModalBackdrop"></div>');
            }
        }
        setTimeout(function() {
            $('#inlineLoginPassword').focus();
        }, 350);
    }

    function closeInlineLoginModal() {
        var modalEl = document.getElementById('existingAccountModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        } else if (typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('hide');
        }
        $(modalEl).removeClass('show').css('display', 'none');
        $('body').removeClass('modal-open');
        $('#inlineModalBackdrop').remove();
        $('.modal-backdrop').remove();
    }

    function checkCustomerAccount(payload, callback) {
        var email = (payload.email || '').trim().toLowerCase();
        var phone = (payload.phone || '').trim();

        if (!email && !phone) {
            if (callback) callback(false);
            return;
        }

        $.ajax({
            url: "{{ route('checkout.check-existing-user') }}",
            type: "POST",
            data: JSON.stringify({ email: email, phone: phone }),
            contentType: "application/json",
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            dataType: 'json',
            success: function(res) {
                if (email) lastCheckedEmail = email;
                if (phone) lastCheckedPhone = phone;

                if (res && res.exists) {
                    isRegisteredUser = true;
                    var identifier = res.email || email || res.phone || phone;
                    openInlineLoginModal(identifier, res.message);
                    if (callback) callback(true);
                } else {
                    isRegisteredUser = false;
                    if (callback) callback(false);
                }
            },
            error: function() {
                if (callback) callback(false);
            }
        });
    }

    // Check account on email blur
    $('#email').on('blur', function() {
        var emailVal = $(this).val().trim();
        if (emailVal && emailVal.indexOf('@') !== -1 && emailVal !== lastCheckedEmail) {
            checkCustomerAccount({ email: emailVal });
        }
    });

    // Check account on phone blur
    $('#phone').on('blur', function() {
        var phoneVal = $(this).val().trim();
        if (phoneVal && phoneVal.length >= 8 && phoneVal !== lastCheckedPhone) {
            checkCustomerAccount({ phone: phoneVal });
        }
    });

    // Enter key submit in inline login modal
    $('#inlineLoginForm').on('submit', function(e) {
        e.preventDefault();
        $('#inlineLoginActionBtn').click();
    });

    // Inline login submit
    $('#inlineLoginActionBtn').on('click', function() {
        var email = $('#inlineLoginEmail').val().trim();
        var password = $('#inlineLoginPassword').val();
        var btn = $(this);

        if (!password) {
            $('#inlineLoginError').text('Please enter your password.').show();
            return;
        }

        btn.prop('disabled', true).text('Signing in...');
        $('#inlineLoginError').hide().text('');

        $.ajax({
            url: "{{ route('checkout.inline-login') }}",
            type: "POST",
            data: JSON.stringify({ email: email, password: password }),
            contentType: "application/json",
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    window.location.href = res.redirect || "{{ route('checkout') }}";
                } else {
                    btn.prop('disabled', false).text('Log In to Account');
                    $('#inlineLoginError').text(res.message || 'Invalid email or password.').show();
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Log In to Account');
                var err = xhr.responseJSON;
                $('#inlineLoginError').text(err && err.message ? err.message : 'Invalid credentials. Please try again.').show();
            }
        });
    });

    $('#closeExistingAccountModal, #useDifferentEmailModalBtn').on('click', function() {
        closeInlineLoginModal();
        $('#email').val('').focus();
        lastCheckedEmail = '';
        lastCheckedPhone = '';
        isRegisteredUser = false;
    });

    // Apply validation rules & proper placement
    $('#guest_login_form').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                minlength: 8,
                maxlength: 15
            }
        },
        messages: {
            email: {
                required: "Please enter your email address",
                email: "Please enter a valid email address"
            },
            phone: {
                required: "Please enter your phone number",
                minlength: "Phone number must be at least 8 digits",
                maxlength: "Phone number cannot exceed 15 digits"
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") === "phone") {
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            var emailVal = $('#email').val().trim();
            var phoneVal = $('#phone').val().trim();

            // First ensure it's not a registered user
            checkCustomerAccount({ email: emailVal, phone: phoneVal }, function(exists) {
                if (exists) {
                    return false; // Modal is opened
                }

                var form_data = new FormData(form);
                var action_url = "{{ route('websiteregisterpost') }}";
                var csrf = "{{ csrf_token() }}";

                $.ajax({
                    url: action_url,
                    data: form_data,
                    headers: { 'X-CSRF-TOKEN': csrf },
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
                            window.location.href = "{{ route('checkout') }}";
                        }
                    },
                    error: function(errors) {
                        $('.loader').css("visibility", "hidden");
                        var errs = errors.responseJSON;
                        $("#guest-email-error").text('');
                        $("#guest-phone-error").text('');
                        if (errs && typeof errs === 'object') {
                            if (errs.email) {
                                $("#guest-email-error").text(errs.email[0]);
                            }
                            if (errs.phone) {
                                $("#guest-phone-error").text(errs.phone[0]);
                            }
                        } else {
                            $("#guest-email-error").text('An error occurred. Please try again.');
                        }
                    }
                });
            });
        }
    });
</script>
@endsection
