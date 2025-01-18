<?php
/*if($player->GetArank() < 3) {
    echo "Wartungsarbeiten - Bitte später erneut vorbeischauen :)";
    return;
}*/
?>
<img width='100%' height='300' src='img/marketing/Arena.png' />
<div style='position:absolute; top: 0; width: 100%; height: 300px; overflow: hidden'>
    <div class='catGradient' style='line-height: 1.3; font-weight: bold; width: 300px; padding: 10px; position: absolute; top: 50px; right: -91px; transform: rotate(45deg); box-shadow: 0 4px 10px black;'>
        <span>Kolosseumspunkte: <?php echo number_format($player->GetArenaPoints(), '0', '', '.'); ?></span>
    </div>
    <?php
            $sum = $player->GetDailyArenaPoints();
            $color = 'white';
            if($sum <= 0)
            {
                $sum = 0;
                $color = 'red';
            }

            ?>
                <div class='catGradient' style='line-height: 1.3; font-weight: bold; width: 300px; padding: 10px; position: absolute; top: 50px; right: 435px; transform: rotate(-45deg); box-shadow: 0 4px 10px black;'>
                    <span style="color: <?php echo $color; ?>;">Tägliche Kolopunkte: <?php echo number_format($sum, '0', '', '.'); ?></span>
                </div>
    <div class='schatten' style='position: absolute; font-size: 200px; font-weight: bold; top: 40px; left: 66px; width: 200px; text-align: center; -webkit-text-stroke: 2px black;'>
        <?php echo number_format($arena->GetFighterCount(), '0', '', '.'); ?>
    </div>
    <div style="position: absolute; top: 267px; left: 77px;">
    <?php
        if($arena->IsFighterIn($player->GetID()))
        {

            ?>
                <form action="?p=arena&a=search" id="search" method="post">
                    <div class="exitFight" style="position: absolute; top: -30px; left: 28px;" onmousedown="search.submit();">Kampf suchen</div>
                </form>
                <div class="exitFight borderB" onmousedown="location.replace('?p=arena&a=leave')">Kolosseum verlassen</div>
            <?php
            if($player->GetArank() >= 3)
            {
                ?>
                <form action="?p=arena&a=setpvp" method="post">
                    <div class="exitFight borderB" style="position: absolute; top: -20px; left: 470px;" onmousedown="location.replace('?p=arena&a=setpvp')">
                        <?php
                            if(!$arena->HasPvpEnabled($player->GetID()))
                                echo 'PvP einschalten';
                            else
                                echo 'PvP ausschalten';
                        ?>
                    </div>
                </form>
                    <?php
            }
        }
        else
        {
            ?>
                <div class="exitFight borderB" onmousedown="location.replace('?p=arena&a=join')">Kolosseum beitreten</div>
            <?php
        }
        ?>
    </div>
</div>
<?php
    if($arena->GetFighterCount() > 0)
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
                $afp = $database->Select('*', 'arenafighter');
                while($af = $afp->fetch_assoc())
                {
                    $up = $database->Select('*', 'accounts', 'id="'.$af['fighter'].'"');
                    $u = $up->fetch_assoc();
                    //$att = $u['attack'] / 1; TODO: Reupload
                    $att = $u['attack'] / 2;
                    $lpp = $u['mlp'] / 10;
                    $kpp = $u['mkp'] / 10;
                    $def = $u['defense'] / 1;

                    $danger = round(($lpp + $kpp + $att + $def) / 4);
                    if($u['fakeki'] != 0)
                        $danger = $u['fakeki'];
                    ?>
                    <tr>
                        <td class="boxSchatten" align="center">
                            <img class="boxSchatten borderT borderR borderL borderB" src="<?php echo $u['charimage']; ?>" style="width:50px;height:50px;">
                        </td>
                        <td class="boxSchatten" align="center"><b><?php echo "<a href='?p=profil&id=".$u['id']."'>".$u['name']."</a>"; ?></b></td>
                        <td class="boxSchatten" align="center"><b><?php echo number_format($danger, 0 , '', '.'); ?></b></td>
                        <?php
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
?>
<table width="100%" cellspacing="0" border="0">
  <tr>
    <td colspan=6 class="catGradient borderT borderB" align="center">
      <b>
        <span style="color: white;">
          <div class="schatten">Shop</div>
        </span>
      </b>
    </td>
  </tr>
  <tr class="boxSchatten">
    <td width="10%" align="center"><b> Bild </b></td>
    <td width="20%" align="center"><b> Name </b></td>
    <td width="30%" align="center"><b> Wirkung </b></td>
    <td width="10%" align="center"><b> Preis </b></td>
    <td width="10%" align="center"><b> Aktion </b></td>
  </tr>
  <?php
  $items = $itemManager->GetArenaitems();
  foreach ($items as $item) {

    if ($item->GetLevel() > $player->GetLevel() || !$item->IsVisible() && $player->GetArank() < 2)
    {
      continue;
    }
  ?>
    <tr>
        <td class="boxSchatten" align="center">
            <div class="tooltip" style="position: relative; z-index: 100;">
                <div style="width:50px; height:50px;">
                    <?php if ($item->HasOverlay())
                    {
                        ?>
                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png?002" style="width:50px;height:50px; position:relative; top:2px; z-index:1;">
                        <?php
                    }
                    ?>
                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png?002" style="width:50px;height:50px; position:relative; top:2px; z-index:0;">
                </div>
                <?php
                    if($item->GetHoverDescription() != '')
                    {
                        ?>
                        <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -75px; bottom: 40px;">
                                    <?php
                                        echo htmlspecialchars_decode($item->GetHoverDescription());
                                    ?>
                                </span>
                        <?php
                    }
                ?>
            </div>
          <input type="hidden" name="item" value="<?php echo $item->GetID(); ?>">
        </td>
        <td class="boxSchatten" align="center"> <b><?php echo $item->GetName(); ?></b> </td>
        <td class="boxSchatten" align="center">
          <?php
          echo $item->GetDescription();
          if ($item->GetLevel() != 0)
              echo 'Benötigt Level ' . number_format($item->GetLevel(), '0', '', '.');
          ?>
        </td>

        <td class="boxSchatten" align="center">
            <?php echo number_format($item->GetArenaPoints(), '0', '', '.'); ?> Punkte
        </td>
        <td class="boxSchatten" style="text-align: center;">
            <?php
                if($item->GetArenaPoints() <= $player->GetArenaPoints())
                {
                    ?>
                        <button onclick="OpenPopupPage('Item Kaufen','arena/buy.php', 'item=<?php echo $item->GetID();?>');">
                            Kaufen
                        </button>
                    <?php
                }
                ?>
        </td>
    </tr>
  <?php
  }
  ?>
</table>
<hr>
<br />
<i>Franky wechselt dir dein Geld!</i>
<details>
<summary><img src="img/marketing/onepieceopwxdeleads.png" /></summary>
<br />
<b>
<br />
Hier kannst du deine Punkte aus dem Kolosseum in Berry umwandeln!
<br />
<br />
Umrechnungskurs: 1 zu 25.</b>
<br />
<br />
<form method="POST" action="?p=arena&change=kolopunkte" >
<input name="money" type="text" value="<?= $player->GetArenaPoints(); ?>" />
<br />
<br />
<input type="submit" value="wechseln" />
</form>
</details>
