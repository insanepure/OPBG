<?php
include_once 'classes/items/itemmanager.php';
include_once 'classes/header.php';
$ItemManager = new ItemManager($database);
$coupons = array(350, 351, 352, 353);
if(isset($_GET['p']) == 'casino' && isset($_GET['activate']) == 'rabatt')
{
    $id = $_POST['rabatt'];
    if(!is_numeric($id) || !in_array($id, $coupons))
    {
        $message = "Die ID ist ungültig";
    }
    else if(!$player->HasItemWithID($id, $id))
    {
        $message = "Du besitzt diesen Coupon nicht";
    }
    else
    {
        $item = $player->GetItemByStatsIDOnly($id);
        $rabatt = 0;
        if($id == 350)
        {
            $rabatt = 25;
        }
        else if($id == 351)
        {
            $rabatt = 50;
        }
        else if($id == 352)
        {
            $rabatt = 75;
        }
        else if($id == 353)
        {
            $rabatt = 100;
        }

        if($existrabatt > 100)
        {
            $message = "Du kannst maximal 100% aktivieren, du hast aktuell ein Rabatt von ".$player->GetRabatt()."% aktiviert";
        }
        else
        {
            $existrabatt = $player->GetRabatt() + $rabatt;
            $result = $database->Update('casino_rabatt="'.$existrabatt.'"', 'accounts', 'id="'.$player->GetID().'"');
            $message = "Ab jetzt sparst du ".$existrabatt."% Berry im Casino, wähle nun deine Tür. Viel Glück";
            $player->RemoveItems($item, 1);
        }
    }
}
?>
<img width='100%' height='300' src='https://www.onepiece-bildupload.de/pictures/user/Sload186757H/casino.png' />
<div class="spacer"></div>
<a href="?p=casinofloorone"><img src="https://www.onepiece-bildupload.de/pictures/user/Sload186757H/TurM1.png" /></a>
<a href="?p=casinofloortwo"><img src="https://www.onepiece-bildupload.de/pictures/user/Sload186757H/TurM2.png" /></a>
<a href="?p=casinofloorthree"><img src="https://www.onepiece-bildupload.de/pictures/user/Sload186757H/TurM4.png" /></a>
<div class="spacer"></div><br /><br />
<a href="?p=fussballwetten"><img src="https://opwx.de/img/marketing/logoruffyfootball.png" /></a>
<hr>
<div class="spacer"></div>
<form method="post" action="?p=casino&activate=rabatt">
    <select class="select" name="rabatt">
        <option value="0">Kein Rabatt</option>
        <?php
        $LoadInventory = $database->Select('*', 'inventory', 'ownerid='.$player->GetID());
        if($LoadInventory)
        {
            while($inventory = $LoadInventory->fetch_assoc())
            {
                if(in_array($inventory['statsid'], $coupons))
                {
                    $item = $ItemManager->GetItem($inventory['statsid']);
                    echo "<option value='".$item->GetID()."'>".$item->GetName()." x".$inventory['amount']."</option>";
                }
            }
        }
        ?>
    </select>
    <br ><br />
    <button class="button">Aktivieren</button>
</form>
<div class="spacer"></div>
<b>Aktueller Rabatt</b> <?= $player->GetRabatt(); ?>%
<br />
<center><h2>Rangliste</h2></center>
<br />

<style>
    .url:hover {
        color: #dddddd;
    }
</style>
<table>
    <tr>
        <td style="width: 25%;">Rang</td>
        <td style="width: 25%;">Name</td>
        <td style="width: 25%;">Bisher Ausgegeben</td>
        <td style="width: 25%;">Suchtfaktor</td>
    </tr>
    <?php
    $suchtcheck = $database->Select('id, name, casino_out_of_money', 'accounts', 'arank < 2 AND banned != 1 AND casino_out_of_money > 0', 99999999, 'casino_out_of_money', 'DESC');
    if($suchtcheck)
    {
        $rang = 1;
        while($sucht = $suchtcheck->fetch_assoc())
        {
            $suchtfaktor = "";
            if($sucht['casino_out_of_money'] >= 10000000)
            {
                $suchtfaktor = "Knossi";
            }
            else if($sucht['casino_out_of_money'] >= 9000000)
            {
                $suchtfaktor = "Al Bundy";
            }
            else if($sucht['casino_out_of_money'] >= 8000000)
            {
                $suchtfaktor = "80%";
            }
            else if($sucht['casino_out_of_money'] >= 7000000)
            {
                $suchtfaktor = "70%";
            }
            else if($sucht['casino_out_of_money'] >= 6000000)
            {
                $suchtfaktor = "60%";
            }
            else if($sucht['casino_out_of_money'] >= 5000000)
            {
                $suchtfaktor = "50%";
            }
            else if($sucht['casino_out_of_money'] >= 4000000)
            {
                $suchtfaktor = "40%";
            }
            else if($sucht['casino_out_of_money'] >= 3000000)
            {
                $suchtfaktor = "30%";
            }
            else if($sucht['casino_out_of_money'] >= 2000000)
            {
                $suchtfaktor = "20%";
            }
            else if($sucht['casino_out_of_money'] >= 1000000)
            {
                $suchtfaktor = "10%";
            }
            else if($sucht['casino_out_of_money'] < 1000000)
            {
                $suchtfaktor = "Casino Noob";
            }
            $count = $suchtcheck->num_rows;

            ?>
            <tr>
                <td style="border: 1px solid white;"><?= $rang; ?></td>
                <?php
                $color = $sucht['name'] != $player->GetName() ? "#ffffff" : "#ff0000";
                ?>
                <td style="border: 1px solid white;"><a class="url" href="?p=profil&id=<?= $sucht['id']; ?>"><?= $sucht['name']; ?></a></td>
                <td style="border: 1px solid white;"><?= number_format($sucht['casino_out_of_money'], 0, '', '.'); ?></td>
                <td style="border: 1px solid white;"><?= $suchtfaktor; ?></td>
            </tr>
            <?php
            $rang++;
        }
    }
    ?>
</table>