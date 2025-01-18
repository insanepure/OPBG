<?php

$debugTree = false;
if (!isset($p))
{
    ?>
    <div class="spacer"></div>
    Du hast noch
    <?php
    echo number_format($player->GetSkillPoints(),'0', '', '.');
    if ($player->GetSkillPoints() == 1)
    {
        ?> Skillpoint.<?php
    }
    else
    {
        ?> Skillpoints.<?php
    }
}
?>

    <div class="spacer"></div>
    <table width="600px">
        <tr>
            <td style="text-align:center"><a href="?p=<?php if (isset($p)) echo $p; ?>skilltree&tree=1<?php if (isset($p)) echo '&race=' . $race; ?>">
                    <div class="SideMenuButton <?php if (!isset($_GET['tree']) || $_GET['tree'] == 1) echo 'skilltree'; ?>">Zoan</div>
                </a></td>
            <td style="text-align:center"><a href="?p=<?php if (isset($p)) echo $p; ?>skilltree&tree=2<?php if (isset($p)) echo '&race=' . $race; ?>">
                    <div class="SideMenuButton <?php if ($_GET['tree'] == 2) echo 'skilltree'; ?>">Paramecia</div>
                </a></td>
            <td style="text-align:center"><a href="?p=<?php if (isset($p)) echo $p; ?>skilltree&tree=3<?php if (isset($p)) echo '&race=' . $race; ?>">
                    <div class="SideMenuButton <?php if ($_GET['tree'] == 3) echo 'skilltree'; ?>">Logia</div>
                </a></td>
            <td style="text-align:center"><a href="?p=<?php if (isset($p)) echo $p; ?>skilltree&tree=4<?php if (isset($p)) echo '&race=' . $race; ?>">
                    <div class="SideMenuButton <?php if ($_GET['tree'] == 4) echo 'skilltree'; ?>">Schwertkämpfer</div>
                </a></td>
            <td style="text-align:center"><a href="?p=<?php if (isset($p)) echo $p; ?>skilltree&tree=5<?php if (isset($p)) echo '&race=' . $race; ?>">
                    <div class="SideMenuButton <?php if ($_GET['tree'] == 5) echo 'skilltree'; ?>">Schwarzfuß</div>
                </a></td>
            <td style="text-align:center"><a href="?p=<?php if (isset($p)) echo $p; ?>skilltree&tree=6<?php if (isset($p)) echo '&race=' . $race; ?>">
                    <div class="SideMenuButton <?php if ($_GET['tree'] == 6) echo 'skilltree'; ?>">Karatekämpfer</div>
                </a></td>

        </tr>
    </table>



<?php
$tree = 1;
$tree2 = 1;
if (isset($_GET['tree']) && is_numeric($_GET['tree']) && $_GET['tree'] >= 1 && $_GET['tree'] <= 6)
{
    $tree = $database->EscapeString($_GET['tree']);

    if($tree >= 1 && $tree <= 3)
    {
        if(isset($_GET['tree2']) && is_numeric($_GET['tree2']) && $_GET['tree2'] >= 1 && $_GET['tree2'] <= 3)
            $tree2 = $database->EscapeString($_GET['tree2']);
        else
            $tree2 = 1;
    }
    else
        $tree2 = 0;
}


$select = "skilltree.*, attacks.id as atkID, attacks.name as atkName, attacks.image as atkImage";
$where = 'attacks.id = skilltree.attack AND skilltree.type="' . $tree . '" AND skilltree.type2 = "'. $tree2 .'" AND (skilltree.race="" OR skilltree.race="' . $race . '")';
$order = 'id';
$join = 'attacks';
$from = 'skilltree';
$group = 'skilltree.attack';
$skilltreeData = new Generallist($database, $from, $select, $where, $order, 100, 'ASC', $join, $group);

$select = 'id, type, attack, angle, col, row, learnable';
$where = 'attack=0 AND type="' . $tree . '" AND skilltree.type2 = "'. $tree2 .'" AND (race="" OR race="' . $race . '")';
$skilltreeData->AddEntries('skilltree', $select, $where, '', 100, 'ASC');

$height = 800;
$width = 640;
$leftOffset = 25;
?>
    <div style="margin-top:3px;
            width:<?php echo $width; ?>px; height:<?php echo $height; ?>px;
            border: 2px solid #ffd700;
            outline: 1px solid #000;
            background-image: radial-gradient(#222, #111);">
        <?php
        if($tree >= 1 && $tree <= 3)
        {
            ?>
            <table width="600px">
                <tr>
                    <td style="text-align:center">
                        <a href="?p=<?php if (isset($p)) echo $p; ?>skilltree&tree=<?php echo $tree; ?><?php if (isset($p)) echo '&race=' . $race; ?>&tree2=1">
                            <div class="SideMenuButton <?php if (!isset($_GET['tree2']) || $_GET['tree2'] == 1) echo 'skilltree'; ?>">
                                <?php
                                switch ($tree)
                                {
                                    case 1:
                                        echo 'Fisch-Frucht';
                                        break;
                                    case 2:
                                        echo 'Operations-Frucht';
                                        break;
                                    case 3:
                                        echo 'Donner-Frucht';
                                        break;
                                }
                                ?>
                            </div>
                        </a>
                    </td>
                    <td style="text-align:center">
                        <a href="?p=<?php if (isset($p)) echo $p; ?>skilltree&tree=<?php echo $tree; ?><?php if (isset($p)) echo '&race=' . $race; ?>&tree2=2">
                            <div class="SideMenuButton <?php if ($_GET['tree2'] == 2) echo 'skilltree'; ?>">
                                <?php
                                switch ($tree)
                                {
                                    case 1:
                                        echo 'Vogel-Frucht';
                                        break;
                                    case 2:
                                        echo 'Faden-Frucht';
                                        break;
                                    case 3:
                                        echo 'Feuer-Frucht';
                                        break;
                                }
                                ?>
                            </div>
                        </a>
                    </td>
                    <td style="text-align:center">
                        <a href="?p=<?php if (isset($p)) echo $p; ?>skilltree&tree=<?php echo $tree; ?><?php if (isset($p)) echo '&race=' . $race; ?>&tree2=3">
                            <div class="SideMenuButton <?php if ($_GET['tree2'] == 3) echo 'skilltree'; ?>">
                                <?php
                                switch ($tree)
                                {
                                    case 1:
                                        echo 'Mensch-Mensch-Frucht';
                                        break;
                                    case 2:
                                        echo 'Mochi-Frucht';
                                        break;
                                    case 3:
                                        echo 'Gefrier-Frucht';
                                        break;
                                }
                                ?>
                            </div>
                        </a>
                    </td>
                </tr>
            </table>
            <?php
        }
        $id = 0;
        $entry = $skilltreeData->GetEntry($id);
        while ($entry != null)
        {
            $image = '';
            $isAttack = $entry['attack'] != 0;
            if ($isAttack)
                $image = $entry['atkImage'];
            else
                $image = 'cellline';
            $left = ($entry['col'] * 35) + $leftOffset;
            $top = $height - $topOffset - ($entry['row'] * 35);
            $hasAttack = true;
            if (!isset($p))
                $hasAttack = in_array($entry['attack'], $pAttacks);
            ?>
            <div style="position:absolute; left:<?php echo $left; ?>px; top:<?php echo $top; ?>px; width:50px; height:50px;">

                <?php
                if ($isAttack)
                {
                    ?>
                    <div class="tooltip" style="z-index:<?php echo $top; ?>; position:absolute; left:0px; top:0px;">
                        <?php
                        if ($entry['learnable'] && !$hasAttack)
                        {
                        ?>
                        <a href="?p=skilltree&tree=<?php echo $_GET['tree']; ?>&tree2=<?php echo $_GET['tree2']; ?>&a=learn&attack=<?php echo $entry['id']; ?>">
                            <?php
                            }
                            else if ($entry['learnable'] && isset($p))
                            {
                            ?>
                            <a href="?p=info&info=techniken&id=<?php echo $entry['attack']; ?>">
                                <?php
                                }
                                if ($debugTree)
                                {
                                    ?>
                                    <div style="position:absolute; left:0px; top:20px; width:50px; height:50px; z-index:3;">
                                        <b>
                                            <p style="color:#ff0000"><?php echo $entry['id']; ?></p>
                                        </b>
                                    </div>
                                    <?php
                                }
                                ?>
                                <img src="img/attacks/<?php echo $image; ?>.png" style="position:absolute; z-index:1; left:3px; top:3px; width:45px; height:45px;
                                <?php
                                if (!$hasAttack)
                                {
                                    ?>
                                        filter: gray; /* IE6-9 */
                                        -webkit-filter: grayscale(1); /* Google Chrome, Safari 6+ & Opera 15+ */
                                        filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */
                                    <?php
                                }
                                ?>
                                        " />
                                <img src="img/skilltree/cell.png" style="position:absolute; left:0px; top:0px; width:50px; height:50px; z-index:2;" />
                                <?php
                                if (($entry['learnable'] && !$hasAttack) || ($entry['learnable'] && isset($p)))
                                {
                                ?>
                            </a>
                        <?php
                        }
                                if($entry['berry'] > 0)
                                    $top = -145;
                                else
                                    $top = -130;
                        ?>
                            <span class="tooltiptext" style="min-width:180px; width: fit-content; top:<?= $top ?>px; left:-70px; z-index: 0;">
                  <?php
                  $needitem = '';
                  if ($entry['type'] == 1)
                  {
                      $needitem = "Teufelsfrucht Zoan";
                  }
                  else if ($entry['type'] == 2)
                  {
                      $needitem = "Teufelsfrucht Paramecia";
                  }
                  else if ($entry['type'] == 3)
                  {
                      $needitem = "Teufelsfrucht Logia";
                  }
                  else if ($entry['type'] == 4)
                  {
                      $needitem = "Schwert";
                  }
                  else if ($entry['type'] == 5)
                  {
                      $needitem = "Schuhe";
                  }
                  else if ($entry['type'] == 6)
                  {
                      $needitem = "Schwarzgurt";
                  }
                  echo '<span style="white-space: nowrap;">'. $entry['atkName'] . "</span><br />";
                  echo '<hr>';
                  echo "Benötigte Skillpunkte: " . number_format($entry['neededpoints'],'0', '', '.') . "<br />";
                  echo $needitem . "<br /> Benötigte Anzahl: " . number_format($entry['amount'],'0', '', '.') . "<br />";
                  echo "Benötigtes Level: " . number_format($entry['level'],'0', '', '.') . "<br />";
                  if($entry['berry'] > 0)
                    echo "Berrykosten: " . number_format($entry['berry'], '0', '', '.') . " <img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 2px; height: 20px; width: 13px;'/><br />";
                  if ($debugTree)
                  {
                      echo ' (' . $entry['attack'] . ')';
                      echo ' NeedAtk: ' . $entry['needattacks'];
                      echo ' NeedPts: ' . $entry['neededpoints'];
                  }
                  ?>
                </span>
                            <?php
                            if (isset($p))
                            {
                                echo '</a>';
                            }
                            ?>
                    </div>
                    <?php
                }
                else
                {
                    $left = 0;
                    $top = 0;
                    $cellHeight = 50;
                    if ($entry['angle'] != 0)
                    {
                        $left = 5;
                        $top = -15;
                        $cellHeight = 85;
                    }
                    if ($debugTree)
                    {
                        ?>
                        <div style="position:absolute; z-index:0; left:<?php echo $left - 25; ?>px; top:<?php echo $top + 15 + ($entry['angle'] / 5); ?>px; width:100px; z-index:3;">
                            <b>
                                <span style="color: #fff;"><?php echo $entry['id']; ?></span>
                            </b>
                        </div>
                        <?php
                    }
                    ?>
                    <img src="img/skilltree/<?php echo $image; ?>.png" style="transform: rotate(<?php echo $entry['angle']; ?>deg); position:absolute; z-index:0; left:<?php echo $left; ?>px; top:<?php echo $top; ?>px; width:45px; height:<?php echo $cellHeight; ?>px;" />
                    <?php
                }
                ?>
            </div>
            <?php
            ++$id;
            $entry = $skilltreeData->GetEntry($id);
        }
        ?>
    </div>
<?php
if($player->GetArank() >= 2)
{
    ?>
    <div class="spacer"></div>
    <form method="post" action="?p=skilltree&a=reset">
        <input type="submit" value="Reset">
    </form>
    <?php
}
?>