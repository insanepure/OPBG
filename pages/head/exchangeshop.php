<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/player/player.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/header.php';
$title = "Tauschshop";

if(isset($_GET['a']))
{
    if($_GET['a'] == "buy")
    {
        if(!isset($_POST['item']) || !is_numeric($_POST['item']) || $_POST['item'] == 0)
        {
            $message = "Dies ist kein gültiges Item.";
        }
        else if(!isset($_POST['amount']) || !is_numeric($_POST['amount']) || $_POST['amount'] < 0 || $_POST['amount'] > 100)
        {
            $message = "Ungültige Anzahl.";
        }
        else if(!isset($_POST['fruit']) && !isset($_POST['weapon'])
            || isset($_POST['fruit']) && !is_numeric($_POST['fruit'])
            || isset($_POST['weapon']) && !is_numeric($_POST['weapon']))
        {
            $message = "Ungültige Frucht oder Waffe.";
        }
        else
        {
            $amount = $_POST['amount'];
            $itemID = $_POST['item'];
            $pathID = $_POST['fruit'];
            $pathItems = array(52,53,54,55,56,57);
            $fruits = array(52,53,54);
            $weapons = array(55,56,57);

            if(!in_array($itemID, $pathItems) || !in_array($pathID, $pathItems))
            {
                $message = "Diesen Gegenstand kannst du nicht tauschen!";
            }
            else
            {
                if(isset($_POST['price']) && $_POST['price'] < 3)
                {
                    $text = "Manipulationsverdacht: <a href='?p=profil&id=".$player->GetID()."'>".$player->GetName()."</a> - Tauschshop - Veränderter Wert: price - Sollwert: 3 - Istwert: ".$_POST['price'];
                    $player->AddMeldung($text,$player->GetID(),'System',2);
                }
                $fArray = $player->GetInventory()->GetPathItem($pathID);
                if($fArray[1] < $amount)
                {
                    $message = "Du hast nicht genügend ".$fArray[2].".";
                }
                else if($player->GetBerry() < ($amount * 500))
                {
                    $message = "Du hast nicht genügend Berry.";
                }
                else
                {
                    $itemManager = new ItemManager($database);
                    $newItem = $itemManager->GetItem($itemID);
                    $oldItem =  $itemManager->GetItem($pathID);
                    $fruitItem = $player->GetInventory()->GetItemByStatsIDOnly($pathID);
                    if($newItem) {
                        $player->AddItems($newItem, $newItem, $amount);
                        $player->SetBerry($player->GetBerry() - (500 * $amount));
                        $price = (in_array($itemID, $fruits) && in_array($pathID, $weapons) || in_array($pathID, $fruits) && in_array($itemID, $weapons)) ? 1 : 1;
                        $player->GetInventory()->RemoveItem($fruitItem, $amount*$price);
                        $player->AddDebugLog(date('H:i:s', time()) . " - Tauschshop: ".($amount*$price)."x ".$oldItem->GetName()." gegen ".$amount."x ".$newItem->GetName());
                        $message = "Du hast ".($amount * $price)."x ".$fArray[2]." gegen ".$amount." " . $newItem->GetName() . " getauscht.";
                    }
                }
            }
        }
    }
}
