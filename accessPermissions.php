<?php

// $appUsername = 'Apps_KKid';
// $apiPrefix = 'kkids';

if(!in_array($apiPrefix,$accessPermissions) && $authorized && !$superuser){
        	// Set Status to 401 (Unauthorized)
			$statusCode = 401;
			$authorized = false;
			$json = array("status" => 0, "error" => "API Access Denied! You do not have access to this API Endpoint.");
			header('Content-type: application/json');
			echo json_encode($json);
}else{

	// Superuser has access to ALL API Verbs
	if((in_array($apiPrefix,$accessPermissions) && in_array("apiSuper",$accessPermissions)) || $superuser || ($apiUsername == $appUsername && in_array("iOS App",$accessPermissions))){
		$isSuperuser = true;
		$allowGet = true;
		$allowPost = true;
		$allowPut = true;
		$allowDelete = true;
	}else{
		$isSuperuser = false;
	}

	//GET Verb used to get information using read-only
	if(in_array($apiPrefix,$accessPermissions) && in_array($apiPrefix."-get",$accessPermissions)){
		$allowGet = true;
	}else if (!$isSuperuser){
		$allowGet = false;
	}

	//POST Verb used to create/insert new data
	if(in_array($apiPrefix,$accessPermissions) && in_array($apiPrefix."-post",$accessPermissions)){
		$allowPost = true;
	}else if (!$isSuperuser){
		$allowPost = false;
	}

	//PUT Verb used to update/replace existing data
	if(in_array($apiPrefix,$accessPermissions) && in_array($apiPrefix."-put",$accessPermissions)){
		$allowPut = true;
	}else if (!$isSuperuser){
		$allowPut = false;
	}

	//DELETE Verb used to delete existing data
	if(in_array($apiPrefix,$accessPermissions) && in_array($apiPrefix."-delete",$accessPermissions)){
		$allowDelete = true;
	}else if (!$isSuperuser){
		$allowDelete = false;
	}
}

?>