<?php

include_once('/var/www/html/kumpeapps.com/api/apns/apns.php');

	function kkidPushNotification($userID,$section,$Title,$Body,$Badge,$Sound,$Action){

		$appName = 'com.kumpeapps.ios.KKid';

		if($userID == '0'){
			$useridquery = '';
		}else if (!is_numeric($userID)){
			$useridquery = " AND userID = getUserID('$userID')";
		}else{
			$useridquery = " AND userID = '$userID'";
		}
		
		$UserData = "
			SELECT 
    			userID,
    			masterID
			FROM
    			Apps_APNs.Subscriptions
			WHERE 1=1
				AND appName = '$appName'
    			AND sectionName = '$Section' $useridquery;
		";
		
		$UserQuery = mysqli_query($conn, $UserData) or die("Couldn't execute query. ". mysqli_error($conn)); 
		$Users = array();

		//Run script for each user
		while($User = mysqli_fetch_array($UserQuery))
    		$Users[] = $User;
		foreach($Users as $UserArray){ 
			send_apns($Title, $Body, $Badge, $Sound, $UserArray['userID'], $appName, $Action);
		}
	}
		
?>