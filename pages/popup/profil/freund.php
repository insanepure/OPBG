<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
if(!isset($_GET['id']) || !is_numeric($_GET['id']))
{
	exit();
}
if(!isset($_GET['userid']) || !is_numeric($_GET['userid']))
{
	exit();
}
if(!$player->IsLogged())
{
	echo 'Du bist nicht eingeloggt.';
	exit();
}

$otherPlayer = new Player($database, $_GET['id'], $actionManager);
if(!$otherPlayer->IsValid())
{
	exit();
}

if($_GET['userid'] == $player->GetUserID())
{
	echo 'Du kannst dich nicht selbst befreunden.';
	exit();
}

$friends = $player->IsFriend($_GET['id']);
?>

<?php
if($friends)
{
    ?>Möchtest du <a href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>"><?php echo $otherPlayer->GetName(); ?></a> wirklich als Freund entfernen?<br/><?php
}
else
{
	?>Möchtest du <a href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>"><?php echo $otherPlayer->GetName(); ?></a> wirklich als Freund hinzufügen?<br/><?php
}
?>
<br/>

<form action="?p=profil&a=friend&id=<?php echo $_GET['id']; ?>" method="post">
<?php
if($friends)
{
	?><input type="submit" class="nein" value="Freund entfernen"><?php
}
else
{
	?><input type="submit" class="ja" value="Befreunden"><?php
}
?>
</form>