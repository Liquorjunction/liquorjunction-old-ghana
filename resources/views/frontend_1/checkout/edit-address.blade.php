
<div class="page-border"></div>
<div class="form-group">
    <label for="">{{@Helper::language('name_label_web')}}<span class="text-red star">*</span></label>
    <input type="text" id="name" value="{{@$UserAddressData->name}}" onkeypress="return onlyString(event)" placeholder="{{@Helper::language('enter_name_web')}}" name="name" id="name" class="">

</div>
</div>
<?php
// dd($countryData);
?>
<div class="form-group has-validation">
    <label for="">{{@Helper::language('phone_number')}}<span class="text-red star">*</span></label>
    <div class="input-group phone-number">
        <select class="numbers" id="phone" name="phonecode" value="{{@$UserAddressData->phonecode}}">
            @foreach($countryData as $value)
            <option value="{{$value->phonecode}}" {{(@$UserAddressData->phonecode == $value->phonecode) ? 'selected' : ''}}>+ {{$value->phonecode.' ('.$value->shortname.')'}}</option>
            @endforeach
        </select>
        <input type="text" value="{{@$UserAddressData->phone}}" oninput="this.value = this.value.replace(/\D+/g, '')" placeholder="{{@Helper::language('enter_phone_number_place')}} " name="phone" maxlength="15" id="phone">
    </div>
</div>
</div>
<div class="form-group">
    <label for=""> {{@Helper::language('country_label_web')}}<span class="text-red star">*</span></label>
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
<div class="form-group">
    <label for="">{{@Helper::language('region_label_web')}}<span class="text-red star">*</span></label>
    <select value="{{old('region_id')}}" onchange="getAreaList(this)" name="region_id" id="region_id" class="form-select">
        <option value="">{{@Helper::language('choose_region_web')}}</option>
        @foreach($region as $value)
        <option value="{{$value->id}}" {{(@$UserAddressData->region_id == $value->id) ? 'selected' : ''}}>{{$value->title}}</option>
        @endforeach
    </select>

</div>
<div class="form-group">
    <label for="">{{@Helper::language('area_label_web')}}<span class="text-red star">*</span></label>
    <select value="{{old('area_id')}}" name="area_id" id="area_id" class="form-select">
        <option value="">{{@Helper::language('choose_area_web')}}</option>
        @foreach($area as $value)
        <option value="{{$value->id}}" {{(@$UserAddressData->area_id == $value->id) ? 'selected' : ''}}>{{$value->title}}</option>
        @endforeach
    </select>
</div>
                            <div class="form-group">
                                <label for="">{{@Helper::language('zip_code_label')}}<span class="text-red star">*</span></label>
                                <input type="text" name="zip_code"placeholder="{{@Helper::language('enter_code_place')}}" value="{{@$UserAddressData->zip_code}}" class="required" placeholder="Enter Zip Code {{@Helper::language('name_label_web')}}" required>
                                <div class="invalid-feedback">
                                    Please enter Zip Code.
                                </div>
                            </div>
                        
                   
                            <div class="form-group">
                                <label for="">{{@Helper::language('city_label')}} <span class="text-red star">*</span></label>
                                <input type="text" value="{{@$UserAddressData->city}}" name="city" id="city" placeholder="{{@Helper::language('enter_city_label')}}" class="">
                                <div class="invalid-feedback">
                                    Please enter City.
                                </div>
                            </div>
                        
<div class="form-group">
    <label for="">{{@Helper::language('street_address')}} <span class="text-red star">*</span></label>
    <textarea name="address" id="address" placeholder="{{@Helper::language('enter_street_address')}}" col="5" rows="2">{{@$UserAddressData->address}}</textarea>
    <div class="invalid-feedback">
        Please enter Address.
    </div>
</div>

<input type="hidden" name="checkout_page" id="checkout_page" value="1">
<input type="hidden" name="edit_address_id" id="edit_address_id" value="{{@$UserAddressData->id}}">
<button type="submit" class="solid-button w-100">Submit</button>
@push('after-scripts')
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
    function onlyNumberKey(num) {
        let ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }
</script>
<script>
    function onlyString(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

            return false;
        return true;

    }


    $('#name').on("input", function() {
        console.log(this.value);
        this.value = this.value.replace(/[^a-zA-Z\s]/gi, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });

    function getSubCatList(thisitem) {

        var idCountry = $('#country_id').val();
        var cat_id = $('#cat_id').val();
        //alert(category_id);
        $('#region_id').html('');
        $('#region_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
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
@endpush