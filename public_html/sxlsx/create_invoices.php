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

$xlsx = new SimpleXLSX('Duty_Slips_0405_1.xlsx');

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
	
	$booking_id = trim($r[0]);
	$pickup_date = trim($r[2]);
	$pickup_time = trim($r[3]);
	$drop_date = trim($r[4]);
	$drop_time = trim($r[5]);
	$start_km = trim($r[6]);
	$end_km =  trim($r[7]);
	$extras = trim($r[8]);
	
	$is_night_rate = 0;
	
	$admin_id = 1;
	//For drop date	
	$ts1 = ($drop_date - 25569)*86400;
	$drop_date = date('Y-m-d', $ts1);
	$h = floor($drop_time * 24);
	$m = round(((($drop_time * 24) - $h) * 60),0);
	if($m == 60)
	{
		$h = $h+1;
		$m = 0;
	}
	
	if($h < 7 || $h >= 22)
		$is_night_rate = 1;
	if (!$m) 
		$m = "00";
	else
		$m = (string)$m;
		
	if(strlen($h) == 1)
		$h = "0".$h;
	if(strlen($m) == 1)
		$m = "0".$m;
	$drop_time = $h.':'.$m;
	
	//For pickup date
	$ts1 = ($pickup_date - 25569)*86400;
	$pickup_date = date('Y-m-d', $ts1);
	$h = floor($pickup_time * 24);
	$m = round(((($pickup_time * 24) - $h) * 60),0);
	if($m >= 59)
	{
		$h = $h+1;
		$m = 0;
	}
	
	if($h < 7 || $h >= 22)
		$is_night_rate = 1;
		
	if (!$m) 
		$m = "00";
	else
		$m = (string)$m;
	if(strlen($h) == 1)
		$h = "0".$h;
	if(strlen($m) == 1)
		$m = "0".$m;
	$pickup_time = $h.':'.$m;
	
	$pickup_datetime = $pickup_date . " " . $pickup_time;
	$drop_datetime = $drop_date." ".$drop_time;
	
	/*if($is_night_rate)
		echo "$row_no - $pickup_datetime - $drop_datetime - ".$night_rate."<br>";
	else
		echo "$row_no - $pickup_datetime - $drop_datetime - 0<br>";*/
		
	
	$hourdiff = ceil((strtotime($drop_datetime) - strtotime($pickup_datetime))/3600);
	if($hourdiff < 0)
	{
		echo $row_no." - "."Please check pickup datetime and drop datetime<br>";
		$k++;
		continue;
	}
	
	$total_km = $end_km - $start_km;
	if($total_km < 0)
	{
		echo $row_no." - "."Please check start km and end km<br>";
		$k++;
		continue;
	}
	
	
	
	//Create new invoice
	$query = "SELECT b.is_assign, r.id, r.tour_type, r.package_name, r.kms,r.hours,r.km_rate,r.hour_rate,r.base_rate,r.night_rate
						from bookings `b` 
						left join rates `r` on r.id = b.rate_id
						where b.id = '$booking_id'";
	$sql = mysqli_query($db,$query);
	
	if(mysqli_num_rows($sql) > 0)
	{
		$rlt = mysqli_fetch_array($sql);
		if($rlt['tour_type'] == '2')
		{
			echo $booking_id." - "."Outstation Tour<br>";
			$k++;
			continue;
		}
		
		if($rlt['is_assign'] == '0')
		{
			echo $booking_id." - "."Not Yet Assigned<br>";
			$k++;
			continue;
		}
		$allowed_km = $rlt['kms'];
		$allowed_hrs = $rlt['hours'];
		$rate_per_km = $rlt['km_rate'];
		$rate_per_hr = $rlt['hour_rate'];
		$base_rate = $rlt['base_rate'];
		$rate_id = $rlt['id'];
		$package_name = $rlt['package_name'];
		
		$extra_kms = $total_km - $allowed_km;
		if((int)$extra_kms < 0)
			$extra_kms = 0;
			
		$extra_hours = $hourdiff - $allowed_hrs;
		if((int)$extra_hours < 0)
			$extra_hours = 0;
			
		$extra_kms_charge = $extra_kms * $rate_per_km;
		$extra_hours_charge = $extra_hours * $rate_per_hr;
		
	
		if($is_night_rate)
			$driver = $rlt['night_rate'];
		else
			$driver = 0;
		
		$total_ex_tax = $base_rate + $extra_kms_charge + $extra_hours_charge + $driver;
		$tax_rate = 5.8;
		$tax = round(($total_ex_tax * $tax_rate)/100,2);
		$total = $total_ex_tax + $tax + $extras;
		
		if($total > 15000)
		{
			echo $booking_id." - "."Please check the data once again<br>";
			$k++;
			continue;
		}
		
		$taxivaxi_rate = 100; //Management Fee Rate
		$taxivaxi_charge = 100; //Management Fee
		$taxivaxi_tax_rate = 14.5; //Service Tax rate
		$taxivaxi_tax_charge = round(($taxivaxi_charge * $taxivaxi_tax_rate)/100,2);
		
		$sub_total = $total + $taxivaxi_charge + $taxivaxi_tax_charge;
		
		//Check if invoice already exist
		$sql = mysqli_query($db,"select id from invoice where booking_id = '$booking_id'");
		if(mysqli_num_rows($sql) > 0)
		{
			mysqli_query($db,"UPDATE invoice set rate_id = '$rate_id', rate_name = '$package_name', pickup_date = '$pickup_date',pickup_time = '$pickup_time', drop_date = '$drop_date', drop_time = '$drop_time', hours_done = '$hourdiff', allowed_hrs = '$allowed_hrs', extra_hours = '$extra_hours', extra_hours_charge = '$extra_hours_charge', start_km = '$start_km', end_km = '$end_km', kms_done = '$total_km', allowed_kms = '$allowed_km', extra_kms = '$extra_kms', extra_kms_charge = '$extra_kms_charge', driver = '$driver', extras = '$extras', base_rate = '$base_rate', total_ex_tax = '$total_ex_tax', tax_rate = '$tax_rate', tax = '$tax', total = '$total', taxivaxi_rate = '$taxivaxi_rate', taxivaxi_charge = '$taxivaxi_charge', taxivaxi_tax_rate = '$taxivaxi_tax_rate', taxivaxi_tax_charge = '$taxivaxi_tax_charge', sub_total = '$sub_total' where booking_id = '$booking_id'");
			
			$updated++;
		}
	
		else
		{
			//Insert in Invoice table
			mysqli_query($db,"INSERT INTO invoice (booking_id,tour_type,rate_id,rate_name,pickup_date,pickup_time,drop_date,drop_time,hours_done,allowed_hrs,extra_hours,hour_rate,extra_hours_charge,start_km,end_km,kms_done,allowed_kms,extra_kms,km_rate,extra_kms_charge,driver,extras,base_rate,total_ex_tax,tax_rate,tax,total,taxivaxi_rate,taxivaxi_charge,taxivaxi_tax_rate,taxivaxi_tax_charge,sub_total) VALUES ('$booking_id','Local','$rate_id','$package_name','$pickup_date','$pickup_time','$drop_date','$drop_time','$hourdiff','$allowed_hrs','$extra_hours','$rate_per_hr','$extra_hours_charge','$start_km','$end_km','$total_km','$allowed_km','$extra_kms','$rate_per_km','$extra_kms_charge','$driver','$extras','$base_rate','$total_ex_tax','$tax_rate','$tax','$total','$taxivaxi_rate','$taxivaxi_charge','$taxivaxi_tax_rate','$taxivaxi_tax_charge','$sub_total')");
			
			$invoice_id = mysqli_insert_id($db);if($invoice_id) //UPDATE bookings table
			{
				$new_entries++;
				mysqli_query($db,"UPDATE bookings set is_invoice = 1, invoice_id = '$invoice_id' WHERE id = '$booking_id'");
			}
			else
			{
				echo $row_no." - ".mysqli_error($db)."<br>";
				$k++;
				continue;
			}
			
		}
		
		
		
	}
	else
	{
		echo $row_no." - "."Rate does not exist<br>";
		$k++;
		continue;
	}
	
	$k++;
}
echo '</table>';

echo '</td><td valign="top">';


echo '</td></tr>';
echo "<tr><td>New Entries: $new_entries</td></tr><tr><td>Updated: $updated</td></tr></table>";

?>