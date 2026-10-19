<div class="bread-crumb-block">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
                <li class="breadcrumb-item"><a href="#" style="pointer-events: none;">{{@Helper::language('delivery_and_returns')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    @if(Request::route()->getName()=='deliveryInformation')
                    {{@Helper::language('delivery_information')}}
                    @elseif (Request::route()->getName()=='returnsCancellation')
                    {{@Helper::language('returns_and_cancellation')}}
                    @elseif (Request::route()->getName()=='damagesWrongGoods')
                    {{@Helper::language('damages_and_wrong_goods')}}
                    @elseif (Request::route()->getName()=='ourPackaging')
                    {{@Helper::language('our_packaging')}}
                    @endif 
                </li>
            </ul>
        </nav>
    </div>
</div>