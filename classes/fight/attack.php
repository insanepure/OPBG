<?php
if ($database == NULL)
{
	print 'This File (' . __FILE__ . ') should be after Database!';
}

class Attack
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

	public function GetDescription()
	{
		return $this->data['description'];
	}

	public function GetLevel()
	{
		return $this->data['level'];
	}

	public function IsPickableByNPC()
	{
		return $this->data['npcpickable'];
	}

	public function GetImage() : string
	{
        return 'img/attacks/' . $this->data['image'] . '.png';
	}

    public function GetNPCImage() : string
    {
        return 'img/npc/' . $this->data['npcimage'] . '.png';
    }

    public function GetRemoveAttacks()
    {
        return $this->data['removeattacks'];
    }

    public function GetPlusDamage()
    {
        return $this->data['plusdamage'];
    }

    public function GetAddAttacks()
    {
        return $this->data['addattacks'];
    }

	public static function GetTypeCount() : int
	{
		return 27;
	}

    public function GetDisplayed()
    {
        return $this->data['displayed'];
    }

	public static function GetTypeName($type) : string
	{
		switch ($type)
		{
			case 1:
				return 'Schaden';
			case 2:
                return 'Verteidigung';
			case 3:
                return 'Tod';
			case 4:
                return 'Verwandlung';
			case 5:
                return 'Heilung';
			case 6:
                return 'Lad-Angriff';
			case 7:
                return 'Ladung';
			case 8:
                return 'Beschwörung';
			case 9:
                return 'Betäubung';
			case 10:
                return 'Betäubt';
			case 11:
                return 'Regenerierung';
			case 12:
                return 'Absorbierung';
			case 13:
                return 'Fusion';
			case 14:
                return 'Wetter';
			case 15:
                return 'Sonstiges';
			case 16:
                return 'Aufgeben';
			case 17:
                return 'Beleben';
			case 18:
                return 'Buffs';
			case 19:
                return 'Befreiung';
			case 20:
                return 'Reflektion';
			case 21:
                return 'Selbst-Buffs';
			case 22:
                return 'DOTS';
			case 23:
                return '% Schaden';
			case 24:
                return '% Tech Schaden';
			case 25:
                return 'Debuffs';
            case 26:
                return 'AOE-Schaden';
            case 27:
                return 'AOE %-Schaden';
		}
        return '';
	}

	public function GetType()
	{
		return $this->data['type'];
	}

	public function GetItem()
	{
		return $this->data['item'];
	}

	public function GetLoadRounds()
	{
		return $this->data['loadrounds'];
	}

	public function GetBlockAttack()
	{
		return $this->data['blockattack'];
	}

	public function GetBlockedAttack()
	{
		return $this->data['blockedattack'];
	}

	public function GetValue()
	{
		return $this->data['value'];
	}

	public function GetLPValue()
	{
		return $this->data['lpvalue'];
	}

	public function GetKPValue()
	{
		return $this->data['kpvalue'];
	}

	public function IsSingleCost()
	{
		return $this->data['singlecost'];
	}

	public function GetEPValue()
	{
		return $this->data['epvalue'];
	}

	public function GetAtkValue()
	{
		return $this->data['atkvalue'];
	}

	public function GetDefValue()
	{
		return $this->data['defvalue'];
	}

	public function GetTauntValue()
	{
		return $this->data['tauntvalue'];
	}

	public function GetReflectValue()
	{
		return $this->data['reflectvalue'];
	}
	public function GetReflexValue()
	{
		return $this->data['reflexvalue'];
	}

	public function GetReflexBuff()
	{
		return $this->data['reflexbuf'];
	}

	public function GetAccBuff()
	{
		return $this->data['accbuf'];
	}

	public function GetMinValue()
	{
		return $this->data['minvalue'];
	}

	public function GetKP($procentual = false)
	{
		$kp = $this->data['kp'];
		if ($procentual)
			$kp = $this->data['kp'] / 100;
		return $kp;
	}

	function IsKPProcentual()
	{
		return $this->data['kpprocentual'];
	}

	public function GetLP()
	{
		return $this->data['lp'];
	}

	public function GetEnergy()
	{
		return $this->data['energy'];
	}

    public function GetCritchance()
    {
        return $this->data['critchance'];
    }

    public function GetCritdamage()
    {
        return $this->data['critdamage'];
    }

	public function IsProcentual()
	{
		return $this->data['procentual'] == 1;
	}

	public function IsCostProcentual()
	{
		return $this->data['procentualcost'] == 1;
	}

	public function GetAccuracy()
	{
		return $this->data['accuracy'];
	}

	public function GetText()
	{
		return $this->data['text'];
	}

	public function GetLoadText()
	{
		return $this->data['loadtext'];
	}

	public function GetTransformationID()
	{
		return $this->data['transformationid'];
	}

	public function GetMissText()
	{
		return $this->data['missText'];
	}

	public function GetDeadText()
	{
		return $this->data['deadText'];
	}

	public function GetLearnKI()
	{
		return $this->data['learnki'];
	}

	public function GetLearnLP()
	{
		return $this->data['learnlp'];
	}

	public function GetLearnKP()
	{
		return $this->data['learnkp'];
	}

	public function GetLearnAttack()
	{
		return $this->data['learnattack'];
	}

	public function GetLearnDefense()
	{
		return $this->data['learndefense'];
	}

	public function GetRace()
	{
		return $this->data['race'];
	}

	public function GetNPCID()
	{
		return $this->data['npcid'];
	}

	public function GetLoadAttack()
	{
		return $this->data['loadattack'];
	}

	public function GetRounds()
	{
		return $this->data['rounds'];
	}

	public function GetLearnTime()
	{
		return $this->data['learntime'];
	}

	public function GetDisplayDied()
	{
		return $this->data['displaydied'];
	}

    public function GetEnemyAmount()
    {
        return $this->data['enemyamount'];
    }
}
