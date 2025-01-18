<?php

include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/fight/attackmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/market/market.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/pms/pmmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'pages/itemzorder.php';


include 'vendor/autoload.php';

use RestCord\DiscordClient;

$displayedPlayer = null;
$displayedAccount = null;
$isLocalPlayer = false;
$itemManager = new ItemManager($database);
$attackManager = new AttackManager($database);
$titelManager = new titelManager($database);
$market = new Market($database);
$inventory = $player->GetInventory();
$marineShipArray = array(42, 384, 385);

function ShowSlotEquippedImage($slot, $inventory, $zorders, $zordersOnTop, $player, $top = 0)
{
    $item = $inventory->GetItemAtSlot($slot);
    if ($item != null)
    {
        if ($item->IsOnTop())
            $zindex = $zordersOnTop[$slot];
        else
            $zindex = $zorders[$slot];

        if($item->GetEquippedImage() != '' && ($item->IsVisible() || $player->GetArank() >= 2))
        {
            ?>
            <div class="profilecharacter" style="top:<?= 20 + $top; ?>px; left:185px; z-index:<?php echo $zindex; ?>; background-image:url('img/ausruestung/<?php echo $item->GetEquippedImage(); ?>.png?005')"></div>
            <?php
        }
    }
}
if ($player->GetARank() >= 3 && isset($_GET['a']) && $_GET['a'] == 'adminlogin')
{
    $otherPlayer = new Player($database, $_GET['id'], $actionManager);
    $otherPlayer->Login(false, $player->GetID());
    $date_of_expiry = time() - 10;
    setcookie("ocharaid", "", $date_of_expiry);
    $player->Logout();
    header('Location: ?p=news');
    exit();
}

if (isset($_GET['a']) && $_GET['a'] == 'adminlogout')
{
    $otherPlayer = new Player($database, $player->GetAdminLogged(), $actionManager);
    $otherPlayer->Login(false, false);
    $date_of_expiry = time() - 10;
    setcookie("ocharaid", "", $date_of_expiry);
    $player->Logout();
    header('Location: ?p=news');
    exit();
}

if (isset($_GET['a']) && $_GET['a'] == 'changefraktion')
{
   $afraktion = $player->GetRace();
   $newfraktion = $database->EscapeString($_POST['race']);
   $costg = $player->GetGold() - 250;
   $costb = $player->GetBerry() - 25000;
   $newchar = '';
   if($newfraktion == 'Pirat')
   {
       $newchar = 'Pirat1';
   }
   else
   {
       $newchar = 'Marine1';
   }

   if($costg < 0)
   {
       $message = "Du besitzt nicht genug Gold um deine Fraktion zu wechseln!";
   }
   else if($costb < 0)
   {
       $message = "Du besitzt nicht genug Berry um deine Fraktion zu wechseln!";
   }
   else if($newfraktion != 'Pirat' && $newfraktion != 'Marine')
   {
       $message = "Du musst Pirat oder Marine wählen!";
       $text = $player->GetName().' hat beim Fraktionswechsel versucht eine andere Fraktion zu wählen als Marine oder Pirat!';
       $player->Track($text, $player->GetID(), 'system', 2);
   }
   else if($afraktion == $newfraktion)
   {
       $message = "Du bist bereits ".$afraktion." bitte wähle eine andere Fraktion!";
   }
   else
   {
       $player->SetBerry($costb);
       $player->SetGold($costg);
       $player->SetRaceImage($newchar);
       $player->SetRace($newfraktion);
       $text = $player->GetName().' hat seine Fraktion gewechselt von '.$afraktion.' in '.$newfraktion.'!';
       $player->Track($text, $player->GetID(), 'system', 2);
       $message = "Du hast erfolgreich deine Fraktion in ".$newfraktion." geändert!";
   }
}

if(isset($_GET['a']) && $_GET['a'] == 'bday')
{
    $bday = $database->EscapeString($_POST['bday']);
    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$bday))
    {
        $message = "Das eingegebene Format ist falsch!";
    }
    else if($bday == '0000-00-00')
    {
        $message = "Du musst ein gültiges Geburstags Datum eingeben!";
    }
    else if($player->GetBday() != '0000-00-00')
    {
        $message = "Du kannst dein Geburtstag nur einmalig eintragen!";
    }
    else
    {
        $player->SetBday($bday);
        $message = "Dein Datum wurde eingetragen!";
    }
}

if(isset($_GET['a']) && $_GET['a'] == 'shadowage')
{
    $answer = $database->EscapeString($_POST['answer']);
    if(!is_numeric($answer))
    {
        $message = "Fehler!";
    }
    else
    {
        $player->SetShadowBday($answer);
        $message = "Deine Einstellung wurde gespeichert!";
    }
}

if(isset($_GET['a']) && $_GET['a'] == 'booking')
{
    $days = intval($database->EscapeString($_POST['bdays']));
    $EloFights = $days * 5 + $player->GetDailyMaxElofights();
    $KgFights = $days * 5 + $player->GetPvPMaxFights();
    $NPCFights = $days * 50 + $player->GetDailyNPCFightsMax();
    $Arenapoints = $days * 500 + $player->GetDailyArenaPoints();
    $month = intval(date("n"));
    $day = intval(date("j"));
    $year = date("Y");
    $cday = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $daydifference = $day + $days;
    $restdays = $cday - $day;
    if($daydifference > $cday)
    {
        $message = "Du kannst für diesen Monat nur noch ".$restdays." Tage Pause machen!";
    }
    else if($days < 1 || $days > 14)
    {
        $message = "Die Anzahl der Tage ist ungültig";
    }
    else if($player->GetBookingDays() > 0)
    {
        $message = "Du pausierst bereits!";
    }
    else if($player->GetDailyArenaPoints() < 500 || $player->GetDailyEloFights() > 0 || $player->GetDailyNPCFights() > 0)
    {
        $message = "Du kannst die Pause nur starten wenn du heute noch nichts gemacht hast!";
    }
    else if($player->GetFight() != 0)
    {
        $message = "Du kannst im Fight keine Pausierung starten!";
    }
    else if($player->GetDailyMaxElofights() > 5 || $player->GetDailyNPCFightsMax() > 50 || $player->GetPvPMaxFights() > 5)
    {
        $message = "Du warst bereits in der Pause, bitte gleiche die Kämpfe der letzten Pause aus um erneut in Pause zu gehen!";
    }
    else
    {
        $message = "Du pausierst nun für ".$days." Tage, wir freuen uns wenn du wieder da bist!";
        $player->SetDailyMaxEloFights($EloFights);
        $player->SetBookingDays($days);
        $player->SetDailyNPCFightsMax($NPCFights);
        $player->SetPvPMaxFights($KgFights);
        $player->SetDailyArenaPoints($Arenapoints);
        $player->AddDebugLog(date('H:i:s', time())." ".$player->GetName()." erstellt eine Pause für ".$days." Tage");
    }
}

if(isset($_GET['a']) && $_GET['a'] == 'bookingbroke')
{

        $message = "Du hast die Pause abgebrochen!";
        $player->SetDailyMaxEloFights(5);
        $player->SetBookingDays(0);
        $player->SetDailyNPCFightsMax(50);
        $player->SetPvPMaxFights(5);
        $player->SetDailyArenaPoints(500);
    $player->AddDebugLog(date('H:i:s', time())." ".$player->GetName()." bricht die Pause ab!");
}

if (isset($_GET['a']) && $_GET['a'] == 'speedup')
{
    $action = $actionManager->GetAction($player->GetTravelAction());
    $ortTravelID = 2;
    $planetTravelID = 13;
    if ($action == null || $action->GetID() != $ortTravelID && $action->GetID() != $planetTravelID)
    {
        $message = 'Du bist nicht auf Reise.';
    }
    else if (isset($_POST['factor']) && is_numeric($_POST['factor']) && $_POST['factor'] > 0 && $_POST['factor'] <= 100)
    {
        $factor = $_POST['factor'];
        $minutes = $factor * (($action->GetID() == $ortTravelID) ? 10 : 60);
        $multiplier = 100;
        if($player->GetRace() == "Marine" && in_array($player->GetInventory()->GetShipItemID(), $marineShipArray) && $action->GetID() == $ortTravelID)
            $multiplier = 200;
        else if($player->GetRace() == "Marine" && in_array($player->GetInventory()->GetShipItemID(), $marineShipArray) && $action->GetID() != $ortTravelID)
            $multiplier = 400;
        else if($player->GetRace() == "Marine" && !in_array($player->GetInventory()->GetShipItemID(), $marineShipArray) && $action->GetID() != $ortTravelID)
            $multiplier = 200;
        else if($player->GetRace() == "Pirat" && $action->GetID() != $ortTravelID)
            $multiplier = 200;

        $berry = (round($player->GetLevel() * $multiplier)) * $factor;
        if ($player->GetBerry() < $berry)
        {
            $message = 'Du hast nicht genug Berry.';
        }
        else
        {
            $player->SpeedUpAction($minutes, $berry, 1);
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'wappen')
{
    if($player->ShowWappen())
    {
        $player->SetWappen(0);
        $message = 'Das Wappen wird nicht mehr angezeigt!';
    }
    else
    {
        $player->SetWappen(1);
        $message = 'Das Wappen wird nun angezeigt!';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'picture') {
    $raceimages = array("Pirat1","Pirat2","Pirat3","Pirat4", "Pirat5", "Pirat6", "Pirat7", "Pirat8", "Pirat9", "Pirat10", "Pirat11", "Pirat12", "Pirat13", "Marine1","Marine2","Marine3","Marine4", "Marine5", "Marine6", "Marine7", "Marine8", "Marine9", "Marine10");
    if(!in_array($_POST['raceimage'],$raceimages))
    {
        $message = 'Das Charakteraussehen ist ungültig!';
    }
    else if($player->GetBerry() < 25000)
    {
        $message = 'Du hast nicht genug Berry. Es kostet 25.000 <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>, das Aussehen zu verändern.';
    }
    else
    {
        $player->ChangeRaceImage($_POST['raceimage']);
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'impeldownfree')
{
    if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    {
        $message = 'Dieser Spieler ist ungültig.';
    }
    else
    {
        $target = new Player($database, $_GET['id'], $actionManager);
        if ($player->GetPlanet() == 2)
        {
            $message = "Du befindest dich selbst in Impel Down und kannst keinen anderen Spieler befreien.";
        }
        else if(!$target->IsValid())
        {
            $message = "Ungültiger Spieler.";
        }
        else
        {
            $clan = null;
            $keyid = 0;
            if($target->GetPlace() == 10)
                $keyid = 1;
            if($target->GetPlace() == 11)
                $keyid = 2;
            if($target->GetPlace() == 12)
                $keyid = 3;
            if($target->GetPlace() == 13)
                $keyid = 4;
            if($target->GetPlace() == 14)
                $keyid = 5;
            if($target->GetPlace() == 15)
                $keyid = 6;
            if($target->GetClan() != 0)
                $clan = new Clan($database, $target->GetClan());
            $targetinv = new Inventory($database, $target->GetID());
            if ($target->GetPlanet() != 2)
            {
                $message = "Dieser Spieler befindet sich nicht in Impel Down";
            }
            else if(($clan != null && $player->GetClan() != $target->GetClan() && !in_array($player->GetClan(), $clan->GetAlliances())) && !in_array($player->GetID(), explode(";", $target->GetFriends())))
            {
                $message = "Du kannst diesen Spieler nicht befreien.";
            }
            else if(!isset($_GET['with']))
            {
                $message = 'Ungültige Parameter';
            }
            else if($_GET['with'] == "key" && !$targetinv->HasItemWithID($keyid, $keyid))
            {
                $message = 'Du hast den benötigten Schlüssel nicht.';
            }
            else if (($clan == null || !$clan->PaysBounty()) && $player->GetBerry() < $target->GetPvP() && $_GET['with'] == "berry")
            {
                $message = 'Du hast nicht ausreichend Berry um das Kopfgeld in Höhe von ' . number_format($target->GetPvP(), 0, ',', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/> zu bezahlen!';
            }
            else if ($clan != null && $clan->PaysBounty() && $clan->GetBerry() < $target->GetPvP() && $_GET['with'] == "berry")
            {
                $message = "Deine Bande hat nicht genug Berry um das Kopfgeld zu zahlen, bitte versuche es erneut, dann übernimmst du die Kosten.";
            }
            else
            {
                $msgplus = "";
                if ($clan != null && $clan->PaysBounty() && $_GET['with'] == "berry")
                    $msgplus = ", deine Bande hat die Kosten übernommen.";
                $message = "Du hast " . $target->GetName() . " aus Impel Down für " . number_format($target->GetPvP(), '0', '', '.') . " <img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 5px; height: 20px; width: 13px;'/> befreit" . $msgplus . ".";
                $target->FreeFromImpelDown($player->GetID(), $_GET['with']);
                $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Befreit!', 'Du wudest von ' . $player->GetName() . ' aus Impel Down befreit.', $target->GetName());
                $target->SetImpelDownPopUp(0);
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'reset')
{
    if ($player->GetArank() < 2)
    {
        $message = "Du besitzt die nötigen Rechte nicht.";
    }
    else if ($player->GetFight() != 0)
    {
        $message = "Du kannst dich nur außerhalb eines Kampfes heilen!";
    }
    else
    {
        $player->ResetHealth();
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'block')
{
    if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    {
        $message = 'Dieser Spieler ist ungültig.';
    }
    else
    {
        $blockPlayer = new Player($database, $_GET['id'], $actionManager);
        $blocked = $player->IsBlocked($_GET['id']);
        if($blockPlayer->GetArank() < 1)
        {
            if ($blocked)
            {
                $player->UnBlock($_GET['id']);
                $message = 'Du hast <a href="?p=profil&id='.$blockPlayer->GetID().'">' . $blockPlayer->GetName() . '</a> entblockiert.';
            }
            else
            {
                $player->Block($_GET['id']);
                $message = 'Du hast <a href="?p=profil&id='.$blockPlayer->GetID().'">' . $blockPlayer->GetName() . '</a> blockiert.';
            }
        }
        else
        {
            $message = "Mitglieder vom Team können nicht blockiert werden";
        }

    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'friend')
{
    if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    {
        $message = 'Dieser Spieler ist ungültig.';
    }
    else
    {
        $friendPlayer = new Player($database, $_GET['id']);
        $friends = $player->IsFriend($_GET['id']);
        if($friendPlayer->IsBlocked($player->GetID()) && !$friends)
        {
            $message = 'Du wurdest von diesem Spieler blockiert.';
        }
        else
        {
            if ($friends)
            {
                $player->UnFriend($_GET['id']);
                $friendPlayer->UnFriend($player->GetID());
                $message = 'Du hast <a href="?p=profil&id='.$friendPlayer->GetID().'">' . $friendPlayer->GetName() . '</a> als Freund entfernt.';
            }
            else
            {
                $friendPlayer->FriendRequest($player->GetID());
                $message = 'Du hast <a href="?p=profil&id='.$friendPlayer->GetID().'">' . $friendPlayer->GetName() . '</a> eine Freundschaftsanfrage gesendet.';
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'acceptfriend')
{
    $freq = $player->GetFriendRequests();
    if ($freq == '')
    {
        $message = "Du hast keine offenen Freundschaftsanfragen!";
    }
    else
    {
        $freq = explode(";", $freq);
        $friendPlayer = new Player($database, $freq[0]);
        if (!$friendPlayer)
        {
            $message = 'Dieser Spieler ist ungültig.';
        }
        $friends = $player->IsFriend($freq[0]);
        if ($friends)
        {
            $message = 'Du bist bereits mit <a href="?p=profil&id='.$friendPlayer->GetID().'">' . $friendPlayer->GetName() . '</a> befreundet.';
        }
        else
        {
            $player->Friend($freq[0]);
            $friendPlayer->Friend($player->GetID());
            $player->FriendRequestRemove($freq[0]);
            $message = 'Du hast <a href="?p=profil&id='.$friendPlayer->GetID().'">' . $friendPlayer->GetName() . '</a>s Freundschaftsanfrage angenommen.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'declinefriend')
{
    $freq = $player->GetFriendRequests();
    if ($freq == '')
    {
        $message = "Du hast keine offenen Freundschaftsanfragen!";
    }
    else
    {
        $freq = explode(";", $freq);
        $friendPlayer = new Player($database, $freq[0]);
        if (!$friendPlayer)
        {
            $message = 'Dieser Spieler ist ungültig.';
        }
        $friends = $player->IsFriend($freq[0]);
        if ($friends)
        {
            $message = 'Du bist bereits mit <a href="?p=profil&id='.$friendPlayer->GetID().'">' . $friendPlayer->GetName() . '</a> befreundet.';
        }
        else
        {
            $player->FriendRequestRemove($freq[0]);
            $message = 'Du hast die Freundschaftsanfrage von <a href="?p=profil&id='.$friendPlayer->GetID().'">' . $friendPlayer->GetName() . '</a> abgelehnt.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'acceptcancelsparring')
{
    $otherPlayer = new Player($database, $player->GetSparringPartner(), $actionManager);
    if ($otherPlayer->IsValid())
    {
        $otherPlayer->DoSparringCancel();
        $player->DoSparringCancel();
        $message = 'Du hast das Sparring beendet.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'declinecancelsparring')
{
    $player->DenySparringCancel();
}
else if (isset($_GET['a']) && $_GET['a'] == 'acceptsparring')
{
    $otherPlayer = new Player($database, $player->GetSparringRequest(), $actionManager);
    if (!$otherPlayer->IsValid())
    {
        $message = 'Der andere Spieler ist ungültig.';
        $player->DenySparringRequest();
    }
    else if ($player->GetARank() > 0)
    {
        $message = 'Als Admin kannst du kein Sparring machen.';
        $player->DenySparringRequest();
    }
    else if ($otherPlayer->GetARank() > 0)
    {
        $message = 'Du kannst kein Sparring mit einen Admin machen.';
        $player->DenySparringRequest();
    }
    else if ($otherPlayer->GetAction() != 0)
    {
        $message = 'Dein andere Spieler tut bereits etwas.';
        $player->DenySparringRequest();
    }
    else if ($player->GetAction() != 0)
    {
        $message = 'Du tut bereits etwas.';
        $player->DenySparringRequest();
    }
    else
    {
        $time = $player->GetSparringTime();
        $player->DoSparring($otherPlayer->GetID(), $time);
        $otherPlayer->DoSparring($player->GetID(), $time);

        $charaids = array();
        $charaids[] = $player->GetID();
        $charaids[] = $otherPlayer->GetID();
        LoginTracker::AddInteraction($accountDB, $charaids, 'Sparring', 'opbg');

        $message = 'Ihr trainiert nun zusammen.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'declinesparring')
{
    $player->DenySparringRequest();
}
/*else if (isset($_GET['a']) && $_GET['a'] == 'sparring')
{
	if ($player->GetARank() > 0)
	{
		$message = 'Als Admin kannst du kein Sparring machen.';
	}
	else if ($player->GetAction() != 0)
	{
		$message = 'Du tust bereits eine Aktion.';
	}
	else if (!is_numeric($_POST['hours']) || $_POST['hours'] < 1 || $_POST['hours'] > 24 * 7)
	{
		$message = 'Die Stunden sind ungültig.';
	}
	else if ($player->GetSparringRequest() != 0)
	{
		//keine Message weil popup kommen sollt
	}
	else
	{
		$target = new Player($database, $_GET['id'], $actionManager);
		if (!$target->IsValid())
		{
			$message = 'Dein Partner ist ungültig.';
		}
		else if ($target->GetARank() > 0)
		{
			$message = 'Du kannst kein Sparring mit einen Admin machen.';
		}
		else if ($player->IsMulti($target))
		{
			$message = 'Du kannst mit einen deiner Charaktere kein Sparring machen.';
		}
		else if ($target->GetAction() != 0)
		{
			$message = 'Dein Partner tut bereits etwas.';
		}
		else
		{
			$target->SparringRequest($player->GetID(), $_POST['hours']);
			$message = 'Du hast eine Sparring Anfrage an <a href="?p=profil&id='.$target->GetID().'">' . $target->GetName() . '</a> gesendet.';
		}
	}
}*/
else if (isset($_GET['a']) && $_GET['a'] == 'delete')
{
    if (!isset($_GET['code']) && !isset($_POST['realcheck']))
    {
        $message = 'Du musst die Checkbox ankreuzen.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Du kannst dich während eines Kampfes nicht löschen.';
    }
    else if ($player->GetGroup() != null)
    {
        $message = 'Du musst zunächst die Gruppe verlassen.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du kannst dich während eines Turniers nicht löschen.';
    }
    else if ($player->GetAction() != 0)
    {
        $message = 'Du musst zuerst deine Aktion beenden.';
    }
    else if ($player->GetClan() != 0)
    {
        $message = 'Du musst zuerst deine Bande verlassen.';
    }
    else
    {
        $codeHash = $player->GetName() . '+' . $account->Get('id');
        $code = hash('sha256', 'thisisa' . $codeHash . 'safepw');
        if (isset($_GET['code']) && $_GET['code'] == $code && isset($_GET['id']) && $_GET['id'] == $player->GetID())
        {
            $player->DeleteAccount();
            header('Location: ?p=news');
            exit();
        }
        else if (isset($_GET['id']) && $_GET['id'] != $player->GetID())
        {
            $message = 'Du musst dich mit dem Charakter einloggen, den du löschen willst.';
        }
        else
        {
            $grund = '';
            if(isset($_POST['grund']))
                $grund = $database->EscapeString($_POST['grund']);
            $topic = 'Charakter Löschung';
            $content = '
					Möchtest du wirklich deinen Charakter <b>' . $player->GetName() . '</b> löschen?<br/>
					Wenn du deinen Charakter löschst, kann niemand den Charakter wiederherstellen.<br/>
					Alle deine Daten gehen verloren.<br/>
					<br/>
					Falls du den Charakter nicht löschen willst, dann ignoriere diese Mail.<br/>
					<br/>
					<br/>
					<br/>
					<a href="' . $serverUrl . '?p=profil&a=delete&code=' . $code . '&id=' . $player->GetID() . '">Ich möchte den Charakter löschen.</a>
					<br/>
					<br/>';
            SendMail($account->Get('email'), $topic, $content);
            $result = $database->Update('grund="'.$grund.'"', 'accounts', 'id='.$player->GetID(), 1);
            $message = 'Es wurde eine Mail an deiner E-Mailadresse (' . $account->Get('email') . ') gesendet. Schau auch im Spam Ordner nach.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'deleteadmin')
{
    $otherPlayer = new Player($database, $_GET['id'], $actionManager);
    if ($player->GetARank() != 3)
    {
        $message = "Du bist nicht berechtigt!";
    }
    else if (!$otherPlayer->IsValid())
    {
        $message = "Das ist kein gültiger Charakter!";
    }
    else if ($otherPlayer->GetARank() > 0)
    {
        $message = "Dieser Charakter hat noch einen Admin-Rank.";
    }
    else
    {
        $message = 'Du hast den Charakter ' . $otherPlayer->GetName() . ' gelöscht.';
        $otherPlayer->DeleteAccount(1);
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'usereset')
{
    if (!isset($_POST['pfad']))
    {
        $message = 'Du musst mindestens einen Pfad wählen!';
    }
    else if (!isset($_POST['id']))
    {
        $message = 'Ungültiges Item';
    }
    else if ($player->GetPfad(1) == "None" && $player->GetPfad(2) == "None")
    {
        $message = 'Du hast aktuell keinen Pfad belegt, du kannst den Skill Reset nicht nutzen.';
    }
    else
    {
        $player->DoSkillReset($_POST['id'], $_POST['pfad']);
        $message = 'Du hast 1x Skill Reset verwendet.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'powerup')
{
    if (!isset($_POST['powerup']))
    {
        $message = 'Du musst mindestens eine Technik auswählen.';
    }
    else if (!is_numeric($_POST['powerup']))
    {
        $message = 'Die Technik ist ungültig.';
    }
    else
    {
        $allAttacks = explode(';', $player->GetFightAttacks());
        if ($_POST['powerup'] != 0 && !in_array($_POST['powerup'], $allAttacks))
        {
            $message = 'Du hast dieses Powerup nicht ausgewählt für den Kampf.';
        }
        else
        {
            $player->UpdateStartingPowerup($_POST['powerup']);
            $message = 'Du hast deinen Powerup für den Kampf ausgewählt.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'fightattack')
{
    if (!isset($_GET['aid']) || !is_numeric($_GET['aid']))
    {
        $message = 'Du hast keine Technik ausgewählt.';
    }
    else
    {
        $aid = $_GET['aid'];
        $attacks = explode(';', $player->GetAttacks());
        $fightAttacks = explode(';', $player->GetFightAttacks());
        if (!in_array($aid, $attacks))
        {
            $message = 'Du besitzt diese Technik nicht.';
        }
        else
        {
            $equipped = in_array($aid, $fightAttacks);
            if($equipped)
            {
                //unequip
                if(count($fightAttacks) == 1)
                {
                    $message = "Du kannst nicht alle Techniken abwählen, eine muss mindestens aktiviert sein!";
                }
                else
                {
                    $hasDefaultHits = false;
                    $idx = array_search($aid, $fightAttacks);
                    array_splice($fightAttacks, $idx, 1);
                    foreach ($fightAttacks as $fightAttack)
                    {
                        $newAttack = $attackManager->GetAttack($fightAttack);
                        if($newAttack->GetType() == 1 && $newAttack->GetKP() == 0 && $newAttack->GetEnergy() == 0)
                            $hasDefaultHits = true;
                    }
                    if(!$hasDefaultHits)
                    {
                        $message = 'Du musst mindestens einen Standardangriff ohne Kosten ausgerüstet lassen.';
                    }
                    else
                    {
                        $player->UpdateFightAttacks($fightAttacks);
                    }
                }
            }
            else
            {
                //equip
                $fightAttacks[] = $aid;
                $player->UpdateFightAttacks($fightAttacks);
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'attacks')
{
    if (!isset($_POST['attacks']))
    {
        $message = 'Du musst mindestens eine Technik auswählen.';
    }
    else if (!in_array('1', $_POST['attacks']))
    {
        $message = 'Du musst Schlag als Technik wählen.';
    }
    else if (count($_POST['attacks']) > 18)
    {
        $message = 'Du kannst maximal 18 Techniken wählen.';
    }
    else
    {
        $player->UpdateFightAttacks($_POST['attacks']);
        $message = 'Du hast deine Techniken für den Kampf ausgewählt.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'fakeki' && isset($_POST['fakeki']) && $player->CanFakeKI() && is_numeric($_POST['fakeki']))
{
    $fakeki = intval($_POST['fakeki']);
    if ($fakeki > $player->GetKI() || $fakeki < 0)
    {
        $fakeki = 0;
    }
    $player->ChangeFakeKI($fakeki);
}
else if (isset($_GET['a']) && $_GET['a'] == 'style' && isset($_POST['design']))
{
    $design = $_POST['design'];
    $player->ChangeDesign($design);
}
else if (isset($_GET['a']) && $_GET['a'] == 'background' && isset($_POST['background']))
{
    $background = $_POST['background'];
    $player->ChangeBackground($background);
}
else if (isset($_GET['a']) && $_GET['a'] == 'profilebg' && isset($_POST['profilebg']))
{
    $profilebg = $_POST['profilebg'];
    $player->ChangeProfileBG($profilebg);
}
else if (isset($_GET['a']) && $_GET['a'] == 'header' && isset($_POST['header']))
{
    $header = $_POST['header'];
    $player->ChangeHeader($header);
}
else if (isset($_GET['a']) && $_GET['a'] == 'kopfgeldimage')
{
    //$pvppic = $_POST['kpic'];
    //$player->ChangePvPPic($pvppic);
    $image = $player->GetPvPImage();
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['size'] != 0)
    {
        $imgHandler = new ImageHandler('userdata/kopfgeld/');
        $result = $imgHandler->Upload($_FILES['file_upload'], $image, 251, 191);
        switch ($result)
        {
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
    $player->ChangePvPPic($image);
}
else if (isset($_GET['a']) && $_GET['a'] == 'titel' && (isset($_POST['titel0']) || isset($_POST['titel1']) || isset($_POST['titel2'])))
{
    $titels = $player->GetTitels();
    $titels[] = 0;
    if (isset($_POST['titel0']))
    {
        $titel = $_POST['titel0'];
        if (in_array($titel, $titels))
        {
            $player->ChangeTitel($titel, 0);
        }
        else
        {
            $message = "Du besitzt diesen Titel nicht!";
        }
    }
    if (isset($_POST['titel1']))
    {
        $titel = $_POST['titel1'];
        if (in_array($titel, $titels))
        {
            $player->ChangeTitel($titel, 1);
        }
        else
        {
            $message = "Du besitzt diesen Titel nicht!";
        }
    }
    if (isset($_POST['titel2']))
    {
        $titel = $_POST['titel2'];
        if (in_array($titel, $titels))
        {
            $player->ChangeTitel($titel, 2);
        }
        else
        {
            $message = "Du besitzt diesen Titel nicht!";
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'change')
{
    $text = $_POST['text'];
    $image = $player->GetImage();
    if ($database->HasBadWords($text))
    {
        $message = 'Der Text enthält ungültige Wörter.';
    }
    else if (!$player->IsVerified() && $text != '')
    {
        $message = 'Solange dein Charakter nicht verifiziert wurde, kannst du deine Beschreibung nicht anpassen.';
    }
    else if ($player->GetLevel() < 5 && $text != '')
    {
        $message = 'Du musst mindestens Level 5 sein um deine Beschreibung anpassen zu können.';
    }
    else
    {
        $imagepossible = true;
        if (isset($_FILES['file_upload']) && $_FILES['file_upload']['size'] != 0)
        {
            $imgHandler = new ImageHandler('userdata/profilbilder/');
            $result = $imgHandler->Upload($_FILES['file_upload'], $image);
            switch ($result)
            {
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
        if(!$player->IsVerified())
        {
            $chatactivate = 0;
        }
        else
        {
            $chatactivate = $_POST['chatactivate'];
        }

        $player->ChangeProfile($text, $image, $chatactivate, $_POST['onlineactivate']);
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'changedouriki')
{
$douriki = $database->EscapeString($_POST['fakeki']);

if(!is_numeric($douriki))
{
    $message = "Die Eingabe ist ungültig!";
}
else if(!$player->IsDonator())
{
    $message = "Du kannst die Douriki nicht unterdrücken";
}
else if($douriki > $player->GetKI())
{
    $message = "Der eingegebene Wert darf nicht den der eigentlichen Douriki überschreiten";
}
else if($douriki <= 0)
{
    $player->SetFakeKI($player->GetKI());
    $message = "Es werden nun deine richtige Anzahl an Douriki angezeigt!";
}
else
{
    $player->SetFakeKI($douriki);
    $message = "Du hast deine Douriki erfolgreich unterdrückt!";
}
}
else if (isset($_GET['a']) && $_GET['a'] == 'groupaccept' && $player->GetGroup() == null && $player->GetGroupInvite() != 0)
{
    if ($otherPlayer == null || !$otherPlayer->IsValid())
    {
        $message = 'Der andere Spieler existiert nicht mehr.';
    }
    else
    {
        $group = $otherPlayer->GetGroup();
        $otherPlayer->AddToGroup($player->GetID());
        $group = implode(';', $otherPlayer->GetGroup());
        //SQL is updated by AddToGroup
        $player->SetGroup($group);
        $message = 'Du bist der Gruppe beigetreten.';
    }
    $player->DeclineGroupInvite();
}
else if (isset($_GET['a']) && $_GET['a'] == 'groupdecline' && $player->GetGroupInvite() != 0)
{
    $player->DeclineGroupInvite();
}
else if (isset($_GET['a']) && $_GET['a'] == 'groupkick')
{
    $target = null;
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] != $player->GetID())
    {
        $target = new Player($database, $_GET['id'], $actionManager);
    }

    if (isset($_GET['id']) && $_GET['id'] == $player->GetID())
    {
        $message = 'Du kannst dich nicht selber kicken.';
    }
    else if ($target == null || !$target->IsValid())
    {
        $message = 'Dieser User gibt es nicht.';
    }
    else if ($target->GetGroup() != $player->GetGroup())
    {
        $message = 'Der User ist nicht in deiner Gruppe.';
    }
    else if (!$player->IsGroupLeader() && $player->GetGroup() != null)
    {
        $message = 'Du musst Gruppenleiter sein um andere Spieler zu kicken.';
    }
    else
    {
        $groupSQL = $target->LeaveGroup();
        if ($groupSQL == '')
        {
            $player->SetGroup(null);
            $player->GiveupGroupLeader();
        }
        else
        {
            $player->SetGroup($groupSQL);
        }
        $message = 'Du hast <a href="?p=profil&id='.$target->GetID().'">' . $target->GetName() . '</a> aus der Gruppe entfernt.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'grouppromote')
{
    $target = null;
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] != $player->GetID())
    {
        $target = new Player($database, $_GET['id'], $actionManager);
    }

    if (isset($_GET['id']) && $_GET['id'] == $player->GetID())
    {
        $message = 'Du kannst dich nicht selber zum Leiter ernennen.';
    }
    else if ($target == null || !$target->IsValid())
    {
        $message = 'Dieser User gibt es nicht.';
    }
    else if ($target->GetGroup() != $player->GetGroup())
    {
        $message = 'Der User ist nicht in deiner Gruppe.';
    }
    else if (!$player->IsGroupLeader() && $player->GetGroup() != null)
    {
        $message = 'Du musst Gruppenleiter sein um andere Spieler zu ernennen.';
    }
    else
    {
        $target->MakeGroupLeader();
        $player->GiveupGroupLeader();
        $message = 'Du hast <a href="?p=profil&id='.$target->GetID().'">' . $target->GetName() . '</a> zum Leiter ernannt.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'groupinvite')
{
    $target = null;
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] != $player->GetID())
    {
        $target = new Player($database, $_GET['id'], $actionManager);
    }

    if (isset($_GET['id']) && $_GET['id'] == $player->GetID())
    {
        $message = 'Du kannst dich nicht selber zur Gruppe einladen.';
    }
    else if ($target == null || !$target->IsValid())
    {
        $message = 'Dieser User gibt es nicht.';
    }
    else if ($player->GetARank() < 3 && $player->IsMulti($target))
    {
        $message = 'Du kannst mit einen deiner Charaktere keine Gruppe bilden machen.';
    }
    else if($target->IsBlocked($player->GetID()))
    {
        $message = 'Du wurdest von diesem Spieler blockiert.';
    }
    else if ($target->GetGroup() != null)
    {
        $message = 'Der User ist schon in einer Gruppe.';
    }
    else if ($target->GetGroupInvite() != 0)
    {
        $message = 'Der User wurde schon eingeladen.';
    }
    else if (!$player->IsGroupLeader() && $player->GetGroup() != null)
    {
        $message = 'Du musst Gruppenleiter sein um andere Spieler einzuladen.';
    }
    else if(!$player->IsVerified())
    {
        $message = "Du musst verifiziert sein um andere in eine Gruppe einladen zu können";
    }
    else
    {
        $target->InviteToGroup($player->GetID());
        $message = 'Du hast <a href="?p=profil&id='.$target->GetID().'">' . $target->GetName() . '</a> zur Gruppe eingeladen.';
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'declineEvent')
{
    $player->DeclineEvent();
    if (isset($eventFight) && $eventFight->IsValid() && !$eventFight->IsStarted())
    {
        $eventFight->DeleteFightAndFighters();
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'acceptEvent' && isset($eventFight) && $eventFight->IsValid())
{
    $player->DeclineEvent();
    if (isset($fight))
    {
        $message = "Du befindest dich schon in einem Kampf";
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du kannst während eines Turniers nicht kämpfen.';
    }
    else if ($player->GetLP() < ($player->GetMaxLP() * 0.2))
    {
        $message = 'Du hast nicht genügend LP. Du benötigst mindestens 20% deiner maximalen LP.';
    }
    else if ($eventFight->IsStarted())
    {
        $message = 'Der Kampf hat schon begonnen.';
    }
    else
    {
        $eventFight->Join($player, 0, false);
        header('Location: ?p=fight');
        exit();
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'declineChallenge' && (!isset($challengeFight) || isset($challengeFight) && $challengeFight->GetType() != 7))
{
    $player->DeclineChallenge();
    if (isset($challengeFight) && $challengeFight->IsValid() && $challengeFight->GetChallenge() == $player->GetID())
    {
        $challengeFight->DeleteFightAndFighters();
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'readChallenge' && (!isset($challengeFight) || isset($challengeFight) && $challengeFight->GetType() != 7))
{
    $player->SetClanWarPopup(0);
}
else if (isset($_GET['a']) && $_GET['a'] == 'acceptChallenge' && isset($challengeFight) && $challengeFight->IsValid() && $challengeFight->GetChallenge() == $player->GetID())
{
    if (isset($fight))
    {
        $message = "Du befindest dich schon in einem Kampf";
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du kannst während eines Turniers nicht kämpfen.';
    }
    else if ($challengeFight->GetType() != 0 && $player->GetLP() < $player->GetMaxLP())
    {
        $message = 'Du hast nicht genügend LP. Du benötigst mindestens 20% deiner maximalen LP.';
    }
    /*else if($challengeFight->GetType() == 13 && $player->GetDailyEloFights() >= 5 && $player->GetDailyMaxElofights() == 5)
    {
        $message = 'Du hast bereits 5 Elokämpfe gemacht.';
        $player->DeclineChallenge();
    }*/
    else
    {
        $challenger = $challengeFight->GetTeams()[0][0];
        $challenger = new Player($database, $challenger->GetAcc());
        if(!$challenger->IsValid())
        {
            $message = "Der Herausforderer ist ungültig.";
            $player->DeclineChallenge();
        }
        else if($challengeFight->GetType() == 13 && $player->GetDailyEloEnemyCount($challenger->GetID()) >= 2)
        {
            $message = 'Du hast heute bereits 2 Elo-Kämpfe gegen diesen Spieler durchgeführt, such dir einen anderen Gegner.';
            $player->DeclineChallenge();
        }
        else if($challenger->GetLP() < $challenger->GetMaxLP())
        {
            $message = "Der Herausforderer hat nicht genügend LP.";
            $player->DeclineChallenge();
        }
        else if($challenger->GetKP() < $challenger->GetMaxKP())
        {
            $message = "Der Herausforderer hat nicht genügend KP.";
            $player->DeclineChallenge();
        }
        else if($challengeFight->GetType() == 13 && $challenger->IsEloClose())
        {
            $message = "Der Herausforderer kann derzeit keine Elo-Kämpfe durchführen.";
            $player->DeclineChallenge();
        }
        /*else if($challengeFight->GetType() == 13 && $challenger->GetDailyEloFights() >= 5 && $player->GetDailyMaxElofights() == 5)
        {
            $message = "Der Herausforderer hat bereits 5 Elokämpfe gemacht.";
            $player->DeclineChallenge();
        }*/
        else {
            $player->DeclineChallenge();
            $challengeFight->Join($player, 1, false);
            header('Location: ?p=fight');
            exit();
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'challenge')
{
    if (isset($fight))
    {
        $message = "Du befindest dich schon in einem Kampf";
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du kannst während eines Turniers nicht kämpfen.';
    }
    else if($fight->GetType() == 13)
    {
        $message = "Du kannst diese Funktion nicht für einen Elokampf nutzen!";
    }
    else if (!isset($_POST['type']) || !is_numeric($_POST['type']) || $_POST['type'] != 0 && $_POST['type'] != 13)
    {
        $message = 'Diese Art von Kampf gibt es nicht!';
    }
    else if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    {
        $message = "Dieser Gegner ist ungültig.";
    }
    else if ($_POST['type'] != 0 && ($player->GetLP() < $player->GetMaxLP() || $player->GetKP() < $player->GetMaxKP()))
    {
        $message = 'Deine LP und KP müssen bei 100% liegen.';
    }
    /*else if($_POST['type'] == 13 && $player->GetDailyEloFights() >= 5 && $player->GetDailyMaxElofights() == 5)
    {
        $message = "Du hast heute bereits 5 Elo Kämpfe gemacht";
    }*/
    else if($_POST['type'] == 13 && !$player->IsVerified())
    {
        $message = "Du bist nicht verifiziert, deswegen kannst du keine Elokämpfe machen";
    }
    else if($_POST['type'] == 13 && $player->IsMultiChar())
    {
        $message = 'Du kannst mit einen Zweitcharakter keine Elo-Kämpfe durchgeführen.';
    }
    else if($_POST['type'] == 13 && $player->IsEloClose())
    {
        $message = 'Du darfst aktuell keine Elokämpfe durchführen.';
    }
    else
    {
        $target = new Player($database, $_GET['id'], $actionManager);
        if ($target->GetFight() != 0)
        {
            $message = 'Dein Gegner befindet sich schon im Kampf.';
        }
        else if ($_POST['type'] != 0 && $player->IsMulti($target))
        {
            $message = 'Du kannst mit deinen Charakteren nicht kämpfen.';
        }
        else if($target->IsBlocked($player->GetID()))
        {
            $message = 'Du kannst diesen Charakter nicht herausfordern, da du von ihm blockiert wurdest.';
        }
        else if ($target->GetTournament() != 0)
        {
            $message = 'Dein Gegner kann während eines Turniers nicht kämpfen.';
        }
        else if ($target->GetChallengeFight() != 0)
        {
            $message = 'Dieser Spieler wurde schon herausgefordert.';
        }
        else if ($_POST['type'] != 0 && ($target->GetLP() < $target->GetMaxLP() || $target->GetKP() < $target->GetMaxKP()))
        {
            $message = 'Dein Gegner hat nicht genügend LP oder KP.';
        }
        /*else if($_POST['type'] == 13 && $target->GetDailyEloFights() >= 5 && $player->GetDailyMaxElofights() == 5)
        {
            $message = "Dein Gegner hat heute bereits 5 Elo Kämpfe gemacht";
        }*/
        else if($_POST['type'] == 13 && !$target->IsVerified())
        {
            $message = "Dein Gegner ist nicht verifiziert, deswegen kannst er keine Elokämpfe machen";
        }
        else if($_POST['type'] == 13 && $target->IsMultiChar())
        {
            $message = 'Dein Gegner kann mit einen Zweitcharakter keine Elo-Kämpfe durchgeführen.';
        }
        else if($_POST['type'] == 13 && $target->IsEloClose())
        {
            $message = 'Dein Gegner darf aktuell keine Elokämpfe durchführen.';
        }
        else
        {
            $type = $_POST['type'];
            $mode = '1vs1';
            $name = $player->GetName() . ' vs ' . $target->GetName();
            $team = 1;
            $createdFight = Fight::CreateFight($player, $database, $type, $name, $mode, 0, $actionManager, 0, 0, 0, 0, 0, 0, $target->GetID());
            $createdFight->Join($player, 0, false);
            $target->Challenge($createdFight->GetID());
            if ($createdFight->IsStarted())
            {
                header('Location: ?p=infight');
                exit();
            }
            if($type != 0 && $type != 13)
            {
                $text = 'Der Spieler <a href="'.$serverurl.'index.php?p=profil&id='.$player->GetID().'">'.$player->GetName().'</a> hat eine Kopfgeldanfrage an <a href="'.$serverurl.'index.php?p=profil&id='.$target->GetID().'">'.$target->GetName().'</a> gesendet.';
                $timestamp = date('Y-m-d H:i:s');

                $PMManager = new PMManager($database, 506);
                $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'PvP-Kampf über Profil', $text, 'ShirobakaX', 1);
            }
            $message = 'Du hast <a href="?p=profil&id=' . $target->GetID() . '">' . $target->GetName() . '</a> herausgefordert.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'statspopup')
{
    $player->CloseStatsPopup();
}
else if (isset($_GET['a']) && $_GET['a'] == 'stats')
{
    if ($player->GetFight() != 0)
    {
        $message = 'Du kannst im Kampf deine Stats nicht ändern.';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du kannst im Turnier deine Stats nicht ändern.';
    }
    else if(
        isset($_POST['attack']) && (is_numeric($_POST['attack']) || $_POST['attack'] == '')
        && isset($_POST['lp']) && (is_numeric($_POST['lp']) || $_POST['lp'] == '')
        && isset($_POST['kp']) && (is_numeric($_POST['kp']) || $_POST['kp'] == '')
        && isset($_POST['defense']) && (is_numeric($_POST['defense']) || $_POST['defense'] == '')
    )
    /*else if (
        isset($_POST['attack']) && is_numeric($_POST['attack'])
        && isset($_POST['lp']) && is_numeric($_POST['lp'])
        && isset($_POST['kp']) && is_numeric($_POST['kp'])
        && isset($_POST['defense']) && is_numeric($_POST['defense'])
    )*/
    {
        $totalStats = $player->GetStats();
        $lp = intval($_POST['lp'] != '' ? $_POST['lp'] : 0);
        $kp = intval($_POST['kp'] != '' ? $_POST['kp'] : 0);
        $attack = intval($_POST['attack'] != '' ? $_POST['attack'] : 0);
        $defense = intval($_POST['defense'] != '' ? $_POST['defense'] : 0);
        $stats = $lp + $kp + $attack + $defense;
        if (
            $attack >= 0
            && $lp >= 0
            && $kp >= 0
            && $defense >= 0
            && $totalStats >= $stats
            && $totalStats - $stats >= 0
        )
        {
            if ($player->GetAssignedStats() + $stats <= 2000 || ($player->HasStatsResetted() == 1 && ($player->GetResettedStatsAmount() - $stats) >= 0) || $player->GetARank() >= 2)
            {
                $player->IncreaseStats($lp, $kp, $attack, $defense);
                $player->AddDebugLog(date('d.m.Y - H:i:s', time()) . " ".$stats." Stats wie folgt verteilt: LP ".$lp." AD ".$kp." Attack ".$attack." Defense ".$defense);
            }
            else
            {
                $message = "Diese Statswerte sind ungültig.";
            }

        }
        else
        {
            $message = 'Diese Statswerte sind ungültig.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'copy' && isset($_POST['copy']) && ($_POST['copy'] == 'stats' || $_POST['copy'] == 'all' || $_POST['copy'] == 'equip'))
{
    if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    {
        $message = "Die ID ist ungültig!";
    }
    else if ($player->GetARank() < 2)
    {
        $message = "Du bist nicht dazu berechtigt dies zu tun!";
    }
    else
    {
        $otherPlayer = new Player($database, $_GET['id']);
        if (!$otherPlayer->IsValid())
        {
            $message = "Der Spieler ist ungültig!";
        }
        else if ($otherPlayer->GetID() == $player->GetID())
        {
            $message = "Du kannst deine eigenen Werte nicht kopieren!";
        }
        else
        {
            if ($_POST['copy'] == 'stats')
            {
                $player->CopyStats($otherPlayer);
                $message = 'Du hast die Werte von <a href="?p=profil&id=' . $otherPlayer->GetID() . '">' . $otherPlayer->GetName() . '</a> kopiert.';
            }
            else if ($_POST['copy'] == 'all')
            {
                $player->CopyAll($otherPlayer);
                $message = 'Du hast die LP, AD, Attack, Defense, Angriffe, Pfade, Level, Verwandlung, Story und Nebenstory von <a href="?p=profil&id=' . $otherPlayer->GetID() . '">' . $otherPlayer->GetName() . '</a> kopiert.';
            }
            else
            {
                if($_POST['copy'] == 'equip')
                {
                    $player->CopyEquip($otherPlayer);
                    $message = 'Du hast die Rüstung von <a href="?p=profil&id=' . $otherPlayer->GetID() . '">' . $otherPlayer->GetName() . '</a> kopiert.';
                }
                else
                {
                    $player->SetLP($otherPlayer->GetMaxLP());
                    $player->SetMaxLP($otherPlayer->GetMaxLP());
                    $player->SetKP($otherPlayer->GetMaxKP());
                    $player->SetMaxKP($otherPlayer->GetMaxKP());
                }
            }
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'freefight' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    if($player->GetArank() < 2)
    {
        $message = "Du bist dazu nicht berechtigt.";
    }
    else
    {
        $displayedPlayer = new Player($database, $_GET['id']);
        if(!$displayedPlayer->IsValid())
        {
            $message = "Ungültiger Spieler";
        }
        else if($displayedPlayer->GetFight() == 0)
        {
            $message = "Dieser Spieler befindet sich in keinen Kampf";
        }
        else
        {
            $displayedPlayer->SetFight(0);
            $message = "Du hast den Spieler aus den Kampf befreit.";
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'buy')
{
    if (!$player->IsVerified())
    {
        $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
    }
    else if ($player->GetLevel() < 5)
    {
        $message = 'Du musst mindestens Level 5 sein, um etwas auf dem Markt kaufen zu können.';
    }
    else if (!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = 'Das Item ist ungültig.';
    }
    else if (!isset($_POST['amount']) || !is_numeric($_POST['amount']) || floor($_POST['amount']) <= 0)
    {
        $message = 'Die Anzahl ist ungültig.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Du bist in einem Kampf und kannst das Item nicht kaufen.';
    }
    else
    {
        $amount = floor($_POST['amount']);
        $item = $market->GetItemByID($_POST['id']);
        if ($item == null)
        {
            $message = 'Das Item gibt es nicht auf dem Markt.';
        }
        else if (!$item->IsPremium() && ($player->GetBerry() < ($item->GetPrice() * $amount)))
        {
            $message = 'Du hast nicht genügend Berry.';
        }
        else if ($item->IsPremium() && ($player->GetGold() < ($item->GetPrice() * $amount)))
        {
            $message = 'Du hast nicht genügend Gold.';
        }
        else if ($amount > $item->GetAmount())
        {
            $message = 'So viele Items werden im Markt nicht angeboten.';
        }
        else if ($item->GetSellerID() == $player->GetID())
        {
            $message = 'Du kannst dir nichts selber verkaufen.';
        }
        else if ($item->GetKaeufer() != 0 && $item->GetKaeufer() != $player->GetID())
        {
            $message = 'Du darfst das Item nicht kaufen.';
        }
        else
        {
            $seller = new Player($database, $item->GetSellerID());
            $price = $item->GetPrice() * $amount;
            $priceminus = round(10 * $price / 100);
            $priceseller = $price - $priceminus;
            $statstype = $item->GetStatsType();
            $upgrade = $item->GetUpgrade();
            $owners = array();
            if ($seller->IsBanned())
            {
                $message = 'Der Verkäufer wurde gebannt, du kannst nicht von ihm Kaufen!';
            }
            else if ($seller->GetUserID() == $player->GetUserID())
            {
                $message = 'Du darfst mit keinem deiner Charaktere interagieren.';
            }
            else
            {
                if ($item->GetFormerOwners() != '')
                    $owners = explode(";", $item->GetFormerOwners());
                $buyerAccount = $player->GetUserID();
                $darf = true;
                foreach ($owners as $owner) {
                    $ow = new Player($database, $owner);
                    if ($ow->GetUserID() == $player->GetUserID()) {
                        if ((sizeof($owners) - (array_search($player->GetUserID(), $owners) + 1)) <= 5 && $ow->GetID() != $player->GetID()) {
                            $message = "Du hast dieses Item erst vor kurzem über einen anderen Charakter verkauft!";
                            $darf = false;
                            break;
                        }
                    }
                }
                if ($darf == true) {
                    $owners[] = $item->GetSellerID();
                    $owners = implode(";", $owners);

                    $PMManager = new PMManager($database, $player->GetID());
                    $waehrung = "<img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 5px; height: 20px; width: 13px;'/>";
                    if ($item->IsPremium())
                        $waehrung = "<img src='img/offtopic/GoldSymbol.png' alt='Gold' title='Gold' style='position: relative; top: 5px; height: 20px; width: 20px;'/>";

                    if ($item->GetBieter() != 0 && $item->GetGebot() > 0) {
                        if ($item->GetBieter() != $player->GetID())
                            $offerPlayer = new Player($database, $item->GetBieter());
                        else
                            $offerPlayer = $player;
                        $offerPlayer->ReturnOffer($item->GetGebot(), $item->IsPremium());
                        $database->Update('zeni=' . $offerPlayer->GetBerry() . ', gold=' . $offerPlayer->GetGold(), 'accounts', 'id=' . $offerPlayer->GetID());

                        $text = $item->GetName() . " wurde von <a href='?p=profil&id=" . $player->GetID() . "'>" . $player->GetName() . "</a> gekauft. <br/>Du hast " . number_format($item->GetGebot(), 0, '', '.') . " " . $waehrung . " zurückerhalten.";
                        $text = "<center>" . $text . "</center>";
                        $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', $item->GetName() . ' wurde leider verkauft.', $text, $offerPlayer->GetName(), 1);

                    }
                    $player->BuyItemFrom($itemManager->GetItem($item->GetStatsID()), $itemManager->GetItem($item->GetVisualID()), $statstype, $upgrade, $amount, $price, $item->GetSellerID(), $owners);
                    $market->TakeItem($_POST['id'], $amount);

                    $text = "<img src='img/items/" . $item->GetImage() . ".png' width='80px' height='80px'>Du hast " . number_format($amount, '0', '', '.') . "x " . $item->GetName() . " für " . number_format($price, '0', '', '.') . " " . $waehrung . " an <a href='?p=profil&id=" . $player->GetID() . "'>" . $player->GetName() . "</a> verkauft.";
                    $text = "<center>" . $text . "</center>";
                    $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', $item->GetName() . ' wurde verkauft.', $text, $seller->GetName(), 1);

                    $charaids = array();
                    $charaids[] = $player->GetID();
                    $charaids[] = $item->GetSellerID();
                    $waehrung = "Berry";
                    if ($item->IsPremium())
                        $waehrung = "Gold";
                    $marktkauf = 'Marktkauf ' . number_format($amount, '0', '', '.') . 'x ' . $item->GetName() . ' für ' . number_format($price, '0', '', '.') . ' ' . $waehrung . ' gekauft';
                    LoginTracker::AddInteraction($accountDB, $charaids, $marktkauf, 'opbg');
                    $player->AddDebugLog(date('H:i:s', time()) . " - Marktkauf: " . $item->GetName() . " - Anzahl: " . $amount . " - Einzelpreis: " . $item->GetPrice() . " - Gesamtpreis: " . $price);

                    $message = 'Du hast ' . number_format($amount, '0', '', '.') . 'x ' . $item->GetName() . ' von <a href="?p=profil&id=' . $seller->GetID() . '">' . $seller->GetName() . '</a> gekauft.';
                }
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'bid')
{
    if (!$player->IsVerified())
    {
        $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
    }
    else if ($player->GetLevel() < 5)
    {
        $message = 'Du musst mindestens Level 5 sein, um ein Gebot auf dem Marktplatz platzieren zu können.';
    }
    else if (!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = 'Das Item ist ungültig.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Du bist in einem Kampf und kannst das Item nicht kaufen.';
    }
    else if(!isset($_POST['gebot']) || !is_numeric($_POST['gebot']))
    {
        $message = 'Dein Gebot ist ungültig.';
    }
    else {
        $gebot = $_POST['gebot'];
        $amount = floor($_POST['amount']);
        $item = $market->GetItemByID($_POST['id']);
        if ($item == null)
        {
            $message = 'Das Item gibt es nicht auf dem Markt.';
        }
        else if (!$item->IsPremium() && ($player->GetBerry() < ($item->GetPrice() * $amount)))
        {
            $message = 'Du hast nicht genügend Berry.';
        }
        else if ($item->IsPremium() && ($player->GetGold() < ($item->GetPrice() * $amount)))
        {
            $message = 'Du hast nicht genügend Gold.';
        }
        else if ($amount > $item->GetAmount())
        {
            $message = 'So viele Items werden im Markt nicht angeboten.';
        }
        else if ($item->GetSellerID() == $player->GetID())
        {
            $message = 'Du kannst nicht auf deine eigenen Items bieten.';
        }
        else if ($item->GetKaeufer() != 0 && $item->GetKaeufer() != $player->GetID())
        {
            $message = 'Du kannst nicht auf deine eigenen Items bieten.';
        }
        else if($item->GetBieter() == $player->GetID())
        {
            $message = 'Dein Gebot ist bereits das Höchste.';
        }
        else if($gebot < $item->GetGebot())
        {
            $message = 'Dein Gebot ist kleiner als das aktuelle Gebot.';
        }
        else
        {
            $seller = new Player($database, $item->GetSellerID());
            $price = $item->GetPrice() * $amount;
            $priceminus = round(10 * $price / 100);
            $priceseller = $price - $priceminus;
            $statstype = $item->GetStatsType();
            $upgrade = $item->GetUpgrade();
            $owners = array();
            if ($seller->IsBanned())
            {
                $message = 'Der Verkäufer wurde gebannt, du kannst nicht von ihm Kaufen!';
            }
            else
            {
                if ($item->GetFormerOwners() != '')
                    $owners = explode(";", $item->GetFormerOwners());
                $buyerAccount = $player->GetUserID();
                $darf = true;
                foreach ($owners as $owner) {
                    $ow = new Player($database, $owner);
                    if ($ow->GetUserID() == $player->GetUserID()) {
                        if ((sizeof($owners) - (array_search($player->GetUserID(), $owners) + 1)) <= 5 && $ow->GetID() != $player->GetID()) {
                            $message = "Du hast dieses Item erst vor kurzem über einen anderen Charakter verkauft!";
                            $darf = false;
                            break;
                        }
                    }
                }
                if ($darf == true) {
                    $owners[] = $item->GetSellerID();
                    $owners = implode(";", $owners);
                    $PMManager = new PMManager($database, $player->GetID());

                    $waehrung = "<img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 5px; height: 20px; width: 13px;'/>";
                    if($item->IsPremium())
                        $waehrung = "<img src='img/offtopic/GoldSymbol.png' alt='Gold' title='Gold' style='position: relative; top: 5px; height: 20px; width: 20px;'/>";

                    if ($item->GetBieter() != 0 && $item->GetGebot() > 0)
                    {
                        if($item->GetBieter() != $player->GetID())
                            $offerPlayer = new Player($database, $item->GetBieter());
                        else
                            $offerPlayer = $player;
                        $offerPlayer->ReturnOffer($item->GetGebot(), $item->IsPremium());
                        $database->Update('zeni='.$offerPlayer->GetBerry().', gold='.$offerPlayer->GetGold(),'accounts', 'id='.$offerPlayer->GetID());
                        $text = "Du wurdest von <a href='?p=profil&id=" . $player->GetID()."'>" . $player->GetName() . "</a> bei " . $item->GetName() . " überboten.<br/> Das neue Gebot liegt bei: " . number_format($gebot, 0, '', '.') . " " . $waehrung . ". Du hast " . number_format($item->GetGebot(), 0, '', '.') . " " . $waehrung . " zurückerhalten.";
                        $text = "<center>".$text."</center>";
                        $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', 'Du wurdest überboten bei ' . $item->GetName() . '.', $text, $offerPlayer->GetName(), 1);
                    }

                    $market->SetGebot($item->GetID(), $player->GetID(), $gebot);
                    if($item->IsPremium())
                        $player->SetGold($player->GetGold() - $gebot);
                    else
                        $player->SetBerry($player->GetBerry() - $gebot);
                    $database->Update('zeni='.$player->GetBerry().', gold='.$player->GetGold(), 'accounts', 'id='.$player->GetID());

                    $text = "<img src='img/items/" . $item->GetImage() . ".png' width='80px' height='80px'>Du hast ein Gebot in Höhe von " . number_format($gebot, '0', '', '.') . " " . $waehrung . " für " . $item->GetName() . " von <a href='?p=profil&id=" . $player->GetID()."'>" . $player->GetName() . "</a> bekommen.";
                    $text = "<center>".$text."</center>";
                    $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', 'Gebot für ' . $item->GetName() . ' erhalten.', $text, $item->GetSeller(), 1);

                    $message = 'Du hast ' . number_format($gebot, '0', '', '.') . ' ' . $waehrung . ' auf ' . $item->GetName() . ' von <a href="?p=profil&id=' . $item->GetSellerID().'">' . $item->GetSeller() . '</a> geboten.';
                }
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'retake')
{
    if (!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = 'Das Item ist ungültig.';
    }
    else if (!isset($_POST['amount']) || !is_numeric($_POST['amount']) || floor($_POST['amount']) <= 0)
    {
        $message = 'Die Anzahl ist ungültig.';
    }
    else
    {
        $item = $market->GetItemByID($_POST['id']);
        $amount = floor($_POST['amount']);
        if ($item == null)
        {
            $message = 'Das Item gibt es nicht auf dem Markt.';
        }
        else if ($item->GetSellerID() != $player->GetID())
        {
            $message = 'Das item gehört dir nicht.';
        }
        else if ($amount > $item->GetAmount())
        {
            $message = 'So viele Items werden im Markt nicht angeboten.';
        }
        else if($item->GetBieter() != 0)
        {
            $message = 'Es gibt bereits ein Gebot auf dieses Item.';
        }
        else
        {
            $statsItem = $itemManager->GetItem($item->GetStatsID());
            $visualItem = $itemManager->GetItem($item->GetVisualID());
            $player->AddItems($statsItem, $visualItem, $amount, $item->GetStatsType(), $item->GetUpgrade());
            $market->TakeItem($_POST['id'], $amount);

            $message = 'Du hast ' . number_format($amount,'0', '', '.') . 'x ' . $item->GetName() . ' zurückgenommen.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'remove')
{
    if (!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = 'Das Item ist ungültig.';
    }
    else
    {
        $item = $market->GetItemByID($_POST['id']);
        if ($item == null)
        {
            $message = 'Das Item gibt es nicht auf dem Markt.';
        }
        else if($player->GetArank() < 3)
        {
            $message = 'Du bist dazu nicht berechtigt.';
        }
        else
        {
            $statsItem = $itemManager->GetItem($item->GetStatsID());
            $visualItem = $itemManager->GetItem($item->GetVisualID());
            $seller = new Player($database, $item->GetSellerID());
            $seller->AddItems($statsItem, $visualItem, $item->GetAmount(), $item->GetStatsType(), $item->GetUpgrade());
            $text = "Der Gegenstand ".$item->GetName()." wurde von ".$player->GetName()." vom Marktplatz entfernt.<br/>Der Gegenstand wurde deinem Inventar hinzugefügt.";
            $PMManager->SendPM(0, 'img/system.png', 'System', 'Marktplatz: Gegenstand entfernt', $text, $seller->GetName(), 1);
            $market->RemoveItem($_POST['id']);

            $message = 'Du hast ' . number_format($item->GetAmount(),'0', '', '.') . 'x ' . $item->GetName() . ' vom Marktplatz entfernt.';
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'racechange')
{
    if($player->GetArank() == 3)
    {
        if(!isset($_GET['race']) || $_GET['race'] !== 'Pirat' && $_GET['race'] !== 'Marine')
        {
            $message = "Ungültige Fraktion";
        }
        else {
            $race = $database->EscapeString($_GET['race']);
            $player->FactionChange($race);
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'sell')
{
    if (!$player->IsVerified())
    {
        $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
    }
    else if($player->GetFight() != 0)
    {
        $message = 'Dies kannst du im Kampf nicht tun.';
    }
    else if($player->GetLevel() < 5)
    {
        $message = 'Du musst mindestens Level 5 sein, um etwas auf dem Markt verkaufen zu können.';
    }
    else if (!isset($_POST['item']) || !is_numeric($_POST['item']))
    {
        $message = 'Das Item ist ungültig.';
    }
    else if (!isset($_POST['amount']) || !is_numeric($_POST['amount']) || floor($_POST['amount']) <= 0)
    {
        $message = 'Die Anzahl ist ungültig.';
    }
    else if (!isset($_POST['price']) || !is_numeric($_POST['price']) || floor($_POST['price']) <= 0)
    {
        $message = 'Der Preis ist ungültig.';
    }
    else if (!is_numeric($_POST['offer']) && $_POST['offer'] != '')
    {
        $message = 'Der Gebotspreis ist ungültig.';
    }
    else
    {
        $itemID = $_POST['item'];
        $amount = floor($_POST['amount']);
        $price = floor($_POST['price']);
        $gebot = floor($_POST['offer']);
        $item = $inventory->GetItem($itemID);
        if ($item == null)
        {
            $message = 'Das Item gibt es nicht.';
        }
        else if($item->IsProtected())
        {
            $message = 'Das Item ist geschützt.';
        }
        else
        {
            $itemPrice = $item->GetPrice();

            $buyerName = htmlentities($database->EscapeString($_POST['kaeufername']), ENT_QUOTES | ENT_XML1);
            $buyerPlayer = null;
            $buyerID = 0;
            if($buyerName != null)
            {
                $result = $database->Select('*', 'accounts', 'name="' . $buyerName . '"', 1);
                if ($result) {
                    $row = $result->fetch_assoc();
                    $buyerID = $row['id'];
                    $result->close();
                }
                $buyerPlayer = new Player($database, $buyerID);
            }

            if($buyerPlayer != null && !$buyerPlayer->IsValid())
            {
                $message = 'Diesen Spieler gibt es nicht.';
            }
            else if($buyerPlayer != null && $buyerPlayer->IsBanned())
            {
                $message = 'Dieser Spieler ist gebannt!';
            }
            else if($buyerPlayer != null && $buyerPlayer->GetID() == $player->GetID())
            {
                $message = 'Du kannst das Item nicht an dich selbst verkaufen!';
            }
            else
            {
                $auras = array(125, 127, 128, 129, 130, 294, 295, 296, 297, 298, 299, 300);
                $pfaditems = array(52, 53, 54, 55, 56, 57);
                $chestItems = array(319,320,102,318);
                if ($item->IsMarktplatz() == 0)
                {
                    $message = 'Das Item kann nicht verkauft werden.';
                }
                else if ($item->GetWear() > 0 && $itemManager->GetItem($item->GetVisualID())->GetItemUses() != 0)
                {
                    $message = "Abgenutzte Items kannst du nicht auf dem Marktplatz verkaufen!";
                }
                else if ($item->IsEquipped())
                {
                    $message = 'Ein ausgerüstet Item kannst du nicht verkaufen.';
                }
                else if ($item->GetType() != 3 && $gebot > 0)
                {
                    $message = 'Dieses Item kann kein Gebot annehmen.';
                }
                else if($gebot > $_POST['price'])
                {
                    $message = 'Das Gebot kann nicht höher sein als der Preis.';
                }
                else if ($item->GetAmount() < $amount)
                {
                    $message = 'Du besitzt nicht genügend davon.';
                }
                else if ($item->GetType() != 3 && $market->HasItemInside($item->GetStatsID(), $item->GetVisualID(), $item->GetStatsType(), $item->GetUpgrade(), $player->GetID()))
                {
                    $message = 'Du hast so ein Item schon im Markt.';
                }
                else if(in_array($item->GetVisualID(), $auras))
                {
                    $message = 'Dieses Item kann so nicht verkauft werden.';
                }
                else if(in_array($item->GetStatsID(), $pfaditems) && $price < 5000)
                {
                    $message = "Pfaditems können nicht unter 5000 Berry pro Item verkauft werden.";
                }
                else if(in_array($item->GetStatsID(), $chestItems) && $price < 5000)
                {
                    $message = "Diese Truhen können nicht unter 5000 Berry verkauft werden.";
                }
                else
                {
                    if($buyerPlayer != null)
                    {
                        $buyerID = $buyerPlayer->GetID();
                    }
                    $owners = $item->GetFormerOwners();
                    $player->RemoveItems($item, $amount);
                    $market->AddItem($item->GetStatsID(), $item->GetVisualID(), $item->GetStatsType(), $item->GetUpgrade(), $amount, $price, $player, $owners, $buyerID, $gebot);
                    $waehrung = '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
                    if($item->IsPremium())
                        $waehrung = '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>';

                    if($buyerPlayer == null)
                    {
                        $message = 'Du hast ' . number_format($amount,'0', '', '.') . 'x ' . $item->GetName() . ' für ' . number_format($price,'0', '', '.') . ' ' . $waehrung . ' in den Marktplatz gestellt.';
                    }
                    else
                    {
                        $message = 'Du hast ' . number_format($amount,'0', '', '.') . 'x ' . $item->GetName() . ' für ' . number_format($price,'0', '', '.') . ' ' . $waehrung . ' an <a href="?p=profil&id='.$buyerPlayer->GetID().'">'.$buyerPlayer->GetName().'</a> in den Marktplatz gestellt.';
                    }
                }
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'meld' && $player->GetName() != 'Google' && isset($_GET['id']))
{
    $target = new Player($database, $_GET['id']);
    if($target->IsValid()) {
        $option = $database->EscapeString($_POST['option']);
        $textarea = $database->EscapeString($_POST['reason']);
        $text = "Profilmeldung von ".$player->GetName()." - Art der Meldung: ".$option." - Gemeldetes Profil: <a href='?p=profil&id=".$target->GetID()."'>".$target->GetName()."</a> - Grund der Meldung: ".$textarea;
        $player->AddMeldung($text, $player->GetID(), "System", 2);
        $message = "Vielen dank für die Meldung.";
    }
}
else if(isset($_GET['a']) && $_GET['a'] == "namechange")
{
    if($player->GetArank() < 3)
    {
        $message = "Du darfst das nicht!";
    }
    else if(!isset($_GET['newName']) || !isset($_GET['id']))
    {
        $message = "Falsche Parameter.";
    }
    else
    {
        $target = new Player($database, $_GET['id']);
        $newName = $database->EscapeString($_GET['newName']);
        $result = $database->Select('id', 'accounts', 'name like "'.$newName.'"');
        if($result && $result->num_rows > 0)
        {
            $message = "Dieser Name wird bereits verwendet.";
        }
        else if(!$target->IsValid())
        {
            $message = "Dies ist kein gültiger Spieler!";
        }
        else
        {
            $oldName = $target->GetName();
            $target->ChangeUserName($oldName, $newName);
            $message = "Du hast den Namen von ".$oldName." zu ".$newName." geändert.";
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'revive' && $player->GetPlanet() == 2)
    {
        $reviveTime = $player->GetReviveTime();
        if ($reviveTime < 0)
            $reviveTime = 0;
        if ($reviveTime != 0 && $player->GetRace() == "Pirat" && $player->GetReviveDays() == 0)
        {
            $timeDiffMinutes = $reviveTime / 60;
            $timeDiffHours = $timeDiffMinutes / 60;
            $timeDiffDays = $timeDiffHours / 24;
            $time = '';
            if ($reviveTime < 60)
            {
                $timeDiffSeconds = floor($reviveTime);
                $time .= number_format($timeDiffSeconds,'0', '', '.');
                if ($timeDiffSeconds == 1) $time .= ' Sekunde';
                else $time .= ' Sekunden';
            }
            else if ($timeDiffMinutes < 60)
            {
                $timeDiffMinutes = floor($timeDiffMinutes);
                $time .= number_format($timeDiffMinutes,'0', '', '.');
                if ($timeDiffMinutes == 1) $time .= ' Minute';
                else $time .= ' Minuten';
            }
            else if ($timeDiffHours < 24)
            {
                $timeDiffHours = floor($timeDiffHours);
                $time .= number_format($timeDiffHours,'0', '', '.');
                if ($timeDiffHours == 1) $time .= ' Stunde';
                else $time .= ' Stunden';
            }
            else
            {
                $timeDiffDays = floor($timeDiffDays);
                $time .= number_format($timeDiffDays,'0', '', '.');
                if ($timeDiffDays == 1) $time .= ' Tag';
                else $time .= ' Tagen';
            }
            $message = 'Du kannst dich erst in ' . $time . ' befreien.';
        }
        else if ($player->GetFight() != 0)
            $message = 'Du kannst dich während eines Kampfes nicht befreien.';
        else
        {
            if($reviveTime != 0 && $player->GetRace() == "Marine")
            {
                $price = $player->GetPvP() / 2;
                $prices = ceil($player->GetBerry() - $price);
                if($prices >= 0)
                {
                    $player->Revive();
                    $message = 'Du wurdest befreit.';
                    $player->SetBerry($prices);
                    $player->SetImpelDownPopUp(0);
                }
                else
                {
                    $message = "Du hast nicht genug Genug Berry um dich zu befreien. Die Kosten betragen: ".number_format(ceil($price),0,'', '.')." Berry";
                }

            }
            else if($reviveTime != 0 && $player->GetReviveDays() > 0)
            {
                $price = $player->GetPvP() / 4;
                $prices = ceil($player->GetBerry() - $price);
                if($prices >= 0)
                {
                    $player->Revive();
                    $message = 'Du wurdest befreit.';
                    $player->SetBerry($prices);
                    $player->SetImpelDownPopUp(0);
                }
                else
                {
                    $message = "Du hast nicht genug Genug Berry um dich zu befreien. Die Kosten betragen: ".number_format(ceil($price),0,'', '.')." Berry";
                }

            }
            else
            {
                $player->Revive();
                $message = 'Du wurdest befreit.';
                $player->SetImpelDownPopUp(0);
            }
        }
    }

function generateRandomString($length = 30): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++)
    {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>