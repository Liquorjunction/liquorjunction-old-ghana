@extends('dashboard.layouts.master')
@section('title', 'Offer | Admin Panel')
@section('content')
    @include('sweetalert::alert')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

    <style>
        .model {
            z-index: 1050 !important;
        }

        .model-backdrop {
            z-index: 1040 !important;
        }

        table.dataTable tbody td:last-child {
            padding: 8px 8px !important;
        }

        #option_width {
            width: 160px !important;
        }

        .btn {
            padding: 7px 10px;
        }
    </style>
    <div class="loader" id="loader"></div>
    <div class="padding website-label">
        <div class="success_message" style="margin-bottom: 10px;"></div>
        <div id="success_file_popup" style="margin-bottom: 10px;"></div>
        <div class="box">

            <div class="box-header dker">
                <h3>Offer Management</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>Offer Management</span>
                </small>
            </div>

            <div class="box-tool">
                <ul class="nav">

                    <li class="nav-item inline">
                        <a class="btn btn-fw primary" data-toggle="modal" data-target="#myModal" data-backdrop="static"
                            data-keyboard="false">
                            <i class="material-icons">&#xe02e;</i>
                            &nbsp; New Offer
                        </a>
                    </li>

                </ul>
            </div>

            {{ Form::open(['route' => 'offerUpdateAll', 'method' => 'post', 'id' => 'updateAll']) }}
            <div class="table-responsive">
                <table class="table table-bordered m-a-0" id="label">
                    <thead class="dker">
                        <tr>
                            <th id="offer">Offer Type</th>
                            <th id="product">Applicable On</th>
                            <th id="amount">Discount Amount/ Percentage(%)</th>
                            <th id="count">Total Used Count</th>
                            <th id="created">Created On</th>
                            {{-- <th id="minimum">Minimum Amount</th> --}}
                            {{-- <th id="maximum">Maximum Amount</th> --}}
                            {{-- <th id="use">Usage Limit</th> --}}
                            {{-- <th id="users">Max User</th> --}}
                            <th id="expiry">Expiry Date</th>
                            <th>{{ __('backend.status') }}</th>
                            <th id="option_width">{{ __('backend.options') }}</th>
                        </tr>
                    </thead>
                    <tbody id="bannerTable">
                    </tbody>
                </table>
            </div>
            <footer class="dker p-a">
                <div class="row">
                    <div class="col-sm-3 hidden-xs">
                        <!-- .modal -->
                        <div id="m-all" class="modal fade" data-backdrop="true">
                            <div class="modal-dialog" id="animate">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('backend.confirmation') }}</h5>
                                    </div>
                                    <div class="modal-body text-center p-lg">
                                        <p>
                                            {{ __('backend.confirmationDeleteMsg') }}
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn dark-white p-x-md"
                                            data-dismiss="modal">{{ __('backend.no') }}</button>
                                        <button type="submit" class="btn danger p-x-md">{{ __('backend.yes') }}</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div>
                        </div>
                        <!-- / .modal -->
                    </div>


                    <div class="col-sm-6 text-right text-center-xs">

                    </div>
                </div>
            </footer>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Offer</h4>
                </div>
            <div id="conflictError" class="alert alert-danger" style="display:none;"></div>
                <form class="cmxform" id="offerForm" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Offer Type<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <select name="offer_type" id="offer_type" class="form-control">
                                    <option value="percentage" {{ old('offer_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="flat" {{ old('offer_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                                </select>
                                <span style="color: red;display: none;" id="errorMsgType" class='validate'></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-5 form-control-cms">Applicable On<span class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <select name="product_type" id="product_type" onchange="showHide()" class="form-control" value="">
                                    <option value="">Select Type</option>
                                    <option value="1">Brand</option>
                                    <option value="2">Category</option>
                                    <option value="3">Product</option>
                                </select>
                                @if ($errors->has('product_type'))
                                <span class="help-block">
                                    <span style="color: red;" class='validate'>{{ $errors->first('product_type') }}</span>
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
                                    <option value="{{ $value->id }}">
                                        {{ ucfirst($value->title) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row category">
                            <div class="col-sm-5 form-control-cms ">Select Category<span class="valid_field">*</span></div>
                            <div class="col-sm-7">
                                <select name="category_id" id="category_id" class="form-control" value="">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $key => $value)
                                    <option value="{{ $value->id }}">
                                        {{ ucfirst($value->title) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row subcategory" style="display: none;">
                            <div class="col-sm-5 form-control-cms">Select Subcategory</div>
                            <div class="col-sm-7">
                                <select name="subcategory_id" id="subcategory_id" onchange="getSubCatList(this)"
                                    class="form-control" value="">
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row product">
                                <div class="col-sm-5 form-control-cms ">Select Product<span class="valid_field">*</span></div>
                                <div class="col-sm-7">
                                    <select name="product_id" id="product_id" class="form-control" value="">
                                        <option value="">Select Product</option>
                                        @foreach ($product as $key => $value)
                                        <option value="{{ $value->id }}" >
                                            {{ ucfirst($value->product_name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>
                        <div class="form-group row" id="discountAmountDiv" style="display: none;">
                            <label class="col-sm-5 form-control-label">Discount Amount<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number" name="dis_amount" id="dis_amount" class="form-control" placeholder="100" value="{{ old('dis_amount') }}" required>
                                <span style="color: red;display: none;" id="errorMsgAmount" class='validate'></span>
                            </div>
                        </div>
                        <div class="form-group row" id="discountPercentageDiv">
                            <label class="col-sm-5 form-control-label">Discount Percentage(%)<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number" name="dis_percentage" id="dis_percentage" class="form-control" placeholder="100" value="{{ old('dis_percentage') }}" required>
                                <span style="color: red;display: none;" id="errorMsgPercentage" class='validate'></span>
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Minimum Amount<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number" name="min_amount" id="min_amount" class="form-control" placeholder="100" value="{{ old('min_amount') }}" required>
                                <span style="color: red;display: none;" id="errorMsgMin" class='validate'></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Maximum Amount<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number"  name="max_amount"  id="max_amount" class="form-control"
                                 placeholder="200"
                                    value="{{ old('max_amount') }}" required>
                                <span style="color: red;display: none;" id="errorMsgMax" class='validate'></span>
                            </div>
                        </div> --}}

                        <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Expiry Date<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="date"  name="expiry_date"  id="expiry_date" class="form-control"
                                 placeholder="200"
                                    value="{{ old('expiry_date') }}" required>
                                <span style="color: red;display: none;" id="errorMsgExpiry" class='validate'></span>
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Usage Limit<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number"  name="total_usage"  id="total_usage" class="form-control"
                                 placeholder="15"
                                    value="{{ old('total_usage') }}" required>
                                <span style="color: red;display: none;" id="errorMsgUsage" class='validate'></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Max Users<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number"  name="max_users"  id="max_users" class="form-control"
                                    value="{{ old('expiry_date') }}" placeholder="25" required>
                                <span style="color: red;display: none;" id="errorMsgUsers" class='validate'></span>
                            </div>
                        </div> --}}

                        {{-- <div class="form-group row">
                            <label class="col-sm-4 form-control-cms">Title<span
                                class="valid_field">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="title" id="title" class="form-control" placeholder="Offer Title" value="{{old('title')}}">
                                <span style="color: red;display: none;" id="errorMsgTitle" class='validate'></span>
                            </div>
                        </div> --}}

                        <div class="form-group row">
                            <label class="col-sm-5 form-control-cms">Button Template<span
                                class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="text" name="template" id="template" class="form-control" placeholder="Button Template Text" value="{{old('template')}}">
                                <span style="color: red;display: none;" id="errorMsgTemplate" class='validate'></span>
                            </div>
                        </div>

                         <div class="form-group row">
                            <label class="col-sm-5 form-control-cms">Custom URL<span
                                class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="text" name="custom_url" id="custom_url" class="form-control" placeholder="Custom Url" value="{{old('custom_url')}}">
                                <span style="color: red;display: none;" id="errorMsgUrl" class='validate'></span>
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label class="col-sm-4 form-control-cms">Description<span
                                class="valid_field">*</span></label>
                            <div class="col-sm-8">
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Offer Description...">{{old('description')}}</textarea>
                                <span style="color: red;display: none;" id="errorMsgDescription" class='validate'></span>
                            </div>
                        </div> --}}

                        {{-- <div class="form-group row">
                            <label class="col-sm-4 form-control-label">Image<span class="valid_field">*</span></label>
                            <div class="col-sm-8">
                                <div class="mb-1">
                                    <img id="blah" height="100" width="100" src="{{ asset('assets/dashboard/images/no_image_found.jpg') }}" alt="your image" style="width:100px !important; height:100px !important; margin-bottom: 10px;" />
                                </div>
                                <input type="file" name="photo" id="offerimage" class="form-control" accept="image/*">
                                @if ($errors->has('photo'))
                                <span class="help-block">
                                    <span style="color: red;" class='validate'>{{ $errors->first('photo') }}</span>
                                </span>
                                @endif
                                <div>
                                    <small>
                                        <i class="material-icons">&#xe8fd;</i>
                                        Choose image, .png, .jpg, .jpeg files only.
                                    </small>
                                    <br>
                                    <small>
                                        <i class="material-icons">&#xe8fd;</i>
                                        Recommended size for offer 1440(Width) x 560(Height).
                                    </small> 
                                </div>
                            </div>
                        </div> --}}

                        <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Images<span class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="file" name="offer_images[]" id="offer_images" class="form-control"
                                    accept="image/*" placeholder="Offer Images" multiple>
                                <span class="help-block" id="offer_images_input_error">
    
                                    @if (!empty(@$errors) && @$errors->has('offer_images.*'))
                                        <span style="color: red;"
                                            class='validate'>{{ is_string($errors->first('offer_images.*')) ? $errors->first('offer_images.*') : @$errors->first('offer_images.*')[0] }}
                                        </span>
                                    @endif
                                    <span style="color: red;" class='validate'>{{ $errors->first('offer_images') }}</span>
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
    
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label"></label>
                            <div class="col-sm-8">
                                <div class="mt-1 text-center uploaded_images box p-a-xs" style="display: none">
                                    {{-- <h3>New Uploaded Images</h3> --}}
                                    <div class="images-preview-div">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i> Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
                    </div>
                    {{-- {{ Form::close() }} --}}
                </form>
            </div>

        </div>
    </div>


    <div class="modal fade" id="editOffer" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content add-offer-data">
            </div>
        </div>
    </div>

    <div class="modal fade" id="showoffer" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content show-offer-data">
            </div>
        </div>
    </div>

@endsection
@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>


    <script>
        $(document).ready(function(e) {
            var offerform = $("#offerForm").validate({
            
                rules: {
                    offer_type: {
                        required: true,
                    },
                    product_type: {
                        required: true,
                    },
                    brand_id: {
                        required: function () {
                            return $("#product_type").val() == "1";
                        }
                    },
                    category_id: {
                        required: function () {
                            return $("#product_type").val() == "2";
                        }
                    },
                    product_id: {
                        required: function () {
                            return $("#product_type").val() == "3";
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
                    offer_images: {
                        required: true,
                        maxFileSize: 2 * 1024 * 1024, 
                        mimetypes: ["image/jpeg", "image/png", "image/jpg"], 
                        maxImages: 4 
                    }
                },
                messages: {
                    offer_type: {
                        required: "Offer Type Field is required.",
                    },
                    product_type: {
                        required: "Product Type is required."
                    },
                    brand_id: {
                        required: "Brand is required."
                    },
                    category_id: {
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
                    offer_images: {
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

                    var form_data = new FormData($('#offerForm')[0]);
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
                            $('.loader').css("visibility", "visible");
                        },
                        success: function(data) {
                            if (data.success) {
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
                            $('#conflictError').hide().html(''); // Hide the conflict error div

                            let erroJson = errors.responseJSON;

                            // Loop through each field error
                            $.each(erroJson, function(field, messages) {
                                const message = messages[0];

                                if (field === 'conflict') {
                                    // Show the conflict error in a special div
                                    $('#conflictError').html(message).show();
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

            $('#product_type').change(function () {
                $('#brand_id, #category_id, #product_id').valid();
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


        $(document).on('click', '.edit-offer', function(e) {
            $("#editOffer").modal({
                backdrop: false
            });
            var offer_id = $(this).attr('data-id');
            $(document).find('#editOffer').find(".add-offer-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('offeredit') }}",
                type: 'POST',
                data: 'id=' + offer_id,
                success: function(response) {
                    $(document).find('#editOffer').find(".add-offer-data").append(response.html);
                    $('.selectpicker').selectpicker();
                    $(document).find('#editOffer').modal('show');
                },
                error: function(response) {

                    alert(response.responseText);
                }
            });
        });


        $(document).on('click', '.show-offer', function(e) {
            $("#showoffer").modal({
                backdrop: false
            });
            var offer_id = $(this).attr('data-id');
            $(document).find('#showoffer').find(".show-offer-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('offer.show') }}",
                type: 'POST',
                data: 'id=' + offer_id,
                success: function(response) {
                    $(document).find('#showoffer').find(".show-offer-data").append(response.html);
                    $(document).find('#showoffer').modal('show');
                },
                error: function(response) {
                    alert(response.responseText);
                }
            });
        });

        
        $(document).on('click', '.status_active', function(e) {
            var offer_id = $(this).attr('data-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('offer.status_active')}}",
                type: 'POST',
                data: 'id='+offer_id,
                beforeSend: function(){
                    $('.loader').css("visibility", "visible");
                },
                success: function (response) {
                    $('.loader').css("visibility", "visible");
                    window.location.href = "{{ route('offer')}}";
                },
                error: function (response) {
                alert(response.responseText);
            }
            });
        }); 


        $(document).on('click', '.status_inactive', function(e) {
            var offer_id = $(this).attr('data-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('offer.status_inactive')}}",
                type: 'POST',
                data: 'id='+offer_id,
                beforeSend: function(){
                    $('.loader').css("visibility", "visible");
                },
                success: function (response) {
                    $('.loader').css("visibility", "visible");
                    window.location.href = "{{ route('offer')}}";
                },
                error: function (response) {
                alert(response.responseText);
            }
        });
        }); 
    </script>

    <script type="text/javascript">
        function isNumberKey(evt) {
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

                return false;
            return true;
        }


        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            load_data();

            function load_data() {
                var action_url = "{!! route('offer.anyData') !!}";

                $('#label').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    ajax: {
                        url: action_url,
                        type: 'POST'
                    },
                    columns: [
                        { data: 'offer', name: 'offer_type' },
                        { data: 'product', name: 'product_type' },
                        { data: 'discount', name: 'dis_amount' },
                        // { data: 'minimum', name: 'min_amount' },
                        // { data: 'maximum', name: 'max_amount' },
                        // { data: 'use', name: 'total_usage' },
                        // { data: 'users', name: 'max_users' },
                        { data: 'count', name: 'count' },
                        { data: 'created', name: 'created_at' },
                        { data: 'expiry', name: 'expiry_date' },
                        { data: 'status', name: 'status' },
                        { data: 'options', orderable: false, searchable: false }
                    ],

                    order: [[0, 'DESC']] 
                });
            }
        });


        $(document).ready(function() {
            if ($('.no-sort').hasClass('sorting_disabled')) {
                $('.no-sort').removeClass('sorting_asc')
            }
        });

        $("#submit_show_msg").click(function() {
            var numberOfChecked = $('input:checkbox:checked').length;
            if (numberOfChecked == '') {
                alert("Please select row.");
                return false;
            }
        });
        
        $("#checkAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });


        $("#filter_btn").click(function() {
            $("#filter_div").slideToggle();
        });


        $("#find_q").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#doctorTypeTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });


        $(document).on('submit', '#updateAll', function(e) {
            e.preventDefault();
            var allVals = [];
            var check = false;

            var select_row = "{{ __('backend.select_row') }}";
            var select_status = "{{ __('backend.select_status') }}";

            var type = $(document).find('#action').val();
            if (type == 'no') {
                $(document).find('#alert_confirm').modal('show');
                $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_status);

            } else {
                $(".has-value:checked").each(function() {
                    var idvalue = $(this).attr('data-id');
                    if (typeof idvalue === "undefined") {

                    } else {
                        allVals.push(idvalue);
                    }
                });

                if (allVals.length <= 0) {
                    $(document).find('#alert_confirm').modal('show');
                    $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_row);
                } else {
                    var msg = "";
                    if (type == 0) {
                        msg = "Are you sure you want to deactivate this Offer?";
                    } else if (type == 1) {
                        msg = "Are you sure you want to activate this Offer?";
                    } else {
                        msg = "Are you sure you want to delete this Offer?";
                    }

                    $(document).find('#default_confirm').modal('show');
                    $(document).find('#default_confirm').find('.dynamic_message').text(msg);
                    var join_selected_values = allVals.join(",");
                    $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
                    $(document).find('#default_confirm').find('.checkbox_type').val(type);

                }

            }
        });
        
        $(document).on('click', '.yes_click', function(e) {
            var join_selected_values = $(document).find('#default_confirm').find('.checkbox_data').val();
            var type = $(document).find('#default_confirm').find('.checkbox_type').val();
            var csrf = "{{ csrf_token() }}";
            ajaxUpdateAll(csrf, join_selected_values, type);
        });

        // Mail
        $(document).on('click', '.send-offer', function(e) {
            e.preventDefault();
            var offer_id = $(this).attr('data-id');

            swal({
                    title: "Active",
                    text: "Are you sure you want to publish this offer?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#fbb516",
                    cancelButtonColor: "rgb(36, 36, 36)",
                    confirmButtonText: "Active",
                    closeOnConfirm: false
                },
                function() {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('sendMail') }}",
                        type: 'GET',
                        data: { offer_id: offer_id },
                        beforeSend: function() {
                            $('.loader').css("visibility", "visible");
                        },
                        success: function(response) {
                            $('.loader').css("visibility", "visible");
                            // window.location.href = "{{ route('offer') }}";
                               swal({
                                    title: "Success!",
                                    text: "Offer published and mail sent successfully!",
                                    type: "success",
                                    confirmButtonColor: "#fbb516",
                                    confirmButtonText: "OK"
                                }, function() {
                                    window.location.href = "{{ route('offer') }}";
                                });
                        },
                        error: function(response) {
                            alert(response.responseText);
                        }
                    });
                })
        });


        $(document).on('click', '.delete-school', function(e) {
            e.preventDefault();
            var package_id = $(this).attr('data-id');
            var allVals = [];
            allVals.push(package_id);
            var type = 2;
            var msg = "Are you sure you want to delete?";

            $(document).find('#default_confirm').modal('show');
            $(document).find('#default_confirm').find('.dynamic_message').text(msg);
            var join_selected_values = allVals.join(",");
            $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
            $(document).find('#default_confirm').find('.checkbox_type').val(type);
        });



        function ajaxUpdateAll(csrf, join_selected_values, type) {
            $.ajax({
                url: "{{ route('offerUpdateAll') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                data: 'ids=' + join_selected_values + '&status=' + type,
                beforeSend: function() {
                    $('.loader').css("visibility", "visible");
                },
                success: function(data) {

                    if (data.success == true) {
                        $('.loader').css("visibility", "hidden");
                        $('#success_file_popup').append(messages('alert-success', data.msg));
                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);


                        $(document).find('#default_confirm').modal('hide');
                        var tabe = $('#label').DataTable();
                        $(document).find('#action').prop('selectedIndex', 0);
                        tabe.ajax.reload(null, false);
                        $("#checkAll").prop('checked', false);

                    } else {
                        $('.loader').css("visibility", "hidden");
                        $('#success_file_popup').append(messages('alert-danger', data.error));

                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);
                    }
                },
                error: function(data) {
                    alert(data.responseText);
                }
            });
        }

        // $("#action").change(function() {
        //     if (this.value == "delete") {
        //         $("#submit_all").css("display", "none");
        //         $("#submit_show_msg").css("display", "inline-block");
        //     } else {
        //         $("#submit_all").css("display", "inline-block");
        //         $("#submit_show_msg").css("display", "none");
        //     }
        // });

        function messages(classname, msg) {
            return '<div class="alert ' + classname +
                ' m-b-0"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>' +
                msg + '</div>';
        }

        setTimeout(function() {
            $('#topmsgall').hide();
        }, 5000);

        function isNumberBlock(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;
            return true;
        }
    </script>

    <script>
          $(document).ready(function() {
                 showHide();
          });

          function showHide() {
                var type = $("#product_type").val();
                $('.category').hide();
                $('.brand').hide();
                $('.product').hide();
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
                    }
                    else{
                        $('.category').hide();
                        $('.product').show();
                        $('.brand').hide();
                        $('.subcategory').hide();
                    }
                }
            }


    </script>


    <script>
         $(document).ready(function() {
            toggleFieldsBasedOnOfferType();

        $('#offer_type').change(function() {
            toggleFieldsBasedOnOfferType();
        });

        function toggleFieldsBasedOnOfferType() {
            var offerType = $('#offer_type').val();

            if (offerType === 'percentage') {
                document.getElementById('discountPercentageDiv').style.display="block"
                document.getElementById('discountAmountDiv').style.display="none"
            } else if (offerType === 'flat') {
                document.getElementById('discountPercentageDiv').style.display="none"
                document.getElementById('discountAmountDiv').style.display="block"
            }
        }
    });
    </script>

    <script>
            function getSubCatList(thisitem) {
                var idCategory = $('#category_id').val();
                var cat_id = $('#cate_id').val();

                $('#sub_category_id').html('');
                $('#sub_category_id').html('<option value="">Select Subcategory</option>');
                $.ajax({
                    url: "{{ route('product.getsubcatlist') }}",
                    type: "POST",
                    data: {
                        id: idCategory,
                        cat_id: cat_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        console.log(result);
                        $('#sub_category_id').html('<option value="">Select Category</option>');
                        $.each(result.sub, function(key, value) {
                            var selected = '';
                            selected = value.category_id == idCategory ? "selected" : "";
                            $("#sub_category_id").append('<option ' + selected + ' value="' + value.id +
                                '">' +
                                value.title + '</option>');
                        });
                    }
                });
                }



                   const empty_product_subcategory = () => $('#subcategory_id option:not(:first-child)').remove();
                   var product_category = "{{ old('category_id') }}";
                   var product_subcategory = "{{ old('subcategory_id') }}";

                   function getSubCategory() {
                        var category_ele = $('#category_id');
                        var subcategory_ele = $("#subcategory_id");
                        var category_val = category_ele.val();
                        if (!category_ele) {
                            category_val = product_category;
                        }
                   
                        empty_product_subcategory();
                        if (category_val) {
                            var __url = "{{ route('getsubcategories', ['id' => ':id']) }}".replace(':id', category_val);
                            $.ajax({
                                url: __url,
                                type: 'post',
                                dataType: 'json',
                                success: function(data) {
                                    var subcats = data.data;
                                    if (data.code == 200) {
                                        subcats.map((ele, index) => {
                                            var is_selected = product_subcategory ? 'selected="selected"' : "";
                                            subcategory_ele.append(
                                                `<option value="${ele.id}" ${is_selected}>${ele.title}</option>`
                                                );
                                        });
                                    } else {
                                        alert('Something went wrong.');
                                    }
                                }
                            })
                        }
                    }

                (function() {
                    getSubCategory();
                    $("#category_id").on('change', function(e) {
                        getSubCategory();
                    });
                })();

    </script>


{{-- Image Script --}}
<script>
    function getFileUploadLimit() {
        var fileUploadLimit = 4;
        var remainUploads = 4;
        return remainUploads;
    }

    $(function() {
            // Multiple images preview with JavaScript                
            var previewImages = function(input, imgPreviewPlaceholder) {
                var imageUploadLimit = getFileUploadLimit();
                if (input.files) {
                    console.log(input.files);
                    var filesAmount = input.files.length;
                    $(imgPreviewPlaceholder).html("");
                    if (filesAmount == 0) {
                        $(".uploaded_images").hide();
                    } else if (filesAmount > imageUploadLimit) {
                        $('#offer_images').val("");
                        var messageToShow;
                        if (imageUploadLimit > 0) {
                            messageToShow = 'You can upload only ' + imageUploadLimit + ' images.'
                        } else {
                            messageToShow = "You can not upload images now."
                        }
                        $(".uploaded_images").hide();
                        $("#offer_images_input_error").html(
                            `<span style="color: red;" class='validate'>Product max image exceeded, ${messageToShow} </span>`
                        );
                    } else {
                        $("#offer_images_input_error").html("");
                        for (i = 0; i < filesAmount; i++) {
                            var reader = new FileReader();
                            reader.onload = function(event) {
                                $($.parseHTML('<img>')).attr('src', event.target.result).css({
                                    'width': '100px',
                                    'height': '100px',
                                    'margin': '10px',
                                }).appendTo(imgPreviewPlaceholder);
                            }
                            reader.readAsDataURL(input.files[i]);
                        }
                        $(".uploaded_images").show();
                    }
                }
            };
            $('#offer_images').on('change', function() {
                var selection = document.getElementById('offer_images');
                for (var i = 0; i < selection.files.length; i++) {
                    var ext = selection.files[i].name.substr(-3);
                    var fileSize = selection.files[i].size;
                    const fileMb = fileSize / 1024 ** 2;
                    if (ext !== "png" && ext !== "jpg" && ext !== "jpeg") {
                        $("#offer_images_input_error").html(
                            '<span style="color: red;">The files must be a file of type: jpg, jpeg, png.<span>'
                            );
                        return false;
                    }
                    if (fileMb >= 2) {
                        $("#offer_images_input_error").html(
                            '<span style="color: red;">Please select files less than 2MB.<span>');
                        return false;
                    }
                }
                previewImages(this, 'div.images-preview-div');
            });
        });

        if (getFileUploadLimit() <= 0) {
            let messageToShow = "You can not upload images now.";
            $("#offer_images_input_error").html(
                `<span style="color: red;" class='validate'>{{ __('backend.offerMaxImageLimitExceded') }} ${messageToShow} </span>`
            );
            $('#offer_images').attr('disabled', true);
        }

        function validateImageUpload() {

            var selection = document.getElementById('offer_images');
            console.log(selection,"selection")
        
            if (selection.files.length<1) {
                event.preventDefault();
                $("#offer_images_input_error").html(
                    `<span style="color: red;" class='validate'>The image field is required.</span>`
                );
                return false;
            }

            // Clear previous errors if validation passes
            $("#offer_images_input_error").html("");
            return true;

        }


</script>
@endpush
