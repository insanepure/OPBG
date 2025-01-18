<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';


$inventory = $player->GetInventory();
$item = null;
if (isset($_GET['id']) && is_numeric($_GET['id']))
{
  $item = $inventory->GetItem($_GET['id']);
  if ($item == null)
  {
    exit();
  }
}
else
{
  exit();
}


if ($item->IsEquipped())
{
  exit();
}
$itemManager = new ItemManager($database);
$statsitem = $itemManager->GetItem($item->GetStatsID());
?>
<form method="POST" action="?p=ausruestung&a=resetvisual">
  <div class="spacer"></div>
  <table width="100%" cellspacing="0">
    <tr>
      <td width="2%"></td>
      <td align="center" width="38%" class="catGradient borderT borderR borderL"><b>Original Visual</b></td>
      <td width="10%"></td>
      <td align="center" width="38%" class="catGradient borderT borderR borderL"><b>Aktuelles Visual</b></td>
      <td width="2%"></td>
    </tr>
    <tr>
      <td></td>
      <td align="center" class="borderL borderR">
      </td>
      <td align="center">
      </td>
      <td align="center" class="borderL borderR">
      </td>
    </tr>
    <tr>
      <td></td>
      <td align="center" class="borderL borderR">
        <?php
        $level = $item->GetUpgrade() > 0 ? ' Level ' . ($item->GetUpgrade() + 1) : '';
        echo $statsitem->GetName() . $level;
        ?>
        <div class="spacer"></div>
        <div style="width:80px; height:80px; position:relative; top:0px; left:0px;">
          <?php if ($statsitem->HasOverlay())
          {
          ?>
            <img src="img/items/<?php echo $statsitem->GetOverlay(); ?>.png" style="width:80px; height:80px; left:0px; top:0px; position:absolute; z-index:1;"><br />
          <?php
          }
          ?>
          <img src="img/items/<?php echo $statsitem->GetImage(); ?>.png" style="width:80px; height:80px; left:0px; top:0px; position:absolute; z-index:0;"><br />
        </div>
        <div class="spacer"></div>
      </td>
      <td align="center">
        <<== <br />
        <<== <br />
        <<== <br />
        <<== <br />
        <<== <br />
        <<== <br />
        <<== <br />
        <<== <br />
      </td>
      <td align="center" class="borderL borderR">
        <?php echo $item->GetName(); ?>
        <div class="spacer"></div>
        <div style="width:80px; height:80px; position:relative; top:0px; left:0px;">
          <?php if ($item->HasOverlay())
          {
          ?>
            <img src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="width:80px; height:80px; left:0px; top:0px; position:absolute; z-index:1;"><br />
          <?php
          }
          ?>
          <img src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:80px; height:80px; left:0px; top:0px; position:absolute; z-index:0;"><br />
        </div>
      </td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td class="borderL borderR borderB"></td>
      <td></td>
      <td class="borderL borderR borderB"></td>
      <td></td>
    </tr>
  </table>
  <div class="spacer"></div>
  <input type="hidden" name="visualitem" value="<?php echo $_GET['id']; ?>">
  <input type="submit" value="Zurücksetzen">
</form>
<div class="spacer3"></div>
Es kostet dich 2500 <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/> das Visuelle Item wieder vom Stat Item zu trennen!<br />
Info: Beide Items bleiben erhalten!<br />