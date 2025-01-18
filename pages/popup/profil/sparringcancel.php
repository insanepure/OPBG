<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
$action = 3;
exit();
if(!$player->IsLogged())
{
  echo 'Du bist nicht eingeloggt.';
  exit();
}
if($player->GetSparringCancel() == 0 || $player->GetAction() != $action)
{
  $player->DenySparringCancel();
  echo 'Dein Partner wollte das Sparring abbrechen, es ist jedoch schon vorbei.';
  exit();
}
$otherPlayer = new Player($database, $player->GetSparringPartner(), $actionManager);
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

<div class="bplayerkampf boxSchatten borderB borderR borderT borderL" style="height:60px">
    <a href="?p=profil&id=<?php echo $otherPlayer->GetID(); ?>"><?php echo $otherPlayer->GetName(); ?></a> möchte das Sparring abbrechen.<br/>
  <table width="100%">
   <tr>
    <td align="center">
  <form method="POST" action="?p=profil&a=acceptcancelsparring">
  <input type="submit" class="ja" value="Annehmen">
  </form>
     </td>
    <td align="center">
  <form method="POST" action="?p=profil&a=declinecancelsparring">
  <input type="submit" class="nein" value="Ablehnen">
  </form>
     </td>
    </tr>
  </table>
</div>