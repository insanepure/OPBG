<?php
if (isset($fight) && $fight->IsEnded())
{
    ?>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb', {
                action: 'Kampf'
            });
        });
    </script>
    <div class="spacer"></div>
    <?php
    $day = date("w");
    $event = new Event($database, $fight->GetEvent());
    if($fight->GetType() == 8)
        $dir = 'arena';
    else if ($fight->GetType() == 12)
        $dir = 'treasurehunt';
    else if ($fight->GetType() != 3 && $fight->GetType() != 5)
        $dir = 'fight';
    else if ($fight->GetType() == 3)
        $dir = 'npc';
    else if ($fight->GetType() == 5 && $event->IsDungeon())
        $dir = 'boss';
    else if ($fight->GetType() == 5 && !$event->IsDungeon())
        $dir = 'event';
    ?>
    <div class="exitFight" onmousedown="location.replace('?p=<?php echo $dir; ?>')">Zurück</div>
    <?php
    $fighters = $fight->GetFighters();
    $fighters = str_replace('[', '', $fighters);
    $fighters = str_replace(']', '', $fighters);
    $fighters = explode(';', $fighters);

    if($dir == 'npc' && $player->GetLastNPCID() != 0 && $player->GetLastFight() == $fight->GetID())
    {
        ?>
        <div class="spacer"></div>
        <div class="exitFight" onmousedown="location.replace('?p=infight&start=lastfight&id=<?= $player->GetLastNPCID(); ?>&type=3')">Nochmal</div> <?php echo "<br />Tägliche NPC Kämpfe: ".$player->GetDailyNPCFights()." / ".$player->GetDailyNPCFightsMax(); ?><br />
        <?php
        $TitelManager = new TitelManager($database);
        $titels = $TitelManager->GetTitelsOfNPC($player->GetLastNPCID(), 3);
        $lowtitel = null;
        $i = 0;
        for ($i = 0; $i < count($titels); ++$i)
        {
            $titel = $titels[$i];
            if (!$player->HasTitel($titel->GetID()) && $titel->IsVisible())
            {
                if ($lowestTitel == null || $lowestTitel->GetCondition() > $titel->GetCondition())
                    $lowestTitel = $titel;
            }
        }

        if ($lowestTitel != null && $player->IsDonator() && $lowestTitel->IsVisible())
        {
            $titelProgress = $TitelManager->LoadProgress($player->GetID(), $lowestTitel->GetID());
            $progress = 0;
            if (isset($titelProgress))
                $progress = $titelProgress['progress'];
            ?>
            <div class="expback" style="height:20px; width:90%;">
                <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                <div class="expanzeige" style="width:<?php echo $progress / $lowestTitel->GetCondition() * 100 ; ?>%"></div>
                <div class="exptext">
                    Nächster Titel: <?php echo number_format($progress,'0', '', '.') . ' / ' . number_format($lowestTitel->GetCondition(),'0', '', '.'); ?>
                </div>
            </div>
            <div class="spacer"></div>
            Nächster Titel: <font color="<?= $lowestTitel->GetColor(); ?>"><?= $lowestTitel->GetName(); ?></font>
            <br />
            <img src="<?= $lowestTitel->GetTitelPic(); ?>" />
            <?php
        }
    }
    else if($dir == 'arena' && $day == 3 && $player->GetCapchaCount() < 20 && $player->GetLastFight() == $fight->GetID() && $player->IsKoloPlayer())
    {
        ?>
        <div class="spacer"></div>
        <div class="exitFight" onmousedown="location.replace('?p=infight&start=lastfight&id=<?= $player->GetLastNPCID(); ?>&type=8')">Nochmal</div><br />
        <?php
        $TitelManager = new TitelManager($database);
        $titels = $TitelManager->GetTitelsOfNPC($player->GetLastNPCID(), 3);
        $lowtitel = null;
        $i = 0;
        for ($i = 0; $i < count($titels); ++$i)
        {
            $titel = $titels[$i];
            if (!$player->HasTitel($titel->GetID()) && $titel->IsVisible())
            {
                if ($lowestTitel == null || $lowestTitel->GetCondition() > $titel->GetCondition())
                    $lowestTitel = $titel;
            }
        }

        if ($lowestTitel != null && $player->IsDonator() && $lowestTitel->IsVisible())
        {
            $titelProgress = $TitelManager->LoadProgress($player->GetID(), $lowestTitel->GetID());
            $progress = 0;
            if (isset($titelProgress))
                $progress = $titelProgress['progress'];
            ?>
            <div class="expback" style="height:20px; width:90%;">
                <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                <div class="expanzeige" style="width:<?php echo $progress / $lowestTitel->GetCondition() * 100 ; ?>%"></div>
                <div class="exptext">
                    Nächster Titel: <?php echo number_format($progress,'0', '', '.') . ' / ' . number_format($lowestTitel->GetCondition(),'0', '', '.'); ?>
                </div>
            </div>
            <div class="spacer"></div>
            Nächster Titel: <font color="<?= $lowestTitel->GetColor(); ?>"><?= $lowestTitel->GetName(); ?></font>
            <br />
            <img src="<?= $lowestTitel->GetTitelPic(); ?>" />
            <?php
        }

    }
    else if(($dir == 'event' || $dir == 'boss') && in_array($player->GetID(), $fighters) && $event != null)
    {
        ?>
        <div class="spacer"></div>
        <form method="post" action="?p=<?php echo $dir; ?>&a=start">
            <input type="hidden" name="id" value="<?php echo $event->GetID(); ?>">
            <input type="hidden" name="players" value="<?php echo $fight->GetMode()[0]; ?>">
            <input class="exitFight" type="submit" value="Nochmal">
            <?php
            if($dir == 'event')
            {
                $wins = 0;
                $playersData = $event->GetFinishedPlayers();
                foreach ($playersData as $players) {
                    if ($players[0] == $player->GetID())
                    {
                        $wins = $players[1];
                        break;
                    }
                }
                echo '<div class="spacer"></div>';
                if ($event->GetDailyreset() == 1)
                    echo 'Täglich: ';
                else
                    echo 'Maximal: ';
                echo "<b>" . number_format($wins, '0', '', '.') . "</b> / <b>" . number_format($event->GetWinable(), '0', '', '.') . "</b>";
            }
            else if($dir == 'boss')
            {
                if($event->GetID() != 18 && $event->GetID() != 23 && $event->GetID() != 11 && $event->GetID() != 20)
                    $wins = $player->GetDungeon();
                else {
                    if($event->GetID() == 18 && $player->GetExtraDungeon(18) > $player->GetDungeon())
                        $wins = $player->GetExtraDungeon(18);
                    else if($event->GetID() == 20 && $player->GetExtraDungeon(20) > $player->GetDungeon())
                        $wins = $player->GetExtraDungeon(20);
                    else if($event->GetID() == 15 && $player->GetExtraDungeon(15) > $player->GetDungeon())
                        $wins = $player->GetExtraDungeon(15);
                    else if($event->GetID() == 23 || $event->GetID() == 11)
                    {
                        $playersData = $event->GetFinishedPlayers();
                        foreach ($playersData as $players) {
                            if ($players[0] == $player->GetID())
                            {
                                $wins = $players[1];
                                break;
                            }
                        }
                    }
                    else
                        $wins = $player->GetDungeon();
                }
                ?>
                <?php
                echo '<div class="spacer"></div>';
                if ($event->GetDailyreset() == 1)
                    echo 'Täglich: ';
                else
                    echo 'Maximal: ';
                ?>
                <?php
                echo '<b>'.number_format($wins, '0', '', '.') . '</b> / <b>' . number_format($event->GetWinable(), '0', '', '.') . '</b>';
                if($player->GetExtraDungeon(18) < $player->GetDungeon() && $player->GetDungeon() == 10 && $event->GetID() == 18)
                {
                    echo '<div class="spacer"></div>';
                    echo "<br/>Du hast die maximale Anzahl an täglichen<br/>Dungeonkämpfe bereits aufgebraucht, du erhältst<br/>heute keine Stats mehr aus diesen Dungeon.";
                }
                else if($player->GetExtraDungeon(20) < $player->GetDungeon() && $player->GetDungeon() == 10 && $event->GetID() == 20)
                {
                    echo '<div class="spacer"></div>';
                    echo "<br/>Du hast die maximale Anzahl an täglichen<br/>Dungeonkämpfe bereits aufgebraucht, du erhältst<br/>heute keine Stats mehr aus diesen Dungeon.";
                }
                else if($player->GetExtraDungeon(15) < $player->GetDungeon() && $player->GetDungeon() == 10 && $event->GetID() == 15)
                {
                    echo '<div class="spacer"></div>';
                    echo "<br/>Du hast die maximale Anzahl an täglichen<br/>Dungeonkämpfe bereits aufgebraucht, du erhältst<br/>heute keine Stats mehr aus diesen Dungeon.";
                }
            }
            ?>
            <div class="spacer"></div>
            <?php
            $typefight = 5; // Event
            $titelManager = new TitelManager($database);
            $npcdata = explode('@', $event->GetFights());
            $lowestTitel = null;
            if(count($npcdata) > 1)
            {
                for ($i = 0; $i < count($npcdata); $i++)
                {
                    $npcraw = $npcdata[$i];
                    $npc = explode(";", $npcraw);
                    if(strpos($npc[0], ':'))
                    {
                        $npcs = explode(':',$npc[0]);
                        for($j = 0; $j < count($npcs); $j++)
                        {
                            $titels = $titelManager->GetTitelsOfNPC($npcs[$j], $typefight);
                            for ($k = 0; $k < count($titels); ++$k)
                            {
                                $titel = $titels[$k];

                                if (!$player->HasTitel($titel->GetID()))
                                {
                                    if ($lowestTitel == null || $lowestTitel->GetCondition() > $titel->GetCondition())
                                        $lowestTitel = $titel;
                                }
                            }
                        }
                    }
                    else
                    {
                        $titels = $titelManager->GetTitelsOfNPC($npc[0], $typefight);
                        $j = 0;
                        for ($j = 0; $j < count($titels); ++$j)
                        {
                            $titel = $titels[$j];
                            if (!$player->HasTitel($titel->GetID()))
                            {
                                if ($lowestTitel == null || $lowestTitel->GetCondition() > $titel->GetCondition())
                                    $lowestTitel = $titel;
                            }
                        }
                    }
                }
            }
            else
            {
                $npcraw = $npcdata[0];
                $npcs = explode(":", $npcraw);
                foreach ($npcs as $npc) {
                    $titels = $titelManager->GetTitelsOfNPC(explode(';', $npc)[0], $typefight);
                    for ($i = 0; $i < count($titels); ++$i)
                    {
                        $titel = $titels[$i];
                        if (!$player->HasTitel($titel->GetID()))
                        {
                            if ($lowestTitel == null || $lowestTitel->GetCondition() > $titel->GetCondition())
                                $lowestTitel = $titel;
                        }
                    }
                }
            }
            if ($lowestTitel != null)
            {
                $titelProgress = $titelManager->LoadProgress($player->GetID(), $lowestTitel->GetID());
                $progress = 0;
                if (isset($titelProgress))
                    $progress = $titelProgress['progress'];
                ?>
                <div class="expback" style="height:20px; width:90%;">
                    <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                    <div class="expanzeige" style="width:<?php echo $progress / $lowestTitel->GetCondition() * 100 ; ?>%"></div>
                    <div class="exptext">
                        Nächster Titel: <?php echo number_format($progress,'0', '', '.') . ' / ' . number_format($lowestTitel->GetCondition(),'0', '', '.'); ?>
                    </div>
                </div>
                <div class="spacer"></div>
                Nächster Titel: <font color="<?= $lowestTitel->GetColor(); ?>"><?= $lowestTitel->GetName(); ?></font>
                <br />
                <img src="<?= $lowestTitel->GetTitelPic(); ?>" />
                <?php
            }
            ?>
        </form>
        <?php
    }
    ?>
    <div class="spacer"></div>
    <?php
}
if (isset($fight) && $fight->GetID() == $player->GetFight() && !$fight->IsEnded())
{
    ?>
    <div class="spacer"></div>
    <div class="fightBox boxSchatten smallBG">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">Aktionen</div>
        </div>
        <div class="spacer"></div>
        <div class="attacks">
            <div class="spacer"></div>
            <?php
            if ($pFighter->GetAction() == 0 && $pFighter->GetLP() != 0 && !$pFighter->HasNPCControl())
            {
            if($fight->IsTestFight())
            {
                ?>
                <form action="?p=infight&a=deleteFight" method="post">
                    <input type="submit" value="Kampf Löschen">
                </form>
            <br/>
                <?php
            }
                ?>
                <form id="captcha" action="?p=infight&a=attack&code=<?php echo $pFighter->GetAttackCode(); ?>&fight=<?php echo $fight->GetID(); ?>" method="post">
                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                    <input type="hidden" name="action" value="validate_captcha">
                    <select class="select" style="min-width:300px;" name="target">
                        <?php
                        $i = 0;
                        $selected = false;

                        $pTarget = $pFighter->GetPreviousTarget();
                        while (isset($teams[$i]))
                        {
                            $players = $teams[$i];
                            $j = 0;
                            while (isset($players[$j]))
                            {
                                $fighter = $players[$j];
                                if ($pFighter->GetTeam() != $fighter->GetTeam() && $fighter->GetLP() == 0 && $fighter->GetID() == $pTarget || $fight->GetFighter($pTarget) == null)
                                {
                                    $pTarget = 0;
                                }
                                ++$j;
                            }
                            ++$i;
                        }
                        $i = 0;
                        while (isset($teams[$i]))
                        {
                            $players = $teams[$i];
                            $j = 0;
                            while (isset($players[$j]))
                            {
                                $fighter = $players[$j];
                                if ($fighter->IsInactive() || $pFighter->GetTeam() != $fighter->GetTeam() && $fighter->GetLP() == 0)
                                {
                                    ++$j;
                                    continue;
                                }
                                ?>
                                <option value="<?php echo $fighter->GetID(); ?>" <?php
                                if ($pTarget == $fighter->GetID() || $pTarget == 0 && !$selected && $fighter->GetTeam() != $pFighter->GetTeam() && $fighter->GetLP() != 0)
                                {
                                    ?> selected
                                    <?php
                                    $selected = true;
                                }
                                ?>><?php echo $fighter->GetName(); ?></option>
                                <?php
                                $j++;
                            }
                            ++$i;
                        }
                        ?>
                    </select>
                    <div class="spacer"></div>
                    <?php
                    $i = 0;
                    $attacks = explode(';', $pFighter->GetAttacks());

                    while (isset($attacks[$i]))
                    {
                        $attack = $fight->GetAttack($attacks[$i]);
                        if($attack->GetType() == 4 && $player->GetArank() < 2)
                        {
                            $i++;
                            continue;
                        }

                        $targetcheck = $database->Select('level', 'fighters', 'fight="'.$fight->GetID().'" AND team != "'.$pFighter->GetTeam().'"', 1);
                        $target = $targetcheck->fetch_assoc();
                        if(
                            (
                                $attack->GetID() != 2 || // Aufgeben ab Runde 30
                                $attack->GetID() == 2 &&
                                $fight->GetRound() >= 30
                            ) &&
                            (
                                $attack->GetType() != 9 || // Stun in Story und Bandenkampf verbieten
                                $attack->GetType() == 9 &&
                                $fight->GetType() != 4 &&
                                $fight->GetType() != 11
                            ) &&
                            (
                                $attack->GetType() != 3 || // KillTech ab Target-Level 18
                                $attack->GetType() == 3 &&
                                $target['level'] > 18
                            ) &&
                            (
                                $attack->GetID() != 4 || // Verteidigung ohne Mirror-Fight
                                $attack->GetID() == 4 &&
                                $fight->IsMirror() == 0
                            ) &&
                            (
                                $attack->GetType() != 3 ||
                                $attack->GetType() == 3 && // Kill Techs im Impeldown heraus verbieten
                                $player->GetPlanet() != 2
                            )
                        )
                        {
                            ?>
                            <div class="tooltip" style="height:55px;">
                                <button type="submit" class="attackButton boxSchatten" name="attack" id="attack<?= $attack->GetID(); ?>" value="<?php echo $attack->GetID(); ?>">
                                    <input type="hidden" name="kampfid" value="<?php echo $fight->GetID(); ?>">
                                    <img src="<?php echo $attack->GetImage(); ?>" class="attack" <?php if(($pFighter->GetKP() < $attack->GetKP() && !$attack->IsKPProcentual()) || ($attack->IsKPProcentual() && $pFighter->GetKPPercentage() < $attack->GetKP()) || ($pFighter->GetRemainingEnergy() < $attack->GetEnergy())) echo 'style=" filter: gray; -webkit-filter: grayscale(1); filter: grayscale(1);"'; ?>>
                                </button>
                                <span class="tooltiptext" style="position:absolute; z-index:5; width:220px; bottom:64px; left: -22px;">
                                                <?php echo $attack->GetName(); ?>
                                                <span class="attackInfo">
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

                                                            echo$attack->GetValue() * $attack->GetTauntValue() / 100;
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

                                                            echo$attack->GetValue() * $attack->GetReflectValue() / 100;
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

                                                            echo$attack->GetValue() * $attack->GetAccBuff() / 100;
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

                                                            echo$attack->GetValue() * $attack->GetReflexBuff() / 100;
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
                                            </span>
                            </div>
                            <?php
                        }
                        ++$i;
                    }
                    ?>
                </form>
                <div class="spacer"></div>
            <?php
            }
            else
            {
            ?>
                Warte auf andere Spieler.<br /><script>setTimeout(location.reload.bind(location), 2000);</script>
                <?php
            }
            ?>
        </div>
        <div class="spacer"></div>
        <div class="ShowTooltips" style="position:relative; top:-18px; float:right;">
            <div class="tooltip" style="z-index: 10;">
                <input type="checkbox" id="ShowTooltips" onchange="localStorage.setItem('ShowAttackInfo', this.checked); ToggleInfos();">
                <span class="tooltiptext" style="width:auto; height: auto; bottom:35px; left:-7px;">Attackeninformationen anzeigen?</span>
            </div>
        </div>
    </div>
    <div class="spacer"></div>
    <?php
}
else
{
    if (!$fight->IsEnded())
    {
        if ($player->GetFight() == $fight->GetID() && $fight->IsStarted())
        {

        }
        else
            echo '<div class="spacer"></div>';
        echo '<script>setTimeout(location.reload.bind(location), 2000);</script>';
    }
}
?>
<div class="spacer"></div>
<div class="spacer"></div>
<div class="fightBox boxSchatten smallBG">
    <div class="SideMenuKat catGradient borderB">
        <div class="schatten">Verlauf</div>
    </div>
    <img src="img/marketing/infightarena.png">
    <table width="100%">
        <?php echo stripcslashes($fight->GetText()); ?>
    </table>
    <div class="spacer"></div>
</div>

<div class="spacer"></div>
<?php
if($fight->GetMeldeCount() == 0)
{
    ?>
    Die Melde-Funktion ist nur zum Melden von Fehlern oder Verstößen, das unbegründete Melden kann ebenfalls geahndet werden!
    <div class="spacer"></div>
    <button onclick="OpenPopupPage('Melden','fight/melden.php?id=<?php echo $fight->GetID();?>')">Melden</button>
    <?php
}
?>
<script>
    function ToggleInfos() {
        var show = 'none';
        document.getElementById('ShowTooltips').checked = false;
        if(localStorage.getItem('ShowAttackInfo') == 'true')
        {
            show = 'block';
            document.getElementById('ShowTooltips').checked = true;
        }
        for(var i = 0; i< document.getElementsByClassName('attackInfo').length; i++)
        {
            document.getElementsByClassName('attackInfo')[i].style.display = show;
        }
    }
    ToggleInfos();
</script>
