<?php
    $select = "fights.*, GROUP_CONCAT(CONCAT(fighters.acc,';',fighters.name,';',fighters.team,';',fighters.isnpc,';',fighters.clan) ORDER BY fighters.team SEPARATOR '@') as fighters";
    $canSeeTest = $player->GetARank() >= 2;
    $where = 'fights.id = fighters.fight';
    if (!$canSeeTest)
        $where = $where . ' AND testfight=0';
    $order = 'state, id, fighters.team';
    $join = 'fighters';
    $from = 'fights';
    $group = 'fights.id';
    $list = new Generallist($database, $from, $select, $where, $order, 9999999, 'ASC', $join, $group);
    $currentFights = array();
    $openFights = array();
    $openID = 0;
    $currentID = 0;

//preSort the arrays, so that we can easily show them
    $id = 0;
    $entry = $list->GetEntry($id);
    while ($entry != null)
    {
        //$clan = null;
        //if($entry['challenge'] != 0)
        //    $clan = new Clan($database, $entry['challenge']);
        if (
                $player->GetClan() != 0 && $player->GetClan() == $entry['challenge'] && $player->GetPlace() == $entry['place'] || // Bandenkampf eigener Clan
                //$player->GetClan() != 0 && !is_null($clan) && $clan->IsValid() && in_array($player->GetClan(), $clan->GetAlliances()) && $player->GetPlace() == $entry['place'] || // Bandenkampf verbündeter Clan
                $entry['challenge'] != 0 && $player->GetFight() == $entry['id'] || // Bandenkampf ist im Kampf
                $entry['event'] != 0 && $player->GetFight() == $entry['id'] ||
                $entry['challenge'] == 0 && $entry['state'] == 0 && $entry['event'] == 0 &&
                ($entry['planet'] == $player->GetPlanet() || $entry['type'] == 0 || $entry['type'] == 1 || $entry['type'] == 13)
        )
        {
            $openFights[$openID] = $entry;
            $openID++;
        }
        else if ($entry['state'] != 0)
        {
            $currentFights[$currentID] = $entry;
            $currentID++;
        }
        $id++;
        $entry = $list->GetEntry($id);
    }
//function to easily display one entry
    function ShowEntry($entry, $player, $database)
    {
        ?>
        <tr>
            <td width="15%" class="boxSchatten" align="center">
                <?php
                if($player->GetArank() < 2)
                {
                    echo $entry['name'];
                }
                else
                {
                    ?>
                <a href="?p=fight&a=delete&id=<?php echo $entry['id'];?>">Löschen</a>
                <?php
                }
                ?>
            </td>
            <td width="15%" class="boxSchatten" align="center">
                <?php
                if ($entry['type'] == 5 && $entry['event'] != 0) {
                    $event = new Event($database, $entry['event']);
                    if ($event->IsDungeon())
                        echo "Dungeon";
                    else
                        echo Fight::GetTypeName($entry['type']);
                } else {
                    echo Fight::GetTypeName($entry['type']);
                }
                ?>
            </td>
            <td width="15%" class="boxSchatten" align="center">
                <?php
                    echo $entry['mode'];
                ?>
            </td>
            <td width="40%" class="boxSchatten">
                <center>
                    <?php
                        $mode = explode('vs', $entry['mode']);
                        $fightPlayers = explode('@', $entry['fighters']);
                        $teamCount = count($mode);

                        $i = 0;
                        $currentTeam = 0;
                        $teamPlayers = 0;
                        while (isset($fightPlayers[$i]))
                        {
                            $playerData = explode(';', $fightPlayers[$i]);
                            if ($playerData[2] != $currentTeam)
                            {
                                ?> vs <?php
                                $currentTeam = $playerData[2];
                                $teamPlayers = 0;
                            }
                            if ($teamPlayers != 0)
                            {
                                ?>, <?php
                            }

							if($entry['type'] == 11 || $entry['type'] == 13) 
							{
								echo '???';
							}
                            else if ($playerData[3])
                            {
                                echo $playerData[1];
                            }
                            else
                            {
                                if($playerData[4] != 0 && $entry['type'] != 13)
                                {
                                    $playerBande = new Clan($database, $playerData[4]);
                                    echo '<a href="?p=clan&id='.$playerBande->GetID().'">';
                                    if($playerBande->GetID() == $player->GetClan())
                                    {
                                        echo '<span style="color: green">';
                                    }
                                    echo '[' . $playerBande->GetTag() .']';
                                    if($playerBande->GetID() == $player->GetClan())
                                    {
                                        echo '</span>';
                                    }
                                    echo '</a> ';
                                }
                                ?>
                                <a href="?p=profil&id=<?php echo $playerData[0]; ?>"><?php echo $playerData[1]; ?></a><?php
                                }
                            ++$teamPlayers;
                            ++$i;
                        }

                        ++$currentTeam;
                        while ($currentTeam < $teamCount)
                        {
                            ?> vs <?php
                            ++$currentTeam;
                        }
                    ?>

                </center>
            </td>
            <td width="15%" class="boxSchatten">
                <center>
                    <?php

                        if ($entry['state'] == 0)
                        {
                            if ($player->GetFight() == $entry['id'] && ($entry['type'] != 11 || $entry['type'] == 11 && $entry['challenge'] == $player->GetClan()))
                            {
                                if($player->GetArank() >= 2)
                                {
                                    $timestamp = strtotime($entry['time']);
                                    $currentTime = strtotime("now");
                                    $difference = $timestamp + 300 - $currentTime;
                                    if ($difference < 0)
                                        $difference = 0;
                                    ?>
                                    <div id="fighttimer">Init
                                        <script>
                                            countdown(<?php echo $difference; ?>, 'fighttimer');
                                        </script>
                                    </div>
                                    <?php
                                }
                                ?>
                                <a href="?p=fight&a=leave">Verlassen</a>
                                <?php
                            }
                            else if($entry['type'] == 11 && $entry['challenge'] != $player->GetClan())
                            {
                                $challengeclan = new Clan($database, $entry['challenge']);
                                ?>
                                <div id="fighttimer">Init
                                    <script>
                                        countdown(<?php echo $challengeclan->GetChallengeCountdown(); ?>, 'fighttimer');
                                    </script>
                                </div>
                                <?php
                            }
                            else
                            {
                                ?>
                                <a href="#" onclick="OpenPopupPage('Kampf Beitreten','fight/join.php','fight=<?php echo $entry['id']; ?>')">Beitreten</a>
                                <?php
                            }
                        }
                        else
                        {
                            ?>
                            <br /><a href="?p=infight&fight=<?php echo $entry['id']; ?>">Zuschauen</a>
                            <?php
                            if ($player->GetArank() == 3)
                            {

                                echo "<br />";
                                ?>
                                <br /><a href="?p=fight&a=delete&id=<?php echo $entry['id'];?>">Löschen</a>
                                <?php
                                for ($i = 0; $i < $teamCount; ++$i)
                                {
                                    ?>
                                    <br /><a href="?p=fight&a=adminjoin&team=<?php echo $i; ?>&fight=<?php echo $entry['id']; ?>">Team <?php echo $i + 1; ?> Beitreten</a><br />
                                    <?php
                                }
                            }
                        }

                    ?>
                </center>
            </td>
        </tr>
        <?php
    }
?>
    <div class="spacer"></div>
    <table width="98%" cellspacing="0" border="0" style="margin-top: -15px;">
        <tr>
            <td colspan=6 class="catGradient borderT borderB">
                <b>
                    <span style="text-align: center">
                        <span style="color: white">
                            <div class="schatten">Kampf Option</div>
                        </span>
                    </span>
                </b>
            </td>
        </tr>
        <tr>
            <td width="50%" class="boxSchatten">
                <center><a href="#" onclick="OpenPopupPage('Kampf Erstellen','fight/create.php')">Kampf Erstellen</a></center>
            </td>
            <td width="50%" class="boxSchatten">
                <center><a href="?p=fights">Vergangene Kämpfe</a></center>
            </td>

        </tr>
    </table>
    <div class="spacer"></div>

<?php
    if(isset($openFights[0]))
    {
        ?>
        <table width="98%" cellspacing="0" border="0">
            <tr>
                <td colspan=6 height="20px">
                </td>
            </tr>
            <tr>
                <td colspan=6 class="catGradient borderT borderB">
                    <b>
                        <center>
                            <div class="schatten">Offene Kämpfe</div>
                        </center>
                    </b>
                </td>
            </tr>
            <?php
                $id = 0;
                while (isset($openFights[$id]))
                {
                    ShowEntry($openFights[$id], $player, $database);
                    ++$id;
                }
            ?>
        </table>
        <?php
    }
?>

<?php
    if(isset($currentFights[0]))
    {
        ?>
        <table width="98%" cellspacing="0" border="0">
            <tr>
                <td colspan=6 height="20px">
                </td>
            </tr>
            <tr>
                <td colspan=6 class="catGradient borderT borderB">
                    <b>
                        <center>
                            <div class="schatten">Laufende Kämpfe</div>
                        </center>
                    </b>
                </td>
            </tr>
            <?php
                $id = 0;
                while (isset($currentFights[$id]))
                {
                    ShowEntry($currentFights[$id], $player, $database);
                    ++$id;
                }
            ?>
        </table>
        <?php
    }
?>