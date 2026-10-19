@extends('dashboard.layouts.master')
@section('title', __('backend.cms'))
@section('content')
@include('sweetalert::alert')
   
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />  
    <div class="padding website-label">
        <div class="success_message" style="margin-bottom: 10px;"></div>
        <div id="success_file_popup" style="margin-bottom: 10px;"></div>
        <div class="box">
            
            <div class="box-header dker">
                <h3>{{ __('backend.cms') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>{{ __('backend.cms') }}</span> 
                </small>
            </div>

            {{-- <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="btn btn-fw primary" href="{{route('cms.create')}}">
                            <i class="material-icons">&#xe7fe;</i>
                            &nbsp; New CMS
                        </a>
                    </li>
                </ul>
            </div> --}}
            {{Form::open(['route'=>'userlistUpdateAll','method'=>'post','id'=>'updateAll'])}}
            {{-- <div class="bulk-action">
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
            </div>  --}}
                <div class="table-responsive">
                    <table class="table table-bordered m-a-0" id="cms">
                        <thead class="dker">
                        <tr>
                            <!-- <th  class="width20 dker no-sort">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th> -->
                            <th>ID</th>
                            <th>{{ __('backend.topicCommentName') }}</th>
                            <th>Status</th>
                            <th>{{ __('backend.options') }}</th>
                        </tr>
                        </thead>
                        <tbody id="bannerTable">

                     

                        </tbody>
                    </table>

                </div>
              
                {{Form::close()}}
        </div>
    </div>
@endsection
@push("after-scripts")
 <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <script type="text/javascript">
        $(function() {
           $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });
           load_data();
           function load_data() 
           {
        
              var action_url = "{!!  route('cms.anyData') !!} ";
            
               $('#cms').DataTable({
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
                      
                   },
                   columns: [
                    {
                        data:'id',
                        name:'id',
                        visible:false

                    },
                   {
                       data: 'name',
                       name: 'Name',
                      
                   },

                   { data: 'status', name: 'status' },  
                  
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

        $(document).on('click', '.status_active', function(e) {
        var id = $(this).attr('data-id');
            // $(document).find('#editCustomer').find(".add-customer-data").empty();
        swal({ 
           title: "Deactive", 
           text: "Are you sure you want to deactive this customer?", 
           type: "warning", 
           showCancelButton: true, 
           confirmButtonColor: "#BF0A30", 
           confirmButtonText: "Deactive", 
           closeOnConfirm: false
       }, 
       function() { 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('cms.status_active')}}",
            type: 'POST',
            data: 'id='+id,
            beforeSend: function(){
                                    // $(".loader").fadeIn();
                $('.loader').css("visibility", "visible");
            },
            success: function (response) {
                $('.loader').css("visibility", "visible");
                window.location.href = "{{ route('cms')}}";
            },
            error: function (response) {
             alert(response.responseText);
         }
     });
    }
    ); 
    });

    $(document).on('click', '.status_inactive', function(e) {
        var id = $(this).attr('data-id');
            // $(document).find('#editCustomer').find(".add-customer-data").empty();
        swal({ 
           title: "Active", 
           text: "Are you sure you want to active this customer?", 
           type: "warning", 
           showCancelButton: true, 
           confirmButtonColor: "#002868",
           confirmButtonText: "Active",
           closeOnConfirm: false
       }, 
       function() { 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('cms.status_inactive')}}",
            type: 'POST',
            data: 'id='+id,
            beforeSend: function(){
                                    // $(".loader").fadeIn();
                $('.loader').css("visibility", "visible");
            },
            success: function (response) {
                $('.loader').css("visibility", "visible");
                // return false;
                window.location.href = "{{ route('cms')}}";
            },
            error: function (response) {
             alert(response.responseText);
         }
     });
    }
    ); 
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
                       msg = "Are you sure you want to deactive this cms?";
                   }
                   else if(type == 1)
                   {
                       msg = "Are you sure you want to active this cms?";
                   }
                   else
                   {
                       msg = "Are you sure you want to delete this cms?";
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
            $("#default_confirm").modal('hide');
        });
       
        function ajaxUpdateAll(csrf,join_selected_values,type)
        {
            $.ajax({
               url: "{{ route('cmsUpdateAll')}}",
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
                        var tabe = $('#cms').DataTable();
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
