<?php

if($allowPut){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	$apiKey = $_REQUEST['apiKey'];
	
	$sql = "
		UPDATE Core_RESTAPI.API_Keys__KKid SET expirationDate = now() 
		WHERE 1=1
			AND apiKey = '$apiKey';";
	mysqli_query($conn, $sql) or die(mysqli_error($conn));
	$json = array("status" => 1, "message" => "API Key Set to Expire Immediately");
		
//Set Status Code to 200 (Accepted)
	$statusCode = 200;
		
	
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>