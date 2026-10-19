<div class="modal-dialog">
                <div class="modal-content">
                    <!-- <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div> -->
                    <div class="modal-body">
                        <img src="{{asset('assets/frontend/images/icon-order-successfully-modal.svg')}}" alt="icon-order-successfully">
                        <h3>Order Successfully</h3>
                        <h6><span>Transaction Number:</span> {{@$trans_no}}</h6>
                        <h6><span>Order number :</span> {{@$uniqid}} </h6>
                        <p class="body-large">You Successfully place your order from {{@$store_name}} </p>
                        <p class="description">We have received your purchase order request</p>
                        <a href="{{route('frontend.home')}}" class="common-btn hvr-radial-out explore-more-btn">BACK TO HOME</a>
                        <p class="click-here-btn">To see your order <a href="{{route('myOrder')}}" class="red-text-link body-normal text-uppercase">Click here</a></p>
                    </div>
                </div>
        </div>
        <script type="text/javascript">
            $('#OrderSuccessfully').modal({
               backdrop: 'static',
               keyboard: false
            });
        </script>
