<?php
    error_reporting(E_ERROR | E_PARSE);
    include "../common.php";
    require_once("../db.php");
	require_once("../Rest.inc.php");
	require_once('../phpMailer/class.phpmailer.php');
	require_once('../email_address_validator.php');
	define("BASE_URL", "http://taxivaxi.com/testapi/images/taxi/");
	
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
		
		
		//Add Taxi Type
		/*
		public function add()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkAuth($token_id);
			$name = $this->_request['name'];	
			$city_id = $this->_request['city_id'];	
			$latitude = $this->_request['latitude'];
			$longitude = $this->_request['longitude'];
			
			try
			{
				$sql = mysqli_query($this->db, "select id from stations where name = '$name' and city_id = $city_id");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'The station name for the selected city already exist');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "INSERT into stations (name,city_id,latitude,longitude,created) values ('$name',$city_id,'$latitude','$longitude',now())";
					mysqli_query($this->db, $query);
					$airport_id = mysqli_insert_id($this->db);
				}
				if($airport_id != 0)
				{
					$result = array('access_token' => $token_id,'Message'=>'Station Added Successfully');
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Station Not Added Successfully');
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
			$user_id = $this->checkAuth($token_id);
			$id = $this->_request['id'];
			$name = $this->_request['name'];	
			$city_id = $this->_request['city_id'];	
			$latitude = $this->_request['latitude'];
			$longitude = $this->_request['longitude'];
			
			try
			{
				$query = "UPDATE stations set name = '$name',city_id = '$city_id',latitude='$latitude',longitude='$longitude',modified = now() where id = $id";
				mysqli_query($this->db, $query);
				
				$result = array('access_token' => $token_id,'Message'=>'Station Edited Successfully');
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
			$user_id = $this->checkAuth($token_id);
			$id = $this->_request['id'];
			
			try
			{
				$query = "DELETE from stations where id = $id";
				mysqli_query($this->db, $query);
				
				$result = array('access_token' => $token_id,'Message'=>'Station Deleted Successfully');
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
			$user_id = $this->checkAuth($token_id);
			$id = $this->_request['id'];
			
			try
			{
				$query = "SELECT s.*,c.name `city_name` from stations `s` inner join cities `c` on s.city_id = c.id where s.id = $id";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$result = array('access_token' => $token_id,'Station'=>$result);
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
			
		}*/
		
		public function getAll()
		{
			$token_id = $this->_request['access_token'];	
			//$user_id = $this->checkAuth($token_id);
			
			try
			{
				$query = "SELECT tt.id, tt.name, tt.description, tt.image, tt.created, tt.modified from taxi_types `tt`";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$rlt['image'] = BASE_URL.$rlt['image'];
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'TaxiTypes'=>$result);
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