<?php
include_once 'classes/items/itemmanager.php';
include_once 'classes/fight/attackmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/npc/npc.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/arena/arena.php';
$itemManager = new ItemManager($database);
$attackManager = new AttackManager($database);
$actionManager = new ActionManager($database);
if(isset($_GET['p']) && $_GET['p'] == 'infight' && isset($_GET['start']) && $_GET['start'] == 'lastfight' && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['type']) && is_numeric($_GET['type']))
{
    $id = $_GET['id'];
    $type = $_GET['type'];
    if($id != $player->GetLastNPCID())
    {
        return;
    }
    $npc = new NPC($database, $player->GetLastNPCID());
    if ($player->GetLP() < $player->GetMaxLP() * 0.2)
    {
        $message = 'Du hast nicht genügend LP. Du brauchst mindestens 20% LP.';
    }
    else if ($player->GetKP() < $player->GetMaxKP() * 0.2)
    {
        $message = 'Du hast nicht genügend AD. Du brauchst mindestens 20% AD.';
    }
    else if($type != 3 && $type != 8)
    {
        $message = 'Für diesen Kampf kannst du kein Rematch ausführen.';
    }
    else if($type == 8 && date("w") != 3)
    {
        $message = 'Im Kolosseum kannst du nur Mittwochs ein Rematch machen.';
    }
    else
    {
        $mode = '1vs1';
        $berry = 0;
        $items = '';
        if ($type == 3)
        {
            if ($npc->GetID() != 35)
            {
                $berry = $npc->GetBerry();
            }
            else if ($npc->GetID() == 35)
            {
                $berry = $player->GetLevel() * $npc->GetBerry();
            }
            $items = $npc->GetItems();
        }
        $gold = 0;
        $pvp = 0;
        $name = 'Rematch Vs '.$npc->GetName();
        $team = 1;
        $difficulty = 1;
        $survivalrounds = $npc->GetSurvivalRounds();
        $survivalteam = $npc->GetSurvivalTeam();
        $survivalwinner = $npc->GetSurvivalWinner();
        $healthRatio = $npc->GetHealthRatio();
        $healthRatioTeam = $npc->GetHealthRatioTeam();
        $healthRatioWinner = $npc->GetHealthRatioWinner();
        $createdFight = Fight::CreateFight(
            $player,
            $database,
            $type,
            $name,
            $mode,
            0,
            $actionManager,
            $berry,
            $pvp,
            $gold,
            $items,
            0,
            0,
            0,
            $survivalteam,
            $survivalrounds,
            $survivalwinner,
            0,
            0,
            0,
            0,
            $_GET['id'],
            $difficulty,
            $healthRatio,
            $healthRatioTeam,
            $healthRatioWinner
        );
        $createdFight->Join($player, 0);
        $createdFight->Join($npc, $team, true);
        if($type == 8)
        {
            $arena = new Arena($database);
            $arena->UpdateFight($player->GetID(), 0);
            if ($createdFight->IsStarted()) {
                $player->UpdateFight($createdFight->GetID());
                $player->SetLastNPCID($_GET['id']);
                header('Location: ?p=infight');
                exit();
            }
        }
        if ($createdFight->IsStarted())
        {
            header('Location: ?p=infight');
            exit();
        }
        else
        {
            $message = 'Der Kampf wurde eröffnet.';
        }
    }
}
$pFighter = null;
if (isset($fight) && $fight->IsStarted())
{
    $pFighter = $fight->GetPlayer();
    $attackCode = '';
    if ($pFighter != null)
    {
        if ($fight->GetRound() >= 30 && ($fight->GetType() == 1 || $fight->GetType() == 11))
            $pFighter->AddAttack(2);
        $attackCode = $pFighter->GetAttackCode();
    }

    if (
        isset($_GET['a']) && $_GET['a'] == 'attack' && isset($_POST['attack']) && isset($_POST['target']) &&
        is_numeric($_POST['attack']) && is_numeric($_POST['target']) && isset($_GET['code']) && $pFighter->GetAttackCode() == $_GET['code']
    )
    {
        $message = $fight->DoAttack($pFighter, $_POST['attack'], $_POST['target']);
    }
    else if (isset($_GET['a']) && $_GET['a'] == 'kick' && isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $kickFighter = $fight->GetFighter($_GET['id']);
        if($kickFighter == null)
        {
            $message = 'Der Kämpfer ist ungültig.';
        }
        else
        {
            $cooldown = $kickFighter->GetActionCountdown($fight->GetType());
            if($cooldown > 0)
            {
                $message = 'Der Countdown ist noch nicht abgelaufen.';
            }
            $fight->AddDebugLog($pFighter->GetName() . ' kickt Fighter: ' . $kickFighter->GetName() . ' - countdown: ' . $kickFighter->GetActionCountdown($fight->GetType()));
            $fight->Kick($kickFighter->GetID());
        }
    }
    else if (isset($_GET['a']) && $_GET['a'] == 'giveup' && isset($_POST['giveup']))
    {
        if(($fight->GetType() == 1 || $fight->GetType() == 8) && $fight->GetRound() < 30)
        {
            $message = 'Der Kampf kann noch nicht aufgegeben werden.';
        }
        else
        {
            $fight->GiveUp($pFighter);
        }
    }
    else if(isset($_GET['a']) && $_GET['a'] == 'use' && (isset($_POST['booster']) || isset($_POST['vitamins']) ||
            isset($_POST['redFruit']) || isset($_POST['orangeFruit']) || isset($_POST['yellowFruit']) ||
            isset($_POST['gruenewolke']) || isset($_POST['rotewolke'])))
    {
        $booster = 0;
        $vitamins = 0;
        $redfruit = 0;
        $orangefruit = 0;
        $yellowfruit = 0;
        $changecloud = 0;
        $changecloudr = 0;

        if(isset($_POST['booster']) && $_POST['booster'] != '' && !is_numeric($_POST['booster']))
        {
            $message = 'Der Booster ist ungültig.';
        }
        else if(isset($_POST['vitamins']) && $_POST['vitamins'] != '' && !is_numeric($_POST['vitamins']))
        {
            $message = 'Die Vitamine sind ungültig.';
        }
        else if(isset($_POST['redFruit']) && $_POST['redFruit'] != '' && !is_numeric($_POST['redFruit']))
        {
            $message = 'Die rote Frucht ist ungültig.';
        }
        else if(isset($_POST['orangeFruit']) && $_POST['orangeFruit'] != '' && !is_numeric($_POST['orangeFruit']))
        {
            $message = 'Die orange Frucht ist ungültig.';
        }
        else if(isset($_POST['yellowFruit']) && $_POST['yellowFruit'] != '' && !is_numeric($_POST['yellowFruit']))
        {
            $message = 'Die gelbe Frucht ist ungültig.';
        }
        else if(isset($_POST['gruenewolke']) && $_POST['gruenewolke'] != '' && !is_numeric($_POST['gruenewolke']))
        {
            $message = 'Die grüne Wolke ist ungültig.';
        }
        else if(isset($_POST['rotewolke']) && $_POST['rotewolke'] != '' && !is_numeric($_POST['rotewolke']))
        {
            $message = 'Die rote Wolke ist ungültig.';
        }
        else
        {
            $booster = $_POST['booster'];
            $vitamins = $_POST['vitamins'];
            $redfruit = $_POST['redFruit'];
            $orangefruit = $_POST['orangeFruit'];
            $yellowfruit = $_POST['yellowFruit'];
            $changecloud = $_POST['gruenewolke'];
            $changecloudr = $_POST['rotewolke'];
            if(!is_numeric($booster))
                $booster = 0;
            if(!is_numeric($vitamins))
                $vitamins = 0;
            if(!is_numeric($redfruit))
                $redfruit = 0;
            if(!is_numeric($orangefruit))
                $orangefruit = 0;
            if(!is_numeric($yellowfruit))
                $yellowfruit = 0;
            if(!is_numeric($changecloud))
                $changecloud = 0;
            if(!is_numeric($changecloudr))
                $changecloudr = 0;

            if($yellowfruit > 1 || $yellowfruit > 0 && $pFighter->GetUsedYellowFruits() == 1)
            {
                $message = 'Du kannst insgesamt nur 1 Gelbe Frucht benutzen.';
            }
            else if($redfruit + $orangefruit + $yellowfruit + $pFighter->GetUsedFruits() > 2)
            {
                $message = 'Du kannst insgesamt nur 2 Früchte benutzen.';
            }
            else if($booster > 5 || (5 - $pFighter->GetUsedTestos()) < $booster)
            {
                $message = 'Du kannst insgesamt nur 5 Testo-Booster benutzen.';
            }
            else if($vitamins > 5 || (5 - $pFighter->GetUsedVitamin()) < $vitamins)
            {
                $message = 'Du kannst insgesamt nur 5 Vitamine benutzen.';
            }
            else if($changecloud > 1 || $changecloudr > 1)
            {
                $message = "Du kannst immer nur eine Wolke aktivieren.";
            }
            else
            {
                #region Booster
                if($booster > 0)
                {
                    if(!$player->HasItemWithID(81,81))
                    {
                        $message = 'Du hast dieses Item nicht.';
                    }
                    else
                    {
                        $itemDataBooster = $player->GetItemByStatsIDOnly(81);
                        if($itemDataBooster->GetAmount() < $booster)
                        {
                            $message = 'Du hast nicht so viele Items.';
                        }
                    }
                }
                #endregion

                #region Grüne Wolke
                if($changecloud > 0)
                {
                    if(!$player->HasItemWithID(406,406))
                    {
                        $message = 'Du hast dieses Item nicht.';
                    }
                    else {
                        $itemDataChangeCloud = $player->GetItemByStatsIDOnly(406);
                        if ($itemDataChangeCloud->GetAmount() < $changecloud)
                        {
                            $message = 'Du hast nicht so viele Items.';
                        }
                    }
                }

                if($changecloudr > 0)
                {
                    if(!$player->HasItemWithID(407,407))
                    {
                        $message = 'Du hast dieses Item nicht.';
                    }
                    else {
                        $itemDataChangeCloudr = $player->GetItemByStatsIDOnly(407);
                        if ($itemDataChangeCloudr->GetAmount() < $changecloudr)
                        {
                            $message = 'Du hast nicht so viele Items.';
                        }
                    }
                }
                #endregion

                #region vitamins
                if($vitamins > 0)
                {
                    if(!$player->HasItemWithID(82,82))
                    {
                        $message = 'Du hast dieses Item nicht.';
                    }
                    else {
                        $itemDataVitamins = $player->GetItemByStatsIDOnly(82);
                        if ($itemDataVitamins->GetAmount() < $vitamins)
                        {
                            $message = 'Du hast nicht so viele Items.';
                        }
                        else
                        {
                            if($pFighter->GetEnergy() < 25)
                            {
                                $message = 'Die Vitamine hätten aktuell kaum einen Effekt.';
                            }
                        }
                    }
                }
                #endregion

                #region redFruit
                if($redfruit > 0)
                {
                    if(!$player->HasItemWithID(86,86))
                    {
                        $message = 'Du hast dieses Item nicht.';
                    }
                    else
                    {
                        $itemDataRedFruit = $player->GetItemByStatsIDOnly(86);
                        if($itemDataRedFruit->GetAmount() < $redfruit)
                        {
                            $message = 'Du hast nicht so viele Items.';
                        }
                    }
                }
                #endregion

                #region orangeFruit
                if($orangefruit > 0)
                {
                    if(!$player->HasItemWithID(87,87))
                    {
                        $message = 'Du hast dieses Item nicht.';
                    }
                    else
                    {
                        $itemDataOrangeFruit = $player->GetItemByStatsIDOnly(87);
                        if($itemDataOrangeFruit->GetAmount() < $orangefruit)
                        {
                            $message = 'Du hast nicht so viele Items.';
                        }
                    }
                }
                #endregion

                #region yellowFruit
                if($yellowfruit > 0)
                {
                    if(!$player->HasItemWithID(88,88))
                    {
                        $message = 'Du hast dieses Item nicht.';
                    }
                    else if($player->GetLevel() >= 48)
                    {
                        $message = 'Du kannst dieses Item nicht mehr nutzen.';
                    }
                    else
                    {
                        $itemDataYellowFruit = $player->GetItemByStatsIDOnly(88);
                        if($itemDataYellowFruit->GetAmount() < $yellowfruit)
                        {
                            $message = 'Du hast nicht so viele Items.';
                        }
                    }
                }
                #endregion

                if($message == '')
                {
                    if($booster > 0)
                        $player->UseItem2($player->GetInventory()->GetItemID($itemDataBooster->GetID()), $booster);
                    if($vitamins > 0)
                        $player->UseItem2($player->GetInventory()->GetItemID($itemDataVitamins->GetID()), $vitamins);
                    if($redfruit > 0)
                        $player->UseItem2($player->GetInventory()->GetItemID($itemDataRedFruit->GetID()), $redfruit);
                    if($orangefruit > 0)
                        $player->UseItem2($player->GetInventory()->GetItemID($itemDataOrangeFruit->GetID()), $orangefruit);
                    if($yellowfruit > 0)
                        $player->UseItem2($player->GetInventory()->GetItemID($itemDataYellowFruit->GetID()), $yellowfruit);
                    if($changecloud > 0)
                        $player->UseItem2($player->GetInventory()->GetItemID($itemDataChangeCloud->GetID()), $changecloud);
                    if($changecloudr > 0)
                        $player->UseItem2($player->GetInventory()->GetItemID($itemDataChangeCloudr->GetID()), $changecloudr);
                    $used = $pFighter->GetUsedFruits();
                    $used += $redfruit + $orangefruit + $yellowfruit;
                    $pFighter->SetUsedFruits($used);
                    if($yellowfruit != 0)
                        $pFighter->SetUsedYellowFruits($yellowfruit);
                    if($booster != 0)
                        $pFighter->SetUsedTestos($pFighter->GetUsedTestos() + $booster);
                    if($vitamins != 0)
                        $pFighter->SetUsedVitamin($pFighter->GetUsedVitamin() + $vitamins);
                    $database->Update('usedfruits='. $used.', usedyellowfruits='.$pFighter->GetUsedYellowFruits().', usedvitamin='.$pFighter->GetUsedVitamin().', usedtesto='.$pFighter->GetUsedTestos(), 'fighters', 'id= ' . $pFighter->GetID(), 999);
                    header('Location: ?p=infight');
                }
            }
        }
    }

    if ($pFighter != null)
    {
        $fight->UpdateAttackCode($pFighter);
    }
}
else if (isset($_GET['fight']))
{
    $fight = new Fight($database, $_GET['fight'], $player, $actionManager);
}

if (isset($fight) && $fight->IsStarted())
{
    $teams = $fight->GetTeams();
}
else if (!isset($fight) || !$fight->IsStarted())
{
    header('Location: ?p=news');
    exit();
}

if (isset($_GET['a']) && $_GET['a'] == 'meld' && $fight != null && $player->GetName() != 'Google')
{
    if($fight->GetMeldeCount() > 0)
    {
        $message = 'Der Kampf wurde bereits gemeldet, bitte warte die Antwort von einem Admin oder Director ab.';
    }
    else if(empty(trim($_POST['reason'])))
    {
        $message = 'Du hast keinen Grund angegeben.';
    }
    else if(!$player->IsVerified())
    {
        $message = "Du musst verifiziert sein um einen Kampf melden zu können";
    }
    else
    {
        $option = $database->EscapeString($_POST['option']);
        $textarea = $database->EscapeString($_POST['reason']);
        $grund = "Es handelt sich hierbei um einen ".$option." und wurde wie folgt beschrieben: <br/><br/>".$textarea;
        $type = Fight::GetTypeName($fight->GetType());
        $result = $database->Update('meldecount=1, meldegrund="'.$textarea.'"', 'fights', 'id='.$fight->GetID());
        $text = "Kampfmeldung von ".$player->GetName()." - Art der Meldung: ".$option." - Gemeldeter Kampf: <a href='?p=infight&fight=".$fight->GetID()."'>".$fight->GetID()."</a> <a href='?p=fightlog&fight=".$fight->GetID()."'>(Log)</a> - Grund der Meldung: ".$textarea;
        $player->AddMeldung($text, $player->GetID(), "System", 2);
        $message = "Vielen dank für die Meldung.";
    }
}
if(isset($_GET['a']) && $_GET['a'] == "deleteFight")
{
    if($player->GetArank() < 2)
    {
        $message = 'Du bist dazu nicht berechtigt.';
    }
    else if(!$fight->IsValid())
    {
        $message = 'Der Kampf ist ungültig.';
    }
    else if(!$fight->IsTestFight())
    {
        $message = 'Diesen Kampf kannst du nicht löschen.';
    }
    else
    {
        $database->Delete('fighters', 'fight="' . $player->GetFight() . '"');
        $fight->DeleteFight();
        $player->SetFight(0);
        header('Location: ?p=news');
    }
}

function interpolateColor($corA, $corB, $lerp)
{
    $redA = $corA & 0xFF0000;
    $greenA = $corA & 0x00FF00;
    $blueA = $corA & 0x0000FF;
    $redB = $corB & 0xFF0000;
    $greenB = $corB & 0x00FF00;
    $blueB = $corB & 0x0000FF;

    $redC = $redA + (($redB - $redA) * $lerp) & 0xFF0000;         // Only Red
    $greenC = $greenA + (($greenB - $greenA) * $lerp) & 0x00FF00; // Only Green
    $blueC = $blueA + (($blueB - $blueA) * $lerp) & 0x0000FF;     // Only Blue

    $result = dechex($redC | $greenC | $blueC);
    return str_pad($result, 6, "0", STR_PAD_LEFT);
}

function GetLPCSS($value)
{
    $downColor = interpolateColor(0xffaf00, 0x00fafa, $value);
    $upColor = interpolateColor(0xfffa00, 0x00afaf, $value);
    return "background: #$downColor;
        background: -moz-linear-gradient(#$upColor ,#$downColor);
        background: -webkit-linear-gradient(#$upColor ,#$downColor);
        background: linear-gradient(#$upColor ,#$downColor);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#$upColor', endColorstr='#$downColor',GradientType=0 );";
}

function GetKPCSS($value)
{
    $downColor = interpolateColor(0x00aaff, 0xffffff, $value);
    $upColor = interpolateColor(0x55aabb, 0x00aaff, $value);
    return "background: #$downColor;
        background: -moz-linear-gradient(#$upColor ,#$downColor);
        background: -webkit-linear-gradient(#$upColor ,#$downColor);
        background: linear-gradient(#$upColor ,#$downColor);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#$upColor', endColorstr='#$downColor',GradientType=0 );";
}

function ShowTeam($team, $id, $player, $fight)
{
    if ($player != null && $player->GetTeam() == $id)
    {
        $sortTeam = array();
        $i = 0;
        while (isset($team[$i]))
        {
            $fighter = $team[$i];
            if (!$fighter->IsInactive() && $player != $fighter)
            {
                $sortTeam[] = $fighter;
            }
            ++$i;
        }

        array_unshift($sortTeam, $player);
        $team = $sortTeam;
    }

    ?>
    <div class="SideMenuContainer borderT borderL borderB borderR">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">
                <span style="color: <?php echo $fight->GetTeamColor($id); ?>;">Team: <?php echo $id + 1; ?></span>
            </div>
        </div>
        <div class="SideMenuInfo">
            <div class="spacer"></div>
            <!-- Player Anfang -->
            <?php
            $i = 0;
            while (isset($team[$i]))
            {
                $fighter = $team[$i];
                if ($fighter->IsInactive())
                {
                    ++$i;
                    continue;
                }
                ?>
                <div class="SideMenuContainer2 borderT borderL borderB borderR">
                    <div class="SideMenuKat catGradient" style="height: fit-content">
                        <div class="schatten">
                            <?php
                            Global $database;
                            $fighterClan = new Clan($database, $fighter->GetClanID());
                            if($fighter->GetClanID() != 0)
                                echo '<a href="?p=clan&id='.$fighter->GetClanID().'">['.$fighterClan->GetTag().']</a> ';
                            if(!$fighter->IsNPC())
                                echo '<a href="?p=profil&id='.$fighter->GetAcc().'">' . $fighter->GetName() . '</a>';
                            else
                                echo $fighter->GetName();
                            ?>
                        </div>
                    </div>
                    <div class="SideMenuInfo2">
                        <div class="char_main2">
                            <div class="spacer"></div>
                            <?php
                            $playerimagenew = $fighter->GetImage();
                            ?>
                            <div class="char_image2 smallBG borderT borderB borderR borderL"><img src="<?php echo $playerimagenew; ?>" width="100%" height="100%"></div>
                            <div class="spacer"></div>
                            <div class="char_live">
                                <div class="lpback" style="height:20px; width:100%;">
                                    <div class="lpbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                    <div class="lpanzeige" style="width:<?php echo $fighter->GetLPPercentage(); ?>%"></div>
                                    <?php
                                    $maxLP = $fighter->GetMaxLP();
                                    $tempLP = $fighter->GetLP() - $maxLP;
                                    $lpInc = 0.1;
                                    while ($tempLP > 0)
                                    {
                                        $lpProc = min(100, ($tempLP / $maxLP * 100));
                                        ?>
                                        <div style="position: absolute; height:100%; top:1px; left:1px; <?php echo GetLPCSS($lpInc); ?> width:<?php echo $lpProc; ?>%"></div>
                                        <?php
                                        $tempLP = $tempLP - $maxLP;
                                        $lpInc += 0.1;
                                    }
                                    if ($player != null && $player->GetTeam() == $id)
                                    {
                                        ?>
                                        <div class="lptext" style="font-size: 10px;">LP:
                                            <?php echo number_format($fighter->GetLP(),'0', '', '.'); ?>
                                        </div>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="lptext" style="font-size: 10px;">LP: <?php echo $fighter->GetLPPercentage(); ?>%</div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="spacer"></div>
                                <div class="kpback" style="height:20px; width:100%;">
                                    <div class="kpbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                    <div class="kpanzeige" style="width:<?php echo $fighter->GetKPPercentage(); ?>%"></div>
                                    <?php
                                    $maxKP = $fighter->GetMaxKP();
                                    $tempKP = $fighter->GetKP() - $maxKP;
                                    $kpInc = 0.1;
                                    while ($tempKP > 0)
                                    {
                                        $kpProc = min(100, ($tempKP / $maxKP * 100));
                                        ?>
                                        <div style="position: absolute; height:100%; top:1px; left:1px; <?php echo GetKPCSS($kpInc); ?> width:<?php echo $kpProc; ?>%"></div>
                                        <?php
                                        $tempKP = $tempKP - $maxKP;
                                        $kpInc += 0.1;
                                    }
                                    if ($player != null && $player->GetTeam() == $id)
                                    {
                                        ?>
                                        <div class="kptext" style="font-size: 10px;">AD:
                                            <?php echo number_format($fighter->GetKP(),'0', '', '.'); ?>
                                        </div>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="kptext" style="font-size: 10px;">AD: <?php echo $fighter->GetKPPercentage(); ?>%</div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="spacer"></div>
                                <div class="epback" style="height:20px; width:100%;">
                                    <div class="epbox smallBG borderT borderB borderR borderL boxSchatten"></div>
                                    <div class="epanzeige" style="width:<?php echo $fighter->GetEPPercentage(); ?>%"></div>
                                    <?php
                                    if ($fighter->GetEnergy() > $fighter->GetMaxEnergy())
                                    {
                                        ?>
                                        <div class="epanzeige2" style="width:<?php echo $fighter->GetTotalEPPercentage() - 100; ?>%"></div>
                                        <?php
                                    }
                                    if ($player != null && $player->GetTeam() == $id)
                                    {
                                        ?>
                                        <div class="eptext" style="font-size: 10px;">EP:
                                            <?php echo number_format($fighter->GetEnergy(),'0', '', '.'); ?>
                                        </div>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="eptext" style="font-size: 10px;">EP: <?php echo $fighter->GetEPPercentage(); ?>%</div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div style="position:relative; height: fit-content; display: flex; flex-direction: row; flex-wrap: wrap; justify-content: space-evenly; top: -23px; width: 95%;">
                            <?php
                            if($fighter->GetTransformations() != '')
                            {
                                $playertransforms = explode(';', $fighter->GetTransformations());
                                foreach($playertransforms as $playertransform)
                                {
                                    global $attackManager;
                                    $skill = $attackManager->GetAttack($playertransform);
                                    if($skill->GetDisplayed())
                                    {
                                        ?>
                                        <div class="tooltip" style="height:30px; width: 30px;">
                                            <img src="<?php echo $skill->GetImage(); ?>" style="border-radius:25%; overflow:hidden; width: 25px; height: 25px; position: relative;">
                                            <img src="img/offtopic/Buff.png" style="width: 15px; height: 15px; position: relative; left: 10px; top: -20px;">
                                            <span class="tooltiptext" style="left:3px;">
                                            <?php echo $skill->GetName(); ?>
                                            <hr/>
                                            <?php
                                            if($skill->GetAtkValue() / 100 * $skill->GetValue() != 0)
                                            {
                                                echo 'Attack: ';
                                                echo '+';
                                                echo number_format($skill->GetAtkValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                            }
                                            if($skill->GetDefValue() / 100 * $skill->GetValue() != 0)
                                            {
                                                echo 'Defense: ';
                                                echo '+';
                                                echo number_format($skill->GetDefValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                            }
                                            if($skill->GetLPValue() / 100 * $skill->GetValue() != 0)
                                            {
                                                echo 'LP: ';
                                                echo '+';
                                                echo number_format($skill->GetLPValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                            }
                                            if($skill->GetKPValue() / 100 * $skill->GetValue() != 0)
                                            {
                                                echo 'AD: ';
                                                echo '+';
                                                echo number_format($skill->GetKPValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                            }
                                            if($skill->GetTauntValue() / 100 * $skill->GetValue() != 0)
                                            {
                                                echo 'Taunt: ';
                                                echo '+';
                                                echo number_format($skill->GetTauntValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                            }
                                            if($skill->GetReflectValue() / 100 * $skill->GetValue() != 0)
                                            {
                                                echo 'Reflect: ';
                                                echo '+';
                                                echo number_format($skill->GetReflectValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                            }
                                            if($skill->GetReflexBuff() / 100 * $skill->GetValue() != 0)
                                            {
                                                echo 'Reflex: ';
                                                echo '+';
                                                echo number_format($skill->GetReflexBuff() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                            }
                                            if($skill->GetAccBuff() / 100 * $skill->GetValue() != 0)
                                            {
                                                echo 'Genauigkeit: ';
                                                echo '+';
                                                echo number_format($skill->GetAccBuff() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                            }
                                            ?>
                                        </span>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            if($fighter->GetBuffs() != '')
                            {
                                $playerbuffs = explode(';',$fighter->GetBuffs());

                                foreach($playerbuffs as $playerbuff)
                                {
                                    $buff = explode('@', $playerbuff);
                                    global $attackManager;
                                    $skill = $attackManager->GetAttack($buff[1]);
                                    if($skill->GetDisplayed())
                                    {
                                        ?>
                                        <div class="tooltip" style="height:30px; width: 30px;">
                                            <img src="<?php echo $skill->GetImage(); ?>" alt="<?php echo $skill->GetName(); ?>" title="<?php echo $skill->GetName(); ?>" style="border-radius:25%; overflow:hidden; width: 25px; height: 25px; position: relative;"/>
                                            <?php
                                            if(($buff[3] + $buff[4] + $buff[5] + $buff[6] + $buff[7] + $buff[8] + $buff[9] + $buff[10] + $buff[11] + $buff[12]) > 0)
                                            {
                                                echo '<img src="img/offtopic/Buff.png" alt="" style="width: 15px; height: 15px; position: relative; left: 10px; top: -20px;">';
                                            }
                                            else
                                            {
                                                echo '<img src="img/offtopic/Debuff.png" alt="" style="width: 15px; height: 15px; position: relative; left: 10px; top: -20px;">';
                                            }
                                            ?>
                                            <span style="position:absolute; left:3px; top:5px; font-size:12px; color:#000;
                                    text-shadow:
                                      -1px -1px 0 #fff,
                                      1px -1px 0 #fff,
                                      -1px 1px 0 #fff,
                                      1px 1px 0 #fff;"><b><?php echo number_format($buff[2], '0', '', '.'); ?></b></span>
                                            <span class="tooltiptext" style="left:3px;">
                                        <?php echo $skill->GetName(); ?>
                                        <hr/>
                                        <?php
                                        if($buff[3] != 0)
                                        {
                                            echo 'Attack: ';
                                            if($buff[3] > 0)
                                                echo '+';
                                            echo number_format($skill->GetAtkValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($buff[4] != 0)
                                        {
                                            echo 'Defense: ';
                                            if($buff[4] > 0)
                                                echo '+';
                                            echo number_format($skill->GetDefValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($buff[5] != 0)
                                        {
                                            echo 'LP: ';
                                            if($buff[5] > 0)
                                                echo '+';
                                            echo number_format($skill->GetLPValue() / 100 * $skill->GetValue(), '0', '', '.') . '<br/>';
                                        }
                                        if($buff[7] != 0)
                                        {
                                            echo 'AD: ';
                                            if($buff[7] > 0)
                                                echo '+';
                                            echo number_format($skill->GetKPValue() / 100 * $skill->GetValue(), '0', '', '.') . '<br/>';
                                        }
                                        if($buff[9] != 0)
                                        {
                                            echo 'Taunt: ';
                                            if($buff[9] > 0)
                                                echo '+';
                                            echo number_format($skill->GetTauntValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($buff[10] != 0)
                                        {
                                            echo 'Reflect: ';
                                            if($buff[10] > 0)
                                                echo '+';
                                            echo number_format($skill->GetReflectValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($buff[11] != 0)
                                        {
                                            echo 'Reflex: ';
                                            if($buff[11] > 0)
                                                echo '+';
                                            echo number_format($skill->GetReflexValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($buff[12] != 0)
                                        {
                                            echo 'Genauigkeit: ';
                                            if($buff[12] > 0)
                                                echo '+';
                                            echo number_format($skill->GetAccBuff() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        ?>
                                    </span>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            if($fighter->GetDebuffs() != '')
                            {
                                $playerdebuffs = explode(';',$fighter->GetDebuffs());

                                foreach($playerdebuffs as $playerdebuff)
                                {
                                    $debuff = explode('@', $playerdebuff);
                                    global $attackManager;
                                    $skill = $attackManager->GetAttack($debuff[1]);
                                    if($skill->GetDisplayed())
                                    {
                                        ?>
                                        <div class="tooltip" style="height:30px; width: 30px;">
                                            <img src="<?php echo $skill->GetImage(); ?>" alt="<?php echo $skill->GetName(); ?>" title="<?php echo $skill->GetName(); ?>" style="border-radius:25%; overflow:hidden; width: 25px; height: 25px; position: relative;"/>
                                            <?php
                                            if(($debuff[3] + $debuff[4] + $debuff[5] + $debuff[6] + $debuff[7] + $debuff[8] + $debuff[9] + $debuff[10] + $debuff[11] + $debuff[12]) > 0)
                                            {
                                                echo '<img src="img/offtopic/Buff.png" alt="" style="width: 15px; height: 15px; position: relative; left: 10px; top: -20px;">';
                                            }
                                            else
                                            {
                                                echo '<img src="img/offtopic/Debuff.png" alt="" style="width: 15px; height: 15px; position: relative; left: 10px; top: -20px;">';
                                            }
                                            ?>
                                            <span style="position:absolute; left:3px; top:5px; font-size:12px; color:#000;
                                    text-shadow:
                                      -1px -1px 0 #fff,
                                      1px -1px 0 #fff,
                                      -1px 1px 0 #fff,
                                      1px 1px 0 #fff;"><b><?php echo number_format($debuff[2], '0', '', '.'); ?></b></span>
                                            <span class="tooltiptext" style="left:3px;">
                                        <?php echo $skill->GetName(); ?>
                                        <hr/>
                                        <?php
                                        if($debuff[3] != 0)
                                        {
                                            echo 'Attack: ';
                                            if($debuff[3] > 0)
                                                echo '+';
                                            echo number_format($skill->GetAtkValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($debuff[4] != 0)
                                        {
                                            echo 'Defense: ';
                                            if($debuff[4] > 0)
                                                echo '+';
                                            echo number_format($skill->GetDefValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($debuff[5] != 0)
                                        {
                                            echo 'LP: ';
                                            if($debuff[5] > 0)
                                                echo '+';
                                            echo number_format($skill->GetLPValue() / 100 * $skill->GetValue(), '0', '', '.') . '<br/>';
                                        }
                                        if($debuff[7] != 0)
                                        {
                                            echo 'AD: ';
                                            if($debuff[7] > 0)
                                                echo '+';
                                            echo number_format($skill->GetKPValue() / 100 * $skill->GetValue(), '0', '', '.') . '<br/>';
                                        }
                                        if($debuff[9] != 0)
                                        {
                                            echo 'Taunt: ';
                                            if($debuff[9] > 0)
                                                echo '+';
                                            echo number_format($skill->GetTauntValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($debuff[10] != 0)
                                        {
                                            echo 'Reflect: ';
                                            if($debuff[10] > 0)
                                                echo '+';
                                            echo number_format($skill->GetReflectValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($debuff[11] != 0)
                                        {
                                            echo 'Reflex: ';
                                            if($debuff[11] > 0)
                                                echo '+';
                                            echo number_format($skill->GetReflexValue() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        if($debuff[12] != 0)
                                        {
                                            echo 'Genauigkeit: ';
                                            if($debuff[12] > 0)
                                                echo '+';
                                            echo number_format($skill->GetAccBuff() / 100 * $skill->GetValue(), '0', '', '.') . '%<br/>';
                                        }
                                        ?>
                                    </span>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                        <?php
                        if ($player != null && $player->GetTeam() == $id)
                        {
                            ?>
                            <div class="info smallBG borderT borderB borderR borderL boxSchatten">Douriki: <?php echo number_format($fighter->GetKI(),'0', '', '.'); ?></div>
                            <div class="spacer"></div>
                            <?php
                        }
                        ?>
                        <?php
                        if($player != null)
                        {
                            Global $database;
                            $adminPlayer = new Player($database, $player->GetAcc());
                            if($adminPlayer->GetArank() >= 2)
                            {
                                ?>
                                <div class="info smallBG borderT borderB borderR borderL boxSchatten">Attack: <?php echo number_format($fighter->GetAttack(),'0', '', '.'); ?></div>
                                <div class="spacer"></div>
                                <div class="info smallBG borderT borderB borderR borderL boxSchatten">Defense: <?php echo number_format($fighter->GetDefense(),'0', '', '.'); ?></div>
                                <div class="spacer"></div>
                                <?php
                            }

                        }
                        ?>
                        <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                            <?php
                            if ($fighter->GetLP() == 0)
                            {
                                ?>Besiegt<?php
                            }
                            else if ($fighter->GetAction() == 0)
                            {
                                ?>Keine Aktion<?php
                            }
                            else if ($player != null && $player->GetTeam() == $id)
                            {
                                $attack = $fight->GetAttack($fighter->GetAction());
                                echo $attack->GetName();
                            }
                            else
                            {
                                ?>Wartet<?php
                            }
                            ?>
                        </div>
                        <?php
                        if ($player != null && $player->GetTeam() == $id && $fighter->GetTarget() != 0)
                        {
                            ?>
                            <div class="spacer"></div>
                            <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                                <?php
                                $target = $fight->GetFighter($fighter->GetTarget());
                                echo $target->GetName();
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="spacer"></div>
                        <?php
                        if ($player != null && $fighter->GetName() != $player->GetName())
                        {
                            if ($fighter->GetAction() == 0 && $fighter->GetLP() != 0 && !$fighter->IsNPC() && !$fighter->HasNPCControl())
                            {
                                $cooldown = $fighter->GetActionCountdown($fight->GetType());
                                ?>
                                <div class="info smallBG borderT borderB borderR borderL boxSchatten">
                                <?php
                                if ($cooldown <= 0)
                                {
                                    ?>
                                    <form method="post" action="?p=infight&a=kick&id=<?php echo $fighter->GetID(); ?>">
                                        <input type="submit" value="Kick">
                                    </form>
                                    <?php
                                }
                                else
                                {
                                    ?>

                                    <div id="cID<?php echo $fighter->GetID(); ?>">Init<script>
                                            countdown(<?php echo $cooldown; ?>, 'cID<?php echo $fighter->GetID(); ?>', 'Aktualisieren');
                                        </script>
                                    </div>
                                    <?php
                                }
                                ?>
                                </div><?php
                            }
                            ?>
                            <div class="spacer"></div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="spacer"></div>
                <?php
                ++$i;
            }
            ?>
            <!-- Player Ende -->
        </div>
    </div> <!-- SideMenuContainer -->
    <div class="spacer"></div>
    <?php
}
?>