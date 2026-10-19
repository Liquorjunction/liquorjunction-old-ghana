@extends('frontEnd.layouts.new_app')
@section('title','Assign Quote')
@section('content')
@include('sweetalert::alert')

  <main class="site-content">
     <div class="loader" id="loader"></div> 
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    <li><p class="text-black body-normal">Assign Quote</p></li>
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
                        <h1>Assigned Quote</h1>
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
                                    <div class="quote-item-content-main">
                                    <span class="body-large text-black d-inline-block">Customer Name : <span class="body-large text-bold text-black">{{@$quote->first_name}} {{@$quote->last_name}}</span></span>
                                    <span class="body-large text-black d-inline-block">Email : <span class="body-large text-bold text-black">{{@$quote->email}}</span></span>
                                    <span class="body-large text-black d-inline-block">Phone : <span class="body-large text-bold text-black">{{@$quote->phone}}</span></span>
                                    </div>
                                    <p class="body-large text-black mb-0">{{ Str::limit(@$quote->description, 200) }}
                                    @if($stringLength > 200)
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

<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">

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