<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentUserRegister($email,$name,$id);
?>
<div>{!! $content !!}</div>
