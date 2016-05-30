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
$ids= $_GET['ids'];
//echo $ids;
$id_arr = explode(',',$ids);

foreach($id_arr as $id)
{
	$opts = array(
 					'http'=>array(
  					'header' => 'Connection: close'
 					)
				);
	$context = stream_context_create($opts);
	$image_url = "http://taxivaxi.in/business/images/duty_slips/TVTCS$id.PNG";
		
	$content = @file_get_contents($image_url,false,$context);
	if($content === FALSE)
	{
		$image_url = "http://taxivaxi.in/business/images/duty_slips/TVTCS$id.png";
		$content = @file_get_contents($image_url,false,$context);
		if($content === FALSE)
		{
			$image_url = "http://taxivaxi.in/business/images/duty_slips/TVTCS$id.jpg";
			$content = @file_get_contents($image_url,false,$context);
			
			if($content === FALSE)
			{
				$image_url = "http://taxivaxi.in/business/images/duty_slips/$id.png";
				$content = @file_get_contents($image_url,false,$context);
				
				if($content === TRUE)
					echo "$booking_id - $image_url<br>";
			}
			else
				echo "$booking_id - $image_url<br>";
		}
		else
			echo "$booking_id - $image_url<br>";
		
	}
	else
	{
		echo "$booking_id - $image_url<br>";
	}

}	

echo "<br>".$new_entries;

?>