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
		
		
		//Add City
		public function add()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkAuth($token_id);
			$name = $this->_request['name'];	
			$state_id = $this->_request['state_id'];
			$city_code = $this->_request['city_code'];	
			$std_code = $this->_request['std_code'];
			$latitude = $this->_request['latitude'];
			$longitude = $this->_request['longitude'];
			
			try
			{
				$sql = mysqli_query($this->db, "select id from cities where std_code = '$std_code'");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'This City Already Exist');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "INSERT into cities (name,state_id,city_code,std_code,latitude,longitude,created) values ('$name',$state_id,'$city_code','$std_code','$latitude','$longitude',now())";
					mysqli_query($this->db, $query);
				}
				
				$result = array('access_token' => $token_id,'Message'=>'City Added Successfully');
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
			$user_id = $this->checkAuth($token_id);
			$id = $this->_request['city_id'];
			$name = $this->_request['name'];	
			$state_id = $this->_request['state_id'];
			$city_code = $this->_request['city_code'];	
			$std_code = $this->_request['std_code'];
			$latitude = $this->_request['latitude'];
			$longitude = $this->_request['longitude'];
			
			try
			{
				$sql = mysqli_query($this->db, "select id from cities where std_code = '$std_code' and id != $id");
				
				//If user has already liked or disliked the merchant, then update
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'The City With the Same STD Code Already Exist');
					$this->response($this->json($response),200);
				}
				else  //else update with new values
				{
					$query = "UPDATE cities set name = '$name',state_id = $state_id,city_code = '$city_code',std_code = '$std_code',latitude='$latitude',longitude='$longitude',modified = now() where id = $id";
					mysqli_query($this->db, $query);
				}
				
				$result = array('access_token' => $token_id,'Message'=>'City Edited Successfully');
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
			$id = $this->_request['city_id'];
			
			try
			{
				$query = "DELETE from cities where id = $id";
				mysqli_query($this->db, $query);
				
				$result = array('access_token' => $token_id,'Message'=>'City Deleted Successfully');
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
			$id = $this->_request['city_id'];
			
			try
			{
				$query = "SELECT * from cities where id = $id";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$result = array('access_token' => $token_id,'City'=>$result);
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
			try
			{
				$query = "SELECT * from cities";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'Cities'=>$result);
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
		
		public function getStates()
		{
			$token_id = $this->_request['access_token'];	
			if($token_id)
				$user_id = $this->checkAuth($token_id);
			
			try
			{
				$query = "SELECT * from states";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'States'=>$result);
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
		
		public function getStateId()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkAuth($token_id);
			$state_name = $this->_request['state_name'];	
			
			try
			{
				$query = "SELECT id from states where name = '$state_name'";
				$sql = mysqli_query($this->db, $query);
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'state_id'=>$result);
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