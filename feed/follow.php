<?php
session_start();
include_once("../header.php");
include_once("functions_explorar.php");

$id = $_GET['id'];
$do = $_GET['do'];

switch ($do) {
    case "follow":
        follow_user($_SESSION['userid'],$id, $db);
        //$msg = "You have followed a user!";
        break;
    case "unfollow":
        unfollow_user($_SESSION['userid'],$id, $db);
        //$msg = "You have unfollowed a user!";
        break;
}
//$_SESSION['message'] = $msg;
// futuramente imprimir com um framework uma caixa na tela dizendo q esta seguindo 
header("Location: explorar.php");

