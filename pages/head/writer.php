<?php
    if($player->GetArank() < 2)
    {
        header('Location: ?p=news');
        exit();
    }
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/writer/writer.php';

    if(isset($_GET['a']) && $_GET['a'] == 'delete' && isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $id = $_GET['id'];
        if($player->GetArank() < 3)
        {
            $message = "Du darfst den Post nicht löschen";
        }
        else
        {
            $result = $database->Delete('writer', 'id='.$id.'', 1);
            $message = "Der Eintrag wurde gelöscht";
        }
    }
    else if($_GET['a'] == 'create')
    {
        if(!isset($_POST['titel']))
        {
            $message = "Der Titel ist ungültig!";
        }
        else if(!isset($_POST['mode']))
        {
            $message = 'Der Mode ist ungültig.';
        }
        else if(!isset($_POST['bild']) || (!strpos($_POST['bild'], '.png') && !strpos($_POST['bild'], '.jpg') && !strpos($_POST['bild'], '.jpeg')))
        {
            $message = 'Die Bild-URL ist ungültig.';
        }
        else if(!isset($_POST['datum']))
        {
            $message = 'Das Datum ist ungültig.';
        }
        else if(!isset($_POST['text']))
        {
            $message = 'Der Text ist ungültig.';
        }
        else
        { 
            $titel = $database->EscapeString($_POST['titel']);
            $mode = $database->EscapeString($_POST['mode']);
            $bild = $database->EscapeString($_POST['bild']);
            $datum = $database->EscapeString($_POST['datum']);
            $unformattedtext = $_POST['text'];
            $text = $database->EscapeString($_POST['text']);
            $result = $database->Insert('titel, mode, bild, datum, text', '"'.$titel.'", "'.$mode.'", "'.$bild.'", "'.$datum.'", "'.$text.'"', 'writer');
            $message = "Eintrag erfolgreich gespeichert";
        }
    }
    else if(isset($_GET['a']) && $_GET['a'] == 'edit')
    {
        if(!isset($_GET['id']) || !is_numeric($_GET['id']))
        {
            $message = "Die ID ist ungültig";
        }
        else
        {
            $id = $_GET['id'];
            $titel = $database->EscapeString($_POST['titel']);
            $mode = $database->EscapeString($_POST['mode']);
            $bild = $database->EscapeString($_POST['bild']);
            $datum = $database->EscapeString($_POST['datum']);
            $text = $database->EscapeString($_POST['text']);
            $result = $database->Update('titel="'.$titel.'", mode="'.$mode.'", bild="'.$bild.'", datum="'.$datum.'", text="'.$text.'"', 'writer', 'id="'.$id.'"', 1);
            $message = "Du hast den Beitrag erfolgreich bearbeitet";
        }
    }