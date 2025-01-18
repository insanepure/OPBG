<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
$heute = date("j.n.Y - H:i");
?>
<form method="POST" action="?p=writer&a=create">
    <input type="text" placeholder="Titel" name="titel" />
    <div class="spacer"></div>
    <input type="text" value="https://www.op-bg.de/index?p=verzeichnis" placeholder="Mode" name="mode" />
    <div class="spacer"></div>
    <input tyoe="text" placeholder="Bild-URL" name="bild" />
    <div class="spacer"></div>
    <input type="text" value="<?= $heute; ?>"  name="datum" />
    <div class="spacer"></div>
    <textarea name="text" rows="20" cols="50" placeholder="Text" style="resize: vertical;"></textarea>
    <div class="spacer"></div>
    <input type="submit" value="Absenden" />
</form>