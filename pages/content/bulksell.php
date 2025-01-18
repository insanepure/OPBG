<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
$inventory = $player->GetInventory();
$itemManager = new ItemManager($database);
$title = 'Massenverkauf';

if(!isset($_POST['action']) || !isset($_POST['sellItem']))
{
    $message = 'Fehlende Aktion';
}
else if($_POST['action'] == "bulksell")
{
    ?>
    Möchtest du folgende Items verkaufen?
    <div class="spacer"></div>
    <table style="text-align: center; width: 100%;">
        <tbody>
            <tr>
                <td class="catGradient borderB borderT" colspan="6" style="text-align:center;"><b>Ausgewählte Gegenstände</b></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tr style="text-align: center" class="boxSchatten">
            <td style="width: 80%;">
                Gegenstandsname
            </td>
            <td style="width: 20%;" class="boxSchatten">
                Verkaufspreis
            </td>
        </tr>
        <?php
        $items = $_POST['sellItem'];
        $priceBerry = 0;
        $priceGold = 0;
        $sellItems = array();
        foreach($items as $pItem)
        {
            $item = $inventory->GetItem($pItem);
            if($item->IsSellable() && !$item->IsProtected() && !$item->IsStored() && !$item->IsEquipped() && ($item->GetType() == 3 || $item->GetType() == 4))
            {
                $waehrung = '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
                if($item->IsPremium())
                    $waehrung = '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>';
                ?>
                <tr>
                    <td class="boxSchatten" style="text-align: center">
                        <?= $item->GetName() ?><br/>
                    </td>
                    <td class="boxSchatten" style="text-align: center">
                        <?php
                        echo '<b>' . number_format(round(($item->GetPrice() / 2)),'0', '', '.') . ' ' . $waehrung . '</b>';
                        if($item->IsPremium())
                            $priceGold += round(($item->GetPrice() / 2));
                        else
                            $priceBerry += round(($item->GetPrice() / 2));
                        ?>
                    </td>
                </tr>
                <?php
                $sellItems[] = $item->GetID();
            }
        }
        ?>
        <tr style="text-align: center;" class="boxSchatten">
            <td class="boxSchatten" style="width: 80%">
                <b>
                    --------------------------------------------------------------------------------------------------
                </b>
            </td>
            <td class="boxSchatten" style="width: 20%">
                <b>
                    ------------------------
                </b>
            </td>
        </tr>
        <tr style="text-align: center;" class="boxSchatten">
            <td class="boxSchatten" style="width: 80%">
                <b>
                    Gesamtverkaufspreis (Berry)
                </b>
            </td>
            <td class="boxSchatten" style="width: 20%">
                <b>
                    <?= number_format($priceBerry, '0', '', '.') ?> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>
                </b>
            </td>
        </tr>
        <tr style="text-align: center;" class="boxSchatten">
            <td class="boxSchatten" style="width: 80%">
                <b>
                    Gesamtverkaufspreis (Gold)
                </b>
            </td>
            <td class="boxSchatten" style="width: 20%">
                <b>
                    <?= number_format($priceGold, '0', '', '.') ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>
                </b>
            </td>
        </tr>
    </table>
    <br/>
    <form method="POST" action="?p=ausruestung&a=bulksell">
        <input type="hidden" name="ids" value="<?= implode(";", $sellItems); ?>">
        <input style="width:100px" type="submit" value="Verkaufen">
        </button>
    </form>

<?php
}
else if($_POST['action'] == "bulkrecycle")
{
    ?>
    Möchtest du folgende Items recyclen?
    <div class="spacer"></div>
    <table style="text-align: center; width: 100%;">
        <tbody>
        <tr>
            <td class="catGradient borderB borderT" colspan="6" style="text-align:center;"><b>Ausgewählte Gegenstände</b></td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tr style="text-align: center" class="boxSchatten">
            <td style="width: 80%;">
                Gegenstandsname
            </td>
            <td style="width: 20%;" class="boxSchatten">
                Du erhältst
            </td>
        </tr>
        <?php
        $items = $_POST['sellItem'];
        $sellItems = array();
        foreach($items as $pItem)
        {
            $item = $inventory->GetItem($pItem);
            if(!$item->IsPremium() && $item->GetID() != 11 && !$item->IsStored() && !$item->IsEquipped() && ($item->GetType() == 3 || $item->GetType() == 4))
            {
                ?>
                <tr>
                    <td class="boxSchatten" style="text-align: center">
                        <?= $item->GetName() ?><br/>
                    </td>
                    <td class="boxSchatten" style="text-align: center">
                        <?php
                        if($item->GetUpgrade() < 2)
                            echo '<b>1x Einfacher Splitter</b>';
                        if($item->GetUpgrade() >= 2 && $item->GetUpgrade() < 4)
                            echo '<b>1x Seltener Splitter</b>';
                        if($item->GetUpgrade() >= 4 && $item->GetUpgrade() <= 5)
                            echo '<b>1x Legendärer Splitter,<br>1x Schmied W.,<br>1x Rünstungskristall</b>'
                        ?>
                    </td>
                </tr>
                <?php
                $sellItems[] = $item->GetID();
            }
        }
        ?>
    </table>
    <br/>
    <form method="POST" action="?p=ausruestung&a=bulkrecycle">
        <input type="hidden" name="ids" value="<?= implode(";", $sellItems); ?>">
        <input style="width:100px" type="submit" value="Recyclen">
    </form>
<?php
}