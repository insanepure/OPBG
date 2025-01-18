<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb', {
            action: 'Event'
        });
    });
</script>
<img width="100%" height="300" src="img/marketing/Events.png" />
<?php
$events = new Generallist($database, 'events', '*', 'isdungeon=0', '', 9999, 'ASC');
$itemManager = new ItemManager($database);
$active = false;
$id = 0;
$entry = $events->GetEntry($id);
while ($entry != null)
{
    //Erde;Wald;1:2:3:4:5;1-31;1-12;0-365;2018-3000
    $pandts = explode('@', $entry['placeandtime']);
    $i = 0;
    $weekDay = date('N');
    $monthDay = date('j');
    $yearDay = date('z') + 1;
    $month = date('n');
    $year = date('Y');
    if (($player->GetARank() >= 2 || $player->GetLevel() >= $entry['level']))
    {
        $active = true;
        $isToday = Event::IsTodayEvent(1, $entry['placeandtime'], $entry['begin'], $entry['end']);
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
        <table width="25%" cellspacing="0" cellpadding="1" border="0">
            <tr>
                <td class="boxSchatten catGradient borderB borderT" colspan="6" align="center"><b><?php echo $entry['name']; ?></b></td>
            </tr>
            <tr>
            <tr>
                <td width="25%" class="boxSchatten borderT borderR borderL borderB">
                    <center><img src="img/events/<?php echo $entry['image']; ?>.png" />
                        <hr><b>Dropchance: <?php echo $entry['dropchance']; ?>%</b>
                        <?php
                        if($entry['minplayers'] != 1)
                        {
                            ?>
                            <hr><b>Gruppenbeitritt ab +<?php echo $entry['minplayers']; ?></b>
                            <?php
                        }
                        if ($isToday)
                        {
                            if ($entry['decreasenpcfight'])
                            {
                                echo "<hr>";
                                echo 'Tägliche NPC Kämpfe: ' . number_format($player->GetDailyNPCFights(), '0', '', '.') . ' / ' . number_format($player->GetDailyNPCFightsMax(), '0', '', '.');
                            }
                            else if ($entry['winable'] != 0)
                            {
                                $wins = 0;
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
                                echo "<hr>";
                                if ($entry['dailyreset'])
                                    echo 'Täglich: ';
                                else
                                    echo 'Maximal: ';
                                echo "<b>" . number_format($wins, '0', '', '.') . " / " . number_format($entry['winable'], '0', '', '.') . "</b>";
                                if($entry['id'] != 19)
                                {
                                    $typefight = 5; // Event
                                    $titelManager = new TitelManager($database);
                                    $npcdata = explode('@', $entry['fights']);
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
                                                $i = 0;
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
                                        <?php
                                    }
                                }
                            }
                            echo "<hr>";
                            if ($entry['minplayers'] > $amount)
                            {
                                echo "Zuwenig Spieler in der Gruppe.";
                            }
                            else
                            {
                                ?>
                                <form method="POST" action="?p=event&a=start">
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
                        }
                        else
                        {
                            ?>
                            <hr><?php
                            echo $entry['schedule'];
                        }
                        ?>
                    </center>
                    <div class="spacer"></div>
                </td>
            </tr>
        </table>
        <?php
    }
    $id++;
    $entry = $events->GetEntry($id);
    echo "<br />";
}


if (!$active)
{
    ?>
    Hier im Ort finden keine Events statt.
    <?php
}
?>