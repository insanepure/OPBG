<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/treasurehunt/treasurehunt.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/treasurehunt/treasurehuntisland.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/treasurehunt/treasurehuntprogress.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/treasurehunt/treasurehuntmanager.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';

    $itemManager = new ItemManager($database);

    $title = 'Schatzsuche';
    $treasurehuntmanager = new treasurehuntmanager($database);
    $treasurehuntprogress = $treasurehuntmanager->LoadPlayerData($player->GetID());
    $treasurehunt = new treasurehunt($database, $treasurehuntprogress->GetTreasurehuntid());

    if(isset($_GET['a']) && $_GET['a'] == 'fight1')
    {
        if($player->GetFight() != 0)
        {
            $message = 'Du befindest dich bereits in einem Kampf.';
        }
        else if($player->GetLP() < ($player->GetMaxLP() * 0.2))
        {
            $message = 'Du benötigst mindestens 20% deiner LP.';
        }
        else if($treasurehuntprogress->GetNPC1() == 0)
        {
            $message = 'Ungültiger NPC';
        }
        else
        {
            $npc = new NPC($database, $treasurehuntprogress->GetNPC1(), 1);
            $type = 12;
            $berry = 0;
            $gold = 0;
            $pvp = 0;
            $name = 'Schatzsuche vs '.$npc->GetName();
            $team = 1;
            $items = $treasurehuntprogress->GetLoot();
            $difficulty = 1;
            $mode = $difficulty . 'vs1';
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
                $npc->GetID(),
                $difficulty,
                $healthRatio,
                $healthRatioTeam,
                $healthRatioWinner
            );
            $createdFight->Join($npc, $team, true);
            $createdFight->Join($player, 0, false);

            if ($createdFight->IsStarted())
            {
                header('Location: ?p=infight');
                exit();
            }
        }
    }
    else if(isset($_GET['a']) && $_GET['a'] == 'fight2')
    {
        if($player->GetFight() != 0)
        {
            $message = 'Du befindest dich bereits in einem Kampf.';
        }
        else if($player->GetLP() < ($player->GetMaxLP() * 0.2))
        {
            $message = 'Du benötigst mindestens 20% deiner LP.';
        }
        else if($treasurehuntprogress->GetNPC2() == 0)
        {
            $message = 'Ungültiger NPC';
        }
        else
        {
            $npc = new NPC($database, $treasurehuntprogress->GetNPC2(), 1);
            $type = 12;
            $berry = 0;
            $gold = 0;
            $pvp = 0;
            $name = 'Schatzsuche vs '.$npc->GetName();
            $team = 1;
            $items = $treasurehuntprogress->GetLoot();
            $difficulty = 1;
            $mode = $difficulty . 'vs1';
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
                $npc->GetID(),
                $difficulty,
                $healthRatio,
                $healthRatioTeam,
                $healthRatioWinner
            );
            $createdFight->Join($npc, $team, true);
            $createdFight->Join($player, 0, false);

            if ($createdFight->IsStarted())
            {
                header('Location: ?p=infight');
                exit();
            }
        }
    }
    else if(isset($_GET['a']) && $_GET['a'] == 'dolphin')
    {
        if(!$treasurehuntprogress->GetDolphin1() && !$treasurehuntprogress->GetDolphin2() && !$treasurehuntprogress->GetDolphin3())
        {
            $message = 'Auf dieser Stufe befindet sich kein Delfin.';
        }
        else if($treasurehuntprogress->GetTreasurehuntid() == 10)
        {
            $message = 'Du bist bereits bei der Höchststufe.';
        }
        else
        {
            $items = explode(';',$treasurehuntprogress->GetLoot());
            $randItem = rand(0, count($items) - 1);
            $item = explode('@', $items[$randItem]);
            $itemID = $item[0];
            $itemamount = $item[1];
            $newItem = $itemManager->GetItem($itemID);
            $treasurehuntmanager->LevelUp($player->GetID());
            $player->AddItems($newItem, $newItem, $itemamount);
            $wonItem = $itemID . '@' . $itemamount . '@' . 0;
            $result = $database->Update('npcwonitems="' . $wonItem . '",npcwonitemtype="99",npcwonitemdungeon="0"', 'accounts', 'id = ' . $player->GetID() . '', 1);
            header('Location: ?p=treasurehunt');
            exit();
        }
    }
    else if(isset($_GET['a']) && $_GET['a'] == 'reset')
    {
        if($player->GetArank() < 2)
        {
            $message = 'Diese Aktion ist ungültig.';
        }
        else
        {
            $treasurehuntmanager->Reset($player->GetID());
            header('Location: ?p=treasurehunt');
            exit();
        }
    }
    else if(isset($_GET['a']) && $_GET['a'] == 'treasure')
    {
        $validItems = array(52,53,54,55,56,57);
        if($treasurehuntprogress->GetTreasurehuntid() % 5 != 0)
        {
            $message = 'Diese Aktion ist ungültig.';
        }
        else if(!isset($_POST['item']) || !is_numeric($_POST['item']) || !in_array($_POST['item'], $validItems))
        {
            $message = 'Das Item ist ungültig.';
        }
        else
        {
            $island = new treasurehuntisland($database, $treasurehuntprogress->GetIsland1());
            $itemID = $_POST['item'];
            $itemamount = explode(';', $island->GetLoot())[1];
            $newItem = $itemManager->GetItem($itemID);
            $treasurehuntmanager->LevelUp($player->GetID());
            $player->AddItems($newItem, $newItem, $itemamount);
            $wonItem = $itemID . '@' . $itemamount . '@' . 0;
            $result = $database->Update('npcwonitems="' . $wonItem . '",npcwonitemtype="12",npcwonitemdungeon="0"', 'accounts', 'id = ' . $player->GetID() . '', 1);
            header('Location: ?p=treasurehunt');
            exit();
        }
    }
    else if(isset($_GET['a']) && $_GET['a'] == 'skip')
    {
        if($player->GetArank() < 2)
        {
            $message = 'Diese Aktion ist ungültig.';
        }
        else if($treasurehuntprogress->GetTreasurehuntid() >= $treasurehuntmanager->GetTreasurehuntCount())
        {
            $message = 'Du bist bereits bei der Höchststufe.';
        }
        else
        {
            $treasurehuntmanager->LevelUp($player->GetID());
            header('Location: ?p=treasurehunt');
            exit();
        }
    }