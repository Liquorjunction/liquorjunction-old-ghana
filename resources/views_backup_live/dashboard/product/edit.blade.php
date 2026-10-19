@extends('dashboard.layouts.master')
@section('title', 'Product | Admin Panel')
@push('after-styles')
<style type="text/css">
    #blah {
        height: 50% !important;
        width: 25% !important;
    }

    #blah1 {
        height: 50% !important;
        width: 25% !important;
    }

    .product-single-variant {
        margin: 30px 0 10px 0;
        padding: 20px 0px 0 0;
    }

    .product-single-variant:not(:first-child) {
        border-top: 1px solid gray;
        /* border-bottom: 1px solid gray; */
    }

    .product-single-variant:last-child {
        /* border-top: 1px solid gray; */
        border-bottom: 1px solid gray;
    }
</style>
<link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

<!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

<style type="text/css">
    .error {
        color: red;
        margin-left: 5px;
    }
</style>
@endpush
@section('content')
<div class="padding edit-package">
    <div class="box">
        <div class="box-header dker">

            <h3><i class="material-icons">
                    &#xe02e;</i> Edit {{ __('backend.product') }}
            </h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <a href="{{ route('product') }}">{{ __('backend.product_management') }}</a> / Edit Product
            </small>
        </div>
        <div class="box-body">
            <!-- <form class="cmxform" id="productForm" method="post" action="" autocomplete="off"> -->
            {{ Form::open(['route' => ['product.update', $productData->id], 'method' => 'POST', 'files' => true, 'enctype' => 'multipart/form-data', 'id' => 'labelForm']) }}

            <div class="personal_informations">
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.product_name') !!} [EN]<span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Product Name [EN]" value="{{ old('product_name',@$productData->product_name) }}">
                        @if ($errors->has('product_name'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('product_name') }}</span>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.product_name') !!} [FR] <span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="product_name_fr" id="product_name_fr" class="form-control" placeholder="Product Name [FR]" value="{{ old('product_name_fr', @$productData->product_name_fr ) }}">
                        @if ($errors->has('product_name_fr'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('product_name_fr') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-3 form-control-label">Brand <span class="valid_field">*</span></div>
                    <div class="col-sm-9">
                        <select name="brand_id" id="brand_id" class="form-control" value="{{ old('brand_id', @$productData->brand_id ) }}">
                            <option value="">Select Brand</option>
                            @foreach ($brands as $key => $value)
                            <option value="{{ $value->id }}" {{ old('brand_id', @$productData->brand_id ) == $value->id ? 'selected' : '' }}>
                                {{ ucfirst($value->title) }}
                            </option>
                            @endforeach
                        </select>
                        @if ($errors->has('brand_id'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('brand_id') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-3 form-control-label">{{ __('backend.product_category') }} <span class="valid_field">*</span></div>
                    <div class="col-sm-9">
                        <select name="category_id" id="category_id" onchange="getSubCatList(this)" class="form-control" value="{{ old('category_id', @$productData->category_id ?: '') }}">
                            <option value="">Select Category</option>
                            @if ($categories)
                            @foreach ($categories as $item)
                            <?php $selected = ''; ?>
                            @if ($item->id == $productData->category_id)
                            <?php $selected = 'selected'; ?>
                            @endif
                            <option {{ $selected }} value="{{ $item->id }}">{{ $item->title }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                        @if ($errors->has('category_id'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('category_id') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-3 form-control-cms">Subcategory <span class="valid_field">*</span></div>
                    <div class="col-sm-9">
                        <!-- <select name="subcategory_id" id="subcategory_id" class="form-control"  value="{{ old('subcategory_id', @$productData->subcategory_id) }}">
                            <option value="">Select Subcategory</option>
                        </select> -->

                        <select id="subcategory_id" name="subcategory_id" class="form-control" value="{{ old('subcategory_id', @$productData->subcategory_id) }}">
                            <option value="" selected>Select SubCategory</option>

                            @if(!empty($subcategories))
                            @foreach($subcategories as $value)
                            <option value="{{$value->id}}" {{ ($productData->subcategory_id == $value->id) ? 'selected' : ''}}>{{$value->title}}</option>
                            @endforeach
                            @endif
                        </select>
                        @if ($errors->has('subcategory_id'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('subcategory_id') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">Images <span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="file" name="property_images[]" id="property_images" class="form-control" accept="image/*" placeholder="{{ __('backend.property_images') }}" multiple>
                        <!-- {{-- <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('property_images') }}</span>
                        </span>
                        <br> --}} -->
                        <span class="help-block" id="property_images_input_error">

                            @if (!empty(@$errors) && @$errors->has('property_images.*'))
                            <span style="color: red;" class='validate'>{{ is_string($errors->first('property_images.*'))?$errors->first('property_images.*'):@$errors->first('property_images.*')[0] }}
                            </span>
                            @endif
                            <span style="color: red;" class='validate'>{{ $errors->first('property_images') }}</span>
                        </span>
                        <div>
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                Choose maximum of 5 images, .png, .jpg, .jpeg files only.
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
                        <!-- {{-- <br>
                        <span class="help-block" id="property_images_input_error">
                            @if (!empty(@$errors) && @$errors->has('property_images'))
                            <span style="color: red;" class='validate'>{{ $errors->first('property_images') }}
                        </span>
                        @endif
                        </span> --}} -->
                    </div>
                </div>

                @if ($productData->get_product_images->count() > 0)
                <div class="form-group row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <div class="mt-1 text-center old_images">
                            <h3>Old Images</h3>
                            <div class="old-images-div">
                                <div class="row">
                                    @foreach ($productData->get_product_images as $key => $image)
                                    <div class="col-sm-2 property_images_old">
                                        <div id="property_photo_{{ $image->id }}">
                                            <img src="{{ asset('uploads/product/' . $image->image) }}" alt="Property image" style="height:100px; width:100px; padding-right:10px;">
                                            <br>
                                            <br>
                                            {{-- <div class="delete">
                                                <a onclick="deleteImage(this)" data-image_id="{{ $image->id }}" class="btn btn-sm btn-default">{!! __('backend.delete') !!}</a>
                                            {{ $image->property_image }}
                                        </div> --}}
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
                <div class="col-sm-12">
                    <div class="mt-1 text-center uploaded_images" style="display: none">
                        <h3>New Uploaded Images</h3>
                        <div class="images-preview-div">

                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 form-control-label">Product Video (optional)</span></label>
                <div class="col-sm-9">
                    <span class="video-tag">
                        @if (@$productData->video)
                        <video src="{{asset('uploads/product/' . $productData->video)}}" height="170px"></video>
                        @endif
                    </span>
                    <input type="file" name="video" id="video" class="form-control" accept="video/mp4">
                    <div>
                        <small>
                            <i class="material-icons">&#xe8fd;</i>
                            Up to 20 Mb - MP4 format only
                        </small>
                    </div>
                    <br>
                    @if ($errors->has('video'))
                    <span class="help-block">
                        <span style="color: red;" class='validate'>{{ $errors->first('video') }}</span>
                    </span>
                    @endif
                    @if (@$productData->video)
                    <div class="delete m-t-xs" style="margin-top: 5px !important;" id="delete-video">
                        <a onclick="deleteVideo(this)" data-image_id="{{ $image->id }}" class="btn btn-sm btn-default">{!! __('backend.delete') !!}</a>
                        <input type="hidden" name="is_video_delete" id="is_video_delete" value="">
                    </div>
                    <div class="col-sm-4 p-a-xs" style="display: none" id="undo-video">
                        <a onclick="undoVideo(this)" >
                            <i class="material-icons">&#xe166;</i> {!! __('backend.undoDelete') !!}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="product-multi-variant-class">
                @php
                $product_variants = old('prod_variant',@$productData->get_product_variants->toArray());

                if(!$product_variants) {
                $product_variants = [
                [
                "variant_uof" => "",
                "variant_size" => "",
                "variant_price" => "",
                "variant_qty" => "",
                // "variant_discounted_price" => ""
                ]
                ];
                }
                @$k =0;
                @endphp

                @foreach ($product_variants as $key => $variantArr)
                <div class="product-single-variant" data-id="{{@$variantArr['id']}}" data-product_id="{{$productData->id}}">
                    <input type="hidden" name="prod_variant[{{$key}}][id]" value="{{ @$variantArr['id'] }}">
                    @if($k==0)<p style="font-size:11px; margin-bottom:4px;">This will show default unit & price for product list & detail page</p>@endif
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">Product Attributes <span class="valid_field">*</span></label>
                        <div class="col-sm-3">
                            <select name="prod_variant[{{$key}}][variant_uof]" class="form-control" id="uof_{{$key}}">
                                <option value="">Select Unit</option>
                                @foreach ($uofs as $item)
                                <option value="{{$item->id}}" @selected(($item->id==@$variantArr['variant_uof']))>{{$item->title}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('prod_variant.'.$key.'.variant_uof'))
                            <span class="help-block">
                                <span style="color: red;" class='validate'>{{ $errors->first('prod_variant.'.$key.'.variant_uof') }}</span>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-{{$key==0?6:4}}">
                            <input type="text" name="prod_variant[{{$key}}][variant_size]" maxlength="5" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Size eg:200" value="{{ @$variantArr['variant_size'] }}">
                            @if ($errors->has('prod_variant.'.$key.'.variant_size'))
                            <span class="help-block">
                                <span style="color: red;" class='validate'>{{ $errors->first('prod_variant.'.$key.'.variant_size') }}</span>
                            </span>
                            @endif
                        </div>
                        @if ($key>0)
                        <div class="col-sm-2 text-right">
                            <button type="button" onclick="remove_current_variant(this)" class="btn btn-danger">Remove</button>
                        </div>
                        @endif
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">Original Price ({{ @$settings->currency_symbol }}) <span class="valid_field">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="prod_variant[{{$key}}][variant_price]" maxlength="7" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Original Price" value="{{@$variantArr['variant_price']?$variantArr['variant_price']+0:'' }}">
                            @if ($errors->has('prod_variant.'.$key.'.variant_price'))
                            <span class="help-block">
                                <span style="color: red;" class='validate'>{{ $errors->first('prod_variant.'.$key.'.variant_price') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">Discounted Price ({{ @$settings->currency_symbol }})</label>
                        <div class="col-sm-9">
                            <input type="text" name="prod_variant[{{$key}}][variant_discounted_price]" maxlength="7" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Discounted Price" value="{{ @$variantArr['variant_discounted_price']?@$variantArr['variant_discounted_price']+0:"" }}">
                            @if ($errors->has('prod_variant.'.$key.'.variant_discounted_price'))
                            <span class="help-block">
                                <span style="color: red;" class='validate'>{{ $errors->first('prod_variant.'.$key.'.variant_discounted_price') }}</span>
                            </span>
                            @endif

                        </div>
                        <input type="hidden" name="prod_variant[{{$key}}][id]" value="{{@$variantArr['id']}}">
                    </div> --}}
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">Qty <span class="valid_field">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="prod_variant[{{$key}}][variant_qty]" maxlength="5" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Qty" value="{{@$variantArr['variant_qty']}}">
                            @if ($errors->has('prod_variant.'.$key.'.variant_qty'))
                            <span class="help-block">
                                <span style="color: red;" class='validate'>{{ $errors->first('prod_variant.'.$key.'.variant_qty') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <?php $k++; ?>
                @endforeach

            </div>

            <div class="form-group row">
                <div class="col-sm-12 text-right">
                    <button type="button" class="addMoreVariantsBtn btn btn-info" data-target_multiplication=".product-multi-variant-class" onclick="return add_more_variant();">+ Add more variant</button>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 form-control-label" style="display: flex;">Short {!! __('backend.description') !!} [EN]
                    <span class="valid_field">*</span></label>
                <div class="col-sm-9">
                    <!-- <input type="text" name="description" id="description" class="form-control" placeholder="Category Description" value="{{ old('description') }}"> -->
                    <textarea class="form-control" id="short_description" name="short_description" placeholder="Product Short Description">{{ old('short_description', @$productData->short_description) }}</textarea>
                    @if ($errors->has('short_description'))
                    <span class="help-block">
                        <span style="color: red;" class='validate'>{{ $errors->first('short_description') }}</span>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 form-control-label" style="display: flex;">Short {!! __('backend.description') !!} [FR]<span class="valid_field">*</span>
                </label>
                <div class="col-sm-9">
                    <!-- <input type="text" name="description" id="description" class="form-control" placeholder="Category Description" value="{{ old('description') }}"> -->
                    <textarea class="form-control" id="short_description_fr" name="short_description_fr" placeholder="Product Short Description">{{ old('short_description_fr', @$productData->short_description_fr) }}</textarea>
                    @if ($errors->has('short_description_fr'))
                    <span class="help-block">
                        <span style="color: red;" class='validate'>{{ $errors->first('short_description_fr') }}</span>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3 form-control-cms">Long Description [EN]<span class="valid_field">*</span> </div>
                <div class="col-sm-9">
                    <textarea class="form-control" id="page_content" name="page_content">{{old('page_content', @$productData->description)}}</textarea>
                    @if ($errors->has('page_content'))
                    <span class="help-block">
                        <span style="color: red;" class='validate'>{{ $errors->first('page_content') }}</span>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3 form-control-cms">Long Description [FR]<span class="valid_field">*</span></div>
                <div class="col-sm-9">
                    <textarea class="form-control" id="page_content_fr" name="page_content_fr">{{old('page_content_fr', @$productData->page_content_fr)}}</textarea>
                    @if ($errors->has('page_content_fr'))
                    <span class="help-block">
                        <span style="color: red;" class='validate'>{{ $errors->first('page_content_fr') }}</span>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3">Add product as Bestseller</label>
            <div class="col-sm-9">
                <input type="checkbox" style="cursor:pointer;" id="ans_yes" name="is_product_bestseller" value="1" @checked((old('is_product_bestseller', @$productData->is_product_bestseller) == 1)?true:false)>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3">Is offer ? </label>
            <div class="col-sm-9">
                <input type="checkbox" style="cursor:pointer;" id="offer" name="offer" value="1" @checked((old('offer', @$productData->offer) == 1)?true:false)>
            </div>
        </div>
        <div class="form-group row m-t-md">
            <div class="offset-sm-3 col-sm-9">
                <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> Update</button>
                <a href="{{ route('product') }}" class="btn btn-default m-t">
                    <i class="material-icons">
                        &#xe5cd;</i> {!! __('backend.cancel') !!}
                </a>
            </div>
        </div>

        {{ Form::close() }}
    </div>
</div>
</div>
@endsection
@push('after-scripts')
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">
    function deleteImage(element) {
        var __element = $(element);
        var image_id = __element.data('image_id');
        document.getElementById('property_photo_' + image_id).style.display = 'none';
        //document.getElementById('property_photo_' + image_id).style.display = 'none';
        // {
        //     {
        //         document.getElementById('property_photo_delete_' + image_id).value = '1';
        //     }
        // }
        document.getElementById('is_deleted_' + image_id).checked = true;
        document.getElementById('undo_' + image_id).style.display = 'block';
        document.getElementById('images_id_' + image_id).value = '';
        afterDisableRemoveImage();
    }

    function undoDeleteImage(element) {
        var __element = $(element);
        var image_id = __element.data('image_id');
        document.getElementById('property_photo_' + image_id).style.display = 'block';
        // {
        //     {
        //         document.getElementById('property_photo_delete_' + image_id).value = '0';

        //     }
        // }
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
        if (uploaded_item <= 5 && remove_image_count != 0) {
            $('#property_images').attr('disabled', false);
            $("#property_images_input_error").html(``);
        } else {
            $('#property_images').attr('disabled', true);
            $("#property_images_input_error").html(`<span style="color: red;" class='validate'>You can not upload images now.</span>`);
        }
    }

    // function getFileUploadLimit() {
    //     var fileUploadLimit = 5;
    //     var remainUploads = {{5 - (@$productData->get_product_images->count()?:0)}};
    //     return remainUploads;
    // }

    function getFileUploadLimit() {
        var remove_image_count = 0;
        $('input[name="images_ids[]"]').each(function() {
            remove_image_count = remove_image_count + ($.trim($(this).val()) == "" ? 1 : 0);
        });
        var image_count = '{{$productData->get_product_images->count()}}';
        if (remove_image_count != 0) {
            var image_count = image_count - remove_image_count;
        }
        var fileUploadLimit = 5;
        var remainUploads = 5;
        if (image_count != '') {
            var remainUploads = (fileUploadLimit - image_count);
        }
        return remainUploads;
    }

    //     function getSubCatList(thisitem) {

    // var idCountry = $('#category_id').val();
    // var cat_id = $('#cate_id').val();
    // //alert(category_id);
    // $('#sub_category_id').html('');
    // $('#sub_category_id').html('<option value="">Select Subcategory</option>');
    // $.ajax({
    //     url: "{{ route('product.getsubcatlist') }}",
    //     type: "POST",
    //     data: {
    //         id: idCountry,
    //         cat_id: cat_id,
    //         _token: '{{ csrf_token() }}'
    //     },
    //     dataType: 'json',
    //     success: function(result) {
    //         console.log(result);

    //         // $('#category_id').html('<option value="">Select Category</option>'); 
    //         $.each(result.sub, function(key, value) {
    //             var selected = '';
    //             selected = value.category_id == idCountry ? "selected" : "";
    //             $("#sub_category_id").append('<option ' + selected + ' value="' + value.id +
    //                 '">' +
    //                 value.title + '</option>');
    //         });
    //     }
    // });
    // }
    function getSubCatList(thisitem) {

        var idCountry = $('#category_id').val();
        var cat_id = $('#cate_id').val();
        //alert(category_id);
        //$('#subcategory_id').html('');
        $('#subcategory_id').html('<option value="">Select Subcategory</option>');
        $.ajax({
            url: "{{ route('product.getsubcatlist') }}",
            type: "POST",
            data: {
                id: idCountry,
                cat_id: cat_id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result.sub);

                //$('#category_id').html('<option value="">Select Category</option>'); 
                //$('#subcategory_id').html('');
                $.each(result.sub, function(key, value) {
                    var selected = '';
                    // selected = value.category_id == idCountry ? "selected" : "";
                    $("#subcategory_id").append('<option value="' + value.id +
                        '">' +
                        value.title + '</option>');
                });
            }
        });
    }
    $(function() {
        // Multiple images preview with JavaScript                
        var previewImages = function(input, imgPreviewPlaceholder) {
            var imageUploadLimit = getFileUploadLimit();
            if (input.files) {
                var filesAmount = input.files.length;
                $(imgPreviewPlaceholder).html("");
                if (filesAmount == 0) {
                    $(".uploaded_images").hide();
                } else if (filesAmount > imageUploadLimit) {
                    $('#property_images').val("");
                    var messageToShow;
                    if (imageUploadLimit > 0) {
                        messageToShow = 'You can now upload only ' + imageUploadLimit + ' images.'
                    } else {
                        messageToShow = "You can not upload images now."
                    }
                    $(".uploaded_images").hide();
                    $("#property_images_input_error").html(
                        `<span style="color: red;" class='validate'>Product max image exceeded, ${messageToShow} </span>`
                    );
                    // {
                    //     {
                    //         setTimeout(() => {
                    //             $("#property_images_input_error").hide(1200, () => {
                    //                 $("#property_images_input_error").html("");
                    //             })
                    //         }, 3000);

                    //     }
                    // }
                } else {
                    $("#property_images_input_error").html("");
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
                    $(".uploaded_images").show();
                }
            }
        };
        $('#property_images').on('change', function() {
            var selection = document.getElementById('property_images');
            for (var i = 0; i < selection.files.length; i++) {
                var ext = selection.files[i].name.substr(-3);
                var fileSize = selection.files[i].size;
                const fileMb = fileSize / 1024 ** 2;
                if (ext !== "png" && ext !== "jpg" && ext !== "jpeg") {
                    $("#property_images_input_error").html('<span style="color: red;">The files must be a file of type: jpg, jpeg, png.<span>');
                    return false;
                }
                if (fileMb >= 2) {
                    $("#property_images_input_error").html('<span style="color: red;">Please select files less than 2MB.<span>');
                    return false;
                }
            }
            previewImages(this, 'div.images-preview-div');
        });
    });
    if (getFileUploadLimit() <= 0) {
        let messageToShow = "You can not upload images now.";
        $("#property_images_input_error").html(
            `<span style="color: red;" class='validate'>{{ __('backend.propertyMaxImageLimitExceded') }} ${messageToShow} </span>`
        );
        $('#property_images').attr('disabled', true);
    }

    $(document).submit('#labelForm', function(event) {
        $("#property_images_input_error").html('');
        var image_count = 0;
        $('input[name="images_ids[]"]').each(function() {
            image_count = image_count + ($.trim($(this).val()) == "" ? 1 : 0);
        });
        var upload_files = $('#property_images')[0].files.length;
        var deleted_item = $('input[name="deleted_image[]"]').length;
        if ((image_count != 0 && upload_files == 0) && (image_count == deleted_item && upload_files == 0)) {
            event.preventDefault();
            $("#property_images_input_error").html(
                `<span style="color: red;" class='validate'>The image field is required.</span>`
            );
            return false;
        }
    });

    function isNumberKey(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

            return false;
        return true;

    }

    function isNumberBlock(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        // alert($(this).val())
        // evt.which.val().length
        if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            // alert()
            return false;
        return true;
    }
    // image.onchange = evt => {

    //     const [file] = image.files
    //     if (file) {
    //         blah.src = URL.createObjectURL(file)
    //     }
    // }
</script>

<script>
    $(function() {
        $('.icp-auto').iconpicker({placement: '{{ @Helper::currentLanguage()->direction == 'rtl ' ? 'topLeft ' : 'topRight ' }}'});
    });

    function sendFile(file, editor, welEditable, lang) {
        data = new FormData();
        data.append("file", file);  
        data.append("_token", "{{ csrf_token() }}");

    }

    // update progress bar
    function progressHandlingFunction(e) {
        if (e.lengthComputable) {
            $('progress').attr({
                value: e.loaded,
                max: e.total
            });
            // reset progress on complete
            if (e.loaded == e.total) {
                $('progress').attr('value', '0.0');
            }
        }
    }
</script>
<script>
    // CKEDITOR.on('instanceReady', function(ev) {
    //     document.getElementById('eMessage').innerHTML = 'Instance <code>' + ev.editor.name + '<\/code> loaded.';

    //     document.getElementById('eButtons').style.display = 'block';
    // });

    function InsertHTML() {
        var editor = CKEDITOR.instances.editor1;
        var value = document.getElementById('htmlArea').value;

        if (editor.mode == 'wysiwyg') {
            editor.insertHtml(value);
        } else
            alert('You must be in WYSIWYG mode!');
    }

    function InsertText() {
        var editor = CKEDITOR.instances.editor1;
        var value = document.getElementById('txtArea').value;

        if (editor.mode == 'wysiwyg') {
            editor.insertText(value);
        } else
            alert('You must be in WYSIWYG mode!');
    }

    function SetContents() {
        var editor = CKEDITOR.instances.editor1;
        var value = document.getElementById('htmlArea').value;

        editor.setData(value);
    }

    function GetContents() {
        var editor = CKEDITOR.instances.editor1;
        alert(editor.getData());
    }

    function ExecuteCommand(commandName) {
        var editor = CKEDITOR.instances.editor1;

        if (editor.mode == 'wysiwyg') {
            editor.execCommand(commandName);
        } else
            alert('You must be in WYSIWYG mode!');
    }

    function CheckDirty() {
        var editor = CKEDITOR.instances.editor1;
        alert(editor.checkDirty());
    }

    function ResetDirty() {
        var editor = CKEDITOR.instances.editor1;
        editor.resetDirty();
        alert('The "IsDirty" status has been reset');
    }

    function Focus() {
        CKEDITOR.instances.editor1.focus();
    }

    function onFocus() {
        document.getElementById('eMessage').innerHTML = '<b>' + this.name + ' is focused </b>';
    }

    function onBlur() {
        document.getElementById('eMessage').innerHTML = this.name + ' lost focus';
    }

    CKEDITOR.replace('page_content', {
        on: {
            // focus: onFocus,
            // blur: onBlur,
            pluginsLoaded: function(evt) {
                var doc = CKEDITOR.document,
                    ed = evt.editor;
                if (!ed.getCommand('bold')) doc.getById('exec-bold').hide();
                if (!ed.getCommand('link')) doc.getById('exec-link').hide();
            }
        }
    });

    CKEDITOR.replace('page_content_fr', {
        on: {
            // focus: onFocus,
            // blur: onBlur,
            pluginsLoaded: function(evt) {
                var doc = CKEDITOR.document,
                    ed = evt.editor;
                if (!ed.getCommand('bold')) doc.getById('exec-bold').hide();
                if (!ed.getCommand('link')) doc.getById('exec-link').hide();
            }
        }
    });
</script>

<script type="text/javascript">
    CKEDITOR.config.height = '400px';
</script>

<script>
    // const empty_product_subcategory = () => $('#subcategory_id option:not(:first-child)').remove();
    // var product_category = "{{old('category_id', @$productData->category_id)}}";
    // var product_subcategory = "{{old('subcategory_id', @$productData->subcategory_id)}}";

    // function getSubCategory() {
    //     var category_ele = $('#category_id');
    //     var subcategory_ele = $("#subcategory_id");
    //     var category_val = category_ele.val();
    //     if (!category_ele) {
    //         category_val = product_category;
    //     }
    //     // empty_product_subcategory();
    //     if (category_val) {
    //         var __url = "{{route('getsubcategories', ['id' => ':id'])}}".replace(':id', category_val);
    //         $.ajax({
    //             url: __url,
    //             type: 'post',
    //             dataType: 'json',
    //             success: function(data) {
    //                 var subcats = data.data;
    //                 if (data.code == 200) {
    //                     subcats.map((ele, index) => {
    //                         subcats.map((ele, index) => {
    //                             var is_selected = product_subcategory ? 'selected="selected"' : "";
    //                             subcategory_ele.append(`<option value="${ele.id}" ${is_selected}>${ele.title}</option>`);
    //                         });
    //                     });
    //                 } else {
    //                     alert('Something went wrong.');
    //                 }
    //             }
    //         })
    //     }
    // }

    (function() {
        // getSubCategory();
        $("#category_id").on('change', function(e) {
            getSubCategory();
        });
    })();

    var variant_count = {{count(old('prod_variant', @$productData -> get_product_variants ? : []) ? : []) ? : 1}};

    function add_more_variant() {
        let get_variant_url = "{{route('product.add_more_variant')}}";
        $.ajax({
            url: get_variant_url,
            type: 'post',
            data: {
                count: variant_count
            },
            success: function(result) {
                if (result.success == true) {
                    $(".product-multi-variant-class").append(result.html);
                    variant_count++;
                }
            }
        });
    }

    function remove_current_variant(ele) {
        let element = $(ele);
        let singleVariantBlock = element.parents('.product-single-variant');
        if (singleVariantBlock.hasClass('newadded')) {
            singleVariantBlock.remove();
        } else {
            if (confirm('Are you sure you want to delete this?')) {
                remove_variant(singleVariantBlock);
            }
        }
    }

    function remove_variant(singleVariant) {
        let variant_id = singleVariant.data('id');
        let product_id = singleVariant.data('product_id');

        $.ajax({
            url: '{{route("product.variant.remove")}}',
            type: 'post',
            data: {
                variant_id: variant_id,
                product_id: product_id
            },
            success: function(result) {
                singleVariant.remove();
            }
        });
    }

    function deleteVideo(){
        let product_id = "{{$productData->id}}";
        $("#is_video_delete").val('1');
        $(".video-tag,#delete-video").hide();
        $("#undo-video").show();
    }
    function undoVideo(){       
        $("#is_video_delete").val('0');
        $(".video-tag, #delete-video").show();
        $("#undo-video").hide();       
    }
    
</script>
@endpush