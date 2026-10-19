<div class="modal-header">
    <h4 class="modal-title">View Country Admin</h4>
</div>
<div class="modal-body">
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">{!! __('backend.name') !!}</label>
        <div class="col-sm-10">
            <div class="show_blade_div">
                {{ @$customerData->name }}
            </div>  
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label" style="display: flex;">{!! __('backend.email') !!}</label>
        <div class="col-sm-10">
            <div class="show_blade_div">{{ @$customerData->email }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label" style="display: flex;">{!! __('backend.phone') !!}</label>
        <div class="col-sm-10">
            <div class="show_blade_div">{{ @$customerData->phone }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label" style="display: flex;">{!! __('backend.country') !!}</label>
        <div class="col-sm-10">
            <div class="show_blade_div">{{ @$country_info->name }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">Profile Photo</label>
        <div class="col-sm-10"> @if (@$customerData->photo) <a href="{{ asset('uploads/customer/') . '/' . $customerData->photo }}" target="_blank">
                <img src="{{ asset('uploads/customer/') . '/' . $customerData->photo }}" alt="Category Image" height="100" width="100"  style="width:100px !important; height:100px !important;">
            </a> @else <div>
                <img src="{{ asset('assets/dashboard/images/no_image_found.jpg') }}" alt="Category Image" height="100" width="100"  style="width:100px !important; height:100px !important;">
            </div> @endif </div>
    </div>
</div>
<div class="modal-footer">
    <input type="hidden" name="customer_id" id="customer_id" value="{{ @$customerData->id }}">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
</div>