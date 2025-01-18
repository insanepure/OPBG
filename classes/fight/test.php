<?php
// Raus aus der While Schleife vom Bot
$maxValue = 0;
// ENDE
if ($attackKP > $fighterKP)
{
++$i;
continue;
}

if ($attack->GetEnergy() > $fighterEP)
{
++$i;
continue;
}

if($fighter->GetTransformations() == "")
{
    if($attack->GetType() != 4)
    {
        ++$i;
        continue;
    }
    $count = $attack->GetType();
    $needType = 4;
    $need = substr_count($count, $needType);
    if($attack->GetType() == 4 && $need >= 1)
    {
        if($attack->GetValue() > $maxValue)
        {
            $maxValue = $attack->GetValue();
            return $attack->GetID();
        }
    }
    else
    {
        ++$i;
        continue;
    }
}

if (!$attack->IsPickableByNPC())
{
++$i;
continue;
}

if($fighter->GetBuffs() == "" && $attack->GetType() != 18 && $fighter->HasNPCControl() == 1)
{
++$i;
continue;
}

if($fighter->GetBuffs() != "")
{

}

if($attack->GetType() == 3 && $fighter->HasNPCControl() == 1)
{
++$i;
continue;
}

if($fighter->GetBuffs() != "" && $attack->GetType() == 25 && $fighter->HasNPCControl() == 1)
{
++$i;
continue;
}

if($fighter->GetDOTS() != "" && $attack->GetType() == 22 && $fighter->HasNPCControl() == 1)
{
++$i;
continue;
}

if($fighter->GetBuffs() != "" && $attack->GetType() == 21 && $fighter->HasNPCControl() == 1)
{
++$i;
continue;
}

/*if($attack->GetType() == 3 && $target->GetLP() > $attack->GetValue())
{
++$i;
continue;
}*/

?>
