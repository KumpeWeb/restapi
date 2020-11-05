<?php

include_once('/var/www/html/kumpeapps.com/api/apns/kkid.php');

if($allowPut){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	$idChoreList = $_REQUEST['idChoreList'];
	$updatedBy = $authenticatedUser;
	
	if(isset($_REQUEST['kidUsername'])){
		$kidUsername = $_REQUEST['kidUsername'];
		$kidUsernameWhere = "AND kid = '$kidUsername'";
	}else{
		$kidUsernameWhere = '';
		$kidUsername = NULL;
	}
	
	if(isset($_REQUEST['nfcTag'])){
		if($_REQUEST['nfcTag'] == "null"){
			$nfcTag = ", nfcTag=null";
		}else{
			$nfcTag = ", nfcTag='".$_REQUEST['nfcTag']."'";
		}
	}else{
		$nfcTag = '';
	}
	
	if(isset($_REQUEST['status'])){
		$status = ",status='".$_REQUEST['status']."'";
	}else{
		$status = '';
	}
	
	if(isset($_REQUEST['stolen'])){
		$stolen = ", stolen = ".$_REQUEST['stolen']."";
	}else{
		$stolen = '';
	}
	
	if(isset($_REQUEST['stolenBy'])){
		if($_REQUEST['stolenBy'] == "null"){
			$stolenBy = ", stolenBy=null";
		}else{
			$stolenBy = ", stolenBy='".$_REQUEST['stolenBy']."'";
		}
	}else{
		$stolenBy = '';
	}
	
	if(isset($_REQUEST['notes'])){
		if($_REQUEST['notes'] == "null"){
			$notes = ", notes=null";
		}else{
			$notes = ", notes='".$_REQUEST['notes']."'";
		}
	}else{
		$notes = '';
	}
	
	if(isset($_REQUEST['latitude'])){
		$latitude = ",latitude='".$_REQUEST['latitude']."'";
	}else{
		$latitude = '';
	}
	
	if(isset($_REQUEST['longitude'])){
		$longitude = ",longitude='".$_REQUEST['longitude']."'";
	}else{
		$longitude = '';
	}
	
	if(isset($_REQUEST['altitude'])){
		$altitude = ",altitude='".$_REQUEST['altitude']."'";
	}else{
		$altitude = '';
	}
	
	$sql = "
		UPDATE Apps_KKid.Chores__List
		SET updatedBy='$updatedBy', updated=now() $nfcTag $status $stolen $stolenBy $notes $latitude $longitude $altitude
    	WHERE 1=1
    		AND masterID='$masterID'
    		AND idChoreList='$idChoreList'
    		$kidUsernameWhere;";
    		
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	
	if(($kidUsername != NULL)) {

		$sql2 = "
			SELECT 
    			COUNT(*) as Count
			FROM
    			Apps_KKid.Chores__Today
			WHERE 1=1
				AND kid = '$kidUsername'
        		AND Day != 'Weekly'
        		AND Status = 'todo';
		";

		$choreCountData = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
		$choreCountArray = mysqli_fetch_array($choreCountData);
		$choreCount = $choreCountArray['Count'];
	
		//work on this function
		kkidPushNotification($kidUsername,"Chores",NULL,NULL,intval($choreCount),"",NULL);

	}
	if($query){
		$json = array("status" => 1, "message" => "PUT Successful");
		$statusCode = 200;
	}else{
		$json = array("status" => 0, "error" => "Error, chore update not successful!");
		
		//Set Status Code to 206 (Partial Content)
		$statusCode = 206;
		
	}
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>