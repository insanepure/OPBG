<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
if(!$player->IsLogged())
{
  echo 'Du bist nicht eingeloggt.';
  exit();
}

$marineShipArray = array(42, 384, 385);
$leftMinutes = ceil($player->GetTravelActionCountdown() / 600);
$maxMinutes = 1;
if(round(($player->GetLevel() * 100)) <= $player->GetBerry())
{
	$maxMinutes = floor($player->GetBerry() / round(($player->GetLevel() * 100)));
}
if(in_array($player->GetInventory()->GetShipItemID(), $marineShipArray) && $player->GetRace() == "Marine" && round(($player->GetLevel() * 200)) <= $player->GetBerry())
{
    $maxMinutes = floor($player->GetBerry() / round(($player->GetLevel() * 200)));
}
$maxMinutes = min($leftMinutes, $maxMinutes);
if($maxMinutes > 100)
    $maxMinutes = 100;

$multiplier = 100;
if(in_array($player->GetInventory()->GetShipItemID(), $marineShipArray) && $player->GetRace() == "Marine")
    $multiplier = 200;

?>
Möchtest du die Reise beschleunigen?<br/>
Dadurch verringert sich die Reisezeit um <b id="reduceminutes">10</b> Minuten<br/>
Es kostet dich aber <b id="cost1"><?php echo number_format((round(($player->GetLevel() * $multiplier))), '0', '', '.'); ?></b> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>.<br/>
<br/>
<form action="?p=profil&a=speedup" method="post">
    Beschleunigungsfaktor: <span id="factor">1</span>
    <br/>
    <input type="range" min="1" max="<?php echo $maxMinutes; ?>" value="1" class="slider" id="myRange" name="factor">
    <br/>
    <input type="submit" class="ja" value="Beschleunigen">
</form>