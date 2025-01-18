<?php

if ($database == NULL) {
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class treasurehuntisland
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

    public function GetName()
    {
        return $this->data['name'];
    }

    public function GetImage()
    {
        return $this->data['image'];
    }

    public function GetHeight()
    {
        return $this->data['height'];
    }

    public function GetWidth()
    {
        return $this->data['width'];
    }

    public function GetNPCs()
    {
        if($this->data['npcs'] == '')
        {
            return array();
        }
        return explode(';', $this->data['npcs']);
    }

    public function GetLoot()
    {
        return $this->data['loot'];
    }

    public function GetX()
    {
        return $this->data['x'];
    }

    public function GetY()
    {
        return $this->data['y'];
    }

    private function LoadData($id)
    {
        $result = $this->database->Select('*', 'treasurehuntislands', 'id=' . $id, 1);
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