<div class="bread-crumb-block">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
                <li class="breadcrumb-item"><a href="#" style="pointer-events: none;">{{@Helper::language('shopping_with_us')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    @if(Request::route()->getName()=='paymentOption')
                    {{@Helper::language('payment_options')}}
                    @elseif (Request::route()->getName()=='placingOrder')
                    {{@Helper::language('placing_your_order')}}
                    @elseif (Request::route()->getName()=='termsCondition')
                   {{@Helper::language('terms_condition')}}
                    @elseif (Request::route()->getName()=='securityPrivacy')
                    {{@Helper::language('security_privacy')}}
                    @endif 
                </li>
            </ul>
        </nav>
    </div>
</div>