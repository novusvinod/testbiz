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



$k=0;
$new_entries = 0;
$updated = 0;


	$sql = mysqli_query($db, "SELECT id, duty_slip from bookings where is_invoice = 1 and tour_type = 1 and duty_slip != ''");
	if(mysqli_num_rows($sql) > 0)
	{
		while($rlt = mysqli_fetch_array($sql))
		{
			$booking_id = $rlt['id'];
			$duty_slip_url = $rlt['duty_slip'];
			
			$opts = array(
 					'http'=>array(
  					'header' => 'Connection: close'
 					)
				);
			$context = stream_context_create($opts);
			$content = @file_get_contents($duty_slip_url,false,$context);
			if($content === FALSE)
			{
				echo "$booking_id - $duty_slip_url<br>";
				$new_entries++;
			}
				
		}
	
		//var_dump($rlt);
		//echo "<br><br>";
	}
	

echo "<br>".$new_entries;

?>