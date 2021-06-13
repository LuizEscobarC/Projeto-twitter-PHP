<?php
include_once("../header.php");
include_once("functions_explorar.php");
session_start();
$userid = $_SESSION['userid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = substr($_POST['body'], 0, 140);
    add_post($userid, htmlentities($body), $db);

    header("Location: ../publicacoes.php");
} else {
    if ($_GET['other_user_id'] && $_GET['body_comment']) {
        $body = substr( htmlentities($_GET['body_comment']), 0, 140 );
        $other_user_id = $_GET['other_user_id'];
        add_comment($userid, $body, $other_user_id, $_GET['post_id'], $db);

        header("Location: ../publicacoes.php");
  }
    $_SESSION['message'] = "Seu post foi adicionado!";
}