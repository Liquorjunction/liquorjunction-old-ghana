
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.ShowWholesaler') }}</h4>
        </div>
       
        <div class="modal-body">

           <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.firstname') !!}</label>
                        <div class="col-sm-10">
                            
                                                        <div class="show_blade_div">
                                {{@$customerData->first_name}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.lastname') !!}</label>
                        <div class="col-sm-10">
                            
                                                        <div class="show_blade_div">
                                {{@$customerData->last_name}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" style="display: flex;">{!!  __('backend.email') !!}</label>
                        <div class="col-sm-10">
                           
                            <div class="show_blade_div">{{@$customerData->email}}</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" style="display: flex;">{!!  __('backend.phone') !!}</label>
                        <div class="col-sm-10">
                           
                            <div class="show_blade_div">{{@$customerData->phone}}</div>
                        </div>
                    </div>
                    <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Image</label>
                            <div class="col-sm-10">
                                @if (@$customerData->profile)
                            <a href="{{ asset('uploads/customer/').'/'.$customerData->profile }}" target="_blank">
                                <img src="{{ asset('uploads/customer/').'/'.$customerData->profile }}" alt="Category Image" height="100" width="100">
                            </a>
                            @else
                                <div> <img src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="Category Image" height="100" width="100"></div>
                            @endif
                            </div>
                        </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="customer_id" id="customer_id" value="{{@$customerData->id}}">
          <!-- <button type="submit" class="btn btn-default btn btn-primary">Submit</button> -->
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
       