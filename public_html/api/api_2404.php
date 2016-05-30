<?php
    error_reporting(E_ERROR | E_PARSE);
    include "common.php";
    require_once("db.php");
    include "GCM.php";
	require_once("Rest.inc.php");
	require_once('phpMailer/class.phpmailer.php');
	
	define("BASE_URL_IMG","http://taxivaxi.in/business/");
	define("BASE_URL","http://taxivaxi.in/business/");
	define("GOOGLE_TAXI_OPERATOR_API_KEY", "AIzaSyAdUx4u8uS65Q2aKDJsHcWs4jVmpYXv66U"); // Place your Google API Key
	define("GOOGLE_TAXI_OPERATOR_SENDER_ID", "499789922951");

	// define("TAXIVAXI_NUMBER", "7891022360");
	define("TAXIVAXI_NUMBER", "7291995227, 9990045853, 9990045953");
	define("TAXIVAXI_EMAIL", "corporate.oe@taxivaxi.com");
	
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

			//-------------------------------PROFILES------------------------------//

		/*private function getAllCodes()
		{
			$token_id = $this->_request['access_token'];	
			$employee_id = $this->checkEmployee($token_id);

			$admin_id = $this->_request['admin_id'];

			try
			{
				$sql = mysqli_query($this->db, "select * from assessment_codes where admin_id='$admin_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						echo ":hhg";
						array_push($result, $rlt);
						// $result[] = $rlt;
					}
					$result2 = array('access_token' => $token_id,'AssCodes'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result2);
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

		public function getAllDeactivatedSpocs()
		{
			$access_token = $this->_request['access_token'];
			$admin_id = $this->checkAdmin($access_token);

			try
			{
				$sql = mysqli_query($this->db, "SELECT * from users
					where status = '0' and admin_id = '$admin_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $access_token,'Dspocs'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function viewPeopleProfile()
		{
			$people_id = $this->_request['people_id'];

			try
			{
				$sql = mysqli_query($this->db, "SELECT * from people
					where id = '$people_id'");
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

		private function viewSpocProfile()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkEmployee($token_id);

			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					u.*,
					a.corporate_name,
					g.group_name,
					sg.subgroup_name 
					from users `u`
					left join admins `a` on u.admin_id = a.id
					left join groups `g` on u.group_id = g.id
					left join subgroups `sg` on u.subgroup_id = sg.id
					where u.id = '$user_id'");
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

		private function spocChangePassword()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkEmployee($token_id);
			
			$current_password = $this->_request['current_password'];
			$new_password = $this->_request['new_password'];
			$confirm_new_password = $this->_request['confirm_new_password'];
			
			
			try
			{
				$sql = mysqli_query($this->db, "select id from users where id = $user_id and password = md5('$current_password')");
				
				//If password match
				if(mysqli_num_rows($sql) > 0)
				{
					if($new_password != $confirm_new_password)
					{
						$response = array('success' => "0", 'error' => 'New Password and Re-Password Doed Not Match!!');
						$this->response($this->json($response),200);
					}
					try
					{
						$query = "UPDATE users set password = md5('$new_password') WHERE id = $user_id";
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

		private function viewAuthtwoProfile()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthtwo($token_id);

			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					ga.*,
					a.corporate_name,
					g.group_name
					from group_authenticater `ga`
					left join groups `g` on ga.group_id = g.id
					left join admins `a` on g.admin_id = a.id
					where ga.id = '$auth_id'");
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

		private function authtwoChangePassword()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthtwo($token_id);
			
			$current_password = $this->_request['current_password'];
			$new_password = $this->_request['new_password'];
			$confirm_new_password = $this->_request['confirm_new_password'];
			
			
			try
			{
				$sql = mysqli_query($this->db, "select id from group_authenticater where id = $auth_id and password = md5('$current_password')");
				
				//If password match
				if(mysqli_num_rows($sql) > 0)
				{
					if($new_password != $confirm_new_password)
					{
						$response = array('success' => "0", 'error' => 'New Password and Re-Password Doed Not Match!!');
						$this->response($this->json($response),200);
					}
					try
					{
						$query = "UPDATE group_authenticater set password = md5('$new_password') WHERE id = $auth_id";
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

		private function viewAuthoneProfile()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthone($token_id);

			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					sga.*,
					a.corporate_name,
					g.group_name,
					sg.subgroup_name
					from subgroup_authenticater `sga`
					left join subgroups `sg` on sga.subgroup_id = sg.id
					left join groups `g` on sg.group_id = g.id
					left join admins `a` on sg.admin_id = a.id
					where sga.id = '$auth_id'");
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

		private function authoneChangePassword()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthone($token_id);
			
			$current_password = $this->_request['current_password'];
			$new_password = $this->_request['new_password'];
			$confirm_new_password = $this->_request['confirm_new_password'];
			
			
			try
			{
				$sql = mysqli_query($this->db, "select id from subgroup_authenticater where id = $auth_id and password = md5('$current_password')");
				
				//If password match
				if(mysqli_num_rows($sql) > 0)
				{
					if($new_password != $confirm_new_password)
					{
						$response = array('success' => "0", 'error' => 'New Password and Re-Password Doed Not Match!!');
						$this->response($this->json($response),200);
					}
					try
					{
						$query = "UPDATE subgroup_authenticater set password = md5('$new_password') WHERE id = $auth_id";
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

		private function viewAdminProfile()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAdmin($token_id);

			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					a.*
					from admins `a`
					where a.id = '$auth_id'");
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

		private function adminChangePassword()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAdmin($token_id);
			
			$current_password = $this->_request['current_password'];
			$new_password = $this->_request['new_password'];
			$confirm_new_password = $this->_request['confirm_new_password'];
			
			
			try
			{
				$sql = mysqli_query($this->db, "select id from admins where id = $auth_id and password = md5('$current_password')");
				
				//If password match
				if(mysqli_num_rows($sql) > 0)
				{
					if($new_password != $confirm_new_password)
					{
						$response = array('success' => "0", 'error' => 'New Password and Re-Password Doed Not Match!!');
						$this->response($this->json($response),200);
					}
					try
					{
						$query = "UPDATE admins set password = md5('$new_password') WHERE id = $auth_id";
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

			//----------------------------------HOME------------------------------//

	private function adminHome()
	{
		$token_id = $this->_request['access_token'];	
		$admin_id = $this->checkAdmin($token_id);

		try
		{
			$sql = mysqli_query($this->db, "SELECT count(*) as `bk_count` from bookings 
				where (status_auth1 = 1 or status_auth2 = 1) and status_user = 1
				and admin_id = '$admin_id'");
			if(mysqli_num_rows($sql) > 0){
				$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
			}
			else{
				$result['bk_count'] = 0;
			}	

			$sql2 = mysqli_query($this->db, "SELECT count(*) as `invoice_count`, sum(i.sub_total) as `invoice_amount` 
				from bookings `b` 
				left join invoice `i` on b.invoice_id = i.id 
				where b.admin_id = '$admin_id' and b.is_invoice = '1'");
			if(mysqli_num_rows($sql2) > 0){
				$result2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);
			}
			else{
				$result2['invoice_amount'] = 0;
			}

			$sql3 = mysqli_query($this->db, "SELECT count(*) as `un_ass_bk_count` 
				from bookings 
				where ((status_auth1 = 1 or status_auth2 = 1) and status_user = 1 and status_auth_taxivaxi != 2) and is_assign=0 
				and is_invoice=0 and admin_id='$admin_id'");
			if(mysqli_num_rows($sql3) > 0){
				$result3 = mysqli_fetch_array($sql3,MYSQL_ASSOC);
			}
			else{
				$result3['un_ass_bk_count'] = 0;
			}

			//Bus
			$sql4 = mysqli_query($this->db, "SELECT count(*) as `bk_count` 
				from bus_bookings 
				where (status_auth1 = 1 or status_auth2 = 1) and status_spoc = 1 and admin_id='$admin_id' ");
			if(mysqli_num_rows($sql4) > 0){
				$result4 = mysqli_fetch_array($sql4,MYSQL_ASSOC);
			}
			else{
				$result4['bk_count'] = 0;
			}	

			$sql5 = mysqli_query($this->db, "SELECT count(*) as `invoice_count`,sum(i.sub_total) as `invoice_amount` 
				from bus_bookings `b` 
				left join bus_invoice `i` on b.invoice_id = i.id
				where  b.is_invoice = '1' and b.admin_id='$admin_id' ");
			if(mysqli_num_rows($sql5) > 0)
			{
				$result5 = mysqli_fetch_array($sql5,MYSQL_ASSOC);
			}
			else
			{
				$result5['invoice_amount'] = 0;
			}

			// $bk_time = strtotime($rlt['pickup_datetime']) + (12*60*60);
			$sql6 = mysqli_query($this->db, "SELECT count(*) as `un_ass_bk_count` 
				from bus_bookings 
				where ((status_auth1 = 1 or status_auth2 = 1) and status_spoc = 1 and status_auth_taxivaxi != 2) and is_assign=0
				and admin_id = '$admin_id'				 
				");
			if(mysqli_num_rows($sql6) > 0)
			{
				$result6 = mysqli_fetch_array($sql6,MYSQL_ASSOC);
			}
			else
			{
				$result6['un_ass_bk_count'] = 0;
			}

			$result = array('access_token' => $token,'Bookings'=>$result, 'Amount' => $result2, 'Unassigned'=>$result3,
				'BusBookings'=>$result4, 'BusAmount' => $result5, 'BusUnassigned'=>$result6);
			$success = array('success' => "1", "error" => "","response"=>$result);
			$this->response($this->json($success), 200);
		}
		catch(Exception $e)
		{
			$response = array('success' => "0", 'error' => $e->getMessage());
			$this->response($this->json($response),200);
		}
	}
	
	private function authoneHome()
	{
		$token_id = $this->_request['access_token'];	
		$auth_id = $this->checkAuthone($token_id);

		try
		{
			$sql = mysqli_query($this->db, "SELECT count(*) as `bk_count` 
				from bookings `b`
				left join subgroup_authenticater `sa` on b.subgroup_id = sa.subgroup_id
				where sa.id = '$auth_id' and b.status_user = '1' and (b.status_auth_taxivaxi ='1' 
					or b.status_auth_taxivaxi ='0' or b.status_auth_taxivaxi = '3')");
			if(mysqli_num_rows($sql) > 0)
			{
				$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
			}
			else
			{
				$result['bk_count'] = 0;
			}



			$sql2 = mysqli_query($this->db, "SELECT count(*) as `invoice_count`,sum(i.sub_total) as `invoice_amount` 
				from bookings `b` 
				left join invoice `i` on b.invoice_id = i.id 
				left join subgroup_authenticater `sa` on b.subgroup_id = sa.subgroup_id
				where sa.id = '$auth_id' and b.is_invoice = '1'");
			if(mysqli_num_rows($sql2) > 0)
			{
				$result2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);
			}
			else
			{
				$result2['invoice_amount'] = 0;
			}

			$result = array('access_token' => $token,'Bookings'=>$result, 'Amount' => $result2,);
			$success = array('success' => "1", "error" => "","response"=>$result);
			$this->response($this->json($success), 200);
		}
		catch(Exception $e)
		{
			$response = array('success' => "0", 'error' => $e->getMessage());
			$this->response($this->json($response),200);
		}
	}

	private function authtwoHome()
	{
		$token_id = $this->_request['access_token'];	
		$auth_id = $this->checkAuthtwo($token_id);

		try
		{
			$sql = mysqli_query($this->db, "SELECT count(*) as `bk_count` 
				from bookings `b`
				left join group_authenticater `sa` on b.group_id = sa.group_id
				where sa.id = '$auth_id' and b.status_user = '1' and (b.status_auth_taxivaxi ='1' 
					or b.status_auth_taxivaxi ='0' or b.status_auth_taxivaxi = '3')");

			if(mysqli_num_rows($sql) > 0)
			{
				$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
			}
			else
			{
				$result['bk_count'] = 0;
			}	

			$sql2 = mysqli_query($this->db, "SELECT count(*) as `invoice_count`,sum(i.sub_total) as `invoice_amount` 
				from bookings `b` 
				left join invoice `i` on b.invoice_id = i.id 
				left join group_authenticater `sa` on b.group_id = sa.group_id
				where sa.id = '$auth_id' and b.is_invoice = '1'");
			if(mysqli_num_rows($sql2) > 0)
			{
				$result2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);
			}
			else
			{
				$result2['invoice_amount'] = 0;
			}

			$result = array('access_token' => $token,'Bookings'=>$result, 'Amount' => $result2,);
			$success = array('success' => "1", "error" => "","response"=>$result);
			$this->response($this->json($success), 200);
		}
		catch(Exception $e)
		{
			$response = array('success' => "0", 'error' => $e->getMessage());
			$this->response($this->json($response),200);
		}
	}
	

	private function taxivaxiHome()
	{
		$token_id = $this->_request['access_token'];	
		$auth_id = $this->checkTaxivaxiAdmin($token_id);

		try
		{
			$sql = mysqli_query($this->db, "SELECT count(*) as `bk_count` 
				from bookings 
				where (status_auth1 = 1 or status_auth2 = 1) and status_user = 1 ");
			if(mysqli_num_rows($sql) > 0)
			{
				$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
			}
			else
			{
				$result['bk_count'] = 0;
			}	

			$sql2 = mysqli_query($this->db, "SELECT count(*) as `invoice_count`,sum(i.sub_total) as `invoice_amount` 
				from bookings `b` 
				left join invoice `i` on b.invoice_id = i.id
				where  b.is_invoice = '1'");
			if(mysqli_num_rows($sql2) > 0)
			{
				$result2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);
			}
			else
			{
				$result2['invoice_amount'] = 0;
			}

			// $bk_time = strtotime($rlt['pickup_datetime']) + (12*60*60);
			$sql3 = mysqli_query($this->db, "SELECT count(*) as `un_ass_bk_count` 
				from bookings 
				where ((status_auth1 = 1 or status_auth2 = 1) and status_user = 1 and status_auth_taxivaxi != 2) and is_assign=0 
				and is_invoice=0");
			if(mysqli_num_rows($sql3) > 0)
			{
				$result3 = mysqli_fetch_array($sql3,MYSQL_ASSOC);
			}
			else
			{
				$result3['un_ass_bk_count'] = 0;
			}

			//Bus

			$sql4 = mysqli_query($this->db, "SELECT count(*) as `bk_count` 
				from bus_bookings 
				where (status_auth1 = 1 or status_auth2 = 1) and status_spoc = 1 ");
			if(mysqli_num_rows($sql4) > 0)
			{
				$result4 = mysqli_fetch_array($sql4,MYSQL_ASSOC);
			}
			else
			{
				$result4['bk_count'] = 0;
			}	

			$sql5 = mysqli_query($this->db, "SELECT count(*) as `invoice_count`,sum(i.sub_total) as `invoice_amount` 
				from bus_bookings `b` 
				left join bus_invoice `i` on b.invoice_id = i.id
				where  b.is_invoice = '1'");
			if(mysqli_num_rows($sql5) > 0)
			{
				$result5 = mysqli_fetch_array($sql5,MYSQL_ASSOC);
			}
			else
			{
				$result5['invoice_amount'] = 0;
			}

			// $bk_time = strtotime($rlt['pickup_datetime']) + (12*60*60);
			$sql6 = mysqli_query($this->db, "SELECT count(*) as `un_ass_bk_count` 
				from bus_bookings 
				where ((status_auth1 = 1 or status_auth2 = 1) and status_spoc = 1 and status_auth_taxivaxi != 2) and is_assign=0 
				");
			if(mysqli_num_rows($sql6) > 0)
			{
				$result6 = mysqli_fetch_array($sql6,MYSQL_ASSOC);
			}
			else
			{
				$result6['un_ass_bk_count'] = 0;
			}

			$result = array('access_token' => $token,'Bookings'=>$result, 'Amount' => $result2, 'Unassigned'=>$result3,
				'BusBookings'=>$result4, 'BusAmount' => $result5, 'BusUnassigned'=>$result6);
			$success = array('success' => "1", "error" => "","response"=>$result);
			$this->response($this->json($success), 200);
		}
		catch(Exception $e)
		{
			$response = array('success' => "0", 'error' => $e->getMessage());
			$this->response($this->json($response),200);
		}
	}

	private function spocHome()
	{
		$token_id = $this->_request['access_token'];	
		$auth_id = $this->checkEmployee($token_id);

		try
		{
			$sql = mysqli_query($this->db, "SELECT count(*) as `bk_count` 
				from bookings `b`
				left join user_bookings `ub` on b.id = ub.booking_id
				left join users `u` on ub.user_id = u.id
				where u.id = '$auth_id' ");
			$result = mysqli_fetch_array($sql,MYSQL_ASSOC);

			$sql2 = mysqli_query($this->db, "SELECT count(*) as `invoice_count`,sum(i.sub_total) as `invoice_amount` 
				from bookings `b` 
				left join invoice `i` on b.invoice_id = i.id
				left join user_bookings `ub` on b.id = ub.booking_id
				left join users `u` on ub.user_id = u.id
				where u.id = '$auth_id' and b.is_invoice = '1'");
			$result2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);

			$result = array('access_token' => $token,'Bookings'=>$result, 'Amount' => $result2,);
			$success = array('success' => "1", "error" => "","response"=>$result);
			$this->response($this->json($success), 200);
		}
		catch(Exception $e)
		{
			$response = array('success' => "0", 'error' => $e->getMessage());
			$this->response($this->json($response),200);
		}
	}

	//----------------------------------COMPANY - Profile------------------------------//

		private function addDutySlip()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkTaxivaxiAdmin($token_id);
			
			// Request Parameters 
			$booking_id = $this->_request['booking_id'];
			$img_string = $this->_request['img_string'];
			$img_ext = $this->_request['img_ext'];
			
			try
			{
				$img_name = $this->save_image($img_string,$img_ext,'duty_slips',$booking_id);
				$image_url = BASE_URL_IMG."images/duty_slips/".$img_name;
				$query = "update bookings set duty_slip = '$image_url' where id = '$booking_id'";
				mysqli_query($this->db, $query);
				
				$result = array('booking_id' => $booking_id, 'image' => $image_url);
				$result = array('access_token' => $token_id,'booking'=>$result);
				$success = array('success' => "1", 'message' => "Duty Slip added successfully", 'error' => "","response"=>$result);
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function addBusTicket()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkTaxivaxiAdmin($token_id);
			
			// Request Parameters 
			$invoice_id = $this->_request['invoice_id'];
			$img_string = $this->_request['img_string'];
			$img_ext = $this->_request['img_ext'];
			
			try
			{
				$img_name = $this->save_image($img_string,$img_ext,'bus_tickets',$invoice_id);
				$image_url = BASE_URL_IMG."images/bus_tickets/".$img_name;
				$query = "update bus_invoice set ticket = '$image_url' where id = '$invoice_id'";
				mysqli_query($this->db, $query);
				
				$result = array('invoice_id' => $invoice_id, 'image' => $image_url);
				$result = array('access_token' => $token_id,'invoice'=>$result);
				$success = array('success' => "1", 'message' => "BUS Ticket added successfully", 'error' => "","response"=>$result);
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		private function addAdminProfileImage()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			
			// Request Parameters
			$img_string = $this->_request['img_string'];
			$img_ext = $this->_request['img_ext'];
			
			try
			{
				$img_name = $this->save_image($img_string,$img_ext,'admin_profile_images',$booking_id);
				$image_url = BASE_URL_IMG."images/admin_profile_images/".$img_name;
				$query = "update admins set profile_image = '$image_url' where id = '$admin_id'";
				mysqli_query($this->db, $query);
				
				$result = array('admin_id' => $admin_id, 'image' => $image_url);
				$result = array('access_token' => $token_id,'booking'=>$result);
				$success = array('success' => "1", 'message' => "Profile Image added successfully", 'error' => "","response"=>$result);
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function addSpocProfileImage()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkEmployee($token_id);
			
			// Request Parameters
			$img_string = $this->_request['img_string'];
			$img_ext = $this->_request['img_ext'];
			
			try
			{
				$img_name = $this->save_image($img_string,$img_ext,'spoc_profile_images',$booking_id);
				$image_url = BASE_URL_IMG."images/spoc_profile_images/".$img_name;
				$query = "update users set profile_image = '$image_url' where id = '$admin_id'";
				mysqli_query($this->db, $query);
				
				$result = array('admin_id' => $admin_id, 'image' => $image_url);
				$result = array('access_token' => $token_id,'booking'=>$result);
				$success = array('success' => "1", 'message' => "Profile Image added successfully", 'error' => "","response"=>$result);
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function addAuthoneProfileImage()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAuthone($token_id);
			
			// Request Parameters
			$img_string = $this->_request['img_string'];
			$img_ext = $this->_request['img_ext'];
			
			try
			{
				$img_name = $this->save_image($img_string,$img_ext,'authone_profile_images',$booking_id);
				$image_url = BASE_URL_IMG."images/authone_profile_images/".$img_name;
				$query = "update subgroup_authenticater set profile_image = '$image_url' where id = '$admin_id'";
				mysqli_query($this->db, $query);
				
				$result = array('admin_id' => $admin_id, 'image' => $image_url);
				$result = array('access_token' => $token_id,'booking'=>$result);
				$success = array('success' => "1", 'message' => "Profile Image added successfully", 'error' => "","response"=>$result);
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function addAuthtwoProfileImage()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAuthtwo($token_id);
			
			// Request Parameters
			$img_string = $this->_request['img_string'];
			$img_ext = $this->_request['img_ext'];
			
			try
			{
				$img_name = $this->save_image($img_string,$img_ext,'authone_profile_images',$booking_id);
				$image_url = BASE_URL_IMG."images/authone_profile_images/".$img_name;
				$query = "update group_authenticater set profile_image = '$image_url' where id = '$admin_id'";
				mysqli_query($this->db, $query);
				
				$result = array('admin_id' => $admin_id, 'image' => $image_url);
				$result = array('access_token' => $token_id,'booking'=>$result);
				$success = array('success' => "1", 'message' => "Profile Image added successfully", 'error' => "","response"=>$result);
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//--------------------------------------------OLA---------------------------------------------//
		private function olaBook()
		{
			$token_id = $this->_request['access_token'];
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$booking_id = $this->_request['booking_id'];

			try
			{
				$sql = mysqli_query($this->db, "select * from bookings where id='$booking_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$address = $result['pickup_location'];
					$latlng = getLatLong($address);
					$pickup_lat = floatval($latlng['lat']);
					$pickup_lng = floatval($latlng['long']);
					$pickup_mode = 'NOW';
					$category = 'sedan';
					$r = olaMakeBooking($pickup_lat, $pickup_lng, $pickup_mode, $category);
					//If Sedan not available, check for Mini and Prime
					if($r['status'] != '')
					{
						$category = 'mini';
						$r = olaMakeBooking($pickup_lat, $pickup_lng, $pickup_mode, $category);
						if($r['status'] != '')
						{
							$category = 'prime';
							$r = olaMakeBooking($pickup_lat, $pickup_lng, $pickup_mode, $category);
						}
					}
					
					//Success
					if($r['status'] == '')
					{
						$crn 			= $r['crn'];
					    $driver_name 	= $r['driver_name'];
					    $driver_number 	= $r['driver_number'];
					    $cab_type 		= $r['cab_type'];
					    $cab_number 	= $r['cab_number'];
					    $car_model 		= $r['car_model'];
					    $eta 			= $r['eta'];

						mysqli_query($this->db, "UPDATE bookings SET 
							reference_no='$crn', operator_name='OLA', driver_name='$driver_name',
							driver_contact='$driver_number', taxi_type_name='$cab_type',
							taxi_reg_no = '$cab_number', taxi_model_name='$car_model', is_assign = 1, 
							status_auth_taxivaxi = 3,
							status = 'Assigned', assign_date=now() where id='$booking_id'");

						$sqlb = mysqli_query($this->db, "SELECT 
							b.*, 
							r.package_name, 
							a.contact_name `tcs_name`, a.contact_no `tcs_no` 
							from bookings `b` 
							left join rates `r` on b.rate_id = r.id 
							left join admins `a` on b.admin_id = a.id
							where b.id = '$booking_id' ");
						$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);
							
						//For sms to tcs
						$booking_details['tcs_name'] = $resultb['tcs_name'];
						$booking_details['tcs_no'] = $resultb['tcs_no'];
						
						//Generete common varialble for email
						// $pickup_datetime = date();
						$booking_details['id'] = $resultb['id'];
						$booking_details['reference_no'] = $resultb['reference_no'];
						$booking_details['pickup_location'] = $resultb['pickup_location'];
						$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];

						if($resultb['tour_type'] == '0')
						{
							$booking_details['trip'] = 'Radio Taxi';
						}
						elseif($resultb['tour_type'] == '1')
						{
							$booking_details['trip'] = 'Local Package';	
						}
						else
						{
							$booking_details['trip'] = 'Outstation';
						}


						if($resultb['tour_type'] == '0')
						{
							$booking_details['package'] = 'Pick-Drop';
						}
						elseif($resultb['tour_type'] == '1')
						{
							$booking_details['package'] = $resultb['package_name'];	
						}
						else
						{
							$booking_details['package'] = $resultb['days'] . " Days";
						}
						
						$booking_details['taxi_model_name'] = $resultb['taxi_model_name'];
						$booking_details['taxi_type_name'] = $resultb['taxi_type_name'];
						$booking_details['taxi_reg_no'] = $resultb['taxi_reg_no'];
						$booking_details['driver_name'] = $resultb['driver_name'];
						$booking_details['driver_contact'] = $resultb['driver_contact'];
						

						//Generate spoc details for email
						$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
						$sqlt = mysqli_query($this->db, "SELECT 
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

			            //Communicate to all spocs
						for($i=1; $i<=1; $i++)
			            {	
							$mail_body = assignMailToSpoc($booking_details, $i, $user_details, $employee_details);
							sendEmail($booking_details['email'],"TVTCS".$booking_details['id']." - TCS Booking Assigned Successfully!!",$mail_body);
			            }

			            //Communicate to all employees
			            $sqlt = mysqli_query($this->db, "SELECT 
							p.people_cid, p.people_name, p.people_email, p.people_contact
							from people_bookings `pb`
							left join people `p` on pb.people_id = p.id
							where pb.booking_id = '$booking_id'");
						while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
						{	
							$mail_body = assignMailToEmployee($booking_details, $i, $user_details, $employee_details);
							sendEmail($resultt['people_email'],"TVTCS".$booking_details['id']." - TCS Booking Assigned Successfully!!",$mail_body);
			            }
			            
						//SMS to spoc
						$m = "Dear ".$booking_details['name'].",\n\nFor your booking with id TVTCS".$booking_id.".\nDriver: ".$driver_name." (".$driver_number.")\nTaxi: ".$car_model." (".$cab_number.")\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi";
						sendESMS($booking_details['user_contact'], $m);
						sendESMS("9881102875", $sms_text);
						
						//SMS to all Employees
			            $sqlt = mysqli_query($this->db, "SELECT 
							p.people_cid, p.people_name, p.people_email, p.people_contact
							from people_bookings `pb`
							left join people `p` on pb.people_id = p.id
							where pb.booking_id = '$booking_id'");
						while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
						{	
							$n = $resultt['people_name'];

							$m = "Dear ".$n.",\n\nFor your booking with id TVTCS".$booking_id.".\nDriver: ".$driver_name." (".$driver_number.")\nTaxi: ".$car_model." (".$cab_number.")\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi";
							sendESMS($resultt['people_contact'], $m);
			            }

					
						$success = array('success' => "1", "error" => "","response"=>'Booked Successfully with Ref No:CRN'.$crn.'!!');
						$this->response($this->json($success), 200);
					}
					else
					{
						$success = array('success' => "0", "error" => $r['message']);
						$this->response($this->json($success), 200);
					}
				}
				else
				{
					$response = array('success' => "0", 'error' => "No such Booking Found");
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function olaCancelbook()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkTaxivaxiAdmin($token_id);
			$booking_id = $this->_request['booking_id'];

			try
			{
				$sql = mysqli_query($this->db, "select * from bookings where id='$booking_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$crn = $result['reference_no'];
					$r = olaCancelBooking($crn);

					//Success
					if($r['status'] == '1')
					{
						mysqli_query($this->db, "UPDATE bookings SET 
							operator_name='', driver_name='Cancelled',
							driver_contact='Cancelled', taxi_type_name='Cancelled',
							taxi_reg_no = 'Cancelled', taxi_model_name='Cancelled', is_assign = 0,
							status_auth_taxivaxi = 1,
							status = '' where id='$booking_id'");

						$success = array('success' => "1", "error" => "","response"=>$r['message']);
						$this->response($this->json($success), 200);
					}
					else
					{
						$success = array('success' => "0", "error" => $r['message']);
						$this->response($this->json($success), 200);
					}
				}
				else
				{
					$response = array('success' => "0", 'error' => "No such Booking Found");
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		public function getRadioEstimates()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkEmployee($token_id);

			$pickup_location = $this->_request['pickup_location'];
			$drop_location = $this->_request['drop_location'];

			$latlng = getLatLong($pickup_location);
			$pickup_lat = floatval($latlng['lat']);
			$pickup_lng = floatval($latlng['long']);

			$latlng = getLatLong($drop_location);
			$drop_lat = floatval($latlng['lat']);
			$drop_lng = floatval($latlng['long']);

			$r = olaRadioEstimates($pickup_lat, $pickup_lng, $drop_lat, $drop_lng);
			return $r;
		}

		private function assignRadioBooking()
		{
			$token_id = $this->_request['access_token'];
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$booking_id = $this->_request['booking_id'];

			$reference_no = $this->_request['reference_no'];
			$operator_name = $this->_request['operator_name'];

			$driver_name = $this->_request['driver_name'];
			$driver_contact = $this->_request['driver_contact'];

			$taxi_type_name = $this->_request['taxi_type_name'];
			$taxi_reg_no = $this->_request['taxi_reg_no'];
			$taxi_model_name = $this->_request['taxi_model_name'];

			try
			{
				mysqli_query($this->db, "UPDATE bookings SET 
					reference_no='$reference_no', operator_name='$operator_name', driver_name='$driver_name',
					driver_contact='$driver_contact', taxi_type_name='$taxi_type_name',
					taxi_reg_no = '$taxi_reg_no', taxi_model_name='$taxi_model_name', is_assign = 1, 
					status_auth_taxivaxi = 3,
					status = 'Assigned', assign_date=now() where id='$booking_id'");

				$sqlb = mysqli_query($this->db, "SELECT 
					b.*, 
					r.package_name, 
					a.contact_name `tcs_name`, a.contact_no `tcs_no` 
					from bookings `b` 
					left join rates `r` on b.rate_id = r.id 
					left join admins `a` on b.admin_id = a.id
					where b.id = '$booking_id' ");
				$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);
					
				//For sms to tcs
				$booking_details['tcs_name'] = $resultb['tcs_name'];
				$booking_details['tcs_no'] = $resultb['tcs_no'];
				
				//Generete common varialble for email
				// $pickup_datetime = date();
				$booking_details['id'] = $resultb['id'];
				$booking_details['reference_no'] = $resultb['reference_no'];
				$booking_details['pickup_location'] = $resultb['pickup_location'];
				$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];
				$tour_type = $resultb['tour_type'];

				if($tour_type == '0')
				{
					$booking_details['trip'] = 'Radio Taxi';
				}
				elseif($tour_type == '1')
				{
					$booking_details['trip'] = 'Local Package';	
				}
				else
				{
					$booking_details['trip'] = 'Outstation';
				}


				if($tour_type == '0')
				{
					$booking_details['package'] = 'Pick-Drop';
				}
				elseif($tour_type == '1')
				{
					$booking_details['package'] = $resultb['package_name'];	
				}
				else
				{
					$booking_details['package'] = $resultb['days'] . " Days";
				}

				if($tour_type == '0')
				{
					$booking_details['taxi_type_name'] = 'Sedan';
				}
				else
				{
					$booking_details['taxi_type_name'] = $resultl['taxi_type_namee'];	
				}
				
				
					$booking_details['taxi_reg_no'] = $resultb['taxi_reg_no'];
					$booking_details['driver_name'] = $resultb['driver_name'];
					$booking_details['driver_contact'] = $resultb['driver_contact'];
				

				//Generate spoc details for email
				$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
				$sqlt = mysqli_query($this->db, "SELECT 
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

	            //Communicate to all spocs
				for($i=1; $i<=1; $i++)
	            {	
					$mail_body = assignMailToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail($booking_details['email'],"TVTCS".$booking_details['id']." - TCS Booking Assigned Successfully!!",$mail_body);
	            }

	            //Communicate to all employees
	            $sqlt = mysqli_query($this->db, "SELECT 
					p.people_cid, p.people_name, p.people_email, p.people_contact
					from people_bookings `pb`
					left join people `p` on pb.people_id = p.id
					where pb.booking_id = '$booking_id'");
				while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
				{	
					$mail_body = assignMailToEmployee($booking_details, $i, $user_details, $employee_details);
					sendEmail($resultt['people_email'],"TVTCS".$booking_details['id']." - TCS Booking Assigned Successfully!!",$mail_body);
	            }

				//SMS to SPOC
				$m = "Dear ".$booking_details['name'].",\n\nFor your booking with id TVTCS".$booking_id.".\nDriver: ".$driver_name." (".$driver_contact.")\nTaxi: ".$taxi_model_name." (".$taxi_reg_no.")\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi";
				sendESMS($booking_details['user_contact'], $m);
				sendESMS("9881102875", $sms_text);

				//SMS to all Employees
	            $sqlt = mysqli_query($this->db, "SELECT 
					p.people_cid, p.people_name, p.people_email, p.people_contact
					from people_bookings `pb`
					left join people `p` on pb.people_id = p.id
					where pb.booking_id = '$booking_id'");
				while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
				{	
					$n = $resultt['people_name'];
					
					//$m = "Dear ".$resultt['people_name'].",\n\nFor booking with id ".$booking_details['reference_no'].".\nDriver: ".$booking_details['driver_name']." (".$booking_details['driver_contact'].")\nTaxi: ".$booking_details['taxi_model_name']." (".$booking_details['taxi_reg_no'].")\n\nPlease call at +919990045853 for any query.\n\nRgrds,\nTaxiVaxi";
					$m = "Dear ".$n.",\n\nFor your booking with id TVTCS".$booking_id.".\nDriver: ".$driver_name." (".$driver_contact.")\nTaxi: ".$taxi_model_name." (".$taxi_reg_no.")\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi";
					sendESMS($resultt['people_contact'],$m);
					//sendESMS('+91'.$resultt['people_contact'], $m);
	            }

	            //SMS to driver
				$sqlt = mysqli_query($this->db, "SELECT 
					p.people_cid, p.people_name, p.people_email, p.people_contact
					from people_bookings `pb`
					left join people `p` on pb.people_id = p.id
					where pb.booking_id = '$booking_id' LIMIT 1");
				$resulttmp = mysqli_fetch_array($sqlt, MYSQL_ASSOC);
				$m = "Booking from TaxiVaxi.\nBooking Id: TVTCS".$booking_id."\nCar Type: ".$booking_details['taxi_type_name']."\nPackage: ".$booking_details['trip']."\nUse: ".$booking_details['package']."\nFrom: ".$booking_details['pickup_location']."\nTime: ".date('d M Y - h:i a', strtotime($resultb['pickup_datetime']))."\nGuest: ".$resulttmp['people_name']."-".$resulttmp['people_contact']."\n\nCarry Duty Slip and put the booking Id. Report 15 mins before time.\n\nRegards\nTaxiVaxi";
				sendESMS($driver_contact, $m);
						
				$success = array('success' => "1", "error" => "","response"=>'Booked Successfully with Ref No:CRN'.$crn.'!!');
				$this->response($this->json($success), 200);
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//--------------------------------------------------------------------------------//
		
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
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username) and !empty($password))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT * from admins where username = '$username' AND password = '$enc_pwd'");
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$admin_id = $result['id'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							/*mysqli_query($this->db,"UPDATE admins set access_token = '$token' where id = '$admin_id'");*/
							mysqli_query($this->db,"INSERT into admins_access_tokens (admin_id, access_token) VALUES ('$admin_id', '$token')");
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
					else
					{
						$response = array('success' => "0", "error" => "Invalid Username");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username and Password");
					$this->response($this->json($response),200);	
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
			$admin_id = $this->checkAdmin($token_id);
			
			try
			{
				mysqli_query($this->db, "DELETE from admins_access_tokens  where access_token = '$token_id'");
				$sql = mysqli_query($this->db, "select * from admins where id = '$admin_id' LIMIT 1");
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

		
	//----------------------------------Bookings-Company Admin------------------------------//

		private function getAllAdminBookings()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$type = $this->_request['type'];

			try
			{
				switch($type)
				{
					case '1':  //Active (Unassigned)
					{
						$query = "SELECT b.*, sg.subgroup_name 
						from bookings `b` 
						left join user_bookings `ub` on ub.booking_id = b.id 
						left join subgroups `sg` on b.subgroup_id = sg.id
						where ub.admin_id = '$admin_id' and b.status_user = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi < 2  order by pickup_datetime asc";
						break;
					}
					case '3':  //Archived
					{
						$query = "SELECT b.*, sg.subgroup_name 
						from user_bookings `ub` 
						left join bookings `b` on ub.booking_id = b.id 
						left join subgroups `sg` on b.subgroup_id = sg.id
						where ub.admin_id = '$admin_id' and b.status_user = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi = 3 and b.is_invoice = 0 and DATE_SUB(NOW(), INTERVAL 12 HOUR) > b.pickup_datetime order by pickup_datetime desc";
						break;
					}
					case '4': //Cancelled /Rejected
					{
						$query = "SELECT b.*, sg.subgroup_name 
						from user_bookings `ub` 
						left join bookings `b` on ub.booking_id = b.id 
						left join subgroups `sg` on b.subgroup_id = sg.id
						where ub.admin_id = '$admin_id' and b.status_user = 2 OR b.status_auth1 = 2 OR b.status_auth2 = 2 OR b.status_auth_taxivaxi = 2 order by pickup_datetime desc";
						break;
					}
					case '2': //Active (Assigned)
					{
						$query = "SELECT b.*, sg.subgroup_name 
						from user_bookings `ub` 
						left join bookings `b` on ub.booking_id = b.id 
						left join subgroups `sg` on b.subgroup_id = sg.id
						where ub.admin_id = '$admin_id' and b.status_user = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi = 3 and b.is_invoice = 0 and DATE_SUB(NOW(), INTERVAL 12 HOUR) < b.pickup_datetime order by pickup_datetime asc";
						break;
					}
				}
				
				$sql = mysqli_query($this->db,$query);

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getAdminBookingReport()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$group_id = $this->_request['group_id'];
			$subgroup_id = $this->_request['subgroup_id'];
			$spoc_id = $this->_request['spoc_id'];
			$from_date = $this->_request['from_date'];
			$to_date = $this->_request['to_date'];
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					concat('TVTCS',b.id) as booking_id,b.ass_code,b.tour_type,r.package_name,b.pickup_location,b.drop_location,b.booking_date,b.pickup_datetime,b.status_user,b.status_auth1,b.status_auth2,b.status_auth_taxivaxi,b.tv_accept_reject_date,b.approved_date,b.rejected_date,b.user_cancel_date,b.cancel_reason,b.assign_date,ga.name `approver2_name`,sga.name `approver1_name`,
					g.group_name,sg.subgroup_name,u.user_name,u.user_cid,i.hours_done,i.allowed_hrs,i.extra_hours,i.hour_rate,i.extra_hours_charge,i.start_km,i.end_km,i.kms_done,i.allowed_kms,i.extra_kms,i.km_rate,i.extra_kms_charge,i.extras,i.driver,i.base_rate,i.tax,i.total,i.taxivaxi_charge,i.taxivaxi_tax_charge,i.sub_total
					from user_bookings `ub` 
					left join bookings `b` on ub.booking_id = b.id 
					left join rates `r` on b.rate_id = r.id
					left join users `u` on u.id = ub.user_id
					left join subgroups `sg` on b.subgroup_id = sg.id
					left join subgroup_authenticater `sga` on sga.subgroup_id = sg.id 
					left join groups `g` on b.group_id = g.id
					left join group_authenticater `ga` on ga.group_id = g.id
					left join invoice `i` on i.booking_id = b.id
					where ub.admin_id = '$admin_id'
					order by b.id asc");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$booking_datetime = $rlt['booking_date'];
						$booking_date = explode(' ',$booking_datetime)[0];
						$booking_time = explode(' ',$booking_datetime)[1];
						
						$pickup_datetime = $rlt['pickup_datetime'];
						$pickup_date = explode(' ',$pickup_datetime)[0];
						$pickup_time = explode(' ',$pickup_datetime)[1];
						
						$user_cancel_datetime = $rlt['user_cancel_date'];
						$approved_datetime = $rlt['approved_date'];
						$rejected_datetime = $rlt['rejected_date'];
						$tv_accept_reject_datetime = $rlt['tv_accept_reject_date'];
						$assign_datetime = $rlt['assign_date'];
						
						$rlt2 = array();
						$rlt2['Booking ID'] = $rlt['booking_id'];
						$rlt2['Assessment Code'] = $rlt['ass_code'];
						switch($rlt['tour_type'])
						{
							case '0':
							{
								$rlt2['Tour Type'] = 'Radio';
								break;
							}
							case '1':
							{
								$rlt2['Tour Type'] = 'Local';
								break;
							}
							case '2':
							{
								$rlt2['Tour Type'] = 'Outstation';
								break;
							}
						}
						
						$rlt2['Package Name'] = $rlt['package_name'];
						$rlt2['Pickup Location'] = $rlt['pickup_location'];
						$rlt2['Drop Location'] = $rlt['drop_location'];
						$rlt2['Booking Date'] = $booking_date;
						$rlt2['Booking Time'] = $booking_time;
						$rlt2['Pickup Date'] = $pickup_date;
						$rlt2['Pickup Time'] = $pickup_time;
						
						
						
						switch($rlt['status_user'])
						{
							case '1':
							{
								$rlt2['SPOC Status'] = 'Active';
								break;
							}
							case '2':
							{
								$rlt2['SPOC Status'] = 'Cancelled';
								break;
							}
						}
						
						$rlt2['SPOC Cancel Date'] = "";
						$rlt2['SPOC Cancel Time'] = "";
						
						if($user_cancel_datetime)
						{
							$rlt2['SPOC Cancel Date'] = explode(' ',$user_cancel_datetime)[0];
							$rlt2['SPOC Cancel Time'] = explode(' ',$user_cancel_datetime)[1];
						}
						
						switch($rlt['status_auth1'])
						{
							case '0':
							{
								$rlt2['Approver1 Status'] = 'No Action';
								break;
							}
							case '1':
							{
								$rlt2['Approver1 Status'] = 'Approved';
								break;
							}
							case '2':
							{
								$rlt2['Approver1 Status'] = 'Cancelled';
								break;
							}
						}
						
						
						$rlt2['Approver1 Name'] = $rlt['approver1_name'];
						
						switch($rlt['status_auth2'])
						{
							case '0':
							{
								$rlt2['Approver2 Status'] = 'No Action';
								break;
							}
							case '1':
							{
								$rlt2['Approver2 Status'] = 'Approved';
								break;
							}
							case '2':
							{
								$rlt2['Approver2 Status'] = 'Cancelled';
								break;
							}
						}
						
						$rlt2['Approver2 Name'] = $rlt['approver2_name'];
						
						$rlt2['Approved Date'] = "";
						$rlt2['Approved Time'] = "";
						$rlt2['Reject Date'] = "";
						$rlt2['Reject Time'] = "";
						
						if($approved_datetime)
						{
							$rlt2['Approved Date'] = explode(' ',$approved_datetime)[0];
							$rlt2['Approved Time'] = explode(' ',$approved_datetime)[1];
						}
						
						if($rejected_datetime)
						{
							$rlt2['Reject Date'] = explode(' ',$rejected_datetime)[0];
							$rlt2['Reject Time'] = explode(' ',$rejected_datetime)[1];
						}
						
						switch($rlt['status_auth_taxivaxi'])
						{
							case '0':
							{
								$rlt2['TaxiVaxi Status'] = 'No Action';
								break;
							}
							case '1':
							{
								$rlt2['TaxiVaxi Status'] = 'Accepted';
								break;
							}
							case '2':
							{
								$rlt2['TaxiVaxi Status'] = 'Cancelled';
								break;
							}
							case '3':
							{
								$rlt2['TaxiVaxi Status'] = 'Assigned';
								break;
							}
						}
						
						$rlt2['Assign Date'] = "";
						$rlt2['Assign Time'] = "";
						
						if($assign_datetime)
						{
							$rlt2['Assign Date'] = explode(' ',$assign_datetime)[0];
							$rlt2['Assign Time'] = explode(' ',$assign_datetime)[1];
						}
						
						$rlt2['Group Name'] = $rlt['group_name'];
						$rlt2['Subgroup Name'] = $rlt['subgroup_name'];
						$rlt2['SPOC Name'] = $rlt['user_name'];
						$rlt2['Hours Done'] = "";
						$rlt2['Allowed Hours'] = "";
						$rlt2['Extra Hours'] = "";
						$rlt2['Rate per Hour'] = "";
						$rlt2['Extra Hour Charge'] = "";
						$rlt2['Kms Done'] = "";
						$rlt2['Allowed Kms'] = "";
						$rlt2['Extra Kms'] = "";
						$rlt2['Rate Per Km'] = "";
						$rlt2['Extra Km Charge'] = "";
						$rlt2['Driver/Night Charge'] = "";
						$rlt2['Base Price'] = "";
						$rlt2['Tax'] = "";
						$rlt2['Extras'] = "";
						if($rlt['tour_type'] == '1')
						{
							$rlt2['Hours Done'] = $rlt['hours_done'];
							$rlt2['Allowed Hours'] = $rlt['allowed_hrs'];
							$rlt2['Extra Hours'] = $rlt['extra_hours'];
							$rlt2['Rate per Hour'] = $rlt['hour_rate'];
							$rlt2['Extra Hour Charge'] = $rlt['extra_hours_charge'];
							$rlt2['Kms Done'] = $rlt['kms_done'];
							$rlt2['Allowed Kms'] = $rlt['allowed_kms'];
							$rlt2['Extra Kms'] = $rlt['extra_kms'];
							$rlt2['Rate Per Km'] = $rlt['km_rate'];
							$rlt2['Extra Km Charge'] = $rlt['extra_kms_charge'];
							$rlt2['Driver/Night Charge'] = $rlt['driver'];
							$rlt2['Base Price'] = $rlt['base_rate'];
							$rlt2['Tax'] = $rlt['tax'];
							$rlt2['Extras'] = $rlt['extras'];
						}
						$rlt2['Usage Charge'] = $rlt['total'];
						$rlt2['Management Fee'] = $rlt['taxivaxi_charge'];
						$rlt2['Service Tax (Mgmt. Fee)'] = $rlt['taxivaxi_tax_charge'];
						$rlt2['Sub Total'] = $rlt['sub_total'];
						array_push($result, $rlt2);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getAdminBusBookingReport()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$group_id = $this->_request['group_id'];
			$subgroup_id = $this->_request['subgroup_id'];
			$spoc_id = $this->_request['spoc_id'];
			$from_date = $this->_request['from_date'];
			$to_date = $this->_request['to_date'];
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					concat('TVTCSBUS',b.id) as booking_id,b.pickup_city,b.drop_city,b.booking_datetime,b.assessment_code,b.status_spoc,b.status_auth1,b.status_auth2,b.status_auth_taxivaxi,b.auth_accept_time,b.auth_reject_time,b.spoc_cancel_date,b.taxivaxi_accept_time,b.taxivaxi_reject_time,b.taxivaxi_assign_time,b.pickup_datetime_taxivaxi,b.time_range,b.bus_type_priority_1,bus_type_allocated,g.group_name,ga.name `approver2_name`,
					sg.subgroup_name,sga.name `approver1_name`,u.user_name,u.user_cid,i.total,i.taxivaxi_charge,i.taxivaxi_tax_charge,i.sub_total
					from bus_bookings `b` 
					left join users `u` on u.id = b.user_id
					left join groups `g` on b.group_id = g.id
					left join group_authenticater `ga` on ga.group_id = g.id
					left join subgroups `sg` on b.subgroup_id = sg.id
					left join subgroup_authenticater `sga` on sga.subgroup_id = sg.id
					left join bus_invoice i on i.booking_id = b.id
					where b.admin_id = '$admin_id'
					order by b.id asc");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$rlt2 = array();
						$rlt2['Booking ID'] = $rlt['booking_id'];
						$rlt['Assessment Code'] = $rlt['assessment_code'];
						$rlt2['Pickup City'] = $rlt['pickup_city'];
						$rlt2['Drop City'] = $rlt['drop_city'];
						$rlt2['Booking Date'] = explode(' ',$rlt['booking_datetime'])[0];
						$rlt2['Booking Time'] = explode(' ',$rlt['booking_datetime'])[1];
						
						if($rlt['pickup_datetime_taxivaxi']) 
						{
							$rlt2['Journey Date'] = explode(' ',$rlt['pickup_datetime_taxivaxi'])[0];
							$rlt2['Journey Time'] = explode(' ',$rlt['pickup_datetime_taxivaxi'])[1];
						}	
						else
						{
							$time_range = $rlt['time_range'];
							$journey_datetime = explode("::", $time_range)[1];
							$rlt2['Journey Date'] = explode(' ',$journey_datetime)[0];
							$rlt2['Journey Time'] = explode(' ',$journey_datetime)[1];
						}
						
						switch($rlt['status_spoc'])
						{
							case '1':
							{
								$rlt2['SPOC Status'] = 'Active';
								break;
							}
							case '2':
							{
								$rlt2['SPOC Status'] = 'Cancelled';
								break;
							}
						}
						
						$rlt2['SPOC Cancel Date'] = "";
						$rlt2['SPOC Cancel Time'] = ""; 
						
						if($rlt['spoc_cancel_date'])
						{
							$rlt2['SPOC Cancel Date'] = explode(' ',$rlt['spoc_cancel_date'])[0];
							$rlt2['SPOC Cancel Time'] = explode(' ',$rlt['spoc_cancel_date'])[1];
						}
						
						switch($rlt['status_auth1'])
						{
							case '0':
							{
								$rlt2['Approver1 Status'] = 'No Action';
								break;
							}
							case '1':
							{
								$rlt2['Approver1 Status'] = 'Approved';
								break;
							}
							case '2':
							{
								$rlt2['Approver1 Status'] = 'Cancelled';
								break;
							}
						}
						
						$rlt2['Approver1 Name'] = $rlt['approver1_name'];
						
						switch($rlt['status_auth2'])
						{
							case '0':
							{
								$rlt2['Approver2 Status'] = 'No Action';
								break;
							}
							case '1':
							{
								$rlt2['Approver2 Status'] = 'Approved';
								break;
							}
							case '2':
							{
								$rlt2['Approver2 Status'] = 'Cancelled';
								break;
							}
						}
						
						$rlt2['Approver2 Name'] = $rlt['approver2_name'];
						
						$rlt2['Approved Date'] = "";
						$rlt2['Approved Time'] = "";
						$rlt2['Reject Date'] = "";
						$rlt2['Reject Time'] = "";
						
						if($rlt['auth_accept_time'])
						{
							$rlt2['Approved Date'] = explode(' ',$rlt['auth_accept_time'])[0];
							$rlt2['Approved Time'] = explode(' ',$rlt['auth_accept_time'])[1];
						}
						
						if($rlt['auth_reject_time'])
						{
							$rlt2['Reject Date'] = explode(' ',$rlt['auth_reject_time'])[0];
							$rlt2['Reject Time'] = explode(' ',$rlt['auth_reject_time'])[1];
						}
						
						switch($rlt['status_auth_taxivaxi'])
						{
							case '0':
							{
								$rlt2['TaxiVaxi Status'] = 'No Action'; 
								break;
							}
							case '1':
							{
								$rlt2['TaxiVaxi Status'] = 'Accepted';
								break;
							}
							case '2':
							{
								$rlt2['TaxiVaxi Status'] = 'Cancelled';
								break;
							}
							case '3':
							{
								$rlt2['TaxiVaxi Status'] = 'Assigned';
								break;
							}
						}
						
						$rlt2['Assign Date'] = "";
						$rlt2['Assign Time'] = "";
						
						if($rlt['taxivaxi_assign_time'])
						{
							$rlt2['Assign Date'] = explode(' ',$rlt['taxivaxi_assign_time'])[0];
							$rlt2['Assign Time'] = explode(' ',$rlt['taxivaxi_assign_time'])[1];
						}
						
						$rlt2['Group Name'] = $rlt['group_name'];
						$rlt2['Subgroup Name'] = $rlt['subgroup_name'];
						$rlt2['SPOC Name'] = $rlt['user_name'];
						$rlt2['Ticket Price'] = $rlt['total'];
						$rlt2['Management Fee'] = $rlt['taxivaxi_charge'];
						$rlt2['Service Tax (Mgmt. Fee)'] = $rlt['taxivaxi_tax_charge'];
						$rlt2['Sub Total'] = $rlt['sub_total'];
						array_push($result, $rlt2);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		private function getAllAdminInvoices()
		{
			$token_id = $this->_request['access_token'];
			$type = intval($this->_request['type']);
			$admin_id = $this->checkAdmin($token_id);
			try
			{	
				$sql;
				if($type!=7)
				{
					$sql = mysqli_query($this->db, "SELECT 
					b.*,
					i.sub_total `bill_amount`, i.status `invoice_status`, i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.bill_id, i.accepted_by, i.rejected_by,
					u.user_cid, u.user_name
					from bookings `b`
					left join user_bookings `ub` on ub.booking_id = b.id
					left join invoice `i` on b.invoice_id = i.id
					left join users `u` on ub.user_id = u.id
					where b.is_invoice = '1' and b.admin_id = '$admin_id' and i.status = $type
					order by b.id desc");
				}
				else
				{
					$sql = mysqli_query($this->db, "SELECT 
					b.*,
					i.sub_total `bill_amount`, i.status `invoice_status`,  i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.accepted_by, i.rejected_by,
					u.user_cid, u.user_name
					from bookings `b`
					left join user_bookings `ub` on ub.booking_id = b.id
					left join invoice `i` on b.invoice_id = i.id
					left join users `u` on ub.user_id = u.id
					where b.is_invoice = '1' and b.admin_id = '$admin_id' and ((i.status = '7' and i.previous_status = '2') or (i.status = '1' and i.previous_status = '7'))
					order by b.id desc");
				}

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getAllAuthoneInvoices()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthone($token_id);
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					b.*,
					i.sub_total `bill_amount`,
					u.user_cid, u.user_name
					from bookings `b`
					left join user_bookings `ub` on ub.booking_id = b.id
					left join invoice `i` on b.invoice_id = i.id
					left join users `u` on ub.user_id = u.id
					left join subgroup_authenticater `sa` on b.subgroup_id = sa.subgroup_id
					where b.is_invoice = 1  and sa.id = '$auth_id'
					order by b.id desc");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getAllAuthtwoInvoices()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthtwo($token_id);
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					b.*,
					i.sub_total `bill_amount`,
					u.user_cid, u.user_name
					from bookings `b`
					left join user_bookings `ub` on ub.booking_id = b.id
					left join invoice `i` on b.invoice_id = i.id
					left join users `u` on ub.user_id = u.id
					left join group_authenticater `ga` on b.group_id = ga.group_id
					where b.is_invoice = 1  and ga.id = '$auth_id'
					order by b.id desc");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getAllSpocInvoices()
		{
			$token_id = $this->_request['access_token'];	
			$type = $this->_request['type'];
			$employee_id = $this->checkEmployee($token_id);
			try
			{
				$sql;
				if($type!=3)
				{
					$sql = mysqli_query($this->db, "SELECT 
						b.*,
						i.sub_total `bill_amount`, i.status `invoice_status`, i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.bill_id, i.accepted_by, i.rejected_by,
						u.user_cid, u.user_name
						from bookings `b`
						left join user_bookings `ub` on ub.booking_id = b.id
						left join invoice `i` on b.invoice_id = i.id
						left join users `u` on ub.user_id = u.id
						where b.is_invoice = 1  and ub.user_id = '$employee_id' and i.status = '$type'
						order by b.id desc");
				}
				else
				{
					$sql = mysqli_query($this->db, "SELECT 
						b.*,
						i.sub_total `bill_amount`, i.status `invoice_status`, i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.bill_id, i.accepted_by, i.rejected_by,
						u.user_cid, u.user_name
						from bookings `b`
						left join user_bookings `ub` on ub.booking_id = b.id
						left join invoice `i` on b.invoice_id = i.id
						left join users `u` on ub.user_id = u.id
						where b.is_invoice = 1  and ub.user_id = '$employee_id' and ((i.status = '3') or (i.status = '1' and i.previous_status = '3') or (i.status = '1' and i.previous_status = '7') or (i.status=7))
						order by b.id desc");
				}

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getAllSpocBusInvoices()
		{
			$token_id = $this->_request['access_token'];	
			$type = $this->_request['type'];
			$employee_id = $this->checkEmployee($token_id);
			try
			{
				$sql;
				if($type!=3)
				{
					$sql = mysqli_query($this->db, "SELECT 
						b.*,
						i.sub_total `bill_amount`, i.status `invoice_status`, i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.bill_id, i.accepted_by, i.rejected_by,
						u.user_cid, u.user_name
						from bus_bookings `b`
						left join bus_invoice `i` on b.invoice_id = i.id
						left join users `u` on b.user_id = u.id
						where b.is_invoice = 1  and b.user_id = '$employee_id' and i.status = '$type'
						order by b.id desc");
				}
				else
				{
					$sql = mysqli_query($this->db, "SELECT 
						b.*,
						i.sub_total `bill_amount`, i.status `invoice_status`, i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.bill_id, i.accepted_by, i.rejected_by,
						u.user_cid, u.user_name
						from bus_bookings `b`
						left join bus_invoice `i` on b.invoice_id = i.id
						left join users `u` on b.user_id = u.id
						where b.is_invoice = 1  and b.user_id = '$employee_id' and ((i.status = '3') or (i.status = '1' and i.previous_status = '3') or (i.status = '1' and i.previous_status = '7') or (i.status=7))
						order by b.id desc");
				}

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getAllTaxivaxiadminInvoices()
		{
			$token_id = $this->_request['access_token'];
			$type = intval($this->_request['type']);
			$auth_id = $this->checkTaxivaxiAdmin($token_id);
			try
			{	
				$sql;
				if($type!=3) //This can be 3 or 7
				{
					$sql = mysqli_query($this->db, "SELECT 
						b.*,
						i.sub_total `bill_amount`, i.status `invoice_status`, i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.bill_id, i.accepted_by, i.rejected_by,
						u.user_cid, u.user_name
						from bookings `b`
						left join user_bookings `ub` on ub.booking_id = b.id
						left join invoice `i` on b.invoice_id = i.id
						left join users `u` on ub.user_id = u.id
						where b.is_invoice = 1 and i.status = $type
						order by b.id desc");
				}
				else
				{
					$sql = mysqli_query($this->db, "SELECT 
						b.*,
						i.sub_total `bill_amount`, i.status `invoice_status`, i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.bill_id, i.accepted_by, i.rejected_by,
						u.user_cid, u.user_name
						from bookings `b`
						left join user_bookings `ub` on ub.booking_id = b.id
						left join invoice `i` on b.invoice_id = i.id
						left join users `u` on ub.user_id = u.id
						where b.is_invoice = 1 and (i.status =3 or i.status=7)
						order by b.id desc");	
				}
				

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

	//----------------------------------GROUPS------------------------------//

		//Get All Groups with Details 
		private function getAllGroups()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					g.*, 
					a.corporate_name, a.budget `admin_budget`, a.expense `admin_expense`, 
					a.allocated `admin_allocated`, a.unallocated `admin_unallocated`,
					ga.name `group_auth_name`, ga.email `group_auth_email`, ga.cid `group_auth_cid` ,
					ga.contact_no `group_auth_contact`
					from groups `g` 
					left join admins `a` on g.admin_id = a.id 
					inner join group_authenticater `ga` on ga.id = g.authenticater_id
					where g.admin_id ='$admin_id'");

				$sql2 = mysqli_query($this->db, "select budget, unallocated from admins where id='$admin_id'");
				$rlt2 = mysqli_fetch_array($sql2, MYSQL_ASSOC);

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$temp = array('group_id' => $rlt['id'], 'admin_id' => $rlt['admin_id'], 
							'group_name' => $rlt['group_name'], 'admin_name' => $rlt['admin_name'], 
							'budget' => $rlt['budget'], 'admin_budget' => $rlt['admin_budget'],
							'expense' => $rlt['expense'], 'admin_expense' => $rlt['admin_expense'], 
							'allocated' => $rlt['allocated'],'admin_allocated' => $rlt['admin_allocated'],
							'unallocated' => $rlt['unallocated'], 'admin_unallocated' => $rlt['admin_unallocated'],
							'is_radio' => $rlt['is_radio'], 'is_outstation' => $rlt['is_outstation'],
							'group_auth_name' => $rlt['group_auth_name'], 'group_auth_email' => $rlt['group_auth_email'],
							'group_auth_cid' => $rlt['group_auth_cid'], 'group_auth_contact' => $rlt['group_auth_contact']);

						array_push($result, $temp);
					}
					$result = array('access_token' => $token_id,'Groups'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result, 'admin_unallocated' => $rlt2['unallocated']);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id, 
						'admin_budget' => $rlt2['budget'], 'admin_unallocated' => $rlt2['unallocated']);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Add group
		private function addGroup()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);

			$group_name = $this->_request['group_name'];
			$budget = $this->_request['budget'];
			$auth_cid = $this->_request['auth_cid'];
			$auth_name = $this->_request['auth_name'];
			$auth_contact = $this->_request['auth_contact'];
			$auth_email = $this->_request['auth_email'];
			$auth_password = $this->_request['auth_password'];
			$enc_pwd = md5($auth_password);
			$is_radio = $this->_request['is_radio'];
			$is_local = $this->_request['is_local'];
			$is_outstation = $this->_request['is_outstation'];
			
			try
			{
				//Add new group
				mysqli_query($this->db,"INSERT into groups (admin_id,group_name,budget,unallocated,is_radio,is_local,is_outstation) VALUES ('$admin_id','$group_name','$budget', '$budget', '$is_radio', '$is_local', '$is_outstation')");

				$new_id = mysqli_insert_id($this->db);			
				if($new_id)
				{
					
					$result = array('group_id' => $new_id, 'message' => 'Group Added Successfully');

					//Update admins allocated and unallocated field
					$sql = mysqli_query($this->db, "SELECT contact_no, email, allocated, unallocated from admins where id = '$admin_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
					$admin_new_allocated = floatval($rlt['allocated']) + floatval($budget);
					$admin_new_unallocated = floatval($rlt['unallocated']) - floatval($budget); 
					mysqli_query($this->db,"UPDATE admins SET allocated = '$admin_new_allocated', unallocated = '$admin_new_unallocated' where id = '$admin_id'");

					//Add new group Authenticator
					
					$sql  = mysqli_query($this->db, "SELECT id from group_authenticater where email = '$auth_email' ");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$group_id = $rlt['id'];
					}
					else
					{
						mysqli_query($this->db,"INSERT into group_authenticater (group_id, cid, name, contact_no, email, password, admin_id) VALUES ('$new_id','$auth_cid','$auth_name','$auth_contact','$auth_email', '$enc_pwd', '$admin_id')");
						$group_id = mysqli_insert_id($this->db);
					}
					
					//Update Groups auath_id
					$query = mysqli_query($this->db,"UPDATE groups SET authenticater_id='$group_id' WHERE id = $new_id");

					//Send Email
					$mail_body = sendRegistrationEmailToAuthenticator2($auth_name, $group_name, $auth_email, 
						$auth_password, $rlt['contact_no'], $rlt['email']);
					sendEmail($auth_email,"[TCS] Registered as Approver Level 2",$mail_body);
					
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//View Group
		private function viewGroup()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$group_id = $this->_request['group_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					g.*, 
					ga.name `auth_name`, ga.email `auth_email`, ga.cid `auth_cid` 
					from groups `g` 
					left join group_authenticater `ga` on g.authenticater_id  = ga.id 
					where g.id = '$group_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);

					$result = array('access_token' => $token_id,'Group'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'No such Group Found');
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Edit Group
		private function editGroup()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$group_id = $this->_request['group_id'];
			$group_name = $this->_request['group_name'];
			$budget = $this->_request['budget'];
			$auth_cid = $this->_request['auth_cid'];
			$auth_name = $this->_request['auth_name'];
			$is_radio = $this->_request['is_radio'];
			$is_local = $this->_request['is_local'];
			$is_outstation = $this->_request['is_outstation'];

			try
			{
				//Get old value of groups budget
				$sql2 = mysqli_query($this->db, "SELECT budget from groups where id = '$group_id' ");
				$rlt2 = mysqli_fetch_array($sql2, MYSQL_ASSOC);
				$old_budget = floatval($rlt2['budget']);
				$diff = floatval($budget) - floatval($old_budget);

				$query = mysqli_query($this->db,"UPDATE groups SET group_name='$group_name', budget='$budget', is_radio = '$is_radio', is_local = '$is_local', is_outstation = '$is_outstation' WHERE id = $group_id");
				$query2 = mysqli_query($this->db,"UPDATE group_authenticater SET name='$auth_name', cid='$auth_cid' WHERE id = (SELECT authenticater_id from groups where id = '$group_id')");
				if($query)
				{
					$result = array('user_id' => $user_id, 'message' => 'Group Updated Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					//Update admins allocated and unallocated field
					$sql = mysqli_query($this->db, "SELECT allocated, unallocated from admins where id = '$admin_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
					if($dff >= 0)
					{
						$admin_new_allocated = floatval($rlt['allocated']) + floatval($diff);
						$admin_new_unallocated = floatval($rlt['unallocated']) - floatval($diff); 	
					}
					else
					{
						$admin_new_allocated = floatval($rlt['allocated']) - floatval($diff);
						$admin_new_unallocated = floatval($rlt['unallocated']) + floatval($budget); 
					}
					
					mysqli_query($this->db,"UPDATE admins SET allocated = '$admin_new_allocated', unallocated = '$admin_new_unallocated' where id = '$admin_id'");

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not edit!!');
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Delete Group
		private function deleteGroup()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$group_id = $this->_request['group_id'];
			
			try
			{
				//Get groups budget
				$sql = mysqli_query($this->db, "SELECT budget from groups where id = '$group_id' ");
				$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
				$group_budget = floatval($rlt['budget']);

				$query = mysqli_query($this->db,"DELETE FROM groups WHERE id = $group_id");

				//get admins current allocated and inallocated
				$sql = mysqli_query($this->db, "SELECT allocated, unallocated from admins where id = '$admin_id' ");
				$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
				$admin_allocated = floatval($rlt['allocated']) - $group_budget;
				$admin_unallocated = floatval($rlt['unallocated']) + $group_budget;

				//update admins allocated and unallocated
				$query = mysqli_query($this->db,"UPDATE admins SET allocated = '$admin_allocated', unallocated = '$admin_unallocated' where id = '$admin_id'");
				if($query)
				{
					$result = array('group_id' => $group_id, 'message' => 'Group Deleted Successfully');
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//----------------------------------SUB-GROUPS------------------------------//

		//Get All Subgroups with Details 
		private function getAllSubgroups()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					sg.*, 
					a.corporate_name, a.budget `admin_budget`, a.expense `admin_expense`, 
					a.allocated `admin_allocated`, a.unallocated `admin_unallocated`, 
					g.group_name, g.budget `group_budget`, g.expense `group_expense`, 
					g.allocated `group_allocated`, g.unallocated `group_unallocated`, 
					g.is_radio `group_is_radio`, g.is_outstation `group_is_outstation`,
					sga.name `subgroup_auth_name`, sga.email `subgroup_auth_email`, sga.cid `subgroup_auth_cid`,
					sga.contact_no `subgroup_auth_contact`
					from subgroups `sg` 
					left join groups `g` on sg.group_id = g.id 
					left join admins `a` on sg.admin_id = a.id 
					left join subgroup_authenticater `sga` on sga.id = sg.authenticater_id
					where g.admin_id ='$admin_id'");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$temp = array('subgroup_id' => $rlt['id'], 'group_id' => $rlt['group_id'], 'admin_id' => $rlt['admin_id'], 
							'subgroup_name' => $rlt['subgroup_name'], 'group_name' => $rlt['group_name'], 'admin_name' => $rlt['corporate_name'], 
							'budget' => $rlt['budget'], 'group_budget' => $rlt['group_budget'], 'admin_budget' => $rlt['admin_budget'],
							'expense' => $rlt['expense'], 'group_expense' => $rlt['group_expense'], 'admin_expense' => $rlt['admin_expense'], 
							'allocated' => $rlt['allocated'], 'group_allocated' => $rlt['group_allocated'], 'admin_allocated' => $rlt['admin_allocated'],
							'unallocated' => $rlt['unallocated'], 'group_unallocated' => $rlt['group_unallocated'], 'admin_unallocated' => $rlt['admin_unallocated'],  
							'is_radio' => $rlt['is_radio'], 'group_is_radio' => $rlt['group_is_radio'], 
							'is_outstation' => $rlt['is_outstation'], 'group_is_outstation' => $rlt['group_is_outstation'],
							'subgroup_auth_name' => $rlt['subgroup_auth_name'], 'subgroup_auth_email' => $rlt['subgroup_auth_email'],
							'subgroup_auth_cid' => $rlt['subgroup_auth_cid'], 'subgroup_auth_contact' => $rlt['subgroup_auth_contact']);

						array_push($result, $temp);
					}
					$result = array('access_token' => $token_id,'Subgroups'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Add subgroup
		private function addSubgroup()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);

			$group_id = $this->_request['group_id'];
			$subgroup_name = $this->_request['subgroup_name'];
			$budget = $this->_request['budget'];

			$auth_cid = $this->_request['auth_cid'];
			$auth_name = $this->_request['auth_name'];
			$auth_contact = $this->_request['auth_contact'];
			$auth_email = $this->_request['auth_email'];
			$auth_password = $this->_request['auth_password'];
			$enc_pwd = md5($auth_password);
			$is_radio = $this->_request['is_radio'];
			$is_local = $this->_request['is_local'];
			$is_outstation = $this->_request['is_outstation'];
			
			try
			{
				//Add new group
				mysqli_query($this->db,"INSERT into subgroups (admin_id,group_id,subgroup_name,budget,unallocated,is_radio,is_local,is_outstation) VALUES ('$admin_id','$group_id','$subgroup_name','$budget', '$budget', '$is_radio', '$is_local','$is_outstation')");

				$new_id = mysqli_insert_id($this->db);			
				if($new_id)
				{
					
					$result = array('subgroup_id' => $new_id, 'message' => 'Sub-group Added Successfully');

					//Update groups allocated and unallocated field
					$sql = mysqli_query($this->db, "SELECT allocated, unallocated from groups where id = '$group_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
					$group_new_allocated = floatval($rlt['allocated']) + floatval($budget);
					$group_new_unallocated = floatval($rlt['unallocated']) - floatval($budget); 
					mysqli_query($this->db,"UPDATE groups SET allocated = '$group_new_allocated', unallocated = '$group_new_unallocated' where id = '$group_id'");

					//Add new group Authenticator
					$sql  = mysqli_query($this->db, "SELECT id from subgroup_authenticater where email = '$auth_email' ");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$subgroup_id = $rlt['id'];
					}
					else
					{
						mysqli_query($this->db,"INSERT into subgroup_authenticater (subgroup_id, cid, name, contact_no, email, password, admin_id) VALUES ('$new_id','$auth_cid','$auth_name','$auth_contact','$auth_email', '$enc_pwd', '$admin_id')");
						$subgroup_id = mysqli_insert_id($this->db);
					}
					//Update Groups auath_id
					$query = mysqli_query($this->db,"UPDATE subgroups SET authenticater_id='$subgroup_id' WHERE id = $new_id");

					//Send Email to Auth 1
					$sql = mysqli_query($this->db, "SELECT * from admins where id = '$admin_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
					$mail_body = sendRegistrationEmailToAuthenticator1($auth_name, $subgroup_name, $auth_email, 
						$auth_password, $rlt['contact_no'], $rlt['email']);
					sendEmail($auth_email,"[TCS] Registered as Approver Level 1",$mail_body);
					
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage()." ".$e->getLine());
				$this->response($this->json($response),200);
			}
		}

		//View Subgroup
		private function viewSubgroup()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$subgroup_id = $this->_request['subgroup_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					sg.*, 
					g.is_radio `group_is_radio`, g.is_local `group_is_local`, g.is_outstation `group_is_outstation`, g.unallocated `group_unallocated`, 
					sga.name `auth_name`, sga.email `auth_email`, sga.cid `auth_cid`
					from subgroups `sg` 
					left join groups `g` on sg.group_id = g.id 
					left join subgroup_authenticater `sga` on sg.authenticater_id = sga.id 
					where sg.id = '$subgroup_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);

					$result = array('access_token' => $token_id,'Subgroup'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'No such Subgroup Found');
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Edit Subgroup
		private function editSubgroup()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);

			$subgroup_id = $this->_request['subgroup_id'];
			$group_id = $this->_request['group_id'];
			$subgroup_name = $this->_request['subgroup_name'];
			$budget = $this->_request['budget'];
			$auth_cid = $this->_request['auth_cid'];
			$auth_name = $this->_request['auth_name'];
			$is_radio = $this->_request['is_radio'];
			$is_local = $this->_request['is_local'];
			$is_outstation = $this->_request['is_outstation'];

			try
			{
				//Get old value of subgroup budget and old value of subgroup groupid
				$sql2 = mysqli_query($this->db, "SELECT budget, group_id from subgroups where id = '$subgroup_id' ");
				$rlt2 = mysqli_fetch_array($sql2, MYSQL_ASSOC);
				$old_budget = floatval($rlt2['budget']);
				$old_group_id = $rlt2['group_id'];
				$diff = floatval($budget) - floatval($old_budget);

				$query = mysqli_query($this->db,"UPDATE subgroups SET group_id = '$group_id',  subgroup_name='$subgroup_name', budget='$budget', is_radio = '$is_radio', is_outstation = '$is_outstation' WHERE id = '$subgroup_id'");
				$query2 = mysqli_query($this->db,"UPDATE subgroup_authenticater SET name='$auth_name', cid='$auth_cid' WHERE id = (SELECT authenticater_id from subgroups where id = '$subgroup_id')");
				if($query)
				{
					$result = array('subgroup_id' => $subgroup_id, 'message' => 'Subgroup Updated Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					//Update groups allocated and unallocated field
					$sql = mysqli_query($this->db, "SELECT allocated, unallocated from groups where id = '$group_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);

					if($old_group_id == $group_id)
					{
						if($dff >= 0)
						{
							$group_new_allocated = floatval($rlt['allocated']) + floatval($diff);
							$group_new_unallocated = floatval($rlt['unallocated']) - floatval($diff); 	
						}
						else
						{
							$group_new_allocated = floatval($rlt['allocated']) - floatval($diff);
							$group_new_unallocated = floatval($rlt['unallocated']) + floatval($budget); 
						}
						mysqli_query($this->db,"UPDATE groups SET allocated = '$group_new_allocated', unallocated = '$group_new_unallocated' where id = '$group_id'");
					}
					else
					{
						$sql3 = mysqli_query($this->db, "SELECT allocated, unallocated from groups where id = '$old_group_id' ");
						$rlt3 = mysqli_fetch_array($sql3, MYSQL_ASSOC);

						$new_group_new_allocated = floatval($rlt['allocated']) + floatval($budget);
						$new_group_new_unallocated = floatval($rlt['unallocated']) - floatval($budget); 
						mysqli_query($this->db,"UPDATE groups SET allocated = '$new_group_new_allocated', unallocated = '$new_group_new_unallocated' where id = '$group_id'");

						$old_group_new_allocated = floatval($rlt3['allocated']) - floatval($old_budget);
						$old_group_new_unallocated = floatval($rlt3['unallocated']) + floatval($old_budget);
						mysqli_query($this->db,"UPDATE groups SET allocated = '$old_group_new_allocated', unallocated = '$old_group_new_unallocated' where id = '$old_group_id'");
					}

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not edit!!');
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Delete Subgroup
		private function deleteSubgroup()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$subgroup_id = $this->_request['subgroup_id'];
			
			try
			{
				//Get subgroups budget
				$sql = mysqli_query($this->db, "SELECT budget, group_id from subgroups where id = '$subgroup_id' ");
				$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
				$subgroup_budget = floatval($rlt['budget']);
				$group_id  = $rlt['group_id'];

				$query = mysqli_query($this->db,"DELETE FROM subgroups WHERE id = $subgroup_id");

				//get groups current allocated and inallocated
				$sql = mysqli_query($this->db, "SELECT allocated, unallocated from groups where id = '$group_id' ");
				$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
				$group_allocated = floatval($rlt['allocated']) - $subgroup_budget;
				$group_unallocated = floatval($rlt['unallocated']) + $subgroup_budget;

				//update groups allocated and unallocated
				$query = mysqli_query($this->db,"UPDATE groups SET allocated = '$group_allocated', unallocated = '$group_unallocated' where id = '$group_id'");
				if($query)
				{
					$result = array('group_id' => $group_id, 'message' => 'Sub-group Deleted Successfully');
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//----------------------------------Spocs -Dashboard------------------------------//

		//Get All Spocs with Details 
		private function getAllSpocs()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			try
			{
				/*$sql = mysqli_query($this->db, "SELECT e.*, a.corporate_name, a.budget `admin_budget`, a.expense `admin_expense`, a.allocated `admin_allocated`, a.unallocated `admin_unallocated`, g.group_name, g.budget `group_budget`, g.expense `group_expense`, g.allocated `group_allocated`, g.unallocated `group_unallocated`, g.is_radio `group_is_radio`, g.is_outstation `group_is_outstation`,
				sg.subgroup_name, sg.budget `subgroup_budget`, sg.expense `subgroup_expense`, sg.allocated `subgroup_allocated`, sg.unallocated `subgroup_unallocated`, sg.is_radio `subgroup_is_radio`, sg.is_outstation `subgroup_is_outstation`
				 from employees `e` left join subgroups `sg` on e.subgroup_id = sg.id left join groups `g` on e.group_id = g.id left join admins `a` on e.admin_id = a.id where e.admin_id ='$admin_id'");*/
				
				$sql = mysqli_query($this->db, "SELECT e.*, 
				sg.subgroup_name, sg.budget `subgroup_budget`, sg.expense `subgroup_expense`, sg.allocated `subgroup_allocated`, sg.unallocated `subgroup_unallocated`, sg.is_radio `subgroup_is_radio`, sg.is_local `subgroup_is_local`, sg.is_outstation `subgroup_is_outstation`,
				g.group_name				
				 from users `e` left join subgroups `sg` on e.subgroup_id = sg.id left join groups `g` on sg.group_id = g.id left join admins `a` on e.admin_id = a.id where e.admin_id ='$admin_id'");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						/*$temp = array('employee_id' => $rlt['id'] 'subgroup_id' => $rlt['subgroup_id'], 'group_id' => $rlt['group_id'], 'admin_id' => $rlt['admin_id'], 
							'subgroup_name' => $rlt['subgroup_name'], 'group_name' => $rlt['group_name'], 'admin_name' => $rlt['corporate_name'], 
							'budget' => $rlt['budget'], 'group_budget' => $rlt['group_budget'], 'admin_budget' => $rlt['admin_budget'],
							'expense' => $rlt['expense'], 'group_expense' => $rlt['group_expense'], 'admin_expense' => $rlt['admin_expense'], 
							'allocated' => $rlt['allocated'], 'group_allocated' => $rlt['group_allocated'], 'admin_allocated' => $rlt['admin_allocated'],
							'unallocated' => $rlt['unallocated'], 'group_unallocated' => $rlt['group_unallocated'], 'admin_unallocated' => $rlt['admin_unallocated'],  
							'is_radio' => $rlt['is_radio'], 'group_is_radio' => $rlt['group_is_radio'], 
							'is_outstation' => $rlt['is_outstation'], 'group_is_outstation' => $rlt['group_is_outstation']);*/

						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Employees'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Add Spoc
		private function addSpoc()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);

			$user_cid = $this->_request['user_cid'];
			$user_name = $this->_request['user_name'];
			$user_contact = $this->_request['user_contact'];
			$email = $this->_request['email'];
			$subgroup_id = $this->_request['subgroup_id'];
			$budget = $this->_request['budget'];
			$status = $this->_request['status'];
			$is_radio = $this->_request['is_radio'];
			$is_local = $this->_request['is_local'];
			$is_outstation = $this->_request['is_outstation'];

			$acquired_spoc_id = $this->_request['acquired_spoc_id'];

			$password = 'taxi123';
			$enc_pwd = md5($password);
			
			try
			{
				//Add new group
				mysqli_query($this->db,"INSERT into users (admin_id,subgroup_id,user_cid,user_name,user_contact,email,username,password,budget,status,is_radio,is_local,is_outstation) VALUES ('$admin_id','$subgroup_id','$user_cid','$user_name', '$user_contact','$email', '$email', '$enc_pwd','$budget', '$status', '$is_radio', '$is_local','$is_outstation')");

				$new_id = mysqli_insert_id($this->db);			
				if($new_id)
				{
					//Get group_id from subgroup_id
					$sql = mysqli_query($this->db, "SELECT group_id from subgroups where id = '$subgroup_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
					$group_id = intval($rlt['group_id']);
					//Update group_id for new employee
					mysqli_query($this->db,"UPDATE users SET group_id = '$group_id' where id = '$new_id'");

					//Get all the employees of acquired spoc and update employees details with new group, subgroup and spoc ids
					$sql_people = mysqli_query($this->db, "SELECT * from people where user_id = '$acquired_spoc_id'");
					while($rlt_people = mysqli_fetch_array($sql_people, MYSQL_ASSOC))
					{
						$p_id = $rlt_people['id'];
						mysqli_query($this->db, "UPDATE people SET group_id='$group_id', subgroup_id='$subgroup_id',
							user_id='$new_id' WHERE id='$p_id' ");
					}

					//Update the status of acquired spoc as 2(i.e., Employees acquired)
					mysqli_query($this->db, "UPDATE users SET status='2' where id='$acquired_spoc_id'");

					$result = array('employee_id' => $new_id, 'message' => 'Employee Added Successfully');

					//Update subgroups allocated and unallocated field
					$sql = mysqli_query($this->db, "SELECT allocated, unallocated from subgroups where id = '$subgroup_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
					$subgroup_new_allocated = floatval($rlt['allocated']) + floatval($budget);
					$subgroup_new_unallocated = floatval($rlt['unallocated']) - floatval($budget); 
					mysqli_query($this->db,"UPDATE subgroups SET allocated = '$subgroup_new_allocated', unallocated = '$subgroup_new_unallocated' where id = '$subgroup_id'");

					//Send Email to Auth 1
					$sql = mysqli_query($this->db, "SELECT * from admins where id = '$admin_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
					$mail_body = sendRegistrationEmailToAuthenticatorSpoc($user_name, $email, 
						$password, $rlt['contact_no'], $rlt['email']);
					sendEmail($email,"[TCS] Registered as Spoc",$mail_body);

					//Send SMS to spoc
					$m = "Dear ".$user_name.",\n\nYou have been registered as Spoc.\nUsername: ".$email.".\nPassword: ".$password.".\nUrl: http://business.taxivaxi.in/\n\n".
					"Please call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($user_contact, $m);
					
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage()." ".$e->getLine());
				$this->response($this->json($response),200);
			}
		}

		//View Spoc
		private function viewSpoc()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$employee_id = $this->_request['spoc_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT e.*, sg.is_radio `subgroup_is_radio`, sg.is_local `subgroup_is_local`, sg.is_outstation `subgroup_is_outstation`, sg.unallocated `subgroup_unallocated` from users `e` left join subgroups `sg` on e.subgroup_id = sg.id where e.id = '$employee_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);

					$result = array('access_token' => $token_id,'Employee'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'No such Employee Found');
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Edit Spoc
		private function editSpoc()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);

			$user_id = $this->_request['user_id'];
			$subgroup_id = $this->_request['subgroup_id'];
			$user_cid = $this->_request['user_cid'];
			$user_name = $this->_request['user_name'];
			$email = $this->_request['email'];
			$budget = $this->_request['budget'];
			$is_radio = $this->_request['is_radio'];
			$is_local = $this->_request['is_local'];
			$is_outstation = $this->_request['is_outstation'];
			$status = $this->_request['status'];

			try
			{
				//Get old value of employee budget and old value of employee subgroupid
				$sql2 = mysqli_query($this->db, "SELECT budget, subgroup_id from users where id = '$user_id' ");
				$rlt2 = mysqli_fetch_array($sql2, MYSQL_ASSOC);
				$old_budget = floatval($rlt2['budget']);
				$old_subgroup_id = $rlt2['subgroup_id'];
				$diff = floatval($budget) - floatval($old_budget);

				$query = mysqli_query($this->db,"UPDATE users SET user_cid = '$user_cid', subgroup_id = '$subgroup_id',  user_name='$user_name', email = '$email', budget='$budget', is_radio = '$is_radio', is_local = '$is_local',is_outstation = '$is_outstation', status = '$status' WHERE id = '$user_id'");
				if($query)
				{
					$result = array('employee_id' => $user_id, 'message' => 'Employee Updated Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					//Update subgroups allocated and unallocated field
					$sql = mysqli_query($this->db, "SELECT allocated, unallocated from subgroups where id = '$subgroup_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);

					if($old_subgroup_id == $subgroup_id)
					{
						if($dff >= 0)
						{
							$subgroup_new_allocated = floatval($rlt['allocated']) + floatval($diff);
							$subgroup_new_unallocated = floatval($rlt['unallocated']) - floatval($diff); 	
						}
						else
						{
							$subgroup_new_allocated = floatval($rlt['allocated']) - floatval($diff);
							$subgroup_new_unallocated = floatval($rlt['unallocated']) + floatval($budget); 
						}
						mysqli_query($this->db,"UPDATE subgroups SET allocated = '$subgroup_new_allocated', unallocated = '$subgroup_new_unallocated' where id = '$subgroup_id'");
					}
					else
					{
						$sql3 = mysqli_query($this->db, "SELECT allocated, unallocated from subgroups where id = '$old_subgroup_id' ");
						$rlt3 = mysqli_fetch_array($sql3, MYSQL_ASSOC);

						$new_subgroup_new_allocated = floatval($rlt['allocated']) + floatval($budget);
						$new_subgroup_new_unallocated = floatval($rlt['unallocated']) - floatval($budget); 
						mysqli_query($this->db,"UPDATE subgroups SET allocated = '$new_subgroup_new_allocated', unallocated = '$new_subgroup_new_unallocated' where id = '$subgroup_id'");

						$old_subgroup_new_allocated = floatval($rlt3['allocated']) - floatval($old_budget);
						$old_subgroup_new_unallocated = floatval($rlt3['unallocated']) + floatval($old_budget);
						mysqli_query($this->db,"UPDATE subgroups SET allocated = '$old_subgroup_new_allocated', unallocated = '$old_subgroup_new_unallocated' where id = '$old_subgroup_id'");
					}

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not edit!!');
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Delete Spoc
		private function deleteSpoc()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$employee_id = $this->_request['employee_id'];
			
			try
			{
				//Get employee budget
				$sql = mysqli_query($this->db, "SELECT budget, subgroup_id from users where id = '$employee_id' ");
				$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
				$employee_budget = floatval($rlt['budget']);
				$subgroup_id  = $rlt['subgroup_id'];

				$query = mysqli_query($this->db,"DELETE FROM users WHERE id = $employee_id");

				//get subgroups current allocated and unallocated
				$sql = mysqli_query($this->db, "SELECT allocated, unallocated from subgroups where id = '$subgroup_id' ");
				$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
				$subgroup_allocated = floatval($rlt['allocated']) - $employee_budget;
				$subgroup_unallocated = floatval($rlt['unallocated']) + $employee_budget;

				//update subgroups allocated and unallocated
				$query = mysqli_query($this->db,"UPDATE subgroups SET allocated = '$subgroup_allocated', unallocated = '$subgroup_unallocated' where id = '$subgroup_id'");
				if($query)
				{
					$result = array('group_id' => $group_id, 'message' => 'Employee Deleted Successfully');
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

//*********************************************************************************************************************//
													//SPOCS
		//**************************************************************************************************//


		//----------------------------------Spoc - Profile------------------------------//
	
		private function spoclogin()
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
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username) and !empty($password))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT * from users where username = '$username' AND password = '$enc_pwd'");
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$user_id = $result['id'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							if($result['status'] == '1')
							{
								mysqli_query($this->db,"INSERT into users_access_tokens (user_id, access_token) VALUES ('$user_id', '$token')");
								$result = array('access_token' => $token,'user'=>$result);
								$success = array('success' => "1", "error" => "","response"=>$result);
								$this->response($this->json($success), 200);
							}
							else
							{
								$response = array('success' => "0", 'error' => "Your Account has been Deactivated!!");
								$this->response($this->json($response),200);
							}
						}
						else
						{
							$response = array('success' => "0", 'error' => "Invalid Username or Password");
							$this->response($this->json($response),200);
						}					
						
					}
					else
					{
						$response = array('success' => "0", "error" => "Invalid Username");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username and Password");
					$this->response($this->json($response),200);	
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
		
		private function spoclogout()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkEmployee($token_id);
			
			try
			{
				mysqli_query($this->db, "DELETE from users_access_tokens  where access_token = '$token_id'");
				$sql = mysqli_query($this->db, "select * from users where id = '$user_id' LIMIT 1");
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

		//Get All Employees's Bookings with Details 
		private function getAllSpocBookings()
		{
			$token_id = $this->_request['access_token'];	
			$employee_id = $this->checkEmployee($token_id);
			$type = $this->_request['type'];

			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					b.*,
					sg.subgroup_name
					from user_bookings `ub` 
					left join bookings `b` on ub.booking_id = b.id 
					left join subgroups `sg` on b.subgroup_id = sg.id
					where ub.user_id = '$employee_id' and b.is_invoice = 0
					order by b.id desc");
				$current_time = time();

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$bk_time = strtotime($rlt['pickup_datetime']) + (2*60*60);
						if($type == '1') //Active Bookings
						{
							if($bk_time > $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '2') //Old Bookings
						{
							if($bk_time < $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '3') //Rejected Bookings
						{
							array_push($result, $rlt);
						}
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Employee By Spoc Id
		public function employeeBySpoc()
		{
			$token_id = $this->_request['access_token'];
			$spoc_id = $this->checkEmployee($token_id);
			try
			{
				$sql = mysqli_query($this->db, "SELECT * from people where user_id = '$spoc_id' order by people_name");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'People'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Add Booking
		public function addBooking()
		{
			$token_id = $this->_request['access_token'];	
			//Spoc Id
			$employee_id = $this->checkEmployee($token_id);

			$admin_id = intval($this->_request['admin_id']);
			$group_id = intval($this->_request['group_id']);
			$subgroup_id = intval($this->_request['subgroup_id']);
			$tour_type = intval($this->_request['tour_type']);

			$ass_code = $this->_request['ass_code'];
			$reason_booking = $this->_request['reason_booking'];

			$city_id;
			$pickup_location;
			$pickup_datetime;
			$rate_id;
			$taxi_type_id;
			$days;
			$drop_city_name;

			$no_of_seats = intval($this->_request['no_of_seats']);
			$a_id = array();
			$a_contact = array();
			/*$a_cid = array();
			$a_name = array();
			$a_contact = array();
			$a_email = array();*/
			$i = 1;
			for($i; $i<=(int)$no_of_seats; $i++)
            {
            	array_push($a_id,$this->_request['employee_id_'.$i]);
                // array_push($a_cid,$this->_request['employee_cid_'.$i]);
                // array_push($a_name,$this->_request['employee_name_'.$i]);
                array_push($a_contact,$this->_request['employee_contact_'.$i]);
                // array_push($a_email,$this->_request['employee_email_'.$i]);
            }

            /*while($i<5)
            {
            	array_push($a_cid,'');
                array_push($a_name,'');
                array_push($a_contact,'');
                array_push($a_email,'');

                $i++;
            }*/
            
           
			if(!$reason_booking)
			{
				$response = array('success' => "0", 'error' => 'Please enter reason of booking');
				$this->response($this->json($response),200);
			}

			if($tour_type == '0')
			{
				$pickup_location = $this->_request['pickup_location'];
				
				$drop_location = $this->_request['drop_location'];
				$pickup_datetime = $this->_request['pickup_datetime'];	
			}
			elseif ($tour_type == '1') 
			{
				$city_id = $this->_request['city_id'];
				$pickup_location = $this->_request['pickup_location'];
				$pickup_datetime = $this->_request['pickup_datetime'];
				$rate_id = $this->_request['rate_id'];
				$taxi_type_id = $this->_request['taxi_type_id'];
			}
			else
			{
				$city_id = $this->_request['city_id'];
				$pickup_location = $this->_request['pickup_location'];
				$pickup_datetime = $this->_request['pickup_datetime'];
				$taxi_type_id = $this->_request['taxi_type_id'];
				$days = $this->_request['days'];
				$drop_city_name = $this->_request['drop_city_name'];

				$sqlt = mysqli_query($this->db, "SELECT id from rates where city_id = '$city_id' and 
					taxi_type_id = '$taxi_type_id' and admin_id = '$admin_id' and tour_type = '2' ");
				$rltt = mysqli_fetch_array($sqlt, MYSQL_ASSOC);
				$rate_id = $rltt['id'];

			}	
			/*$no_of_seats = 1;*/
			
			if(!$pickup_location)
            {
				$response = array('success' => "0", 'error' => 'Please enter pickup location');
				$this->response($this->json($response),200);
			}

			$timestamp = strtotime($pickup_datetime);
			$nice_pdate = date('D, d M Y h:i A',$timestamp);

			$booking_id;
			
			try
			{
				//Add Booking
				if($tour_type == '0')
				{
					$query = "INSERT into bookings (tour_type,pickup_location,drop_location,
						booking_date,pickup_datetime,
						created, modified,
						admin_id,group_id, subgroup_id,
						ass_code, reason_booking, no_of_seats) values ('$tour_type','$pickup_location', '$drop_location',
						now(),'$pickup_datetime',
						now(),now(),
						'$admin_id','$group_id', '$subgroup_id',
						'$ass_code', '$reason_booking', '$no_of_seats')";
					if (!mysqli_query($this->db,$query))
					  {
					  // echo("Error description: " . mysqli_error($con));
					  	$response = array('success' => "0", 'error' => mysqli_error($this->db));
						$this->response($this->json($response),200);
					  }
				}
				elseif ($tour_type == '1') 
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
					if (!mysqli_query($this->db,$query))
					  {
					  // echo("Error description: " . mysqli_error($con));
					  	$response = array('success' => "0", 'error' => mysqli_error($this->db));
						$this->response($this->json($response),200);
					  }
				}
				else
				{
					$query = "INSERT into bookings (tour_type,city_id,pickup_location,
						booking_date,pickup_datetime,rate_id,taxi_type_id, days, 
						drop_city_name,created, modified,
						admin_id,group_id, subgroup_id,
						ass_code, reason_booking, no_of_seats) values ('$tour_type','$city_id','$pickup_location',
						now(),'$pickup_datetime','$rate_id','$taxi_type_id','$days',
						'$drop_city_name',now(),now(),
						'$admin_id','$group_id', '$subgroup_id',
						'$ass_code', '$reason_booking', '$no_of_seats')";
					if (!mysqli_query($this->db,$query))
					  {
					  // echo("Error description: " . mysqli_error($con));
					  	$response = array('success' => "0", 'error' => mysqli_error($this->db));
						$this->response($this->json($response),200);
					  }
				}
				/*mysqli_query($this->db, $query);*/
				$booking_id = mysqli_insert_id($this->db);
				
				
				//Add Spocs Booking Table
				/*for($i=0; $i<(int)$no_of_seats; $i++)
	            {*/
	                $query = "INSERT into user_bookings (admin_id,group_id,subgroup_id,booking_id,user_id) values ('$admin_id','$group_id','$subgroup_id','$booking_id','$employee_id')";
					mysqli_query($this->db, $query);
	            /*}*/

				if($booking_id)
				{
					//Add Reference No
					$reference_no = "TVTCS".$booking_id;
		            try{
		                $query = "UPDATE bookings set
		                	reference_no = '$reference_no'
		                	where id = '$booking_id'";
						mysqli_query($this->db, $query);

						for($i=0;$i<$no_of_seats;$i++)
						{
							$eid = $a_id[$i];
							$q = "INSERT into people_bookings (booking_id, people_id) VALUES ('$booking_id', '$eid')";
							mysqli_query($this->db, $q);							
						}

						//Update contact numbers
						for($i=0;$i<$no_of_seats;$i++)
						{
							$eid = $a_id[$i];
							$eid = $a_id[$i];
							$econtact = $a_contact[$i];
							$q = "UPDATE people set people_contact = '$econtact' where id = '$eid'";
							mysqli_query($this->db, $q);							
						}


					}
					catch(Exception $e)
					{
						$response = array('success' => "0", 'error' => $e->getMessage());
						$this->response($this->json($response),200);			
					}
		            // }
					
					/////////////////////////////communication
					//Get package name
					$sqll = mysqli_query($this->db, "SELECT 
						r.package_name,
						tt.name `taxi_type_name` 
						from bookings `b` 
						left join rates `r` on b.rate_id = r.id
						left join taxi_types `tt` on b.taxi_type_id = tt.id 
						where b.id = '$booking_id'");
					$resultl = mysqli_fetch_array($sqll,MYSQL_ASSOC);
					
					//Generete common varialble for email
					// $pickup_datetime = date();
					$booking_details['reference_no'] = $reference_no;
					$booking_details['pickup_location'] = $pickup_location;
					$booking_details['pickup_datetime'] = $pickup_datetime;
					if($tour_type == '0')
					{
						$booking_details['trip'] = 'Radio Taxi';
					}
					elseif($tour_type == '1')
					{
						$booking_details['trip'] = 'Local Package';	
					}
					else
					{
						$booking_details['trip'] = 'Outstation';
					}

					if($tour_type == '0')
					{
						$booking_details['package'] = 'Radio';
					}
					elseif($tour_type == '1')
					{
						$booking_details['package'] = $resultl['package_name'];	
					}
					else
					{
						$booking_details['package'] = $days . " Days";
					}

					if($tour_type == '0')
					{
						$booking_details['taxi_type_name'] = 'Sedan';
					}
					else
					{
						$booking_details['taxi_type_name'] = $resultl['taxi_type_name'];	
					}
					
					//Get taxi details from taxi_model_id
					$sqll = mysqli_query($this->db, "SELECT tt.name  from bookings `b` left join taxi_types `tt`
						on b.taxi_type_id = tt.id where b.id = '$booking_id'");
					$resultl = mysqli_fetch_array($sqll,MYSQL_ASSOC);
					
					if($tour_type == '0')
					{
						$booking_details['car_type'] = 'Radio Taxi';
					}
					else
					{
						$booking_details['car_type'] = $resultl['name'];	
					}
					
					//Generate spoc details for email
					$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
					$sqlt = mysqli_query($this->db, "SELECT 
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
					$sqla = mysqli_query($this->db, "SELECT 
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
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$mail_body = bookingDetailsToUser($booking_details, $i, $user_details, $employee_details);
						sendEmail($resultt['people_email'],"$reference_no - New Booking TCS",$mail_body);
		            }

		            $booking_details['booking_id'] = "TVTCS".$resulta['bk_id'];

		            //Communication to Spoc
		            $mail_body = bookingDetailsToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail($booking_details['email'],"$reference_no - New Booking TCS",$mail_body);

		            //Communication to Approver 1
		            $mail_body = bookingDetailsToAuthone($booking_details, $i, $user_details, $employee_details);
					sendEmail($resulta['email_approve_1'],"$reference_no - New Booking TCS",$mail_body);

					//Communication to Approver 2
		            $mail_body = bookingDetailsToAuthtwo($booking_details, $i, $user_details, $employee_details);
					sendEmail($resulta['email_approver_2'],"$reference_no - New Booking TCS",$mail_body);

					//sms to approver 1
					$m = "Dear ".$booking_details['approver_1'].",\n\nWe have got a booking with id ".$booking_details['booking_id']." from ".$booking_details['name'].".\nWe request you to take appropriate action on the same.\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($resulta['contact_approver_1'], $m);

					//sms to spoc
					$m = "Dear ".$booking_details['name'].",\n\nBooking successfully registered with id ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);

					//sms to taxivaxi
					$m = "New Booking - ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\nTaxiVaxi";
					sendESMS('9990045853', $m);

					//sms to neeraj
					$m = "New Booking - ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\nTaxiVaxi";
					sendESMS('8860375872', $m);
					
					//sms to Ankit
					sendESMS('9990045953', $m);

					//Communicate to all employees
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$m = "Dear ".$resultt['people_name'].",\n\nWe have got a booking with id ".$booking_details['booking_id']." from ".$booking_details['name']." for your travel.\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
						sendESMS($resultt['people_contact'], $m);
		            }

					//Send Response
					$result = array('access_token' => $token_id,'Message'=>'Booking Added Successfully ','BookingDetails' => $result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Booking Not Added Successfully');
					$this->response($this->json($response),200);
				}	
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//----------------------------------People -Dashboard------------------------------//

		//Get All People with Details 
		private function getAllPeople()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			try
			{	
				$sql = mysqli_query($this->db, "SELECT 
					p.*,
					g.group_name,
					sg.subgroup_name,
					s.user_name `spoc_name`
					from people `p`
					left join groups `g` on p.group_id = g.id
					left join subgroups `sg` on p.subgroup_id = sg.id
					left join users `s` on p.user_id = s.id
					where p.admin_id ='$admin_id'"
					);

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'People'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Add People
		private function addPeople()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);

			$user_id = $this->_request['user_id'];     				//Spoc Id
			$people_cid = $this->_request['people_cid'];
			$people_name = $this->_request['people_name'];
			$people_email = $this->_request['people_email'];
			$people_contact = $this->_request['people_contact'];

			try
			{
					//Get details from spoc table (adminid, groupid, subgroupid)
					$sql = mysqli_query($this->db, "SELECT admin_id, group_id, subgroup_id from users where id = '$user_id' ");
					$rlt = mysqli_fetch_array($sql, MYSQL_ASSOC);
					$group_id = intval($rlt['group_id']);
					$subgroup_id = intval($rlt['subgroup_id']);

					//Add new people
					mysqli_query($this->db,"INSERT into people (admin_id,group_id,subgroup_id,user_id,people_cid,people_name,people_contact,people_email) VALUES ('$admin_id','$group_id','$subgroup_id','$user_id','$people_cid','$people_name', '$people_contact','$people_email')");
					$new_id = mysqli_insert_id($this->db);

					$result = array('people_id' => $new_id, 'message' => 'Employee Added Successfully');
					
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage()." ".$e->getLine());
				$this->response($this->json($response),200);
			}
		}

		//View People
		private function viewPeople()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$people_id = $this->_request['people_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					* 
					from people
					where id = '$people_id'");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);

					$result = array('access_token' => $token_id,'People'=>$result);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'No such Employee Found');
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Edit People
		private function editPeople()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);

			$people_id = $this->_request['people_id'];
			$people_cid = $this->_request['people_cid'];
			$people_name = $this->_request['people_name'];
			$people_contact = $this->_request['people_contact'];
			$people_email = $this->_request['people_email'];

			try
			{
				$query = mysqli_query($this->db,"UPDATE people SET people_cid = '$people_cid', people_contact = '$people_contact', people_name='$people_name', people_email = '$people_email' WHERE id = '$people_id'");
				if($query)
				{
					$result = array('people_id' => $people_id, 'message' => 'Employee Updated Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not edit!!');
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Delete People
		private function deletePeople()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$people_id = $this->_request['people_id'];
			
			try
			{
				$query = mysqli_query($this->db,"DELETE FROM people WHERE id = $people_id");

				if($query)
				{
					$result = array('people_id' => $people_id, 'message' => 'Employee Deleted Successfully');
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//*********************************************************************************************************************//
													//Sub-Group Auth (Level 1)
		//**************************************************************************************************//

		private function authonelogin()
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
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username) and !empty($password))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT sa.*, sg.budget from subgroup_authenticater `sa` left join subgroups `sg` on sa.subgroup_id = sg.id where sa.email = '$username' AND sa.password = '$enc_pwd' LIMIT 1");
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$user_id = $result['id'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							mysqli_query($this->db,"INSERT into subgroup_authenticater_acess_tokens (subgroup_authenticater_id, access_token) VALUES ('$user_id', '$token')");
							$result = array('access_token' => $token,'user'=>$result);
							$success = array('success' => "1", "error" => "","response"=>$result);
							$this->response($this->json($success), 200);
						}
						else
						{
							$response = array('success' => "0", 'error' => "Invalid Username or Password");
							$this->response($this->json($response),200);
						}					
						
					}
					else
					{
						$response = array('success' => "0", "error" => "Invalid Username");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username and Password");
					$this->response($this->json($response),200);	
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
		
		private function authonelogout()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkAuthone($token_id);
			
			try
			{
				mysqli_query($this->db, "DELETE from subgroup_authenticater_acess_tokens  where access_token = '$token_id'");
				$sql = mysqli_query($this->db, "select * from subgroup_authenticater where id = '$user_id' LIMIT 1");
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

		//Get All Authone's Bookings with Details 
		private function getAllAuthoneBookings()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthone($token_id);
			$type = $this->_request['type'];
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					b.*, 
					u.user_cid `user_cid`, u.user_name `user_name`, u.email `employee_email`
					from bookings `b`
					left join user_bookings `ub` on b.id = ub.booking_id
					left join users `u` on ub.user_id = u.id 
					where b.subgroup_id in (SELECT id from subgroups where authenticater_id ='$auth_id')
					order by b.id desc");
				$current_time = time();
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$bk_time = strtotime($rlt['pickup_datetime']) + (2*60*60);
						if($type == '1') //Active Bookings
						{
							if($bk_time > $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '2') //Old Bookings
						{
							if($bk_time < $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '3') // Rejected Bookings
						{
							array_push($result, $rlt);
						}
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function acceptAuthoneBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthone($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bookings set status_auth1 = '1', approved_date=now() where id = '$booking_id'");
				if($query)
				{

					$sqlb = mysqli_query($this->db, "SELECT b.*, r.package_name, tt.name `taxi_type_name` from bookings `b` left join rates `r` 
						on b.rate_id = r.id left join taxi_types `tt`
						on b.taxi_type_id = tt.id where b.id = '$booking_id' ");
					$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);
					
					
					//Generete common varialble for email
					$booking_details['reference_no'] = $resultb['reference_no'];
					$booking_details['pickup_location'] = $resultb['pickup_location'];
					$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];
					if($resultb['tour_type'] == '0')
					{
						$booking_details['trip'] = 'Radio Taxi';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['trip'] = 'Local Package';	
					}
					else
					{
						$booking_details['trip'] = 'Outstation';
					}

					if($resultb['tour_type'] == '0')
					{
						$booking_details['package'] = 'Radio';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['package'] = $resultb['package_name'];	
					}
					else
					{
						$booking_details['package'] = $resultb['days'] . " Days";
					}
					
					
					if($resultb['tour_type'] == '0')
					{
						$booking_details['car_type'] = 'Sedan';
					}
					else
					{
						$booking_details['car_type'] = $resultb['taxi_type_name'];	
					}

					//Generate spoc details for email
					$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
					$sqlt = mysqli_query($this->db, "SELECT 
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
						$employee_details = $employee_details.'Employee Id          : '.$e_id.'<br>Name          : '.$n.'<br>Company Name          : '.$c.'<br>Email          : '.$e.'<br><br>';
					}

					//Approver Details
					$sqla = mysqli_query($this->db, "SELECT 
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

					$booking_details['booking_id'] = "TVTCS".$resulta['bk_id'];
					
					//Communicate to all Authtwo
					/*$mail_body = bookingDetailsToAuthtwo($booking_details, $i, $user_details, $employee_details);
					sendEmail($resulta['email_approve_2'],"New Booking TCS",$mail_body);*/
					//Communicate to Taxivaxi
					/*$mail_body = bookingDetailsToTaxiVaxiAdmin($booking_details, $i, $user_details, $employee_details);
					sendEmail('corporate.tcs@taxivaxi.com',"New Booking TCS",$mail_body);*/

					//SMS to Taxivaxi
		            /*$m = "Dear Admin,\n\nWe have got a booking with id ".$booking_details['reference_no']." from TCS Portal.\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['trip'].".\n\nRgrds,\nTaxiVaxi.";
					sendESMS('+91'.'9990045853', $m);*/


					//Email to spoc
					$mail_body = bookingAcceptToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail( $booking_details['email'],$booking_details['reference_no']." - Booking Accepted By Approver",$mail_body);

					//SMS to Spoc
					$m = "Dear ".$booking_details['name'].",\n\nBooking with id ".$booking_details['booking_id']." has been accepted by "."Approver".".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['car_type'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);					

					//SMS to Approver 1
					$m = "Dear ".$booking_details['approver_1'].",\n\nBooking with id ".$booking_details['booking_id']." has been accepted.\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['car_type'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($resulta['contact_approver_1'], $m);					
					
					$result = array('booking_id' => $booking_id, 'message' => 'Booking Accepted Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Accept!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function rejectAuthoneBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthone($token_id);
			$booking_id = $this->_request['booking_id'];
			$reason = $this->_request['reason_cancel'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bookings set status_auth1 = '2', cancel_reason = '$reason', rejected_date=now() where id = '$booking_id'");
				if($query)
				{

					$sqlb = mysqli_query($this->db, "SELECT b.*, r.package_name, tt.name `taxi_type_name` from bookings `b` left join rates `r` 
						on b.rate_id = r.id left join taxi_types `tt`
						on b.taxi_type_id = tt.id where b.id = '$booking_id' ");
					$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);
					
					
					//Generete common varialble for email
					// $pickup_datetime = date();
					$booking_details['cancel_reason'] = $resultb['cancel_reason'];

					$booking_details['reference_no'] = $resultb['reference_no'];
					$booking_details['pickup_location'] = $resultb['pickup_location'];
					$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];
					if($resultb['tour_type'] == '0')
					{
						$booking_details['trip'] = 'Radio Taxi';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['trip'] = 'Local Package';	
					}
					else
					{
						$booking_details['trip'] = 'Outstation';
					}

					if($resultb['tour_type'] == '0')
					{
						$booking_details['package'] = 'Radio';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['package'] = $resultb['package_name'];	
					}
					else
					{
						$booking_details['package'] = $resultb['days'] . " Days";
					}
					
					
					if($resultb['tour_type'] == '0')
					{
						$booking_details['car_type'] = 'Sedan';
					}
					else
					{
						$booking_details['car_type'] = $resultb['taxi_type_name'];	
					}

					//Generate spoc details for email
					$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
					$sqlt = mysqli_query($this->db, "SELECT 
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

					//Dis-approver Details
					$sqla = mysqli_query($this->db, "SELECT 
						name `disapprover_1`, contact_no `contact_disapprover_1` 
						from subgroup_authenticater 
						where id = '$auth_id' ");
					$resulta = mysqli_fetch_array($sqla,MYSQL_ASSOC);

					$booking_details['disapprover_1'] = $resulta['disapprover_1'];

					$booking_details['booking_id'] = "TVTCS".$booking_id;

		            //Communicate to spocs
					$mail_body = rejectionByAuthOneToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail($booking_details['email'],$booking_details['reference_no']." - Booking Rejected!!",$mail_body);

					//SMS to SPOC
		            $m = "Dear ".$booking_details['name'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected by ".$booking_details['disapprover_1'].".\n\nPlease call your administrator for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);

					//SMS to approver 1
		            $m = "Dear ".$booking_details['disapprover_1'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected by ".$booking_details['disapprover_1'].".\n\nRgrds,\nTaxiVaxi.";
					sendESMS($resulta['contact_disapprover_1'], $m);
		            

		            //Communicate to all employees
		            /*$sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$mail_body = rejectionByAuthOneToSpoc($booking_details, $i, $user_details, $employee_details);
						sendEmail($resultt['people_email'],"TCS Booking Rejected!!",$mail_body);
		            }*/

		            

					//SMS to all Employees
					/*$sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{
						$m = "Dear ".$resultt['people_name'].",\n\nYour Booking Id ".$booking_details['reference_no']." has been rejected by ".$booking_details['disapprover_1'].".\n\nPlease call your administrator for any query.\n\nRgrds,\nTaxiVaxi";
						sendESMS('+91'.$resultt['people_contact'], $m);
					}*/

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Rejected Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Reject!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//*********************************************************************************************************************//
													//Group Auth (Level 2)
		//**************************************************************************************************//

		private function authtwologin()
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
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username) and !empty($password))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT ga.*, g.budget from group_authenticater `ga` left join groups `g` on ga.group_id = g.id where ga.email = '$username' AND ga.password = '$enc_pwd' LIMIT 1");
														
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$user_id = $result['id'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							mysqli_query($this->db,"INSERT into group_authenticater_access_tokens (group_authenticater_id, access_token) VALUES ('$user_id', '$token')");
							$result = array('access_token' => $token,'user'=>$result);
							$success = array('success' => "1", "error" => "","response"=>$result);
							$this->response($this->json($success), 200);
						}
						else
						{
							$response = array('success' => "0", 'error' => "Invalid Username or Password");
							$this->response($this->json($response),200);
						}					
						
					}
					else
					{
						$response = array('success' => "0", "error" => "Invalid Username");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username and Password");
					$this->response($this->json($response),200);	
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
		
		private function authtwologout()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkAuthtwo($token_id);
			
			try
			{
				mysqli_query($this->db, "DELETE from group_authenticater_access_tokens where access_token = '$token_id'");
				$sql = mysqli_query($this->db, "select * from group_authenticater where id = '$user_id' LIMIT 1");
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

		//Get All Authone's Bookings with Details   (Only Those Accepted by Level 1)
		private function getAllAuthtwoBookings()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthtwo($token_id);
			$type = $this->_request['type'];	
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					b.*,
					u.user_cid `user_cid`, u.user_name `user_name`, u.email `employee_email`
					from bookings `b`
					left join user_bookings `ub` on b.id = ub.booking_id
					left join users `u` on ub.user_id = u.id 
					where b.group_id in (SELECT id from groups where authenticater_id ='$auth_id') 
					order by b.id desc");
				$current_time = time();
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$bk_time = strtotime($rlt['pickup_datetime']) + (2*60*60);
						if($type == '1') //Active Bookings
						{
							if($bk_time > $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '2') //Old Bookings
						{
							if($bk_time < $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '3') // Rejected Bookings
						{
							array_push($result, $rlt);
						}
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function acceptAuthtwoBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthtwo($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bookings set status_auth2 = '1', approved_date=now() where id = '$booking_id'");
				if($query)
				{

					$sqlb = mysqli_query($this->db, "SELECT b.*, r.package_name, tt.name `taxi_type_name` from bookings `b` left join rates `r` 
						on b.rate_id = r.id left join taxi_types `tt`
						on b.taxi_type_id = tt.id where b.id = '$booking_id' ");
					$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);
					
					
					//Generete common varialble for email
					// $pickup_datetime = date();
					$booking_details['reference_no'] = $resultb['reference_no'];
					$booking_details['pickup_location'] = $resultb['pickup_location'];
					$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];
					if($resultb['tour_type'] == '0')
					{
						$booking_details['trip'] = 'Radio Taxi';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['trip'] = 'Local Package';	
					}
					else
					{
						$booking_details['trip'] = 'Outstation';
					}

					if($resultb['tour_type'] == '0')
					{
						$booking_details['package'] = 'Radio';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['package'] = $resultb['package_name'];	
					}
					else
					{
						$booking_details['package'] = $resultb['days'] . " Days";
					}
					
					
					if($resultb['tour_type'] == '0')
					{
						$booking_details['car_type'] = 'Sedan';
					}
					else
					{
						$booking_details['car_type'] = $resultb['taxi_type_name'];	
					}

					//Generate spoc details for email
					$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
					$sqlt = mysqli_query($this->db, "SELECT 
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
						$employee_details = $employee_details.'Employee Id          : '.$e_id.'<br>Name          : '.$n.'<br>Company Name          : '.$c.'<br>Email          : '.$e.'<br><br>';
					}

					//Approver Details
					$sqla = mysqli_query($this->db, "SELECT 
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

					$booking_details['booking_id'] = "TVTCS".$resulta['bk_id'];
					
					//Communicate to Taxivaxi
					/*for($i=1; $i<=1; $i++)
		            {*/	
						/*$mail_body = bookingDetailsToTaxiVaxiAdmin($booking_details, $i, $user_details, $employee_details);
						sendEmail('corporate.tcs@taxivaxi.com',"New Booking TCS",$mail_body);*/
						/*$m = "Dear ".$booking_details['name_'.$i].", we have got the booking from your company for you with id ".$reference_no.", pickup from ".$pickup_location." on ".$nice_pdate." for ".$taxi_model_name.". Driver details shall be shared 4 hrs prior to pickup. Rgrds, TaxiVaxi.";
						sendESMS($booking_details['contact_no_'.$i], $m);*/
		            /*}*/

		            //SMS to Taxivaxi
		            /*$m = "Dear Admin,\n\nWe have got a booking with id ".$booking_details['reference_no']." from TCS Portal.\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['trip'].".\n\nRgrds,\nTaxiVaxi.";
					sendESMS('+91'.'9990045853', $m);*/

					//Email to spoc
					$mail_body = bookingAcceptToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail( $booking_details['email'],$booking_details['reference_no']." - Booking Accepted By Approver",$mail_body);

					//SMS to Spoc
					$m = "Dear ".$booking_details['name'].",\n\nBooking with id ".$booking_details['booking_id']." has been accepted by "."Approver".".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['car_type'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);					

					//SMS to Approver 1
					$m = "Dear ".$booking_details['approver_1'].",\n\nBooking with id ".$booking_details['booking_id']." has been accepted.\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['car_type'].".\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($resulta['contact_approver_1'], $m);	

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Accepted Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Accept!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function rejectAuthtwoBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthtwo($token_id);
			$booking_id = $this->_request['booking_id'];
			if($this->_request['reason_cancel']){
				$reason = $this->_request['reason_cancel'];	
			}
			else
			{
				$reason = '';
			}
			
			try
			{
				$query = mysqli_query($this->db,"UPDATE bookings set status_auth2 = '2', cancel_reason = '$reason', rejected_date=now() where id = '$booking_id'");
				if($query)
				{

					$sqlb = mysqli_query($this->db, "SELECT b.*, r.package_name, tt.name `taxi_type_name` from bookings `b` left join rates `r` 
						on b.rate_id = r.id left join taxi_types `tt`
						on b.taxi_type_id = tt.id where b.id = '$booking_id' ");
					$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);
					
					
					//Generete common varialble for email
					// $pickup_datetime = date();
					$booking_details['cancel_reason'] = $resultb['cancel_reason'];

					$booking_details['reference_no'] = $resultb['reference_no'];
					$booking_details['pickup_location'] = $resultb['pickup_location'];
					$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];
					if($resultb['tour_type'] == '0')
					{
						$booking_details['trip'] = 'Radio Taxi';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['trip'] = 'Local Package';	
					}
					else
					{
						$booking_details['trip'] = 'Outstation';
					}

					if($resultb['tour_type'] == '0')
					{
						$booking_details['package'] = 'Radio';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['package'] = $resultb['package_name'];	
					}
					else
					{
						$booking_details['package'] = $resultb['days'] . " Days";
					}
					
					
					if($resultb['tour_type'] == '0')
					{
						$booking_details['car_type'] = 'Sedan';
					}
					else
					{
						$booking_details['car_type'] = $resultb['taxi_type_name'];	
					}

					//Generate spoc details for email
					$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
					$sqlt = mysqli_query($this->db, "SELECT 
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

					//Dis-approver Details
					$sqla = mysqli_query($this->db, "SELECT name `disapprover_2` from group_authenticater 
						where id = '$auth_id' ");
					$resulta = mysqli_fetch_array($sqla,MYSQL_ASSOC);

					$booking_details['disapprover_2'] = $resulta['disapprover_2'];

					$booking_details['booking_id'] = "TVTCS".$booking_id;

		            //Communicate to all spocs
					$mail_body = rejectionByAuthTwoToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail($booking_details['email'],$booking_details['reference_no']." - Booking Rejected!!",$mail_body);
		            
					//SMS to SPOC
		            $m = "Dear ".$booking_details['name'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected by ".$booking_details['disapprover_2'].".\n\nPlease call your administrator for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);

					//SMS to approver 1
		            $m = "Dear ".$booking_details['disapprover_1'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected by ".$booking_details['disapprover_1'].".\n\nRgrds,\nTaxiVaxi.";
					sendESMS($resulta['contact_disapprover_1'], $m);

		            //Communicate to all employees
		            /*$sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$mail_body = rejectionByAuthTwoToEmployee($booking_details, $i, $user_details, $employee_details);
						sendEmail($resultt['people_email'],"TCS Booking Rejected!!",$mail_body);
		            }*/

		            

					//SMS to all Employees
					/*$sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$m = "Dear ".$resultt['people_name'].",\n\nYour Booking Id ".$booking_details['reference_no']." has been rejected by ".$booking_details['disapprover_2'].".\n\nPlease call your administrator for any query.\n\nRgrds,\nTaxiVaxi";
						sendESMS('+91'.$resultt['people_contact'], $m);
		            }*/

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Rejected Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Reject!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function rejectSpocBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkEmployee($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bookings set status_user = '2', user_cancel_date=now() where id = '$booking_id'");
				if($query)
				{

					$sqlb = mysqli_query($this->db, "SELECT b.*, r.package_name, tt.name `taxi_type_name` from bookings `b` left join rates `r` 
						on b.rate_id = r.id left join taxi_types `tt`
						on b.taxi_type_id = tt.id where b.id = '$booking_id' ");
					$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);
					
					
					//Generete common varialble for email
					// $pickup_datetime = date();
					$booking_details['reference_no'] = $resultb['reference_no'];
					$booking_details['pickup_location'] = $resultb['pickup_location'];
					$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];
					if($resultb['tour_type'] == '0')
					{
						$booking_details['trip'] = 'Radio Taxi';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['trip'] = 'Local Package';	
					}
					else
					{
						$booking_details['trip'] = 'Outstation';
					}

					if($resultb['tour_type'] == '0')
					{
						$booking_details['package'] = 'Radio';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['package'] = $resultb['package_name'];	
					}
					else
					{
						$booking_details['package'] = $resultb['days'] . " Days";
					}
					
					
					if($resultb['tour_type'] == '0')
					{
						$booking_details['car_type'] = 'Sedan';
					}
					else
					{
						$booking_details['car_type'] = $resultb['taxi_type_name'];	
					}

					//Generate spoc details for email
					$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
					$sqlt = mysqli_query($this->db, "SELECT 
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

					$booking_details['booking_id'] = "TVTCS".$booking_id;

					//Communicate to spocs
					$mail_body = rejectionBySpocToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail($booking_details['email'],$booking_details['reference_no']." - Booking Rejected!!",$mail_body);

					//SMS to SPOC
		            $m = "Dear ".$booking_details['name'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);


		            //Communicate to all employees
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$mail_body = rejectionBySpocToEmployee($booking_details, $i, $user_details, $employee_details);
						sendEmail($resultt['people_email'],$booking_details['reference_no']." - Booking Rejected!!",$mail_body);
		            }

		            //SMS to employees
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$m = "Dear ".$resultt['people_name'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected by ".$booking_details['name'].".\n\nPlease call your administrator for any query.\n\nRgrds,\nTaxiVaxi.";
						sendESMS($resultt['people_contact'], $m);
		            }


					$result = array('booking_id' => $booking_id, 'message' => 'Booking Cancelled Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Cancel!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function taxivaxiforgot()
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			$username = $this->_request['username'];
			
			$password = randomPassword();
			$enc_pwd = md5($password);
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT * from taxivaxi_admins where username = '$username' LIMIT 1");
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$user_id = $result['id'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							$tsql = mysql_query("SELECT * from taxivaxi_admins where username = '$username'");
							$tresult = mysqli_fetch_array($tsql, MYSQL_ASSOC);
							$tpassword = $tresult['password'];
							mysqli_query($this->db, "UPDATE taxivaxi_admins set old_password = '$tpassword' where username = '$username'");
							mysqli_query($this->db, "UPDATE taxivaxi_admins set password = '$enc_pwd' where username = '$username'");

							$details['name'] = 'Admin';
							$details['password'] = $password;

							$mail_body = forgotPasswordMail($details);
							sendEmail($result['email'],"[TCS] Password Reset!!",$mail_body);				

							$result = array('access_token' => $token,'user'=>$result);
							$success = array('success' => "1", "error" => "","response"=>$result);
							$this->response($this->json($success), 200);
						}
						else
						{
							$response = array('success' => "0", 'error' => "Not such Email exisis with us!!");
							$this->response($this->json($response),200);
						}					
						
					}
					else
					{
						$response = array('success' => "0", "error" => "Invalid Username");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username/Email");
					$this->response($this->json($response),200);	
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

		private function spocforgot()
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			$username = $this->_request['username'];
			
			$password = randomPassword();
			$enc_pwd = md5($password);
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT * from users where username = '$username' LIMIT 1");
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$user_id = $result['id'];
							$user_password = $result['password'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							mysqli_query($this->db, "UPDATE users set old_password = '$user_password' where username = '$username'");
							mysqli_query($this->db, "UPDATE users set password = '$enc_pwd' where username = '$username'");

							$details['name'] = $result['user_name'];
							$details['password'] = $password;

							$mail_body = forgotPasswordMail($details);
							sendEmail($result['email'],"[TCS] Password Reset!!",$mail_body);				

							$result = array('access_token' => $token,'user'=>$result);
							$success = array('success' => "1", "error" => "","response"=>$result);
							$this->response($this->json($success), 200);
						}
						else
						{
							$response = array('success' => "0", 'error' => "Not such Email exisis with us!!");
							$this->response($this->json($response),200);
						}					
						
					}
					else
					{
						$response = array('success' => "0", "error" => "Invalid Username");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username/Email");
					$this->response($this->json($response),200);	
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

		private function adminforgot()
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			$username = $this->_request['username'];
			
			$password = randomPassword();
			$enc_pwd = md5($password);
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT * from admins where username = '$username' LIMIT 1");
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$user_id = $result['id'];
							$user_password = $result['password'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							mysqli_query($this->db, "UPDATE admins set old_password = '$user_password' where username = '$username'");
							mysqli_query($this->db, "UPDATE admins set password = '$enc_pwd' where username = '$username'");

							$details['name'] = $result['contact_name'];
							$details['password'] = $password;

							$mail_body = forgotPasswordMail($details);
							sendEmail($result['email'],"[TCS] Password Reset!!",$mail_body);				

							$result = array('access_token' => $token,'user'=>$result);
							$success = array('success' => "1", "error" => "","response"=>$result);
							$this->response($this->json($success), 200);
						}
						else
						{
							$response = array('success' => "0", 'error' => "Not such Email exisis with us!!");
							$this->response($this->json($response),200);
						}					
						
					}
					else
					{
						$response = array('success' => "0", "error" => "Invalid Email");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username/Email");
					$this->response($this->json($response),200);	
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

		private function authoneforgot()
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			$username = $this->_request['username'];
			
			$password = randomPassword();
			$enc_pwd = md5($password);
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT * from subgroup_authenticater where email = '$username' LIMIT 1");
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$user_id = $result['id'];
							$user_password = $result['password'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							mysqli_query($this->db, "UPDATE subgroup_authenticater set old_password = '$user_password' where email = '$username'");
							mysqli_query($this->db, "UPDATE subgroup_authenticater set password = '$enc_pwd' where email = '$username'");

							$details['name'] = $result['name'];
							$details['password'] = $password;

							$mail_body = forgotPasswordMail($details);
							sendEmail($result['email'],"[TCS] Password Reset!!",$mail_body);				

							$result = array('access_token' => $token,'user'=>$result);
							$success = array('success' => "1", "error" => "","response"=>$result);
							$this->response($this->json($success), 200);
						}
						else
						{
							$response = array('success' => "0", 'error' => "Not such Email exisis with us!!");
							$this->response($this->json($response),200);
						}					
						
					}
					else
					{
						$response = array('success' => "0", "error" => "Invalid Username");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username/Email");
					$this->response($this->json($response),200);	
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

		private function authtwoforgot()
		{
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}
			
			$username = $this->_request['username'];
			
			$password = randomPassword();
			$enc_pwd = md5($password);
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT * from group_authenticater where email = '$username' LIMIT 1");
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$user_id = $result['id'];
							$user_password = $result['password'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							mysqli_query($this->db, "UPDATE group_authenticater set old_password = '$user_password' where email = '$username'");
							mysqli_query($this->db, "UPDATE group_authenticater set password = '$enc_pwd' where email = '$username'");

							$details['name'] = $result['name'];
							$details['password'] = $password;

							$mail_body = forgotPasswordMail($details);
							sendEmail($result['email'],"[TCS] Password Reset!!",$mail_body);				

							$result = array('access_token' => $token,'user'=>$result);
							$success = array('success' => "1", "error" => "","response"=>$result);
							$this->response($this->json($success), 200);
						}
						else
						{
							$response = array('success' => "0", 'error' => "Not such Email exisis with us!!");
							$this->response($this->json($response),200);
						}					
						
					}
					else
					{
						$response = array('success' => "0", "error" => "Invalid Username");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username/Email");
					$this->response($this->json($response),200);	
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

		//*********************************************************************************************************************//
													//Taxivaxi Portal
		//**************************************************************************************************//

		private function taxivaxilogin()
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
			
			$token = uniqid(mt_rand(), true);
			$token = md5(uniqid(mt_rand(), true));
		
			try
			{
				// Input validations
				if(!empty($username) and !empty($password))
				{
					if(filter_var($username, FILTER_VALIDATE_EMAIL))
					{
					
						$sql = mysqli_query($this->db, "SELECT * from taxivaxi_admins where username = '$username' AND password = '$enc_pwd' LIMIT 1");
						
						if(mysqli_num_rows($sql) > 0)
						{
							$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
							$user_id = $result['id'];
							
							if($result['logo']!=NULL && $result['logo'] != "")
							{
								$base_url = BASE_URL."images/users/";
								$img_url = $base_url.$result['logo'];
								$result['logo'] = $img_url;
							}
							else
								$result['logo'] = "";
							// If success everythig is good send header as "OK" and user details
							mysqli_query($this->db,"INSERT into taxivaxi_admins_access_tokens (taxivaxi_admin_id, access_token) VALUES ('$user_id', '$token')");
							$result = array('access_token' => $token,'user'=>$result);
							$success = array('success' => "1", "error" => "","response"=>$result);
							$this->response($this->json($success), 200);
						}
						else
						{
							$response = array('success' => "0", 'error' => "Invalid Username or Password");
							$this->response($this->json($response),200);
						}					
						
					}
					else
					{
						$response = array('success' => "0", "error" => "Invalid Username");
						$this->response($this->json($response),200);	
					}
				}
				else
				{
					$response = array('success' => "0", "error" => "Please Enter Username and Password");
					$this->response($this->json($response),200);	
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
		
		private function taxivaxilogout()
		{
			$token_id = $this->_request['access_token'];	
			$user_id = $this->checkTaxivaxiAdmin($token_id);
			
			try
			{
				mysqli_query($this->db, "DELETE from taxivaxi_admins_access_tokens  where access_token = '$token_id'");
				$sql = mysqli_query($this->db, "select * from taxivaxi_admins where id = '$user_id' LIMIT 1");
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

		//Get All Bookings with Details 
		private function getAllTaxivaxiBookings()
		{
			$token_id = $this->_request['access_token'];
			$type = $this->_request['type'];		
			$auth_id = $this->checkTaxivaxiAdmin($token_id);
			try
			{
				/*$sql = mysqli_query($this->db, "SELECT 
					b.*,
					g.group_name 
					from bookings `b`
					left join groups `g` on b.group_id = g.id where b.status_user = 1 and (b.status_auth1 = 1 || b.status_auth2 = 1) 
					order by pickup_datetime asc");
				$current_time = time();*/
				//and DATE_SUB(NOW(), INTERVAL 12 HOUR) < b.pickup_datetime
				
				switch($type)
				{
					case '1':  //Active (Unassigned)
					{
						$query = "SELECT b.*, g.group_name 
						from bookings `b`
						left join groups `g` on b.group_id = g.id where b.status_user = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi < 2 order by pickup_datetime asc";
						break;
					}
					case '3':  //Archived
					{
						$query = "SELECT b.*, g.group_name 
						from bookings `b`
						left join groups `g` on b.group_id = g.id where b.status_user = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi = 3 and b.is_invoice = 0 and DATE_SUB(NOW(), INTERVAL 12 HOUR) > b.pickup_datetime order by pickup_datetime desc";
						break;
					}
					case '4': //Cancelled /Rejected
					{
						$query = "SELECT b.*, g.group_name 
						from bookings `b`
						left join groups `g` on b.group_id = g.id where b.status_user = 2 OR b.status_auth1 = 2 OR b.status_auth2 = 2 OR b.status_auth_taxivaxi = 2 order by pickup_datetime desc";
						break;
					}
					case '2': //Active (Assigned)
					{
						$query = "SELECT b.*, g.group_name 
						from bookings `b`
						left join groups `g` on b.group_id = g.id where b.status_user = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi = 3 and b.is_invoice = 0 and DATE_SUB(NOW(), INTERVAL 12 HOUR) < b.pickup_datetime order by pickup_datetime asc";
						break;
					}
				}
				
				$sql = mysqli_query($this->db,$query);
				
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						/*$bk_time = strtotime($rlt['pickup_datetime']) + (12*60*60);
						if($type == '1') //Active Bookings
						{
							if($bk_time > $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '2') //Old Bookings
						{
							if($bk_time < $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '3') // Rejected Bookings
						{
							array_push($result, $rlt);
						}*/
						$booking_id = $rlt['id'];
						
						$sql2 = mysqli_query($this->db,"select p.people_name `employee_name`,p.people_contact `employee_contact`,p.people_email `employee_email`,p.people_cid `employee_cid` from people `p` inner join people_bookings `pb` on pb.people_id = p.id where pb.booking_id = '$booking_id' LIMIT 1");
						
						if(mysqli_num_rows($sql2) > 0)
						{
							$rlt2 = mysqli_fetch_array($sql2, MYSQL_ASSOC);
							$rlt['employee_name'] = $rlt2['employee_name'];
							$rlt['employee_contact'] = $rlt2['employee_contact'];
							$rlt['employee_email'] = $rlt2['employee_email'];
							$rlt['employee_cid'] = $rlt2['employee_cid'];
							$result[] = $rlt;
						}
						
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function acceptTaxivaxiBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkTaxivaxiAdmin($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bookings set status_auth_taxivaxi = '1', tv_accept_reject_date=now() where id = '$booking_id'");
				if($query)
				{
					$result = array('booking_id' => $booking_id, 'message' => 'Booking Accepted Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Accept!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function rejectTaxivaxiBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkTaxivaxiAdmin($token_id);
			$booking_id = $this->_request['booking_id'];
			$reason = $this->_request['reason_cancel'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bookings set 
					status_auth_taxivaxi = '2' , cancel_reason = '$reason'
					where id = '$booking_id'");
				if($query)
				{

					$sqlb = mysqli_query($this->db, "SELECT 
						b.*, 
						r.package_name, tt.name `taxi_type_name`,
						a.contact_name `tcs_name`, a.contact_no `tcs_no`
						from bookings `b` 
						left join rates `r` on b.rate_id = r.id 
						left join taxi_types `tt`on b.taxi_type_id = tt.id 
						left join admins `a` on b.admin_id = a.id
						where b.id = '$booking_id' ");
					$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);

					//SMS to TCS
					$booking_details['tcs_name'] = $resultb['tcs_name'];
					$booking_details['tcs_no'] = $resultb['tcs_no'];

					$booking_details['cancel_reason'] = $resultb['cancel_reason'];
					
					//Generete common varialble for email
					// $pickup_datetime = date();
					$booking_details['reference_no'] = $resultb['reference_no'];
					$booking_details['pickup_location'] = $resultb['pickup_location'];
					$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];
					if($resultb['tour_type'] == '0')
					{
						$booking_details['trip'] = 'Radio Taxi';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['trip'] = 'Local Package';	
					}
					else
					{
						$booking_details['trip'] = 'Outstation';
					}

					if($resultb['tour_type'] == '0')
					{
						$booking_details['package'] = 'Radio';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['package'] = $resultb['package_name'];	
					}
					else
					{
						$booking_details['package'] = $resultb['days'] . " Days";
					}
					
					
					if($resultb['tour_type'] == '0')
					{
						$booking_details['car_type'] = 'Sedan';
					}
					else
					{
						$booking_details['car_type'] = $resultb['taxi_type_name'];	
					}

					//Generate user details for email
					$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
					$sqlt = mysqli_query($this->db, "SELECT 
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

					$booking_details['booking_id'] = "TVTCS".$booking_id;

		            //Communicate to spocs
					$mail_body = rejectionByTaxivaxiToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail($booking_details['email'],$booking_details['reference_no']." - Booking Rejected!!",$mail_body);
		            

		            //Communicate to all employees
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$mail_body = rejectionByTaxivaxiToEmployee($booking_details, $i, $user_details, $employee_details);
						sendEmail($resultt['people_email'],$booking_details['reference_no']." - TCS Booking Rejected!!",$mail_body);
		            }

		            //SMS to SPOC
		            $m = "Dear ".$booking_details['name'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected by TaxiVaxi due to unavailability of Cab.\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);

					//SMS to all Employees
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$m = "Dear ".$resultt['people_name'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected by TaxiVaxi due to unavailability of Cab.\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
						sendESMS($resultt['people_contact'], $m);
		            }

		            //SMS to TCS
		            /*$m = "Dear ".$booking_details['tcs_name'].",\n\nBooking Id ".$booking_details['reference_no']." created by your employee has been rejected by TaxiVaxi due to unavailability of Cab.\n\nPlease call at 9990045853 for any query.\n\nRgrds,\nTaxiVaxi";
					sendESMS('+91'.$booking_details['tcs_no'], $m);*/

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Rejected Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Reject!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function taximodelsByBookingId()
		{
			$booking_id = $this->_request['booking_id'];
			
			try
			{
				$sqlt = mysqli_query($this->db, "select taxi_type_id,admin_id from bookings where id='$booking_id'");
				$rltt = mysqli_fetch_array($sqlt, MYSQL_ASSOC);
				$taxi_type_id = $rltt['taxi_type_id'];
				$admin_id = $rltt['admin_id'];

				if($taxi_type_id)
				{
					$sql = mysqli_query($this->db, "SELECT * from taxi_models where admin_id='$admin_id' and taxi_type_id = '$taxi_type_id'");
					if(mysqli_num_rows($sql) > 0)
					{
						$result = array();
						while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
						{

							array_push($result, $rlt);
							// $result[] = $rlt;
						}
						$result = array('access_token' => $token_id,'Taxis'=>$result);
						$success = array('success' => "1", "error" => "","response"=> $result);
						$this->response($this->json($success), 200);
					} 
					else
					{
						$response = array('success' => "0", 'error' => 'No Result Found1-'.$taxi_type_id."--".$admin_id);
						$this->response($this->json($response),200);
					}
				}
				else
				{
					$sql = mysqli_query($this->db, "select * from taxi_models where admin_id='$admin_id'");
					if(mysqli_num_rows($sql) > 0)
					{
						$result = array();
						while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
						{

							array_push($result, $rlt);
							// $result[] = $rlt;
						}
						$result = array('access_token' => $token_id,'Taxis'=>$result);
						$success = array('success' => "1", "error" => "","response"=> $result);
						$this->response($this->json($success), 200);
					} 
					else
					{
						$response = array('success' => "0", 'error' => 'No Result Found2');
						$this->response($this->json($response),200);
					}
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function assignTaxivaxiBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkTaxivaxiAdmin($token_id);

			$booking_id = $this->_request['booking_id'];
			$driver_name = $this->_request['driver_name'];
			$driver_contact = $this->_request['driver_contact'];
			$operator_name = $this->_request['operator_name'];
			$taxi_model_id = $this->_request['taxi_model_id'];
			$taxi_reg_no = $this->_request['taxi_reg_no'];

			try
			{
				$query = mysqli_query($this->db,"UPDATE bookings set driver_name = '$driver_name',
				driver_contact='$driver_contact', operator_name = '$operator_name',
				taxi_model_id = '$taxi_model_id', taxi_reg_no = '$taxi_reg_no', is_assign = 1,
				status_auth_taxivaxi = 3, status = 'Assigned' , assign_date=now()
				where id = '$booking_id'");

				if($query)
				{

					$sqlb = mysqli_query($this->db, "SELECT 
						b.*, 
						r.package_name, 
						tm.name `taxi_model_namee`,
						a.contact_name `tcs_name`, a.contact_no `tcs_no` 
						from bookings `b` 
						left join rates `r` on b.rate_id = r.id 
						left join taxi_models `tm`on b.taxi_model_id = tm.id 
						left join admins `a` on b.admin_id = a.id
						where b.id = '$booking_id' ");
					$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);
						
					//For sms to tcs
					$booking_details['tcs_name'] = $resultb['tcs_name'];
					$booking_details['tcs_no'] = $resultb['tcs_no'];
					
					//Generete common varialble for email
					// $pickup_datetime = date();
					$booking_details['id'] = $resultb['id'];
					$booking_details['reference_no'] = $resultb['reference_no'];
					$booking_details['pickup_location'] = $resultb['pickup_location'];
					$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];

					if($resultb['tour_type'] == '0')
					{
						$booking_details['trip'] = 'Radio Taxi';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['trip'] = 'Local Package';	
					}
					else
					{
						$booking_details['trip'] = 'Outstation';
					}


					if($resultb['tour_type'] == '0')
					{
						$booking_details['package'] = 'Radio';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['package'] = $resultb['package_name'];	
					}
					else
					{
						$booking_details['package'] = $resultb['days'] . " Days";
					}
					

					if($resultb['tour_type'] == '0')
					{
						$booking_details['taxi_model_name'] = 'Sedan';
					}
					else
					{
						$booking_details['taxi_model_name'] = $resultb['taxi_type_namee'];	
					}
					
					
					$booking_details['taxi_reg_no'] = $resultb['taxi_reg_no'];
					$booking_details['driver_name'] = $resultb['driver_name'];
					$booking_details['driver_contact'] = $resultb['driver_contact'];
					

					//Generate spoc details for email
					$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
					$sqlt = mysqli_query($this->db, "SELECT 
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

					$booking_details['booking_id'] = "TVTCS".$booking_id;

		            //Communicate to all spocs
					$mail_body = assignMailToSpoc($booking_details, $i, $user_details, $employee_details);
					sendEmail($booking_details['email'],$booking_details['reference_no']." - TCS Booking Assigned Successfully!!",$mail_body);
		            

		            //Communicate to all employees
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$mail_body = assignMailToEmployee($booking_details, $i, $user_details, $employee_details);
						sendEmail($resultt['people_email'],$booking_details['reference_no']." - TCS Booking Assigned Successfully!!",$mail_body);
		            }

		            //SMS to TCS Admin
		            /*$m = "Dear ".$booking_details['tcs_name'].",\n\nFor booking with id ".$booking_details['reference_no']." from your employee.\nDriver: ".$booking_details['driver_name']." (".$booking_details['driver_contact'].")\nTaxi: ".$booking_details['taxi_model_name']." (".$booking_details['taxi_reg_no'].")\n\nPlease call at +919990045853 for any query.\n\nRgrds,\nTaxiVaxi";
					sendESMS('+91'.$booking_details['tcs_no'], $m);*/

					//SMS to SPOC
		            $m = "Dear ".$booking_details['name'].",\n\nFor your booking with id ".$booking_details['booking_id'].".\nDriver: ".$driver_name." (".$driver_contact.")\nTaxi: ".$booking_details['taxi_model_name']." (".$taxi_reg_no.")\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi";
					sendESMS($booking_details['user_contact'], $m);

					//SMS to all Employees
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$m = "Dear ".$resultt['people_name'].",\n\nFor your booking with id ".$booking_details['booking_id'].".\nDriver: ".$driver_name." (".$driver_contact.")\nTaxi: ".$booking_details['taxi_model_name']." (".$taxi_reg_no.")\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi";
						sendESMS($resultt['people_contact'], $m);
		            }

		            //SMS to driver
					$sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id' LIMIT 1");
					$resulttmp = mysqli_fetch_array($sqlt, MYSQL_ASSOC);
					$m = "Booking from TaxiVaxi.\nBooking Id: TVTCS".$booking_id."\nCar Type: ".$booking_details['taxi_model_name']."\nPackage: ".$booking_details['trip']."\nUse: ".$booking_details['package']."\nFrom: ".$booking_details['pickup_location']."\nTime: ".date('d M Y - h:i a', strtotime($resultb['pickup_datetime']))."\nGuest: ".$resulttmp['people_name']."-".$resulttmp['people_contact']."\n\nCarry Duty Slip and put the booking Id. Report 15 mins before time.\n\nRegards\nTaxiVaxi";
					sendESMS($driver_contact, $m);

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Assigned Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Assign!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		
		private function viewBookingTaxivaxi()
		{
			$token_id = $this->_request['access_token'];	
			// $auth_id = $this->checkTaxivaxiAdmin($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$sql = mysqli_query($this->db, "SELECT b.*, 
					r.package_name `rate_name`, r.kms, r.hours, r.km_rate, r.hour_rate, r.base_rate, r.night_rate,
					tm.name `taxi_model_name_o`,
					u.user_name, u.email `user_email`, u.user_contact,
					i.sub_total `total` 
					from bookings `b` 
					left join rates `r` on b.rate_id = r.id  
					left join taxi_models `tm` on b.taxi_model_id = tm.id
					left join user_bookings `ub` on ub.booking_id = b.id
					left join users `u` on ub.user_id = u.id
					left join invoice `i` on b.invoice_id = i.id
					where  b.id = '$booking_id'");
				

				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);

					$result2 = array();
					$sql2 = mysqli_query($this->db, "SELECT 
							p.people_name, p.people_email, p.people_cid, p.people_contact
							from people `p`
							left join people_bookings `pb` on pb.people_id = p.id 
							where pb.booking_id = '$booking_id'");
					while ($rlt2 = mysqli_fetch_array($sql2, MYSQL_ASSOC)) 
					{
						array_push($result2, $rlt2);
					}
					$result['People'] = $result2;
					

					$result = array('access_token' => $token_id,'BookingDetail'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function addInvoiceRadio()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$booking_id = $this->_request['booking_id'];
            $tour_type = $this->_request['tour_type'];
            $taxi_model_name = $this->_request['taxi_model_name'];

            $hours_done = floatval($this->_request['hours_done']);
            $allowed_hrs = floatval($this->_request['allowed_hrs']);
            $extra_hours = floatval($this->_request['extra_hours']);
            $hour_rate = floatval($this->_request['hour_rate']);

            $extras = floatval($this->_request['extras']);

            $extra_hours_charge = floatval($this->_request['extra_hours_charge']);
            $kms_done = floatval($this->_request['kms_done']);
            $allowed_kms = floatval($this->_request['allowed_kms']);

            $extra_kms = floatval($this->_request['extra_kms']);
            $km_rate = floatval($this->_request['km_rate']);
            $extra_kms_charge = floatval($this->_request['extra_kms_charge']);

            $base_rate = floatval($this->_request['base_rate']);
            $total_ex_tax = floatval($this->_request['total_ex_tax']);
            $tax_rate = floatval($this->_request['tax_rate']);
            $tax = floatval($this->_request['tax']);
            $total = floatval($this->_request['total']);

            $taxivaxi_rate = floatval($this->_request['taxivaxi_rate']);
            $taxivaxi_charge = floatval($this->_request['taxivaxi_charge']);
            $taxivaxi_tax_rate = floatval($this->_request['taxivaxi_tax_rate']);
            $taxivaxi_tax_charge = floatval($this->_request['taxivaxi_tax_charge']);
            $sub_total = floatval($this->_request['sub_total']);  

			try
			{
				$sql = mysqli_query($this->db,"SELECT id from invoice where booking_id = '$booking_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'Invoice for this booking already exist');
					$this->response($this->json($response),200);
				}
				mysqli_query($this->db,"INSERT into invoice (booking_id,tour_type,taxi_model_name,
					hours_done,allowed_hrs,extra_hours,hour_rate,
					extra_hours_charge,kms_done,allowed_kms,
					extra_kms,km_rate,extra_kms_charge,
					base_rate,total_ex_tax,tax_rate,tax,total,
					taxivaxi_rate, taxivaxi_charge, taxivaxi_tax_rate, taxivaxi_tax_charge, sub_total, 
					extras) 
					VALUES ('$booking_id','$tour_type','$taxi_model_name',
						'$hours_done','$allowed_hrs','$extra_hours','$hour_rate',
						'$extra_hours_charge','$kms_done','$allowed_kms',
						'$extra_kms','$km_rate','$extra_kms_charge',
						'$base_rate','$total_ex_tax','$tax_rate','$tax','$total',
						'$taxivaxi_rate', '$taxivaxi_charge', '$taxivaxi_tax_rate', '$taxivaxi_tax_charge', '$sub_total',
						'$extras')");
				$invoice_id = mysqli_insert_id($this->db);			

				if($invoice_id)
				{
					$query = mysqli_query($this->db,"UPDATE bookings SET is_invoice=true, invoice_id='$invoice_id' WHERE id = '$booking_id'");

					$result = array('rate_id' => $invoice_id, 'message' => 'Invoice Generated Successfully');
					
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function addInvoiceLocal()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$booking_id = $this->_request['booking_id'];
            $tour_type = $this->_request['tour_type'];
            $rate_id = $this->_request['rate_id'];
            $rate_name = $this->_request['rate_name'];
            $taxi_model_id = $this->_request['taxi_model_id'];
            $taxi_model_name = $this->_request['taxi_model_name'];
            $pickup_date = $this->_request['pickup_date'];
            $pickup_time = $this->_request['pickup_time'];
            $drop_date = $this->_request['drop_date'];
            $drop_time = $this->_request['drop_time'];

            $hours_done = floatval($this->_request['hours_done']);
            $allowed_hrs = floatval($this->_request['allowed_hrs']);
            $extra_hours = floatval($this->_request['extra_hours']);
            $hour_rate = floatval($this->_request['hour_rate']);

            $extra_hours_charge = floatval($this->_request['extra_hours_charge']);
            $start_km = floatval($this->_request['start_km']);
            $end_km = floatval($this->_request['end_km']);
            $kms_done = floatval($this->_request['kms_done']);
            $allowed_kms = floatval($this->_request['allowed_kms']);

            $extra_kms = floatval($this->_request['extra_kms']);
            $km_rate = floatval($this->_request['km_rate']);
            $extra_kms_charge = floatval($this->_request['extra_kms_charge']);

            $parking = floatval($this->_request['parking']);
            $driver = floatval($this->_request['driver']);
            $base_rate = floatval($this->_request['base_rate']);
            $total_ex_tax = floatval($this->_request['total_ex_tax']);
            $tax_rate = floatval($this->_request['tax_rate']);
            $tax = floatval($this->_request['tax']);
            $total = floatval($this->_request['total']);
            
            $taxivaxi_rate = floatval($this->_request['taxivaxi_rate']);
            $taxivaxi_charge = floatval($this->_request['taxivaxi_charge']);
            $taxivaxi_tax_rate = floatval($this->_request['taxivaxi_tax_rate']);
            $taxivaxi_tax_charge = floatval($this->_request['taxivaxi_tax_charge']);
            $sub_total = floatval($this->_request['sub_total']);  
            
			try
			{
				$sql = mysqli_query($this->db,"SELECT id from invoice where booking_id = '$booking_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'Invoice for this booking already exist');
					$this->response($this->json($response),200);
				}
				mysqli_query($this->db,"INSERT into invoice (booking_id,tour_type,rate_id,taxi_model_id,pickup_date,pickup_time,
					drop_date,drop_time,hours_done,allowed_hrs,extra_hours,hour_rate,extra_hours_charge,
					start_km,end_km,kms_done,allowed_kms,extra_kms,km_rate,extra_kms_charge,
					parking,driver,base_rate,total_ex_tax,tax_rate,tax,total,taxivaxi_rate,taxivaxi_charge,taxivaxi_tax_rate,taxivaxi_tax_charge,
					rate_name,taxi_model_name,
					sub_total) 
					VALUES ('$booking_id','$tour_type','$rate_id','$taxi_model_id','$pickup_date','$pickup_time',
						'$drop_date','$drop_time','$hours_done','$allowed_hrs','$extra_hours','$hour_rate','$extra_hours_charge',
						'$start_km','$end_km','$kms_done','$allowed_kms','$extra_kms','$km_rate','$extra_kms_charge',
						'$parking','$driver','$base_rate','$total_ex_tax','$tax_rate','$tax','$total','$taxivaxi_rate','$taxivaxi_charge','$taxivaxi_tax_rate','$taxivaxi_tax_charge',
						'$rate_name', '$taxi_model_name',
						'$sub_total')");
				$invoice_id = mysqli_insert_id($this->db);			

				if($invoice_id)
				{
					$query = mysqli_query($this->db,"UPDATE bookings SET is_invoice=true, invoice_id='$invoice_id' WHERE id = '$booking_id'");

					$result = array('rate_id' => $invoice_id, 'message' => 'Invoice Generated Successfully');
					
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function viewInvoice()
		{
			$token_id = $this->_request['access_token'];	
			// $admin_id = $this->checkAdmin($token_id);
			$invoice_id = $this->_request['booking_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					i.*, 
					b.reference_no, b.duty_slip , b.booking_date
				 	from invoice `i` 
				 	left join bookings `b` on i.booking_id=b.id 
				 	where i.id = '$invoice_id'");

				$sql2 = mysqli_query($this->db, "SELECT b.*, 
					r.package_name `rate_name`, r.kms, r.hours, r.km_rate, r.hour_rate, r.base_rate, r.night_rate,
					tm.name `taxi_model_name_o`,
					u.user_name, u.email `user_email`, u.user_contact, u.user_cid,
					i.total 
					from bookings `b` 
					left join rates `r` on b.rate_id = r.id  
					left join taxi_models `tm` on b.taxi_model_id = tm.id
					left join user_bookings `ub` on ub.booking_id = b.id
					left join users `u` on ub.user_id = u.id
					left join invoice `i` on b.invoice_id = i.id
					where  i.id = '$invoice_id'");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$result2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);

					$result = array('access_token' => $token_id,'Invoice'=>$result, 'BookingDetails' => $result2);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => $booking_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//-----------------------------------------Utility-----------------------------------//

		//For further uses
		private function viewBooking()
		{
			$token_id = $this->_request['access_token'];	
			// $auth_id = $this->checkTaxivaxiAdmin($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$sql = mysqli_query($this->db, "SELECT b.*,
					u.user_name, u.email `employee_email` 
					from bookings `b` 
					left join user_bookings `ub` on ub.booking_id = b.id
					left join users `u` on ub.user_id = u.id
					where  b.id = '$booking_id'");


				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);

					if(is_null($result['rate_id']))
					{
						$result['rate_name'] = '';
						$result['kms'] = '';
						$result['hours'] = '';
						$result['km_rate'] = '';
						$result['hour_rate'] = '';
						$result['base_rate'] = '';
						$result['night_rate'] = '';
					}
					else
					{
						$sql2 = mysqli_query($this->db, "SELECT r.package_name `rate_name`, r.kms, r.hours, r.km_rate, r.hour_rate, r.base_rate, r.night_rate
							from booking `b`
							left join rates `r` on b.rate_id = r.id 
							where  b.id = '$booking_id'");

						$result2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);
						$result['rate_name'] = $result2['rate_name'] ;
						$result['kms'] = $result2['kms'] ;
						$result['hours'] = $result2['hours'] ;
						$result['km_rate'] = $result2['km_rate'] ;
						$result['hour_rate'] = $result2['hour_rate'] ;
						$result['base_rate'] = $result2['base_rate'] ;
						$result['night_rate'] = $result2['night_rate'] ;
					}

					if($result['taxi_model_id'] == 'NULL')
					{
						$result['taxi_model_name'] = '';
					}
					else
					{
						$sql3 = mysqli_query($this->db, "SELECT tm.name `taxi_model_name`
							from booking `b`
							left join taxi_models `tm` on b.taxi_model_id = tm.id 
							where  b.id = '$booking_id'");
						$result3 = mysqli_fetch_array($sql3,MYSQL_ASSOC);
						$result['taxi_model_name'] = $result3['package_name'] ;
					}

					$result = array('access_token' => $token_id,'BookingDetail'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getAllCities()
		{
			$token_id = $this->_request['access_token'];	
			$employee_id = $this->checkEmployee($token_id);

			$admin_id = $this->_request['admin_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "select * from cities where admin_id='$admin_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{

						array_push($result, $rlt);
						// $result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'Cities'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
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

		private function getAllTaxiTypes()
		{
			$token_id = $this->_request['access_token'];	
			$employee_id = $this->checkEmployee($token_id);

			$admin_id = $this->_request['admin_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "select * from taxi_types where admin_id='$admin_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{

						array_push($result, $rlt);
						// $result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'TaxiTypes'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
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

		private function getRateByCityAndTaxi()
		{
			$token_id = $this->_request['access_token'];	
			$employee_id = $this->checkEmployee($token_id);

			$city_id = $this->_request['city_id'];
			$taxi_type_id = $this->_request['taxi_type_id'];
			$admin_id = $this->_request['admin_id'];
			$tour_type = $this->_request['tour_type'];
			
			try
			{
				$sql = mysqli_query($this->db, "select * from rates  where city_id='$city_id' and taxi_type_id='$taxi_type_id' and admin_id='$admin_id' and tour_type = '$tour_type'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Pkages'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
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
	
		private function getAllAdminBusBookings()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$type 	  =  $this->_request['type'];
			try
			{
				$current_time = time();
				switch($type)
				{
					case '1': //Active Unassigned
					{
						$query  = "SELECT b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where ((b.status_auth1 = 1 or b.status_auth2 = 1) and b.status_spoc = 1 and b.status_auth_taxivaxi < 2) and b.admin_id = '$admin_id' order by b.time_range asc";
						break;
					}
					case '3': //Archived
					{
						$query  = "SELECT b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where b.status_spoc = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi = 3 and b.is_invoice = 0 and b.date_of_journey < DATE_SUB(NOW(), INTERVAL 24 HOUR) and b.admin_id = '$admin_id' order by b.time_range asc";
						break;
					}
					case '4': //Rejected
					{
						$query  = "SELECT b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where (b.status_spoc = 2 OR b.status_auth1 = 2 OR b.status_auth2 = 2 OR b.status_auth_taxivaxi = 2) and b.admin_id = '$admin_id'  order by b.time_range asc";
						break;
					}
					case '2': //Active Assigned
					{
						$query  = "SELECT b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where ((b.status_auth1 = 1 or b.status_auth2 = 1) and b.status_spoc = 1 and b.status_auth_taxivaxi = 3 and b.is_invoice = 0 and b.date_of_journey > DATE_SUB(NOW(), INTERVAL 24 HOUR)) and b.admin_id = '$admin_id' order by b.time_range asc";
						break;
					}
				}

				$sql = mysqli_query($this->db,$query);

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}


		private function getAllSpocBusBookings()
		{
			$token_id = $this->_request['access_token'];	
			$employee_id = $this->checkEmployee($token_id);
			$type = $this->_request['type'];

			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					bb.*,
					sg.subgroup_name
					from bus_bookings `bb`
					left join subgroups `sg` on sg.id = bb.subgroup_id
					where bb.user_id = $employee_id and bb.is_invoice = 0
					order by bb.id desc");
				$current_time = time();

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$bk_time = strtotime(explode("::",$rlt['time_range'])[0]) + (2*60*60);
						if($type == '1') //Active Bookings
						{
							if($bk_time > $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '2') //Old Bookings
						{
							if($bk_time < $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '3') //Rejected Bookings
						{
							array_push($result, $rlt);
						}
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}


		public function addBusBooking()
		{
			$token_id = $this->_request['access_token'];	
			//Spoc Id
			$employee_id = $this->checkEmployee($token_id);

			$admin_id = intval($this->_request['admin_id']);
			$group_id = intval($this->_request['group_id']);
			$subgroup_id = intval($this->_request['subgroup_id']);
			$tour_type = intval($this->_request['tour_type']);

			$ass_code = $this->_request['ass_code'];
			$reason_booking = $this->_request['reason_booking'];	

			$pickup_city = $this->_request['pickup_city'];
        	$drop_city = $this->_request['drop_city'];
        	$time_range = $this->_request['time_range'];
        	$age = $this->_request['age'];
        	
        	$id_proof_type = $this->_request['id_proof_type'];
        	$id_proof_no = $this->_request['id_proof_no'];
        	
        	$pickup_datetime_from = explode("::", $time_range)[1];
        	$pickup_datetime_to = explode("::", $time_range)[0];
        	
        	//$date_of_journey = explode(" ",$pickup_datetime_from)[0];
        	$date_of_journey = $pickup_datetime_from;
        	
        	
        	
        	$bus_type_priority_1 = $this->_request['bus_type_priority_1'];
        	$bus_type_priority_2 = $this->_request['bus_type_priority_2'];
        	$bus_type_priority_3 = $this->_request['bus_type_priority_3'];
        	$boarding_point = $this->_request['boarding_point'];
        	$people_id = $this->_request['people_id'];
        	
        	if(strtotime($pickup_datetime_to) < strtotime($pickup_datetime_from))
        	{
				$response = array('success' => "0", 'error' => 'Please select proper date range. To Datetime cannot be less than From Datetime');
				$this->response($this->json($response),200);
			}

        	$query = "INSERT into bus_bookings (admin_id, group_id, subgroup_id,
				people_id, user_id, pickup_city, drop_city,
				time_range,date_of_journey,age, id_proof_type, id_proof_no, bus_type_priority_1, bus_type_priority_2, bus_type_priority_3,
				 boarding_point, no_of_seats,
				assessment_code, reason_of_booking, 
				booking_datetime, created, modified) values ('$admin_id', '$group_id', '$subgroup_id',
				'$people_id', '$employee_id', '$pickup_city', '$drop_city',
				'$time_range','$date_of_journey','$age', '$id_proof_type','$id_proof_no', '$bus_type_priority_1', '$bus_type_priority_2', '$bus_type_priority_3','$boarding_point', 1,'$ass_code', '$reason_booking',now(), now(), now())";
				
			if (!mysqli_query($this->db,$query))
			  {
			  // echo("Error description: " . mysqli_error($con));
			  	$response = array('success' => "0", 'error' => mysqli_error($this->db));
				$this->response($this->json($response),200);
			  }

			  $booking_id = mysqli_insert_id($this->db);

			  $reference_no = "TVTCSBUS".$booking_id;
	            try{
	                $query = "UPDATE bus_bookings set
	                	reference_no = '$reference_no'
	                	where id = '$booking_id'";
					mysqli_query($this->db, $query);
				}
				catch(Exception $e)
				{
					$response = array('success' => "0", 'error' => $e->getMessage());
					$this->response($this->json($response),200);			
				}
				//$booking_details['booking_id'] = "TVTCS".$resulta['bk_id'];
				
			
				$sql = mysqli_query($this->db,"select b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where b.id = '$booking_id'");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$booking_details = mysqli_fetch_array($sql, MYSQL_ASSOC);
				}
				else
				{
					$response = array('success' => "0", 'error' => "Booking Not Added");
					$this->response($this->json($response),200);
				}
				
				$sql = mysqli_query($this->db,"select * from subgroup_authenticater where id = (select authenticater_id from subgroups where id = ".$booking_details['subgroup_id'].")");
					
				if(mysqli_num_rows($sql) > 0)
				{
					$approver1 = mysqli_fetch_array($sql, MYSQL_ASSOC);
					$booking_details['approver1_contact'] = $approver1['contact_no'];
					$booking_details['approver1_name'] = $approver1['name'];
					$booking_details['approver1_email'] = $approver1['email'];
					$booking_details['approver1_cid'] = $approver1['cid'];
				}
				
				$sql = mysqli_query($this->db,"select * from group_authenticater where id = (select authenticater_id from groups where id = ".$booking_details['group_id'].")");
				
				if(mysqli_num_rows($sql) > 0)
				{
					$approver2 = mysqli_fetch_array($sql, MYSQL_ASSOC);
					$booking_details['approver2_contact'] = $approver2['contact_no'];
					$booking_details['approver2_name'] = $approver2['name'];
					$booking_details['approver2_email'] = $approver2['email'];
					$booking_details['approver2_cid'] = $approver2['cid'];
				}
				
				$booking_details['booking_id'] = "TVTCSBUS".$booking_id;
				$ref_no = $booking_details['reference_no'];
				
				$user_details = $user_details.'Spoc Employee Id          : '.$booking_details['user_cid'].'<br>Name          : '.$booking_details['user_name'].'<br>Company Name          : '.$booking_details['corporate_name'].'<br>Email          : '.$booking_details['email'].'<br><br>';
					
					$employee_details = $employee_details.'Employee Id          : '.$booking_details['people_cid'].'<br>Name          : '.$booking_details['people_name'].'<br>Contact No          : '.$booking_details['people_contact'].'<br>Email          : '.$booking_details['people_email'].'<br><br>';
				
		          //Communication to Spoc
		            $mail_body = busBookingCreated($booking_details, $user_details, $employee_details,'spoc');
					sendEmail($booking_details['email'],"New Booking TCS - $ref_no",$mail_body);

		            //Communication to Approver 1
		            $mail_body = busBookingCreated($booking_details, $user_details, $employee_details,'approver1');
					sendEmail($booking_details['approver1_email'],"New Booking TCS - $ref_no",$mail_body);

					//Communication to Approver 2
		            $mail_body = busBookingCreated($booking_details, $user_details, $employee_details,'approver2');
					sendEmail($booking_details['approver2_email'],"New Booking TCS - $ref_no",$mail_body);


					//sms to approver 1
					$m = "Dear ".$booking_details['approver1_name'].",\n\nWe have got a bus booking with id ".$booking_details['reference_no']." from ".$booking_details['user_name'].".\nWe request you to take appropriate action on the same.\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['approver1_contact'], $m);

					//sms to spoc
					$m = "Dear ".$booking_details['user_name'].",\n\nYour Bus Booking is created and sent for approval.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);
					
					//sms to employee
					$m = "Dear ".$booking_details['people_name'].",\n\nYour Bus Booking is created and sent for approval.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['people_contact'], $m);

					//sms to taxivaxi
					$m = "New Bus Booking Requested.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nTaxiVaxi.";
					sendESMS('9990045853', $m);

					//sms to neeraj
					//$m = "New Booking - ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\nTaxiVaxi";
					sendESMS('8860375872', $m);
					
					//sms to Vinod
					sendESMS('9990045953', $m);

					

				$result = array('access_token' => $token_id,'Message'=>'Booking Added Successfully');
				$success = array('success' => "1", "error" => "","response"=>$result);
				$this->response($this->json($success), 200);
		}


		private function getAllAuthoneBusBookings()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthone($token_id);
			$type = $this->_request['type'];
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					bb.*, 
					u.user_cid `user_cid`, u.user_name `user_name`, u.email `employee_email`
					from bus_bookings `bb`
					left join users `u` on bb.user_id = u.id 
					where bb.subgroup_id = (SELECT subgroup_id from subgroup_authenticater where id ='$auth_id')
					order by bb.id desc");
				$current_time = time();
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$bk_time = strtotime(explode("::",$rlt['time_range'])[1]) + (2*60*60);
						if($type == '1') //Active Bookings
						{
							if($bk_time > $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '2') //Old Bookings
						{
							if($bk_time < $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '3') //Rejected Bookings
						{
							array_push($result, $rlt);
						}
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}


		private function acceptAuthoneBusBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthone($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bus_bookings set status_auth1 = '1', auth_accept_time=now() where id = '$booking_id'");
				if($query)
				{

					$sql = mysqli_query($this->db,"select b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where b.id = '$booking_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$booking_details = mysqli_fetch_array($sql, MYSQL_ASSOC);
					}
					else
					{
						$response = array('success' => "0", 'error' => "Booking Not Added");
						$this->response($this->json($response),200);
					}
					
					$sql = mysqli_query($this->db,"select * from subgroup_authenticater where id = '$auth_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$approver1 = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$booking_details['approver1_contact'] = $approver1['contact_no'];
						$booking_details['approver1_name'] = $approver1['name'];
						$booking_details['approver1_email'] = $approver1['email'];
						$booking_details['approver1_cid'] = $approver1['cid'];
					}
					
					$sql = mysqli_query($this->db,"select * from group_authenticater where id = (select authenticater_id from groups where id = ".$booking_details['group_id'].")");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$approver2 = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$booking_details['approver2_contact'] = $approver2['contact_no'];
						$booking_details['approver2_name'] = $approver2['name'];
						$booking_details['approver2_email'] = $approver2['email'];
						$booking_details['approver2_cid'] = $approver2['cid'];
					}
					
					$booking_details['booking_id'] = "TVTCSBUS".$booking_id;
					$ref_no = $booking_details['reference_no'];

					$user_details = 'Spoc Employee Id          : '.$booking_details['user_cid'].'<br>Name          : '.$booking_details['user_name'].'<br>Company Name          : '.$booking_details['corporate_name'].'<br>Email          : '.$booking_details['email'].'<br><br>';
					
					$employee_details = 'Employee Id          : '.$booking_details['people_cid'].'<br>Name          : '.$booking_details['people_name'].'<br>Contact No          : '.$booking_details['people_contact'].'<br>Email          : '.$booking_details['people_email'].'<br><br>';
					
					//Email to spoc
					$mail_body = BusBookingAcceptRejectToSpoc($booking_details, $user_details, $employee_details,'accepted');
					sendEmail( $booking_details['email'],"$ref_no - Booking Accepted By Approver",$mail_body);

					//SMS to Spoc
					$m = "Dear ".$booking_details['user_name'].",\n\nYour Bus Booking is accepted by your approver.\n\nID: ".$booking_details['reference_no']."\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);					

					//SMS to Approver 1
					$m = "Dear ".$booking_details['approver1_name'].",\n\nBus Booking is accepted.\n\nID: ".$booking_details['reference_no']."\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['approver1_contact'], $m);	
					
					//sms to taxivaxi
					$m = "Bus Booking Approved.\n\nID: ".$booking_details['reference_no']."\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nTaxiVaxi";
					sendESMS('9990045853', $m);

					//sms to neeraj
					//$m = "New Booking - ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\nTaxiVaxi";
					sendESMS('8860375872', $m);
					
					//sms to Ankit
					sendESMS('9990045953', $m);				
					
					$result = array('booking_id' => $booking_id, 'message' => 'Booking Accepted Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Accept!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}


		private function rejectAuthoneBusBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthone($token_id);
			$booking_id = $this->_request['booking_id'];
			$reason = $this->_request['reason_cancel'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bus_bookings set status_auth1 = '2', auth_reject_reason = '$reason', auth_reject_time=now() where id = '$booking_id'");
				if($query)
				{

					$sql = mysqli_query($this->db,"select b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where b.id = '$booking_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$booking_details = mysqli_fetch_array($sql, MYSQL_ASSOC);
					}
					else
					{
						$response = array('success' => "0", 'error' => "Booking Not Added");
						$this->response($this->json($response),200);
					}
					
					$sql = mysqli_query($this->db,"select * from subgroup_authenticater where id = '$auth_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$approver1 = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$booking_details['approver1_contact'] = $approver1['contact_no'];
						$booking_details['approver1_name'] = $approver1['name'];
						$booking_details['approver1_email'] = $approver1['email'];
						$booking_details['approver1_cid'] = $approver1['cid'];
					}
					
					$sql = mysqli_query($this->db,"select * from group_authenticater where id = (select authenticater_id from groups where id = ".$booking_details['group_id'].")");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$approver2 = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$booking_details['approver2_contact'] = $approver2['contact_no'];
						$booking_details['approver2_name'] = $approver2['name'];
						$booking_details['approver2_email'] = $approver2['email'];
						$booking_details['approver2_cid'] = $approver2['cid'];
					}
					
					$booking_details['booking_id'] = "TVTCSBUS".$booking_id;
					$ref_no = $booking_details['reference_no'];

					$user_details = 'Spoc Employee Id          : '.$booking_details['user_cid'].'<br>Name          : '.$booking_details['user_name'].'<br>Company Name          : '.$booking_details['corporate_name'].'<br>Email          : '.$booking_details['email'].'<br><br>';
					
					$employee_details = 'Employee Id          : '.$booking_details['people_cid'].'<br>Name          : '.$booking_details['people_name'].'<br>Contact No          : '.$booking_details['people_contact'].'<br>Email          : '.$booking_details['people_email'].'<br><br>';
					
					//Email to spoc
					$mail_body = BusBookingAcceptRejectToSpoc($booking_details, $user_details, $employee_details,'rejected');
					sendEmail( $booking_details['email'],"$ref_no - Booking Rejected By Approver",$mail_body);

					

					//SMS to Spoc
					$m = "Dear ".$booking_details['user_name'].",\n\nYour Bus Booking is rejected by your approver.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);					

					//SMS to Approver 1
					$m = "Dear ".$booking_details['approver1_name'].",\n\nBus Booking is rejected.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['approver1_contact'], $m);	

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Rejected Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Reject!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}



		private function getAllAuthtwoBusBookings()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthtwo($token_id);
			$type = $this->_request['type'];	
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					bb.*,
					u.user_cid `user_cid`, u.user_name `user_name`, u.email `employee_email`
					from bus_bookings `bb`
					left join users `u` on bb.user_id = u.id 
					where bb.group_id = (SELECT group_id from group_authenticater where id ='$auth_id') 
					order by bb.id desc");
				$current_time = time();
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$bk_time = strtotime(explode("::",$rlt['time_range'])[1]) + (2*60*60);
						if($type == '1') //Active Bookings
						{
							if($bk_time > $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '2') //Old Bookings
						{
							if($bk_time < $current_time)
								array_push($result, $rlt);
						}
						elseif($type == '3') //Rejected Bookings
						{
							array_push($result, $rlt);
						}
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}



		private function acceptAuthtwoBusBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthtwo($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bus_bookings set status_auth2 = '1', auth_accept_time=now() where id = '$booking_id'");
				if($query)
				{

					$sql = mysqli_query($this->db,"select b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where b.id = '$booking_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$booking_details = mysqli_fetch_array($sql, MYSQL_ASSOC);
					}
					else
					{
						$response = array('success' => "0", 'error' => "Booking Not Added");
						$this->response($this->json($response),200);
					}
					
					$sql = mysqli_query($this->db,"select * from group_authenticater where id = '$auth_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$approver2 = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$booking_details['approver2_contact'] = $approver2['contact_no'];
						$booking_details['approver2_name'] = $approver2['name'];
						$booking_details['approver2_email'] = $approver2['email'];
						$booking_details['approver2_cid'] = $approver2['cid'];
					}
					
					$sql = mysqli_query($this->db,"select * from subgroup_authenticater where id = (select authenticater_id from subgroups where id = ".$booking_details['subgroup_id'].")");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$approver1 = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$booking_details['approver1_contact'] = $approver1['contact_no'];
						$booking_details['approver1_name'] = $approver1['name'];
						$booking_details['approver1_email'] = $approver1['email'];
						$booking_details['approver1_cid'] = $approver1['cid'];
					}
					
					$booking_details['booking_id'] = "TVTCSBUS".$booking_id;
					$ref_no = $booking_details['reference_no'];

					$user_details = 'Spoc Employee Id          : '.$booking_details['user_cid'].'<br>Name          : '.$booking_details['user_name'].'<br>Company Name          : '.$booking_details['corporate_name'].'<br>Email          : '.$booking_details['email'].'<br><br>';
					
					$employee_details = 'Employee Id          : '.$booking_details['people_cid'].'<br>Name          : '.$booking_details['people_name'].'<br>Contact No          : '.$booking_details['people_contact'].'<br>Email          : '.$booking_details['people_email'].'<br><br>';
					
					//Email to spoc
					$mail_body = BusBookingAcceptRejectToSpoc($booking_details, $user_details, $employee_details,'accepted');
					sendEmail( $booking_details['email'],"$ref_no - Booking Accepted By Approver",$mail_body);

					//SMS to Spoc
					$m = "Dear ".$booking_details['user_name'].",\n\nYour Bus Booking is accepted by your approver.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);					

					//SMS to Approver 1
					$m = "Dear ".$booking_details['approver2_name'].",\n\nBus Booking is accepted.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['approver2_contact'], $m);	
					
					//sms to taxivaxi
					$m = "Bus Booking Approved.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nTaxiVaxi.";
					sendESMS('9990045853', $m);

					//sms to neeraj
					//$m = "New Booking - ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\nTaxiVaxi";
					sendESMS('8860375872', $m);
					
					//sms to Vinod
					sendESMS('9881102875', $m);				

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Accepted Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Accept!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}



		private function rejectAuthtwoBusBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkAuthtwo($token_id);
			$booking_id = $this->_request['booking_id'];
			if($this->_request['reason_cancel']){
				$reason = $this->_request['reason_cancel'];	
			}
			else
			{
				$reason = '';
			}
			
			try
			{
				$query = mysqli_query($this->db,"UPDATE bus_bookings set status_auth2 = '2', auth_reject_reason = '$reason', auth_reject_time=now() where id = '$booking_id'");
				if($query)
				{

					$sql = mysqli_query($this->db,"select b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where b.id = '$booking_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$booking_details = mysqli_fetch_array($sql, MYSQL_ASSOC);
					}
					else
					{
						$response = array('success' => "0", 'error' => "Booking Not Added");
						$this->response($this->json($response),200);
					}
					
					$sql = mysqli_query($this->db,"select * from group_authenticater where id = '$auth_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$approver2 = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$booking_details['approver2_contact'] = $approver2['contact_no'];
						$booking_details['approver2_name'] = $approver2['name'];
						$booking_details['approver2_email'] = $approver2['email'];
						$booking_details['approver2_cid'] = $approver2['cid'];
					}
					
					$sql = mysqli_query($this->db,"select * from subgroup_authenticater where id = (select authenticater_id from subgroups where id = ".$booking_details['subgroup_id'].")");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$approver1 = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$booking_details['approver1_contact'] = $approver1['contact_no'];
						$booking_details['approver1_name'] = $approver1['name'];
						$booking_details['approver1_email'] = $approver1['email'];
						$booking_details['approver1_cid'] = $approver1['cid'];
					}
					
					$booking_details['booking_id'] = "TVTCSBUS".$booking_id;
					$ref_no = $booking_details['reference_no'];

					$user_details = 'Spoc Employee Id          : '.$booking_details['user_cid'].'<br>Name          : '.$booking_details['user_name'].'<br>Company Name          : '.$booking_details['corporate_name'].'<br>Email          : '.$booking_details['email'].'<br><br>';
					
					$employee_details = 'Employee Id          : '.$booking_details['people_cid'].'<br>Name          : '.$booking_details['people_name'].'<br>Contact No          : '.$booking_details['people_contact'].'<br>Email          : '.$booking_details['people_email'].'<br><br>';
					
					//Email to spoc
					$mail_body = BusBookingAcceptRejectToSpoc($booking_details, $user_details, $employee_details,'rejected');
					sendEmail( $booking_details['email'],"$ref_no - Booking Rejected By Approver",$mail_body);


					//Email to spoc
					//$mail_body = bookingAcceptToSpoc($booking_details, $i, $user_details, $employee_details);
					//sendEmail( $booking_details['email'],"[TCS] Booking Accepted By Approver",$mail_body);

					//SMS to Spoc
					$m = "Dear ".$booking_details['user_name'].",\n\nYour Bus Booking is rejected by your approver.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);					

					//SMS to Approver 1
					$m = "Dear ".$booking_details['approver2_name'].",\n\nBus Booking is rejected.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['approver2_contact'], $m);	

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Rejected Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Reject!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}



		private function rejectSpocBusBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkEmployee($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bus_bookings set status_spoc = 2, spoc_cancel_date=now() where id = '$booking_id'");
				if($query)
				{

					/*$sqlb = mysqli_query($this->db, 
						"SELECT b.*, 
						r.package_name, tt.name `taxi_type_name` 
						from bookings `b` left join rates `r` 
						on b.rate_id = r.id left join taxi_types `tt`
						on b.taxi_type_id = tt.id where b.id = '$booking_id' ");
					$resultb = mysqli_fetch_array($sqlb,MYSQL_ASSOC);
					
					
					//Generete common varialble for email
					// $pickup_datetime = date();
					$booking_details['reference_no'] = $resultb['reference_no'];
					$booking_details['pickup_location'] = $resultb['pickup_location'];
					$booking_details['pickup_datetime'] = $resultb['pickup_datetime'];
					if($resultb['tour_type'] == '0')
					{
						$booking_details['trip'] = 'Radio Taxi';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['trip'] = 'Local Package';	
					}
					else
					{
						$booking_details['trip'] = 'Outstation';
					}

					if($resultb['tour_type'] == '0')
					{
						$booking_details['package'] = 'Radio';
					}
					elseif($resultb['tour_type'] == '1')
					{
						$booking_details['package'] = $resultb['package_name'];	
					}
					else
					{
						$booking_details['package'] = $resultb['days'] . " Days";
					}
					
					
					if($resultb['tour_type'] == '0')
					{
						$booking_details['car_type'] = 'Sedan';
					}
					else
					{
						$booking_details['car_type'] = $resultb['taxi_type_name'];	
					}

					//Generate spoc details for email
					$sqlt = mysqli_query($this->db, "SELECT u.user_cid, u.user_name, u.email, u.user_contact,
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
					$sqlt = mysqli_query($this->db, "SELECT 
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

					$booking_details['booking_id'] = "TVTCS".$booking_id;

					//Communicate to spocs
					$mail_body = rejectionBySpocToSpoc($booking_details, $i, $user_details, $employee_details);
					//sendEmail($booking_details['email'],"[TCS] Booking Rejected!!",$mail_body);

					//SMS to SPOC
		            $m = "Dear ".$booking_details['name'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected.\n\nRgrds,\nTaxiVaxi.";
					sendESMS('+91'.$booking_details['user_contact'], $m);


		            //Communicate to all employees
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$mail_body = rejectionBySpocToEmployee($booking_details, $i, $user_details, $employee_details);
						//sendEmail($resultt['people_email'],"TCS Booking Rejected!!",$mail_body);
		            }

		            //SMS to employees
		            $sqlt = mysqli_query($this->db, "SELECT 
						p.people_cid, p.people_name, p.people_email, p.people_contact
						from people_bookings `pb`
						left join people `p` on pb.people_id = p.id
						where pb.booking_id = '$booking_id'");
					while($resultt = mysqli_fetch_array($sqlt,MYSQL_ASSOC))
					{	
						$m = "Dear ".$resultt['people_name'].",\n\nBooking Id ".$booking_details['booking_id']." has been rejected by ".$booking_details['name'].".\n\nPlease call your administrator for any query.\n\nRgrds,\nTaxiVaxi.";
						sendESMS('+91'.$resultt['people_contact'], $m);
		            }*/


					$result = array('booking_id' => $booking_id, 'message' => 'Booking Cancelled Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Cancel!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}



		private function getAllTaxivaxiBusBookings()
		{
			$token_id = $this->_request['access_token'];
			$type = $this->_request['type'];		
			$auth_id = $this->checkTaxivaxiAdmin($token_id);
			try
			{
				//DATE_SUB(NOW(), INTERVAL 12 HOUR) < b.pickup_datetime
				//$sql = mysqli_query($this->db, "select b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where b.status_spoc = 1 and (b.status_auth1 = 1 || b.status_auth2 = 1) order by b.time_range asc");
				$current_time = time();
				switch($type)
				{
					case '1': //Active Unassigned
					{
						$query  = "SELECT b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where ((b.status_auth1 = 1 or b.status_auth2 = 1) and b.status_spoc = 1 and b.status_auth_taxivaxi < 2) order by b.time_range asc";
						break;
					}
					case '3': //Archived
					{
						$query  = "SELECT b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where b.status_spoc = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi = 3 and b.is_invoice = 0 and b.date_of_journey < DATE_SUB(NOW(), INTERVAL 24 HOUR) order by b.time_range asc";
						break;
					}
					case '4': //Rejected
					{
						$query  = "SELECT b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where b.status_spoc = 2 OR b.status_auth1 = 2 OR b.status_auth2 = 2 OR b.status_auth_taxivaxi = 2  order by b.time_range asc";
						break;
					}
					case '2': //Active Assigned
					{
						$query  = "SELECT b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id where ((b.status_auth1 = 1 or b.status_auth2 = 1) and b.status_spoc = 1 and b.status_auth_taxivaxi = 3 and b.is_invoice = 0 and b.date_of_journey > DATE_SUB(NOW(), INTERVAL 24 HOUR)) order by b.time_range asc";
						break;
					}
					
				}
				
				$sql = mysqli_query($this->db,$query);
				
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$result[] = $rlt;
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}



		private function acceptTaxivaxiBusBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkTaxivaxiAdmin($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bus_bookings set status_auth_taxivaxi = '1', taxivaxi_accept_time=now() where id = '$booking_id'");
				if($query)
				{
					$result = array('booking_id' => $booking_id, 'message' => 'Booking Accepted Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Accept!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}



		private function rejectTaxivaxiBusBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkTaxivaxiAdmin($token_id);
			$booking_id = $this->_request['booking_id'];
			$reason = $this->_request['reason_cancel'];
			try
			{
				$query = mysqli_query($this->db,"UPDATE bus_bookings set 
					status_auth_taxivaxi = '2' , taxivaxi_reject_time = now(), taxivaxi_comment = '$reason'
					where id = '$booking_id'");
				if($query)
				{

					$sql = mysqli_query($this->db,"select b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact,a.corporate_name,a.contact_name `corporate_contact_name`,a.contact_no `corporate_contact_no`,a.email `corporate_contact_email` from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id inner join admins `a` on b.admin_id = a.id where b.id = '$booking_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$booking_details = mysqli_fetch_array($sql, MYSQL_ASSOC);
					}
					else
					{
						$response = array('success' => "0", 'error' => "Booking Not Rejected");
						$this->response($this->json($response),200);
					}

					//SMS to SPOC
					$m = "Dear ".$booking_details['user_name'].",\n\nYour Booking Id ".$booking_details['reference_no']." has been rejected by TaxiVaxi due to unavailability of Bus.\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi";
					sendESMS($booking_details['user_contact'],$m);
					
					//SMS to Employee
					$m = "Dear ".$booking_details['people_name'].",\n\nYour Booking Id ".$booking_details['reference_no']." has been rejected by TaxiVaxi due to unavailability of Bus.\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi";
					sendESMS($booking_details['people_contact'],$m);

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Rejected Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Reject!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}



		private function assignTaxivaxiBusBooking()
		{
			$token_id = $this->_request['access_token'];	
			$auth_id = $this->checkTaxivaxiAdmin($token_id);

			$booking_id = $this->_request['booking_id'];
			
			$boarding_point_taxivaxi = $this->_request['boarding_point_taxivaxi'];
			$pickup_datetime_taxivaxi = $this->_request['pickup_datetime_taxivaxi'];
			$ticket_number = $this->_request['ticket_number'];
			$pnr_number = $this->_request['pnr_number'];
			$bus_type_allocated = $this->_request['bus_type_allocated'];
			$operator_name = $this->_request['operator_name'];
			$operator_contact = $this->_request['operator_contact'];
			$seat_number = $this->_request['seat_number'];

			try
			{
				$query = mysqli_query($this->db,"UPDATE bus_bookings set 
					boarding_point_taxivaxi='$boarding_point_taxivaxi', pickup_datetime_taxivaxi='$pickup_datetime_taxivaxi', 
					ticket_number = '$ticket_number',
					pnr_number='$pnr_number', bus_type_allocated = '$bus_type_allocated',
					operator_name = '$operator_name', operator_contact = '$operator_contact', seat_number = '$seat_number', is_assign = 1,status_auth_taxivaxi = 3 , taxivaxi_assign_time=now() where id = '$booking_id'");

				if($query)
				{

					$sql = mysqli_query($this->db,"select b.*,u.user_cid,u.user_name,u.user_contact,u.email,p.people_cid,p.people_name,p.people_email,p.people_contact,a.corporate_name,a.contact_name `corporate_contact_name`,a.contact_no `corporate_contact_no`,a.email `corporate_contact_email` from bus_bookings `b` inner join users `u` on b.user_id = u.id inner join people `p` on b.people_id = p.id inner join admins `a` on b.admin_id = a.id where b.id = '$booking_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$booking_details = mysqli_fetch_array($sql, MYSQL_ASSOC);
					}
					else
					{
						$response = array('success' => "0", 'error' => "Booking Not Added");
						$this->response($this->json($response),200);
					}
					
					/*$sql = mysqli_query($this->db,"select * from subgroup_authenticater where id = '$auth_id'");
					
					if(mysqli_num_rows($sql) > 0)
					{
						$approver1 = mysqli_fetch_array($sql, MYSQL_ASSOC);
						$booking_details['approver1_contact'] = $approver1['contact_no'];
						$booking_details['approver1_name'] = $approver1['name'];
						$booking_details['approver1_email'] = $approver1['email'];
						$booking_details['approver1_cid'] = $approver1['cid'];
					}*/
					
					$booking_details['booking_id'] = "TVTCSBUS".$booking_id;

					$user_details = $user_details.'Spoc Employee Id          : '.$booking_details['user_cid'].'<br>Name          : '.$booking_details['user_name'].'<br>Company Name          : '.$booking_details['corporate_name'].'<br>Email          : '.$booking_details['email'].'<br><br>';
					
					$employee_details = $employee_details.'Employee Id          : '.$booking_details['people_cid'].'<br>Name          : '.$booking_details['people_name'].'<br>Contact No          : '.$booking_details['people_contact'].'<br>Email          : '.$booking_details['people_email'].'<br><br>';

					//Email to spoc
					$mail_body = busBookingAssigned($booking_details, $user_details, $employee_details, 'spoc');
					sendEmail( $booking_details['email'],"Bus Ticket Booking Confirmation - ".$booking_details['reference_no'],$mail_body);
					
					//Email to employee
					$mail_body = busBookingAssigned($booking_details, $user_details, $employee_details, 'employee');
					sendEmail( $booking_details['email'],"Bus Ticket Booking Confirmation - ".$booking_details['reference_no'],$mail_body);

					//SMS to Spoc
					/*$m = "Dear ".$booking_details['user_name'].",\n\nYour Bus Booking is accepted by your approver.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['user_contact'], $m);					

					//SMS to Approver 1
					$m = "Dear ".$booking_details['approver1_name'].",\n\nBus Booking is accepted.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nPlease call at ".TAXIVAXI_NUMBER." for any query.\n\nRgrds,\nTaxiVaxi.";
					sendESMS($booking_details['approver1_contact'], $m);	
					
					//sms to taxivaxi
					$m = "Bus Booking Approved.\n\nID: ".$booking_details['reference_no']."\n\nFrom: ".$booking_details['pickup_city']."\nTo: ".$booking_details['drop_city']."\nJourney Date: ".$booking_details['date_of_journey']."\nBus Type: ".$booking_details['bus_type_priority_1']."\n\nTaxiVaxi.";
					sendESMS('9990045853', $m);

					//sms to neeraj
					//$m = "New Booking - ".$booking_details['booking_id'].".\n\nPickup from ".$booking_details['pickup_location']." on ".$booking_details['pickup_datetime'].".\nTrip Type: ".$booking_details['package'].".\nTaxi Type: ".$booking_details['taxi_type_name'].".\nTaxiVaxi";
					sendESMS('8860375872', $m);
					
					//sms to Vinod
					sendESMS('9881102875', $m);			*/	

					$result = array('booking_id' => $booking_id, 'message' => 'Booking Assigned Successfully');					
					$success = array('success' => "1", "error" => "","response"=>$result);

					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => 'Could not Assign!!');
					$this->response($this->json($response),200);
				}
			} 
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}



		private function viewBusBookingTaxivaxi()
		{
			$token_id = $this->_request['access_token'];	
			// $auth_id = $this->checkTaxivaxiAdmin($token_id);
			$booking_id = $this->_request['booking_id'];
			try
			{
				$sql = mysqli_query($this->db, "SELECT bb.*,
					u.user_name, u.email `user_email`, u.user_contact
					from bus_bookings `bb` 
					left join users `u` on bb.user_id = u.id
					where  bb.id = '$booking_id'");
				

				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);

					$result2 = array();
					$sql2 = mysqli_query($this->db, "SELECT 
							p.people_name, p.people_email, p.people_cid, p.people_contact
							from people `p`
							left join bus_bookings `bb` on bb.people_id = p.id 
							where bb.id = '$booking_id'");
					while ($rlt2 = mysqli_fetch_array($sql2, MYSQL_ASSOC)) 
					{
						array_push($result2, $rlt2);
					}
					$result['People'] = $result2;
					

					$result = array('access_token' => $token_id,'BookingDetail'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		public function assignOperator()
		{
			$token_id = $this->_request['access_token'];
			$auth_id = $this->checkTaxivaxiAdmin($token_id);

			$booking_id = $this->_request['booking_id'];
			$name = $this->_request['name'];
			$email = $this->_request['email'];
			$contact_no = $this->_request['contact_no'];

			mysqli_query($this->db,"UPDATE bookings SET operator_name = '$name',operator_email = '$email',operator_contact = '$contact_no' where id = '$booking_id'");

			$sqll = mysqli_query($this->db, "SELECT 
				b.*,
				r.package_name,
				tt.name `taxi_type_namee`,
				g.group_name
				from bookings `b`
				left join groups `g` on b.group_id = g.id
				left join rates `r` on b.rate_id = r.id
				left join taxi_types `tt` on b.taxi_type_id = tt.id 
				where b.id = '$booking_id'");
			$resultl = mysqli_fetch_array($sqll,MYSQL_ASSOC);
			
			//Generete common varialble for email
			// $pickup_datetime = date();
			$booking_details['booking_id'] = "TVTCS".$booking_id;
			$booking_details['city'] = $resultl['group_name'];
			$booking_details['pickup_datetime'] = date("d M Y - h:i a", strtotime($resultl['pickup_datetime']));
			$booking_details['pickup_location'] = $resultl['pickup_location'];
			$booking_details['drop_location'] = $resultl['drop_location'];
			$tour_type = $resultl['tour_type'];

			if($tour_type == '0')
			{
				$booking_details['tour_type'] = 'Radio Taxi';
			}
			elseif($tour_type == '1')
			{
				$booking_details['tour_type'] = 'Local Package';	
			}
			else
			{
				$booking_details['tour_type'] = 'Outstation';
			}

			if($tour_type == '0')
			{
				$booking_details['package'] = 'Radio';
			}
			elseif($tour_type == '1')
			{
				$booking_details['package'] = $resultl['package_name'];	
			}
			else
			{
				$booking_details['package'] = $resultl['days'] . " Days";
			}

			if($tour_type == '0')
			{
				$booking_details['taxi_type_name'] = 'Sedan';
			}
			else
			{
				$booking_details['taxi_type_name'] = $resultl['taxi_type_namee'];	
			}
			
			//Generate employee details for email
			$sqlt = mysqli_query($this->db, "SELECT 
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


            //Communication to Operator
            $mail_body = bookingDetailsToOperator($booking_details, $employee_details);
			sendEmail($email,"TaxiVaxi Booking | [TVTCS".$booking_id."] | [".$booking_details['pickup_datetime']."] | [".$booking_details['city']."]",$mail_body);

			//sms to operator
			$sqlt = mysqli_query($this->db, "SELECT 
				p.people_cid, p.people_name, p.people_email, p.people_contact
				from people_bookings `pb`
				left join people `p` on pb.people_id = p.id
				where pb.booking_id = '$booking_id' LIMIT 1");
			$resulttmp = mysqli_fetch_array($sqlt, MYSQL_ASSOC);
			$m = "Dear ".$name.",\nBooking from TaxiVaxi.\nBooking Id: TVTCS".$booking_id."\nCar Type: ".$booking_details['taxi_type_name']."\nPackage: ".$booking_details['tour_type']."\nUse: ".$booking_details['package']."\nFrom: ".$booking_details['pickup_location']."\nTime: ".date('d M Y - h:i a', strtotime($resultl['pickup_datetime']))."\nGuest: ".$resulttmp['people_name']."-".$resulttmp['people_contact']."\n\nEmail also Sent. Please allocate Car and Driver to this booking at the earliest.\nRegards\nTaxiVaxi";
			sendESMS($contact_no, $m);


			//Send Response
			$result = array('access_token' => $token_id,'Message'=> 'Send Successfully', "sms" => $m, "sql_result" => $resulttmp);
			$success = array('success' => "1", "error" => "","response"=>$result);
			$this->response($this->json($success), 200);
		}

		public function resetBooking()
		{
			$token_id = $this->_request['access_token'];
			$auth_id = $this->checkTaxivaxiAdmin($token_id);

			$booking_id = $this->_request['booking_id'];
			
			try
			{
				mysqli_query($this->db,"UPDATE bookings SET 
					operator_name = '', driver_name='', driver_contact='', taxi_type_name='',
					taxi_model_name='', taxi_reg_no='', status='', is_assign=0, status_auth_taxivaxi=1
					where id = '$booking_id'");
			}
			catch(Exception $e)
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),406);
			}

			//Send Response
			$result = array('access_token' => $token_id,'Message'=> 'Send Successfully');
			$success = array('success' => "1-".$booking_id, "error" => "","response"=>$result);
			$this->response($this->json($success), 200);
		}


		private function viewBusBookingTaxivaxiByInvoiceId()
		{
			$token_id = $this->_request['access_token'];	
			// $auth_id = $this->checkTaxivaxiAdmin($token_id);
			$invoice_id = $this->_request['booking_id'];
			try
			{
				$sql = mysqli_query($this->db, "SELECT bb.*,
					u.user_name, u.email `user_email`, u.user_contact
					from bus_bookings `bb` 
					left join users `u` on bb.user_id = u.id
					where  bb.invoice_id = '$invoice_id'");
				

				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);

					$result2 = array();
					$sql2 = mysqli_query($this->db, "SELECT 
							p.people_name, p.people_email, p.people_cid, p.people_contact
							from people `p`
							left join bus_bookings `bb` on bb.people_id = p.id 
							where bb.invoice_id = '$invoice_id'");
					while ($rlt2 = mysqli_fetch_array($sql2, MYSQL_ASSOC)) 
					{
						array_push($result2, $rlt2);
					}
					$result['People'] = $result2;
					

					$result = array('access_token' => $token_id,'BookingDetail'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}


		private function getAllTaxivaxiadminBusInvoices()
		{
			$token_id = $this->_request['access_token'];
			$type = intval($this->_request['type']);
			$auth_id = $this->checkTaxivaxiAdmin($token_id);
			try
			{
				$sql;
				if($type!=3) //This can be 3 or 7
				{
					$sql = mysqli_query($this->db, "SELECT 
						b.*,
						i.sub_total `bill_amount`, i.status `invoice_status`, i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`,  i.bill_id, i.accepted_by, i.rejected_by,
						u.user_cid, u.user_name, u.user_contact,
						p.people_cid, p.people_name, p.people_contact
						from bus_bookings `b`
						left join users `u` on b.user_id = u.id
						left join people `p` on b.people_id = p.id
						left join bus_invoice `i` on b.invoice_id = i.id
						where b.is_invoice = 1 and i.status = $type
						order by b.id desc");
				}
				else
				{
					$sql = mysqli_query($this->db, "SELECT 
						b.*,
						i.sub_total `bill_amount`, i.status `invoice_status`, i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`,  i.bill_id, i.accepted_by, i.rejected_by,
						u.user_cid, u.user_name, u.user_contact,
						p.people_cid, p.people_name, p.people_contact
						from bus_bookings `b`
						left join users `u` on b.user_id = u.id
						left join people `p` on b.people_id = p.id
						left join bus_invoice `i` on b.invoice_id = i.id
						where b.is_invoice = 1 and (i.status =3 or i.status=7)
						order by b.id desc");	
				}

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function getAllAdminBusInvoices()
		{
			$token_id = $this->_request['access_token'];
			$type = intval($this->_request['type']);
			$admin_id = $this->checkAdmin($token_id);
			try
			{	
				$sql;
				if($type!=7)
				{
					$sql = mysqli_query($this->db, "SELECT 
					b.*,
					i.sub_total `bill_amount`, i.status `invoice_status`,  i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.accepted_by, i.rejected_by, i.bill_id,
					u.user_cid, u.user_name, u.user_contact,
					p.people_cid, p.people_name, p.people_contact
					from bus_bookings `b`
					left join users `u` on u.id = b.user_id
					left join people `p` on p.id = b.people_id
					left join bus_invoice `i` on b.invoice_id = i.id
					where b.is_invoice = '1' and b.admin_id = '$admin_id' and i.status = $type
					order by b.id desc");
				}
				else
				{
					$sql = mysqli_query($this->db, "SELECT 
					b.*,
					i.sub_total `bill_amount`, i.status `invoice_status`,  i.previous_status `invoice_previous_status`, i.comment_taxivaxi `invoice_comment_taxivaxi`, i.comment_client `invoice_comment_client`, i.accepted_by, i.rejected_by, i.bill_id,
					u.user_cid, u.user_name, u.user_contact,
					p.people_cid, p.people_name, p.people_contact
					from bus_bookings `b`
					left join bus_invoice `i` on b.invoice_id = i.id
					left join users `u` on u.id = b.user_id
					left join people `p` on p.id = b.people_id
					where b.is_invoice = '1' and b.admin_id = '$admin_id' and ((i.status = '7' and i.previous_status = '2') or (i.status = '1' and i.previous_status = '7'))
					order by b.id desc");
				}

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}


		private function addBusInvoice()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$booking_id = $this->_request['booking_id'];
            
            $total = floatval($this->_request['total']);
            $taxivaxi_charge = floatval($this->_request['taxivaxi_charge']);
            $taxivaxi_tax_rate = floatval($this->_request['taxivaxi_tax_rate']);
            $taxivaxi_tax_charge = floatval($this->_request['taxivaxi_tax_charge']);
            $sub_total = floatval($this->_request['sub_total']);  

			try
			{
				try
				{
					mysqli_query($this->db,"INSERT into bus_invoice (booking_id,
					total,taxivaxi_charge, taxivaxi_tax_rate, taxivaxi_tax_charge, sub_total, 
					is_paid, created, modified) 
					VALUES ('$booking_id',
						'$total', '$taxivaxi_charge', '$taxivaxi_tax_rate', '$taxivaxi_tax_charge', '$sub_total',
						0, now(), now())");
				}
				catch(Exception $e)
				{
					$response = array('success' => "0", 'error' => $e->getMessage());
					$this->response($this->json($response),200);
				}

				$invoice_id = mysqli_insert_id($this->db);			

				if($invoice_id)
				{
					try
					{
						$query = mysqli_query($this->db,"UPDATE bus_bookings SET is_invoice=1, invoice_id='$invoice_id' WHERE id = '$booking_id'");	
					}
					catch(Exception $e)
					{
						$response = array('success' => "0", 'error' => $e->getMessage());
						$this->response($this->json($response),200);
					}

					$result = array('rate_id' => $invoice_id, 'message' => 'Invoice Generated Successfully');	
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => mysqli_error($this->db));
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		private function editBusInvoice()
		{
			if($this->get_request_method() != "POST")
			{
				$response = array('success' => "0", 'error' => "Method Not Acceptable");
				$this->response($this->json($response),406);	
			}

			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$invoice_id = $this->_request['invoice_id'];

            $total = floatval($this->_request['total']);
            $taxivaxi_charge = floatval($this->_request['taxivaxi_charge']);
            $taxivaxi_tax_rate = floatval($this->_request['taxivaxi_tax_rate']);
            $taxivaxi_tax_charge = floatval($this->_request['taxivaxi_tax_charge']);
            $sub_total = floatval($this->_request['sub_total']);  

			try
			{
				mysqli_query($this->db,"UPDATE bus_invoice SET 
					total='$total',
					taxivaxi_charge='$taxivaxi_charge', 
					taxivaxi_tax_rate='$taxivaxi_tax_rate', taxivaxi_tax_charge='$taxivaxi_tax_charge', 
					sub_total='$sub_total', modified=now()
					where id='$invoice_id'"
				);
				
				$success = array('success' => "1", "error" => "","response"=>"Update Successfully");
				$this->response($this->json($success), 200);
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}




		private function viewBusInvoice()
		{
			$token_id = $this->_request['access_token'];	
			// $admin_id = $this->checkAdmin($token_id);
			$invoice_id = $this->_request['booking_id'];
			
			try
			{
				$sql = mysqli_query($this->db, "SELECT 
					i.*, 
					b.reference_no, b.booking_datetime,b.operator_name,b.operator_contact,b.date_of_journey,b.pickup_datetime_taxivaxi
				 	from bus_invoice `i` 
				 	left join bus_bookings `b` on i.booking_id=b.id 
				 	where i.id = '$invoice_id'");

				$sql2 = mysqli_query($this->db, "SELECT b.*, 
					r.package_name `rate_name`, r.kms, r.hours, r.km_rate, r.hour_rate, r.base_rate, r.night_rate,
					tm.name `taxi_model_name_o`,
					u.user_name, u.email `user_email`, u.user_contact, u.user_cid,
					i.sub_total 
					from bus_bookings `b` 
					left join rates `r` on b.rate_id = r.id  
					left join taxi_models `tm` on b.taxi_model_id = tm.id
					left join user_bookings `ub` on ub.booking_id = b.id
					left join users `u` on b.user_id = u.id
					left join bus_invoice `i` on b.invoice_id = i.id
					where  i.id = '$invoice_id'");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$result2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);

					$result = array('access_token' => $token_id,'Invoice'=>$result, 'BookingDetails' => $result2);
					$success = array('success' => "1", "error" => "","response"=>$result);
					$this->response($this->json($success), 200);
				}
				else
				{
					$response = array('success' => "0", 'error' => $booking_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}
		
		private function getTaxiVaxiBookingReport()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkTaxivaxiAdmin($token_id);
			$group_id = $this->_request['group_id'];
			$subgroup_id = $this->_request['subgroup_id'];
			$spoc_id = $this->_request['spoc_id'];
			$from_date = $this->_request['from_date'];
			$to_date = $this->_request['to_date'];
			$type = $this->_request['type'];
			
			try
			{
				switch($type)
				{
					case '1':  //Active (Unassigned)
					{
						$query = "SELECT b.*, g.subgroup_name, u.user_name, u.user_contact, r.package_name, c.name `city_name`
						from bookings `b` 
						left join rates `r` on r.id = b.rate_id
						left join cities `c` on c.id = b.city_id
						inner join subgroups `g` on b.subgroup_id = g.id 
						inner join user_bookings `ub` on ub.booking_id = b.id inner join users `u` on ub.user_id = u.id 
						where b.status_user = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi < 2 order by pickup_datetime asc";
						break;
					}
					case '2':  //Archived
					{
						$query = "SELECT b.*, g.subgroup_name, u.user_name, u.user_contact, r.package_name, c.name `city_name`
						from bookings `b` 
						left join rates `r` on r.id = b.rate_id
						left join cities `c` on b.city_id = c.id
						inner join subgroups `g` on b.subgroup_id = g.id 
						inner join user_bookings `ub` on ub.booking_id = b.id inner join users `u` on ub.user_id = u.id
						where b.status_user = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi = 3 and b.is_invoice = 0 and DATE_SUB(NOW(), INTERVAL 12 HOUR) > b.pickup_datetime order by pickup_datetime desc";
						break;
					}
					case '3': //Cancelled /Rejected
					{
						$query = "SELECT b.*, g.subgroup_name, u.user_name, u.user_contact, r.package_name, c.name `city_name` 
						from bookings `b` 
						left join rates `r` on r.id = b.rate_id
						left join cities `c` on b.city_id = c.id
						inner join subgroups `g` on b.subgroup_id = g.id 
						inner join user_bookings `ub` on ub.booking_id = b.id inner join users `u` on ub.user_id = u.id
						where b.status_user = 2 OR b.status_auth1 = 1 OR b.status_auth2 = 1 OR b.status_auth_taxivaxi = 2 order by pickup_datetime desc";
						break;
					}
					case '4': //Active (Assigned)
					{
						$query = "SELECT b.*, g.subgroup_name, u.user_name, u.user_contact, r.package_name, c.name `city_name` 
						from bookings `b` 
						left join rates `r` on r.id = b.rate_id
						left join cities `c` on b.city_id = c.id
						inner join subgroups `g` on b.subgroup_id = g.id 
						inner join user_bookings `ub` on ub.booking_id = b.id inner join users `u` on ub.user_id = u.id
						where b.status_user = 1 and (b.status_auth1 = 1 OR b.status_auth2 = 1) and b.status_auth_taxivaxi = 3 and DATE_SUB(NOW(), INTERVAL 12 HOUR) < b.pickup_datetime order by pickup_datetime asc";
						break;
					}
				}
				
				$sql = mysqli_query($this->db,$query);

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$rlt2 = array();
						$booking_id = $rlt['id'];
						$rlt2['Booking ID'] = "TVTCS".$booking_id;
						switch($rlt['tour_type'])
						{
							case '0':
							{
								$rlt2['Tour Type'] = 'Radio';
								break;
							}
							case '1':
							{
								$rlt2['Tour Type'] = 'Local';
								break;
							}
							case '2':
							{
								$rlt2['Tour Type'] = 'Outstation';
								break;
							}
						}
						$rlt2['Package Name'] = $rlt['package_name'];
						$rlt2['City'] = $rlt['city_name'];
						if($type == '2')
						{
							$rlt2['Operator Name'] = $rlt['operator_name'];
							$rlt2['Operator Contact'] = $rlt['operator_contact'];
							$rlt2['Operator Email'] = $rlt['operator_email'];
						}
						$rlt2['Pickup Location'] = $rlt['pickup_location'];
						$rlt2['Drop Location'] = $rlt['drop_location'];
						$rlt2['Booking Date'] = $rlt['booking_date'];
						$rlt2['Pickup Date'] = $rlt['pickup_datetime'];
						
						$rlt2['Subgroup Name'] = $rlt['subgroup_name'];
						$rlt2['SPOC Details'] = $rlt['user_name']." (".$rlt['user_contact'].")";
						
						$sql1 = mysqli_query($this->db,"SELECT p.people_name,p.people_contact from people `p` left join people_bookings `pb` on p.id = pb.people_id where pb.booking_id = '$booking_id'");
						
						$passenger_details = "";
						
						$i=1;
						
						while($rlt1 = mysqli_fetch_array($sql1))
						{
							$passenger_details = $rlt1['people_name']." (".$rlt1['people_contact'].")";
							$rlt2['Passenger'.$i.' Details'] = $passenger_details;
							$i++;
						}
						
						for($j=$i; $j <= 6; $j++)
						{
							$rlt2['Passenger'.$j.' Details'] = "";
						}
						
						//$rlt2['Passenger Details'] = $passenger_details;
						array_push($result, $rlt2);
					}
					$result = array('access_token' => $token_id,'Bookings'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		//Billings
		private function adminCommentInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$comment = $this->_request['comment'];
			$admin_id = $this->checkAdmin($token_id);
			$rejected_by = $this->_request['rejected_by'];

			$query = mysqli_query($this->db,"UPDATE invoice SET previous_status=status, status=7, 
				comment_client='$comment', comment_taxivaxi='', rejected_by='$rejected_by', admin_reject_time=now() WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		private function adminCommentBusInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$comment = $this->_request['comment'];
			$admin_id = $this->checkAdmin($token_id);
			$rejected_by = $this->_request['rejected_by'];

			$query = mysqli_query($this->db,"UPDATE bus_invoice SET previous_status=status, status=7, 
				comment_client='$comment', comment_taxivaxi='', rejected_by='$rejected_by', admin_reject_time=now() WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		private function adminClearInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$admin_id = $this->checkAdmin($token_id);
			$accepted_by = $this->_request['accepted_by'];

			$query = mysqli_query($this->db,"UPDATE invoice SET previous_status=status, 
				status=6, accepted_by='$accepted_by', admin_accept_time=now() WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		private function adminClearBusInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$admin_id = $this->checkAdmin($token_id);
			$accepted_by = $this->_request['accepted_by'];

			$query = mysqli_query($this->db,"UPDATE bus_invoice SET previous_status=status, 
				status=6, accepted_by='$accepted_by', admin_accept_time=now() WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		private function spocCommentInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$comment = $this->_request['comment'];
			$admin_id = $this->checkEmployee($token_id);
			$rejected_by = $this->_request['rejected_by'];

			$query = mysqli_query($this->db,"UPDATE invoice SET previous_status=status, status=3, 
				comment_client='$comment', comment_taxivaxi='', rejected_by='$rejected_by', spoc_reject_time=now() WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		private function spocCommentBusInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$comment = $this->_request['comment'];
			$admin_id = $this->checkEmployee($token_id);
			$rejected_by = $this->_request['rejected_by'];

			$query = mysqli_query($this->db,"UPDATE bus_invoice SET previous_status=status, status=3, 
				comment_client='$comment', comment_taxivaxi='', rejected_by='$rejected_by', spoc_reject_time=now() WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		private function spocClearInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$admin_id = $this->checkEmployee($token_id);
			$accepted_by = $this->_request['accepted_by'];

			$query = mysqli_query($this->db,"UPDATE invoice SET previous_status=status, 
				status=2, accepted_by='$accepted_by', spoc_accept_time=now() WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		private function spocClearBusInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$admin_id = $this->checkEmployee($token_id);
			$accepted_by = $this->_request['accepted_by'];

			$query = mysqli_query($this->db,"UPDATE bus_invoice SET previous_status=status, 
				status=2, accepted_by='$accepted_by', spoc_accept_time=now() WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200) ;
		}

		private function taxivaxiAdminCommentInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$comment = $this->_request['comment'];
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$query = mysqli_query($this->db,"UPDATE invoice SET previous_status=status, status=1, comment_taxivaxi='$comment' WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		private function taxivaxiAdminCommentBusInvoice()
		{
			$token_id = $this->_request['access_token'];
			$invoice_id = intval($this->_request['invoice_id']);
			$comment = $this->_request['comment'];
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$query = mysqli_query($this->db,"UPDATE bus_invoice SET previous_status=status, status=1, comment_taxivaxi='$comment' WHERE id = $invoice_id");

			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		public function invoiceStatusBilled()
		{
			$token_id = $this->_request['access_token'];
			$invoice_ids = rtrim($this->_request['invoice_ids'], ",");
			$booking_ids = rtrim($this->_request['booking_ids'], ",");
			$tour_type = $this->_request['tour_type'];
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$sql_query2 = "SELECT SUM(sub_total) AS `sub_total` FROM invoice WHERE id IN(".$invoice_ids.")";
			$sql = mysqli_query($this->db, $sql_query2);
			$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
			$sum_amount = $result['sub_total'];

			mysqli_query($this->db, "INSERT INTO bills(invoice_ids, booking_ids, bill_amount, bill_type) VALUES ('$invoice_ids', '$booking_ids', '$sum_amount', '$tour_type')");
			$bill_id = (string)mysqli_insert_id($this->db);

			$sql_query = "UPDATE invoice SET previous_status=status, status=4, bill_id=".$bill_id.", is_billed=1 WHERE id IN(" . $invoice_ids . ")";
			$query = mysqli_query($this->db, $sql_query);
			

			$success = array('success' => "1", "error" => "","response"=> $result['sub_total'], "bill_id"=>$sql_query);
			$this->response($this->json($success), 200);
		}

		public function businvoiceStatusBilled()
		{
			$token_id = $this->_request['access_token'];
			$invoice_ids = rtrim($this->_request['invoice_ids'], ",");
			$booking_ids = rtrim($this->_request['booking_ids'], ",");
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$sql_query2 = "SELECT SUM(sub_total) AS `sub_total` FROM bus_invoice WHERE id IN(".$invoice_ids.")";
			$sql = mysqli_query($this->db, $sql_query2);
			$result = mysqli_fetch_array($sql,MYSQL_ASSOC);
			$sum_amount = $result['sub_total'];

			mysqli_query($this->db, "INSERT INTO bills(invoice_ids, booking_ids, bill_amount, bill_type) VALUES ('$invoice_ids', '$booking_ids', '$sum_amount', 3)");
			$bill_id = (string)mysqli_insert_id($this->db);

			$sql_query = "UPDATE bus_invoice SET previous_status=status, status=4, bill_id=".$bill_id.", is_billed=1 WHERE id IN(" . $invoice_ids . ")";
			$query = mysqli_query($this->db, $sql_query);
			

			$success = array('success' => "1", "error" => "","response"=> $result['sub_total'], "bill_id"=>$sql_query);
			$this->response($this->json($success), 200);
		}

		public function invoiceStatusPaid()
		{
			$token_id = $this->_request['access_token'];
			$invoice_ids = rtrim($this->_request['invoice_ids'], ",");
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$sql_query = "UPDATE invoice SET previous_status=status, status=5, is_paid=1 WHERE id IN(" . $invoice_ids . ")";
			$query = mysqli_query($this->db, $sql_query);
			

			$success = array('success' => "1", "error" => "","response"=> $result['sub_total'], "bill_id"=>$sql_query);
			$this->response($this->json($success), 200);
		}

		private function getAllBills()
		{
			$token_id = $this->_request['access_token'];
			$paid_status = intval($this->_request['type'])-1;
			// $auth_id = $this->checkTaxivaxiAdmin($token_id);
			try
			{
				$sql = mysqli_query($this->db, "SELECT * FROM bills WHERE is_paid=$paid_status");

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result = array('access_token' => $token_id,'Bills'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result);
					$this->response($this->json($success), 200);
				} 
				else
				{
					$response = array('success' => "0", 'error' => 'No Result Found', 'id' => $admin_id);
					$this->response($this->json($response),200);
				}
			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
		}

		public function billStatusPaid()
		{
			$token_id = $this->_request['access_token'];
			$bill_id = $this->_request['bill_id'];
			$bill_type = $this->_request['bill_type'];
			$payment_date = $this->_request['payment_date'];
			$payment_amount = $this->_request['payment_amount'];
			$payment_mode = $this->_request['payment_mode'];
			$payment_account_no = $this->_request['payment_account_no'];
			$admin_id = $this->checkTaxivaxiAdmin($token_id);

			$sql_query = "UPDATE bills SET 
				is_paid=1, payment_date='$payment_date', payment_amount='$payment_amount',
				payment_mode='$payment_mode', payment_account_no='$payment_account_no'
				WHERE id='$bill_id'";
			$query = mysqli_query($this->db, $sql_query);

			$invoice_ids = '';
			$sql = mysqli_query($this->db, "SELECT invoice_ids FROM bills WHERE id IN (" . $bill_id . ")");
			if(mysqli_num_rows($sql) > 0)
			{
				while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					$invoice_ids = $invoice_ids . $rlt['invoice_ids'] . ",";
			}
			$invoice_ids = rtrim($invoice_ids, ",");

			$sql_query2='';
			if($bill_type == '3')
				$sql_query2 = "UPDATE bus_invoice SET previous_status=status, status=5, is_paid=1 WHERE id IN(" . $invoice_ids . ")";
			else
				$sql_query2 = "UPDATE invoice SET previous_status=status, status=5, is_paid=1 WHERE id IN(" . $invoice_ids . ")";	
			$query2 = mysqli_query($this->db, $sql_query2);
			
			$success = array('success' => "1", "error" => "","response"=> "Success");
			$this->response($this->json($success), 200);
		}

		public function viewBill()
		{
			$token_id = $this->_request['access_token'];
			$bill_id = $this->_request['bill_id'];
			// $admin_id = $this->checkTaxivaxiAdmin($token_id);

			$sql = mysqli_query($this->db, "SELECT * FROM bills WHERE id='$bill_id'");
			$result = mysqli_fetch_array($sql, MYSQL_ASSOC);

			$result2 = array();
			$sql2='';
			if($result['bill_type'] == '3')
				$sql2 = mysqli_query($this->db, "SELECT * FROM bus_invoice WHERE id IN(".(string)$result['invoice_ids'].")");
			else
				$sql2 = mysqli_query($this->db, "SELECT * FROM invoice WHERE id IN(".(string)$result['invoice_ids'].")");
			while($rlt = mysqli_fetch_array($sql2, MYSQL_ASSOC))	
			{
				array_push($result2, $rlt);		
			}

			$result_s = array('access_token' => $token_id,'Bill'=>$result, 'Invoices' => $result2);
			$success = array('success' => "1", "error" => "","response"=> $result_s);
			$this->response($this->json($success), 200);
		}
		//End Billing
		
		private function addAssessmentCode()
		{
			$token_id = $this->_request['access_token'];	
			$admin_id = $this->checkAdmin($token_id);
			$assessment_code = $this->_request['assessment_code'];
			$code_desc = $this->_request['code_desc'];
			
			try
			{
				$sql = mysqli_query("SELECT id from assessment_codes where assessment_code = '$assessment_code'");
				if(mysqli_num_rows($sql) > 0)
				{
					$response = array('success' => "0", 'error' => 'Assessment Code '.$assessment_code.' already exist');
					$this->response($this->json($response),200);
				}
				try
				{
					mysqli_query($this->db,"INSERT into assessment_codes (assessment_code,code_desc,admin_id) VALUES ('$assessment_code','$code_desc','$admin_id')");
					$id = mysqli_insert_id($this->db);
					if($id)
					{
						$result = array('access_token' => $token_id, 'message' => 'Assessment Code Added Successfully!');	
						$success = array('success' => "1", "error" => "","response"=>$result);
						$this->response($this->json($success), 200);
					}
					else
					{
						$response = array('success' => "0", 'error' => mysqli_error($this->db));
						$this->response($this->json($response),200);
					}
					
				}
				catch(Exception $e)
				{
					$response = array('success' => "0", 'error' => $e->getMessage());
					$this->response($this->json($response),200);
				}

			}
			catch (Exception $e) 
			{
				$response = array('success' => "0", 'error' => $e->getMessage());
				$this->response($this->json($response),200);
			}
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


		public function checkAdmin($token_id)
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
				$sql = mysqli_query($this->db, "select admin_id from admins_access_tokens where access_token = '$token_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$admin = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$admin_id = $admin['admin_id'];
					return $admin_id;
				}
				else
				{
					$response = array('success' => "0", 'error' => "Access Token Invalid");
					$this->response($this->json($response),200);
				}
			}
		}

		public function checkEmployee($token_id)
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
				$sql = mysqli_query($this->db, "select user_id from users_access_tokens where access_token = '$token_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$user = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$user_id = $user['user_id'];
					return $user_id;
				}
				else
				{
					$response = array('success' => "0", 'error' => "Access Token Invalid");
					$this->response($this->json($response),200);
				}
			}
		}

		public function checkAuthone($token_id)
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
				$sql = mysqli_query($this->db, "SELECT 
					subgroup_authenticater_id from subgroup_authenticater_acess_tokens
 					where access_token = '$token_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$user = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$user_id = $user['subgroup_authenticater_id'];
					return $user_id;
				}
				else
				{
					$response = array('success' => "0", 'error' => "Access Token Invalid");
					$this->response($this->json($response),200);
				}
			}
		}

		public function checkAuthtwo($token_id)
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
				$sql = mysqli_query($this->db, "select group_authenticater_id from group_authenticater_access_tokens
 where access_token = '$token_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$user = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$user_id = $user['group_authenticater_id'];
					return $user_id;
				}
				else
				{
					$response = array('success' => "0", 'error' => "Access Token Invalid");
					$this->response($this->json($response),200);
				}
			}
		}

		public function checkTaxivaxiAdmin($token_id)
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
				$sql = mysqli_query($this->db, "select taxivaxi_admin_id from taxivaxi_admins_access_tokens where access_token = '$token_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$user = mysqli_fetch_array($sql,MYSQL_ASSOC);
					$user_id = $user['taxivaxi_admin_id'];
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
			$pos = strrpos($output_path, "/api");
			$output_path = substr($output_path,0,$pos+1);
			if($image_name)
				$img_name = $image_name . "." . $image_ext;
			else
				$img_name = time() . "." . $image_ext;
			$output_path = "/home/taxivaxiin/public_html/business/images/".$folder_name."/".$img_name;
			//$output_path .= "images/".$folder_name."/".$img_name;
			$output = base64_to_image($base64_string,$output_path);
			return $img_name; //return image name to save in database
		}
		
		private function getAllCodes()
		{
			$token_id = $this->_request['access_token'];	
			//$employee_id = $this->checkEmployee($token_id);

			$admin_id = $this->_request['admin_id'];

			try
			{
				$sql = mysqli_query($this->db, "select * from assessment_codes where admin_id='$admin_id'");
				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						array_push($result, $rlt);
					}
					$result2 = array('access_token' => $token_id,'AssCodes'=>$result);
					$success = array('success' => "1", "error" => "","response"=> $result2);
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