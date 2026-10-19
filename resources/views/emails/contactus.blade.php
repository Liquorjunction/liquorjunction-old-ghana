<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentContactUs($name,$email,$phone,$id,$from_email,$logo);
?>
<div>{!! $content !!}</div>
