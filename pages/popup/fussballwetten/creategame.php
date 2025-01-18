<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';

if ($player->GetArank() < 2)
    exit();
?>
<form method="post" action="?p=fussballwetten&a=create">
<table>
    <tr>
       <th><b>Mannschaft Zuhause</b></th>
        <th><input type="text" name="mannschaftz" ></th>
    </tr>
    <tr>
        <th><b>Mannschaft Zuhause Logo</b></th>
        <th><input type="text" name="logoz" ></th>
    </tr>
    <tr>
        <th><b>Mannschaft Gast</b></th>
        <th><input type="text" name="mannschaftg" ></th>
    </tr>
    <tr>
        <th><b>Mannschaft Gast Logo</b></th>
        <th><input type="text" name="logog" ></th>
    </tr>
    <tr>
        <th><b>Start Uhrzeit ohne Punkt Beispiel -> 1831 = 18:31</b></th>
        <th><input type="text" name="startuhrzeit" ></th>
    </tr>
    <tr>
        <th><b>Start Datum ohne Punkt Beispiel -> 25082023 = 25.08.2023</b></th>
        <th><input type="text" name="startdatumop" ></th>
    </tr>
    <tr>
        <th><b>Start Datum mit Punkt und Uhrzeit, Beispiel 25.08.2023 - 20:30</b></th>
        <th><input type="text" name="start" ></th>
    </tr>
    <tr>
        <th></th>
        <th><button class="button">Speichern</button></th>
    </tr>
</table>
</form>
<?php
?>