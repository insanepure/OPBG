<?php
    if($player->GetArank() < 3)
{
    header('Location: ?p=news');
    exit();
}

$endofevent = new DateTime("2023-08-17 00:00:00");
$date = new DateTime();
if($date->getTimestamp() >= $endofevent->GetTimestamp())
{
    exit();
}

include_once ('classes/items/itemmanager.php');
$itemManager = new ItemManager($database);
$title = 'Orangenbäume';
if(!$player->IsLogged() || $player->GetClan() == 0)
{
    header('Location: ?p=news');
    exit();
}
$scale1 = 0.1;
$scale2 = 0;
$scale3 = 0;

$orangeAmount = 0;
$clanMembers = new generalList($database, 'accounts', '*', 'clan='.$clan->GetID());

$id = 0;
$entry = $clanMembers->GetEntry($id);
while($entry != null)
{
    $orangeAmount += $entry['münzen'];
    $id++;
    $entry = $clanMembers->GetEntry($id);
}

if($orangeAmount >= 100)
    $scale1 = 1;
else if($orangeAmount > 0)
    $scale1 = $orangeAmount / 100;

if($orangeAmount >= 250)
    $scale2 = 1;
else if($orangeAmount >= 100)
    $scale2 = $orangeAmount / 250;

if($orangeAmount == 500)
    $scale3 = 1;
else if($orangeAmount >= 250)
    $scale3 = $orangeAmount / 500;

if($scale1 > 1)
    $scale1 = 1;
if($scale2 > 1)
    $scale2 = 1;
if($scale3 > 1)
    $scale3 = 1;

//$belohnungen[x] => date
//$belohnungen[x][x] => itemid
//$belohnungen[x][x][x] => itemamount
$diff = strtotime("2023-08-01") - strtotime("2023-08-17");
$daysSince = abs(round($diff / 86400));

$eDate = date('d') - 1;
if($eDate < 0)
    $eDate = 0;
$belohnungen = array(
    array(array(57,2), array(55,2), array(56,2)), // Schuhe 2x, Schwarzgurt 2x, Schwert 2x
    array(array('berry',1500), array('berry',2500), array('berry',5000)), // Berry 1500, Berry 2500, Berry 5000
    array(array(193,5), array(302,1), array("stats",20)), // Cola 5x, Reiseticket 1x, Stats 20x
    array(array(31,1), array(33,1), array(34,1)), // Leichte Medizin 1x, Mittlere Medizin 1x, Starke Medizin 1x
    array(array('berry',3000), array(81,1), array(303,1)), // Berry 3000, Testo Booster 1x, Sommer Badehose 1x
    array(array(192,5), array(302,1), array("stats",20)), // Fleischkeulen 5x, Reiseticket 1x, Stats 20x
    array(array('gold',10), array('gold',15), array('gold',25)), // Gold 10, Gold 15, Gold 25
    array(array('berry',3000), array(82,1), array(304,1)), // Berry 3000, Vitamine 1x, Sommer Badering 1x
    array(array(60,1), array(60,2), array(60,3)), // Schatztruhe 1x, Schatztruhe 2x, Schatztruhe 3x
    array(array(53,2), array(52,2), array(54,2)), // Paramecia 2x, Zoan 2x, Logia 2x
    array(array(49,2), array(50,2), array(51,2)), // Holz 2x, Nägel 2x, Stoff 2x
    array(array('gold',100), array(305,1), array('berry', 5000)), // Gold 100, Sommer Wasserpistole 1x, 5000 Berry
    array(array(71,1), array(71,2), array(174,1)), // Schmied W. 1x, Schmied W. 2x, Rüstungskristall 1x
    array(array('gold',20), array(119,1), array(119,2)), // Gold 20, Rumble Ball 1x, Rumble Ball 2x
    array(array(103,1), array(103,2), array(103,3)), // Geldsack 1x, Geldsack 2x, Geldsack 3x
    array(array(86,1), array(87,1), array(137,1)), // Rote Frucht 1x, Orange Frucht 1x, Kerkerschlüssel 1x
    array(array(102,1), array(102,2), array(102,3)), // Regenbogentruhe 1x, Regenbogentruhe 2x, Regenbogentruhe 3x
);

if(isset($_GET['open']) && (!is_numeric($_GET['open']) || $_GET['open'] > 3 || $_GET['open'] < 1))
{
    $message = 'Ungültige Eingabe.';
}
else
{
    if($_GET['open'] == 1)
    {
        if($orangeAmount < 100)
        {
            $message = 'Ihr habt noch nicht genug Orangen gesammelt.';
        }
        else if($player->HasOrange1())
        {
            $message = 'Du hast die Belohnung bereits eingesammelt!';
        }
        else
        {
            if($belohnungen[$eDate][0][0] == 'gold')
            {
                $player->AddGold($belohnungen[$eDate][0][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][0][1], 0, '', '.') . '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="height: 20px; width: 20px;"/> erhalten.';
            }
            else if($belohnungen[$eDate][0][0] == 'berry')
            {
                $player->AddBerry($belohnungen[$eDate][0][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][0][1], 0, '', '.') . '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="height: 20px; width: 13px;"/> erhalten.';
            }
            else if($belohnungen[$eDate][0][0] == "stats")
            {
                $player->AddStats2($belohnungen[$eDate][0][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][0][1], 0, '', '.') . ' Statpunkte erhalten.';
            }
            else
            {
                $item = $itemManager->GetItem($belohnungen[$eDate][0][0]);
                $player->AddItems($item, $item, $belohnungen[$eDate][0][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][0][1], 0, "", ".") .'x ' . $item->GetName() . ' erhalten.';
            }
            $database->Update('orange1=1', 'accounts', 'id='.$player->GetID());
            $player->SetOrange1(1);
        }
    }
    else if($_GET['open'] == 2)
    {
        if($orangeAmount < 250)
        {
            $message = 'Ihr habt noch nicht genug Orangen gesammelt.';
        }
        else if($player->HasOrange2())
        {
            $message = 'Du hast die Belohnung bereits eingesammelt!';
        }
        else
        {
            if($belohnungen[$eDate][1][0] == 'gold')
            {
                $player->AddGold($belohnungen[$eDate][1][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][1][1], 0, '', '.') . '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="height: 20px; width: 20px;"/> erhalten.';
            }
            else if($belohnungen[$eDate][1][0] == 'berry')
            {
                $player->AddBerry($belohnungen[$eDate][1][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][1][1], 0, '', '.') . '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="height: 20px; width: 13px;"/> erhalten.';
            }
            else if($belohnungen[$eDate][1][0] == "stats")
            {
                $player->AddStats2($belohnungen[$eDate][1][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][1][1], 0, '', '.') . ' Statpunkte erhalten.';
            }
            else
            {
                $item = $itemManager->GetItem($belohnungen[$eDate][1][0]);
                $player->AddItems($item, $item, $belohnungen[$eDate][1][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][1][1], 0, '', '.') .'x ' . $item->GetName() . ' erhalten.';
            }
            $database->Update('orange2=1', 'accounts', 'id='.$player->GetID());
            $player->SetOrange2(1);
        }
    }
    else if($_GET['open'] == 3)
    {
        if($orangeAmount < 500)
        {
            $message = 'Ihr habt noch nicht genug Orangen gesammelt.';
        }
        else if($player->HasOrange3())
        {
            $message = 'Du hast die Belohnung bereits eingesammelt!';
        }
        else
        {
            if($belohnungen[$eDate][2][0] == 'gold')
            {
                $player->AddGold($belohnungen[$eDate][2][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][2][1], 0, '', '.') . '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="height: 20px; width: 20px;"/> erhalten.';
            }
            else if($belohnungen[$eDate][2][0] == 'berry')
            {
                $player->AddBerry($belohnungen[$eDate][2][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][2][1], 0, '', '.') . '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="height: 20px; width: 13px;"/> erhalten.';
            }
            else if($belohnungen[$eDate][2][0] == "stats")
            {
                $player->AddStats2($belohnungen[$eDate][2][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][2][1], 0, '', '.') . ' Statpunkte erhalten.';
            }
            else
            {
                $item = $itemManager->GetItem($belohnungen[$eDate][2][0]);
                $player->AddItems($item, $item, $belohnungen[$eDate][2][1]);
                $message = 'Du hast ' . number_format($belohnungen[$eDate][2][1], 0, '', '.') .'x ' . $item->GetName() . ' erhalten.';
            }
            $database->Update('orange3=1', 'accounts', 'id='.$player->GetID());
            $player->SetOrange3(1);
        }
    }
}

