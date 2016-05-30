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

$xlsx = new SimpleXLSX('Self_Drive_Full_Data.xlsx');

echo '<table cellpadding="10">
<tr><td valign="top">';

// output worsheet 1

list($num_cols, $num_rows) = $xlsx->dimension();

//echo '<h1>Sheet 1</h1>';
echo '<table>';
$k=0;
$new_entries = 0;
foreach( $xlsx->rows() as $r ) 
{
	if($k<2)
	{
		$k++;
		continue;
	}
	$company_name = $r[0];
	$city = $r[1];
	$car_company = $r[2];
	$model_name = $r[3];
	$weekday_hourly_rate = $r[4];
	$weekend_hourly_rate = $r[5];
	$weekday_daily_rate = $r[6];
	$weekend_daily_rate = $r[7];
	$weekly_rate = $r[8];
	$monthly_rate = $r[9];
	$security_deposit = $r[11];
	
	if($weekday_daily_rate == "N/A")
		$weekday_daily_rate == NULL;
	if($weekday_hourly_rate == "N/A")
		$weekday_hourly_rate == NULL;
	if($weekend_daily_rate == "N/A")
		$weekend_daily_rate == NULL;
	if($weekend_hourly_rate == "N/A")
		$weekend_hourly_rate == NULL;
	if($weekly_rate == "N/A")
		$weekly_rate == NULL;
	if($monthly_rate == "N/A")
		$monthly_rate == NULL;
	if($security_deposit == "N/A")
		$security_deposit == NULL;
	/*$name = str_replace("'", '', $name);
	$city = str_replace("'", '', $city);
	$latitude = str_replace("'", '', $latitude);
	$latitude = str_replace("'", '', $longitude);*/
	/*$fname = preg_replace('/[^A-Za-z0-9\-\']/', '', $fname);
	$lname = preg_replace('/[^A-Za-z0-9\-\']/', '', $lname);*/
	
	$operator_id = 0;
	$sql = mysqli_query($db, "SELECT id from self_drive_operators where name = '$company_name'");
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
		$operator_id = $rlt['id'];
	}
	
	$city_id = 0;
	
	
	$sql = mysqli_query($db, "SELECT id from cities where name = '$city'");
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
		$city_id = $rlt['id'];
	}
	
	$car_model_id = 0;
	
	$sql = mysqli_query($db, "SELECT id from self_drive_car_models where car_company = '$car_company' and model_name = '$model_name'");
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
		$car_model_id = $rlt['id'];
	}
	
	if($city_id && $car_model_id && $operator_id)
	{
		mysqli_query($db, "INSERT INTO self_drive_rates (car_model_id, city_id, operator_id, weekday_hourly_rate, weekend_hourly_rate, weekday_daily_rate, weekend_daily_rate, weekly_rate, monthly_rate, security_deposit) values ($car_model_id, $city_id, $operator_id,$weekend_hourly_rate,$weekend_hourly_rate,$weekday_daily_rate,$weekend_daily_rate,$weekly_rate,$monthly_rate,$security_deposit)");
		$id = mysqli_insert_id($db);
		if($id > 0)
			$new_entries++;
			
	}
	/*echo '<tr>';
	echo '<td>'.$car_company.'</td>';
	for( $i=0; $i < $num_cols; $i++ )
	{
		echo '<td>'.( (!empty($r[$i])) ? $r[$i] : '&nbsp;' ).'</td>';
	}
		
	echo '</tr>';*/
}
echo '</table>';

echo '</td><td valign="top">';


echo '</td></tr>';
echo "<tr><td>$new_entries</td></tr></table>";

?><?php

?>