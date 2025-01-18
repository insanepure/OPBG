<?php
include_once 'pages/itemzorder.php';
?>
<script type="text/javascript" src="js/combine.js?5"></script>
<div class="spacer"></div>
<div style="position:relative;">
    <div class="char">
        <div class="char2" style="z-index:<?php echo $zorders[0]; ?>; background-image: url('img/races/<?php echo $player->GetRaceImage(); ?>.png?003')"></div>
        <div class="char2" style="z-index:11; background-image: url('img/races/<?php echo $player->GetRaceImage(); ?>Head.png?003')"></div>
        <?php if ($clan != null && $clan->GetBanner() != '' && $player->ShowWappen())
        {
            ?>
            <div class="tooltip" style="z-index:<?php echo $zorders[12]; ?>; position:absolute; left:119px; top:155px;">
                <img src="<?php echo $clan->GetBanner(); ?>" style="z-index:<?php echo $zorders[11]; ?>; position:absolute; left:50px; top:50px;" width="30px" height="30px">
                <span class="tooltiptext"><?php echo $clan->GetName(); ?></span>
            </div>
            <?php
        }
        ?>
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Charakter</div>
        </div>
        <!-- Kleidung an Körper wie angezogen test -->
        <?php
        ShowSlotEquippedImage(6, $inventory, $zorders, $zordersOnTop); //Waffe
        ShowSlotEquippedImage(1, $inventory, $zorders, $zordersOnTop); //Aura
        ShowSlotEquippedImage(5, $inventory, $zorders, $zordersOnTop); //Brust
        ShowSlotEquippedImage(8, $inventory, $zorders, $zordersOnTop); //Accessoire
        ShowSlotEquippedImage(2, $inventory, $zorders, $zordersOnTop); //Hände
        ShowSlotEquippedImage(3, $inventory, $zorders, $zordersOnTop); //Hose
        ShowSlotEquippedImage(7, $inventory, $zorders, $zordersOnTop); //Schuhe
        ShowSlotEquippedImage(4, $inventory, $zorders, $zordersOnTop); //Haki

        ?>
    </div>
    <div class="kopfr borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Aura</div>
        </div>
        <?php ShowSlot($player, 1, $inventory, $itemManager); ?>
        <div class="spacer"></div>
    </div>

    <div class="handr borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Hände</div>
        </div>
        <?php ShowSlot($player,2, $inventory, $itemManager); ?>
        <div class="spacer"></div>
    </div>

    <div class="spr borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Hose</div>
        </div>
        <?php ShowSlot($player,3, $inventory,$itemManager); ?>
        <div class="spacer"></div>
    </div>

    <div class="reise borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Haki</div>
        </div>
        <?php ShowSlot($player,4, $inventory,$itemManager); ?>
        <div class="spacer"></div>
    </div>

    <div class="brustr borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Brust</div>
        </div>
        <?php ShowSlot($player,5, $inventory,$itemManager); ?>
        <div class="spacer"></div>
    </div>

    <div class="waffer borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Waffe</div>
        </div>
        <?php ShowSlot($player,6, $inventory,$itemManager); ?>
        <div class="spacer"></div>
    </div>

    <div class="panzr borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Accessoire</div>
        </div>
        <?php ShowSlot($player,8, $inventory,$itemManager); ?>
        <div class="spacer"></div>
    </div>

    <div class="schuhe borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Schuhe</div>
        </div>
        <?php ShowSlot($player,7, $inventory,$itemManager); ?>
        <div class="spacer"></div>
    </div>


    <div class="panzr2 borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Werte</div>
        </div>
        <span style="position:absolute; left:5px;">
      <b>Angriff: </b>
    </span>
        <span style="position:absolute; right:5px;">
      <?php
      $equippedStats = explode(';', $player->GetEquippedStats());
      $titelStats = explode(';', $player->GetTitelStats());
      echo number_format($player->GetAttack(), '0', '', '.');
      $count = 2;
      if ($equippedStats[$count] != 0 || $titelStats[$count] != 0)
      {
          ?>
          <span style="color: #00bb00;">+<?php echo number_format($equippedStats[$count] + $titelStats[$count], '0', '', '.'); ?></span>
          <?php
      }
      ?>
    </span><br>
        <span style="position:absolute; left:5px;">
      <b>Abwehr: </b>
    </span>
        <span style="position:absolute; right:5px;">
      <?php echo number_format($player->GetDefense(), '0', '', '.'); ?>
            <?php
            $count = 3;
            if ($equippedStats[$count] != 0 || $titelStats[$count] != 0)
            {
                ?>
                <span style="color: #00bb00;">+<?php echo number_format($equippedStats[$count] + $titelStats[$count], '0', '', '.'); ?></span>
                <?php
            }
            ?>
    </span><br>
        <span style="position:absolute; left:5px;">
      <b>LP: </b>
    </span>
        <span style="position:absolute; right:5px;">
      <?php
      echo number_format($player->GetLP(), '0', '', '.'); ?>/<?php echo number_format($player->GetMaxLP(), '0', '', '.');
            $count = 0;
            if ($equippedStats[$count] != 0 || $titelStats[$count] != 0)
            {
                ?>
                <span style="color: #00bb00;">+<?php echo number_format($equippedStats[$count] + $titelStats[$count], '0', '', '.'); ?></span>
                <?php
            }
            ?>
    </span><br>
        <span style="position:absolute; left:5px;">
      <b>AD: </b>
    </span>
        <span style="position:absolute; right:5px;">
      <?php
      echo number_format($player->GetKP(), '0', '', '.'); ?>/<?php echo number_format($player->GetMaxKP(), '0', '', '.');
            $count = 1;
            if ($equippedStats[$count] != 0 || $titelStats[$count] != 0)
            {
                ?>
                <span style="color: #00bb00;">+<?php echo number_format($equippedStats[$count] + $titelStats[$count], '0', '', '.'); ?></span>
                <?php
            }
            ?>
    </span><br>
        <span style="position:absolute; left:5px;">
      <b>Reflex: </b>
    </span>
        <span style="position:absolute; right:5px;">
      <?php
      echo number_format($player->GetReflex(), '0', '', '.');

      ?>
    </span><br>
        <div class="spacer"></div>
    </div>
</div>
<div class="spacer"></div>
<div class="spacer"></div>
<div class="SideMenuStyle borderB borderR borderT borderL catGradient" style="width:95%; box-shadow: 2px 1px 2px #888888; margin-top:665px;" onmousedown="location.replace('?p=kleiderschrank')">Kleiderschrank (<?= $player->GetInventory()->GetStorageCount() ?> Gegenstände)</div>
<form method="post" action="?p=bulksell">
    <div class="ausr borderB borderR borderT borderL">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Ausrüstung<?php if($player->GetArank() >= 2)  { ?> <a href="?p=ausruestung&a=delete"><button type="button" style="float: right;">Löschen</button></a> <?php } ?></div>
        </div>
        <div class="spacer"></div>
        <table width="100%" cellspacing="0">
            <tr style="text-align: center">
                <td width="15%">
                    <b>Bild</b>
                </td>
                <td width="20%">
                    <b>Item</b>
                </td>
                <td width="30%">
                    <b>Wirkung</b>
                </td>
                <td width="10%">
                    <b>Wert</b>
                </td>
                <td width="20%">
                    <b>Aktion</b>
                </td>
                <td width="5%">
                    <b>X</b>
                </td>
            </tr>

            <?php
            $i = 0;
            $item = $inventory->GetItem($i);
            while (isset($item))
            {
                if ($item->GetType() != 3 && $item->GetType() != 4 || $item->IsEquipped() || $item->IsStored())
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
                        <?php echo number_format($item->GetPrice(), '0', '', '.'); ?>
                        <?php
                        if(!$item->IsPremium())
                            echo '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 15px; width: 10px;"/>';
                        else
                            echo '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 15px; width: 15px;"/>';
                        ?>
                    </td>
                    <td class="borderT" style="text-align: center">
                        <?php
                        $slotItem = $inventory->GetItemAtSlot($item->GetSlot());
                        if ($slotItem != null)
                        {
                            ?>
                            <div class="spacer3"></div>
                            <Button type="button" onclick="OpenPopupPage('Ausrüsten','ausruestung/equip.php','id=<?php echo $i; ?>&slot=<?php echo $item->GetSlot(); ?>')">Anlegen</Button>
                            <div class="spacer3"></div>
                            <?php
                        }
                        else
                        {
                            ?>
                            <div class="spacer3"></div>
                            <div class="AusruestungButton" onmousedown="location.replace('?p=ausruestung&a=equip&item=<?= $i ?>')">
                                Anlegen
                            </div>
                            <div class="spacer3"></div>
                            <?php
                        }
                        if ($item->GetStatsID() != $item->GetVisualID() && !$item->IsProtected())
                        {
                            ?>
                            <div class="spacer3"></div>
                            <button type="button" onclick="OpenPopupPage('Item Visuell Zurücksetzen','ausruestung/removevisual.php?id=<?php echo $i; ?>')">
                                Visuell Entf.
                            </button>
                            <div class="spacer3"></div>
                            <?php
                        }
                        if (($item->IsSellable() || $item->IsPremium()) && !$item->IsProtected())
                        {
                            ?>
                            <div class="spacer3"></div>
                            <button type="button" onclick="OpenPopupPage('Item Verkaufen','ausruestung/sell.php?id=<?php echo $i; ?>')">
                                Verkaufen
                            </button>
                            <div class="spacer3"></div>
                            <Button type="button" onclick="OpenPopupPage('Item Recyceln','ausruestung/recycle.php','id=<?php echo $i; ?>')">Recyceln</Button>
                            <div class="spacer3"></div>
                            <?php

                        }
                        if ($item->GetRace() == '' && !$item->IsProtected())
                        {
                            ?>
                            <div class="spacer3"></div>
                            <Button type="button" onclick="OpenPopupPage('Item Kombinieren','ausruestung/combine.php','id=<?php echo $i; ?>')">Kombinieren</Button>
                            <div class="spacer3"></div>
                            <?php
                        }
                        if(!$item->IsProtected())
                        {
                            ?>
                            <div class="spacer3"></div>
                            <Button type="button" onclick="OpenPopupPage('Item Schützen', 'ausruestung/protection.php', 'id=<?= $i ?>&action=protect')">Schützen</Button>
                            <div class="spacer3"></div>
                            <?php
                        }
                        else
                        {
                            ?>
                            <div class="spacer3"></div>
                            <Button type="button" onclick="OpenPopupPage('Item Schutz Entfernen', 'ausruestung/protection.php', 'id=<?= $i ?>&action=unprotect')">Schutz Entfernen</Button>
                            <div class="spacer3"></div>
                            <?php
                        }
                        ?>
                    </td>
                    <td class="borderT" style="text-align: center">
                        <?php
                        if(!$item->IsProtected() && $item->IsSellable())
                        {
                            ?>
                            <div class="spacer3"></div>
                            <label>
                                <input type="checkbox" name="sellItem[]" value="<?= $i ?>">
                            </label>
                            <div class="spacer3"></div>
                            <?php
                        }
                        ?>
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
    <input type="checkbox" name="sellAll" id="markall" onClick="toggle(this)" style="cursor: pointer;" /><label for="markall" style="cursor: pointer;">Alle Markieren</label><br />
    <button type="submit" name="action" value="bulksell">
        Ausgewählte Gegenstände verkaufen
    </button><br/><br/>
    <button type="submit" name="action" value="bulkrecycle">
        Ausgewählte Gegenstände recyclen
    </button>
</form>
<div class="spacer"></div>

<style>
    .AusruestungButton {
        color: white;
        padding: 5px 15px;
        background: #2a2c30;
        display: inline-block;
        border: 0 none;
        cursor: pointer;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        -webkit-appearance: button;
        overflow: visible;
        font-family: sans-serif;
        font-size: 100%;
        line-height: 1.15;
        margin: 0;
    }
</style>

<script>
    function toggle(source) {
        let checkboxes = document.getElementsByName('sellItem[]');
        for (let i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>