<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/items/itemmanager.php';
include_once '../../../classes/market/market.php';
$itemManager = new ItemManager($database);
$market = new Market($database);

if (!isset($_GET['item']) || !is_numeric($_GET['item']))
  exit();

$item = $market->GetItemByID($_GET['item']);
if ($item == null)
  exit();

if ($item->GetSellerID() != $player->GetID())
  exit();

?>
<div style="width:100px; height:100px; position:relative; top:0px; left:0px;">
  <?php if ($item->HasOverlay())
  {
  ?>
    <img src="img/items/<?php echo $item->GetOverlay(); ?>.png?01" alt="<?php echo $item->GetName(); ?>" title="<?php echo $item->GetName(); ?>" style="left:0px; top:0px; position:absolute; z-index:1;"><br />
  <?php
  }
  ?>
  <img src="img/items/<?php echo $item->GetImage(); ?>.png?01" alt="<?php echo $item->GetName(); ?>" title="<?php echo $item->GetName(); ?>" style="left:0px; top:0px; position:absolute; z-index:0;"><br />
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
if ($item->GetLevel() != 0)
{
    if($item->GetLevel() > $player->GetLevel())
        echo '<span style="color:red;">';
    echo 'Benötigt Level ' . number_format($item->GetLevel(),'0', '', '.') . '<br/>';
    if($item->GetLevel() > $player->GetLevel())
        echo '</span>';
}
?>
<hr>
<div class="spacer"></div>
Verkäufer: <a href="?p=profil&id=<?php echo $item->GetSellerID(); ?>"><?php echo $item->GetSeller(); ?></a>
<hr>
<div class="spacer"></div>
<?php
    if($item->GetBieter() != 0)
    {
        $bieter = new Player($database, $item->GetBieter());
        ?>
            Gebot:
            <?php
                echo number_format($item->GetGebot(), 0, '', '.') . ' ';
                if($item->IsPremium())
                    echo "<img src='img/offtopic/GoldSymbol.png' title='Gold' alt='Gold' style='position: relative; top: 3px;'>";
                else
                    echo "<img src='img/offtopic/BerrySymbol.png' title='Berry' alt='Berry' style='position: relative; top: 3px;'>";
                echo ' von <a href="?p=profil&id='.$bieter->GetID().'">' . $bieter->GetName() .'</a>';
            ?>
        <?php
    }
    else
    {
        ?>
            <form method="POST" action="?p=market&a=retake<?php if (isset($_GET['itemname'])) echo '&itemname=' . $_GET['itemname'];
                if (isset($_GET['itemcategory'])) echo '&itemcategory=' . $_GET['itemcategory']; ?>">
                <input type="hidden" name="id" value="<?php echo $_GET['item']; ?>">
                <?php
                    if($item->GetAmount() > 1)
                    {
                        ?>
                            Anzahl:
                            <select style="width:100px" class="select" name="amount">
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
                        <?php
                    }
                    else
                    {
                        ?>
                            <input type="hidden" name="amount" value="1"/>
                        <?php
                    }
                ?>
                <div class="spacer"></div>
                <input style="width:100px" type="submit" value="Nehmen">
            </form>
        <?php
    }
?>
<div class="spacer"></div>