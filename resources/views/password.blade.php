<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentForgotpassword($id,$email,@$otp,$name,$url,$logo);

?>
<div>{!! $content !!}</div>

