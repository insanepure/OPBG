<?php

    if ($database == NULL) {
        print 'This File (' . __FILE__ . ') should be after Database!';
    }

    class treasurehunt
    {
        private $database;
        private $data;

        function __construct($db, $id)
        {
            $this->database = $db;
            $this->LoadData($id);
        }

        public function GetID()
        {
            return $this->data['id'];
        }

        public function GetBackgroundImage()
        {
            return $this->data['image'];
        }

        public function SetBackgroundImage($value)
        {
            $this->data['image'] = $value;
        }

        public function GetIsland1()
        {
            return $this->data['island1'];
        }

        public function GetIsland2()
        {
            return $this->data['island2'];
        }

        public function GetIsland3()
        {
            return $this->data['island3'];
        }

        public function HasDolphins()
        {
            return $this->data['dolphins'];
        }

        private function LoadData($id)
        {
            $result = $this->database->Select('*', 'treasurehunt', 'id=' . $id, 1);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $row = $result->fetch_assoc();
                    $this->data = $row;
                }
                $result->close();
            }
        }
    }

