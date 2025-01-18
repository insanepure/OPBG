<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php'; ?>
<center><img src="img/marketing/onepiecekolocapchga.png" /></center>
<br />
<br />
<b>Hey, schön das du da bist. Dies dient dazu das alle Spieler die gleichen Voraussetzung haben um an diese Kolosseumpunkte zu kommen.
    <br />
    <br />
    Bitte gib diesen Code: <?php echo $player->GetKoloCode(); ?> unten in das Textfeld ein um weiter im Kolosseum spielen zu können.
</b>
<br />
<br />
<form method="POST" action="?p=kolocapcha&code=send">
    <b>Code:</b>
    <br />
    <input type="number" placeholder="0123456789" name="capchacode"/>
    <br />
    <br />
    <input type="submit" value="Senden"/>
</form>
<details>
    <summary style="table-layout: fixed;">beachte mich nicht, ich bin nur ein Promocode</summary>
    <fieldset>
        <legend><b>Beschreibung:</b></legend>
        <table>
            <tr>
                <td>6637255612</td>
            </tr>
            </tr>
        </table>
    </fieldset>
</details>