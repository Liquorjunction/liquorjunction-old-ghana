@php
    $reCaptchaService = app(\App\Services\ReCaptchaService::class);
@endphp

@if($reCaptchaService->isEnabled())
    @php
        $siteKey = $reCaptchaService->getSiteKey();
    @endphp
    <!-- Google reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}"></script>
    <style>
        /* Ultra-compact Google reCAPTCHA v3 icon on bottom-left */
        .grecaptcha-badge,
        .grecaptcha-badge:hover,
        .grecaptcha-badge:focus {
            width: 70px !important;
            height: 60px !important;
            overflow: hidden !important;
            left: 10px !important;
            right: auto !important;
            bottom: 10px !important;
            z-index: 99999 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
            border-radius: 8px !important;
            transform: scale(0.65) !important;
            transform-origin: bottom left !important;
            opacity: 0.88 !important;
            transition: opacity 0.2s ease !important;
        }

        .grecaptcha-badge:hover {
            opacity: 1 !important;
        }

        /* Mobile screens: even smaller mini-icon (~38px) */
        @media (max-width: 768px) {
            .grecaptcha-badge,
            .grecaptcha-badge:hover,
            .grecaptcha-badge:focus {
                left: 6px !important;
                bottom: 6px !important;
                transform: scale(0.55) !important;
                transform-origin: bottom left !important;
            }
        }
    </style>
    <script type="text/javascript">
        (function() {
            var reCaptchaSiteKey = "{{ $siteKey }}";

            /**
             * Central helper to fetch a fresh Google reCAPTCHA v3 token.
             *
             * @param {string} action Action name (e.g. 'register', 'login', 'contact_us')
             * @param {function} callback Receives the token string
             */
            window.getReCaptchaToken = function(action, callback) {
                var finished = false;
                var safeCallback = function(token) {
                    if (!finished) {
                        finished = true;
                        if (typeof callback === 'function') callback(token || '');
                    }
                };

                // Safety timeout: Never hang a user's form submission if Google network is slow or blocked
                var timeoutTimer = setTimeout(function() {
                    safeCallback('');
                }, 4000);

                if (typeof grecaptcha === 'undefined') {
                    clearTimeout(timeoutTimer);
                    console.warn('reCAPTCHA script is not loaded.');
                    safeCallback('');
                    return;
                }

                try {
                    grecaptcha.ready(function() {
                        grecaptcha.execute(reCaptchaSiteKey, { action: action || 'submit' })
                            .then(function(token) {
                                clearTimeout(timeoutTimer);
                                safeCallback(token);
                            })
                            .catch(function(err) {
                                console.error('reCAPTCHA execution error:', err);
                                clearTimeout(timeoutTimer);
                                safeCallback('');
                            });
                    });
                } catch (e) {
                    clearTimeout(timeoutTimer);
                    safeCallback('');
                }
            };

            /**
             * Helper to fetch token and automatically inject it into a form before proceeding.
             *
             * @param {string} action Action name
             * @param {HTMLElement|jQuery|string} form Form selector, DOM element, or jQuery element
             * @param {function} callback Called with token once injected
             */
            window.withReCaptcha = function(action, form, callback) {
                var formEl = null;
                if (typeof form === 'string') {
                    formEl = document.querySelector(form);
                } else if (form && form.jquery) {
                    formEl = form[0];
                } else if (form && (form instanceof HTMLElement || form.nodeType === 1)) {
                    formEl = form;
                }

                window.getReCaptchaToken(action, function(token) {
                    if (formEl) {
                        var input = formEl.querySelector('input[name="g-recaptcha-response"]');
                        if (!input) {
                            input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'g-recaptcha-response';
                            formEl.appendChild(input);
                        }
                        input.value = token;
                    }
                    if (typeof callback === 'function') {
                        callback(token);
                    }
                });
            };

            // Global handler for standard form submissions with [data-recaptcha-action]
            document.addEventListener('submit', function(e) {
                var form = e.target;
                if (!form || !form.matches || !form.matches('form[data-recaptcha-action]')) {
                    return;
                }

                if (form.getAttribute('data-recaptcha-submitting') === 'true') {
                    return;
                }

                e.preventDefault();
                var action = form.getAttribute('data-recaptcha-action') || 'submit';

                window.withReCaptcha(action, form, function() {
                    form.setAttribute('data-recaptcha-submitting', 'true');
                    form.submit();
                });
            });
        })();
    </script>
@else
    <script type="text/javascript">
        // Fallback dummy functions when reCAPTCHA is disabled in Admin
        window.getReCaptchaToken = function(action, callback) {
            if (typeof callback === 'function') callback('');
        };
        window.withReCaptcha = function(action, form, callback) {
            if (typeof callback === 'function') callback('');
        };
    </script>
@endif
