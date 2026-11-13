<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentWholesalerRegister($email,$password,$fullname);

?>
<div>{!! $content !!}</div>

