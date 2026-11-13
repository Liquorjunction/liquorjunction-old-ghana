@extends('frontend.layouts.app')
@section('title','Forgot Password')
@section('content')
@include('sweetalert::alert')

<div class="loader" id="loader"></div>
<section class="registration">
    <div class="container">
        <div id="Register">
            <div class="registration-content">
                <h1 class="text-center mb-0">{{@Helper::language('forgot_password_heading')}} </h1>
                <div class="registration-card">
                    <form action="#" class="row registration-form" method="POST" enctype="multipart/form-data" id="forgot_password_form">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">{{@Helper::language('email_label')}} <span class="valid_field">*</span></label>
                                <input type="email" placeholder="{{@Helper::language('enter_email_place')}}" name="email" id="email">
                            </div>
                            <span class="help-block" id="errorMessagecommon" style="display:none">
                                <span style="color: red;display: none;" id="errorMsgcommon" class='validate text-center'></span>
                            </span>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="solid-button w-100">{{@Helper::language('submit_btn')}}</button>
                            <div class="registration-social">
                                {{-- <p class="text-sm grey-text">{{@Helper::language('login_or_continue_with')}}</p>
                                <ul>
                                    <li>
                                        <a href="https://www.facebook.com/" target="_blank">
                                            <img src="{{ asset('assets/frontend/images/icon_login_facebook.svg')}}" alt="">
                                        </a>
                                    </li>
                                    <!--<li>-->
                                    <!--    <a href="https://www.instagram.com/" target="_blank">-->
                                    <!--        <img src="{{ asset('assets/frontend/images/icon_login_insta.svg')}}" alt="">-->
                                    <!--    </a>-->
                                    <!--</li>-->
                                    <li>
                                        <a href="https://www.google.com/" target="_blank">
                                            <img src="{{ asset('assets/frontend/images/icon_login_google.svg')}}" alt="">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.apple.com/" target="_blank">
                                            <img src="{{ asset('assets/frontend/images/icon_login_apple.svg')}}" alt="">
                                        </a>
                                    </li>
                                </ul> --}}
                            </div>
                             {{-- <p class="text-center">{{@Helper::language('enter_the_verification_code_we_just_sent_on_email_address')}}<a href="{{ route('websitelogin') }}" class="d-block">{{@Helper::language('back_to_login_msg')}}</a></p> --}}
                            <p class="text-center"><a href="{{ route('websitelogin') }}" class="d-block">{{@Helper::language('back_to_login_msg')}}</a></p>
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
    var validation_email_required="{{ \Helper::language('email_field_required'); }}";
    var validation_email="{{ \Helper::language('enter_valid_email_validation'); }}";
    var test = $("#forgot_password_form").validate({
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            email: {
                required: true,
                email: true
            },

        },
        // in 'messages' user have to specify message as per rules
        messages: {

            email: {
                required: validation_email_required,
                email: validation_email,
            },
        },
        submitHandler: function() {
            var form_data = new FormData($('#forgot_password_form')[0]);
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
                    // console.log('test'+response);
                    $('.loader').css("visibility", "visible");
                    var url = "{{route('websiteforgototp')}}";
                    window.location.href = url;
                },
                error: function(errors) {
                    // alert(errors);
                    // console.log(response)
                    $('.loader').css("visibility", "hidden");
                    var erroJson = JSON.parse(errors.responseText);
                    // console.log(erroJson.status);
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
        }
    });
</script>

@endsection