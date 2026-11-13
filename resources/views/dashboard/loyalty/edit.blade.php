
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.EditLoyalty') }}</h4>
        </div>
        <form class="cmxform" id="loyaltyEditForm" method="post" action="" autocomplete="off">
        <div class="modal-body">

           <div class="form-group row">
                        <label class="col-sm-5 form-control-label">{!!  __('backend.minimum_purchase_amount') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" name="minimum_purchase_amount" id="minimum_purchase_amount" class="form-control" placeholder="Minimum Purchase Amount"  value="{{@$loyaltyData->minimum_purchase_amount}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" style="display: flex;">Loyalty Percentage(%)<span class="valid_field">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" name="loyalty_percentage" id="loyalty_percentage" class="form-control" placeholder="e.g. 25 for 25%" value="{{@$loyaltyData->loyalty_percentage}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label"  style="display: flex;">Redeem Limit <span class="valid_field">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" name="maximum_points" id="maximum_points" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="200 GHS" value="{{@$loyaltyData->maximum_points}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" style="display: flex;">Points Redeem Rate <span class="valid_field">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" name="points_per_ghs" id="points_per_ghs" class="form-control" placeholder="e.g. 50 = 2 GHS" value="{{@$loyaltyData->points_per_ghs}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" style="display: flex;">Value for Points(GH₵)<span class="valid_field">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" name="redeem_ghs_value" id="redeem_ghs_value" class="form-control" placeholder="e.g. 2 for 50 points = 2 GHS" value="{{@$loyaltyData->redeem_ghs_value}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-5 form-control-label" style="display: flex;">Max Redeem Percentage(%) <span class="valid_field">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" name="max_redeem_percentage" id="max_redeem_percentage" class="form-control" placeholder="e.g. 10 for 10%" value="{{@$loyaltyData->max_redeem_percentage}}">
                        </div>
                    </div>
                   
        </div>
        <div class="modal-footer">
            <input type="hidden" name="loyalty_id" id="loyalty_id" value="{{@$loyaltyData->id}}">
          <button type="submit" class="btn btn-default btn btn-primary">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {{Form::close()}}
<script type="text/javascript">
    $(document).ready(function () {
 
            $("#loyaltyEditForm").validate({
                rules: {
                    minimum_purchase_amount: "required",
                    loyalty_percentage: "required",
                    maximum_points: "required",
                    points_per_ghs: "required",
                    redeem_ghs_value: "required",
                    max_redeem_percentage: "required",
                },
                messages: {
                    minimum_purchase_amount: " Minimum purchase amount field is required",
                    loyalty_percentage: "Loyalty percentage field is required",
                    maximum_points: "Maximum Redeemable Points field is required",
                    points_per_ghs: "Points redeem rate field is required",
                    redeem_ghs_value: "GHS value field is required",
                    max_redeem_percentage: "Max redeem percentage field is required",
                },
                submitHandler: function(){
                    var form_data = new FormData($('#loyaltyEditForm')[0]);
                    action_url = "{{ route('loyalty.store') }}";
                    var csrf = "{{ csrf_token() }}";
                    $.ajax({
                            url: action_url,
                            data: form_data,
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            processData: false,
                            contentType: false,
                            type: "POST",
                            dataType: 'json',
                             beforeSend: function(){
                                    // $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
                            success: function(data){
                                if (data.success) {
                                    $('.loader').css("visibility", "visible");
                                    window.location.href = "{{ route('loyalty')}}";
                                }
                            }
                        });
                }
            });
        });
</script>