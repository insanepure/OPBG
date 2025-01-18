
<div class="spacer"></div>
<?php

include_once 'classes/header.php';

?>
<table style='width: 100%;'>
    <tr>
        <?php
        if($player->GetInventory()->GetEinfacheSplitter() == 0)
        {
            ?>
            <td class='boxSchatten' align='center'>
                Keine vorhanden
                <div class='spacer'></div>
                <img src='img/items//SplitterNormal.png' />
                <br />
                Einfacher Splitter
            </td>
            <?php
        }
        else
        {
            ?>
            <td class='boxSchatten' align='center'>
                Du besitzt <?= $player->GetInventory()->GetEinfacheSplitter() ?> Splitter
                <div class='spacer'></div>
                <img src='img/items//SplitterNormal.png' />
                <br />
                Einfacher Splitter
            </td>
            <?php
        }

        if($player->GetInventory()->GetSelteneSplitter() == 0)
        {
            ?>
            <td class='boxSchatten' align='center'>
                Keine vorhanden
                <div class='spacer'></div>
                <img src='img/items//SplitterSelten.png' />
                <br />
                Seltener Splitter
            </td>

            <?php
        }
        else
        {
            ?>
            <td class='boxSchatten' align='center'>
                Du besitzt <?= $player->GetInventory()->GetSelteneSplitter() ?> Splitter
                <div class='spacer'></div>
                <img src='img/items//SplitterSelten.png' />
                <br />
                Seltener Splitter
            </td>
            <?php
        }

        if($player->GetInventory()->GetLegendaereSplitter() == 0)
        {
            ?>
            <td class='boxSchatten' align='center'>
                Keine vorhanden
                <div class='spacer'></div>
                <img src='img/items//SplitterLegende.png' />
                <br />
                Legendärer Splitter
            </td>
            <?php

        }
        else
        {
            ?>
            <td class='boxSchatten' align='center'>
                Du besitzt <?= $player->GetInventory()->GetLegendaereSplitter() ?> Splitter
                <div class='spacer'></div>
                <img src='img/items//SplitterLegende.png' />
                <br />
                Legendärer Splitter
            </td>
            <?php
        }
        ?>
    </tr>
</table>
<br />
<br />
<div class="catGradient borderT borderB">Schiff/Boote Reparieren</div>
<table>
    <tr>
        <td width="10%" align="center">Bild</td>
        <td width="10%" align="center">Name</td>
        <td width="10%" align="center">Beschreibung</td>
        <td width="10%" align="center">Reparaturkosten</td>
        <td width="10%" align="center">Aktion</td>
    </tr>
    <tr>
        <form method="post" action="?p=werkstatt&a=repairboat">
            <td class="boxSchatten" align="center"><img class="boxSchatten borderT borderR borderL borderB" src="img/items/boot.png" /></td>
            <td class="boxSchatten" align="center">
                Boot
                <input type="hidden" name="id" value="1">
            </td>
            <td class="boxSchatten" align="center">Ein Boot brauch man um zwischen den Orten reisen zu können.</td>
            <td class="boxSchatten" align="center">10 Einfache Splitter</td>
            <td class="boxSchatten" align="center"><input type="submit" value="Reparieren"/></td>
        </form>
    </tr>
    <tr>
        <form method="post" action="?p=werkstatt&a=repairboat">
            <td class="boxSchatten" align="center"><img class="boxSchatten borderT borderR borderL borderB" src="img/items/merrygo.png" /></td>
            <td class="boxSchatten" align="center">
                Going Merry
                <input type="hidden" name="id" value="40">
            </td>
            <td class="boxSchatten" align="center">Ein großes Schiff, welches sehr Robust ist.</td>
            <td class="boxSchatten" align="center">10 Seltene Splitter</td>
            <td class="boxSchatten" align="center"><input type="submit" value="Reparieren"/></td>
        </form>
    </tr>

</table>
<br />
<hr >
<div class="catGradient borderT borderB">Items herstellen mit einfachen Splittern</div>
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
    $splitterItems = $database->Select('*', 'items', 'einfachesplitter != 0');
    if($splitterItems)
    {
        while($items = $splitterItems->fetch_assoc())
        {
            ?>
            <form method="post" action="?p=werkstatt&a=buy">
                <tr>
                    <td class="boxSchatten" align="center"><img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $items['image']; ?>.png" style="width:50px;height:50px;">
                        <input type="hidden" name="item" value="<?php echo $items['id']; ?>">
                    </td>
                    <td class="boxSchatten" align="center"><?php echo $items['name']; ?></td>
                    <td class="boxSchatten" align="center"><?php echo $items['description']; ?></td>
                    <td class="boxSchatten" align="center"><?php echo $items['einfachesplitter']; ?>
                    <td class="boxSchatten" align="center">
                        <select name="amount" style="width: 52.5px;" class="select">
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
            <?php
        }
    }
    ?>
</table>

<br />
<hr>

<div class="catGradient borderT borderB">Items herstellen mit seltenen Splittern</div>
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
    $splitterItems = $database->Select('*', 'items', 'seltenesplitter > 0');
    if($splitterItems)
    {
        while($items = $splitterItems->fetch_assoc())
        {
            ?>
            <form method="post" action="?p=werkstatt&a=buy">
                <tr>
                    <td class="boxSchatten" align="center"><img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $items['image']; ?>.png" style="width:50px;height:50px;">
                        <input type="hidden" name="item" value="<?= $items['id']; ?>">
                    </td>
                    <td class="boxSchatten" align="center"><?php echo $items['name']; ?></td>
                    <td class="boxSchatten" align="center"><?php echo $items['description']; ?></td>
                    <td class="boxSchatten" align="center"><?php echo $items['seltenesplitter']; ?>
                    <td class="boxSchatten" align="center">
                        <select name="amount" style="width: 52.5px;" class="select">
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
            <?php
        }
    }
    ?>
</table>

<br />
<hr>
<div class="catGradient borderT borderB">Items herstellen mit Legendären Splittern</div>
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
    $splitterItems = $database->Select('*', 'items', 'legendaeresplitter > 0');
    if($splitterItems)
    {
        while($items = $splitterItems->fetch_assoc())
        {
            ?>
            <form method="post" action="?p=werkstatt&a=buy">
                <tr>
                    <td class="boxSchatten" align="center"><img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $items['image']; ?>.png" style="width:50px;height:50px;">
                        <input type="hidden" name="item" value="<?= $items['id']; ?>">
                    </td>
                    <td class="boxSchatten" align="center"><?php echo $items['name']; ?></td>
                    <td class="boxSchatten" align="center"><?php echo $items['description']; ?></td>
                    <td class="boxSchatten" align="center"><?php echo $items['legendaeresplitter']; ?>
                    <td class="boxSchatten" align="center">
                        <select name="amount" style="width: 52.5px;" class="select">
                            <?php
                            for($i=1; $i<=100; $i++)
                            {
                                echo "<option value='".$i."'>".$i."</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td class="boxSchatten" align="center"><input type="submit" value="Herstellen"></td>
                </tr>
            </form>
            <?php
        }
    }
    ?>
</table>
