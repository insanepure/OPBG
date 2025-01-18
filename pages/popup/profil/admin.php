<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
{
    exit();
}
if (!$player->IsLogged())
{
    echo 'Du bist nicht eingeloggt.';
    exit();
}

if ($player->GetARank() < 2)
{
    echo 'Du bist nicht berechtigt.';
    exit();
}

$otherPlayer = new Player($database, $_GET['id'], $actionManager);
if (!$otherPlayer->IsValid())
{
    echo "Das ist kein gültiger Spieler!";
    exit();
}


if (isset($_GET['a']) && $_GET['a'] == "copy")
{
?>
    <br />

    <form action="?p=profil&a=copy&id=<?php echo $otherPlayer->GetID(); ?>" method="post">
        Was möchtest du kopieren?<br />
        <br />
        <select name="copy" class="select">
            <option value="stats">Werte (LP, AD, Angriff, Abwehr)</option>
            <option value="equip">Nur Ausrüstung</option>
            <option value="all">Alles</option>
        </select>
        <div class="spacer"></div>
        <input type="submit" value="Kopieren">
    </form>
<?php
}
?>