<?php

include_once('/var/www/html/kumpeapps.com/api/apns/apns.php');

if($allowPost || $allowDelete){
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
	
	if($tool == 'unsubscribe'){
		unsubscribe_apns($appName,$userID,$masterID,$Section);
	}
	
	
	$json = array("status" => 1, "message" => "DELETE Successful");
	$statusCode = 200;
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>