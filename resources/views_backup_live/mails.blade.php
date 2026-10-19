<?php
use App\Helpers;

//$content=(new App\Helpers)->getEmailtemplateContent($id,$email,$password,$name,$url,$logo);
 $content = Helper::getEmailtemplateContent($id,$email,$password,$name,$url,$logo);
?>
<div>{!! $content !!}</div>

