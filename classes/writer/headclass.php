<?php
$datenbank = new PDO('mysql:host=localhost;dbname=lkwstreet', 'root', '');

if ($datenbank == NULL)
{
  print 'This File (' . __FILE__ . ') should be after Database!';
}

Class lkw {

    private $data;

    function __construct($data)
  {
    $this->data = $data;
  }

public function ID()
{
    return $this->data['id'];
}

public function Strassenname()
{
    return $this->data['strassenname'];
}

public function PLZ()
{
    return $this->data['plz'];
}

public function Stadt()
{
    return $this->data['stadt'];
}

public function Ortsteil()
{
    return $this->data['ortsteil'];
}
} 
?>