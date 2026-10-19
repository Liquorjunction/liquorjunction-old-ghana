<div class="row" >
    @foreach($stores as $store)
    <div class="col-xl-4 col-lg-4 col-sm-6">
        <?php 
        // if ($store->in_store == 1) {
        $productList =  route('productlistview',['id'=>$store->id]);
            
        // }else{

        // $productList =  route('productlistview',['id'=>$store->id]);
        // }
        ?>
        <a href="{{$productList}}" class="hvr-float-shadow store-box">
            <span class="d-block store-image">
                @php($storeImage = ($store->profile)?'uploads/customer/'.$store->profile:"assets/frontend/images/store_image.png")
                <img src="{{ asset($storeImage) }}" alt="Store Image">
            </span>
            <h4 class="mb-0">{{ $store->store_name }}</h4>
        </a>
    </div>                    
    @endforeach                
</div>
@if($stores->total() > 0)
    <div class="mb-0">
     {{ $stores->links('vendor.pagination.custom_pagination') }}
    </div>
@endif