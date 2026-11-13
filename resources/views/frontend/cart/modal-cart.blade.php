@if($cartItemCount > 0)
<ul class="minicart-list">
                @foreach($cartData as $key=>$cart)
                <li class="minicart-product">
                    <div class="mini-product-list">
                        <div class="product-img">
                            <a href="{{route('productdetails',['id'=>$cart->product_id])}}">
                                 <img src="{{ asset('uploads/product/').'/'.$cart->product_image }}" alt="product img">
                            </a>
                        </div>
                        <div class="product-detail">
                            <a href="#" class="heading-six mb-0">{{@$cart->product_name}}</a>
                            <span class="d-block mb-0">Sold by : <a href="{{route('productlistview',['id'=>$cart->store_id])}}">{{@$cart->store_name}}</a></span>
                            <label class="body-normal text-dark-grey d-block mb-1"></label>
                            <label class="heading-four mb-0 me-1">{{@$setting->currency_symbol}}{{isset($cart->offer_price) ? $cart->offer_price : $cart->product_price}}</label>
                            <span class="heading-six pb-0 mb-0 price-disable">{{@$setting->currency_symbol}}{{@$cart->product_price}}</span>                        
                        </div>
                    </div>
                    <div class="mini-product-bottom">
                         <span class="counter mb-0">
                                                <input class="counter__input" type="text" value="{{isset($cart->quantity) ? $cart->quantity : '1'}}" name="counter" size="5" readonly="readonly"/>
                                                <a class="counter__increment" onclick="return incrementModalCart({{$cart->product_id}})" href="javascript:void(0)"><i class="fa-solid fa-plus"></i></a>
                                                <a class="counter__decrement" onclick="return decrementModalCart({{$cart->product_id}})" href="javascript:void(0)"><i class="fa-solid fa-minus"></i></a>
                                            </span>    
                        <a href="#" class="red-text-link body-normal text-uppercase" onclick="return removeModalCart({{$cart->product_id}})">Remove</a>     
                    </div>
                </li>
                @endforeach
            </ul>
            @if($cartItemCount > 0)
            <div class="minicart-bottom">
                <h5 class="text-center">Subtotal<span class="body-large mb-0">({{@$cartItemCount}} item)</span>{{@$setting->currency_symbol}}{{@$totalCartAmount}}</h5>
                <a href="{{route('cart')}}" class="border-btn hvr-radial-out-border">View cart</a>
                <a href="{{route('checkout')}}" class="common-btn hvr-radial-out mb-0">Go To checkout</a>
            </div>
            @endif
            @else
                <div class="empty-cart">
                <img src="{{ asset('assets/frontend/images/icon_cart.svg') }}" class="cart-img hithere" alt="cart">
                <span class="d-block heading-four mb-0">Oops..</span>
                <span class="d-block heading-four mb-1">The cart is empty.</span>
                <p class="d-block body-normal">Please add your product you like </p>
                <a href="{{ route('frontend.home') }}" class="common-btn hvr-radial-out mb-0">Back To Shop</a>
            </div>
            @endif