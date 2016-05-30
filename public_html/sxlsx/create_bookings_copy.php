<?php

include 'simplexlsx.class.php';
include('common.php');
include('db.php');

define("TAXIVAXI_NUMBER", "7891022360");
define("TAXIVAXI_EMAIL", "corporate.oe@taxivaxi.com");

$db = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD);
mysqli_select_db($db,DB);

function dbConnect()
{
	$this->db = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD);
	if($this->db)
		mysqli_select_db($this->db,DB);
}
		
function dbClose()
{
	mysqli_close($this->db);
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
	 
	$http_result = curl_exec($ch);
	$error = curl_error($ch);
	$http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
	 
	curl_close($ch);
	return $http_result;
	//print "Response = ".print_r($http_result);
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

	mail($to, $subject, $message, $headers,'-freturn@taxivaxi.com');
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

			$mail_body = $mail_body.'Please note that once your booking request is approved, TaxiVaxi will further accept the booking based on availability of cars.<br><br>Once the booking is accepted by TaxiVaxi, the driver and car details shall be shared with you shortly.<br><br>For any query you can reach us at '.TAXIVAXI_NUMBER.' or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
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

			$mail_body = $mail_body.'Please note that once your booking request is approved, TaxiVaxi will further accept the booking based on availability of cars.<br><br>Once the booking is accepted by TaxiVaxi, the driver and car details shall be shared with you shortly.<br><br>For any query you can reach us at '.TAXIVAXI_NUMBER.' or mail us at '.TAXIVAXI_EMAIL.'.<br><br>Best Regards,<br>TaxiVaxi Team<br>corporate@taxivaxi.com
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




// output worsheet 1

//list($num_cols, $num_rows) = $xlsx->dimension();

//echo '<h1>Sheet 1</h1>';

$k=0;
$new_entries = 0;
$updated = 0;
$ids= $_GET['ids'];
//echo $ids;
$id_arr = explode(',',$ids);
$pickup_datetimes = array('2016-04-23 05:00:00','2016-04-24 05:00:00');//,'2016-04-18 05:30:00','2016-04-19 05:30:00','2016-04-22 05:30:00','2016-04-24 05:30:00');//,'2016-04-27 05:00:00','2016-04-28 05:00:00','2016-04-29 05:00:00','2016-04-30 05:00:00');

/*$pickup_datetimes = array('2016-03-31 05:30:00','2016-04-02 05:30:00','2016-04-03 05:30:00','2016-04-04 05:30:00','2016-04-05 05:30:00','2016-04-06 05:30:00','2016-04-07 05:30:00','2016-04-09 05:30:00','2016-04-10 05:30:00','2016-04-11 05:30:00','2016-04-12 05:30:00','2016-04-16 05:30:00','2016-04-18 05:30:00','2016-04-19 05:30:00','2016-04-22 05:30:00','2016-04-26 05:30:00','2016-04-27 05:30:00','2016-04-28 05:30:00','2016-04-29 05:30:00','2016-04-30 05:30:00');*/

foreach($id_arr as $id)
{
	$sql = mysqli_query($db, "SELECT tour_type,city_id,pickup_location,
					booking_date,pickup_datetime,rate_id,taxi_type_id,
					created, modified,
					admin_id,group_id, subgroup_id,
					ass_code, reason_booking, no_of_seats from bookings where id = '$id'");
	if(mysqli_num_rows($sql) > 0)
	{
		$rlt = mysqli_fetch_array($sql);
		$tour_type = $rlt['tour_type'];
		$city_id = $rlt['city_id'];
		$pickup_location = $rlt['pickup_location'];
		$rate_id = $rlt['rate_id'];
		$taxi_type_id = $rlt['taxi_type_id'];
		$admin_id = $rlt['admin_id'];
		$group_id = $rlt['group_id'];
		$subgroup_id = $rlt['subgroup_id'];
		$ass_code = $rlt['ass_code'];
		$reason_booking = $rlt['reason_booking'];
		$no_of_seats = $rlt['no_of_seats'];
		
		$sql2 = mysqli_query($db, "SELECT user_id from user_bookings where booking_id = '$id'");
		$rlt2 = mysqli_fetch_array($sql2);
		$user_id = $rlt2['user_id'];
		
		//$sql3 = mysqli_query($db, "SELECT people_id from people_bookings where booking_id = '$id'");
		//$rlt3 = mysqli_fetch_array($sql3);
		//$people_id = $rlt3['people_id'];
		
		foreach($pickup_datetimes as $pickup_datetime)
		{
			$query = "INSERT into bookings (tour_type,city_id,pickup_location,
					booking_date,pickup_datetime,rate_id,taxi_type_id,
					created, modified,
					admin_id,group_id, subgroup_id,
					ass_code, reason_booking, no_of_seats) values ('$tour_type','$city_id','$pickup_location',
					now(),'$pickup_datetime','$rate_id', '$taxi_type_id',
					now(),now(),
					'$admin_id','$group_id', '$subgroup_id',
					'$ass_code', '$reason_booking', '$no_of_seats')";
					
			mysqli_query($db,$query);
			$booking_id = mysqli_insert_id($db);
			echo $booking_id."<br>";
			if(!$booking_id)
			{
				echo $id." - ".$pickup_datetime."<br>";
				continue;
			}
			
			mysqli_query($db,"INSERT INTO user_bookings (user_id,booking_id,group_id,subgroup_id,admin_id) VALUES ($user_id,$booking_id,$group_id,$subgroup_id,$admin_id)");
			
			$sql3 = mysqli_query($db, "SELECT people_id from people_bookings where booking_id = '$id'");
			
			while($rlt3 = mysqli_fetch_array($sql3))
			{
				$people_id = $rlt3['people_id'];
				mysqli_query($db,"INSERT INTO people_bookings (people_id,booking_id) VALUES ($people_id, $booking_id)");
			}
			
			
			$booking_details['booking_id'] = 'TVTCS'.$booking_id;
			$booking_details['pickup_location'] = $pickup_location;
			$booking_details['pickup_datetime'] = $pickup_datetime;
			$booking_details['trip'] = 'Local';
			$booking_details['package'] = '12 Hrs/ 125 Km';
			
			switch($taxi_type_id)
			{
				case '1': 
				{
					$booking_details['car_type'] = 'Sedan';
					$booking_details['taxi_type_name'] = 'Sedan';
					break;
				}
				case '2':
				{
					$booking_details['car_type'] = 'SUV';
					$booking_details['taxi_type_name'] = 'SUV';
					break;
				}
			}
			
			
			
			
			//$booking_details['package_name'] = $pa
			//Generate spoc details for email
					$sqlt = mysqli_query($db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
						a.corporate_name 
						from user_bookings `b` 
						left join users `u` on b.user_id = u.id
						left join admins `a` on b.admin_id = a.id
						where b.booking_id = '$booking_id'");
					$c=1;
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{
						$booking_details['name'] = $resultt['user_name'];
						$booking_details['email'] = $resultt['email'];
						$booking_details['user_cid'] = $resultt['user_cid'];
						$booking_details['user_contact'] = $resultt['user_contact'];
						$booking_details['corporate_name'] = $resultt['corporate_name'];
					}
					$user_details = '';
					for($i=1;$i<=1;$i++)
					{
						$e_id = $booking_details['user_cid'];
						$n = $booking_details['name'];
						$c = $booking_details['corporate_name'];
						$e = $booking_details['email'];
						$user_details .= $user_details.'Spoc Employee Id          : '.$e_id.'<br>Name          : '.$n.'<br>Company Name          : '.$c.'<br>Email          : '.$e.'<br><br>';
					}
			
			//Generate employee details for email
					$sqlt = mysqli_query($db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					$employee_details = '';
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{
						$e_id = $resultt['people_cid'];
						$n = $resultt['people_name'];
						$c = $resultt['people_contact'];
						$e = $resultt['people_email'];
						$employee_details = $employee_details.'Employee Id          : '.$e_id.'<br>Name          : '.$n.'<br>Contact No          : '.$c.'<br>Email          : '.$e.'<br><br>';
					}

					//Approver Details
					$sqla = mysqli_query($db, "SELECT 
						sa.name `approver_1`, sa.email `email_approve_1`, sa.contact_no `contact_approver_1`,
						ga.name `approver_2`, ga.email `email_approver_2`,
						b.id `bk_id`
						from bookings `b`
						left join subgroup_authenticater `sa` on b.subgroup_id = sa.subgroup_id
						left join group_authenticater `ga` on b.group_id = ga.group_id
						where b.id = '$booking_id' ");
					$resulta = mysqli_fetch_array($sqla,MYSQL_ASSOC);

					$booking_details['approver_1'] = $resulta['approver_1'];
					$booking_details['approver_2'] = $resulta['approver_2'];
					

		            $i = 1;
		            //Communicate to all employees
		            $sqlt = mysqli_query($db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$mail_body = bookingDetailsToUser($booking_details, $i, $user_details, $employee_details);
						sendEmail($resultt['people_email'],"New Booking TCS - TVTCS$booking_id",$mail_body);
		            }

		            $booking_details['booking_id'] = "TVTCS".$resulta['bk_id'];
		            
		           /* var_dump($booking_details);
		            echo "<br>";
		            var_dump($employee_details);
		            echo "<br>";*/

		            //Communication to Spoc
		            $mail_body = bookingDetailsToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail($booking_details['email'],"New Booking TCS - TVTCS$booking_id",$mail_body);

		            //Communication to Approver 1
		            $mail_body = bookingDetailsToAuthone($booking_details, $i, $user_details, $employee_details);
					sendEmail($resulta['email_approve_1'],"New Booking TCS - TVTCS$booking_id",$mail_body);

					//Communication to Approver 2
		            $mail_body = bookingDetailsToAuthtwo($booking_details, $i, $user_details, $employee_details);
					sendEmail($resulta['email_approver_2'],"New Booking TCS - TVTCS$booking_id",$mail_body);

					//sms to approver 1
					$m = "Dear ".$booking_details['approver_1'].",\n\nWe have got a booking with id ".$booking_details['booking_id']." from ".$booking_details['name'].".\nWe request you to take appropriate action on the same.\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($resulta['contact_approver_1'], $m);

					//sms to spoc
					$m = "Dear ".$booking_details['name'].",\n\nBooking successfully registered with id ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);

					//sms to taxivaxi
					$m = "New Booking - ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\nTaxiVaxi";
					sendESMS('9004850047', $m);

					//sms to neeraj
					$m = "New Booking - ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\nTaxiVaxi";
					sendESMS('8860375872', $m);
					
					//sms to Ankit
					sendESMS('9990045953', $m);

					//Communicate to all employees
		            $sqlt = mysqli_query($db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$m = "Dear ".$resultt['people_name'].",\n\nWe have got a booking with id ".$booking_details['booking_id']." from ".$booking_details['name']." for your travel.\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
						sendESMS($resultt['people_contact'], $m);
		            }
			
			$new_entries++;
			
		}

		
		
		
		//var_dump($rlt);
		//echo "<br><br>";
	}
	
}

echo "<br>".$new_entries;

?>