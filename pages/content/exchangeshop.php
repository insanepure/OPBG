
<div class="spacer"></div>
<?php
$zoanamount = $player->GetInventory()->GetItemAmount(52);
$paraamount = $player->GetInventory()->GetItemAmount(53);
$logiaamount = $player->GetInventory()->GetItemAmount(54);
$gurtamount = $player->GetInventory()->GetItemAmount(55);
$schwertamount = $player->GetInventory()->GetItemAmount(56);
$schuheamount = $player->GetInventory()->GetItemAmount(57);
$itemManager = new ItemManager($database);
?>
<table style='width: 100%;'>
    <tr>
            <td class='boxSchatten' align='center'>
                <?php
                    if($zoanamount == 0)
                    {
                        ?>
                            Keine vorhanden
                        <?php
                    }
                    else
                    {
                        ?>
                            Du besitzt <?= number_format($zoanamount,0,'','.'); ?> Zoan Früchte
                        <?php
                    }
                ?>
                <div class='spacer'></div>
                <img src='<?php echo $serverUrl; ?>img/items/fruchtzoan.png' />
                <br />
                Teufelsfrucht Zoan
            </td>
            <td class='boxSchatten' align='center'>
                <?php
                    if($paraamount == 0)
                    {
                        ?>
                        Keine vorhanden
                        <?php
                    }
                    else
                    {
                        ?>
                        Du besitzt <?= number_format($paraamount,0,'','.'); ?> Paramecia Früchte
                        <?php
                    }
                ?>
                <div class='spacer'></div>
                <img src='<?php echo $serverUrl; ?>img/items/parafrucht.png' />
                <br />
                Teufelsfrucht Paramecia
            </td>
            <td class='boxSchatten' align='center'>
                <?php
                if($logiaamount == 0)
                {
                ?>
                Keine vorhanden
                <?php
                    }
                    else
                    {
                        ?>
                Du besitzt <?= number_format($logiaamount,0,'','.'); ?> Logia Früchte
                <?php
                }
                ?>
                <div class='spacer'></div>
                <img src='<?php echo $serverUrl; ?>img/items/logiafrucht.png' />
                <br />
                Teufelsfrucht Logia
            </td>
    </tr>
    <tr>
            <td class='boxSchatten' align='center'>
                <?php
                    if($gurtamount == 0)
                    {
                        ?>
                        Keine vorhanden
                        <?php
                    }
                    else
                    {
                        ?>
                        Du besitzt <?= number_format($gurtamount,0,'','.'); ?> Schwarzgurt
                        <?php
                    }
                ?>
                <div class='spacer'></div>
                <img src='<?php echo $serverUrl; ?>img/items/Fischitem1.png' />
                <br />
                Schwarzgurt
            </td>
            <td class='boxSchatten' align='center'>
                <?php
                    if($schwertamount == 0)
                    {
                        ?>
                        Keine vorhanden
                        <?php
                    }
                    else
                    {
                        ?>
                        Du besitzt <?= number_format($schwertamount,0,'','.'); ?> Schwerter
                        <?php
                    }
                ?>
                <div class='spacer'></div>
                <img src='<?php echo $serverUrl; ?>img/items/needitemschert.png' />
                <br />
                Schwerter
            </td>
            <td class='boxSchatten' align='center'>
                <?php
                    if($schuheamount == 0)
                    {
                        ?>
                        Keine vorhanden
                        <?php
                    }
                    else
                    {
                        ?>
                        Du besitzt <?= number_format($schuheamount,0,'','.'); ?> Schuhe
                        <?php
                    }
                ?>
                <div class='spacer'></div>
                <img src='<?php echo $serverUrl; ?>img/items/needitemschwarzfu.png' />
                <br />
                Schuhe
            </td>
    </tr>
</table>
<br />
<hr>

<div class="catGradient borderT borderB">Items Tauschen</div>
<table>
    <tr class="boxSchatten">
        <td width="10%" align="center">Bild</td>
        <td width="20%" align="center">Name</td>
        <td width="30%" align="center">Beschreibung</td>
        <td width="10%" align="center">Herstellungskosten</td>
        <td width="10%" align="center">Anzahl</td>
        <td width="10%" align="center">Aktion</td>
    </tr>
    <?php
        $id = 52;
        $item = $itemManager->GetItem($id);
        while($item->GetID() <= 57)
        {
            ?>
            <form method="post" action="?p=exchangeshop&a=buy">
                <tr>
                    <td class="boxSchatten" align="center">
                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png" style="width:50px; height:50px;">
                        <input type="hidden" name="item" value="<?= $item->GetID(); ?>">
                    </td>
                    <td class="boxSchatten" align="center"><?php echo $item->GetName(); ?></td>
                    <td class="boxSchatten" align="center"><?php echo $item->GetDescription(); ?></td>
                    <td class="boxSchatten" align="center">
                        <input type="hidden" name="price" id="price<?= $item->GetID() ?>" value="3">
                        <div name="amnt" id="amnt<?= $item->GetID() ?>" style="font-size: 20px">
                            1x
                        </div>
                        <select name="fruit" id="fruit<?= $item->GetID() ?>" class="select" onchange="UpdatePrice(<?= $item->GetID() ?>);">
                            <?php
                            if(!empty($player->GetInventory()->GetPathItems())) {
                                foreach($player->GetInventory()->GetPathItems() as $fruit) {
                                    if($item->GetID() != $fruit[0])
                                        echo "<option value='" . $fruit[0] . "'>" . $fruit[2] . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <div name="berry" id="berry<?= $item->GetID() ?>">
                            500 Berry
                        </div>
                    </td>
                    <td class="boxSchatten" align="center">
                        <select name="amount" id="amount<?= $item->GetID() ?>" style="width: 52.5px;" class="select" onchange="UpdatePrice(<?= $item->GetID() ?>);">
                            <?php
                            for($i=1; $i<=100; $i++)
                            {
                                echo "<option value='".$i."'>".$i."</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td class="boxSchatten" align="center"><input type="submit" value="Herstellen" /></td>
                </tr>
            </form>

            <script>
                UpdatePrice(<?= $item->GetID() ?>);
            </script>
            <?php
            $id++;
            $item = $itemManager->GetItem($id);
        }
    ?>
</table>

