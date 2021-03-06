<?php
include_once('../header.php');
include_once('functions_explorar.php');  
deslogar();  
?>

<!DOCTYPE html>
<!--  This site was created in Webflow. http://www.webflow.com  -->
<!--  Last Published: Wed Oct 16 2019 23:46:02 GMT+0000 (UTC)  -->
<html data-wf-page="5da786dd00b10d79c698bf04" data-wf-site="5da766d32783b3459dfbc795">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="utf-8">
  <title>Explorar</title>
  <meta content="Publicações" property="og:title">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta content="Webflow" name="generator">
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
  <link href="../css/normalize.css" rel="stylesheet" type="text/css">
  <link href="../css/webflow.css" rel="stylesheet" type="text/css">
  <link href="../css/style.css" rel="stylesheet" type="text/css">
  <link href="../css/desafio.webflow.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({  google: {    families: ["PT Sans:400,400italic,700,700italic","Ubuntu:300,300italic,400,400italic,500,500italic,700,700italic"]  }});</script>
  <!-- [if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" type="text/javascript"></script><![endif] -->
    <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
    <link href="../images/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link href="../images/webclip.png" rel="apple-touch-icon">
  </head>
  <body>
    <nav>
      <div class="nav-wrapper #1565c0 blue darken-3">
              <ul class="side-nav" id="menu-mobile">
                  <?php 
                  // Envia o id e usuario da sessão por GET
                  print"<li><a href=\"../perfil/perfil.php?userid=$_SESSION[userid]&name=$_SESSION[username]\">Perfil</a></li>";
                  ?>
                  <li><a href="../publicacoes.php">Publicações</a></li>
                  <li><a href="#">Explorar</a></li>
              </ul>
          </div>
      </nav>
    <div class="topo-publicacoes w-clearfix">
     <!-- Essa página é igual a publicações com algumas mudanças e com algumas mudanças:
                          1 - Imprime todo mundo para seguir ou não.
                          2 - Não tem a função DESLOGAR.
                          3 - Não tem limite de Posts.
     -->       
      <div class="div-perfil">
        <p class="nome-perfil"><?=$_SESSION['username']?></p>  
        <a href="index.php" class="botao-seguir w-inline-block">
          <p class="seguir">Sair</p>
        </a>  
        
        <?php 
        // Imprime os usuários e se quer seguir ou não
        $users = show_users($db);
        $following = following($_SESSION['userid'], $db);

        foreach ($users as $key => $user) {
            print "<p class=\" p-font\">@". $user ."</p>";
            if (in_array($key, $following)) {
                $_GET['id'] = $key;
                $_GET['do'] = 'follow';
                print"<a nome=\"./follow\" href=\"follow.php?id=$key&do=unfollow\" class=\"botao-seguir-2 w-inline-block\">
                <p class=\"seguir\"><small>Deixar de seguir</small></p>
                </a>";      
          } else {
              print"<a href=\"./follow.php?id=$key&do=follow\" class=\"botao-seguir w-inline-block\">
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
              <form id="email-form" name="email-form" data-name="Email Form" method="POST" action="./add.php"><textarea placeholder="Texto da Publicação" maxlength="5000" id="field" name="body" class="texto-publicar w-input"></textarea><input type="submit" value="Publicar" data-wait="Please wait..." class="botao-publicar w-button"></form>
            </div>
          </div>
          <!-- fim do feed do usuario-->
          <?php
          list($posts, $comments) = show_posts($_SESSION['userid'], $db);
          if (count($posts)) {
              foreach ($posts as $post) {
                  $user_id_post = select_username($post['user_id'], $db);
                  $user_post_id = $post['id'];
                  print "<div class=\"div-publicacao-feed\">\n";
                  print  "<p class=\"texto-publicacao\"><b>@{$user_id_post}: </b>   {$post['body']}</p>\n";
                  print "<div class=\"div-comentario-existente\">\n";              
                 //imprime comentários
                  foreach ($comments as $comment) { 
                      $post_id = $post['id'];
                      if ($comment['id_comment'] == $post_id){
                          $name_comment = select_username($comment['user_id'],$db);
                          $comment_body = $comment['body_comment'];
                          // verifica se comment esta setado e retorna
                          if (isset($comment_body) && $post['id'] =
                                       $comment['id_comment']
                          ) {
                              print "<p class=\"nome-perfil-comentario\">@{$name_comment}</p>\n";
                              print "<small class=\"comment-stamp\">{$comment['stamp']}</small>\n";
                              print "<p class=\"nome_comentador\">{$comment_body}</p>\n";
                            }
                      }
                  }
                  print  "<div class=\"w-form\">\n";
                  print  "<form id=\"email-form-2\" method=\"GET\" action=\"add.php\" name=\"email-form-2\" data-name=\"Email Form 2\" class=\"w-clearfix\"><input type=\"hidden\" name=\"other_user_id\" value=\"$post[user_id]\"><input type=\"hidden\" name=\"post_id\" value=\"$post[id]\"> <textarea placeholder=\"...\" maxlength=\"5000\" id=\"field-2\" name=\"body_comment\" class=\"textarea w-input\"></textarea><input type=\"submit\" value=\"Comentar\" data-wait=\"Please wait...\" class=\"submit-button w-button\"></form>\n";
                  print "</div>\n";
                  print "<p><smal>{$post['stamp']}</small></p>\n";
                  print "</div>\n";
                  print "</div>\n";
              }         
          }
         ?>
         <!-- fim do feed do usuario-->
         <style>
         .w-webflow-badge {display: none !important;}
       </style>
     </div>
  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.4.1.min.220afd743d.js" type="text/javascript" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="../js/webflow.js" type="text/javascript"></script>
  <!-- [if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif] -->
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js"></script>
  <script>
    $(function(){
        $(".button-collapse").sideNav();
    });
    </script>
 </body>
</html>


