
<style scoped>
    summary {
        cursor: pointer;
    }

    details summary, summary::marker > * {
        display: inline;
    }
</style>
<div class="spacer"></div>
<?php
if ($player->IsLogged())
{
?>
<div id="SideMenuBar" class="SideMenuBar" style="display:flex; flex-direction: column; align-items: center; text-align: center; margin-left:0;float:left;min-height:0;">
    <div class="SideMenuContainer borderT borderL borderB borderR">
        <?php
        if($player->IsAdminLogged())
        {
            ?>
            <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=profil&a=adminlogout')"><span style="color: red;">[Reloggen]</span></div>
            <?php
        }
        ?>
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">
                <?php
                echo $player->GetName();
                if ($player->GetArank() >= 2)
                {
                    ?>
                    <span style="color: red; cursor: pointer;" onmousedown="location.replace('?p=profil&a=reset')">[Heal]</span>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="SideMenuInfo" style="display: flex; flex-direction: column; align-items: center;">
            <div class="char_main" style="max-height: 5px;">
                <div class="char_image smallBG borderT borderB borderR borderL">
                    <img src="<?php echo $player->GetImage(); ?>" alt="<?php echo $player->GetName(); ?>" title="<?php echo $player->GetName(); ?>" width="99%" height="99%">
                </div>
            </div>
            <div class="spacer"></div>
            <div class="SideMenuKat catGradient borderB borderT">
                <img alt="Infos" title="Infos" width="160" src="img/marketing/onepieceinfosbild.png" />
            </div>
            <div class="spacer"></div>
            <div class="lpback" style="height:20px; width:90%;">
                <div class="lpbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                <div class="lpanzeige" style="width:<?php echo $player->GetLPPercentage(); ?>%"></div>
                <div class="lptext">
                    LP:
                    <?php echo number_format($player->GetLP(), '0', '', '.'); ?> /
                    <?php echo number_format($player->GetMaxLP(), '0', '', '.'); ?>
                </div>
            </div>
            <div class="spacer"></div>
            <div class="kpback" style="height:20px; width:90%;">
                <div class="kpbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                <div class="kpanzeige" style="width:<?php echo $player->GetKPPercentage(); ?>%"></div>
                <div class="kptext">
                    AD:
                    <?php echo number_format($player->GetKP(), '0', '', '.'); ?> /
                    <?php echo number_format($player->GetMaxKP(), '0', '', '.'); ?>
                </div>
            </div>
            <div class="spacer"></div>
            <div class="info smallBG borderT borderB borderR borderL boxSchatten" style="cursor: pointer;" onmousedown="location.replace('?p=user&page=<?php echo ceil($player->GetRank() / 30); ?>')">
                Rang:
                <?php echo number_format($player->GetRank(), '0', '', '.'); ?>
            </div>
            <div class="spacer"></div>
            <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                Douriki:
                <?php echo number_format($player->GetKI(), '0', '', '.'); ?>
            </div>
            <div class="spacer"></div>
            <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                <?php echo ($player->GetRace() == "Pirat" ? "Kopfgeld: " : "Prestige: "); ?>
                <?php echo number_format($player->GetPvP(), '0', '', '.'); ?>
            </div>
            <div class="spacer"></div>
            <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                Fraktion:
                <?php echo $player->GetRace(); ?>
            </div>
            <div class="spacer"></div>
            <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                <?php echo number_format($player->GetBerry(), 0, ',', '.'); ?>
                <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 15px; width: 10px;"/>
            </div>
            <div class="spacer"></div>
            <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                <?php echo number_format($player->GetGold(), '0', ',', '.'); ?>
                <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 15px; width: 15px;"/>
            </div>
            <?php
                if($halloweenEventActive)
                {
                    ?>
                        <div class="spacer"></div>
                        <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                            Kürbismünzen: <?php echo number_format($player->GetMünzen(), '0', ',', '.'); ?>
                        </div>
                    <?php
                }
            ?>
            <div class="spacer"></div>
            <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                Meer:
                <?php echo $planet->GetName(); ?>
            </div>
            <div class="spacer"></div>
            <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                Ort:
                <?php echo $place->GetName(); ?>
            </div>
            <div class="spacer"></div>
            <?php if ($player->GetAction() != 0 || $player->GetTravelAction() != 0)
            {
                if ($player->GetAction() != 0)
                {
                    $action = $actionManager->GetAction($player->GetAction());
                    ?>
                    <div class="info smallBG borderT borderB borderR borderL boxSchatten" style="overflow: inherit;">
                                <span>
                                    <?php
                                    $image = 'actions/'.$action->GetImage();
                                    if ($action->GetType() == 5)
                                    {
                                        $attackName = '';

                                        $result = $database->Select('id, name, image', 'attacks', 'id = ' . $player->GetLearningAttack(), 1);
                                        $attackID = 0;
                                        if ($result)
                                        {
                                            $row = $result->fetch_assoc();
                                            $attackName = $row['name'];
                                            $image = 'attacks/'.$row['image'];
                                            $result->close();
                                        }
                                        echo $attackName . ' ';
                                    }

                                    echo $action->GetName();
                                    ?>
                                    </span>
                        <div class="tooltip" style="position: relative;">
                            <img src="img/<?= $image; ?>.png" alt="<?= $action->GetName(); ?>" title="<?= $action->GetName(); ?>" style="width: 100px; height: 100px; border-radius:25%; overflow:hidden;">
                            <span class="tooltiptext" style="width:180px; top:-50px; left:-40px;"><?php echo $action->GetDescription() . ' ' . $attackName; ?></span>
                        </div>
                        <br />
                        <div id="cID">Init
                            <script>
                                countdown(<?php echo $player->GetActionCountdown(); ?>, 'cID');
                            </script>
                        </div>
                        <?php
                        if ($action->GetID() == 2 || $action->GetID() == 13)
                        {
                            ?>
                            <button onclick="OpenPopupPage('Reise abbrechen','action/cancel.php', 't=travel')">
                                Abbrechen
                            </button>
                            <?php
                        }
                        else
                        {
                            ?>
                            <button onclick="OpenPopupPage('Aktion abbrechen','action/cancel.php')">
                                Abbrechen
                            </button>
                            <?php
                        }
                        if ($action->GetID() == 1)
                        {
                            ?>
                            <div class="spacer"></div>
                            <button onclick="OpenPopupPage('Aktion Aktualisieren','action/refresh.php')">
                                Aktualisieren
                            </button>
                            <?php
                        }
                        else if($action->GetID() == 9 && $player->IsDonator())
                        {
                            ?>
                            <div class="spacer"></div>
                            <button onclick="OpenPopupPage('Aktion Aktualisieren','action/refresh.php')">
                                Aktualisieren
                            </button>
                            <?php
                        }

                        if ($player->GetARank() >= 2 && $action->GetID() != 0)
                        {
                            ?>
                            <div class="spacer"></div>
                            <form method="post" action="<?php if (isset($_GET['p'])) echo '?p=' . $_GET['p'] . '&';
                            else echo '?';  ?>a=endAction">
                                <input type="submit" value="beenden">
                            </form>
                            <?php
                        }
                        ?>
                        <div class="spacer"></div>
                    </div>
                    <div class="spacer"></div>
                    <?php
                }
                if ($player->GetTravelAction() != 0)
                {
                    $ortTravelID = 2;
                    $planetTravelID = 13;
                    $action = $actionManager->GetAction($player->GetTravelAction());
                    ?>
                    <div class="info smallBG borderT borderB borderR borderL boxSchatten" style="overflow: inherit;">
                        <?php echo $action->GetName();
                        ?>
                        <div class="spacer"></div>
                        <div class="tooltip" style="position: relative;">
                            <img src="img/actions/<?php echo $action->GetImage(); ?>.png" alt="<?php echo $action->GetName(); ?>" title="<?php echo $action->GetName(); ?>" style="width: 100px; height: 100px; border-radius:25%; overflow:hidden;">
                            <span class="tooltiptext" style="width:180px; top:-50px; left:-40px;"><?php echo $action->GetDescription(); ?></span>
                        </div>
                        <?php
                        if ($action->GetID() == $ortTravelID) {
                            $place = new Place($database, $player->GetTravelPlace(), null);
                            echo '<br/>Ort: ' . $place->GetName();
                        }
                        else if ($action->GetID() == $planetTravelID) {
                            $planet = new Planet($database, $player->GetTravelPlanet());
                            echo '<br/>Meer: ' . $planet->GetName();
                        }
                        ?>
                        <br />
                        <div id="cID2">Init
                            <script>
                                countdown(<?php echo $player->GetTravelActionCountdown(); ?>, 'cID2');
                            </script>
                        </div>
                        <?php
                        if($action->GetID() == 2 || $action->GetID() == 13 || $action->GetID() == 25 || $action->GetID() == 26)
                        {
                            ?>
                            <!--<button onclick="OpenPopupPage('Reise abbrechen','action/cancel.php', 't=travel')">
                                Abbrechen
                            </button>-->
                            <?php
                        }
                        else
                        {
                            ?>
                            <button onclick="OpenPopupPage('Aktion abbrechen','action/cancel.php')">
                                Abbrechen
                            </button>
                            <?php
                            if ($action->GetID() == 1)
                            {
                                ?>
                                <div class="spacer"></div>
                                <button onclick="OpenPopupPage('Aktion Aktualisieren','action/refresh.php')">
                                    Aktualisieren
                                </button>
                                <?php
                            }
                        }

                        if ($player->GetARank() >= 2 && $action->GetType() == 4)
                        {
                            ?>
                            <div class="spacer"></div>
                            <form method="post" action="<?php if (isset($_GET['p'])) echo '?p=' . $_GET['p'] . '&';
                            else echo '?';  ?>a=endAction&type=travel">
                                <input type="submit" value="beenden">
                            </form>
                            <?php
                        }
                        if ($action->GetType() == 4 && $ortTravelID == $action->GetID())
                        {
                            ?>
                            <div class="spacer"></div>
                            <button onclick="OpenPopupPage('Reise Beschleunigen','travel/speedup.php')">
                                Beschleunigen
                            </button>
                            <?php
                        }
                        if ($action->GetType() == 4 && $planetTravelID == $action->GetID())
                        {
                            ?>
                            <div class="spacer"></div>
                            <button onclick="OpenPopupPage('Reise Beschleunigen','shiptravel/speedup.php')">
                                Beschleunigen
                            </button>
                            <?php
                        }
                        ?>
                        <div class="spacer"></div>
                    </div>
                    <div class="spacer"></div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <?php
    if ($player->GetGroup() != null)
    {
        $group = $player->GetGroup();
        ?>
        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR borderB borderT">
            <center>
                <div class="SideMenuKat catGradient borderB">
                    <div class="schatten">Gruppe</div>
                </div>
                <?php
                $i = 0;
                $limit = count($group);
                $where = '';
                while (isset($group[$i]))
                {
                    if ($where == '')
                    {
                        $where = 'id = ' . $group[$i] . '';
                    }
                    else
                    {
                        $where = $where . ' OR id = ' . $group[$i] . '';
                    }
                    ++$i;
                }
                $list = new Generallist($database, 'accounts', '*', $where, '', $limit, 'ASC');
                $id = 0;
                $entry = $list->GetEntry($id);
                $avatar = 'img/imagefail.png';
                while ($entry != null)
                {
                    if ($entry['charimage'] != '') $avatar = $entry['charimage'];
                    ?>
                    <div class="spacer"></div>
                    <div class="gbox borderT borderL borderR borderB boxSchatten">
                        <div class="catGradient borderB"><?php if ($entry['groupleader'] == 1)
                            { ?> <img alt="Gruppenleiter" title="Gruppenleiter" src="img/stern.png" width="15px" height="15px"><?php } ?><a class="no-link" href="?p=profil&id=<?php echo $entry['id']; ?>" class="catGradient"><?php echo $entry['name']; ?></a></div>
                        <div class="gbild borderB borderT borderR borderL">
                            <img src="<?php echo $avatar; ?>" alt="<?php echo $entry['name']; ?>" title="<?php echo $entry['name']; ?>" width="100%" height="100%">
                        </div>
                        <div class="lpback" style="top:5px; left:23px; height:15px; width:70px;">
                            <div class="lpbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                            <div class="lpanzeige" style="width:<?php echo round(($entry['lp'] / $entry['mlp']) * 100); ?>%"></div>
                        </div>
                        <div class="kpback" style="top:10px; left:23px; height:15px; width:70px;">
                            <div class="kpbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                            <div class="kpanzeige" style="width:<?php echo round(($entry['kp'] / $entry['mkp']) * 100); ?>%"></div>
                        </div>
                    </div>
                    <?php
                    if ($player->IsGroupLeader())
                    {
                        ?>
                        <a class="no-link" href="?p=profil&id=<?php echo $entry['id']; ?>&a=grouppromote">
                            <div style="cursor:pointer; width:90%;" class="boxSchatten SideMenuButton borderB borderL borderR">Leiter</div>
                        </a>
                        <a class="no-link" href="?p=profil&id=<?php echo $entry['id']; ?>&a=groupkick">
                            <div style="cursor:pointer; width:90%;" class="boxSchatten SideMenuButton borderB borderL borderR">Kick</div>
                        </a>
                        <?php
                    }
                    $id++;
                    $entry = $list->GetEntry($id);
                }
                ?>
                <div class="spacer"></div>
                <a class="no-link" href="?<?php if (isset($_GET['p'])) echo 'p=' . $_GET['p'] . '&'; ?>a=groupleave">
                    <div style="cursor:pointer;" class="SideMenuButton borderT">Verlassen</div>
                </a>
            </center>
        </div>
        <?php
    }
    }
    ?>
    <?php
        if(!$player->IsLogged())
        {
            ?>
            <?php
        }
    ?>
    <div class="SideMenuContainer borderT borderL borderB borderR">
        <div class="SideMenuKat catGradient schatten">
            Playerinfo
        </div>
        <details id="top5" ontoggle="toggleTopFive()">
            <summary style="text-align: center;">
                <b>Aufklappen</b>
            </summary>
            <div class="SideMenuInfo">
                <div class="spacer"></div>
                <?php
                    $listplayers = new GeneralList($database, 'statslist', 'acc, dailywin', 'type="-1"', 'dailywin', 10, 'DESC');
                    $titelManager = new TitelManager($database);
                    $id = 0;
                    $anzahl = 0;
                    $listplayerentry = $listplayers->GetEntry($id);
                    while($listplayerentry != null && $anzahl < 5)
                    {
                        if($listplayerentry['acc'] == $player->GetID())
                            $listplayer = $player;
                        else
                            $listplayer = new Player($database, $listplayerentry['acc'], null, false, 0, false);

                        if($listplayer->IsBanned())
                        {
                            $id++;
                            $listplayerentry = $listplayers->GetEntry($id);
                            continue;
                        }
                        $titel = $titelManager->GetTitel($listplayer->GetTitel());
                        $bande = new Clan($database, $listplayer->GetClan());
                        $anzahl++;
                        if($bande == null)
                        {
                            $logo = "<img src='img/marketing/ruffyflaggeohnebande.png' />";
                        }
                        ?>
                        <div style="text-align: center;">
                            <?php if($titel != null) : ?>
                                <p style='font-size: 12px; color:#<?= $titel->GetColor() ?>;'><?= $titel->GetName(); ?></p>
                            <?php
                            endif;
                            if($bande->GetBanner() != ''):
                                ?>
                                <a href="?p=clan&id=<?= $bande->GetID(); ?>"><img title='<?= $bande->GetName(); ?>' src='<?= $bande->GetBanner(); ?>' /></a><br />
                            <?php else:
                                if ($bande->GetName() != '') echo $bande->GetName().'<br />'; ?>
                            <?php endif; ?>
                            <a href="?p=profil&id=<?= $listplayer->GetID(); ?>" style="font-size: 16px"><?= $listplayer->GetName() ?></a><br />
                            <img height='100' width='100' src='<?= $listplayer->GetImage() ?>' /><br />
                            <b>Heutige Kämpfe: </b><br /><?php echo $listplayerentry['dailywin']; ?>
                        </div>
                        <hr>
                        <?php
                        $id++;
                        $listplayerentry = $listplayers->GetEntry($id);
                    }
                ?>
                <div class="spacer"></div>
            </div>
        </details>
    </div>
    <div class="spacer"></div>
    <div class="SideMenuContainer borderL borderR">
        <div class="SideMenuKat catGradient borderB borderT">
            <div class="schatten">
                Serverinfo
            </div>
        </div>
        <div class="SideMenuInfo borderB borderR" style="text-align: center;">
            Version: Beta 2.1<br>
            Spieler Online: <?php echo number_format($gameData->GetOnline(), '0', '', '.'); ?><br>
            Spieler: <?php echo number_format($gameData->GetTotal(), '0', '', '.'); ?><br>
            <?php if ($player != null && $player->IsLogged() && $player->GetARank() > 0)
            {
                ?>
                Unique Online: <?php echo number_format($gameData->GetUniqueOnline(), '0', '', '.'); ?><br>
                Unique: <?php echo number_format($gameData->GetUniqueTotal(), '0', '', '.'); ?><br>
                <?php
            }
            ?>
            <?php $clanCount = $gameData->GetClans();
            if ($clanCount == 1) echo 'Bande: ' . $clanCount;
            else echo 'Banden: ' . number_format($clanCount, '0', '', '.'); ?><br>
        </div>
    </div>
    <div class="spacer"></div>
    <div id="SideMenuBar" class="SideMenuBar" style="text-align: center; margin-left:0;float:left;min-height:0;">
        <?php
        if ($player->IsLogged())
        {
            if ($player->GetArank() >= 1 || $player->GetTeamUser() >= 2)
            {
                ?>
                <div class="SideMenuContainer borderL borderR">
                    <div class="SideMenuKat catGradient borderB borderT">
                        <img alt="Admin Menü" title="Admin Menü" width="160" src="img/marketing/onepieceadminbild.png" />
                    </div>
                    <?php
                    if ($player->GetArank() >= 3)
                    {
                        ?>
                        <a href="?p=detailcheck" class="no-link">
                            <div class="SideMenuStyle borderB">
                                Detailsuche
                            </div>
                        </a>
                        <?php
                    }
                    ?>
                    <?php
                    if ($player->GetArank() >= 2 || $player->GetTeamUser() >= 2)
                    {
                        ?>
                        <?php

                        $cheks = $database->Select('*', 'ticket', 'gelesen=1 and active=0');
                        $check = $cheks->num_rows;
                        $ticketcheck = $database->Select('*', 'ticket', 'active=0');
                        $nums = $ticketcheck->num_rows;
                        if($nums > 0)
                        {
                            ?>
                            <a href="?p=admintickets" class="no-link">
                                <div class="SideMenuStyle borderB"><font color="red">Ticket System ( <?= $check; ?> )</font></div>
                            </a>
                            <?php
                        }
                        else
                        {
                            ?>
                            <a href="?p=admintickets" class="no-link">
                                <div class="SideMenuStyle borderB">Ticket System</div>
                            </a>
                            <?php
                        }
                        ?>
                        <a href="?p=adminwarn" class="no-link">
                            <div class="SideMenuStyle borderB">Verwarnung</div>
                        </a>
                            <?php
                        if($player->GetArank() >= 2)
                        {
                            ?>
                            <a href="?p=writer" class="no-link">
                                <div class="SideMenuStyle borderB">News Posts</div>
                            </a>
                            <a href="?p=admin" class="no-link">
                                <div class="SideMenuStyle borderB">Admin Menu</div>
                            </a>
                            <a href="?p=givestats" class="no-link">
                                <div class="SideMenuStyle borderB">Stats Vergabe</div>
                            </a>
                            <a href="?p=adminlog" class="no-link">
                                <div class="SideMenuStyle borderB">Admin Log</div>
                            </a>
                            <a href="?p=adminimages" class="no-link">
                                <div class="SideMenuStyle borderB">Bilder Verwaltung</div>
                            </a>
                            <a href="?p=admininteractions" class="no-link">
                                <div class="SideMenuStyle borderB">Interaktionen</div>
                            </a>
                            <a href="?p=statistics" class="no-link">
                                <div class="SideMenuStyle borderB">Statistiken</div>
                            </a>
                            <a href="?p=report" class="no-link">
                                <?php
                                $cred = false;
                                $result = $database->Select('*', 'meldungen', 'status=0');
                                if ($result) {
                                    while ($row = $result->fetch_assoc()) {
                                        $target = new Player($database, $row['receiver']);
                                        if($target->IsValid() && !$target->IsBanned())
                                        {
                                            $cred = true;
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <div style="cursor:pointer;" class="SideMenuButton borderB">
                                        <span style="color: <?php if($cred) echo 'red; font-weight: bold;'; else echo 'white;'; ?>">
                                            User Meldungen
                                        </span>
                                </div>
                            </a>
                            <?php
                        }
                    }
                    if ($player->GetArank() >= 3)
                    {
                        ?>
                        <a href="?p=rundmail" class="no-link">
                            <div class="SideMenuStyle borderB">Rundmail</div>
                        </a>
                        <a href="?p=itemforall" class="no-link">
                            <div class="SideMenuStyle borderB">Item an alle senden</div>
                        </a>
                        <a href="?p=balancing" class="no-link">
                            <div class="SideMenuStyle borderB">Balancing</div>
                        </a>
                        <a href="https://server.opbu.de/db-administration" target="_blank" class="no-link">
                            <div class="SideMenuStyle borderB">Datenbank</div>
                        </a>
                        <a href="?p=gewinns" class="no-link">
                            <div class="SideMenuStyle borderB">Gewinnspiel</div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="spacer"></div>
                <?php
            }
        ?>
    </div>
<?php
}
?>
</div>
<script>
    if(localStorage.getItem('top5open') === 'true')
    {
        document.getElementById("top5").open = true;
        console.log('top5open: open');
    }
    else
    {
        document.getElementById("top5").open = false;
        console.log('top5open: closed');
    }

    function toggleTopFive()
    {
        if(document.getElementById("top5").hasAttribute('open'))
        {
            localStorage.setItem('top5open', 'true');
            console.log('opened');
        }
        else
        {
            localStorage.setItem('top5open', 'false');
            console.log('closed');
        }
    }
</script>