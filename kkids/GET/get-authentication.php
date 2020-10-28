<?php

if($allowGet){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	$username = $_REQUEST['username'];
	
	$apiKey = bin2hex(random_bytes(64));
	

	
	$sql = "
		SELECT 
    		userID,
    		masterID,
    		homeID,
    		username,
    		password,
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
    		WeeklyAllowance,
    		emoji
		FROM
    		Apps_KKid.UserList
    	WHERE 1=1
    		AND (username = '$username' OR email = '$username');";
	$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$result = array();
		$sqlResult = mysqli_fetch_array($get_data_query);
		
		
			extract($sqlResult);
			
			 	$result['userID'] = intval($userID);
			 	$result['masterID'] = intval($masterID);
			 	$result['homeID'] = intval($homeID);
				$result['username'] = $username;
			 	$result['firstName'] = $firstName;
				$result['lastName'] = $lastName;
				$result['email'] = $email;
				$result['isActive'] = boolval($isActive);
				$result['isAdmin'] = boolval($isAdmin);
				$result['enableAllowance'] = boolval($enableAllowance);
				$result['isBanned'] = boolval($isBanned);
				$result['isChild'] = boolval($isChild);
				$result['enableChores'] = boolval($enableChores);
				$result['isDisabled'] = boolval($isDisabled);
				$result['isLocked'] = boolval($isLocked);
				$result['isMaster'] = boolval($isMaster);
				$result['enableBehaviorChart'] = boolval(0);
				$result['weeklyAllowance'] = floatval($WeeklyAllowance);
				$result['emoji'] = $emoji;
		
		
		if ((strcasecmp($_REQUEST['username'],$sqlResult['username']) == 0 || strcasecmp($_REQUEST['username'],$sqlResult['email']) == 0) && (Password_verify($_REQUEST['password'],$sqlResult['password'])) && $sqlResult['isActive']) { 
			
			$json = array("status" => 1, "apiKey" => $apiKey, "user" => $result);
			
			if($sqlResult['isBanned']){
				$json = array("status" => 0, "error" => "User is Banned");
			}
			
			if($sqlResult['isLocked']){
				$json = array("status" => 0, "error" => "User is Locked ");
			}
			$authenticatedUser = $result['username'];
			$sql = "
				INSERT INTO Core_RESTAPI.API_Keys__KKid SET apiKey='$apiKey', apiUser='$apiUsername', authenticatedUser='$authenticatedUser', authenticationDate=now();";
			mysqli_query($conn, $sql) or die(mysqli_error($conn));
		}else{
			$json = array("status" => 0, "error" => "Username/Password incorrect");
		}
		$statusCode = 200;
	}else{
	
		$sql = "
			SELECT
				login as `username`,
				email as `email`,
				pass as `password`
			FROM Core_KumpeApps.am_user_phphash
			WHERE 1=1
    		AND (login = '$username' OR email = '$username');
		";
		
		$query = mysqli_query($conn, $sql) or die("Couldn't execute query. ". mysqli_error($conn)); 
  		$sqlResult = mysqli_fetch_array($query); 
  		
  		if (($_REQUEST['username'] == $sqlResult['username'] || $_REQUEST['username'] == $sqlResult['email']) && (Password_verify($_REQUEST['password'],$sqlResult['password']))) {
  			$json = array("status" => 2, "error" => "Username/Password valid but no access to KKid system!");
  		}else{
			$json = array("status" => 0, "error" => "Username/Password invalid");
		}
		//Set Status Code to 200
		$statusCode = 200;
		
	}
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>