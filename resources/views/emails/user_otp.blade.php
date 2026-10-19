<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentUserOtp($email,$name,$otp,$id);
?>
<div>{!! $content !!}</div>
