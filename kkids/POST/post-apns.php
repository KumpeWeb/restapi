<?php

include_once('/var/www/html/kumpeapps.com/apns/apns.php');

if($allowPost){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	if($isSuperUser){
		$masterID = $_REQUEST['masterID'];
	}
	
	$updatedBy = $authenticatedUser;
	$userID = $_REQUEST['kidUserId'];
	$tool = $_REQUEST['tool'];
	$token = $_REQUEST['token'];
	$appName = 'com.kumpeapps.ios.kkid';
	$deviceName = $_REQUEST['deviceName'];
	
	if($tool == 'register'){
		register_apns($token,$appName,$userID,$deviceName,$masterID);
	}
	
	$json = array("status" => 1, "message" => "POST Successful");
	$statusCode = 200;
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>