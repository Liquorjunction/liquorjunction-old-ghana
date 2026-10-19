<div class="modal-header">
    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
    <h4 class="modal-title">Edit Sub Admin</h4>
</div>
<form class="cmxform" id="subadminEditForm" method="post" action="" autocomplete="off">
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-sm-2 form-control-cms" style="display: flex;">{{ __('backend.name') }} <span
                    class="valid_field">*</span></div>
            <div class="col-sm-10">
                <input type="text" name="fullname" id="fullname" class="form-control"
                    onkeypress="return isNumberKey(event)" placeholder="Enter Name" value="{{ @$customerData->name }}">
                    <span style="color: red;" id="errorMsgName" class='validate'></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!! __('backend.phone') !!} <span
                    class="valid_field">*</span></label>
            <div class="col-sm-10">
                <input type="text" name="phone" id="phone" maxlength="15" class="form-control"
                    placeholder="Enter Phone Number" value="{{ @$customerData->phone }}">
                    <span style="color: red;" id="errorMsgPhone" class='validate'></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!! __('backend.email') !!} <span
                    class="valid_field">*</span></label>
            <div class="col-sm-10">
                <input type="text" name="email" id="email" class="form-control" placeholder="Enter Email"
                    value="{{ @$customerData->email }}">
                    <span style="color: red;" id="errorMsgEmail" class='validate'></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Profile Photo <span class="valid_field">*</span></label>
            <div class="col-sm-10">
                <img id="blah"
                    src="<?php
                                if (!empty($customerData->photo)) { ?>
                                       {{ asset('uploads/customer/' . $customerData->photo) }}
                                    <?php } else { ?>
                                        {{ asset('assets/dashboard/images/no_image_found.jpg') }}
                                    <?php }
                                    ?>"
                    alt="your image" style="width: 100px!important; height:100px !important;" />

                <input type="file" name="profile" id="profile" class="form-control"
                    style="border: none; margin-left: -13px;">
                    <span class="help-block" style="color: red;" id="errorMsgImage" class='validate'></span>
                    <div>
                        <small>
                            <i class="material-icons">&#xe8fd;</i>
                            choose image .png, .jpg, .jpeg files only.
                        </small>
                        <br>
                        <small>
                            <i class="material-icons">&#xe8fd;</i>
                            Recommended size 120(Width) x 180(Height).
                        </small>
                    </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <input type="hidden" name="customer_id" id="customer_id" value="{{ @$customerData->id }}">
        <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i> Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
    </div>
    {{ Form::close() }}
    <script type="text/javascript">
        function isNumberKey(evt) {
            //var e = evt || window.event;
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

                return false;
            return true;
        }

        profile.onchange = evt => {
            const [file] = profile.files
            fileName = document.querySelector('#profile').value;
            extension = fileName.split('.').pop();
            document.querySelector('.output').textContent = extension;
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }
        $(document).ready(function() {
            $("#subadminEditForm").validate({
                submitHandler: function() {
                    var form_data = new FormData($('#subadminEditForm')[0]);
                    action_url = "{{ route('subadmin.store') }}";
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
                            //$(".loader").fadeIn();
                            $('.loader').css("visibility", "visible");
                        },
                        success: function(data) {
                            $('.loader').css("visibility", "hidden");
                            if (data.success) {
                                window.location.href = "{{ route('subadmin') }}";
                            }
                        },
                        error: function(errors) {
                            $('.loader').css("visibility", "hidden");

                            var errors = errors.responseJSON;
                            $("span#errorMsgName,span#errorMsgEmail,span#errorMsgPhone,span#errorMsgImage")
                                .text('');
                            if (errors.fullname) {
                                $("span#errorMsgName").text(errors.fullname[0]);
                            }
                            if (errors.email) {
                                $("span#errorMsgEmail").text(errors.email[0]);
                            }
                            if (errors.phone) {
                                $("span#errorMsgPhone").text(errors.phone[0]);
                            }
                            if (errors.profile) {
                                $("span#errorMsgImage").text(errors.profile[0]);
                            }
                        }
                    });
                }
            });
        });
    </script>
