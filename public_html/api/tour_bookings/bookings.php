<?php
    error_reporting(E_ERROR | E_PARSE);
    include "../common.php";
    require_once("../db.php");
	require_once("../Rest.inc.php");
	require_once('../phpMailer/class.phpmailer.php');
	require_once('../email_address_validator.php');
	
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
		
		
		//Add Driver
		public function add()
		{
			$token_id = $this->_request['access_token'];	
			$operator_id = $this->checkOperator($token_id);
			$name = $this->_request['name'];
			$gender = $this->_request['gender'];
			$image = $this->_request['image'];
			$contact_no = $this->_request['contact_no'];
			$license_no = $this->_request['license_no'];
			$address = $this->_request['address'];
			$is_verified = $this->_request['is_verified'];
			$city_id = $this->_request['city_id'];
			
			
			try
			{
				$sql = mysqli_query($this->db, "select id from drivers where license_no = '$license_no'");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'This driver already exist');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "INSERT into drivers (operator_id,name,gender,image,contact_no,license_no,address, is_verified, city_id,created) values ('$operator_id','$name','$gender','$image','$contact_no','$license_no','$address','$is_verified','$city_id',now())";
					//echo $query;
					mysqli_query($this->db, $query);
					$driver_id = mysqli_insert_id($this->db);
				}
				
				/*$email_body = "Dear $name,<br><br>Welcome to TaxiVaxi!<br><br>Your TaxiVaxi login details are as follows:<br><br>Username: $email<br>Password: $password<br><br>Best Regards,<br>TaxiVaxi Team";
				sendEmail($email,'Welcome to TaxiVaxi',$email_body);*/
				if($driver_id)
				{
					$result = array('access_token' => $token_id,'Message'=>'Driver Added Successfully');
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Driver Not Added Successfully');
					$this->response($this->json($response),200);
				}
				
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
			$operator_id = $this->checkOperator($token_id);
			$id = $this->_request['id'];
			$name = $this->_request['name'];
			$gender = $this->_request['gender'];
			$image = $this->_request['image'];
			$contact_no = $this->_request['contact_no'];
			$license_no = $this->_request['license_no'];
			$address = $this->_request['address'];
			$is_verified = $this->_request['is_verified'];
			$city_id = $this->_request['city_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "select id from drivers where license_no = '$license_no' and id != $id");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'The Driver With the Same License No. Already Exist');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "UPDATE drivers set name = '$name', gender = '$gender',image = '$image',contact_no = '$contact_no',license_no='$license_no',address = '$address',is_verified = '$is_verified',city_id='$city_id' where id = $id";
					mysqli_query($this->db, $query);
				}
				
				$result = array('access_token' => $token_id,'Message'=>'Driver Edited Successfully');
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
			$operator_id = $this->checkOperator($token_id);
			$id = $this->_request['id'];
			
			try
			{
				$query = "DELETE from taxis where id = $id";
				mysqli_query($this->db, $query);
				
				$result = array('access_token' => $token_id,'Message'=>'Driver Deleted Successfully');
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
			$operator_id = $this->checkOperator($token_id);
			$id = $this->_request['id'];
			
			try
			{
				$query = "SELECT tb.id, tb.user_id, ud.name `user_name`, tb.contact_no, tb.driver_id, d.name `driver_name`, tb.taxi_id, t.plate_no, t.reg_no `taxi_registration_no`, tb.pickup_location, s.value `status`, tb.tour_type, tb.days, tb.hours, tb.booking_date, tb.pickup_datetime from tour_bookings `tb` inner join user_details `ud` on tb.user_id = ud.user_id left join drivers `d` on tb.driver_id = d.id inner join taxis `t` on tb.taxi_id = t.id inner join status `s` on tb.status_id = s.id where t.id = '$id'";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$result = array('access_token' => $token_id,'Driver'=>$result);
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
			$operator_id = $this->checkOperator($token_id);
			
			try
			{
				$query = "SELECT tb.id, tb.user_id, ud.name `user_name`, tb.contact_no, tb.driver_id, d.name `driver_name`, tb.taxi_id, t.plate_no, t.reg_no `taxi_registration_no`, tb.pickup_location, s.value `status`, tb.tour_type, tb.days, tb.hours, tb.booking_date, tb.pickup_datetime from tour_bookings `tb` inner join user_details `ud` on tb.user_id = ud.user_id left join drivers `d` on tb.driver_id = d.id inner join taxis `t` on tb.taxi_id = t.id inner join status `s` on tb.status_id = s.id where tb.operator_id = '$operator_id'";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						switch($rlt['tour_type'])
						{
							case 0:
							{
								$rlt['tour_type'] = "Local";
								break;
							}
							case 1:
							{
								$rlt['tour_type'] = "One Way";
								break;
							}
							case 2:
							{
								$rlt['tour_type'] = "Round Trip";
								break;
							}
							case 3:
							{
								$rlt['tour_type'] = "Multi City";
								break;
							}
						}
						
						$is_completed = "0";
						$pickup_datetime = $rlt['pickup_datetime']; 
						if(time() < strtotime($pickup_datetime))
							$is_completed = "0";
						else
							$is_completed = "1";
						$rlt['is_completed'] = $is_completed;
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
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
		
		public function upcoming()
		{
			$token_id = $this->_request['access_token'];	
			$operator_id = $this->checkOperator($token_id);
			
			try
			{
				$query = "SELECT tb.id, tb.user_id, ud.name `user_name`, tb.contact_no, tb.driver_id, d.name `driver_name`, tb.taxi_id, t.reg_no `taxi_registration_no`, tb.pickup_location, s.value `status`, tb.tour_type, tb.days, tb.hours, tb.booking_date, tb.pickup_datetime from tour_bookings `tb` inner join user_details `ud` on tb.user_id = ud.user_id left join drivers `d` on tb.driver_id = d.id inner join taxis `t` on tb.taxi_id = t.id inner join status `s` on tb.status_id = s.id where pickup_datetime > now() and taxi_id in (select taxi_id from taxis where operator_id = '$operator_id')";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						switch($rlt['tour_type'])
						{
							case 0:
							{
								$rlt['tour_type'] = "Local";
								break;
							}
							case 1:
							{
								$rlt['tour_type'] = "One Way";
								break;
							}
							case 2:
							{
								$rlt['tour_type'] = "Round Trip";
								break;
							}
							case 3:
							{
								$rlt['tour_type'] = "Multi City";
								break;
							}
						}
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
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
		
		public function history()
		{
			$token_id = $this->_request['access_token'];	
			$operator_id = $this->checkOperator($token_id);
			
			try
			{
				$query = "SELECT tb.id, tb.user_id, ud.name `user_name`, tb.contact_no, tb.driver_id, d.name `driver_name`, tb.taxi_id, t.reg_no `taxi_registration_no`, tb.pickup_location, s.value `status`, tb.tour_type, tb.days, tb.hours, tb.booking_date, tb.pickup_datetime from tour_bookings `tb` inner join user_details `ud` on tb.user_id = ud.user_id left join drivers `d` on tb.driver_id = d.id inner join taxis `t` on tb.taxi_id = t.id inner join status `s` on tb.status_id = s.id where pickup_datetime < now() and taxi_id in (select taxi_id from taxis where operator_id = '$operator_id')";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						switch($rlt['tour_type'])
						{
							case 0:
							{
								$rlt['tour_type'] = "Local";
								break;
							}
							case 1:
							{
								$rlt['tour_type'] = "One Way";
								break;
							}
							case 2:
							{
								$rlt['tour_type'] = "Round Trip";
								break;
							}
							case 3:
							{
								$rlt['tour_type'] = "Multi City";
								break;
							}
						}
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
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

		public function assignDriver()
		{
			$token_id = $this->_request['access_token'];	
			$operator_id = $this->checkOperator($token_id);
			$booking_id = $this->_request['booking_id'];
			$driver_id = $this->_request['driver_id'];
			
			try
			{
				$query = "UPDATE tour_bookings set driver_id = '$driver_id' where id = '$booking_id'";
				mysqli_query($this->db, $query);
				$result = array('access_token' => $token_id,'Message'=>'Driver Assigned Successfully');
				$success = array('success' => "1", "error" => "","response"=>$result);
				$this->response($this->json($success), 200);
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
				$sql = mysqli_query($this->db, "select id from taxi_operators where token_id = '$token_id' or web_token_id = '$token_id' LIMIT 1");
				if(mysqli_num_rows($sql) > 0)
				{
					$operator = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$operator_id = $operator['id'];
					return $operator_id;
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