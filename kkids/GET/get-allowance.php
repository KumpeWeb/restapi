<?php

if($allowGet){
	// Set Status to 202 (Accepted)
	$statusCode = 202;
	
	if(isset($_REQUEST['kidUserId'])){
		$kidUserId = "AND userID = '".$_REQUEST['kidUserId']."'";
	}else{
		$kidUserId = '';
	}
	
	if(isset($_REQUEST['transactionDays'])){
		$days = $_REQUEST['transactionDays'];
	}else{
		$days = 90;
	}
	
	$sql = "
		SELECT 
    		idAllowance_Transactions AS transactionID,
    		userID,
    		transactionType,
    		date,
    		description,
    		amount
		FROM
    		Apps_KKid.Allowance_Transactions
    	WHERE 1=1
    		AND masterID='$masterID'
    		$kidUserId
    		AND date >= DATE_SUB(now(), INTERVAL $days DAY)
    	ORDER BY date DESC;";
    		
	$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)>=0){
		$result = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
			extract($r);
			$result[] = array(
			 	'transactionId' => intval($transactionID),
			 	'userId' => intval($userID),
				'transactionType' => $transactionType,
			 	'date' => $date,
				'transactionDescription' => $description,
				'amount' => floatval($amount));
		}
		
		$sql = "
			SELECT
				parameter,
				value,
				decimalValue
			FROM Apps_KKid.Parameters
			WHERE 1=1
    		AND masterID='$masterID'
    		$kidUserId
    		AND parameter = 'Allowance_Balance';
		";
		
		$query = mysqli_query($conn, $sql) or die("Couldn't execute query. ". mysqli_error($conn)); 
  		$allowanceBalance = mysqli_fetch_array($query); 
  		
		$json = array("status" => 1, "id" => intval($_REQUEST['kidUserId']),"balance" => floatval($allowanceBalance['decimalValue']), "lastUpdated" => $allowanceBalance['value'], "allowanceTransaction" => $result);
		if(isset($_REQUEST['outputCase']) && $_REQUEST['outputCase'] == "snake"){
			
				$json = $convertToCase->$snake($json);
			}
		$statusCode = 200;
	}
	else{
		$json = array("status" => 0, "error" => "No Data Found!");
		
		//Set Status Code to 206 (Partial Content)
		$statusCode = 206;
		
	}
}else{
	//Set Status Code to 405 (Method Not Allowed)
	$statusCode = 405;
}
?>