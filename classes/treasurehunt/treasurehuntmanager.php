<?php

    if ($database == NULL) {
        print 'This File (' . __FILE__ . ') should be after Database!';
    }

    class treasurehuntmanager
    {
        private $database;

        function __construct($db)
        {
            $this->database = $db;
        }

        public function LevelUp($id)
        {
            $treasurehuntprogress = $this->LoadPlayerData($id);
            $treasurehuntprogress->SetTreasurehuntid($treasurehuntprogress->GetTreasurehuntid() + 1);
            $treasurehunt = new treasurehunt($this->database, $treasurehuntprogress->GetTreasurehuntid());

            $islands = array($treasurehunt->GetIsland1(),$treasurehunt->GetIsland2(),$treasurehunt->GetIsland3());
            array_splice($islands, rand(0,2), 1);
            if($treasurehunt->GetIsland1() != 0 && $treasurehunt->GetIsland2() != 0 && $treasurehunt->GetIsland3() != 0)
            {
                $island1 = new treasurehuntisland($this->database, $islands[0]);
                $island2 = new treasurehuntisland($this->database, $islands[1]);
                $npcs1 = $island1->GetNPCs();
                $npcs2 = $island2->GetNPCs();
                $loot = array($island1->GetLoot(), $island2->GetLoot());
                $island1id = $island1->GetID();
                $island2id = $island2->GetID();
            }
            else
            {
                $npcs1 = array(0,0,0);
                $npcs2 = array(0,0,0);
                $loot = array('', '');
                $island1id = 0;
                $island2id = 0;
            }
            $dolphin1 = rand(1,300);
            $dolphin2 = rand(1,300);
            $dolphin3 = rand(1,300);

            if($dolphin1 > 2 || $treasurehunt->HasDolphins() == 0)
                $dolphin1 = 0;
            if($dolphin2 > 2 || $treasurehunt->HasDolphins() == 0)
                $dolphin2 = 0;
            if($dolphin3 > 2 || $treasurehunt->HasDolphins() == 0)
                $dolphin3 = 0;
            $this->database->Update('treasurehuntid='.$treasurehuntprogress->GetTreasurehuntid().', island1='.$island1id.', island2='.$island2id.', dolphin1='.$dolphin1.', dolphin2='.$dolphin2.', dolphin3='.$dolphin3.', npc1='.$npcs1[rand(0,2)].', npc2='.$npcs2[rand(0,2)].', loot="'.$loot[rand(0,1)].'"', 'treasurehuntprogress', 'playerid='.$id);
        }

        public function Reset($id)
        {
            $this->database->Delete('treasurehuntprogress', 'playerid='.$id, 1);
            $this->CreateNewPlayerData($id);
        }

        public function CreateNewPlayerData($id)
        {

            $treasurehunt = new treasurehunt($this->database, 1);

            $islands = array($treasurehunt->GetIsland1(),$treasurehunt->GetIsland2(),$treasurehunt->GetIsland3());
            array_splice($islands, rand(0,2), 1);
            $island1 = new treasurehuntisland($this->database, $islands[0]);
            $island2 = new treasurehuntisland($this->database, $islands[1]);
            $npcs1 = $island1->GetNPCs();
            $npcs2 = $island2->GetNPCs();
            $loot = array($island1->GetLoot(), $island2->GetLoot());
            $dolphin1 = rand(1,300);
            $dolphin2 = rand(1,300);
            $dolphin3 = rand(1,300);

            if($dolphin1 > 1 || $treasurehunt->HasDolphins() == 0)
                $dolphin1 = 0;
            if($dolphin2 > 1 || $treasurehunt->HasDolphins() == 0)
                $dolphin2 = 0;
            if($dolphin3 > 1 || $treasurehunt->HasDolphins() == 0)
                $dolphin3 = 0;
            $this->database->Insert(
                '`playerid`, `treasurehuntid`, `island1`, `island2`, `dolphin1`, `dolphin2`, `dolphin3`, `npc1`, `npc2`, `loot`',
                '"' . $id . '","' . 1 . '","' . $island1->GetID() . '","' . $island2->GetID() . '","' . $dolphin1 . '","' . $dolphin2 . '","' . $dolphin3 . '","' . $npcs1[rand(0,2)] . '","' . $npcs2[rand(0,2)] . '", "'.$loot[rand(0,1)].'"',
                'treasurehuntprogress'
            );

            $result = $this->database->Select('*', 'treasurehuntprogress', 'playerid='.$id);
            if ($result)
            {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    return new treasurehuntprogress($row);
                }
            }
        }

        public function LoadPlayerData($id) : ?treasurehuntprogress
        {
            $result = $this->database->Select('*', 'treasurehuntprogress', 'playerid='.$id);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $row = $result->fetch_assoc();
                    return new treasurehuntprogress($row);
                }
                else
                {
                     return $this->CreateNewPlayerData($id);
                }
            }
        }

        public function GetTreasurehuntCount()
        {
            $result = $this->database->Select('COUNT(id) as total', 'treasurehunt', '');
            if ($result)
            {
                $row = $result->fetch_assoc();
                return $row['total'];
            }
        }
    }