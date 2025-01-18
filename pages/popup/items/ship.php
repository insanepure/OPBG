<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
$inventory = $player->GetInventory();
$ShipRepairNeeded = array(
	1 => array(1, 1, 1),
	40 => array(7, 4, 4),
	41 => array(3, 2, 2),
	42 => array(10, 6, 4),
	43 => array(10, 8, 8),
	44 => array(20, 6, 1),
	45 => array(30, 6, 7),
	46 => array(35, 6, 1),
	47 => array(40, 8, 8),
	48 => array(50, 10, 9),
	384 => array(1, 1, 1),
	385 => array(10, 6, 4)
);

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['action']) || $_GET['action'] != 0 && $_GET['action'] != 1 && $_GET['action'] != 2 && $_GET['action'] != 3)
	exit();

$item = $inventory->GetItem($_GET['id']);
if ($item == null)
	exit();

?>
<div style="width:100px; height:100px; position:relative; top:0px; left:0px;">
	<?php if ($item->HasOverlay())
	{
	?>
		<img src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="left:0px; top:0px; position:absolute; z-index:1;"><br />
	<?php
	}
	?>
	<img src="img/items/<?php echo $item->GetImage(); ?>.png" style="left:0px; top:0px; position:absolute; z-index:0;"><br />
</div>

<br />

<?php
echo '<b>' . $item->GetName() . '</b><br/>';
echo $item->GetDescription();
?>
<div class="spacer"></div>
<?php if ($item->GetLevel() != 0)
{
?>
	<hr>
	<div class="spacer"></div>
<?php
	echo 'Benötigt Level ' . number_format($item->GetLevel(),'0', '', '.') . '<br/>';
}
?>
<hr>
<div class="spacer"></div>
<?php
if ($_GET['action'] == 0)
{
?>
	<form method="POST" action="?p=inventar&a=ship&do=0">
		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		<div class="spacer"></div>
		<input style="width:100px" type="submit" value="Verlassen">
	</form>
<?php
}
else if ($_GET['action'] == 1)
{
?>
	<form method="POST" action="?p=inventar&a=ship&do=1">
		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		<div class="spacer"></div>
		<input style="width:100px" type="submit" value="Besetzen">
	</form>
<?php
}
else if ($_GET['action'] == 2)
{
?>
	<form method="POST" action="?p=inventar&a=ship&do=2">
		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		<b>Benötigte Items</b><br />
		Holz: <?php echo number_format($ShipRepairNeeded[$item->GetStatsID()][0],'0', '', '.'); ?><br />
		Nägel: <?php echo number_format($ShipRepairNeeded[$item->GetStatsID()][1],'0', '', '.'); ?><br />
		Stoff: <?php echo number_format($ShipRepairNeeded[$item->GetStatsID()][2],'0', '', '.'); ?><br />
		<div class="spacer"></div>
		<input style="width:110px" type="submit" value="Reparieren">
	</form>
<?php
}
else if ($_GET['action'] == 3)
{
?>
	<form method="POST" action="?p=inventar&a=ship&do=3">
		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		<b>Du erhältst beim recyclen zufällig Holz, Nägel oder Stoff.</b><br />
		<?php
		if ($item->GetRepairCount() < 3) echo "<br><b><span style='color:red;'>Achtung!</b><br>Das Boot kann noch repariert werden!</span>";
		?><br />
		<div class="spacer"></div>
		<input style="width:110px" type="submit" value="Recyclen">
	</form>
<?php
}
?>
<div class="spacer"></div>