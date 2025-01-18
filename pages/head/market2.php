<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/market/market.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
    $title = 'Marktplatz';
    $market = new Market($database);
    $inventory = $player->GetInventory();

    if (!isset($player) || !$player->IsValid())
    {
        header('Location: ?p=news');
        exit();
    }
