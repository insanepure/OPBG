<?php
    if ($database == NULL)
    {
        print 'This File (' . __FILE__ . ') should be after Database!';
    }

    include_once 'marketitem.php';

    class Market
    {

        private $database;
        private $items;

        function __construct($db)
        {
            $this->database = $db;
            $this->items = array();
            $this->LoadItems();
        }

        public function HasItemInside(int $statsID, int $visualID, int $statstype, int $upgrade, int $seller) : bool
        {
            $i = 0;
            $item = $this->GetItem($i);
            while (isset($item))
            {
                if (
                    $item->GetSellerID() == $seller && $item->GetStatsID() == $statsID && $item->GetVisualID() == $visualID
                    && $item->GetStatsType() == $statstype && $item->GetUpgrade() == $upgrade
                )
                {
                    return true;
                }
                ++$i;
                $item = $this->GetItem($i);
            }
            return false;
        }

        public function RemoveItem(int $id) : void
        {
            $itemIdx = $this->GetItemIndex($id);
            $item = $this->GetItem($itemIdx);
            $this->database->Delete('market', '`id`="' . $item->GetID() . '"', 1);
            array_splice($this->items, $itemIdx, 1);
        }

        public function TakeItem(int $id, int $amount) : void
        {
            $itemIdx = $this->GetItemIndex($id);
            $item = $this->GetItem($itemIdx);
            $newAmount = $item->GetAmount() - $amount;
            if ($newAmount == 0)
            {
                $this->database->Delete('market', '`id`="' . $item->GetID() . '"', 1);
                array_splice($this->items, $itemIdx, 1);
            }
            else
            {
                $this->database->Update('`amount`="' . $newAmount . '"', 'market', '`id`="' . $item->GetID() . '"', 1);
                $item->SetAmount($newAmount);
            }
        }

        public function SetGebot(int $id, int $playerid, int $gebot) : void
        {
            $this->database->Update('bieter='.$playerid.',gebot=' . $gebot, 'market', '`id`=' . $id, 1);
        }

        public function AddItem(int $statsid, int $visualid, int $statstype, int $upgrade, int $amount, int $price, Player $player, string $owners, int $buyer = 0, int $gebot = 0)
        {
            foreach ($this->items as &$item)
            {
                if ($item->GetSellerID() != $player->GetID())
                    continue;
                if ($item->GetStatsID() != $statsid)
                    continue;
                if ($item->GetVisualID() != $visualid)
                    continue;
                if ($item->GetStatsType() != $statstype)
                    continue;
                if ($item->GetUpgrade() != $upgrade)
                    continue;
                if ($item->GetPrice() != $price)
                    continue;
                if($item->GetType() == 3)
                    continue;

                $newAmount = $item->GetAmount() + $amount;
                $item->SetAmount($newAmount);
                $this->database->Update('`amount`="' . $newAmount . '"', 'market', '`id`="' . $item->GetID() . '"', 1);
                return;
            }

            if($buyer == null)
            {
                $buyer = '0';
            }

            $dauer = date('Y-m-d H:i:s', strtotime(' + 2 days'));

            $this->database->Insert(
                '`seller`, `sellerid`, `statsid`, `visualid`, `statstype`, `upgrade`, `amount`, `price`, `formerowners`, `kaeufer`, `gebot`, `dauer`',
                '"' . $player->GetName() . '","' . $player->GetID() . '","' . $statsid . '","' . $visualid . '","' . $statstype . '","' . $upgrade . '","' . $amount . '","' . $price . '","' . $owners . '","'.$buyer . '","'.$gebot.'","'.$dauer.'"',
                'market'
            );
            $id = $this->database->GetLastID();
            $marketitem = $this->LoadItem($id);
            array_push($this->items, $marketitem);
        }

        private function LoadItem(int $id) : ?MarketItem
        {
            $select = 'market.id, 
            market.statsid, 
            market.visualid, 
            market.sellerid,
            market.statstype,
            market.upgrade,
            market.seller,
            market.amount,
            market.price,
            market.formerowners,
            market.kaeufer,
            market.gebot,
            market.bieter,
            market.dauer,
            visualitem.name, 
            visualitem.image,
            visualitem.equippedimage,
            visualitem.description,
            visualitem.hoverdescription,
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
            statsitem.fightattack,
            statsitem.needitem,
            statsitem.race,
            statsitem.trainbonus,
            statsitem.upgradeid,
            statsitem.slot';
            $where = 'market.id = ' . $id;
            $order = 'market.statsid, market.visualid, market.price, market.id';
            $join = 'items statsitem ON market.statsid = statsitem.id JOIN items visualitem ON market.visualid = visualitem.id';
            $from = 'market';
            $result = $this->database->Select($select, $from, $where, 1, $order, 'ASC', $join);

            $item = null;
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    while ($row = $result->fetch_assoc())
                    {
                        $item = new MarketItem($row);
                    }
                }
                $result->close();
            }

            return $item;
        }

        private function LoadItems()
        {
            $select = 'market.id, 
            market.statsid, 
            market.visualid, 
            market.sellerid,
            market.seller,
            market.statstype,
            market.upgrade,
            market.amount,
            market.price,
            market.formerowners,
            market.kaeufer,
            market.gebot,
            market.bieter,
            market.dauer,
            visualitem.name, 
            visualitem.image,
            visualitem.equippedimage,
            visualitem.description,
            visualitem.hoverdescription,
            statsitem.type,
            statsitem.lp, 
            statsitem.kp,
            statsitem.attack,
            statsitem.defense,
            statsitem.value,
            statsitem.travelbonus,
            statsitem.upgradedivider,
            statsitem.lv,
            statsitem.sellable,
            statsitem.premium,
            statsitem.marktplatz,
            statsitem.category,
            statsitem.fightattack,
            statsitem.needitem,
            statsitem.race,
            statsitem.trainbonus,
            statsitem.upgradeid,
            statsitem.slot';
            $where = '';
            $order = 'market.statsid, market.visualid, market.price, market.id';
            $join = 'items statsitem ON market.statsid = statsitem.id JOIN items visualitem ON market.visualid = visualitem.id';
            $from = 'market';
            $result = $this->database->Select($select, $from, $where, 999999, $order, 'ASC', $join);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    while ($row = $result->fetch_assoc())
                    {
                        $marketitem = new MarketItem($row);
                        array_push($this->items, $marketitem);
                    }
                }
                $result->close();
            }
        }

        public function GetItemIndex(int $id) : int
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
            return 0;
        }

        public function GetItemByID(int $id) : ?MarketItem
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

        public function GetItem(int $id) : ?MarketItem
        {
            if (count($this->items) > $id)
            {
                return $this->items[$id];
            }
            return null;
        }
    }
