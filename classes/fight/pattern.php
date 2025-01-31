<?php
if ($database == NULL)
{
  print 'This File (' . __FILE__ . ') should be after Database!';
}

class Pattern
{
  private $data;

  function __construct($data)
  {
    $this->data = $data;
  }

  public function GetID()
  {
    return $this->data['id'];
  }

  public function GetName()
  {
    return $this->data['name'];
  }

  public function GetType()
  {
    return $this->data['type'];
  }

  public function GetValueName()
  {
    return $this->data['valuename'];
  }

  public function GetValue()
  {
    return $this->data['value'];
  }

  public function GetOperator()
  {
    return $this->data['operator'];
  }

  public function IsProcent()
  {
    return $this->data['isprocent'];
  }

  public function GetAttack()
  {
    return $this->data['attack'];
  }

  public function GetPatternTarget()
  {
    return $this->data['patterntarget'];
  }

  public function GetSpecificTarget()
  {
      return $this->data['specifictarget'];
  }

  public function GetFallbackAttack()
  {
        return $this->data['fallbackattack'];
  }

  public function GetPatternNeed()
  {
    return $this->data['patternneed'];
  }

  public function GetPatternSet()
  {
    return $this->data['patternset'];
  }
}
