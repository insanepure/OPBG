<script>
    SelectionChange(0);
    grecaptcha.ready(function() {
        grecaptcha.execute('6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb', {action: 'Markt'});
    });
</script>
<style>
    .maincontainer, .content {
        width: 1800px;
    }

    .chatfenster{
        position: absolute;
        width: 86%;
        height: 300px;
    }

    .chatuser{
        position: absolute;
        width: 250.3px;
        left: 86%;
        height: 300px;
        text-align: center;
    }

    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #888;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .MarketAnzeigenContainer {
        border: 1px solid transparent;
        vertical-align:middle;
        width: 100%;
        display: flex;
        align-items:center;
        margin-top:5px;
    }

    .MarketAnzeigenContainer:hover {
        border: 1px solid #4c4e51;
    }

    .select2-results {
        color: #000000;
    }
</style>
<?php
$wartung = false;
if($wartung == false || $player->GetArank() >= 0) {
    $result = $database->Select('*', 'accounts', 'userid='.$player->GetUserID().' AND id != '.$player->GetID().' AND clan != 0');
    if($result && $result->num_rows > 0) {
        $clans = array();
        while ($row = $result->fetch_assoc()) {
            $clans[] = $row['clan'];
        }
    }
    ?>
    <!-- SUCHEN -->
    <table width="98%">
        <tr>
            <td width="70%">
                <table cellspacing="0">
                    <tr>
                        <td colspan=6 class="catGradient borderT borderB" align="center">
                            <b> <span style="color: white"><div class="schatten">Suchen</div></span> </b>
                        </td>
                    </tr>
                    <tr style="boxSchatten">
                        <td width="20%"><b> Name </b></td>
                        <td width="10%"><b> Kategorie </b></td>
                        <td width="10%" align="center"><b> Level </b></td>
                        <td width="10%" align="center"><b> Benötigtes Level </b></td>
                        <td width="12%" rowspan=2 align="left">
                            <div class="spacer"></div>
                            <label style="cursor:pointer;">Zeige Items für Gold:
                                <input id="goldItems" type="checkbox" style="width:18px; height:18px; cursor:pointer; float: right;" onchange="Search()" checked>
                            </label>
                            <div class="spacer"></div>
                            <label style="cursor:pointer;">Nur eigene Items anzeigen:
                                <input id="eigeneItems" type="checkbox" style="width:18px; height:18px; cursor:pointer; float:right;" onchange="Search()">
                            </label>
                            <div class="spacer"></div>
                            <label style="cursor:pointer;">Nur Gebote anzeigen:
                                <input id="gebote" type="checkbox" style="width:18px; height:18px; cursor:pointer; float:right;" onchange="käufe.checked = false; Search();">
                            </label>
                            <div class="spacer"></div>
                            <label style="cursor:pointer;">Nur Sofortkäufe anzeigen:
                                <input id="käufe" type="checkbox" style="width:18px; height:18px; cursor:pointer; float:right;" onchange="gebote.checked = false; Search();">
                            </label>
                            <input type="hidden" id="playername" value="<?php echo $player->GetName(); ?>">
                            <input type="hidden" id="playerid" value="<?php echo $player->GetID(); ?>">
                        </td>
                    </tr>
                    <tr>
                        <input type="hidden" name="p" value="market">
                        <td width="20%" style="boxSchatten">
                            <input style="width:90%;" type="text" id="itemname" onKeyUp="Search()" value="<?php if(isset($_GET['itemname'])) echo htmlentities($_GET['itemname']); ?>" placeholder="Item">
                        </td>
                        <td width="10%" style ="boxSchatten">
                            <select style="width:90%;" class="select" id="itemcategory" onchange="Search()">
                                <option value="0" <?php if(isset($_GET['itemcategory']) && $_GET['itemcategory'] == 0) echo 'selected'; ?>>Alle</option>
                                <option value="1" <?php if(isset($_GET['itemcategory']) && $_GET['itemcategory'] == 1) echo 'selected'; ?>>Medizin</option>
                                <option value="2" <?php if(isset($_GET['itemcategory']) && $_GET['itemcategory'] == 2) echo 'selected'; ?>>Rüstungen</option>
                                <option value="3" <?php if(isset($_GET['itemcategory']) && $_GET['itemcategory'] == 3) echo 'selected'; ?>>Waffen</option>
                                <option value="5" <?php if(isset($_GET['itemcategory']) && $_GET['itemcategory'] == 5) echo 'selected'; ?>>Skillitems</option>
                                <option value="4" <?php if(isset($_GET['itemcategory']) && $_GET['itemcategory'] == 4) echo 'selected'; ?>>Sonstiges</option>
                            </select>
                        </td>
                        <td width="10%" style="boxSchatten" align="center">
                            <input style="width:20%;" type="text" id="itemlevel" onKeyUp="Search()" onchange="Search()" placeholder="1">
                        </td>
                        <td width="10%" style="boxSchatten" align="center">
                            <input style="width:30%;" type="text" id="neededitemlevel" onKeyUp="Search()" placeholder="1">
                        </td>
                    </tr>
                </table>
            </td>
            <td width="18%" style="text-align: center; vertical-align: baseline;">
                <table width="100%" cellspacing="0">
                    <tr>
                        <td colspan=6 class="catGradient borderT borderB" align="center">
                            <b> <span style="color: white"><div class="schatten">Guthaben</div></span> </b>
                        </td>
                    </tr>
                    <tr>
                        <td height="30">Berry: </td>
                        <td style="vertical-align: center;"><?php echo number_format($player->GetBerry(), 0, '', '.'); ?> <img src='<?php echo "img/offtopic/BerrySymbol.png"; ?>' title='Berry' alt='Berry' style="position: relative; top: 5px;"></td>
                    </tr>
                    <tr>
                        <td>Gold: </td>
                        <td style="vertical-align: center;"><?php echo number_format($player->GetGold(), 0, '', '.'); ?> <img src='<?php echo "img/offtopic/GoldSymbol.png"; ?>' title='Gold' alt='Gold' style="position: relative; top: 5px;"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- VERKAUFEN -->
    <table width="27%" cellspacing="0" border="0" style="float:left; margin-left: 1%;">
        <tr>
            <td colspan=6 height="20px">
            </td>
        </tr>
        <tr>
            <td colspan=6 class="catGradient borderT borderB" align="center">
                <b> <span style="color: white"><div class="schatten">Verkaufen</div></span> </b>
            </td>
        </tr>
    </table>
    <div id="sellContainer" style="clear: left; float: left; margin-left: 1%; width: 27%; height: 1000px;">
        <form method="POST" style="height: 100%;" action="?p=market&a=sell">
            <div class="spacer"></div>
            <div id="itemContainer" style="width: 100%; height: 75px; white-space: nowrap;">
                <div class="spacer"></div>
                <div id="itemContainerImage" class="boxSchatten borderL borderT borderR borderB" style="width: 60px; height: 60px; float: left; position: relative; left: 5%;">
                    <?php
                    $i = 0;
                    $item = $inventory->GetItem($i);
                    while(isset($item) && ($item->IsEquipped() || !$item->IsMarktplatz()))
                    {
                        if($item->IsEquipped() || !$item->IsMarktplatz() || $item->IsStored())
                        {
                            ++$i;
                            $item = $inventory->GetItem($i);
                        }
                    }
                    if($item != null)
                    {
                        if ($item->HasOverlay())
                        {
                            ?>
                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png" alt="<?php echo $item->GetName(); ?>" title="<?php echo $item->GetName(); ?>" style="width:60px; height:60px; position:relative; z-index:1;">
                            <?php
                        }

                        ?>
                        <img id="itemimage" class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" alt="<?php echo $item->GetName(); ?>" title="<?php echo $item->GetName(); ?>" style="width:60px; height:60px; position:relative; z-index:0;">
                        <?php
                    }
                    else
                    {
                        ?>
                        <img id="itemimage" class="boxSchatten borderT borderR borderL borderB" style="width:60px; height:60px; position:relative; z-index:0;">
                        <?php
                    }
                    ?>
                </div>
                <div id="itemContainerName" class="boxSchatten borderL borderT borderR borderB" style="width: fit-content; min-width: 100px; max-width: 215px; overflow-x: auto; height: fit-content; margin-top: 5px; margin-left: 20px; padding: 15px 10px 15px 10px; position: relative;">
                    <?php
                    if(isset($item) && !$item->IsEquipped() && $item->IsMarktplatz() && !$item->IsStored())
                        echo $item->GetName();
                    ?>
                </div>
            </div>
            <div class="spacer"></div>
            <select style="height:30px; min-width:80%; width: fit-content; max-width: 100%;" id="itemselect" name="item" class="select select2-market2" onchange="SelectionChange(this.value)">
                <?php

                $i = 0;
                $item = $inventory->GetItem($i);
                while(isset($item))
                {
                    if(!$item->IsEquipped() && $item->IsMarktplatz() && !$item->IsStored())
                    {
                        $item = $inventory->GetItem($i);
                        ?>
                        <option value="<?php echo $i; ?>"><?php echo $item->GetName().' ('.number_format($item->GetAmount(), 0, '', '.').')'; ?></option>
                        <?php
                    }
                    ++$i;
                    $item = $inventory->GetItem($i);
                }

                ?>
            </select>
            <?php

            $i = 0;
            $item = $inventory->GetItem($i);

            while(isset($item))
            {
                if(!$item->IsEquipped() && $item->IsMarktplatz() && !$item->IsStored())
                {
                    $item = $inventory->GetItem($i);
                    ?>
                    <input type="hidden" value="<?php echo $item->GetImage();?>" id="itemimage<?php echo $i;?>"/>
                    <input type="hidden" value="<?php echo $item->GetName();?>" id="itemname<?php echo $i;?>"/>
                    <input type="hidden" value="<?php echo $item->IsPremium();?>" id="itempremium<?php echo $i;?>"/>
                    <input type="hidden" value="<?php echo $item->GetPrice();?>" id="itemprice<?php echo $i;?>"/>
                    <input type="hidden" value="<?php echo $item->GetType();?>" id="itemtype<?php echo $i;?>"/>
                    <input type="hidden" value="<?php echo $item->GetAmount();?>" id="itemamount<?php echo $i;?>"/>
                    <input type="hidden" value="<?php echo $item->GetStatsID();?>" id="itemstatsid<?php echo $i;?>"/>
                    <?php
                }
                ++$i;
                $item = $inventory->GetItem($i);
            }

            ?>
            <div class="spacer"></div>
            <hr />
            <div class="spacer"></div>
            <table>
                <tr style="width: 60%;">
                    <td style="width: 30%; text-align: right;">Anzahl:</td>
                    <td style="width: 30%; text-align: right;"><input type="text" style="width:80%" id="amount" name="amount" placeholder="0"></td>
                    <td style="width: 30%;"><button type="button" onclick="FillInMax();">MAX</button></td>
                </tr>
                <tr>
                    <td colspan="2" height="20px"></td>
                </tr>
                <tr id="offerRow" style="width: 60%;" <?php
                $i = 0;
                $item = $inventory->GetItem($i);
                while(isset($item)) {
                    if (!$item->IsEquipped() && $item->IsMarktplatz() && !$item->IsStored())
                    {
                        if($item->GetType() != 3)
                        {
                            ?>
                            hidden
                            <?php
                        }
                        $item = null;
                    }
                    else
                    {
                        ++$i;
                        $item = $inventory->GetItem($i);
                    }
                }
                ?>
                >
                    <td style="width: 20%; text-align: right;">Startgebot:</td>
                    <td style="width: 30%; text-align: right;"><input type="text" style="width:80%" id="offer" name="offer" placeholder="<?php
                        $i = 0;
                        $item = $inventory->GetItem($i);
                        while(isset($item))
                        {
                            if (!$item->IsEquipped() && $item->IsMarktplatz() && !$item->IsStored())
                            {
                                $item = $inventory->GetItem($i);

                                if (isset($item) && !$item->IsEquipped() && $item->IsMarktplatz() && !$item->IsStored())
                                {
                                    echo round($item->GetPrice() / 2);
                                    if($item->IsPremium())
                                    {
                                        $symbol = "GoldSymbol";
                                        $alt = "Gold";
                                    }
                                    else
                                    {
                                        $symbol = "BerrySymbol";
                                        $alt = "Berry";
                                    }
                                }
                                else
                                    echo '0';
                                $item = null;
                            }
                            else
                            {
                                ++$i;
                                $item = $inventory->GetItem($i);
                            }
                        }
                        ?>"></td>
                    <td style="width: 10%;"><img id="offerSymbol" src='<?php echo "img/offtopic/" . $symbol . ".png"; ?>' alt="<?php echo $alt; ?>" title="<?php echo $alt; ?>" style='position: relative; top: 3px; left: 10px;'></td>
                </tr>
                <tr style="width: 60%;">
                    <td style="width: 20%; text-align: right;">Sofortkauf:</td>
                    <td style="width: 30%; text-align: right;"><input type="text" style="width:80%" id="priceinput"  name="price" placeholder="<?php
                        $i = 0;
                        $item = $inventory->GetItem($i);
                        $pfaditems = array(52, 53, 54, 55, 56, 57);
                        while(isset($item)) {
                            if (!$item->IsEquipped() && $item->IsMarktplatz() && !$item->IsStored()) {
                                $item = $inventory->GetItem($i);

                                if (isset($item) && !$item->IsEquipped() && $item->IsMarktplatz() && !$item->IsStored()) {
                                    if(in_array($item->GetStatsID(), $pfaditems))
                                        echo $item->GetPrice();
                                    else
                                        echo round($item->GetPrice() / 2);
                                }
                                else
                                    echo '0';
                                $item = null;
                            }
                            else
                            {
                                ++$i;
                                $item = $inventory->GetItem($i);
                            }
                        }
                        ?>"></td>
                    <td style="width: 10%;"><img id="instantSymbol" src='<?php echo "img/offtopic/" . $symbol . ".png"; ?>' alt="<?php echo $alt; ?>" title="<?php echo $alt; ?>" style='position: relative; top: 3px; left: 10px;'></td>
                </tr>
            </table>
            <div class="spacer"></div>
            <hr />
            <div class="spacer"></div>
            <table>
                <tr style="width: 60%;">
                    <td style="width: 30%; text-align: right;">Käufer:</td>
                    <td style="width: 30%; text-align: right;"><input style="width:80%;" type="text" id="kaeufername" name="kaeufername" placeholder="Käufer"></td>
                    <td style="width: 10%;"></td>
                </tr>
            </table>
            <div style="position: relative; top: 30px;">
                <b>Achtung!</b><br/>
                Es werden 10% Steuern von dem Erlös abgezogen!
                <div class="spacer"></div>
                <input type="submit" style="width:90%; height: 40px;" id="submit" value="Verkaufen">
            </div>
        </form>
    </div>

    <!-- ANZEIGEN -->
    <table width="70%" cellspacing="0" border="0" style="margin-right: 1%;">
        <tr>
            <td colspan=7 height="20px">
            </td>
        </tr>
        <tr>
            <td colspan=7 class="catGradient borderT borderB" align="center">
                <b> <span style="color: white"><div class="schatten">Marktplatz</div></span> </b>
            </td>
        </tr>
        <tr>
            <td width="10%"  align="center"><b>Bild</b></td>
            <td width="15%" style="cursor:pointer;" align="center" onclick="sortTable('name')"><b>Name</b></td>
            <td width="24%"  align="center"><b>Wirkung</b></td>
            <td width="13%"  align="center"><b>Dauer</b></td>
            <td width="19%" style="cursor:pointer;" align="center" onclick="sortTable('price')"><b>Preis</b></td>
            <?php
            if($player->GetArank() == 3)
            {
                ?>
                <td width="20%" align="center"><b>Verkäufer</b></td>
            <?php
            }
            ?>
            <td width="19%"  align="center"><b>Aktion</b></td>
        </tr>
    </table>
    <div id="MarketContainer" style="border:0 solid red; width:70%; height:1000px; overflow-y:auto; overflow-x: hidden; margin-right: 1%;">
        <?php
        $i = 0;
        $marketItem = $market->GetItem($i);
        while (isset($marketItem))
        {
            if (isset($_GET['itemname']) && $_GET['itemname'] != '' && strpos($marketItem->GetName(), $_GET['itemname']) === false)
            {
                ++$i;
                $marketItem = $market->GetItem($i);
                continue;
            }
            if (isset($_GET['itemcategory']) && $_GET['itemcategory'] != 0 && $_GET['itemcategory'] != $marketItem->GetCategory())
            {
                ++$i;
                $marketItem = $market->GetItem($i);
                continue;
            }
            $seller = new Player($database, $marketItem->GetSellerID());
            if (($seller->IsValid()) && $seller->IsBanned() || $marketItem->GetKaeufer() != 0 && $marketItem->GetKaeufer() != $player->GetID() && $marketItem->GetSellerID() != $player->GetID() || $seller->GetUserID() == $player->GetUserID() && $seller->GetID() != $player->GetID())
            {
                ++$i;
                $marketItem = $market->GetItem($i);
                continue;
            }

            /*
            if($player->GetUserID() == $seller->GetUserID() && $player->GetID() != $seller->GetID() && $player->GetClan() == $seller->GetClan() && ($seller->IsMultiChar() || $player->IsMultiChar()) && $player->GetArank() == 0)
            {
                ++$i;
                $marketItem = $market->GetItem($i);
                continue;
            }

            if($player->GetArank() == 0 && in_array($clans, $seller->GetClan()) && $seller->GetUserID() == $player->GetUserID() && $seller->GetID() != $player->GetID() && $seller->GetClan() != 0)
            {
                ++$i;
                $marketItem = $market->GetItem($i);
                continue;
            }*/


            $result = $database->Select('*', 'accounts', 'userid='.$player->GetUserID().' AND id != '.$player->GetID().' AND clan != 0');
            if($result && $result->num_rows > 0) {
                $clans = array();
                while ($row = $result->fetch_assoc()) {
                    $clans[] = $row['clan'];
                }
            }

            $result = $database->Select('*', 'accounts', 'userid='.$player->GetUserID().' AND id != '.$player->GetID().' AND clan > 0');
            if($result && $result->num_rows > 0)
            {
                $userIDs = array();
                while($row = $result->fetch_assoc())
                {
                    $userIDs[] = $row['clan'];
                }
                if(in_array($seller->GetClan(), $userIDs) && $player->GetArank() == 0)
                {
                    ++$i;
                    $marketItem = $market->GetItem($i);
                    continue;
                }
            }

            if($player->GetClan() != 0)
            {
                $result = $database->Select('*', 'accounts', 'clan=' . $player->GetClan() . ' AND id != '.$player->GetID());
                if ($result && $result->num_rows > 0)
                {
                    $doBreak = false;
                    while($row = $result->fetch_assoc())
                    {
                        if($row['userid'] == $seller->GetUserID() && ($seller->IsMultiChar() || $player->IsMultiChar()) && $player->GetArank() == 0)
                        {
                            $doBreak = true;
                            break;
                        }
                    }
                    if($doBreak)
                    {
                        ++$i;
                        $marketItem = $market->GetItem($i);
                        continue;
                    }
                }
            }
            ?>
            <div class="MarketAnzeigenContainer">
                <div style="width:10%; height:100%; float:left;">
                    <?php if ($marketItem->HasOverlay())
                    {
                        ?>
                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $marketItem->GetOverlay(); ?>.png" alt="<?php echo $marketItem->GetRawName(); ?>" title="<?php echo $marketItem->GetRawName(); ?>" style="width:50px; height:50px; position:relative; z-index:1;">
                        <?php
                    }
                    ?>
                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $marketItem->GetImage(); ?>.png" alt="<?php echo $marketItem->GetRawName(); ?>" title="<?php echo $marketItem->GetRawName(); ?>" style="width:50px; height:50px; position:relative; z-index:0;">
                </div>
                <input type="hidden" class="id" value="<?php echo $i; ?>">
                <input type="hidden" class="seller" value="<?php echo $marketItem->GetSeller(); ?>">
                <input type="hidden" class="buyer" value="<?php echo $marketItem->GetKaeufer() ?>">
                <input type="hidden" class="level" value="<?php echo $marketItem->GetLevel(); ?>">
                <input type="hidden" class="upgrade" value="<?php echo $marketItem->GetUpgrade(); ?>">
                <input type="hidden" class="name" value="<?php echo $marketItem->GetRawName(); ?>">
                <input type="hidden" class="type" value="<?php echo $marketItem->GetCategory(); ?>">
                <input type="hidden" class="price" value="<?php echo $marketItem->GetPrice(); ?>">
                <input type="hidden" class="gold" value="<?php echo $marketItem->IsPremium(); ?>">
                <input type="hidden" class="gebot" value="<?php echo $marketItem->GetGebot(); ?>">
                <input type="hidden" class="sofortkauf" value="<?php echo $marketItem->GetGebot(); ?>">
                <div style="width:15%; float:left;">
                    <?php echo $marketItem->GetName(); ?>
                </div>
                <div style="width: 24%; float:left;">
                    <?php
                    echo $marketItem->DisplayEffect();
                    if($marketItem->GetLevel() != 0)
                    {
                        if($marketItem->GetLevel() > $player->GetLevel())
                            echo '<span style="color:red;">';
                        echo 'Benötigt Level '.number_format($marketItem->GetLevel(), 0, '', '.');
                        if($marketItem->GetLevel() > $player->GetLevel())
                            echo '</span>';
                    }
                    ?>
                </div>
                <div style="width:13%; float:left;">
                    <?php
                    if($marketItem->GetBieter() != '')
                        echo date('d.m.Y', strtotime($marketItem->GetDauer()));
                    ?>
                </div>
                <div style="width:19%; float:left;">
                    <?php
                    if($marketItem->GetGebot() > 0)
                    {
                        ?>
                        Gebot: <span style="white-space: nowrap;"><?php echo number_format($marketItem->GetGebot(), 0, '', '.'); ?> <?php if(!$marketItem->IsPremium()) { echo "<img src='img/offtopic/BerrySymbol.png' title='Berry' alt='Berry' style='position: relative; top: 3px;'>"; } else { echo "<img src='img/offtopic/GoldSymbol.png' title='Gold' alt='Gold' style='position: relative; top: 3px;'>"; } ?></span> <br/>
                        <?php
                    }
                    ?>
                    Sofortkauf: <span style="white-space: nowrap;"><?php echo number_format($marketItem->GetPrice(), 0, '', '.'); ?> <?php if(!$marketItem->IsPremium()) { echo "<img src='img/offtopic/BerrySymbol.png' title='Berry' alt='Berry' style='position: relative; top: 3px;'>"; } else { echo "<img src='img/offtopic/GoldSymbol.png' title='Gold' alt='Gold' style='position: relative; top: 3px;'>"; } ?></span>
                </div>
                <?php
                if($player->GetArank() == 3)
                {
                    ?>
                <div style="width:20%; float:left;"><b><?= $marketItem->GetSeller(); ?></b></div>
                    <?php
                }
                ?>
                <div style="width:19%; float:left;">
                    <?php if($marketItem->GetSellerID() == $player->GetID())
                    {
                        if($marketItem->GetBieter() == 0)
                        {
                            ?>
                            <button onclick="OpenPopupPage('Item Nehmen','market/retake.php?p=<?php echo $_GET['p']; ?>&item=<?php echo $marketItem->GetID(); ?>')">
                                Nehmen
                            </button>
                            <?php
                        }
                        else
                        {
                            ?>
                            <button onclick="OpenPopupPage('Item Nehmen','market/retake.php?p=<?php echo $_GET['p']; ?>&item=<?php echo $marketItem->GetID(); ?>')">
                                Enthält Gebote
                            </button>
                            <?php
                        }
                    }
                    else
                    {
                        ?>
                        <button onclick="OpenPopupPage('Item Kaufen','market/buy.php?p=<?php echo $_GET['p']; ?>&item=<?php echo $marketItem->GetID(); ?>')">
                            Kaufen
                        </button>
                        <?php
                    }
                    if($player->GetArank() == 3)
                    {
                        ?>
                        <br/>
                        <button onclick="OpenPopupPage('Item Entfernen','market/remove.php?p=<?php echo $_GET['p']; ?>&item=<?php echo $marketItem->GetID(); ?>')">
                            Entfernen
                        </button>
                        <?php

                    }
                    ?>
                </div>
            </div>
            <?php
            ++$i;
            $marketItem = $market->GetItem($i);
        }
        ?>
    </div>
    <?php
}
else
{
    echo "Wartung";
}
?>
<div class="spacer"></div>
<script>
    $(document).ready(function() {
        $('.select2-market2').select2({
            tags: true
        });
    });
</script>
<style>
    .select2-search {
        background-color: #1a1b1e;
    }
    .select2-container--default .select2-selection--single {
        background-color: #1a1b1e;
        border: 2px solid #2a2c30;
    }
    .select2-selection__rendered {
        color: #FFFFFF !important;
    }
    .select2-search input {
        background-color: #1a1b1e;
    }
    .select2-results {
        background-color: #1a1b1e;
        color: #FFFFFF;
    }
</style>