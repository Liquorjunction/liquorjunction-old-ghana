<ul class="contact-sidebar">
    <li class="sidebar-item">
        <a href="{{route('deliveryInformation')}}" class="sidebar-link {{ in_array(\Request::route()->getName(), ['deliveryInformation']) ? 'active' : ' ' }}">{{@Helper::language('delivery_information')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('returnsCancellation')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['returnsCancellation']) ? 'active' : ' ' }}">{{@Helper::language('returns_and_cancellation')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('damagesWrongGoods')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['damagesWrongGoods']) ? 'active' : ' ' }}">{{@Helper::language('damages_and_wrong_goods')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('ourPackaging')}}"  class="sidebar-link {{ in_array(\Request::route()->getName(), ['ourPackaging']) ? 'active' : ' ' }}">{{@Helper::language('our_packaging')}}</a>
    </li>
</ul>