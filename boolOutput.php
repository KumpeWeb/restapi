<?php

function boolOutputFunc($bool){
	if(isset($_REQUEST['boolAsInt']) && $_REQUEST['boolAsInt'] == "true"){
		return $bool;
	}else{
		return (boolval($bool) ? 'true' : 'false');
	}
}

$boolOutput = "boolOutputFunc";

?>