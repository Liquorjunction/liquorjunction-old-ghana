<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentContactus($id,$name,$email,$phone,$msg,$url,$logo);
?>
<div>{!! $content !!}</div>
