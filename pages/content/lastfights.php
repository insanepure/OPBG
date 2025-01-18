<?php
if (isset($fight) && $fight->IsEnded())
{
?>
	<script>
		grecaptcha.ready(function() {
			grecaptcha.execute('6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb', {
				action: 'Kampf'
			});
		});
	</script>
	<div class="spacer"></div>
	<?php
	$event = new Event($database, $fight->GetEvent());
	if($fight->GetType() == 8)
        $dir = 'arena';
    else if ($fight->GetType() == 12)
        $dir = 'treasurehunt';
	else if ($fight->GetType() != 3 && $fight->GetType() != 5)
        $dir = 'fight';
	else if ($fight->GetType() == 3)
        $dir = 'npc';
	else if ($fight->GetType() == 5 && $event->IsDungeon())
        $dir = 'boss';
	else if ($fight->GetType() == 5 && !$event->IsDungeon())
        $dir = 'event';
	?>
    <div class="exitFight" onmousedown="location.replace('?p=<?php echo $dir; ?>')">Zurück</div>
	<div class="spacer"></div>
    <?php
    }
?>
<div class="spacer"></div>
<div class="spacer"></div>
<div class="fightBox boxSchatten smallBG">
	<div class="SideMenuKat catGradient borderB">
		<div class="schatten">Verlauf</div>
	</div>
	<img src="img/marketing/infightarena.png">
	<table width="100%">
		<?php echo $fight->GetText(); ?>
	</table>
	<div class="spacer"></div>
</div>
<div class="spacer"></div>
