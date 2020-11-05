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
			$getUserID = "SELECT getUserID('$userID') as userID;";
			$userID1 = mysqli_query($conn, $getUserID) or die("Couldn't execute query. ". mysqli_error($conn)); 
			$userIDarray = mysqli_fetch_array($userID1);
			$userID = $userIDarray['userID'];
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
	
	function kkidPushNotificationAsync($userID,$Section,$Title,$Body,$Badge,$Sound,$Action) {
		//Create User
		$url = 'https://api.kumpeapps.com/apns/kkid_push.php';
		$nonce = NonceUtil::generate(NONCE_SECRET, 10);
		$fields = array(
    		'nonce' => $nonce,
    		'userID' => $userID,
    		'Section' => $Section,
    		'Title' => $Title,
    		'Body' => $Body,
    		'Badge' => $Badge,
    		'Sound' => $Sound,
    		'Action' => $Action,
		);
		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));

		//execute post
		$curlResponse = curl_exec($ch);
		$response1 = json_decode($curlResponse,true);
		//close connection
		curl_close($ch);
	}
		
?>