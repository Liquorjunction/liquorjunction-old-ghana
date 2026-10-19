<?php
use App\Helpers;

 $content = Helper::getEmailtemplateContentInviteLink($email,$supplier_id);

?>
<div>{!! $content !!}</div>

