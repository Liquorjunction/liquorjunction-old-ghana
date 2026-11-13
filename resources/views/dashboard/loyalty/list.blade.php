@extends('dashboard.layouts.master')
@section('title','Loyalty | Admin Panel')
@section('content')
@include('sweetalert::alert')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" /> 
    <div class="loader" id="loader"></div> 
    <div class="padding website-label">
        <div class="success_message" style="margin-bottom: 10px;"></div>
        <div id="success_file_popup" style="margin-bottom: 10px;"></div>
        <div class="box">
           
            <div class="box-header dker">
                <h3>{{ __('backend.loyalty') }} Points</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>{{ __('backend.loyalty') }} Points</span>
                </small>
            </div>

            <div class="box-tool">
                <ul class="nav">
                       
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" data-toggle="modal" data-target="#myModal">
                                    <i class="material-icons">&#xe02e;</i>
                                    &nbsp; {{ __('backend.NewLoyalty') }}
                                </a>
                            </li>
                       
                </ul>
            </div>
                {{Form::open(['route'=>'loyaltyUpdateAll','method'=>'post','id'=>'updateAll'])}}
                <div class="bulk-action">
                    <div></div>
                        <div>
                        <select name="action" id="action" class="form-control c-select w-sm inline v-middle"
                                required>
                            <option value="no">{{ __('backend.bulkAction') }}</option>
                            <option value="1">{{ __('backend.activeSelected') }}</option>
                            <option value="0">{{ __('backend.blockSelected') }}</option>
                            <option value="2">{{ __('backend.deleteSelected') }}</option>
                        </select>
                        <button type="submit" id="submit_all"
                                class="btn white">{{ __('backend.apply') }}</button>
                    </div>
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
                            <th id="">{{ __('backend.minimum_purchase_amount') }}</th>
                            <th>Loyalty Percentage(%)</th>
                            <th>Redeem Limit</th>
                            <th>Points Redeem Rate</th>
                            <th>Value for Points(GH₵)</th>
                            <th>Max Redeem Percentage(%) </th>
                            <th>Created On</th>
                            <th>{{ __('backend.status') }}</th>
                            <th id="option_width">{{ __('backend.options') }}</th>
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

     <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.AddLoyalty') }}</h4>
        </div>
        <form class="cmxform" id="loyaltyForm" method="post" action="" autocomplete="off">
        <div class="modal-body">

            <div class="form-group row">
                <label class="col-sm-5 form-control-label">{!!  __('backend.minimum_purchase_amount') !!} <span class="valid_field">*</span></label>
                <div class="col-sm-7">
                    <input type="text" name="minimum_purchase_amount" onkeypress="return isNumberBlock(event)" id="minimum_purchase_amount" class="form-control"  placeholder="Minimum Purchase Amount" value="{{old('minimum_purchase_amount')}}">
                    
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-5 form-control-label" style="display: flex;">Loyalty Percentage(%) <span class="valid_field">*</span></label>
                <div class="col-sm-7">
                    <input type="text" name="loyalty_percentage" id="loyalty_percentage" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="e.g. 25 for 25%" value="{{old('loyalty_percentage')}}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-5 form-control-label">
                   Redeem Limit <span class="valid_field">*</span>
                </label>
                <div class="col-sm-7">
                    <input type="text" name="maximum_points" id="maximum_points" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="200 GHS" value="{{ old('maximum_points') }}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-5 form-control-label">
                    Points Redeem Rate <span class="valid_field">*</span>
                </label>
                <div class="col-sm-7">
                    <input type="text" name="points_per_ghs" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="e.g. 50 = 2 GHS" value="{{ old('points_per_ghs') }}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-5 form-control-label">
                    Value for Points(GH₵) <span class="valid_field">*</span>
                </label>
                <div class="col-sm-7">
                    <input type="text" name="redeem_ghs_value" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="e.g. 2 for 50 points = 2 GHS" value="{{ old('redeem_ghs_value') }}">
                </div>
            </div>


            <div class="form-group row">
                <label class="col-sm-5 form-control-label">
                    Max Redeem Percentage(%) <span class="valid_field">*</span>
                </label>
                <div class="col-sm-7">
                    <input type="text" name="max_redeem_percentage" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="e.g. 10 for 10%" value="{{ old('max_redeem_percentage') }}">
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

  
  <div class="modal fade" id="editLoyalty" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content add-loyalty-data">
      </div>
    </div>
  </div>

  <div class="modal fade" id="showLoyalty" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content show-loyalty-data">
      </div>
    </div>
  </div>

@endsection
@push("after-scripts")
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

    <script>
        function isNumberBlock(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode

    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode!=46)
        return false;
    return true;
}
        $(document).on('click', '.edit-loyalty', function(e) {
            $("#editLoyalty").modal({backdrop: false});
            var loyalty_id = $(this).attr('data-id');
            $(document).find('#editLoyalty').find(".add-loyalty-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('loyalty.edit')}}",
               type: 'POST',
               data: 'loyalty_id='+loyalty_id,
               success: function (response) {
                    $(document).find('#editLoyalty').find(".add-loyalty-data").append(response.html);
                    $(document).find('#editLoyalty').modal('show');
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });

        $(document).on('click', '.show-loyalty', function(e) {
            $("#showLoyalty").modal({backdrop: false});
            var loyalty_id = $(this).attr('data-id');
            $(document).find('#showLoyalty').find(".show-loyalty-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('loyalty.show')}}",
               type: 'POST',
               data: 'loyalty_id='+loyalty_id,
               success: function (response) {
                    $(document).find('#showLoyalty').find(".show-loyalty-data").append(response.html);
                    $(document).find('#showLoyalty').modal('show');
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });

        $(document).on('click', '.status_active', function(e) {
            var loyalty_id = $(this).attr('data-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('loyalty.status_active')}}",
               type: 'POST',
               data: 'loyalty_id='+loyalty_id,
               beforeSend: function(){
                                    // $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
               success: function (response) {
                $('.loader').css("visibility", "visible");
                    window.location.href = "{{ route('loyalty')}}";
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });

        $(document).on('click', '.status_inactive', function(e) {
            var loyalty_id = $(this).attr('data-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('loyalty.status_inactive')}}",
               type: 'POST',
               data: 'loyalty_id='+loyalty_id,
               beforeSend: function(){
                                    // $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
               success: function (response) {
                $('.loader').css("visibility", "visible");
                // return false;
                    window.location.href = "{{ route('loyalty')}}";
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });
        

        $(document).ready(function () {
    $('#myModal form')[0].reset();
            var test = $("#loyaltyForm").validate({
                rules: {
                    minimum_purchase_amount: "required",
                    loyalty_percentage: "required",
                    maximum_points: "required",
                    points_per_ghs: "required",
                    redeem_ghs_value: "required",
                    max_redeem_percentage: "required",
                },
                messages: {
                    minimum_purchase_amount: "Minimum purchase amount field is required",
                    loyalty_percentage: "Loyalty percentage field is required",
                    maximum_points: "Maximum Redeemable Points field is required",
                    points_per_ghs: "Points redeem rate field is required",
                    redeem_ghs_value: "GHS value field is required",
                    max_redeem_percentage: "Max redeem percentage field is required",
                },
                submitHandler: function(){
                    var form_data = new FormData($('#loyaltyForm')[0]);
                    action_url = "{{ route('loyalty.store') }}";
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
                                if (data.success) {
                                    $('.loader').css("visibility", "visible");
                                    window.location.href = "{{ route('loyalty')}}";
                                }
                            },
                             error: function (errors) {
                                    $('.loader').css("visibility", "hidden");
                                 var erroJson = JSON.parse(errors.responseText);
                                 console.log(erroJson.title);
                                   for (var err in erroJson) {
                            for (var errstr of erroJson[err])
                                  $("span#errorMessage").css("display", "block");
                              $("span#errorMsg").css("display", "block");

                              $("span#errorMsg").html(errstr);
                              }
                               }
                        });
                }
            });
            $('#myModal').on('hidden.bs.modal', function () {
  test.resetForm();
  $('#myModal form')[0].reset();
})
        });
 
    </script>
  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
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
           function load_data() 
           {
        
              var action_url = "{!!  route('loyalty.anyData') !!} ";
            
               $('#label').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    // columnDefs: [{
                    //    'bSortable': false,
                    //    'aTargets': [0,3,4]
                    // }],
                   ajax: {
                       url : action_url,
                       type: 'POST',
                       data:{
                       
                       }
                   },
                   columns: [
                    {
                       data: 'checkbox',
                        orderable: false,
                       searchable: false

                   },
                   {
                       data: 'minimum_purchase_amount',
                       name: 'minimum_purchase_amount',
                       
                   },
                   {
                      data: 'loyalty_percentage',
                      name: 'loyalty_percentage',
                   },
                    {
                      data: 'maximum_points',
                      name: 'maximum_points',
                   },
                    {
                      data: 'points_per_ghs',
                      name: 'points_per_ghs',
                   },
                    {
                      data: 'redeem_ghs_value',
                      name: 'redeem_ghs_value',
                   },
                    {
                      data: 'max_redeem_percentage',
                      name: 'max_redeem_percentage',
                   },
                    {
                      data: 'created_at',
                      name: 'created_at',
                   },
                    {
                      data: 'status',
                      name: 'status',
                   },
                   {
                       data: 'options',
                       orderable: false,
                       searchable: false
                   }
                   ],
                   order: ['0', 'DESC']
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
                       msg = "Are you sure you want to deactive this loyalty?";
                   }
                   else if(type == 1)
                   {
                       msg = "Are you sure you want to active this loyalty?";
                   }
                   else
                   {
                       msg = "Are you sure you want to delete this loyalty?";
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
               url: "{{ route('loyaltyUpdateAll')}}",
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
