        <div class="modal-header">
            <h4 class="modal-title">{{ __('backend.EditCategory') }}</h4>
        </div>
        <form class="cmxform" id="categoryEditForm" method="post" action="" autocomplete="off">
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">{!! __('Title [EN]') !!}<span
                            class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="title" id="title" class="form-control"
                            placeholder="Title [EN]" onkeypress="return isNumberKey(event)"
                            value="{{ @$categoryData->title }}">
                        <span class="help-block" id="errorMessage" style="display:none">
                            <span style="color: red;display: none;" id="errorMsg" class='validate'></span>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Title [FR]<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="title_fr" id="title_fr" class="form-control"
                            placeholder="Title [FR]" value="{{ @$categoryData->title_fr }}">

                        <span style="color: red;display: none;" id="errorMsgtitlefr" class='validate'></span>
                        <span class="help-block" id="errorMessagetitlefr" style="display:none">
                        </span>

                    </div>

                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Image<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <div class="">
                            <input class="form-control" type="file" name="imagefile" id="imagefile"
                                value="{{ $categoryData->imagefile }}">
                            <span style="color: red;display: none;" id="errorMsgimage" class='validate'></span>
                            <span class="help-block" id="errorMessageimage" style="display:none">
                            </span>
                            <div>
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    Choose image .png, .jpg, .jpeg files only.
                                </small>
                                <br>
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    Recommended size 300(Width) x 300(Height).
                                </small>
                            </div>

                            <img style="width: 100px !important;" id="blah1"
                                src="<?php if (!empty($categoryData->imagefile)) { ?>
                                   {{ asset('uploads/category/' . $categoryData->imagefile) }}
                                   <?php } else { ?>
                                    {{ asset('assets/dashboard/images/no_image_found.jpg') }}
                                    <?php }
                                    ?>"
                                alt="your image" />
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label"> Background Image<span
                            class="valid_field"></span></label>
                    <div class="col-sm-10">
                        <input type="file" name="photo" id="bannerimage" class="form-control"
                            style="margin-left: -10px;" accept="image/png, image/jpeg">
                        <div class="help-block with-errors" style="color: red;"></div>
                        <div>
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                Choose image .png, .jpg, .jpeg files only.
                            </small>
                            <br>
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                Recommended size 1920(Width) x 250(Height).
                            </small>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2"></label>
                    <div class="col-sm-10">
                        @if (isset($categoryData->photo) && $categoryData->photo != '')
                            <img id="image" src="<?php echo $categoryData->photo != '' ? asset('uploads/categoryback/') . '/' . $categoryData->photo : 'http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image'; ?>" width="100px" height="100px" />&nbsp; &nbsp;
                            &nbsp;
                            {{-- <button type="button" class="btn btn-dark removeImage"><span class="glyphicon glyphicon-trash"></span> </button> --}}
                        @else
                            <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                        @endif
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">URL<span
                            class="valid_field"></span></label>
                    <div class="col-sm-10">
                        <input type="text" name="url" id="url" class="form-control"
                            placeholder="URL" 
                            value="{{ @$categoryData->url }}">
                        <span class="help-block" id="errorMessage" style="display:none">
                            <span style="color: red;display: none;" id="errorMsg" class='validate'></span>
                        </span>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="category_id" id="category_id" value="{{ @$categoryData->id }}">
                <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i>
                    Update</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                        class="material-icons">&#xe5cd;</i> Close</button>
            </div>
            {{ Form::close() }}
            <!-- </div> -->

            <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
            <script type="text/javascript">
                $(document).ready(function() {

                    

                    $("#categoryEditForm").validate({
                        // in 'rules' user have to specify all the constraints for respective fields
                        rules: {
                            imagefile: {
                                extension: "jpg|jpeg|png",
                            },
                            title: {
                                required: true,
                                maxlength: 30,

                            },
                            title_fr: {
                                required: true,
                                maxlength: 30,
                            },
                            description: "required",
                            description_fr: "required",
                            icon_id: "required",
                        },
                        // in 'messages' user have to specify message as per rules
                        messages: {
                            imagefile: {
                                extension: 'Image file type must be jpeg,png,jpg.',
                            },
                            title: {
                                required: "Title field is required.",
                                maxlength: "Title field cannot exceed {0} characters.",

                            },
                            title_fr: {
                                required: "Title Fr field is required.",
                                maxlength: "Title Fr field cannot exceed {0} characters."
                            },
                            description: " Description field is required",
                            description_fr: "Descripton Fr field is required",
                            // icon_id: "Icon field is required",
                        },
                        submitHandler: function() {
                            var form_data = new FormData($('#categoryEditForm')[0]);
                            action_url = "{{ route('category.store') }}";
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
                                        window.location.href = "{{ route('category') }}";
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

                                            $("span#errorMsg").css("display", "block");
                                        $("span#errorMsgtitlefr").css("display", "block");
                                        $("span#errorMsgimage").css("display", "block");


                                        $("span#errorMsg").html(errstr);
                                        $("span#errorMsgimage").html(errstr);

                                    }
                                }
                            });
                        }
                    });
                });
            </script>
            <script type="text/javascript">
                document.getElementById('bannerimage').onchange = function(evt) {
                    const file = evt.target.files[0];
                    if (!file) return;

                    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
                    const maxSize = 2 * 1024 * 1024; // 2 MB

                    if (!allowedExtensions.exec(file.name)) {
                        this.value = ''; // Reset the input
                        document.querySelector('.help-block.with-errors').innerHTML =
                            'Please upload only .png , .jpg , .jpeg only.';
                        return;
                    }

                    if (file.size > maxSize) {
                        this.value = ''; // Reset the input
                        document.querySelector('.help-block.with-errors').innerHTML = 'File upload size is not more than 2 MB.';
                        return;
                    }

                    document.querySelector('.help-block.with-errors').innerHTML = ''; // Clear error messages
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        document.getElementById('previewImage').src = e.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            </script>
