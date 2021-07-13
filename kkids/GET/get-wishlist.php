<?php

if($allowGet){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	if(isset($_REQUEST['kidUserId'])){
		$kidUserId = "AND userID = '".$_REQUEST['kidUserId']."'";
	}else{
		$kidUserId = '';
	}
	
	
	$listSQL = "
		SELECT 
    		idWishList,
    		userID,
    		masterID,
    		title,
    		description,
    		priority,
    		link
		FROM
    		Apps_KKid.WishList
		WHERE
    		1 = 1
    		AND masterID = $masterID ";
    		
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
    		enableObjectDetection,
    		enableWishList,
    		enableMeds,
    		enableMedsTake,
    		WeeklyAllowance,
    		emoji
		FROM
    		Apps_KKid.UserList
    	WHERE 1=1
    		AND masterID = '$masterID' 
    		AND homeID = '$homeID'";
    		
	$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$userResult = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
			extract($r);
			
			$sql2 = $listSQL." AND userID = '$userID';";
    	
		$get_data_query2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query2)!=0){
			$result = array();
		
			while($r = mysqli_fetch_array($get_data_query2)){
				extract($r);
				$result[] = array(
			 		'id' => intval($idWishList),
			 		'userID' => intval($idUsers),
					'masterID' => intval($masterID),
					'title' => $title,
					'description' => $description,
					'priority' => intval($priority),
					'link' => $link);
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
				'enableObjectDetection' => floatval($enableObjectDetection),
				'emoji' => $emoji,
				'list' => $result);
		}
		
		if(isset($_REQUEST['outputCase']) && $_REQUEST['outputCase'] == "snake"){
			
			$userResult = $convertToCase->$snake($userResult);
		}
		$json = array("status" => 1, "user" => $userResult);
			$statusCode = 200;
		}
    }else{
    	
    	$sql = $listSQL.$kidUsername;
    	
		$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
			$result = array();
		
			while($r = mysqli_fetch_array($get_data_query)){
				extract($r);
				$result[] = array(
			 		'id' => intval($idWishList),
			 		'userID' => intval($idUsers),
					'masterID' => intval($masterID),
					'title' => $title,
					'description' => $description,
					'priority' => intval($priority),
					'link' => $link);
			}
			if(isset($_REQUEST['outputCase']) && $_REQUEST['outputCase'] == "snake"){
			
				$result = $convertToCase->$snake($result);
			}
			$json = array("status" => 1, "list" => $result);
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
