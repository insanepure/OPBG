<?php
    if ($database == NULL)
    {
        print 'This File (' . __FILE__ . ') should be after Database!';
    }

    include_once 'eventfight.php';

    class Event
    {
        private $database;
        private $data;
        private $fights;

        function __construct($db, $id)
        {
            $this->database = $db;
            $this->fights = array();
            $this->LoadData($id);
        }

        static function GetPlayerWins($finishedplayers, $pID) : int
        {
            $wins = 0;

            $playersData = explode(';', $finishedplayers);
            $i = 0;
            while (isset($playersData[$i]))
            {
                $playerData = explode('@', $playersData[$i]);
                if ($playerData[0] == $pID)
                {
                    $wins = $playerData[1];
                    break;
                }
                ++$i;
            }

            return $wins;
        }

        static function IsPlaceAndPlanet($planet, $place, $placeandtime) : bool
        {
            $pandts = explode('@', $placeandtime);
            $i = 0;
            while (isset($pandts[$i]))
            {
                $pandt = explode(';', $pandts[$i]);
                $pPlanet = $pandt[0]; // Planet
                $pPlace = $pandt[1]; // Place
                if ($planet != $pPlanet || $place != $pPlace)
                {
                    return false;
                }
                ++$i;
            }

            return true;
        }

        static function IsPlanetEvent($planet, $placeandtime) : bool
        {
            $pandts = explode('@', $placeandtime);
            $i = 0;
            while (isset($pandts[$i]))
            {
                $pandt = explode(';', $pandts[$i]);
                $pPlanet = $pandt[0]; // Planet
                if ($planet != $pPlanet)
                {
                    return false;
                }
                ++$i;
            }

            return true;
        }

        static function IsToday($planet, $place, $placeandtime) : bool
        {
            //Erde;Wald;1-5;1-31;1-12;0-365;2018-3000@Erde;Canyon;1:2;1:2:3:4;12;0-365;2018
            $pandts = explode('@', $placeandtime);
            $i = 0;
            $weekDay = date('N');
            $monthDay = date('j');
            $yearDay = date('z') + 1;
            $month = date('n');
            $year = date('Y');

            while (isset($pandts[$i]))
            {
                $isToday = true;
                $pandt = explode(';', $pandts[$i]);
                $pPlanet = $pandt[0]; // Planet
                $pPlace = $pandt[1]; // Place
                if ($planet != $pPlanet || $place != $pPlace)
                {
                    $isToday = false;
                }
                //: seperation is single Day, so 1:3 would be first day and third day
                //- seperation is between days, so 1-5 are all days between 1 and 5.

                $sWeekDays = explode(':', $pandt[2]); // Week day, 1-7. 1 is Monday, 7 is Sunday
                $bWeekDays = explode('-', $pandt[2]); // Week day, 1-7. 1 is Monday, 7 is Sunday
                if (count($bWeekDays) > 1 && ($weekDay < $bWeekDays[0] || $weekDay > $bWeekDays[1]))
                {
                    $isToday = false;
                }
                else if (count($bWeekDays) <= 1 && !in_array($weekDay, $sWeekDays))
                {
                    $isToday = false;
                }

                $sMonthDays = explode(':', $pandt[3]); // Month day, 1-31
                $bMonthDays = explode('-', $pandt[3]); // Month day, 1-31
                if (count($bMonthDays) > 1 && ($monthDay < $bMonthDays[0] || $monthDay > $bMonthDays[1]))
                {
                    $isToday = false;
                }
                else if (count($bMonthDays) <= 1 && !in_array($monthDay, $sMonthDays))
                {
                    $isToday = false;
                }

                $sMonths = explode(':', $pandt[4]); // Months, 1-12
                $bMonths = explode('-', $pandt[4]); // Months, 1-12
                if (count($bMonths) > 1 && ($month < $bMonths[0] || $month > $bMonths[1]))
                {
                    $isToday = false;
                }
                else if (count($bMonths) <= 1 && !in_array($month, $sMonths))
                {
                    $isToday = false;
                }

                $sYears = explode(':', $pandt[5]); // Years, 2017-3000
                $bYears = explode('-', $pandt[5]); // Years, 2017-3000
                if (count($bYears) > 1 && $year < $bYears[0] && $year > $bYears[1])
                {
                    $isToday = false;
                }
                else if (count($bYears) <= 1 && !in_array($year, $sYears))
                {
                    $isToday = false;
                }

                if ($isToday)
                {
                    return true;
                }
                ++$i;
            }

            return false;
        }

        static function IsTodayEvent($planet, $placeandtime, $begin = NIL, $end = NIL) : bool
        {
            //Erde;Wald;1-5;1-31;1-12;0-365;2018-3000@Erde;Canyon;1:2;1:2:3:4;12;0-365;2018
            $pandts = explode('@', $placeandtime);
            $i = 0;
            $weekDay = date('N');
            $monthDay = date('j');
            $yearDay = date('z') + 1;
            $month = date('n');
            $year = date('Y');
            $time = date('H:i:s');

            while (isset($pandts[$i]))
            {
                $isToday = true;
                $pandt = explode(';', $pandts[$i]);
                $pPlanet = $pandt[0]; // Planet
                if ($planet != $pPlanet)
                {
                    $isToday = false;
                }
                //: seperation is single Day, so 1:3 would be first day and third day
                //- seperation is between days, so 1-5 are all days between 1 and 5.

                $sWeekDays = explode(':', $pandt[2]); // Week day, 1-7. 1 is Monday, 7 is Sunday
                $bWeekDays = explode('-', $pandt[2]); // Week day, 1-7. 1 is Monday, 7 is Sunday
                if (count($bWeekDays) > 1 && ($weekDay < $bWeekDays[0] || $weekDay > $bWeekDays[1]))
                {
                    $isToday = false;
                }
                else if (count($bWeekDays) <= 1 && !in_array($weekDay, $sWeekDays))
                {
                    $isToday = false;
                }

                $sMonthDays = explode(':', $pandt[3]); // Month day, 1-31
                $bMonthDays = explode('-', $pandt[3]); // Month day, 1-31
                if (count($bMonthDays) > 1 && ($monthDay < $bMonthDays[0] || $monthDay > $bMonthDays[1]))
                {
                    $isToday = false;
                }
                else if (count($bMonthDays) <= 1 && !in_array($monthDay, $sMonthDays))
                {
                    $isToday = false;
                }

                $sMonths = explode(':', $pandt[4]); // Months, 1-12
                $bMonths = explode('-', $pandt[4]); // Months, 1-12
                if (count($bMonths) > 1 && ($month < $bMonths[0] || $month > $bMonths[1]))
                {
                    $isToday = false;
                }
                else if (count($bMonths) <= 1 && !in_array($month, $sMonths))
                {
                    $isToday = false;
                }

                $sYears = explode(':', $pandt[5]); // Years, 2017-3000
                $bYears = explode('-', $pandt[5]); // Years, 2017-3000
                if (count($bYears) > 1 && $year < $bYears[0] && $year > $bYears[1])
                {
                    $isToday = false;
                }
                else if (count($bYears) <= 1 && !in_array($year, $sYears))
                {
                    $isToday = false;
                }

                if ((isset($begin) && isset($end)) && ($time < date('H:i:s', strtotime($begin. ':00')) || $time > date('H:i:s', strtotime($end. ':00'))))
                {
                    $isToday = false;
                }

                if ($isToday)
                {
                    return true;
                }
                ++$i;
            }

            return false;
        }

        public function GetID()
        {
            return $this->data['id'];
        }

        public function GetStats()
        {
            return $this->data['stats'];
        }

        public function GetDailyreset()
        {
            return $this->data['dailyreset'];
        }

        public function GetName()
        {
            return $this->data['name'];
        }

        public function GetLevel()
        {
            return $this->data['level'];
        }

        public function GetImage()
        {
            return $this->data['image'];
        }

        public function GetPlaceAndTime()
        {
            return $this->data['placeandtime'];
        }

        public function GetMinPlayers()
        {
            return $this->data['minplayers'];
        }

        public function GetMaxPlayers()
        {
            return $this->data['maxplayers'];
        }

        public function GetDecreaseNPCFight()
        {
            return $this->data['decreasenpcfight'];
        }

        public function GetBerry()
        {
            return $this->data['zeni'];
        }

        public function GetItem()
        {
            return $this->data['item'];
        }

        public function GetBegin()
        {
            return $this->data['begin'];
        }

        public function GetEnd()
        {
            return $this->data['end'];
        }

        public function IsDungeon()
        {
            return $this->data['isdungeon'];
        }

        public function GetFights()
        {
            return $this->data['fights'];
        }

        public function GetDropChance()
        {
            $day = date("w");
            $chance = $this->data['dropchance'];
            if($day == 2 && $this->GetID() == 1)
            {
                $chance = 100;
            }
            else if($day == 6 && $this->IsDungeon())
            {
                $chance = 100;
            }
            else
            {
                $chance = $this->data['dropchance'];
            }
            return $chance;
        }

        public function GetWinable()
        {
            return $this->data['winable'];
        }

        public function GetDifficulty()
        {
            return $this->data['difficulty'];
        }

        public function GetMünzen()
        {
            return $this->data['münzen'];
        }

        public function GetFinishedTimes($id) : int
        {
            $players = $this->GetFinishedPlayers();
            $i = 0;
            while (isset($players[$i]))
            {
                if ($players[$i][0] == $id)
                {
                    return $players[$i][1];
                }
                ++$i;
            }
            return 0;
        }

        public function GetPlayers() : array
        {
            return explode(";", $this->data['finishedplayers']);
        }

        public function GetFinishedPlayers() : array
        {
            $players = array();
            $playersData = explode(';', $this->data['finishedplayers']);
            $i = 0;
            while (isset($playersData[$i]))
            {
                $playerData = explode('@', $playersData[$i]);
                $players[] = $playerData;
                ++$i;
            }
            return $players;
        }

        public function AddFinishedPlayers(int $id) : void
        {
            $players = $this->GetFinishedPlayers();
            $i = 0;
            $found = false;
            while (isset($players[$i]))
            {
                if ($players[$i][0] == $id)
                {
                    $players[$i][1] = $players[$i][1] + 1;
                    $found = true;
                    break;
                }
                ++$i;
            }

            if (!$found)
            {
                $player = array();
                $player[0] = $id;
                $player[1] = 1;
                $players[] = $player;
            }

            $i = 0;
            $string = '';
            while (isset($players[$i]))
            {
                if ($string == '')
                {
                    $string = implode('@', $players[$i]);
                }
                else
                {
                    $string = $string . ';' . implode('@', $players[$i]);
                }
                ++$i;
            }
            $this->data['finishedplayers'] = $string;
            $this->database->Update('`finishedplayers`="' . $string . '"', 'events', 'id=' . $this->GetID() . '', 1);
        }

        public function RemoveFinishedPlayers($id)
        {
            $players = $this->GetFinishedPlayers();
            $i = 0;
            $found = false;
            while (isset($players[$i]))
            {
                if ($players[$i][0] == $id)
                {
                    $players[$i][1] = $players[$i][1] - 1;
                    if ($players[$i][1] == 0)
                        array_splice($players, $i, 1);
                    $found = true;
                    break;
                }
                ++$i;
            }

            if (!$found)
                return;

            $i = 0;
            $string = '';
            while (isset($players[$i]))
            {
                if ($string == '')
                {
                    $string = implode('@', $players[$i]);
                }
                else
                {
                    $string = $string . ';' . implode('@', $players[$i]);
                }
                ++$i;
            }
            $this->data['finishedplayers'] = $string;
            $this->database->Update('`finishedplayers`="' . $string . '"', 'events', 'id=' . $this->GetID() . '', 1);
        }

        public function GetFight($id) : ?EventFight
        {
            if (count($this->fights) <= $id)
            {
                return null;
            }
            return $this->fights[$id];
        }

        public function IsOnPlanetandPlace($group, $IsPlanet)
        {
            $IsPlanet = false;
            $i = 0;
            while(isset($group[$i]))
            {
                $GroupPlayerCheck = $this->database->Select('*', 'accounts', 'id="'.$group[$i].'"');
                if($GroupPlayerCheck)
                {
                    $GrouPlayer = $GroupPlayerCheck->fetch_assoc();
                    $GP = new Player($this->database, $GrouPlayer['id']);
                    if($this->IsDungeon() == 1)
                    {
                        if($this->IsPlaceAndPlanet($GP->GetPlanet(), $GP->GetPlace(), $this->GetPlaceAndTime()) == true)
                        {
                            $IsPlanet = true;
                        }
                    }
                }

                $i++;
            }
            return $IsPlanet;
        }

        public function Invite($fightID, $group, $player) : void
        {
            $where = '`group` = "' . implode(';', $group) . '" AND fight=0';
            $limit = count($group);
            $this->database->Update('`eventinvite`="' . $fightID . '"', 'accounts', $where, $limit);
        }

        public function GetValidPlayers($player, $group, $level = 0, $eventp) : int
        {
            $where = '';

            if ($group == null)
            {
                $where = 'id = ' . $player->GetID() . '';
            }
            else
            {
                $i = 0;
                while (isset($group[$i]))
                {
                    if ($where == '')
                    {
                        $where = '(id = ' . $group[$i] . '';
                    }
                    else
                    {
                        $where = $where . ' OR id = ' . $group[$i] . '';
                    }
                    ++$i;
                }
                $where = $where . ')';
            }

            if($this->IsDungeon() == 1)
            {
                $where = $where . ' AND lp > mlp*0.2 AND fight = 0 AND planet="'.$eventp.'" AND level >= ' . $level . ' AND tournament = 0';
            }
            else
            {
                $where = $where . ' AND lp > mlp*0.2 AND fight = 0 AND level >= ' . $level . ' AND tournament = 0';
            }
            $validPlayers = 0;
            $result = $this->database->Select('COUNT(id) as total', 'accounts', $where);
            if ($result)
            {
                $row = $result->fetch_assoc();
                $validPlayers = $row['total'];
                $result->close();
            }

            return $validPlayers;
        }

        public function GetPlanet()
        {
            $planets = explode(';', $this->data['placeandtime']);
            $planet = $planets[0];
            return $planet;

        }

        public function GetPlace()
        {
            $places = explode(';', $this->data['placeandtime']);
            $place = $places[1];
            return $place;
        }

        private function LoadData($id) : void
        {
            $result = $this->database->Select('*', 'events', 'id=' . $id . '', 1);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $row = $result->fetch_assoc();
                    $this->data = $row;
                }
                $result->close();
            }


            $fights = explode('@', $this->data['fights']);
            $i = 0;
            while (isset($fights[$i]))
            {
                $fight = new EventFight($fights[$i]);
                $this->fights[] = $fight;
                ++$i;
            }
        }
    }
