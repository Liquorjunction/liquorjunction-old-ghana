<div class="modal-header">
    <h4 class="modal-title">{{ __('backend.ShowCategory') }}</h4>
</div>

<div class="modal-body">   
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">{!! __('Title [EN]') !!}</label>
        <div class="col-sm-10">
            <div class="show_blade_div">
                {{@$categoryData->title}}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">{!! __('Title [FR]') !!}</label>
        <div class="col-sm-10">
            <div class="show_blade_div">
                {{@$categoryData->title_fr}}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">Image</label>
        <div class="col-sm-10">
            @if (@$categoryData->imagefile)
            <a href="{{ asset('uploads/category/').'/'.$categoryData->imagefile }}" target="_blank">
                <img src="{{ asset('uploads/category/').'/'.$categoryData->imagefile }}" alt="Subcategory Image" height="100" width="100">
            </a>
            @else
            <div> <img src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="Category Image" height="100" width="100"></div>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">Background Image</label>
        <div class="col-sm-10">
            @if (@$categoryData->photo)
            <a href="{{ asset('uploads/categoryback/').'/'.$categoryData->photo }}" target="_blank">
                <img src="{{ asset('uploads/categoryback/').'/'.$categoryData->photo }}" alt="Subcategory Image" height="100" width="100">
            </a>
            @else
            <div> <img src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="Category Image" height="100" width="100"></div>
            @endif
        </div>
    </div>
    @if($categoryData->url)
    <div class="form-group row">
        <label class="col-sm-2 form-control-label">URL</label>
        <div class="col-sm-10">
            <div class="show_blade_div">
                {{@$categoryData->url}}
            </div>
        </div>
    </div>
    @endif

    

</div>
<div class="modal-footer">
    <input type="hidden" name="category_id" id="category_id" value="{{@$categoryData->id}}">
    <!-- <button type="submit" class="btn btn-default btn btn-primary">Submit</button> -->
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
</div>