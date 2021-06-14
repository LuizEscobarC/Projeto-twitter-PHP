<?php
include_once("header.php");
include_once("functions.php");
$userid = $_SESSION['userid'];
/* Arquivo separado que realiza a lógica de armazenamento
    entre comentários e publicações
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (strlen($_POST['body']) < 3000) {
        $body = substr(htmlentities($_POST['body']), 0, 2000);
        add_post($userid, $body, $db);
    }

  header("Location: publicacoes.php");
} else {
    if ($_GET['other_user_id'] && $_GET['body_comment']) {
        if (strlen($_GET['body_comment']) < 2000) {
            $body = substr( $_GET['body_comment'], 0, 1500 );
            $other_user_id = $_GET['other_user_id'];
            add_comment($userid, $body, $other_user_id, $_GET['post_id'], $db);
        }
        header("Location: publicacoes.php");
     }
  //$_SESSION['message'] = "Seu post foi adicionado!";
  header("Location: publicacoes.php");
}