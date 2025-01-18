<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/arena/arena.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';

$title = 'Kolosseum';
$itemManager = new ItemManager($database);

$shipArray = array(1, 40, 41, 42, 43, 44, 45, 46, 47, 48, 384, 385);
$pirateShipArray = array(1, 40, 41, 43, 44, 45, 46, 47, 48);
$marineShipArray = array(42, 384, 385);
$arena = new Arena($database);
if(isset($_GET['a']) && $_GET['a'] == 'setpvp')
{
   if(!$arena->HasPvpEnabled($player->GetID()))
   {
       $result = $database->Update('kolopvp=1', 'arenafighter', 'fighter="'.$player->GetID().'"');
   }
   else
   {
       $result = $database->Update('kolopvp=0', 'arenafighter', 'fighter="'.$player->GetID().'"');
   }
}
if (isset($_GET['a']) && $_GET['a'] == 'buy')
{
    if (!isset($_POST['item']) || !is_numeric($_POST['item']))
    {
        $message = 'Das Item ist ungültig.';
    }
    else if (!isset($_POST['amount']) || !is_numeric($_POST['amount']) || floor($_POST['amount']) <= 0)
    {
        $message = 'Die Anzahl ist ungültig.';
    }
    else if ($player->GetFight() != 0)
    {
        $message = 'Du bist in einem Kampf und kannst das Item nicht kaufen.';
    }
    else if($player->GetCapchaCount() >= 20)
    {
        $message = "Du musst erst das Captcha bestätigen bist du weiterspielen kannst";
    }
    else
    {
        $amount = $_POST['amount'];
        $item = $itemManager->GetItem($_POST['item']);
        if ($item == null)
        {
            $message = 'Das Item gibt es nicht.';
        }
        else
        {
            $arenaPoints = $item->GetArenaPoints() * $amount;
            if ($player->GetArenaPoints() < $arenaPoints)
            {
                $message = 'Du hast nicht genügend Kolosseumspunkte.';
            }
            else if ($arenaPoints == 0)
            {
                $message = 'Du kannst das item nicht kaufen.';
            }
            else if ($item->GetNeedItem() != 0 && !$player->HasItemWithID($item->GetNeedItem(), $item->GetNeedItem()))
            {
                $message = 'Du benötigst ein besonderes Item.';
            }
            else if ($player->GetInventory()->HasShip() && in_array($item->GetID(), $shipArray))
            {
                $message = "Du besitzt bereits ein Schiff!";
            }
            else if (in_array($item->GetID(), $shipArray) && ($player->GetRace() == "Marine" && !in_array($item->GetID(), $marineShipArray) || $player->GetRace() == "Pirat" && !in_array($item->GetID(), $pirateShipArray)))
            {
                $message = "Dieses Schiff ist für deine Fraktion nicht geeignet.";
            }
            else
            {
                $statstype = $item->GetDefaultStatsType();
                $upgrade = 0;
                $player->BuyItem($item, $item, $statstype, $upgrade, $amount, 0, $arenaPoints);
                $message = 'Du hast ' . number_format($amount, '0', '', '.') . 'x ' . $item->GetName() . ' gekauft.';
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'search')
{
    if(!$arena->IsFighterIn($player->GetID()))
    {
        $message = 'Du bist nicht in dem Kolosseum.';
    }
    else if($player->GetBookingDays() > 0)
    {
        $message = "Du kannst keinen Kampf suchen wenn du am pausieren bist!";
    }
    else if($player->IsKoloClose() == 1)
    {
        $message = "Du wurdest für diesen Tag vom Kolosseum ausgeschlossen";
    }
    else if($player->GetLP() != $player->GetMaxLP())
    {
        $message = 'Du musst dich erst voll heilen, bevor du einen Kampf starten kannst';
    }
    else if ($player->GetTournament() != 0)
    {
        $message = 'Du kannst während des Turniers nichts kämpfen.';
        $arena->Leave($player->GetID());
    }
    else if($player->GetFight() != 0)
    {
        $message = 'Du befindest dich schon in einem Kampf';
    }
    else if($player->GetCapchaCount() >= 20)
    {
        $message = "Bitte bestätige zunächst den Captcha Code um weiterspielen zu können";
    }
    else
    {
        $enemyID = $arena->GetRandomFighter($player->GetID());
        $createdFight = null;
        if ($enemyID != -1 && rand(0,1) == 0)
        {
            $otherPlayer = new Player($database, $enemyID, $actionManager);
            if ($otherPlayer->GetTournament() != 0)
            {
                $message = 'Er kann während des Turniers nichts kämpfen.';
                $arena->Leave($otherPlayer->GetID());
            }
            else if ($otherPlayer->GetFight() == 0)
            {
                $type = 8;
                $name = $player->GetName() . ' vs ' . $otherPlayer->GetName();
                $mode = '1vs1';
                $createdFight = Fight::CreateFight($player, $database, $type, $name, $mode, 0, $actionManager);
                if ($createdFight)
                {
                    $arena->UpdateFight($player->GetID(), $otherPlayer->GetID());
                    $player->UpdateFight($createdFight->GetID());
                    $otherPlayer->UpdateFight($createdFight->GetID());
                    header('Location: ?p=infight');
                }
                $createdFight->Join($player, 0, false);
                $createdFight->Join($otherPlayer, 1, false);


                exit();
            }
        }

        if($player->GetFight() == 0 && ($createdFight == null || !$createdFight->IsStarted()))
        {
            $npcs = new GeneralList($database, 'npcs', '*', 'iskolo=1 AND level <= ' . $player->GetLevel(), 'id', 99999);
            $npcid = $npcs->GetEntry(rand(0, $npcs->GetCount() - 1))['id'];
            $npc = new NPC($database, $npcid);
            $message = 'Es wurde ein Kampf gegen ' . $npc->GetName() . ' eröffnet';
            $items = '';
            $name = $player->GetName() . ' vs ' . $npc->GetName();
            $mode = '1vs1';
            $team = 1;
            $type = 8;
            $pvp = 0;
            $berry = 0;
            $gold = 0;
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
                $npcid,
                $difficulty,
                $healthRatio,
                $healthRatioTeam,
                $healthRatioWinner
            );
            $createdFight->Join($player, 0);
            $createdFight->Join($npc, $team, true);
            $arena->UpdateFight($player->GetID(), 0);
            if ($createdFight->IsStarted()) {
                $player->UpdateFight($createdFight->GetID());
                $player->SetLastNPCID($npcid);
                header('Location: ?p=infight');
                exit();
            }
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'join')
{
    if($player->GetArenaKickCount() >= 3)
    {
        $message = 'Du wurdest heute bereits 3x wegen Inaktivität aus dem Kolosseum gekickt. Aus diesem Grund erhältst du eine Sperre bis zum nächsten Update.';
    }
    else if ($arena->IsFighterIn($player->GetID()))
    {
        $message = 'Du bist schon in dem Kolosseum.';
    }
    else if($player->GetBookingDays() > 0)
    {
        $message = "Du kannst dem Kolosseum nicht beitreten wenn du am pausieren bist!";
    }
    else if (!$player->IsVerified())
    {
        $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
    }
    else if ($player->GetLevel() < 5)
    {
        $message = 'Du musst mindestens Level 5 sein um dem Kolosseum beizutreten.';
    }
    else if($player->GetFight())
    {
        $message = 'Du kannst das Kolosseum während eines Kampfes nicht betreten.';
    }
    else
    {
        $arena->Join($player->GetID());
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'leave')
{
    if (!$arena->IsFighterIn($player->GetID()))
        $message = 'Du bist nicht in dem Kolosseum.';
    else
    {
        $arena->Leave($player->GetID());
        $message = 'Du hast das Kolosseum verlassen.';
    }
}
else if($_GET['p'] == 'arena' && $_GET['change'] == 'kolopunkte')
{
    $money = $_POST['money'];
    $minusmensch = $player->GetArenaPoints() - $money;
    $changethemoney = $money * 25;
    $moneyboy = $player->GetBerry() + $changethemoney;
    $dispo = $player->GetArenaPoints() - $money;
    if($minusmensch < 0)
    {
        $message = "Du kannst nicht mehr umtauschen als du zur Verfügung hast!";
    }
    else if(!is_numeric($money))
    {
        $message = "Es sind nur Zahlen erlaubt!";
    }
    else
    {
        $result = $database->Update('zeni="'.$moneyboy.'", arenapoints="'.$dispo.'"', 'accounts', 'id="'.$player->GetID().'"');
        $message = "Dir wurden ".$money." Kolosseumpunkte abgezogen, es sind ".$minusmensch." Punkte übrig geblieben! Dir wurden durch diesen Tausch ".$changethemoney." Berry gut geschrieben";
    }
}