<?php
function show_form($errors = array(), $input = array()) 
{
    header('location: index.php');
}

//adiciona um post
function add_post($userid,$body, $db )
{
    $stmt = $db->prepare("INSERT INTO posts (user_id, body, stamp)
                          VALUES ( ?, ?, now())");
    $result = $stmt->execute([$userid, $body]);
}

function add_comment($userid,
                     $body, 
                     $other_user_id, 
                     $id_post, 
                     $db
) {
    $stmt = $db->prepare("INSERT INTO comments(user_id, body_comment,
                                      other_user_id, id_comment, stamp)
                          VALUES ( ?, ?, ?, ?, now())");
    $result = $stmt->execute([$userid,
                              $body,
                              $other_user_id,
                              $id_post]
                            );
}

//mostra as publicações
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
    //Se fosse mostrar somente os posts de quem o usuario está seguindo
    $placeholders = '?'. str_repeat(',?', $n - 1); //no exemplo a string gerada é ?,?,?
    //aqui eu recupero o post user id, corpo, e tempo
    $consulta = "SELECT id ,user_id, body, stamp FROM posts  
                 ORDER BY stamp DESC ";
    $stmt = $db->prepare($consulta);
    $stmt->execute($array_user);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // aqui eu recupero ID e BODY de comments
    $q = $db->query("SELECT comments.id_comment, comments.body_comment,
                            posts.user_id, comments.stamp, comments.user_id
                     FROM comments, posts 
                     WHERE comments.id_comment = posts.id ");
    $comments = $q->fetchAll(PDO::FETCH_ASSOC);    
    
    return array($posts, $comments);

}
//imprime uduários para SEGUIR que não seja o próprio da sessão
function show_users($db)
{
    $users = array();
    $stmt = "SELECT DISTINCT users.id, users.username FROM users, following 
             WHERE  users.id = following.follower_id ORDER BY username";
    $result = $db->prepare($stmt);
    $result->execute();
    while ($data = $result->fetch(PDO::FETCH_OBJ)) {
      $users[$data->id] = $data->username;
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
    $stmt = $db->prepare( "SELECT count(*) AS count FROM following
                           WHERE user_id= ? AND follower_id= ?");
    $stmt->execute(array($second, $first));
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    
    return $row->count;
}
//verifica e segue o usuario $them
function follow_user($me,$them,$db)
{
    $count = check_count($me, $them, $db);

    if ($count == 0) {
        $stmt = $db->prepare("INSERT INTO following (user_id, follower_id)
                              VALUES (? , ?)");
        $stmt->execute(array($them, $me));
    }
}

function unfollow_user($me,$them, $db)
{
    $count = check_count($me,$them, $db);

    if ($count != 0) {
      $stmt = $db->prepare("DELETE FROM following
        WHERE user_id= ? and follower_id= ?");
      $stmt->execute(array($them, $me));
    }
}

// Essa função serve para imprimir o nome do usuario somente com o id
function select_username($userid, $db)
{
    $stmt = $db->prepare("SELECT username FROM users
      WHERE  id = ? LIMIT 10");
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
?>