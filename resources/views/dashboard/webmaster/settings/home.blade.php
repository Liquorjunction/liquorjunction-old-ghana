@extends('dashboard.layouts.master')
@section('title',  __('backend.generalSettings'))
@push("after-styles")
    <link rel="stylesheet" href="{{ asset('assets/dashboard/js/sweetalert/sweetalert.css') }}">
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <style>
        .modal.in .modal-dialog{
            margin-top: 70px;
        }
        .modal-backdrop{
            display: none;
        }

        *,
        *:before,
        *:after {
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, ".SFNSText-Regular", "Helvetica Neue", "Roboto", "Segoe UI", sans-serif;
        }

        .toggle {
            cursor: pointer;
            display: inline-block;
        }

        .toggle-switch {
            display: inline-block;
            background: #ccc;
            border-radius: 16px;
            width: 58px;
            height: 32px;
            position: relative;
            vertical-align: middle;
            transition: background 0.25s;
        }
        .toggle-switch:before, .toggle-switch:after {
            content: "";
        }
        .toggle-switch:before {
            display: block;
            background: linear-gradient(to bottom, #fff 0%, #eee 100%);
            border-radius: 50%;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.25);
            width: 24px;
            height: 24px;
            position: absolute;
            top: 4px;
            left: 4px;
            transition: left 0.25s;
        }
        .toggle:hover .toggle-switch:before {
            background: linear-gradient(to bottom, #fff 0%, #fff 100%);
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.5);
        }
        .toggle-checkbox:checked + .toggle-switch {
            background: #56c080;
        }
        .toggle-checkbox:checked + .toggle-switch:before {
            left: 30px;
        }

        .toggle-checkbox {
            position: absolute;
            visibility: hidden;
        }

        .toggle-label {
            margin-left: 5px;
            position: relative;
            top: 2px;
        }
    </style>
@endpush
@section('content')
    
    <div class="padding general-setting">
        <div class="">
            <div class="col-sm-12 col-lg-12">
                <div class="p-y">
                    <div class="nav-active-border left b-primary">
                        <ul class="nav nav-sm">

                            {{--<li class="nav-item">
                                <a class="nav-link block {{ ( Session::get('active_tab') == 'frontSettingsTab' || Session::get('active_tab') =="") ? 'active' : '' }}"
                                   href
                                   data-toggle="tab" data-target="#tab-5"
                                   onclick="document.getElementById('active_tab').value='frontSettingsTab'">
                                    &nbsp; {!!  __('backend.frontSettings') !!}</a>
                            </li>--}}
                            <li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'languageSettingsTab' || Session::get('active_tab') =="" ) ? 'active' : '' }}"
                                   href
                                   data-toggle="tab" data-target="#tab-2"
                                   onclick="document.getElementById('active_tab').value='languageSettingsTab'">
                                    &nbsp; {!!  __('backend.languageSettings') !!}</a>
                            </li>
                            {{--<li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'SEOSettingTab') ? 'active' : '' }}"
                                   href
                                   data-toggle="tab" data-target="#tab-3"
                                   onclick="document.getElementById('active_tab').value='SEOSettingTab'">
                                    &nbsp; {!!  __('backend.seoTabTitle') !!}</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'registrationSettingsTab') ? 'active' : '' }}"
                                   href
                                   data-toggle="tab" data-target="#tab-4"
                                   onclick="document.getElementById('active_tab').value='registrationSettingsTab'">
                                    &nbsp; {!!  __('backend.registrationSettings') !!}</a>
                            </li>--}}
                            <li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'mailSettingsTab') ? 'active' : '' }}"
                                   href data-toggle="tab" data-target="#tab-7"
                                   onclick="document.getElementById('active_tab').value='mailSettingsTab'">
                                    &nbsp; {!!  __('backend.mailSettings') !!}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'webInfo') ? 'active' : '' }}"
                                   href data-toggle="tab" data-target="#tab-8"
                                   onclick="document.getElementById('active_tab').value='webInfo'">
                                    &nbsp; Website Information</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link block {{ (Session::get('active_tab') == 'maintenance') ? 'active' : '' }}"
                                href data-toggle="tab" data-target="#tab-12"
                                onclick="document.getElementById('active_tab').value='maintenance'">
                                 &nbsp; Website Maintenance</a>                             
                            </li>
                           {{-- <li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'googleTagsTab') ? 'active' : '' }}"
                                   href data-toggle="tab" data-target="#tab-9"
                                   onclick="document.getElementById('active_tab').value='googleTagsTab'">
                                    &nbsp; {!!  __('backend.googleTags') !!}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'googleMapsTab') ? 'active' : '' }}"
                                   href data-toggle="tab" data-target="#tab-10"
                                   onclick="document.getElementById('active_tab').value='googleMapsTab'">
                                    &nbsp; {!!  __('backend.googleMaps') !!}</a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'analyticsTab') ? 'active' : '' }}"
                                   href data-toggle="tab" data-target="#tab-11"
                                   onclick="document.getElementById('active_tab').value='analyticsTab'">
                                    &nbsp; {!!  __('backend.analyticsSettings') !!}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'appsSettingsTab') ? 'active' : '' }}"
                                   href data-toggle="tab" data-target="#tab-1"
                                   onclick="document.getElementById('active_tab').value='appsSettingsTab'">
                                    &nbsp; {!!  __('backend.appsSettings') !!}</a>
                            </li>
                           <li class="nav-item">
                                <a class="nav-link block {{  ( Session::get('active_tab') == 'restfulAPITab') ? 'active' : '' }}"
                                   href
                                   data-toggle="tab" data-target="#tab-6"
                                   onclick="document.getElementById('active_tab').value='restfulAPITab'">
                                    &nbsp; {!!  __('backend.restfulAPI') !!}</a>
                            </li>--}}
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-12 light lt locale-settings">

                {{Form::open(['route'=>['webmasterSettingsUpdate'], 'id' => 'setting-form', 'method'=>'POST', 'files'=>true])}}
                <input type="hidden" id="active_tab" name="active_tab" value="{{ Session::get('active_tab') }}"/>
                <div class="tab-content pos-rlt">

                    <button type="button" id="save-settings-btn"
                            class="btn primary m-a pull-right" onclick="minMaxDyration();"> <i class="material-icons">&#xe31b;</i> {{ __('backend.update') }}</button>


                    {{-- @include('dashboard.webmaster.settings.front') --}}
                    @include('dashboard.webmaster.settings.language')
                    {{-- @include('dashboard.webmaster.settings.seo') --}}
                    {{-- @include('dashboard.webmaster.settings.login') --}}
                    {{-- @include('dashboard.webmaster.settings.api') --}}
                    @include('dashboard.webmaster.settings.mail')
                    @include('dashboard.webmaster.settings.maintenance')
                    {{-- @include('dashboard.webmaster.settings.captcha') --}}
                    {{-- @include('dashboard.webmaster.settings.tags') --}}
                    {{-- @include('dashboard.webmaster.settings.maps') --}}
                    {{-- @include('dashboard.webmaster.settings.analytics') --}}
                    {{-- @include('dashboard.webmaster.settings.apps') --}}
                    @include('dashboard.webmaster.settings.webinfo')
                </div>
                {{Form::close()}}
                {{-- @include('dashboard.webmaster.settings.languages') --}}
            </div>
        </div>
    </div>
@endsection
@push("after-scripts")
    <script src="{{ asset('assets/dashboard/js/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset("assets/dashboard/js/summernote/dist/summernote.js") }}"></script>
    <script type="text/javascript">
        $("input:radio[name=api_status]").click(function () {
            if ($(this).val() == 1) {
                $("#api_key_div").css("display", "block");
            } else {
                $("#api_key_div").css("display", "none");
            }
        });
        $("input:radio[name=geoip_status]").click(function () {
            if ($(this).val() == 1) {
                $("#geoip_service_info").css("display", "block");
            } else {
                $("#geoip_service_info").css("display", "none");
            }
        });

        $('#mail_driver').on('change', function () {
            if ($(this).val() == "sendmail" || $(this).val()=="") {
                $("#smtp_check").hide();

                $("#mail_host").val('');
                $("#mail_port").val('');
                $("#mail_username").val('');
                $("#mail_encryption").val('');
                $("#mail_password").val('');

                $("#mail_host_div").hide();
                $("#mail_port_div").hide();
                $("#mail_username_div").hide();
                $("#mail_password_div").hide();
                $("#mail_encryption_div").hide();
                $("#send_test").show();
                $("#mail_from_div").show();
                if($(this).val()==""){
                    $("#send_test").hide();
                    $("#mail_from_div").hide();
                }

            } else {
                $("#mail_host_div").show();
                $("#mail_port_div").show();
                $("#mail_username_div").show();
                $("#mail_password_div").show();
                $("#mail_encryption_div").show();
                $("#smtp_check").show();
                $("#send_test").show();
                $("#mail_from_div").show();
            }
            if ($(this).val() != "smtp") {
                $("#smtp_check").hide();
            }else{
                $("#smtp_check").show();
            }
        });

        function generate_key() {
            if (!confirm('{!!  __('backend.APIKeyConfirm') !!}')) {
                return false;
            } else {
                $("#api_key").val(Math.floor(Math.random() * 1000000000000000));
            }
        }


        $(document).ready(function () {
            $("#nocaptcha_status2").click(function () {
                $("#nocaptcha_div").css("display", "none");
            });
            $("#nocaptcha_status1").click(function () {
                $("#nocaptcha_div").css("display", "block");
            });

            $("#google_tags_status2").click(function () {
                $("#google_tags_div").css("display", "none");
            });
            $("#google_tags_status1").click(function () {
                $("#google_tags_div").css("display", "block");
            });

            $("#google_maps_status2").click(function () {
                $("#google_maps_div").css("display", "none");
                $("#google_maps_key").val('');
            });
            $("#google_maps_status1").click(function () {
                $("#google_maps_div").css("display", "block");
            });

            $("#login_facebook_status2").click(function () {
                $("#facebook_ids_div").css("display", "none");
            });
            $("#login_facebook_status1").click(function () {
                $("#facebook_ids_div").css("display", "block");
            });

            $("#login_twitter_status2").click(function () {
                $("#twitter_ids_div").css("display", "none");
            });
            $("#login_twitter_status1").click(function () {
                $("#twitter_ids_div").css("display", "block");
            });

            $("#login_google_status2").click(function () {
                $("#google_ids_div").css("display", "none");
            });
            $("#login_google_status1").click(function () {
                $("#google_ids_div").css("display", "block");
            });

            $("#login_linkedin_status2").click(function () {
                $("#linkedin_ids_div").css("display", "none");
            });
            $("#login_linkedin_status1").click(function () {
                $("#linkedin_ids_div").css("display", "block");
            });

            $("#login_github_status2").click(function () {
                $("#github_ids_div").css("display", "none");
            });
            $("#login_github_status1").click(function () {
                $("#github_ids_div").css("display", "block");
            });

            $("#login_bitbucket_status2").click(function () {
                $("#bitbucket_ids_div").css("display", "none");
            });
            $("#login_bitbucket_status1").click(function () {
                $("#bitbucket_ids_div").css("display", "block");
            });

            document.getElementById('timezone').value = '{!! $WebmasterSetting->timezone !!}';

        });
        $(function(){
            $(".backend_path").keypress(function(event){
                var ew = event.which;
                if(ew == 32)
                    return true;
                if(48 <= ew && ew <= 57)
                    return true;
                if(65 <= ew && ew <= 90)
                    return true;
                if(97 <= ew && ew <= 122)
                    return true;
                return false;
            });
        });

        $('#smtp_check').click(function () {
            if ($("#mail_host").val() != "" && $("#mail_port").val() != "") {
                $('#smtp_check').html("<img src=\"{{ asset('assets/dashboard/images/loading.gif') }}\" style=\"height: 20px\"/> {!! __('backend.smtpCheck') !!}");
                $('#smtp_check').prop('disabled', true);
                $('#mail_save_btn').prop('disabled', true);

                var xhr = $.ajax({
                    type: "POST",
                    url: "<?php echo route("mailSMTPCheck"); ?>",
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        "mail_driver": $("#mail_driver").val(),
                        "mail_host": $("#mail_host").val(),
                        "mail_port": $("#mail_port").val(),
                        "mail_username": $("#mail_username").val(),
                        "mail_password": $("#mail_password").val(),
                        "mail_encryption": $("#mail_encryption").val(),
                    },
                    success: function (result) {
                        var obj_result = jQuery.parseJSON(result);
                        if (obj_result.stat == 'success') {
                            swal({
                                title: "<span class='text-success'>{{ __("backend.smtpCheckSuccess") }}</span>",
                                text: "{{ __("backend.smtpCheckSuccessMsg") }}",
                                html: true,
                                type: "success",
                                confirmButtonText: "{{ __("backend.close") }}",
                                confirmButtonColor: "#acacac",
                                timer: 5000,
                            });
                        } else {
                            swal({
                                title: "<span class='text-danger'>{{ __("backend.smtpCheck") }}</span>",
                                text: "<span class='text-danger' dir='ltr'>" + obj_result.error + "</span>",
                                html: true,
                                type: "error",
                                confirmButtonText: "{{ __("backend.close") }}",
                                confirmButtonColor: "#acacac",
                            });
                        }
                        $('#smtp_check').html("<i class=\"fa fa-bolt\"></i> {!! __('backend.smtpCheck') !!}");
                        $('#smtp_check').prop('disabled', false);
                        $('#mail_save_btn').prop('disabled', false);
                    }
                });
            }
        });
        $('#send_test').click(function () {
            swal({
                title: "{{ __("backend.sendTestMail") }}",
                text: "{{ __("backend.sendTestMailTo") }}",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                animation: "slide-from-top",
                inputPlaceholder: "email@site.com",
                inputValue: $("#to_email").val(),
                confirmButtonText: "{{ __("backend.continue") }}",
                cancelButtonText: "{{ __("backend.cancel") }}",
                showLoaderOnConfirm: true,
            }, function (inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("{{ __("backend.sendTestMailTo") }}");
                    return false
                }
                if (!validateEmail(inputValue)) {
                    swal.showInputError("{{ __("backend.sendTestMailError") }}");
                    return false
                }
                $("#to_email").val(inputValue);
                var xhr = $.ajax({
                    type: "POST",
                    url: "<?php echo route("mailTest"); ?>",
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        "mail_driver": $("#mail_driver").val(),
                        "mail_host": $("#mail_host").val(),
                        "mail_port": $("#mail_port").val(),
                        "mail_username": $("#mail_username").val(),
                        "mail_password": $("#mail_password").val(),
                        "mail_encryption": $("#mail_encryption").val(),
                        "mail_no_replay": $("#mail_no_replay").val(),
                        "mail_test": $("#to_email").val(),
                    },
                    success: function (result) {
                        var obj_result = jQuery.parseJSON(result);
                        if (obj_result.stat == 'success') {
                            swal({
                                title: "<span class='text-success'>{{ __("backend.mailTestSuccess") }}</span>",
                                text: inputValue,
                                html: true,
                                type: "success",
                                confirmButtonText: "{{ __("backend.close") }}",
                                confirmButtonColor: "#acacac",
                                timer: 5000,
                            });
                        } else {
                            swal({
                                title: "<span class='text-danger'>{{ __("backend.mailTestFailed") }}</span>",
                                text: inputValue,
                                html: true,
                                type: "error",
                                confirmButtonText: "{{ __("backend.close") }}",
                                confirmButtonColor: "#acacac",
                            });
                        }
                    }
                });
            });
        });
        function validateEmail(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }

        

        // update progress bar
        function progressHandlingFunction(e) {
            if (e.lengthComputable) {
                $('progress').attr({value: e.loaded, max: e.total});
                // reset progress on complete
                if (e.loaded == e.total) {
                    $('progress').attr('value', '0.0');
                }
            }
        }
        setTimeout(function() {
        $('#topmsgall').hide();
         }, 5000);

        $("#popular_dance_category").change(function(){
            if($(this).prop("checked") == true){
                $(this).val('1');
            }else{
                $(this).val('0');
            }
        });

        $("#popular_dance_class").change(function(){
            if($(this).prop("checked") == true){
                $(this).val('1');
            }else{
                $(this).val('0');
            }
        });

        $("#popular_instructor").change(function(){
            if($(this).prop("checked") == true){
                $(this).val('1');
            }else{
                $(this).val('0');
            }
        });

        var specialKeys = new Array();
        specialKeys.push(8);

        function IsNumeric(e) {

            var keyCode = e.which ? e.which : e.keyCode
            var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
            //document.getElementById("error").style.display = ret ? "none" : "inline";  
            return ret;
        }

        function minMaxDyration(){
            var min = $("[name='min_duration']").val();
            var max = $("[name='max_duration']").val();
            if(min != '' && min != null && max != '' && max != null){
                min = parseInt(min);
                max = parseInt(max);
                if(min > max){
                    alert("Min duration cannot greater than max duration");
                }else{
                    $('#setting-form').submit();
                }
            }else{
                    $('#setting-form').submit();
                }
        }
    </script>
    
@endpush
