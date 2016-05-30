<?php
    error_reporting(E_ERROR | E_PARSE);
    include "../common.php";
    require_once("../db.php");
    include "../GCM.php";
	require_once("../Rest.inc.php");
	require_once('../phpMailer/class.phpmailer.php');
	require_once('../email_address_validator.php');
	
	define("GOOGLE_TAXIVAXI_USER_API_KEY", "AIzaSyD3mXb6yIfv8QgxI0AOxAMJP4cqOy9hFPo"); // Place your Google API Key
	define("GOOGLE_TAXIVAXI_USER_SENDER_ID", "400620263167");
	define("BASE_URL", "http://taxivaxi.com/images/");
	
	class API extends REST 
	{
	
		public $data = "";
		
		private $db = NULL;
	
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}
		
		public function __destruct() 
		{
			$this->dbClose();					// Initiate Database connection
   		}
		/*
		 *  Database connection 
		*/
		private function dbConnect(){
			$this->db = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD);
			if($this->db)
				mysqli_select_db($this->db,DB);
		}
		
		private function dbClose()
		{
			mysqli_close($this->db);
		}
		/*
		 * Public method for access api.
		 * This method dynamically call the method based on the query string
		 *
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['request'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404); // If the method not exist with in this class, response would be "Page not found".
		}
		
		
		//Add Operator
		public function add()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAuth($token_id);
			$name = $this->_request['name'];
			$email = $this->_request['email'];
			$username = $email;
			$address = $this->_request['address'];	
			$contact_no = $this->_request['contact_no'];
			$website = $this->_request['website'];
			$terms_url = $this->_request['terms_url'];
			$privacy_policy_url = $this->_request['privacy_policy_url'];
			
			$password = randomPassword();
			
			try
			{
				$sql = mysqli_query($this->db, "select id from taxi_operators where email = '$email'");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'This Operator Already Exist');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "INSERT into taxi_operators (name,email,username,password,address,contact_no,website,terms_url,privacy_policy_url,created) values ('$name','$email','$username',md5('$password'),'$address','$contact_no','$website','$terms_url','$privacy_policy_url',now())";
					mysqli_query($this->db, $query);
				}
				
				$email_body = "Dear $name,<br><br>Welcome to TaxiVaxi!<br><br>Your TaxiVaxi login details are as follows:<br><br>Username: $email<br>Password: $password<br><br>Best Regards,<br>TaxiVaxi Team";
				sendEmail($email,'Welcome to TaxiVaxi',$email_body);
				
				$result = array('access_token' => $token_id,'Message'=>'Operator Added Successfully');
				$success = array('success' => "1", "error" => "","response"=>$result);
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		public function edit()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAuth($token_id);
			$id = $this->_request['id'];
			$name = $this->_request['name'];	
			$email = $this->_request['email'];
			$username = $email;
			$address = $this->_request['address'];	
			$contact_no = $this->_request['contact_no'];
			$website = $this->_request['website'];
			$terms_url = $this->_request['terms_url'];
			$privacy_policy_url = $this->_request['privacy_policy_url'];
			
			try
			{
				$sql = mysqli_query($this->db, "select id from taxi_operators where email = '$email' and id != $id");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'The Operator With the Same Email Already Exist');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "UPDATE taxi_operators set name = '$name',email = '$email',username = '$username',address='$address',contact_no='$contact_no',website = '$website',terms_url = '$terms_url',privacy_policy_url = '$privacy_policy_url' where id = $id";
					mysqli_query($this->db, $query);
				}
				
				$result = array('access_token' => $token_id,'Message'=>'Operator Edited Successfully');
				$success = array('success' => "1", "error" => "","response"=>$result);
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}

		public function delete()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAuth($token_id);
			$id = $this->_request['id'];
			
			try
			{
				$query = "DELETE from taxi_operators where id = $id";
				mysqli_query($this->db, $query);
				
				$result = array('access_token' => $token_id,'Message'=>'Operator Deleted Successfully');
				$success = array('success' => "1", "error" => "","response"=>$result);
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		public function view()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAuth($token_id);
			$id = $this->_request['id'];
			
			try
			{
				$query = "SELECT id,name,email,address,contact_no,website,terms_url,privacy_policy_url from taxi_operators where id = $id";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$result = array('access_token' => $token_id,'Operator'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found');
					$this->response($this->json($response),200);
				}
				
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		public function getAll()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAuth($token_id);
			
			try
			{
				$query = "SELECT id,name,email,address,contact_no,website,terms_url,privacy_policy_url from taxi_operators order by name";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'Operators'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found');
					$this->response($this->json($response),200);
				}
				
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		public function getDashboardDetails()
		{
			$token_id = $this->_request['access_token'];	
			$operator = $this->checkOperator($token_id);
			$operator_id = $operator['id'];
			
			$result = array();
			try
			{
				$query = "SELECT count(*) `total_trips` from tour_bookings where operator_id = '$operator_id'";
				$sql = mysqli_query($this->db, $query);
				$total_trips = 0;
				$completed_trips = 0;
				$cancelled_trips = 0;
				$on_ride_trips = 0;
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$total_trips = $rlt['total_trips']; 
				}
				
				$sql = mysqli_query($this->db, "SELECT count(*) `completed_trips` from tour_bookings where operator_id = '$operator_id' and status_id in (1,2,3,5)");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$completed_trips = $rlt['completed_trips']; 
				}
				
				$sql = mysqli_query($this->db, "SELECT count(*) `cancelled_trips` from tour_bookings where operator_id = '$operator_id' and status_id in (6,7)");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$cancelled_trips = $rlt['cancelled_trips']; 
				}
				
				$sql = mysqli_query($this->db, "SELECT count(*) `on_ride_trips` from tour_bookings where operator_id = '$operator_id' and status_id = 4");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$on_ride_trips = $rlt['on_ride_trips']; 
				}
				$rlt['total_trips'] = $total_trips;
				$rlt['completed_trips'] = $completed_trips;
				$rlt['cancelled_trips'] = $cancelled_trips;
				$rlt['on_ride_trips'] = $on_ride_trips;
				$rlt['total_payment'] = "0";
				$rlt['paid_by_cash'] = "0";
				$rlt['paid_by_paytm'] = "0";
				
				$result[] = $rlt;
				
				if(count($result) > 0)
				{
					$result = array('access_token' => $token_id,'Details'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found');
					$this->response($this->json($response),200);
				}
				
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		private function login()
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			$username = $this->_request['username'];		
			$password = $this->_request['password'];
			$device_id = $this->_request['device_id'];
			$gcm_regId = $this->_request['gcm_regId'];
			$is_web_login = $this->_request['is_web_login'];
			
			$enc_pwd = md5($password);
			
			$token = uniqid(time(), true);
			$token = md5(uniqid(time(), true));
		
			try
			{
				// Input validations
				if(!empty($username) and !empty($password))
				{
					
					$sql = mysqli_query($this->db, "SELECT id, name, email, contact_no, address, website, terms_url, privacy_policy_url, created, modified from taxi_operators WHERE username = '$username' AND password = '$enc_pwd' LIMIT 1");
					
					
					if(mysqli_num_rows($sql) > 0)
					{
						$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
						$id = $result['id'];
						
						/*if($result['image']!=NULL && $result['image'] != "")
						{
							$base_url = "http://taxivaxi.com/images/admins/";
							$img_url = $base_url.$result['image'];
							$result['image'] = $img_url;
						}*/
						
						if($is_web_login)
							mysqli_query($this->db, "UPDATE taxi_operators set web_token_id = '$token', modified = now() where id = $id");
						else
							mysqli_query($this->db, "UPDATE taxi_operators set token_id = '$token', device_id = '$device_id', gcm_regId = '$gcm_regId', modified = now() where id = $id");
						
						$result = array('access_token' => $token,'operator'=>$result);
						$success = array('success' => "1", "error" => "","response"=>$result);
						$this->response($this->json($success), 200);
					}
					else
					{
						$response = array('success' => "0", 'error' => "Invalid Username or Password");
						$this->response($this->json($response),200);
					}					
						
				}
			}
			catch(Exception $e)
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),406);
			}
			
			// If invalid inputs "Bad Request" status message and reason
			$response = array('success' => "0", "error" => "Please Enter Username and Password");
			$this->response($this->json($response),200);	
		}
		
		private function logout()
		{
			$token_id = $this->_request['access_token'];	
			$operator = $this->checkOperator($token_id);
			$operator_id = $operator['id'];
			$is_web_logout = $this->_request['is_web_logout'];	
			
			try
			{
				if($is_web_logout)
					mysqli_query($this->db, "update taxi_operators set web_token_id = '' where id = $operator_id");
				else
					mysqli_query($this->db, "update taxi_operators set token_id = '' where id = $operator_id");
				
				$sql = mysqli_query($this->db, "SELECT id, name, email, contact_no, address, website, terms_url, privacy_policy_url, created, modified from taxi_operators where id = $operator_id");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
				}
				$response = array('success' => "1", 'error' => "", "response" => $result);
				$this->response($this->json($response),200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		private function acceptBooking()
		{
			$token_id = $this->_request['access_token'];	
			$operator = $this->checkOperator($token_id);
			$operator_id = $operator['id'];
			$booking_id = $this->_request['booking_id'];
			
			try
			{
				mysqli_query($this->db, "update tour_bookings set status_id = 2 where id = '$booking_id'");
				$sql = mysqli_query($this->db, "SELECT ud.user_id, ud.gcm_regId, ud.name, u.email, ud.contact_no from users `u` inner join user_details `ud` on u.id = ud.user_id where user_id = (select user_id from tour_bookings where id = '$booking_id')");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$gcm_regId = $rlt['gcm_regId'];
					$result = array('access_token' => $token_id, 'Message'=>'Operator Accepted', 'notification_for' => 'Booking Accepted', 'booking_id' => $booking_id);
				
					$success = array('success' => "1", "error" => "","response"=>$result);
				
					$gcm = new GCM();
					$gcm->send_notification(array($gcm_regId),$this->json($success),GOOGLE_TAXIVAXI_USER_API_KEY);
					
					$sql2 = mysqli_query($this->db, "SELECT * from tour_bookings where id = '$booking_id'");
					
					$booking_details = "";
					
					if(mysqli_num_rows($sql2) > 0)
					{
						$rlt2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);
						$booking_details .= "Booking ID: TAV".$booking_id."<br>";
						$booking_details .= "Contact No.: ".$rlt['contact_no']."<br>";
						$booking_details .= "Pickup Location: ".$rlt2['pickup_location']."<br>";
						$booking_details .= "Pickup Time: ".$rlt2['pickup_datetime']."<br>";
						switch($rlt2['tour_type'])
						{
							case 0:
							{
								$rlt2['tour_type'] = "Local";
								break;
							}
							case 1:
							{
								$rlt2['tour_type'] = "One Way";
								break;
							}
							case 2:
							{
								$rlt2['tour_type'] = "Round Trip";
								break;
							}
							case 3:
							{
								$rlt2['tour_type'] = "Multi City";
								break;
							}
						}
						$booking_details .= "Type of Tour: ".$rlt2['tour_type']."<br>";
						if($rlt2['days'] > 0)
							$booking_details .= "Duration of Trip: ".$rlt2['days']. " days"."<br>";
						else
							$booking_details .= "Duration of Trip: ".$rlt2['hours']. " hours"."<br>";
						if($rlt2['cities'])
							$booking_details .= "Cities: ".$rlt2['cities']."<br>";
							
						$taxi_details = $this->getTaxiInfo($rlt2['taxi_id']);
						$booking_details .= "Taxi Detail: ".$taxi_details['taxi_model']."<br>";
						$booking_details .= "Operator Name: ".$operator['name']."<br>";
						$booking_details .= "Operator Address: ".$operator['address']."<br>";
						$booking_details .= "Operator Contact: ".$operator['contact_no']."<br>";
						
					}
					$mail_body = "Dear ".$rlt['name'].",<br><br>Thanks for your booking with TaxiVaxi. We will send the cab and driver details 2 hours before start of your journey. Your booking details are as follows:<br><br>".$booking_details."<br><br>Best Regards,<br>TaxiVaxi Team";
					sendEmail($rlt['email'],"Booking Confirmed",$mail_body);	
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Unable to fetch User Details');
					$this->response($this->json($response),200);
				}
				
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		private function rejectBooking()
		{
			$token_id = $this->_request['access_token'];	
			$operator = $this->checkOperator($token_id);
			$operator_id = $operator['id'];
			//$device_id = $this->_request['device_id'];
			$booking_id = $this->_request['booking_id'];
			
			try
			{
				mysqli_query($this->db, "update tour_bookings set status_id = 7 where id = '$booking_id'");
				$sql = mysqli_query($this->db, "SELECT user_id, gcm_regId from user_details where user_id = (select user_id from tour_bookings where id = '$booking_id')");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$gcm_regId = $rlt['gcm_regId'];
					$result = array('access_token' => $token_id, 'Message'=>'Operator Rejected', 'notification_for' => 'Booking Rejected', 'booking_id' => $booking_id);
				
					$success = array('success' => "1", "error" => "","response"=>$result);
				
					$gcm = new GCM();
					$gcm->send_notification(array($gcm_regId),$this->json($success),GOOGLE_TAXIVAXI_USER_API_KEY);
						
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Unable to fetch User Details');
					$this->response($this->json($response),200);
				}
				
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		private function timeoutBooking()
		{
			$token_id = $this->_request['access_token'];	
			$operator = $this->checkOperator($token_id);
			$operator_id = $operator['id'];
			$booking_id = $this->_request['booking_id'];
			
			try
			{
				mysqli_query($this->db, "update tour_bookings set status_id = 1 where id = '$booking_id'");
				$sql = mysqli_query($this->db, "SELECT user_id, gcm_regId from user_details where user_id = (select user_id from tour_bookings where id = '$booking_id')");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$gcm_regId = $rlt['gcm_regId'];
					$result = array('access_token' => $token_id, 'Message'=>'Not Attended', 'notification_for' => 'Not Attended', 'booking_id' => $booking_id);
				
					$success = array('success' => "1", "error" => "","response"=>$result);
				
					$gcm = new GCM();
					$gcm->send_notification(array($gcm_regId),$this->json($success),GOOGLE_TAXIVAXI_USER_API_KEY);
						
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Unable to fetch User Details');
					$this->response($this->json($response),200);
				}
				
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		private function forgetPassword()
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			//
			$email = $this->_request['email'];
			
			/*$token_id = uniqid(mt_rand(), true);
			$token_id = md5(uniqid(mt_rand(), true));	*/	
			
			try
			{
				/*$validator = new EmailAddressValidator;
				if(!$validator->check_email_address($email))
				{
					$response = array('success' => "0", 'error' => 'Invalid Email ID');
					$this->response($this->json($response),200);
				}*/
				//Check Whether Anybody has been registered with this Email ID
				$sql = mysqli_query($this->db, "select id from taxi_operators where email = '$email'");
				if(mysqli_num_rows($sql) > 0)
				{
					try
					{
						$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
						$operator_id = $result['id'];
						$new_password = $this->randomPassword();
						sendEmail($email,'Your Password has been reset',"You have requested for password change.<br />Your New Password is: $new_password");
						
						
						$query = "UPDATE taxi_operators set password = md5('$new_password') WHERE id = $operator_id";
						mysqli_query($this->db, $query);
						$result = array('operator_id' => $operator_id, 'message' => 'Password Reset and Mailed Successfully');
						//$result = array('access_token' => $token_id,'user'=>$result);
						$success = array('success' => "1", "error" => "","response"=>$result);
						$this->response($this->json($success), 200);
					} 
					catch(Exception $ex) 
					{
						$response = array('success' => "0", 'error' => $e->getMessage());
						$this->response($this->json($response),200);
					}
				}
				else
				{
					$response = array('success' => "0", 'error' => 'No Operator Found');
					$this->response($this->json($response),200);
				}
			}
			catch(Exception $ex)
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),406);
			}
		}
		
		
		private function ChangePassword()
		{
			$token_id = $this->_request['access_token'];	
			$operator = $this->checkOperator($token_id);
			$operator_id = $operator['id'];
			
			// Request Parameters 
			//$branch_id = $this->_request['branch_id'];
			$old_password = $this->_request['old_password'];
			$new_password = $this->_request['new_password'];
			
			
			try
			{
				$sql = mysqli_query($this->db, "select id from taxi_operators where id = $operator_id and password = md5('$old_password')");
				
				//If password match
				if(mysqli_num_rows($sql) > 0)
				{
					try
					{
						$query = "UPDATE taxi_operators set password = md5('$new_password') WHERE id = $operator_id";
						mysqli_query($this->db, $query);
						$result = array('operator_id' => $operator_id, 'message' => 'Password Changed Successfully');
						$result = array('access_token' => $token_id,'operator'=>$result);
						$success = array('success' => "1", "error" => "","response"=>$result);
						$this->response($this->json($success), 200);
					} 
					catch(Exception $ex) 
					{
						$response = array('success' => "0", 'error' => $e->getMessage());
						$this->response($this->json($response),200);
					}
					
				}
				else  //else insert new value
				{
					$response = array('success' => "0", 'error' => 'Please enter correct old password');
					$this->response($this->json($response),200);
				}
				
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
			
		}
		
		
		public function checkAuth($token_id)
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			if(empty($token_id))
			{
				$response = array('success' => "0", 'error' => "Access Token Invalid");
				$this->response($this->json($response),200);
			}
			else
			{
				$sql = mysqli_query($this->db, "select id from admins where token_id = '$token_id' LIMIT 1");
				if(mysqli_num_rows($sql) > 0)
				{
					$user = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$user_id = $user['id'];
					return $user_id;
				}
				else
				{
					$response = array('success' => "0", 'error' => "Access Token Invalid");
					$this->response($this->json($response),200);
				}
			}
		}
		
		public function checkOperator($token_id)
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			if(empty($token_id))
			{
				$response = array('success' => "0", 'error' => "Access Token Invalid");
				$this->response($this->json($response),200);
			}
			else
			{
				$sql = mysqli_query($this->db, "select id, name, contact_no, address from taxi_operators where token_id = '$token_id' or web_token_id = '$token_id' LIMIT 1");
				if(mysqli_num_rows($sql) > 0)
				{
					$operator = mysqli_fetch_array($sql,MYSQL_ASSOC);
					//$operator_id = $operator['id'];
					return $operator;
				}
				else
				{
					$response = array('success' => "0", 'error' => "Access Token Invalid");
					$this->response($this->json($response),200);
				}
			}
		}
		
		public function save_image($base64_string, $image_ext, $folder_name, $image_name = NULL)
		{
			$output_path = getcwd();
			$pos = strrpos($output_path, "/");
			$output_path = substr($output_path,0,$pos+1);
			if($image_name)
				$img_name = $image_name . "." . $image_ext;
			else
				$img_name = time() . "." . $image_ext;
			$output_path .= "/images/".$folder_name."/".$img_name;
			$output = base64_to_image($base64_string,$output_path);
			return $img_name; //return image name to save in database
		}

		private function getTaxiInfo($taxi_id)
		{
			$sql = mysqli_query($this->db, "select t.reg_no, t.reg_year, t.is_airconditioned, t.fuel_type, t.garage_location, tm.name `taxi_model`, tm.no_of_seats, tt.name `taxi_type` from taxis `t` inner join taxi_models `tm` on t.model_id = tm.id inner join taxi_types `tt` on tt.id = tm.type_id where t.id = '$taxi_id' LIMIT 1");
					
			if(mysqli_num_rows($sql) > 0)
			{
				$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
				$result['image'] = "http://taxivaxi.com/images/taxi/".$result['image'];
			}
			return $result;
		}
		

		/*
		 *	Encode array into JSON
		*/
		private function json($data)
		{
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	
	// Initiiate Library
	$api = new API;
	$api->processApi();
	
?>