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

$xlsx = new SimpleXLSX('Bus_Tickets_2604.xlsx');

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
	$total = trim($r[1]);

	$taxivaxi_rate = 100; //Management Fee Rate
	$taxivaxi_charge = 100; //Management Fee
	/*if($taxivaxi_charge > 100)
		$taxivaxi_charge = 100;*/
	$taxivaxi_tax_rate = 14.5; //Service Tax rate
	$taxivaxi_tax_charge = round(($taxivaxi_charge * $taxivaxi_tax_rate)/100,2);
	
	$sub_total = $total + $taxivaxi_charge + $taxivaxi_tax_charge;
	
	//Check if invoice already exist
	$sql = mysqli_query($db,"select id from bus_invoice where booking_id = '$booking_id'");
	if(mysqli_num_rows($sql) > 0)
	{
		/*$rlt = mysqli_fetch_array($sql);
		$id = $rlt['id'];
		mysqli_query($db,"UPDATE bus_invoice set total = '$total',taxivaxi_rate = '$taxivaxi_rate', taxivaxi_charge = '$taxivaxi_charge',taxivaxi_tax_rate = '$taxivaxi_tax_rate',taxivaxi_tax_charge = '$taxivaxi_tax_charge',sub_total = '$sub_total' WHERE id = '$id'");*/
		echo $row_no." - "."Invoice already exist<br>";
		$k++;
		//$updated++;
		continue;
	}
	
	
	//Insert in Invoice table
	mysqli_query($db,"INSERT INTO bus_invoice (booking_id,total,taxivaxi_rate,taxivaxi_charge,taxivaxi_tax_rate,taxivaxi_tax_charge,sub_total) VALUES ('$booking_id','$total','$taxivaxi_rate','$taxivaxi_charge','$taxivaxi_tax_rate','$taxivaxi_tax_charge','$sub_total')");
	
	$invoice_id = mysqli_insert_id($db);
	
	if($invoice_id) //UPDATE bookings table
	{
		$new_entries++;
		mysqli_query($db,"UPDATE bus_bookings set is_invoice = 1, invoice_id = '$invoice_id' WHERE id = '$booking_id'");
		mysqli_query($db,"UPDATE bus_invoice set ticket = concat('http://taxivaxi.in/business/images/bus_tickets/TVTCSBUS',$booking_id,'.png') WHERE booking_id = '$booking_id'");
	}
	else
	{
		echo $row_no." - ".mysqli_error($db)."<br>";
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