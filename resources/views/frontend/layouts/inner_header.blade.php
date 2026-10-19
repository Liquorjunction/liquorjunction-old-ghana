<body>
    <?php $menu_link = basename($_SERVER['PHP_SELF']);?>
    <?php 
    $user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
     $cartData = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->leftJoin('main_users','main_users.id','=','product.supplier_id')->where('cart.status',1)->select('cart.*','product.product_image','product.uniqid as product_unique_id','product.product_name','main_users.store_name')->where('cart.user_id',$user_id)->where('cart.status',1)->get();
     $cartItemCount = $cartData->count();
     // echo "<pre>";print_r($cartItemCount);exit();
     $serviceData = DB::table('categories')->leftjoin('font_icon','font_icon.id','=','categories.icon_id')->where('categories.status',1)->select('categories.*','font_icon.name as font_icon_name')->get();

     $notificationListCount = 0;
     if ($user_id) {
          $notificationList = DB::table('notification')->where('sender_id',$user_id)->orderby('id','DESC')->limit(10)->get();
         $notificationListCount = DB::table('notification')->where('sender_id',$user_id)->where('is_read',0)->count();
     }
     // echo "<pre>";print_r($serviceData);exit();
    ?>
    <style type="text/css">
    /*.active{
        background-color: #cf6d6d !important;
    }*/
</style>
<header id="header" class="site-header">
    <input type="hidden" name="user_id" id="user_id" value="{{@$user_id}}">
    <div class="header-top">
        <div class="container">
            <div class="desktop-header">
                <div class="header-main">
                    <div class="row align-items-center">
                        <div class="col-xl-1 col-md-2">
                            <div class="header-logo">
                                <a href="{{route('frontend.home')}}">
                                   
                                        <!-- <img src="{{ asset('assets/frontend/images/Trade 25 logo-grey.svg') }}"> -->
                                        <svg id="Layer_2" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 144.12 144.12">
                                        <defs>
                                            <style>
                                            .cls-1 {
                                                fill: #212b46;
                                            }

                                            .cls-2 {
                                                fill: url(#linear-gradient);
                                            }
                                            </style>
                                            <linearGradient id="linear-gradient" x1="9.6" y1="78.13" x2="49.56" y2="37.85" gradientUnits="userSpaceOnUse">
                                            <stop offset="0" stop-color="#ffdc3c"/>
                                            <stop offset=".28" stop-color="#ffd339"/>
                                            <stop offset=".47" stop-color="#ffce37"/>
                                            <stop offset=".69" stop-color="#ffc033"/>
                                            <stop offset=".75" stop-color="#ffbb32"/>
                                            <stop offset="1" stop-color="#ffab2e"/>
                                            </linearGradient>

                                            <linearGradient id="landing-linear-gradient" x1="9.6" y1="78.13" x2="49.56" y2="37.85" gradientUnits="userSpaceOnUse">
                                            <stop offset="0" stop-color="#ffdc3c"/>
                                            <stop offset=".28" stop-color="#ffd339"/>
                                            <stop offset=".47" stop-color="#ffce37"/>
                                            <stop offset=".69" stop-color="#ffc033"/>
                                            <stop offset=".75" stop-color="#ffbb32"/>
                                            <stop offset="1" stop-color="#ffab2e"/>
                                            </linearGradient>
                                        </defs>
                                        <g id="Layer_1-2" data-name="Layer 1">
                                            <g>
                                            <path class="cls-1" d="m139.18,0H4.94C2.22,0,0,2.22,0,4.94v134.24c0,2.72,2.22,4.94,4.94,4.94h134.24c2.73,0,4.94-2.22,4.94-4.94V4.94c0-2.72-2.22-4.94-4.94-4.94Zm1.94,139.19c0,1.07-.87,1.94-1.94,1.94H4.94c-1.07,0-1.94-.87-1.94-1.94V4.95c0-1.07.87-1.94,1.94-1.94h0s134.24-.01,134.24-.01c1.07,0,1.94.87,1.94,1.94v134.25Z"/>
                                            <path class="cls-2" d="m10.04,36.05v12.98h14.76v40.48h14.82v-40.48h4.34l1.64-2.44c4.15-5.37,8.27-8.96,14.18-10.54H10.04Z"/>
                                            <g>
                                                <path class="cls-1" d="m45.28,78.36l20.01-15.28c4.97-3.89,6.88-6.42,6.88-9.62s-2.37-5.42-5.96-5.42-6.42,2.06-10.62,6.87l-11.71-5.78,1.7-2.54c5.73-7.41,11.38-11.46,21.77-11.46,11.68,0,19.7,6.95,19.7,17.03v.15c0,8.55-4.43,12.98-12.37,18.71l-9.17,6.42h22.07v12.07h-42.31v-11.15h.01Zm45.07,3.74l8.48-9.93c4.35,3.67,8.48,5.8,12.98,5.8,4.89,0,7.87-2.44,7.87-6.26v-.15c0-3.82-3.21-6.19-7.87-6.19-3.28,0-6.03,1.15-8.55,2.67l-8.78-4.89,1.53-27.11h35.74v12.22h-24.06l-.46,8.1c2.44-1.15,4.96-1.91,8.55-1.91,9.62,0,18.33,5.35,18.33,16.96v.15c0,11.91-9.09,19.02-22.07,19.02-9.47,0-16.11-3.28-21.69-8.48"/>
                                                <path class="cls-1" d="m17.4,109.16c-.15.3-.37.55-.66.76-.29.2-.63.35-1.03.45-.4.1-.85.15-1.34.15h-4.15v-8.82h4.05c.9,0,1.6.21,2.12.62.51.41.77.97.77,1.66v.03c0,.25-.03.47-.09.67-.06.19-.14.37-.25.52-.1.16-.22.29-.36.41-.14.12-.28.22-.44.3.5.19.9.46,1.19.79.29.33.44.79.44,1.38v.03c0,.4-.08.76-.23,1.06m-2.16-4.85c0-.29-.11-.52-.32-.67-.22-.15-.53-.23-.94-.23h-1.9v1.86h1.77c.42,0,.76-.07,1.01-.22s.37-.39.37-.71v-.02h.01Zm.49,3.53c0-.29-.11-.53-.34-.7-.22-.17-.59-.26-1.09-.26h-2.22v1.94h2.29c.42,0,.76-.08,1-.23s.36-.4.36-.72v-.03h0Z"/>
                                                <polygon class="cls-1" points="18.73 110.52 18.73 101.7 25.31 101.7 25.31 103.42 20.63 103.42 20.63 105.21 24.75 105.21 24.75 106.94 20.63 106.94 20.63 108.79 25.38 108.79 25.38 110.52 18.73 110.52"/>
                                                <polygon class="cls-1" points="31.06 103.49 31.06 110.52 29.14 110.52 29.14 103.49 26.48 103.49 26.48 101.7 33.72 101.7 33.72 103.49 31.06 103.49"/>
                                                <polygon class="cls-1" points="38.16 103.49 38.16 110.52 36.23 110.52 36.23 103.49 33.57 103.49 33.57 101.7 40.82 101.7 40.82 103.49 38.16 103.49"/>
                                                <polygon class="cls-1" points="42.04 110.52 42.04 101.7 48.63 101.7 48.63 103.42 43.94 103.42 43.94 105.21 48.06 105.21 48.06 106.94 43.94 106.94 43.94 108.79 48.69 108.79 48.69 110.52 42.04 110.52"/>
                                                <path class="cls-1" d="m54.97,110.52l-1.87-2.82h-1.51v2.82h-1.92v-8.82h4c1.04,0,1.85.26,2.43.77.58.51.87,1.23.87,2.14v.03c0,.71-.17,1.3-.52,1.75s-.8.78-1.36.99l2.14,3.15h-2.26Zm.04-5.81c0-.42-.13-.73-.4-.94-.27-.21-.64-.31-1.11-.31h-1.91v2.53h1.95c.48,0,.84-.11,1.09-.34s.38-.53.38-.91v-.02h0Z"/>
                                                <path class="cls-1" d="m68.39,109.16c-.15.3-.37.55-.66.76-.29.2-.63.35-1.03.45-.4.1-.85.15-1.34.15h-4.15v-8.82h4.05c.9,0,1.6.21,2.11.62.51.41.77.97.77,1.66v.03c0,.25-.03.47-.09.67-.06.19-.14.37-.25.52-.1.16-.22.29-.36.41-.14.12-.28.22-.44.3.5.19.9.46,1.19.79.29.33.43.79.43,1.38v.03c0,.4-.08.76-.23,1.06m-2.16-4.85c0-.29-.11-.52-.32-.67-.22-.15-.53-.23-.94-.23h-1.9v1.86h1.77c.42,0,.76-.07,1.01-.22s.37-.39.37-.71v-.02h.01Zm.49,3.53c0-.29-.11-.53-.34-.7-.22-.17-.59-.26-1.09-.26h-2.22v1.94h2.29c.42,0,.76-.08,1-.23s.36-.4.36-.72v-.03h0Z"/>
                                                <path class="cls-1" d="m76.93,108.42c-.18.5-.44.91-.77,1.24-.33.33-.74.58-1.21.74s-1,.25-1.59.25c-1.17,0-2.1-.33-2.77-.98-.68-.65-1.01-1.63-1.01-2.93v-5.04h1.92v4.99c0,.72.17,1.27.5,1.63.33.37.79.55,1.39.55s1.05-.18,1.39-.53c.33-.35.5-.88.5-1.59v-5.05h1.92v4.98c0,.67-.09,1.26-.27,1.75"/>
                                                <polygon class="cls-1" points="83.3 107 83.3 110.52 81.37 110.52 81.37 107.04 78.01 101.7 80.26 101.7 82.35 105.24 84.47 101.7 86.66 101.7 83.3 107"/>
                                                <polygon class="cls-1" points="94.57 103.49 94.57 110.52 92.64 110.52 92.64 103.49 89.98 103.49 89.98 101.7 97.23 101.7 97.23 103.49 94.57 103.49"/>
                                                <path class="cls-1" d="m103.76,110.52l-1.87-2.82h-1.51v2.82h-1.92v-8.82h4c1.04,0,1.85.26,2.43.77.58.51.87,1.23.87,2.14v.03c0,.71-.17,1.3-.52,1.75s-.8.78-1.36.99l2.14,3.15h-2.26Zm.04-5.81c0-.42-.13-.73-.4-.94-.27-.21-.64-.31-1.11-.31h-1.91v2.53h1.95c.48,0,.84-.11,1.09-.34s.38-.53.38-.91v-.02h0Z"/>
                                                <path class="cls-1" d="m114.24,110.52l-.8-1.98h-3.7l-.8,1.98h-1.96l3.75-8.88h1.77l3.75,8.88h-2.01Zm-2.65-6.55l-1.16,2.86h2.32l-1.16-2.86Z"/>
                                                <path class="cls-1" d="m125.08,107.85c-.23.54-.54,1-.96,1.4-.41.39-.9.71-1.47.93-.57.23-1.2.34-1.88.34h-3.41v-8.82h3.41c.68,0,1.31.11,1.88.33s1.06.53,1.47.93c.41.39.73.86.96,1.39.22.53.34,1.11.34,1.73v.02c0,.62-.11,1.2-.34,1.74m-1.67-1.74c0-.39-.06-.74-.19-1.07s-.31-.61-.54-.84c-.23-.24-.51-.42-.83-.55s-.68-.19-1.07-.19h-1.49v5.32h1.49c.39,0,.75-.06,1.07-.19s.6-.31.83-.54c.23-.24.41-.51.54-.83.13-.32.19-.68.19-1.07v-.04Z"/>
                                                <polygon class="cls-1" points="126.62 110.52 126.62 101.7 133.21 101.7 133.21 103.42 128.52 103.42 128.52 105.21 132.64 105.21 132.64 106.94 128.52 106.94 128.52 108.79 133.27 108.79 133.27 110.52 126.62 110.52"/>
                                            </g>
                                            </g>
                                        </g>
                                    </svg>
                                </a>                        
                            </div>
                        </div>
                       <div class="col-xl-11 col-md-10">
                            
                        <div class="header-right-inner">                            
                                <div class="row align-items-center">
                                    <div class="col-xl-8 col-lg-7 col-md-6">
                                        <div class="header-search">
                                            <form action="{{route('productlistview',['id'=>13,'test'=>1])}}">                        
                                                <input type="search" name="" id="search-box" placeholder="Search the Product or Services you need..." >
                                                <button type="submit" class="search-button search-box-click"><i class="fa-solid fa-magnifying-glass text-white"></i></button>
                                            </form>
                                            <div class="auto-suggested-box suggesstion-box d-none" id="suggesstion-box">
                                            
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-5 col-md-6">
                                        <div class="header-buttons">
                                        @if(!empty($user_id))
                                        <div class="information-button">
                                                <a href="{{route('my-account')}}" class="common-btn common-border-btn hvr-radial-out-black">
                                                    <i class="fa-solid fa-user text-dark-grey me-md-2 me-0"></i>
                                                    <span>Profile</span>
                                                </a>
                                                <ul>
                                                    <li>
                                                        <button type="button" class="round-button backdrop notification_btn" data-bs-toggle="offcanvas" data-bs-target="#notificationoffcanvas" aria-controls="offcanvasRight"><i class="fa-solid fa-bell"></i>
                                                            @if($notificationListCount > 0)
                                        <span class="count-number">{{@$notificationListCount}}</span>
                                        @endif
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="round-button backdrop" data-bs-toggle="offcanvas" data-bs-target="#miniCartoffcanvas" aria-controls="offcanvasRight"><i class="fa-solid fa-cart-shopping"></i>
                                                            @if($cartItemCount != 0)
                                                            <span class="count-number">{{@$cartItemCount}}</span>
                                                            @endif
                                                        </button>  
                                                    </li>
                                                </ul>                                                                                        
                                            </div>
                                        @else
                                        <div class="validation-buttons">
                                                <a href="{{route('websitelogin')}}" class="common-btn common-border-btn hvr-radial-out-black">Login</a>
                                                <a href="{{route('websiteregister')}}" class="common-btn hvr-radial-out">Sign up</a>                                            
                                            </div>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </div> 
                                          
                        </div>
                    </div>
                </div>            
            </div>    
            <div class="mobile-header">
                <div class="mobile-header-main">                
                    <div class="mobile_sidebar_menu">
                        <button class="mobile-header__menu-button mobile_only backdrop" type="button">
                        <i class="fa-solid fa-bars text-dark-grey"></i>
                        </button>    
                        <div class="mobile-menu mobile_only">
                            <div class="mobile-menu__backdrop"></div>
                            <div class="mobile-menu__body">         
                                <button class="mobile-menu__close" type="button"><i class="fa-solid fa-xmark text-white"></i></button>
                                
                                <div class="mobile-menu__panel">
                                    <div class="mobile-menu__panel-header">
                                        <div class="mobile-menu__panel-title mobile-menu__panel-title-logo">
                                            <div class="top_logo">
                                                <a href="{{route('frontend.home')}}">
                                                    <svg id="menu_logo" data-name="menu logo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 144.12 144.12">
                                                        <defs>
                                                            <style>
                                                            .cls-5 {
                                                                fill: #FFFFFF;
                                                            }

                                                            .cls-4 {
                                                                fill: url(#mobile_logo_gradient);
                                                            }
                                                            </style>
                                                            <linearGradient id="mobile_logo_gradient" x1="9.6" y1="78.13" x2="49.56" y2="37.85" gradientUnits="userSpaceOnUse">
                                                            <stop offset="0" stop-color="#ffdc3c"/>
                                                            <stop offset=".28" stop-color="#ffd339"/>
                                                            <stop offset=".47" stop-color="#ffce37"/>
                                                            <stop offset=".69" stop-color="#ffc033"/>
                                                            <stop offset=".75" stop-color="#ffbb32"/>
                                                            <stop offset="1" stop-color="#ffab2e"/>
                                                            </linearGradient>
                                                        </defs>
                                                        <g id="menu_logo_text" data-name="menu logo text">
                                                            <g>
                                                            <path class="cls-5" d="m139.18,0H4.94C2.22,0,0,2.22,0,4.94v134.24c0,2.72,2.22,4.94,4.94,4.94h134.24c2.73,0,4.94-2.22,4.94-4.94V4.94c0-2.72-2.22-4.94-4.94-4.94Zm1.94,139.19c0,1.07-.87,1.94-1.94,1.94H4.94c-1.07,0-1.94-.87-1.94-1.94V4.95c0-1.07.87-1.94,1.94-1.94h0s134.24-.01,134.24-.01c1.07,0,1.94.87,1.94,1.94v134.25Z"/>
                                                            <path class="cls-5" d="m10.04,36.05v12.98h14.76v40.48h14.82v-40.48h4.34l1.64-2.44c4.15-5.37,8.27-8.96,14.18-10.54H10.04Z"/>
                                                            <g>
                                                                <path class="cls-4" d="m45.28,78.36l20.01-15.28c4.97-3.89,6.88-6.42,6.88-9.62s-2.37-5.42-5.96-5.42-6.42,2.06-10.62,6.87l-11.71-5.78,1.7-2.54c5.73-7.41,11.38-11.46,21.77-11.46,11.68,0,19.7,6.95,19.7,17.03v.15c0,8.55-4.43,12.98-12.37,18.71l-9.17,6.42h22.07v12.07h-42.31v-11.15h.01Zm45.07,3.74l8.48-9.93c4.35,3.67,8.48,5.8,12.98,5.8,4.89,0,7.87-2.44,7.87-6.26v-.15c0-3.82-3.21-6.19-7.87-6.19-3.28,0-6.03,1.15-8.55,2.67l-8.78-4.89,1.53-27.11h35.74v12.22h-24.06l-.46,8.1c2.44-1.15,4.96-1.91,8.55-1.91,9.62,0,18.33,5.35,18.33,16.96v.15c0,11.91-9.09,19.02-22.07,19.02-9.47,0-16.11-3.28-21.69-8.48"/>
                                                                <path class="cls-5" d="m17.4,109.16c-.15.3-.37.55-.66.76-.29.2-.63.35-1.03.45-.4.1-.85.15-1.34.15h-4.15v-8.82h4.05c.9,0,1.6.21,2.12.62.51.41.77.97.77,1.66v.03c0,.25-.03.47-.09.67-.06.19-.14.37-.25.52-.1.16-.22.29-.36.41-.14.12-.28.22-.44.3.5.19.9.46,1.19.79.29.33.44.79.44,1.38v.03c0,.4-.08.76-.23,1.06m-2.16-4.85c0-.29-.11-.52-.32-.67-.22-.15-.53-.23-.94-.23h-1.9v1.86h1.77c.42,0,.76-.07,1.01-.22s.37-.39.37-.71v-.02h.01Zm.49,3.53c0-.29-.11-.53-.34-.7-.22-.17-.59-.26-1.09-.26h-2.22v1.94h2.29c.42,0,.76-.08,1-.23s.36-.4.36-.72v-.03h0Z"/>
                                                                <polygon class="cls-5" points="18.73 110.52 18.73 101.7 25.31 101.7 25.31 103.42 20.63 103.42 20.63 105.21 24.75 105.21 24.75 106.94 20.63 106.94 20.63 108.79 25.38 108.79 25.38 110.52 18.73 110.52"/>
                                                                <polygon class="cls-5" points="31.06 103.49 31.06 110.52 29.14 110.52 29.14 103.49 26.48 103.49 26.48 101.7 33.72 101.7 33.72 103.49 31.06 103.49"/>
                                                                <polygon class="cls-5" points="38.16 103.49 38.16 110.52 36.23 110.52 36.23 103.49 33.57 103.49 33.57 101.7 40.82 101.7 40.82 103.49 38.16 103.49"/>
                                                                <polygon class="cls-5" points="42.04 110.52 42.04 101.7 48.63 101.7 48.63 103.42 43.94 103.42 43.94 105.21 48.06 105.21 48.06 106.94 43.94 106.94 43.94 108.79 48.69 108.79 48.69 110.52 42.04 110.52"/>
                                                                <path class="cls-5" d="m54.97,110.52l-1.87-2.82h-1.51v2.82h-1.92v-8.82h4c1.04,0,1.85.26,2.43.77.58.51.87,1.23.87,2.14v.03c0,.71-.17,1.3-.52,1.75s-.8.78-1.36.99l2.14,3.15h-2.26Zm.04-5.81c0-.42-.13-.73-.4-.94-.27-.21-.64-.31-1.11-.31h-1.91v2.53h1.95c.48,0,.84-.11,1.09-.34s.38-.53.38-.91v-.02h0Z"/>
                                                                <path class="cls-5" d="m68.39,109.16c-.15.3-.37.55-.66.76-.29.2-.63.35-1.03.45-.4.1-.85.15-1.34.15h-4.15v-8.82h4.05c.9,0,1.6.21,2.11.62.51.41.77.97.77,1.66v.03c0,.25-.03.47-.09.67-.06.19-.14.37-.25.52-.1.16-.22.29-.36.41-.14.12-.28.22-.44.3.5.19.9.46,1.19.79.29.33.43.79.43,1.38v.03c0,.4-.08.76-.23,1.06m-2.16-4.85c0-.29-.11-.52-.32-.67-.22-.15-.53-.23-.94-.23h-1.9v1.86h1.77c.42,0,.76-.07,1.01-.22s.37-.39.37-.71v-.02h.01Zm.49,3.53c0-.29-.11-.53-.34-.7-.22-.17-.59-.26-1.09-.26h-2.22v1.94h2.29c.42,0,.76-.08,1-.23s.36-.4.36-.72v-.03h0Z"/>
                                                                <path class="cls-5" d="m76.93,108.42c-.18.5-.44.91-.77,1.24-.33.33-.74.58-1.21.74s-1,.25-1.59.25c-1.17,0-2.1-.33-2.77-.98-.68-.65-1.01-1.63-1.01-2.93v-5.04h1.92v4.99c0,.72.17,1.27.5,1.63.33.37.79.55,1.39.55s1.05-.18,1.39-.53c.33-.35.5-.88.5-1.59v-5.05h1.92v4.98c0,.67-.09,1.26-.27,1.75"/>
                                                                <polygon class="cls-5" points="83.3 107 83.3 110.52 81.37 110.52 81.37 107.04 78.01 101.7 80.26 101.7 82.35 105.24 84.47 101.7 86.66 101.7 83.3 107"/>
                                                                <polygon class="cls-5" points="94.57 103.49 94.57 110.52 92.64 110.52 92.64 103.49 89.98 103.49 89.98 101.7 97.23 101.7 97.23 103.49 94.57 103.49"/>
                                                                <path class="cls-5" d="m103.76,110.52l-1.87-2.82h-1.51v2.82h-1.92v-8.82h4c1.04,0,1.85.26,2.43.77.58.51.87,1.23.87,2.14v.03c0,.71-.17,1.3-.52,1.75s-.8.78-1.36.99l2.14,3.15h-2.26Zm.04-5.81c0-.42-.13-.73-.4-.94-.27-.21-.64-.31-1.11-.31h-1.91v2.53h1.95c.48,0,.84-.11,1.09-.34s.38-.53.38-.91v-.02h0Z"/>
                                                                <path class="cls-5" d="m114.24,110.52l-.8-1.98h-3.7l-.8,1.98h-1.96l3.75-8.88h1.77l3.75,8.88h-2.01Zm-2.65-6.55l-1.16,2.86h2.32l-1.16-2.86Z"/>
                                                                <path class="cls-5" d="m125.08,107.85c-.23.54-.54,1-.96,1.4-.41.39-.9.71-1.47.93-.57.23-1.2.34-1.88.34h-3.41v-8.82h3.41c.68,0,1.31.11,1.88.33s1.06.53,1.47.93c.41.39.73.86.96,1.39.22.53.34,1.11.34,1.73v.02c0,.62-.11,1.2-.34,1.74m-1.67-1.74c0-.39-.06-.74-.19-1.07s-.31-.61-.54-.84c-.23-.24-.51-.42-.83-.55s-.68-.19-1.07-.19h-1.49v5.32h1.49c.39,0,.75-.06,1.07-.19s.6-.31.83-.54c.23-.24.41-.51.54-.83.13-.32.19-.68.19-1.07v-.04Z"/>
                                                                <polygon class="cls-5" points="126.62 110.52 126.62 101.7 133.21 101.7 133.21 103.42 128.52 103.42 128.52 105.21 132.64 105.21 132.64 106.94 128.52 106.94 128.52 108.79 133.27 108.79 133.27 110.52 126.62 110.52"/>
                                                            </g>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mobile-menu__panel-body">
                                        <ul class="mobile-menu__links">
                                            <li class="main-nav__item" data-mobile-menu-item="">
                                                <a href="{{route('store.listing-offline')}}" class="sidebar_close">Store</a>
                                                <a class="child_menu_icon" data-mobile-menu-trigger=""><i class="fa-solid fa-chevron-right right_icon"></i></a>

                                                <div class="mobile-menu__links-panel" data-mobile-menu-panel>
                                                    <div class="mobile-menu__panel mobile-menu__panel--hidden">
                                                        <div class="mobile-menu__panel-header">
                                                            <button class="mobile-menu__panel-back" type="button">
                                                                <!--<i class="icon_24 icon_arrow_back"></i>-->
                                                                <i class="fa-solid fa-chevron-left text-white"></i><span>Back</span>
                                                            </button>
                                                            <div class="mobile-menu__panel-title">Shop Online</div>
                                                        </div>
                                                        <div class="mobile-menu__panel-body">
                                                            <ul class="mobile-menu__links second_level">
                                                                @foreach($serviceData as $service)
                                                                <li data-mobile-menu-item>
                                                                    <a href="{{route('dashboard-category',['id'=>$service->id])}}" >{{@$service->title}}</a>
                                                                </li>
                                                                <div class="mobile-menu__divider"></div>
                                                                @endforeach
                                                                
                                                                
                                                            </ul>
                                                        </div>                          
                                                    </div>
                                                </div>
                                            </li>
                                            <div class="mobile-menu__divider"></div>
                                            
                                            <div class="mobile-menu__divider"></div>
                                            <li class="main-nav__item" data-mobile-menu-item="">
                                                <a href="" class="sidebar_close" data-bs-toggle="modal" data-bs-target="#getquoteModal">Get Quote</a>
                                            </li>
                                            @if(empty($user_id))
                                            <li class="main-nav__item" data-mobile-menu-item="">
                                                <a href="{{route('websitelogin')}}" class="sidebar_close">Login</a>
                                                <a class="child_menu_icon" data-mobile-menu-trigger=""></a>
                                            </li>

                                            <li class="main-nav__item" data-mobile-menu-item="">
                                                <a href="{{route('websiteregister')}}" class="sidebar_close">Sign up</a>
                                                <a class="child_menu_icon" data-mobile-menu-trigger=""></a>
                                            </li>
                                            @else
                                        
                                                <li class="main-nav__item {{ \Request::route()->getName() == 'my-account' || \Request::route()->getName() == 'edit-profile' || \Request::route()->getName() == 'userchange-password' || \Request::route()->getName() == 'my-address' || \Request::route()->getName() == 'add-address' || \Request::route()->getName() == 'edit-address' || \Request::route()->getName() == 'my-order'  || \Request::route()->getName() == 'order-detail' || \Request::route()->getName() == 'my-quote' || \Request::route()->getName() == 'favorite-list'  ? 'active' : ' ' }}" data-mobile-menu-item="">
                                                <a href="{{route('my-account')}}" class="sidebar_close">My Account</a>
                                                <a class="child_menu_icon" data-mobile-menu-trigger=""><i class="fa-solid fa-chevron-right right_icon"></i></a>

                                                <div class="mobile-menu__links-panel" data-mobile-menu-panel>
                                                    <div class="mobile-menu__panel mobile-menu__panel--hidden">
                                                        <div class="mobile-menu__panel-header">
                                                            <button class="mobile-menu__panel-back" type="button">
                                                                <!--<i class="icon_24 icon_arrow_back"></i>-->
                                                                <i class="fa-solid fa-chevron-left text-white"></i><span>Back</span>
                                                            </button>
                                                            <div class="mobile-menu__panel-title">My Account</div>
                                                        </div>
                                                        <div class="mobile-menu__panel-body">
                                                            <ul class="mobile-menu__links second_level">
                                                                <li class="{{ \Request::route()->getName() == 'my-account' || \Request::route()->getName() == 'edit-profile' || \Request::route()->getName() == 'userchange-password'  ? 'active' : ' ' }}" data-mobile-menu-item>
                                                                    <a href="{{route('my-account')}}">My Account</a>
                                                                </li>
                                                                <div class="mobile-menu__divider"></div>
                                                                <li class="{{ \Request::route()->getName() == 'my-address' || \Request::route()->getName() == 'add-address' || \Request::route()->getName() == 'edit-address'  ? 'active' : ' ' }}" data-mobile-menu-item="">
                                                                    <a href="{{route('my-address')}}">My Address</a>
                                                                </li>
                                                                <div class="mobile-menu__divider"></div>
                                                                <li class="{{ \Request::route()->getName() == 'my-order'  || \Request::route()->getName() == 'order-detail'  ? 'active' : ' ' }}" data-mobile-menu-item="">
                                                                    <a href="{{route('my-order')}}">My Order</a>
                                                                </li>
                                                                <div class="mobile-menu__divider"></div>
                                                                <li class="{{ \Request::route()->getName() == 'my-quote'  ? 'active' : ' ' }}" data-mobile-menu-item="">
                                                                    <a href="{{route('my-quote')}}">My Quote</a>
                                                                </li>
                                                                <div class="mobile-menu__divider"></div>
                                                                <li class="{{ \Request::route()->getName() == 'favorite-list'  ? 'active' : ' ' }}" data-mobile-menu-item="">
                                                                    <a href="{{route('favorite-list')}}">My Favorite</a>
                                                                </li>
                                                                <div class="mobile-menu__divider"></div>
                                                                
                                                            </ul>
                                                        </div>                          
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="main-nav__item" data-mobile-menu-item="">
                                                <a href="#" class="sidebar_close" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                                                <a class="child_menu_icon" data-mobile-menu-trigger=""></a>
                                            </li>
                                            @endif                                          
                                        </ul>
                                        <div class="mobile-menu__divider"></div>
                                    </div>
                                </div>                            
                            </div>
                        </div>
                    </div>
                    <div class="header-logo">
                        <a href="{{route('frontend.home')}}">
                            <svg id="mobile_logo" data-name="mobile logo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 144.12 144.12">
                                <defs>
                                    <style>
                                    .cls-1 {
                                        fill: #212b46;
                                    }

                                    .cls-3 {
                                        fill: url(#mobile_logo_gradient);
                                    }
                                    </style>
                                    <linearGradient id="mobile_logo_gradient" x1="9.6" y1="78.13" x2="49.56" y2="37.85" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#ffdc3c"/>
                                    <stop offset=".28" stop-color="#ffd339"/>
                                    <stop offset=".47" stop-color="#ffce37"/>
                                    <stop offset=".69" stop-color="#ffc033"/>
                                    <stop offset=".75" stop-color="#ffbb32"/>
                                    <stop offset="1" stop-color="#ffab2e"/>
                                    </linearGradient>
                                </defs>
                                <g id="mobile_logo_text" data-name="mobile logo text">
                                    <g>
                                    <path class="cls-1" d="m139.18,0H4.94C2.22,0,0,2.22,0,4.94v134.24c0,2.72,2.22,4.94,4.94,4.94h134.24c2.73,0,4.94-2.22,4.94-4.94V4.94c0-2.72-2.22-4.94-4.94-4.94Zm1.94,139.19c0,1.07-.87,1.94-1.94,1.94H4.94c-1.07,0-1.94-.87-1.94-1.94V4.95c0-1.07.87-1.94,1.94-1.94h0s134.24-.01,134.24-.01c1.07,0,1.94.87,1.94,1.94v134.25Z"/>
                                    <path class="cls-3" d="m10.04,36.05v12.98h14.76v40.48h14.82v-40.48h4.34l1.64-2.44c4.15-5.37,8.27-8.96,14.18-10.54H10.04Z"/>
                                    <g>
                                        <path class="cls-1" d="m45.28,78.36l20.01-15.28c4.97-3.89,6.88-6.42,6.88-9.62s-2.37-5.42-5.96-5.42-6.42,2.06-10.62,6.87l-11.71-5.78,1.7-2.54c5.73-7.41,11.38-11.46,21.77-11.46,11.68,0,19.7,6.95,19.7,17.03v.15c0,8.55-4.43,12.98-12.37,18.71l-9.17,6.42h22.07v12.07h-42.31v-11.15h.01Zm45.07,3.74l8.48-9.93c4.35,3.67,8.48,5.8,12.98,5.8,4.89,0,7.87-2.44,7.87-6.26v-.15c0-3.82-3.21-6.19-7.87-6.19-3.28,0-6.03,1.15-8.55,2.67l-8.78-4.89,1.53-27.11h35.74v12.22h-24.06l-.46,8.1c2.44-1.15,4.96-1.91,8.55-1.91,9.62,0,18.33,5.35,18.33,16.96v.15c0,11.91-9.09,19.02-22.07,19.02-9.47,0-16.11-3.28-21.69-8.48"/>
                                        <path class="cls-1" d="m17.4,109.16c-.15.3-.37.55-.66.76-.29.2-.63.35-1.03.45-.4.1-.85.15-1.34.15h-4.15v-8.82h4.05c.9,0,1.6.21,2.12.62.51.41.77.97.77,1.66v.03c0,.25-.03.47-.09.67-.06.19-.14.37-.25.52-.1.16-.22.29-.36.41-.14.12-.28.22-.44.3.5.19.9.46,1.19.79.29.33.44.79.44,1.38v.03c0,.4-.08.76-.23,1.06m-2.16-4.85c0-.29-.11-.52-.32-.67-.22-.15-.53-.23-.94-.23h-1.9v1.86h1.77c.42,0,.76-.07,1.01-.22s.37-.39.37-.71v-.02h.01Zm.49,3.53c0-.29-.11-.53-.34-.7-.22-.17-.59-.26-1.09-.26h-2.22v1.94h2.29c.42,0,.76-.08,1-.23s.36-.4.36-.72v-.03h0Z"/>
                                        <polygon class="cls-1" points="18.73 110.52 18.73 101.7 25.31 101.7 25.31 103.42 20.63 103.42 20.63 105.21 24.75 105.21 24.75 106.94 20.63 106.94 20.63 108.79 25.38 108.79 25.38 110.52 18.73 110.52"/>
                                        <polygon class="cls-1" points="31.06 103.49 31.06 110.52 29.14 110.52 29.14 103.49 26.48 103.49 26.48 101.7 33.72 101.7 33.72 103.49 31.06 103.49"/>
                                        <polygon class="cls-1" points="38.16 103.49 38.16 110.52 36.23 110.52 36.23 103.49 33.57 103.49 33.57 101.7 40.82 101.7 40.82 103.49 38.16 103.49"/>
                                        <polygon class="cls-1" points="42.04 110.52 42.04 101.7 48.63 101.7 48.63 103.42 43.94 103.42 43.94 105.21 48.06 105.21 48.06 106.94 43.94 106.94 43.94 108.79 48.69 108.79 48.69 110.52 42.04 110.52"/>
                                        <path class="cls-1" d="m54.97,110.52l-1.87-2.82h-1.51v2.82h-1.92v-8.82h4c1.04,0,1.85.26,2.43.77.58.51.87,1.23.87,2.14v.03c0,.71-.17,1.3-.52,1.75s-.8.78-1.36.99l2.14,3.15h-2.26Zm.04-5.81c0-.42-.13-.73-.4-.94-.27-.21-.64-.31-1.11-.31h-1.91v2.53h1.95c.48,0,.84-.11,1.09-.34s.38-.53.38-.91v-.02h0Z"/>
                                        <path class="cls-1" d="m68.39,109.16c-.15.3-.37.55-.66.76-.29.2-.63.35-1.03.45-.4.1-.85.15-1.34.15h-4.15v-8.82h4.05c.9,0,1.6.21,2.11.62.51.41.77.97.77,1.66v.03c0,.25-.03.47-.09.67-.06.19-.14.37-.25.52-.1.16-.22.29-.36.41-.14.12-.28.22-.44.3.5.19.9.46,1.19.79.29.33.43.79.43,1.38v.03c0,.4-.08.76-.23,1.06m-2.16-4.85c0-.29-.11-.52-.32-.67-.22-.15-.53-.23-.94-.23h-1.9v1.86h1.77c.42,0,.76-.07,1.01-.22s.37-.39.37-.71v-.02h.01Zm.49,3.53c0-.29-.11-.53-.34-.7-.22-.17-.59-.26-1.09-.26h-2.22v1.94h2.29c.42,0,.76-.08,1-.23s.36-.4.36-.72v-.03h0Z"/>
                                        <path class="cls-1" d="m76.93,108.42c-.18.5-.44.91-.77,1.24-.33.33-.74.58-1.21.74s-1,.25-1.59.25c-1.17,0-2.1-.33-2.77-.98-.68-.65-1.01-1.63-1.01-2.93v-5.04h1.92v4.99c0,.72.17,1.27.5,1.63.33.37.79.55,1.39.55s1.05-.18,1.39-.53c.33-.35.5-.88.5-1.59v-5.05h1.92v4.98c0,.67-.09,1.26-.27,1.75"/>
                                        <polygon class="cls-1" points="83.3 107 83.3 110.52 81.37 110.52 81.37 107.04 78.01 101.7 80.26 101.7 82.35 105.24 84.47 101.7 86.66 101.7 83.3 107"/>
                                        <polygon class="cls-1" points="94.57 103.49 94.57 110.52 92.64 110.52 92.64 103.49 89.98 103.49 89.98 101.7 97.23 101.7 97.23 103.49 94.57 103.49"/>
                                        <path class="cls-1" d="m103.76,110.52l-1.87-2.82h-1.51v2.82h-1.92v-8.82h4c1.04,0,1.85.26,2.43.77.58.51.87,1.23.87,2.14v.03c0,.71-.17,1.3-.52,1.75s-.8.78-1.36.99l2.14,3.15h-2.26Zm.04-5.81c0-.42-.13-.73-.4-.94-.27-.21-.64-.31-1.11-.31h-1.91v2.53h1.95c.48,0,.84-.11,1.09-.34s.38-.53.38-.91v-.02h0Z"/>
                                        <path class="cls-1" d="m114.24,110.52l-.8-1.98h-3.7l-.8,1.98h-1.96l3.75-8.88h1.77l3.75,8.88h-2.01Zm-2.65-6.55l-1.16,2.86h2.32l-1.16-2.86Z"/>
                                        <path class="cls-1" d="m125.08,107.85c-.23.54-.54,1-.96,1.4-.41.39-.9.71-1.47.93-.57.23-1.2.34-1.88.34h-3.41v-8.82h3.41c.68,0,1.31.11,1.88.33s1.06.53,1.47.93c.41.39.73.86.96,1.39.22.53.34,1.11.34,1.73v.02c0,.62-.11,1.2-.34,1.74m-1.67-1.74c0-.39-.06-.74-.19-1.07s-.31-.61-.54-.84c-.23-.24-.51-.42-.83-.55s-.68-.19-1.07-.19h-1.49v5.32h1.49c.39,0,.75-.06,1.07-.19s.6-.31.83-.54c.23-.24.41-.51.54-.83.13-.32.19-.68.19-1.07v-.04Z"/>
                                        <polygon class="cls-1" points="126.62 110.52 126.62 101.7 133.21 101.7 133.21 103.42 128.52 103.42 128.52 105.21 132.64 105.21 132.64 106.94 128.52 106.94 128.52 108.79 133.27 108.79 133.27 110.52 126.62 110.52"/>
                                    </g>
                                    </g>
                                </g>
                            </svg>
                        </a>                        
                    </div>
                    @if(!empty($user_id))
                    <ul class="mobile-header-buttons">
                        <li>
                            <button type="submit" class="round-button" onclick="return Myaccount()"><i class="fa-solid fa-user text-dark-grey"></i></button>
                        </li>
                        <li>
                            <button type="button" class="round-button backdrop" data-bs-toggle="offcanvas" data-bs-target="#notificationoffcanvas" aria-controls="offcanvasRight"><i class="fa-solid fa-bell text-dark-grey"></i>
                               @if($notificationListCount > 0)
                                        <span class="count-number">{{@$notificationListCount}}</span>
                                        @endif
                            </button>
                        </li>
                        <li>
                           <button type="button" class="round-button backdrop" data-bs-toggle="offcanvas" data-bs-target="#miniCartoffcanvas" aria-controls="offcanvasRight"><i class="fa-solid fa-cart-shopping"></i>
                            @if($cartItemCount != 0)
                                <span class="count-number">{{@$cartItemCount}}</span>
                                @endif
                            </button>
                        </li>
                    </ul>
                    @endif
                </div>
                <div class="header-search <?php if ($menu_link=="landing.php") {echo "d-none"; } else { echo "d-block"; }?>">
                    <form action="">                        
                        <input type="search" name="" id="search-box2" placeholder="Search the Product or Services you need..." >
                        <button type="submit" class="search-button search-box-click"><i class="fa-solid fa-magnifying-glass text-white"></i></button>
                                            </form>
                                            <div class="auto-suggested-box suggesstion-box d-none" id="suggesstion-box">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="mega-menu">
        <div class="container">
            <ul class="menu">
                @foreach($serviceData as $service)
                <li class="menu-item menu-item-has-children">
                    <a href="#" onclick="return ChangeFilter('{{$service->id}}')">
                        <i class="fa-solid {{@$service->font_icon_name}}"></i><span>{{@$service->title}}</span>
                    </a>                
                </li>

                @endforeach
                
            </ul>
        </div>        
    </div> -->
    <div class="categories-slide">
        <div class="container">
            <div class="swiper categories-slider">
                <ul class="swiper-wrapper">
                    @foreach($serviceData as $service)
                    <!-- onclick="return ChangeFilter('{{$service->id}}')" -->
                    <li class="swiper-slide">
                        <a href="{{route('dashboard-category',['id'=>$service->id])}}" >
                            <i class="fa-solid {{@$service->font_icon_name}}"></i><span>{{@$service->title}}</span>
                    </a>
                    </li>
                    @endforeach
                    
                </ul>
            </div>
            <div class="swiper-button-row">
                <div class="swiper-button-prev-cate"></div>
                <div class="swiper-button-next-cate"></div>
            </div>
        </div>
    </div>

</header>  
<div class="modal fade remove-item-modal p-0 show" id="logoutModal" tabindex="-1" aria-modal="true">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    <input type="hidden" name="address_id" id="address_id" value="">
     <div class="modal-body">
        <h5 class="mb-3">Logout</h5>
         <p class="body-large mb-4">Are you sure you want to logout ?</p>
          <div class="d-flex justify-content-between gap-2">
           <button type="submit" class="small-common-btn common-border-btn hvr-radial-out-black w-100" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            <button type="submit" class="small-common-btn hvr-radial-out w-100" onclick="return logoutUser()">Yes</button>
             </div>
              </div>
               </div>
                </div>
            </div>
        </div>
        @if(!empty($user_id))
        <div class="offcanvas offcanvas-end offcanvasNotification" tabindex="-1" id="notificationoffcanvas" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasRightLabel">Notification</h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="offcanvas-body">
            <ul class="notification-block mb-0">

                @foreach($notificationList as $notification)
                <?php 
            
                $date_time = \Helper::converttimeTozone($notification->created_at);

                $date_time = date('h:i A d M, Y',strtotime($date_time));

                if ($notification->notification_type == 1) {
                    $icon = "fa-solid fa-check text-dark-green";
                }elseif ($notification->notification_type == 2) {
                    $icon = "fa-solid fa-image text-orange";
                    
                }elseif ($notification->notification_type == 3) {
                    $icon = "fa-solid fa-van-shuttle text-pink";
                    
                }elseif ($notification->notification_type == 4) {
                    $icon = "fa-solid fa-check text-dark-green";
                    
                }elseif ($notification->notification_type == 5) {
                    $icon = "fa-solid fa-xmark text-dark-green";
                    
                }else{
                    $icon = "fa-solid fa-image text-orange";

                }
                ?>
                <li>
                    <div class="preview-img">
                        <span class="bg-light-orange"><i class="{{@$icon}}"></i></span>
                    </div>                    
                    <div class="preview-info">
                        <p class="body-normal text-black">{{@$notification->title}}</p>
                        <span class="text-grey d-block">{{@$date_time}}</span>
                    </div>
                </li>
                @endforeach
                <!-- <li>
                    <div class="preview-img">
                        <span class="bg-light-purple"><i class="fa-solid fa-medal text-purple"></i></span>
                    </div>
                    <div class="preview-info">
                        <p class="body-normal text-black">Get $10 as loyalty point</p>
                        <span class="text-grey d-block">08:52 PM 17 jan, 2023</span>
                    </div>
                </li>
                <li>
                    <div class="preview-img">
                        <span class="bg-light-pink"><i class="fa-solid fa-van-shuttle text-pink"></i></span>
                    </div>
                    <div class="preview-info">
                        <p class="body-normal text-black">Your package will dispatched at 18 jan, 2023</p>
                        <span class="text-grey d-block">02:52 PM 17 jan, 2023</span>
                    </div>
                </li>
                <li>
                    <div class="preview-img">
                        <span class="bg-light-green"><i class="fa-solid fa-check text-dark-green"></i></span>
                    </div>
                    <div class="preview-info">
                        <p class="body-normal text-black">Order Confirmed</p>
                        <span class="text-grey d-block">06:52 PM 16 jan, 2023</span>
                    </div>
                </li>
                <li>
                    <div class="preview-img">
                        <span class="bg-light-purple"><i class="fa-solid fa-bag-shopping text-purple"></i></span>
                    </div>
                    <div class="preview-info">
                        <p class="body-normal text-black">Package from your order #18456ABC has arrived.</p>
                        <span class="text-grey d-block">07:52 PM 16 jan, 2023</span>
                    </div>
                </li> -->
            </ul>
        </div>
    </div>
    @endif
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script type="text/javascript">

    
    $('.notification_btn').click(function(e) {
       // Add and Remove class from body 
        $('body').addClass('scrollidisable'); 
        e.stopPropagation(); 
    });

    $('html').on('click', function(e) { 
        $('body').removeClass('scrollidisable'); 
        e.stopPropagation(); 
    });

 function logoutUser()
        {
            // alert('ff')
            var data = 1;
             action_url = "{{ route('user-logout') }}";
            var csrf = "{{ csrf_token() }}";

            $.ajax({
                            url: action_url,
                            data: {'data':data},
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            type: "POST",
                    
                             beforeSend: function(){
                                    $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
                            success: function (response) 
                            {
                                // return false;
                                var url = "{{route('frontend.home')}}";
                                    window.location.href = url;
                                // location.reload();
                            },
                        });
        }

    function Myaccount(){
        // alert()
        var user_id = $("#user_id").val();
        // alert(user_id)
        if (user_id != "") {
            var url = "{{route('my-account')}}";
            window.location.href = url;
        }else{
            var url = "{{route('websitelogin')}}";
            window.location.href = url;
        }
    }

    $(document).ready(function(){
// const element = document.querySelector('form');
// element.addEventListener('submit', event => {
//  // event.preventDefault(); 
//     alert('hello')
//  Search();
// });
    $("#search-box, #search-box-click").keyup(function() {
        // alert('hello')
        Search();
    });

    function Search(){
    // alert('hello236')    
        var keyword = $("#search-box").val();  
        // alert(APP_URL);
        action_url = "{{ route('search-auto-suggestion') }}";
        // alert(keyword)
        var csrf = "{{ csrf_token() }}";
        // return false;   
        if(keyword!=''){
            $.ajax({            
                url: action_url,
                data: {'keyword':keyword}, 
                headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                type: "POST",     
                beforeSend: function() {
                    // $("#search-box").css("background", "#FFF url(LoaderIcon.gif) no-repeat 165px");
                },
                success: function(response) {
                // console.log('response'+response)
                // return false; 
                        if(response){
                            $('.suggesstion-box').css('border','1px solid rgba(114, 106, 106, 0.2)');
                            // $('.suggesstion-box').css('display','none');
                            $(".suggesstion-box").removeClass("d-none")
                            // var outputText = '<ul id="auto-box">'+response+'</ul>';
                            $("#suggesstion-box").html(outputText);
                        }
                        else{
                            $(".suggesstion-box").removeClass("d-none")
                            // $('.suggesstion-box').css('display','none');
                            // var outputText = '<ul id="auto-box">';  
                            // var divHtml ='<li><a href="javascript::void(0);">No results found.</a> </li>';
                            //     outputText += divHtml; 
                            // outputText += '</ul>';     
                            var divHtml = '<div class="suggestion-grid"><div class="suggestion-item"><a href="javascript:void(0);">No results found.</a></div></div>';                      
                            $("#suggesstion-box").html(outputText);
                        }
                }
            });
        }else{
            $('.suggesstion-box').css('border','none');
            $("#suggesstion-box").html('');
        }
    };
});

    $("#search-box").keydown(function (e) {
    if (e.keyCode == 40) {    
        
        $("#auto-box li:eq(0)").addClass('active').children('a').focus();    
        $('#auto-box').animate({scrollTop: '0px'}, 1000);
        // var el = document.querySelector('#auto-box');
        // //el.scrollTop = el.scrollHeight;
        // setTimeout(function(){
        // el.scrollTop = 0;
        // }, 50);   
    }
});

$("body #suggesstion-box").keydown(function (e) {
    //$("#auto-box li:eq(0)").addClass('active').children('a').focus();
    var total_li_count = $("#suggesstion-box ul li").length ;
    if (e.which == 40) {
        var data_class = $("#suggesstion-box .active").attr('data-index');
        var tli = parseInt(total_li_count-1);
        if(tli == data_class  ){
            $('#auto-box li:eq('+tli+')').addClass('active').children('a').focus();
        }else{
            var next = $('.active').removeClass('active').next('li');
            next = next.length > 0 ? next : $('.focus li:eq(0)');
            next.addClass('active').children('a').focus();
        }       
    }    
});

$("body #suggesstion-box").keyup(function (e) {
    var data_class = $("#suggesstion-box .active").attr('data-index');
    if (e.which == 38) {
        if(data_class==0){
            $("#auto-box li:eq(0)").addClass('active').children('a').focus();
        }else{
            var prev = $('.active').removeClass('active').prev('li');
            prev = prev.length > 0 ? prev : $('.focus li').last();
            prev.addClass('active').children('a').focus();
        }
    }
});

function ChangeFilter(category_id){
    // alert('helloo')
        var dashboard_filter = 1;
        var page = 1;
        action_url = "{{ route('productfilter') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: {'category_id':category_id,'dashboard_filter':dashboard_filter,'page':page},
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            type: "POST",
                    
                             beforeSend: function(){
                                    $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
                            success: function (response) 
                            {
                                // console.log(response);
                                // return false;
                                // console.log('test'+response);
                                   var url = "{{route('store.filter')}}";
                                   // url += '/'+response.supplier_id;
                                    window.location.href = url;
                                     // $(document).find("#filterData").modal('show');
                            },
                        });

    }
</script>

{{-- Script for load more auto suggestion --}}
<script>
//     $(document).on('click', '.load-more-btn', function() {
//     var shown = parseInt($(this).data('shown'));
//     var keyword = $("#search-box").val();
//     var csrf = "{{ csrf_token() }}";
//     var action_url = "{{ route('search-auto-suggestion') }}";

//     $.ajax({
//         url: action_url,
//         type: "POST",
//         headers: {
//             'X-CSRF-TOKEN': csrf
//         },
//         data: {
//             keyword: keyword,
//             load_more: true,
//             offset: shown
//         },
//         success: function(response) {
//             $(".search-grid").append(response.html);
//             if (response.remaining <= 0) {
//                 $('.load-more-btn').remove();
//             } else {
//                 $('.load-more-btn').data('shown', shown + response.loaded);
//             }
//         }
//     });
// });

</script>