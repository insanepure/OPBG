<?php

    if(isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['order']) && is_numeric($_POST['order']))
    {
        include_once 'serverurl.php';
        include_once $_SERVER['DOCUMENT_ROOT'] . '../../classes/header.php';

$db = 'DATENBANK';
$user = 'USER';
$pw = 'PASSWORT';
        $database = new Database($db, $user, $pw);
        $database->Update('reihenfolge='.$_POST['order'], 'inventory', 'id='.$_POST['id']);
    }
    else
    {
        header('Location: ?p=news');
        exit();
    }