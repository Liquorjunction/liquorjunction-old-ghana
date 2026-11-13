@extends('frontEnd.layouts.new_app')
@section('title','Contact Us')
@section('content')
@include('sweetalert::alert')
<?php
$setting = DB::table('settings')->first();
// echo "<pre>";print_r($setting);exit();
?>
<main class="site-content">
    <div class="loader" id="loader"></div> 
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    <li><p class="text-black body-normal">Contact Us</p></li>
                </ul>
            </div>
        </div>        
        <section class="contact pt-30 py-80">
            <div class="container">
                <h2>Contact Us</h2>
                <div class="row">
                    <div class="col-md-4">
                        <div class="account-sidebar">
                            <div class="footer-contact">
                                <h4>Contact Information</h4>
                                <ul class="mb-0">
                                    <li><a href="tel:{{@$setting->phone}}" class="contact-no body-large">{{@$setting->phone}}</a></li>
                                    <li><span class="address body-large">{{@$setting->address}}</span></li>
                                </ul>
                            </div>
                            <div class="footer-social">
                                <h4>Social Handles</h4>
                                <ul class="mb-0">
                                    <li><a href="{{@$setting->facebook_link}}" target="_blank" alt="facebook"><i class="fa-brands fa-facebook-f text-red"></i></a></li>
                                    <li><a href="{{@$setting->twitter_link}}" target="_blank" alt="linkedin"><i class="fa-brands fa-linkedin-in text-red"></i></a></li>
                                    <li><a href="{{@$setting->instagram_link}}" target="_blank" alt="instagram"><i class="fa-brands fa-instagram text-red"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- <ul class="account-sidebar">
                            <li><a href="tel:1234567890" class="contact-no body-large">(123) 456-7890</a></li>
                            <li><span class="address body-large">Trade25 Company, 123 East, 17th Street</span></li>
                        </ul> -->
                    </div>
                    <div class="col-md-8">
                        <div class="common-card">
                            <form class="row edit-address-form" id="contactForm" novalidate>
                                <div class="col-lg-6 col-md-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="">Name <span class="valid_field">*</span></label>
                                        <input type="text" name="name" id="name" value="{{old('name')}}" placeholder="Enter name"  class="">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-6">                                    
                                    <div class="form-group">
                                        <label for="">Email <span class="valid_field">*</span></label>
                                        <input type="email" placeholder="Enter email" name="email" id="email" value="{{old('email')}}">
                                        
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group has-validation">
                                        <label for="">Phone Number <span class="valid_field">*</span></label>
                                        <div class="input-group phone-number">
                                            <span class="numbers body-normal text-black d-inline-block">+61</span>
                                            <input type="tel" placeholder="Enter phone number" name="phone" id="phone" value="{{old('phone')}}">
                                            
                                        </div>                                                                                
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Message <span class="text-red">*</span></label>
                                        <textarea name="message" id="message" col="5" rows="1"  placeholder="Enter address"></textarea>
                                        
                                    </div>
                                </div>                                
                                <div class="col-lg-6 col-md-12 col-sm-6">
                                    <button class="common-btn w-100 hvr-radial-out" type="submit">Save details</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>            
        </section>
    </main>
  <script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">

         $('#name').on("input", function () {
                // console.log(this.value);
                this.value = this.value.replace(/[^a-zA-Z\s]/gi,''); 
                $(this).val($(this).val().replace(/^\s+/g, ''));
            });

         $('#phone').on("input", function () {
            this.value = this.value.replace(/[^0-9\.]/g,''); 
            $(this).val($(this).val().replace(/^\s+/g, ''));
        });
        var test = $("#contactForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    name: "required",
                    email: {
                    required: true,
                    email: true
                    },
                    phone: {
                        required :true,
                        minlength:8,
                        maxlength:15
                    },
                   // phone: "required",
                    // description: "required",
                    message: {
                        required :true,
                        maxlength:255
                    },
                    // file-upload-input: "required",
                    
                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    name: " Name field is required",
                    email: {
                        required: "Email field is required",
                        email: "Please enter a valid email"
                    },
                    phone: {
                        required: " Phone number field is required",
                        minlength : " Phone number must not less than 8 digits",
                        maxlength : " Phone number must not more than 15 digits"
                    },
                    //phone: " Phone number field is required",
                    // description: " Description field is required",
                    message: {
                        required : "Message field is required",
                        maxlength : "Maxlength 255 character required"
                    },
                    // file-upload-input: " Quote Image field is required",
                   
                },
                submitHandler: function(){
                    var form_data = new FormData($('#contactForm')[0]);
                    action_url = "{{ route('store-contact-us') }}";
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
                             beforeSend: function(){
                                    // $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
                            success: function(data){
                                // console.log(data)
                                // return false;
                                // $(el).parents('.cart-product-box-content').find('b[name=price]').text(fix_price*text);
                               // return false;
                                if (data.success) {
                                    $('.loader').css("visibility", "visible");
                                    window.location.href = "{{ route('contact-us')}}";
                                }
                            },
                            
                        });
                }
            });
    </script>
@endsection