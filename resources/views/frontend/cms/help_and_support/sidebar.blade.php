<ul class="contact-sidebar">
    <li class="sidebar-item">
        <a href="{{route('trackOrder')}}" class="sidebar-link {{ in_array(\Request::route()->getName(), ['trackOrder']) ? 'active' : ' ' }}">{{@Helper::language('track_your_order')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('faqs')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['faqs']) ? 'active' : ' ' }}">{{@Helper::language('faqs')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('queries')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['queries']) ? 'active' : ' ' }}">{{@Helper::language('Queries')}}</a>
    </li>
   
</ul>