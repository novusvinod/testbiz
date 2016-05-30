<?php    
 $access_key = "VE6GACEUVSVSL2MQRUF1"; //put your own access_key - found in admin panel     
 $secret_key = "ffbd50ef5d192fca74ef2fc44bcd7405f149a1f0"; //put your own secret_key - found in admin panel     
 $return_url = "https://taxivaxi.com/citrus/return.php"; //put your own return_url.php here.    
 $txn_id = time() . rand(10000,99999);    
 $value = $_GET["amount"]; //Charge amount is in INR by default    
 $data_string = "merchantAccessKey=" . $access_key
                ."&transactionId="  . $txn_id          
              . "&amount="         . $value;    
 $signature = hash_hmac('sha1', $data_string, $secret_key);    
 $amount = array('value' => $value, 'currency' => 'INR');    
 $bill = array('merchantTxnId' => $txn_id,      
               'amount' => $amount,        
               'requestSignature' => $signature,         
               'merchantAccessKey' => $access_key,        
               'returnUrl' => $return_url);     echo json_encode($bill);   ?>	