<?php
    if ($database == NULL)
    {
        print 'This File (' . __FILE__ . ') should be after Database!';
    }

    class Arena
    {
        private $database;
        private $fighter;
        private $inFight;

        function __construct($db)
        {
            $this->database = $db;
            $this->fighter = array();
            $this->inFight = array();
            $this->LoadData();
        }

        public function GetFighterCount()
        {
            return count($this->fighter);
        }

        public function IsFighterIn($id)
        {
            return in_array($id, $this->fighter);
        }

        public function HasPvpEnabled($id)
        {
            $result = $this->database->Select('kolopvp', 'arenafighter', 'fighter="'.$id.'"');
            $pvp = $result->fetch_assoc();
            return $pvp['kolopvp'] == 1;
        }

        public function UpdateFight($player1, $player2)
        {
            $this->database->Update('infight=1', 'arenafighter', 'fighter="' . $player1 . '" OR fighter="' . $player2 . '"', 2);
        }

        public function GetRandomFighter($id)
        {
           return -1;
            $tempFighter = $this->fighter;
            $tempInfight = $this->inFight;
            $maxNum = count($tempFighter);
            for ($i = 0; $i < $maxNum;)
            {
                if ($tempInfight[$i] == 1 || $tempFighter[$i] == $id)
                {
                    array_splice($tempFighter, $i, 1);
                    array_splice($tempInfight, $i, 1);
                    --$maxNum;
                }
                else
                    ++$i;
            }
            if (count($tempFighter) == 0)
                return -1;

            $enemyID = rand(0, count($tempFighter) - 1);
            return $tempFighter[$enemyID];
        }

        public function Join($id)
        {
            $this->database->Insert('fighter', $id, 'arenafighter');
            $this->fighter[count($this->fighter)] = $id;
        }

        public function Leave($id)
        {
            $this->database->Delete('arenafighter', 'fighter = "' . $id . '"', 1);

            $key = array_search($id, $this->fighter);
            array_splice($this->fighter, $key, 1);
        }

        private function LoadData()
        {
            $i = 0;
            $result = $this->database->Select('*', 'arenafighter', '', 99999);
            if ($result)
            {
                while ($row = $result->fetch_assoc())
                {
                    $this->inFight[$i] = $row['infight'];
                    $this->fighter[$i] = $row['fighter'];
                    ++$i;
                }
                $result->close();
            }
        }

        public function ReloadData()
        {
            $i = 0;
            $result = $this->database->Select('*', 'arenafighter', '', 99999);
            if ($result)
            {
                while ($row = $result->fetch_assoc())
                {
                    $this->inFight[$i] = $row['infight'];
                    $this->fighter[$i] = $row['fighter'];
                    ++$i;
                }
                $result->close();
            }
        }
    }
