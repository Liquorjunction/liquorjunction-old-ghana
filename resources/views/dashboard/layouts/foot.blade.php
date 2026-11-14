
@stack('before-scripts')
<!-- jQuery -->
<script src="{{ asset('assets/dashboard/js/jquery/dist/jquery.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('assets/dashboard/js/tether/dist/js/tether.min.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/bootstrap/dist/js/bootstrap.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/moment/moment.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/moment/moment.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/moment/locale/en.js') }}" defer></script>
<!-- core -->
<script src="{{ asset('assets/dashboard/js/underscore/underscore-min.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/jQuery-Storage-API/jquery.storageapi.min.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/pace/pace.min.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/config.lazyload.js') }}" defer></script>

<script src="{{ asset('assets/dashboard/js/scripts/palette.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-load.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-jp.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-include.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-device.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-form.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-nav.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-screenfull.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-scroll-to.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-toggle-class.js') }}" defer></script>


<script src="{{ asset('assets/dashboard/js/scripts/app.js') }}" defer></script>


@stack('after-scripts')
<script src="{{ asset('assets/dashboard/js/datatable-state-manager.js') }}"></script>
<script type="text/javascript">
    (function() {
        // Helper: Check if element is a phone/mobile input
        function isPhoneField(el) {
            if (!el || el.tagName !== 'INPUT') return false;
            var t = (el.type || '').toLowerCase();
            if (t === 'file' || t === 'checkbox' || t === 'radio' || t === 'hidden' || t === 'submit' || t === 'button' || t === 'reset') return false;
            var n = (el.name || '').toLowerCase();
            var i = (el.id || '').toLowerCase();
            var c = (el.className || '').toLowerCase();
            return t === 'tel' ||
                   n === 'phone' || n === 'mobile' || n === 'contact_number' || n === 'phone_number' ||
                   n.indexOf('phone') !== -1 || n.indexOf('mobile') !== -1 || n.indexOf('contact') !== -1 ||
                   i === 'phone' || i === 'mobile' || i === 'contact_number' || i === 'phone_number' ||
                   i.indexOf('phone') !== -1 || i.indexOf('mobile') !== -1 || i.indexOf('contact') !== -1 ||
                   c.indexOf('phone') !== -1 || c.indexOf('mobile') !== -1;
        }

        // 1. Strict Keystroke Blocker (KeyDown): Blocks letters, symbols, punctuation before they enter the input
        document.addEventListener('keydown', function(e) {
            if (!isPhoneField(e.target)) return;
            // Allow: Backspace (8), Tab (9), Enter (13), Escape (27), Delete (46)
            if ([8, 9, 13, 27, 46].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl/Cmd + A, C, V, X, Z
                ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88, 90].indexOf(e.keyCode) !== -1) ||
                // Allow: Home, End, Left, Up, Right, Down (35-40)
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                return;
            }
            // Block shift + numbers (which produce !@#$%^&*())
            if (e.shiftKey) {
                e.preventDefault();
                return false;
            }
            // Check if key is digit (top row 48-57, numpad 96-105)
            var isDigit = (e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105);
            if (!isDigit) {
                e.preventDefault();
                return false;
            }
            // Enforce max 15 digits
            var selLen = (e.target.selectionEnd || 0) - (e.target.selectionStart || 0);
            if (e.target.value.length >= 15 && selLen === 0) {
                e.preventDefault();
                return false;
            }
        }, true);

        // 2. Strict KeyPress blocker (fallback for browsers/devices)
        document.addEventListener('keypress', function(e) {
            if (!isPhoneField(e.target)) return;
            var code = e.which || e.keyCode;
            if (code === 0 || code === 8 || code === 9 || code === 13 || code === 27) return;
            var char = String.fromCharCode(code);
            if (!/^[0-9]$/.test(char)) {
                e.preventDefault();
                return false;
            }
        }, true);

        // 3. BeforeInput (modern mobile/desktop browsers): blocks any non-digit input before insertion
        document.addEventListener('beforeinput', function(e) {
            if (!isPhoneField(e.target)) return;
            if (e.data && /[^0-9]/.test(e.data)) {
                e.preventDefault();
                return false;
            }
        }, true);

        // 4. Paste handler: Strips everything except 0-9 upon pasting
        document.addEventListener('paste', function(e) {
            if (!isPhoneField(e.target)) return;
            e.preventDefault();
            var text = (e.clipboardData || window.clipboardData).getData('text') || '';
            var digits = text.replace(/[^0-9]/g, '');
            var el = e.target;
            var start = el.selectionStart || 0;
            var end = el.selectionEnd || 0;
            var cur = el.value || '';
            var newVal = (cur.slice(0, start) + digits + cur.slice(end)).replace(/[^0-9]/g, '');
            if (newVal.length > 15) newVal = newVal.slice(0, 15);
            el.value = newVal;
            var pos = Math.min(start + digits.length, newVal.length);
            el.setSelectionRange(pos, pos);
            if (window.jQuery) { $(el).trigger('change'); }
        }, true);

        // 5. Input sanitizer (failsafe for autofill, virtual keyboards, drag/drop)
        document.addEventListener('input', function(e) {
            if (!isPhoneField(e.target)) return;
            var val = e.target.value;
            var clean = val.replace(/[^0-9]/g, '');
            if (clean.length > 15) clean = clean.slice(0, 15);
            if (val !== clean) {
                e.target.value = clean;
            }
        }, true);

        // 6. Clean Country Code Dropdown (e.g. "+233 (GH)" -> "+233")
        function cleanAdminPhoneCodeOptions() {
            var selects = document.querySelectorAll('select[name="phonecode"], select[name="phone_code"], select#phone_code, select.phone-code-select');
            for (var s = 0; s < selects.length; s++) {
                var opts = selects[s].options;
                for (var o = 0; o < opts.length; o++) {
                    var t = opts[o].text.trim();
                    var m = t.match(/\+?\d+/);
                    if (m) {
                        var cleanCode = '+' + m[0].replace('+', '');
                        if (t !== cleanCode) {
                            opts[o].text = cleanCode;
                        }
                    }
                }
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', cleanAdminPhoneCodeOptions);
        } else {
            cleanAdminPhoneCodeOptions();
        }

        if (window.jQuery) {
            $(document).ajaxComplete(function() {
                cleanAdminPhoneCodeOptions();
            });
        }
    })();
</script>
