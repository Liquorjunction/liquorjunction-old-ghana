
<div class="tab-pane {{ (Session::get('active_tab') == 'googleRecaptchaTab') ? 'active' : '' }}" id="tab-recaptcha">
    <div class="p-a-sm">
        <h5>Google reCAPTCHA (v3)</h5>
    </div>

    <div class="p-a-sm col-md-12">
        <div class="row">
            <div class="col-sm-6 mb-2">
                <label>Google reCAPTCHA Status:</label><br>
                <label>
                    <input type="radio" name="nocaptcha_status" id="nocaptcha_status1" value="1"
                        {{ (isset($WebmasterSetting->nocaptcha_status) && $WebmasterSetting->nocaptcha_status == 1) ? 'checked' : '' }}>
                    Active
                </label>
                <label class="ms-3" style="margin-left: 15px;">
                    <input type="radio" name="nocaptcha_status" id="nocaptcha_status2" value="0"
                        {{ (!isset($WebmasterSetting->nocaptcha_status) || $WebmasterSetting->nocaptcha_status == 0) ? 'checked' : '' }}>
                    Not Active
                </label>
            </div>
        </div>
    </div>

    <div id="nocaptcha_div" class="p-a-sm col-md-12" {!! (!$WebmasterSetting->nocaptcha_status) ? "style='display:none'":"" !!}>
        <div class="row">
            <div class="col-sm-6 form-group">
                <label for="nocaptcha_sitekey">Google reCAPTCHA Site Key :</label>
                {!! Form::text('nocaptcha_sitekey', $WebmasterSetting->nocaptcha_sitekey, [
                    'placeholder' => 'Enter Google reCAPTCHA v3 Site Key',
                    'class' => 'form-control',
                    'id' => 'nocaptcha_sitekey',
                    'dir' => 'ltr'
                ]) !!}
            </div>

            <div class="col-sm-6 form-group">
                <label for="nocaptcha_secret">Google reCAPTCHA Secret Key :</label>
                <div style="position: relative;">
                    <input type="password" name="nocaptcha_secret" id="nocaptcha_secret" 
                           class="form-control" placeholder="Enter Google reCAPTCHA v3 Secret Key" 
                           value="{{ $WebmasterSetting->nocaptcha_secret }}" dir="ltr" autocomplete="new-password" style="padding-right: 42px;">
                    <span id="toggle_secret_btn" role="button" title="Show / Hide Secret Key" 
                          style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #777; font-size: 16px; user-select: none; z-index: 5;">
                        <i class="fa fa-eye" id="toggle_secret_icon"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <small class="text-muted">
                    <i class="material-icons" style="font-size: 16px; vertical-align: middle;">&#xe8fd;</i>
                    Keys manage karne ke liye Google Console: 
                    <a href="https://www.google.com/recaptcha/admin" style="text-decoration: underline; color: #0cc2aa;" target="_blank">Google reCAPTCHA Admin Console</a>
                </small>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on('click', '#toggle_secret_btn', function () {
        var $secretInput = $('#nocaptcha_secret');
        var $icon = $('#toggle_secret_icon');
        if ($secretInput.attr('type') === 'password') {
            $secretInput.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $secretInput.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
</script>

