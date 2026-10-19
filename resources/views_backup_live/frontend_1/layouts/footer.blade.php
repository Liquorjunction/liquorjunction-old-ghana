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

</script>
@endpush