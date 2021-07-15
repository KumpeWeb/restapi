<?php

require_once '/var/www/html/kumpeapps.com/api/caseConverter.php';
require_once '/var/www/html/kumpeapps.com/api/boolOutput.php';
require_once '/var/www/html/kumpeapps.com/api/GoogleAuthenticator.php';
$ga = new GoogleAuthenticator();

// Set Status to 400 (Bad Request)
$statusCode = 400;
$authorized = false;

include_once('/var/www/html/kumpeapps.com/api/sqlConfig.php');
$apiUsername = isset($_REQUEST['apiUsername']) ? mysqli_real_escape_string($conn, $_REQUEST['apiUsername']) :  "";
$apiPassword = isset($_REQUEST['apiPassword']) ? mysqli_real_escape_string($conn, $_REQUEST['apiPassword']) :  "";
$apiOtp = isset($_REQUEST['apiOtp']) ? mysqli_real_escape_string($conn, $_REQUEST['apiOtp']) :  "";
$apiKey = isset($_SERVER['HTTP_AUTHKEY']) ? mysqli_real_escape_string($conn, $_SERVER['HTTP_AUTHKEY']) :  "none";

if (isset($_REQUEST['apiUsername']) && isset($_REQUEST['apiPassword'])) {

	$AccessData = "
		SELECT 
    		idUsers,
    		userID, 
    		username, 
    		password, 
    		CASE WHEN masterID = 0 THEN userID ELSE masterID END AS masterID, 
    		totpKey
		FROM
    		Core_RESTAPI.Users
		WHERE 1=1
			AND username = '$apiUsername';
	"; 
  		$AccessQuery = mysqli_query($conn, $AccessData) or die("Couldn't execute query. ". mysqli_error($conn)); 
  		$AccessFetch = mysqli_fetch_array($AccessQuery); 
  		$getUsername = $AccessFetch['username'];
  		$getPassword = $AccessFetch['password'];
  		$getTotpKey = $AccessFetch['totpKey'];
  		$getMasterId = $AccessFetch['masterID'];
  		$getUserId = $AccessFetch['userID'];
  		$getIdUsers = $AccessFetch['idUsers'];
  		
  		$validPassword = Password_verify($apiPassword,$getPassword);
  		$validOTP = $ga->verifyCode($getTotpKey, $apiOtp, 2);
  		
    if (($apiUsername == $getUsername) && ($validPassword || $validOTP)){
    	$authorized = true;
    	$accessPermissions = [];
    	//Set Access Permissions
            $accesssql = "
            	SELECT 
    				groupName
				FROM
    				Core_RESTAPI.User_GroupName_Relationship
				WHERE
    				1 = 1
    				AND idUsers = $getIdUsers;";
					
							
			// Check if there are results
			if ($accessresult = mysqli_query($conn, $accesssql))
			{
				// Loop through each row in the result set
				while($access = mysqli_fetch_array($accessresult))
				{
                    $accessPermissions[] = $access['groupName'];
                }
            }
            
        if(in_array('banned',$accessPermissions) || in_array('locked',$accessPermissions) || in_array('noaccess',$accessPermissions)){
        	// Set Status to 403 (Forbidden)
			$statusCode = 403;
			$authorized = false;
			$json = array("status" => 0, "error" => "This account is either banned or locked out!!!!");
			header('Content-type: application/json');
			echo json_encode($json);
        }
        
        
        
        if(in_array("superuser",$accessPermissions)){
        	$superuser = true;
        }else{
        	$superuser = false;
        }
    }else{
    	// Set Status to 403 (Forbidden)
		$statusCode = 403;
		$authorized = false;
		$json = array("status" => 0, "error" => "API Access Denied! Please provide valid API credentials!");
		header('Content-type: application/json');
		echo json_encode($json);
    }
}else{
	// Set Status to 401 (Unauthorized)
	$statusCode = 401;
	$authorized = false;
	$json = array("status" => 0, "error" => "API Access Denied! You must provide valid API credentials in the apiUsername and apiPassword parameters.");
	header('Content-type: application/json');
	echo json_encode($json);
}

include_once('/var/www/html/kumpeapps.com/api/accessPermissions.php');
    
?>