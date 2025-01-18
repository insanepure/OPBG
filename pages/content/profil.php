<?php
if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] != 0)
{
    $displayedPlayer = new Player($database, $_GET['id']);
    $displayedAccount = new Account($accountDB, $displayedPlayer->GetUserID());
}
if (!isset($displayedPlayer) || (!$displayedPlayer->IsValid() || $displayedPlayer->GetID() == $player->GetID()) || !isset($_GET['id']))
{
    $displayedPlayer = new Player($database, $player->GetID());
    $displayedAccount = new Account($accountDB, $player->GetUserID());
    $isLocalPlayer = true;
}
if($displayedPlayer->IsDeleted() && !$displayedPlayer->IsAdminLogged() && $player->GetArank() < 3)
{
    ?>
    <script type="text/javascript">
        window.location.href="?p=profil";
    </script>
    <?php
    exit;
}
$inventory = $displayedPlayer->GetInventory();
$displayedClan = null;
if ($displayedPlayer->GetClan() != 0)
{
    $displayedClan = new Clan($database, $displayedPlayer->GetClan());
}

$titel = $titelManager->GetTitel($displayedPlayer->GetTitel());
$titelText = '';
if ($titel != null)
{
    $titelText = $titel->GetName();
    if ($titel->GetColor() != '')
    {
        $titelText = '<span style="color: #' . $titel->GetColor() . '">' . $titelText . '</span>';
    }
}
$height = 600;
$top = 0;
if($displayedPlayer->GetBookingDays() > 0) {
    $height = 650;
    $top = 50;
}
?>

<div style="height:<?= $height; ?>px; position:relative; <?php if($displayedPlayer->GetProfileBG() != '') { echo 'background-image: url(img/profilebackgrounds/'. $displayedPlayer->GetProfileBG(). '.png); background-size: 100% 100%;';} ?>">
    <?php
    if($displayedPlayer->GetBookingDays() > 0)
    {
        ?>
        <hr>
        <b><font color="red">Dieser Spieler pausiert aktuell!</font></b><br />
        Er ist voraussichtlich in <?= $displayedPlayer->GetBookingDays(); ?> Tagen wieder verfügbar!
        <hr>
        <div class="spacer"></div>
        <div class="spacer"></div>
        <?php
    }
    ?>
    <div class="spacer"></div>
    <div class="profileBox boxSchatten" style="position:absolute; left:10px; top:<?= 25 + $top; ?>px; width:250px; height:130px;">

        <center>
            <table width="100%" cellspacing="0" border="0">
                <tr>
                    <?php
                    $age = "";
                    if($displayedPlayer->GetShadowAge() == 1)
                    {
                        $age = $displayedPlayer->GetAge() ? "(" . $displayedPlayer->GetAge() . ")" : '';
                        //$age = "(".$displayedPlayer->GetAge().")";
                    }
                    if($displayedPlayer->GetID() == 934 && $displayedPlayer->GetTitel() == 110)
                    {
                        echo "<td class='catGradient borderB borderT' colspan='6' align='center' style='white-space: nowrap; max-width: 248px;'> <b>&#187; " . $displayedPlayer->GetName() . " " . $titelText ." &#171;</b></td>";
                    }
                    else
                    {
                        echo "<td class='catGradient borderB borderT' colspan='6' align='center' style='white-space: nowrap; max-width: 248px;'> <b>&#187; " . $titelText . " " . $displayedPlayer->GetName()." &#171; ".$age."</b></td>";
                    }
                    ?>
                </tr>
            </table>
        </center>
    </div>
    <?php
    if ($displayedPlayer->GetImage() == '')
    {
        $playerimagenw = "img/imagefail.png";
    }
    else
    {
        $playerimagenw = $displayedPlayer->GetImage();
    }
    ?>
    <img class="profileBox" style="position:absolute; left:16px; top:<?= 60 + $top; ?>px;" src="<?php echo $playerimagenw; ?>" alt="<?php echo $displayedPlayer->GetName(); ?>" title="<?php echo $displayedPlayer->GetName(); ?>" width="80" height="80">
    <div style="z-index:400001; position:absolute; left:100px; top:<?= 50 + $top; ?>px; width:150px; height:110px; text-align:left; font-size:15px;">
        <b>Level:</b> <?php echo number_format($displayedPlayer->GetLevel(),'0', '', '.'); ?><br />
        <b>Fraktion:</b> <?php echo $displayedPlayer->GetRace(); ?><br />
        <?php
        if ($isLocalPlayer || $player->GetArank() >= 2)
        {
            $money = $displayedPlayer->GetBerry();
            $gold = $displayedPlayer->GetGold();
            ?>
            <?php echo number_format($money, 0, ',', '.'); ?> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 14px; width: 10px;"/><br />
            <?php echo number_format($gold, 0, ',', '.'); ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 14px; width: 14px;"/><br />
            <?php
        }
        if ($displayedAccount->IsBannedInGame('OPBG') || $displayedPlayer->IsBanned() == 1)
        {
            ?>
            <b>
                <span style="color: red">Gebannt</span>
            </b>
            <?php
        }
        else if($displayedPlayer->IsDeleted() == 1)
        {
            ?>
            <b>
                <span style="color: red">Gelöscht</span>
            </b>
            <?php
        }
        else
        {
            $userTime = strtotime($displayedPlayer->GetLastAction());
            $timeDiffSeconds = time() - $userTime;
            $timeDiffMinutes = $timeDiffSeconds / 60;
            $timeDiffHours = $timeDiffMinutes / 60;
            $timeDiffDays = $timeDiffHours / 24;
            $clan = new Clan($database, $player->GetClan());
            if ($displayedPlayer->IsOnline())
            {
                if ($timeDiffMinutes < 10 && ($displayedPlayer->GetOnlineStatus() == 1 || $displayedPlayer->GetOnlineStatus() == 0 && $player->GetArank() >= 2 || $displayedPlayer->IsFriend($player->GetID()) || $displayedPlayer->GetID() == $player->GetID() || $displayedPlayer->GetClan() == $player->GetClan() && $displayedPlayer->GetClan() != 0 || $player->GetClan() != 0 && in_array($displayedPlayer->GetClan(), $clan->GetAlliances())))
                {
                    $atext = '';
                    if($player->GetArank() >= 2)
                    {
                        if($displayedPlayer->IsStillActive())
                        {
                            $timeString = '';
                            $timeNow = new DateTime();
                            $to_time = strtotime($timeNow->format('Y-m-d H:i:s'));
                            $from_time = strtotime($displayedPlayer->GetActiveSince());
                            $activeSince = round(abs($to_time - $from_time) / 60, 0);
                            $minutes = $activeSince;

                            if($minutes >= 60)
                            {
                                $hours = floor($minutes/60);
                                $minutes = (intval($minutes) - $hours*60);
                            }

                            if(isset($hours) && $hours >= 1)
                                $timeString = $hours . " H ";

                            $timeString = $timeString . $minutes . " M";

                            $atext = ' (Seit '.$timeString.')';
                        }
                    }
                    $userstatus = "<span style='color: green; white-space: nowrap;'>Online" . $atext;
                }
                else if($displayedPlayer->GetOnlineStatus() == 0 && ($displayedPlayer->GetClan() != $player->GetClan() && $player->GetClan() != 0 || $player->GetClan() != 0 && in_array($displayedPlayer->GetClan(), $clan->GetAlliances()) || $player->GetClan() == 0) && $player->GetArank() == 0)
                {
                    $userstatus = "<span style='color: red'>Verborgen</span>";
                }
                else if ($timeDiffMinutes >= 10 and $timeDiffMinutes <= 30)
                {
                    $userstatus = "<span style='color: orange'>AFK";
                }
                else
                {
                    $userstatus = "<span style='color: red'>Offline";
                }
                ?>
                <b><?php echo $userstatus ?></span></b><br>
                <?php
            }
            else
            {
                ?>
                <b><span style="color: red">Offline</span></b><br>
                <?php
            }
            ?><b>Aktiv vor:</b> <?php

            if($displayedPlayer->GetOnlineStatus() == 1 || $displayedPlayer->GetOnlineStatus() == 0 && $player->GetArank() >= 2 || $displayedPlayer->IsFriend($player->GetID()) || $displayedPlayer->GetID() == $player->GetID() || $displayedPlayer->GetClan() == $player->GetClan() && $displayedPlayer->GetClan() != 0 || $player->GetClan() != 0 && in_array($displayedPlayer->GetClan(), $clan->GetAlliances())) {
                if ($timeDiffSeconds < 60) {
                    $timeDiffSeconds = floor($timeDiffSeconds);
                    echo number_format($timeDiffSeconds, '0', '', '.');
                    if ($timeDiffSeconds == 1) echo ' Sekunde';
                    else echo ' Sekunden';
                } else if ($timeDiffMinutes < 60) {
                    $timeDiffMinutes = floor($timeDiffMinutes);
                    echo number_format($timeDiffMinutes, '0', '', '.');
                    if ($timeDiffMinutes == 1) echo ' Minute';
                    else echo ' Minuten';
                } else if ($timeDiffHours < 24) {
                    $timeDiffHours = floor($timeDiffHours);
                    echo number_format($timeDiffHours, '0', '', '.');
                    if ($timeDiffHours == 1) echo ' Stunde';
                    else echo ' Stunden';
                } else {
                    $timeDiffDays = floor($timeDiffDays);
                    echo number_format($timeDiffDays, '0', '', '.');
                    if ($timeDiffDays == 1) echo ' Tag';
                    else echo ' Tagen';
                }
            }
            else
            {
                echo 'Verborgen';
            }
        }
        echo "<br />";
        if($displayedPlayer->GetArank() == 0 && $displayedPlayer->GetID() != $player->GetID() && $player->GetArank() <= 2)
        {
            $displayedPlayer->MultiAccounts();
        }
        ?>
        <br />
    </div>
    <div class="profileBox boxSchatten" style="position:absolute; left:10px; top:<?= 170 + $top; ?>px; width:250px; height:80px;">
        <div style="text-align: center;">
            <table width="100%" cellspacing="0" border="0">
                <tr>
                    <td class="catGradient borderB borderT" colspan="6" align="center"><b>&#187; Douriki &#171;</b></td>
                </tr>
            </table>
            <b>
                <span style="font-size: 32px"><?php echo number_format($displayedPlayer->GetFakeKI(),'0', '', '.'); ?></span>
            </b><br />
        </div>
    </div>

    <div class="profileBox boxSchatten" style="position:absolute; left:15px; top:<?= 280 + $top; ?>px; width:250px; height:80px;">
        <div style="text-align: center;">
            <table width="100%" cellspacing="0" border="0">
                <tr>
                    <td class="catGradient borderB borderT" colspan="6" align="center"><b>&#187; Aufenthaltsort &#171;</b></td>
                </tr>
            </table>
            <b>
        <span style="font-size: 32px">
            <?php
            $displayedplanet = new Planet($database, $displayedPlayer->GetPlanet());
            if($player->GetArank() < 2 && !$displayedplanet->IsVisible())
            {
                echo 'East Blue';
            }
            else
            {
                echo $displayedplanet->GetName();
            }
            ?>
        </span>
            </b><br />
            <b>-
                <?php
                $actionManager = new ActionManager($database);
                $displayedplace = new Place($database, $displayedPlayer->GetPlace(), $actionManager);

                if($player->GetArank() < 2 && $displayedplace->GetAdminPlace())
                {
                    $displayedplanet = new Planet($database, $displayedPlayer->GetPlanet());
                    $hPlanet = new Planet($database, $displayedplanet->GetStartingPlace());
                    if($displayedplanet->IsVisible())
                    {
                        echo $hPlanet->GetName();
                    }
                    else
                    {
                        $displayedplanet = new Planet($database, 1);
                        $hPlanet = new Planet($database, $displayedplanet->GetStartingPlace());
                        echo $hPlanet->GetName();
                    }

                }
                else
                {
                    $hPlace = new Place($database, $displayedPlayer->GetPlace(), null);
                    echo $hPlace->GetName();
                }
                ?> -</b>
        </div>
    </div>
    <?php
    if ($displayedPlayer->GetPlanet() == 2 && $displayedPlayer->GetUserID() != $player->GetUserID() && ($player->IsFriend($displayedPlayer->GetID()) || ($player->GetClan() == $displayedPlayer->GetClan() && $player->GetClan() != 0) || ($displayedPlayer->GetClan() != 0 && in_array($displayedPlayer->GetClan(), $displayedClan->GetAlliances())) || $displayedClan != null && in_array($player->GetClan(), $displayedClan->GetAlliances())))
    {
        ?>
        <div class="profileBox boxSchatten" style="position:absolute; left:15px; top:<?= 360 + $top; ?>px; width:250px; height:25px;">
            <div style="text-align: center;">
                <button onclick="OpenPopupPage('Befreien','profil/freeplayer.php?userid=<?php echo $displayedPlayer->GetUserID(); ?>&id=<?php echo $displayedPlayer->GetID(); ?>')">
                    Befreien
                </button>
                <br />
            </div>
        </div>
        <?php
    }

    if ($displayedClan != null)
    {
        $clanpixel = 390 + $top;;
        if ($displayedPlayer->GetPlanet() == 2) $clanpixel = 400 + $top;
        ?>
        <div class="profileBox boxSchatten" style="position:absolute; left:15px; top:<?= $clanpixel; ?>px; width:250px; height:110px;">
            <center>
                <table width="100%" cellspacing="0" border="0">
                    <tr>
                        <td class="catGradient borderB borderT" colspan="6" align="center" style="white-space: nowrap; max-width: 248px; color:#ffffff;"> <b>&#187; <a href="?p=clan&id=<?php echo $displayedClan->GetID(); ?>"><b>[<?php echo $displayedClan->GetTag(); ?>] <?php echo $displayedClan->GetName(); ?></b></a> &#171;</b></td>
                    </tr>
                    <?php
                    if($isLocalPlayer)
                    {
                        ?>
                        <tr>
                            <td style="height: 15px; color:#ffffff;">
                                <div class="tooltip" style="position: relative; top:0; right: -230px;">
                                    <form name="wappenform" action="?p=profil&a=wappen" method="post">
                                        <input onclick="wappenform.submit();" type="checkbox" name="wappen" style="cursor: pointer;" <?php if($player->ShowWappen()) echo 'checked'; ?>>
                                        <span class="tooltiptext" style="width:180px; top:-35px; left:-85px;">
                                            Wappen anzeigen?
                                        </span>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php if ($displayedClan->GetImage() != '')
                {
                    if(!$isLocalPlayer)
                    {
                        ?>
                        <br/>
                        <?php
                    }
                    ?>
                    <img src="<?php echo $displayedClan->GetImage(); ?>" width="90px" height="60px">
                    <?php
                }
                ?>
            </center>
        </div>
        <?php
    }
    ?>
    <div class="profilecharacter" style="top:<?= 20 + $top; ?>px; left:185px; z-index:<?php echo $zorders[0]; ?>; background-image: url('img/races/<?php echo $displayedPlayer->GetRaceImage(); ?>.png?003'"></div>
    <div class="profilecharacter" style="top:<?= 20 + $top; ?>px; left:185px; z-index:11; background-image: url('img/races/<?php echo $displayedPlayer->GetRaceImage(); ?>Head.png?003'"></div>
    <?php if ($displayedClan != null && $displayedClan->GetBanner() != '' && $displayedPlayer->ShowWappen())
    {
        ?>
        <div class="tooltip" style="z-index:<?php echo $zorders[12]; ?>; position:absolute; left:305px; top:<?= 145 + $top; ?>px;">
            <img src="<?php echo $displayedClan->GetBanner(); ?>" style="z-index:<?php echo $zorders[11]; ?>; position:absolute; left:50px; top:<?= 50 + $top; ?>px;" width="30px" height="30px">
            <span class="tooltiptext"><?php echo $displayedClan->GetName(); ?></span>
        </div>
        <?php
    }
    if ($displayedPlayer->GetPlanet() == 2)
    {
        ?>
        <div class="profilecharacter" style="top:<?= 20 + $top; ?>px; left:185px; z-index:<?php echo $zorders[5]; ?>; background-image: url('img/ausruestung/ImpelDownOben.png?002'"></div>
        <div class="profilecharacter" style="top:<?= 20 + $top; ?>px; left:185px; z-index:<?php echo $zorders[2]; ?>; background-image: url('img/ausruestung/FesselnOben2.png?002'"></div>
        <div class="profilecharacter" style="top:<?= 20 + $top; ?>px; left:185px; z-index:<?php echo $zorders[3]; ?>; background-image: url('img/ausruestung/ImpelDownUnten.png?002'"></div>
        <div class="profilecharacter" style="top:<?= 20 + $top; ?>px; left:185px; z-index:<?php echo $zorders[7]; ?>; background-image: url('img/ausruestung/Fesseln.png?002'"></div>
        <?php
    }
    else
    {
        ShowSlotEquippedImage(6, $inventory, $zorders, $zordersOnTop, $player, $top); //Waffe
        ShowSlotEquippedImage(1, $inventory, $zorders, $zordersOnTop, $player, $top); //Aura
        ShowSlotEquippedImage(5, $inventory, $zorders, $zordersOnTop, $player, $top); // Brust
        ShowSlotEquippedImage(8, $inventory, $zorders, $zordersOnTop, $player, $top); //Accessoire
        ShowSlotEquippedImage(3, $inventory, $zorders, $zordersOnTop, $player, $top); //Hose
        ShowSlotEquippedImage(2, $inventory, $zorders, $zordersOnTop, $player, $top); // Fesseln
        ShowSlotEquippedImage(7, $inventory, $zorders, $zordersOnTop, $player, $top); //Schuhe
        ShowSlotEquippedImage(4, $inventory, $zorders, $zordersOnTop, $player, $top); //Reise
    }
    ?>

    <div class="profileBox boxSchatten" style="position:absolute; right:10px; top:<?= 25 + $top; ?>px; width:240px; height:120px;">
        <table width="100%" cellspacing="0" border="0">
            <tr>
                <td class="catGradient borderB borderT" colspan="6" align="center">
                    <b>&#187; User Option &#171;</b>
                </td>
            </tr>
        </table>
        <div class="tooltip" style="margin-left:-80px; position: absolute;">
            <a href="#" id="kampf2" onclick="OpenPopupPage('Private Nachricht','profil/pmpopup.php?name=<?php echo $displayedPlayer->GetName(); ?>')"><img src="img/pm.png" width="45px" height="45px"></a>
            <span class="tooltiptext" style="width:180px; top:-30px; left:-70px;">Private Nachricht</span>
        </div>
        <div class="tooltip" style="margin-left:-20px; position: absolute;">
            <a href="#" id="kampf2" onclick="OpenPopupPage('Herausforderung','profil/challenge.php?id=<?php echo $displayedPlayer->GetID(); ?>')"><img src="img/battel2.png" width="45px" height="45px"></a>
            <span class="tooltiptext" style="width:180px; top:-30px; left:-70px;">Herausfordern</span>
        </div>
        <div class="tooltip" style="margin-left:40px; position: absolute;">
            <a href="#" id="kampf2" onclick="OpenPopupPage('Gruppeneinladung','profil/gruppe.php?id=<?php echo $displayedPlayer->GetID(); ?>')"><img src="img/gruppe.png" width="45px" height="45px"></a>
            <span class="tooltiptext" style="width:180px; top:-30px; left:-70px;">Gruppeneinladung</span>
        </div>
        <br style="line-height:120%" /><br>
        <div class="tooltip" style="margin-top:10px; margin-left:-80px; position: absolute;">
            <a href="#" id="kampf2" onclick="OpenPopupPage('Blockieren','profil/block.php?id=<?php echo $displayedPlayer->GetID(); ?>&userid=<?php echo $displayedPlayer->GetUserID(); ?>')"><img src="img/block.png" width="45px" height="45px"></a>
            <span class="tooltiptext" style="width:180px; top:-30px; left:-70px;">Blockieren</span>
        </div>
        <div class="tooltip" style="margin-top:10px; margin-left:-20px; position: absolute;">
            <a href="#" id="kampf2" onclick="OpenPopupPage('Befreunden','profil/freund.php?id=<?php echo $displayedPlayer->GetID(); ?>&userid=<?php echo $displayedPlayer->GetUserID(); ?>')"><img src="img/freund.png?002" width="45px" height="45px"></a>
            <span class="tooltiptext" style="width:180px; top:-30px; left:-70px;">Befreunden</span>
        </div>
        <br style="line-height:120%" />
    </div>

    <?php
    if ($isLocalPlayer || $player->GetArank() >= 2)
    {
        $equippedStats = explode(';', $displayedPlayer->GetEquippedStats());
        $titelStats = explode(';', $displayedPlayer->GetTitelStats());
        if ($isLocalPlayer || $player->GetArank() >= 2)
        {
            ?>
            <div class="profileBox boxSchatten" style="position:absolute; right:10px; top:<?= 170 + $top; ?>px; width:240px; height:210px;">
                <table width="100%" cellspacing="0" border="0">
                    <tr>
                        <td class="catGradient borderB borderT" colspan="6" align="center">
                            <b>&#187; Werte &#171;</b>
                            <?php
                            if ($player->GetARank() >= 2 && $player->GetID() != $displayedPlayer->GetID())
                            {
                                ?>
                                (<a href="#" onclick="OpenPopupPage('Kopieren','profil/admin.php?id=<?php echo $displayedPlayer->GetID(); ?>&a=copy')">Kopieren</a>)
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                <span style="position:absolute; left:5px;">
          <b>LP: </b>
        </span>
                <span style="position:absolute; right:5px;">
          <?php
          $plp = $displayedPlayer->GetLP();
          $pmlp = $displayedPlayer->GetMaxLP();
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
                        <span style="color: #00bb00">+<?php echo number_format($sum,'0', '', '.'); ?></span>
                        <?php
                    }
                    else if ($sum < 0)
                    {
                        ?>
                        <span style="color: red">-<?php echo number_format($sum,'0', '', '.'); ?></span>
                        <?php
                    }
                    ?>
        </span>
                <br />
                <span style="position:absolute; left:5px;">
          <b>AD: </b>
        </span>
                <span style="position:absolute; right:5px;">
          <?php
          $pkp = $displayedPlayer->GetKP();
          $pmkp = $displayedPlayer->GetMaxKP();
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
                        <span style="color: #00bb00">+<?php echo number_format($sum,'0', '', '.'); ?></span>
                        <?php
                    }
                    else if ($sum < 0)
                    {
                        ?>
                        <span style="color: red">-<?php echo number_format($sum,'0', '', '.'); ?></span>
                        <?php
                    }
                    ?>
        </span>
                <br />
                <span style="position:absolute; left:5px;">
          <b>Angriff: </b>
        </span>
                <span style="position:absolute; right:5px;">
          <?php
          $pattackl = $displayedPlayer->GetAttack();
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
              <span style="color: #00bb00">+<?php echo number_format($sum,'0', '', '.'); ?></span>
              <?php
          }
          else if ($sum < 0)
          {
              ?>
              <span style="color: red">-<?php echo number_format($sum,'0', '', '.'); ?></span>
              <?php
          }
          ?>
        </span>
                <br />
                <span style="position:absolute; left:5px;">
          <b>Abwehr: </b>
        </span>
                <span style="position:absolute; right:5px;">
          <?php
          $pattackl2 = $displayedPlayer->GetDefense();
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
              <span style="color: #00bb00">+<?php echo number_format($sum,'0', '', '.'); ?></span>
              <?php
          }
          else if ($sum < 0)
          {
              ?>
              <span style="color: red">-<?php echo number_format($sum,'0', '', '.'); ?></span>
              <?php
          }
          ?>
        </span>
                <br />
                <?php
                $info = $displayedPlayer->GetLevel() * 10;
                ?>
                <span style="position:absolute; left:5px;"><b>Tägliche PvP-Kämpfe: </b></span><span style="position:absolute; right:5px;"><?php echo number_format($displayedPlayer->GetDailyFights(),'0', '', '.'); ?> / <?php echo number_format($displayedPlayer->GetPvPMaxFights(),'0', '', '.'); echo $taley2 ?></span><br />
                <span style="position:absolute; left:5px;"><b>Tägliche Elokämpfe: </b></span><span style="position:absolute; right:5px;"><?php echo number_format($displayedPlayer->GetDailyEloFights(),'0', '', '.'); ?> / <?php echo number_format($displayedPlayer->GetDailyMaxElofights(),'0', '', '.'); echo $taley2 ?></span><br />
                <span style="position:absolute; left:5px;"><b>Tägliche NPC Kämpfe: </b></span><span style="position:absolute; right:5px;"><?php echo number_format($displayedPlayer->GetDailyNPCFights(),'0', '', '.'); ?> / <?php echo number_format($displayedPlayer->GetDailyNPCFightsMax(),'0', '', '.'); echo $taley ?></span><br />
                <span style="position:absolute; left:5px;"><b>Stats Kämpfe: </b></span><span style="position:absolute; right:5px;"><?php echo number_format($displayedPlayer->GetTotalStatsFights(),'0', '', '.') . " / " . number_format($info,'0', '', '.'); ?></span><br />
                <span style="position:absolute; left:5px;"><b>Elo Punkte: </b></span><span style="position:absolute; right:5px;"><?php echo number_format($displayedPlayer->GetEloPoints(),'0', '', '.'); ?></span><br />
            </div>
            <?php
        }
        ?>
        <?php if ($player->GetArank() >= 2) {
        ?>
        <div class="profileBox boxSchatten" style="position:absolute; right:10px; top:<?= 370 + $top; ?>px; width:300px; height:150px;">
            <table width="100%" cellspacing="0" border="0">
                <tr>
                    <td class="catGradient borderB borderT" colspan="2" align="center">
                        <b>Charaktere
                            <?php
                            if($player->GetARank() >= 2) {
                                echo '<a href="?p=profil&id='.$displayedPlayer->GetID().'&a=freefight">(Aus Kampf befreien)</a></b>';
                            }
                            ?>
                    </td>
                </tr>
                <?php if ($player->GetArank() >= 2)
                {
                    if($player->GetArank() == 3)
                    {
                        ?>
                        <tr>
                            <td>
                                <form name="form1" action="?p=profil&id=<?php echo $displayedPlayer->GetID(); ?>&a=adminlogin" method="post" enctype="multipart/form-data">
                                    <center><input type="submit" value="Einloggen"></center>
                                </form>
                            </td>
                            <td>
                                <center><a href="?p=admin&a=see&table=accounts&id=<?php echo $displayedPlayer->GetID(); ?>"><input type="submit" value="Editieren"></a></center>
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td>
                            <center><a href="?p=detailcheck&a=search&player=<?php echo $displayedPlayer->GetID(); ?>" target="blank"><input type="submit" value="Detailsuche"></a></center>
                            </form>
                        </td>
                        <?php
                        if($player->GetArank() == 3)
                        {
                            ?>
                            <td>
                                <center><a href="?p=admin&table=inventory&pid=<?php echo $displayedPlayer->GetID(); ?>"><input type="submit" value="Inventar"></a></center>
                                </form>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td>
                        <form name="form1" action="?p=adminmod&user=<?php echo $displayedPlayer->GetID(); ?>" method="post" enctype="multipart/form-data">
                            <center><input type="submit" value="Moderieren"></center>
                        </form>
                    </td>
                    <td>
                        <form name="form1" action="?p=adminwarn&user=<?= $displayedPlayer->GetID(); ?>" method="post" enctype="multipart/form-data">
                            <!--<input type="hidden" name="user" value="<?= $displayedPlayer->GetID(); ?>">-->
                            <center><input type="submit" value="Verwarnen"></center>
                        </form>
                    </td>
                </tr>
            </table>
            <?php
            if($displayedAccount->Get('id') != 244)
            {
                echo '<span style="position:absolute; left:5px;">';
                echo '<b>Multi: </b>';
                $select = "id, name";
                $where = 'userid=' . $displayedPlayer->GetUserID() . '';
                $order = 'id';
                $from = 'accounts';
                $list = new Generallist($database, $from, $select, $where, $order, 100, 'DESC');

                $id = 0;
                $entry = $list->GetEntry($id);
                while ($entry != null)
                {
                    ?>
                    <a href="?p=profil&id=<?php echo $entry['id']; ?>">
                        <?php echo $entry['name']; ?>
                    </a>
                    <?php
                    ++$id;
                    $entry = $list->GetEntry($id);
                }
                echo "</span>";
            }
            ?>
            <span style="position:absolute; left:5px; bottom:6px; font-size:14px;">
            <b>Clicks: <?php echo $displayedPlayer->GetClickCount().' / '.$displayedPlayer->GetTotalClickCount(); ?> | Speed: <?php echo $displayedPlayer->GetTotalClickCount() == 0 ? 0 : ($displayedPlayer->GetClickSpeed()/$displayedPlayer->GetTotalClickCount()); ?></b>
        </span>
        </div>
        <?php
    }
    }
    ?>
</div>
<div class="profileBox boxSchatten" style="position:absolute; left:34px; top:<?= 525 + $top; ?>px; width:600px;">
    <table width="100%" cellspacing="0" border="0">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" align="center"> <b>Statistik</b></td>
        </tr>
    </table>
    <?php
    $totalFights = StatsList::GetEntryOrEmpty($database, $displayedPlayer->GetID(), -1);
    $sFights = StatsList::GetEntryOrEmpty($database, $displayedPlayer->GetID(), 0);
    $wFights = StatsList::GetEntryOrEmpty($database, $displayedPlayer->GetID(), 1);
    $tFights = StatsList::GetEntryOrEmpty($database, $displayedPlayer->GetID(), 2);
    $nFights = StatsList::GetEntryOrEmpty($database, $displayedPlayer->GetID(), 3);
    $aFights = StatsList::GetEntryOrEmpty($database, $displayedPlayer->GetID(), 8);
    ?>
    <b>
        Verloren: <?php echo number_format($totalFights['loose'],'0', '', '.'); ?> | Gewonnen: <?php echo number_format($totalFights['win'],'0', '', '.'); ?> | Unentschieden: <?php echo number_format($totalFights['draw'],'0', '', '.'); ?><br />
        Spaß: <?php echo number_format($sFights['total'],'0', '', '.'); ?> | PvP: <?php echo number_format($wFights['total'],'0', '', '.'); ?> | NPC: <?php echo number_format($nFights['total'],'0', '', '.'); ?> | Kolosseum: <?php echo number_format($aFights['total'],'0', '', '.'); ?><br />
        Erstellte Artikel: <?php echo number_format($displayedPlayer->GetCountArticle(),'0', '', '.'); ?><br />
    </b>
</div>
<div class="spacer"></div>
<?php
if($player->GetArank() >= 4 && $isLocalPlayer)
{
    ?>
    <div class="profileBox boxSchatten" style="position:relative; width:600px;">
        <table width="100%" cellspacing="0" border="0">
            <tr>
                <td class="catGradient borderB borderT" colspan="6" align="center"> <b>Adventskalender</b></td>
            </tr>
        </table>
        <img src="/img/gameevents/Adventskalender2022.png">
    </div>
    <div class="spacer"></div>
    <?php
}
?>
<div class="profileBox boxSchatten" style="width:600px; min-height:100px; word-wrap: break-word; overflow:hidden; position:relative;">
    <table width="100%" cellspacing="0" border="0">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" align="center"> <b>Auszeichnungen</b></td>
        </tr>
    </table>
    <table>
        <tr>
            <?php
            $titelNameOne = '';
            $titelNameTwo = '';
            $titelNameThree = '';
            if ($displayedPlayer->GetRankOne() > 0)
                $titelNameOne = $titelManager->GetTitel($displayedPlayer->GetRankOne())->GetName();
            else
                $titelNameOne = 'Keine Auszeichnung';
            if ($displayedPlayer->GetRankTwo() > 0)
                $titelNameTwo = $titelManager->GetTitel($displayedPlayer->GetRankTwo())->GetName();
            else
                $titelNameTwo = 'Keine Auszeichnung';
            if ($displayedPlayer->GetRankThree() > 0)
                $titelNameThree = $titelManager->GetTitel($displayedPlayer->GetRankThree())->GetName();
            else
                $titelNameThree = 'Keine Auszeichnung';
            ?>
            <td>
                <div class="catGradient borderB borderT" align="center"><?php echo "<b>" . $titelNameOne . "</b>"; ?></div>
                <?php
                if ($displayedPlayer->GetRankOne() != 0 && $titelManager->GetTitel($displayedPlayer->GetRankOne())->GetTitelPic() != "")
                    echo "<img width='195' src='" . $titelManager->GetTitel($displayedPlayer->GetRankOne())->GetTitelPic() . "?002' />";
                else echo "<img width='195' src='img/titel/norank.png?002' />";
                ?>
            </td>
            <td>
                <div class="catGradient borderB borderT" align="center"><?php echo "<b>" . $titelNameTwo . "</b>"; ?></div>
                <?php
                if ($displayedPlayer->GetRankTwo() != 0 && $titelManager->GetTitel($displayedPlayer->GetRankTwo())->GetTitelPic() != "")
                    echo "<img width='195' src='" . $titelManager->GetTitel($displayedPlayer->GetRankTwo())->GetTitelPic() . "?002' />";
                else echo "<img width='195' src='img/titel/norank.png?002' />";
                ?>
            </td>
            <td>
                <div class="catGradient borderB borderT" align="center"><?php echo "<b>" . $titelNameThree . "</b>"; ?></div>
                <?php
                if ($displayedPlayer->GetRankThree() != 0 && $titelManager->GetTitel($displayedPlayer->GetRankThree())->GetTitelPic() != "")
                    echo "<img width='195' src='" . $titelManager->GetTitel($displayedPlayer->GetRankThree())->GetTitelPic() . "?002' />";
                else echo "<img width='195'  src='img/titel/norank.png?002' />";
                ?>
            </td>
        </tr>
    </table>
</div>
<div class="spacer"></div>
<?php

$pTitelsCount = count($displayedPlayer->GetTitels());

if ($pTitelsCount <= 10)
{
    echo "<img width='585' src='img/titel/10reihe.png' />";
}
else if ($pTitelsCount >= 11 && $pTitelsCount < 50)
{
    echo "<img width='585' src='img/titel/11reihe.png' />";
}
else if ($pTitelsCount >= 50 && $pTitelsCount < 100)
{
    echo "<img width='585' src='img/titel/50reihe.png' />";
}
else if ($pTitelsCount >= 100 && $pTitelsCount < 250)
{
    echo "<img width='585' src='img/titel/100reihe.png' />";
}
else if ($pTitelsCount >= 250 && $pTitelsCount < 500)
{
    echo "<img width='585' src='img/titel/250reihe.png' />";
}
else if ($pTitelsCount >= 500 && $pTitelsCount < 750)
{
    echo "<img width='585' src='img/titel/400reijhe.png' />";
}
else if ($pTitelsCount >= 750 && $pTitelsCount < 1000)
{
    echo "<img width='585' src='img/titel/550reijhe.png' />";
}
else if ($pTitelsCount >= 1000 && $pTitelsCount < 1250)
{
    echo "<img width='585' src='img/titel/650reijhe.png' />";
}
else if ($pTitelsCount >= 1250 && $pTitelsCount < 1500)
{
    echo "<img width='585' src='img/titel/650reijhe.png' />";
}
else if ($pTitelsCount >= 1500 && $pTitelsCount < 2000)
{
    echo "<img width='585' src='img/titel/750reihe.png' />";
}
else if ($pTitelsCount >= 2000 && $pTitelsCount < 2250)
{
    echo "<img width='585' src='img/titel/900reihe.png' />";
}
else if ($pTitelsCount >= 2250 && $pTitelsCount < 2500)
{
    echo "<img width='585' src='img/titel/1000reihe.png' />";
}
else if ($pTitelsCount >= 2500 && $pTitelsCount < 3000)
{
    echo "<img width='585' src='img/titel/1250reihe.png' />";
}
else if ($pTitelsCount >= 3000 && $pTitelsCount < 3500)
{
    echo "<img width='585' src='img/titel/1250reihe.png' />";
}
?>
<br />
<style>
    .allrankpicture{
        position: relative;
        min-height: 100px;
    }
</style>
<div class="allrankpicture">
    <?php
        $gettrophys = explode(";", $displayedPlayer->GetEloTrophaeen());
        $i = 0;
        while(isset($gettrophys[$i]))
        {
            $levelone = 'img/marketing/bronze.png';
            $leveltwo = 'img/marketing/silber.png';
            $levelthree = 'img/marketing/gold.png';
            $levelfour = 'img/marketing/dia.png';
            if($gettrophys[$i] == 1)
            {
                echo '<img width="75" height="75" src="'.$levelfour.'" />';
            }
            else if($gettrophys[$i] == 2)
            {
                echo '<img width="75" height="75" src="'.$levelthree.'" />';
            }
            else if($gettrophys[$i] == 3)
            {
                echo '<img width="75" height="75" src="'.$leveltwo.'" />';
            }
            else if($gettrophys[$i] > 3 && $gettrophys[$i] <= 50)
            {
                echo '<img width="75" height="75" src="'.$levelone.'" />';
            }
            $i++;
        }
    ?>
</div>
<br />
<style>
    .bild_beschriftung {
        position: relative;
    }

    .bild_beschriftung img {
        display: block;
        width: auto;
        height: auto;
    }

    .bild_beschriftung span {
        position: absolute;
        bottom: 0;
        left: 3em;
        width: 300px;
        color: black;
        text-align: center;
        height: 2.8em;
        line-height: 2.5em;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 60px;
        font-weight: bold;
    }

    .kopfgeldbild {
        position: absolute;
        color: #fff;
        text-align: center;
        bottom: 170px;
        left: 210px;
    }
</style>
<!--<div class="bild_beschriftung">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC">
    <img src="<?php echo $serverUrl; ?>img/offtopic/onepieceWantedpic.png" alt="color_key" />
    <div class="kopfgeldbild">
        <?php echo "<img src='" . $displayedPlayer->GetPvPImage() . "'/>" ?>
    </div>
    <span style="font-family: 'Playfair Display SC', serif; font-weight:bold; font-size:30px; top:<?= 25 + $top; ?>px; left:182px; color:#5e4c46; text-shadow: 0.5px  0.5px 0.5px black,0.5px -0.5px 0.5px black,-0.5px  0.5px 0.5px black,-0.5px -0.5px 0.5px black;"><?php echo $displayedPlayer->GetName(); ?><br /></span>
    <span style="font-family: 'Playfair Display SC', serif;left:182px; top:<?= 25 + $top; ?>px; font-size:32px; color:#5e4c46; text-shadow: 0.5px  0.5px 0.5px black,0.5px -0.5px 0.5px black,-0.5px  0.5px 0.5px black,-0.5px -0.5px 0.5px black;"><?php echo number_format($displayedPlayer->GetPvP()); ?></span>
</div>
<div class="spacer"></div>-->

<div class=" spacer">
</div>
<div class="profileBox boxSchatten" style="width:600px; min-height:200px; word-wrap: break-word; overflow:hidden;">
    <?php
    if($displayedPlayer->GetWarnsCount() > 0)
    {
        if ($displayedPlayer->GetWarnsCount() == 1)
        {
            $color = '#008000';
        }
        else if ($displayedPlayer->GetWarnsCount() == 2)
        {
            $color = '#FFFF00';
        }
        else if ($displayedPlayer->GetWarnsCount() == 3)
        {
            $color = '#FF0000';
        }
        ?>
        <span style="font-size: 24px; color: <?php echo $color; ?>">Dieser Spieler wurde bisher <?php echo $displayedPlayer->GetWarnsCount(); ?>x Verwarnt!</span>
        <?php
        if ($player->GetID() == $displayedPlayer->GetID())
        {
            ?>
            <br />
            <span style="color: #FF0000">Solltest du der Meinung sein, dass du eine Verwarnung zu Unrecht erhalten hast,<br>dann melde dich bitte über ein <a href="?p=ticketsystem">Ticket</a> bei uns.</span>
            <div class="spacer"></div>
            <?php
            $now = time(); // or your date as well
            $your_date = strtotime($displayedPlayer->GetWarns()[0]['expires']);
            $count = round(($your_date-$now)/60/60/24);
            if($displayedPlayer->GetWarnsCount() > 1)
                $text = "Verwarnungen werden";
            else
                $text = "Verwarnung wird";
            echo "Diese ". (($displayedPlayer->GetWarnsCount() > 1) ? 'Verwarnungen werden' : 'Verwarnung wird') ." in ".$count." Tagen automatisch gelöscht!";
        }
    }
    ?>
    <table width="100%" cellspacing="0" border="0">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" align="center"><b>Beschreibung</b></td>
        </tr>
    </table>
    <?php echo $bbcode->parse($displayedPlayer->GetText()); ?>
</div>
<div class="spacer"></div>
<div class="spacer"></div>
<div class="profileBox boxSchatten" style="width:600px; min-height:100px; max-height:250px; word-wrap: break-word; overflow-y: auto;">
    <table width="100%" cellspacing="0" border="0">
        <tr>
            <td colspan="6" class="catGradient borderT borderB">
                <b>
                                  <span style="text-align: center">
                                    <span style="color: white">
                                      <div class="schatten">Vergangene Kämpfe</div>
                                    </span>
                                  </span>
                </b>
            </td>
        </tr>
        <tr>
            <td class="boxSchatten" align="center"><b>ID</b></td>
            <td class="boxSchatten" align="center"><b>Name</b></td>
            <td class="boxSchatten" align="center"><b>Art</b></td>
            <td class="boxSchatten" align="center"><b>Modus</b></td>
            <td class="boxSchatten" align="center"><b>Aktion</b></td>
            <?php
            if($player->GetArank() >= 2)
            {
                ?>
                <td class="boxSchatten" align="center"><b>Log</b></td>
                <?php
            }
            ?>
        </tr>
        <?php
        $select = "id, name, type, mode, fighters, state";
        $pID = '[' . $displayedPlayer->GetID() . ']';
        $where = 'fights.fighters LIKE "%' . $pID . '%" AND state = 2';
        $canSeeTest = $player->GetARank() >= 2;
        if (!$canSeeTest)
            $where .= ' AND testfight=0';
        $order = 'id';
        $from = 'fights';
        $list = new Generallist($database, $from, $select, $where, $order, 100, 'DESC');


        //preSort the arrays, so that we can easily show them
        $id = 0;
        $entry = $list->GetEntry($id);
        while (($player->GetArank() < 3 && $entry != null && $displayedPlayer->GetARank() < 2) || ($player->GetArank() >= 2 && $entry != null))
        {
            ?>
            <tr>
                <td class="boxSchatten" align="center">
                    <?php
                    echo number_format($entry['id'],0 ,'','.');
                    ?>
                </td>
                <td class="boxSchatten" align="center">
                    <?php
                    echo $entry['name'];
                    ?>
                </td>
                <td class="boxSchatten" align="center">
                    <?php
                    echo Fight::GetTypeName($entry['type']);
                    ?>
                </td>
                <td class="boxSchatten" align="center">
                    <?php
                    echo $entry['mode'];
                    ?>
                </td>
                <td class="boxSchatten" align="center">
                    <a href="?p=infight&fight=<?php echo $entry['id']; ?>">Betrachten</a>
                </td>
                <?php
                if($player->GetArank() >= 2)
                {
                    ?>
                    <td class="boxSchatten" align="center">
                        <a href="?p=fightlog&fight=<?php echo $entry['id']; ?>">Log</a>
                    </td>
                    <?php
                }
                ?>
            </tr>
            <?php
            $id++;
            $entry = $list->GetEntry($id);
        }
        ?>
    </table>
</div>
<?php
if ($isLocalPlayer)
{
    ?>
    <div class="spacer"></div>
    <hr>
    <form name="form1" action="?p=profil&a=change" method="post" enctype="multipart/form-data">
        <table style="text-align: center;" width="100%" cellspacing="0" border="0">
            <tr>
                <td class="catGradient borderB borderT" colspan="6" align="center"><b>Einstellungen</b></td>
            </tr>
            <tr>
                <td width="50%"><b>Profilbild (200x200)</b></td>
                <td width="50%"><input type="file" name="file_upload" /><input type="hidden" name="image" /></td>
            </tr>
            <tr>
                <td width="20%"><b>Profiltext</b></td>
                <td width="80%">
                    <textarea class="textfield" name="text" maxlength="300000" style="width:400px; height:200px; resize: none;"><?php echo $player->GetText(); ?></textarea>
                </td>
            </tr>
            <tr>
                <td width="20%"><b>Chat</b></td>
                <td width="80%">
                    <select style="height:30px; width:310px;" name="chatactivate" id="chatactivate" class="select">
                        <option value="1" <?php if ($player->GetChatActive()) echo 'selected'; ?>>Aktivieren</option>
                        <option value="0" <?php if (!$player->GetChatActive()) echo 'selected'; ?>>Deaktivieren</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="20%"><b>Online-Status</b></td>
                <td width="80%">
                    <select style="height:33px; width:310px;" name="onlineactivate" id="onlineactivate" class="select">
                        <option value="1" <?php if ($player->GetOnlineStatus()) echo 'selected'; ?>>Zeigen</option>
                        <option value="0" <?php if (!$player->GetOnlineStatus()) echo 'selected'; ?>>Verbergen</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" value="ändern"> </td>
            </tr>
        </table>
    </form>
    <br />
    <?php
    if($displayedPlayer->GetArank() >= 0)
    {
        ?>
        <form method="post" action="?p=profil&a=bday">
            <b>Geburtstag</b>
            <br />
            <input class="select" type="date" name="bday" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" value="<?= $displayedPlayer->GetBday(); ?>" />
            <br /><br />
            <button class="buttons">Speichern</button>
        </form>
        <br />
        Soll dein Alter auf dem Profil angezeigt werden?
        <br />
        <form method="post" action="?p=profil&a=shadowage">
        <select name="answer" class="select">
            <option value="1" <?php echo ($displayedPlayer->GetShadowAge() == 1) ? 'selected' : ''; ?> >Ja</option>
            <option value="0" <?php echo ($displayedPlayer->GetShadowAge() == 0) ? 'selected' : ''; ?> >Nein</option>
        </select>
            <br />
            <button class="buttons">Speichern</button>
        </form>
            <?php
    }
    ?>
    <!-- Freunde -->
    <div class="spacer"></div>
    <hr>
    <table style="text-align: center;" width="100%" cellspacing="0" border="0">
        <tr>
            <td class="catGradient borderB borderT" colspan="7" align="center"><b>Freunde</b></td>
        </tr>
        <tr>
            <td class="boxSchatten" align="center"><b>Name</b></td>
            <td class="boxSchatten" align="center"><b>Douriki</b></td>
            <td class="boxSchatten" align="center"><b>Ozean</b></td>
            <td class="boxSchatten" align="center"><b>Ort</b></td>
            <td class="boxSchatten" align="center"><b>Tägl. NPC</b></td>
            <td class="boxSchatten" align="center"><b>Tägl. PvP</b></td>
            <td class="boxSchatten" align="center"><b>Status</b></td>
        </tr>
        <?php
        if ($displayedPlayer->GetFriends() != '')
        {
            $fl = explode(";", $displayedPlayer->GetFriends());

            foreach ($fl as &$friendid)
            {
                echo "<tr>";
                $friend = new Player($database, $friendid);
                $friendplanet = new Planet($database, $friend->GetPlanet());
                $friendplace = new Place($database, $friend->GetPlace(), $actionManager);
                $userstatus = '';

                if ($friend->IsOnline())
                {
                    $userstatus = "<span style='color: green'>Online</span>";
                }
                else
                {
                    $userstatus = "<span style='color: red'>Offline</span>";
                }
                echo '<td class="boxSchatten" align="center"><a href="?p=profil&id=' . $friend->GetID() . '">' . $friend->GetName() . '</a></td>';
                echo '<td class="boxSchatten" align="center">' . number_format($friend->GetKI(),'0', '', '.') . '</td>';
                echo '<td class="boxSchatten" align="center">' . $friendplanet->GetName() . '</td>';
                echo '<td class="boxSchatten" align="center">' . $friendplace->GetName() . '</td>';
                echo '<td class="boxSchatten" align="center">' . number_format($friend->GetDailyNPCFights(),'0', '', '.') . '</td>';
                echo '<td class="boxSchatten" align="center">' . number_format($friend->GetDailyFights(),'0', '', '.') . '</td>';
                echo '<td class="boxSchatten" align="center">' . $userstatus . '</td>';
                echo "</tr>";
            }
        }
        ?>
    </table>
    <div class="spacer2"></div>
    <hr>
    <table width="100%" cellspacing="0" border="0">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" align="center"> <b>Verwaltung Auszeichnung</b></td>
        </tr>
    </table>
    <br />
    <form name="form1" action="?p=profil&a=titel" method="post" enctype="multipart/form-data">
        <?php
        $count = 0;
        while ($count < 3)
        {
            if ($count == 0)
                $title = $displayedPlayer->GetRankOne();
            else if ($count == 1)
                $title = $displayedPlayer->GetRankTwo();
            else if ($count == 2)
                $title = $displayedPlayer->GetRankThree();
            if ($count > 0)
            {
                echo "<br/>";
            }
            ?>
            <b>Auszeichnung <?php echo $count + 1; ?></b>
            <div class="spacer"></div>
            <select style="height:30px; width:310px;" name="titel<?php echo $count; ?>" class="select">
                <option value="0" <?php if ($title == 0)
                { ?> selected <?php } ?>> Kein Titel</option>
                <?php
                $titels = $titelManager->GetTitels();
                $playerTitels = $displayedPlayer->GetTitels();
                foreach ($playerTitels as &$pTitel)
                {
                    $titel = $titelManager->GetTitel($pTitel);
                    if ($titel != null)
                    {
                        ?><option value="<?php echo $titel->GetID(); ?>" <?php if ($title == $titel->GetID())
                    { ?> selected <?php } ?>> <?php echo $titel->GetName(); ?></option><?php
                    }
                }
                ?>
            </select>
            <div class="spacer"></div>
            <?php
            $count += 1;
        }
        ?>
        <div class="spacer"></div>
        <input type="submit" value="Ändern">
    </form>
    <div class="spacer2"></div>
    <hr>
    <table width="100%" cellspacing="0" border="0">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" align="center"><b>Verwaltung Design</b></td>
        </tr>
    </table>
    <br />
    <b>Charakteraussehen</b>
    <div class="spacer"></div>
    <button onclick="OpenPopupPage('Charakteraussehen','profil/bild.php?id=<?php echo $player->GetID(); ?>')">Ändern</button>
<div class="spacer"></div>
    <b>Fraktion Ändern</b>
    <br />
    <b>Kosten: </b>25.000 Berry & 250 Gold
    <form method="post" action="?p=profil&a=changefraktion">
        <select class="select" name="race">
        <?php
        if($player->GetRace() == 'Pirat')
        {
            echo '<option value="Marine">Marine</option>';
        }
        else
        {
            echo '<option value="Pirat">Pirat</option>';
        }
        ?>
        </select>
        <br /><br />
        <input type="submit" value="Speichern"/>
    </form>
    <div class="spacer"></div>
    <b>Design</b>
    <form name="form1" action="?p=profil&a=style" method="post" enctype="multipart/form-data">
        <select style="height:30px; width:310px;" name="design" id="design" class="select">
            <option value="Crocodile - Braun" <?php if ($player->GetDesign() == 'Crocodile - Braun') echo "selected"; ?>>Crocodile - Sand</option>
            <option value="Crocodile - Gold" <?php if ($player->GetDesign() == 'Crocodile - Gold') echo "selected"; ?>>Crocodile - Sand 2</option>
            <option value="LuffyPink" <?php if ($player->GetDesign() == 'LuffyPink') echo "selected"; ?>>Ruffy - Pink</option>
            <option value="RuffyOrange" <?php if ($player->GetDesign() == 'RuffyOrange') echo "selected"; ?>>Ruffy - Orange</option>
            <option value="Enel" <?php if ($player->GetDesign() == 'Enel') echo "selected"; ?>>Enel - Blau</option>
            <option value="Nami" <?php if ($player->GetDesign() == 'Nami') echo "selected"; ?>>Nami - Lila</option>
            <option value="TrafalgarLaw" <?php if ($player->GetDesign() == 'TrafalgarLaw') echo "selected"; ?>>Trafalgar Law - Grün</option>
            <option value="Kuzan" <?php if ($player->GetDesign() == 'Kuzan') echo "selected"; ?>>Kuzan - Hellblau</option>
            <option value="Ace" <?php if ($player->GetDesign() == 'Ace') echo "selected"; ?>>Ace - Orange/Gelb</option>
            <option value="Chopper" <?php if ($player->GetDesign() == 'Chopper') echo "selected"; ?>>Chopper - Blau/Grau</option>
            <option value="Zorro" <?php if ($player->GetDesign() == 'Zorro') echo "selected"; ?>>Zorro - Grün</option>
            <option value="Blackbeard" <?php if ($player->GetDesign() == 'Blackbeard') echo "selected"; ?>>Blackbeard - Schwarz</option>
            <option value="LawBlue" <?php if ($player->GetDesign() == 'LawBlue') echo "selected"; ?>>Trafalgar Law - Blau</option>
            <option value="namiorange" <?php if ($player->GetDesign() == 'namiorange') echo "selected"; ?>>New Nami - Orange/Rot</option>
        </select>
        <input type="submit" value="Ändern">
    </form>
    <div class="spacer"></div>
    <b>Hintergrund</b>
    <form name="form1" action="?p=profil&a=background" method="post" enctype="multipart/form-data">
        <select style="height:30px; width:310px;" name="background" id="background" class="select">
            <option value="" <?php if ($player->GetBackground() == '') echo "selected"; ?>>Kein Hintergrundbild</option>
            <?php
            $path    = 'img/backgrounds';
            $files = scandir($path);
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as &$file)
            {
                $file = str_replace('.jpg', '', $file);
                ?>
                <option value="<?php echo $file; ?>" <?php if ($player->GetBackground() == $file) echo "selected"; ?>>
                    <?php
                    $file = str_replace('0', ' ', $file);
                    $file = str_replace('ue', 'ü', $file);
                    $file = str_replace('ae', 'ä', $file);
                    $file = str_replace('oe', 'ö', $file);
                    $file = str_replace('Ue', 'Ü', $file);
                    $file = str_replace('Ae', 'Ä', $file);
                    $file = str_replace('Oe', 'Ö', $file);
                    echo $file;
                    ?>
                </option>
                <?php
            }
            ?>
        </select>
        <input type="submit" value="Ändern">
    </form>
    <?php
    if($player->GetArank() >= 0)
    {
        ?>
        <div class="spacer"></div>
        <b>Profil Hintergrund</b>
        <form name="form1" action="?p=profil&a=profilebg" method="post" enctype="multipart/form-data">
            <select style="height:30px; width:310px;" name="profilebg" id="profilebg" class="select">
                <option value="" <?php if ($player->GetProfileBG() == '') echo "selected"; ?>>Kein Profilhintergrund</option>
                <?php
                $path    = 'img/profilebackgrounds';
                $files = scandir($path);
                $files = array_diff(scandir($path), array('.', '..'));
                foreach ($files as &$file)
                {
                    $file = str_replace('.png', '', $file);
                    ?>
                    <option value="<?php echo $file; ?>" <?php if ($player->GetProfileBG() == $file) echo "selected"; ?>>
                        <?php
                        $file = str_replace('0', ' ', $file);
                        $file = str_replace('ue', 'ü', $file);
                        $file = str_replace('ae', 'ä', $file);
                        $file = str_replace('oe', 'ö', $file);
                        $file = str_replace('Ue', 'Ü', $file);
                        $file = str_replace('Ae', 'Ä', $file);
                        $file = str_replace('Oe', 'Ö', $file);
                        echo $file;
                        ?>
                    </option>
                    <?php
                }
                ?>
            </select>
            <input type="submit" value="Ändern">
        </form>
        <?php
    }
    ?>
    <div class="spacer"></div>
    <b>Header</b>
    <form name="form3" action="?p=profil&a=header" method="post" enctype="multipart/form-data">
        <select style="height:30px; width:310px;" name="header" id="header" class="select">
            <option value="" <?php if ($player->GetHeader() == '') echo "selected"; ?>>Kein Header</option>
            <?php
            $path    = 'img/header';
            $files = scandir($path);
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as &$file)
            {
                $file = str_replace('.png', '', $file);
                ?>
                <option value="<?php echo $file; ?>" <?php if ($player->GetHeader() == $file) echo "selected"; ?>>
                    <?php
                    echo $file;
                    ?>
                </option>
                <?php
            }
            ?>
        </select>
        <input type="submit" value="Ändern">
    </form>
    <!--<div class="spacer"></div>
    <b>Kopfgeld Bild</b> (250x190)
    <form name="form2" action="?p=profil&a=kopfgeldimage" method="post" enctype="multipart/form-data">
        <input type="file" name="file_upload" /><input type="hidden" name="image" /><br />
        <input type="submit" value="Hochladen">
    </form>-->
    <?php
    if($player->IsDonator())
    {
        $ki = 0;
        if($player->GetFakeKI() != 0)
        {
            $ki = $player->GetFakeKI();
        }
        else
        {
            $ki = $player->GetKI();
        }
        ?>
        <div class="spacer2"></div>
        <b>Douriki unterdrücken</b>
        <form method="post" action="?p=profil&a=changedouriki">
            <input type="text" name="fakeki" value="<?= $ki; ?>"/><br /><br />
            <button>Speichern</button>
        </form>
        <?php
    }
    ?>
    <div class="spacer2"></div>
    <hr>
    <table width="100%" cellspacing="0" border="0">
        <tr>
            <td class="catGradient borderB borderT" colspan="6" align="center"> <b>Verwaltung Techniken</b></td>
        </tr>
    </table>
    <br />
    Du hast die Techniken im Kampf zur Verfügung, die farbig sind.<br />
    Klicke auf eine Technik um sie im Kampf hinzuzufügen oder zu entfernen.
    <div class="spacer"></div>
    <?php
    $attacks = explode(';', $player->GetAttacks());
    $fightAttacks = explode(';', $player->GetFightAttacks());
    $powerups = array();
    $id = 0;
    while (isset($attacks[$id]))
    {
        $attack = $attackManager->GetAttack($attacks[$id]);
        if ($attack != null)
        {
            $isSelected = in_array($attack->GetID(), $fightAttacks);
            if ($isSelected && $attack->GetType() == 4)
            {
                $powerups[] = $attack;
            }
            $paths = array(
                52, // Zoan->Fisch-Frucht
                762, // Zoan->Vogel-Frucht
                763, // Zoan->Mensch-Mensch-Frucht
                50, // Paramecia->Operations-Frucht
                760, // Paramecia->Faden-Frucht
                761, // Paramecia->Mochi-Frucht
                51, // Logia->Donner-Frucht
                764, // Logia->Feuer-Frucht
                765, // Logia->Gefrier-Frucht
                53, // Schwertkämpfer
                54, // Schwarzfuß
                55 // Karatekämpfer
            );
            if (!in_array($attack->GetID(), $paths))
            {
                ?>
                <div class="tooltip" style="position:relative; height:60px; width:60px; display:inline-block">
                    <a href="?p=profil&a=fightattack&aid=<?php echo $attack->GetID(); ?>">
                        <img class="attack" width="40px" height="40px" src="<?php echo $attack->GetImage(); ?>" style="
                        <?php
                        if (!$isSelected)
                        {
                            ?>
                                filter: gray; /* IE6-9 */
                                -webkit-filter: grayscale(1); /* Google Chrome, Safari 6+ & Opera 15+ */
                                filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */
                            <?php
                        }
                        ?>
                                ">
                    </a>
                    <span class="tooltiptext" style="position:absolute; z-index:5; width:220px; bottom:64px; left:-80px;">
                        <?php echo $attack->GetName(); ?>
                        <hr/>
                        <span>Typ: <?php echo Attack::GetTypeName($attack->GetType()); ?></span>
                        <div class="spacer3"></div>
                        <span>Genauigkeit: <?php echo $attack->GetAccuracy(); ?>% </span>
                        <?php
                        if($attack->GetEnergy() != 0 || $attack->GetLP() != 0 || $attack->GetKP() != 0)
                        {
                            ?>
                            <div class="spacer3"></div>
                            <span>Kosten:<br/>
                                    <?php
                                    if ($attack->GetEnergy() != 0)
                                        echo number_format($attack->GetEnergy(), '0', '', '.') . ' EP<br/>';
                                    if ($attack->GetLP() != 0)
                                    {
                                        if ($attack->GetCostProcentual() == 1) echo ($attack->GetLP() / 100) . '%';
                                        else echo number_format($attack->GetLP(), '0', '', '.');
                                        echo ' LP<br/>';
                                    }
                                    if ($attack->GetKP() != 0)
                                    {
                                        //if ($attack->IsSingleCost() == 1) echo "Einmalig ";
                                        if ($attack->IsKPProcentual() == 1) echo ($attack->GetKP()) . '%';
                                        else echo number_format($attack->GetKP(), '0', '', '.');
                                        echo ' AD<br/>';
                                    }
                                    ?>
                                </span>
                            <?php
                        }
                        ?>
                        <div class="spacer"></div>
                        <?php
                        if ($attack->GetType() == 3)
                        {
                            echo 'Ziel benötigt <br/>' .$attack->GetValue() . '% LP<br/>';
                        }
                        if ($attack->GetType() == 1 || $attack->GetType() == 12 || $attack->GetType() == 26 || $attack->GetType() == 27)
                        {
                            if ($attack->GetLPValue() != 0)
                            {
                                $valName = 'LP';
                                echo number_format(round($attack->GetValue() * $attack->GetLPValue() / 100), '0', '', '.');
                                if ($attack->IsProcentual())
                                    echo '%';
                                echo ' Douriki Schaden an ' . $valName . '<br/>';
                            }
                            if ($attack->GetKPValue() != 0)
                            {
                                $valName = 'AD';
                                echo number_format(round($attack->GetValue() * $attack->GetKPValue() / 100), '0', '', '.');
                                if ($attack->IsProcentual())
                                    echo '%';
                                echo ' Douriki Schaden an ' . $valName . '<br/>';
                            }
                            if ($attack->GetEPValue() != 0)
                            {
                                $valName = 'EP';
                                echo number_format(round($attack->GetValue() * $attack->GetEPValue() / 100), '0', '', '.');
                                if ($attack->IsProcentual())
                                    echo '%';
                                echo ' Douriki Schaden an ' . $valName . '<br/>';
                            }
                            if($attack->GetType() == 26 || $attack->GetType() == 27)
                            {
                                $amount = number_format($attack->GetEnemyAmount(), 0, '', '.');
                                if($attack->GetEnemyAmount() == 0)
                                    $amount = 'alle';
                                echo 'auf ' . $amount . ' Gegner';
                            }
                        }
                        else if ($attack->GetType() == 5 || $attack->GetType() == 11)
                        {
                            if ($attack->GetLPValue() != 0)
                            {
                                $valName = 'LP';
                                echo number_format(round($attack->GetValue() * $attack->GetLPValue() / 100), '0', '', '.');
                                if ($attack->IsProcentual())
                                    echo '%';
                                echo ' Douriki Heilung an ' . $valName . '<br/>';
                            }
                            if ($attack->GetKPValue() != 0)
                            {
                                $valName = 'AD';
                                echo number_format(round($attack->GetValue() * $attack->GetKPValue() / 100), '0', '', '.');
                                if ($attack->IsProcentual())
                                    echo '%';
                                echo ' Douriki Heilung an ' . $valName . '<br/>';
                            }
                            if ($attack->GetEPValue() != 0)
                            {
                                $valName = 'EP';
                                echo number_format(round($attack->GetValue() * $attack->GetEPValue() / 100), '0', '', '.');
                                if ($attack->IsProcentual())
                                    echo '%';
                                echo ' Douriki Heilung an ' . $valName . '<br/>';
                            }
                        }
                        else if ($attack->GetType() == 9 || $attack->GetType() == 19)
                        {
                            $rounds = 'Runde';
                            if($attack->GetValue() > 1)
                                $rounds = 'Runden';
                            echo number_format($attack->GetValue(), 0, '', '.') . ' ' . $rounds;
                        }
                        else if ($attack->GetType() == 6)
                        {
                            echo $attack->GetValue() . ' % Douriki pro Ladung';
                        }
                        else if ($attack->GetType() == 8)
                        {
                            if($attack->GetNPCID() != 0)
                            {
                                $npc = new NPC($database, $attack->GetNPCID());
                                echo 'Beschwört den NPC: ' . $npc->GetName() . '<br/>';
                                echo 'LP: ' . number_format($npc->GetMaxLP(), 0, '', '.') . '<br/>';
                                echo 'AD: ' . number_format($npc->GetMaxKP(), 0, '', '.') . '<br/>';
                                echo 'ATK: ' . number_format($npc->GetAttack(), 0, '', '.') . '<br/>';
                                echo 'DEF: ' . number_format($npc->GetDefense(), 0, '', '.') . '<br/>';
                            }
                        }
                        else
                        {
                            $valName = 'LP';
                            if ($attack->GetLPValue() != 0)
                            {
                                if ($attack->GetType() == 20 || $attack->GetType() == 2)
                                    echo $valName . ' x ';

                                echo$attack->GetValue() * $attack->GetLPValue() / 100;
                                if ($attack->IsProcentual())
                                    echo '%';

                                if (/*$attack->GetType() == 18 || */$attack->GetType() == 22)
                                    echo ' Douriki auf';

                                if ($attack->GetType() != 20 && $attack->GetType() != 2)
                                    echo ' ' . $valName;

                                echo '<br/>';
                            }
                            $valName = 'AD';
                            if ($attack->GetKPValue() != 0)
                            {
                                if ($attack->GetType() == 20 || $attack->GetType() == 2)
                                    echo $valName . ' x ';

                                echo number_format($attack->GetValue() * $attack->GetKPValue() / 100, '0', '', '.');
                                if ($attack->IsProcentual())
                                    echo '%';

                                if (/*$attack->GetType() == 18 || */$attack->GetType() == 22)
                                    echo ' Douriki auf';

                                if ($attack->GetType() != 20 && $attack->GetType() != 2)
                                    echo ' ' . $valName;

                                echo '<br/>';
                            }
                            $valName = 'Energie';
                            if ($attack->GetEPValue() != 0)
                            {
                                if ($attack->GetType() == 20 || $attack->GetType() == 2)
                                    echo $valName . ' x ';

                                echo number_format($attack->GetValue() * $attack->GetEPValue() / 100, '0', '', '.');
                                if ($attack->IsProcentual())
                                    echo '%';

                                if (/*$attack->GetType() == 18 || $attack->GetType() == 21 || */$attack->GetType() == 22)
                                    echo ' Douriki auf';

                                if ($attack->GetType() != 20 && $attack->GetType() != 2)
                                    echo ' ' . $valName;

                                echo '<br/>';
                            }
                            $valName = 'Angriff';
                            if ($attack->GetAtkValue() != 0)
                            {
                                if ($attack->GetType() == 20 || $attack->GetType() == 2)
                                    echo $valName . ' x ';

                                echo number_format($attack->GetValue() * $attack->GetAtkValue() / 100, '0', '', '.');
                                if ($attack->IsProcentual())
                                    echo '%';

                                if (/*$attack->GetType() == 18 || $attack->GetType() == 21 || */$attack->GetType() == 22)
                                    echo ' Douriki auf';

                                if ($attack->GetType() != 20 && $attack->GetType() != 2)
                                    echo ' ' . $valName;

                                echo '<br/>';
                            }
                            $valName = 'Verteidigung';
                            if ($attack->GetDefValue() != 0)
                            {
                                if ($attack->GetType() == 20 || $attack->GetType() == 2)
                                    echo $valName . ' x ';

                                echo$attack->GetValue() * $attack->GetDefValue() / 100;
                                if ($attack->IsProcentual())
                                    echo '%';

                                if (/*$attack->GetType() == 18 || $attack->GetType() == 21 || */$attack->GetType() == 22)
                                    echo ' Douriki auf';

                                if ($attack->GetType() != 20 && $attack->GetType() != 2)
                                    echo ' ' . $valName;

                                echo '<br/>';
                            }
                            $valName = 'Anziehung';
                            if ($attack->GetTauntValue() != 0)
                            {
                                if ($attack->GetType() == 20 || $attack->GetType() == 2)
                                    echo $valName . ' x ';

                                echo $attack->GetValue() * $attack->GetTauntValue() / 100;
                                if ($attack->IsProcentual())
                                    echo '%';

                                if (/*$attack->GetType() == 18 || $attack->GetType() == 21 || */$attack->GetType() == 22)
                                    echo ' Douriki auf';

                                if ($attack->GetType() != 20 && $attack->GetType() != 2)
                                    echo ' ' . $valName;

                                echo '<br/>';
                            }
                            $valName = 'Reflektion';
                            if ($attack->GetReflectValue() != 0)
                            {
                                if ($attack->GetType() == 2)
                                    echo $valName . ' x ';

                                echo $attack->GetValue() * $attack->GetReflectValue() / 100;
                                if ($attack->IsProcentual())
                                    echo '%';

                                if (/*$attack->GetType() == 18 || $attack->GetType() == 21 || */$attack->GetType() == 22)
                                    echo ' Douriki auf';

                                if ($attack->GetType() != 2)
                                    echo ' ' . $valName;

                                echo '<br/>';
                            }
                            if ($attack->GetAccBuff() != 0)
                            {
                                if ($attack->GetType() == 2)
                                    echo $valName . ' x ';

                                echo $attack->GetValue() * $attack->GetAccBuff() / 100;
                                if ($attack->IsProcentual())
                                    echo '%';

                                if ($attack->GetType() != 2)
                                    echo ' ' . $valName;

                                echo '<br/>';
                            }
                            if ($attack->GetReflexBuff() != 0)
                            {
                                if ($attack->GetType() == 2)
                                    echo $valName . ' x ';

                                echo $attack->GetValue() * $attack->GetReflexBuff() / 100;
                                if ($attack->IsProcentual())
                                    echo '%';

                                if ($attack->GetType() != 2)
                                    echo ' ' . $valName;

                                echo '<br/>';
                            }

                            if($attack->GetType() == 21 || $attack->GetType() == 18 && $attack->GetRounds() > 0)
                            {
                                $rounds = 'Runde';
                                if($attack->GetRounds() + 1 > 1)
                                    $rounds = 'Runden';
                                echo number_format($attack->GetRounds() + 1, 0, '', '.') . ' ' . $rounds;
                            }
                        }
                        ?>
                    </span>
                </div>
                <?php
            }
        }
        ++$id;
    }
    ?>
    <div class="spacer2"></div>
    <hr>
    <b>Start-Verwandlung</b>
    <div class="spacer"></div>
    Mit welcher Verwandlung möchtest du starten?
    <form name="form1" action="?p=profil&a=powerup" method="post" enctype="multipart/form-data">
        <select style="height:30px; width:310px;" name="powerup" id="powerup" class="select">
            <option value="0">Keine Verwandlung</option>
            <?php
            foreach ($powerups as &$powerup)
            {
                ?>
                <option value="<?php echo $powerup->GetID(); ?>" <?php if ($player->GetStartingPowerup() == $powerup->GetID()) echo "selected"; ?>>
                    <?php echo $powerup->GetName(); ?>
                </option>
                <?php
            }
            ?>
        </select>
        <br />
        <input type="submit" value="ändern">
    </form>
    <?php
    if($player->GetUserID() == $account->Get('id'))
    {
        ?>
        <div class="spacer2"></div>
        <hr>
        <b>Charakter löschen</b>
        <div class="spacer"></div>
        <form name="form1" action="?p=profil&a=delete" method="post" enctype="multipart/form-data">
            <input type="text" name="grund" style="width: 90%;" placeholder="Magst du uns einen Grund nennen, der dich dazu bringt aufzuhören?"><br/>
            <br/>
            <label for="realcheck" style="cursor: pointer;">Möchtest du diesen Charakter wirklich löschen?</label><input type="checkbox" id="realcheck" name="realcheck"><br />
            <input type="submit" value="Löschen">
        </form>
        <?php
    }
    ?>
    <div class="spacer2"></div>
    <?php
    if($player->GetUserID() == $account->Get('id'))
    {
        if($player->GetBookingDays() == 0)
        {
            ?>
            <hr>
            <b>Pause einlegen! (BETA)</b>
            <br />
            Wie viele Tage soll die Pause andauern?
            <form method="post" action="?p=profil&a=booking">
                <select class="select" name="bdays">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                </select>
                <b>Tage</b> <br /><br />
                <button value="Speichern">Speichern</button>
            </form>
            <?php
        }
        else if($player->GetBookingDays() > 0)
        {
            ?>
            <hr>
            <b>Du pausierst aktuell noch <?= $player->GetBookingDays(); ?> Tage</b><br />
            Willst du abbrechen? Bitte bedenke das dir dadurch die bereits vergangenen Tage verloren gehen!<br />
            <a href="?p=profil&a=bookingbroke">Abbrechen</a>
            <?php
        }
    }
}
?>
<hr>
<b>Melden</b>
<div class="spacer"></div>
<button onclick="OpenPopupPage('Melden','profil/melden.php?id=<?php if(isset($_GET["id"])) { echo $_GET["id"]; } else { echo $player->GetID(); } ?>')">Melden</button>
<div class="spacer2"></div>