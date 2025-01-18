<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
if(!$player->IsLogged())
{
	echo 'Du bist nicht eingeloggt.';
	exit();
}
$friendrequests = $player->GetFriendRequests();
if($friendrequests == '')
{
	exit();
}
$friendrequests = explode(";", $friendrequests);
$otherPlayer = new Player($database, $friendrequests[0], $actionManager);
if(!$otherPlayer->IsValid())
{
	$player->FriendRequestRemove($friendrequests[0]);
	exit();
}
?>
<div style="height:225px;">
	<div class="spacer"></div> 
	 
	<div class="bplayer1">
        <div class="bplayer1name smallBG">
            <b><?php echo $player->GetName(); ?></b>
            <img class="bplayer1image" src="<?php echo $player->GetImage(); ?>" />
        </div>
    </div>
	<div class="bplayer2">
        <div class="bplayer2name smallBG">
            <b><?php echo $otherPlayer->GetName(); ?></b>
            <img class="bplayer2image" src="<?php echo $otherPlayer->GetImage(); ?>" />
        </div>
    </div>

	<div class="bplayerkampf boxSchatten borderB borderR borderT borderL" style="height:70px">
        <a href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>"><?php echo $otherPlayer->GetName(); ?></a> hat dir eine Freundschaftsanfrage gesendet.<br/>
		Möchtest du sie annehmen?<br/>
		<table width="100%">
			<tr>
				<td align="center">
					<form method="POST" action="?p=profil&a=acceptfriend">
						<input type="submit" class="ja" value="Annehmen">
					</form>
				</td>
				<td align="center">
					<form method="POST" action="?p=profil&a=declinefriend">
						<input type="submit" class="nein" value="Ablehnen">
					</form>
				</td>
			</tr>
		</table>
	</div>
	<div class="spacer"></div> 
</div>
<div class="spacer2"></div> 