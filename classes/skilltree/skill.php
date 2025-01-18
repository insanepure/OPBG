<?php
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class Skill
{

    private $database;
    private $data;

    function __construct($db, $id)
    {
        $id = $db->EscapeString($id);
        $this->database = $db;
        $this->LoadData($id);
    }

    private function LoadData($id)
    {
        $result = $this->database->Select('*', 'skilltree', 'id=' . $id . '', 1);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                while ($row = $result->fetch_assoc())
                {
                    $this->data = $row;
                }
            }
            $result->close();
        }
    }

    public function HasEnoughPoints($points)
    {
        return $this->data['neededpoints'] <= $points;
    }

    public function GetType()
    {
        return $this->data['type'];
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function GetAttack()
    {
        return $this->data['attack'];
    }

    public function GetLevel()
    {
        return $this->data['level'];
    }

    public function GetRace()
    {
        return $this->data['race'];
    }

    public function GetNeedAttacks()
    {
        return explode(';', $this->data['needattacks']);
    }

    public function GetNeedItem()
    {
        return $this->data['skillneeditem'];
    }

    public function GetNeededItemAmount()
    {
        return $this->data['amount'];
    }

    public function GetNeededPoints()
    {
        return $this->data['neededpoints'];
    }

    public function IsLearnable()
    {
        return $this->data['learnable'];
    }

    public function GetBerry()
    {
        return $this->data['berry'];
    }
}
