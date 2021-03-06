<?php
/*
Simple iOS push notification with auth key
*/

include_once('/var/www/html/kumpeapps.com/api/apns/inc_jwt_helper.php');
	
if(isset($_REQUEST['isSandbox']) && $_REQUEST['isSandbox'] == 1){
	$isSandbox = true;
}else{
	$isSandbox = false;
}

if(ISSET($_REQUEST['isBackgroundNotification']) && $_REQUEST['isBackgroundNotification'] == 1){
		$isBackgroundNotification = true;
	}else{
		$isBackgroundNotification = false;
	}
	
function register_apns($token,$appName,$userID,$deviceName,$masterID){
	global $conn;
	$sql = "
		INSERT INTO `Apps_APNs`.`Tokens` (token, appName, userID, deviceName, masterID, lastUpdated, markForDeletion)
			VALUES('$token','$appName','$userID','$deviceName','$masterID',now(),0) 
			ON DUPLICATE KEY UPDATE userID='$userID', deviceName='$deviceName', masterID='$masterID', lastUpdated=now(), markForDeletion=0
	";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}

function delete_apns($token) {
	global $conn;
	$sql = "
		DELETE FROM `Apps_APNs`.`Tokens` 
		WHERE 1=1
			AND token = '$token'
	";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}

function subscribe_apns($appName,$userID,$masterID,$sectionName){
	global $conn;
	$sql = "
		INSERT INTO `Apps_APNs`.`Subscriptions` (appName, userID, masterID, sectionName)
			VALUES('$appName','$userID','$masterID','$sectionName')
	";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}

function unsubscribe_apns($appName,$userID,$masterID,$sectionName){
	global $conn;
	$sql = "
		DELETE FROM `Apps_APNs`.`Subscriptions` 
		WHERE 1=1
			AND appName = '$appName'
			AND userID = '$userID'
			AND masterID = '$masterID'
			AND sectionName = '$sectionName'
	";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}

function send_apns($Title, $Body, $Badge, $Sound, $userID, $appName, $Action){
	global $conn;
	//Get List of users taht have access to Behavior Chart 
	$TokenData = "CALL `Apps_APNs`.getTokens($userID,'$appName');";  
	$TokenQuery = mysqli_query($conn, $TokenData) or die("Couldn't execute query. ". mysqli_error($conn)); 
	$Tokens = array();

	//Run script for each user
	while($Token = mysqli_fetch_array($TokenQuery))
    	$Tokens[] = $Token;
	foreach($Tokens as $TokenArray){ 
		//Set dbEXT for User
    	$UserToken = $TokenArray['token'];
		build_push_to_apns($Title, $Body, $Badge, $Sound, $UserToken, $appName, $Action);
	}

}
	
function build_push_to_apns($Title, $Body, $Badge, $Sound, $Token, $AppID, $Action) {
	
	$action = $Action;

	global $isBackgroundNotification;
		
	$authKey = "/var/www/html/kumpeapps.com/api/apns/authkeys/AuthKey_A9Y45QVNXM.p8";
  	$arParam['teamId'] = '2T42Z3DM34';// Get it from Apple Developer's page
 	$arParam['authKeyId'] = 'A9Y45QVNXM';
  	$arParam['apns-topic'] = $AppID;
	$arClaim = ['iss'=>$arParam['teamId'], 'iat'=>time()];
	$arParam['p_key'] = file_get_contents($authKey);
	$arParam['header_jwt'] = JWT::encode($arClaim, $arParam['p_key'], $arParam['authKeyId'], 'RS256');
	$arSendData = array();
	$arSendData['aps']['action'] = sprintf($action);

	if($Title != "" && $Title != NULL){
		$arSendData['aps']['alert']['title'] = sprintf($Title); // Notification title
		$arSendData['aps']['alert']['body'] = sprintf($Body); // body text
	}else{
		$isBackgroundNotification = true;
	}
	
		$arSendData['aps']['sound'] = sprintf($Sound); // sound

		$arSendData['aps']['badge'] = $Badge; // badge #


	if($isBackgroundNotification){
		$arSendData['aps']['content-available'] = 1;
	}
	
	if(ISSET($_REQUEST['category'])){
		$arSendData['aps']['category'] = sprintf($_REQUEST['category']);
	}

	// Sending a request to APNS
	$stat = push_to_apns($arParam, $ar_msg, $arSendData, $Token);
	if($stat == FALSE){
    // err handling
		//exit();
	}

	//exit();
}
// ***********************************************************************************
function push_to_apns($arParam, &$ar_msg, $arSendData, $Token){

	global $isSandbox;

	$sendDataJson = json_encode($arSendData);
  
	$endPoint = 'https://api.push.apple.com/3/device'; // https://api.[sandbox.]push.apple.com/3/device
	
	if($isSandbox){
		$endPoint = "https://api.sandbox.push.apple.com/3/device";
	}

	//　Preparing request header for APNS
	$ar_request_head[] = sprintf("content-type: application/json");
	$ar_request_head[] = sprintf("authorization: bearer %s", $arParam['header_jwt']);
	$ar_request_head[] = sprintf("apns-topic: %s", $arParam['apns-topic']);

	$dev_token = $Token;  // Device token

	$url = sprintf("%s/%s", $endPoint, $dev_token);

	$ch = curl_init($url);
	
	$certificate_location = '/etc/letsencrypt/cloudns/kumpeapps.com/fullchain.pem';
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $certificate_location);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $certificate_location);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $sendDataJson);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $ar_request_head);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if(empty(curl_error($ch))){
    // echo "empty curl error \n";
	}else{
		// echo curl_error($ch);
	}

	// Logging
  // After we need to remove device tokens which had response code 410.
  /*
		if(intval($httpcode) == 200){ fwrite($fp_ok, $output); }
		else{ fwrite($fp_ng, $output); }

		if(intval($httpcode) == 410){ fwrite($fp_410, $output); }
  */
  curl_close($ch);
  
  	if(intval($httpcode) == 410){ 
  		// echo("Invalid Token");
  
		return TRUE;
	}
}

?>