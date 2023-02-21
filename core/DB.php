<?php
class DB {
 function connect($db)
 {
 try {
 // this is a constructor connecting to database using PDO and $db array defined in the config.php
 $conn = new PDO("mysql:host={$db['host']};dbname=bloging", $db['username'], $db['password']);
 // set the PDO error mode to exception
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 return $conn;
 } catch (PDOException $exception) {
 exit($exception->getMessage());
 }
 }
}