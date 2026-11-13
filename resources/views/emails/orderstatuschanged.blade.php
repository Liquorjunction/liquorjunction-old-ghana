<?php
use App\Helpers;
$order_status = ($order_status)?$order_status:'';
 $content = Helper::getEmailtemplateContentOrderStatusChanged($user_name,$sendname,$sendemail,$order, $order_status,$id);
//  dd($content);exit;
?>
<div>{!!$content!!}</div>