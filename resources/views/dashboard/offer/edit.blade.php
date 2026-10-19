<div class="modal-header">
    <h4 class="modal-title">Edit Offer</h4>
</div>

<div id="editConflictError" class="alert alert-danger" style="display:none;"></div>
<form class="cmxform" id="offerEditForm" method="post" action="" autocomplete="off">
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-5 form-control-label">Offer Type<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <select name="offer_types" id="offer_types" class="form-control">
                    <option value="percentage" {{ (isset($editData->offer_type) && $editData->offer_type == 'percentage') ? 'selected' : '' }}>Percentage</option>
                    <option value="flat" {{ (isset($editData->offer_type) && $editData->offer_type == 'flat') ? 'selected' : '' }}>Flat</option>
                </select>                
                <span style="color: red;display: none;" id="errorMsgType" class='validate'></span>
            </div>
        </div>  
        <div class="form-group row">
            <label class="col-sm-5 form-control-cms">Applicable On<span class="valid_field">*</span></label>
            <div class="col-sm-7">
                <select name="type" id="type" onchange="showHide()" class="form-control" value="">
                    <option value="">Select type</option>
                    <option {{ (isset($editData->product_type) && $editData->product_type == 1) || old('product_type') == 1 ? 'selected' : '' }} value="1">Brand</option>
                    <option  {{ (isset($editData->product_type) && $editData->product_type == 2)  || old('product_type') == 2 ? 'selected' : '' }} value="2">Category</option>
                    <option {{ (isset($editData->product_type) && $editData->product_type == 3)  || old('product_type') == 3 ? 'selected' : '' }}  value="3">Product</option>
                </select>
                @if ($errors->has('type'))
                <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('type') }}</span>
                </span>
                @endif
            </div>
        </div>
        <div class="form-group row brand">
            <div class="col-sm-5 form-control-cms ">Select Brand<span class="valid_field">*</span></div>
            <div class="col-sm-7">
                <select name="brand_id" id="brand_id" class="form-control" value="">
                    <option value="">Select Brand</option>
                    @foreach ($brand as $key => $value)
                    <option value="{{ $value->id }}" {{ ($editData->brand_id == $value->id) ? 'selected' : ''}}>
                        {{ ucfirst($value->title) }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row category">
            <div class="col-sm-5 form-control-cms ">Select {{ __('backend.category') }}<span class="valid_field">*</span></div>
            <div class="col-sm-7">
                <select name="category_ids" id="category_ids" class="form-control" value="{{ old('category_ids', @$editData->category_id ?: '') }}" onchange="getSubCatList(this)" >
                    <option value="">Select category</option>
                    @foreach ($categories as $key => $value)
                    <option value="{{ $value->id }}" {{ ($editData->category_id == $value->id) ? 'selected' : ''}}>
                        {{ ucfirst($value->title) }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row subcategory" style="display: none;">
            <div class="col-sm-5 form-control-cms">Select Subcategory </div>
            <div class="col-sm-7">
                <select name="subcategory_ids" id="subcategory_ids" class="form-control" value="{{ old('subcategory_ids', @$editData->subcategory_id) }}">
                    <option value="">Select Subcategory</option>
                    @if(!empty($subcategories))
                    @foreach($subcategories as $key => $value)
                    <option value="{{$value->id}}" {{ ($editData->subcategory_id == $value->id) ? 'selected' : ''}}>{{ucfirst($value->title)}}</option>
                    @endforeach
                    @endif
                </select>
                @if ($errors->has('subcategory_ids'))
                    <span class="help-block">
                        <span style="color: red;"
                            class='validate'>{{ $errors->first('subcategory_ids') }}</span>
                    </span>
                @endif
            </div>
        </div>
        <div class="form-group row product">
                <div class="col-sm-5 form-control-cms ">Select Product<span class="valid_field">*</span></div>
                <div class="col-sm-7">
                    <select name="product_id" id="product_id" class="form-control" value="">
                        <option value="">Select product</option>
                        @foreach ($product as $key => $value)
                        <option value="{{ $value->id }}"  {{ ($editData->product_id == $value->id) ? 'selected' : ''}}>
                            {{ ucfirst($value->product_name) }}
                        </option>
                        @endforeach
                    </select>
                </div>
        </div>
        <div class="form-group row discountAmount">
            <label class="col-sm-5 form-control-label">Discount Amount<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="number" name="dis_amount" id="dis_amount" class="form-control" placeholder="100" value="{{ $editData->dis_amount }}" required>
                <span style="color: red;display: none;" id="errorMsgAmount" class='validate'></span>
            </div>
        </div>
        <div class="form-group row discountPercentage">
            <label class="col-sm-5 form-control-label">Discount Percentage(%)<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="number" name="dis_percentage" id="dis_percentage" class="form-control" placeholder="100" value="{{ $editData->dis_amount }}" required>
                <span style="color: red;display: none;" id="errorMsgPercentage" class='validate'></span>
            </div>
        </div>

        {{-- <div class="form-group row">
            <label class="col-sm-5 form-control-label">Minimum Amount<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="number" name="min_amount" id="min_amount" class="form-control" placeholder="100" value="{{ $editData->min_amount }}" required>
                <span style="color: red;display: none;" id="errorMsgMin" class='validate'></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-5 form-control-label">Maximum Amount<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="number"  name="max_amount"  id="max_amount" class="form-control"
                 placeholder="200"
                    value="{{ $editData->max_amount }}" required>
                <span style="color: red;display: none;" id="errorMsgMax" class='validate'></span>
            </div>
        </div> --}}

        <div class="form-group row">
            <label class="col-sm-5 form-control-label">Expiry Date<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="date"  name="expiry_date"  id="expiry_date" class="form-control"
                 placeholder="200"
                    value="{{$editData->expiry_date }}" required>
                <span style="color: red;display: none;" id="errorMsgExpiry" class='validate'></span>
            </div>
        </div>

        {{-- <div class="form-group row">
            <label class="col-sm-5 form-control-label">Usage Limit<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="number"  name="total_usage"  id="total_usage" class="form-control"
                 placeholder="15"
                    value="{{ $editData->total_usage }}" required>
                <span style="color: red;display: none;" id="errorMsgUsage" class='validate'></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-5 form-control-label">Max Users<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="number"  name="max_users"  id="max_users" class="form-control"
                    value="{{$editData->max_users }}" placeholder="25" required>
                <span style="color: red;display: none;" id="errorMsgUsers" class='validate'></span>
            </div>
        </div> --}}

        {{-- <div class="form-group row">
            <label class="col-sm-4 form-control-cms">Title<span
                class="valid_field">*</span></label>
            <div class="col-sm-8">
                <input type="text" name="title" id="title" class="form-control" placeholder="Offer Title" value="{{$editData->title }}">
                <span style="color: red;display: none;" id="errorMsgTitle" class='validate'></span>
            </div>
        </div> --}}

        <div class="form-group row">
            <label class="col-sm-5 form-control-cms">Button Template<span
                class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="text" name="template" id="template" class="form-control" placeholder="Button Template Text" value="{{$editData->template }}">
                <span style="color: red;display: none;" id="errorMsgTemplate" class='validate'></span>
            </div>
        </div>

          <div class="form-group row">
            <label class="col-sm-5 form-control-cms">Custom URL<span
                class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="text" name="custom_url" id="custom_url" class="form-control" placeholder="Custom Url" value="{{$editData->custom_url }}">
                <span style="color: red;display: none;" id="errorMsgUrl" class='validate'></span>
            </div>
        </div>

        {{-- <div class="form-group row">
            <label class="col-sm-4 form-control-cms">Description</label>
            <div class="col-sm-8">
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Offer Description...">{{$editData->description }}</textarea>
                <span style="color: red;display: none;" id="errorMsgDescription" class='validate'></span>
            </div>
        </div> --}}
        <div class="form-group row">
            <label class="col-sm-5 form-control-label">Images <span class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="file" name="offer_image[]" id="offer_image" class="form-control" accept="image/*" placeholder="Offer Images" multiple>
                <span class="help-block" id="offer_image_input_error">

                    @if (!empty(@$errors) && @$errors->has('offer_image.*'))
                    <span style="color: red;" class='validate'>{{ is_string($errors->first('offer_image.*'))?$errors->first('offer_image.*'):@$errors->first('offer_image.*')[0] }}
                    </span>
                    @endif
                    <span style="color: red;" class='validate'>{{ $errors->first('offer_image') }}</span>
                </span>
                <div>
                    <small>
                        <i class="material-icons">&#xe8fd;</i>
                        Choose maximum of 4 images, .png, .jpg, .jpeg files only.
                    </small>
                    <br>
                    <small>
                        <i class="material-icons">&#xe8fd;</i>
                        Recommended size 480(Width) x 520(Height).
                    </small>
                    <br>
                    <small>
                        <i class="material-icons">&#xe8fd;</i>
                        You can select multiple images by pressing CTRL + Select Image.
                    </small>
                </div>
            </div>
        </div>

        @if ($editData->get_offer_images->count() > 0)
            <div class="form-group row">
                <label class="col-sm-4 form-control-label"></label>
                <div class="col-sm-8">
                    <div class="mt-1 text-center old_images box p-a-xs">
                        <h3>Old Images</h3>
                        <div class="old-images-div">
                            <div class="row">
                                @foreach ($editData->get_offer_images as $key => $image)
                                <div class="col-sm-3 offer_images_old">
                                    <div id="offer_photo_{{ $image->id }}">
                                        <img src="{{ asset('uploads/offers/' . $image->image) }}" alt="Offer Image" style="height:100px; width:100px;padding-right: 10px;">
                                        <br>
                                        <br>
                                    <div class="delete m-t-xs" style="margin-top: 10px !important;margin-bottom: 10px !important;">
                                        <a onclick="deleteImage(this)" data-image_id="{{ $image->id }}" class="btn btn-sm btn-default btndeleteprofile">{!! __('backend.delete') !!}</a>
                                    </div>
                                </div>
                                <div id="undo_{{ $image->id }}" class="col-sm-4 p-a-xs" style="display: none">
                                    <a onclick="undoDeleteImage(this)" data-image_id="{{ $image->id }}">
                                        <i class="material-icons">&#xe166;</i> {!! __('backend.undoDelete') !!}
                                    </a>
                                </div> {!! Form::checkbox('deleted_image[]', $image->id, '', [
                                'class' => 'hidden-checkboxes',
                                'id' => 'is_deleted_' . $image->id,
                                'style' => 'display:none',
                                ]) !!}
                                <input type="hidden" value="{{ $image->id }}" id="images_id_{{ $image->id }}" name="images_ids[]">
                            </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        @endif
    
      
        <div class="form-group row">
            <label class="col-sm-4 form-control-label"></label>
            <div class="col-sm-8">
                <div class="mt-1 text-center uploaded_image box p-a-xs" style="display: none">
                    <h3>New Uploaded Images</h3>
                    <div class="images-preview-div">

                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <input type="hidden" name="offer_id" id="offer_id" value="{{$editData->id}}">
        <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i> Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
    </div>
{{Form::close()}}
    <!-- </div> -->

<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script>
       $(document).ready(function() {
        showHide();
    });

    function showHide() {
        var type = $("#type").val();
        $('.category').hide();
        $('.product').hide();
        $('.brand').hide();
        $('.subcategory').hide();
        if (type != '' && type != null) {
            if (type == '1') {
                $('.category').hide();
                $('.product').hide();
                $('.brand').show();
                $('.subcategory').hide();
            } else if(type == '2') {
                $('.category').show();
                $('.product').hide();
                $('.brand').hide();
                $('.subcategory').show();
            }else{
                $('.category').hide();
                $('.product').show();
                $('.brand').hide();
                $('.subcategory').hide();
            }
        }
    }
</script>

<script>
      $(document).ready(function(e) {
            var offerform = $("#offerEditForm").validate({
            
                rules: {
                    offer_types: {
                        required: true,
                    },
                    product_type: {
                        required: true,
                    },
                    brand_id: {
                        required: function () {
                            return $("#type").val() == "1";
                        }
                    },
                    category_ids: {
                        required: function () {
                            return $("#type").val() == "2";
                        }
                    },
                    product_id: {
                        required: function () {
                            return $("#type").val() == "3";
                        }
                    },
                    dis_amount: {
                        required: function () {
                            return $("#dis_percentage").val() === "";
                        },
                        number: true,
                        min: 0
                    },
                    dis_percentage: {
                        required: function () {
                            return $("#dis_amount").val() === "";
                        },
                        number: true,
                        min: 0
                    },
                    // min_amount: {
                    //     required: true,
                    //     number: true,
                    //     min: 0
                    // },
                    // max_amount: {
                    //     required: true,
                    //     number: true,
                    //     min: 0,
                    //     greaterThan: true // no param passed
                    // },
                    expiry_date: {
                        required: true,
                        date: true,
                        futureDate: true
                    },
                    // total_usage: {
                    //     required: true,
                    //     number: true,
                    //     min: 1
                    // },
                    // max_users: {
                    //     required: true,
                    //     number: true,
                    //     min: 1
                    // },
                    template: {
                        required: true
                    },
                    custom_url: {
                        required: true
                    },
                    offer_image: {
                        required: true,
                        maxFileSize: 2 * 1024 * 1024, 
                        mimetypes: ["image/jpeg", "image/png", "image/jpg"], 
                        maxImages: 4 
                    }
                },
                messages: {
                    offer_types: {
                        required: "Offer Type Field is required.",
                    },
                    product_type: {
                        required: "Product Type is required."
                    },
                    brand_id: {
                        required: "Brand is required."
                    },
                    category_ids: {
                        required: "Category is required."
                    },
                    product_id: {
                        required: "Product is required."
                    },
                    dis_amount: {
                        required: "Discount Amount Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    dis_percentage: {
                        required: "Discount Percentage Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    // min_amount: {
                    //     required: "Minimum Amount Field is required.",
                    //     number: "Please enter a valid number.",
                    //     min: "Amount must be greater than or equal to 0."
                    // },
                    // max_amount: {
                    //     required: "Maximum Amount Field is required.",
                    //     number: "Please enter a valid number.",
                    //     min: "Amount must be greater than or equal to 0.",
                    //     greaterThan: "Maximum amount must be greater than minimum amount."
                    // },
                    expiry_date: {
                        required: "Expiry Date Field is required.",
                        date: "Enter a valid date.",
                        futureDate: "Expiry date must be in the future."
                    },
                    // total_usage: {
                    //     required: "Total Usage Field is required.",
                    //     number: "Please enter a valid number.",
                    //     min: "Must be at least 1"
                    // },
                    // max_users: {
                    //     required: "Maximum User Field is required.",
                    //     number: "Please enter a valid number.",
                    //     min: "Must be at least 1"
                    // },
                    template: {
                        required: "Button Template Field is required.",
                    },
                    custom_url:{
                        required: "Custom Url Field is required.",
                    },
                    offer_image: {
                        required: "The image field is required.",
                        maxFileSize: "The image should be less than 2 MB.",
                        mimetypes: "The images must be in .png, .jpg or .jpeg format.",
                        maxImages: "You can upload a maximum of 4 images."
                    }
                },
                submitHandler: function() {
                    if (!validateImageUpload()) {
                        return false;
                    }

                    var form_data = new FormData($('#offerEditForm')[0]);
                    action_url = "{{ route('offerstore') }}";
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
                            // $('.loader').css("visibility", "visible");
                        },
                        success: function(data) {
                            if (data.success) {
                                 $('.loader').css("visibility", "visible");
                                window.location.href = "{{ route('offer') }}";
                            }
                        },
                        error: function(errors) {
                            $('.loader').css("visibility", "hidden");
                            // var erroJson = JSON.parse(errors.responseText);
                            // for (var err in erroJson) {
                            //     console.log(erroJson);
                            //     for (var errstr of erroJson[err])
                            //     $("span#errorMessage").css("display", "block");
                            //     $("span#errorMsgTitle").css("display", "block");
                            //     $("span#errorMsgTitle").html(erroJson.title);

                            // }


                             // Clear any previous errors
                            $('.validate').text('').hide(); // Hide all validation error spans
                            $('#editConflictError').hide().html(''); // Hide the conflict error div

                            let erroJson = errors.responseJSON;

                            // Loop through each field error
                            $.each(erroJson, function(field, messages) {
                                const message = messages[0];

                                if (field === 'conflict') {
                                    // Show the conflict error in a special div
                                    $('#editConflictError').html(message).show();
                                } else {
                                    // Show other errors in their corresponding <span id="errorMsg<Field>">
                                    let errorSpan = $(`#errorMsg${capitalizeFirstLetter(field)}`);
                                    if (errorSpan.length) {
                                        errorSpan.text(message).show();
                                    } else {
                                        console.warn(`No error span found for field: ${field}`);
                                    }
                                }
                            });
                        }
                    });
                }
            });

            $('#type').change(function () {
                $('#brand_id, #category_ids, #product_id').valid();
            });


            // $.validator.addMethod("greaterThan", function (value, element) {
            //     var minValRaw = $(element.form).find('[name="min_amount"]').val();
            //     var maxValRaw = value;

            //     var minVal = parseFloat(minValRaw);
            //     var maxVal = parseFloat(maxValRaw);

            //     if (isNaN(minVal) || isNaN(maxVal)) return true;

            //     return maxVal > minVal;
            // }, "Maximum amount must be greater than minimum amount.");




            // $('#min_amount').on('keyup change blur', function () {
            //     $('#max_amount').valid();
            // });


            $.validator.addMethod("futureDate", function(value, element) {
                var now = new Date();
                var inputDate = new Date(value);
                return inputDate > now;
            }, "Date must be in the future.");

            $('#myModal').on('hidden.bs.modal', function() {
                offerform.resetForm();
                $('#myModal form')[0].reset();
            })

            $.validator.addMethod("maxFileSize", function(value, element, param) {
                var file = element.files[0];
                if (file) {
                    return file.size <= param; 
                }
                return true;
            }, "The file size must be less than {0} MB.");

            $.validator.addMethod("mimetypes", function(value, element, param) {
                var file = element.files[0];
                if (file) {
                    return param.indexOf(file.type) !== -1; 
                }
                return true;
            }, "The images must be in .png, .jpg or .jpeg format.");

            $.validator.addMethod("maxImages", function(value, element, param) {
                var files = element.files;
                return files.length <= param; 
            }, "You can upload a maximum of 4 images.");

        });
</script>

<script>
     $(document).ready(function() {
            toggleFieldsBasedOnOfferType();
     });

    $('#offer_types').change(function() {
        toggleFieldsBasedOnOfferType();
    });

    function toggleFieldsBasedOnOfferType() {
        var offerType = $('#offer_types').val();

        $('.discountAmount').hide();
        $('.discountPercentage').hide();

        if (offerType == 'percentage') {
            $('.discountAmount').hide();
            $('.discountPercentage').show();

        } else if (offerType =='flat') {
            $('.discountAmount').show();
            $('.discountPercentage').hide();
        }
    }

</script>

<script>
    function getSubCatList(thisitem) {
        var idCategory = $('#category_ids').val();
        $('#subcategory_ids').html('<option value="">Select Subcategory</option>');

        $.ajax({
            url: "{{ route('product.getsubcatlist') }}",
            type: "POST",
            data: {
                id: idCategory,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                if (result.sub && result.sub.length > 0) {
                    $.each(result.sub, function(key, value) {
                        $("#subcategory_ids").append(
                            '<option value="' + value.id + '">' + value.title + '</option>'
                        );
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }
</script> 

{{-- Image --}}
<script>
     function deleteImage(element) {
        var __element = $(element);
        var image_id = __element.data('image_id');
        document.getElementById('offer_photo_' + image_id).style.display = 'none';
        document.getElementById('is_deleted_' + image_id).checked = true;
        document.getElementById('undo_' + image_id).style.display = 'block';
        document.getElementById('images_id_' + image_id).value = '';
        afterDisableRemoveImage();
    }

    function undoDeleteImage(element) {
        var __element = $(element);
        var image_id = __element.data('image_id');
        document.getElementById('offer_photo_' + image_id).style.display = 'block';
        document.getElementById('is_deleted_' + image_id).checked = false;
        document.getElementById('undo_' + image_id).style.display = 'none';
        document.getElementById('images_id_' + image_id).value = image_id;
        afterDisableRemoveImage();
    }

    function afterDisableRemoveImage() {
        var uploaded_item = $('input[name="deleted_image[]"]').length;
        var remove_image_count = 0;
        $('input[name="images_ids[]"]').each(function() {
            remove_image_count = remove_image_count + ($.trim($(this).val()) == "" ? 1 : 0);
        });
        if (uploaded_item <= 4 && remove_image_count != 0) {
            $('#offer_image').attr('disabled', false);
            $("#offer_image_input_error").html(``);
        } else {
            $('#offer_image').attr('disabled', true);
            $("#offer_image_input_error").html(`<span style="color: red;" class='validate'>You can not upload images now.</span>`);
        }
    }

    function getFileUploadLimit() {
        var remove_image_count = 0;
        $('input[name="images_ids[]"]').each(function() {
            remove_image_count = remove_image_count + ($.trim($(this).val()) == "" ? 1 : 0);
        });
        var image_count = '{{$editData->get_offer_images->count()}}';
        if (remove_image_count != 0) {
            var image_count = image_count - remove_image_count;
        }
        var fileUploadLimit = 4;
        var remainUploads = 4;
        if (image_count != '') {
            var remainUploads = (fileUploadLimit - image_count);
        }
        return remainUploads;
    }

    $(function() {
        // Multiple images preview with JavaScript                
        var previewImages = function(input, imgPreviewPlaceholder) {
            var imageUploadLimit = getFileUploadLimit();
            if (input.files) {
                var filesAmount = input.files.length;

                $(imgPreviewPlaceholder).html("");
                if (filesAmount == 0) {
                    $(".uploaded_image").hide();
                } else if (filesAmount > imageUploadLimit) {
                    $('#offer_image').val("");
                    var messageToShow;
                    if (imageUploadLimit > 0) {
                        messageToShow = 'You can now upload only ' + imageUploadLimit + ' images.'
                    } else {
                        messageToShow = "You can not upload images now."
                    }
                    $(".uploaded_image").hide();
                    $("#offer_image_input_error").html(
                        `<span style="color: red;" class='validate'>Product max image exceeded, ${messageToShow} </span>`
                    );
                } else {
                    $("#offer_image_input_error").html("");
                    for (i = 0; i < filesAmount; i++) {
                        var reader = new FileReader();
                        reader.onload = function(event) {
                            $($.parseHTML('<img>')).attr('src', event.target.result).css({
                                'width': '100px',
                                'height': '100px',
                                'margin': '10px'
                            }).appendTo(imgPreviewPlaceholder);
                        }
                        reader.readAsDataURL(input.files[i]);
                    }
                    $(".uploaded_image").show();
                }
            }
        };
        $('#offer_image').on('change', function() {
            var selection = document.getElementById('offer_image');
            for (var i = 0; i < selection.files.length; i++) {
                var ext = selection.files[i].name.substr(-3);
                var fileSize = selection.files[i].size;
                const fileMb = fileSize / 1024 ** 2;
                if (ext !== "png" && ext !== "jpg" && ext !== "jpeg") {
                    $("#offer_image_input_error").html('<span style="color: red;">The files must be a file of type: jpg, jpeg, png.<span>');
                    return false;
                }
                if (fileMb >= 2) {
                    $("#offer_image_input_error").html('<span style="color: red;">Please select files less than 2MB.<span>');
                    return false;
                }
            }
            previewImages(this, 'div.images-preview-div');
        });
    });

    if (getFileUploadLimit() <= 0) {
        let messageToShow = "You can not upload images now.";
        $("#offer_image_input_error").html(
            `<span style="color: red;" class='validate'>{{ __('backend.propertyMaxImageLimitExceded') }} ${messageToShow} </span>`
        );
        $('#offer_image').attr('disabled', true);
    }

    function validateImageUpload() {

        let image_count = 0;
        $('input[name="images_ids[]"]').each(function () {
            image_count += ($.trim($(this).val()) === "" ? 1 : 0);
        });

        var upload_files = $('#offer_image')[0].files.length;
        var deleted_item = $('input[name="deleted_image[]"]').length;

        if ((image_count != 0 && upload_files == 0) && (image_count == deleted_item && upload_files == 0)) {
            event.preventDefault();
            $("#offer_image_input_error").html(
                `<span style="color: red;" class='validate'>The image field is required.</span>`
            );
            return false;
        }

        // Clear previous errors if validation passes
        $("#offer_image_input_error").html("");
        return true;

    }



</script>