<?php
define('DB_SERVER','localhost');
define('DB_USER','group4');
define('DB_PASS' ,'moh76med');
define('DB_NAME', 'id_control');
$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
// Check connection
if (mysqli_connect_errno())
{
 echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>
