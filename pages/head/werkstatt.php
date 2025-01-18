<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/player/player.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/header.php';

if(isset($_GET['a']))
{
    if($_GET['a'] == "buy")
    {
        if(!isset($_POST['item']) || $_POST['item'] == 0)
        {
            $message = "Dies ist kein gültiges Item.";
        }
        else if(!isset($_POST['amount']) || !is_numeric($_POST['amount']) || floor($_POST['amount']) <= 0 || floor($_POST['amount']) > 100)
        {
            $message = "Ungültige Anzahl.";
        }
        else
        {
            $amount = floor($_POST['amount']);
            $itemID = $_POST['item'];
            $nItems = array();
            $sItems = array();
            $lItems = array();
            $result = $database->Select('*', 'items', 'einfachesplitter != 0 OR seltenesplitter != 0 OR legendaeresplitter != 0');
            if($result)
            {
                while($row = $result->fetch_assoc())
                {
                    if($row['einfachesplitter'] != 0)
                        $nItems[] = array($row['id'], $row['einfachesplitter']);
                    if($row['seltenesplitter'] != 0)
                        $sItems[] = array($row['id'], $row['seltenesplitter']);
                    if($row['legendaeresplitter'] != 0)
                        $lItems[] = array($row['id'], $row['legendaeresplitter']);
                }

                if(!in_array_r($itemID, $nItems) && !in_array_r($itemID, $sItems) && !in_array_r($itemID, $lItems))
                {
                    $message = "Diesen Gegenstand kannst du mit Splittern nicht kaufen!";
                }
                else
                {
                    if(in_array_r($itemID, $nItems))
                    {
                        foreach($nItems as $item)
                        {
                            if($item[0] == $itemID)
                            {
                                if($player->GetInventory()->GetEinfacheSplitter() < ($item[1] * $amount))
                                {
                                    $message = "Du hast nicht genügend einfache Splitter.";
                                }
                                else
                                {
                                    $itemManager = new ItemManager($database);
                                    $newItem = $itemManager->GetItem($item[0]);
                                    if($newItem) {
                                        $player->AddItems($newItem, $newItem, $amount);
                                        $player->GetInventory()->RemoveEinfacheSplitter(($item[1] * $amount));
                                        $message = "Du hast ".$amount."x ".$newItem->GetName()." für ".($item[1] * $amount)." einfache Splitter gekauft.";
                                    }
                                }
                                break;
                            }
                        }
                    }

                    if(in_array_r($itemID, $sItems))
                    {
                        foreach($sItems as $item)
                        {
                            if($item[0] == $itemID)
                            {
                                if($player->GetInventory()->GetSelteneSplitter() < ($item[1] * $amount))
                                {
                                    $message = "Du hast nicht genügend seltene Splitter.";
                                }
                                else
                                {
                                    $itemManager = new ItemManager($database);
                                    $newItem = $itemManager->GetItem($item[0]);
                                    if($newItem) {
                                        $player->AddItems($newItem, $newItem, $amount);
                                        $player->GetInventory()->RemoveSelteneSplitter(($item[1] * $amount));
                                        $message = "Du hast ".$amount."x ".$newItem->GetName()." für ".($item[1] * $amount)." seltene Splitter gekauft.";
                                    }
                                }
                                break;
                            }
                        }
                    }

                    if(in_array_r($itemID, $lItems))
                    {
                        foreach($lItems as $item)
                        {
                            if($item[0] == $itemID)
                            {
                                if($player->GetInventory()->GetLegendaereSplitter() < ($item[1] * $amount))
                                {
                                    $message = "Du hast nicht genügend legendäre Splitter.";
                                }
                                else
                                {
                                    $itemManager = new ItemManager($database);
                                    $newItem = $itemManager->GetItem($item[0]);
                                    if($newItem) {
                                        $player->AddItems($newItem, $newItem, $amount);
                                        $player->GetInventory()->RemoveLegendaereSplitter(($item[1] * $amount));
                                        $message = "Du hast ".$amount."x ".$newItem->GetName()." für ".($item[1] * $amount)." legendäre Splitter gekauft.";
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
    if($_GET['a'] == "repairboat")
    {
        $boat = $_POST['id'] ?? 0;
        if($boat == 1 && $player->GetInventory()->GetEinfacheSplitter() < 10 || $boat == 40 && $player->GetInventory()->GetSelteneSplitter() < 10)
        {
            $message = "Du hast nicht genug Splitter";
        }
        else if(!isset($_POST['id']) || $_POST['id'] != 1 && $_POST['id'] != 40)
        {
            $message = "Das ist kein gültiges Boot/Schiff";
        }
        else if(!$player->GetInventory()->HasItemWithID($boat, $boat))
        {
            $message = "Du besitzt dieses Schiff nicht";
        }
        else if($boat == 1 && $player->GetInventory()->GetShipWear(1) < 2 || $boat == 40 && $player->GetInventory()->GetShipWear(40) < 10)
        {
            $message = "Du kannst das Boot noch nutzen";
        }
        else if($boat == 1 && $player->GetInventory()->GetShipRepairCount(1) >= 2 || $boat == 40 && $player->GetInventory()->GetShipRepairCount(40) >= 3)
        {
            $message = "Du hast das Schiff schon zu oft repariert!";
        }
        else
        {
            $ship = $player->GetInventory()->GetItemByIDOnly($boat, $boat);
            $player->GetInventory()->Repair(0, 0, 0, $ship);
            if($boat == 1)
                $player->GetInventory()->RemoveEinfacheSplitter(10);
            else
                $player->GetInventory()->RemoveSelteneSplitter(10);
            $message = "Dein Schiff wurde erfolgreich repariert";
        }
    }
}

function in_array_r($needle, $haystack, $strict = false): bool
{
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}
