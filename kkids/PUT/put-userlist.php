<?php

if($allowPut){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	$updatedBy = $authenticatedUser;
	$kidUserID = $_REQUEST['userID'];
	$username = $_REQUEST['username'];
	$email = $_REQUEST['email'];
	$firstName = $_REQUEST['firstName'];
	$lastName = $_REQUEST['lastName'];
	$emoji = $_REQUEST['emoji'];
	
	
//Update User
$url = "https://www.kumpeapps.com/api/users/$kidUserID";
$apiKey = $kumpeAppsApiKey;
$fields = array(
    '_key' => $apiKey,
    '_method' => "PUT",
    'login' => $username,
    'email' => $email,
    'name_f' => $firstName,
    'name_l' => $lastName,
    'comment' => "Updated Via API by authenticated user $updatedBy",
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

if(isset($_REQUEST['enableChores']) && $_REQUEST['enableChores'] == 'true'){
//Add Chores Access
$ch = curl_init();
$url = "https://www.kumpeapps.com/api/access";
$vars = array(
        '_key'          =>      $apiKey,
        'user_id'       =>      $kidUserID,
        'product_id'    =>      157,
        'begin_date'    =>      date('Y-m-d'), // Today;
        'expire_date'   =>      '2037-12-31', // Lifetime
        'comment'		=>		"Added via API by authenticated user $authenticatedUser"
);
//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
//execute post
curl_exec($ch);

//close connection
curl_close($ch);
}

if(isset($_REQUEST['enableAllowance']) && $_REQUEST['enableAllowance'] == 'true'){
//Add Allowance Access
$ch = curl_init();
$url ="https://www.kumpeapps.com/api/access";
$vars = array(
        '_key'          =>      $apiKey,
        'user_id'       =>      $kidUserID,
        'product_id'    =>      156,
        'begin_date'    =>      date('Y-m-d'), // Today;
        'expire_date'   =>      '2037-12-31', // Lifetime
        'comment'		=>		"Added via API by authenticated user $authenticatedUser"
);
//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
//execute post
curl_exec($ch);

//close connection
curl_close($ch);
}

if(isset($_REQUEST['enableAdmin']) && $_REQUEST['enableAdmin'] == 'true'){
//Add Admin Access
$ch = curl_init();
$url ="https://www.kumpeapps.com/api/access";
$vars = array(
        '_key'          =>      $apiKey,
        'user_id'       =>      $kidUserID,
        'product_id'    =>      158,
        'begin_date'    =>      date('Y-m-d'), // Today;
        'expire_date'   =>      '2037-12-31', // Lifetime
        'comment'		=>		"Added via API by authenticated user $authenticatedUser"
);
//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
//execute post
curl_exec($ch);

//close connection
curl_close($ch);
}

if(isset($_REQUEST['emoji'])){
//Update User's Emoji
	$sql = "
		UPDATE Apps_KKid.Parameters
		SET value='$emoji'
    	WHERE 1=1
    		AND masterID='$masterID'
    		AND userID='$kidUserID'
    		AND parameter = 'Emoji';";
    		
	mysqli_query($conn, $sql) or die(mysqli_error($conn));
//
}


if(isset($_REQUEST['enableChores']) && $_REQUEST['enableChores'] == 'false'){

$sql = "
	SELECT 
    	access_id,
    	expire_date
	FROM
    	Core_KumpeApps.am_access
	WHERE 1=1
		AND user_id = $kidUserID
    	AND product_id = 157

";
$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$result = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
		
			expireAccess($r['access_id'],$apiKey,$authenticatedUser);
		
		}

}}


if(isset($_REQUEST['enableAllowance']) && $_REQUEST['enableAllowance'] == 'false'){

$sql = "
	SELECT 
    	access_id,
    	expire_date
	FROM
    	Core_KumpeApps.am_access
	WHERE 1=1
		AND user_id = $kidUserID
    	AND product_id = 156

";
$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$result = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
		
			expireAccess($r['access_id'],$apiKey,$authenticatedUser);
		
		}

}}


if(isset($_REQUEST['enableAdmin']) && $_REQUEST['enableAdmin'] == 'false'){

$sql = "
	SELECT 
    	access_id,
    	expire_date
	FROM
    	Core_KumpeApps.am_access
	WHERE 1=1
		AND user_id = $kidUserID
    	AND product_id = 158
    	AND expire_date >= now()

";
$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$result = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
		
			expireAccess($r['access_id'],$apiKey,$authenticatedUser);
		
		}

}
}



	if(isset($response1['error']) && $response1['error']){
		$json = array("status" => 0, "error" => "Update user unsuccessful!");
		//Set Status Code to 409 (Conflict)
		$statusCode = 409;
	}else{
		$json = $response1;
		$statusCode = 200;
	}
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}





function expireAccess($accessID,$apiKey,$authenticatedUser){
	$ch = curl_init();
	$url = "https://www.kumpeapps.com/api/access/$accessID"; // Access id should be included in url
	$vars = array(
        	'_key'          =>      $apiKey,
        	'expire_date'   =>date('Y-m-d', time() - 60 * 60 * 24),  // Set expire date to yesterday's date
        	'comment'		=>		"Expired via API by authenticated user $authenticatedUser"
	);
	//set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Set method to PUT, so API module knows that this is update request.
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch);
}
?>