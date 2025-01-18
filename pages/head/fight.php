<?php
$title = 'Kämpfe';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/clan/clan.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/arena/arena.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/events/events.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/tournament/tournament.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/tournament/tournamentfighter.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/tournament/tournamentmanager.php';

if (isset($_GET['a']) && $player->IsLogged())
{
    $a = $_GET['a'];
    $team = -1;
    $newFight = null;
    $fightes = null;
    $mode = null;
    $arena = new Arena($database);

    if (isset($_REQUEST['team']))
    {
        $team = $_REQUEST['team'];
    }

    if (isset($_GET['fight']))
    {
        $newFight = new Fight($database, $_GET['fight'], $player, $actionManager);
        $teams = $newFight->GetTeams();
        $mode = $newFight->GetMode();

    }

    if ($a == 'join')
    {
        $newFight = new Fight($database, $_GET['fight'], $player, $actionManager);
        // $target = Spieler welcher den Kampf erstellt hat
        $target = "";
        if($newFight->GetType() == 1 || $newFight->GetType() == 13)
        {
            $TargetCheck = $database->Select('*', 'fighters', 'fight="'.$newFight->GetID().'" AND team=0');
            $ewfcerfref = $TargetCheck->fetch_assoc();
            $target = new Player($database, $ewfcerfref['acc']);

        }
        // Ende
        if (isset($fight))
        {
            $message = "Du befindest dich schon in einem Kampf";
        }
        else if ($player->GetTournament() != 0)
        {
            $message = 'Du kannst während des Turniers nichts kämpfen.';
        }
        else if($newFight->GetType() == 5 && $player->GetGroup() != 0)
        {
            $groupCheck = $database->Select('*', 'accounts', 'group="'.$player->GetGroup().'"');
            if($groupCheck)
            {
                while($group = $groupCheck->fetch_assoc())
                {
                    $groupPlayer = new Player($database, $group['id']);
                    if($player->GetPlanet() != $groupPlayer->GetPlanet())
                    {
                        $message = "Es befinden sich nicht alle im selben Meer!";
                    }
                }
            }
        }
        else if($player->GetLevel() < 5 && $newFight->GetType() == 13)
        {
            $message = "Du kannst einen Elokampf erst ab Level 5 bestreiten!";
        }
        else if($player->GetBookingDays() > 0 && $newFight->GetType() != 0)
        {
            $message = "Du kannst während du pausierst keinen Kampf beitreten!";
        }
        else if (!isset($_GET['fight']) || !is_numeric($_GET['fight']))
        {
            $message = "Dieser Kampf ist ungültig.";
        }
        else if (!$newFight->IsValid())
        {
            $message = "Dieser Kampf existiert nicht.";
        }
        else if ($newFight->IsStarted())
        {
            $message = "Dieser Kampf läuft bereits.";
        }
        else if ($newFight->GetType() == 11 && $newFight->GetChallenge() != $clan->GetID() && $newFight->GetChallenge() != 0)
        {
            $message = "Dieser Bandenkampf kann nur von der betroffenen Bande betreten werden.";
        }
        else if ((!isset($team) || !is_numeric($team)))
        {
            $message = "Das Team ist ungültig.";
        }
        else if (!isset($mode[$team]))
        {
            $message = 'Das Team gibt es nicht.';
        }
        else if ($newFight->GetType() == 13 && $player->GetLP() < ($player->GetMaxLP() * 1))
        {
            $message = 'Deine LP müssen für einen Elokampf voll geheilt sein';
        }
        else if ($newFight->GetType() == 13 && $player->GetKP() < ($player->GetMaxKP() * 1))
        {
            $message = 'Deine AD müssen für einen Elokampf voll geheilt sein';
        }
        /*else if($player->GetDailyEloFights() >= 5 && $player->GetDailyMaxEloFights() == 5 && $newFight->GetType() == 13)
        {
            $message = "Du hast bereits alle Elokämpfe absolviert!";
        }*/
        else if($newFight->GetType() == 13 && !$player->IsVerified())
        {
            $message = "Du bist nicht verifiziert, deswegen kannst du keine Elokämpfe machen";
        }
        else if ($newFight->GetType() != 0 && $newFight->GetType() != 13 && $player->GetLP() < ($player->GetMaxLP() * 0.1))
        {
            $message = 'Du hast nicht genügend LP. Du benötigst mindestens 10% deiner maximalen LP.'.$newFight->GetType();
        }
        else if ($newFight->GetType() != 0 && $newFight->GetType() != 13 && $player->GetKP() < ($player->GetMaxKP() * 0.1))
        {
            $message = 'Du hast nicht genügend AD. Du benötigst mindestens 10% deiner maximalen AD.';
        }
        else if ($newFight->GetType() != 0 && $newFight->GetType() != 1 && $newFight->GetType() != 13 && $newFight->GetType() != 2 && $newFight->GetPlanet() != $player->GetPlanet())
        {
            $message = 'Du befindest dich nicht im richtigen Meeresgebiet.';
        }
        else if($newFight->GetType() != 0 && $newFight->GetType() != 1 && $newFight->GetType() != 13 && $newFight->GetType() != 2 && $newFight->GetPlace() != $player->GetPlace())
        {
            $message = "Es müssen sich alle Kämpfer am selben Ort befinden!";
        }
        else if($arena->IsFighterIn($player->GetID()))
        {
            $message = 'Du kannst diesen Kampf nicht bestreiten, solange du dich im Kolosseum befindest.';
        }
        else if($newFight->GetType() == 11 && $player->InClanSince() > -5)
        {
            $message = 'Du kannst einen Bandenkampf erst beitreten wenn du mindestens 5 Tage Mitglied der Bande bist.';
        }
        else if($newFight->GetType() == 13 && ($player->IsMultiChar() || $target->IsMultiChar()))
        {
            $message = 'Mit einen Zweitcharakter können keine Elo-Kämpfe durchgeführt werden.';
        }
        else if($newFight->GetType() == 1 && $target->IsMultiChar() && !$player->IsMultiChar() && $player->GetClan() != 0 && $player->GetClan() == $target->IsMulticharBande())
        {
            $message = "Der Main dieses Multichars ist in deiner Bande, deshalb dürft ihr keine KGs machen!";
        }
        else if($newFight->GetType() == 1 && $player->IsMultiChar() && $target->GetClan() != 0 && $target->GetClan() == $player->IsMulticharBande())
        {
            $message = "Dieser User ist in der Bande deines Mains, deswegen kannst du dem KG nicht joinen";
        }
        else if($newFight->GetType() == 1 && $player->IsKGClose())
        {
            $message = 'Du darfst aktuell keine PvP-Kämpfe durchführen.';
        }
        else if($newFight->GetType() == 13 && $player->IsEloClose())
        {
            $message = 'Du darfst aktuell keine Elokämpfe durchführen.';
        }
        else if($newFight->GetType() == 11 && $player->GetActiveUserID() != $player->GetUserID())
        {
            $message = 'Als Sitter kannst du keinen Bandenkampf mit einen zu sittenden Charakter durchführen.';
        }
        else
        {

            if (isset($teams[$team]) && count($teams[$team]) >= $mode[$team])
            {
                $message = 'Das Team ist schon voll!';
            }
            else {
                /*$ClanLimitReached = false;
                $enemyTeam = $teams[0];
                if ($player->GetClan() != 0) {
                    $clan = new Clan($database, $player->GetClan());
                    foreach ($enemyTeam as $member) {
                        $enemy = new Player($database, $member->GetAcc());
                        if ($enemy->IsValid()) {
                            if ($member->GetClanID() == $player->GetClan() || in_array($enemy->GetClan(), $clan->GetAlliances())) {
                                if (
                                    ($enemy->GetClan() == $player->GetClan() && ($player->GetClanEloFights() >= 2 || $enemy->GetClanEloFights() >= 2)) || // Eigener Clan
                                    (in_array($enemy->GetClan(), $clan->GetAlliances()) && ($player->GetClanEloFights() >= 2 || $enemy->GetClanEloFights() >= 2)) // Allianz-Clan
                                ) {
                                    $ClanLimitReached = true;
                                }
                            }
                        }
                    }
                }
                if ($newFight->GetType() == 13 && $ClanLimitReached)
                {
                    $message = 'Du oder der Gegner können keine weiteren Elo-Kämpfe gegen Bandenmitglieder oder Allianzbanden-Mitglieder durchführen.';
                }*/
                $cantFight = false;
                $enemyTeam = $teams[0];
                if ($player->GetClan() != 0) {
                    foreach ($enemyTeam as $member) {
                        if ($member->GetAcc() != $player->GetID()) {
                            if($player->GetDailyEloEnemyCount($member->GetAcc()) >= 2 && $newFight->GetType() == 13)
                                $cantFight = true;
                        }
                    }
                }
                if($cantFight)
                {
                    $message = "Du hast heute bereits 2 Elo-Kämpfe gegen diesen Spieler durchgeführt, such dir einen anderen Gegner.";
                }
                else
                {
                    if (!$newFight->Join($player, $team)) {
                        $message = 'Es gab Probleme beim Betreten vom Kampf.';
                    } else {
                        $fight = $newFight;

                        if ($newFight->IsStarted() && $newFight->GetType() != 0) {
                            $charaids = array();
                            $charaids[] = $player->GetID();
                            $i = 0;
                            while (isset($teams[$i])) {
                                $players = $teams[$i];
                                $j = 0;
                                while (isset($players[$j])) {
                                    if (!$players[$j]->IsNPC())
                                        $charaids[] = $players[$j]->GetAcc();
                                    $j++;
                                }
                                ++$i;
                            }
                            $fighttype = 'Unbekannter Kampf';
                            switch ($newFight->GetType()) {
                                case 1:
                                    $fighttype = 'PvP-Kampf';
                                    break;
                                case 3:
                                    $fighttype = 'NPCKampf';
                                    break;
                                case 4:
                                    $fighttype = 'Storykampf';
                                    break;
                                case 5:
                                    $fighttype = 'Eventkampf';
                                    break;
                                case 6:
                                    $fighttype = 'Turnierkampf';
                                    break;
                                case 7:
                                    $fighttype = 'FEHLER';
                                    break;
                                case 8:
                                    $fighttype = 'Kolosseumskampf';
                                    break;
                                case 9:
                                    $fighttype = 'Turmkampf';
                                    break;
                                case 10:
                                    $fighttype = 'Nebenstorykampf';
                                    break;
                                case 11:
                                    $fighttype = 'Bandenkampf';
                                    break;
                                case 12:
                                    $fighttype = 'Schatzsuche';
                                    break;
                                case 13:
                                    $fighttype = 'Elokampf';
                                    break;
                            }
                            LoginTracker::AddInteraction($accountDB, $charaids, $fighttype, 'opbg');
                        }
                    }
                }
            }
        }
    }
    else if ($a == 'adminjoin' && $player->GetARank() == 3)
    {
        if (isset($fight))
        {
            $message = "Du befindest dich schon in einem Kampf.";
        }
        else if ($player->GetTournament() != 0)
        {
            $message = 'Du kannst während des Turniers nichts kämpfen.';
        }
        else if (!isset($_GET['fight']) || !is_numeric($_GET['fight']))
        {
            $message = "Dieser Kampf ist ungültig.";
        }
        else if (!$newFight->IsValid())
        {
            $message = "Dieser Kampf existiert nicht.";
        }
        else if ((!isset($team) || !is_numeric($team)))
        {
            $message = "Das Team ist ungültig.";
        }
        else if (!isset($mode[$team]))
        {
            $message = 'Das Team gibt es nicht.';
        }
        else
        {
            $newFight->ForceJoin($player, $team);
        }
    }
    else if($_GET['a'] == 'delete')
    {
        if(!isset($_GET['id']) || !is_numeric($_GET['id']))
        {
            $message = "Ungültige Kampf-ID";
        }
        else if($player->GetArank() < 3)
        {
            $message = "Du bist nicht berechtigt.";
        }
        else {
            $id = $_GET['id'];
            $fight = new Fight($database, $id, $player, $actionManager);
            if($fight->IsTestFight() && $player->GetArank() < 2 || !$fight->IsTestFight() && $player->GetArank() < 3)
            {
                $message = 'Du bist dazu nicht berechtigt.';
            }
            else
            {
                $targetFight = $database->Select('*', 'fighters', 'fight="' . $id . '" and acc != -1');
                if ($targetFight) {
                    while ($fighter = $targetFight->fetch_assoc()) {
                        if ($fighter['npc'] == 0) {
                            $database->Update('fight=0', 'accounts', 'id="' . $fighter['acc'] . '"');
                            $targetPlayer = new Player($database, $fighter['acc']);
                            $targetPlayer->SetFight(0);
                        }
                        $database->Delete('fighters', 'fight="' . $id . '"');
                    }
                    $fight->DeleteFight();
                    $message = "Der Kampf mit folgender ID " . $id . " wurde gelöscht";
                }
            }
        }
    }
    else if ($a == 'leave' && isset($fight))
    {
        if($fight->GetType() == 11 && $fight->GetChallenge() == $player->GetClan())
            $result = $fight->Leave($player);
        else if($fight->GetType() == 11)
            $message = 'Du kannst den Bandenkampf nicht verlassen.';
        else
            $result = $fight->Leave($player);
    }
    else if ($a == 'start' && isset($_POST['type']) && isset($_POST['name']) && isset($_POST['mode']))
    {

        $type = $_POST['type'];
        $name = $_POST['name'];
        $mode = Fight::ValidateMode($_POST['mode']);
        if ($name == '')
        {
            switch ($type)
            {
                case 0:
                    $name = 'Spaß';
                    break;
                case 1:
                    $name = 'PvP';
                    break;
                case 13:
                    $name = 'Elokampf';
                    break;
            }
        }
        if ($database->HasBadWords($name))
        {
            $message = 'Der Name enthält ungültige Wörter.';
        }
        else if($name != 'Spaß' && $name != 'PvP' && !$player->IsVerified())
        {
            $message = "Du kannst dem Kampf erst ein eigenen Namen geben wenn du von einem Admin verifiziert wurden bist!";
        }
        else if (strlen($name) >= 20)
        {
            $message = 'Der Name ist zu lang.';
        }
        else if($player->GetLevel() < 5 && $type == 13)
        {
            $message = "Du kannst erst ab Level 5 einen Elokampf bestreiten!";
        }
        else if (isset($fight))
        {
            $message = "Du befindest dich schon in einem Kampf.";
        }
        else if($type == 5 && $player->GetGroup() != 0)
        {
            $groupCheck = $database->Select('*', 'accounts', 'group="'.$player->GetGroup().'"');
            if($groupCheck)
            {
                while($group = $groupCheck->fetch_assoc())
                {
                    $groupPlayer = new Player($database, $group['id']);
                    if($player->GetPlanet() != $groupPlayer->GetPlanet())
                    {
                        $message = "Es befinden sich nicht alle im selben Meer!";
                    }
                }
            }
        }
        else if($player->GetBookingDays() > 0 && $type != 0)
        {
            $message = "Du kannst keinen Kampf starten wenn du am pausieren bist!";
        }
        else if ($player->GetTournament() != 0)
        {
            $message = 'Du kannst während des Turniers nichts kämpfen.';
        }
        else if ($type < 0)
        {
            $message = 'Diese Art von Kampf gibt es nicht!';
        }
        else if ($type > 1 && $type != 13)
        {
            $message = 'Diese Art von Kampf gibt es nicht!';
        }
        else if (($type == 1 || $type == 13) && $mode != '1vs1')
        {
            $message = 'Dieser Kampf kann nur in einem 1 vs 1 ausgetragen werden.';
        }
        /*else if($type == 13 && $player->GetDailyEloFights() >= 5 && $player->GetDailyMaxElofights() == 5)
        {
            $message = "Du hast bereits alle Elokämpfe absolviert!";
        }*/
        else if(($type == 0 || $type == 13) && !$player->IsVerified())
        {
            $message = "Um Spaßkämpfe öffnen zu können, musst du erst verifiziert werden!";
        }
        else if (!$mode)
        {
            $message = 'Der Mode ist ungültig';
        }
        else if ($name == '' || $name == ' ')
        {
            $message = 'Der Name ist ungültig';
        }
        else if ($type == 13 && $player->GetLP() < ($player->GetMaxLP() * 1))
        {
            $message = 'Deine LP müssen für einen Elokampf voll geheilt sein';
        }
        else if ($type == 13 && $player->GetKP() < ($player->GetMaxKP() * 1))
        {
            $message = 'Deine AD müssen für einen Elokampf voll geheilt sein';
        }
        /*else if($player->GetDailyEloFights() >= 5 && $player->GetDailyMaxElofights() == 5 && $type == 13)
        {
            $message = "Du hast bereits alle Elokämpfe absolviert!";
        }*/
        else if($type == 13 && !$player->IsVerified())
        {
            $message = "Du bist nicht verifiziert, deswegen kannst du keine Elokämpfe machen";
        }
        else if ($type != 0 && $type != 13 && $player->GetLP() < ($player->GetMaxLP() * 0.1))
        {
            $message = 'Du hast nicht genügend LP. Du benötigst mindestens 10% deiner maximalen LP.';
        }
        else if ($type != 0 && $type != 13 && $player->GetKP() < ($player->GetMaxKP() * 0.1))
        {
            $message = 'Du hast nicht genügend AD. Du benötigst mindestens 10% deiner maximalen AD.';
        }
        else if($type == 13 && $player->GetKP() < $player->GetMaxKP())
        {
            $message = 'Du hast nicht genügend AD. Du benötigst 100% deiner maximalen AD.';
        }
        else if($type == 13 && $player->GetLP() < $player->GetMaxLP())
        {
            $message = 'Du hast nicht genügend LP. Du benötigst 100% deiner maximalen LP.';
        }
        else if (!$player->IsVerified() && $player->GetDailyfights() >= 5)
        {
            $message = 'Solange dein Charakter nicht verifiziert wurde, kannst du nur 5 PvP-Kämpfe eröffnen.';
        }
        else if ($player->GetLevel() < 5 && $player->GetDailyfights() >= 5)
        {
            $message = 'Du musst mindestens Level 5 sein um mehr als 5 PvP-Kämpfe öffnen zu können.';
        }
        else if($arena->IsFighterIn($player->GetID()))
        {
            $message = 'Du kannst diesen Kampf nicht bestreiten, solange du dich im Kolosseum befindest.';
        }
        else if($type == 13 && $player->IsMultiChar())
        {
            $message = 'Mit einen Zweitcharakter können keine Elo-Kämpfe durchgeführt werden.';
        }
        else if($type == 6 && $player->IsMultiChar())
        {
            $message = 'Mit einen Zweitcharakter können keine Turnierkämpfe durchgeführt werden durchgeführt werden.';
        }
        else if($type == 11 && $player->IsMultiChar())
        {
            $message = 'Mit einen Zweitcharakter können keine Bandenkämpfe durchgeführt werden.';
        }
        else if($type == 1 && $player->IsKGClose())
        {
            $message = 'Du darfst aktuell keine PvP-Kämpfe durchführen.';
        }
        else if($type == 13 && $player->IsEloClose())
        {
            $message = 'Du darfst aktuell keine Elokämpfe durchführen.';
        }
        else if($type == 5)
        {

        }
        else
        {
            $createdFight = Fight::CreateFight($player, $database, $type, $name, $mode, 0, $actionManager);
            if (!$createdFight)
            {
                $message = 'Es gab Probleme bei der Erstellung des Kampfes.';
            }
            else
            {
                $createdFight->Join($player, 0, false);
            }
        }
    }
    else if($a == 'mirror' && isset($_GET['t']) && is_numeric($_GET['t']))
    {
        $now = new DateTime('now');
        $weekday = date("l");
        $mirrorTime = 5;
        if($player->IsDonator())
            $mirrorTime = 3;
       $min = '00:01';
       $max = '23:59';
        if((date('H:i') < $min || date('H:i') > $max) && $player->GetArank() < 3)
        {
            $message = 'Du kannst diese Aktion nur Mo-Fr. von 16-22 Uhr & Sa-So. von 12-22 Uhr nutzen.';
        }
        else if(!isset($fight) || $fight->IsStarted())
        {
            $message = 'Diese Aktion ist ungültig!';
        }
        else if($fight->GetType() != 1 && $fight->GetType() != 13)
        {
            $message = 'Diese Aktion ist bei dem Kampftyp nicht möglich.';
        }
        else if(abs( DateTime::createFromFormat('Y-m-d H:i:s', $fight->GetTime())->getTimestamp() - $now->getTimestamp()) / 60 < $mirrorTime)
        {
            $message = 'Du musst mindestens '.$mirrorTime.' Minuten warten.';
        }
        else
        {
            if($_GET['t'] == 0)
            {
                $database->Update('mirrorpopup=0', 'accounts', 'id='.$player->GetID(), 1);
                $database->Update('time=NOW(), mirror=0', 'fights', 'id='.$fight->GetID(), 1);
                $player->OpenMirror(0);
                $fight->SetMirror(0);
            }
            else if($_GET['t'] == 1)
            {
                $mirrorPlayer = $player;
                $mirrorPlayer->SetFight(0);
                $MirrorAD = round($player->GetMaxKP() * 2);
                $mirrorPlayer->SetFightAttacks($player->GetAttacks());
                if($fight->Join($mirrorPlayer, 1, false))
                {
                    $database->Update('kp="'.$MirrorAD.'", mkp="'.$MirrorAD.'", npccontrol=1, acc="-1", attacks="'.$player->GetAttacks().'"', 'fighters', 'id='.$fight->GetTeams()[1][0]->GetID(), 1);
                    $database->Update('mirror=1', 'fights', 'id='.$player->GetFight());
                    $database->Update('mirrorpopup=0', 'accounts', 'id='.$player->GetID(), 1);
                    $fight->SetMirror(1);
                    $mirrorPlayer->OpenMirror(0);
                    $player->OpenMirror(0);
                }
                header('Location: ?p=infight');
                exit();
            }
        }
    }
}
