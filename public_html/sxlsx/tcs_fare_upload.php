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

$xlsx = new SimpleXLSX('Jeypore_Fares.xlsx');

echo '<table cellpadding="10">
<tr><td valign="top">';

// output worsheet 1

list($num_cols, $num_rows) = $xlsx->dimension();

//echo '<h1>Sheet 1</h1>';
echo '<table>';
$k=0;
$new_entries = 0;
$updated = 0;
foreach( $xlsx->rows() as $r ) 
{
	if($k<2)
	{
		$k++;
		continue;
	}
	$row_no = $k+1;
	
	$city_name = trim($r[0]);
	$taxi_type_id = trim($r[1]);
	$pkg_1_name = '8 Hrs/ 80 Km';
	$pkg_1_base = trim($r[2]);
	$pkg_1_hour = 8;
	$pkg_1_km = 80;
	$pkg_2_name = '12 Hrs/ 125 Km';
	$pkg_2_base = trim($r[3]);
	$pkg_2_hour = 12;
	$pkg_2_km = 125;
	$local_hour_rate = trim($r[4]);
	$local_km_rate =  trim($r[5]);
	$outstation_km = trim($r[6]);
	$outstation_km_rate = trim($r[7]);
	$night_rate = trim($r[8]);
	
	
	$admin_id = 1;	
	$state_id = 20; //Odisha
	
	$sql = mysqli_query($db, "SELECT id from cities where name = '$city_name' and state_id = '$state_id'");
	
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
		$city_id = $rlt['id'];
	}
	else
	{
		mysqli_query($db, "INSERT INTO cities(`name`, `state_id`, `city_code`, `std_code`, `latitude`, `longitude`, `created`, `modified`,`admin_id`) VALUES ('$city_name','$state_id','$city_name','','','',now(),now(),'$admin_id')");
		$city_id = mysqli_insert_id($db);
	}
	if($city_id)	
	{
		
		$sql2 = mysqli_query($db, "SELECT id from rates where city_id = '$city_id' and taxi_type_id = '$taxi_type_id' and tour_type = 1 and package_name = '$pkg_1_name' ");
		
		if(mysqli_num_rows($sql2) > 0)  
		{
			$rlt2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);
			$rate_id = $rlt2['id'];
			$sql3 = mysqli_query($db,"UPDATE rates set km_rate ='$local_km_rate',hour_rate ='$local_hour_rate', base_rate ='$pkg_1_base' where id = '$rate_id'");
			$updated++;
		}
		else
		{
			$sql2 = mysqli_query($db, "INSERT INTO rates (`package_name`, `city_id`, `taxi_type_id`, `tour_type`, `kms`, `hours`, `km_rate`, `hour_rate`,`base_rate`,`night_rate`,`admin_id`) VALUES ('$pkg_1_name','$city_id','$taxi_type_id','1','$pkg_1_km','$pkg_1_hour','$local_km_rate','$local_hour_rate','$pkg_1_base','$night_rate',1)");
			$id = mysqli_insert_id($db);
			if($id > 0)
				$new_entries++;
			else
			{
				echo "$row_no: Rate Not Created<br>";
				$k++;
				continue;
			}
		}
		
		//For Package 2 
		$sql2 = mysqli_query($db, "SELECT id from rates where city_id = '$city_id' and taxi_type_id = '$taxi_type_id' and tour_type = 1 and package_name = '$pkg_2_name' ");
		
		if(mysqli_num_rows($sql2) > 0)  
		{
			$rlt2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);
			$rate_id = $rlt2['id'];
			$sql3 = mysqli_query($db,"UPDATE rates set km_rate ='$local_km_rate',hour_rate ='$local_hour_rate', base_rate ='$pkg_2_base' where id = '$rate_id'");
			$updated++;
		}
		else
		{
			$sql2 = mysqli_query($db, "INSERT INTO rates (`package_name`, `city_id`, `taxi_type_id`, `tour_type`, `kms`, `hours`, `km_rate`, `hour_rate`,`base_rate`,`night_rate`,`admin_id`) VALUES ('$pkg_2_name','$city_id','$taxi_type_id','1','$pkg_2_km','$pkg_2_hour','$local_km_rate','$local_hour_rate','$pkg_2_base','$night_rate',1)");
			$id = mysqli_insert_id($db);
			if($id > 0)
				$new_entries++;
			else
			{
				echo "$row_no: Rate Not Created<br>";
				$k++;
				continue;
			}
		}
		
		//For Outstation 
		$pkg_name = $outstation_km." Km";
		$outstation_base_rate = $outstation_km * $outstation_km_rate;
		
		$sql2 = mysqli_query($db, "SELECT id from rates where city_id = '$city_id' and taxi_type_id = '$taxi_type_id' and tour_type = 2 and package_name = '$pkg_name' ");
		
		if(mysqli_num_rows($sql2) > 0)  
		{
			$rlt2 = mysqli_fetch_array($sql2,MYSQL_ASSOC);
			$rate_id = $rlt2['id'];
			$sql3 = mysqli_query($db,"UPDATE rates set kms = '$outstation_km', km_rate ='$outstation_km_rate',hour_rate ='0', base_rate ='$outstation_base_rate' where id = '$rate_id'");
			$updated++;
		}
		else
		{
			$sql2 = mysqli_query($db, "INSERT INTO rates (`package_name`, `city_id`, `taxi_type_id`, `tour_type`, `kms`, `hours`, `km_rate`, `hour_rate`,`base_rate`,`night_rate`,`admin_id`) VALUES ('$pkg_name','$city_id','$taxi_type_id','2','$outstation_km','0','$outstation_km_rate','0','$outstation_base_rate','$night_rate',1)");
			$id = mysqli_insert_id($db);
			if($id > 0)
				$new_entries++;
			else
			{
				echo "$row_no: Rate Not Created<br>";
				$k++;
				continue;
			}
		}
	}
	else
	{
		
		echo "$row_no: City does not exist<br>";
		$k++;
		continue;
	}
	
	$k++;
}
echo '</table>';

echo '</td><td valign="top">';


echo '</td></tr>';
echo "<tr><td>New Entries: $new_entries</td></tr><tr><td>Updated: $updated</td></tr></table>";

?>