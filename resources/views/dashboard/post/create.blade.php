@extends('dashboard.layouts.master')
@section('title', 'Create User')
@section('content')

   <link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" />
   <style type="text/css">
   .select2-container 
   {
    width: 100% !important;
   }
   </style>

    <div class="padding edit-package add-schoo">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">&#xe02e;</i> Create User </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('users') }}">User</a> 
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
                        
                         {{Form::open(['route'=>['userstore'],'method'=>'POST', 'files' => true])}}
                    
                          
                        <div class="form-group row">
                            <label for="name"
                                   class="col-sm-2 form-control-label">Full Name
                            </label>
                            <div class="col-sm-10">
                                {!! Form::text('fullname', '',array('placeholder' => 'FullName','class' => 'form-control','id'=>'fullname')) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name"
                                   class="col-sm-2 form-control-label">Email
                            </label>
                            <div class="col-sm-10">
                                {!! Form::email('email', '',array('placeholder' => 'Email','class' => 'form-control','id'=>'email')) !!}
                            </div>
                        </div>

                       
                        
                        <div class="form-group row m-t-md">
                            <div class="offset-sm-2 col-sm-10">
                                
                               
                                    <button type="submit" class="btn btn-primary m-t"><i class="material-icons">
                                        &#xe31b;</i> {!! __('backend.add') !!}</button>
                               
                                <a href="{{route('users')}}"
                                   class="btn btn-default m-t"><i class="material-icons">
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
async defer></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
    $('.select-program').select2();
     function mutliselect2()
    {
        $('.select-program').select2();
        
    }
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
            var group = document.querySelector('#'+this.inputId).closest('.address_block');
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
        function IsNumeric(e) 
        {  
           
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