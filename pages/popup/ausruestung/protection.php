<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';
$inventory = $player->GetInventory();

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    exit();

if(!isset($_GET['action']) || $_GET['action'] != "protect" && $_GET['action'] != "unprotect")
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
<b>
    <?= $item->GetName() ?>
</b>
<br/>
<?= $item->GetDescription() ?>

<hr>
<div class="spacer"></div>
<?php
if($_GET['action'] == "protect")
{
    ?>
    Möchtest du diesen Ausrüstungsgegenstand schützen?<br/>
    Ist der Schutz aktiv, lässt sich der Gegenstand nicht verkaufen oder kombinieren.
    <?php
}
else
{
    ?>
    Möchtest du den Schutz dieses Ausrüstungsgegenstands entfernen?<br/>
    Ist der Schutz inaktiv, kann der Gegenstand verkauft und kombiniert werden.
    <?php
}
?>
<div class="spacer"></div>
<hr>
<div class="spacer"></div>
<form method="POST" action="?p=ausruestung&a=protection">
    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
    <input type="hidden" name="action" value="<?= $_GET['action'] ?>">
    <div class="spacer"></div>
    <input style="width:<?php echo ($_GET['action'] == 'protect') ? '100' : '150'; ?>px" type="submit" value="<?php echo ($_GET['action'] == 'protect') ? 'Schützen' : 'Schutz entfernen'; ?>">
</form>
<div class="spacer"></div>