
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.ShowAdvertise') }}</h4>
        </div>
       
        <div class="modal-body">

           <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Wholesaler Name</label>
                        <div class="col-sm-10">
                            
                                                        <div class="show_blade_div">
                                {{@$store_name}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Website Banner</label>
                            <div class="col-sm-10">
                                @if (@$advertiseData->image)
                            <a href="{{ asset('uploads/advertise/').'/'.$advertiseData->image }}" target="_blank">
                                <img src="{{ asset('uploads/advertise/').'/'.$advertiseData->image }}" alt="Category Image" height="100" width="100">
                            </a>
                            @else
                                <div> <img src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="Category Image" height="100" width="100"></div>
                            @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Mobile Banner</label>
                            <div class="col-sm-10">
                                @if (@$advertiseData->mobile_banner)
                            <a href="{{ asset('uploads/advertise/').'/'.$advertiseData->mobile_banner }}" target="_blank">
                                <img src="{{ asset('uploads/advertise/').'/'.$advertiseData->mobile_banner }}" alt="Category Image" height="100" width="100">
                            </a>
                            @else
                                <div> <img src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="Category Image" height="100" width="100"></div>
                            @endif
                            </div>
                        </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="advertise_id" id="advertise_id" value="{{@$advertiseData->id}}">
          <!-- <button type="submit" class="btn btn-default btn btn-primary">Submit</button> -->
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
       