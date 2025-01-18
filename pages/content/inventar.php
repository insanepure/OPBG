<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/items/itemmanager.php';
$heal = false;
$ruestung = false;
$kampf = false;
$boats = false;
$specialitems = false;
$treasures = false;
$skillitems = false;
?>
<?php
$i = 0;
$item = $inventory->GetItem($i);
while (isset($item)) {
    if ($item->GetType() != 1 && $item->GetType() != 2) {
        ++$i;
        $item = $inventory->GetItem($i);
        continue;
    }
    $heal = true;
    break;
}
if($heal)
{
    ?>
    <table style="width: 100%; border-spacing: 0; border: none; border-collapse: collapse;">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Heal</b><?php if($player->GetArank() >= 2)  { ?> <a href="?p=inventar&a=delete&type=1"><button style="float: right;">Löschen</button></a> <?php } ?></td>
        </tr>
        <tr>
        <tr>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Bild</b>
                </div>
            </td>
            <td style="width: 15%">
                <div style="text-align:center">
                    <b>Item</b>
                </div>
            </td>
            <td style="width: 25%">
                <div style="text-align:center">
                    <b>Wirkung</b>
                </div>
            </td>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Anzahl</b>
                </div>
            </td>
            <td style="width: 40%">
                <div style="text-align:center">
                    <b>Aktion</b>
                </div>
            </td>
        </tr>
        <?php
        $i = 0;
        $item = $inventory->GetItem($i);
        while (isset($item))
        {
            if ($item->GetType() != 1 && $item->GetType() != 2)
            {
                ++$i;
                $item = $inventory->GetItem($i);
                continue;
            }
            ?>
            <tr>
                <td class="borderT">
                    <div class="tooltip" style="position: relative; top:0; left:0; z-index: 100;">
                        <div style="width:80px; height:80px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="width:60px;height:60px; z-index:1; position: absolute;" alt="<?= $item->GetName() ?>" />
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0; position: relative;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            else
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            ?>
                        <span style="position:absolute; right:24px; bottom: 20px; font-size:24px; color:#000; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
                        <b>
                            <?php echo number_format($item->GetAmount(), '0', '', '.'); ?>
                        </b>
                        </span>
                        </div>
                        <?php
                            if($item->GetHoverDescription() != '')
                            {
                                ?>
                                <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -60px; bottom: 60px;">
                                    <?php
                                        echo htmlspecialchars_decode($item->GetHoverDescription());
                                    ?>
                                </span>
                                <?php
                            }
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center">
                        <?php echo $item->GetName(); ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php

                        echo $item->GetDescription();
                        if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php
                        echo number_format($item->GetAmount(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <div class="spacer"></div>
                        <button onclick="OpenPopupPage('Item Benutzen','items/use.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                            Benutzen
                        </button>
                        <div class="spacer"></div>
                        <?php
                        if ($item->IsSellable())
                        {
                            if ($player->GetInventory()->GetShip() != $item->GetID() && $item->GetStatsID() != 86 && $item->GetStatsID() != 87)
                            {
                                ?>
                                <button onclick="OpenPopupPage('Item Verkaufen','items/sell.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                    Verkaufen
                                </button>
                                <div class="spacer"></div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>

            <?php
            ++$i;
            $item = $inventory->GetItem($i);
        }

        ?>
    </table>
    <div class="spacer"></div>
    <?php
}

$i = 0;
$item = $inventory->GetItem($i);

while (isset($item)) {
    if (!in_array($item->GetStatsID(), $aufwertungsItems)) {
        ++$i;
        $item = $inventory->GetItem($i);
        continue;
    }
    $ruestung = true;
    break;
}
if($ruestung)
{
    ?>
    <table style="width: 100%; border-spacing: 0; border: none; border-collapse: collapse;">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Rüstungs Aufwertung</b><?php if($player->GetArank() >= 2)  { ?> <a href="?p=inventar&a=delete&type=upgrade"><button style="float: right;">Löschen</button></a> <?php } ?></td>
        </tr>
        <tr>
        <tr>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Bild</b>
                </div>
            </td>
            <td style="width: 15%">
                <div style="text-align:center">
                    <b>Item</b>
                </div>
            </td>
            <td style="width: 25%">
                <div style="text-align:center">
                    <b>Wirkung</b>
                </div>
            </td>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Anzahl</b>
                </div>
            </td>
            <td style="width: 40%">
                <div style="text-align:center">
                    <b>Aktion</b>
                </div>
            </td>
        </tr>
        <?php
        $i = 0;
        $item = $inventory->GetItem($i);
        while (isset($item))
        {
            if (!in_array($item->GetStatsID(), $aufwertungsItems))
            {
                ++$i;
                $item = $inventory->GetItem($i);
                continue;
            }
            ?>
            <tr>
                <td class="borderT">
                    <div class="tooltip" style="position: relative; top:0; left:0; z-index: 100;">
                        <div style="width:80px; height:80px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="width:60px;height:60px; z-index:1; position: absolute;" alt="<?= $item->GetName() ?>" />
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0; position: relative;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            else
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            ?>
                            <span style="position:absolute; right:24px; bottom: 20px; font-size:24px; color:#000; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
                        <b>
                            <?php echo number_format($item->GetAmount(), '0', '', '.'); ?>
                        </b>
                        </span>
                        </div>
                        <?php
                            if($item->GetHoverDescription() != '')
                            {
                                ?>
                                <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -60px; bottom: 60px;">
                                    <?php
                                        echo htmlspecialchars_decode($item->GetHoverDescription());
                                    ?>
                                </span>
                                <?php
                            }
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center">
                        <?php echo $item->GetName(); ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php

                        echo $item->GetDescription();
                        if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
                        if ($itemManager->GetItem($item->GetVisualID())->GetItemUses() != 0) echo '<br/>Abnutzung: ' . number_format($item->GetWear(), '0', '', '.') . '/' . number_format($itemManager->GetItem($item->GetVisualID())->GetItemUses(), '0', '', '.');
                        if ($item->GetRepairCount() != 0) echo '<br />Repariert: ' . number_format($item->GetRepairCount(), '0', '', '.') . '/3';
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php
                        echo number_format($item->GetAmount(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <div class="spacer"></div>
                        <button onclick="OpenPopupPage('Item Benutzen','items/use.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                            Benutzen
                        </button>
                        <div class="spacer"></div>
                        <?php
                        if ($item->IsSellable())
                        {
                            if ($item->GetRepairCount() == 0 && $item->GetStatsID() != 86 && $item->GetStatsID() != 87)
                            {
                                ?>
                                <button onclick="OpenPopupPage('Item Verkaufen','items/sell.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                    Verkaufen
                                </button>
                                <div class="spacer"></div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>

            <?php
            ++$i;
            $item = $inventory->GetItem($i);
        }

        ?>
    </table>
    <div class="spacer"></div>
    <?php
}

$i = 0;
$item = $inventory->GetItem($i);

while (isset($item)) {
    if (!in_array($item->GetStatsID(), $fightItems)) {
        ++$i;
        $item = $inventory->GetItem($i);
        continue;
    }
    $kampf = true;
    break;
}
if($kampf)
{
    ?>
    <table style="width: 100%; border-spacing: 0; border: none; border-collapse: collapse;">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Kampfitems</b><?php if($player->GetArank() >= 2)  { ?> <a href="?p=inventar&a=delete&type=fight"><button style="float: right;">Löschen</button></a> <?php } ?></td>
        </tr>
        <tr>
        <tr>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Bild</b>
                </div>
            </td>
            <td style="width: 15%">
                <div style="text-align:center">
                    <b>Item</b>
                </div>
            </td>
            <td style="width: 25%">
                <div style="text-align:center">
                    <b>Wirkung</b>
                </div>
            </td>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Anzahl</b>
                </div>
            </td>
            <td style="width: 40%">
                <div style="text-align:center">
                    <b>Aktion</b>
                </div>
            </td>
        </tr>
        <?php
        $i = 0;
        $item = $inventory->GetItem($i);
        while (isset($item))
        {
            if (!in_array($item->GetStatsID(), $fightItems))
            {
                ++$i;
                $item = $inventory->GetItem($i);
                continue;
            }
            ?>
            <tr>
                <td class="borderT">
                    <div class="tooltip" style="position: relative; top:0; left:0; z-index: 100;">
                        <div style="width:80px; height:80px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="width:60px;height:60px; z-index:1; position: absolute;" alt="<?= $item->GetName() ?>" />
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0; position: relative;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            else
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            ?>
                            <span style="position:absolute; right:24px; bottom: 20px; font-size:24px; color:#000; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
                        <b>
                            <?php echo number_format($item->GetAmount(), '0', '', '.'); ?>
                        </b>
                        </span>
                        </div>
                        <?php
                            if($item->GetHoverDescription() != '')
                            {
                                ?>
                                <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -60px; bottom: 60px;">
                                    <?php
                                        echo htmlspecialchars_decode($item->GetHoverDescription());
                                    ?>
                                </span>
                                <?php
                            }
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center">
                        <?php echo $item->GetName(); ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php

                        echo $item->GetDescription();
                        if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php
                        echo number_format($item->GetAmount(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <div class="spacer"></div>
                        <?php
                        if ($item->IsSellable())
                        {
                            if ($item->GetRepairCount() == 0 && $item->GetStatsID() != 86 && $item->GetStatsID() != 87)
                            {
                                ?>
                                <button onclick="OpenPopupPage('Item Verkaufen','items/sell.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                    Verkaufen
                                </button>
                                <div class="spacer"></div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>

            <?php
            ++$i;
            $item = $inventory->GetItem($i);
        }

        ?>
    </table>
    <div class="spacer"></div>
    <?php
}

$i = 0;
$item = $inventory->GetItem($i);

while (isset($item)) {
    if (!in_array($item->GetStatsID(), $schiffItems)) {
        ++$i;
        $item = $inventory->GetItem($i);
        continue;
    }
    $boats = true;
    break;
}
if($boats)
{
    ?>
    <table style="width: 100%; border-spacing: 0; border: none; border-collapse: collapse;">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Schiffe</b><?php if($player->GetArank() >= 2)  { ?> <a href="?p=inventar&a=delete&type=ship"><button style="float: right;">Löschen</button></a> <?php } ?></td>
        </tr>
        <tr>
        <tr>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Bild</b>
                </div>
            </td>
            <td style="width: 15%">
                <div style="text-align:center">
                    <b>Item</b>
                </div>
            </td>
            <td style="width: 25%">
                <div style="text-align:center">
                    <b>Wirkung</b>
                </div>
            </td>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Anzahl</b>
                </div>
            </td>
            <td style="width: 40%">
                <div style="text-align:center">
                    <b>Aktion</b>
                </div>
            </td>
        </tr>
        <?php
        $i = 0;
        $item = $inventory->GetItem($i);
        while (isset($item))
        {
            if (!in_array($item->GetStatsID(), $schiffItems))
            {
                ++$i;
                $item = $inventory->GetItem($i);
                continue;
            }
            ?>
            <tr>
                <td class="borderT">
                    <div class="tooltip" style="position: relative; top:0; left:0; z-index: 100;">
                        <div style="width:80px; height:80px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="width:60px;height:60px; z-index:1; position: absolute;" alt="<?= $item->GetName() ?>" />
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0; position: relative;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            else
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            ?>
                            <span style="position:absolute; right:24px; bottom: 20px; font-size:24px; color:#000; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
                        <b>
                            <?php echo number_format($item->GetAmount(), '0', '', '.'); ?>
                        </b>
                        </span>
                        </div>
                        <?php
                            if($item->GetHoverDescription() != '')
                            {
                                ?>
                                <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -60px; bottom: 60px;">
                                    <?php
                                        echo htmlspecialchars_decode($item->GetHoverDescription());
                                    ?>
                                </span>
                                <?php
                            }
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center">
                        <?php echo $item->GetName(); ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php
                        echo $item->GetDescription();
                        if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
                        if ($itemManager->GetItem($item->GetVisualID())->GetItemUses() != 0) echo '<br/>Abnutzung: ' . number_format($item->GetWear(), '0', '', '.') . '/' . number_format($itemManager->GetItem($item->GetVisualID())->GetItemUses(), '0', '', '.');
                        if ($item->GetRepairCount() != 0) echo '<br />Repariert: ' . number_format($item->GetRepairCount(), '0', '', '.') . '/3';
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php
                        echo number_format($item->GetAmount(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <div class="spacer"></div>
                        <?php
                        if ($item->IsSellable() && !$item->IsEquipped())
                        {
                            if ($item->GetRepairCount() != 0 && $item->GetStatsID() != 86 && $item->GetStatsID() != 87)
                            {
                                ?>
                                <button onclick="OpenPopupPage('Item Verkaufen','items/sell.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                    Verkaufen
                                </button>
                                <div class="spacer"></div>
                                <?php
                            }
                        }
                        if (in_array($item->GetStatsID(), $shipArray))
                        {
                            if ($player->GetInventory()->GetShip() == $item->GetID())
                            {
                                ?>
                                <div class="spacer"></div>
                                <button onclick="OpenPopupPage('Verlassen','items/ship.php?id=<?php echo $i; ?>&action=0')">
                                    Verlassen
                                </button>
                                <div class="spacer"></div>
                                <?php
                            }
                            else
                            {
                                ?>
                                <div class="spacer"></div>
                                <button onclick="OpenPopupPage('Besetzen','items/ship.php?id=<?php echo $i; ?>&action=1')">
                                    Besetzen
                                </button>
                                <div class="spacer"></div>
                                <?php
                            }
                            if ($item->GetWear() >= $itemManager->GetItem($item->GetVisualID())->GetItemUses() && $item->GetRepairCount() < 3)
                            {
                                ?>
                                <button onclick="OpenPopupPage('Item Reparieren','items/ship.php?id=<?php echo $i; ?>&action=2')">
                                    Reparieren
                                </button>
                                <div class="spacer"></div>
                                <?php
                            }
                            if($item->GetWear() == $itemManager->GetItem($item->GetStatsID())->GetItemUses() && $item->GetRepairCount() >= 3)
                            {
                                ?>
                                <button onclick="OpenPopupPage('Item Recycle','items/ship.php?id=<?php echo $i; ?>&action=3')">
                                    Recyclen
                                </button>
                                <div class="spacer"></div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>

            <?php
            ++$i;
            $item = $inventory->GetItem($i);
        }

        ?>
    </table>
    <div class="spacer"></div>
    <?php
}

$i = 0;
$item = $inventory->GetItem($i);

while (isset($item)) {
    if (!in_array($item->GetStatsID(), $sonstigeItems)) {
        ++$i;
        $item = $inventory->GetItem($i);
        continue;
    }
    $specialitems = true;
    break;
}
if($specialitems)
{
    ?>
    <table style="width: 100%; border-spacing: 0; border: none; border-collapse: collapse;">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Besondere Items</b><?php if($player->GetArank() >= 2)  { ?> <a href="?p=inventar&a=delete&type=sonstige"><button style="float: right;">Löschen</button></a> <?php } ?></td>
        </tr>
        <tr>
        <tr>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Bild</b>
                </div>
            </td>
            <td style="width: 15%">
                <div style="text-align:center">
                    <b>Item</b>
                </div>
            </td>
            <td style="width: 25%">
                <div style="text-align:center">
                    <b>Wirkung</b>
                </div>
            </td>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Anzahl</b>
                </div>
            </td>
            <td style="width: 40%">
                <div style="text-align:center">
                    <b>Aktion</b>
                </div>
            </td>
        </tr>
        <?php
        $i = 0;
        $item = $inventory->GetItem($i);
        while (isset($item))
        {
            if (!in_array($item->GetStatsID(), $sonstigeItems))
            {
                ++$i;
                $item = $inventory->GetItem($i);
                continue;
            }
            ?>
            <tr>
                <td class="borderT">
                    <div class="tooltip" style="position: relative; top:0; left:0; z-index: 100;">
                        <div style="width:80px; height:80px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="width:60px;height:60px; z-index:1; position: absolute;" alt="<?= $item->GetName() ?>" />
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0; position: relative;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            else
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            ?>
                            <span style="position:absolute; right:24px; bottom: 20px; font-size:24px; color:#000; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
                        <b>
                            <?php echo number_format($item->GetAmount(), '0', '', '.'); ?>
                        </b>
                        </span>
                        </div>
                        <?php
                            if($item->GetHoverDescription() != '')
                            {
                                ?>
                                <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -60px; bottom: 60px;">
                                    <?php
                                        echo htmlspecialchars_decode($item->GetHoverDescription());
                                    ?>
                                </span>
                                <?php
                            }
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center">
                        <?php echo $item->GetName(); ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php

                        echo $item->GetDescription();
                        if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php
                        echo number_format($item->GetAmount(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php
                        if ($item->GetStatsID() == 30)
                        {
                            ?>
                            <button onclick="OpenPopupPage('Skill Reset','profil/skillreset.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                Benutzen
                            </button>
                            <div class="spacer"></div>
                            <?php
                        }
                        else if($item->GetStatsID() != 510 && $item->GetStatsID() != 399 && $item->GetStatsID() != 73 && $item->GetStatsID() != 222 && $item->GetStatsID() != 315 && $item->GetStatsID() != 316 && $item->GetStatsID() != 317 && $item->GetStatsID() != 224 && $item->GetStatsID() != 226)
                        {
                            ?>
                            <div class="spacer"></div>
                            <button onclick="OpenPopupPage('Item Benutzen','items/use.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                Benutzen
                            </button>
                            <div class="spacer"></div>
                            <?php
                        }
                        else if($item->GetStatsID() == 510)
                        {
                            ?>
                            <div class="spacer"></div>
                            <button onclick="OpenPopupPage('Vivrecard','items/vivrecard.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                Benutzen
                            </button>
                            <div class="spacer"></div>
                                <?php
                        }
                        if ($item->IsSellable())
                        {
                            if ($item->GetStatsID() != 86 && $item->GetStatsID() != 87)
                            {
                                ?>
                                <button onclick="OpenPopupPage('Item Verkaufen','items/sell.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                    Verkaufen
                                </button>
                                <div class="spacer"></div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>

            <?php
            ++$i;
            $item = $inventory->GetItem($i);
        }

        ?>
    </table>
    <div class="spacer"></div>
    <?php
}

$i = 0;
$item = $inventory->GetItem($i);

while (isset($item)) {
    if ($item->GetType() != 7) {
        ++$i;
        $item = $inventory->GetItem($i);
        continue;
    }
    $treasures = true;
    break;
}
if($treasures)
{
    ?>
    <table style="width: 100%; border-spacing: 0; border: none; border-collapse: collapse;">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Schätze</b><?php if($player->GetArank() >= 2)  { ?> <a href="?p=inventar&a=delete&type=7"><button style="float: right;">Löschen</button></a> <?php } ?></td>
        </tr>
        <tr>
        <tr>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Bild</b>
                </div>
            </td>
            <td style="width: 15%">
                <div style="text-align:center">
                    <b>Item</b>
                </div>
            </td>
            <td style="width: 25%">
                <div style="text-align:center">
                    <b>Wirkung</b>
                </div>
            </td>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Anzahl</b>
                </div>
            </td>
            <td style="width: 40%">
                <div style="text-align:center">
                    <b>Aktion</b>
                </div>
            </td>
        </tr>
        <?php
        $i = 0;
        $item = $inventory->GetItem($i);
        while (isset($item))
        {
            if ($item->GetType() != 7)
            {
                ++$i;
                $item = $inventory->GetItem($i);
                continue;
            }
            ?>
            <tr>
                <td class="borderT">
                    <div class="tooltip" style="position: relative; top:0; left:0; z-index: 100;">
                        <div style="width:80px; height:80px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="width:60px;height:60px; z-index:1; position: absolute;" alt="<?= $item->GetName() ?>" />
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0; position: relative;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            else
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            ?>
                            <span style="position:absolute; right:24px; bottom: 20px; font-size:24px; color:#000; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
                        <b>
                            <?php echo number_format($item->GetAmount(), '0', '', '.'); ?>
                        </b>
                        </span>
                        </div>
                        <?php
                            if($item->GetHoverDescription() != '')
                            {
                                ?>
                                <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -60px; bottom: 60px;">
                                    <?php
                                        echo htmlspecialchars_decode($item->GetHoverDescription());
                                    ?>
                                </span>
                                <?php
                            }
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center">
                        <?php echo $item->GetName(); ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php

                        echo $item->GetDescription();
                        if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php
                        echo number_format($item->GetAmount(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <div class="spacer"></div>
                        <?php
                        if ($item->IsSellable())
                        {
                            ?>
                            <button onclick="OpenPopupPage('Item Verkaufen','items/sell.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                Verkaufen
                            </button>
                            <div class="spacer"></div>
                            <?php
                        }
                        ?>
                        <div class="spacer"></div>
                        <button onclick="OpenPopupPage('Öffnen','items/use.php?id=<?php echo $i; ?>')">
                            Öffnen
                        </button>
                        <div class="spacer"></div>
                    </div>
                </td>
            </tr>

            <?php
            ++$i;
            $item = $inventory->GetItem($i);
        }

        ?>
    </table>
    <div class="spacer"></div>
    <?php
}

$i = 0;
$item = $inventory->GetItem($i);
    while (isset($item)) {
        if ($item->GetCategory() != 5) {
            ++$i;
            $item = $inventory->GetItem($i);
            continue;
        }
        $skillitems = true;
        break;
    }
if($skillitems)
{
    ?>
    <table style="width: 100%; border-spacing: 0; border: none; border-collapse: collapse;">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Skill Items</b><?php if($player->GetArank() >= 2)  { ?> <a href="?p=inventar&a=delete&category=5"><button style="float: right;">Löschen</button></a> <?php } ?></td>
        </tr>
        <tr>
        <tr>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Bild</b>
                </div>
            </td>
            <td style="width: 15%">
                <div style="text-align:center">
                    <b>Item</b>
                </div>
            </td>
            <td style="width: 25%">
                <div style="text-align:center">
                    <b>Wirkung</b>
                </div>
            </td>
            <td style="width: 10%">
                <div style="text-align:center">
                    <b>Anzahl</b>
                </div>
            </td>
            <td style="width: 40%">
                <div style="text-align:center">
                    <b>Aktion</b>
                </div>
            </td>
        </tr>
        <?php
        $i = 0;
        $item = $inventory->GetItem($i);
        while (isset($item))
        {
            if ($item->GetCategory() != 5) {
                ++$i;
                $item = $inventory->GetItem($i);
                continue;
            }
            ?>
            <tr>
                <td class="borderT">
                    <div class="tooltip" style="position: relative; top:0; left:0; z-index: 100;">
                        <div style="width:80px; height:80px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="width:60px;height:60px; z-index:1; position: absolute;" alt="<?= $item->GetName() ?>" />
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0; position: relative;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            else
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:60px;height:60px; z-index:0;" alt="<?= $item->GetName() ?>" />
                                <?php
                            }
                            ?>
                            <span style="position:absolute; right:24px; bottom: 20px; font-size:24px; color:#000; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
                        <b>
                            <?php echo number_format($item->GetAmount(), '0', '', '.'); ?>
                        </b>
                        </span>
                        </div>
                        <?php
                            if($item->GetHoverDescription() != '')
                            {
                                ?>
                                <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -60px; bottom: 60px;">
                                    <?php
                                        echo htmlspecialchars_decode($item->GetHoverDescription());
                                    ?>
                                </span>
                                <?php
                            }
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center">
                        <?php echo $item->GetName(); ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php

                        echo $item->GetDescription();
                        if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <?php
                        echo number_format($item->GetAmount(), '0', '', '.');
                        ?>
                    </div>
                </td>
                <td class="borderT">
                    <div style="text-align:center;">
                        <div class="spacer"></div>
                        <?php
                        if ($item->IsSellable())
                        {
                            ?>
                            <button onclick="OpenPopupPage('Item Verkaufen','items/sell.php?id=<?php echo array_search($item, $inventory->GetItems()); ?>')">
                                Verkaufen
                            </button>
                            <div class="spacer"></div>
                            <?php
                        }
                        ?>
                    </div>
                </td>
            </tr>

            <?php
            ++$i;
            $item = $inventory->GetItem($i);
        }

        ?>
    </table>
    <div class="spacer"></div>
    <?php
}