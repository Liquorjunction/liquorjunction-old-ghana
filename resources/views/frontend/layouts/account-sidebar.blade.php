<?php $menu_link = basename($_SERVER['PHP_SELF']);?>
<ul class="account-sidebar">
    <li class="sidebar-item">
        <a href="{{route('my-account')}}" class="sidebar-link {{ in_array(\Request::route()->getName(), ['my-account','userchange-password','edit-profile']) ? 'active' : ' ' }}">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 11C13.3869 11 15.3214 9.15342 15.3214 6.875C15.3214 4.59658 13.3869 2.75 11 2.75C8.61309 2.75 6.67857 4.59658 6.67857 6.875C6.67857 9.15342 8.61309 11 11 11ZM9.45712 12.5469C6.13164 12.5469 3.4375 15.1186 3.4375 18.2929C3.4375 18.8214 3.88652 19.25 4.44021 19.25H17.5598C18.1135 19.25 18.5625 18.8214 18.5625 18.2929C18.5625 15.1186 15.8684 12.5469 12.5429 12.5469H9.45712Z" fill="#858584"/>
        </svg>
        {{@Helper::language('my_account_label')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('my-address')}}" class="sidebar-link {{ in_array(\Request::route()->getName(), ['my-address','edit-address','add-address']) ? 'active' : ' ' }}">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="location">
                <path id="Vector" d="M12.15 19.5513C13.895 17.3905 17.875 12.1536 17.875 9.21204C17.875 5.64445 14.9495 2.75 11.3438 2.75C7.73796 2.75 4.8125 5.64445 4.8125 9.21204C4.8125 12.1536 8.79248 17.3905 10.5375 19.5513C10.956 20.0662 11.7315 20.0662 12.15 19.5513ZM11.3438 11.366C10.143 11.366 9.16667 10.4001 9.16667 9.21204C9.16667 8.02396 10.143 7.05802 11.3438 7.05802C12.5445 7.05802 13.5208 8.02396 13.5208 9.21204C13.5208 10.4001 12.5445 11.366 11.3438 11.366Z" fill="#858584"/>
            </g>
        </svg>
        {{@Helper::language('my_address_label')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('myOrder')}}" class="sidebar-link {{ in_array(\Request::route()->getName(), ['myOrder','order-detail']) ? 'active' : ' ' }}">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="my order">
                <path id="Vector" d="M8.83929 5.82227C8.83929 4.63727 9.80823 3.67383 11 3.67383C12.1918 3.67383 13.1607 4.63727 13.1607 5.82227V7.43359H8.83929V5.82227ZM7.21875 7.43359H5.05804C4.16337 7.43359 3.4375 8.15533 3.4375 9.04492V16.0273C3.4375 17.8065 4.88923 19.25 6.67857 19.25H15.3214C17.1108 19.25 18.5625 17.8065 18.5625 16.0273V9.04492C18.5625 8.15533 17.8366 7.43359 16.942 7.43359H14.7813V5.82227C14.7813 3.74432 13.0898 2.0625 11 2.0625C8.91018 2.0625 7.21875 3.74432 7.21875 5.82227V7.43359ZM8.02902 10.6562C7.57999 10.6562 7.21875 10.2971 7.21875 9.85059C7.21875 9.40411 7.57999 9.04492 8.02902 9.04492C8.47804 9.04492 8.83929 9.40411 8.83929 9.85059C8.83929 10.2971 8.47804 10.6562 8.02902 10.6562ZM14.7813 9.85059C14.7813 10.2971 14.42 10.6562 13.971 10.6562C13.522 10.6562 13.1607 10.2971 13.1607 9.85059C13.1607 9.40411 13.522 9.04492 13.971 9.04492C14.42 9.04492 14.7813 9.40411 14.7813 9.85059Z" fill="#858584"/>
            </g>
        </svg>
        {{@Helper::language('my_orders')}}</a>
    </li>
      <li class="sidebar-item">
        <a href="{{ route('reward-points') }}" class="sidebar-link {{ in_array(\Request::route()->getName(), ['reward-points']) ? 'active' : '' }}">
           <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
             <path d="M12 2l2.89 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 7.11-1.01L12 2z" fill="#858584"/>
            </svg>
            My Reward Points
        </a>
    </li>
    <li class="sidebar-item">
        <a href="{{route('favorite-list')}}" class="sidebar-link {{ in_array(\Request::route()->getName(), ['favorite-list']) ? 'active' : ' ' }}">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="icon_fill like">
                <path id="Vector" d="M4.28398 12.6404L10.1073 18.2031C10.349 18.4339 10.6681 18.5625 11 18.5625C11.3319 18.5625 11.651 18.4339 11.8927 18.2031L17.716 12.6404C18.6957 11.7073 19.25 10.3982 19.25 9.02981V8.83856C19.25 6.5337 17.6226 4.56847 15.4021 4.18927C13.9326 3.93867 12.4373 4.42998 11.3867 5.50492L11 5.9006L10.6133 5.50492C9.5627 4.42998 8.06738 3.93867 6.59785 4.18927C4.37744 4.56847 2.75 6.5337 2.75 8.83856V9.02981C2.75 10.3982 3.3043 11.7073 4.28398 12.6404Z" fill="#858584"/>
            </g>
        </svg>
        {{@Helper::language('my_favorite_list_label')}}</a>
    </li>
    <li class="sidebar-item">
        <a href="javascript:void(0)" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="icon_logout">
                <path id="Vector" d="M12.1 3.17137C12.1 2.55803 11.6084 2.0625 11 2.0625C10.3916 2.0625 9.9 2.55803 9.9 3.17137V10.9335C9.9 11.5468 10.3916 12.0423 11 12.0423C11.6084 12.0423 12.1 11.5468 12.1 10.9335V3.17137ZM7.13281 6.24156C7.60031 5.84999 7.66219 5.15001 7.27375 4.67874C6.88531 4.20747 6.19094 4.1451 5.72344 4.53667C3.90844 6.06137 2.75 8.36227 2.75 10.9335C2.75 15.5249 6.44531 19.25 11 19.25C15.5547 19.25 19.25 15.5249 19.25 10.9335C19.25 8.36227 18.0881 6.06137 16.2731 4.53667C15.8056 4.1451 15.1112 4.21094 14.7228 4.67874C14.3344 5.14655 14.3997 5.84999 14.8638 6.24156C16.2009 7.36082 17.0466 9.04839 17.0466 10.9335C17.0466 14.3017 14.3378 17.0323 10.9966 17.0323C7.65531 17.0323 4.94656 14.3017 4.94656 10.9335C4.94656 9.04839 5.79562 7.36082 7.12937 6.24156H7.13281Z" fill="#858584"/>
            </g>
        </svg>
        {{@Helper::language('logout_label_web')}}</a>
    </li>
</ul>
<div class="modal fade remove-item-modal p-0 show" id="logoutModal" tabindex="-1" aria-modal="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                 <input type="hidden" name="address_id" id="address_id" value="">
                 <div class="modal-body">
                     <h5 class="mb-3">{{@Helper::language('logout_label_web')}}</h5>
                     <p class="body-large mb-4">{{@Helper::language('are_you_sure_you_want_to_logout')}}</p>
                     <div class="d-flex justify-content-between gap-2">
                         <button type="submit" class="small-common-btn common-border-btn hvr-radial-out-black w-100" data-bs-dismiss="modal" aria-label="Close">{{@Helper::language('cancel_btn')}}</button>
                         <button type="submit" class="small-common-btn hvr-radial-out solid-button w-100" onclick="return logoutUser()">{{@Helper::language('yes')}}</button>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
     <!-- Logout -->
{{-- <div class="modal fade logout-modal" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="icon-cross-black"></i></button>
            </div>
            <div class="modal-body">
                <span class="modal-icon"><i class="icon-caution"></i></span>
                <h5 class="text-center">{{@Helper::language('are_you_sure_you_want_to_logout')}}</h5>
                <a data-bs-toggle="modal" href="javascript::void(0)"  onclick="return logoutUser()" role="button" aria-controls="" class="solid-button w-100 red-background" data-bs-dismiss="modal" aria-label="Close">{{@Helper::language('logout_label_web')}}</a>
            </div>
        </div>
    </div>
</div> --}}
<!-- Logout End -->
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
 <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
 <script type="text/javascript">
     function logoutUser() {
         // alert('ff')
         var data = 1;
         action_url = "{{ route('user-logout') }}";
         var csrf = "{{ csrf_token() }}";

         $.ajax({
             url: action_url,
             data: {
                 'data': data
             },
             headers: {
                 'X-CSRF-TOKEN': csrf
             },
             type: "POST",

             beforeSend: function() {
                 $(".loader").fadeIn();
                 $('.loader').css("visibility", "visible");
             },
             success: function(response) {
                 // return false;
                 var url = "{{route('frontend.home')}}";
                 window.location.href = url;
                 // location.reload();
             },
         });
     }
 </script>