@extends('dashboard.layouts.master')
@section('title', __('View Roles | Admin Panel') )
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
</style> 
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
<div class="padding">
   <div class="box">
      <div class="box-header dker">
         <h3>View Role</h3>
         <small>
            <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
            <a href="{{ route('roles') }}">Roles</a> /
            <span>View Role</span>
         </small>
      </div> 
      <form> 
         <div class="col-sm-12">
            <div class="form-group">
               <label>Role Name</label>
               <input  class="form-control has-value" name="role_name" type="text" value=" {{$role_name}}" disabled>
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
                   <a href="{{ route('roles') }}" class="btn btn-default m-t">
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
                  data:{
                   encode_id:'{{$encode_id}}',
                   page:'show',
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
              order: ['0', 'DESC']
          });
      }
   
   });
    
   
   
  
</script>
@endpush