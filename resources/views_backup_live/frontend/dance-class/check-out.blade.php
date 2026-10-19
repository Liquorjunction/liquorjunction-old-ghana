@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
<style type="text/css">
    form .error {
        color: red;
    }
</style>
<div class="site_content_cover">
    <!--Page Title-->
    <div class="page_title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Checkout</h1>
                </div>
            </div>
        </div>
    </div>
    <!--Page Title-->

    <!--Breadcrumb-->
    <div class="breadcrumb_cover">
        <div class="container">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><img src="{{ asset('assets/frontend/images/breadcrumb_od.svg') }}" alt="breadcrumb_od" /></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dancecategory') }}">Category</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('danceclassdetailwithid',base64_encode($dance_class->id))}}">{{$dance_class->category_name}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$dance_class->class_name}}</li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--Breadcrumb-->

    <!--Checkout Page-->
    <section class="checkout_page">
        <div class="container">
            <div class="row checkout_course">
                <div class="col-xs-12 col-sm-12 col-md-8">
                    <h2>{{$dance_class->class_name}}</h2>
                    <div class="course_details_cover">
                        <div class="course_info">
                            <img src="{{ asset('uploads/dance_class/images/').'/'.$dance_class->class_thumbnail_image }}" alt="class_img" title="Class Img">
                            <div class="course_desc">
                                <h3>The Complete {{$dance_class->category_name }} Dance Course!</h3>
                                <p>A Class by <strong>{{$dance_class->intructore_name }},</strong> A FreeStyle Dancer</p>
                            </div>
                        </div>
                        <ul class="price_nav">
                            <?php 
                                $class_price = $dance_class->price;
                                $discount = $dance_class->discount;
                                $discount_price = ($class_price * $discount) /100;
                                $total_price = $class_price - $discount_price;

                                $out_1 = strlen($total_price) > 10 ? substr($total_price,0,10)."..." : $total_price;
                                
                                $out_2 = strlen($dance_class->price) > 10 ? substr($dance_class->price,0,10)."..." : $dance_class->price;
                            ?>
                            <li class="main_price">
                                <span>{{isset($setting) ? $setting->currency_symbol : '$'}}</span>
                                <h4>{{$out_1}}</h4>
                            </li>
                            <li class="offer_price">
                                <h4 class="grey_color">{{isset($setting) ? $setting->currency_symbol : '$'}} {{$out_2}}</h4>
                            </li>
                        </ul>
                    </div>
                    <div class="course_payment">
                        <h2>Payment</h2>
                        <form class="row" id="payment-form" action="javascript:void(0)">
<!--                             <div class="col-md-6 form-group">
                                <label for="card name">Name on card</label>
                                <input type="text" name="card_name" id="card_name" placeholder="Name On Card">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="card number">Card number</label>
                                <input type="text" name="card_number" id="card_number" placeholder="0000 0000 0000 0000" onkeypress="return isNumber(event)" maxlength="19">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="cvc/cvv">CVC/CVV</label>
                                <input type="password" name="cvc_cvv" id="cvc_cvv" placeholder="CVC/CVV" onkeypress="return isNumber(event)" maxlength="3">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="cvc/cvv">Expiry Month</label>
                                <input type="text" name="exp_month" id="exp_month" placeholder="MM" maxlength="2" onkeypress="return isNumber(event)">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="cvc/cvv">Expiry Year</label>
                                <input type="text" name="exp_year" id="exp_year" placeholder="YYYY" maxlength="4" onkeypress="return isNumber(event)">
                            </div>
                            <div class="col-md-6 form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="secure_check">
                                    <label class="form-check-label" for="secure_check">Securely save this card for my later purchase</label>
                                </div>
                            </div>
                            <div class="overlay" style="display: none;"></div>
                            <div id="three-ds-container" style="display: none;">
                                <iframe height="450" width="550" id="sample-inline-frame" name="sample-inline-frame"> </iframe>
                            </div> -->
                            <div class="col-md-12 form-group">
                                <button type="submit" class="common_btn submit" id="payment-btn">Make Payment</button>
                            </div>
                        </form>
                        <div class="text-center" id="loader" style="display:none;     margin-left: 550px; margin-top: -120px;">
                                <img class="logo-img" alt="" src="{{ asset('assets/dashboard/images/loading.gif')}}">
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="course_summary">
                        <h3>Summary</h3>
                        <?php $out_2 = strlen($dance_class->price) > 10 ? substr($dance_class->price,0,10)."..." : $dance_class->price;
                        ?>
                        <ul>
                            <?php 
                                $class_price = $dance_class->price;
                                $discount = $dance_class->discount;
                                $discount_price = ($class_price * $discount) /100;
                                $total_price = $class_price - $discount_price;

                                $out_1 = strlen($total_price) > 10 ? substr($total_price,0,10)."..." : $total_price;
                                
                                $out_2 = strlen($dance_class->price) > 10 ? substr($dance_class->price,0,10)."..." : $dance_class->price;
                            ?>
                            <li>
                                <span>Class Price : </span>
                                <span class="class_price">{{isset($setting) ? $setting->currency_symbol : '$'}} {{$out_2}}</span>
                            </li>
                            <li>
                                <span>Discount ({{isset($dance_class->discount) ? $dance_class->discount : '0.0'}}%) : </span>
                                <span class="discount">{{isset($setting) ? $setting->currency_symbol : '$'}} {{isset($discount_price) ? $discount_price : '0.0'}}</span>
                            </li>
                            <li class="total_amt">
                                <span>Total</span>
                                <span>{{isset($setting) ? $setting->currency_symbol : '$'}} {{isset($out_1) ? $out_1 : '0.0'}}</span>
                                <input type="hidden" name="total_price" value="{{$total_price}}">
                            </li>
                        </ul>
                        <div class="checkout_btn"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Checkout Page-->
</div>
<script type="text/javascript" src="https://js.xendit.co/v1/xendit.min.js"></script>
<script type="text/javascript">
    
    Xendit.setPublishableKey('xnd_public_development_ZSINsRFvNy7tLnZa8vDUxqWVahOXgVpY7o1FidnzAENbUtIWbyHlNLtdfjOOwc');
    
    var $form = $('#payment-form');
    $(function() {

        $form.submit(function(event) {
            // if($("#payment-form").valid())
            // {
                // // Disable the submit button to prevent repeated clicks:
                // $('#payment-form').find('.btn_make_payment').prop('disabled', true);
                // // Request a token from Xendit:
                // Xendit.card.createToken({
                //     amount: "{{$total_price}}",
                //     card_number: $form.find('#card_number').val(),
                //     card_exp_month: $form.find('#exp_month').val(),
                //     card_exp_year: $form.find('#exp_year').val(),
                //     card_cvn: $form.find('#cvc_cvv').val(),
                //     is_multiple_use: false,
                //     should_authenticate: true
                // }, xenditResponseHandler);

           
               // var aa = "{{$dance_class->id}}";
                loadershow();
                $.ajax({
                        url: "{{ url('/create-invoice') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            class_id: "{{$dance_class->id}}",
                            total_price: "{{$total_price}}"
                        },
                        success: function (response) 
                        {
                            loaderhide();
                            var url = response.invoice_url;  
                            window.location.href = url;
                        },
                        error:function(err) {
                            if (err.status == 422)
                            {
                              alert(err);
                            }
                        }
                   });

                // $.ajax({
                //         url: "{{ url('/get-balance') }}",
                //         type: 'POST',
                //         dataType: 'json',
                //         data: {
                //             amount: "{{$total_price}}",
                //             card_number: $form.find('#card_number').val(),
                //             card_exp_month: $form.find('#exp_month').val(),
                //             card_exp_year: $form.find('#exp_year').val(),
                //             card_cvn: $form.find('#cvc_cvv').val(),
                //             card_name: $form.find('#card_name').val()
                //         },
                //         success: function (response) 
                //         {
                //             // var url = response.invoice_url;  
                //             // window.location.href = url;
                //             console.log(response);
                //         },
                //         error:function(err) {
                //             if (err.status == 422)
                //             {
                              
                //                alert(err);
                //             }
                //         }
                //    });
            // }

            // Prevent the form from being submitted:
            return false;
        });
    });
    function xenditResponseHandler(err, creditCardToken) {
        
        if (err) {
            // Show the errors on the form
            $('#error pre').text(err.message);
            $('#error').show();
            $form.find('.btn_make_payment').prop('disabled', false); // Re-enable submission

            return;
        }

        if (creditCardToken.status === 'VERIFIED') {
            // Get the token ID:
            var token = creditCardToken.id;

            // Insert the token into the form so it gets submitted to the server:
            $form.append($('<input type="hidden" name="xenditToken" />').val(token));

            // Submit the form to your server:
            $form.get(0).submit();
        } else if (creditCardToken.status === 'IN_REVIEW') {
            window.open(creditCardToken.payer_authentication_url, 'sample-inline-frame');
            $('#three-ds-container').show();
        } else if (creditCardToken.status === 'FAILED') {
            $('#error pre').text(creditCardToken.failure_reason);
            $('#error').show();
            $form.find('.btn_make_payment').prop('disabled', false); // Re-enable submission
        }


    }

    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    $('#card_number').keyup(function() {
      var foo = $(this).val().split(" ").join(""); 
      if (foo.length > 0) {
        foo = foo.match(new RegExp('.{1,4}', 'g')).join(" ");
      }
      $(this).val(foo);
    });

    // $("#payment-btn").attr("disabled", "disabled");

    // $("#secure_check").click(function() {
    //     var checked_status = this.checked;
    //     if (checked_status == true) {
    //        $("#payment-btn").removeAttr("disabled");
    //     } else {
    //        $("#payment-btn").attr("disabled", "disabled");
    //     }
    // });

    $(document).ready(function() {
        $("#payment-form").validate({
                        rules: {
                            'card_name': {
                                required: true,
                                maxlength: 50,
                            },
                            'card_number': {
                                required: true,
                            },
                            'cvc_cvv': {
                                required: true,
                            },
                            'exp_month': {
                                required: true,
                            },
                            'exp_year': {
                                required: true,
                            },
                        },
                        messages: {
                            card_name: {
                                required: "Please enter card name",
                                maxlength: "Card name should not be more than 50 characters",
                            },
                            card_number: {
                                required: "Please enter card number",
                            },
                            dance_level: {
                                required: "Please select dance level",
                            },
                            cvc_cvv: {
                                required: "Please enter cvc/cvv",
                            },
                            exp_month: {
                                required: "Please enter expiry month",
                            },
                            exp_year: {
                                required: "Please enter expiry year",
                            },
                        },
                        highlight: function (element) {
                            $(element).parent().addClass('error')
                        },
                        unhighlight: function (element) {
                            $(element).parent().removeClass('error')
                        }
            });
    });

     // $('#payment-btn').click(function (){
        
     //            $.ajax({
     //                    url: "{{ url('/create-invoice') }}",
     //                    type: 'POST',
     //                    dataType: 'json',
     //                    success: function (response) 
     //                    {
     //                        var url = response.invoice_url;  
     //                        window.location.href = url;
     //                    },
     //                    error:function(err) {
     //                        if (err.status == 422)
     //                        {
                              
     //                           alert(err);
     //                        }
     //                    }
     //               });
        
     // });  

         function loadershow(){
            $("#center-block").attr('disabled','disabled');
            $('#loader').show();                               
          } 

          function loaderhide(){
             
             $('#loader').hide();                               
           }  

</script>
@endsection