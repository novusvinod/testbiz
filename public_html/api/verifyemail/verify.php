<?php
include '../db.php';

$db = @mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
$msg='';
if(!empty($_GET['code']) && isset($_GET['code']))
{
$code=mysqli_real_escape_string($db,$_GET['code']);
$sql=mysqli_query($db,"SELECT user_id FROM user_email_validation WHERE validation_code='$code' and valid_till > now() LIMIT 1");

if(mysqli_num_rows($sql) > 0)
{
$rlt = mysqli_fetch_array($c,MYSQL_ASSOC);
$user_id = $rlt['user_id'];
$count=mysqli_query($db,"SELECT * FROM user_details WHERE validation_code='$code' and is_email_validated = 0 and user_id = $user_id");

if(mysqli_num_rows($count) == 1)
{
mysqli_query($db,"UPDATE user_details SET is_email_validated=1 WHERE user_id = $user_id");
$msg="Your account is activated"; 
}
else
{
$msg ="Your account is already active, no need to activate again";
}

}
else
{
$msg ="Wrong activation code.";
}

}
?>
<?php echo $msg; echo "123"; ?>