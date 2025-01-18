<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/clan/clan.php';

if (!isset($_GET['id']))
{
    return;
}

if ($_GET['id'] != $player->GetClan())
{
    return;
}

$clan = new Clan($database, $_GET['id']);
if ($clan->GetLeader() != $player->GetID())
{
    return;
}
?>
Die Änderung des Bandennamen kostet 200 <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>,<br />
diese werden aus der Bandenkasse genommen.
<br />
<br />
<form method="post" action="?p=clanmanage&a=changeclanname">
    Wie soll der neue Bandenname lauten?
    <div class="spacer"></div>
    <input type="text" name="clanname" value="" placeholder="Bandenname">
    <div class="spacer"></div>
    Wie soll der neue Bandentag lauten?
    <div class="spacer"></div>
    <input type="text" name="clantag" value="" placeholder="TAG">
    <div class="spacer"></div>
    <input type="submit" value="Namen ändern">
</form>
<div class="spacer"></div>