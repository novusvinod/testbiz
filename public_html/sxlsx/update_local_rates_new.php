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

$xlsx = new SimpleXLSX('taxi_rates_local_2104.xlsx');

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
	
	$taxi_model = trim($r[0]);
	$city_name = trim($r[1]);
	$base_rate = trim($r[3]);
	$per_hr_rate = trim($r[5]);
	$per_km_rate = trim($r[6]);
	
	//echo "$taxi_model - $city_name - $base_rate - $per_hr_rate - $per_km_rate<br>"; continue;
	
	$sql = mysqli_query($db, "SELECT id from cities where name = '$city_name'");
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC);
		$city_id = $rlt['id'];
		
		//echo $city_id; die;
		$sql2 = mysqli_query($db, "SELECT id,type_id from taxi_models where name = '$taxi_model'");
		if(mysqli_num_rows($sql2) > 0)  
		{
			$rlt2 = mysqli_fetch_array($sql2,MYSQLI_ASSOC);
			$taxi_model_id = $rlt2['id'];
			$taxi_type_id = $rlt2['type_id'];
			
			$sql3 = mysqli_query($db, "SELECT id from taxi_rates_local where taxi_model_id = '$taxi_model_id' and city_id = '$city_id' and operator_id = '3'");
			if(mysqli_num_rows($sql3) > 0)  
			{
				$query = "UPDATE taxi_rates_local SET min_fare_per_booking = '$base_rate',additional_rate_per_hour = '$per_hr_rate',additional_rate_per_km = '$per_km_rate' WHERE taxi_model_id = '$taxi_model_id' and city_id = '$city_id' and operator_id = '3'";
				//echo $query; die;
				mysqli_query($db, $query);
				
				if(mysqli_error($db))
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
					$k++;
					continue;
				}
				
				$updated++;
			}
			else
			{
				$query = "INSERT INTO taxi_rates_local (operator_id,taxi_model_id,taxi_type_id,city_id,min_hour_per_booking,min_fare_per_booking,max_km_per_min_hour,additional_rate_per_hour,additional_km_per_hour,additional_rate_per_km) values ('3','$taxi_model_id','$taxi_type_id','$city_id','8','$base_rate','80','$per_hr_rate','10','$per_km_rate')";
				mysqli_query($db, $query);
				
				$rate_id = mysqli_insert_id($db);
				
				if(!$rate_id)
				{
					echo "$row_no - ".mysqli_error($db)."<br>";
					$k++;
					continue;
				}
				
				$new_entries++;
			}
				
		}
		else
		{
			echo "$row_no - Taxi Model does not exist<br>";
			$k++;
			continue;
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