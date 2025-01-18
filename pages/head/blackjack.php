<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/blackjack/blackjack.php';
$BlackJack = new BlackJack($database, $player->GetID());
if(isset($_GET['a']) && $_GET['a'] == 'openthegame')
{
    $BlackJack->OpenTheGame($player->GetID());
    $message = "Spiel wurde eröffnet!";
}

if(isset($_GET['a']) && $_GET['a'] == 'draw')
{
    $BlackJack->SetDraw($BlackJack->Draw());
    $message = "Du hast eine Karte gezogen!";
}

if(isset($_GET['a']) && $_GET['a'] == 'endthegame')
{
$BlackJack->SetTheEnd(1);
$message = "Du hast das Spiel geschlossen!";
}
?>