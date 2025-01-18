<?php
$ranks = explode('@',$clan->GetRanks());

if ($player->GetPlanet() != 2)
{
    ?>
    <style>
        input[type=number]
        {
            color:white;
            background:#1a1b1e;
            padding:5px;
            border:2px solid #2a2c30;
            -webkit-border-radius: 5px;
            border-radius: 5px;
        }
    </style>
    <div class="spacer"></div>
    <?php
    if ($clan->GetImage() != '')
    {
        ?>
        <div class="catGradient borderT borderB" style="width:90%;">
            <h2>
                <?php echo $clan->GetName(); ?>
            </h2>
        </div>
        <div style="min-width:100%; max-width:100%;">
            <img src="img.php?url=<?php echo $clan->GetImage(); ?>" style="width:600px; height:400px;">
        </div>
        <?php
    }
    else
    {
        ?>
        <div class="catGradient borderT borderB" style="width:90%;">
            <h2>
                <?php echo $clan->GetName(); ?>
            </h2>
        </div>
        <div style="min-width:100%; max-width:100%;">
            <img src="img/clannoimage.png" style="width:600px; height:400px;">
        </div>
        <?php
    }
    ?>
    <div class="spacer"></div>
    <div class="catGradient borderT" style="width:90%;">
        Bande Level: <?php echo number_format($clan->GetLevel(), 0 , ',', '.'); ?>
    </div>
    <div style="width: 90%; height: 300px;">
        <div style="width: 100%; height: 300px;">
            <img src="img/offtopic/background.png" id="bg" style="transition: 0.4s; width: 100%; height: 100%;"/>
        </div>
        <div style="position:relative; top: -300px; width: 100%; height: 300px;">
            <?php
            if($clan->GetFlag() != '')
            {
                ?>
                <img src="img/offtopic/flag.png" style="position:relative; top: 60px; left: 120px; width: 60%; height: 60%;"/>
                <img src="<?php echo $clan->GetFlag(); ?>" style="position:relative; top: 30px; left: -180px; width: 230px; height: 110px;"/>
                <?php
            }
            else
            {
                ?>
                <img src="img/offtopic/flag.png" style="position:relative; top: 60px; width: 60%; height: 60%;"/>
                <?php
            }
            ?>
        </div>
        <div style="position:relative; top: -600px; width: 100%; height: 300px;">
            <div class="tooltip" style="position: relative; top: 220px; left: 170px;" onmouseenter="bg.style.filter = 'blur(2px)';" onmouseleave="bg.style.filter = '';">
                <img src="img/offtopic/ANG.png?1" style="cursor: pointer; width: 80px; height: 80px;" <?php if($clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management')) { ?> onclick="OpenPopupPage('Angriff aufwerten','clan/level.php?id=<?php echo $clan->GetID(); ?>&a=atk')" <?php } ?>/>
                <span class="tooltiptext" style="width:250px; top:-140px; left:-80px;">
                        Angriff aufwerten
                        <hr/>
                        Erhöht den Angriff der Bandenmitglieder außerhalb von Storykämpfen um 0.5%.<br/>
                        <hr/>
                        Level: <?php echo number_format($clan->GetAttack() + 1, 0, '', '.'); ?> <br/>
                        <?php echo number_format($clan->GetLevelUpCostBerry($clan->GetAttack() + 1), 0, '', '.'); ?> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/><br/>
					    <?php echo number_format($clan->GetLevelUpCostGold($clan->GetAttack() + 1), 0, '', '.'); ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>
                    </span>
            </div>
            <div class="tooltip" style="position: relative; top: 0; left: 90px;" onmouseenter="bg.style.filter = 'blur(2px)';" onmouseleave="bg.style.filter = '';">
                <img src="img/offtopic/LP.png?1" style="cursor: pointer; width: 80px; height: 80px;" <?php if($clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management')) { ?> onclick="OpenPopupPage('LP aufwerten','clan/level.php?id=<?php echo $clan->GetID(); ?>&a=lp')" <?php } ?>/>
                <span class="tooltiptext" style="width:250px; top:-140px; left:-80px;">
                        LP aufwerten
                        <hr/>
                        Erhöht die LP der Bandenmitglieder außerhalb von Storykämpfen um 0.5%.<br/>
                        <hr/>
                        Level: <?php echo number_format($clan->GetLP() + 1, 0, '', '.'); ?> <br/>
                        <?php echo number_format($clan->GetLevelUpCostBerry($clan->GetLP() + 1), 0, '', '.'); ?> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/><br/>
					    <?php echo number_format($clan->GetLevelUpCostGold($clan->GetLP() + 1), 0, '', '.'); ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>
                    </span>
            </div>
            <div class="tooltip" style="position: relative; top: 100px; left: -190px;" onmouseenter="bg.style.filter = 'blur(2px)';" onmouseleave="bg.style.filter = '';">
                <img src="img/offtopic/AD.png?1" style="cursor: pointer; width: 80px; height: 80px;" <?php if($clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management')) { ?> onclick="OpenPopupPage('AD aufwerten','clan/level.php?id=<?php echo $clan->GetID(); ?>&a=ad')" <?php } ?>/>
                <span class="tooltiptext" style="width:250px; top:-140px; left:-80px;">
                        AD aufwerten
                        <hr/>
                        Erhöht die AD der Bandenmitglieder außerhalb von Storykämpfen um 0.5%.<br/>
                        <hr/>
                        Level: <?php echo number_format($clan->GetAD() + 1, 0, '', '.'); ?> <br/>
                        <?php echo number_format($clan->GetLevelUpCostBerry($clan->GetAD() + 1), 0, '', '.'); ?> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/><br/>
					    <?php echo number_format($clan->GetLevelUpCostGold($clan->GetAD() + 1), 0, '', '.'); ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>
                    </span>
            </div>
            <div class="tooltip" style="position: relative; top: 100px; left: 110px;" onmouseenter="bg.style.filter = 'blur(2px)';" onmouseleave="bg.style.filter = '';">
                <img src="img/offtopic/DEF.png?1" style="cursor: pointer; width: 80px; height: 80px;" <?php if($clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management')) { ?> onclick="OpenPopupPage('Abwehr aufwerten','clan/level.php?id=<?php echo $clan->GetID(); ?>&a=def')" <?php } ?>/>
                <span class="tooltiptext" style="width:250px; top:-140px; left:-80px;">
                        Abwehr aufwerten
                        <hr/>
                        Erhöht die Abwehr der Bandenmitglieder außerhalb von Storykämpfen um 0.5%.<br/>
                        <hr/>
                        Level: <?php echo number_format($clan->GetDefense() + 1, 0, '', '.'); ?> <br/>
                        <?php echo number_format($clan->GetLevelUpCostBerry($clan->GetDefense() + 1), 0, '', '.'); ?> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/><br/>
					    <?php echo number_format($clan->GetLevelUpCostGold($clan->GetDefense() + 1), 0, '', '.'); ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>
                    </span>
            </div>
            <div class="tooltip" style="position: relative; top: 215px; left: -415px;" onmouseenter="bg.style.filter = 'blur(2px)';" onmouseleave="bg.style.filter = '';">
                <img src="<?php if($clan->GetExp() >= $clan->GetRequiredExp()) echo 'img/offtopic/lvlup.png?1'; else echo 'img/offtopic/keinlvlup.png?1'; ?>" style="cursor: pointer; width: 80px; height: 80px;" <?php if($clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management') && $clan->GetExp() >= $clan->GetRequiredExp()) { ?> onclick="OpenPopupPage('Bande aufwerten','clan/level.php?id=<?php echo $clan->GetID(); ?>&a=level')" <?php } ?>/>
                <span class="tooltiptext" style="width:250px; top:-180px; left:-80px;">
                        Bande aufwerten
                        <hr/>
                        Erhöht alle Stats der Bandenmitglieder außerhalb von Storykämpfen um 0.5%.<br/>
                        Jede 2. Stufe erhöht das Mitgliederlimit um 1.<br/>
                        <hr/>
                        Level: <?php echo number_format($clan->GetLevel() + 1, 0, '', '.'); ?> <br/>
                        <?php echo number_format($clan->GetLevelUpCostGold($clan->GetLevel() + 1, false), 0, '', '.'); ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>
                    </span>
            </div>
        </div>
    </div>
    <div class="expback" style="height:20px; width:90%;">
        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
        <div class="expanzeige" style="width:<?php echo $clan->GetEXPPercentage(); ?>%"></div>
        <div class="exptext">
            Aktivitätspunkte:
            <?php echo number_format($clan->GetExp(), '0', '', '.'); ?> /
            <?php echo number_format($clan->GetRequiredExp(), '0', '', '.'); ?>
            <?php echo '('.$clan->GetEXPPercentage().' %)' ?>
        </div>
    </div>
    <div class="spacer"></div>

    <table width="90%">
        <tr>
            <td style="text-align:center">
                <div class="SideMenuButton" style="cursor: pointer;" onclick="aktivitaet.hidden = true; verwaltung.hidden = false;">Verwaltung</div>
            </td>
            <td style="text-align:center">
                <div class="SideMenuButton" style="cursor: pointer;" onclick="aktivitaet.hidden = false; verwaltung.hidden = true;">Aktivität</div>
            </td>
        </tr>
    </table>
    <div class="catGradient borderT" style="width:90%;">
        Mitglieder <?php echo number_format($clan->GetMembers(), 0 , ',', '.'); ?> / <?php echo number_format($clan->GetMaxMembers(), 0, '', '.'); ?>
    </div>
    <div id="verwaltung">
        <table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
            <tr>
                <td width="8%"><b>Lv.</b></td>
                <td width="20%"><b>User</b></td>
                <td width="10%"><b>Rang</b></td>
                <td width="15%"><b>Fraktion</b></td>
                <td width="25%"><b>Bandenrang</b></td>
                <td width="22%"><b>Status</b></td>
            </tr>
            <?php
            if (!isset($titelManager)) $titelManager = new TitelManager($database);

            $id = 0;
            $list = new Generallist($database, 'accounts', '*', 'clan=' . $clan->GetID(), 'rank', 999, 'ASC');
            $entry = $list->GetEntry($id);
            while ($entry != null)
            {
                ?>
                <tr>
                    <td>
                        <?php echo number_format($entry['level'], '0', '', '.'); ?>
                    </td>
                    <td>
                        <?php
                        if ($entry['clanrank'] == 0)
                        {
                            echo '<img alt="'.explode(';', $ranks[0])[0].'" title="'.explode(';', $ranks[0])[0].'" src="../img/stern2.png" width="15px" height="15px">';
                        }
                        else if ($entry['clanrank'] == 1)
                        {
                            echo '<img alt="'.explode(';', $ranks[1])[0].'" title="'.explode(';', $ranks[1])[0].'" src="../img/stern.png" width="15px" height="15px">';
                        } ?>
                        <a href="?p=profil&id=<?php echo $entry['id']; ?>"><?php echo $entry['name']; ?></a>
                    </td>
                    <td>
                        <?php echo number_format($entry['rank'], '0', '', '.'); ?>
                    </td>
                    <td>
                        <?php echo $entry['race']; ?>
                    </td>
                    <td>
                        <form method="post" action="?p=clanmanage&a=changerank">
                            <input type="hidden" name="player" value="<?php echo $entry['id']; ?>">
                            <?php
                            if($entry['clanrank'] == 0)
                            {
                                ?>
                                <select onchange="this.form.submit();" class="select" name="rank" style="width: 120px;" disabled>
                                    <?php
                                    for ($i = 0; $i < count($ranks); $i++)
                                    {
                                        $rank = explode(';', $ranks[$i]);
                                        $rankname = $rank[0];
                                        $selected = '';
                                        if($entry['clanrank'] == $i)
                                            $selected = 'selected';
                                        echo '<option value="'.$i.'" '.$selected.'>'.$rankname.'</option>';
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            else
                            {
                                ?>
                                <select onchange="this.form.submit();" class="select" name="rank" style="width: 120px;" <?php if($clan->GetLeader() != $player->GetID()) echo 'disabled';?>>
                                    <?php
                                    for ($i = 1; $i < count($ranks); $i++)
                                    {
                                        $rank = explode(';', $ranks[$i]);
                                        $rankname = $rank[0];
                                        $selected = '';
                                        if($entry['clanrank'] == $i)
                                            $selected = 'selected';
                                        echo '<option value="'.$i.'" '.$selected.'>'.$rankname.'</option>';
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            ?>
                        </form>
                    </td>
                    <td>
                        <?php
                        $userTime = strtotime($entry['lastaction']);
                        $timeDiffSeconds = time() - $userTime;
                        $timeDiffMinutes = $timeDiffSeconds / 60;
                        $timeDiffHours = $timeDiffMinutes / 60;
                        $timeDiffDays = $timeDiffHours / 24;
                        if ($timeDiffMinutes < 10)
                        {
                            $userstatus = "<span style='color: green;'>Online";
                        }
                        else if ($timeDiffMinutes >= 10 and $timeDiffMinutes <= 30)
                        {
                            $userstatus = "<span style='color: orange;'>AFK";
                        }
                        else
                        {
                            $userstatus = "<span style='color: red;'>Offline";
                            $userstatus .= ' *';

                            if ($timeDiffMinutes < 60) {
                                $timeDiffMinutes = floor($timeDiffMinutes);
                                $userstatus .= number_format($timeDiffMinutes, '0', '', '.');
                                $userstatus .= ' Min.';
                            } else if ($timeDiffHours < 24) {
                                $timeDiffHours = floor($timeDiffHours);
                                $userstatus .= number_format($timeDiffHours, '0', '', '.');
                                $userstatus .= ' S';
                            } else {
                                $timeDiffDays = floor($timeDiffDays);
                                $userstatus .= number_format($timeDiffDays, '0', '', '.');
                                $userstatus .= ' T';
                            }
                        }

                        echo '<b>' . $userstatus . '</span></b><br>';
                        ?>
                    </td>
                    <?php
                    if($entry['clanrank'] > 0 && $player->GetClan() == $clan->GetID())
                    {
                        if($clan->GetRankPermission($player->GetClanRank(), "management"))
                        {
                            ?>
                            <td>
                                <div class="tooltip" style="position: relative; top:0; left:0;">
                                    <?php
                                    if($entry['leaveclan'] == 0)
                                    {
                                        ?>
                                        <button type="button" onclick="OpenPopupPage('<?php echo $entry['name']; ?> aus der Bande werfen?','clan/kick.php?id=<?php echo $clan->GetID(); ?>&pid=<?php echo $entry['id'];?>&a=kick')"><img src="img/offtopic/Debuff.png"></button>
                                        <span class="tooltiptext" style="width:180px; top:-30px; left:-72px;">Rauswerfen?</span>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <button type="button" onclick="OpenPopupPage('<?php echo $entry['name']; ?> aus der Bande werfen?','clan/kick.php?id=<?php echo $clan->GetID(); ?>&pid=<?php echo $entry['id'];?>&a=unkick')"><img src="img/offtopic/Buff.png"></button>
                                        <span class="tooltiptext" style="width:210px; top:-30px; left:-72px;">Rauswurf rückgängig machen?</span>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </td>
                            <?php
                        }
                        if($clan->GetLeader() == $player->GetID())
                        {
                            ?>
                            <td>
                                <div class="tooltip" style="position: relative; top:0; left:0;">
                                    <button type="button" onclick="OpenPopupPage('<?php echo $entry['name']; ?> zum Kapitän ernennen?','clan/promote.php?id=<?php echo $clan->GetID(); ?>&pid=<?php echo $entry['id'];?>')"><img src="img/offtopic/Buff.png"></button>
                                    <span class="tooltiptext" style="width:180px; top:-30px; left:-72px;">Zum Kapitän ernennen?</span>
                                </div>
                            </td>
                            <?php
                        }
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
    <div id="aktivitaet" hidden>
        <table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
            <tr>
                <td width="8%"><b>Lv.</b></td>
                <td width="20%"><b>User</b></td>
                <td width="10%"><b>Rang</b></td>
                <td width="10%"><b>NPC</b></td>
                <td width="8%"><b>PvP</b></td>
                <td width="8%"><b>Elo</b></td>
                <td width="16%"><b>Dungeons</b></td>
                <td width="20%"><b>Kolosseum</b></td>
            </tr>
            <?php
            if (!isset($titelManager)) $titelManager = new TitelManager($database);

            $id = 0;
            $list = new Generallist($database, 'accounts', '*', 'clan=' . $clan->GetID(), 'rank', 999, 'ASC');
            $entry = $list->GetEntry($id);
            while ($entry != null)
            {
                $arenapoints = $entry['collectedkolopoints'];
                $maxarenapoints = $entry['bookingdays'] == 0 ? 500 : 0;
                $dungeon = $entry['bookingdays'] == 0 ? $entry['dungeon'] : 0;
                $dungeonmax = $entry['bookingdays'] == 0 ? 10 : 0;
                $elofights = $entry['bookingdays'] == 0 ? min($entry['dailyelofights'], 5) : 0;
                $elofightsmax = $entry['bookingdays'] == 0 ? 5 : 0;
                $pvps = $entry['bookingdays'] == 0 ? min($entry['dailyfights'], 5) : 0;
                $pvpsmax = $entry['bookingdays'] == 0 ? 5 : 0;
                $npcfights = $entry['bookingdays'] == 0 ? min($entry['dailynpcfights'], 50) : 0;
                $npcsmax = $entry['bookingdays'] == 0 ? min($entry['dailynpcfightsmax'], 50) : 0;
                ?>
                <tr>
                    <td>
                        <?php echo number_format($entry['level'], '0', '', '.'); ?>
                    </td>
                    <td>
                        <?php
                        if ($entry['clanrank'] == 0)
                        {
                            echo '<img alt="'.explode(';', $ranks[0])[0].'" title="'.explode(';', $ranks[0])[0].'" src="../img/stern2.png" width="15px" height="15px">';
                        }
                        else if ($entry['clanrank'] == 1)
                        {
                            echo '<img alt="'.explode(';', $ranks[1])[0].'" title="'.explode(';', $ranks[1])[0].'" src="../img/stern.png" width="15px" height="15px">';
                        } ?>
                        <a href="?p=profil&id=<?php echo $entry['id']; ?>"><?php echo $entry['name']; ?></a>
                    </td>
                    <td>
                        <?php echo number_format($entry['rank'], '0', '', '.'); ?>
                    </td>
                    <td>
                        <?php echo number_format($npcfights, '0', '', '.') . " / " . number_format($npcsmax, '0', '', '.'); ?>
                    </td>
                    <td>
                        <?php echo number_format($pvps, '0', '', '.') . " / " . $pvpsmax; ?>
                    </td>
                    <td>
                        <?php echo number_format($elofights, '0', '', '.') . " / " . $elofightsmax; ?>
                    </td>
                    <td>
                        <?php echo number_format($dungeon, '0', '', '.') . " / " . $dungeonmax; ?>
                    </td>
                    <td>
                        <?php echo number_format($arenapoints, '0', '', '.') . " / " . $maxarenapoints; ?>
                    </td>
                </tr>
                <?php
                $id++;
                $entry = $list->GetEntry($id);
            }
            ?>
        </table>
        <?php
        if($player->GetArank() == 3)
        {
        ?>
      <?php
}
 ?>
    <div class="spacer"></div>

        <div class="catGradient borderT" style="width:90%;">
            Tägliche Aufgaben
        </div>
    <div class="expback" style="height:20px; width:90%;">
        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
        <div class="expanzeige" style="width:<?php echo $clan->AllTaskNPCPerecentage(); ?>%"></div>
        <div class="exptext">
            Tägliche NPC:
            <?php echo number_format($clan->AllTaskNPCs(), '0', '', '.'); ?> /
            <?php echo number_format($clan->AllTaskISNPCs(), '0', '', '.'); ?>
            <?php echo '('.$clan->AllTaskNPCPerecentage().' %)' ?>
        </div>
    </div>

    <div class="expback" style="height:20px; width:90%;">
        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
        <div class="expanzeige" style="width:<?php echo $clan->AllTaskEloPerecentage(); ?>%"></div>
        <div class="exptext">
            Tägliche Elokämpfe:
            <?php echo number_format($clan->AllTaskElos(), '0', '', '.'); ?> /
            <?php echo number_format($clan->AllTaskISElo(), '0', '', '.'); ?>
            <?php echo '('.$clan->AllTaskEloPerecentage().' %)' ?>
        </div>
    </div>
        <div class="expback" style="height:20px; width:90%;">
            <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
            <div class="expanzeige" style="width:<?php echo $clan->AllTaskPvPPerecentage(); ?>%"></div>
            <div class="exptext">
                Tägliche PvP-Kämpfe:
                <?php echo number_format($clan->AllTaskPvP(), '0', '', '.'); ?> /
                <?php echo number_format($clan->AllTaskISPvP(), '0', '', '.'); ?>
                <?php echo '('.$clan->AllTaskPvPPerecentage().' %)' ?>
            </div>
        </div>
        <div class="expback" style="height:20px; width:90%;">
            <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
            <div class="expanzeige" style="width:<?php echo $clan->AllTaskDungeonPercentage(); ?>%"></div>
            <div class="exptext">
                Tägliche Dungeons:
                <?php echo number_format($clan->AllTaskDungeon(), '0', '', '.'); ?> /
                <?php echo number_format($clan->AllTaskISDungeon(), '0', '', '.'); ?>
                <?php echo '('.$clan->AllTaskDungeonPercentage().' %)' ?>
            </div>
        </div>
    </div>
    <?php
    if($clan->GetLeader() == $player->GetID() || $player->GetArank() >= 2)
    {
        ?>
        <div class="spacer"></div>
        <div class="catGradient borderT" style="width:90%;">
            Rangverwaltung
        </div>
        <form method="post" action="?p=clanmanage&a=rank">
            <table width="90%" cellspacing="0" border="0" id="ranks" class="borderT borderR borderL borderB">
                <tr>
                    <th>Rang:</th>
                    <th style="width: 70px;">
                        <div class="tooltip" style="position: relative; top:0; left:0;">
                            <img src="img/offtopic/Schatzkammer.png" width="25px" height="20px">
                            <span class="tooltiptext" style="width:180px; top:-30px; left:-77px;">Schatzkammer einsehen:</span>
                        </div>
                    </th>
                    <th style="width: 70px;">
                        <div class="tooltip" style="position: relative; top:0; left:0;">
                            <img src="img/offtopic/Schatzkammerauszahlen.png" width="30px" height="20px">
                            <span class="tooltiptext" style="width:180px; top:-30px; left:-77px;">Schatzkammer auszahlen:</span>
                        </div>
                    </th>
                    <th style="width: 70px;">
                        <div class="tooltip" style="position: relative; top:0; left:0;">
                            <img src="img/pm.png" width="25px" height="20px">
                            <span class="tooltiptext" style="width:180px; top:-30px; left:-77px;">Rundmail verschicken:</span>
                        </div>
                    </th>
                    <th style="width: 70px;">
                        <div class="tooltip" style="position: relative; top:0; left:0;">
                            <img src="img/offtopic/Bandenprofilbearbeiten.png" width="25px" height="20px">
                            <span class="tooltiptext" style="width:180px; top:-30px; left:-77px;">Bandenprofil bearbeiten:</span>
                        </div>
                    </th>
                    <th style="width: 70px;">
                        <div class="tooltip" style="position: relative; top:0; left:0;">
                            <img src="img/offtopic/InternerBereich.png" width="25px" height="20px">
                            <span class="tooltiptext" style="width:230px; top:-30px; left:-102px;">Internen Bereich einsehen:</span>
                        </div>
                    </th>
                    <th style="width: 70px;">
                        <div class="tooltip" style="position: relative; top:0; left:0;">
                            <img src="img/offtopic/Bandenkampfe.png" width="25px" height="20px">
                            <span class="tooltiptext" style="width:180px; top:-30px; left:-77px;">Bandenkämpfe führen:</span>
                        </div>
                    </th>
                    <th style="width: 70px;">
                        <div class="tooltip" style="position: relative; top: 0; left: 0;">
                            <img src="img/offtopic/management.png" width="25px" height="20px">
                            <span class="tooltiptext" style="width: 180px; top: -30px; left: -77px;">Bandenmanagement</span>
                        </div>
                    </th>
                    <th style="width: 70px;">
                        <div class="tooltip" style="position: relative; top:0; left:0;">
                            X
                            <span class="tooltiptext" style="width:180px; top:-30px; left:-82px;">Rang löschen:</span>
                        </div>
                    </th>
                </tr>
                <?php
                for ($i = 1; $i < count($ranks); $i++)
                {
                    $rank = explode(';', $ranks[$i]);
                    $rankname = $rank[0];
                    $canSeeSK = $rank[1];
                    $canChangeSK = $rank[2];
                    $canDoRM = $rank[3];
                    $canChangePro = $rank[4];
                    $canSeeIntern = $rank[5];
                    $canDoFights = $rank[6];
                    $canDoManagement = $rank[7];
                    ?>
                    <tr style="height: 25px;">
                        <td style="width: 20%; text-align: center; padding-top: 10px;"><input type="text" name="rankname[<?php echo $i; ?>]" value="<?php echo $rankname; ?>" style="height:20px; width: 80px; padding: 5px;" <?php if($clan->GetLeader() != $player->GetID()) echo 'disabled'; ?> ></td>
                        <td style="text-align: center; padding-top: 10px;"><input name="canseesk[<?php echo $i; ?>]" type="checkbox" <?php if($canSeeSK) echo 'checked'; ?> <?php if($clan->GetLeader() != $player->GetID()) echo 'disabled'; ?>></td>
                        <td style="text-align: center; padding-top: 10px;"><input name="canchangesk[<?php echo $i; ?>]" type="checkbox" <?php if($canChangeSK) echo 'checked'; ?> <?php if($clan->GetLeader() != $player->GetID()) echo 'disabled'; ?>></td>
                        <td style="text-align: center; padding-top: 10px;"><input name="candorm[<?php echo $i; ?>]" type="checkbox" <?php if($canDoRM) echo 'checked'; ?> <?php if($clan->GetLeader() != $player->GetID()) echo 'disabled'; ?>></td>
                        <td style="text-align: center; padding-top: 10px;"><input name="canchangepro[<?php echo $i; ?>]" type="checkbox" <?php if($canChangePro) echo 'checked'; ?> <?php if($clan->GetLeader() != $player->GetID()) echo 'disabled'; ?>></td>
                        <td style="text-align: center; padding-top: 10px;"><input name="canseeintern[<?php echo $i; ?>]" type="checkbox" <?php if($canSeeIntern) echo 'checked'; ?> <?php if($clan->GetLeader() != $player->GetID()) echo 'disabled'; ?>></td>
                        <td style="text-align: center; padding-top: 10px;"><input name="candofights[<?php echo $i; ?>]" type="checkbox" <?php if($canDoFights) echo 'checked'; ?> <?php if($clan->GetLeader() != $player->GetID()) echo 'disabled'; ?>></td>
                        <td style="text-align: center; padding-top: 10px;"><input name="candomanagement[<?php echo $i; ?>]" type="checkbox" <?php if($canDoManagement) echo 'checked'; ?> <?php if($clan->GetLeader() != $player->GetID()) echo 'disabled'; ?>></td>
                        <?php
                        if($i > 2 && $clan->GetLeader() == $player->GetID())
                        {
                            ?>
                            <td style="text-align: center; padding-top: 10px;">
                                <div class="tooltip" style="position: relative; top:0; left:0;">
                                    <a onclick="RemoveTableRow(this);" href="?p=clanmanage&a=deleterank&rid=<?php echo $i; ?>">
                                        <button type="button">X</button>
                                    </a>
                                    <span class="tooltiptext" style="width:180px; top:-30px; left:-70px;">Rang löschen?</span>
                                </div>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
                if($clan->GetLeader() == $player->GetID())
                {
                    ?>
                    <tr>
                        <td colspan="3" style="padding: 10px;">
                            <button type="button" onclick="AddClanRank('ranks', 7);">Rang hinzufügen</button>
                        </td>
                        <td colspan="6" style="text-align: right; padding: 10px;">
                            <input type="submit" value="Speichern">
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </form>
        <?php
    }
    ?>
    <div class="spacer"></div>
    <div class="catGradient borderT" style="width:90%;">
        Information
    </div>
    <table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
        <tr>
            <td width="20%">Discord:</td>
            <td width="80%">
                <a target="_blank" href="<?php echo $clan->GetDiscord(); ?>">
                    <?php echo $clan->GetDiscord(); ?>
                </a>
            </td>
        </tr>
    </table>
    <div class="spacer"></div>
    <div class="catGradient borderT" style="width:90%;">
        Banden Shoutbox
    </div>
    <div class="clankasse2 borderT borderB borderL borderR" style="width:90%">
        <div class="clanshoutboxchat borderB">
            <table width="100%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
                <?php
                $msgs = $clan->GetShoutboxMSG();
                $i = 0;
                while (isset($msgs[$i]))
                {
                    $msg = $msgs[$i];
                    ?>
                    <tr>
                        <td style="text-align:left;" width="23%">
                            [<?php echo $msg->GetDate(); ?>]
                        <td style="text-align:left;" width="20%">
                            <?php echo $msg->GetFrom(); ?>:
                        </td>
                        <td style="text-align:left;" width="65%">
                            <?php echo htmlentities($msg->GetText()); ?>
                        </td>
                    </tr>
                    <?php
                    ++$i;
                }
                ?>
            </table>
        </div>
        <div class="spacer"></div>
        <?php
        if($player->GetClan() == $clan->GetID())
        {
            ?>
            <div class="clanshoutboxchat2">
                <form method="POST" action="?p=clanmanage&a=post">
                    <input type="text" name="shoutboxtext" placeholder="Nachricht" style="height:30px; min-width:400px; max-width:400px;"><input style="height:38px;" type="submit" value="Senden">
                </form>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="spacer"></div>
    <div class="catGradient borderT" style="width:90%;">
        Schatzkammer
    </div>
    <table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
        <tr>
            <td width="20%">Punkte:</td>
            <td width="80%"><?php echo number_format($clan->GetPoints(), '0', '', '.'); ?></td>
        </tr>
        <?php
        if($clan->GetRankPermission($player->GetClanRank(), "treasuresee"))
        {
            ?>
            <tr>
                <td width="20%"><img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/>:</td>
                <td width="80%"><?php echo number_format($clan->GetBerry(), '0', '', '.'); ?></td>
            </tr>
            <tr>
                <td width="20%"><img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>:</td>
                <td width="80%"><?php echo number_format($clan->GetGold(), '0', '', '.'); ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <div class="spacer"></div>
    <div class="catGradient borderT" style="width:90%;">
        <b>Schatzkammer Log</b>
    </div>
    <div class="clankasse" style="width:90%">
        <table width="100%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
            <tr>
                <td width="15%">
                    <center><b>Datum</b></center>
                </td>
                <td width="20%">
                    <center><b>Von</b></center>
                </td>
                <td width="20%">
                    <center><b>An</b></center>
                </td>
                <td width="10%">
                    <center><b>Betrag</b></center>
                </td>
            </tr>
            <?php
            if($clan->GetRankPermission($player->GetClanRank(), "treasuresee"))
            {
                $msgs = $clan->GetLogMSG();
                $i = 0;
                while (isset($msgs[$i]))
                {
                    $msg = $msgs[$i];
                    ?>
                    <tr>
                        <td width="15%">
                            <center><?php echo $msg->GetDate(); ?></center>
                        </td>
                        <td width="20%">
                            <center><?php echo $msg->GetFrom(); ?></center>
                        </td>
                        <td width="20%">
                            <center><?php echo $msg->GetTo(); ?></center>
                        </td>
                        <td width="10%">
                            <center><?php echo number_format($msg->GetAmount(), '0', '', '.'); ?></center>
                        </td>
                    </tr>
                    <?php
                    ++$i;
                }
            }
            else
            {
                ?>
                <tr>
                    <td colspan="4" style="text-align: center; height: 160px;">
                        Du hast keine Berechtigung die Schatzkammer einzusehen.
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
    <?php
    if($player->GetClan() == $clan->GetID())
    {
        ?>
        <table width="90%" cellspacing="0" border="0" class="borderB borderR borderL">
            <tr>
                <td class="catGradient borderB borderT" colspan="3" align="center">Schatzkammer Verwaltung</td>
            </tr>
            <tr>
                <form method="post" action="?p=clanmanage&a=pay">
                    <td width="50%">
                        <center><input type="number" name="berry" placeholder="0"></center>
                    </td>
                    <td width="50%">
                        <center><img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/> in die Kasse</center>
                    </td>
                    <td width="50%">
                        <center><input type="submit" value="Einzahlen"></center>
                    </td>
                </form>
            </tr>
            <tr>
                <form method="POST" action="?p=clanmanage&a=payg">
                    <td width="50%">
                        <center><input type="number" name="gold" placeholder="0"></center>
                    </td>
                    <td width="50%">
                        <center><img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/> in die Kasse</center>
                    </td>
                    <td width="50%">
                        <center><input type="submit" value="Einzahlen"></center>
                    </td>
                </form>
            </tr>
            <?php
            if($clan->GetRankPermission($player->GetClanRank(), "treasureedit"))
            {
                ?>
                <tr>
                    <form method="POST" action="?p=clanmanage&a=payout">
                        <td width="50%">
                            <center><input type="number" name="berry" placeholder="0"></center>
                        </td>
                        <td width="50%">
                            <center>
                                <select class="select" name="playerid">
                                    <?php
                                    $list = new Generallist($database, 'accounts', '*', 'clan="' . $clan->GetID() . '"', 'rank', 30, 'ASC');
                                    $id = 0;
                                    $entry = $list->GetEntry($id);
                                    while ($entry != null)
                                    {
                                        ?>
                                        <option value="<?php echo $entry['id']; ?>"><?php echo $entry['name']; ?></option>
                                        <?php
                                        ++$id;
                                        $entry = $list->GetEntry($id);
                                    }
                                    ?>
                                </select>
                            </center>
                        </td>
                        <td width="50%">
                            <center>
                                <input type="submit" value="Auszahlen">
                            </center>
                        </td>
                    </form>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }
    if ($clan->GetRankPermission($player->GetClanRank(), "massmail") && $player->GetClan() == $clan->GetID())
    {
        ?>
        <div class="spacer"></div>
        <form method="post" action="?p=clanmanage&a=rundmail">
            <div class="catGradient borderT" style="width:90%;">
                Banden Rundmail
            </div>
            <table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
                <tr>
                    <td colspan="2" height="20px"></td>
                </tr>
                <tr>
                    <td width="20%">Betreff:</td>
                    <td width="80%"><input type="text" name="title" placeholder="Betreff" style="width:400px;"></td>
                </tr>
                <tr>
                    <td width="20%">Text:</td>
                    <td width="80%"><textarea class="textfield" name="text" maxlength="300000" style="width:400px; height:200px; resize: vertical;"></textarea></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <center><input type="submit" value="Senden"></center>
                    </td>
                </tr>
            </table>
        </form>
        <?php
    }
    ?>
    <?php
    $list = new Generallist($database, 'accounts', '*', 'clanapplication="' . $clan->GetID() . '"', 'rank', 30, 'ASC');

    if ($list->GetCount() > 0 && $player->GetClan() == $clan->GetID())
    {
        ?>
        <div class="spacer"></div>
        <div class="catGradient borderT borderB" style="width:90%;">
            <h3>
                Bewerbungen
            </h3>
        </div>
        <table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
            <tr>
                <td width="5%"><b>Level</b></td>
                <td width="20%"><b>User</b></td>
                <td width="60%"><b>Text</b></td>
                <td width="15%"><b>Aktion</b></td>
            </tr>
            <?php
            $id = 0;
            $entry = $list->GetEntry($id);
            while ($entry != null)
            {
                $titel = '';
                ?>
                <tr>
                    <td>
                        <?php echo number_format($entry['level'], '0', '', '.'); ?>
                    </td>
                    <td>
                        <a href="?p=profil&id=<?php echo $entry['id']; ?>">
                            <?php echo $titel . ' ' . $entry['name']; ?>
                        </a>
                    </td>
                    <td>
                        <?php echo $bbcode->parse($entry['clanapplicationtext']); ?>
                    </td>
                    <td align="center">
                        <?php
                        if ($clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management'))
                        {
                            if($clan->GetMembers() < $clan->GetMaxMembers())
                            {
                                ?>
                                <a href="?p=clanmanage&a=accept&uid=<?php echo $entry['id']; ?>">
                                    <input type="submit" class="ja" value="Annehmen" />
                                </a>
                                <div class="spacer"></div>
                                <?php
                            }
                            ?>
                            <a href="?p=clanmanage&a=decline&uid=<?php echo $entry['id']; ?>">
                                <input type="submit" class="nein" value="Ablehnen" />
                            </a>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
                $id++;
                $entry = $list->GetEntry($id);
            }
            ?>
        </table>
        <div class="spacer"></div>
        <?php
    }
    if (count($clan->GetAllianceInvites()) > 0 && ($player->GetClan() == $clan->GetID() || $player->GetArank() >= 2))
    {
        ?>
        <div class="spacer"></div>
        <div class="catGradient borderT borderB" style="width:90%;">
            <h3>
                Allianzanfragen
            </h3>
        </div>
        <table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
            <tr>
                <td style="width=30%"><b>Bandenname</b></td>
                <td style="width=10%"><b>Bandentag</b></td>
                <td style="width=25%"><b>Bandenpunkte</b></td>
                <td style="width=10%"><b>Mitgliederzahl</b></td>
                <td style="width=25%"><b>Aktion</b></td>
            </tr>
            <?php
            foreach($clan->GetAllianceInvites() as $alliance)
            {
                $alliClan = new Clan($database, $alliance);
                if($alliClan->IsValid())
                {
                    ?>
                    <tr>
                        <td style="width=30%; text-align: center;">
                            <?= $alliClan->GetName(); ?>
                        </td>
                        <td style="width=10%; text-align: center;">
                            <?= $alliClan->GetTag(); ?>
                        </td>
                        <td style="width=25%; text-align: center;">
                            <?= number_format($alliClan->GetPoints(), 0, '', '.'); ?>
                        </td>
                        <td style="width=10%; text-align: center;">
                            <?= $alliClan->GetMembers(); ?>
                        </td>
                        <?php
                        if ($clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management'))
                        {
                            ?>
                            <td style="width=25%; text-align: center;">
                                <a href="?p=clanmanage&a=acceptalliance&cid=<?= $alliClan->GetID(); ?>">
                                    <input type="submit" class="ja" value="Annehmen" />
                                </a>
                                <div class="spacer"></div>
                                <a href="?p=clanmanage&a=declinealliance&cid=<?= $alliClan->GetID(); ?>">
                                    <input type="submit" class="ja" value="Ablehnen" />
                                </a>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <div class="spacer"></div>
        <?php
    }
    if (count($clan->GetAlliances()) > 0 && ($player->GetClan() == $clan->GetID() || $player->GetArank() >= 2))
    {
        ?>
        <div class="spacer"></div>
        <div class="catGradient borderT borderB" style="width:90%;">
            <h3>
                Allianzen
            </h3>
        </div>
        <table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
            <tr>
                <td style="width=30%; text-align: center"><b>Name</b></td>
                <td style="width=15%; text-align: center"><b>Tag</b></td>
                <td style="width=10%; text-align: center"><b>Punkte</b></td>
                <td style="width=5%; text-align: center"><b>Mitglieder</b></td>
                <td style="width=40%; text-align: center"><b>Aktion</b></td>
            </tr>
            <?php
            foreach($clan->GetAlliances() as $alliance)
            {
                $alliClan = new Clan($database, $alliance);
                if($alliClan->IsValid())
                {
                    ?>
                    <tr>
                        <td style="width=30%; text-align: center;">
                            <a href="?p=clan&id=<?= $alliClan->GetID() ?>">
                                <?= $alliClan->GetName(); ?>
                            </a>
                        </td>
                        <td style="width=15%; text-align: center;">
                            <?= $alliClan->GetTag(); ?>
                        </td>
                        <td style="width=10%; text-align: center;">
                            <?= number_format($alliClan->GetPoints(), 0, '', '.'); ?>
                        </td>
                        <td style="width=5%; text-align: center;">
                            <?= $alliClan->GetMembers(); ?>
                        </td>
                        <?php
                        if ($player->GetClan() == $clan->GetID() && $clan->GetRankPermission($player->GetClanRank(), 'management'))
                        {
                            ?>
                            <td style="width=40%; text-align: center;">
                                <button onclick="OpenPopupPage('Allianz auflösen','clan/cancelalliance.php?id=<?php echo $alliClan->GetID(); ?>')">Allianz auflösen</button>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <div class="spacer"></div>
        <?php
    }
    if ($clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management') || $player->GetArank() >= 2)
    {
        $id = 0;
        $tableExists = false;
        $clans = new GeneralList($database, 'clans', '*', '');
        $entry = $clans->GetEntry($id);
        while ($entry != null)
        {
            $alliClan = new Clan($database, $entry['id']);
            if(!$alliClan->IsValid())
            {
                $id++;
                $entry = $clans->GetEntry($id);
                continue;
            }
            if(in_array($clan->GetID(), $alliClan->GetAllianceInvites()))
            {
                if(!$tableExists)
                {
                    ?>
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB" style="width:90%;">
                        <h3>
                            Gesendete Allianz-Anfragen
                        </h3>
                    </div>
                    <table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
                    <tr>
                        <td style="width=40%; text-align: center"><b>Name</b></td>
                        <td style="width=20%; text-align: center"><b>Tag</b></td>
                        <td style="width=40%; text-align: center"><b>Aktion</b></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td style="width=40%; text-align: center;">
                        <?= $alliClan->GetName(); ?>
                    </td>
                    <td style="width=20%; text-align: center;">
                        <?= $alliClan->GetTag(); ?>
                    </td>
                    <td style="width=40%; text-align: center;">
                        <?php if ($player->GetClan() == $clan->GetID() && $clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management')) { ?>
                            <button onclick="OpenPopupPage('Allianzanfrage zurückziehen','clan/cancelallianceinvite.php?id=<?php echo $alliClan->GetID(); ?>')">Anfrage zurückziehen</button>
                        <?php } ?>
                    </td>
                </tr>
                <?php
                if(!$tableExists)
                {
                    $tableExists = true;
                    ?>
                    </table>
                    <div class="spacer"></div>
                    <?php
                }
            }
            $id++;
            $entry = $clans->GetEntry($id);
        }
    }
    if($player->GetClan() == $clan->GetID())
    {
        ?>
        <div class="spacer"></div>
        <table width="90%" cellspacing="0" border="0" style="text-align: center;" class="borderT borderR borderB borderL">
            <tr>
                <td class="catGradient borderB borderT" colspan="6" align="center">
                    <h2>Verwaltung</h2>
                </td>
            </tr>
            <?php
            if ($clan->GetRankPermission($player->GetClanRank(), "profiledit"))
            {
                ?>
                <form action="?p=clanmanage&a=change" method="post" enctype="multipart/form-data">
                    <tr>
                        <td width="20%"><b>Bandenlogo (600x400)</b></td>
                        <td width="80%"><input type="file" name="file_upload" accept="image/png, image/jpeg"/><input type="hidden" name="image"></td>
                    </tr>
                    <tr>
                        <td width="20%"><b>Flagge (230x110)</b></td>
                        <td width="80%"><input type="file" name="file_upload3" accept="image/png, image/jpeg"/><input type="hidden" name="flag"></td>
                    </tr>
                    <tr>
                        <td width="20%"><b>Wappen (30x30)</b></td>
                        <td width="80%"><input type="file" name="file_upload2" accept="image/png, image/jpeg"/><input type="hidden" name="banner"></td>
                    </tr>
                    <tr>
                        <td width="20%"><b>Text</b></td>
                        <td width="80%">
                            <textarea class="textfield" name="text" maxlength="300000" style="width:400px; height:200px; resize: vertical;"><?php echo $clan->GetText(); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="20%"><b>Regeln</b></td>
                        <td width="80%">
                            <textarea class="textfield" name="rules" maxlength="300000" style="width:400px; height:200px; resize: vertical;"><?php echo $clan->GetRules(); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="20%"><b>Interner Text</b></td>
                        <td width="80%">
                            <textarea class="textfield" name="interntext" maxlength="300000" style="width:400px; height:200px; resize: vertical;"><?php echo $clan->GetInternText(); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="20%"><b>Aufnahmebedingungen</b></td>
                        <td width="80%">
                            <textarea class="textfield" name="requirements" maxlength="300000" style="width:400px; height:200px; resize: vertical;"><?php echo $clan->GetRequirements(); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="20%"><b>Discord Link</b></td>
                        <td width="80%">
                            <input type="text" class="schatten" name="discord" maxlength="255" style="width:400px" value="<?php echo $clan->GetDiscord(); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td width="0%"></td>
                        <td width="100%">
                            <div class="spacer2"></div>
                            <label for="bounty" style="cursor: pointer;">Setze diesen Haken, wenn die Gebühren, für die Impel Down befreiung, von der Bande übernommen werden sollen.</label> <br />
                            <input type="checkbox" id="bounty" name="paysbounty" style="cursor: pointer;" <?php if ($clan->PaysBounty()) echo "checked" ?>>
                            <div class="spacer2"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" value="ändern">
                        </td>
                    </tr>
                </form>
                <?php
            }
            if ($clan->GetLeader() == $player->GetID())
            {
                ?>
                <tr>
                    <td colspan="2">
                        <div class="spacer2"></div>
                        <button type="submit" onclick="OpenPopupPage('Bandennamen ändern','clan/namechange.php?id=<?php echo $clan->GetID(); ?>')">Bandennamen ändern</button>
                    <td>
                </tr>
                <form action="?p=clanmanage&a=delete" method="post" enctype="multipart/form-data">
                    <tr>
                        <td colspan="6" align="center" height="50px"></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="color:red;">
                                Du kannst die Bande auch als Leader verlassen,<br/>
                                die Leitungsposition geht automatisch an einen Co-Leader über,<br/>
                                sollte die Bande keinen Co-Leader haben, geht die Bande an ein zufälliges Mitglied über.<br/>
                                Die Bande wird gelöscht wenn kein weiteres Mitglied in der Bande ist.<br/><br/>
                            </div>
                            <input type="checkbox" name="realcheck"> Mit diesen Haken bestätige ich, dass ich die Bande verlassen möchte.<br/><br/>
                            <input type="submit" value="Bande Verlassen">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" height="20px"></td>
                    </tr>
                </form>
                <?php
            }
            else
            {
                if($player->GetLeaveClan() == 0)
                {
                    ?>
                    <form action="?p=clanmanage&a=leave" method="post" enctype="multipart/form-data">
                        <tr>
                            <td colspan="6" align="center" height="20px"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="realcheck">Bestätige mit diesem Haken, das du die Bande verlassen möchtest.</label><br />
                                <input type="checkbox" id="realcheck" name="realcheck"><br />
                                <input type="submit" value="Bande verlassen">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="20px"></td>
                        </tr>
                    </form>
                    <?php
                }
                else
                {
                    ?>
                    <form action="?p=clanmanage&a=stay" method="post" enctype="multipart/form-data">
                        <tr>
                            <td colspan="6" align="center" height="20px"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Zum nächsten Update verlässt du die Bande, wenn du doch in der Bande bleiben möchtest, dann bestätige dies bitte unten mit den Haken, und drücke den Button "Im Clan bleiben".<br/>
                                <label for="realcheck">Bestätige mit diesem Haken, dass du im Clan bleiben möchtest.</label><br />
                                <input type="checkbox" id="realcheck" name="realcheck"><br />
                                <input type="submit" value="Im Clan bleiben">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="20px"></td>
                        </tr>
                    </form>
                    <?php
                }
            }
            ?>
        </table>
        <?php
    }
}
else
{
    echo "<br/><br/><h3>Du bist im Gefängnis, du kannst keinen Kontakt zu deiner Bande aufnehmen.</h3>";
    echo "<br/><div class='spacer'></div><hr>";
    echo "<img src='img/marketing/imgefngnis.png'/><br/><hr>";
}
?>
<div class="spacer"></div>