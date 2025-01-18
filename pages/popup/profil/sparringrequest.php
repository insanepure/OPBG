<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
exit();
if(!$player->IsLogged())
{
  echo 'Du bist nicht eingeloggt.';
  exit();
}
if($player->GetSparringRequest() == 0)
{
  exit();
}
$otherPlayer = new Player($database, $player->GetSparringRequest(), $actionManager);
if(!$otherPlayer->IsValid())
{
  $player->DenySparringRequest();
  exit();
}
?>
<div style="height:225px;">
<div class="spacer"></div> 
 
<div class="bplayer1">
    <div class="bplayer1name smallBG">
        <b><?php echo $player->GetName(); ?></b>
        <img class="bplayer1image" src="<?php echo $player->GetImage(); ?>">
    </div>
</div>
<div class="bplayer2">
    <div class="bplayer2name smallBG">
        <b><?php echo $otherPlayer->GetName(); ?></b>
        <img class="bplayer2image" src="<?php echo $otherPlayer->GetImage(); ?>">
    </div>
</div>

<div class="bplayerkampf boxSchatten borderB borderR borderT borderL" style="height:70px">
<?php
			if($player->GetSparringTime() == 1)
			{
				$time = $player->GetSparringTime()." Stunde";
			}
			else
			{
				$time = number_format($player->GetSparringTime(),'0', '', '.')." Stunden";
			}
?>
    <a href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>"><?php echo $otherPlayer->GetName(); ?></a> möchte <?php echo $time ?> trainieren.<br/>
  <table width="100%">
   <tr>
    <td align="center">
  <form method="POST" action="?p=profil&a=acceptsparring">
  <input type="submit" class="ja" value="Annehmen">
  </form>
     </td>
    <td align="center">
  <form method="POST" action="?p=profil&a=declinesparring">
  <input type="submit" class="nein" value="Ablehnen">
  </form>
     </td>
    </tr>
  </table>
</div>

<div class="spacer"></div> 
</div>
<?php
$statsWin = Player::CalculateSparringWin($database, $player, $otherPlayer);
?>
<div class="spacer"></div> 
Gewinn pro Stunde bei gleichbleibender Douriki: <b><?php echo number_format($statsWin,'0', '', '.'); ?></b> Stats.<br>
<div class="spacer2"></div> 