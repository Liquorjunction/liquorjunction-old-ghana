
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.EditAdvertise') }}</h4>
        </div>
        <form class="cmxform" id="advertiseEditForm" method="post" action="" autocomplete="off">
        <div class="modal-body">

            <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.wholesaler_name') !!} </label>
                         <div class="col-sm-10">
                        <select class="form-control" name="wholesaler_id" id="wholesaler_id">
                            <option value="0">Select Wholesaler</option>
                            @foreach($wholesalerData as $wholesaler)
                            <option value="{{$wholesaler->id}}" {{ ($advertiseData->wholesaler_id == $wholesaler->id)  ?  'selected' : "" }}>{{@$wholesaler->name}}->{{@$wholesaler->store_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Website Banner <span class="valid_field">*</span></label>
                            <div class="col-sm-8">
                               
                                    
                                <img id="blah3" src="<?php
                                    if (!empty($advertiseData->image)) { ?>
                                        {{ asset('uploads/advertise/'.$advertiseData->image) }}
                                    <?php }else{ ?>
                                        {{ asset('assets/dashboard/images/no_image_found.jpg')}}
                                    <?php }
                                 ?>" alt="your image" />
                                
                                <input type="file" name="website_banner_edit" id="website_banner_edit" class="form-control" value="" style="border: none; margin-left: -13px;" accept="image/png, image/jpg, image/jpeg">
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .png, .jpg, .jpeg <br/>
                                    <i class="material-icons">&#xe8fd;</i>
                                    Recommended size 1400(Width) x 300(Height)
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Mobile Banner <span class="valid_field">*</span></label>
                            <div class="col-sm-8">
                               
                                    
                                <img id="blah4" src="<?php
                                    if (!empty($advertiseData->image)) { ?>
                                        {{ asset('uploads/advertise/'.$advertiseData->mobile_banner) }}
                                    <?php }else{ ?>
                                        {{ asset('assets/dashboard/images/no_image_found.jpg')}}
                                    <?php }
                                 ?>" alt="your image" />
                                
                                <input type="file" name="mobile_banner_edit" id="mobile_banner_edit" class="form-control" value="" style="border: none; margin-left: -13px;" accept="image/png, image/jpg, image/jpeg">
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .png, .jpg, .jpeg <br/>
                                    <i class="material-icons">&#xe8fd;</i>
                                    Recommended size 1400(Width) x 300(Height)
                                </small>
                            </div>
                        </div>
                        <span class="help-block" id="errorMessageBanner" >
                                <span  style="color: red;display: none;" id="errorMsgBanner" class='validate'></span>
                            </span>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="advertise_id" id="advertise_id" value="{{@$advertiseData->id}}">
          <button type="submit" class="btn btn-default btn btn-primary">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {{Form::close()}}
      <!-- </div> -->
<script type="text/javascript">
    website_banner_edit.onchange = evt => {
        // alert('hello')
  const [file] = website_banner_edit.files
  if (file) {
    blah3.src = URL.createObjectURL(file)
  }
}

mobile_banner_edit.onchange = evt => {
        // alert('hello')
  const [file] = mobile_banner_edit.files
  if (file) {
    blah4.src = URL.createObjectURL(file)
  }
}
    $(document).ready(function () {
 
            $("#advertiseEditForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    
                    // image: "required",
                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    
                    // image: "Image field is required",
                },
                submitHandler: function(){
                    var form_data = new FormData($('#advertiseEditForm')[0]);
                    action_url = "{{ route('advertise.store') }}";
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
                                // $(el).parents('.cart-product-box-content').find('b[name=price]').text(fix_price*text);
                               // return false;
                                if (data.success) {
                                    $('.loader').css("visibility", "visible");
                                    window.location.href = "{{ route('advertise')}}";
                                }
                            },
                             error: function (errors) {
                                   // alert(errors);
                                    $('.loader').css("visibility", "hidden");
                                 var erroJson = JSON.parse(errors.responseText);
                                 console.log(erroJson.title);
                                   for (var err in erroJson) {
                            for (var errstr of erroJson[err])
                                  $("span#errorMessageBanner").css("display", "block");
                              $("span#errorMsgBanner").css("display", "block");

                              $("span#errorMsgBanner").html(errstr);
                              }
                               }
                        });
                }
            });
        });
</script>