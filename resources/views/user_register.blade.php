<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentUserRegister($id,$email,$name,$url,$logo);
?>
<div>{!! $content !!}</div>
