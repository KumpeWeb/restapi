<?php

include_once('/var/www/html/kumpeapps.com/api/apns/apns.php');

if($allowPost){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	if($isSuperuser && isset($_REQUEST['masterID'])){
		$masterID = $_REQUEST['masterID'];
	}
	
	$updatedBy = $authenticatedUser;
	$userID = $_REQUEST['kidUserId'];
	$tool = $_REQUEST['tool'];
	$token = (isset($_REQUEST['token']) ? $_REQUEST['token'] : NULL);
	$appName = 'com.kumpeapps.ios.kkid';
	$deviceName = (isset($_REQUEST['deviceName']) ? $_REQUEST['deviceName'] : NULL);
	$Title = (isset($_REQUEST['title']) ? $_REQUEST['title'] : NULL);
	$Body = (isset($_REQUEST['body']) ? $_REQUEST['body'] : NULL);
	$Badge = (isset($_REQUEST['badge']) ? (int)$_REQUEST['badge'] : NULL);
	$Sound = (isset($_REQUEST['sound']) ? $_REQUEST['sound'] : "");
	$appName = 'com.kumpeapps.ios.KKid';
	$Action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL);
	$Section = (isset($_REQUEST['section']) ? $_REQUEST['section'] : NULL);
	
	if($tool == 'register' && $isSuperuser){
		register_apns($token,$appName,$userID,$deviceName,$masterID);
	}
	
	if($tool == 'subscribe'){
		subscribe_apns($appName,$userID,$masterID,$Section);
	}
	
	if($tool == 'unsubscribe'){
		unsubscribe_apns($appName,$userID,$masterID,$Section);
	}
	
	if($tool == 'send'){
	
		if($userID == '0'){
			$useridquery = '';
		}else{
			$useridquery = " AND userID = '$userID'";
		}
		
		if($isSuperuser && $masterID == '0'){
			$masteridquery = '';
		}else{
			$masteridquery = " AND masterID = '$masterID'";
		}
		
		$UserData = "
			SELECT 
    			userID,
    			masterID
			FROM
    			Apps_APNs.Subscriptions
			WHERE 1=1
				AND appName = '$appName'
    			AND sectionName = '$Section' $useridquery $masteridquery;
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
	
	$json = array("status" => 1, "message" => "POST Successful");
	$statusCode = 200;
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>