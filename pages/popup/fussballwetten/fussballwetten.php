<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    exit();
?>
<b>Bitte nehmen sie im 1:1 Format teil, alles andere kann nicht gewertet werden! <br /> Die Teilnahme kostet 10.000 Berry!</b>
<form method="post" action="?p=fussballwetten&id=<?= $_GET['id']; ?>&teilnahme=true">
    <input type="text" name="tipp" value="Tipp"> <button class="button">Teilnehmen</button>
</form>
<?php
?>