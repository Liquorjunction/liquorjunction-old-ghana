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
                                {!! Form::text('phone',isset($Users->phone)? $Users->phone : '', array('placeholder' => 'Mobile Number','class' => 'form-control', 'onkeypress="return IsNumeric(event);" ondrop="return false;" maxlength="12"','id'=>'phone')) !!}
                                @if ($errors->has('phone'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('phone') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- <div class="form-group row">
                            <label for="email" class="col-sm-2 form-control-label">Address
                            </label>
                            <div class="col-sm-10">
                                {!! Form::textarea('about_me',isset($Users->about_me)? $Users->about_me : '', array('placeholder' => 'Address','class' => 'form-control','rows' => 2, 'cols' => 40,'id'=>'address')) !!}
                            </div>
                        </div> --}}
                       
                      
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