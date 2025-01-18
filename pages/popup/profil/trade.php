<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
{
  exit();
}

if(true)
{
    header('Location: ?p=news');
    exit();
}
include_once '../../../classes/items/itemmanager.php';
$itemManager = new ItemManager($database);
$inventory = $player->GetInventory();

if ($_GET['id'] == $player->GetID())
{
  echo 'Du kannst dir nichts selbst schenken.';
  exit();
}
?>
<form method="POST" action="?p=profil&id=<?php echo $_GET['id']; ?>&a=trade">
  <select style="height:30px;" name="item" class="select">
    <option value="0">Berry (<?php echo number_format($player->GetBerry(),0, '', '.');?>)</option>
    <option value="1">Gold (<?php echo number_format($player->GetGold(),0, '', '.');?>)</option>
    <?php
    $i = 0;
    $item = $inventory->GetItem($i);
    $ids = array();
    while (isset($item))
    {
      if ($item->GetSlot() == 0 && $item->GetStatsID() == $item->GetVisualID())
      {
        $itemData = $itemManager->GetItem($item->GetStatsID());
        if (isset($itemData) && !in_array($itemData->GetID(), $ids) && ($itemData->IsMarktplatz() || $itemData->IsSellable()))
        {
          $ids[] = $itemData->GetID();
          echo '<option value="' . $itemData->GetID() . '">' . $itemData->GetName() . ' (' . number_format($item->GetAmount(),0, '', '.') . ')</option>';
        }
      }
      ++$i;
      $item = $inventory->GetItem($i);
    }
    ?>
  </select>
  <input type="text" name="amount" placeholder="0">
  <input type="submit" class="ja" value="Schenken">
</form>