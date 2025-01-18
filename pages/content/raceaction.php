
<table width="100%" cellspacing="0" style="margin-top: -5px;">
  <tr>
    <td class="catGradient borderB borderT" colspan="6" align="center"><b>Aktionen</b></td>
  </tr>
  <tr>
    <td width="15%" align="center"><b>Bild</b></td>
    <td width="20%" align="center"><b>Name</b></td>
    <td width="30%" align="center"><b>Wirkung</b></td>
    <td width="15%" align="center"><b>Kosten</b></td>
    <td width="10%" align="center"><b>Aktion</b></td>
  </tr>
  <?php
  $id = 0;
  $list = new Generallist($database, 'actions', 'id, race', 'special=1 AND level <= ' . $player->GetLevel() . '', '', 9999, 'ASC');
  $entry = $list->GetEntry($id);
  while ($entry != null)
  {
    if ($entry['race'] === "" || $entry['race'] == $player->GetRace())
    {
      $action = $actionManager->GetAction($entry['id']);
      if (
        $action->GetID() == 4 || // Rüstungshaki
        $action->GetID() == 44 && $player->GetPfad(1) == "Zoan" && $player->HasAttack(52) ||
        $action->GetID() == 18 && $player->GetPfad(1) == "Zoan" && $player->HasAttack(762) ||
        $action->GetID() == 19 && $player->GetPfad(1) == "Zoan" && $player->HasAttack(763) ||
        $action->GetID() == 41 && $player->GetPfad(1) == "Logia" && $player->HasAttack(764) ||
        $action->GetID() == 42 && $player->GetPfad(1) == "Logia" && $player->HasAttack(765) ||
        $action->GetID() == 47 && $player->GetPfad(1) == "Logia" && $player->HasAttack(51) ||
        $action->GetID() == 46 && $player->GetPfad(1) == "Paramecia" && $player->HasAttack(50) ||
        $action->GetID() == 43 && $player->GetPfad(1) == "Paramecia" && $player->HasAttack(760) ||
        $action->GetID() == 45 && $player->GetPfad(1) == "Paramecia" && $player->HasAttack(761) ||
        $action->GetID() == 21 && $player->GetPfad(2) == "Schwertkaempfer" && $player->HasAttack(53) ||
        $action->GetID() == 22 && $player->GetPfad(2) == "Schwarzfuss" && $player->HasAttack(54) ||
        $action->GetID() == 23 && $player->GetPfad(2) == "Karatekämpfer" && $player->HasAttack(55)
      )
      {
  ?>
        <tr>
          <form method="POST" action="?p=raceaction&a=train&id=<?php echo $action->GetID(); ?>">
            <td><img class="boxSchatten borderT borderR borderL borderB" src="img/actions/<?php echo $action->GetImage(); ?>.png" width="75px" height="75px" style="margin-left:5px; margin-top:5px;"></td>
            <td><?php echo $action->GetName(); ?></td>
            <td>
              <?php
              echo $bbcode->parse($action->GetDescription());
              $rhaki = $player->GetItemByIDOnly(11, 11);
              if ($action->GetID() == 4)
              {
                if (is_null($rhaki))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($rhaki->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(125); // Aura des Teufels
              if ($action->GetID() == 22)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(127); // Aura des Dämonengottes
              if ($action->GetID() == 21)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(128); // Aura der Phönixflammen
              if ($action->GetID() == 18)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(129); // Aura vom Gear
              if ($action->GetID() == 19)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(130); // Aura des Wassers
              if ($action->GetID() == 23)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(294); // Aura des Feuers
              if ($action->GetID() == 41)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(295); // Gefrierende Aura
              if ($action->GetID() == 42)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(296); // Aura vom Fäden
              if ($action->GetID() == 43)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(297); // Aura von Kaido
              if ($action->GetID() == 44)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(298); // Aura des Zuckers
              if ($action->GetID() == 45)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(299); // Aura des Raums
              if ($action->GetID() == 46)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }

              $aura = $player->GetItemByStatsIDOnly(300); // Aura des Donners
              if ($action->GetID() == 47)
              {
                if (is_null($aura))
                  echo "Upgradestand: 0 / " . number_format($player->GetLevel(),'0', '', '.');
                else
                  echo "Upgradestand: " . number_format($aura->GetCalculateUpgrade(),'0', '', '.') . " / " . number_format($player->GetLevel(),'0', '', '.');
              }
              ?>
            </td>
            <td>
              <?php
              if ($action->GetPrice() != 0)
              {
                echo number_format($action->GetPrice(),'0', '', '.') . ' <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
              }
              echo '<br/>';
              if ($action->GetItem() != 0)
              {
                $itemData = $itemManager->GetItem($action->GetItem());
                echo $itemData->GetName();
              }
              ?>
            </td>
            <input type="hidden" value="0" name="hours" />
            <td align="center">
              <input type="submit" value="Start" />
            </td>
          </form>
        </tr>
  <?php

      }
    }
    ++$id;
    $entry = $list->GetEntry($id);
  }
  ?>
</table>
<?php
if($player->GetArank() >= 0)
{
    $attackManager = new AttackManager($database);
?>
        <div class="spacer"></div>
<table width="100%" cellspacing="0" border="0">
    <tr>
        <td class="catGradient borderB borderT" colspan="6" align="center"><b>Attacken</b></td>
    </tr>
    <tr class="boxSchatten">
        <td>Bild</td>
        <td>Name</td>
        <td>Level</td>
        <td>Kosten</td>
        <td>Aktion</td>
    </tr>
    <?php
    $pattacks = explode(';', $player->GetAttacks());
    $nonvalidtypes = array(25, 21, 16, 2, 18, 19, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
    $nonvalidids = array(50,51,52,53,54,55);
    $i = 0;
    while($pattacks[$i])
    {
        $attack = $attackManager->GetAttack($pattacks[$i]);
        if(!in_array($attack->GetType(), $nonvalidtypes) && !in_array($attack->GetID(), $nonvalidids))
        {
        ?>
    <form method="post" action="?p=raceaction&attack=levelup">
        <input type="hidden" name="id" value="<?= $attack->GetID(); ?>"/>
        <tr class="boxSchatten">

            <td><img width="50" height="50" src="/<?= $attack->GetImage(); ?>" /></td>
            <td><?= $attack->GetName(); ?></td>
            <td><?= $player->GetAttackLevel($attack->GetID()); ?></td>
            <td>50.000 <img src="img/offtopic/BerrySymbol.png" /></td>
            <td><button class="button">Aufleveln</button></td>
        </tr>
    </form>
        <?php
        }
        ++$i;
    }

    ?>
</table>
<?php
}
    ?>