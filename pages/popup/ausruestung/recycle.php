<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
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
<form method="post" action="?p=ausruestung&a=recycle&id=<?= $item->GetID(); ?>">
    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
    <div class="spacer"></div>
    <input style="width:100px" type="submit" value="Recyceln">
</form>
