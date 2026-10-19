@extends('dashboard.layouts.master')
@section('title', __('backend.blog'))
@push("after-styles")
<link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

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
                    &#xe02e;</i> {{ __('backend.topicNew') }} {{ __('backend.blog') }}
            </h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <a href="{{ route('blog') }}">{{ __('backend.blog_management') }}</a> / New Blog 
            </small>
        </div>
        <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{ route('blog') }}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-body">
            {{Form::open(['route'=>['blog.store'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'labelForm' ])}}

            <div class="personal_informations">
                <!-- <h3>{!!  __('backend.label') !!}</h3>
                    <br>
                    <br> -->
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">{!! __('Title [EN]') !!}<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="title" id="title" class="form-control" placeholder="Title [EN]" value="{{old('title')}}">
                        @if ($errors->has('title'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('title') }}</span>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">{!! __('Title [FR]') !!}<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="title_fr" id="title" class="form-control" placeholder="Title [FR]" value="{{old('title_fr')}}">
                        @if ($errors->has('title_fr'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('title_fr') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">{!! __('backend.short_description_en')!!}<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="short_description" id="short_description" placeholder="Short Description [EN]">{{old('short_description')}}</textarea>
                        @if ($errors->has('short_description'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('short_description') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">{!! __('backend.short_description_fr')!!}<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="short_description_fr" id="short_description" placeholder="Short Description [FR]">{{old('short_description_fr')}}</textarea>
                        @if ($errors->has('short_description_fr'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('short_description_fr') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Image<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <img id="blah" style="width:100px !important; height:100px !important;" src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="your image"/>
                        <input type="file" name="image" id="image" class="form-control" style="margin-left: -13px;" accept="image/png, image/jpg, image/jpeg">
                        @if ($errors->has('image'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('image') }}</span>
                        </span>
                        <br>
                        @endif
                        <small>
                            <i class="material-icons">&#xe8fd;</i>
                            Choose image .png, .jpg, .jpeg files only.
                        </small>
                        <br>
                        <small>
                            <i class="material-icons">&#xe8fd;</i>
                            Recommended size 1920(Width) x 1280(Height).
                        </small>
                        <br>  
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-cms">Long Description [EN]<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="long_description" name="long_description" autofocus>{{old('long_description')}}</textarea>
                        @if ($errors->has('long_description'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('long_description') }}</span>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-cms">Long Description [FR]<span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="long_description_fr" name="long_description_fr" autofocus>{{old('long_description')}}</textarea>
                        @if ($errors->has('long_description_fr'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('long_description_fr') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

            </div>

            <div class="form-group row m-t-md">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
                    <a href="{{ route('blog')}}" class="btn btn-default m-t">
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
<script src="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.js") }}"></script>
<script src="{{ asset("assets/dashboard/js/summernote/dist/summernote.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>




<script>
    image.onchange = evt => {
        const [file] = image.files
        fileName = document.querySelector('#image').value;
        extension = fileName.split('.').pop();
        document.querySelector('.output').textContent = extension;
        if (file) {
            blah.src = URL.createObjectURL(file)
        }
    }
    $(function() {
        $('.icp-auto').iconpicker({
            placement: '{{ (@Helper::currentLanguage()->direction=="rtl")?"topLeft":"topRight" }}'
        });
    });

    function sendFile(file, editor, welEditable, lang) {
        data = new FormData();
        data.append("file", file);
        data.append("_token", "{{csrf_token()}}");

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

    CKEDITOR.replace('long_description', {
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

    CKEDITOR.replace('long_description_fr', {
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
    CKEDITOR.config.height = '400px';
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#submitDetail').on('click', function() {
            var myForm = $("form#labelForm");
            if (myForm) {
                $(this).prop('disabled', true);
                $(myForm).submit();
            }
        });
    });
</script>
@endpush