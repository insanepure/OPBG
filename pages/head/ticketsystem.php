<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/ticketsystem/ticket.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/ticketsystem/ticketmanager.php';

if(!$player->IsLogged())
{
    header('location: ?p=news');
    exit();
}

$ticketManager = new TicketManager($database);

if(isset($_POST['create']) && $_POST['create'] == 'answer' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    $post = trim($_POST['answer']);
    if(strlen($post) < 10)
    {
        $message = "Bitte gib mindestens 10 Zeichen ein.";
    }
    else
    {
        $url_regex = '/(https?:\/\/)?(www){0,1}[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()!@:%_\+.~#?&\/\/=]*)/';

        $post = preg_replace($url_regex, "<b><a href='$0' target='_blank'>$0</a></b>", $post);
        $id = $_GET['id'];
        $date = date("d.m.Y H:i");
        $ticket = $ticketManager->GetTicket($id);
        $answer =
            "---------- " . $date. " von <a href='?p=profil&id=".$player->GetID()."'>" . $player->GetName() . "</a> ----------<div class='spacer'></div>" .
            $post . "
            <div class='spacer'></div>";
        $answerer = new Player($database, $ticket->GetCreator());
        if($player->GetID() == $ticket->GetCreator() || $player->GetArank() >= 2 || $player->GetTeamUser() >= 2 && ($ticket->GetActive() == 0)) {
            if (strlen($_POST['answer']) > 750) {
                $message = "Es dürfen nicht mehr als 750 Zeichen angegeben werden!";
            } else {
                $ticket->SetVerlauf($answer);
                if ($player->GetID() == $ticket->GetCreator()) {
                    $ticket->UpdateRead(1);
                } else if ($player->GetID() != $ticket->GetCreator() && $player->GetArank() >= 2 || ($player->GetTeamUser() >= 2)) {
                    $ticket->UpdateRead(0);
                }
                $betreff = "<span style='color: red;'>Du hast eine Antwort erhalten</span>";
                $pms = "Du hast eine Antwort auf dein Ticket erhalten";
                $PMManager->SendPM(1, 'img/system.png', 'Support', $betreff, $pms, $answerer->GetName(), 1);
                $message = "Du hast erfolgreich eine Antwort gesendet";
            }
        }
    }
}


else if(isset($_POST['delete']) && $_POST['delete'] == 'ticket' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    if($player->GetArank() == 3 || $player->GetTeamUser() == 3)
    {
        $id = $_GET['id'];
        $ticket = $ticketManager->GetTicket($id);
        $ersteller = new Player($database, $ticket->GetCreator());
        $ticket->DeleteTicket();
        $betreff = "<span style='color: red;'>Das Ticket wurde gelöscht</span>";
        $pms = "Dein Ticket wurde gelöscht, hiermit erhältst du nochmal den Verlauf <div class='spacer'></div>".nl2br($ticket->GetVerlauf());
        $PMManager->SendPM(1, 'img/system.png', 'Support', $betreff, $pms, $ersteller->GetName(), 1);
        $message = "Das Ticket wurde erfolgreich gelöscht";
        header('Location: ?p=ticketsystem');
        exit();
    }
}


else if(isset($_POST['close']) && $_POST['close'] == 'ticket' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    if($player->GetArank() >= 2 || $player->GetTeamUser() >= 2)
    {
        $ticket = $ticketManager->GetTicket($_GET['id']);
        $deleter = new Player($database, $ticket->GetCreator());
        if($deleter->IsValid()) {
            $ticket->TicketClose($player->GetID());
            $pms = $database->EscapeString("Dein Ticket wurde geschlossen, hiermit erhältst du nochmal den Verlauf <br /><br />" . $ticket->GetVerlauf());
            $pms = nl2br($pms);
            $betreff = "<span style='color: red'>Ticket wurde geschlossen</span>";
            $return = $PMManager->SendPM(1, 'img/system.png', 'Support', $betreff, $pms, $deleter->GetName(), 1);
            $message = "Das Ticket wurde erfolgreich geschlossen";
        }
    }
}


if(isset($_POST['create']) && $_POST['create'] == 'ticket')
{
    $text = $database->EscapeString(trim($_POST['text']));
    $betreff = $database->EscapeString($_POST['betreff']);
    if(!$player->IsVerified())
    {
        $message = "Nur verifizierte Spieler dürfen ein Ticket öffnen";
    }
    else if($ticketManager->HasOpenTickets($player->GetID()))
    {
        $message = "Du hast noch ein Ticket offen";
    }
    else if(strlen($text) < 10)
    {
        $message = "Bitte gib mindestens 10 Zeichen ein.";
    }
    else if($betreff == 'Kein Betreff')
    {
        $message = "Bitte gib ein Betreff an";
    }
    else
    {
        $url_regex = '/(https?:\/\/)?(www){0,1}[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()!@:%_\+.~#?&\/\/=]*)/';

        $text = preg_replace($url_regex, "<b><a href='$0' target='_blank'>$0</a></b>", $text);

        $date = date("d.m.Y H:i");
        $text = "---------- " . $date. " von <a href='?p=profil&id=".$player->GetID()."'>" . $player->GetName() . "</a> ----------<div class='spacer'></div>" . $text;
        $ticketManager->CreateTicket($player->GetID(), $betreff, $text);
        $message = "Ticket wurde eröffnet";
    }
}


if(isset($_POST['open']) && $_POST['open'] == 'ticket' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    $ticket = $ticketManager->GetTicket($_GET['id']);
    if($ticket) {
        $deleter = new Player($database, $ticket->GetCreator());
        if ($player->GetArank() < 2)
        {
            $message = "Du hast keine Berechtigung das Ticket zu öffnen";
        }
        else if ($ticketManager->HasOpenTickets($deleter->GetID()))
        {
            $message = "Der Spieler hat bereits ein offenes Ticket.";
        }
        else
        {
            $ticket->TicketOpen();
            $message = "Ticket wurde wieder geöffnet";
            $betreff = "<span style='color: red;'>Dein Ticket wurde wieder geöffnet</span>";
            $pms = "Dein Ticket wurde auf deinem Wunsch wieder geöffnet";
            $PMManager->SendPM(1, 'img/system.png', 'Support', $betreff, $pms, $deleter->GetName(), 1);
        }
    }
}
