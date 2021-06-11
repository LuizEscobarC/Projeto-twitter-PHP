<?php
include_once("header.php");
include_once("functions.php");
session_start();
$userid = $_SESSION['userid'];
if($_SERVER['REQUEST_METHOD'] == 'POST') {
$body = substr($_POST['body'], 0, 140);

add_post($userid, $body, $db);

header("Location:publicacoes.php");
} else {

if ($_GET['other_user_id'] && $_GET['body_comment'])
{
    $body = substr( $_GET['body_comment'], 0, 140 );
    $other_user_id = $_GET['other_user_id'];
<<<<<<< HEAD
    add_comment($userid, $body, $other_user_id, $db);
=======
    add_comment($userid, $body, $other_user_id, $_GET['post_id'], $db);
>>>>>>> master
    header("Location:publicacoes.php");
}

$_SESSION['message'] = "Seu post foi adicionado!";



}
?>