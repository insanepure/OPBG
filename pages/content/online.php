<?php

$titelManager = new TitelManager($database);
$GameData = new GameData($database);
$timeOut = 15;
$where = 'deleted=0 AND TIMESTAMPDIFF(MINUTE, lastaction, NOW()) < ' . $timeOut;
$start = 0;
$limit = 30;
if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
{
    $start = $limit * ($_GET['page'] - 1);
}
$onlineList = new Generallist($database, 'accounts', 'userid,lastaction,id,name,rank,race,place,level,titel,planet,clan,clanname,onlinestatus', $where, 'planet, rank, level', $start . ',' . $limit, 'ASC');
?>
    <div class="spacer"></div>
    <table width="100%" cellspacing="0" border="0">
        <?php
        $id = 0;
        $previousPlanet = 0;
        $entry = $onlineList->GetEntry($id);
        while ($entry != null)
        {
            $listplanet = new Planet($database, $entry['planet']);
            if ($previousPlanet != $entry['planet'])
            {
                if(!$listplanet->IsVisible() && (($player->IsLogged() && $player->GetArank() < 2) || !$player->IsLogged()))
                {
                    $entry['planet'] = $previousPlanet;
                    $planenew = new Planet($database, $entry['planet']);
                    $startingplacenew = new Place($database, $planenew->GetStartingPlace(), null);
                    $entry['place'] = $planenew->GetStartingPlace();
                }

            }
            $clan = new Clan($database, $player->GetClan());
            if ($entry['onlinestatus'] == 1 || $player->GetArank() >= 2 || $player->GetClan() != 0 && in_array($entry['clan'], $clan->GetAlliances()) || $player->GetClan() == $entry['clan'] && $player->GetClan() != 0 || $player->IsFriend($entry['id']) || $player->IsLogged() && $entry['userid'] == $player->GetUserID())
            {
                if ($previousPlanet != $listplanet->GetID())
                {
                    if ($previousPlanet != 0)
                    {
                        ?>
                        <tr>
                            <td colspan=6 height="20px">
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan=6 class="catGradient borderT borderB" style="text-align: center;">
                            <?php
                            if ($listplanet->GetID() == 2)
                            {
                                ?>
                                <b>
                                                <span style="color: white;">
                                                    <div class="schatten">User im <?php echo $listplanet->GetName(); ?></div>
                                                </span>
                                </b>
                                <?php
                            }
                            else if ($listplanet->GetID() == 4)
                            {
                                ?>
                                <b>
                                                <span style="color: white;">
                                                    <div class="schatten">User auf <?php echo $listplanet->GetName(); ?></div>
                                                </span>
                                </b>
                                <?php
                            }
                            else
                            {
                                ?>
                                <b>
                                                <span style="color: white;">
                                                    <div class="schatten">User im Gebiet<?php if($listplanet->GetID() == 3) { echo ' der '; } else { echo ' des '; } echo $listplanet->GetName(); ?></div>
                                                </span>
                                </b>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="5%"><b>Level</b></td>
                        <td width="30%"><b>User</b></td>
                        <td width="5%"><b>Rang</b></td>
                        <td width="15%"><b>Fraktion</b></td>
                        <td width="20%"><b>Bande</b></td>
                        <td width="30%"><b>Ort</b></td>
                    </tr>
                    <?php

                    $previousPlanet = $listplanet->GetID();
                }

                $titel = $titelManager->GetTitel($entry['titel']);
                $titelText = '';
                if ($titel != null)
                {
                    $titelText = $titel->GetName();
                    if ($titel->GetColor() != '')
                    {
                        $titelText = '<span style="color: #' . $titel->GetColor() . '">' . $titelText . '</span>';
                    }
                }
                ?>
                <tr>
                    <td width="5%"><?php echo number_format($entry['level'],'0', '', '.'); ?></td>
                    <?php
                    if($entry['id'] == 934 && $entry['titel'] == 110)
                    {
                        echo "<td width='30%'><a href='?p=profil&id=" . $entry['id'] ."'>". $entry['name'] . " ".$titelText."</a></td>";
                    }
                    else
                    {
                        echo "<td width='30%'><a href='?p=profil&id=" . $entry['id'] ."'>". $titelText . " ".$entry['name']."</a></td>";
                    }
                    ?>

                    <td width="10%"><?php echo number_format($entry['rank'],'0', '', '.'); ?></td>
                    <td width="15%"><?php echo $entry['race']; ?></td>
                    <td width="20%">
                        <?php
                        if ($entry['clan'] == 0)
                        {
                            ?><b>Bandenlos</b><?php
                        }
                        else
                        {
                            ?><a href="?p=clan&id=<?php echo $entry['clan']; ?>"><?php echo $entry['clanname']; ?></a><?php
                        }
                        ?></td>
                    <td width="30%">
                        <?php
                        $listplace = new Place($database, $entry['place'], null);

                        if($player->GetArank() < 2 && $listplace->GetAdminPlace())
                        {
                            $listplanet = new Planet($database, $entry['planet']);
                            $startingplace = new Place($database, $listplanet->GetStartingPlace(), null);
                            if($planenew != null)
                                echo $startingplacenew->GetName();
                            else
                                echo $startingplace->GetName();
                        }
                        else
                        {
                            if($listplace->GetID() != -1 && $player->GetArank() >= 2)
                            {
                                ?>
                                <a href="#" onclick="OpenPopupPage('<?php echo $listplace->GetName(); ?>','map/place.php?id=<?php echo $listplace->GetID(); ?>')">
                                    <?php
                                    echo $listplace->GetName();
                                    ?>
                                </a>
                                <?php
                            }
                            else
                                echo $listplace->GetName();
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            $id++;
            $entry = $onlineList->GetEntry($id);
        }
        ?>
    </table>
<?php

$result = $database->Select('COUNT(id) as total', 'accounts', $where);
$total = 0;
if ($result)
{
    $row = $result->fetch_assoc();
    $total = $row['total'];
    $result->close();
}

$pages = ceil($total / $limit);
if ($pages != 1)
{
    ?>
    <div class="spacer"></div>
    <?php
    $i = 0;
    while ($i != $pages)
    {
        ?>
        <a href="?p=online&page=<?php echo $i + 1; ?>">Seite <?php echo number_format($i + 1,'0', '', '.'); ?></a>
        <?php
        ++$i;
    }
}
// Wer war heute Online -> Wer das liest ist doof
$onlinecheck = $database->Select('*', 'accounts', 'dailylogin=1 AND arank=0 AND banned=0 AND deleted=0','', 'name');
$onlineliste = $onlinecheck->num_rows;
echo "<br /><br /><hr><br /><b>Heute waren bereits ".number_format($onlineliste, 0, '', '.')." User online </b><br /><br />";
if($onlinecheck)
{
    $onlineList = '';
    while($online = $onlinecheck->fetch_assoc())
    {
        if($onlineList == '')
            $onlineList = "<a target='_blank' href='?p=profil&id=" . $online['id'] . "'>" . $online['name'] . "</a>";
        else
            $onlineList .= ", <a target='_blank' href='?p=profil&id=" . $online['id'] . "'>" . $online['name'] . "</a>";
    }
    echo $onlineList;
}
echo "<br /><br />";

if($GameData->GetBdayUsersCount() > 0)
{
    $heute = date("d.m.Y");
    echo "<b>Die Spieler die heute am ".$heute." Geburtstag haben!</b><br /><br/>";
    echo $GameData->GetAllBDayUsers();
    echo "<br /><br /><a href='?p=inventar'><img src='img/items/onepieceitemsbdaygeschenk.png'/></a>";

    echo "<hr>";
}

?>