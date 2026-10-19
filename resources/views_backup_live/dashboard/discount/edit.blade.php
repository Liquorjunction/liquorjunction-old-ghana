<div class="modal-header">
    <h4 class="modal-title">Edit Discount</h4>
</div>

<form class="cmxform" id="discountEditForm" method="post" action="" autocomplete="off">
    <div class="modal-body">

         <div class="form-group row">
            <label class="col-sm-5 form-control-label">Minimum Amount<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="number" name="min_amount" id="min_amount" class="form-control" placeholder="100" value="{{ $discountData->min_amount }}" required>
                <span style="color: red;display: none;" id="errorMsgMin" class='validate'></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-5 form-control-label">Discount Type<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <select name="discount_types" id="discount_types" class="form-control">
                    <option value="percentage" {{ (isset($discountData->discount_type) && $discountData->discount_type == 'percentage') ? 'selected' : '' }}>Percentage</option>
                    <option value="flat" {{ (isset($discountData->discount_type) && $discountData->discount_type == 'flat') ? 'selected' : '' }}>Flat</option>
                </select>                
                <span style="color: red;display: none;" id="errorMsgType" class='validate'></span>
            </div>
        </div>  

        <div class="form-group row discountAmount">
            <label class="col-sm-5 form-control-label">Discount Amount<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="number" name="dis_amount" id="dis_amount" class="form-control" placeholder="100" value="{{ $discountData->discount_amount }}" required>
                <span style="color: red;display: none;" id="errorMsgAmount" class='validate'></span>
            </div>
        </div>

        <div class="form-group row discountPercentage">
            <label class="col-sm-5 form-control-label">Discount Percentage(%)<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="number" name="dis_percentage" id="dis_percentage" class="form-control" placeholder="100" value="{{ $discountData->discount_percentage }}" required>
                <span style="color: red;display: none;" id="errorMsgPercentage" class='validate'></span>
            </div>
        </div>


        <div class="form-group row discountUpto">
                <label class="col-sm-5 form-control-label">Discount Up to Amount<span class="valid_field">*</span></label>
                <div class="col-sm-7">
                    <input type="number" name="upto_amount"  id="upto_amount" class="form-control" placeholder="200" value="{{ $discountData->upto_amount }}" required>
                    <span style="color: red;display: none;" id="errorMsgUpto" class='validate'></span>
                </div>
        </div>

        {{-- <div class="form-group row">
            <label class="col-sm-4 form-control-label">Maximum Amount<span
                    class="valid_field">*</span></label>
            <div class="col-sm-8">
                <input type="number"  name="max_amount"  id="max_amount" class="form-control"
                 placeholder="200"
                    value="{{ $discountData->max_amount }}" required>
                <span style="color: red;display: none;" id="errorMsgMax" class='validate'></span>
            </div>
        </div> --}}

        <div class="form-group row">
            <label class="col-sm-5 form-control-label">Expiry Date<span
                    class="valid_field">*</span></label>
            <div class="col-sm-7">
                <input type="date"  name="expiry_date"  id="expiry_date" class="form-control"
                 placeholder="200"
                    value="{{$discountData->expiry_date }}" required>
                <span style="color: red;display: none;" id="errorMsgExpiry" class='validate'></span>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <input type="hidden" name="discount_id" id="discount_id" value="{{$discountData->id}}">
        <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i> Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
    </div>
{{Form::close()}}
    <!-- </div> -->

<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script>
      $(document).ready(function(e) {
            var discountform = $("#discountEditForm").validate({
            
                rules: {
                    discount_types: {
                        required: true,
                    },
                    dis_amount: {
                        required: function () {
                            return $("#dis_percentage").val() === "";
                        },
                        number: true,
                        min: 0
                    },
                    dis_percentage: {
                        required: function () {
                            return $("#dis_amount").val() === "";
                        },
                        number: true,
                        min: 0
                    },
                    min_amount: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    // max_amount: {
                    //     required: true,
                    //     number: true,
                    //     min: 0,
                    //     greaterThan: true // no param passed
                    // },
                    upto_amount:{
                        required: function () {
                            return $("#dis_amount").val() === "";
                        },
                        number: true,
                        min: 0 
                    },
                    expiry_date: {
                        required: true,
                        date: true,
                        futureDate: true
                    },
                },
                messages: {
                    discount_types: {
                        required: "Discount Type Field is required.",
                    },
                    dis_amount: {
                        required: "Discount Amount Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    dis_percentage: {
                        required: "Discount Percentage Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    min_amount: {
                        required: "Minimum Amount Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    // max_amount: {
                    //     required: "Maximum Amount Field is required.",
                    //     number: "Please enter a valid number.",
                    //     min: "Amount must be greater than or equal to 0.",
                    //     greaterThan: "Maximum amount must be greater than minimum amount."
                    // },
                    upto_amount: {
                        required: "Discount Up to Amount Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    expiry_date: {
                        required: "Expiry Date Field is required.",
                        date: "Enter a valid date.",
                        futureDate: "Expiry date must be in the future."
                    },
                },
                submitHandler: function() {
                    var form_data = new FormData($('#discountEditForm')[0]);
                    action_url = "{{ route('discountstore') }}";
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
                            // $('.loader').css("visibility", "visible");
                        },
                        success: function(data) {
                            if (data.success) {
                                 $('.loader').css("visibility", "visible");
                                window.location.href = "{{ route('discount') }}";
                            }
                        },
                        error: function(errors) {
                            // $('.loader').css("visibility", "hidden");
                            var erroJson = JSON.parse(errors.responseText);
                            for (var err in erroJson) {
                                console.log(erroJson);
                                for (var errstr of erroJson[err])
                                $("span#errorMessage").css("display", "block");
                                $("span#errorMsgTitle").css("display", "block");
                                $("span#errorMsgTitle").html(erroJson.title);

                            }
                        }
                    });
                }
            });

            // $.validator.addMethod("greaterThan", function (value, element) {
            //     var minValRaw = $(element.form).find('[name="min_amount"]').val();
            //     var maxValRaw = value;

            //     var minVal = parseFloat(minValRaw);
            //     var maxVal = parseFloat(maxValRaw);

            //     if (isNaN(minVal) || isNaN(maxVal)) return true;

            //     return maxVal > minVal;
            // }, "Maximum amount must be greater than minimum amount.");


            // $('#min_amount').on('keyup change blur', function () {
            //     $('#max_amount').valid();
            // });


            $.validator.addMethod("futureDate", function(value, element) {
                var now = new Date();
                var inputDate = new Date(value);
                return inputDate > now;
            }, "Date must be in the future.");

            $('#myModal').on('hidden.bs.modal', function() {
                discountform.resetForm();
                $('#myModal form')[0].reset();
            })

        });
</script>

<script>
     $(document).ready(function() {
            toggleFieldsBasedOnDiscountType();
     });

    $('#discount_types').change(function() {
        toggleFieldsBasedOnDiscountType();
    });

    function toggleFieldsBasedOnDiscountType() {
        var discountType = $('#discount_types').val();

        $('.discountAmount').hide();
        $('.discountPercentage').hide();
        $('.discountUpto').hide();

        if (discountType == 'percentage') {
            $('.discountAmount').hide();
            $('.discountPercentage').show();
            $('.discountUpto').show();

        } else if (discountType =='flat') {
            $('.discountAmount').show();
            $('.discountPercentage').hide();
            $('.discountUpto').hide();
        }
    }

</script>

