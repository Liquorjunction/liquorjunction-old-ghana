<div class="modal-header">
    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
    <h4 class="modal-title">{{ __('backend.showArea') }}</h4>
</div>
<div class="modal-body">
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">{!! __('backend.region') !!}</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ $areaData->region->title }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">{!! __('Title [EN]') !!}</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$areaData->title }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">{!! __('Title [FR]') !!}</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$areaData->title_fr }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Delivery Fee</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$areaData->delivery_fee }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Purchase Amount</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$areaData->delivery_amount }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="hidden" name="area_id" id="area_id" value="{{ @$areaData->id }}">
    <!-- <button type="submit" class="btn btn-default btn btn-primary">Submit</button> -->
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
</div>
