<?php

if($allowDelete){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	$updatedBy = $authenticatedUser;
	
	
	
	if(isset($_REQUEST['idChoreList']) && $_REQUEST['idChoreList'] != ''){
	
	$idChoreList = $_REQUEST['idChoreList'];
	
	$sql = "
		DELETE FROM Apps_KKid.Chores__List
		WHERE 1=1
			AND masterID = $masterID
			AND idChoreList = $idChoreList;";
    	
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	
	if($query){
		$json = array("status" => 1, "message" => "success");
		$statusCode = 200;
	}else{
		$json = array("status" => 0, "error" => "Delete chore unsuccessful. Chore may not exist.");
		//Set Status Code to 410 (Gone)
		$statusCode = 410;
		
	}
	}else{
		$json = array("status" => 0, "error" => "Error, idChoreList parameter is REQUIRED!");
		
		//Set Status Code to 428 (Precondition Required)
		$statusCode = 428;
	}
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>