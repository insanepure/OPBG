<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
    include_once 'pages/itemzorder.php';
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $( function() {
        $( "#tabs" ).tabs({
            beforeLoad: function( event, ui ) {
                ui.jqXHR.fail(function() {
                    ui.panel.html("Der Tab konnte nicht geladen werden.");
                });
            }
        });
    } );
</script>
<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:90%;">
    <table>
        <tr>
            <td style="width: 90%; text-align: center;">
                <h2>Detailsuche</h2>
            </td>
            <td style="width: 10%; text-align: center;">
                <?php
                if((isset($_GET['player']) && is_numeric($_GET['player'])))
                {
                    if(isset($_GET['player']))
                        $otherPlayer = new Player($database, $_GET['player']);

                    $otherAccount = new Account($accountDB, $otherPlayer->GetUserID());
                    if($player->GetArank() == 3)
                    {
                        ?>
                        <a href="?p=multicheck&chara=<?php echo $otherPlayer->GetName() ?>&score=50">
                            <button>
                                Multicheck
                            </button>
                        </a>
                        <?php
                    }
                }
                ?>
            </td>
        </tr>
    </table>
</div>
<form method="get">
    <table>
        <tr>
            <td>
                <input type="hidden" name="p" value="detailcheck">
                <input type="hidden" name="a" value="search">
                <select class="select" name="player" style="width:200px;">
                    <option value="0">Spieler auswählen</option>
                    <?php
                    $accounts = new Generallist($database, 'accounts', '*', '', '', 99999999999, 'ASC');
                    $id = 0;
                    $entry = $accounts->GetEntry($id);
                    while ($entry != null)
                    {
                        ?>
                        <option value="<?php echo $entry['id']; ?>" <?php if ($_GET['player'] == $entry['id']) echo 'selected'; ?>> <?php echo $entry['name']; ?></option>
                        <?php
                        ++$id;
                        $entry = $accounts->GetEntry($id);
                    }
                    ?>
                </select>
            </td>
            <td>
                <input type="submit" value="Suchen">
            </td>
        </tr>
    </table>
</form>
<?php
if(isset($_GET['a']) && $_GET['a'] == 'search')
{
    if(!isset($_GET['player']) || !is_numeric($_GET['player']) || $_GET['player'] == 0)
    {
        echo 'Ungültiger Spieler!';
    }
    else
    {
        $otherPlayer = new Player($database, $_GET['player']);
        $inventory = $otherPlayer->GetInventory();
        $equippedStats = explode(';', $otherPlayer->GetEquippedStats());
        $titelStats = explode(';', $otherPlayer->GetTitelStats());

        $otherClan = null;
        if ($otherPlayer->GetClan() != 0) {
            $otherClan = new Clan($database, $otherPlayer->GetClan());
        }

        $titelManager = new TitelManager($database);
        $titel = $titelManager->GetTitel($otherPlayer->GetTitel());
        $titelText = '';
        if ($titel != null)
        {
            $titelText = $titel->GetName();
            if ($titel->GetColor() != '')
            {
                $titelText = '<span style="color: #' . $titel->GetColor() . '">' . $titelText . '</span>';
            }
        }
        ?>
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
        </style>
        <div style="width: 100%; min-height: 200px; height: fit-content;">
            <div class="catGradient borderT" style="width:90%;">
                <h2>
                    <a target="_blank" href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>">
                        <?php echo $otherPlayer->GetName(); ?>
                    </a>
                </h2>
            </div>
            <div class="borderB" style="width: 90%; height: 250px;">
                <div style="float: left; width: 20%; height: 200px;">
                    <div class="profileBox boxSchatten" style="width:350px; height:200px;">
                        <center>
                            <table width="100%" cellspacing="0" border="0">
                                <tr>
                                    <td class="catGradient borderB borderT" colspan="6" align="center" style="white-space: nowrap; max-width: 248px;">
                                        <b>&#187; <?php echo $titelText . ' <a target="_blank" href="?p=profil&id='. $otherPlayer->GetID().'">' . $otherPlayer->GetName() . '</a>'; ?> &#171;</b>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </div>
                    <?php
                    if ($otherPlayer->GetImage() == '')
                    {
                        $playerimagenw = "img/imagefail.png";
                    }
                    else
                    {
                        $playerimagenw = $otherPlayer->GetImage();
                    }
                    ?>
                    <img class="profileBox" style="position:relative; left:-67px; top:-125px;" src="<?php echo $playerimagenw; ?>" alt="<?php echo $otherPlayer->GetName(); ?>" title="<?php echo $otherPlayer->GetName(); ?>" width="80" height="80">
                    <div style="z-index:1; position:relative; left:60px; top:-250px; width:150px; text-align:left; font-size:15px;">
                        <b>Level:</b> <?php echo number_format($otherPlayer->GetLevel(),'0', '', '.'); ?><br />
                        <b>Fraktion:</b> <?php echo $otherPlayer->GetRace(); ?><br />
                        <b>Pfad 1:</b> <?php echo $otherPlayer->GetPfad(1); ?><br />
                        <b>Pfad 2:</b> <?php echo $otherPlayer->GetPfad(2); ?><br />
                        <?php echo number_format($otherPlayer->GetBerry(), 0, ',', '.'); ?> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 14px; width: 10px;"/><br />
                        <?php echo number_format($otherPlayer->GetGold(), 0, ',', '.'); ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 14px; width: 14px;"/><br />
                        <?php
                        if($player->GetArank() == 3)
                        {
                            echo $otherAccount->Get('email');
                            echo "<br />".$otherPlayer->GetCreationTime()."<br />";
                        }
                        if ($otherPlayer->IsBanned() == 1)
                        {
                            ?>
                            <b>
                                <span style="color: red;">Gebannt</span>
                            </b>
                            <?php
                        }
                        else
                        {
                            $userTime = strtotime($otherPlayer->GetLastAction());
                            $timeDiffSeconds = time() - $userTime;
                            $timeDiffMinutes = $timeDiffSeconds / 60;
                            $timeDiffHours = $timeDiffMinutes / 60;
                            $timeDiffDays = $timeDiffHours / 24;
                            if ($otherPlayer->IsOnline())
                            {
                                if ($timeDiffMinutes < 10)
                                {
                                    $userstatus = "<span style='color: green;'>Online";
                                }
                                else if ($timeDiffMinutes <= 30)
                                {
                                    $userstatus = "<span style='color: orange;'>AFK";
                                }
                                else
                                {
                                    $userstatus = "<span style='color: red;'>Offline";
                                }
                                ?>
                                <b><?php echo $userstatus ?></span></b><br>
                                <?php
                            }
                            else
                            {
                                ?>
                                <b><span style="color: red;">Offline</span></b><br>
                                <?php
                            }
                            ?><b>Aktiv vor:</b> <?php

                            if ($timeDiffSeconds < 60)
                            {
                                $timeDiffSeconds = floor($timeDiffSeconds);
                                echo number_format($timeDiffSeconds,'0', '', '.');
                                if ($timeDiffSeconds == 1) echo ' Sekunde';
                                else echo ' Sekunden';
                            }
                            else if ($timeDiffMinutes < 60)
                            {
                                $timeDiffMinutes = floor($timeDiffMinutes);
                                echo number_format($timeDiffMinutes,'0', '', '.');
                                if ($timeDiffMinutes == 1) echo ' Minute';
                                else echo ' Minuten';
                            }
                            else if ($timeDiffHours < 24)
                            {
                                $timeDiffHours = floor($timeDiffHours);
                                echo number_format($timeDiffHours,'0', '', '.');
                                if ($timeDiffHours == 1) echo ' Stunde';
                                else echo ' Stunden';
                            }
                            else
                            {
                                $timeDiffDays = floor($timeDiffDays);
                                echo number_format($timeDiffDays,'0', '', '.');
                                if ($timeDiffDays == 1) echo ' Tag';
                                else echo ' Tagen';
                            }
                        }
                        echo "<br />";
                        $otherPlayer->MultiAccounts();
                        ?>
                        <br />
                    </div>
                </div>
                <div style="float: left; width: 28%; height: 200px;">
                    <div class="profileBox boxSchatten" style="width:150px; height:80px;">
                            <span style="text-align: center;">
                                <table width="100%" cellspacing="0" border="0">
                                    <tr>
                                        <td class="catGradient borderB borderT" colspan="6" align="center"><b>&#187; Douriki &#171;</b></td>
                                    </tr>
                                </table>
                                <b>
                                    <span style="font-size: 32px"><?php echo number_format($otherPlayer->GetKI(),'0', '', '.'); ?></span><br/>
                                    <?php
                                    if($otherPlayer->GetRealFakeKI() != 0)
                                    {
                                        ?>
                                        Fake: <span style="font-size: 12px"><?php echo number_format($otherPlayer->GetFakeKI(),'0', '', '.'); ?></span>
                                        <?php
                                    }
                                    ?>
                                </b>
                                <br />
                            </span>
                    </div>
                    <div class="spacer"></div>
                    <div class="spacer"></div>
                    <div class="spacer"></div>
                    <div class="spacer"></div>
                    <div class="profileBox boxSchatten" style="width:180px; height:80px;">
                            <span style="text-align: center;">
                                <table width="100%" cellspacing="0" border="0">
                                    <tr>
                                        <td class="catGradient borderB borderT" colspan="6" align="center"><b>&#187; Kolosseumspunkte &#171;</b></td>
                                    </tr>
                                </table>
                                <b>
                                    <span style="font-size: 32px;"><?php echo number_format($otherPlayer->GetArenaPoints(),'0', '', '.'); ?></span>
                                </b>
                                <br />
                            </span>
                    </div>
                </div>
                <div style="float: left; width: 28%; height: 200px;">
                    <div class="profileBox boxSchatten" style="position: relative; left: -150px; width:250px; height:80px;">
                            <span style="text-align: center;">
                                <table width="100%" cellspacing="0" border="0">
                                    <tr>
                                        <td class="catGradient borderB borderT" colspan="6" align="center"><b>&#187; Aufenthaltsort &#171;</b></td>
                                    </tr>
                                </table>
                                <b>
                                    <span style="font-size: 32px;">
                                        <?php
                                        $planet = new Planet($database, $otherPlayer->GetPlanet());
                                        if($player->GetArank() < 2 && !$planet->IsVisible())
                                        {
                                            echo 'East Blue';
                                        }
                                        else
                                        {
                                            echo $planet->GetName();
                                        }
                                        ?>
                                    </span>
                                </b><br />
                                <b>-
                                    <?php
                                    $actionManager = new ActionManager($database);
                                    $place = new Place($database, $otherPlayer->GetPlace(), $actionManager);

                                    if($player->GetArank() < 2 && $place->GetAdminPlace())
                                    {
                                        $planet = new Planet($database, $otherPlayer->GetPlanet());
                                        if($planet->IsVisible())
                                        {
                                            echo $planet->GetStartingPlace();
                                        }
                                        else
                                        {
                                            $planet = new Planet($database, 1);
                                            echo $planet->GetStartingPlace();
                                        }

                                    }
                                    else
                                    {
                                        echo $place->GetName();
                                    }
                                    ?> -</b>
                            </span>
                    </div>
                </div>
                <div style="float: left; width: 4%; height: 200px;">
                    <?php
                    if($otherClan != null)
                    {
                        $clanpixel = "390";
                        if ($otherPlayer->GetPlanet() == 2) $clanpixel = "400";
                        ?>
                        <div class="profileBox boxSchatten" style="position:relative; width:250px; height:110px; left: -200px; ">
                                    <span style="text-align: center;">
                                        <table width="100%" cellspacing="0" border="0">
                                            <tr>
                                                <td class="catGradient borderB borderT" colspan="6" align="center" style="white-space: nowrap; max-width: 248px;"> <b>&#187; <span style="color: #ffffff;"><a style="color:#ffffff;" href="?p=clan&id=<?php echo $otherClan->GetID(); ?>"><b>[<?php echo $otherClan->GetTag(); ?>] <?php echo $otherClan->GetName(); ?></b></a></span> &#171;</b></td>
                                            </tr>
                                        </table>
                                        <?php if ($otherClan->GetImage() != '')
                                        {
                                            ?>
                                            <br /><img src="<?php echo $otherClan->GetImage(); ?>" width="90px" height="60px">
                                            <?php
                                        }
                                        ?>
                                    </span>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div style="float: left; width: 20%; height: 200px;">
                    <div class="profileBox boxSchatten" style="padding-left: 5px; padding-right: 5px; width:250px; height:210px;">
                        <table width="100%" cellspacing="0" border="0">
                            <tr>
                                <td class="catGradient borderB borderT" colspan="6" align="center">
                                    <b>&#187; Werte &#171;</b>
                                </td>
                            </tr>
                        </table>
                        <span style="float: left;">
                                    <b>LP: </b>
                                </span>
                        <span style="float: right;">
                                    <?php
                                    $plp = $otherPlayer->GetLP();
                                    $pmlp = $otherPlayer->GetMaxLP();
                                    echo number_format($plp,'0', '', '.'); ?> / <?php echo number_format($pmlp,'0', '', '.');
                            $count = 0;
                            $sum = 0;
                            if ($equippedStats[$count] != 0)
                            {
                                $sum += $equippedStats[$count];
                            }
                            if ($titelStats[$count] != 0)
                            {
                                $sum += $titelStats[$count];
                            }
                            if ($sum > 0)
                            {
                                ?>
                                <span style="color: #00bb00;">+<?php echo number_format($sum,'0', '', '.'); ?></span>
                                <?php
                            }
                            else if ($sum < 0)
                            {
                                ?>
                                <span style="color: red;">-<?php echo number_format($sum,'0', '', '.'); ?></span>
                                <?php
                            }
                            ?>
                                </span>
                        <br />
                        <span style="float: left;">
                                    <b>AD: </b>
                                </span>
                        <span style="float: right;">
                                    <?php
                                    $pkp = $otherPlayer->GetKP();
                                    $pmkp = $otherPlayer->GetMaxKP();
                                    echo number_format($pkp,'0', '', '.'); ?> / <?php echo number_format($pmkp,'0', '', '.');
                            $count = 1;
                            $sum = 0;
                            if ($equippedStats[$count] != 0)
                            {
                                $sum += $equippedStats[$count];
                            }
                            if ($titelStats[$count] != 0)
                            {
                                $sum += $titelStats[$count];
                            }
                            if ($sum > 0)
                            {
                                ?>
                                <span style="color: #00bb00;">+<?php echo number_format($sum,'0', '', '.'); ?></span>
                                <?php
                            }
                            else if ($sum < 0)
                            {
                                ?>
                                <span style="color: red;">-<?php echo number_format($sum,'0', '', '.'); ?></span>
                                <?php
                            }
                            ?>
                                </span>
                        <br />
                        <span style="float: left;">
                                    <b>Angriff: </b>
                                </span>
                        <span style="float: right;">
                                    <?php
                                    $pattackl = $otherPlayer->GetAttack();
                                    echo number_format($pattackl,'0', '', '.');
                                    $count = 2;
                                    $sum = 0;
                                    if ($equippedStats[$count] != 0)
                                    {
                                        $sum += $equippedStats[$count];
                                    }
                                    if ($titelStats[$count] != 0)
                                    {
                                        $sum += $titelStats[$count];
                                    }
                                    if ($sum > 0)
                                    {
                                        ?>
                                        <span style="color: #00bb00;">+<?php echo number_format($sum,'0', '', '.'); ?></span>
                                        <?php
                                    }
                                    else if ($sum < 0)
                                    {
                                        ?>
                                        <span style="color: red;">-<?php echo number_format($sum,'0', '', '.'); ?></span>
                                        <?php
                                    }
                                    ?>
                                </span>
                        <br />
                        <span style="float: left;">
                                    <b>Abwehr: </b>
                                </span>
                        <span style="float: right;">
                                    <?php
                                    $pattackl2 = $otherPlayer->GetDefense();
                                    echo number_format($pattackl2,'0', '', '.');
                                    $count = 3;
                                    $sum = 0;
                                    if ($equippedStats[$count] != 0)
                                    {
                                        $sum += $equippedStats[$count];
                                    }
                                    if ($titelStats[$count] != 0)
                                    {
                                        $sum += $titelStats[$count];
                                    }
                                    if ($sum > 0)
                                    {
                                        ?>
                                        <span style="color: #00bb00;">+<?php echo number_format($sum,'0', '', '.'); ?></span>
                                        <?php
                                    }
                                    else if ($sum < 0)
                                    {
                                        ?>
                                        <span style="color: red;">-<?php echo number_format($sum,'0', '', '.'); ?></span>
                                        <?php
                                    }
                                    ?>
                                </span>
                        <br />
                        <span style="float: left;"><b>Tägliche PvP-Kämpfe: </b></span><span style="float: right;"><?php echo number_format($otherPlayer->GetDailyFights(),'0', '', '.'); ?></span><br />
                        <span style="float: left;"><b>Tägliche NPC Kämpfe: </b></span><span style="float: right;"><?php echo number_format($otherPlayer->GetDailyNPCFights(),'0', '', '.'); ?></span><br />
                        <span style="float: left;"><b>Stats Kämpfe: </b></span><span style="float: right;"><?php echo number_format($otherPlayer->GetTotalStatsFights(),'0', '', '.') . " / " . number_format($otherPlayer->GetLevel() * 10,'0', '', '.'); ?></span><br />
                        <span style="float: left;"><b>Elo Punkte: </b></span><span style="float: right;"><?php echo number_format($otherPlayer->GetEloPoints(),'0', '', '.'); ?></span><br />
                        <span style="float: left;">
                                    <b>Clicks: <?php echo $otherPlayer->GetClickCount().' / '.$otherPlayer->GetTotalClickCount(); ?> | Speed: <?php echo $otherPlayer->GetTotalClickCount() == 0 ? 0 : ($otherPlayer->GetClickSpeed()/$otherPlayer->GetTotalClickCount()); ?></b>
                                </span>
                    </div>
                </div>
            </div>
            <div class="borderB" style="width: 90%; height: 730px;">
                <div style="position:relative; top: 5px; float:left; height: 720px; width: 670px;">
                    <div id="equip">
                        <div class="char">
                            <div class="char2" style="z-index:<?php echo $zorders[0]; ?>; background-image: url('img/races/<?php echo $otherPlayer->GetRaceImage(); ?>.png?003')"></div>
                            <div class="char2" style="z-index:11; background-image: url('img/races/<?php echo $otherPlayer->GetRaceImage(); ?>Head.png?003')"></div>
                            <?php if ($otherClan != null)
                            {
                                if($otherClan->GetBanner() != '' && $otherPlayer->ShowWappen())
                                {
                                    ?>
                                    <div class="tooltip" style="z-index:<?php echo $zorders[12]; ?>; position:absolute; left:119px; top:155px;">
                                        <img src="<?php echo $otherClan->GetBanner(); ?>" style="z-index:<?php echo $zorders[11]; ?>; position:absolute; left:50px; top:50px;" width="30px" height="30px">
                                        <span class="tooltiptext"><?php echo $otherClan->GetName(); ?></span>
                                    </div>
                                    <?php
                                }
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
                            <?php ShowSlot($otherPlayer, 1, $inventory); ?>
                            <div class="spacer"></div>
                        </div>
                        <div class="handr borderB borderR borderT borderL">
                            <div class="SideMenuKat catGradient borderB">
                                <div class="schatten">Hände</div>
                            </div>
                            <?php ShowSlot($otherPlayer,2, $inventory); ?>
                            <div class="spacer"></div>
                        </div>
                        <div class="spr borderB borderR borderT borderL">
                            <div class="SideMenuKat catGradient borderB">
                                <div class="schatten">Hose</div>
                            </div>
                            <?php ShowSlot($otherPlayer,3, $inventory); ?>
                            <div class="spacer"></div>
                        </div>
                        <div class="reise borderB borderR borderT borderL">
                            <div class="SideMenuKat catGradient borderB">
                                <div class="schatten">Haki</div>
                            </div>
                            <?php ShowSlot($otherPlayer,4, $inventory); ?>
                            <div class="spacer"></div>
                        </div>
                        <div class="brustr borderB borderR borderT borderL">
                            <div class="SideMenuKat catGradient borderB">
                                <div class="schatten">Brust</div>
                            </div>
                            <?php ShowSlot($otherPlayer,5, $inventory); ?>
                            <div class="spacer"></div>
                        </div>
                        <div class="waffer borderB borderR borderT borderL">
                            <div class="SideMenuKat catGradient borderB">
                                <div class="schatten">Waffe</div>
                            </div>
                            <?php ShowSlot($otherPlayer,6, $inventory); ?>
                            <div class="spacer"></div>
                        </div>
                        <div class="panzr borderB borderR borderT borderL">
                            <div class="SideMenuKat catGradient borderB">
                                <div class="schatten">Accessoire</div>
                            </div>
                            <?php ShowSlot($otherPlayer,8, $inventory); ?>
                            <div class="spacer"></div>
                        </div>
                        <div class="schuhe borderB borderR borderT borderL">
                            <div class="SideMenuKat catGradient borderB">
                                <div class="schatten">Schuhe</div>
                            </div>
                            <?php ShowSlot($otherPlayer,7, $inventory); ?>
                            <div class="spacer"></div>
                        </div>
                        <div class="panzr2 borderB borderR borderT borderL" style="float: left; width: 150px;">
                            <div class="SideMenuKat catGradient borderB">
                                <div class="schatten">Ausrüstung</div>
                            </div>
                            <span style="position:absolute; left:5px;">
                                    <b>Angriff: </b>
                                </span>
                            <?php
                            $equippedStats = explode(';', $otherPlayer->GetEquippedStats());
                            $count = 2;
                            ?>
                            <span style="position:absolute; right:5px;">
                                    <span style="color: #00bb00;">+<?php echo number_format($equippedStats[$count], '0', '', '.'); ?></span>
                                </span><br>
                            <span style="position:absolute; left:5px;">
                                    <b>Abwehr: </b>
                                </span>
                            <?php
                            $count = 3;
                            ?>
                            <span style="position:absolute; right:5px;">
                                    <span style="color: #00bb00;">+<?php echo number_format($equippedStats[$count], '0', '', '.'); ?></span>
                                </span><br>
                            <span style="position:absolute; left:5px;">
                                    <b>LP: </b>
                                </span>
                            <?php
                            $count = 0;
                            ?>
                            <span style="position:absolute; right:5px;">
                                    <span style="color: #00bb00;">+<?php echo number_format($equippedStats[$count], '0', '', '.'); ?></span>
                                </span><br>
                            <span style="position:absolute; left:5px;">
                                    <b>AD: </b>
                                </span>
                            <?php
                            $count = 1;
                            ?>
                            <span style="position:absolute; right:5px;">
                                    <span style="color: #00bb00;">+<?php echo number_format($equippedStats[$count], '0', '', '.'); ?></span>
                                </span><br>
                            <div class="spacer"></div>
                        </div>
                        <div class="panzr2 borderB borderR borderT borderL" style="float: right; margin-left: 150px; width: 150px;">
                            <div class="SideMenuKat catGradient borderB">
                                <div class="schatten">Titel</div>
                            </div>
                            <span style="position:absolute; left:5px;">
                                    <b>Angriff: </b>
                                </span>
                            <?php
                            $titelStats = explode(';', $otherPlayer->GetTitelStats());
                            $count = 2;
                            ?>
                            <span style="position:absolute; right:5px;">
                                    <span style="color: #00bb00;">+<?php echo number_format($titelStats[$count], '0', '', '.'); ?></span>
                                </span><br>
                            <span style="position:absolute; left:5px;">
                                    <b>Abwehr: </b>
                                </span>
                            <?php
                            $count = 3;
                            ?>
                            <span style="position:absolute; right:5px;">
                                    <span style="color: #00bb00;">+<?php echo number_format($titelStats[$count], '0', '', '.'); ?></span>
                                </span><br>
                            <span style="position:absolute; left:5px;">
                                    <b>LP: </b>
                                </span>
                            <?php
                            $count = 0;
                            ?>
                            <span style="position:absolute; right:5px;">
                                    <span style="color: #00bb00;">+<?php echo number_format($titelStats[$count], '0', '', '.'); ?></span>
                                </span><br>
                            <span style="position:absolute; left:5px;">
                                    <b>AD: </b>
                                </span>
                            <?php
                            $count = 1;
                            ?>
                            <span style="position:absolute; right:5px;">
                                    <span style="color: #00bb00;">+<?php echo number_format($titelStats[$count], '0', '', '.'); ?></span>
                                </span><br>
                            <div class="spacer"></div>
                        </div>
                    </div>
                    <div id="inv" style="width: 100%; height: 680px; overflow-x: auto;" hidden>
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
                                    <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Heal</b></td>
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
                                    <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Rüstungs Aufwertung</b></td>
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
                                    <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Kampfitems</b></td>
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
                                    <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Schiffe</b></td>
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
                                    <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Besondere Items</b></td>
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
                                    <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Schätze</b></td>
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
                                    <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Skill Items</b></td>
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
                        ?>
                        <table width="100%" cellspacing="0">
                            <tr>
                                <td class="catGradient borderB borderT" colspan="6" style="text-align:center;"><b>Ausrüstung</b></td>
                            </tr>
                            <tr>
                                <td width="10%">
                                    <div style="text-align:center;">
                                        <b>Bild</b>
                                    </div>
                                </td>
                                <td width="25%">
                                    <div style="text-align:center;">
                                        <b>Item</b>
                                    </div>
                                </td>
                                <td width="35%">
                                    <div style="text-align:center;">
                                        <b>Wirkung</b>
                                    </div>
                                </td>
                                <td width="30%">
                                    <div style="text-align:center;">
                                        <b>Anzahl</b>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $itemManager = new ItemManager($database);
                            $i = 0;
                            $item = $inventory->GetItem($i);
                            while (isset($item))
                            {
                                if ($item->GetType() != 3 && $item->GetType() != 4 || $item->IsEquipped())
                                {
                                    ++$i;
                                    $item = $inventory->GetItem($i);
                                    continue;
                                }
                                ?>
                                <tr height="75px">
                                    <td class="borderT">
                                        <div>
                                            <div style="position:relative; width:60px; height:60px">
                                                <div style="width:60px; height:60px;">
                                                    <?php if ($item->HasOverlay())
                                                    {
                                                        ?>
                                                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="position: absolute; top: 0; width:60px;height:60px; z-index:1;" />
                                                        <?php
                                                    }
                                                    ?>
                                                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="position: absolute; top: 0; width:60px;height:60px; z-index:0;" />
                                                </div>
                                                <span style="position:absolute; right:2px; bottom: 1px; font-size:24px; color:#000;
              text-shadow:
                -1px -1px 0 #fff,
                1px -1px 0 #fff,
                -1px 1px 0 #fff,
                1px 1px 0 #fff;"><b><?php echo number_format($item->GetAmount(), '0', '', '.'); ?></b></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="borderT">
                                        <div style="text-align:center;">
                                            <?php echo $item->GetName(); ?>
                                        </div>
                                    </td>
                                    <td class="borderT">
                                        <div style="text-align:center;">
                                            <?php
                                            echo $item->DisplayEffect();
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
                                </tr>
                                <?php
                                ++$i;
                                $item = $inventory->GetItem($i);
                            }

                            ?>
                        </table>
                    </div>
                    <div id="prom" style="width: 100%; height: 680px; overflow-x: auto;" hidden>
                        <table width="100%" cellspacing="0" border="0">
                            <tr>
                                <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Benutzte Promocodes</b></td>
                            </tr>
                            <tr>
                                <td>ID</td>
                                <td>Promocode</td>
                                <td>Datum - Uhrzeit</td>
                            </tr>
                            <?php
                            $codes = new GeneralList($database, 'usepromocodes', '*', 'userid="'.$otherPlayer->GetID().'"');

                            $id = 0;
                            $entry = $codes->GetEntry($id);
                            while($entry != null)
                            {
                                ?>
                                <tr>
                                    <td><?php echo number_format($entry['id'], 0, '', '.'); ?></td>
                                    <td><?php echo $entry['promocode']; ?>
                                    <td><?php echo $entry['usedate']; ?></td>
                                </tr>
                                <?php
                                $id++;
                                $entry = $codes->GetEntry($id);
                            }
                            ?>
                        </table>
                    </div>
                    <div id="specials" style="width: 100%; height: 680px; overflow-x: auto;" hidden>
                        <table width="100%" cellspacing="0" border="0">
                            <tr>
                                <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Absolvierte Spezialtrainings</b></td>
                            </tr>
                            <tr>
                                <td>ID</td>
                                <td>Name</td>
                                <td>Stats</td>
                            </tr>

                            <?php
                            $Specials = $otherPlayer->GetSpecialTrainings();
                            $SpecialCheck = explode(";", $Specials);
                            $actions = new ActionManager($database);
                            $i = 0;
                            while(isset($SpecialCheck[$i]))
                            {
                                if($SpecialCheck[$i] != "")
                                {
                                    $action = $actions->GetAction($SpecialCheck[$i]);
                                    ?>
                                    <tr>
                                        <td><?= $action->GetID(); ?></td>
                                        <td><?= $action->GetName(); ?></td>
                                        <td><?= number_format($action->GetStats(), 0,'', '.'); ?></td>
                                    </tr>
                                    <?php
                                }
                                ++$i;
                            }
                            ?>
                        </table>
                    </div>
                    <div id="pms" style="width: 100%; height: 680px; overflow-x: auto;" hidden>
                        <table width="100%" cellspacing="0" border="0">
                            <tr>
                                <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Nachrichten</b></td>
                            </tr>
                            <tr>
                                <td>Datum</td>
                                <td>Von</td>
                                <td>Betreff</td>
                                <td>Aktion</td>
                            </tr>
                            <?php
                                $PMManager = new PMManager($database, $otherPlayer->GetID());
                            $PMManager->LoadAllPMs();
                            $i = 0;
                            $pm = $PMManager->GetPM($i);
                            while ($pm != null)
                            {
                            ?>
                            <tr>
                                <td width="25%" class="boxSchatten">
                                    <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><?php if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                                        {
                                            echo "<span style='color: red'>" . $pm->GetTime() . "</span>";
                                        }
                                        else
                                        {
                                            echo $pm->GetTime();
                                        } ?><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                                </td>
                                <td width="25%" class="boxSchatten">
                                    <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><a href="?p=profil&id=<?php echo $pm->GetSenderID(); ?>"><?php if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                                            {
                                                echo "<span style='color: red'>", $pm->GetSenderName(), "</span>";
                                            }
                                            else
                                            {
                                                echo $pm->GetSenderName();
                                            } ?></a><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                                </td>
                                <td width="30%" class="boxSchatten">
                                    <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><?php if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                                        {
                                            echo "<span style='color: red'>" . $pm->GetTopic() . "</span>";
                                        }
                                        else
                                        {
                                            echo $pm->GetTopic();
                                        } ?><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                                </td>
                                <td width="20%" class="boxSchatten">
                                    <center>
                                        <?php if ($pm->GetRead() == 0) echo '<b>'; ?>
                                        <a style="cursor: pointer;" onclick="pm<?php echo $pm->GetID(); ?>.hidden = !pm<?php echo $pm->GetID(); ?>.hidden; if(this.innerHTML == 'Einklappen') { this.innerHTML = 'Ausklappen'} else { this.innerHTML = 'Einklappen'};"><?php if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                                            {
                                                echo "<span style='color: red'>Ausklappen</span></a>";
                                            }
                                            else
                                            {
                                                echo "Ausklappen</a>";
                                            }
                                            if ($pm->GetRead() == 0) echo '</b>'; ?>
                                    </center>
                                </td>
                            </tr>
                            <tr id="<?php echo 'pm' . $pm->GetID(); ?>" hidden>
                                <td colspan="4">
                                    <?php
                                        if ($pm->IsHTML())
                                        {
                                            echo stripcslashes($pm->GetText());
                                        }
                                        else
                                        {
                                            echo $bbcode->parse(htmlspecialchars_decode($pm->GetText()));
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php
                                ++$i;
                                $pm = $PMManager->GetPM($i);
                            }
                            ?>
                        </table>
                    </div>
                    <table style="position: absolute; top: 680px; left: 0px;" width="100%">
                        <tr>
                            <td style="text-align:center">
                                <div class="SideMenuButton" style="cursor: pointer;" onclick="inv.hidden = true; equip.hidden = false; prom.hidden = true; specials.hidden = true; pms.hidden = true;">Ausrüstung</div>
                            </td>
                            <td style="text-align:center">
                                <div class="SideMenuButton" style="cursor: pointer;" onclick="inv.hidden = false; equip.hidden = true; prom.hidden = true; specials.hidden = true; pms.hidden = true;">Inventar</div>
                            </td>
                            <td style="text-align:center">
                                <div class="SideMenuButton" style="cursor: pointer;" onclick="inv.hidden = true; equip.hidden = true; prom.hidden = false; specials.hidden = true; pms.hidden = true;">Promocodes</div>
                            </td>
                            <td style="text-align:center">
                                <div class="SideMenuButton" style="cursor: pointer;" onclick="inv.hidden = true; equip.hidden = true; prom.hidden = true; specials.hidden = false; pms.hidden = true;">Spezialtrainings</div>
                            </td>
                            <td style="text-align:center">
                                <div class="SideMenuButton" style="cursor: pointer;" onclick="inv.hidden = true; equip.hidden = true; prom.hidden = true; specials.hidden = true; pms.hidden = false;">Nachrichten</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="position: relative; top: 5px; float:right; margin-right: 20px; width: 600px; height: 720px;">
                    <div style="float:left; width: 100%;" id="tabs">
                        <ul style="width: 100%; margin: 0; padding-left: 0;">
                            <li class="SideMenuButton" hidden>
                                <a style="display: block; width: 100%; height: 100%; color: white;" href="#tabs-1">Logs</a>
                            </li>
                            <li class="SideMenuButton" style="list-style: none; cursor: pointer; width: 33%; margin-right: 5px; float: left;">
                                <a style="display: block; width: 100%; height: 100%; color: white;" href="/pages/ajax/logs.php?id=<?php echo $otherPlayer->GetID(); ?>">Logs</a>
                            </li>
                            <li class="SideMenuButton" style="list-style: none; cursor: pointer; width: 32%; margin-right: 5px;  float: left;">
                                <a style="display: block; width: 100%; height: 100%; color: white;" href="/pages/ajax/fights.php?id=<?php echo $otherPlayer->GetID(); ?>">Kämpfe (Heute)</a>
                            </li>
                            <li class="SideMenuButton" style="list-style: none; cursor: pointer; width: 33%; float: left;">
                                <a style="display: block; width: 100%; height: 100%; color: white;" href="/pages/ajax/lastfights.php?id=<?php echo $otherPlayer->GetID(); ?>">Kämpfe (Gestern)</a>
                            </li>
                        </ul>
                    </div>
                    <div id="tabs-1"></div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
<script>
    $('.select').select2();
</script>
<style>
    .select2-results {
        color: #000000;
    }
</style>