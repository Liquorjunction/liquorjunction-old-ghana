@extends('dashboard.layouts.master')
@section('title', __('category'))
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
                        &#xe02e;</i> {{ __('Category') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('category.index') }}">{{ __('category') }}</a> / 
                    <span>{{ __('Edit category') }}</span>

                </small>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['category.update',$category->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'categoryForm','name'=>'form1' ])}}
                {{ csrf_field() }}
                <div class="personal_informations">

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Category Name<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::text('name', old('name',urldecode($category->name)), ['class' => 'form-control', 'id' => 'name' ,'maxlength'=>"30" , 'onkeypress'=>"return blockSpecialChar(event)"]) !!}
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('name'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('name') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label"> Description <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::text('description', old('description',urldecode($category->description)), ['class' => 'form-control', 'id' => 'description' ,'maxlength'=>"100" , 'onkeypress'=>"return blockSpecialChar(event)"]) !!}
                            <span class="help-block">
                                @if(!empty(@$errors) && @$errors->has('description'))
                                    <span  style="color: red;" class='validate'>{{ $errors->first('description') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label"> Activity Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-control" name="activity_id" value="Select Activity">
                      <option>Select Activity</option>
                       @if($activitylistdata)

                            @foreach($activitylistdata as $item)
                               <?php $selected = ''; ?>
                               @if($item->id == $category->activity_id)
                                <?php $selected = 'selected'; ?>
                               @endif
                                <option value="{{$item->id}}" <?php echo $selected; ?> >{{$item->title}}</option>
                            @endforeach
                        @endif
                        
                    </select>
                        </div>
                    </div>

                    

                     <div class="form-group row">
                        <label for="photo_file" class="col-sm-2 form-control-label"> Image </label>
                        <div class="col-sm-10">
                            @if ($category->image != '')
                                <div class="row">
                                    <div class="col-sm-12 images">
                                        <div id="image" class="col-sm-8 box p-a-xs">
                                            <a target="_blank" href="{{ $image_url . $category->image }}"><img
                                                    src="{{ $image_url . $category->image }}" class="img-responsive1" height="100" width="100">
                                            </a>
                                            <br>
                                            <div class="delete m-t-xs">
                                                <a onclick="document.getElementById('image').style.display='none';document.getElementById('photo_delete').value='1';document.getElementById('undo').style.display='block';"
                                                    class="btn btn-sm btn-default">{!! __('backend.delete') !!}</a>
                                                {{ $category->image }}
                                            </div>
                                        </div>
                                        <div id="undo" class="col-sm-4 p-a-xs" style="display: none">
                                            <a
                                                onclick="document.getElementById('image').style.display='block';document.getElementById('photo_delete').value='0';document.getElementById('undo').style.display='none';">
                                                <i class="material-icons">&#xe166;</i> {!! __('backend.undoDelete') !!}
                                            </a>
                                        </div>
    
                                        {!! Form::hidden('photo_delete', '0', ['id' => 'photo_delete']) !!}
                                    </div>
                                </div>
                            @endif
    
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
                            <button type="submit" class="btn btn-primary m-t" id="submitDetail" onclick="ValidateEmail(document.form1.email)"><i class="material-icons">&#xe31b;</i> {!! __('backend.update') !!}</button>
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
