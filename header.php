<?php
$host = 'localhost';
$dbname = 'id17032125_twitter';
$user = 'id17032125_desafio';
$pass = '0o)$)W)8(&vU*I@r';
$dsn = "mysql:dbname=$dbname;host=$host";

try {
    $db = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    print "Error: " . $e->getMessage();
}
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 

