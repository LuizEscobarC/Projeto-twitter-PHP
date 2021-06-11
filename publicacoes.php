<?php
session_start();
include_once('header.php');
include_once('functions.php');
//seta o username da seassão
$stmt=" SELECT username
        FROM users
        WHERE :id = users.id";
$resultado=$db->prepare($stmt);
             
$resultado->bindValue(":id", $_SESSION['userid']);

$resultado->execute();

$user_atual =  $resultado->fetch(PDO::FETCH_OBJ);
$_SESSION['username'] = $user_atual->username;     
?>

<!DOCTYPE html>
<!--  This site was created in Webflow. http://www.webflow.com  -->
<!--  Last Published: Wed Oct 16 2019 23:46:02 GMT+0000 (UTC)  -->
<html data-wf-page="5da786dd00b10d79c698bf04" data-wf-site="5da766d32783b3459dfbc795">
<head>
  <meta charset="utf-8">
  <title>Publicações</title>
  <meta content="Publicações" property="og:title">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta content="Webflow" name="generator">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/webflow.css" rel="stylesheet" type="text/css">
  <link href="css/desafio.webflow.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({  google: {    families: ["PT Sans:400,400italic,700,700italic","Ubuntu:300,300italic,400,400italic,500,500italic,700,700italic"]  }});</script>
  <!-- [if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" type="text/javascript"></script><![endif] -->
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">
  <link href="images/webclip.png" rel="apple-touch-icon">
</head>
<body>
  <div class="topo-publicacoes w-clearfix">
    <div class="div-perfil">
      <p class="nome-perfil"><?=$_SESSION['username']?></p>
      <a href="control.php" class="botao-seguir w-inline-block">
        <p class="seguir">LOG OUT</p>
      </a>
      <p>.</p>
      <?php 
          // imprime os usuários e se quer seguir ou não
          $users = show_users($db);
          $following = following($_SESSION['userid'], $db);

          foreach ($users as $key => $user) {
            print "<p class=\"nome-perfil-comentario\">". $user ."</p>";
            if (in_array($key, $following)){
              $_GET['id'] = $key;
              $_GET['do'] = 'follow';
                  print"<a nome=\"follow\" href=\"follow.php?id=$key&do=unfollow\" class=\"botao-seguir w-inline-block\">
                          <p class=\"seguir\"><small>Não seguir</small></p>
                        </a>";      
            } else {
              
                print"<a href=\"follow.php?id=$key&do=follow\" class=\"botao-seguir w-inline-block\">
                  <p class=\"seguir\"><small>Seguir</small></p>
                </a>";  
            }
          }
                                            
          ?>
    </div>
    <div class="div-feed">
      <div class="container-publicacoes">
        <div class="bloco-publicacao">
          <div class="w-form">
            <form id="email-form" name="email-form" data-name="Email Form" method="POST" action="add.php"><textarea placeholder="Texto da Publicação" maxlength="5000" id="field" name="body" class="texto-publicar w-input"></textarea><input type="submit" value="Publicar" data-wait="Please wait..." class="botao-publicar w-button"></form>
            <div class="w-form-done">
              <div>Thank you! Your submission has been received!</div>
            </div>
            <div class="w-form-fail">
              <div>Oops! Something went wrong while submitting the form.</div>
            </div>
          </div>
        </div>
        <p class="feed">Feed</p>
        <!-- fim do feed do usuario-->
        <?php
        list($posts, $comments) = show_posts($_SESSION['userid'], $db);
        if (count($posts)){
            foreach ($posts as $post) {
                $user_id_post = select_username($post['user_id'], $db);
                 $user_post_id = $post['id'];
                    print <<<_HTML_INIC
                  <div class="div-publicacao-feed">
                    <p class="texto-publicacao">
                      <b>{$user_id_post}</b> {$post['body']}</p>
                      <div class="div-comentario-existente">
                       <div class="div-publicacao-feed">             
                _HTML_INIC;    
                 foreach ($comments as $comment) { 
                   if ($comment['id_comment'] == $post['id']){
                     $comment_body = $comment['body_comment'];
                   } 
                    if(isset($comment_body)) {
                    print <<<HTM
                    class="nome-perfil-comentario">nome_comentador</p><p class="nome_comentador">{$comment_body}</p>
                  HTM;
                  }
                }
                 print <<<_HTML_FIM
                                  <div class="w-form">
                                  <form id="email-form-2" method="GET" action="add.php" name="email-form-2" data-name="Email Form 2" class="w-clearfix"><input type="hidden" name="other_user_id" value="$post[user_id]"><input type="hidden" name="post_id" value="$post[id]"> <textarea placeholder="..." maxlength="5000" id="field-2" name="body_comment" class="textarea w-input"></textarea><input type="submit" value="Comentar" data-wait="Please wait..." class="submit-button w-button"></form>
                                </div>
                              </div>
                            <p ><smal>{$post['stamp']}</smal></p>
                          </div>
                _HTML_FIM;
            } 
        }
        ?>
        <!-- fim do feed do usuario-->
  
<style>
 .w-webflow-badge {display: none !important;}
</style>
  </div>
  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.4.1.min.220afd743d.js" type="text/javascript" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="js/webflow.js" type="text/javascript"></script>
  <!-- [if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif] -->
</body>
</html>


