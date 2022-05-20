<?php
set_time_limit(0);
error_reporting(0);
 $dbh=mysqli_connect("p:10.10.1.11","dhuarina","B0l1v14.@1202","financierocobofar_100");
//$dbh=mysqli_connect("p:10.10.1.19","Cobofar77","Cobofar1","financiero_cobofar_200");
if (mysqli_connect_errno())
{
  echo "Error en la conexión: " . mysqli_connect_error();
}
mysqli_set_charset($dbh,"utf8");


if (!function_exists('mysqli_result')) {
    function mysqli_result($result, $number, $field=0) {
        mysqli_data_seek($result, $number);
        $row = mysqli_fetch_array($result);
        return $row[$field];
    }
}
?>
