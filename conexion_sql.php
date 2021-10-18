<?php 

  function ConexionFarma_all($server,$bdname,$user,$pass){
    set_time_limit(0);
    $server_bd="sqlsrv:server=".$server.";Database=".$bdname;   
    try{
      $dbh = new PDO($server_bd, $user, $pass);
      return $dbh; 
    }catch(PDOException $e){
      // echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
      return false;
    }
  }
  
?>