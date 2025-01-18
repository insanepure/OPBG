<?php
    if ($database == NULL)
    {
        print 'This File (' . __FILE__ . ') should be after Database!';
    }

    include_once 'item.php';

    class ItemManager
    {

        private $database;
        private $items;
        private $arenaitems;

        function __construct($db)
        {
            $this->database = $db;
            $this->items = array();
            $this->arenaitems = array();
            $this->LoadItems();
        }

        private function LoadItems() : void
        {
            $result = $this->database->Select('*', 'items', '', 99999);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    while ($row = $result->fetch_assoc())
                    {
                        $item = new Item($row);
                        array_push($this->items, $item);
                        if($item->GetArenaPoints() > 0)
                            array_push($this->arenaitems, $item);
                    }
                }
                $result->close();
            }
        }

        public function GetArenaitems() : ?array
        {
            if(count($this->arenaitems) == 0)
                return array();
            return $this->arenaitems;
        }

        public function GetItemByName(string $name) : ?Item
        {
            $i = 0;
            while (isset($this->items[$i]))
            {
                if ($this->items[$i]->GetName() == $name)
                {
                    return $this->items[$i];
                }
                ++$i;
            }
            return null;
        }

        public function GetItem(int $id) : ?Item
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
    }
