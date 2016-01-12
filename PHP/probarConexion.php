<?php

$strconn = $_REQUEST['strconn'];
$conn = pg_connect($strconn);

if($conn){
    $a = array("status"=>"ok");
}
else{
    $a = array("status"=>"error");
}


print_r(json_encode($a));