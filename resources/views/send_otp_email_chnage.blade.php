<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentUserSendotpemailchnage($email,$name,$otp,$id,$logo,$from_email);
?>
<div>{!! $content !!}</div>
