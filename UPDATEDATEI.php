<?php

//Cron Style ist 
//  *      *         *       *        *
//  0,30  15         *       *        2        // jeden Dienstag um 15:00 und 15:30
// */5     *         *       *        *        // alle 5 Minuten
//Minute Stunde Tag (Monat) Monat Tag (Woche)
//
//
//a=ranking - */10 * * * * //Jede 10 Minuten
//a=logout - 0,30 * * * * //jede Stunde um 0 und 30
//a=update - 59 23 * * * //Jeden Tag um 23:59
// https://OPBG.de/oDYrCZ9cAqdK6b.php?s=iz6JbTdzAxQWL6bFjtn5NfNAZfA5FGodWAY23zi3gtzEvNdUS6nGBTRwE6UtJd2W&a=


include_once '/home/users/main/www/classes/session.php';
include_once '/home/users/main/www/classes/header.php';
include_once 'classes/header.php';
error_reporting(0);

$db = 'DATENBANK';
$user = 'USER';
$pw = 'PASSWORT';
$database = new Database($db, $user, $pw);

include_once 'classes/clan/clan.php';
include_once 'classes/titel/titelmanager.php';
include_once 'classes/generallist.php';
include_once 'classes/items/itemmanager.php';
include_once 'classes/fight/attackmanager.php';
include_once 'classes/pms/pmmanager.php';

include 'vendor/autoload.php';

$itemManager = new ItemManager($database);
$database->Debug();
$page = $argv[1];

if (isset($_GET['a']) && isset($_GET['s']))
{
    if ($_GET['s'] == "iz6JbTdzAxQWL6bFjtn5NfNAZfA5FGodWAY23zi3gtzEvNdUS6nGBTRwE6UtJd2W")
    {
        $page = $_GET['a'];
    }
}

if ($page == 'logout')
{
    echo 'Logging out users which were inactive for more than 60 minutes<br/>';
    $timeOut = 60;
    $where = 'session != "" AND TIMESTAMPDIFF(MINUTE, lastaction, NOW()) < ' . $timeOut;
    // $database->Update('session=""', 'accounts',  $where, 999999999999);
}
else if ($page == "check")
{
    $attackManager = new AttackManager($database);
    $result = $database->Select("id,name,attacks,pfad,pfad2", "accounts");
    if ($result)
    {
        while ($row = $result->fetch_assoc())
        {
            $attacks = explode(";", $row['attacks']);
            foreach ($attacks as $pattack)
            {
                $attack = $attackManager->GetAttack($pattack);
                if ($attack)
                {
                    $attackresult = $database->Select('type', 'skilltree', 'attack=' . $pattack);
                    if ($attackresult)
                    {
                        $attackrow = $attackresult->fetch_assoc();
                        if ($attackrow['type'] == 1 && $row['pfad'] != 'Zoan')
                            echo "<span style='color:red'>" . $row['name'] . " - Attack: " . $attack->GetName() . "</span><br/>";
                        if ($attackrow['type'] == 2 && $row['pfad'] != 'Paramecia')
                            echo "<span style='color:red'>" . $row['name'] . " - Attack: " . $attack->GetName() . "</span><br/>";
                        if ($attackrow['type'] == 3 && $row['pfad'] != 'Logia')
                            echo "<span style='color:red'>" . $row['name'] . " - Attack: " . $attack->GetName() . "</span><br/>";
                        if ($attackrow['type'] == 4 && $row['pfad2'] != 'Schwertkaempfer')
                            echo "<span style='color:red'>" . $row['name'] . " - Attack: " . $attack->GetName() . "</span><br/>";
                        if ($attackrow['type'] == 5 && $row['pfad2'] != 'Schwarzfuss')
                            echo "<span style='color:red'>" . $row['name'] . " - Attack: " . $attack->GetName() . "</span><br/>";
                        if ($attackrow['type'] == 6 && $row['pfad2'] != 'Karatekämpfer')
                            echo "<span style='color:red'>" . $row['name'] . " - Attack: " . $attack->GetName() . "</span><br/>";
                    }
                }
            }
        }
    }
}
else if ($page == 'usergift')
{
    if (isset($_GET['item']) && isset($_GET['amount']) && isset($_GET['user']))
    {
        echo "Sende Geschenke an User " . $_GET['user'] . "<br/>";
        $itemManager = new ItemManager($database);
        $item = $itemManager->GetItem($_GET['item']);
        if ($item)
        {
            echo "Geschenk: " . $item->GetName() . "<br/>";
            $addAmount = $_GET['amount'];
            $statstype = 0;
            $upgrade = 0;
            $resulti = $database->Select('id,amount', 'inventory', 'statsid=' . $item->GetID() . ' AND ownerid=' . $_GET['user'], 1);
            $amount = 0;
            $itemID = 0;
            if ($resulti)
            {
                $rowi = $resulti->fetch_assoc();
                $itemID = $rowi['id'];
                $amount = $rowi['amount'];
            }
            if ($itemID != 0)
            {
                $amount = $amount + $addAmount;
                $database->Update('amount=' . $amount, 'inventory', 'id=' . $itemID, 1);
            }
            else
            {
                $database->Insert('statsid,visualid,ownerid,amount,statstype,upgrade', $item->GetID() . ', ' . $item->GetID() . ', ' . $_GET['user'] . ', ' . $addAmount . ', ' . $statstype . ', ' . $upgrade, 'inventory');
            }
        }
    }
}
else if ($page == 'statreset')
{
    if (isset($_GET['amount']))
    {
        echo "Sende Stat Reset an alle User ohne Stat Reset Item<br/>";
        $itemManager = new ItemManager($database);
        $item = $itemManager->GetItem(9);
        if ($item)
        {
            $result = $database->Select('id', 'accounts');
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    while ($row = $result->fetch_assoc())
                    {
                        $addAmount = $_GET['amount'];
                        $resulti = $database->Select('id,amount', 'inventory', 'statsid=' . $item->GetID() . ' AND ownerid=' . $row['id'], 1);
                        $rowi = array();
                        if ($resulti)
                        {
                            $rowi = $resulti->fetch_assoc();
                        }
                        if (COUNT($rowi) == 0)
                        {
                            $database->Insert('statsid,visualid,ownerid,amount,statstype,upgrade', '9, 9, ' . $row['id'] . ', ' . $addAmount . ', 0, 0', 'inventory');
                        }
                    }
                }
            }
        }
    }
}
else if ($page == 'gift')
{
    if (isset($_GET['item']) && isset($_GET['amount']))
    {
        echo "Sende Geschenke an alle User<br/>";
        $itemManager = new ItemManager($database);
        $item = $itemManager->GetItem($_GET['item']);
        if ($item)
        {
            echo "Geschenk: " . $item->GetName() . "<br/>";
            $result = $database->Select('id', 'accounts');
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    while ($row = $result->fetch_assoc())
                    {
                        $addAmount = $_GET['amount'];
                        $statstype = 0;
                        $upgrade = 0;
                        $resulti = $database->Select('id,amount', 'inventory', 'statsid=' . $item->GetID() . ' AND ownerid=' . $row['id'], 1);
                        $amount = 0;
                        $itemID = 0;
                        if ($resulti)
                        {
                            $rowi = $resulti->fetch_assoc();
                            $itemID = $rowi['id'];
                            $amount = $rowi['amount'];
                        }
                        if ($itemID != 0)
                        {
                            $amount = $amount + $addAmount;
                            $database->Update('amount=' . $amount, 'inventory', 'id=' . $itemID, 1);
                        }
                        else
                        {
                            $database->Insert('statsid,visualid,ownerid,amount,statstype,upgrade', $item->GetID() . ', ' . $item->GetID() . ', ' . $row['id'] . ', ' . $addAmount . ', ' . $statstype . ', ' . $upgrade, 'inventory');
                        }
                    }
                }
            }
        }
    }
}
else if ($page == 'clanupdate')
{
    echo 'Doing Clan Update<br/>';

    $database->Update('yesterdayfights=dailyfights, challengefight=0', 'accounts', '', 999999999999);


    $result = $database->Select('*', 'clans', '', 99999999);
    if ($result)
    {
        if ($result->num_rows > 0)
        {
            while ($row = $result->fetch_assoc())
            {
                if ($row['members'] >= 3)
                {
                    $mresult = $database->Select('yesterdayfights', 'accounts', 'clan="' . $row['id'] . '" AND arank=0 AND banned=0 AND deleted=0 AND bookingdays=0');
                    $activity = 0;
                    while ($mrow = $mresult->fetch_assoc())
                    {
                        $activity += round($mrow['yesterdayfights'] / 10);
                    }
                    echo $activity . "<br/>";
                    $memberki = $row["memberki"] + $activity;
                    $activitypoints = $activity + $row["activitypoints"];
                    $cresult = $database->Update('memberki=' . $memberki . ', activitypoints=' . $activitypoints, 'clans', 'id=' . $row['id']);

                }
            }
        }
    }
}
else if ($page == 'update')
{
    echo 'Doing Update<br/>';
    $database->Truncate('lastfights');
    $database->Copy('fights', 'lastfights');
    $database->Truncate('fights');
    $database->Truncate('fighters');
    $database->Truncate('arenafighter');
    $database->Truncate('treasurehuntprogress');
    // Sportwetten auszahlen
    $datums = date('jmY');
    $TeilnehmerCheck = $database->Select('*', 'fussballwetten', 'datum="'.$datums.'" AND ausgezahlt=0', 999999);
    if($TeilnehmerCheck)
    {
        $spieler = $TeilnehmerCheck->fetch_assoc();
        $teilnehmer = explode(';', $spieler['teilnehmer']);
        $i = 0;
        $ergebnis = $spieler['ergebnis'];
        while(isset($teilnehmer[$i]))
        {
            $ergebnisse = $teilnehmer[$i][2].''.$teilnehmer[$i][3].''.$teilnehmer[$i][4];
            $id = $teilnehmer[$i][0];
            if(is_numeric($teilnehmer[$i][1]) && !is_numeric($teilnehmer[$i][2]))
            {
                $id = $id.''.$teilnehmer[$i][1];
                $ergebnisse = $teilnehmer[$i][3].''.$teilnehmer[$i][4].''.$teilnehmer[$i][5];
            }
            else if(is_numeric($teilnehmer[$i][1]) && is_numeric($teilnehmer[$i][2]) && !is_numeric($teilnehmer[$i][3]))
            {
                $id = $id.''.$teilnehmer[$i][1].''.$teilnehmer[$i][2];
                $ergebnisse = $teilnehmer[$i][4].''.$teilnehmer[$i][5].''.$teilnehmer[$i][6];
            }
            else if(is_numeric($teilnehmer[$i][1]) && is_numeric($teilnehmer[$i][2]) && is_numeric($teilnehmer[$i][3]) && !is_numeric($teilnehmer[$i][4]))
            {
                $id = $id.''.$teilnehmer[$i][1].''.$teilnehmer[$i][2].''.$teilnehmer[$i][3];
                $ergebnisse = $teilnehmer[$i][5].''.$teilnehmer[$i][6].''.$teilnehmer[$i][7];
            }
            if($spieler['ergebnis'] == $ergebnisse)
            {
                $gamer = new Player($database, $id);
                $alleteilnehmer = count($teilnehmer) - 1;
                $premie = round($spieler['einsatz'] / $alleteilnehmer);
                $gamer->SetBerry($gamer->GetBerry() + $premie);


            }
            $i++;
        }
        $database->Update('ausgezahlt=1', 'fussballwetten', 'id="'.$spieler['id'].'"', 1);
    }
// Ende
    // Zahle Belohnung für TOP 5 aus Sload
    $seefive = $database->Select('*', 'statslist', 'type=-1', 5, 'dailywin', 'DESC');
    if($seefive)
    {
        while($five = $seefive->fetch_assoc())
        {
            $player = new Player($database, $five['acc']);
            $player->AddItems(8, 8, 1);
            $player->SetBerry($player->GetBerry() + 5000);
            $pm = new PMManager($database, $player->GetID());
            $text = 'Glückwunsch! Du bist heute in den Top 5 der meist gewonnen Kämpfe, als Dankeschön für deine Aktivität erhältst du 5000 Berry und 1x Sehr starke Medizin';
            $pm->SendPM(0, 'img/system.png', 'SYSTEM', 'Gut gemacht!', $text, $player->GetName(), 1);
        }
    }
    // Ende

    $database->Update('placeandtime="3;0;1:2:3:4:5:6:7;1-31;1-12;1-365;-", displayinfo=0', 'events', 'id=29 OR id=30 OR id=31');
    $randomDungeon = rand(29,31);
    $database->Update('placeandtime="3;51;1:2:3:4:5:6:7;1-31;1-12;1-365;-", displayinfo=1', 'events', 'id='.$randomDungeon);

    // Kick die Spieler aus der Bande
    $result = $database->Select('*', 'accounts', 'leaveclan=1');
    if($result)
    {
        while($row = $result->fetch_assoc())
        {
            $bande = new Clan($database, $row['clan']);
            $bande->PlayerLeaves();
            $database->Update('clan=0, clanname="", leaveclan=0', 'accounts', 'id="'.$row['id'].'"');
        }
        $result->close();
    }
    $day = date("w");
    if($day == 0)
    {
        $database->Update('lp=mlp, kp=mkp', 'accounts', '');
    }

    // Eingetragenes Update Check
    $datum = date("Y-m-d");
    $result = $database->Select('*', 'updates', 'time=2359');
    if($result)
    {
        while($row = $result->fetch_assoc())
        {
            if($row['datum'] == $datum || $row['datum'] == 0)
            {
                $database->Update($row['sets'], $row['bys'], $row['wheres']);
                if($row['deletes'] == 1)
                {
                    $database->Delete('updates', 'id="'.$row['id'].'"');
                }
            }
        }
        $result->close();
    }
    // Update Ende
    $tag = date("d");
    $seasonend = cal_days_in_month(CAL_GREGORIAN, date("n"), date("Y"));

    if($tag == $seasonend)
    {
        $database->Update('territorium=0,sieger="",gewinn=0,clanbonus="0000-00-00 00:00:00",blocked=0', 'places');

        $lastWinners = $database->Select('*', 'accounts', 'race="Marine"', 11, 'last_elo_rank');
        if($lastWinners)
        {
            if($lastWinners->num_rows > 0)
            {
                while($row = $lastWinners->fetch_assoc())
                {
                    $titels = explode(";", $row['titels']);
                    if(($key = array_search("84", $titels)))
                        unset($titels[$key]);
                    if(($key = array_search("85", $titels)))
                        unset($titels[$key]);
                    $titels = implode(";", $titels);
                    $titel = $row['titel'];
                    if($titel == 84 || $titel == 85)
                        $titel = 0;

                    $database->Update('titels="'.$titels.'", titel='.$titel, 'accounts', 'id='.$row['id']);
                }
            }
            $lastWinners->close();
        }

        $lastWinners = $database->Select('*', 'accounts', 'race="Pirat"', 11, 'last_elo_rank');
        if($lastWinners)
        {
            if($lastWinners->num_rows > 0)
            {
                while($row = $lastWinners->fetch_assoc())
                {
                    $titels = explode(";", $row['titels']);
                    if(($key = array_search("82", $titels)))
                        unset($titels[$key]);
                    if(($key = array_search("83", $titels)))
                        unset($titels[$key]);
                    $titels = implode(";", $titels);
                    $titel = $row['titel'];
                    if($titel == 82 || $titel == 83)
                        $titel = 0;

                    $database->Update('titels="'.$titels.'", titel='.$titel, 'accounts', 'id='.$row['id']);
                }
            }
            $lastWinners->close();
        }

        $players = new GeneralList($database, 'accounts', '*', 'rank < 51', '', 50);
        $id = 0;
        $entry = $players->GetEntry($id);
        if($entry != null) {
            while ($entry != NULL) {
                $player = new Player($database, $entry['id']);
                $PMManager = new PMManager($database, $player->GetID());
                $wins = array();
                if ($player->GetRank() >= 1 && $player->GetRank() <= 3) {
                    if ($player->GetRank() <= $player->GetLastEloRank()) {
                        // Berry
                        $Elo_Berry_Win = $player->GetBerry() + 100000;
                        // Gold
                        $Elo_Gold_Win = $player->GetGold() + 200;
                        // Items
                        $items = array(406, 87, 86, 81, 82, 350, 350, 351, 351, 352, 352, 353, 353, 410, 411, 412, 413, 414, 415);
                        // Gewinn Auszahlung
                        $i = 0;
                        while ($i < count($items)) {
                            $item = $itemManager->GetItem($items[$i]);
                            $wins[2] = array($item->GetName(), 2);
                            $player->AddItems($item, $item, 2);
                            $i++;
                        }
                        $wins[0] = 100000;
                        $wins[1] = 200;
                    } else {
                        // Berry
                        $Elo_Berry_Win = $player->GetBerry() + 50000;
                        // Gold
                        $Elo_Gold_Win = $player->GetGold() + 100;
                        // Items
                        $items = array(406, 87, 86, 81, 82, 350, 350, 351, 351, 352, 352, 353, 353, 410, 411, 412, 413, 414, 415);
                        // Gewinn Auszahlung
                        $i = 0;
                        while ($i < count($items)) {
                            $item = $itemManager->GetItem($items[$i]);
                            $wins[2] = array($item->GetName(), 1);
                            $player->AddItems($item, $item, 1);
                            $i++;
                        }
                        $wins[0] = 50000;
                        $wins[1] = 100;
                    }
                    $player->SetBerry($Elo_Berry_Win);
                    $player->SetGold($Elo_Gold_Win);
                    $wintext = "- ".$wins[0] . " Berry<br/>";
                    $wintext .= "- ".$wins[1] . " Gold<br/>";
                    foreach($wins[2] as $win)
                    {
                        $wintext .= "- ".$win[0]."x ".$win[1]."<br/>";
                    }
                    if($player->GetRace() == "Marine")
                        $wintext .= "- Titel: Admiral<br/>";
                    else
                        $wintext .= "- Titel: Yonkō<br/>";
                    $text = '<div style="text-align:center">Hallo ' . $player->GetName() . ',<br/>die Elo-Saison hat geendet, du hast Platz ' . $player->GetRank() . ' erreicht, herzlichen Glückwunsch.<br/>Du erhältst entsprechend deines erreichten Ranges folgende Belohnungen:<br/>'.$wintext.'<br/><br/>Wir hoffen das du auch in Zukunft spaß am Spiel haben wirst.<br/><br/>Dein OPBG-Team</div>';
                    $PMManager->SendPM(649, '', 'OPBG', 'Ende der Elo-Saison', $text, $player->GetName(), 1);
                    $player->SetEloTrophäe($player->GetRank());
                } else if ($player->GetRank() > 3 && $player->GetRank() < 11) {
                    if ($player->GetRank() <= $player->GetLastEloRank()) {
                        // Berry
                        $Elo_Berry_Win = $player->GetBerry() + 60000;
                        // Gold
                        $Elo_Gold_Win = $player->GetGold() + 150;
                        // Items
                        $items = array(81, 82, 350, 351, 352, 353, 412);
                        // Gewinn Auszahlung
                        $i = 0;
                        while ($i < count($items)) {
                            $item = $itemManager->GetItem($items[$i]);
                            $wins[2] = array($item->GetName(), 4);
                            $player->AddItems($item, $item, 4);
                            $i++;
                        }
                        $wins[0] = 60000;
                        $wins[1] = 150;

                    } else {
                        // Berry
                        $Elo_Berry_Win = $player->GetBerry() + 30000;
                        // Gold
                        $Elo_Gold_Win = $player->GetGold() + 75;
                        // Items
                        $items = array(81, 82, 350, 351, 352, 353, 412);
                        // Gewinn Auszahlung
                        $i = 0;
                        while ($i < count($items)) {
                            $item = $itemManager->GetItem($items[$i]);
                            $wins[2] = array($item->GetName(), 2);
                            $player->AddItems($item, $item, 2);
                            $i++;
                        }
                        $wins[0] = 30000;
                        $wins[1] = 75;
                    }
                    $player->SetBerry($Elo_Berry_Win);
                    $player->SetGold($Elo_Gold_Win);
                    $wintext = "- ".$wins[0] . " Berry<br/>";
                    $wintext .= "- ".$wins[1] . " Gold<br/>";
                    foreach($wins[2] as $win)
                    {
                        $wintext .= "- ".$win[0]."x ".$win[1]."<br/>";
                    }
                    if($player->GetRace() == "Marine")
                        $wintext .= "- Titel: Vizeadmiral<br/>";
                    else
                        $wintext .= "- Titel: Shichibukai<br/>";
                    $text = '<div style="text-align:center">Hallo ' . $player->GetName() . ',<br/>die Elo-Saison hat geendet, du hast Platz ' . $player->GetRank() . ' erreicht, herzlichen Glückwunsch.<br/>Du erhältst entsprechend deines erreichten Ranges folgende Belohnungen:<br/>'.$wintext.'<br/><br/>Wir hoffen das du auch in Zukunft spaß am Spiel haben wirst.<br/><br/>Dein OPBG-Team</div>';
                    $PMManager->SendPM(649, '', 'OPBG', 'Ende der Elo-Saison', $text, $player->GetName(), 1);
                    $player->SetEloTrophäe($player->GetRank());
                } else if ($player->GetRank() > 10 && $player->GetRank() < 51) {

                    if ($player->GetRank() <= $player->GetLastEloRank()) {
                        // Berry
                        $Elo_Berry_Win = $player->GetBerry() + 30000;
                        // Gold
                        $Elo_Gold_Win = $player->GetGold() + 50;
                        // Items
                        $items = array(81, 82, 350, 351, 352, 353);
                        // Gewinn Auszahlung
                        $i = 0;
                        while ($i < count($items)) {
                            $item = $itemManager->GetItem($items[$i]);
                            $wins[2] = array($item->GetName(), 2);
                            $player->AddItems($item, $item, 2);
                            $i++;
                        }
                        $wins[0] = 30000;
                        $wins[1] = 50;
                    } else {
                        // Berry
                        $Elo_Berry_Win = $player->GetBerry() + 15000;
                        // Gold
                        $Elo_Gold_Win = $player->GetGold() + 25;
                        // Items
                        $items = array(81, 82, 350, 351, 352, 353);
                        // Gewinn Auszahlung
                        $i = 0;
                        while ($i < count($items)) {
                            $item = $itemManager->GetItem($items[$i]);
                            $wins[2] = array($item->GetName(), 1);
                            $player->AddItems($item, $item, 1);
                            $i++;
                        }
                        $wins[0] = 15000;
                        $wins[1] = 25;
                    }
                    $player->SetBerry($Elo_Berry_Win);
                    $player->SetGold($Elo_Gold_Win);
                    $wintext = "- ".$wins[0] . " Berry<br/>";
                    $wintext .= "- ".$wins[1] . " Gold<br/>";
                    foreach($wins[2] as $win)
                    {
                        $wintext .= "- ".$win[0]."x ".$win[1]."<br/>";
                    }
                    $text = '<div style="text-align:center">Hallo ' . $player->GetName() . ',<br/>die Elo-Saison hat geendet, du hast Platz ' . $player->GetRank() . ' erreicht, herzlichen Glückwunsch.<br/>Du erhältst entsprechend deines erreichten Ranges folgende Belohnungen:<br/>'.$wintext.'<br/><br/>Wir hoffen das du auch in Zukunft spaß am Spiel haben wirst.<br/><br/>Dein OPBG-Team</div>';
                    $PMManager->SendPM(649, '', 'OPBG', 'Ende der Elo-Saison', $text, $player->GetName(), 1);
                    $player->SetEloTrophäe($player->GetRank());
                }
                $clans = new Clan($database, $player->GetClan());
                $clans->SetTourFinishTournmanet(0);
                $clans->SetGoldTournmanet(0);
                $clans->SetBerryTournmanet(0);
                $clans->SetTestoTournmanet(0);
                $clans->SetVitaTournmanet(0);
                $clans->SetRedTournmanet(0);
                $clans->SetOrangeTournmanet(0);
                $clans->SetRunningPoints(0);
                $id++;
                $entry = $players->GetEntry($id);
            }
        }
        $database->Update('elopoints=0', 'accounts', 'elopoints >= 1');
        #$database->Update('dailykopfgeldmax=5, dailyelofightsmax=5, dailynpcfightsmax=50', dailyarenapoints=0, 'accounts', 'dailykopfgeldmax > 5');
        $database->Update('last_elo_rank=rank', 'accounts');

        $lastWinners = $database->Select('*', 'accounts', 'race="Marine"', 11, 'last_elo_rank');
        if($lastWinners)
        {
            if($lastWinners->num_rows > 0)
            {
                $i = 1;
                while($row = $lastWinners->fetch_assoc())
                {
                    $titels = explode(";", $row['titels']);
                    if($i < 5) {
                        $titels[] = 85;
                    }
                    if($i > 4 && $i < 12) {
                        $titels[] = 84;
                    }
                    $titels = implode(";", $titels);
                    $i++;

                    $database->Update('titels="'.$titels.'"', 'accounts', 'id='.$row['id']);
                }
            }
            $lastWinners->close();
        }

        $lastWinners = $database->Select('*', 'accounts', 'race="Pirat"', 11, 'last_elo_rank');
        if($lastWinners)
        {
            if($lastWinners->num_rows > 0)
            {
                $i = 1;
                while($row = $lastWinners->fetch_assoc())
                {
                    $titels = explode(";", $row['titels']);
                    if($i < 5) {
                        $titels[] = 83;
                    }
                    if($i > 4 && $i < 12) {
                        $titels[] = 82;
                    }
                    $titels = implode(";", $titels);
                    $i++;

                    $database->Update('titels="'.$titels.'"', 'accounts', 'id='.$row['id']);
                }
            }
            $lastWinners->close();
        }

        $result = $database->Select('*', 'clans', '', 99999, 'memberki', 'DESC');
        if($result && $result->num_rows > 0)
        {
            $count = 1;
            while($row = $result->fetch_assoc()) {
                $topcount = $row['topcount'];
                if($count == 1)
                    $topcount += 1;
                $database->Update('topcount='.$topcount.', lastrank=' . $count, 'clans', 'id=' . $row['id']);
                ++$count;
            }
        }
        $database->Update('fpoints=0', 'clans');
        $database->Update('elotournament="0;0;0;0;0;0;0;0;0;0;0;0;0;0;0",dailymaxelofights=5', 'accounts');
    }

    $koloclosechecking = $database->Select('*', 'accounts', 'koloclose=1');
    if($koloclosechecking->num_rows >= 1)
    {
        while($kolofree = $koloclosechecking->fetch_assoc())
        {
            $database->Update('koloclose=0', 'accounts', 'id="'.$kolofree['id'].'"');
        }
    }

    $warncheck = $database->Select('*', 'warnings', 'active=1 AND expire=1');
    if($warncheck && $warncheck->num_rows > 0)
    {
        while($row = $warncheck->fetch_assoc())
        {
            $now = time(); // or your date as well
            $warndate = strtotime($row['expires']);
            $count = round(($warndate-$now)/60/60/24);
            if($count <= 0)
            {
                $database->Update('active=0', 'warnings', 'id='.$row['id']);
            }
        }
    }

    $database->Delete('adminlog', ' time < NOW() - INTERVAL 60 DAY');
    $database->Delete('meldungen', ' date < NOW() - INTERVAL 60 DAY');

    $result = $database->Select('id, debuglog, sitter, sitterstart, sitterend', 'accounts');
    if ($result)
    {
        if ($result->num_rows)
        {
            while ($row = $result->fetch_assoc())
            {
                if($row['debuglog'] != '')
                    $database->Insert('time, playerid, log', 'CURRENT_TIMESTAMP(), ' . $row['id'] .', "'.$row['debuglog'].'"', 'logs');

                if($row['sitter'] != 0)
                {
                    if($row['sitterend'] == date("Y-m-d"))
                    {
                        $database->Update('sitter=0,sitterstart="0000-00-00",sitterend="0000-00-00",session=""', 'accounts', 'id='.$row['id']);
                    }
                }
            }
        }
    }

    $database->Update('bookingdays=bookingdays-1', 'accounts', 'bookingdays > 0');
    $database->Update('dailyarenapoints=500', 'accounts', 'dailyarenapoints < 500');
    $database->Update('vivrecard=0', 'accounts', 'vivrecard > 0');
    #$database->Update('münzen=0, orange1=0, orange2=0, orange3=0', 'accounts', 'münzen > 0 OR orange1 > 0 OR orange2 > 0 OR orange3 > 0'); Sommer Event
    /*$result = $database->Select('dailyarenapoints, collectedkolopoints, id', 'accounts', 'dailyarenapoints > 500');
    if($result && $result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            $dailypoints = $row['dailyarenapoints'];
            $collected = $row['collectedkolopoints'];
            $new_kolopoints = ((500-$collected)+$dailypoints);
            $database->Update('dailyarenapoints='.$new_kolopoints, 'accounts', 'id='.$row['id']);
        }
    }*/
    $database->Update('collectedkolopoints=0, fight=0, yesterdayfights=dailyfights, clanelofights=0, challengefight=0, debuglog="", assignedstats=0, arenakickcount=0, clickcount=0, totalclickcount=0', 'accounts', 'bookingdays=0', 999999999999);
    $database->Update('finishedplayers=""', 'events', 'dailyreset="1"', 999999999999);
    $database->Update('dailyeloenemys=""', 'accounts');

    $result = $database->Select('*', 'clans', '', 99999999);
    if ($result)
    {
        if ($result->num_rows > 0)
        {
            while ($row = $result->fetch_assoc())
            {
                if ($row['members'] >= 3)
                {
                    $mresult = $database->Select('id, dailyfights, dailynpcfights, dungeon, dailyelofights', 'accounts', 'clan="' . $row['id'] . '" AND arank=0 AND banned=0 AND deleted=0 AND bookingdays=0');
                    $activity = 0;
                    $exp = 0;
                    while ($mrow = $mresult->fetch_assoc())
                    {
                        $activity += round(min($mrow['dailyfights'], 5) / 10);
                        if($mrow['dailyfights'] >= 5)
                            $exp += 5;
                        if($mrow['dailynpcfights'] >= 50)
                            $exp += 3;
                        if($mrow['dungeon'] >= 10)
                            $exp += 5;
                        if($mrow['dailyelofights'] >= 5)
                            $exp += 5;
                    }
                    echo $activity . "<br/>";
                    $memberki = $row["memberki"] + $activity + $exp;
                    $activitypoints = $activity + $row["activitypoints"];
                    $cresult = $database->Update('memberki=' . $memberki . ', activitypoints=' . $activitypoints.', exp= exp + '.$exp, 'clans', 'id=' . $row['id']);
                }
            }
        }
    }

    $database->Update('dailyfights=0, dailynpcfights=0', 'accounts', '', 999999999999);

    $titelManager = new titelManager($database);
    foreach ($titelManager->GetTitels() as $titel)
    {
        if ($titel->GetType() != 5)
            continue;

        $where = '';
        $sort = $titel->GetSort();

        switch ($sort)
        {
            case 0:
                $sort = 'win';
                break;
            case 1:
                $sort = 'loose';
                break;
            case 2:
                $sort = 'draw';
                break;
            case 3:
                $sort = 'total';
                break;
            case 4:
                $sort = 'dailywin';
                break;
            case 5:
                $sort = 'dailyloose';
                break;
            case 6:
                $sort = 'dailydraw';
                break;
            case 7:
                $sort = 'dailytotal';
                break;
        }

        $type = $titel->GetFight();

        $where = 'type="' . $type . '" AND ' . $sort . ' != 0';
        $list = new Generallist($database, 'statslist', '*', $where, $sort, ($titel->GetCondition() - 1) . ',' . $titel->GetCondition(), 'DESC');
        $id = 0;
        $entry = $list->GetEntry($id);
        if ($entry != null)
        {
            echo $entry['name'] . ' (' . $entry['acc'] . ') erhält mit ' . $entry[$sort] . ': ' . $titel->GetName() . '<br/>';
            $result = $database->Select('*', 'accounts', 'id=' . $entry['acc'], 1);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $row = $result->fetch_assoc();
                    if ($row['titels'] == '')
                        $titels = array();
                    else
                        $titels = explode(';', $row['titels']);
                    if (in_array($titel->GetID(), $titels))
                    {
                        echo ' - hat aber den Titel schon....<br/>';
                    }
                    else
                    {
                        $titels[] = $titel->GetID();
                        $titels = implode(';', $titels);
                        $database->Update('titels="' . $titels . '"', 'accounts', 'id=' . $entry['acc'], 1);

                        echo ' - erfolgreich hinzugefügt.<br/>';
                    }
                }
                $result->close();
            }
        }
    }

    $database->Update('dailywin="0", dailyloose="0", dailydraw="0", dailytotal="0"', 'statslist', '', 999999999999);

    $result = $database->Select('*', 'market', 'dauer < NOW()');
    if ($result)
    {
        if ($result->num_rows)
        {
            while ($row = $result->fetch_assoc())
            {
                $item = $itemManager->GetItem($row['statsid']);
                if($row['bieter'] == 0)
                {
                    $result2 = $database->Select('*', 'inventory', 'statsid=' . $row['statsid'].' AND visualid=' . $row['visualid']. ' AND ownerid='.$row['sellerid']);
                    if($result2 && $result2->num_rows != 0 && $item->GetType() != 3 && $item->GetType() != 4)
                    {
                        $database->Update('amount=amount + ' . $row['amount'], 'inventory', 'statsid='.$row['statsid'].' AND visualid='.$row['visualid'].' AND ownerid='.$row['sellerid']);
                    }
                    else
                    {
                        $database->Insert(
                            'statsid, visualid, ownerid, amount, statstype, upgrade, formerowners',
                            '"' . $row['statsid'] . '","' . $row['visualid'] . '","' . $row['sellerid'] . '","' . $row['amount'] . '","' . $row['statstype'] . '","' . $row['upgrade'] . '","' . $row['formerowners'] . '"',
                            'inventory'
                        );
                    }
                    $PMManager = new PMManager($database, $row['sellerid']);
                    $text = "<div style='text-align:center;'><img src='img/items/" . $item->GetImage() . ".png' width='80px' height='80px' alt='itemimage'><br/>Du hast <b>" . number_format($row['amount'],'0', '', '.') . "x " . $item->GetName() . '</b> leider nicht verkaufen können, daher hast du es zurück erhalten.</div>';
                    $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', $item->GetName() . ' kam zurück.', $text, $row['seller'], 1);
                }
                else
                {
                    $result2 = $database->Select('*', 'inventory', 'statsid=' . $row['statsid'].' AND visualid=' . $row['visualid']. ' AND ownerid='.$row['bieter']);
                    if($result2 && $item->GetType() != 2 && $item->GetType() != 3)
                    {
                        $database->Update('amount=amount + ' . $row['amount'], 'inventory', 'statsid='.$row['statsid'].' AND visualid='.$row['visualid'].' AND ownerid='.$row['bieter']);
                    }
                    else
                    {
                        $database->Insert(
                            'statsid, visualid, ownerid, amount, statstype, upgrade, formerowners',
                            '"' . $row['statsid'] . '","' . $row['visualid'] . '","' . $row['bieter'] . '","' . $row['amount'] . '","' . $row['statstype'] . '","' . $row['upgrade'] . '","' . $row['formerowners'] . '"',
                            'inventory'
                        );
                    }

                    $PMManager = new PMManager($database, $row['bieter']);
                    if($item->IsPremium())
                        $waehrung = '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>';
                    else
                        $waehrung = '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
                    $result3 = $database->Select('*', 'accounts', 'id='.$row['bieter']);
                    if($result3) {
                        $row2 = $result3->fetch_assoc();
                        $bietername = $row2['name'];
                        $text = "<img src='img/items/" . $item->GetImage() . ".png' width='80px' height='80px' alt='itemimage'> Du hast " . number_format($row['amount'], '0', '', '.') . "x " . $item->GetName() . " für " . number_format($row['gebot'], '0', '', '.') . " " . $waehrung . " von <a href='?p=profil&id=" . $row['sellerid'] . "'>" . $row['seller'] . "</a> ersteigert.";
                        $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', $item->GetName() . ' wurde ersteigert.', $text, $bietername, 1);

                        if ($item->IsPremium())
                            $database->Update('gold=gold+' . $row['price'], 'accounts', 'id=' . $row['sellerid']);
                        else
                            $database->Update('zeni=zeni+' . $row['price'], 'accounts', 'id=' . $row['sellerid']);
                        $PMManager = new PMManager($database, $row['sellerid']);
                        $text = "<img src='img/items/" . $item->GetImage() . ".png' width='80px' height='80px' alt='itemimage'> Du hast " . number_format($row['amount'], '0', '', '.') . "x " . $item->GetName() . " für " . number_format($row['gebot'], '0', '', '.') . " " . $waehrung . " an <a href='?p=profil&id=" . $row['bieter'] . "'>" . $bietername . "</a> versteigert.";
                        $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', $item->GetName() . ' wurde versteigert.', $text, $row['seller'], 1);
                    }
                }
                $database->Delete('market', 'id='.$row['id']);
            }
        }
    }

    $result = $database->Select('*', 'tournaments', 'id=3');
    if($result)
    {
        if($result->num_rows > 0)
        {
            $tournamentStart = strtotime($row['starttime']);
            $currentTime = strtotime("now");
            if($currentTime >= $tournamentStart)
            {
                $newTournamentStart = $tournamentStart + 604800;
                $newDateTime = date("d.m.Y H:i:s", $newTournamentStart);
                $database->Update('starttime="'.$newDateTime.'"', 'tournaments', 'id=3');
            }
        }
    }
}
else if($page == 'checkEquippedStats')
{
    $result = $database->Select('id, name, equippedstats', 'accounts', 'arank = 0');

    if($result)
    {
        if($result->num_rows > 0)
        {
            $EquippedStats = array();
            $i = 0;
            while($row = $result->fetch_assoc())
            {
                $player = new Player($database, $row['id']);
                $items = $player->GetInventory()->GetItems();
                foreach($items as $item) {
                    if(($item->GetLP() != 0 || $item->GetKP() != 0 || $item->GetAttack() != 0 || $item->GetDefense() != 0) && $item->IsEquipped() && ($item->GetType() == 3 || $item->GetType() == 4)) {
                        $EquippedStats[$i][0] = $row['name'];
                        $EquippedStats[$i][1] += $item->GetLP();
                        $EquippedStats[$i][2] += $item->GetKP();
                        $EquippedStats[$i][3] += $item->GetAttack();
                        $EquippedStats[$i][4] += $item->GetDefense();
                        $EquippedStats[$i][5] = 0;
                        $EquippedStats[$i][6] = 0;
                        $EquippedStats[$i][7] = $row['equippedstats'];
                    }
                }
                if(!empty($EquippedStats[$i]))
                    $i++;
            }
            foreach($EquippedStats as $stats) {
                if ($stats[7] != $stats[1] . ";" . $stats[2] . ";" . $stats[3] . ";" . $stats[4] . ";" . $stats[5] . ";" . $stats[6])
                    echo "LALALALALA ".$stats[7] . " - " . $stats[0] . " - " . $stats[1] . ";" . $stats[2] . ";" . $stats[3] . ";" . $stats[4] . ";" . $stats[5] . ";" . $stats[6] . "<br/>";
            }
        }
    }
}
else if($page == 'checkTitelStats')
{
    $result = $database->Select('id, name, titelstats, titels', 'accounts', 'arank = 0');

    if($result)
    {
        if($result->num_rows > 0)
        {
            $TitelStats = array();
            $i = 0;
            while($row = $result->fetch_assoc())
            {
                $titels = explode(";", $row['titels']);
                foreach($titels as $titelID) {
                    $titelManager = new TitelManager($database);
                    $titel = $titelManager->GetTitel($titelID);
                    if($titel) {
                        if (($titel->GetLP() != 0 || $titel->GetKP() != 0 || $titel->GetAtk() != 0 || $titel->GetDef() != 0)) {
                            $TitelStats[$i][0] = $row['name'];
                            $TitelStats[$i][1] += $titel->GetLP();
                            $TitelStats[$i][2] += $titel->GetKP();
                            $TitelStats[$i][3] += $titel->GetAtk();
                            $TitelStats[$i][4] += $titel->GetDef();
                            $TitelStats[$i][5] = $row['titelstats'];
                        }
                    }
                }
                if(!empty($TitelStats[$i]))
                    $i++;
            }
            foreach($TitelStats as $stats) {
                if ($stats[5] != $stats[1] . ";" . $stats[2] . ";" . $stats[3] . ";" . $stats[4])
                    echo $stats[5] . " - " . $stats[0] . " - " . $stats[1] . ";" . $stats[2] . ";" . $stats[3] . ";" . $stats[4] . "<br/>";
            }
        }
    }
}
else if ($page == 'ranking')
{
    echo 'Doing Ranking<br/>';
    // Eingetragenes Update Check
    $datum = date("d.m.Y");
    $updatesinclude = $database->Select('*', 'updates', 'time=10');
    if($updatesinclude)
    {
        while($update = $updatesinclude->fetch_assoc())
        {
            if($update['datum'] != 0 && $update['datum'] == $datum)
            {
                $result = $database->Update($update['sets'], $update['bys'], $update['wheres']);
                if($update['deletes'] == 1)
                {
                    $results = $database->Delete('updates', 'id="'.$update['id'].'"');
                }
            }
            else if($update['datum'] == 0)
            {
                $result = $database->Update($update['sets'], $update['bys'], $update['wheres']);
                if($update['deletes'] == 1)
                {
                    $results = $database->Delete('updates', 'id="'.$update['id'].'"');
                }
            }
        }
    }
    // Update Ende
    $CheckEloPoints = $database->Select('*', 'accounts', 'elopoints > 500');
    if($CheckEloPoints)
    {
        while($elos = $CheckEloPoints->fetch_assoc())
        {
            $EloPlayer = new Player($database, $elos['id']);
            $EloPlayer->SetAllEloTournamenBonus();
            if($EloPlayer->GetFakeKI() != $EloPlayer->GetKI() && !$EloPlayer->IsDonator())
            {
                $EloPlayer->SetFakeKI($EloPlayer->GetKI());
            }
        }
    }

    $set = 'SET @counter = 0;';
    //$order = 'banned ASC, arank ASC, inranking DESC, ((((mlp / 10)+( mkp / 10) + (attack / 2) + defense) / 4) + kopfgeld)'; // Altes Ranking [30.06.2022]
    //$order = 'arank ASC, banned ASC, inranking DESC, ((((mlp / 10)+( mkp / 10) + attack + defense) / 4) + kopfgeld)'; TODO: Reupload
    $order = 'banned ASC, deleted ASC, arank ASC, elopoints';

    $result = $database->Update('rank= @counter := @counter + 1', 'accounts', '', 999999, $order, 'DESC', $set);

    $result = $database->Select('*', 'clans', '', 9999999);
    if ($result)
    {
        if ($result->num_rows > 0)
        {
            while ($row = $result->fetch_assoc())
            {
                $id = $row['id'];
                $members = $row['members'];
                $result2 = $database->Select('((mlp/10) + (mkp/10) + (attack/2) + defense)/4 as total', 'accounts', 'clan="' . $id . '" AND arank = 0 AND banned = 0 AND deleted = 0 AND bookingdays=0', 9999999);
                //$result2 = $database->Select('((mlp/10) + (mkp/10) + attack + defense)/4 as total', 'accounts', 'clan="' . $id . '" AND arank = 0 AND banned = 0', 9999999); TODO: Reupload
                $total = 0;
                $fpoints = $row['fpoints'];
                while ($row2 = $result2->fetch_assoc())
                {
                    $total += $row2['total'];
                }
                if ($members >= 3)
                {
                    //$total2 = round($total / $members) + $members + $row['gold'] + $row['activitypoints']; // Altes Bandenranking
                    $total2 = round($total / $members) + $members + $row['activitypoints'];
                }
                else
                {
                    $total2 = 0;
                }
                $result2->close();
                $result2 = $database->Select('COUNT(*) as total', 'accounts', 'clan="' . $id . '" AND arank = 0', 9999999);
                if ($row2 = $result2->fetch_assoc())
                {
                    if ($row2['total'] == 0)
                    {
                        $total2 = 0;
                    }
                }
                $result3 = $database->Select('*', 'places', 'earnable=1 AND territorium=' . $id);
                if ($result3) {
                    if ($result3->num_rows > 0) {
                        while($row3 = $result3->fetch_assoc()) {
                            // Get time difference between clan claim and now
                            $today = new DateTime("now");
                            $match_date = DateTime::createFromFormat('Y-m-d H:i:s', $row3['time']);
                            $match_date = $match_date->setTime(0, 0);
                            $diff = $today->diff($match_date);
                            $diffDays = (integer)$diff->format("%R%a");
                            echo $total2;
                            if($row3['planet'] == 1) { // East Blue
                                $total2 = $total2 + 50;
                            }
                            else if($row3['planet'] == 3) { // Grandline
                                $total2 = $total2 + 100;
                            }
                            else if($row3['planet'] == 5) { // North Blue
                                $total2 = $total2 + 50;
                            }


                            if ($diffDays != 0) {
                                if ((time() - strtotime($row3['clanbonus'])) > 3600) {
                                    if($row3['planet'] == 1)
                                        $fpoints = $fpoints + 2;
                                    else if($row3['planet'] == 3)
                                        $fpoints = $fpoints + 5;
                                    else if($row3['planet'] == 5)
                                        $fpoints = $fpoints + 2;
                                    $database->Update('clanbonus=NOW()', 'places', 'id='.$row3['id']);
                                }
                            }
                        }
                    }
                }
                $total2 = $total2 + $fpoints;
                $database->Update('fpoints='.$fpoints.', memberki='.$total2, 'clans', 'id=' . $id, 1);

                $clan = new Clan($database, $row['id']);
                $clan->SetAllTaskNPCDrop();
                $clan->SetAllTaskEloDrop();
                $clan->SetAllTaskPvPDrop();
                $clan->SetAllTaskDungeonDrop();
                $clan->SetAllTournamentDrops();
                $clan->SetAllTaskQuestDrops();
            }
        }
        $result->close();
    }

    $set = 'SET @clancounter = 0;';
    $order = 'memberki';

    $database->Update('rang= @clancounter := @clancounter + 1', 'clans', '', 999999, $order, 'DESC', $set);
    $database->Delete('pms', 'archived = 0 AND senderid = 0 AND time < DATE_SUB(NOW(), INTERVAL 2 DAY) AND receivername NOT LIKE "%SloadX%" AND receivername NOT LIKE "%ShirobakaX%" AND topic NOT LIKE "%Verwarnt%"');

    $result = $database->Select('*', 'accounts', '', 999999999);
    if($result)
    {
        while($row = $result->fetch_assoc())
        {

            $secDifference = 30*60;
            $dtObject = new DateTime($row['lastaction']);
            if($dtObject->getTimestamp() > time()-$secDifference)
            {
                if($row['stillActive'] == 0)
                    $database->Update('stillActive=1, activeSince=lastaction', 'accounts', 'id='.$row['id']);
            }
            else
            {
                if ($row['stillActive'] == 1)
                    $database->Update('stillActive=0', 'accounts', 'id=' . $row['id']);
            }


        }
    }

   # $result = $database->Select('*', 'places', 'planet=3 AND npcs LIKE "%198%"');
   # $carrotPlaces = array(1,3,4,5,6,7,8,9,16,18,19,48,20,21,22,24,25,26,27,28,29,31,32,50,51,55,56,57);
   # if($result && $result->num_rows > 0)
    #{
     #   while($row = $result->fetch_assoc())
     #   {
     #       if($row['npcs'] != '') {
     #           $npcs = explode(";", $row['npcs']);
      #          if (in_array("198", $npcs))
       #         {
        #            if (($key = array_search("198", $npcs)) !== false) {
         #               unset($npcs[$key]);
          #          }
           #         $npcs = implode(";", $npcs);
            #        if($npcs == ";")
             #           $npcs = "";
              #      $database->Update('npcs="'.$npcs.'"', 'places', 'id='.$row['id']);
               #     if (($key = array_search("198", $carrotPlaces)) !== false) {
                #        unset($carrotPlaces[$key]);
                 #   }
               # }
           # }
       # }
    #}
    #$randomPlace = rand(0, sizeof($carrotPlaces)-1);
    #$result = $database->Select('*', 'places', 'id='.$carrotPlaces[$randomPlace]);
    #if($result && $result->num_rows > 0)
    #{
    #    $row = $result->fetch_assoc();
     #   if($row['npcs'] != '')
     #       $npcs = explode(";", $row['npcs']);
      #  else
       #     $npcs = array();
       # $npcs[] = 198;
       # $npcs = implode(";", $npcs);
       # var_dump($npcs);
       # $database->Update('npcs="'.$npcs.'"', 'places', 'id='.$row['id']);
    #}
}
else if($page == 'verzeichnistitel')
{
    $result = $database->Select('*', 'accounts', '', 999999999);
    if($result)
    {
        while($row = $result->fetch_assoc())
        {
            $verzeichnis = $database->Select('*', 'verzeichnis', 'status=0 AND creator='.$row['id']);
            if($verzeichnis && $verzeichnis->num_rows > 0)
            {
                $database->Update('artikel='.$verzeichnis->num_rows, 'accounts', 'id='.$row['id']);
                $player = new Player($database, $row['id']);
                $titelManager = new TitelManager($database);
                if(!$player->HasTitel(93))
                {
                    $playerTitels = $player->GetTitels();
                    $titel = $titelManager->GetTitel(93);
                    $titelManager->AddTitelProgressSpecial($player, $playerTitels, $verzeichnis->num_rows, $titel);
                }
                if(!$player->HasTitel(94))
                {
                    $playerTitels = $player->GetTitels();
                    $titel = $titelManager->GetTitel(94);
                    $titelManager->AddTitelProgressSpecial($player, $playerTitels, $verzeichnis->num_rows, $titel);
                }
                if(!$player->HasTitel(95))
                {
                    $playerTitels = $player->GetTitels();
                    $titel = $titelManager->GetTitel(95);
                    $titelManager->AddTitelProgressSpecial($player, $playerTitels, $verzeichnis->num_rows, $titel);
                }
                if(!$player->HasTitel(96))
                {
                    $playerTitels = $player->GetTitels();
                    $titel = $titelManager->GetTitel(96);
                    $titelManager->AddTitelProgressSpecial($player, $playerTitels, $verzeichnis->num_rows, $titel);
                }
            }
        }
    }
}
else if($page == 'getlastfights')
{
    $result = $database->Select('*', 'lastfights', 'type=1');
    if($result && $result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            $gains = explode(";", $row['gainaccs']);
            $winner = explode(";", $row['winner']);
            if($gains[0] == $winner[1])
                echo "Gewinner: ".$gains[0]."<br/>";
            else
                echo "Verlierer: ".$gains[0]."<br/>";;

            if($gains[1] == $winner[1])
                echo "Gewinner: ".$gains[1]."<br/>";
            else
                echo "Verlierer: ". $gains[1]."<br/>";
            echo "<br/><br/>";
        }
    }
}
else if($page == "updateshipplaces")
{
    $result = $database->Select('*', 'places');
    if($result && $result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            $items = explode(";", $row['items']);
            if(in_array(1, $items) && !in_array(384, $items))
            {
                $items[] = 384;
            }
            if(in_array(40, $items) && !in_array(385, $items))
            {
                $items[] = 385;
            }
            $items = implode(";", $items);
            $database->Update('items="'.$items.'"', 'places', 'id='.$row['id']);
        }
    }
}
else if($page == 'clantournamentfix')
{
    $clanresult = $database->Select('*', 'clans', 'runnpoints >= 350');
    if($clanresult && $clanresult->num_rows > 0)
    {
        while($clanrow = $clanresult->fetch_assoc())
        {
            $clan = new Clan($database, $clanrow['id']);
            $userresult = $database->Select('*', 'accounts', 'clan="'.$clan->GetID().'"');
            if($userresult && $userresult->num_rows > 3)
            {
                $setTourFinished = 0;
                $setGoldFinished = 0;
                $setOrangeFinish = 0;
                $setRedFinish = 0;
                $setVitaFinish = 0;
                $setTestoFinish = 0;
                $setBerryFinished = 0;
                while($userrow = $userresult->fetch_assoc())
                {
                    $player = new Player($database, $userrow['id']);
                    if ($clan->GetRunningPoints() >= 350) {
                        if($clan->GetGoldTournament() == 0) {
                            $setGoldFinished = 1;
                            $goldwin = $player->GetGold() + 100;
                            $player->SetGold($goldwin);
                            $PMManager = new PMManager($database, $player->GetID());
                            $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 350 Punkte erhält jeder Spieler in deiner Bande 100 Gold";
                            $PMManager->SendPM(649, 'img/system.png', $clan->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                        }
                    }
                    if($clan->GetRunningPoints() >= 500)
                    {
                        if($clan->GetBerryTournament() == 0) {
                            $setBerryFinished = 1;
                            $berrywin = $player->GetBerry() + 50000;
                            $player->SetBerry($berrywin);
                            $PMManager = new PMManager($database, $player->GetID());
                            $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 500 Punkte erhält jeder Spieler in deiner Bande 50000 Berry";
                            $PMManager->SendPM(649, 'img/system.png', $clan->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                        }
                    }
                    if($clan->GetRunningPoints() >= 650) {
                        if ($clan->GetOrangeFruitTournament() == 0) {
                            $setOrangeFinish = 1;
                            $player->AddItems(87, 87, 1);
                            $PMManager = new PMManager($database, $player->GetID());
                            $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 650 Punkte erhält jeder Spieler in deiner Bande 1x das Item Seltene Orangene Frucht";
                            $PMManager->SendPM(649, 'img/system.png', $clan->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                        }
                    }
                    if($clan->GetRunningPoints() >= 850)
                    {
                        if($clan->GetRedFruitTournament() == 0)
                        {
                            $setRedFinish = 1;
                            if($player->GetID() != 318) {
                                $player->AddItems(86, 86, 1);
                                $PMManager = new PMManager($database, $player->GetID());
                                $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 850 Punkte erhält jeder Spieler in deiner Bande 1x das Item Seltene Rote Frucht";
                                $PMManager->SendPM(649, 'img/system.png', $clan->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                            }
                        }
                    }
                    if($clan->GetRunningPoints() >= 950)
                    {
                        if($clan->GetVitaTournament() == 0)
                        {
                            $setVitaFinish = 1;
                            if($player->GetID() != 318) {
                                $player->AddItems(82, 82, 1);
                                $PMManager = new PMManager($database, $player->GetID());
                                $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 950 Punkte erhält jeder Spieler in deiner Bande 1x das Item Vitamine";
                                $PMManager->SendPM(649, 'img/system.png', $clan->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                            }
                        }
                    }
                    if($clan->GetRunningPoints() >= 1150)
                    {
                        if($clan->GetTestoTournament() == 0)
                        {
                            $setTestoFinish = 1;
                            $player->AddItems(81, 81, 1);
                            $PMManager = new PMManager($database, $player->GetID());
                            $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 1150 Punkte erhält jeder Spieler in deiner Bande 1x das Item Testo Booster";
                            $PMManager->SendPM(649, 'img/system.png', $clan->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                        }
                    }
                    if($clan->GetRunningPoints() >= 1350)
                    {
                        if($clan->GetTourFinishTournament() == 0)
                        {
                            $setTourFinished = 1;
                            $player->SetEloPoints($player->GetEloPoints() + 100);
                            $PMManager = new PMManager($database, $player->GetID());
                            $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 1350 Punkte erhält jeder Spieler in deiner Bande 100 Elopunkte";
                            $PMManager->SendPM(649, 'img/system.png', $clan->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                        }
                    }
                }
                if($setGoldFinished)
                    $clan->SetGoldTournmanet(1);
                if($setBerryFinished)
                    $clan->SetBerryTournmanet(1);
                if($setOrangeFinish)
                    $clan->SetOrangeTournmanet(1);
                if($setRedFinish)
                    $clan->SetRedTournmanet(1);
                if($setVitaFinish)
                    $clan->SetVitaTournmanet(1);
                if($setTestoFinish)
                    $clan->SetTestoTournmanet(1);
                if($setTourFinished)
                    $clan->SetTourFinishTournmanet(1);
            }
        }
    }
}
else if($page == "AddDonatorForChristmas")
{
    $players = $database->Select('*', 'accounts', 'banned = 0');
    if($players && $players->num_rows > 0)
    {
        while($row = $players->fetch_assoc())
        {
            $target = new Player($database, $row['id']);
            if($target->IsValid())
            {
                if(!$target->IsDonator())
                {
                    $target->SetDonator(1);
                }
            }
        }
    }
}
else if($page == "ClanDailyRewards")
{
    if(!isset($_GET['clan'])) {
        echo "Es wurde kein Clan ausgewählt.";
        return;
    }
    $clan = new Clan($database, $_GET['clan']);
    if(!$clan->IsValid()) {
        echo "Der angegebene Clan ist ungültig.";
        return;
    }

    if(!isset($_GET['ra'])) {
        echo "Es wurde keine Belohnung ausgewählt.";
        return;
    }

    if($_GET['ra'] == "npc")
        $clan->SetAllTaskNPCDrop(1);

    else if($_GET['ra'] == "elo")
        $clan->SetAllTaskEloDrop(1);

    else if($_GET['ra'] == "pvp")
        $clan->SetAllTaskPvPDrop(1);

    else if($_GET['ra'] == "dungeon")
        $clan->SetAllTaskDungeonDrop(1);

    else if($_GET['ra'] == "all")
    {
        $clan->SetAllTaskQuestDrops(1);
    }
}
else if($page == "UpdateStatsList")
{
    $result = $database->Select('*', 'accounts', 'banned != 0 OR arank != 0 OR deleted != 0');
    if($result && $result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            $database->Update('igentry=1', 'statslist', 'acc='.$row['id']);
        }
    }
}
else if($page == "ShiroDebug")
{
    /*$clans = array(38);
    foreach($clans as $clan)
    {
        $clan = new Clan($database, $clan);
        if($clan->IsValid())
        {
            $clan->SetAllTaskQuestDrops(1);
        }
    }*/

    $result = $database->Select('*', 'story', 'type=2');
    if($result && $result->num_rows > 0)
    {
        $npclist = array();
        while($row = $result->fetch_assoc())
        {
            if($row['npcs'] != '') {
                $npcs = explode(";", $row['npcs']);
                foreach($npcs as $npc)
                {
                    $npcresult = $database->Select('*', 'npcs', 'id='.$npc);
                    if($npcresult && $npcresult->num_rows > 0)
                    {
                        if(!in_array($npc, $npclist)) {
                            $npclist[] = $npc;
                            $npcrow = $npcresult->fetch_assoc();
                            $database->Insert(
                                'name,
                        image,
                        description,
                        douriki,
                        lp,
                        mlp,
                        kp,
                        mkp,
                        attack,
                        defense,
                        critchance,
                        critdamage,
                        accuracy,
                        reflex,
                        attacks,
                        zeni,
                        gold,
                        items,
                        needitems,
                        survivalrounds,
                        survivalteam,
                        survivalwinner,
                        race,
                        healthratio,
                        healthratioteam,
                        healthratiowinner,
                        playerattack,
                        overrideattacks,
                        patterns,
                        level,
                        isstatsprocentual,
                        maxenemy,
                        trainerneeditem,
                        traineritemamount,
                        münzen,
                        iskolo',
                                '"Duellturm ' . $npcrow["name"] . '",
                        "' . $npcrow["image"] . '",
                        "' . $npcrow["description"] . '",
                        ' . $npcrow["douriki"] . ',
                        ' . $npcrow["lp"] . ',
                        ' . $npcrow["mlp"] . ',
                        ' . $npcrow["kp"] . ',
                        ' . $npcrow["mkp"] . ',
                        ' . $npcrow["attack"] . ',
                        ' . $npcrow["defense"] . ',
                        ' . $npcrow["critchance"] . ',
                        "' . $npcrow["critdamage"] . '",
                        ' . $npcrow["accuracy"] . ',
                        ' . $npcrow["reflex"] . ',
                        "' . $npcrow["attacks"] . '",
                        ' . $npcrow["zeni"] . ',
                        ' . $npcrow["gold"] . ',
                        "' . $npcrow["items"] . '",
                        "' . $npcrow["needitems"] . '",
                        ' . $npcrow["survivalrounds"] . ',
                        ' . $npcrow["survivalteam"] . ',
                        ' . $npcrow["survivalwinner"] . ',
                        "' . $npcrow["race"] . '",
                        ' . $npcrow["healthratio"] . ',
                        ' . $npcrow["healthratioteam"] . ',
                        ' . $npcrow["healthratiowinner"] . ',
                        "' . $npcrow["playerattack"] . '",
                        ' . $npcrow["overrideattacks"] . ',
                        "' . $npcrow["patterns"] . '",
                        ' . $npcrow["level"] . ',
                        ' . $npcrow["isstatsprocentual"] . ',
                        ' . $npcrow["maxenemy"] . ',
                        ' . $npcrow["trainerneeditem"] . ',
                        ' . $npcrow["traineritemamount"] . ',
                        ' . $npcrow["münzen"] . ',
                        ' . $npcrow["iskolo"],
                                'npcs',
                            );
                        }
                    }
                }
            }
        }
    }
}