<?php
// Retorna para o Login_Cadastro com erros e inputs que por ventura se algum ser válido,
// "poderia" ser colocado novamente no formulário
function show_form($errors = array(), $input = array()) 
{
    include('Login_Cadastro.php');
}
/*
*/
function validate_form_cadastro() 
{
    $errors = array();
    $input = array();

    $input['username'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    if (strlen(trim($input['username'])) > 70 || ! (isset($input['username']))) {
        $errors[] = 'digite um nome válido';
    }
    $input['email'] = filter_input(INPUT_POST, 'name-2', FILTER_VALIDATE_EMAIL);
    if ($input['email'] === false) {
        $errors[] = 'email inválido';
    }
    $input['password'] = $_POST['name-3'];
    // Limitei até caracteres para ficar mais simples
    if (strlen($input['password']) > 8 || strlen($input['password']) < 4) {
        $errors[] = 'Digite uma senha entre 5 e 8 caracteres';
    }
    $input['new_password'] = $_POST['name-4'];
    if ($input['new_password'] != $input['password'] || strlen($input['new_password']) < 4 || 
        strlen($input['new_password']) > 8) {
        $errors[] = 'Digite uma senha entre 5 e 8 caracteres';
    } else {
        $input['password'] = $input['new_password'];
    }
  // forma a string de erros
  if ($errors) {
  $errors = "<li>" . implode (' </li><li> ', $errors );
  }  
  return array($errors, $input);
}

function validate_form_login()
{
    $errors_login = array();
    $input_login = array();

    $input_login['email_login'] = filter_input(INPUT_POST, 'email_login', FILTER_VALIDATE_EMAIL);
    if (($input_login['email_login'] === false) || (is_null($input_login['email_login']))) {
        $errors_login[] = 'email inválido';
    }

    $input_login['password_login'] = $_POST['password_login'];
    if (strlen($input_login['password_login']) > 8 || is_null($input_login['password_login']) || 
        strlen($input_login['password_login']) < 5) {
        $errors_login[] = 'Senha inválida';
    }   
    //forma uma lista de erros
    if ($errors_login) {
        $errors_login = "<li>" . implode (' </li><li> ', $errors_login );
    }

return array($errors_login, $input_login);
}

function process_form($input = array(), $db)
{
    //cadastra e volta ao começo ou loga
    if (array_key_exists('email', $input) && $input != 0) {
        // Nome, se caso houver uma consulta pelo nome 
        $input_sanitaze['username'] = $db->quote($input['username']);
        // Anti sql injection
        $input_sanitaze['username'] = strtr($input_sanitaze['username'],
                                      array('_'=>'\_', '%' => '\%'));
        $input_sanitaze['email'] = $db->quote($input['email']);
        $input_sanitaze['password'] = $db->quote($input['password']);
        $c = $db->exec("
                        INSERT INTO users ( username, email, password )
                        VALUES ( $input_sanitaze[username], 
                        $input_sanitaze[email], $input_sanitaze[password] )
                       ");
         header('location: Login_Cadastro.php');
    // Id é colocado na sessão userid e é logado se id estiver setado   
  } else if (array_key_exists('email_login', $input) && $input != 0) {  
      $stmt ="SELECT id FROM users WHERE email= :email 
              AND password= :password";
      $resultado= $db->prepare($stmt);
    
      $resultado->bindValue(":email", $input['email_login']);
      $resultado->bindValue(":password",$input['password_login']);
      $resultado->execute();
      $input_db = $resultado->fetch(PDO::FETCH_OBJ); 
      $_SESSION['userid'] = $input_db->id;
      if ( $input_db ) {
          header('location: publicacoes.php');
      } else {
          header('location: Login_Cadastro.php');
      }
    }
}

// adiciona um post
function add_post($userid,$body, $db)
{
    $stmt = $db->prepare("INSERT INTO posts (user_id, body, stamp)
                          VALUES ( ?, ?, NOW())");
    $result = $stmt->execute([$userid, $body]);
}

function add_comment($userid,$body, $other_user_id, $id_post, $db)
{
    $stmt = $db->prepare("INSERT INTO comments(user_id, body_comment, other_user_id,
                                      id_comment, stamp) VALUES ( ?, ?, ?, ?, NOW())");
    $result = $stmt->execute([$userid, $body, $other_user_id, $id_post]);
}

// Imprime as publicações
function show_posts($userid, $db)
{
    $array_user = array();
    $users_id = following($userid, $db);

    if (count($users_id)) {
        $array_user = array_values($users_id);
    } else {
        $array_user = array();
    }
    $array_user[] = $_SESSION['userid'];
    $n = count($array_user);

    $placeholders = '?'. str_repeat(',?', $n - 1); 
    // No exemplo a string gerada é ?,?,?
    // aqui eu recupero o post user id, corpo, e tempo
    // Com essa função dava pra criar mais um funcionalidade onde se aumenta
    // o limite de posts e atualiza se certo botão for clicado (carregar mais), igual ao antigo
    // feed do Facebook que hoje é automático
    $consulta = "SELECT id ,user_id, body, stamp FROM posts
    WHERE user_id IN ( $placeholders )  
    ORDER BY stamp DESC LIMIT 5";

    $stmt = $db->prepare($consulta);
    $stmt->execute($array_user);
    
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // recupera ID e BODY de comments
    $q = $db->query("SELECT comments.id_comment, comments.body_comment, 
                            posts.user_id, comments.stamp, comments.user_id
                     FROM comments, posts 
                     WHERE comments.id_comment = posts.id ");
    $comments = $q->fetchAll(PDO::FETCH_ASSOC);    
    
    //aqui faço um stmt para que todos os posts relacionados a o usuario atual seja imprimido
    return array($posts, $comments);

}
//imprime uduários Seguidos que não seja o próprio da sessão
function show_users($db)
{
    $users = array();
    $stmt = "SELECT DISTINCT users.id, users.username FROM users, 
             following WHERE  users.id = following.follower_id 
             ORDER BY username";
    $result = $db->prepare($stmt);
    $result->execute();

    while ($data = $result->fetch(PDO::FETCH_OBJ)) {
       if ($data->id != $_SESSION['userid']) { 
           $users[$data->id] = $data->username;
       }
    }
    return $users;
}
//retorna quem o usuario esta seguindo
function following($userid, $db)
{
    $users = array();
    $stmt = $db->prepare("SELECT DISTINCT user_id FROM following
                          WHERE follower_id = ?");
    $stmt->execute(array($userid));

    while($data = $stmt->fetch(PDO::FETCH_OBJ)){
      array_push($users, $data->user_id);
    }

    return $users;
}
//checa se já segue ou não e retorna a contagem $first = me, $second  = them
function check_count($first, $second,$db)
{
    $stmt = $db->prepare("SELECT count(*) 
                          AS count FROM following
                          WHERE user_id= ? AND follower_id= ?");
    $stmt->execute(array($second, $first));

    $row = $stmt->fetch(PDO::FETCH_OBJ);
    return $row->count;

}
//verifica e segue o usuario $them
function unfollow_user($me,$them, $db)
{
      $count = check_count($me,$them, $db);

    if ($count != 0) {
        $stmt = $db->prepare("DELETE FROM following
                              WHERE user_id= ? and follower_id= ?
                              limit 1");
        $stmt->execute(array($them, $me));
    }
}

// Essa função serve para imprimir o nome do usuario somente com o id
function select_username($userid, $db)
{
      $stmt = $db->prepare("SELECT username FROM users
                            WHERE  id = ? LIMIT 1");
      $stmt->execute(array($userid));
      $id = $stmt->fetch(PDO::FETCH_OBJ);
      return $id->username; 
  }
function select_perfil ($userid, $db)
{
    $stmt = $db->query("SELECT FROM users 
                        WHERE id = $userid");
    $stmt->exec();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
function deslogar()
{
    if ( ! array_key_exists('userid', $_SESSION)) {
        header('location: index.php');
    }
}
?>