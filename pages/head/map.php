<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/story/story.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/story/sidestory.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/clan/clan.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/player/playerinventory.php';

$title = 'Karte';
$planet = new Planet($database, $player->GetPlanet());
$inventory = $player->GetInventory();
$marineShipArray = array(42, 384, 385);
if (isset($_GET['a']) && $_GET['a'] == 'travel')
{
    if (isset($_POST['destination']))
    {
        $name = $_POST['destination'];
        $tplace = new Place($database, $name, null);
        $item = new ItemManager($database);
        $placeclan = null;
        if($tplace->GetTerritorium() != 0)
        {
            $placeclan = new Clan($database, $tplace->GetTerritorium());
        }
        if ($tplace->IsValid() && $tplace->GetPlanet() == $player->GetPlanet())
        {
            if (($tplace->IsTravelable() || !$tplace->IsTravelable() && $player->GetARank() >= 2) && $tplace->GetID() != $player->GetPlace())
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
                    $message = 'Du befindest dich bereits auf Reisen.';
                }
                else if (!$inventory->HasShipEquipped($inventory->GetShip()) && $tplace->GetPlanet() != 2)
                {
                    $message = 'Du musst erst ein Schiff besetzen bevor du deine Reise antreten kannst, dieses findest du im Inventar';
                }
                else if ($inventory->GetShipWear() >= $inventory->GetShipMaxWear($inventory->GetShipItemID()) && $tplace->GetPlanet() != 2)
                {
                    $message = "Dein Schiff ist zu abgenutzt, du kannst damit nicht mehr Reisen.";
                }
                else if ($player->GetPlanet() == 3 && !$player->HasLogPort() || $player->GetPlanet() == 1 && !$player->HasEastBlueMap() || $player->GetPlanet() == 2 && !$player->HasImpeldownMap())
                {
                    $message = "Du hast keine Karte um dieses Meer zu bereisen.";
                }
                else if ($player->GetLevel() < $tplace->GetPlaceLevel())
                {
                    $message = "Dein Level ist zu niedrig um dich auf die Reise zu diesem Ort zu machen";
                }
                else if ($player->GetLP() < $player->GetMaxLP() / 4)
                {
                    $message = "Du brauchst 25% deiner LP um so eine gefährliche Reise starten zu können.";
                }
                else if($player->GetLevel() >= 5 && $tplace->GetTerritorium() != 0 && $player->GetClan() != $tplace->GetTerritorium() &&
                    (!in_array($player->GetClan(), $placeclan->GetAlliances()) && $player->GetGold() < 10 || in_array($player->GetClan(), $placeclan->GetAlliances()) && $player->GetGold() < 5 ))
                {
                    $message = "Du hast nicht genug Gold um dieses Territorium zu bereisen";
                }
                else
                {
                    $playerPlace = new Place($database, $player->GetPlace(), null);

                    $travelTime = 0;

                    if ($player->GetX() != 0 && $player->GetY() != 0)
                    {
                        $x = $player->GetX();
                        $y = $player->GetY();
                    }
                    else
                    {
                        $x = $playerPlace->GetX();
                        $y = $playerPlace->GetY();
                    }

                    $travelTime = round(round(abs($tplace->GetX() - $x) + abs($tplace->GetY() - $y)) / 10);
                    $travelBonus = $player->GetTravelBonus();
                    $travelBonus = floor($travelTime * ($travelBonus / 100));
                    $travelTime = $travelTime - $travelBonus;
                    $travelActionID = 2;
                    if($player->GetTravelTicket() != 0)
                    {
                        $travelTime = 0;
                    }
                    else
                    {
                        if ($travelTime < 10)
                        {
                            $travelTime = 10;
                        }
                        else if ($travelTime > 60)
                        {
                            $travelTime = 60;
                        }
                    }
                    if($tplace->GetPlanet() != 2)
                    {
                        $day = date("w");
                        if($day != 5)
                        {
                            $inventory->AddShipWear();
                        }
                        if($tplace->GetTerritorium() != 0 && $player->GetArank() < 2)
                        {
                            $bande = new Clan($database, $tplace->GetTerritorium());
                            if($bande->GetID() != $player->GetClan() && $player->GetLevel() >= 5)
                            {
                                $goldprice = 10;
                                if(in_array($player->GetClan(), $placeclan->GetAlliances()))
                                    $goldprice = 5;
                                $price = $player->GetGold() - $goldprice;
                                $update = $bande->GetGold();
                                if(!$player->IsMultiChar())
                                {
                                    $update = $bande->GetGold() + $goldprice;
                                    $resultb = $database->Update('gold="'.$update.'"', 'clans', 'id="'.$bande->GetID().'"');
                                    $database->Update('gewinn=gewinn+'.$goldprice, 'places', 'id='.$tplace->GetID());
                                }
                                $resultp = $database->Update('gold="'.$price.'"', 'accounts', 'id="'.$player->GetID().'"');
                            }
                        }
                    }
                    if($player->GetRace() == "Marine" && in_array($player->GetInventory()->GetShipItemID(), $marineShipArray))
                        $travelTime = round($travelTime / 2);

                    $action = $actionManager->GetAction($travelActionID);
                    $player->Travel($tplace->GetID(), $travelTime, $action, $x, $y);
                    if($player->GetTravelTicket() != 0)
                    {
                        $ticket = 0;
                        $player->UseTravelTicket($ticket);
                    }
                    if($player->GetLastNPCID() != 0)
                    {
                        $player->SetLastNPCID(0);
                    }
                    $message = 'Du reist nun zum Ort "' . $tplace->GetName() . '".';
                }
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'jump')
{
    if(!isset($_POST['placeid']) || !is_numeric($_POST['placeid']))
    {
        $message = 'Dieser Ort ist ungültig.';
    }
    else if ($player->GetARank() < 2)
    {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else
    {
        $tplace = new Place($database, $_POST['placeid'], null);
        $player->MapJump($tplace->GetID(), $player->GetPlanet(), $tplace->GetX(), $tplace->GetY());
    }
}
else if(isset($_GET['p']) == 'map' && isset($_GET['activate']) == 'ticket')
{
    $coupons = array(302);
    $id = $_POST['ticket'];
    if(!is_numeric($id) || !in_array($id, $coupons))
    {
        $message = "Die ID ist ungültig";
    }
    else if(!$player->HasItemWithID($id, $id))
    {
        $message = "Du besitzt diesen Coupon nicht";
    }
    else if($id != 302)
    {
        $message = "Das Ticket ist ungültig!";
    }
    else
    {
        $item = $player->GetItemByStatsIDOnly($id);

        if($player->GetTravelTicket() > 0)
        {
            $message = "Du hast bereits ein Ticket aktiv, bitte nutze dies erst!";
        }
        else
        {
            $existrabatt = $player->GetRabatt() + $rabatt;
            $result = $database->Update('usetravelticket=1', 'accounts', 'id="'.$player->GetID().'"');
            $message = "Du hast nun ein Ticket aktiv!";
            $player->RemoveItems($item, 1);
        }
    }
}
else if(isset($_GET['a']) && $_GET['a'] == 'impeldownpopup')
{
    if($player->GetImpelDownPopUp() == 1)
    {
        $player->SetImpelDownPopUp(0);
        $message = "Du hast die Info erfolgreich gelesen!";
    }
}
