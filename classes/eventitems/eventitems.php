<?php
    if ($database == NULL)
    {
        print 'This File (' . __FILE__ . ') should be after Database!';
    }

    class EventItem
    {
        private $data;

        function __construct($data)
        {
            $this->data = $data;
        }

        function GetID()
        {
            return $this->data['id'];
        }

        function GetImage()
        {
            return 'img/gameevents/' . $this->data['image'];
        }

        function GetX()
        {
            return $this->data['x'];
        }

        function GetY()
        {
            return $this->data['y'];
        }

        function GetBerry()
        {
            return $this->data['zeni'];
        }

        function GetGold()
        {
            return $this->data['gold'];
        }

        function GetItem()
        {
            return $this->data['item'];
        }

        function GetStatspunkte()
        {
            return $this->data['statspunkte'];
        }

        function GetItemAmount()
        {
            return $this->data['itemamount'];
        }

        public function GetStartTime()
        {
            return $this->data['starttime'];
        }

        public function GetEndTime()
        {
            return $this->data['endtime'];
        }

        function GetActive()
        {
            return $this->data['active'];
        }

        function SetActive($value)
        {
            $this->data['active'] = $value;
        }
    }

    class EventItems
    {
        private $database;
        private $eventItems;

        function __construct($db)
        {
            $this->database = $db;
            $this->eventItems = array();
        }

        function LoadItems($url) : array
        {
            $url = $this->database->EscapeString($url);
            $result = $this->database->Select('*', 'eventitems', 'url="' . $url . '"', 99999);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    while ($row = $result->fetch_assoc())
                    {
                        $eventItem = new EventItem($row);
                        $this->eventItems[] = $eventItem;
                    }
                }
                $result->close();
            }
            return $this->eventItems;
        }

        function LoadItem($id) : ?EventItem
        {
            $result = $this->database->Select('*', 'eventitems', 'id=' . $id, 1);
            $eventItem = null;
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    if ($row = $result->fetch_assoc())
                    {
                        $eventItem = new EventItem($row);
                    }
                }
                $result->close();
            }
            return $eventItem;
        }

        function HasItem($acc, $eventItemID) : bool
        {
            $result = $this->database->Select('*', 'eventitemsunlocked', 'acc=' . $acc . ' AND eventitem=' . $eventItemID, 1);
            $hasItem = false;
            if ($result)
            {
                $hasItem = $result->num_rows > 0;
                $result->close();
            }
            return $hasItem;
        }

        function AddItem($acc, $eventItemID) : void
        {
            $this->database->Insert('acc, eventitem', '"' . $acc . '","' . $eventItemID . '"', 'eventitemsunlocked');
        }
    }
