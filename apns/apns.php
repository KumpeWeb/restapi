<?php
/*
Simple iOS push notification with auth key
*/
	require_once('inc_jwt_helper.php');

	$Title = $_POST['title'];
	$Body = $_POST['body'];
	$Badge = (is_numeric($_POST['badge']) ? (int)$_POST['badge'] : NULL);
	$Sound = $_POST['sound'];
	$Token = $_POST['token'];
	$AppID = $_POST['AppID'];
	
	$authKey = "AuthKey_KXTY95CN6R.p8";
  	$arParam['teamId'] = '2T42Z3DM34';// Get it from Apple Developer's page
 	$arParam['authKeyId'] = 'KXTY95CN6R';
  	$arParam['apns-topic'] = $AppID;
	$arClaim = ['iss'=>$arParam['teamId'], 'iat'=>time()];
	$arParam['p_key'] = file_get_contents($authKey);
	$arParam['header_jwt'] = JWT::encode($arClaim, $arParam['p_key'], $arParam['authKeyId'], 'RS256');
	$arSendData = array();

	if($includeAlert){
		$arSendData['aps']['alert']['title'] = sprintf($Title); // Notification title
		$arSendData['aps']['alert']['body'] = sprintf($Body); // body text
		$arSendData['aps']['sound'] = sprintf($Sound); // sound
	}
	
	if($includeBadge){
		$arSendData['aps']['badge'] = $Badge; // badge #
	}
	
	if($isBackgroundNotification){
		$arSendData['aps']['content-available'] = 1;
	}

	// Sending a request to APNS
	$stat = push_to_apns($arParam, $ar_msg, $arSendData);
	if($stat == FALSE){
    // err handling
		exit();
	}

	exit();

// ***********************************************************************************
function push_to_apns($arParam, &$ar_msg, $arSendData){

	

	$sendDataJson = json_encode($arSendData);
  
	$endPoint = 'https://api.push.apple.com/3/device'; // https://api.push.apple.com/3/device

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
