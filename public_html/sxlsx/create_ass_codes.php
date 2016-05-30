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

$xlsx = new SimpleXLSX('ass_codes.xlsx');

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
	
	$assessment_code = trim($r[0]);
	$code_desc = trim($r[1]);
	

	$admin_id = 1;	

	
	$sql = mysqli_query($db, "SELECT id from assessment_codes where assessment_code = '$assessment_code'");
	
	if(mysqli_num_rows($sql) > 0)  
	{
		
	}
	else
	{
		
		$sql2 = mysqli_query($db, "INSERT INTO `assessment_codes`(`admin_id`, `assessment_code`, `code_desc`, `code_date`, `valid_through`) VALUES ('$admin_id','$assessment_code','$code_desc',now(),now())");
		$id = mysqli_insert_id($db);
		if($id > 0)
			$new_entries++;
		else
		{
			echo "$row_no: Code Not Created<br>";
			$k++;
			continue;
		}
	}
	
	$k++;
}
echo '</table>';

echo '</td><td valign="top">';


echo '</td></tr>';
echo "<tr><td>New Entries: $new_entries</td></tr><tr><td>Updated: $updated</td></tr></table>";

?>