<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';

?>

<form method="POST" action="?p=infight&fight=<?php echo $_GET['id']; ?>&a=meld">
    <label>
        <select name="option" class="select">
            <option value="Regelverstoß">Regelverstoß</option>
            <option value="Bug">Bug</option>
        </select>
    </label>
    <br />
    <br />
    <!--<textarea rows="20" cols="50" name="grund" placeholder="Bitte Erläutere uns kurz, weshalb du diesen Kampf melden möchtest"></textarea>-->
    <label>
        Bitte gib einen kurzen Grund für die Meldung an:<br/>
        <input type="text" name="reason" maxlength="32" placeholder="Grund der Meldung">
    </label>
    <br />
    <br />
    <button>Absenden</button>
</form>