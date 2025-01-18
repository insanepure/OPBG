<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/promocodes/promocode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
if($_GET['p'] == "promocode" && $_GET['active'] == 'code') {
    $id = $player->GetID();
    $timestamp = time();
    $datum = date("d.m.Y - H:i", $timestamp);
    $promocodes = $_POST['pcode'];
    $promocheck = $database->Select('*', 'promocodes', 'promocode="' . $promocodes . '"');
    $promocode = $promocheck->fetch_assoc();
    $usecheck = $database->Select('*', 'usepromocodes', 'promocode="' . $promocodes . '" AND userid="' . $id . '"');
    $use = $usecheck->num_rows;
    $datum = $promocode['erstellt'];
    $d = explode(".", $datum);
    $unix = mktime(0, 0, 0, $d[1], $d[0], $d[2]);
    $stamp = time();
    $diff = $stamp - $unix;
    $diff = $diff / 86400;
    $itemmanager = new ItemManager($database);
    if ($promocheck->num_rows != 1) {
        $message = "Der Promocode ist entweder falsch oder ungültig";
    } else if ($promocode['zbegrenzt'] != 'nein' && floor($diff) > $promocode['dauer']) {
        $message = "Der Promocode ist abgelaufen und somit ungültig";
    } else if ($promocode['ownerid'] != 1 && $player->GetID() != $promocode['ownerid']) {
        $message = "Dieser Promocode ist nicht für dich bestimmt";
    } else if ($promocode['ownerlevel'] != 0 && $player->GetLevel() != $promocode['ownerlevel']) {
        $message = "Du kannst diesen Promocode maximal bis Level " . $promocode['ownerlevel'] . " einlösen";
    } else if ($use >= 1) {
        $message = "Du hast den Promocode schon genutzt!";
    } else if (!is_numeric($promocodes)) {
        $message = "Es funktionieren nur zahlen!";
    } else if ($promocode['item'] == 0 && $promocode['berry'] == 0 && $promocode['gold'] == 0 && $promocode['titel'] == 0 && $promocode['stats'] == 0) {
        $message = "Dieser Promocode enthält keine Belohnungen!";
    } else {
        $useitem = $promocode['item'];
        $useberry = $promocode['berry'];
        $usegold = $promocode['gold'];
        $usestats = $promocode['stats'];
        $useamount = $promocode['amount'];
        if ($useitem != 0 && $useamount == 0) $useamount = 1;
        $titel = $promocode['titel'];
        $user = $player->GetID();
        $amountberry = $player->GetBerry() + $useberry;
        $amountgold = $player->GetGold() + $usegold;
        $amountstats = $player->GetStats() + $usestats;
        $pTitels = $player->GetTitels();
        if ($titel != 0) {
            if (!$player->HasTitel($titel)) {
                $pTitels[] = $titel;
                $pTitels = implode(";", $pTitels);
                $player->SetTitels($pTitels);
            }
        }
        $pTitelsS = implode(";", $player->GetTitels());
        if ($useitem != 0) {
            $item = $itemmanager->GetItem($useitem);
            $player->AddItems($item, $item, $useamount);
        }
        //$result = $database->Update('amount="'.$amountitem.'"', 'inventory', 'ownerid="'.$user.'" AND statsid="'.$useitem.'"');
        $resultz = $database->Update('zeni="' . $amountberry . '", gold="' . $amountgold . '", stats="' . $amountstats . '", titels="' . $pTitelsS . '"', 'accounts', 'id="' . $user . '"');
        $results = $database->Insert('userid, promocode, usedate', '"' . $user . '", "' . $promocodes . '", "' . $datum . '"', 'usepromocodes');
        $player->SetBerry($amountberry);
        $player->SetGold($amountgold);
        $player->SetStats($amountstats);
        $message = "Der Promocode wurde eingelöst!";
        echo "<script language='javascript'>document.location.reload;</script>";
        $itemcheckz = $database->Select('*', 'items', 'id="' . $useitem . '"');
        $itempm = $itemcheckz->fetch_assoc();
        $titelcheckz = $database->Select('*', 'titel', 'id="' . $promocode['titel'] . '"');
        $titelpm = $titelcheckz->fetch_assoc();
        $id = 0;
        $image = "img/system.png";
        $name = 'System';
        $title = "Promocode " . $promocodes;
        if ($promocode['item'] == 0) {
            $itemtext = "Kein Item";
        } else {
            $itemtext = number_format($useamount, 0, '', '.') . "x " . $itempm['name'];
        }
        if ($promocode['titel'] == 0) {
            $titeltext = "Kein Titel";
        } else {
            $titeltext = $titelpm['name'];
        }
        $text = "Hey " . $player->GetName() . "
        <br />
        <br />
        Du hast folgenden Promocode eingelöst: " . $promocodes . " durch das einlösen dieses Codes hast du folgende Belohnungen erhalten.
        <br />
        <br />
        " . $itemtext . "
        <br />
        <br />
        Berry: " . number_format($useberry, 0, '', '.') . "
        <br />
        <br />
        Gold: " . number_format($usegold, 0, '', '.') . "
        <br />
        <br />
        Titel: " . $titeltext . "
        <br />
        <br />
        Wir hoffen dir damit eine kleine Freude für zwischendurch gemacht zu haben
        <br />
        <br />
        Dein Team von OPBG";
        $chara = $player->GetName();
        $PMManager = new PMManager($database, $player->GetID());
        $PMManager->SendPM($id, $image, $name, $title, $text, $chara, 1);
    }
}

if($_GET['p'] == 'promocode' && $_GET['create'] == 'code')
{
    if($player->GetArank() == 3)
    {
        $admin = $player->GetName();
        $acode = $_POST['aprocode'];
        $adate = $_POST['adate'];
        $owner = $_POST['aownerid'];
        $ownerlevel = $_POST['aownerlevel'];
        $answer = $_POST['danswer'];
        $begrenzung = $_POST['dzbegrenzung'];
        $item = $_POST['aitem'];
        $amount = $_POST['aamount'];
        $berry = $_POST['aberry'];
        $gold = $_POST['agold'];
        $titel = $_POST['atitel'];
        $stats = $_POST['astats'];
        $datum = date("d.m.Y");
// Checken ob alles was verteilt werden soll auch existiert!
// TITEL
        $titeclcheck = $database->Select('*', 'titel', 'id="'.$titel.'"');
// ITEM
        $itemcheck = $database->Select('*', 'items', 'id="'.$item.'"');
// USER
        $ownercheck = $database->Select('*', 'accounts', 'id="'.$owner.'"');
// Promocode
        $promocheck = $database->Select('*', 'promocodes', 'promocode="'.$acode.'"');
// Ende
        if(!is_numeric($ownerlevel) && !is_numeric($owner) && !is_numeric($item) && !is_numeric($amount) && !is_numeric($berry) && !is_numeric($gold) && !is_numeric($titel) && !is_numeric($stats))
        {
            $message = "Fehler! In einem Feld wo nur zahlen stehen dürfen hast du etwas anderes, verbotenes eingetragen!";
        }
        else if($adate != $datum)
        {
            $message = "Fehler! Dein eingetragenes Datum entspricht nicht dem von heute!";
        }
        else if($answer == "nein" && $begrenzung != 0)
        {
            $message = "Fehler! Um eine Dauer einstellen zu können musst die die Zeitbegrenzung auf 'Ja' umstellen!";
        }
        else if($answer == "Ja" && $begrenzung == 0)
        {
            $message = "Fehler! Bitte gib einer Dauer in Tagen an wenn du die Zeitbegrenzung aktiviert hast";
        }
        else if($titeclcheck->num_rows == 0 && $titel != 0)
        {
            $message = "Fehler! Der Titel existiert nicht";
        }
        else if($itemcheck->num_rows == 0 && $item != 0)
        {
            $message = "Fehler! Das Item existiert nicht";
        }
        else if($ownercheck->num_rows == 0 && $owner != 1)
        {
            $message = "Fehler! Der User existiert nicht";
        }
        else if($promocheck->num_rows != 0)
        {
            $message = "Fehler! Der Promocode existiert bereits";
        }
        else
        {
            $result = $database->Insert('promocode, erstellt, ownerid, ownerlevel, createowner, zbegrenzt, dauer, item, amount, berry, gold, titel, stats', '"'.$acode.'", "'.$adate.'", "'.$owner.'", "'.$ownerlevel.'", "'.$admin.'", "'.$answer.'", "'.$begrenzung.'", "'.$item.'", "'.$amount.'", "'.$berry.'", "'.$gold.'", "'.$titel.'", "'.$stats.'"', 'promocodes');
            $message = "Glückwunsch du hast folgenden Promocode erstellt: ".$acode." ";
        }
    }
    else
    {
        header('Location: '.$_SERVER['HTTP_HOST']);
        exit;
    }
}
?>