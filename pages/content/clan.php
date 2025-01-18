<div class="spacer"></div>
<?php if ($displayClan->GetImage() != '')
{
    ?>
    <div class="catGradient borderT borderB" style="width:600px;">
        <h2>
            <?php echo $displayClan->GetName(); ?>
        </h2>
    </div>
    <img src="<?php echo $displayClan->GetImage(); ?>" style="width:600px; height:400px;">
    <?php
}
else
{
    ?>
    <div class="catGradient borderT borderB" style="width:600px;">
        <h2>
            <?php echo $displayClan->GetName(); ?>
        </h2>
    </div>
    <div style="min-width:100%; max-width:100%;">
        <img src="img/clannoimage.png" style="width:600px; height:400px;">
    </div>
    <?php
}
?>
<?php
$list = new Generallist($database, 'places', 'name, planet, sieger, time, gewinn', 'territorium="' . $displayClan->GetID() . '"', 'id', 999, 'ASC');
if($list->GetCount() > 0)
{
    ?>
    <div class="spacer"></div>
    <div class="catGradient borderT borderB" style="width:600px;">
        <h2>
            Territorien
        </h2>
    </div>
    <table width="90%" class="borderT borderR borderL borderB">
        <tr>
            <td>Ort:</td>
            <td>Meer:</td>
            <td>Sieger:</td>
            <td>Am:</td>
            <?php
            if($player->GetClan() == $displayClan->GetID() || $player->GetArank() >= 2)
            {
                ?>
                <td>Erhalten:</td>
                <?php
            }
            ?>
        </tr>
        <?php
        $id = 0;
        $entry = $list->GetEntry($id);
        while ($entry != null)
        {
            ?>
            <tr>
                <td><?php echo $entry['name']?></td>
                <?php
                    $displayedplanet = new Planet($database, $entry['planet']);
                ?>
                <td><?php echo $displayedplanet->GetName(); ?></td>
                <td>
                    <?php
                    $siegerListe = explode(';', $entry['sieger']);
                    for ($i = 0; $i < count($siegerListe); $i++)
                    {
                        $siegerPlayer = new Player($database, $siegerListe[$i]);
                        echo '<a href="?p=profil&id='.$siegerPlayer->GetID().'">'.$siegerPlayer->GetName().'</a>';
                        if($i != count($siegerListe) - 1)
                            echo ', ';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    $formatedDate = date('d.m.Y', strtotime($entry['time']));
                    echo $formatedDate;
                    ?>
                </td>
                <?php
                if($player->GetClan() == $displayClan->GetID() || $player->GetArank() >= 2)
                {
                    ?>
                    <td>
                        <?php echo number_format($entry['gewinn'], 0, '', '.'); ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>
                    </td>
                    <?php
                }
                ?>
            </tr>
            <?php
            $id++;
            $entry = $list->GetEntry($id);
        }
        ?>
    </table>
    <?php
}
?>
<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:600px;">
    <h2>
        Banden Text
    </h2>
</div>
<div class="profileBox " style="width:600px; min-height:200px; word-wrap: break-word; overflow:hidden;">
    <?php echo $bbcode->parse($displayClan->GetText()); ?>
</div>
<?php if ($displayClan->GetRules() != '')
{
    ?>
    <div class="spacer"></div>
    <div class="catGradient borderT borderB" style="width:600px;">
        <h2>
            Banden Regeln
        </h2>
    </div>
    <div class="profileBox" style="width:600px; min-height:200px; word-wrap: break-word; overflow:hidden;">
        <?php echo $bbcode->parse($displayClan->GetRules()); ?>
    </div>
    <?php
}
if ($displayClan->GetRequirements() != '')
{
    ?>
    <div class="spacer"></div>
    <div class="catGradient borderT borderB" style="width:600px;">
        <h2>
            Banden Aufnahmebedingungen
        </h2>
    </div>
    <div class="profileBox" style="width:600px; min-height:200px; word-wrap: break-word; overflow:hidden;">
        <?php echo $bbcode->parse($displayClan->GetRequirements()); ?>
    </div>
    <?php
}
?>
<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:600px;">
    <h2>
        Crew Mitglieder
    </h2>
</div>
<table width="90%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
    <tr>
        <td width="10%"><b>Level</b></td>
        <td width="40%"><b>User</b></td>
        <?php
            if($player->GetClan() == $displayClan->GetID())
            {
                ?>
                <td width="15%">Ozean</td>
                <td width="15%">Ort</td>
                <?php
            }
        ?>
        <td width="20%"><b>Rang</b></td>
        <td width="20%"><b>Fraktion</b></td>
    </tr>
    <?php
    if (!isset($titelManager)) $titelManager = new TitelManager($database);

    $id = 0;
    $list = new Generallist($database, 'accounts', 'id,planet,place,name,rank,race,arank,titel,level,clanrank', 'clan="' . $displayClan->GetID() . '"', 'rank', 999, 'ASC');
    $entry = $list->GetEntry($id);
    while ($entry != null)
    {
        $titel = $titelManager->GetTitel($entry['titel']);
        $titelText = '';
        if ($titel != null)
        {
            $titelText = $titel->GetName();
            if ($titel->GetColor() != '')
            {
                $titelText = '<span style="color: #' . $titel->GetColor() . '">' . $titelText . '</span>';
            }
        }
        ?>
        <tr>
            <td width="5%"><?php echo number_format($entry['level'], '0', '', '.'); ?></td>
            <td width="30%">
                <?php if ($displayClan->GetLeader() == $entry['id'])
                {
                    echo '<img src="../img/stern2.png" width="15px" height="15px">';
                }
                else if ($entry['clanrank'] == 1)
                {
                    echo '<img src="../img/stern.png" width="15px" height="15px">';
                } ?>
                <a href="?p=profil&id=<?php echo $entry['id']; ?>"><?php echo $titelText . ' ' . $entry['name']; ?></a>
            </td>
            <?php
                if($player->GetClan() == $displayClan->GetID())
                {
                    $displayedplanet = new Planet($database, $entry['planet']);
                    $displayedplace = new Place($database, $entry['place'], $actionManager);
                    ?>
                        <td width="15%"><?php echo $displayedplanet->GetName(); ?></td>
                        <td width="15%"><?php echo $displayedplace->GetName(); ?></td>
                    <?php
                }
            ?>
            <td width="10%"><?php echo number_format($entry['rank'], '0', '', '.'); ?></td>
            <td width="15%"><?php echo $entry['race']; ?></td>
        </tr>
        <?php
        $id++;
        $entry = $list->GetEntry($id);
    }
    ?>
</table>
<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:600px;">
    <h2>
        Bande
    </h2>
</div>
<table width="90%" class="borderT borderR borderL borderB">
    <tr>
        <td>
            <div class="tooltip" style="position: relative; top: 0; left: 0;">
                <img src="img/offtopic/lvlup.png?1" alt="Level" title="Level" style="width: 100px; height: 100px;" />
                <span class="tooltiptext" style="width:180px; top:0; left:-40px;">
                        Bandenlevel: <?php echo number_format($displayClan->GetLevel(), 0, '', '.'); ?>
                </span>
            </div>
        </td>
        <td>
            <div class="tooltip" style="position: relative; top: 0; left: 0;">
                <img src="img/offtopic/ANG.png?1" alt="Angriff" title="Angriff" style="width: 100px; height: 100px; <?php if($displayClan->GetAttack() == 0) { ?>
                        filter: gray; /* IE6-9 */
                        -webkit-filter: grayscale(1); /* Google Chrome, Safari 6+ & Opera 15+ */
                        filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */ <?php } ?>" />
                <span class="tooltiptext" style="width:180px; top:0; left:-40px;">
                        Angriff Level: <?php echo number_format($displayClan->GetAttack(), 0, '', '.'); ?>
                </span>
            </div>
        </td>
        <td>
            <div class="tooltip" style="position: relative; top: 0; left: 0;">
                <img src="img/offtopic/DEF.png?1" alt="Abwehr" title="Abwehr" style="width: 100px; height: 100px; <?php if($displayClan->GetDefense() == 0) { ?>
                        filter: gray; /* IE6-9 */
                        -webkit-filter: grayscale(1); /* Google Chrome, Safari 6+ & Opera 15+ */
                        filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */ <?php } ?>"/>
                <span class="tooltiptext" style="width:180px; top:0; left:-40px;">
                        Abwehr Level: <?php echo number_format($displayClan->GetDefense(), 0, '', '.'); ?>
                </span>
            </div>
        </td>
        <td>
            <div class="tooltip" style="position: relative; top: 0; left: 0;">
                <img src="img/offtopic/AD.png?1" alt="AD" title="AD" style="width: 100px; height: 100px; <?php if($displayClan->GetAD() == 0) { ?>
                        filter: gray; /* IE6-9 */
                        -webkit-filter: grayscale(1); /* Google Chrome, Safari 6+ & Opera 15+ */
                        filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */ <?php } ?>"/>
                <span class="tooltiptext" style="width:180px; top:0; left:-40px;">
                        AD Level: <?php echo number_format($displayClan->GetAD(), 0, '', '.'); ?>
                </span>
            </div>
        </td>
        <td>
            <div class="tooltip" style="position: relative; top: 0; left: 0;">
                <img src="img/offtopic/LP.png?1" alt="LP" title="LP" style="width: 100px; height: 100px; <?php if($displayClan->GetLP() == 0) { ?>
                        filter: gray; /* IE6-9 */
                        -webkit-filter: grayscale(1); /* Google Chrome, Safari 6+ & Opera 15+ */
                        filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */ <?php } ?>"/>
                <span class="tooltiptext" style="width:180px; top:0; left:-40px;">
                        LP Level: <?php echo number_format($displayClan->GetLP(), 0, '', '.'); ?>
                </span>
            </div>
        </td>
    </tr>
</table>
<div class="spacer"></div>
<?php
if($player->GetClan() != $displayClan->GetID() && $clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'management'))
{
    ?>
    <div class="catGradient borderT borderB" style="width:600px;">
        <h2>
            Allianz
        </h2>
    </div>
    <div class="profileBox" style="width:600px; min-height:70px; word-wrap: break-word; overflow:hidden;">
        Möchtest du der Bande <?= $displayClan->GetName() ?> eine Allianzanfrage schicken?<br/><br/>
        <button onclick="OpenPopupPage('Allianzanfrage', 'clan/allianceinvite.php?id=<?= $displayClan->GetID() ?>')">Allianzanfrage stellen</button>
    </div>
    <?php
}
$ranks = explode('@',$displayClan->GetRanks());
$rank = explode(';', $ranks[$player->GetClanRank()]);
$requirements = $rank;
if($clan->IsValid() && $clan->GetRankPermission($player->GetClanRank(), 'intern') && $displayClan->GetID() == $player->GetClan() || $player->GetArank() >= 2)
{
    ?>
    <div class="catGradient borderT borderB" style="width:600px;">
        <h2>
            Interner Bereich
        </h2>
    </div>
    <div class="profileBox " style="width:600px; min-height:200px; word-wrap: break-word; overflow:hidden;">
        <?php echo $bbcode->parse($displayClan->GetInternText()); ?>
    </div>
    <?php
    if($player->GetArank() >= 2)
    {
        ?>
        <div class="spacer"></div>
        <a href="?p=clanmanage&id=<?= $displayClan->GetID() ?>"><input type="submit" value="Interner Clanbereich" style="width:fit-content; min-width: 200px; "></a>
        <?php
    }
}
?>