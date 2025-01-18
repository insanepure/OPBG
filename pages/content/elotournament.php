
<style>
    .points{
        z-index: 1;
        position: absolute;
        left: 475px;
        top: 96px;
    }
</style>
<?php
$icon = "img/planets/".$player->GetRace().".png";
$up = 0;
$length = 0;
if($player->GetEloPoints() <= 0)
{
$up = 3310;
$length = 215;
}
else if($player->GetEloPoints() > 0 && $player->GetEloPoints() <= 250)
{
    $up = 3290;
    $length = 270;
}
else if($player->GetEloPoints() > 250 && $player->GetEloPoints() <= 500)
{
    $up = 3260;
    $length = 315;
}
else if($player->GetEloPoints() > 500 && $player->GetEloPoints() <= 750)
{
    $up = 3225;
    $length = 370;
}
else if($player->GetEloPoints() > 750 && $player->GetEloPoints() <= 1000)
{
    $up = 3175;
    $length = 330;
}
else if($player->GetEloPoints() > 1000 && $player->GetEloPoints() <= 1250)
{
    $up = 3120;
    $length = 280;
}
else if($player->GetEloPoints() > 1250 && $player->GetEloPoints() <= 1500)
{
    $up = 3065;
    $length = 220;
}
else if($player->GetEloPoints() > 1500 && $player->GetEloPoints() <= 1750)
{
    $up = 3050;
    $length = 330;
}
else if($player->GetEloPoints() > 1750 && $player->GetEloPoints() <= 2000)
{
    $up = 3030;
    $length = 400;
}
else if($player->GetEloPoints() > 2000 && $player->GetEloPoints() <= 2250)
{
    $up = 3010;
    $length = 450;
}
else if($player->GetEloPoints() > 2250 && $player->GetEloPoints() <= 2500)
{
    $up = 2980;
    $length = 510;
}
else if($player->GetEloPoints() > 2500 && $player->GetEloPoints() <= 2750)
{
    $up = 2940;
    $length = 450;
}
else if($player->GetEloPoints() > 2500 && $player->GetEloPoints() <= 2750)
{
    $up = 2940;
    $length = 450;
}
else if($player->GetEloPoints() > 2750 && $player->GetEloPoints() <= 3000)
{
    $up = 2890;
    $length = 410;
}
else if($player->GetEloPoints() > 3000 && $player->GetEloPoints() <= 3250)
{
    $up = 2850;
    $length = 360;
}
else if($player->GetEloPoints() > 3250 && $player->GetEloPoints() <= 3500)
{
    $up = 2800;
    $length = 320;
}
else if($player->GetEloPoints() > 3500 && $player->GetEloPoints() <= 3750)
{
    $up = 2780;
    $length = 280;
}
else if($player->GetEloPoints() > 3750 && $player->GetEloPoints() <= 4000)
{
    $up = 2750;
    $length = 220;
}
else if($player->GetEloPoints() > 4000 && $player->GetEloPoints() <= 4250)
{
    $up = 2685;
    $length = 175;
}
else if($player->GetEloPoints() > 4250 && $player->GetEloPoints() <= 4500)
{
    $up = 2625;
    $length = 175;
}
else if($player->GetEloPoints() > 4500 && $player->GetEloPoints() <= 4750)
{
    $up = 2625;
    $length = 175;
}
else if($player->GetEloPoints() > 4750 && $player->GetEloPoints() <= 5000)
{
    $up = 2570;
    $length = 175;
}
else if($player->GetEloPoints() > 5000 && $player->GetEloPoints() <= 5250)
{
    $up = 2520;
    $length = 175;
}
else if($player->GetEloPoints() > 5250 && $player->GetEloPoints() <= 5500)
{
    $up = 2520;
    $length = 175;
}
else if($player->GetEloPoints() > 5500 && $player->GetEloPoints() <= 6000)
{
    $up = 2460;
    $length = 190;
}
else if($player->GetEloPoints() > 6000 && $player->GetEloPoints() <= 6250)
{
    $up = 2390;
    $length = 240;
}
else if($player->GetEloPoints() > 6250 && $player->GetEloPoints() <= 6500)
{
    $up = 2340;
    $length = 315;
}
else if($player->GetEloPoints() > 6500 && $player->GetEloPoints() <= 7000)
{
    $up = 2290;
    $length = 390;
}
else if($player->GetEloPoints() > 7000 && $player->GetEloPoints() <= 7250)
{
    $up = 2230;
    $length = 370;
}
else if($player->GetEloPoints() > 7250 && $player->GetEloPoints() <= 7500)
{
    $up = 2170;
    $length = 355;
}
else if($player->GetEloPoints() > 7500 && $player->GetEloPoints() <= 8000)
{
    $up = 2100;
    $length = 355;
}
else if($player->GetEloPoints() > 8000 && $player->GetEloPoints() <= 8250)
{
    $up = 2050;
    $length = 325;
}
else if($player->GetEloPoints() > 8250 && $player->GetEloPoints() <= 8500)
{
    $up = 1985;
    $length = 300;
}
else if($player->GetEloPoints() > 8500 && $player->GetEloPoints() <= 8750)
{
    $up = 1930;
    $length = 280;
}
else if($player->GetEloPoints() > 8750 && $player->GetEloPoints() <= 9000)
{
    $up = 1860;
    $length = 290;
}
else if($player->GetEloPoints() > 9000 && $player->GetEloPoints() <= 9250)
{
    $up = 1800;
    $length = 310;
}
else if($player->GetEloPoints() > 9250 && $player->GetEloPoints() <= 9750)
{
    $up = 1740;
    $length = 330;
}
else if($player->GetEloPoints() > 9750 && $player->GetEloPoints() <= 10000)
{
    $up = 1670;
    $length = 315;
}
else if($player->GetEloPoints() > 10000 && $player->GetEloPoints() <= 10250)
{
    $up = 1600;
    $length = 305;
}
else if($player->GetEloPoints() > 10250 && $player->GetEloPoints() <= 10500)
{
    $up = 1540;
    $length = 295;
}
else if($player->GetEloPoints() > 10500 && $player->GetEloPoints() <= 11000)
{
    $up = 1480;
    $length = 280;
}
else if($player->GetEloPoints() > 11000 && $player->GetEloPoints() <= 11250)
{
    $up = 1430;
    $length = 370;
}
else if($player->GetEloPoints() > 11250 && $player->GetEloPoints() <= 11500)
{
    $up = 1410;
    $length = 435;
}
else if($player->GetEloPoints() > 11500 && $player->GetEloPoints() <= 11750)
{
    $up = 1350;
    $length = 460;
}
else if($player->GetEloPoints() > 11750 && $player->GetEloPoints() <= 12250)
{
    $up = 1300;
    $length = 445;
}
else if($player->GetEloPoints() > 12250 && $player->GetEloPoints() <= 12500)
{
    $up = 1245;
    $length = 370;
}
else if($player->GetEloPoints() > 12500 && $player->GetEloPoints() <= 12750)
{
    $up = 1190;
    $length = 330;
}
else if($player->GetEloPoints() > 12750 && $player->GetEloPoints() <= 13000)
{
    $up = 1130;
    $length = 330;
}
else if($player->GetEloPoints() > 13000 && $player->GetEloPoints() <= 13500)
{
    $up = 1070;
    $length = 370;
}
else if($player->GetEloPoints() > 13500 && $player->GetEloPoints() <= 13750)
{
    $up = 1010;
    $length = 390;
}
else if($player->GetEloPoints() > 13750 && $player->GetEloPoints() <= 14000)
{
    $up = 940;
    $length = 390;
}
else if($player->GetEloPoints() > 14000 && $player->GetEloPoints() <= 14100)
{
    $up = 885;
    $length = 370;
}
else if($player->GetEloPoints() > 14000 && $player->GetEloPoints() <= 14100)
{
    $up = 885;
    $length = 370;
}
else if($player->GetEloPoints() > 14100 && $player->GetEloPoints() <= 14200)
{
    $up = 825;
    $length = 370;
}
else if($player->GetEloPoints() > 14200 && $player->GetEloPoints() <= 14450)
{
    $up = 780;
    $length = 395;
}
else if($player->GetEloPoints() > 14200 && $player->GetEloPoints() <= 14450)
{
    $up = 780;
    $length = 395;
}
else if($player->GetEloPoints() > 14450 && $player->GetEloPoints() <= 14600)
{
    $up = 730;
    $length = 360;
}
else if($player->GetEloPoints() > 14600 && $player->GetEloPoints() <= 14650)
{
    $up = 680;
    $length = 325;
}
else if($player->GetEloPoints() > 14650 && $player->GetEloPoints() <= 14700)
{
    $up = 630;
    $length = 290;
}
else if($player->GetEloPoints() > 14700 && $player->GetEloPoints() <= 14750)
{
    $up = 580;
    $length = 255;
}
else if($player->GetEloPoints() > 14750 && $player->GetEloPoints() <= 14800)
{
    $up = 520;
    $length = 255;
}
else if($player->GetEloPoints() > 14800 && $player->GetEloPoints() <= 14850)
{
    $up = 470;
    $length = 300;
}
else if($player->GetEloPoints() > 14850 && $player->GetEloPoints() <= 14900)
{
    $up = 415;
    $length = 345;
}
else if($player->GetEloPoints() > 14900 && $player->GetEloPoints() <= 14950)
{
    $up = 360;
    $length = 340;
}
else if($player->GetEloPoints() > 14950 && $player->GetEloPoints() <= 14975)
{
    $up = 310;
    $length = 310;
}
else if($player->GetEloPoints() > 14975)
{
    $up = 190;
    $length = 310;
}
?>
<div style="top: <?= $up;?>px; left: <?= $length; ?>px; z-index: 1; position: absolute;">
    <img src="<?= $icon; ?>" />
</div>
<div class="points">
    <?php
    echo "<b>".number_format($player->GetEloPoints(), 0, '', '.')."</b>";
    ?>
</div>