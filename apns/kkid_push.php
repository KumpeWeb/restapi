<?php

include_once('/var/www/html/kumpeapps.com/api/apns/kkid.php');
include_once('/var/www/html/kumpeapps.com/api/sqlConfig.php');

$userID = $_REQUEST['userID'];
$Section = $_REQUEST['Section'];
$Title = $_REQUEST['Title'];
$Body = $_REQUEST['Body'];
$Badge = $_REQUEST['Badge'];
$Sound = $_REQUEST['Sound'];
$Action = $_REQUEST['Action'];
$nonce = $_REQUEST['nonce'];
$validNonce = NonceUtil::check(NONCE_SECRET, $nonce);

if($validNonce){
	kkidPushNotification($userID,$Section,$Title,$Body,$Badge,$Sound,$Action);
	error_log("nonce valid");
}else{
	error_log("nonce invalid");
}

?>