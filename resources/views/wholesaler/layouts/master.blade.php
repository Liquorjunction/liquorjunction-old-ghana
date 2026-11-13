<!DOCTYPE html>
<html>

<head>
  @include('wholesaler.layouts.head')
</head>

<body>
  <div class="app" id="app">
    @include('wholesaler.layouts.menu')

    <div id="content" class="app-content box-shadow-z0" role="main">
      @include('wholesaler.layouts.header')
      @include('wholesaler.layouts.footer')
      <div ui-view class="app-body" id="view">
        {{-- @include('wholesaler.layouts.errors') --}}
        @if(Session::has('doneMessage'))
    <div class="padding p-b-0" id="topmsgall">
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-success m-b-0">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    {{ Session::get('doneMessage') }}
                </div>
            </div>
        </div>
    </div>
@endif

@if(Session::has('errorMessage'))
    <div class="padding p-b-0" id="topmsgall">
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger m-b-0">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    {{ Session::get('errorMessage') }}
                </div>
            </div>
        </div>
    </div>
@endif
        @yield('content')
      </div>
    </div>

    @include('wholesaler.layouts.settings')
  </div>
  @include('wholesaler.layouts.foot')
  <div class="modal fade" id="alert_confirm" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
        </div>
        <div class="modal-body">
          <p class="alert_dynamic_message">
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="default_confirm" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
        </div>
        <div class="modal-body p-lg">
          <p class="dynamic_message">
            Are you sure ?
          </p>
          <input type="hidden" name="checkbox_data" class="checkbox_data">
          <input type="hidden" name="checkbox_type" class="checkbox_type">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger yes_click">Yes</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="cancel_confirm" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
        </div>
        <div class="modal-body p-lg">
          <p class="dynamic_message">
            Are you sure ?
          </p>
          <input type="hidden" name="checkbox_data" class="checkbox_data">
          <input type="hidden" name="checkbox_type" class="checkbox_type">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger yes_cancel">Yes</button>
        </div>
      </div>
    </div>
  </div>

</body>
<script type="text/javascript">
   function checkChange() {
         var totalCheckbox = document.querySelectorAll('input[name="ids[]"]').length;
         var totalChecked = document.querySelectorAll('input[name="ids[]"]:checked').length;
        if (totalCheckbox == totalChecked) {
           $('#checkAll').not(this).prop('checked', true);
        } else {
          $('#checkAll').not(this).prop('checked', false);
        }
}
</script>

</html>