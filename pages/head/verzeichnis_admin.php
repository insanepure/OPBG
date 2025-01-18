<?php
include_once 'classes/verzeichnis/verzeichnis.php';

if($player->GetArank() < 3)
{
    return;
}

if(!isset($_GET['id']))
{
    return;
}

$id = $_GET['id'];
if(isset($_GET['a']))
{
    $action = $_GET['a'];
    if($action == "verify_new")
    {
        $verzeichnis = new Verzeichnis($database);
        $verzeichnis->Activate($id);
        $message = "Der Verzeichniseintrag wurde verifiziert.";
    }
    if($action == "delete_new")
    {
        $verzeichnis = new Verzeichnis($database);
        $verzeichnis->DeleteNewEntry($id);
    }
    if($action == "verify_edit")
    {
        $verzeichnis = new Verzeichnis($database);
        $verzeichnis->VerifyEdit($id);
        $message = "Die Änderung des Verzeichniseintrags wurde erfolgreich bestätigt.";
    }
    if($action == "delete_edit")
    {
        $verzeichnis = new Verzeichnis($database);
        $verzeichnis->DeleteEdit($id);
        $message = "Die Änderung wurde verworfen.";
    }
}