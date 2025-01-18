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

if ($_GET['id'] == $player->GetID())
{
  echo 'Du kannst dich nicht selbst zur Gruppe einladen.';
  exit();
}

$otherPlayer = new Player($database, $_GET['id'], $actionManager);
if (!$otherPlayer->IsValid())
{
  exit();
}
else if ($otherPlayer->GetGroup() != null)
{
  echo 'Der Spieler ist schon in einer Gruppe.';
  exit();
}
if ($player->GetUserID() == $otherPlayer->GetUserID())
{
  echo 'Deine Charaktere dürfen nicht interagieren.';
  exit();
}
?>
<div class="spacer"></div>
<img width="100px" height="100px" src="<?php echo $otherPlayer->GetImage(); ?>"><br />
<div class="spacer"></div>
Möchtest du <a href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>"><?php echo $otherPlayer->GetName(); ?></a> zur Gruppe einladen?
<div class="spacer"></div>
<form method="POST" action="?p=profil&id=<?php echo $_GET['id']; ?>&a=groupinvite">
  <input type="submit" class="ja" value="Einladen">
</form>
<div class="spacer2"></div>