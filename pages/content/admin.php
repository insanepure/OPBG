<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:90%;">
    <h2>System Menu</h2>
</div>
<script type="text/javascript" src="js/admin.js?15"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div class="spacer"></div>
<?php

    if ($player->GetArank() >= 2)
    {
        if (!isset($_GET['table']))
        {
            ?>
            <table width="90%">
                <?php
                    $i = 0;
                    $result = $database->ShowTables();
                    while ($tableName = mysqli_fetch_row($result))
                    {
                        if ($player->GetArank() < 3 && !in_array($tableName[0], $limitedTables))
                        {
                            continue;
                        }

                        if ($i % 2 == 0)
                        {
                            ?><tr><?php
                        }
                        $table = $tableName[0];
                        ?>
                        <td width="50%" height="25px" align="center"><a href="?p=admin&table=<?php echo $table; ?>"><input type="submit" value="<?php echo $table; ?>" style="width:fit-content; min-width: 200px; "></a></td>
                        <?php
                        if ($i % 2 == 1)
                        {
                            ?>
                            </tr><?php
                        }
                        ++$i;
                    }
                    $result->Close();
                ?>
            </table>
            <?php
        }
        else if ($player->GetArank() < 3 && !in_array($_GET['table'], $limitedTables))
        {
            ?>Diese Tabelle ist nicht verfügbar.<br /><?php
        }
        else if (isset($_GET['a']) && $_GET['a'] == 'see')
        {
            $attacks = null;
            $npcs = null;
            $items = null;
            $actions = null;
            $places = null;

            $table = $_GET['table'];
            $id = 0;
            $where = '';
            if (isset($_GET['id']))
            {
                $id = $_GET['id'];
                $where = 'id="' . $id . '"';
            }
            echo '<h3>', $table, '</h3>';
            $result = $database->Select('*', $table, $where);
            /* Get field information for all columns */
            $finfo = $result->fetch_fields();
            $row = $result->fetch_assoc();

            $action = '?p=admin&table=' . $table . '&a=edit';
            ?>
            <form method="POST" action="<?php echo $action; ?>">
                <table width="100%">
                    <?php
                        foreach ($finfo as $val)
                        {
                            $name = $val->name;
                            $type = $val->type;
                            $value = '';
                            if ($id != 0 && isset($row[$name]))
                            {
                                if($name == 'quizcorrect')
                                    continue;
                                $value = $row[$name];
                            }
                            ?>
                            <tr>
                                <td>
                                    <fieldset>
                                        <legend><b><?php echo $name; ?></b></legend>
                                        <table width="100%">
                                            <tr>
                                                <td align="center">
                                                    <?php
                                                        if ($name == 'titels')
                                                        {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="playertitels">
                                                                <tr>
                                                                    <td><b>Titel</b></td>
                                                                </tr>
                                                                <?php
                                                                    $titels = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($titels))
                                                                    {
                                                                        if ($titels[$i] == 0)
                                                                        {
                                                                            ++$i;
                                                                            continue;
                                                                        }
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select" style="background-color:#030303; width:300px;" name="player_titel[<?php echo $i; ?>]">
                                                                                    <?php
                                                                                        if (!isset($titelList) || $titelList == null)
                                                                                        {
                                                                                            $titelList = new Generallist($database, 'titel', '*', '', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $titelList->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($titels[$i] == $entry['id']) echo 'selected'; ?>> <?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $titelList->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <?php
                                                                                if($titels[$i] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=titel&id=<?php echo $titels[$i];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('playertitels', 1);" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                            }
                                                            else if ($name == 'patterns')
                                                            {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="fighterpatterns">
                                                                <tr>
                                                                    <td><b>Patterns</b></td>
                                                                </tr>
                                                                <?php
                                                                    $patterns = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($patterns))
                                                                    {
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select" name="fighter_patterns[<?php echo $i; ?>]" style="width:300px;">
                                                                                    <?php
                                                                                        if ($patternList == null)
                                                                                        {
                                                                                            $patternList = new Generallist($database, 'patterns', '*', '', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $patternList->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($patterns[$i] == $entry['id']) echo 'selected'; ?>> <?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $patternList->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <?php
                                                                                if($patterns[$i] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=patterns&id=<?php echo $patterns[$i];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('fighterpatterns', 1)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                            }
                                                            else if ($name == 'passives')
                                                            {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="attackpassives">
                                                                <tr>
                                                                    <td><b>Passives</b></td>
                                                                </tr>
                                                                <?php
                                                                    $passives = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($passives))
                                                                    {
                                                                        if(isset($_GET['id']))
                                                                        {
                                                                            ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <select class="select" name="attack_passives[<?php echo $i; ?>]" style="width:300px;">
                                                                                        <?php
                                                                                            if ($passiveList == null)
                                                                                            {
                                                                                                $passiveList = new Generallist($database, 'passives', '*', '', '', 99999999999, 'ASC');
                                                                                            }
                                                                                            $id = 0;
                                                                                            $entry = $passiveList->GetEntry($id);
                                                                                            while ($entry != null)
                                                                                            {
                                                                                                ?>
                                                                                                <option value="<?php echo $entry['id']; ?>" <?php if ($passives[$i] == $entry['id']) echo 'selected'; ?>> <?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                                <?php
                                                                                                ++$id;
                                                                                                $entry = $passiveList->GetEntry($id);
                                                                                            }
                                                                                        ?>
                                                                                    </select>
                                                                                </td>
                                                                                <?php
                                                                                    if($passives[$i] != 0)
                                                                                    {
                                                                                        ?>
                                                                                        <td>
                                                                                            <a href="?p=admin&a=see&table=passives&id=<?php echo $passives[$i];?>"><button type="button">Bearbeiten</button></a>
                                                                                        </td>
                                                                                        <?php
                                                                                    }
                                                                                ?>
                                                                                <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('attackpassives', 1)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                            }
                                                            else if ($name == 'multiaccounts')
                                                            {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="multiaccs">
                                                                <tr>
                                                                    <td><b>Charakter</b></td>
                                                                </tr>
                                                                <?php
                                                                    $multis = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($multis))
                                                                    {
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select" name="multi_accs[<?php echo $i; ?>]" style="width:300px;">
                                                                                    <?php
                                                                                        if ($accounts == null)
                                                                                        {
                                                                                            $accounts = new Generallist($database, 'accounts', '*', '', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $accounts->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['name']; ?>" <?php if ($multis[$i] == $entry['name']) echo 'selected'; ?>> <?php echo $entry['name']; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $accounts->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <?php
                                                                                if($multis[$i] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=accounts&id=<?php echo $multis[$i];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('multiaccs', 1)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                            }
                                                            else if (($table == 'npcs' || $table == 'story' || $table == 'sidestory' || $table == 'fights') && $name == 'items')
                                                            {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="npcandstoryitems">
                                                                <tr>
                                                                    <td><b>Item</b></td>
                                                                    <td><b>Chance</b></td>
                                                                </tr>
                                                                <?php
                                                                    $npcItems = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($npcItems))
                                                                    {
                                                                        $item = explode('@', $npcItems[$i]);
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select" name="npcandstory_item[<?php echo $i; ?>]" style="width:300px;">
                                                                                    <?php
                                                                                        if ($items == null)
                                                                                        {
                                                                                            $items = new Generallist($database, 'items', '*', '', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $items->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($item[0] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $items->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <td><input type="text" name="npcandstory_itemchance[<?php echo $i; ?>]" value="<?php echo $item[1]; ?>" style="width:70px"></td>
                                                                            <?php
                                                                                if($item[0] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=items&id=<?php echo $item[0];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('npcandstoryitems', 2)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                            }
                                                            else if (($table == 'tournaments' || $table == 'actions' || $table == 'titel') && $name == 'items' || $table == 'treasurehuntislands' && $name == 'loot')
                                                            {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="amountitems">
                                                                <tr>
                                                                    <td style="width:300px;"><b>Item</b></td>
                                                                    <td><b>Amount</b></td>
                                                                </tr>
                                                                <?php
                                                                    $npcItems = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($npcItems))
                                                                    {
                                                                        $item = explode('@', $npcItems[$i]);
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select" name="amountitems_item[<?php echo $i; ?>]" style="width:300px;">
                                                                                    <?php
                                                                                        if ($items == null)
                                                                                        {
                                                                                            $items = new Generallist($database, 'items', '*', '', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $items->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($item[0] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $items->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <td><input type="text" name="amountitems_amount[<?php echo $i; ?>]" value="<?php echo $item[1]; ?>" style="width:70px"></td>
                                                                            <?php
                                                                                if($item[0] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=items&id=<?php echo $item[0];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('amountitems', 2)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                            }
                                                            else if ($name == 'quizanswers')
                                                            {
                                                            if($value != '')
                                                                $answers = explode('@', $value);
                                                            else
                                                                $answers = array('', '', '');

                                                            $corrects = explode(';',$row['quizcorrect']);
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="items">
                                                                <tr>
                                                                    <td style="width:300px;" colspan="3"><b>Antworten</b></td>
                                                                </tr>
                                                                <?php
                                                                    for($i = 0; $i < 3; $i++)
                                                                    {
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="text" name="answer[<?php echo $i; ?>]" value="<?php echo $answers[$i]; ?>" style="width: 400px;">
                                                                            </td>
                                                                            <td>
                                                                                <input type="checkbox" name="correct[<?php echo $i; ?>]" <?php if(in_array($i, $corrects)) echo 'checked'; ?> style="cursor: pointer;">
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </table>
                                                            <?php
                                                            }
                                                            else if ($table == 'items' && $name == 'items')
                                                            {
                                                            //$npcItems[0] => can be empty
                                                            //$npcItems[1] => chooseable
                                                            //$npcItems[2] => berrymin @ berrymax
                                                            //$npcItems[3] => goldmin @ goldmin
                                                            //$npcItems[4+] => itemids
                                                            $npcItems = explode(';', $value);
                                                            $berry = explode('@', $npcItems[2]);
                                                            $gold = explode('@', $npcItems[3]);
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="items">
                                                                <tr>
                                                                    <td style="width:300px;" colspan="3"><b>Items</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width:300px;" colspan="2"><label for="empty">Kann "nichts" enthalten?</label></td>
                                                                    <td><input type="checkbox" id="empty2" name="empty" onclick="choose2.checked = false;" <?php if($npcItems[0] == 1) echo 'checked' ?>></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width:300px;" colspan="2"><label for="choose">Items auswählbar?</label></td>
                                                                    <td colspan="2"><input type="checkbox" id="choose2" name="choose" onclick="empty2.checked = false;" <?php if($npcItems[1] == 1) echo 'checked' ?>></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width:300px;">Kann Berry enthalten?</td>
                                                                    <td><input type="number" id="berrymin" name="berrymin" placeholder="min" value="<?php echo $berry[0]; ?>"></td>
                                                                    <td><input type="number" id="berrymax" name="berrymax" placeholder="max" value="<?php echo $berry[1]; ?>"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width:300px;">Kann Gold enthalten?</td>
                                                                    <td><input type="number" id="goldmin" name="goldmin" placeholder="min" value="<?php echo $gold[1]; ?>"></td>
                                                                    <td><input type="number" id="goldmax" name="goldmax" placeholder="max" value="<?php echo $gold[1]; ?>"></td>
                                                                </tr>
                                                                <?php
                                                                    $npcItems = explode(';', $value);
                                                                    $i = 4;
                                                                    while ($value != '' && $i != count($npcItems))
                                                                    {
                                                                        $item = explode('@', $npcItems[$i]);
                                                                        ?>
                                                                        <tr>
                                                                            <td colspan="2">
                                                                                <select class="select" name="items[<?php echo $i; ?>]" style="width:200px;">
                                                                                    <option value="0" <?php if ($item[0] == 0) echo 'selected'; ?>>Kein Item(0)</option>
                                                                                    <?php
                                                                                        if ($items == null)
                                                                                        {
                                                                                            $items = new Generallist($database, 'items', '*', '', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $items->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($item[0] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $items->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <?php
                                                                                if($item[0] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=items&id=<?php echo $item[0];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                        <br />
                                                            <a onclick="AddTableRow('items', 1)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                        <?php
                                                            }
                                                            else if ($table == 'events' && $name == 'fights')
                                                            {
                                                            ?>
                                                            <table width="100%" cellspacing="0" id="eventrounds" border="1">
                                                                <?php
                                                                    $fights = explode('@', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($fights))
                                                                    {
                                                                        $fight = explode(';', $fights[$i]);
                                                                        $fnpcs = explode(':', $fight[0]);
                                                                        if (!isset($fight[1]))
                                                                            $healing = 0;
                                                                        else
                                                                            $healing = $fight[1];

                                                                        if (!isset($fight[2]) || !is_numeric($fight[2]))
                                                                            $survivalteam = 0;
                                                                        else
                                                                            $survivalteam = $fight[2];

                                                                        if (!isset($fight[3]) || !is_numeric($fight[3]))
                                                                            $survivalrounds = 0;
                                                                        else
                                                                            $survivalrounds = $fight[3];

                                                                        if (!isset($fight[4]) || !is_numeric($fight[4]))
                                                                            $survivalWinner = 0;
                                                                        else
                                                                            $survivalWinner = $fight[4];

                                                                        if (!isset($fight[5]) || !is_numeric($fight[5]))
                                                                            $healthRatio = 0;
                                                                        else
                                                                            $healthRatio = $fight[5];

                                                                        if (!isset($fight[6]) || !is_numeric($fight[6]))
                                                                            $healthRatioTeam = 0;
                                                                        else
                                                                            $healthRatioTeam = $fight[6];

                                                                        if (!isset($fight[7]) || !is_numeric($fight[7]))
                                                                            $healthRatioWinner = 0;
                                                                        else
                                                                            $healthRatioWinner = $fight[7];
                                                                        ?>
                                                                        <tr>
                                                                            <td width="15%">Runde <?php echo $i + 1; ?></td>
                                                                            <td width="80%" align="left">
                                                                                <table width="100%" cellspacing="0" id="eventnpcs[<?php echo $i; ?>]">
                                                                                    <?php
                                                                                        $j = 0;
                                                                                        while ($fnpcs != '' && $j != count($fnpcs))
                                                                                        {
                                                                                            $fnpc = $fnpcs[$j];
                                                                                            ?>
                                                                                            <tr>
                                                                                                <td width="80%">
                                                                                                    <select class="select" name="event_npcs[<?php echo $i; ?>][]" style="width:400px;">
                                                                                                        <?php
                                                                                                            if ($npcs == null)
                                                                                                            {
                                                                                                                $npcs = new Generallist($database, 'npcs', '*', '', '', 99999999999, 'ASC');
                                                                                                            }
                                                                                                            $id = 0;
                                                                                                            $entry = $npcs->GetEntry($id);
                                                                                                            while ($entry != null)
                                                                                                            {
                                                                                                                ?>
                                                                                                                <option value="<?php echo $entry['id']; ?>" <?php if ($entry['id'] == $fnpc) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                                                <?php
                                                                                                                ++$id;
                                                                                                                $entry = $npcs->GetEntry($id);
                                                                                                            }
                                                                                                        ?>
                                                                                                    </select>
                                                                                                </td>
                                                                                                <?php
                                                                                                    if($fnpc != 0)
                                                                                                    {
                                                                                                        ?>
                                                                                                        <td>
                                                                                                            <a href="?p=admin&a=see&table=npcs&id=<?php echo $fnpc;?>"><button type="button">Bearbeiten</button></a>
                                                                                                        </td>
                                                                                                        <?php
                                                                                                    }
                                                                                                ?>
                                                                                                <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                                            </tr>
                                                                                            <?php
                                                                                            ++$j;
                                                                                        }
                                                                                    ?>
                                                                                </table>
                                                                                <br />
                                                                                <a onclick="AddTableRow('eventnpcs[<?php echo $i; ?>]', 1)">NPC hinzufügen</a><br /><br />
                                                                                <input type="checkbox" name="event_fhealing[<?php echo $i; ?>]" <?php if ($healing) echo 'checked'; ?>> Healing</br>
                                                                                <input type="number" name="event_survivalteam[<?php echo $i; ?>]" value="<?php echo $survivalteam; ?>" style="width:50px"> SurvivalTeam</br>
                                                                                <input type="number" name="event_survivalrounds[<?php echo $i; ?>]" value="<?php echo $survivalrounds; ?>" style="width:50px"> SurvivalRounds</br>
                                                                                <input type="number" name="event_survivalwinner[<?php echo $i; ?>]" value="<?php echo $survivalWinner; ?>" style="width:50px"> SurvivalWinner</br>
                                                                                <input type="number" name="event_healthratio[<?php echo $i; ?>]" value="<?php echo $healthRatio; ?>" style="width:50px"> HealthRatio</br>
                                                                                <input type="number" name="event_healthratioteam[<?php echo $i; ?>]" value="<?php echo $healthRatioTeam; ?>" style="width:50px"> HealthRatioTeam</br>
                                                                                <input type="number" name="event_healthratiowinner[<?php echo $i; ?>]" value="<?php echo $healthRatioWinner; ?>" style="width:50px"> HealthRatioWinner</br>
                                                                            </td>
                                                                            <td align="left"><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('eventrounds', 2)">Runde hinzufügen</a><br />
                                                            <?php
                                                            }
                                                            else if ($name == 'placeandtime')
                                                            {
                                                            $pat = explode(';', $value);
                                                            $weekdays = explode(':', $pat[2]);
                                                            $monthdays = explode('-', $pat[3]);
                                                            $months = explode('-', $pat[4]);
                                                            $yeardays = explode('-', $pat[5]);
                                                            $years = explode('-', $pat[6]);
                                                            ?>
                                                            <b>Planet</b><br />
                                                            <select class="select" name="pat_planet" style="width:400px;">
                                                                <?php
                                                                    if ($planets == null)
                                                                    {
                                                                        $planets = new Generallist($database, 'planet', '*', '', '', 99999999999, 'ASC');
                                                                    }
                                                                    $id = 0;
                                                                    $entry = $planets->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($pat[0] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $planets->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                        if($pat[0] != 0)
                                                        {
                                                            ?>
                                                                <a href="?p=admin&a=see&table=planet&id=<?php echo $pat[0];?>"><button type="button">Bearbeiten</button></a>
                                                            <?php
                                                        }
                                                        ?>
                                                        <br />
                                                            <b>Place</b><br />
                                                            <select class="select" name="pat_place" style="width:400px;">
                                                                <?php
                                                                    if ($places == null)
                                                                    {
                                                                        $places = new Generallist($database, 'places', '*', '', '', 99999999999, 'ASC');
                                                                    }
                                                                    $id = 0;
                                                                    $entry = $places->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($pat[1] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $places->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                        if($pat[1] != 0)
                                                        {
                                                            ?>
                                                                <a href="?p=admin&a=see&table=places&id=<?php echo $pat[1];?>"><button type="button">Bearbeiten</button></a>
                                                            <?php
                                                        }
                                                       ?>
                                                        <br />
                                                            <b>Wochentag</b><br />
                                                            <?php
                                                            $i = 0;
                                                        while ($i != 7)
                                                        {
                                                            ++$i;
                                                            ?>
                                                                <div style="height:30px; width:30px; display: inline-block;">
                                                                    <?php echo date('D', mktime(20, 12, 0, 1, $i, 2018)); ?>
                                                                    <input type="checkbox" name="pat_weekday[]" value="<?php echo $i; ?>" <?php if (in_array($i, $weekdays)) echo 'checked'; ?>>
                                                                </div>
                                                            <?php
                                                            }
                                                            ?><br />
                                                            <b>Tage im Monat</b><br />
                                                            <select class="select" name="pat_monthday1" style="width:60px;">
                                                                <?php
                                                                    $i = 0;
                                                                    while ($i != 31)
                                                                    {
                                                                        ++$i;
                                                                        ?>
                                                                        <option value="<?php echo $i; ?>" <?php if ($i == $monthdays[0]) echo 'selected'; ?>><?php echo $i; ?></option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                            -
                                                            <select class="select" name="pat_monthday2" style="width:60px;">
                                                                <?php
                                                                    $i = 0;
                                                                    while ($i != 31)
                                                                    {
                                                                        ++$i;
                                                                        ?>
                                                                        <option value="<?php echo $i; ?>" <?php if ($i == $monthdays[1]) echo 'selected'; ?>><?php echo $i; ?></option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select><br />
                                                            <b>Monate</b><br />
                                                            <select class="select" name="pat_months1" style="width:60px;">
                                                                <?php
                                                                    $i = 0;
                                                                    while ($i != 12)
                                                                    {
                                                                        ++$i;
                                                                        ?>
                                                                        <option value="<?php echo $i; ?>" <?php if ($i == $months[0]) echo 'selected'; ?>><?php echo $i; ?></option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                            -
                                                            <select class="select" name="pat_months2" style="width:60px;">
                                                                <?php
                                                                    $i = 0;
                                                                    while ($i != 12)
                                                                    {
                                                                        ++$i;
                                                                        ?>
                                                                        <option value="<?php echo $i; ?>" <?php if ($i == $months[1]) echo 'selected'; ?>><?php echo $i; ?></option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select><br />
                                                            <b>Tage im Jahr</b><br />
                                                            <select class="select" name="pat_yeardays1" style="width:70px;">
                                                                <?php
                                                                    $i = 0;
                                                                    while ($i != 365)
                                                                    {
                                                                        ++$i;
                                                                        ?>
                                                                        <option value="<?php echo $i; ?>" <?php if ($i == $yeardays[0]) echo 'selected'; ?>><?php echo $i; ?></option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                            -
                                                            <select class="select" name="pat_yeardays2" style="width:70px;">
                                                                <?php
                                                                    $i = 0;
                                                                    while ($i != 365)
                                                                    {
                                                                        ++$i;
                                                                        ?>
                                                                        <option value="<?php echo $i; ?>" <?php if ($i == $yeardays[1]) echo 'selected'; ?>><?php echo $i; ?></option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select><br />
                                                            <b>Jahre</b><br />
                                                            <input type="text" name="pat_years1" value="<?php echo $years[0]; ?>" style="width:100px">
                                                            -
                                                            <input type="text" name="pat_years2" value="<?php echo $years[1]; ?>" style="width:100px">
                                                            <?php
                                                            }
                                                            else if ($name == 'race' && $table == 'attacks')
                                                            {
                                                            $races = explode(', ', $row[$name]);
                                                            ?>
                                                                <table>
                                                                    <tr>
                                                                        <td align="center">Pirat<br /><input type="checkbox" value="Pirat" name="race[]" <?php if (in_array('Pirat', $races)) echo 'checked'; ?>></td>
                                                                        <td align="center">Marine<br /><input type="checkbox" value="Marine" name="race[]" <?php if (in_array('Marine', $races)) echo 'checked'; ?>></td>
                                                                    </tr>
                                                                </table>
                                                            <?php
                                                            }
                                                            else if ($table == 'titel' && $name == 'color')
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="FF0000" <?php if ($row[$name] == 'FF0000') echo 'selected'; ?>>Team</option>
                                                                    <option value="DBA901" <?php if ($row[$name] == 'DBA901') echo 'selected'; ?>>Gelb</option>
                                                                    <option value="206694" <?php if ($row[$name] == '206694') echo 'selected'; ?>>Admin</option> <!-- 0066ff -->
                                                                    <option value="00C5A1" <?php if ($row[$name] == '00C5A1') echo 'selected'; ?>>Writer</option>
                                                                    <option value="088A08" <?php if ($row[$name] == '088A08') echo 'selected'; ?>>Artist</option>
                                                                    <option value="25E214" <?php if ($row[$name] == '25E214') echo 'selected'; ?>>Integrator</option>
                                                                    <option value="aa22ff" <?php if ($row[$name] == 'aa22ff') echo 'selected'; ?>>Moderator</option>
                                                                    <option value="3498db" <?php if ($row[$name] == '3498db') echo 'selected'; ?>>Director</option>
                                                                    <option value="B2B2B2" <?php if ($row[$name] == 'B2B2B2') echo 'selected'; ?>>Level 1</option>
                                                                    <option value="35AFD2" <?php if ($row[$name] == '35AFD2') echo 'selected'; ?>>Level 2</option>
                                                                    <option value="A437CC" <?php if ($row[$name] == 'A437CC') echo 'selected'; ?>>Level 3</option>
                                                                    <option value="FF00FF" <?php if ($row[$name] == 'FF00FF') echo 'selected'; ?>>Level 4</option>
                                                                    <option value="FFA500" <?php if ($row[$name] == 'FFA500') echo 'selected'; ?>>Halloween</option>
                                                                    <option value="FFD700" <?php if ($row[$name] == 'FFD700') echo 'selected'; ?>>Elo</option>
                                                                </select>
                                                            <?php
                                                            }
                                                            else if ($table == 'titel' && $name == 'star')
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="" <?php if ($row[$name] == '') echo 'selected'; ?>>Kein Stern</option>
                                                                    <option value="stern" <?php if ($row[$name] == 'stern') echo 'selected'; ?>>Gelber Stern</option>
                                                                    <option value="stern2" <?php if ($row[$name] == 'stern2') echo 'selected'; ?>>Roter Stern</option>
                                                                    <option value="stern3" <?php if ($row[$name] == 'stern3') echo 'selected'; ?>>Blauer Stern</option>
                                                                </select>
                                                            <?php
                                                            }
                                                            else if($name == 'clan' || $name == 'territorium')
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="0" <?php if ($row[$name] == "0") echo 'selected'; ?>>Kein Clan(0)</option>
                                                                    <?php
                                                                        $clanlist = new Generallist($database, 'clans', '*', '', '', 99999999999, 'ASC');
                                                                        $i = 0;
                                                                        $entry = $clanlist->GetEntry($i);
                                                                        while ($entry != null)
                                                                        {
                                                                            ?>
                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                            <?php
                                                                            ++$i;
                                                                            $entry = $clanlist->GetEntry($i);
                                                                        }
                                                                    ?>
                                                                </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                    <a href="?p=admin&a=see&table=clans&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                                }
                                                            }
                                                            else if ($table == 'treasurehunt' && ($name == 'island1' || $name == 'island2' || $name == 'island3'))
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="0" <?php if ($row[$name] == '') echo 'selected'; ?>>Keine Insel(0)</option>
                                                                    <?php

                                                                        if ($treasureislands == null)
                                                                        {
                                                                            $treasureislands = new Generallist($database, 'treasurehuntislands', '*', '', '', 99999999999, 'ASC');
                                                                        }
                                                                        $i = 0;
                                                                        $entry = $treasureislands->GetEntry($i);
                                                                        while ($entry != null)
                                                                        {
                                                                            ?>
                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                            <?php
                                                                            ++$i;
                                                                            $entry = $treasureislands->GetEntry($i);
                                                                        }
                                                                    ?>
                                                                </select>
                                                            <?php
                                                                if($row[$name] != 0)
                                                                {
                                                                ?>
                                                                    <a href="?p=admin&a=see&table=treasurehuntislands&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                                }
                                                            }
                                                            else if ($name == 'slot')
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Kein Slot</option>
                                                                    <option value="1" <?php if ($row[$name] == 1) echo 'selected'; ?>>Aura / Zusatz</option>
                                                                    <option value="2" <?php if ($row[$name] == 2) echo 'selected'; ?>>Handschuhe</option>
                                                                    <option value="3" <?php if ($row[$name] == 3) echo 'selected'; ?>>Hose</option>
                                                                    <option value="4" <?php if ($row[$name] == 4) echo 'selected'; ?>>Haki</option>
                                                                    <option value="5" <?php if ($row[$name] == 5) echo 'selected'; ?>>Hemd</option>
                                                                    <option value="6" <?php if ($row[$name] == 6) echo 'selected'; ?>>Waffe</option>
                                                                    <option value="7" <?php if ($row[$name] == 7) echo 'selected'; ?>>Schuhe</option>
                                                                    <option value="8" <?php if ($row[$name] == 8) echo 'selected'; ?>>Accessoire</option>
                                                                </select>
                                                            <?php
                                                            }
                                                            else if ($name == 'statstype' || $name == 'defaultstatstype')
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Alle</option>
                                                                    <option value="1" <?php if ($row[$name] == 1) echo 'selected'; ?>>Attack</option>
                                                                    <option value="2" <?php if ($row[$name] == 2) echo 'selected'; ?>>Abwehr</option>
                                                                    <option value="3" <?php if ($row[$name] == 3) echo 'selected'; ?>>LP</option>
                                                                    <option value="4" <?php if ($row[$name] == 4) echo 'selected'; ?>>AD</option>
                                                                    <option value="5" <?php if ($row[$name] == 5) echo 'selected'; ?>>Attack+Abwehr</option>
                                                                    <option value="6" <?php if ($row[$name] == 6) echo 'selected'; ?>>Attack+LP</option>
                                                                    <option value="7" <?php if ($row[$name] == 7) echo 'selected'; ?>>Attack+AD</option>
                                                                    <option value="8" <?php if ($row[$name] == 8) echo 'selected'; ?>>Abwehr+LP</option>
                                                                    <option value="9" <?php if ($row[$name] == 9) echo 'selected'; ?>>Abwehr+AD</option>
                                                                    <option value="10" <?php if ($row[$name] == 10) echo 'selected'; ?>>LP+AD</option>
                                                                    <option value="11" <?php if ($row[$name] == 11) echo 'selected'; ?>>Angriff+Abwehr+LP</option>
                                                                    <option value="12" <?php if ($row[$name] == 12) echo 'selected'; ?>>Angriff+Abwehr+AD</option>
                                                                    <option value="13" <?php if ($row[$name] == 13) echo 'selected'; ?>>Angriff+LP+AD</option>
                                                                    <option value="14" <?php if ($row[$name] == 14) echo 'selected'; ?>>Abwehr+LP+AD</option>
                                                                </select>
                                                            <?php
                                                            }
                                                            else if ($name == 'type' && $table == 'passives')
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>StatsChange</option>
                                                                    <option value="1" <?php if ($row[$name] == 1) echo 'selected'; ?>>Stun</option>
                                                                </select>
                                                            <?php
                                                            }
                                                            else if ($name == 'target' && $table == 'passives')
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Selbst</option>
                                                                    <option value="1" <?php if ($row[$name] == 1) echo 'selected'; ?>>Ziel</option>
                                                                </select>
                                                            <?php
                                                            }
                                                            else if ($name == 'operator' && $table == 'passives')
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Add</option>
                                                                    <option value="1" <?php if ($row[$name] == 1) echo 'selected'; ?>>Subtract</option>
                                                                </select>
                                                            <?php
                                                            }
                                                            else if ($name == 'type' && $table == 'patterns')
                                                            {
                                                            ?>
                                                                <select id="patterntype" class="select" name="<?php echo $name; ?>" style="width:400px;" onChange="loadPatternValueNames();">
                                                                    <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Fighter</option>
                                                                    <option value="1" <?php if ($row[$name] == 1) echo 'selected'; ?>>Kampf</option>
                                                                    <option value="2" <?php if ($row[$name] == 2) echo 'selected'; ?>>Enemy</option>
                                                                    <option value="3" <?php if ($row[$name] == 3) echo 'selected'; ?>>Team</option>
                                                                </select>
                                                            <?php
                                                            }
                                                            else if ($name == 'patterntarget' && $table == 'patterns')
                                                            {
                                                            ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Selbst</option>
                                                                    <option value="1" <?php if ($row[$name] == 1) echo 'selected'; ?>>Random Gegner</option>
                                                                    <option value="2" <?php if ($row[$name] == 2) echo 'selected'; ?>>Random Team</option>
                                                                    <option value="3" <?php if ($row[$name] == 3) echo 'selected'; ?>>Schwächster Team</option>
                                                                    <option value="4" <?php if ($row[$name] == 4) echo 'selected'; ?>>Schwächster Gegner</option>
                                                                    <option value="5" <?php if ($row[$name] == 5) echo 'selected'; ?>>Spezifischer NPC</option>
                                                                </select>
                                                            <?php
                                                            }
                                                            else if ($name == 'valuename' && $table == 'patterns')
                                                            {
                                                            ?>
                                                            <select id="patternvaluename" class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                            </select>

                                                            <script>
                                                                function addPatternValueOption(optionName, i) {
                                                                    var sel = document.getElementById("patterntype");
                                                                    var sel = document.getElementById("patternvaluename");
                                                                    var option = document.createElement("option");
                                                                    option.text = optionName;
                                                                    option.value = optionName;
                                                                    sel.add(option);

                                                                    if (optionName == '<?php echo $row[$name]; ?>')
                                                                        sel.value = optionName;

                                                                }

                                                                function loadPatternValueNames() {
                                                                    var sel = document.getElementById("patterntype");
                                                                    var selValue = sel.options[sel.selectedIndex].value;

                                                                    var nameSel = document.getElementById("patternvaluename");
                                                                    var L = nameSel.options.length - 1;
                                                                    for (var i = L; i >= 0; i--) {
                                                                        nameSel.remove(i);
                                                                    }
                                                                    if (selValue == 0 || selValue == 2 || selValue == 3) {
                                                                        addPatternValueOption('ki');
                                                                        addPatternValueOption('lp');
                                                                        addPatternValueOption('kp');
                                                                        addPatternValueOption('energy');
                                                                        addPatternValueOption('race');
                                                                        addPatternValueOption('taunt');
                                                                        addPatternValueOption('reflect');
                                                                        addPatternValueOption('loadattack');
                                                                        addPatternValueOption('loadrounds');
                                                                        addPatternValueOption('isnpc');
                                                                    } else if (selValue == 1) {
                                                                        addPatternValueOption('round');
                                                                        addPatternValueOption('healthratio');
                                                                        addPatternValueOption('place');
                                                                        addPatternValueOption('planet');
                                                                        addPatternValueOption('mode');
                                                                        addPatternValueOption('story');
                                                                        addPatternValueOption('sidestory');
                                                                        addPatternValueOption('type');
                                                                    }
                                                                }

                                                                loadPatternValueNames();
                                                            </script>
                                                        <?php
                                                            }
                                                            else if ($name == 'valuename' && $table == 'passives')
                                                            {
                                                        ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                <option value="lp" <?php if ($row[$name] == 'lp') echo 'selected'; ?>>LP</option>
                                                                <option value="kp" <?php if ($row[$name] == 'kp') echo 'selected'; ?>>AD</option>
                                                                <option value="energy" <?php if ($row[$name] == 'energy') echo 'selected'; ?>>EP</option>
                                                                <option value="attack" <?php if ($row[$name] == 'attack') echo 'selected'; ?>>Angriff</option>
                                                                <option value="defense" <?php if ($row[$name] == 'defense') echo 'selected'; ?>>Verteidigung</option>
                                                                <option value="accuracy" <?php if ($row[$name] == 'accuracy') echo 'selected'; ?>>Genauigkeit</option>
                                                                <option value="reflex" <?php if ($row[$name] == 'reflex') echo 'selected'; ?>>Reflex</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'operator' && $table == 'patterns')
                                                        {
                                                            ?>
                                                            <select class="select" name="operator" style="width:400px;">
                                                                <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Weniger Als</option>
                                                                <option value="1" <?php if ($row[$name] == 1) echo 'selected'; ?>>Weniger Gleich</option>
                                                                <option value="2" <?php if ($row[$name] == 2) echo 'selected'; ?>>Gleich</option>
                                                                <option value="3" <?php if ($row[$name] == 3) echo 'selected'; ?>>Nicht Gleich</option>
                                                                <option value="4" <?php if ($row[$name] == 4) echo 'selected'; ?>>Mehr Als</option>
                                                                <option value="5" <?php if ($row[$name] == 5) echo 'selected'; ?>>Mehr Gleich</option>
                                                                <option value="6" <?php if ($row[$name] == 6) echo 'selected'; ?>>Modulo</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'race')
                                                        {
                                                            ?>
                                                            <select class="select" name="race" style="width:400px;">
                                                                <option value="" <?php if ($row[$name] == '' || !isset($_GET['id'])) echo 'selected'; ?>>Keine</option>
                                                                <option value="Pirat" <?php if ($row[$name] == 'Pirat' && isset($_GET['id'])) echo 'selected'; ?>>Pirat</option>
                                                                <option value="Marine" <?php if ($row[$name] == 'Marine' && isset($_GET['id'])) echo 'selected'; ?>>Marine</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'typesort' && $table == 'titel')
                                                        {
                                                            ?>
                                                            <select class="select" name="typesort" style="width:400px;">
                                                                <option value="0" <?php if ($row[$name] == '0') echo 'selected'; ?>>Siege</option>
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>Niederlagen</option>
                                                                <option value="2" <?php if ($row[$name] == '2') echo 'selected'; ?>>Unentschieden</option>
                                                                <option value="3" <?php if ($row[$name] == '3') echo 'selected'; ?>>Total</option>
                                                                <option value="4" <?php if ($row[$name] == '4') echo 'selected'; ?>>Tägliche Siege</option>
                                                                <option value="5" <?php if ($row[$name] == '5') echo 'selected'; ?>>Tägliche Niederlagen</option>
                                                                <option value="6" <?php if ($row[$name] == '6') echo 'selected'; ?>>Tägliche Unentschieden</option>
                                                                <option value="7" <?php if ($row[$name] == '7') echo 'selected'; ?>>Täglich Total</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'typefight' && $table == 'titel')
                                                        {
                                                            ?>
                                                            <select class="select" name="typefight" style="width:400px;">
                                                                <option value="-1" <?php if ($row[$name] == -1) echo 'selected'; ?>>Alle</option>
                                                                <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Spaß</option>
                                                                <option value="1" <?php if ($row[$name] == 1) echo 'selected'; ?>>Kopfgeld</option>
                                                                <option value="2" <?php if ($row[$name] == 2) echo 'selected'; ?>>Tod</option>
                                                                <option value="3" <?php if ($row[$name] == 3) echo 'selected'; ?>>NPC</option>
                                                                <option value="4" <?php if ($row[$name] == 4) echo 'selected'; ?>>Story/SideStory</option>
                                                                <option value="5" <?php if ($row[$name] == 5) echo 'selected'; ?>>Event</option>
                                                                <option value="6" <?php if ($row[$name] == 6) echo 'selected'; ?>>Tournament</option>
                                                                <option value="8" <?php if ($row[$name] == 8) echo 'selected'; ?>>Kolosseum</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'type' && $table == 'titel')
                                                        {
                                                            ?>
                                                            <select class="select" name="type" style="width:400px;">
                                                                <option value="0" <?php if ($row[$name] == '0') echo 'selected'; ?>>Spezial</option>
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>NPC</option>
                                                                <option value="2" <?php if ($row[$name] == '2') echo 'selected'; ?>>Story/SideStory</option>
                                                                <option value="3" <?php if ($row[$name] == '3') echo 'selected'; ?>>Aktion</option>
                                                                <option value="4" <?php if ($row[$name] == '4') echo 'selected'; ?>>Wunsch</option>
                                                                <option value="5" <?php if ($row[$name] == '5') echo 'selected'; ?>>Rang</option>
                                                                <option value="6" <?php if ($row[$name] == '6') echo 'selected'; ?>>Kampf</option>
                                                                <option value="7" <?php if ($row[$name] == '7') echo 'selected'; ?>>Attacken</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'type' && ($table == 'story' || $table == 'sidestory'))
                                                        {
                                                            ?>
                                                            <select class="select" name="type" style="width:400px;">
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>Reden</option>
                                                                <option value="2" <?php if ($row[$name] == '2') echo 'selected'; ?>>Kampf</option>
                                                                <option value="3" <?php if ($row[$name] == '3') echo 'selected'; ?>>Aktion</option>
                                                                <option value="4" <?php if ($row[$name] == '4') echo 'selected'; ?>>Quiz</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'type' && $table == 'actions')
                                                        {
                                                            ?>
                                                            <select class="select" name="type" style="width:400px;">
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>Statstraining</option>
                                                                <option value="2" <?php if ($row[$name] == '2') echo 'selected'; ?>>Wiederbelebung</option>
                                                                <option value="3" <?php if ($row[$name] == '3') echo 'selected'; ?>>Heilung</option>
                                                                <option value="4" <?php if ($row[$name] == '4') echo 'selected'; ?>>Reise</option>
                                                                <option value="5" <?php if ($row[$name] == '5') echo 'selected'; ?>>Lernen</option>
                                                                <option value="6" <?php if ($row[$name] == '6') echo 'selected'; ?>>Sonstiges</option>
                                                                <option value="7" <?php if ($row[$name] == '7') echo 'selected'; ?>>ItemGewinn</option>
                                                                <option value="8" <?php if ($row[$name] == '8') echo 'selected'; ?>>Schatzsuche</option>
                                                                <option value="15" <?php if ($row[$name] == '15') echo 'selected'; ?>>Spezial Training</option>

                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'type' && $table == 'attacks')
                                                        {
                                                            ?>
                                                            <select class="select" name="type" style="width:400px;">
                                                                <?php
                                                                    for ($i = 1; $i <= Attack::GetTypeCount(); ++$i)
                                                                    {
                                                                        ?> <option value="<?php echo $i; ?>" <?php if ($row[$name] == $i) echo 'selected'; ?>><?php echo Attack::GetTypeName($i); ?></option><?php
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                    <a href="?p=admin&a=see&table=attacks&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                            }
                                                        }
                                                        else if ($name == 'category' && $table == 'items')
                                                        {
                                                            ?>
                                                            <select class="select" name="category" style="width:400px;">
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>Medizin</option>
                                                                <option value="2" <?php if ($row[$name] == '2') echo 'selected'; ?>>Rüstung</option>
                                                                <option value="3" <?php if ($row[$name] == '3') echo 'selected'; ?>>Waffen</option>
                                                                <option value="5" <?php if ($row[$name] == '5') echo 'selected'; ?>>Skillitems</option>
                                                                <option value="4" <?php if ($row[$name] == '4') echo 'selected'; ?>>Sonstiges</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'type' && $table == 'items')
                                                        {
                                                            ?>
                                                            <select class="select" name="type" style="width:400px;">
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>Heilung</option>
                                                                <option value="2" <?php if ($row[$name] == '2') echo 'selected'; ?>>Heilung Prozentual</option>
                                                                <option value="3" <?php if ($row[$name] == '3') echo 'selected'; ?>>Ausrüstung</option>
                                                                <option value="4" <?php if ($row[$name] == '4') echo 'selected'; ?>>ReiseBonus</option>
                                                                <option value="5" <?php if ($row[$name] == '5') echo 'selected'; ?>>Besondere Items</option>
                                                                <option value="6" <?php if ($row[$name] == '6') echo 'selected'; ?>>Besondere Consumables</option>
                                                                <option value="7" <?php if ($row[$name] == '7') echo 'selected'; ?>>Schätze</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'overlay' && $table == 'items')
                                                        {
                                                            ?>
                                                            <select class="select" name="overlay" style="width:400px;">
                                                                <option value="0" <?php if ($row[$name] == '0') echo 'selected'; ?>>Kein</option>
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>Event</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'overrideattacks')
                                                        {
                                                            ?>
                                                            <select class="select" name="overrideattacks" style="width:400px;">
                                                                <option value="0" <?php if ($row[$name] == '0') echo 'selected'; ?>>Nicht überschreiben</option>
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>Alles überschreiben </option>
                                                                <option value="2" <?php if ($row[$name] == '2') echo 'selected'; ?>>Alles außer Verwandlungen</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'typeaction' || $name == 'action' || $name == 'travelaction')
                                                        {

                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                <option value="0" <?php if ($row[$name] == '') echo 'selected'; ?>>Keine Aktion(0)</option>
                                                                <?php

                                                                    if ($actions == null)
                                                                    {
                                                                        $actions = new Generallist($database, 'actions', '*', '', '', 99999999999, 'ASC');
                                                                    }
                                                                    $id = 0;
                                                                    $entry = $actions->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $actions->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                <a href="?p=admin&a=see&table=actions&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                            }
                                                        }
                                                        else if ($name == 'planet')
                                                        {

                                                            ?>
                                                            <select class="select" name="planet" style="width:400px;">
                                                                <option value="" <?php if ($row[$name] == 0) echo 'selected'; ?>>No Planet(0)</option>
                                                                <?php

                                                                    if ($planets == null)
                                                                    {
                                                                        $planets = new Generallist($database, 'planet', '*', '', '', 99999999999, 'ASC');
                                                                    }
                                                                    $id = 0;
                                                                    $entry = $planets->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $planets->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                <a href="?p=admin&a=see&table=planet&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                            }
                                                        }
                                                        else if($name == 'type' && $table == 'skilltree')
                                                        {
                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                <option value="None" <?php if ($row[$name] == 'None') echo 'selected'; ?>>Kein Pfad (None)</option>
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>Zoan</option>
                                                                <option value="2" <?php if ($row[$name] == '2') echo 'selected'; ?>>Paramecia</option>
                                                                <option value="3" <?php if ($row[$name] == '3') echo 'selected'; ?>>Logia</option>
                                                                <option value="4" <?php if ($row[$name] == '4') echo 'selected'; ?>>Schwertkämpfer</option>
                                                                <option value="5" <?php if ($row[$name] == '5') echo 'selected'; ?>>Schwarzfuß</option>
                                                                <option value="6" <?php if ($row[$name] == '6') echo 'selected'; ?>>Karatekämpfer</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if($name == 'type2' && $table == 'skilltree')
                                                        {
                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                <option value="None" <?php if ($row[$name] == 'None') echo 'selected'; ?>>Kein Extrapfad (None)</option>
                                                                <option value="1" <?php if ($row[$name] == '1') echo 'selected'; ?>>Operations-Frucht | Fisch-Frucht | Donner-Frucht</option>
                                                                <option value="2" <?php if ($row[$name] == '2') echo 'selected'; ?>>Faden-Frucht | Vogel-Frucht | Feuer-Frucht</option>
                                                                <option value="3" <?php if ($row[$name] == '3') echo 'selected'; ?>>Mochi-Frucht | Mensch-Mensch-Frucht | Gefrier-Frucht</option>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'pfad' || $name == 'pfad2')
                                                        {
                                                            if ($name == 'pfad')
                                                            {
                                                                ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="None" <?php if ($row[$name] == 'None') echo 'selected'; ?>>Kein Pfad (None)</option>
                                                                    <option value="Zoan" <?php if ($row[$name] == 'Zoan') echo 'selected'; ?>>Zoan</option>
                                                                    <option value="Paramecia" <?php if ($row[$name] == 'Paramecia') echo 'selected'; ?>>Paramecia</option>
                                                                    <option value="Logia" <?php if ($row[$name] == 'Logia') echo 'selected'; ?>>Logia</option>
                                                                </select>
                                                                <?php
                                                            }
                                                            else
                                                            {
                                                                ?>
                                                                <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                    <option value="None" <?php if ($row[$name] == 'None') echo 'selected'; ?>>Kein Pfad (None)</option>
                                                                    <option value="Schwertkaempfer" <?php if ($row[$name] == 'Schwertkaempfer') echo 'selected'; ?>>Schwertkaempfer</option>
                                                                    <option value="Schwarzfuss" <?php if ($row[$name] == 'Schwarzfuss') echo 'selected'; ?>>Schwarzfuss</option>
                                                                    <option value="Karatekämpfer" <?php if ($row[$name] == 'Karatekämpfer') echo 'selected'; ?>>Karatekämpfer</option>
                                                                </select>
                                                                <?php
                                                            }
                                                        }
                                                        else if ($name == 'place' || $name == 'startingplace')
                                                        {

                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                <option value="-1" <?php if ($row[$name] == -1) echo 'selected'; ?>>Auf Reise(-1)</option>
                                                                <option value="" <?php if ($row[$name] == 0) echo 'selected'; ?>>Kein Ort(0)</option>
                                                                <?php

                                                                    if ($places == null)
                                                                    {
                                                                        $places = new Generallist($database, 'places', '*', '', '', 99999999999, 'ASC');
                                                                    }
                                                                    $id = 0;
                                                                    $entry = $places->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $places->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                <a href="?p=admin&a=see&table=places&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                            }
                                                        }
                                                        else if ($name == 'typeattack' || $name == 'loadattack' || $name == 'blockattack' || $name == 'blockedattack' || $name == 'fallbackattack' || $name == 'attack' && $table != 'fighters' && $table != 'actions' && $table != 'accounts' && $table != 'npcs' && $table != 'items' || $name == 'needattack')
                                                        {
                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Kein Angriff(0)</option>
                                                                <?php


                                                                    $list = new Generallist($database, 'attacks', '*', '', '', 99999999999, 'ASC');
                                                                    $id = 0;
                                                                    $entry = $list->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $list->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                <a href="?p=admin&a=see&table=attacks&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                            }
                                                        }
                                                        else if ($name == 'spcialtrainuses')
                                                        {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="<?php echo $name; ?>">
                                                                <tr>
                                                                    <td><b>Actions</b></td>
                                                                </tr>
                                                                <?php
                                                                    $specialActions = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($specialActions))
                                                                    {
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select" name="<?php echo $name; ?>[<?php echo $i; ?>]" style="width:400px;">
                                                                                    <option value="0" <?php if ($specialActions[$i] == '0') echo 'selected'; ?>>keine Aktion(0)</option>
                                                                                    <?php

                                                                                        if ($specialActionsList == null)
                                                                                        {
                                                                                            $specialActionsList = new Generallist($database, 'actions', '*', 'type=15', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $specialActionsList->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($specialActions[$i] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $specialActionsList->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <?php
                                                                                if($specialActions[$i] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=actions&id=<?php echo $specialActions[$i];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('<?php echo $name; ?>', 1)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                        }
                                                        else if ($name == 'addattacks' ||$name == 'removeattacks' || $name == 'attacks' || $name == 'fightattacks' || $name == 'learnableattacks' || $name == 'needattacks' || $name == 'playerattack')
                                                        {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="<?php echo $name; ?>">
                                                                <tr>
                                                                    <td><b>Attacks</b></td>
                                                                </tr>
                                                                <?php
                                                                    $needattacks = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($needattacks))
                                                                    {
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select" name="<?php echo $name; ?>[<?php echo $i; ?>]" style="width:400px;">
                                                                                    <option value="0" <?php if ($needattacks[$i] == '0') echo 'selected'; ?>>keine Attacke(0)</option>
                                                                                    <?php

                                                                                        if ($attacks == null)
                                                                                        {
                                                                                            $attacks = new Generallist($database, 'attacks', '*', '', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $attacks->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($needattacks[$i] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $attacks->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <?php
                                                                                if($needattacks[$i] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=attacks&id=<?php echo $needattacks[$i];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('<?php echo $name; ?>', 1)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                        }
                                                        else if ($name == 'needitems')
                                                        {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="<?php echo $name; ?>">
                                                                <tr>
                                                                    <td><b>Items</b></td>
                                                                </tr>
                                                                <?php
                                                                    $needitems = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($needitems))
                                                                    {
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select" name="56<?php echo $name; ?>[<?php echo $i; ?>]" style="width:400px;">
                                                                                    <option value="0" <?php if ($needitems[$i] == '0') echo 'selected'; ?>>keine Items (0)</option>
                                                                                    <?php

                                                                                        if ($items == null)
                                                                                        {
                                                                                            $items = new Generallist($database, 'items', '*', '', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $items->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($needitems[$i] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $items->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <?php
                                                                                if($needitems[$i] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=items&id=<?php echo $needitems[$i];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('<?php echo $name; ?>', 1)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                        }
                                                        else if ($name == 'titel' && $table == 'titelprogress')
                                                        {
                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:300px;">
                                                                <?php

                                                                    if ($titel == null)
                                                                    {
                                                                        $titel = new Generallist($database, 'titel', '*', '', '', 99999999999, 'ASC');
                                                                    }
                                                                    $id = 0;
                                                                    $entry = $titel->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $titel->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                <a href="?p=admin&a=see&table=titel&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                            }
                                                        }
                                                        else if ($name == 'npcid' || $name == 'npc' || $name == 'talknpc' || $name == 'supportnpc' || $name == 'typenpc')
                                                        {
                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                <option value="0" <?php if ($row[$name] == 0) echo 'selected'; ?>>Kein NPC(0)</option>
                                                                <?php

                                                                    if ($npcs == null)
                                                                    {
                                                                        $npcs = new Generallist($database, 'npcs', '*', '', '', 99999999999, 'ASC');
                                                                    }
                                                                    $id = 0;
                                                                    $entry = $npcs->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $npcs->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                <a href="?p=admin&a=see&table=npcs&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                            }
                                                        }
                                                        else if (($name == 'userid' || $name == 'sitter') && ($table == 'accounts' || $table == 'multichars'))
                                                        {
                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                <option value="0" selected>Niemand</option>
                                                                <?php
                                                                    $spieler = new Generallist($accountDB, 'users', '*', '', '', 99999999999, 'ASC');
                                                                    $id = 0;
                                                                    $entry = $spieler->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['id'] . ' (' . $entry['login'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $spieler->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if(!isset($_GET['id']) && $name == 'author' && $table == 'News')
                                                        {
                                                            ?>
                                                                <input type="number" name="<?php echo $name; ?>" value="<?php echo $player->GetID(); ?>" style="width:400px">
                                                            <?php
                                                        }
                                                        else if(!isset($_GET['id']) && $name == 'authorname' && $table == 'News')
                                                        {
                                                            ?>
                                                                <input type="text" name="<?php echo $name; ?>" value="<?php echo $player->GetName(); ?>" style="width:400px">
                                                            <?php
                                                        }
                                                        else if(!isset($_GET['id']) && $name == 'authorimage' && $table == 'News')
                                                        {
                                                            ?>
                                                                <input type="text" name="<?php echo $name; ?>" value="<?php echo $player->GetImage(); ?>" style="width:400px">
                                                            <?php
                                                        }
                                                        else if ($name == 'acc' || $name == 'spielerid' || $name == 'ownerid' || $name == 'userid' || $name == 'id' && $table == 'accounts' || $name == 'userid')
                                                        {
                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">

                                                                <?php
                                                                    $spieler = new Generallist($database, 'accounts', '*', '', '', 99999999999, 'ASC');
                                                                    $id = 0;
                                                                    $entry = $spieler->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $spieler->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                        }
                                                        else if ($name == 'titelid')
                                                        {
                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">

                                                                <?php
                                                                    $titel = new Generallist($database, 'titel', '*', '', '', 99999999999, 'ASC');
                                                                    $id = 0;
                                                                    $entry = $titel->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $titel->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                <a href="?p=admin&a=see&table=titel&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                            }
                                                        }
                                                        else if ($table == 'events' && $name == 'item')
                                                        {
                                                            ?>
                                                            <table width="90%" cellspacing="0" id="eventitems">
                                                                <tr>
                                                                    <td><b>Items</b></td>
                                                                </tr>
                                                                <?php
                                                                    $eventItems = explode(';', $value);
                                                                    $i = 0;
                                                                    while ($value != '' && $i != count($eventItems))
                                                                    {
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select" name="event_items[<?php echo $i; ?>]" style="width:400px;">
                                                                                    <option value="0" <?php if ($eventItems[$i] == '0') echo 'selected'; ?>>Kein Item(0)</option>
                                                                                    <?php

                                                                                        if ($items == null)
                                                                                        {
                                                                                            $items = new Generallist($database, 'items', '*', '', '', 99999999999, 'ASC');
                                                                                        }
                                                                                        $id = 0;
                                                                                        $entry = $items->GetEntry($id);
                                                                                        while ($entry != null)
                                                                                        {
                                                                                            ?>
                                                                                            <option value="<?php echo $entry['id']; ?>" <?php if ($eventItems[$i] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                            <?php
                                                                                            ++$id;
                                                                                            $entry = $items->GetEntry($id);
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <?php
                                                                                if($eventItems[$i] != 0)
                                                                                {
                                                                                    ?>
                                                                                    <td>
                                                                                        <a href="?p=admin&a=see&table=items&id=<?php echo $eventItems[$i];?>"><button type="button">Bearbeiten</button></a>
                                                                                    </td>
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                            <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                        </tr>
                                                                        <?php
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('eventitems', 1)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                        }
                                                        else if ($name == 'itemid' || $name == 'item' || $name == 'statsid' || $name == 'visualid' || $name == 'earnitem' || $name == 'upgradeid' || $name == 'needitem')
                                                        {

                                                            ?>
                                                            <select class="select" name="<?php echo $name; ?>" style="width:400px;">
                                                                <option value="0" <?php if ($row[$name] == '0') echo 'selected'; ?>>Kein Item(0)</option>
                                                                <?php

                                                                    if ($items == null)
                                                                    {
                                                                        $items = new Generallist($database, 'items', '*', '', '', 99999999999, 'ASC');
                                                                    }
                                                                    $id = 0;
                                                                    $entry = $items->GetEntry($id);
                                                                    while ($entry != null)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $entry['id']; ?>" <?php if ($row[$name] == $entry['id']) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                        <?php
                                                                        ++$id;
                                                                        $entry = $items->GetEntry($id);
                                                                    }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if($row[$name] != 0)
                                                            {
                                                                ?>
                                                                <a href="?p=admin&a=see&table=items&id=<?php echo $row[$name];?>"><button type="button">Bearbeiten</button></a>
                                                                <?php
                                                            }
                                                        }
                                                        else if ($name == 'items' && $table == 'places')
                                                        {
                                                            if ($items == null)
                                                            {
                                                                $items = new Generallist($database, 'items', '*', '', 'category, lv', 99999999999, 'ASC');
                                                            }
                                                            $values = explode(';', $row[$name]);
                                                            $id = 0;
                                                            $entry = $items->GetEntry($id);
                                                            while ($entry != null)
                                                            {
                                                                ?>
                                                                <div class="tooltip" style="position:relative; height:60px; width:40px; display:inline-block">
                                                                    <label for="item<?php echo $entry['id']; ?>" style="cursor: pointer;">
                                                                        <div style="width:40px; height:40px;">
                                                                            <?php if ($entry['overlay'] == 1)
                                                                            {
                                                                                ?>
                                                                                <img width="40px" height="40px" src="img/items/OverlayEvent.png" class="attack" style="position:absolute; z-index:1;">
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                            <img width="40px" height="40px" src="img/items/<?php echo $entry['image']; ?>.png" class="attack" style="z-index:0;">
                                                                        </div>
                                                                    </label>
                                                                    <input type="checkbox" name="<?php echo $name; ?>[]" id="item<?php echo $entry['id']; ?>" value="<?php echo $entry['id']; ?>" <?php if (in_array($entry['id'], $values)) echo 'checked'; ?>>
                                                                    <span class="tooltiptext" style="left:-50px; top:-35px;"><?php echo $entry['name']; ?></span>
                                                                </div>
                                                                <?php
                                                                ++$id;
                                                                $entry = $items->GetEntry($id);
                                                            }
                                                        }
                                                        else if ($name == 'actions')
                                                        {
                                                            if ($actions == null)
                                                            {
                                                                $actions = new Generallist($database, 'actions', '*', '', '', 99999999999, 'ASC');
                                                            }
                                                            $values = explode(';', $row[$name]);
                                                            $id = 0;
                                                            $entry = $actions->GetEntry($id);
                                                            while ($entry != null)
                                                            {
                                                                ?>
                                                                <div class="tooltip" style="position:relative; height:60px; width:40px; display:inline-block">
                                                                    <label for="action<?php echo $entry['id']; ?>" style="cursor: pointer;">
                                                                        <img width="40px" height="40px" src="img/actions/<?php echo $entry['image']; ?>.png" class="attack">
                                                                    </label>
                                                                    <input type="checkbox" name="<?php echo $name; ?>[]" id="action<?php echo $entry['id']; ?>" value="<?php echo $entry['id']; ?>" <?php if (in_array($entry['id'], $values)) echo 'checked'; ?>>
                                                                    <span class="tooltiptext" style="left:-50px; top:-40px;"><?php echo $entry['name']; ?></span>
                                                                </div>
                                                                <?php
                                                                ++$id;
                                                                $entry = $actions->GetEntry($id);
                                                            }
                                                        }
                                                        else if ($name == 'supportnpcs' || $name == 'npcs' || $name == 'trainers')
                                                        {
                                                            ?>
                                                            <table width="100%" id="<?php echo $name; ?>">
                                                                <tr></tr>
                                                                <?php
                                                                    $tableNPCs = explode(';', $row[$name]);
                                                                    $i = 0;
                                                                    while ($tableNPCs != null && isset($tableNPCs[$i]))
                                                                    {
                                                                        $tableNPC = $tableNPCs[$i];
                                                                        if ($tableNPC != null)
                                                                        {
                                                                            ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <select class="select" name="<?php echo $name; ?>[]" style="width:400px;">
                                                                                        <?php
                                                                                            if ($npcs == null)
                                                                                            {
                                                                                                $npcs = new Generallist($database, 'npcs', '*', '', '', 99999999999, 'ASC');
                                                                                            }
                                                                                            $id = 0;
                                                                                            $entry = $npcs->GetEntry($id);
                                                                                            while ($entry != null)
                                                                                            {
                                                                                                ?>
                                                                                                <option value="<?php echo $entry['id']; ?>" <?php if ($entry['id'] == $tableNPC) echo 'selected'; ?>><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                                                                                                <?php
                                                                                                ++$id;
                                                                                                $entry = $npcs->GetEntry($id);
                                                                                            }
                                                                                        ?>
                                                                                    </select>
                                                                                </td>
                                                                                <?php
                                                                                    if($tableNPC != 0)
                                                                                    {
                                                                                        ?>
                                                                                        <td>
                                                                                            <a href="?p=admin&a=see&table=npcs&id=<?php echo $tableNPC;?>"><button type="button">Bearbeiten</button></a>
                                                                                        </td>
                                                                                        <?php
                                                                                    }
                                                                                ?>
                                                                                <td><a onclick="RemoveTableRow(this)" style="cursor: pointer;">X</a></td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        ++$i;
                                                                    }
                                                                ?>
                                                            </table>
                                                            <br />
                                                            <a onclick="AddTableRow('<?php echo $name; ?>', 1)" style="cursor: pointer;">Eintrag hinzufügen</a><br />
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            switch ($type)
                                                            {
                                                            case 1: //tinyint
                                                                ?>
                                                                    <input type="checkbox" name="<?php echo $name; ?>" <?php if ($value == 1 || $name == 'marktplatz') echo 'checked'; ?>>
                                                                <?php
                                                                break;
                                                            case 3: //int
                                                                if(!isset($_GET['id']))
                                                                    $value = '';
                                                                ?>
                                                                    <input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" style="width:400px">
                                                                <?php
                                                                break;
                                                            case 4: //float
                                                                ?>
                                                                    <input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" step="0.01" style="width:400px">
                                                                <?php
                                                                break;
                                                            case 5: //Double
                                                                ?>
                                                                    <input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" step="0.01" style="width:400px">
                                                                <?php
                                                                break;
                                                            case 8: //bigint
                                                                if(!isset($_GET['id']))
                                                                    $value = '';
                                                                ?>
                                                                    <input type="number" name="<?php echo $name; ?>" value="<?php echo $value; ?>" style="width:400px">
                                                                <?php
                                                                break;
                                                            case 253: //varchar
                                                                ?>
                                                                    <input type="text" name="<?php echo $name; ?>" value="<?php echo $value; ?>" style="width:400px">
                                                                <?php
                                                                break;
                                                            case 252: //longtext
                                                                ?>
                                                                    <textarea name="<?php echo $name; ?>" style="width:440px; height:100px; resize: vertical;"><?php if(isset($_GET['id'])) echo $value; ?></textarea>
                                                                <?php
                                                                break;
                                                            case 12: //Datum
                                                                $value2 = $value;
                                                                if ($value == '') $value2 = date('d.m.Y H:i');
                                                                ?>
                                                                    <input type="datetime-local" name="<?php echo $name; ?>" value="<?php echo date('Y-m-d\TH:i', strtotime($value2)); ?>" style="width:400px">
                                                                <?php
                                                                break;
                                                            case 10: //Datum
                                                                ?>
                                                                    <input type="date" name="<?php echo $name; ?>" value="<?php echo $value; ?>" style="width:400px">
                                                                <?php
                                                                break;
                                                            }
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                </table>
                <input type="submit" value="ändern">
            </form>
            <?php
            $result->Close();
        }
        else
        {
            $table = $_GET['table'];
            ?>
            <div class="spacer"></div>
            <hr>
            <h2>Bearbeiten</h2>
            Wenn du das ID Feld leer lässt, dann wird mit den Werten ein neuer Eintrag erstellt.<br />
            <div class="spacer"></div>
            <form method="GET" action="?p=admin">
                <input type="hidden" name="p" value="admin">
                <input type="hidden" name="a" value="see">
                <input type="hidden" name="table" value="<?php echo $table; ?>">
                <select class="select" name="id">
                    <?php
                        if($table == 'inventory')
                        {
                            if(isset($_GET['pid']) && is_numeric($_GET['pid']))
                            {
                                $result = $database->Select('*', $table, 'ownerid ='.$_GET['pid'], 999999, 'ownerid', 'ASC');
                            }
                            else
                            {
                                $result = $database->Select('*', $table, '', 999999, 'ownerid', 'ASC');
                            }
                        }

                        else
                            $result = $database->Select('*', $table, '', 999999, 'id', 'ASC');
                        if ($result)
                        {
                            if ($result->num_rows > 0)
                            {
                                while ($row = $result->fetch_assoc())
                                {
                                    $pid = (isset($row['id'])) ? $row['id'] : $row['ID'];
                                    ?>
                                    <option value="<?php echo $pid; ?>">
                                        <?php
                                            $id = (isset($row['spielerid'])) ? $row['spielerid'] : 0;
                                            if ($id != 0)
                                            {
                                                $spieler = new Generallist($database, 'accounts', 'name, id', 'id = ' . $id . '', 1);
                                                $spielerid = $spieler->GetEntry(0);
                                            }
                                            else
                                                $spielerid = null;

                                            $id = (isset($row['userid'])) ? $row['userid'] : 0;
                                            if ($id != 0)
                                            {
                                                $users = new Generallist($database, 'accounts', 'name, id', 'id = ' . $id . '', 1);
                                                $userid = $users->GetEntry(0);
                                            }
                                            else
                                                $userid = null;

                                            $id = (isset($row['ownerid'])) ? $row['ownerid'] : 0;
                                            if ($id != 0)
                                            {
                                                $users = new Generallist($database, 'accounts', 'name, id', 'id = ' . $id . '', 1);
                                                $ownerid = $users->GetEntry(0);
                                            }
                                            else
                                                $ownerid = null;

                                            $id = (isset($row['attack'])) ? $row['attack'] : 0;
                                            if ($id != 0)
                                            {
                                                $attacks = new Generallist($database, 'attacks', 'name, id', 'id = ' . $id . '', 1);
                                                $attack = $attacks->GetEntry(0);
                                            }
                                            else
                                                $attack = null;

                                            $id = (isset($row['statsid'])) ? $row['statsid'] : 0;
                                            if ($id != 0)
                                            {
                                                $items = new Generallist($database, 'items', 'name, id', 'id = ' . $id . '' , 1);
                                                $item = $items->GetEntry(0);
                                            }
                                            else
                                                $item = null;

                                            if (isset($row['used']))
                                            {
                                                if ($row['used'] == 0)
                                                {
                                                    $genutzt = "Benutzt: Nein";
                                                }
                                                else
                                                {
                                                    $genutzt = "Benutzt: Ja";
                                                }
                                            }
                                            if (isset($row['name'])) echo $row['name'];
                                            else if (isset($row['title'])) echo $row['title'];
                                            else if (isset($row['stars'])) echo 'Stars: ' . $row['stars'];
                                            else if (isset($row['promocode'])) echo $row['promocode'];
                                            else if (isset($row['titel'])) echo $row['titel'];
                                            else if (isset($row['topic'])) echo $row['topic'];
                                            else if (isset($row['seller'])) echo $item['name'] . ' x' . $row['amount'] . ' | ' . $row['seller'] . ' (' . $row['sellerid'] . ')';
                                            else if (isset($row['Betreff'])) echo $row['Betreff'];
                                            else if (isset($row['spielerid'])) echo $row['spielerid'] . ' ' . $spielerid['name'];
                                            else if (isset($row['comment'])) echo $row['comment'];
                                            else if (isset($row['userid'])) echo $row['userid'] . ' ' . $userid['name'];
                                            else if (isset($row['attack'])) echo $row['attack'] . ' ' . $attack['name'];
                                            else if (isset($row['ownerid'])) echo $ownerid['name'].' '.$item['name'].' (' . $item['id'] . ') Anzahl x'.$row['amount'];
                                            if (isset($row['col'])) echo ' | Col: ' . $row['col'];
                                            if (isset($row['row'])) echo ' | Row: ' . $row['row'];
                                            if (isset($row['race'])) echo ' | Race: ' . $row['race'];
                                            if (isset($row['planet'])) echo ' | Planet: ' . $row['planet'];
                                            if (isset($row['player'])) echo ' | Player: ' . $row['player'];
                                            if (isset($row['price'])) echo ' | Preis: ' . $row['price'];
                                            if (isset($row['time'])) echo 'Time: ' . $row['time'];
                                            echo ' | [' . $pid . ']';
                                        ?>
                                    </option>
                                    <?php
                                }
                            }
                            $result->close();
                        }
                    ?>
                </select>
                <div class="spacer"></div>
                <input type="submit" value="Bearbeiten">
            </form>
            <div class="spacer"></div>
            <hr>
            <h2>Löschen</h2>
            Du solltest nur Einträge löschen, wenn diese wirklich nicht mehr benötigt werden!<br />
            Dies kann nämlich zu Problemen führen, wenn der Eintrag in einer anderen Tabelle genutzt wird.<br />
            <div class="spacer"></div>
            Man kann auch nicht mehr benötigte Einträge später auf die Werte eines neuen Eintrages anpassen, statt einen neuen Eintrag zu nutzen.<br />
            <div class="spacer"></div>
            <form method="POST" action="?p=admin&a=delete&table=<?php echo $table; ?>">
                <select class="select" name="id">
                    <?php
                        if($table == 'inventory')
                        {
                            if(isset($_GET['pid']) && is_numeric($_GET['pid']))
                            {
                                $result = $database->Select('*', $table, 'ownerid ='.$_GET['pid'], 999999, 'ownerid', 'ASC');
                            }
                            else
                            {
                                $result = $database->Select('*', $table, '', 999999, 'ownerid', 'ASC');
                            }
                        }
                        else
                            $result = $database->Select('*', $table, '', 999999);
                        if ($result)
                        {
                            if ($result->num_rows > 0)
                            {
                                while ($row = $result->fetch_assoc())
                                {
                                    $pid = (isset($row['id'])) ? $row['id'] : $row['ID'];
                                    ?>
                                    <option value="<?php echo $pid; ?>">
                                        <?php
                                            $id = (isset($row['spielerid'])) ? $row['spielerid'] : 0;
                                            if ($id != 0)
                                            {
                                                $spieler = new Generallist($database, 'accounts', 'name, id', 'id = ' . $id . '', 1);
                                                $spielerid = $spieler->GetEntry(0);
                                            }
                                            else
                                                $spielerid = null;

                                            $id = (isset($row['userid'])) ? $row['userid'] : 0;
                                            if ($id != 0)
                                            {
                                                $users = new Generallist($database, 'accounts', 'name, id', 'id = ' . $id . '', 1);
                                                $userid = $users->GetEntry(0);
                                            }
                                            else
                                                $userid = null;

                                            $id = (isset($row['ownerid'])) ? $row['ownerid'] : 0;
                                            if ($id != 0)
                                            {
                                                $users = new Generallist($database, 'accounts', 'name, id', 'id = ' . $id . '', 1);
                                                $ownerid = $users->GetEntry(0);
                                            }
                                            else
                                                $ownerid = null;

                                            $id = (isset($row['attack'])) ? $row['attack'] : 0;
                                            if ($id != 0)
                                            {
                                                $attacks = new Generallist($database, 'attacks', 'name, id', 'id = ' . $id . '', 1);
                                                $attack = $attacks->GetEntry(0);
                                            }
                                            else
                                                $attack = null;

                                            $id = (isset($row['statsid'])) ? $row['statsid'] : 0;
                                            if ($id != 0)
                                            {
                                                $items = new Generallist($database, 'items', 'name, id', 'id = ' . $id . '', 1);
                                                $item = $items->GetEntry(0);
                                            }
                                            else
                                                $item = null;

                                            if (isset($row['used']))
                                            {
                                                if ($row['used'] == 0)
                                                {
                                                    $genutzt = "Benutzt: Nein";
                                                }
                                                else
                                                {
                                                    $genutzt = "Benutzt: Ja";
                                                }
                                            }
                                            if (isset($row['name'])) echo $row['name'];
                                            else if (isset($row['title'])) echo $row['title'];
                                            else if (isset($row['stars'])) echo 'Stars: ' . $row['stars'];
                                            else if (isset($row['promocode'])) echo $row['promocode'];
                                            else if (isset($row['titel'])) echo $row['titel'];
                                            else if (isset($row['topic'])) echo $row['topic'];
                                            else if (isset($row['seller'])) echo $item['name'] . ' x' . $row['amount'] . ' | ' . $row['seller'] . ' (' . $row['sellerid'] . ')';
                                            else if (isset($row['Betreff'])) echo $row['Betreff'];
                                            else if (isset($row['spielerid'])) echo $row['spielerid'] . ' ' . $spielerid['name'];
                                            else if (isset($row['comment'])) echo $row['comment'];
                                            else if (isset($row['userid'])) echo $row['userid'] . ' ' . $userid['name'];
                                            else if (isset($row['attack'])) echo $row['attack'] . ' ' . $attack['name'];
                                            else if (isset($row['ownerid'])) echo $item['name'] . ' (' . $item['id'] . ') x' . $row['amount'] . ' | ' . $ownerid['name'] . ' (' . $ownerid['id'] . ')';
                                            if (isset($row['col'])) echo ' | Col: ' . $row['col'];
                                            if (isset($row['row'])) echo ' | Row: ' . $row['row'];
                                            if (isset($row['race'])) echo ' | Race: ' . $row['race'];
                                            if (isset($row['planet'])) echo ' | Planet: ' . $row['planet'];
                                            if (isset($row['player'])) echo ' | Player: ' . $row['player'];
                                            if (isset($row['price'])) echo ' | Preis: ' . $row['price'];
                                            echo ' | [' . $pid . ']';
                                        ?>
                                    </option>
                                    <?php
                                }
                            }
                            $result->close();
                        }
                    ?>
                </select>
                <div class="spacer"></div>
                <input type="checkbox" id="sure" name="sure"><label for="sure" style="cursor: pointer;">Sicher?</label>
                <div class="spacer"></div>
                <input type="submit" value="Löschen">
            </form>
            <br />
            <hr>
            <h2>Erstellen</h2>
            Es wird nur ein neuer Eintrag erstellt, wenn das ID Feld leer ist.
            <div class="spacer"></div>
            <form method="GET" action="?p=admin">
                <input type="hidden" name="p" value="admin">
                <input type="hidden" name="a" value="see">
                <input type="hidden" name="table" value="<?php echo $table; ?>">
                <input type="submit" value="Erstellen">
            </form>
            <?php
        }
    }
?>

<script>
    $(document).ready(function() {
        $('.select').select2();
    });
</script>

<style>
    .select2-results {
        color: #000000;
    }
</style>