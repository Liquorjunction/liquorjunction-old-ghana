<!-- Existing Account Detected Inline Login Modal -->
<div class="modal fade" id="existingAccountModal" tabindex="-1" role="dialog" aria-labelledby="existingAccountModalLabel" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.25); overflow: hidden;">
            <div class="modal-header" style="background: #242424; color: #fff; border-bottom: 2px solid #e5a624; padding: 18px 24px;">
                <div class="d-flex align-items-center gap-2">
                    <span style="display: inline-flex; width: 36px; height: 36px; border-radius: 50%; background: #e5a624; color: #000; align-items: center; justify-content: center; font-weight: bold;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <h5 class="modal-title mb-0" id="existingAccountModalLabel" style="font-weight: 700; font-size: 18px; color: #fff;">
                        Details Already Registered
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" id="closeExistingAccountModal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.8;"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p id="existingAccountMessage" style="font-size: 14px; color: #555; line-height: 1.5; margin-bottom: 20px;">
                    You are already registered. Please log in to your account to continue.
                </p>

                <form id="inlineLoginForm" autocomplete="on">
                    <div class="form-group mb-3">
                        <label for="inlineLoginEmail" style="font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px; display: block;">
                            Email Address / Phone
                        </label>
                        <input type="text" id="inlineLoginEmail" name="email" class="form-control" readonly 
                            style="background-color: #f8f9fa; border: 1px solid #ced4da; border-radius: 8px; padding: 12px 14px; font-size: 14px; color: #495057; font-weight: 500;">
                    </div>

                    <div id="inlinePasswordGroup" class="form-group mb-3">
                        <label for="inlineLoginPassword" style="font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px; display: block;">
                            Password <span style="color: red;">*</span>
                        </label>
                        <div style="position: relative;">
                            <input type="password" id="inlineLoginPassword" name="password" class="form-control" placeholder="••••••••" required
                                style="border: 1px solid #ced4da; border-radius: 8px; padding: 12px 42px 12px 14px; font-size: 14px;">
                            <button type="button" id="toggleInlinePassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #888; padding: 0;">
                                <i class="fa fa-eye" id="inlineEyeIcon"></i>
                            </button>
                        </div>
                        <span id="inlineLoginError" class="red-text" style="display: none; font-size: 13px; color: #e53935; margin-top: 6px; display: none;"></span>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-4">
                        <button type="button" id="inlineLoginActionBtn" class="solid-button w-100" style="padding: 12px; font-size: 15px; font-weight: 600; text-align: center; border-radius: 8px;">
                            Log In to Account
                        </button>
                        <button type="button" id="useDifferentEmailModalBtn" class="border-button w-100" data-bs-dismiss="modal" style="padding: 10px; font-size: 14px; text-align: center; border-radius: 8px; margin-top: 8px;">
                            Use Different Email / Phone
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
