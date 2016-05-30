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
$cids = $_GET['cids'];
//echo $cids; die;
$id_arr = explode(',',$ids);
$cid_arr = explode(',',$cids);
$pickup_datetimes = array('2016-04-16 05:00:00','2016-04-30 05:00:00');//,'2016-04-19 05:00:00','2016-04-22 05:00:00','2016-04-26 05:00:00','2016-04-27 05:00:00','2016-04-28 05:00:00','2016-04-29 05:00:00','2016-04-30 05:00:00');

/*$pickup_datetimes = array('2016-03-31 05:30:00','2016-04-02 05:30:00','2016-04-03 05:30:00','2016-04-04 05:30:00','2016-04-05 05:30:00','2016-04-06 05:30:00','2016-04-07 05:30:00','2016-04-09 05:30:00','2016-04-10 05:30:00','2016-04-11 05:30:00','2016-04-12 05:30:00','2016-04-16 05:30:00','2016-04-18 05:30:00','2016-04-19 05:30:00','2016-04-22 05:30:00','2016-04-26 05:30:00','2016-04-27 05:30:00','2016-04-28 05:30:00','2016-04-29 05:30:00','2016-04-30 05:30:00');*/

foreach($id_arr as $id)
{
	$sql = mysqli_query($db, "SELECT people_id from people_bookings where booking_id = '$id'");
	if(mysqli_num_rows($sql) > 0)
	{
		while($rlt = mysqli_fetch_array($sql))
		{
			$people_id = $rlt['people_id'];
			
			foreach($cid_arr as $cid)
			{
				$sql2 = mysqli_query($db, "SELECT people_id from people_bookings where booking_id = '$cid'");
				$rlt2 = mysqli_fetch_array($sql2);
				if($rlt2['people_id'] == $people_id)
				{
					continue;
				}
				else
				{
					mysqli_query($db,"INSERT INTO people_bookings (booking_id,people_id) VALUES ('$cid',$people_id) ");
				}
			}
		}
	
		//var_dump($rlt);
		//echo "<br><br>";
	}
	
}

echo "<br>".$new_entries;

?>