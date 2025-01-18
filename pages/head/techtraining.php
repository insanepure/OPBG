<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/fight/attackmanager.php';

    $title = 'Technik';
    $place = new Place($database, $player->GetPlace(), $actionManager);
    $attackManager = new AttackManager($database);
    if (isset($_GET['a']) && $_GET['a'] == 'train' && isset($_GET['id']))
    {
        $id = $_GET['id'];
        $pAttacks = explode(';', $player->GetAttacks());
        if (!is_numeric($id))
        {
            $message = 'Die ID ist ungültig.';
        }
        else if (in_array($id, $pAttacks))
        {
            $message = 'Du kannst diese Technik bereits.';
        }
        else
        {
            $attacks = explode(';', $place->GetLearnableAttacks());
            if (!in_array($id, $attacks))
            {
                $message = 'Du kannst das hier nicht lernen.';
            }
            else
            {
                $attack = $attackManager->GetAttack($id);
                if (
                    $player->GetMaxLP() < $attack->GetLearnLP()
                    || $player->GetKI() < $attack->GetLearnKI()
                    || $player->GetMaxKP() < $attack->GetLearnKP()
                    || $player->GetAttack() < $attack->GetLearnAttack()
                    || $player->GetDefense() < $attack->GetLearnDefense()
                )
                {
                    $message = 'Du hast nicht genügend Stats um diese Technik zu lernen.';
                }
                else if ($attack->GetLevel() > $player->GetLevel())
                {
                    $message = 'Dein Level ist zu niedrig.';
                }
                else if ($attack->GetRace() != '' && $player->GetRace() != $attack->GetRace())
                {
                    $message = 'Du hast nicht die richtige Rasse.';
                }
                else
                {
                    $learnID = 6;
                    $action = $actionManager->GetAction($learnID);
                    $minutes = $action->GetMinutes() * $attack->GetLearnTime();
                    if ($player->GetAction() != 0 && $minutes != 0 || $player->GetTravelAction() == 25 || $player->GetTravelAction() == 26)
                    {
                        $message = 'Du tust bereits etwas.';
                    }
                    else
                    {
                        $player->Learn($action, $minutes, $attack->GetID());
                        $message = 'Du lernst nun ' . $attack->GetName().'.';
                    }
                }
            }
        }
    }
