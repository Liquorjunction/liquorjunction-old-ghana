@extends('frontend.layouts.app')
@section('title',Helper::language('edit_address'))
@section('content')
@include('sweetalert::alert')
<style>
    .text-red{
        color:red;
    }
</style>
<div class="bread-crumb-block">
    <div class="container">
        <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
        <li class="breadcrumb-item"><a href="{{route('my-account')}}">{{@Helper::language('my_account_label')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{@Helper::language('edit_address')}}</li>
        </ul>
    </div>
</div>

<section class="edit-address pt-20 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                @include('frontend.layouts.account-sidebar')
            </div>
            <?php
            // dd($UserAddressData);
            ?>
            <div class="col-lg-9 col-md-8">
                <h2>{{@Helper::language('edit_address')}}</h2>
                <div class="common-card">
                    <form class="row edit-address-form" id="edit_address_form">
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('name_label_web')}} <span class="text-red">*</span></label>
                                <input type="text" value="{{@$UserAddressData->name}}" placeholder="{{@Helper::language('enter_name_web')}}" name="name" id="name" class="">
                            </div>
                        </div>
                        <?php
                        // dd($countryData);
                        ?>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group has-validation">
                                <label for="">{{@Helper::language('phone_number')}}<span class="text-red">*</span></label>
                                <div class="input-group phone-number">
                                    <select class="numbers" name="phonecode" value="{{@$UserAddressData->phonecode}}">
                                        @foreach($countryData as $value)
                                        <option value="{{$value->phonecode}}" {{(@$UserAddressData->phonecode == $value->phonecode) ? 'selected' : ''}}>+ {{$value->phonecode.' ('.$value->shortname.')'}}</option>

                                        @endforeach
                                    </select>
                                    <input type="tel" value="{{@$UserAddressData->phone}}" placeholder="{{@Helper::language('enter_phone_number_place')}} " name="phone" maxlength="15" id="phone">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for=""> {{@Helper::language('country_label_web')}}<span class="text-red">*</span></label>
                                <select name="country_id" id="country_id" onchange="getSubCatList(this)" class="form-select" value="{{@$UserAddressData->country_id}}">
                                    <option value="">{{@Helper::language('choose_country_web')}}</option>
                                    <?php 
                                        $countryData = @$countryData->sortBy(['name', 'ASC']);
                                        $countryData = $countryData->values();
                                    ?>
                                    @foreach($countryData as $value)
                                   
                                    <option value="{{$value->id}}" {{(@$UserAddressData->country_id == $value->id) ? 'selected' : ''}}>{{$value->name}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please enter Country.
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('region_label_web')}}<span class="text-red">*</span></label>
                                <select value="{{old('region_id')}}" onchange="getAreaList(this)" name="region_id" id="region_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_region_web')}}</option>
                                    @foreach($region as $value)
                                    <option value="{{$value->id}}" {{(@$UserAddressData->region_id == $value->id) ? 'selected' : ''}}>{{$value->title}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('area_label_web')}}<span class="text-red">*</span></label>
                                <select value="{{old('area_id')}}" name="area_id" id="area_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_area_web')}}</option>
                                    @foreach($area as $value)
                                    <option value="{{$value->id}}" {{(@$UserAddressData->area_id == $value->id) ? 'selected' : ''}}>{{$value->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                         <!-- <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('state_label')}} <span class="text-red">*</span></label>
                                <input type="text" value="{{@$UserAddressData->state}}" placeholder=" {{@Helper::language('enter_state_place')}}" name="states" id="states" class="">
                                <div class="invalid-feedback">
                                    Please enter State.
                                </div>
                            </div>
                        </div> -->
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('zip_code_label')}}<span class="text-red">*</span></label>
                                <input type="text" name="zip_code"placeholder="{{@Helper::language('enter_code_place')}}" value="{{@$UserAddressData->zip_code}}" class="required" placeholder="Enter Zip Code {{@Helper::language('name_label_web')}}" required>
                                <div class="invalid-feedback">
                                    Please enter Zip Code.
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('city_label')}} <span class="text-red">*</span></label>
                                <input type="text" value="{{@$UserAddressData->city}}" name="city" id="city" placeholder="{{@Helper::language('enter_city_label')}}" class="">
                                <div class="invalid-feedback">
                                    Please enter City.
                                </div>
                            </div>
                        </div> 
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('street_address')}} <span class="text-red">*</span></label>
                                <textarea name="address" id="address" placeholder="{{@Helper::language('enter_street_address')}}" col="5" rows="2">{{@$UserAddressData->address}}</textarea>
                                <div class="invalid-feedback">
                                    Please enter Address.
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-12">
                                    <div class="form-group">
                                        <div class="check-group">
                                            <input class="form-check-input" type="checkbox" name="billing_address" {{ ($UserAddressData->billing_address == 1) ? 'checked' : "" }} value="1" id="flexCheckAddress">
                                            <label class="form-check-label" for="flexCheckAddress">Make this my default billing address</label>
                                        </div>                                        
                                    </div>
                                </div> -->
                        <div class="col-lg-6 col-md-12">

                            <input type="hidden" name="edit_address_id" id="edit_address_id" value="{{@$UserAddressData->id}}">
                            <button class="solid-button w-100" type="submit">{{@Helper::language('save_details_btn')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>



<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript">
    function isNumberKey(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

            return false;
        return true;

    }

    $('#phone').on("input", function() {
        this.value = this.value.replace(/[^0-9\.]/g, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });

    $('#name').on("input", function() {
        // console.log(this.value);
        this.value = this.value.replace(/[^a-zA-Z\s]/gi, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });
    var validation_name_required = "{{ \Helper::language('name_field_is_required'); }}";
    var validation_name_max_required = "{{ \Helper::language('the_name_may_not_be_greater_than_40_characters'); }}";
    var validation_country_required = "{{ \Helper::language('validation_country_required'); }}";
    var validation_region_required = "{{ \Helper::language('validation_region_required'); }}";
    var validation_area_required = "{{ \Helper::language('validation_area_required'); }}";

    var validation_address_required = "{{ \Helper::language('address_field_is_required'); }}";

    var validation_phone_required = "{{ \Helper::language('phone_number_field_is_required'); }}";
    var validation_phone_minlength = "{{ \Helper::language('phone_number_min_max'); }}";
    var validation_phone_maxlength = "{{ \Helper::language('phone_number_min_max'); }}";

    var test = $("#edit_address_form").validate({
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            name: {
                required:true,
                maxlength: 40,
            },
            country_id: "required",
            region_id: "required",
            area_id: "required",
            address: "required",
            phone: {
                required: true,
                minlength: 8,
                maxlength: 15
            },

        },
        // in 'messages' user have to specify message as per rules
        messages: {
            name: {
                required:validation_name_required,
                maxlength:validation_name_max_required
            },
            country_id: validation_country_required,
            region_id: validation_region_required,
            area_id: validation_area_required,
            address: validation_address_required,
            phone: {
                required: validation_phone_required,
                minlength: validation_phone_minlength,
                minlength: validation_phone_maxlength,

            },
        },
        submitHandler: function() {
            var form_data = new FormData($('#edit_address_form')[0]);
            action_url = "{{ route('upadte-address') }}";
            var csrf = "{{ csrf_token() }}";
            $.ajax({
                url: action_url,
                data: form_data,
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                processData: false,
                contentType: false,
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                },
                success: function(response) {
                    // return false;
                    // console.log(response);
                    $('.loader').css("visibility", "visible");
                    var url = "{{route('my-address')}}";
                    window.location.href = url;
                },
            });
        }
    });

        function getSubCatList(thisitem) {

    var idCountry = $('#country_id').val();
    var cat_id = $('#cat_id').val();
    //alert(category_id);
    $('#region_id').html('');
    $('#region_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
    $('#area_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
    $.ajax({
        url: "{{ route('getsubcatlist') }}",
        type: "POST",
        data: {
            id: idCountry,
            cat_id: cat_id,
            _token: '{{ csrf_token() }}'
        },
        dataType: 'json',
        success: function(result) {
            console.log(result);
            $('#region_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
            $.each(result.sub, function(key, value) {
                var selected = '';
                selected = value.country_id == idCountry ? "selected" : "";
                $("#region_id").append('<option ' + selected + ' value="' + value.id +
                    '">' +
                    value.title + '</option>');
            });
        }
    });
    }

    function getAreaList(thisitem) {

    var idCountry = $('#region_id').val();
    var cat_id = $('#cat_id').val();

    //alert(category_id);
    $('#area_id').html('');
    $('#area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
    $.ajax({
        url: "{{ route('getarealist') }}",
        type: "POST",
        data: {
            id: idCountry,
            cat_id: cat_id,
            _token: '{{ csrf_token() }}'
        },
        dataType: 'json',
        success: function(result) {
            console.log(result);
            $('#area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
            $.each(result.sub, function(key, value) {
                var selected = '';
                selected = value.area_id == idCountry ? "selected" : "";
                $("#area_id").append('<option ' + selected + ' value="' + value.id +
                    '">' +
                    value.title + '</option>');
            });
        }
    });
    }

</script>
@endsection