<?php
    if(!$halloweenEventActive && $player->GetArank() < 2)
    {
        header("Location: ?p=news");
    }
    $title = 'Halloween Shop';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
    $itemManager = new ItemManager($database);
    $place = new Place($database, $player->GetPlace(), $actionManager);

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
            $items = $place->GetEventItems();
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
            else if ($player->GetMünzen() < $price)
            {
                $message = 'Du hast nicht genügend Kürbismünzen.';
            }
            else if ($item->GetNeedItem() != 0 && !$player->HasItem($item->GetNeedItem()))
            {
                $message = 'Du benötigst ein besonderes Item.';
            }
            else
            {
                $statstype = 0;
                $upgrade = 0;
                $player->BuyEventItem($item, $item, $statstype, $upgrade, $amount, $price);
                $message = 'Du hast ' . number_format($amount,'0', '', '.') . 'x ' . $item->GetName() . ' gekauft.';
            }
        }
    }
