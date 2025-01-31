<?php
    if ($database == NULL)
    {
        print 'This File (' . __FILE__ . ') should be after Database!';
    }

    class Titel
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

        public function GetName()
        {
            return $this->data['name'];
        }

        public function GetDescription()
        {
            return $this->data['description'];
        }

        public function GetSortierung()
        {
            return $this->data['sortierung'];
        }

        public function GetColor()
        {
            return $this->data['color'];
        }

        public function GetType()
        {
            return $this->data['type'];
        }

        public function GetCondition()
        {
            return $this->data['typecondition'];
        }

        public function GetNPC()
        {
            return $this->data['typenpc'];
        }

        public function GetTitelPic()
        {
            return $this->data['titelpic'];
        }

        public function GetFight()
        {
            return $this->data['typefight'];
        }

        public function GetAction()
        {
            return $this->data['typeaction'];
        }

        public function GetItems()
        {
            return $this->data['items'];
        }

        public function GetSort()
        {
            return $this->data['typesort'];
        }

        public function GetLP()
        {
            return $this->data['lp'];
        }

        public function GetKP()
        {
            return $this->data['kp'];
        }

        public function GetAtk()
        {
            return $this->data['atk'];
        }

        public function GetDef()
        {
            return $this->data['def'];
        }
        public function GetBerry()
        {
            return $this->data['berry'];
        }
        public function GetGold()
        {
            return $this->data['gold'];
        }
        public function GetAttack()
        {
            return $this->data['typeattack'];
        }
        public function IsVisible()
        {
            return $this->data['visible'] == 1;
        }

    }
