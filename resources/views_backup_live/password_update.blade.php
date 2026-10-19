<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentUpdatepassword($email,$password,$name,$id,$logo,$from_email);

?>
<div>{!! $content !!}</div>

