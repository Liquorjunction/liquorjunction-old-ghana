<div class="modal-header">
    <h4 class="modal-title">{{ __('backend.EditSubcategory') }}</h4>
</div>
<form class="cmxform" id="subcategoryEditForm" method="post" action="" autocomplete="off">
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-sm-2 form-control-cms">{{__('backend.category')}}<span class="valid_field">*</span></div>
            <div class="col-sm-10">
                <select name="category_id" id="category_id" class="form-control" value="{{@$subcategoryData->category_id}}">
                    <option value="">Select category</option>
                    @foreach ($categories as $key => $value)
                    <option value="{{$value->id}}" {{ ($subcategoryData->category_id == $value->id) ? 'selected' : ''}}>{{ucfirst($value->title)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!! __('backend.title') !!} [EN]<span class="valid_field">*</span></label>
            <div class="col-sm-10">
                <input type="text" name="title" id="title" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Title [EN]" value="{{@$subcategoryData->title}}">
                <span class="help-block" id="errorMessage" style="display:none">
                    <span style="color: red;display: none;" id="errorMsg" class='validate'></span>
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!! __('backend.title') !!} [FR]<span class="valid_field">*</span></label>
            <div class="col-sm-10">
                <input type="text" name="title_fr" id="title_fr" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Title [FR]" value="{{@$subcategoryData->title_fr}}">
                <span class="help-block" id="errorMessage" style="display:none">
                    <span style="color: red;display: none;" id="errorMsgTitlefr" class='validate'></span>
                </span>
            </div>
        </div>


    </div>
    <div class="modal-footer">
        <input type="hidden" name="subcategory_id" id="subcategory_id" value="{{@$subcategoryData->id}}">
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

        $(document).ready(function() {

            $("#subcategoryEditForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    title: "required",
                    title_fr: "required",
                    description: "required",
                    description_fr: "required",
                    category_id: "required",
                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    title: "Title field is required",
                    title_fr: "Title Fr field is required",
                    description: "Description field is required",
                    description_fr: "Description_FR field is required",
                    category_id: "Category field is required",
                },
                submitHandler: function() {
                    var form_data = new FormData($('#subcategoryEditForm')[0]);
                    action_url = "{{ route('subcategory.store') }}";
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
                                window.location.href = "{{ route('subcategory')}}";
                            }
                        },
                        error: function(errors) {
                            // alert(errors);
                            // $('.loader').css("visibility", "none");
                            var erroJson = JSON.parse(errors.responseText);
                            console.log(erroJson.title);
                            for (var err in erroJson) {
                                for (var errstr of erroJson[err])
                                    // console.log(err);

                                    $("span#errorMessage").css("display", "block");
                                $("span#errorMsg").css("display", "block");
                                // $("span#errorMsgimage").css("display", "block");


                                // $("span#errorMsgimage").html(erroJson.image);
                                $("span#errorMsg").html(erroJson.title);
                            }
                        }
                    });
                }
            });
        });
    </script>