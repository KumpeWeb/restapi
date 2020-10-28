<?php

include_once('/var/www/html/kumpeapps.com/api/kkids/accessPermissions.php');
include_once('/var/www/html/kumpeapps.com/api/apiAuth.php');

if($isSuperuser){
	$masterID = isset($_REQUEST['masterID']) ? mysqli_real_escape_string($conn, $_REQUEST['masterID']) :  $getMasterId;
}else{
	$masterID = $getMasterId;
}

if($_SERVER['REQUEST_METHOD'] == "GET" && $authorized){
	if($allowGet){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	if(isset($_REQUEST['isChild'])){
		$isChild = "AND isChild = '".$_REQUEST['isChild']."'";
	}else{
		$isChild = '';
	}
	
	if(isset($_REQUEST['isActive'])){
		$isActive = "AND isActive = '".$_REQUEST['isActive']."'";
	}else{
		$isActive = '';
	}
	
	if(isset($_REQUEST['isAdmin'])){
		$isAdmin = "AND isAdmin = '".$_REQUEST['isAdmin']."'";
	}else{
		$isAdmin = '';
	}
	
	if(isset($_REQUEST['enableAllowance'])){
		$enableAllowance = "AND enableAllowance = '".$_REQUEST['enableAllowance']."'";
	}else{
		$enableAllowance = '';
	}
	
	if(isset($_REQUEST['enableChores'])){
		$enableChores = "AND enableChores = '".$_REQUEST['enableChores']."'";
	}else{
		$enableChores = '';
	}
	
	if(isset($_REQUEST['userID'])){
		$userID = "AND userID = '".$_REQUEST['userID']."'";
	}else{
		$userID = '';
	}
	
	if(isset($_REQUEST['username'])){
		$kkidUsername = "AND username = '".$_REQUEST['username']."'";
	}else{
		$kkidUsername = '';
	}
	
	if(isset($_REQUEST['email'])){
		$email = "AND email = '".$_REQUEST['email']."'";
	}else{
		$email = '';
	}
	
	$sql = "
		SELECT 
    		userID,
    		masterID,
    		username,
    		firstName,
    		lastName,
    		email,
    		isActive,
    		isAdmin,
    		enableAllowance,
    		isBanned,
    		isChild,
    		enableChores,
    		isDisabled,
    		isLocked,
    		isMaster,
    		enableBehaviorChart,
    		WeeklyAllowance
		FROM
    		Apps_KKid.User_Permissions
    	WHERE 1=1
    		AND masterID='$masterID' $isChild $isActive $isAdmin $enableAllowance $enableChores $userID $kkidUsername $email;";
	$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$result = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
			extract($r);
			$result[] = array(
			 	'userID' => intval($userID),
			 	'masterID' => intval($masterID),
				'username' => $username,
			 	'firstName' => $firstName,
				'lastName' => $lastName,
				'email' => $email,
				'isActive' => $isActive,
				'isAdmin' => $isAdmin,
				'enableAllowance' => $enableAllowance,
				'isBanned' => $isBanned,
				'isChild' => $isChild,
				'enableChores' => $enableChores,
				'isDisabled' => $isDisabled,
				'isLocked' => $isLocked,
				'isMaster' => $isMaster,
				'enableBehaviorChart' => $enableBehaviorChart,
				'weeklyAllowance' => floatval($WeeklyAllowance));
		}
		$json = array("status" => 1, "user" => $result);
		$statusCode = 200;
	}
	else{
		$json = array("status" => 0, "error" => "No Data Found!");
		
		//Set Status Code to 204 (No Content)
		$statusCode = 204;
		
	}
	}else{
		//Set Status Code to 405 (Method Not Allowed)
		$statusCode = 405;
	}
}else if($_SERVER['REQUEST_METHOD'] == "POST" && $authorized){
	
	if($allowPost){
		//Set Status Code to 501 (Not Implemented)
		$statusCode = 501;
	}else{
		//Set Status Code to 405 (Method Not Allowed)
		$statusCode = 405;
	}
}else if($_SERVER['REQUEST_METHOD'] == "PUT" && $authorized){
	if($allowPut){
		//Set Status Code to 501 (Not Implemented)
		$statusCode = 501;
	}else{
		//Set Status Code to 405 (Method Not Allowed)
		$statusCode = 405;
	}
}else if($_SERVER['REQUEST_METHOD'] == "DELETE" && $authorized){
	if($allowDelete){
		//Set Status Code to 501 (Not Implemented)
		$statusCode = 501;
	}else{
		//Set Status Code to 405 (Method Not Allowed)
		$statusCode = 405;
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
}else if($statusCode == 501 && $authorized){
	$json = array("status" => 0, "error" => "This Verb Method has not yet been implemented. Please refrence the API Documentation for correct Verb.");
	header('Content-type: application/json');
	echo json_encode($json);
}


?>