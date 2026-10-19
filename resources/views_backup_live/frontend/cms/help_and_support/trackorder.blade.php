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

        .sticky .site-content {
            padding-top: 142px !important;
        }
        
         h2{
            text-align: center;
            display: block;
            font-size: 1.7em;
            font-weight: bold;
        }

        .progress-step.active .fas {
            color: white; 
        }

        .tracking-container {
            margin: auto;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            position: relative;
            height: 4px;
            background-color: #e0e0e0;
            margin: 40px auto 0;
            border-radius: 2px;
            width: 100%;
            z-index: 1;
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
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }

        .progress-labels div {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .progress-date {
            margin-top: 4px; 
            font-size: 14px; 
            color: #555; 
        }

        .tracking-status {
            padding: 20px;
            background: #f3f3f3;
            border-radius: 5px;
            margin-top: 55px;
        }

        .tracking-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 10px;
        }

        .tracking-info div {
            text-align: left;
            flex: 1;
        }

        .tracking-info strong {
            display: block;
            font-weight: bold;
        }

        h4 {
            display: block;
            font-weight: bold;
            unicode-bidi: isolate;
            margin-left: 15px;
        }

        p{
            display: block;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            unicode-bidi: isolate;
            font-family: 'Roboto';
        }

        h3{
            font-size: 20px;
        }

        .progress-container {
            position: relative;
            margin: 30px 0;
        }

        .progress-bar-bg {
            width: 100%;
            height: 5px;
            background-color: #ddd;
            position: relative;
        }

        #progress-fill {
            height: 100%;
            background-color: #28a745;
            border-radius: 2px;
            transition: width 0.4s ease-in-out;
            width: 0%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        #progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: absolute;
            width: 100%;
            top: -18px; 
            z-index: 3;
        }

        .step {
            text-align: center;
            color: #aaa;
        }

        .step .icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 5px;
            background-color: #e0e0e0;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .step.active .icon {
            background-color: #28a745;
        }
        .step.active .icon .fas {
            color: #fff;
        }

        .step.current:not(.active) .icon {
            background-color: #28a745;
        }
        .step.current:not(.active) .icon .fas {
            color: #fff;
        }

        .step .icon .fas {
            color: black;
        }


        .progress-steps .step .label {
            margin-top: 5px;
            font-size: 15px;
        }


        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            background: transparent;
            border: none;
            font-size: 30px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            z-index: 10;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: #fbb516;
        }

        .progress-container.cancelled .step.active .icon,
        .progress-container.cancelled .step.current:not(.active) .icon {
            background-color: #cd0c0c !important;
        }

        .progress-container.cancelled #progress-fill {
            background-color: #cd0c0c !important;
        }


        @media only screen and (max-width: 500px) {
            .tracking-info {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 5px 15px;
                    margin-left: 0px;
            }

            .progress-steps .step .label {
                font-size: 10px;
            }

            h2 {
                font-size: 0.9em;
            }


    }
</style>

<section class="contact pt-40 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="contact-sidebar-wrapper">
                    <h3>{{@ucfirst(Helper::language('help_and_support'))}}</h3>
                    @include('frontend.cms.help_and_support.sidebar')
                </div>
            </div>
            <div class="col-lg-9 col-md-8">
                <div class="content-inner">
                    <h3>{{Helper::language('teack_your_order')}}</h3>
                    <form id="orderStatusForm" class="row">
                        @csrf
                        <div class="form-group col-12 col-lg-6">
                            <label for="">{{Helper::language('order_numbe_label')}}</label>
                            <input type="text" id="order_number" name="order_number" placeholder="{{Helper::language('enter_order_number')}}" value="{{old('order_number')}}">
                            <span id="order-error" class='red-text'></span>

                        </div>
                        <div class="col-12 col-lg-6" style="margin-top:33px;">
                            <button type="submit" class="solid-button w-100">{{Helper::language('check status')}}</button>
                        </div>

                        {{-- Track Content --}}
                        <div class="col-l2 d-block mt-3">
                            <div id="orderStatusContainer" class="tracking-container" style="display: none; position: relative;">
                                <button type="button" class="close-btn" id="closeTracking">&times;</button>
                                <h2>Order #<span id="orderId"></span></h2>
                                <div class="progress-container">
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" id="progress-fill"></div>
                                    </div>
                                    <div class="progress-steps" id="progress-steps">
                                      
                                    </div>
                                </div>
                            
                                <div class="tracking-status">
                                    <h4>Status: <span id="stats"></span></h4>
                                    <div class="tracking-info">
                                        <div>
                                            <strong>Location:</strong>
                                            <p id="shippingTo"></p>
                                        </div>
                                        {{-- <div>
                                            <strong>Expected Delivery:</strong>
                                            <p>Friday,September 2025</p>
                                        </div> --}}
                                        <div>
                                            <strong>Order Details:</strong>
                                            <div id="orderDetailsContent">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="error-container">
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


@include('frontend.newsletter.newsletter')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    function renderOrderProgress(statusSteps, currentIndex,iconClasses) {
        const progressSteps = document.getElementById('progress-steps');
        const progressFill = document.getElementById('progress-fill');

        // Clear previous steps
        progressSteps.innerHTML = '';

        // Build steps
        statusSteps.forEach((step, index) => {
            let stepClass = '';
            if (index < currentIndex) stepClass = 'active';          // completed steps
            else if (index === currentIndex) stepClass = 'current';  // current step
    

            const stepDiv = document.createElement('div');
            stepDiv.className = 'step ' + stepClass;

            stepDiv.innerHTML = `
                <div class="icon"><i class="fas ${iconClasses[index] || 'fa-circle'}"></i></div>
                <div class="label text-dark">${step}</div>
            `;
            progressSteps.appendChild(stepDiv);
        });


        // Set progress bar fill width
        const percent = (currentIndex / (statusSteps.length - 1)) * 100;
        progressFill.style.width = percent + '%';
    }

    function renderOrderProgress(statusSteps, currentIndex, iconClasses) {
        const progressSteps = document.getElementById('progress-steps');
        const progressFill = document.getElementById('progress-fill');
        const progressContainer = document.querySelector('.progress-container');

        // Reset previous content
        progressSteps.innerHTML = '';
        progressFill.classList.remove('cancelled-bar');

        // Check if the current status is 'Cancelled'
        const isCancelled = statusSteps[currentIndex]?.toLowerCase() === 'cancelled';

        // Add the cancelled class to the progress container if it’s cancelled
        if (isCancelled) {
            progressContainer.classList.add('cancelled');
        } else {
            progressContainer.classList.remove('cancelled');
        }

        // Calculate and set the progress bar percentage
        const percent = (currentIndex / (statusSteps.length - 1)) * 100;
        progressFill.style.width = percent + '%';

        // Loop through all the steps and render them
        statusSteps.forEach((step, index) => {
            let stepClass = 'step';

            // Set active/current classes
            if (index < currentIndex) stepClass += ' active';
            else if (index === currentIndex) stepClass += ' current';

            // Append the step element
            const stepDiv = document.createElement('div');
            stepDiv.className = stepClass.trim();

            // Add icon and label
            stepDiv.innerHTML = `
                <div class="icon"><i class="fas ${iconClasses[index] || 'fa-circle'}"></i></div>
                <div class="label text-dark">${step}</div>
            `;
            progressSteps.appendChild(stepDiv);
        });
    }



    $(document).ready(function () {
        $('#orderStatusForm').on('submit', function (e) {
            e.preventDefault();
            const orderNumber = $('#order_number').val();
    
            $('#order-error').text('');
            $('#orderStatusContainer').hide();
    
            if (orderNumber.trim() === "") {
                $('#order-error').text('Order number is required');
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
                        console.log(response)
                        $('#orderId').text(response.order_id);
                        $('#currentStatus').text(response.current_status);
                        $('#stats').text(response.current_status);
                        if (response.current_status.toLowerCase() === 'cancelled') {
                            $('#stats').css('color', '#cd0c0c');
                        } else {
                            $('#stats').css('color', '#4CAF50');
                        }

                        // $('#shippingTo').text(response.shipping_address);

                        if(response.order_num==2 || response.order_num==3)
                        {
                            $('#shippingTo').text(response.address);
                        }
                        else{
                            $('#shippingTo').text(response.shipping_address);    
                        }


                        const orderDate = new Date(response.order_date);
                        const formattedDate = orderDate.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                        });


                        $('#orderDetailsContent').html(`
                            <p><strong>Order Type:</strong> ${response.order_type}</p>
                            <p><strong>Amount:</strong> ${response.amount}GH₵</p>
                            <p><strong>Order Date:</strong> ${formattedDate}</p>
                            <p><strong>Payment Method:</strong> ${response.payment_type}</p>
                        `);


    
                        const totalSteps = response.status_steps.length;
                        const currentStatusIndex = response.currentStatusIndex;
                        const progressPercentage = ((currentStatusIndex + 1) / totalSteps) * 100;
                        $('.progress').css('width', progressPercentage + '%');
    
                        renderOrderProgress(response.status_steps, response.currentStatusIndex,response.icon_classes);
    
                        $('#orderStatusContainer').show();
                         $('#error-container').hide()
                    } else {
                        $('#error-container').html(`
                                <p style="color:red;"><strong>Error:-${response.message}</strong></p>
                        `).show();

                        let check=setTimeout(() => {
                            $('#error-container').hide();
                        }, 2000);
                        }
                },
                error: function (xhr, status, error) {
                    $('#error-container').html(`
                                <p style="color:red;"><strong>Error:-An error occurred. Please try again later.</strong></p>
                    `).show();

                    let check=setTimeout(() => {
                        $('#error-container').hide();
                    }, 2000);
                   
                }
            });
        });
    });
</script>

<script>
    $('#closeTracking').on('click', function () {
    $('#orderStatusContainer').hide(); 
    $('#order_number').val('');
});
</script>


@endsection