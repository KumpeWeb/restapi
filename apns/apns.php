<?php
/*
Simple iOS push notification with auth key
*/
	use \Firebase\JWT\JWT;
	
function register_apns($token,$appName,$userID,$deviceName,$masterID){
	$sql="
		INSERT INTO `Apps_APNs`.`Tokens` (token, appName, userID, deviceName, masterID, lastUpdated, markForDeletion)
			VALUES('$token','$appName','$userID','$deviceName','$masterID',now(),0) 
			ON DUPLICATE KEY UPDATE userID='$userID', deviceName='$deviceName', masterID='$masterID', lastUpdated=now(), markForDeletion=0
	";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}

function subscribe_apns($appName,$userID,$masterID,$sectionName){
	$sql="
		INSERT INTO `Apps_APNs`.`Subscriptions` (appName, userID, masterID, sectionName)
			VALUES('$appName','$userID','$masterID','$sectionName')
	";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}
	
function send_apns($Title, $Body, $Badge, $Sound, $Token, $AppID, $Action) {
	$Badge = (is_numeric($Badge) ? (int)$Badge : NULL);
	$action = $Action;
	
	
	
	$includeAlert = true;
	$includeBadge = true;
	
	if(ISSET($_REQUEST['isBackgroundNotification'])){
		$isBackgroundNotification = true;
	}
	
	if(ISSET($_REQUEST['isSandbox'])){
		$isSandbox = true;
	}
		
	$authKey = "AuthKey_KXTY95CN6R.p8";
  	$arParam['teamId'] = '2T42Z3DM34';// Get it from Apple Developer's page
 	$arParam['authKeyId'] = 'KXTY95CN6R';
  	$arParam['apns-topic'] = $AppID;
	$arClaim = ['iss'=>$arParam['teamId'], 'iat'=>time()];
	$arParam['p_key'] = file_get_contents($authKey);
	$arParam['header_jwt'] = JWT::encode($arClaim, $arParam['p_key'], $arParam['authKeyId'], 'RS256');
	$arSendData = array();
	$arSendData['aps']['action'] = sprintf($action);

	if(isset($_REQUEST['title'])){
		$arSendData['aps']['alert']['title'] = sprintf($Title); // Notification title
		$arSendData['aps']['alert']['body'] = sprintf($Body); // body text
	}
	
		$arSendData['aps']['sound'] = sprintf($Sound); // sound
	if(isset($_REQUEST['badge'])){
		$arSendData['aps']['badge'] = $Badge; // badge #
	}
	
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
		exit();
	}

	exit();
}
// ***********************************************************************************
function push_to_apns($arParam, &$ar_msg, $arSendData, $Token, $isSandbox){

	

	$sendDataJson = json_encode($arSendData);
  
	$endPoint = 'https://api.push.apple.com/3/device'; // https://api.[sandbox.]push.apple.com/3/device

	//　Preparing request header for APNS
	$ar_request_head[] = sprintf("content-type: application/json");
	$ar_request_head[] = sprintf("authorization: bearer %s", $arParam['header_jwt']);
	$ar_request_head[] = sprintf("apns-topic: %s", $arParam['apns-topic']);

	$dev_token = $Token;  // Device token

	$url = sprintf("%s/%s", $endPoint, $dev_token);

	$ch = curl_init($url);
	
	$certificate_location = '/etc/apache2/ssl/kumpeapps.com/fullchain.pem';
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $certificate_location);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $certificate_location);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $sendDataJson);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $ar_request_head);
	$response = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if(empty(curl_error($ch))){
    // echo "empty curl error \n";
	}else{
		echo curl_error($ch);
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
  echo("Invalid Token");
  echo($Token);
  	$host = "sql.kumpedns.us";
	$user = "Apps_APNs";
	$password = "Tc4wcPikQ";
	$database = "Apps_APNs";
	
	$data = "DELETE FROM `APNS_Tokens` WHERE `Push_Token` = '".$Token."'";
	$connection = mysqli_connect($host,$user,$password) or die ("Couldn't connect to server."); 
	$db = mysqli_select_db($connection, $database) or die ("Couldn't select database.");
  	$Query = mysqli_query($connection, $data) or die("Couldn't execute query. ". mysqli_error($connection));
  }
  
	

	return TRUE;
}

?>

