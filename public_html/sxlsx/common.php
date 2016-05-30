<?php

error_reporting (E_ALL ^ E_NOTICE);
include("db_mysql.inc");

global $db;
$db = new DB_Sql();
$db->Database = 'isip_new';
$db->User     = 'root';
$db->Password = '';
$db->Host     = 'localhost';


global $db1;
$db1 = new DB_Sql();
$db1->Database = 'lts_fresh';
$db1->User     = 'root';
$db1->Password = '';
$db1->Host     = 'localhost';

global $db47;
$db47 = new DB_Sql();
$db47->Database = 'lts';
$db47->User     = 'root';
$db47->Password = '';
$db47->Host     = 'localhost';

global $db2;
$db2 = new DB_Sql();
$db2->Database = 'lts_fresh';
$db2->User     = 'root';
$db2->Password = '';
$db2->Host     = 'localhost';

//Site root path.



define("sBaseHref","http://www.rosemanassetprotectionservices.com/");







function tohtml($strValue)



{



  return htmlspecialchars($strValue);



}







function tourl($strValue)



{



  return urlencode($strValue);



}







function get_param($ParamName)



{



   //$_POST["$ParamName"];



   //$_GET["$ParamName"];







  $ParamValue = "";



  if(isset($_POST[$ParamName]))



    $ParamValue = $_POST[$ParamName];



  else if(isset($_GET[$ParamName]))



    $ParamValue = $_GET[$ParamName];







  return $ParamValue;



}











function get_session($parameter_name)



{



    return isset($_SESSION[$parameter_name]) ? $_SESSION[$parameter_name] : "";



}







function set_session($param_name, $param_value)



{



    $_SESSION[$param_name] = $param_value;



}



function is_number($string_value)



{



  if(is_numeric($string_value) || !strlen($string_value))



    return true;



  else



    return false;



}







function mid ($str, $start, $howManyCharsToRetrieve = 0)



{



  $start--;



  	if ($howManyCharsToRetrieve === 0)



    $howManyCharsToRetrieve = strlen ($str) - $start;







  return substr ($str, $start, $howManyCharsToRetrieve);



} 



function is_param($param_value)



{



  if($param_value)



    return 1;



  else



    return 0;



}







function tosql($value, $type="Text")



{



  if($value == "")



  {



    return "NULL";



  }



  else



  {



    if($type == "Number")



      return doubleval($value);



	elseif($type == "Date")



	{



	  $arrDate = explode("/",$value);	



      return "'" . $arrDate[2].'-'.$arrDate[0].'-'.$arrDate[1] . "'";  



	}



    else



    {



      if(get_magic_quotes_gpc() == 0)



      {



        $value = str_replace("'","''",$value);



        $value = str_replace("\\","\\\\",$value);



      }



      else



      {



        $value = str_replace("\\'","''",$value);



        $value = str_replace("\\\"","\"",$value);



      }



      return "'" . $value . "'";



     }



   }



}



function strip($value)



{



  if(get_magic_quotes_gpc() == 0)



    return $value;



  else



    return stripslashes($value);



}







function dlookup($Table, $fName, $sWhere)



{



  global $db2;



  $db2 = new DB_Sql();



  $db2->Database = DATABASE_NAME;



  $db2->User     = DATABASE_USER;



  $db2->Password = DATABASE_PASSWORD;



  $db2->Host     = DATABASE_HOST;



 



  $db2->query("SELECT " . $fName . " FROM " . $Table . " WHERE " . $sWhere);



  if($db2->next_record())



    return $db2->f(0);



  else



    return "";



}







function slookup($Table, $fName)



{



  global $db2;



  $db2 = new DB_Sql();



  $db2->Database = DATABASE_NAME;



  $db2->User     = DATABASE_USER;



  $db2->Password = DATABASE_PASSWORD;



  $db2->Host     = DATABASE_HOST;



  $db2->query("SELECT " . $fName . " FROM " . $Table );



  if($db2->next_record())



    return $db2->f(0);



  else



    return "";



}



function get_checkbox_value($sVal, $CheckedValue, $UnCheckedValue)



{



  if(!strlen($sVal))



    return tosql($UnCheckedValue);



  else



    return tosql($CheckedValue);



}







//- function returns options for HMTL control "<select>" as one string



function get_options($sql,$selected_value="")



{







  global $db2;  //-- connection special for list box



  $options_str="";



  $db2->query($sql);



  while ($db2->next_record($sql))



  {



    $id=$db2->f(0);



    $value=$db2->f(1);







    $selected="";



    if ($id == $selected_value)



    {



      $selected = "SELECTED";



    }



    $options_str.= "<option value='".$id."' ".$selected.">".$value."</option>";







  }



  return $options_str;



}



//--------------------------



function get_lov_options($lov_str,$is_search,$is_required,$selected_value)



{



  $options_str="";



  if (!$is_required && !$is_search)



    $options_str.="<option value=\"\"></option>";







  $LOV = split(";", $lov_str);







  if(sizeof($LOV)%2 != 0)



    $array_length = sizeof($LOV) - 1;



  else



    $array_length = sizeof($LOV);



  reset($LOV);







  for($i = 0; $i < $array_length; $i = $i + 2)



  {



    $id =  $LOV[$i];



    $value = $LOV[$i + 1];



    $selected="";



    if ($id == $selected_value)



      $selected = "SELECTED";







    $options_str.= "<option value='".$id."' ".$selected.">".$value."</option>";



  }



  return $options_str;



}







//-- function take $lov_str as parameter, parse it and return the result as array



function get_lov_values($lov_str)



{



  $options_str="";



  $LOV = split(";", $lov_str);







  if(sizeof($LOV)%2 != 0)



    $array_length = sizeof($LOV) - 1;



  else



    $array_length = sizeof($LOV);



  reset($LOV);







  $values = array();



  for($i = 0; $i < $array_length; $i = $i + 2)



  {



    $id =  $LOV[$i];



    $value = $LOV[$i + 1];



    $values[$id] = $value;



  }



  return $values;



}







//Job Type



$arrJobType = array("Select","Acting","Directing","Writing");







//Job Status



$arrJobStatus = array("Select","Full-time","Part-time");











/********************************************************************************



* Description:  It returns the Date of the first (or any required) day of the week, of the given date.



*********************************************************************************/



function fnGetWeekDate($intYear, $intMonth, $intDate)



{



	$strWeekDay = Date("l", Mktime('','','',$intMonth, $intDate, $intYear));



	switch($strWeekDay)



	{



		case "Monday":



			$intSubtract = 1;



			break;



		case "Tuesday":



			$intSubtract = 2;



			break;



		case "Wednesday":



			$intSubtract = 3;



			break;



		case "Thursday":



			$intSubtract = 4;



			break;



		case "Friday":



			$intSubtract = 5;



			break;



		case "Saturday":



			$intSubtract = 6;



			break;



		case "Sunday":



			$intSubtract = 0;



			break;



	}



	$intTimeStamp = $intSubtract*24*60*60;



	$intNewTimeStamp = Mktime('','','',$intMonth, $intDate, $intYear) - $intTimeStamp;



	$arrNewDate = getdate($intNewTimeStamp);



	$intNewYear = $arrNewDate[year];



	$intNewMonth = $arrNewDate[mon];



	$intNewDay = $arrNewDate[mday];



	$strNewDate = $intNewYear . "-" . $intNewMonth . "-" . $intNewDay;



	//print_r ($arrNewDate);







	return $strNewDate;



}







//Following function appends the zero decimals to the integer. e.g. converts 25 to 25.00



function fnAppendDecimals($strAmount)



{



	$intDotPos = strpos($strAmount, ".");



	if(!strlen($intDotPos))



		$strAmount = $strAmount . ".00";







	return $strAmount;



}







//Following function check for the user's validity.



//If the user is not logged in, it redirects them to the login page.



function check_security($sLevel)



{



	if(!session_is_registered("MEMBER_ID"))



	{



		if($sLevel == 1)



			header("Location: sign_in.php");



		else if($sLevel == 2)



			header("Location: ../sign_in.php");



		exit();



	}



}







//Check security function for admin interface.



function admin_check_security($intLevel)



{



	if(!session_is_registered("ADMIN_EMAIL"))



	{



		session_unset();



		if($intLevel == 0)



			header("Location: default.php");



		else



			header("Location: ../default.php");



		exit();



	}



}







//Check security function for affiliate interface.



function affiliate_check_security()



{



	if(!session_is_registered("AFFILIATE_EMAIL"))



	{



		session_unset();



		header("Location: default.php");



		exit();



		/*if($intLevel == 0)



			header("Location: default.php");



		else



			header("Location: ../default.php");



		exit();*/



	}



}







//Following function return the flag to show the selected (admin)menu opened/closed.



function fnShowMenuOpened($sVal)



{



	//Show the expanded left menu.



	if(strlen($sVal))



		$IsOpn = true;



	else



		$IsOpn = false;







	return $IsOpn;



}







//Function to get the file extension.



function fnGetFileExtension($strFileName)



{



	$strPos = strrpos($strFileName, ".");



	$strExtension = substr($strFileName, ($strPos+1), strlen($strFileName));







	return $strExtension;



}







//Function to get the file extension.



function GetFileExtension($strFileName)



{



	$arrExtension = explode(".", $strFileName);



	$intCount     = count($arrExtension);



	$strExtension = $arrExtension[$intCount-1];







	return $strExtension;



}







//Following function validates for email address.



function validateEmail($email) 



{ 



	return preg_match("^([_a-zA-Z0-9]+([\\._a-zA-Z0-9-]+)*)@([_a-zA-Z0-9-]{2,}(\\.[_a-zA-Z0-9-]{2,})*\\.[a-zA-Z]{2,3})$^", $email); 



} 







//Function to add the selected style for the links.



function fnApplyLinkStyle($strText, $strStartingTags, $strEndingTags)



{



	$arr1 = split("<A href", $strText);



	$a2 = $arr1[0];







	for($i=1;$i<count($arr1);$i++)



	{



		$a2 .= "<A href";



		$intFlg = 0;







		$intPos1 = strpos($arr1[$i], ">");



		$a2 .= substr($arr1[$i], 0, $intPos1) . ">" . $strStartingTags;







		$intPos2 = strpos($arr1[$i], "</A");



		$intPos3 = strpos($arr1[$i], ">");



		$strMiddleText = substr($arr1[$i], $intPos3+1, $intPos2-($intPos3+1));



		$a2 .= $strMiddleText . $strEndingTags . "</A>";







		$strRemainingText = substr($arr1[$i], $intPos2+4, strlen($arr1[$i]));







		$a2 .= $strRemainingText;



	}



	return $a2;



}


//curl
	function get_data($url) {
		  $ch = curl_init();
		  $timeout = 5;
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		  $data = curl_exec($ch);
		  curl_close($ch);
		  return $data;
		}




//Function to remove the starting and ending para. tags from the string provided.



function fnRemoveParaTag($strStyle)



{



	$strStyle = str_replace("<P>", "", $strStyle); //Removes the starting <P> tag.



	$strStyle = str_replace("</P>", "", $strStyle); //Removes the ending </P> tag.







	return $strStyle;



}















//Dynamic linker function.



function CreateURLFromQS($strLink)



{



//	global $Separator;







	$Separator = "DynaLink";







	$xDirList=explode("/",$strLink);







	for($i=0;$i<=count($xDirList) - 2;$i++)



	{



		if(!empty($DirList))



			$DirList .= "/" . $xDirList[$i];



		else



			$DirList .=$xDirList[$i];



	}







	$xQSList=explode("?",$xDirList[count($xDirList)-1]);







	$FileName=$xQSList[0];







	$xQSList[1]=str_replace("&","/",$xQSList[1]);



	$xQSList[1]=str_replace("=","/",$xQSList[1]);		







	$FinalString="/$DirList/$Separator/$xQSList[1]/$FileName";



	return $FinalString;



//	echo 	$FinalString;



}







//Function to replace the new line.



function replace_nl($strString)



{



	$newString = str_replace("



", "; ", $strString);







	return $newString;



}







//Function to replace the comma by semicolon.



function replace_comma($strString)



{



	$newString = str_replace(",", ";", $strString);







	return $newString;



}



function GetFileExtention($filename) { 



    $ext = explode(".", $filename); 



    return $ext[count($ext)-1]; 



} 



//Function to validate price.



function validate_price($floatValue)



{



	if (trim($floatValue)=="")



		return false;



	if (!is_numeric($floatValue))



        return false;



	if ($floatValue<0)



        return false;



	return true;



}















function TruncParagraph($paragraph, $limit){



    $word = explode(" ", $paragraph); //separate the words by spaces



    $total = count($word); //count how many words were separated



    



    if ($total>$limit){



    // total words was bigger than the limit



    //     specified



    for($x=0;$x<$limit;$x++){



    // loop $x from 0 for as long as it is l



    //     ess than $limit and add 1 to $x to make 



    //     the loop incontinuous



    $smpara = $smpara.$word[$x]." "; //small paragraph with the same amount of words as the number specified as $limit



    }



    $smpara = trim($smpara)."...(description truncated)"; // add ...(description truncated) to the end of the paragraph



    } else {



    // total words was smaller thanthe limit



    //     specified



    $smpara = implode(" ", $word); // put the paragraph back to its original state - with the spaces back in



    }



    return $smpara; // return the resultant paragraph after being put through all that hassle!!



}















//Function to validate number.



function validate_number($value)



{



	$len = strlen($value);



	for($i=0; $i<$len; $i++)



	{



		$c = substr($value, $i, 1);



		if ($c < '0' || $c > '9')



		{



			return false;



		}



	}        



	return true;



}







//Function to validate date



function validate_date($strDate)



{



	$arrDate = explode("/",$strDate);	



	return checkdate($arrDate[0], $arrDate[1], $arrDate[2]);



}







//Function to convert date in display format 



function display_date($strDate)



{



	$arrDate = explode("-",$strDate);	



	return $arrDate[2]."/".$arrDate[1]."/".$arrDate[0];



}







function is_leap_year($year) {



	if ((($year % 4) == 0 and ($year % 100)!=0) or ($year % 400)==0) {



       return 1;



    } else {



       return 0;



    }



}







function iso_week_days($yday, $wday) {



    return $yday - (($yday - $wday + 382) % 7) + 3;



}







function get_week_number($timestamp) {



    $d = getdate($timestamp);



    $days = iso_week_days($d["yday"], $d["wday"]);



    if ($days < 0) {



       $d["yday"] += 365 + is_leap_year(--$d["year"]);



       $days = iso_week_days($d["yday"], $d["wday"]);



    } else {



       $d["yday"] -= 365 + is_leap_year($d["year"]);



       $d2 = iso_week_days($d["yday"], $d["wday"]);



       if (0 <= $d2) {



          $days = $d2;



       }



    }



    return (int)($days / 7) + 1;



}



//Function for view Company name paging in admin section

/* paging */
function paging_bearing($pageName, $iCurrentPage, $intTotalPages, $isNextRecord , $strKeyword="",$strNewWay="", $strAlphabet="",$dropPage,$datefrom,$dateto)



{



	//echo '<form name="frmPaging" action="'.$pageName.'" method="post">';



	//-- scroller



	if($isNextRecord || $iCurrentPage != 1)



	{



		//-- current page



		echo "&nbsp &nbsp;&nbsp;<br />";



		$i=1;

		$prvpg=$iCurrentPage-1;
		$nxtpg=$iCurrentPage+1;
		if($iCurrentPage!=1) { echo "<a href=\"".$pageName."?page=".$prvpg."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&datefrom=".urlencode($datefrom)."&dateto=".urlencode($dateto)."\" class='paging'>< Previous</a>&nbsp;&nbsp;"; }
		
		
		
		if($iCurrentPage % 10==0) { 
			$xP=$iCurrentPage;
		} else {
			$xP = $iCurrentPage-($iCurrentPage % 10);
			//$xP = $xp-($xp-1);
			//echo "here";
		}
		if($xP==0) { $xP=1; }
		
		//echo "XP is ==> ".$xP."<br />";
	
		//exit;
		$remaingPages = $intTotalPages-$iCurrentPage;	
		//$xP10 = $xP+$remaingPages;
		
		
		
		for($xP,$iC;$xP<=$intTotalPages && $iC<10;$xP++,$iC++) {
		//echo "TP --> ".$intTotalPages."<br />";
		//echo "CP --> ".$currentPageRef."<br />";
			
		
			if($xP==$iCurrentPage) { 
				echo "<b>".$xP."</b>&nbsp;&nbsp;";
			}
			else {
				echo "<a href=\"".$pageName."?page=".$xP."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&datefrom=".urlencode($datefrom)."&dateto=".urlencode($dateto)."\" class='paging'>".$xP."</a>&nbsp;&nbsp;";
			}
			
		
		}
		//echo "iC".$iCurrentPage."TP".$intTotalPages;
		if($iCurrentPage < $intTotalPages) { echo "<a href=\"".$pageName."?page=".$nxtpg."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&datefrom=".urlencode($datefrom)."&dateto=".urlencode($dateto)."\" class='paging'> Next ></a>"; }
		



	 }



     echo '<input type="hidden" name="tid" value="'.urlencode($strKeyword).'">';



     echo '<input type="hidden" name="Way" value="'.urlencode($strNewWay).'">';



	  echo '<input type="hidden" name="main_group" value="'.urlencode($str_main_group).'">';



	  echo '<input type="hidden" name="Alphabet" value="'.urlencode($strAlphabet).'">';	



	  echo '<input type="hidden" name="dropPage" value="'.urlencode($dropPage).'">';
	  
	   echo '<input type="hidden" name="datefrom" value="'.urlencode($datefrom).'">';
	   
	   echo '<input type="hidden" name="dateto" value="'.urlencode($dateto).'">';



	 //echo '</form>';



}



/* paging */
function paging_view_company($pageName, $iCurrentPage, $intTotalPages, $isNextRecord , $strKeyword="",$strNewWay="", $strAlphabet="",$dropPage,$selecteddate)



{



	//echo '<form name="frmPaging" action="'.$pageName.'" method="post">';



	//-- scroller



	if($isNextRecord || $iCurrentPage != 1)



	{



		//-- current page



		echo "&nbsp &nbsp;&nbsp;<br />";



		$i=1;

		$prvpg=$iCurrentPage-1;
		$nxtpg=$iCurrentPage+1;
		if($iCurrentPage!=1) { echo "<a href=\"".$pageName."?page=".$prvpg."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&selecteddate=".urlencode($selecteddate)."\" class='paging'>< Previous</a>&nbsp;&nbsp;"; }
		
		
		
		if($iCurrentPage % 10==0) { 
			$xP=$iCurrentPage;
		} else {
			$xP = $iCurrentPage-($iCurrentPage % 10);
			//$xP = $xp-($xp-1);
			//echo "here";
		}
		if($xP==0) { $xP=1; }
		
		//echo "XP is ==> ".$xP."<br />";
	
		//exit;
		$remaingPages = $intTotalPages-$iCurrentPage;	
		//$xP10 = $xP+$remaingPages;
		
		
		
		for($xP,$iC;$xP<=$intTotalPages && $iC<10;$xP++,$iC++) {
		//echo "TP --> ".$intTotalPages."<br />";
		//echo "CP --> ".$currentPageRef."<br />";
			
		
			if($xP==$iCurrentPage) { 
				echo "<b>".$xP."</b>&nbsp;&nbsp;";
			}
			else {
				echo "<a href=\"".$pageName."?page=".$xP."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&selecteddate=".urlencode($selecteddate)."\" class='paging'>".$xP."</a>&nbsp;&nbsp;";
			}
			
		
		}
		//echo "iC".$iCurrentPage."TP".$intTotalPages;
		if($iCurrentPage < $intTotalPages) { echo "<a href=\"".$pageName."?page=".$nxtpg."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&selecteddate=".urlencode($selecteddate)."\" class='paging'> Next ></a>"; }
		
/*		while($i<=$intTotalPages)



		{



		   if($i != $iCurrentPage)



		   		echo "<a href=\"".$pageName."?page=".$i."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&selecteddate=".urlencode($selecteddate)."\" class='paging'>$i</a>&nbsp;";



		   else



		   		echo "<font class='paging'>$i&nbsp;</font>";



			$i++;



		}*/


		
		//-- previous page



		/*if($iCurrentPage == 1)



			echo "";



		else



		{



			$iPage = $iCurrentPage - 1;



			



			echo "&nbsp;&nbsp;"."<a href=\"".$pageName."?page=".$iPage."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&selecteddate=".urlencode($selecteddate)."\" class='paging'><</a>";



		}									



		if (!$isNextRecord || $iCurrentPage == $intTotalPages)



			echo "";



		else



		{



		    $iPage = $iCurrentPage + 1;



			



			echo "&nbsp;&nbsp;"."<a href=\"".$pageName."?page=$iPage&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&selecteddate=".urlencode($selecteddate)."\" class='paging'>></a>";



		}							*/	



		//-- next page



		



	 }



     echo '<input type="hidden" name="keyword" value="'.urlencode($strKeyword).'">';



     echo '<input type="hidden" name="Way" value="'.urlencode($strNewWay).'">';



	  echo '<input type="hidden" name="main_group" value="'.urlencode($str_main_group).'">';



	  echo '<input type="hidden" name="Alphabet" value="'.urlencode($strAlphabet).'">';	



	  echo '<input type="hidden" name="dropPage" value="'.urlencode($dropPage).'">';
	  
	   echo '<input type="hidden" name="selecteddate" value="'.urlencode($selecteddate).'">';



	 //echo '</form>';



}



/* paging */
function paging_view_search($pageName, $iCurrentPage, $intTotalPages, $isNextRecord , $strKeyword="",$strNewWay="", $strAlphabet="",$dropPage,$s1,$s2,$s3,$selecteddate)



{

	//echo '<form name="frmPaging" action="'.$pageName.'" method="post">';



	//-- scroller



	if($isNextRecord || $iCurrentPage != 1)



	{



		//-- current page



		echo "&nbsp &nbsp;&nbsp;<br />";



		$i=1;

		$prvpg=$iCurrentPage-1;
		$nxtpg=$iCurrentPage+1;
		if($iCurrentPage!=1) { echo "<a href=\"".$pageName."?page=".$prvpg."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&s1=".$s1."&s2=".$s2."&s3=".$s3."&selecteddate=".urlencode($selecteddate)."\" class='paging'>< Previous</a>&nbsp;&nbsp;"; }
		
		
		
		if($iCurrentPage % 10==0) { 
			$xP=$iCurrentPage;
		} else {
			$xP = $iCurrentPage-($iCurrentPage % 10);
			//$xP = $xp-($xp-1);
			//echo "here";
		}
		if($xP==0) { $xP=1; }
		
		//echo "XP is ==> ".$xP."<br />";
	
		//exit;
		$remaingPages = $intTotalPages-$iCurrentPage;	
		//$xP10 = $xP+$remaingPages;
		
		
		
		for($xP,$iC;$xP<=$intTotalPages && $iC<10;$xP++,$iC++) {
		//echo "TP --> ".$intTotalPages."<br />";
		//echo "CP --> ".$currentPageRef."<br />";
			
		
			if($xP==$iCurrentPage) { 
				echo "<b>".$xP."</b>&nbsp;&nbsp;";
			}
			else {
				echo "<a href=\"".$pageName."?page=".$xP."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&s1=".$s1."&s2=".$s2."&s3=".$s3."&selecteddate=".urlencode($selecteddate)."\" class='paging'>".$xP."</a>&nbsp;&nbsp;";
			}
			
		
		}
		//echo "iC".$iCurrentPage."TP".$intTotalPages;
		if($iCurrentPage < $intTotalPages) { echo "<a href=\"".$pageName."?page=".$nxtpg."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&s1=".$s1."&s2=".$s2."&s3=".$s3."&selecteddate=".urlencode($selecteddate)."\" class='paging'> Next ></a>"; }
		
/*		while($i<=$intTotalPages)



		{



		   if($i != $iCurrentPage)



		   		echo "<a href=\"".$pageName."?page=".$i."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&selecteddate=".urlencode($selecteddate)."\" class='paging'>$i</a>&nbsp;";



		   else



		   		echo "<font class='paging'>$i&nbsp;</font>";



			$i++;



		}*/


		
		//-- previous page



		/*if($iCurrentPage == 1)



			echo "";



		else



		{



			$iPage = $iCurrentPage - 1;



			



			echo "&nbsp;&nbsp;"."<a href=\"".$pageName."?page=".$iPage."&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&selecteddate=".urlencode($selecteddate)."\" class='paging'><</a>";



		}									



		if (!$isNextRecord || $iCurrentPage == $intTotalPages)



			echo "";



		else



		{



		    $iPage = $iCurrentPage + 1;



			



			echo "&nbsp;&nbsp;"."<a href=\"".$pageName."?page=$iPage&keyword=".urlencode($strKeyword)."&Way=".urlencode($strNewWay)."&Alphabet=".urlencode($strAlphabet)."&dropPage=".urlencode($dropPage)."&selecteddate=".urlencode($selecteddate)."\" class='paging'>></a>";



		}							*/	



		//-- next page



		



	 }



     echo '<input type="hidden" name="keyword" value="'.urlencode($strKeyword).'">';



     echo '<input type="hidden" name="Way" value="'.urlencode($strNewWay).'">';



	  echo '<input type="hidden" name="main_group" value="'.urlencode($str_main_group).'">';



	  echo '<input type="hidden" name="Alphabet" value="'.urlencode($strAlphabet).'">';	



	  echo '<input type="hidden" name="dropPage" value="'.urlencode($dropPage).'">';
	  
	   echo '<input type="hidden" name="s1" value="'.urlencode($s1).'">';
	    echo '<input type="hidden" name="s2" value="'.urlencode($s2).'">';
		 echo '<input type="hidden" name="s3" value="'.urlencode($s3).'">';
	  
	   echo '<input type="hidden" name="selecteddate" value="'.urlencode($selecteddate).'">';



	 //echo '</form>';



}



//Function for paging in Frontend section

function paging($pageName, $iCurrentPage, $intTotalPages, $isNextRecord, $strKeyword="",$strNewWay="", $strAlphabet="", $cid="", $cid1="")

{

	echo '<form name="frmPaging" action="'.$pageName.'" method="post">';

    

	//-- scroller

	if($isNextRecord || $iCurrentPage != 1)

	{

		//-- previous page

		if($iCurrentPage == 1)

			echo "";

		else

		{

			$iPage = $iCurrentPage - 1;

		    echo "<a href=\"".$pageName."?page=".$iPage."\"><img src='images/back_arrow.gif' border=0 alt='Previous' title='Previous'></a>";

		}									

		if (!$isNextRecord || $iCurrentPage == $intTotalPages)

			echo "";

		else

		{

		    $iPage = $iCurrentPage + 1;

			echo "&nbsp;&nbsp;"."<a href=\"".$pageName."?page=".$iPage."\"><img src='images/next_arrow.gif' border=0 alt='Next' title='Next'></a>";

		}								

		//-- current page

		echo "&nbsp;&nbsp;Page&nbsp;&nbsp;<input type='text' name='page' size='2' class='page_textfield' value='$iCurrentPage'>&nbsp;&nbsp;of&nbsp;$intTotalPages

		&nbsp;<input type='submit' name='submit' value='Go' class='button'>";

		//-- next page

		

	 }

     echo '<input type="hidden" name="keyword" value="'.urlencode($strKeyword).'">';

     echo '<input type="hidden" name="Way" value="'.urlencode($strNewWay).'">';	

	 echo '<input type="hidden" name="Alphabet" value="'.urlencode($strAlphabet).'">';	

	 echo '<input type="hidden" name="cid" value="'.urlencode($cid).'">';	

	 echo '<input type="hidden" name="cid1" value="'.urlencode($cid1).'">';	

	 echo '</form>';

}



 







// for showing checked uchecked images



function checkimage($arr,$num)



{



	if(!empty($arr))



	{



	  foreach($arr  as $temp)



	  {



		  if($temp==$num )



		  {



			 print "checked";



			 $cheched = 1;



		  }



		 



	  }



	   if( $cheched <>1)



		  {



			 print "unchecked";



			 return 0;



		  }



	}



}







//function for file deleting



function file_delete($strFileName, $strPath)



{



	$strFullFilePath = $strPath.$strFileName;



	if (!empty($strFileName) && file_exists($strFullFilePath))



	{



		//change mode to read write and executable



		//chmod($strFullFilePath, 0777);



		//delete file



		unlink($strFullFilePath);



	}



}



 



//Function to delete folder removing it's contents first



//Generated By: Amol Chidrewar.



//Generated On: 29th Jan, 2004.



//Used In:act_edit_affiliate.php,act_delete_affiliate.php







function delete($file) 



{ 



	chmod($file,0777); 



 	if (is_dir($file)) 



	{ 



  		$handle = opendir($file); 



  		while($filename = readdir($handle)) 



		{ 



   			if ($filename != "." && $filename != "..") 



			{ 



   				delete($file."/".$filename); 



   			} 



 	 	} 



  		closedir($handle); 



  		rmdir($file); 



 	}



	else 



	{ 



 	 unlink($file); 



 	} 



} 











function empty_cart()



{



	if(session_is_registered("MY_BASKET"))



		session_unregister("MY_BASKET");



}











function showSelected($option,$value)



{



	if($option==$value) 



	print "selected" ;



}







function gd2resize1($srcFile,$dstWidth,$dstHeight,$dstPath,$suffix)



{







$dstPath = $dstPath.$suffix.GetFileNameFromPath($srcFile);



$dstPath = GetFileName($dstPath);



$file_ext = GetFileExtension(GetFileNameFromPath($srcFile));



$srcType  = strtolower($file_ext) ;



$dstType  = $srcType ;







   if ($srcType == "jpg"||$srcType == "jpeg")



       $handle = @imagecreatefromjpeg($srcFile);



   else if ($srcType == "png")



       $handle = @imagecreatefrompng($srcFile);



   else if ($srcType == "gif")



       {$handle = @imagecreatefromgif($srcFile);



;}



   else



       return false;



	   



   if (!$handle)



       return false;



   $srcWidth  = @imagesx($handle);



   $srcHeight = @imagesy($handle);







  // $dstHeight = (int) (($dstWidth / $srcWidth) * ($dstHeight / $srcHeight));



  // $dstWidth = (int) (($dstHeight / $srcHeight) * ($dstWidth / $srcWidth));



   



   $newHandle = @imagecreatetruecolor($dstWidth, $dstHeight);



 	 if (!$newHandle)



         return false;











       if (!@imagecopyresampled($newHandle,$handle, 0,0,0,0, $dstWidth, $dstHeight, $srcWidth, $srcHeight))



      {



		   echo "sdfds";



		   return false;



      }



	   @imagedestroy($handle);







       if ($dstType == "png")



           @imagepng($newHandle, $dstPath.".png");



       else if ($dstType == "jpg")



	   {



		   



		   @imagejpeg($newHandle, $dstPath.".jpg", 90);



       }



	   else if ($dstType == "gif")



	   {



           @imagegif($newHandle,$dstPath.".gif");



		 



		}



	   else



           return false;



      



	  



	   @imagedestroy($newHandle);



   



	   return true;







   



} 







 function gd2resize($srcFile,$dstWidth,$dstPath,$suffix)



{







$dstPath = $dstPath.$suffix.GetFileNameFromPath($srcFile);



$dstPath = GetFileName($dstPath);



$file_ext = GetFileExtension(GetFileNameFromPath($srcFile));



$srcType  = strtolower($file_ext) ;



$dstType  = $srcType ;







   if ($srcType == "jpg"||$srcType == "jpeg")



       $handle = @imagecreatefromjpeg($srcFile);



   else if ($srcType == "png")



       $handle = @imagecreatefrompng($srcFile);



   else if ($srcType == "gif")



       {$handle = @imagecreatefromgif($srcFile);



;}



   else



       return false;



   if (!$handle)



       return false;



   $srcWidth  = @imagesx($handle);



   $srcHeight = @imagesy($handle);







   $dstHeight = (int) (($dstWidth / $srcWidth) * $srcHeight);



   $newHandle = @imagecreatetruecolor($dstWidth, $dstHeight);



 	 if (!$newHandle)



         return false;











       if (!@imagecopyresampled($newHandle,$handle, 0,0,0,0, $dstWidth, $dstHeight, $srcWidth, $srcHeight))



      {



		   echo "sdfds";



		   return false;



      }



	   @imagedestroy($handle);







       if ($dstType == "png")



           @imagepng($newHandle, $dstPath.".png");



       else if ($dstType == "jpg")



	   {



		   



		   @imagejpeg($newHandle, $dstPath.".jpg", 90);



       }



	   else if ($dstType == "gif")



	   {



           @imagegif($newHandle,$dstPath.".gif");



		 



		}



	   else



           return false;



      



	  



	   @imagedestroy($newHandle);



   



	   return true;







   



} 







function GetFileName($strFileName)



{



	$arrFileName = explode(".", $strFileName);



	$intCount     = count($arrFileName);



	$arrFileName[$intCount-1] = "";



	$fileName     = implode(".",$arrFileName);



	$fileName = substr($fileName,0,(strlen($fileName)-1));



	return $fileName;



}







function GetFileNameFromPath($strFilePath)



{



	$strFilePath = explode("/", $strFilePath);



	 



	$intCount     = count($strFilePath);



	return  $strFilePath[$intCount-1] ;



}


////////Function for paging










?>