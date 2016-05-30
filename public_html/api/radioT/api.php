<?php
    error_reporting(E_ERROR | E_PARSE);
    include "../common.php";
    include "../GCM.php";
    require_once("../db.php");
	require_once("../Rest.inc.php");
	require_once('../phpMailer/class.phpmailer.php');
	require_once('../email_address_validator.php');
	
	define("GOOGLE_TAXIVAXI_USER_API_KEY", "AIzaSyD3mXb6yIfv8QgxI0AOxAMJP4cqOy9hFPo"); // Place your Google API Key
	//define("GOOGLE_RADIO_TAXI_DRIVER_API_KEY", "AIzaSyCW2E87HznrWtdwhr0CaxS2KtcoMrJOxtc"); // Place your Google API Key
	define("BASE_URL", "http://taxivaxi.com/testapi/images/");
	
	define("UBER_SECRET_KEY", "8oiebNd0BfzzH7zrghV-rValvqD6HlM6MtuXOFkD");
	
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
		
		
		private function getUber($p_lat,$p_long,$d_lat,$d_long)
		{
				//Uber 
				$api_url = "https://api.uber.com/v1/estimates/time";
				
				$data['server_token'] = "X_LO36fxO79F3U7qfRVKn2FM-A_VJxz3Mqz_edr7";
				$data['start_latitude'] = $p_lat;
				$data['start_longitude'] = $p_long;
				$api_url = sprintf("%s?%s", $api_url, http_build_query($data));
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $api_url);
    			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    			$res_uber = curl_exec($curl);
    			$res = array();
    			curl_close($curl);
    			
    			$jsonIterator = new RecursiveIteratorIterator(
			    new RecursiveArrayIterator(json_decode($res_uber, TRUE)),
			    RecursiveIteratorIterator::CATCH_GET_CHILD);

			foreach ($jsonIterator as $key => $val) 
			{
				
			    if(is_array($val)) 
			    {
			        if($key == "times")
			        	$timesArray = $val;
			        	break;
			    } 
			    else 
			    {
			        continue;
			    }
			}
			//var_dump($timesArray);
			foreach($timesArray as $product)
			{
				$product_id = $product['product_id'];
				$res['product_id'] = $product_id;
				$res['type_name'] = $product['localized_display_name'];
				$res['ETA'] = round($product['estimate'] / 60) . " Min";
				
				$api_url = "https://api.uber.com/v1/estimates/price";
		
				$data['server_token'] = "X_LO36fxO79F3U7qfRVKn2FM-A_VJxz3Mqz_edr7";
				$data['start_latitude'] = $p_lat;
				$data['start_longitude'] = $p_long;
				$data['end_latitude'] = $d_lat;
				$data['end_longitude'] = $d_long;
				$api_url = sprintf("%s?%s", $api_url, http_build_query($data));
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $api_url);
    			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    			$res_uber = curl_exec($curl);
    			curl_close($curl);
    			$jsonIterator = new RecursiveIteratorIterator(
			    new RecursiveArrayIterator(json_decode($res_uber, TRUE)),
			    RecursiveIteratorIterator::CATCH_GET_CHILD);
			    
			    foreach ($jsonIterator as $key => $val) 
				{
					
				    if(is_array($val)) 
				    {
				        if($key == "prices")
				        	$priceArray = $val;
				        	break;
				    } 
				    else 
				    {
				        continue;
				    }
				}
				
				foreach($priceArray as $price)
				{
					if($product_id == $price['product_id'])
					{
						$res['estimated_fare_min'] = $price['low_estimate'];
						$res['estimated_fare_max'] = $price['high_estimate'];
						
						$api_url = "https://api.uber.com/v1/products/".$product_id;
		
						$data['server_token'] = "X_LO36fxO79F3U7qfRVKn2FM-A_VJxz3Mqz_edr7";
						$api_url = sprintf("%s?%s", $api_url, http_build_query($data));
						$curl = curl_init();
						curl_setopt($curl, CURLOPT_URL, $api_url);
		    			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    			$res_uber = curl_exec($curl);
		    			curl_close($curl);
		    			$jsonIterator = new RecursiveIteratorIterator(
					    new RecursiveArrayIterator(json_decode($res_uber, TRUE)),
					    RecursiveIteratorIterator::CATCH_GET_CHILD);
					    
					    foreach ($jsonIterator as $key => $val) 
						{
							
						    if(is_array($val)) 
						    {
						    	if($key == "price_details")
						    	{
									$productArray = $val;
						    		$res['base_fare'] = $productArray['base'];
						    		$res['per_km_fare'] = $productArray['cost_per_distance'];
						    		$res['per_min_fare'] = $productArray['cost_per_minute'];
						        	break;
								}
					        	
						    } 
						    else 
						    {
						        switch($key)
						        {
									case "capacity":
									{
										$res['no_of_seats'] = $val;
										break;
									}
								}
						    }
						}
					}
					
				}
				$result[] = array('cab_service_id' => "3",'is_client' => "1", 'cab_service_name'=>"Uber", "cab_service_logo" => "http://taxivaxi.com/images/radioT/uber.png", 'CabDetail' => $res);
			}
			
			return $result;
		}
		
		private function getCabs()
		{
			$token_id =  $this->_request['access_token'];
			if($token_id)
			{
				$user = $this->checkUser($token_id);
				$user_id = $user['user_id'];
				$contact_no = $user['contact_no'];
				$email = $user['email'];
			}
				
		
			$pickup_location = $this->_request['pickup_location'];
			$p_latlong = getLatLong($pickup_location);
			$p_lat = $p_latlong['lat'];
			$p_long = $p_latlong['long'];
			
			$drop_location = $this->_request['drop_location'];
			$d_latlong = getLatLong($drop_location);
			$d_lat = $d_latlong['lat'];
			$d_long = $d_latlong['long'];
			
			$pickup_time = $this->_request['pickup_time'];
			
			$city = getCity($p_lat,$p_long);
			$city_id = $this->getCityId($city);
			
			$result = array();
			try
			{
				// For each apis of type getCabs
				$sql = mysqli_query($this->db, "select cs.id, cs.name, cs.is_client, cs.logo,  csa.cab_service_id, csa.api_url, csa.request_parameters, csa.method_type from cab_services_api `csa` inner join cab_services `cs` on csa.cab_service_id = cs.id where csa.api_type = 1");
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$cab_service_name = $rlt['name'];
						$cab_service_id = $rlt['cab_service_id'];
						$cab_service_logo = $rlt['logo'];
						$is_client = $rlt['is_client'];
						$method_type  = $rlt['method_type'];
						$api_url = $rlt['api_url'];
						$req_param[] = explode(',',$rlt['request_parameters']);
						$data = array();
						$sql2 = mysqli_query($this->db, "select meta_key,meta_value from cab_services_api_meta where cab_service_id = ".$rlt['cab_service_id']);
						if(mysqli_num_rows($sql2) > 0)
						{
							while($rlt2 = mysqli_fetch_array($sql2,MYSQL_ASSOC))
							{
								$key = $rlt2['meta_key'];
								$value = $rlt2['meta_value'];
								$data[$key] = $value;
							}
						}
						else
							continue;
						
						$data['start_latitude'] = $p_lat;
						$data['start_longitude'] = $p_long;
						$data['end_latitude'] = $d_lat;
						$data['end_longitude'] = $d_long;
						if($token_id)
						{
							$data['contact_no'] = $contact_no;
							$data['email'] = $email;
						}
							
						
						//$curl = curl_init("http://localhost/radioTaxis/abc/public/getCabs");
						$curl = curl_init($api_url);
						
						switch($method_type)
						{
							case "POST":
				            curl_setopt($curl, CURLOPT_POST, TRUE);

				            if ($data)
				                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				            break;
					        case "PUT":
					            curl_setopt($curl, CURLOPT_PUT, 1);
					            break;
					        default:
					            if ($data)
					                $url = sprintf("%s?%s", $url, http_build_query($data));
						}
						
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					    $response = json_decode(curl_exec($curl));
					    if($response->success == 1)
					    {
							$response  = $response->response->Cabs;
							foreach($response as $res)
							{
								$result[] = array('cab_service_id' => $cab_service_id,'is_client' => $is_client, 'cab_service_name'=>$cab_service_name, 'cab_service_logo' => $cab_service_logo, 'CabDetail' => $res);
							}
						}
						//$response = array('cab_service_id' => $cab_service_id,'is_client' => $is_client, 'response' => $response);
					    //$result[$cab_service_name] = $response;
					    curl_close($curl);
					}
				}
				
				//Get Uber Cabs
				
				/*$results = $this->getUber($p_lat,$p_long,$d_lat,$d_long);
				foreach($results as $res)
				{
					$result[] = $res;
				}*/
				
				
				//Cab services which are not client
				
				$cab_services = json_decode($this->getCabServicesByCity($city_id));
				if($cab_services->success == 1)
				{
					$cab_services = $cab_services->response->Services;
					foreach($cab_services as $cab_service)
					{
						$result[] = array('cab_service_id' => $cab_service->id,'is_client' => '0', 'cab_service_name'=>$cab_service->name, 'website' => $cab_service->website,'customer_care_no' => $cab_service->contact_no,'cab_service_logo' => $cab_service->logo, 'cab_service_pkg' => $cab_service->android_package_name,'android_package_name' => $cab_service->android_package_name, 'ios_package_name' => $cab_service->ios_package_name);
					}
				}
				
				if(count($result) > 0)
				{
					//$result = array('access_token' => $token_id,'Cabs'=>$response);
					$success = array('success' => "1", "error" => "","access_token" => $token_id, "response"=>$result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => "No Result Found");
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		private function getAllCabs()
		{
			$token_id =  $this->_request['access_token'];
			if($token_id)
				$user_id = $this->checkUser($token_id);
		
			$pickup_location = $this->_request['pickup_location'];
			$p_latlong = getLatLong($pickup_location);
			$p_lat = $p_latlong['lat'];
			$p_long = $p_latlong['long'];
			
			$drop_location = $this->_request['drop_location'];
			$d_latlong = getLatLong($drop_location);
			$d_lat = $d_latlong['lat'];
			$d_long = $d_latlong['long'];
			
			$pickup_date = $this->_request['pickup_date'];  //YYYY-MM-DD Format
			$pickup_time = $this->_request['pickup_time'];   //HH:MM:SS Format
			$pickup_datetime = $pickup_date . " " . $pickup_time;
			
			$city = getCity($p_lat,$p_long);
			$city_id = $this->getCityId($city);
			
			$result = array();
			try
			{
				// For each apis of type getCabs
				$sql = mysqli_query($this->db, "select cs.id, cs.name, cs.logo, cs.is_client, csa.cab_service_id, csa.api_url, csa.request_parameters, csa.method_type from cab_services_api `csa` inner join cab_services `cs` on csa.cab_service_id = cs.id where csa.api_type = 2");
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$cab_service_name = $rlt['name'];
						$cab_service_id = $rlt['cab_service_id'];
						$cab_service_logo = $rlt['logo'];
						$is_client = $rlt['is_client'];
						$method_type  = $rlt['method_type'];
						$api_url = $rlt['api_url'];
						$req_param[] = explode(',',$rlt['request_parameters']);
						$data = array();
						$sql2 = mysqli_query($this->db, "select meta_key,meta_value from cab_services_api_meta where cab_service_id = ".$rlt['cab_service_id']);
						if(mysqli_num_rows($sql2) > 0)
						{
							while($rlt2 = mysqli_fetch_array($sql2,MYSQL_ASSOC))
							{
								$key = $rlt2['meta_key'];
								$value = $rlt2['meta_value'];
								$data[$key] = $value;
							}
						}
						else
							continue;
						
						$data['start_latitude'] = $p_lat;
						$data['start_longitude'] = $p_long;
						$data['end_latitude'] = $d_lat;
						$data['end_longitude'] = $d_long;
						$data['pickup_datetime'] = $pickup_datetime;
						
						//$curl = curl_init("http://localhost/radioTaxis/abc/public/getCabs");
						$curl = curl_init($api_url);
						
						switch($method_type)
						{
							case "POST":
				            curl_setopt($curl, CURLOPT_POST, TRUE);

				            if ($data)
				                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				            break;
					        case "PUT":
					            curl_setopt($curl, CURLOPT_PUT, 1);
					            break;
					        default:
					            if ($data)
					                $url = sprintf("%s?%s", $url, http_build_query($data));
						}
						
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						$response = json_decode(curl_exec($curl));
						//$response['cab_service_id'] = $cab_service_id;
						//$response['is_client'] = $is_client;
						if($response->success == 1)
					    {
							$response  = $response->response->Cabs;
							foreach($response as $res)
							{
								$result[] = array('cab_service_id' => $cab_service_id,'is_client' => $is_client, 'cab_service_name'=>$cab_service_name, 'cab_service_logo' => $cab_service_logo, 'CabDetail' => $res);
							}
						}
						//$response = array('cab_service_id' => $cab_service_id,'is_client' => $is_client, 'response' => $response);
					    //$result[$cab_service_name] = $response;
					    curl_close($curl);
					}
				}
				
				//Cab services which are not client
				
				$cab_services = json_decode($this->getCabServicesByCity($city_id));
				if($cab_services->success == 1)
				{
					$cab_services = $cab_services->response->Services;
					foreach($cab_services as $cab_service)
					{
						$result[] = array('cab_service_id' => $cab_service->id,'is_client' => '0', 'cab_service_name'=>$cab_service->name, 'website' => $cab_service->website,'customer_care_no' => $cab_service->contact_no, 'cab_service_logo'=>$cab_service->logo, 'cab_service_pkg' => $cab_service->android_package_name,'android_package_name' => $cab_service->android_package_name, 'ios_package_name' => $cab_service->ios_package_name);
					}
				}
				
				if(count($result) > 0)
				{
					//$result = array('access_token' => $token_id,'Cabs'=>$response);
					$success = array('success' => "1", "error" => "","access_token" => $token_id, "response"=>$result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => "No Result Found");
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}


		private function getFareBreakup()
		{
			$cab_service_id = $this->_request['cab_service_id'];
			$pickup_location = $this->_request['pickup_location'];	
			$p_latlng = getLatLong($pickup_location);
			$p_lat = $p_latlng['lat'];
			$p_long = $p_latlng['long'];
			$drop_location = $this->_request['drop_location'];
			$drop_latlng = getLatLong($drop_location);
			$d_lat = $drop_latlng['lat'];
			$d_long = $drop_latlng['long'];
			//$distance = distanceBetweenPoints($p_lat,$p_long,$drop_lat,$drop_long);
			$product_id = $this->_request['product_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "select api_url, request_parameters, method_type from cab_services_api where api_type = 3");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
					$api_url = $rlt['api_url'];
					$method_type = $rlt['method_type'];
					$req_param[] = explode(',',$rlt['request_parameters']);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Details not available');
					$this->response($this->json($response),200);
				}
				$data = array();
				$sql = mysqli_query($this->db, "select meta_key,meta_value from cab_services_api_meta where cab_service_id = ".$cab_service_id);
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$key = $rlt['meta_key'];
						$value = $rlt['meta_value'];
						$data[$key] = $value;
					}
				}
				
				$data['start_latitude'] = $p_lat;
				$data['start_longitude'] = $p_long;
				$data['end_latitude'] = $d_lat;
				$data['end_longitude'] = $d_long;
				$data['product_id'] = $product_id;
				
						
				$curl = curl_init($api_url);
						
				switch($method_type)
				{
					case "POST":
		            curl_setopt($curl, CURLOPT_POST, TRUE);

		            if ($data)
		                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		            break;
			        case "PUT":
			            curl_setopt($curl, CURLOPT_PUT, 1);
			            break;
			        default:
			            if ($data)
			                $api_url = sprintf("%s?%s", $api_url, http_build_query($data));
				}
				
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$response = json_decode(curl_exec($curl));
				curl_close($curl);
				//$response = array('response' => $response);
				if($response)
				{
					if($response->success == 1)
				    {
						$response  = $response->response->FareBreakup;
						$success = array('success' => "1", "error" => "","access_token" => $token_id, "FareBreakup"=>$response);
						$this->response($this->json($success), 200);
					}
					else
					{
						$response = array('success' => "0", 'error' => "Details Not Available");
						$this->response($this->json($response),200);
					}
					
				}
			    else
			    {
					$response = array('success' => "0", 'error' => "Details Not Available");
					$this->response($this->json($response),200);
				}
			   
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		
		private function sendRequestToDriver()
		{
			$token_id = $this->_request['access_token'];	
			$user = $this->checkUser($token_id);
			$user_id = $user['user_id'];
			$contact_no = $user['contact_no'];
			$email = $user['email'];
			$pickup_location = $this->_request['pickup_location'];	
			$drop_location = $this->_request['drop_location'];  //optional
			$p_latlng = getLatLong($pickup_location);
			$p_lat = $p_latlng['lat'];
			$p_long = $p_latlng['long'];
			
			$d_latlng = getLatLong($drop_location);
			$d_lat = $d_latlng['lat'];
			$d_long = $d_latlng['long'];
			
			$product_id = $this->_request['product_id'];  
			$cab_service_id = $this->_request['cab_service_id'];  
			
			
			try
			{
				$sql = mysqli_query($this->db, "select api_url, request_parameters, method_type from cab_services_api where api_type = 4");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
					$api_url = $rlt['api_url'];
					$method_type = $rlt['method_type'];
					$req_param[] = explode(',',$rlt['request_parameters']);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Details not available');
					$this->response($this->json($response),200);
				}
				$data = array();
				$sql = mysqli_query($this->db, "select meta_key,meta_value from cab_services_api_meta where cab_service_id = ".$cab_service_id);
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$key = $rlt['meta_key'];
						$value = $rlt['meta_value'];
						$data[$key] = $value;
					}
				}
				
				$data['start_latitude'] = $p_lat;
				$data['start_longitude'] = $p_long;
				$data['end_latitude'] = $d_lat;
				$data['end_longitude'] = $d_long;
				$data['contact_no'] = $contact_no;
				$data['email'] = $email;
				$data['product_id'] = $product_id;
				
				//$api_url = "http://localhost/radioTaxis/abc/public/sendRequestToDriver";		
				$curl = curl_init($api_url);
						
				switch($method_type)
				{
					case "POST":
		            curl_setopt($curl, CURLOPT_POST, TRUE);

		            if ($data)
		                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		            break;
			        case "PUT":
			            curl_setopt($curl, CURLOPT_PUT, 1);
			            break;
			        default:
			            if ($data)
			                $api_url = sprintf("%s?%s", $api_url, http_build_query($data));
				}
				
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$response = json_decode(curl_exec($curl));
				curl_close($curl);
				//$response = array('response' => $response);
				if($response)
				{
					if($response->success == 1)
				    {
						$response  = $response->response->Details;
						mysqli_query($this->db, "INSERT INTO requests (`user_id`,`cab_service_id`, `cs_request_id`, `product_id`, `created`,`pickup_location`,`pickup_datetime`) values ($user_id,$cab_service_id,'$response->request_id','$product_id',now(),'$pickup_location',now())");
						$request_id = mysqli_insert_id($this->db);
						$result = $response;
						$result->cs_request_id = $response->request_id;
						$result->request_id = "$request_id";
						$success = array('success' => "1", "error" => "","access_token" => $token_id,"response" => array("Details"=>$result));
						$this->response($this->json($success), 200);
					}
					else
					{
						$response = array('success' => "0", 'error' => $response->error);
						$this->response($this->json($response),200);
					}
					
				}
			    else
			    {
					$response = array('success' => "0", 'error' => "Request Failed");
					$this->response($this->json($response),200);
				}
			   
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		private function checkRequest()
		{
			$token_id = $this->_request['access_token'];	
			$user = $this->checkUser($token_id);
			$user_id = $user['user_id'];
			$contact_no = $user['contact_no'];
			$email = $user['email'];
			
			$request_id = $this->_request['request_id'];
			
			
			
			try
			{
				/*$sql = mysqli_query($this->db, "select api_url, request_parameters, method_type from cab_services_api where api_type = 4");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
					$api_url = $rlt['api_url'];
					$method_type = $rlt['method_type'];
					$req_param[] = explode(',',$rlt['request_parameters']);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Details not available');
					$this->response($this->json($response),200);
				}*/
				
				$sql = mysqli_query($this->db, "select cab_service_id, cs_request_id from requests where id = $request_id");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
					$cs_request_id = $rlt['cs_request_id'];
					$cab_service_id = $rlt['cab_service_id'];
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Invalid Request');
					$this->response($this->json($response),200);
				}
				
				$data = array();
				$sql = mysqli_query($this->db, "select meta_key,meta_value from cab_services_api_meta where cab_service_id = ".$cab_service_id);
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$key = $rlt['meta_key'];
						$value = $rlt['meta_value'];
						$data[$key] = $value;
					}
				}
				
				$data['request_id'] = $cs_request_id;
				$data['client_id'] = "ABCCABS00000000001";
				
				$api_url = 	"http://taxivaxi.in/radioT/abc/public/checkRequest";
				//$api_url = 	"http://localhost/radioTaxis/abc/public/checkRequest";
				$method_type = "POST";
				
				$curl = curl_init($api_url);
						
				switch($method_type)
				{
					case "POST":
		            curl_setopt($curl, CURLOPT_POST, TRUE);

		            if ($data)
		                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		            break;
			        case "PUT":
			            curl_setopt($curl, CURLOPT_PUT, 1);
			            break;
			        default:
			            if ($data)
			                $api_url = sprintf("%s?%s", $api_url, http_build_query($data));
				}
				
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$response = json_decode(curl_exec($curl));
				curl_close($curl);
				//$response = array('response' => $response);
				if($response)
				{
					if($response->success == 1)
				    {
						$response  = $response->response;
						
						//Update Request Table Status
						$status = $response->Message;
						mysqli_query($this->db,"UPDATE requests set status = '$status' where id = '$request_id'");
						
						$taxi_details = $response->taxi_details;
						$taxi_model = $taxi_details->model;
						$plate_no = $taxi_details->plate_no;
						
						$driver_details = $response->driver_details;
						$driver_name = $driver_details->name;
						$driver_contact = $driver_details->contact_no;
						
						$sql2 = mysqli_query($this->db,"select * from request_taxis where request_id = '$request_id'");
						
						if(mysqli_num_rows($sql2) <= 0)
						{
							mysqli_query($this->db,"INSERT INTO request_taxis (request_id, taxi_model, plate_no) values ('$request_id','$taxi_model','$plate_no')");
						}
						
						$sql2 = mysqli_query($this->db,"select * from request_drivers where request_id = '$request_id'");
						
						if(mysqli_num_rows($sql2) <= 0)
						{
							mysqli_query($this->db,"INSERT INTO request_drivers (request_id, driver_name, driver_contact) values ('$request_id','$driver_name','$driver_contact')");
						}
						
						$result = array("Message"=>$response->Message, 'notification_for' => $response->notification_for, 'request_id' => $request_id, 'driver_details' => $response->driver_details, 'taxi_details' => $response->taxi_details);
						if($response->ride_details)
							$result['ride_details'] = $response->ride_details;
						if($response->driver_location)
							 $result['driver_location'] = $response->driver_location;
						//$success = array('success' => "1", "error" => "","access_token" => $token_id, "Message"=>$response->Message, 'notification_for' => $response->notification_for, 'request_id' => $request_id, 'driver_details' => $response->driver_details, 'taxi_details' => $response->taxi_details);
						$success = array('success' => "1", "error" => "","access_token" => $token_id, "response" => $result);
						
						if($response->Message == "Driver Accepted")
						{
							$user_details = $this->_getUserInfo($user_id);
							$gcm_regId = $user_details['gcm_regId'];
							$gcm = new GCM();
							$gcm->send_notification(array($gcm_regId),$this->json($success),GOOGLE_TAXIVAXI_USER_API_KEY);
						}
						
						
						$this->response($this->json($success), 200);
					}
					else
					{
						$response = array('success' => "0", 'error' => $response->error);
						$this->response($this->json($response),200);
					}
					
				}
			    else
			    {
					$response = array('success' => "0", 'error' => "Request Failed");
					$this->response($this->json($response),200);
				}
			   
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		private function checkRequestStatus()
		{
			$token_id = $this->_request['access_token'];	
			$user = $this->checkUser($token_id);
			$user_id = $user['user_id'];
			$contact_no = $user['contact_no'];
			$email = $user['email'];
			
			$request_id = $this->_request['request_id'];
			
			
			
			try
			{
				/*$sql = mysqli_query($this->db, "select api_url, request_parameters, method_type from cab_services_api where api_type = 4");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
					$api_url = $rlt['api_url'];
					$method_type = $rlt['method_type'];
					$req_param[] = explode(',',$rlt['request_parameters']);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Details not available');
					$this->response($this->json($response),200);
				}*/
				
				$sql = mysqli_query($this->db, "select cab_service_id, cs_request_id from requests where id = $request_id");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
					$cs_request_id = $rlt['cs_request_id'];
					$cab_service_id = $rlt['cab_service_id'];
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Invalid Request');
					$this->response($this->json($response),200);
				}
				
				$data = array();
				$sql = mysqli_query($this->db, "select meta_key,meta_value from cab_services_api_meta where cab_service_id = ".$cab_service_id);
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$key = $rlt['meta_key'];
						$value = $rlt['meta_value'];
						$data[$key] = $value;
					}
				}
				
				$data['request_id'] = $cs_request_id;
				$data['client_id'] = "ABCCABS00000000001";
				
				$api_url = 	"http://taxivaxi.in/radioT/abc/public/checkRequest";
				//$api_url = 	"http://localhost/radioTaxis/abc/public/checkRequest";
				$method_type = "POST";
				
				$curl = curl_init($api_url);
						
				switch($method_type)
				{
					case "POST":
		            curl_setopt($curl, CURLOPT_POST, TRUE);

		            if ($data)
		                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		            break;
			        case "PUT":
			            curl_setopt($curl, CURLOPT_PUT, 1);
			            break;
			        default:
			            if ($data)
			                $api_url = sprintf("%s?%s", $api_url, http_build_query($data));
				}
				
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$response = json_decode(curl_exec($curl));
				curl_close($curl);
				//$response = array('response' => $response);
				if($response)
				{
					if($response->success == 1)
				    {
						$response  = $response->response;
						
						//Update Request Table Status
						$status = $response->Message;
						mysqli_query($this->db,"UPDATE requests set status = '$status' where id = '$request_id'");	 
						$taxi_details = $response->taxi_details;
						$taxi_model = $taxi_details->model;
						$plate_no = $taxi_details->plate_no;
						
						$driver_details = $response->driver_details;
						$driver_name = $driver_details->name;
						$driver_contact = $driver_details->contact_no;
						
						$sql2 = mysqli_query($this->db,"select * from request_taxis where request_id = '$request_id'");
						
						if(mysqli_num_rows($sql2) <= 0)
						{
							mysqli_query($this->db,"INSERT INTO request_taxis (request_id, taxi_model, plate_no) values ('$request_id','$taxi_model','$plate_no')");
						}
						
						$sql2 = mysqli_query($this->db,"select * from request_drivers where request_id = '$request_id'");
						
						if(mysqli_num_rows($sql2) <= 0)
						{
							mysqli_query($this->db,"INSERT INTO request_drivers (request_id, driver_name, driver_contact) values ('$request_id','$driver_name','$driver_contact')");
						}
						
						$result = array("Message"=>$response->Message, 'notification_for' => $response->notification_for, 'request_id' => $request_id, 'driver_details' => $response->driver_details, 'taxi_details' => $response->taxi_details);
						if($response->ride_details)
							$result['ride_details'] = $response->ride_details;
						if($response->driver_location)
							 $result['driver_location'] = $response->driver_location;
						//$success = array('success' => "1", "error" => "","access_token" => $token_id, "Message"=>$response->Message, 'notification_for' => $response->notification_for, 'request_id' => $request_id, 'driver_details' => $response->driver_details, 'taxi_details' => $response->taxi_details);
						$success = array('success' => "1", "error" => "","access_token" => $token_id, "response" => $result);
						
						$this->response($this->json($success), 200);
					}
					else
					{
						$response = array('success' => "0", 'error' => $response->error);
						$this->response($this->json($response),200);
					}
					
				}
			    else
			    {
					$response = array('success' => "0", 'error' => "Request Failed");
					$this->response($this->json($response),200);
				}
			   
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		private function cancelRequest()
		{
			$token_id = $this->_request['access_token'];	
			$user = $this->checkUser($token_id);
			$user_id = $user['user_id'];
			$contact_no = $user['contact_no'];
			$email = $user['email'];
			
			$request_id = $this->_request['request_id'];	
			
			try
			{
				$sql = mysqli_query($this->db, "select cab_service_id, cs_request_id from requests where id = $request_id");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
					$cs_request_id = $rlt['cs_request_id'];
					$cab_service_id = $rlt['cab_service_id'];
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Invalid Request');
					$this->response($this->json($response),200);
				}
				
				$data = array();
				$sql = mysqli_query($this->db, "select meta_key,meta_value from cab_services_api_meta where cab_service_id = ".$cab_service_id);
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$key = $rlt['meta_key'];
						$value = $rlt['meta_value'];
						$data[$key] = $value;
					}
				}
				
				$data['request_id'] = $cs_request_id;
				
				$api_url = 	"http://taxivaxi.in/radioT/abc/public/cancelRequest";
				//$api_url = 	"http://localhost/radioTaxis/abc/public/checkRequest";
				$method_type = "POST";
				
				$curl = curl_init($api_url);
						
				switch($method_type)
				{
					case "POST":
		            curl_setopt($curl, CURLOPT_POST, TRUE);

		            if ($data)
		                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		            break;
			        case "PUT":
			            curl_setopt($curl, CURLOPT_PUT, 1);
			            break;
			        default:
			            if ($data)
			                $api_url = sprintf("%s?%s", $api_url, http_build_query($data));
				}
				
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$response = json_decode(curl_exec($curl));
				curl_close($curl);
				
				if($response)
				{
					if($response->success == 1)
				    {
						$response  = $response->response;
						
						//Update Request Table Status
						$status = $response->Message;
						mysqli_query($this->db,"UPDATE requests set status = '$status' where id = '$request_id'");
						$result = array("Message"=>$response->Message, 'notification_for' => $response->notification_for, 'request_id' => $request_id, 'user_details' => $response->user_details);
						//$success = array('success' => "1", "error" => "","access_token" => $token_id, "Message"=>$response->Message, 'notification_for' => $response->notification_for, 'request_id' => $request_id, 'driver_details' => $response->driver_details, 'taxi_details' => $response->taxi_details);
						$success = array('success' => "1", "error" => "","access_token" => $token_id, "response" => $result);
						
						$this->response($this->json($success), 200);
					}
					else
					{
						$response = array('success' => "0", 'error' => $response->error);
						$this->response($this->json($response),200);
					}
					
				}
			    else
			    {
					$response = array('success' => "0", 'error' => "Request Failed");
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getDriverLocation()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkAuth($token_id);
			$request_id = $this->_request['request_id'];	
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT pickup_location from requests where id = $request_id");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$pickup = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$pickup_location = $pickup['pickup_location'];
					if(!empty($pickup_location))
					{
						$p_latlng = getLatLong($pickup_location);
						$p_lat = $p_latlng['lat'];
						$p_long = $p_latlng['long'];
					}
				}
				
				$sql = mysqli_query($this->db, "SELECT latitude, longitude, distance from drive_location where request_id = $request_id order by updated_at desc LIMIT 1");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$driver_location = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$lat = $driver_location['latitude'];
					$long = $driver_location['longitude'];
					if($p_lat != NULL && $p_long != NULL)
					{
						$distance = distanceBetweenPoints($p_lat, $p_long, $lat, $long);
						$driver_location['distance'] = "$distance";
						$eta = ($distance/30)*60;  //Assuming 30km/hr average speed
						$driver_location['ETA'] = round($eta,0). " Min";
					}
					
					
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Invalid Request');
					$this->response($this->json($response),200);
				}
				
				$result = array('access_token' => $token_id, 'Message'=>'Driver On the Way', 'request_id' => $request_id, 'driver_location' => $driver_location);
				
				$success = array('success' => "1", "error" => "","response"=>$result);
					
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function reviewDriver()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkUser($token_id);
			$request_id = $this->_request['request_id'];
			$rating = $this->_request['rating'];
			$comment = $this->_request['comment'];
			
			try
			{
				$sql = mysqli_query($this->db, "select cab_service_id, cs_request_id from requests where id = $request_id");
				if(mysqli_num_rows($sql) > 0)
				{
					$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
					$cs_request_id = $rlt['cs_request_id'];
					$cab_service_id = $rlt['cab_service_id'];
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Invalid Request');
					$this->response($this->json($response),200);
				}
				
				$data = array();
				$sql = mysqli_query($this->db, "select meta_key,meta_value from cab_services_api_meta where cab_service_id = ".$cab_service_id);
				if(mysqli_num_rows($sql) > 0)
				{
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$key = $rlt['meta_key'];
						$value = $rlt['meta_value'];
						$data[$key] = $value;
					}
				}
				
				$data['request_id'] = $cs_request_id;
				$data['rating'] = $rating;
				$data['comment'] = $comment;
				
				$api_url = 	"http://taxivaxi.in/radioT/abc/public/reviewDriver";
				//$api_url = 	"http://localhost/radioTaxis/abc/public/checkRequest";
				$method_type = "POST";
				
				$curl = curl_init($api_url);
						
				switch($method_type)
				{
					case "POST":
		            curl_setopt($curl, CURLOPT_POST, TRUE);

		            if ($data)
		                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		            break;
			        case "PUT":
			            curl_setopt($curl, CURLOPT_PUT, 1);
			            break;
			        default:
			            if ($data)
			                $api_url = sprintf("%s?%s", $api_url, http_build_query($data));
				}
				
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$response = json_decode(curl_exec($curl));
				curl_close($curl);
				
				if($response)
				{
					if($response->success == 1)
				    {
						$response  = $response->response;
						
						$result = array("Message"=>$response->Message, 'request_id' => $request_id);
						
						$success = array('success' => "1", "error" => "","access_token" => $token_id, "response" => $result);
						
						$this->response($this->json($success), 200);
					}
					else
					{
						$response = array('success' => "0", 'error' => $response->error);
						$this->response($this->json($response),200);
					}
					
				}
			    else
			    {
					$response = array('success' => "0", 'error' => "Request Failed");
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		
		
		private function getCabServicesByCity($city_id)
		{
			//$token_id = $this->_request['access_token'];	
			//$user_id = $this->checkAuth($token_id);
			//$city_id = $this->_request['city_id'];	
			
			try
			{
				$query = "SELECT * from cab_services where FIND_IN_SET($city_id,cities)>0";
				$sql = mysqli_query($this->db, $query);
				
				
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
				
					while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC))
					{
						$city_code = $this->_getCityDetails($city_id,'std_code');
						$rlt['contact_no'] = $city_code.$rlt['contact_no'];
						$result[] = $rlt;
					}
					$result = array('Services'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					return json_encode($success);
				} 
				else
				{
					$response = array('success' => "0", 'error' => "No Result Found");
					return json_encode($response);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				return json_encode($response);
			}
		}
		
		
		private function getUserInfo()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkAuth($token_id);
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT u.* FROM users as `u` WHERE u.id = $user_id LIMIT 1");
				
				
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$user_id = $result['user_id'];
					$total_photos = 0;
					if($result['image']!=NULL && $result['image'] != "")
					{
						$base_url = BASE_URL."users/";
						$img_url = $base_url.$result['image'];
						$result['image'] = $img_url;
						$total_photos = 1;
					}
					
					
					$result = array('access_token' => $token_id,'user'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => "No Result Found");
					$this->response($this->json($response),200);
				}					
			}
			catch(Exception $e)
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),406);
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
		
			
		
		private function myBookings()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkAuth($token_id);
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
		
		
		
		public function checkUser($token_id)
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			if(empty($token_id))
			{
				$response = array('success' => "0", 'error' => "Access Token Empty");
				$this->response($this->json($response),200);
			}
			else
			{
				$sql = mysqli_query($this->db, "select ud.user_id,ud.contact_no,u.email from authenticate `a` inner join user_details `ud` on a.user_id = ud.user_id inner join users `u` on u.id = ud.user_id where token_id = '$token_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$user = mysqli_fetch_array($sql,MYSQL_ASSOC);
					//$user_id = $user['user_id'];
					//return $user_id;
					return $user;
				}
				else
				{
					$response = array('success' => "0", 'error' => "Access Token Invalid");
					$this->response($this->json($response),200);
				}
			}
		}
		
		public function checkClientAuth($secret_key)
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			if(empty($secret_key))
			{
				$response = array('success' => "0", 'error' => "Secret Key Empty");
				$this->response($this->json($response),200);
			}
			else
			{
				$sql = mysqli_query($this->db, "select id from clients where md5(`secret_key`) = '$secret_key'");
				if(mysqli_num_rows($sql) > 0)
				{
					$client = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$client_id = $client['id'];
					return $client_id;
				}
				else
				{
					$response = array('success' => "0", 'error' => "Secret Key Invalid");
					$this->response($this->json($response),200);
				}
			}
		}
		
		private function _getUserInfo($user_id)
		{
			$sql = mysqli_query($this->db, "SELECT name, image, contact_no, gender,dob, gcm_regId FROM user_details WHERE user_id = $user_id LIMIT 1");
					
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
		
		private function getCityId($city_name)
		{
			$sql = mysqli_query($this->db, "SELECT id FROM cities WHERE name = '$city_name' LIMIT 1");
			if(mysqli_num_rows($sql) > 0)
			{
				$res = mysqli_fetch_array($sql,MYSQL_ASSOC);
				$city_id = $res['id'];
				return $city_id;
			}
			return 0;
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
	
	// Initiate Library
	$api = new API;
	$api->processApi();
	
?>