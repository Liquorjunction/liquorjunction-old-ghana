<?php
use App\Helpers;

 $content = Helper::getSubscribeEmailContent($id,$email,$url,$logo);

?>
<div>{!! $content !!}</div>