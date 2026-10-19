@extends('frontend.layouts.app')
@section('title',Helper::language('track_your_order') )
@section('content')
@if(@$pageInfo->photo)
<section class="title-banner" style="{{ !empty($pageInfo->photo) ? 'background-image: url('.asset('uploads/cms/' . $pageInfo->photo).'); background-repeat: no-repeat; background-size: cover;' : '' }}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-0">{{ $pageInfo->page_name }}</h1>
            </div>
        </div>
    </div>
</section>
@endif
@include('frontend.cms.help_and_support.breadcrumb')  
@include('sweetalert::alert')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
   
<style>
    /* body {
        font-family: Arial, sans-serif;
        margin: 20px;
        text-align: center;
    } */

    .tracking-container {
        max-width: 800px;
        margin: auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        margin: 20px 0;
    }

    .progress-bar::before {
        content: "";
        position: absolute;
        width: 100%;
        height: 5px;
        background: #ddd;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        z-index: 1;
    }

    .progress-step {
        position: relative;
        z-index: 2;
        width: 50px;
        height: 50px;
        background: #ddd;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        transition: 0.3s;
    }

    .progress-step.active {
        background: #4CAF50;
        color: #fff;
    }

    .progress-bar .progress {
        position: absolute;
        height: 5px;
        background: #4CAF50;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        width: 0;
        z-index: 1;
        transition: width 0.5s ease-in-out;
    }

    .progress-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        font-size: 14px;
        color: #666;
    }

    .progress-labels div {
        display: flex;
        flex-direction: column;
        align-items: center; /* Centers the text */
    }

    .progress-date {
        margin-top: 4px; /* Adds space between the label and the date */
        font-size: 14px; /* Adjust size if needed */
        color: #555; /* Optional: Change text color */
    }

    .tracking-status {
        padding: 20px;
        background: #f3f3f3;
        border-radius: 5px;
        margin-top: 20px;
    }

    .tracking-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
    }

    .tracking-info div {
        text-align: left;
        flex: 1;
    }

    .tracking-info strong {
        display: block;
        font-weight: bold;
    }
</style>

<section class="contact pt-40 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <div class="contact-sidebar-wrapper">
                    <h3>{{@ucfirst(Helper::language('help_and_support'))}}</h3>
                    @include('frontend.cms.help_and_support.sidebar')
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="content-inner">
                    <h3>{{Helper::language('teack_your_order')}}</h3>
                    <form id="orderStatusForm" class="row">
                        @csrf
                        <div class="form-group col-12 col-lg-6">
                            <label for="">{{Helper::language('order_numbe_label')}}</label>
                            {{-- <input type="text" name="order_number" placeholder="{{Helper::language('enter_order_number')}}" value="{{old('order_number')}}"> --}}
                            <input type="text" id="order_number" name="order_number" placeholder="{{Helper::language('enter_order_number')}}" value="{{old('order_number')}}">

                            @if ($errors->has('order_number'))
                            <span class="help-block">
                                <span style="color: red;" class='validate'>{{ $errors->first('order_number') }}</span>
                            </span>
                            @endif
                        </div>
                        <div class="form-group col-12 col-lg-6"></div>
                        <div class="col-sm-6">
                            <button type="submit" class="solid-button w-100">{{Helper::language('check status')}}</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Section for track content --}}
            <div class="col-lg-5 col-md-5">
                    <div id="orderStatusContainer" class="tracking-container" style="display: block;">
                        <h2>Order #<span id="orderId"></span></h2>

                        <div class="progress-bar">
                            <div class="progress" style="width: 0%;"></div>
                            <div id="progressSteps" class="progress-steps"></div>
                        </div>

                        <div class="progress-labels">
                            <div id="progressLabels"></div>
                        </div>

                        <div class="tracking-status">
                            <h4>Status: <span id="currentStatus" style="color: #4CAF50;"></span></h4>
                            <div class="tracking-info">
                                <div>
                                    <strong>Shipping To:</strong>
                                    <p id="shippingTo"></p>
                                </div>
                                <div>
                                    <strong>Carrier:</strong>
                                    <p id="carrier"></p>
                                </div>
                                <div>
                                    <strong>Tracking Number:</strong>
                                    <p id="trackingNumber"></p>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</section>


@include('frontend.newsletter.newsletter')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('#orderStatusForm').on('submit', function (e) {
            e.preventDefault();
            const orderNumber = $('#order_number').val();
    
            // Clear any previous errors or content
            $('#order_number_error').text('');
            $('#orderStatusContainer').hide();

            console.log(orderNumber,"orderNumber")
    
            if (orderNumber.trim() === "") {
                $('#order_number_error').text('Order number is required');
                return;
            }
    
            $.ajax({
                url: '{{ route("checkOrderStatus") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_number: orderNumber
                },
                success: function (response) {
                    if (response.status === 'success') {
                        console.log(response,"response")
                        // Populate the tracking status container
                        $('#orderId').text(response.order_id);
                        $('#currentStatus').text(response.current_status);
                        $('#shippingTo').text(response.address);
                        $('#carrier').text(response.carrier);
                        $('#trackingNumber').text(response.tracking_number);
    
                        // Populate progress bar
                        const totalSteps = response.status_steps.length;
                        const currentStatusIndex = response.currentStatusIndex;
                        const progressPercentage = ((currentStatusIndex + 1) / totalSteps) * 100;
                        $('.progress').css('width', progressPercentage + '%');
    
                        // Create progress steps
                        $('#progressSteps').empty();
                        response.status_steps.forEach((step, index) => {
                            const stepClass = index <= currentStatusIndex ? 'active' : '';
                            $('#progressSteps').append(`<div class="progress-step ${stepClass}">${step}</div>`);
                        });
    
                        // Create progress labels
                        $('#progressLabels').empty();
                        response.status_steps.forEach((step, index) => {
                            $('#progressLabels').append(`<div><strong>${step}</strong></div>`);
                        });
    
                        $('#orderStatusContainer').show();
                    } else {
                        // Show error if the order number is invalid
                        alert(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('An error occurred. Please try again later.');
                }
            });
        });
    });
</script>

@endsection