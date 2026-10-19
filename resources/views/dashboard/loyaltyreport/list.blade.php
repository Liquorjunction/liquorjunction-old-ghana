@extends('dashboard.layouts.master')
@section('title','Loyalty Points Report | Admin Panel')
@section('content')
@include('sweetalert::alert')
<style type="text/css">
   .padding .box form .bulk-action{
    display: flex !important;
    justify-content: space-between !important;
    padding: 15px 25px 50px !important;
   }
   .clear_button{
        margin-top: 30px;
   }
   .manage-space {
        white-space: nowrap;
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
                <h3>Loyalty Points Report</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>Loyalty Points Report</span>
                </small>
            </div>

            <div class="box-tool">
                <ul class="nav">
                       
                        <li class="nav-item inline">
                                {{Form::open(['route'=>'export_loyaltyreport','method'=>'post','style="background :none !important;"','id'=>'export'])}}
             
                        <input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : ''}}" id="export_start_date">
                        <input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : ''}}" id="export_end_date">
                        <input type="hidden" name="customername" value="" id="customername">
                        <input type="hidden" name="customeremail" value="" id="customeremail">
                        <div class="col-sm-12">
                            <a style="height:35px; min-width: unset;" class="btn btn-fw primary export-form" href="javascript:void(0)">
                             Export CSV
                            </a>
                        </div>
                                {{Form::close()}}
                        </li>

                </ul>
            </div>

            
            <div class="card-body">
                <div class="form-group">
                    <div class="col-md-2">
                        <label>Start Date</label>
                        <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD/MM/YYYY">
                    </div>
                    <div class="col-md-2">
                        <label>End Date</label>
                        <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD/MM/YYYY">
                    </div>
                     <div class="col-md-3">
                        <label>Name</label>
                        <select name="namefilter" id="namefilter" class="form-control">
                            <option value="">Select</option>
                            @foreach($loyalty_report as $loyaltyData)
                                <option value="{{$loyaltyData->user_id}}">{{$loyaltyData->customer_name}}</option>
                            @endforeach
                        </select>   
                    </div>
                       <div class="col-md-3">
                        <label>Email</label>
                        <select name="emailfilter" id="emailfilter" class="form-control">
                            <option value="">Select</option>
                            @foreach($loyalty_report as $loyaltyData)
                                <option value="{{$loyaltyData->user_id}}">{{$loyaltyData->customer_email}}</option>
                            @endforeach
                        </select>   
                    </div>
                    <div class="col-md-2 clear_button">
                            <label>&nbsp;</label>
                        <a onclick="location.reload();">
                            <button type="button" class="btn btn-danger mr-2 mb-5">Clear</button>
                        </a>
                    </div>
                </div>
            </div>

                {{Form::open(['route'=>'customerUpdateAll','method'=>'post','id'=>'updateAll'])}}
                <div class="bulk-action">
                    <div></div>
                        
                  </div> 

                <div class="table-responsive">
                    <table class="table table-bordered m-a-0 manage-space" id="label">
                        <thead class="dker">
                        <tr>
                            <th id="">Order ID</th>
                            <th id="">Customer Name</th>
                            <th id="">Customer Email</th>
                            <th id="">Earned Points</th>
                            <th id="">Spent Points</th>
                            <th id="">Order Amount</th>
                            <th id="">Order Date</th>
                            <th id="">Order Status</th>
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
                        </div>

                      
                        <div class="col-sm-6 text-right text-center-xs">
                           
                        </div>
                    </div>
                </footer>
                {{Form::close()}}
        </div>
    </div>


@endsection
@push("after-scripts")

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
        
              var action_url = "{!!  route('loyaltyreport.anyData') !!} ";
            
              var dataTable = $('#label').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                       'bSortable': false,
                    }],
                   ajax: {
                       url : action_url,
                       type: 'POST',
                       data: function(d) {
                            return $.extend({}, d, {
                                   "startdate": $("#startdate").val().toLowerCase(),
                                   "enddate": $("#enddate").val().toLowerCase(),
                                   "namefilter": $("#namefilter").val().toLowerCase(),
                                   "emailfilter": $("#emailfilter").val().toLowerCase()
                            });
                        }
                   },
                   columns: [
                    {
                      data: 'order_id',
                      name: 'order_id',
                   },
                   {
                    data:'customer_name',
                    name:'customer_name',
                   },
                    {
                    data:'customer_email',
                    name:'customer_email',
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
                      data: 'order_amount',
                      name: 'order_amount',
                   },
                   {
                      data: 'order_date',
                      name: 'order_date',
                   },
                    {
                      data: 'order_status',
                      name: 'order_status',
                   }
                   ],
                   order: ['0', 'DESC']
               });

               $('#startdate, #enddate').change(function() {
                    $("#export_start_date").val($("#startdate").val().toLowerCase());
                    $("#export_pdf_start_date").val($("#startdate").val().toLowerCase());
                    $("#export_pdf_end_date").val($("#enddate").val().toLowerCase());
                    $("#export_end_date").val($("#enddate").val().toLowerCase());
                    
                    dataTable.draw();
                });

                $('#namefilter').change(function() {
                    $("#namefilter").val($("#namefilter").val().toLowerCase());
                    $("#customername").val($("#namefilter").val().toLowerCase());
                    dataTable.draw();
                });

                $('#emailfilter').change(function() {
                    $("#emailfilter").val($("#emailfilter").val().toLowerCase());
                    $("#customeremail").val($("#emailfilter").val().toLowerCase());
                    dataTable.draw();
                });
           }
        
        });


        $( document ).ready(function() {
            if($('.no-sort').hasClass('sorting_disabled')){
                $('.no-sort').removeClass('sorting_asc')
            }
        });

        $(".export-form").click(function () {
            $('#export').submit();          
        });

        $(".export-pdfform").click(function () {
            $('#exportpdf').submit();          
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
