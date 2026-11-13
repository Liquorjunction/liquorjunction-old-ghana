@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
        <div class="site_content_cover">
            <!--Page Title-->
                <div class="page_title">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1>My Earning</h1>
                            </div>
                        </div>
                    </div>
                </div>
            <!--Page Title-->
            <!--Breadcrumb-->
                <div class="breadcrumb_cover">
                    <div class="container">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><img src="{{ asset('assets/frontend/images/breadcrumb_od.svg') }}" alt="breadcrumb_od" /></a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Earning</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            <!--Breadcrumb-->    

            <!--My Favourite Page-->
                <section class="my_account">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="account_sidebar">
                                    <ul>
                                        @if(isset(auth()->guard('main_user')->user()->id) && auth()->guard('main_user')->user()->id > 0)
                                            @if(auth()->guard('main_user')->user()->user_type == 3)
                                                <li class="{{ request()->is('profile') ? 'active' : '' }}"><a href="{{route('websiteprofile')}}">Profile </a></li>
                                                <li class="{{ request()->is('my-library') ? 'active' : '' }}"><a href="{{route('mylibrary')}}">My Library </a></li>
                                                <li class="{{ request()->is('my-class') ? 'active' : '' }}"><a href="{{route('my-class')}}">My Classes</a></li>
                                                <li class="active"><a href="{{route('myearning')}}">My Earning/History</a></li>
                                                <li class="{{ request()->is('my-favourite') ? 'active' : '' }}"><a href="{{route('my-favourite')}}">My Favourite</a></li>
                                                <li class="{{ request()->is('change-password') ? 'active' : '' }}"><a href="{{route('user-change-password')}}">Change Password</a></li>
                                                <li><a id="logout" href="{{ url('/logout') }}">Logout</a><form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                                </form></li>
                                            @else
                                                <li class="{{ request()->is('profile') ? 'active' : '' }}"><a href="{{route('websiteprofile')}}">Profile </a></li>
                                                <li class="{{ request()->is('my-library') ? 'active' : '' }}"><a href="{{route('mylibrary')}}">My Library </a></li>
                                                <li class="{{ request()->is('my-favourite') ? 'active' : '' }}"><a href="{{route('my-favourite')}}">My Favourite</a></li>
                                                <li class="{{ request()->is('change-password') ? 'active' : '' }}"><a href="{{route('user-change-password')}}">Change Password</a></li>
                                                <li><a id="logout" href="{{ url('/logout') }}">Logout</a><form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                                </form></li>
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                            </div>                           
                            <div class="col-lg-9">
                                <div class="search_row">
                                    <a href="{{route('myearning')}}" class="common_btn">Back</a>
                                </div>
                                <div class="purchase_history_cover">
                                    <h3>Purchase History Detail</h3>
                                    <div class="purchase_history">
                                        <table class="table table-bordered" id="earning_detail">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Name of class</th>
                                                    <th scope="col">Total no. of purchase</th>
                                                    <th scope="col">Instructor amount</th>
                                                    <th scope="col">Total amount</th>
                                                    <th scope="col">Commission</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--My Account Page-->
        </div>
        <script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>
        <script type="text/javascript">
            $(document).on("click", "#logout", function(e) {
                        // alert('hello')
                            e.preventDefault();
                            var link = $(this).attr("href");
                            // alert(link)
                            // return false;
                            Swal.fire({
                              title: 'Logout ?',
                              text: "Are You Sure You Want to logout ?",
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

            $.ajaxSetup({
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   }
            });

           load_data();

           function  load_data()
           {
          
              var action_url = "{{  route('myearningdetail.anydata') }}";
            
               
                $('#earning_detail').DataTable({
                    searching: false, 
                    paging: true, 
                    info: false,
                    responsive: true,
                    bLengthChange: false,
                    processing: true,
                    serverSide: true,
                    columnDefs: [{
                      "orderable": false, 
                      "targets": [0,1,2,3,4,5,6]
                    }],
                    ajax: {
                       url : action_url,
                       type: 'POST',
                       data:{
                        
                       }
                    },
                   columns: [
                   
                   {
                      data: 'no',
                      name: 'no',
                      visible:false
                   },
                   {
                      data: 'date',
                      name: 'date',
                   },
                   {
                       data: 'class_name',
                       name: 'class_name',
                   },
                   {
                       data: 'purchase_count',
                       name: 'purchase_count',
                   },
                   {
                       data: 'instructor_amount',
                       name: 'instructor_amount',
                   },
                   {
                       data: 'total_amount',
                       name: 'total_amount',
                   },
                   {
                       data: 'commission',
                       name: 'commission',
                   }
                   ],
                   order : ['0', 'DESC'],
                });
           }
        </script>
@endsection
