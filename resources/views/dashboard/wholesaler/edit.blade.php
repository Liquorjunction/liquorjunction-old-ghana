<div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.EditWholesaler') }}</h4>
        </div>
        <form class="cmxform" id="wholesalerEditForm" method="post" action="" autocomplete="off">
        <div class="modal-body">
            <div class="form-group row">
                        <div class="col-sm-2 form-control-cms" style="display: flex;">{{__('backend.firstname')}} <span class="valid_field">*</span></div>
                        <div class="col-sm-10">
                            <input type="text" name="first_name" id="first_name" class="form-control" onkeypress="return isNumberKey(event)" placeholder="First Name" value="{{@$customerData->first_name}}">
                            <span class="help-block" id="errorMessageFirstname" style="display:none">
                                <span  style="color: red;display: none;" id="errorMsgFirstname" class='validate'></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2 form-control-cms" style="display: flex;">{{__('backend.lastname')}} <span class="valid_field">*</span></div>
                        <div class="col-sm-10">
                            <input type="text" name="last_name" id="last_name" class="form-control" onkeypress="return isNumberKey(event)" placeholder="Last Name" value="{{@$customerData->last_name}}">
                            <span class="help-block" id="errorMessageLastname" style="display:none">
                                <span  style="color: red;display: none;" id="errorMsgLastname" class='validate'></span>
                            </span>
                        </div>
                    </div>
           <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.email') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{@$customerData->email}}">
                            
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.phone') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone" maxlength="15" value="{{@$customerData->phone}}">
                            
                        </div>
                    </div>

                    <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Profile</label>
                            <div class="col-sm-6">
                                <?php
                                if (!empty($customerData->profile)) { ?>
                                    
                                <img id="blah" src="{{ asset('uploads/customer/'.$customerData->profile) }}" alt="your image" />
                                <?php }else{ ?>

                                <img id="blah" src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="your image" />
                                <?php }
                                ?>
                                <input type="file" name="profile" id="profile" class="form-control" style="border: none; margin-left: -13px;" accept="image/png, image/jpg, image/jpeg">
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .png, .jpg, .jpeg
                                </small>
                            </div>
                        </div>
                    
        </div>
        <div class="modal-footer">
            <input type="hidden" name="customer_id" id="customer_id" value="{{@$customerData->id}}">
          <button type="submit" class="btn btn-default btn btn-primary">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {{Form::close()}}
<script type="text/javascript">
    function isNumberKey(evt){ 
    //var e = evt || window.event;
    var keyCode = (evt.which) ? evt.which : evt.keyCode;
    if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
         
        return false;
            return true;
    
    }
    profile.onchange = evt => {
  const [file] = profile.files
  if (file) {
    blah.src = URL.createObjectURL(file)
  }
}

    $(document).ready(function () {
         $('#phone').on("input", function () {
            this.value = this.value.replace(/[^0-9\.]/g,''); 
            $(this).val($(this).val().replace(/^\s+/g, ''));
        });
 
            $("#wholesalerEditForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    first_name: "required",
                    last_name: "required",
                    email: {
                    required: true,
                    email: true
                    },
                    phone: {
                        required :true,
                        minlength:10
                    },
                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    first_name: " First name field is required",
                    last_name: " Last name field is required",
                   email: {
                        required: "Email field is required",
                        email: "separate"
                    },
                    phone: {
                        required : "Phone field is required",
                        minlength : "Minimum 10 digit required"
                    },
                },
                submitHandler: function(){
                    var form_data = new FormData($('#wholesalerEditForm')[0]);
                    action_url = "{{ route('wholesaler.store') }}";
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
                                    // $('.loader').css("visibility", "visible");
                                },
                            success: function(data){
                                // $(el).parents('.cart-product-box-content').find('b[name=price]').text(fix_price*text);
                               // return false;
                                if (data.success) {
                                    $('.loader').css("visibility", "visible");
                                    window.location.href = "{{ route('wholesaler')}}";
                                }
                            },
                            error: function (errors) {
                                   // alert(errors);
                                    // $('.loader').css("visibility", "none");
                                 var erroJson = JSON.parse(errors.responseText);
                                 console.log(erroJson);
                                   for (var err in erroJson) {
                            for (var errstr of erroJson[err])
                                 // console.log(err);
                                  if (err == "first_name") {
                            $("span#errorMessageLastname").css("display", "none");
                              $("span#errorMsgLastname").css("display", "none");
                            $("span#errorMessageFirstname").css("display", "block");
                              $("span#errorMsgFirstname").css("display", "block");

                              $("span#errorMsgFirstname").html(errstr);
                          }else{
                                    $("span#errorMessageFirstname").css("display", "none");
                              $("span#errorMsgFirstname").css("display", "none");
                                    $("span#errorMessageLastname").css("display", "block");
                              $("span#errorMsgLastname").css("display", "block");

                              $("span#errorMsgLastname").html(errstr);
                          }
                              }
                               }
                        });
                }
            });
        });
</script>