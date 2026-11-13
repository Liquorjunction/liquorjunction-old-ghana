<div class="modal-header">
    <h4 class="modal-title">{{ __('backend.EditCustomer') }}</h4>
</div>
<form class="cmxform" id="customerEditForm" method="post" action="" autocomplete="off">
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-sm-2 form-control-cms" style="display: flex;">{{__('backend.firstname')}} <span class="valid_field">*</span></div>
            <div class="col-sm-10">
                <input type="text" name="first_name" id="first_name" onkeypress="return isNumberKey(event)" class="form-control" placeholder="First Name" value="{{@$customerData->first_name}}">
                <span class="help-block" id="errorMessageFirstname" style="display:none">
                    <span style="color: red;display: none;" id="errorMsgFirstname" class='validate'></span>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2 form-control-cms" style="display: flex;">{{__('backend.lastname')}} <span class="valid_field">*</span></div>
            <div class="col-sm-10">
                <input type="text" name="last_name" id="last_name" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Last Name" value="{{@$customerData->last_name}}">
                <span class="help-block" id="errorMessageLastname" style="display:none">
                    <span style="color: red;display: none;" id="errorMsgLastname" class='validate'></span>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!! __('backend.email') !!} <span class="valid_field">*</span></label>
            <div class="col-sm-10">
                <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{@$customerData->email}}">
                <span class="help-block" id="errorMessageEmail" style="display:none">
                    <span style="color: red;display: none;" id="errorMsgEmail" class='validate'></span>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!! __('backend.phone') !!} <span class="valid_field">*</span></label>
            <div class="col-sm-3">
                <select style="font-size:15px !important;" name="phone_code" id="phone_code" class="form-control">
                    @foreach ($phonecode as $value)
                    <option value="{{$value->phonecode}}" {{ ($customerData->phone_code == $value->phonecode) ? 'selected' : ''}}>+{{$value->phonecode.' ('.$value->shortname.')' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-7">
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" value="{{@$customerData->phone}}">
                <span class="help-block" id="errorMessagePhone" style="display:none">
                    <span style="color: red;display: none;" id="errorMsgPhone" class='validate'></span>
                </span>
            </div>
        </div>

        <!-- <div class="form-group row">
            <label class="col-sm-2 form-control-label">Profile Photo <span class="valid_field">*</span></label> 
            <div class="col-sm-9">
                <?php
                // if (!empty($customerData->profile)) { 
                    ?>

                    <img id="blah" src="{{ asset('uploads/customer/'.$customerData->profile) }}" height="100" width="100"  style="width:100px !important; height:100px !important;" />
                <?php
            //  } else { 
                ?>

                    <img id="blah" src="http://127.0.0.1:8000/uploads/contacts/profile.jpg" height="100" width="100"  style="width:100px !important; height:100px !important;" />
                <?php
                //  }
                ?>
                <input type="file" name="profile" id="profile" class="form-control" style="border: none; margin-left: -13px;">
                <span class="help-block" id="errorMessagePhoto" style="display:none">
                    <span style="color: red;display: none;" id="errorMsgPhoto" class='validate'></span>
                </span>
                <div>
                <small>
                    <i class="material-icons">&#xe8fd;</i>
                    Choose image, .png, .jpg, .jpeg files only.
                </small>
                </div>
            </div>
        </div> -->

    </div>

    <div class="modal-footer">
        <input type="hidden" name="customer_id" id="customer_id" value="{{@$customerData->id}}">
        <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i> Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
    </div>
    
    {{Form::close()}}
    <script type="text/javascript">
        function isNumberKey(evt) {
            //var e = evt || window.event;
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

                return false;
            return true;

        }
        // profile.onchange = evt => {
        //     const [file] = profile.files
        //     fileName = document.querySelector('#profile').value;
        //     extension = fileName.split('.').pop();
        //     document.querySelector('.output').textContent = extension;
        //     if (file) {
        //         console.log(file);
        //         blah.src = URL.createObjectURL(file)
        //     }
        // }

        $(document).ready(function() {

            $('#phone').on("input", function() {
                this.value = this.value.replace(/[^0-9\.]/g, '');
                $(this).val($(this).val().replace(/^\s+/g, ''));
            });

            $("#customerEditForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    first_name: {
                        required: true,
                        maxlength: 30,
                    },
                    last_name: {
                        required: true,
                        maxlength: 30,
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        minlength: 8,
                        maxlength: 15,
                    },
                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    first_name: {
                        required: "First name field is required",
                        maxlength: "First name may not be greater than 30 characters."

                    },
                    last_name: {
                        required: "Last name field is required",
                        maxlength: "Last name may not be greater than 30 characters.",
                    },
                    email: {
                        required: "Email field is required",
                        email: "Please enter a valid email"
                    },
                    phone: {
                        required: "Phone number field is required",
                        minlength: "Phone number field should appear 8 to 15 digits",
                        maxlength: "Phone number field should appear 8 to 15 digits"

                    },
                },
                submitHandler: function() {
                    var form_data = new FormData($('#customerEditForm')[0]);
                    action_url = "{{ route('customer.store') }}";
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
                        beforeSend: function() {
                            // $(".loader").fadeIn();
                            // $('.loader').css("visibility", "visible");
                        },
                        success: function(data) {
                            // $(el).parents('.cart-product-box-content').find('b[name=price]').text(fix_price*text);
                            // return false;
                            if (data.success) {
                                $('.loader').css("visibility", "visible");
                                window.location.href = "{{ route('customer')}}";
                            }
                        },
                        error: function(errors) {
                            // alert(errors);
                            // $('.loader').css("visibility", "none");
                            var erroJson = JSON.parse(errors.responseText);
                            for (var err in erroJson) {
                                for (var errstr of erroJson[err])
                                    // console.log(err);
                                    if (err == "first_name") {
                                        $("span#errorMessageLastname").css("display", "none");
                                        $("span#errorMessageEmail").css("display", "none");
                                        $("span#errorMessagePhone").css("display", "none");
                                        $("span#errorMessagePhoto").css("display", "none");
                                        $("span#errorMessageFirstname").css("display", "block");
                                        $("span#errorMsgFirstname").css("display", "block");
                                        $("span#errorMsgFirstname").html(errstr);
                                    } else if (err == "email") {
                                    $("span#errorMessageLastname").css("display", "none");
                                    // $("span#errorMessagePhone").css("display", "none");
                                    $("span#errorMessageFirstname").css("display", "none");
                                    $("span#errorMessageEmail").css("display", "block");
                                    $("span#errorMessagePhoto").css("display", "none");
                                    $("span#errorMsgEmail").css("display", "block");
                                    $("span#errorMsgEmail").html(errstr);
                                } else if (err == "phone") {
                                    $("span#errorMessageLastname").css("display", "none");
                                    // $("span#errorMessageEmail").css("display", "none");
                                    $("span#errorMessageFirstname").css("display", "none");
                                    $("span#errorMessagePhone").css("display", "block");
                                    $("span#errorMsgPhone").css("display", "block");
                                    $("span#errorMessagePhoto").css("display", "none");
                                    $("span#errorMsgPhone").html(errstr);
                                }
                                //  else if (err == "profile") {
                                //     $("span#errorMessageLastname").css("display", "none");
                                //     // $("span#errorMessageEmail").css("display", "none");
                                //     $("span#errorMessageFirstname").css("display", "none");
                                //     $("span#errorMessagePhone").css("display", "none");
                                //     $("span#errorMsgPhone").css("display", "none");
                                //     $("span#errorMessagePhoto").css("display", "block");
                                //     $("span#errorMsgPhoto").css("display", "block");
                                //     $("span#errorMsgPhoto").html(errstr);

                                // }
                                 else {
                                    $("span#errorMessageFirstname").css("display", "none");
                                    $("span#errorMessageEmail").css("display", "none");
                                    $("span#errorMessagePhone").css("display", "none");
                                    $("span#errorMessageLastname").css("display", "block");
                                    $("span#errorMsgLastname").css("display", "block");
                                    // $("span#errorMsgPhoto").css("display", "block");
                                    $("span#errorMsgLastname").html(errstr);
                                }
                            }
                        }
                    });
                }
            });
        });
    </script>