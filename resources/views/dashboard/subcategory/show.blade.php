<div class="modal-header">
    <h4 class="modal-title">{{ __('backend.ShowSubcategory') }}</h4>
</div>

<div class="modal-body">
    <div class="form-group row">
        <label class="col-sm-2 form-control-label" style="display: flex;">{!! __('backend.category') !!}</label>
        <div class="col-sm-10">

            <div class="show_blade_div">{{@$subcategoryData->category->title}}</div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">{!! __('backend.title') !!} [EN]</label>
        <div class="col-sm-10">

            <div class="show_blade_div">{{@$subcategoryData->title}}</div>

        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">{!! __('backend.title') !!} [FR]</label>
        <div class="col-sm-10">

            <div class="show_blade_div">{{@$subcategoryData->title}}</div>

        </div>
    </div>
    

</div>
<div class="modal-footer">
    <!-- <input type="hidden" name="category_id" id="category_id" value="{{@$subcategoryData->id}}"> -->
    <!-- <button type="submit" class="btn btn-default btn btn-primary">Submit</button> -->
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
</div>