<?php
use App\Helpers;

 $content = Helper::studentRegisterEmail($id,$email,$name,$logo,$schoolname,$schoolcode,$address,$program);
?>
<div>{!! $content !!}</div>
