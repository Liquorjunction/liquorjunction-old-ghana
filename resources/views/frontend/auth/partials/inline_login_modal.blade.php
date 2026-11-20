<!-- Existing Account Detected Inline Login Modal -->
<div class="modal fade" id="existingAccountModal" tabindex="-1" role="dialog" aria-labelledby="existingAccountModalLabel" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 460px; margin: 1.75rem auto;">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 12px 40px rgba(0,0,0,0.25); overflow: hidden;">
            <!-- Modal Header -->
            <div class="modal-header d-flex align-items-center justify-content-between" style="background: #242424; color: #fff; border-bottom: 2px solid #fbb516; padding: 18px 24px; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                <div class="d-flex align-items-center gap-2">
                    <span style="display: inline-flex; width: 34px; height: 34px; border-radius: 50%; background: rgba(251, 181, 22, 0.18); color: #fbb516; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fbb516" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <h5 class="modal-title mb-0 text-white" id="existingAccountModalLabel" style="font-weight: 600; font-size: 18px; letter-spacing: 0.3px; text-transform: uppercase;">
                        Details Already Registered
                    </h5>
                </div>
                <button type="button" class="btn p-0 border-0" id="closeExistingAccountModal" data-bs-dismiss="modal" aria-label="Close" 
                        style="background: transparent; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0.85; transition: opacity 0.3s;"
                        onmouseover="this.style.opacity='1'; this.querySelector('svg path').style.stroke='#fbb516';"
                        onmouseout="this.style.opacity='0.85'; this.querySelector('svg path').style.stroke='#ffffff';">
                    <svg width="18" height="18" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 8L8 24M8 8L24 24" stroke="#ffffff" stroke-width="2.5" stroke-linecap="square" stroke-linejoin="round" style="transition: stroke 0.3s;"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="padding: 28px 24px 32px; background: #ffffff;">
                <p id="existingAccountMessage" style="font-size: 14px; line-height: 22px; color: #555555; margin-bottom: 22px;">
                    You are already registered. Please log in to your account to continue.
                </p>

                <form class="row registration-form p-0 m-0" id="inlineLoginForm" autocomplete="on" onsubmit="return false;">
                    {{-- Email / Phone Readonly Field --}}
                    <div class="col-12 p-0">
                        <div class="form-group mb-3">
                            <label for="inlineLoginEmail">
                                {{ @Helper::language('email_label') }} / {{ @Helper::language('phone_number') }}
                            </label>
                            <input type="text" id="inlineLoginEmail" name="email" readonly 
                                style="background-color: #f7f7f7; border: 1px solid #dddddd; color: #555555; cursor: not-allowed; font-weight: 500; border-radius: 20px; padding: 12px 20px;">
                        </div>
                    </div>

                    {{-- Password Field with Theme Eye Toggle --}}
                    <div class="col-12 p-0">
                        <div class="form-group form-password mb-3">
                            <label for="inlineLoginPassword">
                                {{ @Helper::language('password_label') }}<span class="valid_field">*</span>
                            </label>
                            <input type="password" id="inlineLoginPassword" name="password" placeholder="{{ @Helper::language('enter_password_placeholder') }}" required autocomplete="current-password" style="border-radius: 20px; padding: 12px 50px 12px 20px;">
                            <div class="toggle-password" style="position: absolute; top: 45px; right: 18px; cursor: pointer;">
                                <button type="button" class="show-password" tabindex="-1"><i class="icon-eye"></i></button>
                                <button type="button" class="hide-password" tabindex="-1"><i class="icon-eye-slash"></i></button>
                            </div>
                            <span class="help-block" id="inlinePasswordHelpBlock">
                                <span class="red-text" id="inlineLoginError" style="display: none; font-size: 14px; margin-top: 6px; float: left; width: 100%;"></span>
                            </span>
                            <div class="forget-pass" style="clear: both; padding-top: 6px;">
                                <a href="{{ route('userforgotpassword') }}" target="_blank">{{ @Helper::language('forgot_password_text') }}</a>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-12 p-0 mt-3">
                        <button type="button" id="inlineLoginActionBtn" class="solid-button w-100" style="border-radius: 20px;">
                            LOG IN TO ACCOUNT
                        </button>
                        <button type="button" id="useDifferentEmailModalBtn" class="outline-theme-btn w-100 mt-2" data-bs-dismiss="modal" style="border-radius: 20px;">
                            USE DIFFERENT EMAIL / PHONE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    #existingAccountModal .modal-content {
        border-radius: 20px !important;
        border: none !important;
    }
    #existingAccountModal .modal-header {
        border-top-left-radius: 20px !important;
        border-top-right-radius: 20px !important;
    }
    #existingAccountModal .form-group {
        margin-bottom: 20px;
    }
    #existingAccountModal .form-group label {
        font-size: 14px;
        font-weight: 500;
        line-height: 21px;
        color: #242424;
        margin-bottom: 8px;
        display: block;
        text-align: left;
    }
    #existingAccountModal input {
        font-size: 14px;
        font-weight: 400;
        line-height: 21px;
        background: #ffffff;
        border: 1px solid #dddddd;
        color: #242424;
        width: 100%;
        border-radius: 20px !important;
        box-shadow: none;
        padding: 12px 20px;
        transition: all 0.4s ease-out 0s;
    }
    #existingAccountModal input:focus {
        border-color: #fbb516 !important;
        outline: none;
        box-shadow: none !important;
    }
    #existingAccountModal .form-password {
        position: relative;
    }
    #existingAccountModal .form-password input {
        padding-right: 50px !important;
    }
    #existingAccountModal .toggle-password {
        position: absolute;
        top: 45px;
        right: 18px;
        cursor: pointer;
    }
    #existingAccountModal .solid-button {
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        padding: 12px 30px;
        cursor: pointer;
        background: #fbb516 !important;
        color: #242424 !important;
        transition: all 0.4s;
        border-radius: 20px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        border: none !important;
        letter-spacing: 0.5px;
    }
    #existingAccountModal .solid-button:hover {
        background: #242424 !important;
        color: #fbb516 !important;
    }
    #existingAccountModal .outline-theme-btn {
        font-weight: 400;
        font-size: 15px;
        line-height: 24px;
        padding: 11px 24px;
        cursor: pointer;
        background: #ffffff;
        color: #242424;
        border: 1px solid #242424 !important;
        transition: all 0.4s;
        border-radius: 20px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-decoration: none;
    }
    #existingAccountModal .outline-theme-btn:hover {
        background: #242424;
        color: #ffffff;
        border-color: #242424 !important;
    }
    #existingAccountModal .forget-pass {
        margin-top: 8px;
        display: flex;
        justify-content: flex-end;
    }
    #existingAccountModal .forget-pass a {
        font-size: 13px;
        font-weight: 500;
        color: #695f70;
        text-decoration: underline;
        transition: color 0.3s;
    }
    #existingAccountModal .forget-pass a:hover {
        color: #fbb516;
    }
    #existingAccountModal .valid_field {
        color: #ff4444;
        margin-left: 2px;
    }
    #existingAccountModal .red-text {
        color: #ff4444;
        font-size: 13px;
    }
</style>
