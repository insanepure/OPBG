<?php
if ($database == NULL)
{
	print 'This File (' . __FILE__ . ') should be after Database!';
}


class InventoryItem
{
	private $data;

	function __construct($data)
	{
		$this->data = $data;
	}

	public function GetItems()
	{
		return $this->data;
	}

	public function GetID()
	{
		return $this->data['id'];
	}

	public function GetAmount()
	{
		return $this->data['amount'];
	}

	public function SetAmount($value)
	{
		$this->data['amount'] = $value;
	}

	public function GetStatsType()
	{
		return $this->data['statstype'];
	}

    public function IsProtected()
    {
        return $this->data['protected'];
    }

    public function SetProtected($value)
    {
        $this->data['protected'] = $value;
    }

	public function SetStatsType($value)
	{
		$this->data['statstype'] = $value;
	}

	public function GetValue()
	{
		return $this->data['value'];
	}

	public function SetValue($value)
	{
		$this->data['value'] = $value;
	}

	public function GetUpgrade()
	{
		return $this->data['upgrade'];
	}

	public function GetCalculateUpgrade()
	{
		return $this->data['upgrade'] + 1;
	}

	public function SetUpgrade($value)
	{
		$this->data['upgrade'] = $value;
	}

	public function GetMaxUpgrade()
	{
		return $this->data['maxupgrade'];
	}

	public function GetVisualID()
	{
		return $this->data['visualid'];
	}

	public function SetVisualID($value)
	{
		$this->data['visualid'] = $value;
	}

	public function GetStatsID()
	{
		return $this->data['statsid'];
	}

	public function SetStatsID($value)
	{
		$this->data['statsid'] = $value;
	}

	public function CanChangeType()
	{
		return $this->data['changetype'];
	}

	public function CanUpgrade()
	{
		return $this->data['changeupgrade'];
	}

	public function GetUpgradeDivider()
	{
		return $this->data['upgradedivider'];
	}

	public function IsEquipped()
	{
		return $this->data['equipped'];
	}

    public function IsStored()
    {
        return $this->data['isstored'];
    }

    public function SetStored($value)
    {
        $this->data['isstored'] = $value;
    }

	public function SetEquipped($value)
	{
		$this->data['equipped'] = $value;
	}

	private function GetTypeName()
	{
		switch ($this->GetStatsType())
		{
			case 0: //Alle
				return 'der Balance';
				break;
			case 1: //Angriff
				return 'des Angriffes';
				break;
			case 2: //Abwehr
				return 'der Verteidigung';
				break;
			case 3: //LP
				return 'des Lebens';
				break;
			case 4: //AD
				return 'der Kraft';
				break;
			case 5: //Angriff+Abwehr
				return 'der Kontrolle';
				break;
			case 6: //Angriff+LP
				return 'der Stärke';
				break;
			case 7: //Angriff+AD
				return 'der Zerstörung';
				break;
			case 8: //Abwehr+LP
				return 'der Ehre';
				break;
			case 9: //Abwehr+AD
				return 'des Schutzes';
				break;
			case 10: //LP+AD
				return 'der Energie';
				break;
			case 11: //Angriff+Abwehr+LP
				return 'des Ausgleiches';
				break;
			case 12: //Angriff+Abwehr+AD
				return 'der Macht';
				break;
			case 13: //Angriff+LP+AD
				return 'der Offensive';
				break;
			case 14: //Abwehr+LP+AD
				return 'der Defensive';
				break;
		}
        return '';
	}

	public function GetWear()
	{
		return $this->data['wear'];
	}

	public function GetFormerOwners()
	{
		return $this->data['formerowners'];
	}

	public function SetWear($value)
	{
		$this->data['wear'] = $value;
	}

	public function GetRealName()
	{
		return $this->data['name'];
	}

	public function GetName()
	{
		$returnValue = $this->data['name'];

		if ($this->GetUpgrade() != 0)
			$returnValue = $returnValue . ' Level ' . $this->GetCalculateUpgrade();

		return $returnValue;
	}

	public function SetName($value)
	{
		$this->data['name'] = $value;
	}

	public function GetRepairCount()
	{
		return $this->data['repaircount'];
	}

    public function SetRepairCount($value)
    {
        $this->data['repaircount'] = $value;
    }

	public function GetImage()
	{
		return $this->data['image'];
	}

	public function SetImage($value)
	{
		$this->data['image'] = $value;
	}

	public function IsSellable()
	{
		return $this->data['sellable'];
	}

	public function IsPremium()
	{
		return $this->data['premium'];
	}

	public function IsMarktplatz()
	{
		return $this->data['marktplatz'];
	}

	public function GetNeedItem()
	{
		return $this->data['needitem'];
	}

	public function GetRace()
	{
		return $this->data['race'];
	}

	public function GetFightAttack()
	{
		return $this->data['fightattack'];
	}

	public function IsOnTop()
	{
		return $this->data['ontop'];
	}

	public function GetEquippedImage()
	{
		return $this->data['equippedimage'];
	}

	public function SetEquippedImage($value)
	{
		$this->data['equippedimage'] = $value;
	}

	public function GetSlot()
	{
		return $this->data['slot'];
	}

	public function GetTravelBonus()
	{
		return $this->data['travelbonus'];
	}

	public function GetDescription()
	{
		return $this->data['description'];
	}

    public function GetHoverDescription()
    {
        return $this->data['hoverdescription'];
    }

	public function GetCategory()
	{
		return $this->data['category'];
	}

	public function GetPrice()
	{
		return $this->data['price'] + floor($this->data['price'] / 10 * $this->GetUpgrade());
	}

	public function GetArenaPoints()
	{
		return $this->data['arenapoints'];
	}

	public function GetType()
	{
		return $this->data['type'];
	}

    public function GetSchatzitems()
    {
        return explode(';',$this->data['items']);
    }

	private function GetStatsValue($validTypes, $multiplier)
	{
		$value = $this->GetValue();
		$statsType = $this->GetStatsType();
		if (!in_array($statsType, $validTypes) || $value == 0)
			return 0;

		$divider = 1;
		switch ($statsType)
		{
			case 0:
				$divider = 1;
				break;
			case 1:
			case 2:
			case 3:
			case 4:
				$divider = 1;
				break;
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
				$divider = 1;
				break;
			case 11:
			case 12:
			case 13:
			case 14:
				$divider = 3;
				break;
		}

		$upgradeDivider = $this->GetUpgradeDivider();
		if ($upgradeDivider == 0)
			$upgradeDivider = 1;

		$addValue = $value / $upgradeDivider * $this->GetUpgrade();

		$value = $value + $addValue;
		$value = $value / $divider;
		$value = floor($value);

		$value = $value * $multiplier;
		return $value;
	}

	private function GetStatsValueNew($multiplier, $value)
	{
		if (in_array($this->GetStatsID(), array(11)))
			$value = $this->GetValue();
		if ($value == 0)
			return 0;

		$upgradeDivider = $this->GetUpgradeDivider();
		if ($upgradeDivider == 0)
			$upgradeDivider = 1;

		$addValue = $value / $upgradeDivider * $this->GetUpgrade();

		$value = $value + $addValue;
		$value = floor($value);

		$value = $value * $multiplier;
		return $value;
	}

	public function GetLP()
	{
		//$aura = array(125, 126, 127, 128, 129, 130);
		if ($this->GetType() == 3)
			$value = $this->GetStatsValueNew(10, $this->data['lp']);
		else
			$value = $this->data['lp'];
		return $value;
	}

	public function GetKP()
	{
		//$aura = array(125, 126, 127, 128, 129, 130);
		if ($this->GetType() == 3)
			$value = $this->GetStatsValueNew(10, $this->data['kp']);
		else
			$value = $this->data['kp'];
		return $value;
	}

	public function GetAttack()
	{
		//$aura = array(125, 126, 127, 128, 129, 130);
		if ($this->GetType() == 3)
			$value = $this->GetStatsValueNew(1, $this->data['attack']);
		else
			$value = $this->data['attack'];
		return $value;
	}

	public function GetDefense()
	{
		//$aura = array(125, 126, 127, 128, 129, 130);
		if ($this->GetType() == 3)
			$value = $this->GetStatsValueNew(1, $this->data['defense']);
		else
			$value = $this->data['defense'];
		return $value;
	}

	public function GetTrainbonus()
	{
		return $this->data['trainbonus'];
	}

	public function GetLevel()
	{
		return $this->data['lv'];
	}

	public function GetUpgradeID()
	{
		return $this->data['upgradeid'];
	}

	public function DisplayEffect()
	{
        $text = '';
		switch ($this->GetType())
		{
			case 1:
				{
					if ($this->GetLP() != 0) $text .= 'Heilt <b>' . number_format($this->GetLP(), '0', '', '.') . '</b> <b>LP</b><br/>';
					if ($this->GetKP() != 0) $text .= 'Heilt <b>' . number_format($this->GetKP(), '0', '', '.') . '</b> <b>AD</b><br/>';
				}
				break;
			case 2:
				{
					if ($this->GetLP() != 0) $text .= 'Heilt <b>' . number_format($this->GetLP(), '0', '', '.') . '%</b> <b>LP</b><br/>';
					if ($this->GetKP() != 0) $text .= 'Heilt <b>' . number_format($this->GetKP(), '0', '', '.') . '%</b> <b>AD</b><br/>';
				}
				break;
			case 3:
				{
					if ($this->GetLP() != 0) $text .= 'Erhöht <b>LP</b> um ' . number_format($this->GetLP(), '0', '', '.') . '</b><br/>';
					if ($this->GetKP() != 0) $text .= 'Erhöht <b>AD</b> um ' . number_format($this->GetKP(), '0', '', '.') . '</b><br/>';
					if ($this->GetAttack() != 0) $text .= 'Erhöht <b>Angriff</b> um ' . number_format($this->GetAttack(), '0', '', '.') . '</b><br/>';
					if ($this->GetDefense() != 0) $text .= 'Erhöht <b>Verteidigung</b> um ' . number_format($this->GetDefense(), '0', '', '.') . '</b><br/>';
				}
				break;
			case 4:
				{
					if ($this->GetTravelBonus() != 0) $text .= 'Verringert <b>Reisedauer um <b>' . number_format($this->GetTravelBonus(), '0', '', '.') . '%</b>';
				}
				break;
		}
        return $text;
	}

	public function HasOverlay()
	{
		return $this->data['overlay'] != 0;
	}

	public function GetOverlay()
	{
		switch ($this->data['overlay'])
		{
			case 1:
				return 'OverlayEvent';
				break;
		}
		return '';
	}

    public function IsVisible()
    {
        return $this->data['visible'];
    }

    public function GetReihenfolge()
    {
        return $this->data['reihenfolge'];
    }
}


class Inventory
{
	private $database;
	private $ownerid = 0;
	private $items;
	private $equippedItems;
	private	$hasGanfortKey;
	private $ganfortKeyID = 226;
	private $shipIDs = array(1, 40, 41, 42, 43, 44, 45, 46, 47, 48, 384, 385);

	function __construct($database, $ownerid)
	{
		$this->database = $database;

		$this->ownerid = $ownerid;

		$this->items = array();
		$this->equippedItems = array();
        $this->hasGanfortKey = false;

		$this->LoadData($ownerid);
	}

	public function GetCount()
	{
		return count($this->items);
	}

    public function HasGanfortKey()
    {
        return $this->hasGanfortKey;
    }

	public function GetShipWear($id = 0)
	{
        $where = " and equipped = 1";
        if($id != 0)
            $where = " and statsid=".$id;

		$result = $this->database->Select("statsid,wear", "inventory", "ownerid=" . $this->ownerid . $where);
		if ($result)
		{
			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{
					if (in_array($row['statsid'], $this->shipIDs))
                        return $row['wear'];
				}
			}
		}
		return -1;
	}

	public function GetShipMaxWear($shipid)
	{
		$result = $this->database->Select("uses", "items", "id=" . $shipid);
		if ($result)
		{
			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{
					return $row['uses'];
				}
			}
			$result->close();
		}
		return -1;
	}

    public function GetShipRepairCount($id): int
    {
        $result = $this->database->Select('repaircount', 'inventory', 'statsid='.$id.', ownerid='.$this->ownerid, 1);
        if($result)
        {
            if($result->num_rows > 0)
            {
                if($row = $result->fetch_assoc())
                    return $row['repaircount'];
            }
        }
        return -1;
    }

	public function AddShipWear()
	{
        $this->database->Update('wear=wear+1', 'inventory', 'id=' . $this->GetShip() .' AND ownerid='.$this->ownerid, 1);
	}

	public function HasShip($id = 0)
	{
        if($id == 0) {
            $result = $this->database->Select("statsid", "inventory", "ownerid=" . $this->ownerid);
            if ($result) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if (in_array($row['statsid'], $this->shipIDs))
                            return true;
                    }
                }
                $result->close();
            }
        }
        else
        {
            $this->HasItem(1);
        }
		return false;
	}

    public function HasShipEquipped($id)
    {
        $result = $this->database->Select("statsid", "inventory", "ownerid=" . $this->ownerid .  " AND equipped=1 AND id=".$id);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                while ($row = $result->fetch_assoc())
                {
                    if (in_array($row['statsid'], $this->shipIDs))
                        return true;
                }
            }
            $result->close();
        }
        return false;
    }

	public function GetShip()
	{
		$result = $this->database->Select("id, statsid", "inventory", "ownerid=" . $this->ownerid . " AND equipped=1");
		if ($result)
		{
			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{
					if (in_array($row['statsid'], $this->shipIDs))
                        return $row['id'];
				}
			}
			$result->close();
		}
		return -1;
	}

    public function RemoveEinfacheSplitter($value)
    {
        $splitter = $this->GetItemByStatsIDOnly(315);
        $this->RemoveItem($splitter, $value);
    }

    public function GetEinfacheSplitter()
    {
        $result = $this->database->Select("amount", "inventory", "statsid=315 AND ownerid=" . $this->ownerid, 1);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                    return $row['amount'];
            }
            $result->close();
        }
        return 0;
    }

    public function GetItemAmount($item)
    {
        $result = $this->database->Select("amount", "inventory", "statsid=".$item." AND ownerid=" . $this->ownerid, 1);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                return $row['amount'];
            }
            $result->close();
        }
        return 0;
    }

    public function HasImpeldownKey($level): bool
    {
        $item = 0;
        if($level == 1) $item = 388;
        if($level == 2) $item = 389;
        if($level == 3) $item = 390;
        if($level == 4) $item = 391;
        if($level == 5) $item = 392;
        if($level == 6) $item = 393;
        $result = $this->database->Select('*', 'inventory', 'statsid='.$item.' AND ownerid='.$this->ownerid);
        if($result && $result->num_rows > 0)
            return true;
        return false;
    }

    public function GetPathItems(): array
    {
        $result = $this->database->Select("*", "inventory", "statsid >= 52 AND statsid <= 57 AND ownerid=".$this->ownerid);
        $fruits = array();
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc()) {
                    $itemManager = new ItemManager($this->database);
                    $item = $itemManager->GetItem($row['statsid']);
                    $fruits[] = array($row['statsid'], $row['amount'], $item->GetName());
                }
            }
            $result->close();
        }
        return $fruits;
    }

    public function GetPathItem($id): array
    {
        $result = $this->database->Select("*", "inventory", "statsid = ".$id." AND ownerid=".$this->ownerid, 1);
        $fruit = array();
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                if($row = $result->fetch_assoc())
                {
                    $itemManager = new ItemManager($this->database);
                    $item = $itemManager->GetItem($row['statsid']);
                    $fruit = array($row['statsid'], $row['amount'], $item->GetName());
                }
            }
            $result->close();
        }
        return $fruit;
    }

    public function GetPathWeapons(): array
    {
        $result = $this->database->Select("*", "inventory", "statsid >= 55 AND statsid <= 57 AND ownerid=".$this->ownerid);
        $fruits = array();
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc()) {
                    $itemManager = new ItemManager($this->database);
                    $item = $itemManager->GetItem($row['statsid']);
                    $fruits[] = array($row['statsid'], $row['amount'], $item->GetName());
                }
            }
            $result->close();
        }
        return $fruits;
    }

    public function RemoveSelteneSplitter($value)
    {
        $splitter = $this->GetItemByStatsIDOnly(316);
        $this->RemoveItem($splitter, $value);
    }

    public function GetStorageCount(): int
    {
        $result = $this->database->Select('*', 'inventory', 'ownerid='.$this->ownerid.' and isstored=1');
        if($result)
        {
            return $result->num_rows;
        }
        return 0;
    }

    public function GetSelteneSplitter()
    {
        $result = $this->database->Select("amount", "inventory", "statsid=316 AND ownerid=" . $this->ownerid, 1);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                return $row['amount'];
            }
            $result->close();
        }
        return 0;
    }

    public function RemoveLegendaereSplitter($value)
    {
        $splitter = $this->GetItemByStatsIDOnly(317);
        $this->RemoveItem($splitter, $value);
    }

    public function GetLegendaereSplitter()
    {
        $result = $this->database->Select("amount", "inventory", "statsid=317 AND ownerid=" . $this->ownerid, 1);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                return $row['amount'];
            }
            $result->close();
        }
        return 0;
    }

    public function GetShipItemID(): int
    {
        $result = $this->database->Select("id, statsid", "inventory", "ownerid=" . $this->ownerid . " AND equipped=1");
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                while ($row = $result->fetch_assoc())
                {
                    if (in_array($row['statsid'], $this->shipIDs))
                        return $row['statsid'];
                }
            }
            $result->close();
        }
        return -1;
    }

    public function SetHasGanfortKey($value)
    {
        $this->hasGanfortKey = $value;
    }

	public function SetHasShip($value, $id = -1)
	{
		if ($value == -1)
            $value = $id;
		if ($id == -1)
            $this->database->Update('equipped=1', 'inventory', 'id = ' . $value . '', 1);
		else
            $this->database->Update('equipped=0', 'inventory', 'id = ' . $value . '', 1);
	}

    public function GetItems()
    {
        return $this->items;
    }

	public function GetItem($id) : ?InventoryItem
	{
		if (count($this->items) > $id && $id >= 0)
		{
			return $this->items[$id];
		}
		return null;
	}

    public function GetItemsTest()
    {
        return $this->items;
    }

	public function Recycle()
	{
		$rand = rand(49, 51);
		$itemManager = new ItemManager($this->database);
		$rec = $itemManager->GetItem($rand);
		$this->AddItem($rec, $rec, 1);
        return "Das Schiff wurde recycelt. Du erhältst 1x " . $rec->GetName();
	}

	public function Repair($wood, $needles, $fabric, $ship)
	{
		$shipArray = array(1, 40, 41, 42, 43, 44, 45, 46, 47, 48, 384, 385);
		if (in_array($ship->GetStatsID(), $shipArray))
		{
			$hwood = $this->GetMaterialAmount(1);
			$hneedles = $this->GetMaterialAmount(2);
			$hfabric = $this->GetMaterialAmount(3);

			$wood = $hwood - $wood;
			$needles = $hneedles - $needles;
			$fabric = $hfabric - $fabric;

			$this->database->Update("wear=0, repaircount=repaircount+1", "inventory", "id=" . $ship->GetID(), 1);

			if ($wood > 0)
                $this->database->Update("amount=" . $wood, "inventory", "id=" . $this->GetMaterialID(1), 1);
			else
                $this->database->Delete('inventory', 'id = ' . $this->GetMaterialID(1) . '', 1);

			if ($needles > 0)
                $this->database->Update("amount=" . $needles, "inventory", "id=" . $this->GetMaterialID(2), 1);
			else
                $this->database->Delete('inventory', 'id = ' . $this->GetMaterialID(2) . '', 1);

			if ($fabric > 0)
                $this->database->Update("amount=" . $fabric, "inventory", "id=" . $this->GetMaterialID(3), 1);
			else
                $this->database->Delete('inventory', 'id = ' . $this->GetMaterialID(3) . '', 1);

			return "Das Schiff wurde repariert!";
		}
		return "Das ist kein Schiff!";
	}

	public function GetMaterialID($material)
	{
		if ($material == 1)
		{
			$result = $this->database->Select("id", "inventory", "statsid=49 and visualid=49 and ownerid=" . $this->ownerid);
			if ($result)
			{
				if ($result->num_rows > 0)
				{
					while ($row = $result->fetch_assoc())
					{
						return $row['id'];
					}
				}
				$result->close();
			}
			return -1;
		}
		else if ($material == 2)
		{
			$result = $this->database->Select("id", "inventory", "statsid=50 and visualid=50 and ownerid=" . $this->ownerid);
			if ($result)
			{
				if ($result->num_rows > 0)
				{
					while ($row = $result->fetch_assoc())
					{
						return $row['id'];
					}
				}
				$result->close();
			}
			return -1;
		}
		else if ($material == 3)
		{
			$result = $this->database->Select("id", "inventory", "statsid=51 and visualid=51 and ownerid=" . $this->ownerid);
			if ($result)
			{
				if ($result->num_rows > 0)
				{
					while ($row = $result->fetch_assoc())
					{
						return $row['id'];
					}
				}
				$result->close();
			}
			return -1;
		}
		return -1;
	}

	public function GetMaterialAmount($material)
	{
		if ($material == 1)
		{
			$result = $this->database->Select("amount", "inventory", "statsid=49 and visualid=49 and ownerid=" . $this->ownerid);
			if ($result)
			{
				if ($result->num_rows > 0)
				{
					while ($row = $result->fetch_assoc())
					{
						return $row['amount'];
					}
				}
				$result->close();
			}
			return -1;
		}
		else if ($material == 2)
		{
			$result = $this->database->Select("amount", "inventory", "statsid=50 and visualid=50 and ownerid=" . $this->ownerid);
			if ($result)
			{
				if ($result->num_rows > 0)
				{
					while ($row = $result->fetch_assoc())
					{
						return $row['amount'];
					}
				}
				$result->close();
			}
			return -1;
		}
		else if ($material == 3)
		{
			$result = $this->database->Select("amount", "inventory", "statsid=51 and visualid=51 and ownerid=" . $this->ownerid);
			if ($result)
			{
				if ($result->num_rows > 0)
				{
					while ($row = $result->fetch_assoc())
					{
						return $row['amount'];
					}
				}
				$result->close();
			}
			return -1;
		}
		return -1;
	}

	public function GetItemByIDOnly($statsid, $visualid) : ?InventoryItem
	{
		for ($i = 0; $i < count($this->items); ++$i)
		{
			if ($this->items[$i]->GetStatsID() == $statsid && $this->items[$i]->GetVisualID() == $visualid)
				return $this->items[$i];
		}
		return null;
	}

	public function GetItemByStatsIDOnly($statsid) : ?InventoryItem
	{
		for ($i = 0; $i < count($this->items); ++$i)
		{
			if ($this->items[$i]->GetStatsID() == $statsid)
				return $this->items[$i];
		}
		return null;
	}

	public function GetItemByVisualIDOnly($visualid) : ?InventoryItem
	{
		for ($i = 0; $i < count($this->items); ++$i)
		{
			if ($this->items[$i]->GetVisualID() == $visualid)
				return $this->items[$i];
		}
		return null;
	}

	public function GetItemByID($statsid, $visualid, $statstype, $upgrade) : ?InventoryItem
	{
		for ($i = 0; $i < count($this->items); ++$i)
		{
			if (
				$this->items[$i]->GetStatsID() == $statsid && $this->items[$i]->GetVisualID() == $visualid &&
				$this->items[$i]->GetStatsType() == $statstype && $this->items[$i]->GetUpgrade() == $upgrade
			)
				return $this->items[$i];
		}
		return null;
	}

	public function GetItemIndexByID($statsid, $visualid, $statstype, $upgrade)
	{
		for ($i = 0; $i < count($this->items); ++$i)
		{
			if (
				$this->items[$i]->GetStatsID() == $statsid && $this->items[$i]->GetVisualID() == $visualid &&
				$this->items[$i]->GetStatsType() == $statstype && $this->items[$i]->GetUpgrade() == $upgrade
			)
				return $i;
		}
		return -1;
	}

	public function GetItemID($id)
	{
		$i = 0;
		while (isset($this->items[$i]))
		{
			if ($this->items[$i]->GetID() == $id)
			{
				return $i;
			}
			++$i;
		}
		return $i;
	}

	public function GetItemByDatabaseID($id) : ?InventoryItem
	{
		$i = 0;
		while (isset($this->items[$i]))
		{
			if ($this->items[$i]->GetID() == $id)
			{
				return $this->items[$i];
			}
			++$i;
		}
		return null;
	}

	public function GetItemAtSlot($slot) : ?InventoryItem
	{
		if (isset($this->equippedItems[$slot]))
		{
			return $this->equippedItems[$slot];
		}
		return null;
	}

	public function HasItem($id)
	{
		$i = 0;
		while (isset($this->items[$i]))
		{
			if ($this->items[$i]->GetID() == $id)
			{
				return true;
			}
			++$i;
		}
		return false;
	}


	public function HasItemWithID($statsid, $visualid)
	{
		$i = 0;
		while (isset($this->items[$i]))
		{
			if ($this->items[$i]->GetStatsID() == $statsid && $this->items[$i]->GetVisualID() == $visualid)
			{
				return true;
			}
			++$i;
		}
		return false;
	}


	public function HasItemWithStatsID($statsid)
	{
		$i = 0;
		while (isset($this->items[$i]))
		{
			if ($this->items[$i]->GetStatsID() == $statsid)
			{
				return true;
			}
			++$i;
		}
		return false;
	}


	public function CombineItems($statsitem, $visualitem)
	{
		$visualID = $visualitem->GetID();
		$id = $this->GetItemID($visualID);

		$statsitem->SetVisualID($visualID);
		$statsitem->SetName($visualitem->GetRealName());
		$statsitem->SetImage($visualitem->GetImage());
		$statsitem->SetEquippedImage($visualitem->GetEquippedImage());

		$this->database->Update('visualid=' . $visualitem->GetVisualID() . '', 'inventory', 'id = ' . $statsitem->GetID() . '', 1);

		array_splice($this->items, $id, 1);
		$this->database->Delete('inventory', 'id = ' . $visualitem->GetID() . '', 1);
	}


	public function RevertCombineItems($visualitem, $statsitem)
	{
		$visualitem->SetVisualID($visualitem->GetStatsID());
		$visualitem->SetName($statsitem->GetName());
		$visualitem->SetImage($statsitem->GetImage());
		$visualitem->SetEquippedImage($statsitem->GetEquippedImage());
		$this->database->Update('visualid=' . $visualitem->GetStatsID() . '', 'inventory', 'id = ' . $visualitem->GetID() . '', 1);
	}


	public function EquipItem($item)
	{
		$slot = $item->GetSlot();
		$item->SetEquipped(1);
		$this->equippedItems[$slot] = $item;
		$this->database->Update('equipped="1"', 'inventory', 'id = ' . $item->GetID() . '', 1);
	}

	public function UnequipItem($item)
	{
		$slot = $item->GetSlot();
		$item->SetEquipped(0);
		unset($this->equippedItems[$slot]);
		$this->database->Update('equipped="0"', 'inventory', 'id = ' . $item->GetID() . '', 1);
	}

	public function AddItem($statsitem, $visualitem, $amount, $statstype = 0, $upgrade = 0, $owners = '') : ?InventoryItem
	{
		$inventoryItem = null;

		if ($statsitem->GetType() != 3 && $statsitem->GetType() != 4)
		{
			$inventoryItem = $this->GetItemByID($statsitem->GetID(), $visualitem->GetID(), $statstype, $upgrade);
		}

        if ($statsitem->GetID() == $this->ganfortKeyID)
        {
            $this->SetHasGanfortKey(true);
        }

		if (in_array($statsitem->GetID(), $this->shipIDs))
		{
			$this->SetHasShip(1, $statsitem->GetID());
		}

		if ($inventoryItem == null)
		{

			$i = 0;
			//Add only one if type == 3
			while ($i != $amount)
			{
				$inventoryItem = null;
				$addAmount = 1;
				if ($statsitem->GetType() == 3)
				{
					++$i;
				}
				else
				{
					$addAmount = $amount;
					$i = $amount;
				}
				$this->database->Insert(
					'statsid, visualid, ownerid, amount, statstype, upgrade, formerowners',
					'"' . $statsitem->GetID() . '","' . $visualitem->GetID() . '","' . $this->ownerid . '","' . $addAmount . '","' . $statstype . '","' . $upgrade . '","' . $owners . '"',
					'inventory'
				);
				$id = $this->database->GetLastID();
				$inventoryItem = $this->LoadItem($id);
				$this->AddInventoryItem($inventoryItem);
			}
		}
		else
		{
			$itemAmount = $inventoryItem->GetAmount() + $amount;
			$inventoryItem->SetAmount($itemAmount);
			$this->database->Update('amount="' . $itemAmount . '"', 'inventory', 'id = ' . $inventoryItem->GetID() . '', 1);
		}

		return $inventoryItem;
	}

	public function RemoveItem($item, $amount)
	{
        if(is_null($item)) return;
		$itemAmount = $item->GetAmount() - $amount;
		if ($itemAmount > 0)
		{
			$item->SetAmount($itemAmount);
			$this->database->Update('amount="' . $itemAmount . '"', 'inventory', 'id = ' . $item->GetID() . '', 1);
		}
		else
		{
            if ($item->GetID() == $this->ganfortKeyID)
            {
                $this->SetHasGanfortKey(false);
            }
			array_splice($this->items, $this->GetItemID($item->GetID()), 1);
			$this->database->Delete('inventory', 'id = ' . $item->GetID() . '', 1);
		}
	}

	private function LoadItem($id) : ?InventoryItem
	{
		$select = 'inventory.id, 
			inventory.statsid, 
			inventory.visualid, 
			inventory.ownerid, 
			inventory.amount,
			inventory.equipped,
			inventory.statstype,
			inventory.upgrade,
			inventory.wear,
			inventory.formerowners,
			inventory.repaircount,
			inventory.reihenfolge,
			visualitem.name, 
			visualitem.image,
			visualitem.equippedimage,
			visualitem.description,
			visualitem.hoverdescription,
			visualitem.ontop,
			statsitem.type,
			statsitem.lp, 
			statsitem.kp,
			statsitem.attack,
			statsitem.defense,
			statsitem.value,
			statsitem.travelbonus,
			statsitem.lv,
			statsitem.sellable,
			statsitem.premium,
			statsitem.marktplatz,
			statsitem.category,
			statsitem.price,
			statsitem.fightattack,
			statsitem.needitem,
			statsitem.race,
			statsitem.trainbonus,
			statsitem.upgradeid,
			statsitem.slot,
			statsitem.items,
			visualitem.visible,
            inventory.protected,
            inventory.isstored';
		$where = 'inventory.id = ' . $id;
		$order = 'inventory.id';
		$join = 'items statsitem ON inventory.statsid = statsitem.id JOIN items visualitem ON inventory.visualid = visualitem.id';
		$from = 'inventory';
		$result = $this->database->Select($select, $from, $where, 1, $order, 'ASC', $join);

		$item = null;
		if ($result)
		{
			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{
					$item = new InventoryItem($row);
				}
			}
			$result->close();
		}

		return $item;
	}

	private function AddInventoryItem($item)
	{
        if ($item->GetStatsID() == $this->ganfortKeyID)
        {
            $this->SetHasGanfortKey(true);
        }
		/*if (in_array($item->GetStatsID(), $this->shipIDs))
		{
			$this->SetHasShip($item->GetID());
		}*/
		if ($item->IsEquipped())
		{
			$slot = $item->GetSlot();
			$this->equippedItems[$slot] = $item;
		}

		$this->items[] = $item;
	}


	private function LoadData($ownerid)
	{
		$select = 'inventory.id, 
			inventory.statsid, 
			inventory.visualid, 
			inventory.ownerid, 
			inventory.amount,
			inventory.equipped,
			inventory.statstype,
			inventory.upgrade,
			inventory.wear,
			inventory.formerowners,
			inventory.repaircount,
			inventory.reihenfolge,
			visualitem.name, 
			visualitem.image,
			visualitem.equippedimage,
			visualitem.description,
			visualitem.hoverdescription,
			visualitem.ontop,
			visualitem.overlay,
			statsitem.type,
			statsitem.lp, 
			statsitem.kp,
			statsitem.attack,
			statsitem.defense,
			statsitem.value,
			statsitem.travelbonus,
			statsitem.lv,
			statsitem.sellable,
			statsitem.premium,
			statsitem.marktplatz,
			statsitem.category,
			statsitem.price,
			statsitem.fightattack,
			statsitem.needitem,
			statsitem.race,
			statsitem.trainbonus,
			statsitem.upgradeid,
			statsitem.slot,
			statsitem.changetype,
			statsitem.changeupgrade,
			statsitem.maxupgrade,
			statsitem.upgradedivider,
			statsitem.items,
			visualitem.visible,
            inventory.protected,
            inventory.isstored';
		$where = 'inventory.ownerid = ' . $ownerid;
		$order = 'inventory.id';
		$join = 'items statsitem ON inventory.statsid = statsitem.id JOIN items visualitem ON inventory.visualid = visualitem.id';
		$from = 'inventory';
		$result = $this->database->Select($select, $from, $where, 999999, $order, 'ASC', $join);
		if ($result)
		{
			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{
					$item = new InventoryItem($row);
					$this->AddInventoryItem($item);
				}
			}
			$result->close();
		}
	}
}
