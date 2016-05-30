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
		
		/* 
		 *	Simple login API
		 *  Login must be POST method
		 *  email : <USER EMAIL>
		 *  pwd : <USER PASSWORD>
		 */
		
		
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
			
			$enc_pwd = md5($password);
			
			$token = uniqid(time(), true);
			$token = md5(uniqid(time(), true));
		
			try
			{
				// Input validations
				if(!empty($username) and !empty($password))
				{
					
					$sql = mysqli_query($this->db, "SELECT id, name, username, image, email, mobile, created, modified from admins WHERE username = '$username' AND password = '$enc_pwd' LIMIT 1");
					
					
					if(mysqli_num_rows($sql) > 0)
					{
						$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
						$id = $result['id'];
						
						if($result['image']!=NULL && $result['image'] != "")
						{
							$base_url = "http://taxivaxi.com/images/admins/";
							$img_url = $base_url.$result['image'];
							$result['image'] = $img_url;
						}
						
						mysqli_query($this->db, "UPDATE admins set token_id = '$token', modified = now() where id = $id");
						
					
						$result = array('access_token' => $token,'admin'=>$result);
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
			$admin_id = $this->checkAuth($token_id);
			//$device_id = $this->_request['device_id'];
			
			try
			{
				mysqli_query($this->db, "update admins set token_id = '' where id = $admin_id");
				$sql = mysqli_query($this->db, "SELECT id, name, username, image, email, mobile, created, modified from admins where id = $admin_id");
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
				$sql = mysqli_query($this->db, "select id from admins where email = '$email'");
				if(mysqli_num_rows($sql) > 0)
				{
					try
					{
						$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
						$admin_id = $result['id'];
						$new_password = $this->randomPassword();
						$mail = new PHPMailer();
						$mail->From = 'info@taxivaxi.com';
						$mail->FromName = 'TaxiVaxi';
						$mail->Subject = 'Your Password has been reset';
						$mail->AddAddress($email,'');
						$mail->Body = "You have requested for password change.<br />Your New Password is: $new_password";
						$mail->Send();
						
						$query = "UPDATE admins set password = md5('$new_password') WHERE id = $admin_id";
						mysqli_query($this->db, $query);
						$result = array('admin_id' => $admin_id, 'message' => 'Password Reset and Mailed Successfully');
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
					$response = array('success' => "0", 'error' => 'No User Found');
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
			$admin_id = $this->checkAuth($token_id);
			
			// Request Parameters 
			//$branch_id = $this->_request['branch_id'];
			$old_password = $this->_request['old_password'];
			$new_password = $this->_request['new_password'];
			
			
			try
			{
				$sql = mysqli_query($this->db, "select id from admins where id = $admin_id and password = md5('$old_password')");
				
				//If password match
				if(mysqli_num_rows($sql) > 0)
				{
					try
					{
						$query = "UPDATE admins set password = md5('$new_password') WHERE id = $admin_id";
						mysqli_query($this->db, $query);
						$result = array('user_id' => $user_id, 'message' => 'Password Changed Successfully');
						$result = array('access_token' => $token_id,'user'=>$result);
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
		

		private function getAllOperators()
		{
			$token_id = $this->_request['access_token'];	
			//$user_id = $this->checkAuth($token_id);
			
			$query = "select * from taxi_operators";
			$sql = mysqli_query($this->db, $query);
			if(mysqli_num_rows($sql) > 0)
			{
				$result = array();
				
				while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
				{
					$result[] = $rlt;
				}
				$result = array('access_token' => $token_id,'taxi_operators'=>$result);
				$success = array('success' => "1", "error" => "","response"=>$result);
				$this->response($this->json($success), 200);
			}
			else
			{
				$response = array('success' => "0", "error" => "No Data Found");
				$this->response($this->json($response), 200);
			}
		}
		
		
		//Returns reviews of a specific taxi operator
		
		private function getOperatorFeedbacks()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkAuth($token_id);
			
			// Request Parameters 
			//$branch_id = $this->_request['branch_id'];
			$operator_id = $this->_request['operator_id'];
			
			$query = "select id,operator_id, user_id, feedback, rating, created, modified from operator_feedbacks where operator_id = $operator_id";
			
			$sql = mysqli_query($this->db, $query);
			
			if(mysqli_num_rows($sql) > 0)
			{
				$result = array();
				
				while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
				{
					$review_id = $rlt['id'];
					$uid = $rlt['user_id'];
					
					$user_details = $this->_getUserInfo($uid);
					
					$rlt['user_details'] = $user_details;	
					$result[] = $rlt;
				}
				$result = array('access_token' => $token_id,'feedbacks'=>$result);
				$success = array('success' => "1", "error" => "","response"=>$result);
				$this->response($this->json($success), 200);
			}
			else
			{
				$response = array('success' => "0", "error" => "No Data Found");
				$this->response($this->json($response), 200);
			}
			
		}
		

		private function getTaxiTypes()
		{
			$token_id = $this->_request['access_token'];	
			//$user_id = $this->checkAuth($token_id);
			
			// Request Parameters 
			try
			{
				$sql = mysqli_query($this->db,"select * from taxi_types");
				$result = array();
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$type_id = $rlt['id'];
						$sql2 = mysqli_query($this->db,"select * from taxi_models where type_id = $type_id");
						$models = array();
						if(mysqli_num_rows($sql2) > 0)
						{
							while($rlt2 = mysqli_fetch_array($sql2,MYSQL_ASSOC))
							{
								$models[] = $rlt2;
							}
							$rlt['models'] = $models;
						}
						$result[] = $rlt;
					}
				}
				
				if(count($result) > 0)
				{
					$result = array('access_token' => $token_id,'TaxiTypes'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
				}
				else
				{
					$success = array('success' => "0", "error" => "No Result Found");
				}	
				$this->response($this->json($success), 200);
				
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		
		private function userBookings()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAuth($token_id);
			$user_id = $this->_request['user_id'];	
			//$status = $this->_request['status'];  //0 - all, 1- upcoming, 2- completed
			
			try
			{
				$query = "select * from tour_bookings where user_id = $user_id";
				$sql = mysqli_query($this->db,$query);
				
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$status_id = $rlt['status_id'];
						$taxi_id = $rlt['taxi_id'];
						
						/*Temporary Solution for Status*/
						$pickup_datetime = $rlt['pickup_datetime']; 
						if(time() < strtotime($pickup_datetime))
							$status = 'upcoming';
						else
							$status = 'completed';
						/**End -Temporary Solution for Status**/
						
						$sql2 = mysqli_query($this->db,"select tm.name as `taxi_model`, tt.name as `taxi_type`, tt.image, to.name as `operator_name`, to.address as `operator_address`, to.contact_no as `operator_contact`, to.website as `operator_website`, to.terms_url as `operator_terms`, to.privacy_policy_url as `operator_privacy_policy` from taxis `t` inner join taxi_operators `to` on t.operator_id = to.id inner join taxi_models `tm` on t.model_id = tm.id inner join taxi_types `tt` on tm.type_id = tt.id where t.id = $taxi_id LIMIT 1");
						if(mysqli_num_rows($sql2) > 0)
						{
							$taxi_details  = mysqli_fetch_array($sql2,MYSQL_ASSOC);
							$rlt['taxi_details'] = $taxi_details;
						}
						/*if($status_id <= 4)
							$status = 'upcoming';
						else
							$status = 'completed';*/
						$rlt['status'] = $status;
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'bookings' => $result);
					$success = array('success' => "1", 'error' => "", "response" => $result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found');
					$this->response($this->json($response),200);
				}
				
			}
			catch(Exception $e)
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
				$sql = mysqli_query($this->db, "select id from admins where token_id = '$token_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$admin = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$admin_id = $admin['id'];
					return $admin_id;
				}
				else
				{
					$response = array('success' => "0", 'error' => "Access Token Invalid");
					$this->response($this->json($response),200);
				}
			}
		}
		
		//Get City field value
		private function _getCityDetails($city_id,$field)
		{
			$sql = mysqli_query($this->db, "select name, city_code, std_code from cities where id = $city_id");
			if(mysqli_num_rows($sql) > 0)
			{
				$res = mysqli_fetch_array($sql,MYSQL_ASSOC);
				$out = $res[$field];
				return $out;
			}
			return false;
		}
		
		private function _getFavoriteLocationList($user_id)
		{
			$sql = mysqli_query($this->db, "select fl.*, c.name `city_name` from favorite_locations `fl` inner join cities `c` on fl.city_id = c.id where user_id = $user_id");
			$result = array();
			if(mysqli_num_rows($sql) > 0)
			{
				while($res = mysqli_fetch_array($sql,MYSQL_ASSOC))
				{
					$result[] = $res;
				}	
			}
			return $result;
		}

		private function _getUserInfo($user_id)
		{
			$sql = mysqli_query($this->db, "SELECT ud.user_id, ud.name, u.email, u.created, u.modified, ud.gender, ud.dob, ud.image, ud.contact_no, u.reg_type, ud.facebook_id, ud.gplus_id, ud.is_mobile_validated, ud.is_email_validated, up.points_earned FROM users as `u` left join user_details as `ud` on u.id = ud.user_id left join user_points `up` on u.id = up.user_id WHERE u.id = $user_id LIMIT 1");
					
			if(mysqli_num_rows($sql) > 0)
			{
				$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
				$result['image'] = "http://taxivaxi.com/images/users/".$result['image'];
				
				/*SoS Contact*/
				$sql2 = mysqli_query($this->db, "SELECT name `sos_contact_name`, sos_contact `sos_contact_no` from user_sos_contacts where user_id = $user_id");
				if(mysqli_num_rows($sql2) > 0)  
				{
					$sos = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$result['sos'] = $sos;
				}
				
				/*End - SoS Contact*/
			}
			return $result;
		}
		
		public function test()
		{
			$output_path = getcwd();
			$pos = strrpos($output_path, "/");
			$output_path = substr($output_path,0,$pos+1);
			$output_path .= "/images/reviews/".time().".png";
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
		
		public function getOneValue($query, $field)
		{
			$sq = mysqli_query($this->db, $query);
			if(mysqli_num_rows($sq) > 0)
			{
				$res = mysqli_fetch_array($sq,MYSQL_ASSOC);
				$out = $res[$field];
				return $out;
			}
			return false;
		}
		
		
		private function randomPassword() 
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