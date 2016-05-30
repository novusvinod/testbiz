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

function getLatLong($address)
{
	$address = str_replace(" ", "+", $address);
	$opts = array(
 					'http'=>array(
  					'header' => 'Connection: close'
 					)
				);
	$context = stream_context_create($opts);
	$json = @file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false",false,$context);
	if($json === FALSE)
	{
		return 0;
	}
    $json = json_decode($json);

    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
    $latlong = array('lat' => $lat, 'long' => $long);
    return $latlong;
}

$xlsx = new SimpleXLSX('Cities.xlsx');

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
	$state_id = $r[1];
	$state_name = $r[2];
	$city_code = $r[3];
	
	
	$operator_id = 0;
	$sql = mysqli_query($db, "SELECT id from cities where name = '$city_name' and state_id = '$state_id'");
	if(mysqli_num_rows($sql) > 0)  
	{
		echo "$city_name - City already exist<br>";
		$k++; 
		continue;
	}
	else
	{
		$city_address = "$city_name, $state_name, India";
		$latlong = getLatLong($city_address);
		if(!$latlong)
		{
			echo "$city_name - Address not found<br>";
			$k++; 
			continue;
		}
		//echo "$city_name - ".$latlong['lat'].",".$latlong['long'];
		$lat = $latlong['lat'];
		$long = $latlong['long'];
		//var_dump($latlong);
		//echo "<br>";
		mysqli_query($db, "INSERT INTO cities (`name`,`state_id`,`city_code`,`std_code`,`latitude`,`longitude`,`created`,`modified`) values ('$city_name','$state_id','$city_code','','$lat','$long',now(),now())");
		$city_id = mysqli_insert_id($db);
		if(!$city_id)
		{
			echo "$city_name: City not created<br>";
			$k++; 
			continue;
		}
		else
			$new_entries++;
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