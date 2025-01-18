<?php
    if (!isset($player) || !$player->IsValid())
    {
        header('Location: ?p=news');
        exit();
    }
    $title = 'Archiv';