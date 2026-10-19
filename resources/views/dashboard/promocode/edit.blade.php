@extends('dashboard.layouts.master')
@section('title', 'Edit Promo Code')
@section('content')
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<div class="padding edit-package add-schoo">
<div class="box">
    <div class="box-header dker">
        <h3><i class="material-icons">&#xe02e;</i> Edit Promo Code </h3>
        <small>
        <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
        <a href="{{ route('promocode') }}">Promo Code</a> / Edit Promo Code
        </small>
    </div>
    <div class="box-tool">
        <ul class="nav">
            <li class="nav-item inline">
                <a class="nav-link" href="{{ route('promocode') }}">
                <i class="material-icons md-18">×</i>
                </a>
            </li>
        </ul>
    </div>
    <div class="box nav-active-border b-info">
        <div class="tab-content clear b-t">
            <div class="tab-pane active" id="tab_details">
                <div class="box-body">
                    {{ Form::open(['route' => ['promocode.update', $Promocode->id], 'method' => 'POST', 'id' => 'promocode-form', 'files' => true, 'onsubmit' =>"return dateCheck(event)"]) }}
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 form-control-label">Promo Code<span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::text('promo_code', isset($Promocode->promo_name) ? $Promocode->promo_name : old('promocode'), [
                            'placeholder' => 'Promo Code',
                            'class' => 'form-control',
                            'maxlength' => '20',
                            'id' => 'promo_code',
                            ]) !!}
                            @error('promo_code')
                            <div class="valid_field">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="start" class="col-sm-2 form-control-label">Start Date<span class="valid_field">*</span> </label>
                        <div class="col-sm-10">
                            <input class="form-control" id="startdate"
                                value="{{ isset($Promocode->start_date) ? Carbon\Carbon::createFromFormat('Y-m-d', $Promocode->start_date)->format('d-m-Y') : '' }}"
                                name="startdate" placeholder="DD-MM-YYYY" type="datetime" disabled />
                                <input type="hidden" type="datetime" name="startdate" value="{{ isset($Promocode->start_date) ? Carbon\Carbon::createFromFormat('Y-m-d', $Promocode->start_date)->format('d-m-Y') : '' }}">
                            @error('startdate')
                            <div class="valid_field">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="end" class="col-sm-2 form-control-label">End Date<span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input class="form-control" id="enddate" 
                                value="{{ isset($Promocode->end_date) ? Carbon\Carbon::createFromFormat('Y-m-d', $Promocode->end_date)->format('d-m-Y') : '' }}"
                                placeholder="DD-MM-YYYY" name="enddate" type="datetime" />
                            @error('enddate')
                            <div class="valid_field"> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Applicable On<span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <select name="product_type" id="product_type" onchange="showHide()" class="form-control" value="">
                                <option value="">Select type</option>
                                <option {{ (isset($Promocode->product_type) && $Promocode->product_type == 1) || old('product_type') == 1 ? 'selected' : '' }} value="1">Brand</option>
                                <option  {{ (isset($Promocode->product_type) && $Promocode->product_type == 2)  || old('product_type') == 2 ? 'selected' : '' }} value="2">Category</option>
                                <option {{ (isset($Promocode->product_type) && $Promocode->product_type == 3)  || old('product_type') == 3 ? 'selected' : '' }}  value="3">Product</option>
                            </select>
                                @error('product_type')
                                    <div class="valid_field"> {{ $message }} </div>
                                @enderror
                        </div>
                    </div>
                    <div class="form-group row brand">
                            <div class="col-sm-2 form-control-label">Select Brand<span class="valid_field">*</span></div>
                            <div class="col-sm-10">
                                <select name="brand_id" id="brand_id" class="form-control" value="">
                                    <option value="">Select Brand</option>
                                    @foreach ($brand as $key => $value)
                                    <option value="{{ $value->id }}" {{ ($Promocode->brand_id == $value->id) ? 'selected' : ''}}>
                                        {{ ucfirst($value->title) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                    <div class="form-group row category">
                            <div class="col-sm-2 form-control-label ">Select Category<span class="valid_field">*</span></div>
                            <div class="col-sm-10">
                                <select name="category_id" id="category_id" class="form-control" value="{{ old('category_id', @$Promocode->category_id ?: '') }}" onchange="getSubCatList(this)">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $key => $value)
                                    <option value="{{ $value->id }}"  {{ ($Promocode->category_id == $value->id) ? 'selected' : ''}}>
                                        {{ ucfirst($value->title) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                    <div class="form-group row subcategory" style="display: none;">
                            <div class="col-sm-2 form-control-label">Select Subcategory</div>
                            <div class="col-sm-10">
                                <select name="subcategory_id" id="subcategory_id"
                                    class="form-control" value="{{ old('subcategory_id', @$Promocode->subcategory_id) }}">
                                    <option value="">Select Subcategory</option>
                                        @if(!empty($subcategories))
                                            @foreach($subcategories as $key => $value)
                                            <option value="{{$value->id}}" {{ ($Promocode->subcategory_id == $value->id) ? 'selected' : ''}}>{{ucfirst($value->title)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                            </div>
                    </div>
                    <div class="form-group row product">
                            <div class="col-sm-2 form-control-label">Select Product<span class="valid_field">*</span></div>
                            <div class="col-sm-10">
                                <select name="product_id" id="product_id" class="form-control" value="">
                                    <option value="">Select Product</option>
                                    @foreach ($product as $key => $value)
                                    <option value="{{ $value->id }}" {{ ($Promocode->product_id == $value->id) ? 'selected' : ''}}>
                                        {{ ucfirst($value->product_name) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                    </div> --}}

                    <div class="form-group row">
                        <label for="min_amount" class="col-sm-2 form-control-label">Minimum Amount<span
                                class="valid_field">*</span></label>
                        <div class="col-sm-10">
                             {!! Form::text('min_amount',isset($Promocode->minimum_amount) ? $Promocode->minimum_amount : old('min_amount') , [
                            'placeholder' => '100',
                            // 'maxlength' => '5',
                            'class' => 'form-control',
                            'id' => 'min_amount',
                            ]) !!}
                            @error('min_amount')
                            <div class="valid_field">{{ $message }} </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Per User Limit<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-10">
                                {!! Form::text('total_usage',isset($Promocode->total_usage) ? $Promocode->total_usage : old('total_usage'), [
                                    'placeholder' => '3',
                                    'maxlength' => '3',
                                    'class' => 'form-control',
                                    'id' => 'total_usage',
                                    'onkeyup' => 'onlyNumber(this),this.value = minmax(this.value,1,100)',
                                    ]) !!}
                                    @error('total_usage')
                                    <div class="valid_field">{{ $message }} </div>
                                    @enderror
                            </div>
                    </div>
                    <div class="form-group row">
                        <label for="person" class="col-sm-2 form-control-label">Discount Percentage(%)<span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::text(
                            'discount_percentage',
                            isset($Promocode->discount_percentage) ? $Promocode->discount_percentage : old('description_percentage'),
                            [
                            'placeholder' => 'Discount Percentage',
                            'maxlength' => '5',
                            'class' => 'form-control',
                            'id' => 'discount_percentage',
                            'onkeyup' => 'onlyNumber(this),this.value = minmax(this.value,1,100)',
                            ],
                            ) !!}
                            @error('discount_percentage')
                            <div class="valid_field"> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Image</label>
                        <div class="col-sm-10">
                            <input type="file" name="image" id="image" class="form-control" style="border: none">
                            @error('image')
                        <div class="text-danger">
                                    {{ $message }}
                                </div>
                        @enderror
                        </div>
                        <label class="col-sm-2 form-control-label"></label>
                        <?php $src = 'promocode/' . $Promocode->image; ?>
                        <div class="col-sm-8">
                            @if (isset($Promocode->image) && !empty($Promocode->image) && Storage::disk('public')->exists($src))
                        <img id="image" src="{{ asset('storage/promocode/' . $Promocode->image) }}" width="100px" height="100px" />
                        @else
                        <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                        @endif
                        </div>
                        </div> -->
                    <!-- <div class="form-group row">
                        <label for="person" class="col-sm-2 form-control-label">Allowed Time
                        </label>
                        
                        <div class="col-sm-10">
                            {!! Form::text(
                                'allowed_time',
                                isset($Promocode->allowed_time) ? $Promocode->allowed_time : old('allowed_time'),
                                [
                                    'placeholder' => 'Allowed Time',
                                    'maxlength' => '2',
                                    'class' => 'form-control',
                                    'id' => 'allowed_time',
                                    'onkeyup' => 'onlyNumber(this),this.value = minmax(this.value,1,10)',
                                ],
                            ) !!}
                            <small>* how many times a single user is allowed to use the promotion code</small>
                            @error('allowed_time')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        </div> -->
                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            @if (isset($Promocode) && !empty($Promocode))
                            <button type="submit" class="btn btn-primary m-t"><i class="material-icons">
                            &#xe31b;</i> Update</button>
                            @else
                            <button type="submit" class="btn btn-primary m-t"><i class="material-icons">
                            &#xe31b;</i> {!! __('backend.add') !!}</button>
                            @endif
                            <a href="{{ route('promocode') }}" class="btn btn-default m-t"><i
                                class="material-icons">
                            &#xe5cd;</i> {!! __('backend.cancel') !!}</a>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('after-scripts')
<script src="{{ asset('assets/dashboard/js/jquery.validate.min.js') }} "></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/moment-with-locales.min.js"
    integrity="sha512-bD+NptvsSHsytHV6cB1VGqsz70DB8skG6CR943xg1cm8pIoGP/uhZz1RrMQCgVDGI35iDcpnp0cIIu31RNM6SQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
    
    var validNumber = new RegExp(/^\d*\.?\d*$/);
    var lastValid = document.getElementById("discount_percentage").value;
    
    function onlyNumber(elem) {
        if (validNumber.test(elem.value)) {
            lastValid = elem.value;
        } else {
            elem.value = lastValid;
        }
    }
    
    function minmax(value, min, max) {
        if (parseInt(value) < min || isNaN(parseInt(value)))
            return '';
        else if (parseInt(value) > max)
            return max;
        else return value;
    }
    
    function dateCheck() {
        var from_date = $('#startdate').val();
        var to_date = $('#enddate').val();
        if (from_date != '' && from_date != null && to_date != '' && to_date != null) {
            console.log(to_date);
            console.log(from_date);
            var date1Updated = new Date(to_date.replace(/-/g, '/'));
            var date2Updated = new Date(from_date.replace(/-/g, '/'));
            if (date1Updated < date2Updated) {
                alert('End Date should be greater than the Start Date!!');
                return false;
            } else {
                $('#promocode-form').submit();
            }
        } else {
            $('#promocode-form').submit();
        }
    }
    $("#startdate").datepicker({
        changeMonth: true,
        startDate: '+0d',
        changeYear: true,
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        orientation: "bottom",
        autoclose: true
    }).on('changeDate', function(selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#enddate').datepicker('setStartDate', minDate);
    });
    $("#enddate").datepicker({
        changeMonth: true,
        //startDate: '+0d',
        changeYear: true,
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        orientation: "bottom",
        autoclose: true
    }).on('click', function(selected) {
        var minDate = $('#startdate').datepicker("getDate");
        minDate = new Date(minDate.valueOf());
        $('#enddate').datepicker('setStartDate', minDate);
    });
    // .on('changeDate', function(selected) {
    //     var minDate = new Date(selected.date.valueOf());
    //     $('#startdate').datepicker('setEndDate', minDate);
    // });
     
    
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

{{-- <script>
    // validation for Fields
    $(document).ready(function () {
        $("#promocode-form").validate({

            rules: {
                product_type: {
                    required: true
                },
                brand_id: {
                    required: function () {
                        return $("#product_type").val() == "1";
                    }
                },
                category_id: {
                    required: function () {
                        return $("#product_type").val() == "2";
                    }
                },
                product_id: {
                    required: function () {
                        return $("#product_type").val() == "3";
                    }
                },
            },
            messages: {
                product_type: {
                    required: "Product Type is required."
                },
                brand_id: {
                    required: "Brand is required when Product Type is Brand."
                },
                category_id: {
                    required: "Category is required when Product Type is Category."
                },
                product_id: {
                    required: "Product is required when Product Type is Product."
                },
            }
        })

        $("#product_type").change(function () {
            $("#brand_id, #category_id, #product_id").valid();
        });

    })

</script> --}}

{{-- <script>
    $(document).ready(function() {
        showHide();
    });

    function showHide() {
        var type = $("#product_type").val();
        $('.category').hide();
        $('.product').hide();
        $('.brand').hide();
        $('.subcategory').hide();
        if (type != '' && type != null) {
            if (type == '1') {
                $('.category').hide();
                $('.product').hide();
                $('.brand').show();
                $('.subcategory').hide();
            } else if(type == '2') {
                $('.category').show();
                $('.product').hide();
                $('.brand').hide();
                $('.subcategory').show();
            }else{
                $('.category').hide();
                $('.product').show();
                $('.brand').hide();
                $('.subcategory').hide();
            }
        }
    }
</script> --}}

{{-- <script>
    function getSubCatList(thisitem) {
        var idCategory = $('#category_id').val();
        $('#subcategory_id').html('<option value="">Select Subcategory</option>');

        $.ajax({
            url: "{{ route('product.getsubcatlist') }}",
            type: "POST",
            data: {
                id: idCategory,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                if (result.sub && result.sub.length > 0) {
                    $.each(result.sub, function(key, value) {
                        $("#subcategory_id").append(
                            '<option value="' + value.id + '">' + value.title + '</option>'
                        );
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }
</script>  --}}

@endpush