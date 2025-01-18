<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
$title = 'System';

if (isset($_GET['p2']) && $_GET['p2'] == 'read')
{
    if (isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $id = $_GET['id'];
        $PMManager->Read($id, $player->GetID());
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'action')
{
    $deleteIDs = array();
    if (isset($_GET['deleteID']))
    {
        $deleteIDs[] = $_GET['deleteID'];
    }
    else
    {
        $deleteIDs = $_POST['deleteID'];
    }

    if (isset($_REQUEST['action']))
    {
        //echo $_REQUEST['action'];
        if ($_REQUEST['action'] == 'delete')
        {
            $PMManager->Delete($deleteIDs, $player->GetID());
        }
        else if ($_REQUEST['action'] == 'read')
        {
            $PMManager->ReadAll($deleteIDs, $player->GetID());
        }
        else if ($_REQUEST['action'] == 'markread')
        {
            $PMManager->ReadAllOnly($deleteIDs, $player->GetID());
        }
        else if($_REQUEST['action'] == 'archive')
        {
            $PMManager->ArchivePM($deleteIDs, $player->GetID(), 1);
        }
        else if($_REQUEST['action'] == 'unarchive')
        {
            $PMManager->ArchivePM($deleteIDs, $player->GetID(), 0);
        }
        else if ($_REQUEST['action'] == 'deleteall')
        {
            $PMManager->DeleteAll(true, $player->GetID());
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'send')
{
    if (!isset($_POST['to']))
    {
        $message = 'Du hast keinen Namen angegeben!';
    }
    else if (!isset($_POST['text']) || $_POST['text'] == '')
    {
        $message = 'Du hast keinen Text angegeben!';
    }
    else if (!isset($_POST['topic']) || $_POST['topic'] == '')
    {
        $message = 'Du hast keinen Betreff angegeben!';
    }
    else
    {
        $tname = $database->EscapeString($_POST['to']);
        $text = $database->EscapeString($_POST['text']);
        $titlepm = $database->EscapeString($_POST['topic']);

        if ($database->HasBadWords($titlepm))
        {
            $message = 'Der Betreff enthält ungültige Wörter.';
        }
        else if ($database->HasBadWords($text))
        {
            $message = 'Der Text enthält ungültige Wörter.';
        }
        if ($tname == '')
        {
            $message = 'Du hast keinen Namen angegeben.';
        }
        else if ($text == '')
        {
            $message = 'Du hast keinen Text angegeben.';
        }
        else if ($titlepm == '')
        {
            $message = 'Du hast keinen Betreff angegeben.';
        }
        else
        {
            if ($PMManager->SendPM($player->GetID(), $player->GetImage(), $player->GetName(), $titlepm, $text, $tname))
            {
                $message = 'PM wurde erfolgreich gesendet.';
            }
            else
            {
                $message = 'Der Spieler existiert nicht.';
            }
        }
    }
}
