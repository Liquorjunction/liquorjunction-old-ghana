<div class="modal-header">
    <h4 class="modal-title">View Offer</h4>
</div>
<div class="modal-body">
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Offer Type</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ucfirst( $offerData->offer_type) }}
            </div>
        </div>
    </div>
    {{-- <div class="form-group row">
        <label class="col-sm-4 form-control-label">Product Type</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->product_type }}
            </div>
        </div>
    </div> --}}
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Applicable On</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                @php
                    $types = [1 => 'Brand', 2 => 'Category', 3 => 'Product'];
                    echo isset($types[$offerData->product_type]) ? $types[$offerData->product_type] : 'N/A';
                @endphp
            </div>
        </div>
    </div>
    <div class="form-group row discountAmount">
        <label class="col-sm-4 form-control-label">Discount Amount</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->dis_amount }}
            </div>
        </div>
    </div>
    <div class="form-group row discountPercentage">
        <label class="col-sm-4 form-control-label">Discount Percentage(%)</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->dis_amount }}
            </div>
        </div>
    </div>
    {{-- <div class="form-group row">
        <label class="col-sm-4 form-control-label">Minimum Amount</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->min_amount }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Maximum Amount</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->max_amount }}
            </div>
        </div>
    </div> --}}
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Expiry Date</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->expiry_date }}
            </div>
        </div>
    </div>
    {{-- <div class="form-group row">
        <label class="col-sm-4 form-control-label">Usage Limit</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->total_usage }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Max Users</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->max_users }}
            </div>
        </div>
    </div> --}}
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Button Template</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->template ? $offerData->template : "-" }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Custom URL</label>
        <div class="col-sm-8">
            <div class="show_blade_div">
                {{ @$offerData->custom_url ? $offerData->custom_url : "-" }}
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-4 form-control-label">Images</label>
        <div class="col-sm-8">
            @if ($offerData->get_offer_images[0]->image)
            <div class="row">
                <div class="col-sm-12 images">
                    <div id="user_photo" class="col-sm-12 box p-a-xs">
                        <?php foreach ($offerData->get_offer_images as $singleImage) { ?>

                            <a href="{{ asset('uploads/offers/' . $singleImage->image) }}" target="_blank">
                                <img src="{{ asset('uploads/offers/' . $singleImage->image) }}" alt="Offer Image" height="100" width="100" style="padding-top:3px;">
                            </a>&nbsp;
                        <?php } ?>
                    </div>
                </div>
            </div>
            @else
            <div>-</div>
            @endif
        </div>

    </div>



</div>
<div class="modal-footer">
    <input type="hidden" name="offer_id" id="offer_id" value="{{ @$offerData->id }}">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
</div>


<script>
        $(document).ready(function() {
            toggleFieldsBasedOnOfferType();
        });

        function toggleFieldsBasedOnOfferType() {
            var offerType = "{{ $offerData->offer_type }}";

        $('.discountAmount').hide();
        $('.discountPercentage').hide();

        if (offerType == 'percentage') {
            $('.discountAmount').hide();
            $('.discountPercentage').show();

        } else if (offerType =='flat') {
            $('.discountAmount').show();
            $('.discountPercentage').hide();
        }
    }
</script>