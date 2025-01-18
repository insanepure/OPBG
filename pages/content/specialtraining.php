<?php
$trainers = $place->GetTrainers();
if (count($trainers) == 0)
{
    echo "<div class='spacer'></div><b>Es scheint niemand hier zu sein, der dir etwas Neues beibringen kann..</b><br /><br/>";
}
else
{
    $where = '';
    $i = 0;
    while (isset($trainers[$i]))
    {
        $string = 'id=' . $trainers[$i] . '';
        if ($where == '')
        {
            $where = $string;
        }
        else
        {

            $where = $where . ' OR ' . $string;
        }
        ++$i;
    }

    $where = '(' . $where . ') AND level<="' . $player->GetLevel() . '" AND (race="' . $player->GetRace() . '" OR race="")';
    $start = 0;
    $limit = 10;
    $list = new Generallist($database, 'npcs', 'description, name, image, attacks, id, trainerneeditem, traineritemamount', $where, 'id', $start . ',' . $limit, 'ASC');

    $id = 0;
    $entry = $list->GetEntry($id);
    while ($entry != null)
    {
        ?>
        <table width="90%" cellspacing="0" class="boxSchatten">
            <tr>
                <td class="catGradient borderB borderT borderR borderL" colspan="2" align="center"><b>Training bei <?php echo $entry['name']; ?></b></td>
            </tr>
            <tr>
                <td class="borderL borderR borderB" width="180px"><img class="boxSchatten" width="180px" height="250px" src="img/npc/<?php echo $entry['image']; ?>.png?01"></td>
                <td class="borderL borderR borderB"><?php echo $entry['description']; ?></td>
            </tr>
        </table>
        <div class="spacer"></div>
        <table width="90%" cellspacing="0" class="boxSchatten">
            <tr>
                <td class="catGradient borderB borderT borderR borderL" colspan="8" align="center"><b>Spezialtraining</b></td>
            </tr>
            <tr>
                <td width="10%" class="borderL" align="center"><b>Bild</b></td>
                <td width="10%" align="center"><b>Name</b></td>
                <td width="30%" align="center"><b>Beschreibung</b></td>
                <td width="20%" align="center"><b>Voraussetzung</b></td>
                <td width="10%" class="borderR" align="center"><b>Aktion</b></td>
            </tr>
            <?php
                $attacks = explode(';', $entry['attacks']);
                $j = 0;
                while (isset($attacks[$j]))
                {
                    $attack = $attackManager->GetAttack($attacks[$j]);
                    ?>
                    <form method="POST" action="?p=specialtraining&a=train&npcid=<?php echo $entry['id']; ?>&id=<?php echo $attack->GetID(); ?>">
                        <tr>
                            <td width="50px" class="borderL borderB" align="center">
                                <img src="<?php echo $attack->GetImage(); ?>?02" width="50px" height="50px">
                            </td>
                            <td align="center" class="borderB"><?php echo $attack->GetName(); ?></td>
                            <td align="center" class="borderB"><?php echo $attack->GetDescription(); ?></td>
                            <td class="borderB" align="center">
                                <?php
                                    $nothing = true;
                                    if ($attack->GetLearnKI() != 0) if ($player->GetKI() < $attack->GetLearnKI())
                                    {
                                        echo '<span style="color: red;">Douriki: ' . number_format($attack->GetLearnKI(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    else
                                    {
                                        echo '<span style="color: green;">Douriki: ' . number_format($attack->GetLearnKI(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    if ($attack->GetLearnLP() != 0) if ($player->GetLP() < $attack->GetLearnLP())
                                    {
                                        echo '<span style="color: red;">LP: ' . number_format($attack->GetLearnLP(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    else
                                    {
                                        echo '<span style="color: green;">LP: ' . number_format($attack->GetLearnLP(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    if ($attack->GetLearnKP() != 0) if ($player->GetKP() < $attack->GetLearnKP())
                                    {
                                        echo '<span  style="color: red;">AD: ' . number_format($attack->GetLearnKP(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    else
                                    {
                                        echo '<span  style="color: green;">AD: ' . number_format($attack->GetLearnKP(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    if ($attack->GetLearnAttack() != 0) if ($player->GetAttack() < $attack->GetLearnAttack())
                                    {
                                        echo '<span  style="color: red;">Attack: ' . number_format($attack->GetLearnAttack(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    else
                                    {
                                        echo '<span  style="color: green;">Attack: ' . number_format($attack->GetLearnAttack(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    if ($attack->GetLearnDefense() != 0) if ($player->GetDefense() < $attack->GetLearnDefense())
                                    {
                                        echo '<span  style="color: red;">Defense: ' . number_format($attack->GetLearnDefense(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    else
                                    {
                                        echo '<span  style="color: green;">Defense: ' . number_format($attack->GetLearnDefense(),'0', '', '.') . '</span><br/>';
                                        $nothing = false;
                                    }
                                    if ($entry['trainerneeditem'] != 0 && $entry['traineritemamount'] > 0)
                                    {
                                        $itemManager = new ItemManager($database);
                                        $item = $itemManager->GetItem($entry['trainerneeditem']);
                                        if (!is_null($item)) echo number_format($entry['traineritemamount'], 0, '', '.') . 'x ' . $item->GetName();
                                        $nothing = false;
                                    }
                                    if ($nothing) echo "Keine";

                                ?>
                            </td>
                            <td class="borderR borderB" align="center"><input type="submit" value="Start" /></td>
                        </tr>
                    </form>
                    <?php
                    ++$j;
                }
            ?>
        </table>
        <div class="spacer2"></div>
        <?php
        ++$id;
        $entry = $list->GetEntry($id);
    }
}
?>