<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
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

if($player->GetArank() < 3)
    exit;
?>
<div style="width:100px; height:100px; position:relative; top:0px; left:0px;">
    <?php if ($item->HasOverlay())
    {
        ?>
        <img src="img/items/<?php echo $item->GetOverlay(); ?>.png?01" alt="<?php echo $item->GetName(); ?>" title="<?php echo $item->GetName(); ?>" style="left:0; top:0; position:absolute; z-index:1;" /><br />
        <?php
    }
    ?>
    <img src="img/items/<?php echo $item->GetImage(); ?>.png?01" alt="<?php echo $item->GetName(); ?>" title="<?php echo $item->GetName(); ?>" style="left:0px; top:0px; position:absolute; z-index:0;" /><br />
</div>
<br />
<?php
echo '<b>' . $item->GetName() . '</b><br/>';
echo $item->GetDescription();
?>
<hr>
<div class="spacer"></div>
<?php
if($item->IsPremium())
{
    $symbol = "GoldSymbol";
    $alt = "Gold";
}
else
{
    $symbol = "BerrySymbol";
    $alt = "Berry";
}
?>
<?php

if ($item->IsPremium())
{
    if($item->GetGebot() != 0 && ($item->GetType() == 2 || $item->GetType() == 3))
    {
        echo 'Gebot: <b><span id="priceg">' . number_format($item->GetGebot(),'0', '', '.') . '</span> <img id="offerSymbol" src="img/offtopic/' . $symbol . '.png" alt="'.$alt.'" title="'.$alt.'" style="position: relative; top: 3px;"></b><br/>';
    }
    echo 'Sofortkauf: <b><span id="price">' . number_format($item->GetPrice(),'0', '', '.') . '</span> <img id="offerSymbol" src="img/offtopic/' . $symbol . '.png" alt="'.$alt.'" title="'.$alt.'" style="position: relative; top: 3px;"></b>';
}
else if ($item->IsSellable() || $item->IsMarktplatz())
{
    if($item->GetGebot() != 0 && ($item->GetType() == 2 || $item->GetType() == 3))
    {
        echo 'Gebot: <b><span id="priceg">' . number_format($item->GetGebot(),'0', '', '.') . '</span> <img id="offerSymbol" src="img/offtopic/' . $symbol . '.png" alt="'.$alt.'" title="'.$alt.'" style="position: relative; top: 3px;"></b><br/>';
    }
    echo 'Sofortkauf: <b><span id="price">' . number_format($item->GetPrice(),'0', '', '.') . '</span> <img id="offerSymbol" src="img/offtopic/' . $symbol . '.png" alt="'.$alt.'" title="'.$alt.'" style="position: relative; top: 3px;"></b>';
}

?>
<div class="spacer"></div>
Verkäufer: <a href="?p=profil&id=<?php echo $item->GetSellerID(); ?>"><?php echo $item->GetSeller(); ?></a>
<hr>
Möchtest du den Gegenstand aus dem Marktplatz entfernen?<br/>
Er wird dem Verkäufer zurück ins Inventar gelegt.
<hr>
<div class="spacer"></div>
<form method="POST" action="?p=market&a=remove">
    <input type="hidden" name="id" value="<?php echo $_GET['item']; ?>">
    <div class="spacer"></div>
    <input style="width:100px" type="submit" value="Entfernen">
</form>
<div class="spacer"></div>