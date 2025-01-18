<img width="100%" height="300" src="img/marketing/onepiecepromobild.png" />
<form method="POST" action="?p=promocode&active=code">
    <input type="text" placeholder="Promocode" name="pcode" />
    <div class="spacer"></div>
    <input type="submit" value="Promocode Aktivieren" />
</form>
<br />
<br />
<b>
    Was genau ist ein Promocode? Ein Promocode wird vom Team verteilt, hierbei handelt es sich um eine Zahlenkombination welche erstellt wird und hier eingelöst werden kann.
    <br />
    <br />
    Aktiviert man einen Code so erhält man eine vordefinierte Anzahl an Belohnungen, jeden Code kann man nur 1x nutzen danach deaktiviert er sich für den Spieler der ihn aktiviert hat.
    <br />
    <br />
    Es gibt keine Voraussetzungen für das Team zum erstellen so eines Codes, diese Codes dienen dazu Marketing zu betreiben oder generell eine Belohnung für die Community zu erstellen.
    <br />
    <br />
</b>
<?php
if($player->GetArank() == 3)
{
    echo "<hr>";
    $procode = rand(1, 99999999999);
    $date = date("d.m.Y");
    ?>
    <h1>Promocode Erstellen</h1>
    <details>
        <summary style="table-layout: fixed;">Formular aufklappen</summary>
        <form method="POST" action="?p=promocode&create=code">
            <b>Promocoe:</b><br />
            <input name="aprocode" type="text" value="<?= $procode; ?>"/>
            <br />
            <br />
            <b>Datum</b><br />
            <input name="adate" type="text" value="<?= $date; ?>" />
            <br />
            <br />
            <b>Nutzer:
                <br />
                <select name="aownerid" style="height:30px"; class="select">
                    <option value="1">Alle</option>
                    <?php
                    $ownercheck = $database->Select('*', 'accounts', 'id >= 1');
                    if($ownercheck)
                    {
                        while($owner = $ownercheck->fetch_assoc())
                        {
                            echo "<option value='".$owner['id']."'>".$owner['name']."</option>";
                        }
                    }
                    ?>
                </select>
                <br />
                <br />
                <b>User Level:</b>
                <br />
                <input name="aownerlevel" type="text" value="0"/>
                <br />
                <br />
                <b>Soll es eine Zeitbegrenzung geben?</b>
                <br />
                <select name="danswer" style="height:30px"; class="select">
                    <option value="nein">Nein</option>
                    <option value="ja">Ja</option>
                </select>
                <br />
                <br />
                <b>Zeitbegrenzung (In Tage)</b>
                <br />
                <select name="dzbegrenzung" style="height:30px"; class="select">
                    <?php
                    for($i=0; $i <= 365; $i++)
                    {
                        echo "<option value='".$i."'>".$i."</option>";
                    }
                    ?>
                </select>
                <br />
                <br />
                <b>Item:</b>
                <br />
                <select name="aitem" style="height:30px"; class="select">
                    <option value="0">Kein Item (0)</option>
                    <?php
                    $itemcheck = $database->Select('*', 'items');
                    if($itemcheck)
                    {
                        while($item = $itemcheck->fetch_assoc())
                        {
                            echo "<option value=".$item['id'].">".$item['name']." (".$item['id'].")</option>";
                        }
                    }
                    ?>
                </select>
                <br />
                <br />
                <b>Menge:</b>
                <br />
                <select name="aamount" style="height:30px;" class="select">
                    <?php
                    for($i=0; $i <= 100; $i++)
                    {
                        echo "<option value=".$i.">".$i."</option>";
                    }
                    ?>
                </select>
                <br />
                <br />
                <b>Soll der Spieler Berry erhalten? Ja, wie viel?</b>
                <br />
                <input name="aberry" value="0" type="text"/>
                <br />
                <br />
                <b>Soll der Spieler Gold erhalten? Ja, wie viel?</b>
                <br />
                <input name="agold" value="0" type="text"/>
                <br />
                <br />
                Soll der Spieler einen Titel erhalten?
                <br />
                <select name="atitel" style="height:30px;" class="select">
                    <option value="0">Kein Titel (0)</option>
                    <?php
                    $titelcheck = $database->Select('*', 'titel');
                    if($titelcheck)
                    {
                        while($titel = $titelcheck->fetch_assoc())
                        {
                            echo "<option value='".$titel['id']."'>".$titel['name']." (".$titel['id'].")</option>";
                        }
                    }
                    ?>
                </select>
                <br />
                <br />
                <b>Soll der Spieler Stats erhalten? Wenn ja, wie viele:</b>
                <br />
                <input name="astats" type="text" value="0" />
                <br />
                <br />
                <input type="submit" value="Erstellen" />
                <br />
        </form>
    </details>
    <?php
}
?>
