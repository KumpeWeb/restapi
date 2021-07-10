<?php

if($allowGet){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	if(isset($_REQUEST['isChild'])){
		$isChild = "AND isChild = ".$_REQUEST['isChild']."";
	}else{
		$isChild = '';
	}
	
	if(isset($_REQUEST['isActive'])){
		$isActive = "AND isActive = ".$_REQUEST['isActive']."";
	}else{
		$isActive = '';
	}
	
	if(isset($_REQUEST['isAdmin'])){
		$isAdmin = "AND isAdmin = ".$_REQUEST['isAdmin']."";
	}else{
		$isAdmin = '';
	}
	
	if(isset($_REQUEST['enableAllowance'])){
		$enableAllowance = "AND enableAllowance = ".$_REQUEST['enableAllowance']."";
	}else{
		$enableAllowance = '';
	}
	
	if(isset($_REQUEST['enableChores'])){
		$enableChores = "AND enableChores = ".$_REQUEST['enableChores']."";
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
    		enableNoAds,
    		enableTmdb,
    		enableObjectDetection,
    		WeeklyAllowance,
    		emoji,
    		tmdbKey,
    		pushChores,
    		pushChoresNew,
    		pushChoresReminders,
    		pushAllowance,
    		pushAllowanceNew
		FROM
    		Apps_KKid.UserList
    	WHERE 1=1
    		AND masterID='$masterID' 
    		AND homeID = '$homeID' 
    		AND username NOT LIKE 'Apps_%' 
    		AND username NOT LIKE 'API_%'
    		$isChild $isActive $isAdmin $enableAllowance $enableChores $userID $kkidUsername $email;";
	$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$result = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
			extract($r);
			
				$result[] = array(
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
					'enableBehaviorChart' => $boolOutput($enableBehaviorChart),
					'enableNoAds' =>	$boolOutput($enableNoAds),
					'enableTmdb' =>	$boolOutput($enableTmdb),
					'enableObjectDetection' =>	$boolOutput($enableObjectDetection),
					'weeklyAllowance' => floatval($WeeklyAllowance),
					'emoji' => $emoji,
					'tmdbKey' => $tmdbKey,
					'pushChores' =>	$boolOutput($pushChores),
					'pushChoresNew' =>	$boolOutput($pushChoresNew),
					'pushChoresReminders' =>	$boolOutput($pushChoresReminders),
					'pushAllowance' =>	$boolOutput($pushAllowance),
					'pushAllowanceNew' =>	$boolOutput($pushAllowanceNew));
		}
		
		if(isset($_REQUEST['outputCase']) && $_REQUEST['outputCase'] == "snake"){
			
			$result = $convertToCase->$snake($result);
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
	
?>