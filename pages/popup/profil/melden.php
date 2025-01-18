<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';

?>

<form method="POST" action="?p=profil&id=<?php echo $_GET['id']; ?>&a=meld">
    <label>
        <select name="option" class="select">
            <option value="Regelverstoß">Regelverstoß</option>
            <option value="Bug">Bug</option>
        </select>
    </label>
    <br />
    <br />
    <label>
        <input type="text" name="reason" maxlength="32" placeholder="Grund der Meldung">
    </label>
    <br />
    <br />
    <button>Absenden</button>
</form>