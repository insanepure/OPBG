<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
    include_once '../../../classes/header.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/actions/actionmanager.php';

    $actionManager = new ActionManager($database);

    $action = $actionManager->GetAction($player->GetAction());
    if($_GET['t'] == "travel")
    {
        exit();
        //$action = $actionManager->GetAction($player->GetTravelAction());
    }
?>
Möchtest du diese Aktion wirklich abbrechen?
<div class="spacer"></div>
<div class="tooltip" style="position: relative;">
    <img src="img/actions/<?php echo $action->GetImage(); ?>.png" alt="<?php echo $action->GetName(); ?>" title="<?php echo $action->GetName(); ?>" style="width: 100px; height: 100px; border-radius:25%; overflow:hidden;">
    <?php
        if($_GET['t'] == "travel")
        {
            ?>
                <span class="tooltiptext" style="width:180px; top:0; left:-40px;"><?php echo $action->GetDescription(); ?></span>
            <?php
        }
        else
        {
            ?>
                <span class="tooltiptext" style="width:180px; top:-50%; left:-40px;"><?php echo $action->GetDescription(); ?></span>
            <?php
        }
    ?>

</div>
<br />
<?php
    if($_GET['t'] == "travel")
    {
        ?>
            <div id="cID2">Init
                <script>
                    countdown(<?php echo $player->GetTravelActionCountdown(); ?>, 'cID2');
                </script>
            </div>
        <?php
    }
    else
    {
        ?>
            <div id="cID">Init
                <script>
                    countdown(<?php echo $countdown; ?>, 'cID');
                </script>
            </div>
        <?php
    }
?>
<div class="spacer"></div>
<form method="post" action="<?php if(isset($_GET['p'])) echo '?p='.$_GET['p'];  ?>">
	<input type="hidden" name="a" value="cancelAction">
	<?php
	if(isset($_GET['t']) && $_GET['t'] == "travel")
		echo '<input type="hidden" name="t" value="travel">';
	?>
	<input type="submit" value="Abbrechen">
</form>
<div class="spacer"></div>