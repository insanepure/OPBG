<?php

if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class EloArena
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

    public function GetFighterCount(): int
    {
        return count($this->fighter);
    }

    public function IsFighterIn($id): bool
    {
        return in_array($id, $this->fighter);
    }

    public function UpdateFight($player1, $player2): void
    {
        $this->database->Update('infight=1', 'eloarena', 'fighter="' . $player1 . '" OR fighter="' . $player2 . '"', 2);
    }

    public function GetRandomFighter($id)
    {
        $canFight = false;
        $fighter = -1;
        $result = $this->database->Select('*', 'eloarena', 'infight = 0 AND fighter != ' . $id);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $i = 0;
            while($canFight == false && $i < $result->num_rows) {
                $randsleep = rand(500, 2000);
                usleep($randsleep);

                if ($result->num_rows > 1) {
                    $randfighter = rand(0, $result->num_rows - 1);
                    $fighter = $row[$randfighter]['fighter'];
                } else {
                    $fighter = $row['fighter'];
                }
                $target = new Player($this->database, $fighter);
                if($target->GetDailyEloFights() >= 5 ||
                    $target->GetTournament() != 0 ||
                    !$target->IsOnline())
                {
                    $this->Leave($target->GetID());
                    ++$i;
                    continue;
                }
                if($target->GetLP() != $target->GetMaxLP() ||
                    $target->GetKP() != $target->GetMaxKP() ||
                    $target->GetFight() != 0)
                {
                    ++$i;
                    continue;
                }
                $canFight = true;
            }
        }
        if($fighter == -1 || !$canFight)
            return -1;
        else
            return $fighter;
    }

    public function Join($id): void
    {
        $this->database->Insert('fighter', $id, 'eloarena');
        $this->fighter[count($this->fighter)] = $id;
    }

    public function Leave($id): void
    {
        $this->database->Delete('eloarena', 'fighter = "' . $id . '"', 1);

        $key = array_search($id, $this->fighter);
        array_splice($this->fighter, $key, 1);
    }

    private function LoadData(): void
    {
        $i = 0;
        $result = $this->database->Select('*', 'eloarena', '', 99999);
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

    public function ReloadData(): void
    {
        $i = 0;
        $result = $this->database->Select('*', 'eloarena', '', 99999);
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
