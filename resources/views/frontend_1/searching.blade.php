<?php
    $html = "";
    $i=0;
    foreach($categoryData as $result){
        if(\Session::get('language')==1) {
            $title = $result->title;
        }else{
            $title = ($result->title_fr)?$result->title_fr:$result->title;
        }
        $categoryroute = route('productlist',['id'=>Helper::encodeUrl($result->id)]);
        $html .= '<li data-index="'.$i.'"><a href="'.$categoryroute.'" ><span class="top-content">'.ucwords($title).'</span><span>'.@Helper::language('category').'</span></a> </li>';
        $i++;
    }

    foreach($subcategoryData as $result){
        if(\Session::get('language')==1) {
            $title = $result->title;
        }else{
            $title = ($result->title_fr)?$result->title_fr:$result->title;
        }
        $categoryroute = route('productlist',['id'=>Helper::encodeUrl($result->category_id).'?sid='.Helper::encodeUrl($result->id)]);
        $html .= '<li data-index="'.$i.'"><a href="'.$categoryroute.'" ><span class="top-content">'.ucwords($title).'</span><span>'.@Helper::language('subcategory').'</span></a> </li>';
        $i++;
    }

    foreach($productDataFilter as $result){
        $productroute = route('productdetails',['id'=>Helper::encodeUrl($result->id)]);
        if(\Session::get('language')==1) {
            $product_name = $result->product_name;
        }else{
            $product_name = ($result->product_name_fr)?$result->product_name_fr:$product_name;
        }
        $html .= '<li data-index="'.$i.'"><a href="'.$productroute.'"><span class="top-content">'.ucwords($product_name).'</span ><span>'.@Helper::language('product').'</span></a> </li>';
        $i++;
    }
    echo  $html ;
?>