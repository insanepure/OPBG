<?php
    if($player->GetPlanet() == 4 && $player->GetArank() < 2)
    {
        header('location: ?p=news');
        exit();
    }
    $title = 'Schiff';

    $inventory = $player->GetInventory();
    $marineShipArray = array(42, 384, 385);
if ((isset($_GET['a']) && $_GET['a'] == 'travel' || isset($_GET['a']) && $_GET['a'] == 'teleport') && isset($_POST['destination']))
{
	$travelplanet = new Planet($database, $_POST['destination']);
	if ($travelplanet == null || !$travelplanet->CanSee($player->GetStory()) || !$travelplanet->CanSeeSide($player->GetSideStory()))
	{
		$message = 'Dieser Planet existiert nicht.';
	}
	else if (!$travelplanet->IsTravelable())
	{
		$message = 'Du kannst dieses Meer nicht bereisen.';
	}
    else if (!$inventory->HasShipEquipped($inventory->GetShip()))
    {
		$message = 'Du musst erst ein Schiff besetzen bevor du deine Reise antreten kannst, dieses findest du im Inventar';
    }
	else if ($inventory->GetShipWear() >= $inventory->GetShipMaxWear($inventory->GetShipItemID()))
	{
		$message = "Dein Schiff ist zu abgenutzt, du kannst damit nicht mehr Reisen.";
	}
	else if ($player->GetPlanet() == 3 && !$player->HasLogPort() || $player->GetPlanet() == 1 && !$player->HasEastBlueMap() || $player->GetPlanet() == 2 && !$player->HasImpeldownMap())
	{
		$message = "Du hast keine Karte um dieses Meer zu bereisen.";
	}
	else if ($player->GetLevel() < $travelplanet->GetPlanetLevel())
	{
		$message = "Dein Level ist zu niedrig um dich auf die Reise zu diesem Ort zu machen";
	}
	else if ($player->GetLP() < $player->GetMaxLP() / 4)
	{
		$message = "Du brauchst 25% deiner LP um so eine gefährliche Reise starten zu können.";
	}
	else
	{
		if ($player->GetFight() != 0)
		{
			$message = 'Du kannst im Kampf nicht reisen.';
		}
		else if ($player->GetTournament() != 0)
		{
			$message = 'Du kannst im Turnier nicht reisen.';
		}
		else if ($player->GetTravelAction() != 0)
		{
			$message = 'Du tust bereits etwas.';
		}
		else
		{
            if($player->GetRace() == 'Marine' && in_array($player->GetInventory()->GetShipItemID(), $marineShipArray))
                $travelTime = 720;
            else
			    $travelTime = 1440;
			if ($_GET['a'] == 'travel')
			{
                if($travelplanet->GetID() != 2 && date("w") != 5)
                    $inventory->AddShipWear();
				$travelActionID = 13;
				$action = $actionManager->GetAction($travelActionID);
				$player->TravelPlanet($travelplanet->GetID(), $travelTime, $action);
                if ($travelplanet->GetID() == 1)
                    $message = 'Du reist nun zum ' . $travelplanet->GetName() . '.';
                else
                    $message = 'Du reist nun zur ' . $travelplanet->GetName() . '.';
			}
		}
	}
}
else if (isset($_GET['a']) && $_GET['a'] == 'jump')
{
    if(!isset($_POST['oceanid']) || !is_numeric($_POST['oceanid']))
    {
        $message = 'Der Ort ist ungültig.';
    }
	else if ($player->GetARank() < 2) {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else
    {
        $travelplanet = new Planet($database, $_POST['oceanid']);
		$player->OceanJump($travelplanet->GetID(), $travelplanet->GetStartingPlace(), $travelplanet->GetX(), $travelplanet->GetY());
	}
}
