<?php
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class Planet
{
    private $database;
    private $data;
    private $valid;

    function __construct($db, $id)
    {
        $this->database = $db;
        $this->valid = false;
        $this->LoadData($id);
    }

    public function IsValid()
    {
        return $this->valid;
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function GetName()
    {
        return $this->data['name'];
    }

    public function GetMap()
    {
        return $this->data['map'];
    }

    public function IsTravelable()
    {
        return $this->data['travelable'];
    }
    public function GetPlanetLevel()
    {
        return $this->data['planetenlevel'];
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

    public function GetStartingPlace()
    {
        return $this->data['startingplace'];
    }

    public function GetImage()
    {
        return $this->data['image'];
    }

    public function GetMinStory()
    {
        return $this->data['minstory'];
    }

    public function GetMaxStory()
    {
        return $this->data['maxstory'];
    }

    public function GetMinSideStory()
    {
        return $this->data['minsidestory'];
    }

    public function GetMaxSideStory()
    {
        return $this->data['maxsidestory'];
    }

    public function CanSee($playerStory)
    {
        return ($this->data['minstory'] == 0 || $playerStory >= $this->data['minstory']) && ($this->data['maxstory'] == 0 || $playerStory <= $this->data['maxstory']);
    }

    public function CanSeeSide($playerStory)
    {
        return ($this->data['minsidestory'] == 0 || $playerStory >= $this->data['minsidestory']) && ($this->data['maxsidestory'] == 0 || $playerStory <= $this->data['maxsidestory']);
    }

    public function IsVisible()
    {
        return $this->data['visible'];
    }

    private function LoadData($id)
    {
        $id = $this->database->EscapeString($id);
        $result = $this->database->Select('*', 'planet', 'id=' . $id, 1);
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
