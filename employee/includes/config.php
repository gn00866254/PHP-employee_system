<?php
ob_start();
session_start();

date_default_timezone_set("Asia/Tokyo");

try {
    //接続を確立。
    $connection = new PDO("mysql:dbname=employee;charset=utf8;host=localhost","root","");
    $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
}
catch (PDOException $error){
    echo "Connetion failed: ".$error->getMessage();
    exit;
}
?>