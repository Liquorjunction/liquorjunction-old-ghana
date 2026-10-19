<?php
use App\Helpers;

 $content = Helper::getInstructorRequestContent($id,$email,$name,$url,$logo);

?>
<div>{!! $content !!}</div>

