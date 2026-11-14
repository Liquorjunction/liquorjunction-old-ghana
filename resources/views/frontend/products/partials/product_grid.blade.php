@if(!empty($productData) && count($productData) > 0)
    @php
        $last_id = '';
        $i = 0;
    @endphp
    @foreach ($productData as $key2 => $result)
        @php
            $product_title = $result->product_name;
            $image_not_found = 'Image not available';
            $product_image = $result->image;
            $product_unit = Helper::getUnitById(@$result->product_uof);
            $fav_data = Helper::userFavoriteProduct($result->product_id);

            // Pricing logic matching native theme
            $original_price = $result->product_price;
            $final_price = $original_price;

            if ($result->offer_status && !$result->bogo_status) {
                if ($result->offer_type === 'flat') {
                    $final_price = max(0, $original_price - $result->discount_amount);
                } elseif ($result->offer_type === 'percentage') {
                    $final_price = max(0, $original_price - ($original_price * $result->discount_amount / 100));
                }
            }

            // Cart check for both Guest (Session) and Logged-in User (DB)
            $gdisplay = 'none';
            $adisplay = 'flex';
            $available_qty = $result->available_qty ?? 0;
            $is_in_cart = false;
            $user_id = auth()->guard('user')->id();
            $product_id = $result->product_id;
            $variant_id = $result->id;

            $get_cart_array = Session::get('cart_info');

            if (isset($get_cart_array) && !empty($get_cart_array)) {           
                if (array_key_exists($product_id, $get_cart_array)) {
                    foreach ($get_cart_array as $key => $variant_array) {   
                        if ($key == $product_id && is_array($variant_array) && array_key_exists($variant_id, $variant_array)) {                       
                            $is_in_cart = true;
                            break;
                        }
                    }
                }
            }
            
            if (!$is_in_cart && $user_id) {
                $cartItem = DB::table('cart')
                    ->where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->where('product_variant_id', $variant_id)
                    ->where('status', 1)
                    ->first();

                if ($cartItem) {
                    $is_in_cart = true;
                }
            }

            if ($is_in_cart == true) {                                   
                $gdisplay = 'flex';
                $adisplay = 'none';
            }
        @endphp

        <div class="col-lg-4 col-md-6 col-sm-6 product-listing-col mb-4">
            <div class="bs-box">
                <div class="bs-image">
                    @if ($result->bogo_status)
                        <div class="mb-1 mt-1">
                            <span class="bogo" id="bogo">
                                {{ Helper::language('bogo') }}
                            </span>
                        </div>
                    @endif

                    @if ($result->offer_status && !$result->bogo_status)
                        <div class="mb-1 mt-1">
                            <span class="offer" id="offer">
                                @if ($result->offer_type === 'flat')
                                    Flat {{ intval($result->discount_amount) }} {{ Helper::Settings('currency_symbol') }} Off
                                @elseif ($result->offer_type === 'percentage')
                                    {{ intval($result->discount_amount) }}% Off
                                @endif
                            </span>
                        </div>
                    @endif

                    <a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->product_id)]) }}">
                        @if (!empty($product_image) && file_exists(public_path('uploads/product/' . $product_image)))
                            <img src="{{ asset('uploads/product/' . $product_image) }}" title="{{ $product_title }}" />
                        @else
                            <img src="{{ asset('assets/frontend/images/image-not-avilable.png') }}" title="{{ $image_not_found }}" alt="{{ $image_not_found }}">
                        @endif
                    </a>
                </div>

                <div class="bs-content">
                    <h6>
                        <a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->product_id)]) }}" class="heading-six">
                            {{ ucfirst($product_title) }}
                        </a>
                    </h6>

                    <span class="text-sm grey-text">
                        {{ !empty($result->product_size) ? $result->product_size . ' ' . $product_unit : '' }}
                    </span>

                    <div class="price-wrapper">
                        @if($final_price < $original_price)
                            <span class="sell-price">
                                {{ number_format($final_price, 2) }}{{ Helper::Settings('currency_symbol') }}
                            </span>
                            <span class="original-price">
                                {{ number_format($original_price, 2) }}{{ Helper::Settings('currency_symbol') }}
                            </span>
                        @else
                            <span class="sell-price">
                                {{ number_format($original_price, 2) }}{{ Helper::Settings('currency_symbol') }}
                            </span>
                        @endif
                    </div>

                    @if(!empty($result->average_rating))
                        <div class="product-rating">
                            <span class="text-sm black-text">{{ $result->average_rating }}</span>
                            <i class="icon-star-fill"></i>
                        </div>
                    @endif

                    @if ($available_qty > 0)
                        <a style="display: <?php echo $adisplay; ?>;" 
                           title="{{ Helper::language('add_to_cart') }}" 
                           data-product-id="{{ Helper::encodeUrl($result->product_id) }}" 
                           data-variant-id="{{ $result->id }}" 
                           data-bogo_status="{{ $result->bogo_status ? '1' : '0' }}" 
                           data-offer_status="{{ $result->offer_status ? '1' : '0' }}" 
                           class="add-bucket" 
                           href="javascript:void(0);">
                            <i class="icon-bucket"></i>
                        </a>
                    @endif

                    <input class="favs-icon checked_box" 
                           type="checkbox" 
                           id="fav-item{{ $key2 }}" 
                           value="{{ ($fav_data != '') ? '1' : '0' }}" 
                           {{ ($fav_data != '') ? 'checked' : '' }} 
                           onclick="return productFav({{ $result->product_id }}, {{ ($fav_data != '') ? '1' : '0' }})" />
                    <label class="favs-button" title="{{ Helper::language('add_to_favourite') }}" for="fav-item{{ $key2 }}"></label>
                </div>

                <div class="load_products_ids" data-id="{{ Helper::encodeUrl($result->product_id) }}"></div>
            </div>
        </div>
    @endforeach
@else
    <div class="col-12 py-5 text-center">
        <div class="empty-products-state py-4">
            <h4 class="text-dark fw-bold mb-2">{{ Helper::language('no_result_found') ?: 'No Products Found' }}</h4>
            <p class="text-muted mb-4" style="font-size: 14px;">Try clearing some filters or searching for something else.</p>
            <button type="button" class="solid-button" onclick="resetAllFacetedFilters();">
                Clear All Filters
            </button>
        </div>
    </div>
@endif
