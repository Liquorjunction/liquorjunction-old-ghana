@extends('frontend.layouts.app')
@section('title',Helper::language('add_address'))
@section('content')
@include('sweetalert::alert')
<style>
    .text-red-point{
        color:red;
    }

    .form-check {
    padding-left: 0 !important;
    margin-left: 0 !important;
    }
</style>
<div class="bread-crumb-block">
    <div class="container">
        <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
        <li class="breadcrumb-item"><a href="{{route('my-account')}}">{{@Helper::language('my_account_label')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page"> {{@Helper::language('add_address')}}</li>
        </ul>
    </div>
</div>
<section class="edit-bill-address pt-20 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                @include('frontend.layouts.account-sidebar')
            </div>
            <div class="col-lg-9 col-md-8">
                <h2>{{@Helper::language('add_address')}}</h2>
                <div class="common-card">
                    <form class="row edit-bill-address-form" id="add_bill_address_form" novalidate>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('name_label_web')}} <span class="text-red">*</span></label>
                                <input type="text" value="{{old('bill_name')}}" placeholder="{{@Helper::language('enter_name_web')}}" name="bill_name" id="bill_name" class="" placeholder="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group has-validation">
                                <label for="">{{@Helper::language('phone_number')}} <span class="text-red-point">*</span></label>
                                <div class="input-group phone-number">
                                    <select class="numbers" name="bill_phonecode" id="bill_phonecode">
                                        @foreach($countryData as $value)
                                        <option value="{{$value->phonecode}}">+{{$value->phonecode.' ('.$value->shortname.')' }}</option>
                                        @endforeach
                                    </select>
                                    <input type="tel" value="{{old('bill_phone')}}" placeholder="{{@Helper::language('enter_phone_number_place')}} " name="bill_phone" id="bill_phone">

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('country_label_web')}} <span class="text-red">*</span></label>
                                <select value="{{old('bill_country_id')}}" onchange="getSubCatList(this)" name="bill_country_id" id="bill_country_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_country_web')}}</option>
                                    <?php 
                                        $countryData = @$countryData->sortBy(['name', 'ASC']);
                                        $countryData = $countryData->values();
                                    ?>
                                    @foreach($countryData as $value)
                                    <?php $selected = ''; ?>
                                    @if ($value->id == old('bill_country_id'))
                                    <?php $selected = 'selected'; ?>
                                    @endif
                                    <option {{ $selected }} value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('region_label_web')}}<span class="text-red">*</span></label>
                                <select value="{{old('bill_region_id')}}" onchange="getAreaList(this)" name="bill_region_id" id="bill_region_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_region_web')}}</option>
                                    {{-- @foreach($region as $value)
                                    <option value="{{$value->id}}">{{$value->title}}</option>
                                    @endforeach --}}
                                </select>

                            </div>
                        </div>
                         <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('city_label')}} <span class="text-red">*</span></label>
                                <input type="text" value="{{old('bill_city')}}" placeholder="{{@Helper::language('enter_city_label')}}" name="bill_city" id="bill_city" class="">

                            </div>
                        </div> 
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('area_label_web')}}<span class="text-red">*</span></label>
                                <select value="{{old('bill_area_id')}}" name="bill_area_id" id="bill_area_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_area_web')}}</option>
                                    {{-- @foreach($area as $value)
                                    <option value="{{$value->id}}">{{$value->title}}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="">{{@Helper::language('street_address')}} <span class="text-red">*</span></label>
                                <textarea name="bill_address" id="bill_address" col="5" rows="2" placeholder="{{@Helper::language('enter_street_address')}}">{{old('bill_address')}}</textarea>

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6">
                        <div class="form-group">
                            <label for="bill_zip_code">{{@Helper::language('zip_code_label')}} <span class="text-red">*</span></label>
                            <input type="text" value="{{old('bill_zip_code')}}" placeholder="{{@Helper::language('enter_zip_code')}}" name="bill_zip_code" id="bill_zip_code" class="">
                        </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-6" style="margin-bottom:15px ">
                            <div class="form-check" style="display: flex; align-items: center">
                                <input class="form-check-input" type="checkbox" value="" id="defaultAdd">
                                <label class="form-check-label"  style="margin-top: 10px;color: #858584 !important;cursor: pointer;padding-left:10px;font-size:15px;">Set as Default</label>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12">
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

    $('#bill_phone').on("input", function() {
        this.value = this.value.replace(/[^0-9\.]/g, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });

    $('#bill_name').on("input", function() {
        this.value = this.value.replace(/[^a-zA-Z\s]/gi, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });
    var validation_name_required = "{{ \Helper::language('name_field_is_required'); }}";
    var validation_name_max_required = "{{ \Helper::language('the_name_may_not_be_greater_than_40_characters'); }}";

    var validation_states_required = "{{ \Helper::language('states_field_is_required'); }}";
    var validation_zip_code_required = "{{ \Helper::language('zipcode_field_is_required'); }}";
    var validation_city_required = "{{ \Helper::language('city_field_is_required'); }}";

    var validation_address_required = "{{ \Helper::language('address_field_is_required'); }}";
    var validation_country_required = "{{ \Helper::language('validation_country_required'); }}";

    var validation_region_required = "{{ \Helper::language('validation_region_required'); }}";
    var validation_area_required = "{{ \Helper::language('validation_area_required'); }}";

    var validation_phone_required = "{{ \Helper::language('phone_number_field_is_required'); }}";
    var validation_phone_minlength = "{{ \Helper::language('phone_number_min_max'); }}";
    var validation_phone_maxlength = "{{ \Helper::language('phone_number_min_max'); }}";

    var test = $("#add_bill_address_form").validate({
        rules: {
            bill_name: {
                required:true,
                maxlength: 40,
            },
            bill_zip_code: "required",
            bill_city: "required",
            bill_address: "required",
            bill_country_id: "required",
            bill_region_id:"required",
            bill_area_id:"required",
            bill_phone: {
                required: true,
                minlength: 8,
                maxlength: 15
            },

        },
        messages: {
            bill_name: {
                required:validation_name_required,
                maxlength:validation_name_max_required
            },
            bill_zip_code: validation_zip_code_required,
            bill_city: validation_city_required,
            bill_address: validation_address_required,
            bill_country_id: validation_country_required,
            bill_region_id: validation_region_required,
            bill_area_id: validation_area_required,
            bill_phone: {
                required: validation_phone_required,
                minlength: validation_phone_minlength,
                minlength: validation_phone_maxlength,

            },
        },
        submitHandler: function() {
            var form_data = new FormData($('#add_bill_address_form')[0]);
            var isDefaultChecked = $("#defaultAdd").is(":checked");
            form_data.append('isDefault', isDefaultChecked ? 1 : 0);

            action_url = "{{ route('store-bill-address') }}";
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
                    $('.loader').css("visibility", "visible");
                    var url = "{{route('my-address')}}";
                    window.location.href = url;
                },
            });
        }
    });

    function getSubCatList(thisitem) {

        var idCountry = $('#bill_country_id').val();
        var cat_id = $('#cat_id').val();

        $('#bill_region_id').html('');
        $('#bill_region_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
        $('#bill_area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
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
                $('#bill_region_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
                $.each(result.sub, function(key, value) {
                    var selected = '';
                    selected = value.country_id == idCountry ? "selected" : "";
                    $("#bill_region_id").append('<option ' + selected + ' value="' + value.id +
                        '">' +
                        value.title + '</option>');
                });
            }
        });
    }

    function getAreaList(thisitem) {

        var idRegion = $('#bill_region_id').val();
        var cat_id = $('#cat_id').val();

        $('#bill_area_id').html('');
        $('#bill_area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
        $.ajax({
            url: "{{ route('getarealist') }}",
            type: "POST",
            data: {
                id: idRegion,
                cat_id: cat_id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#bill_area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
                $.each(result.sub, function(key, value) {
                    var selected = '';
                    selected = value.area_id == idRegion ? "selected" : "";
                    $("#bill_area_id").append('<option ' + selected + ' value="' + value.id +
                        '">' +
                        value.title + '</option>');
                });
            }
        });
    }
</script>
@endsection