<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../../classes/header.php';
if(!$player->IsLogged())
{
  echo 'Du bist nicht eingeloggt.';
  exit();
}

$multiplier = 100;
$marineShipArray = array(42, 384, 385);

if(in_array($player->GetInventory()->GetShipItemID(), $marineShipArray) && $player->GetRace() == "Marine")
    $multiplier = 200;

$var = (round(($player->GetLevel() * $multiplier)));
?>

var slider = document.getElementById("myRange");
var factor = document.getElementById("factor");
var reduceminutes = document.getElementById("reduceminutes");
var cost1 = document.getElementById("cost1");
factor.innerHTML = slider.value; // Display the default slider value

// Update the current slider value (each time you drag the slider handle)
slider.oninput = function() {
  factor.innerHTML = this.value;
  reduceminutes.innerHTML = this.value * 10;
  cost1.innerHTML = (this.value * <?php echo $var; ?>).toLocaleString();

}