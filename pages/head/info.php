<?php
    $title = 'Infos';

    if(isset($_GET['a']) && $_GET['a'] == 'jump' && isset($_GET['m']) && is_string($_GET['m']) && isset($_GET['o']) && is_string($_GET['o']) && $player != null && $player->GetArank() >= 2)
    {
        $planet = $database->EscapeString($_GET['m']);
        $place = $database->EscapeString($_GET['o']);
        $player->SetPlanet($_GET['m']);
        $player->SetPlace($_GET['o']);
        $database->Update('planet="'.$planet.'", place="'.$place.'"', 'accounts', 'id='.$player->GetID());
        header('Location: ?p=boss');
    }