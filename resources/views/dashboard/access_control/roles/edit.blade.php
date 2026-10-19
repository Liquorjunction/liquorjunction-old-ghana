@extends('dashboard.layouts.master')
@section('title', __('Edit Roles | Admin Panel'))
@section('content') 

<?php
 $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,29,'read');  
?>
@if(isset($check_view_permission) && $check_view_permission==true)
<style type="text/css"> 
div#roles_length {
    display: none;
} 
div#roles_filter {
    display: none;
}
div#roles_paginate {
    display: none;
}
div#roles_info {
    display: none;
}

.swal2-confirm {
   float: right !important;
   margin-left: 10px;
}

.swal2-cancel {
   float: left;
   margin-right: 10px;
}
</style> 
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<div class="padding">
   <div class="box">
      <div class="box-header dker">
         <h3>Edit Role</h3>
         <small>
         <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
         <a href="{{ route('roles') }}">Roles</a> /
         <span>Edit Role</span>
         </small>
      </div>
      <div class="box-tool">
         <ul class="nav">
            @if(@Auth::user()->permissionsGroup->webmaster_status)
            <li class="nav-item inline">
               <a class="btn btn-fw primary" href="{{route('roles.create')}}">
               <i class="material-icons">&#xe02e;</i>
               &nbsp; New Email Templete
               </a>
            </li>
            @endif
         </ul>
      </div>
      <form action="{{route('role.update.permission')}}" method="POST" onsubmit="return validate_form()">
         @CSRF    
         <input type="hidden" name="encode_id" value="{{$encode_id}}">
         
         <div class="col-sm-12">
            <div class="form-group">
               <label>Role Name <!---<span class="valid_field">*</span> --> </label>
               <input  class="form-control has-value" readonly name="role_name" type="text" value="{{$role_name}}">
               <span class="help-block">
               @if(!empty(@$errors) && @$errors->has('role_name'))
               <span  style="color: red;" >{{ $errors->first('role_name') }}</span>
               @endif
               </span>
            </div>
         </div> 
         <br>
         <div class="table-responsive">
            <table class="table table-bordered m-a-0" id="roles">
               <thead class="dker">
                  <tr>
                     <th>id</th>
                     <th>Module Name</th>
                     <th>Read</th>
                     {{-- <th>Create</th>
                     <th>Update</th>
                     <th>Delete</th> --}}
                  </tr>
               </thead>
               <tbody id="roles">
               </tbody>
            </table>
            <div class="form-group row m-t-md">
               <div class="col-sm-12" style="margin-left:11px;">                  
                  <button type="submit" id="save-settings-btn" class="btn primary m-a"> <i class="material-icons"></i> Update</button>
                  <a href="{{ route('roles') }}" class="btn btn-default ">
                     <i class="material-icons">
                         &#xe5cd;</i> {!! __('backend.cancel') !!}
                  </a>
               </div>
           </div>
         </div>
      </form>
   </div>
</div>

@else
<input type="hidden" value="{{route('adminHome')}}" id="redirect_url">
<script type="text/javascript">
   var redirect_url = $('#redirect_url').val();
   window.location.replace(redirect_url); 
</script>
@endif

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
   
         var action_url = "{!!  route('roles.edit.permission.filter') !!} ";
       
          $('#roles').DataTable({
              paging:false,
              bInfo:false,
              searching:false,
              pageLength:false,
              columnDefs: [{
                  'bSortable': false,  
              }],
              ajax: {
                  url : action_url,
                  type: 'POST',
                  data:{
                   encode_id:'{{$encode_id}}',
                   page:'edit',
                  }
              },
              columns: [
              {
                  data: 'id',
                  name: 'id',
                  visible:false
                
              },
              {
                  data: 'name',
                  name: 'name',
                  orderable: false,
                  
                
              }, 
              {
                  data: 'read',
                  name: 'Read',
                  orderable: false,
                  searchable: false
              },
            //   {
            //       data: 'create',
            //       name: 'Create',
            //       orderable: false,
            //       searchable: false
            //   },
            //   {
            //       data: 'update',
            //       name: 'Update',
            //       orderable: false,
            //       searchable: false
            //   },
            //   {
            //       data: 'delete',
            //       name: 'Delete',
            //       orderable: false,
            //       searchable: false
            //   },
              ],
           //   order: ['0', 'desc']
          });
      }
   
   });
   
   $(document).on("click", ".create", function(e) {  

   var id = $(this).data("id"); 

   if($("input[name='create["+id+"]']").prop("checked") == true){
      var create_checkbox = 1;
   }
   else if($("input[name='create["+id+"]']").prop("checked") == false){
      var create_checkbox = 0;
   }
   
   if($("input[name='update["+id+"]']").prop("checked") == true){
      var update_checkbox = 1;
   }
   else if($("input[name='update["+id+"]']").prop("checked") == false){
      var update_checkbox = 0;
   } 

   if($("input[name='delete["+id+"]']").prop("checked") == true){
      var delete_checkbox = 1;
   }
   else if($("input[name='delete["+id+"]']").prop("checked") == false){
      var delete_checkbox = 0;
   }    
   if(create_checkbox == 1 || update_checkbox == 1 || delete_checkbox == 1){ 
      $("input[name='read["+id+"]']").prop('checked', true);
      $("input[name='read["+id+"]']").attr('onchange', 'this.checked = !this.checked'); 
   } 
   
   
});

$(document).on("click", ".update", function(e) {  

   var id = $(this).data("id"); 

   if($("input[name='create["+id+"]']").prop("checked") == true){
      var create_checkbox = 1;
   }
   else if($("input[name='create["+id+"]']").prop("checked") == false){
      var create_checkbox = 0;
   }
   
   if($("input[name='update["+id+"]']").prop("checked") == true){
      var update_checkbox = 1;
   }
   else if($("input[name='update["+id+"]']").prop("checked") == false){
      var update_checkbox = 0;
   } 

   if($("input[name='delete["+id+"]']").prop("checked") == true){
      var delete_checkbox = 1;
   }
   else if($("input[name='delete["+id+"]']").prop("checked") == false){
      var delete_checkbox = 0;
   }    
   if(create_checkbox == 1 || update_checkbox == 1 || delete_checkbox == 1){ 
      $("input[name='read["+id+"]']").prop('checked', true);
      $("input[name='read["+id+"]']").attr('onchange', 'this.checked = !this.checked'); 
   } 
  
   
});

$(document).on("click", ".delete", function(e) {  

   var id = $(this).data("id"); 

   if($("input[name='create["+id+"]']").prop("checked") == true){
      var create_checkbox = 1;
   }
   else if($("input[name='create["+id+"]']").prop("checked") == false){
      var create_checkbox = 0;
   }
   
   if($("input[name='update["+id+"]']").prop("checked") == true){
      var update_checkbox = 1;
   }
   else if($("input[name='update["+id+"]']").prop("checked") == false){
      var update_checkbox = 0;
   } 

   if($("input[name='delete["+id+"]']").prop("checked") == true){
      var delete_checkbox = 1;
   }
   else if($("input[name='delete["+id+"]']").prop("checked") == false){
      var delete_checkbox = 0;
   }    
   if(create_checkbox == 1 || update_checkbox == 1 || delete_checkbox == 1){ 
      $("input[name='read["+id+"]']").prop('checked', true);
      $("input[name='read["+id+"]']").attr('onchange', 'this.checked = !this.checked'); 
   } 
  
   
});

$(document).on("click", ".read", function(e) {  

   var id = $(this).data("id"); 

   if($("input[name='create["+id+"]']").prop("checked") == true){
      var create_checkbox = 1;
   }
   else if($("input[name='create["+id+"]']").prop("checked") == false){
      var create_checkbox = 0;
   }
   
   if($("input[name='update["+id+"]']").prop("checked") == true){
      var update_checkbox = 1;
   }
   else if($("input[name='update["+id+"]']").prop("checked") == false){
      var update_checkbox = 0;
   } 

   if($("input[name='delete["+id+"]']").prop("checked") == true){
      var delete_checkbox = 1;
   }
   else if($("input[name='delete["+id+"]']").prop("checked") == false){
      var delete_checkbox = 0;
   }    
   
   if(create_checkbox == 1 || update_checkbox == 1 || delete_checkbox == 1){  
         e.preventDefault();
         e.stopPropagation(); 
   }else{ 
      $("input[name='read["+id+"]']").removeAttr('onchange');
   }  
   
});

function validate_form()
{
   valid = true;
   if($('input[type=checkbox]:checked').length == 0)
   {
      Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'At least one checkbox must be selected',
      })
      valid = false;
   }
   return valid;
}
   
</script>
@endpush