<div class="modal-header">
    <h4 class="modal-title">{{ __('backend.showRegion') }}</h4>
</div>

<div class="modal-body">

    <div class="form-group row">

        <label class="col-sm-2 form-control-label">{!! __('backend.country') !!}</label>
        <div class="col-sm-10">

            <div class="show_blade_div">
                {{$regionData->country->name}}
            </div>
        </div>
    </div>
    <div class="form-group row">

        <label class="col-sm-2 form-control-label">{!! __('Title [EN]') !!}</label>
        <div class="col-sm-10">
            <div class="show_blade_div">
                {{@$regionData->title}}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">{!! __('Title [FR]') !!}</label>
        <div class="col-sm-10">
            <div class="show_blade_div">
                {{@$regionData->title_fr}}
            </div>
        </div>
    </div>



</div>
<div class="modal-footer">
    <input type="hidden" name="brand_id" id="brand_id" value="{{@$brandData->id}}">
    <!-- <button type="submit" class="btn btn-default btn btn-primary">Submit</button> -->
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
</div>