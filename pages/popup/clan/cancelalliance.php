<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/clan/clan.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
{
    return;
}


$clan = new Clan($database, $player->GetClan());
if (!$clan->GetRankPermission($player->GetClanRank(), 'management'))
{
    return;
}

$otherClan = new Clan($database, $_GET['id']);
if(!$otherClan->IsValid() || !in_array($otherClan->GetID(), $clan->GetAlliances()))
{
    return;
}
?>
<form method="post" action="?p=clanmanage&a=cancelalliance">
    <input type="hidden" name="id" value="<?= $otherClan->GetID() ?>">
    Möchtest du die Allianz mit der Bande <?= $otherClan->GetName() ?> auflösen?
    <div class="spacer"></div>
    <input type="submit" value="Allianz auflösen">
</form>
<div class="spacer"></div>