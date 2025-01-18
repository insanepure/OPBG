<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';

$title = 'Spezialfähigkeiten';
$itemManager = new ItemManager($database);

if (isset($_GET['a']) && $_GET['a'] == 'train' && isset($_GET['id']) && isset($_POST['hours']))
{
    $hours = $_POST['hours'];
    $id = $_GET['id'];
    if (!is_numeric($hours) || $hours < 0)
    {
        $message = 'Die Stundenzahl ist ungültig.';
    }
    else if (!is_numeric($id))
    {
        $message = 'Die ID ist ungültig.';
    }
    else
    {
        $action = $actionManager->GetAction($id);
        $actionPossible = true;
        $actionHours = $action->GetMinutes() / 60;
        $isRound = false;
        if ($action->GetMinutes() != 0)
            $isRound = $hours % $actionHours;
        $maxHours = 24 * 30;
        $price = 0;
        if ($action->GetType() == 6)
            $price = $action->GetPrice() * 1;
        else
            $price = $action->GetPrice() * $hours;
        $item = $action->GetItem();
        $race = $action->GetRace();

        if ($item != 0 && !$player->HasItemWithID($item->GetID(), $item->GetID()))
        {
            $message = 'Du hast das benötigte Item nicht.';
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
        else if ($player->GetAction() != 0 && $action->GetMinutes() != 0 || $player->GetAction() != 0 && ($action->GetID() == 25 || $action->GetID() == 26))
        {
            $message = 'Du tust bereits etwas.';
        }
        else if ($race != '' && $player->GetRace() != $race)
        {
            $message = 'Du kannst diese Aktion nicht machen, sie gehört einer anderen Rasse.';
        }
        else if (
            ($action->GetID() == 44 && $player->GetPfad(1) != "Zoan") ||
            ($action->GetID() == 18 && $player->GetPfad(1) != "Zoan") ||
            ($action->GetID() == 19 && $player->GetPfad(1) != "Zoan") ||
            ($action->GetID() == 41 && $player->GetPfad(1) != "Logia") ||
            ($action->GetID() == 42 && $player->GetPfad(1) != "Logia") ||
            ($action->GetID() == 47 && $player->GetPfad(1) != "Logia") ||
            ($action->GetID() == 46 && $player->GetPfad(1) != "Paramecia") ||
            ($action->GetID() == 43 && $player->GetPfad(1) != "Paramecia") ||
            ($action->GetID() == 45 && $player->GetPfad(1) != "Paramecia") ||
            ($action->GetID() == 21 && $player->GetPfad(2) != "Schwertkaempfer") ||
            ($action->GetID() == 22 && $player->GetPfad(2) != "Schwarzfuss") ||
            ($action->GetID() == 23 && $player->GetPfad(2) != "Karatekämpfer")
        )
        {
            $message = "Du hast nicht den richtigen Pfad um diese Aura zu lernen!";
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
                $player->Travel($action->GetPlace(), $hours * 60, $action, $x, $y, false);
            }
            else
            {
                $minutes = $hours * 60;
                if ($action->GetID() == 4)
                {
                    $player->UpgradeRüstungshaki($price);
                    $message = "Du hast dein Rüstungshaki verbessert.";
                }
                else if ($action->GetID() == 18)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(128))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(128, $price);
                    $message = "Du hast Aura der Phönixflammen verbessert.";
                }
                else if ($action->GetID() == 19)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(129))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(129, $price);
                    $message = "Du hast Aura vom Gear verbessert.";
                }
                else if ($action->GetID() == 20)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(126))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(126, $price);
                    $message = "Du hast Aura der Wüste verbessert.";
                }
                else if ($action->GetID() == 21)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(127))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(127, $price);
                    $message = "Du hast Aura des Dämonengottes verbessert.";
                }
                else if ($action->GetID() == 22)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(125))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(125, $price);
                    $message = "Du hast Aura des Teufels verbessert.";
                }
                else if ($action->GetID() == 23)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(130))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(130, $price);
                    $message = "Du hast Aura des Wassers verbessert.";
                }
                else if($action->GetID() == 41)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(294))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(294, $price);
                    $message = "Du hast Aura des Feuers verbessert.";
                }
                else if($action->GetID() == 42)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(295))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(295, $price);
                    $message = "Du hast Gefrierende Aura verbessert.";
                }
                else if($action->GetID() == 43)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(296))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(296, $price);
                    $message = "Du hast Aura vom Fäden verbessert.";
                }
                else if($action->GetID() == 44)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(297))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(297, $price);
                    $message = "Du hast Aura von Kaido verbessert.";
                }
                else if($action->GetID() == 45)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(298))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(298, $price);
                    $message = "Du hast Aura des Zuckers verbessert.";
                }
                else if($action->GetID() == 46)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(299))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(299, $price);
                    $message = "Du hast Aura des Raums verbessert.";
                }
                else if($action->GetID() == 47)
                {
                    if ($price > $player->GetBerry() && $player->GetItemByStatsIDOnly(300))
                    {
                        $message = 'Du hast nicht genug Berry.';
                        return;
                    }
                    $player->UpgradeAura(300, $price);
                    $message = "Du hast Aura des Donners verbessert.";
                }
                else
                    $player->DoAction($action, $minutes);
            }

            if ($action->GetItem() != 0)
            {
                $statstype = 0;
                $upgrade = 0;
                $amount = 1;
                $player->RemoveItemsByID($action->GetItem(), $action->GetItem(), $statstype, $upgrade, $amount);
            }
        }
    }
}


if(isset($_GET['p']) == 'raceaction' && isset($_GET['attack']) == 'levelup')
{
    $id = 0;
if(!is_numeric($_POST['id']))
{
    $message = "Die Eingabe ist ungültig";
}
else
{
    $id = $_POST['id'];
}
$price = 50000;
$checka = explode(';', $player->GetAttacks());
$i = 0;
while($checka[$i])
{
    if(!in_array($id, $checka))
    {
        $message = "Du besitzt diese Attacke nicht";
    }
++$i;
}
if($player->GetBerry() < $price)
{
    $message = "Du hast nicht genügend Berry um diese Attacke aufzuleveln";
}
else if($player->GetAttackLevel($id) == 20)
{
    $message = "Du kannst eine Attacke nicht höher als Level 20 aufleveln";
}
else if($price != 50000)
{
    $message = "Der Preis ist ungültig";
    $text = 'Der Spieler '.$player->GetName().' hat versucht beim leveln der Attacken den Preis zu manipulieren! Es waren statt 50.000 => '.$price.'';
    $player->Track($text, $player->GetID(), 'System', 2);
}
else
{
    $bcost = $player->GetBerry() - 50000;
    $player->SetBerry($bcost);
    $level = $player->GetAttackLevel($id) + 1;
    $cdamage = $level * 1;
    $alevel = $player->SetAttackLevel($id, ($player->GetAttackLevel($id) + 1));
    $message = "Du hast deine Attacke nun auf Level ".$level." gebracht, dadurch erhöht sich der mögliche kritische Schaden auf ".$cdamage."%";
}
}

