<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/fight/attackmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/npc/npc.php';
$title = 'Trainer';
$attackManager = new AttackManager($database);
if (isset($_GET['a']) && $_GET['a'] == 'train' && isset($_GET['id']) && isset($_GET['npcid']))
{
	$id = $_GET['id'];
	$npcid = $_GET['npcid'];
	$pAttacks = explode(';', $player->GetAttacks());
	if (!is_numeric($id))
	{
		$message = 'Die ID ist ungültig.';
	}
	else if (in_array($id, $pAttacks))
	{
		$message = 'Du kannst diese Technik bereits.';
	}
	else if (!is_numeric($npcid))
	{
		$message = 'Diese NPC-ID ist ungültig.';
	}
	else
	{
		$trainers = $place->GetTrainers();
		if (!in_array($npcid, $trainers))
		{
			$message = 'Der NPC befindet sich nicht an diesen Ort.';
		}
		else
		{
			$npc = new NPC($database, $npcid, 1);
			if (!$npc->IsValid())
			{
				$message = 'Diese NPC existiert nicht.';
			}
			$attacks = explode(';', $npc->GetAttacks());
			if (!in_array($id, $attacks))
			{
				$message = 'Dieser NPC kennt diese Attacke nicht.';
			}
			else
			{
				$attack = $attackManager->GetAttack($id);
				if (
					$player->GetMaxLP() < $attack->GetLearnLP()
					|| $player->GetMaxKP() < $attack->GetLearnKP()
					|| $player->GetKI() < $attack->GetLearnKI()
					|| $player->GetAttack() < $attack->GetLearnAttack()
					|| $player->GetDefense() < $attack->GetLearnDefense()
				)
				{
					$message = 'Du hast nicht genügend Stats um diese Technik zu lernen.';
				}
				else if ($attack->GetRace() != '' && $player->GetRace() != $attack->GetRace())
				{
					$message = 'Du hast nicht die richtige Gesinnung.';
				}
				else if ($npc->NeedTrainerItem() != 0 && !$player->GetInventory()->HasItem($player->GetInventory()->HasItemWithID($npc->NeedTrainerItem(), $npc->NeedTrainerItem())))
				{
					$itemManager = new ItemManager($database);
					$item = $itemManager->GetItem($npc->NeedTrainerItem());
					$message = "Du benötigst " . number_format($npc->NeedTrainerItemAmount(),'0', '', '.') . "x " . $item->GetRealName();
				}
				else
				{
					//$needitemid = $player->GetInventory()->GetItemID($npc->NeedTrainerItem());
					$needitem = $player->GetInventory()->GetItemByIDOnly($npc->NeedTrainerItem(), $npc->NeedTrainerItem());
					if ($npc->NeedTrainerItem() != 0 && $needitem->GetAmount() < $npc->NeedTrainerItemAmount())
					{
						$message = "Du benötigst " . number_format($npc->NeedTrainerItemAmount(),'0', '', '.') . "x " . $needitem->GetRealName();
					}
					else
					{
						$learnID = 6;
						$action = $actionManager->GetAction($learnID);
						$minutes = $action->GetMinutes();
						if ($player->GetAction() != 0 && $minutes != 0 || $player->GetTravelAction() == 25 || $player->GetTravelAction() == 26)
						{
							$message = 'Du tust bereits etwas.';
						}
						else
						{
							$player->Learn($action, $minutes, $attack->GetID());
							$message = 'Du lernst nun ' . $attack->GetName();
						}
					}
				}
			}
		}
	}
}
