<img width='100%' height='300' src='img/marketing/Eventshop.png' />
<table width="100%" cellspacing="0" border="0" style="margin-top: -5px;">
    <tr>
        <td colspan=3 class="catGradient borderT borderB" align="center">
            <b>
        <span style="color: white;">
          <div class="schatten">Suchen</div>
        </span>
            </b>
        </td>
    </tr>
    <tr>
        <td width="40%"><b> Name </b></td>
        <td width="40%"><b> Kategorie </b></td>
        <td width="40%"><b> Aktion </b></td>
    </tr>
    <tr>
        <form method="GET" action="?p=eventshop">
            <input type="hidden" name="p" value="eventshop">
            <td width="40%">
                <input style="width:90%;" type="text" name="itemname" value="<?php if (isset($_GET['itemname'])) echo htmlentities($_GET['itemname']); ?>">
            </td>
            <td width="40%">
                <select style="width:90%;" class="select" name="itemcategory">
                    <option value="0" <?php if (isset($_GET['itemcategory']) && $_GET['itemcategory'] == 0) echo 'selected'; ?>>Alle</option>
                    <option value="1" <?php if (isset($_GET['itemcategory']) && $_GET['itemcategory'] == 1) echo 'selected'; ?>>Medizin</option>
                    <option value="2" <?php if (isset($_GET['itemcategory']) && $_GET['itemcategory'] == 2) echo 'selected'; ?>>Rüstungen</option>
                    <option value="3" <?php if (isset($_GET['itemcategory']) && $_GET['itemcategory'] == 3) echo 'selected'; ?>>Waffen</option>
                    <option value="5" <?php if (isset($_GET['itemcategory']) && $_GET['itemcategory'] == 5) echo 'selected'; ?>>Skillitems</option>
                    <option value="4" <?php if (isset($_GET['itemcategory']) && $_GET['itemcategory'] == 4) echo 'selected'; ?>>Sonstiges</option>
                </select>
            </td>
            <td width="20%">
                <input type="submit" style="width:90%" value="Suchen">
            </td>
        </form>
    </tr>
</table>

<table width="100%" cellspacing="0" border="0">
    <tr>
        <td colspan=6 height="20px">
        </td>
    </tr>
    <tr>
        <td colspan=6 class="catGradient borderT borderB" align="center">
            <b>
        <span style="color: white;">
          <div class="schatten">Halloween Shop</div>
        </span>
            </b>
        </td>
    </tr>
    <tr class="boxSchatten">
        <td width="10%" align="center"><b> Bild </b></td>
        <td width="20%" align="center"><b> Name </b></td>
        <td width="35%" align="center"><b> Wirkung </b></td>
        <td width="15%" align="center"><b> Preis </b></td>
        <td width="10%" align="center"><b> Aktion </b></td>
    </tr>
    <?php
        $items = $place->GetEventItems();
        usort($items, function ($a, $b) use ($itemManager)
        {
            $item1 = $itemManager->GetItem($a);
            $item2 = $itemManager->GetItem($b);
            return $item1->GetType() <=> $item2->GetType();
        });
        $i = 0;
        while (isset($items[$i]))
        {
            if ($items[$i] == '')
            {
                ++$i;
                continue;
            }
            $item = $itemManager->GetItem($items[$i]);
            if ($item->IsPremium())
            {
                ++$i;
                continue;
            }
            if ($item == null)
            {
                echo 'Item: ' . $items[$i] . ' not valid.<br/>';
                ++$i;
                continue;
            }
            if (isset($_GET['itemname']) && $_GET['itemname'] != '' && strpos($item->GetName(), $_GET['itemname']) === false)
            {
                ++$i;
                continue;
            }
            if (isset($_GET['itemcategory']) && $_GET['itemcategory'] != 0 && $_GET['itemcategory'] != $item->GetCategory())
            {
                ++$i;
                continue;
            }

            if ($item->GetNeedItem() != 0 && !$player->HasItemWithID($item->GetNeedItem(), $item->GetNeedItem()))
            {
                ++$i;
                continue;
            }
            ?>
            <tr>
                <td class="borderT" align="center">
                    <div class="tooltip" style="position: relative; z-index: 100;">
                        <div style="width:50px; height:50px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png?002" style="width:50px;height:50px; position:absolute; top:2px; z-index:1;">
                                <?php
                            }
                            ?>
                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png?002" style="width:50px;height:50px; position:relative; top:2px; z-index:0;">
                        </div>
                        <?php
                            if($item->GetHoverDescription() != '')
                            {
                                ?>
                                <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -75px; bottom: 40px;">
                                    <?php
                                        echo htmlspecialchars_decode($item->GetHoverDescription());
                                    ?>
                                </span>
                                <?php
                            }
                        ?>
                    </div>
                    <input type="hidden" name="item" value="<?php echo $item->GetID(); ?>">
                </td>
                <td class="borderT" align="center"> <b><?php echo $item->GetName(); ?></b> </td>
                <td class="borderT" align="center">
                    <?php

                        echo $item->GetDescription();
                        if ($item->GetLevel() != 0) echo '<br/>Benötigt Level ' . number_format($item->GetLevel(),'0', '', '.');
                    ?>
                </td>

                <td class="borderT" align="center"><?php echo number_format($item->GetPrice(), 0, ',', '.'); ?> Kürbismünzen</td>
                <td class="borderT">
                    <button onclick="OpenPopupPage('Item Kaufen','eventshop/buy.php', 'item=<?php echo $item->GetID();
                        if (isset($_GET['itemname'])) echo '&itemname=' . $_GET['itemname'];
                        if (isset($_GET['itemcategory'])) echo '&itemcategory=' . $_GET['itemcategory']; ?>')">
                        Kaufen
                    </button>
                </td>
            </tr>
            <?php
            ++$i;
        }
    ?>
</table>
<div class="spacer"></div>