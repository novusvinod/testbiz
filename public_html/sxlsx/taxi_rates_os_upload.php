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

$xlsx = new SimpleXLSX('taxi_rates_outstation.xlsx');

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
	if($k<1)
	{
		$k++;
		continue;
	}
	$row_no = $k+1;
	
	$operator_email = $r[0];
	$city = $r[3];
	//$car_company = $r[2];
	$model_name = $r[1];
	$car_model = explode(' ',$r[1]);
	$len = count($car_model);
	$car_model_name = $car_model[$len-1];
	$operator_name = $r[2];
	$min_km_per_day = $r[4];
	$min_fare_per_day = $r[5];
	$per_km_charge = $r[6];
	$driver_allowance = $r[7];
	
	$password = md5('taxi123');
	/*$name = str_replace("'", '', $name);
	$city = str_replace("'", '', $city);
	$latitude = str_replace("'", '', $latitude);
	$latitude = str_replace("'", '', $longitude);*/
	/*$fname = preg_replace('/[^A-Za-z0-9\-\']/', '', $fname);
	$lname = preg_replace('/[^A-Za-z0-9\-\']/', '', $lname);*/
	
	$operator_id = 0;
	$sql = mysqli_query($db, "SELECT id from taxi_operators where email = '$operator_email'");
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
		$operator_id = $rlt['id'];
	}
	else
	{
		mysqli_query($db, "INSERT INTO taxi_operators (`logo`,`name`,`contact_name`,`email`,`username`,`password`,`contact_no`,`created`) values ('','$operator_name','','$operator_email','$operator_email','$password','9881102875',now())");
		$operator_id = mysqli_insert_id($db);
		if(!$operator_id)
		{
			echo "$row_no: Operator not created<br>";
			$k++;
			continue;
		}
	}
	
	$city_id = 0;
	
	$sql = mysqli_query($db, "SELECT id from cities where name = '$city'");
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
		$city_id = $rlt['id'];
	}
	else
	{
		echo "$row_no: $city not found.<br>";
		$k++;
		continue;
	}
	
	$taxi_model_id = 0;
	
	$sql = mysqli_query($db, "SELECT id,type_id from taxi_models where name = '$car_model_name'");
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
		$taxi_model_id = $rlt['id'];
		$taxi_type_id = $rlt['type_id'];
	}
	else
	{
		echo "$row_no: $model_name not found.<br>";
		$k++;
		continue;
	}
	
	
	if($city_id && $taxi_model_id && $operator_id)
	{
		$reg_no = time();
		$sql = mysqli_query($db, "SELECT id from taxis where model_id = '$taxi_model_id' and $city_id = '$city_id' and operator_id = '$operator_id'");
		if(mysqli_num_rows($sql) == 0)  
		{
			mysqli_query($db,"INSERT INTO `taxis`(`reg_no`, `reg_year`, `plate_no`, `is_airconditioned`, `fuel_type`, `model_id`, `operator_id`, `garage_location`, `g_latitude`, `g_longitude`, `city_id`, `is_available`, `created`, `modified`) VALUES ('$reg_no','2015','$reg_no',1,2,$taxi_model_id,$operator_id,'','','',$city_id,1,now(),now())");
		}
		$sql = mysqli_query($db, "SELECT id from taxi_rates_outstation where taxi_model_id = '$taxi_model_id' and $city_id = '$city_id' and operator_id = '$operator_id'");
		if(mysqli_num_rows($sql) > 0)  
		{
			$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
			$rate_id = $rlt['id'];
			mysqli_query($db,"UPDATE taxi_rates_outstation set min_km_per_day = '$min_km_per_day', min_fare_per_day='$min_fare_per_day', per_km_charge = '$per_km_charge', driver_allowance = '$driver_allowance' where id = '$rate_id'");
			$updated++;
		}
		else
		{
			mysqli_query($db, "INSERT INTO taxi_rates_outstation (operator_id, taxi_model_id, taxi_type_id, city_id, min_km_per_day, min_fare_per_day, per_km_charge, driver_allowance) values ($operator_id, $taxi_model_id, $taxi_type_id,$city_id,'$min_km_per_day', '$min_fare_per_day' , '$per_km_charge' , '$driver_allowance')");
			$id = mysqli_insert_id($db);
			if($id > 0)
				$new_entries++;
		}
		
			
	}
	/*echo '<tr>';
	echo '<td>'.$car_company.'</td>';
	for( $i=0; $i < $num_cols; $i++ )
	{
		echo '<td>'.( (!empty($r[$i])) ? $r[$i] : '&nbsp;' ).'</td>';
	}
		
	echo '</tr>';*/
	$k++;
}
echo '</table>';

echo '</td><td valign="top">';


echo '</td></tr>';
echo "<tr><td>New Entries: $new_entries</td></tr><tr><td>Updated: $updated</td></tr></table>";

?><?php

?><?php

?><?php

?>