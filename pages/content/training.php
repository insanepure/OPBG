<table width="100%" cellspacing="0">
    <tr>
        <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Aktionen</b></td>
    </tr>
    <tr>
        <td width="15%" style="text-align:center"><b>Bild</b></td>
        <td width="15%" style="text-align:center"><b>Name</b></td>
        <td width="30%" style="text-align:center"><b>Wirkung</b></td>
        <td width="15%" style="text-align:center"><b>Kosten</b></td>
        <td width="10%" style="text-align:center"><b>Stunden</b></td>
        <td width="10%" style="text-align:center"><b>Aktion</b></td>
    </tr>
    <?php
        $actions = $place->GetActions();
        $i = 0;
        while (isset($actions[$i]))
        {
            $action = $actions[$i];
            $canSee = true;
            if ($action->GetLevel() > $player->GetLevel())
                $canSee = false;

            if ($action->IsSpecial())
                $canSee = false;
            if ($action->GetType() == 15)
                $canSee = false;
            if ($canSee)
            {
                ?>
                <tr>
                    <form method="POST" action="?p=training&a=train&id=<?php echo $action->GetID(); ?>">
                        <td><img class="boxSchatten borderT borderR borderL borderB" src="img/actions/<?php echo $action->GetImage(); ?>.png" width="75px" height="75px" style="margin-left:5px; margin-top:5px;"></td>
                        <td><?php echo $action->GetName(); ?></td>
                        <td>
                            <?php echo $bbcode->parse($action->GetDescription()); ?>
                        </td>
                        <td>
                            <?php
                                if ($action->GetPrice() != 0)
                                {
                                    $price = number_format($action->GetPrice(),'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/> /h';
                                    if($player->GetBerry() >= $action->GetPrice())
                                        echo '<span style="color: white;">'. $price . '</span>';
                                    else
                                        echo '<span style="color: red;">'. $price . '</span>';
                                }
                                echo '<br/>';
                                if ($action->GetItem() != 0)
                                {
                                    $itemData = $itemManager->GetItem($action->GetItem());
                                    $itemname = '1x ' . $itemData->GetName();
                                    if($player->HasItemWithID($action->GetItem(), $action->GetItem()))
                                        echo '<span style="color: white;">'. $itemname . '</span>';
                                    else
                                        echo '<span style="color: red;">'. $itemname . '</span>';
                                }
                                if ($action->GetItems() != "")
                                {
                                    $items = explode(";", $action->GetItems());
                                    foreach($items as &$itemDataArray)
                                    {
                                        $itemData = explode('@',$itemDataArray);
                                        $itemID = $itemData[0];
                                        $itemAmount = $itemData[1];
                                        $itemData = $itemManager->GetItem($itemID);
                                        $itemname = number_format($itemAmount,'0', '', '.') . ' x ' . $itemData->GetName() . ' /h';
                                        if($player->GetInventory()->GetItemAmount($itemID) >= $itemAmount)
                                            echo '<span style="color: white;">'. $itemname . '</span>';
                                        else
                                            echo '<span style="color: red;">'. $itemname . '</span>';
                                    }
                                }
                            ?>
                        </td>
                        <td style="text-align:center">
                            <select class="select" style="width:100px" name="hours">
                                <?php
                                    $minutes = $action->GetMinutes();
                                    $startHours = $minutes / 60;
                                    $maxHours = 24 * 30;
                                    $j = 0;
                                    $hours = 0;
                                    while ($maxHours > $hours)
                                    {
                                        if ($j == $action->GetMaxTimes())
                                            break;
                                        $hours = $startHours + ($startHours * $j);
                                        if($action->GetMinutes() < 60)
                                        {
                                            ?>
                                            <option value="<?php echo $action->GetMinutes() / 60; ?>"><?php echo $action->GetMinutes();
                                                    if ($action->GetMinutes() == 1) echo ' Minute';
                                                    else echo ' Minuten'; ?></option>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <option value="<?php echo $hours; ?>"><?php echo number_format($hours,'0', '', '.');
                                                    if ($hours == 1) echo ' Stunde';
                                                    else echo ' Stunden'; ?></option>
                                            <?php
                                        }
                                        ++$j;
                                    }
                                ?>
                            </select>
                        </td>
                        <td style="text-align:center">
                            <input type="submit" value="Start" />
                        </td>
                    </form>
                </tr>
                <?php
            }

            ++$i;
        }
    ?>
</table>
<?php
    $actions = $place->GetActions();
    $canSeetwo = false;

    if(count($actions) > 1)
    {
        foreach ($actions as $action)
        {
            if ($action->GetLevel() > $player->GetLevel())
                $canSeetwo = false;

            if ($action->IsSpecial())
                $canSeetwo = false;

            if ($action->GetType() == 15 && !$player->HasSpecialTrainingUsed($action->GetID()))
                $canSeetwo = true;
        }
    }

    if($canSeetwo)
    {
?>
<table width="100%" cellspacing="0">
    <tr>
        <td class="catGradient borderB borderT" colspan="6" style="text-align:center"><b>Spezial Training</b></td>
    </tr>
    <tr>
        <td width="15%" style="text-align:center"><b>Bild</b></td>
        <td width="15%" style="text-align:center"><b>Name</b></td>
        <td width="30%" style="text-align:center"><b>Wirkung</b></td>
        <td width="15%" style="text-align:center"><b>Kosten</b></td>
        <td width="10%" style="text-align:center"><b>Stunden</b></td>
        <td width="10%" style="text-align:center"><b>Aktion</b></td>
    </tr>
    <?php
        }


        $i = 0;
        while (isset($actions[$i]))
        {
            $action = $actions[$i];
            $canSeetwo = false;
            if ($action->GetLevel() > $player->GetLevel())
                $canSeetwo = false;

            if ($action->IsSpecial())
                $canSeetwo = false;

            if ($action->GetType() == 15 && !$player->HasSpecialTrainingUsed($action->GetID()))
                $canSeetwo = true;

            if ($canSeetwo)
            {
                ?>
                <tr>
                    <form method="POST" action="?p=training&a=train&id=<?php echo $action->GetID(); ?>">
                        <td><img class="boxSchatten borderT borderR borderL borderB" src="img/actions/<?php echo $action->GetImage(); ?>.png" width="75px" height="75px" style="margin-left:5px; margin-top:5px;"></td>
                        <td><?php echo $action->GetName(); ?></td>
                        <td>
                            <?php echo $bbcode->parse($action->GetDescription()) . " Für dieses Training benötigt man Level: " . number_format($action->GetLevel(),'0', '', '.') . ""; ?>
                        </td>
                        <td>
                            <?php
                                if ($action->GetPrice() != 0)
                                {
                                    echo number_format($action->GetPrice(),'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/> /h';
                                }
                                echo '<br/>';
                                if ($action->GetItem() != 0)
                                {
                                    $itemData = $itemManager->GetItem($action->GetItem());
                                    echo $itemData->GetName();
                                }
                            ?>
                        </td>
                        <td style="text-align:center">
                            <select class="select" style="width:100px" name="hours">
                                <?php
                                    $minutes = $action->GetMinutes();
                                    $startHours = $minutes / 60;
                                    $maxHours = 24 * 30;
                                    $j = 0;
                                    $hours = 0;
                                    while ($maxHours > $hours)
                                    {
                                        if ($j == $action->GetMaxTimes())
                                            break;
                                        $hours = $startHours + ($startHours * $j);
                                        ?>
                                        <option value="<?php echo $hours; ?>"><?php echo number_format($hours,'0', '', '.');
                                                if ($hours == 1) echo ' Stunde';
                                                else echo ' Stunden'; ?></option>
                                        <?php
                                        ++$j;
                                    }
                                ?>
                            </select>
                        </td>
                        <td style="text-align:center">
                            <input type="submit" value="Start" />
                        </td>
                    </form>
                </tr>
                <?php
            }
            ++$i;
        }
    ?>
</table>