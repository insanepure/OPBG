<?php
    class treasurehuntprogress
    {
        private $data;

        function __construct($data)
        {
            $this->data = $data;
        }

        public function GetID()
        {
            return $this->data['id'];
        }

        public function GetPlayer()
        {
            return $this->data['playerid'];
        }

        public function GetTreasurehuntid()
        {
            return $this->data['treasurehuntid'];
        }

        public function SetTreasurehuntid($value)
        {
            $this->data['treasurehuntid'] = $value;
        }

        public function GetIsland1()
        {
            return $this->data['island1'];
        }

        public function GetIsland2()
        {
            return $this->data['island2'];
        }

        public function GetDolphin1()
        {
            return $this->data['dolphin1'] == 1;
        }

        public function GetDolphin2()
        {
            return $this->data['dolphin2'] == 1;
        }

        public function GetDolphin3()
        {
            return $this->data['dolphin3'] == 1;
        }

        public function GetNPC1()
        {
            return $this->data['npc1'];
        }

        public function GetNPC2()
        {
            return $this->data['npc2'];
        }

        public function GetLoot()
        {
            return $this->data['loot'];
        }
    }