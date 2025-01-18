<?php
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

include_once 'fighter.php';
include_once 'attackmanager.php';
include_once 'patternmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/treasurehunt/treasurehunt.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/treasurehunt/treasurehuntisland.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/treasurehunt/treasurehuntprogress.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/treasurehunt/treasurehuntmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/clan/clan.php';

class Fight
{
    private $database;
    private $data;
    private $valid;
    private $player;
    private $playerAccount;
    private $actionManager;
    private $attackManager;
    private $patternManager;
    private $defenseArray;
    private $titelManager = null;
    private $lastfights;

    private $bufflimit = 3;
    private $debufflimit = 1;

    private $preAttacks;
    private $attacks;

    private $startLog = '';

    function __construct($db, $id, $player, ActionManager $actionManager, $lastfights = false)
    {
        $id = $db->EscapeString($id);
        $this->database = $db;
        $this->lastfights = $lastfights;
        $this->valid = false;
        $this->teams = array();
        $this->player = null;
        $this->playerAccount = $player;
        $this->actionManager = $actionManager;
        $this->attackManager = new AttackManager($db);
        $this->patternManager = new PatternManager($db);
        $this->defenseArray = array();

        $this->preAttacks = array();
        $this->attacks = array();

        $this->LoadFight($id);

        if (!$this->IsEnded())
        {
            $this->LoadFighters($player);
        }
        if ($this->IsStarted() && !$this->IsEnded())
        {
            $this->DoRound();
        }

        $this->startLog = $this->GetDebugLog();
    }

    function __destruct()
    {
        $debuglog = $this->GetDebugLog();
        if ($debuglog == $this->startLog)
            return;

        $this->database->Update('debuglog="' . $debuglog . '"', 'fights', 'id = ' . $this->GetID() , 1);
    }

    public function GetPatternValue($name)
    {
        if($name == 'healthratio')
        {
            $players = $this->teams[1];
            $j = 0;
            $hLP = 0;
            while (isset($players[$j]))
            {
                $player = $players[$j];
                $hLP += $player->GetLP() / $player->GetMaxLP();
                ++$j;
            }
            $hLP = round(($hLP / $j) * 100);
            return $hLP;
        }
        else
        {
            return $this->data[$name];
        }
    }

    public function DebugSend($withText = false, $title = '')
    {
        if ($withText)
        {
            echo 'In Euren Kampf kam es zu einen Fehler. Der Fehler muss manuell behoben werden, die Administration wurde informiert.<br/>';
            echo 'Falls du diese Nachricht siehst, melde dich bei Shirobaka im Discord.<br/>';
        }

        $timestamp = date('Y-m-d H:i:s');
        if ($title == '')
            $title = 'Error In Fight: ' . $this->GetName() . '(' . $this->GetID() . ')';
        $isHTML = 1;
        $this->database->Insert(
            '`sendername`, `senderimage`, `senderid`, `receiverid`, `receivername`, `text`, `time`, `topic`, `read`, `ishtml`',
            '"System","img/battel2.png","0","3","SloadX","' . $this->GetDebugLog() . '","' . $timestamp . '","' . $title . '", "0","' . $isHTML . '"',
            'pms'
        );
        $this->database->Insert(
            '`sendername`, `senderimage`, `senderid`, `receiverid`, `receivername`, `text`, `time`, `topic`, `read`, `ishtml`',
            '"System","img/battel2.png","0","506","ShirobakaX","' . $this->GetDebugLog() . '","' . $timestamp . '","' . $title . '", "0","' . $isHTML . '"',
            'pms'
        );
    }

    public function GetMeldeGrund()
    {
        return $this->data['meldegrund'];
    }

    public function Kick($id)
    {
        $target = $this->GetFighter($id);
        $actionsLeft = 0;
        $this->CalculateLeftActions($actionsLeft);
        if ($target == null || $target->IsNPC() || $target->GetAction() != 0 || $target->GetLP() == 0 || $target->GetActionCountdown($this->GetType()) > 0 || $target->HasNPCControl())
        {
            return;
        }
        else if($this->GetType() == 0)
        {
            $target->SetLP(0);
            $this->database->Update('lp="0"', 'fighters', 'id = ' . $target->GetID() , 1);
        }
        else if ($this->GetType() == 6 || $this->GetType() == 8 || $this->GetType() == 1) // Tournament || Arena || PvP //
        {
            $checkp = $this->database->Select('*', 'accounts', 'id="'.$target->GetAcc().'"', 1);
            $check = $checkp->fetch_assoc();
            $cautionc = $check['zeni'] / 100;
            $caution = $check['zeni'] - $cautionc;
            if($caution < 0)
            {
                $caution = 0;
            }

            $target->SetLP(0);
            $this->database->Update('lp="0"', 'fighters', 'id = ' . $target->GetID() , 1);
            $this->database->Update('zeni="'.$caution.'"', 'accounts', 'id="'.$target->GetAcc().'"');
        }
        else
        {
            $target->SetNPCControl(true);
            $this->database->Update('npccontrol=1, iskicked=1', 'fighters', 'id = ' . $target->GetID() , 1);
            $target->SetKicked(1);
        }

        if ($this->GetType() == 8)
        {
            $p = $this->database->Select('*', 'accounts', 'id="'.$target->GetAcc().'"');
            $pl = $p->fetch_assoc();
            $r = 8;
            $a = $pl['arenapoints'] + 3;
            $b = $a - $r;
            if($b < 0)
            {
                $b = 0;
            }
            $counts = $pl['arenakickcount'];
            $rechnung = $counts + 1;
            if($rechnung > 3)
            {
                $rechnung = 3;
            }
            $this->database->Update('arenakickcount="'.$rechnung.'"', 'accounts', 'id="'.$pl['id'].'"');
            $this->database->Update('arenapoints="'.$b.'"', 'accounts', 'id="'.$target->GetAcc().'"');
            $this->database->Delete('arenafighter', 'fighter = ' . $target->GetAcc() , 1);
        }

        if ($actionsLeft == 1)
        {
            $this->CalculateRound();
        }
    }

    static function GetTypeName($type)
    {
        switch ($type)
        {
            case 0:
                return 'Spaß';
            case 1:
                return 'PvP';
            case 3:
                return 'NPC';
            case 4:
                return 'Story';
            case 5:
                return 'Event';
            case 6:
                return 'Turnier';
            case 8:
                return 'Kolosseum';
            case 9:
                return 'Turm';
            case 10:
                return 'Neben-Story';
            case 11:
                return 'Bande';
            case 12:
                return 'Schatzsuche';
            case 13:
                return 'Elokampf';
            default:
                return 'ERROR';
        }
    }

    private function getTitelManager() : TitelManager
    {
        if ($this->titelManager == null)
            $this->titelManager = new TitelManager($this->database);

        return $this->titelManager;
    }

    public function GetMeldeCount()
    {
        return $this->data['meldecount'];
    }

    private function addInAttackArray(&$array, $attack)
    {
        $isAdded = false;
        if (count($array) != 0)
        {
            $kp = $attack->GetKP();
            $isAdded = false;
            for ($i = 0; $i < count($array); ++$i)
            {
                if ($array[$i]->GetKP() < $kp)
                {
                    $isAdded = true;
                    array_splice($array, $i, 0, [$attack]);
                    break;
                }
            }
        }

        if (!$isAdded)
            $array[] = $attack;
    }

    private function pickRandomAttack(&$array)
    {
        for ($i = 0; $i < count($array); ++$i)
        {
            $aRand = rand(0, 4);
            if ($aRand != 0)
            {
                return $array[$i];
            }
        }

        if (count($array) == 0)
            return null;

        return $array[0];
    }

    private function addInTargetArray(&$array, $target)
    {
        $isAdded = false;
        if (count($array) != 0)
        {
            $def = $target->GetDefense();
            for ($i = 0; $i < count($array); ++$i)
            {
                if ($array[$i]->GetDefense() < $def)
                {
                    $isAdded = true;
                    array_splice($array, $i, 0, [$target]);
                    break;
                }
            }
        }

        if (!$isAdded)
            $array[] = $target;
    }

    private function pickRandomTarget($player, $targetType)
    {
        $targets = array();
        $this->AddDebugLog(' - - Pick Random Target based on Type: ' . $targetType);

        $weakestTarget = null;

        for ($i = 0; $i < count($this->teams); ++$i)
        {
            if (
                $i == $player->GetTeam() && ($targetType == 1  || $targetType == 4)
                || $i != $player->GetTeam() && ($targetType == 2  || $targetType == 3)
            )
            {
                continue;
            }

            $players = $this->teams[$i];

            for ($j = 0; $j < count($players); ++$j)
            {
                if ($players[$j]->GetLP() != 0 && !$players[$j]->IsInactive())
                {
                    $this->AddDebugLog(' - - Random Target can be: ' . $players[$j]->GetName());
                    $this->AddDebugLog(' - - Random Target LP: ' . number_format($players[$j]->GetLP(), '0', '', '.'));
                    if ($weakestTarget != null)
                    {
                        $this->AddDebugLog(' - - PreviousTarget: ' . $weakestTarget->GetName());
                        $this->AddDebugLog(' - - PreviousTarget LP: ' . number_format($weakestTarget->GetLP(), '0', '', '.'));
                    }

                    if ($weakestTarget == null || $weakestTarget != null && $weakestTarget->GetLP() > $players[$j]->GetLP())
                        $weakestTarget = $players[$j];

                    $this->addInTargetArray($targets, $players[$j]);
                }
            }
        }

        $this->AddDebugLog(' - - Targets: ' . count($targets));

        if (count($targets) == 0)
            return null;

        if ($targetType == 3 || $targetType == 4) //Weakest
            return $weakestTarget;

        $tID = rand(0, count($targets) - 1);
        return $targets[$tID];
    }

    private function CalculateRandomAttack($fighter)
    {
        $offensives = array();
        $defenses = array();
        $npcspawn = array();
        $trans = array();
        $heal = array();
        $kill = array();
        $paralyzes = array();
        $buffs = array();
        $debuffs = array();


        $endAttacks = array();
        $i = 0;

        $attacks = explode(';', $fighter->GetAttacks());
        $fighterKP = $fighter->GetKP();

        $fighterEP = $fighter->GetRemainingEnergy();

        $this->AddDebugLog(' - - AD: ' . number_format($fighterKP, '0', '', '.'));
        $this->AddDebugLog(' - - EP: ' . number_format($fighterEP, '0', '', '.'));

        $maxKPCost = 0;

        if($fighter->GetNPC())
        {

            while (isset($attacks[$i]))
            {
                $aid = $attacks[$i];
                $attack = $this->GetAttack($aid);
                if ($attack == null)
                {
                    $this->AddDebugLog(' - - ERROR: Attack with ID ' . number_format($aid, '0', '', '.') . ' not found');
                    $this->DebugSend(true);
                    ++$i;
                    continue;
                }
                $this->AddDebugLog(' - - Test Attack: ' . $attack->GetName());

                $attackKP = $attack->GetKP();

                if ($attackKP > $maxKPCost)
                    $maxKPCost = $attackKP;

                $this->AddDebugLog(' - - Attack AD: ' . number_format($attackKP, '0', '', '.'));
                $this->AddDebugLog(' - - Attack EP: ' . number_format($attack->GetEnergy(), '0', '', '.'));

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

                if (!$attack->IsPickableByNPC())
                {
                    ++$i;
                    continue;
                }

                $this->AddDebugLog(' - - Valid Attack: ' . $attack->GetName());


                $endAttacks[] = $attack;

                if ($attack->GetType() == 1)
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 2)
                {
                    $this->addInAttackArray($defenses, $attack);
                }
                else if ($attack->GetType() == 3)
                {
                    $this->addInAttackArray($kill, $attack);
                }
                else if ($attack->GetType() == 4)
                {
                    $this->addInAttackArray($trans, $attack);
                }
                else if ($attack->GetType() == 5)
                {
                    $this->addInAttackArray($heal, $attack);
                }
                else if ($attack->GetType() == 6)
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 7)
                {
                    $this->addInAttackArray($defenses, $attack);
                }
                else if ($attack->GetType() == 8)
                {
                    $this->addInAttackArray($npcspawn, $attack);
                }
                else if ($attack->GetType() == 9)
                {
                    $this->addInAttackArray($paralyzes, $attack);
                }
                else if ($attack->GetType() == 10)
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 11)
                {
                    $this->addInAttackArray($heal, $attack);
                }
                else if ($attack->GetType() == 12)
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 12) // Heal-Attack
                {
                    $this->addInAttackArray($heal, $attack);
                }
                //else if($attack->GetType() == 13) // Fusion
                //{
                //	$this->addInAttackArray($trans, $attack);
                //}
                else if ($attack->GetType() == 14) // Wetter
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 15) // Sonstiges
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 16) // Aufgeben
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 17) // Beleben
                {
                    $this->addInAttackArray($heal, $attack);
                }
                else if ($attack->GetType() == 18) // Buffs
                {
                    $this->addInAttackArray($heal, $attack);
                }
                else if ($attack->GetType() == 19) // UnParalyze
                {
                    $this->addInAttackArray($heal, $attack);
                }
                else if ($attack->GetType() == 20) // Reflect
                {
                    $this->addInAttackArray($defenses, $attack);
                }
                else if ($attack->GetType() == 21) // Selbstbuff
                {
                    $this->addInAttackArray($heal, $attack);
                }
                else if ($attack->GetType() == 22) // Dots
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 23) // % Damage
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 24) // % Tech Damage
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 25)
                {
                    $this->addInAttackArray($heal, $attack);
                }
                else if ($attack->GetType() == 26) // AOE Damage
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 27) // AOE % Damage
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                ++$i;
            }

            $attack = null;
            $defenseChance = rand(0, 10);
            $transChance = 0;
            $killChance = rand(0, 10);
            $healChance = rand(0, 20);
            if ($fighterKP < $maxKPCost)
                $healChance = rand(0, 1);
            $npcspawnChance = rand(0, 10);
            $paraChance = rand(0, 10);

            if ($fighter->GetTransformations() == '' && count($trans) != 0)
            {
                $transChance = 1;
            }

            if (count($paralyzes) > 0 && $paraChance == 1)
            {
                $attack = $this->pickRandomAttack($paralyzes);
            }
            else if (count($npcspawn) > 0 && $npcspawnChance == 1)
            {
                $attack = $this->pickRandomAttack($npcspawn);
            }
            else if (count($kill) > 0 && $killChance == 1)
            {
                $attack = $this->pickRandomAttack($kill);
            }
            else if (count($trans) > 0 && $transChance == 1)
            {
                $attack = $this->pickRandomAttack($trans);
            }
            else if ($fighter->GetLP() < ($fighter->GetMaxLP() * 0.9) && count($heal) > 0 && $healChance == 1)
            {
                $attack = $this->pickRandomAttack($heal);
            }
            else if (count($defenses) > 0 && $defenseChance == 1)
            {
                $attack = $this->pickRandomAttack($defenses);
            }
            else
            {
                $attack = $this->pickRandomAttack($offensives);
            }
            $this->AddDebugLog(' - - NPC: ' . $fighter->GetName());
        }
        else // Botübernahme
        {
            while (isset($attacks[$i]))
            {
                $aid = $attacks[$i];
                $attack = $this->GetAttack($aid);
                if ($attack == null)
                {
                    $this->AddDebugLog(' - - ERROR: Attack with ID ' . number_format($aid, '0', '', '.') . ' not found');
                    $this->DebugSend(true);
                    ++$i;
                    continue;
                }
                $this->AddDebugLog(' - - Test Attack: ' . $attack->GetName());

                $attackKP = $attack->GetKP();

                if ($attackKP > $maxKPCost)
                    $maxKPCost = $attackKP;

                $this->AddDebugLog(' - - Attack AD: ' . number_format($attackKP, '0', '', '.'));
                $this->AddDebugLog(' - - Attack EP: ' . number_format($attack->GetEnergy(), '0', '', '.'));

                if($attack->GetType() == 3) // Keine Killtechs
                {
                    ++$i;
                    continue;
                }

                if($attack->GetType() == 2) // Keine Verteidigungen
                {
                    ++$i;
                    continue;
                }

                if($attack->GetType() == 16) // Keine Aufgeben
                {
                    ++$i;
                    continue;
                }

                if($attack->GetType() == 4 && $fighter->GetTransformations() != "")
                {
                    ++$i;
                    continue;
                }

                $npcbuffs = explode(';', $fighter->GetBuffs());
                $buffentrys = array();
                foreach ($npcbuffs as $npcbuff) {
                    $buffentrys[] = explode('@', $npcbuff)[1];
                }

                if(in_array($attack->GetID(), $buffentrys))
                {
                    ++$i;
                    continue;
                }

                if(($attack->GetType() == 18 || $attack->GetType() == 21) && count(explode(';', $fighter->GetBuffs())) >= 3)
                {
                    ++$i;
                    continue;
                }

                if($attack->GetType() == 25 && $this->teams[0][0]->GetDebuffs() != "")
                {
                    ++$i;
                    continue;
                }

                if($attack->GetType() == 22 && $this->teams[0][0]->GetDOTS() != "")
                {
                    ++$i;
                    continue;
                }

                if($attack->GetType() == 9 && $this->teams[0][0]->GetParalyzed() != 0)
                {
                    ++$i;
                    continue;
                }

                if($attack->GetType() != 1 && $this->teams[0][0]->GetLP() < $this->teams[0][0]->GetMaxLP() / 10)
                {
                    ++$i;
                    continue;
                }

                if($attack->GetType() != 1 && ($attack->GetKP($attack->IsKPProcentual()) > $fighter->GetKP() || $attack->GetEnergy() > $fighter->GetRemainingEnergy()))
                {
                    ++$i;
                    continue;
                }

                $this->AddDebugLog(' - - Valid Attack: ' . $attack->GetName());

                $endAttacks[] = $attack;

                if ($attack->GetType() == 1)
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 4)
                {
                    $this->addInAttackArray($trans, $attack);
                }
                else if ($attack->GetType() == 6)
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 8)
                {
                    $this->addInAttackArray($npcspawn, $attack);
                }
                else if ($attack->GetType() == 9)
                {
                    $this->addInAttackArray($paralyzes, $attack);
                }
                else if ($attack->GetType() == 10)
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 12)
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 18) // Buffs
                {
                    $this->addInAttackArray($buffs, $attack);
                }
                else if ($attack->GetType() == 20) // Reflect
                {
                    $this->addInAttackArray($defenses, $attack);
                }
                else if ($attack->GetType() == 21) // Selbstbuff
                {
                    $this->addInAttackArray($buffs, $attack);
                }
                else if ($attack->GetType() == 22) // Dots
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 23) // % Damage
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 24) // % Tech Damage
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 25)
                {
                    $this->addInAttackArray($debuffs, $attack);
                }
                else if ($attack->GetType() == 26) // AOE Damage
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                else if ($attack->GetType() == 27) // AOE % Damage
                {
                    $this->addInAttackArray($offensives, $attack);
                }
                ++$i;
            }

            $attack = null;
            $transChance = 0;
            $buffchance = rand(0,3);
            $debuffchance = rand(0,3);
            $paraChance = rand(0,4);

            if ($fighter->GetTransformations() == '')
                $transChance = 1;

            if($this->GetMembersOfTeam($fighter->GetTeam()) == 1)
                $npcspawnChance = rand(0,2);

            if (count($trans) > 0 && $transChance == 1)
            {
                $attack = $this->GetStrongestTransformation($trans);
            }
            else if (count($npcspawn) > 0 && $npcspawnChance == 1)
            {
                $attack = $this->pickRandomAttack($npcspawn);
            }
            else if (count($buffs) > 0 && (($this->GetRound() <= 5 && $buffchance >= 2) || ($this->GetRound() > 5 && $buffchance >= 1)))
            {
                $attack = $this->GetStrongestBuff($buffs);
            }
            else if (count($debuffs) > 0 && (($this->GetRound() <= 5 && $debuffchance >= 2) || ($this->GetRound() > 5 && $debuffchance >= 1)))
            {
                $attack = $this->GetStrongestBuff($debuffs);
            }
            /*else if (count($paralyzes) > 0 && $paraChance == 0)
                {
                    $attack = $this->pickRandomAttack($paralyzes);
                }*/
            else
            {
                $attack = $this->GetStrongestAttack($offensives);
                if ($attack->GetKP($attack->IsKPProcentual()) > $fighter->GetKP())
                {
                    $attack = $this->GetStrongestAttackWithoutCosts($offensives);
                }
                else if ($attack->GetEnergy() > $fighter->GetRemainingEnergy())
                {
                    $attack = $this->GetStrongestAttackWithoutCosts($offensives);
                }
            }
        }

        if ($attack == null)
        {
            if (count($endAttacks) == 0)
            {
                $failSafe = 1;
                $this->AddDebugLog(' - - ERROR: no Attack for ' . $fighter->GetName());
                $this->AddDebugLog(' - - ERROR: Fallback to ID ' . number_format($failSafe, '0', '', '.'));
                $this->DebugSend(true);
                $attack = $this->GetAttack($failSafe);
            }
            else
                $attack = $this->pickRandomAttack($endAttacks);
        }

        return $attack;
    }

    private function GetStrongestTransformation($attacks)
    {
        $strongest = null;
        foreach ($attacks as $vw)
        {
            $vwattack = $vw;
            if($strongest == null)
            {
                $strongest = $vwattack;
            }
            else
            {
                $strongestvalue = $strongest->GetValue() * $strongest->GetLPValue() +
                    $strongest->GetValue() * $strongest->GetKPValue() +
                    $strongest->GetValue() * $strongest->GetAtkValue() +
                    $strongest->GetValue() * $strongest->GetDefValue();

                $vwvalue = $vwattack->GetValue() * $vwattack->GetLPValue() +
                    $vwattack->GetValue() * $vwattack->GetKPValue() +
                    $vwattack->GetValue() * $vwattack->GetAtkValue() +
                    $vwattack->GetValue() * $vwattack->GetDefValue();

                if($vwvalue > $strongestvalue)
                    $strongest = $vwattack;
            }
        }
        return $strongest;
    }

    private function GetStrongestBuff($attacks)
    {
        $strongest = null;
        foreach ($attacks as $attack)
        {
            $vwattack = $attack;
            if($strongest == null)
            {
                $strongest = $vwattack;
            }
            else
            {
                $strongestvalue = $strongest->GetValue() * $strongest->GetLPValue() +
                    $strongest->GetValue() * $strongest->GetKPValue() +
                    $strongest->GetValue() * $strongest->GetAtkValue() +
                    $strongest->GetValue() * $strongest->GetDefValue();

                $vwvalue = $vwattack->GetValue() * $vwattack->GetLPValue() +
                    $vwattack->GetValue() * $vwattack->GetKPValue() +
                    $vwattack->GetValue() * $vwattack->GetAtkValue() +
                    $vwattack->GetValue() * $vwattack->GetDefValue();

                if($vwvalue > $strongestvalue)
                    $strongest = $vwattack;
            }
        }
        return $strongest;
    }

    private function GetStrongestAttack($attacks)
    {
        $strongest = null;
        foreach ($attacks as $attack)
        {
            $vwattack = $attack;
            if($strongest == null)
            {
                $strongest = $vwattack;
            }
            else
            {
                $strongestvalue = $strongest->GetValue() * $strongest->GetLPValue();

                $attackvalue = $vwattack->GetValue() * $vwattack->GetLPValue();

                if($attackvalue > $strongestvalue)
                    $strongest = $vwattack;
            }
        }
        return $strongest;
    }

    private function GetStrongestAttackWithoutCosts($attacks)
    {
        foreach ($attacks as $attack) {
            if($attack->GetKP() > 0 || $attack->GetEnergy() > 0)
                array_splice($attacks, array_search($attack, $attacks), 1);
        }
        $strongest = null;
        foreach ($attacks as $attack)
        {
            $vwattack = $attack;
            if($strongest == null)
            {
                $strongest = $vwattack;
            }
            else
            {
                $strongestvalue = $strongest->GetValue() * $strongest->GetLPValue();

                $attackvalue = $vwattack->GetValue() * $vwattack->GetLPValue();

                if($attackvalue > $strongestvalue)
                    $strongest = $vwattack;
            }
        }
        return $strongest;
    }

    private function DoNPCAttack($fighter)
    {
        $this->AddDebugLog(' - DoNPCAttack for: ' . $fighter->GetName());
        $this->AddDebugLog(' - - Attacks: ' . $fighter->GetAttacks());
        $target = $fighter->GetID();

        $patterns = $fighter->GetPatterns();

        $pattern = null;
        if ($patterns != '')
        {
            $this->AddDebugLog(' - - Pattern Test: ' . $patterns);
            $patterns = explode(';', $patterns);

            for ($i = 0; $i < count($patterns); ++$i)
            {
                $patternID = $patterns[$i];
                $foundPattern = $this->patternManager->GetPattern($patternID);
                if ($foundPattern != null && $this->patternManager->IsPatternPossible($fighter, $this, $this->attackManager, $foundPattern))
                {
                    $pattern = $foundPattern;
                    break;
                }
            }
        }

        $patternid = 0;

        //0 = self
        //1 = Random Enemy
        //2 = Random Team
        //3 = Schwächster Team
        //4 = Schwächster Gegner
        //5 = Spezifischer NPC
        $targetType = 0;
        if ($pattern != null)
        {
            $attack = $this->GetAttack($pattern->GetAttack());
            $targetType = $pattern->GetPatternTarget();
            $patternid = $pattern->GetPatternSet();
        }
        else
        {
            $attack = $this->CalculateRandomAttack($fighter);

            if ($attack->GetType() == 5)
                $targetType = 2;
            else
                $targetType = 1;
        }

        if ($targetType == 0)
            $target = $fighter;
        else if($targetType == 5) {
            for ($i = 0; $i < count($this->teams); ++$i) {
                $players = $this->teams[$i];
                for ($j = 0; $j < count($players); ++$j) {
                    if ($players[$j]->GetNPC() == $pattern->GetSpecificTarget()) {
                        if ($players[$j]->GetLP() > 0) {
                            $target = $players[$j];
                        }
                    }
                }
            }
            if(is_string($target) || $target == null)
            {
                $attack = $this->GetAttack($pattern->GetFallbackAttack());
                $target = $this->pickRandomTarget($fighter, 1);
            }

        }
        else
            $target = $this->pickRandomTarget($fighter, $targetType);

        if ($target == null)
        {
            $target = $fighter;
            $this->AddDebugLog(' - - ERROR: No target for ' . $fighter->GetName());
            $this->DebugSend(true);
        }

        $tID = $target->GetID();
        $aid = $attack->GetID();
        $fighter->SetAction($aid);
        $fighter->SetTarget($tID);
        $fighter->SetPreviousTarget($tID);
        $fighter->SetPatternID($patternid);
        $timestamp = date('Y-m-d H:i:s');
        $this->database->Update('action=' . $aid . ', target=' . $tID . ', previoustarget=' . $tID . ', lastaction="' . $timestamp . '", patternid=' . $patternid , 'fighters', 'id = ' . $fighter->GetID() , 1);
    }

    public function UpdateAttackCode($fighter)
    {
        $attackcode = md5($fighter->GetID() + time() + hexdec($fighter->GetAttackCode()));
        $this->database->Update('attackcode="' . $attackcode . '"', 'fighters', 'id = ' . $fighter->GetID() , 1);
        $fighter->SetAttackCode($attackcode);
    }

    public function AddDebugLog($text)
    {
        $debugLog = $this->GetDebugLog();
        if ($debugLog == '')
            $debugLog = $text;
        else
            $debugLog = $debugLog . '<br/>' . $text;

        $this->SetDebugLog($debugLog);
    }

    private function CalculateLeftActions(&$actionsLeft)
    {
        $this->AddDebugLog('- CalculateLeftActions -');
        $i = 0;
        $actionsLeft = 0;
        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            while (isset($players[$j]))
            {
                if ($players[$j]->GetAction() == 0 && $players[$j]->GetLP() != 0 && !$players[$j]->IsInactive())
                {
                    if ($players[$j]->IsNPC() || $players[$j]->HasNPCControl())
                    {
                        $this->AddDebugLog('- NPCAttack -');
                        $this->DoNPCAttack($players[$j]);
                    }
                    else
                    {
                        ++$actionsLeft;
                    }
                }
                else
                {
                    $this->AddDebugLog($players[$j]->GetName());
                    $this->AddDebugLog('Action: ' . $players[$j]->GetAction());
                    $this->AddDebugLog('IsInactive: ' . $players[$j]->IsInactive());
                    $this->AddDebugLog('- NO NPCAttack -');
                }
                ++$j;
            }
            ++$i;
        }
    }

    public function DoRound()
    {
        $actionsLeft = 0;
        $this->CalculateLeftActions($actionsLeft);
        if ($actionsLeft == 0)
        {
            $this->AddDebugLog('- Do Round');
            $this->CalculateRound();
        }
    }

    public function GiveUp($fighter)
    {
        if ($fighter->GetAction() != 0 || $fighter->GetLP() == 0)
        {
            return;
        }
        $fighter->SetLP(0);

        $actionsLeft = 0;
        $this->CalculateLeftActions($actionsLeft);
        if ($actionsLeft == 0)
        {
            $this->CalculateRound();
        }
        else
        {
            $this->AddDebugLog($fighter->GetName() . ' gibt den Kampf auf.');
            $this->database->Update('lp="0"', 'fighters', 'id = ' . $fighter->GetID() , 1);
        }
    }

    public function DoAttack($fighter, $aid, $tid)
    {
        if ($fighter->GetAction() != 0 || $fighter->GetLP() == 0)
        {
            $this->AddDebugLog('returning DoAttack for Acc: '. $fighter->GetAcc());
            return;
        }

        $attack = $this->GetAttack($aid);
        if ($attack == null)
        {
            return;
        }

        $cost = $this->CalculateCost($attack->GetKP(), $attack->IsCostProcentual(), $fighter->GetMaxKP());
        $energy = $attack->GetEnergy();

        if ($energy < 0)
            $energy = 0;

        if ($energy > $fighter->GetRemainingEnergy())
        {
            $fighter->SetPreviousTarget($tid);
            return;
        }

        if ($cost > $fighter->GetKP())
        {
            $fighter->SetPreviousTarget($tid);
            return;
        }

        if($attack->GetType() == 21)
            $target = $fighter;
        else
            $target = $this->GetFighter($tid);

        if($attack->GetType() == 3 && $fighter->GetUseKill() == 0)
        {
            $result = $this->database->Update('usekill=1', 'fighters', 'acc="'.$fighter->GetAcc().'"', 1);
        }

        if(!$fighter->IsNPC() && $attack->GetType() == 25 && $target->GetDebuffs() != "" && count(explode(';', $target->GetDebuffs())) >= $this->debufflimit)
        {
            $debuffs = explode(';', $target->GetDebuffs());
            $debuffids = array();
            foreach ($debuffs as $debuff)
            {
                $debuffdata = explode('@', $debuff);
                array_push($debuffids, $debuffdata[1]);
            }
            if(!in_array($attack->GetID(), $debuffids))
            {
                $fighter->SetPreviousTarget($tid);
                return 'Dieser Spieler hat bereits die maximale Anzahl an Debuffs erhalten.';
            }
        }

        if(!$fighter->IsNPC() && $attack->GetType() == 18 && $target->GetBuffs() != "" && count(explode(';', $target->GetBuffs())) >= $this->bufflimit)
        {
            $buffs = explode(';', $target->GetBuffs());
            $buffids = array();
            foreach ($buffs as $buff)
            {
                $buffdata = explode('@', $buff);
                array_push($buffids, $buffdata[1]);
            }
            if(!in_array($attack->GetID(), $buffids))
            {
                $fighter->SetPreviousTarget($tid);
                return 'Dieser Spieler hat bereits die maximale Anzahl an Buffs erhalten.';
            }
        }

        if(!$fighter->IsNPC() && $attack->GetType() == 21 && $target->GetBuffs() != "" && count(explode(';', $target->GetBuffs())) >= $this->bufflimit)
        {
            $buffs = explode(';', $target->GetBuffs());
            $buffids = array();
            foreach ($buffs as $buff)
            {
                $buffdata = explode('@', $buff);
                array_push($buffids, $buffdata[1]);
            }
            if(!in_array($attack->GetID(), $buffids))
            {
                $fighter->SetPreviousTarget($tid);
                return 'Du hast bereits die maximale Anzahl an Buffs erhalten.';
            }
        }

        if ($attack->GetID() == 2 && $fighter->GetName() != $target->GetName())
        {
            return "";
        }

        if ($target == null || $target->IsInactive())
        {
            return "";
        }

        $fighterAttacks = explode(';', $fighter->GetAttacks());

        $i = 0;
        $gotAttack = false;
        while (isset($fighterAttacks[$i]))
        {
            if ($fighterAttacks[$i] == $aid)
            {
                $gotAttack = true;
                break;
            }
            ++$i;
        }

        if (!$gotAttack)
        {
            return "";
        }

        $actionsLeft = 0;
        $this->CalculateLeftActions($actionsLeft);

        $loadRounds = $attack->GetLoadRounds();
        if ($loadRounds > 0)
        {
            $loadRounds = $loadRounds + 1;
        }

        --$actionsLeft;
        $fighter->SetAction($aid);
        $fighter->SetTarget($tid);
        $fighter->SetPreviousTarget($tid);
        $fighter->SetLoadRounds($loadRounds);

        $kickStartSeconds = 10;
        $timeDiff = (time() - strtotime($fighter->GetLastAction())) - $kickStartSeconds;
        if ($timeDiff > 0)
        {
            $kickTimer = $fighter->GetKickTimer() + $timeDiff;
            $fighter->SetKickTimer($kickTimer);
            $this->database->Update('kicktimer="' . $kickTimer . '"', 'fighters', 'id = ' . $fighter->GetID() , 1);
        }

        if ($actionsLeft == 0)
        {
            $this->AddDebugLog('- Do Attack');
            $this->CalculateRound();
        }
        else
        {
            $timestamp = date('Y-m-d H:i:s');
            $this->database->Update('action="' . $aid . '", target="' . $tid . '", previoustarget="' . $tid . '", lastaction="' . $timestamp . '", loadrounds="' . $loadRounds . '"', 'fighters', 'id = ' . $fighter->GetID() , 1);
        }
    }

    public function GetTeamColor($team)
    {
        $teamColor = '#000000';
        if ($team == 0)
        {
            $teamColor = '#0000ff';
        }
        else if ($team == 1)
        {
            $teamColor = '#ff0000';
        }
        else if ($team == 2)
        {
            $teamColor = '#00ff00';
        }
        else if ($team == 3)
        {
            $teamColor = '#ffaa22';
        }
        else if ($team == 4)
        {
            $teamColor = '#00ffff';
        }
        else if ($team == 5)
        {
            $teamColor = '#ff00ff';
        }
        else if ($team == 6)
        {
            $teamColor = '#11bb55';
        }
        else if ($team == 7)
        {
            $teamColor = '#0022aa';
        }
        else if ($team == 8)
        {
            $teamColor = '#bb5599';
        }
        else if ($team == 9)
        {
            $teamColor = '#aaaa00';
        }
        else if ($team == 10)
        {
            $teamColor = '#00aaaa';
        }

        return $teamColor;
    }

    private function CheckForWonTeam()
    {
        $this->AddDebugLog('CheckForWonTeam');
        if ($this->GetHealthRatio() != 0)
        {
            $this->AddDebugLog('- HealthRatio (' . $this->GetHealthRatio() . ') != 0');
            $healthTeam = $this->GetHealthRatioTeam();
            $players = $this->teams[$healthTeam];
            $j = 0;
            $hLP = 0;
            while (isset($players[$j]))
            {
                $player = $players[$j];
                $hLP += $player->GetLP() / $player->GetMaxLP();
                ++$j;
            }
            $hLP = round(($hLP / $j) * 100);
            if ($hLP < $this->GetHealthRatio())
            {
                $this->AddDebugLog('- HealthRatioWinner: ' . $this->GetHealthRatioWinner());
                return $this->GetHealthRatioWinner();
            }
        }

        if ($this->GetSurvivalRounds() != 0 && $this->GetSurvivalRounds() == $this->GetRound())
        {
            $this->AddDebugLog(' - SurvivalRounds ( ' . number_format($this->GetSurvivalRounds(), '0', '', '.') . ') != 0');
            //if rounds are over and we still have no team won, make the winner win
            return $this->GetSurvivalWinner();
        }

        $aliveTeam = -2; //Nobody won
        $this->AddDebugLog(' - Check alive');
        $i = 0;
        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            while (isset($players[$j]))
            {
                $player = $players[$j];
                $this->AddDebugLog(' -- Player ' . $player->GetName() . ' LP = ' . $player->GetLP());
                $this->AddDebugLog(' -- Player ' . $player->GetName() . ' AD = ' . $player->GetKP());
                $this->AddDebugLog(' -- Player ' . $player->GetName() . ' EP = ' . $player->GetEnergy());
                if ($player->GetLP() != 0)
                {
                    if ($aliveTeam != -2 && $aliveTeam != $player->GetTeam())
                    {
                        $this->AddDebugLog(' -- AliveTeam: ' . $aliveTeam . ' != PlayerTeam: ' . $player->GetTeam() . ' => ALIVE');
                        return -1;
                    }
                    else
                    {
                        $aliveTeam = $player->GetTeam();
                        $this->AddDebugLog(' -- Set AliveTeam: ' . $aliveTeam);
                    }
                }

                ++$j;
            }

            ++$i;
        }

        $this->AddDebugLog(' - AliveTeam: ' . $aliveTeam);

        //if one team is alive only, those lines will be executed
        //so if we are in a survival game, we have to check
        if ($this->GetSurvivalRounds() != 0)
        {
            if ($this->GetSurvivalTeam() == $aliveTeam)
            {
                //The team that should survive survived, so make the winner team win
                $aliveTeam = $this->GetSurvivalWinner();
            }
            else if ($this->GetSurvivalTeam() != $this->GetSurvivalWinner())
            {
                //So here given is: SurvivalTeam (0) != AliveTeam (2)

                //Team 0 is survivalteam and winner, but aliveTeam = 1, so do not execute here, as aliveTeam can stay 1
                //Team 0 is survivalteam, winner is 1 and alive is 1 = aliveTeam = 0
                //Team 1 is survivalteam, winner is 0, alive is 1 = 1
                //Team 1 is survivalteam, winner is 0, but alive = 0, so aliveTeam = 1
                $aliveTeam = $this->GetSurvivalTeam();
            }
        }

        return $aliveTeam;
    }

    private function CalculateAndGetText($attack, $imageLeft)
    {
        $attackImage = $attack[2]->GetImage();
        $calculatedText = $this->CalculateAttack($attack[0], $attack[1], $attack[2]);
        return $this->DisplayAttackText($attackImage, $calculatedText, $imageLeft);
    }

    function DisplayAttackText($attackImage, $text, $imageLeft)
    {
        $attackText = '<tr>';
        if ($imageLeft)
        {
            $attackText .= '<td width=50><img width=50 height=50 src=' . $attackImage . '></td>';
            $attackText .= '<td width=50% align=left>' . $text . '</td>';
            $attackText .= '<td colspan=2></td>';
        }
        else
        {
            $attackText .= '<td colspan=2></td>';
            $attackText .= '<td width=50% align=right>' . $text . '</td>';
            $attackText .= '<td width=50 align=right><img width=50 height=50 src=' . $attackImage . '></td>';
        }
        $attackText .= '</tr>';
        return $attackText;
    }

    private function RemoveNPCSpawns()
    {
        $i = 0;
        $removeFighters = array();
        $removing = false;
        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            while (isset($players[$j]))
            {
                $player = $players[$j];

                if ($player->GetOwner() != 0)
                {
                    $removeFighters[] = $player;
                    $removing = true;
                }
                ++$j;
            }
            ++$i;
        }

        if (!$removing)
        {
            return;
        }

        $i = 0;
        while (isset($removeFighters[$i]))
        {
            $player = $removeFighters[$i];
            $this->RemoveNPCSpawn($player);
            ++$i;
        }
    }

    private function RemoveNPCSpawn($fighter)
    {
        $this->RemoveFighter($fighter->GetID(), true);
        $this->database->Delete('fighters', 'id=' . $fighter->GetID() , 1);
    }

    private function ResetActions()
    {
        $i = 0;
        $removeFighters = array();
        $removing = false;
        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            while (isset($players[$j]))
            {
                $player = $players[$j];

                if ($player->GetLP() == 0 && $player->GetOwner() != 0)
                {
                    $removeFighters[] = $player;
                    $removing = true;
                }

                $paralyzed = $player->GetParalyzed();
                $this->AddDebugLog(' - ResetAction of ' . $player->GetName());
                if ($paralyzed == 0 && $player->GetLoadRounds() == 0)
                {
                    $this->AddDebugLog(' - - Reset!');
                    $player->SetAction(0);
                    $player->SetTarget(0);
                    $this->AddDebugLog( '- - - Setze Target zu 0');
                    $timestamp = date('Y-m-d H:i:s');
                    $player->SetLastAction($timestamp);
                }
                else
                {
                    $this->AddDebugLog(' - - Paralyzed (' . $paralyzed . ') or LoadRound(' . $player->GetLoadRounds() . ')');
                }

                ++$j;
            }
            ++$i;
        }

        if (!$removing)
        {
            return;
        }

        $i = 0;
        while (isset($removeFighters[$i]))
        {
            $player = $removeFighters[$i];
            $this->RemoveNPCSpawn($player);
            ++$i;
        }
    }

    private function CalculateCost($value, $procentual, $ki)
    {
        if ($procentual)
            $cost = ($value / 100) * $ki;
        else
            $cost = $value;
        return round($cost);
    }

    public function SetWinner($value)
    {
        $winner = explode(';', $this->data['winner']);
        if($winner == null)
            $winner = array();
        $winner[] = $value;
        $this->data['winner'] = implode(';', $winner);
    }

    public function GetWinner()
    {
        return $this->data['winner'];
    }

    private function UpdateAllPlayers($wonTeam, $lpkp, $addberry, $addpvp, $addgold, $addstats, $addstory, $addsidestory)
    {
        $this->AddDebugLog('-----------------');
        $this->AddDebugLog('UpdateAllPlayers');
        $itemManager = new ItemManager($this->database);
        $id = 0;
        if ($this->playerAccount != null)
        {
            $id = $this->playerAccount->GetID();
        }
        $PMManager = new PMManager($this->database, $id);
        $i = 0;
        $oneGotItem = false;
        if(!is_null($this->player)) {
            if ($this->player == $this->teams[0][0]) {
                $this->AddDebugLog('UpdateAllPlayers - player: ' . $this->player->GetName());
                $this->database->Update('debuglog="' . $this->GetDebugLog() . '"', 'fights', 'id=' . $this->GetID());
            } else {
                $this->AddDebugLog('UpdateAllPlayers - other player: ' . $this->player->GetName());
                $this->database->Update('debuglog="' . $this->GetDebugLog() . '"', 'fights', 'id=' . $this->GetID());
            }
        }
        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            $tferhalten1 = '';
            $tferhalten2 = '';
            while (isset($players[$j]))
            {
                $fighter = $players[$j];
                $berry = 0;
                $elo = 0;
                if ($fighter->IsNPC() || $fighter->GetFusedAcc() != 0)
                {
                    ++$j;
                    continue;
                }

                if ($fighter->GetTransformations() != '')
                    $this->Revert($fighter, false);

                if ($this->playerAccount != null && $this->playerAccount->GetID() == $fighter->GetAcc())
                {
                    $player = $this->playerAccount;
                }
                else
                {
                    $player = new Player($this->database, $fighter->GetAcc(), $this->actionManager);
                }

                $this->AddDebugLog(' - Update: ' . $player->GetName());

                if ($wonTeam == $i)
                {
                    $this->AddDebugLog(' - - Player won!');
                    $this->SetWinner($player->GetID());
                }

                else
                    $this->AddDebugLog(' - - Player lost!');

                $text = "<table width='100%'>";
                $text = $text . $this->GetText();
                $text = $text . '</table>';

                $player->UpdateFight(0);
                $update = 'fight = 0';
                $inGainAccs = $this->IsInGainAccs($player->GetID());

                if ($this->GetType() == 6 && $wonTeam != $i)
                {
                    $this->AddDebugLog(' - - set Tournament to 0');
                    $update = $update . ', tournament=0';
                    $player->SetTournament(0);
                }
                else if ($this->GetType() == 8) // Kolosseum
                {
                    $elowin = $player->GetEloPoints();
                    if($player->GetDailyArenaPoints() > 0)
                    {
                        $arenaPoints = 5;
                        if ($wonTeam == $i)
                        {
                            if($player->IsDonator())
                            {
                                $elowin = $player->GetEloPoints() + 0;
                                $arenaPoints = 20;
                            }
                            else
                            {
                                $elowin = $player->GetEloPoints() + 0;
                                $arenaPoints = 10;
                            }
                            $this->AddDebugLog("1638 Set Elo-Points from " . $player->GetEloPoints() . " to " . $elowin);
                            $player->SetEloPoints($elowin);
                        }

                        $weekday = date("l");
                        if($weekday == "Friday" || $weekday == "Saturday" || $weekday == "Sunday")
                        {
                            $arenaPoints = $arenaPoints * 2;
                        }

                        if($player->GetDailyArenaPoints() - $arenaPoints < 0)
                            $arenaPoints = $player->GetDailyArenaPoints();

                        $dailyarenapoints = $player->GetDailyArenaPoints() - $arenaPoints;
                        $playerarenapoints = $player->GetArenaPoints() + $arenaPoints;
                        $collectedkolopoints = (($player->GetDailyCollectedKoloPoints() + $arenaPoints) <= 500) ? $player->GetDailyCollectedKoloPoints() + $arenaPoints : 500;
                        $this->AddDebugLog(' - - add Arenapoints: ' . number_format($arenaPoints, '0', '', '.'));
                        $this->AddDebugLog(' - - Arenapoints from: '.number_format($player->GetArenaPoints(), '0', '', '.'));
                        $this->AddDebugLog(' - - Arenapoints to: '.number_format($playerarenapoints, '0', '', '.'));
                        $this->AddDebugLog(' - - DailyArenapoints from: '.number_format($player->GetDailyArenaPoints(), '0', '', '.'));
                        $this->AddDebugLog(' - - DailyArenapoints to: '.number_format($dailyarenapoints, '0', '', '.'));
                        $update = $update . ', arenapoints=' . $playerarenapoints . ', dailyarenapoints = ' . $dailyarenapoints . ', collectedkolopoints = ' . $collectedkolopoints;
                        $player->SetArenaPoints($playerarenapoints);
                        $player->SetDailyArenaPoints($dailyarenapoints);
                        if($player->GetDailyCollectedKoloPoints() < 500)
                            $player->SetDailyCollectedKoloPoints($collectedkolopoints);
                    }
                    $day = date("w");
                    
                    $backcapcha = 0;
                    if(!$player->IsDonator() && $day != '1')
                    {
                        $backcapcha = $player->GetCapchaCount() + rand(1, 2);
                    }
                    else if($player->IsDonator() && $day != '1')
                    {
                        $backcapcha = $player->GetCapchaCount() + 1;
                    }

                    if($backcapcha >= 15)
                    {
                        $captcha = rand(1, 9999999);
                    }
                    else
                    {
                        $captcha = 0;
                    }
                    $update = $update . ', capchacount=' . $backcapcha . ', kolocode = ' . $captcha;
                }
                /*else if($this->GetType() == 4)
                {
                    if($player->GetLP() > 0 && $player->GetKP() > 0)
                    {
                        $player->SetLP(0);
                        $player->SetKP(0);
                    }
                }*/
                else if($this->GetType() == 12) // Schatzsuche
                {
                    if($wonTeam == $i)
                    {
                        $treasurehuntManager = new treasurehuntmanager($this->database);
                        $treasurehuntprogress = $treasurehuntManager->LoadPlayerData($player->GetID());

                        $treasurehuntManager->LevelUp($player->GetID());
                        $this->AddDebugLog(' - - Update Treasurehuntprogress');
                    }
                }
                else if($this->GetType() == 13) // Elo Fight
                {
                    $dailys = $player->GetDailyEloFights() + 1;
                    if($wonTeam == $i && ($dailys <= 5 && $player->GetDailyMaxElofights() == 5 || $player->GetDailyMaxElofights() > 5))
                    {
                        if($player->GetDailyMaxElofights() > 5 && $player->GetDailyEloFights() >= 5)
                        {
                            $player->SetDailyMaxEloFights($player->GetDailyMaxElofights() - 1);
                        }
                        if($dailys == 4 || $dailys == 5)
                        {
                            $player->SetClanRunPoints(1);
                        }
                        $g = 0;
                        $getki = 0;
                        $tki = 0;

                        while(isset($this->teams[$g]))
                        {
                            if($i == $g)
                            {
                                ++$g;
                                continue;
                            }
                            foreach($this->teams[$g] as &$geremi)
                            {
                                $getki += $geremi->GetKI();
                                $tki = $getki - $geremi->GetTitelKI($this->database);
                            }
                            ++$g;
                        }
                        $pki = $fighter->GetKI() - $fighter->GetTitelKI($this->database);
                        $difference = round($pki - $tki);
                        $elowin = 0;
                        if($difference <= 50)
                        {
                            $elowin = $player->GetEloPoints() + 120;
                            $elo = 120;
                            $berry = $player->GetLevel() * 50;
                        }
                        else if($difference <= 100)
                        {
                            $elowin = $player->GetEloPoints() + 110;
                            $elo = 110;
                            $berry = $player->GetLevel() * 40;
                        }
                        else if($difference <= 150)
                        {
                            $elowin = $player->GetEloPoints() + 100;
                            $elo = 100;
                            $berry = $player->GetLevel() * 30;
                        }
                        else if($difference <= 200)
                        {
                            $elowin = $player->GetEloPoints() + 90;
                            $elo = 90;
                            $berry = $player->GetLevel() * 20;
                        }
                        else
                        {
                            $elowin = $player->GetEloPoints() + 80;
                            $elo = 80;
                            $berry = $player->GetLevel() * 10;
                        }

                        if($this->IsMirror() == 1)
                        {
                            $elowin = $elowin - 20;
                            $elo = $elo - 20;
                        }

                        $this->AddDebugLog("1777 Set Elo-Points from " . $player->GetEloPoints() . " to " . $elowin);
                        $player->SetEloPoints($elowin);
                        $this->AddDebugLog(' - - diff ' . number_format($difference, '0', '', '.'));
                        $player->SetDailyEloFights($dailys);
                    }
                    else if($wonTeam != $i && $wonTeam != -2 && ($dailys <= 5 && $player->GetDailyMaxElofights() == 5 || $player->GetDailyMaxElofights() > 5))
                    {
                        if($player->GetDailyMaxElofights() > 5 && $player->GetDailyEloFights() >= 5)
                        {
                            $player->SetDailyMaxEloFights($player->GetDailyMaxElofights() - 1);
                        }
                        if($dailys == 4 || $dailys == 5)
                        {
                            $player->SetClanRunPoints(1);
                        }
                        $g = 0;
                        $getki = 0;
                        $tki = 0;
                        while(isset($this->teams[$g]))
                        {
                            if($i == $g)
                            {
                                ++$g;
                                continue;
                            }
                            foreach($this->teams[$g] as &$geremi)
                            {
                                $getki = $geremi->GetKI();
                                $tki = $getki - $geremi->GetTitelKI($this->database);
                            }
                            ++$g;
                        }
                        $pki = $fighter->GetKI() - $fighter->GetTitelKI($this->database);
                        $difference = round($pki - $tki);
                        $elof = $player->GetDailyEloFights() + 1;
                        if($elof > 10)
                        {
                            $elof = 10;
                        }
                        if($difference <= 50)
                        {
                            $elowin = $player->GetEloPoints() + 70;
                            $elo = 70;
                            $berry = $player->GetLevel() * 25;
                        }
                        else if($difference <= 100)
                        {
                            $elowin = $player->GetEloPoints() + 60;
                            $elo = 60;
                            $berry = $player->GetLevel() * 20;
                        }
                        else if($difference <= 150)
                        {
                            $elowin = $player->GetEloPoints() + 50;
                            $elo = 50;
                            $berry = $player->GetLevel() * 15;
                        }
                        else if($difference <= 200)
                        {
                            $elowin = $player->GetEloPoints() + 40;
                            $elo = 40;
                            $berry = $player->GetLevel() * 10;
                        }
                        else
                        {
                            $elowin = $player->GetEloPoints() + 30;
                            $elo = 30;
                            $berry = $player->GetLevel() * 5;
                        }
                        if($elowin < 0)
                        {
                            $elowin = 0;
                        }

                        if($this->IsMirror() == 1)
                        {
                            $elowin = $elowin - 20;
                            $elo = $elo - 20;
                        }
                        $this->AddDebugLog("1856 Set Elo-Points from " . $player->GetEloPoints() . " to " . $elowin);
                        $player->SetEloPoints($elowin);
                        $this->AddDebugLog(' - - diff ' . number_format($difference, '0', '', '.'));
                        $fplayer = $this->GetFighter($fighter->GetID());
                        if($fplayer->GetKicked() != 1)
                        {
                            $halfAD = round($player->GetMaxKP() / 2);
                            $halfLP = round($player->GetMaxLP() / 2);
                            if($player->GetLP() < $halfLP && $fighter->GetUseKill() == 0)
                            {
                                $player->SetLP($halfLP);
                            }
                            else if($player->GetKP() < $halfAD && $fighter->GetUseKill() == 0)
                            {
                                $player->SetKP($halfAD);
                            }
                            else if($player->GetKP() < $player->GetMaxKP() && $fighter->GetUseKill() == 1)
                            {
                                $player->SetKP(0);
                            }
                            else if($player->GetLP() < $player->GetMaxLP() && $fighter->GetUseKill() == 1)
                            {
                                $player->SetLP(0);
                            }
                        }
                        else
                        {
                            $player->SetLP(0);
                            $player->SetKP(0);
                        }
                        $player->SetDailyEloFights($dailys);
                    }
                    else if($wonTeam == -2 && ($dailys <= 5 && $player->GetDailyMaxElofights() == 5 || $player->GetDailyMaxElofights() > 5))
                    {
                        $halfAD = round($player->GetMaxKP() / 2);
                        $halfLP = round($player->GetMaxLP() / 2);
                        if($player->GetLP() < $halfLP && $fighter->GetUseKill() == 0)
                        {
                            $player->SetLP($halfLP);
                        }
                        else if($player->GetKP() < $halfAD && $fighter->GetUseKill() == 0)
                        {
                            $player->SetKP($halfAD);
                        }
                        else if($player->GetKP() < $player->GetMaxKP() && $fighter->GetUseKill() == 1)
                        {
                            $player->SetKP(0);
                        }
                        else if($player->GetLP() < $player->GetMaxLP() && $fighter->GetUseKill() == 1)
                        {
                            $player->SetLP(0);
                        }
                        //$player->AddMeldung("Elokampf Unentschieden: Spieler <a href='?p=profil&id=".$player->GetID()."'>".$player->GetName()."</a> - KampfID: ".$this->GetID(), $player->GetID(), "System", 2);
                    }
                }
                else if($this->GetType() == 3) // NPC
                {
                    $elowin = $player->GetEloPoints();
                    if($wonTeam != $i && $wonTeam != -2) {
                        if ($player->GetDailyNPCFights() < $player->GetDailyNPCFightsMax())
                            if ($player->IsDonator())
                                $elowin = $player->GetEloPoints() - 0;
                            else
                                $elowin = $player->GetEloPoints() - 0;
                        else
                            $elowin = $player->GetEloPoints() - 0;
                        if($elowin < 0)
                        {
                            $elowin = 0;
                        }
                        if($player->GetDailyNPCFightsMax() > 50 && $player->GetDailyNPCFights() > 50)
                        {
                            if($player->IsDonator())
                            {
                                $player->SetDailyNPCFightsMax($player->GetDailyNPCFightsMax() - 2);
                            }
                            else
                            {
                                $player->SetDailyNPCFightsMax($player->GetDailyNPCFightsMax() - 1);
                            }
                        }
                    }
                    else
                    {
                        $run = $player->GetDailyNPCFights() + 1;
                        if($run == 50 && (!$player->IsDonator()))
                        {
                            $player->SetClanRunPoints(1);
                        }
                        else if($run == 49)
                        {
                            $player->SetClanRunPoints(1);
                        }
                        if ($player->GetDailyNPCFights() < $player->GetDailyNPCFightsMax())
                        {
                            if ($player->IsDonator()) {
                                $elo = 0;
                            } else {
                                $elo = 0;
                            }
                        }
                        if($player->GetDailyNPCFightsMax() > 50 && $player->GetDailyNPCFights() > 50)
                        {
                            if($player->IsDonator())
                            {
                                $player->SetDailyNPCFightsMax($player->GetDailyNPCFightsMax() - 2);
                            }
                            else
                            {
                                $player->SetDailyNPCFightsMax($player->GetDailyNPCFightsMax() - 1);
                            }
                        }
                    }
                    $this->AddDebugLog("1969 Set Elo-Points from " . $player->GetEloPoints() . " to " . $elowin);
                    $player->SetEloPoints($elowin);
                }
                else if($this->GetType() == 5) // Dungeon/Event
                {
                    $elowin = $player->GetEloPoints();
                    $event = new Event($this->database, $this->GetEvent());
                    $elop = 0;
                    if($event->IsDungeon()) {
                        if($player->GetDungeon() < $event->GetWinable()) {
                            if ($wonTeam != $i) {
                                $elowin = $player->GetEloPoints() - 0;
                                $elop = 0;
                                if ($elowin < 0) {
                                    $elowin = 0;
                                }
                            }
                        }
                    }
                    else
                    {
                        if($event->GetPlayerWins(implode(";", $event->GetPlayers()), $player->GetID()) < $event->GetWinable()) {
                            if ($wonTeam != $i) {
                                $elowin = $player->GetEloPoints() - 0;
                                $elop = 0;
                                if ($elowin < 0) {
                                    $elowin = 0;
                                }
                            }
                        }
                    }
                    $this->AddDebugLog("2000 Set Elo-Points from " . $player->GetEloPoints() . " to " . $elowin);
                    $player->SetEloPoints($elowin);
                }


                $dailyFights = $player->GetDailyFights() + 1;
                if ($this->GetType() == 1 && $dailyFights <= 5)
                {
                    $this->AddDebugLog(' - - add dailyfights to: ' . number_format($dailyFights, '0', '', '.'));
                    $update = $update . ', dailyfights=' . $dailyFights ;
                }

                if ($wonTeam == -2)
                {
                    $titleSort = 2;
                    StatsList::AddDraw($this->database, $player->GetID(), $player->GetName(), $this->GetType());
                }
                else if ($wonTeam == $i)
                {
                    $titleSort = 0;
                    StatsList::AddWin($this->database, $player->GetID(), $player->GetName(), $this->GetType());
                }
                else
                {
                    $titleSort = 1;
                    StatsList::AddLoose($this->database, $player->GetID(), $player->GetName(), $this->GetType());
                }

                if($this->GetType() == 13 && $player->GetClan() != 0 && $wonTeam != -2) {
                    $k = 0;
                    while (isset($this->teams[$k]))
                    {
                        if ($i == $k)
                        {
                            ++$k;
                            continue;
                        }
                        foreach ($this->teams[$k] as &$enemy)
                        {
                            /*$clan = new Clan($this->database, $enemy->GetClanID());
                            if (($enemy->GetClanID() == $player->GetClan() || in_array($player->GetClan(), $clan->GetAlliances())) && $wonTeam != -2)
                            {
                                $player->SetClanEloFights($player->GetClanEloFights() + 1);
                            }*/
                            $player->AddDailyEloEnemys($enemy->GetAcc());
                        }
                        ++$k;
                    }
                }

                if ($lpkp)
                {
                    $fLP = $fighter->GetLP();
                    $fKP = $fighter->GetKP();
                    $equippedStats = explode(';', $player->GetEquippedStats());
                    $titelStats = explode(';', $player->GetTitelStats());
                    if ($fLP > $player->GetLP())
                        $fLP = $player->GetLP();
                    if ($fKP > $player->GetKP())
                        $fKP = $player->GetKP();

                    if ($fLP > $player->GetMaxLP())
                        $lpreset = $player->GetMaxLP();
                    else
                        $lpreset = $fLP;

                    if ($fKP > $player->GetMaxKP())
                        $kpreset = $player->GetMaxKP();
                    else
                    {
                        $kpreset = $fKP;
                        if ($kpreset < 0)
                            $kpreset = 0;
                    }
                    $update = $update . ', lp="' . $lpreset . '"';
                    $player->SetLP($lpreset);
                    $update = $update . ', kp="' . $kpreset . '"';
                    $player->SetKP($kpreset);
                }

                $gotSomething = false;

                $stats = 0;
                if ($addstory && $wonTeam == $i && $inGainAccs && $player->GetStory() == $this->GetStory())
                {
                    $story = $player->GetStory() + 1;
                    $player->SetStory($story);
                    $this->AddDebugLog(' - - add story to: ' . number_format($story, '0', '', '.'));
                    $update = $update . ', story=' . $story ;
                    if ($this->GetLevelup())
                    {
                        $level = $player->GetLevel() + 1;
                        $player->SetLevel($level);
                        $this->AddDebugLog(' - - add level to: ' . number_format($level, '0', '', '.'));
                        $update = $update . ', level=' . $level ;
                        $stats = $stats + 25;
                    }
                    $this->getTitelManager()->AddTitelStory($player, $story);
                }
                if ($addsidestory && $wonTeam == $i && $inGainAccs && $player->GetSideStory() == $this->GetSideStory())
                {
                    $sidestory = $player->GetSideStory() + 1;
                    $player->SetSideStory($sidestory);
                    $this->AddDebugLog(' - - add sidestory to: ' . number_format($sidestory, '0', '', '.'));
                    $update = $update . ', sidestory=' . $sidestory ;
                    if ($this->GetLevelup())
                    {
                        $level = $player->GetLevel() + 1;
                        $player->SetLevel($level);
                        $this->AddDebugLog(' - - add level to: ' . number_format($level, '0', '', '.'));
                        $update = $update . ', level=' . $level ;
                        $stats = $stats + 25;
                    }
                    $this->getTitelManager()->AddTitelSideStory($player, $sidestory);
                }

                if ($this->GetBerry() != 0 && ($wonTeam == $i || $wonTeam == -2 && $this->GetType() != 4) && $inGainAccs)
                {
                    $gotSomething = true;
                    if($this->GetType() == 3 && $player->IsDonator())
                        $berry = $berry + $this->GetBerry() * 2;
                    else
                        $berry = $berry + $this->GetBerry();
                }
                $gold = 0;
                if ($this->GetGold() != 0 && ($wonTeam == $i || $wonTeam == -2 && $this->GetType() != 4) && $inGainAccs)
                {
                    $gotSomething = true;
                    $gold = $gold + $this->GetGold();
                }
                $muenzen = 0;
                if ($this->GetMünzen() != 0 && ($wonTeam == $i) && $inGainAccs)
                {
                    $gotSomething = true;
                    $muenzen = $muenzen + $this->GetMünzen();
                }
                if($this->GetType() == 5 && ($wonTeam == $i) && $inGainAccs)
                {
                    $event = new Event($this->database, $this->GetEvent());
                    $day = date("w");
                    if($event->GetID() == 1 && $day = 2)
                    {
                        $chance = 100;
                    }
                    else if($event->IsDungeon() && $day == 6)
                    {
                        $chance = 100;
                    }
                    else
                    {
                        $chance = $event->GetDropChance();
                    }


                    /*if($event->GetID() == 1 && $player->IsDonator())
                        $chance = $chance * 2;*/

                    if ($chance > 100)
                        $chance = 100;

                    $randomChance = rand(0, 100);
                    $this->AddDebugLog(' - - - Item random: ' . $randomChance);
                    $this->AddDebugLog(' - - Item chance: ' . $chance);

                    if ($randomChance <= $chance)
                    {

                        if($event->GetStats() != 0)
                        {
                            $gotSomething = true;
                        }
                    }
                }
                $pvp = 0;
                if ($this->GetPvP() != 0 && ($wonTeam == $i) && $inGainAccs)
                {
                    $gotSomething = true;
                    $pvp = $pvp + $this->GetPvP();
                }

                $kampfWinPM = '';
                if(!$oneGotItem && $this->GetItems() != 0 && ($wonTeam == $i) && $inGainAccs && $this->GetType() == 12)
                {
                    $items = explode(';', $this->GetItems());
                    $wonItems = '';

                    $randItem = rand(0, count($items) - 1);

                    $item = explode('@', $items[$randItem]);
                    $itemID = $item[0];
                    $amount = $item[1];
                    if(rand(1,20) == 1)
                        $amount = intval($amount) + 2;
                    $item = $itemManager->GetItem($itemID);
                    $kampfWinPM = "<img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'>
					Du hast ".number_format($amount, 0, ',', '.')."x " . $item->GetName() . " gewonnen. ";
                    $gotSomething = true;

                    $itemID = $item->GetID();
                    $statstype = 0;
                    $upgrade = 0;
                    $this->AddDebugLog(' - - add Item: ' .number_format($amount, 0, ',', '.').'x ' . $item->GetName());
                    $this->AddDebugLog(' - - - StatsType: ' . $statstype);
                    $player->AddItems($item, $item, $amount, $statstype, $upgrade);
                    $wonItem = $itemID . '@' . $amount . '@' . $statstype;
                    if ($wonItems == '')
                    {
                        $wonItems = $wonItem;
                    }
                    else
                    {
                        $wonItems = $wonItems . ';' . $wonItem;
                    }
                    $player->SetNPCWonItems($wonItems);
                    $player->SetNPCWonItemsType($this->GetType());
                    $player->SetNPCWonItemsDungeon($this->GetType());
                    $update = $update . ',npcwonitems="' . $wonItems . '",npcwonitemtype="' . $this->GetType() . '",npcwonitemdungeon="1"';
                }
                else if (!$oneGotItem && ($this->GetItems() != 0 && ($wonTeam == $i || $wonTeam == -2) && $inGainAccs))
                {

                    if($event != null && $event->IsDungeon() && $player->GetDungeon() >= $event->GetWinable() && $event->GetID() != 29 && $event->GetID() != 30 && $event->GetID() != 31)
                    {

                    }
                    else
                    {

                        $items = explode(';', $this->GetItems());
                        $wonItems = '';

                        $randItem = rand(0, count($items) - 1);

                        $item = explode('@', $items[$randItem]);
                        $itemID = $item[0];
                        $chance = $item[1];

                        /*if($this->GetEvent() == 1 && $player->IsDonator())
                            $chance = $chance * 2;*/

                        if ($chance > 100)
                            $chance = 100;

                        $randomChance = rand(0, 100);
                        $this->AddDebugLog(' - - Item random: ' . $randomChance);
                        $this->AddDebugLog(' - - Item chance: ' . $chance);

                        if ($randomChance <= $chance && $this->GetType() != 12) {
                            #if($this->GetType() == 5)
                            #{
                            #  $oneGotItem = true;
                            #} Wenn nur noch ein Spieler etwas bekommen soll, dann wieder entkommentieren // Sload
                            $item = $itemManager->GetItem($itemID);
                            $kampfWinPM = "<img src='img/items/" . $item->GetImage() . ".png' alt='" . $item->GetName() . "' title='" . $item->GetName() . "' width='80px' height='80px'>
							Du hast 1x " . $item->GetName() . " gewonnen. ";
                            $gotSomething = true;

                            $itemID = $item->GetID();
                            $statstype = 0;

                            $upgrade = 0;
                            $amount = 1;
                            $this->AddDebugLog(' - - add Item: ' . $item->GetName());
                            $this->AddDebugLog(' - - - StatsType: ' . $statstype);

                            $player->AddItems($item, $item, $amount, $statstype, $upgrade);
                            $wonItem = $itemID . '@' . $amount . '@' . $statstype;
                            if ($wonItems == '') {
                                $wonItems = $wonItem;
                            } else {
                                $wonItems = $wonItems . ';' . $wonItem;
                            }

                            $event = new Event($this->database, $this->GetEvent());

                            $player->SetNPCWonItems($wonItems);
                            $player->SetNPCWonItemsType($this->GetType());
                            $player->SetNPCWonItemsDungeon($this->GetType());
                            /*$eloPoints = $player->GetEloPoints();
                                $eloPoints += 4;
                                $this->AddDebugLog("2280 Set Elo-Points from " . $player->GetEloPoints() . " to " . $eloPoints);
                                $player->SetEloPoints($eloPoints);*/
                            /*if ($event->IsDungeon()) {
                                $player->SetDungeon($player->GetDungeon() + 1);
                            }*/
                            $update = $update . ',npcwonitems="' . $wonItems . '",npcwonitemtype="' . $this->GetType() . '",npcwonitemdungeon="' . $event->IsDungeon() . '"';
                        }
                    }

                }
                if (!$addpvp && $addberry && !$addgold && $inGainAccs)
                {
                    $maxBerryFights = 5;

                    $berryRate = rand(2, 4);
                    $berryGain = 200 + (25 * $berryRate);
                    $berryBonus = ($player->GetLevel() *  $berryGain) * 2;

                    if ($dailyFights <= $maxBerryFights)
                    {
                        $this->AddDebugLog(' - - add Bonus Berry: ' . number_format($berryBonus, '0', '', '.'));
                        $berry = $berry + $berryBonus;
                        $gotSomething = true;
                    }
                }
                if ($addpvp && $addberry && $addgold && $inGainAccs)
                {
                    $maxBerryFights = $player->GetPvPMaxFights();
                    if ($wonTeam == $i || $this->GetMembersOfTeam(0) != $this->GetMembersOfTeam(1))
                    {
                        if($player->GetPvPMaxFights() > 5 && $player->GetDailyfights() >= 5)
                        {
                            $player->SetPvPMaxFights($player->GetPvPMaxFights() - 1);
                        }
                        if($dailyFights == 4 || $dailyFights == 5)
                        {
                            $player->SetClanRunPoints(1);
                        }
                        $berryBonus = ($player->GetLevel() * 500);
                        if ($dailyFights <= $maxBerryFights)
                        {
                            if ($player->GetPfad(1) != 'None')
                            {
                                if ($player->GetPfad(1) == "Logia")
                                {
                                    $tf = $itemManager->GetItem(54);
                                    $player->AddItems($tf, $tf, 1, 0, 0);
                                    $tferhalten1 = $tf->GetName();
                                }
                                else if ($player->GetPfad(1) == "Paramecia")
                                {
                                    $tf = $itemManager->GetItem(53);
                                    $player->AddItems($tf, $tf, 1, 0, 0);
                                    $tferhalten1 = $tf->GetName();
                                }
                                else if ($player->GetPfad(1) == "Zoan")
                                {
                                    $tf = $itemManager->GetItem(52);
                                    $player->AddItems($tf, $tf, 1, 0, 0);
                                    $tferhalten1 = $tf->GetName();
                                }
                            }
                            if ($player->GetPfad(2) != 'None')
                            {
                                if ($player->GetPfad(2) == "Schwertkaempfer")
                                {
                                    $tf = $itemManager->GetItem(56);
                                    $player->AddItems($tf, $tf, 1, 0, 0);
                                    $tferhalten2 = $tf->GetName();
                                }
                                else if ($player->GetPfad(2) == "Schwarzfuss")
                                {
                                    $tf = $itemManager->GetItem(57);
                                    $player->AddItems($tf, $tf, 1, 0, 0);
                                    $tferhalten2 = $tf->GetName();
                                }
                                else if ($player->GetPfad(2) == "Karatekämpfer")
                                {
                                    $tf = $itemManager->GetItem(55);
                                    $player->AddItems($tf, $tf, 1, 0, 0);
                                    $tferhalten2 = $tf->GetName();
                                }
                            }
                        }
                        $goldBonus = 10;
                        $pvpBonus = 10;
                        if($player->GetKP() < $player->GetMaxKP() && $fighter->GetUseKill() == 1)
                        {
                            $player->SetKP(0);
                        }
                        else if($player->GetLP() < $player->GetMaxLP() && $fighter->GetUseKill() == 1)
                        {
                            $player->SetLP(0);
                        }
                    }
                    else
                    {
                        if($player->GetPvPMaxFights() > 5 && $player->GetDailyfights() >= 5)
                        {
                            $player->SetPvPMaxFights($player->GetPvPMaxFights() - 1);
                        }
                        $berryBonus = ($player->GetLevel() * 300);

                        if ($dailyFights <= $maxBerryFights)
                        {
                            if($dailyFights == 4 || $dailyFights == 5)
                            {
                                $player->SetClanRunPoints(1);
                            }
                            $rand = rand(1, 100);
                            if ($rand > 50)
                            {
                                if ($player->GetPfad(1) != 'None')
                                {
                                    if ($player->GetPfad(1) == "Logia")
                                    {
                                        $tf = $itemManager->GetItem(54);
                                        $player->AddItems($tf, $tf, 1, 0, 0);
                                        $tferhalten1 = $tf->GetName();
                                    }
                                    else if ($player->GetPfad(1) == "Paramecia")
                                    {
                                        $tf = $itemManager->GetItem(53);
                                        $player->AddItems($tf, $tf, 1, 0, 0);
                                        $tferhalten1 = $tf->GetName();
                                    }
                                    else if ($player->GetPfad(1) == "Zoan")
                                    {
                                        $tf = $itemManager->GetItem(52);
                                        $player->AddItems($tf, $tf, 1, 0, 0);
                                        $tferhalten1 = $tf->GetName();
                                    }
                                }
                                if ($player->GetPfad(2) != 'None')
                                {
                                    if ($player->GetPfad(2) == "Schwertkaempfer")
                                    {
                                        $tf = $itemManager->GetItem(56);
                                        $player->AddItems($tf, $tf, 1, 0, 0);
                                        $tferhalten2 = $tf->GetName();
                                    }
                                    else if ($player->GetPfad(2) == "Schwarzfuss")
                                    {
                                        $tf = $itemManager->GetItem(57);
                                        $player->AddItems($tf, $tf, 1, 0, 0);
                                        $tferhalten2 = $tf->GetName();
                                    }
                                    else if ($player->GetPfad(2) == "Karatekämpfer")
                                    {
                                        $tf = $itemManager->GetItem(55);
                                        $player->AddItems($tf, $tf, 1, 0, 0);
                                        $tferhalten2 = $tf->GetName();
                                    }
                                }
                            }
                        }

                        $goldBonus = 5;
                        $pvpBonus = 5;
                        if($player->GetKP() < $player->GetMaxKP() && $fighter->GetUseKill() == 1)
                        {
                            $player->SetKP(0);
                        }
                        else if($player->GetLP() < $player->GetMaxLP() && $fighter->GetUseKill() == 1)
                        {
                            $player->SetLP(0);
                        }
                    }


                    if ($dailyFights <= $maxBerryFights)
                    {
                        $this->AddDebugLog(' - - add Bonus Gold: ' . number_format($goldBonus, '0', '', '.') . ', Berry: ' . number_format($berryBonus, '0', '', '.'));
                        $berry = $berry + $berryBonus;
                        $gold = $gold + $goldBonus;
                        $pvp = $pvp + $pvpBonus;
                        $gotSomething = true;
                    }
                }

                if ($this->GetEvent() != 29 && $this->GetEvent() != 30 && $this->GetEvent() != 31 && $this->GetEvent() != 11 && $this->GetType() == 5 &&
                    ($gotSomething || $this->GetEvent() == 1)) //Event
                {
                    $event = new Event($this->database, $this->GetEvent());
                    if((($event->GetID() == 18 && $player->GetExtraDungeon(18) < $event->GetWinable() && $player->GetDungeon() < $event->GetWinable() ||
                                $event->GetID() != 18 && $player->GetDungeon() < $event->GetWinable() ||
                                $event->GetID() == 15 && $player->GetExtraDungeon(15) < $event->GetWinable() && $player->GetDungeon() < $event->GetWinable() ||
                                $event->GetID() != 15 && $player->GetDungeon() < $event->GetWinable() ||
                                $event->GetID() == 20 && $player->GetExtraDungeon(20) < $event->GetWinable() && $player->GetDungeon() < $event->GetWinable() ||
                                $event->GetID() != 20 && $player->GetDungeon() < $event->GetWinable()) ||
                            $event->IsDungeon() == 0 || $event->GetID() == 1) &&
                        $event->GetFinishedTimes($player->GetID()) < $event->GetWinable()) {

                        if ($event->GetDecreaseNPCFight()) {
                            $newDailyFights = $player->GetDailyNPCFights() + 1;
                            $this->AddDebugLog(' - - Previous DailyNPCFights: ' . number_format($player->GetDailyNPCFights(), '0', '', '.'));
                            $this->AddDebugLog(' - - Set DailyNPCFights: ' . number_format($newDailyFights, '0', '', '.'));
                            $stats = $event->GetStats();
                            $player->SetDailyNPCFights($newDailyFights);
                            $player->UpdateDailyNPCFights();

                        } else {
                            $this->AddDebugLog(' - - Add to Finished Players');
                            $event->AddFinishedPlayers($player->GetID());
                            $stats = $event->GetStats();
                        }
                        if ($event->GetID() == 18 || $event->GetID() == 15 || $event->GetID() == 20)
                        {
                            $player->AddExtraDungeon($event->GetID());
                        }
                        if ($event->IsDungeon() && $event->GetID() != 23)
                        {
                            $player->SetDungeon($player->GetDungeon() + 1);
                        }
                        if($event->GetID() != 16 && $event->GetID() != 17)
                        {
                            $eloPoints = $player->GetEloPoints() + 0;
                            $this->AddDebugLog("2497 Set Elo-Points from " . $player->GetEloPoints() . " to " . $eloPoints);
                            $player->SetEloPoints($eloPoints);
                        }
                    }
                }

                if ($this->GetEvent() == 11 || (($this->GetEvent() == 29 || $this->GetEvent() == 30 || $this->GetEvent() == 31) && $gotSomething))
                {
                    if(($this->GetEvent() == 29 || $this->GetEvent() == 30 || $this->GetEvent() == 31) && $gotSomething)
                    {
                        $player->SetDungeon($player->GetDungeon() + 1);
                    }
                    $event = new Event($this->database, $this->GetEvent());
                    $this->AddDebugLog(' - - Add to Finished Players');
                    $event->AddFinishedPlayers($player->GetID());
                }

                if($this->GetType() == 11)
                {
                    $place = new Place($this->database, $this->GetPlace(), $this->actionManager);
                    if($wonTeam == $i)
                    {
                        $kampfWinPM = "<img src='img/places/" . $place->GetImage() . ".png' alt='".$place->GetName()."' title='".$place->GetName()."' width='80px' height='80px'>
					    <br/>Ihr habt den Ort: " . $place->GetName() . " gewonnen. ";
                    }
                    else
                    {
                        $kampfWinPM = "<img src='img/places/" . $place->GetImage() . ".png' alt='".$place->GetName()."' title='".$place->GetName()."' width='80px' height='80px'>
					    <br/>Ihr habt den Ort: " . $place->GetName() . " leider verloren. ";
                    }

                }

                if($stats != 0)
                {
                    if ($kampfWinPM != "")
                    {
                        $kampfWinPM = $kampfWinPM . " und ";
                    }
                    else
                    {
                        $kampfWinPM = "Du hast ";
                    }
                    $kampfWinPM = $kampfWinPM . number_format($stats, '0', '', '.') . " Statspunkte";
                }

                if($muenzen != 0)
                {
                    if ($kampfWinPM != "")
                    {
                        $kampfWinPM = $kampfWinPM . " und ";
                    }
                    else
                    {
                        $kampfWinPM = "Du hast ";
                    }
                    $kampfWinPM = $kampfWinPM . number_format($muenzen, '0', '', '.') . " Kürbismünzen";
                }

                if ($berry != 0)
                {
                    if ($kampfWinPM != "")
                    {
                        $kampfWinPM = $kampfWinPM . " und ";
                    }
                    else
                    {
                        $kampfWinPM = "Du hast ";
                    }
                    $kampfWinPM = $kampfWinPM . number_format($berry, '0', '', '.') . " <img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 2px; height: 20px; width: 13px;'/>";
                }

                if ($gold != 0)
                {
                    if ($kampfWinPM != "")
                    {
                        $kampfWinPM = $kampfWinPM . " und ";
                    }
                    else
                    {
                        $kampfWinPM = "Du hast ";
                    }
                    $kampfWinPM = $kampfWinPM . number_format($gold, '0', '', '.') . " <img src='img/offtopic/GoldSymbol.png' alt='Gold' title='Gold' style='position: relative; top: 2px; height: 20px; width: 20px;'/>";
                }

                if ($elo != 0)
                {
                    if($kampfWinPM != "" && !substr($kampfWinPM, -1) == '.')
                    {
                        $kampfWinPM = $kampfWinPM . " und ";
                    }
                    else
                    {
                        $kampfWinPM .= "Du hast ";
                    }
                    $kampfWinPM = $kampfWinPM . number_format($elo, '0', '', '.') . " Elopunkte";
                }

                if ($berry != 0 || $gold != 0 || $muenzen != 0 || $stats != 0 || $elo != 0)
                {
                    $kampfWinPM = $kampfWinPM . " erhalten.";
                }

                if ($tferhalten1 != "" && $tferhalten2 == "")
                {
                    $kampfWinPM = $kampfWinPM . "<br/>Außerdem hast du 1x " . $tferhalten1 . " erhalten.";
                }
                else if ($tferhalten1 != "" && $tferhalten2 != "")
                {
                    $kampfWinPM = $kampfWinPM . "<br/>Außerdem hast du 1x " . $tferhalten1 . " und 1x " . $tferhalten2 . " erhalten.";
                }
                else if ($tferhalten1 == "" && $tferhalten2 != "")
                {
                    $kampfWinPM = $kampfWinPM . "<br/>Außerdem hast du 1x " . $tferhalten2 . " erhalten.";
                }
                if ($pvp > 0)
                {
                    $kampfWinPM = $kampfWinPM . "<br/>Dein Kopfgeld ist um " . number_format($pvp, '0', '', '.') . " gestiegen.";
                }


                if ($kampfWinPM != "")
                {
                    $kampfWinPM = "<center>" . $kampfWinPM . '</center>';
                    $text = $kampfWinPM . "<br/>" . $text;
                }

                if($this->GetType() == 1)
                {
                    if($this->GetTeams()[0][0]->GetAcc() != $player->GetID())
                    {
                        $title = 'PvP-Kampf gegen: <b><a href=\"?p=profil&id='.$this->GetTeams()[0][0]->GetAcc().'&1\">'.$this->GetTeams()[0][0]->GetName().'</a></b>';
                    }
                    else if($this->GetTeams()[1][0]->GetAcc() != $player->GetID())
                    {
                        $title = 'PvP-Kampf gegen: <b><a href=\"?p=profil&id='.$this->GetTeams()[1][0]->GetAcc().'&2\">'.$this->GetTeams()[1][0]->GetName().'</a></b>';
                    }
                }
                else if($this->GetType() == 13)
                {
                    if($this->GetTeams()[0][0]->GetAcc() != $player->GetID())
                    {
                        $title = 'Elokampf gegen: <b><a href=\"?p=profil&id='.$this->GetTeams()[0][0]->GetAcc().'&1\">'.$this->GetTeams()[0][0]->GetName().'</a></b>';
                    }
                    else if($this->GetTeams()[1][0]->GetAcc() != $player->GetID())
                    {
                        $title = 'Elokampf gegen: <b><a href=\"?p=profil&id='.$this->GetTeams()[1][0]->GetAcc().'&2\">'.$this->GetTeams()[1][0]->GetName().'</a></b>';
                    }
                }
                else if($this->GetType() == 8)
                {
                    if($this->GetTeams()[1][0]->GetAcc() != -1)
                        $title = 'Kolosseum: <b><a href=\"?p=profil&id='.$this->GetTeams()[0][0]->GetAcc().'\">'.$this->GetTeams()[0][0]->GetName().'</a></b> vs <b><a href=\"?p=profil&id='.$this->GetTeams()[1][0]->GetAcc().'\">'.$this->GetTeams()[1][0]->GetName().'</a></b>';
                    else
                        $title = 'Kolosseum: <b><a href=\"?p=profil&id='.$this->GetTeams()[0][0]->GetAcc().'\">'.$this->GetTeams()[0][0]->GetName().'</a></b> vs <b>'.$this->GetTeams()[1][0]->GetName().'</b>';
                }
                else
                    $title = 'Kampf: ' . $this->GetName();

                $PMManager->SendPM(0, 'img/battel2.png', 'SYSTEM', $title, $text, $player->GetName(), 1);

                if ($berry != 0)
                {
                    $this->AddDebugLog(' - - add Berry: ' . number_format($berry, '0', '', '.'));
                    $berry = $player->GetBerry() + $berry;
                    $this->AddDebugLog(' - - - Berry now: ' . number_format($berry, '0', '', '.'));
                    $player->SetBerry($berry);
                    $update = $update . ', zeni=' . $berry ;
                }
                if ($pvp != 0)
                {
                    $this->AddDebugLog(' - - add Kopfgeld: ' . number_format($pvp, '0', '', '.'));
                    $pvp = $player->GetPvP() + $pvp;
                    $this->AddDebugLog(' - - - Kopfgeld now: ' . number_format($pvp, '0', '', '.'));
                    $player->SetPvP($pvp);
                    $update = $update . ', kopfgeld=' . $pvp ;
                }
                if ($gold != 0)
                {
                    $this->AddDebugLog(' - - add Gold: ' . number_format($gold, '0', '', '.'));
                    $gold = $player->GetGold() + $gold;
                    $this->AddDebugLog(' - - - Gold now: ' . number_format($gold, '0', '', '.'));
                    $player->SetGold($gold);
                    $update = $update . ', gold=' . $gold ;
                }
                if ($muenzen != 0)
                {
                    $this->AddDebugLog(' - - add Münzen: ' . number_format($muenzen, '0', '', '.'));
                    $muenzen = $player->GetMünzen() + $muenzen;
                    $this->AddDebugLog(' - - - Münzen now: ' . number_format($muenzen, '0', '', '.'));
                    $player->SetMünzen($muenzen);
                    $update = $update . ', münzen=' . $muenzen ;
                }

                $fightsTillStats = 10;
                $statsBonus = 10;
                if ($addstats && $inGainAccs)
                {
                    $maxStatsFights = $player->GetMaxStatsFights();
                    $totalStatsFights = $player->GetTotalStatsFights() + 1;
                    if ($totalStatsFights <= $maxStatsFights)
                    {
                        $this->AddDebugLog(' - - Previous Statsfight: ' . number_format($player->GetTotalStatsFights(), '0', '', '.'));
                        $this->AddDebugLog(' - - Set Statsfight: ' . number_format($totalStatsFights, '0', '', '.'));
                        $player->SetTotalDailyFights($totalStatsFights);
                        $update = $update . ', totalstatsfights=' . $totalStatsFights ;

                        if ($totalStatsFights % $fightsTillStats == 0)
                        {
                            $stats = $stats + $statsBonus;
                        }
                    }
                }

                if ($stats != 0)
                {
                    $update = $update . ', statspopup=1';
                    $player->SetStatsPopup(true);
                    $this->AddDebugLog(' - - Add Stats: ' . $stats);
                    $stats = $stats + $player->GetStats();
                    $this->AddDebugLog(' - - Stats now: ' . $stats);
                    $player->SetStats($stats);
                    $update = $update . ', stats=' . $stats ;
                }

                if ($this->GetType() != 0) // NO Spaß
                {
                    $k = 0;
                    while (isset($this->teams[$k]))
                    {
                        if ($i == $k)
                        {
                            ++$k;
                            continue;
                        }
                        foreach ($this->teams[$k] as &$titleFighter)
                        {
                            if (!$titleFighter->IsNPC())
                                continue;

                            $this->getTitelManager()->AddTitelNPC($player, 1, $titleFighter->GetNPC(), $this->GetType(), $titleSort);
                        }
                        ++$k;
                    }
                }

                $this->getTitelManager()->AddTitelFight($player, 1, $this->GetType(), $titleSort);

                $this->database->Update($update, 'accounts', 'id = ' . $player->GetID() , 1);
                $this->AddDebugLog($update . ' für ' . $player->GetName());
                ++$j;
            }

            if($this->GetType() == 11)
            {
                $bande = new Clan($this->database, $players[0]->GetClanID());
                if($wonTeam == $i)
                {
                    $winner = array();
                    foreach ($players as $member) {
                        $winner[] = $member->GetAcc();
                    }
                    $winnerS = implode(';', $winner);
                    $result = $this->database->Select('*', 'places', 'id="'.$this->GetPlace().'"', 1);
                    if($result)
                    {
                        $row = $result->fetch_assoc();
                        if($row['territorium'] != $bande->GetID())
                            $this->database->Update('territorium=' . $bande->GetID() . ', time=NOW(), sieger="' . $winnerS . '", gewinn=0, lastfight=NOW(), blocked='.$row['territorium'], 'places', 'id="' . $this->GetPlace() . '"');
                        else
                            $this->database->Update('territorium=' . $bande->GetID() . ', sieger="' . $winnerS . '", lastfight=NOW()', 'places', 'id="' . $this->GetPlace() . '"');
                    }
                }
                $this->database->Update('challengefight=0, challengedtime="'.date('Y-m-d H:i:s').'"', 'clans', 'challengefight='.$this->GetID());
                $this->database->Update('challengedpopup=0', 'accounts', 'clan='.$this->GetChallenge());
            }
            ++$i;
        }

        $debuglog = $this->GetDebugLog();
        $this->database->Update('debuglog="' . $debuglog . '", winner="'.$this->GetWinner().'"', 'fights', 'id = ' . $this->GetID(), 1);
    }

    private function UpdatePlayersData($wonTeam)
    {
        $lpkp = false;
        $addberry = false;
        $addgold = false;
        $addpvp = false;
        $addstats = false;
        $addstory = false;
        $addsidestory = false;
        $addmünzen = false;
        $npcDifficulty = 0;
        $nextFight = false;
        $survivalRounds = 0;
        $survivalWinner = 0;
        $eventID = 0;
        $isHealing = 0;
        $nextFightID = 0;
        $healthRatio =  100;
        $healthRatioTeam =  0;
        $healthRatioWinner =  0;
        $survivalTeam = 0;
        $npcs = array();

        if ($this->GetType() == 1) //PvP
        {
            $lpkp = true;
            $addberry = true;
            $addgold = true;
            $addpvp = true;
            $addstats = true;
        }
        else if ($this->GetType() == 3) //NPC
        {
            $lpkp = true;
        }
        else if ($this->GetType() == 4) //Story
        {
            $addstory = true;
            $lpkp = true;
        }
        else if ($this->GetType() == 5) //Event
        {
            $event = new Event($this->database, $this->GetEvent());
            $eventFight = $event->GetFight($this->GetEventFight() + 1);
            $heal = $this->IsHealing();
            $lpkp = !$heal;
            $npcDifficulty = $event->GetDifficulty() / 100;
            if ($eventFight != null)
            {
                $nextFight = true;
                $survivalRounds = $eventFight->GetSurvivalRounds();
                $survivalWinner = $eventFight->GetSurvivalWinner();
                $eventID = $event->GetID();
                $isHealing = $eventFight->IsHealing();
                $nextFightID = $this->GetEventFight() + 1;
                $healthRatio =  $eventFight->GetHealthRatio();
                $healthRatioTeam =  $eventFight->GetHealthRatioTeam();
                $healthRatioWinner =  $eventFight->GetHealthRatioWinner();
                $survivalTeam = $eventFight->GetSurvivalTeam();
                $npcs = $eventFight->GetNPCs();
                $newNpc = new NPC($this->database, $event->GetFight($this->GetEventFight())->GetNPCs()[0]);
                $muenzen = $newNpc->GetMünzen();
                $this->SetMünzen($muenzen);
            }
            //End
            if ($eventFight == null && $wonTeam == 0)
            {
                $this->SetBerry($event->GetBerry());
                $eventFight2 = $event->GetFight($this->GetEventFight());
                $npcs2 = $eventFight2->GetNPCs();
                $newNpc = new NPC($this->database, $npcs2[0]);
                $muenzen = $newNpc->GetMünzen();
                $this->SetMünzen($muenzen);
                $chance = rand(0, 100);
                $items = explode(';', $event->GetItem());
                $i = 0;
                $itemArray = array();
                while (isset($items[$i]))
                {
                    $item = array();
                    $item[0] = $items[$i];
                    if($event->GetID() == 20)
                        $item[1] = 1;
                    else
                        $item[1] = $event->GetDropChance();
                    $itemArray[] = implode('@', $item);
                    ++$i;
                }
                $setItem = implode(';', $itemArray);
                $this->SetItems($setItem);
            }
        }
        else if ($this->GetType() == 6) //Tournament
        {
            $tournamentManager = new TournamentManager($this->database, $this->GetPlace(), $this->GetPlanet());
            $tournament = $tournamentManager->GetTournamentByID($this->GetTournament());
            $pFighter = null;
            $pAlive = false;
            $pRound = 0;
            $pCell = 0;
            if ($tournament != null && $tournament->GetPlayerInfo($this->playerAccount, $pFighter, $pAlive, $pRound, $pCell))
            {
                if ($this->player->GetTeam() == $wonTeam)
                {
                    $tournament->MoveToNextBracket($pCell);
                    $this->AddDebugLog("2885 Set Elo-Points from " . $this->player->GetEloPoints() . " to " . $tournament->GetEloPoints());
                    $this->player->SetEloPoints($tournament->GetEloPoints());
                }
                else if ($pCell % 2 == 0)
                {
                    $tournament->MoveToNextBracket($pCell + 1);
                }
                else
                {
                    $tournament->MoveToNextBracket($pCell - 1);
                }
                $tournament->UpdateBrackets();
            }
        }
        else if ($this->GetType() == 8) //Arena
        {
            $where = '';

            for ($i = 0; $i < count($this->teams); ++$i)
            {
                $team = $this->teams[$i];
                for ($j = 0; $j < count($team); ++$j)
                {
                    $teamPlayer = $team[$j];
                    $whereString = 'fighter=' . $teamPlayer->GetAcc() ;
                    if ($where == '')
                    {
                        $where = '(' . $whereString;
                    }
                    else
                    {
                        $where = $where . ' OR ' . $whereString;
                    }
                }
            }

            $where = $where . ')';
            $this->database->Update('infight=0', 'arenafighter', $where, 2);
        }
        else if ($this->GetType() == 10) //SideStory
        {
            $addsidestory = true;
            $lpkp = true;
        }
        else if ($this->GetType() == 12) //Schatzsuche
        {
            $lpkp = true;
        }
        else if($this->GetType() == 13) // Elo
        {
            $lpkp = true;
            /*$where = '';

            for ($i = 0; $i < count($this->teams); ++$i)
            {
                $team = $this->teams[$i];
                for ($j = 0; $j < count($team); ++$j)
                {
                    $teamPlayer = $team[$j];
                    $whereString = 'fighter=' . $teamPlayer->GetAcc() ;
                    if ($where == '')
                    {
                        $where = '(' . $whereString;
                    }
                    else
                    {
                        $where = $where . ' OR ' . $whereString;
                    }
                }
            }

            $where = $where . ')';
            $this->database->Update('infight=0', 'eloarena', $where, 2);*/
        }

        $this->UpdateAllPlayers($wonTeam, $lpkp, $addberry, $addpvp, $addgold, $addstats, $addstory, $addsidestory);

        if ($nextFight && $wonTeam == 0)
        {
            $players = count($this->teams[0]);
            $type = $this->GetType();
            $mode = $players . 'vs' . count($npcs);
            $name = $this->GetName();
            $tournament = 0;
            $npcid = 0;
            $difficulty = 0;
            $createdFight = Fight::CreateFight(
                $this->playerAccount,
                $this->database,
                $type,
                $name,
                $mode,
                0,
                $this->actionManager,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                $survivalTeam,
                $survivalRounds,
                $survivalWinner,
                $eventID,
                $isHealing,
                $nextFightID,
                $tournament,
                $npcid,
                $difficulty,
                $healthRatio,
                $healthRatioTeam,
                $healthRatioWinner
            );

            $createdFight->UpdateGainAcc($this->GetGainAccs());

            $i = 0;
            $team = 1;
            $addedDifficulty = $npcDifficulty;
            $npcDifficulty = round(1 * $addedDifficulty);
            while ($i != count($npcs))
            {
                $npc = new NPC($this->database, $npcs[$i], $npcDifficulty);
                $createdFight->Join($npc, $team, true);
                ++$i;
            }

            $createdFight->Join($this->playerAccount, 0, false);
            $i = 0;
            $group = $this->playerAccount->GetGroup();
            while (isset($group[$i]))
            {
                if ($group[$i] != $this->playerAccount->GetID())
                {
                    $groupPlayer = new Player($this->database, $group[$i], $this->actionManager);
                    $createdFight->Join($groupPlayer, 0, false);
                }
                ++$i;
            }

            header('Location: ?p=infight');
        }
    }

    private function CalculateRound()
    {
        $this->AddDebugLog('----------------------------');
        $this->AddDebugLog(' ');
        $this->AddDebugLog('New Round ' . number_format($this->GetRound(), '0', '', '.'));
        //presort attacks
        $this->defenseArray = array();

        $this->buffAttacks = array();
        $this->preAttacks = array();
        $this->attacks = array();
        $this->postAttacks = array();
        $i = 0;

        $teamAttacks = array();
        $singleAttacks = array();

        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            while (isset($players[$j]))
            {
                $player = $players[$j];
                if ($player->GetAction() == 0)
                {
                    ++$j;
                    continue;
                }

                $attack = $this->GetAttack($player->GetAction());
                $target = $this->GetFighter($player->GetTarget());
                //Hotfix wenn target kaputt ist
                if ($target == null)
                {
                    $target = $player;
                }

                $attackEntry = array();
                $attackEntry[0] = $player;
                $attackEntry[1] = $target;
                $attackEntry[2] = $attack;

                if ($attack->GetType() == 1) // Damage
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 2) // Defense
                {
                    $this->preAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 3) // Tod
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 4) // Transformation
                {
                    $this->preAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 5) // Heal
                {
                    $this->preAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 6) // Load-Damage
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 7) // Load
                {
                    $this->preAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 8) // NPC-Spawn
                {
                    $this->postAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 9) // Paralyze
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 10) // Paralyzed
                {
                    $this->postAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 11) // Self-Heal
                {
                    $this->preAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 12) // Heal-Attack
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 13) // Fusion
                {
                    $this->postAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 14) // Wetter
                {
                    $this->postAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 15) // Sonstiges
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 16) // Aufgeben
                {
                    $this->postAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 17) // Beleben
                {
                    $this->postAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 18) // Buffs
                {
                    $this->buffAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 19) // UnParalyze
                {
                    $this->preAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 20) // Reflect
                {
                    $this->preAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 21) // Selbstbuff
                {
                    $this->buffAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 22) // Dots
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 23) // % Damage
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 24) // % Tech Damage
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 25)
                {
                    $this->buffAttacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 26) // AOE
                {
                    $this->attacks[] = $attackEntry;
                }
                else if ($attack->GetType() == 27) // % AOE
                {
                    $this->attacks[] = $attackEntry;
                }
                ++$j;
            }
            ++$i;
        }


        $i = 0;
        $roundText = '<tr><td align=center colspan=4><h2>Runde ' . number_format($this->GetRound(), '0', '', '.') . '</h2></td></tr>';
        $imageLeft = true;
        $attackTexts = '';

        foreach ($singleAttacks as &$singleAttack)
        {
            $rPlayer = $singleAttack[0];
            $rAttack = $this->GetAttack($singleAttack[1]);
            $rText = '';
            $rText = $this->ReplaceTextValues($rText, $rPlayer, null, 0, '');
            $rText = $this->DisplayAttackText($rAttack->GetImage(), $rText, $imageLeft);
            $attackTexts = $attackTexts . $rText;
            $imageLeft = !$imageLeft;
        }

        foreach ($teamAttacks as &$teamAttack)
        {
            $rPlayer = $teamAttack[0];
            $rAttack = $this->GetAttack($teamAttack[1]);
            $rText = '';
            $rText = $this->ReplaceTextValues($rText, $rPlayer, null, 0, '');
            $rText = $this->DisplayAttackText($rAttack->GetImage(), $rText, $imageLeft);
            $attackTexts = $attackTexts . $rText;
            $imageLeft = !$imageLeft;
        }

        $teamTaunts = array();

        //do Taunt
        for ($ti = 0; $ti < count($this->teams); ++$ti)
        {
            $team = &$this->teams[$ti];
            for ($i = 0; $i < count($team); ++$i)
            {
                $iFighter = &$team[$i];
                if ($iFighter->GetLP() == 0 || $iFighter->GetTaunt() == 0)
                    continue;

                $this->AddDebugLog('Taunt of ' . $iFighter->GetName() . ' is ' . number_format($iFighter->GetTaunt(), '0', '', '.'));

                $newTaunt = true;
                if (isset($teamTaunts[$ti]))
                {
                    if ($teamTaunts[$ti]->GetTaunt() > $iFighter->GetTaunt())
                        $newTaunt = false;
                }

                if ($newTaunt)
                {
                    $this->AddDebugLog('Set Taunt of Team ' . $ti . ' to ' . $iFighter->GetName());
                    $teamTaunts[$ti] = $iFighter;
                }
            }
        }

        $i = 0;
        while (isset($this->buffAttacks[$i]))
        {
            $attack = $this->buffAttacks[$i];

            $attackFighter = $attack[0];

            $attackText = $this->CalculateAndGetText($attack, $imageLeft);

            if ($attackFighter->GetTaunt() != 0)
            {
                $this->AddDebugLog($attackFighter->GetName() . ' Taunt is: ' . number_format($attackFighter->GetTaunt(), '0', '', '.'));
                $attackTeam = $attackFighter->GetTeam();
                $newTaunt = true;
                if (isset($teamTaunts[$attackTeam]))
                {
                    if ($teamTaunts[$attackTeam]->GetTaunt() > $attackFighter->GetTaunt())
                        $newTaunt = false;
                }

                if ($newTaunt)
                {
                    $this->AddDebugLog('Set Taunt of Team ' . $attackTeam . ' to ' . $attackFighter->GetName());
                    $teamTaunts[$attackTeam] = $attackFighter;
                }
            }

            $imageLeft = !$imageLeft;
            $attackTexts = $attackTexts . $attackText;
            ++$i;
        }

        $i = 0;
        while (isset($this->preAttacks[$i]))
        {
            $attack = $this->preAttacks[$i];

            $attackFighter = $attack[0];

            $attackText = $this->CalculateAndGetText($attack, $imageLeft);

            if ($attackFighter->GetTaunt() != 0)
            {
                $this->AddDebugLog($attackFighter->GetName() . ' Taunt is: ' . number_format($attackFighter->GetTaunt(), '0', '', '.'));
                $attackTeam = $attackFighter->GetTeam();
                $newTaunt = true;
                if (isset($teamTaunts[$attackTeam]))
                {
                    if ($teamTaunts[$attackTeam]->GetTaunt() > $attackFighter->GetTaunt())
                        $newTaunt = false;
                }

                if ($newTaunt)
                {
                    $this->AddDebugLog('Set Taunt of Team ' . $attackTeam . ' to ' . $attackFighter->GetName());
                    $teamTaunts[$attackTeam] = $attackFighter;
                }
            }

            $imageLeft = !$imageLeft;
            $attackTexts = $attackTexts . $attackText;
            ++$i;
        }

        $i = 0;
        while (isset($this->attacks[$i]))
        {
            $attack = $this->attacks[$i];

            $enemyPlayer = $attack[0];
            $enemyTarget = $attack[1];

            if (isset($teamTaunts[$enemyTarget->GetTeam()]))
            {
                $tauntPlayer = &$teamTaunts[$enemyTarget->GetTeam()];
                $this->AddDebugLog('Taunt exists for Team ' . $enemyTarget->GetTeam() . ' which is ' . $tauntPlayer->GetName());
                $miss = rand(0, 100);
                $hit = ($enemyPlayer->GetAccuracy() / $tauntPlayer->GetReflex()) * 100;
                if ($hit >= $miss)
                {
                    $this->AddDebugLog('Changing Target of ' . $enemyPlayer->GetName() . ' to ' . $tauntPlayer->GetName());
                    $attack[1] = $tauntPlayer;
                }
            }

            $attackText = $this->CalculateAndGetText($attack, $imageLeft);
            $imageLeft = !$imageLeft;
            $attackTexts = $attackTexts . $attackText;
            ++$i;
        }

        $i = 0;
        while (isset($this->postAttacks[$i]))
        {
            $attack = $this->postAttacks[$i];
            $attackText = $this->CalculateAndGetText($attack, $imageLeft);
            $imageLeft = !$imageLeft;
            $attackTexts = $attackTexts . $attackText;
            ++$i;
        }

        //Do DOTS DMG
        for ($ti = 0; $ti < count($this->teams); ++$ti)
        {
            $team = &$this->teams[$ti];
            for ($i = 0; $i < count($team); ++$i)
            {
                $iFighter = &$team[$i];
                if ($iFighter->GetLP() == 0 || $iFighter->GetDots() == '')
                    continue;

                $attackText = $this->DoDotsDmg($iFighter, $imageLeft);
                $imageLeft = !$imageLeft;
                $attackTexts = $attackTexts . $attackText;
            }
        }

        $i = 0;
        while (isset($this->defenseArray[$i]))
        {
            $attackData = $this->defenseArray[$i];
            $attackPlayer = $attackData[0];
            $attack = $attackData[1];

            $defenseValue = $attackPlayer->GetDefense();
            $reflectValue = $attackPlayer->GetReflect();

            if ($attack->GetDefValue() != 0)
                $defenseValue = $attackPlayer->GetDefense() / ($attack->GetValue() * $attack->GetDefValue() / 100);
            if ($attack->GetReflectValue() != 0)
                $reflectValue = $attackPlayer->GetReflect() - ($attack->GetValue() * $attack->GetReflectValue() / 100);

            $attackPlayer->SetDefense($defenseValue);
            $attackPlayer->SetReflect($reflectValue);
            ++$i;
        }

        //revert all buffs
        foreach ($this->teams as &$team)
        {
            for ($i = 0; $i < count($team); ++$i)
            {
                $fighter = &$team[$i];
                if ($fighter->GetLP() != 0 && $fighter->GetBuffs() != '')
                {
                    $this->DoBuffCost($fighter);
                }
                if ($fighter->GetLP() != 0 && $fighter->GetDebuffs() != '')
                {
                    $this->DoDebuffCost($fighter);
                }
                if ($fighter->GetLP() != 0 && $fighter->GetDots() != '')
                {
                    $this->DoDotsCost($fighter);
                }
                if ($fighter->GetLP() == 0 && $fighter->GetTransformations() != '')
                {
                    $this->Revert($fighter);
                }
            }
        }

        $this->RemoveFusions();
        $wonTeam = $this->CheckForWonTeam();

        if ($wonTeam != -1)
        {
            $this->AddDebugLog(' A Team won: ' . $wonTeam);
            if ($wonTeam == -2)
            {
                $wonTeamText = 'Kein Team hat gewonnen.';
            }
            else
            {
                $wonTeamText = '<b><span style="color: ' . $this->GetTeamColor($wonTeam) . '">Team ' . ($wonTeam + 1) . '</span></b> hat den Kampf gewonnen!';
            }
            $roundText = $roundText . '<tr><td align=center colspan=4>' . $wonTeamText . '</td></tr>';
        }
        $roundText = $roundText . $attackTexts;

        $newText = $this->database->EscapeString($roundText . $this->GetText());
        $this->SetText($newText);

        if ($wonTeam != -1)
        {
            $this->AddDebugLog('End Fight');
            $this->RemoveFusions(true);
            if($this->GetState() != 2)
            {
                $this->SetState(2);
                $this->database->Update('state=2', 'fight', 'id=' . $this->GetID(), 1);
                $this->UpdatePlayersData($wonTeam);
            }

            $this->RemoveNPCSpawns();

            $this->database->Delete('fighters', 'fight=' . $this->GetID(), 999);
        }
        else
        {
            $this->database->Update('action=0, target=0, lastaction="' . date('Y-m-d H:i:s') . '"', 'fighters', 'fight=' . $this->GetID() . ' AND paralyzed=0 AND loadrounds=0', 999);
            $this->ResetActions();
            $this->SetRound($this->GetRound() + 1);
        }

        $this->AddDebugLog(' Round End ');
        $this->AddDebugLog('----------------------------');
        $this->AddDebugLog(' ');

        $this->database->Update('text="' . $newText . '",round=' . $this->GetRound() . ',state=' . $this->GetState() , 'fights', 'id = ' . $this->GetID() , 1);

    }

    private function RemoveFusions($force = false)
    {
        $deletes = array();

        $inactives = array();
        $inactivesID = array();
        $i = 0;
        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            while (isset($players[$j]))
            {
                $player = $players[$j];
                if (!$player->IsInactive())
                {
                    ++$j;
                    continue;
                }
                $inactives[] = $player;
                $inactivesID[] = $player->GetAcc();
                ++$j;
            }
            ++$i;
        }

        $i = 0;
        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            while (isset($players[$j]))
            {
                $player = $players[$j];

                if ($player->GetFusedAcc() != 0 && !$force && $player->GetLP() != 0 && $player->GetFuseTimer() != 0)
                {
                    $fuseTimer = $player->GetFuseTimer();
                    if ($fuseTimer > 0)
                    {
                        $fuseTimer = $fuseTimer - 1;
                        $player->SetFuseTimer($fuseTimer);
                        $this->database->Update('fusetimer="' . $fuseTimer . '"', 'fighters', 'id=' . $player->GetID() , 1);
                    }

                    $searchID = array_search($player->GetAcc(), $inactivesID);
                    array_splice($inactivesID, $searchID, 1);
                    array_splice($inactives, $searchID, 1);

                    $searchID = array_search($player->GetFusedAcc(), $inactivesID);
                    array_splice($inactivesID, $searchID, 1);
                    array_splice($inactives, $searchID, 1);
                    ++$j;
                    continue;
                }
                else if ($player->GetFusedAcc() == 0)
                {
                    ++$j;
                    continue;
                }

                $deletes[] = $player;

                ++$j;
            }
            ++$i;
        }
        if (count($deletes) == 0)
        {
            return;
        }


        $lpPercentage = 0;
        $kpPercentage = 0;
        $deleteWhere = '';
        for ($i = 0; $i < count($deletes); ++$i)
        {
            $deleteID = $deletes[$i]->GetID();
            $deleteStr = 'id=' . $deleteID ;
            if ($deleteWhere == '')
            {
                $deleteWhere = $deleteStr;
            }
            else
            {
                $deleteWhere = $deleteWhere . ' OR ' . $deleteStr;
            }
            $lpPercentage = $deletes[$i]->GetLPPercentage();
            $kpPercentage = $deletes[$i]->GetKPPercentage();
            $this->RemoveFighter($deleteID, true);
        }

        $lpPercentage = ($lpPercentage / 100);
        $kpPercentage = ($kpPercentage / 100);

        for ($i = 0; $i < count($inactives); ++$i)
        {
            $inactives[$i]->SetInactive(false);
            $lp = $inactives[$i]->GetMaxLP() * $lpPercentage;
            $kp = $inactives[$i]->GetMaxKP() * $kpPercentage;
            $inactives[$i]->SetLP($lp);
            $inactives[$i]->SetKP($kp);
            $this->database->Update('inactive=0,lp=' . $lp . ',kp=' . $kp , 'fighters', 'id=' . $inactives[$i]->GetID() , 1000);
        }

        $this->database->Delete('fighters', $deleteWhere, count($deletes));
    }

    private function TeamHasLoadAttack($player, $loadAttack)
    {
        $team = $player->GetTeam();
        $j = 0;
        $teamMembers = $this->teams[$team];
        while (isset($teamMembers[$j]))
        {
            $teamMember = $teamMembers[$j];
            if ($player->GetID() != $teamMember->GetID() && $teamMember->GetLoadAttack() == $loadAttack)
            {
                return true;
            }
            ++$j;
        }
        return false;
    }

    private function DoDotsDmg(&$fighter, $imageLeft)
    {
        //Berechne die Kosten der VWs
        $dots = explode(';', $fighter->GetDots());
        foreach ($dots as &$dot)
        {
            $dotData = explode('@', $dot);
            $dotUser = $dotData[0];
            $dotID = $dotData[1];
            $dotRound = $dotData[2];

            $dotAtk = $this->GetAttack($dotID);
            $dotCaster = $this->GetFighter($dotUser);

            if ($dotAtk->IsProcentual())
            {
                $atkVal = $dotCaster->GetKI() * ($dotAtk->GetValue() / 100);
                if (abs($atkVal) < abs($dotAtk->GetMinValue()))
                {
                    $atkVal = $dotAtk->GetMinValue();
                }
            }
            else
            {
                $atkVal = $dotAtk->GetValue();
            }

            $damage = 0;
            $lpDamage = round($atkVal * ($dotAtk->GetLPValue() / 100)); //TODO: Recalc
            $newLP = $fighter->GetLP() + $lpDamage;
            $damage += $lpDamage;
            if ($newLP < 0)
                $newLP = 0;
            else if ($fighter->GetLP() > $fighter->GetIncreasedLP() && $lpDamage > 0)
                $newLP = $fighter->GetLP();
            else if ($newLP > $fighter->GetIncreasedLP())
                $newLP = $fighter->GetIncreasedLP();

            $kpDamage = round($atkVal * ($dotAtk->GetKPValue() / 100));
            $newKP = $fighter->GetKP() + $kpDamage;
            $damage += $kpDamage;
            if ($newKP < 0)
                $newKP = 0;
            else if ($fighter->GetKP() > $fighter->GetIncreasedKP() && $kpDamage > 0)
                $newKP = $fighter->GetKP();
            else if ($newKP > $fighter->GetIncreasedKP())
                $newKP = $fighter->GetIncreasedKP();

            $epDamage = round($atkVal * ($dotAtk->GetEPValue() / 100));
            $newEP = $fighter->GetEnergy() + $epDamage;
            $damage += $epDamage;
            if ($newEP < 0)
                $newEP = 0;
            else if ($fighter->GetEnergy() > $fighter->GetMaxEnergy() && $epDamage > 0)
                $newEP = $fighter->GetEnergy();
            else if ($newEP > $fighter->GetMaxEnergy())
                $newEP = $fighter->GetMaxEnergy();

            $fighter->SetLP($newLP);
            $fighter->SetKP($newKP);
            $fighter->SetEnergy($newEP);
            $this->database->Update('lp=' . $newLP . ',kp=' . $newKP . ',energy=' . $newEP , 'fighters', 'id = ' . $fighter->GetID() , 1);

            $type = '';
            $attackImage = $dotAtk->GetImage();
            $calculatedText = $dotAtk->GetLoadText();
            if ($damage < 0)
                $damage = -$damage;
            $calculatedText = $this->ReplaceTextValues($calculatedText, $dotCaster, $fighter, $damage, $type);
            return $this->DisplayAttackText($attackImage, $calculatedText, $imageLeft);
        }
        return '';
    }

    private function DoDotsCost(&$fighter)
    {
        //Berechne die Kosten der VWs
        $dots = explode(';', $fighter->GetDots());
        foreach ($dots as &$dot)
        {
            $dotData = explode('@', $dot);
            $dotUser = $dotData[0];
            $dotID = $dotData[1];
            $dotRound = $dotData[2];

            $dotAtk = $this->GetAttack($dotID);
            $dotCaster = $this->GetFighter($dotUser);
            if ($dotCaster == null)
            {
                $this->ReverseDots($fighter, $dotAtk);
                continue;
            }

            $cost = $this->CalculateCost($dotAtk->GetKP(), $dotAtk->IsCostProcentual(), $dotCaster->GetMaxKP());
            $kp = $dotCaster->GetKP() - $cost;
            if ($kp < 0)
            {
                $kp = 0;
            }
            $dotCaster->SetKP($kp);
            $this->database->Update('kp=' . $kp , 'fighters', 'id = ' . $dotCaster->GetID() , 1);

            if ($kp == 0 || $dotRound == 0)
            {
                $this->ReverseDots($fighter, $dotAtk);
            }
        }

        $dots = $fighter->GetDots();
        if ($dots != '')
        {
            $dots = explode(';', $dots);

            $newDots = array();
            foreach ($dots as &$dot)
            {
                $dotData = explode('@', $dot);
                $dotData[2]--;
                $newDots[] = implode('@', $dotData);
            }
            $fighter->SetDots(implode(';', $newDots));
        }

        $this->database->Update('dots="' . $fighter->GetDots() . '"', 'fighters', 'id = ' . $fighter->GetID() , 1);
    }

    private function DoBuffCost(&$fighter)
    {
        $this->AddDebugLog(' - DoBuffCost of: ' . $fighter->GetName());
        //Berechne die Kosten der VWs
        $buffs = explode(';', $fighter->GetBuffs());
        foreach ($buffs as &$buff)
        {
            $buffData = explode('@', $buff);
            $buffUser = $buffData[0];
            $buffID = $buffData[1];
            $buffRound = $buffData[2];

            $buffAtk = $this->GetAttack($buffID);
            $this->AddDebugLog(' - - Check Buff: ' . $buffAtk->GetName() . ' (' . $buffID . ')');
            $buffCaster = $this->GetFighter($buffUser);
            if ($buffCaster == null)
            {
                $this->AddDebugLog(' - - ReverseBuff because of no caster');
                $this->ReverseBuff($fighter, $buffAtk);
                continue;
            }
            $this->AddDebugLog(' - - Caster: ' . $buffCaster->GetName());

            $cost = $this->CalculateCost($buffAtk->GetKP(), $buffAtk->IsKPProcentual(), $buffCaster->GetMaxKP());

            if ($buffAtk->IsSingleCost())
                $kp = $buffCaster->GetKP();
            else
                $kp = $buffCaster->GetKP() - $cost;
            if ($kp < 0)
            {
                $kp = 0;
            }
            $buffCaster->SetKP($kp);
            $this->database->Update('kp=' . $kp , 'fighters', 'id = ' . $buffCaster->GetID() , 1);

            if ($kp == 0 && !$buffAtk->IsSingleCost() || $buffRound == 0)
            {
                $this->AddDebugLog(' - - ReverseBuff because round (' . $buffRound . ') or ad (' . $kp . ') is 0');
                $this->ReverseBuff($fighter, $buffAtk);
            }
        }

        $buffs = $fighter->GetBuffs();
        $this->AddDebugLog(' - - Previous Buffs of: ' . $fighter->GetName() . ': ' . $buffs);
        if ($buffs != '')
        {
            $buffs = explode(';', $buffs);

            $newBuffs = array();
            foreach ($buffs as &$buff)
            {
                $buffData = explode('@', $buff);
                $this->AddDebugLog(' - - - check buff: ' . $buffData[1]);
                $this->AddDebugLog(' - - - decrease round: ' . $buffData[2]);
                $buffData[2]--;
                $this->AddDebugLog(' - - - new round: ' . $buffData[2]);
                $newBuffs[] = implode('@', $buffData);
            }
            $fighter->SetBuffs(implode(';', $newBuffs));
        }

        $this->AddDebugLog(' - - Update Buffs to: ' . $fighter->GetBuffs());
        $this->database->Update('buffs="' . $fighter->GetBuffs() . '"', 'fighters', 'id = ' . $fighter->GetID() , 1);
    }

    private function DoDebuffCost(&$fighter)
    {
        $this->AddDebugLog(' - DoDebuffCost of: ' . $fighter->GetName());
        //Berechne die Kosten der VWs
        $debuffs = explode(';', $fighter->GetDebuffs());

        foreach ($debuffs as &$debuff)
        {
            $debuffData = explode('@', $debuff);
            $debuffUser = $debuffData[0];
            $debuffID = $debuffData[1];
            $debuffRound = $debuffData[2];

            $debuffAtk = $this->GetAttack($debuffID);
            $this->AddDebugLog(' - - Check Debuff: ' . $debuffAtk->GetName() . ' (' . $debuffID . ')');
            $debuffCaster = $this->GetFighter($debuffUser);
            if ($debuffCaster == null)
            {
                $this->AddDebugLog(' - - ReverseDebuff because of no caster');
                $this->ReverseDebuff($fighter, $debuffAtk);
                continue;
            }
            $this->AddDebugLog(' - - Caster: ' . $debuffCaster->GetName());

            $cost = $this->CalculateCost($debuffAtk->GetKP(), $debuffAtk->IsKPProcentual(), $debuffCaster->GetMaxKP());

            if ($debuffAtk->IsSingleCost())
                $kp = $debuffCaster->GetKP();
            else
                $kp = $debuffCaster->GetKP() - $cost;
            if ($kp < 0)
            {
                $kp = 0;
            }
            $debuffCaster->SetKP($kp);
            $this->database->Update('kp=' . $kp , 'fighters', 'id = ' . $debuffCaster->GetID() , 1);

            if ($kp == 0 && !$debuffAtk->IsSingleCost() || $debuffRound == 0)
            {
                $this->AddDebugLog(' - - ReverseDebuff because round (' . $debuffRound . ') or ad (' . $kp . ') is 0');
                $this->ReverseDebuff($fighter, $debuffAtk);
            }
        }

        $debuffs = $fighter->GetDebuffs();
        $this->AddDebugLog(' - - Previous Debuffs of: ' . $fighter->GetName() . ': ' . $debuffs);
        if ($debuffs != '')
        {
            $debuffs = explode(';', $debuffs);

            $newDebuffs = array();
            foreach ($debuffs as &$debuff)
            {
                $debuffData = explode('@', $debuff);
                $this->AddDebugLog(' - - - check debuff: ' . $debuffData[1]);
                $this->AddDebugLog(' - - - decrease round: ' . $debuffData[2]);
                $debuffData[2]--;
                $this->AddDebugLog(' - - - new round: ' . $debuffData[2]);
                $newDebuffs[] = implode('@', $debuffData);
            }
            $fighter->SetDebuffs(implode(';', $newDebuffs));
        }

        $this->AddDebugLog(' - - Update Debuffs to: ' . $fighter->GetDebuffs());
        $this->database->Update('debuffs="' . $fighter->GetDebuffs() . '"', 'fighters', 'id = ' . $fighter->GetID() , 1);
    }


    private function CalculateAttack($player, $target, $attack)
    {
        //Deal Damage
        $damage = 0;
        $type = '';
        $text = '';

        $died = false;
        $lpminus = 0;
        $kpminus = 0;
        $removeLoadAttack = false;

        $this->AddDebugLog('Calculate Attack ' . $attack->GetName() . ' of ' . $player->GetName() . ' on ' . $target->GetName());

        if ($player->GetLoadAttack() != 0)
        {
            $loadAttack = $this->GetAttack($player->GetLoadAttack());
            if ($loadAttack->GetLoadAttack() != $attack->GetID())
            {
                $team = $player->GetTeam();
                if (!$this->TeamHasLoadAttack($player, $loadAttack->GetID()))
                {
                    $j = 0;
                    $teamMembers = $this->teams[$team];
                    while (isset($teamMembers[$j]))
                    {
                        $teamMember = $teamMembers[$j];
                        $teamMember->RemoveAttack($loadAttack->GetLoadAttack());
                        $this->database->Update('attacks="' . $teamMember->GetAttacks() . '"', 'fighters', 'id = ' . $teamMember->GetID() , 1);
                        ++$j;
                    }
                    $removeLoadAttack = true;
                }
            }
        }
        $loadRounds = $player->GetLoadRounds();
        if ($loadRounds != 0)
        {
            $loadRounds = $loadRounds - 1;
            $this->database->Update('loadrounds=' . $loadRounds , 'fighters', 'id = ' . $player->GetID() , 1);
            $player->SetLoadRounds($loadRounds);
        }

        if ($loadRounds != 0)
        {
            $text = $attack->GetLoadText();
        }
        else if ($attack->GetType() != 27 && $attack->GetType() != 26 && $attack->GetBlockAttack() != 0 && $attack->GetBlockAttack() == $target->GetAction())
        {
            $text = $attack->GetMissText();
        }
        else if ($attack->GetBlockedAttack() != 0 && $attack->GetBlockedAttack() != $target->GetAction())
        {
            $text = $attack->GetMissText();
        }
        else if($attack->GetType() == 9 && $attack->GetBlockAttack() != 0 && $attack->GetBlockAttack() == $target->GetAction())
        {
            $text = $attack->GetMissText();
        }
        else if ($attack->GetType() == 1)
        {
            $alive = $target->GetLP() != 0;
            $text = $this->DealDamage($player, $target, $attack, $damage, $type, true, false, false);
            if ($alive && $target->GetLP() == 0 && $this->GetType() == 2)
            {
                $died = true;
            }
        }
        else if ($attack->GetType() == 2)
        {
            $text = $this->Defend($player, $attack);
        }
        else if ($attack->GetType() == 3)
        {
            $alive = $target->GetLP() != 0;
            $killed = false;
            $text = $this->Kill($player, $target, $attack, $killed, $damage);

            if ($alive && $killed && $this->GetType() != 0)
            {
                $died = true;
            }
        }
        else if ($attack->GetType() == 4)
        {
            $text = $this->Transform($player, $attack);
        }
        else if ($attack->GetType() == 5)
        {
            $text = $this->Heal($player, $target, $attack, $damage, $type);
        }
        else if ($attack->GetType() == 6)
        {
            $alive = $target->GetLP() != 0;
            $text = $this->LoadDamage($player, $target, $attack, $damage, $type);
            if ($alive && $target->GetLP() == 0 && $this->GetType() == 2)
            {
                $died = true;
            }
        }
        else if ($attack->GetType() == 7)
        {
            if ($attack->GetLoadAttack() == $player->GetLoadAttack())
            {
                $target = $player;
            }
            $text = $this->LoadAttack($player, $target, $attack);
        }
        else if ($attack->GetType() == 8)
        {
            $text = $this->SpawnNPC($player, $attack);
        }
        else if ($attack->GetType() == 9)
        {
            $text = $this->Paralyze($player, $target, $attack);
        }
        else if ($attack->GetType() == 10)
        {
            $text = $this->DeParalyze($player, $attack);
        }
        else if ($attack->GetType() == 11)
        {
            $text = $this->SelfHeal($player, $attack, $damage);
        }
        else if ($attack->GetType() == 12)
        {
            $alive = $target->GetLP() != 0;
            $text = $this->DealDamage($player, $target, $attack, $damage, $type, false, false, false);
            if ($alive && $target->GetLP() == 0 && $this->GetType() == 2)
            {
                $died = true;
            }
            if ($player->GetLP() != 0)
                $this->AbsoluteHeal($player, $damage, $attack);
        }
        else if ($attack->GetType() == 13)
        {
            $text = $this->Fuse($player, $target, $attack);
        }
        else if ($attack->GetType() == 15)
        {
            $text = $this->DoMiscAttacks($attack);
        }
        else if ($attack->GetType() == 16)
        {
            //if ($target->GetName() == $player->GetName())
            $text = $this->DoGiveUP($player, $attack);
            //else
            //return;
        }
        else if ($attack->GetType() == 17)
        {
            $text = $this->Revive($player, $target, $attack, $damage);
        }
        else if ($attack->GetType() == 18)
        {
            $revert = false;
            if ($target->GetBuffs() != '')
            {
                $buffs = explode(';', $target->GetBuffs());
            }
            else
                $buffs = array();

            foreach ($buffs as &$buff)
            {
                $buffData = explode('@', $buff);
                if ($buffData[0] == $player->GetID() && $buffData[1] == $attack->GetID())
                {
                    if($target == $player)
                        $revert = true;
                }
            }

            if(!$revert)
            {
                $lpminus = $this->CalculateCost($attack->GetLP(), $attack->IsCostProcentual(), $player->GetMaxLP());
                if (!$attack->IsKPProcentual())
                    $kpminus = $this->CalculateCost($attack->GetKP(), $attack->IsKPProcentual(), $player->GetMaxKP());
                else
                    $kpminus = round(($player->GetMaxKP()) * $attack->GetKP($attack->IsKPProcentual()));
            }
            $text = $this->Buff($player, $target, $attack);
        }
        else if ($attack->GetType() == 19)
        {
            $text = $this->UnParalyze($player, $target, $attack);
        }
        else if ($attack->GetType() == 20)
        {
            $text = $this->Defend($player, $attack);
        }
        else if ($attack->GetType() == 21)
        {
            $revert = false;
            if ($player->GetBuffs() != '')
            {
                $buffs = explode(';', $player->GetBuffs());
            }
            else
                $buffs = array();

            foreach ($buffs as &$buff)
            {
                $buffData = explode('@', $buff);
                if ($buffData[0] == $player->GetID() && $buffData[1] == $attack->GetID())
                {
                    //apply
                    $revert = true;
                }
            }

            if(!$revert)
            {
                $lpminus = $this->CalculateCost($attack->GetLP(), $attack->IsCostProcentual(), $player->GetMaxLP());
                if (!$attack->IsKPProcentual())
                    $kpminus = $this->CalculateCost($attack->GetKP(), $attack->IsKPProcentual(), $player->GetMaxKP());
                else
                    $kpminus = round(($player->GetMaxKP()) * $attack->GetKP($attack->IsKPProcentual()));
            }
            $text = $this->Buff($player, $player, $attack);
        }
        else if ($attack->GetType() == 22)
        {
            $text = $this->Dots($player, $target, $attack);
        }
        else if ($attack->GetType() == 23)
        {
            $alive = $target->GetLP() != 0;
            $text = $this->DealDamage($player, $target, $attack, $damage, $type, true, true, false);
            if ($alive && $target->GetLP() == 0 && $this->GetType() == 2)
            {
                $died = true;
            }
        }
        else if ($attack->GetType() == 24)
        {
            $alive = $target->GetLP() != 0;
            $text = $this->DealDamage($player, $target, $attack, $damage, $type, true, false, true);
            if ($alive && $target->GetLP() == 0 && $this->GetType() == 2)
            {
                $died = true;
            }
        }
        else if ($attack->GetType() == 25)
        {
            $revert = false;
            if ($target->GetDebuffs() != '')
            {
                $debuffs = explode(';', $target->GetDebuffs());
            }
            else
                $debuffs = array();

            foreach ($debuffs as &$debuff)
            {
                $debuffData = explode('@', $debuff);
                if ($debuffData[0] == $player->GetID() && $debuffData[1] == $attack->GetID())
                {
                    if($target == $player)
                        $revert = true;
                }
            }

            if(!$revert)
            {
                $lpminus = $this->CalculateCost($attack->GetLP(), $attack->IsCostProcentual(), $player->GetMaxLP());
                if (!$attack->IsKPProcentual())
                    $kpminus = $this->CalculateCost($attack->GetKP(), $attack->IsKPProcentual(), $player->GetMaxKP());
                else
                    $kpminus = round(($player->GetMaxKP()) * $attack->GetKP($attack->IsKPProcentual()));
            }
            $text = $this->Debuff($player, $target, $attack);
        }
        else if ($attack->GetType() == 26) // AOE
        {
            $i = 0;
            while (isset($this->teams[$i]))
            {
                $players = $this->teams[$i];
                $j = 0;
                if($player->GetTeam() != $i)
                {
                    if($attack->GetEnemyAmount() == 0 || $attack->GetEnemyAmount() >= $this->GetMembersOfTeam($i))
                    {
                        while (isset($players[$j]))
                        {
                            $target = $players[$j];

                            if ($attack->GetBlockAttack() != 0 && $attack->GetBlockAttack() == $target->GetAction())
                            {
                                $text .= $attack->GetMissText() . '<br/>';
                            }
                            else
                            {
                                $alive = $target->GetLP() != 0;
                                $text .= $this->DealDamage($player, $target, $attack, $damage, $type, true, false, false) . '<br/>';
                                $text = $this->ReplaceTextValues($text, $player, $target, $damage, $type);
                                if ($alive && $target->GetLP() == 0 && $this->GetType() == 2)
                                {
                                    $died = true;
                                }
                            }
                            ++$j;
                        }
                    }
                    else
                    {
                        $enemyshit = array();
                        for ($j = 0; $j < $attack->GetEnemyAmount(); ++$j)
                        {
                            $target = null;
                            while($target == null)
                            {
                                if(in_array($players[rand(0, $this->GetMembersOfTeam($i) - 1)]->GetID(), $enemyshit) || $players[rand(0, $this->GetMembersOfTeam($i) - 1)]->GetLP() <= 0)
                                    $target = null;
                                else
                                    $target = $players[rand(0, $this->GetMembersOfTeam($i))];
                            }
                            if ($attack->GetBlockAttack() != 0 && $attack->GetBlockAttack() == $target->GetAction())
                            {
                                $text .= $attack->GetMissText() . '<br/>';
                            }
                            else
                            {
                                $alive = $target->GetLP() != 0;
                                $text .= $this->DealDamage($player, $target, $attack, $damage, $type, true, false, false) . '<br/>';
                                $text = $this->ReplaceTextValues($text, $player, $target, $damage, $type);
                                if ($alive && $target->GetLP() == 0 && $this->GetType() == 2)
                                {
                                    $died = true;
                                }
                            }
                            array_push($enemyshit, $target->GetID());
                        }
                    }
                }
                ++$i;
            }
        }
        else if ($attack->GetType() == 27) // % AOE
        {
            $i = 0;
            while (isset($this->teams[$i]))
            {
                $players = $this->teams[$i];
                $j = 0;
                if($player->GetTeam() != $i)
                {
                    if($attack->GetEnemyAmount() == 0 || $attack->GetEnemyAmount() >= $this->GetMembersOfTeam($i))
                    {
                        while (isset($players[$j]))
                        {
                            $target = $players[$j];
                            $alive = $target->GetLP() != 0;
                            if($alive)
                            {
                                if ($attack->GetBlockAttack() != 0 && $attack->GetBlockAttack() == $target->GetAction())
                                {
                                    $text .= $attack->GetMissText() . '<br/>';
                                }
                                else
                                {
                                    $text .= $this->DealDamage($player, $target, $attack, $damage, $type, true, true, false) . '<br/>';
                                    $text = $this->ReplaceTextValues($text, $player, $target, $damage, $type);
                                    if ($alive && $target->GetLP() == 0 && $this->GetType() == 2)
                                    {
                                        $died = true;
                                    }
                                }
                            }
                            ++$j;
                        }
                    }
                    else
                    {
                        $enemyshit = array();
                        for ($j = 0; $j < $attack->GetEnemyAmount(); ++$j)
                        {
                            $target = null;
                            while($target == null)
                            {
                                if(in_array($players[rand(0, $this->GetMembersOfTeam($i) - 1)]->GetID(), $enemyshit) || $players[rand(0, $this->GetMembersOfTeam($i) - 1)]->GetLP() <= 0)
                                    $target = null;
                                else
                                    $target = $players[rand(0, $this->GetMembersOfTeam($i))];
                            }
                            $alive = $target->GetLP() != 0;
                            if($alive)
                            {
                                if ($attack->GetBlockAttack() != 0 && $attack->GetBlockAttack() == $target->GetAction())
                                {
                                    $text .= $attack->GetMissText() . '<br/>';
                                }
                                else
                                {
                                    $text .= $this->DealDamage($player, $target, $attack, $damage, $type, true, true, false) . '<br/>';
                                    $text = $this->ReplaceTextValues($text, $player, $target, $damage, $type);
                                    if ($alive && $target->GetLP() == 0 && $this->GetType() == 2)
                                    {
                                        $died = true;
                                    }
                                }
                            }
                            array_push($enemyshit, $target->GetID());
                        }
                    }
                }
                ++$i;
            }
        }

        if ($attack->GetType() != 4 && $attack->GetType() != 18 && $attack->GetType() != 21 && $attack->GetType() != 22 && $attack->GetType() != 25)
        {
            $lpminus = $this->CalculateCost($attack->GetLP(), $attack->IsCostProcentual(), $player->GetMaxLP());
            if (!$attack->IsKPProcentual())
                $kpminus = $this->CalculateCost($attack->GetKP(), $attack->IsKPProcentual(), $player->GetMaxKP());
            else
                $kpminus = round(($player->GetMaxKP()) * $attack->GetKP($attack->IsKPProcentual()));
        }

        $energyMinusConstant = 10;

        $energyMinus = $attack->GetEnergy() - $energyMinusConstant;

        if ($removeLoadAttack)
        {
            $player->SetLoadAttack(0);
            $this->database->Update('loadattack=0', 'fighters', 'id = ' . $player->GetID() , 1);
        }

        $trans = array_filter(explode(';', $player->GetTransformations()));
        $i = 0;

        $revertKP = false;
        $revertLP = false;
        while (isset($trans[$i]))
        {
            $transAttack = $this->GetAttack($trans[$i]);
            if ($transAttack->GetLP() != 0)
            {
                $cost = $this->CalculateCost($transAttack->GetLP(), $transAttack->IsCostProcentual(), $player->GetMaxLP());
                $lpminus += $cost;
                $revertLP = true;
            }
            if ($transAttack->GetKP() != 0)
            {
                $cost = $this->CalculateCost($transAttack->GetKP(), $transAttack->IsCostProcentual(), $player->GetMaxKP());
                $kpminus += $cost;
                $revertKP = true;
            }
            ++$i;
        }

        $reverts = false;

        if ($kpminus != 0)
        {
            $kp = $player->GetKP() - $kpminus;

            if ($kp < 0) $kp = 0;
            else if ($player->GetKP() <= $player->GetIncreasedKP() && $kp > $player->GetIncreasedKP()) $kp = $player->GetIncreasedKP();
            $player->SetKP($kp);
            if ($revertKP && $kp == 0)
            {
                $reverts = true;
            }
        }
        if ($lpminus != 0)
        {
            $lp = $player->GetLP() - $lpminus;
            if ($lp < 0) $lp = 0;
            else if ($player->GetLP() <= $player->GetIncreasedLP() && $lp > $player->GetIncreasedLP()) $lp = $player->GetIncreasedLP();
            $player->SetLP($lp);
            if ($revertLP && $lp == 0)
            {
                $reverts = true;
            }
        }

        $energy = $player->GetEnergy() + $energyMinus;
        if ($energy < 0)
            $energy = 0;
        else if ($energy >= $player->GetMaxEnergy())
            $energy = $player->GetMaxEnergy();
        $player->SetEnergy($energy);

        if ($reverts)
        {
            $this->Revert($player);
        }
        else
        {
            $update = '';
            if ($kpminus != 0)
            {
                $updateVal = 'kp=' . $player->GetKP() ;
                if ($update != '')
                {
                    $update = $update . ', ' . $updateVal;
                }
                else
                {
                    $update = $updateVal;
                }
            }
            if ($lpminus != 0)
            {
                $updateVal = 'lp=' . $player->GetLP() ;
                if ($update != '')
                {
                    $update = $update . ', ' . $updateVal;
                }
                else
                {
                    $update = $updateVal;
                }
            }

            $updateVal = 'energy=' . $player->GetEnergy() ;
            if ($update != '')
            {
                $update = $update . ', ' . $updateVal;
            }
            else
            {
                $update = $updateVal;
            }

            if ($update != '')
            {
                $this->database->Update($update, 'fighters', 'id = ' . $player->GetID() , 1);
            }
        }

        $text = $this->ReplaceTextValues($text, $player, $target, $damage, $type);

        if ($died && $this->GetType() != 8)
        {
            $planet = 2;
            $place = 10;
            if ($this->playerAccount != null && $target->GetAcc() == $this->playerAccount->GetID())
            {
                $deadPlayer = $this->playerAccount;
            }
            else
            {
                $deadPlayer = new Player($this->database, $target->GetAcc(), $this->actionManager);
            }
            if ($deadPlayer->GetAction() != 0 || $deadPlayer->GetTravelAction() != 0)
            {
                $deadPlayer->CancelAction(true, true);
            }
            if ($deadPlayer->GetPlanet() != 2)
            {
                if($deadPlayer->GetLevel() >= 48 && $deadPlayer->GetLevel() < 100)
                {
                    $place = 11;
                }
                else if($deadPlayer->GetLevel() >= 100 && $deadPlayer->GetLevel() < 200)
                {
                    $place = 12;
                }
                else if($deadPlayer->GetLevel() >= 200 && $deadPlayer->GetLevel() < 300)
                {
                    $place = 13;
                }
                else if($deadPlayer->GetLevel() >= 300 && $deadPlayer->GetLevel() < 400)
                {
                    $place = 14;
                }
                else if($deadPlayer->GetLevel() >= 400 && $deadPlayer->GetLevel() < 500)
                {
                    $place = 15;
                }
                $deadPlayer->SetImpelDownPopUp(0);
                $deadPlayer->SetPlanet($planet);
                $deadPlayer->SetPlace($place);
                $timestamp = date('Y-m-d H:i:s');
                $this->database->Update('impeldownpopup=1, deathplace=place, deathplanet=planet, deathtime= "' . $timestamp . '", place="' . $place . '", planet="' . $planet . '"', 'accounts', 'id = ' . $target->GetAcc() , 1);
            }
        }

        return $text;
    }

    public function ReplaceTextValues($text, $player, $target, $damage, $type)
    {
        if ($player != null) {
            $playerName = $player->GetName();
            if($player->HasNPCControl())
                $playerName .= " (AFK)";
            $playerText = '<b><span style="color: ' . $this->GetTeamColor($player->GetTeam()) . '">' . $playerName . '</span></b>';
        }
        else
            $playerText = '';

        $playernameText = '<b><span style="color: ' . $this->GetTeamColor(0) . '">' . $this->playerAccount->GetName() . '</span></b>';

        if ($target != null) {
            $playerName = $target->GetName();
            if ($target->HasNPCControl())
                $playerName .= " (AFK)";
            $targetText = '<b><span style="color: ' . $this->GetTeamColor($target->GetTeam()) . '">' . $playerName . '</span></b>';
        }
        else
            $targetText = '';

        $damageText = '<b>' . number_format($damage, '0', '', '.') . '</b>';
        $typeText = '<b>' . $type . '</b>';
        $text = str_replace('!source', $playerText, $text);
        $text = str_replace('!target', $targetText, $text);
        $text = str_replace('!player', $playernameText , $text);
        $text = str_replace('!damage', $damageText, $text);
        $text = str_replace('!type', $typeText, $text);

        return $text;
    }

    private function DoGiveUP($player, $attack)
    {
        $player->SetLP(0);
        $this->database->Update('lp=0', 'fighters', 'id = ' . $player->GetID() , 1);
        if ($this->GetType() == 8)
        {
            $this->database->Delete('arenafighter', 'fighter = ' . $player->GetAcc() , 1);
        }
        return $attack->GetText();
    }

    private function DoMiscAttacks($attack)
    {
        return $attack->GetText();
    }

    private function Fuse($player, $target, $action)
    {
        if ($target->GetTarget() != $player->GetID() || $target->GetAction() != $action->GetID())
        {
            return $action->GetMissText();
        }
        if ($target->GetID() == $player->GetID())
        {
            return $action->GetMissText();
        }
        else if ($target->GetTeam() != $player->GetTeam())
        {
            return $action->GetMissText();
        }
        else if ($player->GetFusedAcc() != 0 || $target->GetFusedAcc() != 0)
        {
            return $action->GetMissText();
        }
        else if ($target->GetLP() == 0)
        {
            return $action->GetMissText();
        }
        else if ($target->IsInactive())
        {
            return $action->GetText();
        }

        $this->Revert($player);
        $this->Revert($target);

        $fusedPlayer = new Player($this->database, $player->GetAcc(), $this->actionManager);

        $miss = rand(0, 100);
        $hit = $action->GetAccuracy();

        $increase = ($action->GetValue() / 100);
        if ($hit < $miss)
        {
            $increase = 0.75;
        }

        $lp = ($player->GetLP() + $target->GetLP());
        $lp = round($lp * $increase);
        $mlp = ($player->GetMaxLP() + $target->GetMaxLP());
        $mlp = round($mlp * $increase);
        $kp = ($player->GetKP() + $target->GetKP());
        $kp = round($kp * $increase);
        $mkp = ($player->GetMaxKP() + $target->GetMaxKP());
        $mkp = round($mkp * $increase);

        $attack = (($player->GetMaxAttack() + $target->GetMaxAttack()) / 2);
        $attack = round($attack * $increase);
        $defense = (($player->GetMaxDefense() + $target->GetMaxDefense()) / 2);
        $defense = round($defense * $increase);

        $fusedPlayer->SetLP($lp);
        $fusedPlayer->SetMaxLP($mlp);
        $fusedPlayer->SetKP($kp);
        $fusedPlayer->SetMaxKP($mkp);
        $fusedPlayer->SetAttack($attack);
        $fusedPlayer->SetDefense($defense);

        $halfPlayerName = substr($player->GetName(), 0, strlen($player->GetName()) / 2);
        $halfTargetName = substr($target->GetName(), strlen($target->GetName()) / 2, strlen($target->GetName()));

        $name = $halfPlayerName . $halfTargetName;
        if ($hit < $miss)
        {
            if (rand(0, 1) == 0)
                $name = 'Fetter ' . $name;
            else
                $name = 'Dünner ' . $name;
        }


        $fusedPlayer->SetName($name);
        $this->CreateFighter($fusedPlayer, $player->GetTeam(), false, 0, $target->GetAcc(), $action->GetRounds());
        $this->database->Update('inactive=1', 'fighters', 'id = ' . $player->GetID() . ' OR id=' . $target->GetID() , 2);
        $target->SetInactive(true);
        $player->SetInactive(true);

        if ($hit < $miss)
        {
            return $action->GetDeadText();
        }
        return $action->GetText();
    }

    private function Paralyze($player, $target, $attack)
    {
        if ($target->GetLP() == 0)
        {
            return $attack->GetDeadText();
        }

        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy() * ($player->GetAccuracy() / $target->GetReflex());

        $addtext = '';
        if ($hit < $miss || $target->GetParalyzed() != 0 || $target->GetAction() == 784)
        {
            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }
        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';
        $target->SetAction($attack->GetLoadAttack());
        $target->SetParalyzed($attack->GetValue());
        $target->SetPreviousTarget($target->GetTarget());
        $this->database->Update('previoustarget='. $target->GetTarget(). ',action=' . $attack->GetLoadAttack() . ',paralyzed=' . $attack->GetValue() , 'fighters', 'id = ' . $target->GetID() , 1);
        $this->AddDebugLog(' - Paralyze: ' . $attack->GetValue());
        return $returnText;
    }

    private function DeParalyze($player, $attack)
    {
        $paralyzed = $player->GetParalyzed() - 1;
        if ($paralyzed < 0)
            $paralyzed = 0;

        $returnText = $attack->GetText();
        if ($paralyzed == 0)
        {
            $returnText = $attack->GetMissText();
        }
        $player->SetParalyzed($paralyzed);
        $this->AddDebugLog(' - DeParalyze: ' . $paralyzed);
        $this->database->Update('paralyzed=' . $paralyzed , 'fighters', 'id = ' . $player->GetID() , 1);
        return $returnText;
    }

    private function Kill($player, $target, $attack, &$killed, &$damage)
    {
        $killed = false;
        if ($target->GetLP() == 0)
        {
            return $attack->GetDeadText();
        }

        if($target->IsNPC())
        {
            $damage = 0;
            return $attack->GetMissText();
        }

        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy() * ($player->GetAccuracy() / $target->GetReflex());

        if ($hit < $miss || $target->GetLPPercentage() > $attack->GetValue())
        {
            $atkVal = $player->GetAttack() / $target->GetDefense();
            $this->AddDebugLog(' - $atkVal: ' . $atkVal);
            $atkStrength = 1;
            $playerValue = $player->GetKI() * ($atkStrength / 100);
            $this->AddDebugLog(' - $playerValue: ' . $playerValue);
            $atkVal = round($playerValue * $atkVal);
            $damage = $atkVal;
            $this->AddDebugLog(' - $damage: ' . number_format($damage, '0', '', '.'));

            $lp = $target->GetLP() - $damage;
            if ($lp < 0)
                $lp = 0;
            $target->SetLP($lp);
            $this->database->Update('lp=' . $lp, 'fighters', 'id=' . $target->GetID(), 1);

            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }

        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';
        $lp = 0;
        $returnText = $returnText . '<br/>!target sackt zu Boden.';

        $target->SetLP($lp);
        $this->database->Update('lp=' . $lp, 'fighters', 'id=' . $target->GetID(), 1);
        $killed = true;

        return $returnText;
    }

    private function SpawnNPC($player, $attack)
    {
        $team = $player->GetTeam();
        $teamMembers = $this->teams[$team];
        $j = 0;
        while (isset($teamMembers[$j]))
        {
            $teamMember = $teamMembers[$j];
            if ($teamMember->GetOwner() == $player->GetID() && !$player->IsNPC())
            {
                return $attack->GetMissText();
            }
            ++$j;
        }

        $stats = $attack->GetValue();
        if ($attack->IsProcentual())
        {
            $stats = ceil($player->GetKI() * ($attack->GetValue() / 100));
        }

        for($i = 0; $i < $attack->GetValue(); ++$i)
        {
            $npcID = $attack->GetNPCID();
            $npc = new NPC($this->database, $npcID, $stats);
            $this->CreateFighter($npc, $team, true, $player->GetID());
        }

        return $attack->GetText();
    }

    public function IncMode($team)
    {
        //Increase mode
        $mode = $this->GetMode();
        $mode[$team] = $mode[$team] + 1;
        $mode = implode('vs', $mode);
        $this->SetMode($mode);
        $this->database->Update('mode="' . $mode . '"', 'fights', 'id = ' . $this->GetID() , 1);
        $this->AddDebugLog('Increase mode to: ' .$mode);
    }

    public function DecMode($team)
    {
        //Decrease mode
        $mode = $this->GetMode();
        $mode[$team] = $mode[$team] - 1;
        $mode = implode('vs', $mode);
        $this->SetMode($mode);
        $this->database->Update('mode=' . $mode , 'fights', 'id = ' . $this->GetID() , 1);
    }

    private function LoadAttack($player, $target, $attack)
    {
        if ($target->GetLoadAttack() != $attack->GetLoadAttack())
        {
            return $attack->GetMissText();
        }

        if ($attack->IsProcentual())
        {
            $damage = ceil($player->GetKI() * ($attack->GetValue() / 100));
        }
        else
            $damage = $attack->GetValue();

        $newLoadValue = $target->GetLoadValue() + $damage;
        $target->SetLoadValue($newLoadValue);
        $this->database->Update('loadvalue=' . $newLoadValue , 'fighters', 'id = ' . $target->GetID() , 1);
        return $attack->GetText();
    }

    private function StartLoadDamage($player, $attack)
    {
        $loadAttack = $attack->GetID();
        $player->SetLoadAttack($loadAttack);
        $player->SetLoadValue(1);
        $this->database->Update('loadvalue=0,loadattack=' . $loadAttack , 'fighters', 'id = ' . $player->GetID() , 1);

        $team = $player->GetTeam();
        $j = 0;
        $teamMembers = $this->teams[$team];
        while (isset($teamMembers[$j]))
        {
            $teamMember = $teamMembers[$j];
            $teamMember->AddAttack($attack->GetLoadAttack());
            $this->database->Update('attacks="' . $teamMember->GetAttacks() . '"', 'fighters', 'id = ' . $teamMember->GetID() , 1);
            ++$j;
        }

        return $attack->GetLoadText();
    }

    private function FireLoadDamage($player, $target, $attack, &$damage, &$type)
    {
        if ($target->GetLP() == 0)
        {
            return $attack->GetDeadText();
        }

        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy() * ($player->GetAccuracy() / $target->GetReflex());

        if ($target->GetParalyzed() == 0 && $hit < $miss)
        {
            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }

        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';

        $atkVal = $player->GetAttack() / $target->GetDefense();

        $damage = $player->GetLoadValue() * ($attack->GetValue() / 100) * $atkVal;

        if($attack->GetCritchance() > 0)
        {
            $critChance = $attack->GetCritchance();
        }
        else
        {
            $critChance = $player->GetCritchance();
        }
        $crit = rand(0, 100);

        if($crit <= $critChance)
        {


            $damage = $damage * $attack->GetCritdamage() * $player->GetCritdamage();

            $type= 'kritische';
        }

        $damage = round($damage);

        $lp = $target->GetLP();
        $lp = $lp - $damage;

        if ($lp <= 0)
        {
            $lp = 0;
            $returnText = $returnText . '<br/>!target kippt um.';
        }

        $target->SetLP($lp);
        $player->SetLoadValue(0);
        $player->SetLoadAttack(0);
        $this->database->Update('lp="' . $lp . '", loadvalue="0", loadattack="0"', 'fighters', 'id = "' . $player->GetID() . '"', 1);

        return $returnText;
    }

    private function LoadDamage($player, $target, $attack, &$damage, &$type)
    {
        if ($player->GetLoadAttack() != $attack->GetID())
        {
            return $this->StartLoadDamage($player, $attack);
        }

        return $this->FireLoadDamage($player, $target, $attack, $damage, $type);
    }

    private function DealDamage($player, $target, $attack, &$damage, &$type, $useAtkVal, $damageProcentual, $techProcentual)
    {
        if ($target->GetLP() == 0)
        {
            $this->AddDebugLog(' - ' . $player->GetName() . ' targeted dead ' . $target->GetName());
            return $attack->GetDeadText();
        }

        $miss = rand(0, 100);

        $accuracy = $player->GetAccuracy();
        $reflex = $target->GetReflex();

        $hit = $attack->GetAccuracy() * ($accuracy / $reflex);

        if ($target->GetParalyzed() == 0 && $hit < $miss)
        {
            $this->AddDebugLog(' - ' . $player->GetName() . ' missed ' . $target->GetName());
            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }

        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';
        $this->AddDebugLog(' - Player ' . $player->GetName() . ' has Attack: <b>' . number_format($player->GetAttack(), '0', '', '.') . '</b>');
        $this->AddDebugLog(' - Target ' . $target->GetName() . ' has Defense: <b>' . number_format($target->GetDefense(), '0', '', '.') . '</b>');

        $atkVal = $player->GetAttack() / $target->GetDefense();
        if (!$useAtkVal)
            $atkVal = 1;
        $this->AddDebugLog(' - AtkVal is: <b>' . $atkVal . '</b>');
        $this->AddDebugLog(' - Attack Value is: <b>' . $attack->GetValue() . '</b>');

        if ($techProcentual)
        {
            $this->AddDebugLog(' - Damage is tech procentual');

            $highestTechVal = 0;
            $i = 0;
            while (isset($this->teams[$i]))
            {
                $players = $this->teams[$i];
                $j = 0;
                while (isset($players[$j]))
                {
                    $teamPlayer = $players[$j];
                    $attacks = explode(';', $teamPlayer->GetAttacks());
                    foreach ($attacks as &$attackID)
                    {
                        $attack = $this->attackManager->GetAttack($attackID);
                        if ($attack->GetType() == 1 && $attack->GetValue() > $highestTechVal)
                            $highestTechVal = $attack->GetValue();
                    }
                    ++$j;
                }
                ++$i;
            }


            $this->AddDebugLog(' - Player ' . $player->GetName() . ' has KI: <b>' . number_format($player->GetKI(), '0', '', '.') . '</b>');
            $playerValue = $player->GetKI() * ($attack->GetValue() / 100) * ($highestTechVal / 100);
            $this->AddDebugLog(' - Playervalue is: <b>' . $playerValue . '</b>');
            $this->AddDebugLog(' - Attackminvalue is: <b>' . $attack->GetMinValue() . '</b>');
            $this->AddDebugLog(' - $highestTechVal is: <b>' . $highestTechVal . '</b>');

            if ($playerValue < $attack->GetMinValue())
            {
                $playerValue = $attack->GetMinValue();
            }
            $atkVal = $playerValue * $atkVal;
        }
        else if ($damageProcentual)
        {
            $targetLP = $target->GetIncreasedLP();
            $atkVal = $targetLP * ($attack->GetValue() / 100);
            $this->AddDebugLog(' - Damage is procentual');
            $this->AddDebugLog(' - Target ' . $target->GetName() . ' has LP: <b>' . number_format($targetLP, '0', '', '.') . '</b>');
            $this->AddDebugLog(' - Attackminvalue is: <b>' . $attack->GetMinValue() . '</b>');
        }
        else if ($attack->IsProcentual())
        {
            $this->AddDebugLog(' - Attack is procentual');
            $this->AddDebugLog(' - Player ' . $player->GetName() . ' has KI: <b>' . number_format($player->GetKI(), '0', '', '.') . '</b>');
            $playerValue = $player->GetKI() * ($attack->GetValue() / 100);
            $this->AddDebugLog(' - Playervalue is: <b>' . $playerValue . '</b>');
            $this->AddDebugLog(' - Attackminvalue is: <b>' . $attack->GetMinValue() . '</b>');

            if ($playerValue < $attack->GetMinValue())
            {
                $playerValue = $attack->GetMinValue();
            }
            $atkVal = $playerValue * $atkVal;
        }
        else
        {
            $this->AddDebugLog(' - Attack is not procentual');
            $atkVal = $atkVal * $attack->GetValue();
        }
        $this->AddDebugLog(' - $atkVal now is: <b>' . $atkVal . '</b>');


        $crit = rand(0, 100);

        if($attack->GetCritchance() > 0)
        {
            $critChance = $attack->GetCritchance();
        }
        else
        {
            $critChance = $player->GetCritchance();
        }

        if ($damageProcentual)
            $critChance = 0;

        if ($crit <= $critChance)
        {
            $players = new Player($this->database, $player->GetAcc());
            $inklDamage = 0;
            $attackss = explode(';', $players->GetAttackLevels());
            if(in_array($attack->GetID(), $attackss))
            {
                $attacklevel = $players->GetAttackLevel($attack->GetID());
                $inklDamage = round($attacklevel * ($attack->GetCritDamage() / 100));
            }
            else
            {
                $inklDamage = 0;
            }

            $atkVal = $atkVal * $attack->GetCritdamage() * $player->GetCritdamage() + $inklDamage;
            $type = 'kritische';
        }

        $atkVal = round($atkVal);
        if ($atkVal < 1)
        {
            $atkVal = 0;
        }
        $this->AddDebugLog(' - Attackval rounded is: <b>' . $atkVal . '</b>');

        $damage = 0;

        $lp = $target->GetLP();
        $lpDamage = 0;
        $this->AddDebugLog(' - Attack LP Value is: <b>' . $attack->GetLPValue() . '</b>');
        if($attack->GetPlusDamage() != 0)
        {
            $lpDamage = round($atkVal * ($attack->GetLPValue() / 100) + $attack->GetPlusDamage());
        }
        else
        {
            $lpDamage = round($atkVal * ($attack->GetLPValue() / 100));
        }
        $this->AddDebugLog(' - ' . $player->GetName() . ' lpDamage is ' . $lpDamage . ' calculated by ' . $atkVal . ' atkVal * ' . ($attack->GetLPValue() / 100) . ' GetLPValue / 100 (GetLPValue: '.$attack->GetLPValue().')');
        if ($lpDamage < 7) $lpDamage = 7;
        $damage += $lpDamage;
        $lp = $lp - $lpDamage;

        $this->AddDebugLog(' - ' . $player->GetName() . ' dealt <b>' . number_format($damage, '0', '', '.') . '</b> DMG to ' . $target->GetName());

        $update = '';

        if ($lp <= 0)
        {
            $lp = 0;
            $this->AddDebugLog(' - ' . $target->GetName() . ' is dead.');
            if ($attack->GetDisplayDied())
                $returnText = $returnText . '<br/>!target kippt um.';
        }

        $kp = $target->GetKP();
        $kpDamage = round($atkVal * ($attack->GetKPValue() / 100));
        $damage += $kpDamage;
        $kp = $kp - $kpDamage;
        if ($kp <= 0)
        {
            $kp = 0;
        }

        $ep = $target->GetEnergy();
        $epDamage = round($atkVal * ($attack->GetEPValue() / 100));
        $damage += $epDamage;
        $ep = $ep + $epDamage;
        if ($ep <= 0)
            $ep = 0;
        else if ($ep >= $target->GetMaxEnergy())
            $ep = $target->GetMaxEnergy();

        if ($target->GetReflect() != 0)
        {
            $reflectVal = $target->GetDefense() / $player->GetAttack();
            if (!$useAtkVal)
                $reflectVal = 1;
            $reflectLP = round($lpDamage * $reflectVal * $target->GetReflect() / 100);
            $reflectKP = round($kpDamage * $reflectVal * $target->GetReflect() / 100);
            $reflectEP = round($epDamage * $reflectVal * $target->GetReflect() / 100);

            $totalReflect = $reflectLP + $reflectKP + $reflectLP;
            if ($totalReflect != 0)
            {
                $this->AddDebugLog(' - ' . $target->GetName() . ' reflected <b>' . number_format($totalReflect, '0', '', '.') . '</b> DMG.');
                $this->AddDebugLog(' - - Reflect - LP: ' . number_format($reflectLP, '0', '', '.') . ' - AD: ' . number_format($reflectKP, '0', '', '.') . ' - EP: ' . number_format($reflectEP, '0', '', '.'));
                $this->AddDebugLog(' - - ReflectVal: ' . $reflectVal);
                $this->AddDebugLog(' - - Target Reflect: ' . $target->GetReflect());
                $returnText = $returnText . '<br/>!target reflektiert <b>' . number_format($totalReflect, '0', '', '.') . '</b> Schaden.';
            }

            if ($reflectLP != 0 || $reflectKP != 0 || $reflectEP != 0)
            {
                $newLP = $player->GetLP() - $reflectLP;
                if ($newLP <= 0)
                {
                    $newLP = 0;
                    $this->AddDebugLog(' - ' . $player->GetName() . ' died.');
                    $returnText = $returnText . '<br/>!source kippt um.';
                }
                $player->SetLP($newLP);
                $newKP = $player->GetKP() - $reflectKP;
                if ($newKP <= 0)
                    $newKP = 0;
                $player->SetKP($newKP);
                $newEP = $player->GetEnergy() + $reflectEP;
                if ($newEP <= 0)
                    $newEP = 0;
                else if ($newEP >= $player->GetMaxEnergy())
                    $newEP = $player->GetMaxEnergy();
                $player->SetEnergy($newEP);
                $this->database->Update('lp=' . $newLP . ',kp=' . $newKP . ',energy=' . $newEP , 'fighters', 'id = ' . $player->GetID() , 1);
            }
        }

        $target->SetLP($lp);
        $target->SetKP($kp);
        $target->SetEnergy($ep);

        if ($update != '')
            $update = $update . ',';
        $update = $update . 'lp=' . $lp . ',kp=' . $kp . ',energy=' . $ep;
        $this->database->Update($update, 'fighters', 'id = ' . $target->GetID() , 1);

        return $returnText;
    }

    private function UpdateTransform($player)
    {

        $this->AddDebugLog(' - UpdateTransform ' . $player->GetName());
        $this->AddDebugLog(' - -  buffs: ' . $player->GetBuffs());
        $this->AddDebugLog(' - -  debuffs: ' . $player->GetDebuffs());
        $this->AddDebugLog(' - -  Taunt: ' . $player->GetTaunt());
        $this->AddDebugLog(' - -  Transformations: ' . $player->GetTransformations());
        $this->AddDebugLog(' - -  lp: ' . number_format($player->GetLP(), '0', '', '.'));
        $this->AddDebugLog(' - -  ad: ' . number_format($player->GetKP(), '0', '', '.'));
        $this->AddDebugLog(' - -  ilp: ' . number_format($player->GetIncreasedLP(), '0', '', '.'));
        $this->AddDebugLog(' - -  ikp: ' . number_format($player->GetIncreasedKP(), '0', '', '.'));
        $this->AddDebugLog(' - -  attack: ' . number_format($player->GetAttack(), '0', '', '.'));
        $this->AddDebugLog(' - -  defense: ' . number_format($player->GetDefense(), '0', '', '.'));
        $this->AddDebugLog(' - -  ki: ' . number_format($player->GetKI(), '0', '', '.'));
        $sql = 'lp="' . $player->GetLP() . '"
																			,kp="' . $player->GetKP() . '"
																			,ilp="' . $player->GetIncreasedLP() . '"
																			,ikp="' . $player->GetIncreasedKP() . '"
																			,attack="' . $player->GetAttack() . '"
																			,defense="' . $player->GetDefense() . '"
																			,ki="' . $player->GetKI() . '"
																			,taunt="' . $player->GetTaunt() . '"
																			,reflect="' . $player->GetReflect() . '"
																			,reflex="' . $player->GetReflex() . '"
																			,accuracy="' . $player->GetAccuracy() . '"
																			,transformations="' . $player->GetTransformations() . '"
																			,buffs="' . $player->GetBuffs() . '"
																			,debuffs="' . $player->GetDebuffs() . '"';
        $this->database->Update($sql, 'fighters', 'id = ' . $player->GetID() , 1);
    }

    private function Revert($player, $update = true)
    {
        $this->AddDebugLog(' - Revert ' . $player->GetName());
        $i = 0;
        while (isset($this->defenseArray[$i]))
        {
            $defense = $this->defenseArray[$i];
            if ($defense[0]->GetID() == $player->GetID())
            {
                $attack = $defense[1];
                $defenseValue = $player->GetDefense();
                $reflectValue = $player->GetReflect();

                if ($attack->GetDefValue() != 0)
                    $defenseValue = $player->GetDefense() / ($attack->GetValue() * $attack->GetDefValue() / 100);
                if ($attack->GetReflectValue() != 0)
                    $reflectValue = $player->GetReflect() - ($attack->GetValue() * $attack->GetReflectValue() / 100);

                $player->SetDefense($defenseValue);
                $this->AddDebugLog(' - - Revert Defense to: ' . number_format($defenseValue,'0', '', '.'));
                $player->SetReflect($reflectValue);
                break;
            }
            ++$i;
        }

        $trans = array_filter(explode(';', $player->GetTransformations()));
        $i = 0;
        while (isset($trans[$i]))
        {
            $transAttack = $this->GetAttack($trans[$i]);
            $player->Transform($transAttack, true);
            ++$i;
        }

        if (!$update)
        {
            return;
        }

        $this->UpdateTransform($player);

        $i = 0;
        while (isset($this->defenseArray[$i]))
        {
            $defense = $this->defenseArray[$i];
            if ($defense[0]->GetID() == $player->GetID())
            {
                $attack = $defense[1];
                $defenseValue = $player->GetDefense();
                $reflectValue = $player->GetReflect();

                if ($attack->GetDefValue() != 0)
                    $defenseValue = $player->GetDefense() * ($attack->GetValue() * $attack->GetDefValue() / 100);
                if ($attack->GetReflectValue() != 0)
                    $reflectValue = $player->GetReflect() + ($attack->GetValue() * $attack->GetReflectValue() / 100);

                $player->SetDefense($defenseValue);
                $this->AddDebugLog(' - -(2) Revert Defense to: ' . number_format($defenseValue,'0', '', '.'));
                $player->SetReflect($reflectValue);
                break;
            }
            ++$i;
        }
    }

    private function Transform($player, $attack)
    {
        $trans = array_filter(explode(';', $player->GetTransformations()));

        $i = 0;
        while (isset($trans[$i]))
        {
            if ($trans[$i] == $attack->GetID())
            {
                $player->Transform($attack, true);
                $this->UpdateTransform($player);
                //Revert
                return $attack->GetMissText();
            }
            $transAttack = $this->GetAttack($trans[$i]);

            if ($transAttack->GetTransformationID() == $attack->GetTransformationID())
            {
                //revert
                $player->Transform($transAttack, true);
            }
            ++$i;
        }

        $player->Transform($attack, false);
        $this->UpdateTransform($player);

        return $attack->GetText();
    }

    private function Defend($player, $attack)
    {
        $defense = $player->GetDefense();
        $reflect = $player->GetReflect();

        if ($attack->GetDefValue() != 0)
            $defense = $player->GetDefense() * ($attack->GetValue() * $attack->GetDefValue() / 100);
        if ($attack->GetReflectValue() != 0)
            $reflect = $player->GetReflect() + ($attack->GetValue() * $attack->GetReflectValue() / 100);

        $defensePlayer = array();
        $defensePlayer[0] = $player;
        $defensePlayer[1] = $attack;

        $this->defenseArray[] = $defensePlayer;
        $player->SetDefense($defense);
        $player->SetReflect($reflect);

        return $attack->GetText();
    }

    private function AbsoluteHeal($player, $damage, $attack)
    {
        $lp = $player->GetLP();
        if ($lp < $player->GetIncreasedLP())
        {
            $lp = $lp + round($damage * ($attack->GetLPValue() / 100));
            $lp = min($lp, $player->GetIncreasedLP());
            $player->SetLP($lp);
        }

        $kp = $player->GetKP();
        if ($kp < $player->GetIncreasedKP())
        {
            $kp = $kp + round($damage * ($attack->GetKPValue() / 100));
            $kp = min($kp, $player->GetIncreasedKP());
            $player->SetKP($kp);
        }

        $ep = $player->GetEnergy();
        if ($ep < $player->GetMaxEnergy())
        {
            $ep = $ep + round($damage * ($attack->GetEPValue() / 100));
            $ep = min($ep, $player->GetMaxEnergy());
            $player->SetEnergy($ep);
        }

        $this->database->Update('lp=' . $lp . ',kp=' . $kp . ',energy=' . $ep , 'fighters', 'id = ' . $player->GetID() , 1);
    }

    private function Heal($player, $target, $attack, &$damage, &$type)
    {
        if ($target->GetLP() == 0)
        {
            return $attack->GetDeadText();
        }

        $enemyKI = 0;

        $i = 0;
        while (isset($this->teams[$i]))
        {
            if ($i == $target->GetTeam())
            {
                ++$i;
                continue;
            }
            $this->AddDebugLog(' - Heal Checking Team ' . $i);

            $j = 0;
            $teamMembers = $this->teams[$i];
            while (isset($teamMembers[$j]))
            {

                $this->AddDebugLog(' - Heal Fighter: ' . $teamMembers[$j]->GetName());
                $this->AddDebugLog(' - Heal FighterKI: ' . number_format($teamMembers[$j]->GetKI(), '0', '', '.'));

                if ($teamMembers[$j]->GetKI() > $enemyKI)
                    $enemyKI = $teamMembers[$j]->GetKI();
                ++$j;
            }
            ++$i;
        }

        $this->AddDebugLog(' - Heal EnemyKI: ' . number_format($enemyKI, '0', '', '.'));

        if ($enemyKI == 0)
        {
            $this->AddDebugLog(' - - ERROR: EnemyKI is 0');
            $this->DebugSend(true);
        }

        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy() * ($player->GetAccuracy() / $target->GetReflex());

        if ($target->GetParalyzed() == 0 && $hit < $miss)
        {
            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }

        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';

        $enemyKI = $player->GetKI();

        if ($attack->IsProcentual())
        {
            $enemyValue = $enemyKI * ($attack->GetValue() / 100);
            $constValue = 0.2;
            $playerValue = $enemyValue + ($player->GetKI() * $constValue);

            if ($playerValue < $attack->GetMinValue())
            {
                $playerValue = $attack->GetMinValue();
            }
            $atkVal = $playerValue;
        }
        else
        {
            $atkVal = $attack->GetValue();
        }

        $damage = 0;

        $lpVal = $attack->GetLPValue();
        $lp = $target->GetLP();
        $count = 0;
        if ($lpVal != 0)
        {
            $lpDamage = round($atkVal * ($lpVal / 100));
            if ($lp < $target->GetIncreasedLP() || $lpDamage < 0)
            {
                $damage += $lpDamage;
                $lp = $lp + $lpDamage;
                $lp = min($lp, $target->GetIncreasedLP());
                $target->SetLP($lp);
                ++$count;
            }
        }

        $kpVal = $attack->GetKPValue();

        $kp = $target->GetKP();
        if ($kpVal != 0)
        {
            $kpDamage = round($atkVal * ($kpVal / 100));
            if ($kp < $target->GetIncreasedKP() || $kpDamage < 0)
            {
                $damage += $kpDamage;
                $kp = $kp + $kpDamage;
                $kp = min($kp, $target->GetIncreasedKP());
                $target->SetKP($kp);
                ++$count;
            }
        }

        $epVal = $attack->GetEPValue();

        $ep = $target->GetEnergy();
        if ($epVal != 0)
        {
            $epDamage = round($atkVal * ($epVal / 100));
            if ($ep < $target->GetMaxEnergy() || $epDamage < 0)
            {
                $damage += $epDamage;
                $ep = $ep + $epDamage;
                $ep = min($ep, $target->GetMaxEnergy());
                $target->SetEnergy($ep);
                ++$count;
            }
        }

        $crit = rand(0, 100);
        if ($attack->GetCritchance() > 0 && $crit <= $attack->GetCritchance() || $crit <= $player->GetCritchance())
        {
            $damage = $damage * $attack->GetCritdamage() * $player->GetCritdamage();
            $type = 'kritische';
        }

        if($count == 0)
            $count = 1; // Keine Division durch 0;
        $damage = round($damage / $count);


        $this->database->Update('lp=' . $lp . ',kp=' . $kp . ',energy=' . $ep , 'fighters', 'id = ' . $target->GetID() , 1);

        return $returnText;
    }

    private function SelfHeal($player, $attack, &$damage)
    {
        if ($player->GetLP() == 0)
        {
            return $attack->GetDeadText();
        }

        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy();

        if ($player->GetParalyzed() == 0 && $hit < $miss)
        {
            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }

        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';

        if ($attack->IsProcentual())
        {
            $playerValue = $player->GetKI() * ($attack->GetValue() / 100);
            if ($playerValue < $attack->GetMinValue())
            {
                $playerValue = $attack->GetMinValue();
            }
            $atkVal = $playerValue;
        }
        else
        {
            $atkVal = $attack->GetValue();
        }

        $damage = 0;

        $lpVal = $attack->GetLPValue();
        $lp = $player->GetLP();
        $count = 0;
        if ($lpVal != 0)
        {
            $lpDamage = round($atkVal * ($lpVal / 100));
            if ($lp < $player->GetIncreasedLP() || $lpDamage < 0)
            {
                $damage += $lpDamage;
                $lp = $lp + $lpDamage;
                $lp = min($lp, $player->GetIncreasedLP());
                $player->SetLP($lp);
                ++$count;
            }
        }

        $kpVal = $attack->GetKPValue();

        $kp = $player->GetKP();
        if ($kpVal != 0)
        {
            $kpDamage = round($atkVal * ($kpVal / 100));
            if ($kp < $player->GetIncreasedKP() || $kpDamage < 0)
            {
                $damage += $kpDamage;
                $kp = $kp + $kpDamage;
                $kp = min($kp, $player->GetIncreasedKP());
                $player->SetKP($kp);
                ++$count;
            }
        }

        $epVal = $attack->GetEPValue();

        $ep = $player->GetEnergy();
        if ($epVal != 0)
        {
            $epDamage = round($atkVal * ($epVal / 100));
            if ($ep < $player->GetMaxEnergy() || $epDamage < 0)
            {
                $damage += $epDamage;
                $ep = $ep + $epDamage;
                $ep = min($ep, $player->GetMaxEnergy());
                $player->SetEnergy($ep);
                ++$count;
            }
        }

        if ($count != 0)
        {
            $damage = round($damage / $count);
            $this->database->Update('lp=' . $lp . ',kp=' . $kp . ',energy=' . $ep , 'fighters', 'id = ' . $player->GetID() , 1);
        }

        return $returnText;
    }

    private function Revive($player, $target, $attack, &$damage)
    {
        $this->AddDebugLog(' - Revive: ' . $player->GetName() . ' on ' . $target->GetName());
        if ($target->GetLP() != 0)
        {
            return $attack->GetDeadText();
        }

        if ($player->GetLP() == 0 || $player->GetID() == $target->GetID())
        {
            return $attack->GetMissText();
        }

        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy() * ($player->GetAccuracy() / $target->GetReflex());

        if ($target->GetParalyzed() == 0 && $hit < $miss)
        {
            return $attack->GetMissText();
        }

        $returnText = $attack->GetText();

        $atkVal = $attack->GetValue();
        if ($attack->IsProcentual())
        {
            $atkVal = $player->GetKI() * ($attack->GetValue() / 100);
        }
        $this->AddDebugLog(' - $atkVal: ' . $atkVal);

        $damage = 0;

        $lp = $target->GetLP();
        $lpDamage = round($atkVal * ($attack->GetLPValue() / 100));
        $this->AddDebugLog(' - $lpDamage: ' . number_format($lpDamage, '0', '', '.'));
        $damage += $lpDamage;
        $lp = $lp + $lpDamage;
        if ($lp >= $target->GetIncreasedLP()) $lp = $target->GetIncreasedLP();
        $target->SetLP($lp);

        $kp = $target->GetKP();
        $kpDamage = round($atkVal * ($attack->GetKPValue() / 100));
        $this->AddDebugLog(' - $kpDamage: ' . number_format($kpDamage, '0', '', '.'));
        $damage += $kpDamage;
        $kp = $kp + $kpDamage;
        if ($kp >= $target->GetIncreasedKP()) $kp = $target->GetIncreasedKP();
        $target->SetKP($kp);

        $ep = $target->GetEnergy();
        $epDamage = round($atkVal * ($attack->GetEPValue() / 100));
        $this->AddDebugLog(' - $epDamage: ' . number_format($epDamage, '0', '', '.'));
        $damage += $epDamage;
        $ep = $ep + $epDamage;
        if ($ep >= $target->GetMaxEnergy()) $ep = $target->GetMaxEnergy();
        $target->SetEnergy($ep);

        $this->database->Update('lp=' . $lp . ',kp=' . $kp . ',energy=' . $ep , 'fighters', 'id = ' . $target->GetID() , 1);

        return $returnText;
    }

    private function Buff(&$player, &$target, &$attack)
    {
        $this->AddDebugLog(' - Buff by: ' . $player->GetName() . ' on ' . $target->GetName());
        $this->AddDebugLog(' - - Buffs: ' . $target->GetBuffs());
        $buffs = array();
        if ($target->GetBuffs() != '')
        {
            $buffs = explode(';', $target->GetBuffs());
        }
        $newBuffs = array();

        foreach ($buffs as &$buff)
        {
            $buffData = explode('@', $buff);
            if ($buffData[0] == $player->GetID() && $buffData[1] == $attack->GetID())
            {
                $this->AddDebugLog(' - - ReverseBuff because recasted');
                $this->ReverseBuff($target, $attack);
                if($target == $player)
                {
                    $text = '!source beendet ' . $attack->GetName() . ' bei sich.';
                }
                else
                {
                    $text = '!source beendet ' . $attack->GetName() . ' bei !target.';
                }

                return $text;
            }
            else
            {
                $this->AddDebugLog(' - - Add Buff');
                $newBuffs[] = $buff;
            }
        }

        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy() * ($player->GetAccuracy() / $target->GetReflex());
        if ((!$player->IsNPC() && count($newBuffs) >= $this->bufflimit) || ($target->GetParalyzed() == 0 && $hit < $miss && $attack->GetType() != 21)) // Selbstbuff ausgenommen
        {
            $this->AddDebugLog(' - - Missed');
            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }

        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';

        //Füge die Werte zusammen
        $atkDiff = round((($attack->GetValue() / 100) * $target->GetEquippedAttack()) * ($attack->GetAtkValue() / 100)); // Berechnung absolute Attack Buff
        $atk = $target->GetAttack() + $atkDiff;
        if ($atk < 1)
        {
            $atkDiff -= $atk - 1;
            $atk = 1;
        }

        $defDiff = round((($attack->GetValue() / 100) * $target->GetEquippedDefense()) * ($attack->GetDefValue() / 100)); //  Berechnung absolute Defense Buff
        $def = $target->GetDefense() + $defDiff;
        if ($def < 1)
        {
            $def = 1;
        }

        $lpDiff = round((($attack->GetValue() / 100) * $target->GetLP()) * ($attack->GetLPValue() / 100)); //  Berechnung absolute LP Buff
        $lp = $target->GetLP() + $lpDiff;
        if ($lp < 0)
        {
            $lpDiff -= $lp;
            $lp = 0;
        }
        $ilpDiff = round((($attack->GetValue() / 100) * $target->GetIncreasedLP()) * ($attack->GetLPValue() / 100)); //  Berechnung absolute iLP Buff
        $ilp = $target->GetIncreasedLP() + $ilpDiff;
        if ($ilp < 0)
        {
            $ilpDiff -= $ilp;
            $ilp = 0;
        }
        $kpDiff = round((($attack->GetValue() / 100) * $target->GetKP()) * ($attack->GetKPValue() / 100)); //  Berechnung absolute KP Buff
        $kp = $target->GetKP() + $kpDiff;
        if ($kp < 0)
        {
            $kpDiff -= $kp;
            $kp = 0;
        }
        $ikpDiff = round((($attack->GetValue() / 100) * $target->GetIncreasedKP()) * ($attack->GetKPValue() / 100)); //  Berechnung absolute iKP Buff
        $ikp = $target->GetIncreasedKP() + $ikpDiff;
        if ($ikp < 0)
        {
            $ikpDiff -= $ikp;
            $ikp = 0;
        }
        $ireflexDiff = round((($attack->GetValue() / 100) * $target->GetReflex()) * ($attack->GetReflexBuff() / 100));
        $ireflex = $target->GetReflex() + $ireflexDiff;
        if ($ireflex < 0)
        {
            $ireflexDiff -= $ireflex;
            $ireflex = 0;
        }
        $iaccDiff = round((($attack->GetValue() / 100) * $target->GetAccuracy()) * ($attack->GetAccBuff() / 100));
        $iacc = $target->GetAccuracy() + $iaccDiff;
        if ($iacc < 0)
        {
            $iaccDiff -= $iacc;
            $iacc = 0;
        }
        $itauntDiff = round((($attack->GetValue() / 100) * 1) * ($attack->GetTauntValue() / 100)); //  Berechnung absolute Taunt Buff
        //$itauntDiff = round($atkVal * ($attack->GetTauntValue() / 100));
        $this->AddDebugLog(' - - $itauntDiff: ' . $itauntDiff);
        $this->AddDebugLog(' - - Target Taunt: ' . $target->GetTaunt());
        $itaunt = $target->GetTaunt() + $itauntDiff;
        $this->AddDebugLog(' - - $itaunt: ' . $itaunt);
        if ($itaunt < 0)
        {
            $itauntDiff -= $itaunt;
            $itaunt = 0;
        }
        $ireflectDiff = $attack->GetReflectValue();
        $ireflect = $target->GetReflect() + $ireflectDiff;
        if ($ireflect < 0)
        {
            $ireflectDiff -= $ireflect;
            $ireflect = 0;
        }
        $buffData = $player->GetID() . '@' . $attack->GetID() .
            '@' . $attack->GetRounds() .
            '@' . $atkDiff .
            '@' . $defDiff .
            '@' . $lpDiff .
            '@' . $ilpDiff .
            '@' . $kpDiff .
            '@' . $ikpDiff .
            '@' . $itauntDiff .
            '@' . $ireflectDiff .
            '@' . $ireflexDiff .
            '@' . $iaccDiff;
        //'@' . $reflexvalueDiff;

        //Noch nicht ausgewählt, also füge sie hinzu
        $newBuffs[] = $buffData;

        //Setzte die Werte
        $target->SetAccuracy($iacc);
        $target->SetReflex($ireflex);
        $target->SetAttack($atk);
        $target->SetDefense($def);
        $target->SetLP($lp);
        $target->SetIncreasedLP($ilp);
        $target->SetKP($kp);
        $target->SetIncreasedKP($ikp);
        if($attack->GetAddAttacks() != '')
        {
            $target->AddAttacks(explode(';',$attack->GetAddAttacks()));
            $this->database->Update('attacks="' . $target->GetAttacks() . '"', 'fighters', 'id = ' . $target->GetID() , 1);
        }
        if($attack->GetRemoveAttacks() != '')
        {
            $target->RemoveAttacks(explode(';',$attack->GetRemoveAttacks()));
            $this->database->Update('attacks="' . $target->GetAttacks() . '"', 'fighters', 'id = ' . $target->GetID() , 1);
        }
        $this->AddDebugLog(' -- - Set Taunt of ' . $target->GetName() . ' to ' . $itaunt);
        if ($itaunt < 0)
        {
            $this->AddDebugLog(' - - ERROR: taunt is less than 0');
            $this->DebugSend(true);
            $itaunt = 0;
        }
        $target->SetTaunt($itaunt);
        $target->SetReflect($ireflect);
        $buffString = implode(';', $newBuffs);
        $target->SetBuffs($buffString);
        if($attack->GetNPCImage() != 'img/npc/.png')
        {
            $player->SetImage($attack->GetNPCImage());
            $result = $this->database->Update('charimage="' . $attack->GetNPCImage() . '"', 'fighters', 'id = ' . $player->GetID() , 1);
        }
        $this->AddDebugLog(' - - SetBuff: ' . $buffString);
        $this->AddDebugLog(' - - Defense: ' . $target->GetDefense());
        $this->UpdateTransform($target);

        return $returnText;
    }

    private function Debuff(&$player, &$target, &$attack)
    {
        $this->AddDebugLog(' - Debuff by: ' . $player->GetName() . ' on ' . $target->GetName());
        $this->AddDebugLog(' - - Debuffs: ' . $target->GetDebuffs());
        $debuffs = array();
        if ($target->GetDebuffs() != '')
        {
            $debuffs = explode(';', $target->GetDebuffs());
        }
        $newDebuffs = array();

        foreach ($debuffs as &$debuff)
        {
            $debuffData = explode('@', $debuff);
            if ($debuffData[0] == $player->GetID() && $debuffData[1] == $attack->GetID())
            {
                $this->AddDebugLog(' - - ReverseDebuff because recasted');
                $this->ReverseDebuff($target, $attack);
                if($target == $player)
                {
                    $text = '!source beendet ' . $attack->GetName() . ' bei sich.';
                }
                else
                {
                    $text = '!source beendet ' . $attack->GetName() . ' bei !target.';
                }

                return $text;
            }
            else
            {
                $this->AddDebugLog(' - - Add Debuff');
                $newDebuffs[] = $debuff;
            }
        }

        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy() * ($player->GetAccuracy() / $target->GetReflex());
        if ((!$player->IsNPC() && count($newDebuffs) >= $this->debufflimit) || ($target->GetParalyzed() == 0 && $hit < $miss && $attack->GetType() != 21)) // Selbstbuff ausgenommen
        {
            $this->AddDebugLog(' - - Missed');
            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }

        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';

        //Füge die Werte zusammen
        $atkDiff = round((($attack->GetValue() / 100) * $target->GetEquippedAttack()) * ($attack->GetAtkValue() / 100)); // Berechnung absolute Attack Buff
        $atk = $target->GetAttack() + $atkDiff;
        if ($atk < 1)
        {
            $atkDiff -= $atk - 1;
            $atk = 1;
        }

        $defDiff = round((($attack->GetValue() / 100) * $target->GetEquippedDefense()) * ($attack->GetDefValue() / 100)); //  Berechnung absolute Defense Buff
        $def = $target->GetDefense() + $defDiff;
        if ($def < 1)
        {
            $def = 1;
        }

        $lpDiff = round((($attack->GetValue() / 100) * $target->GetLP()) * ($attack->GetLPValue() / 100)); //  Berechnung absolute LP Buff
        $lp = $target->GetLP() + $lpDiff;
        if ($lp < 0)
        {
            $lpDiff -= $lp;
            $lp = 0;
        }
        $ilpDiff = round((($attack->GetValue() / 100) * $target->GetIncreasedLP()) * ($attack->GetLPValue() / 100)); //  Berechnung absolute iLP Buff
        $ilp = $target->GetIncreasedLP() + $ilpDiff;
        if ($ilp < 0)
        {
            $ilpDiff -= $ilp;
            $ilp = 0;
        }
        $kpDiff = round((($attack->GetValue() / 100) * $target->GetKP()) * ($attack->GetKPValue() / 100)); //  Berechnung absolute KP Buff
        $kp = $target->GetKP() + $kpDiff;
        if ($kp < 0)
        {
            $kpDiff -= $kp;
            $kp = 0;
        }
        $ikpDiff = round((($attack->GetValue() / 100) * $target->GetIncreasedKP()) * ($attack->GetKPValue() / 100)); //  Berechnung absolute iKP Buff
        $ikp = $target->GetIncreasedKP() + $ikpDiff;
        if ($ikp < 0)
        {
            $ikpDiff -= $ikp;
            $ikp = 0;
        }
        $ireflexDiff = round((($attack->GetValue() / 100) * $target->GetReflex()) * ($attack->GetReflexBuff() / 100));
        $ireflex = $target->GetReflex() + $ireflexDiff;
        if ($ireflex < 0)
        {
            $ireflexDiff -= $ireflex;
            $ireflex = 0;
        }
        $iaccDiff = round((($attack->GetValue() / 100) * $target->GetAccuracy()) * ($attack->GetAccBuff() / 100));
        $iacc = $target->GetAccuracy() + $iaccDiff;
        if ($iacc < 0)
        {
            $iaccDiff -= $iacc;
            $iacc = 0;
        }
        $itauntDiff = round(($attack->GetValue() / 100) * ($attack->GetTauntValue() / 100)); //  Berechnung absolute Taunt Buff
        //$itauntDiff = round($atkVal * ($attack->GetTauntValue() / 100));
        $this->AddDebugLog(' - - $itauntDiff: ' . $itauntDiff);
        $this->AddDebugLog(' - - Target Taunt: ' . $target->GetTaunt());
        $itaunt = $target->GetTaunt() + $itauntDiff;
        $this->AddDebugLog(' - - $itaunt: ' . $itaunt);
        if ($itaunt < 0)
        {
            $itauntDiff -= $itaunt;
            $itaunt = 0;
        }
        $ireflectDiff = $attack->GetReflectValue();
        $ireflect = $target->GetReflect() + $ireflectDiff;
        if ($ireflect < 0)
        {
            $ireflectDiff -= $ireflect;
            $ireflect = 0;
        }
        $debuffData = $player->GetID() . '@' . $attack->GetID() .
            '@' . $attack->GetRounds() .
            '@' . $atkDiff .
            '@' . $defDiff .
            '@' . $lpDiff .
            '@' . $ilpDiff .
            '@' . $kpDiff .
            '@' . $ikpDiff .
            '@' . $itauntDiff .
            '@' . $ireflectDiff .
            '@' . $ireflexDiff .
            '@' . $iaccDiff;
        //'@' . $reflexvalueDiff;

        //Noch nicht ausgewählt, also füge sie hinzu
        $newDebuffs[] = $debuffData;

        //Setzte die Werte
        $target->SetAccuracy($iacc);
        $target->SetReflex($ireflex);
        $target->SetAttack($atk);
        $target->SetDefense($def);
        $target->SetLP($lp);
        $target->SetIncreasedLP($ilp);
        $target->SetKP($kp);
        $target->SetIncreasedKP($ikp);
        if($attack->GetAddAttacks() != '')
        {
            $target->AddAttacks(explode(';',$attack->GetAddAttacks()));
            $this->database->Update('attacks="' . $target->GetAttacks() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
        }
        if($attack->GetRemoveAttacks() != '')
        {
            $target->RemoveAttacks(explode(';',$attack->GetRemoveAttacks()));
            $this->database->Update('attacks="' . $target->GetAttacks() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
        }
        $this->AddDebugLog(' -- - Set Taunt of ' . $target->GetName() . ' to ' . $itaunt);
        if ($itaunt < 0)
        {
            $this->AddDebugLog(' - - ERROR: taunt is less than 0');
            $this->DebugSend(true);
            $itaunt = 0;
        }
        $target->SetTaunt($itaunt);
        $target->SetReflect($ireflect);
        $debuffString = implode(';', $newDebuffs);
        $target->SetDebuffs($debuffString);
        if($attack->GetNPCImage() != 'img/npc/.png')
        {
            $player->SetImage($attack->GetNPCImage());
            $result = $this->database->Update('charimage="' . $attack->GetNPCImage() . '"', 'fighters', 'id = ' . $player->GetID(), 1);
        }
        $this->AddDebugLog(' - - SetDebuff: ' . $debuffString);
        $this->AddDebugLog(' - - Defense: ' . $target->GetDefense());
        $this->UpdateTransform($target);

        return $returnText;
    }

    private function ReverseBuff(&$target, &$attack)
    {
        $this->AddDebugLog(' - - ReverseBuff');
        $this->AddDebugLog(' - - - Buffs: ' . $target->GetBuffs());

        //Speichere die neuen VWs ab, falls der Spieler noch welche hat
        $buffs = array();
        if ($target->GetBuffs() != '')
        {
            $buffs = explode(';', $target->GetBuffs());
        }
        $newBuffs = array();
        foreach ($buffs as &$buff)
        {
            $this->AddDebugLog(' - - - Buff - ' . $buff);
            $buffData = explode('@', $buff);
            $this->AddDebugLog(' - - - Attack: ' . $buffData[1]);

            if ($buffData[1] != $attack->GetID())
            {
                $newBuffs[] = $buff;
            }
            else
            {
                //Füge die Werte zusammen
                $atk = $target->GetAttack() - $buffData[3];
                $def = $target->GetDefense() - $buffData[4];
                $lp = $target->GetLP() - $buffData[5];
                $ilp = $target->GetIncreasedLP() - $buffData[6];
                $kp = $target->GetKP() - $buffData[7];
                $ikp = $target->GetIncreasedKP() - $buffData[8];
                $itaunt = $target->GetTaunt() - $buffData[9];
                $ireflect = $target->GetReflect() - $buffData[10];
                $reflex = $target->GetReflex() - $buffData[11];
                $iacc = $target->GetAccuracy() - $buffData[12];
                //Wenn der Spieler dadurch stirbt, setzte Leben auf 0
                if ($lp < 0)
                {
                    $lp = 0;
                }
                if ($kp < 0)
                {
                    $kp = 0;
                }
                if($def < 0)
                {
                    $def = 1;
                }
                if($atk < 0)
                {
                    $atk = 1;
                }
                if($attack->GetNPCImage() != 'img/npc/.png' && $target->GetImage() == $attack->GetNPCImage())
                {
                    if($target->IsNPC())
                    {
                        $npc = new NPC($this->database, $target->GetNPC());
                        $target->SetImage($npc->GetImage());
                        $this->database->Update('charimage="' . $npc->GetImage() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
                    }
                    else
                    {
                        $targetplayer = new Player($this->database, $target->GetAcc());
                        $target->SetImage($targetplayer->GetImage());
                        $this->database->Update('charimage="' . $targetplayer->GetImage() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
                    }
                }
                $target->SetAccuracy($iacc);
                $target->SetReflex($reflex);
                $target->SetAttack($atk);
                $target->SetDefense($def);
                $target->SetLP($lp);
                $target->SetIncreasedLP($ilp);
                if($attack->GetAddAttacks() != '')
                {
                    $target->RemoveAttacks(explode(';',$attack->GetAddAttacks()));
                    $this->database->Update('attacks="' . $target->GetAttacks() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
                }
                if($attack->GetRemoveAttacks() != '')
                {
                    $target->AddAttacks(explode(';',$attack->GetRemoveAttacks()));
                    $this->database->Update('attacks="' . $target->GetAttacks() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
                }
                $target->SetKP($kp);
                $target->SetIncreasedKP($ikp);
                $this->AddDebugLog(' - - - Set Taunt of ' . $target->GetName() . ' to ' . $itaunt);
                $target->SetTaunt($itaunt);
                $target->SetReflect($ireflect);
            }
        }
        $buffString = implode(';', $newBuffs);
        $this->AddDebugLog(' - - - SetBuff: ' . $buffString);
        $target->SetBuffs($buffString);
        $this->UpdateTransform($target);
    }

    private function ReverseDebuff(&$target, &$attack)
    {
        $this->AddDebugLog(' - - ReverseDebuff');
        $this->AddDebugLog(' - - - Debuffs: ' . $target->GetDebuffs());

        //Speichere die neuen VWs ab, falls der Spieler noch welche hat
        $debuffs = array();
        if ($target->GetDebuffs() != '')
        {
            $debuffs = explode(';', $target->GetDebuffs());
        }
        $newDebuffs = array();
        foreach ($debuffs as &$debuff)
        {
            $this->AddDebugLog(' - - - Debuff - ' . $debuff);
            $debuffData = explode('@', $debuff);
            $this->AddDebugLog(' - - - Attack: ' . $debuffData[1]);

            if ($debuffData[1] != $attack->GetID())
            {
                $newDebuffs[] = $debuff;
            }
            else
            {
                //Füge die Werte zusammen
                $atk = $target->GetAttack() - $debuffData[3];
                $def = $target->GetDefense() - $debuffData[4];
                $lp = $target->GetLP() - $debuffData[5];
                $ilp = $target->GetIncreasedLP() - $debuffData[6];
                $kp = $target->GetKP() - $debuffData[7];
                $ikp = $target->GetIncreasedKP() - $debuffData[8];
                $itaunt = $target->GetTaunt() - $debuffData[9];
                $ireflect = $target->GetReflect() - $debuffData[10];
                $reflex = $target->GetReflex() - $debuffData[11];
                $iacc = $target->GetAccuracy() - $debuffData[12];
                //Wenn der Spieler dadurch stirbt, setzte Leben auf 0
                if ($lp < 0)
                {
                    $lp = 0;
                }
                if ($kp < 0)
                {
                    $kp = 0;
                }
                if($def < 0)
                {
                    $def = 1;
                }
                if($atk < 0)
                {
                    $atk = 1;
                }
                if($attack->GetNPCImage() != 'img/npc/.png' && $target->GetImage() == $attack->GetNPCImage())
                {
                    if($target->IsNPC())
                    {
                        $npc = new NPC($this->database, $target->GetNPC());
                        $target->SetImage($npc->GetImage());
                        $this->database->Update('charimage="' . $npc->GetImage() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
                    }
                    else
                    {
                        $targetplayer = new Player($this->database, $target->GetAcc());
                        $target->SetImage($targetplayer->GetImage());
                        $this->database->Update('charimage="' . $targetplayer->GetImage() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
                    }
                }
                $target->SetAccuracy($iacc);
                $target->SetReflex($reflex);
                $target->SetAttack($atk);
                $target->SetDefense($def);
                $target->SetLP($lp);
                $target->SetIncreasedLP($ilp);
                if($attack->GetAddAttacks() != '')
                {
                    $target->RemoveAttacks(explode(';',$attack->GetAddAttacks()));
                    $this->database->Update('attacks="' . $target->GetAttacks() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
                }
                if($attack->GetRemoveAttacks() != '')
                {
                    $target->AddAttacks(explode(';',$attack->GetRemoveAttacks()));
                    $this->database->Update('attacks="' . $target->GetAttacks() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
                }
                $target->SetKP($kp);
                $target->SetIncreasedKP($ikp);
                $this->AddDebugLog(' - - - Set Taunt of ' . $target->GetName() . ' to ' . $itaunt);
                $target->SetTaunt($itaunt);
                $target->SetReflect($ireflect);
            }
        }
        $debuffString = implode(';', $newDebuffs);
        $this->AddDebugLog(' - - - SetDebuff: ' . $debuffString);
        $target->SetDebuffs($debuffString);
        $this->UpdateTransform($target);
    }

    private function UnParalyze($player, $target, $attack)
    {
        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy() * ($player->GetAccuracy() / $target->GetReflex());

        if ($target->GetParalyzed() == 0 && $hit < $miss)
        {
            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }

        $paralyzed = $target->GetParalyzed() - $attack->GetValue();
        if ($paralyzed < 0)
            $paralyzed = 0;

        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';
        $target->SetParalyzed($paralyzed);
        $this->AddDebugLog(' - UnParalyze: ' . $paralyzed);
        $this->database->Update('paralyzed=' . $paralyzed , 'fighters', 'id = ' . $target->GetID(), 1);
        return $returnText;
    }

    private function Dots(&$fighter, &$target, &$attack)
    {
        $dots = array();
        if ($target->GetDots() != '')
        {
            $dots = explode(';', $target->GetDots());
        }
        $newDots = array();

        foreach ($dots as &$dot)
        {
            $dotData = explode('@', $dot);
            if ($dotData[0] == $fighter->GetID() && $dotData[1] == $attack->GetID())
            {
                $this->ReverseDots($target, $attack);
                return '!source beendet ' . $attack->GetName() . ' bei !target.';
            }
            else
            {
                $newDots[] = $dot;
            }
        }

        $miss = rand(0, 100);
        $hit = $attack->GetAccuracy() * ($fighter->GetAccuracy() / $target->GetReflex());

        if ($target->GetParalyzed() == 0 && $hit < $miss)
        {
            return $attack->GetMissText().' Trefferquote: '.$miss.'%';
        }

        $returnText = $attack->GetText().' Trefferquote: '.$miss.'%';

        $dotData = $fighter->GetID() . '@' . $attack->GetID() . '@' . $attack->GetRounds();

        //Noch nicht ausgewählt, also füge sie hinzu
        $newDots[] = $dotData;

        //Setzte die Werte
        $target->SetDots(implode(';', $newDots));
        $this->database->Update('dots="' . $target->GetDots() . '"', 'fighters', 'id = ' . $target->GetID(), 1);

        return $returnText;
    }

    private function ReverseDots( &$target, &$attack)
    {
        $dots = array();
        if ($target->GetDots() != '')
        {
            $dots = explode(';', $target->GetDots());
        }
        $newDots = array();
        foreach ($dots as &$dot)
        {
            $dotData = explode('@', $dot);

            if ($dotData[0] != $target->GetID() && $dotData[1] != $attack->GetID())
            {
                $newDots[] = $dot;
            }
        }
        $target->SetDots(implode(';', $newDots));
        $this->database->Update('dots="' . $target->GetDots() . '"', 'fighters', 'id = ' . $target->GetID(), 1);
    }

    private function LoadFight($id): void
    {
        if($this->lastfights)
            $result = $this->database->Select('*', 'lastfights', 'id=' . $id, 1);
        else
            $result = $this->database->Select('*', 'fights', 'id=' . $id, 1);
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

    private function LoadFighters($player): void
    {
        $result = $this->database->Select('*', 'fighters', 'fight=' . $this->GetID(), 999, 'team');
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                while ($row = $result->fetch_assoc())
                {
                    $this->AddFighter($row, $player);
                }
            }
            $result->close();
        }
    }

    private function AddFighter($data, $player = null)
    {
        $fighter = new Fighters($data);
        $fighterTeam = $fighter->GetTeam();
        if (!isset($this->teams[$fighterTeam]))
        {
            $this->teams[$fighterTeam] = array();
        }
        if ($player != null && !$fighter->IsInactive() && ($player->GetID() == $fighter->GetAcc() || $player->GetID() == $fighter->GetFusedAcc()))
        {
            $this->player = $fighter;
        }
        $this->teams[$fighterTeam][] = $fighter;
        return $fighter;
    }

    private function RemoveFighter($playerID, $isNPC = false): void
    {
        $i = 0;
        while (isset($this->teams[$i]))
        {
            $j = 0;
            $teamMembers = $this->teams[$i];
            while (isset($teamMembers[$j]))
            {
                $fighter = $teamMembers[$j];
                if ($fighter->GetAcc() == $playerID && !$isNPC || $fighter->GetID() == $playerID && $isNPC)
                {

                    //Remove fighter from Team
                    array_splice($this->teams[$i], $j, 1);
                    //count teamMembers, if zero, this team is empty and can be removed
                    if (count($this->teams[$i]) == 0)
                    {
                        unset($this->teams[$i]);
                    }
                    break;
                }
                ++$j;
            }
            ++$i;
        }
    }

    public function GetMembersOfTeam($team)
    {
        $teams = count($this->teams);
        if ($team >= $teams)
            return 0;

        return count($this->teams[$team]);
    }

    public function GetTeamOfGroup($group)
    {
        $i = 0;
        $team = count($this->teams);
        if ($group == null)
        {
            return $team;
        }

        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            while (isset($players[$j]))
            {
                $playerFighter = $players[$j];
                if (in_array($playerFighter->GetAcc(), $group))
                {
                    return $i;
                }
                ++$j;
            }
            ++$i;
        }
        return $team;
    }

    public function ForceJoin($player, $team)
    {
        $this->AddDebugLog(round(microtime(true) * 1000) .' Time - Force Join ' . $player->GetName() . ' into Team: ' . $team);
        $this->CreateFighter($player, $team, false);
        $this->IncMode($team);
    }

    public function Join($joiningPlayer, $team, $isNPC = false)
    {
        $this->AddDebugLog(round(microtime(true) * 1000) .' Time - Join ' . $joiningPlayer->GetName() . ' into Team: ' . $team);
        if (!$isNPC && $joiningPlayer->GetFight() != 0 || $this->IsStarted())
        {
            $this->AddDebugLog($joiningPlayer->GetName() . ' already infight!');
            return false;
        }

        if($this->GetType() == 1 && $team == 1 || $this->GetType() == 8 && $team == 1)
        {
            $this->SetState(1);
            $this->database->Update('state=1, debuglog="'.$this->GetDebugLog().'"', 'fights', 'id = ' . $this->GetID(), 1);
        }

        $this->AddDebugLog('Join');
        $this->CreateFighter($joiningPlayer, $team, $isNPC);

        $mode = $this->GetMode();
        $teams = $this->GetTeams();
        $i = 0;
        $full = true;
        while (isset($mode[$i]))
        {
            if (!isset($teams[$i]) || $mode[$i] != count($teams[$i]))
            {
                $full = false;
                break;
            }
            ++$i;
        }

        $roundText = '<tr><td align=center colspan=4><h2>Kampf Beginn</h2></td></tr>';

        if ($full)
        {
            $this->AddDebugLog('- Fight is full!');
            $imageLeft = true;
            $i = 0;
            $highestKI = 0;
            $hasNPCs = false;
            while (isset($this->teams[$i]))
            {
                $players = $this->teams[$i];
                $j = 0;
                while (isset($players[$j]))
                {
                    $teamPlayer = $players[$j];

                    $trans = $teamPlayer->GetTransformations();
                    $teamPlayer->SetTransformations('');
                    if ($trans != '')
                    {
                        $transformations = explode(';', $trans);
                        $powerup = $transformations[0];
                        $attack = $this->attackManager->GetAttack($powerup);
                        $attackImage = $attack->GetImage();
                        $playerText = $this->Transform($teamPlayer, $attack);
                    }
                    else
                    {
                        $playerText = "!source macht sich zum Kampf bereit.";
                        $attackImage = $teamPlayer->GetImage();
                    }

                    $playerKI = $teamPlayer->GetMaxKI();
                    $attacks = explode(';', $teamPlayer->GetAttacks());
                    foreach ($attacks as &$attackID)
                    {
                        $attack = $this->attackManager->GetAttack($attackID);
                        if ($attack->GetType() == 4)
                        {
                            $vwKI = $teamPlayer->GetMaxKI() * (1 + ($attack->GetValue() / 100));
                            //$vwReflex = $teamPlayer->GetReflex + $attack->GetReflexValue;
                            if ($vwKI > $playerKI)
                                $playerKI = $vwKI;
                        }
                    }

                    if (!$teamPlayer->IsNPC() && $playerKI > $highestKI)
                        $highestKI = $playerKI;

                    if($teamPlayer->IsNPC())
                        $hasNPCs = true;

                    $playerText = $this->ReplaceTextValues($playerText, $teamPlayer, $teamPlayer, 0, 4);
                    $attackText = $this->DisplayAttackText($attackImage, $playerText, $imageLeft);
                    $roundText = $roundText . $attackText;
                    $imageLeft = !$imageLeft;
                    ++$j;
                }
                ++$i;
            }
            $this->AddDebugLog('- Highest KI: ' . number_format($highestKI, '0', '', '.'));

            $i = 0;
            if($hasNPCs)
            {
                while (isset($this->teams[$i]))
                {
                    $players = $this->teams[$i];
                    $j = 0;
                    while (isset($players[$j]))
                    {
                        $teamPlayer = $players[$j];
                        if ($teamPlayer->IsNPC() && $teamPlayer->IsStatsProcentual())
                        {
                            $this->AddDebugLog('- NPC: ' . $teamPlayer->GetName());
                            $lp = ($teamPlayer->GetMaxLP() / 100) * $highestKI;
                            $lp = round($lp) * 10;
                            $this->AddDebugLog('- - New LP: ' . number_format($lp, '0', '', '.'));
                            $kp = ($teamPlayer->GetMaxKP() / 100) * $highestKI;
                            $kp = round($kp) * 10;
                            $this->AddDebugLog('- - New AD: ' . number_format($kp, '0', '', '.'));
                            $atk = ($teamPlayer->GetMaxAttack() / 100) * $highestKI;
                            $atk = round($atk);
                            $this->AddDebugLog('- - New Atk: ' . number_format($atk, '0', '', '.'));
                            $def = ($teamPlayer->GetMaxDefense() / 100) * $highestKI;
                            $def = round($def);
                            $this->AddDebugLog('- - New Def: ' . number_format($def, '0', '', '.'));
                            //$ki = round((($lp / 10) + ($kp / 10) + $atk + $def) / 4);  TODO: Reupload
                            $ki = round((($lp / 10) + ($kp / 10) + ($atk / 2) + $def) / 4);
                            $this->AddDebugLog('- - New KI: ' . number_format($ki, '0', '', '.'));
                            $this->database->Update('ki=' . $ki . ',mki=' . $ki . ',lp=' . $lp . ',mlp=' . $lp . ',ilp=' . $lp . ',kp=' . $kp . ',mkp=' . $kp . ',ikp=' . $kp . ',attack=' . $atk . ',mattack=' . $atk . ',defense=' . $def . ',mdefense=' . $def, 'fighters', 'id = ' . $teamPlayer->GetID(), 1);
                        }
                        ++$j;
                    }
                    ++$i;
                }
            }

            $newText = $this->database->EscapeString($roundText . $this->GetText());
            $this->SetText($newText);
            $this->SetState(1);
            $this->database->Update('state=1,text="' . $newText . '", debuglog="'.$this->GetDebugLog().'"', 'fights', 'id = ' . $this->GetID(), 1);
            $this->database->Update('lastaction="' . date('Y-m-d H:i:s') . '"', 'fighters', 'fight = ' . $this->GetID(), 9999999);
            $this->attackManager = new AttackManager($this->database);
            $this->AddDebugLog('FIGHT START - ' . round(microtime(true) * 1000) .' Time');
        }

        return true;
    }

    public function DeleteFightAndFighters()
    {
        $this->database->Update('fight=0', 'accounts', 'fight = ' . $this->GetID(), 9999);
        $this->database->Update('eventinvite=0', 'accounts', 'eventinvite = ' . $this->GetID(), 9999);
        $this->database->Delete('fights', 'id=' . $this->GetID());
        $this->database->Delete('fighter', 'fight=' . $this->GetID(), 99999);
    }

    public function Leave($leavingPlayer)
    {
        if ($leavingPlayer->GetFight() != $this->GetID() || $this->IsStarted())
        {
            return false;
        }

        $this->DeleteFighter($leavingPlayer);
        if ($this->GetType() == 3 && $this->IsInGainAccs($leavingPlayer->GetID()) && $this->GetState() != 0) //NPC
        {
            if(!$leavingPlayer->IsDonator()) {
                $leavingPlayer->SetDailyNPCFights($leavingPlayer->GetDailyNPCFights() - 1);
                $this->AddDebugLog("6422 Set Elo-Points from " . $leavingPlayer->GetEloPoints() . " to " . ($leavingPlayer->GetEloPoints() - 0));
                $leavingPlayer->SetEloPoints($leavingPlayer->GetEloPoints() - 0);
            }
            else {
                $leavingPlayer->SetDailyNPCFights($leavingPlayer->GetDailyNPCFights() - 2);
                $this->AddDebugLog("6427 Set Elo-Points from " . $leavingPlayer->GetEloPoints() . " to " . ($leavingPlayer->GetEloPoints() - 0));
                $leavingPlayer->SetEloPoints($leavingPlayer->GetEloPoints() - 0);
            }
            $this->database->Update('elopoints='.$leavingPlayer->GetEloPoints(), 'accounts', 'id='.$leavingPlayer->GetID());
            $leavingPlayer->UpdateDailyNPCFights();
        }
        if ($this->GetType() == 5 && $this->IsInGainAccs($leavingPlayer->GetID()) && $this->GetState() != 0)
        {
            $event = new Event($this->database, $this->GetEvent());
            $event->RemoveFinishedPlayers($leavingPlayer->GetID());
        }
        $this->RemoveGainAcc($leavingPlayer->GetID());

        $leftTeams = count($this->GetTeams());
        if ($this->GetType() == 3 && $leftTeams == 1 || $this->GetType() != 3 && $leftTeams == 0)
        {
            $this->database->Delete('fights', 'id=' . $this->GetID());

            if($this->GetType() == 11)
            {
                $this->database->Update('challengefight=0, challengedtime="'.date('Y-m-d H:i:s').'"', 'clans', 'challengefight='.$this->GetID());
                $this->database->Update('challengedpopup=0', 'accounts', 'clan='.$this->GetChallenge());
            }

            if ($this->GetType() == 2)
                $this->database->Delete('fighter', 'fight=' . $this->GetID());
        }

        return true;
    }

    public function GetAttack($id)
    {
        return $this->attackManager->GetAttack($id);
    }

    public function IsInGainAccs($id)
    {
        $gainAccs = explode(';', $this->GetGainAccs());
        if (in_array($id, $gainAccs))
            return true;

        return false;
    }

    public function UpdateGainAcc($gainAccs)
    {
        $this->SetGainAccs($gainAccs);
        $this->database->Update('gainaccs="' . $gainAccs . '"', 'fights', 'id = ' . $this->GetID() , 1);
    }

    public function AddGainAcc($id)
    {
        $gainAccs = $id;
        if ($this->GetGainAccs() != '')
        {
            $gainAccs = explode(';', $this->GetGainAccs());
            if (in_array($id, $gainAccs))
            {
                return;
            }
            $gainAccs[] = $id;
            $gainAccs = implode(';', $gainAccs);
        }

        $this->SetGainAccs($gainAccs);
        $this->database->Update('gainaccs="' . $gainAccs . '"', 'fights', 'id = ' . $this->GetID() , 1);
    }

    public function RemoveGainAcc($id)
    {
        $gainAccs = '';
        if ($this->GetGainAccs() == '')
        {
            return;
        }
        else if ($this->GetGainAccs() != $id)
        {
            $gainAccs = explode(';', $this->GetGainAccs());
            if (!in_array($id, $gainAccs))
            {
                return;
            }
            array_splice($gainAccs, array_search($id, $gainAccs), 1);
            $gainAccs = implode(';', $gainAccs);
        }

        $this->SetGainAccs($gainAccs);
        $this->database->Update('gainaccs="' . $gainAccs . '"', 'fights', 'id = ' . $this->GetID() , 1);
    }

    public function GetFighter($id) : ?Fighters
    {
        $i = 0;
        while (isset($this->teams[$i]))
        {
            $players = $this->teams[$i];
            $j = 0;
            while (isset($players[$j]))
            {
                if ($players[$j]->GetID() == $id)
                {
                    return $players[$j];
                }
                ++$j;
            }
            ++$i;
        }

        return null;
    }

    public function DeleteFight()
    {
        $this->database->Delete('fights', 'id=' . $this->GetID());
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

    public function GetTournament()
    {
        return $this->data['tournament'];
    }

    public function SetTournament($value)
    {
        $this->data['tournament'] = $value;
    }

    public function GetPlace()
    {
        return $this->data['place'];
    }

    public function SetPlace($value)
    {
        $this->data['place'] = $value;
    }

    public function GetPlanet()
    {
        return $this->data['planet'];
    }

    public function SetPlanet($value)
    {
        $this->data['planet'] = $value;
    }

    public function GetStory()
    {
        return $this->data['story'];
    }

    public function SetStory($value)
    {
        $this->data['story'] = $value;
    }

    public function GetSideStory()
    {
        return $this->data['sidestory'];
    }

    public function SetSideStory($value)
    {
        $this->data['sidestory'] = $value;
    }

    public function GetMode()
    {
        return explode('vs', $this->data['mode']);
    }

    public function SetMode($value)
    {
        $this->data['mode'] = $value;
    }

    public function GetType()
    {
        return $this->data['type'];
    }

    public function SetType($value)
    {
        $this->data['type'] = $value;
    }

    public function GetTeams()
    {
        return $this->teams;
    }

    public function IsHealing()
    {
        return $this->data['healing'] != 0;
    }

    public function IsValid()
    {
        return $this->valid;
    }

    public function IsStarted()
    {
        return $this->data['state'] != 0;
    }

    public function IsEnded()
    {
        return $this->data['state'] == 2;
    }

    public function GetState()
    {
        return $this->data['state'];
    }

    public function GetSurvivalRounds()
    {
        return $this->data['survivalrounds'];
    }

    public function SetSurvivalRounds($value)
    {
        $this->data['survivalrounds'] = $value;
    }

    public function GetSurvivalTeam()
    {
        return $this->data['survivalteam'];
    }

    public function SetSurvivalTeam($value)
    {
        $this->data['survivalteam'] = $value;
    }

    public function GetSurvivalWinner()
    {
        return $this->data['survivalwinner'];
    }

    public function SetSurvivalWinner($value)
    {
        $this->data['survivalwinner'] = $value;
    }

    public function GetChallenge()
    {
        return $this->data['challenge'];
    }

    public function SetChallenge($value)
    {
        $this->data['challenge'] = $value;
    }

    public function SetState($value)
    {
        $this->data['state'] = $value;
    }

    public function GetRound()
    {
        return $this->data['round'];
    }

    public function GetEvent()
    {
        return $this->data['event'];
    }

    public function IsTestFight()
    {
        return $this->data['testfight'];
    }

    public function GetEventFight()
    {
        return $this->data['eventfight'];
    }

    public function GetLevelup()
    {
        return $this->data['levelup'];
    }

    public function SetLevelup($value)
    {
        $this->data['levelup'] = $value;
    }

    public function GetBerry()
    {
        return $this->data['zeni'];
    }

    public function GetMünzen()
    {
        return $this->data['münzen'];
    }

    public function SetMünzen($value)
    {
        $this->data['münzen'] = $value;
    }

    public function SetBerry($value)
    {
        $this->data['zeni'] = $value;
    }

    public function GetGold()
    {
        return $this->data['gold'];
    }

    public function SetGold($value)
    {
        $this->data['gold'] = $value;
    }

    public function GetPvP()
    {
        return $this->data['kopfgeld'];
    }

    public function SetPvP($value)
    {
        $this->data['kopfgeld'] = $value;
    }

    public function GetGainAccs()
    {
        return $this->data['gainaccs'];
    }

    public function SetGainAccs($value)
    {
        $this->data['gainaccs'] = $value;
    }

    public function GetItems()
    {
        return $this->data['items'];
    }

    public function SetItems($value)
    {
        $this->data['items'] = $value;
    }

    public function SetRound($value)
    {
        $this->data['round'] = $value;
    }

    public function GetText()
    {
        return $this->data['text'];
    }

    public function SetText($text)
    {
        $this->data['text'] = $text;
    }

    public function GetHealthRatio()
    {
        return $this->data['healthratio'];
    }

    public function SetHealthRatio($text)
    {
        $this->data['healthratio'] = $text;
    }

    public function GetHealthRatioTeam()
    {
        return $this->data['healthratioteam'];
    }

    public function SetHealthRatioTeam($text)
    {
        $this->data['healthratioteam'] = $text;
    }

    public function GetHealthRatioWinner()
    {
        return $this->data['healthratiowinner'];
    }

    public function GetTime()
    {
        return $this->data['time'];
    }

    public function SetHealthRatioWinner($text)
    {
        $this->data['healthratiowinner'] = $text;
    }

    public function IsMirror()
    {
        return $this->data['mirror'];
    }

    public function SetMirror($value)
    {
        $this->data['mirror'] = $value;
    }

    public function GetDebugLog()
    {
        return $this->data['debuglog'];
    }

    public function SetDebugLog($text)
    {
        $this->data['debuglog'] = $text;
    }

    public function GetFighters()
    {
        return $this->data['fighters'];
    }

    public function SetFighters($text)
    {
        $this->data['fighters'] = $text;
    }

    public function GetPlayer() : ?Fighters
    {
        return $this->player;
    }

    public function GetNPCAttacks(&$override)
    {
        $this->AddDebugLog('GetNPCAttacks');
        $override = 0;
        $attacks = array();
        for ($i = 0; $i < 10; ++$i)
        {
            $this->AddDebugLog(' - Team: ' . $i);
            if (!isset($this->teams[$i]))
                continue;

            $j = 0;
            $teamMembers = $this->teams[$i];
            while (isset($teamMembers[$j]))
            {
                $this->AddDebugLog(' - Member: ' . $j);
                if ($teamMembers[$j]->GetNPC() != 0)
                {
                    $this->AddDebugLog(' - IS NPC, add Attacks');
                    $npc = new NPC($this->database, $teamMembers[$j]->GetNPC(), 1);
                    if ($npc->GetOverrideAttacks() != 0)
                    {
                        $override = $npc->GetOverrideAttacks();
                        $this->AddDebugLog(' - Override Attacks: ' . $override);
                    }

                    $playerAttacks = $npc->GetPlayerAttack();
                    $this->AddDebugLog(' - Attacks: ' . implode(';', $playerAttacks));
                    foreach ($playerAttacks as &$playerAttack)
                    {
                        $attacks[] = $playerAttack;
                    }
                }
                ++$j;
            }
        }

        $this->AddDebugLog(' - NPC Attacks: ' . count($attacks));

        return $attacks;
    }

    public function CreateFighter($player, $team, $isNPC, $owner = 0, $fusion = 0, $fusetimer = 0)
    {
        $this->AddDebugLog('CreateFighter - ' . round(microtime(true) * 1000) . ' Time');
        $this->AddDebugLog(' - Name: ' . $player->GetName());
        if ($this->GetType() != 1 && !$isNPC)
        {
            $override = 0;
            $playerAttacks = $this->GetNPCAttacks($override);
            if (count($playerAttacks) != 0)
            {
                if ($override == 1)
                {
                    $player->SetFightAttacks('');
                    $this->AddDebugLog(' - Override all Attacks');
                }
                else if ($override == 2)
                {
                    $this->AddDebugLog(' - Override without Powerup');
                    $previousAttacks = explode(';', $player->GetFightAttacks());
                    $player->SetFightAttacks('');
                    foreach ($previousAttacks as &$previousAttackID)
                    {
                        $previousAttack = $this->GetAttack($previousAttackID);
                        if ($previousAttack->GetType() == 4)
                        {
                            $playerAttacks[] = $previousAttackID;
                            $this->AddDebugLog(' - Add: ' . $previousAttack->GetName());
                        }
                    }
                }
                $player->AddFightAttack($playerAttacks);
                $this->AddDebugLog(' - FightAttacks: ' .implode(";", $playerAttacks));
            }
        }

        if(!$isNPC && $player->GetClan() != 0 && $this->GetType() != 4 && $this->GetType() != 10 && $this->GetType() != 13 && $this->GetType() != 0)
        {
            $clan = new Clan($this->database, $player->GetClan());
            $mlp = floor($player->GetMaxLP() + $player->GetMaxLP() / 100 * 0.5 * ($clan->GetLP() + $clan->GetLevel()));
            $mkp = floor($player->GetMaxKP() + $player->GetMaxKP() / 100 * 0.5 * ($clan->GetAD() + $clan->GetLevel()));
            $lp = floor($player->GetLP() + $player->GetLP() / 100 * 0.5 * ($clan->GetLP() + $clan->GetLevel()));
            $kp = floor($player->GetKP() + $player->GetKP() / 100 * 0.5 * ($clan->GetAD() + $clan->GetLevel()));
            $atk = floor($player->GetAttack() + $player->GetAttack() / 100 * 0.5 * ($clan->GetAttack() + $clan->GetLevel()));
            $def = floor($player->GetDefense() + $player->GetDefense() / 100 * 0.5 * ($clan->GetDefense() + $clan->GetLevel()));
        }
        else
        {
            $mlp = $player->GetMaxLP();
            $mkp = $player->GetMaxKP();
            $lp = $player->GetLP();
            $kp = $player->GetKP();
            $atk = $player->GetAttack();
            $def = $player->GetDefense();
        }

        $ilp = $mlp;
        $ikp = $mkp;

        if ($fusion == 0 && ($this->GetType() == 0 || $this->GetType() == 6 || $this->GetType() == 8)) // Spaß oder Turnier oder Arena
        {
            $lp = $mlp;
            $kp = $mkp;
        }

        $id = $player->GetID();
        if ($isNPC)
        {
            $id = -1;
        }

        $critchance = $player->GetCritchance();
        $critdmg = $player->GetCritdamage();
        if (!$isNPC && $this->GetType() != 8 && $this->GetType() != 6)
        {
            $equippedStats = explode(';', $player->GetEquippedStats());
            $titelStats = explode(';', $player->GetTitelStats());
            $lp += intval($equippedStats[0]) + intval($titelStats[0]);
            $ilp += intval($equippedStats[0]) + intval($titelStats[0]);
            $kp += intval($equippedStats[1]) + intval($titelStats[1]);
            $ikp += intval($equippedStats[1]) + intval($titelStats[1]);
            $atk += intval($equippedStats[2]) + intval($titelStats[2]);
            $def += intval($equippedStats[3]) + intval($titelStats[3]);
            //TODO: Aza CritEquip & CritTitel
            //$critchance += $equippedStats[4] + $titelStats[4];
            //$critdmg += $equippedStats[5] + $titelStats[5];
        }

        $attacks = array();
        if (!$isNPC && $player->GetFightAttacks() != '')
            $attacks = explode(';', $player->GetFightAttacks());
        else if ($isNPC && $player->GetAttacks() != '')
            $attacks = explode(';', $player->GetAttacks());

        $powerup = '';
        if (!$isNPC && $player->GetKP() > 0)
            $powerup = $player->GetStartingPowerup();

        if (!in_array($powerup, $attacks))
            $powerup = '';

        $attacks = implode(';', $attacks);
        $this->AddDebugLog(' - FinalAttacks: ' . $attacks);

        $timestamp = date('Y-m-d H:i:s');
        $row['id'] = $player->GetID();
        $row['acc'] = $id;
        $row['fight'] = $this->GetID();
        $row['team'] = $team;
        if(!$isNPC)
            $row['clan'] = $player->GetClan();
        else
            $row['clan'] = 0;
        $row['name'] = $player->GetName();
        $row['charimage'] = $player->GetImage();
        //$row['inventory'] = $inventory;
        //$ki = ($ilp / 10) + ($ikp / 10) + $atk + $def; TODO: Reupload
        $ki = ($ilp / 10) + ($ikp / 10) + ($atk / 2) + $def;
        $ki = round($ki / 4);
        $row['attacks'] = $attacks;
        if ($isNPC)
        {
            $row['npc'] = $player->GetID();
            $row['patterns'] = $player->GetPatterns();
            $row['isstatsprocentual'] = $player->IsStatsProcentual() ? 1 : 0;
        }
        else
        {
            $row['patterns'] = '';
            $row['npc'] = 0;
            $row['isstatsprocentual'] = 0;
        }

        $mKI = $ki;
        $row['ki'] = $ki;
        $row['mki'] = $mKI;
        $row['lp'] = $lp;
        $row['ilp'] = $ilp;
        $row['mlp'] = $mlp;
        $row['kp'] = $kp;
        $row['ikp'] = $ikp;
        $row['mkp'] = $mkp;
        $row['critchance'] = $critchance;
        $row['critdamage'] = $critdmg;
        $row['attack'] = $atk;
        $row['mattack'] = $player->GetAttack();
        $row['equippedattack'] = $atk;
        $row['defense'] = $def;
        $row['mdefense'] = $player->GetDefense();
        $row['equippeddefense'] = $def;
        $row['accuracy'] = $player->GetAccuracy();
        $row['maccuracy'] = $player->GetAccuracy();
        $row['reflex'] = $player->GetReflex();
        $row['mreflex'] = $player->GetReflex();
        $row['transformations'] = $powerup;
        $row['owner'] = $owner;
        $row['fusedacc'] = $fusion;
        $row['fusetimer'] = $fusetimer;
        $row['majincounter'] = 0;
        $row['reflect'] = 0;
        $row['taunt'] = 0;
        if ($isNPC)
        {
            $row['isnpc'] = 1;
            $row['level'] = 0;
        }
        else
        {
            $row['isnpc'] = 0;
            $row['level'] = $player->GetLevel();
        }
        $row['lastaction'] = $timestamp;
        $row['action'] = 0;
        $row['inactive'] = false;
        $row['paralyzed'] = false;
        $row['npccontrol'] = false;
        $row['loadattack'] = 0;
        $row['loadrounds'] = 0;
        $row['gainaccs'] = '';
        $row['buffs'] = '';
        $row['dots'] = '';
        $row['energy'] = 0;
        $row['menergy'] = 100;
        $row['race'] = $player->GetRace();
        $row['kicktimer'] = 0;

        $this->AddDebugLog('Create Fighter: ' . $player->GetName() . ' - ' . round(microtime(true) * 1000) . ' Time');


        foreach ($row as $key => $value)
        {
            if(is_numeric($value))
                $value = number_format($value, 0, ',', '.');
            $this->AddDebugLog(' - ' . $key . ' = ' . $value);
        }

        $this->database->Insert(
            'acc
        , npc
		, fight
		, team
		, clan
		, name
		, level
		, charimage
		, attacks
		, ki
		, mki
		, lp
		, ilp
		, mlp
		, kp
		, ikp
		, mkp
        , critchance
        , critdamage
		, attack
		, mattack
		, equippedattack
		, defense
		, mdefense
		, equippeddefense
		, accuracy
		, maccuracy
		, reflex
		, mreflex
		, energy
		, menergy
		, isnpc
		, owner
		, lastaction
		, fusedacc
		, transformations
		, buffs
		, dots
		, fusetimer
		, patterns
		, race
		, isstatsprocentual',
            '"' . $row['acc'] . '"
		,"' . $row['npc'] . '"
		,"' . $row['fight'] . '"
		,"' . $row['team'] . '"
		, "'.$row['clan'].'"
		,"' . $row['name'] . '"
		,"' . $row['level'] . '"
		,"' . $row['charimage'] . '"
		,"' . $row['attacks'] . '"
		,"' . $row['ki'] . '"
		,"' . $row['mki'] . '"
		,"' . $row['lp'] . '"
		,"' . $row['ilp'] . '"
		,"' . $row['mlp'] . '"
		,"' . $row['kp'] . '"
		,"' . $row['ikp'] . '"
		,"' . $row['mkp'] . '"
        ,"' . $row['critchance'] . '"
        ,"' . $row['critdamage'] . '"
		,"' . $row['attack'] . '"
		,"' . $row['equippedattack'] . '"
		,"' . $row['mattack'] . '"
		,"' . $row['defense'] . '"
		,"' . $row['mdefense'] . '"
		,"' . $row['equippeddefense'] . '"
		,"' . $row['accuracy'] . '"
		,"' . $row['maccuracy'] . '"
		,"' . $row['reflex'] . '"
		,"' . $row['mreflex'] . '"
		,"' . $row['energy'] . '"
		,"' . $row['menergy'] . '"
		,"' . $row['isnpc'] . '"
		,"' . $row['owner'] . '"
		,"' . $row['lastaction'] . '"
		,"' . $row['fusedacc'] . '"
		,"' . $row['transformations'] . '"
		,"' . $row['buffs'] . '"
		,"' . $row['dots'] . '"
		,"' . $row['fusetimer'] . '"
		,"' . $row['patterns'] . '"
		,"' . $row['race'] . '"
		,"' . $row['isstatsprocentual'] . '"',
            'fighters'
        );

        $result = $this->database->Select('*', 'fighters', 'id=' . $this->database->GetLastID() , 1);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
            }
            $result->close();
        }
        $fighter = $this->AddFighter($row);

        if (!$isNPC)
        {

            if ($this->GetFighters() == '')
                $fighters = array();
            else
                $fighters = explode(';', $this->GetFighters());

            $fighters[] = '[' . $player->GetID() . ']';
            $fighters = implode(';', $fighters);
            $this->SetFighters($fighters);

            $test = ($player->GetARank() != 0) ? 1 : 0;

            $this->database->Update('fighters="' . $fighters . '",testfight=' . $test, 'fights', 'id = ' . $this->GetID() , 1);
        }

        if (!$isNPC && $fusion == 0)
        {
            $player->UpdateFight($this->GetID());
            if ($this->GetType() == 3) //NPC
            {
                if ($player->GetDailyNPCFights() < $player->GetDailyNPCFightsMax() || $player->GetDailyNPCFightsMax() > 50)
                {
                    if(!$player->IsDonator())
                    {
                        $dailyNPCFights = $player->GetDailyNPCFights() + 1;
                    }
                    else
                    {
                        $dailyNPCFights = $player->GetDailyNPCFights() + 2;
                    }
                    $this->AddDebugLog("7252 Set Elo-Points from " . $player->GetEloPoints() . " to " . ($player->GetEloPoints() + 0));
                    $player->SetEloPoints($player->GetEloPoints() + 0);
                    if($dailyNPCFights > $player->GetDailyNPCFightsMax() && $player->GetDailyNPCFightsMax() == 50)
                    {
                        $dailyNPCFights = $player->GetDailyNPCFightsMax();
                    }
                    $player->SetDailyNPCFights($dailyNPCFights);
                    $this->database->Update('elopoints='.$player->GetEloPoints(), 'accounts', 'id='.$player->GetID());
                    $player->UpdateDailyNPCFights();
                    $this->AddGainAcc($player->GetID());
                }
            }
            else if ($this->GetType() == 4 && $this->GetStory() == $player->GetStory() || $this->GetType() == 10 && $this->GetSideStory() == $player->GetSideStory()) //Story / SideStory
            {
                $this->AddGainAcc($player->GetID());
            }
            else if ($this->GetType() == 5 && $this->GetEventFight() == 0)
            {
                $event = new Event($this->database, $this->GetEvent());
                if ($event->GetDecreaseNPCFight() && $player->GetDailyNPCFights() < $player->GetDailyNPCFightsMax())
                {
                    $this->AddGainAcc($player->GetID());
                }
                else if ((!$event->GetDecreaseNPCFight() && $event->GetWinable() > $event->GetFinishedTimes($player->GetID())))
                {
                    $this->AddGainAcc($player->GetID());
                }
            }
            else if ($this->GetType() != 5 && $this->GetType() != 4 && $this->GetType() != 10)
            {
                $this->AddGainAcc($player->GetID());
            }
        }
        $this->AddDebugLog('CreateFighter Done - ' . round(microtime(true) * 1000) . ' Time');
        return $fighter;
    }

    public function DeleteFighter($player)
    {
        if ($player->GetID() != -1)
        {
            $newFighterArray = array();
            $fighters = explode(';', $this->GetFighters());

            $rFighterID = '[' . $player->GetID() . ']';
            foreach ($fighters as &$fighterID)
            {
                if ($fighterID != $rFighterID)
                    $newFighterArray[] = $fighterID;
            }

            $newFighterArray = implode(';', $newFighterArray);
            $this->SetFighters($newFighterArray);
            $this->database->Update('fighters="' . $newFighterArray . '"', 'fights', 'id = ' . $this->GetID() , 1);
        }


        $this->database->Delete('fighters', 'acc=' . $player->GetID());
        $this->RemoveFighter($player->GetID());
        $player->UpdateFight(0);
    }

    static function CreateFight(
        $player,
        $database,
        $type,
        $name,
        $mode,
        $levelup = 0,
        $actionManager = null,
        $berry = 0,
        $pvp = 0,
        $gold = 0,
        $items = 0,
        $story = 0,
        $sidestory = 0,
        $challenge = 0,
        $survivalteam = 0,
        $survivalrounds = 0,
        $survivalwinner = 0,
        $event = 0,
        $healing = 0,
        $eventfight = 0,
        $tournament = 0,
        $npcid = 0,
        $difficulty = 0,
        $healthRatio = 0,
        $healthRatioTeam = 0,
        $healthRatioWinner = 0,
        $isMirror = 0
    )
    {
        if ($player != null && $player->GetFight() != 0)
        {
            echo 'Player is invalid!<br/>';
            return false;
        }

        $place = '';
        $planet = '';
        if ($player != null)
        {
            $place = $player->GetPlace();
            $planet = $player->GetPlanet();
        }

        $name = $database->EscapeString($name);
        $name = htmlentities($name, ENT_QUOTES | ENT_XML1);

        $type = $database->EscapeString($type);
        $mode = $database->EscapeString($mode);
        $result = $database->Insert(
            'name, type, mode, place, levelup, planet, zeni, gold, kopfgeld,
                                items, story, sidestory, challenge, survivalteam, survivalrounds, survivalwinner, 
                                event, healing, eventfight, tournament, npcid
                                , npcmode, healthratio, healthratioteam, healthratiowinner, mirror',
            '"' . $name . '","' . $type . '","' . $mode . '", "' . $place . '", "' . $levelup . '", "' . $planet . '", "' . $berry . '", "' . $gold . '", "' . $pvp . '",
																"' . $items . '","' . $story . '", "' . $sidestory . '", "' . $challenge . '","' . $survivalteam . '","' . $survivalrounds . '","' . $survivalwinner . '",
																"' . $event . '","' . $healing . '","' . $eventfight . '","' . $tournament . '","' . $npcid . '"
                                ,"' . $difficulty . '","' . $healthRatio . '","' . $healthRatioTeam . '","' . $healthRatioWinner . '", "' . $isMirror . '"',
            'fights'
        );

        if (!$result)
        {
            echo 'Could not create Fight<br/>';
            return false;
        }

        $lastID = $database->GetLastID();
        $fight = new Fight($database, $lastID, $player, $actionManager);
        $fight->SetID($lastID);
        $fight->SetName($name);
        $fight->SetTournament($tournament);
        $fight->SetType($type);
        $fight->SetMode($mode);
        $fight->SetPlace($place);
        $fight->SetLevelup($levelup);
        $fight->SetPlanet($planet);
        $fight->SetBerry($berry);
        $fight->SetGold($gold);
        $fight->SetPvP($pvp);
        $fight->SetItems($items);
        $fight->SetStory($story);
        $fight->SetSideStory($sidestory);
        $fight->SetChallenge($challenge);
        $fight->SetSurvivalTeam($survivalteam);
        $fight->SetSurvivalRounds($survivalrounds);
        $fight->SetSurvivalWinner($survivalwinner);
        $fight->SetHealthRatio($healthRatio);
        $fight->SetHealthRatioTeam($healthRatioTeam);
        $fight->SetHealthRatioWinner($healthRatioWinner);
        $fight->SetMirror($isMirror);
        $fight->SetState(0);
        $fight->SetRound(1);
        $fight->SetText('');
        $fight->SetDebugLog('');
        $fight->SetFighters('');
        $fight->AddDebugLog($event);
        return $fight;
    }

    static function ValidateMode($mode)
    {
        if (strlen($mode) > 30)
        {
            return false;
        }

        $teams = explode('vs', $mode);
        $i = 0;

        $returnMode = '';
        while (isset($teams[$i]))
        {
            if (!is_numeric($teams[$i]) || $teams[$i] <= 0 || $teams[$i] > 100)
            {
                return false;
            }

            if ($returnMode == '')
            {
                $returnMode = $teams[$i];
            }
            else
            {
                $returnMode = $returnMode . 'vs' . $teams[$i];
            }
            ++$i;
        }

        return $returnMode;
    }
}
