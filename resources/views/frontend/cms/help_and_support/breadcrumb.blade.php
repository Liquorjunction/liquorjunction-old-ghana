<div class="bread-crumb-block">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="#" style="pointer-events: none;">{{@Helper::language('help_and_support')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    @if(Request::route()->getName()=='trackOrder')
                    {{@Helper::language('track_your_order')}}
                    @elseif (Request::route()->getName()=='faqs')
                    {{@Helper::language('faqs')}}
                    @elseif (Request::route()->getName()=='queries')
                    {{@Helper::language('Queries')}}
                    @endif    
                </li>
            </ul>
        </nav>
    </div>
</div>