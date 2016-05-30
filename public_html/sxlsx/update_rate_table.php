<?php

include 'simplexlsx.class.php';
include('common.php');
include('db_live.php');

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

$xlsx = new SimpleXLSX('rates.xlsx');

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
	$city_name = $r[0];
	$taxi_type_id = $r[1];
	$tour_type = $r[2];
	$kms = $r[3];
	$hours = $r[4];
	$per_km_rate = $r[5];
	$per_hr_rate = $r[6];
	$base_rate = $r[7];
	$driver_allowance = $r[8];
	
	if($hours == 12)
	{
		echo "$row_no - Cannot add this package<br>";
		$k++; continue;
	}
	
	$operator_id = 0;
	$sql = mysqli_query($db, "SELECT id from cities where name = '$city_name'");
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
		$city_id = $rlt['id'];
		
		//echo $city_id; die;
	
		if($tour_type == 1)
		{
			if($taxi_type_id == 2)
			{
				mysqli_query($db, "INSERT INTO taxi_rates_local (operator_id,taxi_model_id,taxi_type_id,city_id,min_hour_per_booking,min_fare_per_booking,max_km_per_min_hour,additional_rate_per_hour,additional_km_per_hour,additional_rate_per_km) values ('3','4','4','$city_id','$hours','$base_rate','$kms','$per_hr_rate','10','$per_km_rate')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
				
				mysqli_query($db, "INSERT INTO taxi_rates_local (operator_id,taxi_model_id,taxi_type_id,city_id,min_hour_per_booking,min_fare_per_booking,max_km_per_min_hour,additional_rate_per_hour,additional_km_per_hour,additional_rate_per_km) values ('3','9','4','$city_id','$hours','$base_rate','$kms','$per_hr_rate','10','$per_km_rate')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
				
				mysqli_query($db, "INSERT INTO taxi_rates_local (operator_id,taxi_model_id,taxi_type_id,city_id,min_hour_per_booking,min_fare_per_booking,max_km_per_min_hour,additional_rate_per_hour,additional_km_per_hour,additional_rate_per_km) values ('3','135','4','$city_id','$hours','$base_rate','$kms','$per_hr_rate','10','$per_km_rate')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
			}
			else
			{
				mysqli_query($db, "INSERT INTO taxi_rates_local (operator_id,taxi_model_id,taxi_type_id,city_id,min_hour_per_booking,min_fare_per_booking,max_km_per_min_hour,additional_rate_per_hour,additional_km_per_hour,additional_rate_per_km) values ('3','65','5','$city_id','$hours','$base_rate','$kms','$per_hr_rate','10','$per_km_rate')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
				
				mysqli_query($db, "INSERT INTO taxi_rates_local (operator_id,taxi_model_id,taxi_type_id,city_id,min_hour_per_booking,min_fare_per_booking,max_km_per_min_hour,additional_rate_per_hour,additional_km_per_hour,additional_rate_per_km) values ('3','90','5','$city_id','$hours','$base_rate','$kms','$per_hr_rate','10','$per_km_rate')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
				
				mysqli_query($db, "INSERT INTO taxi_rates_local (operator_id,taxi_model_id,taxi_type_id,city_id,min_hour_per_booking,min_fare_per_booking,max_km_per_min_hour,additional_rate_per_hour,additional_km_per_hour,additional_rate_per_km) values ('3','134','5','$city_id','$hours','$base_rate','$kms','$per_hr_rate','10','$per_km_rate')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
			}
			
			$new_entries = $new_entries + 3;
		}
		else
		{
			if($taxi_type_id == 2)
			{
				mysqli_query($db, "INSERT INTO taxi_rates_outstation (operator_id,taxi_model_id,taxi_type_id,city_id,min_km_per_day,min_fare_per_day,per_km_charge,driver_allowance) values ('3','4','4','$city_id','$kms','$base_rate','$per_km_rate','$driver_allowance')");
			
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
				
				mysqli_query($db, "INSERT INTO taxi_rates_outstation (operator_id,taxi_model_id,taxi_type_id,city_id,min_km_per_day,min_fare_per_day,per_km_charge,driver_allowance) values ('3','9','4','$city_id','$kms','$base_rate','$per_km_rate','$driver_allowance')");
				
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
				
				mysqli_query($db, "INSERT INTO taxi_rates_outstation (operator_id,taxi_model_id,taxi_type_id,city_id,min_km_per_day,min_fare_per_day,per_km_charge,driver_allowance) values ('3','135','4','$city_id','$kms','$base_rate','$per_km_rate','$driver_allowance')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
			}
			else
			{
				mysqli_query($db, "INSERT INTO taxi_rates_outstation (operator_id,taxi_model_id,taxi_type_id,city_id,min_km_per_day,min_fare_per_day,per_km_charge,driver_allowance) values ('3','65','5','$city_id','$kms','$base_rate','$per_km_rate','$driver_allowance')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
				
				mysqli_query($db, "INSERT INTO taxi_rates_outstation (operator_id,taxi_model_id,taxi_type_id,city_id,min_km_per_day,min_fare_per_day,per_km_charge,driver_allowance) values ('3','90','5','$city_id','$kms','$base_rate','$per_km_rate','$driver_allowance')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
				
				mysqli_query($db, "INSERT INTO taxi_rates_outstation (operator_id,taxi_model_id,taxi_type_id,city_id,min_km_per_day,min_fare_per_day,per_km_charge,driver_allowance) values ('3','134','5','$city_id','$kms','$base_rate','$per_km_rate','$driver_allowance')");
				if(!mysqli_insert_id($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
				}
			}
			$new_entries = $new_entries+3;
			
		}	
		
	}
	else
	{
		echo "$row_no - City does not exist<br>";
		$k++; continue;
	}
	
	$k++;
	
}
echo '</table>';

echo '</td><td valign="top">';


echo '</td></tr>';
echo "<tr><td>New Entries: $new_entries</td></tr><tr><td>Updated: $updated</td></tr></table>";

?><?php

?><?php

?>