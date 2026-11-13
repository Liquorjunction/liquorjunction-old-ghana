<?php

// This will be your Callback Verification Token you can obtain from the dashboard.
// Make sure to keep this confidential and not to reveal to anyone.
// This token will be used to verify the origin of request validity is really from Xendit

$xenditXCallbackToken = 'XN7lE0Gs4GebswvoChJen5Bg8u2ROXacHNtpXOO5O9eBfu39';

// This section is to get the callback Token from the header request, 
// which will then later to be compared with our xendit callback verification token
$reqHeaders = getallheaders();

$xIncomingCallbackTokenHeader = isset($reqHeaders['x-callback-token']) ? $reqHeaders['x-callback-token'] : "";

// In order to ensure the request is coming from xendit
// You must compare the incoming token is equal with your xendit callback verification token
// This is to ensure the request is coming from Xendit and not from any other third party.
if($xenditXCallbackToken){
  // Incoming Request is verified coming from Xendit
  // You can then perform your checking and do the necessary, 
  // such as update your invoice records
    
  // This line is to obtain all request input in a raw text json format
  $rawRequestInput = file_get_contents("php://input");
  // This line is to format the raw input into associative array
  $arrRequestInput = json_decode($rawRequestInput, true);
  print_r($arrRequestInput);
  
  $_id = $arrRequestInput['id'];
  $_externalId = $arrRequestInput['external_id'];
  $_userId = $arrRequestInput['user_id'];
  $_status = $arrRequestInput['status'];
  $_paidAmount = $arrRequestInput['paid_amount'];
  $_paidAt = $arrRequestInput['paid_at'];
  $_paymentChannel = $arrRequestInput['payment_channel'];
  $_paymentDestination = $arrRequestInput['payment_destination'];

  // You can then retrieve the information from the object array and use it for your application requirement checking
    
}else{
  // Request is not from xendit, reject and throw http status forbidden
  http_response_code(403);
}