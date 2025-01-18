<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';
?>
<form action="?p=fight&a=start" method="post">
    <table width="100%" cellspacing="0" border="0">
        <tr>
            <td colspan="3" width="100%">
                <center>
                    Du kannst einen Kampf mit unendlich vielen Teams und unendlich vielen Spielern erstellen.
                    Dazu musst du nur die Anzahl pro Team mit vs trennen.
                    Beispiel: 1vs1, 5vs2vs1, 2vs2vs2vs2vs2vs2 <br />
                    <b><u>Achtung: PvP-Kämpfe und Elo-Kämpfe können nur in einem 1vs1 Kampf ausgetragen werden!</u></b>
                </center>
            </td>
        </tr>
        <tr>
            <td colspan="3" height="15px"></td>
        </tr>
        <tr>
            <td width="30%">
                <center>
                    <select style="width:90%;" class="select" name="type">
                        <option value="1" selected>PvP-Kampf</option>
                        <option value="13">Elokampf</option>
                        <option value="0">Spaßkampf</option>
                    </select>
                </center>
            </td>
            <td width="30%">
                <center><input style="width:90%;" type="text" name="mode" value="1vs1" maxlength="30"></center>
            </td>
            <td width="30%">
                <center><input style="width:90%;" type="text" name="name" placeholder="Name" maxlength="30"></center>
            </td>
        </tr>
        <tr>
            <td colspan="3" height="12px"></td>
        </tr>
        <tr>
            <td colspan=3 width="30%" height="30px">
                <center><input type="submit" value="Starten"></center>
            </td>
        </tr>
    </table>
</form>
<fieldset>
    <legend><b>Fakefightverbot:</b></legend>
    <table>
        <tr>
            <td>
                <b>
                    <span style="color: #0066FF;">§1:</span>
                </b>
            </td>
            <td> Es gilt als Verstoß, einen Kampf absichtlich zu verlieren, mit der Absicht, einen Vorteil für den Gewinner zu erzielen. Kämpfe sollten immer ernst genommen werden. <br>
                <span style="text-align: center;">Mehr Infos in den <a href="?p=regeln"><b><u></u>Regeln</u></b></a></span>
            </td>
        </tr>
        <tr>
            <td>
            </td>
        </tr>
    </table>
</fieldset>
<br />