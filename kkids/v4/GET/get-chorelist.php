<?php
include_once("/var/www/html/kumpeapps.com/api/kkids/DayOfWeekSwitch.php");

if($allowGet){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	if(isset($_REQUEST['kidUsername'])){
		$kidUsername = "AND kid = '".$_REQUEST['kidUsername']."'";
		$kidUsername2 = "AND username = '".$_REQUEST['kidUsername']."'";
	}else{
		$kidUsername = '';
		$kidUsername2 = '';
	}
	
	if(isset($_REQUEST['day'])){
		$day = "AND day = '".$_REQUEST['day']."'";
	}else{
		$day = '';
	}
	
	if(isset($_REQUEST['includeCalendar']) && $_REQUEST['includeCalendar'] == "true"){
		$includeCalendar = "";
	}else{
		$includeCalendar = "AND isCalendar = 0";
	}
	
	if(isset($_REQUEST['status'])){
		$status = "AND status = '".$_REQUEST['status']."'";
	}else{
		$status = '';
	}
	
	if(isset($_REQUEST['blockDash'])){
		$blockDash = "AND blockDash = ".$_REQUEST['blockDash']."";
	}else{
		$blockDash = '';
	}
	
	if(isset($_REQUEST['optional'])){
		$optional = "AND optional = ".$_REQUEST['optional']."";
	}else{
		$optional = '';
	}
	
	if(isset($_REQUEST['canSteal'])){
		$canSteal = "AND canSteal = ".$_REQUEST['canSteal']."";
	}else{
		$canSteal = '';
	}
	
	
	$choreSQL = "
		SELECT 
    		idChoreList,
    		kid,
    		day,
    		choreName,
    		choreDescription,
    		choreNumber,
    		nfcTag,
    		status,
    		blockDash,
    		oneTime,
    		extraAllowance,
    		stolen,
    		stolenBy,
    		optional,
    		reassignable,
    		reassigned,
    		updated,
    		updatedBy,
    		canSteal,
    		startDate,
    		notes,
    		latitude,
    		longitude,
    		altitude,
    		isCalendar
		FROM
    		Apps_KKid.Chores__List
    	WHERE 1=1
    		AND masterID='$masterID' 
    		AND startDate <= now()
    		$includeCalendar $day $status $blockDash $optional $canSteal ";
    		
    if(isset($_REQUEST['includeUserInfo']) && $_REQUEST['includeUserInfo'] == 1){
    	
    	$sql = "
		SELECT 
    		userID,
    		masterID,
    		homeID,
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
    		WeeklyAllowance,
    		emoji
		FROM
    		Apps_KKid.UserList
    	WHERE 1=1
    		AND masterID = '$masterID' 
    		AND homeID = '$homeID' 
    		$kkidUsername2;";
    		
	$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$userResult = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
			extract($r);
			
			$sql2 = $choreSQL."AND kid = '$username';";
    	
		$get_data_query2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query2)!=0){
			$result = array();
		
			while($r = mysqli_fetch_array($get_data_query2)){
				extract($r);
				$result[] = array(
			 		'id' => intval($idChoreList),
			 		'kid' => $kid,
					'day' => $day,
					'dayAsInt' => $dayAsInt($day),
			 		'choreName' => $choreName,
					'choreDescription' => $choreDescription,
					'choreNumber' => intval($choreNumber),
					'nfcTag' => $nfcTag,
					'status' => $status,
					'blockDash' => $boolOutput($blockDash),
					'oneTime' => $boolOutput($oneTime),
					'extraAllowance' => floatval($extraAllowance),
					'stolen' => $boolOutput($stolen),
					'stolenBy' => $stolenBy,
					'optional' => $boolOutput($optional),
					'reassignable' => $boolOutput($reassignable),
					'reassigned' => $boolOutput($reassigned),
					'dateUpdated' => $updated,
					'updatedBy' => $updatedBy,
					'startDate' => $startDate,
					'notes' => $notes,
					'latitude' => floatval($latitude),
					'longitude' => floatval($longitude),
					'altitude' => floatval($altitude),
					'isCalendar' => $boolOutput($isCalendar));
			}
			
			
		}
		$userResult[] = array(
			 	'userId' => intval($userID),
			 	'masterId' => intval($masterID),
			 	'homeId' => intval($homeID),
				'username' => $username,
			 	'firstName' => $firstName,
				'lastName' => $lastName,
				'email' => $email,
				'isActive' => $boolOutput($isActive),
				'isAdmin' => $boolOutput($isAdmin),
				'enableAllowance' => $boolOutput($enableAllowance),
				'isBanned' => $boolOutput($isBanned),
				'isChild' => $boolOutput($isChild),
				'enableChores' => $boolOutput($enableChores),
				'isDisabled' => $boolOutput($isDisabled),
				'isLocked' => $boolOutput($isLocked),
				'isMaster' => $boolOutput($isMaster),
				'enableBehaviorChart' => $boolOutput(0),
				'weeklyAllowance' => floatval($WeeklyAllowance),
				'emoji' => $emoji,
				'chores' => $result);
		}
		
		if(isset($_REQUEST['outputCase']) && $_REQUEST['outputCase'] == "snake"){
			
			$userResult = $convertToCase->$snake($userResult);
		}
		$json = array("status" => 1, "user" => $userResult);
			$statusCode = 200;
		}
    }else{
    	
    	$sql = $choreSQL.$kidUsername;
    	
		$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
			$result = array();
		
			while($r = mysqli_fetch_array($get_data_query)){
				extract($r);
				$result[] = array(
			 		'id' => intval($idChoreList),
			 		'kid' => $kid,
					'day' => $day,
					'dayAsInt' => $dayAsInt($day),
			 		'choreName' => $choreName,
					'choreDescription' => $choreDescription,
					'choreNumber' => intval($choreNumber),
					'nfcTag' => $nfcTag,
					'status' => $status,
					'blockDash' => $boolOutput($blockDash),
					'oneTime' => $boolOutput($oneTime),
					'extraAllowance' => floatval($extraAllowance),
					'stolen' => $boolOutput($stolen),
					'stolenBy' => $stolenBy,
					'optional' => $boolOutput($optional),
					'reassignable' => $boolOutput($reassignable),
					'reassigned' => $boolOutput($reassigned),
					'dateUpdated' => $updated,
					'updatedBy' => $updatedBy,
					'startDate' => $startDate,
					'notes' => $notes,
					'latitude' => floatval($latitude),
					'longitude' => floatval($longitude),
					'altitude' => floatval($altitude),
					'isCalendar' => $boolOutput($isCalendar));
			}
			if(isset($_REQUEST['outputCase']) && $_REQUEST['outputCase'] == "snake"){
			
				$result = $convertToCase->$snake($result);
			}
			$json = array("status" => 1, "chore" => $result);
			$statusCode = 200;
		}else{
			$json = array("status" => 0, "error" => "No Data Found!");
			$statusCode = 200;
		}
	}
	
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>