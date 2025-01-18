<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
$title = 'Aktionen';

$itemManager = new ItemManager($database);

$place = new Place($database, $player->GetPlace(), $actionManager);

if (isset($_GET['a']) && $_GET['a'] == 'train' && isset($_GET['id']) && isset($_POST['hours']))
{
  $hours = $_POST['hours'];
  $id = $_GET['id'];
  if (!is_numeric($hours) || $hours < 0)
  {
    $message = ' Die Stundenzahl ist ungültig.';
  }
  else if (!is_numeric($id))
  {
    $message = 'Die ID ist ungültig.';
  }
  else if (!$place->HasAction($id))
  {
    $message = 'Der Ort kennt dieses Training nicht.';
  }
  else
  {
    $action = $actionManager->GetAction($id);
    $actionPossible = true;
    $actionHours = $action->GetMinutes() / 60;
    if($hours >= 1 && $actionHours >= 1)
    {
        if ($actionHours == 0 && $hours == 0) $isRound = 0;
        else $isRound = $hours % $actionHours;
    }

    $maxHours = 24 * 30;
    $price = $action->GetPrice() * $hours;
    $race = $action->GetRace();
    $maxTime = $action->GetMaxTimes() * $actionHours;

    if ($action->GetItems() != "")
    {
        $items = explode(";", $action->GetItems());
        foreach($items as &$itemDataArray)
        {
            $itemData = explode('@',$itemDataArray);
            $itemID = $itemData[0];
            $itemAmount = $itemData[1];

            if(!$player->HasItemWithID($itemID, $itemID))
            {
                $error = 'Du hast das benötigte Item nicht.';
                break;
            }
            else
            {
                $playerItem = $player->GetItemByIDOnly($itemID, $itemID);
                if($playerItem->GetAmount() < $itemAmount * $hours)
                {
                    $error = 'Du hast nicht genug vom benötigtem Item.';
                    break;
                }
            }
        }
    }

    if($error != "")
    {
        $message = $error;
    }
    else if ($action->GetItem() != 0 && !$player->HasItemWithID($action->GetItem(), $action->GetItem()))
    {
      $message = 'Du hast das benötigte Item nicht.';
    }
    else if ($hours > $maxTime + 0.1)
    {
      $message = 'Die Stundenzahl ist ungültig!';
    }
    else if ($action->GetLevel() > $player->GetLevel())
    {
      $message = 'Dein Level ist zu niedrig.';
    }
    else if ($isRound)
    {
      $message = 'Die Stundenzahl ist nicht gültig.';
    }
    else if ($actionHours > $hours)
    {
      $message = 'Die Stundenzahl ist zu klein.';
    }
    else if ($hours > $maxHours)
    {
      $message = 'Die Stundenzahl ist zu groß.';
    }
    else if ($price > $player->GetBerry())
    {
      $message = 'Du hast nicht genug Berry.';
    }
    else if ($player->GetAction() != 0 || $player->GetTravelAction() == 25 || $player->GetTravelAction() == 26)
    {
      $message = 'Du tust bereits etwas.';
    }
    else if ($race != '' && $player->GetRace() != $race)
    {
      $message = 'Du kannst diese Aktion nicht machen, sie gehört einer anderen Rasse.';
    }
    else if($action->GetType() == 15 && $player->HasSpecialTrainingUsed($action->GetID()))
    {
          $message = 'Du hast die Aktion bereits durchgeführt.';
    }
    else if ($actionPossible)
    {

      if ($action->GetType() == 4)
      {
          $playerPlace = new Place($database, $player->GetPlace(), null);

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
          if($action->GetPlanet() != '' && $action->GetPlanet() != $player->GetPlanet())
          {
              $playerPlanet = new Planet($database, $player->GetPlanet());
              $actionplanet = new Planet($database, $action->GetPlanet());
              $travelTime = $action->GetMinutes();
              $travelActionID = $action->GetID();
              $action = $actionManager->GetAction($travelActionID);
              $player->TravelPlanet($actionplanet->GetID(), $travelTime, $action);
          }
          else
          {
              $player->Travel($action->GetPlace(), $hours * 60, $action, $x, $y, false);
          }
      }
      else
      {

        $minutes = $hours * 60;
        $player->DoAction($action, $minutes);
      }

      if ($action->GetItem() != 0)
      {
        $statstype = 0;
        $upgrade = 0;
        $amount = 1;
        $player->RemoveItemsByID($action->GetItem(), $action->GetItem(), $statstype, $upgrade, $amount);
      }

        if ($action->GetItems() != "")
        {
            $items = explode(";", $action->GetItems());
            foreach($items as &$itemDataArray)
            {
                $itemData = explode('@',$itemDataArray);
                $itemID = $itemData[0];
                $itemAmount = $itemData[1];
                $player->RemoveItemsByID($itemID, $itemID, 0, 0, $itemAmount * $hours);
            }
        }
    }
  }
}
