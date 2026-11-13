</main>
<!-- Footer -->
<footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 footer-col">
                    <div class="links-wrapper">
                        <div class="footer-link">
                            <h6 class="mb-12">{{@Helper::language('help_and_support')}}</h6>
                            <ul class="links">
                                <li><a href="{{route('trackOrder')}}">{{@Helper::language('track_your_order')}}</a></li>
                                <li><a href="{{route('faqs')}}">{{@Helper::language('faqs')}}</a></li>
                                <li><a href="{{route('queries')}}">{{@Helper::language('Queries')}}</a></li>
                                <li class="d-md-none"><a href="#"></a></li>
                            </ul>
                        </div>

                        <div class="footer-link">
                            <h6 class="mb-12">{{@Helper::language('our_shops')}}</h6>
                            <ul class="links">
                                <li><a href="{{route('ourStore')}}">{{@Helper::language('great_portland_street_shop')}}</a></li>
                                <li><a href="{{route('ourStore')}}">{{@Helper::language('covent_garden_shop')}}</a></li>
                                <li><a href="{{route('ourStore')}}">{{@Helper::language('london_bridge_shop')}}</a></li>
                                <li><a href="{{route('ourStore')}}">{{@Helper::language('senchi_street')}}</a></li>
                                <li><a href="{{route('ourStore')}}">{{@Helper::language('near_kfc')}}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 footer-col">
                    <div class="links-wrapper">
                        <div class="footer-link">
                            <h6 class="mb-12">{{@Helper::language('delivery_and_returns')}}</h6>
                            <ul class="links">
                                <li><a href="{{route('deliveryInformation')}}">{{@Helper::language('delivery_information')}}</a></li>
                                <li><a href="{{route('returnsCancellation')}}">{{@Helper::language('returns_and_cancellation')}}</a></li>
                                <li><a href="{{route('damagesWrongGoods')}}">{{@Helper::language('damages_and_wrong_goods')}}</a></li>
                                <li><a href="{{route('ourPackaging')}}">{{@Helper::language('our_packaging')}}</a></li>
                            </ul>
                        </div>

                        <div class="footer-link">
                            <h6 class="mb-12">{{@Helper::language('shopping_with_us')}}</h6>
                            <ul class="links">
                                <li><a href="{{route('paymentOption')}}">{{@Helper::language('payment_options')}}</a></li>
                                <li><a href="{{route('placingOrder')}}">{{@Helper::language('placing_your_order')}}</a></li>
                                <li><a href="{{route('securityPrivacy')}}">{{@Helper::language('security_privacy')}}</a></li>
                                <li><a href="{{route('termsCondition')}}">{{@Helper::language('terms_condition')}}</a></li>
                                <li><a href="{{route('frontend.blog')}}">{{@Helper::language('lj_blog')}}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 footer-col">
                    <div class="links-wrapper">
                        <div class="footer-link">
                            <h6 class="mb-12">{{@Helper::language('contact_us')}}</h6>
                            <ul class="links">
                                <li><a href="{{route('headOffice')}}">{{@Helper::language('head_office')}}</a></li>
                                <li><a href="{{route('orderByPhone')}}">{{@Helper::language('order_by_phone')}}</a></li>
                                <li><a href="{{route('tradeEnquieries')}}">{{@Helper::language('trade_enquiries')}}</a></li>
                                <li><a href="{{route('pressEnquieries')}}">{{@Helper::language('press_enquiries')}}</a></li>
                            </ul>
                        </div>

                        <div class="footer-link">
                            <h6 class="mb-12">{{@Helper::language('about_us')}}</h6>
                            <ul class="links">
                                <li><a href="{{route('ourCompany')}}">{{@Helper::language('our_company')}}</a></li>
                                <li><a href="{{route('ourHistory')}}">{{@Helper::language('our_history')}}</a></li>
                                <li><a href="{{route('responsibleDrinking')}}">{{@Helper::language('responsible_drinking')}}</a></li>
                                <li><a href="{{route('ourStore')}}">{{@Helper::language('shops')}}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                @php
                    $url = \DB::table('settings')->first();
                @endphp
                <div class="col-lg-3 footer-col">
                    <div class="links-wrapper">
                        <div class="footer-link">
                            <h6 class="mb-16">{{@Helper::language('social_handels')}}</h6>
                            <ul class="social-media">
                                @if($url->facebook_link)
                                <li>
                                    <a href="{{$url->facebook_link}}" target="_blank">
                                        <img src="{{ asset('assets/frontend/images/icon_facebook.svg')}}" alt="Facebook" class="hoveroff" title="Facebook" />
                                        <img src="{{ asset('assets/frontend/images/icon_facebook_white.svg')}}" alt="Facebook" class="hoveron" title="Facebook" />
                                    </a>
                                </li>
                                @endif
                                {{-- @if($url->twitter_link)
                                <li>
                                    <a href="{{$url->twitter_link}}" target="_blank">
                                        <img src="{{ asset('assets/frontend/images/icon_linkedin.svg')}}" alt="Linkedin" class="hoveroff" title="Linkedin"/>
                                        <img src="{{ asset('assets/frontend/images/icon_linkedin_white.svg')}}" alt="Linkedin" class="hoveron" title="Linkedin" />
                                    </a>
                                </li>
                                @endif --}}
                                @if($url->instagram_link)
                                <li>
                                    <a href="{{$url->instagram_link}}" target="_blank">
                                        <img src="{{ asset('assets/frontend/images/icon_instagram.svg')}}" alt="Instagram" class="hoveroff" title="Instagram"/>
                                        <img src="{{ asset('assets/frontend/images/icon_instagram_white.svg')}}" alt="Instagram" class="hoveron" title="Instagram" />
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>

                        
                        <div class="footer-link">
                            <h6 class="mb-12">{{ 'Android' }}</h6>
                            <a href="https://play.google.com/store/apps/details?id=com.liquor.junction" target="_blank"><img src="{{ asset('assets/frontend/images/ios-app.png')}}" style="height: 60px;" alt="icon_payment" title="Android App QR" /></a> 
                            <br>
                            <h6 class="mb-12">{{ 'Ios' }}</h6>
                            <a href="https://apps.apple.com/in/app/liquor-junction-ghana-app/id6547853117" target="_blank"><img src="{{ asset('assets/frontend/images/android-app.png')}}" style="height: 60px;" alt="icon_payment" title="IOS App QR" /></a>
                        </div>
                        <div class="footer-link">
                            <h6 class="mb-12">{{@Helper::language('we_accept')}}</h6>
                            <img src="{{ asset('assets/frontend/images/iconimage1.png')}}" alt="icon_payment" title="icon_payment" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <p>© {{date('Y')}} liquorjunction. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <a href="#" id="c-go-top" class="c-go-top">
        <span></span>
    </a>
    <!-- Floating Cart Icon -->
    @include('frontend.partials.floating-cart')
    <!-- End Footer -->



@push('after-scripts')
<script src="{{URL::asset('assets/frontend/js/webscripts/search.js')}}"></script>
<script>
// function isCartEmpty(){
//         Swal.fire({
//         title: "Cart",
//         text: "Your cart is empty.",
//     });
// }



</script>
<script type="text/javascript">
    // Restrict user input in a text field
    // create as many regular expressions here as you need:

    function restrictInput(myfield, e, restriction, checkdot) {
        var digitsOnly = /[1234567890]/g;
        var integerOnly = /^[0-9\.]$/g;
        // var integerOnly = /^\d{0,15}(\.\d{1,4})?$/g;
        var alphaOnly = /[A-Za-z]/g;
        var usernameOnly = /[0-9A-Za-z\._-]/g;
        var latLong = /^[0-9\.]+$/g;

        if (restriction == 'digits') {
            restrictionType = digitsOnly;
        }

        if (restriction == 'latLong') {
            restrictionType = latLong;
        }

        if (restriction == 'integer') {
            restrictionType = integerOnly;
        }

        if (restriction == 'alpha') {
            restrictionType = alphaOnly;
        }

        if (restriction == 'username') {
            restrictionType = usernameOnly;
        }

        if (!e) var e = window.event
        if (e.keyCode) code = e.keyCode;
        else if (e.which) code = e.which;
        var character = String.fromCharCode(code);

        // if user pressed esc... remove focus from field...
        if (code == 27) {
            this.blur();
            return false;
        }
        // ignore if the user presses other keys
        // strange because code: 39 is the down key AND ' key...
        // and DEL also equals .
        if (!e.ctrlKey && code != 9 && code != 8 && code != 36 && code != 37 && code != 38 && (code != 39 || (code ==
                39 && character == "'")) && code != 40) {
            if (character.match(restrictionType)) {
                if (checkdot == "checkdot") {
                    return !isNaN(myfield.value.toString() + character);
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
    }

    function onlyString(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

            return false;
        return true;

    }

    function isNumberKey(txt, evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 46) {
            //Check if the text already contains the . character
            if (txt.value.indexOf('.') === -1) {
                return true;
            } else {
                return false;
            }
        } else {
            if (charCode > 31 &&
                (charCode < 48 || charCode > 57))
                return false;
        }
        return true;
    }

    $(".decimal").on("input", function(evt) {
        var self = $(this);
        self.val(self.val().replace(/[^0-9\.]/g, ''));

        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
            evt.preventDefault();
        }
    });

    //jq loader

    // Sitewide Input Restrictions & Phone Code Formatter
    $(document).ready(function() {
        // 1. Phone Code Dropdown: strip "(GH)" etc. so only "+233" is displayed
        function cleanSitewidePhoneCodeOptions() {
            $('select[name="phonecode"], select[name="phone_code"], select[name="bill_phonecode"], #modal_phonecode, select.numbers').find('option').each(function() {
                var t = $(this).text().trim();
                var m = t.match(/\+?\d+/);
                if (m) {
                    var cleanCode = '+' + m[0].replace('+', '');
                    if (t !== cleanCode) {
                        $(this).text(cleanCode);
                    }
                }
            });
        }
        cleanSitewidePhoneCodeOptions();
        $(document).ajaxComplete(function() {
            cleanSitewidePhoneCodeOptions();
        });

        // 2. Name inputs: only letters (A-Z, a-z) and spaces allowed
        $(document).on('keypress', '#modal_name, input[name="name"], input[name="bill_name"], #name, #bill_name', function(e) {
            if (e.which === 0 || e.which === 8 || e.which === 9 || e.which === 13 || e.which === 27) return true;
            var char = String.fromCharCode(e.which);
            if (!/^[a-zA-Z\s]$/.test(char)) {
                e.preventDefault();
                return false;
            }
        });
        $(document).on('input paste', '#modal_name, input[name="name"], input[name="bill_name"], #name, #bill_name', function() {
            var $el = $(this);
            setTimeout(function() {
                var val = $el.val();
                var clean = val.replace(/[^a-zA-Z\s]/g, '');
                if (val !== clean) {
                    $el.val(clean);
                }
            }, 0);
        });

        // 3. Mobile / Phone inputs: only digits (0-9) allowed
        $(document).on('keypress', '#modal_phone, input[type="tel"], input[name="phone"], input[name="bill_phone"], input[name="phone_number"], input[name="mobile"], #phone, #bill_phone, .phone-number input', function(e) {
            if (e.which === 0 || e.which === 8 || e.which === 9 || e.which === 13 || e.which === 27) return true;
            var char = String.fromCharCode(e.which);
            if (!/^[0-9]$/.test(char)) {
                e.preventDefault();
                return false;
            }
        });
        $(document).on('input paste', '#modal_phone, input[type="tel"], input[name="phone"], input[name="bill_phone"], input[name="phone_number"], input[name="mobile"], #phone, #bill_phone, .phone-number input', function() {
            var $el = $(this);
            setTimeout(function() {
                var val = $el.val();
                var clean = val.replace(/[^0-9]/g, '');
                if (val !== clean) {
                    $el.val(clean);
                }
            }, 0);
        });

        // 4. Zip code inputs: only uppercase characters (A-Z, 0-9) allowed
        $(document).on('keypress', '#modal_zip_code, input[name="zip_code"], input[name="bill_zip_code"], #zip_code, #bill_zip_code', function(e) {
            if (e.which === 0 || e.which === 8 || e.which === 9 || e.which === 13 || e.which === 27) return true;
            var char = String.fromCharCode(e.which);
            if (!/^[a-zA-Z0-9]$/.test(char)) {
                e.preventDefault();
                return false;
            }
        });
        $(document).on('input paste', '#modal_zip_code, input[name="zip_code"], input[name="bill_zip_code"], #zip_code, #bill_zip_code', function() {
            var $el = $(this);
            setTimeout(function() {
                var val = $el.val();
                var clean = val.toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (val !== clean) {
                    $el.val(clean);
                }
            }, 0);
        });
    });

    // Global Favorite Toggle without page refresh
    function toggleProductFav(product_id, status, targetEl) {
        var user_id = $("#user_id").val();
        if (!user_id) {
            window.location.href = "{{ route('websitelogin') }}";
            return false;
        }

        var $target = targetEl ? $(targetEl) : null;
        if ($target && $target.is('label') && $target.attr('for')) {
            $target = $('#' + $target.attr('for'));
        }
        if (!$target || !$target.length) {
            if (window.event && window.event.target) {
                var $evtTarget = $(window.event.target);
                if ($evtTarget.is('label') && $evtTarget.attr('for')) {
                    $target = $('#' + $evtTarget.attr('for'));
                } else if ($evtTarget.is('input[type="checkbox"]')) {
                    $target = $evtTarget;
                }
            }
        }

        var $allCheckboxes = $('input[type="checkbox"]').filter(function() {
            var oc = $(this).attr('onclick') || '';
            return oc.indexOf('productFav(' + product_id + ',') !== -1 ||
                   oc.indexOf('productFavTest(' + product_id + ',') !== -1 ||
                   oc.indexOf('productFav(' + product_id + ')') !== -1;
        });
        if ($target && $target.length) {
            $allCheckboxes = $allCheckboxes.add($target);
        }

        var sendStatus = status;
        if ($target && $target.length) {
            if ($target.data('fav-status') !== undefined) {
                sendStatus = $target.data('fav-status');
            } else if ($target.attr('value') !== undefined) {
                sendStatus = $target.val();
            }
        }

        $.ajax({
            url: "{{ route('productfav') }}",
            type: "POST",
            data: {
                'user_id': user_id,
                'product_id': product_id,
                'status': sendStatus
            },
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $(".loader").fadeIn();
                $('.loader').css("visibility", "visible");
            },
            success: function(response) {
                $(".loader").fadeOut();
                $('.loader').css("visibility", "hidden");

                if (response && response.success) {
                    var newStatus = (response.status !== undefined) ? response.status : (sendStatus == 0 ? 1 : 0);
                    var count = response.count;

                    // 1. Update Header Counter
                    var $favBadge = $('.fav_list .favorite-no');
                    if ($favBadge.length) {
                        $favBadge.text(count);
                        if (count > 0) {
                            $favBadge.show();
                        } else {
                            $favBadge.hide();
                        }
                    }

                    // 2. Update Header Heart Icon
                    if (count > 0) {
                        $('#icon_wishlist .wishlist-filled').show();
                        $('#icon_wishlist .wishlist-outlined').hide();
                    } else {
                        $('#icon_wishlist .wishlist-filled').hide();
                        $('#icon_wishlist .wishlist-outlined').show();
                    }

                    // 3. Update Checkboxes for this product
                    $allCheckboxes.each(function() {
                        var $cb = $(this);
                        $cb.prop('checked', newStatus == 1);
                        $cb.val(newStatus);
                        $cb.data('fav-status', newStatus);
                        var oc = $cb.attr('onclick') || '';
                        if (oc.indexOf('productFavTest') !== -1) {
                            $cb.attr('onclick', 'return productFavTest(' + product_id + ',' + newStatus + ')');
                        } else {
                            $cb.attr('onclick', 'return productFav(' + product_id + ',' + newStatus + ')');
                        }
                    });
                }
            },
            error: function() {
                $(".loader").fadeOut();
                $('.loader').css("visibility", "hidden");
            }
        });
        return false;
    }
    window.toggleProductFav = toggleProductFav;
    window.productFav = toggleProductFav;
    window.productFavTest = toggleProductFav;

</script>
@endpush