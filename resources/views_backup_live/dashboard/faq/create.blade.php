@extends('dashboard.layouts.master')
<?php
$title_var = "title_" . @Helper::currentLanguage()->code;
$title_var2 = "title_" . env('DEFAULT_LANGUAGE');
?>
@section('title', __("FAQ's"))
@push("after-styles")
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

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
                        &#xe02e;</i> {{ __('backend.topicNew') }} FAQ's
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('faq') }}">FAQ's</a> / New FAQ
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('faq') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['faq.store'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'faqForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>faq</h3>
                    <br>
                    <br> -->
                    {{-- <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Type</label>
                        <div class="col-sm-10">
                           <select name="type" class="form-control" id="type">
                               <option value="">Select Type</option>
                               <option value="1">User</option>
                               <option value="2">Driver</option>
                           </select>
                        </div>
                    </div> --}}
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Question [EN]<span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="question" id="question" class="form-control" placeholder="Question [EN]" value="{{old('question_name ')}}">
                            @if ($errors->has('question'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('question') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Question [FR]<span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="question_fr" id="question" class="form-control" placeholder="Question [FR]" value="{{old('question_name_fr')}}">
                            @if ($errors->has('question_fr'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('question_fr') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Answer [EN]<span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="faq_answer" name="answer" autofocus placeholder="Answer [EN]" >{{old('answer')}}</textarea>
                            @if ($errors->has('answer'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('answer') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Answer [FR]<span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="faq_answer_fr" name="answer_fr" autofocus placeholder="Answer [FR]">{{old('answer_fr')}}</textarea>
                            @if ($errors->has('answer_fr'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('answer_fr') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>

                </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
                            <a href="{{ route('faq')}}" class="btn btn-default m-t">
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
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

 

    <script>

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
    <script type="text/javascript">
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
            
        CKEDITOR.replace('faq_answer___fgfg', {
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
$(document).ready(function() {
    $('#submitDetail').on('click', function() {
        var myForm = $("form#faqForm");
        if (myForm) {
            $(this).prop('disabled', true);
            $(myForm).submit();
        }
    });
});
</script>
    
@endpush
