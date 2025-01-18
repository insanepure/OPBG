<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/clan/clan.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['pid']) || !is_numeric($_GET['pid']) || !isset($_GET['a']))
{
    return;
}

if($_GET['id'] != $player->GetClan())
{
    return;
}

$clan = new Clan($database, $_GET['id']);
$playertokick = new Player($database, $_GET['pid']);
if (!$clan->GetRankPermission($player->GetClanRank(), "management"))
{
    return;
}
?>
<div class="spacer"></div>
<?php
if($_GET['a'] == 'kick')
{
    ?>
    Bist du sicher, dass du <a href="?p=profil&id=<?php echo $playertokick->GetID();?>"><b><?php echo $playertokick->GetName(); ?></b></a> aus der Bande werfen möchtest?
    <?php
}
else
{
    ?>
    Bist du sicher, dass du den rauswurf von <a href="?p=profil&id=<?php echo $playertokick->GetID();?>"><b><?php echo $playertokick->GetName(); ?></b></a> rückgängig machen möchtest?
    <?php
}
?>
<div class="spacer"></div>
<div class="spacer"></div>
<a href="?p=clanmanage&a=kick&uid=<?php echo $playertokick->GetID(); ?>">
    <button class="ja">Ja</button>
</a>
<div class="spacer"></div>