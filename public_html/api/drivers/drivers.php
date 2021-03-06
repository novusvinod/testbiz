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
			$device_id = $this->_request['device_id'];
			$img_string = $this->_request['img_string'];
			$img_ext = $this->_request['img_ext'];
			$contact_no = $this->_request['contact_no'];
			$license_no = $this->_request['license_no'];
			$license_img_string = $this->_request['license_img_string'];
			$license_img_ext = $this->_request['license_img_ext'];
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
					if($img_string && $img_ext)
						$img_name = $this->save_image($img_string,$img_ext,'drivers',$driver_id);
					if($license_img_string && $license_img_ext)
						$license_img = $this->save_image($img_string,$img_ext,'licenses',$driver_id);
					mysqli_query($this->db, "UPDATE drivers set image = '$img_name', license_copy = '$license_img' where id = '$driver_id'");
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
			$device_id = $this->_request['device_id'];
			$img_string = $this->_request['img_string'];
			$img_ext = $this->_request['img_ext'];
			$contact_no = $this->_request['contact_no'];
			$license_no = $this->_request['license_no'];
			$license_img_string = $this->_request['license_img_string'];
			$license_img_ext = $this->_request['license_img_ext'];
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
					if($img_string && $img_ext)
						$img_name = $this->save_image($img_string,$img_ext,'drivers',$id);
					if($license_img_string && $license_img_ext)
						$license_img = $this->save_image($img_string,$img_ext,'licenses',$id);

					$query = "UPDATE drivers set name = '$name', gender = '$gender',image = '$img_name',contact_no = '$contact_no',license_no='$license_no',license_copy = '$license_img', address = '$address',is_verified = '$is_verified',city_id='$city_id' where id = $id";
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
				$query = "DELETE from drivers where id = $id";
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
				$query = "SELECT d.id, d.name, d.gender, d.image, d.contact_no, d.license_no, d.address, c.name `city`, d.is_verified from drivers `d` inner join cities `c` on d.city_id = c.id where d.id = $id";
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
				$query = "SELECT d.id, d.name, d.gender, d.image, d.contact_no, d.license_no, d.license_copy, d.address, c.name `city`, d.is_verified from drivers `d` inner join cities `c` on d.city_id = c.id where d.operator_id = $operator_id";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$rlt['image'] = "http://taxivaxi.com/images/drivers/".$rlt['image'];
						$rlt['license_copy'] = "http://taxivaxi.com/images/licenses/".$rlt['license_copy'];
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'Drivers'=>$result);
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
		
		public function test()
		{
			/*$url = 'http://taxivaxi.com/images/taxi/suv.jpg';
			$img = '/images/test/suv.jpg';
			file_put_contents($img, file_get_contents($url));*/
			
			$ch = curl_init('http://taxivaxi.com/images/taxi/suv.jpg');
			$fp = fopen('/images/test/suv.jpg', 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
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
			$output_path .= "images/".$folder_name."/".$img_name;
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