<ul class="contact-sidebar">
    <li class="sidebar-item">
        <a href="{{route('paymentOption')}}" class="sidebar-link {{ in_array(\Request::route()->getName(), ['paymentOption']) ? 'active' : ' ' }}">{{@Helper::language('payment_options')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('placingOrder')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['placingOrder']) ? 'active' : ' ' }}">{{@Helper::language('placing_your_order')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('securityPrivacy')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['securityPrivacy']) ? 'active' : ' ' }}">{{@Helper::language('security_privacy')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('termsCondition')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['termsCondition']) ? 'active' : ' ' }}">{{@Helper::language('terms_condition')}}</a>
    </li>
</ul>