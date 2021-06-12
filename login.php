<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include_once('header.php');
include_once('functions.php');

$_SESSION['userid'] = 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Microblogging Application</title>
</head>
<body>

<?php
if (isset($_SESSION['message'])){
	echo "<b>". $_SESSION['message']."</b>";
	unset($_SESSION['message']);
}
?>
<form method='post' action='add.php'>
<p>Your status:</p>
<textarea name='body' rows='5' cols='40' wrap=VIRTUAL></textarea>
<p><input type='submit' value='submit'/></p>
</form>

</body>
</html>


<?php
$posts = show_posts($_SESSION['userid'], $db);
foreach ($posts as $post) {
    echo $row['firstname'];
    echo $row['lastname'];
        print <<<HTML
            <p class="feed">Feed</p>
                <div class="div-publicacao-feed">
                <p class="texto-publicacao">{$row['body']}</p>
                <div class="div-comentario-existente">
                    <p class="nome-perfil-comentario">Joana</p>
                    <p class="comentario">Oi</p>
                    <div class="w-form">
                    <form id="email-form-2" name="email-form-2" data-name="Email Form 2" class="w-clearfix"><textarea placeholder="..." maxlength="5000" id="field-2" name="field-2" class="textarea w-input"></textarea><input type="submit" value="Comentar" data-wait="Please wait..." class="submit-button w-button"></form>
                    <div class="w-form-done">
                        <div>Thank you! Your submission has been received!</div>
                    </div>
                    <div class="w-form-fail">
                        <div>Oops! Something went wrong while submitting the form.</div>
                    </div>
                    </div>
                </div>
                </div>
        HTML;
}


$stmt = $db->prepare('SELECT * FROM noticias WHERE noticias.titulo LIKE :buscarNoticiaTitulo');
$sqlNoticias->execute(array("buscarNoticiaTitulo" => '%'. $buscarNoticiaTitulo .'%'));
?>