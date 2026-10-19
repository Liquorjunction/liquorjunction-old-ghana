@extends('wholesaler.layouts.master')
@section('title', 'Product | Wholesaler Panel')
@push("after-styles")
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
                        &#xe02e;</i> View {{ __('backend.product') }}
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
               <!-- <form class="cmxform" id="productForm" method="post" action="" autocomplete="off"> -->
                {{Form::open(['route'=>['wholesalerproduct.update',$productData->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'labelForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>{!!  __('backend.cms') !!}</h3>
                    <br>
                    <br> -->
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">{!!  __('backend.product_category') !!}</label>
                        <div class="col-sm-9">
                           
                            <div class="show_blade_div">{{@$productData->title}}</div>
                        </div>
                    </div>
           <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{!!  __('backend.product_name') !!}</label>
                        <div class="col-sm-9">
                            
                                                        <div class="show_blade_div">
                                {{@$productData->product_name}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Short {!!  __('backend.description') !!}</label>
                        <div class="col-sm-9">
                            
                                                        <!-- <div class="show_blade_div"> -->
                                
                                 <textarea class="form-control" id="description" readonly name="description"  placeholder="Product Description">{{@$productData->short_description}}</textarea>
                            <!-- </div> -->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">{!!  __('backend.retail_price') !!}</label>
                        <div class="col-sm-9">
                           
                            <div class="show_blade_div">{{@$settings->currency_symbol}}{{@$productData->retail_price}}</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" style="display: flex;">{!!  __('backend.discount_price') !!}</label>
                        <div class="col-sm-9">
                            <div class="show_blade_div">{{isset($productData->discount_price) ? $settings->currency_symbol.' '.$productData->discount_price : $settings->currency_symbol.' '.'0'}}</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3">{!!  __('backend.product_for') !!}</label>
                        <div class="col-sm-9">
                             <input type="checkbox" id="in_store" name="in_store" disabled value="1" {{ ($productData->in_store == 1) ? 'checked' : ''}}>
                              <label for="in_store"> In Store</label>
                               <input type="checkbox" id="in_online" name="in_online" disabled value="1" {{ ($productData->in_online == 1) ? 'checked' : ''}}>
                              <label for="in_online"> Online</label>
                        </div>
                    </div>
                    <div class="form-group row">
                            <label class="col-sm-3 form-control-label">{!!  __('backend.product_image') !!} </label>
                            <div class="col-sm-9">
                                <a href="{{ asset('uploads/product/').'/'.$productData->product_image }}" target="_blank"><img height="100" width="100" src="<?php
                                    if (!empty($productData->product_image)) { ?>
                                       {{ asset('uploads/product/'.$productData->product_image) }}
                                    <?php }else{ ?>
                                        {{ asset('assets/dashboard/images/no_image_found.jpg')}}
                                    <?php }
                                 ?>" alt="your image" /></a>
                               
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 form-control-label">Tech Data Sheets </label>
                            <div class="col-sm-9">
                                 <a href="{{ asset('uploads/product/').'/'.$productData->tech_data_sheet }}" target="_blank"><img src="{{ asset('assets/dashboard/images/pdf_icon.webp')}}" alt="ID Proof" height="100" width="100"></a>
                               
                            </div>
                        </div>


                    <div class="form-group row">
                        <cms class="col-sm-3 form-control-cms">Long Description </cms>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="page_content" name="page_content" autofocus disabled>{{@$productData->description}}</textarea>
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
                           
                            <a href="{{ route('wholesalerproduct')}}" class="btn btn-default m-t" style="margin-left: 70px;">
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
