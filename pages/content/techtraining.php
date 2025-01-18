<?php
    $techniken = false;
    if($place->GetLearnableAttacks() != '')
        $attacks = explode(';', $place->GetLearnableAttacks());

    $i = 0;
    while (isset($attacks[$i]))
    {
        $attack = $attackManager->GetAttack($attacks[$i]);
        $playerAttacks = explode(";", $player->GetAttacks());
        if (($player->GetRace() == $attack->GetRace() || $attack->GetRace() == '') && !in_array($attack->GetID(), $playerAttacks))
        {
            $techniken = true;
        }
        $i++;
    }
    if ($techniken)
    {
        ?>
        <table width="100%" cellspacing="0" class="boxSchatten">
            <tr>
                <td class="catGradient borderB borderT borderR borderL" colspan="9" style="text-align:center"><b>Ausbildung</b></td>
            </tr>
            <tr>
                <td width="20%" class="borderL" style="text-align:center"><b>Bild</b></td>
                <td width="20%" style="text-align:center"><b>Name</b></td>
                <td width="10%" style="text-align:center"><b>Typ</b></td>
                <td width="5%" style="text-align:center"><b>Stärke</b></td>
                <td width="5%" style="text-align:center"><b>Genauigkeit</b></td>
                <td width="10%" style="text-align:center"><b>Kosten</b></td>
                <td width="10%" style="text-align:center"><b>Dauer</b></td>
                <td width="20%" style="text-align:center"><b>Voraussetzung</b></td>
                <td width="10%" class="borderR" style="text-align:center"><b>Aktion</b></td>
            </tr>
            <?php
                $j = 0;
                while (isset($attacks[$j]))
                {
                     $attack = $attackManager->GetAttack($attacks[$j]);
                     if($attack->GetRace() == '' || $attack->GetRace() == $player->GetRace())
                     {
                         ?>
                         <form method="POST" action="?p=techtraining&a=train&id=<?php echo $attack->GetID(); ?>">
                             <tr>
                                 <td width="50px" class="borderL borderB" style="text-align:center">
                                     <img src="<?php echo $attack->GetImage(); ?>" width="50px" height="50px"/>
                                 </td>
                                 <td style="text-align:center" class="borderB">
                                     <?php echo $attack->GetName(); ?>
                                 </td>
                                 <td style="text-align:center" class="borderB">
                                     <?php echo Attack::GetTypeName($attack->GetType()); ?>
                                 </td>
                                 <td style="text-align:center" class="borderB">
                                     <?php echo $attack->GetValue();
                                         if ($attack->IsProcentual()) echo '%'; ?>
                                 </td>
                                 <td style="text-align:center" class="borderB">
                                     <?php echo $attack->GetAccuracy(); ?>%
                                 </td>
                                 <td style="text-align:center" class="borderB">
                                     <?php
                                         if ($attack->GetLP() != 0)
                                         {
                                             echo number_format($attack->GetLP(),'0', '', '.');
                                             if ($attack->IsCostProcentual()) echo '%';
                                             echo ' LP';
                                         }
                                         if ($attack->GetKP() != 0)
                                         {
                                             echo number_format($attack->GetKP(),'0', '', '.');
                                             if ($attack->IsKPProcentual()) echo '%';
                                             echo ' AD';
                                         }
                                     ?>
                                 </td>
                                 <td class="borderB" style="text-align:center">
                                     <?php echo $attack->GetLearnTime(); ?> Stunden
                                 </td>
                                 <td class="borderB" style="text-align:center">
                                     <?php
                                         if ($attack->GetLevel() != 0) echo 'Level: ' . number_format($attack->GetLevel(),'0', '', '.') . '</br>';
                                         if ($attack->GetLearnKI() != 0) echo 'Douriki: ' . number_format($attack->GetLearnKI(),'0', '', '.') . '<br/>';
                                         if ($attack->GetLearnLP() != 0) echo 'LP: ' . number_format($attack->GetLearnLP(),'0', '', '.') . '<br/>';
                                         if ($attack->GetLearnKP() != 0) echo 'EP: ' . number_format($attack->GetLearnKP(),'0', '', '.') . '<br/>';
                                         if ($attack->GetLearnAttack() != 0) echo 'Attack: ' . number_format($attack->GetLearnAttack(),'0', '', '.') . '<br/>';
                                         if ($attack->GetLearnDefense() != 0) echo 'Defense: ' . number_format($attack->GetLearnDefense(),'0', '', '.') . '<br/>';
                                     ?>
                                 </td>
                                 <td class="borderR borderB" style="text-align:center"><input type="submit" value="Start" /></td>
                             </tr>
                         </form>
                         <?php
                     }
                    $j++;
                }
            ?>
        </table>
        <?php
    }
    else if (!$techniken)
    {
        ?>
        <b>An diesem Ort kann ich dir keine neue Technik beibringen.</b>
        <div class="spacer"></div>
        <img src="img/marketing/onepiecekeinetechnik.png" />
        <?php
    }
?>