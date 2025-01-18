<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';

$stats = 0;
$statsResetted = $player->HasStatsResetted();
$ResettedAmount = $player->GetResettedStatsAmount();

if((2000 - $player->GetAssignedStats()) < $player->GetStats() && ($statsResetted == 0 || $statsResetted == 1 && $ResettedAmount == 0) && $player->GetArank() < 2)
{
    $stats = (2000 - $player->GetAssignedStats());
}
else if($statsResetted == 1 && $ResettedAmount > 0 && $player->GetStats() >= $ResettedAmount)
{
    $stats = $ResettedAmount;
}
else if($player->GetArank() >= 2)
{
    $stats = $player->GetStats();
}
else if((2000 - $player->GetAssignedStats()) != 0)
{
    $stats = $player->GetStats();
}
/*if (((500 - $player->GetAssignedStats()) < $player->GetStats()) && ($statsResetted == 0 || $statsResetted == 1 && $ResettedAmount == 0) && $player->GetArank() < 2) {
    $stats = (500 - $player->GetAssignedStats());
} else if ($statsResetted == 1 && $ResettedAmount > 0 && $player->GetArank() >= 2) {
    $stats = $ResettedAmount;
}*/
?>
<style>
    .popup-container > .spacer
    {
        height: 0;
    }
    .popup-container {
        width: 640px;
        height: 426px;
    }
</style>
<div style="width: 100%; height: 400px; background-image: url('/img/offtopic/verteilen.png');">
    <div style="width: 364px; position: absolute; left: 254px; opacity: .8;">
        <h2>Statspunkte: <span id="totalStats"><?php echo number_format($stats, '0', '', '.'); ?></span></h2>
    </div>
    <div style="width: 364px; position: absolute; left: 247px; top: 126px; opacity: .8;">
        <form action="?p=profil&a=stats" method="post">
            <table style="width:100%" cellspacing="0" border="0">
                <tr>
                    <td style="width:20%; text-align:center;">
                        <b>LP:</b>
                    </td>
                    <td style="width:20%; text-align:center;">
                        <Button type="button" onclick="statsDecrease('lp');">
                            < </Button>
                    </td>
                    <td style="width:20%; text-align:center;">
                        <input type="number" id="lp" name="lp" placeholder="<?= $player->GetMaxLP() ?>" min="0" style="width:100%;" onkeyup="statsChanged('lp');" onChange="statsChanged('lp');">
                    </td>
                    <td style="width:20%; text-align:center;">
                        <Button type="button" onclick="statsIncrease('lp');">
                            >
                        </Button>
                    </td>
                    <td style="width:20%; text-align:center;">
                        <button type="button" onclick="maximize('lp', <?php echo $stats; ?>)">MAX</button>
                    </td>
                </tr>
                <tr>
                    <td width="25%" align="center">
                        <b>AD:</b>
                    </td>
                    <td width="25%" align="center">
                        <Button type="button" onclick="statsDecrease('kp');">
                            <
                        </Button>
                    </td>
                    <td width="30%" align="center">
                        <input type="number" id="kp" name="kp" placeholder="<?= $player->GetMaxKP() ?>" min="0" style="width:100%;" onkeyup="statsChanged('kp');" onChange="statsChanged('kp');">
                    </td>
                    <td width="25%" align="center">
                        <Button type="button" onclick="statsIncrease('kp');">
                            >
                        </Button>
                    </td>
                    <td width="25%" align="center">
                        <button type="button" onclick="maximize('kp', <?php echo $stats; ?>)">MAX</button>
                    </td>
                </tr>
                <tr>
                    <td width="25%" align="center">
                        <b>Angriff:</b>
                    </td>
                    <td width="25%" align="center">
                        <Button type="button" onclick="statsDecrease('attack');">
                            < </Button>
                    </td>
                    <td width="30%" align="center">
                        <input type="number" id="attack" name="attack" placeholder="<?= $player->GetAttack() ?>" min="0" style="width:100%;" onkeyup="statsChanged('attack');" onChange="statsChanged('attack');">
                    </td>
                    <td width="25%" align="center">
                        <Button type="button" onclick="statsIncrease('attack');">
                            >
                        </Button>
                    </td>
                    <td width="25%" align="center">
                        <button type="button" onclick="maximize('attack', <?php echo $stats; ?>)">MAX</button>
                    </td>
                </tr>
                <tr>
                    <td width="25%" align="center">
                        <b>Abwehr:</b>
                    </td>
                    <td width="25%" align="center">
                        <Button type="button" onclick="statsDecrease('defense');">
                            < </Button>
                    </td>
                    <td width="30%" align="center">
                        <input type="number" id="defense" name="defense" placeholder="<?= $player->GetDefense() ?>" min="0" style="width:100%;" onkeyup="statsChanged('defense');" onChange="statsChanged('defense');">
                    </td>
                    <td width="25%" align="center">
                        <Button type="button" onclick="statsIncrease('defense');">
                            >
                        </Button>
                    </td>
                    <td width="25%" align="center">
                        <button type="button" onclick="maximize('defense', <?php echo $stats; ?>)">MAX</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <div style="float: left; padding-left: 75px;">
                            <input type="submit" value="Benutzen">
                        </div>
                        <div style="float: right; padding-right: 25px;">
                            <input formaction="?p=profil&a=statspopup" formmethod="post" type="submit" value="Popup schließen">
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="smallBG boxSchatten" style="position: absolute; left: 10%; top: 330px; width: 80%; padding: 10px; opacity: .8;">
        <?php
        if($stats == 0)
        {
            ?>
            <span style="color: red; font-weight: bold;">Du hast heute bereits deine 2.000 Statspunkte verteilt, um weitere Statspunkte verteilen zu können, nutze ein Stat Geist oder warte bis morgen!</span>
            <?php
        }
        else
        {
            ?>
            <span style="font-weight: bold;">Täglich können bis zu 2.000 Statspunkte verteilt werden.</span>
            <?php
        }
        ?>
    </div>
</div>
