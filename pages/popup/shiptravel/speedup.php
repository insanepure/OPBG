<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
if(!$player->IsLogged())
{
    echo 'Du bist nicht eingeloggt.';
    exit();
}

$marineShipArray = array(42, 384, 385);

$leftHours = ceil($player->GetTravelActionCountdown() / 3600);
$maxHours = 1;
if(round(($player->GetLevel() * 200)) <= $player->GetBerry())
{
    $maxHours = floor($player->GetBerry() / round($player->GetLevel() * 200));
}
if(in_array($player->GetInventory()->GetShipItemID(), $marineShipArray) && $player->GetRace() == "Marine" && round(($player->GetLevel() * 400)) <= $player->GetBerry())
{
    $maxHours = floor($player->GetBerry() / round(($player->GetLevel() * 400)));
}
$maxHours = min($leftHours, $maxHours);
if($maxHours > 100)
    $maxHours = 100;

$multiplier = 200;
if(in_array($player->GetInventory()->GetShipItemID(), $marineShipArray) && $player->GetRace() == "Marine")
    $multiplier = 400;
?>
Möchtest du die Reise beschleunigen?<br/>
Dadurch verringert sich die Reisezeit um <b id="reducehours">1</b> <span id="hours">Stunde</span><br/>
Es kostet dich aber <b id="cost1"><?php echo number_format((round($player->GetLevel() * $multiplier)), '0', '', '.'); ?></b> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>.<br/>
<br/>
<form action="?p=profil&a=speedup" method="post">
    Beschleunigungsfaktor: <span id="factor">1</span>
    <br/>
    <label for="myRange">
        <input type="range" min="1" max="<?php echo $maxHours; ?>" value="1" class="slider" id="myRange" name="factor">
    </label>
    <br/>
    <input type="submit" class="ja" value="Beschleunigen">
</form>