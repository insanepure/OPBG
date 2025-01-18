<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/chat/chat.php';
$chat = new Chat($accountDB, session_id());
$title = 'Admin Menü';
function postChangelogToDiscord($text)
{

}


if (!isset($player) || !$player->IsValid() || $player->GetARank() < 2)
{
	header('Location: ?p=news');
	exit();
}

$limitedTables = array(
	'actions', 'attacks', 'items', 'npcs', 'places', 'story', 'sidestory', 'events', 'titel', 'clans',
	'patterns', 'planet', 'changelog', 'passives', 'verzeichnis','eventitems', 'skilltree', 'treasurehunt', 'treasurehuntprogress', 'treasurehuntislands'
);

if($player->GetARank() < 3 && isset($_GET['table']) && !in_array($_GET['table'], $limitedTables) || $_GET['table'] == 'adminlog')
{
	header('Location: ?p=admin');
	exit();
}

function AddToLog($database, $ip, $accs, $log)
{
	$timestamp = date('Y-m-d H:i:s');
	$insert = '"' . $ip . '","' . $accs . '","' . $database->EscapeString($log) . '","' . $timestamp . '"';
	$result = $database->Insert('ip,accounts,log,time', $insert, 'adminlog');
}



$ip = $account->GetIP();
$accs = $player->GetName() . ' (' . $player->GetID() . ')';
$log = '';


if (isset($_GET['a']) && $_GET['a'] == 'delete' && isset($_POST['sure']))
{
	$table = $_GET['table'];
    $id = $_POST['id'];
    if($table == 'attacks')
    {
        $result = $database->Select('*', 'accounts', 'attacks LIKE "%;'.$id.'%" OR fightattacks LIKE "%;'.$id.'%"');

        if($result)
        {
            while ($row = $result->fetch_assoc())
            {
                $attacks = explode(';', $row['attacks']);
                $fightattacks = explode(';', $row['fightattacks']);

                array_splice($attacks, array_search($id, $attacks), 1);
                array_splice($fightattacks, array_search($id, $fightattacks), 1);

                $attacks = implode(';', $attacks);
                $fightattacks = implode(';', $fightattacks);

                $database->Update('attacks="'.$attacks.'", fightattacks="'.$fightattacks.'"', 'accounts', 'id='.$row['id'], 1);
            }
        }
    }

	$database->Delete($table, 'id=' . $id . '');
	$message = 'Die ID ' . $id . ' wurde aus Tabelle ' . $table . ' gelöscht.';
	$log = $log . 'Die ID <b>' . number_format($id,'0', '', '.') . '</b> wurde aus Tabelle <b>' . $table . '</b> gelöscht.<br/>';
	AddToLog($database, $ip, $accs, $log);
}
else if (isset($_GET['a']) && $_GET['a'] == 'edit')
{
	$table = $_GET['table'];
	$result = $database->Select('*', $table, '');
	$finfo = $result->fetch_fields();



	$create = false;
	if (!isset($_POST['id']) || $_POST['id'] == '' || $_POST['id'] == 0)
	{
		$create = true;
	}

	if ($create && $table == 'changelog')
	{
        postChangelogToDiscord($_POST['text']);

        $text = '/system Es ist ein neuer Changelog verfügbar!';
        $text = str_replace('{@BGMSG@}', '', $text);
        $text = str_replace('{@BG@}', '', $text);
        $result = $chat->SendMessage($text);
	}

	if ($table == 'npcs' || $table == 'fighters')
	{
		if (isset($_POST['fighter_patterns']))
		{
			$_POST['patterns'] = implode(';', $_POST['fighter_patterns']);
		}
		else
		{
			$_POST['patterns'] = '';
		}

        if (isset($_POST['tfighter_patterns']))
        {
            $_POST['tpatterns'] = implode(';', $_POST['tfighter_patterns']);
        }
        else
        {
            $_POST['tpatterns'] = '';
        }
	}

	if ($table == 'attacks')
	{
		if (isset($_POST['attack_passives']))
		{
			$_POST['passives'] = implode(';', $_POST['attack_passives']);
		}
		else
		{
			$_POST['passives'] = '';
		}
	}

    if ($table == 'accounts')
    {
        if (isset($_POST['spcialtrainuses']))
        {
            $_POST['spcialtrainuses'] = implode(';', $_POST['spcialtrainuses']);
        }
        else
        {
            $_POST['spcialtrainuses'] = '';
        }
    }
	if ($table == 'events')
	{
		if (isset($_POST['event_items']))
		{
			$_POST['item'] = implode(';', $_POST['event_items']);
		}
		else
		{
			$_POST['item'] = '';
		}
	}
	if ($table == 'accounts')
	{
		if (isset($_POST['player_titel']))
		{
			$_POST['titels'] = implode(';', $_POST['player_titel']);
		}
		else
		{
			$_POST['titels'] = '';
		}

		if (isset($_POST['multi_accs']))
		{
			$_POST['multiaccounts'] = implode(';', $_POST['multi_accs']);
		}
		else
		{
			$_POST['multiaccounts'] = '';
		}
	}

    if($table == 'story')
    {
        if (isset($_POST['answer']))
        {
            $_POST['quizanswers'] = implode('@', $_POST['answer']);
        }
        else
        {
            $_POST['quizanswers'] = '';
        }

        if (isset($_POST['correct']))
        {
            $correct = '';
            if($_POST['correct'][0] == 'on')
                $correct .= '0;';
            if($_POST['correct'][1] == 'on')
                $correct .= '1;';
            if($_POST['correct'][2] == 'on')
                $correct .= '2;';
            $_POST['quizcorrect'] = $correct;
        }
        else
        {
            $_POST['quizcorrect'] = '';
        }
    }

	if ($table == 'npcs' || $table == 'story' || $table == 'sidestory' || $table == 'fights')
	{
		if (isset($_POST['npcandstoryitems_item']))
		{
			$npcItems = array();
			$itemCount = count($_POST['npcandstoryitems_item']);
			$i = 0;
			while ($i != $itemCount)
			{
				$item = array();

				$item[0] = $_POST['npcandstoryitems_item'][$i];
				$item[1] = $_POST['npcandstoryitems_itemchance'][$i];

				$npcItems[$i] = implode('@', $item);
				++$i;
			}
			$_POST['items'] = implode(';', $npcItems);
		}
		else
		{
			$_POST['items'] = '';
		}
	}

    if ($table == 'treasurehuntislands')
    {
        if (isset($_POST['amountitems_item']))
        {
            $items = array();
            $itemCount = count($_POST['amountitems_item']);
            $i = 0;
            while ($i != $itemCount)
            {
                $item = array();

                $item[0] = $_POST['amountitems_item'][$i];
                $item[1] = $_POST['amountitems_amount'][$i];

                $items[$i] = implode('@', $item);
                ++$i;
            }
            $_POST['loot'] = implode(';', $items);
        }
        else
        {
            $_POST['loot'] = '';
        }
    }

	if ($table == 'tournaments' || $table == 'actions' || $table == 'titel')
	{
		if (isset($_POST['amountitems_item']))
		{
			$items = array();
			$itemCount = count($_POST['amountitems_item']);
			$i = 0;
			while ($i != $itemCount)
			{
				$item = array();

				$item[0] = $_POST['amountitems_item'][$i];
				$item[1] = $_POST['amountitems_amount'][$i];

				$items[$i] = implode('@', $item);
				++$i;
			}
			$_POST['items'] = implode(';', $items);
		}
		else
		{
			$_POST['items'] = '';
		}
	}

	if ($table == 'attacks')
	{
		if (isset($_POST['race']))
		{
			$_POST['race'] = implode(', ', $_POST['race']);
		}
		else
		{
			$_POST['race'] = '';
		}
	}

	if ($table == 'events')
	{
		$fights = array();
		if (isset($_POST['event_npcs']))
		{
			$fightCount = count($_POST['event_npcs']);
			$i = 0;
			while ($i != $fightCount)
			{
				$fightArray = array();

				$fightArray[0] = implode(':', $_POST['event_npcs'][$i]);
				if (isset($_POST['event_fhealing'][$i]))
				{
					$fightArray[1]  = 1;
				}
				else
				{
					$fightArray[1]  = 0;
				}
				$fightArray[2] = $_POST['event_survivalteam'][$i];
				$fightArray[3] = $_POST['event_survivalrounds'][$i];
				$fightArray[4] = $_POST['event_survivalwinner'][$i];
				$fightArray[5] = $_POST['event_healthratio'][$i];
				$fightArray[6] = $_POST['event_healthratioteam'][$i];
				$fightArray[7] = $_POST['event_healthratiowinner'][$i];

				$fights[$i] = implode(';', $fightArray);
				++$i;
			}
			$_POST['fights'] = implode('@', $fights);

			$pat = array();

			$patID = 0;
			if (isset($_POST['pat_planet']))
			{
				$pat[$patID] = $_POST['pat_planet'];
			}
			else
			{
				$pat[$patID] = '';
			}

			$patID++;
			if (isset($_POST['pat_place']))
			{
				$pat[$patID] = $_POST['pat_place'];
			}
			else
			{
				$pat[$patID] = '';
			}

			$patID++;
			if (isset($_POST['pat_weekday']))
			{
				$pat[$patID] = implode(':', $_POST['pat_weekday']);
			}
			else
			{
				$pat[$patID] = '';
			}
			$patID++;

			$monthdays = array();
			$patID++;
			if (isset($_POST['pat_monthday1']))
			{
				$monthdays[0] = $_POST['pat_monthday1'];
			}
			else
			{
				$monthdays[0] = 1;
			}
			if (isset($_POST['pat_monthday2']))
			{
				$monthdays[1] = $_POST['pat_monthday2'];
			}
			else
			{
				$monthdays[1] = 31;
			}
			$pat[$patID] = implode('-', $monthdays);

			$months = array();
			$patID++;
			if (isset($_POST['pat_months1']))
			{
				$months[0] = $_POST['pat_months1'];
			}
			else
			{
				$months[0] = 1;
			}
			if (isset($_POST['pat_months2']))
			{
				$months[1] = $_POST['pat_months2'];
			}
			else
			{
				$months[1] = 12;
			}
			$pat[$patID] = implode('-', $months);

			$yeardays = array();
			$patID++;
			if (isset($_POST['pat_yeardays1']))
			{
				$yeardays[0] = $_POST['pat_yeardays1'];
			}
			else
			{
				$yeardays[0] = 1;
			}
			if (isset($_POST['pat_yeardays2']))
			{
				$yeardays[1] = $_POST['pat_yeardays2'];
			}
			else
			{
				$yeardays[1] = 365;
			}
			$pat[$patID] = implode('-', $yeardays);

			$years = array();
			$patID++;
			if (isset($_POST['pat_years1']))
			{
				$years[0] = $_POST['pat_years1'];
			}
			else
			{
				$years[0] = 1;
			}
			if (isset($_POST['pat_years2']))
			{
				$years[1] = $_POST['pat_years2'];
			}
			else
			{
				$years[1] = 3000;
			}
			$pat[$patID] = implode('-', $years);

			$_POST['placeandtime'] = implode(';', $pat);
		}
		else
		{
			$_POST['placeandtime'] = '';
		}
	}

	if (isset($_POST['items']) && $table == 'places')
	{
		$_POST['items'] = implode(';', $_POST['items']);
	}
    else if (isset($_POST['items']) && $table == 'items')
    {
        if(isset($_POST['empty']))
            $empty = 1;
        else
            $empty = 0;
        if(isset($_POST['choose']))
            $choose = 1;
        else
            $choose = 0;

        $berrymin = 0;
        $berrymax = 0;
        $goldmin = 0;
        $goldmax = 0;
        if(!empty($_POST['berrymin']))
            $berrymin = $_POST['berrymin'];
        if(!empty($_POST['berrymax']))
            $berrymax = $_POST['berrymax'];
        if(!empty($_POST['goldmin']))
            $goldmin = $_POST['goldmin'];
        if(!empty($_POST['goldmax']))
            $goldmax = $_POST['goldmax'];
        $_POST['items'] = $empty . ';' . $choose . ';' . $berrymin . '@' . $berrymax . ';' . $goldmin . '@' . $goldmax . ';' . implode(';', $_POST['items']);
    }
	else if (!isset($_POST['items']))
	{
		$_POST['items'] = '';
	}
	if (isset($_POST['attacks']))
	{
		$_POST['attacks'] = implode(';', $_POST['attacks']);
	}
	else
	{
		$_POST['attacks'] = '';
	}

    if (isset($_POST['tattacks']))
    {
        $_POST['tattacks'] = implode(';', $_POST['tattacks']);
    }
    else
    {
        $_POST['tattacks'] = '';
    }


	if (isset($_POST['removeattacks']))
	{
		$_POST['removeattacks'] = implode(';', $_POST['removeattacks']);
	}
	else
	{
		$_POST['removeattacks'] = '';
	}

	if (isset($_POST['addattacks']))
	{
		$_POST['addattacks'] = implode(';', $_POST['addattacks']);
	}
	else
	{
		$_POST['addattacks'] = '';
	}

	if (isset($_POST['needattacks']))
	{
		$_POST['needattacks'] = implode(';', $_POST['needattacks']);
	}
	else
	{
		$_POST['needattacks'] = '';
	}

	if (isset($_POST['needitems']))
	{
		$_POST['needitems'] = implode(';', $_POST['needitems']);
	}
	else
	{
		$_POST['needitems'] = '';
	}

	if (isset($_POST['playerattack']))
	{
		$_POST['playerattack'] = implode(';', $_POST['playerattack']);
	}
	else
	{
		$_POST['playerattack'] = '';
	}

	if (isset($_POST['actions']))
	{
		$_POST['actions'] = implode(';', $_POST['actions']);
	}
	else
	{
		$_POST['actions'] = '';
	}

	if (isset($_POST['npcs']))
	{
		$_POST['npcs'] = implode(';', $_POST['npcs']);
	}
	else
	{
		$_POST['npcs'] = '';
	}

    if (isset($_POST['tnpcs']))
    {
        $_POST['tnpcs'] = implode(';', $_POST['tnpcs']);
    }
    else
    {
        $_POST['tnpcs'] = '';
    }

	if (isset($_POST['supportnpcs']))
	{
		$_POST['supportnpcs'] = implode(';', $_POST['supportnpcs']);
		echo $_POST['supportnpcs'];
	}
	else
	{
		$_POST['supportnpcs'] = '';
	}

	if (isset($_POST['trainers']))
	{
		$_POST['trainers'] = implode(';', $_POST['trainers']);
	}
	else
	{
		$_POST['trainers'] = '';
	}

	if (isset($_POST['fightattacks']))
	{
		$_POST['fightattacks'] = implode(';', $_POST['fightattacks']);
	}
	else
	{
		$_POST['fightattacks'] = '';
	}

	if (isset($_POST['learnableattacks']))
	{
		$_POST['learnableattacks'] = implode(';', $_POST['learnableattacks']);
	}
	else
	{
		$_POST['learnableattacks'] = '';
	}

	$update = '';
	$names = '';

	$row = array();
	$result = $database->Select('*', $table, 'id = ' . $_POST['id'] . '', 1);
	if ($result)
	{
		$row = $result->fetch_assoc();
		$result->close();
	}

	$changedValues = '';
	foreach ($finfo as $val)
	{
		$name = $val->name;

		switch ($val->type)
		{
			case 1: //tinyint
				if (!isset($_POST[$name]))
				{
					$_POST[$name] = 0;
				}
				else
				{
					$_POST[$name] = 1;
				}
				break;
		}

		if ($row[$name] != $_POST[$name])
		{
			$changedValues = $changedValues . $name . ' editiert von ' . $row[$name] . ' zu ' . $_POST[$name] . chr(10);
		}

        if($name == 'arank' && $table == 'accounts')
        {
            if($_POST[$name] != 0)
                $database->Update('igentry=1', 'statslist', 'acc='.$_POST['id']);
            else
                $database->Update('igentry=0', 'statslist', 'acc='.$_POST['id']);
        }


		$updateValue = $database->EscapeString($_POST[$name]);
		if (!$create)
		{
			$updatestr = '`' . $name . '`="' . $updateValue . '"';
			if($name == 'name' && $table == 'accounts')
			{
				$result = $database->Update('name="' . $database->EscapeString($_POST[$name]) . '"', 'statslist', 'name = "' . $row[$name] . '"', 9999);
				$result = $database->Update('sendername="' . $database->EscapeString($_POST[$name]) . '"', 'pms', 'sendername = "' . $row[$name] . '"', 9999);
				$result = $database->Update('receivername="' . $database->EscapeString($_POST[$name]) . '"', 'pms', 'receivername = "' . $row[$name] . '"', 9999);
				$result = $database->Update('authorname="' . $database->EscapeString($_POST[$name]) . '"', 'News', 'authorname = "' . $row[$name] . '"', 9999);
				$result = $database->Update('seller="' . $database->EscapeString($_POST[$name]) . '"', 'market', 'seller = "' . $row[$name] . '"', 9999);
				$result = $database->Update('name="' . $database->EscapeString($_POST[$name]) . '"', 'fighters', 'name = "' . $row[$name] . '"', 9999);
			}

			if ($update == '')
			{
				$update = $updatestr;
			}
			else
			{
				$update = $update . ', ' . $updatestr;
			}
		}
		else
		{
			if ($names == '')
			{
				$names = $name;
			}
			else
			{
				$names = $names . ', ' . $name;
			}

			if ($update == '')
			{
				$update = '"' . $updateValue . '"';
			}
			else
			{
				$update = $update . ', "' . $updateValue . '"';
			}
		}
	}

	if ($create)
	{
		$result = $database->Insert($names, $update, $table);
		$newID = $database->GetLastID();
		$message = 'Neuer Eintrag ' . number_format($newID,'0', '', '.') . ' in ' . $table . ' wurde erstellt.';
		$log = $log . 'Neuer Eintrag <b>' . number_format($newID,'0', '', '.') . '</b> in <b>' . $table . '</b> wurde erstellt.';

	}
	else
	{
		$log = $log . 'ID <b>' . number_format($_POST['id'],'0', '', '.') . '</b> ';
		if (isset($row['name'])) $log = $log . ' (<b>' . $row['name'] . '</b>) ';

		$log = $log . 'in Tabelle <b>' . $table . '</b> wurde bearbeitet.<br/>';

		$message = 'ID ' . number_format($_POST['id'],'0', '', '.') . ' in Tabelle ' . $table . ' wurde bearbeitet.';

		$result = $database->Select('*', $table, 'id=' . $_POST['id'] . '');
		$row = $result->fetch_assoc();
		foreach ($finfo as $val)
		{
			$name = $val->name;
			$type = $val->type;
			if ($row[$name] != $_POST[$name])
			{
				if(is_numeric($row[$name]))
					$oldValue = number_format($row[$name], '2', ',', '.');
				else
					$oldValue = $row[$name];

				if(is_numeric($_POST[$name]))
					$newValue = number_format($_POST[$name], '2', ',', '.');
				else
					$newValue = $_POST[$name];

				$log = $log . ' Setze Wert <b>' . $name . '</b> von <b>' . $oldValue . '</b> zu <b>' . $newValue . '</b><br/>';
			}
		}

		$database->Update($update, $table, 'id = ' . $_POST['id'] . '', 1);
	}
	AddToLog($database, $ip, $accs, $log);
}