<div class="ausr borderB borderR borderT borderL">
    <div class="SideMenuKat catGradient borderB">
        <div class="schatten">Kleiderschrank</div>
    </div>
    <div class="spacer"></div>
    <table width="100%" cellspacing="0">
        <tr style="text-align: center">
            <td width="15%">
                <b>Bild</b>
            </td>
            <td width="25%">
                <b>Item</b>
            </td>
            <td width="35%">
                <b>Wirkung</b>
            </td>
            <td width="25%">
                <b>Aktion</b>
            </td>
        </tr>

        <?php
        $i = 0;
        $inventory = new Inventory($database, $player->GetID());
        $item = $inventory->GetItem($i);
        while (isset($item))
        {
            if ($item->GetType() != 3 && $item->GetType() != 4 || !$item->IsStored() || $item->IsEquipped())
            {
                ++$i;
                $item = $inventory->GetItem($i);
                continue;
            }
            ?>
            <tr height="120px">
                <td class="borderT" style="text-align: center">
                    <div class="tooltip" style="position: relative; top:0; left:0; z-index: 100;">
                        <div style="width:80px; height:80px; position:relative; top:-5px; left:-40px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png?002" style="width:80px;height:80px; position:absolute; z-index:1;">
                                <?php
                            }
                            ?>
                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png?002" style="width:80px;height:80px; position:absolute; z-index:0;">
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
                <td class="borderT" style="text-align: center">
                    <?php echo $item->GetName(); ?>
                </td>
                <td class="borderT" style="text-align: center">
                    <?php
                    echo $item->DisplayEffect();
                    if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
                    ?>
                </td>
                <td class="borderT" style="text-align: center">
                    <div class="spacer3"></div>
                    <Button onclick="OpenPopupPage('Ausrüsten','ausruestung/kleiderschrank.php','id=<?php echo $i; ?>&action=1')">Auslagern</Button>
                    <div class="spacer3"></div>
                </td>
            </tr>

            <?php
            ++$i;
            $item = $inventory->GetItem($i);
        }

        ?>
    </table>
</div>
<div class="spacer"></div>
<div class="spacer"></div>
<div class="ausr borderB borderR borderT borderL">
    <div class="SideMenuKat catGradient borderB">
        <div class="schatten">Ausrüstung</div>
    </div>
    <div class="spacer"></div>
    <table width="100%" cellspacing="0">
        <tr style="text-align: center">
            <td width="15%">
                <b>Bild</b>
            </td>
            <td width="25%">
                <b>Item</b>
            </td>
            <td width="35%">
                <b>Wirkung</b>
            </td>
            <td width="25%">
                <b>Aktion</b>
            </td>
        </tr>

        <?php
        $i = 0;
        $inventory = new Inventory($database, $player->GetID());
        $item = $inventory->GetItem($i);
        while (isset($item))
        {
            if ($item->GetType() != 3 && $item->GetType() != 4 || $item->IsStored() || $item->IsEquipped())
            {
                ++$i;
                $item = $inventory->GetItem($i);
                continue;
            }
            ?>
            <tr height="120px">
                <td class="borderT" style="text-align: center">
                    <div class="tooltip" style="position: relative; top:0; left:0; z-index: 100;">
                        <div style="width:80px; height:80px; position:relative; top:-5px; left:-40px;">
                            <?php if ($item->HasOverlay())
                            {
                                ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png?002" style="width:80px;height:80px; position:absolute; z-index:1;">
                                <?php
                            }
                            ?>
                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png?002" style="width:80px;height:80px; position:absolute; z-index:0;">
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
                <td class="borderT" style="text-align: center">
                    <?php echo $item->GetName(); ?>
                </td>
                <td class="borderT" style="text-align: center">
                    <?php
                    echo $item->DisplayEffect();
                    if ($item->GetLevel() != 0)
                        echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
                    ?>
                </td>
                <td class="borderT" style="text-align: center">
                    <div class="spacer3"></div>
                    <Button onclick="OpenPopupPage('Ausrüsten','ausruestung/kleiderschrank.php','id=<?php echo $i; ?>&action=0')">Einlagern</Button>
                    <div class="spacer3"></div>
                </td>
            </tr>

            <?php
            ++$i;
            $item = $inventory->GetItem($i);
        }
        ?>
    </table>
</div>