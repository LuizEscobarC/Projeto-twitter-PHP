<?php
    include('../header.php');
    include('Email.php');
    // Executa os pre requisitos para enviar um email com PHPMailer
    // Se o email for enviado e o usuario clicar no link será redirecionado para
    // redefinir
    if(isset($_POST['esqueciasenha'])){
        $token = uniqid();
    
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['token'] = $token;

        $sql = $db->prepare("SELECT * FROM users
                         WHERE email = ?");
        $sql->execute([$_SESSION['email']]);

        if ($sql->rowCount() == 1) {
            $info = $sql->fetch();
            // Minha hopedagem gratiuta não oferece disparador de emails :/
            $mail = new Email('localhost','test',
                              'testando123','ReplicaTwitter');
            $mail->enviarPara($_POST['email'],
                              $info['username']);

            $url = 'challengelikett.000webhostapp.com/users/redefinir.php';
            $corpo = 'Olá '.$info['username'].', <br>
            Foi solicitada uma redefinição da sua senha na ReplicaTwitter.com. Acesse o link 
            abaixo para redefinir sua senha.<br>
            <h3><a href="'.$url.'?token='.$_SESSION['token'].'">Redefinir a sua senha</a></h3> 
            <br>            
            Caso você não tenha solicitado essa redefinição, ignore esta mensagem.<br>
            Qualquer problema ou dúvida entre em contato pelo email contato@contato.com';

            $informacoes = ['Assunto'=>'Redefinição de senha', 'Corpo'=>$corpo];           
            $mail->formatarEmail($informacoes);
           
            if ($mail->enviarEmail()) {
                $data['sucesso'] = true;
                print '<script>alert(Enviado com sucesso!)</script>';
            } else {
                $data['erro'] = true;
            }
        } else {
            die('Não encontramos esse <b>email</b> em nossa base de dados.');
        }
    }
?>