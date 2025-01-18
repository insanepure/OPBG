<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';

$titelManager = new titelManager($database);
$itemManager = new ItemManager($database);
include_once $_SERVER['DOCUMENT_ROOT'].'classes/npc/npc.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/arena/arena.php';
$title = 'NPC';
$place = new Place($database, $player->GetPlace(), $actionManager);
$arena = new Arena($database);
if (isset($_GET['a']) && $_GET['a'] == 'fight')
{
  if ($player->GetFight() != 0)
  {
    $message = 'Du befindest dich schon in einem Kampf.';
  }
  else if ($player->GetTournament() != 0)
  {
    $message = 'Du kannst während eines Turniers nicht kämpfen.';
  }
  else if ($player->GetLP() < $player->GetMaxLP() * 0.2)
  {
    $message = 'Du hast nicht genügend LP. Du brauchst mindestens 20% LP.';
  }
  else if($player->GetBookingDays() > 0)
  {
      $message = "Du kannst während der Pause keine NPC Kämpfe machen!";
  }
  else if ($player->GetKP() < $player->GetMaxKP() * 0.2)
  {
    $message = 'Du hast nicht genügend AD. Du brauchst mindestens 20% AD.';
  }
  else if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  {
    $message = 'Dieser NPC ist ungültig.';
  }
  else if (!isset($_POST['type']) || !is_numeric($_POST['type']) || $_POST['type'] != 0 && $_POST['type'] != 3)
  {
    $message = 'Dieser Typ ist ungültig.';
  }
  else if (!isset($_POST['difficulty']) || !is_numeric($_POST['difficulty']) || $_POST['difficulty'] < 0 || $_POST['difficulty'] > 3)
  {
    $message = 'Diese Schwierigkeit ist ungültig.';
  }
  else
  {
    $difficulty = 1;
    $rdifficulty = $_POST['difficulty'] + 1;
    $npc = new NPC($database, $_GET['id'], $difficulty);

    $npcs = $place->GetNPCs();
    if (!$npc->IsValid() || !in_array($npc->GetID(), $npcs) || $player->GetARank() < 2 && $npc->GetLevel() > $player->GetLevel())
    {
      $message = 'Dieser NPC ist ungültig.';
    }
    else if ($npc->GetMaxEnemy() < $rdifficulty)
    {
      $message = 'Dieser NPC kann nicht mit so vielen Kameraden bekämpft werden!';
    }
    else if($arena->IsFighterIn($player->GetID()))
    {
      $message = 'Du kannst diesen Kampf nicht bestreiten, solange du dich im Kolosseum befindest.';
    }
    else
    {
      $gPlayers = array();
      $group = $player->GetGroup();
      if ($group != null)
      {
        foreach ($group as &$gID)
        {
          $gPlayer = new Player($database, $gID, $actionManager);
          if (
            $gPlayer->GetPlanet() == $player->GetPlanet()
            && $gPlayer->GetLP() > ($gPlayer->GetMaxLP() * 0.2)
            && $gPlayer->GetFight() == 0
            && $gPlayer->GetTournament() == 0
            && $gPlayer->GetID() != $player->GetID()
          )
          {
            $gPlayers[] = $gPlayer;
            if ((count($gPlayers) + 1) == $rdifficulty)
              break;
          }
        }
      }
      $gPlayers[] = $player;

      $type = $_POST['type'];
      $mode = $rdifficulty . 'vs1';
      if ($type == 3)
      {
        $berry = 0;
        if ($npc->GetID() != 35)
        {
          $berry = $npc->GetBerry();
        }
        else if ($npc->GetID() == 35)
        {
          $berry = $player->GetLevel() * $npc->GetBerry();
        }
        $gold = rand(0, $npc->GetGold());
      }
      else
      {
        $berry = 0;
        $gold = 0;
      }
      $pvp = 0;
      $name = 'NPCKampf gegen ' . $npc->GetName();
      $team = 1;
      if ($type == 3)
        $items = $npc->GetItems();
      else
        $items = '';
      $difficulty = 1;
      $survivalrounds = $npc->GetSurvivalRounds();
      $survivalteam = $npc->GetSurvivalTeam();
      $survivalwinner = $npc->GetSurvivalWinner();
      $healthRatio = $npc->GetHealthRatio();
      $healthRatioTeam = $npc->GetHealthRatioTeam();
      $healthRatioWinner = $npc->GetHealthRatioWinner();
      $createdFight = Fight::CreateFight(
        $player,
        $database,
        $type,
        $name,
        $mode,
        0,
        $actionManager,
        $berry,
        $pvp,
        $gold,
        $items,
        0,
        0,
        0,
        $survivalteam,
        $survivalrounds,
        $survivalwinner,
        0,
        0,
        0,
        0,
        $_GET['id'],
        $difficulty,
        $healthRatio,
        $healthRatioTeam,
        $healthRatioWinner
      );
      $createdFight->Join($npc, $team, true);
      $createdFight->Join($player, 0, false);
      $player->SetLastNPCID($npc->GetID());

      foreach ($gPlayers as &$gPlayer)
      {
        if (isset($charaids))
        {
          $charaids[] = $gPlayer->GetID();
        }

        if ($gPlayer->GetID() == $player->GetID())
          continue;

        $createdFight->Join($gPlayer, 0, false);
      }

      if ($createdFight->IsStarted())
      {
        header('Location: ?p=infight');
        exit();
      }
      else
        $message = 'Der Kampf wurde eröffnet.';
    }
  }
}
else if (isset($_GET['a']) && $_GET['a'] == 'fights')
{
    if ($player->GetFight() != 0)
    {
        $message = 'Du befindest dich schon in einem Kampf.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du kannst während eines Turniers nicht kämpfen.';
    }
    else if ($player->GetLP() < $player->GetMaxLP() * 0.2)
    {
        $message = 'Du hast nicht genügend LP. Du brauchst mindestens 20% LP.';
    }
    else if($player->GetBookingDays() > 0)
    {
        $message = "Du kannst während der Pause keine NPC Kämpfe machen!";
    }
    else if ($player->GetKP() < $player->GetMaxKP() * 0.2)
    {
        $message = 'Du hast nicht genügend AD. Du brauchst mindestens 20% AD.';
    }
    else if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    {
        $message = 'Dieser NPC ist ungültig.';
    }
    else if (!isset($_POST['type']) || !is_numeric($_POST['type']) || $_POST['type'] != 0 && $_POST['type'] != 3)
    {
        $message = 'Dieser Typ ist ungültig.';
    }
    else if (!isset($_POST['difficulty']) || !is_numeric($_POST['difficulty']) || $_POST['difficulty'] < 0 || $_POST['difficulty'] > 3)
    {
        $message = 'Diese Schwierigkeit ist ungültig.';
    }
    else
    {
        $difficulty = 1;
        $rdifficulty = $_POST['difficulty'] + 1;
        $npc = new NPC($database, $_GET['id'], $difficulty);

        $npcs = $place->GetNPCs();
        if (!$npc->IsValid() || $player->GetARank() < 2 && $npc->GetLevel() > $player->GetLevel())
        {
            $message = 'Dieser NPC ist ungültig.';
        }
        else if ($npc->GetMaxEnemy() < $rdifficulty)
        {
            $message = 'Dieser NPC kann nicht mit so vielen Kameraden bekämpft werden!';
        }
        else if($arena->IsFighterIn($player->GetID()))
        {
            $message = 'Du kannst diesen Kampf nicht bestreiten, solange du dich im Kolosseum befindest.';
        }
        else
        {
            $gPlayers = array();
            $group = $player->GetGroup();
            if ($group != null)
            {
                foreach ($group as &$gID)
                {
                    $gPlayer = new Player($database, $gID, $actionManager);
                    if (
                        $gPlayer->GetPlanet() == $player->GetPlanet()
                        && $gPlayer->GetLP() > ($gPlayer->GetMaxLP() * 0.2)
                        && $gPlayer->GetFight() == 0
                        && $gPlayer->GetTournament() == 0
                        && $gPlayer->GetID() != $player->GetID()
                    )
                    {
                        $gPlayers[] = $gPlayer;
                        if ((count($gPlayers) + 1) == $rdifficulty)
                            break;
                    }
                }
            }
            $gPlayers[] = $player;

            $type = $_POST['type'];
            $mode = $rdifficulty . 'vs1';
            if ($type == 3)
            {
                $berry = 0;
                if ($npc->GetID() != 35)
                {
                    $berry = $npc->GetBerry();
                }
                else if ($npc->GetID() == 35)
                {
                    $berry = $player->GetLevel() * $npc->GetBerry();
                }
                $gold = rand(0, $npc->GetGold());
            }
            else
            {
                $berry = 0;
                $gold = 0;
            }
            $pvp = 0;
            $name = 'NPCKampf gegen ' . $npc->GetName();
            $team = 1;
            if ($type == 3)
                $items = $npc->GetItems();
            else
                $items = '';
            $difficulty = 1;
            $survivalrounds = $npc->GetSurvivalRounds();
            $survivalteam = $npc->GetSurvivalTeam();
            $survivalwinner = $npc->GetSurvivalWinner();
            $healthRatio = $npc->GetHealthRatio();
            $healthRatioTeam = $npc->GetHealthRatioTeam();
            $healthRatioWinner = $npc->GetHealthRatioWinner();
            $createdFight = Fight::CreateFight(
                $player,
                $database,
                $type,
                $name,
                $mode,
                0,
                $actionManager,
                $berry,
                $pvp,
                $gold,
                $items,
                0,
                0,
                0,
                $survivalteam,
                $survivalrounds,
                $survivalwinner,
                0,
                0,
                0,
                0,
                $_GET['id'],
                $difficulty,
                $healthRatio,
                $healthRatioTeam,
                $healthRatioWinner
            );
            $createdFight->Join($npc, $team, true);
            $createdFight->Join($player, 0, false);
            $player->SetLastNPCID($npc->GetID());

            foreach ($gPlayers as &$gPlayer)
            {
                if (isset($charaids))
                {
                    $charaids[] = $gPlayer->GetID();
                }

                if ($gPlayer->GetID() == $player->GetID())
                    continue;

                $createdFight->Join($gPlayer, 0, false);
            }

            if ($createdFight->IsStarted())
            {
                header('Location: ?p=infight');
                exit();
            }
            else
                $message = 'Der Kampf wurde eröffnet.';
        }
    }
}


