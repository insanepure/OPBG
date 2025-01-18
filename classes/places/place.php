<?php
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class Place
{
    private $database;
    private $data;
    private $valid;
    private $actionManager;

    function __construct($db, $id, $actionManager)
    {
        $this->database = $db;
        $this->valid = false;
        $this->actionManager = $actionManager;
        $this->data['id'] = $id;
        $this->LoadData($id);
    }

    public function IsValid()
    {
        return $this->valid;
    }

    public function GetAdminPlace()
    {
        return $this->data['adminplace'];
    }

    public function GetTime()
    {
        return $this->data['time'];
    }

    public function IsBlocked()
    {
        return $this->data['blocked'];
    }

    public function SetBlocked($value)
    {
        $this->data['blocked'] = $value;
    }

    public function IsEarnable()
    {
        return $this->data['earnable'] == 1;
    }

    public function GetTerritorium()
    {
        return $this->data['territorium'];
    }

    public function GetBandenBanner()
    {
        $result = $this->database->Select('*', 'clans', 'id="'.$this->GetTerritorium().'"', 1);
        if($result)
        {
            $row = $result->fetch_assoc();
            if($row['banner'] == '')
            {
                return '';
            }
            else
            {
                return '<img src="'.$row['banner'].'" alt="'.$row['name'].'" title="'.$row['name'].'" />';
            }
        }
        return '';
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function GetPlanet()
    {
        return $this->data['planet'];
    }
    public function GetPlaceLevel()
    {
        return $this->data['levelplace'];
    }

    public function GetLearnableAttacks()
    {
        return $this->data['learnableattacks'];
    }

    public function GetName()
    {
        if($this->GetID() == -1) return 'Auf Reise';
        else return $this->data['name'];
    }

    public function IsTravelable()
    {
        return $this->data['travelable'];
    }

    public function GetX()
    {
        return $this->data['x'];
    }

    public function GetY()
    {
        return $this->data['y'];
    }

    public function GetDescription()
    {
        return $this->data['description'];
    }

    public function GetImage()
    {
        return $this->data['image'];
    }

    public function GetItems()
    {
        return explode(';', $this->data['items']);
    }

    public function GetEventItems()
    {
        return explode(';', $this->data['eventitems']);
    }

    public function GetNPCs()
    {
        return explode(';', $this->data['npcs']);
    }

    public function GetTrainers()
    {
        if($this->data['trainers'] != '')
            return explode(';', $this->data['trainers']);
        else
            return array();
    }

    public function GetActions()
    {
        $i = 0;
        $actionData = explode(';', $this->data['actions']);
        $actions = array();
        while (isset($actionData[$i]))
        {
            $action = $this->actionManager->GetAction($actionData[$i]);
            array_push($actions, $action);
            ++$i;
        }
        return $actions;
    }

    public function HasAction($id)
    {
        $i = 0;
        $actionData = explode(';', $this->data['actions']);

        while (isset($actionData[$i]))
        {
            if ($actionData[$i] == $id)
            {
                return true;
            }
            ++$i;
        }
        return false;
    }

    public function GetLastFightInSeconds(): int
    {
        $time = time();
        $lastAttack = strtotime($this->data['lastfight']);
        return ($time - $lastAttack);
    }

    private function LoadData($id)
    {
        $id = $this->database->EscapeString($id);
        $result = $this->database->Select('*', 'places', 'id=' . intval($id), 1);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                $this->data = $row;
                $this->valid = true;
            }
            $result->close();
        }
    }
}
