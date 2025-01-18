<?php
class Promocode
{
	private $data;
	
	function __construct($initialData)
	{
		$this->data = $initialData;
	}

public function GetID()
{
  return $this->data['id'];  
}

public function GetPromocode()
{
    return $this->data['promocode'];
}
public function GetReleas()
{
    return $this->data['erstellt'];
}

public function GetItem()
{
    return $this->data['item'];
}

public function GetBerry()
{
    return $this->data['berry'];   
}

public function GetGold()
{
    return $this->data['gold'];
}

public function GetTitel()
{
    return $this->data['titel'];
}
}
