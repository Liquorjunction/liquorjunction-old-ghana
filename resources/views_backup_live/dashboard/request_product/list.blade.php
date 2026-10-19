@extends('dashboard.layouts.master')
@section('title','Request Product')
@section('content')

   <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
   <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" /> 
    <div class="loader" id="loader"></div> 
    <div class="padding website-label">
        <div class="success_message" style="margin-bottom: 10px;"></div>
        <div id="success_file_popup" style="margin-bottom: 10px;"></div>
        <div class="box">
           
            <div class="box-header dker">
                <h3>{{ __('backend.request_for_product') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>{{ __('backend.request_for_product') }}</span>
                </small>
            </div>

            

                {{Form::open(['route'=>'requestproductUpdateAll','method'=>'post','id'=>'updateAll'])}}
                <div class="bulk-action">
                    <div></div>
                       <div>
                        <select name="action" id="action" class="form-control c-select w-sm inline v-middle"
                                required>
                            <option value="no">{{ __('backend.bulkAction') }}</option>
                            <option value="1">{{ __('backend.verify') }}</option>
                            <!-- <option value="0">{{ __('backend.not_verify') }}</option> -->
                            <option value="2">{{ __('backend.reject') }}</option>
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
                            <!-- <th>Id</th> -->
                            <th id="category_title">{{ __('backend.product_name') }}</th>
                            <th>{{ __('backend.product_image') }}</th>
                            <th>{{ __('backend.product_category') }}</th>
                            <th>{{ __('backend.supplier_name') }}</th>
                            <th>{{ __('backend.retail_price') }}</th>
                            <th>{{ __('backend.discount_price') }}</th>
                            <th>{{ __('backend.status') }}</th>
                            <!-- <th id="option_width">{{ __('backend.options') }}</th> -->
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
          <h4 class="modal-title">{{ __('backend.AddCategory') }}</h4>
        </div>
        <form class="cmxform" id="categoryForm" method="post" action="" autocomplete="off">
        <div class="modal-body">

           <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.title') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="title" id="title" class="form-control" onkeypress="return isNumberKey(event)" placeholder="Category Title" value="{{old('title')}}">
                            
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" style="display: flex;">{!!  __('backend.description') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <!-- <input type="text" name="description" id="description" class="form-control" placeholder="Category Description" value="{{old('description')}}"> -->
                            <textarea class="form-control" id="description" name="description" placeholder="Category Description">{{old('description')}}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Image <span class="valid_field">*</span></label>
                            <div class="col-sm-6">
                                 <img id="blah" src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="your image" />
                                <input type="file" name="image" id="image" class="form-control" style="border: none; margin-left: -13px;" accept="image/*">
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .png, .jpg, .jpeg
                                </small>
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

  
  <div class="modal fade" id="editCategory" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content add-category-data">
      </div>
    </div>
  </div>

  <div class="modal fade" id="showProduct" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content show-product-data">
      </div>
    </div>
  </div>

@endsection
@push("after-scripts")
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

    <script>
        image.onchange = evt => {
  const [file] = image.files
  if (file) {
    blah.src = URL.createObjectURL(file)
  }
}
        $(document).on('click', '.edit-category', function(e) {
            var category_id = $(this).attr('data-id');
            $(document).find('#editCategory').find(".add-category-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('category.edit')}}",
               type: 'POST',
               data: 'category_id='+category_id,
               success: function (response) {
                    $(document).find('#editCategory').find(".add-category-data").append(response.html);
                    $(document).find('#editCategory').modal('show');
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
        });

         $(document).on('click', '.show-product', function(e) {
            var product_id = $(this).attr('data-id');
            $(document).find('#showProduct').find(".show-product-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
               url: "{{ route('product.show')}}",
               type: 'POST',
               data: 'product_id='+product_id,
               success: function (response) {
                    $(document).find('#showProduct').find(".show-product-data").append(response.html);
                    $(document).find('#showProduct').modal('show');
               },
               error: function (response) {
                   alert(response.responseText);
               }
           });
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
        
              var action_url = "{!!  route('request_product.anyData') !!} ";
            
              var dataTable = $('#label').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                       'bSortable': false,
                       'aTargets': [0,2,7]
                    }],
                   ajax: {
                       url : action_url,
                       type: 'POST',
                       data: function(d) {
                            return $.extend({}, d, {
                                
                               
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
                       data: 'product_name',
                       name: 'product_name',
                       
                   },
                   {
                       data: 'product_image',
                       name: 'product_image',
                       
                   },
                   {
                      data: 'product_category',
                      name: 'product_category',
                   },
                   
                   {
                       data: 'supplier_name',
                       name: 'supplier_name',
                       
                   },
                   {
                       data: 'retail_price',
                       name: 'retail_price',
                       
                   },
                   {
                       data: 'discount_price',
                       name: 'discount_price',
                       
                   },
                    {
                      data: 'status',
                      name: 'status',
                   },
                   // {
                   //     data: 'options',
                   //     orderable: false,
                   //     searchable: false
                   // }
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
                       msg = "Are you sure you want to deactive this product?";
                   }
                   else if(type == 1)
                   {
                       msg = "Are you sure you want to verify this product?";
                   }
                   else
                   {
                       msg = "Are you sure you want to reject this product?";
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
               url: "{{ route('requestproductUpdateAll')}}",
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
