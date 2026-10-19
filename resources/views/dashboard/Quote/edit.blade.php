@extends('dashboard.layouts.master')
@section('title', __('backend.quote'))
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
                        &#xe02e;</i> Edit Assign {{ __('backend.quote') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / {{ __('backend.quote') }}
                    <!-- <a href="{{ route('label') }}">{{ __('backend.label') }}</a> -->

                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('quote') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['quote.update',$quoteData->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'labelForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>{!!  __('backend.label') !!}</h3>
                    <br>
                    <br> -->
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.assign_list') !!}</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="assign_user_id" id="assign_user_id" data-live-search="true">
                                @foreach($assignData as $assign)
                                <option value="{{@$assign->id}}">{{@$assign->first_name}} {{@$assign->last_name}} ({{@$assign->post_code}})</option>
                                @endforeach
                            </select>
                            @if ($errors->has('label_name'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('label_name') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.create_user') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="label_name" id="label_key" class="form-control" placeholder="Label Key" value="{{@$quoteData->create_user_first_name}} {{@$quoteData->create__user_last_name}}" disabled>
                            @if ($errors->has('label_name'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('label_name') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.category_name') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="category_name" id="category_name" class="form-control" placeholder="Label Key" value="{{@$quoteData->category_name}}" disabled>
                           
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.post_code') !!}</label>
                        <div class="col-sm-10">
                            <input type="text" name="post_code" id="post_code" class="form-control" placeholder="Label Key" value="{{@$quoteData->post_code}}" disabled>
                            
                        </div>
                    </div>


                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
                            <a href="{{ route('quote')}}" class="btn btn-default m-t">
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
