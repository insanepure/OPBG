<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/items/itemmanager.php';
$itemManager = new ItemManager($database);

if (!isset($_GET['item']) || !is_numeric($_GET['item']))
	exit();

$item = $itemManager->GetItem($_GET['item']);
if ($item == null)
	exit();



?>
<div style="width:100px; height:100px; position:relative; top:0px; left:0px;">
	<?php if ($item->HasOverlay())
	{
		?>
		<img src="img/items/<?php echo $item->Getoverlay(); ?>.png" style="left:0px; top:0px; position:absolute; z-index:1;"><br />
		<?php
	}
	?>
	<img width="100" height="100<" src="img/items/<?php echo $item->GetImage(); ?>.png" style="left:0px; top:0px; position:absolute; z-index:0;"><br />
</div>
<br />
<?php
echo '<b>' . $item->GetName() . '</b><br/>';
echo $item->GetDescription();

?>
<hr>
<div class="spacer"></div>
Preis: <b> <span id="price"><?= number_format($item->GetPrice(), '0', '', '.') ?></span> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/></b>
<hr>
<div class="spacer"></div>
<?php
echo $item->DisplayEffect();
if($item->GetID() == 223) // Stat Münze
{
    // Variable declarations
    $actionStats = 0;
    $eventStats = 0;
    $promoStats = 0;
    $levelStats = ($player->GetLevel() - 1) * 25;
    $statFightStats = (floor($player->GetTotalStatsFights() / 10) * 10);
    // --------------------

    // Calculation of finished actions and dungeons
    $getSpecialActionStats = $database->Select('*', 'actions', 'type=15 AND level <= "' . $player->GetLevel() . '"');
    if ($getSpecialActionStats) {
        while ($specialActionStats = $getSpecialActionStats->fetch_assoc()) {
            if ($player->HasSpecialTrainingUsed($specialActionStats['id'])) {
                $actionStats += $specialActionStats['stats'];
            }
        }
    }
    if (!empty($player->GetExtraDungeons())) {
        $dungeons = $player->GetExtraDungeons();
        foreach ($dungeons as &$dungeon) {
            $event = new Event($database, $dungeon[0]);
            if ($event->GetStats() > 0) {
                $eventStats += $event->GetStats() * $dungeon[1];
            }
        }
    }
    // -------------------------------------------

    // Get Statpoints since game-start
    $statsSinceStart = 0;
    $time = $gameStartTime;
    $now = new DateTime("now");
    $hoursSinceOpening = floor(($now->getTimestamp() - $time) / 3600);
    if ($hoursSinceOpening >= 1)
        $statsSinceStart = $hoursSinceOpening * 6;

    $statsSinceStart = $statsSinceStart + 0;
    // -------------------------------

    // Current Statpoints by assigned Stats
    $rlp = $player->GetMaxLP() / 10;
    $rkp = $player->GetMaxKP() / 10;
    $ratk = $player->GetAttack() / 2;
    $rdef = $player->GetDefense();
    $rechnung = $rlp + $rkp + $ratk + $rdef;
    // ----------------------------------

    // Alle Stats vom Player
    $newplayer = $statsSinceStart + $eventStats + $levelStats + $actionStats + $statFightStats;
    $oldplayer = $rechnung;
    $different = $newplayer - ($oldplayer + $player->GetStats());
    if($different < 0) $different = 0;
    $different = abs($different);

    echo "Du besitzt ".number_format($oldplayer, 0, '', '.')." Stats<br />";
    echo "Ein neuer Spieler bekommt ".number_format($newplayer, 0, '', '.')." Stats<br />";
    //echo "Zum start: ".number_format($statsSinceStart, 0, '', '.')."<br/>";
    //echo "Stats durch Spezialtraining ".number_format($actionStats, 0, '', '.')."<br/>";
    //echo "Stats durch Events ".number_format($eventStats, 0, '', '.')."<br/>";
    //echo "Stats durch Story ".number_format($levelStats, 0, '', '.')."<br/>";
    //echo "Stats durch Statsfight ".number_format($statFightStats, 0, '', '.')."<br/>";
    //echo "Stats durch PromoCodes: ".$promoStats."<br />";
    echo 'Verfügbare Punkte: '.number_format($different,0,',','.');
}
// Stat Münze ENDE
if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.') . '<br/>';
?>
<hr>
<div class="spacer"></div>
<?php
    if($item->GetSchatzitems()[1])
    {
        ?>
        <table>
            <tr>
                <td colspan="3" style="text-align: center;">Auswählbare Items:</td>
            </tr>
            <tr>
                <?php
                    $items = array();
                    for($i = 4; $i < count($item->GetSchatzitems()); $i++)
                    {
                        array_push($items, $itemManager->GetItem($item->GetSchatzitems()[$i]));
                    }
                ?>
                <?php
                    for ($i = 0; $i < count($items); $i++)
                    {
                ?>
                <td class="itemButton" style="padding: 10px;">
                    <label for="<?= $items[$i]->GetID();?>">
                        <img src="img/items/<?= $items[$i]->GetImage(); ?>.png" alt="<?php echo $items[$i]->GetName(); ?>" title="<?php echo $items[$i]->GetName(); ?>" class="item" width="75px" height="75px">
                    </label>
                </td>
                <?php
                    if(($i + 1) % 3 == 0)
                    {
                ?>
            </tr>
            <tr>
                <?php
                    }
                    }
                ?>
            </tr>
        </table>
        <?php
    }
?>
<form method="POST" action="?p=shop&a=buy<?php if (isset($_GET['itemname'])) echo '&itemname=' . $_GET['itemname'];
                                          if (isset($_GET['itemcategory'])) echo '&itemcategory=' . $_GET['itemcategory']; ?>">
	<input type="hidden" name="item" value="<?php echo $item->GetID(); ?>">
	Anzahl: <select style="width:100px" class="select" name="amount" onchange="setAmount(this,<?=$item->GetPrice()?>)">
	<?php
	$j = 1;
	$amount = 99;
	while ($j <= $amount)
	{
		?>
		<option value="<?php echo $j; ?>"><?php echo number_format($j, '0', '', '.'); ?></option>
		<?php
		++$j;
	}
	?>
	</select>
	<div class="spacer"></div>
	<input style="width:100px" type="submit" value="Kaufen">
</form>
<div class="spacer"></div>