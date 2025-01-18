<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/places/place.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
$itemManager = new ItemManager($database);

$inventory = $player->GetInventory();

$place = null;

$marineShipArray = array(42, 384, 385);

if (isset($_GET['id']))
{
    $place = new Place($database, $_GET['id'], null);
    if (!$place->IsValid() || $place->GetPlanet() != $player->GetPlanet())
    {
        $place = null;
    }
}

if ($place == null)
{
    ?>
    Dieser Ort befindet sich nicht auf diesem Meer.
    <?php
}
else
{
    ?>
    <div class="spacer"></div>
    <div class="reise2">
    <div class="reisebild"><img src="img/places/<?php echo $place->GetImage(); ?>.png" width="100%" height="100%"></div>
    <div class="reisebeschreibung" style="color:black;">
        <?php
        echo $bbcode->parse($place->GetDescription());
        ?>
        <div style="position: absolute; top:0; display: flex; align-items: flex-end; height: 100%;">
            <?php
            if($place->IsEarnable())
            {
                if($place->GetTerritorium() == 0)
                {
                    echo "<b>Es scheint noch keine Bande diesen Ort im Besitz zu haben!</b>";
                }
                else
                {
                    $bande = new Clan($database, $place->GetTerritorium());
                    ?>
                    <span>Dieser Ort ist im Besitz der Bande <b><a style="color:black;" href="?p=clan&id=<?php echo $bande->GetID(); ?>"><?php echo $bande->GetName(); ?></a></b>
                            <?php
                            if($player->GetClan() != $bande->GetID() && $player->GetLevel() >= 5)
                            {
                                $price = 10;
                                if(in_array($player->GetClan(), $bande->GetAlliances()))
                                    $price = 5;
                                ?>
                                Die Reise kostet <?= $price ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>.
                                <?php
                            }
                            ?>
                            </span>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <div class="reisedauer">
    <?php
    if (($place->IsTravelable() || !$place->IsTravelable() && $player->GetARank() >= 2) && $place->GetName() != $player->GetPlace())
    {
        $playerPlace = new Place($database, $player->GetPlace(), null);
        $travelTime = 0;

        if ($player->GetX() != 0 && $player->GetY() != 0)
        {
            $x = $player->GetX();
            $y = $player->GetY();
        }
        else
        {
            $x = $playerPlace->GetX();
            $y = $playerPlace->GetY();
        }
        $travelTime = round(round(abs($place->GetX() - $x) + abs($place->GetY() - $y)) / 10);

        $travelBonus = $travelTime / 100 * $player->GetTravelBonus();
        if ($travelTime < 10)
        {
            $travelTime = 10;
        }
        else if ($travelTime > 60)
        {
            $travelTime = 60;
        }

        $travelTimeOriginalHours = floor($travelTime / 60);
        $travelTimeOriginalMinutes = $travelTime - ($travelTimeOriginalHours * 60);

        $travelTimeDisplay = $travelTime - $travelBonus;
        if($travelTimeDisplay < 10)
            $travelTimeDisplay = 10;
        $travelTimeHours = floor($travelTimeDisplay / 60);
        if($player->GetTravelTicket() != 0)
        {
            $travelTimeMinutes = 0;
        }
        else {
            if($player->GetID() == 506)
                var_dump($travelTimeDisplay);
            $travelTimeMinutes = (($travelTimeHours >= 1) ? 60 : $travelTimeDisplay);
        }

        if($player->GetRace() == "Marine" && in_array($player->GetInventory()->GetShipItemID(), $marineShipArray))
            $travelTimeMinutes = round($travelTimeMinutes / 2);
        if($place->GetPlaceLevel() > $player->GetLevel())
        {
            ?>
            <div style="color:black; display: flex; height: 100%; align-items: center; justify-content: center; align-content: center;">
                            <span>
                                Info: <?php echo "<b>Du musst für diesen Ort <span style='white-space: nowrap;'>Level: " . number_format($place->GetPlaceLevel(), 0, '', '.') . "</span> sein.</b>"; ?>
                            </span>
            </div>
            <?php
        }
        else
        {
            if ($inventory->HasShipEquipped($inventory->GetShip()))
            {
                if($inventory->GetShipWear() < $inventory->GetShipMaxWear($inventory->GetShipItemID()))
                {
                    if($player->GetArank() == 3)
                    {
                        echo $inventory->GetShipWear();
                    }
                    ?>
                    <div style="color:black;">Die Reisedauer nach <?php echo "<br />" . $place->GetName() . "<br />dauert " . $travelTimeMinutes . " Minuten <br />"; ?></div>
                    <form method="POST" action="?p=map&a=travel">
                        <input type="hidden" name="destination" value="<?php echo $place->GetID(); ?>">
                        <input type="submit" value="Reisen">
                    </form>
                    <div class="spacer"></div>
                    <?php
                }
                else
                {
                    ?>
                    <div style="color:black; display: flex; height: 100%; align-items: center; justify-content: center; align-content: center;">
                                <span>
                                    Info: <?php echo "<b>Dein Schiff ist zu abgenutzt.</b>"; ?>
                                </span>
                    </div>
                    <?php
                }
            }
            else
            {
                ?>
                <div style="color:black; display: flex; height: 100%; align-items: center; justify-content: center; align-content: center;">
                        <span>
                            Info: <?php echo "<b>Du benötigst ein Schiff um zu reisen.</b>"; ?>
                        </span>
                </div>
                <div class="spacer"></div>
                <?php
            }
        }
        ?>
        </div>
        </div>
        <?php
    }
    else
    {
        ?>
        <div style="color:black; display: flex; height: 100%; align-items: center; justify-content: center; align-content: center;">
                            <span>
                                Info: <?php echo "<b>Hier befindest du dich.</b>"; ?>
                            </span>
        </div>
        <?php
    }
}
?>