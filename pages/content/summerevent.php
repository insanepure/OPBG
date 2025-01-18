<div style="width: 100%; height: 600px; background-image: url('img/gameevents/summerevent.png'); background-size: 100% 100%;"></div>
<a href="?p=summerevent&open=1">
    <div class="tooltip" style="cursor: pointer; position: absolute; z-index: 1; top: 100px; left: 180px;">
        <div style="width: 50px; height: 60px; background-image: url('img/gameevents/orange.png'); background-size: 100% 100%; transform: scale(<?php echo $scale1; ?>);"></div>
        <span class="tooltiptext" style="left: -50px;">
            <img style="width: 50px; height: 60px;" src="img/gameevents/orange.png" alt="Orange" title="Orange"/>
            <br/>
            <div class="kpback" style="margin-left: 5%; height:20px; width:90%;">
                <div class="kpbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                <div class="kpanzeige" style="width: <?php echo $scale1 * 100; ?>%"></div>
                <div class="kptext">
                    Orangen:
                    <?php
                    if($orangeAmount < 100)
                        echo $orangeAmount . " / 100";
                    else
                        echo "100 / 100";
                    ?>
                </div>
            </div>
            <br/>
            <?php
            if($belohnungen[$eDate][0][0] == 'gold')
            {
                $image = 'img/offtopic/GoldSymbol.png';
                $name = 'Gold';
            }
            else if($belohnungen[$eDate][0][0] == 'berry')
            {
                $image = 'img/offtopic/BerrySymbol.png';
                $name = 'Berry';
            }
            else if($belohnungen[$eDate][0][0] == 'stats')
            {
                $image = 'img/offtopic/BerrySymbol.png';
                $name = 'Statpunkte';
            }
            else
            {
                $item = $itemManager->GetItem($belohnungen[$eDate][0][0]);
                $image = 'img/items/' . $item->GetImage() . '.png';
                $name = $item->GetName();
            }
            ?>
                            <img style="<?php if($player->HasOrange1()) echo 'position: relative; left: 32.5px;'; ?>width: 60px; height: 60px; <?php if($scale1 != 1) echo 'filter: grayscale(1);'; ?>" src="<?php echo $image; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>"/>
                    <span style="position:absolute; right:40px; bottom: 7px; font-size:24px; color:#000;
          text-shadow:
            -1px -1px 0 #fff,
            1px -1px 0 #fff,
            -1px 1px 0 #fff,
            1px 1px 0 #fff;"><b><?php echo number_format($belohnungen[$eDate][0][1], '0', '', '.'); ?></b></span>
                <?php
                if($player->HasOrange1())
                {
                    ?>
                    <img style="position: relative;left: -32.5px; width: 60px; transform: scale(.8); height: 60px;" src="img/offtopic/haken.png" alt="<?php echo $name; ?>" title="<?php echo $name; ?>"/>
                    <?php
                }
                ?>
        </span>
    </div>
</a>
<a href="?p=summerevent&open=2">
    <div class="tooltip" style="cursor: pointer; position: absolute; z-index: 1; top: 40px; left: 340px;" <?php if($scale1 != 1) echo 'hidden'; ?>>
        <div style="width: 50px; height: 60px; background-image: url('img/gameevents/orange.png'); background-size: 100% 100%; transform: scale(<?php echo $scale2; ?>);"></div>
        <span class="tooltiptext" style="left: -50px;">
        <img style="width: 50px; height: 60px;" src="img/gameevents/orange.png" alt="Orange" title="Orange"/>
        <br/>
        <div class="kpback" style="margin-left: 5%; height:20px; width:90%;">
            <div class="kpbox smallBG borderT borderB borderR borderL boxSchatten"></div>
            <div class="kpanzeige" style="width: <?php echo $scale2 * 100; ?>%"></div>
            <div class="kptext">
                Orangen:
                <?php
                if($orangeAmount < 250)
                    echo $orangeAmount . " / 250";
                else
                    echo "250 / 250";
                ?>
            </div>
        </div>
        <br/>
        <?php
        if($belohnungen[$eDate][1][0] == 'gold')
        {
            $image = 'img/offtopic/GoldSymbol.png';
            $name = 'Gold';
        }
        else if($belohnungen[$eDate][1][0] == 'berry')
        {
            $image = 'img/offtopic/BerrySymbol.png';
            $name = 'Berry';
        }
        else if($belohnungen[$eDate][1][0] == 'stats')
        {
            $image = 'img/offtopic/BerrySymbol.png';
            $name = 'Statpunkte';
        }
        else
        {
            $item = $itemManager->GetItem($belohnungen[$eDate][1][0]);
            $image = 'img/items/' . $item->GetImage() . '.png';
            $name = $item->GetName();
        }
        ?>
                        <img style="<?php if($player->HasOrange2()) echo 'position: relative; left: 32.5px;'; ?>width: 60px; height: 60px; <?php if($scale2 != 1) echo 'filter: grayscale(1);'; ?>" src="<?php echo $image; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>"/>
                <span style="position:absolute; right:40px; bottom: 7px; font-size:24px; color:#000;
      text-shadow:
        -1px -1px 0 #fff,
        1px -1px 0 #fff,
        -1px 1px 0 #fff,
        1px 1px 0 #fff;"><b><?php echo number_format($belohnungen[$eDate][1][1], '0', '', '.'); ?></b></span>
            <?php
            if($player->HasOrange2())
            {
                ?>
                <img style="position: relative;left: -32.5px; width: 60px; transform: scale(.8); height: 60px;" src="img/offtopic/haken.png" alt="<?php echo $name; ?>" title="<?php echo $name; ?>"/>
                <?php
            }
            ?>
    </span>
    </div>
</a>
<a href="?p=summerevent&open=3">
    <div class="tooltip" style="cursor: pointer; position: absolute; z-index: 1; top: 160px; left: 440px;" <?php if($scale2 != 1) echo 'hidden'; ?>>
        <div style="width: 50px; height: 60px; background-image: url('img/gameevents/orange.png'); background-size: 100% 100%; transform: scale(<?php echo $scale3; ?>);"></div>
        <span class="tooltiptext" style="left: -50px;">
        <img style="width: 50px; height: 60px;" src="img/gameevents/orange.png" alt="Orange" title="Orange"/>
        <br/>
        <div class="kpback" style="margin-left: 5%; height:20px; width:90%;">
            <div class="kpbox smallBG borderT borderB borderR borderL boxSchatten"></div>
            <div class="kpanzeige" style="width: <?php echo $scale3 * 100; ?>%"></div>
            <div class="kptext">
                Orangen:
                <?php
                if($orangeAmount < 500)
                    echo $orangeAmount . " / 500";
                else
                    echo "500 / 500";
                ?>
            </div>
        </div>
        <br/>
        <?php
        if($belohnungen[$eDate][2][0] == 'gold')
        {
            $image = 'img/offtopic/GoldSymbol.png';
            $name = 'Gold';
        }
        else if($belohnungen[$eDate][2][0] == 'berry')
        {
            $image = 'img/offtopic/BerrySymbol.png';
            $name = 'Berry';
        }
        else if($belohnungen[$eDate][2][0] == 'stats')
        {
            $image = 'img/offtopic/BerrySymbol.png';
            $name = 'Statpunkte';
        }
        else
        {
            $item = $itemManager->GetItem($belohnungen[$eDate][2][0]);
            $image = 'img/items/' . $item->GetImage() . '.png';
            $name = $item->GetName();
        }
        ?>
                        <img style="<?php if($player->HasOrange3()) echo 'position: relative; left: 32.5px;'; ?>width: 60px; height: 60px; <?php if($scale3 != 1) echo 'filter: grayscale(1);'; ?>" src="<?php echo $image; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>"/>
                <span style="position:absolute; right:40px; bottom: 7px; font-size:24px; color:#000;
      text-shadow:
        -1px -1px 0 #fff,
        1px -1px 0 #fff,
        -1px 1px 0 #fff,
        1px 1px 0 #fff;"><b><?php echo number_format($belohnungen[$eDate][2][1], '0', '', '.'); ?></b></span>
            <?php
            if($player->HasOrange3())
            {
                ?>
                <img style="position: relative;left: -32.5px; width: 60px; transform: scale(.8); height: 60px;" src="img/offtopic/haken.png" alt="<?php echo $name; ?>" title="<?php echo $name; ?>"/>
                <?php
            }
            ?>
    </span>
    </div>
</a>

<div class="spacer"></div>
<div class="spacer"></div>
<div class="catGradient borderT" style="width:90%;">
    Bandeneventübersicht
</div>
<table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
    <tr>
        <td>
            <div style="text-align: center;">
                <b>
                    Mitglied
                </b>
            </div>
        </td>
        <td>
            <div style="text-align: center;">
                <b>
                    Gesammelte Orangen
                </b>
            </div>
        </td>
        <td>
            <div style="text-align: center;">
                <b>
                    Meer
                </b>
            </div>
        </td>
        <td>
            <div style="text-align: center;">
                <b>
                    Ort
                </b>
            </div>
        </td>
    </tr>
    <?php
    $id = 0;
    $list = new Generallist($database, 'accounts', '*', 'clan=' . $player->GetClan(), 'rank', 999, 'ASC');
    $clanEntry = $list->GetEntry($id);
    while ($clanEntry != null)
    {
        ?>
        <tr>
            <td>
                <div style="text-align: center;">
                    <?php echo "<a href='?p=profil&id=" . $clanEntry['id'] . "'>" . $clanEntry['name'] . "</a>"; ?>
                </div>
            </td>
            <td>
                <div style="text-align: center;">
                    <?php echo number_format($clanEntry['münzen'], '0', '', '.') . " / 50" ?>
                </div>
            </td>
            <td>
                <div style="text-align: center;">
                    <?= $clanEntry['planet'] ?>
                </div>
            </td>
            <td>
                <div style="text-align: center;">
                    <?= $clanEntry['place']; ?>
                </div>
            </td>
        </tr>
        <?php
        $id++;
        $clanEntry = $list->GetEntry($id);
    }
    ?>
</table>