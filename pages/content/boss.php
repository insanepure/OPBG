<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb', {
            action: 'Dungeon'
        });
    });
</script>
<div class="spacer"></div>
<div class="spacer"></div>
<?php
$events = new Generallist($database, 'events', '*', 'isdungeon="1"', '', '9999', 'ASC');
$itemManager = new ItemManager($database);
?>
<p style="text-align: center">
    Tägliche Dungeons: <?php echo $player->GetDungeon(); ?>/10
</p>
<?php
$id = 0;
$entry = $events->GetEntry($id);
while ($entry != null)
{
    $kampfanzahl = count(explode('@', $entry['fights']));
    if ($kampfanzahl == 1)
    {
        $gegneranzahl = count(explode(':', $entry['fights']));
        $text = $gegneranzahl . " Gegner";
    }
    else
    {
        $text = $kampfanzahl . " Runden";
    }
    //Erde;Wald;1:2:3:4:5;1-31;1-12;0-365;2018-3000
    $pandts = explode('@', $entry['placeandtime']);
    $i = 0;
    $weekDay = date('N');
    $monthDay = date('j');
    $yearDay = date('z') + 1;
    $month = date('n');
    $year = date('Y');
    $isToday = Event::IsToday($player->GetPlanet(), $player->GetPlace(), $entry['placeandtime']);

    if($halloweenEventActive && $entry['id'] == 11)
        $isToday = true;
    if ($isToday && ($player->GetARank() >= 2 || $player->GetLevel() >= $entry['level']))
    {
        $group = $player->GetGroup();
        $amount = 1;
        if ($group != null)
        {
            $amount = count($group);
        }

        if ($amount > $entry['maxplayers'])
        {
            $amount = $entry['maxplayers'];
        }
        ?>
        <table>
            <tr>
                <td>
                    <table width="25%" cellspacing="0" cellpadding="1" border="0" style="text-align: center">
                        <tr>
                            <td class="boxSchatten catGradient borderB borderT" colspan="6" align="center">
                                <b><?php echo $entry['name']; ?></b>
                            </td>
                        </tr>
                        <tr>
                            <td width="25%" class="boxSchatten borderT borderR borderL borderB">
                                <img src="img/events/<?php echo $entry['image']; ?>.png" />
                                <hr>
                                <b><span style="text-align:center;"><?php echo $text; ?></span></b>
                                <?php
                                if($entry['stats'] != 0 && $entry['displayprice'])
                                {
                                    echo '<hr><b>'. number_format($entry['stats'],0 ,'', '.') . ' Statspunkte </b>';
                                }
                                if($entry['minplayers'] != 1)
                                {
                                    ?>
                                    <hr><b>Gruppenbeitritt ab +<?php echo $entry['minplayers']; ?></b>
                                    <?php
                                }
                                $wins = 0;
                                if ($entry['decreasenpcfight'])
                                {
                                    ?>
                                    <hr>
                                    <?php
                                    echo 'Tägliche NPC Kämpfe: ' . number_format($player->GetDailyNPCFights(), '0', '', '.') . ' / ' . number_format($player->GetDailyNPCFightsMax(), '0', '', '.');
                                }
                                else if ($entry['winable'] != 0)
                                {
                                    //$wins = Event::GetPlayerWins($entry['finishedplayers'], $player->GetID());
                                    if($entry['id'] == 29 || $entry['id'] == 30 || $entry['id'] == 31)
                                    {
                                        $wins = Event::GetPlayerWins($entry['finishedplayers'], $player->GetID());
                                    }
                                    else if($entry['id'] != 18 && $entry['id'] != 23 && $entry['id'] != 11 && $entry['id'] != 20)
                                    {
                                        $wins = $player->GetDungeon();
                                    }
                                    else 
                                    {
                                        if($entry['id'] == 18 && $player->GetExtraDungeon(18) > $player->GetDungeon())
                                            $wins = $player->GetExtraDungeon(18);
                                        else if($entry['id'] == 20 && $player->GetExtraDungeon(20) > $player->GetDungeon())
                                            $wins = $player->GetExtraDungeon(20);
                                        else if($entry['id'] == 15 && $player->GetExtraDungeon(15) > $player->GetDungeon())
                                            $wins = $player->GetExtraDungeon(15);
                                        else if($entry['id'] == 23 || $entry['id'] == 11)
                                        {
                                            $playersData = explode(';', $entry['finishedplayers']);
                                            $s = 0;
                                            while (isset($playersData[$s]))
                                            {
                                                $playerData = explode('@', $playersData[$s]);
                                                if ($playerData[0] == $player->GetID())
                                                {
                                                    $wins = $playerData[1];
                                                    break;
                                                }
                                                ++$s;
                                            }
                                        }
                                        else
                                            $wins = $player->GetDungeon();
                                    }
                                    ?>
                                    <hr>
                                    <?php
                                    if ($entry['dailyreset'])
                                        echo 'Täglich: ';
                                    else
                                        echo 'Maximal: ';
                                    ?>
                                    <b>
                                        <?php
                                        echo number_format($wins, '0', '', '.'); ?> / <?php echo number_format($entry['winable'], '0', '', '.');
                                        if($player->GetExtraDungeon(18) < $player->GetDungeon() && $player->GetDungeon() == 10 && $entry['id'] == 18)
                                        {
                                            echo "<br/>Du hast die maximale Anzahl an täglichen<br/>Dungeonkämpfe bereits aufgebraucht,du erhältst<br/>heute keine Stats mehr aus diesen Dungeon.";
                                        }
                                        else if($player->GetExtraDungeon(20) < $player->GetDungeon() && $player->GetDungeon() == 10 && $entry['id'] == 20)
                                        {
                                            echo "<br/>Du hast die maximale Anzahl an täglichen<br/>Dungeonkämpfe bereits aufgebraucht,du erhältst<br/>heute keine Stats mehr aus diesen Dungeon.";
                                        }
                                        else if($player->GetExtraDungeon(15) < $player->GetDungeon() && $player->GetDungeon() == 10 && $entry['id'] == 15)
                                        {
                                            echo "<br/>Du hast die maximale Anzahl an täglichen<br/>Dungeonkämpfe bereits aufgebraucht,du erhältst<br/>heute keine Stats mehr aus diesen Dungeon.";
                                        }
                                        $typefight = 5; // Event
                                        $titelManager = new TitelManager($database);
                                        $npcdata = explode('@', $entry['fights']);
                                        $lowestTitel = null;
                                        if(count($npcdata) > 1)
                                        {
                                            for ($n = 0; $n < count($npcdata); $n++)
                                            {
                                                $npcraw = $npcdata[$n];
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
                                            $npc = explode(";", $npcraw);
                                            $titels = $titelManager->GetTitelsOfNPC($npc[0], $typefight);
                                            for ($t = 0; $t < count($titels); ++$t)
                                            {
                                                $titel = $titels[$t];
                                                if (!$player->HasTitel($titel->GetID()))
                                                {
                                                    if ($lowestTitel == null || $lowestTitel->GetCondition() > $titel->GetCondition())
                                                        $lowestTitel = $titel;
                                                }
                                            }
                                        }
                                        if($lowestTitel != null)
                                        {
                                            $titelProgress = $titelManager->LoadProgress($player->GetID(), $lowestTitel->GetID());
                                            $progress = 0;
                                            if (isset($titelProgress))
                                                $progress = $titelProgress['progress'];
                                            ?>
                                            <div class="expback" style="height:20px; width:90%; margin-left: 5%;">
                                                <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                <div class="expanzeige" style="width:<?php echo $progress / $lowestTitel->GetCondition() * 100 ; ?>%"></div>
                                                <div class="exptext">
                                                    Nächster Titel: <?php echo number_format($progress,'0', '', '.') . ' / ' . number_format($lowestTitel->GetCondition(),'0', '', '.'); ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </b>
                                    <?php
                                }
                                ?>
                                <hr>
                                <?php
                                if ($entry['minplayers'] > $amount)
                                {
                                    echo 'Zuwenig Spieler in der Gruppe.';
                                }
                                else
                                {
                                    ?>
                                    <form method="POST" action="?p=boss&a=start">
                                        <?php
                                        if($amount > 1)
                                        {
                                            ?>
                                            <select style="width:90%;" class="select" name="players">
                                                <?php
                                                $j = $entry['minplayers'];
                                                while ($j <= $amount)
                                                {
                                                    ?>
                                                    <option value="<?php echo $j; ?>" <?php if ($j == $amount) { echo 'selected'; } ?>><?php echo $j; ?> Spieler</option>
                                                    <?php
                                                    ++$j;
                                                }
                                                ?>
                                            </select>
                                            <?php
                                        }
                                        else
                                        {
                                            $j = $entry['minplayers'];
                                            ?>
                                            <input type="hidden" name="players" value="1">
                                            <?php
                                        }
                                        ?>
                                        <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                        <input type="submit" value="Kampf Starten">
                                    </form>
                                    <?php
                                }
                                ?>
                                <div class="spacer"></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php
        echo '<br/>';
    }
    $id++;
    $entry = $events->GetEntry($id);
}
if ($j == 0)
{
    echo "<img src='".$serverUrl."img/marketing/onepiecechoppernoevent.png'  />";
    echo "<br /><br /><b>Nope.... Hier gibt es keinen Dungeon oder du bist zu schwach</b>";
}
?>