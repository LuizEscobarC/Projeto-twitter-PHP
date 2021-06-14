<?php
include_once('header.php');
include_once('functions.php');
//lógica da página control
// - Se o formulário for enviado, válida e então processa ou reexibe
// - Se ele não for enviado é exibido 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Se POST for de Cadastro de Usúarios
    if ( array_key_exists('name' ,$_POST) ) {
        list($errors, $input) = validate_form_cadastro();
        if ($errors) {
            show_form($errors, $input);
        } else {
            process_form($input, $db);
        }
    // Se POST for de Login     
    } else if (array_key_exists('email_login', $_POST)
            && array_key_exists('password_login',$_POST)
    )  {
        list($errors, $input) = validate_form_login($db);
        if ($errors) {
            show_form($errors, $input);
        } else {
            process_form($input, $db);
        }
    }
} else {
    show_form();
    session_destroy();
}