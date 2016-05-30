<?php
//Need to replace the last part of URL("your-vanityUrlPart") with your Testing/Live URL
    $formPostUrl = "https://sandbox.citruspay.com/sslperf/taxivaxi";	
    //Need to change with your Secret Key
    $secret_key = "ffbd50ef5d192fca74ef2fc44bcd7405f149a1f0";	
             
    //Need to change with your Vanity URL Key from the citrus panel
    $vanityUrl = "taxivaxi";
	
    //Should be unique for every transaction
    $merchantTxnId = uniqid(); 
    //Need to change with your Order Amount
    $orderAmount = "1000";
    $currency = "INR";
    $data= $vanityUrl.$orderAmount.$merchantTxnId.$currency;
   

    $securitySignature = hash_hmac('sha1', $data, $secret_key);
	
	echo $securitySignature;
	
?>