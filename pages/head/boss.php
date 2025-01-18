<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/arena/arena.php';

$title = 'Dungeon';
if (isset($_GET['a']) && $_GET['a'] == 'start')
{
    if (!isset($_POST['id']) || !is_numeric($_POST['id']) || $_POST['id'] <= 0)
    {
        $message = 'Dieses Event ist ungültig.';
    }
    else
    {
        $event = new Event($database, $_POST['id']);
        $arena = new Arena($database);
        if (!$event->IsDungeon())
        {
            $message = 'Das Event ist kein Bosskampf.';
        }
        else if ($event->GetLevel() > $player->GetLevel() && $player->GetARank() < 2)
        {
            $message = 'Dieses Event ist ungültig.';
        }
        else if (!isset($_POST['players']) || !is_numeric($_POST['players']) || $_POST['players'] < $event->GetMinPlayers())
        {
            $message = 'Das Event benötigt mehr Spieler.';
        }
        else if ($_POST['players'] > $event->GetMaxPlayers() || ($player->GetGroup() != '' && $event->GetMaxPlayers() == 1))
        {
            $message = 'Das Event kann nicht mit so vielen Spielern starten.';
        }
        else if (!Event::IsToday($player->GetPlanet(), $player->GetPlace(), $event->GetPlaceAndTime()) && $event->GetID() != 11)
        {
            $message = 'Das Event ist nicht heute, oder du bist am falschen Ort.';
        }
        else if($player->GetGroup() != '' && $event->IsOnPlanetandPlace($player->GetGroup(), $IsPlanet) == false && $event->IsDungeon() == 1)
        {
            $message = "Alle Spieler müssen am selben Ort sein um diesen Dungeon zusammen spielen zu können!";
        }
        else if ($player->GetFight() != 0)
        {
            $message = 'Du befindest dich schon in einem Kampf.';
        }
        else if($player->GetBookingDays() > 0)
        {
            $message = "Du kannst keinem Dungeon beitreten wenn du am pausieren bist!";
        }
        else if ($player->GetTournament() != 0)
        {
            $message = 'Du kannst während des Turniers nichts kämpfen.';
        }
        else if ($player->GetLP() < $player->GetMaxLP() * 0.2)
        {
            $message = 'Du hast nicht genügend LP.';
        }
        else if($arena->IsFighterIn($player->GetID()))
        {
            $message = 'Du kannst diesen Kampf nicht bestreiten, solange du im Kolosseum bist.';
        }
        else
        {
            $group = $player->GetGroup();
            $validPlayers = $event->GetValidPlayers($player, $group, $event->GetLevel(), $event->GetPlanet());
            $players = $_POST['players'];
            if ($validPlayers < $players)
            {
                $message = 'Eines deiner Gruppenmitglieder ist nicht bereit, das könnte daran liegen das ihr nicht am selben Meer seid, nicht geheilt oder das nötige Level habt!';
            }
            else
            {
                $eventFight = $event->GetFight(0);
                $npcs = $eventFight->GetNPCs();
                $type = 5;
                $mode = $players . 'vs' . count($npcs);
                $name = $event->GetName();
                $tournament = 0;
                $npcid = 0;
                $difficulty = 0;
                $eventFightNum = 0;
                $createdFight = Fight::CreateFight(
                    $player,
                    $database,
                    $type,
                    $name,
                    $mode,
                    0,
                    $actionManager,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    $eventFight->GetSurvivalTeam(),
                    $eventFight->GetSurvivalRounds(),
                    $eventFight->GetSurvivalWinner(),
                    $event->GetID(),
                    $eventFight->IsHealing(),
                    $eventFightNum,
                    $tournament,
                    $npcid,
                    $difficulty,
                    $eventFight->GetHealthRatio(),
                    $eventFight->GetHealthRatioTeam(),
                    $eventFight->GetHealthRatioWinner()
                );

                $i = 0;
                $team = 1;
                $addedDifficulty = $event->GetDifficulty() / 100;
                $npcDifficulty = round(1 * $addedDifficulty);
                while ($i != count($npcs))
                {
                    $npc = new NPC($database, $npcs[$i], $npcDifficulty);
                    $createdFight->Join($npc, $team, true);
                    ++$i;
                }

                if($group != '')
                {
                    for($i = 0; $i < $players; $i++)
                    {
                        $groupplayer = new Player($database, $group[$i]);
                        if($groupplayer->GetFight() == 0)
                            $createdFight->Join($groupplayer, 0, false);
                    }
                }
                else
                {
                    if ($players > 1)
                    {
                        $event->Invite($createdFight->GetID(), $group, $player);
                    }
                    else
                        $createdFight->Join($player, 0, false);
                }

                if ($createdFight->IsStarted())
                {
                    header('Location: ?p=infight');
                    exit();
                }
                $message = 'Der Bosskampf wurde gestartet.';
            }
        }
    }
}
