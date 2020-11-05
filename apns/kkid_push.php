<?php

include_once('/var/www/html/kumpeapps.com/api/apns/kkid.php');
require_once('/var/www/html/kumpeapps.com/api/nonce.php');

$userID = $_POST['userID'];
$Section = $_POST['Section'];
$Title = $_POST['Title'];
$Body = $_POST['Body'];
$Badge = $_POST['Badge'];
$Sound = $_POST['Sound'];
$Action = $_POST['Action'];
$nonce = $_POST['nonce'];
$validNonce = 

kkidPushNotification($userID,$Section,$Title,$Body,$Badge,$Sound,$Action);

?>