<div class="modal-header">
    <h4 class="modal-title">{{ __('backend.editArea') }}</h4>
</div>
<form class="cmxform" id="areaEditForm" method="post" action="" autocomplete="off">
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-sm-4 form-control-cms">{{__('backend.region')}} <span class="valid_field">*</span></div>
            <div class="col-sm-8">
                <select name="region_id" id="region_id" class="form-control" value="{{@$editData->region_id}}">
                    <option value="">Select area</option>
                    @foreach ($areaData as $key => $value)
                        <option value="{{$value->id}} " {{ ($editData->region_id == $value->id) ? 'selected' : ''}} >{{ucfirst($value->title)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 form-control-label">Title [EN]<span class="valid_field">*</span></label>
            <div class="col-sm-8">
                <input type="text" name="title" id="title" class="form-control" placeholder="Title [EN]" onkeypress="return isNumberKey(event)" value="{{$editData->title}}">
                <span class="help-block" id="errorMessage" style="display:none">
                    <span style="color: red;display: none;" id="errorMsg" class='validate'></span>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 form-control-label">{!! __('Title [FR]') !!}<span class="valid_field">*</span></label>
            <div class="col-sm-8">
                <input type="text" name="title_fr" id="title_fr" class="form-control" placeholder="{!! __('Title [FR]') !!}" value="{{$editData->title_fr}}">
                <span style="color: red;display: none;" id="errorMsgtitlefr" class='validate'></span>
                <span class="help-block" id="errorMessagetitlefr" style="display:none">
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 form-control-label">Delivery Fee<span class="valid_field">*</span></label>
            <div class="col-sm-8">
                <input type="text" onkeypress="return isNumberBlock(event)" maxlength="7"  name="delivery_fee" id="delivery_fee" class="form-control" placeholder="20" value="{{$editData->delivery_fee}}">
                <span style="color: red;display: none;" id="errorMsgDeliveryFee" class='validate'></span>
                <span class="help-block" id="errorMsgDeliveryFee" style="display:none">
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 form-control-label">Purchase Amount<span class="valid_field">*</span></label>
            <div class="col-sm-8">
                <input type="text" onkeypress="return isNumberBlock(event)" maxlength="7"  name="delivery_amount" id="delivery_amount" class="form-control" placeholder="20" value="{{$editData->delivery_amount}}">
                <span style="color: red;display: none;" id="errorMsgDeliveryAmount" class='validate'></span>
                <span class="help-block" id="errorMsgDeliveryAmount" style="display:none">
                </span>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="area_id" id="area_id" value="{{$editData->id}}">
        <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i> Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
    </div>
    {{Form::close()}}
    <!-- </div> -->

    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $("#areaEditForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    region_id: {
                        required: true,
                    },
                    title: {
                        required: true,
                        maxlength: 30,

                    },
                    title_fr: {
                        required: true,
                        maxlength: 30,
                    },
                    delivery_fee: {                        
                        required: true,
                    },
                    delivery_amount: {                        
                        required: true,
                    },

                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    region_id: "Region filed is required.",
                    title: {
                        required: "Title field is required.",
                        maxlength: "Title field cannot exceed {0} characters.",

                    },
                    title_fr: {
                        required: "Title Fr field is required.",
                        maxlength: "Title Fr field cannot exceed {0} characters."
                    },
                    delivery_fee:{
                        required:"Delivery Fee is required.",
                    },
                      delivery_amount:{
                        required:"Delivery Fee is required.",
                    },

                },
                submitHandler: function() {
                    var form_data = new FormData($('#areaEditForm')[0]);
                    action_url = "{{ route('area.store') }}";
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
                                window.location.href = "{{ route('area')}}";
                            }
                        },
                        error: function(errors) {
                            // alert(errors);
                            // $('.loader').css("visibility", "none");
                            var erroJson = JSON.parse(errors.responseText);
                            console.log(erroJson);
                            for (var err in erroJson) {
                                for (var errstr of erroJson[err])
                                    // console.log(err);

                                $("span#errorMessage").css("display", "block");
                                $("span#errorMsg").css("display", "block");

                                $("span#errorMsg").html(errstr);
                            }
                        }
                    });
                }
            });
        });

        function isNumberBlock(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            // alert($(this).val())
            // evt.which.val().length
            if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                
                return false;
            return true;
        }
    </script>