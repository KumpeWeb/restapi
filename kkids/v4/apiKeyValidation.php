<?php

	$KeyData = "
		SELECT 
    		apiKey,
    		apiUser,
    		authenticatedUser,
    		masterID,
    		homeID
		FROM
    		Core_RESTAPI.API_Key_Views__KKid
		WHERE 1=1
			AND apiKey = '$apiKey'
			AND apiUser = '$apiUsername';
	"; 
  		$KeyQuery = mysqli_query($conn, $KeyData) or die("Couldn't execute query. ". mysqli_error($conn)); 
  		$KeyFetch = mysqli_fetch_array($KeyQuery); 
  	
  	if($KeyFetch['apiKey'] == $apiKey){
  		$masterID = $KeyFetch['masterID'];
  		$homeID = $KeyFetch['homeID'];
  		$authenticatedUser = $KeyFetch['authenticatedUser'];
  	}else{
  		$masterID = 0;
  		$homeID = 0;
  		$statusCode = 412;
  		$authorized = false;
  		@mysqli_close($conn);
  		http_response_code($statusCode);
  		$json = array("status" => 0, "error" => "API Access Denied! Your API key is invalid or has expired!!!");
		header('Content-type: application/json');
		echo json_encode($json);
		exit();
  	}

?>