<?php
    if ($database == NULL)
    {
        print 'This File (' . __FILE__ . ') should be after Database!';
    }

    include_once 'titel.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/npc/npc.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pms/pmmanager.php';

    class TitelManager
    {

        private $database;
        private $titels;
        private $progresses;

        function __construct($db)
        {
            $this->database = $db;
            $this->titels = array();
            $this->progresses = array();
            $this->LoadData();
        }

        private function LoadData()
        {
            $result = $this->database->Select('*', 'titel', '', 99999);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    while ($row = $result->fetch_assoc())
                    {
                        $titel = new Titel($row);
                        array_push($this->titels, $titel);
                    }
                }
                $result->close();
            }
        }

        public function LoadProgress($playerid, $titleid)
        {
            $result = $this->database->Select('id, acc, titel, progress', 'titelprogress', 'acc = ' . $playerid . ' AND titel=' . $titleid . '', 1);
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $row = $result->fetch_assoc();
                    $result->close();
                    return $row;
                }
                $result->close();
            }
            return array();
        }

        public function LoadProgressesForPlayer($playerid)
        {

            $result = $this->database->Select('id, acc, titel, progress', 'titelprogress', 'acc = ' . $playerid, 99999);
            if ($result)
            {
                if($result->num_rows > 0)
                {
                    while($row = $result->fetch_assoc())
                    {
                        array_push($this->progresses, $row);
                    }
                    $result->close();
                }

            }
            return null;
        }

        public function LoadProgressByID($titelid)
        {
            foreach ($this->progresses as $progress) {
                if($progress['titel'] == $titelid)
                    return $progress['progress'];
            }
            return null;
        }

        public function AddTitelStory($player, $story)
        {
            $titels = $player->GetTitels();
            foreach ($this->titels as &$titel)
            {
                if (!$player->HasTitel($titel->GetID()) && $titel->GetType() == 2 && $titel->GetCondition() < $story)
                {
                    $this->AddTitelWithProgress($player, $titels, $titel->GetCondition(), $titel);
                }
            }
        }

        public function AddTitelSideStory($player, $story)
        {
            $titels = $player->GetTitels();
            foreach ($this->titels as &$titel)
            {
                if (!$player->HasTitel($titel->GetID()) && $titel->GetType() == 2 && $titel->GetCondition() < $story)
                {
                    $this->AddTitelWithProgress($player, $titels, $titel->GetCondition(), $titel);
                }
            }
        }

        public function AddTitelAction($player, $progress, $action)
        {
            $titels = $player->GetTitels();
            foreach ($this->titels as &$titel)
            {
                if (!$player->HasTitel($titel->GetID()) && $titel->GetType() == 3 && $titel->GetAction() == $action)
                {
                    $this->AddTitelWithProgress($player, $titels, $progress, $titel);
                }
            }
        }

        public function AddTitelFight($player, $progress, $fight, $sort)
        {
            $titels = $player->GetTitels();
            foreach ($this->titels as &$titel)
            {
                //TitleSort = 3 == Total
                if (!$player->HasTitel($titel->GetID()) && $titel->GetType() == 6 && ($titel->GetFight() == $fight || $titel->GetFight() == -1) && ($titel->GetSort() == $sort || $titel->GetSort() == 3))
                {
                    $this->AddTitelWithProgress($player, $titels, $progress, $titel);
                }
            }
        }


        public function AddTitelNPC($player, $progress, $npc, $fight, $sort)
        {
            $titels = $player->GetTitels();
            foreach ($this->titels as &$titel)
            {
                //TitleSort = 3 == Total
                if (!$player->HasTitel($titel->GetID()) && $titel->GetType() == 1 && $titel->GetNPC() == $npc && ($titel->GetFight() == $fight || $titel->GetFight() == -1) && ($titel->GetSort() == $sort || $titel->GetSort() == 3))
                {
                    $this->AddTitelWithProgress($player, $titels, $progress, $titel);
                }
            }
        }

        public function AddTitelProgressSpecial($player, &$playertitels, $progress, $titel)
        {
            if($player->HasTitel($titel->GetID()))
                return;

            if ($titel->GetCondition() != $progress)
            {
                $titleprogress = $this->LoadProgress($player->GetID(), $titel->GetID());
                if (empty($titleprogress))
                {
                    $this->database->Insert('acc, titel, progress', '"' . $player->GetID() . '","' . $titel->GetID() . '","' . $progress . '"', 'titelprogress');

                    $titleprogress['id'] = $this->database->GetLastID();
                }

                if ($progress >= $titel->GetCondition() && $titel->GetType() == 0)
                {
                    $progress = $titel->GetCondition();
                    $this->database->Delete('titelprogress', 'id = ' . $titleprogress['id'], 1);
                }
                else
                {
                    $this->database->Update('progress = "' . $progress . '"', 'titelprogress', 'id=' . $titleprogress['id'] . '', 1);
                }
            }

            if ($titel->GetCondition() == $progress)
                $this->AddTitel($player, $playertitels, $titel);
        }

        private function AddTitelWithProgress($player, &$playertitels, $progress, $titel)
        {
            if (in_array($titel->GetID(), $playertitels))
                return;

            if ($titel->GetCondition() != $progress)
            {
                $progress = $this->AddProgressForPlayer($player->GetID(), $progress, $titel);
            }

            if ($titel->GetCondition() == $progress)
                $this->AddTitel($player, $playertitels, $titel);
        }

        private function AddProgressForPlayer($playerid, $progress, $titel)
        {
            $titleprogress = $this->LoadProgress($playerid, $titel->GetID());
            if (empty($titleprogress))
            {
                $this->database->Insert('acc, titel, progress', '"' . $playerid . '","' . $titel->GetID() . '","' . $progress . '"', 'titelprogress');

                $titleprogress['id'] = $this->database->GetLastID();
                $titleprogress['acc'] = $playerid;
                $titleprogress['titel'] = $titel->GetID();
                $titleprogress['progress'] = $progress;
            }
            else
            {
                $titleprogress['progress'] += $progress;
            }

            if ($titleprogress['progress'] >= $titel->GetCondition())
            {
                $titleprogress['progress'] = $titel->GetCondition();
                $this->database->Delete('titelprogress', 'id = ' . $titleprogress['id'] . '', 1);
            }
            else
            {
                $this->database->Update('progress = "' . $titleprogress['progress'] . '"', 'titelprogress', 'id=' . $titleprogress['id'] . '', 1);
            }

            return $titleprogress['progress'];
        }

        private function AddTitel($player, &$playertitels, $titel)
        {
            $this->database->Delete('titelprogress', 'acc = ' . $player->GetID() . ' AND title=' . $titel->GetID() . '', 1);

            array_push($playertitels, $titel->GetID());
            $playerTitelStr = implode(';', $playertitels);

            //$titelStats = explode(';', $player->GetTitelStats());
            $titelStats = array(0, 0, 0, 0);
            foreach ($playertitels as &$title)
            {
                $titelStats[0] += $this->GetTitel($title)->GetLP();
                $titelStats[1] += $this->GetTitel($title)->GetKP();
                $titelStats[2] += $this->GetTitel($title)->GetAtk();
                $titelStats[3] += $this->GetTitel($title)->GetDef();
            }
            $TitelStatsStr = implode(';', $titelStats);

            $npc = new NPC($this->database, $titel->GetNPC());
            $lp = $titel->GetLP();
            $kp = $titel->GetKP();
            $att = $titel->GetAtk();
            $def = $titel->GetDef();
            $berry = $titel->GetBerry();
            $gold = $titel->GetGold();
            $berryu = $player->GetBerry() + $titel->GetBerry();
            $goldu = $player->GetGold() + $titel->GetGold();
            $text = "<center><img src='" . $titel->GetTitelPic() . "' alt='' /><br />
		<br />
		Herzlichen Glückwunsch,<br />
		du hast NPC <strong>" . $npc->GetName() . "</strong> besiegt, für diese herausragende Leistung möchten wir dich belohnen!<br />
		Hiermit erhältst du einen Titel, welcher den Namen <strong>" . $titel->GetName() . "</strong> trägt.</center>";

            if ($lp > 0 || $kp > 0 || $att > 0 || $def > 0)
            {
                $text .= '<center><br />
			Dies ist ein besonderer Titel, dieser bringt dir zusätzliche Statspunkte ein, nun gehst du stärker in den nächsten Kampf.<br />
			<br />
			Folgende Werte wurden gestärkt:</center>';
            }
            if ($lp > 0)
            {
                $text .= '<center>
			LP: ' . number_format($lp, '0', '', '.') . '</center>';
            }
            if ($kp > 0)
            {
                $text .= '<center>
			AD: ' . number_format($kp, '0', '', '.') . '</center>';
            }
            if ($att > 0)
            {
                $text .= '<center>
			Angriff: ' . number_format($att, '0', '', '.') . '</center>';
            }
            if ($def > 0)
            {
                $text .= '<center>
			Abwehr: ' . number_format($def, '0', '', '.') . '</center>';
            }
            if($berry > 0 || $gold > 0)
            {
                $text .= '<center><br /> Das erreichen dieses Titels verschafft dir zu neuem Reichtum, du erhältst:</center>';
                if ($berry > 0)
                {
                    $text .= '<center>
        Berry: ' . number_format($berry, '0', '', '.') . '</center>';
                }
                if ($gold > 0)
                {
                    $text .= '<center>
        Gold: ' . number_format($gold, '0', '', '.') . '</center>';
                }

            }
            if ($titel->GetItems() != 0)
            {
                $itemManager = new ItemManager($this->database);
                $items = explode(';', $titel->GetItems());
                $text .= '<center><b>Items: </b>';
                foreach ($items as $item) {
                    $item = explode('@', $item);
                    $itemData = $itemManager->GetItem($item[0]);
                    $text .= number_format($item[1], 0, '', '.') . 'x ' . $itemData->GetName() . '<br/>';
                    $player->AddItems($itemData, $itemData, $item[1]);
                }
                $text .='</center>';
            }
            $PMManager = new PMManager($this->database, $player->GetID());
            $PMManager->SendPM(0, "img/system.png", "SYSTEM", "Glückwunsch", $text, $player->GetName(), 1);

            $set = 'titels = "' . $playerTitelStr . '", titelstats="' . $TitelStatsStr . '", zeni="'.$berryu.'", gold="'.$goldu.'"';
            $player->SetTitels($playerTitelStr);
            $this->database->Update($set, 'accounts', 'id = ' . $player->GetID() . '', 1);
        }

        public function GetTitels()
        {
            return $this->titels;
        }

        public function GetTitel($id) : ?Titel
        {
            $i = 0;
            while (isset($this->titels[$i]))
            {
                if ($this->titels[$i]->GetID() == $id)
                {
                    return $this->titels[$i];
                }
                ++$i;
            }
            return null;
        }

        public function GetTitelsOfNPC($id, $typeFight)
        {
            $npcTitels = array();
            $i = 0;
            while (isset($this->titels[$i]))
            {
                $titel = $this->titels[$i];
                if ($titel->GetType() == 1 && $titel->GetNPC() == $id && ($titel->GetFight() == -1 || $titel->GetFight() == $typeFight)) {
                    array_push($npcTitels, $titel);
                }
                ++$i;
            }
            return $npcTitels;
        }
    }
