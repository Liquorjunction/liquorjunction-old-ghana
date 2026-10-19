<div class="modal-header">
    <h4 class="modal-title">View Discount</h4>
</div>
<div class="modal-body">
    <div class="form-group row">
        <label class="col-sm-5 form-control-label">Discount Type</label>
        <div class="col-sm-7">
            <div class="show_blade_div">
                {{ucfirst( $discountData->discount_type) }}
            </div>
        </div>
    </div>
     <div class="form-group row discountAmountDiv">
        <label class="col-sm-5 form-control-label">Discount Amount</label>
        <div class="col-sm-7">
            <div class="show_blade_div">
                {{  intVal($discountData->discount_amount) }} {{ $settings->currency_symbol ?? '' }}
            </div>
        </div>
    </div>
    <div class="form-group row discountPercentageDiv">
        <label class="col-sm-5 form-control-label">Discount Percentage(%)</label>
        <div class="col-sm-7">
            <div class="show_blade_div">
                {{ intVal($discountData->discount_percentage).'%' }}
            </div>
        </div>
    </div>
      <div class="form-group row discountUptoDiv">
        <label class="col-sm-5 form-control-label">Discount Up to Amount</label>
        <div class="col-sm-7">
            <div class="show_blade_div">
                {{ intVal($discountData->upto_amount) }} {{ $settings->currency_symbol ?? '' }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-5 form-control-label">Minimum Amount</label>
        <div class="col-sm-7">
            <div class="show_blade_div">
                {{ @$discountData->min_amount }}
            </div>
        </div>
    </div>
    {{-- <div class="form-group row">
        <label class="col-sm-4 form-control-label">Maximum Amount</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$discountData->max_amount }}
            </div>
        </div>
    </div> --}}
    <div class="form-group row">
        <label class="col-sm-5 form-control-label">Expiry Date</label>
        <div class="col-sm-7">
            <div class="show_blade_div">
                {{ @$discountData->expiry_date }}
            </div>
        </div>
    </div>  
    
</div>
<div class="modal-footer">
    <input type="hidden" name="discount_id" id="discount_id" value="{{ @$discountData->id }}">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
</div>


<script>
        $(document).ready(function() {
            toggleFieldsBasedOnDiscountType();
        });

        function toggleFieldsBasedOnDiscountType() {
            var discountType = "{{ $discountData->discount_type }}";

        $('.discountAmountDiv').hide();
        $('.discountPercentageDiv').hide();
        $('.discountUptoDiv').hide();

        if (discountType == 'percentage') {
            $('.discountAmountDiv').hide();
            $('.discountPercentageDiv').show();
            $('.discountUptoDiv').show();
        } else if (discountType =='flat') {
            $('.discountAmountDiv').show();
            $('.discountPercentageDiv').hide();
            $('.discountUptoDiv').hide();
        }
    }
</script>