@extends('frontEnd.layouts.new_app')
@section('title','Request Quote')
@section('content')
@include('sweetalert::alert')

  <main class="site-content">
     <div class="loader" id="loader"></div> 
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    <li><p class="text-black body-normal">Request Quote</p></li>
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
                    <h1>Request Quote</h1>
                        
                        <ul class="common-card quote-list-items mb-0">
                            @if($requestQuote->count() > 0)
                        	@foreach($requestQuote as $quote)
                            
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
                                    <div class="d-flex justify-content-between gap-2">
                                    <button type="submit" class="small-common-btn hvr-radial-out w-100 test_data" data-bs-toggle="modal" id="test_data" data-id={{@$quote->quote_id}}>Yes</button>
                                    <button type="submit" class="small-common-btn common-border-btn hvr-radial-out-black w-100 test_data_no" data-id={{@$quote->quote_id}}>No</button>
                                </div>
                                </div>
                            </li>
                        	@endforeach
                            @else
                            <div class="col-lg-12 col-sm-12">
                            <h3 class="text-danger text-center">No data found</h3>
                        </div>
                        @endif
                        </ul>
                        {{ $requestQuote->links('vendor.pagination.custom_pagination') }}                     
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="modal fade read-more-modal p-0" id="readMoreModal" tabindex="-1" aria-modal="true">
    
</div>

<div class="modal fade remove-item-modal p-0 show" id="requestModal" tabindex="-1" aria-modal="true">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    <input type="hidden" name="address_id" id="address_id" value="">
     <div class="modal-body">
        <h5 class="mb-3">Quote Approve</h5>
         <p class="body-large mb-4">Are you sure you want to approve ?</p>
          <div class="d-flex justify-content-between gap-2">
           <button type="submit" class="small-common-btn common-border-btn hvr-radial-out-black w-100" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
           <input type="hidden" name="quote_id" id="quote_id" value="">
            <button type="submit" class="small-common-btn hvr-radial-out w-100" onclick="return quoteApprove(this)">Yes</button>
             </div>
              </div>
               </div>
                </div>
            </div>
        </div>

        <div class="modal fade remove-item-modal p-0 show" id="requestModalNo" tabindex="-1" aria-modal="true">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    <input type="hidden" name="address_id" id="address_id" value="">
     <div class="modal-body">
        <h5 class="mb-3">Quote Reject</h5>
         <p class="body-large mb-4">Are you sure you want to reject ?</p>
          <div class="d-flex justify-content-between gap-2">
           <button type="submit" class="small-common-btn common-border-btn hvr-radial-out-black w-100" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
           <input type="hidden" name="quote_id" id="quote_id" value="">
            <button type="submit" class="small-common-btn hvr-radial-out w-100" onclick="return quoteReject(this)">Yes</button>
             </div>
              </div>
               </div>
                </div>
            </div>
        </div>

<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">

        $(".test_data").click(function(){
  // alert("The paragraph was clicked.");
            var quote_id = $(this).attr('data-id');
            // alert(quote_id);
            $("#quote_id").val(quote_id);
            $('#requestModal').modal('toggle');
            $('#requestModal').modal('show');

});

        $(".test_data_no").click(function(){
  // alert("The paragraph was clicked.");
            var quote_id = $(this).attr('data-id');
            // alert(quote_id);
            $("#quote_id").val(quote_id);
            $('#requestModalNo').modal('toggle');
            $('#requestModalNo').modal('show');

});

        function quoteApprove()
        {
            // e.preventDefault();
            var quote_id = $("#quote_id").val();
            // alert(quote_id)
            action_url = "{{ route('storeassignstore') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: {'quote_id':quote_id},
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
                                    window.location.href = "{{ route('request-quote')}}";
                                   // $(document).find("#readMoreModal").empty();
                                   
                                   //  $(document).find("#readMoreModal").append(response.html);
                                   //   $(document).find("#readMoreModal").modal('show');
                            },
                        });
        }

        function quoteReject()
        {
            var quote_id = $("#quote_id").val();
            // alert(quote_id)
            action_url = "{{ route('storerejectstore') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: {'quote_id':quote_id},
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
                                    window.location.href = "{{ route('request-quote')}}";
                                   // $(document).find("#readMoreModal").empty();
                                   
                                   //  $(document).find("#readMoreModal").append(response.html);
                                   //   $(document).find("#readMoreModal").modal('show');
                            },
                        });
        }

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