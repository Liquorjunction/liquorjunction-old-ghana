@extends('dashboard.layouts.master')
@section('title', 'Create Promo Code')
@section('content')
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<style type="text/css">
    #startdate {
    z-index: 1200 !important;
    }
</style>
<div class="padding edit-package add-schoo">
<div class="box">
    <div class="box-header dker">
        <h3><i class="material-icons">&#xe02e;</i> New Promo Code </h3>
        <small>
        <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
        <a href="{{ route('promocode') }}">Promo Code</a> / New Promo Code
        </small>
    </div>
    <div class="box-tool">
        <ul class="nav">
            <li class="nav-item inline">
                <a class="nav-link" href="{{ route('users') }}">
                <i class="material-icons md-18">×</i>
                </a>
            </li>
        </ul>
    </div>
    <div class="box nav-active-border b-info" style="position:relative;">
        <div class="tab-content clear b-t">
            <div class="tab-pane active" id="tab_details">
                <div class="box-body">
                    {{ Form::open(['route' => ['promocode.store'], 'method' => 'POST', 'id' => 'promocode-form', 'files' => true]) }}
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 form-control-label">Promo Code<span
                            class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::text('promo_code', '', [
                            'placeholder' => 'Promo Code',
                            'maxlength' => '20',
                            'class' => 'form-control',
                            'id' => 'promo_code',
                            ]) !!}
                            @error('promo_code')
                            <div class="valid_field">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="start" class="col-sm-2 form-control-label">Start Date<span
                            class="valid_field">*</span> </label>
                        <div class="col-sm-10" style="position:relative;">
                            <input class="form-control" id="startdate" name="startdate"
                                value="{{ old('startdate') }}" placeholder="DD-MM-YYYY" type="datetime" />
                            @error('startdate')
                            <div class="valid_field">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="end" class="col-sm-2 form-control-label">End Date<span
                            class="valid_field">*</span></label>
                        <div class="col-sm-10" style="position:relative;">
                            <input class="form-control" id="enddate" placeholder="DD-MM-YYYY"
                                value="{{ old('enddate') }}" name="enddate" type="datetime" />
                            @error('enddate')
                            <div class="valid_field"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>
                    {{-- <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Applicable On<span class="valid_field">*</span></label>
                            <div class="col-sm-10"  style="position:relative;">
                                <select name="product_type" id="product_type" onchange="showHide()" class="form-control" value="">
                                    <option value="">Select Type</option>
                                    <option value="1">Brand</option>
                                    <option value="2">Category</option>
                                    <option value="3">Product</option>
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
                                    <option value="{{ $value->id }}">
                                        {{ ucfirst($value->title) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                    <div class="form-group row category">
                            <div class="col-sm-2 form-control-label ">Select Category<span class="valid_field">*</span></div>
                            <div class="col-sm-10">
                                <select name="category_id" id="category_id" class="form-control" value="">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $key => $value)
                                    <option value="{{ $value->id }}">
                                        {{ ucfirst($value->title) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                    <div class="form-group row subcategory" style="display: none;">
                            <div class="col-sm-2 form-control-label">Select Subcategory</div>
                            <div class="col-sm-10">
                                <select name="subcategory_id" id="subcategory_id" onchange="getSubCatList(this)"
                                    class="form-control" value="">
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>
                    </div>
                    <div class="form-group row product">
                            <div class="col-sm-2 form-control-label">Select Product<span class="valid_field">*</span></div>
                            <div class="col-sm-10">
                                <select name="product_id" id="product_id" class="form-control" value="">
                                    <option value="">Select Product</option>
                                    @foreach ($product as $key => $value)
                                    <option value="{{ $value->id }}" >
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
                             {!! Form::text('min_amount','' , [
                            'placeholder' => '100',
                            'maxlength' => '5',
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
                                {!! Form::text('total_usage','' , [
                                    'placeholder' => '3',
                                    'maxlength' => '5',
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
                        <label for="person" class="col-sm-2 form-control-label">Discount Percentage(%)<span
                            class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            {!! Form::text('discount_percentage', old('discount_percentage'), [
                            'placeholder' => 'Discount Percentage',
                            'maxlength' => '5',
                            'class' => 'form-control',
                            'id' => 'discount_percentage',
                            'onkeyup' => 'onlyNumber(this),this.value = minmax(this.value,1,100)',
                            ]) !!}
                            @error('discount_percentage')
                            <div class="valid_field">{{ $message }} </div>
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
                        </div> -->
                    <div class="form-group row">
                        <!-- <label for="person" class="col-sm-2 form-control-label">Allowed Time
                            </label>
                            
                            <div class="col-sm-10">
                                {!! Form::text('allowed_time', old('allowed_time'), [
                                    'placeholder' => 'Allowed Time',
                                    'maxlength' => '2',
                                    'class' => 'form-control',
                                    'id' => 'allowed_time',
                                    'onkeyup' => 'onlyNumber(this),this.value = minmax(this.value,1,10)',
                                ]) !!}
                                <small>* how many times a single user is allowed to use the promotion code</small>
                                @error('allowed_time')
                            <div class="text-danger">
                                        {{ $message }}
                                    </div>
                            @enderror
                            
                            </div> -->
                    </div>
                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit"  class="btn btn-primary m-t" onclick ="dateCheck()"><i
                                class="material-icons">
                            &#xe31b;</i> {!! __('backend.add') !!}</button>
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
    
    function dateCheck(event) {  
        var from_date = $('#startdate').val();
        var to_date = $('#enddate').val();
        if (from_date != '' && from_date != null && to_date != '' && to_date != null) {
            // if (new Date(to_date) < new Date(from_date)) {
            //     alert('End Date should be greater than the Start Date!!');
            //     return false;
            // } else {
            //   //  $('#promocode-form').submit();
            // }
        } else {
         //   $('#promocode-form').submit();
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
        autoclose: true,
    }).on('click', function(selected) {
        var minDate = $('#startdate').datepicker("getDate");
        minDate = new Date(minDate.valueOf());
        $('#enddate').datepicker('setStartDate', minDate);
    });
   
        
    var specialKeys = new Array();
    specialKeys.push(8);
    
    function IsNumeric(e) {
    
        var keyCode = e.which ? e.which : e.keyCode
        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
        //document.getElementById("error").style.display = ret ? "none" : "inline";  
        return ret;
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
        $('.brand').hide();
        $('.product').hide();
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
            }
            else{
                $('.category').hide();
                $('.product').show();
                $('.brand').hide();
                $('.subcategory').hide();
            }
        }
    }
</script> --}}

{{-- <script>
    // Sub Category
    function getSubCatList(thisitem) {
        var idCategory = $('#category_id').val();
        var cat_id = $('#cate_id').val();

        $('#sub_category_id').html('');
        $('#sub_category_id').html('<option value="">Select Subcategory</option>');
        $.ajax({
            url: "{{ route('product.getsubcatlist') }}",
            type: "POST",
            data: {
                id: idCategory,
                cat_id: cat_id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                $('#sub_category_id').html('<option value="">Select Category</option>');
                $.each(result.sub, function(key, value) {
                    var selected = '';
                    selected = value.category_id == idCategory ? "selected" : "";
                    $("#sub_category_id").append('<option ' + selected + ' value="' + value.id +
                        '">' +
                        value.title + '</option>');
                });
            }
        });
    }

    const empty_product_subcategory = () => $('#subcategory_id option:not(:first-child)').remove();

    var product_category = "{{ old('category_id') }}";
    var product_subcategory = "{{ old('subcategory_id') }}";

    function getSubCategory() {
        var category_ele = $('#category_id');
        var subcategory_ele = $("#subcategory_id");
        var category_val = category_ele.val();
        if (!category_ele) {
            category_val = product_category;
        }
                   
        empty_product_subcategory();
        if (category_val) {
            var __url = "{{ route('getsubcategories', ['id' => ':id']) }}".replace(':id', category_val);
            $.ajax({
                url: __url,
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    var subcats = data.data;
                    if (data.code == 200) {
                        subcats.map((ele, index) => {
                            var is_selected = product_subcategory ? 'selected="selected"' : "";
                            subcategory_ele.append(
                                `<option value="${ele.id}" ${is_selected}>${ele.title}</option>`
                                );
                        });
                    } else {
                        alert('Something went wrong.');
                    }
                }
            })
        }
    }

    (function() {
        getSubCategory();
        $("#category_id").on('change', function(e) {
            getSubCategory();
        });
    })();

</script> --}}

@endpush