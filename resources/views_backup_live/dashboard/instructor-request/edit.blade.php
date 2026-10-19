@extends('dashboard.layouts.master')
@section('title', 'Edit Instructor Request')
@section('content')

<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet" />
<style type="text/css">
    .select2-container {
        width: 100% !important;
    }
</style>

<div class="padding edit-package add-schoo">
    <div class="box">
        <div class="box-header dker">
            <h3><i class="material-icons">&#xe02e;</i> Edit Instructor Request </h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <a href="{{ route('instructor-request') }}">Instructor Request Management</a>
            </small>
        </div>
        <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('instructor-request')}}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="box nav-active-border b-info">

            <div class="tab-content clear b-t">
                <div class="tab-pane active" id="tab_details">
                    <div class="box-body">

                        {{Form::open(['route'=>['instructor-request.update',$instructor_request->id],'method'=>'POST', 'files' => true])}}
                        
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 form-control-label">User Type
                            </label>
                            <div class="col-sm-8">
                                @if(isset($instructor_request->user_type) && $instructor_request->user_type == 2)
                                    {!! Form::text('user_type','Normal User',array('placeholder' => 'User Type','class' => 'form-control','id'=>'vehicle_name','disabled')) !!}
                                @elseif(isset($instructor_request->user_type) && $instructor_request->user_type == 3)
                                    {!! Form::text('user_type','Instructor User',array('placeholder' => 'User Type','class' => 'form-control','id'=>'vehicle_name','disabled')) !!}
                                @else
                                {!! Form::text('user_type','Admin',array('placeholder' => 'User Type','class' => 'form-control','id'=>'vehicle_name','disabled')) !!}
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 form-control-label">Name
                            </label>
                            <div class="col-sm-8">
                                {!! Form::text('name',isset($instructor_request->name) ? $instructor_request->name : '',array('placeholder' => 'Name','class' => 'form-control','id'=>'vehicle_name','disabled')) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-4 form-control-label">Email
                            </label>
                            <div class="col-sm-8">
                                {!! Form::text('email',isset($instructor_request->email) ? $instructor_request->email : '',array('placeholder' => 'Email','class' => 'form-control','id'=>'vehicle_name','disabled')) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 form-control-label">Country Code
                            </label>
                            <div class="col-sm-8">
                                {!! Form::text('country_code',isset($instructor_request->country_code) ? $instructor_request->country_code : '',array('placeholder' => 'Country Code','class' => 'form-control','id'=>'vehicle_name','disabled')) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 form-control-label">Mobile Number
                            </label>
                            <div class="col-sm-8">
                                {!! Form::text('phone',isset($instructor_request->phone) ? $instructor_request->phone : '',array('placeholder' => 'Mobile No.','class' => 'form-control','id'=>'vehicle_name','disabled')) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 form-control-label">Image</label>
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-8">
                                @if(isset($instructor_request->profile) && $instructor_request->profile != "")
                                <img id="image" src="{{ asset('uploads/website_users/').'/'.$instructor_request->profile }}" width="100px" height="100px" />
                                @else
                                <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                @endif                                       
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 form-control-label">Request Status
                            </label>

                            <div class="col-sm-8">
                                <select name="is_verify_instructor" id="vehicle_type" class="form-control">
                                    <option value="">Select Request Status</option>
                                    <option {{((isset($instructor_request->is_verify_instructor) && $instructor_request->is_verify_instructor == '1') ? 'selected' : '' ) }} value="1">Pending</option>
                                    <option {{((isset($instructor_request->is_verify_instructor) && $instructor_request->is_verify_instructor == '2') ? 'selected' : '' ) }} value="2">Approved</option>
                                    <option {{((isset($instructor_request->is_verify_instructor) && $instructor_request->is_verify_instructor == '3') ? 'selected' : '' ) }} value="3">Rejected</option>
                                </select>
                                @if ($errors->has('is_verify_instructor'))
                                <span class="help-block">
                                    <span  style="color: red;" class='validate'>{{ $errors->first('is_verify_instructor') }}</span>
                                </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row m-t-md">
                            <div class="offset-sm-2 col-sm-10">


                                <button type="submit" class="btn btn-primary m-t"><i class="material-icons">
                                        &#xe31b;</i> {!! __('backend.update') !!}</button>

                                <a href="{{route('instructor-request')}}" class="btn btn-default m-t"><i class="material-icons">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/js/bootstrap-switch.min.js"></script>

    <script type="text/javascript">
        $('.select-program').select2();

        $("[name='ride_allowed']").bootstrapSwitch();

        function mutliselect2() {
            $('.select-program').select2();

        }

        var validNumber = new RegExp(/^\d*\.?\d*$/);
        var lastValid = '';

        function onlyNumber(elem) {
            if (validNumber.test(elem.value)) {
                lastValid = elem.value;
            } else {
                elem.value = lastValid;
            }
        }

        function minmax(value, min, max) {
            if (parseInt(value) < min || isNaN(parseInt(value)))
                return min;
            else if (parseInt(value) > max)
                return max;
            else return value;
        }

        function showHide () {
            var type = $("[name='vehicle_type']").val();
            if(type != '' && type != null){
                if(type == '1'){
                    $('.person').show();
                }else{
                    $('.person').hide();
                }
            }else{
                $('.person').hide();
            }
        }

        $(document).ready(function() {
            showHide();
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