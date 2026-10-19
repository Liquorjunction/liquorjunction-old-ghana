<ul class="contact-sidebar">
    <li class="sidebar-item">
        <a href="{{route('headOffice')}}" class="sidebar-link {{ in_array(\Request::route()->getName(), ['headOffice']) ? 'active' : ' ' }}">{{@Helper::language('head_office')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('orderByPhone')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['orderByPhone']) ? 'active' : ' ' }}">{{@Helper::language('order_by_phone')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('tradeEnquieries')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['tradeEnquieries']) ? 'active' : ' ' }}">{{@Helper::language('trade_enquiries')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('pressEnquieries')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['pressEnquieries']) ? 'active' : ' ' }}">{{@Helper::language('press_enquiries')}}</a>
    </li>
</ul>