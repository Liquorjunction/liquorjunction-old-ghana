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
                            <div class="form-group form-password">
                                <label for="">{{@Helper::language('new_password_label')}}<span
                                    class="valid_field">*</span></label>
                                    <input type="password" placeholder="{{@Helper::language('enter_new_password_msg')}}" name="new_password" id="new_password">
                                     <div class="toggle-password">
                                    <button type="button" class="show-password">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="icon_eye_open">
                                                <path id="Vector" d="M7.79188 12C7.79188 9.86953 9.50797 8.14286 11.6254 8.14286C13.7428 8.14286 15.4589 9.86953 15.4589 12C15.4589 14.1305 13.7428 15.8571 11.6254 15.8571C9.50797 15.8571 7.79188 14.1305 7.79188 12ZM11.6254 14.4107C12.9491 14.4107 14.0213 13.3319 14.0213 12C14.0213 10.6681 12.9491 9.58929 11.6254 9.58929C11.6044 9.58929 11.5864 9.58929 11.5385 9.58929C11.6044 9.74297 11.6254 9.90569 11.6254 10.0714C11.6254 11.1352 10.7658 12 9.70863 12C9.54391 12 9.38218 11.9789 9.22944 11.9126C9.22944 11.9608 9.22944 11.9789 9.22944 11.9729C9.22944 13.3319 10.3016 14.4107 11.6254 14.4107ZM5.85776 7.67879C7.26777 6.36013 9.20548 5.25 11.6254 5.25C14.0453 5.25 15.983 6.36013 17.3936 7.67879C18.7952 8.98661 19.7326 10.5265 20.1759 11.6294C20.2747 11.8674 20.2747 12.1326 20.1759 12.3706C19.7326 13.4464 18.7952 14.9863 17.3936 16.3212C15.983 17.6411 14.0453 18.75 11.6254 18.75C9.20548 18.75 7.26777 17.6411 5.85776 16.3212C4.45613 14.9863 3.51932 13.4464 3.07371 12.3706C2.97543 12.1326 2.97543 11.8674 3.07371 11.6294C3.51932 10.5265 4.45613 8.98661 5.85776 7.67879ZM11.6254 6.69643C9.67269 6.69643 8.06741 7.58839 6.8365 8.7365C5.68345 9.81529 4.8874 11.0689 4.48069 12C4.8874 12.904 5.68345 14.1847 6.8365 15.2635C8.06741 16.4116 9.67269 17.3036 11.6254 17.3036C13.5781 17.3036 15.1833 16.4116 16.4143 15.2635C17.5673 14.1847 18.337 12.904 18.7713 12C18.337 11.0689 17.5673 9.81529 16.4143 8.7365C15.1833 7.58839 13.5781 6.69643 11.6254 6.69643Z" fill="#858584" />
                                            </g>
                                        </svg>
                                    </button>
                                    <button type="button" class="hide-password">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="icon_eye_open">
                                                <path id="Vector" d="M4.04591 5.38462C3.76561 5.16841 3.35862 5.21851 3.13761 5.49272C2.9166 5.76693 2.96781 6.16507 3.24812 6.38128L19.2041 18.6154C19.4844 18.8316 19.8914 18.7815 20.1124 18.5073C20.3334 18.2331 20.2822 17.8349 20.0019 17.6187L17.1665 15.4461C18.2338 14.3756 18.9561 13.176 19.32 12.3243C19.4089 12.116 19.4089 11.884 19.32 11.6757C18.9184 10.7344 18.0748 9.36334 16.8134 8.21903C15.5466 7.06417 13.8028 6.09388 11.625 6.09388C9.78683 6.09388 8.25592 6.78732 7.06192 7.69697L4.04591 5.38462ZM9.01329 9.19196C9.70058 8.57761 10.6197 8.20321 11.625 8.20321C13.7677 8.20321 15.5062 9.90385 15.5062 12C15.5062 12.6565 15.3364 13.2735 15.0372 13.8114L13.9968 13.0151C14.137 12.704 14.2125 12.3612 14.2125 12C14.2125 10.6026 13.0535 9.46881 11.625 9.46881C11.5495 9.46881 11.4741 9.47144 11.3986 9.47935C11.5414 9.72456 11.625 10.0093 11.625 10.3125C11.625 10.5815 11.5603 10.8346 11.4471 11.0587L9.01329 9.19196ZM15.0264 17.0492L13.0535 15.5305C12.6115 15.7019 12.129 15.7968 11.625 15.7968C9.48226 15.7968 7.74382 14.0961 7.74382 12C7.74382 11.8181 7.75729 11.6414 7.78155 11.4674L5.23992 9.50836C4.6254 10.2914 4.18607 11.0719 3.93002 11.6757C3.84107 11.884 3.84107 12.116 3.93002 12.3243C4.33161 13.2656 5.17523 14.6367 6.43661 15.781C7.70339 16.9358 9.44723 17.9061 11.625 17.9061C12.9133 17.9061 14.048 17.566 15.0264 17.0492Z" fill="#858584" />
                                            </g>
                                        </svg>
                                    </button>
                                </div>
                                    <span class="help-block" id="errorMessagecommon1" style="display:none">
                                        <span class="" style="color: #FF4444;font-size:14px;display: none;" id="errorMsgcommon1" class='validate validate-error text-center'></span>
                                    </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group form-password">
                                <label for="">{{@Helper::language('confirm_password_label')}}<span
                                    class="valid_field">*</span></label>
                                    <input type="password" placeholder="{{@Helper::language('enter_old_password_msg')}}" name="confirm_password" id="confirm_password">
                                     <div class="toggle-password">
                                    <button type="button" class="show-password">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="icon_eye_open">
                                                <path id="Vector" d="M7.79188 12C7.79188 9.86953 9.50797 8.14286 11.6254 8.14286C13.7428 8.14286 15.4589 9.86953 15.4589 12C15.4589 14.1305 13.7428 15.8571 11.6254 15.8571C9.50797 15.8571 7.79188 14.1305 7.79188 12ZM11.6254 14.4107C12.9491 14.4107 14.0213 13.3319 14.0213 12C14.0213 10.6681 12.9491 9.58929 11.6254 9.58929C11.6044 9.58929 11.5864 9.58929 11.5385 9.58929C11.6044 9.74297 11.6254 9.90569 11.6254 10.0714C11.6254 11.1352 10.7658 12 9.70863 12C9.54391 12 9.38218 11.9789 9.22944 11.9126C9.22944 11.9608 9.22944 11.9789 9.22944 11.9729C9.22944 13.3319 10.3016 14.4107 11.6254 14.4107ZM5.85776 7.67879C7.26777 6.36013 9.20548 5.25 11.6254 5.25C14.0453 5.25 15.983 6.36013 17.3936 7.67879C18.7952 8.98661 19.7326 10.5265 20.1759 11.6294C20.2747 11.8674 20.2747 12.1326 20.1759 12.3706C19.7326 13.4464 18.7952 14.9863 17.3936 16.3212C15.983 17.6411 14.0453 18.75 11.6254 18.75C9.20548 18.75 7.26777 17.6411 5.85776 16.3212C4.45613 14.9863 3.51932 13.4464 3.07371 12.3706C2.97543 12.1326 2.97543 11.8674 3.07371 11.6294C3.51932 10.5265 4.45613 8.98661 5.85776 7.67879ZM11.6254 6.69643C9.67269 6.69643 8.06741 7.58839 6.8365 8.7365C5.68345 9.81529 4.8874 11.0689 4.48069 12C4.8874 12.904 5.68345 14.1847 6.8365 15.2635C8.06741 16.4116 9.67269 17.3036 11.6254 17.3036C13.5781 17.3036 15.1833 16.4116 16.4143 15.2635C17.5673 14.1847 18.337 12.904 18.7713 12C18.337 11.0689 17.5673 9.81529 16.4143 8.7365C15.1833 7.58839 13.5781 6.69643 11.6254 6.69643Z" fill="#858584" />
                                            </g>
                                        </svg>
                                    </button>
                                    <button type="button" class="hide-password">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="icon_eye_open">
                                                <path id="Vector" d="M4.04591 5.38462C3.76561 5.16841 3.35862 5.21851 3.13761 5.49272C2.9166 5.76693 2.96781 6.16507 3.24812 6.38128L19.2041 18.6154C19.4844 18.8316 19.8914 18.7815 20.1124 18.5073C20.3334 18.2331 20.2822 17.8349 20.0019 17.6187L17.1665 15.4461C18.2338 14.3756 18.9561 13.176 19.32 12.3243C19.4089 12.116 19.4089 11.884 19.32 11.6757C18.9184 10.7344 18.0748 9.36334 16.8134 8.21903C15.5466 7.06417 13.8028 6.09388 11.625 6.09388C9.78683 6.09388 8.25592 6.78732 7.06192 7.69697L4.04591 5.38462ZM9.01329 9.19196C9.70058 8.57761 10.6197 8.20321 11.625 8.20321C13.7677 8.20321 15.5062 9.90385 15.5062 12C15.5062 12.6565 15.3364 13.2735 15.0372 13.8114L13.9968 13.0151C14.137 12.704 14.2125 12.3612 14.2125 12C14.2125 10.6026 13.0535 9.46881 11.625 9.46881C11.5495 9.46881 11.4741 9.47144 11.3986 9.47935C11.5414 9.72456 11.625 10.0093 11.625 10.3125C11.625 10.5815 11.5603 10.8346 11.4471 11.0587L9.01329 9.19196ZM15.0264 17.0492L13.0535 15.5305C12.6115 15.7019 12.129 15.7968 11.625 15.7968C9.48226 15.7968 7.74382 14.0961 7.74382 12C7.74382 11.8181 7.75729 11.6414 7.78155 11.4674L5.23992 9.50836C4.6254 10.2914 4.18607 11.0719 3.93002 11.6757C3.84107 11.884 3.84107 12.116 3.93002 12.3243C4.33161 13.2656 5.17523 14.6367 6.43661 15.781C7.70339 16.9358 9.44723 17.9061 11.625 17.9061C12.9133 17.9061 14.048 17.566 15.0264 17.0492Z" fill="#858584" />
                                            </g>
                                        </svg>
                                    </button>
                                </div>
                                    <span class="help-block" id="errorMessagecommon" style="display:none">
                                        <span class="" style="color: #FF4444;font-size:14px;display: none;" id="errorMsgcommon" class='validate validate-error text-center'></span>
                                    </span>
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