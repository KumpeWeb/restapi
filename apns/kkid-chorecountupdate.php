<?php

include_once('/var/www/html/kumpeapps.com/api/sqlConfig.php');
include_once('/var/www/html/kumpeapps.com/api/apns/kkid.php');

$conn2 = mysqli_connect($sqlHost, $sqlUser, $sqlPass);
		mysqli_select_db($conn2, "Apps_KKid");
		
	$UserSQL1 = "
			SELECT 
    			userID,
    			masterID,
    			username,
    			9 AS choreCount
			FROM
    			Apps_KKid.User_Permissions
			WHERE 1=1
				AND isActive = 'Yes'
    			AND isBanned = 'No'
				AND username NOT LIKE 'Apps_%'
    			AND username NOT LIKE 'API_%';
		";
	$UserQuery1 = mysqli_query($conn2, $UserSQL1) or die("Couldn't execute query. ". mysqli_error($conn2)); 
	$Users = array();
echo $UserSQL1;
	//Run script for each user
	while($User = mysqli_fetch_array($UserQuery1))
    	$Users[] = $User;
	foreach($Users as $UserArray){ 
		//Set dbEXT for User
		$username = $UserArray['username'];
		$choreCount = $UserArray['choreCount'];
	
		//work on this function
		kkidPushNotification($username,"Chores",NULL,NULL,intval($choreCount),"",NULL);
	}

?>