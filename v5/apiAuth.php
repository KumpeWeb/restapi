<?php

require_once '/var/www/html/kumpeapps.com/api/caseConverter.php';
require_once '/var/www/html/kumpeapps.com/api/boolOutput.php';
require_once '/var/www/html/kumpeapps.com/api/GoogleAuthenticator.php';
$ga = new GoogleAuthenticator();

// Set Status to 400 (Bad Request)
$statusCode = 400;
$authorized = false;

include_once('/var/www/html/kumpeapps.com/api/sqlConfig.php');

if (!isset($_SERVER['HTTP_AUTHUSERNAME']) && isset($_REQUEST['keyinbody']) && $_REQUEST['keyinbody'] == 1) {
	$json = file_get_contents('php://input');
	$getjson = json_decode($json);
	$bodyAppKey = $getjson->appKey;
	$bodyAuthKey = $getjson->authKey;
} else {
	$bodyAppKey = "";
	$bodyAuthKey = "none";
}

$appKey = isset($_SERVER['HTTP_APPKEY']) ? mysqli_real_escape_string($conn, $_SERVER['HTTP_APPKEY']) :  $bodyAppKey;
$apiKey = isset($_SERVER['HTTP_AUTHKEY']) ? mysqli_real_escape_string($conn, $_SERVER['HTTP_AUTHKEY']) :  $bodyAuthKey;

if (isset($_SERVER['HTTP_APPKEY']) || (isset($_REQUEST['keyinbody']) && $_REQUEST['keyinbody'] == 1)) {

	$AccessData = "
		SELECT 
    		*
		FROM
    		Core_RESTAPI.vw_Users
		WHERE 1=1
			AND appKey = '$appKey'
			AND blocked = 0;
	"; 
  		$AccessQuery = mysqli_query($conn, $AccessData) or die("Couldn't execute query. ". mysqli_error($conn)); 
  		$AccessFetch = mysqli_fetch_array($AccessQuery); 
  		$getMasterId = $AccessFetch['masterID'];
  		$getUserId = $AccessFetch['userID'];
  		$getUsername = $AccessFetch['username'];
  		$getAppKey = $AccessFetch['appKey'];
  		$getCompromised = $AccessFetch['compromised'];
  		
    if ($appKey == $getAppKey){
    	$authorized = true;
    	$accessPermissions = [];
    	//Set Access Permissions
            $accesssql = "
            	SELECT 
    				groupName
				FROM
    				Core_RESTAPI.vw_User_GroupName_Relationship
				WHERE
    				1 = 1
    				AND username = '$getUsername';";
					
							
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
        
        if($getCompromised == 1){
        	// Set Status to 423(Locked)
        	$statusCode = 423;
        	$authorized = false;
			$json = array("status" => 0, "error" => "This App Key has been compromised. Please revoke the key and request a new one.");
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
		$json = array("status" => 0, "error" => "API Access Denied! Please provide valid API appKey!");
		header('Content-type: application/json');
		echo json_encode($json);
    }
}else{
	// Set Status to 401 (Unauthorized)
	$statusCode = 401;
	$authorized = false;
	$json = array("status" => 0, "error" => "API Access Denied! You must provide valid API appKey.");
	header('Content-type: application/json');
	echo json_encode($json);
}

include_once('/var/www/html/kumpeapps.com/api/accessPermissions.php');
    
?>