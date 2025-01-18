<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
$zorders = array();
$zorders[1] = 0; //aura
$zorders[6] = 1; //weapon
$zorders[9] = 2; //saiyatail
$zorders[10] = 3; //holyshine
$zorders[0] = 4; //body
$zorders[3] = 5; //hose
$zorders[5] = 8; //brust
$zorders[7] = 6; //schuhe
$zorders[2] = 10; //hand
$zorders[4] = 11; //reise
$zorders[8] = 12; //accessoire
$zorders[11] = 17; //clan
$zorders[12] = 18; //tooltip

$zordersOnTop[1] = 0; //aura
$zordersOnTop[6] = 1; //weapon
$zordersOnTop[9] = 2; //saiyatail
$zordersOnTop[10] = 3; //holyshine
$zordersOnTop[0] = 4; //body
$zordersOnTop[3] = 7; //hose
$zordersOnTop[5] = 13; //brust
$zordersOnTop[7] = 9; //schuhe
$zordersOnTop[2] = 14; //hand
$zordersOnTop[4] = 15; //reise
$zordersOnTop[8] = 16; //accessoire
$zordersOnTop[11] = 17; //clan
$zordersOnTop[12] = 18; //tooltip


function ShowSlotEquippedImage($slot, $inventory, $zorders, $zordersOnTop)
{
    $item = $inventory->GetItemAtSlot($slot);
    if ($item != null) {
        if ($item->IsOnTop())
            $zindex = $zordersOnTop[$slot];
        else
            $zindex = $zorders[$slot];

        if($item->GetEquippedImage() != '')
        {
            ?>
            <div class="profilecharacter equip" style="top:20px; left:150px; z-index:<?php echo $zindex; ?>; background-image:url('img/ausruestung/<?php echo $item->GetEquippedImage(); ?>.png?005')"></div>
            <?php
        }
    }
}
if (!isset($_GET['id']))
{
    exit();
}
if (!$player->IsLogged())
{
    echo 'Du bist nicht eingeloggt.';
    exit();
}
?>
<img class="profilecharacter" id="image" src="img/races/<?php echo $player->GetRaceImage();?>.png?003" style="width: 300px; height: 500px; top:20px; left:150px; z-index:<?php echo $zorders[0]; ?>;">
<img class="profilecharacter" id="imageHead" src="img/races/<?php echo $player->GetRaceImage();?>Head.png?003" style="width: 300px; height: 500px; top:20px; left:150px; z-index:11;">
<div style="width: 300px; height: 500px;"></div>
<?php
$inventory = $player->GetInventory();
if ($player->GetPlanet() == 2)
{
    ?>
    <div class="profilecharacter" style="top:20px; left:150px; z-index:<?php echo $zorders[5]; ?>; background-image: url('img/ausruestung/ImpelDownOben.png?002'"></div>
    <div class="profilecharacter" style="top:20px; left:150px; z-index:<?php echo $zorders[2]; ?>; background-image: url('img/ausruestung/FesselnOben2.png?002'"></div>
    <div class="profilecharacter" style="top:20px; left:150px; z-index:<?php echo $zorders[3]; ?>; background-image: url('img/ausruestung/ImpelDownUnten.png?002'"></div>
    <div class="profilecharacter" style="top:20px; left:150px; z-index:<?php echo $zorders[7]; ?>; background-image: url('img/ausruestung/Fesseln.png?002'"></div>
    <?php
}
else
{
    ShowSlotEquippedImage(6, $inventory, $zorders, $zordersOnTop); //Waffe
    ShowSlotEquippedImage(1, $inventory, $zorders, $zordersOnTop); //Aura
    ShowSlotEquippedImage(5, $inventory, $zorders, $zordersOnTop); // Brust
    ShowSlotEquippedImage(8, $inventory, $zorders, $zordersOnTop); //Accessoire
    ShowSlotEquippedImage(3, $inventory, $zorders, $zordersOnTop); //Hose
    ShowSlotEquippedImage(2, $inventory, $zorders, $zordersOnTop); // Fesseln
    ShowSlotEquippedImage(7, $inventory, $zorders, $zordersOnTop); //Schuhe
    ShowSlotEquippedImage(4, $inventory, $zorders, $zordersOnTop); //Reise
}
?>
<div class="spacer"></div>
<input id="showequip" type="checkbox" checked style="cursor: pointer;" onclick="ToggleEquip(this)"><label for="showequip" style="cursor: pointer;"> Ausrüstung anzeigen</label>
<div class="spacer"></div>
<form method="post" action="?p=profil&a=picture">
    <select class="select" name="raceimage" id="raceimage" onchange="onImageSelected(this)">
        <option value="<?php echo $player->GetRace(); ?>1" <?php if($player->GetRaceImage() == $player->GetRace() . "1") echo "selected"; ?>><?php echo $player->GetRace(); ?> 1</option>
        <option value="<?php echo $player->GetRace(); ?>2" <?php if($player->GetRaceImage() == $player->GetRace() . "2") echo "selected"; ?>><?php echo $player->GetRace(); ?> 2</option>
        <option value="<?php echo $player->GetRace(); ?>3" <?php if($player->GetRaceImage() == $player->GetRace() . "3") echo "selected"; ?>><?php echo $player->GetRace(); ?> 3</option>
        <option value="<?php echo $player->GetRace(); ?>4" <?php if($player->GetRaceImage() == $player->GetRace() . "4") echo "selected"; ?>><?php echo $player->GetRace(); ?> 4</option>
        <option value="<?php echo $player->GetRace(); ?>5" <?php if($player->GetRaceImage() == $player->GetRace() . "5") echo "selected"; ?>><?php echo $player->GetRace(); ?> 5</option>
        <option value="<?php echo $player->GetRace(); ?>6" <?php if($player->GetRaceImage() == $player->GetRace() . "6") echo "selected"; ?>><?php echo $player->GetRace(); ?> 6</option>
        <option value="<?php echo $player->GetRace(); ?>7" <?php if($player->GetRaceImage() == $player->GetRace() . "7") echo "selected"; ?>><?php echo $player->GetRace(); ?> 7</option>
        <option value="<?php echo $player->GetRace(); ?>8" <?php if($player->GetRaceImage() == $player->GetRace() . "8") echo "selected"; ?>><?php echo $player->GetRace(); ?> 8</option>
        <option value="<?php echo $player->GetRace(); ?>9" <?php if($player->GetRaceImage() == $player->GetRace() . "9") echo "selected"; ?>><?php echo $player->GetRace(); ?> 9</option>
        <option value="<?php echo $player->GetRace(); ?>10" <?php if($player->GetRaceImage() == $player->GetRace() . "10") echo "selected"; ?>><?php echo $player->GetRace(); ?> 10</option>
        <?php
        if($player->GetRace() == 'Pirat')
        {
            ?>
            <option value="<?php echo $player->GetRace(); ?>11" <?php if($player->GetRaceImage() == $player->GetRace() . "11") echo "selected"; ?>><?php echo $player->GetRace(); ?> 11</option>
            <option value="<?php echo $player->GetRace(); ?>12" <?php if($player->GetRaceImage() == $player->GetRace() . "12") echo "selected"; ?>><?php echo $player->GetRace(); ?> 12</option>
            <option value="<?php echo $player->GetRace(); ?>13" <?php if($player->GetRaceImage() == $player->GetRace() . "13") echo "selected"; ?>><?php echo $player->GetRace(); ?> 13</option>
            <?php
        }
        ?>

    </select>
    <div class="spacer"></div>
    <button type="submit" class="ja">Auswählen</button>
</form>
<p class="info" style="color:red; font-weight: bold;">Das Wechseln kostet 25.000 <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>!</p>
<div class="spacer"></div>