@extends('dashboard.layouts.master')
@section('title', __('Add Category'))
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
                        &#xe02e;</i> {{ __('Add Category') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('category.index') }}">{{ __('Category') }}</a> / 
                    <span>{{ __('Add Category') }}</span>

                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['category.store'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'userForm' ])}}
                {{ csrf_field() }}
                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Category Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Category Name" value="{{ old('name') }}" maxlength="30">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Description</label>
                        <div class="col-sm-10">
                        <input type="test" name="description" id="description" class="form-control"
                                placeholder="Description" maxlength="100"  value="{{ old('description') }}">
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('description'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('description') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Activity Name</label>
                        <div class="col-sm-10">
                     <select class="form-control" id="activity_id" name="activity_id">
                      <option value="">Select Activity</option>
                        @if($activitylistdata)

                            @foreach($activitylistdata as $item)                               
                                <option value="{{$item->id}}">{{$item->title}}</option>
                            @endforeach

                        @endif
                        
                    </select>
                     <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('activity_id'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('activity_id') }}</span>
                                @endif
                            </span>
                        </div>
                       

                    </div>

                    <div class="form-group row">
                        <label for="photo_file" class="col-sm-2 form-control-label">Image</label>
                        <div class="col-sm-10">
                            {!! Form::file('image', ['class' => 'form-control', 'id' => 'photo', 'accept' => 'image/*']) !!}
                            <small>
                                <i class="material-icons">&#xe8fd;</i>
                                {!! __('backend.imagesTypes') !!}
                            </small>
                            <br>
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('image'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('image') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail">&nbsp;&nbsp;{!! __('backend.add') !!}&nbsp;&nbsp;</button>
                            <a href="{{ route('category.index')}}" class="btn btn-default m-t">
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
    <script src="{{asset('assets/dashboard/js/inputFilter.js')}}"></script>


    <script>
        $(function () {
            $('.icp-auto').iconpicker({placement: '{{ (@Helper::currentLanguage()->direction=="rtl")?"topLeft":"topRight" }}'});
        });

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


        $(document).ready(function() {
            $("#mobile_number").inputFilter(function(value) {
              return /^\d*$/.test(value);    // Allow digits only, using a RegExp
            });
        });

    </script>
@endpush
