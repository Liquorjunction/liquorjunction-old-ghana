        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.ShowTimeFrame') }}</h4>
        </div>
        <form class="cmxform" id="timeframeEditForm" method="post" action="" autocomplete="off">
        <div class="modal-body">
            
           <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.name') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name"  value="{{@$timeframeData->name}}" readonly>
                            
                        </div>
                    </div> 
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {{Form::close()}}
      <!-- </div> -->


