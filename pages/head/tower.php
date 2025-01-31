<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tower/tower.php';
if(!isset($player) || !$player->IsValid() || $player->GetARank() < 2)
{
	header('Location: ?p=news');
	exit();  
}

$tower = new Tower($database);
$totalFloors = $tower->GetFloorCount();

if(isset($_GET['a']) && $_GET['a'] == 'start')
{
    if($player->GetFight() != 0)
    {
      $message = 'Du befindest dich schon in einem Kampf.';
    }
		else if($player->GetTournament() != 0)
		{
			$message = 'Du kannst während des Turnieres nichts kämpfen.';
		}
    else if($player->GetLP() < $player->GetMaxLP() * 0.2)
    {
      $message = 'Du hast nicht genügend LP.';
    }
    else if(!is_numeric($_POST['towerfloor']))
    {
        $message = "Fehler";
    }
    else
    {
      $players = 1;
      $towerfloor = $_POST['towerfloor'];
      $startFloor = 1;
      if($towerfloor != 'Alle')
        $startFloor = $towerfloor;
      $towerFloor = $tower->GetFloor($startFloor);
      if($towerfloor != 'Alle')
        $startFloor = 1;
      $npcs = $towerFloor->GetNPCs();
      $type = 9;
      if($towerfloor != 'Alle')
        $type = 9;
			$mode = $players.'vs'.count($npcs);
			$name = 'Turmkampf';
      $tournament=0;
      $dragonball=0;
      $npcid=0;
      $difficulty=0;
      $survivalTeam = 0;
      $survivalRounds = 0;
      $survivalWinner = 0;
      $isHealing = 0;
      $healthRatio = 0;
      $healthRatioTeam = 0;
      $healthRatioWinner = 0;
      $fightNum = 0;
      $eventid = 0;
			$createdFight = Fight::CreateFight($player, $database, $type, $name, $mode, 0, $actionManager, 0, '', 0, 0,$survivalTeam
                                         ,$survivalRounds, $survivalWinner, $eventid, $isHealing, $startFloor
                                         , $tournament, $npcid, $difficulty, $healthRatio,$healthRatioTeam, $healthRatioWinner);
      
      $i = 0;
			$team = 1;
      $npcDifficulty = $players;
      while($i != count($npcs))
      {
        $npc = new NPC($database, $npcs[$i], $npcDifficulty);
        $createdFight->Join($npc, $team, true);
        ++$i;
      }
      $createdFight->Join($player, 0, false);
      
      if($createdFight->IsStarted())
      {
        header('Location: ?p=infight');
			  exit();
      }
      $message = 'Du hast den Turm betreten.';
    }
}
?>