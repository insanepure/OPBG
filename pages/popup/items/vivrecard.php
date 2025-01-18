<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
{
    exit;
}



$UseNPCs = $database->Select('*', 'npcs', 'vivrecard=0', 999999);
if($UseNPCs)
{
    ?>
    <form method="post" action="?p=inventar&a=vivrecard">
        <select class="select" name="npc">
    <?php
    while($npcs = $UseNPCs->fetch_assoc())
    {
        $npc = new NPC($database, $npcs['id']);

            ?>


                <option value="<?= $npc->GetID(); ?>"><?= $npc->GetName(); ?></option>


<?php
    }
    ?>
        </select>
        <br /><br />
        <button value="Absenden">Absenden</button>
    </form>
        <?php
}
?>
<script>
    $('.select').select2();
</script>
<style>
    .select2-results {
        color: #000000;
    }
</style>
