<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/items/itemmanager.php';
$inventory = $player->GetInventory();
$itemManager = new ItemManager($database);

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    exit();

$item = $inventory->GetItem($_GET['id']);
if ($item == null)
    exit();

$berry = '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
$gold = '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>';

if ($_GET['action'] != 1)
{
    ?>
    <div style="width:100px; height:100px; position:relative; top:0px; left:0px;">
        <?php if ($item->HasOverlay())
        {
            ?>
            <img src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="left:0px; top:0px; position:absolute; z-index:1;"><br />
            <?php
        }
        ?>
        <img src="img/items/<?php echo $item->GetImage(); ?>.png" style="left:0px; top:0px; position:absolute; z-index:0;"><br />
    </div>
    <br />
    <?php
    echo '<b>' . $item->GetName() . '</b><br/>';
    echo $item->GetDescription();
    ?>
    <hr>
    <div class="spacer"></div>
    <?php
    if ($item->IsPremium())
        echo 'Preis: <b>' . number_format($item->GetPrice(),'0', '', '.') . ' ' . $gold .'</b><br/>';
    else
        echo 'Preis: <b>' . number_format($item->GetPrice(),'0', '', '.') . ' ' . $berry .'</b><br/>';
    ?>
    <hr>
    <div class="spacer"></div>
    <?php
    echo $item->DisplayEffect();
    if($item->GetStatsID() == 223) // Stat Münze
    {
        // Variable declarations
        $actionStats = 0;
        $eventStats = 0;
        $promoStats = 0;
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
        $different = $newplayer - ($oldplayer + $player->GetStats());
        if($different < 0) $different = 0;
        $different = abs($different);

        echo "Du besitzt ".number_format($oldplayer, 0, '', '.')." Stats<br />";
        echo "Ein neuer Spieler bekommt ".number_format($newplayer, 0, '', '.')." Stats<br />";
        //echo "Zum start: ".number_format($statsSinceStart, 0, '', '.')."<br/>";
        //echo "Stats durch Spezialtraining ".number_format($actionStats, 0, '', '.')."<br/>";
        //echo "Stats durch Events ".number_format($eventStats, 0, '', '.')."<br/>";
        //echo "Stats durch Story ".number_format($levelStats, 0, '', '.')."<br/>";
        //echo "Stats durch Statsfight ".number_format($statFightStats, 0, '', '.')."<br/>";
        //echo "Stats durch PromoCodes: ".$promoStats."<br />";
        echo 'Verfügbare Punkte: '.number_format($different,0,',','.');
    }
// Stat Münze ENDE
    if ($item->GetLevel() != 0)
    {
        echo 'Benötigt Level ' . number_format($item->GetLevel(),'0', '', '.') . '<br/><hr>';
    }
    ?>
    <div class="spacer"></div>
    <form method="POST" action="?p=inventar&a=use">
        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
        <table>
            <?php
            if ($item->GetStatsID() == 71 || $item->GetStatsID() == 181)
            {
                ?>
                <tr>
                    <td>Item:</td>
                    <td>
                        <select style="width:100px" class="select" name="item">
                            <?php
                            $i = 0;
                            $onItem = $inventory->GetItem($i);
                            $counter = 0;
                            while (isset($onItem))
                            {
                                if ($onItem->GetType() != 3 && $onItem->GetType() != 4 || $onItem->IsEquipped() || $onItem->GetUpgrade() > 3 || $item->GetStatsID() == 184 && !$onItem->CanChangeType() || $item->GetStatsID() == 71 && !$onItem->CanUpgrade() || $item->GetStatsID() == 181 && !$onItem->CanUpgrade()|| $item->GetStatsID() == 180 && !$onItem->CanUpgrade() || $onItem->IsStored())
                                {
                                    ++$i;
                                    $onItem = $inventory->GetItem($i);
                                    continue;
                                }
                                $counter++;
                                ?>
                                <option value="<?php echo $onItem->GetID(); ?>"><?php echo $onItem->GetName(); ?></option>
                                <?php
                                ++$i;
                                $onItem = $inventory->GetItem($i);
                            }
                            if($counter == 0)
                                echo '<option value="0">Kein Item möglich</option>';
                            ?>
                        </select>
                        <?php
                        $i = 0;
                        $onItem = $inventory->GetItem($i);
                        while (isset($onItem))
                        {
                            if ($onItem->GetType() != 3 && $onItem->GetType() != 4 || $onItem->IsEquipped() || $onItem->GetUpgrade() > 3 || $item->GetStatsID() == 184 && !$onItem->CanChangeType() || $item->GetStatsID() == 71 && !$onItem->CanUpgrade() || $item->GetStatsID() == 181 && !$onItem->CanUpgrade()|| $item->GetStatsID() == 180 && !$onItem->CanUpgrade())
                            {
                                ++$i;
                                $onItem = $inventory->GetItem($i);
                                continue;
                            }
                            $counter++;
                            ?>
                            <input type="hidden" id="<?php echo $onItem->GetID(); ?>" value="<?php if($item->GetAmount() > $onItem->GetMaxUpgrade() - $onItem->GetUpgrade()) echo $onItem->GetMaxUpgrade() - $onItem->GetUpgrade(); else echo $item->GetAmount(); ?>">
                            <?php
                            ++$i;
                            $onItem = $inventory->GetItem($i);
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            if ($item->GetStatsID() == 174 || $item->GetStatsID() == 180) // Rüstungskristall
            {
                ?>
                <tr>
                    <td>Item:</td>
                    <td><select style="width:100px" class="select" name="item">
                            <?php
                            $i = 0;
                            $onItem = $inventory->GetItem($i);
                            $counter = 0;
                            while (isset($onItem))
                            {
                                if ($onItem->GetType() != 3 && $onItem->GetType() != 4 || $onItem->IsEquipped() || $onItem->GetUpgrade() != 4 || $item->GetStatsID() == 184 && !$onItem->CanChangeType() || $item->GetStatsID() == 71 || $item->GetStatsID() == 174 && !$onItem->CanUpgrade() || $item->GetStatsID() == 181 && !$onItem->CanUpgrade() || $item->GetStatsID() == 180 && !$onItem->CanUpgrade() || $onItem->IsStored())
                                {
                                    ++$i;
                                    $onItem = $inventory->GetItem($i);
                                    continue;
                                }
                                $counter++;
                                ?>
                                <option value="<?php echo $onItem->GetID(); ?>"><?php echo $onItem->GetName(); ?></option>
                                <?php
                                ++$i;
                                $onItem = $inventory->GetItem($i);
                            }
                            if($counter == 0)
                                echo '<option value="0">Kein Item möglich</option>';
                            ?>
                        </select></td>
                </tr>
                <?php
            }
            if ($item->GetStatsID() != 81 && $item->GetStatsID() != 82 && $item->GetStatsID() != 104 && $item->GetStatsID() != 119 && $item->GetStatsID() != 302)
            {
                if($item->GetSchatzitems()[1])
                {
                    $items = array();
                    for($i = 4; $i < count($item->GetSchatzitems()); $i++)
                    {
                        array_push($items, $itemManager->GetItem($item->GetSchatzitems()[$i]));
                    }
                    ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">Wähle ein Item aus:</td>
                        </tr>
                    <style>
                        .itemButton:hover {
                            background-image:linear-gradient(#4a4d56,#333438);
                        }
                    </style>
                    <tr>
                    <?php
                    for ($i = 0; $i < count($items); $i++)
                    {
                        ?>
                            <input type="radio" name="item" id="<?= $items[$i]->GetID();?>" value="<?= $items[$i]->GetID();?>" style="visibility: hidden;" required>
                            <td class="itemButton" style="padding: 10px; cursor: pointer;">
                                <label for="<?= $items[$i]->GetID();?>">
                                    <img src="img/items/<?= $items[$i]->GetImage(); ?>.png" class="item" width="75px" height="75px" style="cursor: pointer;" onclick="ChangeItem(this);">
                                </label>
                            </td>
                        <?php
                        if(($i + 1) % 3 == 0)
                        {
                            ?>
                            </tr>
                            <tr>
                            <?php
                        }
                    }
                    ?>
                    </tr>
                    <input type="hidden" name="amount" value="1">
                    <?php
                }
                else
                {
                    ?>
                    <tr>
                        <td>Anzahl:</td>
                        <td>
                            <select style="width:100px" class="select" name="amount">
                                <?php
                                    $j = 1;
                                    $amount = $item->GetAmount();
                                    while ($j <= $amount)
                                    {
                                        ?>
                                        <option value="<?php echo $j; ?>"><?php echo number_format($j,'0', '', '.'); ?></option>
                                        <?php
                                        ++$j;
                                    }
                                ?>
                            </select>
                        </td>
                        <?php
                            $min = 1;
                            if($item->GetType() == 1)
                            {
                                if($item->GetKP() > 0)
                                    $min = ($player->GetMaxKP() - $player->GetKP()) / $item->GetKP();
                                if($item->GetLP() > 0 && $min < ($player->GetMaxLP() - $player->GetLP()) / $item->GetLP())
                                    $min = ($player->GetMaxLP() - $player->GetLP()) / $item->GetLP();
                                if($min > $item->GetAmount())
                                    $min = $item->GetAmount();
                            }
                            else if($item->GetType() == 2)
                            {
                                if($item->GetKP() > 0)
                                    $min = (100 - $player->GetKPPercentage()) / $item->GetKP();
                                if($item->GetLP() > 0 && $min < (100 - $player->GetLPPercentage()) / $item->GetLP())
                                    $min = (100 - $player->GetLPPercentage()) / $item->GetLP();
                                if($min > $item->GetAmount())
                                    $min = $item->GetAmount();
                            }

                            if($item->GetType() == 7)
                                $min = $item->GetAmount();

                            if($min < 1)
                                $min = 1;
                        ?>
                        <td>
                            <?php
                                if($item->GetType() == 1 || $item->GetType() == 2 || $item->GetType() == 7)
                                {
                                    ?>
                                    <button type="button" onclick="amount.selectedIndex = <?php echo ceil($min - 1); ?>">MAX</button>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <button type="button" onclick="amount.selectedIndex = document.getElementById(item.value).value - 1">MAX</button>
                                    <?php
                                }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <div class="spacer"></div>
        <?php
        if ($item->GetType() == 7)
        {
            echo '<input style="width:100px" type="submit" value="Öffnen">';
        }
        else
        {
            if ($item->GetStatsID() == 119 || $item->GetStatsID() == 302 || $item->GetStatsID() == 350 || $item->GetStatsID() == 351 || $item->GetStatsID() == 352 || $item->GetStatsID() == 353 || $item->GetStatsID() == 104)
                echo '<input type="hidden" name="amount" value="1">';
            echo '<input style="width:100px" type="submit" value="Benutzen">';
        }
        ?>
    </form>
    <div class="spacer"></div>
    <?php
}
?>