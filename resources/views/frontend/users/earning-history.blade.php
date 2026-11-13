@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
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
                                                <li class="{{ request()->is('my-earning') ? 'active' : '' }}"><a href="{{route('myearning')}}">My Earning/History</a></li>
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
                            @if(isset($earning_history) && $earning_history != null) 
                            @foreach($earning_history as $es)                           
                           <div class="col-lg-9">
                                <div class="row align-items-center">
                                    <div class="col-sm-8">
                                        <div class="acc_label">
                                            <h3>My Account Balance</h3>
                                            <p>You can withdraw your amount</p>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="acc_bal">
                                            <span>{{isset($setting) ? $setting->currency_symbol : '$'}} {{$es->balance}}</span>    
                                            <a href="javascript:(void)" class="withdraw_link" data-bs-toggle="modal" data-bs-target="#withdrawrequest">Withdraw Request</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="withdraw_modal">
                                    <div class="modal fade" id="withdrawrequest_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLongTitle">Withdraw Request</h4>
                                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g opacity="0.3" clip-path="url(#clip0_1346_10214)">
                                                                <path d="M12.5029 24.999C5.67561 25.0317 0.0217524 19.4155 -0.000294086 12.5851C-0.00947247 10.9372 0.306788 9.30373 0.93033 7.77841C1.55387 6.25309 2.47242 4.86596 3.63326 3.69663C4.79411 2.52729 6.17439 1.59877 7.69489 0.964358C9.21539 0.329944 10.8462 0.00212378 12.4937 -0.000285882C19.321 -0.0394106 24.9777 5.59882 24.9997 12.472C25.0218 19.3714 19.4404 24.9663 12.5029 24.999ZM12.5029 10.8807C12.4218 10.8038 12.35 10.7384 12.2824 10.6729C11.2977 9.68842 10.313 8.70343 9.32822 7.71796C9.02526 7.4135 8.66824 7.26767 8.24225 7.36228C8.03991 7.3989 7.85219 7.49242 7.70105 7.63187C7.54992 7.77133 7.44162 7.95097 7.38884 8.14975C7.25371 8.60218 7.38883 8.98845 7.71171 9.31567C8.70167 10.3078 9.69258 11.2994 10.6844 12.2906C10.7506 12.3574 10.8146 12.4272 10.9291 12.546C10.8382 12.6017 10.7516 12.6642 10.6702 12.733C9.69495 13.7033 8.723 14.6755 7.75438 15.6496C7.36252 16.043 7.26438 16.5345 7.47702 16.987C7.81981 17.7182 8.74647 17.8626 9.34813 17.2672C10.331 16.2927 11.306 15.3103 12.2853 14.3322C12.3528 14.2646 12.424 14.2006 12.5043 14.1245C12.5797 14.1956 12.6466 14.2504 12.707 14.3115L14.4949 16.0999C14.9088 16.5139 15.3177 16.9336 15.7395 17.3398C16.0574 17.6464 16.4407 17.7488 16.8681 17.6158C17.0671 17.5614 17.2464 17.4512 17.385 17.2984C17.5235 17.1455 17.6156 16.9563 17.6504 16.7529C17.7414 16.314 17.575 15.9576 17.2649 15.6496C16.2845 14.6736 15.3057 13.695 14.3285 12.7138C14.2617 12.6477 14.1984 12.5773 14.0995 12.4734C14.1955 12.3951 14.2773 12.3404 14.3456 12.2721C15.3192 11.3011 16.2892 10.3258 17.2614 9.35551C17.5715 9.04749 17.7386 8.6911 17.6476 8.2529C17.6132 8.04926 17.5213 7.85971 17.3827 7.70667C17.244 7.55363 17.0645 7.4435 16.8653 7.38931C16.4158 7.24704 16.0247 7.37295 15.6968 7.70018C14.7045 8.69039 13.7133 9.68155 12.7234 10.6737C12.653 10.7405 12.5818 10.8045 12.5001 10.8807H12.5029Z" fill="#4E4E4E"></path>
                                                            </g>
                                                            <defs>
                                                            <clipPath id="clip0_1346_10214"><rect width="25" height="25" fill="white"></rect></clipPath>
                                                            </defs>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{route('request-amount')}}">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <span class="help-block" id="successMessage" style="display:none;">
                                                                        <span  style="color: green; display:none;" id="successMsg" class='validate'></span>
                                                                    </span>
                                                                    <span class="help-block" id="errorMessage" style="display:none;">
                                                                        <span  style="color: red; display:none;" id="errorMsg" class='validate'></span>
                                                                    </span>
                                                                    <input type="hidden" name="instructor_id" value="{{auth()->guard('main_user')->user()->id}}">
                                                                    <input type="text" placeholder="Enter Amount" name="amount" id="amount" onkeypress="return isNumber(event)" maxlength="9">
                                                                </div>
                                                                
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <button type="submit" class="withdraw_submit" id="submit_btn">Submit</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="earning_title">
                                    <h3>Total earning</h3>
                                    <span>{{isset($setting) ? $setting->currency_symbol : '$'}} {{isset($total_earning) ? $total_earning : '0.0'}}</span>
                                </div>
                                <div class="purchase_history_cover" id="normal_div">
                                    <h3>Purchase History</h3>
                                    <div class="purchase_history">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Name of class</th>
                                                    <th scope="col">Total number of purchase</th>
                                                    <th scope="col">Total amount</th>
                                                    <th scope="col">Commission</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($purchase_history->count())
                                                @foreach($purchase_history as $key => $ph)
                                                <?php $key++; ?>
                                                <tr>
                                                    <?php 
                                                    $newDate = date("d/m/Y", strtotime($ph->purchase_date));
                                                    ?>
                                                    <th scope="row">{{$key}}</th>
                                                    <td>{{$newDate}}</td>
                                                    <td>{{$ph->class_name}}</td>
                                                    <td>{{isset($ph->count_user_id) ? $ph->count_user_id : '0'}}</td>
                                                    <td><span>{{isset($setting) ? $setting->currency_symbol : '$'}} {{isset($ph->total_amount) ? $ph->total_amount : '0.0'}}</span></td>
                                                    <td>{{isset($setting) ? $setting->commission_in_per : '0'}}%</td>
                                                    <td><a href="{{route('myearningdetail')}}"><img src="{{ asset('assets/frontend/images/show_eye.svg') }}" width="20px" height="20px" style="margin-left: 15px;"></a></td>
                                                </tr>
                                                @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="2"> No Record Found </td>
                                                    </tr>
                                                @endif
                                               <!--  <tr>
                                                    <th scope="row">2</th>
                                                    <td>29/08/2022</td>
                                                    <td>Class Name</td>
                                                    <td>400</td>
                                                    <td><span>&#8381; 6000</span></td>
                                                    <td>12%</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">3</th>
                                                    <td>30/08/2022</td>
                                                    <td>Class Name</td>
                                                    <td>600</td>
                                                    <td><span>&#8381; 8000</span></td>
                                                    <td>8%</td>
                                                </tr> -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="purchase_history_cover" style="display: none;" id="detail_div">
                                    <h3>Purchase History Detail</h3>
                                    <div class="purchase_history">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Name of class</th>
                                                    <th scope="col">Total number of purchase</th>
                                                    <th scope="col">Instructor amount</th>
                                                    <th scope="col">Total amount</th>
                                                    <th scope="col">Commission</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($purchase_history->count())
                                                @foreach($purchase_history as $key => $ph)
                                                <?php $key++; ?>
                                                <tr>
                                                    <?php 
                                                    $newDate = date("d/m/Y", strtotime($ph->purchase_date));
                                                    ?>
                                                    <th scope="row">{{$key}}</th>
                                                    <td>{{$newDate}}</td>
                                                    <td>{{$ph->class_name}}</td>
                                                    <td>{{isset($ph->count_user_id) ? $ph->count_user_id : '0'}}</td>
                                                    <td><span>{{isset($setting) ? $setting->currency_symbol : '$'}} {{isset($ph->instructor_amount) ? $ph->instructor_amount : '0.0'}}</span></td>
                                                    <td><span>{{isset($setting) ? $setting->currency_symbol : '$'}} {{isset($ph->total_amount) ? $ph->total_amount : '0.0'}}</span></td>
                                                    <td>{{isset($setting) ? $setting->commission_in_per : '0'}}%</td>
                                                </tr>
                                                @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="2"> No Record Found </td>
                                                    </tr>
                                                @endif
                                               <!--  <tr>
                                                    <th scope="row">2</th>
                                                    <td>29/08/2022</td>
                                                    <td>Class Name</td>
                                                    <td>400</td>
                                                    <td><span>&#8381; 6000</span></td>
                                                    <td>12%</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">3</th>
                                                    <td>30/08/2022</td>
                                                    <td>Class Name</td>
                                                    <td>600</td>
                                                    <td><span>&#8381; 8000</span></td>
                                                    <td>8%</td>
                                                </tr> -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="withdraw_history_cover" id="withdraw_div">
                                    <h3>Withdraw History</h3>
                                    <div class="withdraw_history">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">No</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Amount</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($withdraw_history->count())
                                                @foreach($withdraw_history as $key => $wh)
                                                <?php $key++; ?>
                                                <tr>
                                                    <?php 
                                                    $newDate = date("d/m/Y", strtotime($wh->created_at));
                                                    ?>
                                                    <th scope="row">{{$key}}</th>
                                                    <td>{{$newDate}}</td>
                                                    <td><span>{{isset($setting) ? $setting->currency_symbol : '$'}} {{isset($wh->amount) ? $wh->amount : '0.0'}}</span></td>
                                                    @if($wh->request_status == 0)
                                                    <td><span class="blue">Requested</span></td>
                                                    @elseif($wh->request_status == 1)
                                                    <td><span class="green">Paid</span></td>
                                                    @else
                                                    <td><span class="red">Cancelled</span></td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="2"> No Record Found </td>
                                                    </tr>
                                                @endif
                                                <!-- <tr>
                                                    <th scope="row">2</th>
                                                    <td>29/08/2022</td>
                                                    <td><span>&#8381; 6000</span></td>
                                                    <td><span class="green">Paid</span></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">3</th>
                                                    <td>30/08/2022</td>
                                                    <td><span>&#8381; 8000</span></td>
                                                    <td><span class="red">Cancelled</span></td>
                                                </tr> -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endforeach 
                            @else
                            <div class="col-lg-9">
                                No Data Found
                            </div>                          
                            @endif
                        </div>
                    </div>
                </section>
                <!--My Account Page-->
        </div>
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

            $('#view_detail').click(function(){
                $("#detail_div").css("display", "block");
                $("#normal_div").css("display", "none");
                $("#withdraw_div").css("display", "none");
            });
//view_detail
             $("#submit_btn").click(function(e){

                e.preventDefault();

                var instructor_id = $("input[name=instructor_id]").val();
                var amount = $("input[name=amount]").val();
                var url = '{{ route('request-amount') }}';

                $.ajax({
                   url:url,
                   method:'POST',
                   dataType: 'json',
                   data:{
                          instructor_id:instructor_id,
                          amount:amount
                        },
                   success:function(response){
                      if(response.success){
                              $("span#successMessage").css("display", "block");
                              $("span#successMsg").css("display", "block");
                              $("span#successMsg").html("Your withdraw request will be proceed..!");
                              setTimeout(function() {
                                  $('#successMessage').fadeOut('fast');
                                }, 5000);
                              $('#amount').val('');
                              window.location.reload();
                      }else{
                              $("span#errorMessage").css("display", "block");
                              $("span#errorMsg").css("display", "block");
                              $("span#errorMsg").html("Your request amount is greater than your account balance..!");
                              setTimeout(function() {
                                  $('#errorMessage').fadeOut('fast');
                                }, 5000);
                              $('#amount').val('');
                      }
                   },
                   error:function(error){
                      
                       var erroJson = JSON.parse(error.responseText);
                          for (var err in erroJson) {
                            for (var errstr of erroJson[err])
                              $("span#errorMessage").css("display", "block");
                              $("span#errorMsg").css("display", "block");
                              $("span#errorMsg").html(errstr);
                              setTimeout(function() {
                                  $('#errorMessage').fadeOut('fast');
                                }, 5000);
                              $('#amount').val('');
                          }
                   }
                });  
            });

             function isNumber(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    return false;
                }
                return true;
            }
        </script>
@endsection
