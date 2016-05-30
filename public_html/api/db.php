<?php

	//Local
	const DB_SERVER = "localhost";
	const DB_USER = "tv_corporate";
	const DB_PASSWORD = "xJ4ZYeGcu9XcmfsC";
	const DB = "business_temp";
	
	function secure($str,$sqlHandle)
	{
		$secured = strip_tags($str);
		$secured = htmlspecialchars($secured);
		$secured = mysqli_real_escape_string($sqlHandle,$secured);
		
		return $secured;
	}
?>