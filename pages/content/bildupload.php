<?php
if($player->IsLogged())
{
?>
<br />
<br />
<form action="?p=bildupload&upload=picture" method="post" multipart="" enctype="multipart/form-data">
  <input type="file" name="files">
  <input type="submit">
</form>
<?php
}
?>

