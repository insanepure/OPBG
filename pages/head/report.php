<?php
$title = 'Usermeldungen';
if ($player->GetARank() < 2)
{
    header('Location: ?p=news');
}

if (isset($_GET['a'])) {
    if ($_GET['a'] == 'close')
    {
        $database->Update('status=1, closedby="'.$player->GetName().'", closedtime=NOW()', 'meldungen', 'id = ' . $_GET['id'], 1);
    }
    if ($_GET['a'] == 'verify' && isset($_GET['u'])) {
        if (is_numeric($_GET['u'])) {
            $otherPlayer = new Player($database, $_GET['u']);
            if ($otherPlayer->IsValid()) {
                $otherPlayer->SetVerified(1);
                $otherPlayer->SetChatActive(1);
                $message = 'Der Spieler <a href="?p=profil&id=' . $otherPlayer->GetID() . '">' . $otherPlayer->GetName() . '</a> wurde verifiziert.';
                $database->Update('status=1, closedby="'.$player->GetName().'", closedtime=NOW()', 'meldungen', 'id = ' . $_GET['id'], 1);
            }
        }
    }
}