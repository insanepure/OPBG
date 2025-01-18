<?php
    include_once 'classes/fight/attack.php';
?>
    <div class="spacer"></div>
    <table style="text-align: center;">
        <tr>
            <td style="width: 33%;">
                <a href="?p=info&info=allgemeines">Allgemeines</a><br>
                <a href="?p=info&info=fraktion">Fraktion</a><br>
                <a href="?p=info&info=pfad">Pfad</a><br>
                <a href="?p=info&info=techniken">Techniken</a><br>
                <a href="?p=info&info=skilltree&race=Pirat&tree=1">Skilltree</a><br>
                <a href="?p=info&info=items">Items</a><br>
                <a href="?p=info&info=bande">Bande</a><br>
                <a href="?p=info&info=events">Events</a><br>
                <a href="?p=info&info=dungeons">Dungeons</a><br>
            </td>
            <td style="width: 33%;">
                <img src="img/sunny.png" alt="Info" title="Info" height="300"/><br>
                <div style="text-align:center">
                    <a href="?p=info&info=faq">• Häufig gestellte Fragen</a><br>
                </div>
            </td>
            <td style="width: 33%;">
                <a href="?p=info&info=motto">Motto Tage</a><br>
                <a href="?p=info&info=titel">Titel</a><br>
                <a href="?p=info&info=chat">Chat</a><br>
                <a href="?p=regeln">Regeln</a><br>
                <a href="?p=info&info=bbcode">BBCode</a><br>
                <a href="?p=info&info=cookies">Cookies</a><br>
                <a href="?p=info&info=dsgvo">Datenschutzverordnung</a><br>
                <a href="?p=info&info=impressum">Impressum</a><br>
                <a href="?p=support">Team</a><br>
            </td>
        </tr>
    </table>
    <hr/>
    <br>

<?php
    //if(!$player->IsDonator() && $player->GetArank() != 3)
    if (isset($_GET['info']) && $_GET['info'] == 'skilltree')
    {
        $skillpoints = 0;
        $race = 'Pirat';
        if (isset($_GET['race']))
        {
            $race = $_GET['race'];
        }
        $p = 'info&info=';
        $topOffset = -368;

        $tree = 1;
        if (isset($_GET['tree']))
            $tree = $_GET['tree'];


        $drace = 'Pirat';
        if ($race == $drace)
        { ?> <b> <?php } ?> <a href="?p=info&info=skilltree&race=<?php echo $drace; ?>&tree=<?php echo $tree; ?>"><?php echo $drace; ?></a> <?php if ($race == $drace)
    { ?> </b> <?php } ?> | <?php
        $drace = 'Marine';
        if ($race == $drace)
        { ?> <b> <?php } ?> <a href="?p=info&info=skilltree&race=<?php echo $drace; ?>&tree=<?php echo $tree; ?>"><?php echo $drace; ?></a> <?php if ($race == $drace)
    { ?> </b> <?php }  ?> <?php
        ?>
        <?php
        include_once 'skilltree.php';
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'titel')
    {
        echo "<b>Hier werden die zu holenden Titel und ihre Voraussetzungen aufgeführt und was genau man dafür bekommt.</b><br /><br />";
        ?>
            <a href="?p=info&info=titel&sort=npc">NPC Titel</a> | <a href="?p=info&info=titel&sort=kolo">Kolosseum Titel</a> | <a href="?p=info&info=titel&sort=event">Event Titel</a> | <a href="?p=info&info=titel&sort=dungeon">Dungeon Titel</a> | <a href="?p=info&info=titel&sort=spezial">Spezial Titel</a>
        <table width="100%">
            <?php
                $itemManager = new ItemManager($database);
                $titelManager = new TitelManager($database);
                $titelManager->LoadProgressesForPlayer($player->GetID());
                foreach ($titelManager->GetTitels() as $titel) {
                    $progress = $titelManager->LoadProgressByID($titel->GetID());

                    if (($titel->IsVisible() || $player->GetArank() >= 2) && $titel->GetID() != 1 && $titel->GetID() != 2 && $titel->GetID() != 19)
                    {
                        if($_GET['info'] == 'titel' && isset($_GET['sort']) && $_GET['sort'] == 'npc' && $titel->GetSortierung() == 'NPC')
                        {
                            ?>
                            <tr>
                                <td>
                                    <div class="catGradient borderT borderB">
                                        <div style="text-align:center">
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '<a href="?p=admin&a=see&table=titel&id='.$titel->GetID().'">';
                                            ?>
                                            <b>
                                                <?php echo "<span style='color: #" . $titel->GetColor() . "'>" . $titel->GetName() . "</span>"; ?>
                                            </b>
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '</a>';
                                            ?>
                                        </div>
                                    </div>
                                    <table>
                                        <tr>
                                            <td><img src="<?php echo $titel->GetTitelPic(); ?>" /></td>
                                            <td>
                                                <?php echo $titel->GetDescription() . "<br />";
                                                if ($titel->GetLP() != 0)
                                                {
                                                    echo "<br /><b>LP: </b>" . number_format($titel->GetLP(), '0', '', '.');
                                                }
                                                if ($titel->GetKP() != 0)
                                                {
                                                    echo "<br /> <b>AD: </b>" . number_format($titel->GetKP(), '0', '', '.');
                                                }
                                                if ($titel->GetAtk() != 0)
                                                {
                                                    echo "<br /> <b>ATK: </b>" . number_format($titel->GetAtk(), '0', '', '.');
                                                }
                                                if ($titel->GetDef() != 0)
                                                {
                                                    echo "<br /> <b>DEF: </b>" . number_format($titel->GetDef(), '0', '', '.');
                                                }
                                                if ($titel->GetItems() != 0)
                                                {
                                                    $items = explode(';', $titel->GetItems());
                                                    foreach ($items as $item) {
                                                        $item = explode('@', $item);
                                                        // $item[0] == id
                                                        // $item[1] == amount
                                                        $itemData = $itemManager->GetItem($item[0]);
                                                        echo "<br /><b>Item:</b> " . number_format($item[1], 0, '', '.') . "x " . $itemData->GetName();
                                                    }
                                                }
                                                if($titel->GetBerry() != 0)
                                                {
                                                    echo "<br /><b>Berry:</b> " . number_format($titel->GetBerry(), '0', '', '.');
                                                }
                                                if($titel->GetGold() != 0)
                                                {
                                                    echo "<br /><b>Gold:</b> " . number_format($titel->GetGold(), '0', '', '.');
                                                }
                                                else
                                                {
                                                    echo "";
                                                }
                                                if($progress > $titel->GetCondition())
                                                {
                                                    echo "";
                                                }
                                                else if($player->HasTitel($titel->GetID()))
                                                {
                                                    echo "<br /><br /><span style='color:green;'>Glückwunsch! Du hast den Titel bereits erreicht.</span>";
                                                }
                                                else if($titel->GetCondition() == 0)
                                                {
                                                    echo "";
                                                }
                                                else if($progress == 0)
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:0%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: 0 / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:<?php echo $progress / $titel->GetCondition() * 100 ; ?>%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: <?php echo number_format($progress,'0', '', '.') ?> / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if(!$titel->IsVisible())
                                                {
                                                    echo "<br /><br /><span style='color:red;'>Dieser Titel ist nur für das Team sichtbar!</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                                <?php
                        }
                        else if($_GET['info'] == 'titel' && isset($_GET['sort']) && $_GET['sort'] == 'kolo' && $titel->GetSortierung() == 'kolosseum')
                        {
                            ?>
                            <tr>
                                <td>
                                    <div class="catGradient borderT borderB">
                                        <div style="text-align:center">
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '<a href="?p=admin&a=see&table=titel&id='.$titel->GetID().'">';
                                            ?>
                                            <b>
                                                <?php echo "<span style='color: #" . $titel->GetColor() . "'>" . $titel->GetName() . "</span>"; ?>
                                            </b>
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '</a>';
                                            ?>
                                        </div>
                                    </div>
                                    <table>
                                        <tr>
                                            <td><img src="<?php echo $titel->GetTitelPic(); ?>" /></td>
                                            <td>
                                                <?php echo $titel->GetDescription() . "<br />";
                                                if ($titel->GetLP() != 0)
                                                {
                                                    echo "<br /><b>LP: </b>" . number_format($titel->GetLP(), '0', '', '.');
                                                }
                                                if ($titel->GetKP() != 0)
                                                {
                                                    echo "<br /> <b>AD: </b>" . number_format($titel->GetKP(), '0', '', '.');
                                                }
                                                if ($titel->GetAtk() != 0)
                                                {
                                                    echo "<br /> <b>ATK: </b>" . number_format($titel->GetAtk(), '0', '', '.');
                                                }
                                                if ($titel->GetDef() != 0)
                                                {
                                                    echo "<br /> <b>DEF: </b>" . number_format($titel->GetDef(), '0', '', '.');
                                                }
                                                if ($titel->GetItems() != 0)
                                                {
                                                    $items = explode(';', $titel->GetItems());
                                                    foreach ($items as $item) {
                                                        $item = explode('@', $item);
                                                        // $item[0] == id
                                                        // $item[1] == amount
                                                        $itemData = $itemManager->GetItem($item[0]);
                                                        echo "<br /><b>Item:</b> " . number_format($item[1], 0, '', '.') . "x " . $itemData->GetName();
                                                    }
                                                }
                                                if($titel->GetBerry() != 0)
                                                {
                                                    echo "<br /><b>Berry:</b> " . number_format($titel->GetBerry(), '0', '', '.');
                                                }
                                                if($titel->GetGold() != 0)
                                                {
                                                    echo "<br /><b>Gold:</b> " . number_format($titel->GetGold(), '0', '', '.');
                                                }
                                                else
                                                {
                                                    echo "";
                                                }
                                                if($progress > $titel->GetCondition())
                                                {
                                                    echo "";
                                                }
                                                else if($player->HasTitel($titel->GetID()))
                                                {
                                                    echo "<br /><br /><span style='color:green;'>Glückwunsch! Du hast den Titel bereits erreicht.</span>";
                                                }
                                                else if($titel->GetCondition() == 0)
                                                {
                                                    echo "";
                                                }
                                                else if($progress == 0)
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:0%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: 0 / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:<?php echo $progress / $titel->GetCondition() * 100 ; ?>%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: <?php echo number_format($progress,'0', '', '.') ?> / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if(!$titel->IsVisible())
                                                {
                                                    echo "<br /><br /><span style='color:red;'>Dieser Titel ist nur für das Team sichtbar!</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <?php
                        }
                        else if($_GET['info'] == 'titel' && isset($_GET['sort']) && $_GET['sort'] && $_GET['sort'] == 'event' && $titel->GetSortierung() == 'event')
                        {
                            ?>
                            <tr>
                                <td>
                                    <div class="catGradient borderT borderB">
                                        <div style="text-align:center">
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '<a href="?p=admin&a=see&table=titel&id='.$titel->GetID().'">';
                                            ?>
                                            <b>
                                                <?php echo "<span style='color: #" . $titel->GetColor() . "'>" . $titel->GetName() . "</span>"; ?>
                                            </b>
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '</a>';
                                            ?>
                                        </div>
                                    </div>
                                    <table>
                                        <tr>
                                            <td><img src="<?php echo $titel->GetTitelPic(); ?>" /></td>
                                            <td>
                                                <?php echo $titel->GetDescription() . "<br />";
                                                if ($titel->GetLP() != 0)
                                                {
                                                    echo "<br /><b>LP: </b>" . number_format($titel->GetLP(), '0', '', '.');
                                                }
                                                if ($titel->GetKP() != 0)
                                                {
                                                    echo "<br /> <b>AD: </b>" . number_format($titel->GetKP(), '0', '', '.');
                                                }
                                                if ($titel->GetAtk() != 0)
                                                {
                                                    echo "<br /> <b>ATK: </b>" . number_format($titel->GetAtk(), '0', '', '.');
                                                }
                                                if ($titel->GetDef() != 0)
                                                {
                                                    echo "<br /> <b>DEF: </b>" . number_format($titel->GetDef(), '0', '', '.');
                                                }
                                                if ($titel->GetItems() != 0)
                                                {
                                                    $items = explode(';', $titel->GetItems());
                                                    foreach ($items as $item) {
                                                        $item = explode('@', $item);
                                                        // $item[0] == id
                                                        // $item[1] == amount
                                                        $itemData = $itemManager->GetItem($item[0]);
                                                        echo "<br /><b>Item:</b> " . number_format($item[1], 0, '', '.') . "x " . $itemData->GetName();
                                                    }
                                                }
                                                if($titel->GetBerry() != 0)
                                                {
                                                    echo "<br /><b>Berry:</b> " . number_format($titel->GetBerry(), '0', '', '.');
                                                }
                                                if($titel->GetGold() != 0)
                                                {
                                                    echo "<br /><b>Gold:</b> " . number_format($titel->GetGold(), '0', '', '.');
                                                }
                                                else
                                                {
                                                    echo "";
                                                }
                                                if($progress > $titel->GetCondition())
                                                {
                                                    echo "";
                                                }
                                                else if($player->HasTitel($titel->GetID()))
                                                {
                                                    echo "<br /><br /><span style='color:green;'>Glückwunsch! Du hast den Titel bereits erreicht.</span>";
                                                }
                                                else if($titel->GetCondition() == 0)
                                                {
                                                    echo "";
                                                }
                                                else if($progress == 0)
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:0%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: 0 / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:<?php echo $progress / $titel->GetCondition() * 100 ; ?>%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: <?php echo number_format($progress,'0', '', '.') ?> / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if(!$titel->IsVisible())
                                                {
                                                    echo "<br /><br /><span style='color:red;'>Dieser Titel ist nur für das Team sichtbar!</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <?php
                        }
                        else if($_GET['info'] == 'titel' && isset($_GET['sort']) && $_GET['sort'] == 'dungeon' && $titel->GetSortierung() == 'dungeon')
                        {
                            ?>
                            <tr>
                                <td>
                                    <div class="catGradient borderT borderB">
                                        <div style="text-align:center">
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '<a href="?p=admin&a=see&table=titel&id='.$titel->GetID().'">';
                                            ?>
                                            <b>
                                                <?php echo "<span style='color: #" . $titel->GetColor() . "'>" . $titel->GetName() . "</span>"; ?>
                                            </b>
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '</a>';
                                            ?>
                                        </div>
                                    </div>
                                    <table>
                                        <tr>
                                            <td><img src="<?php echo $titel->GetTitelPic(); ?>" /></td>
                                            <td>
                                                <?php echo $titel->GetDescription() . "<br />";
                                                if ($titel->GetLP() != 0)
                                                {
                                                    echo "<br /><b>LP: </b>" . number_format($titel->GetLP(), '0', '', '.');
                                                }
                                                if ($titel->GetKP() != 0)
                                                {
                                                    echo "<br /> <b>AD: </b>" . number_format($titel->GetKP(), '0', '', '.');
                                                }
                                                if ($titel->GetAtk() != 0)
                                                {
                                                    echo "<br /> <b>ATK: </b>" . number_format($titel->GetAtk(), '0', '', '.');
                                                }
                                                if ($titel->GetDef() != 0)
                                                {
                                                    echo "<br /> <b>DEF: </b>" . number_format($titel->GetDef(), '0', '', '.');
                                                }
                                                if ($titel->GetItems() != 0)
                                                {
                                                    $items = explode(';', $titel->GetItems());
                                                    foreach ($items as $item) {
                                                        $item = explode('@', $item);
                                                        // $item[0] == id
                                                        // $item[1] == amount
                                                        $itemData = $itemManager->GetItem($item[0]);
                                                        echo "<br /><b>Item:</b> " . number_format($item[1], 0, '', '.') . "x " . $itemData->GetName();
                                                    }
                                                }
                                                if($titel->GetBerry() != 0)
                                                {
                                                    echo "<br /><b>Berry:</b> " . number_format($titel->GetBerry(), '0', '', '.');
                                                }
                                                if($titel->GetGold() != 0)
                                                {
                                                    echo "<br /><b>Gold:</b> " . number_format($titel->GetGold(), '0', '', '.');
                                                }
                                                else
                                                {
                                                    echo "";
                                                }
                                                if($progress > $titel->GetCondition())
                                                {
                                                    echo "";
                                                }
                                                else if($player->HasTitel($titel->GetID()))
                                                {
                                                    echo "<br /><br /><span style='color:green;'>Glückwunsch! Du hast den Titel bereits erreicht.</span>";
                                                }
                                                else if($titel->GetCondition() == 0)
                                                {
                                                    echo "";
                                                }
                                                else if($progress == 0)
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:0%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: 0 / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:<?php echo $progress / $titel->GetCondition() * 100 ; ?>%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: <?php echo number_format($progress,'0', '', '.') ?> / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if(!$titel->IsVisible())
                                                {
                                                    echo "<br /><br /><span style='color:red;'>Dieser Titel ist nur für das Team sichtbar!</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                                <?php
                        }
                        else if($_GET['info'] == 'titel' && isset($_GET['sort']) && $_GET['sort'] == 'spezial' && $titel->GetSortierung() == 'spezial')
                        {
                        ?>
                            <tr>
                                <td>
                                    <div class="catGradient borderT borderB">
                                        <div style="text-align:center">
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '<a href="?p=admin&a=see&table=titel&id='.$titel->GetID().'">';
                                            ?>
                                            <b>
                                                <?php echo "<span style='color: #" . $titel->GetColor() . "'>" . $titel->GetName() . "</span>"; ?>
                                            </b>
                                            <?php
                                            if($player->GetArank() >= 2)
                                                echo '</a>';
                                            ?>
                                        </div>
                                    </div>
                                    <table>
                                        <tr>
                                            <td><img src="<?php echo $titel->GetTitelPic(); ?>" /></td>
                                            <td>
                                                <?php echo $titel->GetDescription() . "<br />";
                                                if ($titel->GetLP() != 0)
                                                {
                                                    echo "<br /><b>LP: </b>" . number_format($titel->GetLP(), '0', '', '.');
                                                }
                                                if ($titel->GetKP() != 0)
                                                {
                                                    echo "<br /> <b>AD: </b>" . number_format($titel->GetKP(), '0', '', '.');
                                                }
                                                if ($titel->GetAtk() != 0)
                                                {
                                                    echo "<br /> <b>ATK: </b>" . number_format($titel->GetAtk(), '0', '', '.');
                                                }
                                                if ($titel->GetDef() != 0)
                                                {
                                                    echo "<br /> <b>DEF: </b>" . number_format($titel->GetDef(), '0', '', '.');
                                                }
                                                if ($titel->GetItems() != 0)
                                                {
                                                    $items = explode(';', $titel->GetItems());
                                                    foreach ($items as $item) {
                                                        $item = explode('@', $item);
                                                        // $item[0] == id
                                                        // $item[1] == amount
                                                        $itemData = $itemManager->GetItem($item[0]);
                                                        echo "<br /><b>Item:</b> " . number_format($item[1], 0, '', '.') . "x " . $itemData->GetName();
                                                    }
                                                }
                                                if($titel->GetBerry() != 0)
                                                {
                                                    echo "<br /><b>Berry:</b> " . number_format($titel->GetBerry(), '0', '', '.');
                                                }
                                                if($titel->GetGold() != 0)
                                                {
                                                    echo "<br /><b>Gold:</b> " . number_format($titel->GetGold(), '0', '', '.');
                                                }
                                                else
                                                {
                                                    echo "";
                                                }
                                                if($progress > $titel->GetCondition())
                                                {
                                                    echo "";
                                                }
                                                else if($player->HasTitel($titel->GetID()))
                                                {
                                                    echo "<br /><br /><span style='color:green;'>Glückwunsch! Du hast den Titel bereits erreicht.</span>";
                                                }
                                                else if($titel->GetCondition() == 0)
                                                {
                                                    echo "";
                                                }
                                                else if($progress == 0)
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:0%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: 0 / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <div class="expback" style="height:20px; width:90%;">
                                                        <div class="expbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                                        <div class="expanzeige" style="width:<?php echo $progress / $titel->GetCondition() * 100 ; ?>%"></div>
                                                        <div class="exptext" style="position: relative; left: 10px; top: 2px;">
                                                            Aktueller Stand: <?php echo number_format($progress,'0', '', '.') ?> / <?php echo number_format($titel->GetCondition(),'0', '', '.'); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if(!$titel->IsVisible())
                                                {
                                                    echo "<br /><br /><span style='color:red;'>Dieser Titel ist nur für das Team sichtbar!</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                }
            ?>
        </table>
        <div class="spacer"></div>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'dungeons')
    {
        ?>
        <table width="100%">
            <?php
                $itemManager = new ItemManager($database);
                $where = 'isdungeon="1"';
                $events = new Generallist($database, 'events', '*', $where, 'level, id', 99999999999, 'ASC');
                $id = 0;
                $entry = $events->GetEntry($id);
                while ($entry != null)
                {
                    if ($player == null || $entry['id'] == 11 || $entry['id'] == 13 || $entry['id'] == 16 || $entry['id'] == 17 || (!$entry['displayinfo'] && $player->GetArank() < 2))
                    {
                        ++$id;
                        $entry = $events->GetEntry($id);
                        continue;
                    }
                    $pandts = explode('@', $entry['placeandtime']);
                    $pandt = explode(';', $pandts[0]);
                    $dplanet = new Planet($database, $pandt[0]);
                    $dplace = new Place($database, $pandt[1], $actionManager);


                    ?>
                    <tr>
                        <td style="text-align:center">
                            <div class="catGradient borderT borderB">
                                <div style="text-align:center">
                                    <?php
                                        if($player->GetArank() >= 2)
                                            echo '<a href="?p=admin&a=see&table=events&id='.$entry['id'].'">';
                                    ?>
                                    <b>
                                        <?php echo $entry['name']; ?>
                                    </b>
                                    <?php
                                        if($player->GetArank() >= 2)
                                            echo '</a>';
                                    ?>
                                </div>
                            </div>
                            <table>
                                <tr>
                                    <img src="img/events/<?php echo $entry['image']; ?>.png" /><br /><br />
                                    <b>Gebiet: <?php echo $dplanet->GetName() . " - "; ?></b> <b>Ort: <?php echo $dplace->GetName() . " - "; ?></b>

                                    <?php
                                        if ($entry['level'] != 0)
                                        {
                                            ?>
                                            <b>Level: <?php echo number_format($entry['level'], '0', '', '.'); ?></b><br />
                                            <?php
                                        }
                                        if($player != null && $player->GetArank() >= 2)
                                        {
                                            ?>
                                            <div class="spacer"></div>
                                            <form method="post" action="?p=info&info=dungeons&a=jump&m=<?php echo $pandt[0]; ?>&o=<?php echo $pandt[1]; ?>">
                                                <input type="submit" value="Springen"><br/>
                                            </form>
                                            <div class="spacer"></div>
                                            <?php
                                            if(!$entry['displayinfo']) {
                                                echo '<span style="color: red;">Dieser Dungeon ist nur für das Team sichtbar!</span>';
                                            }
                                        }
                                    ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php
                    ++$id;
                    $entry = $events->GetEntry($id);
                }

            ?>
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"></div>
                    </div>
        </table>
        <div class="spacer"></div>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'events')
    {
        ?>
        <table width="100%">
            <?php
                $where = 'isdungeon="0"';
                $events = new Generallist($database, 'events', '*', $where, 'level, id', 99999999999, 'ASC');
                $id = 0;
                $entry = $events->GetEntry($id);
                while ($entry != null)
                {
                    if (!$entry['displayinfo'])
                    {
                        ++$id;
                        $entry = $events->GetEntry($id);
                        continue;
                    }
                    $pandts = explode('@', $entry['placeandtime']);
                    $pandt = explode(';', $pandts[0]);


                    ?>
                    <tr>
                        <td style="text-align:center;">
                            <div class="catGradient borderT borderB">
                                <div style="text-align:center">
                                    <?php
                                        if($player->GetArank() >= 2)
                                            echo '<a href="?p=admin&a=see&table=events&id='.$entry['id'].'">';
                                    ?>
                                    <b>
                                        <?php echo $entry['name']; ?>
                                    </b>
                                    <?php
                                        if($player->GetArank() >= 2)
                                            echo '</a>';
                                    ?>
                                </div>
                            </div>
                            <table>
                                <tr>
                                    <img src="img/events/<?php echo $entry['image']; ?>.png" /><br />
                                    <b><?php echo $entry['schedule']; ?></b><br /><br />
                                    <?php
                                        if ($entry['level'] != 0)
                                        {
                                            ?>
                                            <b>Level: <?php echo number_format($entry['level'], '0', '', '.'); ?></b><br />
                                            <?php
                                        }
                                    ?>

                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php
                    ++$id;
                    $entry = $events->GetEntry($id);
                }

            ?>
        </table><br><br>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'cookies')
    {
        ?>
        <table width="100%">
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Cookies</b></div>
                    </div>
                    Wir nutzen Cookies um eure Logindaten verschlüsselt zu speichern und euch als User zu identifizieren.<br/>
                    Damit wird die Funktion "Eingeloggt bleiben" ermöglicht.<br/>
                    Ebenfalls werden Cookies für die Werbung (Google Adsense) und analysen (Google Analytics) genutzt, dies kommt jedoch von den entsprechenden Providern.<br/>

                </td>
            </tr>
        </table>
        <div class="spacer"></div>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'dsgvo')
    {
        ?>
        <table width="100%">
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Datenschutzverordnung</b></div>
                    </div>
                    <h2 id="m14">Einleitung</h2>
                    <p>Mit der folgenden Datenschutzerklärung möchten wir Sie darüber aufklären, welche Arten Ihrer personenbezogenen Daten (nachfolgend auch kurz als "Daten“ bezeichnet) wir zu welchen Zwecken und in welchem Umfang verarbeiten. Die Datenschutzerklärung gilt für alle von uns durchgeführten Verarbeitungen personenbezogener Daten, sowohl im Rahmen der Erbringung unserer Leistungen als auch insbesondere auf unseren Webseiten, in mobilen Applikationen sowie innerhalb externer Onlinepräsenzen, wie z.B. unserer Social-Media-Profile (nachfolgend zusammenfassend bezeichnet als "Onlineangebot“).</p>
                    <ul class="m-elements"></ul>
                    <p>Stand: 19. August 2019
                    <h2>Inhaltsübersicht</h2>
                    <ul class="index">
                        <li><a class="index-link" href="#m14"> Einleitung</a></li>
                        <li><a class="index-link" href="#m3"> Verantwortlicher</a></li>
                        <li><a class="index-link" href="#mOverview"> Übersicht der Verarbeitungen</a></li>
                        <li><a class="index-link" href="#m13"> Maßgebliche Rechtsgrundlagen</a></li>
                        <li><a class="index-link" href="#m27"> Sicherheitsmaßnahmen</a></li>
                        <li><a class="index-link" href="#m25"> Übermittlung und Offenbarung von personenbezogenen Daten</a></li>
                        <li><a class="index-link" href="#m24"> Datenverarbeitung in Drittländern</a></li>
                        <li><a class="index-link" href="#m134"> Einsatz von Cookies</a></li>
                        <li><a class="index-link" href="#m367"> Registrierung und Anmeldung</a></li>
                        <li><a class="index-link" href="#m182"> Kontaktaufnahme</a></li>
                        <li><a class="index-link" href="#m391"> Kommunikation via Messenger</a></li>
                        <li><a class="index-link" href="#m225"> Bereitstellung des Onlineangebotes und Webhosting</a></li>
                        <li><a class="index-link" href="#m29"> Cloud-Dienste</a></li>
                        <li><a class="index-link" href="#m17"> Newsletter und Breitenkommunikation</a></li>
                        <li><a class="index-link" href="#m263"> Webanalyse und Optimierung</a></li>
                        <li><a class="index-link" href="#m264"> Onlinemarketing</a></li>
                        <li><a class="index-link" href="#m136"> Präsenzen in sozialen Netzwerken</a></li>
                        <li><a class="index-link" href="#m328"> Plugins und eingebettete Funktionen sowie Inhalte</a></li>
                        <li><a class="index-link" href="#m12"> Löschung von Daten</a></li>
                        <li><a class="index-link" href="#m15"> Änderung und Aktualisierung der Datenschutzerklärung</a></li>
                        <li><a class="index-link" href="#m10"> Rechte der betroffenen Personen</a></li>
                        <li><a class="index-link" href="#m42"> Begriffsdefinitionen</a></li>
                    </ul>
                    <h2 id="m3">Verantwortlicher</h2>
                    <p>René Siegner<br>Rosenowstra0e 59<br>04357 Leipzig</p>
                    <ul class="m-elements"></ul>
                    <h2 id="mOverview">Übersicht der Verarbeitungen</h2>
                    <p>Die nachfolgende Übersicht fasst die Arten der verarbeiteten Daten und die Zwecke ihrer Verarbeitung zusammen und verweist auf die betroffenen Personen.</p>
                    <h3>Arten der verarbeiteten Daten</h3>
                    <ul>
                        <li>
                            <p>Bestandsdaten (z.B. Namen, Adressen).</p>
                        </li>
                        <li>
                            <p>Inhaltsdaten (z.B. Texteingaben, Fotografien, Videos).</p>
                        </li>
                        <li>
                            <p>Kontaktdaten (z.B. E-Mail, Telefonnummern).</p>
                        </li>
                        <li>
                            <p>Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).</p>
                        </li>
                        <li>
                            <p>Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten).</p>
                        </li>
                        <li>
                            <p>Standortdaten (Daten, die den Standort des Endgeräts eines Endnutzers angeben).</p>
                        </li>
                    </ul>
                    <h3>Kategorien betroffener Personen</h3>
                    <ul>
                        <li>
                            <p>Beschäftigte (z.B. Angestellte, Bewerber, ehemalige Mitarbeiter).</p>
                        </li>
                        <li>
                            <p>Interessenten.</p>
                        </li>
                        <li>
                            <p>Kommunikationspartner.</p>
                        </li>
                        <li>
                            <p>Kunden.</p>
                        </li>
                        <li>
                            <p>Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten).</p>
                        </li>
                    </ul>
                    <h3>Zwecke der Verarbeitung</h3>
                    <ul>
                        <li>
                            <p>Bereitstellung unseres Onlineangebotes und Nutzerfreundlichkeit.</p>
                        </li>
                        <li>
                            <p>Besuchsaktionsauswertung.</p>
                        </li>
                        <li>
                            <p>Büro- und Organisationsverfahren.</p>
                        </li>
                        <li>
                            <p>Cross-Device Tracking (geräteübergreifende Verarbeitung von Nutzerdaten für Marketingzwecke).</p>
                        </li>
                        <li>
                            <p>Direktmarketing (z.B. per E-Mail oder postalisch).</p>
                        </li>
                        <li>
                            <p>Interessenbasiertes und verhaltensbezogenes Marketing.</p>
                        </li>
                        <li>
                            <p>Kontaktanfragen und Kommunikation.</p>
                        </li>
                        <li>
                            <p>Konversionsmessung (Messung der Effektivität von Marketingmaßnahmen).</p>
                        </li>
                        <li>
                            <p>Profiling (Erstellen von Nutzerprofilen).</p>
                        </li>
                        <li>
                            <p>Remarketing.</p>
                        </li>
                        <li>
                            <p>Reichweitenmessung (z.B. Zugriffsstatistiken, Erkennung wiederkehrender Besucher).</p>
                        </li>
                        <li>
                            <p>Sicherheitsmaßnahmen.</p>
                        </li>
                        <li>
                            <p>Tracking (z.B. interessens-/verhaltensbezogenes Profiling, Nutzung von Cookies).</p>
                        </li>
                        <li>
                            <p>Vertragliche Leistungen und Service.</p>
                        </li>
                        <li>
                            <p>Verwaltung und Beantwortung von Anfragen.</p>
                        </li>
                        <li>
                            <p>Zielgruppenbildung (Bestimmung von für Marketingzwecke relevanten Zielgruppen oder sonstige Ausgabe von Inhalten).</p>
                        </li>
                    </ul>
                    <h2></h2>
                    <h3 id="m13">Maßgebliche Rechtsgrundlagen</h3>
                    <p>Im Folgenden teilen wir die Rechtsgrundlagen der Datenschutzgrundverordnung (DSGVO), auf deren Basis wir die personenbezogenen Daten verarbeiten, mit. Bitte beachten Sie, dass zusätzlich zu den Regelungen der DSGVO die nationalen Datenschutzvorgaben in Ihrem bzw. unserem Wohn- und Sitzland gelten können.</p>
                    <ul>
                        <li>
                            <p><strong>Einwilligung (Art. 6 Abs. 1 S. 1 lit. a DSGVO)</strong> - Die betroffene Person hat ihre Einwilligung in die Verarbeitung der sie betreffenden personenbezogenen Daten für einen spezifischen Zweck oder mehrere bestimmte Zwecke gegeben.</p>
                        </li>
                        <li>
                            <p><strong>Vertragserfüllung und vorvertragliche Anfragen (Art. 6 Abs. 1 S. 1 lit. b. DSGVO)</strong> - Die Verarbeitung ist für die Erfüllung eines Vertrags, dessen Vertragspartei die betroffene Person ist, oder zur Durchführung vorvertraglicher Maßnahmen erforderlich, die auf Anfrage der betroffenen Person erfolgen.</p>
                        </li>
                        <li>
                            <p><strong>Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO)</strong> - Die Verarbeitung ist zur Wahrung der berechtigten Interessen des Verantwortlichen oder eines Dritten erforderlich, sofern nicht die Interessen oder Grundrechte und Grundfreiheiten der betroffenen Person, die den Schutz personenbezogener Daten erfordern, überwiegen.</p>
                        </li>
                    </ul>
                    <p><strong>Nationale Datenschutzregelungen in Deutschland</strong>: Zusätzlich zu den Datenschutzregelungen der Datenschutz-Grundverordnung gelten nationale Regelungen zum Datenschutz in Deutschland. Hierzu gehört insbesondere das Gesetz zum Schutz vor Missbrauch personenbezogener Daten bei der Datenverarbeitung (Bundesdatenschutzgesetz – BDSG). Das BDSG enthält insbesondere Spezialregelungen zum Recht auf Auskunft, zum Recht auf Löschung, zum Widerspruchsrecht, zur Verarbeitung besonderer Kategorien personenbezogener Daten, zur Verarbeitung für andere Zwecke und zur Übermittlung sowie automatisierten Entscheidungsfindung im Einzelfall einschließlich Profiling. Des Weiteren regelt es die Datenverarbeitung für Zwecke des Beschäftigungsverhältnisses (§ 26 BDSG), insbesondere im Hinblick auf die Begründung, Durchführung oder Beendigung von Beschäftigungsverhältnissen sowie die Einwilligung von Beschäftigten. Ferner können Landesdatenschutzgesetze der einzelnen Bundesländer zur Anwendung gelangen.</p>
                    <ul class="m-elements"></ul>
                    <h2 id="m27">Sicherheitsmaßnahmen</h2>
                    <p>Wir treffen nach Maßgabe der gesetzlichen Vorgaben unter Berücksichtigung des Stands der Technik, der Implementierungskosten und der Art, des Umfangs, der Umstände und der Zwecke der Verarbeitung sowie der unterschiedlichen Eintrittswahrscheinlichkeiten und des Ausmaßes der Bedrohung der Rechte und Freiheiten natürlicher Personen geeignete technische und organisatorische Maßnahmen, um ein dem Risiko angemessenes Schutzniveau zu gewährleisten.</p>
                    <p>Zu den Maßnahmen gehören insbesondere die Sicherung der Vertraulichkeit, Integrität und Verfügbarkeit von Daten durch Kontrolle des physischen und elektronischen Zugangs zu den Daten als auch des sie betreffenden Zugriffs, der Eingabe, der Weitergabe, der Sicherung der Verfügbarkeit und ihrer Trennung. Des Weiteren haben wir Verfahren eingerichtet, die eine Wahrnehmung von Betroffenenrechten, die Löschung von Daten und Reaktionen auf die Gefährdung der Daten gewährleisten. Ferner berücksichtigen wir den Schutz personenbezogener Daten bereits bei der Entwicklung bzw. Auswahl von Hardware, Software sowie Verfahren entsprechend dem Prinzip des Datenschutzes, durch Technikgestaltung und durch datenschutzfreundliche Voreinstellungen.</p>
                    <p><strong>Kürzung der IP-Adresse</strong>: Sofern es uns möglich ist oder eine Speicherung der IP-Adresse nicht erforderlich ist, kürzen wir oder lassen Ihre IP-Adresse kürzen. Im Fall der Kürzung der IP-Adresse, auch als "IP-Masking" bezeichnet, wird das letzte Oktett, d.h., die letzten beiden Zahlen einer IP-Adresse, gelöscht (die IP-Adresse ist in diesem Kontext eine einem Internetanschluss durch den Online-Zugangs-Provider individuell zugeordnete Kennung). Mit der Kürzung der IP-Adresse soll die Identifizierung einer Person anhand ihrer IP-Adresse verhindert oder wesentlich erschwert werden.</p>
                    <p><strong>SSL-Verschlüsselung (https)</strong>: Um Ihre via unser Online-Angebot übermittelten Daten zu schützen, nutzen wir eine SSL-Verschlüsselung. Sie erkennen derart verschlüsselte Verbindungen an dem Präfix https:// in der Adresszeile Ihres Browsers.</p>
                    <h2 id="m25">Übermittlung und Offenbarung von personenbezogenen Daten</h2>
                    <p>Im Rahmen unserer Verarbeitung von personenbezogenen Daten kommt es vor, dass die Daten an andere Stellen, Unternehmen, rechtlich selbstständige Organisationseinheiten oder Personen übermittelt oder sie ihnen gegenüber offengelegt werden. Zu den Empfängern dieser Daten können z.B. Zahlungsinstitute im Rahmen von Zahlungsvorgängen, mit IT-Aufgaben beauftragte Dienstleister oder Anbieter von Diensten und Inhalten, die in eine Webseite eingebunden werden, gehören. In solchen Fall beachten wir die gesetzlichen Vorgaben und schließen insbesondere entsprechende Verträge bzw. Vereinbarungen, die dem Schutz Ihrer Daten dienen, mit den Empfängern Ihrer Daten ab.</p>
                    <h2 id="m24">Datenverarbeitung in Drittländern</h2>
                    <p>Sofern wir Daten in einem Drittland (d.h., außerhalb der Europäischen Union (EU), des Europäischen Wirtschaftsraums (EWR)) verarbeiten oder die Verarbeitung im Rahmen der Inanspruchnahme von Diensten Dritter oder der Offenlegung bzw. Übermittlung von Daten an andere Personen, Stellen oder Unternehmen stattfindet, erfolgt dies nur im Einklang mit den gesetzlichen Vorgaben. </p>
                    <p>Vorbehaltlich ausdrücklicher Einwilligung oder vertraglich oder gesetzlich erforderlicher Übermittlung verarbeiten oder lassen wir die Daten nur in Drittländern mit einem anerkannten Datenschutzniveau, zu denen die unter dem "Privacy-Shield" zertifizierten US-Verarbeiter gehören, oder auf Grundlage besonderer Garantien, wie z.B. vertraglicher Verpflichtung durch sogenannte Standardschutzklauseln der EU-Kommission, des Vorliegens von Zertifizierungen oder verbindlicher interner Datenschutzvorschriften, verarbeiten (Art. 44 bis 49 DSGVO, Informationsseite der EU-Kommission: <a href="https://ec.europa.eu/info/law/law-topic/data-protection/international-dimension-data-protection_de" target="_blank">https://ec.europa.eu/info/law/law-topic/data-protection/international-dimension-data-protection_de</a> ).</p>
                    <h2 id="m134">Einsatz von Cookies</h2>
                    <p>Als "Cookies“ werden kleine Dateien bezeichnet, die auf Geräten der Nutzer gespeichert werden. Mittels Cookies können unterschiedliche Angaben gespeichert werden. Zu den Angaben können z.B. die Spracheinstellungen auf einer Webseite, der Loginstatus, ein Warenkorb oder die Stelle, an der ein Video geschaut wurde, gehören. </p>
                    <p>Cookies werden im Regelfall auch dann eingesetzt, wenn die Interessen eines Nutzers oder sein Verhalten (z.B. Betrachten bestimmter Inhalte, Nutzen von Funktionen etc.) auf einzelnen Webseiten in einem Nutzerprofil gespeichert werden. Solche Profile dienen dazu, den Nutzern z.B. Inhalte anzuzeigen, die ihren potentiellen Interessen entsprechen. Dieses Verfahren wird auch als "Tracking", d.h., Nachverfolgung der potentiellen Interessen der Nutzer bezeichnet. Zu dem Begriff der Cookies zählen wir ferner andere Technologien, die die gleichen Funktionen wie Cookies erfüllen (z.B., wenn Angaben der Nutzer anhand pseudonymer Onlinekennzeichnungen gespeichert werden, auch als "Nutzer-IDs" bezeichnet).</p>
                    <p>Soweit wir Cookies oder "Tracking"-Technologien einsetzen, informieren wir Sie gesondert in unserer Datenschutzerklärung. </p>
                    <p><strong>Hinweise zu Rechtsgrundlagen: </strong> Auf welcher Rechtsgrundlage wir Ihre personenbezogenen Daten mit Hilfe von Cookies verarbeiten, hängt davon ab, ob wir Sie um eine Einwilligung bitten. Falls dies zutrifft und Sie in die Nutzung von Cookies einwilligen, ist die Rechtsgrundlage der Verarbeitung Ihrer Daten die erklärte Einwilligung. Andernfalls werden die mithilfe von Cookies verarbeiteten Daten auf Grundlage unserer berechtigten Interessen (z.B. an einem betriebswirtschaftlichen Betrieb unseres Onlineangebotes und dessen Verbesserung) verarbeitet oder, wenn der Einsatz von Cookies erforderlich ist, um unsere vertraglichen Verpflichtungen zu erfüllen.</p>
                    <p><strong>Widerruf und Widerspruch (Opt-Out): </strong> Unabhängig davon, ob die Verarbeitung auf Grundlage einer Einwilligung oder gesetzlichen Erlaubnis erfolgt, haben Sie jederzeit die Möglichkeit, eine erteilte Einwilligung zu widerrufen oder der Verarbeitung Ihrer Daten durch Cookie-Technologien zu widersprechen (zusammenfassend als "Opt-Out" bezeichnet).</p>
                    <p>Sie können Ihren Widerspruch zunächst mittels der Einstellungen Ihres Browsers erklären, z.B., indem Sie die Nutzung von Cookies deaktivieren (wobei hierdurch auch die Funktionsfähigkeit unseres Onlineangebotes eingeschränkt werden kann).</p>
                    <p>Ein Widerspruch gegen den Einsatz von Cookies zu Zwecken des Onlinemarketings kann mittels einer Vielzahl von Diensten, vor allem im Fall des Trackings, über die US-amerikanische Seite <a href="http://www.aboutads.info/choices/" target="_blank">http://www.aboutads.info/choices/</a> oder die EU-Seite <a href="http://www.youronlinechoices.com/" target="_blank">http://www.youronlinechoices.com/</a> oder generell auf <a href="http://optout.aboutads.info" target="_blank">http://optout.aboutads.info</a> erklärt werden.</p>
                    <p><strong>Verarbeitung von Cookie-Daten auf Grundlage einer Einwilligung</strong>: Bevor wir Daten im Rahmen der Nutzung von Cookies verarbeiten oder verarbeiten lassen, bitten wir die Nutzer um eine jederzeit widerrufbare Einwilligung. Bevor die Einwilligung nicht ausgesprochen wurde, werden allenfalls Cookies eingesetzt, die für den Betrieb unseres Onlineangebotes erforderlich sind. Deren Einsatz erfolgt auf der Grundlage unseres Interesses und des Interesses der Nutzer an der erwarteten Funktionsfähigkeit unseres Onlineangebotes.</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten).</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a DSGVO), Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                    </ul>
                    <h2 id="m367">Registrierung und Anmeldung</h2>
                    <p>Nutzer können ein Nutzerkonto anlegen. Im Rahmen der Registrierung werden den Nutzern die erforderlichen Pflichtangaben mitgeteilt und zu Zwecken der Bereitstellung des Nutzerkontos auf Grundlage vertraglicher Pflichterfüllung verarbeitet. Zu den verarbeiteten Daten gehören insbesondere die Login-Informationen (Name, Passwort sowie eine E-Mail-Adresse). Die im Rahmen der Registrierung eingegebenen Daten werden für die Zwecke der Nutzung des Nutzerkontos und dessen Zwecks verwendet. </p>
                    <p>Die Nutzer können über Vorgänge, die für deren Nutzerkonto relevant sind, wie z.B. technische Änderungen, per E-Mail informiert werden. Wenn Nutzer ihr Nutzerkonto gekündigt haben, werden deren Daten im Hinblick auf das Nutzerkonto, vorbehaltlich einer gesetzlichen Aufbewahrungspflicht, gelöscht. Es obliegt den Nutzern, ihre Daten bei erfolgter Kündigung vor dem Vertragsende zu sichern. Wir sind berechtigt, sämtliche während der Vertragsdauer gespeicherte Daten des Nutzers unwiederbringlich zu löschen.</p>
                    <p>Im Rahmen der Inanspruchnahme unserer Registrierungs- und Anmeldefunktionen sowie der Nutzung des Nutzerkontos speichern wir die IP-Adresse und den Zeitpunkt der jeweiligen Nutzerhandlung. Die Speicherung erfolgt auf Grundlage unserer berechtigten Interessen als auch jener der Nutzer an einem Schutz vor Missbrauch und sonstiger unbefugter Nutzung. Eine Weitergabe dieser Daten an Dritte erfolgt grundsätzlich nicht, es sei denn, sie ist zur Verfolgung unserer Ansprüche erforderlich oder es besteht hierzu besteht eine gesetzliche Verpflichtung.</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Bestandsdaten (z.B. Namen, Adressen), Kontaktdaten (z.B. E-Mail, Telefonnummern), Inhaltsdaten (z.B. Texteingaben, Fotografien, Videos), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten).</p>
                        </li>
                        <li>
                            <p><strong>Zwecke der Verarbeitung:</strong> Vertragliche Leistungen und Service, Sicherheitsmaßnahmen, Verwaltung und Beantwortung von Anfragen.</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a DSGVO), Vertragserfüllung und vorvertragliche Anfragen (Art. 6 Abs. 1 S. 1 lit. b. DSGVO), Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                    </ul>
                    <h2 id="m182">Kontaktaufnahme</h2>
                    <p>Bei der Kontaktaufnahme mit uns (z.B. per Kontaktformular, E-Mail, Telefon oder via soziale Medien) werden die Angaben der anfragenden Personen verarbeitet, soweit dies zur Beantwortung der Kontaktanfragen und etwaiger angefragter Maßnahmen erforderlich ist.</p>
                    <p>Die Beantwortung der Kontaktanfragen im Rahmen von vertraglichen oder vorvertraglichen Beziehungen erfolgt zur Erfüllung unserer vertraglichen Pflichten oder zur Beantwortung von (vor)vertraglichen Anfragen und im Übrigen auf Grundlage der berechtigten Interessen an der Beantwortung der Anfragen.</p>
                    <p><strong>Chat-Funktion</strong>: Zu Zwecken der Kommunikation und der Beantwortung von Anfragen bieten wir innerhalb unseres Onlineangebotes eine Chat-Funktion an. Die Eingaben der Nutzer innerhalb des Chats werden für Zwecke der Beantwortung ihrer Anfragen verarbeitet.</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Bestandsdaten (z.B. Namen, Adressen), Kontaktdaten (z.B. E-Mail, Telefonnummern), Inhaltsdaten (z.B. Texteingaben, Fotografien, Videos), Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Kommunikationspartner, Interessenten.</p>
                        </li>
                        <li>
                            <p><strong>Zwecke der Verarbeitung:</strong> Kontaktanfragen und Kommunikation, Verwaltung und Beantwortung von Anfragen.</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Vertragserfüllung und vorvertragliche Anfragen (Art. 6 Abs. 1 S. 1 lit. b. DSGVO), Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                    </ul>
                    <h2 id="m391">Kommunikation via Messenger</h2>
                    <p>Wir setzen zu Zwecken der Kommunikation Messenger-Dienste ein und bitten daher darum, die nachfolgenden Hinweise zur Funktionsfähigkeit der Messenger, zur Verschlüsselung, zur Nutzung der Metadaten der Kommunikation und zu Ihren Widerspruchsmöglichkeiten zu beachten.</p>
                    <p>Sie können uns auch auf alternativen Wegen, z.B. via Telefon oder E-Mail, kontaktieren. Bitte nutzen Sie die Ihnen mitgeteilten Kontaktmöglichkeiten oder die innerhalb unseres Onlineangebotes angegebenen Kontaktmöglichkeiten.</p>
                    <p>Im Fall einer Ende-zu-Ende-Verschlüsselung von Inhalten (d.h., der Inhalt Ihrer Nachricht und Anhänge) weisen wir darauf hin, dass die Kommunikationsinhalte (d.h., der Inhalt der Nachricht und angehängte Bilder) von Ende zu Ende verschlüsselt werden. Das bedeutet, dass der Inhalt der Nachrichten nicht einsehbar ist, nicht einmal durch die Messenger-Anbieter selbst. Sie sollten immer eine aktuelle Version der Messenger mit aktivierter Verschlüsselung nutzen, damit die Verschlüsselung der Nachrichteninhalte sichergestellt ist. </p>
                    <p>Wir weisen unsere Kommunikationspartner jedoch zusätzlich darauf hin, dass die Anbieter der Messenger zwar nicht den Inhalt einsehen, aber in Erfahrung bringen können, dass und wann Kommunikationspartner mit uns kommunizieren sowie technische Informationen zum verwendeten Gerät der Kommunikationspartner und je nach Einstellungen ihres Gerätes auch Standortinformationen (sogenannte Metadaten) verarbeitet werden.</p>
                    <p><strong>Hinweise zu Rechtsgrundlagen: </strong> Sofern wir Kommunikationspartner vor der Kommunikation mit ihnen via Messenger um eine Erlaubnis bitten, ist die Rechtsgrundlage unserer Verarbeitung ihrer Daten deren Einwilligung. Im Übrigen, falls wir nicht um eine Einwilligung bitten und sie z.B. von sich aus Kontakt mit uns aufnehmen, nutzen wir Messenger im Verhältnis zu unseren Vertragspartnern sowie im Rahmen der Vertragsanbahnung als eine vertragliche Maßnahme und im Fall anderer Interessenten und Kommunikationspartner auf Grundlage unserer berechtigten Interessen an einer schnellen und effizienten Kommunikation und Erfüllung der Bedürfnisse unser Kommunikationspartner an der Kommunikation via Messengern. Ferner weisen wir Sie darauf hin, dass wir die uns mitgeteilten Kontaktdaten ohne Ihre Einwilligung nicht erstmalig an die Messenger übermitteln.</p>
                    <p><strong>Widerruf, Widerspruch und Löschung:</strong> Sie können jederzeit eine erteilte Einwilligung widerrufen und der Kommunikation mit uns via Messenger jederzeit widersprechen. Im Fall der Kommunikation via Messenger löschen wir die Nachrichten entsprechend unseren generellen Löschrichtlinien (d.h. z.B., wie oben beschrieben, nach Ende vertraglicher Beziehungen, im Kontext von Archivierungsvorgaben etc.) und sonst, sobald wir davon ausgehen können, etwaige Auskünfte der Kommunikationspartner beantwortet zu haben, wenn kein Rückbezug auf eine vorhergehende Konversation zu erwarten ist und der Löschung keine gesetzlichen Aufbewahrungspflichten entgegenstehen.</p>
                    <p><strong>Vorbehalt des Verweises auf andere Kommunikationswege:</strong> Zum Abschluss möchten wir darauf hinweisen, dass wir uns aus Gründen Ihrer Sicherheit vorbehalten, Anfragen über Messenger nicht zu beantworten. Das ist der Fall, wenn z.B. Vertragsinterna besonderer Geheimhaltung bedürfen oder eine Antwort über den Messenger den formellen Ansprüchen nicht genügt. In solchen Fällen verweisen wir Sie auf adäquatere Kommunikationswege.</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Kontaktdaten (z.B. E-Mail, Telefonnummern), Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Kommunikationspartner.</p>
                        </li>
                        <li>
                            <p><strong>Zwecke der Verarbeitung:</strong> Kontaktanfragen und Kommunikation, Direktmarketing (z.B. per E-Mail oder postalisch).</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a DSGVO), Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                    </ul>
                    <h2 id="m225">Bereitstellung des Onlineangebotes und Webhosting</h2>
                    <p>Um unser Onlineangebot sicher und effizient bereitstellen zu können, nehmen wir die Leistungen von einem oder mehreren Webhosting-Anbietern in Anspruch, von deren Servern (bzw. von ihnen verwalteten Servern) das Onlineangebot abgerufen werden kann. Zu diesen Zwecken können wir Infrastruktur- und Plattformdienstleistungen, Rechenkapazität, Speicherplatz und Datenbankdienste sowie Sicherheitsleistungen und technische Wartungsleistungen in Anspruch nehmen.</p>
                    <p>Zu den im Rahmen der Bereitstellung des Hostingangebotes verarbeiteten Daten können alle die Nutzer unseres Onlineangebotes betreffenden Angaben gehören, die im Rahmen der Nutzung und der Kommunikation anfallen. Hierzu gehören regelmäßig die IP-Adresse, die notwendig ist, um die Inhalte von Onlineangeboten an Browser ausliefern zu können, und alle innerhalb unseres Onlineangebotes oder von Webseiten getätigten Eingaben.</p>
                    <p><strong>E-Mail-Versand und -Hosting</strong>: Die von uns in Anspruch genommenen Webhosting-Leistungen umfassen ebenfalls den Versand, den Empfang sowie die Speicherung von E-Mails. Zu diesen Zwecken werden die Adressen der Empfänger sowie Absender als auch weitere Informationen betreffend den E-Mailversand (z.B. die beteiligten Provider) sowie die Inhalte der jeweiligen E-Mails verarbeitet. Die vorgenannten Daten können ferner zu Zwecken der Erkennung von SPAM verarbeitet werden. Wir bitten darum, zu beachten, dass E-Mails im Internet grundsätzlich nicht verschlüsselt versendet werden. Im Regelfall werden E-Mails zwar auf dem Transportweg verschlüsselt, aber (sofern kein sogenanntes Ende-zu-Ende-Verschlüsselungsverfahren eingesetzt wird) nicht auf den Servern, von denen sie abgesendet und empfangen werden. Wir können daher für den Übertragungsweg der E-Mails zwischen dem Absender und dem Empfang auf unserem Server keine Verantwortung übernehmen.</p>
                    <p><strong>Erhebung von Zugriffsdaten und Logfiles</strong>: Wir selbst (bzw. unser Webhostinganbieter) erheben Daten zu jedem Zugriff auf den Server (sogenannte Serverlogfiles). Zu den Serverlogfiles können die Adresse und Name der abgerufenen Webseiten und Dateien, Datum und Uhrzeit des Abrufs, übertragene Datenmengen, Meldung über erfolgreichen Abruf, Browsertyp nebst Version, das Betriebssystem des Nutzers, Referrer URL (die zuvor besuchte Seite) und im Regelfall IP-Adressen und der anfragende Provider gehören.</p>
                    <p>Die Serverlogfiles können zum einen zu Zwecken der Sicherheit eingesetzt werden, z.B., um eine Überlastung der Server zu vermeiden (insbesondere im Fall von missbräuchlichen Angriffen, sogenannten DDoS-Attacken) und zum anderen, um die Auslastung der Server und ihre Stabilität sicherzustellen.</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Inhaltsdaten (z.B. Texteingaben, Fotografien, Videos), Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten).</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                    </ul>
                    <h2 id="m29">Cloud-Dienste</h2>
                    <p>Wir nutzen über das Internet zugängliche und auf den Servern ihrer Anbieter ausgeführte Softwaredienste (sogenannte "Cloud-Dienste", auch bezeichnet als "Software as a Service") für die folgenden Zwecke: Dokumentenspeicherung und Verwaltung, Kalenderverwaltung, E-Mail-Versand, Tabellenkalkulationen und Präsentationen, Austausch von Dokumenten, Inhalten und Informationen mit bestimmten Empfängern oder Veröffentlichung von Webseiten, Formularen oder sonstigen Inhalten und Informationen sowie Chats und Teilnahme an Audio- und Videokonferenzen.</p>
                    <p>In diesem Rahmen können personenbezogenen Daten verarbeitet und auf den Servern der Anbieter gespeichert werden, soweit diese Bestandteil von Kommunikationsvorgängen mit uns sind oder von uns sonst, wie im Rahmen dieser Datenschutzerklärung dargelegt, verarbeitet werden. Zu diesen Daten können insbesondere Stammdaten und Kontaktdaten der Nutzer, Daten zu Vorgängen, Verträgen, sonstigen Prozessen und deren Inhalte gehören. Die Anbieter der Cloud-Dienste verarbeiten ferner Nutzungsdaten und Metadaten, die von ihnen zu Sicherheitszwecken und zur Serviceoptimierung verwendet werden.</p>
                    <p>Sofern wir mit Hilfe der Cloud-Dienste für andere Nutzer oder öffentlich zugängliche Webseiten Formulare o.a. Dokumente und Inhalte bereitstellen, können die Anbieter Cookies auf den Geräten der Nutzer für Zwecke der Webanalyse oder, um sich Einstellungen der Nutzer (z.B. im Fall der Mediensteuerung) zu merken, speichern.</p>
                    <p><strong>Hinweise zu Rechtsgrundlagen:</strong> Sofern wir um eine Einwilligung in den Einsatz der Cloud-Dienste bitten, ist die Rechtsgrundlage der Verarbeitung die Einwilligung. Ferner kann deren Einsatz ein Bestandteil unserer (vor)vertraglichen Leistungen sein, sofern der Einsatz der Cloud-Dienste in diesem Rahmen vereinbart wurde. Ansonsten werden die Daten der Nutzer auf Grundlage unserer berechtigten Interessen (d.h., Interesse an effizienten und sicheren Verwaltungs- und Kollaborationsprozessen) verarbeitet</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Bestandsdaten (z.B. Namen, Adressen), Kontaktdaten (z.B. E-Mail, Telefonnummern), Inhaltsdaten (z.B. Texteingaben, Fotografien, Videos), Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Kunden, Beschäftigte (z.B. Angestellte, Bewerber, ehemalige Mitarbeiter), Interessenten, Kommunikationspartner.</p>
                        </li>
                        <li>
                            <p><strong>Zwecke der Verarbeitung:</strong> Büro- und Organisationsverfahren.</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a DSGVO), Vertragserfüllung und vorvertragliche Anfragen (Art. 6 Abs. 1 S. 1 lit. b. DSGVO), Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                    </ul>
                    <p><strong>Eingesetzte Dienste und Diensteanbieter:</strong></p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Google Cloud-Dienste:</strong> Cloud-Speicher-Dienste; Dienstanbieter: Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Irland, Mutterunternehmen: Google LLC, 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA; Website: <a href="https://cloud.google.com/" target="_blank">https://cloud.google.com/</a>; Datenschutzerklärung: <a href="https://www.google.com/policies/privacy" target="_blank">https://www.google.com/policies/privacy</a>, Sicherheitshinweise: <a href="https://cloud.google.com/security/privacy" target="_blank">https://cloud.google.com/security/privacy</a>; Privacy Shield (Gewährleistung Datenschutzniveau bei Verarbeitung von Daten in den USA): <a href="https://www.privacyshield.gov/participant?id=a2zt0000000000001L5AAI&status=Aktive" target="_blank">https://www.privacyshield.gov/participant?id=a2zt0000000000001L5AAI&status=Aktive</a>; Standardvertragsklauseln (Gewährleistung Datenschutzniveau bei Verarbeitung im Drittland): <a href="https://cloud.google.com/terms/data-processing-terms" target="_blank">https://cloud.google.com/terms/data-processing-terms</a>; Zusätzliche Hinweise zum Datenschutz: <a href="https://cloud.google.com/terms/data-processing-terms" target="_blank">https://cloud.google.com/terms/data-processing-terms</a>.</p>
                        </li>
                    </ul>
                    <h2 id="m17">Newsletter und Breitenkommunikation</h2>
                    <p>Wir versenden Newsletter, E-Mails und weitere elektronische Benachrichtigungen (nachfolgend "Newsletter“) nur mit der Einwilligung der Empfänger oder einer gesetzlichen Erlaubnis. Sofern im Rahmen einer Anmeldung zum Newsletter dessen Inhalte konkret umschrieben werden, sind sie für die Einwilligung der Nutzer maßgeblich. Im Übrigen enthalten unsere Newsletter Informationen zu unseren Leistungen und uns.</p>
                    <p>Um sich zu unseren Newslettern anzumelden, reicht es grundsätzlich aus, wenn Sie Ihre E-Mail-Adresse angeben. Wir können Sie jedoch bitten, einen Namen, zwecks persönlicher Ansprache im Newsletter, oder weitere Angaben, sofern diese für die Zwecke des Newsletters erforderlich sind, zu tätigen.</p>
                    <p><strong>Double-Opt-In-Verfahren:</strong> Die Anmeldung zu unserem Newsletter erfolgt grundsätzlich in einem sogenannte Double-Opt-In-Verfahren. D.h., Sie erhalten nach der Anmeldung eine E-Mail, in der Sie um die Bestätigung Ihrer Anmeldung gebeten werden. Diese Bestätigung ist notwendig, damit sich niemand mit fremden E-Mail-Adressen anmelden kann. Die Anmeldungen zum Newsletter werden protokolliert, um den Anmeldeprozess entsprechend den rechtlichen Anforderungen nachweisen zu können. Hierzu gehört die Speicherung des Anmelde- und des Bestätigungszeitpunkts als auch der IP-Adresse. Ebenso werden die Änderungen Ihrer bei dem Versanddienstleister gespeicherten Daten protokolliert.</p>
                    <p><strong>Löschung und Einschränkung der Verarbeitung: </strong> Wir können die ausgetragenen E-Mail-Adressen bis zu drei Jahren auf Grundlage unserer berechtigten Interessen speichern, bevor wir sie löschen, um eine ehemals gegebene Einwilligung nachweisen zu können. Die Verarbeitung dieser Daten wird auf den Zweck einer möglichen Abwehr von Ansprüchen beschränkt. Ein individueller Löschungsantrag ist jederzeit möglich, sofern zugleich das ehemalige Bestehen einer Einwilligung bestätigt wird. Im Fall von Pflichten zur dauerhaften Beachtung von Widersprüchen behalten wir uns die Speicherung der E-Mail-Adresse alleine zu diesem Zweck in einer Sperrliste (sogenannte "Blacklist") vor.</p>
                    <p>Die Protokollierung des Anmeldeverfahrens erfolgt auf Grundlage unserer berechtigten Interessen zu Zwecken des Nachweises seines ordnungsgemäßen Ablaufs. Soweit wir einen Dienstleister mit dem Versand von E-Mails beauftragen, erfolgt dies auf Grundlage unserer berechtigten Interessen an einem effizienten und sicheren Versandsystem.</p>
                    <p><strong>Hinweise zu Rechtsgrundlagen:</strong> Der Versand der Newsletter erfolgt auf Grundlage einer Einwilligung der Empfänger oder, falls eine Einwilligung nicht erforderlich ist, auf Grundlage unserer berechtigten Interessen am Direktmarketing, sofern und soweit diese gesetzlich, z.B. im Fall von Bestandskundenwerbung, erlaubt ist. Soweit wir einen Dienstleister mit dem Versand von E-Mails beauftragen, geschieht dies auf der Grundlage unserer berechtigten Interessen. Das Registrierungsverfahren wird auf der Grundlage unserer berechtigten Interessen aufgezeichnet, um nachzuweisen, dass es in Übereinstimmung mit dem Gesetz durchgeführt wurde.</p>
                    <p><strong>Inhalte</strong>: Informationen zu uns, unseren Leistungen, Aktionen und Angeboten.</p>
                    <p><strong>Erfolgsmessung</strong>: Die Newsletter enthalten einen sogenannte "web-beacon“, d.h., eine pixelgroße Datei, die beim Öffnen des Newsletters von unserem Server, bzw., sofern wir einen Versanddienstleister einsetzen, von dessen Server abgerufen wird. Im Rahmen dieses Abrufs werden zunächst technische Informationen, wie Informationen zum Browser und Ihrem System, als auch Ihre IP-Adresse und der Zeitpunkt des Abrufs, erhoben. </p>
                    <p>Diese Informationen werden zur technischen Verbesserung unseres Newsletters anhand der technischen Daten oder der Zielgruppen und ihres Leseverhaltens auf Basis ihrer Abruforte (die mit Hilfe der IP-Adresse bestimmbar sind) oder der Zugriffszeiten genutzt. Diese Analyse beinhaltet ebenfalls die Feststellung, ob die Newsletter geöffnet werden, wann sie geöffnet werden und welche Links geklickt werden. Diese Informationen können aus technischen Gründen zwar den einzelnen Newsletterempfängern zugeordnet werden. Es ist jedoch weder unser Bestreben noch, sofern eingesetzt, das des Versanddienstleisters, einzelne Nutzer zu beobachten. Die Auswertungen dienen uns vielmehr dazu, die Lesegewohnheiten unserer Nutzer zu erkennen und unsere Inhalte an sie anzupassen oder unterschiedliche Inhalte entsprechend den Interessen unserer Nutzer zu versenden.</p>
                    <p>Die Auswertung des Newsletters und die Erfolgsmessung erfolgen, vorbehaltlich einer ausdrücklichen Einwilligung der Nutzer, auf Grundlage unserer berechtigten Interessen zu Zwecken des Einsatzes eines nutzerfreundlichen sowie sicheren Newslettersystems, welches sowohl unseren geschäftlichen Interessen dient, als auch den Erwartungen der Nutzer entspricht.</p>
                    <p>Ein getrennter Widerruf der Erfolgsmessung ist leider nicht möglich, in diesem Fall muss das gesamte Newsletterabonnement gekündigt, bzw. muss ihm widersprochen werden.</p>
                    <p><strong>Voraussetzung der Inanspruchnahme kostenloser Leistungen</strong>: Die Einwilligungen in den Versand von Mailings kann als Voraussetzung zur Inanspruchnahme kostenloser Leistungen (z.B. Zugang zu bestimmten Inhalten oder Teilnahme an bestimmten Aktionen) abhängig gemacht werden. Sofern die Nutzer die kostenlose Leistung in Anspruch nehmen möchten, ohne sich zum Newsletter anzumelden, bitten wir Sie um eine Kontaktaufnahme.</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Bestandsdaten (z.B. Namen, Adressen), Kontaktdaten (z.B. E-Mail, Telefonnummern), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen), Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Kommunikationspartner, Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten).</p>
                        </li>
                        <li>
                            <p><strong>Zwecke der Verarbeitung:</strong> Direktmarketing (z.B. per E-Mail oder postalisch), Vertragliche Leistungen und Service.</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a DSGVO), Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                        <li>
                            <p><strong>Widerspruchsmöglichkeit (Opt-Out):</strong> Sie können den Empfang unseres Newsletters jederzeit kündigen, d.h. Ihre Einwilligungen widerrufen, bzw. dem weiteren Empfang widersprechen. Einen Link zur Kündigung des Newsletters finden Sie entweder am Ende eines jeden Newsletters oder können sonst eine der oben angegebenen Kontaktmöglichkeiten, vorzugswürdig E-Mail, hierzu nutzen.</p>
                        </li>
                    </ul>
                    <h2 id="m263">Webanalyse und Optimierung</h2>
                    <p>Die Webanalyse (auch als "Reichweitenmessung" bezeichnet) dient der Auswertung der Besucherströme unseres Onlineangebotes und kann Verhalten, Interessen oder demographische Informationen zu den Besuchern, wie z.B. das Alter oder das Geschlecht, als pseudonyme Werte umfassen. Mit Hilfe der Reichweitenanalyse können wir z.B. erkennen, zu welcher Zeit unser Onlineangebot oder dessen Funktionen oder Inhalte am häufigsten genutzt werden oder zur Wiederverwendung einladen. Ebenso können wir nachvollziehen, welche Bereiche der Optimierung bedürfen. </p>
                    <p>Neben der Webanalyse können wir auch Testverfahren einsetzen, um z.B. unterschiedliche Versionen unseres Onlineangebotes oder seiner Bestandteile zu testen und optimieren.</p>
                    <p>Zu diesen Zwecken können sogenannte Nutzerprofile angelegt und in einer Datei (sogenannte "Cookie") gespeichert oder ähnliche Verfahren mit dem gleichen Zweck genutzt werden. Zu diesen Angaben können z.B. betrachtete Inhalte, besuchte Webseiten und dort genutzte Elemente und technische Angaben, wie der verwendete Browser, das verwendete Computersystem sowie Angaben zu Nutzungszeiten gehören. Sofern Nutzer in die Erhebung ihrer Standortdaten eingewilligt haben, können je nach Anbieter auch diese verarbeitet werden.</p>
                    <p>Es werden ebenfalls die IP-Adressen der Nutzer gespeichert. Jedoch nutzen wir ein IP-Masking-Verfahren (d.h., Pseudonymisierung durch Kürzung der IP-Adresse) zum Schutz der Nutzer. Generell werden die im Rahmen von Webanalyse, A/B-Testings und Optimierung keine Klardaten der Nutzer (wie z.B. E-Mail-Adressen oder Namen) gespeichert, sondern Pseudonyme. D.h., wir als auch die Anbieter der eingesetzten Software kennen nicht die tatsächliche Identität der Nutzer, sondern nur den für Zwecke der jeweiligen Verfahren in deren Profilen gespeicherten Angaben.</p>
                    <p><strong>Hinweise zu Rechtsgrundlagen:</strong> Sofern wir die Nutzer um deren Einwilligung in den Einsatz der Drittanbieter bitten, ist die Rechtsgrundlage der Verarbeitung von Daten die Einwilligung. Ansonsten werden die Daten der Nutzer auf Grundlage unserer berechtigten Interessen (d.h. Interesse an effizienten, wirtschaftlichen und empfängerfreundlichen Leistungen) verarbeitet. In diesem Zusammenhang möchten wir Sie auch auf die Informationen zur Verwendung von Cookies in dieser Datenschutzerklärung hinweisen.</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten).</p>
                        </li>
                        <li>
                            <p><strong>Zwecke der Verarbeitung:</strong> Reichweitenmessung (z.B. Zugriffsstatistiken, Erkennung wiederkehrender Besucher), Tracking (z.B. interessens-/verhaltensbezogenes Profiling, Nutzung von Cookies), Besuchsaktionsauswertung, Profiling (Erstellen von Nutzerprofilen).</p>
                        </li>
                        <li>
                            <p><strong>Sicherheitsmaßnahmen:</strong> IP-Masking (Pseudonymisierung der IP-Adresse).</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a DSGVO), Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                    </ul>
                    <h2 id="m264">Onlinemarketing</h2>
                    <p>Wir verarbeiten personenbezogene Daten zu Zwecken des Onlinemarketings, worunter insbesondere die Darstellung von werbenden und sonstigen Inhalten (zusammenfassend als "Inhalte" bezeichnet) anhand potentieller Interessen der Nutzer sowie die Messung ihrer Effektivität fallen. </p>
                    <p>Zu diesen Zwecken werden sogenannte Nutzerprofile angelegt und in einer Datei (sogenannte "Cookie") gespeichert oder ähnliche Verfahren genutzt, mittels derer die für die Darstellung der vorgenannten Inhalte relevante Angaben zum Nutzer gespeichert werden. Zu diesen Angaben können z.B. betrachtete Inhalte, besuchte Webseiten, genutzte Onlinenetzwerke, aber auch Kommunikationspartner und technische Angaben, wie der verwendete Browser, das verwendete Computersystem sowie Angaben zu Nutzungszeiten gehören. Sofern Nutzer in die Erhebung ihrer Standortdaten eingewilligt haben, können auch diese verarbeitet werden.</p>
                    <p>Es werden ebenfalls die IP-Adressen der Nutzer gespeichert. Jedoch nutzen wir IP-Masking-Verfahren (d.h., Pseudonymisierung durch Kürzung der IP-Adresse) zum Schutz der Nutzer. Generell werden im Rahmen des Onlinemarketingverfahren keine Klardaten der Nutzer (wie z.B. E-Mail-Adressen oder Namen) gespeichert, sondern Pseudonyme. D.h., wir als auch die Anbieter der Onlinemarketingverfahren kennen nicht die tatsächlich Identität der Nutzer, sondern nur die in deren Profilen gespeicherten Angaben.</p>
                    <p>Die Angaben in den Profilen werden im Regelfall in den Cookies oder mittels ähnlicher Verfahren gespeichert. Diese Cookies können später generell auch auf anderen Webseiten die dasselbe Onlinemarketingverfahren einsetzen, ausgelesen und zu Zwecken der Darstellung von Inhalten analysiert als auch mit weiteren Daten ergänzt und auf dem Server des Onlinemarketingverfahrensanbieters gespeichert werden.</p>
                    <p>Ausnahmsweise können Klardaten den Profilen zugeordnet werden. Das ist der Fall, wenn die Nutzer z.B. Mitglieder eines sozialen Netzwerks sind, dessen Onlinemarketingverfahren wir einsetzen und das Netzwerk die Profile der Nutzer im den vorgenannten Angaben verbindet. Wir bitten darum, zu beachten, dass Nutzer mit den Anbietern zusätzliche Abreden, z.B. durch Einwilligung im Rahmen der Registrierung, treffen können.</p>
                    <p>Wir erhalten grundsätzlich nur Zugang zu zusammengefassten Informationen über den Erfolg unserer Werbeanzeigen. Jedoch können wir im Rahmen sogenannter Konversionsmessungen prüfen, welche unserer Onlinemarketingverfahren zu einer sogenannten Konversion geführt haben, d.h. z.B., zu einem Vertragsschluss mit uns. Die Konversionsmessung wird alleine zur Analyse des Erfolgs unserer Marketingmaßnahmen verwendet.</p>
                    <p><strong>Hinweise zu Rechtsgrundlagen:</strong> Sofern wir die Nutzer um deren Einwilligung in den Einsatz der Drittanbieter bitten, ist die Rechtsgrundlage der Verarbeitung von Daten die Einwilligung. Ansonsten werden die Daten der Nutzer auf Grundlage unserer berechtigten Interessen (d.h. Interesse an effizienten, wirtschaftlichen und empfängerfreundlichen Leistungen) verarbeitet. In diesem Zusammenhang möchten wir Sie auch auf die Informationen zur Verwendung von Cookies in dieser Datenschutzerklärung hinweisen.</p>
                    <p><strong>Facebook-Pixel</strong>: Mit Hilfe des Facebook-Pixels ist es Facebook zum einen möglich, die Besucher unseres Onlineangebotes als Zielgruppe für die Darstellung von Anzeigen (sogenannte "Facebook-Ads") zu bestimmen. Dementsprechend setzen wir das Facebook-Pixel ein, um die durch uns geschalteten Facebook-Ads nur solchen Facebook-Nutzern anzuzeigen, die auch ein Interesse an unserem Onlineangebot gezeigt haben oder die bestimmte Merkmale (z.B. Interesse an bestimmten Themen oder Produkten, die anhand der besuchten Webseiten ersichtlich werden) aufweisen, die wir an Facebook übermitteln (sogenannte "Custom Audiences“). Mit Hilfe des Facebook-Pixels möchten wir auch sicherstellen, dass unsere Facebook-Ads dem potentiellen Interesse der Nutzer entsprechen und nicht belästigend wirken. Mit Hilfe des Facebook-Pixels können wir ferner die Wirksamkeit der Facebook-Werbeanzeigen für statistische und Marktforschungszwecke nachvollziehen, indem wir sehen, ob Nutzer nach dem Klick auf eine Facebook-Werbeanzeige auf unsere Webseite weitergeleitet wurden (sogenannte "Konversionsmessung“).</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen), Standortdaten (Daten, die den Standort des Endgeräts eines Endnutzers angeben).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten), Interessenten.</p>
                        </li>
                        <li>
                            <p><strong>Zwecke der Verarbeitung:</strong> Tracking (z.B. interessens-/verhaltensbezogenes Profiling, Nutzung von Cookies), Remarketing, Besuchsaktionsauswertung, Interessenbasiertes und verhaltensbezogenes Marketing, Profiling (Erstellen von Nutzerprofilen), Konversionsmessung (Messung der Effektivität von Marketingmaßnahmen), Reichweitenmessung (z.B. Zugriffsstatistiken, Erkennung wiederkehrender Besucher), Zielgruppenbildung (Bestimmung von für Marketingzwecke relevanten Zielgruppen oder sonstige Ausgabe von Inhalten), Cross-Device Tracking (geräteübergreifende Verarbeitung von Nutzerdaten für Marketingzwecke).</p>
                        </li>
                        <li>
                            <p><strong>Sicherheitsmaßnahmen:</strong> IP-Masking (Pseudonymisierung der IP-Adresse).</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a DSGVO), Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                        <li>
                            <p><strong>Widerspruchsmöglichkeit (Opt-Out):</strong> Wir verweisen auf die Datenschutzhinweise der jeweiligen Anbieter und die zu den Anbietern angegebenen Widerspruchsmöglichkeiten (sog. \"Opt-Out\"). Sofern keine explizite Opt-Out-Möglichkeit angegeben wurde, besteht zum einen die Möglichkeit, dass Sie Cookies in den Einstellungen Ihres Browsers abschalten. Hierdurch können jedoch Funktionen unseres Onlineangebotes eingeschränkt werden. Wir empfehlen daher zusätzlich die folgenden Opt-Out-Möglichkeiten, die zusammenfassend auf jeweilige Gebiete gerichtet angeboten werden:

                                a) Europa: <a href="https://www.youronlinechoices.eu" target="_blank">https://www.youronlinechoices.eu</a>.
                                b) Kanada: <a href="https://www.youradchoices.ca/choices" target="_blank">https://www.youradchoices.ca/choices</a>.
                                c) USA: <a href="https://www.aboutads.info/choices" target="_blank">https://www.aboutads.info/choices</a>.
                                d) Gebietsübergreifend: <a href="http://optout.aboutads.info" target="_blank">http://optout.aboutads.info</a>.</p>
                        </li>
                    </ul>
                    <p><strong>Eingesetzte Dienste und Diensteanbieter:</strong></p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Google Tag Manager:</strong> Google Tag Manager ist eine Lösung, mit der wir sog. Website-Tags über eine Oberfläche verwalten können (und so z.B. Google Analytics sowie andere Google-Marketing-Dienste in unser Onlineangebot einbinden). Der Tag Manager selbst (welches die Tags implementiert) verarbeitet keine personenbezogenen Daten der Nutzer. Im Hinblick auf die Verarbeitung der personenbezogenen Daten der Nutzer wird auf die folgenden Angaben zu den Google-Diensten verwiesen. Dienstanbieter: Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Irland, Mutterunternehmen: Google LLC, 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA; Website: <a href="https://marketingplatform.google.com" target="_blank">https://marketingplatform.google.com</a>; Datenschutzerklärung: <a href="https://policies.google.com/privacy" target="_blank">https://policies.google.com/privacy</a>; Privacy Shield (Gewährleistung Datenschutzniveau bei Verarbeitung von Daten in den USA): <a href="https://www.privacyshield.gov/participant?id=a2zt000000001L5AAI&status=Active" target="_blank">https://www.privacyshield.gov/participant?id=a2zt000000001L5AAI&status=Active</a>.</p>
                        </li>
                        <li>
                            <p><strong>Google Analytics:</strong> Onlinemarketing und Webanalyse; Dienstanbieter: Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Irland, Mutterunternehmen: Google LLC, 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA; Website: <a href="https://marketingplatform.google.com/intl/de/about/analytics/" target="_blank">https://marketingplatform.google.com/intl/de/about/analytics/</a>; Datenschutzerklärung: <a href="https://policies.google.com/privacy" target="_blank">https://policies.google.com/privacy</a>; Privacy Shield (Gewährleistung Datenschutzniveau bei Verarbeitung von Daten in den USA): <a href="https://www.privacyshield.gov/participant?id=a2zt000000001L5AAI&status=Active" target="_blank">https://www.privacyshield.gov/participant?id=a2zt000000001L5AAI&status=Active</a>; Widerspruchsmöglichkeit (Opt-Out): Opt-Out-Plugin: <a href="http://tools.google.com/dlpage/gaoptout?hl=de" target="_blank">http://tools.google.com/dlpage/gaoptout?hl=de</a>, Einstellungen für die Darstellung von Werbeeinblendungen: <a href="https://adssettings.google.com/authenticated" target="_blank">https://adssettings.google.com/authenticated</a>.</p>
                        </li>
                        <li>
                            <p><strong>Facebook-Pixel:</strong> Facebook-Pixel; Dienstanbieter: <a href="https://www.facebook.com" target="_blank">https://www.facebook.com</a>, Facebook Ireland Ltd., 4 Grand Canal Square, Grand Canal Harbour, Dublin 2, Irland, Mutterunternehmen: Facebook, 1 Hacker Way, Menlo Park, CA 94025, USA; Website: <a href="https://www.facebook.com" target="_blank">https://www.facebook.com</a>; Datenschutzerklärung: <a href="https://www.facebook.com/about/privacy" target="_blank">https://www.facebook.com/about/privacy</a>; Privacy Shield (Gewährleistung Datenschutzniveau bei Verarbeitung von Daten in den USA): <a href="https://www.privacyshield.gov/participant?id=a2zt0000000GnywAAC&status=Active" target="_blank">https://www.privacyshield.gov/participant?id=a2zt0000000GnywAAC&status=Active</a>; Widerspruchsmöglichkeit (Opt-Out): <a href="https://www.facebook.com/settings?tab=ads" target="_blank">https://www.facebook.com/settings?tab=ads</a>.</p>
                        </li>
                    </ul>
                    <h2 id="m136">Präsenzen in sozialen Netzwerken</h2>
                    <p>Wir unterhalten Onlinepräsenzen innerhalb sozialer Netzwerke, um mit den dort aktiven Nutzern zu kommunizieren oder um dort Informationen über uns anzubieten.</p>
                    <p>Wir weisen darauf hin, dass dabei Daten der Nutzer außerhalb des Raumes der Europäischen Union verarbeitet werden können. Hierdurch können sich für die Nutzer Risiken ergeben, weil so z.B. die Durchsetzung der Rechte der Nutzer erschwert werden könnte. Im Hinblick auf US-Anbieter, die unter dem Privacy-Shield zertifiziert sind oder vergleichbare Garantien eines sicheren Datenschutzniveaus bieten, weisen wir darauf hin, dass sie sich damit verpflichten, die Datenschutzstandards der EU einzuhalten.</p>
                    <p>Ferner werden die Daten der Nutzer innerhalb sozialer Netzwerke im Regelfall für Marktforschungs- und Werbezwecke verarbeitet. So können z.B. anhand des Nutzungsverhaltens und sich daraus ergebender Interessen der Nutzer Nutzungsprofile erstellt werden. Die Nutzungsprofile können wiederum verwendet werden, um z.B. Werbeanzeigen innerhalb und außerhalb der Netzwerke zu schalten, die mutmaßlich den Interessen der Nutzer entsprechen. Zu diesen Zwecken werden im Regelfall Cookies auf den Rechnern der Nutzer gespeichert, in denen das Nutzungsverhalten und die Interessen der Nutzer gespeichert werden. Ferner können in den Nutzungsprofilen auch Daten unabhängig der von den Nutzern verwendeten Geräte gespeichert werden (insbesondere, wenn die Nutzer Mitglieder der jeweiligen Plattformen sind und bei diesen eingeloggt sind).</p>
                    <p>Für eine detaillierte Darstellung der jeweiligen Verarbeitungsformen und der Widerspruchsmöglichkeiten (Opt-Out) verweisen wir auf die Datenschutzerklärungen und Angaben der Betreiber der jeweiligen Netzwerke.</p>
                    <p>Auch im Fall von Auskunftsanfragen und der Geltendmachung von Betroffenenrechten weisen wir darauf hin, dass diese am effektivsten bei den Anbietern geltend gemacht werden können. Nur die Anbieter haben jeweils Zugriff auf die Daten der Nutzer und können direkt entsprechende Maßnahmen ergreifen und Auskünfte geben. Sollten Sie dennoch Hilfe benötigen, dann können Sie sich an uns wenden.</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Bestandsdaten (z.B. Namen, Adressen), Kontaktdaten (z.B. E-Mail, Telefonnummern), Inhaltsdaten (z.B. Texteingaben, Fotografien, Videos), Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten).</p>
                        </li>
                        <li>
                            <p><strong>Zwecke der Verarbeitung:</strong> Kontaktanfragen und Kommunikation, Tracking (z.B. interessens-/verhaltensbezogenes Profiling, Nutzung von Cookies), Remarketing, Reichweitenmessung (z.B. Zugriffsstatistiken, Erkennung wiederkehrender Besucher).</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                    </ul>
                    <p><strong>Eingesetzte Dienste und Diensteanbieter:</strong></p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Instagram :</strong> Soziales Netzwerk; Dienstanbieter: Instagram Inc., 1601 Willow Road, Menlo Park, CA, 94025, USA; Website: <a href="https://www.instagram.com" target="_blank">https://www.instagram.com</a>; Datenschutzerklärung: <a href="http://instagram.com/about/legal/privacy" target="_blank">http://instagram.com/about/legal/privacy</a>.</p>
                        </li>
                        <li>
                            <p><strong>Facebook:</strong> Soziales Netzwerk; Dienstanbieter: Facebook Ireland Ltd., 4 Grand Canal Square, Grand Canal Harbour, Dublin 2, Irland, Mutterunternehmen: Facebook, 1 Hacker Way, Menlo Park, CA 94025, USA; Website: <a href="https://www.facebook.com" target="_blank">https://www.facebook.com</a>; Datenschutzerklärung: <a href="https://www.facebook.com/about/privacy" target="_blank">https://www.facebook.com/about/privacy</a>; Privacy Shield (Gewährleistung Datenschutzniveau bei Verarbeitung von Daten in den USA): <a href="https://www.privacyshield.gov/participant?id=a2zt0000000GnywAAC&status=Active" target="_blank">https://www.privacyshield.gov/participant?id=a2zt0000000GnywAAC&status=Active</a>; Widerspruchsmöglichkeit (Opt-Out): Einstellungen für Werbeanzeigen: <a href="https://www.facebook.com/settings?tab=ads" target="_blank">https://www.facebook.com/settings?tab=ads</a>; Zusätzliche Hinweise zum Datenschutz: Vereinbarung über gemeinsame Verarbeitung personenbezogener Daten auf Facebook-Seiten: <a href="https://www.facebook.com/legal/terms/page_controller_addendum" target="_blank">https://www.facebook.com/legal/terms/page_controller_addendum</a>, Datenschutzhinweise für Facebook-Seiten: <a href="https://www.facebook.com/legal/terms/information_about_page_insights_data" target="_blank">https://www.facebook.com/legal/terms/information_about_page_insights_data</a>.</p>
                        </li>
                        <li>
                            <p><strong>YouTube:</strong> Soziales Netzwerk; Dienstanbieter: Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Irland, Mutterunternehmen: Google LLC, 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA; Datenschutzerklärung: <a href="https://policies.google.com/privacy" target="_blank">https://policies.google.com/privacy</a>; Privacy Shield (Gewährleistung Datenschutzniveau bei Verarbeitung von Daten in den USA): <a href="https://www.privacyshield.gov/participant?id=a2zt000000001L5AAI&status=Active" target="_blank">https://www.privacyshield.gov/participant?id=a2zt000000001L5AAI&status=Active</a>; Widerspruchsmöglichkeit (Opt-Out): <a href="https://adssettings.google.com/authenticated" target="_blank">https://adssettings.google.com/authenticated</a>.</p>
                        </li>
                    </ul>
                    <h2 id="m328">Plugins und eingebettete Funktionen sowie Inhalte</h2>
                    <p>Wir binden in unser Onlineangebot Funktions- und Inhaltselemente ein, die von den Servern ihrer jeweiligen Anbieter (nachfolgend bezeichnet als "Drittanbieter”) bezogen werden. Dabei kann es sich zum Beispiel um Grafiken, Videos oder Social-Media-Schaltflächen sowie Beiträge handeln (nachfolgend einheitlich bezeichnet als "Inhalte”).</p>
                    <p>Die Einbindung setzt immer voraus, dass die Drittanbieter dieser Inhalte die IP-Adresse der Nutzer verarbeiten, da sie ohne die IP-Adresse die Inhalte nicht an deren Browser senden könnten. Die IP-Adresse ist damit für die Darstellung dieser Inhalte oder Funktionen erforderlich. Wir bemühen uns, nur solche Inhalte zu verwenden, deren jeweilige Anbieter die IP-Adresse lediglich zur Auslieferung der Inhalte verwenden. Drittanbieter können ferner sogenannte Pixel-Tags (unsichtbare Grafiken, auch als "Web Beacons" bezeichnet) für statistische oder Marketingzwecke verwenden. Durch die "Pixel-Tags" können Informationen, wie der Besucherverkehr auf den Seiten dieser Webseite, ausgewertet werden. Die pseudonymen Informationen können ferner in Cookies auf dem Gerät der Nutzer gespeichert werden und unter anderem technische Informationen zum Browser und zum Betriebssystem, zu verweisenden Webseiten, zur Besuchszeit sowie weitere Angaben zur Nutzung unseres Onlineangebotes enthalten als auch mit solchen Informationen aus anderen Quellen verbunden werden.</p>
                    <p><strong>Hinweise zu Rechtsgrundlagen:</strong> Sofern wir die Nutzer um deren Einwilligung in den Einsatz der Drittanbieter bitten, ist die Rechtsgrundlage der Verarbeitung von Daten die Einwilligung. Ansonsten werden die Daten der Nutzer auf Grundlage unserer berechtigten Interessen (d.h. Interesse an effizienten, wirtschaftlichen und empfängerfreundlichen Leistungen) verarbeitet. In diesem Zusammenhang möchten wir Sie auch auf die Informationen zur Verwendung von Cookies in dieser Datenschutzerklärung hinweisen.</p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>Verarbeitete Datenarten:</strong> Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen), Bestandsdaten (z.B. Namen, Adressen), Kontaktdaten (z.B. E-Mail, Telefonnummern), Inhaltsdaten (z.B. Texteingaben, Fotografien, Videos).</p>
                        </li>
                        <li>
                            <p><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten).</p>
                        </li>
                        <li>
                            <p><strong>Zwecke der Verarbeitung:</strong> Bereitstellung unseres Onlineangebotes und Nutzerfreundlichkeit, Vertragliche Leistungen und Service, Sicherheitsmaßnahmen, Verwaltung und Beantwortung von Anfragen.</p>
                        </li>
                        <li>
                            <p><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a DSGVO), Vertragserfüllung und vorvertragliche Anfragen (Art. 6 Abs. 1 S. 1 lit. b. DSGVO), Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).</p>
                        </li>
                    </ul>
                    <p><strong>Eingesetzte Dienste und Diensteanbieter:</strong></p>
                    <ul class="m-elements">
                        <li>
                            <p><strong>YouTube:</strong> Videos; Dienstanbieter: Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Irland, Mutterunternehmen: Google LLC, 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA; Website: <a href="https://www.youtube.com" target="_blank">https://www.youtube.com</a>; Datenschutzerklärung: <a href="https://policies.google.com/privacy" target="_blank">https://policies.google.com/privacy</a>; Privacy Shield (Gewährleistung Datenschutzniveau bei Verarbeitung von Daten in den USA): <a href="https://www.privacyshield.gov/participant?id=a2zt000000001L5AAI&status=Active" target="_blank">https://www.privacyshield.gov/participant?id=a2zt000000001L5AAI&status=Active</a>; Widerspruchsmöglichkeit (Opt-Out): Opt-Out-Plugin: <a href="http://tools.google.com/dlpage/gaoptout?hl=de" target="_blank">http://tools.google.com/dlpage/gaoptout?hl=de</a>, Einstellungen für die Darstellung von Werbeeinblendungen: <a href="https://adssettings.google.com/authenticated" target="_blank">https://adssettings.google.com/authenticated</a>.</p>
                        </li>
                    </ul>
                    <h2 id="m12">Löschung von Daten</h2>
                    <p>Die von uns verarbeiteten Daten werden nach Maßgabe der gesetzlichen Vorgaben gelöscht, sobald deren zur Verarbeitung erlaubten Einwilligungen widerrufen werden oder sonstige Erlaubnisse entfallen (z.B., wenn der Zweck der Verarbeitung dieser Daten entfallen ist oder sie für den Zweck nicht erforderlich sind).</p>
                    <p>Sofern die Daten nicht gelöscht werden, weil sie für andere und gesetzlich zulässige Zwecke erforderlich sind, wird deren Verarbeitung auf diese Zwecke beschränkt. D.h., die Daten werden gesperrt und nicht für andere Zwecke verarbeitet. Das gilt z.B. für Daten, die aus handels- oder steuerrechtlichen Gründen aufbewahrt werden müssen oder deren Speicherung zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen oder zum Schutz der Rechte einer anderen natürlichen oder juristischen Person erforderlich ist.</p>
                    <p>Weitere Hinweise zu der Löschung von personenbezogenen Daten können ferner im Rahmen der einzelnen Datenschutzhinweise dieser Datenschutzerklärung erfolgen.</p>
                    <ul class="m-elements"></ul>
                    <h2 id="m15">Änderung und Aktualisierung der Datenschutzerklärung</h2>
                    <p>Wir bitten Sie, sich regelmäßig über den Inhalt unserer Datenschutzerklärung zu informieren. Wir passen die Datenschutzerklärung an, sobald die Änderungen der von uns durchgeführten Datenverarbeitungen dies erforderlich machen. Wir informieren Sie, sobald durch die Änderungen eine Mitwirkungshandlung Ihrerseits (z.B. Einwilligung) oder eine sonstige individuelle Benachrichtigung erforderlich wird.</p>
                    <h2 id="m10">Rechte der betroffenen Personen</h2>
                    <p>Ihnen stehen als Betroffene nach der DSGVO verschiedene Rechte zu, die sich insbesondere aus Art. 15 bis 18 und 21 DS-GVO ergeben:</p>
                    <ul>
                        <li><strong>Widerspruchsrecht: Sie haben das Recht, aus Gründen, die sich aus Ihrer besonderen Situation ergeben, jederzeit gegen die Verarbeitung der Sie betreffenden personenbezogenen Daten, die aufgrund von Art. 6 Abs. 1 lit. e oder f DSGVO erfolgt, Widerspruch einzulegen; dies gilt auch für ein auf diese Bestimmungen gestütztes Profiling. Werden die Sie betreffenden personenbezogenen Daten verarbeitet, um Direktwerbung zu betreiben, haben Sie das Recht, jederzeit Widerspruch gegen die Verarbeitung der Sie betreffenden personenbezogenen Daten zum Zwecke derartiger Werbung einzulegen; dies gilt auch für das Profiling, soweit es mit solcher Direktwerbung in Verbindung steht.</strong></li>
                        <li><strong>Widerrufsrecht bei Einwilligungen:</strong> Sie haben das Recht, erteilte Einwilligungen jederzeit zu widerrufen.</li>
                        <li><strong>Auskunftsrecht:</strong> Sie haben das Recht, eine Bestätigung darüber zu verlangen, ob betreffende Daten verarbeitet werden und auf Auskunft über diese Daten sowie auf weitere Informationen und Kopie der Daten entsprechend den gesetzlichen Vorgaben.</li>
                        <li><strong>Recht auf Berichtigung:</strong> Sie haben entsprechend den gesetzlichen Vorgaben das Recht, die Vervollständigung der Sie betreffenden Daten oder die Berichtigung der Sie betreffenden unrichtigen Daten zu verlangen.</li>
                        <li><strong>Recht auf Löschung und Einschränkung der Verarbeitung:</strong> Sie haben nach Maßgabe der gesetzlichen Vorgaben das Recht, zu verlangen, dass Sie betreffende Daten unverzüglich gelöscht werden, bzw. alternativ nach Maßgabe der gesetzlichen Vorgaben eine Einschränkung der Verarbeitung der Daten zu verlangen.</li>
                        <li><strong>Recht auf Datenübertragbarkeit:</strong> Sie haben das Recht, Sie betreffende Daten, die Sie uns bereitgestellt haben, nach Maßgabe der gesetzlichen Vorgaben in einem strukturierten, gängigen und maschinenlesbaren Format zu erhalten oder deren Übermittlung an einen anderen Verantwortlichen zu fordern.</li>
                        <li><strong>Beschwerde bei Aufsichtsbehörde:</strong> Sie haben ferner nach Maßgabe der gesetzlichen Vorgaben das Recht, bei einer Aufsichtsbehörde, insbesondere in dem Mitgliedstaat Ihres gewöhnlichen Aufenthaltsorts, Ihres Arbeitsplatzes oder des Orts des mutmaßlichen Verstoßes, wenn Sie der Ansicht sind, dass die Verarbeitung der Sie betreffenden personenbezogenen Daten gegen die DSGVO verstößt.</li>
                    </ul>
                    <h2 id="m42">Begriffsdefinitionen</h2>
                    <p>In diesem Abschnitt erhalten Sie eine Übersicht über die in dieser Datenschutzerklärung verwendeten Begrifflichkeiten. Viele der Begriffe sind dem Gesetz entnommen und vor allem im Art. 4 DSGVO definiert. Die gesetzlichen Definitionen sind verbindlich. Die nachfolgenden Erläuterungen sollen dagegen vor allem dem Verständnis dienen. Die Begriffe sind alphabetisch sortiert.</p>
                    <ul class="glossary">
                        <li><strong>Besuchsaktionsauswertung:</strong> "Besuchsaktionsauswertung" (englisch "Conversion Tracking") bezeichnet ein Verfahren, mit dem die Wirksamkeit von Marketingmaßnahmen festgestellt werden kann. Dazu wird im Regelfall ein Cookie auf den Geräten der Nutzer innerhalb der Webseiten, auf denen die Marketingmaßnahmen erfolgen, gespeichert und dann erneut auf der Zielwebseite abgerufen. Beispielsweise können wir so nachvollziehen, ob die von uns auf anderen Webseiten geschalteten Anzeigen erfolgreich waren). </li>
                        <li><strong>Cross-Device Tracking:</strong> Das Cross-Device Tracking ist eine Form des Trackings, bei der Verhaltens- und Interessensinformationen der Nutzer geräteübergreifend in sogenannten Profilen erfasst werden, indem den Nutzern eine Onlinekennung zugeordnet wird. Hierdurch können die Nutzerinformationen unabhängig von verwendeten Browsern oder Geräten (z.B. Mobiltelefonen oder Desktopcomputern) im Regelfall für Marketingzwecke analysiert werden. Die Onlinekennung ist bei den meisten Anbietern nicht mit Klardaten, wie Namen, Postadressen oder E-Mail-Adressen, verknüpft. </li>
                        <li><strong>IP-Masking:</strong> Als "IP-Masking” wird eine Methode bezeichnet, bei der das letzte Oktett, d.h., die letzten beiden Zahlen einer IP-Adresse, gelöscht wird, damit die IP-Adresse nicht mehr der eindeutigen Identifizierung einer Person dienen kann. Daher ist das IP-Masking ein Mittel zur Pseudonymisierung von Verarbeitungsverfahren, insbesondere im Onlinemarketing </li>
                        <li><strong>Interessenbasiertes und verhaltensbezogenes Marketing:</strong> Von interessens- und/oder verhaltensbezogenem Marketing spricht man, wenn potentielle Interessen von Nutzern an Anzeigen und sonstigen Inhalten möglichst genau vorbestimmt werden. Dies geschieht anhand von Angaben zu deren Vorverhalten (z.B. Aufsuchen von bestimmten Webseiten und Verweilen auf diesen, Kaufverhaltens oder Interaktion mit anderen Nutzern), die in einem sogenannten Profil gespeichert werden. Zu diesen Zwecken werden im Regelfall Cookies eingesetzt. </li>
                        <li><strong>Konversionsmessung:</strong> Die Konversionsmessung ist ein Verfahren, mit dem die Wirksamkeit von Marketingmaßnahmen festgestellt werden kann. Dazu wird im Regelfall ein Cookie auf den Geräten der Nutzer innerhalb der Webseiten, auf denen die Marketingmaßnahmen erfolgen, gespeichert und dann erneut auf der Zielwebseite abgerufen. Beispielsweise können wir so nachvollziehen, ob die von uns auf anderen Webseiten geschalteten Anzeigen erfolgreich waren. </li>
                        <li><strong>Personenbezogene Daten:</strong> "Personenbezogene Daten“ sind alle Informationen, die sich auf eine identifizierte oder identifizierbare natürliche Person (im Folgenden "betroffene Person“) beziehen; als identifizierbar wird eine natürliche Person angesehen, die direkt oder indirekt, insbesondere mittels Zuordnung zu einer Kennung wie einem Namen, zu einer Kennnummer, zu Standortdaten, zu einer Online-Kennung (z.B. Cookie) oder zu einem oder mehreren besonderen Merkmalen identifiziert werden kann, die Ausdruck der physischen, physiologischen, genetischen, psychischen, wirtschaftlichen, kulturellen oder sozialen Identität dieser natürlichen Person sind. </li>
                        <li><strong>Profiling:</strong> Als "Profiling“ wird jede Art der automatisierten Verarbeitung personenbezogener Daten bezeichnet, die darin besteht, dass diese personenbezogenen Daten verwendet werden, um bestimmte persönliche Aspekte, die sich auf eine natürliche Person beziehen (je nach Art des Profilings gehören dazu Informationen betreffend das Alter, das Geschlecht, Standortdaten und Bewegungsdaten, Interaktion mit Webseiten und deren Inhalten, Einkaufsverhalten, soziale Interaktionen mit anderen Menschen) zu analysieren, zu bewerten oder, um sie vorherzusagen (z.B. die Interessen an bestimmten Inhalten oder Produkten, das Klickverhalten auf einer Webseite oder den Aufenthaltsort). Zu Zwecken des Profilings werden häufig Cookies und Web-Beacons eingesetzt. </li>
                        <li><strong>Reichweitenmessung:</strong> Die Reichweitenmessung (auch als Web Analytics bezeichnet) dient der Auswertung der Besucherströme eines Onlineangebotes und kann das Verhalten oder Interessen der Besucher an bestimmten Informationen, wie z.B. Inhalten von Webseiten, umfassen. Mit Hilfe der Reichweitenanalyse können Webseiteninhaber z.B. erkennen, zu welcher Zeit Besucher ihre Webseite besuchen und für welche Inhalte sie sich interessieren. Dadurch können sie z.B. die Inhalte der Webseite besser an die Bedürfnisse ihrer Besucher anpassen. Zu Zwecken der Reichweitenanalyse werden häufig pseudonyme Cookies und Web-Beacons eingesetzt, um wiederkehrende Besucher zu erkennen und so genauere Analysen zur Nutzung eines Onlineangebotes zu erhalten. </li>
                        <li><strong>Remarketing:</strong> Vom "Remarketing“ bzw. "Retargeting“ spricht man, wenn z.B. zu Werbezwecken vermerkt wird, für welche Produkte sich ein Nutzer auf einer Webseite interessiert hat, um den Nutzer auf anderen Webseiten an diese Produkte, z.B. in Werbeanzeigen, zu erinnern. </li>
                        <li><strong>Tracking:</strong> Vom "Tracking“ spricht man, wenn das Verhalten von Nutzern über mehrere Onlineangebote hinweg nachvollzogen werden kann. Im Regelfall werden im Hinblick auf die genutzten Onlineangebote Verhaltens- und Interessensinformationen in Cookies oder auf Servern der Anbieter der Trackingtechnologien gespeichert (sogenanntes Profiling). Diese Informationen können anschließend z.B. eingesetzt werden, um den Nutzern Werbeanzeigen anzuzeigen, die voraussichtlich deren Interessen entsprechen. </li>
                        <li><strong>Verantwortlicher:</strong> Als "Verantwortlicher“ wird die natürliche oder juristische Person, Behörde, Einrichtung oder andere Stelle, die allein oder gemeinsam mit anderen über die Zwecke und Mittel der Verarbeitung von personenbezogenen Daten entscheidet, bezeichnet. </li>
                        <li><strong>Verarbeitung:</strong> "Verarbeitung" ist jeder mit oder ohne Hilfe automatisierter Verfahren ausgeführte Vorgang oder jede solche Vorgangsreihe im Zusammenhang mit personenbezogenen Daten. Der Begriff reicht weit und umfasst praktisch jeden Umgang mit Daten, sei es das Erheben, das Auswerten, das Speichern, das Übermitteln oder das Löschen. </li>
                        <li><strong>Zielgruppenbildung:</strong> Von Zielgruppenbildung (bzw. "Custom Audiences“) spricht man, wenn Zielgruppen für Werbezwecke, z.B. Einblendung von Werbeanzeigen, bestimmt werden. So kann z.B. anhand des Interesses eines Nutzers an bestimmten Produkten oder Themen im Internet geschlussfolgert werden, dass dieser Nutzer sich für Werbeanzeigen für ähnliche Produkte oder den Onlineshop, in dem er die Produkte betrachtet hat, interessiert. Von "Lookalike Audiences“ (bzw. ähnlichen Zielgruppen) spricht man wiederum, wenn die als geeignet eingeschätzten Inhalte Nutzern angezeigt werden, deren Profile bzw. Interessen mutmaßlich den Nutzern, zu denen die Profile gebildet wurden, entsprechen. Zur Zwecken der Bildung von Custom Audiences und Lookalike Audiences werden im Regelfall Cookies und Web-Beacons eingesetzt. </li>
                    </ul>
                    </p>
                    <p class="seal"><a href="https://datenschutz-generator.de/?l=de" title="Rechtstext von Dr. Schwenke - für weitere Informationen bitte anklicken." target="_blank">Erstellt mit Datenschutz-Generator.de von Dr. jur. Thomas Schwenke</a></p>

                </td>
            </tr>
        </table><br><br>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'chat')
    {
        ?>
        <table width="100%">
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Chat</b></div>
                    </div>
                    Im Chat kannst du mit allen Usern schreiben, die gerade online sind.<br />
                    Der Haupt-Channel ist "OPBG".<br />
                    Du kannst auch mit anderen Nutzern vom Naruto Browsergame schreiben, sie sind im Channel "NBG".<br />
                    Auch kannst du eigene Channel erstellen. Ändere dazu einfach in dem linken Eingabefeld den Wert auf deinen Channelnamen.

                </td>
            </tr>
        </table><br><br>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'faq')
    {
        ?>
        <table width="100%">
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Wie erhalte ich Berry?</b></div>
                    </div>
                    <br />
                    Es gibt 3 Wege Berry zu erhalten:
                    <div class="spacer"></div>
                    1. NPC Kämpfe, gewinnt ihr gegen einen NPC so erhaltet ihr die angezeigte Menge an Berry.
                    <div class="spacer"></div>
                    2. Story, gewinnt ihr in der Story gegen einen Gegner oder löst ihr ein Quiz so erhaltet ihr ebenfalls öfter Berry.
                    <div class="spacer"></div>
                    3. PvP Kämpfe, ihr könnt Kämpfe gegen andere Spieler bestreiten und zeigen wer der bessere ist, hier erhaltet ihr ebenfalls Berry.
                </td>
            </tr>
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Wie erhalte ich Gold</b></div>
                    </div>
                    <br />Es gibt hier 3 Möglichkeiten an Gold zu kommen:
                    <div class="spacer"></div>
                    1. Story, gewinnt ihr gegen einen Gegner in der Story so erhaltet ihr Gold.
                    <div class="spacer"></div>
                    2. PvP Kämpfe, bestreitet ihr Kämpfe gegen andere User so erhaltet ihr auch in diesen Kämpfen Gold.
                    <div class="spacer"></div>
                    3. Items, es gibt Schatztruhen in denen sich auch hin und wieder eine kleine Menge Gold versteckt.
                    <div class="spacer"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Wie erhalte ich Kopfgeld</b></div>
                    </div>
                    <br />
                    Damit dein Kopfgeld steigt gibt es mehrere Möglichkeiten:
                    <div class="spacer"></div>
                    1. Du gewinnst in einem PvP Kampf.
                    <div class="spacer"></div>
                    2. Du Gewinnst gegen einen Gegner in der Story.
                    <div class="spacer"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Trainieren während einer Reise</b></div>
                    </div>
                    <br />Bei OPBG ist es möglich eine Reise zu starten wenn man vorher sein Training eingestellt hat, andersrum geht es nicht.<br /><br />
                </td>
            </tr>
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Wie repariere ich mein Schiff</b></div>
                    </div>
                    <br />Um auf OPBG reisen zu können benötigst du ein Schiff, dein erstes erhältst du zum Start geschenkt.
                    <div class="spacer"></div>
                    Startest du eine Reise so nutzt sich dein Schiff ab und du musst es ggf. Reparieren, hierfür benötigt man unterschiedliche Menge an Holz, Nägel und Stoff.
                    <div class="spacer"></div>
                    Solltet ihr die Reise unterwegs abbrechen und merkt das ihr gar kein Material mehr habt um es zu reparieren so könnt ihr es kostenlos zu einem anderen Ort reisen.
                    <div class="spacer"></div>
                    Du kannst dein Schiff 3x reparieren, danach ist es zu oft repariert wurden und es verschwindet aus deinem Inventar, nun musst du dir ein neues Schiff kaufen.
                </td>
            </tr>
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Wie erhalte ich durch PvP Kämpfe Stats</b></div>
                    </div>
                    <div class="spacer"></div>
                    Dein Level * 10 ist das Ergebnis einer Zahl an PvP Kämpfen für welche man 10 Stats erhält, es gibt allerdings auch ein Item welches man für Berry erwerben kann
                    <br />
                    Um die Kämpfe zu umgehen.

                </td>
            </tr>
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Was ist das Impel Down und wie komme ich dort hinaus</b></div>
                    </div>
                    <div class="spacer"></div>
                    Das Impel Down ist ein Gefängnis worin man durch eine Technik des Gegners in einem PvP-Kampf zum Beispiel kommen kann.
                    <br />
                    <br />
                    Als Marine Mitglied besteht sofort die Möglichkeit sich wieder befreien zu lassen, ein Pirat hingegen muss 10 Tage warten!
                    <br />
                    <br />
                    Aber auch für Piraten besteht die Möglichkeit sofort herauszukommen, entweder lässt man sich von einem User auf der Freundesliste freikaufen oder durch die Bande in der man ist.

                </td>
            </tr>
        </table>
        <div class="spacer"></div>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'fraktion')
    {
        ?>
        <table width="100%">
            <tr>
                <td colspan="2">
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Pirat</b></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                        $zufall = rand(1, 13);
                        ?>
                    <img width="200px" src="img/races/Pirat<?php echo $zufall; ?>.png?003">
                    <img width="200px" style="position: absolute; left: 3px;" src="img/races/Pirat<?php echo $zufall; ?>Head.png?003">
                </td>
                <td valign="top">
                    Die Gründe, warum eine Person sein geregeltes Leben aufgibt und stattdessen sein Leben auf der schiefen Bahn zu beginnen, <br/>
                    unterscheiden sich von Pirat zu Pirat. Es gibt Piraten, die das Meer lieben und sich Hals über Kopf in jedes Abenteuer stürzen.  <br/>
                    Einige von ihnen wollen sich an den Schätzen von anderen bereichern und rauben diese aus. Für alle Piraten gilt die Bande, der sie angehören,  <br/>
                    oftmals als Familie und sie würde ihr Leben opfern, um jemanden aus der eigenen Bande zu retten. <br/>
                    <div class="spacer"></div>
                    <b>Vorteile:</b><br />
                    Piraten haben eine höhere Chance Items in Schatztruhen oder auch auf dem Meer zu finden.
                    <div class="spacer"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Marine</b></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                        $zufall = rand(1, 8);
                        ?>
                    <img width="200px" src="img/races/Marine<?php echo $zufall; ?>.png?003">
                    <img width="200px" style="position: absolute; left: 3px;" src="img/races/Marine<?php echo $zufall; ?>Head.png?003">
                </td>
                <td valign="top">
                    Die Marine – auch "Kriegsmacht der Gerechtigkeit" genannt – ist die größte Seestreitmacht der Erde und untersteht der Weltregierung.
                    Ihre Hauptaufgabe ist es, Recht und Ordnung in der Welt aufrechtzuerhalten, was die Jagd auf Piraten und andere Kriminelle mit einschließt.
                    <div class="spacer"></div>
                    <b>Vorteile:</b><br />
                    Die Marine erwartet im Impel Down Gefängnis keine Wartezeit bis zur Befreiung. Sie können sich für die hälfte ihres Prestiges in Berry selbstständig befreien.<br/>
                    Die Marine kann aufgrund ihrer einzigartigen Schiffe schneller über die Meere reisen und das um die Hälfte gegenüber der Piraten

                </td>
            </tr>
        </table>
        <div class="spacer"></div>
        <?php
    }
    else if(isset($_GET['info']) && $_GET['info'] == 'motto')
    {
        ?>
        Am Montag wird man im <a href="?p=arena">Kolosseum</a> von keinem Captcha gestört! <br /><br />
        Am Dienstag besitzt das <a href="?p=event">Event</a> "Lasst euch von Chopper heilen" eine 100% Dropchance!<br /><br />
        Am Mittwoch ist es möglich gegen einen gewählten NPC noch einmal zu kämpfen, denn am Mittwoch existiert der nochmal Button auch nach einem Kampf im <a href="?p=arena">Kolosseum</a>!<br /><br />
        Am Donnerstag braucht ihr besonderes Glück! Denn am Donnerstag bezahlt ihr im Casino nur die hälfte pro Spiel!<br /><br />
        Am Freitag wird es Zeit dir die Gebiete zu holen welche dir zustehen! An diesem Tag nutzen eure Schiffe nicht ab!<br /><br />
        Am Samstag wird es Zeit seine Verteidigung zu stärken und sich die neueste Rüstung zu schnappen, denn am Samstag besitzen die Dungeon eine 100% Dropchance!<br /><br />
        Es war eine anstrengende Woche! Zum Update von Sonntag zu Montag werden die Spieler vollständig geheilt!
            <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'pfad')
    {
        ?>
        Im Spiel ist es möglich, aus 6 verschiedenen Pfaden zu wählen, jede von Ihnen besitzt gewisse Nachteile, aber auch Vorteile.
        <div class="spacer"></div>
        Wir unterscheiden hierbei in drei Teufelsfruchtpfade (Logia, Paramecia, Zoan) und drei Kampftechnikpfade (Schwert, Karate, Schwarzfuß).
        <div class="spacer"></div>
        Zum Anfang des Spiels ist man in der Lage einen Pfad von den Kampftechniken zu wählen und man erhält erst im späteren Verlauf des Spiels die Möglichkeit, sich für eine Teufelsfrucht zu entscheiden.<br/>
        Jede Teufelsfrucht bietet drei unterschiedliche Varianten. Es ist allerdings nicht möglich, zwei Teufelsfruchtpfade oder zwei Kampftechnikpfade zu kombinieren.<br/>
        Hinzu kommt, dass wenn ihr unzufrieden seid, nachträglich noch in der Lage seid, den Pfad zu wechseln.
        <div class="spacer"></div>
        <hr>
        <b>Zoan</b>
        <div class="spacer"></div>
        Die Zoan Früchte verleihen die Fähigkeit, den eigenen Körper verwandeln zu lassen und so die physikalischen Eigenschaften einer anderen Spezies anzunehmen.<br/>
        Die Zoan Frucht bietet dabei drei Einsatzmöglichkeiten der Teufelsfruchtkräfte. Die Kräfte können ohne sich zu verwandeln genutzt werden, zusätzlich kann man sie in einer Hybrid Variante und in der Variante der vollständigen Verwandlung nutzen.<br/>
        Die körperliche Leistungsfähigkeit wird dabei in hohem Maße gesteigert, was sich durch eine bessere Defensive im Spiel auswirkt.<br/>
        Einer der bekanntesten Zoan Nutzer ist Marco der Phönix, welcher die Vogel-Frucht Modell: Phönix aß - worauf er die Kräfte eines<br/>
        Phönix bekam und kann sich auch in einen verwandeln. Man unterscheidet die Zoan Frucht in drei Unterkategorien. Der Nutzer kann sich je nach Frucht in ein Tier, ein Wesen vergangener Zeiten oder ein Geschöpf aus mythischen Legenden verwandeln.
        <div class="spacer"></div>
        <hr>
        <b>Paramecia</b>
        <div class="spacer"></div>
        Die Paramecia Früchte stellen den größten Anteil an allen bekannten Teufelsfrüchten dar, da sie vielfältige Fähigkeiten unter dem Sammelbegriff "übermenschliche Kräfte" vereinen.<br/>
        Die Paramecia Früchte verleihen eine Vielzahl von möglichen Fähigkeiten, die den eigenen Körper oder das Umfeld beeinflussen und verändern können.<br/>
        Einige können auch bestimmte Materie erschaffen und verändern.<br/>
        Einer der bekanntesten Paramecia Nutzer ist Trafalgar D. Water Law, welcher sich die Kräfte der Operations-Frucht zu nutzen macht.<br/>
        Durch diese Kraft ist er in der Lage einen Raum zu erstellen, mit dem er die natürlichen Gesetze komplett manipulieren kann.<br/>
        Paramecia ist im Spiel als Allrounder ausgelegt, sie haben eine solide Defensive und Offensive.
        <div class="spacer"></div>
        <hr>
        <b>Logia</b>
        <div class="spacer"></div>
        Die Logiafrüchte gelten als die stärksten aller Teufelsfrüchte.<br/>
        Sie verleihen die Fähigkeit, den eigenen Körper komplett in ein Element zu verwandeln und dieses nach Belieben zu kontrollieren.<br/>
        Dadurch kann der Anwender einer Logia Frucht in der Regel allen physischen Angriffen entgehen, indem sein Körper sich an der getroffenen Stelle in sein entsprechendes Element verwandelt.<br/>
        Einer der beliebtesten und bekanntesten Logia Nutzer war Ace, welcher durch Kräfte der Feuer-Frucht in der Lage war, das Feuer-Element zu kontrollieren.<br/>
        Logia übernimmt den offensiven Part im Spiel, sie haben eine sehr gute Offensive und dafür aber eine schlechtere Defensive.
        <div class="spacer"></div>
        <hr>
        <b>Schwertkämpfer</b>
        <div class="spacer"></div>
        Die wohl berühmtesten Schwertkämpfer in der Welt von One Piece sind Lorenor Zorro und Mihawk Falkenauge. Beide sind sehr beeindruckende Charaktere, die bekannt dafür sind, mit ihren Waffen enormen Schaden anzurichten!<br/>
        Auch hier im Spiel nimmt der Schwertkämpfer wie die Logia Frucht eine offensive Rolle ein. Die Verteidigung lässt auch hier zu wünschen übrig.<br/>
        Alle Schwertkämpfer-Fähigkeiten haben einen erhöhten Kritschaden.
        <div class="spacer"></div>
        <hr>
        <b>Schwarzfuß</b>
        <div class="spacer"></div>
        Der bekannteste Kämpfer, der seine Gegner mit Füßen tritt, ist Sanji! Ihm ist es möglich, durch eine spezielle Technik seine Füße in Flammen aufgehen zu lassen.<br/>
        Der Schwarzfuß ist nicht nur offensiv eine gute Wahl, sondern auch in der Defensive besitzt er gute Möglichkeiten, Lücken zu schließen.<br/>
        Alle Schwarzfuß-Fähigkeiten haben eine erhöhte Kritchance.
        <div class="spacer"></div>
        <hr>
        <b>Karatekämpfer</b>
        <div class="spacer"></div>
        Karatekämpfer(Fischmenschen-Karate) ist ein von Fischmenschen praktiziertes Karate, welches aber auch von Menschen erlernt werden kann.<br/>
        Wahre Meister im Fischmenschen-Karate sind in der Lage, in ihre Attacken auch das Wasser der Umgebung mit einzubeziehen.<br/>
        Der bekannteste Fischmenschen-Karate-Nutzer ist Jinbei, ein ehemaliger Samurai der Meere!<br/>

        <?php
    }
    if($_GET['info'] && $_GET['info'] == 'techniken')
    {
        if($_GET['info'] && $_GET['info'] == 'techniken' && $_GET['id'] == is_numeric($_GET['id']))
        {
            $id = $_GET['id'];
          $idsearch = $database->Select('*', 'attacks', 'id="'.$id.'"');
            
            if($idsearch->num_rows == 1)
            {
              $search = $idsearch->fetch_assoc();
                if($search['displayed'] != 0 || $player->GetArank() >= 2)
                {
                    ?>
                    <table width="100%" cellspacing="0" class="boxSchatten">
                        <tr>
                            <td colspan=4 class="catGradient borderT borderB" style="text-align:center;">
                                <b>
                                    <p style="color:white">
                                        <div class="schatten"><?php echo $search['name']; ?></div>
                                    </p>
                                </b>
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <table width="100%" cellspacing="0" class="boxSchatten">
                        <tr>
                            <td width="10%" class="borderL" style="text-align:center;"><b>Bild</b></td>
                            <td width="15%" style="text-align:center;"><b>Name</b></td>
                            <td width="5%" style="text-align:center;"><b>Typ</b></td>
                            <td width="50%" style="text-align:center;"><b>Wirkung</b></td>
                            <td width="40%" style="text-align:center;"><b>Genauigkeit</b></td>
                            <td width="10%" style="text-align:center;"><b>Kosten</b></td>
                            <td width="10%" style="text-align:center;"><b>Runden</b></td>
                            <td width="10%" style="text-align:center;"><b>Rasse</b></td>
                        </tr>
                        <tr>
                            <td width="50px" class="borderL" style="text-align:center">
                                <?php
                                    if($player->GetArank() >= 2)
                                    {
                                        ?>
                                        <a href="?p=admin&a=see&table=attacks&id=<?php echo $search['id'] ?>" target="_blank">
                                            <img class="boxSchatten" src="img/attacks/<?php echo $search['image']; ?>.png" width="50px" height="50px">
                                        </a>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <img class="boxSchatten" src="img/attacks/<?php echo $search['image']; ?>.png" width="50px" height="50px">
                                        <?php
                                    }
                                ?>
                            </td>
                            <td style="text-align:center" id="<?php echo $search['id']; ?>"><?php echo $search['name']; ?></td>
                            <td style="text-align:center"><span title="<?php echo $search['description']; ?>"><?php echo Attack::GetTypeName($search['type']); ?></span></td>
                            <td style="text-align:center">
                                <?php
                                    if ($search['type'] == 3)
                                    {
                                        echo 'Ziel benötigt <br/>' . $search['value'] . '% LP<br/>';
                                    }
                                    if ($search['type'] == 1 || $search['type'] == 12)
                                    {
                                        $value = 'lpvalue';
                                        if ($search[$value] != 0)
                                        {
                                            $valName = 'LP';
                                            echo number_format(round($search['value'] * $search[$value] / 100), '0', '', '.');
                                            if ($search['procentual'])
                                                echo '%';
                                            echo ' Douriki Schaden an ' . $valName . '<br/>';
                                        }
                                        $value = 'kpvalue';
                                        if ($search[$value] != 0)
                                        {
                                            $valName = 'AD';
                                            echo number_format(round($entry['value'] * $entry[$value] / 100), '0', '', '.');
                                            if ($search['procentual'])
                                                echo '%';
                                            echo ' Douriki Schaden an ' . $valName . '<br/>';
                                        }
                                        $value = 'epvalue';
                                        if ($search[$value] != 0)
                                        {
                                            $valName = 'EP';
                                            echo number_format(round($search['value'] * $search[$value] / 100), '0', '', '.');
                                            if ($search['procentual'])
                                                echo '%';
                                            echo ' Douriki Schaden an ' . $valName . '<br/>';
                                        }
                                    }
                                    else if ($search['type'] == 5 || $search['type'] == 11)
                                    {
                                        $value = 'lpvalue';
                                        if ($search[$value] != 0)
                                        {
                                            $valName = 'LP';
                                            echo number_format(round($search['value'] * $search[$value] / 100), '0', '', '.');
                                            if ($search['procentual'])
                                                echo '%';
                                            echo ' Douriki Heilung an ' . $valName . '<br/>';
                                        }
                                        $value = 'kpvalue';
                                        if ($search[$value] != 0)
                                        {
                                            $valName = 'AD';
                                            echo number_format(round($search['value'] * $search[$value] / 100), '0', '', '.');
                                            if ($search['procentual'])
                                                echo '%';
                                            echo ' Douriki Heilung an ' . $valName . '<br/>';
                                        }
                                        $value = 'epvalue';
                                        if ($search[$value] != 0)
                                        {
                                            $valName = 'EP';
                                            echo number_format(round($search['value'] * $search[$value] / 100), '0', '', '.');
                                            if ($search['procentual'])
                                                echo '%';
                                            echo ' Douriki Heilung an ' . $valName . '<br/>';
                                        }
                                    }
                                    else if ($search['type'] == 9 || $search['type'] == 19)
                                    {
                                        echo $search['value'];
                                    }
                                    else if ($search['type'] == 6)
                                    {
                                        echo $search['value'] . ' % Douriki pro Ladung';
                                    }
                                    else
                                    {
                                        $value = 'lpvalue';
                                        $valName = 'LP';
                                        if ($search[$value] != 0)
                                        {
                                            if ($search['type'] == 20 || $search['type'] == 2)
                                                echo $valName . ' x ';

                                            echo $search['value'] * $search[$value] / 100;
                                            if ($search['procentual'])
                                                echo '%';

                                            if (/*$entry['type'] == 18 || */$entry['type'] == 22)
                                                echo ' Douriki auf';

                                            if ($search['type'] != 20 && $search['type'] != 2)
                                                echo ' ' . $valName;

                                            echo '<br/>';
                                        }
                                        $value = 'kpvalue';
                                        $valName = 'AD';
                                        if ($search[$value] != 0)
                                        {
                                            if ($search['type'] == 20 || $search['type'] == 2)
                                                echo $valName . ' x ';

                                            echo number_format($search['value'] * $search[$value] / 100, '0', '', '.');
                                            if ($search['procentual'])
                                                echo '%';

                                            if (/*$entry['type'] == 18 || */$search['type'] == 22)
                                                echo ' Douriki auf';

                                            if ($search['type'] != 20 && $search['type'] != 2)
                                                echo ' ' . $valName;

                                            echo '<br/>';
                                        }
                                        $value = 'epvalue';
                                        $valName = 'Energie';
                                        if ($search[$value] != 0)
                                        {
                                            if ($search['type'] == 20 || $search['type'] == 2)
                                                echo $valName . ' x ';

                                            echo number_format($search['value'] * $search[$value] / 100, '0', '', '.');
                                            if ($search['procentual'])
                                                echo '%';

                                            if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$search['type'] == 22)
                                                echo ' Douriki auf';

                                            if ($search['type'] != 20 && $search['type'] != 2)
                                                echo ' ' . $valName;

                                            echo '<br/>';
                                        }
                                        $value = 'atkvalue';
                                        $valName = 'Angriff';
                                        if ($search[$value] != 0)
                                        {
                                            if ($search['type'] == 20 || $search['type'] == 2)
                                                echo $valName . ' x ';

                                            echo number_format($search['value'] * $search[$value] / 100, '0', '', '.');
                                            if ($search['procentual'])
                                                echo '%';

                                            if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$search['type'] == 22)
                                                echo ' Douriki auf';

                                            if ($search['type'] != 20 && $search['type'] != 2)
                                                echo ' ' . $valName;

                                            echo '<br/>';
                                        }
                                        $value = 'defvalue';
                                        $valName = 'Verteidigung';
                                        if ($search[$value] != 0)
                                        {
                                            if ($search['type'] == 20 || $search['type'] == 2)
                                                echo $valName . ' x ';

                                            echo $search['value'] * $search[$value] / 100;
                                            if ($search['procentual'])
                                                echo '%';

                                            if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$search['type'] == 22)
                                                echo ' Douriki auf';

                                            if ($search['type'] != 20 && $search['type'] != 2)
                                                echo ' ' . $valName;

                                            echo '<br/>';
                                        }
                                        $value = 'tauntvalue';
                                        $valName = 'Anziehung';
                                        if ($search[$value] != 0)
                                        {
                                            if ($search['type'] == 20 || $search['type'] == 2)
                                                echo $valName . ' x ';

                                            echo $search['value'] * $search[$value] / 100;
                                            if ($search['procentual'])
                                                echo '%';

                                            if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$search['type'] == 22)
                                                echo ' Douriki auf';

                                            if ($search['type'] != 20 && $search['type'] != 2)
                                                echo ' ' . $valName;

                                            echo '<br/>';
                                        }
                                        $value = 'reflectvalue';
                                        $valName = 'Reflektion';
                                        if ($search[$value] != 0)
                                        {
                                            if ($search['type'] == 2)
                                                echo $valName . ' x ';

                                            echo $search['value'] * $search[$value] / 100;
                                            if ($search['procentual'])
                                                echo '%';

                                            if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$search['type'] == 22)
                                                echo ' Douriki auf';

                                            if ($search['type'] != 2)
                                                echo ' ' . $valName;

                                            echo '<br/>';
                                        }
                                        $value = 'accbuf';
                                        $valName = 'Genauigkeit';
                                        if ($search[$value] != 0)
                                        {
                                            if ($search['type'] == 2)
                                                echo $valName . ' x ';

                                            echo $search['value'] * $search[$value] / 100;
                                            if ($search['procentual'])
                                                echo '%';

                                            if ($search['type'] != 2)
                                                echo ' ' . $valName;

                                            echo '<br/>';
                                        }
                                        $value = 'reflexbuf';
                                        $valName = 'Reflex';
                                        if ($entry[$value] != 0)
                                        {
                                            if ($entry['type'] == 2)
                                                echo $valName . ' x ';

                                            echo $entry['value'] * $entry[$value] / 100;
                                            if ($entry['procentual'])
                                                echo '%';

                                            if ($entry['type'] != 2)
                                                echo ' ' . $valName;

                                            echo '<br/>';
                                        }
                                    }
                                ?>
                            </td>
                            <td style="text-align:center">
                                <?php echo $search['accuracy']; ?>%
                            </td>
                            <td style="text-align:center">
                                <?php
                                    if ($search['energy'] != 0)
                                        echo number_format($search['energy'], '0', '', '.') . ' EP<br/>';
                                    if ($search['lp'] != 0)
                                    {
                                        if ($search['procentualcost'] == 1) echo ($search['lp'] / 100) . '%';
                                        else echo number_format($search['lp'], '0', '', '.');
                                        echo ' LP<br/>';
                                    }
                                    if ($search['kp'] != 0)
                                    {
                                        if ($search['singlecost'] == 1) echo "Einmalig ";
                                        if ($search['kpprocentual'] == 1) echo ($search['kp']) . '%';
                                        else echo number_format($search['kp'], '0', '', '.');
                                        echo ' AD<br/>';
                                    }
                                ?>
                            </td>
                            <td style="text-align:center">
                                <?php
                                    $runden = $search['rounds'] + 1;
                                    if ($runden > 0)
                                    {
                                        echo number_format($runden, '0', '', '.') . ' ';
                                        if ($runden == 1) echo 'Runde';
                                        else echo 'Runden';
                                    }
                                ?>
                            </td>
                            <td style="text-align:center"><?php echo $search['race']; ?></td>
                        </tr>
                    </table>
                    <?php
                }
            }
        }
        $start = 0;
        $limit = 30;
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
        {
            $start = $limit * ($_GET['page'] - 1);
        }
        ?>
        <table width="100%" cellspacing="0" class="boxSchatten">
            <tr>
                <td colspan=5 class="catGradient borderT borderB" style="text-align:center;">
                    <b>
                        <p style="color:white">
                        <div class="schatten">Suchen</div>
                        </p>
                    </b>
                </td>
            </tr>
            <tr class="boxSchatten">
                <td width="30%"><b>Name</b></td>
                <td width="20%"><b>Rasse</b></td>
                <td width="30%"><b>Type</b></td>
                <td width="20%"><b>Skilltree</b></td>
                <td width="10%"><b></b></td>
            </tr>
            <tr>
                <form method="GET" action="?p=info&info=techniken">
                    <input type="hidden" name="p" value="info">
                    <input type="hidden" name="info" value="techniken">
                    <td width="30%" class="boxSchatten">
                        <input style="width:90%;" type="text" name="attackenname" value="<?php if (isset($_GET['attackenname'])) echo $_GET['attackenname']; ?>">
                    </td>
                    <td width="20%" class="boxSchatten">
                        <select style="width: 100%;" class="select" name="attackenrasse" id="racelist">
                            <option value="" <?php if (isset($_GET['attackenrasse']) && $_GET['attackenrasse'] == '') echo 'selected'; ?>>Alle</option>
                            <option value="none" <?php if (isset($_GET['attackenrasse']) && $_GET['attackenrasse'] == 'none') echo 'selected'; ?>>Keine</option>
                            <option value="Pirat" <?php if (isset($_GET['attackenrasse']) && $_GET['attackenrasse'] == 'Pirat') echo 'selected'; ?>>Pirat</option>
                            <option value="Marine" <?php if (isset($_GET['attackenrasse']) && $_GET['attackenrasse'] == 'Marine') echo 'selected'; ?>>Marine</option>
                        </select><br>
                    </td>
                    <td width="30%" class="boxSchatten">
                        <select style="width: 100%;" class="select" name="attackentyp" id="attackentyp">
                            <option value="" <?php if (isset($_GET['attackentyp']) && $_GET['attackentyp'] == '') echo 'selected'; ?>>Alle</option>
                            <?php
                                for ($i = 1; $i <= 27; ++$i)
                                {
                                    if (($i == 11 || $i == 7 || $i == 10 || $i == 14 || $i == 15 || $i == 16 || $i == 22 || $i == 23 || $i == 24 || $i == 26 || $i == 27) && $player->GetArank() < 2)
                                        continue;
                                    ?> <option value="<?php echo $i; ?>" <?php if (isset($_GET['attackentyp']) && $_GET['attackentyp'] == $i) echo 'selected'; ?>><?php echo Attack::GetTypeName($i); ?></option><?php
                                }
                            ?>
                        </select><br>
                    </td>
                        <td width="20%" class="boxSchatten">
                            <select style="width: 100%;" class="select" name="attackenskilltree" id="skilltreelist">
                                <option value="" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '') echo 'selected'; ?>>Alle</option>
                                <option value="-1" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '-1') echo 'selected'; ?>>Keine</option>
                                <option value="11" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '11') echo 'selected'; ?>>Zoan - Fisch-Frucht</option>
                                <option value="12" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '12') echo 'selected'; ?>>Zoan - Vogel-Frucht</option>
                                <option value="13" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '13') echo 'selected'; ?>>Zoan - Mensch-Mensch-Frucht</option>
                                <option value="21" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '21') echo 'selected'; ?>>Paramecia - Operations-Frucht</option>
                                <option value="22" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '22') echo 'selected'; ?>>Paramecia - Faden-Frucht</option>
                                <option value="23" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '23') echo 'selected'; ?>>Paramecia - Mochi-Frucht</option>
                                <option value="31" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '31') echo 'selected'; ?>>Logia - Donner-Frucht</option>
                                <option value="32" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '32') echo 'selected'; ?>>Logia - Feuer-Frucht</option>
                                <option value="33" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '33') echo 'selected'; ?>>Logia - Gefrier-Frucht</option>
                                <option value="4" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '4') echo 'selected'; ?>>Schwertkämpfer</option>
                                <option value="5" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '5') echo 'selected'; ?>>Schwarzfuß</option>
                                <option value="6" <?php if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] == '6') echo 'selected'; ?>>Karatekämpfer</option>
                            </select>
                        </td>
                    <td width="20%" class="boxSchatten">
                        <input type="submit" style="width:90%" value="Suchen">
                    </td>
                </form>
            </tr>
        </table>
        <br />
        <table width="100%" cellspacing="0" class="boxSchatten">
            <tr>
                <td class="catGradient borderB borderT borderR borderL" colspan="9" style="text-align:center;"><b>Techniken</b></td>
            </tr>
            <tr>
                <td width="10%" class="borderL" style="text-align:center;"><b>Bild</b></td>
                <td width="15%" style="text-align:center;"><b>Name</b></td>
                <td width="5%" style="text-align:center;"><b>Typ</b></td>
                <td width="50%" style="text-align:center;"><b>Wirkung</b></td>
                <td width="40%" style="text-align:center;"><b>Genauigkeit</b></td>
                <td width="10%" style="text-align:center;"><b>Kosten</b></td>
                <td width="10%" style="text-align:center;"><b>Runden</b></td>
                <td width="10%" style="text-align:center;"><b>Rasse</b></td>
            </tr>
            <?php

                if($player->GetArank() < 2)
                {
                    $where = 'attacks.displayed=1';
                }
                else
                {
                    $where = '(attacks.displayed=0 OR attacks.displayed=1)';
                }
                if (isset($_GET['attackenname']) && $_GET['attackenname'] != '')
                {
                    $where = $where . ' AND attacks.name LIKE "%' . $database->EscapeString($_GET['attackenname']) . '%"';
                }
                if (isset($_GET['attackentyp']) && $_GET['attackentyp'] != '')
                {
                    $where = $where . ' AND attacks.type=' .$database->EscapeString($_GET['attackentyp']);
                }
                if (isset($_GET['attackenrasse']) && $_GET['attackenrasse'] != '')
                {
                    if ($_GET['attackenrasse'] == 'none')
                    {
                        $where = $where . ' AND attacks.race LIKE ""';
                    }
                    else
                    {
                        $where = $where . ' AND attacks.race LIKE "%' . $database->EscapeString($_GET['attackenrasse']) . '%"';
                    }
                }
                if (isset($_GET['attackenskilltree']) && $_GET['attackenskilltree'] != '')
                {
                    if($_GET['attackenskilltree'] == '-1')
                    {
                        $where = $where . ' AND skilltree.type IS NULL';
                    }
                    else
                    {
                        switch ($_GET['attackenskilltree'])
                        {
                            case 11:
                                $where = $where . ' AND skilltree.type=1 AND skilltree.type2=1';
                                break;
                            case 12:
                                $where = $where . ' AND skilltree.type=1 AND skilltree.type2=2';
                                break;
                            case 13:
                                $where = $where . ' AND skilltree.type=1 AND skilltree.type2=3';
                                break;
                            case 21:
                                $where = $where . ' AND skilltree.type=2 AND skilltree.type2=1';
                                break;
                            case 22:
                                $where = $where . ' AND skilltree.type=2 AND skilltree.type2=2';
                                break;
                            case 23:
                                $where = $where . ' AND skilltree.type=2 AND skilltree.type2=3';
                                break;
                            case 31:
                                $where = $where . ' AND skilltree.type=3 AND skilltree.type2=1';
                                break;
                            case 32:
                                $where = $where . ' AND skilltree.type=3 AND skilltree.type2=2';
                                break;
                            case 33:
                                $where = $where . ' AND skilltree.type=3 AND skilltree.type2=3';
                                break;
                            default:
                                $where = $where . ' AND skilltree.type='.$_GET['attackenskilltree'];
                                break;
                        }

                    }

                }
                $table = 'attacks LEFT JOIN skilltree ON attacks.id = skilltree.attack';
                $select = 'attacks.*, skilltree.type2, skilltree.type AS SType';
                $attacks = new Generallist($database, $table, $select, $where, 'attacks.id', $start . ',' . $limit, 'ASC');

                $id = 0;
                $entry = $attacks->GetEntry($id);

                while ($entry != null)
                {
                    ?>
                    <tr>
                        <td width="50px" class="borderL" style="text-align:center">
                            <?php
                                if($player->GetArank() >= 2)
                                {
                                    ?>
                                    <a href="?p=admin&a=see&table=attacks&id=<?php echo $entry['id'] ?>" target="_blank">
                                        <img class="boxSchatten" src="img/attacks/<?php echo $entry['image']; ?>.png" width="50px" height="50px">
                                    </a>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <img class="boxSchatten" src="img/attacks/<?php echo $entry['image']; ?>.png" width="50px" height="50px">
                                    <?php
                                }
                            ?>
                        </td>
                        <td style="text-align:center; <?php if($entry['displayed'] == 0) echo 'color: red;' ?>" id="<?php echo $entry['id']; ?>"><?php echo $entry['name']; ?></td>
                        <td style="text-align:center"><span title="<?php echo $entry['description']; ?>"><?php echo Attack::GetTypeName($entry['type']); ?></span></td>
                        <td style="text-align:center">
                            <?php
                                if ($entry['type'] == 3)
                                {
                                    echo 'Ziel benötigt <br/>' . $entry['value'] . '% LP<br/>';
                                }
                                if ($entry['type'] == 1 || $entry['type'] == 12 || $entry['type'] == 26 || $entry['type'] == 27)
                                {
                                    $value = 'lpvalue';
                                    if ($entry[$value] != 0)
                                    {
                                        $valName = 'LP';
                                        echo number_format(round($entry['value'] * $entry[$value] / 100), '0', '', '.');
                                        if ($entry['procentual'])
                                            echo '%';
                                        echo ' Douriki Schaden an ' . $valName . '<br/>';
                                    }
                                    $value = 'kpvalue';
                                    if ($entry[$value] != 0)
                                    {
                                        $valName = 'AD';
                                        echo number_format(round($entry['value'] * $entry[$value] / 100), '0', '', '.');
                                        if ($entry['procentual'])
                                            echo '%';
                                        echo ' Douriki Schaden an ' . $valName . '<br/>';
                                    }
                                    $value = 'epvalue';
                                    if ($entry[$value] != 0)
                                    {
                                        $valName = 'EP';
                                        echo number_format(round($entry['value'] * $entry[$value] / 100), '0', '', '.');
                                        if ($entry['procentual'])
                                            echo '%';
                                        echo ' Douriki Schaden an ' . $valName . '<br/>';
                                    }
                                    if($entry['type'] == 26 || $entry['type'] == 27)
                                    {
                                        $amount = number_format($entry['enemyamount'], 0, '', '.');
                                        if($entry['enemyamount'] == 0)
                                            $amount = 'alle';
                                        echo 'auf ' . $amount . ' Gegner';
                                    }
                                }
                                else if ($entry['type'] == 5 || $entry['type'] == 11)
                                {
                                    $value = 'lpvalue';
                                    if ($entry[$value] != 0)
                                    {
                                        $valName = 'LP';
                                        echo number_format(round($entry['value'] * $entry[$value] / 100), '0', '', '.');
                                        if ($entry['procentual'])
                                            echo '%';
                                        echo ' Douriki Heilung an ' . $valName . '<br/>';
                                    }
                                    $value = 'kpvalue';
                                    if ($entry[$value] != 0)
                                    {
                                        $valName = 'AD';
                                        echo number_format(round($entry['value'] * $entry[$value] / 100), '0', '', '.');
                                        if ($entry['procentual'])
                                            echo '%';
                                        echo ' Douriki Heilung an ' . $valName . '<br/>';
                                    }
                                    $value = 'epvalue';
                                    if ($entry[$value] != 0)
                                    {
                                        $valName = 'EP';
                                        echo number_format(round($entry['value'] * $entry[$value] / 100), '0', '', '.');
                                        if ($entry['procentual'])
                                            echo '%';
                                        echo ' Douriki Heilung an ' . $valName . '<br/>';
                                    }
                                }
                                else if ($entry['type'] == 9 || $entry['type'] == 19)
                                {
                                    echo $entry['value'];
                                }
                                else if ($entry['type'] == 6)
                                {
                                    echo $entry['value'] . ' % Douriki pro Ladung';
                                }
                                else if ($entry['type'] == 8)
                                {
                                    if($entry['npcid'] != 0)
                                    {
                                        $npc = new NPC($database, $entry['npcid']);
                                        echo 'Beschwört den NPC: ' . $npc->GetName() . '<br/>';
                                        echo 'LP: ' . number_format($npc->GetMaxLP(), 0, '', '.') . '<br/>';
                                        echo 'AD: ' . number_format($npc->GetMaxKP(), 0, '', '.') . '<br/>';
                                        echo 'ATK: ' . number_format($npc->GetAttack(), 0, '', '.') . '<br/>';
                                        echo 'DEF: ' . number_format($npc->GetDefense(), 0, '', '.') . '<br/>';
                                    }
                                }
                                else
                                {
                                    $value = 'lpvalue';
                                    $valName = 'LP';
                                    if ($entry[$value] != 0)
                                    {
                                        if ($entry['type'] == 20 || $entry['type'] == 2)
                                            echo $valName . ' x ';

                                        echo $entry['value'] * $entry[$value] / 100;
                                        if ($entry['procentual'])
                                            echo '%';

                                        if (/*$entry['type'] == 18 || */$entry['type'] == 22)
                                            echo ' Douriki auf';

                                        if ($entry['type'] != 20 && $entry['type'] != 2)
                                            echo ' ' . $valName;

                                        echo '<br/>';
                                    }
                                    $value = 'kpvalue';
                                    $valName = 'AD';
                                    if ($entry[$value] != 0)
                                    {
                                        if ($entry['type'] == 20 || $entry['type'] == 2)
                                            echo $valName . ' x ';

                                        echo number_format($entry['value'] * $entry[$value] / 100, '0', '', '.');
                                        if ($entry['procentual'])
                                            echo '%';

                                        if (/*$entry['type'] == 18 || */$entry['type'] == 22)
                                            echo ' Douriki auf';

                                        if ($entry['type'] != 20 && $entry['type'] != 2)
                                            echo ' ' . $valName;

                                        echo '<br/>';
                                    }
                                    $value = 'epvalue';
                                    $valName = 'Energie';
                                    if ($entry[$value] != 0)
                                    {
                                        if ($entry['type'] == 20 || $entry['type'] == 2)
                                            echo $valName . ' x ';

                                        echo number_format($entry['value'] * $entry[$value] / 100, '0', '', '.');
                                        if ($entry['procentual'])
                                            echo '%';

                                        if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$entry['type'] == 22)
                                            echo ' Douriki auf';

                                        if ($entry['type'] != 20 && $entry['type'] != 2)
                                            echo ' ' . $valName;

                                        echo '<br/>';
                                    }
                                    $value = 'atkvalue';
                                    $valName = 'Angriff';
                                    if ($entry[$value] != 0)
                                    {
                                        if ($entry['type'] == 20 || $entry['type'] == 2)
                                            echo $valName . ' x ';

                                        echo number_format($entry['value'] * $entry[$value] / 100, '0', '', '.');
                                        if ($entry['procentual'])
                                            echo '%';

                                        if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$entry['type'] == 22)
                                            echo ' Douriki auf';

                                        if ($entry['type'] != 20 && $entry['type'] != 2)
                                            echo ' ' . $valName;

                                        echo '<br/>';
                                    }
                                    $value = 'defvalue';
                                    $valName = 'Verteidigung';
                                    if ($entry[$value] != 0)
                                    {
                                        if ($entry['type'] == 20 || $entry['type'] == 2)
                                            echo $valName . ' x ';

                                        echo $entry['value'] * $entry[$value] / 100;
                                        if ($entry['procentual'])
                                            echo '%';

                                        if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$entry['type'] == 22)
                                            echo ' Douriki auf';

                                        if ($entry['type'] != 20 && $entry['type'] != 2)
                                            echo ' ' . $valName;

                                        echo '<br/>';
                                    }
                                    $value = 'tauntvalue';
                                    $valName = 'Anziehung';
                                    if ($entry[$value] != 0)
                                    {
                                        if ($entry['type'] == 20 || $entry['type'] == 2)
                                            echo $valName . ' x ';

                                        echo $entry['value'] * $entry[$value] / 100;
                                        if ($entry['procentual'])
                                            echo '%';

                                        if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$entry['type'] == 22)
                                            echo ' Douriki auf';

                                        if ($entry['type'] != 20 && $entry['type'] != 2)
                                            echo ' ' . $valName;

                                        echo '<br/>';
                                    }
                                    $value = 'reflectvalue';
                                    $valName = 'Reflektion';
                                    if ($entry[$value] != 0)
                                    {
                                        if ($entry['type'] == 2)
                                            echo $valName . ' x ';

                                        echo $entry['value'] * $entry[$value] / 100;
                                        if ($entry['procentual'])
                                            echo '%';

                                        if (/*$entry['type'] == 18 || $entry['type'] == 21 || */$entry['type'] == 22)
                                            echo ' Douriki auf';

                                        if ($entry['type'] != 2)
                                            echo ' ' . $valName;

                                        echo '<br/>';
                                    }
                                    $value = 'accbuf';
                                    $valName = 'Genauigkeit';
                                    if ($entry[$value] != 0)
                                    {
                                        if ($entry['type'] == 2)
                                            echo $valName . ' x ';

                                        echo $entry['value'] * $entry[$value] / 100;
                                        if ($entry['procentual'])
                                            echo '%';

                                        if ($entry['type'] != 2)
                                            echo ' ' . $valName;

                                        echo '<br/>';
                                    }
                                    $value = 'reflexbuf';
                                    $valName = 'Reflex';
                                    if ($entry[$value] != 0)
                                    {
                                        if ($entry['type'] == 2)
                                            echo $valName . ' x ';

                                        echo $entry['value'] * $entry[$value] / 100;
                                        if ($entry['procentual'])
                                            echo '%';

                                        if ($entry['type'] != 2)
                                            echo ' ' . $valName;

                                        echo '<br/>';
                                    }
                                }
                            ?>
                        </td>
                        <td style="text-align:center">
                            <?php echo $entry['accuracy']; ?>%
                        </td>
                        <td style="text-align:center">
                            <?php
                                if ($entry['energy'] != 0)
                                    echo number_format($entry['energy'], '0', '', '.') . ' EP<br/>';
                                if ($entry['lp'] != 0)
                                {
                                    if ($entry['procentualcost'] == 1) echo ($entry['lp'] / 100) . '%';
                                    else echo number_format($entry['lp'], '0', '', '.');
                                    echo ' LP<br/>';
                                }
                                if ($entry['kp'] != 0)
                                {
                                    if ($entry['singlecost'] == 1) echo "Einmalig ";
                                    if ($entry['kpprocentual'] == 1) echo ($entry['kp']) . '%';
                                    else echo number_format($entry['kp'], '0', '', '.');
                                    echo ' AD<br/>';
                                }
                            ?>
                        </td>
                        <td style="text-align:center">
                            <?php
                                $runden = $entry['rounds'] + 1;
                                if ($runden > 0)
                                {
                                    echo number_format($runden, '0', '', '.') . ' ';
                                    if ($runden == 1) echo 'Runde';
                                    else echo 'Runden';
                                }
                            ?>
                        </td>
                        <td style="text-align:center"><?php echo $entry['race']; ?></td>
                    </tr>
                    <tr>
                        <td colspan="9" class="borderB"></td>
                    </tr>
                    <?php
                    ++$id;
                    $entry = $attacks->GetEntry($id);
                }
            ?>
        </table>
        <?php
        $newAttacks = new Generallist($database, $table, $select, $where, 'attacks.id', 999, 'ASC');
        $total = $newAttacks->GetCount();
        $pages = ceil($total / $limit);

        if($pages != 1)
        {
            ?>
            <div class="spacer"></div>
            <?php
            $i = 0;
            while($i != $pages)
            {
                ?>
                <a href="?p=info&info=techniken<?php if(isset($_GET['attackenname'])) echo '&attackenname='.$_GET['attackenname'];
                    if(isset($_GET['attackentyp'])) echo '&attackentyp='.$_GET['attackentyp'];
                    if(isset($_GET['attackenskilltree'])) echo '&attackenskilltree='.$_GET['attackenskilltree'];
                    if(isset($_GET['attackenrasse'])) echo '&attackenrasse='.$_GET['attackenrasse']; ?>&page=<?php echo $i + 1; ?>">Seite: <?php echo number_format($i + 1, '0', '', '.'); ?></a>
                <?php
                $i++;
            }
        }
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'bande')
    {
        ?>
        <table width="100%">
            <tr>
                <td>
                    <br>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Die Bande</b></div>
                    </div>
                    <br>
                    Die User können sich wie die Charakter aus One Piece zu einer Bande zusammenschließen.<br/>
                    Jeder kann seine eigene Bande gründen oder sich einer bereits bestehenden Bande anschließen.<br/>
                    Um eine Bande erstellen oder beitreten zu können, muss man das 5. Level erreicht haben.<br/>
                    <div class="spacer2"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Wie gründe ich eine Bande?</b></div>
                    </div>
                    <br>
                    Zum einen sollte man zunächst Level 5 erreicht haben.<br>
                    Außerdem betragen die Gründungskosten 2.000 <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 3px; height: 20px; width: 13px;"/>.<br>
                    Derjenige, der die Bande erstellt, hat das volle Administrationsrecht der Bande<br>
                    und ist auch der Kapitän.<br>
                    Im Bandenprofil wird er mit einem roten Stern markiert.<br>
                    <div class="spacer2"></div>
                    Unter dem Punkt “Bande erstellen” kann man seine Bande erstellen. <br>
                    Ein Name der Bande muss eingetragen werden und ein Kürzel der Bande muss festgelegt werden. <br>
                    Diese kann man aber auch noch im Nachhinein ändern für einen Preis von 200 <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>.<br>
                    <div class="spacer2"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Was kann man mit der Bande machen und wofür?</b></div>
                    </div>
                    <br>
                    Die Bande bietet die Funktion einer gemeinsamen Schatzkammer für Berry und auch für Gold.<br>
                    In diese Schatzkammern können alle Bandenmitglieder einzahlen und man kann die Berry auch wieder an einzelne User einzahlen.<br>
                    Somit kann man sich gegenseitig unterstützen, <br>
                    um beispielsweise schwierige Story-Kämpfe zu schaffen.<br>
                    Das Gold kann nicht mehr ausgezahlt werden.<br>
                    <div class="spacer2"></div>
                    Zusätzlich kann die Bande die Berry und das Gold aus der Bandenkasse nutzen,<br>
                    um die Bandenbuffs zu kaufen.<br>
                    Es gibt Buffs für die <b>Lebenspunkte</b>, <b>Ausdauer</b>, <b>Angriff</b> und <b>Abwehr</b>.<br>
                    Diese Buffs werden in <b>PvP-, Dungeon-, NPC- und Kolosseumskämpfen</b> genutzt und verstärken deinen Charakter.<br>
                    Außerdem kann man nach Vervollständigung der Aktivitätenleiste den Bandenrang erhöhen.<br>
                    Der Bandenrang erhöht alle 2 Stufen die maximale Mitgliederzahl der Bande und zu dem die <b>Lebenspunkte</b>, <b>Ausdauer</b>, <b>Angriff</b> und <b>Abwehr</b>.<br>
                    <div class="spacer2"></div>
                    Der Kapitän oder auch der Vize-Kapitän kann zu dem die Beschreibung bearbeiten oder auch Flaggen, Logos oder ein Bandenprofilbild einfügen. <br>
                    Diese sind dann im Bandenprofil oder auf dem eigenen Charakterprofil sichtbar.<br>
                    <div class="spacer2"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Bandenkämpfe</b></div>
                    </div><br>
                    Durch Bandenkämpfen kann man sich Territorien anderer Banden erkämpfen.<br>
                    Man kann sich jede Inseln als Territorium erkämpfen oder<br/>
                    wenn sie noch keine andere Bande besetzt hat,<br>
                    diese für 25.000 <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 3px; height: 20px; width: 13px;"/> aus der Bandenkasse beanspruchen. <br>
                    Ausgenommen davon sind die Orte, an denen ein Dungeon vorzufinden ist.<br>
                    <div class="spacer2"></div>
                    Um einen Bandenkampf zu starten oder ein freies Territorium zu beanspruchen,<br>
                    müssen sich drei Bandenmitglieder am selben Ort befinden und eine Gruppe gebildet haben.<br>
                    <div class="spacer2"></div>
                    Der Ort, der angegriffen wird, geht nach einer Frist von 30 Minuten an die angreifende Bande über.<br>
                    In den 30 Minuten kann die angegriffene Bande diesem Kampf mit drei Bandenmitgliedern beitreten, um das Territorium zu verteidigen.
                    Je nachdem, wie der Kampf ausgeht, behält die verteidigende Bande das Gebiet oder wechselt den Besitzer.<br>
                    Dieser Kampf startet zudem automatisch und sollte man inaktiv sein, kann man nach 2 Minuten aus dem Kampf gekickt werden.
                    Nach einem Bandenkampf hat das Gebiet einen einstündigen Schutz und kann in der Zeit nicht angegriffen werden.<br>
                    <div class="spacer2"></div>
                    Bandenkämpfe oder freie Gebiete kann man nur in einem festen Zeitrahmen durchführen.<br>
                    <div class="spacer2"></div>
                    Diese Zeiten sind wie folgt:<br>
                    <b>Montags bis Freitags von 16 bis 22 Uhr<br>
                        Samstags und Sonntags von 12 bis 22 Uhr</b>
                    <div class="spacer2"></div>
                    Territorien bringen der Bandenkasse Gold, denn jeder Spieler auf Level 5 oder höher muss 10 <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/> bezahlen, um zu der besetzten Bande reisen zu können.<br>
                    <div class="spacer2"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Allianzen</b></div>
                    </div><br>
                    Jede Bande kann mit bis zu zwei anderen Banden Allianzen schmieden. <br/>
                    Die Allianzpartner können derzeit statt für 10 <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/> nur für 5 <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/> zu den besetzten Territorien des Allianzpartners reisen.<br/>
                    <div class="spacer2"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Bandenstärke und Bandenranking</b></div>
                    </div><br/>
                    Je höher die Bandenstärke ist, desto höher steht man im Bandenranking.<br/>
                    Die Bandenstärke wird ab einer Mitgliederzahl von drei angezeigt und berechnet.<br/>
                    Der Durchschnitt der Douriki aller Mitglieder bildet bei der Bandenstärke immer das Fundament.<br/>
                    <div class="spacer2"></div>
                    Zusätzlich erhöht sich die Bandenstärke, wenn die Mitglieder aktiv sind und ihre NPC- und PvP-Kämpfe machen.<br/>
                    <div class="spacer2"></div>
                    Außerdem spielen die Bandenkämpfe und Territorien eine wichtige Rolle bei der Berechnung der Bandenstärke.<br/>
                    Für die Übernahme eines Territoriums im East Blue erhält man 50 Punkte und auf der Grandline erhält man 100 Punkte.<br/>
                    Sollte man dieses Territorium verlieren, verliert man auch wieder die selben Punkte.<br/>
                    Bei einer erfolgreichen Verteidigung erhält man 15 Punkte.<br/>
                    <div class="spacer2"></div>
                    Für das Halten eines Territoriums erhält man nach jeder vollen Stunde Punkte. <br/>
                    Auf dem East Blue sind es 2 Punkte und auf der Grandline sind es 5 Punkte pro Territorium. <br/>
                    Die Punkte fürs Halten für neu gewonnenen Territorien beginnen allerdings immer erst nach dem Update um 23:59 Uhr desselben Tages.<br/>
                    <div class="spacer2"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Banden Tournament</b></div>
                    </div><br/>
                    Wer eine Bande besitzt wird den Link <a href="?p=bandentournament"><b>Banden Tournament</b></a> finden. Dort ist eine Laufbahn abgebildet wo ein<br />
                    Marathon stattfindet! Auf dem Weg ins Ziel gibt es viele Tolle Items zu gewinnen und zwar für alle Crew Mitglieder der Bande!<br />
                    Erreicht die Bande <b>350 Punkte</b> so erhält jeder Spieler in der Bande <b>100 <img src="/img/offtopic/GoldSymbol.png" /></b><br />
                    Erreicht die Bande <b>500 Punkte</b> so erhält jeder Spieler in der Bande <b>50000 <img src="/img/offtopic/BerrySymbol.png" /></b> <br />
                    Erreicht die Bande <b>650 Punkte</b> so erhält jeder Spieler in der Bande das Item <b>Seltene Orangane Frucht</b><br />
                    Erreicht die Bande <b>850 Punkte</b> so erhält jeder Spieler in der Bande das Item <b>Seltene Rote Frucht</b><br />
                    Erreicht die Bande <b>950 Punkte</b> so erhält jeder Spieler in der Bande das Item <b>Vitamine</b><br />
                    Erreicht die Bande <b>1150 Punkte</b> so erhält jeder Spieler in der Bande das Item <b>Testo Booster</b><br />
                    Erreicht die Bande <b>1350 Punkte</b> so erhält jeder Spieler in der Bande <b>100 Elopunkte extra, oben drauf!</b><br />
                    <br />
                    <br />
                    <center><b>Wie erhält man Running Points?</b></center><br />
                    Running Points, sind diese Punkte welche man Sammelt um im Marathon vorwärts zu kommen, ganz einfach gesagt <b>Aktivität!</b><br />
                    Durch die Meisten Aktivität der Clan Member erhält man solche Punkte, zBsp in KGs, Elo´s, NPC Kämpfe unsw....
                    <div class="spacer2"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Banden Quest</b></div>
                    </div><br/>
                    Klickt man auf den Link der Clan Verwaltung so erscheint der Reiter <b>"Aktivität"</b> dort könnt ihr den Fortschritt<br />
                    Wie man genau diese Quests voll bekommt erfahrt ihr an Hand des Namen der Quest<br />
                    Was genau für Belohnungen gibt es für die Quests?
                    <br />
                    <br />
                    - <b>NPC Quest:</b> Jeder Spieler der Bande erhält 10 <img src="/img/offtopic/GoldSymbol.png" /> und 10.000 <img src="/img/offtopic/BerrySymbol.png" /><br />
                    - <b>Elo Kampf Quest:</b> Jeder Spieler in der Bande erhält 10 <img src="/img/offtopic/GoldSymbol.png" />, 5.000 <img src="/img/offtopic/BerrySymbol.png" /> und jeweils 1x das Item Testo Booster & Vitamine!<br />
                    - <b>PvP Kampf Quest:</b> Jeder Spieler in der Bande erhält 10 <img src="/img/offtopic/GoldSymbol.png" />, 5.000 <img src="/img/offtopic/BerrySymbol.png" /> und von jedem Pfad Item 1 Stück!<br />
                    - <b>Dungeon Quest:</b> Jeder Spieler in der Bande erhält 5 <img src="/img/offtopic/GoldSymbol.png" />, 5.000 <img src="/img/offtopic/BerrySymbol.png" /> und jeweils 1x das Item Full LP Trank & Full AD Trank!<br />
                    <div class="spacer2"></div>
                </td>
                <!--<td>
                    <br>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Die Bande</b></div>
                    </div><br>
                    Ein Gruppe von Usern kann sich zu einer Bande zusammenschließen.<br>
                    Entweder man gründet eine eigene oder tritt einer bestehenden Bande bei.<br>
                    Man muss mindestens Level 5 sein um eine Bande erstellen oder beitreten zu können.<br>
                    <br>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Wofür benötigt man eine Bande</b></div>
                    </div><br />
                    In einer Bande befindet sich eine Schatzkammer, in der ihr Berry einzahlen könnt und dies auf die Crew Mitglieder verteilen könnt.<br /><br />
                    Ebenfalls könnt ihr hier Gold in Punkte umwandeln, was für das Ranking entscheidend ist.<br />
                    <br>
                    <br>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Wie schließe ich mich einer Bande an</b></div>
                    </div><br>
                    Man klickt auf „Bande beitreten“, dann sucht man den entsprechenden Namen der Bande aus.<br>
                    Somit schickt man eine Anfrage an die Bande. Dieser wird dann die Aufnahme bestätigen oder ablehnen.<br>
                    <br>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Wie gründe ich eine Bande</b></div>
                    </div><br>
                    Unter dem Menüpunkt „Bande erstellen“ gründet man eine eigene Bande.<br>
                    Man ist dann automatisch der Kapitän der Bande.<br>
                    Die Gründungskosten betragen 2.000 Berry.<br>
                    Als Kapitän hast du volle Administrationsrechte deiner Bande.<br>
                    <br>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Welche Optionen hat man in einer Bande</b></div>
                    </div><br>
                    Der Kapitän bzw. Vize hat die Möglichkeit die Beschreibung zu ändern.<br>
                    Ebenso können Flagge und Logo eingefügt werden, welche im Profil der Bande und auf dem eigenen Profil sichtbar sind.<br>
                    Außerdem kann man die Beschreibung ändern, Regeln einfügen etc. <br>
                    <br />
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Berechnung der Punkte</b></div>
                    </div><br />
                    Es findet in der Bandenliste ein Ranking statt, ab 3 Mitglieder in der Bande erhält man Punkte.
                    <br />
                    Der Reichtum deiner Bande spielt eine Rolle, man kann Gold einzahlen aber nicht wieder auszahlen da es den Punkten hinzugefügt wird.
                    <div class="spacer"></div>
                    Die gesamte Anzahl an Douriki wird zusammengerechnet und durch die Anzahl an Mitglieder geteilt.
                    <div class="spacer"></div>
                    In die Berechnung fällt auch die aktivität, die täglichen NPC Kämpfe werden den Punkten zum Update 0 Uhr hinzugefügt.
                    <br />
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Bandenkämpfe</b></div>
                    </div><br />
                    Um Bandenkämpfe mit anderen Banden zu starten, <br/>
                    muss man sich in einer Gruppe aus 3 Mitgliedern befinden.
                    <br />
                    <br />
                    <b>Wird ein Ort angegriffen:</b> so hat die besitzende Bande 30 Minuten Zeit zum Ort zu reisen und dem Kampf beizutreten, der Kampf ist für die Angreifer automatisch gewonnen<br />
                    wenn niemand dem Kampf beigetreten ist, sind welche beigetreten so startet der Kampf bei 3 Mitglieder sofort, sind weniger als 3 der Bande beigetreten so startet er automatisch nach<br />
                    30 Minuten mit der Anzahl welche dem Kampf beigetreten sind.
                    <br />
                    <br />
                    Alle 3 Mitglieder müssen sich dann an dem Ort befinden, welchen man herausfordern möchte.
                    <div class="spacer"></div>
                    Bandenkämpfe zu starten ist nur zu den Zeiten möglich:
                    <div class="spacer"></div>
                    Samstags & Sonntags: 12:00 - 22:00 Uhr
                    <div class="spacer"></div>
                    Restlichen Tage: 16:00 - 22:00 Uhr
                    <div class="spacer"></div>
                    Jeder Spieler, über Level 5, der zu einem eingenommenen Ort reisen möchte,<br/>
                    muss 10 <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/> zahlen, die direkt in der Schatzkammer landen.
                    <br />
                </td>-->
            </tr>
        </table>
        <div class="spacer"></div>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'items')
    {
        ?>
        <b>Items sind verschiedene Arten von Optionen im Spiel um sich zum Beispiel heilen zu können oder auch aus dem Impel Down zu befreien.</b>
        <br />
        <hr>
        <br />
        <br />
        <table width="100%" cellspacing="0" border="0">
        <tr>
        <td class="boxSchatten" style="text-align: center;"><b>Bild</b></td>
        <td class="boxSchatten" style="text-align: center;"><b>Name</b></td>
        <td class="boxSchatten" style="text-align: center;"><b>Beschreibung</b></td>
        </tr>
        <tr>
        <td class="boxSchatten" style="text-align: center;"><img src="/img/items/SplitterNormal.png" /></td>
        <td class="boxSchatten" style="text-align: center;">Einfacher Splitter</td>
            <td class="boxSchatten" style="text-align: center;">Der einfache Splitter ist ein Item welches mit welchem man andere wichtige Items herstellen kann. Der einfache Splitter ist eins von 3 Splittern. Es gibt hinzu noch den <font color="yellow">seltenen</font> Splitter
                oder auch den <font color="red">legendären</font>! Mit allen 3 Splittern lassen sich Items herstellen, hierfür klickt ihr einfach auf diesen Link <a href="?p=werkstatt">Hier</a> oder klickst im linken Menü auf Werkstatt.</td>
        </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/itemgelbefrucht.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Seltene Gelbe Frucht</td>
                <td class="boxSchatten" style="text-align: center;">Dies ist ein Item welches du nur im Kampf benutzen kannst! Es erhöht den Wert vom Reflex um 50, Standardwert ist 100. Erhöht man seine Reflex so steigt die Chance die Attacke vom Gegner
                auszuweichen!</td>
            </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/itemrotefrucht.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Seltene Rote Frucht</td>
                <td class="boxSchatten" style="text-align: center;">Dies ist ein Item wie schon bei der gelben Frucht welches du nur im Kampf nutzen kannst! Anders wie bei der gelben Frucht erhöhte die Rote Frucht deinen Angriffswert im Kampf
                was dazu führen kann das du mehr Schaden bei deinem Gegner anrichtest. Der Wert deiner ATK erhöht sich um 10%</td>
            </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/itemorangefrucht.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Seltene Orangene Frucht</td>
                <td class="boxSchatten" style="text-align: center;">Wie schon bei den beiden Früchten davor ist dies ein Item welches du nur im Kampf nutzen kannst. Dieses Item erhöht den Wert deiner Verteidigung um 10%, dies sorgt dafür das
                du mehr Schaden vom Gegner einstecken kannst.</td>
            </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/adheal.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Testo Booster</td>
                <td class="boxSchatten" style="text-align: center;">Witmen wir uns nun einem weiteren Item welches du nur im Kampf nutzen kannst! Der Testo Booster ist ein sehr wichtiges und sehr nützliches Item für jeden Kampf,
                ob es nun der PvP Kampf ist oder in einem NPC/Story Kampf es stellt 10% deiner AD wieder her so das du wieder mehr Techniken einsetzen kannst.</td>
            </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/epheal.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Vitamine</td>
                <td class="boxSchatten" style="text-align: center;"><br />Was gibt es wichtigeres im Leben als Vitamine? Wie der Testo Booster kann man den Vitamintrank im Kampf einsetzen, dieses Item stellt deinen EP Wert um 25 Punkte zurück,
                das ist sehr nützlich  um den Cool Down einer Technik entgegen zu wirken.</td>
            </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/statreset.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Stat Reset</td>
                <td class="boxSchatten" style="text-align: center;"><br />Einer der wichtigsten im Spiel ist das Stat Reset, es wird bei dem einen oder anderen sicher schon vorgekommen sein das man selber nicht mehr weiterkommt obwohl andere
                rushen als wenn es kein Morgen gäbe. Das Kann daran liegen das du in deiner Verteilung der Stats bereits Fehler gemacht hast! Dieses Item sorgt dafür das du alle bereits verteilten Stats erneut verteilen kannst!</td>
            </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/skillreset.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Skill Reset</td>
                <td class="boxSchatten" style="text-align: center;"><br />Wie das Stat Reset Item kann das Skill Reset für dich ein ganz schöner Game Changer sein, mit diesem Item steht es dir frei dich nochmal komplett umzuorientieren!
                Aber <font color="red">ACHTUNG: </font>die bisher eingesetzten Items werden dir nicht zurück erstattet, solltest du den Pfad wechseln so musst du dich weiter auf der Suche nach den richtigen Items machen um die Techniken
                des gewählten Pfades zu lernen. <b>Ihr fragt euch sicher was mit der Aura passiert: </b>diese bleibt unter Ausrüstung erhalten, um sie auf dem Stand zu nutzen oder visuell zu kombinieren verschwindet allerdings bei Spezailfähigkeiten!</td>
            </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/boot.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Boot</td>
                <td class="boxSchatten" style="text-align: center;"><br />Das Boot brauchst du um von Ort zu Ort reisen zu können, abgesehen vom Boot gibt es noch mehr Schiffe. Um ein Boot nutzen zu können musst du in dein Inventar und es
                zunächst "Besetzen", ein Boot kann nur eingeschränkt oft genutzt werden bis es repariert werden muss. Es benötigt Holz, Nägel und Stoff um dein Boot reparieren zu können, jedes Schiff benötigt unterschiedlich viel Material
                um repariert werden zu können. Ist das Boot/Schiff nun zu oft genutzt und repariert wurden so kannst du es recyceln und erhältst eins der Resourcen zurück.</td>
            </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/schmiedewerkzeug.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Schmiede Werkzeug</td>
                <td class="boxSchatten" style="text-align: center;"><br />
                    Das Schmiede Werkzeug ist ein sehr entscheidendes Item im Spiel, wie ihr schon mitbekommen habt gibt es jede Menge Rüstungen im Spiel diese kann man leveln, hier kommt das Schmiede Werkzeug ins Spiel
                mit diesem Item könnt ihr eure Rüstung bis auf Level 5 bringen. Dann gibt es noch den <font color="green">Rüstungskristall</font> welches deine Rüstung zusätzlich auf Level 6 bringen kann!</td>
            </tr>
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/onepiecerevivekey.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Kerkerschlüssel</td>
                <td class="boxSchatten" style="text-align: center;"><br />Ein richtiger Fan von One Piece kennt das Impel Down, ein läsitges Gefängnis welches uns sehr einschränkt. Dieser Schlüssel sorgt dafür das man sich sofort befreien kann ohne das
                man 10 Tage warten muss oder das Freunde oder die Bande eine Menge Geld investieren müssen.</td>
            </tr>
            <!--<tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/onepieceOPBGitemreiseticket.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Reiseticket</td>
                <td class="boxSchatten" style="text-align: center;"><br />Dieses Item ist sehr nützlich, es sorgt dafür das du ohne Wartezeit von einem Ort zum anderen reisen kannst! Geh dafür in dein Inventar und klicke auf "Benutzen", sobald ihr
                auf benutzen geklickt habt wirkt das Ticket bei der nächsten Reise! Das Item wirkt nur von Ort zu Ort nicht von Meer zu Meer!</td>
            </tr>-->
            <tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/schatztruhe.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Schatztruhe</td>
                <td class="boxSchatten" style="text-align: center;"><br />Eine Truhe voller Schätz erwartet dich! In dieser Truhe befinden sich sehr nützliche Items, aber Achtung! Sie kann auch leer sein. Es befinden sich folgende Items in Ihr
                    Die wahrscheinlichkeit das sie leer ist liegt bei 27%, bei einer Wahrscheinlichkeit von 10% erhält man das <font color="red">Schmiede Werkzeug</font>. <font color="green">Testo Booster</font> so wie <font color="green">Vitamine</font>
                    sind ebenfalls in dieser Truhe enthalten, die Chance bei den beiden Items liegt jeweils bei 4%. Diese Truhe besitzt auch heilende Fähgikeiten, es besteht die Chance zu 10% eine <font color="green">leichte Medizin</font> zu bekomen, zu 5% eine <font color="green">normale Medizin</font>
                    und ebenfalls zu 5% eine <font color="green">mittelstarke Medizin</font>! Kostenlos Reisen? Wieso nicht! Ebenfalls in der Truhe enthalten das <font color="red">Reiseticket</font> und das bei einer Wahrscheinlichkeit von 3%.
                    Nun kommen wir zu den Splittern! Bei einer Wahrscheinlichkeit von 3, 2 und 1% erhaltet ihr <font color="green">Einfache Splitter</font>, <font color="yellow">Seltene Splitter</font> & <font color="red">Legendäre Splitter</font>!
                    Nun kommen wir zum letzten Inhalt, um dein Schiff reparieren zu können befindet sich in der Truhe das nötige Zeug dazu! Zu jeweils 9% Droprate befinden sich in der Truhe <font color="aqua">Holz, Nägel und Stoff</font></td>
            </tr>
            <!--<tr>
                <td class="boxSchatten" style="text-align: center;"><img src="/img/items/onepieceOPBGitemkolo.png" /></td>
                <td class="boxSchatten" style="text-align: center;">Kolosseum Truhe</td>
                <td class="boxSchatten" style="text-align: center;"><br />Eine Truhe voller Schätz erwartet dich! In dieser Truhe befinden sich sehr nützliche Items, aber Achtung! Sie kann auch leer sein. Es befinden sich folgende Items in Ihr
                    Die wahrscheinlichkeit das sie leer ist liegt bei 15%, bei einer Wahrscheinlichkeit von 14% erhält man das <font color="red">Schmiede Werkzeug</font>. <font color="green">Testo Booster</font> so wie <font color="green">Vitamine</font>
                    sind ebenfalls in dieser Truhe enthalten, die Chance bei den beiden Items liegt jeweils bei 8%. Diese Truhe besitzt auch heilende Fähgikeiten, es besteht die Chance zu 9% eine <font color="green">leichte Medizin</font> zu bekomen und zu 10% eine <font color="green">normale Medizin</font>
                    und ebenfalls zu 3% eine <font color="green">Sehr starke Medizin</font>! Noch nie wurde so viel Geld gefunden wie auf OPBG, aus dem Grund findet ihr bei einer Wahrscheinlichkeit von 8% einen <font color="red">Geldsack.</font>
                    Nun kommen wir zu den Splittern! Bei einer Wahrscheinlichkeit von 10, 3 und 2% erhaltet ihr <font color="green">Einfache Splitter</font>, <font color="yellow">Seltene Splitter</font> & <font color="red">Legendäre Splitter</font>!</td>
            </tr>-->
        </table>

        <div class="spacer"></div>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'allgemeines')
    {
        ?>

        <table width="100%">
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Elopoint Ranking</b></div>
                    </div>
                    <div class="spacer"></div>
                    Die durch Elokämpfe zu erhaltenen Titel beziehen sich auf die Platzierung des Spielers innerhalb seiner gewählten Fraktion.
                    <div class="spacer"></div>
                    <div class="spacer"></div>
                    Die ersten drei Piraten erhalten den Titel Yonko, wohingegen die ersten vier Marineoffiziere den Titel Admiral erhalten.
                    <div class="spacer"></div>
                    Selbiges gilt für die Titel Shichibukai (Pirat Platz 4-10) und Vizeadmiral (Marine Platz 5-11).
                    <div class="spacer"></div>
                    <div class="spacer"></div>
                    Die monatlichen Elo-Bekohnungen sind Fraktionunabhängig und beziehen sich auf die wirkliche Platzierung in der allgemeinen Rangliste.
                    <div class="spacer"></div><div class="spacer"></div>
                    Sowohl die Platzierung als auch die Titel werden monatlich resettet.
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Werte</b></div>
                    </div>
                    <div class="spacer"></div>
                    Lebenspunkte (LP): Je höher dieser Wert ist, desto mehr Schaden kann man maximal einstecken, bevor man kampfunfähig ist und entweder verliert man oder man kann nicht mehr am Kampfgeschehen teilnehmen.
                    <div class="spacer"></div>
                    Ausdauer (AD): Je höher dieser Wert ist, desto öfter kann man Techniken in einem Kampf einsetzen. Sollte der Wert auf null fallen, so kann man gewisse Techniken nicht mehr einsetzen.
                    <div class="spacer"></div>
                    Angriff: Je höher dieser Wert ist, desto mehr Schaden verursachen die Techniken an einem Gegner.
                    <div class="spacer"></div>
                    Abwehr: Je höher dieser Wert ist, desto geringer wird der Schaden, den du aus Techniken von Gegnern erleiden kannst.
                    <div class="spacer"></div>
                    Energiepunkte (EP): Dieser Wert wird höher, je mehr man an gewissen Techniken einsetzt. Das bedeutet, dass wenn man keine Technik mehr einsetzen kann, weil die EP zu hoch ist, diese erst abgebaut werden müssen, bevor man wieder gewisse Techniken nutzen kann.
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Douriki</b></div>
                    </div>
                    <div class="spacer"></div>
                    Douriki ist der Wert, der beschreibt, wie stark du bzw. dein Charakter ist.
                    4 vergebene Statspunkte entsprechen einem Punkt Douriki.
                    <div class="spacer"></div>
                    Solltest du einen Statspunkt auf LP setzen, so steigen deine Lebenspunkte um 10.
                    Daraus folgt, dass du mehr Schaden von deinen Gegnern einstecken kannst.
                    <div class="spacer"></div>
                    Solltest du einen Statspunkt auf AD setzen, so steigt deine Ausdauer um 10.
                    Daraus folgt, dass du öfters Techniken einsetzen kannst.
                    <div class="spacer"></div>
                    Solltest du einen Statspunkt auf Angriff setzen, so steigt der Angriffswert um 2.
                    Daraus folgt, dass du mehr Schaden am Gegner erzeugst.
                    <div class="spacer"></div>
                    Solltest du einen Statspunkt auf Abwehr setzen, so steigt der Verteidigungswert um 1.
                    Daraus folgt, dass der Schaden des Gegners weniger wird.
                    <div class="spacer"></div>
                    Darüber hinaus steigt der Schaden, je höher deine Douriki sind, weil Techniken prozentual Schaden anhand der Douriki machen.
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Berry/Gold</b></div>
                    </div>
                    <div class="spacer"></div>
                    Berry: Mit dieser Währung ist es dir möglich, Items aus dem Shop oder vom Marktplatz zu erwerben.
                    <div class="spacer"></div>
                    Gold: Mit dieser Währung ist es dir möglich, Items aus dem Gold-Shop oder ebenfalls vom Marktplatz zu erwerben. Gold ist im Gegensatz zu Berry seltener zu bekommen.
                    <div class="spacer"></div>
                    Einen Wechselkurs der beiden Währungen gibt es nicht, das schließt auch einen Umtausch aus.
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Story</b></div>
                    </div>
                    <div class="spacer"></div>
                    In der Story ist es dir möglich, die Geschichte der Hauptprotagonisten von One Piece nachzuspielen. Hier gibt es viele Aufgaben zu bewältigen. Beispielsweise Kämpfe gegen NPCs, Fragen zum Wissen rund um One Piece und auch Rätsel.
                    <div class="spacer"></div>
                    Darüber hinaus steigt man durch das Absolvieren dieser Aufgaben im Level und schaltet dadurch bspw. neue Dungeons, Trainings, Techniken oder auch das Tragen von gewisser Rüstungen frei.
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Aktionen</b></div>
                    </div>
                    <div class="spacer"></div>
                    Unter Aktionen findet man mehrere Trainings, welche nach dem Beenden oder zur vollen Stunde zusätzliche Statspunkte geben. Es gibt eine Vielzahl von verschiedenen Aktionen.
                    <div class="spacer"></div>
                    Zum einen gibt es Trainings, die grundsätzlich auf jeder Insel zur Verfügung stehen.
                    Es gibt allerdings auch Spezialtrainings, diese sind nur auf manchen Inseln zu finden und werden erst mit einem entsprechenden Level freigeschaltet. Darüber hinaus findest du auch in der Story sogenannte Storyaktionen, die du zunächst abschließen musst, um die Story weiter verfolgen zu können.
                    <div class="spacer"></div>
                    Spezialtrainings sind Aufgaben im laufe der Story welche dir entweder nützlich werden oder eher weniger, hier lest bitte die Texte! Hat man eins dieser Aufgaben erfüllt so verschwinden sie,
                    <div class="spacer"></div>
                    denn man kann sie nur 1x machen.
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Reise</b></div>
                    </div>
                    <div class="spacer"></div>
                    Um von Insel zu Insel reisen zu können, benötigst du ein Boot oder Schiff, welches du dir im Gold-Shop kaufen kannst. Je weiter du in der Story kommst, desto mehr Auswahl an Schiffen wirst du dort vorfinden. Man kann nur ein Schiff besitzen. Um lossegeln zu können, musst du dich ebenfalls im Inventar ins Schiff setzen.
                    <div class="spacer"></div>
                    Jedes Schiff hat einen Abnutzungsgrad und eine Anzahl, wie oft man das Schiff reparieren kann. Abnutzung beschreibt, wie oft man von Insel zu Insel reisen kann, sobald diese voll sind, muss man mit Hilfe der Items Holz, Nägel und Stoff sein Schiff unter Inventar reparieren. Zusätzlich kann man sein Schiff auch in der Werkstatt durch den Einsatz von einfachen oder seltenen Splitter reparieren. Wenn du dein Schiff oft genug repariert hast, ist dieses nicht mehr einsatzfähig. Dann steigst du im Inventar aus dem Schiff aus und kannst es recyclen. Dafür kannst du Holz, Nägel oder Stoffe erhalten. Anschließend kannst du dir ein neues Schiff kaufen und dieses auch verwenden und weiter reisen.
                    <div class="spacer"></div>
                    Reisen zwischen den Meeren laufen länger und können wie alle Reisen nicht abgebrochen werden.
                    <div class="spacer"></div>
                    Man hat die Möglichkeit mit dem Item Reiseticket, die Reisezeit zwischen den Insel zu überspringen. Zusätzlich kann man die Schnellreisefunktion unter Einsatz von Berry nutzen.
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Kämpfe</b></div>
                    </div>
                    <div class="spacer"></div>
                    Im Spiel gibt es eine Vielzahl von Möglichkeiten, Kämpfe zu bestreiten. Als Belohnung kann man unter anderem Gold, Kopfgeld, Items, Berry und Rüstungen bekommen.
                    <div class="spacer"></div>
                    NPC Kampf: Neben der Story kannst du auf verschiedenen Inseln gegen weitere NPCs kämpfen. Diese können dir neben Berry auch nach einer gewissen Anzahl von Siegen einen Titel mit Bonusstats geben.
                    <div class="spacer"></div>
                    Events: Hier erwarten dich NPC Gegner, bei denen du durch einen Sieg wichtige Items erhalten kannst. Ebenfalls kann es sein, dass du auch hier Titel bekommen kannst.
                    <div class="spacer"></div>
                    Dungeon: Ab einem gewissen Level schaltest du die Dungeon frei. Bei dem Dungeon kannst du 10 Drops am Tag bekommen. Darunter fallen zum einen neue Rüstungen, die du aufleveln kannst und die dir beim Tragen zusätzliche Stats geben, aber auch Trainings-Dungeons, die zusätzliche Stats droppen.
                    <div class="spacer"></div>
                    Elo: Diese Kämpfe geben Berry und Elo-Punkte. Je höher deine Elo-Punkte sind, desto höher ist dein Rang im Ranking. Allerdings bekommt man gegen schwächere Gegner weniger Elo-Punkte.
                    <div class="spacer"></div>
                    Spiegel-Kämpfe: Montags bis Freitags 16-22 Uhr bzw. Samstags und Sonntags 12-22 Uhr sind Spiegel-Kämpfe möglich, diese ermöglichen es dir einen Elo-Kampf oder PvP-Kampf gegen dich selbst zu machen. Voraussetzung dafür ist, dass keine weiteren PvP-Kämpfe oder Elokämpfer verfügbar sind.
                    <div class="spacer"></div>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>Pause</b></div>
                    </div>
                    <div class="spacer"></div>
                    Du kannst dein Charakter in den Status einer Pause versetzen, hierfür musst du auf dein <a href="?p=profil">Profil</a> gehen.
                    <div class="spacer"></div>
                    Weiter unten siehst du den Bereich für die Pause, dort trägst du nun die Anzahl der Tage ein wie lange du pausieren möchtest.
                    <div class="spacer"></div>
                    Die genommenen Tage müssen innerhalb der Season nachgeholt werden! Bei Beginn einer neuen Season werden die gespeicherten Kämpfe wieder zurück gesetzt!
                    <div class="spacer"></div>
                    Du kannst nicht über den Monat / Season hinaus pausieren, es geht maximal bis zum letzten Tag der laufenden Season.
                </td>
            </tr>
        </table>
        <div class="spacer"></div>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'regeln')
    {
        ?>
        <table width="100%" cellspacing="0" border="0" style="vertical-align:top;" valign="top">
            <tr>
                <td class="catGradient borderB borderT" colspan="6" style="text-align:center">
                    <b>Regeln</b>
                </td>
            </tr>
        </table>
        <center>
            <img src="img/marketing/onepieceRegelwerk.png" alt="Regeln" title="Regeln"/>
        </center>
        <br />
        Wer sich einen Account oder Charakter auf OPBG.de erstellt, verpflichtet sich dazu diese Regeln einzuhalten.
        <div class="spacer"></div>
        <span style="color: #FF0000;">§1. Multiusing</span>
        <div class="spacer"></div>
        1.0 Wer mehr als einen Account oder Charakter erstellen möchte, benötigt zuvor eine Erlaubnis von Sload oder Shirobaka.
        Anderenfalls ist dies verboten. Ein 2. Charakter wird ab sofort nur noch für tatsächliche Familienmitglieder genehmigt die in einem Haushalt leben.
        <div class="spacer"></div>
        1.1 Wer mehr als eine Charakter / Account genehmigt bekommt steht unter genauerer Beobachtung.
        <div class="spacer"></div>
        1.2 Das generelle miteinander Interagieren ist untersagt, dies bedeutet unter Anderem, das keine PvP Kämpfe, Dungeons
        oder NPC Fights gemeinsam durchgeführt werden dürfen. Sollte sich der Fall ergeben, das die Charaktere gegen eine Regel verstoßen so kann die Genehmigung wieder
        entzogen werden, ggf. haften alle Charaktere / Accounts für Verstöße.
        <div class="spacer"></div>
        1.3 Es besteht die Möglichkeit, sollte ein Spieler längere Zeit nicht aktiv spielen können, das der Charakter für einen
        festgelegten Zeitraum auf den Account einer anderen Person überschrieben werden kann, für diese Charaktere gelten dann ebenfalls die in §1.2 genannten Regeln.
        <div class="spacer"></div>
        1.4 Das übertragen von Spielinhalte wie z.B. Berry, Gold oder Items von Charakteren die vor haben sich zu löschen oder generell nicht mehr am Spiel teilhnehmen wollen ist strengstens untersagt.
        <div class="spacer"></div>
        1.5 Das verschenken unter Banden Mitglieder darf nicht ausgenutzt werden, der Beitritt einer Bande nur um diese Funktion nutzen zu können ist verboten, sollte ein verstoß dieser Regel erkannt werden so bedeutet dies den ausschluss vom Spiel.
        <div class="spacer"></div>
        <span style="color: #008BFF;">Info: Lasst euren Charakter nur auf eine andere Person übertragen, wenn ihr dieser Person zu 100% vertraut,
                sollte diese Person wegen eines Regelverstoßes gesperrt werden, ist auch der Charakter nicht wieder übertragbar.</span>
        <div class="spacer"></div>
        <span style="color: #FF0000;">§2 Beleidigungen / Provokationen</span>
        <div class="spacer"></div>
        2.0 Das direkte Beleidigen eines anderen User ist strengstens untersagt.
        Wer mit absicht andere User persönlich angreift oder beleidigt muss mit einer Verwarnung oder Ausschluss vom Spiel rechnen.
        <div class="spacer"></div>
        2.1 Provokationen sind ebenfalls untersagt, sobald klar wir dass das gegenüber kein Interesse daran hat in so einer Form mit
        dir zu kommunizieren!
        <div class="spacer"></div>
        2.2 Die oben aufgeführten Regeln gelten für alle Bereiche mit welche man auf unserer Plattform kommunizieren kann.
        <div class="spacer"></div>
        <span style="color: #008BFF;">Info: Wir sind eine sehr lockere Community, entsprechend kann der Umgangston untereinander auch etwas "harscher" werden,
                dies sollte allerdings nicht übertrieben werden und in einem angemessenen Rahmen bleiben.
                Sollte eine Person klarstellen, das er sich angegriffen fühlt, bzw. auf diese Art nicht kommunizieren möchte, hast du dein Verhalten anzupassen.</span>
        <div class="spacer"></div>
        <span style="color: #FF0000;">§3 Werbung / Seitenverlinkungen</span>
        <div class="spacer"></div>
        3.0 Das Verlinken externer Seiten ist ohne vorherige Genehmigung durch das Team nicht gestattet.
        Verlinkungen zu Partner sind gestattet und bedarf keiner vorherigen Genehmigung.
        <div class="spacer"></div>
        3.1 Das Werben anderer Seiten oder so genannte "Schleichwerbung" ist untersagt und kann verwarnt werden.
        Dies gilt für alle Bereiche von OPBG.de und auf dem Discord.
        <div class="spacer"></div>
        <span style="color: #FF0000;">§4 Sich als Team Mitglied ausgeben</span>
        <div class="spacer"></div>
        4.0 Sich als Team-Mitglied des OPBG-Team auszugeben ist untersagt und kann je nach schwere bis zu einen Ausschluss führen.
        <div class="spacer"></div>
        4.1 Ebenfalls ist es nicht gestattet, sich als eine andere Person auszugeben.
        <div class="spacer"></div>
        <span style="color: #FF0000;">§5 Kommunikation</span>
        <div class="spacer"></div>
        5.0 Wir sind ein deutschsprachiges Browsergame und somit ist auch nur in dieser Sprache zu schreiben,
        das nutzen einer Fremdsprache ist in allen Bereichen des Spiels verboten.
        <div class="spacer"></div>
        <span style="color: #FF0000;">§6 Fake Fights</span>
        <div class="spacer"></div>
        6.1 Es ist untersagt Kämpfe absichtlich zu verlieren.
        <div class="spacer"></div>
        6.2 Es ist untersagt sich in einem Kampf selber zu kicken.
        <div class="spacer"></div>
        6.3 In einem PvP Kampf oder in dem Kolosseum ist das Aufgeben ab Runde 30 gestattet, sofern der Gegner noch mehr als 50% seiner Lebenspunkte (50%) übrig hat.
        <div class="spacer"></div>
        6.4 Das absichtliche verlängern eines Kampfes ist ebenfalls verboten.
        <div class="spacer"></div>
        6.5 Das AFK gehen während eines Kampfes ist verboten.
        <div class="spacer"></div>
        6.6 Es dürfen nur feste Mitglieder einer Bande an einen Bandenkampf teilnehmen, teilnehmende Bandenmitglieder müssen mindestens 5 Tage Mitglied der Bande sein.
        <div class="spacer"></div>
        <span style="color: #008BFF;">Info: Jeder User ist dazu verpflichtet, Regelverstöße die er mitbekommt an das Team zu melden, sollte dies nicht passieren macht man sich des selben verstoßes Strafbar!</span>
        <div class="spacer"></div>
        <span style="color: #FF0000;">§7 Nutzung von Refresher / Bots</span>
        <div class="spacer"></div>
        7.0 Die Nutzung von Bots oder Refreshern ist nicht gestattet, außerdem ist das verfälschen der IP Adresse auf welche Art auch immer verboten.
        <div class="spacer"></div>
        <span style="color: #008BFF;">Info: Alle Arten mit welche man versucht die eigenklicks zu umgehen führen direkt zum Ausschluss vom Spiel.</span>
        <div class="spacer"></div>
        <span style="color: #FF0000;">§8 Pornografisch/Anstößige Inhalte</span>
        <div class="spacer"></div>
        8.0 Es ist strengstens untersagt Pornografische oder Anstößige Inhalte und alles was in diese Richtung geht
        in irgend einer Form zu Posten, dies gilt auf der gesamten OPBG.de Plattform und auf dem Discord.
        <div class="spacer"></div>
        8.1 Es ist ebenfalls nicht gestattet Politische oder Religiöse Inhalte zu verbreiten bzw. Rassistische Äußerungen
        von sich zu geben.
        <div class="spacer"></div>
        <span style="color: #FF0000;">§9 Account Handel</span>
        <div class="spacer"></div>
        9.0 Es ist verboten seinen Account / Charakter zu veräußern, in irgend einer weise zu verschenken oder zu tauschen.
        <div class="spacer"></div>
        <span style="color: #008BFF;">Info: Sollte ein Regelverstoß dieser Art vorkommen, führt dies zu einen Ausschluss sämtlicher Charaktere der betroffenen Spieler.</span>
        <div class="spacer"></div>
        <span style="color: #FF0000;">§10 Spam</span>
        <div class="spacer"></div>
        10.0 Das Spammen von Nachrichten in jeglicher Form ist in alle möglichen Kommunikationsmöglichkeiten auf der Plattform untersagt,
        hierzu zählen auch Kettenbriefe und ähnliches.
        <div class="spacer"></div>
        <span style="color: #FF0000;">§11 Bugusing</span>
        <div class="spacer"></div>
        11.0 Sollte ein Spieler bewusst einen Bug für sich zum Vorteil nutzen, so kann es neben einer Verwarnung ebenfalls
        zum Ausschluss vom Spiel führen.
        <div class="spacer"></div>
        Info: Jeder Spieler ist verpflichtet gefundene Fehler an das Team zu melden.
        <span style="color: #008BFF;">Info: Jeder Spieler ist verpflichtet gefundene Fehler an das Team zu melden.</span>
        <div class="spacer"></div>
        <span style="color: #FF0000;">§12 Entsperrungen</span>
        <div class="spacer"></div>
        12.0 Anfragen zum Entfernen einer Verwarnung oder einer entsperre kann nur von einem Director oder Admin beantwortet werden,
        die zuständigen findet man in der oberen Leiste bei Team.
        <div class="spacer"></div>
        <span style="color: #FF0000;">§13 Das Umgehen von Regeln</span>
        <div class="spacer"></div>
        13.0 Das umgehen von Regeln ist verboten, die Strafe richtet sich nach der umgangenen Regel.
        <div class="spacer"></div>
        <span style="color: #008BFF;">Info: Schon der versuch ist strafbar.</span>
        <div class="spacer"></div>
        <span style="color: #FF0000;">§14 Grafiken & Bilder</span>
        <div class="spacer"></div>
        14.0 Wer Grafiken oder künstlerische Inhalte für OPBG.de zur Verfügung stellt, überträgt damit auch das geistige Eigentum auf die Leitung von OPBG, der Grafikersteller hat keine Rechte mehr an die betreffende Grafik.
        <div class="spacer"></div>
        <span style="color: #FF0000;">§15 Sonstiges</span>
        <div class="spacer"></div>
        15.0 Das Team behält sich das Recht vor, auch hier nicht aufgelistete Regeln zu ahnden, sofern es sich aus logischer Sicht ebenfalls um ein Verstoß handelt.
        <div class="spacer"></div>
        <fieldset>
            <legend>
                <b>
                    Anmeldung:
                </b>
            </legend>
            <table>
                <tr>
                    <td>
                        <b>
                            <span style="color: #0066FF;">1:</span>
                        </b>
                    </td>
                    <td> Mit Anmeldung gelten die Regeln als gelesen und akzeptiert.</td>
                </tr>
                <tr>
                    <td>
                        <b>
                            <span style="color: #0066FF;">2:</span>
                        </b>
                    </td>
                    <td> Wer gegen die Regeln verstößt, kann mit Verwarnung, Sperrung oder Bann rechnen.
                        <br />
                        Unwissenheit schützt vor Strafe nicht!
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            <span style="color: #0066FF;">3:</span>
                        </b>
                    </td>
                    <td> Dauersperren auf OPBG-Welten gelten für alle anderen Welten und sind nachträglich übertragbar. </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            <span style="color: #0066FF;">4:</span>
                        </b>
                    </td>
                    <td> Bei möglichen Missverständnisen oder Anfragen bitte eine Entsperrungsanfrage über das gegebene Feld absenden bzw. beim Support melden. </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            <span style="color: #0066FF;">5:</span>
                        </b>
                    </td>
                    <td> Es besteht keinerlei Anspruch auf einen Account in OPBG. OPBG behält sich somit das Recht vor, die Anmeldung für einzelne Personen zu verweigern oder rückgängig zu machen.</td>
                </tr>
            </table>
        </fieldset>
        <div class="spacer"></div>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'bbcode')
    {
        ?>
        <table width="100%">
            <tr>
                <td>
                    <div class="catGradient borderT borderB">
                        <div style="text-align:center"><b>BB Code</b></div>
                    </div>
                    Ist eine an HTML angelehnte, jedoch vereinfachte Form, die durch simple Änderungen des Codes, euch erlauben, Texte in eurer Signatur zu formatieren.<br />
                    Dadurch könnt ihr den Inhalt von einem Text individuell gestalten.<br>
                    <div style="text-align:center">Folgende Codes stehen euch zur Verfügung:
                        <div class="spacer"></div>
                        <b>Dicker Text: [b]Dicker Text[/b]</b><br>
                        <u>Unterstrichener Text: [u]Unterstrichener Text[/u]</u><br>
                        <i>Kursiver Text: [i]Unterstrichener Text[/i]</i><br>
                        Ein Bild anzeigen: [img]URL ZUM BILD[/img]<br>
                        Verlinkung: [url=URL LINK]TEXT[/url]<br>
                    </div>
                </td>
            </tr>
        </table>
        <div class="spacer"></div>
        <?php
    }
    else if (isset($_GET['info']) && $_GET['info'] == 'impressum')
    {
        ?>
        <div class="spacer"></div>
        <table width="100%" cellspacing="0" border="0" style="text-align: center;">
            <tr>
                <td colspan=6 height="20px">
                    <div class="SideMenuKat catGradient borderB">
                        <div class="schatten">Impressum</div>
                    </div>
                    <div class="spacer"></div>
                </td>
            </tr>
            <tr>
                <td width="30%">
                    <b>E-Mail</b>
                </td>
                <td width="20%">
                    <b>Name</b>
                </td>
                <td width="25%">
                    <b>Adresse</b>
                </td>
                <td width="25%">
                    <b>Angaben</b>
                </td>
            </tr>
            <tr>
                <td width="30%">
                    <p><a href="mailto:p-u-r-e@hotmail.de">p-u-r-e@hotmail.de</a>
                </td>
                <td width="20%">
                    André Ewald
                </td>
                <td width="25%">
                    Obergasse 11A<br />
                    55576 Welgesheim
                </td>
                <td width="25%">
                    Angaben gemäß § 5 TMG
                </td>
            </tr>
            <tr>
                <td colspan=6 height="20px">
                    <hr>
                    <b>Haftungsausschluss:</b><br> <b>Haftung für Inhalte</b>
                    <div class="spacer"></div>
                    Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen. Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen
                    Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige
                    Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung
                    möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.<br>
                    <hr>
                    <b>Haftung für Links</b><br> Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten
                    ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar.
                    Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.
                    <hr>

                    <b>Urheberrecht</b><br> Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes
                    bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet. Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden,
                    werden die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir um einen entsprechenden Hinweis. Bei Bekanntwerden von
                    Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.
                    <hr>

                    <b>Datenschutz</b><br> Die Nutzung unserer Webseite ist in der Regel ohne Angabe personenbezogener Daten möglich. Soweit auf unseren Seiten personenbezogene Daten (beispielsweise Name, Anschrift oder eMail-Adressen so wie IP Adressen(IP Adressen werden in dem Falle Temporär Gespeichert)) erhoben werden, erfolgt dies,
                    soweit möglich, stets auf freiwilliger Basis. Diese Daten werden ohne Ihre ausdrückliche Zustimmung nicht an Dritte weitergegeben. Wir weisen darauf hin, dass die Datenübertragung im Internet (z.B. bei der Kommunikation per E-Mail) Sicherheitslücken
                    aufweisen kann. Ein lückenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht möglich. Der Nutzung von im Rahmen der Impressumspflicht veröffentlichten Kontaktdaten durch Dritte zur Übersendung von nicht ausdrücklich angeforderter Werbung
                    und Informationsmaterialien wird hiermit ausdrücklich widersprochen. Die Betreiber der Seiten behalten sich ausdrücklich rechtliche Schritte im Falle der unverlangten Zusendung von Werbeinformationen, etwa durch Spam-Mails, vor.
                    <hr>

                    <b>Urheber</b><br>Zur Verfügung gestellt für die eigene Nutzung von René Siegner alias Sload.<br />
                    <hr>

                    <b>Quelle: Disclaimer von eRecht24, dem Portal zum Internetrecht von Rechtsanwalt Sören Siebert.</b>
                    <div class="spacer"></div>
                </td>
            </tr>
        </table>
        <?php
    }
?>