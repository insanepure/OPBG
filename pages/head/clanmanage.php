<?php

$title = 'Bandenverwaltung';
if (!isset($clan) && $player->GetArank() < 2)
{
    header('Location: ?p=news');
    exit();
}
if(isset($_GET['id']) && $player->GetArank() >= 2 && $player->GetClan() != $_GET['id'])
{
    $clan = new Clan($database, $_GET['id']);
}
$ranks = explode('@',$clan->GetRanks());

include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
$player->UpdateLastTimeClanVisit();

if (isset($_GET['a']) && $_GET['a'] == 'post' && isset($_POST['shoutboxtext']) && $_POST['shoutboxtext'] != '')
{
    $text = htmlentities($database->EscapeString($_POST['shoutboxtext']));
    if ($database->HasBadWords($text) || strpos($text, '&quot;') !== false)
    {
        $message = 'Der Text enthält ungültige Wörter.';
    }
    else
    {
        $clan->PostShoutbox($player->GetID(), $player->GetName(), $text);
        $player->UpdateLastTimeClanVisit();
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'rundmail')
{
    $id = $player->GetID();
    $image = $player->GetImage();
    $name = $player->GetName();
    $text = $database->EscapeString($_POST['text']);
    $title = $database->EscapeString($_POST['title']);
    if ($text == '')
    {
        $message = 'Der Text ist leer.';
    }
    else if ($title == '')
    {
        $message = 'Der Betreff ist leer.';
    }
    else if(!$clan->GetRankPermission($player->GetClanRank(), "massmail"))
    {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else
    {
        $i = 0;
        $list = new Generallist($database, 'accounts', 'name, clan', 'clan="' . $clan->GetID() . '"', '', 999999, 'ASC');
        $entry = $list->GetEntry($i);
        while ($entry != null)
        {
            $PMManager->SendPM($id, $image, $name, $title, $text, $entry['name']);
            $i++;
            $entry = $list->GetEntry($i);
        }
        $message = 'Du hast eine Rundmail abgesendet.';
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'read')
{
    if($player->GetClan() == 0)
    {
        $message = "Du bist in keiner Bande!";
    }
    else if($clan->GetChallengeFight() == 0)
    {
        $message = "Deine Bande hat keine Herausforderung";
    }
    else
    {
        $player->SetClanWarPopup(0);
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'pay')
{
    if (isset($_POST['berry']) && is_numeric($_POST['berry']) && $_POST['berry'] > 0)
    {
        $berry = $_POST['berry'];
        if ($player->GetBerry() < $berry)
        {
            $message = 'Du hast nicht genügend Berry.';
        }
        else if($player->GetBookingDays() > 0)
        {
            $message = "Du kannst während der Pause nicht in die Bande einzahlen!";
        }
        else
        {
            $clan->AddBerry($berry, $player->GetID(), $player->GetName(), 0, 'Kasse');
            $player->RemoveBerry($berry);
            $message = 'Du hast ' . number_format($berry,'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/> in die Kasse eingezahlt.';
            ?>
            <script>
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.href);
                }
            </script>
            <?php
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'rank' && $clan->GetLeader() == $player->GetID())
{
    $message = '';
    $updatestring = 'Kapitän;1;1;1;1;1;1;1';
    for($i = 0; $i < count($_POST['rankname']); $i++)
    {
        if($_POST['rankname'][$i+1] == '')
        {
            $message = 'Mindestens 1 Rangname war nicht ausgefüllt.';
        }
        else
        {
            $name = $database->EscapeString($_POST['rankname'][$i+1]);
            $updatestring .= '@'.$name.';';
            if($_POST['canseesk'][$i+1] == 'on')
                $updatestring .= '1;';
            else
                $updatestring .= '0;';
            if($_POST['canchangesk'][$i+1] == 'on')
                $updatestring .= '1;';
            else
                $updatestring .= '0;';
            if($_POST['candorm'][$i+1] == 'on')
                $updatestring .= '1;';
            else
                $updatestring .= '0;';
            if($_POST['canchangepro'][$i+1] == 'on')
                $updatestring .= '1;';
            else
                $updatestring .= '0;';
            if($_POST['canseeintern'][$i+1] == 'on')
                $updatestring .= '1;';
            else
                $updatestring .= '0;';
            if($_POST['candofights'][$i+1] == 'on')
                $updatestring .= '1;';
            else
                $updatestring .= '0;';
            if($_POST['candomanagement'][$i+1] == 'on')
                $updatestring .= '1';
            else
                $updatestring .= '0';
        }
    }
    if($message == '')
    {
        if($updatestring == $clan->GetRanks())
            $message = 'Die Ränge haben sich nicht geändert.';
        else
        {
            $clan->SetRanks($updatestring);
            $message = 'Die Ränge wurden aktualisiert.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'deleterank' && isset($_GET['rid']) && is_numeric($_GET['rid']) && $clan->GetLeader() == $player->GetID()) {
    if($_GET['rid'] <= 2)
    {
        $message = 'Dieser Rang kann nicht gelöscht werden.';
    }
    else
    {
        $rankid = $_GET['rid'];
        $database->Update('clanrank=2', 'accounts', 'clanrank = ' . $rankid. ' AND clan=' . $clan->GetID(), 999);
        $database->Update('clanrank=clanrank-1', 'accounts', 'clanrank > ' . $rankid. ' AND clan=' . $clan->GetID(), 999);
        $ranks = explode('@', $clan->GetRanks());
        array_splice($ranks, $rankid, 1);
        $rankupdate = implode('@', $ranks);
        $database->Update('ranks="'.$rankupdate.'"', 'clans', 'id='.$clan->GetID(), 1);
        $clan->SetRanks($rankupdate);
        $message = 'Der Rang wurde gelöscht!<br/>Die betroffenen Mitglieder wurden zurückgestuft.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'changerank' && $clan->GetLeader() == $player->GetID())
{
    if(!isset($_POST['player']) || !is_numeric($_POST['player']))
        $message = 'Ungültiger Spieler';
    else
    {
        $rankPlayer = new Player($database, $_POST['player']);
        if($rankPlayer == null)
            $message = 'Ungültiger Spieler';
        else if(!isset($_POST['rank']) || !is_numeric($_POST['rank']))
            $message = 'Ungültiger Rang';
        else
        {
            $rank = explode('@', $clan->GetRanks());
            $rank = explode(';', $rank[$_POST['rank']]);
            $rankname = $rank[0];
            $message = 'Der Rang für <a href="?p=profil&id='.$rankPlayer->GetID().'">' .$rankPlayer->GetName(). '</a> wurde zu ' .$rankname. ' geändert.';
            $database->Update('clanrank='.$_POST['rank'], 'accounts', 'id='.$rankPlayer->GetID(), 1);
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'changeclanname')
{
    if (!isset($_POST['clanname']))
    {
        $message = 'Ungültiger Bandenname';
    }
    else if (!isset($_POST['clantag']))
    {
        $message = 'Ungültiger Bandentag';
    }
    else if ($_POST['clanname'] == '' || $database->HasBadWords($_POST['clanname']))
    {
        $message = 'Ungültiger Bandenname';
    }
    else if ($_POST['clantag'] == '' || $database->HasBadWords($_POST['clantag']))
    {
        $message = 'Ungültiger Bandentag';
    }
    else if ($clan->GetLeader() != $player->GetID())
    {
        $message = 'Nur der Bandenleiter kann den Bandennamen ändern.';
    }
    else if ($clan->GetGold() < 200)
    {
        $message = 'Die Bande hat zu wenig Gold um sich die Namensänderung leisten zu können.';
    }
    else
    {
        $clanname = $database->EscapeString($_POST['clanname']);
        $clantag = $database->EscapeString($_POST['clantag']);

        if (!preg_match("/^[a-zA-Z0-9öäüÖÄÜß]+$/", $clanname))
        {
            $message = 'Der Name darf nur aus Buchstaben und Zahlen bestehen.';
        }
        else if (!preg_match("/^[a-zA-Z0-9öäüÖÄÜß]+$/", $clantag))
        {
            $message = 'Der Tag darf nur aus Buchstaben und Zahlen bestehen.';
        }
        else
        {
            $result = $database->Select('*', 'clans', 'name="' . $clanname . '" AND ID != '.$clan->GetID().' OR tag="' . $clantag . '" AND ID != ' . $clan->GetID(), 1);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $message = 'Dieser Bandenname oder Bandentag ist bereits vergeben.';
                }
                else
                {
                    $clan->ChangeName($clanname, $clan->GetName(), $player->GetName());
                    $clan->ChangeTag($clantag);
                    $message = 'Der Bandenname sowie der Tag wurde geändert.';
                }
                $result->close();
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'payg')
{
    if (isset($_POST['gold']) && is_numeric($_POST['gold']) && $_POST['gold'] > 0)
    {
        $gold = $_POST['gold'];
        if ($player->GetGold() < $gold)
        {
            $message = 'Du hast nicht genügend Gold.';
        }
        else
        {
            $clan->AddGold($gold, $player->GetID(), $player->GetName(), 0, 'Gold Kasse');
            $player->RemoveGold($gold);
            $message = 'Du hast ' . number_format($gold,'0', '', '.') . ' <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/> in die Kasse eingezahlt.';
            ?>
            <script>
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.href);
                }
            </script>
            <?php
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'decline' && $clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management'))
{
    if (isset($_GET['uid']) && is_numeric($_GET['uid']))
    {
        $joiner = new Player($database, $_GET['uid'], $actionManager);
        if (!$joiner->GetClanApplication() == $clan->GetID())
        {
            $message = 'Dieser Spieler hat keine Bewerbung an die Bande gesendet.';
        }
        else
        {
            $joiner->DeleteClanApplication();
            $message = 'Du hast die Bewerbung von <a href="?p=profil&id='.$joiner->GetID().'">' . $joiner->GetName() . '</a> abgelehnt.';
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'stay' && $clan->GetLeader() != $player->GetID() && isset($_POST['realcheck']))
{
    $player->LeaveClan();
    $message = 'Du hast den Austritt aus der Bande rückgängig gemacht.';
}
else if (isset($_GET['a']) && $_GET['a'] == 'leave' && $clan->GetLeader() != $player->GetID() && isset($_POST['realcheck']))
{
    $player->LeaveClan();
    $message = 'Du wirst die Bande um 0 Uhr verlassen.';
}
else if (isset($_GET['a']) && $_GET['a'] == 'promote' && $clan->GetLeader() == $player->GetID())
{
    if (isset($_GET['uid']) && is_numeric($_GET['uid']))
    {
        $joiner = new Player($database, $_GET['uid'], $actionManager);
        $clan->MakeLeader($joiner);
        $message = 'Du hast den Spieler <a href="?p=profil&id='.$joiner->GetID().'">' . $joiner->GetName() . '</a> zum Kapitän ernannt.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'payout')
{
    if (isset($_POST['playerid']) && is_numeric($_POST['playerid']))
    {
        $otherPlayer = new Player($database, $_POST['playerid'], $actionManager);
        if ($otherPlayer->GetClan() != $clan->GetID())
        {
            $message = 'Dieser Spieler ist nicht in deiner Bande.';
        }
        else if(!$clan->GetRankPermission($player->GetClanRank(), "treasureedit"))
        {
            $message = 'Du bist dazu nicht berechtigt.';
        }
        else if (!isset($_POST['berry']) || !is_numeric($_POST['berry']) || $_POST['berry'] <= 0)
        {
            $message = 'Fehlerhafte Parameter';
        }
        else
        {
            $berry = $_POST['berry'];
            if ($clan->GetBerry() < $berry)
            {
                $message = 'Die Bande hat nicht genügend Berry.';
            }
            else
            {
                $clan->RemoveBerry($berry, $player->GetID(), $player->GetName(), $otherPlayer->GetID(), $otherPlayer->GetName());
                $otherPlayer->AddBerry($berry);
                $message = 'Du hast ' . number_format($berry,'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/> an <a href="?p=profil&id='.$otherPlayer->GetID().'">' . $otherPlayer->GetName() . '</a> gezahlt.';
            }
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'levelstat' && isset($_POST['stat']))
{
    $allowed = array('ad', 'def', 'atk', 'lp');
    $gold = $clan->GetGold();
    $berry = $clan->GetBerry();
    $berrycost = '';
    $goldcost = '';
    switch ($_POST['stat']) {
        case 'ad':
            $berrycost = $clan->GetLevelUpCostBerry($clan->GetAD() + 1);
            $goldcost = $clan->GetLevelUpCostGold($clan->GetAD() + 1);
            break;
        case 'lp':
            $berrycost = $clan->GetLevelUpCostBerry($clan->GetLP() + 1);
            $goldcost = $clan->GetLevelUpCostGold($clan->GetLP() + 1);
            break;
        case 'atk':
            $berrycost = $clan->GetLevelUpCostBerry($clan->GetAttack() + 1);
            $goldcost = $clan->GetLevelUpCostGold($clan->GetAttack() + 1);
            break;
        case 'def':
            $berrycost = $clan->GetLevelUpCostBerry($clan->GetDefense() + 1);
            $goldcost = $clan->GetLevelUpCostGold($clan->GetDefense() + 1);
            break;
    }

    if(!in_array($_POST['stat'], $allowed))
    {
        $message = 'Der Stat ist ungültig.';
    }
    else if(!$clan->GetRankPermission($player->GetClanRank(), 'management'))
    {
        $message = 'Du hast nicht die nötigen Rechte dies zu tun.';
    }
    else if($berry < $berrycost)
    {
        $message = 'Es ist nicht genug Berry in der Schatzkammer.';
    }
    else if($gold < $goldcost)
    {
        $message = 'Es ist nicht genug Gold in der Schatzkammer.';
    }
    else
    {
        $clan->RemoveBerry($berrycost, $player->GetID(), $player->GetName(), 0, 'Aufwertung');
        $clan->RemoveGold($goldcost, $player->GetID(), $player->GetName(), 0, 'Aufwertung');
        $clan->SetExp($clan->GetExp() + 10);
        switch ($_POST['stat'])
        {
            case 'ad':
                $stat = 'AD';
                $image = 'img/offtopic/AD.png?1';
                $statvalue = $clan->GetAD() + 1;
                $clan->SetAD($clan->GetAD() + 1);
                break;
            case 'lp':
                $stat = 'LP';
                $image = 'img/offtopic/LP.png?1';
                $statvalue = $clan->GetLP() + 1;
                $clan->SetLP($clan->GetLP() + 1);
                break;
            case 'atk':
                $stat = 'Angriff';
                $image = 'img/offtopic/ANG.png?1';
                $statvalue = $clan->GetAttack() + 1;
                $clan->SetAttack($clan->GetAttack() + 1);
                break;
            case 'def':
                $stat = 'Abwehr';
                $image = 'img/offtopic/DEF.png?1';
                $statvalue = $clan->GetDefense() + 1;
                $clan->SetDefense($clan->GetDefense() + 1);
                break;
        }

        $database->Update('memberki=memberki+10, zeni='.$clan->GetBerry().', gold='.$clan->GetGold().', ad='.$clan->GetAD().', lp='.$clan->GetLP().', atk='.$clan->GetAttack().', def='.$clan->GetDefense().', exp='.$clan->GetExp(),'clans','id='.$clan->GetID());
        $text = "<a href='?p=profil&id=".$player->GetID()."'>".$player->GetName() . "</a> hat den Stat: " . $stat . " von Level " . number_format($statvalue - 1,0, '', '.') . " auf Level " . number_format($statvalue,0, '', '.') . " erhöht.<br/><img src='".$image."' alt='".$stat."' title='".$stat."' style='width: 100px; height: 100px;'><div class='spacer'></div> Der Preis dafür war: <br/>" . number_format($berrycost, 0, '', '.') . " <img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 2px; height: 20px; width: 13px;'/><br/>" . number_format($goldcost, 0, '', '.') . " <img src='img/offtopic/GoldSymbol.png' alt='Gold' title='Gold' style='position: relative; top: 2px; height: 20px; width: 20px;'/> <div class='spacer'></div>Deine Vorteile sind nun: <div class='spacer'></div>AD: " . 0.5 * ($clan->GetAD() + $clan->GetLevel()) ."%<br/>LP: " . 0.5 * ($clan->GetLP() + $clan->GetLevel()). "%<br/>Angriff: " . 0.5 * ($clan->GetAttack() + $clan->GetLevel()). "%<br/>Abwehr: " . 0.5 * ($clan->GetDefense() + $clan->GetLevel()). "%<br/>";
        $i = 0;
        $list = new Generallist($database, 'accounts', 'name, clan', 'clan="' . $clan->GetID() . '"', '', 999999, 'ASC');
        $entry = $list->GetEntry($i);
        while ($entry != null)
        {
            $PMManager->SendPM(0, $clan->GetBanner(), $clan->GetName(), $stat . ' wurde auf Level '. number_format($statvalue,0, '', '.') . ' erhöht.', '<center>' . $text . '</center>', $entry['name'], 1);
            $i++;
            $entry = $list->GetEntry($i);
        }
        $message = 'Du hast ' . $stat . ' auf ' . number_format($statvalue,0, '', '.') . ' erhöht.<br/>Deine Bande wurde informiert.';
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'level')
{
    $gold = $clan->GetGold();
    $goldcost = $clan->GetLevelUpCostGold($clan->GetLevel() + 1, false);

    if(!$clan->GetRankPermission($player->GetClanRank(), 'management'))
    {
        $message = 'Du hast nicht die nötigen Rechte dies zu tun.';
    }
    else if($gold < $goldcost)
    {
        $message = 'Es ist nicht genug Gold in der Schatzkammer.';
    }
    else if($clan->GetExp() < $clan->GetRequiredExp())
    {
        $message = 'Die Bande hat nicht genug Aktivitätspunkte gesammelt.';
    }
    else
    {
        $clan->RemoveGold($goldcost, $player->GetID(), $player->GetName(), 0, 'Bandenlevel');
        $clan->SetExp($clan->GetExp() - $clan->GetRequiredExp());
        $clan->SetLevel($clan->GetLevel() + 1);

        $database->Update('gold='.$clan->GetGold().', level='.$clan->GetLevel().', exp='.$clan->GetExp(),'clans','id='.$clan->GetID());
        $text = "<a href='?p=profil&id=".$player->GetID()."'>".$player->GetName() . "</a> hat die Bande von Level " . number_format($clan->GetLevel() - 1,0, '', '.') . " auf Level " . number_format($clan->GetLevel(),0, '', '.') . " erhöht.<br/><img src='img/offtopic/lvlup.png?1' alt='Aufwertung' title='Aufwertung' style='width: 100px; height: 100px;'><div class='spacer'></div> Der Preis dafür war: <br/>" . number_format($goldcost, 0, '', '.') . " <img src='img/offtopic/GoldSymbol.png' alt='Gold' title='Gold' style='position: relative; top: 2px; height: 20px; width: 20px;'/> <div class='spacer'></div>Deine Vorteile sind nun: <div class='spacer'></div>AD: " . 0.5 * ($clan->GetAD() + $clan->GetLevel()) ."%<br/>LP: " . 0.5 * ($clan->GetLP() + $clan->GetLevel()). "%<br/>Angriff: " . 0.5 * ($clan->GetAttack() + $clan->GetLevel()). "%<br/>Abwehr: " . 0.5 * ($clan->GetDefense() + $clan->GetLevel()). "%<br/>";
        $i = 0;
        $list = new Generallist($database, 'accounts', 'name, clan', 'clan="' . $clan->GetID() . '"', '', 999999, 'ASC');
        $entry = $list->GetEntry($i);
        while ($entry != null)
        {
            $PMManager->SendPM(0, $clan->GetBanner(), $clan->GetName(), 'Die Bande wurde auf Level '. number_format($clan->GetLevel(),0, '', '.') . ' erhöht.', '<center>' . $text . '</center>', $entry['name'], 1);
            $i++;
            $entry = $list->GetEntry($i);
        }
        $message = 'Du hast die Bande auf ' . number_format($clan->GetLevel(),0, '', '.') . ' erhöht.<br/>Deine Bande wurde informiert.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'kick')
{
    if (isset($_GET['uid']) && is_numeric($_GET['uid']))
    {
        $joiner = new Player($database, $_GET['uid'], $actionManager);
        if ($joiner->GetClan() != $clan->GetID())
        {
            $message = 'Dieser Spieler ist nicht in deiner Bande.';
        }
        else if ($joiner->GetID() == $player->GetID())
        {
            $message = 'Du kannst dich nicht selbst kicken.';
        }
        else if(!$clan->GetRankPermission($player->GetClanRank(), "management"))
        {
            $message = 'Du bist dazu nicht berechtigt!';
        }
        else
        {
            $joiner->LeaveClan();
            if($joiner->GetLeaveClan() == 0)
                $message = 'Du hast den Spieler <a href="?p=profil&id='.$joiner->GetID().'">' . $joiner->GetName() . '</a> aus der Bande geschmissen, er wird ab 0 Uhr nicht mehr teil der Bande sein.';
            else
                $message = 'Du hast den rauswurf von <a href="?p=profil&id='.$joiner->GetID().'">' . $joiner->GetName() . '</a> rückgängig gemacht.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'accept' && $clan->GetRankPermission($player->GetClanRank(), 'management'))
{
    if (isset($_GET['uid']) && is_numeric($_GET['uid']))
    {
        $joiner = new Player($database, $_GET['uid'], $actionManager);
        if (!$joiner->GetClanApplication() == $clan->GetID())
        {
            $message = 'Dieser Spieler hat keine Bewerbung an die Bande gesendet.';
        }
        else if($clan->GetMembers() >= $clan->GetMaxMembers())
        {
            $message = 'Deine Bande kann keine weiteren Mitglieder aufnehmen.';
        }
        else
        {
            $joiner->JoinClan($clan);
            $clan->PlayerJoins();
            $message = 'Du hast die Bewerbung von <a href="?p=profil&id='.$joiner->GetID().'">' . $joiner->GetName() . '</a> angenommen.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'change' && count($_POST) > 0)
{
    $image = $clan->GetImage();
    $banner = $clan->GetBanner();
    $flag = $clan->GetFlag();
    $interntext = '';
    $text = '';
    $rules = '';
    $requirements = '';
    $discord = '';
    $paysbounty = '';

    if(!$clan->GetRankPermission($player->GetClanRank(), "profiledit"))
    {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else {
        if (isset($_POST['image']) && isset($_FILES['file_upload']) && $_FILES['file_upload']['tmp_name'] != '') {
            $imgHandler = new ImageHandler('userdata/clanbilder/');
            $result = $imgHandler->Upload($_FILES['file_upload'], $image, 600, 400);
            switch ($result) {
                case -1:
                    $message = 'Die Datei ist zu groß.';
                    break;
                case -2:
                    $message = 'Die Datei ist ungültig.';
                    break;
                case -3:
                    $message = 'Es ist nur jpg, jpeg und png erlaubt.';
                    break;
                case -4:
                    $message = 'Es gab ein Problem beim generieren des Namens.';
                    break;
                case -5:
                    $message = 'Es gab ein Problem beim hochladen.';
                    break;
            }
        }

        if (isset($_POST['flag']) && isset($_FILES['file_upload3']) && $_FILES['file_upload3']['tmp_name'] != '') {
            $imgHandler = new ImageHandler('userdata/clanbilder/');
            $result = $imgHandler->Upload($_FILES['file_upload3'], $flag, 230, 110);
            switch ($result) {
                case -1:
                    $message = 'Die Datei ist zu groß.';
                    break;
                case -2:
                    $message = 'Die Datei ist ungültig.';
                    break;
                case -3:
                    $message = 'Es ist nur jpg, jpeg und png erlaubt.';
                    break;
                case -4:
                    $message = 'Es gab ein Problem beim generieren des Namens.';
                    break;
                case -5:
                    $message = 'Es gab ein Problem beim hochladen.';
                    break;
            }
        }

        if (isset($_POST['banner']) && isset($_FILES['file_upload2']) && $_FILES['file_upload2']['tmp_name'] != '') {
            $imgHandler = new ImageHandler('userdata/clanwappen/');
            $result = $imgHandler->Upload($_FILES['file_upload2'], $banner, 30, 30);
            switch ($result) {
                case -1:
                    $message = 'Die Datei ist zu groß.';
                    break;
                case -2:
                    $message = 'Die Datei ist ungültig.';
                    break;
                case -3:
                    $message = 'Es ist nur jpg, jpeg und png erlaubt.';
                    break;
                case -4:
                    $message = 'Es gab ein Problem beim generieren des Namens.';
                    break;
                case -5:
                    $message = 'Es gab ein Problem beim hochladen.';
                    break;
            }
        }
        if (!isset($message)) {
            if (isset($_POST['text'])) {
                $text = $_POST['text'];
            }
            if (isset($_POST['interntext'])) {
                $interntext = $_POST['interntext'];
            }
            if (isset($_POST['rules'])) {
                $rules = $_POST['rules'];
            }
            if (isset($_POST['requirements'])) {
                $requirements = $_POST['requirements'];
            }
            if (isset($_POST['discord'])) {
                $discord = $_POST['discord'];
            }
            if (isset($_POST['paysbounty'])) {
                $paysbounty = $_POST['paysbounty'];
            }
            $clan->Change($image, $flag, $banner, $interntext, $text, $rules, $requirements, $discord, $paysbounty);
            $message = 'Du hast die Bandendaten geändert.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'delete' && isset($_POST['realcheck']))
{
    if ($clan->GetLeader() != $player->GetID())
    {
        $message = 'Du bist nicht der Bandenleiter, du kannst die Bande nicht auflösen.';
    }
    else
    {
        $clan->Delete($player);
        header('Location: ?p=news');
        exit();
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'acceptalliance')
{
    if(!$clan->GetRankPermission($player->GetClanRank(), 'management'))
    {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else if(!isset($_GET['cid']) || !is_numeric($_GET['cid']))
    {
        $message = 'Fehlende(r) Parameter';
    }
    else
    {
        $allianceClan = new Clan($database, $_GET['cid']);
        if(!$allianceClan->IsValid())
        {
            $message = "Die Bande existiert nicht.";
        }
        else if($allianceClan->GetID() == $player->GetClan())
        {
            $message = "Deine Bande kann keine Allianz mit sich selbst gründen.";
        }
        else if(!in_array($allianceClan->GetID(), $clan->GetAllianceInvites()))
        {
            $message = "Die Bande hat keine Allianzanfrage gesendet oder sie zurückgezogen.";
        }
        else
        {
            $clan->AddAllianceMember($allianceClan->GetID());
            $clan->RemoveAllianceInvite($allianceClan->GetID());
            $message = "Du hast die Allianz mit der Bande ".$allianceClan->GetName()." bestätigt.";
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'declinealliance')
{
    if(!$clan->GetRankPermission($player->GetClanRank(), 'management'))
    {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else if(!isset($_GET['cid']) || !is_numeric($_GET['cid']))
    {
        $message = 'Fehlende(r) Parameter';
    }
    else
    {
        $allianceClan = new Clan($database, $_GET['cid']);
        if(!$allianceClan->IsValid())
        {
            $message = "Die Bande existiert nicht.";
        }
        else if(!in_array($allianceClan->GetID(), $clan->GetAllianceInvites()))
        {
            $message = "Die Bande hat keine Allianzanfrage gesendet oder sie zurückgezogen.";
        }
        else
        {
            $clan->RemoveAllianceInvite($allianceClan->GetID());
            $message = "Du hast die Allianz mit der Bande ".$allianceClan->GetName()." abgelehnt.";
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'cancelalliance')
{
    if(!$clan->GetRankPermission($player->GetClanRank(), 'management'))
    {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else if(!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = 'Fehlende(r) Parameter';
    }
    else
    {
        $allianceClan = new Clan($database, $_POST['id']);
        if(!$allianceClan->IsValid())
        {
            $message = "Die Bande existiert nicht.";
        }
        else if(!in_array($allianceClan->GetID(), $clan->GetAlliances()))
        {
            $message = "Es besteht kein Bündnis mit dieser Bande.";
        }
        else
        {
            $clan->RemoveAlliance($allianceClan->GetID());
            $message = "Du hast die Allianz mit der Bande ".$allianceClan->GetName()." aufgelöst.";
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'allianceinvite')
{
    if(!$clan->GetRankPermission($player->GetClanRank(), 'management'))
    {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else if(!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = 'Fehlende(r) Parameter';
    }
    else
    {
        $allianceClan = new Clan($database, $_POST['id']);
        if(!$allianceClan->IsValid())
        {
            $message = "Die Bande existiert nicht.";
        }
        else if(in_array($clan->GetID(), $allianceClan->GetAlliances()) || in_array($clan->GetID(), $allianceClan->GetAllianceInvites()))
        {
            $message = "Es existiert bereits ein Bündnis, oder eine Bündnisanfrage.";
        }
        else if(count($allianceClan->GetAlliances()) >= 2 || count($clan->GetAlliances()) >= 2)
        {
            $message = "Deine Bande, oder die Bande ".$allianceClan->GetName()." haben bereits 2 Allianz-Banden.";
        }
        else
        {
            $allianceClan->AddAllianceInvite($clan->GetID());
            $message = "Du hast der Bande ".$allianceClan->GetName()." eine Allianzanfrage gesendet.";
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'cancelallianceinvite')
{
    if(!$clan->GetRankPermission($player->GetClanRank(), 'management'))
    {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else if(!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = 'Fehlende(r) Parameter';
    }
    else
    {
        $allianceClan = new Clan($database, $_POST['id']);
        if(!$allianceClan->IsValid())
        {
            $message = "Die Bande existiert nicht.";
        }
        else if(in_array($clan->GetID(), $allianceClan->GetAlliances()) || !in_array($clan->GetID(), $allianceClan->GetAllianceInvites()))
        {
            $message = "Die Allianz wurde bereits durch die Bande bestätigt oder die Anfrage wurde bereits abgelehnt/zückgezogen.";
        }
        else
        {
            $allianceClan->RemoveAllianceInvite($clan->GetID());
            $message = "Du hast die Allianz-Anfrage an die Bande ".$allianceClan->GetName()." zurückgezogen.";
        }
    }
}