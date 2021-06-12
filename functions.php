<?php

function show_form($errors = array(), $input = array()) 
{
    include('index.php');

}

function validate_form_cadastro() 
{
    $errors = array();
    $input = array();
    //deve ter
    $input['username'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    if (strlen(trim($input['username'])) > 70 || ! (isset($input['username']))) {
        $errors[] = 'digite um nome válido';
    }
    $input['email'] = filter_input(INPUT_POST, 'name-2', FILTER_VALIDATE_EMAIL);
    if($input['email'] === false) {
        $errors[] = 'email inválido';
    }
    $input['password'] = $_POST['name-3'];
    if (strlen($input['password']) > 8) {
        $errors[] = 'Digite uma senha até 8 caracteres';
    }
    $input['new_password'] = $_POST['name-4'];
    if ($input['new_password'] != $input['password']) {
        $errors[] = 'Digite uma senha maior que 8 caracteres';
    } else {
        $input['password'] = $input['new_password'];
    }

    return array($errors, $input);
}

function validate_form_login() {
    $errors_login = array();
    $input_login = array();

    $input_login['email_login'] = filter_input(INPUT_POST, 'email_login', FILTER_VALIDATE_EMAIL);
    if(($input_login['email_login'] === false) || (is_null($input_login['email_login']))) {
        $errors_login[] = 'email inválido';
    }

    $input_login['password_login'] = $_POST['password_login'];
    if (strlen($input_login['password_login']) > 8 || is_null($input_login['password_login']) || 
               $input_login['password_login'] < 4) {
        $errors_login[] = 'Digite uma senha até 8 caracteres';
    }   
    return array($errors_login, $input_login);
}

function process_form($input = array(), $db) {
        //cadastra e volta ao começo
        if(array_key_exists('email', $input) && $input != 0){
            //nome, se caso houver uma consulta pelo nome 
            //e não seja sql injection
            $input_sanitaze['username'] = $db->quote($input['username']);
            $input_sanitaze['username'] = strtr($input_sanitaze['username'], array('_'=>'\_', '%' => '\%'));
            $input_sanitaze['email'] = $db->quote($input['email']);
            $input_sanitaze['password'] = $db->quote($input['password']);
            //cadastra
            $c = $db->exec("
                INSERT INTO users ( username, email, password )
                VALUES ( $input_sanitaze[username], 
                        $input_sanitaze[email], $input_sanitaze[password] )
            ");
            header('location: index.php');
        //loga na conta pela key de $input   
        } else if (array_key_exists('email_login', $input) && $input != 0) {  
            session_start();
            $stmt ="SELECT id FROM users WHERE email= :email AND password= :password";
            $resultado= $db->prepare($stmt);
             
            $resultado->bindValue(":email", $input['email_login']);
            $resultado->bindValue(":password",$input['password_login']);
            $resultado->execute();
            $input_db = $resultado->fetch(PDO::FETCH_OBJ); 
            if ( $input_db ){
                $_SESSION['userid'] = $input_db->id;
                 header('location: publicacoes.php');
            } else {
                header('location:index.php');
            }
        }

}
//adiciona um post
function add_post($userid,$body, $db ){
	$stmt = $db->prepare("insert into posts (user_id, body, stamp)
			              values ( ?, ?, now())");

	$result = $stmt->execute([$userid, $body]);
}
function add_comment($userid,$body, $other_user_id, $id_post, $db ){
  
    
	$stmt = $db->prepare("INSERT INTO comments(user_id, body_comment, other_user_id, id_comment, stamp)
			              values ( ?, ?, ?, ?, now())");

	$result = $stmt->execute([$userid, $body, $other_user_id, $id_post]);
}
//mostra as publicações
function show_posts($userid, $db){
    
    $array_user = array();
    $users_id = following($userid, $db);

    if (count($users_id)){
        $array_user = array_values($users_id);
    }else{
        $array_user = array();
    }
    $array_user[] = $_SESSION['userid'];


    $n = count($array_user);

    $placeholders = '?'. str_repeat(',?', $n - 1); //no exemplo a string gerada é ?,?,?
    //aqui eu recupero o post user id, corpo, e tempo
    $consulta = "SELECT id ,user_id, body, stamp FROM posts
    WHERE user_id IN ( $placeholders )  
    ORDER BY stamp DESC LIMIT 5";

    $stmt = $db->prepare($consulta);
    $stmt->execute($array_user);
  
	$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // aqui eu recupero ID e BODY de comments
    $q = $db->query("SELECT comments.id_comment, comments.body_comment, posts.user_id, comments.stamp, comments.user_id
                     FROM comments, posts 
                     WHERE comments.id_comment = posts.id ");
	  $comments = $q->fetchAll(PDO::FETCH_ASSOC);    
    
    //aqui faço um stmt para que todos os posts relacionados a o usuario atual seja imprimido
	return array($posts, $comments);

}
//imprime uduários para SEGUIR que não seja o próprio da sessão
function show_users($db){
	$users = array();
	$stmt = "SELECT id, username FROM users WHERE  id <> ? ORDER BY username";
	$result = $db->prepare($stmt);
    $result->execute(array($_SESSION['userid']));

	while ($data = $result->fetch(PDO::FETCH_OBJ)){
		$users[$data->id] = $data->username;
	}
	return $users;
}
//retorna quem o usuario esta seguindo
function following($userid, $db){
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
function check_count($first, $second,$db){
	$stmt = $db->prepare( "SELECT count(*) AS count FROM following
			               WHERE user_id= ? AND follower_id= ?");
	$stmt->execute(array($second, $first));

	$row = $stmt->fetch(PDO::FETCH_OBJ);
    
	return $row->count;

}
//verifica e segue o usuario $them
function follow_user($me,$them,$db){
	$count = check_count($me, $them, $db);

	if ($count == 0){
		$stmt = $db->prepare("INSERT INTO following (user_id, follower_id)
				values (? , ?)");

        $stmt->execute(array($them, $me));
	}
}

function unfollow_user($me,$them, $db){
	$count = check_count($me,$them, $db);

	if ($count != 0){
		$stmt = $db->prepare("DELETE FROM following
				WHERE user_id= ? and follower_id= ?
				limit 1");

        $stmt->execute(array($them, $me));

	}
}
// Essa função serve para imprimir o nome do usuario somente com o id
function select_username($userid, $db) {
    $stmt = $db->prepare("SELECT username FROM users
                          WHERE  id = ? LIMIT 1");
    $stmt->execute(array($userid));
     $id = $stmt->fetch(PDO::FETCH_OBJ);
    return $id->username; 
}

?>