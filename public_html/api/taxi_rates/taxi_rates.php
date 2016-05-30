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
		
		
		//Add Rate
		public function add()
		{
			$token_id = $this->_request['access_token'];	
			$operator_id = $this->checkOperator($token_id);
			$taxi_type = $this->_request['type_id'];
			$per_day_charge = $this->_request['per_day_charge'];
			$min_km_per_day = $this->_request['min_km_per_day'];
			$per_km_charge = $this->_request['per_km_charge'];
			$per_hour_charge = $this->_request['per_hour_charge'];
			$overnight_charge = $this->_request['overnight_charge'];
			$driver_allowance = $this->_request['driver_allowance'];
			$city_id = $this->_request['city_id'];
			
			
			try
			{
				$sql = mysqli_query($this->db, "select id from taxi_rental_rates where taxi_type = '$taxi_type' and operator_id = '$operator_id'");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'Taxi Rates are already available for the selected type');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "INSERT into taxi_rental_rates (operator_id,taxi_type,per_day_charge,min_km_per_day,per_km_charge,per_hour_charge,overnight_charge, driver_allowance, city_id) values ('$operator_id','$taxi_type','$per_day_charge','$min_km_per_day','$per_km_charge','$per_hour_charge','$overnight_charge','$driver_allowance','$city_id')";
					//echo $query;
					mysqli_query($this->db, $query);
					$taxi_rate_id = mysqli_insert_id($this->db);
				}
				
				if($taxi_rate_id)
				{
					$result = array('access_token' => $token_id,'Message'=>'Taxi Rate Added Successfully');
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Taxi Rate Not Added Successfully');
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
			$taxi_type = $this->_request['type_id'];
			$per_day_charge = $this->_request['per_day_charge'];
			$min_km_per_day = $this->_request['min_km_per_day'];
			$per_km_charge = $this->_request['per_km_charge'];
			$per_hour_charge = $this->_request['per_hour_charge'];
			$overnight_charge = $this->_request['overnight_charge'];
			$driver_allowance = $this->_request['driver_allowance'];
			$city_id = $this->_request['city_id'];
			
			try
			{
				$query = "UPDATE taxi_rental_rates set taxi_type = '$taxi_type', per_day_charge = '$per_day_charge',min_km_per_day = '$min_km_per_day',per_km_charge = '$per_km_charge',per_hour_charge='$per_hour_charge',overnight_charge = '$overnight_charge',driver_allowance = '$driver_allowance',city_id='$city_id' where id = $id";
				mysqli_query($this->db, $query);
				
				
				$result = array('access_token' => $token_id,'Message'=>'Taxi Rate Edited Successfully');
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
				$query = "DELETE from taxi_rental_rates where id = $id";
				mysqli_query($this->db, $query);
				
				$result = array('access_token' => $token_id,'Message'=>'Taxi Rate Deleted Successfully');
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
				$query = "SELECT tr.id, tr.per_day_charge, tr.min_km_per_day, tr.per_km_charge, tr.per_hour_charge, tr.overnight_charge, tr.driver_allowance, tt.name as `taxi_type`, c.name `city` from taxi_rental_rates `tr` inner join taxi_types `tt` on tr.taxi_type = tt.id inner join cities `c` on tr.city_id = c.id where tr.id = '$id'";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$result = array('access_token' => $token_id,'TaxiRate'=>$result);
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
				$query = "SELECT tr.id, tr.per_day_charge, tr.min_km_per_day, tr.per_km_charge, tr.per_hour_charge, tr.overnight_charge, tr.driver_allowance, tt.name as `taxi_type`, c.name `city` from taxi_rental_rates `tr` inner join taxi_types `tt` on tr.taxi_type = tt.id inner join cities `c` on tr.city_id = c.id where tr.operator_id = '$operator_id'";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
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