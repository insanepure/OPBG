<?php
if(!$player->IsValid())
    return;


if($player->GetArank() < 2)
{
    return;
}

?>

    <img width='100%' height='300' src='img/marketing/Elo.png' />
    <div style='position:absolute; top: 0; width: 100%; height: 300px; overflow: hidden'>
        <div class='catGradient' style='line-height: 1.3; font-weight: bold; width: 300px; padding: 10px; position: absolute; top: 50px; right: -91px; transform: rotate(45deg); box-shadow: 0 4px 10px black;'>
            <span>Elopunkte: <?php echo number_format($player->GetEloPoints(), '0', '', '.'); ?></span>
        </div>
        <?php
        $sum = 5 - $player->GetDailyEloFights();
        $color = 'white';
        if($sum <= 0)
        {
            $sum = 0;
            $color = 'red';
        }

        ?>
        <div class='catGradient' style='line-height: 1.3; font-weight: bold; width: 300px; padding: 10px; position: absolute; top: 50px; right: 435px; transform: rotate(-45deg); box-shadow: 0 4px 10px black;'>
            <span style="color: <?php echo $color; ?>;">Tägliche Elokämpfe: <?php echo number_format($sum, '0', '', '.'); ?></span>
        </div>
        <div class='schatten' style='position: absolute; font-size: 200px; font-weight: bold; top: 40px; left: 80px; width: 200px; text-align: center; -webkit-text-stroke: 2px black;'>
            <?php echo number_format($eloarena->GetFighterCount(), '0', '', '.'); ?>
        </div>
        <div style="position: absolute; top: 267px; left: 77px;">
            <?php
            if($eloarena->IsFighterIn($player->GetID()))
            {

                ?>
                <form action="?p=eloarena&a=search" id="search" method="post">
                    <div class="exitFight" style="position: absolute; top: -30px; left: 38px;" onmousedown="search.submit();">Kampf suchen</div>
                </form>
                <div class="exitFight borderB" onmousedown="location.replace('?p=eloarena&a=leave')">Elo-Warteraum verlassen</div>
                <?php
            }
            else
            {
                ?>
                <div class="exitFight borderB" onmousedown="location.replace('?p=eloarena&a=join')">Elo-Warteraum betreten</div>
                <?php
            }
            ?>
        </div>
    </div>
<?php
if($eloarena->GetFighterCount() > 0)
{
    ?>
    <table width="100%" cellspacing="0" border="0">
        <tr>
            <td colspan=6 class="catGradient borderT borderB" align="center">
                <b>
                        <span style="color: white;">
                            <div class="schatten">Teilnehmer</div>
                        </span>
                </b>
            </td>
        </tr>
        <tr class="boxSchatten">
            <td width="10%" align="center"><b>Bild</b></td>
            <td width="20%" align="center"><b>Name</b></td>
            <td width="30%" align="center"><b>Douriki</b></td>
            <td width="30%" align="center"><b>Status</b></td>
        </tr>
        <?php
        $afp = $database->Select('*', 'eloarena');
        while($af = $afp->fetch_assoc())
        {
            $up = $database->Select('*', 'accounts', 'id="'.$af['fighter'].'"');
            $u = $up->fetch_assoc();
            $enemy = new Player($database, $u['id']);
            $att = $u['attack'] / 2;
            $lpp = $u['mlp'] / 10;
            $kpp = $u['mkp'] / 10;
            $def = $u['defense'] / 1;

            $danger = round(($lpp + $kpp + $att + $def) / 4);
            if($u['fakeki'] != 0)
                $danger = $u['fakeki'];

            if($enemy->GetClan() != $player->GetClan() && !$enemy->IsFriend($player->GetID()) && $player->GetArank() < 2)
            {
                $image = "img/imagefail.png";
                $danger = "Zensiert";
                $name = "Zensiert";
            }
            ?>
            <tr>
                <?php
                if($danger == "Zensiert")
                {
                    ?>
                    <td class="boxSchatten" align="center">
                        <img class="boxSchatten borderT borderR borderL borderB" src="<?php echo $image; ?>" style="width:50px;height:50px;">
                    </td>
                    <td class="boxSchatten" align="center"><b><?php echo $name; ?></b></td>
                    <td class="boxSchatten" align="center"><b><?php echo $danger; ?></b></td>
                    <?php
                }
                else
                {
                    ?>
                    <td class="boxSchatten" align="center">
                        <img class="boxSchatten borderT borderR borderL borderB" src="<?php echo $u['charimage']; ?>" style="width:50px;height:50px;">
                    </td>
                    <td class="boxSchatten" align="center"><b><?php echo "<a href='?p=profil&id=".$u['id']."'>".$u['name']."</a>"; ?></b></td>
                    <td class="boxSchatten" align="center"><b><?php echo number_format($danger, 0 , '', '.'); ?></b></td>
                    <?php
                }
                if($u['fight'] == 0)
                {
                    echo "<td class='boxSchatten' align='center'><b>Bereit</td >";
                }
                else
                {
                    echo "<td class='boxSchatten' align='center'><a href='?p=infight&fight=".$u['fight']."'><b><span style='color: red;'>Im Kampf!</span></b></a></td >";
                }
                ?>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}
