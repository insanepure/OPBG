<?php
if($player->GetArank() < 2)
    return;

$zoan = array(0,0,0);
$paramecia = array(0,0,0);
$logia = array(0,0,0);
$schwerter = 0;
$schwarzfuss = 0;
$karate = 0;
$charas = array(0,0);
$douriki = 0;
$berry = array('', 0, 0);
$gold = array('', 0, 0);
$top30 = 0;

$pfade = $database->Select('*', 'accounts', 'arank=0 AND banned=0 AND deleted=0');
$players = new GeneralList($database, 'accounts', '*', 'arank=0 AND banned=0 AND deleted=0', 'story');
$playerEntry = $players->GetEntry(0);
$bandenBerrys = new GeneralList($database, 'clans', '*', '', 'zeni');
$bandenBerryEntry = $bandenBerrys->GetEntry(0);
$bandenGolds = new GeneralList($database, 'clans', '*', '', 'gold');
$bandenGoldEntry = $bandenGolds->GetEntry(0);
$dungeons = new GeneralList($database, 'events', '*', 'isdungeon=1 AND dailyreset=1');
$id=0;
$entry = $dungeons->GetEntry($id);
$dungeon = 0;
$dungeonkaempfe = 0;
$dungeonkampf = 0;
while($entry != null)
{
    $players = explode(';', $entry['finishedplayers']);
    foreach ($players as $finishedplayer) {
        $finishedplayer = explode('@', $finishedplayer);
        $dungeonkampf += (int)$finishedplayer[1];
    }
    if($dungeonkampf > $dungeonkaempfe)
    {
        $dungeonkaempfe = $dungeonkampf;
        $dungeon = $entry['id'];
    }
    $id++;
    $dungeonkampf = 0;
    $entry = $dungeons->GetEntry($id);
}

if($pfade && $pfade > 0)
{
    while($row = $pfade->fetch_assoc())
    {
        if($row['level'] >= 10) {
            $douriki += round((($row['mlp'] / 10) + ($row['mkp'] / 10) + ($row['attack'] / 2) + ($row['defense'])) / 4);
            $charas[1]++;
        }
        $charas[0]++;
        $attacks = explode(";", $row['attacks']);
        if($berry[1] < $row['zeni'])
        {
            $berry[0] = $row['name'];
            $berry[1] = $row['zeni'];
        }
        if($gold[1] < $row['gold'])
        {
            $gold[0] = $row['name'];
            $gold[1] = $row['gold'];
        }
        if($row['rank'] <= 30)
        {
            $berry[2] += $row['zeni'];
            $gold[2] += $row['gold'];
            $top30++;
        }
        // Zoan
        if(in_array(52, $attacks)) // Fisch-Frucht
            $zoan[0]++;
        if(in_array(762, $attacks)) // Vogel-Frucht
            $zoan[1]++;
        if(in_array(763, $attacks)) // Mensch-Mensch-Frucht
            $zoan[2]++;
        // ----

        // Paramecia
        if(in_array(50, $attacks)) // Operations-Frucht
            $paramecia[0]++;
        if(in_array(760, $attacks)) // Faden-Frucht
            $paramecia[1]++;
        if(in_array(761, $attacks)) // Mochi-Frucht
            $paramecia[2]++;
        // ---------

        // Logia
        if(in_array(51, $attacks)) // Donner-Frucht
            $logia[0]++;
        if(in_array(764, $attacks)) // Feuer-Frucht
            $logia[1]++;
        if(in_array(765, $attacks)) // Gefrier-Frucht
            $logia[2]++;
        // -----

        if(in_array(53, $attacks)) // Pfad Schwertkämpfer
            $schwerter++;
        if(in_array(54, $attacks)) // Pfad Schwarzfuß
            $schwarzfuss++;
        if(in_array(55, $attacks)) // Pfad Karatekämpfer
            $karate++;
    }
}

?>
<h2>Pfade</h2>
Anzahl der Zoan Nutzer: <?= $zoan[0] + $zoan[1] + $zoan[2] ?><br/>
Anzahl der Paramecia Nutzer: <?= $paramecia[0] + $paramecia[1] + $paramecia[2] ?><br/>
Anzahl der Logia Nutzer: <?= $logia[0] + $logia[1] + $logia[2] ?><br/>
Anzahl der Schwertkämpfer Nutzer: <?= $schwerter ?><br/>
Anzahl der Schwarzfuß Nutzer: <?= $schwarzfuss ?><br/>
Anzahl der Karatekämpfer Nutzer: <?= $karate ?><br/><br>

<h2>Unterpfade</h2>
<h3>Zoan</h3>
Anzahl der Fisch-Frucht Nutzer: <?= $zoan[0] ?><br/>
Anzahl der Vogel-Frucht Nutzer: <?= $zoan[1] ?><br/>
Anzahl der Mensch-Mensch-Frucht Nutzer: <?= $zoan[2] ?><br/><br>
<h3>Paramecia</h3>
Anzahl der Operations-Frucht Nutzer: <?= $paramecia[0] ?><br/>
Anzahl der Faden-Frucht Nutzer: <?= $paramecia[1] ?><br/>
Anzahl der Mochi-Frucht Nutzer: <?= $paramecia[2] ?><br/><br/>
<h3>Logia</h3>
Anzahl der Donner-Frucht Nutzer: <?= $logia[0] ?><br/>
Anzahl der Feuer-Frucht Nutzer: <?= $logia[1] ?><br/>
Anzahl der Gefrier-Frucht Nutzer: <?= $logia[2] ?><br/><br/>
<br/>
<br/>
<h2>Sonstiges Statistiken</h2>
Anzahl der registrierten Charaktere: <?= $charas[0] ?><br/>
Anzahl der registrierten Charaktere über Level 10: <?= $charas[1] ?><br/>
Durchschnittliche Douriki (Level 10+): <?= round($douriki / $charas[1]) ?><br/>
Reichster Spieler (Berry): <?= $berry[0] ?> (<?= number_format($berry[1], 0, '', '.') ?> Berry)<br/>
Reichster Spieler (Gold): <?= $gold[0] ?> (<?= number_format($gold[1], 0, '', '.') ?> Gold)<br/>
Durchschnittliche Berry (Top-30): <?= number_format(round($berry[2] / $top30), 0, '', '.') ?> Berry<br/>
Durchschnittliches Gold (Top-30): <?= number_format(round($gold[2] / $top30), 0, '', '.') ?> Gold<br/>

Weitester Spieler in der Story: <?= '<a href="?p=profil&id='.$playerEntry['id'].'">'.$playerEntry['name'] . '</a> bei: ' . $playerEntry['story'] ?><br/>
Reichste Bande: <?= '<a href="?p=clan&id='.$bandenBerryEntry['id'].'">'.$bandenBerryEntry['name'] . '</a> Berry: ' . number_format($bandenBerryEntry['zeni'], 0, '', '.') ?><br/>
Reichste Bande: <?= '<a href="?p=clan&id='.$bandenGoldEntry['id'].'">'.$bandenGoldEntry['name'] . '</a> Gold: ' . number_format($bandenGoldEntry['gold'], 0, '', '.') ?><br/>
Meistgemachter Dungeon am Tag: <?php $dungeon = new Event($database, $dungeon); echo $dungeon->GetName();?> mit <?php echo number_format($dungeonkaempfe, 0, '', '.'); ?> Kämpfen<br/>
<h2>Aktive Spender</h2>
<?php
$CheckSpender = $database->Select('id, name, titels, ismulti, arank, team, clan', 'accounts', '');
if($CheckSpender)
{
    while($spender = $CheckSpender->fetch_assoc())
    {
        $clan = new Clan($database, $spender['clan']);
        $stitel = 76;
        $istitel = explode(";", $spender['titels']);
        if(in_array($stitel, $istitel) && $spender['ismulti'] == 0 && $spender['arank'] == 0 && $spender['team'] == 0)
        {
            ?>
            <style>
                table {border-style: ridge;  border-width: 10px; border-color: #8ebf42;}
                th  {border:5px solid #095484;}
                td {border:10px groove #1c87c9;}
            </style>
                <table>
                    <tr>
                        <td style="width: 100px; border: 1px; border-color: white;"><b>Name</b></td>
                        <td style="width: 100px; border: 1px; border-color: white;"><b>Bande</b></td>
                    </tr>
                    <tr>
                        <td><a href="?p=profil&id=<?= $spender['id']; ?>"><?= $spender['name']; ?></a></td>
                        <td><a href="<?= $clan->GetID(); ?>"><?= $clan->GetName(); ?></a></td>
                    </tr>
                </table>
            <?php
        }
    }
}
?>
<hr>
