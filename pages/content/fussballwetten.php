<?php
$datum = date('jmY');
include_once $_SERVER['DOCUMENT_ROOT'].'classes/player/player.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/fussballwetten/fussballwetten.php';

?>
<h1>Alle aktuellen Spiele!</h1>
<?php
$AllGamesSearch = $database->Select('*', 'fussballwetten', 'ausgezahlt=0', 99999);
if($AllGamesSearch)
{
    while($AllGames = $AllGamesSearch->fetch_assoc())
    {
        echo '<a href="?p=fussballwetten&id='.$AllGames['id'].'">'.$AllGames['mannschaftz'].' Vs '.$AllGames['mannschaftg'].'<br/> Start ist am '.$AllGames['beginn'].'</a><br /><br />';
    }
}

if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'])
{
    $spiel = new fussballwetten($database, $_GET['id']);
    $teilnehmer = explode(';', $spiel->GetTeilnehmer());
    ?>
    <table>
        <tr>
            <th><h1><?= $spiel->GetMannschaftZ(); ?></h1><br /><img height="150" width="150" src="<?= $spiel->GetFlaggeZ(); ?>" /></th>
            <th>VS</th>
            <th><h1><?= $spiel->GetMannschaftG(); ?></h1><br /><img height="150" width="150" src="<?= $spiel->GetFlaggeG(); ?>" /></th>
        </tr>
        <tr>
            <th>
               <b>Teilnehmer</b><br /> <?= count($teilnehmer) - 1; ?> <br /><?php if($spiel->GetDatum() >= $datum) { ?> <Button type="button" onclick="OpenPopupPage('Teilnehmen','fussballwetten/fussballwetten.php?id=<?= $spiel->GetID(); ?>')">Teilnehmen</Button> <?php } ?>
            </th>
            <th>
            <b>Start</b> <br/> <?= $spiel->GetBeginn(); ?><br /> <?= $spiel->GetErgebnis(); ?>
            </th>
            <th>
                <b>Pott</b><br /><?= $spiel->GetEinsatz(); ?><img src="img/offtopic/BerrySymbol.png" />
            </th>
        </tr>
    </table>
    <hr>
<?php
    if($player->GetArank() >= 2)
    {
        ?>
        <table>
            <tr>
                <th><Button type="button" onclick="OpenPopupPage('Bearbeiten','fussballwetten/editgame.php?id=<?= $spiel->GetID(); ?>')">Bearbeiten</Button></th>
                <th><form method="post" action="?p=fussballwetten&a=delete&id=<?= $spiel->GetID(); ?>"><button class="button">Loschen</button></form></th>
                <th><Button type="button" onclick="OpenPopupPage('Erstellen','fussballwetten/creategame.php')">Erstellen</Button></th>
            </tr>
        </table>
        <?php
    }
}
    ?>