<?php

include_once('/var/www/html/kumpeapps.com/api/apns/apns.php');

if($allowPost){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	if($isSuperuser){
		$masterID = $_REQUEST['masterID'];
	}
	
	$updatedBy = $authenticatedUser;
	$userID = $_REQUEST['kidUserId'];
	$tool = $_REQUEST['tool'];
	$token = $_REQUEST['token'];
	$appName = 'com.kumpeapps.ios.kkid';
	$deviceName = $_REQUEST['deviceName'];
	$Title = $_REQUEST['title'];
	$Body = $_REQUEST['body'];
	$Badge = (is_numeric($_REQUEST['badge']) ? (int)$_REQUEST['badge'] : NULL);
	$Sound = $_REQUEST['sound'];
	$appName = 'com.kumpeapps.ios.kkid';
	$Action = $_REQUEST['action'];
	$Section = $_REQUEST['section'];
	
	if($tool == 'register' && $isSuperuser){
		register_apns($token,$appName,$userID,$deviceName,$masterID);
	}
	
	if($tool == 'send'){
	
		if($userID == '0'){
			$useridquery = '';
		}else{
			$useridquery = " AND userID = '$userID'";
		}
		
		if($isSuperUser && $masterID == '0'){
			$masteridquery = '';
		}else{
			$masteridquery = " AND masterID = '$masterID'"
		}
		
		$UserData = "
			SELECT 
    			userID,
    			masterID
			FROM
    			Apps_APNs.Subscriptions
			WHERE 1=1
				AND appName = $appName
    			AND sectionName = $Section $useridquery $masteridquery;
		";  
		$UserQuery = mysqli_query($conn, $UserData) or die("Couldn't execute query. ". mysqli_error($connection)); 
		$Users = array();

		//Run script for each user
		while($User = mysqli_fetch_array($UserQuery))
    		$Users[] = $User;
		foreach($Users as $UserArray){ 
			send_apns($Title, $Body, $Badge, $Sound, $UserArray['userID'], $appName, $Action);
		}
		
	}
	
	$json = array("status" => 1, "message" => "POST Successful");
	$statusCode = 200;
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>