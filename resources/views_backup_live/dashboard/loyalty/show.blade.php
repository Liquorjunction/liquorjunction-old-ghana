
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.ShowLoyalty') }}</h4>
        </div>
       
        <div class="modal-body">

           <div class="form-group row">
              <label class="col-sm-5 form-control-label">{!!  __('backend.minimum_purchase_amount') !!}</label>
                <div class="col-sm-7">
                      <div class="show_blade_div">
                          {{@$loyaltyData->minimum_purchase_amount}}
                      </div>
                  </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-5 form-control-label" style="display: flex;">Loyalty percentage(%)</label>
                    <div class="col-sm-7">
                        <div class="show_blade_div">{{@$loyaltyData->loyalty_percentage}}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-5 form-control-label" style="display: flex;">Redeem Limit</label>
                    <div class="col-sm-7">
                        <div class="show_blade_div">{{@$loyaltyData->maximum_points}}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-5 form-control-label" style="display: flex;">Points Redeem Rate</label>
                    <div class="col-sm-7">
                        <div class="show_blade_div">{{@$loyaltyData->points_per_ghs}}</div>
                    </div>
                </div>     
                 <div class="form-group row">
                    <label class="col-sm-5 form-control-label" style="display: flex;">Value for Points(GH₵)</label>
                    <div class="col-sm-7">
                        <div class="show_blade_div">{{@$loyaltyData->redeem_ghs_value}}</div>
                    </div>
                </div> 
                 <div class="form-group row">
                    <label class="col-sm-5 form-control-label" style="display: flex;">Max Redeem Percentage(%)</label>
                    <div class="col-sm-7">
                        <div class="show_blade_div">{{@$loyaltyData->max_redeem_percentage}}</div>
                    </div>
                </div> 
        </div>
        <div class="modal-footer">
            <input type="hidden" name="loyalty_id" id="loyalty_id" value="{{@$loyaltyData->id}}">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
       