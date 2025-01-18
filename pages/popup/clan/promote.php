<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/clan/clan.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['pid']) || !is_numeric($_GET['pid']))
{
    return;
}

if($_GET['id'] != $player->GetClan())
{
    return;
}

$clan = new Clan($database, $_GET['id']);
$playertopromote = new Player($database, $_GET['pid']);
if ($clan->GetLeader() != $player->GetID())
{
    return;
}
?>
<div class="spacer"></div>
Bist du sicher, dass du <a href="?p=profil&id=<?php echo $playertopromote->GetID();?>"><b><?php echo $playertopromote->GetName(); ?></b></a> zum Kapitän ernennen möchtest?
<div class="spacer"></div>
<div class="spacer"></div>
<a href="?p=clanmanage&a=promote&uid=<?php echo $playertopromote->GetID(); ?>">
    <button class="ja">Ja</button>
</a>
<div class="spacer"></div>