<div class="modal-header">
    <h4 class="modal-title">{{ __('backend.EditCountryAdmin') }}</h4>
</div>
<form class="cmxform" id="subadminEditForm" method="post" action="" autocomplete="off">
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-sm-2 form-control-cms" style="padding-right: 16px !important">Full Name<span class="valid_field">*</span>
            </div>
            <div class="col-sm-10">
                <input type="text" name="fullname" id="fullname" class="form-control" onkeypress="return isNumberKey(event)" placeholder="Enter Name" value="{{ @$customerData->name }}">
                <span class="help-block" style="color: red;" id="editErrorMsgName" class='validate'></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!! __('backend.phone') !!} <span class="valid_field">*</span>
            </label>
            <div class="col-sm-10">
                <input type="text" name="phone" id="phone" maxlength="15"  class="form-control" placeholder="Enter Phone Number" value="{{ @$customerData->phone }}">
                <span style="color: red;" class="help-block" id="errorMsgPhone" class="validate"></span>
            </div>
        </div>
         <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!! __('backend.email') !!} <span class="valid_field">*</span>
            </label>
            <div class="col-sm-10">
                <input type="text" name="email" id="email" class="form-control" placeholder="Enter Email" value="{{ @$customerData->email }}">
                <span class="help-block" style="color: red;" id="editErrorMsgEmail" class='validate'></span>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2 form-control-cms">{{__('backend.country')}} <span class="valid_field">*</span></div>
            <div class="col-sm-10">
                <select name="country_id" id="country_id" class="form-control">
                    <option value="">Select Country</option>
                    @foreach ($countries as $country)
                    <option @if($customerData->country_id==$country->id){{ 'selected' }} @endif value="{{$country->id}}" {{(old('country_id', @$country->country_id ?: "") == $country->id) ? 'selected' : ''}}>
                        {{ucfirst($country->name)}}
                    </option>
                    @endforeach
                </select>
                <span class="help-block" style="color: red;" id="editErrorMsgCountry" class='validate'></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Profile Photo<span class="valid_field">*</span></label>
            <div class="col-sm-10">
                <img id="blah1" height="100" width="100" src="
								<?php
                                if (!empty($customerData->photo)) { ?>
                                       {{ asset('uploads/customer/' . $customerData->photo) }}
                                    
								<?php } else { ?>
                                        {{ asset('assets/dashboard/images/no_image_found.jpg') }}
                                    
								<?php }
                                ?>" alt="your image"  style="width:100px !important; height:100px !important;" />
                <input type="file" name="profile" id="profile" class="form-control" style="border: none; margin-left: -13px;">
                <span class="help-block" style="color: red;" id="editErrorMsgImage" class='validate'></span>
                <div>
                <small>
                    <i class="material-icons">&#xe8fd;</i>
                    Choose image .png, .jpg, .jpeg files only.
                </small>
                <br>
                <small>
                    <i class="material-icons">&#xe8fd;</i>
                    Recommended size 120(Width) x 180(Height).
                </small>
                <br>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="customer_id" id="customer_id" value="{{ @$customerData->id }}">
        <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i> Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
    </div>
</form>
<script type="text/javascript">
    function isNumberKey(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32) return false;
        return true;
    }
    profile.onchange = evt => {
        const [file] = profile.files;
        fileName = document.querySelector('#profile').value;
        extension = fileName.split('.').pop();
        document.querySelector('.output').textContent = extension;
        if (file) {
            blah1.src = URL.createObjectURL(file)
        }
    }
    $(document).ready(function() {
        $('#phone').on("input", function() {
            this.value = this.value.replace(/[^0-9\.]/g, '');
            $(this).val($(this).val().replace(/^\s+/g, ''));
        });
        $("#subadminEditForm").validate({
            submitHandler: function() {
                var form_data = new FormData($('#subadminEditForm')[0]);
                action_url = "{{ route('country-admin.store') }}";
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
                        $(".loader").fadeIn();
                        $('.loader').css("visibility", "visible");
                    },
                    success: function(data) {
                        if (data.success) {
                            $('.loader').css("visibility", "hidden");
                            window.location.href = "{{ route('country-admin') }}";
                        }
                    },
                    error: function(errors) {
                        $('.loader').css("visibility", "hidden");
                        var errors = errors.responseJSON;
                        $("span#editErrorMsgName,span#editErrorMsgEmail,span#editErrorMsgPhone,span#editErrorMsgImage,span#editErrorMsgCountry").text('');
                        if (errors.fullname) {
                            $("span#editErrorMsgName").text(errors.fullname[0]);
                        }
                        if (errors.email) {
                            $("span#editErrorMsgEmail").text(errors.email[0]);
                        }
                        if (errors.phone) {
                            $("span#editErrorMsgPhone").text(errors.phone[0]);
                        }
                        if (errors.profile) {
                            $("span#editErrorMsgImage").text(errors.profile[0]);
                        }
                        if (errors.country_id) {
                            $("span#editErrorMsgCountry").text(errors.country_id[0]);
                        }
                    }
                });
            }
        });
    });
</script>