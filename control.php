<?php
include_once('header.php');
include_once('functions.php');
//lógica da página (controlador)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ( array_key_exists('name' ,$_POST) ) {
        list($errors, $input) = validate_form_cadastro();
        if ($errors) {
            show_form($errors, $input);
        } else {
            process_form($input, $db);
        }
    } else if (array_key_exists('email_login', $_POST) && array_key_exists('password_login',$_POST)){
       list($errors, $input) = validate_form_login();
       if ($errors) {
           show_form($errors, $input);
       } else {
           process_form($input, $db);
       }
   }
} else {
    show_form();
}