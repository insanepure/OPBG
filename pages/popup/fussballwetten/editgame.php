<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once $_SERVER['DOCUMENT_ROOT'].'../../classes/fussballwetten/fussballwetten.php';
include_once '../../../classes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    exit();

$games = $database->Select('*', 'fussballwetten', 'id="'.$_GET['id'].'"');
$game = $games->fetch_assoc();
?>
    <form method="post" action="?p=fussballwetten&a=edit&id=<?= $game['id']; ?>">
        <table>
            <tr>
                <th><b>Mannschaft Zuhause</b></th>
                <th><input type="text" name="mannschaftz" value="<?= $game['mannschaftz']; ?>" /></th>
            </tr>
            <tr>
                <th><b>Mannschaft Zuhause Logo</b></th>
                <th><input type="text" name="logoz" value="<?= $game['flaggez']; ?>" /></th>
            </tr>
            <tr>
                <th><b>Mannschaft Gast</b></th>
                <th><input type="text" name="mannschaftg" value="<?= $game['mannschaftg']; ?>" /></th>
            </tr>
            <tr>
                <th><b>Mannschaft Gast Logo</b></th>
                <th><input type="text" name="logog" value="<?= $game['flaggeg']; ?>" /></th>
            </tr>
            <tr>
                <th><b>Start Uhrzeit ohne Punkt Beispiel 1831 = 18:31</b></th>
                <th><input type="text" name="startuhrzeit" value="<?= $game['start']; ?>"/></th>
            </tr>
            <tr>
                <th><b>Start Datum ohne Punkt Beispiel 25082023 = 25.08.2023</b></th>
                <th><input type="text" name="startdatumop" value="<?= $game['datum']; ?>" /></th>
            </tr>
            <tr>
                <th><b>Start Datum mit Punkt und Uhrzeit, Beispiel 25.08.2023 - 20:30</b></th>
                <th><input type="text" name="start" value="<?= $game['beginn']; ?>"/></th>
            </tr>
            <tr>
                <th><b>Trage hier das Ergebnis ein im 1:1 Format</b></th>
                <th><input type="text" name="ergebnis" value="<?= $game['ergebnis']; ?>"/></th>
            </tr>
            <tr>
                <th><b>Hier kannst du den Einsatz bearbeiten! NUR WENN ES NOTWENDIG IST </b></th>
                <th><input type="text" name="einsatz" value="<?= $game['einsatz']; ?>"/></th>
            </tr>
            <tr>
                <th></th>
                <th><button class="button">Speichern</button></th>
            </tr>
        </table>
    </form>
<?php
?>