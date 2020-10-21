<?php

if($allowPost){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	$updatedBy = $authenticatedUser;
	$userID = $_REQUEST['kidUserId'];
	$amount = $_REQUEST['amount'];
	$description = $_REQUEST['description'];
	$transactionType = $_REQUEST['transactionType'];
	
	$sql = "
		INSERT INTO Apps_KKid.Allowance_Transactions
			(`masterID`,`userID`,`transactionType`,`date`,`description`,`amount`)
		VALUES
			($masterID,$userID,'$transactionType',now(),'$description',$amount);";
    	
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	
	if($query){
		$json = array("status" => 1, "message" => "POST Successful");
		$statusCode = 200;
	}else{
		$json = array("status" => 0, "error" => "Error, allowance insert not successful!");
		//Set Status Code to 206 (Partial Content)
		$statusCode = 206;
		
	}
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>