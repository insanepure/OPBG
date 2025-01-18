<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
{
    exit();
}
if (!isset($_GET['userid']) || !is_numeric($_GET['userid']))
{
    exit();
}
if (!$player->IsLogged())
{
    echo 'Du bist nicht eingeloggt.';
    exit();
}

$otherPlayer = new Player($database, $_GET['id'], $actionManager);
if (!$otherPlayer->IsValid())
{
    exit();
}

if ($_GET['userid'] == $player->GetUserID())
{
    echo 'Du kannst dich nicht selbst befreien.';
    exit();
}

if ($otherPlayer->GetPlanet() != 2)
{
    echo "Dieser Spieler befindet sich nicht in Impel Down!";
    exit();
}

if ($player->GetPlanet() == 2)
{
    echo "Du befindest dich selbst in Impel Down und kannst keine anderen Spieler befreien.";
    exit();
}

if ($player->GetFight() != 0)
{
    echo "Dies kannst du nicht während eines Kampfes tun.";
    exit();
}

if ($otherPlayer->GetFight() != 0)
{
    echo "Dieser Spieler befindet sich aktuell in einem Kampf.";
    exit();
}
$keyid = 0;
if($otherPlayer->GetPlace() == 10)
    $keyid = 388;
if($otherPlayer->GetPlace() == 11)
    $keyid = 389;
if($otherPlayer->GetPlace() == 12)
    $keyid = 390;
if($otherPlayer->GetPlace() == 13)
    $keyid = 391;
if($otherPlayer->GetPlace() == 14)
    $keyid = 392;
if($otherPlayer->GetPlace() == 15)
    $keyid = 393;

$key = false;
if($player->HasItemWithID($keyid,$keyid))
    $key = $player->GetItemByStatsIDOnly($keyid);
?>

Möchtest du <a href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>"><?php echo $otherPlayer->GetName(); ?></a> aus Impel Down befreien?<br />
Du musst die Kaution in Höhe von <?php echo number_format($otherPlayer->GetPvP(),'0', '', '.'); ?> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/> zahlen.

<?php
if($key)
{
    ?>
    <div class="spacer"></div>
    Alternativ kannst du einen <?php echo $key->GetName(); ?> nutzen, um den Spieler zu befreien.
    <?php
}
if ($player->GetClan() == $otherPlayer->GetClan() && $player->GetClan() != 0)
{
    $clan = new Clan($database, $player->GetClan());
    if ($clan->PaysBounty() && $clan->GetBerry() >= $otherPlayer->GetPvP())
    {
        echo "<div class='spacer'></div>Info: Deine Bande wird die Kaution übernehmen.";
    }
}
?>
<div class="spacer"></div>
<table>
    <tr>
        <?php

        if($key)
        {
            ?>
            <td>
                <form action="?p=profil&a=impeldownfree&id=<?php echo $_GET['id']; ?>&with=key" method="post">
                    <button class="ja">
                        <img src="img/items/<?php echo $key->GetImage(); ?>.png" style="width: 15px; height: 15px; position: relative; top: 2px;"> | Schlüssel
                    </button>
                </form>
            </td>
            <?php
        }
        ?>
        <td>
            <form action="?p=profil&a=impeldownfree&id=<?php echo $_GET['id']; ?>&with=berry" method="post">
                <input type="submit" class="ja" value="Befreien">
            </form>
        </td>
    </tr>
</table>