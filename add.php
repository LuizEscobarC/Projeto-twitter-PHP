<?php
include_once("header.php");
include_once("functions.php");
session_start();

$userid = $_SESSION['userid'];
$body = substr($_POST['body'],0,140);

add_post($userid, $body, $db);
$_SESSION['message'] = "Seu post foi adicionado!";
 header("Location:publicacoes.php");

?>