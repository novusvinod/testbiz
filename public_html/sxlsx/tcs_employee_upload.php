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

$xlsx = new SimpleXLSX('QCA_New.xlsx');

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
	
	$employee_id = trim($r[3]);
	$employee_name = trim($r[4]);
	$employee_email = trim($r[5]);
	$employee_contact = trim($r[6]);
	$spoc_email = trim($r[9]);
	$auth1_contact =  trim($r[14]);
	$auth1_cid = trim($r[11]);
	$auth1_email = trim($r[13]);
	$auth1_name = trim($r[12]);
	
	
	$admin_id = 0;	
	$spoc_id = 0;
	$group_id = 0;
	$subgroup_id = 0;
	
	$sql = mysqli_query($db, "SELECT id `spoc_id`, group_id, subgroup_id, admin_id from users where email = '$spoc_email'");
	
	if(mysqli_num_rows($sql) > 0)  
	{
		$rlt = mysqli_fetch_array($sql,MYSQL_ASSOC);
		$spoc_id = $rlt['spoc_id'];
		$group_id = $rlt['group_id'];
		$subgroup_id = $rlt['subgroup_id'];
		$admin_id = $rlt['admin_id'];
		
		$sql2 = mysqli_query($db, "SELECT id from people where people_email = '$employee_email'");
		
		if(mysqli_num_rows($sql2) > 0)  
		{
			$sql3 = mysqli_query($db,"UPDATE people set admin_id ='$admin_id',group_id='$group_id', subgroup_id='$subgroup_id', user_id='$spoc_id' where people_email='$people_email' ");
			echo "$row_no: Employee Updated<br>";
			$k++;
			$updated++;
		}
		else
		{
			$sql2 = mysqli_query($db, "INSERT INTO `people`(`admin_id`, `group_id`, `subgroup_id`, `user_id`, `people_cid`, `people_name`, `people_email`, `people_contact`) VALUES ('$admin_id','$group_id','$subgroup_id','$spoc_id','$employee_id','$employee_name','$employee_email','$employee_contact')");
			$id = mysqli_insert_id($db);
			if($id > 0)
				$new_entries++;
			else
			{
				echo "$row_no: Employee Not Created<br>";
				$k++;
				continue;
			}
		}
		
	}
	else
	{
		
		echo "$row_no: Spoc does not exist<br>";
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