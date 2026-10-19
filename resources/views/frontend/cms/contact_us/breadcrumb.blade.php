<div class="bread-crumb-block">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="#" style="pointer-events: none;">{{@Helper::language('contact_us')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    @if(Request::route()->getName()=='headOffice')
                    {{@Helper::language('head_office')}}
                    @elseif (Request::route()->getName()=='orderByPhone')
                    {{@Helper::language('order_by_phone')}}
                    @elseif (Request::route()->getName()=='tradeEnquieries')
                    {{@Helper::language('trade_enquiries')}}
                    @elseif (Request::route()->getName()=='pressEnquieries')
                    {{@Helper::language('press_enquiries')}}
                    @endif    
                </li>
            </ul>
        </nav>
    </div>
</div>