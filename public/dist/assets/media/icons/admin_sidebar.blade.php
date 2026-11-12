<div class="aside aside-left  aside-fixed  d-flex flex-column flex-row-auto" id="kt_aside">
    <!--begin::Brand-->
    <div class="brand flex-column-auto " id="kt_brand" kt-hidden-height="65" style="">
        <!--begin::Logo-->
        <a href="{{ url('admin/home') }}" class="brand-logo">
            <img alt="Logo" src="{{ asset('dist/assets/media/logos/Logo.png') }}" style="height: 55px;">
        </a>
        <!--end::Logo-->

        <!--begin::Toggle-->
        <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
            <span class="svg-icon svg-icon svg-icon-xl"><!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-left.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                        <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) "></path>
                        <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) "></path>
                    </g>
                </svg><!--end::Svg Icon-->
            </span>           
        </button>
            <!--end::Toolbar-->
    </div>
    <!--end::Brand-->

    <!--begin::Aside Menu-->
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

        <!--begin::Menu Container-->
        <div id="kt_aside_menu" class="aside-menu my-4 scroll ps ps--active-y" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500" style="height: 334px; overflow: hidden;">
            <!--begin::Menu Nav-->
            <ul class="menu-nav ">
                <li class="menu-item {{ Request::is('admin/home*') ? 'menu-item-active' : '' }}  " aria-haspopup="true">
                    <a href="{{ url('admin/home') }}" class="menu-link "><span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
                        
                        <img src="{{ asset('dist/assets/media/icons/dashboard_new.svg') }}" width="20px">
                    </span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
                 @if(Auth::user()->restuarant_type==2 )
                <li class="menu-item {{ Request::is('admin/subadmin*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/subadmin') }}" class="menu-link ">
                        <span class="svg-icon menu-icon">
                            <img src="{{ asset('dist/assets/media/icons/subadmin.svg') }}" width="18px"  >         
                        </span>
                        <span class="menu-text">Sub Admin</span>
                    </a>
                </li>
                @endif
                @if(Auth::user()->restuarant_type==2 )
                <li class="menu-item {{ Request::is('admin/userdetail*') ? 'menu-item-active' : '' || Request::is('admin/customer-orderlist*') ? 'menu-item-active' : '' || Request::is('admin/user-reward*') ? 'menu-item-active' : ''  }} " aria-haspopup="true">
                    <a href="{{ url('admin/userdetail') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\General\User.svg-->
                            <img src="{{ asset('dist/assets/media/icons/manage user.svg') }}" width="25px"  >
                            
    <!--end::Svg Icon--></span>
                        <span class="menu-text">Customers</span>
                    </a>
                </li>

                <li class="menu-item {{ Request::is('admin/driver*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/driver') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\General\User.svg-->
                            <img src="{{ asset('dist/assets/media/icons/driver 1.svg') }}" width="25px"  >
                            
    <!--end::Svg Icon--></span>
                        <span class="menu-text">Drivers</span>
                    </a>
                </li>
                @endif


                <li class="menu-item  menu-item-submenu {{ Request::is('admin/category*') ? 'menu-item-open menu-item-here' : '' }} {{ Request::is('admin/subcategory*') ? 'menu-item-open menu-item-here' : '' }} {{ Request::is('admin/restaurant*') ? 'menu-item-open menu-item-here' : '' }}{{ Request::is('admin/itemlistdata*') ? 'menu-item-open menu-item-here' : '' }}{{ Request::is('admin/itemlist*') ? 'menu-item-open menu-item-here' : '' }}{{ Request::is('admin/timemanagement*') ? 'menu-item-open menu-item-here' : '' }} " aria-haspopup="true" data-menu-toggle="hover">
                    @if(Auth::user()->restuarant_type==0)
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <img src="{{ asset('dist/assets/media/icons/store.svg') }}" width="20px"  >
                        </span>
                        <span class="menu-text">Restaurant</span>
                        <i class="menu-arrow"></i>
                    </a>
                    @elseif(Auth::user()->restuarant_type==1)
                     <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <img src="{{ asset('dist/assets/media/icons/store.svg') }}" width="20px"  >
                        </span>
                        <span class="menu-text">Grocery</span>
                        <i class="menu-arrow"></i>
                    </a>
                    @else
                     <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <img src="{{ asset('dist/assets/media/icons/store.svg') }}" width="20px"  >
                        </span>
                        <span class="menu-text">Restaurant/Grocery</span>
                        <i class="menu-arrow"></i>
                    </a>
                    @endif
                    <div class="menu-submenu ">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">Restaurant/Grocery</span>
                                </span>
                            </li>
                             @if(Auth::user()->restuarant_type==2)
                            <li class="menu-item {{ Request::is('admin/category*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/category') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Categories</span>
                                </a>
                            </li>
                          

                            <li class="menu-item {{ Request::is('admin/subcategory*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/subcategory') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Sub Categories</span>
                                </a>
                            </li>
                              @endif
                            @if(Auth::user()->restuarant_type==2 )
                            <li class="menu-item {{ Request::is('admin/restaurant*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/restaurant') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Restaurant/Grocery</span>
                                </a>
                            </li>
                            @endif
                             @if(Auth::user()->restuarant_type==0 ||Auth::user()->restuarant_type==1 )
                            <li class="menu-item {{ Request::is('admin/itemlistdata*') ? 'menu-item-active' : '' || Request::is('admin/itemlist*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/itemlistdata/'.auth()->user()->id)}}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Menu Item List</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->restuarant_type==0)
                            <li class="menu-item {{ Request::is('admin/timemanagement*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/timemanagement') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Restaurant Hours Management</span>
                                </a>
                            </li>
                            @elseif(Auth::user()->restuarant_type==1)
                             <li class="menu-item {{ Request::is('admin/timemanagement*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/timemanagement') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Grocery Hours Management</span>
                                </a>
                            </li>
                            @endif
                                       @if(Auth::user()->restuarant_type==1 ||Auth::user()->restuarant_type==0 ) 
                <li class="menu-item {{ Request::is('admin/order') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/order') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                            <img src="{{ asset('dist/assets/media/icons/orders.svg') }}" width="15px"  >
                        </span>
                       
                        <span class="menu-text">Orders<strong style="color: white; padding-top:2px;">&nbsp; (<b id="showOrderCount">{{ (new \App\Helpers)->OrderCount()}}</b>)</strong></span>
                       
                    </a>
                </li>
                <script>
                $( document ).ready(function() {
                    console.log( "ready!" );
              
                    window.setInterval(function(){
                       var order_count = $(document).find('#showOrderCount').val();
                      
                        $.ajax({
                            type: "GET",
                            url:"{{ url('/admin/pendingcount')}}",
                            data:'order_count',
                            cache: false,
                            success: function(data){
                                console.log('Here is your data : ' + data);
                                $('#showOrderCount').text(data);
                            },error:function(err){
                                console.log(`Error : ${err}`);
                                $('#showOrderCount').text('0');
                            }
                        });
                        console.log("test");
                    }, 20000);
              
                 });
                 </script>
              
                @endif
                 @if(Auth::user()->restuarant_type==2)
                <li class="menu-item {{ Request::is('admin/order') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/order') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                            <img src="{{ asset('dist/assets/media/icons/orders.svg') }}" width="15px"  >
                        </span>
                        <span class="menu-text">Orders</span>                   
                    </a>
                </li>
                @endif

                            <!-- 4
                             -->       
                        </ul>
                    </div>
                </li>

             @if(Auth::user()->restuarant_type==2||Auth::user()->restuarant_type==1 ||Auth::user()->restuarant_type==0)

                 <li class="menu-item {{ Request::is('admin/discount*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/discount') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                            <img src="{{ asset('dist/assets/media/icons/promo-code.png') }}"  >
                        </span>
                        <span class="menu-text">Promo code</span>
                    </a>
                </li>
        
                 <li class="menu-item {{ Request::is('admin/inquiry*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/inquiry') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                            <img src="{{ asset('dist/assets/media/icons/inquiry.png') }}"  >
                        </span>
                        <span class="menu-text">Inquiry/Contact Us</span>
                    </a>
                </li>      

                <li style="display: none;" class="menu-item {{ Request::is('admin/rented-instrument*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/rented-instrument') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                            <img src="{{ asset('dist/assets/media/icons/manage rented instrument.svg') }}" width="22px"  >
                        </span>
                        <span class="menu-text">Rented Instrument</span>
                    </a>
                </li>


                <li style="display: none;" class="menu-item {{ Request::is('admin/dispute*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/dispute') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                            <i class="fa fa-question-circle"  style="color:white;"></i>
                        </span>
                        <span class="menu-text">Dispute</span>
                    </a>
                </li>

                 <li style="display: none;" class="menu-item {{ Request::is('admin/reportinstrument*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/reportinstrument') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                            <i class="fa fa-question-circle"  style="color:white;"></i>
                        </span>
                        <span class="menu-text">Reported Instruments</span>
                    </a>
                </li>

                <li style="display: none;"  class="menu-item {{ Request::is('admin/invoice*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/invoice') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                            <i class="fas fa-file-invoice-dollar" style="color:white;"></i>
                        </span>
                        <span class="menu-text">Invoice</span>
                    </a>
                </li>

                <li style="display: none;" class="menu-item {{ Request::is('admin/payment-transaction*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                    <a href="{{ url('admin/payment-transaction') }}" class="menu-link ">
                        <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                            <i class="fa fa-credit-card" style="color:white;"></i>
                        </span>
                        <span class="menu-text">Payment Transaction</span>
                    </a>
                </li>

                <li class="menu-item  menu-item-submenu 
                {{ Request::is('admin/user-report*') ? 'menu-item-open menu-item-here' : '' }} 
                {{ Request::is('admin/order-report*') ? 'menu-item-open menu-item-here' : '' }}
                {{ Request::is('admin/dpayment-report*') ? 'menu-item-open menu-item-here' : '' }}   
                {{ Request::is('admin/payment-report*') ? 'menu-item-open menu-item-here' : '' }} 

                 {{ Request::is('admin/store-report*') ? 'menu-item-open menu-item-here' : '' }} 
                 " aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <img src="{{ asset('dist/assets/media/icons/reports.svg') }}" width="18px"  >
                        </span>
                        <span class="menu-text">Reports</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu ">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">Reports</span>
                                </span>
                            </li>
                            @if(Auth::user()->restuarant_type==2)
                            <li class="menu-item {{ Request::is('admin/user-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/user-report') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">User Report</span>
                                </a>
                            </li>  
                            @endif  
                            <li class="menu-item {{ Request::is('admin/order-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/order-report') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Order Report</span>
                                </a>
                            </li> 
                            @if(Auth::user()->restuarant_type==2)
                            <li class="menu-item {{ Request::is('admin/dpayment-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/dpayment-report') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Driver Commision Report</span>
                                </a>
                            </li>
                            @endif

                            <li class="menu-item {{ Request::is('admin/payment-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/payment-report') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Payment History Report</span>
                                </a>
                            </li> 
                            @if(Auth::user()->restuarant_type==2) 
                            <li class="menu-item {{ Request::is('admin/store-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/store-report') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Restaurant/Grocery Report</span>
                                </a>
                            </li>  
                            @endif   
                        </ul>
                    </div>
                </li>
                @if(Auth::user()->restuarant_type==2)
                <li class="menu-item  menu-item-submenu {{ Request::is('admin/deliveryrate*') ? 'menu-item-open menu-item-here' : '' }}{{ Request::is('admin/tip*') ? 'menu-item-open menu-item-here' : '' }} {{ Request::is('admin/label*') ? 'menu-item-open menu-item-here' : '' }}  {{ Request::is('admin/cms*') ? 'menu-item-open menu-item-here' : '' }}  {{ Request::is('admin/push*') ? 'menu-item-open menu-item-here' : '' }}  {{ Request::is('admin/emailtemplate*')  ? 'menu-item-open menu-item-here' : '' }} " aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <i class="fa fa-gear" style="color:white;"></i>
                        </span>
                        <span class="menu-text">Settings</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu ">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">Settings</span>
                                </span>
                            </li>
                             <li class="menu-item {{ Request::is('admin/tip*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/tip') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Driver Tip Amount</span>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::is('admin/label*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/label') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Label</span>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::is('admin/deliveryrate*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/deliveryrate') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text"> Delivery Rate Management</span>
                                </a>
                            </li>         
                            <li class="menu-item {{ Request::is('admin/cms*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/cms') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">CMS</span>
                                </a>
                            </li>     
                            <li class="menu-item {{ Request::is('admin/emailtemplate*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/emailtemplate') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Email Template</span>
                                </a>
                            </li>

                             <li class="menu-item {{ Request::is('admin/push*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                <a href="{{ url('admin/push') }}" class="menu-link ">
                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                    <span class="menu-text">Push Notification</span>
                                </a>
                            </li>    
                                   
                        </ul>
                    </div>
                </li>
                @endif
            <!--end::Menu Nav-->
{{-------------------------------- For SUBADMIN -----------------------------------------------------}}
@if(Auth::user()->restuarant_type==3)
            @if((new \App\Helpers)->check_permission(9, 1) == 1 )
            <li class="menu-item {{ Request::is('admin/home*') ? 'menu-item-active' : '' }}  " aria-haspopup="true">
                <a href="{{ url('admin/home') }}" class="menu-link "><span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
                    
                    <img src="{{ asset('dist/assets/media/icons/dashboard_new.svg') }}" width="20px">
                </span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            @endif
             @if((new \App\Helpers)->check_permission(1, 1) == 1 )
             <li class="menu-item {{ Request::is('admin/userdetail*') ? 'menu-item-active' : '' || Request::is('admin/customer-orderlist*') ? 'menu-item-active' : '' || Request::is('admin/user-reward*') ? 'menu-item-active' : ''  }} " aria-haspopup="true">
                 <a href="{{ url('admin/userdetail') }}" class="menu-link ">
                     <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\General\User.svg-->
                         <img src="{{ asset('dist/assets/media/icons/manage user.svg') }}" width="25px"  >
                        
             </span>
                     <span class="menu-text">Customers</span>
                 </a>
             </li>
             @endif
             @if((new \App\Helpers)->check_permission(2, 1) == 1 )
             <li class="menu-item {{ Request::is('admin/driver*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                 <a href="{{ url('admin/driver') }}" class="menu-link ">
                     <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\General\User.svg-->
                         <img src="{{ asset('dist/assets/media/icons/driver 1.svg') }}" width="25px"  >
                         
             </span>
                     <span class="menu-text">Drivers</span>
                 </a>
             </li>
             @endif
            
             @if((new \App\Helpers)->check_permission(3, 1) == 1 || (new \App\Helpers)->check_permission(10, 1) == 1 ||(new \App\Helpers)->check_permission(11, 1) == 1)
             <li class="menu-item  menu-item-submenu {{ Request::is('admin/category*') ? 'menu-item-open menu-item-here' : '' }} {{ Request::is('admin/subcategory*') ? 'menu-item-open menu-item-here' : '' }} {{ Request::is('admin/restaurant*') ? 'menu-item-open menu-item-here' : '' }}{{ Request::is('admin/itemlistdata*') ? 'menu-item-open menu-item-here' : '' }}{{ Request::is('admin/itemlist*') ? 'menu-item-open menu-item-here' : '' }}{{ Request::is('admin/timemanagement*') ? 'menu-item-open menu-item-here' : '' }} " aria-haspopup="true" data-menu-toggle="hover">
               
                  <a href="javascript:;" class="menu-link menu-toggle">
                     <span class="svg-icon menu-icon">
                         <img src="{{ asset('dist/assets/media/icons/store.svg') }}" width="20px"  >
                     </span>
                     <span class="menu-text">Restaurant/Grocery</span>
                     <i class="menu-arrow"></i>
                 </a>
                 <div class="menu-submenu ">
                     <i class="menu-arrow"></i>
                     <ul class="menu-subnav">
                         <li class="menu-item  menu-item-parent" aria-haspopup="true">
                             <span class="menu-link">
                                 <span class="menu-text">Restaurant/Grocery</span>
                             </span>
                         </li>
                         @if((new \App\Helpers)->check_permission(10, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/category*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/category') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Categories</span>
                             </a>
                         </li>
                         @endif
                         @if((new \App\Helpers)->check_permission(11, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/subcategory*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/subcategory') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Sub Categories</span>
                             </a>
                         </li>
                         @endif
                         @if((new \App\Helpers)->check_permission(3, 1) == 1)
                         <li class="menu-item {{ Request::is('admin/restaurant*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/restaurant') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Restaurant/Grocery</span>
                             </a>
                         </li>
                         @endif
  
                     </ul>
                 </div>
             </li>
             @endif
             @if((new \App\Helpers)->check_permission(4, 1) == 1 )
             <li class="menu-item {{ Request::is('admin/order') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                 <a href="{{ url('admin/order') }}" class="menu-link ">
                     <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                         <img src="{{ asset('dist/assets/media/icons/orders.svg') }}" width="15px"  >
                     </span>
                     <span class="menu-text">Orders</span>
                 </a>
             </li>
             @endif
             @if((new \App\Helpers)->check_permission(5, 1) == 1 )
             <li class="menu-item {{ Request::is('admin/discount*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
                <a href="{{ url('admin/discount') }}" class="menu-link ">
                    <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                        <img src="{{ asset('dist/assets/media/icons/promo-code.png') }}"  >
                    </span>
                    <span class="menu-text">Promo code</span>
                </a>
            </li>
             @endif
             @if((new \App\Helpers)->check_permission(6, 1) == 1 )
             <li class="menu-item {{ Request::is('admin/inquiry*') ? 'menu-item-active' : '' }} " aria-haspopup="true">
             <a href="{{ url('admin/inquiry') }}" class="menu-link ">
                 <span class="svg-icon menu-icon"><!--begin::Svg Icon | path:assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                     <img src="{{ asset('dist/assets/media/icons/inquiry.png') }}"  >
                 </span>
                 <span class="menu-text">Inquiry/Contact Us</span>
             </a>
              </li> 
              @endif 
              @if((new \App\Helpers)->check_permission(7, 1) == 1 ||(new \App\Helpers)->check_permission(12, 1) == 1 || (new \App\Helpers)->check_permission(13, 1) == 1 || (new \App\Helpers)->check_permission(14, 1) == 1 ||(new \App\Helpers)->check_permission(15, 1) == 1 || (new \App\Helpers)->check_permission(16, 1) == 1)
              <li class="menu-item  menu-item-submenu 
             {{ Request::is('admin/user-report*') ? 'menu-item-open menu-item-here' : '' }} 
             {{ Request::is('admin/order-report*') ? 'menu-item-open menu-item-here' : '' }}
             {{ Request::is('admin/dpayment-report*') ? 'menu-item-open menu-item-here' : '' }}   
             {{ Request::is('admin/payment-report*') ? 'menu-item-open menu-item-here' : '' }} 

              {{ Request::is('admin/store-report*') ? 'menu-item-open menu-item-here' : '' }} 
              " aria-haspopup="true" data-menu-toggle="hover">
                 <a href="javascript:;" class="menu-link menu-toggle">
                     <span class="svg-icon menu-icon">
                         <img src="{{ asset('dist/assets/media/icons/reports.svg') }}" width="18px"  >
                     </span>
                     <span class="menu-text">Reports</span>
                     <i class="menu-arrow"></i>
                 </a>
                 <div class="menu-submenu ">
                     <i class="menu-arrow"></i>
                     <ul class="menu-subnav">
                         <li class="menu-item  menu-item-parent" aria-haspopup="true">
                             <span class="menu-link">
                                 <span class="menu-text">Reports</span>
                             </span>
                         </li>
                         @if((new \App\Helpers)->check_permission(12, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/user-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/user-report') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">User Report</span>
                             </a>
                         </li>
                         @endif  
                         @if((new \App\Helpers)->check_permission(13, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/order-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/order-report') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Order Report</span>
                             </a>
                         </li> 
                         @endif
                         @if((new \App\Helpers)->check_permission(14, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/dpayment-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/dpayment-report') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Driver Commision Report</span>
                             </a>
                         </li>
                         @endif
                       
                         @if((new \App\Helpers)->check_permission(15, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/payment-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/payment-report') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Payment History Report</span>
                             </a>
                         </li> 
                         @endif
                         @if((new \App\Helpers)->check_permission(16, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/store-report*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/store-report') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Restaurant/Grocery Report</span>
                             </a>
                         </li>
                         @endif  
                          
                     </ul>
                 </div>
             </li>
             @endif
             @if((new \App\Helpers)->check_permission(8, 1) == 1 ||(new \App\Helpers)->check_permission(17, 1) == 1 ||(new \App\Helpers)->check_permission(18, 1) == 1||(new \App\Helpers)->check_permission(19, 1) == 1||(new \App\Helpers)->check_permission(20, 1) == 1||(new \App\Helpers)->check_permission(21, 1) == 1||(new \App\Helpers)->check_permission(22, 1) == 1)
             <li class="menu-item  menu-item-submenu {{ Request::is('admin/deliveryrate*') ? 'menu-item-open menu-item-here' : '' }}{{ Request::is('admin/tip*') ? 'menu-item-open menu-item-here' : '' }} {{ Request::is('admin/label*') ? 'menu-item-open menu-item-here' : '' }}  {{ Request::is('admin/cms*') ? 'menu-item-open menu-item-here' : '' }}  {{ Request::is('admin/push*') ? 'menu-item-open menu-item-here' : '' }}  {{ Request::is('admin/emailtemplate*')  ? 'menu-item-open menu-item-here' : '' }} " aria-haspopup="true" data-menu-toggle="hover">
                 <a href="javascript:;" class="menu-link menu-toggle">
                     <span class="svg-icon menu-icon">
                         <i class="fa fa-gear" style="color:white;"></i>
                     </span>
                     <span class="menu-text">Settings</span>
                     <i class="menu-arrow"></i>
                 </a>
                 <div class="menu-submenu ">
                     <i class="menu-arrow"></i>
                     <ul class="menu-subnav">
                         <li class="menu-item  menu-item-parent" aria-haspopup="true">
                             <span class="menu-link">
                                 <span class="menu-text">Settings</span>
                             </span>
                         </li>
                         @if((new \App\Helpers)->check_permission(17, 1) == 1 )
                          <li class="menu-item {{ Request::is('admin/tip*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/tip') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Driver Tip Amount</span>
                             </a>
                         </li>
                         @endif
                         @if((new \App\Helpers)->check_permission(18, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/label*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/label') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Label</span>
                             </a>
                         </li>
                         @endif
                         @if((new \App\Helpers)->check_permission(19, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/deliveryrate*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/deliveryrate') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text"> Delivery Rate Management</span>
                             </a>
                         </li>
                         @endif         
                         @if((new \App\Helpers)->check_permission(20, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/cms*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/cms') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">CMS</span>
                             </a>
                         </li>   
                         @endif  
                         @if((new \App\Helpers)->check_permission(21, 1) == 1 )
                         <li class="menu-item {{ Request::is('admin/emailtemplate*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/emailtemplate') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Email Template</span>
                             </a>
                         </li>
                         @endif
                         @if((new \App\Helpers)->check_permission(22, 1) == 1 )
                          <li class="menu-item {{ Request::is('admin/push*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                             <a href="{{ url('admin/push') }}" class="menu-link ">
                                 <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                 <span class="menu-text">Push Notification</span>
                             </a>
                         </li> 
                         @endif   
                                
                     </ul>
                 </div>
             </li>
             @endif
            @endif
            @endif
             {{-- END SUBADMIN --}}
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 334px; right: 4px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 97px;"></div></div></div>
        <!--end::Menu Container-->
    </div>
    <!--end::Aside Menu-->
</div>