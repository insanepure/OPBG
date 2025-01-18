<?php
include_once 'classes/clan/clan.php';
include_once 'classes/bbcode/bbcode.php';
?>
<div id="SideMenuBar" class="SideMenuBar" style="display:flex; flex-direction: column; align-items: center; text-align: center; margin-left:0;float:left;min-height:0;">
    <?php
    if ($player->IsLogged())
    {
        ?>
        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                <img alt="Charakter" title="Charakter" width="160" src="<?php echo $serverUrl; ?>img/marketing/onepiececharakterbild.png" />
            </div>
            <?php
            if($player->GetArank() < 2)
            {
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=profil')">Profil</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=inventar')">Inventar</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=ausruestung')">Ausrüstung</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=skilltree')">
                    <span style="color: <?php if($player->GetSkillpoints() != 0) echo 'red'; else echo 'white'; ?>;">Skilltree</span>
                </div>
                <?php
                if ($player->GetStats() != 0)
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="OpenPopupPage('Statspunkte Verteilen','skill/edit.php');">
                                <span style="color: red;">
                                    Statspunkte:
                                    <?php echo number_format($player->GetStats(),'0', '', '.'); ?>
                                </span>
                    </div>
                    <?php
                }
                ?>
                <?php
            }
            else
            {
                ?>
                <a href="?p=profil" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Profil</div>
                </a>
                <a href="?p=inventar" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Inventar</div>
                </a>
                <a href="?p=ausruestung" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Ausrüstung</div>
                </a>
                <a href="?p=skilltree" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">
                        <span style="color: <?php if($player->GetSkillpoints() != 0) echo 'red'; else echo 'white'; ?>;">Skilltree</span>
                    </div>
                </a>
                <?php
                if ($player->GetStats() != 0)
                {
                    ?>
                    <a href="#" id="no-link" onclick="OpenPopupPage('Statspunkte Verteilen','skill/edit.php')">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">
                                <span style="color: red;">
                                    Statspunkte: <?php echo number_format($player->GetStats(),'0', '', '.'); ?>
                                </span>
                        </div>
                    </a>
                    <?php
                }
                ?>

                <?php

            }
            ?>
        </div>
        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                <img width="160" alt="Kommunikation" title="Kommunikation" src="<?php echo $serverUrl; ?>img/marketing/onepiecepostbild.png" />
            </div>
            <?php
            if($player->GetArank() < 2)
            {
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=pm')">
                        <span style="color: <?php if($PMManager->GetUnreadPMs() > 0) echo 'red'; else echo 'white'; ?>;">
                            <?php
                            echo 'Teleschnecke: ' . number_format($PMManager->GetUnreadPMs(),'0', '', '.');
                            ?>
                        </span>
                </div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=pm2')">
                        <span style="color: <?php if($PMManager->GetSystemPMs() > 0) echo 'red'; else echo 'white'; ?>;">
                            <?php
                            echo 'System: ' . number_format($PMManager->GetSystemPMs(),'0', '', '.');
                            ?>
                        </span>
                </div>
                <?php
            }
            else
            {
                ?>
                <a href="?p=pm" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">
                            <span style="color: <?php if($PMManager->GetUnreadPMs() > 0) echo 'red'; else echo 'white'; ?>;">
                                Teleschnecke: <?php echo number_format($PMManager->GetUnreadPMs(),'0', '', '.'); ?>
                            </span>
                    </div>
                </a>
                <a href="?p=pm2" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">
                            <span style="color: <?php if($PMManager->GetSystemPMs() > 0) echo 'red'; else echo 'white'; ?>;">
                                    System: <?php echo number_format($PMManager->GetSystemPMs(),'0', '', '.'); ?>
                            </span>
                    </div>
                </a>
                <?php
            }
            ?>
        </div>
        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                <img width="160" alt="Ort" title="Ort" src="<?php echo $serverUrl; ?>img/marketing/onepieceortsbild.png" />
            </div>
            <?php
            if($player->GetClan() != 0)
            {
                $group = $player->GetGroup();

                $weekday = date("l");
                if($weekday == "Saturday" || $weekday == "Sunday")
                {
                    $min = '12';
                    $max = '22';
                }
                else
                {
                    $min = '16';
                    $max = '22';
                }

                $groupcheck = false;
                if($group != null && count($group) == 3)
                {

                    foreach ($group as $groupmember) {
                        $groupplayer = new Player($database, $groupmember);
                        if($groupplayer->GetClan() != $player->GetClan())
                            $groupcheck = false;
                        if($groupplayer->GetPlace() != $player->GetPlace())
                            $groupcheck = false;
                    }

                    if(!$groupcheck)
                        $groupcheck = !$groupcheck;
                }
                if($place != null && $groupcheck && $place->IsEarnable() && ($place->GetTerritorium() == 0 || $place->GetTerritorium() != $player->GetClan()) && date('H') >= $min && date('H') < $max)
                {
                    if($player->GetArank() < 2)
                    {
                        ?>
                        <div class="SideMenuStyle borderB" onmousedown="OpenPopupPage('Ort beanspruchen','map/fight.php');">
                            Ort beanspruchen
                        </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <a href="#" id="no-link" onclick="OpenPopupPage('Ort beanspruchen','map/fight.php');">
                            <div style="cursor:pointer;" class="SideMenuButton borderB">
                                Ort beanspruchen
                            </div>
                        </a>
                        <?php
                    }
                }
            }
            if($player->GetArank() < 2)
            {
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=story')">Story</div>
                <?php
                if ($player->GetARank() >= 2)
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=sidestory')">Neben-Story</div>
                    <?php
                }
                if($player->GetLevel() >= 13)
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=specialtraining')">Trainer</div>
                    <?php
                }
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=training')">Aktionen</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=techtraining')">Technik</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=raceaction')">Spezialfähigkeiten</div>
                <?php
            }
            else
            {
                ?>
                <a href="?p=story" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Story</div>
                </a>
                <?php
                if ($player->GetARank() >= 2)
                {
                    ?>
                    <a href="?p=sidestory" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">Neben-Story</div>
                    </a>
                    <?php
                }
                ?>
                <a href="?p=specialtraining" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Trainer</div>
                </a>
                <a href="?p=training" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Aktionen</div>
                </a>
                <a href="?p=techtraining" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Technik</div>
                </a>
                <a href="?p=raceaction" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Spezialfähigkeiten</div>
                </a>
                <?php
            }
            if ($player->GetPlanet() == 2)
            {
                if($player->GetArank() < 2)
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=revive')">Befreiung</div>
                    <?php
                }
                else
                {
                    ?>
                    <a href="?p=revive" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">Befreiung</div>
                    </a>
                    <?php
                }
            }
            ?>
        </div>
        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                <img width="160" alt="Handel" title="Handel" src="<?php echo $serverUrl; ?>img/marketing/Bannerbildhandel.png" />
            </div>
            <?php
            if($player->GetArank() < 2)
            {
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=shop')">Shop</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=goldshop')">Gold-Shop</div>
                <?php
                if($halloweenEventActive)
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=eventshop')">Halloween-Shop</div>
                    <?php
                }
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=werkstatt')">Werkstatt</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=exchangeshop')">Tauschshop</div>
                <?php
                if ($player->GetPlanet() != 2 && $player->GetLevel() >= 5)
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=market')">Marktplatz</div>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=blackboard')">Blackboard</div>
                    <?php
                }
                if($player->GetPlace() == 45 && $player->GetInventory()->HasGanfortKey())
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=ganfortshop')">Gan Forts Kleiderschrank</div>
                    <?php
                }
                if($player->GetLevel() >= 5)
                {
                  //Casino
                    ?>
                    <?php
                }
            }
            else
            {
                ?>
                <a href="?p=shop" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Shop</div>
                </a>
                <a href="?p=goldshop" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Gold-Shop</div>
                </a>
                <?php
                if($halloweenEventActive)
                {
                    ?>
                    <a href="?p=eventshop" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">Halloween-Shop</div>
                    </a>
                    <?php
                }
                ?>
                <a href="?p=werkstatt" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Werkstatt</div>
                </a>
                <a href="?p=exchangeshop" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Tauschshop</div>
                </a>
                <?php
                if ($player->GetPlanet() != 2)
                {
                    ?>
                    <a href="?p=market" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">Marktplatz</div>
                    </a>
                    <a href="?p=blackboard" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">Blackboard</div>
                    </a>
                    <?php
                }
                if($player->GetPlace() == 45 && $player->GetInventory()->HasGanfortKey())
                {
                    ?>
                    <a href="?p=ganfortshop" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">Gan Forts Kleiderschrank</div>
                    </a>
                    <?php
                }
              //Casino
                ?>
                <?php
            }
            ?>
        </div>
        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                <img width="160" alt="Arena" title="Arena" src="<?php echo $serverUrl; ?>img/marketing/onepiecearenabild.png" />
            </div>
            <?php
            if($player->GetArank() < 2)
            {
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=npc')">NPC</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=event')">Event</div>
                <?php
                if($player->GetLevel() >= 5)
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=arena')">Kolosseum</div>
                    <?php
                }
                if($player->GetCapchaCount() >= 20)
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="OpenPopupPage('Captcha Kolosseum','kolocapcha/kolocapcha.php');"><span style="color: red;">Captcha Kolosseum</span></div>
                    <?php
                }
                ?>
                <?php
                if (isset($fight) && $fight->IsValid() && $fight->GetID() == $player->GetFight() && $fight->IsStarted())
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=infight')"><span style="color:red;">Kampf</span></div>
                    <?php
                }
                else
                {
                    $text = '';
                    $list = new Generallist($database, 'fights', '*', 'type=1 AND state=0 AND testfight=0');
                    $list2 = new Generallist($database, 'fights', '*', 'type=13 AND state=0 AND testfight=0');
                    $id = 0;
                    $id2 = 0;
                    $entry2 = $list2->GetEntry($id2);
                    $entry = $list->GetEntry($id);
                    if($entry != null || $entry2 != NULL)
                        $text = '<span style="color: red;"> (*)</span>';
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=fight')">Kämpfe<?php echo $text; ?></div>

                    <?php
                }
                if($player->GetLevel() >= 6)
                {
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=boss')">Dungeon</div>
                    <?php
                }
                ?>
                <!--<div class="SideMenuStyle borderB" onmousedown="location.replace('?p=tournament')">Turnier</div>-->
                <?php
            }
            else
            {
                ?>
                <a href="?p=npc" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">NPC</div>
                </a>
                <a href="?p=event" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Event</div>
                </a>
                <a href="?p=arena" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Kolosseum</div>
                </a>
                <?php
                if($player->GetCapchaCount() >= 20)
                {
                    ?>
                    <a href="#" id="no-link">
                        <div class="SideMenuStyle borderB" onclick="OpenPopupPage('Captcha Kolosseum','kolocapcha/kolocapcha.php');">
                            <span style="color: red;">Captcha Kolosseum</span>
                        </div>
                    </a>
                    <?php
                }
                ?>
                <?php
                if (isset($fight) && $fight->IsValid() && $fight->GetID() == $player->GetFight() && $fight->IsStarted())
                {
                    ?>
                    <a href="?p=infight" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">
                                    <span style="color: red;">
                                        Kampf
                                </span>
                        </div>
                    </a>
                    <?php
                }
                else
                {
                    $text = '';
                    $list = new Generallist($database, 'fights', '*', 'type=1 AND state=0 AND testfight=0');
                    $id = 0;
                    $entry = $list->GetEntry($id);
                    if($entry != null)
                        $text = '<span style="color: red;"> (*)</span>';
                    ?>
                    <a href="?p=fight" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">Kämpfe<?php echo $text; ?></div>
                    </a>
                    <?php
                    if($player->GetMirror() == 1)
                    {
                        ?>
                        <a href="#" id="no-link">
                            <div class="SideMenuStyle borderB" onclick="OpenPopupPage('Spiegelkampf','fight/mirror.php');">
                                <span style="color: red;">Spiegelkampf</span>
                            </div>
                        </a>
                        <?php
                    }
                }
                ?>
                <a href="?p=boss" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Dungeon</div>
                </a>
                <?php
            }
            ?>
        </div>
        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                <img alt="Reisen" title="Reisen" width="160" src="<?php echo $serverUrl; ?>img/marketing/onepiecereisebild.png" />
            </div>
            <?php
            if($player->GetArank() < 2)
            {
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=map')">Karte</div>
                <?php
                if ($player->GetPlanet() != 2)
                {
                    if($player->GetPlanet() != 4)
                    {
                        ?>
                        <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=shiptravel')">Schiff</div>
                        <?php
                    }
                    ?>
                    <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=treasurehunt')">Schatzsuche</div>
                    <?php
                }
                ?>
                <?php
            }
            else
            {
                ?>
                <a href="?p=map" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Karte</div>
                </a>
                <?php
                if ($player->GetPlanet() != 2)
                {
                    ?>
                    <a href="?p=shiptravel" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">Schiff</div>
                    </a>
                    <a href="?p=treasurehunt" id="no-link">
                        <div style="cursor:pointer;" class="SideMenuButton borderB">Schatzsuche</div>
                    </a>
                    <?php
                }
                ?>
                <?php
            }
            ?>
        </div>
        <?php
        if(!$player->IsMultiChar())
        {
        if ($player->GetLevel() >= 5 && ($player->GetPlanet() != 2 || ($clan != null && $clan->GetChallengeCountdown() > 0)))
        {
            ?>
            <div class="spacer"></div>
            <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                <img alt="Bande" title="Bande" width="160" src="<?php echo $serverUrl; ?>img/marketing/onepiececlanbild.png" />
            </div>
            <?php
        }
        if ($player->GetPlanet() != 2 && $player->GetClan() == 0 && $player->GetLevel() >= 5)
        {
            if($player->GetArank() < 2)
            {
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=clancreate')">Bande Erstellen</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=clanjoin')">Bande Beitreten</div>
                <?php
            }
            else
            {
                ?>
                <a href="?p=clancreate" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Bande Erstellen</div>
                </a>
                <a href="?p=clanjoin" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Bande Beitreten</div>
                </a>
                <?php
            }
        }
        else if($player->GetPlanet() != 2 && $player->GetLevel() >= 5)
        {
            if($player->GetArank() < 2)
            {
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=clan&id=<?php echo $player->GetClan(); ?>')">Banden Profil</div>
                <?php
                $hasApplicants = false;
                $text = '';
                if ($clan->GetRankPermission($player->GetClanRank(), 'management'))
                {
                    $hasApplicants = $clan->HasApplicants();
                }
                if($player->GetLastTimeClanVisited() < $clan->GetLastUpdate())
                {
                    $text = '<span style="color: red;">(*)</span>';
                }
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=clanmanage')">
                    <span style="color: <?php if($hasApplicants) echo 'red'; else echo 'white'; ?>;">Banden Verwaltung <?php echo $text; ?></span>
                </div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=bandentournament')">Banden Tournament</div>
                <?php
            }
            else
            {
                ?>
                <a href="?p=clan&id=<?php echo $player->GetClan(); ?>" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Banden Profil</div>
                </a>
                <?php
                $hasApplicants = false;
                $text = '';
                if ($clan->GetRankPermission($player->GetClanRank(), 'management'))
                {
                    $hasApplicants = $clan->HasApplicants();
                }
                if($player->GetLastTimeClanVisited() < $clan->GetLastUpdate())
                {
                    $text = '<span style="color: red;">(*)</span>';
                }
                ?>
                <a href="?p=clanmanage" class="no-link">
                    <div style="cursor: pointer;" class="SideMenuButton borderB">
                        <span style="color: <?php if($hasApplicants) echo 'red'; else echo 'white'; ?>;">Banden Verwaltung <?php echo $text; ?></span>
                    </div>
                </a>
                <a href="?p=bandentournament" class="no-link">
                    <div style="cursor: pointer;" class="SideMenuButton borderB">
                        <span style="color: white;">
                            Banden Tournament
                        </span>
                    </div>
                </a>
                <a href="?p=summerevent" class="no-link">
                    <div style="cursor: pointer;" class="SideMenuButton borderB">
                        <span style="color: white;">
                           Sommer Event
                        </span>
                    </div>
                </a>
                <?php
            }
        }
        if($clan != null && $clan->GetChallengeCountdown() > 0 && $clan->GetChallengeFight() != 0 && $player->GetLevel() >= 5)
        {
            ?>
            <div style="height: 100%; width: 100%;" class="SideMenuStyle borderB" onmousedown="OpenPopupPage('Herausforderung', 'clan/challenged.php');">
                Herausforderung:
                <div id="challenge">
                    <script>
                        countdown(<?php echo $clan->GetChallengeCountdown(); ?>, 'challenge');
                    </script>

                </div>
            </div>
            <?php
        }
        if ($player->GetLevel() >= 5 && $player->GetPlanet() != 2 || ($clan != null && $clan->GetChallengeCountdown() > 0))
        {
            ?>
            </div>
            <?php
        }
        }
        ?>
        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                <img alt="Community" title="Community" width="160" src="<?php echo $serverUrl; ?>img/marketing/onepiececommunitibild.png" />
            </div>
            <?php
            if($player->GetArank() < 2)
            {
                ?>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=user')">Userliste</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=ranglist')">Kampfstatistik</div>
                <div class="SideMenuStyle borderB" onmousedown="location.replace('?p=opwxminigames')">Minispiele</div>
                <?php
            }
            else
            {
                ?>
                <a href="?p=user" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Userliste</div>
                </a>
                <a href="?p=ranglist" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Kampfstatistik</div>
                </a>
                <a href="?p=opwxminigames" id="no-link">
                    <div style="cursor:pointer;" class="SideMenuButton borderB">Minispiele</div>
                </a>
                <?php
            }
            ?>
        </div>
        <?php
        $wartung = false;
        if ($wartung)
        {
            ?>
            <div class="spacer"></div>
            <div class="SideMenuContainer borderL borderR">
                <div class="SideMenuKat catGradient borderB borderT">
                    <div class="schatten">Wartungsarbeiten</div>
                </div>
                <img alt="Wartungsarbeiten" title="Wartungsarbeiten" src="<?php echo $serverUrl; ?>/img/wartungsarbeiten.gif" width="140" height="100">
                <span style="color: red;"><b>WENN CHAT BUGGT BITTE PM SENDEN!</b></span>
            </div>
            <?php
        }
        ?>
        <div class="spacer"></div>
        <?php
    }
    else
    {
        ?>
        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                Serverzeit
            </div>
            <div class="SideMenuInfo borderB borderR">
                <?php
                echo date("d.m.Y H:i:s", time());
                ?>
            </div>
        </div>

        <div class="spacer"></div>
        <div class="SideMenuContainer borderL borderR">
            <div class="SideMenuKat catGradient borderB borderT">
                Screenshots
            </div>
            <div class="SideMenuInfo borderB borderR">
                <table height="100%" width="100%">
                    <?php
                    $screenUrl = $serverUrl.'img/screens/screenshot_';
                    $screenExt = '.png';
                    $cols = 4;
                    $rows = 3;
                    $width = 50;
                    $height = 50;

                    $screenArray = [];
                    for ($i = 1; $i <= 10; ++$i)
                    {
                        $screenArray[] = $i;
                    }
                    for ($col = 1; $col <= $cols; ++$col)
                    {
                    ?><tr><?php
                        for ($row = 1; $row <= $rows; ++$row)
                        {
                            $screenPos = rand(0, count($screenArray) - 1);
                            $screenID = $screenArray[$screenPos];
                            if ($screenID != '')
                            {
                                array_splice($screenArray, $screenPos, 1);
                                $screenImg = $screenUrl . $screenID . $screenExt;
                                echo '<td style="align:center;"><a href="' . $screenImg . '" target="_blank"><img width="' . $width . 'px" height="' . $height . 'px" src="' . $screenImg . '"></a></td>';
                            }
                        }
                        echo '</tr>';
                        }
                        ?>
                </table>
            </div>
        </div>
        <div class="spacer"></div>
        <div class="spacer"></div>
        <?php
    }
    ?>
</div>