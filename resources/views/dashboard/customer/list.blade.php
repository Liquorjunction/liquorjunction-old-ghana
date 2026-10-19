@extends('dashboard.layouts.master')
@section('title','Customer | Admin Panel')
@section('content')
@include('sweetalert::alert')
<style type="text/css">
    
        .clear_button{
                margin-top: 30px;
        }
        .dker {
            padding-top: 0px !important;
            padding-bottom: 0px !important;
        }
        .manage-space {
            white-space: nowrap;
        }
        .status-dropdown {
            position: relative;
            display: inline-block;
        }

        .status-dropdown-button {
            /* background-color: #4CAF50;
                color: white;
                padding: 5px 10px;
                border: none;
                cursor: pointer; */
        }

        .status-dropdown-content {
            display: none;
            left: -20px;
            position: absolute;
            background-color: #f9f9f9;
            /* min-width: 160px; */
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .status-dropdown-item {
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            color: #333;
        }

        .status-dropdown-item:hover {
            background-color: #ddd;
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
                <h3>{{ __('backend.customer_managment') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / {{ __('backend.customer_managment') }}
                  
                </small>
            </div>
            <div class="padding general-setting">
            </div>

            <div class="card-body">
                <div class="form-group">
                    <div class="col-md-3">
                        <label>Start Date</label>
                        <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD-MM-YYYY" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>End Date</label>
                        <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD-MM-YYYY" readonly>
                    </div>                    
                   
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2 clear_button">
                        <label>&nbsp;</label>
                        <a onclick="location.reload();">
                            <button type="button" class="btn btn-danger mr-2 mb-5">Clear</button>
                        </a>
                    </div>
                  
                </div>
                  
                {{Form::open(['route'=>'customerUpdateAll','method'=>'post','id'=>'updateAll'])}}
                @php 
                $check_updation_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'update'); 
                $check_deletion_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'delete');
                $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'read');
                @endphp
                @if(isset($check_updation_permission) && $check_updation_permission==true || isset($check_deletion_permission) && $check_deletion_permission==true )
                <div class="bulk-action">
                    <div></div>
                        <div>
                            <select name="action" id="action" class="form-control c-select w-sm inline v-middle" required>
                                <option value="no">{{ __('backend.bulkAction') }}</option>
                                @if(isset($check_updation_permission) && $check_updation_permission==true)
                                <option value="1">{{ __('backend.activeSelected') }}</option>
                                <option value="0">{{ __('backend.blockSelected') }}</option>
                                @endif
                                @if(isset($check_deletion_permission) && $check_deletion_permission==true)
                                <option value="2">{{ __('backend.deleteSelected') }}</option>
                                @endif
                            </select>
                            <button type="submit" id="submit_all" class="btn white">{{ __('backend.apply') }}</button>
                        </div>
                </div> 
               @endif
                <div class="table-responsive" @if($check_updation_permission==false && $check_deletion_permission==false ) style="margin-top: 55px;" @endif >
                    <table class="table table-bordered m-a-0 manage-space" id="label">
                        <thead class="dker">
                        <tr>
                            <th  class="width20 dker no-sort">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <!-- <th>Id</th> -->
                            <th>{{ __('backend.customer_name') }}</th>
                            <th>{{ __('backend.customer_email') }}</th>
                            
                            <th>{{ __('backend.customer_phone') }}</th>
                            <th>Earned Points</th>
                            <th>Spent Points</th>
                            <th>Remaining Points</th>
                            <th>Browser Type </th>
                            <!-- <th>{{ __('backend.is_technician') }}</th> -->
                            <th>{{ __('backend.customer_join_date') }}</th>
                            <th>{{ __('backend.status') }}</th>
                            <th>{{ __('backend.options') }}</th>
                        </tr>
                        </thead>
                        <tbody id="bannerTable"> </tbody>
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
                        </div>
                        <div class="col-sm-6 text-right text-center-xs">
                           
                        </div>
                    </div>
                </footer>
                {{Form::close()}}
        </div>
    </div>
  
  <div class="modal fade" id="editCustomer" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content add-customer-data">
      </div>
    </div>
  </div>

  <div class="modal fade" id="showCustomer" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content show-customer-data">
      </div>
    </div>
  </div>

@endsection
@push("after-scripts")
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <script>
        $(document).on('click', '.edit-customer', function(e) {
            $("#editCustomer").modal({backdrop: false});
            var customer_id = $(this).attr('data-id');
            $(document).find('#editCustomer').find(".add-customer-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('customer.edit')}}",
               type: 'POST',
               data: 'customer_id='+customer_id,
               success: function (response) {
                    $(document).find('#editCustomer').find(".add-customer-data").append(response.html);
                    $(document).find('#editCustomer').modal('show');
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });

        

        $(document).on('click', '.status_active', function(e) {
        var customer_id = $(this).attr('data-id');
            // $(document).find('#editCustomer').find(".add-customer-data").empty();
        swal({ 
           title: "Deactive", 
           text: "Are you sure you want to deactive this customer?", 
           type: "warning", 
           showCancelButton: true, 
            confirmButtonColor: "#fbb516",
            cancelButtonColor: "rgb(36, 36, 36)",
           confirmButtonText: "Deactive", 
           closeOnConfirm: false
       }, 
       function() { 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('customer.status_active')}}",
            type: 'POST',
            data: 'customer_id='+customer_id,
            beforeSend: function(){
                                    // $(".loader").fadeIn();
                $('.loader').css("visibility", "visible");
            },
            success: function (response) {
                $('.loader').css("visibility", "visible");
                window.location.href = "{{ route('customer')}}";
            },
            error: function (response) {
             alert(response.responseText);
         }
     });
    }
    ); 
    });

    $(document).on('click', '.status_inactive', function(e) {
        var customer_id = $(this).attr('data-id');
            // $(document).find('#editCustomer').find(".add-customer-data").empty();
        swal({ 
           title: "Active", 
           text: "Are you sure you want to active this customer?", 
           type: "warning", 
           showCancelButton: true, 
            confirmButtonColor: "#fbb516",
            cancelButtonColor: "rgb(36, 36, 36)",
           confirmButtonText: "Active",
           closeOnConfirm: false
       }, 
       function() { 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('customer.status_inactive')}}",
            type: 'POST',
            data: 'customer_id='+customer_id,
            beforeSend: function(){
                                    // $(".loader").fadeIn();
                $('.loader').css("visibility", "visible");
            },
            success: function (response) {
                $('.loader').css("visibility", "visible");
                // return false;
                window.location.href = "{{ route('customer')}}";
            },
            error: function (response) {
             alert(response.responseText);
         }
     });
    }
    ); 
    });
 
 
    </script>
  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script>
    var show = "";
    var sort = "";
    var check = "";
</script>
    <?php
        if(isset($check_updation_permission) && $check_updation_permission==true || isset($check_deletion_permission) && $check_deletion_permission==true ) {
    ?>
    <script>
    var show = true;
    var check = true;
    var sort = [0,5,6];
    </script>
    <?php } else if($check_view_permission==true ) {
    ?>
    <script>
    var show = true;
    var check = false;
    var sort = [0,5,6];
    </script>
    <?php }else { ?>
    <script>
    var show = false;
    var check = false;
    var sort = [0, 5];
    </script>
    <?php }?>
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
        
              var action_url = "{!!  route('customer.anyData') !!} ";
            
              var dataTable = $('#label').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [
                        // { targets: [0, 4, 5, 6, 10], orderable: false }
                        { targets: [0, 10], orderable: false }
                    ],
                   ajax: {
                       url : action_url,
                       type: 'POST',
                       data: function(d) {
                            return $.extend({}, d, {
                                "startdate": $("#startdate").val().toLowerCase(),
                                "enddate": $("#enddate").val().toLowerCase(),

                                "status": $("#status").val().toLowerCase(),
                               
                            });
                        }
                   },
                   columns: [
                    {
                       data: 'checkbox',
                       orderable: false,
                       searchable: false,
                       visible: check

                   },
                   {
                       data: 'customer_name',
                       name: 'customer_name',
                       
                   },
                   {
                      data: 'customer_email',
                      name: 'customer_email',
                   },
                   {
                      data: 'customer_phone',
                      name: 'customer_phone',
                   },
                    {
                      data: 'earned_points',
                      name: 'earned_points',
                   },
                    {
                      data: 'spent_points',
                      name: 'spent_points',
                   },
                    {
                      data: 'remaining_points',
                      name: 'remaining_points',
                   },
                   {
                      data: 'browser_type',
                      name: 'browser_type',
                   },
                   // {
                   //    data: 'is_technician',
                   //    name: 'is_technician',
                   // },
                   {
                      data: 'customer_join_date',
                      name: 'customer_join_date',
                   },
                    {
                      data: 'status',
                      name: 'status',
                   },
                   {
                       data: 'options',
                       orderable: false,
                       searchable: false,
                       visible: show
                   }
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
                
                $('#status').change(function() {
                    $("#status").val($("#status").val().toLowerCase());
                    //$("#export_is_admin_approve").val($("#is_admin_approve").val().toLowerCase());
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
                       msg = "Are you sure you want to deactive this customer?";
                   }
                   else if(type == 1)
                   {
                       msg = "Are you sure you want to active this customer?";
                   }
                   else
                   {
                       msg = "Are you sure you want to delete this customer?";
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
               url: "{{ route('customerUpdateAll')}}",
               type: 'POST',
               headers: {'X-CSRF-TOKEN':  csrf },                        
               data: 'ids='+join_selected_values+'&status='+type,
               beforeSend: function(){
                                    // $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
               success: function (data) {
               
                   if (data.success == true) {
                    $('.loader').css("visibility", "hidden");
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
                     $('.loader').css("visibility", "hidden");
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
         $(document).on("click", function(event) {
        var $trigger = $(".status-dropdown");
        if ($trigger !== event.target && !$trigger.has(event.target).length) {
            $(".status-dropdown-content").hide();
        }
    });

</script>
@endpush
