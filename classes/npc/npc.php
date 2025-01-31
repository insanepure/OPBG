<?php
if ($database == NULL)
{
  print 'This File (' . __FILE__ . ') should be after Database!';
}

class NPC
{
  private $database;
  private $data;
  private $valid;
  private $difficulty;

  function __construct($db, $id, $difficulty = 1)
  {
    $this->database = $db;
    $this->valid = false;
    $this->difficulty = $difficulty;
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

  public function SetID($value)
  {
    $this->data['id'] = $value;
  }

  public function GetName()
  {
    return $this->data['name'];
  }

  public function SetName($value)
  {
    $this->data['name'] = $value;
  }

  public function GetImageName()
  {
    return $this->data['image'];
  }

  public function SetImageName($value)
  {
    $this->data['image'] = $value;
  }

  public function GetBerry()
  {
    return $this->data['zeni'];
  }

  public function GetGold()
  {
    return $this->data['gold'];
  }

  public function GetMünzen()
  {
      return $this->data['münzen'];
  }

  public function GetPatterns()
  {
    return $this->data['patterns'];
  }

  public function GetRace()
  {
    return $this->data['race'];
  }

  public function GetLevel()
  {
    return $this->data['level'];
  }

  public function SetRace($value)
  {
    $this->data['race'] = $value;
  }

  public function GetDescription()
  {
    return $this->data['description'];
  }

  public function GetItems()
  {
    return $this->data['items'];
  }

  public function GetSurvivalRounds()
  {
    return $this->data['survivalrounds'];
  }

  public function GetSurvivalTeam()
  {
    return $this->data['survivalteam'];
  }

  public function GetSurvivalWinner()
  {
    return $this->data['survivalwinner'];
  }

  public function GetHealthRatio()
  {
    return $this->data['healthratio'];
  }

  public function GetHealthRatioTeam()
  {
    return $this->data['healthratioteam'];
  }

  public function GetHealthRatioWinner()
  {
    return $this->data['healthratiowinner'];
  }

  public function GetPlayerAttack()
  {
    if ($this->data['playerattack'] == '')
      return array();

    return explode(';', $this->data['playerattack']);
  }

  public function GetOverrideAttacks()
  {
    return $this->data['overrideattacks'];
  }

  public function GetImage()
  {
    return 'img/npc/' . $this->GetImageName() . '.png';
  }

  public function GetAttacks()
  {
    return $this->data['attacks'];
  }

  public function SetAttacks($value)
  {
    $this->data['attacks'] = $value;
  }

  public function AddFightAttack($technique)
  {
    $attacks = array();
    if ($this->GetAttacks() != '')
      $attacks = explode(';', $this->GetAttacks());

    if (in_array($technique, $attacks))
    {
      return;
    }
    array_push($attacks, $technique);
    $attacks = implode(';', $attacks);
    $this->SetAttacks($attacks);
  }

  private function GetDifficultyIncrease($value, $modifier)
  {
    $difficulty = $this->difficulty - 1;
    if ($difficulty < 0) $difficulty = 0;
    $modifiedValue = $value * $modifier;
    $valueIncrease = round($modifiedValue * $difficulty);
    return $value + $valueIncrease;
  }

  public function GetRawLP()
  {
    return $this->data['lp'];
  }

  public function GetLP()
  {
    return $this->GetDifficultyIncrease($this->data['lp'], 1.0);
  }

  public function GetMaxLP()
  {
    return $this->GetDifficultyIncrease($this->data['mlp'], 1.0);
  }

  public function SetLP($value)
  {
    $this->data['lp'] = $value;
  }

  public function GetIsKolo()
  {
      return $this->data['iskolo'];
  }

  public function SetVivrecard($value)
  {
      $this->database->Update('vivrecard="'.$value.'"', 'npcs', 'id="'.$this->GetID().'"');
  }

  public function GetVivrecard()
  {
      return $this->data['vivrecard'];
  }

  public function SetMaxLP($value)
  {
    $this->data['mlp'] = $value;
  }

  public function GetMaxEnemy()
  {
    return $this->data['maxenemy'];
  }

  public function GetKI()
  {
    $value = ($this->GetLP() / 10) + ($this->GetKP() / 10) + $this->GetAttack() + $this->GetDefense();
    $value = round($value / 4);
    return $value;
  }

  public function GetRawKP()
  {
    return $this->data['kp'];
  }

  public function GetKP()
  {
    return $this->GetDifficultyIncrease($this->data['kp'], 2.0);
  }

  public function GetMaxKP()
  {
    return $this->GetDifficultyIncrease($this->data['mkp'], 2.0);
  }

  public function SetKP($value)
  {
    $this->data['kp'] = $value;
  }

  public function SetMaxKP($value)
  {
    $this->data['mkp'] = $value;
  }

  public function GetAttack()
  {
    return $this->data['attack'];
  }

  public function SetAttack($value)
  {
    $this->data['attack'] = $value;
  }

  public function GetDefense()
  {
    return $this->data['defense'];
  }

  public function SetDefense($value)
  {
    $this->data['defense'] = $value;
  }

    public function GetCritchance()
    {
        return $this->data['critchance'];
    }

    public function SetCritchance($value)
    {
        $this->data['critchance'] = $value;
    }

    public function GetCritdamage()
    {
        return $this->data['critdamage'];
    }

    public function SetCritdamage($value)
    {
        $this->data['critdamage'] = $value;
    }

  public function GetAccuracy()
  {
    return $this->data['accuracy'];
  }

  public function SetAccuracy($value)
  {
    $this->data['accuracy'] = $value;
  }

  public function GetReflex()
  {
    return $this->data['reflex'];
  }
  public function NeedItem()
  {
    return $this->data['needitems'];
  }

  public function NeedTrainerItem()
  {
    return $this->data['trainerneeditem'];
  }

  public function NeedTrainerItemAmount()
  {
    return $this->data['traineritemamount'];
  }

  public function SetReflex($value)
  {
    $this->data['reflex'] = $value;
  }

  public function IsStatsProcentual()
  {
    return $this->data['isstatsprocentual'] == 1;
  }

  private function LoadData($id)
  {
    $result = $this->database->Select('*', 'npcs', 'id=' . $id . '', 1);
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
