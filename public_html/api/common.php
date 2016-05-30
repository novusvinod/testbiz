<?php

define("GOOGLE_API_KEY", "AIzaSyCbRQOb3dL3NgyhEOO5b-iJcCWVjC2_1HQ");
define("OLA_X_APP_TOKEN" , "9a4232d84f4544e0bb5383baa5926198");
//define("OLA_ACCESS_TOKEN" , "Bearer 718b5f41e86544248a10873c8ae652d2");  //Corporate.oe@taxivaxi
//define("OLA_ACCESS_TOKEN" , "Bearer 56d44285271a4653a6f8ea33db535cc3");  //Corporate.oe@taxivaxi
//define("OLA_ACCESS_TOKEN" , "Bearer 6030eac710e9420f997826dbbb074fa0");  //Neeraj
//define("OLA_ACCESS_TOKEN" , "Bearer 1146c331c6a0457194f818e16ec785fb");  //Neeraj@taxivaxi
//define("OLA_ACCESS_TOKEN" , "Bearer ef8f4ae8114b4aaba4f2e1e9d584a3c0");  //Accounts.oe@taxivaxi
//define("OLA_ACCESS_TOKEN" , "Bearer a692de8808944fb28cd8cfc4b3e31640"); //Ankit
define("OLA_ACCESS_TOKEN" , "Bearer 05970a61875d4641a82f326f0d61d8e9"); //Vinod
//define("OLA_ACCESS_TOKEN" , "Bearer 84f7341ae71440a79a420fc399270bda"); //Vinod Novus
//define("OLA_ACCESS_TOKEN" , "Bearer 2115b336a8eb43a1a640350b44b89e73"); //7291995226 (Neeraj)


define("TAXIVAXI_NUMBER", "7291995227, 9990045853, 9990045953");
define("TAXIVAXI_EMAIL", "corporate.oe@taxivaxi.com");

function base64_to_image($base64_string, $output_file) {
    $ifp = fopen($output_file, "wb"); 
    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($data[1])); 
    fclose($ifp); 

    return $output_file; 
}

function getLatLong($address)
{
	$address = str_replace(" ", "+", $address);
	$opts = array(
 					'http'=>array(
  					'header' => 'Connection: close'
 					)
				);
	$context = stream_context_create($opts);
	$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false",false,$context);
    $json = json_decode($json);

    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
    $latlong = array('lat' => $lat, 'long' => $long);
    return $latlong;
}

function olaRadioEstimates($pickup_lat, $pickup_lng, $drop_lat, $drop_lng)
{
	$opts = array(
 					'http'=>array(
 						'method' => 'GET',
  						'header' => ['X-APP-TOKEN:'.OLA_X_APP_TOKEN,
  									'Authorization:'.OLA_ACCESS_TOKEN]
 					)
				);
	$context = stream_context_create($opts);
	$json = file_get_contents("https://devapi.olacabs.com/v1/products?pickup_lat=28.6136199&pickup_lng=77.2309209&drop_lat=28.6979758&drop_lng=77.1689511",false,$context);
    $json = json_decode($json);	

    return $json;
}

function olaMakeBooking($pickup_lat, $pickup_lng, $pickup_mode, $category)
{
	$opts = array(
 					'http'=>array(
 						'method' => 'GET',
  						'header' => ['X-APP-TOKEN:'.OLA_X_APP_TOKEN,
  									'Authorization:'.OLA_ACCESS_TOKEN]
 					)
				);
	$context = stream_context_create($opts);
	$json = file_get_contents("https://devapi.olacabs.com/v1/bookings/create?pickup_lat=$pickup_lat&pickup_lng=$pickup_lng&pickup_mode=$pickup_mode&category=$category",false,$context);
    $json = json_decode($json);

    //Error
    $status='';
    $message='';

    //Success
    $crn='';
    $driver_name = '';
    $driver_number='';
    $cab_type='';
    $cab_number='';
    $car_model='';
    $eta='';

    //When error occurs
    if($json->{'status'})
    {
    	$status = $json->{'status'};
    	$message = $json->{'message'};
    }
    else
    {
    	$crn 			= $json->{'crn'};
	    $driver_name 	= $json->{'driver_name'};
	    $driver_number 	= $json->{'driver_number'};
	    $cab_type 		= $json->{'cab_type'};
	    $cab_number 	= $json->{'cab_number'};
	    $car_model 		= $json->{'car_model'};
	    $eta 			= $json->{'eta'};
    }
   
   $result = array('status' => $status, 'message' => $message, 
   					'crn' => $crn, 'driver_name' => $driver_name, 'driver_number' => $driver_number,
   					'cab_type' => $cab_type, 'cab_number' => $cab_number, 'car_model' => $car_model,'eta' => $eta);
   return $result;

}

function olaCancelBooking($crn)
{
	$opts = array(
 					'http'=>array(
 						'method' => 'GET',
  						'header' => ['X-APP-TOKEN:'.OLA_X_APP_TOKEN,
  									'Authorization:'.OLA_ACCESS_TOKEN]
 					)
				);
	$context = stream_context_create($opts);
	$json = file_get_contents("https://devapi.olacabs.com/v1/bookings/cancel?crn=$crn",false,$context);
    $json = json_decode($json);

    $status='';
    $message='';

    //For Success
    if($json->{'status'} == 'SUCCESS')
    {
    	$status = '1';
    	$message = $json->{'text'};
    }
    else
    {	$status = '0';
    	if($json->{'status'} == 'FAILURE')
	    {
	    	$message = $json->{'text'};
	    }
	    else
	    {
	    	$message = $json->{'message'}." ".$json->{'code'};	
	    }
    }

	$result = array('status' => $status, 'message' => $message);
	return $result;
}


function distanceL($location1,$location2) 
{
	$opts = array(
 					'http'=>array(
  					'header' => 'Connection: close'
 					)
				);
	$context = stream_context_create($opts);
	$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$location1&destinations=$location2&mode=driving&language=en-EN&key=".GOOGLE_API_KEY;
	$data = @file_get_contents($url,false,$context);
	$result = json_decode($data, true);
	$distance = $result['rows'][0]['elements'][0]['distance']['value'];
	$distance = $distance/1000;
	return $distance;
}


function distance($city1, $city2)
{
	$opts = array(
 					'http'=>array(
  					'header' => 'Connection: close'
 					)
				);
	$context = stream_context_create($opts);
	$url = "http://maps.googleapis.com/maps/api/directions/json?origin=$city1&destination=$city2";
	$data = @file_get_contents($url,false,$context);
	$result = json_decode($data, true);
	//usort($routes,create_function('$a,$b','return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'))
	$distance = $result['routes'][0]['legs'][0]['distance']['value'];
	$dist = ceil($distance/1000);
	//$dist = explode(" ",$distance);
	return $dist;
}

function sendSMS($contact_no, $message)
{
	// Textlocal account details
	$username = urlencode('novusvinod');
	$password = urlencode('1346590401');

	// Message details
	$number = urlencode($contact_no);
	$sender = urlencode('MYTAXI');
	
	// Prepare data for POST request
	$data = 'username=' . $username . '&password=' . $password . '&mobileno=' . $number . "&sendername=" . $sender . "&message=" . $message;

	// Send the GET request with cURL
	$ch = curl_init('http://bulksms.mysmsmantra.com:8080/WebSMS/SMSAPI.jsp?'.$data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//Commented by chetan
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}

function sendESMS($contact_no, $message)
{
	$post_data = array(
    // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
    // For promotional, this will be ignored by the SMS gateway
    'From'   => 'TXIVXI',
    'To'    => $contact_no,
    'Body'  => $message, 
);
 
	$exotel_sid = "novuslogic1"; // Your Exotel SID - Get it from here: http://my.exotel.in/Exotel/settings/site#api-settings
	$exotel_token = "94bfed570a17cd98d466175e1c893ad3cf5aef03"; // Your exotel token - Get it from here: http://my.exotel.in/Exotel/settings/site#api-settings
	 
	$url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
	 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
	
	//Commented by chetan	 
	$http_result = curl_exec($ch);
	$error = curl_error($ch);
	$http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
	 
	curl_close($ch);
	return $http_result;
	//print "Response = ".print_r($http_result);
}

function randomPassword() 
{
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) 
    {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function sendEmail($email_id,$subject,$body,$cc=NULL)
{
	//$mail = new PHPMailer;
	/*$mail->From = 'info@taxivaxi.com';
	$mail->FromName = 'TaxiVaxi';
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	$mail->AddAddress($email_id,'');
	$mail->Body = $body;
	$mail->Send();*/
	
	/*$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->SMTPAuth   = true;                 
	$mail->SMTPSecure = "tls";                 
	$mail->Host       = "smtp.gmail.com";      
	$mail->Port       = "587";                   
	$mail->Username   = "info@taxivaxi.com";  
	$mail->Password   = "12345@qazxc";            

	$mail->From = 'info@taxivaxi.com';
	$mail->FromName = 'TaxiVaxi';

	$mail->addReplyTo("info@taxivaxi.com","TaxiVaxi");

	$mail->Subject    = $subject;

	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; 
	$mail->msgHTML($body);

	$address = $email_id;
	$mail->addAddress($address, "");
	if($cc)
		$mail->addCc($cc, "");*/
	//$mail->addBcc("info@taxivaxi.com", "");
	//$mail->Send();

	//$mail->AddAttachment("images/phpmailer.gif");      // attachment
	//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

	/*if(!$mail->Send()) {
	  echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	  return;
	}*/
	$to      = $email_id;
	$subject = $subject;
	$message = $body;
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'BCC: vinod@novuslogic.co.in' . "\r\n";
	$headers .= 'From: corporate@taxivaxi.com' . "\r\n" .
    'Reply-To: corporate@taxivaxi.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    //Commented by chetan
	mail($to, $subject, $message, $headers,'-freturn@taxivaxi.com');
}

function getCity($lat, $long)
{
	
	$opts = array(
			'http'=>array(
				'header' => 'Connection: close'
			)
			);
	$context = stream_context_create($opts);
	$geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$long.'&sensor=false',false,$context);

    $output= json_decode($geocode);

    for($j=0;$j<count($output->results[0]->address_components);$j++)
    {
    	if($output->results[0]->address_components[$j]->types[0] == 'locality')
    		return $output->results[0]->address_components[$j]->long_name;
    }
}

function getCityF($pickup_location)  //In case Google API doesn't work
{
	$p = explode(',',$pickup_location);
	$arr_len = count($p);
	if(trim($p[$arr_len-1]) == 'India')
		$city = $p[$arr_len-3];
	else
		$city = $p[$arr_len-2];
	$city = trim($city);
	return $city;
}

function vincentyDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $lonDelta = $lonTo - $lonFrom;
  $a = pow(cos($latTo) * sin($lonDelta), 2) +
    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

  $angle = atan2(sqrt($a), $b);
  return $angle * $earthRadius;
}

function getHours($date1, $date2)
{
	$t1 = strtotime($date1);
	$t2 = strtotime($date2);
	$diff = $t2-$t1;
	$hours = ceil($diff/3600);
	return $hours;
}

function getWeekDay($date,$modifier)
{
	$timestamp = strtotime($date);
	$day = date($modifier, $timestamp);
	return $day;
}

//Return all the days between two dates
function getWeekDays($date1,$date2)
{
	$start = new DateTime($date1);
	$end = new DateTime($date2);
	$oneday = new DateInterval("P1D");

	$days = array();
	/* Iterate from $start up to $end+1 day, one day in each iteration.
	   We add one day to the $end date, because the DatePeriod only iterates up to,
	   not including, the end date. */
	foreach(new DatePeriod($start, $oneday, $end->add($oneday)) as $day) {
	    $day_num = $day->format("N"); /* 'N' number days 1 (mon) to 7 (sun) */
	    $days[] = getWeekDay($day->format("Y-m-d"),'w');
	}    
	return $days;
}

function getHourinDay($date,$is_left=1)
{
	$ts = strtotime($date);
	$day_hours = date('H', $ts);
	$min = date('i', $ts);
	if($is_left)
		$hours = 24 - $day_hours;
	else
	{
		if($min > 0)
			$hours = $day_hours + 1;
		else
			$hours = $day_hours;
	}
		
	return $hours;
}

function createWelcomeMailToCompany($company_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			<div style="text-align: center;">
				<img src="http://taxivaxi.in/corporate/images/company_logos/logo_corporate.png" width="150px" style="clip: rect(0px,930px,30px,0px);"/><br>
				Let\'s Go Somewhere...
			</div>
			<hr>
			Dear '.$company_details['name'].',<br><br>Welcome to TaxiVaxi.<br><br>TaxiVaxi is India’s first marketplace for booking Radio Taxi, Tour taxi, Self Drive cars and carpooling on one platform.<br><br>At TaxiVaxi, we provide the complete booking solution to your company online. We will change the way corporate book the rides for their employees.<br><br>Your Login Credentials are mentioned below:<br><br><u><b><div style="background-color:#FFD929; height:30px;">Booking Panel Link:</div></b></u><br>http://corporate.taxivaxi.in/login<br><br><u><b>Company Login Details</b></u><br>User Name: '.$company_details['username'].'<br>Password: '.$company_details['password'].'<br>Contact No: '.$company_details['contact_no'].'<br><br>Now you can book radio taxi, tour taxi, self drive cars using TaxiVaxi Panel. At TaxiVaxi Corporate, our dedicated division, we focus on providing technologically efficient solution for bookings backed by really good quality service for all the travel related requirements of the corporate clients in India.<br><br><u><b>Salient Features</u></b><br><ul><li> An internet based dashboard shall be given for the following:<ul><li>Each and every car can be tracked on the dashboard</li><li>The details of every car and every driver will be there</li><li>Every passenger can be assigned a vehicle from the dashboard</li><li>The communication through Email, SMS can be sent for the respective allocation of Vehicles from the Dashboard</li><li>The support for Dashboard, Emails, SMS, Vehicle Tracking shall be given by us.</li></ul></li><li>Well dressed and educated chauffeurs with Cell phone along with the vehicles.</li></ul><br><br>For any query on your bookings or complaints you can reach us at +91 9990045853 or mail on corporate@taxivaxi.com. For emails we shall get back to you within 12 hours.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}


function bookingDetailsToUser($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			<div style="text-align: center;">
				<img src="http://taxivaxi.in/corporate/images/company_logos/logo_corporate.png" width="150px" /><br>
				<span>Let\'s Go Somewhere...</span>
			</div>
			<hr>
			Dear Employee'.',<br><br>TaxiVaxi is India’s first marketplace for booking Radio Taxi, Tour taxi, Self Drive cars and carpooling on one platform.<br><br>TaxiVaxi corporate is India’s first corporate solution which allows cash less and paperless travel for corporate travellers and allow booking Radio Taxi and Outstation taxi on one platform.<br><br>This is to inform you that we have received the booking from your spoc for your corporate travel. The details of your booking are mentioned below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Approval Status:</b></u><br>Approver 1: '.'Pending'.'<br>Approver 2: '.'Pending'.'<br><br>';

			$mail_body = $mail_body.'Please note that once your booking request is approved, TaxiVaxi will further accept the booking based on availability of cars.<br><br>Once the booking is accepted by TaxiVaxi, the driver and car details shall be shared with you shortly.<br><br>For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function bookingDetailsToSpoc($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			<div style="text-align: center;">
				<img src="http://taxivaxi.in/corporate/images/company_logos/logo_corporate.png" width="150px" /><br>
				<span>Let\'s Go Somewhere...</span>
			</div>
			<hr>
			Dear '.$company_details['name'].',<br><br>TaxiVaxi is India’s first marketplace for booking Radio Taxi, Tour taxi, Self Drive cars and carpooling on one platform.<br><br>TaxiVaxi corporate is India’s first corporate solution which allows cash less and paperless travel for corporate travellers and allow booking Radio Taxi and Outstation taxi on one platform.<br><br>This is to inform you that we have received the booking from you. The details of your booking are mentioned below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Approval Status:</b></u><br>Approver 1: '.'Pending'.'<br>Approver 2: '.'Pending'.'<br><br>';

			$mail_body = $mail_body.'Please note that once your booking request is approved, TaxiVaxi will further accept the booking based on availability of cars.<br><br>Once the booking is accepted by TaxiVaxi, the driver and car details shall be shared with you shortly.<br><br>For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function bookingDetailsToAuthone($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$company_details['approver_1'].',<br><br>A booking has come from your team. We request you to please check the same and approve.<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Approval Matrix:</b></u><br>Approver 1: '.$company_details['approver_1'].'<br>Approver 2: '.$company_details['approver_2'].'<br><br>';
			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function bookingDetailsToAuthtwo($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$company_details['approver_2'].',<br><br>A booking has come from your team. We request you to please check the same and approve.<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Approval Matrix:</b></u><br>Approver 1: '.$company_details['approver_1'].'<br>Approver 2: '.$company_details['approver_2'].'<br><br>';
			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

/*function bookingDetailsToTaxiVaxiAdmin($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.'TaxiVaxi Team'.',<br><br>A booking has come from TCS. Please assign the driver and car for the same.<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['reference_no'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Approval Matrix:</b></u><br>Approver 1: '.$company_details['approver_1'].'<br>Approver 2: '.$company_details['approver_2'].'<br><br>';
			$mail_body = $mail_body.'Please allocate the driver and car at the earliest. <br><br>Best Regards,<br>TaxiVaxi Corporate Admin
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}*/

function rejectionBySpocToEmployee($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear Employee,<br><br>We regret to inform that your booking request has been rejected by '.$company_details['name'].'(Spoc). Please find the details of the following below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Rejected By:</b></u>'.$company_details['disapprover_1'].'<br><br>';
			$mail_body = $mail_body.'<u><b>Reason:</b></u>'.$company_details['cancel_reason'].'<br><br>';
			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function rejectionBySpocToSpoc($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$company_details['name'].',<br><br>We regret to inform that your booking request has been rejected. Please find the details of the following below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Rejected By:</b></u>'.$company_details['disapprover_1'].'<br><br>';
			$mail_body = $mail_body.'<u><b>Reason:</b></u>'.$company_details['cancel_reason'].'<br><br>';
			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}


function rejectionByAuthOneToSpoc($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$company_details['name'].',<br><br>We regret to inform that your booking request has been rejected. Please find the details of the following below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Rejected By:</b></u>'.$company_details['disapprover_1'].'<br><br>';
			$mail_body = $mail_body.'<u><b>Reason:</b></u>'.$company_details['cancel_reason'].'<br><br>';
			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function rejectionByAuthTwoToSpoc($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$company_details['name'].',<br><br>We regret to inform that your booking request has been rejected. Please find the details of the following below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Rejected By:</b></u>'.$company_details['disapprover_2'].'<br><br>';
			$mail_body = $mail_body.'<u><b>Reason:</b></u>'.$company_details['cancel_reason'].'<br><br>';
			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function rejectionByTaxivaxiToSpoc($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$company_details['name'].',<br><br>We regret to inform that your booking request has been rejected. Please find the details of the following below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Rejected By:</b></u>'.'TaxiVaxi'.'<br><br>';
			$mail_body = $mail_body.'<u><b>Reason of Cancelation:</b></u>'.$company_details['cancel_reason'].'<br><br>For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function rejectionByTaxivaxiToEmployee($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.'Employee'.',<br><br>We regret to inform that your booking request has been rejected. Please find the details of the following below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Rejected By:</b></u>'.'TaxiVaxi'.'<br><br>';
			$mail_body = $mail_body.'<u><b>Reason of Cancelation:</b></u>'.$company_details['cancel_reason'].'<br><br>For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}


function assignMailToSpoc($company_details, $n, $user_details,$employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			<div style="text-align: center;">
				<img src="http://taxivaxi.in/corporate/images/company_logos/logo_corporate.png" width="150px" /><br>
				<span>Let\'s Go Somewhere...</span>
			</div>
			<hr>
			Dear '.$company_details['name'].',<br><br>TaxiVaxi is India’s first marketplace for booking Radio Taxi, Tour taxi, Self Drive cars and carpooling on one platform.<br><br>TaxiVaxi corporate is India’s first corporate solution which allows cash less and paperless travel for corporate travellers and allow booking Radio Taxi and Outstation taxi on one platform.<br><br>This is to inform you that we have received the booking from you for your corporate travel. The details of your booking are mentioned below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID: '.$company_details['reference_no'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br><br>'.
			'<u><b>Driver Details:</b></u><br>Driver Name          : '.$company_details['driver_name'].'<br>Driver Contact          : '.$company_details['driver_contact'].'<br><br>'.
			'<u><b>Journey Type:</b></u><br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br><br>'.
			'<u><b>Taxi Details:</b></u><br>Taxi Model     : '.$company_details['taxi_model_name'].'<br>Taxi Registration No          : '.$company_details['taxi_reg_no'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;

			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function assignMailToEmployee($company_details, $n, $user_details,$employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			<div style="text-align: center;">
				<img src="http://taxivaxi.in/corporate/images/company_logos/logo_corporate.png" width="150px" /><br>
				<span>Let\'s Go Somewhere...</span>
			</div>
			<hr>
			Dear Employee'.',<br><br>TaxiVaxi is India’s first marketplace for booking Radio Taxi, Tour taxi, Self Drive cars and carpooling on one platform.<br><br>TaxiVaxi corporate is India’s first corporate solution which allows cash less and paperless travel for corporate travellers and allow booking Radio Taxi and Outstation taxi on one platform.<br><br>This is to inform you that we have received the booking from you for your corporate travel. The details of your booking are mentioned below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID: '.$company_details['reference_no'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br><br>'.
			'<u><b>Driver Details:</b></u><br>Driver Name          : '.$company_details['driver_name'].'<br>Driver Contact          : '.$company_details['driver_contact'].'<br><br>'.
			'<u><b>Journey Type:</b></u><br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br><br>'.
			'<u><b>Taxi Details:</b></u><br>Taxi Model     : '.$company_details['taxi_model_name'].'<br>Taxi Registration No          : '.$company_details['taxi_reg_no'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;

			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function sendRegistrationEmailToAuthenticator2($auth_name, $group_name, $auth_email, 
						$auth_password, $admin_contact, $admin_email)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$auth_name.',<br><br>A new group '.$group_name.' has been successfully created on TaxiVaxi Booking Portal, and you have been created as Group Authenticator. You will be responsible for approving bookings created by SPOCs.<br><br>
			<u><b>Login Details:</b></u><br>URL:http://business.taxivaxi.in/authtwo/login<br>Email: '.$auth_email.'<br>Password: '.$auth_password.'<br><br>';
			$mail_body = $mail_body.'For any query you can reach your Administrator at +91 '.$admin_contact.' or mail at '.$admin_email.'<br><br>Best Regards,<br>TaxiVaxi Corporate Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function sendRegistrationEmailToAuthenticator1($auth_name, $group_name, $auth_email, 
						$auth_password, $admin_contact, $admin_email)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$auth_name.',<br><br>A new sub-group '.$group_name.' has been successfully created on TaxiVaxi Booking Portal, and you have been created as Sub-Group Authenticator. You will be responsible for approving bookings created by SPOCs.<br><br>
			<u><b>Login Details:</b></u><br>URL:http://business.taxivaxi.in/authone/login<br>Email: '.$auth_email.'<br>Password: '.$auth_password.'<br><br>';
			$mail_body = $mail_body.'For any query you can reach your Administrator at +91 '.$admin_contact.' or mail at '.$admin_email.'<br><br>Best Regards,<br>TaxiVaxi Corporate Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function sendRegistrationEmailToAuthenticatorSpoc($auth_name, $auth_email, 
						$auth_password, $admin_contact, $admin_email)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$auth_name.',<br><br>You have been added as a SPOC on TaxiVaxi Booking Portal, and you will be responsible for creating bookings for employees.<br><br>
			<u><b>Login Details:</b></u><br>URL:http://business.taxivaxi.in/spoc/login<br>Email: '.$auth_email.'<br>Password: '.$auth_password.'<br><br>';
			$mail_body = $mail_body.'For any query you can reach your Administrator at +91 '.$admin_contact.' or mail at '.$admin_email.'<br><br>Best Regards,<br>TaxiVaxi Corporate Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function bookingAcceptToSpoc($company_details, $n, $user_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$company_details['name'].',<br><br>The booking with following details as been accepted by Approver1/2.<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$company_details['booking_id'].'<br>Pickup Location          : '.$company_details['pickup_location'].'<br>Pickup DateTime          : '.$company_details['pickup_datetime'].'<br>Trip          : '.$company_details['trip'].'<br>Package          : '.$company_details['package'].'<br>Car Type          : '.$company_details['car_type'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Approval Matrix:</b></u><br>Approver 1: '.$company_details['approver_1'].'<br>Approver 2: '.$company_details['approver_2'].'<br><br>';
			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function forgotPasswordMail($details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$details['name'].',<br><br>We have reset you password<br><br>Below mentioned is your new password<br><br>We recommend you chaange your password once you login with this password<br><br>'.
			'<u><b>New Password:</b></u><br>';
			$mail_body = $mail_body.$details['password'];
			$mail_body = $mail_body.'<br><br>For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function BusBookingAcceptRejectToSpoc($booking_details, $user_details, $employee_details, $status = 'accepted', $by = 'Approver1/2')
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$booking_details['user_name'].',<br><br>The booking with following details has been '.$status.' by '.$by. '.<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$booking_details['booking_id'].'<br>Pickup City          : '.$booking_details['pickup_city'].'<br>Drop City          : '.$booking_details['drop_city'].'<br>Journey Date          : '.$booking_details['date_of_journey'].'<br>Bus Type          : '.$booking_details['bus_type_priority_1'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'<u><b>Approval Matrix:</b></u><br>Approver 1: '.$booking_details['approver1_name'].'<br>Approver 2: '.$booking_details['approver2_name'].'<br><br>';
			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function busBookingCreated($booking_details, $user_details, $employee_details, $to = 'spoc')
{
	switch($to)
	{
		case 'spoc':
		{
			$start_text = "TaxiVaxi is India’s first marketplace for booking Radio Taxi, Tour taxi, Self Drive cars and carpooling on one platform.<br><br>TaxiVaxi corporate is India’s first corporate solution which allows cash less and paperless travel for corporate travellers and allow booking Radio Taxi and Outstation taxi on one platform.<br><br>This is to inform you that we have received the booking from you. The details of your booking are mentioned below:";
			$end_text = 'Please note that once your booking request is approved, TaxiVaxi will further accept the booking based on availability of cars.<br><br>Once the booking is accepted by TaxiVaxi, the driver and car details shall be shared with you shortly.<br><br>For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate.oe@taxivaxi.com';
			$name = $booking_details['user_name'];
			$approver_text = '<u><b>Approval Status:</b></u><br>Approver 1: '.'Pending'.'<br>Approver 2: '.'Pending'.'<br><br>';
			break;
		}
		case 'approver1':
		{
			$start_text = "A booking has come from your team. We request you to please check the same and approve.";
			$end_text = 'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com';
			$name = $booking_details['approver1_name'];
			$approver_text = '<u><b>Approval Matrix:</b></u><br>Approver 1: '.$booking_details['approver1_name'].'<br>Approver 2: '.$booking_details['approver2_name'].'<br><br>';
			break;
		}
		case 'approver2':
		{
			$start_text = "A booking has come from your team. We request you to please check the same and approve.";
			$end_text = 'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com';
			$name = $booking_details['approver2_name'];
			$approver_text = '<u><b>Approval Matrix:</b></u><br>Approver 1: '.$booking_details['approver1_name'].'<br>Approver 2: '.$booking_details['approver2_name'].'<br><br>';
			break;
		}
	}
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear '.$name.',<br><br>'.$start_text.'<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID:          '.$booking_details['booking_id'].'<br>Pickup City          : '.$booking_details['pickup_city'].'<br>Drop City          : '.$booking_details['drop_city'].'<br>Journey Date          : '.$booking_details['date_of_journey'].'<br>Bus Type          : '.$booking_details['bus_type_priority_1'].'<br><br>'.
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.$approver_text;
			$mail_body = $mail_body.$end_text.'
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function busBookingAssigned($booking_details, $user_details, $employee_details, $to = 'spoc')
{
	$journey_datetime = $booking_details['pickup_datetime_taxivaxi'];
	$journey_datetime = date("d-M-Y h:i a", strtotime($journey_datetime));
	$journey_date = explode(" ",$journey_datetime)[0];
	$journey_time = explode(" ",$journey_datetime)[1]." ".explode(" ",$journey_datetime)[2];
	
	switch($to)
	{
		case 'spoc':
		{
			$name = $booking_details['user_name'];
			//$approver_text = '<u><b>Approval Status:</b></u><br>Approver 1: '.'Pending'.'<br>Approver 2: '.'Pending'.'<br><br>';
			break;
		}
		case 'employee':
		{
			$name = $booking_details['people_name'];
			//$approver_text = '<u><b>Approval Matrix:</b></u><br>Approver 1: '.$booking_details['approver1_name'].'<br>Approver 2: '.$booking_details['approver2_name'].'<br><br>';
			break;
		}
	}
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			<div style="text-align: center;">
				<img src="http://taxivaxi.in/corporate/images/company_logos/logo_corporate.png" width="150px" /><br>
				<span>Let\'s Go Somewhere...</span>
			</div>
			<hr>
			Dear '.$name.',<br><br>TaxiVaxi is India\'s first marketplace for booking Radio Taxi, Tour taxi, Self Drive cars and carpooling on one platform.<br><br>TaxiVaxi corporate is India’s first corporate solution which allows cash less and paperless travel for corporate travellers and allow booking Radio Taxi and Outstation taxi on one platform.<br><br>This is to inform you that we have received the booking from you for your corporate travel. The details of your booking are mentioned below:<br><br>
			<u><b>Booking Details:</b></u><br>Booking ID: '.$booking_details['reference_no'].'<br>Pickup City          : '.$booking_details['pickup_city'].'<br>Drop City         : '.$booking_details['drop_city'].'<br>Journey Date: '.$journey_date.'<br>Journey Time: '.$journey_time.'<br>Boarding Point: '.$booking_details['boarding_point'].'<br><br>'.
			'<u><b>Ticket Details</b></u><br>Ticket #:'.$booking_details['ticket_number'].'<br>PNR #: '.$booking_details['pnr_number'].'Seat No.: '.$booking_details['seat_number'].'<br><br>'.
			'<u><b>Bus Details:</b></u><br>Bus Type: '.$booking_details['bus_type_allocated'].'<br><br>'.
			'<u><b>Operator Details:</b></u><br>Operator Name          : '.$booking_details['operator_name'].'<br>Operator Contact          : '.$booking_details['operator_contact'].'<br><br>'.
			
			
			'<u><b>Spoc Details:</b></u><br>';
			$mail_body = $mail_body.$user_details;
			$mail_body = $mail_body.'<u><b>Employee Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;

			$mail_body = $mail_body.'For any query you can reach us at '.TAXIVAXI_NUMBER.'or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

function bookingDetailsToOperator($booking_details, $employee_details)
{
	
	$mail_body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
	<head>
	<title></title>
	<meta name="" content="">
	</head>
	<body style="font-family: Arial;font-size:12px">
		<div style="border: solid 1px #222; width:600px; border-radius: 10px; padding: 5px">
			Dear Sir,<br><br>Please book the vehicles  as per the below schedule-.<br><br>
			<u><b>Trip Details:</b></u><br>Booking ID:'.$booking_details['booking_id'].'<br>City: '.$booking_details['city'].'<br>Pickup Location: '.$booking_details['pickup_location'].'<br>Pickup DateTime: '.$booking_details['pickup_datetime'].'<br>Drop Location: '.$booking_details['drop_location'].'<br><br>
			<u><b>Vehicle Details:</b></u><br>Tour Type: '.$booking_details['tour_type'].'<br>Vehicle Type: '.$booking_details['taxi_type_name'].'<br>Package Name: '.$booking_details['package'].'<br><br>';
			$mail_body = $mail_body.'<u><b>Passenger Details:</b></u><br>';
			$mail_body = $mail_body.$employee_details;
			$mail_body = $mail_body.'Please confirm the booking and Share the car and the driver details at the earliest.<br><br>
			Very Important Note:<br>Please put the Booking Id as mentioned above in the duty slip<br>Car should be neat and clean and driver should be well uniformed and polite.<br>
			 Please use the duty slip for all our duties<br>Please ask the client to sign every duty slip no matter what.<br>
			 The cars should be commercial licence only and should be the one as mentioned above.<br>Any change in the delivery of the above requirement should not be considered<br>
			 No Payments will be done by customer, we will pay for the same.<br><br>Please call on the following if you have any Issues:<br>Praveen:9990045853<br>Ankit:813037487111/9990045953<br><br>Thanks & Regards<br>TaxiVaxi
		</div>
		
	</body>
	</html>';
	
	return $mail_body;
}

?>