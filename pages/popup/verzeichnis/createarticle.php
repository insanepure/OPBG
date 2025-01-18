<style>
    #popup-content {
        width: 750px;
    }

    .popup-container {
        width: 800px;
        top: -25px;
    }
</style>
<fieldset>
    <legend><font color="red"><b>!!! Achtung !!!</b></font></legend>
    <li>Bei der Beschreibung bitte eigene Creationen speichern und kein Copy & Paste von anderen Seiten!</li>
    <li>Bei den Angaben der Debüs bitte auch das Kapitel mit eintragen.</li>
    <li>Sollte etwas unklar sein dann einfach "Unbekannt" hineinschreiben!</li>
    <li>Bei der Beschreibung können BBCodes verwendet werden!</li>
</fieldset>
<form method="post" action="?p=verzeichnis&createarticle=fromuser">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;">Name</td>
            <td style="width: 50%;"><input type="text" name="dname" /></td>
        </tr>
        <tr>
            <td>Name im Original Jap.</td>
            <td><input type="text" name="jname" /></td>
        </tr>
        <tr>
            <td>Rasse</td>
            <td><input type="text" name="race" /></td>
        </tr>
        <tr>
            <td>Geschlecht</td>
            <td><select class="select" name="geschlecht"><option value="Unbekannt">Unbekannt</option><option value="Männlich">Männlich</option><option value="Weiblich">Weiblich</option></select></td>
        </tr>
        <tr>
            <td>Alter</td>
            <td><input type="text" name="old" /></td>
        </tr>
        <tr>
            <td>Geburtstag</td>
            <td><input type="text" name="bday" /></td>
        </tr>
        <tr>
            <td>Größe in m</td>
            <td><input type="text" name="bigest" /></td>
        </tr>
        <tr>
            <td>Herkunft</td>
            <td><input type="text" name="from" /></td>
        </tr>
        <tr>
            <td>Familie</td>
            <td><input type="text" name="family" /></td>
        </tr>
        <tr>
            <td>Piratenbande</td>
            <td><input type="text" name="pbande" /></td>
        </tr>
        <tr>
            <td>Position</td>
            <td><input type="text" name="position" /></td>
        </tr>
        <tr>
            <td>Kopfgeld</td>
            <td><input type="text" name="kgeld" /></td>
        </tr>
        <tr>
            <td>Teufelsfrucht</td>
            <td><input type="text" name="tf" /></td>
        </tr>
        <tr>
            <td>Deutscher Synchronsprecher</td>
            <td><input type="text" name="dsynchro" /></td>
        </tr>
        <tr>
            <td>Jap. Synchronsprecher</td>
            <td><input type="text" name="jsynchro" /></td>
        </tr>
        <tr>
            <td>Debüt im Anime</td>
            <td><input type="text" name="danime" /></td>
        </tr>
        <tr>
            <td>Debüt im Manga</td>
            <td><input type="text" name="dmanga" /></td>
        </tr>
        <tr>
            <td>Beschreibung</td>
            <td><textarea name="beschreibung" style="height: 150px; width: 500px; resize: none;"></textarea></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <button value="Absenden">Absenden</button>
            </td>
        </tr>
    </table>
</form>