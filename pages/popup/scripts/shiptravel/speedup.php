<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../../classes/header.php';
if(!$player->IsLogged())
{
    exit();
}

$marineShipArray = array(42, 384, 385);
$multiplier = 200;

if(in_array($player->GetInventory()->GetShipItemID(), $marineShipArray) && $player->GetRace() == "Marine")
    $multiplier = 400;
$var = (round($player->GetLevel() * $multiplier));
?>
    let slider = document.getElementById("myRange");
    let factor = document.getElementById("factor");
    let reducehours = document.getElementById("reducehours");
    let hours = document.getElementById("hours");
    let cost1 = document.getElementById("cost1");
    factor.innerHTML = slider.value; // Display the default slider value

    // Update the current slider value (each time you drag the slider handle)
    slider.oninput = function() {
        factor.innerHTML = this.value;
        reducehours.innerHTML = this.value * 1;
        if(this.value > 1)
        {
            hours.innerHTML = "Stunden";
        }
        else
        {
            hours.innerHTML = "Stunde";
        }
        cost1.innerHTML = (this.value * <?php echo $var; ?>).toLocaleString();

    }