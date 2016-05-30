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

$xlsx = new SimpleXLSX('Stations.xlsx');

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
	$name = $r[0];
	$city = $r[1];
	$latitude = $r[2];
	$longitude = $r[3];
	$name = str_replace("'", '', $name);
	$city = str_replace("'", '', $city);
	$latitude = str_replace("'", '', $latitude);
	$latitude = str_replace("'", '', $longitude);
	/*$fname = preg_replace('/[^A-Za-z0-9\-\']/', '', $fname);
	$lname = preg_replace('/[^A-Za-z0-9\-\']/', '', $lname);*/
	
	$city_id = 0;
	
	
	$sql = mysqli_query($db, "SELECT id from cities where name = '$city'");
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
		$city_id = $rlt['id'];
	}
	
	$sql = mysqli_query($db, "SELECT id from stations where name = '$name' and city_id = '$city_id'");
	if(mysqli_num_rows($sql) > 0)  
	{
		echo "<td>$name for $city already exist.</td>";
		continue;
	}
	else
	{
		if($city_id)
		{
			mysqli_query($db, "INSERT INTO stations (name, city_id, latitude, longitude, created) values ('$name', '$city_id', '$latitude','$longitude', now())");
	$id = mysqli_insert_id($db);
		if($id > 0)
			$new_entries++;
		}
			
	}
	echo '<tr>';
	echo '<td>'.$city_id.'</td>';
	for( $i=0; $i < $num_cols; $i++ )
	{
		echo '<td>'.( (!empty($r[$i])) ? $r[$i] : '&nbsp;' ).'</td>';
	}
		
	echo '</tr>';
}
echo '</table>';

echo '</td><td valign="top">';


echo '</td></tr>';
echo "<tr><td>$new_entries</td></tr></table>";

?><?php

?>