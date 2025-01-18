<?php

if ($player->GetPlanet() != 2)
{
  header('Location: ?p=news');
  exit();
}

$title = 'Befreiung';
    $reviveTime = $player->GetReviveTime();
    if ($reviveTime < 0)
        $reviveTime = 0;
