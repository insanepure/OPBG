<?php
// ALSO EDIT THIS IN player.php class !!!
$year = 2025;
$month = 1;
$day = 19;
$hour = 12;
$minute = 0;
$second = 0;
$gameStartTime = mktime($hour, $minute, $second, $month, $day, $year);
$isReleaseNow = time() > $gameStartTime;
// $halloweenEventActive = (time() > mktime(0, 0, 0, 10, 28, 2022) && time() < mktime(0, 0, 0, 11, 11, 2022));
$halloweenEventActive = false;


$userRegisterActive = true;
$userLoginActive = true;
$charLoginActive = $isReleaseNow;
$charaCreationActive = $isReleaseNow;


//this is for login stuff
include_once 'serverurl.php';
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/header.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$db = 'DATENBANK';
$user = 'USER';
$pw = 'PASSWORT';
$database = new Database($db, $user, $pw);

include_once 'generallist.php';
include_once 'statslist/statslist.php';
include_once 'actions/actionmanager.php';
include_once 'items/itemmanager.php';
include_once 'titel/titelmanager.php';
include_once 'pms/pmmanager.php';
include_once 'planet/planet.php';
include_once 'places/place.php';
include_once 'player/player.php';
include_once 'clan/clan.php';
include_once 'eventitems/eventitems.php';

if (!$userLoginActive && $account->IsLogged())
{
    $account->Logout();
}
if(!$charLoginActive && $account->IsLogged() && $player && $player->GetARank() < 3 || $player && $player->IsBanned() && !$player->IsAdminLogged())
{
    $player->Logout();
}


/*if($player->GetARank() >=3) {
$database->Debug();
}*/

include_once 'events/events.php';
include_once 'npc/npc.php';
include_once 'fight/fight.php';
include_once 'gamedata.php';
//$database->Debug();
function SendMail($email, $topic, $message)
{
    $sender   = "info@animebg.de";

    $content = file_get_contents('mail.php');
    $fixedtopic = str_replace('ö', '&ouml;', $topic);
    $fixedtopic = str_replace('Ö', '&Ouml;', $fixedtopic);
    $fixedtopic = str_replace('ä', '&auml;', $fixedtopic);
    $fixedtopic = str_replace('Ä', '&Auml;', $fixedtopic);
    $fixedtopic = str_replace('ü', '&uuml;', $fixedtopic);
    $fixedtopic = str_replace('Ü', '&Uuuml;', $fixedtopic);
    $fixedtopic = str_replace('ß', '&szlig;', $fixedtopic);
    $content = str_replace("{0}", $fixedtopic, $content);

    $fixedcontent = str_replace('ö', '&ouml;', $message);
    $fixedcontent = str_replace('Ö', '&Ouml;', $fixedcontent);
    $fixedcontent = str_replace('ä', '&auml;', $fixedcontent);
    $fixedcontent = str_replace('Ä', '&Auml;', $fixedcontent);
    $fixedcontent = str_replace('ü', '&uuml;', $fixedcontent);
    $fixedcontent = str_replace('Ü', '&Uuuml;', $fixedcontent);
    $fixedcontent = str_replace('ß', '&szlig;', $fixedcontent);
    $content = str_replace("{1}", $fixedcontent, $content);

    $content = utf8_decode($content);
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                        //Send using SMTP
        $mail->Host       = 'smtp.ionos.de';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'info@animebg.de';                     //SMTP username
        $mail->Password   = 'J5j*2Q2b88YD*@j';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        $mail->addCustomHeader("List-Unsubscribe",'<info@animebg.de>, <https://animebg.de/unsubscribe.php?email='.$email.'>');



        //Set who the message is to be sent from
        $mail->setFrom($sender, 'AnimeBG');
        //Set an alternative reply-to address
        $mail->addReplyTo($sender, 'AnimeBG');
        //Set who the message is to be sent to
        $mail->addAddress($email, 'User');
        //Set the subject line
        $mail->Subject = $topic;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($content);
        //Replace the plain text body with one created manually
        $mail->AltBody = $message;

        $mail->send();
        return true;
    }
    catch (Exception $e)
    {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }

}


$eventItems = null;

if ($player->IsLogged())
{
    $eventItems = new EventItems($database);

    $eventItemURL = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    $eventItemPickURL = '&a=eventitem';
    $eventItemURL = str_replace($eventItemPickURL . '&eventid='.$_GET['eventid'], '', $eventItemURL);
    $eventItemURL = str_replace('&a=statspopup', '', $eventItemURL);
    $eventItemURL = str_replace('&a=stats', '', $eventItemURL);
    $eventItemURL = str_replace('&a=change', '', $eventItemURL);
    $eventItemURL = str_replace('&a=style', '', $eventItemURL);
    $eventItemURL = str_replace('&a=titel', '', $eventItemURL);
    $eventItemURL = str_replace('&a=picture', '', $eventItemURL);
    $eventItemURL = str_replace('&a=speedup', '', $eventItemURL);
    $eventItemURL = str_replace('&a=impeldownfree', '', $eventItemURL);
    $eventItemURL = str_replace('&a=reset', '', $eventItemURL);
    $eventItemURL = str_replace('&a=block', '', $eventItemURL);
    $eventItemURL = str_replace('&a=friend', '', $eventItemURL);
    $eventItemURL = str_replace('&a=acceptfriend', '', $eventItemURL);
    $eventItemURL = str_replace('&a=declinefriend', '', $eventItemURL);
    $eventItemURL = str_replace('&a=meld', '', $eventItemURL);
    $eventItemURL = str_replace('&a=pwchange', '', $eventItemURL);
    $eventItemURL = str_replace('&a=delete', '', $eventItemURL);
    $eventItemURL = str_replace('&a=deleteadmin', '', $eventItemURL);
    $eventItemURL = str_replace('&a=usereset', '', $eventItemURL);
    $eventItemURL = str_replace('&a=powerup', '', $eventItemURL);
    $eventItemURL = str_replace('&a=fightattack', '', $eventItemURL);
    $eventItemURL = str_replace('&a=attacks', '', $eventItemURL);
    $eventItemURL = str_replace('&a=fakeki', '', $eventItemURL);
    $eventItemURL = str_replace('&a=kopfgeldimage', '', $eventItemURL);
    $eventItemURL = str_replace('&a=groupaccept', '', $eventItemURL);
    $eventItemURL = str_replace('&a=groupdecline', '', $eventItemURL);
    $eventItemURL = str_replace('&a=groupkick', '', $eventItemURL);
    $eventItemURL = str_replace('&a=grouppromote', '', $eventItemURL);
    $eventItemURL = str_replace('&a=groupinvite', '', $eventItemURL);
    $eventItemURL = str_replace('&a=trade', '', $eventItemURL);
    $eventItemURL = str_replace('&a=declineEvent', '', $eventItemURL);
    $eventItemURL = str_replace('&a=acceptEvent', '', $eventItemURL);
    $eventItemURL = str_replace('&a=declineChallenge', '', $eventItemURL);
    $eventItemURL = str_replace('&a=acceptChallenge', '', $eventItemURL);
    $eventItemURL = str_replace('&a=challenge', '', $eventItemURL);
    $eventItemURL = str_replace('&a=copy', '', $eventItemURL);
    $eventItemURL = str_replace('&a=wappen', '', $eventItemURL);

    $eventItemsData = $eventItems->LoadItems($eventItemURL);

    $planet = new Planet($database, $player->GetPlanet());
    $place = new Place($database, $player->GetPlace(), null);

    if (isset($_GET['a']) && $_GET['a'] == 'eventitem' && isset($_GET['eventid']))
    {
        $eventItem = $eventItems->LoadItem($_GET['eventid']);
        if($eventItem->GetActive() && !$eventItems->HasItem($player->GetID(), $_GET['eventid']) && time() > strtotime($eventItem->GetStartTime()) && time() < strtotime($eventItem->GetEndTime()))
        {
            $eventItems->AddItem($player->GetID(), $eventItem->GetID());

            $itemID = $eventItem->GetItem();
            $itemAmount = $eventItem->GetItemAmount();
            $berry = $eventItem->GetBerry();
            $gold = $eventItem->GetGold();
            $statspunkte = $eventItem->GetStatspunkte();

            $message = 'Du erhältst';
            $itemMessage = '';
            $berryMessage = '';
            $goldMessage = '';
            $statsMessage = '';
            if ($itemID != 0)
            {
                $itemManager = new ItemManager($database);
                //52 Pfad 1
                if($itemID == 52)
                {
                    if ($player->GetPfad(1) != 'None')
                    {
                        if ($player->GetPfad(1) == "Logia")
                        {
                            $itemID = 54;
                        }
                        else if ($player->GetPfad(1) == "Paramecia")
                        {
                            $itemID = 53;
                        }
                        else if ($player->GetPfad(1) == "Zoan")
                        {
                            $itemID = 52;
                        }
                    }
                    else
                    {
                        if ($player->GetPfad(2) != 'None')
                        {
                            if ($player->GetPfad(2) == "Schwertkaempfer")
                            {
                                $itemID = 56;
                            }
                            else if ($player->GetPfad(2) == "Schwarzfuss")
                            {
                                $itemID = 57;
                            }
                            else if ($player->GetPfad(2) == "Karatekämpfer")
                            {
                                $itemID = 55;
                            }
                        }
                    }
                }
                //54 Pfad 2
                else if($itemID == 54)
                {
                    if ($player->GetPfad(2) != 'None')
                    {
                        if ($player->GetPfad(2) == "Schwertkaempfer")
                        {
                            $itemID = 56;
                        }
                        else if ($player->GetPfad(2) == "Schwarzfuss")
                        {
                            $itemID = 57;
                        }
                        else if ($player->GetPfad(2) == "Karatekämpfer")
                        {
                            $itemID = 55;
                        }
                    }
                    else
                    {
                        if ($player->GetPfad(1) != 'None')
                        {
                            if ($player->GetPfad(1) == "Logia")
                            {
                                $itemID = 54;
                            }
                            else if ($player->GetPfad(1) == "Paramecia")
                            {
                                $itemID = 53;
                            }
                            else if ($player->GetPfad(1) == "Zoan")
                            {
                                $itemID = 52;
                            }
                        }
                    }
                }
                $item = $itemManager->GetItem($itemID);
                $player->AddItems($item, $item, $itemAmount);

                $itemMessage = number_format($itemAmount, '0', '', '.') . 'x ' . $item->GetName();
            }
            if ($berry != 0)
            {
                $player->AddBerry($berry);

                $berryMessage = number_format($berry, '0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
            }

            if ($gold != 0)
            {
                $player->AddGold($gold);

                $goldMessage = number_format($gold, '0', '', '.') . ' <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>';
            }

            if($statspunkte != 0)
            {
                $player->AddStats($statspunkte);
                $statsMessage = number_format($statspunkte, '0', '', '.') . ' Statspunkte';
            }

            if ($itemMessage != '')
                $message = $message . ' ' . $itemMessage;
            if ($itemMessage != '' && $berryMessage != '')
                $message = $message . ' und';
            if ($berryMessage != '')
                $message = $message . ' ' . $berryMessage;
            if (($itemMessage != '' || $berryMessage != '') && $goldMessage != '')
                $message = $message . ' und';
            if ($goldMessage != '')
                $message = $message . ' ' . $goldMessage;
            if (($itemMessage != '' || $berryMessage != '' || $goldMessage != '') && $statsMessage != '')
                $message = $message . ' und';
            if ($statsMessage != '')
                $message = $message . ' ' . $statsMessage;

            $message = $message . '.';
        }
    }

    $clan = null;
    if ($player->GetClan() != 0)
    {
        $clan = new Clan($database, $player->GetClan());

        if(strtotime($clan->GetChallengeTime()) < time() && $clan->GetChallengeFight() != 0)
        {
            $challengeFight = new Fight($database, $clan->GetChallengeFight(), $player, $actionManager);

            if($challengeFight->GetState() == 0)
            {
                $database->Update('challengefight=0, challengedtime=NOW()', 'clans', 'id='.$clan->GetID());
                $database->Update('clanwarpopup=0, challengedpopup=0', 'accounts', 'clan='.$clan->GetID());
                $winner = array();
                $clan->SetChallengeTime(date('Y-m-d H:i:s'));
                $clan->SetChallengeFight(0);
                foreach ($challengeFight->GetTeams()[0] as $groupmember) {
                    if($groupmember->GetClanID() == $player->GetClan())
                        $winner[] = $groupmember->GetAcc();
                }

                $blocked = '0';
                if($place->GetTerritorium() != $winner[0]->GetClanID())
                    $blocked = $place->GetTerritorium().', time=NOW()';
                $database->Update('territorium='.$winner[0]->GetClanID().', sieger="'.implode(';', $winner).'", gewinn=0, lastfight=NOW(), blocked="'.$blocked.'"', 'places', 'id='.$challengeFight->GetPlace());
                $challengeplace = new Place($database, $challengeFight->GetPlace(), $actionManager);
                $message = 'Deine Bande hat den Ort: ' . $challengeplace->GetName() . ' verloren.';
                $challengeFight->DeleteFightAndFighters();
            }
        }
    }
    $PMManager = new PMManager($database, $player->GetID());
    $tournamentManager = null;
    if ($player->GetTournament() != 0)
    {
        include_once 'tournament/tournamentmanager.php';
        $tournamentManager = new TournamentManager($database, $player->GetPlace(), $player->GetPlanet());
        $pTournament = $tournamentManager->GetTournamentByID($player->GetTournament());
    }
    $fight = null;
    if ($player->GetFight() != 0)
    {
        $fight = new Fight($database, $player->GetFight(), $player, $actionManager);

        if($fight->GetType() == 11 && $fight->GetState() == 0)
        {
            $gegnerBande = new Clan($database, $fight->GetChallenge());
            if(strtotime($gegnerBande->GetChallengeTime()) < time())
            {
                if($fight->GetMembersOfTeam(1) > 0)
                {

                    $attackManager = new AttackManager($database);
                    $roundText = '<tr><td align=center colspan=4><h2>Kampf Beginn</h2></td></tr>';
                    $fight->AddDebugLog('- Fight is full!');
                    $imageLeft = true;
                    $i = 0;
                    $highestKI = 0;
                    while (isset($fight->GetTeams()[$i]))
                    {
                        $players = $fight->GetTeams()[$i];
                        $j = 0;
                        while (isset($players[$j]))
                        {
                            $teamPlayer = $players[$j];

                            $trans = $teamPlayer->GetTransformations();
                            $teamPlayer->SetTransformations('');
                            $playerText = '';
                            if ($trans != '')
                            {
                                $transformations = explode(';', $trans);
                                $powerup = $transformations[0];
                                $attack = $attackManager->GetAttack($powerup);
                                $attackImage = $attack->GetImage();
                                $playerText = $this->Transform($teamPlayer, $attack);
                            }
                            else
                            {
                                $playerText = "!source macht sich zum Kampf bereit.";
                                $attackImage = $teamPlayer->GetImage();
                            }

                            $playerKI = $teamPlayer->GetMaxKI();
                            $attacks = explode(';', $teamPlayer->GetAttacks());
                            foreach ($attacks as &$attackID)
                            {
                                $attack = $attackManager->GetAttack($attackID);
                                if ($attack->GetType() == 4)
                                {
                                    $vwKI = $teamPlayer->GetMaxKI() * (1 + ($attack->GetValue() / 100));
                                    if ($vwKI > $playerKI)
                                        $playerKI = $vwKI;
                                }
                            }

                            if (!$teamPlayer->IsNPC() && $playerKI > $highestKI)
                                $highestKI = $playerKI;

                            $playerText = $fight->ReplaceTextValues($playerText, $teamPlayer, $teamPlayer, 0, 4);
                            $attackText = $fight->DisplayAttackText($attackImage, $playerText, $imageLeft);
                            $roundText = $roundText . $attackText;
                            $imageLeft = !$imageLeft;
                            $j++;
                        }
                        ++$i;
                    }
                    $fight->AddDebugLog('- Highest KI: ' . number_format($highestKI, '0', '', '.'));

                    $i = 0;
                    while (isset($fight->GetTeams()[$i]))
                    {
                        $players = $fight->GetTeams()[$i];
                        $j = 0;
                        while (isset($players[$j]))
                        {
                            $teamPlayer = $players[$j];
                            if ($teamPlayer->IsNPC() && $teamPlayer->IsStatsProcentual())
                            {
                                $fight->AddDebugLog('- NPC: ' . $teamPlayer->GetName());
                                $lp = ($teamPlayer->GetMaxLP() / 100) * $highestKI;
                                $lp = round($lp) * 10;
                                $fight->AddDebugLog('- - New LP: ' . number_format($lp, '0', '', '.'));
                                $kp = ($teamPlayer->GetMaxKP() / 100) * $highestKI;
                                $kp = round($kp) * 10;
                                $fight->AddDebugLog('- - New AD: ' . number_format($kp, '0', '', '.'));
                                $atk = ($teamPlayer->GetMaxAttack() / 100) * $highestKI;
                                $atk = round($atk);
                                $fight->AddDebugLog('- - New Atk: ' . number_format($atk, '0', '', '.'));
                                $def = ($teamPlayer->GetMaxDefense() / 100) * $highestKI;
                                $def = round($def);
                                $fight->AddDebugLog('- - New Def: ' . number_format($def, '0', '', '.'));
                                $ki = round((($lp / 10) + ($kp / 10) + $atk + $def) / 4);
                                $fight->AddDebugLog('- - New KI: ' . number_format($ki, '0', '', '.'));
                                $result = $database->Update('ki=' . $ki . ',mki=' . $ki . ',lp=' . $lp . ',mlp=' . $lp . ',ilp=' . $lp . ',kp=' . $kp . ',mkp=' . $kp . ',ikp=' . $kp . ',attack=' . $atk . ',mattack=' . $atk . ',defense=' . $def . ',mdefense=' . $def, 'fighters', 'id = ' . $teamPlayer->GetID() . '', 1);
                            }
                            $j++;
                        }
                        ++$i;
                    }

                    $newText = $database->EscapeString($roundText . $fight->GetText());
                    $fight->SetText($newText);

                    $fight->AddDebugLog('FIGHT START');
                    $fight->SetState(1);
                    $result = $database->Update('state=' . $fight->GetState() . ',text="' . $newText . '"', 'fights', 'id = ' . $fight->GetID() . '', 1);
                    $timestamp = date('Y-m-d H:i:s');
                    $result = $database->Update('lastaction="' . $timestamp . '"', 'fighters', 'fight = ' . $fight->GetID() . '', 9999999);
                }
                else
                {
                    $fight->AddDebugLog('Bandenfight endet - Keine Verteidigung.');
                    $gegnerBande->SetChallengeTime(date('Y-m-d H:i:s'));
                    $gegnerBande->SetChallengeFight(0);
                    $database->Update('challengefight=0, challengedtime=NOW()', 'clans', 'id='.$gegnerBande->GetID());
                    $database->Update('challengedpopup=0', 'accounts', 'clan='.$gegnerBande->GetID());
                    $winner = array();
                    $fight->AddDebugLog('Bandenfight endet - Debug 1');
                    foreach ($fight->GetTeams()[0] as $groupmember) {
                        if($groupmember->GetClanID() == $player->GetClan())
                            $winner[] = $groupmember->GetAcc();
                    }
                    $fight->AddDebugLog('Bandenfight endet - Debug 2');
                    $winner = implode(';', $winner);
                    $fight->AddDebugLog('Bandenfight endet - '.$winner);
                    $result = $database->Select('*', 'places', 'id='.$fight->GetPlace(), 1);
                    if($result)
                    {
                        $row = $result->fetch_assoc();
                        if($row['territorium'] != $player->GetClan())
                            $database->Update('territorium='.$player->GetClan().', time=NOW(), sieger="'.$winner.'", gewinn=0, lastfight=NOW(), blocked='.$row['territorium'], 'places', 'id='.$fight->GetPlace());
                        else
                            $database->Update('territorium='.$player->GetClan().', sieger="'.$winner.'", lastfight=NOW()', 'places', 'id='.$fight->GetPlace());
                    }
                    $fightplace = new Place($database, $fight->GetPlace(), $actionManager);
                    $message = $fightplace->GetName() . ' wurde von euch beansprucht.';
                    $fight->DeleteFightAndFighters();
                }
            }
        }
    }

    if ($player->GetGroupInvite() != 0)
    {
        $inviter = $player->GetGroupInvite();
        $otherPlayer = new Player($database, $inviter, $actionManager);
        if ($otherPlayer == null || !$otherPlayer->IsValid())
        {
            $player->DeclineGroupInvite();
        }
    }
    else if ($player->GetChallengeFight() != 0)
    {
        $fightID = $player->GetChallengeFight();
        $challengeFight = new Fight($database, $fightID, $player, $actionManager);
        if (!$challengeFight->IsValid() || $challengeFight->IsStarted())
        {
            $player->DeclineChallenge();
        }
    }
    else if ($player->GetEventInvite() != 0)
    {
        $fightID = $player->GetEventInvite();
        $eventFight = new Fight($database, $fightID, $player, $actionManager);
        if (!$eventFight->IsValid() || $eventFight->IsStarted())
        {
            $player->DeclineEvent();
        }
    }

    if($fight != null && !$fight->IsStarted())
    {
        if($fight->GetType() == 1 || $fight->GetType() == 13)
        {
            $now = new Datetime('now');
            $weekday = date("l");
            $min = '00:01';
            $max = '23:59';

            $canDoMirrorFight = true;
            $fresult = $database->Select('acc', 'fighters', 'acc != -1');
            if($fresult && $fresult->num_rows > 0)
            {
                while($frow = $fresult->fetch_assoc())
                {
                    $fPlayer = new Player($database, $frow['acc']);
                    if($fPlayer->GetArank() == 0)
                    {
                        $fFight = new Fight($database, $fPlayer->GetFight(), $fPlayer, $actionManager);
                        if(!$fFight->IsStarted() && $fFight->GetType() == $fight->GetType() && $fPlayer->GetDailyEloEnemyCount($player->GetID()) < 2 && $fPlayer->GetID() != $player->GetID())
                        {
                            $canDoMirrorFight = false;
                        }
                    }
                }
            }
            $mirrorTime = 5;
            if($player->IsDonator())
                $mirrorTime = 3;
            if(((date('H:i') >= $min && date('H:i') < $max) || $player->GetArank() == 3) && abs(DateTime::createFromFormat('Y-m-d H:i:s', $fight->GetTime())->getTimestamp() - $now->getTimestamp()) / 60  > $mirrorTime && $canDoMirrorFight)
            {
                $database->Update('mirrorpopup=1', 'accounts', 'id='.$player->GetID(), 1);
                $player->SetMirror(1);
            }
        }
    }

    if($player->GetMirror() == 1 && $player->GetFight() == 0)
        $player->SetMirror(0);

    if($player->GetLastPage() != $_GET['p'])
    {
        $newPage = $database->EscapeString($_GET['p']);
        $player->AddDebugLog(" ");
        $player->AddDebugLog(date('H:i:s', time()) . " - Seite: ?p=" . $newPage);
        $database->Update('debuglog="'.$player->GetDebugLog().'", lastpage="'.$newPage.'"', 'accounts', 'id='.$player->GetID(), 1);
    }

    if($player->GetActiveUserID() == 0)
    {
        $player->Logout();
        $date_of_expiry = time() - 10;
        setcookie("ocharaid", "", $date_of_expiry);
        header('Location: ?p=charalogin');
        exit();
    }
}
