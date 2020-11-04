<?php

if($allowDelete && $isSuperuser){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	$updatedBy = $authenticatedUser;
	
	
	
	if(isset($_REQUEST['userID']) && $_REQUEST['userID'] != ''){
	
	$kidUserID = $_REQUEST['userID'];
	
	
		//Delete User
		$url = "https://www.kumpeapps.com/api/users/$kidUserID";
		$apiKey = $kumpeAppsApiKey;
		$fields = array(
    		'_key' => $apiKey,
    		'_method' => "DELETE",
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
	
	if(isset($response1['_success']) && $response1['_success']){
		$json = $response1;
		$statusCode = 200;
	}else{
		$json = array("status" => 0, "error" => "Delete user unsuccessful. User may not exist.");
		//Set Status Code to 410 (Gone)
		$statusCode = 410;
	}
	}else{
		$json = array("status" => 0, "error" => "Error, userID parameter is REQUIRED!");
		
		//Set Status Code to 428 (Precondition Required)
		$statusCode = 428;
	}
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>