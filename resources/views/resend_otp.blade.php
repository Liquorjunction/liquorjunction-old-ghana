<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentResendOtp($email,$name,$otp,$id,$logo,$from_email);
?>
<div>{!! $content !!}</div>
