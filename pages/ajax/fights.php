<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/serverurl.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '../../classes/header.php';

$db = 'DATENBANK';
$user = 'USER';
$pw = 'PASSWORT';
    $database = new Database($db, $user, $pw);

    include_once '../../classes/generallist.php';
    include_once '../../classes/fight/fight.php';
?>
<div class="catGradient" style="margin-top: 40px;">
    <h2>
        Kämpfe
    </h2>
</div>
<div style="width: 600px; height: 632px; overflow-y: auto; float:left;">
    <table width="100%">
        <?php
            $fightslist = new GeneralList($database, 'fights', '*', 'fighters LIKE "%['.$_GET['id'].']%"', 'id', 99999, 'ASC');

            $id = 0;
            $entry = $fightslist->GetEntry($id);
            if($entry == null)
            {
                ?>
                <tr>
                    <td style="text-align: center;">
                        Keine Kämpfe vorhanden.
                    </td>
                </tr>
                <?php
            }
            while($entry != null)
            {

                ?>
                <tr>
                    <td class="borderB">
                        <a target="_blank" href="?p=infight&fight=<?php echo $entry['id']; ?>"><?php echo date('H:i:s', strtotime($entry['time'])); ?></a>
                    </td>
                    <td class="borderB">
                        <a target="_blank" title="log" href="?p=fightlog&fight=<?php echo $entry['id']; ?>"><?php echo $entry['id']; ?></a>
                    </td>
                    <td class="borderB">
                        <a target="_blank" href="?p=infight&fight=<?php echo $entry['id']; ?>"><?php echo Fight::GetTypeName($entry['type']); ?></a>
                    </td>
                    <td class="borderB">
                        <?php
                            if($entry['type'] != 1 && $entry['type'] != 13)
                                echo '<a target="_blank" href="?p=infight&fight=' . $entry['id'] . '">' . $entry['name'] . '</a>';
                            else if($entry['mirror'] == 1)
                                echo $entry['name'] . ' als Mirrorkampf';
                            else
                            {
                                $fighters = explode(';', $entry['fighters']);
                                $fighters = str_replace('[', '', $fighters);
                                $fighters = str_replace(']', '', $fighters);
                                foreach ($fighters as $fighter)
                                {
                                    if($fighter != $_GET['id'])
                                    {
                                        $enemy = $fighter;
                                    }
                                }
                                $result = $database->Select('*', 'accounts', 'id='.$enemy, 1);
                                if($result)
                                {
                                    $enemy = $result->fetch_assoc();
                                }
                                $result = $database->Select('*', 'accounts', 'id='.$_GET['id'], 1);
                                if($result)
                                {
                                    $otherPlayer = $result->fetch_assoc();
                                }
                                $color = 'red';
                                if($enemy['clan'] != 0 && $enemy['clan'] == $otherPlayer['clan'])
                                    $color = 'green';
                                echo $entry['name'] . ' gegen <a target="_blank" href="?p=profil&id='.$enemy['id'].'" style="color: '.$color.'">' . $enemy['name'] . '</a>';
                            }

                        ?>
                    </td>
                    <td class="borderB">
                        <?php
                            if(in_array($_GET['id'], explode(';', $entry['winner'])))
                            {
                                echo '<span style="color: green;">Gewonnen</span>';
                            }
                            else
                            {
                                echo '<span style="color: red;">Verloren</span>';
                            }
                        ?>
                    </td>
                    <td class="borderB">
                        <?php
                            if(in_array($_GET['id'], explode(';', $entry['winner'])) && in_array($_GET['id'], explode(';', $entry['gainaccs'])))
                            {
                                if($entry['zeni'] > 0)
                                {
                                    echo number_format($entry['zeni'], 0, '', '.');
                                    ?>
                                    <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/>
                                    <?php
                                }
                            }
                        ?>
                    </td>
                    <td class="borderB">
                        <?php
                            if(in_array($_GET['id'], explode(';', $entry['winner'])) && in_array($_GET['id'], explode(';', $entry['gainaccs'])))
                            {
                                if($entry['gold'] > 0)
                                {
                                    echo number_format($entry['gold'], 0, '', '.');
                                    ?>
                                    <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>
                                    <?php
                                }
                            }
                        ?>
                    </td>
                    <td class="borderB">
                        <?php
                            if(in_array($_GET['id'], explode(';', $entry['winner'])) && in_array($_GET['id'], explode(';', $entry['gainaccs'])))
                            {
                                if($entry['kopfgeld'] > 0)
                                {
                                    echo number_format($entry['kopfgeld'], 0, '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/>';
                                }
                            }
                        ?>
                    </td>
                    <td class="borderB">
                        <?php
                            if(in_array($_GET['id'], explode(';', $entry['winner'])) && in_array($_GET['id'], explode(';', $entry['gainaccs'])))
                            {
                                if($entry['items'] != 0)
                                {
                                }
                            }
                        ?>
                    </td>
                </tr>
                <?php
                $id++;
                $entry = $fightslist->GetEntry($id);
            }
        ?>
    </table>
</div>
<div style="width: 600px; height: 130px; float:left;">
    <?php
        $fightslist = new GeneralList($database, 'fights', '*', 'fighters LIKE "%['.$_GET['id'].']%"', 'id', 99999);
        $bountylist = new GeneralList($database, 'fights', '*', 'type=1 AND fighters LIKE "%['.$_GET['id'].']%"', 'id', 99999);
        $elolist = new GeneralList($database, 'fights', '*', 'type=13 AND fighters LIKE "%['.$_GET['id'].']%"', 'id', 99999);
        $npclist = new GeneralList($database, 'fights', '*', 'type=3 AND fighters LIKE "%['.$_GET['id'].']%"', 'id', 99999);
        $arenalist = new GeneralList($database, 'fights', '*', 'type=8 AND fighters LIKE "%['.$_GET['id'].']%"', 'id', 99999);
        $bountywonlist = new GeneralList($database, 'fights', '*', 'type=1 AND winner LIKE "%'.$_GET['id'].'%" AND fighters LIKE "%['.$_GET['id'].']%"', 'id', 99999);
        $bountylostlist = new GeneralList($database, 'fights', '*', 'type=1 AND winner NOT LIKE "%'.$_GET['id'].'%" AND fighters LIKE "%['.$_GET['id'].']%"', 'id', 99999);
    ?>
    <div class="spacer"></div>
    Kämpfe gesamt: <?php echo number_format($fightslist->GetCount(), 0, '', '.'); ?> <br/>
    NPC gesamt: <?php echo number_format($npclist->GetCount(), 0, '', '.'); ?> <br/>
    Kolosseum gesamt: <?php echo number_format($arenalist->GetCount(), 0, '', '.'); ?> <br/>
    Elokämpfe gesamt: <?php echo number_format($elolist->GetCount(), 0, '', '.'); ?> <br/>
    PvP-Kämpfe gesamt: <?php echo number_format($bountylist->GetCount(), 0, '', '.'); ?> <br/>
    PvP-Kämpfe gewonnen: <?php echo number_format($bountywonlist->GetCount(), 0, '', '.'); ?> <br/>
    PvP-Kämpfe verloren: <?php echo number_format($bountylostlist->GetCount(), 0, '', '.'); ?> <br/>
</div>
