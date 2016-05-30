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
		
		
		//Add Taxi
		public function add()
		{
			$token_id = $this->_request['access_token'];	
			$operator_id = $this->checkOperator($token_id);
			$reg_no = $this->_request['reg_no'];
			$reg_year = $this->_request['reg_year'];
			$plate_no = $this->_request['plate_no'];
			$is_airconditioned = $this->_request['is_airconditioned'];
			$fuel_type = $this->_request['fuel_type'];
			$model_id = $this->_request['model_id'];
			$garage_location = $this->_request['garage_location'];
			$g_latlong = getLatLong($garage_location);
			$g_lat = $g_latlong['lat'];
			$g_long = $g_latlong['long'];
			$city_id = $this->_request['city_id'];
			
			
			try
			{
				$sql = mysqli_query($this->db, "select id from taxis where reg_no = '$reg_no'");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'This taxi already exist');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "INSERT into taxis (operator_id,reg_no,reg_year,plate_no,is_airconditioned,fuel_type,model_id,garage_location, g_latitude, g_longitude, city_id,created) values ('$operator_id','$reg_no','$reg_year','$plate_no',$is_airconditioned,'$fuel_type','$model_id','$garage_location','$g_lat','$g_long','$city_id',now())";
					//echo $query;
					mysqli_query($this->db, $query);
					$taxi_id = mysqli_insert_id($this->db);
				}
				
				/*$email_body = "Dear $name,<br><br>Welcome to TaxiVaxi!<br><br>Your TaxiVaxi login details are as follows:<br><br>Username: $email<br>Password: $password<br><br>Best Regards,<br>TaxiVaxi Team";
				sendEmail($email,'Welcome to TaxiVaxi',$email_body);*/
				if($taxi_id)
				{
					$result = array('access_token' => $token_id,'Message'=>'Taxi Added Successfully');
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Taxi Not Added Successfully');
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
			$reg_no = $this->_request['reg_no'];
			$reg_year = $this->_request['reg_year'];
			$plate_no = $this->_request['plate_no'];
			$is_airconditioned = $this->_request['is_airconditioned'];
			$fuel_type = $this->_request['fuel_type'];
			$model_id = $this->_request['model_id'];
			$garage_location = $this->_request['garage_location'];
			$g_latlong = getLatLong($garage_location);
			$g_lat = $g_latlong['lat'];
			$g_long = $g_latlong['long'];
			$city_id = $this->_request['city_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "select id from taxi_operators where reg_no = '$reg_no' and id != $id");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'The Taxi With the Same Registration No. Already Exist');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "UPDATE taxis set reg_no = '$reg_no', reg_year = '$reg_year', plate_no = '$plate_no', is_airconditioned = '$is_airconditioned',fuel_type = '$fuel_type',model_id='$model_id',garage_location = '$garage_location',g_latitude = '$g_lat',g_longitude='$g_long',city_id = '$city_id' where id = $id";
					mysqli_query($this->db, $query);
				}
				
				$result = array('access_token' => $token_id,'Message'=>'Taxi Edited Successfully');
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
				
				$result = array('access_token' => $token_id,'Message'=>'Taxi Deleted Successfully');
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
				$query = "SELECT t.id,t.reg_no, t.reg_year, t.is_airconditioned, cc.name `car_company`, tm.name `model_name`, tt.name `taxi_type`, t.garage_location, c.name `city`, t.created, t.modified from taxis `t` inner join taxi_models `tm` on t.model_id = tm.id inner join taxi_types `tt` on tt.id = tm.type_id inner join car_companies `cc` on cc.id = tm.car_company inner join cities `c` on c.id = t.city_id where t.id = $id";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$result = array('access_token' => $token_id,'Taxi'=>$result);
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
				$query = "SELECT t.id,t.reg_no, t.reg_year, t.is_airconditioned, cc.name `car_company`, tm.name `model_name`, tt.name `taxi_type`, t.garage_location, c.name `city`, t.created, t.modified from taxis `t` inner join taxi_models `tm` on t.model_id = tm.id inner join taxi_types `tt` on tt.id = tm.type_id inner join car_companies `cc` on cc.id = tm.car_company inner join cities `c` on c.id = t.city_id where t.operator_id = '$operator_id'";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'Taxis'=>$result);
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
		
		public function getCarCompanies()
		{
			
			try
			{
				$query = "SELECT * from car_companies";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'CarCompanies'=>$result);
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
		
		public function getModelsOfCompany()
		{
			$company_id = $this->_request['company_id'];	
			try
			{
				$query = "SELECT * from taxi_models where car_company = '$company_id'";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'TaxiModels'=>$result);
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