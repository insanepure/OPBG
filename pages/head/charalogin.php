<?php
$daylilogincheck = date("d.m.y");
$locked = false;
if (isset($_GET['id']) && is_numeric($_GET['id']))
{
    $loginPlayer = new Player($database, $_GET['id']);
    $result = $database->Select('*', 'accounts', 'userid='.$loginPlayer->GetUserID().' AND ismulti=0');
    $isDonor = 1;
    if($result && $result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            $otherChar = new Player($database, $row['id']);
            if(!$otherChar->IsDonator())
                $isDonor = 0;
        }
    }
    if($charLoginActive || $loginPlayer->GetArank() >= 2)
    {
        # Geburtstag! => Sload
        $bdaytime = date("m-d");
        $nowtime = explode('-', $loginPlayer->GetBday());
        $bdays = $nowtime[1]."-".$nowtime[2];
        if($bdaytime == $bdays && $loginPlayer->GetBdaySuprise() == 0 && $loginPlayer->GetAge() >= 8)
        {
            $PMManager = new PMManager($database, $loginPlayer->GetID());
            $absender = 649;
            $image = "img/system.png";
            $name = 'Support';
            $title = 'Alles Gute zum Geburtstag!';
            $text = "Hey, ".$loginPlayer->GetName().".<br/><br/>
            
            Das gesamte Team von OPBG wünscht dir alles Gute zu deinem ".$loginPlayer->GetAge().". Geburtstag!<br/>
            Wir wünschen dir und deiner Familie viel Gesundheit, Glück und alles<br/>
            was du dir selber wünschst, und vorgenommen hast soll in erfüllung gehen.<br/>
            <br/><br/>
            Weil wir die gesamte Community Schätzen, erhältst du nun ein Geschenk von uns!
            <br/>
            <img height='250' width='250' src='img/marketing/onepieceopwxspenden.png' /><br/><br/>
            Schau bitte in dein <a href='?p=inventar'>Inventar!</a>";
            $PMManager->SendPM($absender, $image, $name, $title, $text, $loginPlayer->GetName(), 1);
            $loginPlayer->AddItems(491, 491,1);
            $loginPlayer->SetBdaySuprise(1);


        }
        # Geburstag Ende
        if($loginPlayer->IsMultiChar() && $isDonor || !$loginPlayer->IsMultiChar() || $loginPlayer->GetArank() >= 2) {
            if ($loginPlayer->GetUserID() == $account->Get('id') || $loginPlayer->GetSitter() == $account->Get('id')) {
                if ($account->Get('email') != "") {
                    $names = $database->EscapeString($_POST['charnames']);
                    $stayLogged = isset($_POST['logged']);
                    $loginPlayer->SetActiveUserID($account->Get('id'));
                    $loginPlayer->Login($stayLogged);
                    if ($loginPlayer->GetDayliLogin() != $daylilogincheck) {
                        $daylilogin = date("d.m.y");
                        $id = 649;
                        $image = "img/system.png";
                        $name = 'Support';
                        $title = 'Tägliche Belohnung';

                        $text = "<b>Hey,
<br />
<br />
schön das du da bist, wir freuen uns sehr darüber, dass du es heute geschafft hast dir Zeit für das Spiel zu nehmen!
<br />
<br />
Als Belohnung erhältst du dafür 2 <img src='".$serverUrl."img/offtopic/GoldSymbol.png' alt='Gold' title='Gold' style='position: relative; top: 2px; height: 15px; width: 15px;'/> und 2.000 <img src='".$serverUrl."img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 2px; height: 15px; width: 10px;'/>.
<br />
NEU! Du erhältst nun zusätzlich täglich eine leichte Medizin oben drauf!
<br />
Viel Spaß beim Spielen!";

                        $chara = $loginPlayer->GetName();
                        $PMManager = new PMManager($database, $loginPlayer->GetID());
                        $PMManager->SendPM($id, $image, $name, $title, $text, $chara, 1);
                        $dayliberrytrop = $loginPlayer->GetBerry() + 2000;
                        $dayligoldtrop = $loginPlayer->GetGold() + 2;
                        $loginPlayer->AddItems(31, 31, 1);
                        $multitext = ', multitext="' . $names . '"';
                        if ($loginPlayer->GetSitter() == $account->Get('id')) {
                            $multitext = '';
                        }
                        $database->Update('dailylogin=1, daylilogin="' . $daylilogin . '", zeni="' . $dayliberrytrop . '", gold="' . $dayligoldtrop . '"' . $multitext, 'accounts', 'id=' . $loginPlayer->GetID());
                        header('Location: index.php');
                        exit();
                    } else {
                        if ($loginPlayer->GetSitter() == $account->Get('id')) {
                            $database->Update('multitext="' . $names . '"', 'accounts', 'id=' . $loginPlayer->GetID());
                        }
                        header('Location: index.php');
                        exit();
                    }
                } else {
                    $message = "Du kannst dich nicht einloggen wenn deine Email-Adresse nicht bestätigt wurde.";
                }
            }
        }
        else
        {
            $message = "Du kannst diesen Charakter nicht Spielen, da dein Unterstützer-Status abgelaufen ist.";
        }
    }
    else
    {
        $message = "Das Login ist derzeit gesperrt!";
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'accdelete')
{
    if ($account->HasAnyCharacter())
    {
        $message = 'Du musst zuerst alle Charaktere in OPBG, NBG und DBBG löschen.';
    }
    else if (!isset($_GET['code']) && isset($_POST['logged']))
    {
        $message = 'Es wurde eine Mail an deine E-Mail geschickt. Schau auch im Spam Ordner nach.';
        $email = $account->Get('email');
        $topic = 'Account löschen';
        $content = '
			Jemand möchte deinen Account <b>' . $account->Get('login') . '</b> löschen.<br/>
			Wenn du deinen Account wirklich löschen willst, dann folge den folgenden Link.<br/>
			<br/>
			Wenn du den Account nicht löschen willst, ignoriere diese Mail.<br/>
			<br/>
			<br/>
			<br/>
			<a href="' . $serverUrl . '/?p=charalogin&a=accdelete&code=' . md5($account->Get('password')) . '">Ich möchte den Account löschen.</a>
			<br/>
			<br/>';
        SendMail($email, $topic, $content);
    }
    else if ($_GET['code'] == md5($account->Get('password')))
    {
        $account->DeleteAccount();
        $message = 'Dein Account wurde gelöscht.';
        header('Location: index.php');
        exit();
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'changepw')
{
    if($safedPW == $safedPW2)
    {
        $account->ChangePasswordSafe($safedPW);
        $message = 'Du hast dein Passwort geändert.';
    }
    else
    {
        $message = "Die Passwörter stimmen nicht überein.";
    }
}

if ($player->IsLogged())
{
    header('Location: index.php');
    exit();
}
