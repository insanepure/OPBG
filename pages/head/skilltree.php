<?php

include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/fight/attackmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/skilltree/skill.php';
$pAttacks = explode(';', $player->GetAttacks());
$skillpoints = $player->GetSkillPoints();
$race = $player->GetRace();
$topOffset = 0;
if (isset($_GET['a']) && $_GET['a'] == 'learn' && isset($_GET['attack']) && is_numeric($_GET['attack']))
{
	$attackManager = new AttackManager($database);
	$itemManager = new ItemManager($database);
	$skill = new Skill($database, $_GET['attack']);
	$attack = $attackManager->GetAttack($skill->GetAttack());
	if ($attack == null)
	{
		$message = 'Diese Attacke existiert nicht.';
	}
	else if (!$skill->IsLearnable())
	{
		$message = 'Diese Attacke ist hier nicht lernbar.';
	}
	else if ($skill->GetRace() != '' && $skill->GetRace() != $player->GetRace())
	{
		$message = 'Diese Attacke gehört einer anderen Rasse.';
	}
	else if (in_array($skill->GetAttack(), $pAttacks))
	{
		$message = 'Du hast diese Attacke bereits gelernt.';
	}
	else if ($skill->GetLevel() > $player->GetLevel())
	{
		$message = 'Du musst mindestens Level ' . number_format($skill->GetLevel(),'0', '', '.') . ' sein.';
	}
	else if (!$skill->HasEnoughPoints($player->GetSkillPoints()) && $player->GetArank() < 2)
	{
		$message = 'Du hast nicht genügend Skillpunkte.';
	}
	else if (!$player->CheckSkill($skill))
	{
		$message = 'Du kannst nur Skills passend zu deinem Teufelsfrucht-Typ oder zusätzlich Schwertkämpfer, Schwarzfuß oder Karatekämpfer lernen.';
	}
	else
	{

		$canLearn = true;
		$attacksNeeded = '';
		$itemNeeded = '';
		$pItemList = array();
		$numAttacks = 0;
		$needItem = 0;
		$hasNeedItem = 0;
		if ($skill->GetNeedAttacks() != '') $numAttacks = count($skill->GetNeedAttacks());
		if ($skill->GetNeedItem() != 0) $needItem = $skill->GetNeedItem();
		$message = 'numAttacks: ' . $skill->GetID() . ' - NeedItem: ' . $needItem;

		if ($numAttacks != 0)
		{
			foreach ($skill->GetNeedAttacks() as $needAttack)
			{
				if ($needAttack != 0 && $needAttack != '')
				{
					if (!in_array($needAttack, $pAttacks))
					{
						$canLearn = false;
						$otherAttack = $attackManager->GetAttack($needAttack);
						if ($attacksNeeded == '') $attacksNeeded = $otherAttack->GetName();
						else $attacksNeeded = $attacksNeeded . ' oder ' . $otherAttack->GetName();
					}
				}
			}
		}

		$result = $database->Select('visualid', 'inventory', 'ownerid=' . $player->GetID(), 99999, 'id', 'ASC');
		if ($result)
		{
			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{
					$pItemList[] = $row['visualid'];
				}
			}
			$result->close();
		}
		if ($needItem != 0)
		{
			$itemNum = $skill->GetNeededItemAmount();
			$hasNum = 0;
			$otherItem = $itemManager->GetItem($needItem);

			$result = $database->Select('amount', 'inventory', 'ownerid=' . $player->GetID() . ' AND visualid=' . $needItem, 99999, 'id', 'ASC');
			if ($result)
			{
				if ($result->num_rows > 0)
				{
					$row = $result->fetch_assoc();
					$hasNum = $row['amount'];
				}
				$result->close();
			}
			if (!in_array($needItem, $pItemList) || $hasNum < $itemNum)
			{
				if ($itemNeeded == '') $itemNeeded = number_format($itemNum,'0', '', '.') . "x " . $otherItem->GetRealName();
				else $itemNeeded = $itemNeeded . ' und ' . number_format($itemNum,'0', '', '.') . 'x ' . $otherItem->GetRealName();
				++$hasNeedItem;
			}
			$message = $itemNeeded;
		}

		if (!$canLearn && $numAttacks >= 4)
		{
			$message = 'Du musst zuvor eine der vorherigen Techniken lernen.';
		}
		else if (!$canLearn && $numAttacks >= 1)
		{
			$message = 'Du musst zuvor ' . $attacksNeeded . ' lernen.';
		}
		else if ($hasNeedItem > 0 && $player->GetArank() < 2)
		{
			$message = 'Du benötigst vorher ' . $itemNeeded . '.';
		}
        else if($skill->GetBerry() > $player->GetBerry())
        {
            $message = 'Du hast nicht genügend Berry um diesen Skill zu erlernen.';
        }
		else
		{
			$player->LearnSkill($attack->GetID(), $skill->GetNeededPoints(), $skill->GetBerry(), $skill->GetType());
			$item = $player->GetInventory()->GetItemByStatsIDOnly($skill->GetNeedItem());
			$player->GetInventory()->RemoveItem($item, $skill->GetNeededItemAmount());
            $player->SetAttacks($player->GetAttacks() . ';'.$attack->GetID());
            $pAttacks = explode(';', $player->GetAttacks());
			$message = 'Du hast ' . $attack->GetName() . ' gelernt.';
		}
	}
}
else if(isset($_GET['a']) && $_GET['a'] == 'reset' && $player->GetArank() >= 2)
{
    $player->ResetSkills('Alle');
    $message = 'Du hast deine Skilltrees zurückgesetzt.';
}
