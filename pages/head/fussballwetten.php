<?php
$datum = date('jmY');
include_once $_SERVER['DOCUMENT_ROOT'].'classes/player/player.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/fussballwetten/fussballwetten.php';

if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'])
{
    $game = new fussballwetten($database, $_GET['id']);

    // Am Spiel teilnehmen
    if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] && isset($_GET['teilnahme']) && $_GET['teilnahme'] == 'true')
    {
        $price = 10000;
        $wallet = $player->GetBerry() - $price;
        $tipp = $database->EscapeString($_POST['tipp']);
        $check = explode(':', $tipp);
        // ID vom Spieler heraus finden
        $tipper = 0;
        $teilnehmercheck = explode(';', $game->GetTeilnehmer());
        $time = date('Hi');
        foreach($teilnehmercheck as $val)
        {
           $tipper = $val;

        }
        // Ende
        if(!$player->IsLogged())
        {
            $message = 'Du musst eingeloggt sein um für das Spiel zu wetten!';
        }
        else if(!is_numeric($check[0]))
        {
            $message = "Dein Tipp wurde im falschen Format angewendet, bitte nur im 1:1 format ohne Buchstaben oder ähnliches!";
        }
        else if($game->GetTime() < $time)
        {
            $message = "Das Spiel hat bereits begonnen! Du kannst nun kein Tipp mehr abgeben.";
        }
        else if(preg_match('/[a-zA-Z]+/', $tipp))
        {
            $message = "Falsches Format! Es dürfen keine Buchstaben verwendet werden, bitte nur im 1:1 Format!";
        }
        else if($wallet < 0)
        {
            $message = "Du besitzt nicht genug Berry um am Spiel teilzunehmen!";
        }
        else if(strlen($tipp) > 5)
        {
            $message = "Dein Tipp darf nicht mehr als 5 Zeichen haben!";
        }
        else if(in_array($tipper, $teilnehmercheck))
        {
            $message = "Du nimmst bereits teil!";
        }
        else if($game->GetDatum() > $datum)
        {
            $message = "Das Spiel ist bereits vorbei!";
        }
        else
        {
            $game->SetTeilnehmer($player->GetID(), $tipp);
            $game->SetEinsatz($game->GetEinsatz() + 10000);
            $player->SetBerry($wallet);
            $message = "Du hast erfolgreich für das Spiel gewettet!";
        }
        // Ende
    }
}

// Spiel erstellen
if(isset($_GET['a']) && $_GET['a'] == 'create')
{
    $mannschaftz = $database->EscapeString($_POST['mannschaftz']);
    $logoz = $database->EscapeString($_POST['logoz']);
    $mannschaftg = $database->EscapeString($_POST['mannschaftg']);
    $logog = $database->EscapeString($_POST['logog']);
    $starttime = $database->EscapeString($_POST['startuhrzeit']);
    $startdate = $database->EscapeString($_POST['startdatumop']);
    $start = $database->EscapeString($_POST['start']);
    if($player->GetArank() < 2)
    {
        $message = "Du hast hierfür keine Rechte!";
    }
    else
    {
        $result = $database->Insert('mannschaftz, flaggez, mannschaftg, flaggeg, ergebnis, einsatz, teilnehmer, start, datum, beginn', '"'.$mannschaftz.'", "'.$logoz.'", "'.$mannschaftg.'", "'.$logog.'", "0:0", 20000, "", "'.$starttime.'", "'.$startdate.'", "'.$start.'"', 'fussballwetten');
        $message = "Das Spiel wurde erfolgreich erstellt!";
    }


}
// Ende

// >Spiel bearbeiten
if(isset($_GET['a']) && $_GET['a'] == 'edit' && isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'])
{
    $game = new fussballwetten($database, $_GET['id']);
    $mannschaftz = $database->EscapeString($_POST['mannschaftz']);
    $logoz = $database->EscapeString($_POST['logoz']);
    $mannschaftg = $database->EscapeString($_POST['mannschaftg']);
    $logog = $database->EscapeString($_POST['logog']);
    $starttime = $database->EscapeString($_POST['startuhrzeit']);
    $startdate = $database->EscapeString($_POST['startdatumop']);
    $start = $database->EscapeString($_POST['start']);
    $ergebnis = $database->EscapeString($_POST['ergebnis']);
    $pott = $database->EscapeString($_POST['einsatz']);
    if($player->GetArank() < 2)
    {
        $message = "Du hast keine Berechtigung hierfür!";
    }
    else if(!$player->IsLogged())
    {
        $message = "Du musst eingeloggt sein!";
    }
    else
    {
        $game->SetMannschaftZ($mannschaftz);
        $game->SetFlaggeZ($logoz);
        $game->SetMannschaftG($mannschaftg);
        $game->SetFlaggeG($logog);
        $game->SetTime($starttime);
        $game->SetDatum($startdate);
        $game->SetBeginn($start);
        $game->SetErgebnis($ergebnis);
        $game->SetEinsatz($pott);
        $message = "Du hast das Spiel erfolgreich editiert!";
    }
}
// Ende

// Spiel löschen
if(isset($_GET['a']) && $_GET['a'] == 'delete' && isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'])
{
    $game = new fussballwetten($database, $_GET['id']);
    if($player->GetArank() < 2)
    {
        $message = "Du hast keine Berechtigung hierfür!";
    }
    else if(!$player->IsLogged())
    {
        $message = "Du musst eingeloggt sein!";
    }
    else
    {
       $result = $database->Delete('fussballwetten', 'id="'.$game->GetID().'"');
        $message = "Du hast das Spiel erfolgreich gelöscht";
    }
}
// Ende