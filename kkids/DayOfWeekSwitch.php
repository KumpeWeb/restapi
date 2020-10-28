<?php

function dayAsIntFunc($day){
	switch($day) {
case "Sunday":
return 1;
case "Monday":
return 2;
case "Tuesday":
return 3;
case "Wednesday":
return 4;
case "Thursday":
return 5;
case "Friday":
return 6;
case "Saturday":
return 7;
case "Weekly":
return 8;
default:
echo 8;
}
}

$dayAsInt = "dayAsIntFunc";

?>