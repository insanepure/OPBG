<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    exit();

$EditArtikelCheck = $database->Select('*', 'verzeichnis', 'id="'.$_GET['id'].'"');
$article = $EditArtikelCheck->fetch_assoc();
?>
<style>
    #popup-content {
        width: 750px;
    }

    .popup-container {
        width: 800px;
        top: -25px;
    }
</style>
<form method="post" action="?p=verzeichnis&a=edit&aid=<?= $article['id']; ?>">
    <input type="hidden" name="uid" value="<?= $article['uid'] ?>">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;">Bild</td>
            <td style="width: 50%;"><input type="text" name="picture" value="<?= $article['image']; ?>"/></td>
        </tr>
        <tr>
            <td>Name</td>
            <td><input type="text" name="dname" value="<?= $article['name']; ?>"/></td>
        </tr>
        <tr>
            <td>Name im Original Jap.</td>
            <td><input type="text" name="jname" value="<?= $article['romaji']; ?>" /></td>
        </tr>
        <tr>
            <td>Rasse</td>
            <td><input type="text" name="race" value="<?= $article['rasse']; ?>" /></td>
        </tr>
        <tr>
            <td>Geschlecht</td>
            <td><select class="select" name="geschlecht"><option value="Unbekannt" <?php if($article['geschlecht'] == 'Unbekannt') echo 'selected'; ?>>Unbekannt</option><option value="Männlich" <?php if($article['geschlecht'] == 'Männlich') echo 'selected'; ?>>Männlich</option><option value="Weiblich" <?php if($article['geschlecht'] == 'Weiblich') echo 'selected'; ?>>Weiblich</option></select></td>
        </tr>
        <tr>
            <td>Alter</td>
            <td><input type="text" name="old" value="<?= $article['alter']; ?>" /></td>
        </tr>
        <tr>
            <td>Geburtstag</td>
            <td><input type="text" name="bday" value="<?= $article['birthday']; ?>" /></td>
        </tr>
        <tr>
            <td>Größe in m</td>
            <td><input type="text" name="bigest" value="<?= $article['groesse']; ?>" /></td>
        </tr>
        <tr>
            <td>Herkunft</td>
            <td><input type="text" name="from" value="<?= $article['herkunft']; ?>" /></td>
        </tr>
        <tr>
            <td>Familie</td>
            <td><input type="text" name="family" value="<?= $article['family']; ?>" /></td>
        </tr>
        <tr>
            <td>Piratenbande</td>
            <td><input type="text" name="pbande" value="<?= $article['piratenbande']; ?>" /></td>
        </tr>
        <tr>
            <td>Position</td>
            <td><input type="text" name="position" value="<?= $article['position']; ?>" /></td>
        </tr>
        <tr>
            <td>Kopfgeld</td>
            <td><input type="text" name="kgeld" value="<?= $article['kopfgeld']; ?>" /></td>
        </tr>
        <tr>
            <td>Teufelsfrucht</td>
            <td><input type="text" name="tf" value="<?= $article['teufelsfrucht']; ?>" /></td>
        </tr>
        <tr>
            <td>Deutscher Synchronsprecher</td>
            <td><input type="text" name="dsynchro" value="<?= $article['voiceactorger']; ?>" /></td>
        </tr>
        <tr>
            <td>Jap. Synchronsprecher</td>
            <td><input type="text" name="jsynchro" value="<?= $article['voiceactorjap']; ?>" /></td>
        </tr>
        <tr>
            <td>Debüt im Anime</td>
            <td><input type="text" name="danime" value="<?= $article['anime']; ?>" /></td>
        </tr>
        <tr>
            <td>Debüt im Manga</td>
            <td><input type="text" name="dmanga" value="<?= $article['manga']; ?>" /></td>
        </tr>
        <tr>
            <td>Beschreibung</td>
            <td><textarea name="beschreibung" style="height: 150px; width: 500px; resize: none;"><?= $article['description']; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <button value="Absenden">Absenden</button>
            </td>
        </tr>
    </table>
</form>