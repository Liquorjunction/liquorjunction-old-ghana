<?php
// Current Full URL
$fullPagePath = Request::url();
// Char Count of Backend folder Plus 1
$envAdminCharCount = strlen(env('WHOLESALER_BACKEND_PATH')) + 1;
// URL after Root Path EX: admin/home
$urlAfterRoot = substr($fullPagePath, strpos($fullPagePath, env('WHOLESALER_BACKEND_PATH')) + $envAdminCharCount);
?>
<style>
    .dker {
        padding-top: 0px !important;
        padding-bottom: 25px !important;
    }
    .navbar .navbar-brand img{
            max-height: 65px !important; 
    }
</style>
<div id="aside" class="app-aside modal fade folded md nav-expand">
    <div class="left navside dark dk" layout="column">
        <div class="navbar navbar-md no-radius">
            <!-- brand -->
            <a class="navbar-brand" href="{{ route('adminwholesalerHome') }}">
                 <img src="{{ asset('assets/dashboard/images/trade25logowhite.png')}}" alt="Control">
            </a>
            <!-- / brand -->
        </div>
        <div flex class="hide-scroll">
            <nav class="scroll nav-active-primary">

                <ul class="nav" ui-nav>

                    <li class="{{ (\Request::route()->getName()== 'adminwholesalerHome' || \Request::route()->getName()== 'dashboardfilter') ? 'active' : ' ' }}">
                        <a href="{{ route('adminwholesalerHome') }}" onclick="location.href='{{ route('adminwholesalerHome') }}'">
                            <span class="nav-icon">
                                <i class="fa fa-th-large material-icons" aria-hidden="true"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.dashboard') }}</span>
                        </a>
                    </li>

                    <li class="{{ (\Request::route()->getName()== 'wholesalerstore' || \Request::route()->getName()== 'store.create' || \Request::route()->getName()== 'wholesalerstore.edit' || \Request::route()->getName()== 'wholesalerstore.show') ? 'active' : ' ' }}">
                        <a href="{{ route('wholesalerstore') }}" onclick="location.href='{{ route('wholesalerstore') }}'">
                            <span class="nav-icon">
                                <i class="fa fa-th-large material-icons" aria-hidden="true"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.storeAddress') }}</span>
                        </a>
                    </li>

                    <li class="{{ (\Request::route()->getName()== 'wholesalerproduct') ? 'active' : ' ' }}">
                        <a href="{{ route('wholesalerproduct') }}" onclick="location.href='{{ route('wholesalerproduct') }}'">
                            <span class="nav-icon">
                                <!-- <i class="fa fa-th-large material-icons" aria-hidden="true"></i> -->
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.6 4.875C10.6 3.83984 11.4062 3 12.4 3H23.2C24.1938 3 25 3.83984 25 4.875V21.125C25 22.1602 24.1938 23 23.2 23H15.2987C15.3663 22.8047 15.4 22.5938 15.4 22.375V12.8945C16.0975 12.6367 16.6 11.9414 16.6 11.125V9.875C16.6 8.83984 15.7938 8 14.8 8H10.6V4.875ZM22.4237 16.5664C22.6562 16.3242 22.6562 15.9258 22.4237 15.6836L20.0238 13.1836C19.7913 12.9414 19.4088 12.9414 19.1763 13.1836L16.7763 15.6836C16.5438 15.9258 16.5438 16.3242 16.7763 16.5664C17.0088 16.8086 17.3913 16.8086 17.6238 16.5664L19 15.1328V19.875C19 20.2188 19.27 20.5 19.6 20.5C19.93 20.5 20.2 20.2188 20.2 19.875V15.1328L21.5763 16.5664C21.8088 16.8086 22.1912 16.8086 22.4237 16.5664ZM1 9.875C1 9.53125 1.27 9.25 1.6 9.25H14.8C15.13 9.25 15.4 9.53125 15.4 9.875V11.125C15.4 11.4688 15.13 11.75 14.8 11.75H1.6C1.27 11.75 1 11.4688 1 11.125V9.875ZM14.2 13V21.75C14.2 22.4414 13.6638 23 13 23H3.4C2.73625 23 2.2 22.4414 2.2 21.75V13H14.2ZM6.4 15.5C6.07 15.5 5.8 15.7812 5.8 16.125C5.8 16.4688 6.07 16.75 6.4 16.75H10C10.33 16.75 10.6 16.4688 10.6 16.125C10.6 15.7812 10.33 15.5 10 15.5H6.4Z" fill="#fff"/>
                                </svg>
                            </span>
                            <span class="nav-text">{{ __('backend.product_management') }}</span>
                        </a>
                    </li>

                    <li class="{{ (\Request::route()->getName()== 'wholesalerpurchase') ? 'active' : ' ' }}">
                        <a href="{{ route('wholesalerpurchase') }}" onclick="location.href='{{ route('wholesalerpurchase') }}'">
                            <span class="nav-icon">
                                <!-- <i class="fa fa-th-large material-icons" aria-hidden="true"></i> -->
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.6 4.875C10.6 3.83984 11.4062 3 12.4 3H23.2C24.1938 3 25 3.83984 25 4.875V21.125C25 22.1602 24.1938 23 23.2 23H15.2987C15.3663 22.8047 15.4 22.5938 15.4 22.375V12.8945C16.0975 12.6367 16.6 11.9414 16.6 11.125V9.875C16.6 8.83984 15.7938 8 14.8 8H10.6V4.875ZM22.4237 16.5664C22.6562 16.3242 22.6562 15.9258 22.4237 15.6836L20.0238 13.1836C19.7913 12.9414 19.4088 12.9414 19.1763 13.1836L16.7763 15.6836C16.5438 15.9258 16.5438 16.3242 16.7763 16.5664C17.0088 16.8086 17.3913 16.8086 17.6238 16.5664L19 15.1328V19.875C19 20.2188 19.27 20.5 19.6 20.5C19.93 20.5 20.2 20.2188 20.2 19.875V15.1328L21.5763 16.5664C21.8088 16.8086 22.1912 16.8086 22.4237 16.5664ZM1 9.875C1 9.53125 1.27 9.25 1.6 9.25H14.8C15.13 9.25 15.4 9.53125 15.4 9.875V11.125C15.4 11.4688 15.13 11.75 14.8 11.75H1.6C1.27 11.75 1 11.4688 1 11.125V9.875ZM14.2 13V21.75C14.2 22.4414 13.6638 23 13 23H3.4C2.73625 23 2.2 22.4414 2.2 21.75V13H14.2ZM6.4 15.5C6.07 15.5 5.8 15.7812 5.8 16.125C5.8 16.4688 6.07 16.75 6.4 16.75H10C10.33 16.75 10.6 16.4688 10.6 16.125C10.6 15.7812 10.33 15.5 10 15.5H6.4Z" fill="#fff"/>
                                </svg>
                            </span>
                            <span class="nav-text">{{ __('backend.PurchaseOrderManagement') }}</span>
                        </a>
                    </li>

                    <li class="{{ (\Request::route()->getName()== 'promoted-product') ? 'active' : ' ' }}">
                        <a href="{{ route('promoted-product') }}" onclick="location.href='{{ route('promoted-product') }}'">
                            <span class="nav-icon">
                                <i class="fa fa-th-large material-icons" aria-hidden="true"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.promoted_product') }}</span>
                        </a>
                    </li>

                    <li class="{{ (\Request::route()->getName()== 'order') ? 'active' : ' ' }}">
                        <a href="{{ route('order') }}" onclick="location.href='{{ route('order') }}'">
                            <span class="nav-icon">
                               <i class="fa fa-th-large material-icons" aria-hidden="true"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.OrderManagement') }}</span>
                        </a>
                    </li>

                   <?php 
                         $currentFolder = "salesreport"; // Put folder name here
                         $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));

                    ?>
                <li class="{{ ( $PathCurrentFolder==$currentFolder) ? 'active' : '' }}">
                        <a>
                            <span class="nav-caret">
                                <i class="fa fa-caret-down"></i>
                            </span>
                            <span class="nav-icon">
                                <i class="fa fa-users material-icons" aria-hidden="true"></i>
                            </span>
                            <span class="nav-text">{{ __('backend.ReportManagement') }}</span>
                        </a>
                        <ul class="nav-sub">
                            
                           
                            <?php
                            $currentFolder = "salesreport"; // Put folder name here
                            $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                            ?>
                            <li class="{{ ($PathCurrentFolder==$currentFolder) ? 'active' : '' }}">
                                <a href="{{ route('salesreport') }}" class="sub-link">
                                    <span class="nav-text">{{ __('backend.SalesReportManagement') }}</span>
                                </a>
                            </li>

                        </ul>
                    </li>


                </ul>
            </nav>
        </div>
    </div>
</div>