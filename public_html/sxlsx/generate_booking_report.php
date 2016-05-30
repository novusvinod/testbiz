<?php

include 'simplexlsx.class.php';
include('common.php');
include('db.php');

$db = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD);
mysqli_select_db($db,DB);

function dbConnect()
{
	$this->db = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD);
	if($this->db)
		mysqli_select_db($this->db,DB);
}
		
function dbClose()
{
	mysqli_close($this->db);
}

$from_date = $_GET['fd'];
$to_date = $_GET['td'];
$tour_type = $_GET['tour_type'];
$k=0;
$new_entries = 0;
$updated = 0;


					if($from_date && $to_date)
						$query = "SELECT b.id,
					concat('TVTCS',b.id) as booking_id, b.reference_no,b.ass_code,b.tour_type,c.name `city_name`, r.package_name, tt.name `taxi_type`,b.pickup_location,b.drop_location,b.booking_date,b.pickup_datetime,b.status_user,b.status_auth1,b.status_auth2,b.status_auth_taxivaxi,b.tv_accept_reject_date,b.approved_date,b.rejected_date,b.user_cancel_date,b.cancel_reason,b.assign_date,ga.name `approver2_name`,sga.name `approver1_name`,
					g.group_name,sg.subgroup_name,u.user_name,u.user_cid, is_invoice
					from user_bookings `ub` 
					left join bookings `b` on ub.booking_id = b.id 
					left join cities `c` on b.city_id = c.id
					left join rates `r` on b.rate_id = r.id
					left join taxi_types `tt` on tt.id = b.taxi_type_id
					left join users `u` on u.id = ub.user_id
					left join subgroups `sg` on b.subgroup_id = sg.id
					left join subgroup_authenticater `sga` on sga.subgroup_id = sg.id 
					left join groups `g` on b.group_id = g.id
					left join group_authenticater `ga` on ga.group_id = g.id
					where ub.admin_id = '1' and b.pickup_datetime between '$from_date' and '$to_date'
					order by b.id asc";
					
					else
						$query = "SELECT b.id,
					concat('TVTCS',b.id) as booking_id, b.reference_no,b.ass_code,b.tour_type,c.name `city_name`, r.package_name, tt.name `taxi_type`,b.pickup_location,b.drop_location,b.booking_date,b.pickup_datetime,b.status_user,b.status_auth1,b.status_auth2,b.status_auth_taxivaxi,b.tv_accept_reject_date,b.approved_date,b.rejected_date,b.user_cancel_date,b.cancel_reason,b.assign_date,ga.name `approver2_name`,sga.name `approver1_name`,b.is_invoice,
					g.group_name,sg.subgroup_name,u.user_name,u.user_cid
					from user_bookings `ub` 
					left join bookings `b` on ub.booking_id = b.id 
					left join cities `c` on b.city_id = c.id
					left join rates `r` on b.rate_id = r.id
					left join taxi_types `tt` on tt.id = b.taxi_type_id
					left join users `u` on u.id = ub.user_id
					left join subgroups `sg` on b.subgroup_id = sg.id
					left join subgroup_authenticater `sga` on sga.subgroup_id = sg.id 
					left join groups `g` on b.group_id = g.id
					left join group_authenticater `ga` on ga.group_id = g.id
					where ub.admin_id = '1' and b.tour_type = '$tour_type'
					order by b.pickup_datetime asc";
				
					
				$sql = mysqli_query($db, $query);

				if(mysqli_num_rows($sql) > 0)
				{
					$result = array();
					
					while($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC))
					{
						$bid = $rlt['id'];
						
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
						$rlt2['Reference No'] = $rlt['reference_no'];
						$rlt2['Assessment Code'] = $rlt['ass_code'];
						$rlt2['Group Name'] = $rlt['group_name'];
						$rlt2['Subgroup Name'] = $rlt['subgroup_name'];
						$rlt2['SPOC Name'] = $rlt['user_name'];
						
						$passengers = '';
						$sql2 = mysqli_query($db,"SELECT p.people_name from people `p` left join people_bookings `pb` on p.id = pb.people_id where pb.booking_id = '$bid'");
						
						if(mysqli_num_rows($sql2) > 0)
						{
							//echo "Here"; die;
							while($rlt4 = mysqli_fetch_array($sql2))
							{
								
								$passengers .= $rlt4['people_name'].", ";
							}
						}
						
						$rlt2['Passengers'] = $passengers;
						
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
						
						$rlt2['City'] = $rlt['city_name'];
						$rlt2['Package Name'] = $rlt['package_name'];
						$rlt2['Taxi Type'] = $rlt['taxi_type'];
						$rlt2['Pickup Location'] = $rlt['pickup_location'];
						$rlt2['Drop Location'] = $rlt['drop_location'];
						$rlt2['Booking Date'] = $booking_date;
						$rlt2['Booking Time'] = $booking_time;
						$rlt2['Pickup Date'] = $pickup_date;
						$rlt2['Pickup Time'] = $pickup_time;
						
						$rlt2['Is Invoice Created'] = $rlt['is_invoice'];
						
						//$rlt2['Approver1 Name'] = $rlt['approver1_name'];
						
						
						array_push($result, $rlt2);
					}
					$data = $result;
					
					if($from_date && $to_date)
						$filename = "Booking_".$from_date."_".$to_date.".csv";
					else
						$filename = "Bookings_".$tour_type.".csv";
            
		              header("Content-Disposition: attachment; filename=\"$filename\"");
					  header("Content-Type: text/csv");

					  $out = fopen("php://output", 'w');

					  $flag = false;
					 
					  foreach($data as $row) {
					    if(!$flag) {
					      // display field/column names as first row
					      fputcsv($out, array_keys($row), ',', '"');
					      $flag = true;
					    }
					    //array_walk($row, 'cleanData');
					    fputcsv($out, array_values($row), ',', '"');
					  }

					  fclose($out);
					  //return false;
					  exit;
					} 
					else
					{
						echo "No Result Found";
						exit;
					}
			

?>