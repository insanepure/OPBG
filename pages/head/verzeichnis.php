<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/verzeichnis/verzeichnis.php';
include_once $_SERVER['DOCUMENT_ROOT'].'../../classes/chat/chat.php';

$verzeichnis = new Verzeichnis($database);
$chat = new Chat($accountDB, session_id());

$verzeichnisentry = null;
if(isset($_GET['name']))
{
    $verzeichnisentry = $verzeichnis->LoadEntryName($_GET['name']);
}

if(isset($_GET['id']))
{
    $verzeichnisentry = $verzeichnis->LoadEntry($_GET['id']);
}

if(isset($_GET['p']) == 'verzeichnis' && isset($_GET['createarticle']) && $_GET['createarticle'] == 'fromuser')
{
    $dname = $database->EscapeString($_POST['dname']);
    $tf = $database->EscapeString($_POST['tf']);
    $race = $database->EscapeString($_POST['race']);
    $jname = $database->EscapeString($_POST['jname']);
    $gender = $database->EscapeString($_POST['geschlecht']);
    $old = $database->EscapeString($_POST['old']);
    $bday = $database->EscapeString($_POST['bday']);
    $reg = "/^([0-9+]{2}).([0-9+]{2}).([0-9+]{4})$/";
    $size = $database->EscapeString($_POST['bigest']);
    $from = $database->EscapeString($_POST['from']);
    $family = $database->EscapeString($_POST['family']);
    $bande = $database->EscapeString($_POST['pbande']);
    $pos = $database->EscapeString($_POST['position']);
    $pvp = $database->EscapeString($_POST['kgeld']);
    $dsynchro = $database->EscapeString($_POST['dsynchro']);
    $jsynchro = $database->EscapeString($_POST['jsynchro']);
    $danime = $database->EscapeString($_POST['danime']);
    $dmanga = $database->EscapeString($_POST['dmanga']);
    $description = $database->EscapeString($_POST['beschreibung']);
    $description = $description."\n \n [center]Erstellt von [url=?p=profil&id=".$player->GetID()."][b]".$player->GetName()."[/b][/url]\n [img]".$player->GetImage()."[/img][/center]";


    if($gender != 'Männlich' && $gender != 'Weiblich' && $gender != 'Unbekannt')
    {
        $message = "Dein Geschlecht ist ungültig!";
    }
    else if(empty($dname) || empty($tf) || empty($race) || empty($jname) || empty($gender) || empty($old) || empty($bday) || empty($size) || empty($from) || empty($family) || empty($bande) ||
        empty($pos) || empty($pvp) || empty($dsynchro) || empty($jsynchro) || empty($danime) || empty($dmanga) || empty($description))    {
        $message = "Bitte fülle alle Felder aus! Sollte was bei einem nicht klar sein dann trag bitte 'Unbekannt' ein!";
    }
    else if(!$player->IsLogged())
    {
        $message = "Entschuldigung für deine Mühe, aber du musst eingeloggt sein um einen Artikel zu erstellen";
    }
    else if(!$player->IsVerified())
    {
        $message = "Entschuldigung für deine Mühe, aber du musst verifiziert sein um einen Artikel zu erstellen";
    }
    else
    {
        $message = "test";
        $variables = "
                    `image`,
                    `name`,
                    `romaji`,
                    `teufelsfrucht`,
                    `rasse`,
                    `geschlecht`,
                    `alter`,
                    `birthday`,
                    `groesse`,
                    `herkunft`,
                    `family`,
                    `piratenbande`,
                    `position`,
                    `voiceactorger`,
                    `voiceactorjap`,
                    `description`,
                    `anime`,
                    `manga`,
                    `kopfgeld`,
                    `last_editor`,
                    `uid`,
                    `mainpage`,
                    `creator`
                 ";
        $values = "
                    'Leer',
                    '".$dname."',
                    '".$jname."',
                    '".$tf."',
                    '".$race."',
                    '".$gender."',
                    '".$old."',
                    '".$bday."',
                    '".$size."',
                    '".$from."',
                    '".$family."',
                    '".$bande."',
                    '".$pos."',
                    '".$dsynchro."',
                    '".$jsynchro."',
                    '".$description."',
                    '".$danime."',
                    '".$dmanga."',
                    '".$pvp."',
                    '".$player->GetID()."',
                    '".uniqid('', true)."',
                    '0',
                    '".$player->GetID()."'
                ";
        $database->Insert($variables, $values, 'verzeichnis_new');
        $message = "Dein Artikel wurde zur Überprüfung gespeichert, sobald einer vom Team sich den angeschaut hat wird er freigegeben!";
    }
}
else if(isset($_GET['p']) == 'verzeichnis' && isset($_GET['seenew']) && $_GET['seenew'] == 'article' && isset($_GET['activate']) && $_GET['activate'] == 'article' && isset($_GET['id']) && is_numeric($_GET['id']) && $player->GetName() == "SloadX")
{
    if($player->GetArank() >= 2)
    {
        $ArtikelCheck = $database->Select('*', 'verzeichnis', 'id="'.$_GET['id'].'"');
        $artikel = $ArtikelCheck->fetch_assoc();
        $result = $database->Update('status=0', 'verzeichnis', 'id="'.$_GET['id'].'"');
        $message = "Dieser Artikel wurde erfolgreich freigeschaltet";
        $creator = new Player($database, $artikel['creator']);
        $creator->SetBerry($creator->GetBerry() + 100);
        $creator->SetGold($creator->GetGold() + 1);
        $text = '/system Ein neuer Artikel zum Charakter '.$artikel['name'].' wurde erstellt!';
        $text = str_replace('{@BGMSG@}', '', $text);
        $text = str_replace('{@BG@}', '', $text);
        $result = $chat->SendMessage($text);
    }
    else
    {
        $message = "Du hast keinen Zugriff auf diese Option!";
    }
}

else if(isset($_GET['p']) == 'verzeichnis' && isset($_GET['seenew']) && $_GET['seenew'] == 'article' && isset($_GET['delete']) && $_GET['delete'] == 'article' && isset($_GET['id']) && is_numeric($_GET['id']) && $player->GetName() == "SloadX")
{
    if($player->GetArank() == 3)
    {
        $result = $database->Delete('verzeichnis', 'id="'.$_GET['id'].'"');
        $message = "Du hast den Artikel gelöscht!";
    }
    else
    {
        $message = "Du hast keinen Zugriff auf diese Funktion";
    }
}

else if(isset($_GET['p']) == 'verzeichnis' && isset($_GET['a']) == 'edit' && is_numeric($_GET['aid']) && $_GET['aid'])
{
    $id = $_GET['aid'];
    $uid = $database->EscapeString($_POST['uid']);
    $img = $database->EscapeString($_POST['picture']);
    $dname = $database->EscapeString($_POST['dname']);
    $tf = $database->EscapeString($_POST['tf']);
    $race = $database->EscapeString($_POST['race']);
    $jname = $database->EscapeString($_POST['jname']);
    $gender = $database->EscapeString($_POST['geschlecht']);
    $old = $database->EscapeString($_POST['old']);
    $bday = $database->EscapeString($_POST['bday']);
    $size = $database->EscapeString($_POST['bigest']);
    $from = $database->EscapeString($_POST['from']);
    $family = $database->EscapeString($_POST['family']);
    $bande = $database->EscapeString($_POST['pbande']);
    $pos = $database->EscapeString($_POST['position']);
    $pvp = $database->EscapeString($_POST['kgeld']);
    $dsynchro = $database->EscapeString($_POST['dsynchro']);
    $jsynchro = $database->EscapeString($_POST['jsynchro']);
    $danime = $database->EscapeString($_POST['danime']);
    $dmanga = $database->EscapeString($_POST['dmanga']);
    $description = $database->EscapeString($_POST['beschreibung']);
    $article = $database->Select('*', 'verzeichnis', 'id='.$id.' AND uid="'.$uid.'"');

    if($gender != 'Männlich' && $gender != 'Weiblich' && $gender != 'Unbekannt')
    {
        $message = "Dein Geschlecht ist ungültig!";
    }
    else if(empty($dname) || empty($tf) || empty($race) || empty($jname) || empty($gender) || empty($old) || empty($bday) || empty($size) || empty($from) || empty($family) || empty($bande) ||
        empty($pos) || empty($pvp) || empty($dsynchro) || empty($jsynchro) || empty($danime) || empty($dmanga) || empty($description))
    {
        $message = "Bitte fülle alle Felder aus! Sollte was bei einem nicht klar sein dann trag bitte 'Unbekannt' ein!";
    }
    else if(!$player->IsLogged())
    {
        $message = "Entschuldigung für deine Mühe, aber du musst eingeloggt sein um einen Artikel zu erstellen";
    }
    else if(!$player->IsVerified())
    {
        $message = "Entschuldigung für deine Mühe, aber du musst verifiziert sein um einen Artikel zu erstellen";
    }
    /*else if($player->GetArank() < 2)
    {
        $message = "Du hast keine Befugnis diesen Artikel zu bearbeiten!";
    }*/
    else if(!$article || $article->num_rows == 0)
    {
        $message = "Ungültiger Artikel!";
    }
    else
    {
        $article = $article->fetch_assoc();
        if($player->GetArank() < 2)
        {
            $result = $database->Select('*', 'verzeichnis_edit', 'uid="'.$uid.'"');
            if($result && $result->num_rows > 0)
            {
                $message = "Es ist bereits eine Änderung für diesen Artikel ausstehend.";
            }
            else
            {
                $variables = "
                    `image`,
                    `name`,
                    `romaji`,
                    `teufelsfrucht`,
                    `rasse`,
                    `geschlecht`,
                    `alter`,
                    `birthday`,
                    `groesse`,
                    `herkunft`,
                    `family`,
                    `piratenbande`,
                    `position`,
                    `voiceactorger`,
                    `voiceactorjap`,
                    `description`,
                    `anime`,
                    `manga`,
                    `kopfgeld`,
                    `last_editor`,
                    `uid`,
                    `mainpage`,
                    `creator`
                 ";
                $values = "
                    '".$img."',
                    '".$dname."',
                    '".$jname."',
                    '".$tf."',
                    '".$race."',
                    '".$gender."',
                    '".$old."',
                    '".$bday."',
                    '".$size."',
                    '".$from."',
                    '".$family."',
                    '".$bande."',
                    '".$pos."',
                    '".$dsynchro."',
                    '".$jsynchro."',
                    '".$description."',
                    '".$danime."',
                    '".$dmanga."',
                    '".$pvp."',
                    '".$player->GetID()."',
                    '".$uid."',
                    '".$article['mainpage']."',
                    '".$article['creator']."'
                ";
                $database->Insert($variables, $values, 'verzeichnis_edit');
                $message = "Deine Bearbeitung wurde zur Prüfung eingereicht.";
            }
        }
        else {
            $update = "
                    `image` = '" . $img . "',
                    `name` = '" . $dname . "',
                    `romaji` = '" . $jname . "',
                    `teufelsfrucht` = '" . $tf . "',
                    `rasse` = '" . $race . "',
                    `geschlecht` = '" . $gender . "',
                    `alter` = '" . $old . "',
                    `birthday` = '" . $bday . "',
                    `groesse` = '" . $size . "',
                    `herkunft` = '" . $from . "',
                    `family` = '" . $family . "',
                    `piratenbande` = '" . $bande . "',
                    `position` = '" . $pos . "',
                    `voiceactorger` = '" . $dsynchro . "',
                    `voiceactorjap` = '" . $jsynchro . "',
                    `description` = '" . $description . "',
                    `anime` = '" . $danime . "',
                    `manga` = '" . $dmanga . "',
                    `kopfgeld` = '" . $pvp . "',
                    `last_editor` = '" . $player->GetID() . "'
                 ";
            $database->Update($update, 'verzeichnis', 'id=' . $id . ' AND uid="' . $uid . '"');
            $message = "Du hast den Artikel erfolgreich bearbeitet";
        }
    }
}