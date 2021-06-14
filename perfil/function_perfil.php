<?php
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
    $stmt = $db->query("SELECT * FROM users 
                        WHERE id = $userid");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_OBJ);  
    return $data;
}
function followers_count($userid, $db) 
{
    $stmt = $db->query("SELECT count(follower_id) FROM following 
                        WHERE user_id = $userid");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_NUM);  
    return $data;
}
?>