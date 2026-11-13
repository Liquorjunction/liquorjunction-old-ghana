<div class="app-header white box-shadow navbar-md">
    <div class="navbar">
        <!-- Open side - Naviation on mobile -->
        <a data-toggle="modal" data-target="#aside" class="navbar-item pull-left hidden-lg-up">
            <i class="material-icons">&#xe5d2;</i>
        </a>

        <div class="navbar-item pull-left h5" ng-bind="$state.current.data.title" id="pageTitle"></div>

        <!-- navbar right -->
        <ul class="nav navbar-nav pull-right">
            {{--<li class="nav-item p-t p-b">
                <a class="btn btn-sm info marginTop2" href="{{ route('HomePage') }}" target="_blank"
                   title="{{ __('backend.sitePreview') }}">
                    <i class="material-icons">&#xe895;</i> {{ __('backend.sitePreview') }}
                </a>
            </li>--}}
            <?php
           // $alerts = count(Helper::webmailsAlerts()) + count(Helper::eventsAlerts());
            ?>
            {{-- @if($alerts >0)
                <li class="nav-item dropdown pos-stc-xs">
                    <a class="nav-link" href data-toggle="dropdown">
                        <i class="material-icons">&#xe7f5;</i>
                        @if($alerts >0)
                            <span class="label label-sm up warn">{{ $alerts }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu pull-right w-xl animated fadeInUp no-bg no-border no-shadow">
                        <div class="box dark">
                            <div class="box p-a scrollable maxHeight320">
                               
                            </div>
                        </div>
                    </div>
                </li>
            @endif --}}
            <li class="nav-item dropdown">
                <a class="nav-link clear" href data-toggle="dropdown">
                  <span class="avatar">
                      @if(@auth()->guard('main_user')->user()->profile !="")
                          @if(auth()->guard('main_user')->user()->user_type == 1)
                          <img src="{{ asset('uploads/users/'.auth()->guard('main_user')->user()->profile) }}" alt="{{ auth()->guard('main_user')->user()->name }}" style="vertical-align: middle; width:45px; height:38px; border-radius: 50%;border: 1px solid #ffcb64;" 
                               title="{{ Auth::user()->name }}">
                          @else
                          <img src="{{ asset('uploads/customer/'.auth()->guard('main_user')->user()->profile) }}" alt="{{ auth()->guard('main_user')->user()->name }}" style="vertical-align: middle; width:45px; height:38px; border-radius: 50%;border: 1px solid #ffcb64;" 
                               title="{{ auth()->guard('main_user')->user()->name }}">
                          @endif
                      @else
                          <img src="{{ asset('uploads/contacts/profile.jpg') }}" alt="{{ auth()->guard('main_user')->user()->name }}" style="vertical-align: middle; width:45px; height:38px; border-radius: 50%;border: 1px solid #ffcb64;"
                               title="{{ isset(auth()->guard('main_user')->user()->name) ? auth()->guard('main_user')->user()->name : '' }}">
                      @endif
                      <!-- <i class="on b-white bottom"></i> -->
                  </span>
                </a>
                <div class="dropdown-menu pull-right dropdown-menu-scale">
                   
                   
                    <a class="dropdown-item" href="{{ route('userswholesalerEdit',auth()->guard('main_user')->user()->id) }}"><span>{{ __('backend.profile') }}</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('wholesaler-change-password') }}"><span>Change Password</span></a>
                    <div class="dropdown-divider"></div>
                    <a id="logout" class="dropdown-item" href="{{ url('/logout') }}">Logout</a>

                    <form id="logout-form" action="{{ route('main-wholesaler-logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>

            <li class="nav-item hidden-md-up">
                <a class="nav-link" data-toggle="collapse" data-target="#collapse">
                    <i class="material-icons">&#xe5d4;</i>
                </a>
            </li>
            
        </ul>
        <!-- / navbar right -->

        <!-- navbar collapse -->
        <div class="collapse navbar-toggleable-sm" id="collapse">
           
        <!-- link and dropdown -->
            
            
            
            <!-- / -->
        </div>
        <!-- / navbar collapse -->
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
     $(document).on("click", "#logout", function(e) {
        // alert('hello')
            e.preventDefault();
            var link = $(this).attr("href");
            // alert(link)
            // return false;
            Swal.fire({
  title: 'Logout ?',
  text: "Are you sure you want to logout ?",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Yes'
}).then((result) => {
  if (result.isConfirmed) {
    $('#logout-form').submit();
  }
})
});
</script>
