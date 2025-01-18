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

if ($item->GetSellerID() == $player->GetID())
    exit();
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
    if($item->GetGebot() != 0 && ($item->GetType() == 3 || $item->GetType() == 4))
    {
        echo 'Gebot: <b><span id="priceg">' . number_format($item->GetGebot(),'0', '', '.') . '</span> <img id="offerSymbol" src="img/offtopic/' . $symbol . '.png" alt="'.$alt.'" title="'.$alt.'" style="position: relative; top: 3px;"></b><br/>';
    }
    echo 'Sofortkauf: <b><span id="price">' . number_format($item->GetPrice(),'0', '', '.') . '</span> <img id="offerSymbol" src="img/offtopic/' . $symbol . '.png" alt="'.$alt.'" title="'.$alt.'" style="position: relative; top: 3px;"></b>';
}
else if ($item->IsSellable() || $item->IsMarktplatz())
{
    if($item->GetGebot() != 0 && ($item->GetType() == 3 || $item->GetType() == 4))
    {
        echo 'Gebot: <b><span id="priceg">' . number_format($item->GetGebot(),'0', '', '.') . '</span> <img id="offerSymbol" src="img/offtopic/' . $symbol . '.png" alt="'.$alt.'" title="'.$alt.'" style="position: relative; top: 3px;"></b><br/>';
    }
    echo 'Sofortkauf: <b><span id="price">' . number_format($item->GetPrice(),'0', '', '.') . '</span> <img id="offerSymbol" src="img/offtopic/' . $symbol . '.png" alt="'.$alt.'" title="'.$alt.'" style="position: relative; top: 3px;"></b>';
}

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
<?php
if($player->GetID() == $item->GetSellerID() || $item->GetKaeufer() == $player->GetID())
{
    ?>
    Verkäufer: <a href="?p=profil&id=<?php echo $item->GetSellerID(); ?>"><?php echo $item->GetSeller(); ?></a>
    <?php
}
else
{
    ?>
    Verkäufer: Anonym
    <?php
}
?>
<hr>
<div class="spacer"></div>
<table>
    <tr>
        <!-- Gebot -->
        <?php
        if($item->GetGebot() != 0 && ($item->GetType() == 3 || $item->GetType() == 4))
        {
            ?>
            <td style="text-align: center;">
                <form method="POST" action="?p=profil&a=bid">
                    <input type="hidden" name="id" value="<?php echo $_GET['item']; ?>">
                    Gebot: <input style="width: 100px;" type="text" id="gebot" name="gebot" placeholder="<?php echo $item->GetGebot() + ceil($item->GetGebot() / 10) ?>">
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
                    <img id="offerSymbol" src='<?php echo "img/offtopic/" . $symbol . ".png"; ?>' alt="<?php echo $alt; ?>" title="<?php echo $alt; ?>" style='position: relative; top: 3px;'>
                    <div class="spacer"></div>
                    <input style="width:100px" type="submit" value="Bieten" onclick="ClosePopup2();">
                </form>
            </td>
            <td style="width: 100px;"></td>
            <?php
        }
        ?>
        <td style="text-align: center;">
            <!-- Sofortkauf -->
            <form method="POST" action="?p=profil&a=buy">
                <input type="hidden" name="id" value="<?php echo $_GET['item']; ?>">
                <?php
                if($item->GetAmount() > 1)
                {
                    ?>
                    Anzahl: <select style="width:100px" class="select" name="amount" onchange="setAmount(this,<?=$item->GetGebot()?>,<?=$item->GetPrice()?>)">
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
                    <input type="hidden" name="amount" value="1">
                    <?php
                }
                ?>
                <div class="spacer"></div>
                <input style="width:100px" type="submit" value="Sofortkauf">
            </form>
        </td>
    </tr>
</table>
<div class="spacer"></div>