<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentForgotpassword_api($email,$link,$name,$id,$logo,$from_email);
?>
<div>{!! $content !!}</div>
