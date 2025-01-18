<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/fight/attackmanager.php';
    $itemManager = new ItemManager($database);
    $attackManager = new AttackManager($database);

    $pFighter = null;
    if (isset($_GET['fight']))
    {
        $fight = new Fight($database, $_GET['fight'], $player, $actionManager, true);
    }

    if (isset($fight) && $fight->IsStarted())
    {
        $teams = $fight->GetTeams();
    }
    else if (!isset($fight) || !$fight->IsStarted() || $player->GetArank() < 3)
    {
        header('Location: ?p=news');
        exit();
    }