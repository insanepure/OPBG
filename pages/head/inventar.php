<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
$inventory = $player->GetInventory();
$itemManager = new ItemManager($database);

$shipArray = array(1, 40, 41, 42, 43, 44, 45, 46, 47, 48, 384, 385);
$sonstigeItems = array(
    36, // Karte Eastblue
    73, // Kiribachi
    37, // Karte Northblue
    38, // Karte Southblue
    39, // Karte Westblue
    72, // Karte Impeldown
    85, // Logport
    10, // Teleschnecke
    //353, // Casino Rabatt Coupon (100%)
    //352, // Casino Rabatt Coupon (75%)
    //351, // Casino Rabatt Coupon (50%)
    //350, // Casino Rabatt Coupon (25%)
    30, // Skill Reset
    9, // Stats Reset
    405, // Halloween Stats Reset
    137, // Kerkerschlüssel
    388, // Impel Down Level 1 Schlüssel
    389, // Impel Down Level 2 Schlüssel
    390, // Impel Down Level 3 Schlüssel
    391, // Impel Down Level 3 Schlüssel
    392, // Impel Down Level 3 Schlüssel
    393, // Impel Down Level 3 Schlüssel
    119, // Rumble Ball
    104, // Stat Geist
    //315, // Einfacher Splitter
    //316, // Seltener Splitter
    //317, // Legendärer Splitter
    302, // Reiseticket
    223, // Star Stats
    222, // Spalter
    224, // Splitter eines Nonosama Bo
    226, // Gan Forts Schlüssel
    399, // South Bird
    510, // Vivrecard
);
$schiffItems = array(
    49, // Holz
    50, // Nägel
    51, // Stoff
    1, // Boot
    40, // Going Merry
    41, // Big Top
    42, // Marine Schiff
    43, // Thousand Sunny
    44, // Nostra Castello
    45, // Red Force
    46, // Die Mobby Dick
    47, // Oro Jackson
    48, // Victoria Punk
    384, // Aokiji's Fahrrad
    385 // Garp's Kampfschiff
);
$fightItems = array(
    86, //Seltene Rote Frucht
    87, // Seltene Orangene Frucht
    88, // Seltene Gelbe Frucht
    81, // Testo Booster
    82, // Vitamine
    406, // Grüne Wolke
    407 // Rote Wolke
);
$aufwertungsItems = array(
    71, // Schmied W.
    174, // Rüstungskristall
    180, // Halloween Rüstungskristall
    181 // Halloween Schmied W.
);

if (isset($_GET['a']) && $_GET['a'] == 'use')
{
    if(!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        //$message = 'Diese ID ist ungültig.';
    }
    $id = $_POST['id'];
    $amount = $database->EscapeString($_POST['amount']);
    $item = $inventory->GetItem($id);
    if ($item == null)
    {
        $message = 'Du besitzt dieses Item nicht.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Im Kampf kannst du das Item nicht nutzen.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Im Turnier kannst du das Item nicht nutzen.';
    }
    else
    {
        /*if ((!isset($_POST['amount']) || $_POST['amount'] <= 0 || $_POST['amount'] > $item->GetAmount())
            && $item->GetStatsID() != 81 && $item->GetStatsID() != 82 && $item->GetStatsID() != 60 && $item->GetStatsID() != 102 && $item->GetStatsID() != 104
        )*/
        if($item->GetCategory() == 1 && $item->GetKP() > 0 && $player->GetKP() >= $player->GetMaxKP() && $item->GetLP() == 0)
        {
            $message = 'Du hast bereits volle AD.';
        }
        else if($item->GetCategory() == 1 && $item->GetLP() > 0 && $player->GetLP() >= $player->GetMaxLP() && $item->GetKP() == 0)
        {
            $message = 'Du hast bereits volle LP.';
        }
        else if($item->GetCategory() == 1 && $item->GetLP() > 0 && $player->GetLP() >= $player->GetMaxLP() && $item->GetKP() > 0 && $player->GetKP() >= $player->GetMaxKP())
        {
            $message = 'Du hast bereits volle LP und AD.';
        }
        else if(!is_numeric($amount))
        {
            $message = "Die anzahl ist ungültig!";
        }
        else if($amount > $item->GetAmount())
        {
            $text = $player->GetName().' hat versucht mehr Tränke zu nutzen als er besitzt';
            $player->Track($text, $player->GetID(), 'system', 2);
            $message = "Du besitzt nicht so viel wie du versuchst zu nutzen!";
        }
        else
        {
            if ($item->GetType() != 5 && ($item->GetType() == 1 || $item->GetType() == 2 || $item->GetType() == 6))
            {
                if (!isset($_POST['amount'])) $amount = 1;
                else $amount = floor($_POST['amount']);
                if (isset($_POST['item']))
                {
                    if (!is_numeric($_POST['item']) || $_POST['item'] == '0')
                    {
                        $message = 'Das Item auf dem du es anwenden willst existiert nicht.';
                        header('Location: ?p=inventar&a=use');
                    }
                    else
                    {
                        $onItem = $inventory->GetItemByDatabaseID($_POST['item']);
                        if ($onItem == null)
                            $message = 'Das Item auf dem du es anwenden willst existiert nicht.';
                        else if ($item->GetStatsID() == 184 && !$onItem->CanChangeType())
                            $message = 'Du kannst den Typ von diesem Item nicht ändern.';
                        else if (($item->GetStatsID() == 71 || $item->GetStatsID() == 181)  && !$onItem->CanUpgrade())
                            $message = 'Du kannst den Level von diesem Item nicht ändern.';
                        else if (($item->GetStatsID() == 71 || $item->GetStatsID() == 174) && ($onItem->GetType() != 3 && $onItem->GetType() != 4 || $onItem->IsEquipped()))
                            $message = 'Das Item auf dem du es anwenden willst ist ungültig.';
                        else if(($item->GetStatsID() == 71 || $item->GetStatsID() == 174) && $amount > $item->GetAmount())
                        {$text = $player->GetName().' hat versucht mehr Schmide W. oder Rüstungskristalle einzusetzen als er besitzt';
                            $player->Track($text, $player->GetID(), 'system', 2);}
                        else
                        {
                            $return = $player->UseItem2($id, $amount, $_POST['item']);
                            if (($item->GetStatsID() == 71 || $item->GetStatsID() == 174) && !is_null($return))
                            {
                                return $message = $return;
                            }
                        }
                    }
                }
                else
                {
                    if ($item->GetStatsID() == 81 || $item->GetStatsID() == 82 || $item->GetStatsID() == 86 || $item->GetStatsID() == 87 || $item->GetStatsID() == 88 || $item->GetStatsID() == 406 || $item->GetStatsID() == 407)
                    {
                        $message = 'Dieses Item kann nur im Kampfmenü verwendet werden.';
                        return;
                    }
                    else
                    {
                        if ($item->GetStatsID() == 104 && $player->GetStats() == 0)
                            return $message = "Du hast keine Statpunkte zu verteilen, das Item hätte keinen Effekt.";
                        else if ($item->GetStatsID() == 104 && $player->GetAssignedStats() != 2000)
                            return $message = "Du kannst noch Statpunkte verteilen, das Item hätte keinen Effekt.";
                        else if ($item->GetStatsID() == 119 && (((10 * ceil($player->GetTotalStatsfights() / 10)) - 10) == $player->GetTotalStatsfights() || $player->GetTotalStatsfights() == 0))
                            return $message = 'Bitte führe vorher mindestens einen PvP-Kampf aus.';
                        else if($item->GetStatsID() >= 388 && $item->GetStatsID() <= 393 && $player->GetPlanet() != 2)
                            return $message = 'Du kannst dieses Item nur in Impel Down verwenden.';
                        else if($item->GetStatsID() == 388 && $player->GetPlace() != 10 ||
                            $item->GetStatsID() == 389 && $player->GetPlace() != 11 ||
                            $item->GetStatsID() == 390 && $player->GetPlace() != 12 ||
                            $item->GetStatsID() == 391 && $player->GetPlace() != 13 ||
                            $item->GetStatsID() == 392 && $player->GetPlace() != 14 ||
                            $item->GetStatsID() == 393 && $player->GetPlace() != 15)
                            return $message = 'Du kannst diesen Schlüssel auf dieser Ebene nicht verwenden.';
                        else if($item->GetStatsID() == 10 && $player->HasTeleschnecke())
                            return $message = 'Dieses Item kann nur einmal genutzt werden.';
                        else if($item->GetStatsID() == 223)
                        {
                            if($player->GetStats() != 0)
                                return $message = 'Du musst alle Stats verteilen, bevor du das Item benutzen kannst.';
                            else if($player->GetAction() != 0)
                                return $message = 'Du musst alle Aktionen beenden, bevor du das Item benutzen kannst.';
                            //return $message = 'Dieser Gegenstand kann derzeit nicht erworben werden.';
                            // Variable declarations
                            $actionStats = 0;
                            $eventStats = 0;
                            $levelStats = ($player->GetLevel() - 1) * 25;
                            $statFightStats = (floor($player->GetTotalStatsFights() / 10) * 10);
                            // --------------------

                            // Calculation of finished actions and dungeons
                            $getSpecialActionStats = $database->Select('*', 'actions', 'type=15 AND level <= "' . $player->GetLevel() . '"');
                            if ($getSpecialActionStats) {
                                while ($specialActionStats = $getSpecialActionStats->fetch_assoc()) {
                                    if ($player->HasSpecialTrainingUsed($specialActionStats['id'])) {
                                        $actionStats += $specialActionStats['stats'];
                                    }
                                }
                            }
                            if (!empty($player->GetExtraDungeons())) {
                                $dungeons = $player->GetExtraDungeons();
                                foreach ($dungeons as &$dungeon) {
                                    $event = new Event($database, $dungeon[0]);
                                    if ($event->GetStats() > 0) {
                                        $eventStats += $event->GetStats() * $dungeon[1];
                                    }
                                }
                            }
                            // -------------------------------------------

                            // Get Statpoints since game-start
                            $statsSinceStart = 0;
							$time = $gameStartTime;
                            $now = new DateTime("now");
                            $hoursSinceOpening = floor(($now->getTimestamp() - $time) / 3600);
                            if ($hoursSinceOpening >= 1)
                                $statsSinceStart = $hoursSinceOpening * 6;

                            $statsSinceStart = $statsSinceStart + 0;
                            // -------------------------------

                            // Current Statpoints by assigned Stats
                            $rlp = $player->GetMaxLP() / 10;
                            $rkp = $player->GetMaxKP() / 10;
                            $ratk = $player->GetAttack() / 2;
                            $rdef = $player->GetDefense();
                            $rechnung = $rlp + $rkp + $ratk + $rdef;
                            // ----------------------------------

                            // Alle Stats vom Player
                            $newplayer = $statsSinceStart + $eventStats + $levelStats + $actionStats + $statFightStats;
                            $oldplayer = $rechnung;
                            $different = $newplayer - $oldplayer;
                            $different = abs($different);
                            if($different <= 0)
                            {
                                return $message = "Du bist bereits auf dem aktuellsten Stand, das Item bringt dir keine weiteren Statspunkte.";
                            }
                        }
                        $return = $player->UseItem2($id, $amount);
                        if ($item->GetStatsID() == 119 && isset($return))
                        {
                            $message = $return;
                            return;
                        }
                    }
                }
                $message = 'Du hast ' . number_format($amount,'0', '', '.') . 'x ' . $item->GetName() . ' benutzt.';
            }
            else if ($item->GetType() == 7)
            {
                if(!isset($_POST['amount'])) {
                    $amount = 1;
                }
                else if($_POST['amount'] > $item->GetAmount())
                {
                    $text = $player->GetName()." hat versucht die Anzahl seiner ".$item->GetName()." auf ".$_POST['amount']." zu ändern hat aber nur ".$item->GetAmount()."";
                    $player->Track($text, $player->GetID(), 'System', 2);
                    return;

                }
                else {
                    $amount = $_POST['amount'];
                }
                $wartung = false;
                if ($wartung == true && $player->GetArank() < 2)
                {
                    $message = "Derzeit finden Wartungsarbeiten innerhalb der Kiste statt, du hörst nur ein Poltern aus dem inneren. (Du kannst du Kiste aktuell nicht öffnen...)";
                    return;
                }
                else if(($item->GetStatsID() == 356 || $item->GetStatsID() == 357 || $item->GetStatsID() == 358 || $item->GetStatsID() == 359 || $item->GetStatsID() == 360) && (!isset($_POST['item']) || !is_numeric($_POST['item']) || $itemManager->GetItem($_POST['item']) == null))
                {
                    $message = "Das ausgewählte Item ist ungültig!";
                    return;
                }

                $pickedItem = null;
                if($item->GetStatsID() == 356 || $item->GetStatsID() == 357 || $item->GetStatsID() == 358 || $item->GetStatsID() == 359 || $item->GetStatsID() == 360)
                {
                    $items = array(
                        356=>array($itemManager->GetItem(326),$itemManager->GetItem(325),$itemManager->GetItem(321),$itemManager->GetItem(322),$itemManager->GetItem(323),$itemManager->GetItem(324)),
                        357=>array($itemManager->GetItem(328),$itemManager->GetItem(327),$itemManager->GetItem(331),$itemManager->GetItem(330),$itemManager->GetItem(329),$itemManager->GetItem(332)),
                        358=>array($itemManager->GetItem(337),$itemManager->GetItem(334),$itemManager->GetItem(333),$itemManager->GetItem(335),$itemManager->GetItem(336),$itemManager->GetItem(338)),
                        359=>array($itemManager->GetItem(341),$itemManager->GetItem(344),$itemManager->GetItem(342),$itemManager->GetItem(340),$itemManager->GetItem(339),$itemManager->GetItem(343)),
                        360=>array($itemManager->GetItem(349),$itemManager->GetItem(348),$itemManager->GetItem(345),$itemManager->GetItem(346),$itemManager->GetItem(347),$itemManager->GetItem(361)));

                    if(!in_array($itemManager->GetItem($_POST['item']), $items[$item->GetStatsID()]))
                    {
                        $message = "Das ausgewählte Item ist ungültig!";
                        return;
                    }
                }

                $pickedItem = $_POST['item'];

                $chestItems = $player->ChestOpen($item->GetStatsID(), $amount, $pickedItem);

                $items = "";
                $berryEntry = "";
                $goldEntry = "";
                $nichtsEntry = "";
                while ($citem = current($chestItems))
                {
                    $itemData = null;
                    if(key($chestItems) != 'Berry' && key($chestItems) != 'Gold' && key($chestItems) != 'Nichts')
                    {
                        $itemData = $itemManager->GetItemByName(key($chestItems));
                    }
                    if($itemData != null)
                    {
                        $items .= '<tr><td>';

                        if ($itemData->HasOverlay())
                        {
                            $items .= '<img class="boxSchatten borderT borderR borderL borderB" src="img/items/'.$itemData->GetOverlay().'.png" alt="'.$itemData->GetName().'" title="'.$itemData->GetName().'" style="width:60px;height:60px; z-index:1; position: relative;"><img class="boxSchatten borderT borderR borderL borderB" src="img/items/'.$itemData->GetImage().'.png" alt="'.$itemData->GetName().'" title="'.$itemData->GetName().'" style="width:60px;height:60px; z-index:0; position: relative; top: -65.6px;">';
                        }
                        else
                        {
                            $items .= '<img class="boxSchatten borderT borderR borderL borderB" src="img/items/'.$itemData->GetImage().'.png" alt="'.$itemData->GetName().'" title="'.$itemData->GetName().'" style="width:60px;height:60px; z-index:0;">';
                        }

                        $items .= '<span style="position:relative; right:25px; bottom: 8px; font-size:24px; color:#000; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;"><b>'.number_format($citem, '0', '', '.').'</b></span></td><td align="center"><span style="padding-left: 20px;">'.$itemData->GetName().'</span></td>';
                    }
                    else if(key($chestItems) == 'Berry')
                    {
                        $berryEntry .= '<tr><td>'.number_format($citem, 0 , '', '.').'</td><td><img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="height: 30px; width: 20px;"/></td></tr>';
                    }
                    else if(key($chestItems) == 'Gold')
                    {
                        $goldEntry .= '<tr><td>'.number_format($citem, 0 , '', '.').'</td><td><img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="height: 30px; width: 30px;"/></td></tr>';
                    }
                    else if(key($chestItems) == 'Nichts')
                    {
                        $nichtsEntry .= '<tr><td>'.number_format($citem, 0 , '', '.').'x</td><td> leider nichts</td></tr>';
                    }
                    next($chestItems);
                }
                if ($item->GetStatsID() != 103)
                {
                    if ($amount == 1)
                    {
                        if($chestItems['Nichts'] >= '1')
                        {
                            $message = 'Du hast in der Kiste leider nichts gefunden.';
                        }
                        else
                            $message = 'Du hast in der Kiste <table>' . $items . $berryEntry . $goldEntry . $nichtsEntry . '</table> gefunden.';
                    }
                    else
                        $message = 'Du hast in den Kisten <table>' . $items . $berryEntry . $goldEntry . $nichtsEntry . '</table> gefunden.';
                }
                else
                {
                    if ($amount == 1)
                        $message = 'Der Sack war mit <table>' . $items . $berryEntry . $goldEntry . '</table> gefüllt.';
                    else
                        $message = 'Die Säcke waren mit <table>' . $items . $berryEntry . $goldEntry . '</table> gefüllt.';
                }
                ?>
                <script>
                    if (window.history.replaceState) {
                        window.history.replaceState(null, null, window.location.href);
                    }
                </script>
                <?php
            }
            else if($item->GetType() == 5 && $item->GetStatsID() == 302)
            {
                if(!isset($_POST['amount'])) {
                    $amount = 1;
                }
                    else {
                        $amount = $_POST['amount'];
                    }
                if($player->GetTravelTicket() == 0)
                {
                    $result = $player->UseItem2($id, $amount);
                    $message = "Du hast das Reiseticket erfolgreich eingelöst";
                }
                else
                {
                    $message = "Du hast bereits ein Ticket aktiviert, nutze es erstmal bevor du ein weiteres aktivierst";
                }
            }
            else if($item->GetType() == 5 && $item->GetStatsID() == 350)
            {
                if(!isset($_POST['amount']))
                    $amount = 1;
                else
                    $amount = $_POST['amount'];
                $rabattcheck = 25 * $amount;
                $rabatt = $player->GetRabatt() + $rabattcheck;
                if($rabatt > 100)
                {
                    $message = "Man kann sich maximal 100% Rabatt eintragen, bitte nutze diesen erstmal bevor du weitere Coupons einlöst";
                }
                else
                {
                    $player->UseItem2($id, $amount);
                    $message = "Du hast nun ".$rabatt."% Rabatt auf dein nächsten Casino Besuch!";
                }
            }
            else if($item->GetType() == 5 && $item->GetStatsID() == 351)
            {
                if(!isset($_POST['amount']))
                    $amount = 1;
                else
                    $amount = $_POST['amount'];
                $rabattcheck = 50 * $amount;
                $rabatt = $player->GetRabatt() + $rabattcheck;
                if($rabatt > 100)
                {
                    $message = "Man kann sich maximal 100% Rabatt eintragen, bitte nutze diesen erstmal bevor du weitere Coupons einlöst";
                }
                else
                {
                    $player->UseItem2($id, $amount);
                    $message = "Du hast nun ".$rabatt."% Rabatt auf dein nächsten Casino Besuch!";
                }
            }
            else if($item->GetType() == 5 && $item->GetStatsID() == 352)
            {
                if(!isset($_POST['amount']))
                    $amount = 1;
                else
                    $amount = $_POST['amount'];
                $rabattcheck = 75 * $amount;
                $rabatt = $player->GetRabatt() + $rabattcheck;
                if($rabatt > 100)
                {
                    $message = "Man kann sich maximal 100% Rabatt eintragen, bitte nutze diesen erstmal bevor du weitere Coupons einlöst";
                }
                else
                {
                    $player->UseItem2($id, $amount);
                    $message = "Du hast nun ".$rabatt."% Rabatt auf dein nächsten Casino Besuch!";
                }
            }
            else if($item->GetType() == 5 && $item->GetStatsID() == 353)
            {
                if(!isset($_POST['amount']))
                    $amount = 1;
                else
                    $amount = $_POST['amount'];
                $rabattcheck = 100 * $amount;
                $rabatt = $player->GetRabatt() + $rabattcheck;
                if($rabatt > 100)
                {
                    $message = "Man kann sich maximal 100% Rabatt eintragen, bitte nutze diesen erstmal bevor du weitere Coupons einlöst";
                }
                else
                {
                    $player->UseItem2($id, $amount);
                    $message = "Du hast nun ".$rabatt."% Rabatt auf dein nächsten Casino Besuch!";
                }
            }
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'vivrecard')
{
    $npcs = $database->EscapeString($_POST['npc']);
    $npc = new NPC($database, $npcs);
    if(!$npcs)
    {
        $message = "Du musst einen NPC aussuchen!";
    }
    else if($npc->GetVivrecard() != 0)
    {

        $message = "Du kannst diesen NPC nicht nehmen!";
    }
    else if($player->HasVivreCard())
    {

        $message = "Du besitzt keine Vivrecard!";
    }
    else
    {

        $player->SetVivrecard($npcs);
        $player->GiveItem(510,1);
        $message = "Du hast die Vivrecard erfolgreich genutzt, der NPC ".$npc->GetName()." wartet bereits auf dich!";
    }

}
else if (isset($_GET['a']) && $_GET['a'] == 'ship')
{
    if (!isset($_GET['do']))
    {
        $message = 'Diese Aktion ist ungültig.';
    }
    else if (!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = 'Diese ID ist ungültig.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Im Kampf kannst du kein Schiff verlassen.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Im Turnier kannst du kein Schiff verlassen!';
    }
    else
    {
        $id = $_POST['id'];
        $item = $inventory->GetItem($id);

        if ($item == null)
        {
            $message = "Du besitzt dieses Schiff nicht!";
        }
        else if($_GET['do'] == 0)
        {
            if (!$inventory->HasShipEquipped($item->GetID()))
            {
                $message = "Du besetzt dieses Schiff nicht!";
            }
            else
            {
                $inventory->SetHasShip(-1, $item->GetID());
                $message = "Du hast ";
                if($item->GetRealName() == 'Boot')
                {
                    $message .= "das";
                }
                else
                    $message .= "die";
                $message .= " " . $item->GetRealName() . " verlassen!";
            }
        }
        else if ($_GET['do'] == 1)
        {
            if ($inventory->HasShipEquipped($item->GetID()))
            {
                $message = "Du besetzt bereits ein Schiff!";
            }
            else
            {
                $inventory->SetHasShip($item->GetID());
                $message = "Du hast ";
                if($item->GetRealName() == 'Boot')
                {
                    $message .= "das";
                }
                else if($item->GetStatsID() != 384 && $item->GetStatsID() != 385)
                    $message .= "die";
                $message .= " " . $item->GetRealName() . " besetzt!";
            }
        }
        else if ($_GET['do'] == 2)
        {
            if ($item->GetWear() < $itemManager->GetItem($item->GetStatsID())->GetItemUses())
            {
                $message = 'Dieses Schiff benötigt keine Reparatur!';
            }
            else
            {
                $neededItems = array(
                    1 => array(1, 1, 1),
                    40 => array(7, 4, 4),
                    41 => array(3, 2, 2),
                    42 => array(10, 6, 4),
                    43 => array(10, 8, 8),
                    44 => array(20, 6, 1),
                    45 => array(30, 6, 7),
                    46 => array(35, 6, 1),
                    47 => array(40, 8, 8),
                    48 => array(50, 10, 9),
                    384 => array(1, 1, 1),
                    385 => array(10, 6, 4)
                );

                $woodItem = $inventory->GetItemByStatsIDOnly(49);
                $needleItem = $inventory->GetItemByStatsIDOnly(50);
                $fabricItem = $inventory->GetItemByStatsIDOnly(51);

                if (
                    $woodItem != null && $woodItem->GetAmount() >= $neededItems[$item->GetStatsID()][0]
                    && $needleItem != null && $needleItem->GetAmount() >= $neededItems[$item->GetStatsID()][1]
                    && $fabricItem != null && $fabricItem->GetAmount() >= $neededItems[$item->GetStatsID()][2]
                )
                {
                    $woodItem->SetAmount($woodItem->GetAmount() - $neededItems[$item->GetStatsID()][0]);
                    $needleItem->SetAmount($needleItem->GetAmount() - $neededItems[$item->GetStatsID()][1]);
                    $fabricItem->SetAmount($fabricItem->GetAmount() - $neededItems[$item->GetStatsID()][2]);
                    $item->SetWear(0);
                    $item->SetRepaircount($item->GetRepairCount() + 1);
                    $message = $inventory->Repair($neededItems[$item->GetStatsID()][0], $neededItems[$item->GetStatsID()][1], $neededItems[$item->GetStatsID()][2], $item);
                }
                else
                {
                    $message = "Dir fehlen Materialien um die Reparatur durchführen zu können!";
                }
            }
        }
        else if ($_GET['do'] == 3)
        {
            $ShipsArray = array(1, 40, 41, 42, 43, 44, 45, 46, 47, 48, 384, 385);
            if($item->GetWear() < $inventory->GetShipMaxWear($item->GetStatsID()))
            {
                $message = 'Das Schiff kann noch genutzt werden';
            }
            else if($item->IsEquipped())
            {
                $message = 'Du besetzt das Schiff noch!';
            }
            else if(!in_array($item->GetStatsID(), $ShipsArray))
            {
                $message = 'Das ist kein Schiff!';
            }
            else
            {
                $inventory->RemoveItem($item, 1);
                $message = $inventory->Recycle();
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'sell')
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
        else if ($item->IsSellable() != 1)
        {
            $message = 'Dieses Item kann nicht verkauft werden.';
        }
        else if ($item->IsEquipped())
        {
            $message = 'Ein ausgerüstet Item kannst du nicht verkaufen.';
        }
        else if($item->GetStatsID() == 86 || $item->GetStatsID() == 87 || $item->GetStatsID() == 406 || $item->GetStatsID() == 407)
        {
            $message = 'Die seltenen Früchte können nicht an das System verkauft werden!';
        }
        else if (!isset($_POST['amount']) || floor($_POST['amount']) <= 0 || floor($_POST['amount']) > $item->GetAmount())
        {
            $message = 'Die Anzahl ist ungültig.';
        }
        else if ($item->GetWear() >= 1 || $item->GetRepairCount() >= 1)
        {
            $message = 'Das Item kann nicht verkauft werden wenn es abgenutzt wurde oder bereits einmal repariert wurde.';
        }
        else
        {
            $amount = floor($_POST['amount']);
            $player->SellItem($item, $amount);
            $message = 'Du hast ' . number_format($amount,'0', '', '.') . 'x ' . $item->GetName() . ' verkauft.';
            $player->AddDebugLog('');
            if($item->IsPremium()) {
                $player->AddDebugLog(date('H:i:s', time()) . " - Goldshopverkauf: " . $item->GetName() . " - Anzahl: " . $amount . " - Einzelpreis: " . floor($item->GetPrice() / 2) . " Gold - Gesamtpreis: " . floor($item->GetPrice() / 2) * $amount . " Gold");
            }
            else {
                $player->AddDebugLog(date('H:i:s', time()) . " - Shopverkauf: " . $item->GetName() . " - Anzahl: " . $amount . " - Einzelpreis: " . floor($item->GetPrice() / 2) . " Berry - Gesamtpreis: " . floor($item->GetPrice() / 2) * $amount . " Berry");
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'delete')
{
    if((!isset($_GET['type']) || (!is_numeric($_GET['type']) && $_GET['type'] != 'sonstige' && $_GET['type'] != 'ship' && $_GET['type'] != 'fight' && $_GET['type'] != 'upgrade') || is_numeric($_GET['type']) && ($_GET['type'] < 1 || $_GET['type'] > 10)) && (!isset($_GET['category']) || !is_numeric($_GET['category']) || $_GET['category'] < 1 || $_GET['category'] > 10))
    {
        $message = 'Diese Kategorie kann nicht gelöscht werden.';
    }
    else if($player->GetArank() < 2)
    {
        $message = 'Du hast nicht die nötigen Rechte.';
    }
    else
    {
        if(isset($_GET['type']))
            $type = $_GET['type'];
        if(isset($_GET['category']))
            $category = $_GET['category'];
        foreach ($inventory->GetItems() as $item)
        {
            if(isset($type))
            {
                if($type == 1 && ($item->GetType() == 1 || $item->GetType() == 2))
                {
                    $inventory->RemoveItem($item, $item->GetAmount());
                }
                else if($type == 7 && $type == $item->GetType())
                {
                    $inventory->RemoveItem($item, $item->GetAmount());
                }
                else if($type == 'sonstige' && in_array($item->GetStatsID(), $sonstigeItems))
                {
                    $inventory->RemoveItem($item, $item->GetAmount());
                }
                else if($type == 'ship' && in_array($item->GetStatsID(), $schiffItems))
                {
                    $inventory->RemoveItem($item, $item->GetAmount());
                }
                else if($type == 'fight' && in_array($item->GetStatsID(), $fightItems))
                {
                    $inventory->RemoveItem($item, $item->GetAmount());
                }
                else if($type == 'upgrade' && in_array($item->GetStatsID(), $aufwertungsItems))
                {
                    $inventory->RemoveItem($item, $item->GetAmount());
                }
            }
            else if(isset($category))
            {
                if($category == 5 && $category == $item->GetCategory())
                {
                    $inventory->RemoveItem($item, $item->GetAmount());
                }
            }
            $message = 'Die Items wurden gelöscht.';
        }
    }
}