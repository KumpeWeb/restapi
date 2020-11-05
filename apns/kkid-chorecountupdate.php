<?php

include_once('/var/www/html/kumpeapps.com/api/sqlConfig.php');
include_once('/var/www/html/kumpeapps.com/api/apns/kkid.php');

	$UserSQL = "
		SELECT 
    		userID,
    		masterID,
    		username
		FROM
    		Apps_KKid.User_Permissions
		WHERE 1=1
			AND isActive = 'Yes'
    		AND isBanned = 'No'
			AND username NOT LIKE 'Apps_%'
    		AND username NOT LIKE 'API_%';
	";  
	$UserQuery = mysqli_query($conn, $UserSQL) or die("Couldn't execute query. ". mysqli_error($conn)); 
	$Users = array();
	$UserSqlArray = mysqli_fetch_array($UserQuery)

	//Run script for each user
	while($User = $UserSqlArray)
    	$Users[] = $User;
	foreach($Users as $UserArray){ 
		//Set dbEXT for User
		$username = $UserArray['username'];
    	$sql2 = "
			SELECT 
    			COUNT(*) as Count
			FROM
    			Apps_KKid.Chores__Today
			WHERE 1=1
				AND kid = '$username'
        		AND Day != 'Weekly'
        		AND Status = 'todo';
		";

		$choreCountData = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
		$choreCountArray = mysqli_fetch_array($choreCountData);
		$choreCount = $choreCountArray['Count'];
	echo $username." ".$choreCount."      ";
		//work on this function
		kkidPushNotification($username,"Chores",NULL,NULL,intval($choreCount),"",NULL);
	}

?>