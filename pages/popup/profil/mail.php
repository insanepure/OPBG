<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id']))
{
    exit;
}
?>
<form method="POST" action="?p=charalogin&a=updatemail">
    <div class="spacer"></div>
    Derzeit ist keine gültige Email Adresse in dein Account hinterlegt,<br/>
    wenn du das Spiel weiterhin spielen möchtest, benötigst du eine gültige Email-Adresse.<br/>
    bitte trage eine gültige Email-Adresse ein, Wegwerf-Emails sind nicht gestattet:<br /><br/>
    <input type="email" name="email">
    <div class="spacer"></div>
    <hr>
    <div class="spacer"></div>
    <input style="width:125px" type="submit" class="ja" value="Senden">
</form>
<div class="spacer"></div>