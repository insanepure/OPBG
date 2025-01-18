<?php

$title = 'Moderation';
if (!isset($player) || !$player->IsValid() || $player->GetARank() < 2)
{
    header('Location: ?p=news');
    exit();
}

function AddToLog($database, $ip, $accs, $log)
{
    $timestamp = date('Y-m-d H:i:s');
    $insert = '"' . $ip . '","' . $accs . '","' . $database->EscapeString($log) . '","' . $timestamp . '"';
    $result = $database->Insert('ip,accounts,log,time', $insert, 'adminlog');
}

if (isset($_GET['a']) && $_GET['a'] == 'edit' && isset($_POST['main']) && is_numeric($_POST['main']))
{
    $mainID = $_POST['main'];
    $userID = $_POST['userid'];

    $preBanned = 0;
    $preReason = '';
    $preBannedGames = array();
    $result = $accountDB->Select('id, bannedgames, banreason', 'users', 'id = ' . $mainID . '', 1);
    $gameName = 'opbg';
    if ($result)
    {
        if ($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            if ($row['bannedgames'] != '')
                $preBannedGames = explode(';', $row['bannedgames']);
            $preBanned = in_array($gameName, $preBannedGames);
            $preReason = $row['banreason'];
        }
        $result->close();
    }

    $banned = 0;
    if (isset($_POST['banned']))
    {
        $banned = 1;
    }

    $banreason = $accountDB->EscapeString($_POST['banreason']);
    $pvpfights = $database->EscapeString($_POST['dailykg']);
    $kolopoints = $database->EscapeString($_POST['dailyarena']);
    $elofights = $database->EscapeString($_POST['dailyelo']);
    $elofightsp = $database->EscapeString($_POST['dailyelop']);

    $koloclose = 0;
    if (isset($_POST['koloclose']))
    {
        $koloclose = 1;
    }
    $eloclose = 0;
    if (isset($_POST['eloclose']))
    {
        $eloclose = 1;
    }

    $kgclose = 0;
    if (isset($_POST['kgclose']))
    {
        $kgclose = 1;
    }

    $sitterid = $database->EscapeString($_POST['sitter']);
    $sittingstart = $database->EscapeString($_POST['sittingstart']);
    $sittingend = $database->EscapeString($_POST['sittingend']);


    if ($preBanned)
    {
        $key = array_search($gameName, $preBannedGames);
        unset($preBannedGames[$key]);
        $preBannedGames = array_values($preBannedGames);
    }

    if ($banned)
    {
        array_push($preBannedGames, $gameName);
        $preBannedGames = array_values($preBannedGames);
    }

    $preBannedGames = implode(';', $preBannedGames);

    $target = new Player($database, $userID);
    $koloChanged = false;
    $dailyfightsChanged = false;
    $dailyelofightsChanged = false;
    $koloAlt = 0;
    $koloNeu = 0;
    $dailyKGAlt = 0;
    $dailyKGNeu = 0;
    $eloAlt = 0;
    $eloNeu = 0;
    if($target->GetArenaPoints() != $kolopoints) {
        $koloChanged = true;
        $koloAlt = $target->GetArenaPoints();
        $koloNeu = $kolopoints;
    }
    if($target->GetDailyfights() != $pvpfights) {
        $dailyfightsChanged = true;
        $dailyKGAlt = $target->GetDailyfights();
        $dailyKGNeu = $pvpfights;
    }
    if($target->GetDailyEloFights() != $elofights) {
        $dailyelofightsChanged = true;
        $eloAlt = $target->GetDailyEloFights();
        $eloNeu = $elofights;
    }

    $meldung = $player->GetName().' hat Werte von '.$target->GetName().' angepasst: ';

    if($koloChanged)
        $meldung .= 'Kolopunkte - Alt: '.$koloAlt.', Neu: '.$koloNeu.' ';
    if($dailyfightsChanged) {
        if ($meldung != $player->GetName().' hat Werte von '.$target->GetName().' angepasst: ')
            $meldung .= ', ';
        $meldung .= 'PvP-Kämpfe (Täglich) - Alt: ' . $dailyKGAlt . ', Neu: ' . $dailyKGNeu . ' ';
    }
    if($dailyelofightsChanged) {
        if ($meldung != $player->GetName().' hat Werte von '.$target->GetName().' angepasst: ')
            $meldung .= ', ';
        $meldung .= 'Elokämpfe (Täglich) - Alt: ' . $eloAlt . ', Neu: ' . $eloNeu . ' ';
    }

    if($koloChanged || $dailyfightsChanged || $dailyelofightsChanged)
        $player->AddMeldung($meldung, $player->GetID(), 'System', 2);

    $result = $accountDB->Update('bannedgames="' . $preBannedGames . '",banreason="' . $banreason . '"', 'users', 'id = ' . $mainID . '', 1);
    $result = $database->Update('sitter="'.$sitterid.'", sitterstart="'.$sittingstart.'", sitterend="'.$sittingend.'", koloclose="'.$koloclose.'", eloclose="'.$eloclose.'", kgclose="'.$kgclose.'", banned="' . $banned . '", banreason="' . $banreason . '"', 'accounts', 'userid="' . $mainID . '"');
    $result = $database->Update('arenapoints="'.$kolopoints.'", elopoints="'.$elofightsp.'", dailyfights="'.$pvpfights.'", dailyelofights="'.$elofights.'"', 'accounts', 'id='.$userID);
    if($banned)
        $database->Update('igentry=1','statslist', 'acc='.$userID);
    else if($target->GetArank() == 0 && $target->IsDeleted() == 0)
        $database->Update('igentry=0','statslist', 'acc='.$userID);
    $ip = $account->GetIP();
    $accs = $player->GetName() . ' (' . $player->GetID() . ')';
    $log = '';

    if ($preBanned != $banned)
    {
        $log = 'Der Ban vom MainAccount <b>' . number_format($mainID,'0', '', '.') . '</b> wurde von "<b>' . $preBanned . '</b>" zu "<b>' . $banned . '</b>" gesetzt.';
        AddToLog($database, $ip, $accs, $log);
    }
    if ($preReason != $banreason)
    {
        $log = 'Der Bangrund vom MainAccount <b>' . number_format($mainID,'0', '', '.') . '</b> wurde von "<b>' . $preReason . '</b>" zu "<b>' . $banreason . '</b>" gesetzt.';
        AddToLog($database, $ip, $accs, $log);
    }
    if (isset($_POST['report']) && $_POST['report'] > 0)
        $database->Update('status = 1', 'meldungen', 'id=' . $_POST['report']);
    $message = 'Du hast den Account ' . number_format($mainID,'0', '', '.') . ' moderiert.';
}
