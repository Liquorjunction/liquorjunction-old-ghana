<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentSubadminRegister($email,$password,$fullname);

?>
<div>{!! $content !!}</div>

