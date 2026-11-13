<div class="product-single-variant newadded">
    <div class="form-group row">
        <label class="col-sm-3 form-control-label" style="display: flex;">Product Attributes <span class="valid_field">*</span></label>
        <div class="col-sm-3">
            <select name="prod_variant[{{$key}}][variant_uof]" class="form-control" id="uof_{{$key}}">
                <option value="">Select Unit</option>
                @foreach ($uofs as $item)
                    <option value="{{$item->id}}">{{$item->title}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <input type="text" name="prod_variant[{{$key}}][variant_size]" maxlength="5"
                onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Size eg:200"
                value="">
        </div>
        <div class="col-sm-2 text-right">
            <button type="button" onclick="remove_current_variant(this)" class="btn btn-danger">Remove</button>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 form-control-label"
            style="display: flex;">Original Price ({{ @$settings->currency_symbol }}) <span
                class="valid_field">*</span></label>
        <div class="col-sm-9">
            <input type="text" name="prod_variant[{{$key}}][variant_price]" maxlength="7" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Original Price" value="">
        </div>
    </div>
    {{-- <div class="form-group row">
        <label class="col-sm-3 form-control-label"
            style="display: flex;">Discounted Price ({{ @$settings->currency_symbol }})</label>
        <div class="col-sm-9">
            <input type="text" name="prod_variant[{{$key}}][variant_discounted_price]" maxlength="7"
                onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Discounted Price" value="">
        </div>
    </div> --}}
    <div class="form-group row">
        <label class="col-sm-3 form-control-label"
            style="display: flex;">Qty <span
                class="valid_field">*</span></label>
        <div class="col-sm-9">
            <input type="text" name="prod_variant[{{$key}}][variant_qty]" maxlength="5"
                onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Qty"
                value="">
        </div>
    </div>

   
</div>