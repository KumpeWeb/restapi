<?php

include_once('/var/www/html/kumpeapps.com/api/apns/apns.php');

	function kkidPushNotification($userID,$Section,$Title,$Body,$Badge,$Sound,$Action){
		global $sqlHost;
		global $sqlUser;
		global $sqlPass;
		$conn = mysqli_connect($sqlHost, $sqlUser, $sqlPass);
		mysqli_select_db($conn, "Apps_APNs");
		
		$appName = 'com.kumpeapps.ios.KKid';

		if($userID == '0'){
			$useridquery = '';
		}else if (!is_numeric($userID)){
			$getUserID = "SELECT getUserID($userID);";
			$userID = mysqli_query($conn, $getUserID) or die("Couldn't execute query. ". mysqli_error($conn)); 
			$useridquery = " AND userID = '$userID'";
		}else{
			$useridquery = " AND userID = '$userID'";
		}
		
		$UserData1 = "
			SELECT 
    			userID,
    			masterID
			FROM
    			Apps_APNs.Subscriptions
			WHERE 1=1
				AND appName = '$appName'
    			AND sectionName = '$Section' $useridquery;
		";
		$UserQuery1 = mysqli_query($conn, $UserData1) or die("Couldn't execute query. ". mysqli_error($conn)); 
		$Users = array();

		//Run script for each user
		while($User = mysqli_fetch_array($UserQuery1))
    		$Users[] = $User;
		foreach($Users as $UserArray){ 
			send_apns($Title, $Body, $Badge, $Sound, $UserArray['userID'], $appName, $Action);
		}
	}
		
?>