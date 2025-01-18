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
  echo 'Du kannst dich nicht selbst blockieren.';
  exit();
}

$blocked = $player->IsBlocked($_GET['id']);
?>

<?php
if($blocked)
{
    ?>Willst du <a href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>"><?php echo $otherPlayer->GetName(); ?></a> wirklich entblockieren?<br/><?php
}
else
{
  ?>Willst du <a href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>"><?php echo $otherPlayer->GetName(); ?></a> wirklich blockieren?<br/> Er wird dir dann mit keinem Charakter mehr schreiben können.<br/><?php
}
?>
<br/>

<form action="?p=profil&a=block&id=<?php echo $_GET['id']; ?>" method="post">
<?php
if($blocked)
{
  ?><input type="submit" class="ja" value="Entblockieren"><?php
}
else
{
  ?><input type="submit" class="nein" value="Blockieren"><?php
}
?>
</form>