<?php
use App\Helpers;

$content = Helper::getEmailtemplateContentSendOffer(get_defined_vars());

?>
<div>{!! $content !!}</div>

