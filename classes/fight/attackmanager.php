<?php
if ($database == NULL)
{
  print 'This File (' . __FILE__ . ') should be after Database!';
}

include_once 'attack.php';

class AttackManager
{

  private $database;
  private $attacks;

  function __construct($db)
  {
    $this->database = $db;
    $this->attacks = array();
    $this->LoadData();
  }

  private function LoadData() : void
  {
    $result = $this->database->Select('*', 'attacks', '', 99999);
    if ($result)
    {
      if ($result->num_rows > 0)
      {
        while ($row = $result->fetch_assoc())
        {
          $attack = new Attack($row);
          $this->attacks[] = $attack;
        }
      }
      $result->close();
    }
  }

  public function GetAttack($id) : ?Attack
  {
    $i = 0;
    while (isset($this->attacks[$i]))
    {
      if ($this->attacks[$i]->GetID() == $id)
      {
        return $this->attacks[$i];
      }
      ++$i;
    }
    return null;
  }
}
