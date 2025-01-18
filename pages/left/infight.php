<?php
if(!isset($fight) || $fight->IsEnded())
{
    include_once 'main.php';
}
else
{
    ?>
    <style scoped>
        summary {
            cursor: pointer;
        }

        details summary, summary::marker > * {
            display: inline;
        }

        #tooltip {
            left: 15px;
            height: fit-content;
        }

        @media (max-width: 1200px) {
            #tooltip {
                left: 50px;
                height: fit-content;
            }
        }
    </style>
    <!-- Team Anfang -->
    <center>
        <?php
        if($player->GetFight() == $fight->GetID() && $pFighter->GetTeam() == 0 && $fight->GetType() != 13)
        {
            if(($player->HasItemWithID(81, 81) || $player->HasItemWithID(82, 82) || $player->HasItemWithID(86, 86) || $player->HasItemWithID(87, 87) || $player->HasItemWithID(88, 88) || $player->HasItemWithID(406, 406)))
            {
                ?>

                <div class="spacer"></div>
                <div class="SideMenuContainer borderT borderL borderB borderR">
                    <details id="details" ontoggle="toggleKampfitems()">
                        <summary class="SideMenuKat catGradient schatten">Kampfitems</summary>
                        <div class="SideMenuInfo">
                            <div class="spacer"></div>
                            <form method="post" action="?p=infight&a=use">
                                <div class="SideMenuContainer2 borderT borderL borderB borderR">
                                    <?php
                                    if($player->HasItemWithID(81, 81))
                                    {
                                        $itemData = $player->GetItemByStatsIDOnly(81);
                                        ?>
                                        <div class="borderT borderL borderB borderR" style="height: 45px; position: relative;">
                                            <div class="tooltip" style="width:50px; height:40px; float: left;">
                                                <div style="width:50px; height:40px; float: left;">
                                                    <?php if ($itemData->HasOverlay())
                                                    {
                                                        ?>
                                                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" style="width:40px;height:40px; z-index:1;">
                                                        <?php
                                                    }
                                                    ?>
                                                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" style="width:40px;height:40px; z-index:0;">
                                                    <span style="position: relative; right: -7px; bottom: 30px; font-size: 18px; color: #000;
                                         text-shadow: -1px -1px 0 #fff,
                                         1px -1px 0 #fff,
                                         -1px 1px 0 #fff,
                                         1px 1px 0 #fff;"><b><?php echo number_format($itemData->GetAmount(), '0', '', '.'); ?></b>
                                        </span>
                                                </div>
                                                <span class="tooltiptext" id="tooltip">
                                                <?php echo $itemData->GetName(); ?>
                                                <hr/>
                                                <?php echo $itemData->GetDescription(); ?>
                                            </span>
                                            </div>
                                            <div>
                                                <input type="text" placeholder="Anzahl" name="booster" style="height: 30px; width: 70px; text-align: center; font-size: 18px; float: right;">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if($player->HasItemWithID(82, 82))
                                    {
                                        $itemData = $player->GetItemByStatsIDOnly(82);
                                        ?>
                                        <div class="borderT borderL borderB borderR" style="height: 45px; position: relative;">
                                            <div class="tooltip" style="width:50px; height:40px; float: left;">
                                                <div style="width:50px; height:40px; float: left;">
                                                    <?php if ($itemData->HasOverlay())
                                                    {
                                                        ?>
                                                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" style="width:40px;height:40px; z-index:1;">
                                                        <?php
                                                    }
                                                    ?>
                                                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" style="width:40px;height:40px; z-index:0;">
                                                    <span style="position: relative; right: -7px; bottom: 30px; font-size: 18px; color: #000;
                                         text-shadow: -1px -1px 0 #fff,
                                         1px -1px 0 #fff,
                                         -1px 1px 0 #fff,
                                         1px 1px 0 #fff;"><b><?php echo number_format($itemData->GetAmount(), '0', '', '.'); ?></b>
                                        </span>
                                                </div>
                                                <span class="tooltiptext" id="tooltip">
                                                <?php echo $itemData->GetName(); ?>
                                                <hr/>
                                                <?php echo $itemData->GetDescription(); ?>
                                            </span>
                                            </div>
                                            <div>
                                                <input type="text" placeholder="Anzahl" name="vitamins" style="height: 30px; width: 70px; text-align: center; font-size: 18px; float: right;">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if($player->HasItemWithID(86, 86))
                                    {
                                        $itemData = $player->GetItemByStatsIDOnly(86);
                                        ?>
                                        <div class="borderT borderL borderB borderR" style="height: 45px; position: relative;">
                                            <div class="tooltip" style="width:50px; height:40px; float: left;">
                                                <div style="width:50px; height:40px; float: left;">
                                                    <?php if ($itemData->HasOverlay())
                                                    {
                                                        ?>
                                                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" style="width:40px;height:40px; z-index:1;">
                                                        <?php
                                                    }
                                                    ?>
                                                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" style="width:40px;height:40px; z-index:0;">
                                                    <span style="position: relative; right: -7px; bottom: 30px; font-size: 18px; color: #000;
                                         text-shadow: -1px -1px 0 #fff,
                                         1px -1px 0 #fff,
                                         -1px 1px 0 #fff,
                                         1px 1px 0 #fff;"><b><?php echo number_format($itemData->GetAmount(), '0', '', '.'); ?></b>
                                        </span>
                                                </div>
                                                <span class="tooltiptext" id="tooltip">
                                                <?php echo $itemData->GetName(); ?>
                                                <hr/>
                                                <?php echo $itemData->GetDescription(); ?>
                                            </span>
                                            </div>
                                            <div>
                                                <input type="text" placeholder="Anzahl" name="redFruit" style="height: 30px; width: 70px; text-align: center; font-size: 18px; float: right;">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if($player->HasItemWithID(87, 87))
                                    {
                                        $itemData = $player->GetItemByStatsIDOnly(87);
                                        ?>
                                        <div class="borderT borderL borderB borderR" style="height: 45px; position: relative;">
                                            <div class="tooltip" style="width:50px; height:40px; float: left;">
                                                <div style="width:50px; height:40px; float: left;">
                                                    <?php if ($itemData->HasOverlay())
                                                    {
                                                        ?>
                                                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" style="width:40px;height:40px; z-index:1;">
                                                        <?php
                                                    }
                                                    ?>
                                                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" style="width:40px;height:40px; z-index:0;">
                                                    <span style="position: relative; right: -7px; bottom: 30px; font-size: 18px; color: #000;
                                         text-shadow: -1px -1px 0 #fff,
                                         1px -1px 0 #fff,
                                         -1px 1px 0 #fff,
                                         1px 1px 0 #fff;"><b><?php echo number_format($itemData->GetAmount(), '0', '', '.'); ?></b>
                                        </span>
                                                </div>
                                                <span class="tooltiptext" id="tooltip">
                                                <?php echo $itemData->GetName(); ?>
                                                <hr/>
                                                <?php echo $itemData->GetDescription(); ?>
                                            </span>
                                            </div>
                                            <div>
                                                <input type="text" placeholder="Anzahl" name="orangeFruit" style="height: 30px; width: 70px; text-align: center; font-size: 18px; float: right;">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if($player->HasItemWithID(88, 88))
                                    {
                                        if($player->GetLevel() < 47 || true)
                                        {
                                            $itemData = $player->GetItemByStatsIDOnly(88);
                                            ?>
                                            <div class="borderT borderL borderB borderR" style="height: 45px; position: relative;">
                                                <div class="tooltip" style="width:50px; height:40px; float: left;">
                                                    <div style="width:50px; height:40px; float: left;">
                                                        <?php if ($itemData->HasOverlay())
                                                        {
                                                            ?>
                                                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" style="width:40px;height:40px; z-index:1;">
                                                            <?php
                                                        }
                                                        ?>
                                                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" style="width:40px;height:40px; z-index:0;">
                                                        <span style="position: relative; right: -7px; bottom: 30px; font-size: 18px; color: #000;
                                             text-shadow: -1px -1px 0 #fff,
                                             1px -1px 0 #fff,
                                             -1px 1px 0 #fff,
                                             1px 1px 0 #fff;"><b><?php echo number_format($itemData->GetAmount(), '0', '', '.'); ?></b>
                                            </span>
                                                    </div>
                                                    <span class="tooltiptext" id="tooltip">
                                                    <?php echo $itemData->GetName(); ?>
                                                    <hr/>
                                                    <?php echo $itemData->GetDescription(); ?>
                                                </span>
                                                </div>
                                                <div>
                                                    <input type="text" placeholder="Anzahl" name="yellowFruit" style="height: 30px; width: 70px; text-align: center; font-size: 18px; float: right;">
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    if($player->HasItemWithID(406, 406))
                                    {
                                        $itemData = $player->GetItemByStatsIDOnly(406);
                                        ?>
                                        <div class="borderT borderL borderB borderR" style="height: 45px; position: relative;">
                                            <div class="tooltip" style="width:50px; height:40px; float: left;">
                                                <div style="width:50px; height:40px; float: left;">
                                                    <?php if ($itemData->HasOverlay())
                                                    {
                                                        ?>
                                                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" style="width:40px;height:40px; z-index:1;">
                                                        <?php
                                                    }
                                                    ?>
                                                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" style="width:40px;height:40px; z-index:0;">
                                                    <span style="position: relative; right: -7px; bottom: 30px; font-size: 18px; color: #000;
                                         text-shadow: -1px -1px 0 #fff,
                                         1px -1px 0 #fff,
                                         -1px 1px 0 #fff,
                                         1px 1px 0 #fff;"><b><?php echo number_format($itemData->GetAmount(), '0', '', '.'); ?></b>
                                        </span>
                                                </div>
                                                <span class="tooltiptext" id="tooltip">
                                                <?php echo $itemData->GetName(); ?>
                                                <hr/>
                                                <?php echo $itemData->GetDescription(); ?>
                                            </span>
                                            </div>
                                            <div>
                                                <input type="text" placeholder="Anzahl" name="gruenewolke" style="height: 30px; width: 70px; text-align: center; font-size: 18px; float: right;">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if($player->HasItemWithID(407, 407))
                                    {
                                        $itemData = $player->GetItemByStatsIDOnly(407);
                                        ?>
                                        <div class="borderT borderL borderB borderR" style="height: 45px; position: relative;">
                                            <div class="tooltip" style="width:50px; height:40px; float: left;">
                                                <div style="width:50px; height:40px; float: left;">
                                                    <?php if ($itemData->HasOverlay())
                                                    {
                                                        ?>
                                                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" style="width:40px;height:40px; z-index:1;">
                                                        <?php
                                                    }
                                                    ?>
                                                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" style="width:40px;height:40px; z-index:0;">
                                                    <span style="position: relative; right: -7px; bottom: 30px; font-size: 18px; color: #000;
                                         text-shadow: -1px -1px 0 #fff,
                                         1px -1px 0 #fff,
                                         -1px 1px 0 #fff,
                                         1px 1px 0 #fff;"><b><?php echo number_format($itemData->GetAmount(), '0', '', '.'); ?></b>
                                        </span>
                                                </div>
                                                <span class="tooltiptext" id="tooltip">
                                                <?php echo $itemData->GetName(); ?>
                                                <hr/>
                                                <?php echo $itemData->GetDescription(); ?>
                                            </span>
                                            </div>
                                            <div>
                                                <input type="text" placeholder="Anzahl" name="rotewolke" style="height: 30px; width: 70px; text-align: center; font-size: 18px; float: right;">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="spacer"></div>
                                <input class="submit" type="submit" value="Benutzen">
                            </form>
                            <div class="spacer"></div>
                        </div>
                    </details>
                </div>
                <?php
            }
        }
        ?>
        <div class="spacer"></div>
        <?php
        $id = 0;
        while(isset($teams[$id]))
        {
            ShowTeam($teams[$id], $id, $pFighter, $fight);
            $id += 2;
        }
        ?>
    </center>
    <!-- Team Ende -->
    <?php
}
?>
<script>
    if(localStorage.getItem('open') === 'true')
    {
        document.getElementById("details").open = true;
        console.log('storage: open');
    }
    else
    {
        document.getElementById("details").open = false;
        console.log('storage: closed');
    }

    function toggleKampfitems()
    {
        if(document.getElementById("details").hasAttribute('open'))
        {
            localStorage.setItem('open', 'true');
            console.log('opened');
        }
        else
        {
            localStorage.setItem('open', 'false');
            console.log('closed');
        }
    }
</script>

