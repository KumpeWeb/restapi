<?php

include_once('/var/www/html/kumpeapps.com/api/apns/kkid.php');

if($allowPost){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	$updatedBy = $authenticatedUser;
	
	if(isset($_REQUEST['kidUsername'])){
		if($_REQUEST['kidUsername'] == ''){
			$kidUsername = "any";
		}else{
			$kidUsername = mysqli_real_escape_string($conn,$_REQUEST['kidUsername']);
		}
	}else{
		$kidUsername = "any";
	}
	
	if(isset($_REQUEST['day'])){
		if($_REQUEST['day'] == 'Today'){
			$day = "CONVERT( DAYNAME(NOW()) USING LATIN1)";
		}else{
			$day = mysqli_real_escape_string($conn,$_REQUEST['day']);
		}
	}else{
		$day = "'Weekly'";
	}
	
	if(isset($_REQUEST['nfcTag'])){
		if($_REQUEST['nfcTag'] == "null"){
			$nfcTag = "null";
		}else{
			$nfcTag = "'".mysqli_real_escape_string($conn,$_REQUEST['nfcTag'])."'";
		}
	}else{
		$nfcTag = "null";
	}
	
	if(isset($_REQUEST['status'])){
		$status = "'".mysqli_real_escape_string($conn,$_REQUEST['status'])."'";
	}else{
		$status = "'todo'";
	}
	
	if(isset($_REQUEST['choreName'])){
		$choreName = mysqli_real_escape_string($conn,$_REQUEST['choreName']);
	}else{
		$choreName = "Chore Not Named";
	}
	
	if(isset($_REQUEST['choreDescription'])){
		$choreDescription = "'".mysqli_real_escape_string($conn,$_REQUEST['choreDescription'])."'";
	}else{
		$choreDescription = "''";
	}
	
	if(isset($_REQUEST['choreNumber'])){
		$choreNumber = "'".mysqli_real_escape_string($conn,$_REQUEST['choreNumber'])."'";
	}else{
		$choreNumber = "5";
	}
	
	if(isset($_REQUEST['blockDash'])){
		$blockDash = "".mysqli_real_escape_string($conn,$_REQUEST['blockDash'])."";
	}else{
		$blockDash = "0";
	}
	
	if(isset($_REQUEST['oneTime'])){
		$oneTime = "".mysqli_real_escape_string($conn,$_REQUEST['oneTime'])."";
	}else{
		$oneTime = "0";
	}
	
	if(isset($_REQUEST['extraAllowance'])){
		$extraAllowance = "'".mysqli_real_escape_string($conn,$_REQUEST['extraAllowance'])."'";
	}else{
		$extraAllowance = "'0.00'";
	}
	
	if(isset($_REQUEST['optional'])){
		$optional = "".mysqli_real_escape_string($conn,$_REQUEST['optional'])."";
	}else{
		$optional = "0";
	}
	
	if(isset($_REQUEST['reassignable'])){
		$reassignable = "".mysqli_real_escape_string($conn,$_REQUEST['reassignable'])."";
	}else{
		$reassignable = "1";
	}
	
	if(isset($_REQUEST['canSteal'])){
		$canSteal = "".mysqli_real_escape_string($conn,$_REQUEST['canSteal'])."";
	}else{
		$canSteal = "1";
	}
	
	if(isset($_REQUEST['startDate'])){
		$startDate = "CAST('".mysqli_real_escape_string($conn,$_REQUEST['startDate'])."' AS DATE)";
	}else{
		$startDate = "now()";
	}
	
	if(isset($_REQUEST['notes'])){
		if($_REQUEST['notes'] == "null"){
			$notes = "null";
		}else{
			$notes = "'".mysqli_real_escape_string($conn,$_REQUEST['notes'])."'";
		}
	}else{
		$notes = "null";
	}
	
	$sql = "
		INSERT INTO Apps_KKid.Chores__List
			(`kid`,`masterID`,`day`,`status`,`choreName`,`choreDescription`,`choreNumber`,`nfcTag`,`blockDash`,`oneTime`,`extraAllowance`,`optional`,`reassignable`,`canSteal`,`notes`,`startDate`,`updatedBy`,`updated`)
		VALUES
			('$kidUsername',$masterID,'$day',$status,'$choreName',$choreDescription,$choreNumber,$nfcTag,$blockDash,$oneTime,$extraAllowance,$optional,$reassignable,$canSteal,$notes,$startDate,'$updatedBy',now());";
    	
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	
	$sql2 = "
		SELECT 
    		COUNT(*) as Count
		FROM
    		Apps_KKid.Chores__Today
		WHERE 1=1
			AND kid = '$kidUsername'
        	AND Day != 'Weekly'
        	AND Status = 'todo';;
	";
	$choreCountData = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
	$choreCountArray = mysqli_fetch_array($choreCountData);
	$choreCount = $choreCountArray['Count'];
	
	// kkidPushNotification($kidUsername,"Chores",NULL,NULL,$choreCount,"",NULL);
	kkidPushNotification($kidUsername,"Chores-New","$day New Chore Added","$choreName has been added to your chore list for $day.",$choreCount,"default",NULL);
	
	if($query){
		$json = array("status" => 1, "message" => "POST Successful");
		$statusCode = 200;
	}else{
		$json = array("status" => 0, "error" => "Error, chore insert not successful!");
		//Set Status Code to 206 (Partial Content)
		$statusCode = 206;
		
	}
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>