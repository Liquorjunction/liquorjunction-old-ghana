@extends('dashboard.layouts.master')
@section('title', __('backend.cms'))

@push('after-styles')
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
@endpush

@section('content')
<div class="padding edit-package">
    <div class="box">
        <div class="box-header dker">
            <h3>
                <i class="material-icons">&#xe02e;</i> Edit {{ __('backend.cms') }}
            </h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <a href="{{ route('cms') }}">{{ __('backend.cms') }}</a> / Edit CMS
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
            {{ Form::open(['route' => ['cms.update', $cms->id], 'method' => 'POST', 'files' => true]) }}
            <div class="personal_informations">

                {{-- Image Upload --}}
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Image<span class="valid_field"></span></label>
                    <div class="col-sm-10">
                        <input
                            type="file"
                            name="photo"
                            id="bannerimage"
                            class="form-control"
                            accept="image/png, image/jpeg"
                            style="margin-left: -10px;"
                        >
                        <div class="help-block with-errors text-danger"></div>
                        <small><i class="material-icons">&#xe8fd;</i> Choose image .png, .jpg, .jpeg files only.</small><br>
                        <small><i class="material-icons">&#xe8fd;</i> Recommended size 1440(Width) x 250(Height).</small>
                    </div>
                </div>

                {{-- Image Preview --}}
                <div class="form-group row">
                    <label class="col-sm-2"></label>
                    <div class="col-sm-10">
                        @php
                            $imagePath = $cms->photo && file_exists(public_path('uploads/cms/' . $cms->photo))
                                ? asset('uploads/cms/' . $cms->photo)
                                : asset('uploads/contacts/noimage.png');
                        @endphp
                        <img id="previewImage" src="{{ $imagePath }}" width="100" height="100" alt="Preview Image">
                    </div>
                </div>

                {{-- Page Name EN --}}
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">{!! __('backend.newpage') !!} [EN] <span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <input
                            type="text"
                            name="page_name"
                            id="page_name"
                            class="form-control"
                            placeholder="Name"
                            value="{{ old('page_name', $cms->page_name) }}"
                        >
                        @error('page_name')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Page Name FR --}}
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">{!! __('backend.newpage') !!} [FR] <span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <input
                            type="text"
                            name="page_name_fr"
                            id="page_name_fr"
                            class="form-control"
                            placeholder="Name"
                            value="{{ old('page_name_fr', $cms->page_name_fr) }}"
                        >
                        @error('page_name_fr')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Page Content EN --}}
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">{!! __('backend.pagecontent') !!} [EN] <span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <textarea
                            class="form-control"
                            id="page_content"
                            name="page_content"
                            autofocus
                        >{{ old('page_content', urldecode($cms->page_content)) }}</textarea>
                        @error('page_content')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Page Content FR --}}
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">{!! __('backend.pagecontent') !!} [FR] <span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <textarea
                            class="form-control"
                            id="page_content_fr"
                            name="page_content_fr"
                            autofocus
                        >{{ old('page_content_fr', urldecode($cms->page_content_fr)) }}</textarea>
                        @error('page_content_fr')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Mobile Page Content EN --}}
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Mobile {!! __('backend.pagecontent') !!} [EN] <span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <textarea
                            class="form-control"
                            id="mobile_page_content"
                            name="mobile_page_content"
                            autofocus
                        >{{ old('mobile_page_content', urldecode($cms->mobile_page_content)) }}</textarea>
                        @error('mobile_page_content')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Mobile Page Content FR --}}
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Mobile {!! __('backend.pagecontent') !!} [FR] <span class="valid_field">*</span></label>
                    <div class="col-sm-10">
                        <textarea
                            class="form-control"
                            id="mobile_page_content_fr"
                            name="mobile_page_content_fr"
                            autofocus
                        >{{ old('mobile_page_content_fr', urldecode($cms->mobile_page_content_fr)) }}</textarea>
                        @error('mobile_page_content_fr')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="form-group row mt-3">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons">&#xe31b;</i> Update
                    </button>
                    <a href="{{ route('cms') }}" class="btn btn-default">
                        <i class="material-icons">&#xe5cd;</i> {!! __('backend.cancel') !!}
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
    {{-- <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script> --}}

    <script>
        $(function() {
            // Initialize icon picker
            $('.icp-auto').iconpicker({
                placement: '{{ (app()->getLocale() === "ar" || app()->getLocale() === "he") ? "topLeft" : "topRight" }}'
            });

            // Image validation and preview
            $('#bannerimage').on('change', function(evt) {
                const file = this.files[0];
                const helpBlock = $(this).siblings('.help-block.with-errors');
                helpBlock.text('');

                if (!file) return;

                const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
                const maxSize = 2 * 1024 * 1024; // 2 MB

                if (!allowedExtensions.exec(file.name)) {
                    this.value = '';
                    helpBlock.text('Please upload only .png, .jpg, .jpeg files.');
                    $('#previewImage').attr('src', '{{ asset("uploads/contacts/noimage.png") }}');
                    return;
                }

                if (file.size > maxSize) {
                    this.value = '';
                    helpBlock.text('File upload size must not exceed 2 MB.');
                    $('#previewImage').attr('src', '{{ asset("uploads/contacts/noimage.png") }}');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            });

            // Initialize CKEditor for textareas
            ['page_content', 'page_content_fr', 'mobile_page_content', 'mobile_page_content_fr'].forEach(function(id) {
                CKEDITOR.replace(id, {
                    height: 400,
                    on: {
                        focus: function() {
                            console.log(id + ' focused');
                        },
                        blur: function() {
                            console.log(id + ' lost focus');
                        }
                    }
                });
            });

            $('form').on('submit', function() {
                console.log('Form submit triggered');
                for (const instanceName in CKEDITOR.instances) {
                    if (CKEDITOR.instances.hasOwnProperty(instanceName)) {
                        CKEDITOR.instances[instanceName].updateElement();
                        console.log('Updated CKEditor:', instanceName);
                    }
                }
            });
        });
    </script>
@endpush
