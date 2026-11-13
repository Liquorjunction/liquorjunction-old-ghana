<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentUserQuote($user_name,$sendname,$sendemail,$category_name,$post_code,$description,$image_name,$id);
?>
<div>{!!$content!!}</div>