<?php
$title = 'Ticketsystem';

if (!isset($player) || !$player->IsValid() || $player->GetTeamUser() < 2)
{
    header('Location: ?p=news');
    exit();
}