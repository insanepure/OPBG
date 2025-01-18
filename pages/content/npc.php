<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb', {
            action: 'NPC'
        });
    });
</script>
<img width='100%' height='300' src='img/marketing/NPCs.png' />
Tägliche NPC-Kämpfe: <?php echo number_format($player->GetDailyNPCFights(),'0', '', '.') . ' / ' . number_format($player->GetDailyNPCFightsMax(),'0', '', '.'); ?>
<?php
if($player->IsDonator())
{
    ?>
    <br/>(Zähler steigt als Unterstützer +2 pro Kampf.)
    <?php
}
?>
<div class="spacer"></div>
<table width="100%" cellspacing="0">
    <tr>
        <td class="catGradient borderB borderT" colspan="4" align="center"><b>NPC-Kampf</b></td>
    </tr>
    <tr>
        <td width="15%" class="boxSchatten">
            <center><b>Bild</b></center>
        </td>
        <td width="50%" class="boxSchatten">
            <center><b>Beschreibung</b></center>
        </td>
        <td width="30%" class="boxSchatten">
            <center><b>Gewinn</b></center>
        </td>
        <td width="10%" class="boxSchatten">
            <center><b>Aktion</b></center>
        </td>
    </tr>
    <?php
    $id = 0;
    $npcList = new Generallist($database, 'npcs', '*', '', '', 9999, 'ASC');
    $entry = $npcList->GetEntry($id);
    $npcs = $place->GetNPCs();
    while ($entry != null)
    {
        if (in_array($entry['id'], $npcs) && ($player->GetARank() >= 2 || $player->GetLevel() >= $entry['level']))
        {
            ?>
            <tr>
                <td class="boxSchatten">
                    <center>
                        <b>
                            <?php
                            if ($player->GetArank() == 3)
                            {
                                echo $entry['name'];
                            }
                            ?>
                        </b>
                        <img src="img/npc/<?php echo $entry['image']; ?>.png" style="width:100%;height:100%;">
                    </center>
                </td>
                <td class="boxSchatten">
                    <center>
                        <?php echo $bbcode->parse($entry['description']); ?><br />
                        <?php
                        $typefight = 3; //NPC
                        $titels = $titelManager->GetTitelsOfNPC($entry['id'], $typefight);
                        $lowestTitel = null;
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
                        ?>
                    </center>
                </td>
                <td class="boxSchatten">
                    <center>
                        <?php
                        $gewinn = 0;
                        if ($entry['id'] != 35)
                        {
                            $gewinn = $entry['zeni'];
                        }
                        else
                        {
                            $gewinn = $player->GetLevel() * $entry['zeni'];
                        }
                        if ($entry['zeni'] > 0)
                        {
                            if($player->IsDonator())
                                echo number_format($gewinn,'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/> <br/>(+'.number_format($gewinn,'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/> als Unterstützer)<br/>';
                            else
                                echo number_format($gewinn,'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/><br/>';
                        }
                        if ($entry['gold'] > 0) echo number_format($entry['gold'],'0', '', '.') . ' <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/> <br />';
                        if ($entry['items'] != '')
                        {
                            $items = explode(';', $entry['items']);
                            $i = 0;
                            while (isset($items[$i]))
                            {
                                $item = explode('@', $items[$i]);
                                $itemID = $item[0];
                                $chance = $item[1];
                                $itemData = $itemManager->GetItem($itemID);
                                echo $itemData->GetName() . ' ' . $chance . '%<br/>';
                                ++$i;
                            }
                        }
                        ?>
                    </center>
                </td>
                <td class="boxSchatten">
                    <center>
                        <form method="POST" id="fight<?php echo $entry['id']; ?>" action="?p=npc&a=fight&id=<?php echo $entry['id']; ?>">
                            <select class="select" name="type">
                                <option value="0">Spaß</option>
                                <option value="3" selected>NPC</option>
                            </select>
                            <?php
                            $group = $player->GetGroup();
                            $amount = 1;
                            if ($group != null)
                            {
                                $amount = count($group);
                            }
                            if($entry['maxenemy'] > 1 && $amount > 1)
                            {
                                ?>
                                <select class="select" name="difficulty">
                                    <?php
                                    $npcenemy = 0;
                                    while ($npcenemy < $entry['maxenemy'])
                                    {
                                        $npcenemy++;
                                        if ($npcenemy == 1) echo '<option value="0">Alleine</option>';
                                        else
                                        {
                                            echo '<option value="' . ($npcenemy - 1) . '"';
                                            if(($npcenemy - 1) == $amount) echo ' selected';
                                            echo '>' . $npcenemy . 'vs1</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            else
                            {
                                ?>
                                <input type="hidden" name="difficulty" value="0">
                                <?php
                            }
                            ?>
                            <div class="exitFight" style="width: 90px;" onmousedown="fight<?php echo $entry['id']; ?>.submit();">Starten</div>
                        </form>
                        <?php
                        if($player->GetArank() >= 2)
                        {
                            ?>
                            <div class="spacer"></div>
                            <div class="exitFight" onmousedown="location.replace('?p=admin&a=see&table=npcs&id=<?php echo $entry['id']; ?>')">Balancen</div>
                            <?php
                        }
                        ?>
                    </center>
                </td>
            </tr>
            <?php
        }
        ++$id;
        $entry = $npcList->GetEntry($id);
    }
    ?>
</table>
<div class="spacer"></div>
<table width="100%" cellspacing="0">
    <tr>
        <td class="catGradient borderB borderT" colspan="4" align="center"><b>Vivre Card</b></td>
    </tr>
    <tr>
        <td width="15%" class="boxSchatten">
            <center><b>Bild</b></center>
        </td>
        <td width="50%" class="boxSchatten">
            <center><b>Beschreibung</b></center>
        </td>
        <td width="30%" class="boxSchatten">
            <center><b>Gewinn</b></center>
        </td>
        <td width="10%" class="boxSchatten">
            <center><b>Aktion</b></center>
        </td>
    </tr>
    <?php
    $id = 0;
    $npcList = new Generallist($database, 'npcs', '*', 'id="'.$player->GetVivrecard().'"', '', 9999, 'ASC');
    $entry = $npcList->GetEntry($id);
    $npcs = $place->GetNPCs();
    while ($entry != null)
    {
            ?>
            <tr>
                <td class="boxSchatten">
                    <center>
                        <b>
                            <?php
                            if ($player->GetArank() == 3)
                            {
                                echo $entry['name'];
                            }
                            ?>
                        </b>
                        <img src="img/npc/<?php echo $entry['image']; ?>.png" style="width:100%;height:100%;">
                    </center>
                </td>
                <td class="boxSchatten">
                    <center>
                        <?php echo $bbcode->parse($entry['description']); ?><br />
                        <?php
                        $typefight = 3; //NPC
                        $titels = $titelManager->GetTitelsOfNPC($entry['id'], $typefight);
                        $lowestTitel = null;
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
                        ?>
                    </center>
                </td>
                <td class="boxSchatten">
                    <center>
                        <?php
                        $gewinn = 0;
                        if ($entry['id'] != 35)
                        {
                            $gewinn = $entry['zeni'];
                        }
                        else
                        {
                            $gewinn = $player->GetLevel() * $entry['zeni'];
                        }
                        if ($entry['zeni'] > 0)
                        {
                            if($player->IsDonator())
                                echo number_format($gewinn,'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/> <br/>(+'.number_format($gewinn,'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/> als Unterstützer)<br/>';
                            else
                                echo number_format($gewinn,'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/><br/>';
                        }
                        if ($entry['gold'] > 0) echo number_format($entry['gold'],'0', '', '.') . ' <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/> <br />';
                        if ($entry['items'] != '')
                        {
                            $items = explode(';', $entry['items']);
                            $i = 0;
                            while (isset($items[$i]))
                            {
                                $item = explode('@', $items[$i]);
                                $itemID = $item[0];
                                $chance = $item[1];
                                $itemData = $itemManager->GetItem($itemID);
                                echo $itemData->GetName() . ' ' . $chance . '%<br/>';
                                ++$i;
                            }
                        }
                        ?>
                    </center>
                </td>
                <td class="boxSchatten">
                    <center>
                        <form method="POST" id="fight<?php echo $entry['id']; ?>" action="?p=npc&a=fights&id=<?php echo $entry['id']; ?>">
                            <select class="select" name="type">
                                <option value="0">Spaß</option>
                                <option value="3" selected>NPC</option>
                            </select>
                            <?php
                            $group = $player->GetGroup();
                            $amount = 1;
                            if ($group != null)
                            {
                                $amount = count($group);
                            }
                            if($entry['maxenemy'] > 1 && $amount > 1)
                            {
                                ?>
                                <select class="select" name="difficulty">
                                    <?php
                                    $npcenemy = 0;
                                    while ($npcenemy < $entry['maxenemy'])
                                    {
                                        $npcenemy++;
                                        if ($npcenemy == 1) echo '<option value="0">Alleine</option>';
                                        else
                                        {
                                            echo '<option value="' . ($npcenemy - 1) . '"';
                                            if(($npcenemy - 1) == $amount) echo ' selected';
                                            echo '>' . $npcenemy . 'vs1</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            else
                            {
                                ?>
                                <input type="hidden" name="difficulty" value="0">
                                <?php
                            }
                            ?>
                            <div class="exitFight" style="width: 90px;" onmousedown="fight<?php echo $entry['id']; ?>.submit();">Starten</div>
                        </form>
                        <?php
                        if($player->GetArank() >= 2)
                        {
                            ?>
                            <div class="spacer"></div>
                            <div class="exitFight" onmousedown="location.replace('?p=admin&a=see&table=npcs&id=<?php echo $entry['id']; ?>')">Balancen</div>
                            <?php
                        }
                        ?>
                    </center>
                </td>
            </tr>
            <?php
        ++$id;
        $entry = $npcList->GetEntry($id);
    }
    ?>
</table>