<?php
include_once("../header.php");
include_once("functions_explorar.php");
$userid = $_SESSION['userid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (strlen($_POST['body']) < 3000) {
        $body = substr($_POST['body'], 0, 2000);
        add_post($userid, htmlentities($body), $db);
    }

    header("Location: ./explorar.php");
} else {
    if ($_GET['other_user_id'] && $_GET['body_comment']) {
        if (strlen($_GET['body_comment']) < 1500) {
            $body = substr( htmlentities($_GET['body_comment']), 0, 1000 );
            $other_user_id = $_GET['other_user_id'];
            add_comment($userid, $body, $other_user_id, $_GET['post_id'], $db);
        }

        header("Location: ./explorar.php");
    }
    //$_SESSION['message'] = "Seu post foi adicionado!";
    header("Location: ./explorar.php");
}