<center>
<div class="spacer"></div>
<?php
if($story->GetPlanet() != "") {
	$npc = new NPC($database, $story->GetTalkNPC());
	ShowPlayer($npc);
}
?>
</center>