@extends('dashboard.layouts.master')
@section('title', 'Edit User')
@section('content')

<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" />
<style type="text/css">
    .select2-container {
        width: 100% !important;
    }
</style>

<div class="padding edit-package add-schoo">
    <div class="box">
        <div class="box-header dker">
            <h3><i class="material-icons">&#xe02e;</i> Edit User </h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / Edit User
                <!-- <a href="{{ route('users') }}">Edit User</a> -->
            </small>
        </div>
        <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('users')}}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="box nav-active-border b-info">

            <div class="tab-content clear b-t">
                <div class="tab-pane active" id="tab_details">
                    <div class="box-body">

                        {{Form::open(['route'=>['userUpdate',$Users->id],'method'=>'POST', 'files' => true])}}

                        <input type="hidden" name="user_id" value="{{ isset($Users->id) ? $Users->id : '' }}">
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 form-control-label">Name
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('name',isset($Users->name)? $Users->name : '', array('placeholder' => 'Name', 'maxlength' =>"30" ,'class' => 'form-control','id'=>'fullname')) !!}
                                @if ($errors->has('name'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('name') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 form-control-label">Email
                            </label>
                            <div class="col-sm-10">
                                {!! Form::email('email',isset($Users->email)? $Users->email : '', array('placeholder' => 'Email','class' => 'form-control','id'=>'email')) !!}
                                @if ($errors->has('email'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('email') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row" style="display: none;">

                            <label for="password" class="col-sm-2 form-control-label">Password
                            </label>
                            <div class="col-sm-10">
                                {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control','id'=>'password')) !!}
                                <br>
                                <input type="checkbox" onclick="myFunction()"> Show Password
                            </div>

                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-sm-2 form-control-label">Country code
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('country_code',isset($Users->country_code)? $Users->country_code : '', array('placeholder' => 'Country Code', 'maxlength' =>"30" ,'class' => 'form-control','id'=>'country_code')) !!}
                                @if ($errors->has('country_code'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('country_code') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">Mobile Number
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('phone',isset($Users->phone)? $Users->phone : '', array('placeholder' => 'Mobile Number','class' => 'form-control', 'onkeypress="return IsNumeric(event);" ondrop="return false;" maxlength="12" onpaste="return false;"','id'=>'phone')) !!}
                                @if ($errors->has('phone'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('phone') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">About Me
                            </label>
                            <div class="col-sm-10">
                                {!! Form::textarea('about_me',isset($Users->about_me)? $Users->about_me : '', array('placeholder' => 'Address','class' => 'form-control','rows' => 2, 'cols' => 40,'id'=>'address')) !!}
                                @if ($errors->has('about_me'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('about_me') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                       
                      
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Image</label>
                            <div class="col-sm-10">
                                <input type="file" name="profile" id="image_name" class="form-control" style="border: none; margin-left: -15px;" accept="image/*">
                                @if ($errors->has('profile'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('profile') }}</span>
                                </span>
                                @endif
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .png, .jpg, .jpeg
                                </small>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2"></label>
                                <div class="col-sm-10">
                                    @if(isset($Users->profile) && $Users->profile != "")
                                    <!-- {{$Users->profile}} -->
                                    <img id="image" src="{{ asset('uploads/website_users/').'/'.$Users->profile }}" width="100px" height="100px" />
                                    @else
                                    <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                    @endif                                       
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="person" class="col-sm-2 form-control-label">Instructor (He/She)
                            </label>

                            <div class="col-sm-10">
                                <select name="category_dance_instructor" id="vehicle_type" class="form-control">
                                    <option value="">Select Dance Instructor</option>
                                    <option {{((isset($Users->category_dance_instructor) && $Users->category_dance_instructor == '1') ? 'selected' : '' ) }} value="1">Male</option>
                                    <option {{((isset($Users->category_dance_instructor) && $Users->category_dance_instructor == '2') ? 'selected' : '' ) }} value="2">Female</option>
                                </select>
                                @if ($errors->has('category_dance_instructor'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('category_dance_instructor') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">Dance Group Name
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('dance_group_name',isset($Users->dance_group_name)? $Users->dance_group_name : '', array('placeholder' => 'Dance Group Name','class' => 'form-control')) !!}
                                @if ($errors->has('dance_group_name'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('dance_group_name') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">Instructor Facebook Link
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('instructor_facebook_link',isset($Users->instructor_facebook_link)? $Users->instructor_facebook_link : '', array('placeholder' => 'Instructor Facebook Link','class' => 'form-control')) !!}
                                @if ($errors->has('instructor_facebook_link'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('instructor_facebook_link') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">Instructor Instagram Link
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('instructor_instagram_link',isset($Users->instructor_instagram_link)? $Users->instructor_instagram_link : '', array('placeholder' => 'Instructor Instagram Link','class' => 'form-control')) !!}
                                @if ($errors->has('instructor_instagram_link'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('instructor_instagram_link') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">Instructor Tiktok Link
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('instructor_tiktok_link',isset($Users->instructor_tiktok_link)? $Users->instructor_tiktok_link : '', array('placeholder' => 'Instructor Tiktok Link','class' => 'form-control')) !!}
                                @if ($errors->has('instructor_tiktok_link'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('instructor_tiktok_link') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">Instructor Web Link
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('instructor_web_link',isset($Users->instructor_web_link)? $Users->instructor_web_link : '', array('placeholder' => 'Instructor Web Link','class' => 'form-control')) !!}
                                @if ($errors->has('instructor_web_link'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('instructor_web_link') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">Instructor Location
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('instructor_location',isset($Users->instructor_location)? $Users->instructor_location : '', array('placeholder' => 'Instructor Location','class' => 'form-control')) !!}
                                @if ($errors->has('instructor_location'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('instructor_location') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Portfolio Image</label>
                            <div class="col-sm-10">
                                <input type="file" name="instructor_portfolio_image" id="image_name" class="form-control" style="border: none; margin-left: -15px;" accept="image/*">
                                @if ($errors->has('instructor_portfolio_image'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('instructor_portfolio_image') }}</span>
                                </span>
                                @endif
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .png, .jpg, .jpeg
                                </small>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2"></label>
                                <div class="col-sm-10">
                                    @if(isset($Users->instructor_portfolio_image) && $Users->instructor_portfolio_image != "")
                                    <!-- {{$Users->instructor_portfolio_image}} -->
                                    <img id="image" src="{{ asset('uploads/website_users/').'/'.$Users->instructor_portfolio_image }}" width="100px" height="100px" />
                                    @else
                                    <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                    @endif                                       
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Portfolio Video</label>
                            <div class="col-sm-10">
                                <input type="file" name="instructor_portfolio_video" id="image_name" class="form-control" style="border: none; margin-left: -15px;" accept="video/*">
                                @if ($errors->has('instructor_portfolio_video'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('instructor_portfolio_video') }}</span>
                                </span>
                                @endif
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    .mp4
                                </small>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2"></label>
                                <div class="col-sm-10">
                                    @if(isset($Users->instructor_portfolio_video) && $Users->instructor_portfolio_video != "")
                                    <!-- {{$Users->instructor_portfolio_video}} -->
                                    <video width="200" controls  class="video-link" id="video-link">
                                        <source src="{{ asset('uploads/website_users/videos/').'/'.$Users->instructor_portfolio_video }}" class="video_here">
                                          Your browser does not support HTML5 video.
                                      </video>
                                    @else
                                    <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                    @endif                                       
                                </div>
                            </div>
                        </div>

                        

                        <div class="form-group row m-t-md">
                            <div class="offset-sm-2 col-sm-10">
                                @if(isset($Users) && !empty($Users))
                                <button type="submit" class="btn btn-primary m-t" style="margin-left: -10px;"><i class="material-icons">
                                        &#xe31b;</i> {!! __('backend.update') !!}</button>
                                @else
                                <button type="submit" class="btn btn-primary m-t" style="margin-left: -10px;"><i class="material-icons">
                                        &#xe31b;</i> {!! __('backend.add') !!}</button>
                                @endif
                                <a href="{{route('users')}}" class="btn btn-default m-t"><i class="material-icons">
                                        &#xe5cd;</i> {!! __('backend.cancel') !!}</a>
                            </div>
                        </div>

                        {{Form::close()}}
                    </div>
                </div>

            </div>

        </div>
    </div>
    @endsection
    @push("after-scripts")

    <script src="{{ asset('assets/dashboard/js/jquery.validate.min.js') }} "></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
async defer></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize" async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $('.select-program').select2();

        function mutliselect2() {
            $('.select-program').select2();

        }
        $(document).ready(function() {
            $(".box-body .alter_video").hide();
        });
        // $(document).on('change', '.add_image_video_type', function(e){
        //     $this = $(this);
        //     e.preventDefault();
        //     $this.parents(".box-body").find('.alter_image').addClass('d-none');
        //     $this.parents(".student-table").find('.alter_image_'+$this.val()).removeClass('d-none');
        //     $this.parents(".student-table").find('.remove-img').toggleClass('d-none');
        // });
        
        $(document).on('change', '.add_image_video_type', function(e){
            $this = $(this);
            e.preventDefault();
            var option = $(this).find('option:selected');
            var value = option.val();
            if(value == 1)
            {
                $this.parents(".box-body").find('.alter_video').hide();
                $this.parents(".box-body").find('.alter_image').show();
            }
            else
            {
                $this.parents(".box-body").find('.alter_image').hide();
                $this.parents(".box-body").find('.alter_video').show();
            }
        });
        /*  function initialize() {
        var inputs = document.getElementsByClassName('address');
        console.log(inputs);
var options = {
  types: ['(cities)'],
  componentRestrictions: {country: 'fr'}
};
var autocompletes = [];
for (var i = 0; i < inputs.length; i++) {
  var autocomplete = new google.maps.places.Autocomplete(inputs[i], options);
  autocomplete.inputId = inputs[i].id;
  autocomplete.addListener('place_changed', fillIn);
  autocompletes.push(autocomplete);
}

function fillIn() {
  console.log(this.inputId);
  var place = this.getPlace();
  console.log(place. address_components[0].long_name);
}*/
        /*
         var autocomplete = new google.maps.places.Autocomplete(inputs[i], {});
                    autocomplete.inputId = inputs[i].id;
                    google.maps.event.addListener(autocomplete, 'place_changed', function() {
                    var place = this.getPlace();
                    var latitude = place.geometry.location.lat();
                    var longitude = place.geometry.location.lng();
                    console.log(document.querySelector('.address').closest('.address_block'));
                    document.getElementById('latitude'+i).value= latitude;
                    document.getElementById('longitude'+i).value= longitude;
                       
                    });*/

        function initialize() {
            var inputs = document.getElementsByClassName('address');
            var autocompletes = [];
            for (var i = 0; i < inputs.length; i++) {
                var autocomplete = new google.maps.places.Autocomplete(inputs[i], {});
                autocomplete.inputId = inputs[i].id;
                autocomplete.addListener('place_changed', fillIn);
                autocompletes.push(autocomplete);
            }
        }

        function fillIn() {

            var place = this.getPlace();
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();
            var group = document.querySelector('#' + this.inputId).closest('.address_block');
            group.querySelector('.latitude').value = latitude;
            group.querySelector('.longitude').value = longitude;
            /*console.log(place. address_components[0].long_name);*/
        }
        /* $(document).on('blur','.school_code',function(e) {
             e.preventDefault();
             _this = $(this);
             var locationId = _this.attr('data-id');
             var school_code = _this.val();
              

            
         });*/



        var specialKeys = new Array();
        specialKeys.push(8);

        function IsNumeric(e) {

            var keyCode = e.which ? e.which : e.keyCode
            var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
            //document.getElementById("error").style.display = ret ? "none" : "inline";  
            return ret;
        }

        function myFunction() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
    @endpush