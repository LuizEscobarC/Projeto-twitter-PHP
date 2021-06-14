<?php
include '../header.php'; 
include './function_perfil.php';
deslogar();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://unpkg.com/feather-icons"></script>
    <link
      href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,700;1,400&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./css/style.css" />
    <title>Perfil</title>
  </head>
  <body>
<?php 

$infos = select_perfil($_GET['userid'], $db);
$followers_count = followers_count($_GET['userid'], $db);

// Imprime o perfil do usuário logado

print<<<_HTM
        <div class="container">
        <div class="wrapper">
          <div class="user">
            <div class="info">
              <div class="icon"></div>
              <h1>{$_GET['name']}</h1>
              <p>@{$_GET['name']} <hr><i>status:</i><small>({$infos[0]->status})<small></h3>
            </div>
            <div class="button">
              <i data-feather="edit"></i>
              <h1><a href="../publicacoes.php">FEED</a></h1>
            </div>
          </div>
          <div class="stats">
            <div class="projects">
              <p>Email</p>
              <h3>{$infos[0]->email}</h3>
            </div>
            <div class="projects">
              <p>Senha</p>
              <h3>{$infos[0]->password}</h3>
            </div>
            <div class="projects">
              <p>Seguidores</p>
              <h3>{$followers_count[0][0]}</h3>
            </div>
          </div>
        </div>
      </div>
      <script>
        feather.replace();
      </script>
    </body>
  </html>
_HTM;
                            //comentário
/* Em mente tenho mais idéias de implementação porém acabou meus 5 dias.
 Trabalhar, estudar e fazer esse desafio não foi fácil.
   Com essa página daria para mostrar o perfil de qualquer usuário, contanto
 que chegue por GET o id do usuário, ou seja, em publicações ou em explorar
 daria para colocar a funcionalidade em ao clique no nome redirecione com
 GET pra essa página perfil.php. Daria pra editar o perfil, entre outras
 funcionalidade 
*/
?>
