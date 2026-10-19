{{-- <?php
$html = '<div class="suggestion-grid">';
    $i = 0;
    foreach($productDataFilter as $result){
        $productroute = route('productdetails',['id'=>Helper::encodeUrl($result->id)]);
        if(\Session::get('language')==1) {
            $product_name = $result->product_name;
        }else{
            $product_name = ($result->product_name_fr) ? $result->product_name_fr : $product_name;
        }
        $html .= '<div class="suggestion-item" data-index="'.$i.'">
                    <a href="'.$productroute.'">
                        <div class="suggestion-title">'.ucwords($product_name).'</div>
                    </a>
                </div>';
        $i++;
    }
$html .= '</div>';
echo $html;
?> --}}

<?php
$html = '<div class="suggestion-grid">';
$i = 0;
foreach($productDataFilter as $result){
    $productroute = route('productdetails',['id'=>Helper::encodeUrl($result->id)]);
    
    if(\Session::get('language')==1) {
        $product_name = $result->product_name;
        $image_not_found = 'Image not available';
    } else {
        $product_name = ($result->product_name_fr) ? $result->product_name_fr : $product_name;
        $image_not_found = 'Image non disponible';

    }


    $image = asset('uploads/product/default.png'); // Default
    if (!empty($result->get_product_images) && $result->get_product_images->count() > 0) {
        $firstImage = $result->get_product_images->first();
        if ($firstImage && $firstImage->image && file_exists(public_path('uploads/product/' . $firstImage->image))) {
            $image = asset('uploads/product/' . $firstImage->image);
        }
    }

    $price = '';
    if (!empty($result->get_product_variants) && $result->get_product_variants->count() > 0) {
        $variant = $result->get_product_variants->first();
        // $final_price = ($variant->variant_discounted_price > 0)
        //                 ? $variant->variant_discounted_price
        //                 : $variant->variant_price;
        $final_price = $variant->variant_price;
        // $price = '<div class="suggestion-price">'.number_format($final_price, 2).'</div>';
        $price = '<div class="suggestion-price"><span style="font-weight: bold;">' . $settings->currency_symbol . number_format($final_price, 2) . '</span></div>';


    }
    
    $html .= '<div class="suggestion-item" data-index="'.$i.'">
                <a href="'.$productroute.'">
                    <div class="suggestion-img">
                        <img src="'.$image.'" alt="'.ucwords($product_name).'" />
                    </div>
                    <div class="suggestion-title">'.ucwords($product_name).'</div>
                    '.$price.'
                </a>
              </div>';
    $i++;
}
$html .= '</div>';

$html .= '<div class="suggestion-button-wrapper" style="text-align: center; margin-top: 20px;margin-bottom:20px;">
          <a href="' . route('filterproductlist') . '?search=' . urlencode($keyword) . '" class="solid-button fw-bold" style="font-size:0.9rem;padding: 10px 20px;">
                Show all ' . $productCount . ' products
            </a>
          </div>';


echo $html;
?>
