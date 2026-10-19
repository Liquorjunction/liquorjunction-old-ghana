@extends('wholesaler.layouts.master')
@section('title', 'Product | Wholesaler Panel')
@push("after-styles")
<style type="text/css">
    #blah {
    height: 50% !important;
    width: 25% !important;
}

#blah1 {
    height: 50% !important;
    width: 25% !important;
}
</style>
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

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
                    <a href="{{ route('adminwholesalerHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('wholesalerproduct') }}">{{ __('backend.product') }}</a>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('cms') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
               <!-- <form class="cmxform" id="productForm" method="post" action="" autocomplete="o  ff"> -->
                {{Form::open(['route'=>['wholesalerproduct.update',$productData->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'labelForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>{!!  __('backend.cms') !!}</h3>
                    <br>
                    <br> -->
                    <div class="form-group row">
                        <div class="col-sm-3 form-control-cms">{{__('backend.product_category')}} <span class="valid_field">*</span></div>
                        <div class="col-sm-9">
                            <select name="category_id" id="category_id" class="form-control" value="{{@$subcategoryData->category_id}}">
                                <option value="">Select category</option>
                                @foreach ($categories as $key => $value)
                                    <option value="{{$value->id}}" {{ ($productData->category_id == $value->id) ? 'selected' : ''}} >{{ucfirst($value->title)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
           <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.product_name') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="product_name" id="product_name" class="form-control"  placeholder="Product Name" value="{{@$productData->product_name}}">
                            @if ($errors->has('product_name'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('product_name') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">Short {!!  __('backend.description') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-9">
                            <!-- <input type="text" name="description" id="description" class="form-control" placeholder="Category Description" value="{{old('description')}}"> -->
                            <textarea class="form-control" id="short_description" name="short_description" placeholder="Product Short Description">{{@$productData->short_description}}</textarea>
                            @if ($errors->has('short_description'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('short_description') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">{!!  __('backend.retail_price') !!}({{@$settings->currency_symbol}}) <span class="valid_field">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="retail_price" id="retail_price" maxlength="5" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Product Price" value="{{@$productData->retail_price}}">
                           @if ($errors->has('retail_price'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('retail_price') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">{!!  __('backend.discount_price') !!}({{@$settings->currency_symbol}})  </label>
                        <div class="col-sm-9">
                            <input type="text" name="discount_price" id="discount_price" maxlength="5" onkeypress="return isNumberBlock(event)" class="form-control" placeholder="Discount Price" value="{{@$productData->discount_price}}">
                            @if ($errors->has('discount_price'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('discount_price') }}</span>
                            </span>
                            @endif
                           
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.product_for') !!}</label>
                        <div class="col-sm-9">
                             <input type="checkbox" id="in_store" name="in_store" value="1" disabled {{ ($productData->in_store == 1) ? 'checked' : ''}}>
                              <label for="in_store"> In Store</label>
                               <input type="checkbox" id="in_online" name="in_online" value="1" disabled {{ ($productData->in_online == 1) ? 'checked' : ''}}>
                              <label for="in_online"> Online</label>
                        </div>
                    </div>
                    <div class="form-group row">
                            <label class="col-sm-3 form-control-label">{!!  __('backend.product_image') !!} <span class="valid_field">*</span></label>
                            <div class="col-sm-9">
                                <a href="{{ asset('uploads/product/').'/'.$productData->product_image }}" target="_blank"><img height="100" width="100" src="<?php
                                    if (!empty($productData->product_image)) { ?>
                                       {{ asset('uploads/product/'.$productData->product_image) }}
                                    <?php }else{ ?>
                                        {{ asset('assets/dashboard/images/no_image_found.jpg')}}
                                    <?php }
                                 ?>" alt="your image" /></a>
                               
                                <input type="file" name="image" id="image3" class="form-control" style="border: none; margin-left: -13px;" accept="image/png, image/jpg, image/jpeg">
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .png, .jpg, .jpeg
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 form-control-label">Tech Data Sheets <span class="valid_field">*</span></label>
                            <div class="col-sm-9">
                                 <a href="{{ asset('uploads/product/').'/'.$productData->tech_data_sheet }}" target="_blank"><img height="100" width="100" src="{{ asset('assets/dashboard/images/pdf_icon.webp')}}" alt="your image" /></a>
                                <input type="file" name="tech_data_sheet" id="tech_data_sheet" class="form-control" style="border: none; margin-left: -13px;" accept="application/pdf">
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .pdf
                                </small>
                                @if ($errors->has('tech_data_sheet'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('tech_data_sheet') }}</span>
                            </span>
                            @endif
                            </div>
                        </div>

                        <div class="form-group row">                        
                            <label class="col-sm-3 form-control-label">Images (Optional)</label>                     
                            <div class="col-sm-9">                            
                                <input type="file" name="property_images[]" id="property_images" class="form-control"  accept="image/*"
                                placeholder="{{ __('backend.property_images') }}" multiple>                            
                                <span class="help-block" id="property_images_input_error">                                
                                    @if (!empty(@$errors) && @$errors->has('property_images'))
                                    <span style="color: red;" class='validate'>{{ $errors->first('property_images') }}
                                    </span>                                @endif
                            </span>                        
                        </div>                    
                    </div>

                    @if( $productimage->count() > 0 )
                        <div class="form-group row">              
                            <div class="col-sm-12">                                
                                <div class="mt-1 text-center old_images">                                    
                                    <h3>Old Images</h3>                                    
                                    <div class="old-images-div">                                        
                                        <div class="row">                                            
                                            @foreach ($productimage as $key => $image)
                                                <div class="col-sm-3 property_images_old">                                   
                                                    <div id="property_photo_{{ $image->id }}">                            
                                                        <img src="{{ asset('uploads/product/'.$image->image) }}"
                                                            alt="Property image" height="170px">                                                        <br>                                                        
                                                            <div class="delete">                                  
                                                                <a onclick="deleteImage(this)" data-image_id="{{$image->id}}"
                                                                class="btn btn-sm btn-default">{!! __('backend.delete') !!}</a>                                                            {{ $image->property_image }}
                                                        </div>                                                   </div>                                                    
                                                        <div id="undo_{{ $image->id }}" class="col-sm-4 p-a-xs" style="display: none">                                                        
                                                            <a onclick="undoDeleteImage(this)" data-image_id="{{$image->id}}">                                                            
                                                                <i class="material-icons">&#xe166;</i> {!!  __('backend.undoDelete') !!}
                                                        </a>                                                    
                                                    </div>                                                    {!! Form::checkbox('deleted_image[]', $image->id, "", ['class'=>'hidden-checkboxes', 'id' => 'is_deleted_'.$image->id,'style'=>'display:none']) !!}
                                                    
                                                </div>                                            @endforeach
                                        </div>                                    </div>                                </div>                            </div>                        </div>                    @endif

                        <div class="form-group row">                  
                            <div class="col-sm-12">                            
                                <div class="mt-1 text-center uploaded_images" style="display: none">                                <h3>New Uploaded Images</h3>                                
                                    <div class="images-preview-div">
                                   
                                    </div>                            
                            </div>                        
                        </div>                    
                    </div>

                    <div class="form-group row">
                        <cms class="col-sm-3 form-control-cms">Long Description <span class="valid_field">*</span></cms>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="page_content" name="page_content" autofocus >{{@$productData->description}}</textarea>
                            @if ($errors->has('page_content'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('page_content') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail" ><i class="material-icons">&#xe31b;</i> {!! __('backend.edit') !!}</button>
                            <a href="{{ route('wholesalerproduct')}}" class="btn btn-default m-t">
                                <i class="material-icons">
                                &#xe5cd;</i> {!! __('backend.cancel') !!}
                            </a>
                    </div>
                </div>

</form>
                <!-- {{Form::close()}} -->
            </div>
        </div>
    </div>
@endsection
@push("after-scripts")
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script src="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.js") }}"></script>
    <script src="{{ asset("assets/dashboard/js/summernote/dist/summernote.js") }}"></script>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

 <script type="text/javascript">
    function deleteImage(element){
            var __element = $(element);
            var image_id = __element.data('image_id');
            document.getElementById('property_photo_'+image_id).style.display='none';
            document.getElementById('property_photo_'+image_id).style.display='none';
            {{--  document.getElementById('property_photo_delete_'+image_id).value='1';  --}}
            document.getElementById('is_deleted_'+image_id).checked = true;
            document.getElementById('undo_'+image_id).style.display='block';
        }
        function undoDeleteImage(element){
            var __element = $(element);
            var image_id = __element.data('image_id');
            document.getElementById('property_photo_'+image_id).style.display='block';
            {{--  document.getElementById('property_photo_delete_'+image_id).value='0';  --}}
            document.getElementById('is_deleted_'+image_id).checked = false;
            document.getElementById('undo_'+image_id).style.display='none';
        }

   function getFileUploadLimit(){
            var fileUploadLimit = 4;
            var remainUploads = 4;
            return remainUploads;
        }


    $(function() {
                // Multiple images preview with JavaScript                
        var previewImages = function(input, imgPreviewPlaceholder) {
                    var imageUploadLimit = getFileUploadLimit();
                    if (input.files) {
                        var filesAmount = input.files.length;
                        $(imgPreviewPlaceholder).html("");
                        if(filesAmount==0){
                            $(".uploaded_images").hide();
                        }
                        else if(filesAmount > imageUploadLimit){
                            $('#property_images').val("");
                            var messageToShow;
                            if(imageUploadLimit>0){
                                messageToShow = 'You can now upload only '+imageUploadLimit+' images.'                            }else{
                                messageToShow = "You can not upload images now."                            }
                            $(".uploaded_images").hide();
                            $("#property_images_input_error").html(`<span style="color: red;" class='validate'>{{__('backend.propertyMaxImageLimitExceded')}} ${messageToShow} </span>`);
                            {{--  setTimeout(()=>{
                                $("#property_images_input_error").hide(1200, () => {
                                    $("#property_images_input_error").html("");
                                })
                            },3000);  --}}
                        }
                        else{
                            $("#property_images_input_error").html("");
                            for (i = 0; i < filesAmount; i++) {
                                var reader = new FileReader();
                                reader.onload = function(event) {
                                    $($.parseHTML('<img>')).attr('src', event.target.result).css({
                                        'height': '200px',
                                        'width': 'auto',
                                        'margin': '10px'                                    }).appendTo(imgPreviewPlaceholder);
                                }
                                reader.readAsDataURL(input.files[i]);
                            }
                            $(".uploaded_images").show();
                        }
                    }
                };
                $('#property_images').on('change', function() {
                    previewImages(this, 'div.images-preview-div');
                });
            });
            if(getFileUploadLimit() <= 0){
                let messageToShow = "You can not upload images now.";
                $("#property_images_input_error").html(`<span style="color: red;" class='validate'>{{__('backend.propertyMaxImageLimitExceded')}} ${messageToShow} </span>`);
                $('#property_images').attr('disabled',true);
            }

    function isNumberKey(evt){ 
    //var e = evt || window.event;
    var keyCode = (evt.which) ? evt.which : evt.keyCode;
    if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
         
        return false;
            return true;
    
    }

    function isNumberBlock(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    // alert($(this).val())
// evt.which.val().length
    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode!=46)
        // alert()
        return false;
    return true;
}
    image.onchange = evt => {
             
  const [file] = image.files
  if (file) {
    blah.src = URL.createObjectURL(file)
  }
}  
 </script>

    <script>
        $(function () {
            $('.icp-auto').iconpicker({placement: '{{ (@Helper::currentLanguage()->direction=="rtl")?"topLeft":"topRight" }}'});
        });

        function sendFile(file, editor, welEditable, lang) {
            data = new FormData();
            data.append("file", file);
            data.append("_token", "{{csrf_token()}}");
            
        }

        // update progress bar
        function progressHandlingFunction(e) {
            if (e.lengthComputable) {
                $('progress').attr({value: e.loaded, max: e.total});
                // reset progress on complete
                if (e.loaded == e.total) {
                    $('progress').attr('value', '0.0');
                }
            }
        }
    </script>
    <script>
            CKEDITOR.on( 'instanceReady', function( ev ) {
        document.getElementById( 'eMessage' ).innerHTML = 'Instance <code>' + ev.editor.name + '<\/code> loaded.';
                                                                                                                                                            
        document.getElementById( 'eButtons' ).style.display = 'block';
    });

    function InsertHTML() {
        var editor = CKEDITOR.instances.editor1;
        var value = document.getElementById( 'htmlArea' ).value;

        if ( editor.mode == 'wysiwyg' )
        {
            editor.insertHtml( value );
        }
        else
            alert( 'You must be in WYSIWYG mode!' );
    }

    function InsertText() {
        var editor = CKEDITOR.instances.editor1;
        var value = document.getElementById( 'txtArea' ).value;

        if ( editor.mode == 'wysiwyg' )
        {
            editor.insertText( value );
        }
        else
            alert( 'You must be in WYSIWYG mode!' );
    }

    function SetContents() {
        var editor = CKEDITOR.instances.editor1;
        var value = document.getElementById( 'htmlArea' ).value;

        editor.setData( value );
    }

    function GetContents() {
        var editor = CKEDITOR.instances.editor1;
        alert( editor.getData() );
    }

    function ExecuteCommand( commandName ) {
        var editor = CKEDITOR.instances.editor1;

        if ( editor.mode == 'wysiwyg' )
        {
            editor.execCommand( commandName );
        }
        else
            alert( 'You must be in WYSIWYG mode!' );
    }

    function CheckDirty() {
        var editor = CKEDITOR.instances.editor1;
        alert( editor.checkDirty() );
    }

    function ResetDirty() {
        var editor = CKEDITOR.instances.editor1;
        editor.resetDirty();
        alert( 'The "IsDirty" status has been reset' );
    }

    function Focus() {
        CKEDITOR.instances.editor1.focus();
    }

    function onFocus() {
        document.getElementById( 'eMessage' ).innerHTML = '<b>' + this.name + ' is focused </b>';
    }

    function onBlur() {
        document.getElementById( 'eMessage' ).innerHTML = this.name + ' lost focus';
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
        
    </script>
    <script type="text/javascript">
        CKEDITOR.config.height='400px';
</script>
@endpush
