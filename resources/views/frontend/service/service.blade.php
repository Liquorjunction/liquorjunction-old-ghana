
 <section class="service">
    <div class="container">
        <div class="row justify-content-center align-items-end">
            <div class="col-lg-6">
                <div class="white-box-wrapper">
                    <div class="white-box">
                        <img src="{{ asset('assets/frontend/images/delivery.svg') }}" alt="24-hours-support" title="24-hours-support" />
                        <h5 class="mb-0">{{@Helper::language('standard_delivery')}}</h5>
                        <p>On all orders over GH₵500</p>
                    </div>

                    {{-- <div class="white-box">
                        <img src="{{ asset('assets/frontend/images/delivery-truck.svg') }}" alt="delivery-truck" title="delivery-truck" />
                        <h5 class="mb-0">{{@Helper::language('free_shipping_over_200_GH₵')}}</h5>
                    </div> --}}

                    <div class="white-box">
                        <img src="{{ asset('assets/frontend/images/guarantee.svg') }}" alt="credit-card" title="credit-card" />
                        <h5 class="mb-0">{{@Helper::language('authenticity_guarantee')}}</h5>
                        <p>Shop for items with confidence</p>
                    </div>

                    <div class="white-box">
                        <img src="{{ asset('assets/frontend/images/distribution.svg') }}" alt="distribution" title="distribution" />
                        <h5 class="mb-0">{{@Helper::language('easy_shopping')}}</h5>
                        <p>No risk online shopping</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
