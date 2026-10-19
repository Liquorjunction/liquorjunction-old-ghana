
    <input type="hidden" name="user_id" id="user_id" value="{{@$user_id}}">
    @if(!empty($productData) && count($productData) > 0)
    @php
        $last_id = '';
        $i=0
    @endphp
    @foreach ($productData as $key2 => $result )
    @php
        $product_title='';
        if(session()->get('language')==1){
            $product_title = $result->product_name;
            $image_not_found = 'Image not available';
        }else{
            $product_title = $result->product_name_fr;
            $image_not_found = 'Image non disponible';
        }   
        $product_image = $result->get_product_images->first();
        $product_variant = $result->low_price_product;              
        $product_unit = Helper::getUnitById(@$product_variant->variant_uof);       
        $fav_data = Helper::userFavoriteProduct($result->id);    
    @endphp
    <div class="col-lg-3 col-md-4 col-sm-6 product-listing-col">
        <div class="bs-box">
            <div class="bs-image">
                <a href="{{route('productdetails',['id'=>Helper::encodeUrl($result->id)])}}" >
                    @if (file_exists(public_path() . '/uploads/product/'.$product_image->image))					
                    <img src="{{ asset('uploads/product/'.$product_image->image) }}" title="{{$product_title}}" />
                    @else
                    <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{$image_not_found}}" alt="{{$image_not_found}}">
                    @endif 
                </a>                                    
            </div>
            <div class="bs-content">
                <h6><a href="{{route('productdetails',['id'=>Helper::encodeUrl($result->id)])}}" class="heading-six">{{@ucfirst($product_title)?: ''}}</a></h6>
                <span class="text-sm grey-text">{{@$product_variant->variant_size? $product_variant->variant_size.' '.$product_unit :''}}</span>
                <div class="price-wrapper">
                    {{-- <span class="sell-price">{{@$product_variant->variant_discounted_price? $product_variant->variant_discounted_price.Helper::Settings('currency_symbol') :''}}</span>
                    <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.' '.Helper::Settings('currency_symbol'):''}}</span> --}}

                    @if($product_variant->variant_discounted_price=='' || $product_variant->variant_discounted_price==0)
                        <span class="sell-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                    @else
                        <span class="sell-price"> {{@$product_variant->variant_discounted_price?    $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                        <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>
                    @endif
                </div>
                @if(!empty($result->average_rating))
                    <div class="product-rating">
                        <span class="text-sm black-text">{{@$result->average_rating?: ''}}</span>
                        <i class="icon-star-fill"></i>
                    </div>
                    @endif
                <a title="{{Helper::language('add_to_cart')}}" href="{{route('productdetails',['id'=>Helper::encodeUrl($result->id)])}}" class="add-bucket"><i class="icon-bucket"></i></a>
                <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
            </div>                          
            <input class="fav-icon checked_box" type="checkbox" id="fav-item{{$key2}}" value="{{ ($fav_data != "") ? '1' : "0" }}" {{ ($fav_data != "") ? 'checked' : "" }} onclick="return productFav({{$result->id}},{{ ($fav_data != "") ? '1' : "0" }})" />
            <label  class="fav-button" title="{{Helper::language('add_to_favourite')}}" for="fav-item{{$key2}}"></label>
            <div class="load_products_ids"  data-id="{{Helper::encodeUrl($result->id)}}" >  </div>
        </div>
    </div>
    @php
        $i++;
    @endphp
    @if(count($productData)==$i) 
    <input type="hidden" @if(!empty($product_last_id)) value="{{$product_last_id}}" @else value="{{$result->id}}" @endif id="last-id" >
    @php
        if(!empty($product_last_id)) {
            $pd_id = $product_last_id;
        }else{
            $pd_id = $result->id;
        }
    @endphp
    <div id="appen-html{{$pd_id}}" ></div>
    @endif
    @endforeach
    @else
    <div class="col-lg-12 col-md-12 col-sm-12 product-listing-col no-result-found">
        {{-- <div class="bs-box"> --}}
            <h4 class="text-danger text-center no-result-found">{{Helper::language('no_result_found')}}</h4>
        {{-- </div> --}}
    </div>
    @endif          

<div>
    <input type="hidden" id="proFcount" value="{{@$showing_product_count?:''}}">
    <input type="hidden" id="proTcount" value="{{@$total_product_count?:''}}">
</div>
