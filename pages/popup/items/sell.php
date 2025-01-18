<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
$inventory = $player->GetInventory();

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  exit();

$item = $inventory->GetItem($_GET['id']);
if ($item == null)
  exit();
$berry = '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
$gold = '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>';
?>
<div style="width:100px; height:100px; position:relative; top:0; left:0;">
  <?php if ($item->HasOverlay())
  {
  ?>
    <img width="100" height="100" src="img/items/<?php echo $item->GetOverlay(); ?>.png" style="left:0; top:0; position:absolute; z-index:1;"><br />
  <?php
  }
  ?>
  <img width="100" height="100" src="img/items/<?php echo $item->GetImage(); ?>.png" style="left:0; top:0; position:absolute; z-index:0;"><br />
</div>
<br />
<?php
echo '<b>' . $item->GetName() . '</b><br/>';
echo $item->GetDescription();
?>
<hr>
<div class="spacer"></div>
<?php
if ($item->IsPremium())
{
  echo 'Preis: <b>' . number_format($item->GetPrice(),'0', '', '.') . ' ' . $gold .'</b>';
}
else if ($item->IsSellable())
{
  echo 'Preis: <b>' . number_format($item->GetPrice(),'0', '', '.') . ' ' . $berry. '</b>';
}
echo '<br/>';

if ($item->IsPremium())
{
  if ($item->GetStatsID() >= 49 && $item->GetStatsID() <= 51)
    echo 'Verkaufspreis: <b>' . number_format(round((floor($item->GetPrice() / 5))),'0', '', '.') . ' ' . $gold .'</b>';
  else if ($inventory->GetShip() == $item->GetID() && $item->GetWear() == $inventory->GetShipMaxWear($inventory->GetShip()))
    echo 'Verkaufspreis: <b>' . number_format(round((floor($item->GetPrice() / 5))),'0', '', '.') . ' ' . $gold .'</b>';
  else if ($inventory->GetShip() == $item->GetID() && $item->GetWear() > $inventory->GetShipMaxWear($inventory->GetShip()))
    echo 'Verkaufspreis: <b>' . number_format(round((floor($item->GetPrice() / 2))),'0', '', '.') . ' ' . $gold .'</b>';
  else
    echo 'Verkaufspreis: <b>' . number_format(round((floor($item->GetPrice() / 2))),'0', '', '.') . ' ' . $gold .'</b>';
}
else
{
  echo 'Verkaufspreis: <b>' . number_format(round(($item->GetPrice() / 2)),'0', '', '.') . ' ' . $berry. '</b>';
}
?>
<hr>
<div class="spacer"></div>
<?php
    echo $item->DisplayEffect();
    if($item->GetID() == 223) // Stat Münze
    {
        $statssincestart = 0;
        $time = mktime(17, 0, 0, 5, 12, 2023);
        $now = new DateTime("now");
        $hourssinceopening = floor(($now->getTimestamp() - $time) / 3600);
        if ($hourssinceopening >= 1) $statssincestart = $hourssinceopening * 5;

        $rlp = $player->GetMaxLP() / 10;

        $rkp = $player->GetMaxKP() / 10;

        $ratk = $player->GetAttack() / 2;
        //$ratk = $player->GetAttack() / 1; TODO: Reupload

        $rdef = $player->GetDefense() / 1;

        $rechnung = $rlp + $rkp + $ratk + $rdef;
        $stats = $player->GetStats();
        $becomestats = $statssincestart - $rechnung - $stats;
        if($becomestats < 0)
        {
            $becomestats = 0;
        }
        echo 'Verfügbare Punkte: '.number_format($becomestats,0,',','.');
    }
    if ($item->GetLevel() != 0)
        echo 'Benötigt Level ' . number_format($item->GetLevel(),'0', '', '.') . '<br/>';
?>
<hr>
<div class="spacer"></div>
<form method="POST" action="?p=inventar&a=sell">
  <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
  Anzahl: <select style="width:100px" class="select" name="amount">
    <?php
    $j = 1;
    $amount = $item->GetAmount();
    while ($j <= $amount)
    {
    ?>
      <option value="<?php echo $j; ?>"><?php echo number_format($j,'0', '', '.'); ?></option>
    <?php
      ++$j;
    }
    ?>
  </select>
  <button type="button" onclick="amount.selectedIndex = <?php echo ceil($amount - 1); ?>">MAX</button>
  <div class="spacer"></div>
  <input style="width:100px" type="submit" value="Verkaufen">
</form>
<div class="spacer"></div>