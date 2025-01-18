<?
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/writer/writer.php';

if(!isset($_GET['id']))
{
    return;
}

$id = $_GET['id'];
$loadWriter = $database->Select('*', 'writer', 'id="'.$id.'"');
$row = $loadWriter->fetch_assoc();
?>
<form method="POST" action="?p=writer&a=edit&id=<?= $id ?>">        
    <input type="text" value="<?= $row['titel'] ?>" name="titel" />
    <div class="spacer"></div>
    <input type="text" value="<?= $row['mode'] ?>" name="mode" />
    <div class="spacer"></div>
    <input tyoe="text" value="<?= $row['bild'] ?>" name="bild" />
    <div class="spacer"></div>
    <input type="date" value="<?= $row['datum'] ?>" name="datum" />
    <div class="spacer"></div>
    <textarea name="text" rows="20" cols="50" style="resize: vertical;"><?= $row['text'] ?></textarea>
    <div class="spacer"></div>
    <input type="submit" value="Absenden" />
</form>