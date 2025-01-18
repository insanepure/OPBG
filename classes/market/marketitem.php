<?php
    class MarketItem
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

        public function GetSeller()
        {
            return $this->data['seller'];
        }

        public function GetSellerID()
        {
            return $this->data['sellerid'];
        }

        public function GetFormerOwners()
        {
            return $this->data['formerowners'];
        }

        public function GetStatsType()
        {
            return $this->data['statstype'];
        }

        public function GetUpgrade()
        {
            return $this->data['upgrade'];
        }

        public function GetCalculateUpgrade()
        {
            return $this->data['upgrade'] + 1;
        }

        public function GetPrice()
        {
            return $this->data['price'];
        }

        public function GetAmount()
        {
            return $this->data['amount'];
        }

        public function SetAmount($value)
        {
            $this->data['amount'] = $value;
        }

        public function GetVisualID()
        {
            return $this->data['visualid'];
        }

        public function GetStatsID()
        {
            return $this->data['statsid'];
        }

        private function GetTypeName() : string
        {
            switch ($this->GetStatsType())
            {
                case 0: //Alle
                    return 'der Balance';
                case 1: //Angriff
                    return 'des Angriffes';
                case 2: //Abwehr
                    return 'der Verteidigung';
                case 3: //LP
                    return 'des Lebens';
                case 4: //AD
                    return 'der Kraft';
                case 5: //Angriff+Abwehr
                    return 'der Kontrolle';
                case 6: //Angriff+LP
                    return 'der Stärke';
                case 7: //Angriff+AD
                    return 'der Zerstörung';
                case 8: //Abwehr+LP
                    return 'der Ehre';
                case 9: //Abwehr+AD
                    return 'des Schutzes';
                case 10: //LP+AD
                    return 'der Energie';
                case 11: //Angriff+Abwehr+LP
                    return 'des Ausgleiches';
                case 12: //Angriff+Abwehr+AD
                    return 'der Macht';
                case 13: //Angriff+LP+AD
                    return 'der Offensive';
                case 14: //Abwehr+LP+AD
                    return 'der Defensive';
                default:
                    return '';
            }
        }

        public function GetName() : string
        {
            $levelcode = "#FFFFFF";
            if($this->GetUpgrade() == 0 && ($this->GetType() == 3 || $this->GetType() == 4))
            {
                $levelcode = "#6C3801";
            }
            else if($this->GetUpgrade() == 1 && ($this->GetType() == 3 || $this->GetType() == 4))
            {
                $levelcode = "#3CFA08";
            }
            else if($this->GetUpgrade() == 2 && ($this->GetType() == 3 || $this->GetType() == 4))
            {
                $levelcode = "#01B960";
            }
            else if($this->GetUpgrade() == 3 && ($this->GetType() == 3 || $this->GetType() == 4))
            {
                $levelcode = "#0AC2DB";
            }
            else if($this->GetUpgrade() == 4 && ($this->GetType() == 3 || $this->GetType() == 4))
            {
                $levelcode = "#0D05F0";
            }
            else if($this->GetUpgrade() == 5 && ($this->GetType() == 3 || $this->GetType() == 4))
            {
                $levelcode = "#CBBA02";
            }

            $name = $this->data['name'];

            if ($this->GetUpgrade() != 0 || $this->GetUpgrade() == 0 && ($this->GetType() == 3 || $this->GetType() == 4)) {
                if($this->GetUpgrade() == 0)
                    $name = "<span style='color:" . $levelcode . ";'>" . $name . "</span>";
                else
                    $name = "<span style='color:" . $levelcode . ";'>" . $name . ' Level ' . $this->GetCalculateUpgrade() . "</span>";
            }

            return $name;
        }

        public function GetRawName()
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

        public function GetUpgradeDivider()
        {
            return $this->data['upgradedivider'];
        }

        public function GetNeedItem()
        {
            return $this->data['needitem'];
        }

        public function GetRace()
        {
            return $this->data['race'];
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

        public function GetHoverDescription()
        {
            return $this->data['hoverdescription'];
        }

        public function GetCategory()
        {
            return $this->data['category'];
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

        private function GetStatsValue(array $validTypes, float $multiplier) : float
        {
            $value = $this->GetValue();
            $statsType = $this->GetStatsType();
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
                    $divider = 2;
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

        private function GetStatsValueNew(float $multiplier, float $value) : float
        {
            if (in_array($this->GetStatsID(), array(11)))
                $value = $this->GetValue();
            if ($value == 0)
                return 0;

            $upgradeDivider = $this->GetUpgradeDivider();
            if ($upgradeDivider == 0)
                $upgradeDivider = 1;

            $addValue = $value / $upgradeDivider * $this->GetUpgrade();

            $value = $value + $addValue;
            $value = floor($value);

            $value = $value * $multiplier;
            return $value;
        }

        public function GetLP() : float
        {
            if ($this->GetType() == 3)
                $value = $this->GetStatsValueNew(10, $this->data['lp']);
            else
                $value = $this->data['lp'];
            return $value;
        }

        public function GetKP() : float
        {
            if ($this->GetType() == 3)
                $value = $this->GetStatsValueNew(10, $this->data['kp']);
            else
                $value = $this->data['kp'];
            return $value;
        }

        public function GetAttack() : float
        {
            if ($this->GetType() == 3)
                $value = $this->GetStatsValueNew(1, $this->data['attack']);
            else
                $value = $this->data['attack'];
            return $value;
        }

        public function GetDefense() : float
        {
            if ($this->GetType() == 3)
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

        public function DisplayEffect() : string
        {
            switch ($this->GetType())
            {
                case 1:
                    {
                        $effect = '';
                        if ($this->GetLP() != 0) $effect .= 'Heilt <b>' . number_format($this->GetLP(), '0', '', '.') . '</b> <b>LP</b><br/>';
                        if ($this->GetKP() != 0) $effect .= 'Heilt <b>' . number_format($this->GetKP(), '0', '', '.') . '</b> <b>AD</b><br/>';
                        return $effect;
                    }
                case 2:
                    {
                        $effect = '';
                        if ($this->GetLP() != 0) $effect .= 'Heilt <b>' . number_format($this->GetLP(), '0', '', '.') . '%</b> <b>LP</b><br/>';
                        if ($this->GetKP() != 0) $effect .= 'Heilt <b>' . number_format($this->GetKP(), '0', '', '.') . '%</b> <b>AD</b><br/>';
                        return $effect;
                    }
                case 3:
                    {
                        $effect = '';
                        if ($this->GetLP() != 0) $effect .= 'Erhöht <b>LP</b> um ' . number_format($this->GetLP(), '0', '', '.') . '</b><br/>';
                        if ($this->GetKP() != 0) $effect .= 'Erhöht <b>AD</b> um ' . number_format($this->GetKP(), '0', '', '.') . '</b><br/>';
                        if ($this->GetAttack() != 0) $effect .= 'Erhöht <b>Angriff</b> um ' . number_format($this->GetAttack(), '0', '', '.') . '</b><br/>';
                        if ($this->GetDefense() != 0) $effect .= 'Erhöht <b>Verteidigung</b> um ' . number_format($this->GetDefense(), '0', '', '.') . '</b><br/>';
                        return $effect;
                    }
                case 4:
                    {
                        if ($this->GetTravelBonus() != 0) return 'Verringert <b>Reisedauer um <b>' . number_format($this->GetTravelBonus(), '0', '', '.') . '%</b>';
                    }
                default:
                    return $this->GetDescription();
            }
        }

        public function HasOverlay()
        {
            return $this->data['overlay'] != 0;
        }

        public function GetGebot()
        {
            return $this->data['gebot'];
        }

        public function GetBieter()
        {
            return $this->data['bieter'];
        }

        public function GetDauer()
        {
            return $this->data['dauer'];
        }

        public function GetOverlay() : string
        {
            switch ($this->data['overlay'])
            {
                case 1:
                    return 'OverlayEvent';
                default:
                    return '';
            }
        }

        public function GetKaeufer()
        {
            return $this->data['kaeufer'];
        }
    }
