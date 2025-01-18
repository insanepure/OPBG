<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';

$itemManager = new ItemManager($database);
$place = new Place($database, $player->GetPlace(), $actionManager);

$shipArray = array(1, 40, 41, 42, 43, 44, 45, 46, 47, 48, 384, 385);
$pirateShipArray = array(1, 40, 41, 43, 44, 45, 46, 47, 48);
$marineShipArray = array(42, 384, 385);

if (isset($_GET['a']) && $_GET['a'] == 'buy')
{
    if (!isset($_POST['item']) || !is_numeric($_POST['item']))
    {
        $message = 'Das Item ist ungültig.';
    }
    else if (!isset($_POST['amount']) || !is_numeric($_POST['amount']) || floor($_POST['amount']) <= 0)
    {
        $message = 'Die Anzahl ist ungültig.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Du bist in einem Kampf und kannst das Item nicht kaufen.';
    }
    else if ($player->GetInventory()->HasShip() && in_array($_POST['item'], $shipArray))
    {
        $message = "Du besitzt bereits ein Schiff!";
    }
    else if(in_array($_POST['item'], $shipArray) && ($player->GetRace() == "Marine" && !in_array($_POST['item'], $marineShipArray) || $player->GetRace() == "Pirat" && !in_array($_POST['item'], $pirateShipArray)))
    {
        $message = "Dieses Schiff ist für deine Fraktion nicht geeignet.";
    }
    else
    {
        $items = $place->GetItems();
        $i = 0;
        $item = null;
        $amount = floor($_POST['amount']);
        while (isset($items[$i]))
        {
            $idItem = $itemManager->GetItem($items[$i]);
            if ($idItem->GetID() == $_POST['item'])
            {
                $item = $idItem;
                $price = $item->GetPrice() * $amount;
                break;
            }
            ++$i;
        }

        if ($item == null)
        {
            $message = 'Das Item gibt es an diesen Ort nicht.';
        }
        else if ($player->GetGold() < $price)
        {
            $message = 'Du hast nicht genügend Gold.';
        }
        else if ($item->GetNeedItem() != 0 && !$player->HasItem2($item->GetNeedItem()))
        {
            $message = 'Du benötigst ein besonderes Item.';
        }
        else
        {
            $statstype = $item->GetDefaultStatsType();
            $upgrade = 0;
            $player->BuyItem($item, $item, $statstype, $upgrade, $amount, $price);
            $message = 'Du hast ' . number_format($amount,'0', '', '.') . 'x ' . $item->GetName() . ' gekauft.';
            $player->AddDebugLog('');
            $player->AddDebugLog(date('H:i:s', time()) . " - Goldshopkauf: " . $item->GetName() . " - Anzahl: " . $amount . " - Einzelpreis: " . $item->GetPrice() . " Gold - Gesamtpreis: " . $price . " Gold");
        }
    }
}
