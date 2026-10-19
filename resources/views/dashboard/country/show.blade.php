        <div class="modal-header">
          <h4 class="modal-title">{{ __('backend.showCountry') }}</h4>
        </div>
        <form class="cmxform" id="materialcategoryEditForm" method="post" action="" autocomplete="off">
          <div class="modal-body">
            
           <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!!  __('backend.country') !!} </label>
            <div class="col-sm-10">
              <input type="text" name="title" id="title" class="form-control" placeholder="Enter Name"  value="{{@$countryData->name}}" readonly>
              
            </div>
          </div> 
        </div>
        <div class="modal-footer">
          
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
        </div>
        {{Form::close()}}