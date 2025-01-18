<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/items/itemmanager.php';
$inventory = $player->GetInventory();
$itemManager = new ItemManager($database);

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    exit();

$item = $inventory->GetItem($_GET['id']);
if ($item == null)
    exit();

?>
<div style="width:100px; height:100px; position:relative; top:0px; left:0px;">
    <?php
    if ($item->HasOverlay())
    {
        echo '<img src="img/items/' . $item->GetOverlay() . '.png" style="left:0px; top:0px; position:absolute; z-index:1;" /><br />';
    }
    echo '<img src="img/items/' . $item->GetImage() . '.png" style="left:0px; top:0px; position:absolute; z-index:0;" /><br />';
    ?>
</div>
<br />
<?php
echo '<b>' . $item->GetName() . '</b><br/>';
echo $item->GetDescription();
?>
<hr>
<div class="spacer"></div>
<form method="POST" action="?p=profil&a=usereset">
    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
    <div class="spacer"></div>
    Wähle, welchen Pfad du zurücksetzen möchtest:<br /><br />
    <select style="width:200px" class="select" name="pfad">
        <?php
        if ($player->GetPfad(1) != 'None')
            echo "<option value='" . $player->GetPfad(1) . "'>Pfad " . $player->GetPfad(1) . "</option>";

        if ($player->GetPfad(2) != 'None')
            echo "<option value='" . $player->GetPfad(2) . "'>Pfad " . $player->GetPfad(2) . "</option>";

        echo "<option value='Alle'>Alle Pfade</option>";

        ?>
    </select>
    <div class="spacer"></div>
    <hr>
    <div class="spacer"></div>
    <input style="width:125px" type="submit" class="ja" value="Zurücksetzen">
</form>
<div class="spacer"></div>