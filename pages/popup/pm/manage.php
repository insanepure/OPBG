<?php

include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';

if(!$player->IsLogged())
{
	echo 'Du bist nicht eingeloggt.';
	exit();
}

$pm = '';

if((isset($_GET['a']) && isset($_GET['p'])) && $_GET['a'] == 'deleteall')
{
	if($_GET['p'] == 1)
	{
		$pm = 'pm';
	}
	else if($_GET['p'] == 2)
	{
		$pm = 'pm2';
	}
}

?>

Möchtest du wirklich alle Nachrichten löschen?
<br/>
<br/>
<br/>
<form action="?p=<?php echo $pm; ?>&a=action&action=deleteall" method="post">
	<input type="submit" class="ja" value="Bestätigen">
</form>