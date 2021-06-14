<?php
if (! isset($_SESSION)) {
    $time = 2 * 60 * 60; // Defini 2 horas
    session_set_cookie_params($time); 
    session_start();
}

//Seta a conexão
$host = '127.0.0.1';
$dbname = 'twitter';
$user = 'root';
$pass = ''; 
$dsn = "mysql:dbname=$dbname;host=$host";

try {
    $db = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    print "Error: " . $e->getMessage();
}
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Ignorar
if ($tableExists = $db->query("SHOW TABLES LIKE 'users'")
                      ->rowCount() == 0
) {
    $db->exec("
    CREATE TABLE users (
    id              INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    username        VARCHAR( 255 ) NOT NULL ,
    email           VARCHAR( 255 ) NOT NULL ,
    password        VARCHAR( 8 ) NOT NULL ,
    status          ENUM( 'active', 'inactive' ) NOT NULL
    ) ENGINE = MYISAM ;
    ");
}
