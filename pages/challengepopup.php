<?php

    if(isset($_POST['id']) && is_numeric($_POST['id']))
    {
        include_once $_SERVER['DOCUMENT_ROOT'].'classes/serverurl.php';
        include_once $_SERVER['DOCUMENT_ROOT'] . '../../classes/header.php';

$db = 'DATENBANK';
$user = 'USER';
$pw = 'PASSWORT';
        $database = new Database($db, $user, $pw);
        $database->Update('challengedpopup=1, impeldownpopup=0', 'accounts', 'id='.$_POST['id']);
    }
    else
    {
        header('Location: ?p=news');
        exit();
    }