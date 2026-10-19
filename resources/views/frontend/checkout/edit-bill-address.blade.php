
<div class="page-border"></div>
<div class="form-group">
    <label for="">{{@Helper::language('name_label_web')}}<span class="text-red star">*</span></label>
    <input type="text" id="bill_name" value="{{@$UserAddressData->name}}" onkeypress="return onlyStrings(event)" placeholder="{{@Helper::language('enter_name_web')}}" name="bill_name" class="">

</div>
</div>
<div class="form-group has-validation">
    <label for="">{{@Helper::language('phone_number')}}<span class="text-red star">*</span></label>
    <div class="input-group phone-number">
        <select class="numbers" id="bill_phonecode" name="bill_phonecode" value="{{@$UserAddressData->phonecode}}">
            @foreach($countryData as $value)
            <option value="{{$value->phonecode}}" {{(@$UserAddressData->phonecode == $value->phonecode) ? 'selected' : ''}}>+ {{$value->phonecode.' ('.$value->shortname.')'}}</option>
            @endforeach
        </select>
        <input type="text" value="{{@$UserAddressData->phone}}" oninput="this.value = this.value.replace(/\D+/g, '')" placeholder="{{@Helper::language('enter_phone_number_place')}} " name="bill_phone" maxlength="15" id="bill_phone">
    </div>
</div>
</div>
<div class="form-group">
    <label for=""> {{@Helper::language('country_label_web')}}<span class="text-red star">*</span></label>
    <select name="bill_country_id" id="bill_country_id" onchange="getBillSubCatList(this)" class="form-select" value="{{@$UserAddressData->country_id}}">
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
    <select value="{{old('bill_region_id')}}" onchange="getBillAreaList(this)" name="bill_region_id" id="bill_region_id" class="form-select">
        <option value="">{{@Helper::language('choose_region_web')}}</option>
        @foreach($region as $value)
        <option value="{{$value->id}}" {{(@$UserAddressData->region_id == $value->id) ? 'selected' : ''}}>{{$value->title}}</option>
        @endforeach
    </select>

</div>
<div class="form-group">
    <label for="">{{@Helper::language('area_label_web')}}<span class="text-red star">*</span></label>
    <select value="{{old('bill_area_id')}}" name="bill_area_id" id="bill_area_id" class="form-select">
        <option value="">{{@Helper::language('choose_area_web')}}</option>
        @foreach($area as $value)
        <option value="{{$value->id}}" {{(@$UserAddressData->area_id == $value->id) ? 'selected' : ''}}>{{$value->title}}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="">{{@Helper::language('zip_code_label')}}<span class="text-red star">*</span></label>
    <input type="text" name="bill_zip_code" id="bill_zip_code" placeholder="{{@Helper::language('enter_code_place')}}" value="{{@$UserAddressData->zip_code}}" class="required" placeholder="Enter Zip Code {{@Helper::language('name_label_web')}}" required>
    <div class="invalid-feedback">
        Please enter Zip Code.
    </div>
</div>                   
<div class="form-group">
    <label for="">{{@Helper::language('city_label')}} <span class="text-red star">*</span></label>
    <input type="text" value="{{@$UserAddressData->city}}" name="bill_city" id="bill_city" placeholder="{{@Helper::language('enter_city_label')}}" class="">
    <div class="invalid-feedback">
        Please enter City.
    </div>
</div>                            
<div class="form-group">
    <label for="">{{@Helper::language('street_address')}} <span class="text-red star">*</span></label>
    <textarea name="bill_address" id="bill_address" placeholder="{{@Helper::language('enter_street_address')}}" col="5" rows="2">{{@$UserAddressData->address}}</textarea>
    <div class="invalid-feedback">
        Please enter Address.
    </div>
</div>

<input type="hidden" name="edit_bill_address_id" id="edit_bill_address_id" value="{{@$UserAddressData->id}}">
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
    function onlyStrings(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

            return false;
        return true;

    }

    $('#bill_name').on("input", function() {
        this.value = this.value.replace(/[^a-zA-Z\s]/gi, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });

</script>
@endpush