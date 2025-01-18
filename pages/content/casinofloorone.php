<img width='100%' height='300' src='https://www.onepiece-bildupload.de/pictures/user/Sload186757H/casino.png' /><br /><br />
<?php
include_once 'classes/items/item.php';
include_once 'classes/items/itemmanager.php';
$titel = 'Casino Ebene 1';
$itemManager = new ItemManager($database);
if($player->IsLogged() && $player->GetArank() <= 3)
{
    $berrylose = $player->GetCasinoOutOfMoney();
    $casinoitemeins = array("52", "53", "54", "55", "56", "57", "58", "59", "31", "83", "84", "104", "315", "102");
    $casinoitemzwei = array("52", "53", "54", "55", "56", "57", "58", "59", "31", "83", "84", "104", "315", "102");
    $casinoitemdrei = array("52", "53", "54", "55", "56", "57", "58", "59", "31", "83", "84", "104", "315", "102");
    $casinoitemvier = array("52", "53", "54", "55", "56", "57", "58", "59", "31", "83", "84", "104", "315", "102");
    $casinoitemfünf = array("52", "53", "54", "55", "56", "57", "58", "59", "31", "83", "84", "104", "315", "102");
    shuffle($casinoitemeins);
    shuffle($casinoitemzwei);
    shuffle($casinoitemdrei);
    shuffle($casinoitemvier);
    shuffle($casinoitemfünf);
    if($casinoitemeins[0] == $casinoitemzwei[0])
    {
        $casinoitemdrei_dropchance = rand(1, 30);
        if($casinoitemdrei_dropchance == 5)
        {
            $casinoitemdrei[0] = $casinoitemeins[0];
        }
    }
    else if($casinoitemeins[0] == $casinoitemdrei[0])
    {
        $casinoitemzwei_dropchance = rand(1, 30);
        if($casinoitemzwei_dropchance == 10)
        {
            $casinoitemzwei[0] = $casinoitemeins[0];
        }
    }
    else if($casinoitemzwei[0] == $casinoitemdrei[0])
    {
        $casinoitemeins_dropchance = rand(1, 30);
        if($casinoitemeins_dropchance = 15)
        {
            $casinoitemeins[0] = $casinoitemzwei[0];
        }
    }
    $itemo = $itemManager->GetItem($casinoitemeins[0]);
    $itemt = $itemManager->GetItem($casinoitemzwei[0]);
    $itemth = $itemManager->GetItem($casinoitemdrei[0]);
    $itemfo = $itemManager->GetItem($casinoitemvier[0]);
    $itemfi = $itemManager->GetItem($casinoitemfünf[0]);
    echo "<br /><br /><br /><br /><br /><br /><br /><br />";
    echo "<img src='/img/items/".$itemfo->GetImage().".png' /> <img src='/img/items/".$itemo->GetImage().".png' /> <img src='/img/items/".$itemfi->GetImage().".png' /><br />";
    echo "<img src='/img/items/".$itemo->GetImage().".png' /> <img src='/img/items/".$itemt->GetImage().".png' /> <img src='/img/items/".$itemth->GetImage().".png' /><br />";
    echo "<img src='/img/items/".$itemt->GetImage().".png' /> <img src='/img/items/".$itemfi->GetImage().".png' /> <img src='/img/items/".$itemfo->GetImage().".png' /><br />";
    echo "<form method='POST' action='?p=casino&dice=roll'><button value='Würfeln'>Würfeln</button></form><br /><br />";
    ?>
    <style>
        .casino_bild{
            position:absolute;
            left: 140px;
            top: 335px;
            border: 0px;
        }

        .casino_loseins{
            position:absolute;
            left: 385px;
            top: 840px;
            border: 0px;
        }
        .casino_loszwei{
            position:absolute;
            left: 285px;
            top: 840px;
            border: 0px;
        }
        .casino_losdrei{
            position:absolute;
            left: 185px;
            top: 840px;
            border: 0px;
        }
    </style>
    <?php
    echo "<div class='casino_bild'>";
    echo "<a href='?p=casinofloorone&dice=roll'><img height='640' width='440' src='".$serverUrl."img/marketing/Casino3.png' /></a><br /><br />";
    echo "</div>";

    echo "<br /><br /><br /><br /><br /><br /><br /><br /><br />";
    $cost = 1000;
    if(date("w") == 4)
        $cost = 500;
    if(isset($_GET['p']) == 'casino' && $_GET['dice'] == 'roll')
    {
        if(!$player->IsValid())
            return;
        $bereylose = 0;
        if($player->GetRabatt() != 0 && $player->GetRabatt() < 100)
        {
            $rabattcheck = floor(($cost * $player->GetRabatt()) / 100);
            $price = $player->GetBerry() - ($cost - $rabattcheck);
            $prices = $cost - $rabattcheck;
        }
        else if($player->GetRabatt() == 100)
        {
            $price = $player->GetBerry() - 0;
            $prices = 0;
        }
        else
        {
            $price = $player->GetBerry() - $cost;
            $prices = $cost;
        }
        if($price < 0)
        {
            $message = "Du besitzt nicht genügend Berry um den Hebel zu betätigen";
        }
        else if($casinoitemeins[0] != $casinoitemzwei[0] || $casinoitemzwei[0] != $casinoitemdrei[0] || $casinoitemeins[0] != $casinoitemdrei[0])
        {
            $berrylose = $player->GetCasinoOutOfMoney() + $prices;
            echo "<div class='casino_loseins'>";
            echo "<img width='100' height='100' src='/img/marketing/pngaaa.com1712621.png'</div>";
            echo "</div>";
            echo "<div class='casino_loszwei'>";
            echo "<img width='100' height='100' src='/img/marketing/pngaaa.com1712621.png'</div>";
            echo "</div>";
            echo "<div class='casino_losdrei'>";
            echo "<img width='100' height='100' src='/img/marketing/pngaaa.com1712621.png'</div>";
            echo "</div>";
            $player->SetBerry($price);
            if($player->GetRabatt() > 0)
            {
                $player->SetRabatt(0);
            }
        }
        else
        {
            $berrylose = $player->GetCasinoOutOfMoney() + $prices;
            $amount = rand(1, 10);
            if($amount != 2)
            {
                $amount = 1;
            }
            else
            {
                $amount = 2;
            }
            $winner = $player->GetID();
            $item_win = $casinoitemeins[0];
            $item = $itemManager->GetItem(intval($casinoitemeins[0]));
            $player->AddItems($item, $item, $amount);
            $player->SetBerry($price);
            if($player->GetRabatt() > 0)
            {
                $player->SetRabatt(0);
            }
            $player->SetRabattLoseCount(0);
            echo "<div class='casino_loseins'>";
            echo "<img width='100' height='100' src='https://www.onepiece-bildupload.de/pictures/user/Sload186757H/pngwing.com%20(1)_1.png'</div>";
            echo "</div>";
            echo "<div class='casino_loszwei'>";
            echo "<img width='100' height='100' src='https://www.onepiece-bildupload.de/pictures/user/Sload186757H/pngwing.com%20(1)_1.png'</div>";
            echo "</div>";
            echo "<div class='casino_losdrei'>";
            echo "<img width='100' height='100' src='https://www.onepiece-bildupload.de/pictures/user/Sload186757H/pngwing.com%20(1)_1.png'</div>";
            echo "</div>";
            $message = "Glückwunsch du hast ".$amount."x ".$itemth->GetName()." gewonnen!";

        }
        $player->SetCasinoOutOfMoney($berrylose);
    }
    ?>
    <fieldset>
        <legend><b>Casino Informationen</b></legend>
        <table><tr><td>
                    <b><font color='0066FF'>1. </font></b></td><td>Sobald du auf den Hebel klickst, kostet es dich <?php echo (date("w") == 4) ? ("<s>1.000</s> ") . ("nur heute: ". number_format($cost, 0, ".", ".")) : ("1.000") ?><img width='15' height='15' src='https://opwx.de/img/offtopic/BerrySymbol.png' /></td></tr><tr><td>
                    <b><font color='0066FF'>2. </font></b></td><td>Das Prinzip ist rein zufällig und basiert auf reinem Glückspiel</td></tr><tr><td>
                    <b><font color='0066FF'>3.</font></b></td><td>Du musst 3 richtige Bilder haben um das Item in dem Bild zu gewinnen!</td></tr><tr><td>
                    <b><font color='0066FF'>4.</font></b></td><td>Es zählen nur die 3 Bilder in der Mitte!</td></tr><tr><td>
                    <b><font color='0066FF'>5.</font></b></td><td>Sind 2 Bilder bereits gleich so besteht für das 3 Bild eine erhöhte Chance</td></tr><tr><td>
                    <b><font color='0066FF'>6.</font></b></td><td> Erscheint dieses Symbol <img width='15' height='15' src='/img/marketing/pngaaa.com1712621.png'/> bedeutet dies das man nix gewonnen hat</td></tr><tr><td>
                    <b><font color='0066FF'>7.</font></b></td><td> Erscheint dieses Symbol <img width='15' height='15' src='https://www.onepiece-bildupload.de/pictures/user/Sload186757H/pngwing.com%20(1)_1.png'/> bedeutet dies das man das Item in der mittleren Reihe gewonnen hat, die Anzahl steht im Popup</td></tr><tr><td>
                    <b><font color='0066FF'>8.</font></b></td><td>Bevor der Rabatt Coupon wirkt musst du ihn im <a target='_blank' href='?p=casino'>Casino</a> aktivieren, man kann nur maximal 100% aktivieren!</td></tr><tr><td>
                    <b><font color='0066FF'>9.</font></b></td><td>Bereits getätigte Ausgaben: <?= number_format($player->GetCasinoOutOfMoney(),0,'', '.') ?><img width='15' height='15' src='https://opwx.de/img/offtopic/BerrySymbol.png' /></td></tr><tr><td>
                    <b><font color='0066FF'>10.</font></b></td><td>Aktiver Rabatt: <?= $player->GetRabatt() ?>%</td></tr><tr><td>
                    <b><font color='0066FF'>11.</font></b></td><td>Die letzte Ziehung hat dich <?= number_format($prices,0,'', '.') ?><img width='15' height='15' src='https://opwx.de/img/offtopic/BerrySymbol.png' /> gekostet!</td></tr><tr><td>
                </td></tr>
        </table>
    </fieldset>
    <?php
}
?>
<br />
<a style="align-content: center" href="?p=casino"><img height="150" width="150" src="https://www.onepiece-bildupload.de/pictures/user/Sload186757H/back-arrow.png"</a>
