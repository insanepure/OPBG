<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/items/itemmanager.php';
include_once '../../../classes/bbcode/bbcode.php';
$itemManager = new ItemManager($database);

$inventory = $player->GetInventory();
$item = null;
if(isset($_GET['id']) && is_numeric($_GET['id']))
{
    $item = $inventory->GetItem($_GET['id']);
    if($item == null)
    {
        exit();
    }
}
else
{
    exit();
}

if(!isset($_GET['action']) || $_GET['action'] != 0 && $_GET['action'] != 1)
    exit;

if($item->IsEquipped())
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
echo $item->DisplayEffect();
if ($item->GetLevel() != 0) echo 'Benötigt Level ' . number_format($item->GetLevel(),'0', '', '.') . '<br/>';
?>
<hr>
<div class="spacer"></div>
<form method="POST" action="?p=kleiderschrank&a=store">
    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
    <input type="hidden" name="action" value="<?= $_GET['action'] ?>">
    <div class="spacer"></div>
    <input style="width:100px" type="submit" value="<?php echo $_GET['action'] == 0 ? 'Einlagern' : 'Auslagern'; ?>">
</form>
<div class="spacer"></div>