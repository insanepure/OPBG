<?php

include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
$inventory = $player->GetInventory();
$itemManager = new ItemManager($database);
$title = 'Ausrüstung';
if(isset($_GET['a']) && $_GET['a'] == 'recycle' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    $id = $_GET['id'];
    $item = $inventory->GetItemByDatabaseID($id);
    $items = $itemManager->GetItem($item->GetStatsID());
    if($items->GetType() != 2 && $items->GetType() != 3 || $items->GetMaxUpgrade() == 0) // 11=> Rüstungshaki / Rüstung aus dem Gold Shop funktionieren nicht
    {
        $message = "Dieser Itemtyp lässt sich nicht recyceln.";
    }
    else if($items->IsPremium() == 1)
    {
        $message = "Du kannst sachen aus dem Gold Shop nicht recyceln.";
    }
    else if($items->GetID() == 11)
    {
        $message = "Du kannst das Rüstungshaki nicht recyceln.";
    }
    else if(!$player->HasItem($id))
    {
        $message = "Du besitzt dieses Item nicht.";
    }
    else
    {
        if($item->GetUpgrade() >= 1 && $item->GetUpgrade() <= 3)
        {
            $itemName = "Einfacher Splitter, Stoff, Holz und Nägel";
            $player->AddItems(51, 51, 1);
            $player->AddItems(49, 49, 1);
            $player->AddItems(50, 50, 1);
            $player->AddItems(315, 315, 1);
        }
        else if($item->GetUpgrade() == 4)
        {
            $itemName = 'Seltenen Splitter, Schmiede W., Stoff, Nägel und Holz';
            $player->AddItems(51, 51, 1);
            $player->AddItems(49, 49, 1);
            $player->AddItems(50, 50, 1);
            $player->AddItems(316, 316, 1);
            $player->AddItems(71, 71, 1);
        }
        else if($item->GetUpgrade() == 5)
        {
            $itemName = 'Legendärer Splitter, Schmiede W. und Rüstungskristall';
            $player->AddItems(71, 71, 1);
            $player->AddItems(174, 174, 1);
            $player->AddItems(317, 317, 1);


        }
        $player->RemoveItems($item, 1);
        $message = "Du hast ".$item->GetName()." erfolgreich recycelt, dafür hast du 1x ".$itemName." erhalten";
    }
}
if (isset($_GET['a']) && $_GET['a'] == 'combine')
{
    if (!isset($_POST['visualitem']) || !isset($_POST['statsitem']) || !is_numeric($_POST['visualitem']) || !is_numeric($_POST['statsitem']))
    {
        $message = 'Diese ID ist ungültig.';
    }
    else
    {
        $visualid = $_POST['visualitem'];
        $statsid = $_POST['statsitem'];
        $visualitem = $inventory->GetItem($visualid);
        $statsitem = $inventory->GetItem($statsid);
        if ($visualitem == null)
        {
            $message = 'Das visuelle Item ist ungültig.';
        }
        else if ($statsitem == null)
        {
            $message = 'Das stats Item ist ungültig.';
        }
        else if ($visualitem->IsEquipped() || $statsitem->IsEquipped())
        {
            $message = 'Ein ausgerüstetes Item kannst du nicht kombinieren.';
        }
        else if ($visualitem->GetSlot() != $statsitem->GetSlot())
        {
            $message = 'Die Items müssen am selben Slot ausgerüstet werden können-';
        }
        else if ($visualitem->GetRace() != '' || $statsitem->GetRace() != '')
        {
            $message = 'Rassenitems können nicht kombiniert werden.';
        }
        else if ($visualitem->GetType() != 3 && $visualitem->GetType() != 4 || $statsitem->GetType() != 3 && $statsitem->GetType() != 4)
        {
            $message = 'Die Items sind keine ausrüstbare Items.';
        }
        else if($visualitem->IsProtected() || $statsitem->IsProtected())
        {
            $message = 'Eines der ausgewählten Gegenstände ist geschützt, es kann nicht kombiniert werden.';
        }
        else
        {
            $player->CombineItems($statsitem, $visualitem);
            $message = 'Du hast die beiden Items kombiniert.';
        }
    }
}
if (isset($_GET['a']) && $_GET['a'] == 'sell')
{
    if (!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = 'Diese ID ist ungültig.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Im Kampf kannst du das Item nicht verkaufen.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Im Turnier kannst du das Item nicht verkaufen.';
    }
    else
    {
        $id = $_POST['id'];
        $item = $inventory->GetItem($id);
        if ($item == null)
        {
            $message = 'Du besitzt dieses Item nicht.';
        }
        else if ($item->IsEquipped())
        {
            $message = 'Ein ausgerüstetes Item kannst du nicht verkaufen.';
        }
        else if (!$item->IsSellable())
        {
            $message = 'Das Item kann man nicht verkaufen.';
        }
        else if($item->IsProtected())
        {
            $message = 'Das Item ist geschützt und kann nicht verkauft werden.';
        }
        else
        {
            $player->SellItem($item, 1);
            $message = 'Du hast ' . $item->GetName() . ' verkauft.';
            $player->AddDebugLog('');
            if($item->IsPremium())
                $player->AddDebugLog(date('H:i:s', time()) . " - Goldshopverkauf: " . $item->GetName() . " - Anzahl: 1 - Einzelpreis: " . $item->GetPrice() . " Gold - Gesamtpreis: " . $item->GetPrice() * 1 . " Gold");
            else
                $player->AddDebugLog(date('H:i:s', time()) . " - Ausrüstung-Shopverkauf: " . $item->GetName() . " - Anzahl: 1 - Einzelpreis: " . $item->GetPrice() . " Berry - Gesamtpreis: " . $item->GetPrice() * 1 . " Berry");
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'equip')
{
    if ((!isset($_POST['item']) || !is_numeric($_POST['item'])) && (!isset($_GET['item']) || !is_numeric($_GET['item'])))
    {
        $message = 'Dieses Item ist ungültig.';
    }
    else
    {
        if(!isset($_GET['item']))
            $id = $_POST['item'];
        else
            $id = $_GET['item'];
        $item = $inventory->GetItem($id);

        if ($player->GetFight() != 0)
        {
            $message = 'Du kannst während des Kampfes nichts anlegen.';
        }
        else if ($player->GetAction() != 0 && $item->GetTrainbonus() > 0)
        {
            $message = 'Du kannst dies während einer Reise oder Training nicht anlegen.';
        }
        else if ($item->GetLevel() > $player->GetLevel())
        {
            $message = 'Dein Level is zu niedrig.';
        }
        else if ($player->GetTournament() != 0)
        {
            $message = 'Du kannst während des Turniers nichts anlegen.';
        }
        else
        {
            if ($item == null)
            {
                $message = 'Du besitzt dieses Item nicht.';
            }
            else
            {
                if ($item->GetSlot() == 0)
                {
                    $message = 'Du kannst dieses Item nicht anlegen.';
                }
                else if ($item->GetRace() != '' && $item->GetRace() != $player->GetRace())
                {
                    $message = 'Du hast nicht die richtige Rasse für das Item.';
                }
                else if ($item->GetType() != 3 && $item->GetType() != 4)
                {
                    $message = 'Du kannst dieses Item nicht ausrüsten.';
                }
                else
                {
                    $player->EquipItem($item);
                    $message = 'Du hast ' . $item->GetName() . '  ausgerüstet.';
                }
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'unequip')
{
    if (!isset($_POST['slot']) || !is_numeric($_POST['slot']))
    {
        $message = 'Dieser Slot ist ungültig.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Du kannst während des Kampfes nichts ablegen.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du kannst während des Turniers nichts ablegen.';
    }
    else
    {
        $slot = $_POST['slot'];
        $item = $inventory->GetItemAtSlot($slot);
        if ($item == null)
        {
            $message = 'Dort befindet sich kein Item.';
        }
        else
        {
            if ($player->GetAction() != 0 && $item->GetTrainbonus() > 0)
            {
                $message = 'Du kannst dies während einer Reise oder Training nicht ablegen.';
            }
            else
            {
                $player->UnequipItem($item);
                $message = 'Du hast ' . $item->GetName() . ' abgelegt.';
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'resetvisual')
{
    if (!isset($_POST['visualitem']) || !is_numeric($_POST['visualitem']))
    {
        $message = 'Dieses Item ist ungültig!';
    }
    else
    {
        $visualid = $_POST['visualitem'];
        $item = $inventory->GetItem($visualid);
        if ($item == NULL)
        {
            $message = 'Dieses Item ist ungültig!';
        }
        else if ($item->IsEquipped())
        {
            $message = 'Du kannst kein ausgerüstetes Item zurücksetzen!';
        }
        else if ($item->GetVisualID() == $item->GetStatsID())
        {
            $message = 'Dieses Item hat kein anderes Aussehen!';
        }
        else if($player->GetBerry() < 2500)
        {
            $message = "Du hast nicht genug Berry um das Item zu entfernen";
        }
        else if($item->IsProtected())
        {
            $message = "Dieses Item ist geschützt.";
        }
        else
        {
            $rechnung = $player->GetBerry() - 2500;
            $result = $database->Update('zeni="'.$rechnung.'"', 'accounts', 'id="'.$player->GetID().'"');
            $statsitem = $itemManager->GetItem($item->GetStatsID());
            $player->RevertCombineItems($item, $statsitem);
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == "protection")
{
    if(!isset($_POST['id']) || !is_numeric($_POST['id']) || !isset($_POST['action']) || $_POST['action'] != 'protect' && $_POST['action'] != 'unprotect')
    {
        $message = "Fehler: Ungültige(r) Parameter";
    }
    else {
        $item = $inventory->GetItem($_POST['id']);
        if ($item == NULL) {
            $message = "Dieses Item ist ungültig!";
        }
        else
        {
            if($_POST['action'] == "protect")
            {
                $item->SetProtected(1);
                $database->Update('protected=1', 'inventory', 'id='.$item->GetID());
                $message = "Der Ausrüstungsgegenstand ".$item->GetName()." ist nun geschützt.";
            }
            else
            {
                $item->SetProtected(0);
                $database->Update('protected=0', 'inventory', 'id='.$item->GetID());
                $message = "Der Ausrüstungsgegenstand ".$item->GetName()." ist nicht länger geschützt.";
            }
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == "delete")
{
    if($player->GetArank() < 2)
    {
        $message = 'Du hast nicht die nötigen Rechte.';
    }
    else
    {
        foreach ($inventory->GetItems() as $item)
        {
                if ($item->GetType() == 3 && !$item->IsEquipped() && !$item->IsProtected() && !$item->IsStored())
                {
                    $inventory->RemoveItem($item, $item->GetAmount());
                }
            $message = 'Die Ausrüstungen wurden gelöscht.';
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == "bulksell")
{
    if(!isset($_POST['ids']))
    {
        $message = "Fehlende Parameter";
    }
    else if($_POST['ids'] == '')
    {
        $message = 'Falscher Parameter';
    }
    else
    {
        $items = explode(";", $_POST['ids']);
        $priceBerry = 0;
        $priceGold = 0;
        foreach($items as $sItem)
        {
            $itemID = $inventory->GetItemID($sItem);
            $item = $inventory->GetItem($itemID);
            if(!$item->IsSellable() || $item->IsProtected() || $item->IsEquipped() || $item->IsStored() || ($item->GetType() != 3 && $item->GetType() != 4))
            {
                $message = "Eines der ausgewählten Gegenstände kann nicht verkauft werden.";
            }
            else
            {
                $player->SellItem($item);
                if($item->IsPremium())
                    $priceGold += round($item->GetPrice() / 2);
                else
                    $priceBerry += round($item->GetPrice() / 2);
            }
        }
        $message = "Du hast alle Gegenstände für ";
        if($priceBerry > 0)
            $message .= number_format($priceBerry, 0, '', '.') .' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
        if($priceGold > 0)
        {
            if($priceBerry > 0)
                $message .= ' und ';
            $message .= number_format($priceGold, 0, '', '.') . ' <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>';
        }
        $message .= ' verkauft.';
    }
}
else if(isset($_GET['a']) && $_GET['a'] == "bulkrecycle")
{
    if(!isset($_POST['ids']))
    {
        $message = "Fehlende Parameter";
    }
    else if($_POST['ids'] == '')
    {
        $message = 'Falscher Parameter';
    }
    else
    {
        $items = explode(";", $_POST['ids']);
        $recitems = array();
        $recycled = false;
        foreach($items as $sItem)
        {
            $itemID = $inventory->GetItemID($sItem);
            $item = $inventory->GetItem($itemID);
            if($item->IsPremium() == 1 || $item->GetID() == 11 || $item->IsEquipped() || $item->IsStored() || ($item->GetType() != 3 && $item->GetType() != 4))
            {
                $message = "Eines der ausgewählten Gegenstände kann nicht verkauft werden.";
            }
            else
            {
                $itemNames = array();
                if($item->GetUpgrade() >= 1 && $item->GetUpgrade() <= 3)
                {
                    $itemNames[] = "Einfacher Splitter";
                    $itemNames[] = "Stoff";
                    $itemNames[] = "Holz";
                    $itemNames[] = "Nägel";
                    $player->AddItems(51, 51, 1);
                    $player->AddItems(49, 49, 1);
                    $player->AddItems(50, 50, 1);
                    $player->AddItems(315, 315, 1);


                }
                else if($item->GetUpgrade() == 4)
                {
                    $itemNames[] = "Seltener Splitter";
                    $itemNames[] = "Schmiede W.";
                    $itemNames[] = "Stoff";
                    $itemNames[] = "Nägel";
                    $itemNames[] = "Holz";
                    $player->AddItems(51, 51, 1);
                    $player->AddItems(49, 49, 1);
                    $player->AddItems(50, 50, 1);
                    $player->AddItems(316, 316, 1);
                    $player->AddItems(71, 71, 1);

                }
                else if($item->GetUpgrade() == 5)
                {
                    $itemNames[] = "Legendärer Splitter";
                    $itemNames[] = "Schmiede W.";
                    $itemNames[] = "Rüstungskristall";
                    $newItem = $itemManager->GetItem(317);
                    $newItems = $itemManager->GetItem(71);
                    $newItemss = $itemManager->GetItem(174);
                    $player->AddItems($newItem, $newItem, 1);
                    $player->AddItems($newItems, $newItems, 1);
                    $player->AddItems($newItemss, $newItemss, 1);
                }
                $player->RecycleEquip($item->GetID());
                $inventory = new Inventory($database, $player->GetID());
                $found = false;
                foreach ($recitems as $key => $recitem) {
                    foreach($itemNames as $itemName) {
                        if ($recitem[1] == $itemName) {
                            $recitems[$key][0]++;
                            $found = true;
                        }
                    }
                    break;
                }
                if(!$found) {
                    foreach ($itemNames as $itemName) {
                        $recitems[] = array(1, $itemName);
                    }
                }
                $recycled = true;
            }
        }
        if($recycled) {
            $message = "Du hast durch das recyclen folgende Gegenstände erhalten: ";
            if (!empty($recitems)) {
                $it = 0;
                foreach ($recitems as $recitem) {
                    if($it == 0)
                        $message .= $recitem[0] . "x " . $recitem[1];
                    else
                        $message .= ", " . $recitem[0] . "x " . $recitem[1];
                    ++$it;
                }
            }
        }
    }
}

function ShowSlotEquippedImage($slot, $inventory, $zorders, $zordersOnTop)
{
    $item = $inventory->GetItemAtSlot($slot);
    if ($item != null)
    {
        if ($item->IsOnTop())
            $zindex = $zordersOnTop[$slot];
        else
            $zindex = $zorders[$slot];
        if($item->GetEquippedImage() != '')
        {
            ?>
            <div class="char2" style="z-index:<?php echo $zindex; ?>; background-image:url('img/ausruestung/<?php echo $item->GetEquippedImage(); ?>.png?005')"></div>
            <?php
        }
    }
}

function ShowSlot($player, $slot, $inventory, $itemManager)
{
    $item = $inventory->GetItemAtSlot($slot);

    if ($item != null)
    {
        global $database;
        $statsItem = $itemManager->GetItem($item->GetStatsID());
        ?>
        <div class="tooltip" style="position: relative; top:0; left:0; width: 170px; z-index: 100;">
            <font size="2"><?php echo $item->GetName(); ?></font>
            <div class="spacer"></div>
            <div style="width:50px; height:50px; position:relative; top:-5px; left:-25px;">
                <?php if ($item->HasOverlay())
                {
                    ?>
                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png?002" style="width:50px;height:50px; position:absolute; z-index:1;">
                    <?php
                }
                ?>
                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png?002" style="width:50px;height:50px; position:absolute; z-index:0;">
            </div>
            <form method="POST" action="?p=ausruestung&a=unequip">
                <input type="hidden" name="slot" value="<?php echo $slot; ?>">
                <input type="hidden" name="item" value="<?php echo $item->GetID(); ?>">
                <input type="submit" value="Ablegen">
            </form>
            <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -15px; bottom: 70px;">
                    <?php echo $item->GetName(); ?>
                    <hr/>
                    <?php
                    if($item->GetStatsID() != $item->GetVisualID())
                        echo 'Stats von ' . $statsItem->GetName();
                    ?>
                    <div style="width:100%; height: 90px; top:0; left:-40px; position:relative;">
                        <?php if ($item->HasOverlay())
                        {
                            ?>
                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png?002" style="width:80px;height:80px; position: absolute; z-index:1;">
                            <?php
                        }
                        ?>
                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png?002" style="width:80px;height:80px; position: absolute; z-index:0;">
                    </div>
                    <span>
                        <?php
                        echo $item->DisplayEffect();
                        ?>
                    </span>
                    <?php
                    if($item->GetLevel() > 0)
                    {
                        ?>
                        <span>
                                  Benötigtes Level: <?php echo number_format($item->GetLevel(), 0, '', '.'); ?>
                              </span>
                        <?php
                    }
                    ?>
                </span>
        </div>
        <?php
    }
}
?>