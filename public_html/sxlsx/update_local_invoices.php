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


$sql = mysqli_query($db, "select * from invoice where tour_type = 'Local' and  TIME_FORMAT(pickup_time,'%H:%i:%s') < '07:00:00' OR TIME_FORMAT(drop_time,'%H:%i:%s') > '22:00:00'");

if(mysqli_num_rows($sql) > 0)
{
	while($rlt = mysqli_fetch_array($sql))
	{
		$invoice_id = $rlt['id'];
		$rate_id = $rlt['rate_id'];
		$driver_charge = $rlt['driver'];
		
		
		
		$sql2 = mysqli_query($db,"SELECT night_rate from rates where id = '$rate_id'");
		
		$rlt2 = mysqli_fetch_array($sql2);
		
		$night_rate = $rlt2['night_rate'];
		
		$add_driver = $night_rate - $driver_charge;
		
		$query = "update invoice set driver = '$night_rate', total_ex_tax = $add_driver + total_ex_tax, tax = 0.058 * total_ex_tax, total = tax+total_ex_tax, sub_total = total + taxivaxi_charge + taxivaxi_tax_charge + extras where id = '$invoice_id'";
		
		//echo $query; die;
		mysqli_query($db,"update invoice set driver = '$night_rate', total_ex_tax = $add_driver + total_ex_tax, tax = 0.058 * total_ex_tax, total = tax+total_ex_tax, sub_total = total + taxivaxi_charge + taxivaxi_tax_charge + extras where id = '$invoice_id'");
		
		$updated++;
	}
}
					

echo "<br>".$updated;

?>