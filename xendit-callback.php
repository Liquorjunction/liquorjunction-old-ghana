<?php

function logReq() 
{
	file_put_contents('xendit.log', file_get_contents('php://input'));

	$fp = fopen('xendit.log', 'a');

	$data = file_get_contents('php://input');

	$json_string = json_encode(json_decode($data), JSON_PRETTY_PRINT);

	fwrite($fp, $json_string);

	fwrite($fp, "\n");

	fclose($fp);
}

$xenditCallbackToken = 'XN7lE0Gs4GebswvoChJen5Bg8u2ROXacHNtpXOO5O9eBfu39';

$reqHeaders = getallheaders();

$xIncomingCallbackTokenHeader = isset($reqHeaders['X-CALLBACK-TOKEN']) ? $reqHeaders['X-CALLBACK-TOKEN'] : '';

if($xIncomingCallbackTokenHeader == $xenditCallbackToken)
{
	logReq();

	$rowRequestInput = file_get_contents('php://input');

	$arrRequestInput = json_decode($rowRequestInput, true);

	echo "Ok, we got your callback and printed here : ";

	print_r($arrRequestInput);
}
else
{
	http_response_code(403);
}
	
?>