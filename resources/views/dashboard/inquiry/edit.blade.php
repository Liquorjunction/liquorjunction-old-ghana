@extends('dashboard.layouts.master')
<?php
$title_var = "title_" . @Helper::currentLanguage()->code;
$title_var2 = "title_" . env('DEFAULT_LANGUAGE');
?>
@section('title', __("Inquiry"))
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
                        &#xe02e;</i> Edit Inquiry
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('inquiry') }}">Inquiry</a>

                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('inquiry') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['inquiry.update',$inquiry->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'faqForm' ])}}

                <div class="personal_informations">
                {{-- <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Type</label>
                        <div class="col-sm-10">
                           <select name="type" class="form-control" id="type">
                               <option  value="">Select Type</option>
                               <option {{ ((isset($Faq->type) && ( $Faq->type == 1 )) ? 'selected' : '' ) }} value="1">User</option>
                               <option {{ ((isset($Faq->type) && ( $Faq->type == 2 )) ? 'selected' : '' ) }} value="2">Driver</option>
                           </select>
                        </div>
                    </div> --}}
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ isset($inquiry->name)?$inquiry->name:old('name') }}">
                            @if ($errors->has('name'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('name') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Email</label>
                        <div class="col-sm-10">
                            <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{ isset($inquiry->email)?$inquiry->email:old('email') }}">
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('email') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Phone</label>
                        <div class="col-sm-10">
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone" value="{{ isset($inquiry->phone)?$inquiry->phone:old('phone') }}">
                            @if ($errors->has('name'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('phone') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Message</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="message" name="message" autofocus >{{ isset($inquiry->message)?$inquiry->message:old('message') }}</textarea>
                            @if ($errors->has('message'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('message') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
                            <a href="{{ route('inquiry')}}" class="btn btn-default m-t">
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
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

 

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
@endpush
