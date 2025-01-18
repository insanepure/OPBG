<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/eloarena/eloarena.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';

$title = 'Elo-Arena';
$itemManager = new ItemManager($database);
if($player->GetArank() < 2)
{
    return;
}

$eloarena = new EloArena($database);
if (isset($_GET['a']) && $_GET['a'] == 'search')
{
    if(!$eloarena->IsFighterIn($player->GetID()))
    {
        $message = 'Du bist nicht im ELo-Warteraum.';
    }
    else if($player->GetBookingDays() > 0)
    {
        $message = "Du kannst keinen Kampf suchen wenn du am pausieren bist!";
    }
    else if($player->IsEloClose() == 1)
    {
        $message = "Du darfst die Elo-Arena derzeit nicht betreten.";
    }
    else if($player->GetLP() != $player->GetMaxLP())
    {
        $message = 'Du musst dich erst voll heilen, bevor du einen Kampf starten kannst';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du kannst während des Turniers nichts kämpfen.';
        $eloarena->Leave($player->GetID());
    }
    else if($player->GetFight() != 0)
    {
        $message = 'Du befindest dich schon in einem Kampf';
    }
    /*else if($player->GetDailyEloFights() >= 5)
    {
        $message = 'Du kannst heute bereits 5 Elo-Kämpfe gemacht!';
        $eloarena->Leave($player->GetID());
    }*/
    else
    {
        $enemyID = $eloarena->GetRandomFighter($player->GetID());
        $createdFight = null;
        if ($enemyID != -1)
        {
            $otherPlayer = new Player($database, $enemyID, $actionManager);
            $enemyCanFight = true;
            if ($otherPlayer->GetTournament() != 0 ||
                $otherPlayer->GetDailyEloFights() >= 5 ||
                !$otherPlayer->IsOnline())
            {
                $eloarena->Leave($otherPlayer->GetID());
                $enemyCanFight = false;
            }
            else if($otherPlayer->GetLP() != $otherPlayer->GetMaxLP() ||
                $otherPlayer->GetKP() != $otherPlayer->GetMaxKP() ||
                $otherPlayer->GetFight() != 0)
            {
                $enemyCanFight = false;
            }
            else
            {
                $type = 13;
                $name = $player->GetName() . ' vs ' . $otherPlayer->GetName();
                $mode = '1vs1';
                $createdFight = Fight::CreateFight($player, $database, $type, $name, $mode, 0, $actionManager);
                if ($createdFight)
                {
                    $eloarena->UpdateFight($player->GetID(), $otherPlayer->GetID());
                    $createdFight->Join($player, 0);
                    $createdFight->Join($otherPlayer, 1);
                    $player->UpdateFight($createdFight->GetID());
                    $otherPlayer->UpdateFight($createdFight->GetID());
                    header('Location: ?p=infight');
                }
                exit();
            }
        }
        $message = "Es konnte kein geeigneter Gegner gefunden werden.";
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'join')
{
    if ($eloarena->IsFighterIn($player->GetID()))
    {
        $message = 'Du bist schon in der Elo-Arena.';
    }
    else if($player->GetBookingDays() > 0)
    {
        $message = "Du kannst die Elo-Arena nicht beitreten wenn du am pausieren bist!";
    }
    else if (!$player->IsVerified())
    {
        $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
    }
    else if ($player->GetLevel() < 5)
    {
        $message = 'Du musst mindestens Level 5 sein um die Elo-Arena betreten zu können.';
    }
    else if($player->GetFight())
    {
        $message = 'Du kannst die Elo-Arena während eines Kampfes nicht betreten.';
    }
    /*else if($player->GetDailyEloFights() >= 5)
    {
        $message = 'Du hast heute bereits 5 Elo-Kämpfe gemacht.';
    }*/
    else
    {
        $eloarena->Join($player->GetID());
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'leave')
{
    if (!$eloarena->IsFighterIn($player->GetID()))
        $message = 'Du bist nicht in der Elo-Arena.';
    else
    {
        $eloarena->Leave($player->GetID());
        $message = 'Du hast die Elo-Arena verlassen.';
    }
}