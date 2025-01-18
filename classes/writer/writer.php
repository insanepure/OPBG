<?php
if ($database == NULL)
{
  print 'This File (' . __FILE__ . ') should be after Database!';
}

class Writer
{
      private $data;
      private $database;

    function __construct($db, $data)
    {
        $this->database = $db;
        $this->data = $data;
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function GetStoryPunkt()
    {
        return $this->data['storypunkt'];
    }

    public function GetStorypoint()
    {
        return $this->data['storypunkt'];
    }

    public function GetTitel()
    {
        return $this->data['titel'];
    }

    public function GetMode()
    {
        return $this->data['mode'];
    }

    public function GetText()
    {
        return $this->data['text'];
    }

    public function GetImage()
    {
        return $this->data['bild'];
    }

    public function Delete($id)
    {
        $this->database->Delete('writer', 'id="'.$id.'"', 1);
    }
}