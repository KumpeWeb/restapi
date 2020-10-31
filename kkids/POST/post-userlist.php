<?php

if($allowPost){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	$updatedBy = $authenticatedUser;
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];
	$email = $_REQUEST['email'];
	$firstName = $_REQUEST['firstName'];
	$lastName = $_REQUEST['lastName'];
	
	
//Create User
$url = 'https://www.kumpeapps.com/api/users';
$apiKey = 'F3Ugh2Ug2H5ZdVgbifPg';
$fields = array(
    '_key' => $apiKey,
    'login' => $username,
    'pass' => $password,
    'email' => $email,
    'name_f' => $firstName,
    'name_l' => $lastName,
    'subusers_parent_id' => $masterID,
    'comment' => "Added Via API by authenticated user $updatedBy",
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

//Add Chores Access
$ch = curl_init();
$url ="https://www.kumpeapps.com/api/access";
$vars = array(
        '_key'          =>      $apiKey,
        'user_id'       =>      $response1[0]["user_id"],
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

//Add Allowance Access
$ch = curl_init();
$url ="https://www.kumpeapps.com/api/access";
$vars = array(
        '_key'          =>      $apiKey,
        'user_id'       =>      $response1[0]["user_id"],
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


	if(isset($response1['error']) && $response1['error']){
		$json = array("status" => 0, "error" => "Create user unsuccessful! Username or Email address already exist or do not match requirements.");
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
?>