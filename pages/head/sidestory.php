<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/story/sidestory.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/npc/npc.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
$title = 'Neben-Story';
$sidestory = new SideStory($database, $player->GetSideStory());
$groupsidestory = $player->GetGroup();

if (isset($_GET['a']) && $_GET['a'] == 'jump' && $player->GetARank() >= 2 && is_numeric($_POST['sidestoryid']))
{
    $player->JumpStory($_POST['sidestoryid']);
    $sidestory = new SideStory($database, $player->GetSideStory());
}
else if (isset($_GET['a']) && $_GET['a'] == 'continue' && $sidestory->GetType() == 1)
{
    if ($player->GetPlanet() != $sidestory->GetPlanet())
    {
        $message = 'Du befindest dich auf dem falschen Meer.';
    }
    else if ($player->GetPlace() != $sidestory->GetPlace())
    {
        $message = 'Du befindest dich am falschen Ort.';
    }
    else
    {
        $itemManager = new ItemManager($database);
        $player->ContinueSideStory($sidestory->GetLevelup(), $sidestory->GetBerry(), $sidestory->GetGold(), $sidestory->GetItems(), $sidestory->GetSkillpoints(), $sidestory->GetPvP(), $itemManager);
        $sidestory = new SideStory($database, $player->GetSideStory());
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'train' && $story->GetType() == 3)
{
    $itemManager = new ItemManager($database);
    if ($player->GetPlanet() != $sidestory->GetPlanet())
    {
        $message = 'Du befindest dich auf dem falschen Meer.';
    }
    else if ($player->GetPlace() != $sidestory->GetPlace())
    {
        $message = 'Du befindest dich am falschen Ort.';
    }
    else
    {
        $action = $actionManager->GetAction($sidestory->GetAction());
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
else if (isset($_GET['a']) && $_GET['a'] == 'fight' && $sidestory->GetType() == 2)
{
    if ($player->GetPlanet() != $sidestory->GetPlanet())
    {
        $message = 'Du befindest dich im falschen Meeresgebiet.';
    }
    else if ($player->GetPlace() != $sidestory->GetPlace())
    {
        $message = 'Du befindest dich am falschen Ort.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Du befindest dich schon in einem Kampf.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du befindest dich in einem Turnier.';
    }
    else if ($player->GetLP() < $player->GetMaxLP() * 0.2)
    {
        $message = 'Du hast nicht genügend LP.';
    }
    else if ($sidestory->SingleNPC() and isset($groupsidestory) && count($groupsidestory) > 1)
    {
        $message = 'Du kannst diesen Kampf nur alleine bestreiten!';
    }
    else if (isset($groupsidestory) && count($groupsidestory) > $sidestory->GetMaxGroupMembers() && !$sidestory->SingleNPC() && $sidestory->GetMaxGroupMembers() != -1)
    {
        $message = 'Dieser Kampf kann nur mit maximal ' . $sidestory->GetMaxGroupMembers() . ' Gruppenmitglieder bestritten werden!';
    }
    else if (!isset($groupsidestory) && $sidestory->GetMinGroupMembers() != -1)
    {
        $message = 'Dieser Kampf kann nur mit einer Gruppe von mindestens ' . $sidestory->GetMinGroupMembers() . ' Mitgliedern bestritten werden!';
    }
    else if (isset($groupsidestory) && count($groupsidestory) < $sidestory->GetMinGroupMembers() && !$sidestory->SingleNPC() && $sidestory->GetMinGroupMembers() != -1)
    {
        $message = 'Diesen Kampf muss mit mindestens ' . $sidestory->GetMinGroupMembers() . ' Gruppenmitglieder bestritten werden!';
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
                    $gPlayer->GetPlanet() == $player->GetPlanet()
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

        $supportNPCs = $sidestory->GetSupportNPCs();
        $difficulty += count($supportNPCs);
        $npcs = $sidestory->GetNPCs();

        $type = 10; //SideStoryFight
        $mode = $difficulty . 'vs' . count($npcs);
        $name = $sidestory->GetTitel();
        $survivalrounds = $sidestory->GetSurvivalRounds();
        $survivalteam = $sidestory->GetSurvivalTeam();
        $survivalwinner = $sidestory->GetSurvivalWinner();
        $healthRatio = $sidestory->GetHealthRatio();
        $healthRatioTeam = $sidestory->GetHealthRatioTeam();
        $healthRatioWinner = $sidestory->GetHealthRatioWinner();
        $startingHealthRatioPlayer = $sidestory->GetStartingHealthRatioPlayer();
        $startingHealthRatioEnemy = $sidestory->GetStartingHealthRatioEnemy();
        $team = 1;
        $event = 0;
        $healing = 0;
        $eventFight = 0;
        $tournament = 0;
        $npcid = 0;
        $pvp = $sidestory->GetPvP();
        $gold = $sidestory->GetGold();

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
            $sidestory->GetLevelup(),
            $actionManager,
            $sidestory->GetBerry(),
            $pvp,
            $gold,
            $sidestory->GetItems(),
            0,
            $sidestory->GetID(),
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
            LoginTracker::AddInteraction($accountDB, $charaids, 'SideStorykampf', 'opbg');

        if ($createdFight->IsStarted())
            header('Location: ?p=infight');
        exit();
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'quizz' && $sidestory->GetType() == 4)
{
    $answer = $_POST["answer"];
    if ($player->GetPlanet() != $sidestory->GetPlanet())
    {
        $message = 'Du befindest dich im falschen Meeresgebiet.';
    }
    else if ($player->GetPlace() != $sidestory->GetPlace())
    {
        $message = 'Du befindest dich am falschen Ort.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du befindest dich in einem Turnier.';
    }
    else if ($answer != $sidestory->CorrectAnswer())
    {
        $message = 'Die Antwort ist nicht korrekt!';
    }
    else if ($answer != $sidestory->CorrectAnswer())
    {
        $message = 'Deine Antwort ist nicht korrekt!';
    }
    else if ($answer != $sidestory->CorrectAnswer())
    {
        $message = 'Deine Antwort ist nicht korrekt!';
    }
    else
    {
        $itemManager = new ItemManager($database);
        $player->ContinueSideStory($sidestory->GetLevelup(), $sidestory->GetBerry(), $sidestory->GetGold(), $sidestory->GetItems(), $sidestory->GetSkillpoints(), $sidestory->GetPvP(), $itemManager);
        $sidestory = new SideStory($database, $player->GetSideStory());
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