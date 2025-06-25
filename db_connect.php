<?php 
$dsn = "mysql:host=localhost;dbname=tms_database;charest=UTF8";
$username = "root";
$password = "200446";
try{
$conn = new PDO($dsn, $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
}
catch(PDOException $e){
    die('Connection Failed :'.$e->getMessage());
}
