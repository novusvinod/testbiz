<?php

include 'simplexlsx.class.php';
include('common.php');
include('db.php');

define("TAXIVAXI_NUMBER", "7891022360");
define("TAXIVAXI_EMAIL", "corporate.oe@taxivaxi.com");

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


// output worsheet 1

//list($num_cols, $num_rows) = $xlsx->dimension();

//echo '<h1>Sheet 1</h1>';

$k=0;
$new_entries = 0;
$updated = 0;


$sql = mysqli_query($db, "SELECT id,city_id,night_rate,taxi_type_id from rates where tour_type = 2");

if(mysqli_num_rows($sql) > 0)
{
	while($rlt = mysqli_fetch_array($sql))
	{
		$night_rate = $rlt['night_rate'];
		$city_id = $rlt['city_id'];
		$taxi_type_id = $rlt['taxi_type_id'];
		
		mysqli_query($db,"UPDATE rates set night_rate = '$night_rate' where city_id = '$city_id' and taxi_type_id = '$taxi_type_id' and tour_type = 1");
		
		$updated++;
	}
}
					

echo "<br>".$updated;

?>