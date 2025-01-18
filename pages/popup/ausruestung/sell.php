<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';
$inventory = $player->GetInventory();

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
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
<hr>
<div class="spacer"></div>
<?php
$waehrung = '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
if($item->IsPremium())
    $waehrung = '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>';
echo 'Preis: <b>' . number_format($item->GetPrice(),'0', '', '.') . ' ' . $waehrung . '</b>';
echo '<br/>';
echo 'Verkaufspreis: <b>' . number_format(round(($item->GetPrice() / 2)),'0', '', '.') . ' ' . $waehrung . '</b>';
?>
<hr>
<div class="spacer"></div>
<?php
echo $item->DisplayEffect();
if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(),'0', '', '.') . '<br/>';
?>
<hr>
<div class="spacer"></div>
<form method="POST" action="?p=ausruestung&a=sell">
    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
    <input type="hidden" name="amount" value="1">
    <div class="spacer"></div>
    <input style="width:100px" type="submit" value="Verkaufen">
</form>
<div class="spacer"></div>