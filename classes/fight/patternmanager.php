<?php
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

include_once 'pattern.php';

class PatternManager
{
    private $database;
    private $patterns;

    function __construct($db)
    {
        $this->database = $db;
        $this->patterns = array();
        $this->LoadData();
    }

    private function LoadData() : void
    {
        $result = $this->database->Select('*', 'patterns', '', 99999);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                while ($row = $result->fetch_assoc())
                {
                    $pattern = new Pattern($row);
                    $this->patterns[] = $pattern;
                }
            }
            $result->close();
        }
    }

    private function CheckPattern(object $object, Fight $fight, Pattern $pattern) : bool
    {
        $valueName = $pattern->GetValueName();
        $value = $object->GetPatternValue($valueName);

        if ($value == null)
            return false;

        $returnValue = null;

        $operator = $pattern->GetOperator();
        $fight->AddDebugLog(' - - - Value (' . $pattern->GetValueName() . '): ' . $value);

        if (!is_numeric($value) && $operator != 2 && $operator != 3)
            return false;

        if ($pattern->IsProcent())
        {
            $fight->AddDebugLog(' - - - Value is Procentual');
            switch ($valueName)
            {
                case 'ki':
                    $value = ($value / $object->GetPatternValue('mki')) * 100;
                    break;
                case 'lp':
                    $value = ($value / $object->GetPatternValue('ilp')) * 100;
                    break;
                case 'kp':
                    $value = ($value / $object->GetPatternValue('ikp')) * 100;
                    break;
                case 'energy':
                    $value = ($value / $object->GetPatternValue('menergy')) * 100;
                    break;
            }
        }

        $fight->AddDebugLog(' - - - Calculated Value: ' . $value);

        $patternValue = $pattern->GetValue();

        $fight->AddDebugLog(' - - - PatternValue: ' . $patternValue);

        if ($operator == 6 && $patternValue == 0)
            return false;

        switch ($operator)
        {
            case 0: //Weniger Als
                $returnValue = $value < $pattern->GetValue();
                break;
            case 1: //Weniger Gleich
                $returnValue = $value <= $pattern->GetValue();
                break;
            case 2: //Gleich
                $returnValue = $value == $pattern->GetValue();
                break;
            case 3: //Nicht Gleich
                $returnValue = $value != $pattern->GetValue();
                break;
            case 4: //Mehr als
                $returnValue = $value > $pattern->GetValue();
                break;
            case 5: //Mehr Gleich
                $returnValue = $value >= $pattern->GetValue();
                break;
            case 6: //Modulo
                $returnValue = $value % $pattern->GetValue() == 0;
                break;
        }

        return $returnValue;
    }


    public function IsPatternPossible(Fighters $fighter, Fight $fight, AttackManager $attackManager, Pattern $pattern) : bool
    {
        $fight->AddDebugLog(' - - Testing Pattern: ' . $pattern->GetName());

        if ($pattern->GetPatternNeed() != $fighter->GetPatternID())
        {
            $fight->AddDebugLog(' - - - PatternID (' . number_format($fighter->GetPatternID(), '0', '', '.') . ') != Need (' . $pattern->GetPatternNeed() . ')');
            return false;
        }

        $patternAttack = $pattern->GetAttack();

        $patternValid = false;

        $patternType = $pattern->GetType();
        if ($patternType == 0)
            $patternValid = $this->CheckPattern($fighter, $fight, $pattern);
        else if ($patternType == 1)
            $patternValid = $this->CheckPattern($fight, $fight, $pattern);
        else
        {
            $fight->AddDebugLog(' - - - Checking Teams');
            $fight->AddDebugLog(' - - - PatternType: ' . $patternType);
            $fight->AddDebugLog(' - - - FighterTeam: ' . $fighter->GetTeam());
            $fightTeams = $fight->GetTeams();
            for ($i = 0; $i < count($fightTeams); ++$i)
            {
                if (
                    $i == $fighter->GetTeam() && $patternType == 2
                    || $i != $fighter->GetTeam() && $patternType == 3
                )
                {
                    continue;
                }

                $players = $fightTeams[$i];

                for ($j = 0; $j < count($players); ++$j)
                {
                    $fight->AddDebugLog(' - - - Checking Fighter: ' . $players[$j]->GetName());
                    $patternValid = $this->CheckPattern($players[$j], $fight, $pattern);
                    if ($patternValid)
                        break;
                }

                if ($patternValid)
                    break;
            }
        }

        if (!$patternValid)
            return false;

        $fight->AddDebugLog(' - - - Pattern is true');

        if ($attackManager == null)
            return false;

        $attack = $attackManager->GetAttack($patternAttack);

        if ($attack->GetKP() > $fighter->GetKP())
            return false;

        if ($attack->GetEnergy() > $fighter->GetRemainingEnergy())
            return false;

        $fight->AddDebugLog(' - - - Pattern is possible!');

        return true;
    }

    public function GetPattern(int $id) : ?Pattern
    {
        $i = 0;
        while (isset($this->patterns[$i]))
        {
            if ($this->patterns[$i]->GetID() == $id)
            {
                return $this->patterns[$i];
            }
            ++$i;
        }
        return null;
    }
}
