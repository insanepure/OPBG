<?php

include_once $_SERVER['DOCUMENT_ROOT'].'classes/story/story.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/npc/npc.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/arena/arena.php';
$story = new Story($database, $player->GetStory());
$arena = new Arena($database);
$groupstory = $player->GetGroup();

if (isset($_GET['a']) && $_GET['a'] == 'jump' && $player->GetARank() >= 2 && is_numeric($_POST['storyid']))
{
    $story = new Story($database, $_POST['storyid']);
	$player->JumpStory($story);
}
else if (isset($_GET['a']) && $_GET['a'] == 'continue' && $story->GetType() == 1)
{
	if ($player->GetPlanet() != $story->GetPlanet() && $player->GetArank() < 2)
	{
		$message = 'Du befindest dich auf dem falschen Meer.';
	}
	else if ($player->GetPlace() != $story->GetPlace() && $player->GetArank() < 2)
	{
		$message = 'Du befindest dich am falschen Ort.';
	}
	else
	{
		$itemManager = new ItemManager($database);
		$player->ContinueStory($story->GetLevelup(), $story->GetBerry(), $story->GetGold(), $story->GetItems(), $story->GetSkillpoints(), $story->GetPvP(), $itemManager);
		$story = new Story($database, $player->GetStory());

        #if($player->GetArank() >= 2)
        #{
            #$player->SetPlanet($story->GetPlanet());
            #$player->SetPlace($story->GetPlace());
           # $database->Update('planet="'.$story->GetPlanet().'", place="'.$story->GetPlace().'"', 'accounts', 'id='.$player->GetID());
        #} Wenn ein Teammitglied beim Springen an den Ort switchen soll.
	}
}
else if (isset($_GET['a']) && $_GET['a'] == 'train' && $story->GetType() == 3)
{
	$itemManager = new ItemManager($database);
	if ($player->GetPlanet() != $story->Getplanet() && $player->GetArank() < 2)
	{
		$message = 'Du befindest dich auf dem falschen Meer.';
	}
	else if ($player->GetPlace() != $story->GetPlace() && $player->GetArank() < 2) 
	{
		$message = 'Du befindest dich am falschen Ort.';
	}
	else
	{
		$action = $actionManager->GetAction($story->GetAction());
		$minutes = $action->GetMinutes();
		$price = $action->GetPrice();
		$item = $action->GetItem();
		$race = $action->GetRace();
		if ($item != 0 && !$player->HasItemWithID($item->GetID(), $item->GetID()))
		{
			$message = 'Du hast das benötigte Item nicht.';
		}
		else if ($action->GetLevel() > $player->GetLevel())
		{
			$message = 'Dein Level ist zu niedrig.';
		}
		else if ($price > $player->GetBerry())
		{
			$message = 'Du hast nicht genug Berry.';
		}
		else if ($player->GetAction() != 0)
		{
			$message = 'Du tust bereits etwas.';
		}
		else if ($race != '' && $player->GetRace() != $race)
		{
			$message = 'Du kannst diese Aktion nicht machen, sie gehört einer anderen Rasse.';
		}
        else if($player->HasSpecialTrainingUsed($action->GetID()))
        {
            $message = 'Du hast die Aktion bereits durchgeführt.';
        }
		else
		{
			$player->DoAction($action, $minutes);
			if ($action->GetItem() != 0)
			{
				$statstype = 0;
				$upgrade = 0;
				$amount = 1;
				$player->RemoveItemsByID($action->GetItem(), $action->GetItem(), $statstype, $upgrade, $amount);
			}
			$message = 'Du führst die Aktion nun aus.';
		}
	}
}
else if (isset($_GET['a']) && $_GET['a'] == 'fight' && $story->GetType() == 2)
{
	if ($player->GetPlanet() != $story->GetPlanet() && $player->GetArank() < 2)
	{
		$message = 'Du befindest dich im falschen Meeresgebiet.';
	}
	else if ($player->GetPlace() != $story->GetPlace() && $player->GetArank() < 2)
	{
		$message = 'Du befindest dich am falschen Ort.';
	}
	else if ($player->GetFight() != 0)
	{
		$message = 'Du befindest dich schon in einem Kampf.';
	}
    else if(is_numeric($story->GetNPCs()) && $player->IsMultiChar() && $player->GetStory() != $story->GetID() && !is_null($player->GetGroup()))
    {
        $message = "Du kannst diesen Kampf nur in einer Gruppe bestreiten, wenn du selbst diesen Storyabschnitt abschließen musst.";
    }
    else if($player->IsMultiChar() && $story->GetMaxGroupMembers() >= 3 && count($groupstory) > 3 && !$story->SingleNPC())
    {
        $message = "Du kannst mit einem Multichar die Story maximal zu 2 Spielen";
    }
	else if ($player->GetTournament() != 0)
	{
		$message = 'Du befindest dich in einem Turnier.';
	}
	else if ($player->GetLP() < $player->GetMaxLP() * 0.2)
	{
		$message = 'Du hast nicht genügend LP.';
	}
	else if ($story->SingleNPC() && isset($groupstory) && count($groupstory) > 1)
	{
		$message = 'Du kannt diesen Kampf nur alleine bestreiten!';
	}
	else if (isset($groupstory) && count($groupstory) > $story->GetMaxGroupMembers() && !$story->SingleNPC() && $story->GetMaxGroupMembers() != -1)
	{
		$message = 'Dieser Kampf kann nur mit maximal ' . $story->GetMaxGroupMembers() . ' Gruppenmitglieder bestritten werden!';
	}
	else if (!isset($groupstory) && $story->GetMinGroupMembers() != -1)
	{
		$message = 'Dieser Kampf kann nur mit einer Gruppe von mindestens ' . $story->GetMinGroupMembers() . ' Mitgliedern bestritten werden!';
	}
	else if (isset($groupstory) && count($groupstory) < $story->GetMinGroupMembers() && !$story->SingleNPC() && $story->GetMinGroupMembers() != -1)
	{
		$message = 'Diesen Kampf muss mit mindestens ' . $story->GetMinGroupMembers() . ' Gruppenmitglieder bestritten werden!';
	}
	else if($arena->IsFighterIn($player->GetID()))
	{
		$message = 'Du kannst diesen Kampf nicht bestreiten, solange du im Kolosseum bist.';
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
				#if(($gPlayer->GetLevel() > $player->GetLevel()) && $player->GetID() != $gPlayer->GetID())
				#{
				#	$message = "Du kannst diesen Kampf nur mit Gruppenmitglieder bestreiten, die das gleiche Level haben wie du, oder schwächer sind.";
				#	return;
				#}
				if (
                    ($gPlayer->GetPlanet() == 2 && $story->GetPlanet() == 2
                    || $gPlayer->GetPlanet() != 2 && $story->GetPlanet() != 2)
                    && $gPlayer->GetLP() > ($gPlayer->GetMaxLP() * 0.2)
                    && $gPlayer->GetFight() == 0
                    && $gPlayer->GetTournament() == 0
                    && $gPlayer->GetID() != $player->GetID()
                )
				{
					$gPlayers[] = $gPlayer;
				}
			}
		}
		$gPlayers[] = $player;
		$difficulty = count($gPlayers);

		$supportNPCs = $story->GetSupportNPCs();
		$difficulty += count($supportNPCs);
		$npcs = $story->GetNPCs();

		$type = 4; //StoryFight
		$mode = $difficulty . 'vs' . count($npcs);
		$name = $story->GetTitel();
		$survivalrounds = $story->GetSurvivalRounds();
		$survivalteam = $story->GetSurvivalTeam();
		$survivalwinner = $story->GetSurvivalWinner();
		$healthRatio = $story->GetHealthRatio();
		$healthRatioTeam = $story->GetHealthRatioTeam();
		$healthRatioWinner = $story->GetHealthRatioWinner();
		$startingHealthRatioPlayer = $story->GetStartingHealthRatioPlayer();
		$startingHealthRatioEnemy = $story->GetStartingHealthRatioEnemy();
		$team = 1;
		$event = 0;
		$healing = 0;
		$eventFight = 0;
		$tournament = 0;
		$npcid = 0;
		$pvp = $story->GetPvP();
		$gold = $story->GetGold();

		$pLP = $player->GetLP() * ($startingHealthRatioPlayer / 100);
		$pLP = floor($pLP);
		$player->SetLP($pLP);

		//$difficulty = ceil($difficulty / count($npcs));
		$difficulty = 1;

		$npcsArray = array();
		foreach ($npcs as &$npcID)
		{
			$npc = new NPC($database, $npcID, $difficulty);
			$nLP = $npc->GetRawLP() * ($startingHealthRatioEnemy / 100);
			$nLP = floor($nLP);
			$npc->SetLP($nLP);
			$npcsArray[] = $npc;
		}

		$createdFight = Fight::CreateFight(
			$player,
			$database,
			$type,
			$name,
			$mode,
			$story->GetLevelup(),
			$actionManager,
			$story->GetBerry(),
			$pvp,
			$gold,
			$story->GetItems(),
			$story->GetID(),
			0,
			0,
			$survivalteam,
			$survivalrounds,
			$survivalwinner,
			$event,
			$healing,
			$eventFight,
			$tournament,
			$npcid,
			$difficulty,
			$healthRatio,
			$healthRatioTeam,
			$healthRatioWinner
		);

		foreach ($npcsArray as &$npc)
		{
			$createdFight->Join($npc, $team, true);
		}
		$createdFight->Join($player, 0, false);

		if (count($supportNPCs) != 0)
		{
			foreach ($supportNPCs as &$supportNPCID)
			{
				$supportNPC = new NPC($database, $supportNPCID, 1);
				$createdFight->Join($supportNPC, 0, true);
			}
		}

		$charaids = array();

		foreach ($gPlayers as &$gPlayer)
		{
			$charaids[] = $gPlayer->GetID();

			if ($gPlayer->GetID() == $player->GetID())
				continue;

			$pLP = $gPlayer->GetLP() * ($startingHealthRatioPlayer / 100);
			$pLP = floor($pLP);
			$gPlayer->SetLP($pLP);

			$createdFight->Join($gPlayer, 0, false);
		}

		if (count($charaids) > 1)
			LoginTracker::AddInteraction($accountDB, $charaids, 'Storykampf', 'opbg');

		if ($createdFight->IsStarted())
			header('Location: ?p=infight');
		exit();
	}
}
else if (isset($_GET['a']) && $_GET['a'] == 'quiz' && $story->GetType() == 4)
{
    $answers = $_POST["answer"];
    $correct = $story->CorrectAnswers();
    if ($player->GetPlanet() != $story->GetPlanet() && $player->GetArank() < 2)
    {
        $message = 'Du befindest dich im falschen Meeresgebiet.';
    }
    else if ($player->GetPlace() != $story->GetPlace() && $player->GetArank() < 2)
    {
        $message = 'Du befindest dich am falschen Ort.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du befindest dich in einem Turnier.';
    }
    else
    {
        $canContinue = true;
        foreach ($answers as $answer) {
            if (!in_array($answer, $correct)) {
                $message = 'Die Antwort ist nicht korrekt!';
                $canContinue = false;
            }
        }
        foreach ($correct as $answer) {
            if (!in_array($answer, $answers)) {
                $message = 'Die Antwort ist nicht korrekt!';
                $canContinue = false;
            }
        }
        if($canContinue) {
            $itemManager = new ItemManager($database);
            $player->ContinueStory($story->GetLevelup(), $story->GetBerry(), $story->GetGold(), $story->GetItems(), $story->GetSkillpoints(), $story->GetPvP(), $itemManager);
            $story = new Story($database, $player->GetStory());
        }
    }
}
function ShowPlayer($player)
{
    ?>
    <div class="SideMenuBar" style="margin-left:0px;float:left;min-height:0px;">
        <div class="SideMenuContainer borderT borderL borderB borderR">
            <div class="SideMenuKat catGradient borderB">
                <div class="schatten">
                    <a href="?p=profil&id=<?php echo $player->GetID(); ?>"><?php echo $player->GetName(); ?></a>
                </div>
            </div>
            <div class="SideMenuInfo" style="max-height: 156.5px;">
                <div class="char_main">
                    <div class="spacer" style="max-height: 5px;"></div>
                    <div class="char_image smallBG borderT borderB borderR borderL">
                        <img alt="<?php echo $player->GetName(); ?>" title="<?php echo $player->GetName(); ?>" src="<?php echo $player->GetImage(); ?>" width="99%" height="99%">
                    </div>
                </div>
            </div>
        </div>
        <div class="spacer"></div>
    </div>
    <?php
}
?>