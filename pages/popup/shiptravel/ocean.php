<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/planet/planet.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
$itemManager = new ItemManager($database);

$inventory = $player->GetInventory();
$planet = null;
$marineShipIds = array(42, 384, 385);
if (isset($_GET['id']))
{
    $planet = new Planet($database, $_GET['id']);
    if (!$planet->IsValid())
    {
        $planet = null;
    }
}
if ($planet == null || !$planet->CanSee($player->GetStory()) || !$planet->CanSeeSide($player->GetSideStory()))
{
    ?>
    Dieses Meer existiert nicht.
    <?php
}
else
{
    ?>
    <div class="spacer"></div>
    <div class="reise2">
        <div class="reisebild">
            <img src="img/planets/<?php echo $planet->GetImage(); ?>.png?001" width="100%" height="100%">
        </div>
        <div class="reisebeschreibung" style="color: black;">
            <?php
                echo $planet->GetDescription();
            ?>
        </div>
        <div class="reisedauer">
            <?php
            if ($planet->GetName() == $player->GetPlanet())
            {
                ?> Du befindest dich hier.
                <?php
            }
            else if ($planet->IsTravelable() && $player->GetPlanet() != 2)
            {
                if ($inventory->HasShipEquipped($inventory->GetShip()))
                {
                    if($inventory->GetShipWear() < $inventory->GetShipMaxWear($inventory->GetShipItemID()))
                    {
                        ?>
                        <div style="color:black;">
                            <?php
                                $dauer = 24;
                                if($player->GetRace() == 'Marine' && in_array($player->GetInventory()->GetShipItemID(), $marineShipIds))
                                    $dauer = 12;
                            if($planet->GetID() == 1)
                                echo 'Die Reisedauer zum <br />' . $planet->GetName() . '<br />dauert '. $dauer .' Stunden <br />';
                            else
                                echo 'Die Reisedauer zur <br />' . $planet->GetName() . '<br />dauert '. $dauer .' Stunden <br />';
                            ?>
                        </div>
                        <table>
                            <tr>
                                <td>
                                    <form method="POST" action="?p=shiptravel&a=travel">
                                        <input type="hidden" name="destination" value="<?php echo $planet->GetID(); ?>">
                                        <input type="submit" value="Reisen">
                                    </form>
                                </td>
                            </tr>
                        </table>
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
                ?>
                <div class="spacer"></div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}
?>