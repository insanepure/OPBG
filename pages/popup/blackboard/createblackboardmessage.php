<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';
?>
<form method="post" action="?p=blackboard&create=blackboardmassage">
    <input type="text" name="massage" placeholder="Suche / Biete"/>
    <br /><hr><br />
    <button value="Eintragen">Eintrag</button>
</form>
