 <?php
$price_filter = Session::get('price_filter');
$category_id = Session::get('category_id');
// echo "User:<pre>";print_r($user_id);exit();
 ?>
 @if(!empty($user_id))
                        <div class="sort-by">
                            <form action="">
                                <select name="price_filter" id="price_filter" onclick="return PriceFilter(this)">
                                <span class="heading-six mb-0">Sort by :</span>
                                    <option {{ ($price_filter == 1) ? 'selected' : "" }} value="1">Price: High to Low</option>
                                    <option {{ ($price_filter == 2) ? 'selected' : "" }} value="2">Price: Low to High</option>
                                </select>
                            </form>
                        </div>
                        @endif
                        <div class="row" id="">
    @if($category_data->count())
        @foreach($category_data as $key2=>$product)
        <?php 
                                $fav_data = DB::table('favorite_product')->where('user_id',$user_id)->where('product_id',$product->id)->where('status',1)->first();
                                // echo "<pre>";print_r($fav_data);exit();
                                ?>
                                <input type="hidden" name="category_id" id="category_id" value="{{@$category_id}}">
                                <input type="hidden" name="user_id" id="user_id" value="{{@$user_id}}">
                                    <div class="col-lg-4 col-sm-6">
                                <a href="{{route('productdetails',['id'=>$product->id])}}" class="product-box">
                                    <div class="img-box">
                                        <!-- <img src="../../assets/frontend/images/product_solar_panel.png" alt="solar panel"/> -->
                                <img src="{{ asset('uploads/product/').'/'.$product->product_image }}" alt="Category Image">
                                    </div>
                                    <div class="detail-box">
                                        <h5 class="mb-2">{{@$product->product_name}}</h5>
                                        <p>{{@$product->short_description}}</p>
                                         @if(!empty($user_id))
                                         <h6 class="mb-0 text-black">{{@$setting->currency_symbol}}{{ isset($product->discount_price) ? $product->discount_price : $product->retail_price}}<span class="d-inline-block body-normal disable-price">{{@$setting->currency_symbol}}{{@$product->retail_price}}</span></h6>
                                         @else
                                         <!-- <h6 class="mb-0 text-red">Login For Price</h6> -->
                                         <button class="text-red small-common-btn hvr-radial-out" onclick="return LoginForPrice()">Login For Price</button>
                                         @endif
                                    </div>
                                    <input class="fav-icon" type="checkbox" id="fav-item{{$key2}}" value="{{ ($fav_data != "") ? '1' : "0" }}" {{ ($fav_data != "") ? 'checked' : "" }} {{ ($fav_data != "") ? 'checked' : "" }} onclick="return productFav({{$product->id}},{{ ($fav_data != "") ? '1' : "0" }})"/>
                                    <label class="fav-button" for="fav-item{{$key2}}"></label>
                                </a>
                            </div>
                                @endforeach
    @else
    <div class="text-center text-bold">
                                <h3>No Product Found</h3>
                            </div>
    @endif
    {{ $category_data->links('vendor.pagination.custom_pagination') }}
</div>

                           
               
                        