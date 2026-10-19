@extends('dashboard.layouts.master')
@section('title', __('backend.cms'))
@push('after-styles')
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

    <link rel= "stylesheet"
        href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
@endpush
@section('content')
    <div class="padding edit-package website-crm-show">
        <div class="box">
            <div class="box-header dker">

                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.view') }} {{ __('backend.cms') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('cms') }}">{{ __('backend.cms') }}</a> / View CMS
                </small>
            </div>
            {{-- <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('cms') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div> --}}
            <div class="box-body">
                {{ Form::open(['route' => ['cms'], 'method' => 'POST', 'files' => true, 'enctype' => 'multipart/form-data']) }}

                <div class="personal_informations">
                    <!-- <h3>{!! __('backend.cms') !!}</h3>
                        <br> -->
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Image</label>
                        <div class="col-sm-10">
                            @if (isset($cms->photo) && $cms->photo != '')
                                <a href="{{ asset('uploads/cms/' . $cms->photo) }}" target="_blank">
                                    <img id="image" src="{{ asset('uploads/cms/' . $cms->photo) }}"
                                        class="img-thumbnail" width="100px" height="100px" />
                                </a>
                            @else
                                <img src="{{ asset('uploads/contacts/noimage.png') }}" class="img-thumbnail" width="100px"
                                    height="100px">
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Page Name [EN]</label>
                        <div class="col-sm-10">
                            <input type="text" readonly name="page_name" id="page_name" class="form-control"
                                placeholder="Name" value="{{ $cms->page_name }}">

                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Page Name [FR]</label>
                        <div class="col-sm-10">
                            <input type="text" readonly name="page_name_fr" id="page_name_fr" class="form-control"
                                placeholder="Name" value="{{ $cms->page_name_fr }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Page Content [EN]</label>
                        <div class="col-sm-10">
                            <div class="d-flex flex-wrap mb-4">
                                <textarea readonly class="form-control" id="page_content" name="page_content" autofocus disabled>{!! urldecode($cms->page_content) !!}</textarea>

                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Page Content [FR]</label>
                        <div class="col-sm-10">
                            <div class="d-flex flex-wrap mb-4">
                                <textarea readonly class="form-control" id="page_content" name="page_content_fr" autofocus disabled>{!! urldecode($cms->page_content_fr) !!}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Mobile Page Content [EN]</label>
                        <div class="col-sm-10">
                            <div class="d-flex flex-wrap mb-4">
                                <textarea readonly class="form-control" id="page_content" name="mobile_page_content" autofocus disabled>{!! urldecode($cms->mobile_page_content) !!}</textarea>

                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Mobile Page Content [FR]</label>
                        <div class="col-sm-10">
                            <div class="d-flex flex-wrap mb-4">
                                <textarea readonly class="form-control" id="page_content" name="mobile_page_content_fr" autofocus disabled>{!! urldecode($cms->mobile_page_content_fr) !!}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-label"></label>
                    <div class="col-sm-10" style="right: 10px;">
                        <a href="{{ route('cms') }}" class="btn btn-default m-t">
                            <i class="material-icons">
                                &#xe5cd;</i>{!! __('backend.cancel') !!}
                        </a>
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>



    <script>
        $(function() {
            $('.icp-auto').iconpicker({
                placement: '{{ @Helper::currentLanguage()->direction == 'rtl' ? 'topLeft' : 'topRight' }}'
            });
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
        CKEDITOR.replace('page_content_fr', {
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
        CKEDITOR.replace('mobile_page_content', {
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
        CKEDITOR.replace('mobile_page_content_fr', {
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
@endpush
