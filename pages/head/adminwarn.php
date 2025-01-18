<?php
$title = 'Verwarnungen';

if (!isset($player) || !$player->IsValid() || $player->GetTeamUser() < 2)
{
    header('Location: ?p=news');
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'edit' && isset($_POST['main']) && is_numeric($_POST['main']))
{
    if ($_POST['warnreason'] != "")
    {
        $userID = $_POST['main'];
        $otherPlayer = new Player($database, $userID);
        if ($otherPlayer->IsValid())
        {
            $PMManager = new PMManager($database, $otherPlayer->GetID());
            if ($otherPlayer->GetID() == $player->GetID())
            {
                $message = "Du kannst dich nicht selbst verwarnen!";
            }
            else
            {
                $banned = 0;
                $warnReason = $database->EscapeString($_POST['warnreason']);
                $otherPlayer->AddWarning($warnReason);
                $pm = "<div style='text-align:center'><p>Hallo " . $otherPlayer->GetName() . ",<br />Du wurdest von " . $player->GetName() . " verwarnt,<br />Grund: " . $warnReason . "</p>";
                $pm = $pm . "<p>Dies ist deine <span style='color:red'>" . number_format($otherPlayer->GetWarnsCount(),'0', '', '.') . ". Verwarnung</span>, wir hoffen das sich dein Verhalten in Zukunft bessert.</p>";
                if (($otherPlayer->GetWarnsCount() == 3))
                {
                    $pm = $pm . "<p>Bitte beachte, dass dies deine letzte Verwarnung ist, mit dem nächsten Regelverstoß erhältst du eine <span style='color:red'><strong>Charaktersperre</strong></span>.</p>";
                }
                $pm = $pm . "<p>Solltest Du der Meinung sein, diese Verwarnung Grundlos erhalte zu haben, kannst Du Dich gerne bei Sload oder Shirobaka melden.</p>";
                $pm = $pm . "<p>Grüße<br />das OPBG-Team</p></div>";
                $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Du wurdest Verwarnt', $pm, $otherPlayer->GetName(), 1);

                $message = 'Du hast den Spieler ' . $otherPlayer->GetName() . ' verwarnt.';
            }
        }
    }
    else
    {
        $message = 'Du musst einen Grund für die Verwarnung angeben!';
    }
}
if (isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['user']) && isset($_POST['warning']))
{
    if ($player->GetARank() == 3)
    {
        $otherPlayer = new Player($database, $_POST['user']);
        if ($otherPlayer->IsValid())
        {
            if ($otherPlayer->GetID() == $player->GetID())
            {
                $message = "Du kannst deine eigenen Verwarnungen nicht löschen.";
            }
            else
            {
                if (!$otherPlayer->GetWarns()[$_POST['warning']])
                {
                    $message = "Diese Verwarnung existiert nicht.";
                }
                else
                {
                    $otherPlayer->DeleteWarning($otherPlayer->GetWarns()[$_POST['warning']]['id']);
                    $pms = "Einer deiner Verwarungen wurden auf Grund eines Fehlers entfernt, wir Bitten dich dennoch darum die Regeln einzuhalten";
                    $PMManager->SendPM(1, 'img/system.png', 'Support', 'Verwarnung gelöscht', $pms, $otherPlayer->GetName(), 1);
                    $message = "Die Verwarnung wurde entfernt. Der Spieler wurde darüber informiert";
                }
            }
        }
        else
        {
            $message = "Diese Spieler existiert nicht!";
        }
    }
}
