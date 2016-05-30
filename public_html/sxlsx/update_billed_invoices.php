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

$xlsx = new SimpleXLSX('Extras_Update.xlsx');

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
	$rev_pickup_time = trim($r[1]);
	$rev_drop_time = trim($r[2]);
	$rev_start_km = trim($r[3]);
	$rev_end_km = trim($r[4]);
	$rev_extra = trim($r[5]);
	$rev_taxi_type = trim($r[6]);
	
	$is_night_rate = 0;
	
	$admin_id = 1;
	//For drop date	
	
	if($rev_pickup_time)
	{
		$h = floor($rev_pickup_time * 24);
		$m = round(((($rev_pickup_time * 24) - $h) * 60),0);
		if($m == 60)
		{
			$h = $h+1;
			$m = 0;
		}
		
		/*if($h < 7 || $h >= 22)
			$is_night_rate = 1;*/
		if (!$m) 
			$m = "00";
		else
			$m = (string)$m;
			
		if(strlen($h) == 1)
			$h = "0".$h;
		if(strlen($m) == 1)
			$m = "0".$m;
		$rev_pickup_time = $h.':'.$m;
		mysqli_query($db,"UPDATE invoice set pickup_time = '$rev_pickup_time' where booking_id = '$booking_id'");
	}
	if($rev_drop_time)
	{
		$h = floor($rev_drop_time * 24);
		$m = round(((($rev_drop_time * 24) - $h) * 60),0);
		if($m == 60)
		{
			$h = $h+1;
			$m = 0;
		}
		
		/*if($h < 7 || $h >= 22)
			$is_night_rate = 1;*/
		if (!$m) 
			$m = "00";
		else
			$m = (string)$m;
			
		if(strlen($h) == 1)
			$h = "0".$h;
		if(strlen($m) == 1)
			$m = "0".$m;
		$rev_drop_time = $h.':'.$m;
		mysqli_query($db,"UPDATE invoice set drop_time = '$rev_drop_time' where booking_id = '$booking_id'");
	}
	
	if($rev_extra)
	{
		mysqli_query($db,"UPDATE invoice set extras = '$rev_extra' where booking_id = '$booking_id'");
	}
	
	if($rev_taxi_type)
	{
		$sql2 = mysqli_query($db,"SELECT b.city_id,r.package_name from bookings `b` inner join rates `r` on b.rate_id = r.id where b.id = '$booking_id'");
		$rlt2 = mysqli_fetch_array($sql2);
		$city_id = $rlt2['city_id'];
		$package_name = $rlt2['package_name'];
		
		$sql2 = mysqli_query($db,"SELECT * from rates where city_id = '$city_id' and package_name = '$package_name' and taxi_type_id = '$rev_taxi_type'");
		$rlt2 = mysqli_fetch_array($sql2);
		$rate_id = $rlt2['id'];
		
		//echo "$city_id - $package_name - $rev_taxi_type - $rate_id<br>"; continue;
		$kms = $rlt2['kms'];
		$hours = $rlt2['hours'];
		$km_rate = $rlt2['km_rate'];
		$hour_rate = $rlt2['hour_rate'];
		$base_rate = $rlt2['base_rate'];
		$night_rate = $rlt2['night_rate'];
		
		mysqli_query($db,"UPDATE bookings set rate_id = '$rate_id', taxi_type_id = '$rev_taxi_type' where id = '$booking_id'");
		mysqli_query($db,"UPDATE invoice set rate_id = '$rate_id', rate_name = '$package_name', allowed_hrs = '$hours',hour_rate = '$hour_rate',km_rate = '$km_rate',allowed_kms = '$kms',base_rate = '$base_rate',driver = '$night_rate' where booking_id = '$booking_id'");
	}
	//echo "$booking_id - $rev_pickup_time - $rev_drop_time<br>";
	
	$sql = mysqli_query($db,"SELECT i.id, i.booking_id, i.pickup_date, i.pickup_time, i.drop_date, i.drop_time, i.start_km, i.end_km, i.allowed_hrs, i.hour_rate, i.allowed_kms,i.km_rate, i.extra_kms_charge, i.extras, i.base_rate, i.sub_total, i.bill_id, r.night_rate from invoice `i` inner join rates `r` on r.id = i.rate_id where booking_id = '$booking_id'");
	
	if(mysqli_num_rows($sql) > 0)
	{
		
		$rlt = mysqli_fetch_array($sql);
		$pickup_datetime = $rlt['pickup_date']." ".$rlt['pickup_time'];
		$drop_datetime = $rlt['drop_date']." ".$rlt['drop_time'];
		
		$pt = strtotime($pickup_datetime);
		$dt = strtotime($drop_datetime);
		
		if(date('H',$pt) < 7 || date('H',$pt) > 22 || (date('H',$pt) == 22 && date('i',$pt) > 0))
			$is_night_rate = 1;
		if(date('H',$dt) < 7 || date('H',$dt) > 22 || (date('H',$dt) == 22 && date('i',$dt) > 0))
			$is_night_rate = 1;
			
		echo $rlt['pickup_time']. " - " . $rlt['drop_time'] . " - ". $is_night_rate."<br>";
		
		$hourdiff = ceil((strtotime($drop_datetime) - strtotime($pickup_datetime))/3600);
		if($hourdiff < 0)
		{
			echo $row_no." - "."Please check pickup datetime and drop datetime<br>";
			$k++;
			continue;
		}
		
		$start_km = $rlt['start_km'];
		$end_km = $rlt['end_km'];
		
		$allowed_kms = $rlt['allowed_kms'];
		$kms_done = $end_km - $start_km;
			
		$extra_kms = $kms_done - $allowed_kms;
		if($extra_kms < 0)
			$extra_kms = 0;
			
		$allowed_hrs = $rlt['allowed_hrs'];
		$extra_hours = $hourdiff - $allowed_hrs;
		if((int)$extra_hours < 0)
			$extra_hours = 0;
		$rate_per_hr = $rlt['hour_rate'];
		$base_rate = $rlt['base_rate'];
		$rate_per_km = $rlt['km_rate'];
		 
		$extra_kms_charge = $extra_kms * $rate_per_km;
		$extras = $rlt['extras'];
		$extra_hours_charge = $extra_hours * $rate_per_hr;
		
		//$driver = 0;
		
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
		
		$prev_sub_total = $rlt['sub_total'];
		
		$total_diff = $sub_total - $prev_sub_total;
		
		$bill_id = $rlt['bill_id'];
		
		//Update invoice table
		
		mysqli_query($db,"UPDATE invoice set hours_done = '$hourdiff', extra_hours = '$extra_hours', extra_hours_charge = '$extra_hours_charge', extra_kms = '$extra_kms', extra_kms_charge = '$extra_kms_charge', driver = '$driver', total_ex_tax = '$total_ex_tax', tax = '$tax', total = '$total', sub_total = '$sub_total' where booking_id = '$booking_id'");
		
		if(!mysqli_error($db))
		{
			if($bill_id)
			{
				mysqli_query($db,"UPDATE bills set bill_amount = bill_amount + $total_diff where id = '$bill_id'");
			}
			$updated++;
		}
		else
		{
			echo $booking_id." - ".mysqli_error($db)."<br>";
			$k++;
			continue;
		}
		
	}
	else
	{
		echo $booking_id." - Invoice does not exist<br>";
		$k++;
		continue;
	}
	/*if($is_night_rate)
		echo "$row_no - $pickup_datetime - $drop_datetime - ".$night_rate."<br>";
	else
		echo "$row_no - $pickup_datetime - $drop_datetime - 0<br>";*/
		
	
	/*$hourdiff = ceil((strtotime($drop_datetime) - strtotime($pickup_datetime))/3600);
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
	
	//Check if invoice already exist
	$sql = mysqli_query($db,"select id from invoice where booking_id = '$booking_id'");
	if(mysqli_num_rows($sql) > 0)
	{
		echo $row_no." - "."Invoice already exist<br>";
		$k++;
		continue;
	}*/
	
	//Create new invoice
	/*$query = "SELECT r.id, r.package_name, r.kms,r.hours,r.km_rate,r.hour_rate,r.base_rate,r.night_rate
						from bookings `b` 
						left join rates `r` on r.id = b.rate_id
						where b.id = '$booking_id'";
	$sql = mysqli_query($db,$query);
	
	if(mysqli_num_rows($sql) > 0)
	{
		$rlt = mysqli_fetch_array($sql);
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
		
		//Insert in Invoice table
		mysqli_query($db,"INSERT INTO invoice (booking_id,tour_type,rate_id,rate_name,pickup_date,pickup_time,drop_date,drop_time,hours_done,allowed_hrs,extra_hours,hour_rate,extra_hours_charge,start_km,end_km,kms_done,allowed_kms,extra_kms,km_rate,extra_kms_charge,driver,extras,base_rate,total_ex_tax,tax_rate,tax,total,taxivaxi_rate,taxivaxi_charge,taxivaxi_tax_rate,taxivaxi_tax_charge,sub_total) VALUES ('$booking_id','Local','$rate_id','$package_name','$pickup_date','$pickup_time','$drop_date','$drop_time','$hourdiff','$allowed_hrs','$extra_hours','$rate_per_hr','$extra_hours_charge','$start_km','$end_km','$total_km','$allowed_km','$extra_kms','$rate_per_km','$extra_kms_charge','$driver','$extras','$base_rate','$total_ex_tax','$tax_rate','$tax','$total','$taxivaxi_rate','$taxivaxi_charge','$taxivaxi_tax_rate','$taxivaxi_tax_charge','$sub_total')");
		
		$invoice_id = mysqli_insert_id($db);
		
		if($invoice_id) //UPDATE bookings table
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
	else
	{
		echo $row_no." - "."Rate does not exist<br>";
		$k++;
		continue;
	}*/
	
	$k++;
}
echo '</table>';

echo '</td><td valign="top">';


echo '</td></tr>';
echo "<tr><td>New Entries: $new_entries</td></tr><tr><td>Updated: $updated</td></tr></table>";

?>