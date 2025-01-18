<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';

$itemManager = new ItemManager($database);
$items = array(150, 151, 152, 153, 154, 155);

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
    else
    {
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
        else if ($player->GetBerry() < $price)
        {
            $message = 'Du hast nicht genügend Berry.';
        }
        else if ($item->GetNeedItem() != 0 && !$player->HasItem($item->GetNeedItem()))
        {
            $message = 'Du benötigst ein besonderes Item.';
        }
        else if($item->GetID() == 10 && ($amount > 1 || $player->HasTeleschnecke() || $player->HasItemWithID(10, 10))
            || $item->GetID() == 36 && ($amount > 1 || $player->HasEastBlueMap() || $player->HasItemWithID(36, 36))
            || $item->GetID() == 37 && ($amount > 1 || $player->HasNorthBlueMap() || $player->HasItemWithID(37, 37))
            || $item->GetID() == 38 && ($amount > 1 || $player->HasSouthBlueMap() || $player->HasItemWithID(38, 38))
            || $item->GetID() == 39 && ($amount > 1 || $player->HasWestBlueMap() || $player->HasItemWithID(39, 39))
            || $item->GetID() == 72 && ($amount > 1 || $player->HasImpeldownMap() || $player->HasItemWithID(72, 72))
            || $item->GetID() == 85 && ($amount > 1 || $player->HasLogPort() || $player->HasItemWithID(85, 85))) {
            $message = 'Dieses Item kann nur einmal gekauft werden.';
        }
        else
        {
            $statstype = 0;
            $upgrade = 0;
            $player->BuyItem($item, $item, $statstype, $upgrade, $amount, $price);
            $message = 'Du hast ' . number_format($amount,'0', '', '.') . 'x ' . $item->GetName() . ' gekauft.';
            $player->AddDebugLog('');
            $player->AddDebugLog(date('H:i:s', time()) . " - Shopkauf: " . $item->GetName() . " - Anzahl: " . $amount . " - Einzelpreis: " . $item->GetPrice() . " Berry - Gesamtpreis: " . $price . " Berry");
        }
    }
}
