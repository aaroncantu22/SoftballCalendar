<?php
$dsn = "mysql:host=localhost;dbname=calendar_db";
$dbusername = "root";
$dbpassword = " ";
$dbname = "calendar_db";

try{
   $pdo = NEW PDO($dsn,$dbusername,$dbpassword);
   $pdo ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
echo "Connection failed:".$e->getMessage();
}
