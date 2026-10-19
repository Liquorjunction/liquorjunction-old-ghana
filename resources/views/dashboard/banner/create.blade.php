@extends('dashboard.layouts.master')
<?php
$title_var = "title_" . @Helper::currentLanguage()->code;
$title_var2 = "title_" . env('DEFAULT_LANGUAGE');
?>
@section('title', 'Banner')
@push("after-styles")
<link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" />
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
<style>
    .select2-container {
        width: 100% !important;
    }

    .pac-container {
        z-index: 10000 !important;
    }
</style>
<style>
        /* Style for the text color picker container */
        .text-color-picker-container {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
        }
    
        /* Adjust the color picker size */
        #textColorPicker {
            width:110px; /* Adjust as needed */
            height: 30px; /* Adjust as needed */
            border: none;
            padding: 0;
            margin: 0 0 0 10px;
            border-radius: 60px !important; /* Adjust the value to get the desired roundness */
        overflow: hidden;
        }
        .text-color-picker-container label {
        margin-left: 10px; /* Adjust spacing between color picker and label as needed */
    }
    </style>
@endpush
@section('content')
<div class="padding edit-package">
    <div class="box">
        <div class="box-header dker">
            <?php
            $title_var = "title_" . @Helper::currentLanguage()->code;
            $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
            ?>
            <h3><i class="material-icons">
                    &#xe02e;</i> {{ __('backend.topicNew') }} Banner
            </h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <a href="{{ route('banner') }}">{{ __('backend.banner_management') }}</a> / New Banner
            </small>
        </div>
        <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{ route('banner') }}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="alert alert-danger alert-block validate email_validate" style="display:none;">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong id="email_msg"></strong>
        </div>

        @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block validate">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
        @endif


        @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block validate">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
        @endif

        <div class="box-body">
            {{Form::open(['route'=>['banner.store'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'delivery_chargeForm' ])}}

            <div class="personal_informations">
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Title [EN]</label>
                    <div class="col-sm-8">
                        <input type="text" name="title" id="title" class="form-control" placeholder="Title [EN]" value="{{old('title')}}">
                        </div>
                        <div class="col-sm-2">
                                    <div class="text-color-picker-container">
                                    <label for="textColorPicker" class="form-control-label">Color:</label>
                                        <input type="color" id="textColorPicker" class="form-control" value="{{ isset($banner->text_color) ? $banner->text_color : '#000000' }}">
                                        <input type="hidden" name="text_color" id="textColorInput" value="{{ isset($banner->text_color) ? $banner->text_color : '#000000' }}">
                                    </div>
                                </div>
                            </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Title [FR]</label>
                    <div class="col-sm-8">
                        <input type="text" name="title_fr" id="title_fr" class="form-control" placeholder="Title [FR]" value="{{old('title_fr')}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-cms">Description [EN]</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Description [EN]">{{old('description')}}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-cms">Description [FR]</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="description_fr" name="description_fr" rows="5" placeholder="Description [FR]">{{old('description_fr')}}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-cms">Type<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <select name="type" id="type" onchange="showHide()" class="form-control" value="">
                            <option value="">Select type</option>
                            <option value="0">Brand</option>
                            <option value="1">Category</option>
                            <option value="2">Product</option>
                            <option value="3">Custom URL</option>
                        </select>
                        @if ($errors->has('type'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('type') }}</span>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row brand">
                    <div class="col-sm-2 form-control-cms ">Brand<span class="valid_field">*</span></div>
                    <div class="col-sm-10">
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
                        <div class="col-sm-2 form-control-cms ">{{ __('backend.category') }}<span class="valid_field">*</span></div>
                        <div class="col-sm-10">
                            <select name="category_id" id="category_id" class="form-control" value="">
                                <option value="">Select category</option>
                                @foreach ($categories as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ ucfirst($value->title) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="form-group row subcategory">
                        <div class="col-sm-2 form-control-cms ">Subcategory</div>
                        <div class="col-sm-10">
                            <select name="subcategory_id" id="subcategory_id" onchange="getSubCatList(this)"
                                    class="form-control" value="">
                                    <option value="">Select Subcategory</option>
                            </select>
                        </div>
                </div>
                <div class="form-group row product">
                        <div class="col-sm-2 form-control-cms ">Product<span class="valid_field">*</span></div>
                        <div class="col-sm-10">
                            <select name="product_id" id="product_id" class="form-control" value="">
                                <option value="">Select product</option>
                                @foreach ($product as $key => $value)
                                <option value="{{ $value->id }}" >
                                    {{ ucfirst($value->product_name) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="form-group row url" style="display: none;">
                    <label class="col-sm-2 form-control-cms">URL<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="banner_url" id="banner_url" class="form-control" placeholder="URL" value="{{old('banner_url')}}">
                        @if ($errors->has('banner_url'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('banner_url') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-cms">Banner Type<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <select name="banner_type"  class="form-control" value="">
                            <option value="">Select banner type</option>
                            <option value="1">Main banner</option>
                            <option value="2">Highlight</option>
                            <option value="3">Offer</option>
                        </select>
                        @if ($errors->has('banner_type'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('banner_type') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Image<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <div class="mb-1">
                            <img id="blah" height="100" width="100" src="{{ asset('assets/dashboard/images/no_image_found.jpg') }}" alt="your image" style="width:100px !important; height:100px !important; margin-bottom: 10px;" />
                        </div>
                        <input type="file" name="photo" id="bannerimage" class="form-control" accept="image/*">
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
                                Recommended size for main banner & offer 1440(Width) x 560(Height).
                            </small> 
                            <br>                              
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                Recommended size for our highlights 628(Width) x 472(Height).
                            </small>
                        </div>
                    </div>
                </div>
                
                {{-- <div class="form-group row">
                    <label class="col-sm-2">Offer</label>
                    <div class="col-sm-10">
                        <input type="checkbox" style="cursor:pointer;" id="offer" name="offer" value="1">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-2">Highlight</label>
                    <div class="col-sm-10">
                        <input type="checkbox" style="cursor:pointer;" id="highlight" name="highlight" value="1" @checked((old('highlight')==1)?true:false)>
                    </div>
                </div> --}}
            </div>
            <div class="form-group row m-t-md">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
                    <a href="{{ route('banner')}}" class="btn btn-default m-t">
                        <i class="material-icons">
                            &#xe5cd;</i> {!! __('backend.cancel') !!}
                    </a>
                </div>
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>
@endsection
@push("after-scripts")
<script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script src="{{ asset('assets/dashboard/js/select2.min.js') }}"></script>
<script type="text/javascript">
    function mutliselect2() {
        $('#category').select2();
    }
    mutliselect2();

    bannerimage.onchange = evt => {
        const [file] = bannerimage.files
        fileName = document.querySelector('#bannerimage').value;
        extension = fileName.split('.').pop();
        document.querySelector('.output').textContent = extension;
        if (file) {
            blah.src = URL.createObjectURL(file)
        }
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize" async defer></script>
<script type="text/javascript">
    function initialize() {

        var autocomplete = new google.maps.places.Autocomplete($("#address")[0], {});

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();
            console.log(longitude);
            // showmapView(latitude,longitude,0);
            $("#latitude").val(latitude);
            $("#longitude").val(longitude);
        });


    }

    $('.alphaonly').bind('keyup blur', function() {
        var node = $(this);
        node.val(node.val().replace(/[^a-zA-Z]/g, ''));
    });
</script>
<script>
    $(document).ready(function() {
        showHide();
    });

    // mutliselect2();

    function showHide() {
        var type = $("#type").val();
        console.log(type);
        $('.url').show();
        $('.category').hide();
        $('.brand').hide();
        $('.product').hide();
        $('.subcategory').hide();

        if (type != '' && type != null) {
            if (type == '1') {
                $('.url').hide();
                $('.category').show();
                $('.product').hide();
                $('.brand').hide();
                $('.subcategory').show();
            } else if(type == '2') {
                $('.url').hide();
                $('.category').hide();
                $('.product').show();
                $('.brand').hide();
                $('.subcategory').hide();
            }
            else if(type == '0') {
                $('.url').hide();
                $('.category').hide();
                $('.product').hide();
                $('.brand').show();
                $('.subcategory').hide();
            }
            else{
                $('.url').show();
                $('.category').hide();
                $('.product').hide();
                $('.brand').hide();
                $('.subcategory').hide();
            }
        }
    }
</script>
<script>
    $(function() {
        $('.icp-auto').iconpicker({
            placement: '{{ (@Helper::currentLanguage()->direction=="rtl")?"topLeft":"topRight" }}'
        });
    });
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
    CKEDITOR.on('instanceReady', function(ev) {
        document.getElementById('eMessage').innerHTML = 'Instance <code>' + ev.editor.name + '<\/code> loaded.';

        document.getElementById('eButtons').style.display = 'block';
    });

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
            focus: onFocus,
            blur: onBlur,
            pluginsLoaded: function(evt) {
                var doc = CKEDITOR.document,
                    ed = evt.editor;
                if (!ed.getCommand('bold')) doc.getById('exec-bold').hide();
                if (!ed.getCommand('link')) doc.getById('exec-link').hide();
            }
        }
    });

    CKEDITOR.replace('page_content2', {
        on: {
            focus: onFocus,
            blur: onBlur,
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
    function validate_Email() {
        var userinput = $('#email').val();
        var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i
        if (userinput != '') {
            if (!pattern.test(userinput)) {
                $('.email_validate').css('display', 'block');
                $('#email_msg').html('Not a valid Email address');
                //alert('');
                $('#email').val('');
            }
        }

    }
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


@endpush