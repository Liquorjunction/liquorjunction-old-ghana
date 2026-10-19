<div class="modal-header">
    <h4 class="modal-title">{{ __('backend.editRegion') }}</h4>
</div>
<form class="cmxform" id="regionEditForm" method="post" action="" autocomplete="off">
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-sm-2 form-control-cms">{{__('backend.country')}} <span class="valid_field">*</span></div>
            <div class="col-sm-10">
                <select name="country_id" id="country_id" class="form-control" value="{{$editData->country_id}}">
                    <option value="">Select Country</option>
                    @foreach ($countryData as $key => $value)
                    <option value="{{$value->id}} " {{ ($editData->country_id == $value->id) ? 'selected' : ''}}>{{ucfirst($value->name)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!! __('Title') !!} [EN]<span class="valid_field">*</span></label>
            <div class="col-sm-10">
                <input type="text" name="title" id="title" class="form-control" placeholder="Title [EN]" onkeypress="return isNumberKey(event)" value="{{$editData->title}}">
                <span class="help-block" id="errorMessage" style="display:none">
                    <span style="color: red;display: none;" id="errorMsgtitle" class='validate'></span>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Title [FR]<span class="valid_field">*</span></label>
            <div class="col-sm-10">
                <input type="text" name="title_fr" id="title_fr" class="form-control" placeholder="Title [FR]" value="{{$editData->title_fr}}">
                <span style="color: red;display: none;" id="errorMsgtitlefr" class='validate'></span>
                <span class="help-block" id="errorMessagetitlefr" style="display:none">
                </span>

            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="region_id" id="region_id" value="{{$editData->id}}">
        <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i>  Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
    </div>
    {{Form::close()}}
    <!-- </div> -->

    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $("#regionEditForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    country_id: {
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

                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    country_id: "Country filed is required.",
                    title: {
                        required: "Title field is required.",
                        maxlength: "Title field cannot exceed {0} characters.",

                    },
                    title_fr: {
                        required: "Title Fr field is required.",
                        maxlength: "Title Fr field cannot exceed {0} characters."
                    },

                },
                submitHandler: function() {
                    var form_data = new FormData($('#regionEditForm')[0]);
                    action_url = "{{ route('region.store') }}";
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
                                window.location.href = "{{ route('region')}}";
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
                                $("span#errorMsgtitle").css("display", "block");


                                $("span#errorMsgtitle").html(erroJson.title);
                            }
                        }
                    });
                }
            });
        });
    </script>