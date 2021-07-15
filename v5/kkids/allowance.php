<?php

include_once('/var/www/html/kumpeapps.com/api/kkids/accessPermissions.php');
include_once('/var/www/html/kumpeapps.com/api/apiAuth.php');

include_once('/var/www/html/kumpeapps.com/api/kkids/apiKeyValidation.php');

$filename = 'allowance.php';

if($_SERVER['REQUEST_METHOD'] == "GET" && $authorized){
	
	$getFilename = './GET/get-'.$filename;
	
	//If method exists then return method else Status 501
	if(file_exists($getFilename)){
		include $getFilename;
	}else{
		//Set Status Code to 501 (Not Implemented)
		$statusCode = 501;
	}
	
}else if($_SERVER['REQUEST_METHOD'] == "POST" && $authorized){
	
	$postFilename = './POST/post-'.$filename;
	
	//If method exists then return method else Status 501
	if(file_exists($postFilename)){
		include $postFilename;
	}else{
		//Set Status Code to 501 (Not Implemented)
		$statusCode = 501;
	}
	
}else if($_SERVER['REQUEST_METHOD'] == "PUT" && $authorized){
	
	$putFilename = './PUT/put-'.$filename;
	
	//If method exists then return method else Status 501
	if(file_exists($putFilename)){
		include $putFilename;
	}else{
		//Set Status Code to 501 (Not Implemented)
		$statusCode = 501;
	}
	
}else if($_SERVER['REQUEST_METHOD'] == "DELETE" && $authorized){
	
	$deleteFilename = './DELETE/delete-'.$filename;
	
	//If method exists then return method else Status 501
	if(file_exists($deleteFilename)){
		include $deleteFilename;
	}else{
		//Set Status Code to 501 (Not Implemented)
		$statusCode = 501;
	}
	
}

@mysqli_close($conn);
http_response_code($statusCode);
// Set Content-type to JSON
if($statusCode >= 200 && $statusCode <= 299 && $authorized){
	header('Content-type: application/json');
	echo json_encode($json);
}else if($statusCode == 405 && $authorized){
	$json = array("status" => 0, "error" => "API Access Denied! Your API account does not have access to this Verb Method!");
	header('Content-type: application/json');
	echo json_encode($json);
}else if ($statusCode == 406){
	  $json = array("status" => 0, "error" => "API Access Denied! Your API key is invalid or has expired!");
	header('Content-type: application/json');
	echo json_encode($json);
}else if($statusCode == 501 && $authorized){
	$json = array("status" => 0, "error" => "This Verb Method has not yet been implemented. Please refrence the API Documentation for correct Verb.");
	header('Content-type: application/json');
	echo json_encode($json);
}


?>