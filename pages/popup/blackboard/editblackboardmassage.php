<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/writer/writer.php';

if(!isset($_GET['id']))
{
    return;
}

$id = $_GET['id'];
$Postechk = $database->Select('blackboard', 'accounts', 'id="'.$id.'"');
$post = $Postechk->fetch_assoc();
?>
<form method="post" action="?p=blackboard&edit=blackboardmassage&id=<?= $id; ?>">
    <input type="text" name="editmassage" value="<?= $post['blackboard']; ?>"/>
    <br /><hr><br />
    <button value="Ändern">Ändern</button>
</form>
