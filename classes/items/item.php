<?php
class Item
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

    private function GetTypeName()
    {
        switch ($this->GetDefaultStatsType())
        {
            case 0: //Alle
                return 'der Balance';
                break;
            case 1: //Angriff
                return 'des Angriffes';
                break;
            case 2: //Abwehr
                return 'der Verteidigung';
                break;
            case 3: //LP
                return 'des Lebens';
                break;
            case 4: //AD
                return 'der Kraft';
                break;
            case 5: //Angriff+Abwehr
                return 'der Kontrolle';
                break;
            case 6: //Angriff+LP
                return 'der Stärke';
                break;
            case 7: //Angriff+AD
                return 'der Zerstörung';
                break;
            case 8: //Abwehr+LP
                return 'der Ehre';
                break;
            case 9: //Abwehr+AD
                return 'des Schutzes';
                break;
            case 10: //LP+AD
                return 'der Energie';
                break;
            case 11: //Angriff+Abwehr+LP
                return 'des Ausgleiches';
                break;
            case 12: //Angriff+Abwehr+AD
                return 'der Macht';
                break;
            case 13: //Angriff+LP+AD
                return 'der Offensive';
                break;
            case 14: //Abwehr+LP+AD
                return 'der Defensive';
                break;
        }
    }

    public function GetRealName()
    {
        return $this->data['name'];
    }

    public function GetMaxUpgrade()
    {
        return $this->data['maxupgrade'];
    }

    public function GetChangeUpgrade()
    {
        return $this->data['changeupgrade'];
    }

    public function GetName()
    {
        return $this->data['name'];
    }

    public function GetImage()
    {
        return $this->data['image'];
    }

    public function IsSellable()
    {
        return $this->data['sellable'];
    }

    public function IsPremium()
    {
        return $this->data['premium'];
    }
    public function IsMarktplatz()
    {
        return $this->data['marktplatz'];
    }

    public function GetNeedItem()
    {
        return $this->data['needitem'];
    }

    public function GetRace()
    {
        return $this->data['race'];
    }

    public function GetHoverDescription()
    {
        return $this->data['hoverdescription'];
    }

    public function GetFightAttack()
    {
        return $this->data['fightattack'];
    }

    public function GetEquippedImage()
    {
        return $this->data['equippedimage'];
    }

    public function GetSlot()
    {
        return $this->data['slot'];
    }

    public function GetTravelBonus()
    {
        return $this->data['travelbonus'];
    }

    public function GetDescription()
    {
        return $this->data['description'];
    }

    public function GetCategory()
    {
        return $this->data['category'];
    }

    public function GetPrice()
    {
        return $this->data['price'];
    }

    public function GetArenaPoints()
    {
        return $this->data['arenapoints'];
    }

    public function GetType()
    {
        return $this->data['type'];
    }

    public function GetValue()
    {
        return $this->data['value'];
    }

    public function GetExpirationDays()
    {
        return $this->data['expirationdays'];
    }

    public function GetSchatzitems()
    {
        return explode(';', $this->data['items']);
    }
    private function GetStatsValue($validTypes, $multiplier)
    {
        $value = $this->GetValue();
        $statsType = $this->GetDefaultStatsType();

        if (!in_array($statsType, $validTypes) || $value == 0)
            return 0;

        $divider = 1;
        switch ($statsType)
        {
            case 0:
                $divider = 4;
                break;
            case 1:
            case 2:
            case 3:
            case 4:
                $divider = 1;
                break;
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
                $divider = 1;
                break;
            case 11:
            case 12:
            case 13:
            case 14:
                $divider = 3;
                break;
        }

        $value = $value * $multiplier;
        $value = floor($value / $divider);

        return $value;
    }

    public function GetUpgradeDivider()
    {
        return $this->data['upgradedivider'];
    }

    private function GetStatsValueNew($multiplier, $value)
    {
        if (in_array($this->GetID(), array(11)))
            $value = $this->GetValue();
        if ($value == 0)
            return 0;

        $upgradeDivider = $this->GetUpgradeDivider();
        if ($upgradeDivider == 0)
            $upgradeDivider = 1;

        $addValue = $value / $upgradeDivider * 0;

        $value = $value + $addValue;
        $value = floor($value);

        $value = $value * $multiplier;
        return $value;
    }

    public function GetLP()
    {
        if ($this->GetType() == 3)
            $value = $this->GetStatsValueNew(10, $this->data['lp']);
        else
            $value = $this->data['lp'];

        return $value;
    }

    public function GetKP()
    {
        if ($this->GetType() == 3)
            $value = $this->GetStatsValueNew(10, $this->data['kp']);
        else
            $value = $this->data['kp'];
        return $value;
    }

    public function GetAttack()
    {
        if ($this->GetType() == 3)
            $value = $this->GetStatsValueNew(1, $this->data['attack']);
        else
            $value = $this->data['attack'];
        return $value;
    }

    public function GetDefense()
    {
        //$aura = array(125, 126, 127, 128, 129, 130);
        if ($this->GetType() == 3)
            //$value = $this->GetStatsValue(array(0,2,5,8,9,11,12,14), 1);
            $value = $this->GetStatsValueNew(1, $this->data['defense']);
        else
            $value = $this->data['defense'];
        return $value;
    }

    public function GetTrainbonus()
    {
        return $this->data['trainbonus'];
    }

    public function GetLevel()
    {
        return $this->data['lv'];
    }

    public function GetUpgradeID()
    {
        return $this->data['upgradeid'];
    }

    public function GetItemUses()
    {
        return $this->data['uses'];
    }

    public function GetDefaultStatsType()
    {
        return $this->data['defaultstatstype'];
    }

    public function SetDefaultStatsType($value)
    {
        $this->data['defaultstatstype'] = $value;
    }

    public function DisplayEffect()
    {
        $text = '';
        switch ($this->GetType())
        {
            case 1:
                {
                    if ($this->GetLP() != 0) $text .= 'Heilt <b>' . number_format($this->GetLP(), '0', '', '.') . ' LP</b><br/>';
                    if ($this->GetKP() != 0) $text .= 'Heilt <b>' . number_format($this->GetKP(), '0', '', '.') . ' AD</b><br/>';
                }
                break;
            case 2:
                {
                    if ($this->GetLP() != 0) $text .= 'Heilt <b>' . number_format($this->GetLP(), '0', '', '.') . '% LP</b><br/>';
                    if ($this->GetKP() != 0) $text .= 'Heilt <b>' . number_format($this->GetKP(), '0', '', '.') . '% AD</b><br/>';
                }
                break;
            case 3:
                {
                    if ($this->GetLP() != 0) $text .= 'Erhöht <b>LP</b> um ' . number_format($this->GetLP(), '0', '', '.') . '</b><br/>';
                    if ($this->GetKP() != 0) $text .= 'Erhöht <b>AD</b> um ' . number_format($this->GetKP(), '0', '', '.') . '</b><br/>';
                    if ($this->GetAttack() != 0) $text .= 'Erhöht <b>Angriff</b> um ' . number_format($this->GetAttack(), '0', '', '.') . '</b><br/>';
                    if ($this->GetDefense() != 0) $text .= 'Erhöht <b>Verteidigung</b> um ' . number_format($this->GetDefense(), '0', '', '.') . '</b><br/>';
                }
                break;
            case 4:
                {
                    if ($this->GetTravelBonus() != 0) $text .= 'Verringert <b>Reisedauer um <b>' . number_format($this->GetTravelBonus(), '0', '', '.') . '%</b>';
                }
                break;
            case 5:
                {
                    $text .= $this->GetDescription();
                }
                break;
        }
        return $text;
    }

    public function HasOverlay()
    {
        return $this->data['overlay'] != 0;
    }

    public function IsVisible()
    {
        return $this->data['visible'] != 0;
    }

    public function GetOverlay()
    {
        switch ($this->data['overlay'])
        {
            case 1:
                return 'OverlayEvent';
        }
        return '';
    }
}
