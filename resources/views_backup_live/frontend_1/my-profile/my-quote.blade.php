@extends('frontEnd.layouts.new_app')
@section('title','My Quote')
@section('content')
@include('sweetalert::alert')

  <main class="site-content">
     <div class="loader" id="loader"></div> 
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    <li><p class="text-black body-normal">My Quote</p></li>
                </ul>
            </div>
        </div>        
        <section class="account my-quote-listing pt-30 py-80">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-4">
                       @include('frontEnd.layouts.account-sidebar')
                    </div>
                    <div class="col-lg-9 col-md-8">
                        
                        <div class="quote-list-header">
                            <h1 class="mb-0">My Quote</h1>
                            <button class="small-border-btn hvr-radial-out-border" data-bs-toggle="modal" data-bs-target="#getquoteModal">Add new quote</button>
                        </div>
                        <ul class="common-card quote-list-items mb-0">
                            @if($myQuoteData->count() > 0)
                        	@foreach($myQuoteData as $quote)
                            <?php 
                            $stringLength = Str::length($quote->description);
                            // echo "<pre>";print_r($stringLength);exit();
                            ?>
                        	<li>
                                <div class="quote-item-img">
                                    <img src="{{ asset('uploads/quote/').'/'.$quote->quote_image }}" alt="color-service">
                                </div>
                                <div class="quote-item-content">
                                    <h4 class="mb-2">{{@$quote->title}}</h4>
                                    <span class="body-large text-black d-inline-block mb-2">Zip Code : <span class="body-large text-bold text-black">{{@$quote->post_code}}</span></span>
                                    <div class="quote-item-content-main">
                                    <span class="body-large text-black d-inline-block">Material-categories : <span class="body-large text-bold text-black">{{@$quote->material_category_name}}</span></span>
                                    <span class="body-large text-black d-inline-block">Time Frame : <span class="body-large text-bold text-black">{{@$quote->time_frame_name}}</span></span>
                                    </div>
                                    <p class="body-large text-black mb-0">{{ Str::limit(@$quote->description, 200) }}
                                    @if($stringLength > 150)
                                    <a href="javascript:void(0)" class="red-text-link body-normal text-uppercase mb-0" data-bs-toggle="modal"  onclick="return readMoreModal('{{@$quote->id}}')">Read More</a>
                                    @endif
                                    </p>
                                </div>
                            </li>
                        	@endforeach
                            @else
                            <div class="col-lg-12 col-sm-12">
                            <h3 class="text-danger text-center">No data found</h3>
                        </div>
                        @endif
                        </ul>
                        {{ $myQuoteData->links('vendor.pagination.custom_pagination') }}                     
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="modal fade read-more-modal p-0" id="readMoreModal" tabindex="-1" aria-modal="true">
    
</div>

            <?php 
$categoryData = DB::table('categories')->where('status',1)->get();
$timeFrame = DB::table('time_frame')->where('status',1)->get();
$materialCategory = DB::table('material_category')->where('status',1)->get();
?>
<!-- Create A Quote -->
<div class="modal get-quote-modal fade show p-0" id="getquoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark text-black"></i></button>
            </div>
            <div class="modal-body">
                <!-- <p class="title-4 text-dark-blue popup-heading">Your Counter Offer</p> -->
                <h3 class="text-center">Get Quote</h3>
                <form action="" class="get-quote-form row" id="quoteForm">                    
                    <div class="form-group">
                        <label for="" class="body-extra-large">Select the Category <span class="valid_field">*</span></label>
                        <select name="category_id" id="category_id" >
                            <option value="">Select category</option>
                            @foreach($categoryData as $category)

                            <option value="{{$category->id}}">{{@$category->title}}</option>
                            @endforeach
                           
                        </select>                        
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="body-extra-large">Time Frame <span class="valid_field">*</span></label>
                        <select name="time_frame_id" id="time_frame_id" >
                            <option value="">Select time frame</option>
                            @foreach($timeFrame as $time)

                            <option value="{{$time->id}}">{{@$time->name}}</option>
                            @endforeach
                           
                        </select>                        
                    </div>
                   <div class="form-group col-sm-6">
                        <label for="" class="body-extra-large">Material Category <span class="valid_field">*</span></label>
                        <select name="material_id" id="material_id" >
                            <option value="">Select category</option>
                            @foreach($materialCategory as $material)

                            <option value="{{$material->id}}">{{@$material->name}}</option>
                            @endforeach
                           
                        </select>                        
                    </div>
                    <div class="form-group">
                        <label for="" class="body-extra-large">Enter the Zipcode <span class="valid_field">*</span></label>
                        <input type="text" name="post_code" value="{{old('post_code')}}" id="post_code" placeholder="Enter zipcode">
                        
                    </div>                                                          
                    <div class="form-group">
                        <label for="" class="body-extra-large">Enter Description <span class="valid_field">*</span></label>
                        
                        <textarea cols="5" rows="3" name="description" id="description"  placeholder="Enter description">{{old('description')}}</textarea>
                    </div>
                    <div class="form-group">
                        <div class="file-upload">
                            <div class="file-upload-select">
                                <div class="file-select-button"><i class="fa-solid fa-paperclip text-grey"></i></div>
                                <div class="file-select-name body-large">Attach the photo <span class="valid_field">*</span></div> 
                                <input type="file" name="quote_image" id="quote_image" accept="image/png, image/jpg, image/jpeg">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="common-btn hvr-radial-out w-100">Submit quote</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">
        
    	var test = $("#quoteForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    category_id: "required",
                    post_code: {
                        required :true,
                        minlength:4,
                        maxlength:10
                    },
                    // description: "required",
                     quote_image: "required",
                      time_frame_id: "required",
                    material_id: "required",
                    description: {
                        required :true,
                        minlength:25,
                        maxlength:255
                    },
                    // file-upload-input: "required",
                    
                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    category_id: " Category field is required",
                    post_code: {
                        required : " Zip Code field is required",
                        minlength : " Zip Code is not less than 4 characters",
                        maxlength : " Zip Code is not more than 10 characters"
                    },
                    // description: " Description field is required",
                    quote_image: " Quote Image field is required",
                    time_frame_id: " Time Frame field is required",
                    material_id: " Material Category field is required",
                    description: {
                        required : "Description field is required",
                        minlength:"Minimum 25 characters required",
                        maxlength : "Maximum 255 characters required"
                    },
                    // file-upload-input: " Quote Image field is required",
                   
                },
                submitHandler: function(){
                    var form_data = new FormData($('#quoteForm')[0]);
                    action_url = "{{ route('store-quote') }}";
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
                                    window.location.href = "{{ route('my-quote')}}";
                                }
                            },
                            
                        });
                }
            });

        function readMoreModal(id)
        {
            // alert('hello')
        action_url = "{{ route('view-more') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: {'id':id},
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            type: "POST",
                    
                             beforeSend: function(){
                                    $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
                            success: function (response) 
                            {
                                // console.log(response);
                                // return false;
                                // console.log('test'+response);
                                    $('.loader').css("visibility", "hidden");
                                   $(document).find("#readMoreModal").empty();
                                   
                                    $(document).find("#readMoreModal").append(response.html);
                                     $(document).find("#readMoreModal").modal('show');
                            },
                        });
        }
    </script>
@endsection