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
    			 (SELECT 
    				COUNT(*) as Count
				FROM
    				Apps_KKid.Chores__Today
				WHERE 1=1
					AND kid = Apps_KKid.User_Permissions.username
        			AND Day != 'Weekly'
        			AND Status = 'todo') AS choreCount
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
	//Run script for each user
	while($User = mysqli_fetch_array($UserQuery1))
    	$Users[] = $User;
	foreach($Users as $UserArray){ 
		//Set dbEXT for User
		$username = $UserArray['username'];
		$choreCount = $UserArray['choreCount'];
		//work on this function
		if(intval($choreCount) > 0){
			kkidPushNotificationAsync($username,"Chores-Reminders","Chore Reminder","You have ".intval($choreCount)." chores left for today. Do not forget to complete and check off your chores today!!!",intval($choreCount),"default",NULL);
		}
	}

?>