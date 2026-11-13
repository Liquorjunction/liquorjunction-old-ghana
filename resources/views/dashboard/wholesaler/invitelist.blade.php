@extends('dashboard.layouts.master')
@section('title','Wholesaler Invite | Admin Panel')
@section('content')
   <style type="text/css">
       .p-y{
            margin-top: -66px !important;
        }

        .padding .box form{
            padding: 57px 0 0 !important;
        }
   </style>
   <style type="text/css">
   /*.card-body2{
     margin: -35px;
    margin-left: 10px;
   }*/
   .clear_button{
        margin-top: 30px;
   }
</style>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" /> 
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <div class="loader" id="loader"></div> 
    <div class="padding website-label">
        <div class="success_message" style="margin-bottom: 10px;"></div>
        <div id="success_file_popup" style="margin-bottom: 10px;"></div>
        <div class="box">
           
            <div class="box-header dker">
                <h3>{{ __('backend.wholesaler') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>{{ __('backend.wholesaler') }}</span>
                </small>
            </div>
            <div class="padding general-setting">
        <div class="">
            <div class="col-sm-12 col-lg-12">
                <div class="p-y">
            <div class="nav-active-border left b-primary">
                        <ul class="nav nav-sm">
            <li class="nav-item">
                                <a class="nav-link block {{ (\Request::route()->getName()== 'wholesaler') ? 'active' : ' ' }}"
                                   href="{{ route('wholesaler') }}"
                                   
                                   >
                                    {{ __('backend.WholesalerList') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link block {{ (\Request::route()->getName()== 'wholesalerinvite') ? 'active' : ' ' }}"
                                   href="{{ route('wholesalerinvite') }}"
                                   
                                   >{{ __('backend.InviteWholesalerList') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <div class="box-tool">
                <ul class="nav">
                       
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" data-toggle="modal" data-target="#myModal">
                                    <i class="material-icons">&#xe02e;</i>
                                    &nbsp; {{ __('backend.WholesalerInviteLik') }}
                                </a>
                            </li>
                       
                </ul>
            </div>

            <div class="card-body">
           <div class="form-group">
              <div class="col-md-3">
                <label>Start Date</label>
                <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD/MM/YYYY">
              </div>
              <div class="col-md-3">
                <label>End Date</label>
                <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD/MM/YYYY">
              </div>
                <div class="col-md-2 clear_button">
                    <label>&nbsp;</label>
                <a onclick="location.reload();">
                    <button type="button" class="btn btn-danger mr-2 mb-5">Clear</button>
                </a>
               </div>
            </div>
        </div>

                {{Form::open(['route'=>'wholesalerUpdateAll','method'=>'post','id'=>'updateAll'])}}
                <div class="bulk-action">
                    <div></div>
                        
                  </div> 

                <div class="table-responsive">
                    <table class="table table-bordered m-a-0" id="label">
                        <thead class="dker">
                        <tr>
                            <th  class="width20 dker no-sort">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <!-- <th>Id</th> -->
                            <!-- <th>{{ __('backend.wholesaler_name') }}</th> -->
                            <th>{{ __('backend.wholesaler_email') }}</th>
                            <!-- <th>{{ __('backend.wholesaler_phone') }}</th> -->
                            <th>{{ __('backend.customer_join_date') }}</th>
                            <!-- <th>{{ __('backend.status') }}</th> -->
                            <!-- <th>{{ __('backend.options') }}</th> -->
                        </tr>
                        </thead>
                        <tbody id="bannerTable">

                      

                        </tbody>
                    </table>

                </div>
                <footer class="dker p-a">
                    <div class="row">
                        <div class="col-sm-3 hidden-xs">
                            <!-- .modal -->
                            <div id="m-all" class="modal fade" data-backdrop="true">
                                <div class="modal-dialog" id="animate">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('backend.confirmation') }}</h5>
                                        </div>
                                        <div class="modal-body text-center p-lg">
                                            <p>
                                                {{ __('backend.confirmationDeleteMsg') }}
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn dark-white p-x-md"
                                                    data-dismiss="modal">{{ __('backend.no') }}</button>
                                            <button type="submit"
                                                    class="btn danger p-x-md">{{ __('backend.yes') }}</button>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div>
                            </div>
                            <!-- / .modal -->
                           
                                {{-- <select name="action" id="action" class="form-control c-select w-sm inline v-middle"
                                        required>
                                    <option value="">{{ __('backend.bulkAction') }}</option>
                                    <option value="activate">{{ __('backend.activeSelected') }}</option>
                                    <option value="block">{{ __('backend.blockSelected') }}</option>
                                    <option value="delete">{{ __('backend.deleteSelected') }}</option>
                                </select>
                                <button type="submit" id="submit_all"
                                        class="btn white">{{ __('backend.apply') }}</button>
                                <button id="submit_show_msg" class="btn white" data-toggle="modal"
                                        style="display: none"
                                        data-target="#m-all" ui-toggle-class="bounce"
                                        ui-target="#animate">{{ __('backend.apply') }}
                                </button> --}}
                           
                        </div>

                      
                        <div class="col-sm-6 text-right text-center-xs">
                           
                        </div>
                    </div>
                </footer>
                {{Form::close()}}
        </div>
    </div>

    <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.wholesaler_invite') }}</h4>
        </div>
        <form class="cmxform" id="WholesalerForm" method="post" action="" autocomplete="off">
        <div class="modal-body">

           <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.email') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{old('email')}}">
                            <!-- <textarea type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{old('email')}}">{{old('email')}}</textarea> -->
                            <span class="help-block" id="errorMessageEmail" style="display:none">
                                <span  style="color: red;display: none;" id="errorMsgEmail" class='validate'></span>
                            </span>
                        </div>
                    </div>
                    
                    
        </div>
        <div class="modal-footer">

          <button type="submit" class="btn btn-default btn btn-primary">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {{Form::close()}}
      </div>
      
    </div>
  </div>
  
  <div class="modal fade" id="editWholesaler" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content add-wholesaler-data">
      </div>
    </div>
  </div>

  <div class="modal fade" id="showWholesaler" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content show-wholesaler-data">
      </div>
    </div>
  </div>

@endsection
@push("after-scripts")
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

    <script>
        $(document).on('click', '.edit-wholesaler', function(e) {
            var customer_id = $(this).attr('data-id');
            $(document).find('#editWholesaler').find(".add-wholesaler-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('wholesaler.edit')}}",
               type: 'POST',
               data: 'customer_id='+customer_id,
               success: function (response) {
                    $(document).find('#editWholesaler').find(".add-wholesaler-data").append(response.html);
                    $(document).find('#editWholesaler').modal('show');
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });

         $(document).on('click', '.show-wholesaler', function(e) {
            var customer_id = $(this).attr('data-id');
            $(document).find('#showWholesaler').find(".show-wholesaler-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('wholesaler.show')}}",
               type: 'POST',
               data: 'customer_id='+customer_id,
               success: function (response) {
                    $(document).find('#showWholesaler').find(".show-wholesaler-data").append(response.html);
                    $(document).find('#showWholesaler').modal('show');
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });

        $(document).on('click', '.status_active', function(e) {
            var customer_id = $(this).attr('data-id');
            // $(document).find('#editCustomer').find(".add-customer-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('wholesaler.status_active')}}",
               type: 'POST',
               data: 'customer_id='+customer_id,
               beforeSend: function(){
                                    // $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
               success: function (response) {
                $('.loader').css("visibility", "visible");
                    window.location.href = "{{ route('wholesaler')}}";
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });

         $(document).on('click', '.status_inactive', function(e) {
            var customer_id = $(this).attr('data-id');
            // $(document).find('#editCustomer').find(".add-customer-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('wholesaler.status_inactive')}}",
               type: 'POST',
               data: 'customer_id='+customer_id,
               beforeSend: function(){
                                    // $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
               success: function (response) {
                $('.loader').css("visibility", "visible");
                // return false;
                    window.location.href = "{{ route('wholesaler')}}";
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });

         $(document).ready(function () {
 
            $("#WholesalerForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    email: {
                    required: true,
                    email: true
                    },
                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    email: {
                        required: "Email field is required",
                        email: "You must enter a valid email"
                    },
                },
                submitHandler: function(){
                    var form_data = new FormData($('#WholesalerForm')[0]);
                    action_url = "{{ route('wholesaler.invitelink') }}";
                    var csrf = "{{ csrf_token() }}";
                    $.ajax({
                            url: action_url,
                            data: form_data,
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            processData: false,
                            contentType: false,
                            type: "POST",
                            dataType: 'json',
                             beforeSend: function(){
                                    // $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
                            success: function(data){
                                // return false;
                                // $(el).parents('.cart-product-box-content').find('b[name=price]').text(fix_price*text);
                               // return false;
                                if (data.success) {
                                    $('.loader').css("visibility", "visible");
                                    window.location.href = "{{ route('wholesaler')}}";
                                }
                            },
                            error: function (errors) {
                                   // alert(errors);
                                    // $('.loader').css("visibility", "none");
                                 var erroJson = JSON.parse(errors.responseText);
                                 console.log(erroJson);
                                   for (var err in erroJson) {
                            for (var errstr of erroJson[err])
                                 // console.log(err);
                                  if (err == "email") {
                                      $("span#errorMessageEmail").css("display", "block");
                                      $("span#errorMsgEmail").css("display", "block");
                                      $("span#errorMsgEmail").html(errstr);
                            }else{
                             
                          }
                              }
                               }
                        });
                }
            });
        });
    </script>
  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        function isNumberKey(evt){ 
    //var e = evt || window.event;
    var keyCode = (evt.which) ? evt.which : evt.keyCode;
    if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
         
        return false;
            return true;
    
    }
        $(function() {
           $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });
           load_data();
           function load_data(startdate, enddate) 
           {
        
              var action_url = "{!!  route('wholesalerinvite.anyData') !!} ";
            
              var dataTable = $('#label').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                       'bSortable': false,
                       'aTargets': [0]
                    }],
                   ajax: {
                       url : action_url,
                       type: 'POST',
                       data: function(d) {
                            return $.extend({}, d, {
                                "startdate": $("#startdate").val().toLowerCase(),
                                "enddate": $("#enddate").val().toLowerCase(),
                               
                            });
                        }
                   },
                   columns: [
                    {
                       data: 'checkbox',
                        orderable: false,
                       searchable: false

                   },
                   {
                      data: 'email',
                      name: 'email',
                   },
                   {
                      data: 'customer_join_date',
                      name: 'customer_join_date',
                   },
                   // {
                   //     data: 'options',
                   //     orderable: false,
                   //     searchable: false
                   // }
                   ],
                   order: ['0', 'DESC']
               });

               $('#startdate, #enddate').change(function() {
                    {{-- let start_date = $('#startdate').val();
                    let end_date = $('#enddate').val();
                    load_data(start_date, end_date); --}}

                    $("#export_start_date").val($("#startdate").val().toLowerCase());
                    $("#export_end_date").val($("#enddate").val().toLowerCase());
                    
                    dataTable.draw();
                });
           }
        
        });


        $( document ).ready(function() {
            if($('.no-sort').hasClass('sorting_disabled')){
                $('.no-sort').removeClass('sorting_asc')
            }
        });


        $("#submit_show_msg").click(function () {
            var numberOfChecked = $('input:checkbox:checked').length;
            if(numberOfChecked == ''){
                alert("Please select row.");
                return false;
            } 
        });

        $("#startdate").datepicker({
                changeMonth: true,
                endDate: '+0d',
                changeYear: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                orientation: "bottom",
                autoclose: true
            }).on('changeDate', function(selected) {
                var minDate = new Date(selected.date.valueOf());
                $('#enddate').datepicker('setStartDate', minDate);
            });

            $("#enddate").datepicker({
                changeMonth: true,
                endDate: '+0d',
                changeYear: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                orientation: "bottom",
                autoclose: true
            }).on('changeDate', function(selected) {
                var minDate = new Date(selected.date.valueOf());
                $('#startdate').datepicker('setEndDate', minDate);
            });

        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        


        $("#filter_btn").click(function () {
            $("#filter_div").slideToggle();
        });

        $("#find_q").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#doctorTypeTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $(document).on('submit', '#updateAll', function(e) {
            e.preventDefault();
            var allVals = [];  
            var check = false;

            var select_row = "{{ __('backend.select_row') }}";
            var select_status = "{{ __('backend.select_status') }}";
            
            var type = $(document).find('#action').val();
            if(type == 'no')
            {
                $(document).find('#alert_confirm').modal('show'); 
                $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_status); 
              
            }
            else
            {
               $(".has-value:checked").each(function() { 
                var idvalue = $(this).attr('data-id');
                if (typeof idvalue === "undefined") {

                }
                else
                {
                    allVals.push(idvalue);
                }
                });  

                if(allVals.length <=0)  
                {  
                   $(document).find('#alert_confirm').modal('show'); 
                   $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_row); 
                }  
                else 
                {  
                   var msg = "";
                   if(type == 0)
                   {
                       msg = "Are you sure you want to deactive this wholesaler?";
                   }
                   else if(type == 1)
                   {
                       msg = "Are you sure you want to active this wholesaler?";
                   }
                   else
                   {
                       msg = "Are you sure you want to delete this wholesaler?";
                   }

                   $(document).find('#default_confirm').modal('show');
                   $(document).find('#default_confirm').find('.dynamic_message').text(msg);
                    var join_selected_values = allVals.join(","); 
                   $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
                   $(document).find('#default_confirm').find('.checkbox_type').val(type);
                   
                }
            
           }  
        });
        $(document).on('click', '.delete-school', function(e) {
            e.preventDefault();
            var package_id = $(this).attr('data-id');
            var allVals = [];
            allVals.push(package_id);
            var type = 2;
            var msg = "Are you sure you want to delete?";

            $(document).find('#default_confirm').modal('show');
            $(document).find('#default_confirm').find('.dynamic_message').text(msg);
            var join_selected_values = allVals.join(","); 
            $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
            $(document).find('#default_confirm').find('.checkbox_type').val(type);
        });

        $(document).on('click','.yes_click',function(e)
        {
           var  join_selected_values = $(document).find('#default_confirm').find('.checkbox_data').val();
           var  type = $(document).find('#default_confirm').find('.checkbox_type').val();
           var csrf = "{{csrf_token()}}";
            ajaxUpdateAll(csrf,join_selected_values,type,action);
        });
       
        function ajaxUpdateAll(csrf,join_selected_values,type)
        {
            $.ajax({
               url: "{{ route('wholesalerUpdateAll')}}",
               type: 'POST',
               headers: {'X-CSRF-TOKEN':  csrf },                        
               data: 'ids='+join_selected_values+'&status='+type,
               success: function (data) {
               
                   if (data.success == true) {
                       $('#success_file_popup').append( messages('alert-success', data.msg));
                       setTimeout(function() {
                       $('#success_file_popup').empty();
                       }, 5000);
                       
                       
                        $(document).find('#default_confirm').modal('hide');
                        var tabe = $('#label').DataTable();
                        $(document).find('#action').prop('selectedIndex',0);
                        tabe.ajax.reload(null, false);
                        $("#checkAll").prop('checked', false);

                   } else {
                       $('#success_file_popup').append( messages('alert-danger', data.error));

                       setTimeout(function() {
                       $('#success_file_popup').empty();
                       }, 5000);
                   }
               },
               error: function (data) {
                   alert(data.responseText);
               }
            });
        }
        $("#action").change(function () {
            if (this.value == "delete") {
                /*var numberOfChecked = $('input:checkbox:checked').length;
                if(numberOfChecked == ''){
                    alert("Please select row.");
                    return false;
                } */
                $("#submit_all").css("display", "none");
                $("#submit_show_msg").css("display", "inline-block");
            } else {
                $("#submit_all").css("display", "inline-block");
                $("#submit_show_msg").css("display", "none");
            }
        });
         function messages(classname, msg)
        {
           return '<div class="alert '+classname+' m-b-0"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'  + msg +'</div>';
        }
        setTimeout(function() {
        $('#topmsgall').hide();
         }, 5000);
    </script>
@endpush
