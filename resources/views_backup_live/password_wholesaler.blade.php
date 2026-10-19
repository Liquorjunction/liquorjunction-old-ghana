<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentAdminForgotpassword($id,$email,@$password,$name,$url,$logo);

?>
<div>{!! $content !!}</div>

