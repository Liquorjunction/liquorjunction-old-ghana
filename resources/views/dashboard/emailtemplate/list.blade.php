@extends('dashboard.layouts.master')
@section('title', __('backend.emailtemplate'))
@section('content')
  
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />  
    <div class="padding">
        <div class="success_message"></div>
        <div id="success_file_popup"></div>
        <div class="box">

            <div class="box-header dker">
                <h3>{{ __('backend.emailtemplate') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>{{ __('backend.emailtemplate') }}</span>
                </small>
            </div>

            <!-- <div class="box-tool">
                <ul class="nav">
                        
                            <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('emailtemplate.create')}}">
                                    <i class="material-icons">&#xe02e;</i>
                                    &nbsp; New Email Templete
                                </a>
                            </li>
                       
                </ul>
            </div> -->

                {{--<div class="dker b-b displayNone" id="filter_div">
                    <div class="p-a">
                        {{Form::open(['method'=>'GET','id'=>'filter_form','target'=>''])}}
                        <div class="filter_div">
                            <div class="row">
                                <div class="col-md-4"></div>
                                    <div class="col-md-3 col-xs-6 m-b-5p">
                                       <input placeholder="Search For" class="form-control" id="find_q" autocomplete="off" name="find_q" type="text">
                                    </div>
                                <div class="col-md-1 col-xs-6 m-b-5p">
                                    <button class="btn white w-full" id="search-btn" type="submit"><i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>--}}
            {{Form::open(['route'=>'userlistUpdateAll','method'=>'post','id'=>'updateAll'])}}
            <div class="bulk-action" style="display: none;">
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
                    <table class="table table-bordered m-a-0" id="emailtemplate">
                        <thead class="dker">
                        <tr>
                            <th  class="width20 dker no-sort">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <th>id</th>
                            <th>{{ __('backend.topicName') }}</th>
                            <th>{{ __('backend.subject') }}</th>
                            <th>{{ __('backend.options') }}</th>
                        </tr>
                        </thead>
                        <tbody id="emailTemplateTable">

                        </tbody>
                    </table>

                </div>
               
                {{Form::close()}}
        </div>
    </div>
@endsection
@push("after-scripts")
 <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
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
        
              var action_url = "{!!  route('emailtemplate.anyData') !!} ";
            
               $('#emailtemplate').DataTable({
                   processing: true,
                   serverSide: true,
                   responsive: true,
                   ordering: true,
                   columnDefs: [{
                       'bSortable': false,
                       'aTargets': [0,4]
                   }],
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
                       searchable: false,
                       visible:false

                   },
                   {
                       data: 'id',
                       name: 'id',
                       visible:false
                     
                   },
                   {
                       data: 'title',
                       name: 'title',
                     
                   },
                   {
                      data: 'subject',
                      name: 'subject',
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
    
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
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
                       msg = "Are you sure you want to deactive this template?";
                   }
                   else if(type == 1)
                   {
                       msg = "Are you sure you want to active this template?";
                   }
                   else
                   {
                       msg = "Are you sure you want to delete this template?";
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
               url: "{{ route('emailt_UpdateAll')}}",
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
                        var tabe = $('#emailtemplate').DataTable();
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
