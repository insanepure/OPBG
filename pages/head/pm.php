<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
$title = 'Teleschnecke';

if (isset($_GET['p2']) && $_GET['p2'] == 'read')
{
    if (isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $id = $_GET['id'];
        $PMManager->Read($id, $player->GetID());
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'archive')
{
    if(!isset($_GET['id']) || !is_numeric($_GET['id']))
    {
        $message = 'Ungültige PM';
    }
    else
    {
        $id = $_GET['id'];
        $PMManager->ArchivePM($id, $player->GetID(), 1);
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'unarchive')
{
    if(!isset($_GET['id']) || !is_numeric($_GET['id']))
    {
        $message = 'Ungültige PM';
    }
    else
    {
        $id = $_GET['id'];
        $PMManager->ArchivePM($id, $player->GetID(), 0);
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
        else if($_REQUEST['action'] == 'archive')
        {
            $PMManager->ArchivePM($deleteIDs, $player->GetID(), 1);
        }
        else if($_REQUEST['action'] == 'unarchive')
        {
            $PMManager->ArchivePM($deleteIDs, $player->GetID(), 0);
        }
        else if ($_REQUEST['action'] == 'markread')
        {
            $PMManager->ReadAllOnly($deleteIDs, $player->GetID());
        }
        else if ($_REQUEST['action'] == 'deleteall')
        {
            $PMManager->DeleteAll(false, $player->GetID());
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
        $receiver = $database->Select('*', 'accounts', 'name like "'.$tname.'"');
        $target = null;
        if($receiver && $receiver->num_rows > 0)
        {
            $target = $receiver->fetch_assoc();
        }

        if ($database->HasBadWords($titlepm))
        {
            $message = 'Der Betreff enthält ungültige Wörter.';
        }
        else if ($database->HasBadWords($text))
        {
            $message = 'Der Text enthält ungültige Wörter.';
        }
        else if($target != null && $target['arank'] >= 2 && $target['team'] >= 2 && $target['can_receive_pm'] == 0)
        {
            $message = "Du kannst einem Team Mitglied nicht privat schreiben, bitte öffne ein Ticket";
        }
        else if ($tname == '')
        {
            $message = 'Du hast keinen Namen angegeben.';
        }
        else if ($text == '')
        {
            $message = 'Du hast keinen Text angegeben.';
        }
        else if(!$player->IsVerified())
        {
            $message = "Du musst verifiziert sein um Nachrichten zu versenden";
        }
        else if ($titlepm == '')
        {
            $message = 'Du hast keinen Betreff angegeben.';
        }
        else
        {
            $id = $player->GetID();
            $html = 0;
            if($player->GetArank() >= 2)
            {
                $titlepm = "<span style='color: red;'>".$titlepm."</span>";
                $html = 1;
            }
            if ($PMManager->SendPM($id, $player->GetImage(), $player->GetName(), $titlepm, $text, $tname, $html, $player->GetArank()))
            {
                $message = 'PM wurde erfolgreich gesendet.';
            }
            else
            {
                $message = 'Du kannst dem Spieler nicht schreiben.';
            }
        }
    }
}
