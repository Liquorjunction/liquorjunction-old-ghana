<?php
// Current Full URL
$fullPagePath = Request::url();
// Char Count of Backend folder Plus 1
$envAdminCharCount = strlen(env('BACKEND_PATH')) + 1;
// URL after Root Path EX: admin/home
$urlAfterRoot = substr($fullPagePath, strpos($fullPagePath, env('BACKEND_PATH')) + $envAdminCharCount);
?>
<style>
    .dker {
        padding-top: 0px !important;
        padding-bottom: 25px !important;
    }

    .navbar .navbar-brand img {
        max-height: 65px !important;
    }
</style>
<div id="aside" class="app-aside modal fade folded md nav-expand">
    <div class="left navside dark dk" layout="column">
        <div class="navbar navbar-md no-radius">
            <!-- brand -->
            <a class="navbar-brand" href="{{ route('adminHome') }}">
                <img src="{{ asset('assets/dashboard/images/liquor-logo.svg') }}" alt="Control">
            </a>
            <!-- / brand -->
        </div>
        <div flex class="hide-scroll">
            <nav class="scroll nav-active-primary">
                <ul class="nav" ui-nav>
                    @php
                        
                        $dashboard_permission = @Helper::GetRolePermission(Auth::user()->user_type,1,'read');                                                 

                    @endphp
                    @if(isset($dashboard_permission) && $dashboard_permission==true)
                    <li
                        class="{{ \Request::route()->getName() == 'adminHome' || \Request::route()->getName() == 'dashboardfilter' ? 'active' : ' ' }}">
                        <a href="{{ route('adminHome') }}" onclick="location.href='{{ route('adminHome') }}'">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Dashboard.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.dashboard') }}</span>
                        </a>
                    </li>
                    @endif

                    @php
                        $customer_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'read');                                                 
                    @endphp
                    @if(isset($customer_module_permission) && $customer_module_permission==true)
                    <li class="{{ (\Request::route()->getName() == 'customer' || Request::is('admin/customer*') ) ? 'active' : ' ' }}">
                        <a href="{{ route('customer') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Icon_Customer management.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.customer_managment') }}</span>
                        </a>
                    </li>
                    @endif
                
                    @php
                        $country_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,3,'read');                                                 
                    @endphp
                    @if(isset($country_module_permission) && $country_module_permission==true)
                    <li class="{{ \Request::route()->getName() == 'country-admin' ? 'active' : ' ' }}">
                        <a href="{{ route('country-admin') }}" onclick="location.href='{{ route('country-admin') }}'">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Country admin.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.countryAdmin') }}</span>
                        </a>
                    </li>
                    @endif
                    @php
                        $store_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,23,'read');                                                 
                    @endphp
                    @if(isset($store_module_permission) && $store_module_permission==true && Auth::user()->user_type!=1)
                    <li class="{{ (\Request::route()->getName() == 'store' || Request::is('admin/store*') ) ? 'active' : ' ' }}">
                        <a href="{{ route('store') }}" onclick="location.href='{{ route('store') }}'">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/store management.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.store_management') }}</span>
                        </a>
                    </li>
                    @endif
                    @php
                    $subadmin_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,24,'read');     
                    $role_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,29,'read');   
                                                         
                    @endphp
                    @if(($subadmin_module_permission==true || $role_module_permission==true) && Auth::user()->user_type==2)                 

                    <li class="{{ (\Request::route()->getName() == 'subadmin' || \Request::route()->getName() == 'roles.index'  || Request::is('admin/roles*') ) ? 'active' : ' ' }}">
                        <a>
                            <span class="nav-caret"> <i class="fa fa-caret-down"></i></span>
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/access control.svg')}}">
                            </span>
                            <span class="nav-text">Access Control </span>
                        </a>
                        <ul class="nav-sub"> 
                            @if($role_module_permission==true && Auth::user()->user_type==2)    
                            <li class="{{(\Request::route()->getName() == 'roles.index'  || Request::is('admin/roles*') ) ? 'active' : ' ' }}">
                                <a href="{{ route('roles') }}" class="sub-link">
                                    <span class="nav-text">Roles</span>
                                </a>
                            </li> 
                            @endif
                            @if($subadmin_module_permission==true && Auth::user()->user_type==2)   
                            <li class="{{  (\Request::route()->getName() == 'subadmin') ? 'active' : ' ' }}">
                                <a href="{{ route('subadmin') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.subadmin_management') }}</span>
                                </a>
                            </li> 
                            @endif
                        </ul>
                        </li> 
                    @endif
                    @php
                        $banner_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,4,'read');                                                 
                    @endphp
                    @if(isset($banner_module_permission) && $banner_module_permission==true)
                    <li
                    class="{{ in_array(\Request::route()->getName(), ['banner','banner.create','banner.edit','banner.show']) ? 'active' : ' ' }}">
                        <a href="{{ route('banner') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Banner management.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.banner_management') }}</span>
                        </a>
                    </li>
                    @endif
                    @php
                        $brand_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,5,'read');                                                 
                    @endphp
                    @if(isset($brand_module_permission) && $brand_module_permission==true)
                    <li
                        class="{{ in_array(\Request::route()->getName(), ['brand']) ? 'active' : ' ' }}">
                        <a href="{{ route('brand') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Brand.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.brand_managment') }}</span>
                        </a>
                    </li>
                    @endif

                    <?php
                    $category_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,6,'read');
                    $subcategory_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,7,'read');
                   
                    $currentFolder = 'category'; // Put folder name here
                    $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                    
                    $currentFolder1 = 'subcategory'; // Put folder name here
                    $PathCurrentFolder1 = substr($urlAfterRoot, 0, strlen($currentFolder1));
                    if($category_module_permission == true || $subcategory_module_permission == true){
                    ?>
                    <li
                        class="{{in_array(\Request::route()->getName(), ['category', 'category.*', 'subcategory','subcategory.*']) ? 'active' : ' ' }}">
                        <a>  
                            <span class="nav-caret">
                                <i class="fa fa-caret-down"></i>
                            </span>                          
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Category.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.category_managment') }}</span>
                        </a>
                        <ul class="nav-sub">
                            <?php                           
                            if($category_module_permission == true){
                            ?>
                            <li class="{{in_array(\Request::route()->getName(), ['category', 'category.*']) ? 'active' : ' ' }}">
                                <a href="{{ route('category') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.category') }}</span>
                                </a>
                            </li>
                            <?php
                            }
                            $currentFolder = 'subcategory'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            if($subcategory_module_permission == true){
                            ?>
                            <li class="{{in_array(\Request::route()->getName(), ['subcategory','subcategory.*']) ? 'active' : ' ' }}">
                                <a href="{{ route('subcategory') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.Subcategory') }}</span>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                        }
                    ?>
                    @php
                    $product_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,8,'read');                                                 
                    @endphp
                    @if(isset($product_module_permission) && $product_module_permission==true)
                    <li
                        class="{{ in_array(\Request::route()->getName(), [
                            'product',
                            'product.anyData',
                            'product.show',
                            'product.create',
                            'product.edit',
                        ])
                            ? 'active'
                            : ' ' }}">
                        <a href="{{ route('product') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/product-management.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.product_management') }}</span>
                        </a>
                    </li>
                    @endif
                    {{-- Delivery Management --}}
                    {{-- @php
                    $delivery_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,30,'read');                                                 
                    @endphp
                    @if(isset($delivery_module_permission) && $delivery_module_permission==true)
                    <li class="{{ in_array(\Request::route()->getName(), [
                        'delivery'
                        ])
                        ? 'active'
                        : ' ' }}">
                        <a href="{{ route('delivery') }}">
                            <span class="nav-icon"  style="width: 26.01px; height: 26.01px;filter: brightness(0) invert(1);">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Truck.svg')}}">
                            </span>
                            <span class="nav-text">Delivery Management</span>
                        </a>
                    </li>
                    @endif --}}


                    @php
                    $order_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,9,'read');                                                 
                    @endphp
                    @if(isset($order_module_permission) && $order_module_permission==true)
                    <li
                        class="{{in_array(\Request::route()->getName(), ['adminorder', 'adminorder.show']) ? 'active' : ' ' }}">
                        <a href="{{ route('adminorder') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Order management.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.order_management') }}</span>
                        </a>
                    </li>
                    @endif
                    @php
                    $county_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,10,'read');                                                 
                    @endphp
                    @if(isset($county_module_permission) && $county_module_permission==true)
                    <li
                        class="{{ in_array(\Request::route()->getName(), ['country']) ? 'active' : ' ' }}">
                        <a href="{{ route('country') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Country managment.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.country_management') }}</span>
                        </a>
                    </li>
                    @endif
                    @php
                    $region_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,11,'read');                                                 
                    @endphp
                    @if(isset($region_module_permission) && $region_module_permission==true)
                    <li
                        class="{{ in_array(\Request::route()->getName(), ['region']) ? 'active' : ' ' }}">
                        <a href="{{ route('region') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Region.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.region_management') }}</span>
                        </a>
                    </li>
                    @endif
                    @php
                    $area_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,12,'read');                                                 
                    @endphp
                    @if(isset($area_module_permission) && $area_module_permission==true)
                    <li
                        class="{{ in_array(\Request::route()->getName(), ['area']) ? 'active' : ' ' }}">
                        <a href="{{ route('area') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Area.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.area_management') }}</span>
                        </a>
                    </li>
                    @endif


                    {{-- Promotion --}}
                    <?php
                        $offer_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,31,'read');
                        $bogo_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,33,'read'); 
                        $discount_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,32,'read'); 
                        $loyalty_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,34,'read');  
                        $promo_code_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,13,'read');         

                        $currentFolder = 'offer'; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));

                        $currentFolder1 = 'bogo'; // Put folder name here
                        $PathCurrentFolder1 = substr($urlAfterRoot, 0, strlen($currentFolder1));

                        $currentFolder2 = 'discount'; // Put folder name here
                        $PathCurrentFolder2 = substr($urlAfterRoot, 0, strlen($currentFolder2));

                        $currentFolder3 = 'loyalty'; // Put folder name here
                        $PathCurrentFolder3 = substr($urlAfterRoot, 0, strlen($currentFolder3));

                        $currentFolder4 = 'promocode'; // Put folder name here
                        $PathCurrentFolder4 = substr($urlAfterRoot, 0, strlen($currentFolder4));

                        if($offer_module_permission == true || $bogo_module_permission == true ||  $discount_module_permission==true  || $promo_code_module_permission == true){
                    ?>
                    <li
                        class="{{in_array(\Request::route()->getName(), ['offer', 'offer.*', 'bogo','bogo.*','discount','discount.*','loyalty','loyalty.*','promocode','promocode.*']) ? 'active' : ' ' }}">
                        <a>  
                            <span class="nav-caret">
                                <i class="fa fa-caret-down"></i>
                            </span>                          
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Promo code.svg')}}">
                            </span>
                            <span class="nav-text">Promotion</span>
                        </a>
                        <ul class="nav-sub">
                            <?php                           
                            if($offer_module_permission == true){
                            ?>
                            <li class="{{in_array(\Request::route()->getName(), ['offer', 'offer.*']) ? 'active' : ' ' }}">
                                <a href="{{ route('offer') }}" class="sub-link">
                                    <span class="nav-text">Offer Management</span>
                                </a>
                            </li>
                            <?php
                            }
                            $currentFolder = 'bogo'; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            if($bogo_module_permission == true){
                            ?>
                            <li class="{{in_array(\Request::route()->getName(), ['bogo','bogo.*']) ? 'active' : ' ' }}">
                                <a href="{{ route('bogo') }}" class="sub-link">
                                    <span class="nav-text">Bogo Offer</span>
                                </a>
                            </li>
                            <?php 
                            } 

                            $currentFolder1 = 'discount'; // Put folder name here
                            $PathCurrentFolder1 = substr($urlAfterRoot, 0, strlen($currentFolder1));
                            if($discount_module_permission == true){
                            ?>
                            <li class="{{in_array(\Request::route()->getName(), ['discount','discount.*']) ? 'active' : ' ' }}">
                                <a href="{{ route('discount') }}" class="sub-link">
                                    <span class="nav-text">Cart Discount</span>
                                </a>
                            </li>
                            <?php 
                            } 

                            $currentFolder2 = 'loyalty'; // Put folder name here
                            $PathCurrentFolder2 = substr($urlAfterRoot, 0, strlen($currentFolder2));
                            if($loyalty_module_permission == true){
                            ?>
                            <li class="{{in_array(\Request::route()->getName(), ['loyalty','loyalty.*']) ? 'active' : ' ' }}">
                                <a href="{{ route('loyalty') }}" class="sub-link">
                                    <span class="nav-text">Loyalty Points</span>
                                </a>
                            </li>
                            <?php 
                            }

                            $currentFolder3 = 'promocode'; // Put folder name here
                            $PathCurrentFolder3 = substr($urlAfterRoot, 0, strlen($currentFolder3));
                            if($promo_code_module_permission == true){
                            ?>
                            <li class="{{in_array(\Request::route()->getName(), ['promocode','promocode.*']) ? 'active' : ' ' }}">
                                <a href="{{ route('promocode') }}" class="sub-link">
                                    <span class="nav-text">Promo Code</span>
                                </a>
                            </li>
                            <?php 
                            }

                            ?>
                        </ul>
                    </li>
                    <?php
                        }
                    ?>

                    @php
                    $blog_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,14,'read');                                                 
                    @endphp
                    @if(isset($blog_module_permission) && $blog_module_permission==true)
                    <li  class="{{ (\Request::route()->getName() == 'blog.index'  || Request::is('admin/blog*')) ? 'active' : ' ' }}">
                        <a href="{{ route('blog') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/blog.svg')}}">
                            </span>
                            <span class="nav-text">Blog Management</span>
                        </a>
                    </li>
                    @endif
                    @php
                    $report_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,15,'read');        
                    $currentFolder1 = "user-report"; // Put folder name here
                    $PathCurrentFolder1 = substr($urlAfterRoot, 0, strlen($currentFolder1));

                    $currentFolder2 = "stock-report"; // Put folder name here
                    $PathCurrentFolder2 = substr($urlAfterRoot, 0, strlen($currentFolder2));

                    $currentFolder3 = "order-report"; // Put folder name here
                    $PathCurrentFolder3 = substr($urlAfterRoot, 0, strlen($currentFolder3));     
                    
                    $currentFolder4 = "loyalty-report"; // Put folder name here
                    $PathCurrentFolder4 = substr($urlAfterRoot, 0, strlen($currentFolder4));     
                    @endphp
                    @if(isset($report_module_permission) && $report_module_permission==true)
                    <li class="{{ ( $PathCurrentFolder1==$currentFolder1 || $PathCurrentFolder2==$currentFolder2 || $PathCurrentFolder3==$currentFolder3 || $PathCurrentFolder4==$currentFolder4 ) ? 'active' : '' }}">
                        <a>
                            <span class="nav-caret">
                                <i class="fa fa-caret-down"></i>
                            </span>
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Report.svg')}}">
                            </span>
                            <span class="nav-text">{{ __('backend.ReportManagement') }}</span>
                        </a>
                        <ul class="nav-sub">                           
                            <?php
                            $currentFolder = "user-report"; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li class="{{ ($PathCurrentFolder==$currentFolder) ? 'active' : '' }}">
                                <a href="{{ route('userreport') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.UserReport') }}</span>
                                </a>
                            </li>

                            <li class="{{ (\Request::route()->getName() == 'stockreport') ? 'active' : ' ' }}">
                                <a href="{{ route('stockreport') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.StockReport') }}</span>
                                </a>
                            </li> 

                            <li class="{{ (\Request::route()->getName() == 'orderreport') ? 'active' : ' ' }}">
                                <a href="{{ route('orderreport') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.OrderReport') }}</span>
                                </a>
                            </li>

                             <li class="{{ (\Request::route()->getName() == 'loyaltyreport') ? 'active' : ' ' }}">
                                <a href="{{ route('loyaltyreport') }}" class="sub-link">
                                    <span class="nav-text">Loyalty Points Report</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @php
                    $contact_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,16,'read');                                                 
                    @endphp
                    @if(isset($contact_module_permission) && $contact_module_permission==true)
                    <li  class="{{ in_array(\Request::route()->getName(), ['inquiry']) ? 'active' : ' ' }}">
                        <a href="{{ route('inquiry') }}">
                            <span class="nav-icon">
                                <img src="{{ asset('assets/dashboard/images/liquor_icons/Contact us.svg')}}">
                            </span>
                            <span class="nav-text">Contact Us</span>
                        </a>
                    </li>
                    @endif
                    <?php
                    $label_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,17,'read'); 
                    $cms_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,18,'read'); 
                    $faq_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,19,'read'); 
                    $email_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,20,'read'); 
                    $general_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,21,'read'); 
                    $currentFolder2 = "label"; // Put folder name here
                    $PathCurrentFolder2 = substr($urlAfterRoot, 0, strlen($currentFolder2));

                    $currentFolder3 = "cms"; // Put folder name here
                    $PathCurrentFolder3 = substr($urlAfterRoot, 0, strlen($currentFolder3));

                    $currentFolder4 = "emailtemplate"; // Put folder name here
                    $PathCurrentFolder4 = substr($urlAfterRoot, 0, strlen($currentFolder4));

                    $currentFolder5 = "webmaster"; // Put folder name here
                    $PathCurrentFolder5 = substr($urlAfterRoot, 0, strlen($currentFolder5));

                    $currentFolder7 = "faq"; // Put folder name here
                    $PathCurrentFolder7 = substr($urlAfterRoot, 0, strlen($currentFolder7));
                    
                    $currentFolder8 = "inquiry"; // Put folder name here
                    $PathCurrentFolder8 = substr($urlAfterRoot, 0, strlen($currentFolder8));

                    if($label_module_permission==true || $cms_module_permission==true || $faq_module_permission==true || $email_module_permission==true || $general_module_permission==true ){

                ?>
                <li class="{{ (  $PathCurrentFolder2==$currentFolder2 || $PathCurrentFolder3==$currentFolder3 || $PathCurrentFolder4==$currentFolder4 || $PathCurrentFolder5==$currentFolder5 || $PathCurrentFolder7 == $currentFolder7 ) ? 'active' : '' }}">
                    <a>
                        <span class="nav-caret">
                            <i class="fa fa-caret-down"></i>
                        </span>
                        <span class="nav-icon">
                            <img src="{{ asset('assets/dashboard/images/liquor_icons/Website.svg')}}">
                        </span>
                        <span class="nav-text">{{ __('backend.generalSiteSettings') }}</span>
                    </a>
                    <ul class="nav-sub">                       
                        
                        <?php
                        $currentFolder = "label"; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                        if($label_module_permission==true){
                        ?>
                        <li class="{{ ($PathCurrentFolder==$currentFolder) ? 'active' : '' }}">
                            <a href="{{ route('label') }}" class="sub-link">
                                <span class="nav-text">Labels</span>
                            </a>
                        </li>
                        <?php
                        }                        
                        $currentFolder = "cms"; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                        if($cms_module_permission==true){
                        ?>
                        <li class="{{ ($PathCurrentFolder==$currentFolder) ? 'active' : '' }}">
                            <a href="{{ route('cms') }}" class="sub-link">
                                <span class="nav-text">{{ __('backend.cms') }}</span>
                            </a>
                        </li>
                        <?php
                        }                        
                        $currentFolder = "faq"; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                        if($cms_module_permission==true){
                        ?>
                        <li {{ ($PathCurrentFolder==$currentFolder) ? 'class=active' : '' }}>
                            <a href="{{ route('faq') }}" class="sub-link">
                                <span class="nav-text">FAQ's</span>
                            </a>
                        </li> 
                        <?php
                        }
                        $currentFolder = "news_latter"; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                        if($cms_module_permission==true){
                        ?>
                        <li {{ ($PathCurrentFolder==$currentFolder) ? 'class=active' : '' }}>
                            <a href="{{route('news')}}" class="sub-link">
                                <span class="nav-text">NewsLetter</span>
                            </a>
                        </li> 
                        <?php
                        }

                        $currentFolder = "emailtemplate"; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                        if($email_module_permission==true){
                        ?>
                        
                        <li class="{{ ($PathCurrentFolder==$currentFolder) ? 'active' : '' }}">
                            <a href="{{ route('emailtemplate') }}" class="sub-link">
                                <span class="nav-text">{{ __('backend.emailtemplate') }}</span>
                            </a>
                        </li>
                        <?php
                        }
                        $currentFolder = "webmaster"; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                        if($general_module_permission==true){
                        ?>
                        <li class="{{ ($PathCurrentFolder==$currentFolder) ? 'active' : '' }}">
                            <a href="{{ route('webmasterSettings') }}" class="sub-link">
                                <span class="nav-text">{{ __('backend.generalSettings') }}</span>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
